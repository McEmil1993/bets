<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_define.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();
$UTIL = new CommonUtil();    

$idx = $BdsAdminDAO->real_escape_string($_POST['idx']);

if($db_conn) {
	
	$p_data['sql'] = "SELECT * FROM banners WHERE idx = ".$idx;
	$db_dataArr = $BdsAdminDAO->getQueryData($p_data)[0];
	
	$displayType = $db_dataArr['display_type'];
	$status = $db_dataArr['status'];
	
	$p_data['sql'] = "SELECT COUNT(*) as cnt FROM banners WHERE display_type = ".$displayType." AND status = 1";
	
	$db_dataArr = $BdsAdminDAO->getQueryData($p_data)[0];
	$UTIL->logWrite(($db_dataArr['cnt']));
	
	if($status == 1 && $db_dataArr['cnt'] < 2) {
		if($displayType == 1) {
			$displayTypeName = "PC";
		}else {
			$displayTypeName = "모바일";
		}
		$result["retCode"]	= 2000;
		$result['retMsg']	= "사용하는 ".$displayTypeName."타입의 배너는 최소 1개 존재해야합니다.";
		
		echo json_encode($result,JSON_UNESCAPED_UNICODE);
	}else {
	    $p_data['sql'] = "SELECT count(*) as cnt, thumbnail FROM banners WHERE thumbnail in (SELECT thumbnail FROM banners WHERE idx = $idx)";
	    $db_dataArr = $BdsAdminDAO->getQueryData($p_data)[0];
	    
	    $p_data['sql'] = "delete from banners where idx = $idx";
	    $BdsAdminDAO->setQueryData($p_data);
	    $BdsAdminDAO->dbclose();
	
	    // 같은 이름의 파일을 사용하는데가 없으면 파일삭제
	    if($db_dataArr['cnt'] == 1){
	        $POST_DATA = array(
	         'deletePath' => IMAGE_PATH.'/'.$db_dataArr['thumbnail']
	        );
	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, IMAGE_SERVER_DELETE_URL);
	        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
	        $response = curl_exec($curl);
	        curl_close ($curl);
	    }
		
	    $result["retCode"]	= 1000;
	    $result['retMsg']	= $p_data['sql'];
	
	    echo json_encode($result,JSON_UNESCAPED_UNICODE);
	}
}
?>
