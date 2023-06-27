<?php
include_once(_LIBPATH . '/class_GameStatusUtil.php');

class ComQueryUser {
    // 입출금 정산 쿼리 
    public static function doComChExQuery($db_srch_s_date, $db_srch_e_date) {
        $param = array($db_srch_s_date, $db_srch_e_date,$db_srch_s_date, $db_srch_e_date);
   
        $sql = " SELECT 'ch' AS stype, 
                        DATE(update_dt) AS up_dt,
                        count(member_money_charge_history.idx) as user_cnt,
                        SUM(member_money_charge_history.money) as s_money 
                        FROM member  AS T1
                        LEFT JOIN 
                            member_money_charge_history
                        ON 
                            member_money_charge_history.member_idx = T1.idx  
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx";
        $sql .= " WHERE update_dt >= ? AND  update_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business = 1 ";
        $sql .= " AND member_money_charge_history.status = 3";
        $sql .= " GROUP BY up_dt";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'ex' AS stype, 
                        DATE(update_dt) AS up_dt,
                        count(member_money_exchange_history.idx) as user_cnt,
                        SUM(member_money_exchange_history.money) as s_money 
                            FROM member  AS T1
                        LEFT JOIN 
                            member_money_exchange_history
                        ON 
                            member_money_exchange_history.member_idx = T1.idx
                        LEFT JOIN member as parent ON T1.recommend_member = parent.idx     
                        ";
        $sql .= " WHERE update_dt >= ? AND update_dt <= ? ";
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business = 1 ";
        ;
        $sql .= " AND member_money_exchange_history.status = 3";
        $sql .= " GROUP BY up_dt";
        $sql .= " ;";
        return [$sql,$param];
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
        $query .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business = 1 " . $where_new;
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
        $sql .= " AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business = 1 AND bet_status = 3 AND total_bet_money != take_money  " . $where_new;
        $sql .= " AND (CASE WHEN bet_type = 6 THEN T1.idx <> ? 
                   ELSE 1 = 1 END)";
        $sql .= " GROUP BY cr_dt,dis_idx";
        $sql .= " ; ";

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
            AND PRT.u_business = 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";

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
            AND MB.u_business = 1 AND PRT.u_business = 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";
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
           AND MB.u_business = 1 AND PRT.u_business = 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.MOD_DTM)";
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
       AND MB.u_business = 1 AND PRT.u_business = 1 " . $where_new;
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
       AND MB.u_business = 1 AND PRT.u_business = 1 " . $where_new;
        $sql .= " GROUP BY PRT.idx, DATE(CBH.REG_DTM)";
        return $sql;
    }
}

?>