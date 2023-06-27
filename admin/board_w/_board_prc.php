<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();


$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {

    $member_idx = $BdsAdminDAO->real_escape_string($_POST['member_idx']);
    $adminId = trim(isset($_POST['aid']) ? $BdsAdminDAO->real_escape_string($_POST['aid']) : '');
    $nickname = trim(isset($_POST['nickname']) ? $BdsAdminDAO->real_escape_string($_POST['nickname']) : '');

    $title = trim(isset($_POST['msg_title']) ? $BdsAdminDAO->real_escape_string($_POST['msg_title']) : '');

    $message= trim(isset($_POST['msg_content']) ? $BdsAdminDAO->real_escape_string(urldecode($_POST['msg_content'])) : '');

    //$message = $p_content_buff;
    //$message = str_replace("nbsp", "&nbsp;", $message);

    //$message = addslashes(str_replace("&amp;", "", $message));

    // $in_data = "values ('".$p_data['aid']."', '".$p_data['msg_title']."', '".$p_data['msg_content']."', now())";
    $p_data['sql'] = "INSERT INTO 
						menu_board 
							(idx, member_idx, a_id, nick_name, title, contents, create_dt, display) 
						VALUES 
							(NULL, $member_idx, '$adminId', '$nickname', '$title', '$message', NOW(), 1)";

    $BdsAdminDAO->setQueryData($p_data);

    //$p_data['msg_key']  =  date("YmdHis").substr(microtime(),2,6).rand(10000,99999);

    $BdsAdminDAO->dbclose();

    $result['retCode'] = 1000;
    $result['retMsg'] = $p_data['sql'];

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
