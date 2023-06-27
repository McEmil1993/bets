<?php

namespace App\Controllers;

use App\Entities\Member;
use App\Models\GameModel;
use App\Models\MemberLoginHistoryModel;
use App\Models\MemberModel;
use App\Models\MemberUpdateHistoryModel;
use App\Models\MemberBetDetailModel;
use App\Models\TLogCashModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\TMessageModel;
use App\Models\TGameConfigModel;
use App\Util\SMS;
use App\Util\CodeUtil;
use App\Util\AbuseIPDB;

class MemberController extends BaseController {

    use ResponseTrait;

    public function login() {

        $memberModel = new MemberModel();
        $mLoginModel = new MemberLoginHistoryModel();

        try {
            $id = isset($_POST['id']) ? $_POST['id'] : NULL;
            $password = isset($_POST['password']) ? $_POST['password'] : NULL;

            $memberModel->db->transStart();
            $findMember = $memberModel->getMemberWhereId($id);
            if ($findMember == null) {
                $memberModel->db->transRollback();
                return $this->fail('조회되는 유저가 없습니다.');
            }
            //회원상태 ( 1:정상, 2:정지, 3:탈퇴, 11:승인 대기회원 )
            if (2 == $findMember->getStatus()) {
                $memberModel->db->transRollback();
                return $this->fail('회원님께서는 정지 된 회원으로 관리자에게 문의바랍니다.');
            } else if (3 == $findMember->getStatus()) {
                $memberModel->db->transRollback();
                return $this->fail('회원님께서는 탈퇴 된 회원으로 관리자에게 문의바랍니다.');
            } else if (11 == $findMember->getStatus()) {
                $memberModel->db->transRollback();
                return $this->fail('관리자 승인이 필요합니다.');
            }

            $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_site' ";
            $result_game_config = $memberModel->db->query($sql)->getResult();

            if ('Y' == $result_game_config[0]->set_type_val && 9 != $findMember->getLevel()) {
                $memberModel->db->transRollback();
                //return $this->fail(config(App::class)->CheckMessage);
            }

            if (password_verify($password, $findMember->getPassword()) == false) {
                $memberModel->db->transRollback();
                $mLoginModel->insertLog($findMember->getIdx(), $id, 'N');
                return $this->fail('비밀번호가 일치하지 않습니다.');
            }

            $client_ip = CodeUtil::get_client_ip();
            $sql = "SELECT count(*) as cnt FROM member_ip_block_history where ip = ?;";
            $result = $memberModel->db->query($sql, [$client_ip])->getResult()[0];
            if (0 < $result->cnt) {
                $memberModel->db->transRollback();
                return $this->fail('차단 된 아이피로 관리자에게 문의바랍니다.');
            }
            
            // Commercial ip check
            $abuse = new AbuseIPDB($this->logger);
            if (!$abuse) {
                $this->logger->error('------------- fail new abuse ----------------------------');
                return $this->fail('시스템 오류입니다. 관리자에게 문의바랍니다.');
            }

            if(9 != $findMember->getLevel()){
                $ret_check = $abuse->checkEndPoint($client_ip);
                if('Commercial' == $ret_check->data->usageType){
                    $sql = "UPDATE member set is_monitor_security = 'Y' WHERE idx = ? ";
                    $memberModel->db->query($sql, [$findMember->getIdx()]);

                    $client_ip = CodeUtil::get_client_ip();
                    $fDetail = '보안 모니터링(산업용 아이피) 이전=>N  이후=>Y';
                    $sql = "INSERT INTO `t_adm_log`(
                                        `a_id`,
                                        `a_ip`,
                                        `a_country`,
                                        `u_id`,
                                        `u_nick`,
                                        `log_data`,
                                        `log_type`,
                                        `reg_time`
                                        )
                                      VALUES ('system', '$client_ip', FN_GET_IP_COUNTRY('$client_ip'), '".$findMember->getId()."', '".$findMember->getNickName()."', '$fDetail', 94, now())";
                    $memberModel->query($sql);

                    $sql = "INSERT INTO `member_update_history`(
                                        `a_id`,
                                        `member_idx`,
                                        `update_type`,
                                        `before_data`,
                                        `after_data`,
                                        `create_dt`
                                        )
                                      VALUES ('system', ".$findMember->getIdx().", '보안 모니터링 상태(산업용 아이피)', 'N', 'Y', now())";
                    $memberModel->query($sql);
                }
            }

            $tMessageModel = new TMessageModel();
            $member_idx = $findMember->getIdx();
            $sql = "delete tm, tmlt from t_message as tm
                left join t_message_list as tmlt
                on tm.msg_idx = tmlt.idx
                where tm.member_idx = ? and tm.reg_time < date_sub( now(),interval 7 day);";

            $tMessageModel->db->query($sql, [$member_idx]);

            $sql = "select count(*) as cnt from t_message
                where member_idx = ? and read_yn = 'N' ";
            $result_sql = $tMessageModel->db->query($sql, [$member_idx])->getResult();

            $keep_login_access_token = CodeUtil::uuidgen4();

            session()->set('id', $id);
            session()->set('member_idx', $findMember->getIdx());
            session()->set('money', $findMember->getMoney());
            session()->set('point', $findMember->getPoint());
            session()->set('nick_name', $findMember->getNickName());
            session()->set('u_business', $findMember->getUBusiness());
            session()->set('tm_unread_cnt', $result_sql[0]->cnt);
            session()->set('level', $findMember->getLevel());
            session()->set('account_name', $findMember->getAccountName());
            session()->set('account_number', $findMember->getAccountNumber());
            session()->set('account_bank', $findMember->getAccountBank());
            session()->set('g_money', $findMember->getGmoney());
            session()->set('call', $findMember->getCall()); // 머지 대상 아
            session()->set('keep_login_access_token', $keep_login_access_token);
            session()->set('session_key',md5(mt_rand() . $findMember->getIdx()));

            //$_SESSION['session_key'] = md5(mt_rand() . $findMember->getIdx());
            $memberModel
                    ->set('session_key', $_SESSION['session_key'])
                    ->set('last_login', date("Y-m-d H:i:s", strtotime("Now")))
                    ->set('keep_login_access_token', $keep_login_access_token)
                    ->where('idx', $findMember->getIdx())
                    ->update();

            $mLoginModel->insertLog($findMember->getIdx(), $id, 'Y');
            $this->logger->info('::::::::::::::: login keep_login_access_token : ' . $keep_login_access_token.' id : '.$id);

            // 저장되어있지 않은 게임 타입을 추가해준다.
            $sql = "select * from member_game_type where member_idx = ? ";
            $result_member_game_type = $memberModel->db->query(
                            $sql, [$findMember->getIdx()]
                    )->getResultArray();

            $sql = "select * from lsports_game_type ";
            $result_lsports_game_type = $memberModel->db->query(
                            $sql
                    )->getResultArray();

            foreach ($result_lsports_game_type as $l_game_type) {
                $b_find = false;
                foreach ($result_member_game_type as $m_game_type) {
                    if ($l_game_type['id'] == $m_game_type['game_type']) {
                        $b_find = true;
                        break;
                    }
                }

                if (false == $b_find) {
                    $id = $l_game_type['id'];
                    $status = 'ON';
                    
                    // 실시간 단폴더는 OFF로 한다.
                    if(2 == $id){
                        $status = 'OFF';
                    }
                    
                    $member_idx = $findMember->getIdx();
                    $insert_sql = "insert into member_game_type(member_idx, game_type, status) values(?,?,?)";
                    $memberModel->db->query($insert_sql, [$member_idx, $id, $status]);
                }
            }

            $memberModel->db->transComplete();

            /* 
            ** API Gateway Login 
            */
            if (config('CasinoGateway')->enabled) {
                $curl = \Config\Services::curlrequest();
                $response = $curl->request('POST', config('CasinoGateway')->baseUrl . 'login', [
                    'form_params' => [
                        'user_id' => $id,
                        'password' => $password,
                        'site_code' => 'BTS',
                    ]
                ]);
                $responseBody = json_decode($response->getBody());
                if ($responseBody->success) {
                    session()->set('api_gateway_token', $responseBody->data->token);
                }
            }

        } catch (\mysqli_sql_exception $e) {
            $memberModel->db->transRollback();
            $this->logger->critical('login [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: login query : ' . $memberModel->getLastQuery());
            // return $this->fail('로그인 실패');
            return $this->fail($e->getMessage());
        }
        $response = [
            'result_code' => 200,
            'messages' => '로그인 성공',
            'keep_login_access_token'=>$keep_login_access_token,
            'data' => [
            ]
        ];
        return $this->respond($response, 200);
    }

    public function join() {
        $memberIdx = isset($_POST['id']) ? $_POST['id'] : NULL;
        $password = isset($_POST['password']) ? $_POST['password'] : NULL;
        $nickName = isset($_POST['nickname']) ? $_POST['nickname'] : NULL;
        $recommendCode = true === isset($_POST['recommend_code']) ? $_POST['recommend_code'] : NULL;
        $call = isset($_POST['call']) ? $_POST['call'] : NULL;
        $accountBank = isset($_POST['account_bank']) ? $_POST['account_bank'] : NULL;
        $accountNumber = isset($_POST['account_number']) ? $_POST['account_number'] : NULL;
        $accountName = isset($_POST['account_name']) ? $_POST['account_name'] : NULL;
        $birth = isset($_POST['birth']) ? $_POST['birth'] : NULL;
        $mobile_carrier = isset($_POST['mobile_carrier']) ? $_POST['mobile_carrier'] : 0;
        $this->logger->error('isset recommentCode : '.isset($_POST['recommend_code']));
        $this->logger->error('recommentCode : '.$recommendCode);
        $memberModel = new MemberModel();
        
        $this->logger->info('join : recommendCode '.$recommendCode);
        try {

            $memberModel->db->transStart();
            $findMember = $memberModel->where('id', $memberIdx)->find();

            if (count($findMember) > 0) {
                $memberModel->db->transRollback();
                return $this->fail('이미 가입 된 회원으로 확인됩니다.');
            }

            $sql = "select count(*) as count from member where `call` = ? ";
            $result_sql = $memberModel->db->query(
                            $sql, [$call]
                    )->getResult();

            if (0 < $result_sql[0]->count) {
                $memberModel->db->transRollback();
                return $this->fail('중복 전화번호로 확인되어 사용이 불가합니다.');
            }

            $sql = "select count(*) as count from member where account_number = ? ";
            $result_sql = $memberModel->db->query(
                            $sql, [$accountNumber]
                    )->getResult();

            if (0 < $result_sql[0]->count) {
                $memberModel->db->transRollback();
                return $this->fail('중복 계좌번호로 확인되어 사용이 불가합니다.');
            }

            $rememberIdx = NULL;
            $rememberId = NULL;
            if ($recommendCode != NULL) {
                $remember = $memberModel->where('recommend_code', $recommendCode)->first();
                if (null == $remember) {
                    $remember = $memberModel->where('id', $recommendCode)->first();
                }

                if (null == $remember) {
                    $memberModel->db->transRollback();
                    return $this->fail('추천인 코드가 사용 불가합니다.');
                }

                if ('N' == $remember->is_recommend || 1 != $remember->status) {
                    $memberModel->db->transRollback();
                    return $this->fail('추천을 받을수 없는 계정입니다. ');
                }

                // 추천인이 일반유저면  defaultDisId 계정으로 dis_id 값을 정해준다.
                if (1 == $remember->u_business) {
                    $rememberIdx = $remember->idx;

                    // 해당유저의 총판
                    $remember = $memberModel->where('id', 'defaultDisId')->first();
                    if (false === isset($remember) || true === empty($remember)) {
                        $memberModel->db->transRollback();
                        return $this->fail('defaultDisId 계정이 생성이 안되어있습니다. ');
                    }
                    $rememberId = $remember->id;

                    // 사용한 추천코드를 지운다.(1회성)
                    $sql = "update member set recommend_code = default where idx = ?";
                    $result = $memberModel->db->query($sql, [$rememberIdx]);
                } else {
                    $rememberIdx = $remember->idx;
                    $rememberId = $remember->id;
                }
                
                $this->logger->info('join : rememberId '.$rememberId);
                        
            } else {
                $memberModel->db->transRollback();
                return $this->fail('추천인 코드를 입력해야 가입 가능합니다. ');
            }

            // 9월 1일부터 신규가입 유저 20000포인트 지급
            $money = 0;

            // 아래 추천인 코드는 포인트 지급을 안한다.
            // 게임설정 가져온다.
            $tgcModel = new TGameConfigModel();
            $str_sql_config = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type IN('join_point','check_account_number')";
            $arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

            $arr_config = array();
            foreach ($arr_config_result as $key => $value) {
                $arr_config[$value['set_type']] = $value['set_type_val'];
            }

            // 계좌번호 체크
            if ($arr_config['check_account_number']) {

                if ($this->checkAccountNumber($accountBank, $accountNumber)) {
                    $memberModel->db->transRollback();
                    return $this->fail('해당계좌번호는 가입불가합니다.');
                }
            }
$this->logger->error('rememberId : '.$rememberId);
            $point = 0;
            //if (strtolower($rememberId) != 'kktv' && $rememberId != strtolower('oxcom') && $rememberId != strtolower('gm999')) {
            if (strtolower($rememberId) == 'hktv' || strtolower($rememberId) == 'HKTV') {
                $this->logger->info('join : join_point'.$arr_config['join_point']);
                $point = $arr_config['join_point']; //20000;
            }
            //$this->logger->error('point : '.$point);
            $joinMember = [
                'id' => $memberIdx,
                'nick_name' => $nickName,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'is_recommend' => 'Y',
                'recommend_member' => $rememberIdx,
                'dis_id' => $rememberId,
                'call' => $call,
                'account_bank' => $accountBank,
                'account_number' => $accountNumber,
                'account_name' => $accountName,
                'money' => $money,
                'point' => $point,
                'birth' => $birth,
                'mobile_carrier' => $mobile_carrier,
                'coin_password' => $password
            ];

            $joinResult = $memberModel->insert($joinMember);
            

            // 가입축하 쪽지 발송
            $memberModel->sendJoinMessage($memberIdx);
            $sql = "select * from lsports_game_type ";
            $result_lsports_game_type = $memberModel->db->query(
                            $sql
                    )->getResultArray();

            foreach ($result_lsports_game_type as $l_game_type) {
        
                    $id = $l_game_type['id'];
                    $status = 'ON';
                    
                    // 실시간 단폴더는 OFF로 한다.
                    if(2 == $id){
                        $status = 'OFF';
                    }

                    $member_idx = $joinResult;
                    $insert_sql = "insert into member_game_type(member_idx, game_type, status) values(?,?,?)"; 

                    
                    $memberModel->db->query($insert_sql, [$member_idx, $id, $status])->getResult();

            }

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberModel->db->transRollback();
            return $this->fail($e);
        }

        $response = [
            'result_code' => 200,
            'messages' => '회원 가입 성공',
            'data' => [
                'member' => $joinResult,
            ]
        ];
        return $this->respond($response, 200);
    }

    public function logout() {
        session()->destroy();
        /*session()->remove('id');
        session()->remove('member_idx');
        session()->remove('money');
        session()->remove('point');
        session()->remove('nick_name');
        session()->remove('u_business');
        session()->remove('tm_unread_cnt');
        session()->remove('level');
        session()->remove('call');
        session()->remove('session_key');
        session()->remove('g_money');*/
        return redirect()->to(base_url("/"));
    }

    public function passwordChange() {
        $memberIdx = session()->get('member_idx');
        $beforePassword = isset($_POST['beforePassword']) ? $_POST['beforePassword'] : NULL;
        $afterPassword = isset($_POST['afterPassword']) ? $_POST['afterPassword'] : NULL;

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            $response = [
                'result_code' => 400,
                'messages' => '조회되는 회원이 없습니다.',
                'data' => []
            ];
            return $this->fail($response);
        }

        if (!password_verify($beforePassword, $member->getPassword())) {
            $response = [
                'result_code' => 401,
                'messages' => '비밀번호가 일치하지 않습니다.',
                'data' => []
            ];
            return $this->fail($response);
        }

        try {
            $memberModel->db->transStart();
            $memberModel->set('password', password_hash($afterPassword, PASSWORD_DEFAULT))->where('idx', $memberIdx)->update();

            $beHashPassword = $member->getPassword();
            $afHashPassword = $memberModel->getMemberWhereIdx($memberIdx)->getPassword();
            # 로그 추가
            $muhData = [
                'member_idx' => $memberIdx,
                'update_type' => '비밀번호',
                'before_data' => $beHashPassword,
                'after_data' => $afHashPassword,
            ];
            $muhModel = new MemberUpdateHistoryModel();
            $muhModel->insert($muhData);
            $memberModel->db->transComplete();

            $response = [
                'result_code' => 200,
                'messages' => '비밀번호 변경이 성공하였습니다. ',
                'data' => []
            ];
            return $this->respond($response, 200);
        } catch (\mysqli_sql_exception $e) {
            $memberModel->db->transRollback();
            return $this->fail('비밀번호 변경에 실패하였습니다.');
        }
    }

    public function passwordCheck() {
        $memberIdx = session()->get('member_idx');
        $password = isset($_POST['password']) ? $_POST['password'] : NULL;

        if (strlen($password) < 4 || 12 < strlen($password)) {
            $response = [
                'result_code' => 400,
                'messages' => '패스워드는 4~12 이상 사용가능합니다.',
                'data' => []
            ];
            return $this->fail($response);
        }
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            $response = [
                'result_code' => 400,
                'messages' => '조회되는 회원이 없습니다.',
                'data' => []
            ];
            return $this->fail($response);
        }

        if (!password_verify($password, $member->getPassword())) {
            $response = [
                'result_code' => 401,
                'messages' => '비밀번호가 일치하지 않습니다.',
                'data' => []
            ];
            return $this->fail($response);
        }

        $response = [
            'result_code' => 200,
            'messages' => '비밀번호가 일치합니다.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function idCheck() {
        $memberId = isset($_POST['id']) ? $_POST['id'] : NULL;

        if (strlen($memberId) < 4 || 12 < strlen($memberId)) {
            return $this->fail('아이디는 4~12 이상 사용가능합니다.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereId($memberId);

        if ($member != null) {
            return $this->fail('중복된 회원');
        }

        $response = [
            'result_code' => 200,
            'messages' => '아이디 사용 가능.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function nickNameCheck() {

        $nickName = isset($_POST['nick_name']) ? $_POST['nick_name'] : NULL;

        if (mb_strlen($nickName, 'UTF-8') < 2 /* || 11 < mb_strlen($nickName, 'euc-kr') */) {
            return $this->fail('닉네임은 2자 이상으로 사용가능합니다');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereNickName($nickName);

        if ($member != null) {
            return $this->fail('중복된 닉네임');
        }

        $response = [
            'result_code' => 200,
            'messages' => '닉네임 사용 가능.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function duplicateLoginCheck() {

        //$this->logger->critical('::::::::::::::: duplicateLoginCheck start : keep_login_access_token '.$_POST['keep_login_access_token']);

        //list($findMember, $memberModel, $message) = $this->checkLoginAccessToken();
        
        $memberIdx = session()->get('member_idx');

        $memberModel = new MemberModel();
        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        
        if (null === $findMember) {
            $this->logger->critical('::::::::::::::: duplicateLoginCheck null === findMember : message   session_id : '.session()->get('id'));
            return $this->fail('해당유저가 없습니다.');
        }

        try {
            $memberModel->db->transStart();
            if ($findMember->getSessionKey() != $_POST['session_key']) {
                $memberModel->db->transRollback();
                $this->logger->critical('::::::::::::::: duplicateLoginCheck null === session_key : ');
                return $this->fail('다른 곳에서 로그인되어 자동 로그 아웃 처리 됩니다.');
            }

            $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_site' ";

            $result_game_config = $memberModel->db->query($sql)->getResult();

            if ('Y' == $result_game_config[0]->set_type_val && 10 != session()->get('level')) {
                $this->logger->critical('::::::::::::::: duplicateLoginCheck check_server : ');
                $memberModel->db->transRollback();
                return $this->fail(config(App::class)->CheckMessage);
            }

            //$keep_login_access_token = CodeUtil::uuidgen4();
            //$keep_login_access_token = $findMember->get_keep_login_access_token();
            session()->set('money', $findMember->getMoney());
            session()->set('point', $findMember->getPoint());
            session()->set('g_money', $findMember->getGmoney());
            session()->set('level', $findMember->getLevel());
            //session()->set('keep_login_access_token', $keep_login_access_token);
            
            //$sql = "update member set last_login=?, check_login=?,keep_login_access_token = ? where idx=?";
            //$memberModel->db->query($sql,[date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$keep_login_access_token,$findMember->getIdx()])->getResult();
            $sql = "update member set last_login=?, check_login=? where idx=?";
            $memberModel->db->query($sql,[date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$findMember->getIdx()])->getResult();

            //$this->logger->critical('::::::::::::::: duplicateLoginCheck change keep_login_access_token '.session()->get('keep_login_access_token'));
            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $memberModel->db->transRollback();
            $this->logger->critical('duplicateLoginCheck [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->critical('::::::::::::::: duplicateLoginCheck query : ' . $memberModel->getLastQuery());
            return $this->fail('인증실패');
        }
        $response = [
            'result_code' => 200,
            'messages' => '정상',
            'data' => [
                'money' => $findMember->getMoney(),
                'point' => $findMember->getPoint(),
                'g_money' => $findMember->getGmoney(),
                'level' => $findMember->getLevel(),
                'nick_name' => session()->get('nick_name'),
                'notes' => $this->getMyNotes(),
                //'keep_login_access_token' => $keep_login_access_token
            ]
        ];

        return $this->respond($response, 200);
    }

    // 포인트->머니 전환
    public function pointToMoney() {
        $memberIdx = session()->get('member_idx');

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);
        $be_point = $member->getPoint();
        $be_money = $member->getMoney();
        $ubusiness = $member->getUBusiness();

        if ($ubusiness == 1) {
            if ($be_point <= 0) {
                $response = [
                    'result_code' => 400,
                    'messages' => '보유 포인트가 없습니다.',
                    'data' => []
                ];
                return $this->fail($response);
            }
        }

        try {
            $money = $be_money + $be_point;
            $sql = "update member set point = 0, money = money + ? where idx = ?";
            $result_sql = $memberModel->db->query($sql, [$be_point, $memberIdx]);

            $uKey = md5($memberIdx . strtotime('Now'));
            $tLogCashModel = new TLogCashModel();
            try {
                $tLogCashModel->insertCashLog_2($uKey, 5, 0, $be_point, $be_money, $be_point, $be_point, 0, 'P', '');
            } catch (\ReflectionException $e) {
                return $this->fail('포인트 전환중에 에러가 발생하였습니다.');
            }
        } catch (\mysqli_sql_exception $e) {
            return $this->fail('포인트 전환이 실패하였습니다.');
        }

        session()->set('money', $money);
        session()->set('point', 0);

        $response = [
            'result_code' => 200,
            'messages' => '포인트 전환이 성공하였습니다. ',
            'data' => ['money' => $money, 'point' => 0, 'bePoint' => $be_point]
        ];
        return $this->respond($response, 200);
    }

    // 인증코드 발금
    public function requestAuthCode() {
        $type = isset($_POST['type']) ? $_POST['type'] : NULL;
        $member_idx = isset($_POST['member_idx']) ? $_POST['member_idx'] : NULL;

        $memberModel = new MemberModel();
        if ($type == 1) {
            $findMember = $memberModel->getMemberWhereIdx($member_idx);

            if ($findMember == null) {
                return $this->fail('조회되는 유저가 없습니다.');
            }

            $call = $findMember->getCall();
        } else {
            $call = isset($_POST['call']) ? $_POST['call'] : NULL;

            if (false == CodeUtil::idCheck($call)) {
                return $this->fail('전화번호는 숫자만 가능합니다.');
            }

            $findMemberCall = $memberModel->getMemberWhereCall($call);

            if (!empty($findMemberCall)) {
                return $this->fail('중복된 핸드폰번호입니다.');
            }
        }
        
        // 인증제한 체크
        if($call == '01062760134'){
            return $this->fail('인증제한횟수가 초과되었습니다. 관리자에게 문의해주세요.');
        }
        
        $authCode = mt_rand(1000, 9999);
        $toYear = date("yyyy");
        $toMonth = date("m");
        $toDay = date("d");
        $toHour = date("H");
        $toMin = date("i");
        //$call = '01085932604;';

        session()->set('auth_code', $authCode);
        session()->set('auth_code_time', strtotime("now"));
        session()->set('call', $call);

        //$msg = '[K-Win] 인증번호[' . $authCode . ']';
        $name = config(App::class)->ServerName;
        $msg = '[' . $name . '] 인증번호[' . $authCode . ']';
        $this->logger->debug("requestAuthCode msg =>" . $msg);
        // 문자발송
        $SMS = new SMS();    /* SMS 모듈 클래스 생성 */

        // 토큰키 셋팅
        $SMS->SMS_set_key(config(App::class)->SMSTokenKey);

        /**
         * 문자발송 Form을 사용하지 않고 자동 발송의 경우 수신번호가 1개일 경우 번호 마지막에 ";"를 붙인다
         * ex) $strTelList = "0100000001;";
         */
        $strTelList = $call;    /* 수신번호 : 01000000001;0100000002; */
        $strCallBack = '01083260391';  /* 발신번호 : 0317281281 */
        //$strSubject = 'K-Win';    /* LMS제목  : LMS발송에 이용되는 제목( component.php 60라인을 참고 바랍니다. */
        $strSubject = $name;    /* LMS제목  : LMS발송에 이용되는 제목( component.php 60라인을 참고 바랍니다. */
        $strData = $msg;        /* 메세지 : 발송하실 문자 메세지 */
        $strDate = "";

        $chkSendFlag = 0;  /* 예약 구분자 : 0 즉시전송, 1 예약발송 */
        $strTelList = explode(";", $strTelList);

        /* print_r($strTelList);
          echo '	'.$strCallBack;
          echo '	'.$strSubject;
          echo '	'.$strData;
          echo '	'.$chkSendFlag;
          return; */

        // 문자 발송에 필요한 항목을 배열에 추가
        $result = $SMS->Add($strTelList, $strCallBack, $strData, $strSubject, $strDate);

        // 패킷 정의의 결과에 따라 발송여부를 결정합니다.
        if ($result) {
            //echo "일반메시지 입력 성공<br />";
            //echo "<hr>";
            // 패킷이 정상적이라면 발송에 시도합니다.
            $result = $SMS->Send();

            if ($result) {
                //echo "서버에 접속했습니다.<br /><br />";
                $success = $fail = 0;
                //$isStop = 0;
                foreach ($SMS->Result as $result) {
                    list($phone, $code) = explode(":", $result);
                    if (substr($code, 0, 5) == "Error") {
                        //echo $phone.' 발송에러('.substr($code,6,2).'): ';
                        $errMes = '';
                        switch (substr($code, 6, 2)) {
                            case '23':   // "23:데이터오류, 전송날짜오류, 발신번호미등록"
                                $errMes = "데이터를 다시 확인해 주시기바랍니다.";
                                break;

                            // 아래의 사유들은 발송진행이 중단됨.
                            case '85':   // "85:발송번호 미등록"
                                $errMes = "등록되지 않는 발송번호 입니다.";
                                break;
                            case '87':   // "87:인증실패"
                                $errMes = "(정액제-계약확인)인증 받지 못하였습니다.";
                                break;
                            case '88':   // "88:연동모듈 발송불가"
                                $errMes = "연동모듈 사용이 불가능합니다. 아이코드로 문의하세요.";
                                break;

                            case '96':   // "96:토큰 검사 실패"
                                $errMes = "사용할 수 없는 토큰키입니다.";
                                break;
                            case '97':   // "97:잔여코인부족"
                                $errMes = "잔여코인이 부족합니다.";
                                break;
                            case '98':   // "98:사용기간만료"
                                $errMes = "사용기간이 만료되었습니다.";
                                break;
                            case '99':   // "99:인증실패"
                                $errMes = "서비스 사용이 불가능합니다. 아이코드로 문의하세요.";
                                break;
                            default:   // "미 확인 오류"
                                $errMes = "알 수 없는 오류로 전송이 실패하었습니다.";
                                break;
                        }
                        $fail++;
                    } else {
                        $resultString = '';
                        switch (substr($code, 0, 2)) {
                            case '17':   // "17: 접수(발송)대기 처리. 지연해소시 발송됨."
                                //echo "접수(발송)대기처리 되었습니다.";
                                $this->logger->debug("requestAuthCode message : 접수(발송)대기처리 되었습니다. call : " . $call);
                                break;
                            default:   // "00: 전송완료."
                                //echo "전송되었습니다.";
                                break;
                        }
                        //echo $phone.'로 '.$resultString.' (msg seq : '.$code.')<br />';
                        $success++;
                    }
                }
                //echo '<br />'.$success."건을 전송했으며 ".$fail."건을 보내지 못했습니다.<br />";
                if ($fail > 0) {
                    $this->logger->debug("requestAuthCode message : " . $errMes . " call : " . $call);
                }
                $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
            } else {
                //echo "에러: SMS 서버와 통신이 불안정합니다.<br />";
                $this->logger->debug("requestAuthCode error - message : 에러: SMS 서버와 통신이 불안정합니다. call : " . $call);
            }
        } else {
            $this->logger->debug("requestAuthCode error - message : 에러: SMS->Add 실패 call : " . $call);
        }

        // 운영툴 sms 인증내역를 남긴다.
        $sql = "insert into sms_auth(phone_number, auth_number, create_dt, status) values(?,?, now(), 1) ";
        $result_sql = $memberModel->db->query($sql, [$call, $authCode]);
        $response = [
            'result_code' => 200,
            'messages' => '인증코드 발송성공',
            'data' => [
            ]
        ];
        return $this->respond($response, 200);
    }

    // 인증코드 확인
    public function authCodeCheck() {
        $authCode = isset($_POST['auth_code']) ? $_POST['auth_code'] : NULL;
        $call = session()->get('call');

        if (false == CodeUtil::idCheck($call)) {
            return $this->fail('전화번호는 숫자만 가능합니다.');
        }

        if (mb_strlen($authCode) <= 0) {
            return $this->fail('인증코드를 입력해주세요.');
        }

        $tgcModel = new TGameConfigModel();
        $str_sql_config = "SELECT set_type_val FROM t_game_config WHERE set_type = 'auth_code_cheat_use'";
        $arr_config = $tgcModel->db->query($str_sql_config)->getResultArray();
        $auth_code_cheat_use = $arr_config[0]['set_type_val'];

        // 인증코드 치트사용시 치트키 체크
        if ($auth_code_cheat_use == 1) {
            if (strcmp($authCode, config(App::class)->AuthCode) == 0) {
                $response = [
                    'result_code' => 200,
                    'messages' => '인증성공.',
                    'data' => []
                ];
                return $this->respond($response, 200);
            }
        }

        // 인증시간 체크
        $auth_code_time = session()->get('auth_code_time');
        $current_micro_time = strtotime("now");
        if (180 < $current_micro_time - $auth_code_time) {
            $this->logger->debug("authCodeCheck error - message : 에러: 기간만료 call : " . $call . " authCode : " . $authCode . " remainTime : " . ($current_micro_time - $auth_code_time));
            return $this->fail('인증코드 기간 만료로 관리자에게 문의바랍니다.');
        }

        // 발급한 인증코드
        $member_authCode = session()->get('auth_code');
        if ($member_authCode != $authCode) {
            $this->logger->debug("authCodeCheck error - message : 에러: 인증실패 call : " . $call . " authCode : " . $authCode . " member_authCode : " . $member_authCode);
            return $this->fail('인증코드가 일치하지 않습니다.');
        }

        // 운영툴 sms 인증내역를 남긴다.
        $sql = "insert into sms_auth(phone_number, auth_number, create_dt, status) values(?,?, now(), 2) ";
        $result_sql = $tgcModel->db->query($sql, [$call, $member_authCode]);

        $response = [
            'result_code' => 200,
            'messages' => '인증성공.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    // 추천인 코드 체크
    public function checkRecommendCode() {
        $recommendCode = isset($_POST['recommend_code']) ? $_POST['recommend_code'] : NULL;
        $rememberId = NULL;

        if ($recommendCode == NULL) {
            return $this->fail('추천코드를 입력해주세요.');
        }

        $memberModel = new MemberModel();
        if ($recommendCode != NULL) {

            if (strtolower($recommendCode) == config(App::class)->ExcludedRecommandCode) {
                return $this->fail('추천인 코드가 사용 불가합니다.');
            }

            $remember = $memberModel->where('recommend_code', $recommendCode)->first();
            if (null == $remember) {
                $remember = $memberModel->where('id', $recommendCode)->first();

                // 존재하지 않는 계정이다.
                if (null == $remember) {
                    return $this->fail('추천인 코드가 사용 불가합니다.');
                }

                // 유저는 아이디 입력을 하면 안된다.
                if ($remember->u_business == 1) {
                    return $this->fail('추천인 코드가 사용 불가합니다.');
                }
            }
        } else {
            return $this->fail('추천인 코드를 입력해야 가입 가능합니다. ');
        }

        $response = [
            'result_code' => 200,
            'messages' => '인증성공.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function getRankList() {
        $curPageNo = isset($_POST['curPageNo']) ? $_POST['curPageNo'] : 1;
        $startCnt = ($curPageNo - 1) * 10;
        $displayCnt = 10;
        $memberModel = new MemberModel();

        $sql = "SELECT * FROM member WHERE idx > 0 ORDER BY money DESC LIMIT ?, ?";
        $list = $memberModel->db->query($sql, [$startCnt, $displayCnt])->getResult();
        $sql = "SELECT COUNT(*) AS count FROM member";
        $count = $memberModel->db->query($sql)->getResult();

        $response = [
            'result_code' => 200,
            'messages' => '인증성공.',
            'data' => [
                'list' => $list,
                'count' => $count,
                'curPageNo' => $curPageNo
            ]
        ];
        return $this->respond($response, 200);
    }

    // 추천코드 변경
    public function setRecommentCode() {
        $memberIdx = isset($_POST['member_idx']) ? $_POST['member_idx'] : 0;
        $recommend_code = isset($_POST['recommend_code']) ? $_POST['recommend_code'] : NULL;

        if ($recommend_code == NULL) {
            $response = [
                'result_code' => 400,
                'messages' => '추천인 코드를 입력해주세요.',
                'data' => []
            ];
            return $this->fail($response);
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);
        if ($member == null) {
            $response = [
                'result_code' => 400,
                'messages' => '존재하지 않는 계정입니다.',
                'data' => []
            ];
            return $this->fail($response);
        }



        $sql = "select count(*) as cnt from member where recommend_code = ?";
        $result = $memberModel->db->query($sql, [$recommend_code])->getResultArray();
        if ($result[0]['cnt'] > 0) {
            $response = [
                'result_code' => 400,
                'messages' => '사용중인 코드입니다.',
                'data' => []
            ];
            return $this->fail($response);
        }

        try {

            $sql = "update member set recommend_code = ?, recommend_code_dt = now() where idx = ?";
            $result_sql = $memberModel->db->query($sql, [$recommend_code, $memberIdx]);
        } catch (\mysqli_sql_exception $e) {
            $response = [
                'result_code' => 400,
                'messages' => '추천인 코드 변경에 실패 하였습니다.',
                'data' => ['recommend_code' => $recommend_code]
            ];
            return $this->respond($response);
        }

        $response = [
            'result_code' => 200,
            'messages' => '추천인 코드 변경에 성공하였습니다. ',
            'data' => ['recommend_code' => $recommend_code]
        ];
        return $this->respond($response, 200);
    }

    // 회원 상태변경
    public function setMemberStatus() {
        $memberIdx = isset($_POST['member_idx']) ? $_POST['member_idx'] : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : 1;

        if ($memberIdx == 0) {
            $response = [
                'result_code' => 400,
                'messages' => '회원번호를 넣어주세요.',
                'data' => []
            ];
            return $this->fail($response);
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);
        if ($member == null) {
            $response = [
                'result_code' => 400,
                'messages' => '존재하지 않는 계정입니다.',
                'data' => []
            ];
            return $this->fail($response);
        }

        // 총판이고 정상상태가 아닌 변경이면 요율설정을 초기화한다.
        if (($member->getUBusiness() == 2 || $member->getUBusiness() == 3) && $status != 1) {
            $sql = "update shop_config set bet_pre_s_fee = 0, bet_pre_d_fee = 0, bet_real_s_fee = 0, bet_real_d_fee = 0, bet_mini_fee = 0, pre_s_fee = 0 where member_idx = ?";
            $result_sql = $memberModel->db->query($sql, [$memberIdx]);
        }

        try {
            $sql = "update member set status = ? where idx = ?";
            $result_sql = $memberModel->db->query($sql, [$status, $memberIdx]);
        } catch (\mysqli_sql_exception $e) {
            $response = [
                'result_code' => 400,
                'messages' => '상태변경에 실패 하였습니다.',
                'data' => ['status' => $status]
            ];
            return $this->respond($response);
        }

        $response = [
            'result_code' => 200,
            'messages' => '상태변경에 성공하였습니다. ',
            'data' => ['status' => $status]
        ];
        return $this->respond($response, 200);
    }

    // 동접자 체크
    public function updateCurrentUserCount() {
        $memberModel = new MemberModel();
        $sql = "select count(*) as cnt from member where check_login > DATE_SUB(NOW(),INTERVAL 10 SECOND)";
        $result = $memberModel->db->query($sql)->getResultArray();
        $count = $result[0]['cnt'];
        $sql = "update  total_system set current_count = ?";
        $memberModel->db->query($sql, [$count]);
    }

    // 계좌번호 체크
    public function checkAccountNumber($bankName, $accountNumber) {
        $result = false;
        $num = substr($accountNumber, 0, 3);

        if (0 == strcmp($bankName, '신한은행')) {
            if (0 == strcmp($num, '562')) {
                $result = true;
            }
        } else if (0 == strcmp($bankName, '카카오뱅크')) {
            $num = substr($accountNumber, 0, 4);
            if (0 == strcmp($num, '3355') || 0 == strcmp($num, '7979') || 0 == strcmp($num, '7777')) {
                $result = true;
            }
        } else if (0 == strcmp($bankName, '농협')) {
            if (0 == strcmp($num, '356')) {
                $result = true;
            }
        }
        return $result;
    }

    // 포인트내역
    public function pointHistory() {

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $start_page = ($page - 1) * 10;
        //tLogCashModel = new TLogCashModel();
        $memberModel = new MemberModel();
        try {

            $member_idx = session()->get('member_idx');
            $chkMobile = CodeUtil::rtn_mobile_chk();
            $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

            if ($member_idx == NULL || null == session()->get('call')) {
            
                return redirect()->to(base_url("/$viewRoot/index"));
            }
            
            $member = $memberModel->getMemberWhereIdx($member_idx);
            if ($member == null) {
                return redirect()->to(base_url("/$viewRoot/index"));
            }

            // 추천인 리스트를 가져온다.
            $sql_count = "SELECT id FROM member where recommend_member = ? ";
            $recommendCountList = $memberModel->db->query($sql_count, [$member_idx])->getResultArray();
            $recommendAllCount = count($recommendCountList);
            
            // 전체 갯수 8 9  126 127
            $param = array($member_idx);
            $member_idx = $member->getIdx();
            $cntSql = "SELECT count(*) AS cnt FROM t_log_cash WHERE member_idx = $member_idx AND ac_code IN(5,6,8,9,10,11,123,124,126,127,202,203,3001,3002) AND (r_money <> 0 OR point <> 0) 
                       AND reg_time BETWEEN DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00'), INTERVAL -1 WEEK) AND DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')";
            $pointCnt = $memberModel->db->query($cntSql)->getResultArray();
            //$cnt = true === isset($pointCnt) ? count($pointCnt): 0;
            $cnt = $pointCnt[0]['cnt'] + 1;
          
            $sql = "SELECT * FROM (SELECT @rownum := @rownum - 1 AS no
                         , tlc.*
                      FROM t_log_cash as tlc
                         , (SELECT @rownum := ?) AS r
                     WHERE tlc.member_idx = ?
					   AND tlc.ac_code IN (5,6,8,9,10,11,126,127,202,203,3001,3002)
                       AND (r_money != 0 or `point` != 0)
                       AND tlc.reg_time BETWEEN DATE_ADD(DATE_FORMAT(NOW() , '%Y-%m-%d 00:00:00'), INTERVAL -1 WEEK ) AND DATE_FORMAT(NOW(), '%Y-%m-%d 23:59:59')
                     ORDER BY tlc.reg_time DESC) a LIMIT ?, 10";
            $pointList = $memberModel->db->query($sql, [$cnt, $member_idx, $start_page])->getResultArray();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('MemberController [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: pointHistory query : ' . $memberModel->getLastQuery());
        } catch (\Exception $e) {
            $this->logger->error('MemberController Exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        }
     
        return view("$viewRoot/point_history", [
            'totalCnt' => $cnt,
            'pointList' => $pointList,
            'page' => $page,
            'num_per_page' => 10,
            'recommendAllCount'=> $recommendAllCount
        ]);
    }

    public function changePassword() {
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        return view("$viewRoot/change_password", [
        ]);
    }

    // 추천회원 리스트
    public function recommendMember() {

        try {
            $chkMobile = CodeUtil::rtn_mobile_chk();
            $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

            $member_idx = session()->get('member_idx');
            $page = isset($_GET['page']) ? $_GET['page'] : 1;

            // 추천인 리스트를 가져온다.
            $sql_count = "SELECT id FROM member where recommend_member = ? ";

            $memberModel = new MemberModel();
            $recommentChargeInfo = array();

            $recommendCountList = $memberModel->db->query($sql_count, [$member_idx])->getResultArray();
            $recommendAllCount = count($recommendCountList);

            // echo "recommendAllCount : " ;
            // print_r($recommendAllCount);
            // echo "<br><br>" ;

            $start_page = ($page - 1) * 10;

            $sql = "SELECT id,idx,nick_name,recommend_code_dt,level,reg_time  FROM member where recommend_member =  ? order by reg_time desc limit ?,10";
            $recommendList = $memberModel->db->query($sql, [$member_idx, $start_page])->getResultArray();

            if ($recommendAllCount > 0) {
                $recomment_phs = array();
                $recomment_idxs = array();
                foreach ($recommendList as $key => $value) {
                    $recomment_idxs[] = $value['idx'];
                    $recomment_phs[] = '?';
                }
                $recomment_ph_str = implode(',', $recomment_phs);
                $cnt = $recommendAllCount + 1;
                // 추천회원 리스트
                $sql = "select*from(SELECT  @rownum := @rownum - 1 AS no,id,idx,nick_name,recommend_code_dt,level,reg_time  FROM member, (SELECT @rownum := ?) AS r where recommend_member =  ? order by reg_time desc) a limit ?,10";
                $recommendList = $memberModel->db->query($sql, [$cnt, $member_idx, $start_page])->getResultArray();

                $sql = "SELECT member_idx, count(idx) as cnt, sum(money) as money FROM member_money_charge_history where member_idx in (" . $recomment_ph_str . ") and status = 3 group by member_idx;";
                $recommentChargeInfo = $memberModel->db->query($sql, $recomment_idxs)->getResultArray();
            }
            $totalCnt = $recommendAllCount;
            return view("$viewRoot/recommend_member", [
                'recommendAllCount' => $recommendAllCount,
                'recommendList' => $recommendList,
                'recommentChargeInfo' => $recommentChargeInfo,
                'totalCnt' => $totalCnt,
                'page' => $page,
                'num_per_page' => 10
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // 쪽지함
    public function getMyNotes(){
        $tMessageModel = new TMessageModel();
        $member_idx = session()->get('member_idx');
        $messages = $tMessageModel
                ->select(['t_message.*', 'tml.title', 'tml.content', 'tml.a_id'])
                ->join('t_message_list tml', 't_message.msg_idx = tml.idx', 'left')
                ->where('t_message.member_idx', $member_idx)
                ->where('t_message.is_delete', 0)
                ->where('t_message.read_yn', 'N')
                ->orderBy('t_message.idx', 'desc')
                ->find();
        return count($messages);
    }
    public function note() {
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $tMessageModel = new TMessageModel();
        $member_idx = session()->get('member_idx');
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }


        // echo "viewRoot : " . $viewRoot ;
        // echo "member_idx : " . $member_idx ;

        $sql = "delete tm, tmlt from t_message as tm
            left join t_message_list as tmlt
            on tm.msg_idx = tmlt.idx
            where tm.member_idx = ? and tm.reg_time < date_sub( now(),interval 3 day);";
        $tMessageModel->db->query($sql, [$member_idx]);

        $messages = $tMessageModel
                ->select(['t_message.*', 'tml.title', 'tml.content', 'tml.a_id'])
                ->join('t_message_list tml', 't_message.msg_idx = tml.idx', 'left')
                ->where('t_message.member_idx', $member_idx)
                ->where('t_message.is_delete', 0)
                ->orderBy('t_message.idx', 'desc')
                ->find();

        return view("$viewRoot/message", [
            'messageList' => $messages,
        ]);
    }

}
