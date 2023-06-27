<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

if (!isset($_SESSION)) {
	session_start();
}

//$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$msg_title = trim(isset($_POST['msg_title']) ? $BdsAdminDAO->real_escape_string($_POST['msg_title']) : '');
$p_content_buff = isset($_POST['msg_content']) ? $BdsAdminDAO->real_escape_string($_POST['msg_content']) : '';
// $p_content = (urldecode($p_content_buff));
// $msg_content = htmlspecialchars(addslashes($p_content));
$msg_content = $p_content_buff;
$msg_content = str_replace("nbsp", "&nbsp;", $msg_content);
$a_id = $_SESSION['aid'];
// $msg_content = str_replace("&nbsp;", "", $msg_content);
// $msg_content = str_replace("&amp;", "", $msg_content);

if ($db_conn) {
    $p_data['sql'] = "UPDATE join_message SET a_id = '$a_id', title = '$msg_title', contents = '$msg_content', update_dt = NOW()";
	
    $BdsAdminDAO->setQueryData($p_data);

    $BdsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
