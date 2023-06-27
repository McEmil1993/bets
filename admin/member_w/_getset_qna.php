<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

$p_data['p_seq'] = trim(isset($_POST['seq']) ? $_POST['seq'] : 0);
$p_data['p_type'] = trim(isset($_POST['ptype']) ? $_POST['ptype'] : '');

if($p_data["p_seq"]=='') {
	$UTIL->checkFailType('2130','','','json');
	exit;
}


if($p_data['p_type'] == 'answer') {
    
    //$p_data['msg_title'] = trim(isset($_POST['msg_title']) ? $_POST['msg_title'] : '');
    
    $p_content_buff = trim(isset($_POST['msg_answer']) ? $_POST['msg_answer'] : '');
    $p_content 				= (urldecode($p_content_buff));
    $p_data['msg_answer']	= htmlspecialchars(addslashes($p_content));
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    
    
    $p_data['p_seq'] = $MEMAdminDAO->real_escape_string($p_data['p_seq']);
    $p_data['p_type'] = $MEMAdminDAO->real_escape_string($p_data['p_type']);
    
  
    if($p_data['p_type'] == 'answer') {
        
        $p_data['sql'] = "UPDATE menu_qna SET answer='".$p_data['msg_answer']."', is_answer='Y' WHERE idx = ".$p_data['p_seq']." ";
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"]		= 1000;
    }
    else {
        $p_data['sql'] = " SELECT idx, title, contents, answer FROM menu_qna ";
        $p_data['sql'] .= " WHERE idx = ".$p_data['p_seq']." ";
        
        $db_dataList = $MEMAdminDAO->getQueryData($p_data);
        $db_list_cnt = count($db_dataList);
        
        if($db_list_cnt > 0)
        {
            //$row = $Rs[0];
            $result["retCode"]		= 1000;
            $result["db_title"]		= $db_dataList[0]['title'];
            $result["db_content"]	= $db_dataList[0]['contents'];
            $result["db_answer"]	= $db_dataList[0]['answer'];
        }
        else {
            $UTIL->checkFailType('2199','','','json');
            exit;
        }
    }
    
    
	$MEMAdminDAO->dbclose();

	
}
else {
	$UTIL->checkFailType('2200','','','json');
	exit;
}

echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
