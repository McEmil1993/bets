<?php
namespace App\Util;
use CodeIgniter\Log\Logger;
use GuzzleHttp\Client;

class Holdum {
    private $logger;
    private $http_util;
    private $api_url = '';
    private $token = '';
    private $scope = '';
    private $client_id = '';
    private $client_secret = '';
    private $authorization = 'Bearer mF_9.B5f-4.1JqM';
 
    public function __construct($client_id, $client_secret,$scope, $logger) {
        $this->api_url = 'http://champsholdem.com/champs/api';
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->scope = $scope;
        $this->logger = $logger;
        $this->http_util = new httpUtil();
    }
    
    public function token($grant_type,$username,$password){
        $body = array('grant_type'=>$grant_type,'username'=>$username,'password'=>$password,'scope'=> $this->scope,'client_id'=> $this->client_id,'client_secret'=>$this->client_secret);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/token', $body, array('Content-Type: application/x-www-form-urlencoded', 'charset=UTF-8', 'Authorization:'.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    public function start_session($username,$credit){
        $body = array('username'=>$username,'credit'=>$credit);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/start_session', $body, array('Content-Type: application/x-www-form-urlencoded', 'charset=UTF-8', 'Authorization:'.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    public function start_game($session_token){
        $body = array('session_token'=>$session_token);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/start_game', $body, array('Content-Type: application/x-www-form-urlencoded', 'charset=UTF-8', 'Authorization:'.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    public function end_session($username){
        $body = array('username'=>$username);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/end_game', $body, array('Content-Type: application/x-www-form-urlencoded', 'charset=UTF-8', 'Authorization:'.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
    public function register($id,$password,$nickname){
        $body = array('id'=>$id,'password'=>$password,'nickname'=>$nickname);
        $res_result = $this->http_util->curl_post_head_data($this->api_url.'/register', $body, array('Content-Type: application/x-www-form-urlencoded', 'charset=UTF-8', 'Authorization:'.$this->authorization));
        $result = json_decode($res_result);
        return $result;
    }
    
}