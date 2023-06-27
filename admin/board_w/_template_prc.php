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


$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();


if ($db_conn) {


    $p_data['type'] = trim(isset($_POST['type']) ? $BdsAdminDAO->real_escape_string($_POST['type']) : 0);
    $p_data['division'] = trim(isset($_POST['division']) ? $BdsAdminDAO->real_escape_string($_POST['division']) : '');
    $p_data['msg_title'] = trim(isset($_POST['msg_title']) ? $BdsAdminDAO->real_escape_string($_POST['msg_title']) : '');
    $p_content_buff = isset($_POST['msg_content']) ? $BdsAdminDAO->real_escape_string($_POST['msg_content']) : '';
// $p_content 				= (urldecode($p_content_buff));
// $p_data['msg_content']	= htmlspecialchars(addslashes($p_content));

    $p_data['msg_content'] = $p_content_buff;
    $p_data['msg_content'] = str_replace("nbsp", "&nbsp;", $p_data['msg_content']);
// $p_data['msg_content'] = str_replace("&nbsp;", "", $p_data['msg_content']);
// $p_data['msg_content'] = str_replace("&amp;", "", $p_data['msg_content']);

    $in_data = "values (" . $p_data['type'] . ", '" . $p_data['division'] . "', '" . $p_data['msg_title'] . "', '" . $p_data['msg_content'] . "', now())";

    $p_data['sql'] = "INSERT INTO template (type, division, title, content, update_dt) $in_data ";

    $BdsAdminDAO->setQueryData($p_data);

    $BdsAdminDAO->dbclose();

    $result["retCode"] = 1000;
    $result['retMsg'] = $p_data['sql'];

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
