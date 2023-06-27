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

try {

    if (!isset($_SESSION)) {
        session_start();
    }

    $UTIL = new CommonUtil();

    $ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
    $db_conn = $ALBetDAO->dbconnect();
    $result['retCode'] = SUCCESS;
    $is_trans_start = false;
// 1, 2, 3, 7, 52, 101, 102 => 승무패,오버언더,핸디캡,더블찬스,승패,홈팀 오버언더,원정팀 오버언더

    if (!$db_conn) {
        $UTIL->logWrite("[_sports_menu_detail_batch_application_ajax] [error 2200]", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }
  
    $is_trans_start = $ALBetDAO->trans_start();
    if (false == $is_trans_start) {
        // start transaction error
        $UTIL->logWrite("[_sports_menu_detail_batch_application_ajax] [error -10]", "error");
        //$ALBetDAO->dbclose();
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg']  = FAIL_TRANS_START_MSG;
        return;
    }

    $fixture_start_date = $_POST['fixture_start_date'];
    $fixture_id = $_POST['fixture_id'];
    $markets_name = $_POST['markets_name'];
    $sport_id = $_POST['sport_id'];
    $second_pass = $_POST['second_pass'];

    $bet_type = $_POST['bet_type'];
    $live_results_p1 = $_POST['live_results_p1'];
    $live_results_p2 = $_POST['live_results_p2'];
    if (null == $second_pass || null == $markets_name || '' == $markets_name || null == $live_results_p1 || '' == $live_results_p1 || null == $live_results_p2 || '' == $live_results_p2 || false === isset($sport_id) || true === empty($sport_id)) {
        $UTIL->logWrite("[_sports_menu_detail_batch_application_ajax] [error -2]", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg']  = FAIL_PARAM_MSG;
        throw new Exception('param error!!!');
    }

    $fixture_start_date = $ALBetDAO->real_escape_string($fixture_start_date);
    $fixture_id = $ALBetDAO->real_escape_string($fixture_id);
    $markets_name = $ALBetDAO->real_escape_string($markets_name);
    $sport_id = $ALBetDAO->real_escape_string($sport_id);
    $bet_type = $ALBetDAO->real_escape_string($bet_type);
    $live_results_p1 = $ALBetDAO->real_escape_string($live_results_p1);
    $live_results_p2 = $ALBetDAO->real_escape_string($live_results_p2);
    $second_pass = $ALBetDAO->real_escape_string($second_pass);

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

    $market_id = $ALBetDAO->getMarket_id($sport_id, $markets_name, $bet_type);

    if (true === isset($market_id) && 0 < $market_id) {

        $UTIL->logWrite("[_sports_menu_detail_batch_application_ajax] UpdateScorelsportsBet == > " . $markets_name, "error");
        $result_2_p1 = 0;
        $result_2_p2 = 0;
        
        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateScorelsportsBet($fixture_id, $bet_type, $market_id, $live_results_p1, $live_results_p2, $result_2_p1, $result_2_p2)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    
        
    } else {
        $UTIL->logWrite("[!!!!_sports_menu_detail_batch_application_ajax] [error -1]", "error");
       
        $result['retCode'] = FAIL_EMPTY_DATA;
        $result['retMsg']  = FAIL_EMPTY_DATA_MSG;
        throw new Exception('mysqli_sql_exception!!!');
    }

    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '일괄적용 : time=>' . $fixture_start_date . ' fixture=>' . $fixture_id . ' bet_type=>' . $bet_type . ' markets_name=>' . $markets_name . ' live_results_p1=>' . $live_results_p1 . ' live_results_p2=>' . $live_results_p2;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',26) ;";
    //$ALBetDAO->setQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
  
} catch (\mysqli_sql_exception $e) {

    CommonUtil::logWrite('_sports_menu_detail_batch_application_ajax to 2' . $e->getMessage(), "db_error");
  
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg']  = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {

    $UTIL->logWrite("[_sports_menu_detail_batch_application_ajax] [error -2]", "error");
   
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg']  = FAIL_EXCEPTION_MSG;
} catch (\ReflectionException $e) {

    CommonUtil::logWrite('::::::::::::::: _sports_menu_detail_batch_application_ajax to 2 ReflectionException : ' . $e, "error");
  
    $result['retCode'] = FAIL_REFLECTION_EXCEPTION;
    $result['retMsg']  = FAIL_REFLECTION_EXCEPTION_MSG;
} finally {
    $UTIL->logWrite("[_sports_menu_detail_batch_application_ajax finally] ", "error");
    
    if(true == $is_trans_start){
       if(0 < $result['retCode'] ){
           $ALBetDAO->commit();
       }else{
           $ALBetDAO->rollback();
       }
    }

    if ($db_conn) {
      $ALBetDAO->dbclose();
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>