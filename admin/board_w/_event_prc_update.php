<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
$UTIL = new CommonUtil();
//$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$idx = $_POST['idx'];
$msg_title = trim(isset($_POST['msg_title']) ? $BdsAdminDAO->real_escape_string($_POST['msg_title']) : '');
$p_data['filename'] = trim(isset($_POST['filename']) ? $_POST['filename'] : '');
$detail = trim(isset($_POST['detail']) ? $BdsAdminDAO->real_escape_string(urldecode($_POST['detail'])) : '');
$status = trim(isset($_POST['status']) ? $BdsAdminDAO->real_escape_string($_POST['status']) : '');
$thumbnail = 'event/'.$p_data['filename'];

//$p_data['aid'] = 'asdf';

if($db_conn) {

	$UTIL->logWrite(	urldecode($_POST['detail']));
    if (isset($p_data['filename']) && $p_data['filename'] != '') {
        $p_data['sql'] = "update events set name = '$msg_title', thumbnail = '$thumbnail', detail = '$detail', status = $status where idx = $idx";
    }else{
        $p_data['sql'] = "update events set name = '$msg_title', detail = '$detail', status = $status where idx = $idx";
    }

    $BdsAdminDAO->setQueryData($p_data);

    $BdsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
