<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$result["retCode"] = 2001;
$result["retData"] = '';

$p_data['ptype'] = trim(isset($_POST['ptype']) ? $_POST['ptype'] : '');
$p_data['idx'] = trim(isset($_POST['idx']) ? $_POST['idx'] : 0);
$p_data['aip'] = trim(isset($_POST['aip']) ? $_POST['aip'] : '');
$p_data['amemo'] = trim(isset($_POST['amemo']) ? $_POST['amemo'] : '');

if ( ($p_data['ptype'] == 'reg') || ($p_data['ptype'] == 'block_reg') ) {
    if ( ($p_data['aip'] == '') || ($p_data['amemo'] == '') ) {
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
else if ( ($p_data['ptype'] == 'del') || ($p_data['ptype'] == 'block_del') ) {
    if ($p_data['idx'] < 1) {
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
else {
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
    exit;
}


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    $now_ip = CommonUtil::get_client_ip();
    if('14.52.211.218' == $now_ip)
        $now_ip = '103.1.251.57';
    
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $MEMAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    if ($p_data['ptype'] == 'reg') {
        
        $p_data['sql'] = "insert into  t_adm_ip ";
        $p_data['sql'] .= " (a_ip, a_memo) ";
        $p_data['sql'] .= " values('".$p_data['aip']."', '".$p_data['amemo']."') ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $log_data = $p_data['aip']." 등록";
        
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
        $p_data['sql'] .= " values('".$admin_id."', '$now_ip', '$st_country', '".$log_data."', 2) ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"] = 1000;
        $result["retData"] = '';
        
    }
    else if ($p_data['ptype'] == 'del') {
        
        $p_data['sql'] = "select a_ip FROM t_adm_ip ";
        $p_data['sql'] .= " where idx=".$p_data['idx']." ";
        
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $db_ip = $retData[0]['a_ip'];
        $log_data = "$db_ip  삭제";
        
        
        $p_data['sql'] = "delete FROM t_adm_ip ";
        $p_data['sql'] .= " where idx=".$p_data['idx']." ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
        $p_data['sql'] .= " values('".$admin_id."', '$now_ip', '$st_country', '".$log_data."', 2) ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"] = 1000;
        $result["retData"] = '';
        
    }
    else if ($p_data['ptype'] == 'block_reg') {
        
        $p_data['sql'] = " SELECT FN_GET_IP_COUNTRY('172.30.0.2') as ip_country ;";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $db_ip_country = $retData[0]['ip_country'];
        
        $p_data['sql'] = "insert into  member_ip_block_history ";
        $p_data['sql'] .= " (admin_id, ip, country, memo) ";
        $p_data['sql'] .= " values('".$admin_id."','".$p_data['aip']."', '".$db_ip_country."', '".$p_data['amemo']."') ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $log_data = $p_data['aip']." 등록";
        
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
        $p_data['sql'] .= " values('".$admin_id."', '$now_ip', '$st_country', '".$log_data."', 2) ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"] = 1000;
        $result["retData"] = '';
        
    }
    else if ($p_data['ptype'] == 'block_del') {
        
        $p_data['sql'] = "select ip FROM member_ip_block_history ";
        $p_data['sql'] .= " where idx=".$p_data['idx']." ";
        
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $db_ip = $retData[0]['a_ip'];
        $log_data = "$db_ip 개별 차단 관리 IP 삭제";
        
        
        $p_data['sql'] = "delete FROM member_ip_block_history ";
        $p_data['sql'] .= " where idx=".$p_data['idx']." ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
        $p_data['sql'] .= " values('".$admin_id."', '$now_ip', '$st_country', '".$log_data."', 2) ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"] = 1000;
        $result["retData"] = '';
        
    }
    
    $MEMAdminDAO->dbclose();
}


echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
