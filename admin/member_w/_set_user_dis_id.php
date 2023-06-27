<?php 
if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['m_dis_id'] = trim(isset($_POST['m_dis_id']) ? $_POST['m_dis_id'] : '');

if ( ($p_data['m_idx'] == 0) || ($p_data['m_dis_id'] == '')) {
    $result['retCode'] = 2001;
}
else {
    $result['retCode'] = 1000;
    
    $retCash = 0;
    
    $MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
    $db_conn = $MEMAdminDAO->dbconnect();
    
    if($db_conn) {   
        $now_ip = CommonUtil::get_client_ip();
        $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $st_country = $retData[0]['a_country'];
        
        $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
        $p_data['m_dis_id'] = $MEMAdminDAO->real_escape_string($p_data['m_dis_id']);
        
        $p_data['sql'] = "select dis_id, id, nick_name from member where idx=".$p_data['m_idx']." ";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        
        $be_data = $retData[0]['dis_id'];
        $db_id = $retData[0]['id'];
        $db_nick_name = $retData[0]['nick_name'];
        $af_data = $p_data['m_dis_id'];
            
        $p_data['sql'] = "update member set dis_id = '$af_data' where idx=".$p_data['m_idx']." ";
        $MEMAdminDAO->setQueryData($p_data);
                
        $UTIL->logWrite($p_data['sql'], "error");

        $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
        $p_data['sql'] .= " values (".$p_data['m_idx'].",'가입총판라인','$be_data','$af_data', '$admin_id') ";
        $MEMAdminDAO->setQueryData($p_data);
        
        $log_data = "가입총판라인 변경 이전=>$be_data  이후=>".$af_data;
        $log_type = 56;

        // 어드민 히스토리 로그
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
        $MEMAdminDAO->setQueryData($p_data);
    }
    $MEMAdminDAO->dbclose();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>