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
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['p_setkind'] = trim(isset($_POST['p_setkind']) ? $_POST['p_setkind'] : '');
$p_data['p_change'] = trim(isset($_POST['p_change']) ? $_POST['p_change'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$result["retCode"] = 2001;
$result["retData"] = '';


if($db_conn) {
    
    $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
    $p_data['p_setkind'] = $MEMAdminDAO->real_escape_string($p_data['p_setkind']);
    $p_data['p_change'] = $MEMAdminDAO->real_escape_string($p_data['p_change']);
    
    $p_data['sql'] = "select is_monitor, is_monitor_charge, is_monitor_security, is_monitor_bet ";
    $p_data['sql'] .= " from member where idx=".$p_data['m_idx']." ";
    $db_data_mem = $MEMAdminDAO->getQueryData($p_data);
    
    $up_sql_data = $up_change_str = $up_change_val = '';
    
    switch ($p_data['p_setkind']) {
        case "normal":
            $up_sql_data = " is_monitor='".$p_data['p_change']."' ";
            $up_change_str = "일반모니터링";
            $up_change_val = $db_data_mem[0]['is_monitor'];
            break;
        case "charge":
            $up_sql_data = " is_monitor_charge='".$p_data['p_change']."' ";
            $up_change_str = "충전모니터링";
            $up_change_val = $db_data_mem[0]['is_monitor_charge'];
            break;
        case "security":
            $up_sql_data = " is_monitor_security='".$p_data['p_change']."' ";
            $up_change_str = "보안모니터링";
            $up_change_val = $db_data_mem[0]['is_monitor_security'];
            break;
        case "bet":
            $up_sql_data = " is_monitor_bet='".$p_data['p_change']."' ";
            $up_change_str = "중복베팅모니터링";
            $up_change_val = $db_data_mem[0]['is_monitor_bet'];
            break;
    }
    
    if ($up_sql_data != '') {
        $p_data['sql'] = " update  member set ";
        $p_data['sql'] .= $up_sql_data;
        $p_data['sql'] .= " where idx=".$p_data['m_idx']." ";
        $MEMAdminDAO->setQueryData($p_data);
        
        // log
        $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
        $p_data['sql'] .= " values (".$p_data['m_idx'].",'$up_change_str','$up_change_val','".$p_data['p_change']."', '$admin_id') ";
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"]	= 1000;
        //$result["retData"]	= $p_data['randval'];
    }
    
    
	$MEMAdminDAO->dbclose();
}


echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
