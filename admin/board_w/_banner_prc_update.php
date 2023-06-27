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
//$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$idx = $_POST['idx'];
$p_data['filename'] = trim(isset($_POST['filename']) ? $_POST['filename'] : '');
$status = trim(isset($_POST['status']) ? $BdsAdminDAO->real_escape_string($_POST['status']) : '');
$displayType = trim(isset($_POST['displayType']) ? $BdsAdminDAO->real_escape_string($_POST['displayType']) : '');
$rank = trim(isset($_POST['rank']) ? $BdsAdminDAO->real_escape_string($_POST['rank']) : '');
$thumbnail = 'banner/'.$p_data['filename'];

//$p_data['aid'] = 'asdf';

if($db_conn) {
	$chktyp = "";
	if($displayType == 1) {
		$chktyp = "2";
	}else {
		$chktyp = "1";
	}
	
	$p_data['sql'] = "SELECT COUNT(*) as cnt FROM banners WHERE display_type = ? AND status = ?";

	
	
	$db_dataArr = $BdsAdminDAO->getQueryData_pre($p_data['sql'],[$displayType, 1] )[0];
	$UTIL->logWrite(($db_dataArr['cnt']));
	
	if($db_dataArr['cnt'] < 1 && $status == 0) {
		if($displayType == 1) {
			$displayTypeName = "PC";
		}else {
			$displayTypeName = "모바일";
		}
		$result["retCode"]	= 2000;
		$result['retMsg']	= "사용하는 ".$displayTypeName."타입의 배너는 최소 1개 존재해야합니다.";
		
		echo json_encode($result,JSON_UNESCAPED_UNICODE);
	}else {
	
		$p_data['sql'] = "SELECT COUNT(*) as cnt FROM banners WHERE display_type = ? AND status = ?";
		
		$db_dataArr = $BdsAdminDAO->getQueryData_pre($p_data['sql'], [$displayType, 1])[0];
		
		$displayTypeName = "";
		

		if($db_dataArr['cnt'] > 4 && $status == 1) {
			if($displayType == 1) {
				$displayTypeName = "PC";
				
			}else {
				$displayTypeName = "모바일";
				
			}
			$result["retCode"]	= 2000;
			$result['retMsg']	= "사용하는 ".$displayTypeName."타입의 배너가 5개를 초과하였습니다. 삭제 후 진행해주세요.";
			
			echo json_encode($result,JSON_UNESCAPED_UNICODE);
		}else {
			
			if (isset($p_data['filename']) && $p_data['filename'] != '') {
				$p_data['sql'] = "update banners set thumbnail = ?, status = ?, display_type = ?,banners.rank = ? where idx = ?";

				$BdsAdminDAO->setQueryData_pre($p_data['sql'],[$thumbnail, $status, $displayType, $rank, $idx]);
			}else{
				$p_data['sql'] = "update banners set status = ?, display_type = ?,banners.rank = ? where idx = ?";
				$BdsAdminDAO->setQueryData_pre($p_data['sql'],[$status, $displayType, $rank, $idx ]);
			}
			
			


			$count['sql'] = " SELECT * FROM banners WHERE display_type = ? AND status = 0 AND rank = ?";
	
			$cnt = $BdsAdminDAO->getQueryData_pre($count['sql'], [$chktyp, $rank ]);

			foreach($cnt as $value) {
				// $arry .= $value['idx'];
				
				$update_data['sql'] = "update banners set status = 1 where idx = ?";

				$BdsAdminDAO->setQueryData_pre($update_data['sql'], [$value['idx']]);
			}

			
			$BdsAdminDAO->dbclose();
			
			$result["retCode"]	= 1000;
			$result['retMsg']	= $p_data['sql'];
			
			echo json_encode($result,JSON_UNESCAPED_UNICODE);
		}
	}
}
?>
