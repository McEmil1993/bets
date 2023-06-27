<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\TLogCashModel;
use App\Models\TGameConfigModel;
use App\Game\Holdem;
use App\Util\CodeUtil;
use App\Util\Calculate;
use CodeIgniter\API\ResponseTrait;
use App\Util\UserPayBack;

class HoldemController extends BaseController {

    use ResponseTrait;

    public function index() {

        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        $mobileType = "PC" == $chkMobile ? 'web' : 'mobile';

        if (false == session()->has('member_idx')) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        $member_idx = session()->get('member_idx');

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);

        if ($member == null) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('회원가입 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        if (false !== strpos($member->getId(), 'test') || false !== strpos($member->getNickName(), 'test')) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('test가 들어간 계정은 사용할수 없습니다.');
                window.location.href='$url';
        	</script>";
            return;
        }

        $str_sql_config = "SELECT set_type_val FROM t_game_config WHERE set_type = 'service_holdem' ";
        $arr_config_result = $memberModel->db->query($str_sql_config)->getResultArray();
        if ('Y' == $arr_config_result[0]['set_type_val'] && 9 != $member->getLevel()) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('점검중입니다.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        // 유저개인 점검
        $game_type_sql = "SELECT game_type, status FROM member_game_type where member_idx = ? and game_type = ?";
        $arr_member_config_result = $memberModel->db->query($game_type_sql, [$member_idx, LGTB_HOLDEM])->getResultArray();
        if ('OFF' == $arr_member_config_result[0]['status']) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('점검중입니다.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        $holdem_admin_id = config(App::class)->holdem_admin_id;
        $holdem_admin_pass = config(App::class)->holdem_admin_pass;
        $holdem_api_url = config(App::class)->holdem_api_url;
        $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);

        if (!$holdem) {
            $this->logger->error('------------- fail new holdem ----------------------------');
            $this->logger->error('------------- fail new holdem ----------------------------');
            $this->logger->error('------------- fail new holdem ----------------------------');
            die();
        }

        // Token Issuance 
        list($retCode, $access_token) = $this->getHoldemToken();
        if (0 == $retCode) {
            // 없으면 생성
            list($retCode, $access_token) = $this->setHoldemToken();
            if (0 == $retCode) {
                $this->logger->error('------------- fail index setHoldemToken() ----------------------------');
                return view("$viewRoot/holdem", [
                    'error' => 'fail token holdem'
                ]);
            }
        }

        $holdem->setAuthorization($access_token);

        if ('N' == $member->getIsHoldemRegister()) {
            // registration of membership
            $ret_register = $holdem->register($member->getId(), $member->getPassword(), $member->getNickName());
            $this->logger->error('------------- result register : ' . json_encode($ret_register));
            if (!isset($ret_register)) {
                $this->logger->error('------------- fail register holdem ----------------------------');
                return view("$viewRoot/holdem", [
                    'error' => 'fail token holdum'
                ]);
            }

            if ('error' === $ret_register->result) {
                $this->logger->error('------------- fail register holdem  detail : ' . $ret_register->detail);
                return view("$viewRoot/holdem", [
                    'error' => $ret_register->detail
                ]);
            }

            // Update the registration information.
            $sql = "UPDATE member SET is_holdem_register = 'Y' WHERE idx = ? ";
            $this->logger->error('------------- sucess is_holdem_register : ' . $sql);
            $this->logger->error('------------- sucess is_holdem_register : ' . $member->getIdx());
            $memberModel->db->query($sql, [$member->getIdx()]);
            $member->setIsHoldemRegister('Y');
        }

        // 시작한 세션이 있으면 정리
        if ('Y' === $member->getIsHoldemStart()) {
            $end_session_flag = true;
            $ret_end_session = $holdem->end_session($member->getId());
            if (!isset($ret_end_session)) {
                $this->logger->error('------------- fail end_session holdem ----------------------------');
                $end_session_flag = false;
            }

            if (true === isset($ret_end_session->detail)) {
                $this->logger->error('------------- fail end_session holdem  detail : ' . $ret_end_session->detail);
                $end_session_flag = false;
            }

            //
            if ('success' != $ret_end_session->result) {
                $this->logger->error('------------- fail end_session holdum  not success');
                $end_session_flag = false;
            }

            if ($end_session_flag) {
                // Update User Retained Money.
                $sql = "UPDATE member SET is_holdem_start = 'N' WHERE idx = ?";
                $memberModel->db->query($sql, [$member->getIdx()]);

                // log add
                $coment = 'index - holdem end[' . $member->getMoney() . ']';
                $tLogCashModel = new TLogCashModel();
                $tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), HOLDEM_END, 0, $ret_end_session->credit, 0, $coment);
            }


            if (false == $end_session_flag) {
                list($retCode, $access_token) = $this->setHoldemToken();
                if (0 == $retCode) {
                    $this->logger->error('------------- fail index setHoldemToken() ----------------------------');
                    return view("$viewRoot/holdem", [
                        'error' => 'fail token holdem'
                    ]);
                }
                $holdem->setAuthorization($access_token);
            }

            //$this->logger->error('------------- index - call end_session holdem : ' . json_encode($ret_end_session));
        }


        $ret_start_session = $holdem->start_session($member->getId(), 0);
        if (!isset($ret_start_session)) {
            $this->logger->error('------------- fail start_session holdem ----------------------------');
            return view("$viewRoot/holdem", [
                'error' => 'fail start_session holdem'
            ]);
        }

        if (true === isset($ret_start_session->detail)) {
            
            list($retCode, $access_token) = $this->setHoldemToken();
            if (0 == $retCode) {
                $this->logger->error('------------- fail index setHoldemToken() ----------------------------');
                return view("$viewRoot/holdem", [
                    'error' => 'fail token holdem'
                ]);
            }
            
            $holdem->setAuthorization($access_token);
            
            $this->logger->error('------------- fail start_session holdem  detail : ' . $ret_start_session->detail . ' money : ' . $member->getMoney() . ' user id =>' . $member->getId());
            $ret_end_session = $holdem->end_session($member->getId());
            return view("$viewRoot/holdem", [
                'error' => $ret_start_session->detail
            ]);
        }

        if ('success' != $ret_start_session->result) {
            $this->logger->error('------------- fail ret_start_session holdum  not success : ');
            return view("$viewRoot/holdem", [
                'error' => 'fail ret_start_session holdem  not success'
            ]);
        }

        $this->logger->error('------------- success start_session holdem : ' . json_encode($ret_start_session));
        // 게임시작은 호출 api url을 만들어서 넘겨준다. 자바스크립트로 호출해야함.
        $ret_start_game = $holdem->start_game($ret_start_session->access_token);

        // 세션시작
        try {
            $memberModel->db->transStart();
            $sql = "UPDATE member SET is_holdem_start = 'Y', is_holdem_start_dt = now() WHERE idx = ? ";
            $memberModel->db->query($sql, [$member->getIdx()]);
            $member->setIsHoldemStart('Y');
            session()->set('access_token', $ret_token->access_token);
            $tLogCashModel = new TLogCashModel();
            $tLogCashModel->insertCashLog($ret_token->access_token, HOLDEM_START, 0, 0, $member->getMoney(), 'holdem start');

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->critical('end_session [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: end_session query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
            return view("$viewRoot/holdem", [
                'error' => 'fail db'
            ]);
        }

        return view("$viewRoot/holdem", [
            'error' => 'success',
            'access_token' => $ret_token->access_token,
            'token_type' => $ret_token->token_type,
            'ret_start_game' => $ret_start_game,
            'mobileType' => $mobileType
        ]);
    }

    public function end_session() {
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'mobile';

        //$this->logger->critical('------------- start end_session ----------------------------');
        if (false == session()->has('member_idx')) {
            return $this->fail('로그인 후 이용해주세요.');
        }

        $member_idx = session()->get('member_idx');

        //$access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);

        if ($member == null) {
            return $this->fail('회원가입 후 이용해주세요.');
        }

        if ('N' == $member->getIsHoldemRegister()) {
            return $this->fail('홀덤 게임 등록이 안되어있습니다.');
        }

        $holdem_admin_id = config(App::class)->holdem_admin_id;
        $holdem_admin_pass = config(App::class)->holdem_admin_pass;
        $holdem_api_url = config(App::class)->holdem_api_url;
        $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);

        if (!$holdem) {
            $this->logger->error('------------- fail new holdem ----------------------------');
            $this->logger->error('------------- fail new holdem ----------------------------');
            $this->logger->error('------------- fail new holdem ----------------------------');
            die();
        }

        // 관리자 토큰 셋팅
        //$holdem->setAuthorization(session()->get('access_token'));
        /* $ret_token = $holdem->token();
          if (!isset($ret_token)) {
          $this->logger->error('------------- fail token holdem ----------------------------');
          return view("$viewRoot/holdem", ['error' => 'fail token holdem']);
          }

          if (true === isset($ret_token->detail)) {
          $this->logger->error('------------- fail token holdem  detail : ' . $ret_token->detail);
          return view("$viewRoot/holdem", ['error' => $ret_token->detail->msg]);
          } */
        list($retCode, $access_token) = $this->getHoldemToken();
        if (0 == $retCode) {
            $this->logger->error('------------- fail end_session getHoldemToken() ----------------------------');
            return $this->fail('fail end_session getHoldemToken()');
        }
        $holdem->setAuthorization($access_token);

        $this->logger->error('------------- end_session holdem id : ' . $member->getId() . ' detail : ' . $ret_token->access_token);
        $ret_end_session = $holdem->end_session($member->getId());
        if (!isset($ret_end_session)) {
            $this->logger->error('------------- fail end_session holdem ----------------------------');
            return $this->fail('fail end_session holdem');
        }

        if (true === isset($ret_end_session->detail)) {
            $this->logger->error('------------- fail end_session holdem  detail : ' . $ret_end_session->detail);
            return $this->fail($ret_end_session->detail);
        }

        //
        if ('success' != $ret_end_session->result) {
            $this->logger->error('------------- fail end_session holdum  not success : ');
            return $this->fail('fail end_session holdem  not success');
        }

        try {
            $memberModel->db->transStart();

            // Update User Retained Money.
            $sql = "UPDATE member SET is_holdem_start = 'N' WHERE idx = ?";
            $memberModel->db->query($sql, [$member->getIdx()]);
            //$this->logger->error('end_holdem');
            $member = $memberModel->getMemberWhereIdx($member->getIdx());

            if ($member == null) {
                $memberModel->db->transRollback();
                return $this->fail('회원가입 후 이용해주세요.');
            }

            // log add
            $tLogCashModel = new TLogCashModel();
            $tLogCashModel->insertCashLog(session()->get('access_token'), HOLDEM_END, 0, $ret_end_session->credit, $member->getMoney() + $ret_end_session->credit, 'holdem end');

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->critical('end_session [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: end_session query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
            return $this->fail('디비처리 실패로 인한 배팅 실패');
        }

        //session()->set('credit', $ret_end_session->credit);
        //$this->logger->error('end_session success money : ' . $member->getMoney() . ' credit : ' . $ret_end_session->credit);
        $response = [
            'result_code' => 200,
            'money' => $member->getMoney()
        ];
        return $this->respond($response, 200);
    }

    // 홀덤 첫화면 진입
    public function holdem() {
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        return view("$viewRoot/holdem", [
        ]);
    }

    private function queryCredit($amount, $userName, $game_num, $member, $funcName) {
        //$this->logger->error(':::::::::::::::  call_back queryCredit : ' . json_encode($__getData));
        //$stack = $bet_data->stack;
        if ($member->getMoney() < $amount) {
            // return error
            $this->logger->error(':::::::::::::::  call_back queryCredit member->getMoney() < stack username: ' . $userName . ' amount : ' . $amount . ' money : ' . $member->getMoney());
            return false;
        }

        # 로그 추가 시작
        $a_comment = $funcName . ' holdem query credit game ==> ' . ' [' . $game_num . '] ';
        $tLogCashModel = new TLogCashModel();
        $tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), HOLDEM_QUERY_CREDIT, $game_num, -1 * $amount, $member->getMoney(), $a_comment);
        return true;
    }

    private function buyIn($stack, $userName, $game_num, $member, $memberModel, $funcName) {
        //$this->logger->error(':::::::::::::::  call_back buyIn : ' . json_encode($__getData));
        //$stack = $bet_data->stack;
        if ($member->getMoney() < $stack) {
            // return error
            $this->logger->error(':::::::::::::::  call_back buyIn member->getMoney() < stack username: ' . $userName . ' stack : ' . $stack . ' money : ' . $member->getMoney());
            return false;
        }

        $sql = "UPDATE member SET money = money - ? WHERE idx = ?";
        $memberModel->db->query($sql, [$stack, $member->getIdx()]);

        //$this->logger->error(':::::::::::::::  call_back BUY_IN sql ' . $sql . ' param : ' . json_encode([$stack, $member->getIdx()]));
        # 로그 추가 시작
        $a_comment = $funcName . ' holdem buy in game ==> ' . ' [' . $game_num . '] ';
        $tLogCashModel = new TLogCashModel();
        $tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), HOLDEM_BUY_IN, $game_num, -1 * $stack, $member->getMoney(), $a_comment);
        return true;
    }

    private function buyOut($stack, $game_num, $member, $memberModel, $funcName) {
        $sql = "UPDATE member SET money = money + ? WHERE idx = ?";
        $memberModel->db->query($sql, [$stack, $member->getIdx()]);

        $sql = "INSERT INTO tb_user_pay_back_info (member_idx, tot_bet_money) VALUES (?, ?) ON DUPLICATE KEY UPDATE tot_bet_money = tot_bet_money + ?";
        $memberModel->db->query($sql, [$member->getIdx(), $stack, $stack]);

        //$this->logger->error(':::::::::::::::  call_back buyOut sql ' . $sql . ' param : ' . json_encode([$stack, $member->getIdx()]));
        # 로그 추가 시작
        $a_comment = $funcName . ' holdem buy out game ==> ' . ' [' . $game_num . '] ';
        $tLogCashModel = new TLogCashModel();
        $tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), HOLDEM_BUY_OUT, $game_num, $stack, $member->getMoney(), $a_comment);
        $tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), HOLDEM_BUY_OUT, $game_num, $stack, $member->getMoney(), 'test');
    }

    private function roundEnd($before_stack, $after_stack, $betMoney, $winMoney, $game_num, $event, $member, $memberModel, $funcName, $json_data) {
        $fee = 0;
        if (0 < $winMoney) {
            $fee = floor(($winMoney * 0.052));
        }

        $bet_result_date = date('Y-m-d H:i:s');
        $add_bet_date = date("Y-m-d H:i:s", strtotime("-1 second"));
        $start_money = $member->getMoney() + $before_stack;
        $update_money = $after_stack;
        $query = "insert into HOLDEM_BET_HIST(REG_DTM, MBR_IDX, GAME_NUM, BET_MONEY,WIN_MONEY,STACK,FEE,EVENT,START_MONEY,FINAL_MONEY,CALL_BACK_DATA) values (now(),?,?,?,?,?,?,?,?,?,?)";
        $memberModel->db->query($query, [$member->getIdx(), $game_num, $betMoney, $winMoney, $after_stack, $fee, $event, $start_money, $member->getMoney() + $update_money, $json_data]);
        //$this->logger->error(':::::::::::::::  call_back ROUND_END sql ' . $sql . ' param : ' . json_encode([$update_money, $member->getIdx()]));
        # 로그 추가 시작
        $a_comment = $funcName . ' holdem start betting game_num ==> ' . ' [' . $game_num . '] ';
        $tLogCashModel = new TLogCashModel();
        //$tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), ADD_BET, $game_num, $betMoney * -1, $start_money, $a_comment);
        $tLogCashModel->insertCashLog_mem_idx_holdem('', $member->getIdx(), ADD_BET, $game_num, $betMoney * -1, $before_stack, $add_bet_date, $member->getMoney(), $a_comment);

        # 로그 추가 종료
        $a_comment = $funcName . ' holdem end betting game_num ==> ' . ' [' . $game_num . '] ';
        $updateMoney = $bet_result_start_money = 0;
        if ($winMoney > 0) {
            $a_comment .= " 적중";
            $bet_result_start_money = $before_stack - $betMoney;
            //$updateMoney = $winMoney - $betMoney - $fee;
            $updateMoney = $winMoney - $fee;
        } else {
            $a_comment .= " 낙첨";
            $bet_result_start_money = $after_stack;
        }
        //$tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), BET_RESULT, $game_num, $updateMoney, $start_money - $betMoney, $a_comment);
        $tLogCashModel->insertCashLog_mem_idx_holdem('', $member->getIdx(), BET_RESULT, $game_num, $updateMoney, $bet_result_start_money, $bet_result_date, $member->getMoney(), $a_comment);
        // round end log
        $a_comment = $funcName . ' round end game_num ==> ' . ' [' . $game_num . '] ';
        $tLogCashModel->insertCashLog_mem_idx('', $member->getIdx(), HOLDEM_ROUND_END, $game_num, $winMoney - $betMoney - $fee, $start_money - $betMoney, $a_comment);
        
        // exchange list log
        Calculate::updateChargeBetMoney($member->getIdx(), 'HOLDEM', $betMoney, $memberModel, $this->logger);
        UserPayBack::AddBetting($member->getIdx(),-$betMoney,$memberModel);       
    }

    // 콜백 처리
    /*
     * {"event",    "ROUND_START" },
      {"admin",   관리자아이디},
      {"username", 유저아이디},
      {"game_num", 게임회차},
      {"sb",       small blind},
      {"bb",       bing blind},
      {"report_url", callback url}

      {"event",    "ROUND_END"},
      {"admin",   관리자아이디},
      {"username", 유저아이디},
      {"game_num", 게임회차},
      {"sb",       small blind},
      {"bb",       bing blind},
      {"report_url", callback url}
      {"wager",      베팅금액},
      {"win",        반환금액},
      {"comm_card_1", community card 1},
      {"comm_card_2", community card 2},
      {"comm_card_3", community card 3},
      {"comm_card_4", community card 4},
      {"comm_card_5", community card 5},
      {"user_card_1", hand card 1},
      {"user_card_2", hand card 2},
      {"title",       족보}
     */
    public function call_back() {
        $__rawBody = file_get_contents("php://input"); // 본문을 불러옴
        $__getData = array(json_decode($__rawBody));

        $this->logger->error(':::::::::::::::  call_back BetInfo : ' . json_encode($__getData));

        $arr_bet_data = json_decode($__getData[0]);
        if (0 == count($arr_bet_data)) {
            $this->logger->error('::::::::  call_back not data!!');
            $response['messages'] = '데이터가 없다.';
            return $this->fail($response);
        }

        foreach ($arr_bet_data as $bet_data) {
            if ('ENTER_ROOM' == $bet_data->event || 'QUIT_ROOM' == $bet_data->event) {
                continue;
            }

            $userName = explode('_', $bet_data->username);
            $memberModel = new MemberModel();
            $member = $memberModel->getMemberWhereId($userName[1]);

            if ($member == null) {
                $this->logger->error(':::::::::::::::  call_back not user : ' . $userName[1]);
                $response['messages'] = '조회되는 유저가 없습니다.';
                return $this->fail($response);
            }

            // call back data log
            $sql = "INSERT INTO HOLDEM_CALL_BACK_DATA(MBR_IDX,MONEY,EVENT,DATA,CREATE_DT,FUNCTION) VALUES(?,?,?,?,NOW(),?)";
            $memberModel->db->query($sql, [$member->getIdx(), $member->getMoney(), $bet_data->event, json_encode($bet_data), __FUNCTION__]);

            try {

                $game_num = $bet_data->game_num;
                //$credit = $bet_data->credit;
                // end write bet history
                $memberModel->db->transStart();

                if ('QUERY_CREDIT' == $bet_data->event) {

                    // $this->logger->error(':::::::::::::::  call_back BUY_IN : ' . json_encode($__getData));
                    if (false == $this->queryCredit($bet_data->amount, $userName[1], $game_num, $member, __FUNCTION__)) {
                        $response['messages'] = '보유금액부족';
                        return $this->fail($response);
                    }
                } else if ('BUY_IN' == $bet_data->event) {

                    // $this->logger->error(':::::::::::::::  call_back BUY_IN : ' . json_encode($__getData));
                    if (false == $this->buyIn($bet_data->stack, $userName[1], $game_num, $member, $memberModel, __FUNCTION__)) {
                        $response['messages'] = '보유금액부족';
                        return $this->fail($response);
                    }
                } else if ('BUY_OUT' == $bet_data->event) {
                    $this->logger->error(':::::::::::::::  call_back BUY OUT : ' . json_encode($__getData));

                    $this->buyOut($bet_data->stack, $game_num, $member, $memberModel, __FUNCTION__);
                } else if ('ROUND_END' == $bet_data->event) {

                    $this->roundEnd($bet_data->before_stack, $bet_data->after_stack, $bet_data->wager, $bet_data->win, $game_num, $bet_data->event, $member, $memberModel, __FUNCTION__, json_encode($__getData));
                }

                $sql = "UPDATE member SET is_holdem_start_dt = now() WHERE idx = ?";
                $memberModel->db->query($sql, [$member->getIdx()]);

                $memberModel->db->transComplete();
            } catch (\mysqli_sql_exception $e) {
                $memberModel->db->transRollback();
                $this->logger->critical(':::::::::::::::  call_back error : ' . $e->getMessage());
                $this->logger->critical(':::::::::::::::  call_back query : ' . $memberModel->getLastQuery());
                $response['messages'] = '디비처리 실패로 인한 배팅 실패';
                return $this->fail($response);
            }
        }

        $response = [
            //'code' => 200,
            //'messages' => '성공',
            'result' => 'success'
        ];

        $this->logger->info(':::::::::::::::  call_back SUCCESS EVENT : ' . $bet_data->event . ' response ' . json_encode($response) . ' username =>' . $userName[1]);

        return $this->respond($response, 200);
    }

    // 콜백 url 등록
    public function update_host() {
        $holdem_admin_id = config(App::class)->holdem_admin_id;
        $holdem_admin_pass = config(App::class)->holdem_admin_pass;
        $holdem_api_url = config(App::class)->holdem_api_url;
        $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);

        if (!$holdem) {
            $this->logger->error('------------- fail update_host new holdem ----------------------------');
            $this->logger->error('------------- fail update_host new holdem ----------------------------');
            $this->logger->error('------------- fail update_host new holdem ----------------------------');
            $response['messages'] = 'fail update_host new holdem';
            return $this->fail($response);
        }

        // Token Issuance 
        /* $ret_token = $holdem->token();
          if (!isset($ret_token)) {
          $this->logger->error('------------- fail update_host token holdem ----------------------------');
          $response['messages'] = 'fail token holdem';
          return $this->fail($response);
          }

          if (true === isset($ret_token->detail)) {
          $this->logger->error('------------- fail update_host token holdem  detail : ' . $ret_token->detail);
          $response['messages'] = $ret_token->detail->msg;
          return $this->fail($response);
          }

          $access_token = $ret_token->access_token; */
        list($retCode, $access_token) = $this->getHoldemToken();
        if (0 == $retCode) {
            $this->logger->error('------------- fail end_session getHoldemToken() ----------------------------');
            return $this->fail('fail update_host getHoldemToken()');
        }
        $holdem->setAuthorization($access_token);

        //$call_back_url = 'http://121.133.173.232/web/call_back';
        $call_back_url = 'http://210.175.73.186/web/call_back';
        //$call_back_url = 'http://210.175.73.102:8080/web/call_back';

        $ret_update_host = $holdem->update_host($call_back_url);
        if (!isset($ret_update_host)) {
            $this->logger->error('------------- fail update_host holdem ----------------------------');
            return $this->fail('fail update_host holdem');
        }

        if (true === isset($ret_update_host->detail)) {
            $this->logger->error('------------- fail update_host holdem  detail : ' . json_encode($ret_update_host->detail));
            return $this->fail($ret_update_host->detail);
        }

        if ('success' != $ret_update_host->result) {
            $this->logger->error('------------- fail update_host holdum  not success : ');
            return $this->fail('fail update_host holdem  not success');
        }

        $response = [
            //'result_code' => 200,
            //'message' => $ret_update_host->result,
            'result' => 'success'
        ];
        return $this->respond($response, 200);
    }

    // 미발송 이벤트 가져오기
    public function history_event() {
        //$this->logger->critical('------------- success history_event new holdem ----------------------------');
        $holdem_admin_id = config(App::class)->holdem_admin_id;
        $holdem_admin_pass = config(App::class)->holdem_admin_pass;
        $holdem_api_url = config(App::class)->holdem_api_url;
        $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);

        if (!$holdem) {
            $this->logger->error('------------- fail history_event new holdem ----------------------------');
            $this->logger->error('------------- fail history_event new holdem ----------------------------');
            $this->logger->error('------------- fail history_event new holdem ----------------------------');
            die();
        }

        // Token Issuance 
        /* $ret_token = $holdem->token();
          if (!isset($ret_token)) {
          $this->logger->error('------------- fail history_event token holdem ----------------------------');
          $response['messages'] = 'fail token holdem';
          return $this->fail($response);
          }

          if (true === isset($ret_token->detail)) {
          $this->logger->error('------------- fail history_event token holdem  detail : ' . $ret_token->detail);
          $response['messages'] = $ret_token->detail->msg;
          return $this->fail($response);
          }

          $access_token = $ret_token->access_token; */
        list($retCode, $access_token) = $this->getHoldemToken();
        if (0 == $retCode) {
            $this->logger->error('------------- fail end_session getHoldemToken() ----------------------------');
            return $this->fail('fail history_event getHoldemToken()');
        }
        $holdem->setAuthorization($access_token);

        $ret_history_event = $holdem->history_event($_POST['username']);
        if (!isset($ret_history_event)) {
            $this->logger->error('------------- fail history_event holdem ----------------------------');
            return $this->fail('fail end_session holdem');
        }

        if (true === isset($ret_history_event->detail)) {
            $this->logger->error('------------- fail history_event holdem  detail : ' . $ret_end_session->detail);
            return $this->fail($ret_end_session->detail);
        }

        if ('success' != $ret_history_event->result) {
            $this->logger->error('------------- fail history_event holdum  not success : ');
            return $this->fail('fail end_session holdem  not success');
        }

        $this->logger->error(':::::::::::::::  history_event BetInfo : ' . json_encode($ret_history_event));
        //print_r($ret_history_event);
        //return;
        $memberModel = new MemberModel();

        if (is_null($ret_history_event->data)) {
            $this->logger->error('------------- not history_event data');
            return;
        }

        // 2 data json decode
        $history_data = json_decode($ret_history_event->data);

        try {
            //$this->logger->critical('------------- success start trans history_event new holdem ----------------------------');
            $memberModel->db->transStart();
            foreach ($history_data as $json_data) {
                // 3 data json decode
                $value = json_decode($json_data, true);
                $userName = explode('_', $value['username']);
                $member = $memberModel->getMemberWhereId($userName[1]);
                if ($member == null) {
                    $this->logger->error(':::::::::::::::  history_event member is null : ' . $userName[1]);
                    continue;
                }
                
                // call back data log
                $sql = "INSERT INTO HOLDEM_CALL_BACK_DATA(MBR_IDX,MONEY,EVENT,DATA,CREATE_DT,FUNCTION) VALUES(?,?,?,?,NOW(),?)";
                $memberModel->db->query($sql, [$member->getIdx(), $member->getMoney(), $value['event'], $json_data, __FUNCTION__]);

                //$credit = $value['credit'];
                $game_num = $value['game_num'];

                if ('QUERY_CREDIT' == $value['event']) {
                    // $this->logger->error(':::::::::::::::  call_back QUERY_CREDIT : ' . json_encode($__getData));
                    if (false == $this->queryCredit($value['amount'], $userName[1], $game_num, $member, __FUNCTION__)) {
                        $response['messages'] = '보유금액부족';
                        return $this->fail($response);
                    }
                } else if ('BUY_IN' == $value['event']) {
                    //$this->logger->error(':::::::::::::::  history_event BUY_IN : ' . json_encode($json_data));
                    if ($this->buyIn($value['stack'], $userName[1], $game_num, $member, $memberModel, __FUNCTION__)) {
                        $response['messages'] = '보유금액부족';
                        return $this->fail($response);
                    }
                } else if ('BUY_OUT' == $value['event']) {
                    //$this->logger->error(':::::::::::::::  history_event BUY OUT : ' . json_encode($json_data));
                    $this->buyOut($value['stack'], $game_num, $member, $memberModel, __FUNCTION__);
                } else if ('ROUND_END' == $value['event']) {
                    $this->roundEnd($value['before_stack'], $value['after_stack'], $value['wager'], $value['win'], $game_num, $value['event'], $member, $memberModel, __FUNCTION__, json_encode($json_data));
                }
            }
            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberModel->db->transRollback();
            $this->logger->critical(':::::::::::::::  history_event error : ' . $e->getMessage());
            $this->logger->critical(':::::::::::::::  history_event query : ' . $memberModel->getLastQuery());
            $response['messages'] = '디비처리 실패로 인한 배팅 실패';
            return $this->fail($response);
        }

        $response = [
            'result_code' => 200,
            'message' => $ret_history_event->result
        ];
        return $this->respond($response, 200);
    }

    public function update_session() {
        $holdem_admin_id = config(App::class)->holdem_admin_id;
        $holdem_admin_pass = config(App::class)->holdem_admin_pass;
        $holdem_api_url = config(App::class)->holdem_api_url;
        $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);

        if (!$holdem) {
            $this->logger->error('------------- fail update_host new holdem ----------------------------');
            $this->logger->error('------------- fail update_host new holdem ----------------------------');
            $this->logger->error('------------- fail update_host new holdem ----------------------------');
            die();
        }

        $user_id = $_POST['username'];

        if ($user_id == NULL) {
            $response['messages'] = '유저아이디가 잘못되었습니다.';
            return $this->fail($response);
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereId($user_id);
        if ($member == NULL) {
            $response['messages'] = '없는 유저입니다.';
            return $this->fail($response);
        }

        // Token Issuance 
        /* $ret_token = $holdem->token();
          if (!isset($ret_token)) {
          $this->logger->error('------------- fail update_host token holdem ----------------------------');
          $response['messages'] = 'fail token holdem';
          return $this->fail($response);
          }

          if (true === isset($ret_token->detail)) {
          $this->logger->error('------------- fail update_host token holdem  detail : ' . $ret_token->detail);
          $response['messages'] = $ret_token->detail->msg;
          return $this->fail($response);
          }

          $access_token = $ret_token->access_token; */
        list($retCode, $access_token) = $this->getHoldemToken();
        if (0 == $retCode) {
            $this->logger->error('------------- fail end_session getHoldemToken() ----------------------------');
            return $this->fail('fail update_session getHoldemToken()');
        }
        $holdem->setAuthorization($access_token);

        $ret_update_session = $holdem->update_session($user_id, $member->getMoney());
        if (!isset($ret_update_session)) {
            $this->logger->error('------------- fail update_session holdem ----------------------------');
            return $this->fail('fail update_session holdem');
        }

        if (true === isset($ret_update_session->detail)) {
            $this->logger->error('------------- fail update_session holdem  detail : ' . json_encode($ret_update_session->detail));
            return $this->fail($ret_update_session->detail);
        }

        if ('success' != $ret_update_session->result) {
            $this->logger->error('------------- fail update_session holdum  not success : ');
            return $this->fail('fail update_session holdem  not success');
        }

        $response = [
            'result_code' => 200,
            'message' => 'success'
        ];
        return $this->respond($response, 200);
    }

    // crontab job
    public function cron_end_session() {
        $memberModel = new MemberModel();

        $str_sql = "SELECT set_type_val FROM t_game_config WHERE set_type = 'holdem_check_time'";
        $holdem_config = $memberModel->db->query($str_sql)->getResultArray();

        // user over a period of time
        $checkTime = date("Y-m-d H:i:s", strtotime("-" . $holdem_config[0]['set_type_val'] . "second"));
        $query = "select idx, id, money from member where is_holdem_start = 'Y' and is_holdem_start_dt < ?";

        $userLists = $memberModel->db->query($query, [$checkTime])->getResultArray();
        if (0 == count($userLists)) {
            //$this->logger->error('cron_end_session not users!!');
            return $this->fail('cron_end_session not users!!');
        }

        $holdem_admin_id = config(App::class)->holdem_admin_id;
        $holdem_admin_pass = config(App::class)->holdem_admin_pass;
        $holdem_api_url = config(App::class)->holdem_api_url;
        $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);
        if (!$holdem) {
            $this->logger->error('------------- fail new holdem ----------------------------');
            return $this->fail('fail new holdem');
        }

        // 관리자 토큰 셋팅
        /* $ret_token = $holdem->token();
          if (!isset($ret_token)) {
          $this->logger->error('------------- fail token holdem ----------------------------');
          return $this->fail('fail token holdem');
          }

          if (true === isset($ret_token->detail)) {
          $this->logger->error('------------- fail token holdem  detail : ' . $ret_token->detail);
          return $this->fail($ret_token->detail->msg);
          }
          $holdem->setAuthorization($ret_token->access_token); */
        list($retCode, $access_token) = $this->getHoldemToken();
        if (0 == $retCode) {
            $this->logger->error('------------- fail end_session getHoldemToken() ----------------------------');
            return $this->fail('fail cron_end_session getHoldemToken()');
        }
        $holdem->setAuthorization($access_token);

        try {
            $memberModel->db->transStart();

            foreach ($userLists as $value) {
                // update_session
                //$this->logger->error('------------- end_session holdem id : ' . $member->getId() . ' detail : ' . $ret_token->access_token);
                $ret_end_session = $holdem->end_session($value['id']);
                if (!isset($ret_end_session)) {
                    $this->logger->error('------------- fail cron_end_session holdem ----------------------------');
                    continue;
                }

                if (true === isset($ret_end_session->detail)) {
                    $this->logger->error('------------- fail cron_end_session holdem  detail : ' . $ret_end_session->detail.' id : '.$value['id']);
                    list($retCode, $access_token) = $this->setHoldemToken();
                    if (0 == $retCode) {
                        $this->logger->error('------------- fail index setHoldemToken() ----------------------------');
                        return $this->fail('fail token holdem');
                    }
                    
                    //$holdem->setAuthorization($access_token);
                    continue;
                }

                //
                if ('success' != $ret_end_session->result) {
                    $this->logger->error('------------- fail cron_end_session holdum  not success');
                    continue;
                }

                $sql = "UPDATE member SET is_holdem_start = 'N' WHERE idx = ?";
                $memberModel->db->query($sql, [$value['idx']]);

                // Update User Retained Money.
                $coment = 'cron_end_session - holdem end[' . $value['id'] . ']';
                $tLogCashModel = new TLogCashModel();
                $tLogCashModel->insertCashLog_mem_idx('', $value['idx'], HOLDEM_END, 0, $ret_end_session->credit, 0, 'cron_end_session holdem end');
            }

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->critical('cron_end_session [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: cron_end_session query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
            return $this->fail('디비처리 실패로 인한 배팅 실패');
        }

        $response = [
            'result_code' => 200
        ];
        return $this->respond($response, 200);
    }

    // api용 토큰 가져오기
    public function getHoldemToken() {
        try {
            $memberModel = new MemberModel();
            $sql = "SELECT set_type_val FROM t_game_config WHERE u_level = 0 and set_type = 'holdem_token_key'";
            $result_game_config = $memberModel->db->query($sql)->getResult();
            $holdem_token_key = $result_game_config[0]->set_type_val;
            if ('' == $holdem_token_key) {
                return array(0, '');
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->critical('getHoldemToken [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: getHoldemToken query : ' . $memberModel->getLastQuery());
            return array(0, 'db error!!');
        }
        return array(1, $holdem_token_key);
    }

    // 토큰키 생성
    public function setHoldemToken() {
        try {
            $holdem_admin_id = config(App::class)->holdem_admin_id;
            $holdem_admin_pass = config(App::class)->holdem_admin_pass;
            $holdem_api_url = config(App::class)->holdem_api_url;
            $holdem = new Holdem($holdem_admin_id, $holdem_admin_pass, $holdem_api_url, $this->logger);

            if (!$holdem) {
                $this->logger->error('------------- fail new holdem ----------------------------');
                $this->logger->error('------------- fail new holdem ----------------------------');
                $this->logger->error('------------- fail new holdem ----------------------------');
                die();
            }

            // 관리자 토큰 셋팅
            $ret_token = $holdem->token();
            if (!isset($ret_token)) {
                $this->logger->error('------------- fail token holdem ----------------------------');
                return $this->fail('fail token holdem');
            }

            if (true === isset($ret_token->detail)) {
                $this->logger->error('------------- fail token holdem  detail : ' . $ret_token->detail);
                return $this->fail($ret_token->detail->msg);
            }

            $memberModel = new MemberModel();
            $holdem_token_key = $ret_token->access_token;
            $sql = "UPDATE t_game_config SET set_type_val = ? WHERE u_level = 0 and set_type = 'holdem_token_key'";
            $memberModel->db->query($sql, [$holdem_token_key]);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->critical('setHoldemToken [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: setHoldemToken query : ' . $memberModel->getLastQuery());
            return array(0, 'db error!!');
        }
        return array(1, $holdem_token_key);
    }

}
