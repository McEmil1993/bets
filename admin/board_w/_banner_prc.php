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
		$displayTypeName = "";
		$chktyp = "";

		if($p_data['displayType'] == 1) {
			$displayTypeName = "PC";
			$chktyp = "2";
		}else {
			$displayTypeName = "모바일";
			$chktyp = "1";
		}
    
		
		$UTIL->logWrite("SELECT COUNT(*) as cnt FROM banners WHERE display_type=".$p_data['displayType']." AND status = ".$p_data['status']);
		
		
		$p_data['sql'] = "SELECT COUNT(*) as cnt FROM banners WHERE display_type = ? AND status = ?";

		$db_dataArr = $BbsAdminDAO->getQueryData_pre($p_data['sql'],[$p_data['displayType'], 1])[0];
		
		if($db_dataArr['cnt'] > 4 && $p_data['status'] == 1) {
			
			$result["retCode"]	= 2000;
			$result['retMsg']	= $displayTypeName." 타입의 배너가 5개를 초과하였습니다. 삭제 후 진행해주세요.";
			
			echo json_encode($result,JSON_UNESCAPED_UNICODE);
		}else {


			$sql_check['sql'] = "SELECT COUNT(*) as cnt FROM banners WHERE display_type = ? AND rank = ?";
			$count_data = $BbsAdminDAO->getQueryData_pre($sql_check['sql'],[$p_data['displayType'], $p_data['rank']])[0];

			// $result["retCode"]	= 2000;
			// $result['retMsg']	= $count_data['cnt'];
			// echo json_encode($result,JSON_UNESCAPED_UNICODE);
			
			if ($count_data['cnt'] == 1) {

				$result["retCode"]	= 2000;
				$result['retMsg']	= $displayTypeName." 동일한 순번이 존재합니다";
				
				echo json_encode($result,JSON_UNESCAPED_UNICODE);
				
			}else{
				
				$UTIL->logWrite("INSERT INTO banners (thumbnail, status, display_type, rank, create_dt) values ('".$p_data['thumbnail']."', ".$p_data['status'].", ".$p_data['displayType'].", ".$p_data['rank'].", now()) ");
				$p_data['sql'] = "INSERT INTO banners (thumbnail, status, display_type, rank, create_dt) values (?, ?, ?, ?, now()) ";
				
				$BbsAdminDAO->setQueryData_pre($p_data['sql'],[$p_data['thumbnail'],$p_data['status'],$p_data['displayType'],$p_data['rank']]);


				$count['sql'] = " SELECT * FROM banners WHERE display_type = ? AND status = ? AND rank = ?";
	
				$cnt = $BbsAdminDAO->getQueryData_pre($count['sql'],[$chktyp,0,$p_data['rank']]);

				$arry = "";

				foreach($cnt as $value) {
					// $arry .= $value['idx'];
					
					$update_data['sql'] = "update banners set status = ? where idx = ?";
	
					$BbsAdminDAO->setQueryData_pre($update_data['sql'],[1,$value['idx']]);
				}
	
				// if ($cnt[0]['C'] == 1) {
				
					
	
				// }
				// $update_data['sql'] = "update banners set status = 1 where idx = '".$cnt[0]['idx']."'";
		
				
				$BbsAdminDAO->dbclose();
				
				$result["retCode"]	= 1000;
				$result['retMsg']	= $count['sql'];
				
				echo json_encode($result,JSON_UNESCAPED_UNICODE);
				
			}

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
