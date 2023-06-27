<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$p_data['msg_idx'] = trim(isset($_POST['setidx']) ? $_POST['setidx'] : 0);
$p_data['mtype'] = trim(isset($_POST['mtype']) ? $_POST['mtype'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$result['retCode'] = 2001;

if($db_conn) {
    $p_data['msg_idx'] = $MEMAdminDAO->real_escape_string($p_data['msg_idx']);
    $p_data['mtype'] = $MEMAdminDAO->real_escape_string($p_data['mtype']);
      
    if ($p_data['mtype']=='d') {
        $p_data['sql'] = "delete from t_message where idx = ".$p_data['msg_idx']." ";
        $MEMAdminDAO->setQueryData($p_data);
        $result['retCode'] = 1000;
    }
    else if ($p_data['mtype']=='a') {
        $p_data['sql'] = "delete from t_message where idx > 0 ";
        $MEMAdminDAO->setQueryData($p_data);
        $result['retCode'] = 1000;
    }
    else if ($p_data['mtype']=='r') {
        $p_data['sql'] = "delete from t_message where read_yn='Y' ";
        $MEMAdminDAO->setQueryData($p_data);
        $result['retCode'] = 1000;
    }
    else if ($p_data['mtype']=='week') {
        
        $del_date = date("Y-m-d", strtotime("-7 day", time()));
        
        $p_data['sql'] = "delete from t_message where DATE(reg_time) <= '$del_date'; ";
        $MEMAdminDAO->setQueryData($p_data);
        $result['retCode'] = 1000;
    }
    
    
    $MEMAdminDAO->dbclose();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);


?>