<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\TLogCashModel;
use App\Models\MemberMoneyChargeHistoryModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Models\TGameConfigModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;
use App\Util\DateTimeUtil;
use App\Execute\DayChargeEvent;

class UserPayBackController extends BaseController {

    use ResponseTrait;

    public function calPayBack() {

        $this->logger->error(':::::::::::::::  calPayBack start');
        $memberModel = new MemberModel();
        $tLogCashModel = new TLogCashModel();
        $sql = "select mb.money,mb.point,charge_event.pay_back_value,upb.* from tb_user_pay_back_info as upb
                 left join member as mb on upb.member_idx = mb.idx
                 left join charge_event on charge_event.level = mb.level for update
                 ";

        try {
            $memberModel->transStart();
            $result = $memberModel->db->query($sql, [])->getResultArray();

            foreach ($result as $value) {

                if (0 == $value['charge'] || $value['tot_bet_money'] < $value['charge']) {

                    $this->logger->error(':::::::::::::::  calPayBack error member_idx: ' . $value['member_idx']);
                    // 데이터를 이월시킨다.
                    $sql = "update tb_user_pay_back_info set last_charge = charge,last_exchange = exchange,last_tot_bet_money = tot_bet_money where member_idx = ?";
                    $memberModel->db->query($sql, [$value['member_idx']]);
                    continue;
                }

                $reward = ($value['charge'] - ($value['exchange'] + $value['money'])) * ($value['pay_back_value'] / 100);

                if ($reward <= 0) {
                    // 데이터 초기화를 해야한다.
                    
                    $extern_data = array('charge'=>$value['charge'],'exchange'=>$value['exchange'],'money'=>$value['money'],'tot_bet_money'=>$value['tot_bet_money'],'pay_back_value'=>$value['pay_back_value'] );
                    $a_comment = json_encode($extern_data);

                    $ukey = md5($value['member_idx'] . strtotime('now'));
                    $tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_PAY_BACK_REWARD_MYNUS_POINT, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'M', $a_comment);

                
                    $sql = "update tb_user_pay_back_info set charge = 0,exchange = 0,tot_bet_money = 0,last_charge = 0,last_exchange = 0,last_tot_bet_money = 0 where member_idx = ?";

                    $memberModel->db->query($sql, [$value['member_idx']]);
                    $this->logger->error(':::::::::::::::  calPayBack error reward: ' . $reward . ' data : ' . json_encode($value));
                    continue;
                }

                if (MAX_PAY_BACK_POINT < $reward) {
                    $reward = MAX_PAY_BACK_POINT;
                }

                $sql = "update member set point = point + ? where idx = ?";

                $memberModel->db->query($sql, [$reward, $value['member_idx']]);

                $sql = "update tb_user_pay_back_info set charge = 0,exchange = 0,tot_bet_money = 0 ,last_charge = 0,last_exchange = 0,last_tot_bet_money = 0 where member_idx = ?";

                $memberModel->db->query($sql, [$value['member_idx']]);

                //$a_comment = ' 페이백 포인트 지급 => ' . ' [' . $reward . '] '.'charge :'.$value['charge'].
                //        ' exchange:'.$value['exchange'].' money:'.$value['money'].' pay_back_value:'.$value['pay_back_value'];
                //$a_comment = ' 페이백 포인트 지급 => ' . ' [' . $reward . '] ';

                $charge = $value['charge'] - $value['last_charge'];
                $exchange = $value['exchange'] - $value['last_exchange'];
                //$a_comment = '[{(저번주 누적 미정산 입금액 : '. $value['last_charge'].'<br />'
                //.'+ 이번주 입금액 :'.$charge.')'.'<br />'
                //.' - '.'(저번주 출금액+이번주 출금액 :'.$value['exchange'].')}'.'<br />'
                //.' - (현재 보유머니:'.$value['money']. ')]=정산 포인트 : '.$reward;
                //$today = date("2022-06-21");
                $start_date = date("Y-m-d", strtotime("-7 day", time()));
                $month = (int) date("m", strtotime("-7 day", time()));

                $now_week = DateTimeUtil::getWeek($start_date); //date("Y-m-d") 입력도 가능합니다
                //echo $today."는 6월의".$now_week."번째 주 입니다.";

                $a_comment = '(' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(충전 ' . $value['charge'] . '원 / 환전 ' . $value['exchange'] . '원)';

               
                $ukey = md5($value['member_idx'] . strtotime('now'));
                $tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_PAY_BACK_REWARD_POINT, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'P', $a_comment);

                $this->logger->error(':::::::::::::::  calPayBack success reward: ' . $reward . ' data : ' . $a_comment);
            }
            
            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  calPayBack error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  calPayBack query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
        }
    }

    public function calDayChargeEvent() {
        $this->logger->error(':::::::::::::::  calDayChargeEvent start');
        $memberModel = new MemberModel();
        try {
            
            //$execute = new DayChargeEvent($this->logger);
            //$execute->checkReward();
            
            $memberModel->transStart();
            $sql = 'update tb_member_day_charge_event set count = 0, tot_charge = 0,tot_exchange = 0';
            $memberModel->db->query($sql);
            $sql = 'truncate tb_member_day_charge_event_reward_history';
            $memberModel->db->query($sql);
            $memberModel->db->transComplete();
            
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  calDayChargeEvent error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  calDayChargeEvent query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
        }
    }

}
