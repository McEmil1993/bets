<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();


if (!isset($_SESSION)) {
    session_start();
}



//$u_idxArr = $_POST['sel_user'];
//$p_data['sel_level'] = trim(isset($_POST['sel_level']) ? $_POST['sel_level'] : 0);
//$p_data['setUserType'] = trim(isset($_POST['setUserType']) ? $_POST['setUserType'] : '');


$BbsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BbsAdminDAO->dbconnect();

if ($db_conn) {

    $p_data['aid'] = $_SESSION['aid'];
    $p_data['msg_title'] = trim(isset($_POST['msg_title']) ? $BbsAdminDAO->real_escape_string($_POST['msg_title']) : '');
    $p_data['filename'] = trim(isset($_POST['filename']) ?   $_POST['filename'] : '');
    $p_data['detail'] = trim(isset($_POST['detail']) ? $BbsAdminDAO->real_escape_string($_POST['detail']) : '');
    $p_data['status'] = trim(isset($_POST['status']) ? $BbsAdminDAO->real_escape_string($_POST['status']) : '');
    $p_data['thumbnail'] = 'notice/' . $p_data['filename'];

    $in_data = "values ('" . $p_data['msg_title'] . "', '" . $p_data['thumbnail'] . "', '" . $p_data['detail'] . "', " . $p_data['status'] . ", 0, now())";

    $p_data['sql'] = "INSERT INTO notices (name, thumbnail, detail, status, del_flag, create_dt) $in_data ";

    $BbsAdminDAO->setQueryData($p_data);

    $BbsAdminDAO->dbclose();

    $result["retCode"] = 1000;
    $result['retMsg'] = $p_data['sql'];

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
