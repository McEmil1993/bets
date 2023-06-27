<?php
namespace App\Util;
use CodeIgniter\Log\Logger;

class GSPlay {
    private $logger;
    private $httpUtil;
    private $ApiUrl = '';
    private $AgToken = '';
    private $AgCode = '';
    private $SercetKey = '';
 
    public function __construct($url, $token, $code, $sercetkey, $logger) {
        $this->ApiUrl = $url;
        $this->AgToken = $token;
        $this->AgCode = $code;
        $this->SercetKey = $sercetkey;
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
    }
}