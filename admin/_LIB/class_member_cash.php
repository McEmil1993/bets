<?php

class MemberCash {
    //private $id_pattern;

    public function __construct() {
    }
    
    // 당일 총입금액
    public function getTodayTotalDeposit($model_dao, $member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $p_data['sql'] = "select sum(money) as total_money, count(idx) as cnt from member_money_charge_history where member_idx in ($member_idx) "
                . "and update_dt >= '$db_srch_s_date' and update_dt <= '$db_srch_e_date' group by member_idx";
        $result = $model_dao->setQueryData($p_data);
        
        //CommonUtil::logWrite("log_cash sql : ".$p_data['sql'], "info");
        
        return $result;
    }
    
    // 당일 총출금액
    public function getTodayTotalWithdraw($model_dao, $member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $p_data['sql'] = "select sum(money) as total_money, count(idx) as cnt from member_money_exchange_history where member_idx in ($member_idx) "
                . "and update_dt >= '$db_srch_s_date' and update_dt <= '$db_srch_e_date' group by member_idx";
        $result = $model_dao->setQueryData($p_data);
        
        //CommonUtil::logWrite("log_cash sql : ".$p_data['sql'], "info");
        
        return $result;
    }
    
    // 당일 스포츠, 실시간 배팅
    public function getTodayTotalSports($model_dao, $member_idx, $bet_type) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $p_data['sql'] = "select sum(total_bet_money) as total_money, count(idx) as cnt from member_bet where member_idx in ($member_idx) "
                . "and bet_type = $bet_type and create_dt >= '$db_srch_s_date' and create_dt <= '$db_srch_e_date' group by member_idx";
        $result = $model_dao->setQueryData($p_data);
        
        //CommonUtil::logWrite("log_cash sql : ".$p_data['sql'], "info");
        
        return $result;
    }
    
    // 당일 미니게임 배팅
    public function getTodayTotalMini($model_dao, $member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $p_data['sql'] = "select sum(total_bet_money) as total_money, count(idx) as cnt from mini_game_member_bet where member_idx in ($member_idx) "
                            . "and create_dt >= '$db_srch_s_date' and create_dt <= '$db_srch_e_date' group by member_idx";
        $result = $model_dao->setQueryData($p_data);
        
        //CommonUtil::logWrite("log_cash sql : ".$p_data['sql'], "info");
        
        return $result;
    }

    // 당일 카지노 배팅
    public function getTodayTotalCasino($model_dao, $member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $p_data['sql'] = "select sum(BET_MNY) as total_money, count(CSN_BET_IDX) as cnt from KP_CSN_BET_HIST where MBR_IDX in ($member_idx) "
                            . "and REG_DTM >= '$db_srch_s_date' and REG_DTM <= '$db_srch_e_date' group by MBR_IDX";
        $result = $model_dao->setQueryData($p_data);
        
        //CommonUtil::logWrite("log_cash sql : ".$p_data['sql'], "info");
        
        return $result;
    }
    
    // 당일 슬롯 배팅
    public function getTodayTotalSlot($model_dao, $member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $p_data['sql'] = "select sum(BET_MNY) as total_money, count(SLOT_BET_IDX) as cnt from KP_SLOT_BET_HIST where MBR_IDX in ($member_idx) "
                            . "and REG_DTM >= '$db_srch_s_date' and REG_DTM <= '$db_srch_e_date' group by MBR_IDX";
        $result = $model_dao->setQueryData($p_data);
        
        //CommonUtil::logWrite("log_cash sql : ".$p_data['sql'], "info");
        
        return $result;
    }
}

?>
