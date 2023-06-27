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
        $UTIL->logWrite("[_sports_menu_detail_re_calculate] [error 2200]", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    $is_trans_start = $ALBetDAO->trans_start();
    if (false == $is_trans_start) {
        // start transaction error
        $UTIL->logWrite("[_sports_menu_detail_re_calculate] [error -10]", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }

    if (false == isset($_POST['fixture_id']) || false == isset($_POST['markets_id']) || false == $_POST['second_pass']) {
        $UTIL->logWrite("[_msg_set_prc] [error 2201]", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        throw new Exception('param error!!!');
    }

    $fixture_id = $ALBetDAO->real_escape_string($_POST['fixture_id']);
    $bet_type = $ALBetDAO->real_escape_string($_POST['bet_type']);
    $markets_id = $ALBetDAO->real_escape_string($_POST['markets_id']);
    $bet_base_line = $ALBetDAO->real_escape_string($_POST['bet_base_line']);
    $fixture_start_date = $ALBetDAO->real_escape_string($_POST['fixture_start_date']);
    $bet_result_p1 = $ALBetDAO->real_escape_string($_POST['bet_result_p1']);
    $bet_result_p2 = $ALBetDAO->real_escape_string($_POST['bet_result_p2']);
    $bet_result_2_p1 = isset($_POST['bet_result_2_p1']) ? $ALBetDAO->real_escape_string($_POST['bet_result_2_p1']) : 0;
    $bet_result_2_p2 = isset($_POST['bet_result_2_p2']) ? $ALBetDAO->real_escape_string($_POST['bet_result_2_p2']) : 0;
    $second_pass = $ALBetDAO->real_escape_string($_POST['second_pass']);

    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    $result_second_pass = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $result_second_pass) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $result_second_pass = $result_second_pass[0];
    
    if (hash('sha512', $second_pass) != $result_second_pass['set_type_val']) {
        $UTIL->logWrite("[!!!! second_pass _sports_menu_detail_batch_application_ajax] [error -1]", "error");
        $result['retCode'] = FAIL_SECOND_PASS;
        $result['retMsg'] = FAIL_SECOND_PASS_MSG;
        throw new Exception('password error!!!');
    }

    // 이미 정산된 데이터를 롤백한다.
    //$ALBetDAO->UpdateLsportsBetByDetailInfo($fixture_start_date, $fixture_id, $bet_type, $markets_id, $bet_base_line, 'OFF');
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

    if (0 < count($arrReResult)) {

        CommonUtil::logWrite("re_calculate  1 : ", "info");

        if ((0 != $bet_result_p1 && false == isset($bet_result_p1)) && (0 != $bet_result_p2 && false == isset($bet_result_p2))) {

            $bet_result_p1 = $arrReResult[0]['fx_results_p1'];
            $bet_result_p2 = $arrReResult[0]['fx_results_p2'];
            $bet_result_2_p1 = $arrReResult[0]['fx_results_p1'];
            $bet_result_2_p2 = $arrReResult[0]['fx_results_p2'];
        }

        $checkBetCalculate = array();  // mb_bt_idx 


        foreach ($arrReResult as $value) {

            $value['result_p1'] = $bet_result_p1;
            $value['result_p2'] = $bet_result_p2;
            $value['result_2_p1'] = $bet_result_2_p1;
            $value['result_2_p2'] = $bet_result_2_p2;
            $value['result_extra'] = 0;
            $bet_status = 1;
            list($bet_status, $bet_price, $value['result_p1'], $value['result_p2'], $value['result_extra']) = GameUtil::getBetStatus($value, $ALBetDAO);

            if (1 == $bet_status)
                continue;

            $array_result_score = array('live_results_p1' => $value['result_p1'], 'live_results_p2' => $value['result_p2'], 'result_extra' => $value['result_extra']);
           
            if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateMemberBetDetail($value['idx'], $bet_status, json_encode($array_result_score))) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }

            CommonUtil::logWrite("getBetStatus  : array_result_score : " . json_encode($array_result_score) . " bet_status : " . $bet_status . ' market_id : ' . $value['ls_markets_id'] . ' fix_id' . $value['ls_fixture_id'], "info");

            // 이미 처리되었다.
            if (in_array($value['mb_bt_idx'], $checkBetCalculate))
                continue;

            $value['admin_id'] = $_SESSION['aid'];
            list($return_value, $total_bet_money, $take_money) = GameCode::doReCalculate($ALBetDAO, $UTIL, $value, $bet_type);
            if (false == $return_value)
                continue;

            $checkBetCalculate[] = $value['mb_bt_idx'];

            // 정산 -> 적특 이럴시 총판 롤링 금액 다시 계산 
            if (3 == $value['bet_status'] && $value['take_money'] != $value['total_bet_money'] && $total_bet_money == $take_money) {
                GameCode::doReCalculateDistributor($ALBetDAO, $value, 'DEC');
            } else if (3 == $value['bet_status'] && $value['take_money'] == $value['total_bet_money'] && $total_bet_money != $take_money) { // 적특 -> 정산
                GameCode::doReCalculateDistributor($ALBetDAO, $value, 'ADD');
            } else if (1 == $value['bet_status'] && $total_bet_money != $take_money) { // 미정산 처리
                GameCode::doReCalculateDistributor($ALBetDAO, $value, 'ADD');
            }
        }
    }

    if (!isset($bet_result_2_p1)) {
        $bet_result_2_p1 = 0;
    }
    if (!isset($bet_result_2_p2)) {
        $bet_result_2_p2 = 0;
    }
    
    if (!isset($bet_result_p1)) {
        $bet_result_p1 = 0;
    }
    if (!isset($bet_result_p2)) {
        $bet_result_p2 = 0;
    }

    //$ALBetDAO->UpdateScorelsportsBetEndSettleCmp($fixture_start_date, $fixture_id, $bet_type, $markets_id, $bet_base_line, $bet_result_p1, $bet_result_p2, $bet_result_2_p1, $bet_result_2_p2);

    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateScorelsportsBetEndSettleCmp($fixture_id, $bet_type, $markets_id, $bet_base_line, $bet_result_p1, $bet_result_p2, $bet_result_2_p1, $bet_result_2_p2)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    // 개별 수동 정산

    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '개별수동정산 : time=>' . $fixture_start_date . ' fixture=>' . $fixture_id . ' bet_type=>' . $bet_type . ' markets_id=>' . $markets_id .
            ' bet_base_line=>' . $bet_base_line . ' bet_result_p1=>' . $bet_result_p1 . ' bet_result_p2=>' . $bet_result_p2 . ' bet_result_2_p1=>' . $bet_result_2_p1 . ' bet_result_2_p2=>' . $bet_result_2_p2;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $ALBetDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',21) ;";

    //$ALBetDAO->setQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
} catch (\mysqli_sql_exception $e) {
    $UTIL->logWrite('sports_menu_detail_re_calculate_ajax [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("sports_menu_detail_re_calculate_ajax e : " . $e->getMessage(), "error");

    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} catch (\ReflectionException $e) {
    $UTIL->logWrite('::::::::::::::: sports_menu_detail_re_calculate_ajax ReflectionException : ' . $e, "error");

    $result['retCode'] = FAIL_REFLECTION_EXCEPTION;
    $result['retMsg'] = FAIL_REFLECTION_EXCEPTION_MSG;
} finally {
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
                       
            $ALBetDAO->commit();
            $UTIL->logWrite('::::::::::::::: sports_menu_detail_re_calculate_ajax commit : ', "error");
        } else {
           
            $ALBetDAO->rollback();
            $UTIL->logWrite('::::::::::::::: sports_menu_detail_re_calculate_ajax rollback : ', "error");
        }
    }

    if ($db_conn) {
        $ALBetDAO->dbclose();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>