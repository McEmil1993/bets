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

    $result["retCode"] = SUCCESS;
    $is_trans_start = false;
    $LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
    $db_conn = $LSportsAdminDAO->dbconnect();

    if (!$db_conn) {
        CommonUtil::logWrite("_sports_passivity_flag_update_all fail db_conn ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    //트랜잭션
    $is_trans_start = $LSportsAdminDAO->trans_start();
    if (false == $is_trans_start) {
        CommonUtil::logWrite("_sports_passivity_flag_update_all", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    $strFixturesData = $_POST['strFixturesData'];
    //CommonUtil::logWrite("_sports_passivity_flag_update_all arrFixturesData ==> ".$strFixturesData, "error");
    $flag = $LSportsAdminDAO->real_escape_string($_POST['flag']);
    //CommonUtil::logWrite("_sports_passivity_flag_update_all flag ==> ".$flag, "error");
    $bet_type = $LSportsAdminDAO->real_escape_string($_POST['bet_type']);
    //CommonUtil::logWrite("_sports_passivity_flag_update_all bet_type ==> ".$bet_type, "error");
    $arrFixturesData = json_decode($strFixturesData, true);


    foreach ($arrFixturesData as $key => $fix_id) {

        CommonUtil::logWrite("_sports_passivity_flag_update_all fix_id ==> " . $fix_id, "error");
        // 정산완료로 돌릴때
        $p_data['sql'] = " UPDATE lsports_fixtures SET passivity_flag = '$flag' WHERE fixture_id = $fix_id AND bet_type = $bet_type";
        if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        CommonUtil::logWrite("_sports_passivity_flag_update_all sql ==> " . $p_data['sql'], "error");

        $p_data['sql'] = " UPDATE lsports_bet SET passivity_flag = '$flag' WHERE fixture_id = $fix_id AND bet_type = $bet_type";
        if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    }

    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '경기 수동관리 변경 :  fixture=>' . $strFixturesData . ' bet_type=>' . $bet_type . ' flag=>' . $flag;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',62) ;";
    if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('fail _sports_passivity_modify_game_start_date mysqli_sql_exception ' . $e->getMessage(), "error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} finally {
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
            CommonUtil::logWrite("_sports_passivity_flag_update_all commit ==> " . $result['retCode'], "error");
            $LSportsAdminDAO->commit();
        } else {
            CommonUtil::logWrite("_sports_passivity_flag_update_all rollback ==> " . $result['retCode'], "error");
            $LSportsAdminDAO->rollback();
        }
    }

    if ($db_conn) {
        CommonUtil::logWrite("_sports_passivity_flag_update_all dbclose ==> ", "error");
        $LSportsAdminDAO->dbclose();
    }

    CommonUtil::logWrite("_sports_passivity_flag_update_all end ==> " . json_encode($result), "error");
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}


