<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');
$UTIL = new CommonUtil();
if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$result["retCode"] = 2001;
$result["retData"] = '';

$p_data['ptype'] = trim(isset($_POST['ptype']) ? $_POST['ptype'] : '');
$p_data['idx'] = trim(isset($_POST['idx']) ? $_POST['idx'] : 0);
$p_data['reg_first_per'] = trim(isset($_POST['reg_first_per']) ? $_POST['reg_first_per'] : 0);

$p_data['g_level'] = trim(isset($_POST['g_level']) ? $_POST['g_level'] : 0);
$p_data['g_pre_money'] = trim(isset($_POST['g_pre_money']) ? $_POST['g_pre_money'] : 0);
$p_data['g_real_money'] = trim(isset($_POST['g_real_money']) ? $_POST['g_real_money'] : 0);

$p_data['w_bonus_f'] = trim(isset($_POST['w_bonus_f']) ? $_POST['w_bonus_f'] : '');
$p_data['w_odds_3'] = trim(isset($_POST['w_odds_3']) ? $_POST['w_odds_3'] : 0);
$p_data['w_odds_4'] = trim(isset($_POST['w_odds_4']) ? $_POST['w_odds_4'] : 0);
$p_data['w_odds_5'] = trim(isset($_POST['w_odds_5']) ? $_POST['w_odds_5'] : 0);
$p_data['w_odds_6'] = trim(isset($_POST['w_odds_6']) ? $_POST['w_odds_6'] : 0);
$p_data['w_odds_7'] = trim(isset($_POST['w_odds_7']) ? $_POST['w_odds_7'] : 0);

$p_data['w_site_f'] = trim(isset($_POST['w_site_f']) ? $_POST['w_site_f'] : '');
$p_data['w_charge_f'] = trim(isset($_POST['w_charge_f']) ? $_POST['w_charge_f'] : '');
$p_data['w_exchange_f'] = trim(isset($_POST['w_exchange_f']) ? $_POST['w_exchange_f'] : '');
$p_data['w_board_f'] = trim(isset($_POST['w_board_f']) ? $_POST['w_board_f'] : '');
$p_data['w_coin_charge_f'] = trim(isset($_POST['w_coin_charge_f']) ? $_POST['w_coin_charge_f'] : '');
$p_data['w_sports_f'] = trim(isset($_POST['w_sports_f']) ? $_POST['w_sports_f'] : '');
$p_data['w_classic_f'] = trim(isset($_POST['w_classic_f']) ? $_POST['w_classic_f'] : '');
$p_data['w_real_f'] = trim(isset($_POST['w_real_f']) ? $_POST['w_real_f'] : '');

$p_data['w_mini_eos_pb'] = trim(isset($_POST['w_mini_eos_pb']) ? $_POST['w_mini_eos_pb'] : '');
$p_data['w_mini_pb'] = trim(isset($_POST['w_mini_pb']) ? $_POST['w_mini_pb'] : '');
$p_data['w_mini_v_soccer'] = trim(isset($_POST['w_mini_v_soccer']) ? $_POST['w_mini_v_soccer'] : '');
$p_data['w_mini_p_ladder'] = trim(isset($_POST['w_mini_p_ladder']) ? $_POST['w_mini_p_ladder'] : '');
$p_data['w_mini_baccarat'] = trim(isset($_POST['w_mini_baccarat']) ? $_POST['w_mini_baccarat'] : '');
$p_data['w_mini_k_ladder'] = trim(isset($_POST['w_mini_k_ladder']) ? $_POST['w_mini_k_ladder'] : '');
$p_data['w_mini_roulette'] = trim(isset($_POST['w_mini_roulette']) ? $_POST['w_mini_roulette'] : '');
$p_data['w_mini_power_pk'] = trim(isset($_POST['w_mini_power_pk']) ? $_POST['w_mini_power_pk'] : '');
$p_data['w_mini_hilow'] = trim(isset($_POST['w_mini_hilow']) ? $_POST['w_mini_hilow'] : '');


$pre_dividen_1 = isset($_POST['pre_dividen_1']) ? $_POST['pre_dividen_1'] : [];
$pre_dividen_2 = isset($_POST['pre_dividen_2']) ? $_POST['pre_dividen_2'] : [];
$pre_dividen_3 = isset($_POST['pre_dividen_3']) ? $_POST['pre_dividen_3'] : [];
$pre_dividen_4 = isset($_POST['pre_dividen_4']) ? $_POST['pre_dividen_4'] : [];
$classic_dividen_1 = isset($_POST['classic_dividen_1']) ? $_POST['classic_dividen_1'] : [];
$classic_dividen_2 = isset($_POST['classic_dividen_2']) ? $_POST['classic_dividen_2'] : [];
$classic_dividen_3 = isset($_POST['classic_dividen_3']) ? $_POST['classic_dividen_3'] : [];
$classic_dividen_4 = isset($_POST['classic_dividen_4']) ? $_POST['classic_dividen_4'] : [];
$real_dividen_1 = isset($_POST['real_dividen_1']) ? $_POST['real_dividen_1'] : [];
$real_dividen_2 = isset($_POST['real_dividen_2']) ? $_POST['real_dividen_2'] : [];
$real_dividen_3 = isset($_POST['real_dividen_3']) ? $_POST['real_dividen_3'] : [];
$real_dividen_4 = isset($_POST['real_dividen_4']) ? $_POST['real_dividen_4'] : [];

$p_data['w_casino_f'] = trim(isset($_POST['w_casino_f']) ? $_POST['w_casino_f'] : '');
$p_data['w_slot_f'] = trim(isset($_POST['w_slot_f']) ? $_POST['w_slot_f'] : '');

$p_data['w_esports_f'] = trim(isset($_POST['w_esports_f']) ? $_POST['w_esports_f'] : '');
$p_data['w_kiron_f'] = trim(isset($_POST['w_kiron_f']) ? $_POST['w_kiron_f'] : '');
$p_data['w_hash_f'] = trim(isset($_POST['w_hash_f']) ? $_POST['w_hash_f'] : '');
$p_data['w_holdem_f'] = trim(isset($_POST['w_holdem_f']) ? $_POST['w_holdem_f'] : '');

//CommonUtil::logWrite('_set_config_game pre_dividen_1: '.json_encode($_POST['pre_dividen_1']), "error");
//CommonUtil::logWrite('_set_config_game pre_dividen_1_1: '.json_encode($pre_dividen_1), "error");


$set_type_arr = array("pre_min_money", "pre_max_money", "pre_limit_money", "classic_min_money", "classic_max_money", "classic_limit_money", "real_min_money", "real_max_money", "real_limit_money"
    , "lose_self_per", "lose_recomm_per", "charge_first_per", "charge_max_money", "charge_per", "charge_money", "service_coin_charge"
    , "service_sports", "service_real", "service_esports", "service_kiron", "service_hash", "service_holdem");
//CommonUtil::logWrite('_set_config_game : '.json_encode($p_data), "error");
if ($p_data['ptype'] == 'reg_first_charge') {
    
} else if ($p_data['ptype'] == 'bet_config_level') {

    foreach ($set_type_arr as $key => $val) {
        if(false == isset($_POST[$val])){
            continue;
        }
        foreach ($_POST[$val] as $key => $value) {
            $level = $key + 1;
            $game_bet_config[$level][$val] = $value;
          
        }
    }
} else if (($p_data['ptype'] == 'reg_game_level') || ($p_data['ptype'] == 'del_game_level') || ($p_data['ptype'] == 'mod_game_level')) {
    
} else if (($p_data['ptype'] == 'w_config_all') || ($p_data['ptype'] == 'w_bonus') || ($p_data['ptype'] == 'w_site') || ($p_data['ptype'] == 'w_mini')) {
    
} else {
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {
    $now_ip = CommonUtil::get_client_ip();
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $MEMAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    if ($p_data['ptype'] == 'reg_first_charge') {

        $p_data['sql'] = "select set_type_val FROM t_game_config ";
        $p_data['sql'] .= " where set_type='" . $p_data['ptype'] . "' ";

        $retData = $MEMAdminDAO->getQueryData($p_data);
        $db_set_type_val = $retData[0]['set_type_val'];

        if ($db_set_type_val != '') {
            $log_data = "가입 첫충  [" . $p_data['reg_first_per'] . "] 업데이트";

            $p_data['sql'] = "update t_game_config set ";
            $p_data['sql'] .= " set_type_val='" . $p_data['reg_first_per'] . "' ";
            $p_data['sql'] .= " where u_level=0 and set_type='" . $p_data['ptype'] . "' ";
        } else {
            $log_data = "가입 첫충  [" . $p_data['reg_first_per'] . "] 등록";
            $title = "가입 첫충";
            $p_data['sql'] = "insert into t_game_config ";
            $p_data['sql'] .= " (u_level, set_type, set_type_val, title) ";
            $p_data['sql'] .= " values(0, '" . $p_data['ptype'] . "', '" . $p_data['reg_first_per'] . "','" . $title . "') ";
        }

        $dbRet = $MEMAdminDAO->setQueryData($p_data);

        if ($dbRet) {
            $p_data['sql'] = "insert into t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 5) ";

            $MEMAdminDAO->setQueryData($p_data);

            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
    } else if ($p_data['ptype'] == 'bet_config_level') {


        foreach ($set_type_arr as $key => $val) {

            $db_set_data = array();

            $p_data['sql'] = "select u_level, set_type, set_type_val FROM t_game_config ";
            $p_data['sql'] .= " where set_type = '$val' order by set_type, u_level ";
            $db_dataArr = $MEMAdminDAO->getQueryData($p_data);

            if (!empty($db_dataArr)) {
                foreach ($db_dataArr as $row) {
                    $level = $row['u_level'];
                    if ($row['set_type_val'] == '')
                        $row['set_type_val'] = 0;

                    $db_set_data[$level] = $row['set_type_val'];
                }
            }

            $in_sql = " insert into t_game_config (u_level, set_type, set_type_val, title) values ";
            $in_sub = "";

            for ($i = 1; $i <= 10; $i++) {
                
                if(false == isset($game_bet_config[$i][$val])){
                    continue;
                }
                if (($db_set_data[$i] != '') || ($db_set_data[$i] > -1)) {
                    if ($db_set_data[$i] != $game_bet_config[$i][$val]) {
                        $p_data['sql'] = "update t_game_config set ";
                        $p_data['sql'] .= " set_type_val='" . $game_bet_config[$i][$val] . "' ";
                        $p_data['sql'] .= " where u_level=$i and set_type='$val' ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                } else {
                    //insert
                    if ($in_sub != '') {
                        $in_sub .= ",";
                    }

                    $in_sub .= "($i,'$val','" . $game_bet_config[$i][$val] . "','프리매치 최소')";
                }
            }

            if ($in_sub != '') {
                $p_data['sql'] = $in_sql . $in_sub;

                $MEMAdminDAO->setQueryData($p_data);
            }
        }


        // prematch
        for($i = 1; $i <= 10; ++$i){
           $amount = $pre_dividen_1[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 1 and type = 1 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           
           CommonUtil::logWrite('_set_config_game pre_dividen_1 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $pre_dividen_2[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 2 and type = 1 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game pre_dividen_2 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $pre_dividen_3[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 3 and type = 1 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game pre_dividen_3 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $pre_dividen_4[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 4 and type = 1 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game pre_dividen_4 sql: '.$p_data['sql'] , "error");
        }
        
        // classic
        for($i = 1; $i <= 10; ++$i){
           $amount = $classic_dividen_1[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 1 and type = 20 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           
           CommonUtil::logWrite('_set_config_game classic_dividen_1 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $classic_dividen_2[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 2 and type = 20 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game classic_dividen_2 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $classic_dividen_3[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 3 and type = 20 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game classic_dividen_3 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $classic_dividen_4[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 4 and type = 20 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game classic_dividen_4 sql: '.$p_data['sql'] , "error");
        }
        
        
        // real
        for($i = 1; $i <= 10; ++$i){
           $amount = $real_dividen_1[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 1 and type = 2 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game real_dividen_1 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $real_dividen_2[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 2 and type = 2 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game real_dividen_2 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $real_dividen_3[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 3 and type = 2 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game real_dividen_3 sql: '.$p_data['sql'] , "error");
        }
        
        for($i = 1; $i <= 10; ++$i){
           $amount = $real_dividen_4[$i-1];
           $p_data['sql'] = "update dividend_policy set amount = $amount where rank = 4 and type = 2 and level = $i ";
           $MEMAdminDAO->setQueryData($p_data);
           CommonUtil::logWrite('_set_config_game real_dividen_4 sql: '.$p_data['sql'] , "error");
        }
     

        //CommonUtil::logWrite('_set_config_game dividen end: ', "error");
        //$MEMAdminDAO->setQueryData($p_data);



        $result["retCode"] = 1000;
        $result["retData"] = '';
    } else if ($p_data['ptype'] == 'reg_game_level') {

        $log_data = "등급별 베팅금액 설정  등록 [" . $p_data['g_level'] . "] [" . $p_data['g_pre_money'] . "] [" . $p_data['g_real_money'] . "]";
        $title = "등급별 베팅금액 설정";
        $p_data['sql'] = "insert into t_game_config ";
        $p_data['sql'] .= " (u_level, set_type, set_type_val, title) values ";
        $p_data['sql'] .= " (" . $p_data['g_level'] . ", 'game_level_pre', '" . $p_data['g_pre_money'] . "','" . $title . "') ";
        $p_data['sql'] .= " ,(" . $p_data['g_level'] . ", 'game_level_real', '" . $p_data['g_real_money'] . "','" . $title . "') ";

        $dbRet = $MEMAdminDAO->setQueryData($p_data);

        if ($dbRet) {
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 6) ";

            $MEMAdminDAO->setQueryData($p_data);

            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
    } else if (($p_data['ptype'] == 'mod_game_level') || ($p_data['ptype'] == 'del_game_level')) {

        $p_data['sql'] = "select u_level, set_type, set_type_val FROM t_game_config ";
        $p_data['sql'] .= " where u_level = " . $p_data['idx'] . " and (set_type='game_level_pre' OR set_type='game_level_real') ";
        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);

        if (!empty($db_dataArr)) {
            foreach ($db_dataArr as $row) {
                if ($row['set_type'] == 'game_level_pre') {
                    $db_game_level_pre_money = $row['set_type_val'];
                } else if ($row['set_type'] == 'game_level_real') {
                    $db_game_level_real_money = $row['set_type_val'];
                }
            }
        }


        if ($p_data['ptype'] == 'del_game_level') {
            $log_data = "등급별 베팅금액 설정 삭제  [" . $db_dataArr[0]['u_level'] . "] [" . $db_game_level_pre_money . "] [" . $db_game_level_real_money . "]";

            $p_data['sql'] = " delete from t_game_config ";
            $p_data['sql'] .= " where u_level = " . $p_data['idx'] . " and (set_type='game_level_pre' OR set_type='game_level_real') ";

            $dbRet = $MEMAdminDAO->setQueryData($p_data);
        } else if ($p_data['ptype'] == 'mod_game_level') {
            $log_data = "등급별 베팅금액 설정 수정  [" . $db_dataArr[0]['u_level'] . "] [" . $db_game_level_pre_money . "] [" . $db_game_level_real_money . "]";

            $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['g_pre_money'] . "' ";
            $p_data['sql'] .= " where u_level = " . $p_data['idx'] . " and set_type='game_level_pre' ";
            $dbRet = $MEMAdminDAO->setQueryData($p_data);

            $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['g_real_money'] . "' ";
            $p_data['sql'] .= " where u_level = " . $p_data['idx'] . " and set_type='game_level_real' ";
            $dbRet = $MEMAdminDAO->setQueryData($p_data);
        }

        if ($dbRet) {
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 6) ";

            $MEMAdminDAO->setQueryData($p_data);

            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
    } else if (($p_data['ptype'] == 'w_config_all') || ($p_data['ptype'] == 'w_bonus') || ($p_data['ptype'] == 'w_site') || ($p_data['ptype'] == 'w_mini')) {

        $str_service_bonus_folder = "service_bonus_folder";
        $str_odds_folder_3 = "odds_3_folder_bonus";
        $str_odds_folder_4 = "odds_4_folder_bonus";
        $str_odds_folder_5 = "odds_5_folder_bonus";
        $str_odds_folder_6 = "odds_6_folder_bonus";
        $str_odds_folder_7 = "odds_7_folder_bonus";

        $str_service_exchange = "service_exchange";
        $str_service_charge = "service_charge";
        $str_service_board = "service_board";
        $str_service_site = "service_site";
        $str_service_coin_charge = "service_coin_charge";
        $str_service_sports = "service_sports";
        $str_service_classic = "service_classic";
        $str_service_real = "service_real";

        $str_mini_eos_powerball = "mini_service_eos_powerball";
        $str_mini_powerball = "mini_service_powerball";
        $str_mini_power_ladder = "mini_service_power_ladder";
        $str_mini_kino_ladder = "mini_service_kino_ladder";
        $str_mini_power_pk = "mini_service_power_pk";
        $str_mini_v_soccer = "mini_service_v_soccer";
        $str_mini_baccarat = "mini_service_baccarat";
        $str_mini_roulette = "mini_service_roulette";
        $str_mini_hilow = "mini_service_hilow";
        $str_service_casino = "service_casino";
        $str_service_slot = "service_slot";
        $str_service_esports = "service_esports";
        $str_service_kiron = "service_kiron";
        $str_service_hash = "service_hash";
        $str_service_holdem = "service_holdem";

        $str_where_in = "'$str_service_bonus_folder', '$str_odds_folder_3', '$str_odds_folder_4', '$str_odds_folder_5', '$str_odds_folder_6', '$str_odds_folder_7'";
        $str_where_in .= ",'$str_service_exchange','$str_service_charge','$str_service_board','$str_service_site','$str_service_coin_charge','$str_service_sports','$str_service_real'";
        $str_where_in .= ",'$str_mini_eos_powerball','$str_mini_powerball','$str_mini_power_ladder','$str_mini_kino_ladder','$str_mini_power_pk'";
        $str_where_in .= ",'$str_mini_v_soccer','$str_mini_baccarat','$str_mini_roulette','$str_mini_hilow','$str_service_casino','$str_service_slot'";
        $str_where_in .= ",'$str_service_esports','$str_service_kiron','$str_service_hash','$str_service_classic','$str_service_holdem'";

        $db_w_service_bonus_folder = $db_w_odds_3_folder_bonus = $db_w_odds_4_folder_bonus = $db_w_odds_5_folder_bonus = $db_w_odds_6_folder_bonus = $db_w_odds_7_folder_bonus = '-1';
        $db_w_service_exchange = $db_w_service_charge = $db_w_service_board = $db_w_service_site = $db_w_service_coin_charge = $db_w_service_sports = $db_w_service_class = $db_w_service_real = '-1';
        $db_w_mini_eos_powerball = $db_w_mini_powerball = $db_w_mini_p_ladder = $db_w_mini_k_ladder = $db_w_mini_power_pk = '-1';
        $db_w_mini_v_soccer = $db_w_mini_baccarat = $db_w_mini_roulette = $db_w_mini_hilow = '-1';
        $db_w_service_casino = $db_w_service_slot = '-1';
        $db_w_service_esports = $db_w_service_kiron= $db_w_service_hash = $db_w_service_holdem = '-1';

        $p_data['sql'] = "select u_level, set_type, set_type_val FROM t_game_config ";
        $p_data['sql'] .= " where set_type in ($str_where_in) ";
        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);

        if (!empty($db_dataArr)) {
            foreach ($db_dataArr as $row) {


                switch ($row['set_type']) {
                    case $str_service_bonus_folder: $db_w_service_bonus_folder = $row['set_type_val'];
                        break;
                    case $str_odds_folder_3: $db_w_odds_3_folder_bonus = $row['set_type_val'];
                        break;
                    case $str_odds_folder_4: $db_w_odds_4_folder_bonus = $row['set_type_val'];
                        break;
                    case $str_odds_folder_5: $db_w_odds_5_folder_bonus = $row['set_type_val'];
                        break;
                    case $str_odds_folder_6: $db_w_odds_6_folder_bonus = $row['set_type_val'];
                        break;
                    case $str_odds_folder_7: $db_w_odds_7_folder_bonus = $row['set_type_val'];
                        break;

                    case $str_service_exchange: $db_w_service_exchange = $row['set_type_val'];
                        break;
                    case $str_service_charge: $db_w_service_charge = $row['set_type_val'];
                        break;
                    case $str_service_board: $db_w_service_board = $row['set_type_val'];
                        break;
                    case $str_service_site: $db_w_service_site = $row['set_type_val'];
                        break;
                    case $str_service_coin_charge: $db_w_service_coin_charge = $row['set_type_val'];
                        break;
                    case $str_service_sports: $db_w_service_sports = $row['set_type_val'];
                        break;
                    case $str_service_classic: $db_w_service_class = $row['set_type_val'];
                        break;
                    case $str_service_real: $db_w_service_real = $row['set_type_val'];
                        break;

                    case $str_mini_eos_powerball: $db_w_mini_eos_powerball = $row['set_type_val'];
                        break;
                    case $str_mini_powerball: $db_w_mini_powerball = $row['set_type_val'];
                        break;
                    case $str_mini_power_ladder: $db_w_mini_p_ladder = $row['set_type_val'];
                        break;
                    case $str_mini_kino_ladder: $db_w_mini_k_ladder = $row['set_type_val'];
                        break;
                    case $str_mini_power_pk: $db_w_mini_power_pk = $row['set_type_val'];
                        break;
                    case $str_mini_v_soccer: $db_w_mini_v_soccer = $row['set_type_val'];
                        break;
                    case $str_mini_baccarat: $db_w_mini_baccarat = $row['set_type_val'];
                        break;
                    case $str_mini_roulette: $db_w_mini_roulette = $row['set_type_val'];
                        break;
                    case $str_mini_hilow: $db_w_mini_hilow = $row['set_type_val'];
                        break;
                    case $str_service_casino: $db_w_service_casino = $row['set_type_val'];
                        break;
                    case $str_service_slot: $db_w_service_slot = $row['set_type_val'];
                        break;
                    case $str_service_esports: $db_w_service_esports = $row['set_type_val'];
                        break;
                    case $str_service_kiron: $db_w_service_kiron = $row['set_type_val'];
                        break;
                    case $str_service_hash: $db_w_service_hash = $row['set_type_val'];
                        break;
                    case $str_service_holdem: $db_w_service_holdem = $row['set_type_val'];
                        break;
                }
            }
        }

        if (($p_data['ptype'] == 'w_config_all') || ($p_data['ptype'] == 'w_mini')) {
            $in_sub_sql = '';
            $in_sql = "insert into t_game_config ";
            $in_sql .= " (u_level, set_type, set_type_val, title) values ";

            $title = 'EOS 파워볼';
            if ($db_w_mini_eos_powerball == '-1') {
                $in_sub_sql .= " (0, '$str_mini_eos_powerball', '" . $p_data['w_mini_eos_pb'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_eos_pb'] != $db_w_mini_eos_powerball) {

                    $log_data = "$title  [" . $db_w_mini_eos_powerball . "] [" . $p_data['w_mini_eos_pb'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_eos_pb'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_eos_powerball' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '파워볼';
            if ($db_w_mini_powerball == '-1') {
                $in_sub_sql .= " (0, '$str_mini_powerball', '" . $p_data['w_mini_pb'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_pb'] != $db_w_mini_powerball) {

                    $log_data = "$title  [" . $db_w_mini_powerball . "] [" . $p_data['w_mini_pb'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_pb'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_powerball' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '가상축구';
            if ($db_w_mini_v_soccer == '-1') {
                $in_sub_sql .= " (0, '$str_mini_v_soccer', '" . $p_data['w_mini_v_soccer'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_v_soccer'] != $db_w_mini_v_soccer) {

                    $log_data = "$title  [" . $db_w_mini_v_soccer . "] [" . $p_data['w_mini_v_soccer'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_v_soccer'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_v_soccer' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '파워 사다리';
            if ($db_w_mini_p_ladder == '-1') {
                $in_sub_sql .= " (0, '$str_mini_power_ladder', '" . $p_data['w_mini_p_ladder'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_p_ladder'] != $db_w_mini_p_ladder) {

                    $log_data = "$title  [" . $db_w_mini_p_ladder . "] [" . $p_data['w_mini_p_ladder'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_p_ladder'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_power_ladder' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);


                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '바카라(해시)';
            if ($db_w_mini_baccarat == '-1') {
                $in_sub_sql .= " (0, '$str_mini_baccarat', '" . $p_data['w_mini_baccarat'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_baccarat'] != $db_w_mini_baccarat) {

                    $log_data = "$title  [" . $db_w_mini_baccarat . "] [" . $p_data['w_mini_baccarat'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_baccarat'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_baccarat' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '키노 사다리';
            if ($db_w_mini_k_ladder == '-1') {
                $in_sub_sql .= " (0, '$str_mini_kino_ladder', '" . $p_data['w_mini_k_ladder'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_k_ladder'] != $db_w_mini_k_ladder) {

                    $log_data = "$title  [" . $db_w_mini_k_ladder . "] [" . $p_data['w_mini_k_ladder'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_k_ladder'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_kino_ladder' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);


                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '룰렛(해시)';
            if ($db_w_mini_roulette == '-1') {
                $in_sub_sql .= " (0, '$str_mini_roulette', '" . $p_data['w_mini_roulette'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_roulette'] != $db_w_mini_roulette) {

                    $log_data = "$title  [" . $db_w_mini_roulette . "] [" . $p_data['w_mini_roulette'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_roulette'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_roulette' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '파워 프리킥';
            if ($db_w_mini_power_pk == '-1') {
                $in_sub_sql .= " (0, '$str_mini_power_pk', '" . $p_data['w_mini_power_pk'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_power_pk'] != $db_w_mini_power_pk) {

                    $log_data = "$title  [" . $db_w_mini_power_pk . "] [" . $p_data['w_mini_power_pk'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_power_pk'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_power_pk' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '하이로우(해시)';
            if ($db_w_mini_hilow == '-1') {
                $in_sub_sql .= " (0, '$str_mini_hilow', '" . $p_data['w_mini_hilow'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_mini_hilow'] != $db_w_mini_hilow) {

                    $log_data = "$title  [" . $db_w_mini_hilow . "] [" . $p_data['w_mini_hilow'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_mini_hilow'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_mini_hilow' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            if ($in_sub_sql != '') {

                $p_data['sql'] = $in_sql;
                $p_data['sql'] .= $in_sub_sql;

                $dbRet = $MEMAdminDAO->setQueryData($p_data);

                if ($dbRet) {

                    $log_data = "미니게임 사용 등록  [" . $p_data['w_bonus_f'] . "] [" . $p_data['w_odds_3'] . "] [" . $p_data['w_odds_5'] . "] [" . $p_data['w_odds_6'] . "] [" . $p_data['w_odds_7'] . "]";

                    $p_data['sql'] = "insert into  t_adm_log ";
                    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                    $MEMAdminDAO->setQueryData($p_data);
                }
            }

            $result["retCode"] = 1000;
            $result["retData"] = '';
        }


        if (($p_data['ptype'] == 'w_config_all') || ($p_data['ptype'] == 'w_site')) {
            $in_sub_sql = '';
            $in_sql = "insert into t_game_config ";
            $in_sql .= " (u_level, set_type, set_type_val, title) values ";

            $title = '사이트 점검';
            if ($db_w_service_site == '-1') {
                $in_sub_sql .= " (0, '$str_service_site', '" . $p_data['w_site_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_site_f'] != $db_w_service_site) {

                    $log_data = "$title  [" . $db_w_service_site . "] [" . $p_data['w_site_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_site_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_site' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '충전 점검';
            if ($db_w_service_charge == '-1') {
                $in_sub_sql .= " (0, '$str_service_charge', '" . $p_data['w_charge_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_charge_f'] != $db_w_service_charge) {

                    $log_data = "$title  [" . $db_w_service_charge . "] [" . $p_data['w_charge_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_charge_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_charge' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '환전 점검';
            if ($db_w_service_exchange == '-1') {
                $in_sub_sql .= " (0, '$str_service_exchange', '" . $p_data['w_exchange_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_exchange_f'] != $db_w_service_exchange) {

                    $log_data = "$title  [" . $db_w_service_exchange . "] [" . $p_data['w_exchange_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_exchange_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_exchange' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '게시판 점검';
            if ($db_w_service_board == '-1') {
                $in_sub_sql .= " (0, '$str_service_board', '" . $p_data['w_board_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_board_f'] != $db_w_service_board) {

                    $log_data = "$title  [" . $db_w_service_board . "] [" . $p_data['w_board_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_board_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_board' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '코인충전 점검';
            if ($db_w_service_coin_charge == '-1') {
                $in_sub_sql .= " (0, '$str_service_coin_charge', '" . $p_data['w_coin_charge_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_coin_charge_f'] != $db_w_service_coin_charge) {

                    $log_data = "$title  [" . $db_w_service_coin_charge . "] [" . $p_data['w_coin_charge_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_coin_charge_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_coin_charge' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '스포츠 점검';
            if ($db_w_service_sports == '-1') {
                $in_sub_sql .= " (0, '$str_service_sports', '" . $p_data['w_sports_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_sports_f'] != $db_w_service_sports) {

                    $log_data = "$title  [" . $db_w_service_sports . "] [" . $p_data['w_sports_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_sports_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_sports' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '클래식 점검';
            if ($db_w_service_class == '-1') {
                $in_sub_sql .= " (0, '$str_service_classic', '" . $p_data['w_classic_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_classic_f'] != $db_w_service_class) {

                    $log_data = "$title  [" . $db_w_service_class . "] [" . $p_data['w_classic_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_classic_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_classic' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '실시간 점검';
            if ($db_w_service_real == '-1') {
                $in_sub_sql .= " (0, '$str_service_real', '" . $p_data['w_real_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_real_f'] != $db_w_service_real) {

                    $log_data = "$title  [" . $db_w_service_real . "] [" . $p_data['w_real_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_real_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_real' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '카지노 점검';
            if ($db_w_service_casino == '-1') {
                $in_sub_sql .= " (0, '$str_service_casino', '" . $p_data['w_casino_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_casino_f'] != $db_w_service_casino) {

                    $log_data = "$title  [" . $db_w_service_casino . "] [" . $p_data['w_casino_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_casino_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_casino' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '슬롯 점검';
            if ($db_w_service_slot == '-1') {
                $in_sub_sql .= " (0, '$str_service_slot', '" . $p_data['w_slot_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_slot_f'] != $db_w_service_slot) {

                    $log_data = "$title  [" . $db_w_service_slot . "] [" . $p_data['w_slot_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_slot_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_slot' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '이스포츠 점검';
            if ($db_w_service_esports == '-1') {
                $in_sub_sql .= " (0, '$str_service_esports', '" . $p_data['w_esports_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_esports_f'] != $db_w_service_esports) {

                    $log_data = "$title  [" . $db_w_service_esports . "] [" . $p_data['w_esports_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_esports_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_esports' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '키론 점검';
            if ($db_w_service_kiron == '-1') {
                $in_sub_sql .= " (0, '$str_service_kiron', '" . $p_data['w_kiron_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_kiron_f'] != $db_w_service_kiron) {

                    $log_data = "$title  [" . $db_w_service_kiron . "] [" . $p_data['w_kiron_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_kiron_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_kiron' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '해쉬 점검';
            if ($db_w_service_hash == '-1') {
                $in_sub_sql .= " (0, '$str_service_hash', '" . $p_data['w_hash_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_hash_f'] != $db_w_service_hash) {

                    $log_data = "$title  [" . $db_w_service_hash . "] [" . $p_data['w_hash_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_hash_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_hash' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '홀덤 점검';
            if ($db_w_service_holdem == '-1') {
                $in_sub_sql .= " (0, '$str_service_holdem', '" . $p_data['w_holdem_f'] . "','" . $title . "') ";
            } else {
                if ($p_data['w_holdem_f'] != $db_w_service_holdem) {

                    $log_data = "$title  [" . $db_w_service_holdem . "] [" . $p_data['w_holdem_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_holdem_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_holdem' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            
            if ($in_sub_sql != '') {

                $p_data['sql'] = $in_sql;
                $p_data['sql'] .= $in_sub_sql;

                $dbRet = $MEMAdminDAO->setQueryData($p_data);

                if ($dbRet) {

                    $log_data = "점검여부 등록  [" . $p_data['w_bonus_f'] . "] [" . $p_data['w_odds_3'] . "] [" . $p_data['w_odds_5'] . "] [" . $p_data['w_odds_6'] . "] [" . $p_data['w_odds_7'] . "]";

                    $p_data['sql'] = "insert into  t_adm_log ";
                    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                    $MEMAdminDAO->setQueryData($p_data);
                }
            }

            $result["retCode"] = 1000;
            $result["retData"] = '';
        }

        if (($p_data['ptype'] == 'w_config_all') || ($p_data['ptype'] == 'w_bonus')) {

            $in_sub_sql = '';
            $in_sql = "insert into t_game_config ";
            $in_sql .= " (u_level, set_type, set_type_val, title) values ";

            $title = '보너스폴더 사용유무';
            if ($db_w_service_bonus_folder == '-1') {
                $in_sub_sql .= " (0, '$str_service_bonus_folder', '" . $p_data['w_bonus_f'] . "','" . $title . "') ";
            } else {
                // update
                if ($p_data['w_bonus_f'] != $db_w_service_bonus_folder) {

                    $log_data = "$title  [" . $db_w_service_bonus_folder . "] [" . $p_data['w_bonus_f'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_bonus_f'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_service_bonus_folder' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '3폴더 이상 보너스 배당';
            if ($db_w_odds_3_folder_bonus == '-1') {
                if ($in_sub_sql != '') {
                    $in_sub_sql .= ",";
                }

                $in_sub_sql .= " (0, '$str_odds_folder_3', '" . $p_data['w_odds_3'] . "','" . $title . "') ";
            } else {
                // update
                if ($p_data['w_odds_3'] != $db_w_odds_3_folder_bonus) {

                    $log_data = "$title  [" . $db_w_odds_3_folder_bonus . "] [" . $p_data['w_odds_3'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_odds_3'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_odds_folder_3' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }
            
            $title = '4폴더 이상 보너스 배당';
            if ($db_w_odds_4_folder_bonus == '-1') {
                if ($in_sub_sql != '') {
                    $in_sub_sql .= ",";
                }

                $in_sub_sql .= " (0, '$str_odds_folder_4', '" . $p_data['w_odds_4'] . "','" . $title . "') ";
            } else {
                // update
                if ($p_data['w_odds_4'] != $db_w_odds_4_folder_bonus) {

                    $log_data = "$title  [" . $db_w_odds_4_folder_bonus . "] [" . $p_data['w_odds_4'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_odds_4'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_odds_folder_4' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '5폴더 이상 보너스 배당';
            if ($db_w_odds_5_folder_bonus == '-1') {
                if ($in_sub_sql != '') {
                    $in_sub_sql .= ",";
                }
                $in_sub_sql .= " (0, '$str_odds_folder_5', '" . $p_data['w_odds_5'] . "','" . $title . "') ";
            } else {
                // update
                if ($p_data['w_odds_5'] != $db_w_odds_5_folder_bonus) {

                    $log_data = "$title  [" . $db_w_odds_5_folder_bonus . "] [" . $p_data['w_odds_5'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_odds_5'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_odds_folder_5' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '6폴더 이상 보너스 배당';
            if ($db_w_odds_6_folder_bonus == '-1') {
                if ($in_sub_sql != '') {
                    $in_sub_sql .= ",";
                }
                $in_sub_sql .= " (0, '$str_odds_folder_6', '" . $p_data['w_odds_6'] . "','" . $title . "') ";
            } else {
                // update
                if ($p_data['w_odds_6'] != $db_w_odds_6_folder_bonus) {

                    $log_data = "$title  [" . $db_w_odds_6_folder_bonus . "] [" . $p_data['w_odds_6'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_odds_6'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_odds_folder_6' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            $title = '7폴더 이상 보너스 배당';
            if ($db_w_odds_7_folder_bonus == '-1') {
                if ($in_sub_sql != '') {
                    $in_sub_sql .= ",";
                }
                $in_sub_sql .= " (0, '$str_odds_folder_7', '" . $p_data['w_odds_7'] . "','" . $title . "') ";
            } else {
                // update
                if ($p_data['w_odds_7'] != $db_w_odds_7_folder_bonus) {

                    $log_data = "$title  [" . $db_w_odds_7_folder_bonus . "] [" . $p_data['w_odds_7'] . "]";

                    $p_data['sql'] = " update t_game_config set set_type_val='" . $p_data['w_odds_7'] . "' ";
                    $p_data['sql'] .= " where set_type='$str_odds_folder_7' ";
                    $dbRet = $MEMAdminDAO->setQueryData($p_data);

                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                        $MEMAdminDAO->setQueryData($p_data);
                    }
                }
            }

            if ($in_sub_sql != '') {

                $p_data['sql'] = $in_sql;
                $p_data['sql'] .= $in_sub_sql;

                $dbRet = $MEMAdminDAO->setQueryData($p_data);

                if ($dbRet) {

                    $log_data = "보너스폴더 사용 등록  [" . $p_data['w_bonus_f'] . "] [" . $p_data['w_odds_3'] . "] [" . $p_data['w_odds_5'] . "] [" . $p_data['w_odds_6'] . "] [" . $p_data['w_odds_7'] . "]";

                    $p_data['sql'] = "insert into  t_adm_log ";
                    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', 7) ";

                    $MEMAdminDAO->setQueryData($p_data);
                }
            }

            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
    }

    $MEMAdminDAO->dbclose();
}

   //CommonUtil::logWrite('_set_config_game dividen result: '.json_encode($result), "error");
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
