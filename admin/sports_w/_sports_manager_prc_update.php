<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$idx = $_POST['idx'];
$name = $_POST['name'];
$isUse = $_POST['isUse'];
$file_name = $_POST['file_name'];

if($db_conn) {
    $idx = $LSportsAdminDAO->real_escape_string($idx);
    $name = $LSportsAdminDAO->real_escape_string($name);
    $isUse = $LSportsAdminDAO->real_escape_string($isUse);
    $file_name = $LSportsAdminDAO->real_escape_string($file_name);
    $sql = "";
    if($file_name == ""){
        $sql = "update lsports_sports set display_name = '$name', is_use = $isUse  where idx = $idx";
    }else{
        $sql = "update lsports_sports set display_name = '$name', is_use = $isUse , image_path = '$file_name' where idx = $idx";
    }
    
    
    $LSportsAdminDAO->executeQuery($sql);
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $sql;

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
