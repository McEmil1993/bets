<?php
namespace App\Util;
use CodeIgniter\Log\Logger;

class Casino {
    private $logger;
    private $httpUtil;
    private $ApiUrl = 'http://kplayone.com';
    private $AgToken = 'QmihHefXoTdFYK36ZKwb9iYaKwkLTrI5';
    private $AgCode = 'BON2506';
    private $SercetKey = '9FkMWPgI7BifjMvJZGnQab66QCAwftDq';
   
    public function __construct($url, $token, $code, $sercetkey, $logger) {
        $this->ApiUrl = $url;
        $this->AgToken = $token;
        $this->AgCode = $code;
        $this->SercetKey = $sercetkey;
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
    }
     
    // 인증
    public function authCasino($id, $name, $balance, $domain_url, $prd_id, $type, $is_mobile) {
        $url = $this->ApiUrl.'/auth';
        $body = json_encode(array('user' => array('id'=>$id, 'name'=>$name, 'balance'=>$balance, 'domain_url'=>$domain_url, 'language'=>'ko'),
                            'prd'=>array('id' => $prd_id, 'type' => $type, 'is_mobile' => $is_mobile, 'open_type' => 'r')));
        //$result = $this->httpUtil->curl_post_head_casino($url, $body, $this->AgCode, $this->AgToken);
        $result = $this->httpUtil->curl_post_head_data($url, $body, array('Content-Type: application/json', 'charset=UTF-8', 'ag-code:'.$this->AgCode, 'ag-token: '.$this->AgToken));
        $result = json_decode($result);
        
        return $result;
    }

}