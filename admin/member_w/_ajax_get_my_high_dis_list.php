<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

// 내가 속할수 있는 총판리스트를 가져온다.
$u_business = isset($_POST['u_business']) ? $_POST['u_business'] : 10;
$m_idx = isset($_POST['m_idx']) ? $_POST['m_idx'] : 0;

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
    $u_business = $BdsAdminDAO->real_escape_string($u_business);
    
    $p_data['sql'] = "select idx, id, nick_name, u_business from member where u_business in (select high_id from business_type where id = ?) and idx <> $m_idx";
    $dbResult = $BdsAdminDAO->getQueryData_pre($p_data['sql'], [$u_business]);
    $cnt = is_null($dbResult) ? 0 : count($dbResult);
    
    $BdsAdminDAO->dbclose();

    if($cnt == 0){
        $result["retCode"]	= 2000;
        $result['retMsg']	= '해당 목록이 없습니다.';
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        return;
    }
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= 'success';
    $result["list"]	= $dbResult;

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>