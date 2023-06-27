<?php

include_once(_LIBPATH . '/class_GameUtil.php');

class Admin_Mini_Game_DAO extends Database {

    public function __construct($select_db_name) {
        parent::__construct($select_db_name);
    }

    public function __destruct() {
        parent::__destruct();
    }

    public function dbconnect() {
        return $this->connect();
    }

    public function dbclose() {
        return $this->disconnect();
    }

    public function dbfree() {
        return $this->free();
    }

    public function ArrayCount($array) {
        if (isset($array))
            return count($array);
        return -1;
    }

    public function getQueryData($g_data) {
        $sql = $g_data['sql'];

        $recordset = $this->select_query($sql);

        return $recordset;
    }

    // 재정산 데이터 가져온다.
    public function getMiniGamelReCalculate($id, $bet_type) {
        $sql = "SELECT mini_game_member_bet.bet_status,mini_game_member_bet.calculate_dt,mini_game_member_bet.create_dt,mini_game_member_bet.idx, member_idx, bet_type, total_bet_money, take_money, take_point, bet_price, ls_fixture_id, ls_markets_id, ls_markets_name, money, point "
                . "FROM mini_game_member_bet LEFT JOIN member as mb ON mb.idx = mini_game_member_bet.member_idx where ls_fixture_id = $id and bet_type = $bet_type and bet_status <> 5 order by idx desc;";
        //CommonUtil::logWrite("getMiniGamelReCalculate : " . $sql, "info");
        return $this->select_query($sql);
    }

    public function UpdateMemberMiniGameBet($calculate_dt, $idx, $bet_status, $take_money) {
        if (false === isset($calculate_dt) || true === empty($calculate_dt)) {
            $sql = "UPDATE mini_game_member_bet SET bet_status = $bet_status,take_money = $take_money, calculate_dt = now() WHERE idx = $idx";
        } else {
            $sql = "UPDATE mini_game_member_bet SET bet_status = $bet_status,take_money = $take_money WHERE idx = $idx";
        }

        //CommonUtil::logWrite("UpdateMemberBet : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function SelectMemberMiniGameBet($idx) {
        $sql = "SELECT bet_detail.bet_type
               ,bet_detail.ls_fixture_id
               ,mb_bet.total_bet_money
               ,member.money
               ,mb_bet.bet_status,mb_bet.member_idx
               ,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date 
                FROM member_bet_detail as bet_detail
                LEFT JOIN member_bet as mb_bet ON bet_detail.bet_idx = mb_bet.idx
                LEFT JOIN member  ON member.idx = mb_bet.member_idx
                LEFT JOIN lsports_fixtures as fix on bet_detail.ls_fixture_id = fix.fixture_id AND bet_detail.bet_type = fix.bet_type
                WHERE bet_detail.bet_idx = $idx ";

        return $this->select_query($sql);
    }

    public function executeQuery($sql) {
        return $this->execute_query($sql);
    }

    public function setQueryData($g_data) {
        $sql = $g_data['sql'];

        $recordset = $this->execute_query($sql);

        return $recordset;
    }

}

?>