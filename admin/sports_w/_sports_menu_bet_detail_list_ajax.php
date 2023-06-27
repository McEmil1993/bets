<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

// 개별정산 처리 프론트 onBtnClickCalculate 함수에 의해서 호출된다.

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

$UTIL = new CommonUtil();

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');


if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$result['retCode'] = 1000;

if (false == isset($_POST['fixture_id']) || false == isset($_POST['markets_id'])) {
    $UTIL->logWrite("[_sports_menu_bet_detail_list_ajax] [error 2201]", "error");
    $UTIL->checkFailType('-2', '', '', 'json');
    return;
}

$bet_id = $_POST['bet_id'];
$bet_type = $_POST['bet_type'];
$fixture_start_date = $_POST['fixture_start_date'];

$ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $ALBetDAO->dbconnect();

if (!$db_conn) {
    $UTIL->logWrite("[_sports_menu_bet_detail_list_ajax] [error 2200]", "error");
    return;
}

do {

    try {
        
        $bet_id = $ALBetDAO->real_escape_string($bet_id);
        $bet_type = $ALBetDAO->real_escape_string($bet_type);
        $fixture_start_date = $ALBetDAO->real_escape_string($fixture_start_date); 
        $result['retCode'] = 1000;
           
        $result_list = $ALBetDAO->getBetMemberList($bet_id, $bet_type);
        $result['data'] = $result_list;
        
    } catch (\mysqli_sql_exception $e) {
        $UTIL->logWrite('[MYSQL EXCEPTION] mysqli_sql_exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "error");
        //$ALBetDAO->rollback();
        $result['retCode'] = -1005;
        $result['retMsg'] = "mysqli_sql_exception error";
    } catch (\Exception $e) {
        $UTIL->logWrite('::::::::::::::: set_charge_money Exception : ' . $e->getMessage(), "error");
        //$ALBetDAO->rollback();
        $result['retCode'] = -1006;
        $result['retMsg'] = "Exception error";
    } catch (\ReflectionException $e) {
        $UTIL->logWrite('::::::::::::::: set_charge_money ReflectionException : ' . $e->getMessage(), "error");
        //$ALBetDAO->rollback();
        $result['retCode'] = -1007;
        $result['retMsg'] = "ReflectionException error";
    } 
} while (0);

//if($result['retCode'] >  0){
//    $ALBetDAO->commit();
//}else{
//    $ALBetDAO->rollback();
//}
$ALBetDAO->dbclose();
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>