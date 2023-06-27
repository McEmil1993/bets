<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['user_detail_check_time'])){
    $result["retCode"]	= 2000;
    $result['retMsg']	= '유효시간이 지났습니다.';
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
    return;
}
$user_detail_check_time = $_SESSION['user_detail_check_time'];

$gap = 1800;
$currentTime = date('Y-m-d H:i:s');
$checkTime = date("Y-m-d H:i:s", strtotime($user_detail_check_time . "+" . $gap . "seconds"));

if ($currentTime > $checkTime) {
    $result["retCode"]	= 2000;
    $result['retMsg']	= '유효시간이 지났습니다.';
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
    return;
}

$result["retCode"]	= SUCCESS;
$result['retMsg']	= 'success';

echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>