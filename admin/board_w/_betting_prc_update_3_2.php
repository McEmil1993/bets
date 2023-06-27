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

$set_type = $BdsAdminDAO->real_escape_string($_POST['set_type']);
$set_type_val_1 = $BdsAdminDAO->real_escape_string($_POST['set_type_val_1']);
$set_type_val_2 = $BdsAdminDAO->real_escape_string($_POST['set_type_val_2']);
$set_type_val_3 = $BdsAdminDAO->real_escape_string($_POST['set_type_val_3']);
$set_type_val_4 = $BdsAdminDAO->real_escape_string($_POST['set_type_val_4']);
/*
$set_type_val_5 = $_POST['set_type_val_5'];
$set_type_val_6 = $_POST['set_type_val_6'];
$set_type_val_7 = $_POST['set_type_val_7'];
$set_type_val_8 = $_POST['set_type_val_8'];
$set_type_val_9 = $_POST['set_type_val_9'];
$set_type_val_10 = $_POST['set_type_val_10'];
*/

if ($db_conn) {
	// for ($i = 1; $i <= 10; ++$i) {
	for ($i = 1; $i <= 4; ++$i) {
		$set_type_val = $_POST['set_type_val_' . $i];
		$p_data['sql'] = "UPDATE t_game_config SET set_type_val = '$set_type_val' WHERE u_level = $i AND set_type = '$set_type'";
		$BdsAdminDAO->setQueryData($p_data);
	}

	// $BdsAdminDAO->setQueryData($p_data);
    $BdsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];
	
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
