<?php
session_start();
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

/* get member information from database */
$MEMAdminDAO        = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn            = $MEMAdminDAO->dbconnect();
$aid                = $_SESSION["aid"];
$query_result       = $MEMAdminDAO->getSecondPass();
$pass               = $query_result[0]["set_type_val"];
$entered_pass       = md5($_POST["password_auth"]);
$return             = [];

if($pass == $entered_pass)
{
    echo json_encode(1);
}
else
{
    echo json_encode(0);
}
?>