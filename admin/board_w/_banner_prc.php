<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();



if (!isset($_SESSION)) {
    session_start();
}

$BbsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BbsAdminDAO->dbconnect();

if($db_conn) {
    
    
$p_data['aid'] = $_SESSION['aid'];
 
$p_data['filename'] = trim(isset($_POST['filename']) ? $_POST['filename'] : '');
$p_data['status'] = trim(isset($_POST['status']) ? $BbsAdminDAO->real_escape_string($_POST['status']) : '');
$p_data['displayType'] = trim(isset($_POST['displayType']) ? $BbsAdminDAO->real_escape_string($_POST['displayType']) : '');
$p_data['rank'] = trim(isset($_POST['rank']) ? $BbsAdminDAO->real_escape_string($_POST['rank']) : '');
$p_data['thumbnail'] = 'banner/'.$p_data['filename'];

	try {
    
		
		$UTIL->logWrite("SELECT COUNT(*) as cnt FROM banners WHERE display_type=".$p_data['displayType']." AND status = ".$p_data['status']);
		
		
		$p_data['sql'] = "SELECT COUNT(*) as cnt FROM banners WHERE display_type = '".$p_data['displayType']."' AND status = 1";

		$db_dataArr = $BbsAdminDAO->getQueryData($p_data)[0];
		
		if($db_dataArr['cnt'] > 4 && $p_data['status'] == 1) {
			
			if($p_data['displayType'] == 1) {
				$displayTypeName = "PC";
			}else {
				$displayTypeName = "모바일";
			}
			$result["retCode"]	= 2000;
			$result['retMsg']	= $displayTypeName." 타입의 배너가 5개를 초과하였습니다. 삭제 후 진행해주세요.";
			
			echo json_encode($result,JSON_UNESCAPED_UNICODE);
		}else {
			$in_data = "values ('".$p_data['thumbnail']."', ".$p_data['status'].", ".$p_data['displayType'].", ".$p_data['rank'].", now())";
			$UTIL->logWrite("INSERT INTO banners (thumbnail, status, display_type, rank, create_dt) $in_data ");
			$p_data['sql'] = "INSERT INTO banners (thumbnail, status, display_type, rank, create_dt) $in_data ";
			
			$BbsAdminDAO->setQueryData($p_data);
			
			$BbsAdminDAO->dbclose();
			
			$result["retCode"]	= 1000;
			$result['retMsg']	= $p_data['sql'];
			
			echo json_encode($result,JSON_UNESCAPED_UNICODE);
		}
		
	} catch (\Exception $e) {
    	$UTIL->logWrite("[_banner_prc] [error -2]", "error");
    	$result['retCode'] = -3;
    	$result['retMsg'] = 'Exception 예외발생';
    }
} else {
	$UTIL->logWrite("[_banner_prc] [error 2200]", "error");
	$UTIL->checkFailType('2200', '', '', 'json');
	exit;
}
?>
