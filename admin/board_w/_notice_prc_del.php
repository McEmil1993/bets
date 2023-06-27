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
    $p_data['sql'] = "SELECT count(*) as cnt, thumbnail FROM notices WHERE thumbnail in (SELECT thumbnail FROM notices WHERE idx = $idx)";
    $db_dataArr = $BdsAdminDAO->getQueryData($p_data)[0];
    
    $p_data['sql'] = "delete from notices where idx = $idx";
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
?>
