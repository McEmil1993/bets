<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
	session_start();
}

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();


if ($db_conn) {

    $p_data['msg_title'] = trim(isset($_POST['msg_title']) ? $BdsAdminDAO->real_escape_string($_POST['msg_title']) : '');
    $p_content_buff = isset($_POST['msg_content']) ? $BdsAdminDAO->real_escape_string($_POST['msg_content']) : '';
    $p_data['msg_content'] = $p_content_buff;
    $p_data['msg_content'] = str_replace("nbsp", "&nbsp;", $p_data['msg_content']);
    $p_data['aid'] = $_SESSION['aid'];
    
    $p_data['sql'] = "SELECT COUNT(*) as cnt FROM join_message";
    
    $db_dataArr = $BdsAdminDAO->getQueryData($p_data)[0];
    
    if($db_dataArr['cnt'] > 0) {
    	
    	$result["retCode"]	= 2000;
    	$result['retMsg']	= "현재 사용중인 회원가입 메세지가 존재하여 추가할 수 없습니다.";
    	
    	echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }else {
    	
    	$in_data = "values ('" . $p_data['aid'] . "', '" . $p_data['msg_title']. "', '" . $p_data['msg_content']. "', now())";
    	
    	$p_data['sql'] = "INSERT INTO join_message (a_id, title, contents, update_dt) $in_data ";
    	
    	$UTIL->logWrite($p_data['sql']);
    	$BdsAdminDAO->setQueryData($p_data);
    	
    	$BdsAdminDAO->dbclose();
    	
    	$result["retCode"] = 1000;
    	$result['retMsg'] = $p_data['sql'];
    	
    	echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
?>
