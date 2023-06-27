<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$level = $BdsAdminDAO->real_escape_string($_POST['level']);
$set_type = $BdsAdminDAO->real_escape_string($_POST['set_type']);
$set_type_val = $BdsAdminDAO->real_escape_string($_POST['set_type_val']);

if ($db_conn) {
	$p_data['sql'] = "UPDATE 
						t_game_config 
					SET 
						set_type_val = '$set_type_val' 
					WHERE 
						u_level = $level 
					AND 
						set_type = '$set_type'
					";

    $BdsAdminDAO->setQueryData($p_data);
    $BdsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];
	
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
