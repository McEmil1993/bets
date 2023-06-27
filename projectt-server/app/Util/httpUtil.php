<?php
namespace App\Util;
use CodeIgniter\Log\Logger;

class httpUtil {
    public function __construct() {
    }
    
    public function curl($url/*, $isPost = false*/) { // GET
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    
    public function curl_get_head_data($url, $get_data, $header) { // GET
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, ($url . $get_data));            
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function curl_post($url, $body) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            //CURLOPT_HEADER => false, // don't return headers
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    
    public function curl_post_head_data($url, $post_data, $header) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
//print_r($header);
//print_r($post_data);
        // post_data
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        /*if (!is_null($header)) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }*/
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        //print_r($response);
        curl_close($ch);
        return $response;
    }
    
    // 2023-05-16, korea gaming get, post Ãß°¡
    public static function postData($url, $data, $header = '')
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);

            if ($header == '') {
                $header = array('Content-Type: application/json');
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            if (is_array($data)) {
                $data = json_encode($data);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, -1); //dns lookup caching 

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function getData($url, $header = '')
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);

            if ($header == '') {
                $header = array('Content-Type: application/json');
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, -1); //dns lookup caching 

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } catch (\Exception $e) {
            return $e;
        }
    }
}
?>