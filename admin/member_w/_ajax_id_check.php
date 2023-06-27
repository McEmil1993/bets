<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$memberId = isset($_POST['id']) ? $_POST['id'] : NULL;
if (strlen($memberId) < 4 || 12 < strlen($memberId)) {
    $result["retCode"]	= 2000;
    $result['retMsg']	= '아이디는 4~12 이상 사용가능합니다..';
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
    return;
}

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
    
    $memberId = $BdsAdminDAO->real_escape_string($memberId);
    
    $p_data['sql'] = "select count(idx) as cnt from member where id = '$memberId'";
    $dbResult = $BdsAdminDAO->getQueryData($p_data);
    $BdsAdminDAO->dbclose();
    
    if($dbResult[0]['cnt'] > 0){
        $result["retCode"]	= 2000;
        $result['retMsg']	= '이미 사용중인 아이디입니다.';
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        return;
    }
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>