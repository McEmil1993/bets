<?php


namespace App\Controllers;

namespace App\Util;

use App\Models\MemberBetDetailModel;
use App\Models\MiniGameMemberBetModel;
use App\Models\MemberBetModel;
use App\Models\MemberModel;
use App\Models\LSportsBetModel;
use App\Config\Constants;
use App\Models\TLogCashModel;
use App\Util\BetCodeUtil;
use App\Models\TotalMemberCashModel;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;

class Calculate {

    //  카지노 배팅 회원 총수 (회원별)
    public static function doCasinoByUserQueryAllCount($where_new) {

        $sql = "
            SELECT MB.idx                                                                   
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  카지노 배팅 조회 (회원별)
    public static function doCasinoByUserQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT  
             
                    MB.idx                                                                        AS idx,          /* 회원 IDX */
                    MB.id                                                                        AS id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                            AS cr_dt,            /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                    
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                    
                  
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  카지노 배팅 조회 배팅 내역 정보를 가져온다(회원별)
    public static function doCasinoByUserBetDetailQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT 
                    MB.id,
                    MB.nick_name,
                    CBH.HLD_MNY,
                    CBH.BET_MNY,
                    CBH.RSLT_MNY,
                    CBH.TYPE,
                    CBH.REG_DTM,
                    CBH.PRD_ID,
                    CBH.GAME_ID,
                    PI.PRD_NM,
                    GI.GAME_NM
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN KP_PRD_INF PI ON CBH.PRD_ID = PI.PRD_ID
                    LEFT JOIN KP_GAME_INF GI ON CBH.GAME_ID = GI.GAME_ID
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  카지노 배팅 조회 (총판별)
    public static function doCasinoByDistQuery($where_new) {
       
        $sql = "
            SELECT  
                   'S' as type,
                    0 as settlement_point,
                    PRT.idx                                                                      AS dis_idx,          /* 총판 IDX */
                    PRT.id                                                                       AS dis_id,           /* 총판 ID */
                    DATE(CBH.MOD_DTM)                                                            AS date,             /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS take_money,  /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_CSN_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1 
            AND PRT.u_business <> 1" . $where_new;
        
        return $sql;
    }

    //  슬롯 배팅 회원 총수 (회원별)
    public static function doSlotByUserQueryAllCount($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT MB.idx                       
            FROM member MB
                    LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  슬롯 배팅 조회 (회원별)
    public static function doSlotByUserQuery($db_srch_s_date, $db_srch_e_date, $where_new) {


        $sql = "
            SELECT MB.idx                                                                        AS idx,         /* 회원 IDX */
                    MB.id                                                                        AS id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                           AS cr_dt,            /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'L' THEN CBH.RSLT_MNY END), 0)               AS total_lose_money  /* 낙첨금 총계 */
            FROM member MB
            LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

    //  슬롯 배팅 조회 배팅 내역 정보를 가져온다(회원별)
    public static function doSlotByUserBetDetailQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT 
                    MB.id,
                    MB.nick_name,
                    CBH.HLD_MNY,
                    CBH.BET_MNY,
                    CBH.RSLT_MNY,
                    CBH.TYPE,
                    CBH.REG_DTM,
                    CBH.PRD_ID,
                    CBH.GAME_ID,
                    PI.PRD_NM,
                    GI.GAME_NM
            FROM member MB
                    LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN KP_PRD_INF PI ON CBH.PRD_ID = PI.PRD_ID
                    LEFT JOIN KP_GAME_INF GI ON CBH.GAME_ID = GI.GAME_ID
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";

        return $sql;
    }

    //  슬롯 배팅 조회 (총판별)
    public static function doSlotByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {
        
        $sql = "
            SELECT  
                    'S' as type,
                    0 as settlement_point,
                    PRT.idx                                                                      AS dis_idx,           /* 총판 IDX */
                    PRT.id                                                                       AS dis_id,            /* 총판 ID */
                    DATE(CBH.MOD_DTM)                                                            AS date,              /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS take_money,        /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_SLOT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1 AND PRT.u_business <> 1" . $where_new;
        
        return $sql;
    }

    //  이스포츠 배팅 회원 총수 (회원별)
    public static function doEsptByUserQueryAllCount($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT MB.idx                       
            FROM member MB
                    LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(EBH.MOD_DTM)";

        return $sql;
    }

    //  이스포츠 배팅 조회 (회원별)
    public static function doEsptByUserQuery($db_srch_s_date, $db_srch_e_date, $where_new) {


        $sql = "
            SELECT MB.idx                                                                        AS idx,         /* 회원 IDX */
                    MB.id                                                                        AS id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                           AS cr_dt,            /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'L' THEN CBH.RSLT_MNY END), 0)               AS total_lose_money  /* 낙첨금 총계 */
            FROM member MB
                    LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        //$sql .= " GROUP BY MB.idx, DATE(CBH.MOD_DTM)";
        return $sql;
    }

    //  이스포츠 배팅 조회 배팅 내역 정보를 가져온다(회원별)
    public static function doEsptByUserBetDetailQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT 
                    MB.id,
                    MB.nick_name,
                    CBH.HLD_MNY,
                    CBH.BET_MNY,
                    CBH.RSLT_MNY,
                    CBH.TYPE,
                    CBH.REG_DTM,
                    CBH.PRD_ID,
                    CBH.GAME_ID,
                    PI.PRD_NM
            FROM member MB
                    LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN KP_PRD_INF PI ON CBH.PRD_ID = PI.PRD_ID
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;

        return $sql;
    }

    //  이스포츠 배팅 조회(총판별)
    public static function doEsptByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT  
                                                                                                 'S' as type,
                                                                                                 0 as settlement_point,
                    PRT.idx                                                                      AS dis_idx,           /* 총판 IDX */
                    PRT.id                                                                       AS dis_id,            /* 총판 ID */
                    DATE(CBH.MOD_DTM)                                                            AS date,              /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS take_money,        /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN KP_ESPT_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1 AND PRT.u_business <> 1" . $where_new;
        return $sql;
    }

    //  해쉬 배팅 회원 총수 (회원별)
    public static function doHashByUserQueryAllCount($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT MB.idx                       
            FROM member MB
                    LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;

        return $sql;
    }

    //  해쉬 배팅 조회 (회원별)
    public static function doHashByUserQuery($db_srch_s_date, $db_srch_e_date, $where_new) {


        $sql = "
            SELECT MB.idx                                                                        AS idx,         /* 회원 IDX */
                    MB.id                                                                        AS id,          /* 회원 ID */
                    DATE(CBH.MOD_DTM)                                                           AS cr_dt,            /* 일자 */
                    IFNULL(SUM(HBH.BET_MNY), 0)                                                  AS total_bet_money,  /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,  /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'L' THEN CBH.RSLT_MNY END), 0)               AS total_lose_money  /* 낙첨금 총계 */
            FROM member MB
                    LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;
        return $sql;
    }

    //  해쉬 배팅 조회 배팅 내역 정보를 가져온다(회원별)
    public static function doHashByUserBetDetailQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT 
                    MB.id,
                    MB.nick_name,
                    CBH.HLD_MNY,
                    CBH.BET_MNY,
                    CBH.RSLT_MNY,
                    CBH.TYPE,
                    CBH.REG_DTM,
                    '해쉬게임' as PRD_NM
            FROM member MB
                    LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
            WHERE CBH.MOD_DTM >= $db_srch_s_date
            AND CBH.MOD_DTM <= $db_srch_e_date
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1" . $where_new;

        return $sql;
    }

    //  해시 배팅 조회
    public static function doHashByDistQuery($db_srch_s_date, $db_srch_e_date, $where_new) {

        $sql = "
            SELECT  
                                                                                                'S' as type,
                                                                                                0 as settlement_point,
                    PRT.idx                                                                      AS dis_idx,           /* 총판 IDX */
                    PRT.id                                                                       AS dis_id,            /* 총판 ID */
                    DATE(CBH.MOD_DTM)                                                            AS date,              /* 일자 */
                    IFNULL(SUM(CBH.BET_MNY), 0)                                                  AS total_bet_money,   /* 배팅금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS total_win_money,   /* 당첨금 총계 */
                    IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0) AS take_money,        /* 당첨금 총계 */
                    IFNULL( IFNULL(SUM(CBH.BET_MNY), 0)  -  IFNULL(SUM(CASE WHEN CBH.TYPE = 'W' THEN CBH.BET_MNY + CBH.RSLT_MNY END), 0), 0)               AS total_lose_money   /* 차액 총계 */
            FROM member MB
                    LEFT JOIN OD_HASH_BET_HIST CBH ON MB.idx = CBH.MBR_IDX
                    LEFT JOIN member AS PRT ON MB.recommend_member = PRT.idx
            WHERE CBH.MOD_DTM >= ?
            AND CBH.MOD_DTM <= ?
            AND MB.level != 9
            AND CBH.TYPE IN ('W', 'L')
            AND MB.u_business = 1 AND PRT.u_business <> 1" . $where_new;
        return $sql;
    }

    //type 값 :  SPORTS_S,SPORTS_D,REAL_S,REAL_D,MINI,CASINO,SLOT,HOLDEM
    public static function updateChargeBetMoney($member_idx, $type, $bet_money, $model, $logger) {
        $sql = "select idx from member_money_charge_history where member_idx = ? and status = 3 order by update_dt desc limit 1";
        $result = $model->db->query($sql, [$member_idx])->getResultArray();
        if (false === isset($result) || true === empty($result))
            return;

        $idx = $result[0]['idx'];
        switch ($type) {
            case 'SPORTS_S':
                $sql = "UPDATE member_money_charge_history SET sports_bet_s_money = sports_bet_s_money + ? WHERE idx = ?";
                break;
            case 'SPORTS_D':
                $sql = "UPDATE member_money_charge_history SET sports_bet_d_money = sports_bet_d_money + ? WHERE idx = ?";
                break;
            case 'REAL_S':
                $sql = "UPDATE member_money_charge_history SET real_bet_s_money = real_bet_s_money + ? WHERE idx = ?";
                break;
            case 'REAL_D':
                $sql = "UPDATE member_money_charge_history SET real_bet_d_money = real_bet_d_money + ? WHERE idx = ?";
                break;
            case 'MINI':
                $sql = "UPDATE member_money_charge_history SET mini_bet_money = mini_bet_money + ? WHERE idx = ?";
                break;
            case 'CASINO':
                $sql = "UPDATE member_money_charge_history SET casino_bet_money = casino_bet_money + ? WHERE idx = ?";
                break;
            case 'SLOT':
                $sql = "UPDATE member_money_charge_history SET slot_bet_money = slot_bet_money + ? WHERE idx = ?";
                break;
            case 'CLASSIC_S':
                $sql = "UPDATE member_money_charge_history SET classic_bet_s_money = classic_bet_s_money + ? WHERE idx = ?";
                break;
            case 'CLASSIC_D':
                $sql = "UPDATE member_money_charge_history SET classic_bet_d_money = classic_bet_d_money + ? WHERE idx = ?";
                break;
            case 'HOLDEM': 
                $sql = "UPDATE member_money_charge_history SET holdem_bet_money = holdem_bet_money + ? WHERE idx = ?";
                break;

            default:
                $logger->critical("updateChargeBetMoney member_idx :" . $member_idx . ' type =>' . $type);
                return;
        }

        $model->db->query($sql, [$bet_money, $idx]);

        //$logger->info("success updateChargeBetMoney member_idx :" . $member_idx.' type =>'.$type.' sql =>'.$sql);
    }

    public static function decUpdateChargeBetMoney($date, $member_idx, $type, $bet_money, $model, $logger) {
        $sql = "select idx from member_money_charge_history where member_idx = ? and status = 3 and update_dt <= ? order by update_dt desc limit 1";
        $result = $model->db->query($sql, [$member_idx, $date])->getResultArray();
        if (false === isset($result) || true === empty($result))
            return;

        $idx = $result[0]['idx'];
        switch ($type) {
            case 'SPORTS_S':
                $sql = "UPDATE member_money_charge_history SET sports_bet_s_money = sports_bet_s_money - ? WHERE idx = ?";
                break;
            case 'SPORTS_D':
                $sql = "UPDATE member_money_charge_history SET sports_bet_d_money = sports_bet_d_money - ? WHERE idx = ?";
                break;
            case 'REAL_S':
                $sql = "UPDATE member_money_charge_history SET real_bet_s_money = real_bet_s_money - ? WHERE idx = ?";
                break;
            case 'REAL_D':
                $sql = "UPDATE member_money_charge_history SET real_bet_d_money = real_bet_d_money - ? WHERE idx = ?";
                break;
            case 'MINI':
                $sql = "UPDATE member_money_charge_history SET mini_bet_money = mini_bet_money - ? WHERE idx = ?";
                break;
            case 'CASINO':
                $sql = "UPDATE member_money_charge_history SET casino_bet_money = casino_bet_money - ? WHERE idx = ?";
                break;
            case 'SLOT':
                $sql = "UPDATE member_money_charge_history SET slot_bet_money = slot_bet_money - ? WHERE idx = ?";
                break;

            default:
                $logger->critical("decUpdateChargeBetMoney member_idx :" . $member_idx . ' type =>' . $type);
                return;
        }

        $model->db->query($sql, [$bet_money, $idx]);
    }

    public static function initShopCalculateResult($shopConfig, $value, $logger) {
        $dis_idx = $value['member_idx'];
        $shopConfig[$dis_idx] = $value;

        $shopConfig[$dis_idx]['total_point'] = 0;
        $shopConfig[$dis_idx]['low_total_point'] = 0;
        $shopConfig[$dis_idx]['ch_val'] = 0;
        $shopConfig[$dis_idx]['ex_val'] = 0;
        $shopConfig[$dis_idx]['pre_bet_sum_s'] = 0;
        $shopConfig[$dis_idx]['pre_bet_take_s'] = 0;

        $shopConfig[$dis_idx]['pre_bet_sum_d_2'] = 0;
        $shopConfig[$dis_idx]['pre_bet_take_d_2'] = 0;

        $shopConfig[$dis_idx]['pre_bet_sum_d_3'] = 0;
        $shopConfig[$dis_idx]['pre_bet_take_d_3'] = 0;

        $shopConfig[$dis_idx]['pre_bet_sum_d_4'] = 0;
        $shopConfig[$dis_idx]['pre_bet_take_d_4'] = 0;

        $shopConfig[$dis_idx]['pre_bet_sum_d_5_more'] = 0;
        $shopConfig[$dis_idx]['pre_bet_take_d_5_more'] = 0;

        $shopConfig[$dis_idx]['real_bet_sum_s'] = 0;
        $shopConfig[$dis_idx]['real_bet_take_s'] = 0;

        $shopConfig[$dis_idx]['real_bet_sum_d'] = 0;
        $shopConfig[$dis_idx]['real_bet_take_d'] = 0;

        $shopConfig[$dis_idx]['mini_bet_sum'] = 0;
        $shopConfig[$dis_idx]['mini_bet_take'] = 0;

        $shopConfig[$dis_idx]['total_casino_bet_money'] = 0;
        $shopConfig[$dis_idx]['casino_bet_take'] = 0;

        $shopConfig[$dis_idx]['total_slot_bet_money'] = 0;
        $shopConfig[$dis_idx]['slot_bet_take'] = 0;

        $shopConfig[$dis_idx]['total_espt_bet_money'] = 0;
        $shopConfig[$dis_idx]['espt_bet_take'] = 0;

        $shopConfig[$dis_idx]['total_hash_bet_money'] = 0;
        $shopConfig[$dis_idx]['hash_bet_take'] = 0;
        
        $shopConfig[$dis_idx]['pre_classic_bet_sum_s'] = 0;
        $shopConfig[$dis_idx]['pre_classic_bet_take_s'] = 0;
        
        $shopConfig[$dis_idx]['pre_classic_bet_sum_d'] = 0;
        $shopConfig[$dis_idx]['pre_classic_bet_take_d'] = 0;
        
        return $shopConfig;
    }

    // 충전 환전 금액을 가져온다.
    public static function chExMadeQuery($add_where, $logger) {
        $sql = " SELECT 'ch' AS stype
                            ,dis_id
                            ,dis_idx
                            ,DATE(update_dt) AS up_dt
                            ,ifnull(SUM(money), 0) as s_money 
                            FROM (SELECT child.idx,child.id,parent.idx as dis_idx , parent.id  as dis_id
                                    FROM member as child 
                                  left join member as parent  on child.recommend_member = parent.idx  
                                    WHERE child.u_business = 1 AND parent.u_business <> 1 and child.level <> 9) AS T1
                            LEFT JOIN 
                                member_money_charge_history
                            ON 
                                member_money_charge_history.member_idx = T1.idx";
        $sql .= " WHERE update_dt >= ? AND  update_dt <= ? ";
        $sql .= " AND status = 3";
        $sql .= " GROUP BY up_dt, dis_id";
        $sql .= " UNION ALL ";
        $sql .= " SELECT 'ex' AS stype, dis_id, 
                            dis_idx,
                            DATE(update_dt) AS up_dt,
                            ifnull(SUM(money), 0) as s_money 
                            FROM (SELECT child.idx,child.id,parent.idx as dis_idx , parent.id  as dis_id
                                    FROM member as child 
                                  left join member as parent  on child.recommend_member = parent.idx 
                                    WHERE child.u_business = 1 AND parent.u_business <> 1 and child.level <> 9) AS T1
                            LEFT JOIN 
                                member_money_exchange_history
                            ON 
                                member_money_exchange_history.member_idx = T1.idx";
        $sql .= " WHERE update_dt >= ? AND update_dt <= ? AND status = 3 ";
        $sql .= $add_where;

        return $sql;
    }

    public static function sportsRealBetMadeQuery($add_where, $logger) {
        $sql = " SELECT DATE(calculate_dt) AS cr_dt
                    ,T1.dis_id
                    ,parent.idx as dis_idx
                    ,IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 1
                                THEN total_bet_money ELSE 0 END), 0)  AS pre_bet_sum_s,
                     IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 1
                                THEN take_money ELSE 0 END), 0)       AS pre_bet_take_s,

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 2
                                THEN total_bet_money ELSE 0 END), 0)  AS pre_bet_sum_d_2,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 2
                                THEN take_money ELSE 0 END), 0)       AS pre_bet_take_d_2,            

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 3
                                THEN total_bet_money ELSE 0 END), 0)  AS pre_bet_sum_d_3,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 3
                                THEN take_money ELSE 0 END), 0)       AS pre_bet_take_d_3,                         

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 4
                                THEN total_bet_money ELSE 0 END), 0)  AS pre_bet_sum_d_4,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND bet_count = 4
                                THEN take_money ELSE 0 END), 0)       AS pre_bet_take_d_4,                                             

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND 4 < bet_count 
                                THEN total_bet_money ELSE 0 END), 0)  AS pre_bet_sum_d_5_more,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND 4 < bet_count 
                                THEN take_money ELSE 0 END), 0)       AS pre_bet_take_d_5_more,                        

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN total_bet_money ELSE 0 END), 0)   AS real_bet_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'S'
                                THEN take_money ELSE 0 END), 0)        AS real_bet_take_s,

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN total_bet_money ELSE 0 END), 0)   AS real_bet_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 2 AND folder_type = 'D'
                                THEN take_money ELSE 0 END), 0)        AS real_bet_take_d,

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'S' AND is_classic = 'ON'
                                THEN total_bet_money ELSE 0 END), 0)   AS pre_classic_bet_sum_s,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'S' AND is_classic = 'ON'
                                THEN take_money ELSE 0 END), 0)        AS pre_classic_bet_take_s,

                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'D' AND is_classic = 'ON'
                                THEN total_bet_money ELSE 0 END), 0)   AS pre_classic_bet_sum_d,
                    IFNULL(SUM(CASE 
                                WHEN bet_type = 1 AND folder_type = 'D' AND is_classic = 'ON'
                                THEN take_money ELSE 0 END), 0)        AS pre_classic_bet_take_d

                    FROM member AS T1 
                    LEFT JOIN member_bet ON member_bet.member_idx = T1.idx 
                    LEFT JOIN member as parent ON T1.recommend_member = parent.idx 

                    WHERE calculate_dt >= ? AND calculate_dt <= ? AND T1.level <> 9 AND T1.u_business = 1 AND parent.u_business <> 1
                    AND bet_status = 3 AND total_bet_money != take_money " . $add_where;

        return $sql;
    }

    public static function calBetType(&$shopConfig,$dis_idx,$fee_key,$pt_fee_key,$recommend_member,$sum_bet_data,$logger = null){
         if ($shopConfig[$dis_idx][$pt_fee_key] > 0) {
            //$profit_pre_s_fee = $sum_bet_data * ($shopConfig[$dis_idx][$fee_key] * 0.01);
            //$shopConfig[$dis_idx]['total_point'] = $shopConfig[$dis_idx]['total_point'] + $profit_pre_s_fee;
            // 해당 총판의 부모의 싱글 수익 값이 설정되어있으면 부모 - 자식 차이의 비율만큼 자식의 총 합계값에서  수익으로 가져간다 
            $pt_bet_fee = $shopConfig[$dis_idx][$pt_fee_key] - $shopConfig[$dis_idx][$fee_key];
            if (0 < $pt_bet_fee) {
                $pt_profit_fee = $sum_bet_data * ($pt_bet_fee * 0.01);
                $shopConfig[$recommend_member]['low_total_point'] = $shopConfig[$recommend_member]['low_total_point'] + $pt_profit_fee;
                if($logger != null){
                    $logger->info('calBetType dis_idx '.$dis_idx);
                    $logger->info('calBetType recommend_member '.$recommend_member);
                    $logger->info('calBetType recommend_member fee_key '.$pt_profit_fee);
                    $logger->info('calBetType recommend_member pt_fee_key '.$shopConfig[$recommend_member]['low_total_point']);
                    
                }
            }
        }
    }
    
    public static function calLowBetType(&$shopConfig,$dis_idx,$pt_fee_key,$ppt_fee_key,$recommend_member,$profit,$logger = null){
        if(true === isset($shopConfig[$recommend_member]['recommend_member']) && 0 < $shopConfig[$recommend_member]['recommend_member']){
                      
            $pt_recommend_member = $shopConfig[$recommend_member]['recommend_member'];
            $ppt_pre_s_fee = $shopConfig[$pt_recommend_member][$pt_fee_key];
            $pt_pre_s_fee = $shopConfig[$recommend_member][$pt_fee_key];
            $ppt_bet_pre_s_fee = $ppt_pre_s_fee - $pt_pre_s_fee;
            if(0 < $ppt_bet_pre_s_fee){
                $profit_ppt_bet_pre_s_fee = $profit * ($ppt_bet_pre_s_fee * 0.01);
                $shopConfig[$pt_recommend_member]['low_total_point'] = $shopConfig[$pt_recommend_member]['low_total_point'] + $profit_ppt_bet_pre_s_fee;
                
                if($logger != null){
                    $logger->info('calLowBetType dis_idx '.$dis_idx);
                    $logger->info('calLowBetType recommend_member '.$recommend_member);
                    $logger->info('calLowBetType pt_recommend_member '.$pt_recommend_member);
                    $logger->info('calLowBetType ppt_pre_s_fee '.$ppt_pre_s_fee);
                    $logger->info('calLowBetType pt_pre_s_fee '.$pt_pre_s_fee);
                    $logger->info('calLowBetType ppt_bet_pre_s_fee '.$ppt_bet_pre_s_fee);
                    $logger->info('calLowBetType $profit_ppt_bet_pre_s_fee data '. $profit_ppt_bet_pre_s_fee);
                    $logger->info('calLowBetType low_total_point data '. $shopConfig[$pt_recommend_member]['low_total_point']);
              
                }
            }
            
            
            

        }
    }
        
    public static function calSportsRealBet($shopConfig, $dis_idx, $bet_data, $logger) {
        $profit_pre_s_fee = $profit_pre_d_2_fee = $profit_pre_d_3_fee = $profit_pre_d_4_fee = $profit_pre_d_5_more_fee = $profit_real_s_fee = $profit_real_d_fee =  0;
        $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
        // 베팅롤링 정산
        
        // 프리매치 싱글 수익 정산 
        if(0 < $shopConfig[$dis_idx]['bet_pre_s_fee'] && 0 < $bet_data['pre_bet_sum_s']){
            $value = $bet_data['pre_bet_sum_s'] * ($shopConfig[$dis_idx]['bet_pre_s_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
                
        Calculate::calBetType($shopConfig,$dis_idx,'bet_pre_s_fee','pt_bet_pre_s_fee',$recommend_member,$bet_data['pre_bet_sum_s']);
        // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_pre_s_fee', 'pt_bet_pre_s_fee', $recommend_member, $bet_data['pre_bet_sum_s']);
                
        // 프리매치 2폴더 수익 정산 
        if(0 < $shopConfig[$dis_idx]['bet_pre_d_2_fee'] && 0 < $bet_data['pre_bet_sum_d_2']){
            $value = $bet_data['pre_bet_sum_d_2'] * ($shopConfig[$dis_idx]['bet_pre_d_2_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
        
        Calculate::calBetType($shopConfig,$dis_idx,'bet_pre_d_2_fee','pt_bet_pre_d_2_fee',$recommend_member,$bet_data['pre_bet_sum_d_2']);
        // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_pre_d_2_fee', 'pt_bet_pre_d_2_fee', $recommend_member, $bet_data['pre_bet_sum_d_2']);
        
        // 프리매치 3폴더 수익 정산 
        if(0 < $shopConfig[$dis_idx]['bet_pre_d_3_fee'] && 0 < $bet_data['pre_bet_sum_d_3']){
            $value = $bet_data['pre_bet_sum_d_3'] * ($shopConfig[$dis_idx]['bet_pre_d_3_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
        
        Calculate::calBetType($shopConfig,$dis_idx,'bet_pre_d_3_fee','pt_bet_pre_d_3_fee',$recommend_member,$bet_data['pre_bet_sum_d_3']);
        // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_pre_d_3_fee','pt_bet_pre_d_3_fee',$recommend_member,$bet_data['pre_bet_sum_d_3']);
        
        // 프리매치 4폴더 수익 정산 
        if(0 < $shopConfig[$dis_idx]['bet_pre_d_4_fee'] && 0 < $bet_data['pre_bet_sum_d_4']){
            $value = $bet_data['pre_bet_sum_d_4'] * ($shopConfig[$dis_idx]['bet_pre_d_4_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
        Calculate::calBetType($shopConfig,$dis_idx,'bet_pre_d_4_fee','pt_bet_pre_d_4_fee',$recommend_member,$bet_data['pre_bet_sum_d_4']);
        // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_pre_d_4_fee','pt_bet_pre_d_4_fee',$recommend_member,$bet_data['pre_bet_sum_d_4']);
        
        // 프리매치 5폴더 수익 정산 
        if(0 < $shopConfig[$dis_idx]['bet_pre_d_5_more_fee'] && 0 < $bet_data['pre_bet_sum_d_5_more']){
            $value = $bet_data['pre_bet_sum_d_5_more'] * ($shopConfig[$dis_idx]['bet_pre_d_5_more_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
        Calculate::calBetType($shopConfig,$dis_idx,'bet_pre_d_5_more_fee','pt_bet_pre_d_5_more_fee',$recommend_member,$bet_data['pre_bet_sum_d_5_more']);
        // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_pre_d_5_more_fee','pt_bet_pre_d_5_more_fee',$recommend_member,$bet_data['pre_bet_sum_d_5_more']);
        
        // 실시간 싱글폴더 정산 
        if(0 < $shopConfig[$dis_idx]['bet_real_s_fee'] && 0 < $bet_data['real_bet_sum_s']){
            $value = $bet_data['real_bet_sum_s'] * ($shopConfig[$dis_idx]['bet_real_s_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
        Calculate::calBetType($shopConfig,$dis_idx,'bet_real_s_fee','pt_bet_real_s_fee',$recommend_member,$bet_data['real_bet_sum_s']);
        // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_real_s_fee','pt_bet_real_s_fee',$recommend_member,$bet_data['real_bet_sum_s']);
        
        // 실시간 다폴더 정산 
        if(0 < $shopConfig[$dis_idx]['bet_real_d_fee'] && 0 < $bet_data['real_bet_sum_d']){
            $value = $bet_data['real_bet_sum_d'] * ($shopConfig[$dis_idx]['bet_real_d_fee'] * 0.01);
            $shopConfig[$dis_idx]['total_point'] += $value; 
        }
        
        Calculate::calBetType($shopConfig,$dis_idx,'bet_real_d_fee','pt_bet_real_d_fee',$recommend_member,$bet_data['real_bet_sum_d']);
       // the Grand Distributor's Settlement
        Calculate::calLowBetType($shopConfig,$dis_idx,'bet_real_d_fee','pt_bet_real_d_fee',$recommend_member,$bet_data['real_bet_sum_d']);
        
        //스포츠 배팅금 설정
        $shopConfig[$dis_idx]['pre_bet_sum_s'] = $bet_data['pre_bet_sum_s'];
        $shopConfig[$dis_idx]['pre_bet_sum_d_2'] = $bet_data['pre_bet_sum_d_2'];
        $shopConfig[$dis_idx]['pre_bet_sum_d_3'] = $bet_data['pre_bet_sum_d_3'];
        $shopConfig[$dis_idx]['pre_bet_sum_d_4'] = $bet_data['pre_bet_sum_d_4'];
        $shopConfig[$dis_idx]['pre_bet_sum_d_5_more'] = $bet_data['pre_bet_sum_d_5_more'];

        // 실시간 배팅금 설정
        $shopConfig[$dis_idx]['real_bet_sum_s'] = $bet_data['real_bet_sum_s'];
        $shopConfig[$dis_idx]['real_bet_sum_d'] = $bet_data['real_bet_sum_d'];

        //스포츠 당첨금  설정
        $shopConfig[$dis_idx]['pre_bet_take_s'] = $bet_data['pre_bet_take_s'];
        $shopConfig[$dis_idx]['pre_bet_take_d_2'] = $bet_data['pre_bet_take_d_2'];
        $shopConfig[$dis_idx]['pre_bet_take_d_3'] = $bet_data['pre_bet_take_d_3'];
        $shopConfig[$dis_idx]['pre_bet_take_d_4'] = $bet_data['pre_bet_take_d_4'];
        $shopConfig[$dis_idx]['pre_bet_take_d_5_more'] = $bet_data['pre_bet_take_d_5_more'];

        //실시간 당첨금  설정
        $shopConfig[$dis_idx]['real_bet_take_s'] = $bet_data['real_bet_take_s'];
        $shopConfig[$dis_idx]['real_bet_take_d'] = $bet_data['real_bet_take_d'];

        // 클래식 배팅금 설정 
        $shopConfig[$dis_idx]['pre_classic_bet_sum_s'] = $bet_data['pre_classic_bet_sum_s'];
        $shopConfig[$dis_idx]['pre_classic_bet_sum_d'] = $bet_data['pre_classic_bet_sum_d'];
        
        // 클래식 당첨금 설정 
        $shopConfig[$dis_idx]['pre_classic_bet_take_s'] = $bet_data['pre_classic_bet_take_s'];
        $shopConfig[$dis_idx]['pre_classic_bet_take_d'] = $bet_data['pre_classic_bet_take_d'];
                
        return $shopConfig;
    }

    public static function miniGameBetMadeQuery($add_where, $logger) {
        $sql = " SELECT DATE(calculate_dt) AS cr_dt
                        ,dis_id
                        ,dis_idx
                        ,IFNULL(SUM(total_bet_money), 0) as mini_bet_sum
                        ,IFNULL(SUM(take_money), 0)      as mini_bet_take
                        FROM (
                           SELECT child.idx,child.id,parent.idx as dis_idx , parent.id  as dis_id
                                FROM member as child 
                           left join member as parent  on child.recommend_member = parent.idx  
                                WHERE child.u_business = 1 AND parent.u_business <> 1 and child.level <> 9) AS T1
                        LEFT JOIN 
                            mini_game_member_bet
                        ON 
                            mini_game_member_bet.member_idx = T1.idx";
        $sql .= " WHERE calculate_dt >= ? AND calculate_dt <= ?  AND bet_status = 3 AND total_bet_money != take_money ";
        $sql .= " AND (CASE WHEN bet_type = 6 THEN T1.idx <> ?  
                           ELSE 1 = 1 END)" . $add_where;
        return $sql;
    }

    public static function getDistShopInfo() {
        $sql = "
           SELECT shop_config.*
            ,ifnull(parent_shop.bet_pre_s_fee, 0)        as pt_bet_pre_s_fee
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

    public static function getMadeAddBetQuery() {
        $sql = "SELECT IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                bet.fixture_id,bet.bet_id,
                bet.markets_id,
                fix.fixture_sport_id,
                fix.livescore,
                fix.fixture_location_id,
                fix.fixture_league_id,
                fix.fixture_participants_1_id,
                fix.fixture_participants_2_id,
                bet.admin_bet_status,
                fix.admin_bet_status as fix_admin_bet_status,
                rule.end_time,
                rule.max_dividend,
                IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                bet.bet_name,
                bet.bet_base_line,
                IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                fix.fixture_status,
                sports.name  as fixture_sport_name,
                locations.name as fixture_location_name,
                leagues.display_name as fixture_league_name,
                p1.team_name as fixture_participants_1_name, p2.team_name as fixture_participants_2_name,
                leagues.quarter_time,
                markets.not_display_period,
                markets.not_display_time,
                markets.not_display_score,
                markets.limit_bet_price,
                markets.max_bet_price
            FROM lsports_bet as bet 
            LEFT JOIN lsports_fixtures as fix 
                ON bet.fixture_id = fix.fixture_id 
            LEFT JOIN lsports_leagues as leagues 
                ON fix.fixture_league_id = leagues.id 
            LEFT JOIN lsports_sports as sports 
                ON fix.fixture_sport_id = sports.id
            LEFT JOIN lsports_locations as locations 
                ON fix.fixture_location_id = locations.id

            LEFT JOIN    lsports_participant as p1
                ON   fix.fixture_participants_1_id = p1.fp_id
            LEFT JOIN    lsports_participant as p2
                ON   fix.fixture_participants_2_id = p2.fp_id

            LEFT JOIN base_rule as rule 
                ON rule.bet_type = ?
            LEFT JOIN lsports_markets as markets 
                ON bet.markets_id = markets.id
            WHERE bet.fixture_id = ? 
            AND bet.markets_id = ? 
            AND bet.bet_base_line = ? 
            AND bet.bet_name = ? 
            AND bet.bet_type = ?

            AND fix.fixture_start_date = ? 
            AND fix.bet_type = ? 
            AND markets.bet_group = ? 
            AND markets.is_delete = 0 
            AND markets.sport_id = fix.fixture_sport_id
            GROUP BY fix.fixture_id,bet.markets_id;";

        return $sql;
    }
    
     public static function doResultProcessing($bet_type, $logger) {
        $memberBetDetailModel = new MemberBetDetailModel();
        try {
            $logger->error("doResultProcessing start type ==> " . $bet_type);
            $memberBetDetailModel->db->transStart();
            $arrMbBetResult = $memberBetDetailModel->SelectMemberBetResultProcessing($bet_type); // 1인값이 있는지 체크한다.

            if (true == empty($arrMbBetResult) || !isset($arrMbBetResult) || 0 === count($arrMbBetResult)) {
                $memberBetDetailModel->db->transComplete();
                return;
            }
        
            // member_bet.bet_status 1 : 정산전,3 : 정산 완료,5 : 취소
            // member_bet_detail.bet_status 1 : 정산전, 2 : 적중 ,4 : 낙첨 ,5 : 취소 ,6: 적특
            // 정산 안된 데이터만 가져온다. 

            $arr_in_st_mk = array(6, 9, 22, 23, 16, 95, 472, 1328, 1327, 1326, 1332, 1832, 2402, 1677);
            //$arr_in_e_st_st_mk = array(52, 202);
            $arr_in_e_st_st_mk = array(); // 이스포츠는 수동정산이다 
            $arr_in_real_st_mk = array(1, 2, 13, 17, 28, 52, 101, 102, 220, 221, 226, 342, 464);    // 실시간 연장포함 마켓,12 2nd Half Including Overtime

            foreach ($arrMbBetResult as $value) {

                if (ESPORTS == $value['fixture_sport_id'])
                    continue; // 이스포츠 경기는 자동정산에서 제외한다.
                $bet_status = 1;
                //$value['result_extra'] = 0;
                $bet_price = $value['bet_price'];

                list($bet_status, $bet_price, $value['live_results_p1'], $value['live_results_p2']) = BetDataUtil::getBetStatus($value, $logger); // 2,4,6중 하나의 값을 갖는다.
                //$value['result_extra'] = json_decode($value['livescore']);

                if (1 == $bet_status)
                    continue;

                if ((2 == $bet_type && false == in_array($value['ls_markets_id'], $arr_in_real_st_mk)) ||
                        (1 == $bet_type && true == in_array($value['ls_markets_id'], $arr_in_st_mk)) ||
                        (1 == $bet_type && ESPORTS == $value['fixture_sport_id'] && true == in_array($value['ls_markets_id'], $arr_in_e_st_st_mk))
                ) {
                    switch ($value['bet_settlement']) {

                        case -1: // 취소
                            $bet_status = 1; // 취소된 경기는 수동정산처리를 하자 .
                            break;

                        case 0: // 경기중
                            if (2 == $bet_type && 1 != $bet_status) {
                                break;
                            }
                            $bet_status = 1;
                            break;
                        case 1: // 낙첨
                            if (4 != $bet_status) {
                                $bet_status = 1;
                                break;
                            }

                            $bet_status = 4;
                            break;
                        case 2: // 적중
                            if (2 != $bet_status) {
                                $bet_status = 1;
                                break;
                            }

                            $bet_status = 2;
                            break;
                        case 3: // 적특
                            $bet_status = 6;
                            break;
                        default :
                            $bet_status = 1;
                            break;
                    }
                }

                if (1 == $bet_status)
                    continue;

                //$logger->info('doResultProcessing market_id : ' . $value['ls_markets_id'] . ' bet_status :' . $bet_status . ' bet_price: ' . $bet_price . " idx :" . $value['idx']);
                if (true === isset($value['live_results_p1']) && true === isset($value['live_results_p2'])) {
                    $array_result_score = array('live_results_p1' => $value['live_results_p1'], 'live_results_p2' => $value['live_results_p2']);
                    $array_result_score = json_encode($array_result_score);
                } else {
                    $array_result_score = '';
                }

                $logger->info('doResultProcessing array_result_score : ' . json_encode($value));

             
                $memberBetDetailModel->UpdateMemberBetDetail($value['idx'], $bet_status, $array_result_score); // member_bet_detail             }
            }

            $memberBetDetailModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $logger->error('doResultProcessing [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: doTotalCalculate query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
        } catch (\Exception $e) {
            $logger->error('::::::::::::::: error doResultProcessing Exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->info(':::::::::::::::  error doResultProcessing ls_fixture_id : ' . $value['ls_fixture_id'] . ' idx ' . $value['idx'] . ' ls_markets_id ' . $value['ls_markets_id']);
            $logger->error('::::::::::::::: error doResultProcessing query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
            return;
        } catch (\ReflectionException $e) {
            $logger->error('::::::::::::::: doResultProcessing ReflectionException : ' . $e);
            $memberBetDetailModel->db->transRollback();
        }
    }
    
    public static function doResultProcessing_old($bet_type, $logger) {
        $memberBetDetailModel = new MemberBetDetailModel();

        try {
            $memberBetDetailModel->db->transStart();

            $arrMbBetResult = $memberBetDetailModel->SelectMemberBetResultProcessing($bet_type); // 1인값이 있는지 체크한다.


            if (true == empty($arrMbBetResult) || !isset($arrMbBetResult) || 0 === count($arrMbBetResult)) {
                $memberBetDetailModel->db->transComplete();
                return;
            }


            // member_bet.bet_status 1 : 정산전,3 : 정산 완료,5 : 취소
            // member_bet_detail.bet_status 1 : 정산전, 2 : 적중 ,4 : 낙첨 ,5 : 취소 ,6: 적특
            // 정산 안된 데이터만 가져온다. 

            $arr_in_st_mk = array(6, 9, 22, 23, 16, 95, 472, 1328, 1327, 1832, 2402, 1677);
            //$arr_in_e_st_st_mk = array(52, 202);
            $arr_in_e_st_st_mk = array(); // 이스포츠는 수동정산이다 
            $arr_in_real_st_mk = array(1, 2, 13, 17, 28, 52, 101, 102, 220, 221, 226, 342, 464);    // 실시간 연장포함 마켓,12 2nd Half Including Overtime

            foreach ($arrMbBetResult as $value) {

                if (ESPORTS == $value['fixture_sport_id'])
                    continue; // 이스포츠 경기는 자동정산에서 제외한다.
                $bet_status = 1;
                //$value['result_extra'] = 0;
                $bet_price = $value['bet_price'];

                list($bet_status, $bet_price, $value['live_results_p1'], $value['live_results_p2']) = BetDataUtil::getBetStatus($value, $logger); // 2,4,6중 하나의 값을 갖는다.
                //$value['result_extra'] = json_decode($value['livescore']);

                if (1 == $bet_status)
                    continue;

                if ((2 == $bet_type && false == in_array($value['ls_markets_id'], $arr_in_real_st_mk)) ||
                        (1 == $bet_type && true == in_array($value['ls_markets_id'], $arr_in_st_mk)) ||
                        (1 == $bet_type && ESPORTS == $value['fixture_sport_id'] && true == in_array($value['ls_markets_id'], $arr_in_e_st_st_mk))
                ) {
                    switch ($value['bet_settlement']) {

                        case -1: // 취소
                            $bet_status = 1; // 취소된 경기는 수동정산처리를 하자 .
                            break;

                        case 0: // 경기중
                            if (2 == $bet_type && 1 != $bet_status) {
                                break;
                            }
                            $bet_status = 1;
                            break;
                        case 1: // 낙첨
                            if (4 != $bet_status) {
                                $bet_status = 1;
                                break;
                            }

                            $bet_status = 4;
                            break;
                        case 2: // 적중
                            if (2 != $bet_status) {
                                $bet_status = 1;
                                break;
                            }

                            $bet_status = 2;
                            break;
                        case 3: // 적특
                            $bet_status = 6;
                            break;
                        default :
                            $bet_status = 1;
                            break;
                    }
                }

                if (1 == $bet_status)
                    continue;

                //$logger->info('doResultProcessing market_id : ' . $value['ls_markets_id'] . ' bet_status :' . $bet_status . ' bet_price: ' . $bet_price . " idx :" . $value['idx']);
                if (true === isset($value['live_results_p1']) && true === isset($value['live_results_p2'])) {
                    $array_result_score = array('live_results_p1' => $value['live_results_p1'], 'live_results_p2' => $value['live_results_p2']);
                    $array_result_score = json_encode($array_result_score);
                } else {
                    $array_result_score = '';
                }

                //$logger->info('doResultProcessing array_result_score : ' . json_encode($array_result_score) . ' bet_price : ' . $bet_price);
                $memberBetDetailModel->UpdateMemberBetDetail($value['idx'], $bet_status, $array_result_score); // member_bet_detail 
            }

            $memberBetDetailModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $logger->error('doResultProcessing [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: doTotalCalculate query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
        }
    }
    // 수동적특한 배당은 자동 정산시 해당유저한테 배팅금액을 돌려준다.
    public static function renew_doTotalCalculate($bet_type, $logger) {
        $memberModel = new MemberModel();
        $memberBetModel = new MemberBetModel();
        $memberBetDetailModel = new MemberBetDetailModel();
        $tLogCashModel = new TLogCashModel();
        try {
            $logger->info("renew_doTotalCalculate start type ==> " . $bet_type);

            $gmPt = null;
            if ('K-Win' == config(App::class)->ServerName) {
                $gmPt = new KwinGmPt();
            } else if ('Gamble' == config(App::class)->ServerName) {
                $gmPt = new GambelGmPt();
            } else if ('BetGo' == config(App::class)->ServerName) {
                $gmPt = new BetGoGmPt();
            } else if ('CHOSUN' == config(App::class)->ServerName) {
                $gmPt = new ChoSunGmPt();
            } else if ('BETS' == config(App::class)->ServerName) {
                $gmPt = new BetsGmPt();
            }

            $arr_config = $gmPt->getConfigData();

            $memberBetDetailModel->db->transStart();
            $sql = "SELECT member.money,member_bet.* FROM member_bet 
                    left join member on member.idx = member_bet.member_idx
                    WHERE member_bet.bet_status = 1 AND member_bet.bet_type = $bet_type";
            $arrResult = $memberBetDetailModel->db->query($sql)->getResultArray();

            if (true == empty($arrResult) || !isset($arrResult) || 0 === count($arrResult)) {
                $memberBetDetailModel->db->transComplete();
                //$logger->debug("************************empty****************************");
                return;
            }

            foreach ($arrResult as $valueMbBet) {

                $bet_idx = $valueMbBet['idx'];
                $member_idx = $valueMbBet['member_idx'];
                $total_bet_money = $valueMbBet['total_bet_money'];
                $folder_type = $valueMbBet['folder_type'];
                $bonus_price = $valueMbBet['bonus_price'];
                $item_idx = true === isset($valueMbBet['item_idx']) && false === empty($valueMbBet['item_idx']) ? $valueMbBet['item_idx'] : 0;

                $resultDetailBet = $memberBetDetailModel->SelectMemberBetDetail($bet_idx);
                if (false === isset($resultDetailBet) || true === empty($resultDetailBet) || 1 != $resultDetailBet[0]['mb_bet_status'])
                    continue;

                $bet_total_count = count($resultDetailBet);

                list($retval, $total_bet_price, $win_limit_price_count, $win_count, $lose_count) = $gmPt->checkGameResult($resultDetailBet, $arr_config, $bet_total_count, $logger);

                if (1 == $bet_type) {
                    $a_comment = 'prematch ==>';
                } else if (2 == $bet_type) {
                    $a_comment = 'inplay ==>';
                }

                // 처리한 데이터
                $sql = "SELECT money FROM member WHERE idx = ? for update ";
                $arrResultMbBet = $memberBetDetailModel->db->query($sql, [$member_idx])->getResultArray();
                $money = $arrResultMbBet[0]['money'];
                //$money = $valueMbBet['money'];
                $ukey = md5($member_idx . strtotime('now'));

                if (0 < $lose_count) { // 낙첨처리 
                    $gmPt->doLose($valueMbBet, $resultDetailBet, $ukey, $money, $bet_total_count, $win_count, $lose_count, $a_comment, $bet_type
                            , $memberModel, $tLogCashModel, $memberBetModel, $logger);
                    // 
                    continue;
                }

                if (false == $retval) {
                    continue;
                }

                list($total_bet_price, $bonus_price) = $gmPt->calBonusPrice($total_bet_price, $bonus_price, $folder_type, $win_limit_price_count, $arr_config);

                $gm_bonus = $gmPt->useItemAllocation($memberBetModel, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_price, $logger);

                $total_bet_price = $total_bet_price + $gm_bonus;
                $take_money = sprintf('%0.2f', $total_bet_price * $total_bet_money);

                // 해당 데이터를 업데이트 한다.
                // member 의 머니 업데이트를 해줘야 한다.
                //if ($take_money > 0) {
                $p_data['sql'] = "update member set money = money + ? where idx = ? ";
                $memberModel->db->query($p_data['sql'], [$take_money, $member_idx]);

                //}

                $memberBetModel->UpdateMemberBetBonus($bet_idx, 3, $take_money, $bonus_price, $item_idx, 0, 0, $gm_bonus);

                if (0 < $gm_bonus) {
                    $a_comment .= "배당패치 적중";
                    $tLogCashModel->insertCashLog_mem_idx($ukey, $member_idx, AC_GM_ALLOCATION_MONEY, $bet_idx, $take_money, $money, 'P', $a_comment);
                } else {
                    $a_comment .= " 적중";
                    $tLogCashModel->insertCashLog_mem_idx($ukey, $member_idx, 7, $bet_idx, $take_money, $money, 'P', $a_comment);
                }
            }

            $logger->info("renew_doTotalCalculate end");
            $memberBetDetailModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $logger->error('renew_doTotalCalculate [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: renew_doTotalCalculate query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
        }
    }
    // 미니게임 정산(테이블 변경)
    public static function doMiniTotalCalculate_renew($logger) {
        //$logger->error(" doMiniTotalCalculate_renew start: ");
        $memberModel = new MemberModel();
        //$memberBetModel = new MemberBetModel();
        $MiniGameMemberBetModel = new MiniGameMemberBetModel();
        $tLogCashModel = new TLogCashModel();
        
        $sql = "SELECT set_type_val FROM t_game_config where set_type = 'test_expt'";
        $arrConfig = $MiniGameMemberBetModel->db->query($sql)->getResultArray();
        $arrTestExpt = explode(',', $arrConfig[0]['set_type_val']);
        
        $sql = "SELECT 
              mini_mb_bt.idx as mb_bt_idx,
              mini_mb_bt.member_idx,
              mini_mb_bt.bet_type,
              mini_mb_bt.bet_status as mb_bt_bet_status,
              mini_mb_bt.total_bet_money,
              mini_mb_bt.idx,
              mini_mb_bt.ls_fixture_id,
              mini_mb_bt.ls_markets_id,
              mini_mb_bt.ls_markets_name,
              mini_mb_bt.bet_status as mb_bt_dt_bet_status,
              mini_mb_bt.bet_price,
              mini_mb_bt.bet_other_price,
              mini_mb_bt.bet_other_price_draw,
              mini_mb_bt.create_dt,
              game.result,
              game.result_score,
              game.game,
              mb.money
      	    FROM mini_game_member_bet as mini_mb_bt
            LEFT JOIN mini_game as game ON mini_mb_bt.ls_fixture_id = game.id
            LEFT JOIN member as mb ON mb.idx = mini_mb_bt.member_idx
          WHERE 
            mini_mb_bt.bet_status = 1
            AND mini_mb_bt.bet_type IN (3,4,5,6,15)
            AND mini_mb_bt.bet_type = game.bet_type AND game.result is not null
            group by mini_mb_bt.idx";

        //$logger->debug('doMiniTotalCalculate : ' . $sql);
        try {
            $MiniGameMemberBetModel->db->transStart();
            $arrResult = $MiniGameMemberBetModel->db->query($sql)->getResultArray();

            if (true == empty($arrResult) || !isset($arrResult) || 0 === count($arrResult)) {
                $MiniGameMemberBetModel->db->transComplete();
                return;
            }

            // mb_bt_idx 
            foreach ($arrResult as $key => $value) {
                // 이미 처리되었다.

                if (null == $value['result'])
                    continue;

                $data_object = json_decode($value['result']);
                if ($value['result_score'] != '') {
                    $data_object = json_decode($value['result_score']);
                }

                list($bet_status, $bet_price) = BetDataUtil::getMiniBetStatus($value, $data_object, $logger); // 2,4,6중 하나의 값을 갖는다.
                if (1 === $bet_status)
                    continue;

                // 해당 데이터를 업데이트 한다.
                $result_score = '';
                $member_idx = $value['member_idx'];
                $logMarktesName = $value['ls_markets_name'];

                $ukey = md5($member_idx . strtotime('now'));
                if ($bet_status === 4) { // 낙첨시 주는 포인트 lose_self_per,lose_recomm_per
                                        if(6 == $value['bet_type'] && in_array($member_idx, $arrTestExpt)){
                        $otherMarketsId = $value['ls_markets_id'];
                        if("ou" == $data_object->type){
                            if('오버' == $value['ls_markets_name']){
                                $logMarktesName = '언더';
                                $otherMarketsId = 13005;
                            }else{
                                $logMarktesName = '오버';
                                $otherMarketsId = 13004;
                            }
                            $value['bet_price'] = $value['bet_other_price'];
                        }else{
                            // 승
                            if ($data_object->scorea < $data_object->scoreh) {
                                $logMarktesName = '승';
                                $otherMarketsId = 13001;
                                if(13002 == $value['ls_markets_id']){
                                    $value['bet_price'] = $value['bet_other_price'];
                                }else if(13003 == $value['ls_markets_id']){
                                    $value['bet_price'] = $value['bet_other_price'];
                                }
                            // 무
                            }else if ($data_object->scorea == $data_object->scoreh) {
                                $logMarktesName = '무';
                                $otherMarketsId = 13002;
                                if(13001 == $value['ls_markets_id']){
                                    $value['bet_price'] = $value['bet_other_price_draw'];
                                }else if(13003 == $value['ls_markets_id']){
                                    $value['bet_price'] = $value['bet_other_price_draw'];
                                }
                            }else{
                                $logMarktesName = '패';
                                $otherMarketsId = 13003;
                                if(13001 == $value['ls_markets_id']){
                                    $value['bet_price'] = $value['bet_other_price'];
                                }else if(13002 == $value['ls_markets_id']){
                                    $value['bet_price'] = $value['bet_other_price_draw'];
                                }
                            }
                        }
                        
                        $p_data['sql'] = "update mini_game_member_bet set bet_price = ".$value['bet_price'].","
                                . " ls_markets_id = $otherMarketsId, ls_markets_name = '$logMarktesName' where idx = ".$value['mb_bt_idx'];
                        $MiniGameMemberBetModel->db->query($p_data['sql']);
                    }else{
                        //$memberModel->log_lose_bet_bonus_point($member_idx, $value['total_bet_money']);
                        $MiniGameMemberBetModel->UpdateMemberMiniGameBet($value['mb_bt_idx'], 3, 0);

                        $a_comment = "정산 낙첨 [" . $value['game'] . "] " . $value['ls_fixture_id'] . " " . $value['ls_markets_name'] . " ";

                        $a_comment = addslashes($a_comment);

                        $tLogCashModel->insertCashLog_mem_idx($ukey, $member_idx, 7, $value['mb_bt_idx'], 0, $value['money'], 'R', $a_comment);
                        continue;
                    }
                }

                // 결과가 다 반영안됐다 
                // 1, 2, 3, 7, 52, 101, 102 => 승무패,오버언더,핸디캡,더블찬스,승패,홈팀 오버언더,원정팀 오버언더
                $take_money = round($value['bet_price'], 2) * $value['total_bet_money'];

                // 해당 데이터를 업데이트 한다.
                $MiniGameMemberBetModel->UpdateMemberMiniGameBet($value['mb_bt_idx'], 3, $take_money);

                // member 의 머니 업데이트를 해줘야 한다.
                if ($take_money > 0) {
                    $p_data['sql'] = "update member set money = money + $take_money where idx = $member_idx";
                    $memberModel->db->query($p_data['sql']);
                    
                    //$logger->debug($p_data['sql']);
                }

                /* 1:충전,2:환전,3:베팅,4:베팅취소,5:포인트전환(추가),6:포인트차감,10:포인트충전,7:베팅결과처리,8:이벤트충전,9:이벤트차감,101:충전요청,102:환전요청,103:계좌조회,
                 * 111:충전요청취소,112:환전요청취소,113:충전취소,114:환전취소,121:관리자충전,122:관리자회수,123:관리자 포인트 충전, 124:관리자 포인트 회수,998:데이터복구,999:기타 
                 */
                $a_comment = "정산 적중 [" . $value['game'] . "] " . $value['ls_fixture_id'] . " " . $logMarktesName . " ";

                if (6 === $value['bet_type']) {
                    $a_comment .= $data_object->home . " VS " . $data_object->away;
                }

                $a_comment = addslashes($a_comment);

                $tLogCashModel->insertCashLog_mem_idx($ukey, $member_idx, 7, $value['mb_bt_idx'], $take_money, $value['money'], 'P', $a_comment);
                //$totalMemberCashModel = new TotalMemberCashModel();
                //$totalMemberCashModel->insertBetTakeMoneyUpdate($value['create_dt'], $value['member_idx'], $value['bet_type'], '', $value['total_bet_money'], $take_money, $logger);
            }
            $MiniGameMemberBetModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $logger->error('doMiniTotalCalculate [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: doMiniTotalCalculate memberBetDetailModel query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
        }
    }

    public static function distributorCalculate($logger, $db_srch_s_date, $db_srch_e_date) {
        // 날짜 차이가 하루이상이면 에러처리한다.
        $check_s_date = date_create($db_srch_s_date);
        $check_e_date = date_create($db_srch_e_date);
        $diff = date_diff($check_e_date, $check_s_date);
        if ($diff->days > 1) {
            $logger->critical('------------- distributorCalculate empty shopConfig diff->days ==>' . $diff->days);
            return;
        }

        $memberModel = new MemberModel();

        // 정산 요율 값을 얻어온다. kwin,asbet
        // 설정 안된 총판도 값은 넣어준다.
        $sql = Calculate::getDistShopInfo();
        $result_shop = $memberModel->db->query($sql)->getResultArray();

        $shopConfig = [];
        // 총판 member_idx 값으로 배열 정렬
        foreach ($result_shop as $key => $value) {
            $shopConfig = Calculate::initShopCalculateResult($shopConfig, $value, $logger);
        }

        // 정산 제외 유저 정보 가져오기(미니게임 정산에서 해당 유저 제외)
        $excluded_member_idx = 0;

        // 충전 환전
        $add_where = " GROUP BY up_dt,dis_id";
        $sql = Calculate::chExMadeQuery($add_where, $logger);
        $db_dataArr = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date, $db_srch_s_date, $db_srch_e_date])->getResultArray();
        //$logger->info('------------- distributorCalculate ch_ex sql ==>' . $memberModel->getLastQuery());
        //$logger->debug('------------- distributorCalculate ch_ex db_dataArr ==>' . json_encode($db_dataArr));
        // 총판별 정산
        $moneyInfo = array();
        $db_dataArr = true === isset($db_dataArr) ? $db_dataArr : [];
        // dis_idx 값으로 정렬
        foreach ($db_dataArr as $row) {
            $dis_idx = $row['dis_idx'];
            $moneyInfo[$dis_idx]['dis_idx'] = $dis_idx;
            $moneyInfo[$dis_idx]['up_dt'] = $row['up_dt'];
            $moneyInfo[$dis_idx]['dis_id'] = $row['dis_id'];

            if ($row['stype'] == 'ch') {
                $moneyInfo[$dis_idx]['ch_val'] = $row['s_money'];
            } elseif ($row['stype'] == 'ex') {
                $moneyInfo[$dis_idx]['ex_val'] = $row['s_money'];
            }
        }

        //$logger->info('------------- distributorCalculate ch_ex moneyInfo ==>' . json_encode($moneyInfo));

        // 죽장
        if (false === empty($moneyInfo) && true === isset($moneyInfo) && count($moneyInfo) > 0) {
            // dis_idx 값으로 정렬해서 모든 총판의 입출 죽장금을 누적한다.
            foreach ($moneyInfo as $key => $value) {
                $dis_idx = $value['dis_idx'];

                if (!isset($shopConfig[$dis_idx])) {
                    $logger->critical('------------- distributorCalculate empty ex_ch shopConfig idx ==>' . $dis_idx);
                    continue;
                }
             
                $shopConfig[$dis_idx]['ch_val'] = null != $value['ch_val'] ? $value['ch_val'] : 0;
                $shopConfig[$dis_idx]['ex_val'] = null != $value['ex_val'] ? $value['ex_val'] : 0;

                 // 베팅롤링 정산
                if ($shopConfig[$dis_idx]['pre_s_fee'] > 0){
                    $profit_pre_s_fee = ($shopConfig[$dis_idx]['ch_val'] - $shopConfig[$dis_idx]['ex_val']) * ($shopConfig[$dis_idx]['pre_s_fee'] * 0.01);
                    $shopConfig[$dis_idx]['total_point'] += $profit_pre_s_fee;
                }
                
                $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
                Calculate::calBetType($shopConfig, $dis_idx, 'pre_s_fee', 'pt_pre_s_fee', $recommend_member, $shopConfig[$dis_idx]['ch_val'] - $shopConfig[$dis_idx]['ex_val'],$logger);
                Calculate::calLowBetType($shopConfig, $dis_idx, 'pre_s_fee', 'pt_pre_s_fee', $recommend_member, $shopConfig[$dis_idx]['ch_val'] - $shopConfig[$dis_idx]['ex_val'],$logger);
            }

            //$logger->info('------------- distributorCalculate ch_ex shopConfig ==>' . json_encode($shopConfig));
        }

        // 스포츠,실시간 싱글,멀티 베팅금,당첨금 날짜별,총판별로 가져온다.
        $add_where = " GROUP BY cr_dt,dis_idx";
        $sql = Calculate::sportsRealBetMadeQuery($add_where, $logger);
        $result_sp_rl_bet = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date])->getResultArray();
        //$logger->info('------------- distributorCalculate sports_real_bet sql ==>' . $sql);
        //$logger->info('------------- distributorCalculate sports_real_bet db_dataArr ==>' . json_encode($result_sp_rl_bet));

        if (false == empty($result_sp_rl_bet) && true == isset($result_sp_rl_bet) && count($result_sp_rl_bet) > 0) {
            foreach ($result_sp_rl_bet as $key => $bet_data) {
                $dis_idx = $bet_data['dis_idx'];
                if (!isset($shopConfig[$dis_idx])) {
                    $logger->critical('------------- distributorCalculate empty sp_re_bet shopConfig idx ==>' . $dis_idx);
                    continue;
                }

                $shopConfig = Calculate::calSportsRealBet($shopConfig, $dis_idx, $bet_data, $logger);
            }
            //$logger->info('------------- distributorCalculate calSportsRealBet shopConfig ==>' . json_encode($shopConfig));
        }

        // 미니게임 베팅 금액을 날짜별,총판별로 가져온다.
        $add_where = " GROUP BY cr_dt, dis_id";
        $sql = Calculate::miniGameBetMadeQuery($add_where, $logger);
        $result_mini_bet = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date, $excluded_member_idx])->getResultArray();

        //$logger->debug('------------- distributorCalculate mini_bet sql ==>' . $sql);
        //$logger->debug('------------- distributorCalculate mini_bet db_dataArr ==>' . json_encode($result_mini_bet));

        if (false == empty($result_mini_bet) && true == isset($result_mini_bet) && count($result_mini_bet) > 0) {

            foreach ($result_mini_bet as $key => $mini_bet_data) {
                $dis_idx = $mini_bet_data['dis_idx'];
                if (!isset($shopConfig[$dis_idx])) {
                    $logger->critical('------------- distributorCalculate empty mini_bet shopConfig idx ==>' . $dis_idx);
                    continue;
                }

                $profit_mini_fee = 0;
                // 베팅롤링 정산
                if ($shopConfig[$dis_idx]['bet_mini_fee'] > 0){
                    $profit_mini_fee = $mini_bet_data['mini_bet_sum'] * ($shopConfig[$dis_idx]['bet_mini_fee'] * 0.01);
                    $shopConfig[$dis_idx]['total_point'] += $profit_mini_fee;
                }
                
                $shopConfig[$dis_idx]['mini_bet_sum'] = $mini_bet_data['mini_bet_sum'];
                $shopConfig[$dis_idx]['mini_bet_take'] = $mini_bet_data['mini_bet_take'];

                $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
                Calculate::calBetType($shopConfig, $dis_idx, 'bet_mini_fee', 'pt_bet_mini_fee', $recommend_member, $mini_bet_data['mini_bet_sum'],$logger);
                Calculate::calLowBetType($shopConfig, $dis_idx, 'bet_mini_fee', 'pt_bet_mini_fee', $recommend_member, $mini_bet_data['mini_bet_sum'],$logger);
                
            }
            
            //$logger->info('------------- distributorCalculate minigame shopConfig ==>' . json_encode($shopConfig));
        }

        // 카지노
        $where = " GROUP BY DATE(CBH.MOD_DTM),PRT.idx";
        $sql = Calculate::doCasinoByDistQuery($where);
        $casino_result = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date])->getResultArray();
        foreach ($casino_result as $casino) {
            if (!isset($shopConfig[$casino['dis_idx']])) {
                $logger->critical('------------- distributorCalculate empty doCasinoByDistQuery shopConfig idx ==>' . $casino['dis_idx']);
                continue;
            }
            $dis_idx = $casino['dis_idx'];
            $profit_bet_money_fee = 0;
            // 베팅롤링 정산
            if ($shopConfig[$dis_idx]['bet_casino_fee'] > 0){
                $profit_bet_money_fee = $casino['total_bet_money'] * ($shopConfig[$dis_idx]['bet_casino_fee'] * 0.01);
                $shopConfig[$dis_idx]['total_point'] += $profit_bet_money_fee;
            }
            $shopConfig[$dis_idx]['total_casino_bet_money'] += $casino['total_bet_money'];
            $shopConfig[$dis_idx]['casino_bet_take'] += $casino['total_win_money'];

            $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
            Calculate::calBetType($shopConfig, $dis_idx, 'bet_casino_fee', 'pt_bet_casino_fee', $recommend_member, $casino['total_bet_money']);
            Calculate::calLowBetType($shopConfig, $dis_idx, 'bet_casino_fee', 'pt_bet_casino_fee', $recommend_member, $casino['total_bet_money']);
            
        }
        //$logger->info('------------- distributorCalculate casino shopConfig ==>' . json_encode($shopConfig));
        // 슬롯
        $sql = Calculate::doSlotByDistQuery($db_srch_s_date, $db_srch_e_date, $where);
        $slot_result = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date])->getResultArray();
        foreach ($slot_result as $slot) {
            if (!isset($shopConfig[$slot['dis_idx']])) {
                $logger->critical('------------- distributorCalculate empty doSlotByDistQuery shopConfig idx ==>' . $slot['dis_idx']);
                continue;
            }
            $dis_idx = $slot['dis_idx'];
            $profit_bet_money_fee = 0;
            // 베팅롤링 정산
            if ($shopConfig[$dis_idx]['bet_slot_fee'] > 0){
                $profit_bet_money_fee = $slot['total_bet_money'] * ($shopConfig[$dis_idx]['bet_slot_fee'] * 0.01);
                $shopConfig[$dis_idx]['total_point'] += $profit_bet_money_fee;
            }
            
            $shopConfig[$dis_idx]['total_slot_bet_money'] += $slot['total_bet_money'];
            $shopConfig[$dis_idx]['slot_bet_take'] += $slot['total_win_money'];

            $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
            Calculate::calBetType($shopConfig, $dis_idx, 'bet_slot_fee', 'pt_bet_slot_fee', $recommend_member, $slot['total_bet_money']);
            Calculate::calLowBetType($shopConfig, $dis_idx, 'bet_slot_fee', 'pt_bet_slot_fee', $recommend_member, $slot['total_bet_money']);
        }
        //$logger->info('------------- distributorCalculate slot shopConfig ==>' . json_encode($shopConfig));
        
        /*         * ********** 이스포츠 / 키론 / 해시 *********** */
        $sql = Calculate::doEsptByDistQuery($db_srch_s_date, $db_srch_e_date, $where);
        $espt_result = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date])->getResultArray();
        foreach ($espt_result as $espt) {
            if (!isset($shopConfig[$espt['dis_idx']])) {
                $logger->critical('------------- distributorCalculate empty doEsptByDistQuery shopConfig idx ==>' . $espt['dis_idx']);
                continue;
            }

            $dis_idx = $espt['dis_idx'];

            $profit_bet_money_fee = 0;
            // 베팅롤링 정산
            if ($shopConfig[$dis_idx]['bet_esports_fee'] > 0){
                $profit_bet_money_fee = $espt['total_bet_money'] * ($shopConfig[$dis_idx]['bet_esports_fee'] * 0.01);
                $shopConfig[$dis_idx]['total_point'] += $profit_bet_money_fee;
            }
            $shopConfig[$dis_idx]['total_espt_bet_money'] += $espt['total_bet_money'];
            $shopConfig[$dis_idx]['espt_bet_take'] += $espt['total_win_money'];

            $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
            Calculate::calBetType($shopConfig, $dis_idx, 'bet_esports_fee', 'pt_bet_esports_fee', $recommend_member, $espt['total_bet_money']);
            Calculate::calLowBetType($shopConfig, $dis_idx, 'bet_esports_fee', 'pt_bet_esports_fee', $recommend_member, $espt['total_bet_money']);
            
        }
        //$logger->info('------------- distributorCalculate esports shopConfig ==>' . json_encode($shopConfig));
        
        $sql = Calculate::doHashByDistQuery($db_srch_s_date, $db_srch_e_date, $where);
        $hash_result = $memberModel->db->query($sql, [$db_srch_s_date, $db_srch_e_date])->getResultArray();
        foreach ($hash_result as $hash) {
            if (!isset($shopConfig[$hash['dis_idx']])) {
                $logger->critical('------------- distributorCalculate empty doEsptByDistQuery shopConfig idx ==>' . $hash['dis_idx']);
                continue;
            }
            $dis_idx = $hash['dis_idx'];
            $profit_bet_money_fee = 0;
            // 베팅롤링 정산
            if ($shopConfig[$dis_idx]['bet_hash_fee'] > 0){
                $profit_bet_money_fee = $hash['total_bet_money'] * ($shopConfig[$dis_idx]['bet_hash_fee'] * 0.01);
                $shopConfig[$dis_idx]['total_point'] += $profit_bet_money_fee;
            }
            
            $shopConfig[$dis_idx]['total_hash_bet_money'] += $hash['total_bet_money'];
            $shopConfig[$dis_idx]['hash_bet_take'] += $hash['total_win_money'];

            $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
            Calculate::calBetType($shopConfig, $dis_idx, 'bet_hash_fee', 'pt_bet_hash_fee', $recommend_member, $hash['total_bet_money']);
            Calculate::calLowBetType($shopConfig, $dis_idx, 'bet_hash_fee', 'pt_bet_hash_fee', $recommend_member, $hash['total_bet_money']);
            
        }
        /*         * ********** 이스포츠 / 키론 / 해시 *********** */


        //$logger->info('------------- distributorCalculate hash shopConfig ==>' . json_encode($shopConfig));
        return $shopConfig;
    }
  
    public static function distributorCalculatePoint($logger, $shopConfig, $db_srch_s_date) {
        if (true === empty($shopConfig) || false === isset($shopConfig) || count($shopConfig) <= 0) {
            $logger->critical('------------- distributorCalculatePoint empty shopConfig db_srch_s_date ==>' . $db_srch_s_date);
            return;
        }

        $tLogCashModel = new TLogCashModel();
        $shopIdxs = array_keys($shopConfig);
        $shopIdxs = implode(',', $shopIdxs);

        $arrShopInfo = array();
        $arrShopCalculateInfo = array();
        $sql = "select idx, point from member where idx in ($shopIdxs);";
        $result = $tLogCashModel->db->query($sql)->getResultArray();
        foreach ($result as $key => $value) {
            $arrShopInfo[$value['idx']] = $value['point'];
        }

        foreach ($shopConfig as $key => $value) {
            $total_point = true === isset($value['total_point']) ? $value['total_point'] : 0;
            $low_total_point = true === isset($value['low_total_point']) ? $value['low_total_point'] : 0;
            // $arrShopInfo 해당 key의 데이터가 있는지 확인한다.
            if (false === isset($arrShopInfo[$key])) {
                $logger->critical('------------- distributorCalculatePoint empty arrShopInfo key ==>' . $key . ' arrShopInfo ==>' . json_encode($arrShopInfo));
                continue;
            }

            $sum_total_point = $total_point + $low_total_point;

            $b_point = $arrShopInfo[$key];
            $a_point = $b_point + $sum_total_point;

            $sql = "update member set point = point + ? where idx = ?";
            $result = $tLogCashModel->db->query($sql, [$sum_total_point, $key]);

            $ukey = md5($key . strtotime('now'));
            $tLogCashModel->insertCashLog_3($ukey, $key, 301, 0, 0, 0, $sum_total_point, $b_point, $a_point, 'P');

            $ch_val = true === isset($value['ch_val']) && false === empty($value['ch_val']) ? $value['ch_val'] : 0;
            $ex_val = true === isset($value['ex_val']) && false === empty($value['ex_val']) ? $value['ex_val'] : 0;

            $pre_bet_sum_s = true === isset($value['pre_bet_sum_s']) ? $value['pre_bet_sum_s'] : 0;
            $pre_bet_sum_d_2 = true === isset($value['pre_bet_sum_d_2']) ? $value['pre_bet_sum_d_2'] : 0;
            $pre_bet_sum_d_3 = true === isset($value['pre_bet_sum_d_3']) ? $value['pre_bet_sum_d_3'] : 0;
            $pre_bet_sum_d_4 = true === isset($value['pre_bet_sum_d_4']) ? $value['pre_bet_sum_d_4'] : 0;
            $pre_bet_sum_d_5_more = true === isset($value['pre_bet_sum_d_5_more']) ? $value['pre_bet_sum_d_5_more'] : 0;

            $real_bet_sum_s = true === isset($value['real_bet_sum_s']) ? $value['real_bet_sum_s'] : 0;
            $real_bet_sum_d = true === isset($value['real_bet_sum_d']) ? $value['real_bet_sum_d'] : 0;
            $mini_bet_sum = true === isset($value['mini_bet_sum']) ? $value['mini_bet_sum'] : 0;

            // 카지노,슬롯 
            $total_casino_bet_money = true === isset($value['total_casino_bet_money']) ? $value['total_casino_bet_money'] : 0;
            $total_slot_bet_money = true === isset($value['total_slot_bet_money']) ? $value['total_slot_bet_money'] : 0;

            // 이스포츠, 해시
            $total_espt_bet_money = true === isset($value['total_espt_bet_money']) ? $value['total_espt_bet_money'] : 0;
            $total_hash_bet_money = true === isset($value['total_hash_bet_money']) ? $value['total_hash_bet_money'] : 0;

            $bet_pre_s_fee = true === isset($value['bet_pre_s_fee']) ? $value['bet_pre_s_fee'] : 0;
            $bet_pre_d_2_fee = true === isset($value['bet_pre_d_2_fee']) ? $value['bet_pre_d_2_fee'] : 0;
            $bet_pre_d_3_fee = true === isset($value['bet_pre_d_3_fee']) ? $value['bet_pre_d_3_fee'] : 0;
            $bet_pre_d_4_fee = true === isset($value['bet_pre_d_4_fee']) ? $value['bet_pre_d_4_fee'] : 0;
            $bet_pre_d_5_more_fee = true === isset($value['bet_pre_d_5_more_fee']) ? $value['bet_pre_d_5_more_fee'] : 0;

            // 실시간
            $bet_real_s_fee = true === isset($value['bet_real_s_fee']) ? $value['bet_real_s_fee'] : 0;
            $bet_real_d_fee = true === isset($value['bet_real_d_fee']) ? $value['bet_real_d_fee'] : 0;

            // 미니게임
            $bet_mini_fee = true === isset($value['bet_mini_fee']) ? $value['bet_mini_fee'] : 0;

            // 죽장
            $pre_s_fee = true === isset($value['pre_s_fee']) ? $value['pre_s_fee'] : 0;

            // 카지노,슬롯 
            $bet_casino_fee = true === isset($value['bet_casino_fee']) ? $value['bet_casino_fee'] : 0;
            $bet_slot_fee = true === isset($value['bet_slot_fee']) ? $value['bet_slot_fee'] : 0;

            // 이스포츠, 해시
            $bet_espt_fee = true === isset($value['bet_esports_fee']) ? $value['bet_esports_fee'] : 0;
            $bet_hash_fee = true === isset($value['bet_hash_fee']) ? $value['bet_hash_fee'] : 0;

            // 당첨금액 
            $pre_bet_take_s = true === isset($value['pre_bet_take_s']) ? $value['pre_bet_take_s'] : 0;
            $pre_bet_take_d_2 = true === isset($value['pre_bet_take_d_2']) ? $value['pre_bet_take_d_2'] : 0;
            $pre_bet_take_d_3 = true === isset($value['pre_bet_take_d_3']) ? $value['pre_bet_take_d_3'] : 0;
            $pre_bet_take_d_4 = true === isset($value['pre_bet_take_d_4']) ? $value['pre_bet_take_d_4'] : 0;
            $pre_bet_take_d_5_more = true === isset($value['pre_bet_take_d_5_more']) ? $value['pre_bet_take_d_5_more'] : 0;
            $real_bet_take_s = true === isset($value['real_bet_take_s']) ? $value['real_bet_take_s'] : 0;
            $real_bet_take_d = true === isset($value['real_bet_take_d']) ? $value['real_bet_take_d'] : 0;
            $mini_bet_take = true === isset($value['mini_bet_take']) ? $value['mini_bet_take'] : 0;
            $casino_bet_take = true === isset($value['casino_bet_take']) ? $value['casino_bet_take'] : 0;
            $slot_bet_take = true === isset($value['slot_bet_take']) ? $value['slot_bet_take'] : 0;
            $hash_bet_take = true === isset($value['hash_bet_take']) ? $value['hash_bet_take'] : 0;
            $espt_bet_take = true === isset($value['espt_bet_take']) ? $value['espt_bet_take'] : 0;

            // 클래식 배팅,당첨금액 
            $pre_classic_bet_sum_s = true === isset($value['pre_classic_bet_sum_s']) ? $value['pre_classic_bet_sum_s'] : 0;
            $pre_classic_bet_sum_d = true === isset($value['pre_classic_bet_sum_d']) ? $value['pre_classic_bet_sum_d'] : 0;
            $pre_classic_bet_take_s = true === isset($value['pre_classic_bet_take_s']) ? $value['pre_classic_bet_take_s'] : 0;
            $pre_classic_bet_take_d = true === isset($value['pre_classic_bet_take_d']) ? $value['pre_classic_bet_take_d'] : 0;

            $values = "($key, now(), '$db_srch_s_date', $total_point,$low_total_point, $b_point, $a_point, $ch_val, $ex_val,"
                    . "$pre_bet_sum_s, $pre_bet_sum_d_2,$pre_bet_sum_d_3,$pre_bet_sum_d_4,$pre_bet_sum_d_5_more,"
                    . "$real_bet_sum_s, $real_bet_sum_d, $mini_bet_sum,$total_casino_bet_money,$total_slot_bet_money,"
                    . "$bet_pre_s_fee,$bet_real_s_fee, $bet_real_d_fee, $bet_mini_fee, $pre_s_fee,$bet_casino_fee,$bet_slot_fee,"
                    . "$total_espt_bet_money,$total_hash_bet_money,$bet_espt_fee,$bet_hash_fee,"
                    . "$pre_bet_take_s,$pre_bet_take_d_2,$pre_bet_take_d_3,$pre_bet_take_d_4,$pre_bet_take_d_5_more,"
                    . "$real_bet_take_s,$real_bet_take_d,$mini_bet_take,$casino_bet_take,$slot_bet_take,$hash_bet_take,$espt_bet_take,"
                    . "$bet_pre_d_2_fee,$bet_pre_d_3_fee,$bet_pre_d_4_fee,$bet_pre_d_5_more_fee,"
                    . "$pre_classic_bet_sum_s,$pre_classic_bet_take_s,$pre_classic_bet_sum_d,$pre_classic_bet_take_d )";

            array_push($arrShopCalculateInfo, $values);
        }

        // 정산 데이터 업데이트
        if (count($arrShopCalculateInfo) > 0) {
            try {
                $tLogCashModel->db->query(
                        "INSERT INTO `shop_calculate_result`
                        (member_idx, `create_dt`, `calculate_dt`, `calculate_point`,`low_calculate_point`, `be_point`, `af_point`, `ch_val`, `ex_val`, 
                        `pre_bet_sum_s`, `pre_bet_sum_d_2`, `pre_bet_sum_d_3`, `pre_bet_sum_d_4`, `pre_bet_sum_d_5_more`, 
                        `real_bet_sum_s`, `real_bet_sum_d`, `mini_bet_sum`, `total_casino_bet_money`,`total_slot_bet_money`, 
                        `bet_pre_s_fee`,`bet_real_s_fee`, `bet_real_d_fee`, `bet_mini_fee`, `pre_s_fee`,`bet_casino_fee`,`bet_slot_fee`, 
                        `total_espt_bet_money`, `total_hash_bet_money`, `bet_espt_fee`, `bet_hash_fee`,
                         pre_bet_take_s,pre_bet_take_d_2,pre_bet_take_d_3,pre_bet_take_d_4,pre_bet_take_d_5_more,
                         real_bet_take_s,real_bet_take_d,mini_bet_take,casino_bet_take,slot_bet_take,hash_bet_take,espt_bet_take,
                         bet_pre_d_2_fee,bet_pre_d_3_fee,bet_pre_d_4_fee,bet_pre_d_5_more_fee,
                         pre_classic_bet_sum_s,pre_classic_bet_take_s,pre_classic_bet_sum_d,pre_classic_bet_take_d) VALUES " . implode(',', $arrShopCalculateInfo));
            } catch (\mysqli_sql_exception $e) {
                $logger->critical('distributorCalculatePoint [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
                $logger->critical('::::::::::::::: distributorCalculatePoint query : ' . $tLogCashModel->getLastQuery());
            }
        }
    }
}
