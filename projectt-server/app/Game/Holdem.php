<?php
namespace App\Game;
use CodeIgniter\Log\Logger;
use App\Util\httpUtil;


class Holdem {
    private $logger;
    private $http_util;
    private $api_url = '';
    //private $token = '';
    //private $scope = '';
    //private $client_id = '';
    //private $client_secret = '';

    private $authorization = '';
    private $username = '';
    private $password = '';


    /*
     * api document : http://champsholdem.com/champs/api_v1_doc
     */
    public function __construct($username,$password,$api_url,$logger) {

        $this->api_url = $api_url;
        //$this->client_id = $client_id;
        //$this->client_secret = $client_secret;
        //$this->scope = $scope;
        $this->username = $username;
        $this->password = $password;
        $this->logger = $logger;
        $this->http_util = new httpUtil();
    }
    

    public function setAuthorization($authorization){
        $this->authorization = $authorization;
    }
   

    /* response fail (http code not 200)
     {
        "detail": [
          {
            "loc": [
              "string",
              0
            ],
            "msg": "string",
            "type": "string"
          }
        ]
      }
    */
    
    /* response success (http code 200)
      {
        "access_token": "string",
        "token_type": "string"
      }
    */
    public function token(/*$grant_type*/){
        //$body = json_encode(array('grant_type'=>'','username'=>$this->username,'password'=>$this->password,'scope'=> $this->scope,'client_id'=> $this->client_id,'client_secret'=>$this->client_secret));
        $body = array('grant_type'=>'','username'=>$this->username,'password'=>$this->password);
        $res_result = $this->http_util->curl_post($this->api_url.'/token', $body);

        $result = json_decode($res_result);
        return $result;
    }
    
    /* response fail(common) (http code not 200)
     {
        "result": "error",
        "detail": "등록된 유저가 존재 하지 않습니다."
      }
    */
    
    /* response success (http code 200)
        {
            "result": "success",
            "credit": 10000,
            "access_token": "",
            "token_type": "bearer"
        }
    */
    public function start_session($username,$credit){
        $body = array('username'=>$username,'credit'=>$credit);
        
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/start_session', $body, array('Authorization: Bearer '.$this->authorization));

        $result = json_decode($res_result);
        return $result;
    }
   

    /* response success (http code 200)
        "string"
    */

    /*public function start_game($session_token){
        $res_result = $this->http_util->curl($this->api_url.'/start_game?session_token='.$session_token);
        $result = json_decode($res_result);
        return $result;
    }*/
    
    public function start_game($session_token){
        //$start_url = $this->api_url.'/start_game?session_token='.$session_token;
        $start_url = 'http://champspoker-manager.com/champs/api/start_game?session_token='.$session_token;
        return $start_url;
    }
    
    /* response success (http code 200)
        {
            "result": "success",
            "credit": 10000,
            "stack": 0
        }
    */
    public function end_session($username){
        $body = array('username'=>$username);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/end_session', $body, array('Authorization: Bearer '.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    /* response success (http code 200)
        {
            "result": "success",
            "detail": "가입을 축하 드립니다",
            "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhZG1pbl9wbGF5ZXIyIiwiaG9zdCI6IjEyNy4wLjAuMSIsImV4cCI6MTg0NDkwMDkyM30.AxndtOibn5VCd1wDglQc5GANbIdk494Cd7lH2sugVZA",
            "token_type": "bearer"
        }
    */
    public function register($id,$password,$nickname){
        $body = array('id'=>$id,'password'=>$password,'nickname'=>$nickname);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/register', $body, array(/*'Content-Type: application/x-www-form-urlencoded', 'accept: application/json', */'Authorization: Bearer '.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    /*
     * 콜백 등록
     * 각 라운드 시작 및 결과를 받기 위한 url 등록
     * {"result": "success"}
     */
    public function update_host($call_back_url){
        $body = array('host'=>$call_back_url);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/update_host', $body, array('Authorization: Bearer '.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    /*
     * 미발송 이벤트 가져오기
     * 콜백으로 문제가 생긴경우  발송못한 이벤트 조최및 삭제
     * {
            "result": "success",
            "total":  아이템 갯수
            "data": 데이타 배열
       }
     */
    public function history_event($username){
        $body = array('username'=>$username);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/history_event', $body, array('Authorization: Bearer '.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    /*
     * 유저 사용내역 조회
     * 콜백으로 문제가 생긴경우  발송못한 이벤트 조최및 삭제
     * {
        "result": "success",
        "last_credit": 7000, 현재 크레딧
        "last_stack": 2328, 테이블 머니
        "page": 1,          페이지 번호
        "page_size": 10,    페이지당 아이템 갯수
        "total": 4,         아이템 갯수
        "data":
     * }
     */
    public function history_session($username, $token, $page){
        $body = array('username'=>$username, 'token'=>$token, 'page'=>$page);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/history_session', $body, array('Authorization: Bearer '.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    /*
     * 활성 유저 크레딧 증감 및 차감
     * 연동된 유저의 크레딧 증감및 차감
     * {
        "result": "success",
        "last_credit": 7000, 현재 크레딧
        "last_stack": 2328, 테이블 머니
        "page": 1,          페이지 번호
        "page_size": 10,    페이지당 아이템 갯수
        "total": 4,         아이템 갯수
        "data":
     * }
     */
    public function update_session($username, $credit){
        $body = array('username'=>$username, 'credit'=>$credit);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/update_session', $body, array('Authorization: Bearer '.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    // 정산 배팅 조회 (총판별)
    public function doHoldemByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {
       $sql = "
       SELECT  PRT.idx                                                                       AS dis_idx,         /* 회원 IDX */
               PRT.id                                                                        AS dis_id,          /* 회원 ID */
               DATE(CBH.UPDATE_DT)                                                           AS cr_dt,             /* 일자 */
               IFNULL(SUM(CBH.BET_MONEY), 0)                                                 AS total_bet_money,   /* 배팅금 총계 */
               IFNULL(SUM(WIN_MONEY), 0)                                                     AS total_win_money,  /* 당첨금 총계 */
               IFNULL( IFNULL(SUM(CBH.BET_MONEY), 0)  -  IFNULL(SUM(WIN_MONEY), 0), 0)       AS total_lose_money   /* 차액 총계 */
       FROM member MB
               LEFT JOIN HOLDEM_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
               LEFT JOIN member AS PRT ON MB.dis_id = PRT.id
       WHERE CBH.UPDATE_DT>= '$db_srch_s_date'
       AND CBH.UPDATE_DT<= '$db_srch_e_date'
       AND MB.level != 9
       AND MB.u_business = 1 AND PRT.u_business IN (2, 3)" . $where_new;
       return $sql;
    }
    
   
}