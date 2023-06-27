<?php

include_once(_LIBPATH . '/class_GameUtil.php');

class Admin_LSports_Bet_DAO extends Database {

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

        //echo $sql ."<br>";
        //$sql_str = "[getQueryData] ".$sql;

        $recordset = $this->select_query($sql);
        if (false === isset($recordset) || empty($recordset)) {
            //CommonUtil::logWrite("getSportsFixturesCount : " . $sql, "db_error");
        }

        return $recordset;
    }

    public function getSportsFixturesCount($bet_type, $g_data, $startTime, $endTime, $flag = null) {

        $sql = "SELECT 
                        fix.`fixture_id`
                        FROM lsports_fixtures as fix ";
        $sql .= " LEFT JOIN lsports_leagues as league ON fix.fixture_league_id = league.id ";
        $sql .= " LEFT JOIN lsports_sports as sports ON fix.fixture_sport_id = sports.id ";

        $sql .= " LEFT JOIN lsports_participant as p1 ON fix.fixture_participants_1_id = p1.fp_id ";
        $sql .= " LEFT JOIN lsports_participant as p2 ON fix.fixture_participants_2_id = p2.fp_id ";

        $sql .= " LEFT JOIN lsports_bet as bet ON fix.fixture_id = bet.fixture_id ";
        $sql .= " LEFT JOIN lsports_markets as markets ON bet.markets_id = markets.id ";
        $sql .= " WHERE IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) BETWEEN '$startTime' AND '$endTime' AND fix.bet_type = $bet_type ";
        if (null != $flag) {
            $sql .= " AND fix.passivity_flag = '$flag' AND bet.passivity_flag = '$flag' ";
        }
        $sql .= "   AND bet.bet_type = $bet_type
                    AND league.is_use = 1 AND league.bet_type = $bet_type  AND sports.is_use = 1 AND sports.bet_type = $bet_type  
                    AND markets.bet_group = $bet_type  ";

        // AND league.is_use = 1 AND league.bet_type = $bet_type  AND sports.is_use = 1 AND sports.bet_type = $bet_type  
        $sql .= $g_data["sql_where"];
        $sql .= " GROUP BY fix.fixture_id ";
        $recordset = $this->select_query($sql);
        CommonUtil::logWrite("getSportsFixturesCount : " . $sql, "info");

        return !empty($recordset) ? count($recordset) : 0;
    }

    public function getSportsFixturesList($bet_type, $p_data, $startTime, $endTime, $bs_id, $flag = null) {
        $orderBy = " ORDER BY fixture_start_date DESC";
        if ($bet_type == 1 && $bs_id == 1)
            $orderBy = " ORDER BY fixture_start_date ASC";
        else if ($bet_type == 2 && $bs_id == 2)
            $orderBy = " ORDER BY fixture_start_date ASC";

        // 실시간은 사용여부 상관없이 로드
        //$is_use = '1';
        //if ($bet_type == 2)
        //    $is_use = '0,1,2';

        $start = ( $p_data['page'] - 1 ) * $p_data['num_per_page'];
        $sql = "SELECT 
                        fix.fixture_id,
                        fix.`fixture_sport_id`,
                        sports.name as fixture_sport_name,
                        fix.`fixture_location_id`,
                        location.name as `fixture_location_name`,
                        fix.`fixture_league_id`,
                        league.display_name as `fixture_league_name`,
                        IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as `fixture_start_date`,
                        fix.`fixture_status`,
                        fix.`display_status`,
                        fix.`fixture_participants_1_id`,
                        p1.team_name as `fixture_participants_1_name`,
                        fix.`fixture_participants_2_id`,
                        p2.team_name as `fixture_participants_2_name`,
                        p1.team_name + p2.team_name as full_name,
                        fix.admin_bet_status,
                        sports.display_name as sports_display_name,
                        IF ( fix.`m_live_results_p1` is not null,fix.`m_live_results_p1`,fix.`live_results_p1`) as live_results_p1,
                        IF ( fix.`m_live_results_p2` is not null,fix.`m_live_results_p2`,fix.`live_results_p2`) as live_results_p2,
                        p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                        p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                        league.display_name as league_display_name,
                        location.image_path as location_image_path,
                        location.name as location_name,
                        location.display_name as location_display_name,
                           (select sum(mb_bet.total_bet_money) as total_bet_money  from member_bet_detail as detail 
								LEFT JOIN member_bet as mb_bet ON detail.bet_idx = mb_bet.idx
								where  detail.ls_fixture_id = fix.fixture_id AND mb_bet.bet_status = 1 
                                                                    AND detail.bet_status = 1 AND detail.bet_type = $bet_type
								) as bet_id_total_bet_money,
                            (select sum(mb_bet.total_bet_money) as total_bet_money  from member_bet_detail as detail 
								LEFT JOIN member_bet as mb_bet ON detail.bet_idx = mb_bet.idx
								where detail.ls_fixture_id = fix.fixture_id 
                                                                  AND detail.bet_type = $bet_type
                                                                 -- AND mb_bet.bet_type = $bet_type
								) as total_bet_money
                        FROM lsports_fixtures as fix ";
        $sql .= " LEFT JOIN lsports_sports as sports ON fix.fixture_sport_id = sports.id ";
        $sql .= " LEFT JOIN lsports_participant as p1 ON fix.fixture_participants_1_id = p1.fp_id ";
        $sql .= " LEFT JOIN lsports_participant as p2 ON fix.fixture_participants_2_id = p2.fp_id ";
        $sql .= " LEFT JOIN lsports_leagues as league ON fix.fixture_league_id = league.id ";
        $sql .= " LEFT JOIN lsports_locations as location ON fix.fixture_location_id = location.id ";

        $sql .= " WHERE  fix.bet_type = $bet_type  AND IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) BETWEEN '$startTime' AND '$endTime' ";
        if (null != $flag) {
            $sql .= " AND fix.passivity_flag = '$flag'";
        }

        //$sql .= " AND league.is_use in ($is_use) AND sports.is_use = 1 
        $sql .= " AND sports.is_use = 1 AND league.is_use = 1 
                  AND league.bet_type = $bet_type AND sports.bet_type = $bet_type";

        $sql .= $p_data["sql_where"];
        $sql .= " GROUP BY fix.fixture_id";
        $sql .= $orderBy;
        $sql .= " LIMIT $start, " . $p_data['num_per_page'] . ";";
        CommonUtil::logWrite("getSportsFixturesList : " . $sql, "info");

        $recordset = $this->select_query($sql);
        return $recordset;
    }

    public function getDetailSportsFixturesList($fixture_id, $bet_type, $p_data) {

        $sql = "SELECT 
                        bet.markets_id,
                        markets.name as markets_name,
                        markets.main_book_maker,
                        markets.sub_book_maker,
                        markets.limit_bet_price,
                        markets.max_bet_price,
                        bet.providers_id,
                        booker.name as providers_name ,
                        bet.bet_id,
                        bet.bet_name,
                        IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                        bet.bet_status_passivity,
                        bet.bet_type,
                        bet.bet_line,
                        bet.bet_base_line,
                        bet.bet_settlement,
                        IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                        bet.bet_price_passivity,
                        bet.bet_price_hit,
                        bet.admin_bet_status,
                        bet.update_dt,
                        bet.result_p1,
                        bet.result_p2,
                        bet.result_2_p1,
                        bet.result_2_p2,
                      
                        (select sum(mb_bet.total_bet_money) as total_bet_money  from member_bet_detail as detail 
                                                            LEFT JOIN member_bet as mb_bet ON detail.bet_idx = mb_bet.idx
                                                            where   
                                                            detail.ls_fixture_id = bet.fixture_id 
                                                            AND detail.ls_markets_id = bet.markets_id 
                                                            AND detail.ls_markets_base_line = bet.bet_base_line 
							    AND detail.bet_name = bet.bet_name 
                                                            AND mb_bet.bet_status = 1 
                                                            AND detail.bet_status = 1 AND detail.bet_type = $bet_type 
                                                            ) as bet_id_total_bet_money,
                                                            
                         (select sum(mb_bet.total_bet_money) as total_bet_money  from member_bet_detail as detail 
                                                            LEFT JOIN member_bet as mb_bet ON detail.bet_idx = mb_bet.idx
                                                            where 
                                                            detail.ls_fixture_id = bet.fixture_id 
                                                            AND detail.ls_markets_id = bet.markets_id 
                                                            AND detail.ls_markets_base_line = bet.bet_base_line 
                                                            AND detail.bet_name = bet.bet_name 
                                                            AND detail.bet_type = $bet_type 
                                                            ) as total_bet_money,
                        fix.fixture_id,
                        fix.fixture_sport_id,
                        fix.fixture_location_id,
                        location.name as fixture_location_name,
                        fix.fixture_league_id,
                        fix.passivity_flag,
                        league.display_name as fixture_league_name,
                        IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                                           
                        fix.fixture_status,
                        IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status,
                        fix.fixture_participants_1_id,
                    
                        p1.team_name as fixture_participants_1_name,
                        fix.fixture_participants_2_id,
                        p2.team_name as fixture_participants_2_name,
                        p1.team_name + p2.team_name as full_name,
                        fix.livescore,
                        fix.admin_bet_status as fix_admin_bet_status,
                        sports.display_name as sports_display_name,
                        IF ( fix.m_live_results_p1 is not null,fix.m_live_results_p1,fix.live_results_p1) as live_results_p1,
                        IF ( fix.m_live_results_p2 is not null,fix.m_live_results_p2,fix.live_results_p2) as live_results_p2,
                        p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                        p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                        league.display_name as league_display_name,
                        location.image_path as location_image_path
                        FROM lsports_bet as bet ";
        $sql .= " LEFT JOIN lsports_fixtures as fix ON fix.fixture_id = bet.fixture_id ";
        $sql .= " LEFT JOIN lsports_markets as markets ON markets.id = bet.markets_id ";
        $sql .= " LEFT JOIN lsports_participant as p1 ON fix.fixture_participants_1_id = p1.fp_id ";
        $sql .= " LEFT JOIN lsports_participant as p2 ON fix.fixture_participants_2_id = p2.fp_id ";
        $sql .= " LEFT JOIN lsports_leagues as league ON fix.fixture_league_id = league.id ";
        $sql .= " LEFT JOIN lsports_locations as location ON fix.fixture_location_id = location.id ";
        $sql .= " LEFT JOIN lsports_sports as sports ON fix.fixture_sport_id = sports.id ";
        $sql .= " LEFT JOIN lsports_bookmaker as booker ON bet.providers_id = booker.id ";
        // $sql .= " WHERE   ";
        $sql .= " WHERE bet.bet_type = $bet_type AND fix.bet_type = $bet_type AND bet.fixture_id = $fixture_id AND markets.bet_group = $bet_type "
                //. " AND location.is_use = 1 AND league.is_use = 1 AND league.bet_type = $bet_type  AND sports.is_use = 1 AND sports.bet_type = $bet_type AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id group by bet.bet_id";
                . " AND location.is_use = 1 AND league.bet_type = $bet_type  AND sports.is_use = 1 AND sports.bet_type = $bet_type AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id group by bet.bet_id";
        $sql .= " ORDER BY total_bet_money desc ,bet.bet_base_line *1,bet.bet_name";

        CommonUtil::logWrite("getDetailSportsFixturesList : " . $sql, "info");
        if (1 == $bet_type) {
            $recordset = GameUtil::mergeRenewBetData2($this->select_query($sql));
        } else {
            //$recordset = GameUtil::mergeBetData($this->select_query($sql));
            $recordset = GameUtil::mergeRenewBetData2($this->select_query($sql));
        }


        CommonUtil::logWrite("getDetailSportsFixturesList : " . json_encode($recordset), "info");
        return $recordset;
    }

    public function getLeaguesList($s_id, $bet_type) {

        $sql = "SELECT id, name,display_name, location_id, sport_id, season, lang, input_refund_rate ";

        if ('' != $s_id && strlen($s_id) > 0) {
            $sql .= " FROM lsports_leagues WHERE  sport_id = $s_id AND is_use = 1 AND bet_type = $bet_type";
        } else {
            $sql .= " FROM lsports_leagues WHERE is_use = 1 AND bet_type = $bet_type";
        }
        $sql .= " ORDER BY id ASC";
        $recordset = $this->select_query($sql);

        return $recordset;
    }

    public function getSportsList($bet_type = 1) {
        $sql = "SELECT idx, id, name,display_name, lang, create_dt, update_dt, delete_dt, is_use, image_path, input_refund_rate ";
        $sql .= " FROM lsports_sports WHERE is_use = 1 and bet_type = $bet_type";
        $sql .= " ORDER BY idx ASC";
        $recordset = $this->select_query($sql);
        return $recordset;
    }

    public function getLocationsList() {
        $sql = "SELECT idx, id, name, name_en, lang ";
        $sql .= " FROM lsports_locations WHERE is_use = 1";
        $sql .= " ORDER BY name ASC";
        $recordset = $this->select_query($sql);
        return $recordset;
    }

    public function getTotalBetSumCNT($fixture_id, $bet_type) {

        $sql = "SELECT count(mb_bt.idx) as CNT,sum(mb_bt.total_bet_money) as SUM_MONEY from member_bet_detail as detail 
                    LEFT JOIN member_bet as mb_bt ON detail.bet_idx = mb_bt.idx
                    WHERE detail.ls_fixture_id = $fixture_id AND detail.bet_type = $bet_type ";
        // 정산후 상태값이 변함 mb_bt.bet_status = 1 AND detail.bet_status = 1 AND
        $recordset = $this->select_query($sql);
        //CommonUtil::logWrite("getTotalBetSumCNT : " . $sql, "info");
        return $recordset;
    }

    public function updateInplayAdminFixStatus($status) {
        $sql = "UPDATE lsports_fixtures SET admin_bet_status = '$status' WHERE bet_type = 2 ";
        //$this->execute_query($sql);

        if (FAIL_DB_SQL_EXCEPTION === $this->execute_query($sql)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }


        $sql = "UPDATE t_game_config SET set_type_val = '$status' WHERE set_type = 'inplay_status' ";
        //$this->execute_query($sql);
        if (FAIL_DB_SQL_EXCEPTION === $this->execute_query($sql)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        return;
    }

    public function updateAdminFixStatus($fixture_id, $bet_type, $status) {
        $sql = "UPDATE lsports_fixtures SET admin_bet_status = '$status' WHERE fixture_id = $fixture_id AND bet_type = $bet_type";
        //$this->execute_query($sql);
        $return_value = $this->execute_query($sql);
        CommonUtil::logWrite("updateAdminFixStatus1 return_value: " . $return_value, "error");

        if (FAIL_DB_SQL_EXCEPTION === $this->execute_query($sql)) {
            CommonUtil::logWrite("updateAdminFixStatus1 : " . $sql, "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $sql = "UPDATE lsports_bet SET admin_bet_status = '$status' WHERE fixture_id = $fixture_id AND bet_type = $bet_type";
        if (FAIL_DB_SQL_EXCEPTION === $this->execute_query($sql)) {
            CommonUtil::logWrite("updateAdminFixStatus2 : " . $sql, "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        //$this->execute_query($sql);
        return;
    }

    public function getFxSportsData($add_Sql) {
        $sql = "SELECT 
                 *
                FROM lsports_bet as lb
                LEFT JOIN lsports_markets as lm ON lm.id = lb.markets_id 
                WHERE 1=1";
        $sql .= $add_Sql;
        //CommonUtil::logWrite("getFxSportsData : " . $sql, "info");
        return $this->select_query($sql);
    }

    public function UpdateMemberBetDetail($idx, $bet_status, $result_score) {
        $sql = "UPDATE member_bet_detail SET bet_status = $bet_status ,result_score = '$result_score' WHERE idx = $idx";
        CommonUtil::logWrite("UpdateMemberBetDetail : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function getMarket_id($sports_id, $markets_name, $bet_type) {
        $sql = "SELECT id FROM lsports_markets WHERE sport_id = $sports_id AND bet_group = $bet_type AND (name = '$markets_name' OR display_name = '$markets_name')";
        CommonUtil::logWrite("getMarket_id : " . $sql, "info");
        $recordset = $this->select_query($sql);

        if (FAIL_DB_SQL_EXCEPTION === $recordset) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        if (false === isset($recordset) || 0 === count($recordset)) {
            return null;
        }

        return $recordset[0]['id'];
    }

    // 키값이 필요하다.
    public function UpdateScorelsportsBet($fixture_id, $bet_type, $markets_id, $result_score_1, $result_score_2, $result_2_p1, $result_2_p2) {
        $sql = "UPDATE lsports_bet SET admin_bet_status = 'OFF', result_p1 = $result_score_1, result_p2 = $result_score_2,result_2_p1 = $result_2_p1,result_2_p2 = $result_2_p2 "
                . "WHERE fixture_id = $fixture_id AND bet_type = $bet_type AND markets_id = $markets_id ";
        CommonUtil::logWrite("UpdateScorelsportsBet : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function UpdateScorelsportsBetEndSettleCmp($fixture_id, $bet_type, $markets_id, $bet_base_line, $result_score_1, $result_score_2, $result_2_p1, $result_2_p2) {
        if ('' === $bet_base_line || null === $bet_base_line) {
            $sql = "UPDATE lsports_bet SET result_p1 = $result_score_1, result_p2 = $result_score_2,result_2_p1 = $result_2_p1,result_2_p2 = $result_2_p2 "
                    . "WHERE fixture_id = $fixture_id AND bet_type = $bet_type AND  markets_id = $markets_id ";
        } else {
            $sql = "UPDATE lsports_bet SET result_p1 = $result_score_1, result_p2 = $result_score_2,result_2_p1 = $result_2_p1,result_2_p2 = $result_2_p2 "
                    . "WHERE fixture_id = $fixture_id AND bet_type = $bet_type AND  markets_id = $markets_id AND bet_base_line = '$bet_base_line' ";
        }

        //CommonUtil::logWrite("UpdateScorelsportsBetEndSettleCmp : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function UpdateScorelsportsFixtures($fx_id, $bet_type, $result_score_1, $result_score_2) {
        $sql = "UPDATE lsports_fixtures SET m_live_results_p1 = $result_score_1, m_live_results_p2 = $result_score_2 WHERE fixture_id = $fx_id AND bet_type = $bet_type";
        //CommonUtil::logWrite("UpdateScorelsportsFixtures : " . $sql, "info");
        return $this->execute_query($sql);
    }

    // current_day를 체크해야한다 bet_id는 중복될수있다 current_day까지 체크해야 정확한 값을 가져온다.
    public function SelectMemberBetDetail($bet_idx) {
        $sql = "SELECT member_bet_detail.ls_fixture_id,member_bet_detail.bet_status
                ,member_bet_detail.bet_price as detail_bet_price,member_bet_detail.passivity_hit_flag  
                FROM member_bet_detail 
                left join member_bet as mb_bet on mb_bet.idx = member_bet_detail.bet_idx
                where member_bet_detail.bet_idx = $bet_idx and mb_bet.bet_status = 1 for update ";
        CommonUtil::logWrite("SelectMemberBetDetail : " . $sql, "info");
        return $this->select_query($sql);
    }

    public function UpdateMemberBet($calculate_dt, $idx, $bet_status, $take_money, $ch_point_lose_self_per, $flag_bet_sum) {
        if (false === isset($calculate_dt) || true === empty($calculate_dt)) {
            $sql = "UPDATE member_bet SET bet_status = $bet_status,take_money = $take_money,take_point = $ch_point_lose_self_per,flag_bet_sum = '$flag_bet_sum', calculate_dt = now(), cancel_type = 1 WHERE idx = $idx";
        } else {
            $sql = "UPDATE member_bet SET bet_status = $bet_status,take_money = $take_money,take_point = $ch_point_lose_self_per,flag_bet_sum = '$flag_bet_sum', cancel_type = 1 WHERE idx = $idx";
        }


        //CommonUtil::logWrite("UpdateMemberBet lose start calculate_dt : " .empty($calculate_dt), 'info');

        CommonUtil::logWrite("UpdateMemberBet : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function UpdateMemberBetBonus($calculate_dt, $idx, $bet_status, $take_money, $bonus_price, $flag_bet_sum, $item_bonus_price) {
        if (false === isset($calculate_dt) || true === empty($calculate_dt)) {
            $sql = "UPDATE member_bet SET bet_status = $bet_status,take_money = $take_money,bonus_price = $bonus_price,flag_bet_sum = '$flag_bet_sum', calculate_dt = now(), cancel_type = 1,item_bonus_price = $item_bonus_price WHERE idx = $idx";
        } else {
            $sql = "UPDATE member_bet SET bet_status = $bet_status,take_money = $take_money,bonus_price = $bonus_price,flag_bet_sum = '$flag_bet_sum', cancel_type = 1,item_bonus_price = $item_bonus_price WHERE idx = $idx";
        }

        CommonUtil::logWrite("UpdateMemberBetBonus : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function SelectMemberBet($idx) {
        $sql = "SELECT bet_detail.bet_type,bet_detail.ls_fixture_id,mb_bet.total_bet_money,member.money,mb_bet.bet_status
                ,mb_bet.member_idx
                ,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date
                ,mb_bet.folder_type
                ,mb_bet.create_dt
                ,mb_bet.item_idx
                FROM member_bet_detail as bet_detail
                LEFT JOIN member_bet as mb_bet ON bet_detail.bet_idx = mb_bet.idx
                LEFT JOIN member  ON member.idx = mb_bet.member_idx
                LEFT JOIN lsports_fixtures as fix on bet_detail.ls_fixture_id = fix.fixture_id AND bet_detail.bet_type = fix.bet_type
                WHERE bet_detail.bet_idx = $idx ";
        CommonUtil::logWrite("SelectMemberBet : " . $sql, "info");
        return $this->select_query($sql);
    }

    // 개별적특시 데이터 처리 
    public function UpdateLsportsBetException($fixture_id, $bet_type, $markets_id, $bet_base_line, $bet_price) {
        $sql = "UPDATE lsports_bet SET bet_price_hit = $bet_price WHERE fixture_id = $fixture_id AND bet_type = $bet_type AND admin_bet_status = 'OFF'";
        if (0 < $markets_id) {
            $sql .= " AND markets_id = $markets_id";
        }
        if (true == isset($bet_base_line) && false == empty($bet_base_line)) {
            $sql .= " AND bet_base_line = '$bet_base_line'";
        }
        CommonUtil::logWrite("UpdateLsportsBetException : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function UpdateLsportsBetByDetailInfo($fixture_id, $bet_type, $markets_id, $bet_base_line, $status) {
        if ('' === $bet_base_line || null === $bet_base_line) {
            $sql = "UPDATE lsports_bet SET admin_bet_status = '$status' WHERE fixture_id = $fixture_id AND bet_type = $bet_type AND  markets_id = $markets_id ";
        } else {
            $sql = "UPDATE lsports_bet SET admin_bet_status = '$status' WHERE fixture_id = $fixture_id AND bet_type = $bet_type AND  markets_id = $markets_id AND bet_base_line = '$bet_base_line' ";
        }

        CommonUtil::logWrite("UpdateLsportsBetByDetailInfo : " . $sql, "info");
        return $this->execute_query($sql);
    }

    public function UpdateMemberBetDetailByMb_bt_idx($idx, $bet_status) {
        $sql = "UPDATE member_bet_detail SET bet_status = $bet_status WHERE bet_idx = $idx";
        return $this->execute_query($sql);
    }

    // 개별 재정산시 데이터 가져오는 함수
    public function getBetIndividualReCalculate($fixture_id, $bet_type, $markets_id, $bet_base_line) {

        if ('' === $bet_base_line || null === $bet_base_line) {
            $add_sql = " AND bet_detail.bet_status <> 5 AND bet_detail.ls_fixture_id = $fixture_id"
                    . " AND bet_detail.ls_markets_id = $markets_id "
                    . " AND mb_bt.bet_type = $bet_type AND bet.bet_type = $bet_type and bet.admin_bet_status = 'OFF' group by bet_detail.idx ";
        } else {
            $add_sql = " AND bet_detail.bet_status <> 5 AND bet_detail.ls_fixture_id = $fixture_id "
                    . " AND bet_detail.ls_markets_id = $markets_id AND bet_detail.ls_markets_base_line = '$bet_base_line' "
                    . " AND mb_bt.bet_type = $bet_type AND bet.bet_type = $bet_type and bet.admin_bet_status = 'OFF' group by bet_detail.idx ";
        }

        $sql = "SELECT 
                    bet_detail.idx,
                    bet_detail.bet_idx,
                    bet_detail.bet_price,
                    bet_detail.bet_status as mb_bt_dt_bet_status,
                    bet_detail.ls_markets_base_line,
                    bet_detail.ls_markets_id,
                    bet_detail.ls_fixture_id,

                    mb_bt.idx as mb_bt_idx,
                    mb_bt.bet_status,
                    mb_bt.take_money,
                    mb_bt.take_point,
                    mb_bt.recom_take_point,
                    mb_bt.member_idx,
                    mb_bt.bonus_price,
                    mb_bt.total_bet_money,
                    mb_bt.folder_type,
                    mb_bt.create_dt,
                    mb_bt.calculate_dt,
                    mb_bt.item_idx,
                    mb_bt.flag_bet_sum,
                    mb.money,
                    mb.point,
                    mb.recommend_member,
                  
                    bet.bet_type,
                    bet.bet_name,
                    IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as lb_bet_status,
                    bet.bet_line,
                    bet.bet_base_line,
                    bet.result_p1,
                    bet.result_p2,
                    bet.result_2_p1,
                    bet.result_2_p2,
                    bet.bet_settlement,
                    
                    fix.fixture_status,
                    fix.fixture_sport_id,
                    fix.fixture_participants_1_id,
                    fix.fixture_participants_2_id,

                    IF ( fix.`m_live_results_p1` is not null,fix.`m_live_results_p1`,fix.`live_results_p1`) as fx_results_p1,
                    IF ( fix.`m_live_results_p2` is not null,fix.`m_live_results_p2`,fix.`live_results_p2`) as fx_results_p2,

                    fix.fixture_sport_id, sports.name as fixture_sport_name,
                    fix.fixture_location_id, location.name as fixture_location_name,
                    fix.fixture_league_id, league.display_name as fixture_league_name,
                    p1.team_name as fixture_participants_1_name, p2.team_name as fixture_participants_2_name,
                    IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date
                    
                FROM member_bet_detail as bet_detail
                LEFT JOIN member_bet as mb_bt ON mb_bt.idx = bet_detail.bet_idx 
                LEFT JOIN member as mb ON mb.idx = mb_bt.member_idx
                LEFT JOIN lsports_bet as bet ON bet_detail.ls_fixture_id = bet.fixture_id
                 AND bet_detail.ls_markets_id = bet.markets_id
                 AND bet_detail.ls_markets_base_line = bet.bet_base_line
                 AND bet_detail.bet_name = bet.bet_name
                 AND bet_detail.bet_type = bet.bet_type
                LEFT JOIN lsports_fixtures as fix ON bet_detail.ls_fixture_id = fix.fixture_id 
                
                LEFT JOIN 	 lsports_sports as sports
                      ON	 sports.id = fix.fixture_sport_id 
		LEFT JOIN 	 lsports_leagues as league
                      ON	 fix.fixture_league_id = league.id 
                LEFT JOIN 	 lsports_locations as location
                      ON	 fix.fixture_location_id = location.id
                LEFT JOIN 	 lsports_participant as p1
		      ON	 fix.fixture_participants_1_id = p1.fp_id
		LEFT JOIN 	 lsports_participant as p2
		      ON	 fix.fixture_participants_2_id = p2.fp_id
                WHERE 1=1";
        $sql .= $add_sql;
        CommonUtil::logWrite("getBetIndividualReCalculate : " . $sql, "info");
        return $this->select_query($sql);
    }

    // 개별 재정산시 데이터 가져오는 함수
    public function getBetMemberList($bet_id, $bet_type) {

        $sql = "SELECT 
                    bet_detail.idx,
                    bet_detail.bet_idx,
                    bet_detail.bet_price,
                    bet_detail.bet_status as mb_bt_dt_bet_status,
                    bet_detail.ls_markets_base_line,
                    bet_detail.ls_markets_id,
                    bet_detail.ls_markets_name,
                    
                    mb_bt.idx as mb_bt_idx,
                    mb_bt.bet_status,
                    mb_bt.take_money,
                    mb_bt.take_point,
                    mb_bt.recom_take_point,
                    mb_bt.member_idx,
                    mb_bt.bonus_price,
                    mb_bt.total_bet_money,
                    mb_bt.folder_type,
                    mb_bt.create_dt,
                    mb_bt.calculate_dt,
                    
                    mb.id,
                    mb.nick_name,
                    mb.money,
                    mb.point,
                    mb.recommend_member,
                    bet.bet_name,
                    IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as lb_bet_status,
                    bet.bet_line,
                    bet.bet_base_line,
                    bet.result_p1,
                    bet.result_p2,
                    bet.result_2_p1,
                    bet.result_2_p2,
                    bet.bet_settlement,
                    fix.fixture_status,
                    fix.fixture_sport_id,
                    fix.fixture_participants_1_id,
                    fix.fixture_participants_2_id,
                    fix.fixture_sport_id, sports.name as fixture_sport_name,
                    fix.fixture_location_id, location.name as fixture_location_name,
                    fix.fixture_league_id, league.display_name as fixture_league_name,
                    p1.team_name as fixture_participants_1_name, p2.team_name as fixture_participants_2_name,
                    IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date
                FROM member_bet_detail as bet_detail
                LEFT JOIN member_bet as mb_bt ON mb_bt.idx = bet_detail.bet_idx 
                LEFT JOIN member as mb ON mb.idx = mb_bt.member_idx
                LEFT JOIN lsports_bet as bet ON bet_detail.ls_bet_id = bet.bet_id
                LEFT JOIN lsports_fixtures as fix ON bet_detail.ls_fixture_id = fix.fixture_id 
                
                LEFT JOIN 	 lsports_sports as sports
                     ON	 sports.id = fix.fixture_sport_id 
		LEFT JOIN 	 lsports_leagues as league
                      ON	 fix.fixture_league_id = league.id 
                LEFT JOIN 	 lsports_locations as location
                      ON	 fix.fixture_location_id = location.id
                        
		LEFT JOIN 	 lsports_participant as p1
		   ON	 fix.fixture_participants_1_id = p1.fp_id
		LEFT JOIN 	 lsports_participant as p2
		   ON	 fix.fixture_participants_2_id = p2.fp_id
                                           
                WHERE bet_detail.ls_bet_id = $bet_id AND bet_detail.bet_type = $bet_type 
                GROUP BY bet_detail.bet_idx";
        //CommonUtil::logWrite("getBetIndividualReCalculate : " . $sql, "info");
        return $this->select_query($sql);
    }

    public function getBetTotalReCalculate($fixture_id, $bet_type, $market_id) {
        if (0 == $market_id) {
            $add_sql = " AND bet_detail.bet_status <> 5 AND bet_detail.ls_fixture_id = $fixture_id"
                    . " AND mb_bt.bet_type = $bet_type AND bet.bet_type = $bet_type and bet.admin_bet_status = 'OFF' group by bet_detail.idx ";
        } else {
            $add_sql = " AND bet_detail.bet_status <> 5 AND bet_detail.ls_fixture_id = $fixture_id"
                    . " AND bet_detail.ls_markets_id = $market_id "
                    . " AND mb_bt.bet_type = $bet_type AND bet.bet_type = $bet_type and bet.admin_bet_status = 'OFF' group by bet_detail.idx ";
        }

        $sql = "SELECT 
                  bet_detail.idx,
                  bet_detail.bet_idx,
                  bet_detail.bet_price,
                  bet_detail.bet_status as mb_bt_dt_bet_status,
                  bet_detail.ls_markets_base_line,
                  bet_detail.ls_markets_id,
                  
                  mb_bt.idx as mb_bt_idx,
                  mb_bt.bet_status,
                  mb_bt.take_money,
                  mb_bt.take_point,
                  mb_bt.recom_take_point,
                  mb_bt.member_idx,
                  mb_bt.bonus_price,
                  mb_bt.total_bet_money,
                  mb_bt.folder_type,
                  mb_bt.create_dt,
                  mb_bt.calculate_dt,
                  mb_bt.item_idx,
                  mb_bt.flag_bet_sum,
                  mb.money,
                  mb.point,
                  mb.recommend_member,
                  bet.bet_type,
                  bet.bet_name,
                  IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as lb_bet_status,
                  bet.bet_line,
                  bet.bet_base_line,
                  bet.result_p1,
                  bet.result_p2,
                  bet.result_2_p1,
                  bet.result_2_p2,
                  bet.bet_settlement,
                  fix.fixture_status,
                  fix.fixture_sport_id,
                  fix.fixture_participants_1_id,
                  fix.fixture_participants_2_id,
                  fix.fixture_sport_id, sports.name as fixture_sport_name,
                  fix.fixture_location_id, location.name as fixture_location_name,
                  fix.fixture_league_id, league.display_name as fixture_league_name,
                  p1.team_name as fixture_participants_1_name, p2.team_name as fixture_participants_2_name,
                  IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date
                FROM member_bet_detail as bet_detail
                LEFT JOIN member_bet as mb_bt ON mb_bt.idx = bet_detail.bet_idx 
                LEFT JOIN member as mb ON mb.idx = mb_bt.member_idx
                LEFT JOIN lsports_bet as bet ON bet_detail.ls_bet_id = bet.bet_id
                LEFT JOIN lsports_fixtures as fix ON bet_detail.ls_fixture_id = fix.fixture_id 
                LEFT JOIN 	 lsports_sports as sports
                     ON	 sports.id = fix.fixture_sport_id 
		LEFT JOIN 	 lsports_leagues as league
                      ON	 fix.fixture_league_id = league.id 
                LEFT JOIN 	 lsports_locations as location
                      ON	 fix.fixture_location_id = location.id
                        
		LEFT JOIN 	 lsports_participant as p1
		   ON	 fix.fixture_participants_1_id = p1.fp_id
		LEFT JOIN 	 lsports_participant as p2
		   ON	 fix.fixture_participants_2_id = p2.fp_id
                WHERE 1=1";
        $sql .= $add_sql;
        CommonUtil::logWrite("getBetTotalReCalculate : " . $sql, "info");
        return $this->select_query($sql);
    }

    public function getDividendPolicy() {
        $sql = "SELECT a.rank, a.type, a.amount, a.create_dt, a.update_dt FROM dividend_policy AS a WHERE a.rank > 0 AND a.type > 0  ORDER BY a.rank, a.type";
        return $this->select_query($sql);
    }

    public function getDividendPolicyCount($rank) {
        $sql = "SELECT COUNT(*) AS cnt FROM dividend_policy WHERE rank = $rank";
        return $this->select_query($sql);
    }

    public function getLevelBetPolicy() {
        $sql = "SELECT a.level, a.bet_type, a.amount, a.create_dt, a.update_dt FROM level_bet_policy AS a ORDER BY a.level, a.bet_type";
        return $this->select_query($sql);
    }

    public function executeQuery($sql) {
        return $this->execute_query($sql);
    }

    public function setQueryData($g_data) {
        $sql = $g_data['sql'];

        $recordset = $this->execute_query($sql);

        //if(false === isset($recordset) || empty($recordset)){
        //CommonUtil::logWrite("getSportsFixturesCount : " . $sql, "db_error");
        //} 
        return $recordset;
    }

}

?>