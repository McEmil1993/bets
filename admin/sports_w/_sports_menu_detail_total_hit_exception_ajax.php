<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
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
        $UTIL->logWrite("[_sports_menu_detail_total_hit_exception_ajax] [error 2200]", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    $is_trans_start = $ALBetDAO->trans_start();
    if (false == $is_trans_start) {
        // start transaction error
        $UTIL->logWrite("[_sports_menu_detail_total_hit_exception_ajax] [error -10]", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    if (false == isset($_POST['fixture_id']) || false == isset($_POST['fixture_start_date']) || false == isset($_POST['bet_type']) || false == isset($_POST['second_pass'])) {

        $UTIL->logWrite("[_sports_menu_detail_total_hit_exception_ajax] [error 2201]", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        throw new Exception('param error!!!');
    }

    $fixture_start_date =  $ALBetDAO->real_escape_string($_POST['fixture_start_date']);
    $fixture_id =  $ALBetDAO->real_escape_string($_POST['fixture_id']);
    $bet_type =  $ALBetDAO->real_escape_string($_POST['bet_type']);
    $markets_name =  $ALBetDAO->real_escape_string($_POST['markets_name']);
    $sport_id =  $ALBetDAO->real_escape_string($_POST['sport_id']);
    $second_pass =  $ALBetDAO->real_escape_string($_POST['second_pass']);
    
    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    $result_second_pass = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $result_second_pass = $result_second_pass[0];
    
    if (hash('sha512', $second_pass) != $result_second_pass['set_type_val']) {
        $UTIL->logWrite("[!!!! second_pass _sports_menu_detail_batch_application_ajax] [error -1]", "error");
   
        $result['retCode'] = FAIL_SECOND_PASS;
        $result['retMsg']  = FAIL_SECOND_PASS_MSG;
        throw new Exception('password error!!!');
    }
    
    $market_id = 0;
    if (true == isset($markets_name) && false == empty($markets_name) && true == isset($sport_id) && false == empty($sport_id)) {
        $market_id = $ALBetDAO->getMarket_id($sport_id, $markets_name, $bet_type);
    }

    // 이미 정산된 데이터를 롤백한다.

    $arrReResult = $ALBetDAO->getBetTotalReCalculate($fixture_id, $bet_type, $market_id);
    if (FAIL_DB_SQL_EXCEPTION === $arrReResult) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    if (true == isset($arrReResult) && count($arrReResult)) {
        foreach ($arrReResult as $re_value) {
            // detail 정보 bet_status 값 1,result_score = null 
            // mb_bet bet_status = 1 ,take_money = 0,take_point = 0,recom_take_point = 0
            // 낙첨,적중에 따라 금액 롤백 

            $re_value['admin_id'] = $_SESSION['aid'];
            GameCode::doRollbackCalculate($ALBetDAO, $UTIL, $re_value, 6);
        }
    }

    if (0 < count($arrReResult)) {
        GameCode::doReCalculateAndAllHit($arrReResult, $ALBetDAO, $UTIL, $bet_type, $re_value['admin_id']);
    }

    //$ALBetDAO->UpdateLsportsBetException($fixture_start_date, $fixture_id, $bet_type, $market_id, '', 1);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateLsportsBetException($fixture_id, $bet_type, $market_id, '', 1)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '전체수동적특 : time=>' . $fixture_start_date . ' fixture=>' . $fixture_id . ' bet_type=>' . $bet_type . ' markets_name=>' . $markets_name . ' market_id=>' . $market_id;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',22) ;";

    //$ALBetDAO->setQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    //$ALBetDAO->commit();
} catch (\mysqli_sql_exception $e) {

    CommonUtil::logWrite('_sports_menu_detail_total_hit_exception_ajax to 2' . $e->getMessage(), "db_error");
    //$ALBetDAO->rollback();
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {

    $UTIL->logWrite("[_sports_menu_detail_total_hit_exception_ajax] [error -2]", "error");
    //$ALBetDAO->rollback();
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} catch (\ReflectionException $e) {

    CommonUtil::logWrite('::::::::::::::: _sports_menu_detail_total_hit_exception_ajax to 2 ReflectionException : ' . $e, "error");
    //$ALBetDAO->rollback();
    $result['retCode'] = FAIL_REFLECTION_EXCEPTION;
    $result['retMsg'] = FAIL_REFLECTION_EXCEPTION_MSG;
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