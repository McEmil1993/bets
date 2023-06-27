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

include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$p_data['second_pass'] = trim(isset($_POST['second_pass']) ? $_POST['second_pass'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    //2차인증 체크
    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    $second_pass = $MEMAdminDAO->getQueryData($p_data)[0];
    if(hash('sha512', $p_data['second_pass']) != $second_pass['set_type_val']){
        $MEMAdminDAO->dbclose();
        $result['retCode'] = 2002;
        $result['retMsg']  = '2차인증 비번이 틀립니다.';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
}
$MEMAdminDAO->dbclose();

$_SESSION['user_detail_check_time'] = date('Y-m-d H:i:s');
$result["retCode"]	= SUCCESS;
$result['retMsg']	= 'success';
echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>