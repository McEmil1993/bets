<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

try {
    if (!isset($_SESSION)) {
        session_start();
    }

    $UTIL = new CommonUtil();

    $ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
    $db_conn = $ALBetDAO->dbconnect();
    $result['retCode'] = SUCCESS;

    if (!$db_conn) {
        $UTIL->logWrite("[_sports_bet_menu_ajax] [error 2200]", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }
   
    if (false === isset($_POST['fixture_id']) || false === isset($_POST['bet_type']) || false === isset($_POST['markets_id']) || false === isset($_POST['fixture_start_date']) 
            || false === isset($_POST['second_pass'])) {
        $UTIL->logWrite("[_sports_bet_menu_ajax] [error -2]", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        return;
    }
    
    $fixture_id = $ALBetDAO->real_escape_string($_POST['fixture_id']);
    $bet_type = $ALBetDAO->real_escape_string($_POST['bet_type']);
    $markets_id = $ALBetDAO->real_escape_string($_POST['markets_id']);
    $bet_base_line = $ALBetDAO->real_escape_string($_POST['bet_base_line']);
    $fixture_start_date = $ALBetDAO->real_escape_string($_POST['fixture_start_date']);
    $second_pass = $ALBetDAO->real_escape_string($_POST['second_pass']);

    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    //$result_second_pass = $ALBetDAO->getQueryData($p_data)[0];
    $result_second_pass = $ALBetDAO->getQueryData($p_data);
    
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    if (hash('sha512', $second_pass) != $result_second_pass[0]['set_type_val']) {
        $UTIL->logWrite("[!!!! error second_pass _sports_menu_detail_total_re_calculate_ajax] ", "error");
        $result['retCode'] = FAIL_SECOND_PASS;
        $result['retMsg'] = FAIL_SECOND_PASS_MSG;
        return;
    }
    
    $status = 'ON';
    $bet_price_hit = 0;
    $result['retCode'] = '1000';
    if ($_POST['cmd'] == 'bet_off') {
        $bet_price_hit = 1;
        $status = 'OFF';
    }

    if(FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateLsportsBetByDetailInfo($fixture_id, $bet_type, $markets_id, $bet_base_line, $status)){
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '개별마켓ON/OFF : time=>' . $fixture_start_date . ' fixture=>' . $fixture_id . ' bet_type=>' . $bet_type . ' bet_base_line=>' . $bet_base_line . ' market_id=>' . $markets_id . ' status=>' . $status;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',24) ;";

    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_sports_bet_menu_ajax to 2' . $e->getMessage(), "db_error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("[_sports_bet_menu_ajax] [error -2]", "error");
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {
    if ($db_conn) {
        $ALBetDAO->dbclose();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>