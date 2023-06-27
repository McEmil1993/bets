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

$category = $BdsAdminDAO->real_escape_string($_POST['category']);
$sub_category = $BdsAdminDAO->real_escape_string($_POST['sub_category']);
$title = $BdsAdminDAO->real_escape_string($_POST['title']);
$content = $BdsAdminDAO->real_escape_string($_POST['content']);

if ($db_conn) {
	$p_data['sql'] = "UPDATE 
						category_rule 
					SET 
						title = '$title', 
						content = '$content', 
						update_dt = NOW() 
					WHERE 
						category = $category 
					AND 
						sub_category = $sub_category
					";

	$BdsAdminDAO->setQueryData($p_data);
    $BdsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];
	
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
