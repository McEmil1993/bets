<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

$UTIL = new CommonUtil();

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $ALBetDAO->dbconnect();
$result['retCode'] = 1000;

if (!$db_conn) {
    $result['retCode'] = 2200;
    $UTIL->logWrite("[_msg_set_prc] [error 2200]", "error");
    $UTIL->checkFailType('2200', '', '', 'json');
    return;
}

/*if (!$ALBetDAO->trans_start()) {
    // start transaction error
    $UTIL->logWrite("[_msg_set_prc] [error -10]", "error");
    $ALBetDAO->dbclose();
    $result['retCode'] = -10;
    $UTIL->checkFailType('-10', '', '', 'json');
    return;
}*/
try {
    $bonus_option = $_POST['bonus_option'];
    $txtval = $_POST['txtval'];
    
    $sql = "UPDATE t_game_config SET set_type_val = '$txtval' WHERE set_type = '$bonus_option'";
    $ALBetDAO->executeQuery($sql);
    
    // 어드민 히스토리 로그
   
    //$ALBetDAO->commit();
} catch (\Exception $e) {
    $UTIL->logWrite("[_msg_set_prc] [error -2]", "error");
    //$ALBetDAO->rollback();
    $ALBetDAO->dbclose();
    $result['retCode'] = -2;
    $UTIL->checkFailType('-2', '', '', 'json');
    return;
}

$ALBetDAO->dbclose();

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
