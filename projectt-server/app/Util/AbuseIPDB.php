<?php
namespace App\Util;
use CodeIgniter\Log\Logger;
use App\Util\httpUtil;


class AbuseIPDB {
    private $logger;
    private $http_util;
    private $api_url = 'https://api.abuseipdb.com/api/v2';
    private $api_key = '89f057f8298ff1b6f00fa1730ca949c64b5a3b22c827f5d620e6f73d858c8e1edee41604aa10e6ca';


    /*
     * api document : https://api.abuseipdb.com/api/v2/report
     */
    public function __construct($logger) {
        //$this->api_url = $api_url;
        $this->logger = $logger;
        $this->http_util = new httpUtil();
    }
    
    /* 
     * {
        "data": {
          "ipAddress": "118.25.6.39",
          "isPublic": true,
          "ipVersion": 4,
          "isWhitelisted": false,
          "abuseConfidenceScore": 100,
          "countryCode": "CN",
          "countryName": "China",
          "usageType": "Data Center/Web Hosting/Transit",
          "isp": "Tencent Cloud Computing (Beijing) Co. Ltd",
          "domain": "tencent.com",
          "hostnames": [],
          "totalReports": 1,
          "numDistinctUsers": 1,
          "lastReportedAt": "2018-12-20T20:55:14+00:00",
          "reports": [
            {
              "reportedAt": "2018-12-20T20:55:14+00:00",
              "comment": "Dec 20 20:55:14 srv206 sshd[13937]: Invalid user oracle from 118.25.6.39",
              "categories": [
                18,
                22
              ],
              "reporterId": 1,
              "reporterCountryCode": "US",
              "reporterCountryName": "United States"
            }
          ]
        }
      }
     */
    public function checkEndPoint($ip){
        //$body = (array('maxAgeInDays'=> 90,'ip'=> urlencode($ip)));
        $body = "?ipAddress=$ip";
        $res_result = $this->http_util->curl_get_head_data($this->api_url.'/check', $body, array('Accept: application/json', 'Key: '.$this->api_key));
        $result = json_decode($res_result);
        return $result;
    }
}