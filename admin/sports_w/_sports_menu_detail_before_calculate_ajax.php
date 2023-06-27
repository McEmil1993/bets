<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_LIBPATH . '/class_Code.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

try {
    if (!isset($_SESSION)) {
        session_start();
    }

    $UTIL = new CommonUtil();

    $result['retCode'] = SUCCESS;
    $is_trans_start = false;

    $ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
    $db_conn = $ALBetDAO->dbconnect();

    if (!$db_conn) {
        $UTIL->logWrite("fail connect sports_menu_detail_before_calculate_ajax ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    $is_trans_start = $ALBetDAO->trans_start();
    if (false == $is_trans_start) {
        // start transaction error
        $UTIL->logWrite("fail trans_start sports_menu_detail_before_calculate_ajax", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    if (false == isset($_POST['fixture_id']) || false == isset($_POST['markets_id']) || false == $_POST['second_pass']) {
        $UTIL->logWrite("fail param sports_menu_detail_before_calculate_ajax", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        throw new Exception('param error!!!');
    }

    $fixture_id = $ALBetDAO->real_escape_string($_POST['fixture_id']);
    $bet_type = $ALBetDAO->real_escape_string($_POST['bet_type']);
    $markets_id = $ALBetDAO->real_escape_string($_POST['markets_id']);
    $bet_base_line = $ALBetDAO->real_escape_string($_POST['bet_base_line']);
    $second_pass = $ALBetDAO->real_escape_string($_POST['second_pass']);

    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    $result_second_pass = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $result_second_pass = $result_second_pass[0];
    
    if (hash('sha512', $second_pass) != $result_second_pass['set_type_val']) {
        $UTIL->logWrite("[!!!! fail second_pass sports_menu_detail_before_calculate_ajax ", "error");
        $result['retCode'] = FAIL_SECOND_PASS;
        $result['retMsg'] = FAIL_SECOND_PASS_MSG;
        throw new Exception('password error!!!');
    }

    // 이미 정산된 데이터를 롤백한다.
    if (FAIL_DB_SQL_EXCEPTION ===  $ALBetDAO->UpdateLsportsBetByDetailInfo($fixture_id, $bet_type, $markets_id, $bet_base_line, 'OFF')) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $arrReResult = $ALBetDAO->getBetIndividualReCalculate($fixture_id, $bet_type, $markets_id, $bet_base_line);
    if (FAIL_DB_SQL_EXCEPTION === $arrReResult) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    if (true == isset($arrReResult) && count($arrReResult)) {
        foreach ($arrReResult as $re_value) {
            // detail 정보 bet_status 값 1,result_score = null 
            // mb_bet bet_status = 1 ,take_money = 0,take_point = 0,recom_take_point = 0
            // 낙첨,적중에 따라 금액 롤백 
            $re_value['admin_id'] = $_SESSION['aid'];
            GameCode::doRollbackCalculate($ALBetDAO, $UTIL, $re_value, 1);
        }
    }
   
    // 개별 마감 전
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '개별마감 전 : => fixture=>' . $fixture_id . ' bet_type=>' . $bet_type . ' markets_id=>' . $markets_id .
            ' bet_base_line=>' . $bet_base_line;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',".INDIVIDUAL_BF_CALC.") ;";
    
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
} catch (\mysqli_sql_exception $e) {
    $UTIL->logWrite('sports_menu_detail_before_calculate_ajax [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("sports_menu_detail_before_calculate_ajax e : " . $e->getMessage(), "error");

    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
            $ALBetDAO->commit();
            $UTIL->logWrite('::::::::::::::: sports_menu_detail_before_calculate_ajax commit : ', "error");
        } else {
            $ALBetDAO->rollback();
            $UTIL->logWrite('::::::::::::::: sports_menu_detail_before_calculate_ajax rollback : ', "error");
        }
    }

    if ($db_conn) {
        $ALBetDAO->dbclose();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>