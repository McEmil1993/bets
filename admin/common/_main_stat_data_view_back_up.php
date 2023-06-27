<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_LIBPATH . '/class_ComQuery.php');

if (!isset($_SESSION)) {
    session_start();
}

$COMMONDAO = new Admin_Common_DAO(_DB_NAME_WEB);
$db_conn_common = $COMMONDAO->dbconnect();

$UTIL = new CommonUtil();

if ($db_conn_common) {

    // login check
    $sqlWhere = '';
    if($_SESSION['u_business'] == 0){
        $p_data['sql'] = " SELECT a_nick, session_key FROM t_adm_user ";
        $p_data['sql'] .= " WHERE a_id = '" . $_SESSION['aid'] . "' ";
    }else{
        $p_data['sql'] = " SELECT nick_name as a_nick, session_key FROM member ";
        $p_data['sql'] .= " WHERE id = '" . $_SESSION['aid'] . "' ";
        $sqlWhere = ' AND recommend_member = '.$_SESSION['member_idx'];
    }

    $db_dataLogin = $COMMONDAO->getQueryData($p_data);
    $db_session_key = $db_dataLogin[0]['session_key'];

    $nLogin = 0;

    if ($db_session_key == '') {
        $nLogin = 2;
        $result['retCode'] = 2002;
    } else if ($db_session_key != $_SESSION['akey']) {
        $nLogin = 1;
        $result['retCode'] = 2001;
    }

    if ($nLogin > 0) {
        $COMMONDAO->dbclose();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
 
    if ('OFF' == IS_INCLUDING_DISTRIBUTOR) {
        $p_data_comm['sql'] = " SELECT COUNT(*) AS cnt, SUM(money) AS s_money, SUM(POINT + betting_p) AS s_point FROM member  where u_business = 1 AND level <> 9 AND status = 1".$sqlWhere;
    } else {
        $p_data_comm['sql'] = " SELECT COUNT(*) AS cnt, SUM(money) AS s_money, SUM(POINT + betting_p) AS s_point FROM member  where level <> 9 AND status = 1".$sqlWhere;
    }

    $db_dataMemCnt = $COMMONDAO->getQueryData($p_data_comm);
    $tot_mem_cnt = $db_dataMemCnt[0]['cnt'];
    $tot_mem_money = $db_dataMemCnt[0]['s_money'];
    $tot_mem_point = $db_dataMemCnt[0]['s_point'];

    if($_SESSION['u_business'] == 0){
        $p_data_comm['sql'] = "SELECT * FROM day_ch_ex";
    }else{
        $p_data_comm['sql'] = day_ch_ex($_SESSION['member_idx']);
    }
    //CommonUtil::logWrite(" _main_stata_data_view day_ch_ex" . $p_data_comm['sql'], "info");
    $db_dataCommon = $COMMONDAO->getQueryData($p_data_comm);

    $tot_ex_cnt_1 = $tot_ex_cnt_2 = $tot_ex_cnt_3 = 0;
    $tot_ch_money_3 = 0;
    $tot_ex_money_3 = 0;

    $today_tot_cnt_ch_1 = $today_tot_cnt_ch_2 = $today_tot_cnt_ch_3 = 0;
    $today_tot_cnt_ex_1 = $today_tot_cnt_ex_2 = $today_tot_cnt_ex_3 = 0;

    $today_tot_money_ch_1 = $today_tot_money_ch_2 = $today_tot_money_ch_3 = 0;
    $today_tot_money_ex_1 = $today_tot_money_ex_2 = $today_tot_money_ex_3 = 0;


    $tot_bet_money_sports = 0;
    $tot_bet_money_sports_win = 0;
    $tot_bet_money_sports_ing = 0;

    $tot_bet_money_real = 0;
    $tot_bet_money_real_win = 0;
    $tot_bet_money_real_ing = 0;

// 신규 베팅 카운트
    $tot_sports_count = 0;
    $tot_realtime_count = 0;
    foreach ($db_dataCommon as $row_comm) {

        if ($row_comm['stype'] == 'ch') {
            switch ($row_comm['status']) {
                case 1:
                    $today_tot_cnt_ch_1 = $row_comm['cnt'];
                    $today_tot_money_ch_1 = $row_comm['s_money'];
                    break;
                case 2:
                    $today_tot_cnt_ch_2 = $row_comm['cnt'];
                    $today_tot_money_ch_2 = $row_comm['s_money'];
                    break;
                case 3:
                    $today_tot_money_ch_3 = $row_comm['s_money'];
                    $today_tot_cnt_ch_3 = $row_comm['cnt'];
                    break;
            }
        } else if ($row_comm['stype'] == 'ch_tot') {
            switch ($row_comm['status']) {
                case 3:
                    $tot_ch_money_3 = $row_comm['s_money'];
                    break;
            }
        } else if ($row_comm['stype'] == 'ex') {
            switch ($row_comm['status']) {
                case 1: break;
                case 2: break;
                case 3:
                    $today_tot_money_ex_3 = $row_comm['s_money'];
                    $today_tot_cnt_ex_3 = $row_comm['cnt'];
                    break;
            }
        } else if ($row_comm['stype'] == 'ex_tot') {
            switch ($row_comm['status']) {
                case 1:
                case 3:
                    $tot_ex_cnt_3 += $row_comm['cnt'];
                    $tot_ex_money_3 += $row_comm['s_money'];
                    break;
            }
        }
    }
    $p_data_bet['sql'] = '';
    
    if($_SESSION['u_business'] == 0){
        $p_data_bet['sql'] = 'SELECT * FROM day_bet_pre';
    }else{
        $p_data_bet['sql'] = day_bet_pre($_SESSION['member_idx']);
    }
    //CommonUtil::logWrite(" _main_stata_data_view day_bet_pre " . $p_data_bet['sql'], "info");
    $db_dataBet = $COMMONDAO->getQueryData($p_data_bet);


    $tot_bet_money_sports = $tot_bet_money_sports_win = $tot_bet_money_sports_ing = 0;

    foreach ($db_dataBet as $row_bet) {
        if ($row_bet['stype'] == 'bet_tot') {
            $tot_bet_money_sports += $row_bet['s_money'];
        } else if ($row_bet['stype'] == 'bet_tot_ing') {
            $tot_bet_money_sports_ing = $row_bet['s_money'];
        } else if ($row_bet['stype'] == 'bet_tot_sports') {
            $tot_bet_money_sports_win = $row_bet['s_win_money'];
        }
    }

    if($_SESSION['u_business'] == 0){
        $p_data_bet['sql'] = 'SELECT * FROM day_bet_real';
    }else{
        $p_data_bet['sql'] = day_bet_real($_SESSION['member_idx']);
    }
    //CommonUtil::logWrite(" _main_stata_data_view day_bet_real " . $p_data_bet['sql'], "info");

    $db_dataBet = $COMMONDAO->getQueryData($p_data_bet);

    //$today_bet_money_real = $today_bet_money_real_ing = 0;
    $tot_bet_money_real = $tot_bet_money_real_win = $tot_bet_money_real_ing = 0;

    foreach ($db_dataBet as $row_bet) {
        if ($row_bet['stype'] == 'bet_tot') {
            $tot_bet_money_real += $row_bet['s_money'];
        } else if ($row_bet['stype'] == 'bet_tot_ing') {
            $tot_bet_money_real_ing = $row_bet['s_money'];
        } else if ($row_bet['stype'] == 'bet_tot_real') {
            $tot_bet_money_real_win = $row_bet['s_win_money'];
        }
    }
    
    // 클래식 배팅정보
    
    if($_SESSION['u_business'] == 0){
        $p_data_bet['sql'] = 'SELECT * FROM day_bet_classic';
    }else{
        $p_data_bet['sql'] = day_bet_classic($_SESSION['member_idx']);
    }
    //CommonUtil::logWrite(" _main_stata_data_view day_bet_real " . $p_data_bet['sql'], "info");

    $db_dataBet = $COMMONDAO->getQueryData($p_data_bet);
    
    $tot_bet_money_classic = $tot_bet_money_classic_win = $tot_bet_money_classic_ing = 0;

    foreach ($db_dataBet as $row_bet) {
        if ($row_bet['stype'] == 'bet_tot') {
            $tot_bet_money_classic += $row_bet['s_money'];
        } else if ($row_bet['stype'] == 'bet_tot_ing') {
            $tot_bet_money_classic_ing = $row_bet['s_money'];
        } else if ($row_bet['stype'] == 'bet_tot_real') {
            $tot_bet_money_classic_win = $row_bet['s_win_money'];
        }
    }
    

    // 신규 베팅 카운트
    $p_data_bet['sql'] = "select 'sportsCount' AS stype, count(*) as bet_count from member_bet where bet_type = 1 and is_open = 0 and is_classic = 'OFF'";
    $p_data_bet['sql'] .= " UNION ALL ";
    $p_data_bet['sql'] .= "select 'realCount' AS stype, count(*) as bet_count from member_bet where bet_type = 2 and is_open = 0";
    $p_data_bet['sql'] .= " UNION ALL ";
    $p_data_bet['sql'] .= "select 'classicCount' AS stype, count(*) as bet_count from member_bet where bet_type = 1 and is_open = 0 and is_classic = 'ON' ";
    $db_dataBetCount = $COMMONDAO->getQueryData($p_data_bet);

    foreach ($db_dataBetCount as $row_bet) {

        if ($row_bet['stype'] == 'sportsCount') {
            $tot_sports_count = $row_bet['bet_count'];
        } else if ($row_bet['stype'] == 'realCount') {
            $tot_realtime_count = $row_bet['bet_count'];
        } else if ($row_bet['stype'] == 'classicCount') {
            $tot_classic_count = $row_bet['bet_count'];
        }
    }

    if($_SESSION['u_business'] == 0){
        $p_data_mini['sql'] = 'SELECT * FROM day_bet_mini';
    }else{
        $p_data_mini['sql'] = day_bet_mini($_SESSION['member_idx']);
    }
    //CommonUtil::logWrite(" _main_stata_data_view day_bet_mini " . $p_data_mini['sql'], "info");

    $db_dataMiniGame = $COMMONDAO->getQueryData($p_data_mini);

    $bet_tot_mini_powerball = $bet_tot_mini_powerball_win = $bet_tot_mini_pladder = $bet_tot_mini_pladder_win = $bet_tot_mini_kladder = $bet_tot_mini_kladder_win = 0;
    $bet_tot_mini_b_soccer = $bet_tot_mini_b_soccer_win = $bet_tot_mini_eos_powerball = $bet_tot_mini_eos_powerball_win = 0;
    foreach ($db_dataMiniGame as $row_mini) {
        if ($row_mini['stype'] == 'bet_tot_mini_eos_powerball') {
            $bet_tot_mini_eos_powerball = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_eos_powerball_win') {
            $bet_tot_mini_eos_powerball_win = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_powerball') {
            $bet_tot_mini_powerball = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_powerball_win') {
            $bet_tot_mini_powerball_win = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_pladder') {
            $bet_tot_mini_pladder = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_pladder_win') {
            $bet_tot_mini_pladder_win = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_kladder') {
            $bet_tot_mini_kladder = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_kladder_win') {
            $bet_tot_mini_kladder_win = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_b_soccer') {
            $bet_tot_mini_b_soccer = $row_mini['s_money'];
        } else if ($row_mini['stype'] == 'bet_tot_mini_b_soccer_win') {
            $bet_tot_mini_b_soccer_win = $row_mini['s_money'];
        }
    }

    // 카지노 배팅,당첨금  
    // 슬롯 배팅,당첨금 
    if($_SESSION['u_business'] == 0){
        $p_data_ca_sl['sql'] = 'SELECT * FROM day_casino_slot_view';
    }else{
        $p_data_ca_sl['sql'] = day_casino_slot_view($_SESSION['member_idx']);
    }
    //CommonUtil::logWrite(" _main_stata_data_view day_casino_slot_view " . $p_data_ca_sl['sql'], "info");
    $bet_tot_casino_bet = $bet_tot_casino_bet_win = $bet_tot_slot_bet = $bet_tot_slot_bet_win = 0;
    $total_casino_ing_bet_money = $total_slot_ing_bet_money = 0;
    $db_dataCaSi = $COMMONDAO->getQueryData($p_data_ca_sl);
    //print_r($db_dataCaSi);
    foreach ($db_dataCaSi as $row_ca_si) {
        if ($row_ca_si['stype'] == 'bet_tot_casino') {
            $bet_tot_casino_bet = $row_ca_si['total_bet_money'];
            $bet_tot_casino_bet_win = $row_ca_si['total_win_money'];
            $total_casino_ing_bet_money =  $row_ca_si['total_ing_bet_money'];
        } else if ($row_ca_si['stype'] == 'bet_tot_slot') {
            $bet_tot_slot_bet = $row_ca_si['total_bet_money'];
            $bet_tot_slot_bet_win = $row_ca_si['total_win_money'];
            $total_slot_ing_bet_money =  $row_ca_si['total_ing_bet_money'];
        } 
    }

    // 이스포츠, 해시
    if($_SESSION['u_business'] == 0){
        $p_data_ca_sl['sql'] = 'SELECT * FROM day_espt_hash_view';
    }else{
        $p_data_ca_sl['sql'] = day_espt_hash_view($_SESSION['member_idx']);
    }
    $bet_tot_espt_bet = $bet_tot_espt_bet_win = $bet_tot_hash_bet = $bet_tot_hash_bet_win = 0;
    $total_espt_ing_bet_money = $total_hash_ing_bet_money = 0;
    
    $db_dataEsHs = $COMMONDAO->getQueryData($p_data_ca_sl);
    $db_dataEsHs = -40 == $db_dataEsHs || false === isset($db_dataEsHs) ? [] :  $db_dataEsHs;          
    //CommonUtil::logWrite(" _main_stata_data_view db_dataEsHs " . json_encode($db_dataEsHs), "info"); 
    foreach ($db_dataEsHs as $row_es_hs) {
        if ($row_es_hs['stype'] == 'bet_tot_espt') {
            $bet_tot_espt_bet = $row_es_hs['total_bet_money'];
            $bet_tot_espt_bet_win = $row_es_hs['total_win_money'];
            $total_espt_ing_bet_money =  $row_es_hs['total_ing_bet_money'];
        } else if ($row_es_hs['stype'] == 'bet_tot_hash') {
            $bet_tot_hash_bet = $row_es_hs['total_bet_money'];
            $bet_tot_hash_bet_win = $row_es_hs['total_win_money'];
            $total_hash_ing_bet_money =  $row_es_hs['total_ing_bet_money'];
        } 
    }

    $tot_distributor_point = distributorCalculate($COMMONDAO);

    $p_data['sql'] = "select * from total_system";

    $db_data_total_system = $COMMONDAO->getQueryData($p_data);

    $COMMONDAO->dbclose();
}

$tot_ch_cnt_1 = 0;
$tot_ch_cnt_2 = 0;

$result['retCode'] = 1000;
$result['today_tot_money_ch_3'] = number_format($today_tot_money_ch_3);
$result['today_tot_money_ch'] = number_format($today_tot_money_ch_1 + $today_tot_money_ch_2 + $today_tot_money_ch_3);
$result['today_tot_money_ex_3'] = number_format($today_tot_money_ex_3);
$result['today_tot_money_ex'] = number_format($today_tot_money_ex_1 + $today_tot_money_ex_2 + $today_tot_money_ex_3);
$result['today_tot_cnt_ch_3'] = number_format($today_tot_cnt_ch_3);
$result['today_tot_cnt_ex_3'] = number_format($today_tot_cnt_ex_3);
$result['tot_ch_cnt_12'] = number_format($tot_ch_cnt_1 + $tot_ch_cnt_2);

// 총입금
$result['tot_ch_money_3'] = number_format($tot_ch_money_3);
$result['tot_ex_money_3'] = number_format($tot_ex_money_3);


$result['tot_mem_money'] = number_format($tot_mem_money);
$result['tot_mem_point'] = number_format($tot_mem_point);
$result['tot_mem_cnt'] = number_format($tot_mem_cnt);

//스포츠 배팅,당첨,남은 배팅
$result['tot_bet_money_sports'] = number_format($tot_bet_money_sports);
$result['tot_bet_money_sports_win'] = number_format($tot_bet_money_sports_win);
$result['tot_bet_money_sports_ing'] = number_format($tot_bet_money_sports_ing);

//실시간 배팅,당첨,남은 배팅
$result['tot_bet_money_real'] = number_format($tot_bet_money_real);
$result['tot_bet_money_real_win'] = number_format($tot_bet_money_real_win);
$result['tot_bet_money_real_ing'] = number_format($tot_bet_money_real_ing);

//클래식 배팅,당첨,남은 배팅
$result['tot_bet_money_classic'] = number_format($tot_bet_money_classic);
$result['tot_bet_money_classic_win'] = number_format($tot_bet_money_classic_win);
$result['tot_bet_money_classic_ing'] = number_format($tot_bet_money_classic_ing);


// 신규 베팅 카운트
$result['tot_sports_count'] = number_format($tot_sports_count);
$result['tot_realtime_count'] = number_format($tot_realtime_count);
$result['tot_classic_count'] = number_format($tot_classic_count);


// 미니게임 배팅/당첨
$result['tot_bet_money_mini_eos_power'] = number_format($bet_tot_mini_eos_powerball);
$result['tot_bet_money_mini_eos_power_win'] = number_format($bet_tot_mini_eos_powerball_win);
$result['tot_bet_money_mini_power'] = number_format($bet_tot_mini_powerball);
$result['tot_bet_money_mini_power_win'] = number_format($bet_tot_mini_powerball_win);
$result['tot_bet_money_mini_pladder'] = number_format($bet_tot_mini_pladder);
$result['tot_bet_money_mini_pladder_win'] = number_format($bet_tot_mini_pladder_win);
$result['tot_bet_money_mini_kladder'] = number_format($bet_tot_mini_kladder);
$result['tot_bet_money_mini_kladder_win'] = number_format($bet_tot_mini_kladder_win);
$result['tot_bet_money_mini_b_soccer'] = number_format($bet_tot_mini_b_soccer);
$result['tot_bet_money_mini_b_soccer_win'] = number_format($bet_tot_mini_b_soccer_win);

$result['tot_user_conn'] = $db_data_total_system[0]['current_count'];

// 카지노,슬롯
$result['tot_casino_bet'] = number_format($bet_tot_casino_bet);
$result['tot_casino_bet_win'] = number_format($bet_tot_casino_bet_win);
$result['tot_slot_bet'] = number_format($bet_tot_slot_bet);
$result['tot_slot_bet_win'] = number_format($bet_tot_slot_bet_win);


$result['total_casino_ing_bet_money'] = number_format($total_casino_ing_bet_money);
$result['total_slot_ing_bet_money'] = number_format($total_slot_ing_bet_money);

// 이스포츠/키론/해시
$result['tot_espt_bet'] = number_format($bet_tot_espt_bet);
$result['tot_espt_bet_win'] = number_format($bet_tot_espt_bet_win);
$result['tot_hash_bet'] = number_format($bet_tot_hash_bet);
$result['tot_hash_bet_win'] = number_format($bet_tot_hash_bet_win);
$result['total_espt_ing_bet_money'] = number_format($total_espt_ing_bet_money);
$result['total_hash_ing_bet_money'] = number_format($total_hash_ing_bet_money);

// 총판지급액
$result['tot_distributor_point'] = number_format($tot_distributor_point);

echo json_encode($result, JSON_UNESCAPED_UNICODE);

function distributorCalculateRecursive(){
    
}
// 오늘자 총판지급액
function distributorCalculate($COMMONDAO) {
    $where = '';
    $db_srch_s_date = date("Y-m-d 00:00:00");
    $db_srch_e_date = date("Y-m-d 23:59:59");

    // 요율설정
    $p_data['sql'] = "select * from shop_config";
    $result = $COMMONDAO->getQueryData($p_data);
    $shopConfig = null;

    if (true == isset($result) && false == empty($result)) {
        foreach ($result as $key => $value) {
            $shopConfig[$value['member_idx']] = $value;
        }
    }

    $p_data['sql'] = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'users_excluded_from_settlement' ";
    $result_game_config = $COMMONDAO->getQueryData($p_data);
    $excluded_member_idx = $result_game_config[0]['set_type_val'];

    // 멤버 베팅
    $p_data['sql'] = ComQuery::doSportsRealBetQuery($db_srch_s_date, $db_srch_e_date, "");
    //CommonUtil::logWrite("main log sports_real : " . $p_data['sql'], "info");
    
    $param = [$db_srch_s_date, $db_srch_e_date];
    $db_dataArr = $COMMONDAO->getQueryData_pre($p_data['sql'],$param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

    // 정산 합산용 변수
    $bet_pre_s_fee = 0;
    $bet_pre_d_fee = 0;
    $bet_real_s_fee = 0;
    $bet_real_d_fee = 0;
    $bet_mini_fee = 0;

    if (true == isset($db_dataArr) && false == empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            // 베팅롤링 정산
            if ($shopConfig[$row['dis_idx']]['bet_pre_s_fee'] > 0)
                $bet_pre_s_fee += $row['pre_bet_sum_s'] * ($shopConfig[$row['dis_idx']]['bet_pre_s_fee'] / 100);
            if ($shopConfig[$row['dis_idx']]['bet_pre_d_fee'] > 0)
                $bet_pre_d_fee += $row['pre_bet_sum_d'] * ($shopConfig[$row['dis_idx']]['bet_pre_d_fee'] / 100);
            if ($shopConfig[$row['dis_idx']]['bet_real_s_fee'] > 0)
                $bet_real_s_fee += $row['real_bet_sum_s'] * ($shopConfig[$row['dis_idx']]['bet_real_s_fee'] / 100);
            if ($shopConfig[$row['dis_idx']]['bet_real_d_fee'] > 0)
                $bet_real_d_fee += $row['real_bet_sum_d'] * ($shopConfig[$row['dis_idx']]['bet_real_d_fee'] / 100);
        }
    }

    // 미니게임 베팅
    $param = array_merge([$db_srch_s_date,$db_srch_e_date],[$excluded_member_idx]);
    //array_push($param,$excluded_member_idx);
    $p_data['sql'] = ComQuery::doMiniBetQuery($db_srch_s_date, $db_srch_e_date, $excluded_member_idx, '');
    $db_dataArr = $COMMONDAO->getQueryData_pre($p_data['sql'],$param);

    //CommonUtil::logWrite("main log mini_bet : " . $p_data['sql'], "info");
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

    if (true == isset($db_dataArr) && false == empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            // 베팅롤링 정산
            if ($shopConfig[$row['dis_idx']]['bet_mini_fee'] > 0)
                $bet_mini_fee += $row['mini_bet_sum_d'] * ($shopConfig[$row['dis_idx']]['bet_mini_fee'] / 100);

            // 순수익 정산
            //if ($shopConfig[$row['dis_idx']]['mini_fee'] > 0)
                //$mini_fee += $row['mini_sum_d'] * ($shopConfig[$row['dis_idx']]['mini_fee'] / 100);
        }
    }

    // 충전 환전 -- child.recommend_member = parent.idx
    list($p_data['sql'],$param) = ComQuery::doComChExQuery($db_srch_s_date, $db_srch_e_date, "AND 1 = 1",[]);
    //CommonUtil::logWrite("main log ch_ex : " . $p_data['sql'], "info");
    $db_dataArr = $COMMONDAO->getQueryData_pre($p_data['sql'],$param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

    // 충전,환전 합산 구하기 
    $tot_ch_val = $tot_ex_val = 0;
    $convert_arr = [];
    foreach ($db_dataArr as $row) {
        $str_dt = str_replace('-', '', $row['up_dt']);

        if ($row['stype'] == 'ch') {

            $convert_arr[$str_dt][$row['dis_idx']]['ch_val'] = $row['s_money'];
        } elseif ($row['stype'] == 'ex') {

            $convert_arr[$str_dt][$row['dis_idx']]['ex_val'] = $row['s_money'];
        }
    }

    $total_point = 0;
    $total_ch = 0;
    $total_ex = 0;
    foreach ($convert_arr as $key => $date) {
        $str_dt = $key;
        foreach ($date as $key_idx => $row) {
            $dis_idx = $key_idx;
            $ch_val = true === isset($row['ch_val']) ? $row['ch_val'] : 0;
            $ex_val = true === isset($row['ex_val']) ? $row['ex_val'] : 0;
            $total_ch = $total_ch + $ch_val;
            $total_ex = $total_ex + $ex_val;
            if (true === isset($shopConfig[$dis_idx]['pre_s_fee'])) {
                $total_point += ($ch_val - $ex_val) * ($shopConfig[$dis_idx]['pre_s_fee'] / 100);
            }
        }
    }

    $total_fee = $total_point + $bet_pre_s_fee + $bet_pre_d_fee + $bet_real_s_fee + $bet_real_d_fee + $bet_mini_fee;


    return $total_fee;
}

// 총판용도
function day_ch_ex($member_idx) {
    return "SELECT 
        'ch' AS `stype`,
        `mch`.`status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`mch`.`money`) AS `s_money`
    FROM
        (`member_money_charge_history` `mch`
        LEFT JOIN `member` ON (`mch`.`member_idx` = `member`.`idx`))
    WHERE
        `mch`.`update_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mch`.`update_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
    GROUP BY `mch`.`status` 
    UNION ALL SELECT 
        'ex' AS `stype`,
        `meh`.`status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`meh`.`money`) AS `s_money`
    FROM
        (`member_money_exchange_history` `meh`
        LEFT JOIN `member` ON (`meh`.`member_idx` = `member`.`idx`))
    WHERE
        CAST(`meh`.`update_dt` AS DATE) >= CAST(CURRENT_TIMESTAMP() AS DATE)
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
    GROUP BY `meh`.`status` 
    UNION ALL SELECT 
        'ch_tot' AS `stype`,
        `mch`.`status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`mch`.`money`) AS `s_money`
    FROM
        (`member_money_charge_history` `mch`
        LEFT JOIN `member` ON (`mch`.`member_idx` = `member`.`idx`))
    WHERE
        `mch`.`update_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mch`.`update_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
    GROUP BY `mch`.`status` 
    UNION ALL SELECT 
        'ex_tot' AS `stype`,
        `meh`.`status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`meh`.`money`) AS `s_money`
    FROM
        (`member_money_exchange_history` `meh`
        LEFT JOIN `member` ON (`meh`.`member_idx` = `member`.`idx`))
    WHERE
        `meh`.`update_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `meh`.`update_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
    GROUP BY `meh`.`status`";
}

function day_bet_pre($member_idx) {
    return "SELECT 
        'bet_tot_ing' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`member_bet`.`total_bet_money`) AS `s_money`,
        0 AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`bet_type` = 1 AND member_bet.is_classic = 'OFF'
            AND `member_bet`.`bet_status` = 1
            AND `member`.`level` <> 9 
            AND `member`.`recommend_member` = $member_idx
    UNION ALL SELECT 
        'bet_tot' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        `member_bet`.`total_bet_money` AS `s_money`,
        `member_bet`.`take_money` AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member_bet`.`bet_type` = 1
            AND `member_bet`.`bet_status` = 3 AND member_bet.is_classic = 'OFF'
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `member_bet`.`total_bet_money` <> `member_bet`.`take_money`
    GROUP BY `member_bet`.`idx` 
    UNION ALL SELECT 
        'bet_tot_sports' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        `member_bet`.`total_bet_money` AS `s_money`,
        SUM(`member_bet`.`take_money`) AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member_bet`.`bet_type` = 1
            AND `member_bet`.`bet_status` = 3
            AND `member_bet`.`total_bet_money` <> `member_bet`.`take_money` AND member_bet.is_classic = 'OFF'
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx";
}

function day_bet_real($member_idx) {
    return "SELECT 
        'bet_tot_ing' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`member_bet`.`total_bet_money`) AS `s_money`,
        0 AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`bet_type` = 2
            AND `member_bet`.`bet_status` = 1
            AND `member`.`level` <> 9 
            AND `member`.`recommend_member` = $member_idx
    UNION ALL SELECT 
        'bet_tot' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        `member_bet`.`total_bet_money` AS `s_money`,
        `member_bet`.`take_money` AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member_bet`.`bet_type` = 2
            AND `member_bet`.`bet_status` = 3
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `member_bet`.`total_bet_money` <> `member_bet`.`take_money`
    GROUP BY `member_bet`.`u_key` 
    UNION ALL SELECT 
        'bet_tot_real' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        `member_bet`.`total_bet_money` AS `s_money`,
        SUM(`member_bet`.`take_money`) AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member_bet`.`bet_type` = 2
            AND `member_bet`.`bet_status` = 3
            AND `member_bet`.`total_bet_money` <> `member_bet`.`take_money`
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx";
}

function day_bet_classic($member_idx) {
   return "SELECT 
        'bet_tot_ing' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        SUM(`member_bet`.`total_bet_money`) AS `s_money`,
        0 AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`bet_type` = 1
            AND `member_bet`.`bet_status` = 1 AND member_bet.is_classic = 'ON'
            AND `member`.`level` <> 9 
            AND `member`.`recommend_member` = $member_idx
    UNION ALL SELECT 
        'bet_tot' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        `member_bet`.`total_bet_money` AS `s_money`,
        `member_bet`.`take_money` AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member_bet`.`bet_type` = 1
            AND `member_bet`.`bet_status` = 3 AND member_bet.is_classic = 'ON'
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `member_bet`.`total_bet_money` <> `member_bet`.`take_money`
    GROUP BY `member_bet`.`idx` 
    UNION ALL SELECT 
        'bet_tot_sports' AS `stype`,
        `member_bet`.`bet_status` AS `status`,
        COUNT(0) AS `cnt`,
        `member_bet`.`total_bet_money` AS `s_money`,
        SUM(`member_bet`.`take_money`) AS `s_win_money`
    FROM
        (`member_bet`
        LEFT JOIN `member` ON (`member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `member_bet`.`bet_type` = 1
            AND `member_bet`.`bet_status` = 3
            AND `member_bet`.`total_bet_money` <> `member_bet`.`take_money` AND member_bet.is_classic = 'ON'
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx";
}

function day_bet_mini($member_idx) {
    return "SELECT 
        'bet_tot_mini_powerball' AS `stype`,
        SUM(`mini_game_member_bet`.`total_bet_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 3
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `mini_game_member_bet`.`total_bet_money` <> `mini_game_member_bet`.`take_money` 
    UNION ALL SELECT 
        'bet_tot_mini_powerball_win' AS `stype`,
        SUM(`mini_game_member_bet`.`take_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 3
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `mini_game_member_bet`.`take_money` > 0
            AND `member`.`level` <> 9 
            AND `member`.`recommend_member` = $member_idx
    UNION ALL SELECT 
        'bet_tot_mini_pladder' AS `stype`,
        SUM(`mini_game_member_bet`.`total_bet_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 4
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `mini_game_member_bet`.`total_bet_money` <> `mini_game_member_bet`.`take_money` 
    UNION ALL SELECT 
        'bet_tot_mini_pladder_win' AS `stype`,
        SUM(`mini_game_member_bet`.`take_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 4
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `mini_game_member_bet`.`take_money` > 0
            AND `member`.`level` <> 9 
            AND `member`.`recommend_member` = $member_idx
    UNION ALL SELECT 
        'bet_tot_mini_kladder' AS `stype`,
        SUM(`mini_game_member_bet`.`total_bet_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 5
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `mini_game_member_bet`.`total_bet_money` <> `mini_game_member_bet`.`take_money` 
    UNION ALL SELECT 
        'bet_tot_mini_kladder_win' AS `stype`,
        SUM(`mini_game_member_bet`.`take_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 5
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `mini_game_member_bet`.`take_money` > 0
            AND `member`.`level` <> 9 
            AND `member`.`recommend_member` = $member_idx
    UNION ALL SELECT 
        'bet_tot_mini_b_soccer' AS `stype`,
        SUM(`mini_game_member_bet`.`total_bet_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 6
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx
            AND `mini_game_member_bet`.`total_bet_money` <> `mini_game_member_bet`.`take_money` 
    UNION ALL SELECT 
        'bet_tot_mini_b_soccer_win' AS `stype`,
        SUM(`mini_game_member_bet`.`take_money`) AS `s_money`
    FROM
        (`mini_game_member_bet`
        LEFT JOIN `member` ON (`mini_game_member_bet`.`member_idx` = `member`.`idx`))
    WHERE
        `mini_game_member_bet`.`calculate_dt` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `mini_game_member_bet`.`calculate_dt` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `mini_game_member_bet`.`bet_type` = 6
            AND `mini_game_member_bet`.`bet_status` = 3
            AND `mini_game_member_bet`.`take_money` > 0
            AND `member`.`level` <> 9
            AND `member`.`recommend_member` = $member_idx";
}

function day_casino_slot_view($member_idx) {
    return "SELECT 
        'bet_tot_casino' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'B' THEN `CBH`.`BET_MNY`
                END),
                0) AS `total_ing_bet_money`
    FROM
        (`member` `MB`
        LEFT JOIN `KP_CSN_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
    WHERE
        `CBH`.`MOD_DTM` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `CBH`.`MOD_DTM` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `MB`.`level` <> 9
            AND `MB`.`u_business` = 1
            AND `MB`.`recommend_member` = $member_idx
            AND `CBH`.`TYPE` IN ('W' , 'L') 
    UNION ALL SELECT 
        'bet_tot_slot' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'B' THEN `CBH`.`BET_MNY`
                END),
                0) AS `total_ing_bet_money`
    FROM
        (`member` `MB`
        LEFT JOIN `KP_SLOT_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
    WHERE
        `CBH`.`MOD_DTM` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `CBH`.`MOD_DTM` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `MB`.`level` <> 9
            AND `MB`.`u_business` = 1
            AND `MB`.`recommend_member` = $member_idx
            AND `CBH`.`TYPE` IN ('W' , 'L')";
}

function day_espt_hash_view($member_idx) {
    return "SELECT 
        'bet_tot_espt' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'B' THEN `CBH`.`BET_MNY`
                END),
                0) AS `total_ing_bet_money`
    FROM
        (`member` `MB`
        LEFT JOIN `KP_ESPT_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
    WHERE
        `CBH`.`MOD_DTM` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `CBH`.`MOD_DTM` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `MB`.`level` <> 9
            AND `MB`.`u_business` = 1
            AND `MB`.`recommend_member` = $member_idx
            AND `CBH`.`TYPE` IN ('W' , 'L') 
    UNION ALL SELECT 
        'bet_tot_hash' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'B' THEN `CBH`.`BET_MNY`
                END),
                0) AS `total_ing_bet_money`
    FROM
        (`member` `MB`
        LEFT JOIN `OD_HASH_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
    WHERE
        `CBH`.`MOD_DTM` >= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 00:00:00')
            AND `CBH`.`MOD_DTM` <= DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d 23:59:59')
            AND `MB`.`level` <> 9
            AND `MB`.`u_business` = 1
            AND `MB`.`recommend_member` = $member_idx
            AND `CBH`.`TYPE` IN ('W' , 'L')";
}
?>
