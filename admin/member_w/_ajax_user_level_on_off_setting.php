<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$level = $_POST['level'];
$flag = $_POST['flag'];

if (false === isset($level) || false === isset($flag)) {
    $result["retCode"] = -1; // 파라미터 오류 
    $result['retMsg'] = '파라미터 오류 ';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    return;
}

if ($level <= 0 || 10 < $level || ($flag != 'ON' && $flag != 'OFF')) {
    $result["retCode"] = -2; // 파라미터 오류 
    $result['retMsg'] = '데이터 오류 ';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    return;
}

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if (!$db_conn) {
    
    $level = $MEMAdminDAO->real_escape_string($level);
    $flag = $MEMAdminDAO->real_escape_string($flag);
    
    $result["retCode"] = -2200; // 파라미터 오류 
    $result['retMsg'] = '디비 연결 오류';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    $BdsAdminDAO->dbclose();
    return;
}

$p_data['sql'] = "update member_level_up set flag = '$flag' where level = $level";

$dbResult = $BdsAdminDAO->setQueryData($p_data);

$BdsAdminDAO->dbclose();
$result["retCode"] = 1000;
$result['retMsg'] = 'success';
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>