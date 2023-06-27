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
        CommonUtil::logWrite("sports_menu_passivity_apply_ajax db_conn ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    //트랜잭션
    $is_trans_start = $LSportsAdminDAO->trans_start();
    if (false == $is_trans_start) {
        CommonUtil::logWrite("sports_menu_passivity_apply_ajax", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    if (false == isset($_POST['fixture_id']) || false == isset($_POST['markets_id']) || false == isset($_POST['bet_data']) || false == isset($_POST['bet_type'])) {

        $UTIL->logWrite("[sports_menu_passivity_apply_ajax] [error 2201]", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        return;
    }

    $fixture_id = $LSportsAdminDAO->real_escape_string($_POST['fixture_id']);
    $bet_type = $LSportsAdminDAO->real_escape_string($_POST['bet_type']);
    $markets_id = $LSportsAdminDAO->real_escape_string($_POST['markets_id']);
    $bet_base_line = $LSportsAdminDAO->real_escape_string($_POST['bet_base_line']);
    
    $bet_data = $_POST['bet_data'];

    $arr_bet_data = json_decode($bet_data, true);
   

    // 해당 정보를 업데이트 한다.
    foreach ($arr_bet_data as $bet) {
        $bet_price = $bet['price'];
        $status = $bet['status'];
        $name = $bet['name'];
        
        if(true === isset($bet_price) && false === empty($bet_price)){
            $p_data['sql'] = "update lsports_bet set bet_status_passivity = $status ,bet_price_passivity = $bet_price where fixture_id = $fixture_id and bet_type = $bet_type and markets_id = $markets_id";
        } else{
            $p_data['sql'] = "update lsports_bet set bet_status_passivity = $status where fixture_id = $fixture_id and bet_type = $bet_type and markets_id = $markets_id";
        }
        
        
        if (true === isset($bet_base_line) && false === empty($bet_base_line)) {
            $p_data['sql'] .= " and bet_base_line = '$bet_base_line'";
        }
        $p_data['sql'] .= " and bet_name = '$name' ";

        if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    }

    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '마켓 상태 배당 수동 변경 :  fixture_id =>' . $fixture_id . ' bet_type=>' . $bet_type . ' markets_id=>' . $markets_id
            . ' bet_base_line =>' . $bet_base_line.' bet_data => '.$bet_data ;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',55) ;";
    
    if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('fail sports_menu_passivity_apply_ajax mysqli_sql_exception ' . $e->getMessage(), "db_error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("fail sports_menu_passivity_apply_ajax Exception ", "error");

    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} catch (\ReflectionException $e) {
    CommonUtil::logWrite('fail sports_menu_passivity_apply_ajax ReflectionException : ' . $e, "error");

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


