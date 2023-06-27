<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');
include_once(_LIBPATH . '/class_ComQuery.php');
include_once(_LIBPATH . '/class_Code.php');



/*총 베팅금액 : 스포츠,실시간,미니게임,이스포츠,해쉬,슬롯,카지노
> 모든 이용금액을 합산한금액

총 베팅횟수 :  스포츠,실시간,미니게임,이스포츠,해쉬,슬롯,카지노
> 모든 배팅횟수를 합산한 수치

금일 정산액 : 롤링 OR 충환마진 입력된 총판 수익구조에 정산된금액 ( 하부 총판 정산액 제외 )

+ 금일 하부총판 정산액 표기추가
*/

try {
    $result['retCode'] = SUCCESS;
    $dis_id = trim(isset($_POST['dis_id']) ? $_POST['dis_id'] : '');
    $m_idx = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);

    $CASHAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
    $db_conn = $CASHAdminDAO->dbconnect();
    if (!$db_conn) {
        CommonUtil::logWrite("fail _pop_disinfo_content db_conn ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    if (!is_int((int)$m_idx)) {
        CommonUtil::logWrite("fail _pop_disinfo_content", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        return;
    }

    $db_srch_s_date = '2022-05-18 00:00:00';

    $endTime = date("Y-m-d", strtotime("+" . 1 . " days"));
    $db_srch_e_date = $endTime . ' 23:59:59';

    $db_today_s_date = date("Y-m-d 00:00:00");
    $db_today_e_date = date("Y-m-d 23:59:59");

    // 총배팅금액,총 배팅횟수,총입금액/횟수,출금액/횟수,총 정산액 ,수익률 최대 입금액,최대 출금액 

    list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($m_idx, $CASHAdminDAO);
    $str_param = implode(',', $param_dist);
    $where_new = " AND T1.recommend_member in($str_param)";
    
    $add_where = " and member_idx in($str_param) ";
    $p_data['sql'] = ComQuery::doShopResultChExDist($db_srch_s_date, $db_srch_e_date, $add_where);
    CommonUtil::logWrite("_pop_disinfo_content sql ==> " . $p_data['sql'], "info");
    $db_dataTotalArr = $CASHAdminDAO->getQueryData($p_data);
    CommonUtil::logWrite("_pop_disinfo_content ch_ex ==> " . json_encode($db_dataTotalArr), "info");

    $ch_val_sum = 0;
    $ex_val_sum = 0;
    $gab_sum = 0;
    $total_cal_point = 0;
    $total_bet_money = 0;
    $total_bet_count = $tot_s_bet = $tot_d_bet = $tot_m_bet = $tot_cs_bet = $tot_sl_bet = $tot_es_bet = $tot_hs_bet = $max_charge_money = $max_exchange_money = $tot_charge_cnt = $tot_exchange_cnt = 0;
    if (true === isset($db_dataTotalArr) || false === empty($db_dataTotalArr)) {
        $ch_val_sum = (int) $db_dataTotalArr[0]['ch_val_sum'];
        $ex_val_sum = (int) $db_dataTotalArr[0]['ex_val_sum'];
        $gab_sum = (int) $db_dataTotalArr[0]['gab_sum'];
        $total_cal_point = (int) $db_dataTotalArr[0]['total_cal_point'];
        $total_bet_money = (int) $db_dataTotalArr[0]['total_bet_money'];
    }
    
    $db_bet_tot_cash = $total_bet_money;
    $db_charge_tot_cash = $ch_val_sum;
    $db_exchange_tot_cash = $ex_val_sum;
    $db_calculate_tot = $total_cal_point;
    
    //$where_new = " AND T1.recommend_member = $m_idx";
   
    
        
    $p_data['sql'] = ComQuery::getSumTotalMemberCashQuery($where_new);
    $db_sumTotalMemberCashArr = $CASHAdminDAO->getQueryData($p_data);
    $db_sumTotalMemberCashArr = isset($db_sumTotalMemberCashArr) ? $db_sumTotalMemberCashArr : [];
    if (true === isset($db_dataTotalArr) || false === empty($db_dataTotalArr)) {
        $db_bet_tot_cnt = (int) $db_sumTotalMemberCashArr[0]['sum_bet_total_count'];
        $db_charge_tot_cnt = (int) $db_sumTotalMemberCashArr[0]['sum_charge_total_count'];
        $db_exchange_tot_cnt = (int) $db_sumTotalMemberCashArr[0]['sum_exchange_total_count'];
        $db_charge_max_cash = (int) $db_sumTotalMemberCashArr[0]['max_charge'];
        $db_exchange_max_cash = (int) $db_sumTotalMemberCashArr[0]['max_exchange'];
    }
    
    /***********************************************************************************************************************/        
    // 금일 배팅금액/횟수,충전 금액/횟수,환전 금액/횟수,정산액 
    $day_ch_val_sum = 0;
    $day_ex_val_sum = 0;
    $day_total_cal_point = 0;
    $day_total_bet_money = 0;
    $day_total_bet_count = $day_tot_s_bet = $day_tot_d_bet = $day_tot_m_bet = $day_tot_cs_bet = $day_tot_sl_bet = $day_tot_es_bet = $day_tot_hs_bet = $day_max_charge_money = $day_max_exchange_money = $day_tot_charge_cnt = $day_tot_exchange_cnt = 0;

    // 금일 싱글 배팅회원수
    $p_data['sql'] = ComQuery::getSingleBetMemberCount($db_today_s_date, $db_today_e_date, $where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {

        $day_tot_s_bet = $db_dataArr[0]['bet_count'];
    }

    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_s_bet;
    // 금일 멀티 배팅회원수
    $p_data['sql'] = ComQuery::getMultiBetMemberCount($db_today_s_date, $db_today_e_date, $where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {

        $day_tot_d_bet = $db_dataArr[0]['bet_count'];
    }
    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_d_bet;

    $excluded_member_idx = 0;
    //금일 미니게임 배팅회원수 / 배팅수 
    $p_data['sql'] = ComQuery::getMiniGameBetMemberCount($db_today_s_date, $db_today_e_date, $excluded_member_idx, $where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {

        $day_tot_m_bet = $db_dataArr[0]['bet_count'];
    }
    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_m_bet;
    //금일 카지노 배팅회원수
    
    $where_kplay_new = " AND PRT.id ='$dis_id'";
     
    $p_data['sql'] = ComQuery::getCasinoGameBetMemberCount($db_today_s_date, $db_today_e_date,$where_kplay_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_tot_cs_bet = $db_dataArr[0]['bet_count'];
    }
    
    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_cs_bet;
    
    //금일 슬롯 배팅회원수
    $p_data['sql'] = ComQuery::getSlotGameBetMemberCount($db_today_s_date, $db_today_e_date, $where_kplay_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_tot_sl_bet = $db_dataArr[0]['bet_count'];
    }

    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_sl_bet;
    
    //금일 이스포츠 배팅회원수
    $p_data['sql'] = ComQuery::getEsportsGameBetMemberCount($db_today_s_date, $db_today_e_date,$where_kplay_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_tot_es_bet = $db_dataArr[0]['cnt'];
    }
    
    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_es_bet;
    
    //금일 해쉬 배팅회원수
    $p_data['sql'] = ComQuery::getHashGameBetMemberCount($db_today_s_date, $db_today_e_date,$where_kplay_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_tot_hs_bet = $db_dataArr[0]['cnt'];
    }

    $db_bet_tot_cnt = $db_bet_tot_cnt + $day_tot_hs_bet;
    
    //금일 충전횟수
    $p_data['sql'] = ComQuery::getChargeMemberCount($db_today_s_date, $db_today_e_date,$where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_tot_charge_cnt = $db_dataArr[0]['cnt'];
    }
    $db_charge_today_cnt = $day_tot_charge_cnt;
    $db_charge_tot_cnt = $db_charge_tot_cnt + $day_tot_charge_cnt;
    //금일 환전횟수
    $p_data['sql'] = ComQuery::getExchangeMemberCount($db_today_s_date, $db_today_e_date,$where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_tot_exchange_cnt = $db_dataArr[0]['cnt'];
    }
    $db_exchange_today_cnt = $day_tot_exchange_cnt;
    $db_exchange_tot_cnt = $db_exchange_tot_cnt + $day_tot_exchange_cnt;
    //금일 최대 입금
    $p_data['sql'] = ComQuery::getChargeMaxMoney($db_today_s_date, $db_today_e_date,$where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_max_charge_money = $db_dataArr[0]['max_charge_money'];
    }
    
    if($db_charge_max_cash < $day_max_charge_money){
        $db_charge_max_cash = $day_max_charge_money;
    }
    
    //금일 최대 출금액
    $p_data['sql'] = ComQuery::getExchangeMaxMoney($db_today_s_date, $db_today_e_date,$where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    if (true === isset($db_dataArr) || false === empty($db_dataArr)) {
        $day_max_exchange_money = $db_dataArr[0]['max_exchange_money'];
    }
    
     
    if($db_exchange_max_cash < $day_max_exchange_money){
        $db_exchange_max_cash = $day_max_exchange_money;
    }

    //$param_where_new = array();
    //$where_low = " AND 1 = 1";
    //$param_where_low = array();
    //$param_where_low_qm = array();
    //$where_low_dis = "";

    // 본인의 정보를 가져온다. 하위 총판의 idx를 가져와서 
    //$where_new = " AND T1.recommend_member = ? ";
    //array_push($param_where_new, $m_idx);
    // 하위 총판의 recommend_member 값을 얻어온다.
    //$p_data['sql'] = "select idx from member where recommend_member = ? and u_business <> 1 ";
    //$db_lowDist = $CASHAdminDAO->getQueryData_pre($p_data['sql'], [$m_idx]);
    //$db_lowDist = isset($db_lowDist) ? $db_lowDist : [];
    //foreach ($db_lowDist as $low) {
    //    array_push($param_where_low, $low['idx']);
    //    array_push($param_where_low_qm, '?');
    //}

    //$str_where_low_qm = implode(',', $param_where_low_qm);
    //$where_low = " AND T1.recommend_member in (" . $str_where_low_qm . ") ";
    $param_where_new = array();
    $param_dis = array();
    
   
    $where_dis = " AND parent.idx in($str_param)";
    $where_kplay = " AND PRT.idx in($str_param)";
        

    // 요율설정
    $shopConfig = array();
    $p_data['sql'] = ComQuery::getDistShopInfo();
    $result_shop = $CASHAdminDAO->getQueryData($p_data);
    $shopConfig = null;
    foreach ($result_shop as $key => $value) {
        $shopConfig[$value['member_idx']] = $value;
    }

    //$shopConfig[$m_idx]['recommend_member'] = 0;

    //CommonUtil::logWrite("minibetting shopConfig: " . json_encode($shopConfig), "info");

    $begin = new DateTime($db_today_s_date);
    $end = new DateTime($db_today_e_date);

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);

    
    $period = isset($period) ? $period : [];
    $stats_day = [];
    foreach ($period as $dt) {
        $str_dt = $dt->format("Ymd");
        if (strlen($str_dt) > 5) {
            $stats_day[$str_dt]['ymd'] = $dt->format("Y-m-d");
            $stats_day[$str_dt]['val'] = 0;
        }
    }

    $str_dt = str_replace('-', '', $db_today_e_date);
    $stats_day[$str_dt]['val'] = 0;
    $stats_day[$str_dt]['ymd'] = $db_today_e_date;
    // 충전 환전 -- child.recommend_member = parent.idx
    list($p_data['sql'], $param) = ComQuery::doComChExQuery($db_today_s_date, $db_today_e_date, $where_new, $param_where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

    // 충전,환전 합산 구하기 
    $tot_ch_val = $tot_ex_val = 0;
    $total_point = 0;
    $total_point_sub = 0;
    GameCode::doSumChExCalc($shopConfig, $stats_day, $db_dataArr, $total_point, $total_point_sub, $tot_ch_val, $tot_ex_val,$m_idx);
       
    $db_charge_today_cash = $tot_ch_val;
    $db_exchange_today_cash = $tot_ex_val;
    //CommonUtil::logWrite("minibetting bf db_charge_tot_cash: " . $db_charge_tot_cash, "info");
    $db_charge_tot_cash = $db_charge_tot_cash + $tot_ch_val;
    //CommonUtil::logWrite("minibetting af db_charge_tot_cash: " . $db_charge_tot_cash, "info");
    $db_exchange_tot_cash =$db_exchange_tot_cash + $tot_ex_val;
    // 배팅 합계용 변수
    $pre_bet_sum_s = 0;
    $pre_take_sum_s = 0;
    $pre_sum_s = 0; // 차액

    $pre_bet_sum_d = 0;
    $pre_take_sum_d = 0;
    $pre_sum_d = 0; // 차액

    $real_bet_sum_s = 0;
    $real_take_sum_s = 0;
    $real_sum_s = 0; // 차액
    $real_bet_sum_d = 0;
    $real_take_sum_d = 0;
    $real_sum_d = 0; // 차액

      // 클래식 
    $total_classic_bet_money = 0;
    $total_classic_win_money = 0;
    $total_classic_lose_money = 0;
    //$where_dis = " AND 1 = 1";
    //$param_dis = array();
    
    //if (0 != $m_idx) {
    //    $where_dis = "AND parent.idx = ?";
    //    $param_dis[] = $m_idx; //$dist_id;
    //}

    $param = array_merge([$db_today_s_date, $db_today_e_date], $param_dis);
    $p_data['sql'] = ComQuery::doSportsRealForderBetQuery($db_today_s_date, $db_today_e_date, $where_dis);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doSumSportsRealBetCalc($db_dataArr, $stats_day, $shopConfig
            , $pre_bet_sum_s, $pre_take_sum_s, $pre_sum_s
            , $pre_bet_sum_d, $pre_take_sum_d, $pre_sum_d
            , $real_bet_sum_s, $real_take_sum_s, $real_sum_s
            , $real_bet_sum_d, $real_take_sum_d, $real_sum_d
            , $total_classic_bet_money,$total_classic_win_money,$total_classic_lose_money
            , $total_point,$total_point_sub, $m_idx
    );

    $db_bet_tot_cash = $db_bet_tot_cash + $pre_bet_sum_s + $pre_bet_sum_d + $real_bet_sum_s + $real_bet_sum_d;
    // 미니게임 베팅 $excluded_member_idx 값은 포함해서 보여준다.
    $mini_bet_sum_d = 0;
    $mini_take_sum_d = 0;
    $mini_sum_d = 0;
    $param = array_merge([$db_today_s_date, $db_today_e_date], $param_dis);
    array_push($param, $excluded_member_idx);
    $p_data['sql'] = ComQuery::doMiniBetQuery($db_today_s_date, $db_today_e_date, $excluded_member_idx, $where_dis);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doSumMiniBetCalc($db_dataArr, $stats_day, $shopConfig
            , $mini_bet_sum_d, $mini_take_sum_d, $mini_sum_d,$total_point, $total_point_sub,$m_idx);

      
    $db_bet_tot_cash = $db_bet_tot_cash + $mini_bet_sum_d;

    // 카지노 
    $total_casino_bet_money = 0;
    $total_casino_win_money = 0;
    $total_casino_lose_money = 0;

    //$where_kplay = " AND PRT.idx = $m_idx ";

    $p_data['sql'] = ComQuery::doCasinoByDistQuery($db_today_s_date, $db_today_e_date, $where_kplay);
    $db_casinoDataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_casinoDataArr = isset($db_casinoDataArr) ? $db_casinoDataArr : [];

    GameCode::doSumCasinoBetCalc($db_casinoDataArr, $stats_day, $shopConfig
            , $total_casino_bet_money, $total_casino_win_money, $total_casino_lose_money,$total_point, $total_point_sub,$m_idx);

       
    $db_bet_tot_cash = $db_bet_tot_cash + $total_casino_bet_money;

    // 슬롯 
    $total_slot_bet_money = 0;
    $total_slot_win_money = 0;
    $total_slot_lose_money = 0;
    $p_data['sql'] = ComQuery::doSlotByDistQuery($db_today_s_date, $db_today_e_date, $where_kplay);
    $db_slotDataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_slotDataArr = isset($db_slotDataArr) ? $db_slotDataArr : [];
    GameCode::doSumSlotBetCalc($db_slotDataArr, $stats_day, $shopConfig
            , $total_slot_bet_money, $total_slot_win_money, $total_slot_lose_money, $total_point,$total_point_sub,$m_idx);
   
    $db_bet_tot_cash = $db_bet_tot_cash + $total_slot_bet_money;

    // 이스포츠 / 키론
    $total_espt_bet_money = 0;
    $total_espt_win_money = 0;
    $total_espt_lose_money = 0;

    if ('ON' == IS_ESPORTS_KEYRON) {
        $p_data['sql'] = ComQuery::doEsptByDistQuery($db_today_s_date, $db_today_e_date, $where_kplay);
        $db_esptDataArr = $CASHAdminDAO->getQueryData($p_data);
        $db_esptDataArr = isset($db_esptDataArr) ? $db_esptDataArr : [];

        GameCode::doSumEsptBetCalc($db_esptDataArr, $stats_day, $shopConfig
                , $total_espt_bet_money, $total_espt_win_money, $total_espt_lose_money,$total_point, $total_point_sub,$m_idx);
    }
    
    $db_bet_tot_cash = $db_bet_tot_cash + $total_espt_bet_money;

    // 해시
    $total_hash_bet_money = 0;
    $total_hash_win_money = 0;
    $total_hash_lose_money = 0;

    if ('ON' == IS_HASH) {
        $p_data['sql'] = ComQuery::doHashByDistQuery($db_today_s_date, $db_today_e_date, $where_kplay);
        $db_hashDataArr = $CASHAdminDAO->getQueryData($p_data);
        $db_hashDataArr = isset($db_hashDataArr) ? $db_hashDataArr : [];
        GameCode::doSumHashBetCalc($db_hashDataArr, $stats_day, $shopConfig
                , $total_hash_bet_money, $total_hash_win_money, $total_hash_lose_money,$total_point, $total_point_sub,$m_idx);
    }
    
    GameCode::doStatsDay($stats_day, $total_point);
      
    $db_bet_tot_cash = $db_bet_tot_cash + $total_hash_bet_money;
    
    $db_calculate_today = $total_point;
    $db_calculate_tot = $db_calculate_tot + $total_point;
    if(0 < $db_charge_tot_cash){
    $db_rate_buff = (100 - (($db_exchange_tot_cash / $db_charge_tot_cash) * 100));
    } else {
        $db_rate_buff = 0;
    }
    $db_calculate_rate = sprintf('%0.2f', $db_rate_buff); // 520 -> 520.00
    $db_total_point_sub = $total_point_sub;
        
    // 추천인 목록 가져오기 
    $where_new = " AND T1.recommend_member = $m_idx";
    $p_data['sql'] = ComQuery::getRecommandListQuery($where_new);
    $db_recommandArr = $CASHAdminDAO->getQueryData($p_data);
    $db_recommandArr = isset($db_recommandArr) ? $db_recommandArr : [];
        
        
} catch (\mysqli_sql_exception $e) {

    CommonUtil::logWrite('fail _pop_disinfo_content mysqli_sql_exception ' . $e->getMessage(), "db_error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {

    CommonUtil::logWrite("fail _pop_disinfo_content Exception ", "error");
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {
    
    $CASHAdminDAO->dbclose();
    //CommonUtil::logWrite("fail _pop_disinfo_content db_recommandArr ". json_encode($db_recommandArr), "error");
    if ($result['retCode'] < 0) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
}

$data_str = "
<table>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>하부회원 종합내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 베팅금액</td>
<td style='text-align:right;'>" . number_format($db_bet_tot_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 충전금액</td>
<td style='text-align:right;'>" . number_format($db_charge_today_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 환전금액</td>
<td style='text-align:right;'>" . number_format($db_exchange_today_cash) . " 원</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 베팅횟수</td>
<td style='text-align:right;'>" . number_format($db_bet_tot_cnt) . " 회</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 충전횟수</td>
<td style='text-align:right;'>" . number_format($db_charge_today_cnt) . " 회</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 환전횟수</td>
<td style='text-align:right;'>" . number_format($db_exchange_today_cnt) . " 회</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 정산액</td>
<td style='text-align:right;'>" . number_format($db_calculate_today) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 하부정산액</td>
<td style='text-align:right;'>" . $db_total_point_sub . " %</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>하부회원 충/환전 내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 입금금액</td>
<td style='text-align:right;'>" . number_format($db_charge_tot_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;최대 입금금액</td>
<td style='text-align:right;'>" . number_format($db_charge_max_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 입금 횟수</td>
<td style='text-align:right;'>" . number_format($db_charge_tot_cnt) . " 회</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 출금금액</td>
<td style='text-align:right;'>" . number_format($db_exchange_tot_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;최대 출금금액</td>
<td style='text-align:right;'>" . number_format($db_exchange_max_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 출금 횟수</td>
<td style='text-align:right;'>" . number_format($db_exchange_tot_cnt) . " 회</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 정산액</td>
<td style='text-align:right;'>" . number_format($db_calculate_tot) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;수익률</td>
<td style='text-align:right;'>" . $db_calculate_rate . " %</td>

</tr>
</table>
</td>
</tr>
</table>";
$result['retData_1'] = $data_str;
$data_str_2 = "
<table class='mlist'>
<tr>
<td colspan='7' style='background-color:#6F6F6F;color:#fff'>추천인 목록</td>
</tr>
<tr>
<td style='width: 60px;background-color:#6F6F6F;color:#fff'>아이디</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>입금</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>출금</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>정산</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>보유머니</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>포인트</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>상태</td>
</tr>
</table>
<div class='tline' style='max-height: 460px;'>
<table class='mlist'>";

if (count($db_recommandArr) > 0) {
    foreach ($db_recommandArr as $recom) {
        $db_id = $recom['id'];
        $db_total_deposit_money = $recom['charge_total_money'];
        $db_total_withdraw_money = $recom['exchange_total_money'];
        $db_total_cal_money = number_format($db_total_deposit_money - $db_total_withdraw_money);
        $db_money = number_format($recom['money']);
        $db_point = number_format($recom['point']);
        $db_status = "";
        switch ($recom['status']) {
            case 1: $db_status = "사용중";
                break;
            case 2: $db_status = "정지";
                break;
            case 3: $db_status = "탈퇴";
                break;
            case 11: $db_status = "대기";
                break;
        }
        $data_str_2 .= "<td style='width: 60px;'>$db_id</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_total_deposit_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_total_withdraw_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_total_cal_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_point</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_status</td></tr>";
    }
} else {
    $data_str_2 .= "<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .= "</table></div>";
$result['retData_2'] = $data_str_2;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>