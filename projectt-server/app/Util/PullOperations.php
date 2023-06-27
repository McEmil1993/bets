<?php

namespace App\Util;
use App\Models\MemberMoneyChargeHistoryModel;

use CodeIgniter\Log\Logger;

class PullOperations {

    private $username;
    private $password;
    private $guid;
    private $packageID;
    private $inplay_packageID;
    private $lang;
    private $getFixturesURL = 'http://prematch.lsports.eu/OddService/GetFixtures';
    private $getFixturesSTMURL = 'https://stm-snapshot.lsports.eu/PreMatch/GetFixtures';
    private $getFixtureMarketsURL = 'http://prematch.lsports.eu/OddService/GetFixtureMarkets';
    private $getEventsURL = 'http://prematch.lsports.eu/OddService/GetEvents';
    private $getSportsURL = 'http://prematch.lsports.eu/OddService/GetSports';
    private $getLocationsURL = 'http://prematch.lsports.eu/OddService/GetLocations';
    private $getLeaguesURL = 'http://prematch.lsports.eu/OddService/GetLeagues';
    private $getBookmakersURL = 'http://prematch.lsports.eu/OddService/GetBookmakers';
    private $getMarketsURL = 'http://prematch.lsports.eu/OddService/GetMarkets';
    private $orderFixturesURL = 'http://api.lsports.eu/api/schedule/OrderFixtures';
    private $cancelOrderFixturesURL = 'http://inplay.lsports.eu/api/schedule/CancelFixtureOrders';
    /* private $enablePackageURL = 'http://api.lsports.eu/api/Package/EnablePackage';
      private $disablePackageURL = 'http://api.lsports.eu/api/Package/DisablePackage'; */
    private $enablePackageURL = 'http://inplay.lsports.eu/api/Package/EnablePackage';
    private $disablePackageURL = 'http://inplay.lsports.eu/api/Package/DisablePackage';
    private $disablePreMatchPackageURL = 'http://prematch.lsports.eu/OddService/DisablePackage';
    private $enablePreMatchPackageUrl = 'http://prematch.lsports.eu/OddService/EnablePackage';
    private $snapshotURL = 'http://api.lsports.eu/api/Snapshot';
    // 실시간
    private $getInPlayScheduleURL = 'https://inplay.lsports.eu/api/schedule/GetInPlaySchedule';
    private $getOrderFixtures = 'http://inplay.lsports.eu/api/schedule/OrderFixtures';
    private $cancelOrderFixtures = 'http://inplay.lsports.eu/api/schedule/CancelFixtureOrders';
    private $getViewOrderedFixtures = 'http://inplay.lsports.eu/api/schedule/GetOrderedFixtures';
    private $getShapshot = 'https://inplay.lsports.eu/api/Snapshot/GetSnapshotJson';
    private $type = 'STM';
    private $logger;
    private $httpUtil;
    
    // 네오덱스 개발
    /*private $dexApiKey = 'M09IK0KKKHAFDDFS7ASD82FZ';
    private $dexApiUrl = 'https://te-api.neo-dex.com';
    private $dexCode = 'test024';*/
    
    // 네오덱스 라이브
    private $dexApiKey = 'WGEWER24Y5U46IKFGFDSXVXA';
    private $dexApiUrl = 'https://api.neo-dex.com';
    private $dexCode = 'kwill';
    

    public function __construct($type, $logger) {
        $this->username = 'tombow3455@gmail.com';
        $this->type = $type;
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
        if ('STM' == $type) {
            $this->guid = '7d0ac41b-5b58-48ee-a339-9267b4d4ee81'; //'780f3bfc-fe60-4e41-a1bc-75e35b3a0277'
            $this->password = 'Erft426!';
            $this->packageID = 743;
            $this->inplay_packageID = 744;
        } else {
            $this->guid = 'a51eae0f-1c1d-453a-84eb-3219061e418d'; //'780f3bfc-fe60-4e41-a1bc-75e35b3a0277'
            $this->password = '43SDLK4sd3';
            $this->packageID = 3065;
            $this->inplay_packageID = 3066;
        }

        $this->lang = 'ko';
    }

    private function getConnectionString() {

        //return '?username=' . $this->username . '&password=' . $this->password . '&packageid=' . $this->packageID . '&guid=' . $this->guid; //.'&lang='.$this->lang;
        return '?username=' . $this->username . '&password=' . $this->password . '&guid=' . $this->guid; //.'&lang='.$this->lang;
    }

    private function getInplayConnectionString() {
        return '?username=' . $this->username . '&password=' . $this->password . '&packageid=' . $this->inplay_packageID . '&guid=' . $this->guid; //.'&lang='.$this->lang;
    }

    // API

    public function getFixtures($fromDate = '', $toDate = '', $indent = '', $sports = '', $location = '', $leagues = '', $fixtures = '') {
        if ('STM' == $this->type) {
            $url = $this->getFixturesSTMURL;
        } else {
            $url = $this->getFixturesURL . $this->getConnectionString();
        }
     

        if ($fromDate != '')
            $url = $url . '&fromDate=' . $fromDate;
        if ($toDate != '')
            $url = $url . '&toDate=' . $toDate;
        if ($indent != '')
            $url = $url . '&indent=' . $indent;
        if ($sports != '')
            $url = $url . '&sports=' . $sports;
        if ($location != '')
            $url = $url . '&location=' . $location;
        if ($leagues != '')
            $url = $url . '&leagues=' . $leagues;
        if ($fixtures != '')
            $url = $url . '&fixtures=' . $fixtures;

        $this->logger->debug($url);
        if ('STM' == $this->type) {
            //$body = 'UserName=' . $this->username . '&Password=' . $this->password . '&PackageId=' . $this->packageID;
            $body = array(
                'UserName' => $this->username,
                'Password' => $this->password,
                'PackageId' => $this->packageID
            );

            $header = array('Content-Type: application/json', 'charset=UTF-8');
            $result = $this->httpUtil->curl_post_head_data($url, $body,$header);
        } else {
            $result = $this->httpUtil->curl($url);
        }

        $result = json_decode($result);

        return $result;
    }

    public function getFixtureMarkets($fromDate = '', $toDate = '', $indent = '', $sports = '', $location = '', $leagues = '', $markets = '', $fixtures = '') {
        $url = $this->getFixtureMarketsURL . $this->getConnectionString();

        if ($fromDate != '')
            $url = $url . '&fromDate=' . $fromDate;
        if ($toDate != '')
            $url = $url . '&toDate=' . $toDate;
        if ($indent != '')
            $url = $url . '&indent=' . $indent;
        if ($sports != '')
            $url = $url . '&sports=' . $sports;
        if ($location != '')
            $url = $url . '&location=' . $location;
        if ($leagues != '')
            $url = $url . '&leagues=' . $leagues;
        if ($markets != '')
            $url = $url . '&markets=' . $markets;
        if ($fixtures != '')
            $url = $url . '&fixtures=' . $fixtures;

        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    public function getEvents($logger = '', $fromDate = '', $toDate = '', $indent = '', $sports = '', $location = '', $leagues = '', $markets = '', $fixtures = '') {
        // $logger->debug('getEvents : '.$fixtures);
        $url = $this->getEventsURL . $this->getConnectionString();

        if ($fromDate != '')
            $url = $url . '&fromDate=' . $fromDate;
        if ($toDate != '')
            $url = $url . '&toDate=' . $toDate;
        if ($indent != '')
            $url = $url . '&indent=' . $indent;
        if ($sports != '')
            $url = $url . '&sports=' . $sports;
        if ($location != '')
            $url = $url . '&location=' . $location;
        if ($leagues != '')
            $url = $url . '&leagues=' . $leagues;
        if ($markets != '')
            $url = $url . '&markets=' . $markets;
        if ($fixtures != '')
            $url = $url . '&fixtures=' . $fixtures;

        $logger->debug($url);
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        //return $result->Body;
        //$logger->error("getEvents  : ".$url);
        //echo $url;
        return $result;
    }

    public function getSports() {
        $url = $this->getSportsURL . $this->getConnectionString();
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    public function getLocations() {
        $url = $this->getLocationsURL . $this->getConnectionString();
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    public function getLocationsByEn() {
        $url = $this->getLocationsURL . '?username=' . $this->username . '&password=' . $this->password . '&packageid=' . $this->packageID . '&guid=' . $this->packageID;
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    public function getLeagues() {
        $url = $this->getLeaguesURL . $this->getConnectionString();
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    public function getBookmakers() {
        $url = $this->getBookmakersURL . $this->getConnectionString();
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    public function getMarkets() {
        $url = $this->getMarketsURL . $this->getConnectionString();
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result->Body;
    }

    // 아직 사용 용도 모르는 API

    public function getSnapshot($action = 'GetSnapshotJson') {
        echo ($this->snapshotURL . '/' . $action . $this->getConnectionString());
        $snapshot = $this->httpUtil->curl($this->snapshotURL . '/' . $action . $this->getConnectionString());
        return $snapshot;
    }

    public function orderEvent($events = []) {
        $orderResponse = $this->httpUtil->curl($this->orderFixturesURL . $this->getConnectionString() . '&fixtureIds=' . implode(',', $events));
        return $orderResponse;
    }

    public function packageControl($enable = true, $inplay = true) {
        if ($inplay) {
            $url = $enable ? $this->enablePackageURL : $this->disablePackageURL;
            $url .= $this->getInplayConnectionString();
        } else {
            $url = $enable ? $this->enablePreMatchPackageUrl : $this->disablePreMatchPackageURL;
            $url .= $this->getConnectionString();
        }

        return $this->httpUtil->curl($url);
    }

    public function cancelEventOrdering($events = []) {
        $CancelOrderResponse = $this->httpUtil->curl($this->cancelOrderFixturesURL . $this->getConnectionString() . '&fixtureIds=' . implode(',', $events));
        return $CancelOrderResponse;
    }
 
    // 실시간
    public function getInPlayScheduleURL() {
        $url = $this->getInPlayScheduleURL . $this->getInplayConnectionString();

        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        //print_r($result);
        return $result;
    }

    public function getOrderFixtures($fixtures = '') {
        $url = $this->getOrderFixtures . $this->getInplayConnectionString();

        if ($fixtures != '')
            $url = $url . '&fixtures=' . $fixtures;

        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result;
    }

    public function cancelOrderFixtures($fixtures = '') {
        $url = $this->cancelOrderFixtures . $this->getInplayConnectionString();

        if ($fixtures != '')
            $url = $url . '&fixtures=' . $fixtures;

        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result;
    }

    public function getViewOrderedFixtures($fixtures = '') {
        $url = $this->getViewOrderedFixtures . $this->getInplayConnectionString();

        if ($fixtures != '')
            $url = $url; //.'&fixtures='.$fixtures;

        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result;
    }

    public function getShapshot($fixtures, $logger) {
        $url = $this->getShapshot . $this->getInplayConnectionString();

        if ($fixtures != '')
            $url = $url . '&fixtures=' . $fixtures;

        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result;
    }

    // sms 액세스 토큰 받기 
    // POST 요청 보내기 -> http://www.millenapps.com/api/authvalidate.
    public function authvalidate() {
        $url = 'https://www.millenapps.com/api/authvalidate';
        $body = 'auth_username=test1&auth_password=12345678';
        $result = $this->httpUtil->curl_post($url, $body);
        $result = json_decode($result);
        return $result;
    }

    // sms 2.	입금 요청 제출 
    // POST 요청 보내기 -> http://www.millenapps.com/api/depositmanagement.
    public function depositmanagement($access_token, $name, $bankId, $accountNumber, $amount, $timestamp) {
        $url = 'https://www.millenapps.com/api/depositmanagement';
        $body = 'access_token=' . $access_token . '&name=' . $name . '&bankId=' . $bankId . '&accountNumber=' . $accountNumber . '&amount=' . $amount . '&timestamp=' . $timestamp;
        $result = $this->httpUtil->curl_post($url, $body);
        $result = json_decode($result);
        return $result;
    }

    // sms 3.	입금 요청 일치 확인(Get)
    // GET 요청 보내기 ->  http://www.millenapps.com/api/depositmanagement.
    public function depositmanagement_get($access_token, $referenceId = null, $datefrom = null, $dateto = null) {
        $url = 'https://www.millenapps.com/api/depositmanagement';

        $url .= '?access_token=' . $access_token;
        if (null != $referenceId) {
            $url .= '&referenceId=' . $referenceId;
        }
        if (null != $datefrom) {
            $url .= '&datefrom=' . $datefrom;
        }

        if (null != $dateto) {
            $url .= '&dateto=' . $dateto;
        }
        //$url.= '?access_token='.$access_token.'&referenceId='.$referenceId.'&datefrom='.$datefrom.'&dateto='.$dateto;
        $result = $this->httpUtil->curl($url);
        $result = json_decode($result);
        return $result;
    }
    
    // 네오덱스
    // 가입유무조회
    public function registed($account) {
        $url = $this->dexApiUrl.'/api/partner/account/check/registed';
        //$body = "api_key=$this->dexApiKey&userAccount=$account";
        $body = json_encode(array('api_key' => $this->dexApiKey, 'userAccount'=>$account, 'ref_Code'=>$this->dexCode));
        $result = $this->httpUtil->curl_post_head_data($url, $body, array('Content-Type: application/json', 'charset=UTF-8'));
        
        $result = json_decode($result);
        return $result;
    }
    
    // 회원가입
    public function signup($account, $password) {
        $url = $this->dexApiUrl.'/api/partner/account/signup';
        $body = json_encode(array('api_key' => $this->dexApiKey, 'userAccount'=>$account, 'userPassword'=>$password, 'userPasswordConfirm'=>$password, 'ref_Code'=>$this->dexCode));
        //$result = $this->httpUtil->curl_post_head($url, $body);
        $header = array('Content-Type: application/json', 'charset=UTF-8');
        $result = $this->httpUtil->curl_post_head_data($url, $body,$header);
        $result = json_decode($result);
        
        // 넘어온 토큰값 저장(가입성공시)
        if($result->result){
            $access_token = $result->access_token;
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $sql = "update member set access_token = '$access_token' where id='$account'";
            $memberMCHModel->db->query($sql);
        }
        return $result;
    }
    
    // 로그인
    public function login($account, $password) {
        $url = $this->dexApiUrl.'/api/partner/account/login';
        $body = json_encode(array('api_key' => $this->dexApiKey, 'userAccount'=>$account, 'userPassword'=>$password, 'ref_Code'=>$this->dexCode));
        //$result = $this->httpUtil->curl_post_head($url, $body);
        $header = array('Content-Type: application/json', 'charset=UTF-8');
        $result = $this->httpUtil->curl_post_head_data($url, $body,$header);
        $result = json_decode($result);
        
        // 로그인 성공
        if($result->result){
            $access_token = $result->access_token;
            $transaction_key = $result->transaction_key;
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $sql = "update member set access_token = '$access_token', transaction_key = '$transaction_key' where id='$account'";
            $memberMCHModel->db->query($sql);
        }
        
        return $result;
    }
    
    // 코인 -> 게임머니 변환
    public function withdraw($memberInfo, $token, $transaction_key, $withdraw_amount, $db_config, $n_reg_first_charge) {
        $url = $this->dexApiUrl.'/api/partner/gameplayer/withdraw';
        $body = json_encode(array('api_key' => $this->dexApiKey, 'transaction_key'=>$transaction_key, 'withdraw_amount'=>$withdraw_amount));
        $result = $this->httpUtil->curl_post_head_data($url, $body, array('Content-Type: application/json', 'charset=UTF-8', 'Authorization: Bearer '.$token));
        $result = json_decode($result);

        $a_comment = "충전완료";
        $ac_code = 1;
        $cash_use_kind = 'P';
        $str_set_type = '';
        
        try {
            // 변환성공
            // member_money_charge_history 데이터 갱신
            // t_log_cash 로그 추가
            if($result->result){
                //$withdraw_amount = $result->withdraw_amount;
                $memberMCHModel = new MemberMoneyChargeHistoryModel();
                                
                $memberMCHModel->db->transStart();
                
                $memberIdx = $memberInfo['member_idx'];
                $now_cash = $memberInfo['member_money'];       // 현재 유저머니
                $set_money = $withdraw_amount;  // 입금액
                $u_level = $memberInfo['level'];
                $is_exchange = $memberInfo['is_exchange'];
                $now_point = $memberInfo['member_point'];
                $af_point = 0;
                $ch_bonus = 0;
                $p_a_comment = '';
                $ch_point = 0;
                $p_ac_code = 0;
                $n_is_reg_first_charge = $memberInfo['reg_first_charge'];
                $n_is_charge_first_per = $memberInfo['charge_first_per'];
                if (0 == $n_is_reg_first_charge) { // 가입 첫충전 
                    $ch_point = ($n_reg_first_charge * $set_money) / 100;
                    if ($db_config[$u_level]['charge_max_money'] < $ch_point) {
                        $ch_point = $db_config[$u_level]['charge_max_money'];
                    }
                    $p_a_comment = '포인트 충전 : ' . 'reg_first_charge';
                    $str_set_type = 'reg_first_charge';
                } else if (0 == $n_is_charge_first_per) { // 매일 첫 충전
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

                $p_ac_code = 10;
                $af_point = $ch_point + $now_point;
                $af_cash = $now_cash + $set_money;

                // 머니충전
                if (0 == $n_is_reg_first_charge) {
                    $sql = "update member set reg_first_charge = 1, charge_first_per = 1, money = $af_cash, point = point + $ch_point where idx=$memberIdx ";
                } else if (0 == $n_is_charge_first_per) {
                    $sql = "update member set charge_first_per = 1,money = $af_cash, point = point + $ch_point where idx=$memberIdx ";
                } else {
                    $sql = "update member set money = $af_cash, point = point + $ch_point where idx=$memberIdx ";
                }
                //$sql = "update member set money = $af_cash where idx = $memberIdx";
                $memberMCHModel->db->query($sql);
                
                // 충전관리 테이블 업데이트
                $sql = "UPDATE member_money_charge_history 
                    LEFT JOIN member ON member_money_charge_history.member_idx = member.idx
                    SET member_money_charge_history.bonus_point = $ch_point, member_money_charge_history.set_type = '$str_set_type', member_money_charge_history.status=3, 
                        member_money_charge_history.update_dt=now(), member_money_charge_history.money = $withdraw_amount, member_money_charge_history.result_money = $af_cash 
                    WHERE member.idx = $memberIdx and bank_id = 1000 AND member_money_charge_history.status = 1";
                $memberMCHModel->db->query($sql);
                
                // 로그에는 두개합산값으로 저장한다.
                $a_comment = "코인충전완료";
                $ac_code = 303;
                $cash_use_kind = 'P';
                $admin_id = '';
                $set_money = $set_money + $ch_point;

                $admin_id = '';
                $sql = "insert into  t_log_cash ";
                $sql .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id,point) ";
                $sql .= " values($memberIdx, $ac_code, 0, $withdraw_amount, $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id',$ch_point)";
                $memberMCHModel->db->query($sql);
                if (0 < $ch_point) {
                    $sql = "insert into  t_log_cash ";
                    $sql .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                    $sql .= " values(" . $memberIdx . ", $p_ac_code, 0, " . $ch_point . " ";
                    $sql .= ", $now_point, $af_point, '" . strtoupper($cash_use_kind) . "','$p_a_comment','$admin_id')";
                    $memberMCHModel->db->query($sql);
                }
                
                $memberMCHModel->db->transComplete();
            }
        } catch (\mysqli_sql_exception $e) {
            $memberMCHModel->db->transRollback();
        }
        
        return $result;
    }
    
    // 잔액조회
    public function balance($token) {
        $url = $this->dexApiUrl.'/api/partner/gameplayer/balance';
        $body = json_encode(array('api_key' => $this->dexApiKey));
        $result = $this->httpUtil->curl_post_head_data($url, $body, array('Content-Type: application/json', 'charset=UTF-8', 'Authorization: Bearer '.$token));
        $result = json_decode($result);
        return $result;
    }
    
    // 트렌잭션 키 발급
    public function getTransactionKey($token) {
        $url = $this->dexApiUrl.'/api/partner/gameplayer/getTransactionKey';
        $body = json_encode(array('api_key' => $this->dexApiKey));
        $result = $this->httpUtil->curl_post_head_data($url, $body, array('Content-Type: application/json', 'charset=UTF-8', 'Authorization: Bearer '.$token));
        $result = json_decode($result);
        
        // 로그인 성공
        if($result->result){
            $transaction_key = $result->transaction_key;
            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            $sql = "update member set transaction_key = '$transaction_key' where id='$account'";
            $memberMCHModel->db->query($sql);
        }
        
        return $result;
    }

}

//$pullOperations = new PullOperations('manager1@cicarobet.com','rfsEDC438508','780f3bfc-fe60-4e41-a1bc-75e35b3a0277');
//        var_dump($pullOperations->getEvents());