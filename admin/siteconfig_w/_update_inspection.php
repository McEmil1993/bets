<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
$UTIL = new CommonUtil();
$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$msg_title = trim(isset($_POST['msg_title']) ? $_POST['msg_title'] : '');
$check_date = trim(isset($_POST['check_date']) ? $_POST['check_date'] : date("Y-m-d"));
$start_dt = trim(isset($_POST['start_dt']) ? $_POST['start_dt'] : '00:00');
$end_dt = trim(isset($_POST['end_dt']) ? $_POST['end_dt'] : '00:00');
$detail = trim(isset($_POST['detail']) ? urldecode($_POST['detail']) : '');

$start_dt = $check_date.' '.$start_dt;
$end_dt = $check_date.' '.$end_dt;

if($db_conn) {
    $p_data['sql'] = "update inspection set title = '$msg_title', contents = '$detail', start_dt = '$start_dt', end_dt = '$end_dt', update_dt = now() where idx = 1";

    $BdsAdminDAO->setQueryData($p_data);

    $BdsAdminDAO->dbclose();

    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
