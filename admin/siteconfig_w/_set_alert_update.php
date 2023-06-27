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

    $idx = trim(isset($_POST['idx']) ? $BbsAdminDAO->real_escape_string($_POST['idx']) : '');
    $location = trim(isset($_POST['location']) ? $BbsAdminDAO->real_escape_string($_POST['location']) : '');
    $content = trim(isset($_POST['content']) ? $BbsAdminDAO->real_escape_string($_POST['content']) : '');
    $file_location = trim(isset($_POST['file_location']) ? $BbsAdminDAO->real_escape_string($_POST['file_location']) : '');
    $page_line = trim(isset($_POST['page_line']) ? $BbsAdminDAO->real_escape_string($_POST['page_line']) : '');

	try {

        // $UTIL->logWrite("UPDATE notify_setting SET location = '".$location."', content='".$content."' ,file_location= '".$file_location."',update_dt = NOW() WHERE idx = '".$idx."'");
     
        $p_data['sql'] = "UPDATE notify_setting SET location = ?, content = ? ,file_location= ? ,page_line= ? ,update_dt = NOW() WHERE idx = ?";
        
        $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$location,$content,$file_location,$page_line,$idx]);

    
        $BbsAdminDAO->dbclose();
        
        $result["retCode"]	= 1000;
        $result['retMsg']	= $p_data ;
        
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
            
  
		
	} catch (\Exception $e) {
    	$UTIL->logWrite("[_NOTIFICATION_SETTINGS_UPDATE] [error -2]", "error");
    	$result['retCode'] = -3;
    	$result['retMsg'] = 'Exception 예외발생';
    }
} else {
	$UTIL->logWrite("[_NOTIFICATION_SETTINGS_UPDATE] [error 2200]", "error");
	$UTIL->checkFailType('2200', '', '', 'json');
	exit;
}
?>
