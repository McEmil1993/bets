<?php
namespace App\Util;
use App\Models\MemberMoneyChargeHistoryModel;
use CodeIgniter\Log\Logger;

class Payment {
    private $logger;
    private $httpUtil;
    
    private $ApiUrl = 'http://210.175.73.172:82';
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
    }

      
    // 가상계좌 발금(페이윈 신규)
    public function requestOrder($type, $member_idx, $order_name, $order_bank_id, $order_bank_code, $order_account_number, $order_hp, $money) {
        $url = $this->ApiUrl.'/api/money/ChargeRequest';
        $body = array('type' => $type, 'member_idx' => $member_idx, 'order_name' => $order_name, 'order_bank_id' => $order_bank_id, 'order_bank_code' => $order_bank_code
                                    , 'order_account_number' => $order_account_number, 'order_hp'=>$order_hp, 'money'=>$money);
        $result = $this->httpUtil->curl_post($url, $body);
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