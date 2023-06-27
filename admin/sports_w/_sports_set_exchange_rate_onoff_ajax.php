<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

$UTIL = new CommonUtil();

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $ALBetDAO->dbconnect();
$result['retCode'] = -1;

if (!$db_conn) {
    $UTIL->logWrite("[_sports_set_exchange_rate_onoff_ajax] [error 2200]", "error");
  
    return;
}

try {
    $type = $_POST['type'];
    $id = $_POST['id'];
    $bet_type = $_POST['bet_type'];
    $is_margin_refund = $_POST['is_margin_refund'];
    $sports_id = isset($_POST['sports_id']) ? $_POST['sports_id'] : 0;
    $league_id = isset($_POST['league_id']) ? $_POST['league_id'] : 0;
    
    $type = $ALBetDAO->real_escape_string($type);
    $id  = $ALBetDAO->real_escape_string($id);
    $bet_type = $ALBetDAO->real_escape_string($bet_type);
    $is_margin_refund = $ALBetDAO->real_escape_string($is_margin_refund);
    $sports_id = $ALBetDAO->real_escape_string($sports_id);
    $league_id = $ALBetDAO->real_escape_string($league_id);
     
    $result['retCode'] = '1000';

    if($type == 1){
        $sql = "UPDATE lsports_sports SET is_margin_refund = $is_margin_refund WHERE id = $id AND bet_type = $bet_type";
    }else if($type == 2){
        $sql = "UPDATE lsports_leagues SET is_margin_refund = $is_margin_refund WHERE id = $id AND bet_type = $bet_type";
    }else{
                
        $sql = "INSERT INTO lsports_refund_rate_market(sports_id, league_id, market_id, bet_type, is_margin_refund) "
                . "VALUES ($sports_id, $league_id, $id, $bet_type, $is_margin_refund) ON DUPLICATE KEY UPDATE is_margin_refund = $is_margin_refund";
    }
            
    //$ALBetDAO->executeQuery($sql);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->executeQuery($sql)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = "환수율 설정 마진 ON/OFF id=>$id type=>$type bet_type=>$bet_type is_margin_refund=>$is_margin_refund";
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',31) ;";
    
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_sports_set_exchange_rate_onoff_ajax to 2' . $e->getMessage(), "db_error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
    
} catch (\Exception $e) {
    $UTIL->logWrite("[_sports_set_exchange_rate_onoff_ajax] [error -2]", "error");
    $result['retCode'] = -3;
    $result['retMsg'] = 'Exception 예외발생';
    
} catch (\ReflectionException $e) {
    CommonUtil::logWrite('::::::::::::::: _sports_set_exchange_rate_onoff_ajax to 2 ReflectionException : ' . $e, "error");
    $result['retCode'] = -4;
    $result['retMsg'] = 'Exception 예외발생';
    
} finally {
    $ALBetDAO->dbclose();
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>