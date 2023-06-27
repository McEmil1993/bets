<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');


$p_data['ip_idx'] = trim(isset($_POST['setidx']) ? $_POST['setidx'] : 0);
$p_data['mtype'] = trim(isset($_POST['mtype']) ? $_POST['mtype'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$result['retCode'] = 2001;

if($db_conn) {
    
    $p_data['ip_idx'] = $MEMAdminDAO->real_escape_string($p_data['ip_idx']);
    $p_data['mtype'] = $MEMAdminDAO->real_escape_string($p_data['mtype']);
     
    if ($p_data['mtype']=='f') {
        $p_data['sql'] = "delete from member_ip_block_history where idx = ".$p_data['ip_idx']." ";
        $MEMAdminDAO->setQueryData($p_data);
        $result['retCode'] = 1000;
        
        $log_str = "[_set_ipblock] [".$p_data['sql']."]";
        //$UTIL->logWrite($log_str,"pop_memo");
        
    }
    else if ($p_data['mtype']=='a') {
        $p_data['sql'] = "delete from member_ip_block_history where idx > 0 ";
        $MEMAdminDAO->setQueryData($p_data);
        $result['retCode'] = 1000;
        
        $log_str = "[_set_ipblock] [".$p_data['sql']."]";
        //$UTIL->logWrite($log_str,"pop_memo");
    }
    else if ($p_data['mtype']=='b') {
        
        $p_data['sql'] = "SELECT idx, member_idx, ip, country FROM member_login_history where idx=".$p_data['ip_idx']." ";
        $db_dataLoginlog = $MEMAdminDAO->getQueryData($p_data);
        
        $log_str = "[_set_ipblock] [".$p_data['sql']."]";
        //$UTIL->logWrite($log_str,"pop_memo");
        
        $db_loginlog_idx = $db_dataLoginlog[0]['idx'];
        $db_loginlog_member_idx = $db_dataLoginlog[0]['member_idx'];
        $db_loginlog_ip = $db_dataLoginlog[0]['ip'];
        $db_loginlog_country = $db_dataLoginlog[0]['country'];
        
        
        $p_data['sql'] = "SELECT count(*) as cnt FROM member_ip_block_history where ip='".$db_loginlog_ip."' ";
        $db_dataIPBlockCnt = $MEMAdminDAO->getQueryData($p_data);
        
        if ($db_dataIPBlockCnt[0]['CNT'] < 1) {
            $p_data['sql'] = " insert into  member_ip_block_history (member_idx, login_history_idx, ip, country, memo) ";
            $p_data['sql'] .= " values ($db_loginlog_member_idx, $db_loginlog_idx, '$db_loginlog_ip', '$db_loginlog_country','관리자 차단') ";
            $MEMAdminDAO->setQueryData($p_data);
            $result['retCode'] = 1000;
            
            $log_str = "[_set_ipblock] [".$p_data['sql']."]";
            //$UTIL->logWrite($log_str,"pop_memo");
        }
        
        
    }
    
    
    $MEMAdminDAO->dbclose();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);


?>