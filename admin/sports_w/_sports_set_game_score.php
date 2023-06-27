<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$UTIL = new CommonUtil();

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$idx = $LSportsAdminDAO->real_escape_string($_POST['idx']);
$home_score = $LSportsAdminDAO->real_escape_string($_POST['home_score']);
$away_score = $LSportsAdminDAO->real_escape_string($_POST['away_score']);

if($db_conn) {
    $pdate['sql'] = "SELECT result_score FROM member_bet_detail where idx = $idx;";
    $result_sql = $LSportsAdminDAO->getQueryData($pdate);
    $arr_result_score = json_decode($result_sql[0]['result_score'], true);
    $arr_result_score['live_results_p1'] = $home_score;
    $arr_result_score['live_results_p2'] = $away_score;
    $result_score = json_encode($arr_result_score);
    
    $sql = "update member_bet_detail set result_score = '$result_score' where idx = $idx";
    $LSportsAdminDAO->executeQuery($sql);
    $LSportsAdminDAO->dbclose();

    $result["retCode"]	= 1000;
    $result['retMsg']	= 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
