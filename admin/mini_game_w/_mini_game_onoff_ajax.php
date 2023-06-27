<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}
$UTIL = new CommonUtil();

$ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $ALBetDAO->dbconnect();
$result['retCode'] = -1;
$admin_id = $_SESSION['aid'];

if (!$db_conn) {
    $UTIL->logWrite("[_msg_set_prc] [error 2200]", "error");
    //$UTIL->checkFailType('2200', '', '', 'json');
    return;
}

if (!$ALBetDAO->trans_start()) {
    // start transaction error
    $UTIL->logWrite("[_msg_set_prc] [error -10]", "error");
    $ALBetDAO->dbclose();
    //$UTIL->checkFailType('-10', '', '', 'json');
    return;
}
try {

    $id = $_POST['id'];
    $bet_type = $_POST['bet_type'];
    $result['retCode'] = '1000';
    $status = 'ON';

    if ($_POST['cmd'] == 'bet_OFF') {
        $status = 'OFF';
    }
    
    $p_data['sql'] = "UPDATE mini_game SET admin_bet_status = '$status' WHERE id = $id AND bet_type = $bet_type";
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $log_data = "[미니게임 ON/OFF] bet_type=>$bet_type  id=>$id bet_status=".$status;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$log_data',33) ;";
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $ALBetDAO->commit();
} catch (\mysqli_sql_exception $e) {
    $UTIL->logWrite('mini_game_onoff_ajax [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "error");
    $ALBetDAO->rollback();
    $result['retCode'] =  FAIL_DB_SQL_EXCEPTION;
    $result['retMsg']  =  FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("mini_game_onoff_ajax e : " . $e->getMessage(), "error");
    $ALBetDAO->rollback();
    $result['retCode'] = -3;
    $result['retMsg'] = 'Exception 예외발생';
} catch (\ReflectionException $e) {
    $UTIL->logWrite('::::::::::::::: mini_game_onoff_ajax ReflectionException : ' . $e, "error");
    $ALBetDAO->rollback();
    $result['retCode'] = -4;
    $result['retMsg'] = 'ReflectionException 예외발생';
} finally {
    $ALBetDAO->dbclose();
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

?>
