<?php

include_once(_BASEPATH . '/GamblePatch/BaseGmPt.php');

class GambelGmPt extends BaseGmPt {
    // 충전시 적립
    public function giveGMoneyCharge($Model, $u_key, $member_idx, $set_money, $now_cash, $af_cash
    , $ch_point, $now_point, $af_point, $p_a_comment, $charge_idx) {

        $p_data['sql'] = "select set_type_val from t_game_config where set_type = 'gm_pt_charge_value' ";
     
        $resultData = $Model->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $resultData) {
            CommonUtil::logWrite("giveGMoneyCharge set_type_val ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $f_gm_pt_charge_value = $resultData[0]['set_type_val'];

        $g_money = $f_gm_pt_charge_value * $set_money;

        $p_data['sql'] = "select g_money from member where idx = $member_idx ";
        CommonUtil::logWrite("giveGMoneyCharge ==> " . $p_data['sql'], "info");
        
        $resultData = $Model->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $resultData) {
            CommonUtil::logWrite("giveGMoneyCharge g_money ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $bf_g_money = $resultData[0]['g_money'];

        $this->insertLog($u_key, $member_idx, 1, $charge_idx, $set_money, $now_cash, $af_cash, 'P', '머니 충전완료', $ch_point, $g_money, $Model);

        if (0 < $ch_point) {
            $this->insertLog($u_key, $member_idx, 10, $charge_idx, $ch_point, $now_point, $af_point, 'P', $p_a_comment, 0, 0, $Model);
        }

        if (0 < $g_money) {

            $p_data['sql'] = "update member set g_money = g_money + $g_money where idx = $member_idx ";
       
            if (FAIL_DB_SQL_EXCEPTION === $Model->setQueryData($p_data)) {
                CommonUtil::logWrite("giveGMoneyCharge setQueryData ", "error");
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }


            $p_data['sql'] = "update member_money_charge_history set bonus_money = $g_money where idx = $charge_idx ";
       
            if (FAIL_DB_SQL_EXCEPTION === $Model->setQueryData($p_data)) {
                CommonUtil::logWrite("giveGMoneyCharge setQueryData ", "error");
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }


            $af_g_money = $bf_g_money + $g_money;
            $this->insertLog($u_key, $member_idx, AC_CH_GM_TAKE, $charge_idx, $g_money, $bf_g_money, $af_g_money, 'P', 'G_Money 충전', 0, 0, $Model);
        }

        return $g_money;
    }

    public function useItemRefund($Model, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_money, $bf_money) {
      
        list($retval, $error, $item) = $this->checkItemIdx(GM_REFUND,$member_idx,$item_idx, $Model);
        if (false == $retval){
            CommonUtil::logWrite("useItemRefund false == retval ", "info");
            return 0;
        }

        $rollback_money = $total_bet_money * $item[0]['item_value'];
        $af_money = $bf_money + $rollback_money;
        $p_data['sql'] = "update member set money = money + $rollback_money where idx = $member_idx ";
        //$Model->db->query($sql, [$rollback_money, $member_idx]);
        if (FAIL_DB_SQL_EXCEPTION === $Model->setQueryData($p_data)) {
            CommonUtil::logWrite("useItemRefund setQueryData ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $this->insertLog($ukey, $member_idx, AC_GM_REFUND_MONEY, $bet_idx, $rollback_money, $bf_money, $af_money, 'P', '환급패치 아이템 사용 머니 충전', 0, 0, $Model);
     
        return $rollback_money;
    }
    
    public function useItemAllocation($Model, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_price) {
        list($retval, $error, $item) = $this->checkItemIdx(GM_ALLOCATION,$member_idx,$item_idx, $Model);
        if (false == $retval)
            return 0;

        $rollback_pirce = $total_bet_price * $item[0]['item_value'];

        $this->insertLog($ukey, $member_idx, AC_GM_ALLOCATION_PRICE, $bet_idx, $rollback_pirce, $total_bet_price, $total_bet_price + $rollback_pirce, 'P', '배당패치 아이템 사용 배당 증가', 0, 0, $Model);
        return $rollback_pirce;
    }

}
