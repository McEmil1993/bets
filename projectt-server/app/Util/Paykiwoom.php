<?php
namespace App\Util;
use App\Models\MemberMoneyChargeHistoryModel;
use CodeIgniter\Log\Logger;

class Paykiwoom {
    private $logger;
    private $httpUtil;
    
    private $ApiUrl = 'https://paykiwoom.com';
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
    }

    // 회원가입/충전페이지호출
    public function checkoutRequest($agencyurl, $username, $password, $amount, $sendername, $bankname, $accountnumber, $txnid) {
        $url = $this->ApiUrl.'/checkout/request';
        $body = array('agencyurl' => $agencyurl, 'username' => $username, 'password' => $password
                        , 'amount' => $amount, 'sendername'=>$sendername, 'bankname' => $bankname, 'accountnumber' => $accountnumber
                        , 'txnid'=>$txnid);
        $result = $this->httpUtil->curl_post($url, $body);
        //print_r($result);
        $result = json_decode($result);
        return $result;
    }
    
    // 입금내역 받아오기
    public function GetChargeNoti($type) {
        $url = $this->ApiUrl.'/api/money/GetChargeNoti';
        $body = array('type' => $type);
        $result = $this->httpUtil->curl_post($url, $body);
        $result = json_decode($result);
        return $result;
    }
    
    // 머니충전 완료 통보
    public function consume($type, $idx) {
        $url = $this->ApiUrl.'/api/money/consume';
        $body = array('type' => $type, 'idx' => $idx);
        $result = $this->httpUtil->curl_post($url, $body);
        $result = json_decode($result);
        return $result;
    }
}