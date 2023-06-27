<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['p_setkind'] = trim(isset($_POST['p_setkind']) ? $_POST['p_setkind'] : '');
$p_data['p_change'] = trim(isset($_POST['p_change']) ? $_POST['p_change'] : '');
$p_data['name'] = trim(isset($_POST['name']) ? $_POST['name'] : '');
$p_data['name'].=$p_data['name'].'_'.$p_data['p_change'];
$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$result["retCode"] = 2001;
$result["retData"] = '';

$up_change_val = 'ON';
if('ON' == $p_data['p_change'] ){
    $up_change_val = 'OFF';
}
if($db_conn) {
        
        $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
        $p_data['p_setkind'] = $MEMAdminDAO->real_escape_string($p_data['p_setkind']);
        $p_data['p_change'] = $MEMAdminDAO->real_escape_string($p_data['p_change']); 
        $p_data['name'] = $MEMAdminDAO->real_escape_string($p_data['name']); 
        
        
        
        $p_data['sql'] = " update  member_game_type set status = '".$p_data['p_change']."' ";
        $p_data['sql'] .= " where member_idx =".$p_data['m_idx']." AND game_type = ".$p_data['p_setkind']." ";
        $MEMAdminDAO->setQueryData($p_data);
       
        CommonUtil::logWrite("_set_game_type : " . $p_data['sql'], "info");
         
        // log
        $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
        $p_data['sql'] .= " values (".$p_data['m_idx'].",'".$p_data['name']."','$up_change_val','".$p_data['p_change']."','$admin_id') ";
        $MEMAdminDAO->setQueryData($p_data);
        
       
        
        $result["retCode"]	= 1000;
       
	$MEMAdminDAO->dbclose();
}


echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
