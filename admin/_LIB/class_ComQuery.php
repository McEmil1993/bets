<?php

/** @noinspection ALL */
include_once(_LIBPATH . '/class_GameStatusUtil.php');

class ComQuery {

    // 입출금 정산 쿼리 
    public static function doComChExQuery($db_srch_s_date, $db_srch_e_date, $where_new, $param_where_new) {
        // 충전 환전 -- "AND T1.dis_id ='$member_id'" ," AND 1 = 1"
        $param = array($db_srch_s_date, $db_srch_e_date);
        $param = array_merge($param, $param_where_new);
        array_push($param, $db_srch_s_date, $db_srch_e_date);
        $param = array_merge($param, $param_where_new);
        $sql = " SELECT 'ch' AS stype, T1.dis_id, 
                        parent.idx as dis_idx,
                        DATE(update_dt) AS up_dt,
                        SUM(member_money_charge_history.money) as s_money 
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history
                        ON 
                            member_money_charge_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE update_dt >= ? AND  update_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 " . $where_new;

        $sql .= " AND member_money_charge_history.status = 3";
        $sql .= " GROUP BY up_dt,T1.recommend_member";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'ex' AS stype, T1.dis_id, 
                        parent.idx as dis_idx,
                        DATE(update_dt) AS up_dt,
                        SUM(member_money_exchange_history.money) as s_money 
                            FROM member  AS T1
                        LEFT JOIN 
                            member_money_exchange_history
                        ON 
                            member_money_exchange_history.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx     
                        ";
        $sql .= " WHERE update_dt >= ? AND update_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 " . $where_new;
        ;
        $sql .= " AND member_money_exchange_history.status = 3";
        $sql .= " GROUP BY up_dt,T1.recommend_member";
        $sql .= " ;";
        return [$sql, $param];
    }

    // 입출금 정산 쿼리 재귀 하위의 하위까지 모두 가져온다 
    public static function doComChExQueryRecursive($db_srch_s_date, $db_srch_e_date, $where_new, $param_where_new) {
        // 충전 환전 -- "AND T1.dis_id ='$member_id'" ," AND 1 = 1"
        $param = array($db_srch_s_date, $db_srch_e_date);
        $param = array_merge($param, $param_where_new);
        array_push($param, $db_srch_s_date, $db_srch_e_date);
        $param = array_merge($param, $param_where_new);
        $sql = " SELECT 'ch' AS stype, T1.dis_id, 
                        parent.idx as dis_idx,
                        DATE(update_dt) AS up_dt,
                        SUM(member_money_charge_history.money) as s_money 
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history
                        ON 
                            member_money_charge_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE update_dt >= ? AND  update_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 " . $where_new;

        $sql .= " AND member_money_charge_history.status = 3";
        $sql .= " GROUP BY up_dt,T1.recommend_member";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'ex' AS stype, T1.dis_id, 
                        parent.idx as dis_idx,
                        DATE(update_dt) AS up_dt,
                        SUM(member_money_exchange_history.money) as s_money 
                            FROM member  AS T1
                        LEFT JOIN 
                            member_money_exchange_history
                        ON 
                            member_money_exchange_history.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx     
                        ";
        $sql .= " WHERE update_dt >= ? AND update_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 " . $where_new;
        ;
        $sql .= " AND member_money_exchange_history.status = 3";
        $sql .= " GROUP BY up_dt,T1.recommend_member";
        $sql .= " ;";
        return [$sql, $param];
    }

    // 충전횟수
    public static function getChargeMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT 
                        count(member_money_charge_history.idx) as cnt
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history
                        ON 
                            member_money_charge_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE update_dt >= '$db_srch_s_date' AND  update_dt <= '$db_srch_e_date' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 ";
        $sql .= " AND member_money_charge_history.status = 3 " . $where_new;
        return $sql;
    }

    // 환전횟수
    public static function getExchangeMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT 
                        count(member_money_exchange_history.idx) as cnt
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_exchange_history
                        ON 
                            member_money_exchange_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE update_dt >= '$db_srch_s_date' AND  update_dt <= '$db_srch_e_date' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 ";
        $sql .= " AND member_money_exchange_history.status = 3 " . $where_new;
        return $sql;
    }

    // 충전 최대 금액
    public static function getChargeMaxMoney($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT 
                        max(member_money_charge_history.money) as max_charge_money
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history
                        ON 
                            member_money_charge_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE update_dt >= '$db_srch_s_date' AND  update_dt <= '$db_srch_e_date' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 ";
        $sql .= " AND member_money_charge_history.status = 3 " . $where_new;
        return $sql;
    }

    // 환전 최대 금액
    public static function getExchangeMaxMoney($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT 
                        max(member_money_exchange_history.money) as max_exchange_money
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_exchange_history
                        ON 
                            member_money_exchange_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE update_dt >= '$db_srch_s_date' AND  update_dt <= '$db_srch_e_date' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 ";
        $sql .= " AND member_money_exchange_history.status = 3 " . $where_new;
        return $sql;
    }

    // 스포츠,실시간 배팅머니 
    public static function doSportsRealBetQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $query = " SELECT DATE(calculate_dt) AS cr_dt, T1.dis_id, 
                    parent.idx as dis_idx,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0) AS pre_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0) AS pre_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0) AS real_bet_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0) AS real_take_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0) AS real_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0) AS real_bet_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0) AS real_take_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0) AS real_sum_d
                         FROM member  AS T1
                        LEFT JOIN 
                            member_bet
                        ON 
                            member_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx
                        ";
        $query .= " WHERE calculate_dt >= ? AND calculate_dt <= ? ";
        $query .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 " . $where_new;
        $query .= " AND bet_status = 3 
                AND total_bet_money != take_money 
                        ";
        $query .= " GROUP BY cr_dt,dis_idx ";
        $query .= ";";
        return $query;
    }

    // 스포츠,실시간 배팅머니 
    public static function doSportsRealForderBetQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $query = " SELECT DATE(calculate_dt) AS cr_dt, T1.dis_id, 
                    parent.idx as dis_idx,
                                       
                                
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 1
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 1
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 1
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 1
                                THEN take_money ELSE 0 END), 0) AS pre_sum_s,
                    
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 2
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_2,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 2
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_2,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 2
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 2
                                THEN take_money ELSE 0 END), 0) AS pre_sum_2,
                                
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 3
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_3,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 3
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_3,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 3
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 3
                                THEN take_money ELSE 0 END), 0) AS pre_sum_3,
                    
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 4
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_4,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 4
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_4,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 4
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 4
                                THEN take_money ELSE 0 END), 0) AS pre_sum_4,
                    
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND 4 < bet_count
                                THEN total_bet_money ELSE 0 END), 0) AS pre_bet_sum_5_more,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND 4 < bet_count
                                THEN take_money ELSE 0 END), 0) AS pre_take_sum_5_more,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND 4 < bet_count
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND 4 < bet_count
                                THEN take_money ELSE 0 END), 0) AS pre_sum_5_more,
                                

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0) AS real_bet_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0) AS real_take_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0) AS real_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0) AS real_bet_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0) AS real_take_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0) AS real_sum_d,
                                
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND is_classic = 'ON'
                                THEN total_bet_money ELSE 0 END), 0) AS total_classic_bet_money,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND is_classic = 'ON'
                                THEN take_money ELSE 0 END), 0) AS total_classic_win_money,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND is_classic = 'ON'
                                THEN total_bet_money ELSE 0 END), 0)
                    - 	IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND is_classic = 'ON'
                                THEN take_money ELSE 0 END), 0) AS total_classic_lose_money


                         FROM member  AS T1
                        LEFT JOIN 
                            member_bet
                        ON 
                            member_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx
                        ";
        $query .= " WHERE calculate_dt >= ? AND calculate_dt <= ? ";
        $query .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 " . $where_new;
        $query .= " AND bet_status = 3 
                AND total_bet_money != take_money 
                        ";
        $query .= " GROUP BY cr_dt,dis_idx ";
        $query .= ";";
        return $query;
    }

    public static function doMiniBetQuery($db_srch_s_date, $db_srch_e_date, $excluded_member_idx, $where_new) {

        $sql = " SELECT DATE(calculate_dt) AS cr_dt, 
                        parent.idx as dis_idx,
                        IFNULL(SUM(total_bet_money), 0) as mini_bet_sum_d,
                        IFNULL(SUM(take_money), 0) as mini_take_sum_d,
                        IFNULL(SUM(total_bet_money) - SUM(take_money), 0) as mini_sum_d
                         FROM member  AS T1
                        LEFT JOIN 
                            mini_game_member_bet
                        ON 
                            mini_game_member_bet.member_idx = T1.idx
                         LEFT JOIN member as parent ON T1.recommend_member = parent.idx";

        $sql .= " WHERE calculate_dt >= ? AND calculate_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1 AND bet_status = 3 AND total_bet_money != take_money  " . $where_new;
        $sql .= " AND (CASE WHEN bet_type = 6 THEN T1.idx <> ? 
                   ELSE 1 = 1 END)";
        $sql .= " GROUP BY cr_dt,dis_idx";
        $sql .= " ; ";

        return $sql;
    }

    //  카지노 배팅 조회 (회원별)
    public static function doCasinoByUserQuery($db_srch_s_date, $db_srch_e_date, $member_id) {

        $where_new = " AND 1 = 1";
        if ('' != $member_id) {
            $where_new = " AND MB.id ='$member_id'";
        }

        $sql = "
            SELECT MB.idx                                                                       AS user_idx,          /* 회원 IDX */
                    MB.id                                                                        AS user_id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                            AS cr_dt,            /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        $sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  카지노 배팅 조회 (총판별)
    public static function doCasinoByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        //$where_new = " AND 1 = 1";
        //if ('' != $member_id) {
        //    $where_new = " AND PRT.id ='$member_id'";
        //}

        $sql = "
            SELECT PRT.idx                                                                       AS dis_idx,          /* 회원 IDX */
                    PRT.id                                                                       AS dis_id,           /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                           AS cr_dt,             /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1
            AND PRT.u_business <> 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  슬롯 배팅 조회 (회원별)
    public static function doSlotByUserQuery($db_srch_s_date, $db_srch_e_date, $member_id) {

        $where_new = " AND 1 = 1";
        if ('' != $member_id) {
            $where_new = " AND MB.id ='$member_id'";
        }

        $sql = "
            SELECT MB.idx                                                                        AS user_idx,         /* 회원 IDX */
                    MB.id                                                                        AS user_id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                            AS cr_dt,            /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'L' THEN CBH.RSLT_MNY END), 0)               AS total_lose_money  /* 낙첨금 총계 */
            FROM member MB
                    LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        $sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

    //  슬롯 배팅 조회 (총판별)
    public static function doSlotByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {
        //$where_new = " AND 1 = 1";
        //if ('' != $member_id) {
        //    $where_new = " AND PRT.id ='$member_id'";
        //}

        $sql = "
            SELECT  PRT.idx                                                                      AS dis_idx,         /* 회원 IDX */
                    PRT.id                                                                       AS dis_id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                            AS cr_dt,             /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1 AND PRT.u_business <> 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

    //  이스포츠 배팅 조회 (회원별)
    public static function doEsptByUserQuery($db_srch_s_date, $db_srch_e_date, $member_id) {

        $where_new = " AND 1 = 1";
        if ('' != $member_id) {
            $where_new = " AND MB.id ='$member_id'";
        }

        $sql = "
           SELECT MB.idx                                                                        AS user_idx,         /* 회원 IDX */
                   MB.id                                                                        AS user_id,          /* 회원 ID */
                   DATE(CBH.MOD_DTM)                                                            AS cr_dt,            /* 일자 */
                   IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                   IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                   IFNULL(SUM(CASE WHEN CBH.TYPE = 'L' THEN CBH.RSLT_MNY END), 0)               AS total_lose_money  /* 낙첨금 총계 */
           FROM member MB
                   LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
           WHERE CBH.MOD_DTM>= '$db_srch_s_date'
           AND CBH.MOD_DTM<= '$db_srch_e_date'
           AND MB.level != 9
           AND CBH.TYPE IN ('W', 'L')
           AND MB.u_business = 1" . $where_new;
        $sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

    //  이스포츠 배팅 조회 (총판별)
    public static function doEsptByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {
        //$where_new = " AND 1 = 1";
        //if ('' != $member_id) {
        //    $where_new = " AND PRT.id ='$member_id'";
        //}

        $sql = "
           SELECT  PRT.idx                                                                      AS dis_idx,         /* 회원 IDX */
                   PRT.id                                                                       AS dis_id,          /* 회원 ID */
                   DATE(CBH.MOD_DTM)                                                            AS cr_dt,             /* 일자 */
                   IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                   IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                   IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
           FROM member MB
                   LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                   LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
           WHERE CBH.MOD_DTM>= '$db_srch_s_date'
           AND CBH.MOD_DTM<= '$db_srch_e_date'
           AND MB.level != 9
           AND CBH.TYPE IN ('W', 'L')
           AND MB.u_business = 1 AND PRT.u_business <> 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

    //  해시 배팅 조회 (회원별)
    public static function doHashByUserQuery($db_srch_s_date, $db_srch_e_date, $member_id) {

        $where_new = " AND 1 = 1";
        if ('' != $member_id) {
            $where_new = " AND MB.id ='$member_id'";
        }

        $sql = "
       SELECT MB.idx                                                                        AS user_idx,         /* 회원 IDX */
               MB.id                                                                        AS user_id,          /* 회원 ID */
               DATE(CBH.MOD_DTM)                                                            AS cr_dt,            /* 일자 */
               IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
               IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
               IFNULL(SUM(CASE WHEN CBH.TYPE = 'L' THEN CBH.RSLT_MNY END), 0)               AS total_lose_money  /* 낙첨금 총계 */
       FROM member MB
               LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
       WHERE CBH.MOD_DTM>= '$db_srch_s_date'
       AND CBH.MOD_DTM<= '$db_srch_e_date'
       AND MB.level != 9
       AND CBH.TYPE IN ('W', 'L')
       AND MB.u_business = 1" . $where_new;
        $sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

//  해시 배팅 조회 (총판별)
    public static function doHashByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {
        //$where_new = " AND 1 = 1";
        //if ('' != $member_id) {
        //    $where_new = " AND PRT.id ='$member_id'";
        //}

        $sql = "
       SELECT  PRT.idx                                                                      AS dis_idx,         /* 회원 IDX */
               PRT.id                                                                       AS dis_id,          /* 회원 ID */
               DATE(CBH.MOD_DTM)                                                            AS cr_dt,             /* 일자 */
               IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
               IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
               IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
       FROM member MB
               LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
               LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
       WHERE CBH.MOD_DTM>= '$db_srch_s_date'
       AND CBH.MOD_DTM<= '$db_srch_e_date'
       AND MB.level != 9
       AND CBH.TYPE IN ('W', 'L')
       AND MB.u_business = 1 AND PRT.u_business <> 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }
    
    // 홀덤 배팅 조회(총판별)
    public static function doHoldemByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = "
       SELECT  PRT.idx                                                                      AS dis_idx,         /* 회원 IDX */
               PRT.id                                                                       AS dis_id,          /* 회원 ID */
               DATE(CBH.REG_DTM)                                                            AS cr_dt,             /* 일자 */
               IFNULL(SUM(CBH.BET_MONEY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
               IFNULL(SUM(CBH.WIN_MONEY), 0) AS total_win_money,   /* 당첨금 총계 */
               IFNULL( IFNULL(SUM(CBH.BET_MONEY), 0)  -  IFNULL(SUM(CBH.WIN_MONEY), 0), 0)               AS total_lose_money   /* 차액 총계 */
       FROM member MB
               LEFT JOIN HOLDEM_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
               LEFT JOIN member AS PRT ON MB.dis_id = PRT.id
       WHERE CBH.REG_DTM>= '$db_srch_s_date'
       AND CBH.REG_DTM<= '$db_srch_e_date'
       AND MB.level != 9
       AND MB.u_business = 1 AND PRT.u_business <> 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.REG_DTM)";
        return $sql;
    }

    public static function doShopResultChExDist($db_srch_s_date, $db_srch_e_date, $add_where) {
        $sql = "select  IFNULL(SUM(ch_val), 0) as ch_val_sum,IFNULL(SUM(ex_val), 0) as ex_val_sum,IFNULL(SUM(ch_val) - SUM(ex_val),0) as gab_sum,  member_idx,calculate_dt,
                 IFNULL(SUM(calculate_point ), 0) as total_cal_point,
                 IFNULL(SUM(low_calculate_point), 0) as total_low_cal_point,
                 IFNULL(SUM(pre_bet_sum_s + pre_bet_sum_d_2 + pre_bet_sum_d_3 + pre_bet_sum_d_4 + pre_bet_sum_d_5_more 
                 + real_bet_sum_s + real_bet_sum_d + mini_bet_sum 
                 + total_casino_bet_money + total_slot_bet_money + total_hash_bet_money + total_espt_bet_money), 0) as total_bet_money
                 from shop_calculate_result where calculate_dt between '$db_srch_s_date' and '$db_srch_e_date'" . $add_where;
        return $sql;
    }

    // 게시판, 문의, 가입, 배팅회원
    public static function getBoadQnaJoinBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = " SELECT 'board' AS stype, 
                        DATE(create_dt) AS cr_dt, 
                        COUNT(*) AS cnt 
                            FROM member  AS T1
                        LEFT JOIN 
                                menu_board
                        ON 
                                menu_board.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx         
                        ";
        $sql .= " WHERE create_dt >= '" . $db_srch_s_date . "' AND create_dt <= '" . $db_srch_e_date . "' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        //$sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'qna' AS stype, 
                        DATE(create_dt) AS cr_dt, 
                        COUNT(*) AS cnt 
                         FROM member  AS T1
                        LEFT JOIN 
                            menu_qna
                        ON 
                            menu_qna.member_idx = T1.idx 
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx 
                            ";
        $sql .= " WHERE create_dt >= '" . $db_srch_s_date . "' AND create_dt <= '" . $db_srch_e_date . "' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        //$sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'member' AS stype,
                        DATE(reg_time) AS cr_dt,
                        COUNT(*) AS cnt 
                        FROM member ";
        $sql .= " WHERE idx > 0 AND reg_time >= '" . $db_srch_s_date . "' AND reg_time <= '" . $db_srch_e_date . "' ";

        $sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'bet' AS stype, 
                        DATE(calculate_dt) AS cr_dt, 
                        COUNT(distinct(member_idx)) AS cnt 
                        FROM member  AS T1
                        LEFT JOIN 
                            member_bet
                        ON 
                            member_bet.member_idx = T1.idx 
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx      
                        ";
        $sql .= " WHERE calculate_dt >= '" . $db_srch_s_date . "' AND calculate_dt <= '" . $db_srch_e_date . "' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 ";
        $sql .= " AND bet_status = 3 
                        AND total_bet_money != take_money" . $where_new;
        //$sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'charge_cnt' AS stype, 
                        DATE(update_dt) AS cr_dt, 
                        COUNT(distinct(member_money_charge_history.idx)) AS cnt 
                         FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history 
                        ON 
                            member_money_charge_history.member_idx = T1.idx 
                         LEFT JOIN member as parent ON T1.recommend_member = parent.idx     
                        ";
        $sql .= " WHERE update_dt >= '" . $db_srch_s_date . "' AND update_dt <= '" . $db_srch_e_date . "' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 ";
        $sql .= " AND member_money_charge_history.status = 3 " . $where_new;
        //$sql .= " GROUP BY cr_dt ";

        return $sql;
    }

    // 싱글 배팅회원수 / 배팅 카운트
    public static function getSingleBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT 
                        DATE(calculate_dt) AS cr_dt,
                        COUNT(distinct(T1.idx)) AS cnt,
                        COUNT(mb_bet.idx) AS bet_count
                            FROM member  AS T1
                        LEFT JOIN 
                            member_bet AS mb_bet
                        ON 
                            mb_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx       
                        ";
        $sql .= " WHERE folder_type = 'S'
                        AND calculate_dt >= '" . $db_srch_s_date . "' 
                        AND calculate_dt <= '" . $db_srch_e_date . "' 
                        AND total_bet_money != take_money
                        ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        //$sql .= " GROUP BY cr_dt";

        return $sql;
    }

    // 멀티 배팅회원수 / 배팅 카운트
    public static function getMultiBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT 
                        DATE(calculate_dt) AS cr_dt,
                        COUNT(distinct(T1.idx)) AS cnt,
                        COUNT(mb_bet.idx) AS bet_count
                            FROM member  AS T1
                        LEFT JOIN 
                            member_bet AS mb_bet
                        ON 
                            mb_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx    
                        ";
        $sql .= " WHERE folder_type = 'D'
                        AND calculate_dt >= '" . $db_srch_s_date . "' 
                        AND calculate_dt <= '" . $db_srch_e_date . "' 
                        AND total_bet_money != take_money
                        ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        //$sql .= " GROUP BY cr_dt";

        return $sql;
    }

    // 미니게임 배팅회원수 / 배팅 카운트
    public static function getMiniGameBetMemberCount($db_srch_s_date, $db_srch_e_date, $excluded_member_idx, $where_new) {
        $sql = " SELECT 
                        DATE(calculate_dt) AS cr_dt,
                        COUNT(distinct(T1.idx)) AS cnt,
                        COUNT(mg_bet.idx) AS bet_count
                         FROM member  AS T1
                        LEFT JOIN 
                            mini_game_member_bet AS mg_bet
                        ON 
                            mg_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx 
                            ";
        $sql .= " WHERE calculate_dt >= '" . $db_srch_s_date . "' AND calculate_dt <= '" . $db_srch_e_date . "' ";
        $sql .= " AND (CASE WHEN bet_type = 6 THEN T1.idx <> $excluded_member_idx AND total_bet_money != take_money 
                   ELSE total_bet_money != take_money END)";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        //$sql .= " GROUP BY cr_dt";
        return $sql;
    }

    // 카지노 배팅회원수 / 배팅 카운트
    public static function getCasinoGameBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = "
            SELECT count(PRT.idx) AS cnt,
            COUNT(CBH.CSN_BET_IDX) AS bet_count
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1
            AND PRT.u_business <> 1 " . $where_new;
        return $sql;
    }

    // 슬롯 배팅회원수 / 배팅 카운트
    public static function getSlotGameBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = "
            SELECT count(PRT.idx) AS cnt,
            COUNT(CBH.SLOT_BET_IDX) AS bet_count
            FROM member MB
                    LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1
            AND PRT.u_business <> 1 " . $where_new;
        return $sql;
    }

    // 이스포츠 배팅회원수 / 배팅 카운트
    public static function getEsportsGameBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = "
            SELECT count(PRT.idx)  AS cnt,
            COUNT(CBH.SLOT_BET_IDX) AS bet_count
            FROM member MB
                    LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1
            AND PRT.u_business <> 1 " . $where_new;
        return $sql;
    }

    // 해쉬 배팅회원수 / 배팅 카운트
    public static function getHashGameBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = "
            SELECT count(PRT.idx) AS cnt,
            COUNT(CBH.HASH_BET_IDX) AS bet_count
            FROM member MB
                    LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM>= '$db_srch_s_date'
            AND CBH.MOD_DTM<= '$db_srch_e_date'
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1
            AND PRT.u_business <> 1 " . $where_new;
        return $sql;
    }

    // 포인트 증차감 합
    public static function getPMPointSum($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = " SELECT ac_code, 
                        m_kind, 
                        DATE(t_log_cash.reg_time) AS reg_dt, 
                        SUM(t_log_cash.POINT) as s_point 
                         FROM member  AS T1
                        LEFT JOIN 
                          t_log_cash
                        ON 
                          t_log_cash.member_idx = T1.idx
                        ";
        $sql .= " WHERE t_log_cash.reg_time >= '" . $db_srch_s_date . "' AND  t_log_cash.reg_time <= '" . $db_srch_e_date . "' ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        $sql .= " AND ac_code IN(6,10,123,124) ";
        $sql .= " GROUP BY reg_dt, ac_code";

        return $sql;
    }

    public static function doSumDistCalculate($db_srch_s_date, $db_srch_e_date, $where_new) {
        $sql = "
                select 
                    IFNULL(SUM(calculate_point), 0)                                         AS total_point,
                    IFNULL(SUM(low_calculate_point), 0)                                    AS total_low_point,
                    IFNULL(SUM(ch_val), 0)                                                  AS tot_ch_val,
                    IFNULL(SUM(ex_val), 0)                                                  AS tot_ex_val,

                    IFNULL(SUM(pre_bet_sum_s), 0)                                           AS pre_bet_sum_s,
                    IFNULL(SUM(pre_bet_take_s), 0)                                          AS pre_take_sum_s,
                    IFNULL(SUM(pre_bet_sum_s), 0) - IFNULL(SUM(pre_bet_take_s), 0)          AS pre_sum_s,

                    IFNULL(SUM(pre_bet_sum_d_2 + pre_bet_sum_d_3 + pre_bet_sum_d_4 + pre_bet_sum_d_5_more), 0)   AS pre_bet_sum_d,
                    IFNULL(SUM(pre_bet_take_d_2 + pre_bet_take_d_3 +  pre_bet_take_d_4 + pre_bet_take_d_5_more), 0)       AS pre_take_sum_d,
                    IFNULL(SUM(pre_bet_sum_d_2 + pre_bet_sum_d_3 + pre_bet_sum_d_4 + pre_bet_sum_d_5_more), 0)  
                    - IFNULL(SUM(pre_bet_take_d_2 + pre_bet_take_d_3 + pre_bet_take_d_4 + pre_bet_take_d_5_more), 0)      AS pre_sum_d,

                    IFNULL(SUM(real_bet_sum_s), 0)                                           AS real_bet_sum_s,
                    IFNULL(SUM(real_bet_take_s), 0)                                          AS real_take_sum_s,
                    IFNULL(SUM(real_bet_sum_s), 0) - IFNULL(SUM(real_bet_take_s), 0)         AS real_sum_s,

                    IFNULL(SUM(real_bet_sum_d), 0)                                           AS real_bet_sum_d,
                    IFNULL(SUM(real_bet_take_d), 0)                                          AS real_take_sum_d,
                    IFNULL(SUM(real_bet_sum_d), 0) - IFNULL(SUM(real_bet_take_d), 0)         AS real_sum_d,

                    IFNULL(SUM(mini_bet_sum), 0)                                           AS mini_bet_sum_d,
                    IFNULL(SUM(mini_bet_take), 0)                                          AS mini_take_sum_d,
                    IFNULL(SUM(mini_bet_sum), 0) - IFNULL(SUM(mini_bet_take), 0)           AS mini_sum_d,

                    IFNULL(SUM(total_casino_bet_money), 0)                                   AS total_casino_bet_money,
                    IFNULL(SUM(casino_bet_take), 0)                                          AS total_casino_win_money,
                    IFNULL(SUM(total_casino_bet_money), 0) - IFNULL(SUM(casino_bet_take), 0) AS total_casino_lose_money,

                    IFNULL(SUM(total_slot_bet_money), 0)                                   AS total_slot_bet_money,
                    IFNULL(SUM(slot_bet_take), 0)                                          AS total_slot_win_money,
                    IFNULL(SUM(total_slot_bet_money), 0) - IFNULL(SUM(slot_bet_take), 0)   AS total_slot_lose_money,

                    IFNULL(SUM(total_espt_bet_money), 0)                                   AS total_espt_bet_money,
                    IFNULL(SUM(espt_bet_take), 0)                                          AS total_espt_win_money,
                    IFNULL(SUM(total_espt_bet_money), 0) - IFNULL(SUM(espt_bet_take), 0) AS total_espt_lose_money,

                    IFNULL(SUM(total_hash_bet_money), 0)                                   AS total_hash_bet_money,
                    IFNULL(SUM(hash_bet_take), 0)                                          AS total_hash_win_money,
                    IFNULL(SUM(total_hash_bet_money), 0) - IFNULL(SUM(hash_bet_take), 0) AS total_hash_lose_money,
                    
                    IFNULL(SUM(pre_classic_bet_sum_s + pre_classic_bet_sum_d), 0)        AS total_classic_bet_money,
                    IFNULL(SUM(pre_classic_bet_take_s + pre_classic_bet_take_d), 0)      AS total_classic_win_money,
                    IFNULL(SUM(pre_classic_bet_sum_s + pre_classic_bet_sum_d), 0) 
                    - IFNULL(SUM(pre_classic_bet_take_s + pre_classic_bet_take_d), 0)    AS total_classic_lose_money
                    
                  
                from shop_calculate_result where calculate_dt between '$db_srch_s_date' and '$db_srch_e_date'" . $where_new;

        return $sql;
    }

    public static function doDaySumDistCalculate($db_srch_s_date, $db_srch_e_date, $add_day_new) {
        $sql = "
            select 
                    DATE(calculate_dt) 							    AS calculate_dt,
                    IFNULL(SUM(calculate_point), 0)                                         AS cal_point,
                    IFNULL(SUM(low_calculate_point), 0)                                     AS low_cal_point,
                    IFNULL(SUM(ch_val), 0)                                                  AS ch_val,
                    IFNULL(SUM(ex_val), 0)                                                  AS ex_val,

                    IFNULL(SUM(pre_bet_sum_s), 0)                                           AS pre_bet_sum_s,
                    IFNULL(SUM(pre_bet_take_s), 0)                                          AS pre_take_sum_s,
                    IFNULL(SUM(pre_bet_sum_s), 0) - IFNULL(SUM(pre_bet_take_s), 0)          AS pre_sum_s,

                    IFNULL(SUM(pre_bet_sum_d_2 + pre_bet_sum_d_3 + pre_bet_sum_d_4 + pre_bet_sum_d_5_more), 0)   AS pre_bet_sum_d,
                    IFNULL(SUM(pre_bet_take_d_2 + pre_bet_take_d_3 + pre_bet_take_d_4 + pre_bet_take_d_5_more), 0)       AS pre_take_sum_d,
                    IFNULL(SUM(pre_bet_sum_d_2 + pre_bet_sum_d_3 + pre_bet_sum_d_4 + pre_bet_sum_d_5_more), 0)  
                    - IFNULL(SUM(pre_bet_take_d_2 + pre_bet_take_d_3 + pre_bet_take_d_4 + pre_bet_take_d_5_more), 0)      AS pre_sum_d,

                    IFNULL(SUM(real_bet_sum_s), 0)                                           AS real_bet_sum_s,
                    IFNULL(SUM(real_bet_take_s), 0)                                          AS real_take_sum_s,
                    IFNULL(SUM(real_bet_sum_s), 0) - IFNULL(SUM(real_bet_take_s), 0)         AS real_sum_s,

                    IFNULL(SUM(real_bet_sum_d), 0)                                           AS real_bet_sum_d,
                    IFNULL(SUM(real_bet_take_d), 0)                                          AS real_take_sum_d,
                    IFNULL(SUM(real_bet_sum_d), 0) - IFNULL(SUM(real_bet_take_d), 0)         AS real_sum_d,

                    IFNULL(SUM(mini_bet_sum), 0)                                           AS mini_bet_sum_d,
                    IFNULL(SUM(mini_bet_take), 0)                                          AS mini_take_sum_d,
                    IFNULL(SUM(mini_bet_sum), 0) - IFNULL(SUM(mini_bet_take), 0)           AS mini_sum_d,

                    IFNULL(SUM(total_casino_bet_money), 0)                                   AS total_casino_bet_money,
                    IFNULL(SUM(casino_bet_take), 0)                                          AS total_casino_win_money,
                    IFNULL(SUM(total_casino_bet_money), 0) - IFNULL(SUM(casino_bet_take), 0) AS total_casino_lose_money,

                    IFNULL(SUM(total_slot_bet_money), 0)                                   AS total_slot_bet_money,
                    IFNULL(SUM(slot_bet_take), 0)                                          AS total_slot_win_money,
                    IFNULL(SUM(total_slot_bet_money), 0) - IFNULL(SUM(slot_bet_take), 0)   AS total_slot_lose_money,

                    IFNULL(SUM(total_espt_bet_money), 0)                                   AS total_espt_bet_money,
                    IFNULL(SUM(espt_bet_take), 0)                                          AS total_espt_win_money,
                    IFNULL(SUM(total_espt_bet_money), 0) - IFNULL(SUM(espt_bet_take), 0) AS total_espt_lose_money,

                    IFNULL(SUM(total_hash_bet_money), 0)                                   AS total_hash_bet_money,
                    IFNULL(SUM(hash_bet_take), 0)                                          AS total_hash_win_money,
                    IFNULL(SUM(total_hash_bet_money), 0) - IFNULL(SUM(hash_bet_take), 0) AS total_hash_lose_money,
                    
                    IFNULL(SUM(pre_classic_bet_sum_s + pre_classic_bet_sum_d), 0)        AS total_classic_bet_money,
                    IFNULL(SUM(pre_classic_bet_take_s + pre_classic_bet_take_d), 0)      AS total_classic_win_money,
                    IFNULL(SUM(pre_classic_bet_sum_s + pre_classic_bet_sum_d), 0) 
                    - IFNULL(SUM(pre_classic_bet_take_s + pre_classic_bet_take_d), 0)    AS total_classic_lose_money

                    from shop_calculate_result where calculate_dt between '$db_srch_s_date' and '$db_srch_e_date'" . $add_day_new;

        return $sql;
    }

    public static function getDistShopInfo() {
        $sql = "
           SELECT shop_config.*
            ,ifnull(parent_shop.bet_pre_s_fee, 0)        as pt_bet_pre_s_fee
            
            ,ifnull(parent_shop.bet_pre_d_fee, 0)        as pt_bet_pre_d_fee
            ,ifnull(parent_shop.bet_pre_d_2_fee, 0)      as pt_bet_pre_d_2_fee
            ,ifnull(parent_shop.bet_pre_d_3_fee, 0)      as pt_bet_pre_d_3_fee
            ,ifnull(parent_shop.bet_pre_d_4_fee, 0)      as pt_bet_pre_d_4_fee
            ,ifnull(parent_shop.bet_pre_d_5_more_fee, 0) as pt_bet_pre_d_5_more_fee
            
            ,ifnull(parent_shop.bet_real_s_fee, 0)       as pt_bet_real_s_fee
            ,ifnull(parent_shop.bet_real_d_fee, 0)       as pt_bet_real_d_fee
            
            ,ifnull(parent_shop.bet_mini_fee, 0)         as pt_bet_mini_fee
            
            ,ifnull(parent_shop.pre_s_fee, 0)            as pt_pre_s_fee
            
            ,ifnull(parent_shop.bet_casino_fee, 0)       as pt_bet_casino_fee
            ,ifnull(parent_shop.bet_slot_fee, 0)         as pt_bet_slot_fee
            ,ifnull(parent_shop.bet_esports_fee, 0)      as pt_bet_esports_fee
            ,ifnull(parent_shop.bet_hash_fee, 0)         as pt_bet_hash_fee
            
            ,ifnull(pt.idx, 0) as recommend_member
            FROM shop_config 
            LEFT JOIN member ON member.idx = shop_config.member_idx
            LEFT JOIN member as pt ON pt.idx = member.recommend_member
            LEFT JOIN shop_config as parent_shop ON parent_shop.member_idx = pt.idx";

        return $sql;
    }

    public static function doComBdQnJoinBetUserQuery($where_new) {
        $sql = " SELECT 'board' AS stype, 
                        DATE(create_dt) AS cr_dt, 
                        COUNT(*) AS cnt 
                            FROM member  AS T1
                        LEFT JOIN 
                                menu_board
                        ON 
                                menu_board.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx         
                        ";
        $sql .= " WHERE create_dt >= ? AND create_dt <= ? ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'qna' AS stype, 
                        DATE(create_dt) AS cr_dt, 
                        COUNT(*) AS cnt 
                         FROM member  AS T1
                        LEFT JOIN 
                            menu_qna
                        ON 
                            menu_qna.member_idx = T1.idx 
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx        
                            ";
        $sql .= " WHERE create_dt >= ? AND create_dt <= ? ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'member' AS stype,
                        DATE(reg_time) AS cr_dt,
                        COUNT(*) AS cnt 
                        FROM member ";
        $sql .= " WHERE idx > 0 AND reg_time >= ? AND reg_time <= ? ";

        $sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'bet' AS stype, 
                        DATE(calculate_dt) AS cr_dt, 
                        COUNT(distinct(member_idx)) AS cnt 
                        FROM member  AS T1
                        LEFT JOIN 
                            member_bet
                        ON 
                            member_bet.member_idx = T1.idx 
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx        
                        ";
        $sql .= " WHERE calculate_dt >= ? AND calculate_dt <= ? ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " AND bet_status = 3 
                        AND total_bet_money != take_money";
        $sql .= " GROUP BY cr_dt ";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'charge_cnt' AS stype, 
                        DATE(update_dt) AS cr_dt, 
                        COUNT(distinct(member_money_charge_history.idx)) AS cnt 
                         FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history 
                        ON 
                            member_money_charge_history.member_idx = T1.idx 
                         LEFT JOIN member as parent ON T1.recommend_member = parent.idx ";

        $sql .= " WHERE update_dt >= ? AND update_dt <= ? ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " AND member_money_charge_history.status = 3 ";
        $sql .= " GROUP BY cr_dt ";

        return $sql;
    }

    public static function doComSingleBetUserCountQuery($where_new) {
        $sql = " SELECT 
                        DATE(calculate_dt) AS cr_dt,
                        COUNT(distinct(T1.idx)) AS cnt
                            FROM member  AS T1
                        LEFT JOIN 
                            member_bet AS mb_bet
                        ON 
                            mb_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx      
                        ";
        $sql .= " WHERE folder_type = 'S'
                        AND calculate_dt >= ?
                        AND calculate_dt <= ? 
                        AND total_bet_money != take_money
                        ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " GROUP BY cr_dt";

        return $sql;
    }

    public static function doComMultiBetUserCountQuery($where_new) {
        $sql = " SELECT 
                        DATE(calculate_dt) AS cr_dt,
                        COUNT(distinct(T1.idx)) AS cnt
                         FROM member  AS T1
                        LEFT JOIN 
                            member_bet AS mb_bet
                        ON 
                            mb_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx 
                            ";
        $sql .= " WHERE folder_type = 'D'
                        AND calculate_dt >= ?
                        AND calculate_dt <= ?
                        AND total_bet_money != take_money
                        ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " GROUP BY cr_dt";
        return $sql;
    }

    public static function doComMiniGameBetUserCountQuery($where_new) {
        $sql = " SELECT 
                        DATE(calculate_dt) AS cr_dt,
                        COUNT(distinct(T1.idx)) AS cnt
                         FROM member  AS T1
                        LEFT JOIN 
                            mini_game_member_bet AS mg_bet
                        ON 
                            mg_bet.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx ";

        $sql .= " WHERE calculate_dt >= ? AND calculate_dt <= ? ";
        $sql .= " AND (CASE WHEN bet_type = 6 THEN T1.idx <> ? AND total_bet_money != take_money 
                   ELSE total_bet_money != take_money END)";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " GROUP BY cr_dt";
        return $sql;
    }

    public static function doComPointPMQuery($where_new) {
        $sql = " SELECT ac_code, 
                        m_kind, 
                        DATE(t_log_cash.reg_time) AS reg_dt, 
                        SUM(t_log_cash.POINT) as s_point 
                         FROM member  AS T1
                        LEFT JOIN 
                          t_log_cash
                        ON 
                          t_log_cash.member_idx = T1.idx
                        ";
        $sql .= " WHERE t_log_cash.reg_time >= ? AND  t_log_cash.reg_time <= ? ";

        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 " . $where_new;

        $sql .= " AND ac_code IN(6,10,123,124) ";
        $sql .= " GROUP BY reg_dt, ac_code";
        return $sql;
    }

    public static function getRecommandListQuery($where_new) {
        $sql = " SELECT 
                    charge_total_money,
                    exchange_total_money,
                    T1.id,
                    T1.money,
                    T1.point,
                    T1.status
                 FROM total_member_cash 
                 LEFT JOIN 
                   member  AS T1
                 ON 
                   total_member_cash.member_idx = T1.idx";
        $sql .= " WHERE  T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        return $sql;
    }

    public static function getSumTotalMemberCashQuery($where_new) {
        $sql = " SELECT 
                    IFNULL(SUM(bet_total_count), 0)                                              AS sum_bet_total_count,
                    IFNULL(SUM(charge_total_count), 0)                                           AS sum_charge_total_count,
                    IFNULL(SUM(exchange_total_count), 0)                                         AS sum_exchange_total_count,
                    IFNULL(MAX(max_charge), 0)                                                   AS max_charge,
                    IFNULL(MAX(max_exchange), 0)                                                 AS max_exchange
                 FROM total_member_cash 
                 LEFT JOIN 
                   member  AS T1
                 ON 
                   total_member_cash.member_idx = T1.idx";
        $sql .= " WHERE  T1.level <> 9 AND T1.u_business = 1 " . $where_new;
        return $sql;
    }

    // 기간내 총판별 포인트 차감 합
    public static function getMPointSum($where_new) {
        $sql = "select T1.recommend_member as dis_idx,  IFNULL(SUM(t_log_cash.point), 0) as total_point
            from member as T1
            LEFT JOIN member as parent ON T1.recommend_member = parent.idx
            left join t_log_cash on  T1.idx = t_log_cash.member_idx 
            WHERE t_log_cash.reg_time between ? AND  ? 
                AND T1.level <> 9 AND T1.u_business = 1 
                AND ac_code IN(124) ";
        $sql .= $where_new . " GROUP BY T1.recommend_member";

        return $sql;
    }

  

}

?>
