<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyChargeHistoryModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\PullOperations;
use App\Models\TGameConfigModel;
use App\Util\VirtualAccount;
use App\Models\AccountModel;
use App\Models\TotalMemberCashModel;
use App\Util\Payment;
use App\Util\Paykiwoom;
use App\GamblePatch\BaseGmPt;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;
use App\GamblePatch\NobleGmPt;
use App\GamblePatch\BullsGmPt;
use App\Util\UserPayBack;
use App\Execute\DayChargeEvent;


class MemberMoneyChargeHistoryController extends BaseController {

    use ResponseTrait;

    protected $gmPt; // 겜블패치 

    public function __construct() {
        if ('K-Win' == config(App::class)->ServerName) {
            $this->gmPt = new KwinGmPt();
        } else if ('Gamble' == config(App::class)->ServerName) {
            $this->gmPt = new GambelGmPt();
        } else if ('BetGo' == config(App::class)->ServerName) {
            $this->gmPt = new BetGoGmPt();
        } else if ('CHOSUN' == config(App::class)->ServerName) {
            $this->gmPt = new ChoSunGmPt();
        } else if ('BETS' == config(App::class)->ServerName) {
            $this->gmPt = new BetsGmPt();
        } else if ('NOBLE' == config(App::class)->ServerName) {
            $this->gmPt = new NobleGmPt();
        } else if ('BULLS' == config(App::class)->ServerName) {
            $this->gmPt = new BullsGmPt();
        }
    }

    public function chargeRequest() {
        $memberIdx = session()->get('member_idx');

        $chargeMoney = isset($_POST['money']) ? $_POST['money'] : NULL;
        //$depositName = isset($_POST['name']) ? $_POST['name'] : NULL;
        $uKey = isset($_POST['u_key']) ? $_POST['u_key'] : NULL;
        $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : 0;
        $charge_point_yn = isset($_POST['charge_point_yn']) ? $_POST['charge_point_yn'] : 0;

        $bonus_idx = isset($_POST['bonus_idx']) ? $_POST['bonus_idx'] : 0;
        $level = isset($_POST['level']) ? $_POST['level'] : 0;

        $memberModel = new MemberModel();

        $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type in ('service_charge','event_charge_status','event_charge_start','event_charge_end') ";
        $config = $memberModel->db->query($sql)->getResultArray();
        foreach ($config as $key => $item) {
            if ($item['set_type'] == 'service_charge') {
                $service_charge = $item['set_type_val'];
            }
            if ($item['set_type'] == 'event_charge_status') {
                $event_charge_status = $item['set_type_val'];
            }
            if ($item['set_type'] == 'event_charge_start') {
                $event_charge_start = $item['set_type_val'];
            }
            if ($item['set_type'] == 'event_charge_end') {
                $event_charge_end = $item['set_type_val'];
            }
        }
        if ('Y' == $service_charge) {
            return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
        }

        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null || $uKey == null) {

            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
            //$response['messages'] = '사용이 불가능 한 계정으로 관리자에게 문의바랍니다.';
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($findMember->getStatus() == 11) {
            //$response['messages'] = '관리자 승인이 필요합니다.';
            return $this->fail('관리자 승인이 필요합니다.');
        }

        if ($findMember->getUBusiness() != 1) {
            $response['messages'] = '총판은 충전신청을 할 수 없습니다.';
            return $this->fail($response);
        }
        
        
        // 가입첫충, 돌발시 신청후 보너스 선택이 갱신 안될 경우 보너스 선택값이 잘못온다.
        $currentDate = date("Y-m-d H:i:s");
        $unexpectedEvent = 0;
        if ($event_charge_status == 'ON' && date("Y-m-d " . $event_charge_start) <= $currentDate && date("Y-m-d " . $event_charge_end) >= $currentDate) { // 돌발첫충
            $unexpectedEvent = 1;
        }
        
        if(1 == $findMember->getRegFirstCharge() && 0 == $unexpectedEvent){
            if(0 > $bonus_idx){
                return $this->fail('보너스 타입 오류입니다. 잠시후 다시 시도해주세요.',400,1001);
            }
        }

        $sql_bonus = "select * from tb_static_bonus where idx = ? ";
        $bonus_infos = $memberModel->db->query(
                        $sql_bonus, [1]
                )->getResultArray();

        //if (!isset($bonus_infos) || 0 == count($bonus_infos)) {
        if (-1 >$bonus_idx || 5 < $bonus_idx) {
            return $this->fail('보너스 타입을 선택하세요.');
        }

        if (0 != $level && $level != $findMember->getLevel()) {
            return $this->fail('보너스 타입 값 오류입니다.');
        }

        $this->initMemberData(session(), session()->get('member_idx'));
        $member_level = $findMember->getLevel();

        $arr_account_number = explode('(', $account_number);
        $str_account_number = $arr_account_number[0];



        $accountModel = new AccountModel();

        $sql = "SELECT charge_type FROM charge_type WHERE level = ? ";
        $result_charge_type = $accountModel->db->query($sql, [$member_level])->getResult();

        $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** MemberMoneyChargeHistoryController::chargeRequest::result_charge_type ==> " . json_encode($result_charge_type));

        if (4 == $result_charge_type[0]->charge_type) {
            $sql = "SELECT account.bank_id ,account_number_1 as account_number ,account_name_1 as account_name, account_bank_1 as display_account_bank 
                    FROM personal_account_number 
                    LEFT JOIN account ON account.account_name = personal_account_number.account_bank_1
                    LEFT JOIN member ON member.idx = personal_account_number.member_idx
                    WHERE member_idx = ?  ";
            $result_bank_ids = $accountModel->db->query($sql, [$memberIdx])->getResultArray();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** MemberMoneyChargeHistoryController::chargeRequest::accountByLevel ==> " . json_encode($result_bank_ids));
        } else if (5 == $result_charge_type[0]->charge_type) {
            $sql = "SELECT account.bank_id , account_number_2 as account_number ,account_name_2 as account_name, account_bank_2 as display_account_bank 
                    FROM personal_account_number 
                    LEFT JOIN account ON account.account_name = personal_account_number.account_bank_2
                    LEFT JOIN member ON member.idx = personal_account_number.member_idx
                    WHERE member_idx = ?  ";
            $result_bank_ids = $accountModel->db->query($sql, [$memberIdx])->getResultArray();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** MemberMoneyChargeHistoryController::chargeRequest::accountByLevel ==> " . json_encode($result_bank_ids));
        } else {
            $result_bank_ids = $accountModel->where('level', $member_level)->find();
        }

        //$this->logger->error('MemberMoneyChargeHistoryController chargeRequest depositName : ' . session()->get('account_name'));
        //$this->logger->error($result_bank_ids);
        if (0 == count($result_bank_ids) || 0 == $result_bank_ids[0]['bank_id'] || false === isset($result_bank_ids[0]['account_number']) || false === isset($result_bank_ids[0]['account_name']) || false === isset($result_bank_ids[0]['display_account_bank'])) {
            return $this->fail('충전 가능한 계좌가 아닙니다.');
        }

        $bonus_point = 0;

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "SELECT idx FROM member_money_charge_history WHERE member_idx = ? AND status in (1,2)";

            $resultList = $memberMCHModel->db->query($sql, [$memberIdx])->getResult();
            if (0 < count($resultList)) {
                return $this->fail('이전 요청 내역이 처리되지 않았으며,관리자에게 문의바랍니다.');
            }

            $deposit_name = $findMember->getAccountName();//session()->get('account_name');

            $bank_id = $result_bank_ids[0]['bank_id'];
            $display_account_bank = $result_bank_ids[0]['display_account_bank'];
            $account_name = $result_bank_ids[0]['account_name'];


            $sql = "SELECT idx,referenceId FROM member_money_charge_history WHERE member_idx = ? AND deposit_name = ?"
                    . "AND money = ? AND bank_id = ? AND account_number =  ? AND status = 11 AND referenceId <> '' AND NOW() < DATE_ADD(create_dt, INTERVAL 1 HOUR) ORDER BY idx ASC limit 1";

            //$this->logger->error('MemberMoneyChargeHistoryController chargeRequest sql : ' . $sql);
            //$this->logger->error('MemberMoneyChargeHistoryController chargeRequest array : ' . json_encode([$memberIdx, $deposit_name, $chargeMoney, $bank_id, $str_account_number]));

            $resultList = $memberMCHModel->db->query($sql, [$memberIdx, $deposit_name, $chargeMoney, $bank_id, $str_account_number])->getResult();
            $referenceId = '';
            if (0 < count($resultList)) {
                $referenceId = $resultList[0]->referenceId;
            }

            //$this->logger->debug('MemberMoneyChargeHistoryController chargeRequest referenceId : ' . $referenceId);

            $memberMCHModel->chargeRequest(session()->get('account_name'), $chargeMoney, $findMember->getMoney(), $uKey,
                    $result_bank_ids[0]['bank_id'], $str_account_number, $referenceId,
                    $bonus_point, '', $display_account_bank, $account_name, $charge_point_yn, $bonus_idx, $level);
            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $response = [
                'result_code' => 400,
                'messages' => '충전 신청 실패',
                'messages_detail' => $e,
                'data' => []
            ];
            return $this->respond($response, 400);
        }

        $response = [
            'result_code' => 200,
            'messages' => '충전 신청 성공',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function chargeCoinRequest() {

        if (false == session()->has('member_idx')) {
            return $this->fail('세션이 종료되었습니다.');
        }

        $memberIdx = session()->get('member_idx');

        //$chargeMoney = isset($_POST['money']) ? $_POST['money'] : NULL;
        $depositName = isset($_POST['name']) ? $_POST['name'] : NULL;
        $uKey = md5($memberIdx . strtotime('Now'));
        //$account_number = isset($_POST['account_number']) ? $_POST['account_number'] : 0;

        $memberModel = new MemberModel();

        $sql = "SELECT set_type, set_type_val,u_level FROM t_game_config WHERE set_type = 'service_coin_charge' ";
        $result_game_config = $memberModel->db->query($sql)->getResult();
        if ('Y' == $result_game_config[0]->set_type_val) {
            return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
        }

        if (session()->get('level') > $result_game_config[0]->u_level) {
            return $this->fail('충전 가능 레벨이 아닙니다.');
        }

        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null || $uKey == null) {

            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($findMember->getStatus() == 11) {
            return $this->fail('관리자 승인이 필요합니다.');
        }

        if ($findMember->getUBusiness() != 1) {
            return $this->fail('총판은 충전신청을 할 수 없습니다.');
        }

        $this->initMemberData(session(), session()->get('member_idx'));
        $member_level = $findMember->getLevel();
        $this->logger->debug('MemberMoneyChargeHistoryController chargeCoinRequest depositName : ' . session()->get('account_name'));

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "SELECT idx FROM member_money_charge_history WHERE member_idx = ? AND status in (1,2)";

            $resultList = $memberMCHModel->db->query($sql, [$memberIdx])->getResult();
            if (0 < count($resultList)) {
                return $this->fail('이전 요청 내역이 처리되지 않았으며,관리자에게 문의바랍니다.');
            }

            $deposit_name = session()->get('account_name');

            $bank_id = 1000; // 코인계좌 은행 번호

            $memberMCHModel->chargeCoinRequest(session()->get('account_name'), $findMember->getMoney(), $uKey, 1000);
            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $response = [
                'result_code' => 400,
                'messages' => '충전 신청 실패',
                'messages_detail' => $e,
                'data' => []
            ];
            return $this->respond($response, 200);
        }

        $response = [
            'result_code' => 200,
            'messages' => '충전 신청 성공',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    private function get_request_coin_data() {
        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "SELECT member.id, member.idx as member_idx, member.money as member_money, member.point as member_point, member.coin_password, 
                    member.is_exchange, member.reg_first_charge, member.charge_first_per, member.level 
                    FROM member_money_charge_history 
                    LEFT JOIN member ON member_money_charge_history.member_idx = member.idx
                    WHERE bank_id = 1000 AND member_money_charge_history.status = 1";

            $resultList = $memberMCHModel->db->query($sql)->getResult();

            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
        }

        return $resultList;
    }

    // 코인 충전을 요청한 정보를 가져와서 회원가입 및 로그인 후 잔액조회->코인->게임머니 변환 작업을 해준다.
    // 스케줄러 호출 10초에 한번씩 호출한다.

    public function coin_convert_money() {
        //$this->logger->debug('MemberMoneyChargeHistoryController:coin_convert_money start');
        // 코인 입금요청한 유저 정보를 가져온다.
        $result_list = $this->get_request_coin_data();
        if (!isset($result_list) || count($result_list) == 0)
            return;

        // 관련 컨피그를 로드한다.
        $tGameConfigModel = new TGameConfigModel();
        $get_config_str = "'charge_first_per', 'charge_per', 'charge_max_money', 'charge_money','reg_first_charge'";
        $sql = "select u_level, set_type, set_type_val from t_game_config ";
        $sql .= " where set_type in ($get_config_str) ";
        $retData = $tGameConfigModel->db->query($sql)->getResultArray();
        $n_reg_first_charge = 0;
        foreach ($retData as $row) {
            $db_level = $row['u_level'];

            switch ($row['set_type']) {
                case 'charge_first_per':
                    $db_config[$db_level]['charge_first_per'] = $row['set_type_val'];
                    break;
                case 'charge_per':
                    $db_config[$db_level]['charge_per'] = $row['set_type_val'];
                    break;
                case 'charge_max_money':
                    $db_config[$db_level]['charge_max_money'] = $row['set_type_val'];
                    break;
                case 'charge_money':
                    $db_config[$db_level]['charge_money'] = $row['set_type_val'];
                    break;
                case 'reg_first_charge':
                    $n_reg_first_charge = $row['set_type_val'];
                    break;
            }
        }

        $pullOperations = new PullOperations('COIN', $this->logger);
        $access_token = '';
        $transaction_key = '';
        foreach ($result_list as $value) {
            $result_login = $pullOperations->login($value->id, $value->coin_password);
            $this->logger->debug('MemberMoneyChargeHistoryController coin_convert_money account : ' . $value->id . ' result_login : ' . json_encode($result_login));
            if (false == $result_login->result) { // 회원가입
                $result_signup = $pullOperations->signup($value->id, $value->coin_password);
                $this->logger->debug('MemberMoneyChargeHistoryController coin_convert_money account : ' . $value->id . ' result_signup : ' . json_encode($result_signup));
                if (false == $result_signup->result)
                    continue;

                $access_token = $result_signup->access_token;

                // 트랜잭션 키 값 얻어오기
                $result_getTransactionKey = $pullOperations->getTransactionKey($access_token);
                $this->logger->debug('MemberMoneyChargeHistoryController coin_convert_money account : ' . $value->id . ' result_getTransactionKey : ' . json_encode($result_getTransactionKey));
                if (false == $result_getTransactionKey->result)
                    continue;

                $transaction_key = $result_getTransactionKey->transaction_key;
            } else {
                $access_token = $result_login->access_token;
                $transaction_key = $result_login->transaction_key;
            }

            // 잔액조회 
            $result_balance = $pullOperations->balance($access_token);
            $this->logger->debug('MemberMoneyChargeHistoryController coin_convert_money account : ' . $value->id . ' result_balance : ' . json_encode($result_balance));
            if (false == $result_balance->result || 0 == $result_balance->current_balance) {
                //$this->logger->debug('money : '.$result_balance->current_balance);
                continue;
            }

            // 코인 변환
            $result_withdraw = $pullOperations->withdraw((array) $value, $access_token, $transaction_key, $result_balance->current_balance, $db_config, $n_reg_first_charge);
            $this->logger->debug('MemberMoneyChargeHistoryController coin_convert_money account : ' . $value->id . ' result_withdraw : ' . json_encode($result_withdraw));
        }
    }

    // apk 입금통지
    public function depositNotice() {
        $memberIdx = session()->get('member_idx');
        //$member_level = session()->get('level');
        //$jsonObject = '{"json":[{"depositor":"이종원","amount":"100,000","bank":"국민은행","sender":"01098596400","date":"Wed Jul 14 19:47:11 GMT+09:00 2021","ori":"[Web발신]\n[KB]07/01 07:03\n294501**508\n김민호\nFBS입금\n100,000\n잔액303,286\n(국민은행)"}]}';
        //$this->logger->debug('jsonObject : ' . $_POST['jsonObject']);
        $inputData = json_decode($_POST['jsonObject'], true);
        $depositor = preg_replace("/\s+/", "", $inputData['json'][0]['depositor']);
        $money = str_replace(",", "", $inputData['json'][0]['amount']);
        $bankName = preg_replace("/\s+/", "", $inputData['json'][0]['bank']);
        $ori = $inputData['json'][0]['ori'];
        $date = date("Y-m-d H:i:s", strtotime($inputData['json'][0]['date']));
        //$this->logger->debug('json : '.$depositor);

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();

            $sql = "select bank_id from account where account_name = ?";
            $bankInfo = $memberMCHModel->db->query($sql,[$bankName])->getResultArray()[0];

            if (0 >= count($bankInfo)) {
                $this->logger->error('not find bank : ' . $bankName);
            }
            $bank_id = $bankInfo['bank_id'];

            $memberMCHModel->db->transStart();
            $sql = "insert into member_money_charge_sms(deposit_name, bank_id, bank_name, money, status, ori, create_dt) values(?, ?, ?, ?, 1, ?, now())";

            $resultList = $memberMCHModel->db->query($sql, [$depositor, $bank_id, $bankName, $money, $ori]);
            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $response = [
                'result_code' => 300,
                'messages' => '요청 실패',
                'messages_detail' => $e
            ];
            return $this->respond($response, 200);
        }

        // 자동충전 처리
        //$this->auto_money_charge();

        $response = [
            'result_code' => 200,
            'messages' => '충전 신청 성공'
        ];
        return $this->respond($response, 200);
    }

    public function auto_money_charge() { // 10초에 한번씩 호출된다.
        try {
            //$this->logger->debug('::::::::::::::: auto_money_charge start');
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "select idx, deposit_name, bank_id, bank_name, money, create_dt from member_money_charge_sms ";
            $sql .= " where status = 1";
            $result_deposit_get = $memberMCHModel->db->query($sql)->getResultArray();
            if (0 >= count($result_deposit_get)) {
                $this->logger->debug('do not auto_charge data : '.$sql);

                $memberMCHModel->db->transRollback();
                return;
            }

            // 돌발충전 보너스 값
            $sql = "select bonus, max_bonus from charge_event;";
            $chargeEventData = $memberMCHModel->db->query($sql)->getResultArray();

            $a_comment = "충전완료";
            $ac_code = 1;
            $cash_use_kind = 'P';
            $get_config_str = "'charge_first_per', 'charge_per', 'charge_max_money', 'charge_money','reg_first_charge','event_charge_status','event_charge_start','event_charge_end' ";
            $sql = "select u_level, set_type, set_type_val from t_game_config ";
            $sql .= " where set_type in ($get_config_str) ";
            $retData = $memberMCHModel->db->query($sql)->getResultArray();
            $n_reg_first_charge = 0;
            $str_set_type = '';
            $n_event_charge_status = 'OFF';
            $n_event_charge_start = date("Y-m-d H:i:s");
            $n_event_charge_end = date("Y-m-d H:i:s");
            foreach ($retData as $row) {
                $db_level = $row['u_level'];

                switch ($row['set_type']) {
                    case 'charge_first_per':
                        $db_config[$db_level]['charge_first_per'] = $row['set_type_val'];
                        break;
                    case 'charge_per':
                        $db_config[$db_level]['charge_per'] = $row['set_type_val'];
                        break;
                    case 'charge_max_money':
                        $db_config[$db_level]['charge_max_money'] = $row['set_type_val'];
                        break;
                    case 'charge_money':
                        $db_config[$db_level]['charge_money'] = $row['set_type_val'];
                        break;
                    case 'reg_first_charge':
                        $n_reg_first_charge = $row['set_type_val'];
                        break;
                    case 'event_charge_status':
                        $n_event_charge_status = $row['set_type_val'];
                        break;
                    case 'event_charge_start':
                        $n_event_charge_start = $row['set_type_val'];
                        break;
                    case 'event_charge_end':
                        $n_event_charge_end = $row['set_type_val'];
                        break;
                }
            }

            //$this->logger->debug('::::::::::::::: auto_money_charge result_deposit_get ' . json_encode($result_deposit_get));
            $currentDate = date("Y-m-d H:i:s");

            foreach ($result_deposit_get as $result_get) {
                $deposit_get_idx = $result_get['idx'];
                $deposit_name = $result_get['deposit_name'];
                $money = $result_get['money'];
                $bank_id = $result_get['bank_id'];
                $bank_name = $result_get['bank_name'];
                $endTime = date("Y-m-d H:i:s", strtotime($result_get['create_dt'] . "+" . 15 . " minutes"));
                if ($endTime <= $currentDate) {
                    $sql = "update member_money_charge_sms set status = 2 where idx = ?";
                    $memberMCHModel->db->query($sql,[$deposit_get_idx]);
                    $this->logger->debug('::::::::::::::: auto_money_charge time out fail ' . $endTime . ' : ' . $retData[0]['create_dt']);
                    continue;
                }

                // 개인계좌 체크
                if ($bank_name == '수협') {
                    $sql = "select a.idx, b.idx as charge_idx, a.level, a.money, b.money as set_money,a.point, a.is_exchange, a.reg_first_charge,a.charge_first_per,b.create_dt,b.u_key, b.charge_point_yn"
                            . ",b.bonus_level,b.bonus_option_idx"
                            . " from member a, member_money_charge_history b ";
                    $sql .= " where a.idx=b.member_idx and b.deposit_name = ? and b.money = ? AND b.status = 1 ORDER BY idx DESC limit 1";
                    $retData = $memberMCHModel->db->query($sql,[$deposit_name,$money])->getResultArray();
                    if (!isset($retData) || 0 == count($retData)) {
                        //$this->logger->debug(':::::::: do not charge_history data');
                        continue;
                    }

                    $sql = "select charge_type from charge_type where level = ?";
                    $chargeTypeData = $memberMCHModel->db->query($sql,[ $retData[0]['level']])->getResultArray()[0];
                    if (4 != $chargeTypeData['charge_type']) {
                        $sql = "select a.idx, b.idx as charge_idx, a.level, a.money, b.money as set_money,a.point, a.is_exchange, a.reg_first_charge,a.charge_first_per,b.create_dt,b.u_key, b.charge_point_yn"
                                . ",b.bonus_level,b.bonus_option_idx"
                                . " from member a, member_money_charge_history b ";
                        $sql .= " where a.idx=b.member_idx and b.deposit_name = ? and b.money = ? and b.bank_id = ? AND b.status = 1 ORDER BY idx DESC limit 1";
                        $retData = $memberMCHModel->db->query($sql,[$deposit_name,$money,$bank_id])->getResultArray();
                    }
                } else {
                    $sql = "select a.idx, b.idx as charge_idx, a.level, a.money, b.money as set_money,a.point, a.is_exchange, a.reg_first_charge,a.charge_first_per,b.create_dt,b.u_key, b.charge_point_yn"
                            . ",b.bonus_level,b.bonus_option_idx"
                            . " from member a, member_money_charge_history b ";
                    $sql .= " where a.idx=b.member_idx and b.deposit_name = ? and b.money = ? and b.bank_id = ? AND b.status = 1 ORDER BY idx DESC limit 1";
                    $retData = $memberMCHModel->db->query($sql,[$deposit_name,$money,$bank_id])->getResultArray();
                }
                if (!isset($retData) || 0 == count($retData)) {
                    //$this->logger->debug(':::::::: do not charge_history data');
                    continue;
                }

                // 문제시 아래걸로 변경
                /* $sql = "select a.idx, b.idx as charge_idx, a.level, a.money, b.money as set_money,a.point, a.is_exchange, a.reg_first_charge,a.charge_first_per,b.create_dt,b.u_key, a.charge_point_yn from member a, member_money_charge_history b ";
                  $sql .= " where a.idx=b.member_idx and b.deposit_name = '$deposit_name' and b.money = $money and b.bank_id = $bank_id AND b.status = 1 ORDER BY idx DESC limit 1";
                  $retData = $memberMCHModel->db->query($sql)->getResultArray();
                  if (!isset($retData) || 0 == count($retData)) {
                  //$this->logger->debug(':::::::: do not charge_history data');
                  continue;
                  } */

                $m_idx = $retData[0]['idx'];
                $create_dt = $retData[0]['create_dt'];
                $charge_idx = $retData[0]['charge_idx'];
                $now_cash = $retData[0]['money'];
                $set_money = $retData[0]['set_money'];
                $u_level = $retData[0]['level'];
                $is_exchange = $retData[0]['is_exchange'];
                $now_point = $retData[0]['point'];
                $u_key = $retData[0]['u_key'];

                $af_point = 0;
                $ch_bonus = 0;
                $p_a_comment = '';
                $ch_point = 0;
                $p_ac_code = 0;
                $n_is_reg_first_charge = $retData[0]['reg_first_charge'];
                $n_is_charge_first_per = $retData[0]['charge_first_per'];
                $n_is_charge_event = false;
                $ratio_value = 0;
                $const_value = 0;
                //if (1 == $retData['charge_point_yn']) {
                if ($n_event_charge_status == 'ON' && date("Y-m-d " . $n_event_charge_start) <= $currentDate && date("Y-m-d " . $n_event_charge_end) >= $currentDate &&
                        $chargeEventData[$u_level - 1]['bonus'] > 0) { // 돌발첫충
                    $ch_point = ($chargeEventData[$u_level - 1]['bonus'] * $set_money) / 100;
                    if ($chargeEventData[$u_level - 1]['max_bonus'] < $ch_point) {
                        $ch_point = $chargeEventData[$u_level - 1]['max_bonus'];
                    }
                    $p_a_comment = '포인트 돌발충전 : ' . 'charge_event_per';
                    $str_set_type = 'charge_event_per';
                    $n_is_charge_event = true;
                } else {
                    if ('0' == $n_is_reg_first_charge) { // 가입 첫충전 
                        $ch_point = ($n_reg_first_charge * $set_money) / 100;
                        if ($db_config[$u_level]['charge_max_money'] < $ch_point) {
                            $ch_point = $db_config[$u_level]['charge_max_money'];
                        }
                        $p_a_comment = '포인트 충전 : ' . 'reg_first_charge';
                        $str_set_type = 'reg_first_charge';
                    } else if ('0' == $n_is_charge_first_per) { // 매일 첫 충전
                        $bonus_level = $retData[0]['bonus_level'];
                        $bonus_option_idx = $retData[0]['bonus_option_idx'];

                        list($ch_point, $ratio_value, $const_value) = $this->do_select_charge_first($bonus_level,$bonus_option_idx,$set_money,$memberMCHModel);


                        $p_a_comment = '포인트 충전 : ' . 'charge_first_per';
                        $str_set_type = 'charge_first_per';
                    //} else if (0 == $is_exchange) {
                    } else {

                        $bonus_level = $retData[0]['bonus_level'];
                        $bonus_option_idx = $retData[0]['bonus_option_idx'];

                        list($ch_point, $ratio_value, $const_value) = $this->do_select_charge($bonus_level,$bonus_option_idx,$set_money,$memberMCHModel);
                   
                        $p_a_comment = '포인트 충전 : ' . 'charge_per';
                        $str_set_type = 'charge_per';
                    }
                }
                //}
                //echo $p_a_comment.' '.$ch_point.'   '.$bonus_option_idx.'   '.$ratio_value;

                $p_ac_code = 10;
                $af_point = $ch_point + $now_point;
                $af_cash = $now_cash + $set_money;
                //$g_money = $f_gm_pt_charge_value * $set_money;

                // 가입첫충이거나 이벤트 충전이다.
                if ((0 == $n_is_reg_first_charge || true == $n_is_charge_event) && 1 == $retData[0]['charge_point_yn']) {
                    $sql = "update member set reg_first_charge = 1, charge_first_per = 1, money = money + ?, point = point + ? where idx = ? ";
                } else if (0 == $n_is_charge_first_per && 1 == $retData[0]['charge_point_yn']) {
                    $sql = "update member set charge_first_per = 1,money = money + ?, point = point + ? where idx = ? ";
                } else {
                    $sql = "update member set money = money + ?, point = point + ? where idx = ? ";
                }

                $memberMCHModel->db->query($sql,[$set_money,$ch_point,$m_idx]);
                UserPayBack::AddCharge($m_idx,$set_money,$memberMCHModel);
                
                $execute = new DayChargeEvent($this->logger);
                $execute->AddCharge($m_idx,1,$set_money);    
                
                // 입금성공한 금액이 최대 입금금액 보다 많으면 데이터를 갱신해준다.
                $sql_tot = "SELECT max_charge FROM total_member_cash WHERE member_idx = ?";
                $max_charge_arr = $memberMCHModel->db->query($sql_tot,[$m_idx])->getResultArray();
                if (true === isset($max_charge_arr) && 0 < count($max_charge_arr)) {
                    $max_charge = $max_charge_arr[0]['max_charge'];
                    if ($max_charge < $set_money) {
                        $sql_up_tot = "UPDATE total_member_cash set bf_max_charge = ?, max_charge = ? WHERE member_idx = ?";
                        $memberMCHModel->db->query($sql_up_tot,[$max_charge,$set_money,$m_idx]);
                    }
                }

                $sql = "update member_money_charge_history set bonus_point = ?,set_type = ?, status = 3, update_dt=now(), result_money = ?, referenceId= ? 
				,const_value = ? ,ratio_value = ? 
				where idx = ?";
                // 로그에는 두개합산값으로 저장한다.
                $set_money = $set_money + $ch_point;

                $memberMCHModel->db->query($sql,[$ch_point,$str_set_type,$af_cash,$deposit_get_idx,$const_value,$ratio_value ,$charge_idx]);

                $sql = "update member_money_charge_sms set status = 3, update_dt = now() where idx = ?";
                $memberMCHModel->db->query($sql,[$deposit_get_idx]);
                
                $this->gmPt->giveGMoneyCharge($memberMCHModel,$u_key,$m_idx,$set_money,$now_cash,$af_cash
                ,$ch_point,$now_point,$af_point,$p_a_comment,$charge_idx,$this->logger);
            }

            $memberMCHModel->db->transComplete();

            //$this->logger->debug('::::::::::::::: auto_money_charge end');
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] auto_money_charge (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')' . ' query : ' . $memberMCHModel->getLastQuery());
            $memberMCHModel->db->transRollback();
            return;
        } catch (\Exception $e) {
            $this->logger->error('::::::::::::::: auto_money_charge Exception : ' . $e->getMessage());
            $memberMCHModel->db->transRollback();
            return;
        } catch (\ReflectionException $e) {
            $this->logger->error('::::::::::::::: auto_money_charge ReflectionException : ' . $e->getMessage());
            $memberMCHModel->db->transRollback();
        }
    }

    private function do_select_charge_first($bonus_level, $bonus_option_idx,$set_money, $memberMCHModel) {
        $sql = "select * from charge_type where level = ?";
        $retChargeTypeData = $memberMCHModel->db->query($sql, [$bonus_level])->getResultArray();
        
        $const_value = $ratio_value = 0;
        if (0 >= $bonus_option_idx) {
            $ch_point = 0;
        } else if (1 == $bonus_option_idx) {
            if (0 < $retChargeTypeData[0]['bonus_1_charge_first_money']) {
                $ch_point = $retChargeTypeData[0]['bonus_1_charge_first_money'];
                $const_value = $retChargeTypeData[0]['bonus_1_charge_first_money'];
            } else {
                $ch_point = ($retChargeTypeData[0]['bonus_1_charge_first_per'] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0]['bonus_1_charge_first_per'];
            }

            if ($retChargeTypeData[0]['bonus_1_charge_first_max_money'] < $ch_point) {
                $ch_point = $retChargeTypeData[0]['bonus_1_charge_first_max_money'];
            }
        } else if (2 == $bonus_option_idx) {
            if (0 < $retChargeTypeData[0]['bonus_2_charge_first_money']) {
                $ch_point = $retChargeTypeData[0]['bonus_2_charge_first_money'];
                $const_value = $retChargeTypeData[0]['bonus_2_charge_first_money'];
            } else {
                $ch_point = ($retChargeTypeData[0]['bonus_2_charge_first_per'] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0]['bonus_2_charge_first_per'];
            }

            if ($retChargeTypeData[0]['bonus_2_charge_first_max_money'] < $ch_point) {
                $ch_point = $retChargeTypeData[0]['bonus_2_charge_first_max_money'];
            }
        }
        
        return [$ch_point, $ratio_value, $const_value];
    }

    private function do_select_charge($bonus_level, $bonus_option_idx,$set_money, $memberMCHModel) {
        $sql = "select * from charge_type where level = ?";
        $retChargeTypeData = $memberMCHModel->db->query($sql, [$bonus_level])->getResultArray();
        
        $const_value = $ratio_value = 0;
        if (0 >= $bonus_option_idx) {
            $ch_point = 0;
        } else if (1 == $bonus_option_idx) {
            if (0 < $retChargeTypeData[0]['bonus_1_charge_money']) {
                $ch_point = $retChargeTypeData[0]['bonus_1_charge_money'];
                $const_value = $retChargeTypeData[0]['bonus_1_charge_money'];
            } else {
                $ch_point = ($retChargeTypeData[0]['bonus_1_charge_per'] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0]['bonus_1_charge_per'];
            }

            if ($retChargeTypeData[0]['bonus_1_charge_max_money'] < $ch_point) {
                $ch_point = $retChargeTypeData[0]['bonus_1_charge_max_money'];
            }
        } else if (2 == $bonus_option_idx) {
            if (0 < $retChargeTypeData[0]['bonus_2_charge_money']) {
                $ch_point = $retChargeTypeData[0]['bonus_2_charge_money'];
                $const_value = $retChargeTypeData[0]['bonus_2_charge_money'];
            } else {
                $ch_point = ($retChargeTypeData[0]['bonus_2_charge_per'] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0]['bonus_2_charge_per'];
            }

            if ($retChargeTypeData[0]['bonus_2_charge_max_money'] < $ch_point) {
                $ch_point = $retChargeTypeData[0]['bonus_2_charge_max_money'];
            }
        }

        return [$ch_point, $ratio_value, $const_value];
    }

    public function setIsCoinGuid() {
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null) {
            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        // 네오덱스 가입처리
        $pullOperations = new PullOperations('COIN', $this->logger);
        $result = $pullOperations->registed($findMember->getId());
        if (!$result->is_registed) {
            $result_signup = $pullOperations->signup($findMember->getId(), $findMember->getCoinPassword());
            if (false == $result_signup->result) {
                $this->logger->error('MemberMoneyChargeHistoryController::setIsCoinGuid join fail : ' . $findMember->getId() . ' password : ' . $findMember->getCoinPassword());
                return $this->fail('네오덱스 가입실패입니다. 문의해주세요.');
            }
        }

        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "update member set is_coin_guid = 'Y' where idx = ?";
        $memberMCHModel->db->query($sql,[$memberIdx]);

        $response = [
            'result_code' => 200,
            'messages' => '업데이트 성공',
            'data' => [
            ]
        ];
        return $this->respond($response, 200);
    }

    // 코인사이트 가입유무 체크 및 잔액조회
    public function getIsJoinCoinSite() {
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null) {
            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        $pullOperations = new PullOperations('COIN', $this->logger);

        $result_login = $pullOperations->login($findMember->getId(), $findMember->getCoinPassword());
        if (false == $result_login->result) {
            $this->logger->debug('MemberMoneyChargeHistoryController getIsJoinCoinSite account : ' . $findMember->getId() . ' result_login : ' . json_encode($result_login));
            return $this->fail('로그인 실패.');
        }

        $access_token = $result_login->access_token;
        $transaction_key = $result_login->transaction_key;
        $findMember->setAccess_token($access_token);

        $response = [
            'result_code' => 200,
            'messages' => '성공',
            'data' => [
                'access_token' => $access_token
            ]
        ];

        return $this->respond($response, 200);
    }

    // 잔액조회 및 점검체크
    public function getBalance() {
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null) {
            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        $sql = "SELECT set_type_val FROM t_game_config WHERE set_type = 'service_coin_charge'";
        $config = $memberModel->db->query($sql)->getResultArray()[0];
        if ($config['set_type_val'] == 'Y') {
            $response = [
                'result_code' => 300,
                'messages' => '점검중',
                'data' => [
                    'balance' => 0
                ]
            ];
            return $this->respond($response, 200);
        }

        $pullOperations = new PullOperations('COIN', $this->logger);
        $result = $pullOperations->registed($findMember->getId());
        if (!$result->is_registed) {
            if (502 == $result->msg) {
                $response = [
                    'result_code' => 300,
                    'messages' => '점검중',
                    'data' => [
                        'balance' => 0
                    ]
                ];
                return $this->respond($response, 200);
            }
            $response = [
                'result_code' => 200,
                'messages' => '성공',
                'data' => [
                    'balance' => 0
                ]
            ];
            return $this->respond($response, 200);
        }

        $result_login = $pullOperations->login($findMember->getId(), $findMember->getCoinPassword());
        if (false == $result_login->result) {
            $this->logger->debug('MemberMoneyChargeHistoryController getBalance login fail account : ' . $findMember->getId() . ' result_login : ' . json_encode($result_login));
            $response = [
                'result_code' => 200,
                'messages' => '성공',
                'data' => [
                    'balance' => 0
                ]
            ];
            return $this->respond($response, 200);
        }

        $access_token = $result_login->access_token;
        $transaction_key = $result_login->transaction_key;
        $findMember->setAccess_token($access_token);
        $result_balance = $pullOperations->balance($access_token);

        if (false == $result_balance->result || 0 == $result_balance->current_balance) {
            $current_balance = 0;
        } else {
            $current_balance = $result_balance->current_balance;
        }

        $response = [
            'result_code' => 200,
            'messages' => '성공',
            'data' => [
                'balance' => $current_balance
            ]
        ];

        return $this->respond($response, 200);
    }

    // 가상계좌 상품조회
    public function markerSellerInfo() {
        $pullOperations = new VirtualAccount($this->logger);
        $result = $pullOperations->markerSellerInfo();

        if ('0000' != $result->resultCode) {
            $this->logger->debug('markerSellerInfo :: 상품조회 실패');
            return;
        }

        //$arrMoney = array(10000,50000,100000,1000000);
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = array();
        if (count($result->productList) > 0) {
            foreach ($result->productList as $item) {
                if ($item->p_price <= 1800000) {
                    $name = addslashes($item->p_name);
                    $insertSql = "($item->p_seq, '$name', $item->p_price, '$item->p_sell_state')";
                    array_push($sql, $insertSql);
                }
            }
        }

        if (count($sql) > 0) {
            try {
                $memberMCHModel->db->query(
                        'INSERT INTO `virtual_goods_list` ('
                        . 'p_seq, '
                        . 'p_name, '
                        . 'p_price, '
                        . 'p_sell_state) VALUES '
                        . implode(',', $sql)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'p_seq = VALUES(p_seq), '
                        . 'p_name = VALUES(p_name), '
                        . 'p_price = VALUES(p_price), '
                        . 'p_sell_state = VALUES(p_sell_state)'
                );
            } catch (\mysqli_sql_exception $e) {
                $query_str = (string) $memberMCHModel->getLastQuery();
                $this->logger->error("- markerSellerInfo error query_string : " . $query_str);
                return;
            }
        }

        return;
    }

    // 구매 및 가상계좌 발급요청
    public function markerBuyProduct() {
        $m_id = isset($_POST['m_id']) ? $_POST['m_id'] : 0;
        $m_name = isset($_POST['m_name']) ? $_POST['m_name'] : 0;
        $p_seq = isset($_POST['p_seq']) ? $_POST['p_seq'] : 0;
        $mid = isset($_POST['mid']) ? $_POST['mid'] : 0;

        //$memberModel = new MemberModel();

        $virtualAccount = new VirtualAccount($this->logger);
        $result = $virtualAccount->markerSellerInfo();

        if ('0000' != $result->resultCode) {
            $this->logger->debug('markerSellerInfo :: 상품조회 실패');
            return;
        }

        $arrMoney = array(10000, 50000, 100000, 1000000);
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = array();
        if (count($result->productList) > 0) {
            foreach ($result->productList as $item) {
                if (in_array($item->p_price, $arrMoney)) {
                    $name = addslashes($item->p_name);
                    $insertSql = "($item->p_seq, '$name', $item->p_price, '$item->p_sell_state')";
                    array_push($sql, $insertSql);
                }
            }
        }

        if (count($sql) > 0) {
            try {
                $memberMCHModel->db->query(
                        'INSERT INTO `virtual_goods_list` ('
                        . 'p_seq, '
                        . 'p_name, '
                        . 'p_price, '
                        . 'p_sell_state) VALUES '
                        . implode(',', $sql)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'p_seq = VALUES(p_seq), '
                        . 'p_name = VALUES(p_name), '
                        . 'p_price = VALUES(p_price), '
                        . 'p_sell_state = VALUES(p_sell_state)'
                );
            } catch (\mysqli_sql_exception $e) {
                $query_str = (string) $memberMCHModel->getLastQuery();
                $this->logger->error("- markerSellerInfo error query_string : " . $query_str);
                return;
            }
        }

        return;
    }

    // 가상계좌 구매요청
    public function virtualChargeRequest() {
        if (false == session()->has('member_idx')) {
            return $this->fail('세션이 종료되었습니다.');
        }

        $memberIdx = session()->get('member_idx');
        $member_level = session()->get('level');
        $nick_name = session()->get('nick_name');
        $member_id = session()->get('id');
        $account_name = session()->get('account_name');

        $money = isset($_POST['money']) ? $_POST['money'] : 0;
        $uKey = isset($_POST['u_key']) ? $_POST['u_key'] : NULL;
        $currentDate = date("Y-m-d H:i:s");
        $checkStartTime = date("Y-m-d 23:40:00");
        $checkEndTime = date("Y-m-d 00:30:00");

        $memberModel = new MemberModel();

        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null /* || $uKey == null */) {
            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($findMember->getStatus() == 11) {
            return $this->fail('관리자 승인이 필요합니다.');
        }

        if ($findMember->getUBusiness() != 1) {
            $response['messages'] = '총판은 충전신청을 할 수 없습니다.';
            return $this->fail($response);
        }

        // 페이윈 점검시간 체크
        if ($currentDate >= $checkStartTime || $currentDate <= $checkEndTime) {
            return $this->fail('충전 점검중입니다. 00시 30분 이후 신청해주시기 바랍니다.');
        }

        // 점검체크
        /* $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_charge' ";
          $result_game_config = $memberModel->db->query($sql)->getResult();
          if ('Y' == $result_game_config[0]->set_type_val) {
          return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
          } */

        // 5분에 한번만 가능
        $sql = "SELECT create_dt FROM member_money_charge_history where member_idx = ? and bank_id = 2000 order by idx desc limit 1;";
        $result = $memberModel->db->query($sql,[$memberIdx])->getResultArray();
        if (0 < count($result)) {
            $create_dt = $result[0]['create_dt'];
            $checkTime = date("Y-m-d H:i:s", strtotime($create_dt . "+5 minutes"));
            if ($currentDate <= $checkTime)
                return $this->fail('5분후에 다시 신청해주시기 바랍니다.');
        }

        // 해당가격의 상품을 찾는다.
        $sql = "SELECT p_seq, p_name FROM virtual_goods_list where p_price = ? and p_sell_state = 'SELL' ORDER BY RAND() limit 1";
        $result = $memberModel->db->query($sql, [$money])->getResultArray();
        if (0 == count($result)) {
            return $this->fail('구매오류입니다. 고객센터에 문의해주세요.');
        }
        $p_seq = $result[0]['p_seq'];
        $p_name = $result[0]['p_name'];

        $member_level = $findMember->getLevel();
        $bonus_point = 0;

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "SELECT idx FROM member_money_charge_history WHERE member_idx = ? AND status in (1,2)";
            $resultList = $memberMCHModel->db->query($sql,[$memberIdx])->getResult();
            if (0 < count($resultList)) {
                $memberMCHModel->db->transRollback();
                return $this->fail('이전 요청 내역이 처리되지 않았으며,관리자에게 문의바랍니다.');
            }

            $virtualAccount = new VirtualAccount($this->logger);
            $virtual_info = $virtualAccount->markerBuyProduct($member_id, $account_name, $p_seq);
            //print_r($virtual_info);
            if ('0000' != $virtual_info->resultCode) {
                $memberMCHModel->db->transRollback();
                $this->logger->debug('virtualChargeRequest :: 상품구매 실패');
                return;
            }

            $virtual_info->orderVO->o_seq;
            $virtual_info->orderVO->o_product_seq;
            $virtual_info->orderVO->o_sender_bank;
            $o_sender_bank_name = $virtual_info->orderVO->o_sender_bank_name;
            $o_virtual_account = $o_virtual_account = $virtual_info->orderVO->o_virtual_account;
            $o_virtual_account_date = $virtual_info->orderVO->o_virtual_account_date;
            $tid = $virtual_info->orderVO->tid;
            $comment = json_encode(array('o_seq' => $virtual_info->orderVO->o_seq, 'o_product_seq' => $virtual_info->orderVO->o_product_seq,
                'o_sender_bank' => $virtual_info->orderVO->o_sender_bank, 'o_virtual_account_date' => $o_virtual_account_date));

            //$deposit_name = session()->get('account_name');

            $bank_id = 2000;
            $account_name = '나린마켓';
            //$this->logger->debug('MemberMoneyChargeHistoryController chargeRequest referenceId : ' . $referenceId);
            // 입금요청 신청
            $memberMCHModel->chargeVirtualRequest(session()->get('account_name'), $money, $findMember->getMoney(), $uKey, $bank_id, $o_virtual_account, $tid, $bonus_point, '', $o_sender_bank_name, $account_name, $comment);
            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $response = [
                'result_code' => 400,
                'messages' => '가상계좌 신청 실패',
                'messages_detail' => $e,
                'data' => [
                ]
            ];
            return $this->respond($response, 200);
        }

        $response = [
            'result_code' => 200,
            'messages' => '가상계좌 신청 성공',
            'data' => [
                'account_number' => $o_sender_bank_name,
                'account_bank' => $o_virtual_account,
                'account_date' => $o_virtual_account_date
            ]
        ];
        return $this->respond($response, 200);
    }

    // 가상계좌 구매요청
    public function virtualChargeRequest_renew() {
        if (false == session()->has('member_idx')) {
            return $this->fail('세션이 종료되었습니다.');
        }

        $memberIdx = session()->get('member_idx');
        $member_level = session()->get('level');
        $nick_name = session()->get('nick_name');
        $member_id = session()->get('id');
        $account_name = session()->get('account_name');

        $money = isset($_POST['money']) ? $_POST['money'] : 0;
        //$uKey = isset($_POST['u_key']) ? $_POST['u_key'] : NULL;
        $currentDate = date("Y-m-d H:i:s");

        $memberModel = new MemberModel();

        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null /* || $uKey == null */) {
            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($findMember->getStatus() == 11) {
            return $this->fail('관리자 승인이 필요합니다.');
        }

        if ($findMember->getUBusiness() != 1) {
            $response['messages'] = '총판은 충전신청을 할 수 없습니다.';
            return $this->fail($response);
        }

        // 점검체크
        $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_charge' ";
        $result_game_config = $memberModel->db->query($sql)->getResult();
        if ('Y' == $result_game_config[0]->set_type_val) {
            return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
        }

        // 5분에 한번만 가능
        /* $sql = "SELECT create_dt FROM member_money_charge_history where member_idx = $memberIdx and bank_id = 2000 order by idx desc limit 1;";
          $result = $memberModel->db->query($sql)->getResultArray();
          if (0 < count($result)) {
          $create_dt = $result[0]['create_dt'];
          $checkTime = date("Y-m-d H:i:s",strtotime($create_dt. "+5 minutes"));
          if($currentDate <= $checkTime)
          return $this->fail('5분후에 다시 신청해주시기 바랍니다.');
          } */

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "SELECT idx FROM member_money_charge_history WHERE member_idx = ? AND status in (1,2)";
            $resultList = $memberMCHModel->db->query($sql,[$memberIdx])->getResult();
            if (0 < count($resultList)) {
                $memberMCHModel->db->transRollback();
                return $this->fail('이전 요청 내역이 처리되지 않았으며,관리자에게 문의바랍니다.');
            }

            // 은행코드 정보를 가져온다.
            $sql = "SELECT bank_id, bank_code FROM account WHERE account_name = ? ";
            $accountInfo = $memberMCHModel->db->query($sql,[$findMember->getAccountBank()])->getResultArray();
            if (0 == count($accountInfo)) {
                $memberMCHModel->db->transRollback();
                $this->logger->error('virtualChargeRequest_renew account_name : ' . $findMember->getAccountBank());
                return $this->fail('입금자 은행코드가 없습니다. 관리자에게 문의해주세요.');
            }
            $order_bank_id = $accountInfo[0]['bank_id'];
            $order_bank_code = $accountInfo[0]['bank_code'];

            // 페이먼트 서버로 가상계좌 요청
            $payment = new Payment($this->logger);
            $virtual_info = $payment->requestOrder(1, $memberIdx, $account_name, $order_bank_id, $order_bank_code, $findMember->getAccountNumber(), $findMember->getCall(), $money);
            if (1 != $virtual_info->result_code) {
                $memberMCHModel->db->transRollback();
                $this->logger->error('virtualChargeRequest_renew error : ' . $virtual_info->result_code . ' messages : ' . $virtual_info->messages);
                return;
            }

            $o_sender_bank_name = $virtual_info->data->virtual_account_bank;
            $o_virtual_account = $o_virtual_account = $virtual_info->data->virtual_account_number;
        
            $o_virtual_account_date = '없음';
            $tid = ''; // 현시점에서는 알수가 없다.
           
            $comment = '';
            $o_virtual_account_name = '나린마켓';
            //$this->logger->debug('MemberMoneyChargeHistoryController chargeRequest referenceId : ' . $referenceId);
            // 입금요청 신청
            //$ukey = md5($memberIdx . strtotime('now'));
            $memberMCHModel->chargeVirtualRequest(session()->get('account_name'), $money, $findMember->getMoney(), $uKey, 2000, $o_virtual_account, $tid, $bonus_point, '', $o_sender_bank_name, $o_virtual_account_name, $comment);
            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $response = [
                'result_code' => 400,
                'messages' => '가상계좌 신청 실패',
                'messages_detail' => $e,
                'data' => [
                ]
            ];
            return $this->respond($response, 200);
        }

        $response = [
            'result_code' => 200,
            'messages' => '가상계좌 신청 성공',
            'data' => [
                'account_number' => $o_sender_bank_name,
                'account_bank' => $o_virtual_account,
                'account_date' => $o_virtual_account_date
            ]
        ];
        return $this->respond($response, 200);
    }

    private function get_request_virtual_data() {
        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();
            $sql = "SELECT member.id, member.idx as member_idx, member.money as member_money, member.point as member_point, 
                    member.is_exchange, member.reg_first_charge, member.charge_first_per, member.level, member_money_charge_history.idx, 
                    member_money_charge_history.comment
                    FROM member_money_charge_history 
                    LEFT JOIN member ON member_money_charge_history.member_idx = member.idx
                    WHERE bank_id = 2000 AND member_money_charge_history.status = 1";

            $resultList = $memberMCHModel->db->query($sql)->getResult();

            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
        }

        return $resultList;
    }

    public function virtual_auto_charge_money_renew() {
        // 입금완료된 내용을 가져온다.
        // 페이먼트 서버로 가상계좌 요청
        $payment = new Payment($this->logger);
        $depositList = $payment->GetChargeNoti(1);
        if (1 != $depositList->result_code) {
            $this->logger->error('virtual_auto_charge_money_renew error : ' . $depositList->result_code . ' messages : ' . $depositList->messages);
            return;
        }

        if (!isset($depositList) || count($depositList->data->list) == 0) {
            $this->logger->debug(':::::::: do not virtual_auto_charge_money_renew data');
            return;
        }

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $memberMCHModel->db->transStart();

            // 돌발충전 보너스 값
            $sql = "select bonus, max_bonus from charge_event;";
            $chargeEventData = $memberMCHModel->db->query($sql)->getResultArray();

            $a_comment = "충전완료";
            $ac_code = 1;
            $cash_use_kind = 'P';
            $get_config_str = "'charge_first_per', 'charge_per', 'charge_max_money', 'charge_money','reg_first_charge','event_charge_status','event_charge_start','event_charge_end'";
            $sql = "select u_level, set_type, set_type_val from t_game_config ";
            $sql .= " where set_type in ($get_config_str) ";
            $retData = $memberMCHModel->db->query($sql)->getResultArray();
            $n_reg_first_charge = 0;
            $str_set_type = '';
            $n_event_charge_status = 'OFF';
            $n_event_charge_start = date("Y-m-d H:i:s");
            $n_event_charge_end = date("Y-m-d H:i:s");
            foreach ($retData as $row) {
                $db_level = $row['u_level'];

                switch ($row['set_type']) {
                    case 'charge_first_per':
                        $db_config[$db_level]['charge_first_per'] = $row['set_type_val'];
                        break;
                    case 'charge_per':
                        $db_config[$db_level]['charge_per'] = $row['set_type_val'];
                        break;
                    case 'charge_max_money':
                        $db_config[$db_level]['charge_max_money'] = $row['set_type_val'];
                        break;
                    case 'charge_money':
                        $db_config[$db_level]['charge_money'] = $row['set_type_val'];
                        break;
                    case 'reg_first_charge':
                        $n_reg_first_charge = $row['set_type_val'];
                        break;
                    case 'event_charge_status':
                        $n_event_charge_status = $row['set_type_val'];
                        break;
                    case 'event_charge_start':
                        $n_event_charge_start = $row['set_type_val'];
                        break;
                    case 'event_charge_end':
                        $n_event_charge_end = $row['set_type_val'];
                        break;
                }
            }

            //$this->logger->debug('::::::::::::::: auto_money_charge result_deposit_get ' . json_encode($result_deposit_get));
            $currentDate = date("Y-m-d H:i:s");
            $consumeList = array(); // 충전완료처리된 데이터
            foreach ($depositList->data->list as $key => $value) {
                $deposit_get_idx = $value->idx;
                $money = $value->money;
                $bank_id = 2000; //$result_get['bank_id'];
                $bank_name = $value->virtual_account_bank;
                $bank_account_number = $value->virtual_account_number;
                $member_idx = $value->member_idx;
                $deposit_name = $value->order_name;
                $moid = $value->moid;
                $tid = $value->tid;
              
                // 해당하는 요청건이 있는지 체크한다.
                $sql = "select a.idx, b.idx as charge_idx, a.level, a.money, b.money as set_money,a.point, a.is_exchange, a.reg_first_charge,a.charge_first_per,b.create_dt,b.u_key from member a, member_money_charge_history b ";
                $sql .= " where a.idx=b.member_idx and b.deposit_name = ? and b.account_number = ? and b.money = ? and b.bank_id = ? AND b.status = 1 ORDER BY idx DESC limit 1";
                $retData = $memberMCHModel->db->query($sql,[$deposit_name,$bank_account_number,$money,$bank_id])->getResultArray();
                if (!isset($retData) || 0 == count($retData)) {
                    $this->logger->debug(':::::::: do not charge_history data');
                    continue;
                }

                //print_r($retData);
                $m_idx = $retData[0]['idx'];
                $create_dt = $retData[0]['create_dt'];
                $charge_idx = $retData[0]['charge_idx'];
                $now_cash = $retData[0]['money'];
                $set_money = $retData[0]['set_money'];
                $u_level = $retData[0]['level'];
                $is_exchange = $retData[0]['is_exchange'];
                $now_point = $retData[0]['point'];
                $u_key = $retData[0]['u_key'];


                $af_point = 0;
                $ch_bonus = 0;
                $p_a_comment = '';
                $ch_point = 0;
                $p_ac_code = 0;
                $n_is_reg_first_charge = $retData[0]['reg_first_charge'];
                $n_is_charge_first_per = $retData[0]['charge_first_per'];
                $n_is_charge_event = false;

                if ($n_event_charge_status == 'ON' && date("Y-m-d " . $n_event_charge_start) <= $currentDate && date("Y-m-d " . $n_event_charge_end) >= $currentDate &&
                        $chargeEventData[$u_level - 1]['bonus'] > 0) { // 돌발첫충
                    $ch_point = ($chargeEventData[$u_level - 1]['bonus'] * $set_money) / 100;
                    if ($chargeEventData[$u_level - 1]['max_bonus'] < $ch_point) {
                        $ch_point = $chargeEventData[$u_level - 1]['max_bonus'];
                    }
                    $p_a_comment = '포인트 돌발충전 : ' . 'charge_event_per';
                    $str_set_type = 'charge_event_per';
                    $n_is_charge_event = true;
                } else {
                    if ('0' == $n_is_reg_first_charge) { // 가입 첫충전 
                        $ch_point = ($n_reg_first_charge * $set_money) / 100;
                        if ($db_config[$u_level]['charge_max_money'] < $ch_point) {
                            $ch_point = $db_config[$u_level]['charge_max_money'];
                        }
                        $p_a_comment = '포인트 충전 : ' . 'reg_first_charge';
                        $str_set_type = 'reg_first_charge';
                    } else if ('0' == $n_is_charge_first_per) { // 매일 첫 충전
                        $ch_point = ($db_config[$u_level]['charge_first_per'] * $set_money) / 100;
                        if ($db_config[$u_level]['charge_max_money'] < $ch_point) {
                            $ch_point = $db_config[$u_level]['charge_max_money'];
                        }
                        $p_a_comment = '포인트 충전 : ' . 'charge_first_per';
                        $str_set_type = 'charge_first_per';
                    } else if (0 == $is_exchange) {
                        $ch_point = ($db_config[$u_level]['charge_per'] * $set_money) / 100;
                        if ($db_config[$u_level]['charge_money'] < $ch_point) {
                            $ch_point = $db_config[$u_level]['charge_money'];
                        }
                        $p_a_comment = '포인트 충전 : ' . 'charge_per';
                        $str_set_type = 'charge_per';
                    }
                }

                $p_ac_code = 10;
                $af_point = $ch_point + $now_point;
                $af_cash = $now_cash + $set_money;

                // 가입첫충이거나 이벤트 충전이다.
                if (0 == $n_is_reg_first_charge || true == $n_is_charge_event) {
                    $sql = "update member set reg_first_charge = 1, charge_first_per = 1, money = money + ?, point = point + ? where idx = ? ";
                } else if (0 == $n_is_charge_first_per) {
                    $sql = "update member set charge_first_per = 1,money = money + ?, point = point + ? where idx = ? ";
                } else {
                    $sql = "update member set money = money + ?, point = point + ? where idx = ? ";
                }

                $memberMCHModel->db->query($sql,[$set_money,$ch_point,$m_idx]);
                UserPayBack::AddCharge($m_idx,$set_money,$memberMCHModel);
                $execute = new DayChargeEvent($this->logger);
                $execute->AddCharge($m_idx,1,$set_money);    
                // 입금성공한 금액이 최대 입금금액 보다 많으면 데이터를 갱신해준다.
                $sql_tot = "SELECT max_charge FROM total_member_cash WHERE member_idx = ?";
                $max_charge_arr = $memberMCHModel->db->query($sql_tot,[$m_idx])->getResultArray();
                if (true === isset($max_charge_arr) && 0 < count($max_charge_arr)) {
                    $max_charge = $max_charge_arr[0]['max_charge'];
                    if ($max_charge < $set_money) {
                        $sql_up_tot = "UPDATE total_member_cash set bf_max_charge = ?, max_charge = ? WHERE member_idx = ?";
                        $memberMCHModel->db->query($sql_up_tot,[$max_charge,$set_money,$m_idx]);
                    }
                }

                $sql = "update member_money_charge_history set bonus_point = ?,set_type = ?, status=3, update_dt=now(), result_money = ?, referenceId= ? where idx = ?";
                // 로그에는 두개합산값으로 저장한다.
                $set_money = $set_money + $ch_point;

                $memberMCHModel->db->query($sql,[$ch_point,$str_set_type,$af_cash,$deposit_get_idx,$charge_idx]);

                $sql = "update member_money_charge_sms set status = 3, update_dt = now() where idx = ?";
                $memberMCHModel->db->query($sql,[$deposit_get_idx]);

                $this->gmPt->giveGMoneyCharge($memberMCHModel,$u_key,$m_idx,$set_money,$now_cash,$af_cash
                ,$ch_point,$now_point,$af_point,$p_a_comment,$this->logger);
                               
                $consumeList[] = $deposit_get_idx;
            }
            $memberMCHModel->db->transComplete();

            // 충전 완료 처리
            $depositList = $payment->consume(1, implode(',', $consumeList));
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $logger->error('virtual_auto_charge_money_renew [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: virtual_auto_charge_money_renew query : ' . $memberMCHModel->getLastQuery());
        } 
    }

    // 일주일동안 충전내용이 없으면 레벨1로 한다.
    public function changelevel() {
        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $sql = "SELECT m.level, mc.member_idx, mc.status, max(mc.create_dt) as last_create_dt, mc.money, mc.result_money "
                    . "FROM member_money_charge_history as mc join member as m on mc.member_idx = m.idx "
                    . "where m.u_business = 1 and mc.status = 3 and (m.level > 1 and m.level <> 9) group by mc.member_idx;";
            $result_list = $memberMCHModel->db->query($sql)->getResultArray();

            //$memberMCHModel->db->transStart();
            $changeUsers = array();
            $currentDate = date("Y-m-d H:i:s");
            foreach ($result_list as $key => $value) {
                $last_create_dt = date("Y-m-d H:i:s", strtotime($value['last_create_dt'] . "+" . 7 . " days"));
                if ($last_create_dt < $currentDate) {
                    $changeUsers[] = $value['member_idx'];
                }
            }

            // 해당되는 유저들이 있으면 1레벨로 변경한다.
            if (count($changeUsers) > 0) {
                $szUsers = implode(',', $changeUsers);
                $sql = "update member set level = 1 where idx in ($szUsers)";
                $memberMCHModel->db->query($sql);

                // 로그를 남긴다.
                $admin_id = '';
                $a_comment = '미입금 레벨변경 : ' . $szUsers;
                $sql = "insert into  t_log_cash ";
                $sql .= " (member_idx, ac_code, ac_idx, coment) ";
                $sql .= " values(0, 304, 0, ?);";
                $memberMCHModel->db->query($sql,[$a_comment]);
            }

            //$memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            //$memberMCHModel->db->transRollback();
        }
    }
    
    // paykiwoom request, 20220930
    public function chargePayKiwoomRequest() {

        if (false == session()->has('member_idx')) {
            return $this->fail('세션이 종료되었습니다.');
        }

        $memberIdx = session()->get('member_idx');

        $chargeMoney = isset($_POST['money']) ? $_POST['money'] : NULL;
        $charge_point_yn = isset($_POST['charge_point_yn']) ? $_POST['charge_point_yn'] : 0;
        //$depositName = isset($_POST['name']) ? $_POST['name'] : NULL;
        $uKey = md5($memberIdx . strtotime('Now'));
        //$account_number = isset($_POST['account_number']) ? $_POST['account_number'] : 0;

        $memberModel = new MemberModel();

        $sql = "SELECT set_type, set_type_val,u_level FROM t_game_config WHERE set_type = 'service_coin_charge' ";
        $result_game_config = $memberModel->db->query($sql)->getResult();
        if ('Y' == $result_game_config[0]->set_type_val) {
            return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
        }

        /*if (session()->get('level') > $result_game_config[0]->u_level) {
            return $this->fail('충전 가능 레벨이 아닙니다.');
        }*/

        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == null || $uKey == null) {

            return $this->fail('조회되는 유저 또는 uKey 가 없습니다.');
        }

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($findMember->getStatus() == 11) {
            return $this->fail('관리자 승인이 필요합니다.');
        }

        if ($findMember->getUBusiness() != 1) {
            return $this->fail('총판은 충전신청을 할 수 없습니다.');
        }
        
        $AccountBank = $findMember->getAccountBank();
        $AccountName = $findMember->getAccountName();
        $AccountNumber = $findMember->getAccountNumber();
        $member_level = $findMember->getLevel();
        
        //$this->initMemberData(session(), session()->get('member_idx'));
        //$this->logger->debug('MemberMoneyChargeHistoryController chargeCoinRequest depositName : ' . session()->get('account_name'));

        try {
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $sql = "SELECT idx FROM member_money_charge_history WHERE member_idx = ? AND status in (1,2)";
            $resultList = $memberMCHModel->db->query($sql, [$memberIdx])->getResult();
            if (0 < count($resultList)) {
                return $this->fail('이전 요청 내역이 처리되지 않았으며,관리자에게 문의바랍니다.');
            }
            
            $memberMCHModel->db->transStart();
            
            // paykiwon api call
            $agencyurl = config(App::class)->agencyurl;
            $paykiwoom = new Paykiwoom($this->logger);
            $result = $paykiwoom->checkoutRequest($agencyurl, $findMember->getId(), $findMember->getCoinPassword(), $chargeMoney, $AccountName, $AccountBank, $AccountNumber, $uKey);
            if('error' == $result->type){
                $this->logger->error('MemberMoneyChargeHistoryController::chargeCoinRequest error : ' . $result->message);
                $memberMCHModel->db->transRollback();
                $response = [
                    'result_code' => 400,
                    'messages' => $result->message,
                    'data' => []
                ];
                return $this->respond($response, 200);
            }
            $redirectURI = $result->redirectURI;
            //print_r($result);
            
            // db insert
            $bank_id = 1000; // coin bank_id
            $memberMCHModel->chargeCoinRequest($AccountName, $chargeMoney, $findMember->getMoney(), $uKey, $bank_id, $charge_point_yn);
            $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
            $response = [
                'result_code' => 400,
                'messages' => '충전 신청 실패',
                'messages_detail' => $e,
                'data' => []
            ];
            return $this->respond($response, 200);
        }

        $response = [
            'result_code' => 200,
            'messages' => '충전 신청 성공',
            'data' => ['redirectURI'=>$redirectURI]
        ];
        return $this->respond($response, 200);
    }

}
