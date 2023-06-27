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
$p_data['puse'] = trim(isset($_POST['puse']) ? $_POST['puse'] : '');
$p_data['aid'] = trim(isset($_POST['aid']) ? $_POST['aid'] : '');
$p_data['apw'] = trim(isset($_POST['apw']) ? $_POST['apw'] : '');
$p_data['anick'] = trim(isset($_POST['anick']) ? $_POST['anick'] : '');
$p_data['amemo'] = trim(isset($_POST['amemo']) ? $_POST['amemo'] : '');

if ( ($p_data['ptype'] == 'edit')  ) {
    if ( ($p_data['puse'] == '') || ($p_data['aid'] == '') ) {
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
else if ( ($p_data['ptype'] == 'reg')  ) {
    if ( ($p_data['aid'] == '') || ($p_data['apw'] == '') || ($p_data['anick'] == '') ) {
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
    
    if ($p_data['ptype'] == 'edit') {
        
        $p_data['sql'] = "update t_adm_user set a_status='".$p_data['puse']."' ";
        if ($p_data['puse'] == 'Y') {
            $p_data['sql'] .= ", a_reg_id='".$admin_id."' ";
        }
        else if ($p_data['puse'] == 'N') {
            $p_data['sql'] .= ", a_del_id='".$admin_id."' ";
        }
        
        $p_data['sql'] .= " where a_id='".$p_data['aid']."' ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $log_data = $p_data['aid']." 사용변경  : [".$p_data['puse']."]";
        
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, log_data, log_type) ";
        $p_data['sql'] .= " values('".$admin_id."', '".$log_data."', 11) ";
        
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"] = 1000;
        $result["retData"] = '';
        
    }
    else if ($p_data['ptype'] == 'reg') {
        
        $p_data['sql'] = "select count(*) as cnt from t_adm_user where a_id='".$p_data['u_id']."' ";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        if ($retData[0]['cnt'] < 1) {
            $enc_pw = password_hash( $p_data['apw'], PASSWORD_DEFAULT);
            
            $p_data['sql'] = "insert into  t_adm_user ";
            $p_data['sql'] .= " (a_id, a_pw, a_nick, a_memo, a_reg_id) ";
            $p_data['sql'] .= " values('".$p_data['aid']."', '".$enc_pw."','".$p_data['anick']."','".$p_data['amemo']."','".$admin_id."') ";
            
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = $p_data['aip']." 등록";
            
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (aid, log_data, log_type) ";
            $p_data['sql'] .= " values('".$admin_id."', '".$log_data."', 11) ";
            
            $MEMAdminDAO->setQueryData($p_data);
            
            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
        
    }
    
    
    $MEMAdminDAO->dbclose();
}


echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
