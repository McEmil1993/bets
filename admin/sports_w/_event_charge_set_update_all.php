<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$chargeEventData = $_POST['chargeEventData'];
//$bet_type = $_POST['bet_type'];

$chargeEventData = json_decode($chargeEventData, true);

if ($db_conn) {
    
    //$chargeEventData = $LSportsAdminDAO->real_escape_string($chargeEventData);
    //$bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    
    $sql = "";
    foreach ($chargeEventData as $key => $value) {
        $level = $value['level'];
        $bonus = $value['bonus'];
        $max_bonus = $value['max_bonus'];
        $pay_back_value = $value['pay_back_value'];
        $sql = "update charge_event set bonus = $bonus, max_bonus = $max_bonus, pay_back_value = $pay_back_value where level = $level;";
        $LSportsAdminDAO->executeQuery($sql);
    }
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = "이벤트 충전 변경 chargeEventData=>".$_POST['chargeEventData'];
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',35) ;";
    $LSportsAdminDAO->setQueryData($p_data);
    
    $LSportsAdminDAO->dbclose();

    $result["retCode"] = 1000;
    $result['retMsg'] = 'success';

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
