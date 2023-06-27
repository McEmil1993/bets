<?php
namespace App\Util;
use App\Models\MemberMoneyChargeHistoryModel;
use CodeIgniter\Log\Logger;

class VirtualAccount {
    private $logger;
    private $httpUtil;
    
    private $ApiUrl = 'http://api.paywin.co.kr';
    //private $mid = 'paywkwin0m';
    

    public function __construct($logger) {
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
    }
       
    // 상품조회
    public function markerSellerInfo() {
        $url = $this->ApiUrl.'/api/markerSellerInfo';
        $body = json_encode(array('sellerId' => 'mir'));
        //$result = $this->httpUtil->curl_post_head($url, $body);
        $header = array('Content-Type: application/json', 'charset=UTF-8');
        $result = $this->httpUtil->curl_post_head_data($url, $body,$header);
        $result = json_decode($result);
        
        return $result;
    }
    
    // 가상계좌 발급
    public function markerBuyProduct($m_id, $m_name, $p_seq) {
        $mid = config(App::class)->mid; // 가상계좌 mid, $m_id는 유저 아이디
        $url = $this->ApiUrl.'/api/markerBuyProduct';
        $body = json_encode(array('m_id' => $m_id, 'm_name' => $m_name, 'p_seq' => $p_seq, 'mid' => $mid ));
        //$result = $this->httpUtil->curl_post_head($url, $body);
        $header = array('Content-Type: application/json', 'charset=UTF-8');
        $result = $this->httpUtil->curl_post_head_data($url, $body,$header);
        $result = json_decode($result);
        
        return $result;
    }

}