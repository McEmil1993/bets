<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$idx = $_POST['idx'];
$bet_group_id = $_POST['gid'];

if($db_conn) {
    $idx = $LSportsAdminDAO->real_escape_string($idx);
    $bet_group_id = $LSportsAdminDAO->real_escape_string($bet_group_id);
    
    $sql = "update lsports_markets set is_delete = 1, delete_dt = now() where idx = $idx and bet_group = $bet_group_id";
	
    $LSportsAdminDAO->executeQuery($sql);

    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
