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
include_once(_BASEPATH.'/common/auth_check.php');
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
    
    $type = $_POST['type'];
    $desc = $_POST['desc'];
    $bonus_1_desc = $_POST['bonus_1_desc'];
    $bonus_2_desc = $_POST['bonus_2_desc'];
    $bonus_3_desc = $_POST['bonus_3_desc'];
    $bonus_4_desc = $_POST['bonus_4_desc'];
    $bonus_5_desc = $_POST['bonus_5_desc'];
    $name = '';
    
    $arr_param = array();
    if(1 == $type){
        $sql = "UPDATE tb_static_bonus SET `desc` = ? WHERE idx = 1";
        $name = '충전 기본 보너스 문구';
        array_push($arr_param, $desc);
    } else if(2 == $type) {
        $sql = "UPDATE tb_static_bonus SET bonus_1_desc = ?, bonus_2_desc = ? WHERE idx = 1";
        $name = '충전 보너스2 문구';
        array_push($arr_param, $bonus_1_desc);
        array_push($arr_param, $bonus_2_desc);
    } else {
        $sql = "UPDATE tb_static_bonus SET bonus_3_desc = ?, bonus_4_desc = ?, bonus_5_desc = ? WHERE idx = 1";
        $name = '충전 보너스3 문구';
        array_push($arr_param, $bonus_3_desc);
        array_push($arr_param, $bonus_4_desc);
        array_push($arr_param, $bonus_5_desc);
    }
    
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData_pre($sql, $arr_param)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = $name.' 변경 type => ' . $type.' 변경 desc => '.$desc.' 변경 bonus_1_desc => '.$bonus_1_desc.' 변경 bonus_2_desc => '.$bonus_2_desc
                .' 변경 bonus_3_desc => '.$bonus_3_desc.' 변경 bonus_4_desc => '.$bonus_4_desc.' 변경 bonus_5_desc => '.$bonus_5_desc;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY(?) as a_country; ";
    $retData = $ALBetDAO->getQueryData_pre($p_data['sql'],[$now_ip]);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values(?,?,?,?,".CHARGE_BONUS_DESC_CHANGE.") ;";
    
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData_pre($p_data['sql'],[$admin_id, $now_ip, $st_country, $log_data])) {
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