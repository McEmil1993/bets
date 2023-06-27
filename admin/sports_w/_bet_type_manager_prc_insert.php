<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$UTIL = new CommonUtil();

$id = $_POST['id'];
$name = trim(isset($_POST['name']) ? $_POST['name'] : 0);

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if($db_conn) {
    
    $id = $LSportsAdminDAO->real_escape_string($id);
    $name = $LSportsAdminDAO->real_escape_string($name);
     
    $in_data = "values ($id, '$name', now(), now())";

    $sql = "INSERT INTO lsports_markets (id, name, create_dt, update_dt) $in_data ";
    $LSportsAdminDAO->executeQuery($sql);
    $LSportsAdminDAO->dbclose();
    
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
