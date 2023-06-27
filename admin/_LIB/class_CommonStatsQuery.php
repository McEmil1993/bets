<?php

/** @noinspection ALL */
include_once(_LIBPATH . '/class_GameStatusUtil.php');

class CommonStatsQuery {

    // 일반 유저의 총수를 가져온다.
    public static function getTotalUserCountQuery($srch_basic) {
        $sql = "SELECT 
            IFNULL(COUNT(T1.idx), 0) as idx_cnt
            from member AS T1 
            WHERE T1.level != 9  AND T1.u_business = 1";
        $sql .= $srch_basic;
        return $sql;
    }

    //충전합계
    public static function getTotalSumChargeQuery($date_where) {
        $sql = "SELECT IFNULL(SUM(MC.money),0) AS ch_sum 
                        FROM member AS T1
                        LEFT JOIN member_money_charge_history AS MC
                            ON MC.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND MC.status = 3 ";
        $sql .= $date_where;
        return $sql;
    }

    //환전합계
    public static function getTotalSumExchangeQuery($date_where) {
        $sql = "SELECT IFNULL(SUM(MC.money),0) AS ex_sum 
                        FROM member AS T1
                        LEFT JOIN member_money_exchange_history AS MC
                            ON MC.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND MC.status = 3 ";
        $sql .= $date_where;
        return $sql;
    }

    // 프리매치 - 싱글 - 배팅합계    
    public static function getTotalSumPreMatchSingleBetMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT 
                        IFNULL(SUM(total_bet_money),0) AS pre_bet_sum_s
                        FROM member AS T1
                        LEFT JOIN 
                            member_bet AS B
                        ON 
                            B.member_idx = T1.idx";

        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'S'
                        AND B.bet_type = 1
                        AND B.bet_status = 3
                        AND total_bet_money != take_money ";

        return $sql;
    }

    // 프리매치 - 싱글 - 당첨합계
    public static function getTotalSumPreMatchSingleTakeMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(take_money),0) AS pre_take_sum_s
                        FROM member AS T1
                        LEFT JOIN 
                            member_bet AS B
                        ON
                            B.member_idx = T1.idx ";

        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'S'
                        AND B.bet_type = 1
                        AND B.bet_status = 3
                        AND total_bet_money != take_money ";
        return $sql;
    }

    // 프리매치 - 다폴더 - 배팅합계
    public static function getTotalSumPreMatchMultiBetMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(total_bet_money),0) AS pre_bet_sum_d
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";

        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'D'
        AND B.bet_type = 1
        AND B.bet_status = 3
        AND total_bet_money != take_money";
        return $sql;
    }

    // 프리매치 - 다폴더 - 당첨합계
    public static function getTotalSumPreMatchMultiTakeMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(take_money),0) AS pre_take_sum_d
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'D'
                            AND B.bet_type = 1
                            AND B.bet_status = 3
                            AND total_bet_money != take_money";
        return $sql;
    }

    // 실시간 싱글 - 배팅합계
    public static function getTotalSumInplaySingleBetMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(total_bet_money),0) AS real_bet_sum_s
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'S'
                            AND B.bet_type = 2
                            AND B.bet_status = 3
                            AND total_bet_money != take_money";
        return $sql;
    }

    // 실시간 싱글 - 당첨합계
    public static function getTotalSumInplaySingleTakeMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(take_money),0) AS real_take_sum_s
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";
        $sql .= "   where T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'S'
                            AND B.bet_type = 2
                            AND B.bet_status = 3
                            AND B.total_bet_money != B.take_money";
        return $sql;
    }

    // 실시간 - 다폴더 - 배팅합계
    public static function getTotalSumInplayMultiBetMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(total_bet_money),0) AS real_bet_sum_d
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";

        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'D'
                            AND B.bet_type = 2
                            AND B.bet_status = 3
                            AND total_bet_money != take_money";
        return $sql;
    }

    // 실시간 - 다폴더 - 당첨합계
    public static function getTotalSumInplayMultiTakeMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(take_money),0) AS real_take_sum_d
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.folder_type = 'D'
                            AND B.bet_type = 2
                            AND B.bet_status = 3
                            AND total_bet_money != take_money";
        return $sql;
    }

    
     // 클래식 - 배팅합계
    public static function getTotalSumClassicBetMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(total_bet_money),0) AS total_classic_bet_money
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";

        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.is_classic = 'ON'
                            AND B.bet_type = 1
                            AND B.bet_status = 3
                            AND total_bet_money != take_money";
        return $sql;
    }

    // 클래식  - 당첨합계
    public static function getTotalSumClassicTakeMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(take_money),0) AS total_classic_win_money
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS B
                            ON
                                B.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND B.is_classic = 'ON'
                            AND B.bet_type = 1
                            AND B.bet_status = 3
                            AND total_bet_money != take_money";
        return $sql;
    }

    
    
    // 미니게임 - 배팅합계
    public static function getTotalSumMiniGameBetMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(total_bet_money),0) AS mini_bet_sum_d
                            FROM member AS T1
                            LEFT JOIN 
                                mini_game_member_bet AS MG
                            ON
                                MG.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1 AND MG.bet_status = 3";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND total_bet_money != take_money ";

        return $sql;
    }

    // 미니게임 - 당첨합계
    public static function getTotalSumMiniGameTakeMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = "SELECT IFNULL(SUM(take_money),0) AS mini_take_sum_d
                            FROM member AS T1
                            LEFT JOIN 
                                mini_game_member_bet AS MG
                            ON
                                MG.member_idx = T1.idx";
        $sql .= " WHERE T1.level <> 9 AND T1.u_business = 1";
        $sql .= " AND calculate_dt >= '$db_srch_s_date'  AND calculate_dt <= '$db_srch_e_date' ";
        $sql .= " AND total_bet_money != take_money ";

        return $sql;
    }

    // 카지노,슬롯 배팅금,당첨금 
    public static function getTotalSumCasinoSlotMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = " SELECT 
        'bet_tot_casino' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
        FROM
            (`member` `MB`
            LEFT JOIN `KP_CSN_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
                AND `CBH`.`MOD_DTM` <= '$db_srch_e_date' 
                AND `MB`.`level` <> 9
                AND CBH.TYPE IN ('W', 'L')
                AND `MB`.`u_business` = 1 
        UNION ALL SELECT 
            'bet_tot_slot' AS `stype`,
            IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
            IFNULL(SUM(CASE
                        WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                    END),
                    0) AS `total_win_money`
        FROM
            (`member` `MB`
            LEFT JOIN `KP_SLOT_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
                AND `CBH`.`MOD_DTM` <= '$db_srch_e_date' 
                AND `MB`.`level` <> 9
                AND  CBH.TYPE IN ('W', 'L')
                AND `MB`.`u_business` = 1";

        return $sql;
    }

    // 이스포츠 / 키론 / 해시  배팅금,당첨금 
    public static function getTotalSumEsportsHashMoneyQuery($db_srch_s_date, $db_srch_e_date) {
        $sql = " SELECT 
        'bet_tot_espt' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
        FROM
            (`member` `MB`
            LEFT JOIN `KP_ESPT_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
                AND `CBH`.`MOD_DTM` <= '$db_srch_e_date' 
                AND `MB`.`level` <> 9
                AND CBH.TYPE IN ('W', 'L')
                AND `MB`.`u_business` = 1 
        UNION ALL SELECT 
            'bet_tot_hash' AS `stype`,
            IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
            IFNULL(SUM(CASE
                        WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                    END),
                    0) AS `total_win_money`
        FROM
            (`member` `MB`
            LEFT JOIN `OD_HASH_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
                AND `CBH`.`MOD_DTM` <= '$db_srch_e_date' 
                AND `MB`.`level` <> 9
                AND  CBH.TYPE IN ('W', 'L')
                AND `MB`.`u_business` = 1";
        return $sql;
    }

    public static function getTotalSumMemberQuery($db_srch_s_date, $db_srch_e_date, $member_id,$srch_basic,$sql_groupby,$sql_orderby,$start,$num_per_page) {
        $sql = "SELECT 
        mb.idx AS idx,
        mb.id AS id,
        mb.nick_name AS nick_name,
        mb.dis_id,
        IFNULL(mb_ch_his.ch_sum,0) AS ch_sum,
        IFNULL(mb_ex_his.ex_sum, 0) AS ex_sum, 
        IFNULL(IFNULL(mb_ch_his.ch_sum, 0) - IFNULL(mb_ex_his.ex_sum, 0), 0) AS diff_sum,
        IFNULL(ROUND((IFNULL(mb_ch_his.ch_sum, 0) - IFNULL(mb_ex_his.ex_sum, 0)) / IFNULL(mb_ch_his.ch_sum, 0) * 100 , 2), 0) AS diff_per_sum,
        IFNULL(bet.pre_bet_sum_s, 0) AS pre_bet_sum_s,
        IFNULL(bet.pre_take_sum_s, 0) AS pre_take_sum_s,
        IFNULL(bet.pre_sum_s, 0) AS pre_sum_s,
        IFNULL(bet.pre_bet_sum_d, 0) AS pre_bet_sum_d,
        IFNULL(bet.pre_take_sum_d, 0) AS pre_take_sum_d,
        IFNULL(bet.pre_sum_d, 0) AS pre_sum_d,
        IFNULL(bet.real_bet_sum_s, 0) AS real_bet_sum_s,
        IFNULL(bet.real_take_sum_s, 0) AS real_take_sum_s,
        IFNULL(bet.real_sum_s, 0) AS real_sum_s,    
        IFNULL(bet.real_bet_sum_d, 0) AS real_bet_sum_d,
        IFNULL(bet.real_take_sum_d, 0) AS real_take_sum_d,
        IFNULL(bet.real_sum_d, 0) AS real_sum_d,    
        


        IFNULL(mg_bet.mini_bet_sum_d, 0) AS mini_bet_sum_d,
        IFNULL(mg_bet.mini_take_sum_d, 0) AS mini_take_sum_d,
        IFNULL(mg_bet.mini_sum_d, 0) AS mini_sum_d,
        
-- 카지노
        IFNULL(mb_casino.total_bet_money, 0) AS total_casino_bet_money,
        IFNULL(mb_casino.total_win_money, 0) AS total_casino_win_money,
        IFNULL(IFNULL(mb_casino.total_bet_money, 0) - IFNULL(mb_casino.total_win_money, 0), 0) AS total_casino_lose_money,
-- 슬롯
        IFNULL(mb_slot.total_bet_money, 0) AS total_slot_bet_money,
        IFNULL(mb_slot.total_win_money, 0) AS total_slot_win_money,
        IFNULL(IFNULL(mb_slot.total_bet_money, 0) - IFNULL(mb_slot.total_win_money, 0), 0) AS total_slot_lose_money,
-- 이스포츠/키론
        IFNULL(mb_espt.total_bet_money, 0) AS total_espt_bet_money,
        IFNULL(mb_espt.total_win_money, 0) AS total_espt_win_money,
        IFNULL(IFNULL(mb_espt.total_bet_money, 0) - IFNULL(mb_espt.total_win_money, 0), 0) AS total_espt_lose_money,
-- 해시
        IFNULL(mb_hash.total_bet_money, 0) AS total_hash_bet_money,
        IFNULL(mb_hash.total_win_money, 0) AS total_hash_win_money,
        IFNULL(IFNULL(mb_hash.total_bet_money, 0) - IFNULL(mb_hash.total_win_money, 0), 0) AS total_hash_lose_money,
        
-- classic 
        IFNULL(bet.total_classic_bet_money, 0) AS total_classic_bet_money,
        IFNULL(bet.total_classic_win_money, 0) AS total_classic_win_money,
        IFNULL(bet.total_classic_lose_money, 0) AS total_classic_lose_money
        
FROM (SELECT m.idx, m.id, m.nick_name,m.dis_id from member AS m WHERE m.level != 9  and m.u_business = 1) AS mb


    LEFT JOIN 
        (SELECT IFNULL(sum(ch_his.money), 0) AS ch_sum,
            ch_his.member_idx AS member_idx
            FROM member_money_charge_history AS ch_his 
            WHERE ch_his.status = 3
            AND ch_his.update_dt >= '$db_srch_s_date'
            AND ch_his.update_dt <= '$db_srch_e_date'
            GROUP BY ch_his.member_idx) AS mb_ch_his 
    ON 
    mb.idx =  mb_ch_his.member_idx

    LEFT JOIN 
        (SELECT IFNULL(sum(ex_his.money), 0) AS ex_sum,
            ex_his.member_idx AS member_idx
            FROM member_money_exchange_history AS ex_his 
            WHERE ex_his.status = 3
            AND ex_his.update_dt >= '$db_srch_s_date'
            AND ex_his.update_dt <= '$db_srch_e_date'
            GROUP BY ex_his.member_idx) AS mb_ex_his
    ON mb.idx =  mb_ex_his.member_idx
    
    LEFT JOIN 
	    (SELECT 
        mb_bet.member_idx AS member_idx,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0) AS pre_bet_sum_s,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS pre_take_sum_s,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0)
            - 	IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS pre_sum_s,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0) AS pre_bet_sum_d,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS pre_take_sum_d,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0)
            - 	IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS pre_sum_d,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0) AS real_bet_sum_s,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS real_take_sum_s,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0)
            - 	IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'S'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS real_sum_s,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0) AS real_bet_sum_d,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS real_take_sum_d,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0)
            - 	IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 2 AND mb_bet.folder_type = 'D'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS real_sum_d,
                        
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.is_classic = 'ON'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0) AS total_classic_bet_money,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.is_classic = 'ON'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS total_classic_win_money,
            IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.is_classic = 'ON'
                        THEN mb_bet.total_bet_money ELSE 0 END), 0)
            - 	IFNULL(SUM(CASE 
                        WHEN mb_bet.bet_type = 1 AND mb_bet.is_classic = 'ON'
                        THEN mb_bet.take_money ELSE 0 END), 0) AS total_classic_lose_money



	FROM member_bet AS mb_bet
        WHERE mb_bet.bet_status = 3
        AND mb_bet.calculate_dt >= '$db_srch_s_date'
        AND mb_bet.calculate_dt <= '$db_srch_e_date'
        AND total_bet_money != take_money
	GROUP BY mb_bet.member_idx) AS bet
    ON 
    mb.idx =  bet.member_idx
LEFT JOIN 
	(SELECT mg.member_idx AS member_idx,
	    IFNULL(sum(mg.total_bet_money), 0) AS mini_bet_sum_d,
        IFNULL(sum(mg.take_money), 0) AS mini_take_sum_d,
        IFNULL(sum(mg.total_bet_money), 0) - IFNULL(sum(mg.take_money), 0) AS mini_sum_d
	FROM mini_game_member_bet AS mg
	WHERE mg.bet_status = 3  
        AND mg.calculate_dt >= '$db_srch_s_date'
        AND mg.calculate_dt <= '$db_srch_e_date'
        AND mg.total_bet_money != take_money
    GROUP BY mg.member_idx) AS mg_bet
    ON 
    mb.idx =  mg_bet.member_idx ";

        $sql .= "LEFT JOIN (SELECT 
        `CBH`.MBR_IDX,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
        FROM
             `KP_CSN_BET_HIST` `CBH` 
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
             AND `CBH`.`MOD_DTM` <= '$db_srch_e_date'  GROUP BY `CBH`.MBR_IDX ) as mb_casino
       
         ON 
             mb.idx =  mb_casino.MBR_IDX ";

        $sql .= "LEFT JOIN (SELECT 
        `CBH`.MBR_IDX,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
        FROM
             `KP_SLOT_BET_HIST` `CBH` 
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
             AND `CBH`.`MOD_DTM` <= '$db_srch_e_date'  GROUP BY `CBH`.MBR_IDX ) as mb_slot
       
         ON 
             mb.idx =  mb_slot.MBR_IDX ";

        $sql .= "LEFT JOIN (SELECT 
        `CBH`.MBR_IDX,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
        FROM
             `KP_ESPT_BET_HIST` `CBH` 
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
             AND `CBH`.`MOD_DTM` <= '$db_srch_e_date'  GROUP BY `CBH`.MBR_IDX ) as mb_espt
       
         ON 
             mb.idx =  mb_espt.MBR_IDX ";

        $sql .= "LEFT JOIN (SELECT 
        `CBH`.MBR_IDX,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
        FROM
             `OD_HASH_BET_HIST` `CBH` 
        WHERE
            `CBH`.`MOD_DTM` >= '$db_srch_s_date'
             AND `CBH`.`MOD_DTM` <= '$db_srch_e_date'  GROUP BY `CBH`.MBR_IDX ) as mb_hash
       
         ON 
             mb.idx =  mb_hash.MBR_IDX ";

        if ($member_id == '')
            $sql .= " WHERE 1 = 1 ";
        else
            $sql .= " WHERE mb.idx in (SELECT idx FROM member WHERE dis_id = '$member_id') ";
        $sql .= $srch_basic;
        $sql .= $sql_groupby;
        $sql .= $sql_orderby;
        $sql .= " LIMIT " . $start . ", " . $num_per_page;

        return $sql;
    }

     public static function getNewJoinMemberToalCount($where_new) {
        $sql = "SELECT 
                COUNT(idx) AS total_count
                FROM member as T1
                WHERE
                    T1.reg_time >= ?
                    AND T1.reg_time <= ?
                    AND T1.status IN (1,11)". $where_new;
        return $sql;
    }
    
}

?>
