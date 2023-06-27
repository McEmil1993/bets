<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$UTIL = new CommonUtil();

$a_id = $_SESSION['aid'];
try {
    $ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
    $db_conn = $ALBetDAO->dbconnect();
    
    if (!$db_conn) {
        CommonUtil::logWrite("[_set_config_reg_first] [error 2200]", "error");
        throw new mysqli_sql_exception('fail db connect');
    }

    $result['retCode'] = SUCCESS;
    $is_trans_start = false;
    
     $is_trans_start = $ALBetDAO->trans_start();
    if (false == $is_trans_start) {
       throw new mysqli_sql_exception('fail start tran !!!');
    }
    
    $con_reg_first = isset($_POST['con_reg_first']) ? $_POST['con_reg_first'] : 0;
    //$second_password = trim(isset($_POST['second_password']) ? $_POST['second_password'] : '');
    
    /*$sql = "select set_type_val from t_game_config where set_type='second_pass'";
    $result_second_pass = $ALBetDAO->getQueryData_pre($sql,[]);
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $result_second_pass = $result_second_pass[0];
    if (hash('sha512', $second_password) != $result_second_pass['set_type_val']) {
        throw new Exception('fail second_password');
    }*/
    
    $sql = "update t_game_config set set_type_val = ?  where set_type = 'reg_first_charge'";
    $ALBetDAO->setQueryData_pre($sql,[$con_reg_first]);
   
    $now_ip = CommonUtil::get_client_ip();
    $sql = "SELECT FN_GET_IP_COUNTRY(?) as a_country; ";
    $retData = $ALBetDAO->getQueryData_pre($sql,[$now_ip]);
    $st_country = $retData[0]['a_country'];
    
    $ac_code = 5;
    $coment = "가입 첫충  [" . $con_reg_first . "] 업데이트";
    
    $sql = "insert into t_adm_log ";
    $sql .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $sql .= " values(?,?,?,?,?) ";

    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData_pre($sql,[$a_id,$now_ip,$st_country,$coment,$ac_code])) {
        CommonUtil::logWrite("insertLog setQueryData ", "error");
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
}catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_set_config_reg_first to 2' . $e->getMessage(), "db_error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = $e->getMessage();
    
}catch (\Exception $e) {
    CommonUtil::logWrite("[_set_config_reg_first] [error -2]", "error");
       
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = $e->getMessage();
    
} finally {
    
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
            $ALBetDAO->commit();
        } else {
            $ALBetDAO->rollback();
        }
    }
    
    if ($db_conn) {
        $ALBetDAO->dbclose();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>