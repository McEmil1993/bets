<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
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
        CommonUtil::logWrite("multi_leave_proc db_conn ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    //트랜잭션
    $is_trans_start = $LSportsAdminDAO->trans_start();
    if (false == $is_trans_start) {
        CommonUtil::logWrite("multi_leave_proc", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    if (false == isset($_POST['leave_users']) || false == isset($_POST['second_password'])) {
        $UTIL->logWrite("[multi_leave_proc] [error 2201]", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        throw new Exception('param error!!!');
    }

    $second_password = $_POST['second_password'];
    $leave_users = explode(',', $_POST['leave_users']);
    //$leave_users = $_POST['leave_users'];

    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    $result_second_pass = $LSportsAdminDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $result_second_pass = $result_second_pass[0];
    if (hash('sha512', $second_password) != $result_second_pass['set_type_val']) {
        $UTIL->logWrite("[!!!! second_pass multi_leave_proc] [error -1]", "error");
        $result['retCode'] = FAIL_SECOND_PASS;
        $result['retMsg'] = FAIL_SECOND_PASS_MSG;
        throw new Exception('password error!!!');
    }

    $admin_id = $_SESSION['aid'];
    $now_ip = CommonUtil::get_client_ip();
    $log_data = "유저일괄탈퇴처리";
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    $log_type = USER_LEAVE;
    foreach ($leave_users as $member_idx) {
        $p_data['sql'] = "update member set status = 3, leave_time = now() where idx = ? ";
        if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData_pre($p_data['sql'], [$member_idx])) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $log_data = 'leave user member_idx : ' . $member_idx;

        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
        $p_data['sql'] .= " values(?,?,?,?,?) ;";

        if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData_pre($p_data['sql'], [$admin_id, $now_ip, $st_country, $log_data, $log_type])) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    }
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('fail _multi_leave_proc mysqli_sql_exception ' . $e->getMessage(), "db_error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    CommonUtil::logWrite("fail _multi_leave_proc Exception ", "error");
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
            $LSportsAdminDAO->commit();
        } else {
            $LSportsAdminDAO->rollback();
        }
    }

    //if ($db_conn) {
    //    $LSportsAdminDAO->dbclose();
    //}
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}


