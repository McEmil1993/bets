<?php
namespace App\Util;
use App\Models\MemberMoneyChargeHistoryModel;
use CodeIgniter\Log\Logger;

class memberCash {
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
    }
    
    // 당일 총입금액
    public function getTodayTotalDeposit($member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "select sum(money) as total_money, count(idx) as cnt from member_money_charge_history where member_idx in (?) "
                . "and update_dt >= ? and update_dt <= ? group by member_idx";
        $result = $memberMCHModel->db->query($sql,[$member_idx,$db_srch_s_date,$db_srch_e_date])->getResultArray();
        
        return $result;
    }
    
    // 당일 총출금액
    public function getTodayTotalWithdraw($member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "select sum(money) as total_money, count(idx) as cnt from member_money_exchange_history where member_idx in (?) "
                . "and update_dt >= ? and update_dt <= ? group by member_idx";
        $result = $memberMCHModel->db->query($sql,[$member_idx,$db_srch_s_date,$db_srch_e_date])->getResultArray();
        
        return $result;
    }
    
    // 당일 스포츠, 실시간 배팅
    public function getTodayTotalSports($member_idx, $bet_type) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "select sum(total_bet_money) as total_money, count(idx) as cnt from member_bet where member_idx in (?) "
                . "and bet_type = ? and create_dt >= ? and create_dt <= ? group by member_idx";
        $result = $memberMCHModel->db->query($sql,[$member_idx,$bet_type,$db_srch_s_date,$db_srch_e_date])->getResultArray();
        
        return $result;
    }
    
    // 당일 미니게임 배팅
    public function getTodayTotalMini($member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "select sum(total_bet_money) as total_money, count(idx) as cnt from mini_game_member_bet where member_idx in (?) "
                . "and create_dt >= ? and create_dt <= ? group by member_idx";
        $result = $memberMCHModel->db->query($sql,[$member_idx,$db_srch_s_date,$db_srch_e_date])->getResultArray();
        
        return $result;
    }
    
    // 당일 카지노 배팅
    public function getTodayTotalCasino($member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "select sum(BET_MNY) as total_money, count(CSN_BET_IDX) as cnt from KP_CSN_BET_HIST where MBR_IDX in (?) "
                . "and REG_DTM >= ? and REG_DTM <= ? group by MBR_IDX";
        $result = $memberMCHModel->db->query($sql,[$member_idx,$db_srch_s_date,$db_srch_e_date])->getResultArray();
        
        return $result;
    }
    
    // 당일 슬롯 배팅
    public function getTodayTotalSlot($member_idx) {
        $db_srch_s_date = date("Y-m-d 00:00:00");
        $db_srch_e_date = date("Y-m-d 23:59:59");
        
        $memberMCHModel = new MemberMoneyChargeHistoryModel();
        $sql = "select sum(BET_MNY) as total_money, count(SLOT_BET_IDX) as cnt from KP_SLOT_BET_HIST where MBR_IDX in (?) "
                . "and REG_DTM >= ? and REG_DTM <= ? group by MBR_IDX";
        $result = $memberMCHModel->db->query($sql,[$member_idx,$db_srch_s_date,$db_srch_e_date])->getResultArray();
        
        return $result;
    }
}