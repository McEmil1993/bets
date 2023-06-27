<?php

namespace App\Controllers;
use App\Models\MemberModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Log\Logger;
use App\Util\CodeUtil;
use App\Util\accessLogRedis;

class AccessLogController extends BaseController {

    use ResponseTrait;
   
    public function index() {
        if (false == session()->has('member_idx')) {
            return;
        }

        $chkMobile = CodeUtil::rtn_mobile_chk();

        $current_page = isset($_POST['current_page']) ? $_POST['current_page'] : $_SERVER["PHP_SELF"];
        $viewRoot = "PC" == $chkMobile ? 'PC' : 'Mobile';

        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            $response['messages'] = '조회되는 유저가 없습니다.';
            return $this->fail($response);
        }
        
        // 전체 충환전
        $sql = "SELECT charge_total_money, exchange_total_money FROM total_member_cash WHERE member_idx = ?";
        $resultTotalCash = $memberModel->db->query($sql, [$memberIdx])->getResultArray();
        
        // 오늘의 충환전
        $start_dt = date('Y-m-d 00:00:00');
        $end_dt = date('Y-m-d 23:59:59');
        $sql = "SELECT ifnull(sum(money), 0) as money FROM member_money_charge_history WHERE member_idx = ? and status = 3 and update_dt >= ? and ? <= update_dt";
        $resultTodayCharge = $memberModel->db->query($sql, [$memberIdx, $start_dt, $end_dt])->getResultArray();
        
        $sql = "SELECT ifnull(sum(money), 0) as money FROM member_money_exchange_history WHERE member_idx = ? and status = 3 and update_dt >= ? and ? <= update_dt";
        $resultTodayExchange = $memberModel->db->query($sql, [$memberIdx, $start_dt, $end_dt])->getResultArray();
        
        $level = $member->getLevel();
        $id = $member->getId();
        $nickName = $member->getNickName();
        $cal = ($resultTotalCash[0]['charge_total_money'] + $resultTodayCharge[0]['money']) - ($resultTotalCash[0]['exchange_total_money'] + $resultTodayExchange[0]['money']);
        //$page = basename($_SERVER["PHP_SELF"]);
        $hostname=$_SERVER["HTTP_HOST"];
        $client_ip = CodeUtil::get_client_ip();
        $now_time = date("Y-m-d H:i:s");
        $arrRedisData = array(
            'level' => $level,
            'id' => $id,
            'nickName' => $nickName,
            'cal' => $cal,
            'access_type' => $viewRoot,
            'page' => $current_page,
            'path' => $_POST['path'],
            'hostname' => $hostname,
            'client_ip' => $client_ip,
            'now_time' => $now_time
        );
        
        //print_r($arrRedisData);($host, $post, $password, $database, $expire, $logger)
        //return;
        $accessLogRedis = new accessLogRedis(config(App::class)->redis_ip, config(App::class)->redis_port, 
                config(App::class)->redis_password, config(App::class)->redis_database, config(App::class)->redis_expire, $this->logger);
        if(!$accessLogRedis->connect()){
            $response['messages'] = '레디스 연결 실패.';
            return $this->fail($response);
        }
        
        // 일정갯수 이상이면 오래된 순으로 하나 삭제
        $count = $accessLogRedis->llen($memberIdx);
        if($count > 500){
            $accessLogRedis->lpop($memberIdx, 1);
        }
        
        $accessLogRedis->rpush($memberIdx,json_encode($arrRedisData));
        
        echo 'success';
    }

}
