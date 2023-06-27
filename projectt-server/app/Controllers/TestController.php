<?php

namespace App\Controllers;

//require_once '/application/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Models\GameModel;
use App\Models\MemberModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\PullOperations;
use CodeIgniter\Log\Logger;
use App\Models\MemberMoneyChargeHistoryModel;

class TestController extends BaseController {

    use ResponseTrait;

    public function index() {
        // local 에서 cron-tab 역할 하는 스크립트 페이지
        return view("test_crontab", []);
    }

    public function member() { //회원 배팅 / 입금 / 환전 신청
        // local 에서 cron-tab 역할 하는 스크립트 페이지
        return view("member_crontab", []);
    }

    public function rmq_test() {
        echo 'test1 test1' . PHP_EOL;


        $connection = new AMQPStreamConnection('prematch-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, false, 580);
        echo 'test2 test2' . PHP_EOL;
        $channel = $connection->channel();
        echo 'test3 test3' . PHP_EOL;
        $queue = '_3065_';

        $channel->basic_qos(0, 1000, false);
        echo 'test4 test4' . PHP_EOL;
        $callback = function($msg) {
            //file_put_contents('test.log', "\n===> NEW Message start \n", FILE_APPEND);
            //file_put_contents('test.log', print_r($msg->body, true), FILE_APPEND);
            //file_put_contents('test.log', "\n===> NEW Message end \n", FILE_APPEND);

            $fileName = '../writable/logs/test_rmq/' . 'test_rmq_' . date('Y-m-d_His', strtotime('Now')) . '.txt';
            $myFile = fopen($fileName, 'a+');
            fwrite($myFile, $msg->body);
            fclose($myFile);

            echo "=====> Received start\n";
            echo " [x] Received ", $msg->body, "\n";
            echo "=====> Received end\n";
        };
        echo 'test5 test5' . PHP_EOL;
        $channel->basic_consume($queue, 'consumer', false, true, false, false, $callback);
        echo 'test6 test6' . PHP_EOL;
        while (count($channel->callbacks)) {
            echo 'test7 test7' . PHP_EOL;
            $channel->wait();
            echo 'test8 test8' . PHP_EOL;
        }

        echo 'test9 test9' . PHP_EOL;
        $channel->close();
        $connection->close();
    }

    public function rmq_test_inplay() {
        echo 'test1 test1' . PHP_EOL;

        $connection = new AMQPStreamConnection('inplay-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, false, 580);
        echo 'test2 test2' . PHP_EOL;
        $channel = $connection->channel();
        echo 'test3 test3' . PHP_EOL;
        $queue = '_3066_';

        $channel->basic_qos(0, 1000, false);
        echo 'test4 test4' . PHP_EOL;
        $callback = function($msg) {
            //file_put_contents('test.log', "\n===> NEW Message start \n", FILE_APPEND);
            //file_put_contents('test.log', print_r($msg->body, true), FILE_APPEND);
            //file_put_contents('test.log', "\n===> NEW Message end \n", FILE_APPEND);

            $fileName = '../writable/logs/test_rmq/' . 'test_rmq_inplay' . date('Y-m-d_His', strtotime('Now')) . '.txt';
            $myFile = fopen($fileName, 'a+');
            fwrite($myFile, $msg->body);
            fclose($myFile);

            echo "=====> Received start\n";
            echo " [x] Received ", $msg->body, "\n";
            echo "=====> Received end\n";
        };
        echo 'test5 test5' . PHP_EOL;
        $channel->basic_consume($queue, 'consumer', false, true, false, false, $callback);
        echo 'test6 test6' . PHP_EOL;
        while (count($channel->callbacks)) {
            echo 'test7 test7' . PHP_EOL;
            $channel->wait();
            echo 'test8 test8' . PHP_EOL;
        }

        echo 'test9 test9' . PHP_EOL;
        $channel->close();
        $connection->close();
    }

    public function sms_test_authvalidate() {
        try {
            $pullOperations = new PullOperations('ODD',$this->logger);
            $result = $pullOperations->authvalidate();
            $this->logger->debug('sms_test_authvalidate : ' . json_encode($result));
            if('null' == $result || !isset($result)) return;
            if (false == $result->status) {
                $this->logger->error('sms_test_authvalidate fail : ' . json_encode($result));
                return;
            }

            $memberMCHModel = new MemberMoneyChargeHistoryModel();
            //$memberMCHModel->db->transStart();

            foreach ($result->bank_list as $bank) {
                $sql = "select * from member_money_charge_history 
                        where status = 1 AND bank_id = $bank->bank_id AND referenceId = '' ";
                $result_rq_chgs = $memberMCHModel->db->query($sql)->getResult();
                $this->logger->debug('sms_test_authvalidate  member_money_charge_history: sql : ' .$sql);
                  
                $this->logger->debug('sms_test_authvalidate  member_money_charge_history: ' . json_encode($result_rq_chgs));
                
                if(0 == count($result_rq_chgs) || !isset($result_rq_chgs)) continue;
                 
                foreach ($result_rq_chgs as $charge) {
                    $timestamp = strtotime($charge->create_dt);
                    $result_deposit = $pullOperations->depositmanagement($result->auth_access_token, $charge->deposit_name, $bank->bank_id, $charge->account_number, $charge->money, $timestamp);
                    $this->logger->debug('::::::::::::::: sms_test_authvalidate result_deposit ' . json_encode($result_deposit));
                    if('null' == $result_deposit || !isset($result_deposit)) continue;
                    if (true == $result_deposit->status) {
                        $sql = "update member_money_charge_history set referenceId = '$result_deposit->referenceId' where idx = $charge->idx";
                        $memberMCHModel->db->query($sql);
                    }
                }
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] sms_test_authvalidate (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')' . ' query : ' . $memberMCHModel->getLastQuery());
            //$lSportsBetModel->db->transRollback();
            return;
        } 
    }

    public function sms_test_authvalidate_get() {
        try {
            $pullOperations = new PullOperations('ODD',$this->logger);
            $result = $pullOperations->authvalidate();
            if('null' == $result || !isset($result)) return;
            $this->logger->debug('sms_test_authvalidate_get : ' . json_encode($result));

            if (false == $result->status) {
                $this->logger->error('sms_test_authvalidate_get fail : ' . json_encode($result));
                return;
            }

            $memberMCHModel = new MemberMoneyChargeHistoryModel();
           
            $result_deposit_get = $pullOperations->depositmanagement_get($result->auth_access_token, null, null, null);
            if('null' == $result_deposit_get || !isset($result_deposit_get)) return;
            
            $this->logger->debug('::::::::::::::: sms_test_authvalidate_get result_deposit_get ' . json_encode($result_deposit_get));

            if (false == $result_deposit_get->status)
                return;

            $a_comment = "충전완료";
            $ac_code = 1;
            $cash_use_kind = 'P';
            $get_config_str = "'charge_first_per', 'charge_per', 'charge_max_money', 'charge_money','reg_first_charge'";
            $sql = "select u_level, set_type, set_type_val from t_game_config ";
            $sql .= " where set_type in ($get_config_str) ";
            $retData = $memberMCHModel->db->query($sql)->getResultArray();
            $n_reg_first_charge = 0;
            $str_set_type = '';
            foreach ($retData as $row) {
                $db_level = $row['u_level'];
                $str_set_type = $row['set_type'];
                switch ($row['set_type']) {
                    case 'charge_first_per':
                        $db_config[$db_level]['charge_first_per'] = $row['set_type_val'];
                        break;
                    case 'charge_per':
                        $db_config[$db_level]['charge_per'] = $row['set_type_val'];
                        break;
                    case 'charge_max_money':
                        $db_config[$db_level]['charge_max_money'] = $row['set_type_val'];
                        break;
                    case 'charge_money':
                        $db_config[$db_level]['charge_money'] = $row['set_type_val'];
                        break;
                    case 'reg_first_charge':
                        $n_reg_first_charge = $row['set_type_val'];
                        break;
                }
            }

            $memberMCHModel->db->transStart();
            foreach ($result_deposit_get->json as $result_get) {
                if ('Match' == $result_get->Status) {
                    $sql = "select a.idx, a.level, a.money, b.money as set_money,a.point,a.reg_first_charge,a.charge_first_per from member a, member_money_charge_history b ";
                    $sql .= " where a.idx=b.member_idx and b.referenceId='$result_get->ReferenceId' ";
                    $retData = $memberMCHModel->db->query($sql)->getResultArray();

                    $m_idx = $retData[0]['idx'];
                    $now_cash = $retData[0]['money'];
                    $set_money = $retData[0]['set_money'];
                    $u_level = $retData[0]['level'];

                    $now_point = $retData[0]['point'];

                    $af_point = 0;
                    $ch_bonus = 0;
                    $p_a_comment = '';
                    $ch_point = 0;
                    $b_up_sql = false;
                    $p_ac_code = 0;
                 
                    $n_is_reg_first_charge = $retData[0]['reg_first_charge'];
                    $n_is_charge_first_per = $retData[0]['charge_first_per'];
                    if (0 === $n_is_reg_first_charge) { // 가입 첫충전 
                        $ch_point = ($n_reg_first_charge * $set_money) / 100;
                        $p_a_comment = '포인트 충전 : ' . 'reg_first_charge';
                    } else if (0 === $n_is_charge_first_per) { // 매일 첫 충전
                        $ch_point = ($db_config[$u_level]['charge_first_per'] * $set_money) / 100;
                        $p_a_comment = '포인트 충전 : ' . 'charge_first_per';
                    } else {
                        $ch_point = ($db_config[$u_level]['charge_per'] * $set_money) / 100;
                        $p_a_comment = '포인트 충전 : ' . 'charge_per';
                    }

                    $p_ac_code = 10;
                    $af_point = $ch_point + $now_point;
                    $af_cash = $now_cash + $set_money;
                    $b_up_sql = true;

                    if (($m_idx > 0) && $b_up_sql) {

                        if (0 == $n_is_reg_first_charge) {
                            $sql = "update member set reg_first_charge = 1,money = money + $set_money, point = point + $ch_point where idx=$m_idx ";
                        } else if (0 == $n_is_charge_first_per) {
                            $sql = "update member set charge_first_per = 1,money = money + $set_money, point = point + $ch_point where idx=$m_idx ";
                        } else {
                            $sql = "update member set money = money + $set_money, point = point + $ch_point where idx=$m_idx ";
                        }
                        $memberMCHModel->db->query($sql);


                        $sql = "update member_money_charge_history set bonus_point = $ch_point,set_type = '$str_set_type', status=3, update_dt=now(), result_money = $af_cash  where referenceId='$result_get->ReferenceId' ";
                        // 로그에는 두개합산값으로 저장한다.
                        $set_money = $set_money + $ch_point;

                        $memberMCHModel->db->query($sql);
                        $admin_id = '';
                        $sql = "insert into  t_log_cash ";
                        $sql .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                        $sql .= " values(" . $m_idx . ", $ac_code, 0, " . $set_money . " ";
                        $sql .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                        $memberMCHModel->db->query($sql);
                        if (0 < $ch_point) {
                            $sql = "insert into  t_log_cash ";
                            $sql .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                            $sql .= " values(" . $m_idx . ", $p_ac_code, 0, " . $ch_point . " ";
                            $sql .= ", $now_point, $af_point, '" . strtoupper($cash_use_kind) . "','$p_a_comment','$admin_id')";
                            $memberMCHModel->db->query($sql);
                        }
                    }
                } else if ('1HourLimit' == $result_get->Status) {
                    $sql = "update member_money_charge_history set sms_status = '$result_get->Status' where referenceId = '$result_get->ReferenceId'";
                    $memberMCHModel->db->query($sql);
                }
            }
            
           $memberMCHModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] sms_test_authvalidate_get (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')' . ' query : ' . $memberMCHModel->getLastQuery());
            $memberMCHModel->db->transRollback();
            return;
        }
    }
    
    public function phpinfo() {
        echo phpinfo();
    }

}
