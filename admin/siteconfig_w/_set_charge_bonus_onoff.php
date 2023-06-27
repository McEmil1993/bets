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
    $UTIL->logWrite("[_msg_set_prc] [error 2200]", "error");
    return;
}

try {
    $result['retCode'] = '1000';
    $status = 'ON';

    if ($_POST['status'] == 'OFF') {
        $status = 'OFF';
    }
    
    $type = $_POST['type'];
    $name = '';
    
    if(1 == $type){
        $sql = "UPDATE tb_static_bonus SET flag = '$status' WHERE idx = 1";
        $name = '충전 기본 보너스';
    }else if(2 == $type){
        $sql = "UPDATE tb_static_bonus SET bonus_1_flag = '$status' WHERE idx = 1";
        $name = '충전 보너스1';
    }else if(3 == $type){
        $sql = "UPDATE tb_static_bonus SET bonus_2_flag = '$status' WHERE idx = 1";
        $name = '충전 보너스2';
    }else if(4 == $type){
        $sql = "UPDATE tb_static_bonus SET bonus_3_flag = '$status' WHERE idx = 1";
        $name = '충전 보너스3';
    }else if(5 == $type){
        $sql = "UPDATE tb_static_bonus SET bonus_4_flag = '$status' WHERE idx = 1";
        $name = '충전 보너스4';
    }else{
        $sql = "UPDATE tb_static_bonus SET bonus_5_flag = '$status' WHERE idx = 1";
        $name = '충전 보너스5';
    }
    
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->executeQuery($sql)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = $name.' 온오프 상태 => ' . $status;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data', ".CHARGE_BONUS_ONOFF.") ;";
    
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_set_charge_bonus_onoff' . $e->getMessage(), "db_error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
    
}catch (\Exception $e) {
    $UTIL->logWrite("[_set_charge_bonus_onoff] [error -2]", "error");
    $result['retCode'] = -3;
    $result['retMsg'] = 'Exception 예외발생';
    
} catch (\ReflectionException $e) {
    CommonUtil::logWrite('::::::::::::::: _set_charge_bonus_onoff to 2 ReflectionException : ' . $e, "error");
    $result['retCode'] = -4;
    $result['retMsg'] = 'Exception 예외발생';
    
} finally {
    $ALBetDAO->dbclose();
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>