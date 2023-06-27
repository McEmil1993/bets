<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

// 리그 일괄 수정 버튼을 누를면 호출된다.

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$id = $_POST['id'];
$name = $_POST['name'];
$front_name = $_POST['front_name'];

if($db_conn) {
    $id = $LSportsAdminDAO->real_escape_string($id);
    $name = $LSportsAdminDAO->real_escape_string($name);
    $front_name = $LSportsAdminDAO->real_escape_string($front_name);
    
    $sql = "update lsports_leagues set image_path = '$name' where display_name = '$front_name'";
    $LSportsAdminDAO->executeQuery($sql);
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg'] = 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
