<?php

class DayChargeEvent {

    public function AddCharge($member_idx, $count, $money, $admin_id, $model) {
        try {

            //$model->trans_start();
            $sql = 'INSERT INTO `tb_member_day_charge_event` ('
                    . 'member_idx, '
                    . 'count, '
                    . 'tot_charge) VALUE '
                    . '(?,?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'count = count + VALUES(count),'
                    . 'tot_charge = tot_charge + VALUES(tot_charge)';
            $model->setQueryData_pre($sql, [$member_idx, $count, $money]);

            if (-1 == $count){
                //$model->commit();
                return;
            }

            $sql = 'select tb_member_day_charge_event.count,tb_member_day_charge_event.tot_charge,tb_member_day_charge_event.tot_exchange from tb_member_day_charge_event 
                    where tb_member_day_charge_event.member_idx = ? ;';

            $result = $model->getQueryData_pre($sql, [$member_idx]);

            if (!isset($result) || 0 == count($result)){
                //$model->commit();
                return;
            }


            $count = $result[0]['count'];
            $tot_charge = $result[0]['tot_charge'];
            $tot_exchange = $result[0]['tot_exchange'];
            if(0 < $tot_exchange){
                //$memberDayChargeEvent->db->transComplete();
                return;
            }
            
            $sql = 'select tb_static_day_charge_event.idx,tb_static_day_charge_event.reward,tb_static_day_charge_event.tg_count,tb_static_day_charge_event.tg_money from tb_static_day_charge_event 
            where ? >= tb_static_day_charge_event.tg_money and ? >= tb_static_day_charge_event.tg_count order by idx asc ;';

            $results = $model->getQueryData_pre($sql, [$tot_charge, $count]);

            if (!isset($results) || 0 == count($results)){
                //$model->commit();
                return;
            }

            //$this->logger->error(json_encode($results));

            foreach ($results as $result) {
                $reward_idx = $result['idx'];
                $reward = $result['reward'];
                
                $tg_count = $result['tg_count'];
                $tg_money = $result['tg_money'];

                $sql = 'select member_idx from tb_member_day_charge_event_reward_history 
                    where member_idx = ? and reward_idx = ?';

                $result_reward = $model->getQueryData_pre($sql, [$member_idx, $reward_idx]);

                if (true === isset($result_reward) && 0 < count($result_reward))
                    continue;

                $sql = 'select point from member
                    where idx = ? ';

                $result_point = $model->getQueryData_pre($sql, [$member_idx]);

                $before_point = $result_point[0]['point'];

                $sql = 'update member set point = point + ? where idx = ?';

                $model->setQueryData_pre($sql, [$reward, $member_idx]);

                $sql = 'insert into tb_member_day_charge_event_reward_history(member_idx,reward_idx) value(?,?)';
                $model->setQueryData_pre($sql, [$member_idx, $reward_idx]);

                $ukey = md5($member_idx . strtotime('now'));

              
                if ('GAMBLE' == SERVER) {
                    $a_comment = '겜블지원';
                } else if ('NOVA' == SERVER) {
                    $a_comment = '노바지원';
                } else if ('NOBLE' == SERVER) {
                    $a_comment = '노블지원';
                }
                else if ('BULLS' == SERVER) {
                    $a_comment = '황소지원';
                } 
                
                $a_comment .= '('.'충전'.$tg_count.'회 / ' . $tg_money.'만 이상)';
                
                $UTIL = new CommonUtil();
                $UTIL->log_point($model, $ukey, $member_idx, DAY_CHRGE_EVENT_REWARD_POINT, 0, $reward, $before_point, $admin_id, $a_comment, 'P');
                
            }
            
            //return true;
            //$model->commit();
        } catch (\mysqli_sql_exception $ex) {
            //$model->rollback();
            //return false;
        }
    }

    public function AddExchange($member_idx, $money,$model) {
        try {
            //$model->trans_start();
            $sql = 'INSERT INTO `tb_member_day_charge_event` ('
                    . 'member_idx, '
                    . 'tot_exchange) VALUE '
                    . '(?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'tot_exchange = tot_exchange + VALUES(tot_exchange)';
           $model->setQueryData_pre($sql, [$member_idx,$money]);
           //$model->commit();
        } catch (\mysqli_sql_exception $e) {
            //$this->logger->error(':::::::::::::::  DayChargeEvent AddExchange error : ' . $e->getMessage());
            //$this->logger->error(':::::::::::::::  DayChargeEvent AddExchange query : ' . $memberDayChargeEvent->getLastQuery());
            //$model->rollback();
        }
    }

}
