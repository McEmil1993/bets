<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

if (!isset($_SESSION)) {
    session_start();
}

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

$UTIL = new CommonUtil();

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

$ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $ALBetDAO->dbconnect();
$result['retCode'] = -1;

if (!$db_conn) {
    $UTIL->logWrite("[_sports_inplay_menu_ajax] [error 2200]", "error");
    //$UTIL->checkFailType('2200', '', '', 'json');
    return;
}


try {
  
    $result['retCode'] = '1000';
    $status = 'ON';

    if ($_POST['cmd'] == 'bet_off') {
        $status = 'OFF';
    }
    
    $ALBetDAO->updateInplayAdminFixStatus($status);

    //$ALBetDAO->commit();
}catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_sports_inplay_menu_ajax to 2' . $e->getMessage(), "db_error");
    //$ALBetDAO->rollback();
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
    
}catch (\Exception $e) {
    $UTIL->logWrite("[_sports_inplay_menu_ajax] [error -2]", "error");
    //$ALBetDAO->rollback();
       
    $result['retCode'] = -3;
    $result['retMsg'] = 'Exception 예외발생';
    
} catch (\ReflectionException $e) {
    CommonUtil::logWrite('::::::::::::::: _sports_inplay_menu_ajax to 2 ReflectionException : ' . $e, "error");
    //$ALBetDAO->rollback();
    $result['retCode'] = -4;
    $result['retMsg'] = 'Exception 예외발생';
} finally {
    $ALBetDAO->dbclose();
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>