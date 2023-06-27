<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

include_once(_LIBPATH . '/class_Code.php');


try {
    if (!isset($_SESSION)) {
        session_start();
    }

    $UTIL = new CommonUtil();

    $result['retCode'] = SUCCESS;
    $is_trans_start = false;
    $LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
    $db_conn = $LSportsAdminDAO->dbconnect();

    if (!$db_conn) {
        CommonUtil::logWrite("_sports_passivity_modify_game_status_release db_conn ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    //트랜잭션
    $is_trans_start = $LSportsAdminDAO->trans_start();
    if (false == $is_trans_start) {
        CommonUtil::logWrite("_sports_passivity_modify_game_status_release", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    if (false === isset($_POST['fixture_id']) || false === isset($_POST['bet_type'])) {
        $UTIL->logWrite("fail _sports_passivity_modify_game_status_release param ", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        return;
    }
    
    $fixture_id = $LSportsAdminDAO->real_escape_string($_POST['fixture_id']);
    $bet_type = $LSportsAdminDAO->real_escape_string($_POST['bet_type']);
       
    
    $p_data['sql'] = " UPDATE lsports_fixtures SET display_status_passivity = default WHERE fixture_id = $fixture_id AND bet_type = $bet_type";
    
    if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
 
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '경기 상태값 수동 변경 :  fixture_id =>' . $fixture_id . ' bet_type=>' . $bet_type; 
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $log_type = PASSIVITY_MODIFY_STATUS_RELEASE;
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',$log_type) ;";
    //$LSportsAdminDAO->setQueryData($p_data);
    
    if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('fail _sports_passivity_modify_game_status_release mysqli_sql_exception ' . $e->getMessage(), "db_error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("fail _sports_passivity_modify_game_status_release Exception ", "error");

    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} catch (\ReflectionException $e) {
    CommonUtil::logWrite('fail _sports_passivity_modify_game_status_release ReflectionException : ' . $e, "error");

    $result['retCode'] = FAIL_REFLECTION_EXCEPTION;
    $result['retMsg'] = FAIL_REFLECTION_EXCEPTION_MSG;
} finally {
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
            $LSportsAdminDAO->commit();
        } else {
            $LSportsAdminDAO->rollback();
        }
    }

    if ($db_conn) {
        $LSportsAdminDAO->dbclose();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}


