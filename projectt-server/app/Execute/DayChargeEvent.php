<?php

namespace App\Execute;

use CodeIgniter\Log\Logger;
use App\Models\MemberDayChargeEvent;
use App\Models\MemberDayChargeEventHistory;
use App\Models\StaticDayChargeEvent;
use App\Models\MemberModel;
use App\Models\TLogCashModel;

class DayChargeEvent {

    private $logger;

    public function __construct($logger) {
        $this->logger = $logger;
    }

    public function AddCharge($member_idx, $count, $money) {
         $this->logger->error(' DayChargeEvent::AddCharge start');
        $memberDayChargeEvent = new MemberDayChargeEvent;
        try {
            $memberDayChargeEvent->transStart();
            $sql = 'INSERT INTO `tb_member_day_charge_event` ('
                    . 'member_idx, '
                    . 'count, '
                    . 'tot_charge) VALUE '
                    . '(?,?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'count = count + VALUES(count),'
                    . 'tot_charge = tot_charge + VALUES(tot_charge)';
            $memberDayChargeEvent->db->query($sql, [$member_idx, $count, $money]);

            if (-1 == $count) {
                $memberDayChargeEvent->transComplete();
                return;
            }

            $this->checkReward($member_idx);

            $memberDayChargeEvent->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  DayChargeEvent AddCharge error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  DayChargeEvent AddCharge query : ' . $memberDayChargeEvent->getLastQuery());
            $memberDayChargeEvent->db->transRollback();
        }
    }

    public function AddExchange($member_idx, $money) {
        $memberDayChargeEvent = new MemberDayChargeEvent;
       
        try {
            $memberDayChargeEvent->transStart();
            $sql = 'INSERT INTO `tb_member_day_charge_event` ('
                    . 'member_idx, '
                    . 'tot_exchange) VALUE '
                    . '(?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'tot_exchange = tot_exchange + VALUES(tot_exchange)';
            $memberDayChargeEvent->db->query($sql, [$member_idx,$money]);
            $memberDayChargeEvent->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  DayChargeEvent AddExchange error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  DayChargeEvent AddExchange query : ' . $memberDayChargeEvent->getLastQuery());
            $memberDayChargeEvent->db->transRollback();
        }
    }

    public function checkReward($member_idx) {
        $this->logger->error('start checkReward');
         
        $memberDayChargeEvent = new MemberDayChargeEvent;
        $memberDayChargeEventHistory = new MemberDayChargeEventHistory;
        $memberModel = new MemberModel;
        try {
            $memberDayChargeEvent->transStart();
          
            $sql = 'select tb_member_day_charge_event.member_idx,tb_member_day_charge_event.count,tb_member_day_charge_event.tot_charge,tb_member_day_charge_event.tot_exchange 
                  ,member.point
                  from tb_member_day_charge_event
                  left join member on member.idx = tb_member_day_charge_event.member_idx
                  where member_idx = ?;';

            $member = $memberDayChargeEvent->db->query($sql, $member_idx)->getResultArray();

            $count = $member[0]['count'];
            $tot_charge = $member[0]['tot_charge'];
            $tot_exchange = $member[0]['tot_exchange'];
            $before_point = $member[0]['point'];

            if(0 < $tot_exchange){
                $this->logger->error('checkReward 0 < exchange');
                $memberDayChargeEvent->transComplete();
                return;
            }

            $sql = 'select tb_static_day_charge_event.idx,tb_static_day_charge_event.reward,tb_static_day_charge_event.tg_count,tb_static_day_charge_event.tg_money from tb_static_day_charge_event 
                where ? >= tb_static_day_charge_event.tg_money and ? >= tb_static_day_charge_event.tg_count order by idx asc ;';

            $results = $memberDayChargeEvent->db->query($sql, [$tot_charge, $count])->getResultArray();

            if (!isset($results) || 0 == count($results)) {
                $this->logger->error('checkReward results empty');
                $memberDayChargeEvent->transComplete();
                return;
            }

            $this->logger->error(json_encode($results));

            $tot_reward = 0;
            foreach ($results as $result) {
                $reward_idx = $result['idx'];
                $reward = $result['reward'];

                $tg_count = $result['tg_count'];
                $tg_money = $result['tg_money'];

                $sql = 'select member_idx from tb_member_day_charge_event_reward_history 
                    where member_idx = ? and reward_idx = ?';

                $result_reward = $memberDayChargeEventHistory->db->query($sql, [$member_idx, $reward_idx])->getResultArray();

                if (true === isset($result_reward) && 0 < count($result_reward))
                    continue;
                
                $insertData = [
                    'member_idx' => $member_idx,
                    'reward_idx' => $reward_idx
                ];
                
                $memberDayChargeEventHistory->insert($insertData);
                
                $tLogCashModel = new TLogCashModel();
                $a_comment = '(' . '충전' . $tg_count . '회 / ' . $tg_money . '만 이상)';
                $ukey = md5($member_idx . strtotime('now'));
                $tLogCashModel->insertCashLog_3($ukey, $member_idx, DAY_CHRGE_EVENT_REWARD_POINT, 0, 0, 0, $reward, $before_point, $before_point + $reward, 'P', $a_comment);

                $before_point = $before_point + $reward;
                $tot_reward = $tot_reward + $reward;
            }


            $sql = 'update member set point = point + ? where idx = ?';

            $memberModel->db->query($sql, [$tot_reward, $member_idx]);

            $memberDayChargeEvent->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  DayChargeEvent checkReward error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  DayChargeEvent checkReward query : ' . $memberDayChargeEvent->getLastQuery());
            //$this->logger->error(':::::::::::::::  DayChargeEvent AddCharge query : ' . $memberDayChargeEventHistory->getLastQuery());
            $this->logger->error(':::::::::::::::  DayChargeEvent checkReward query : ' . $memberModel->getLastQuery());
            $memberDayChargeEvent->db->transRollback();
        }
    }

}
