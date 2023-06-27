<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$idx1 = $BdsAdminDAO->real_escape_string($_POST['idx1']);
$idx2 = $BdsAdminDAO->real_escape_string($_POST['idx2']);
$idx3 = $BdsAdminDAO->real_escape_string($_POST['idx3']);
$idx4 = $BdsAdminDAO->real_escape_string($_POST['idx4']);

$contents1 = $BdsAdminDAO->real_escape_string($_POST['contents1']);
$contents2 = $BdsAdminDAO->real_escape_string($_POST['contents2']);
$contents3 = $BdsAdminDAO->real_escape_string($_POST['contents3']);
$contents4 = $BdsAdminDAO->real_escape_string($_POST['contents4']);

if ($db_conn) {
	$p_data['sql'] = "UPDATE base_rule_2 SET `contents` = '$contents1' WHERE idx = $idx1";
	$BdsAdminDAO->setQueryData($p_data);

	$p_data['sql'] = "UPDATE base_rule_2 SET `contents` = '$contents2' WHERE idx = $idx2";
	$BdsAdminDAO->setQueryData($p_data);

	$p_data['sql'] = "UPDATE base_rule_2 SET `contents` = '$contents3' WHERE idx = $idx3";
	$BdsAdminDAO->setQueryData($p_data);

	$p_data['sql'] = "UPDATE base_rule_2 SET `contents` = '$contents4' WHERE idx = $idx4";
	$BdsAdminDAO->setQueryData($p_data);
	
    $BdsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];
	
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
