<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

$p_data['p_seq'] = trim(isset($_POST['seq']) ? $_POST['seq'] : 0);


if($p_data["p_seq"]=='') {
	$UTIL->checkFailType('2130','','','json');
	exit;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {

        $p_data['p_seq'] = $MEMAdminDAO->real_escape_string($p_data['p_seq']);
    
	$p_data["p_use_kind"] = "Y";
	// $Rs = $MEMAdminDAO->getMsgSetInfo($p_data);
	$Rs = $MEMAdminDAO->getTemplate($p_data['p_seq']);
	$MEMAdminDAO->dbclose();

	if(count($Rs) > 0)
	{
		$row = $Rs[0];
		$result["retCode"]		= 1000;
		$result["db_title"]		= $row['title'];
		$result["db_content"]	= htmlspecialchars_decode($row['content']);
	}
	else {
		$UTIL->checkFailType('2199','','','json');
		exit;
	}
}
else {
	$UTIL->logWrite("[_msg_set_prc] [error 2200]","error");
	$UTIL->checkFailType('2200','','','json');
	exit;
}

echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
