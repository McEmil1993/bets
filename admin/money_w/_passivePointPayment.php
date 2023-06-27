<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
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
        CommonUtil::logWrite("[_passivePointPayment] [error 2200]", "error");
        throw new mysqli_sql_exception('fail db connect');
    }

    $result['retCode'] = SUCCESS;
    $is_trans_start = false;
    
     $is_trans_start = $ALBetDAO->trans_start();
    if (false == $is_trans_start) {
       throw new mysqli_sql_exception('fail start tran !!!');
    }
    
    $idx =   isset($_POST['idx']) ? $_POST['idx'] : 0;
    $point = isset($_POST['point']) ? $_POST['point'] : 0;
    $second_password = trim(isset($_POST['second_password']) ? $_POST['second_password'] : '');
    
    $sql = "select set_type_val from t_game_config where set_type='second_pass'";
    $result_second_pass = $ALBetDAO->getQueryData_pre($sql,[]);
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $result_second_pass = $result_second_pass[0];
    if (hash('sha512', $second_password) != $result_second_pass['set_type_val']) {
        throw new Exception('fail second_password');
    }
    
    if(0 >= $point){
        throw new Exception('minus point error');
    }

    $sql = "select member_idx, u_key from member_money_charge_history where idx = ?";
    $result_charge = $ALBetDAO->getQueryData_pre($sql,[$idx]);
    if (FAIL_DB_SQL_EXCEPTION === $result_charge) {
        throw new mysqli_sql_exception('mysqli_sql_exception charge !!!');
    }
    
    // 유저포인트
    $member_idx = $result_charge[0]['member_idx'];
    $sql = "select point from member where idx=?";
    $result_member = $ALBetDAO->getQueryData_pre($sql,[$member_idx]);
    if (FAIL_DB_SQL_EXCEPTION === $result_member) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $sql = "update member set point = point + ?  where idx = ?";
    $ALBetDAO->setQueryData_pre($sql,[$point,$member_idx]);
   
    $sql = "update member_money_charge_history set manual_bonus_point = manual_bonus_point + ?  where idx = ?";
    $ALBetDAO->setQueryData_pre($sql,[$point,$idx]);
    $ukey = $result_charge[0]['u_key'];
    $ac_code = AC_GM_ADD_MANAUAL_POINT;
    $be_point = $result_member[0]['point'];
    $af_point = $result_member[0]['point'] + $point;
    $coment = '포인트 수동지급';
    
    $sql = "insert into t_log_cash ";
    $sql .= " (u_key,member_idx, ac_code,ac_idx,r_money, be_r_money, af_r_money, m_kind, coment,point,be_point,af_point,g_money, a_id) "
                   . "values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData_pre($sql,[$ukey,$member_idx,$ac_code,$idx,0,0,0,'P',$coment,$point,$be_point,$af_point,0,$a_id])) {
        CommonUtil::logWrite("insertLog setQueryData ", "error");
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
}catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_passivePointPayment to 2' . $e->getMessage(), "db_error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = $e->getMessage();
    
}catch (\Exception $e) {
    CommonUtil::logWrite("[_passivePointPayment] [error -2]", "error");
       
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