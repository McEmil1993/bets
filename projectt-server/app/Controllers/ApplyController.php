<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyChargeHistoryModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Models\TGameConfigModel;

use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;

class ApplyController extends BaseController {

    use ResponseTrait;

    public function index() {
        $member_idx = session()->get('member_idx');

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $chkMobile = CodeUtil::rtn_mobile_chk();

        if (false == session()->has('member_idx') || !isset($member_idx)) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        } 
        
        $memberModel = new MemberModel();
        //$findMember = $memberModel->getMemberWhereIdx($member_idx);
        //session()->set('level', $findMember->getLevel());
        //$member_level = session()->get('level');
       
        if (0 < session()->get('tm_unread_cnt')) {

            // $url = base_url("/$viewRoot/betting_history?menu=d");
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $menu = isset($_GET['menu']) && !empty($_GET['menu']) ? $_GET['menu'] : 'c'; // c 이면 충전 e면 환전 

        if (!CodeUtil::only_number($page)){
             die();
        }

       
        $this->initMemberData(session(), $member_idx);

        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $TGCModel = new TGameConfigModel();
        $memberModel = new MemberModel();

        //멤버정보
        $member = $memberModel->getMemberWhereIdx($member_idx);
        
        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            echo "<script>
            alert('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
            window.history.back();
            </script>";
            return;
        }

        if ($member->getStatus() == 11) {
            echo "<script>
            alert('가입승인 이 후 이용이 가능합니다.');
            window.history.back();
            </script>";
            return;
        }


        $config = $TGCModel->getMemberConfigLevel($member->getLevel());
        foreach ($config as $key => $item) {
            if ($item['set_type'] == 'reg_first_charge') {
                $reg_first_charge = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_first_per') {
                $charge_first_per = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_max_money') {
                $charge_max_money = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_per') {
                $charge_per = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_money') {
                $charge_money = $item['set_type_val'];
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


        
        



        // 레벨에 맞는 이벤트 충전정보
        $sql = "SELECT level, bonus, max_bonus FROM charge_event where level = ".$member->getLevel();
        $chargeEventData = $memberMCHModel->db->query($sql)->getResultArray()[0];

        // 매충, 이벤트 중 포인트가 높은쪽으로 보여준다.
        $currentDate = date("Y-m-d H:i:s");
        
        $unexpectedEvent = 0;
        if ($event_charge_status == 'ON' && date("Y-m-d " . $event_charge_start) <= $currentDate && date("Y-m-d " . $event_charge_end) >= $currentDate) { // 돌발첫충
            $charge_per = $chargeEventData['bonus'];
            $charge_money = $chargeEventData['max_bonus'];
            $unexpectedEvent = 1;
        }

        $sql_count = "select member_idx from member_money_charge_history where member_idx = ? and status != 11";
        $chargeListCount = $memberMCHModel->db->query(
                        $sql_count,[$member_idx]
                )->getResultArray();
        $chargeAllCount = count($chargeListCount);

        $start_page = ($page - 1) * 10;

        $sql = "select * from member_money_charge_history where member_idx = ? and status != 11 order by create_dt desc limit ?,10 ";

        $chargeList = $memberMCHModel->db->query(
                        $sql, [$member_idx,$start_page]
                )->getResultArray();

        $memberMEHModel = new MemberMoneyExchangeHistoryModel();
        $sql_count = "select member_idx from member_money_exchange_history where member_idx = ? and status != 11";
        $exchangeListCount = $memberMEHModel->db->query(
                        $sql_count,[$member_idx]
                )->getResultArray();
        $exchangeAllCount = count($exchangeListCount);

        $exchangeList = $memberMEHModel
                ->where('member_idx', $member_idx)
                ->whereIn('status', [1, 2, 3])
                ->orderBy('create_dt', 'desc')
                ->limit(10, ($page - 1) * 10)
                ->find();

        // 코인충전
        $sql_count = "select member_idx, create_dt, money, status from member_money_charge_history where member_idx = ? and bank_id = 1000 order by create_dt desc limit ?,10";
        $coinChargeList = $memberMCHModel->db->query(
                        $sql_count, [$member_idx,$start_page]
                )->getResultArray();
        $coinChargeAllCount = count($coinChargeList);

        // 레벨별 충전방식
        $sql_count = "select charge_type, name from charge_type where level = ".$member->getLevel();
        $result = $memberMCHModel->db->query(
                        $sql_count
                )->getResultArray();
        $chargeType = $result[0]['charge_type'];
        $chargeName = $result[0]['name'];

        $name = session()->get('account_name');
        $sub_name1 = "";
        $sub_name2 = "";
        $name_len = mb_strlen($name);

        if (2 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $name = $sub_name1 . "*";
        } else if (3 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $sub_name2 = iconv_substr($name, 2, 2, "utf-8");
            $name = $sub_name1 . "*" . $sub_name2;
        } else if (4 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $sub_name2 = iconv_substr($name, 3, 3, "utf-8");
            $name = $sub_name1 . "**" . $sub_name2;
        }
        
        $totalCnt = 0;
        if ('c' == $menu) {
            $totalCnt = $chargeAllCount;

            $totalCnt = $chargeAllCount;
            $res = $TGCModel->getSetType('service_charge');
            if ('Y' == $res && 9 != $member->getLevel()) {
                echo "<script>
                alert('충전하기 점검중입니다.');
                window.history.back();
                </script>";
                return;
            }
        } else if ('d' == $menu) {
            $totalCnt = $coinChargeAllCount;
        } else {
            $totalCnt = $exchangeAllCount;
        }

        if ($chargeType == 3) {
            $viewPage = "virtualAccount";
        } else if ($chargeType == 2) {
            $viewPage = "paykiwoom";
        } else {
            $viewPage = "apply";
        }
        		
        if ('K-Win' == config(App::class)->ServerName && 5568 == $member_idx) {
            $totalCnt = 0;
            $chargeList = [];
            $chargeAllCount = 0;
            $exchangeList = [];
            $exchangeAllCount = 0;
            $coinChargeList = [];
            $coinChargeAllCount = 0;
        }

        $display_select_bonus = 0;
        if(1 == $member->getRegFirstCharge() && 0 == $unexpectedEvent){
            $display_select_bonus = 1;
        }
        
        $sql_bonus = "select * from tb_static_bonus where flag = 'ON' order by idx asc";
        $bonus_infos = $memberMCHModel->db->query(
                        $sql_bonus
                )->getResultArray();
        
        $sql_charge_type = "select * from charge_type where level = ".$member->getLevel();
        $charge_types = $memberMCHModel->db->query(
                        $sql_charge_type
                )->getResultArray();

        return view("$viewRoot/$viewPage", [
            'chargeList' => $chargeList,
            'chargeAllCnt' => $chargeAllCount,
            'exchangeList' => $exchangeList,
            'exchangeAllCnt' => $exchangeAllCount,
            'coinChargeList' => $coinChargeList,
            'coinChargeAllCnt' => $coinChargeAllCount,
            'accountName_origin' => session()->get('account_name'),
            'accountName' => $name,
            'accountNumber' => session()->get('account_number'),
            'account_bank' => session()->get('account_bank'),
            'totalCnt' => $totalCnt,
            'page' => $page,
            'menu' => $menu,
            'reg_first_charge' => $reg_first_charge,
            'charge_first_per' => $charge_first_per,
            'charge_max_money' => $charge_max_money,
            'charge_per' => $charge_per,
            'charge_money' => $charge_money,
            'member' => $member,
            'chargeType' => $chargeType,
            'chargeName' => $chargeName,
            'bonus_infos' => $bonus_infos,
            'charge_types' => $charge_types,
            'display_select_bonus' => $display_select_bonus,
            'level' => $member->getLevel(),
            'is_charge_first_per' => $member->getChargeFirstPer(),
            'is_reg_first_charge' => $member->getRegFirstCharge(),
            'unexpectedEvent' => $unexpectedEvent
        ]);
    }

    public function index2() {
        $member_idx = session()->get('member_idx');
        $member_level = session()->get('level');

        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {
       
            $url = base_url("/web/note");
            echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
            return;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        if (!CodeUtil::only_number($page)){
             die();
        }
      
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/"));
        }
        $this->initMemberData(session(), $member_idx);

        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $TGCModel = new TGameConfigModel();
        $memberModel = new MemberModel();

        //멤버정보
        $member = $memberModel->getMemberWhereIdx($member_idx);

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            echo "<script>
            alert('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
            window.history.back();
            </script>";
            return;
        }

        if ($member->getStatus() == 11) {
            echo "<script>
            alert('가입승인 이 후 이용이 가능합니다.');
            window.history.back();
            </script>";
            return;
        }


        $config = $TGCModel->getMemberConfigLevel($member_level);
        foreach ($config as $key => $item) {
            if ($item['set_type'] == 'reg_first_charge') {
                $reg_first_charge = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_first_per') {
                $charge_first_per = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_max_money') {
                $charge_max_money = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_per') {
                $charge_per = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_money') {
                $charge_money = $item['set_type_val'];
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

        // 레벨에 맞는 이벤트 충전정보
        $sql = "SELECT level, bonus, max_bonus FROM charge_event where level = ?";
        $chargeEventData = $memberMCHModel->db->query($sql,[$member_level])->getResultArray()[0];

        // 매충, 이벤트 중 포인트가 높은쪽으로 보여준다.
        $currentDate = date("Y-m-d H:i:s");
        if ($event_charge_status == 'ON' && date("Y-m-d " . $event_charge_start) <= $currentDate && date("Y-m-d " . $event_charge_end) >= $currentDate) { // 돌발첫충
            $charge_per = $chargeEventData['bonus'];
            $charge_money = $chargeEventData['max_bonus'];
        }

        $sql_count = "select member_idx from member_money_charge_history where member_idx = ? and status != 11";
        $chargeListCount = $memberMCHModel->db->query(
                        $sql_count,[$member_idx]
                )->getResultArray();
        $chargeAllCount = count($chargeListCount);

        $start_page = ($page - 1) * 10;

        $sql = "select * from member_money_charge_history where member_idx = ? and status != 11 order by create_dt desc limit ?,10 ";

        $chargeList = $memberMCHModel->db->query(
                        $sql, [$member_idx,$start_page]
                )->getResultArray();

        $memberMEHModel = new MemberMoneyExchangeHistoryModel();
        $sql_count = "select member_idx from member_money_exchange_history where member_idx = ? and status != 11";
        $exchangeListCount = $memberMEHModel->db->query(
                        $sql_count,[$member_idx]
                )->getResultArray();
        $exchangeAllCount = count($exchangeListCount);

        $exchangeList = $memberMEHModel
                ->where('member_idx', $member_idx)
                ->whereIn('status', [1, 2, 3])
                ->orderBy('create_dt', 'desc')
                ->limit(10, ($page - 1) * 10)
                ->find();

        // 코인충전
        $sql_count = "select member_idx, create_dt, money, status from member_money_charge_history where member_idx = ? and bank_id = 1000 order by create_dt desc limit ?,10";
        $coinChargeList = $memberMCHModel->db->query(
                        $sql_count, [$member_idx,$start_page]
                )->getResultArray();
        $coinChargeAllCount = count($coinChargeList);

        // 레벨별 충전방식
        $sql_count = "select charge_type, name from charge_type where level = ?";
        $result = $memberMCHModel->db->query(
                        $sql_count,[$member_level]
                )->getResultArray();
        $chargeType = $result[0]['charge_type'];
        $chargeName = $result[0]['name'];

        $name = session()->get('account_name');
        $sub_name1 = "";
        $sub_name2 = "";
        $name_len = mb_strlen($name);

        if (2 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $name = $sub_name1 . "*";
        } else if (3 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $sub_name2 = iconv_substr($name, 2, 2, "utf-8");
            $name = $sub_name1 . "*" . $sub_name2;
        } else if (4 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $sub_name2 = iconv_substr($name, 3, 3, "utf-8");
            $name = $sub_name1 . "**" . $sub_name2;
        }

        $totalCnt = 0;
        if ('c' == $menu) {
            $totalCnt = $chargeAllCount;
        } else if ('d' == $menu) {
            $totalCnt = $coinChargeAllCount;
        } else {
            $totalCnt = $exchangeAllCount;
        }


        if ('K-Win' == config(App::class)->ServerName && 5568 == $member_idx) {
            $totalCnt = 0;
            $chargeList = [];
            $chargeAllCount = 0;
            $exchangeList = [];
            $exchangeAllCount = 0;
            $coinChargeList = [];
            $coinChargeAllCount = 0;
        }


        return view("$viewRoot/apply2", [
            'chargeList' => $chargeList,
            'chargeAllCnt' => $chargeAllCount,
            'exchangeList' => $exchangeList,
            'exchangeAllCnt' => $exchangeAllCount,
            'coinChargeList' => $coinChargeList,
            'coinChargeAllCnt' => $coinChargeAllCount,
            'accountName' => $name,
            'accountNumber' => session()->get('account_number'),
            'totalCnt' => $totalCnt,
            'page' => $page,
            'menu' => $menu,
            'reg_first_charge' => $reg_first_charge,
            'charge_first_per' => $charge_first_per,
            'charge_max_money' => $charge_max_money,
            'charge_per' => $charge_per,
            'charge_money' => $charge_money,
            'member' => $member,
            'chargeType' => $chargeType,
            'chargeName' => $chargeName
        ]);
    }

    public function deleteHistory(){
        $memberModel = new MemberModel();

        $params[] = $_GET['delId'];
        $trans_type = $_GET['trans_type'];
        $sql = "";
        if($trans_type == 'CH'){
            $sql = "UPDATE member_money_charge_history SET delete_dt = '".date('Y-m-d H:i:s')."'  WHERE idx =  ".$_GET['delId'];
        }else{
            $sql = "UPDATE member_money_exchange_history SET delete_dt = '".date('Y-m-d H:i:s')."'  WHERE idx = ".$_GET['delId'];
        }
        $memberModel->db->query($sql, [])->getResult();

        $ret['message'] = 'Success';

        return json_encode($ret);
    }

    // 환전하기
    public function exchange() {

        $member_idx = session()->get('member_idx');

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $chkMobile = CodeUtil::rtn_mobile_chk();

        if (false == session()->has('member_idx') || !isset($member_idx)) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        } 

        $TGCModel = new TGameConfigModel();
        $res = $TGCModel->getSetType('service_exchange');
        if ('Y' == $res && 9 != session()->get('level')) {
            echo "<script>
            alert('환전하기 점검중입니다.');
            window.history.back();
            </script>";
            return;
        }

        // if (false == session()->has('member_idx')) {
        //     return $this->fail('로그인 후 이용해주세요.');
        // }

         // 베팅롤링정보
         $memberModel = new MemberModel();
         $sql = "SELECT sports_bet_s_money, sports_bet_d_money, real_bet_s_money, real_bet_d_money, mini_bet_money"
                 . ", casino_bet_money, slot_bet_money, esports_bet_money, hash_bet_money, money as charge_money FROM member_money_charge_history "
                 . "where member_idx = ? and status = 3 order by idx desc limit 1";

         $result = $memberModel->db->query($sql, [$member_idx])->getResultArray();
         
         $lastChargeInfo = array(
             'charge_money' => 0,
             'sports_bet_s_money' => 0,
             'sports_bet_d_money' => 0,
             'real_bet_s_money' => 0,
             'real_bet_d_money' => 0,
             'mini_bet_money' => 0,
             'casino_bet_money' => 0,
             'slot_bet_money' => 0,
             'esports_bet_money' => 0,
             'hash_bet_money' => 0,
             'sports_s_per' => 0,
             'sports_d_per' => 0,
             'real_s_per' => 0,
             'real_d_per' => 0,
             'mini_bet_money' => 0,
             'casino_bet_per' => 0,
             'slo_bet_per' => 0,
             'esports_bet_per' => 0,
             'hash_bet_per' => 0
         );


         if(0 < count($result)){
             $row = $result[0];
             // 배팅 비율
             //$sports_s_per = $sports_d_per = $real_s_per = $real_d_per = $mini_per = $casino_bet_per = $slo_bet_per = $esports_bet_per = $hash_bet_per = 0;
             $lastChargeInfo['charge_money'] = $row['charge_money'];
             $lastChargeInfo['sports_bet_s_money'] = $row['sports_bet_s_money'];
             $lastChargeInfo['sports_bet_d_money'] = $row['sports_bet_d_money'];
             $lastChargeInfo['real_bet_s_money'] = $row['real_bet_s_money'];
             $lastChargeInfo['real_bet_d_money'] = $row['real_bet_d_money'];
             $lastChargeInfo['mini_bet_money'] = $row['mini_bet_money'];
             $lastChargeInfo['casino_bet_money'] = $row['casino_bet_money'];
             $lastChargeInfo['slot_bet_money'] = $row['slot_bet_money'];
             $lastChargeInfo['esports_bet_money'] = $row['esports_bet_money'];
             $lastChargeInfo['hash_bet_money'] = $row['hash_bet_money'];
             
             if($row['sports_bet_s_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['sports_s_per'] = round($row['sports_bet_s_money']/$row['charge_money']*100);
             
             if($row['sports_bet_d_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['sports_d_per'] = round($row['sports_bet_d_money']/$row['charge_money']*100);
             
             if($row['real_bet_s_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['real_s_per'] = round($row['real_bet_s_money']/$row['charge_money']*100);
             
             if($row['real_bet_d_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['real_d_per'] = round($row['real_bet_d_money']/$row['charge_money']*100);
             
             if($row['mini_bet_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['mini_per'] = round($row['mini_bet_money']/$row['charge_money']*100);
             
             if($row['casino_bet_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['casino_bet_per'] = round($row['casino_bet_money']/$row['charge_money']*100);
             
             if($row['slot_bet_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['slo_bet_per'] = round($row['slot_bet_money']/$row['charge_money']*100);
             
             if($row['esports_bet_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['esports_bet_per'] = round($row['esports_bet_money']/$row['charge_money']*100);
             
             if($row['hash_bet_money'] > 0 && $row['charge_money'] > 0)
                 $lastChargeInfo['hash_bet_per'] = round($row['hash_bet_money']/$row['charge_money']*100);
         }

        return view("$viewRoot/exchange", [
            'lastChargeInfo' => $lastChargeInfo
        ]);
    }

    public function chargeExchangeHistory() {
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        return view("$viewRoot/sub15", [
        ]);
    }

    // 충/환전내역
    public function renew_index() {
        $member_idx = session()->get('member_idx');
        $member_level = session()->get('level');

        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {

            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $type = isset($_GET['type']) ? $_GET['type'] : 0; //  0 : 전체 1 :충전 2 환전
        $menu = isset($_GET['menu']) && !empty($_GET['menu']) ? $_GET['menu'] : 'c'; // c 이면 충전 e면 환전 

        if (!is_int((int)$page) || !is_int((int)$type) || $type < 0 || 2 < $type){
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** ApplyController::renew_index: ==> " );
             die();
        }
        
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/"));
        }
        $this->initMemberData(session(), $member_idx);

        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $TGCModel = new TGameConfigModel();
        $memberModel = new MemberModel();

        //멤버정보
        $member = $memberModel->getMemberWhereIdx($member_idx);

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            echo "<script>
            alert('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
            window.history.back();
            </script>";
            return;
        }

        if ($member->getStatus() == 11) {
            echo "<script>
            alert('가입승인 이 후 이용이 가능합니다.');
            window.history.back();
            </script>";
            return;
        }


        $config = $TGCModel->getMemberConfigLevel($member_level);
        foreach ($config as $key => $item) {
            if ($item['set_type'] == 'reg_first_charge') {
                $reg_first_charge = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_first_per') {
                $charge_first_per = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_max_money') {
                $charge_max_money = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_per') {
                $charge_per = $item['set_type_val'];
            }
            if ($item['set_type'] == 'charge_money') {
                $charge_money = $item['set_type_val'];
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

        // 레벨에 맞는 이벤트 충전정보
        $sql = "SELECT level, bonus, max_bonus FROM charge_event where level = ?";
        $chargeEventData = $memberMCHModel->db->query($sql,[$member_level])->getResultArray()[0];

        // 매충, 이벤트 중 포인트가 높은쪽으로 보여준다.
        $currentDate = date("Y-m-d H:i:s");
        if ($event_charge_status == 'ON' && date("Y-m-d " . $event_charge_start) <= $currentDate && date("Y-m-d " . $event_charge_end) >= $currentDate) { // 돌발첫충
            $charge_per = $chargeEventData['bonus'];
            $charge_money = $chargeEventData['max_bonus'];
        }

        $param = [];
        if (0 == $type) { // 전체
            $sql_count = "select member_idx from member_money_charge_history where member_idx = ? and status != 11 and  delete_dt IS NULL 
                            UNION ALL
                          select member_idx from member_money_exchange_history where member_idx = ? and status != 11 and  delete_dt IS NULL 
                            ";
            $param[] = $member_idx;
            $param[] = $member_idx;
        } else if (1 == $type) {// 충전
            $sql_count = "select member_idx from member_money_charge_history where member_idx = ? and status != 11";
            $param[] = $member_idx;
        } else { // 환전 
            $sql_count = "select member_idx from member_money_exchange_history where member_idx = ? and status != 11";
            $param[] = $member_idx;
        }

        $dataListCount = $memberMCHModel->db->query(
                        $sql_count, $param
                )->getResultArray();
        $dataAllCount = count($dataListCount);

        $start_page = ($page - 1) * 10;
        $param = [];
        if (0 == $type) { // 전체
            $sql = "select 'CH' as etype,deposit_name,bank_id,bank_name,account_number
                            ,account_name,money,result_money,bonus_money,bonus_point
                            ,set_type,status,comment,create_dt,update_dt,idx
                            from member_money_charge_history where member_idx = ? and status and  delete_dt IS NULL 
                            UNION ALL
                          select 'EX' as etype ,'' as deposit_name,0 as bank_id,'' as bank_name,'' as account_number
                             ,'' as account_name,money,result_money,0 as bonus_money,0 as bonus_point
                            ,set_type,status,comment,create_dt,update_dt,idx
                             from member_money_exchange_history where member_idx = ? and status and  delete_dt IS NULL 
                            order by create_dt desc limit ?, 10";
            $param[] = $member_idx;
            $param[] = $member_idx;

            $param[] = $start_page;
        } else if (1 == $type) {// 충전
            $sql = "select 'CH' as etype, member_money_charge_history.*    from member_money_charge_history  where member_idx = ? and status and  delete_dt IS NULL  order by create_dt desc limit ?,10";
            $param[] = $member_idx;
            $param[] = $start_page;
        } else { // 환전 
            $sql = "select 'EX' as etype, member_money_exchange_history.*  from member_money_exchange_history where member_idx = ? and status and  delete_dt IS NULL  order by create_dt desc limit ?,10";
            $param[] = $member_idx;
            $param[] = $start_page;
        }

        $dataList = $memberMCHModel->db->query($sql, $param)->getResultArray();

        // 레벨별 충전방식
        $sql_count = "select charge_type, name from charge_type where level = ?";
        $result = $memberMCHModel->db->query($sql_count,[$member_level])->getResultArray();

        $chargeType = $result[0]['charge_type'];
        $chargeName = $result[0]['name'];

        $name = session()->get('account_name');
        $sub_name1 = "";
        $sub_name2 = "";
        $name_len = mb_strlen($name);

        if (2 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $name = $sub_name1 . "*";
        } else if (3 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $sub_name2 = iconv_substr($name, 2, 2, "utf-8");
            $name = $sub_name1 . "*" . $sub_name2;
        } else if (4 == $name_len) {
            $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
            $sub_name2 = iconv_substr($name, 3, 3, "utf-8");
            $name = $sub_name1 . "**" . $sub_name2;
        }

        return view("$viewRoot/charge_exchange_history", [
            'dataList' => $dataList,
            'dataAllCount' => $dataAllCount,
            'accountName' => $name,
            'accountNumber' => session()->get('account_number'),
            'page' => $page,
            'menu' => $menu,
            'type' => $type,
            'reg_first_charge' => $reg_first_charge,
            'charge_first_per' => $charge_first_per,
            'charge_max_money' => $charge_max_money,
            'charge_per' => $charge_per,
            'charge_money' => $charge_money,
            'member' => $member,
            'chargeType' => $chargeType,
            'chargeName' => $chargeName
        ]);
    }

}
