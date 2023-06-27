<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$idx = $_POST['idx'];
$bet_group_id = $_POST['gid'];
$name = $_POST['name'];
$limitBetPrice = $_POST['limit_bet_price'];
$maxBetPrice = $_POST['max_bet_price'];

if($db_conn) {
    $idx = $LSportsAdminDAO->real_escape_string($idx);
    $bet_group_id = $LSportsAdminDAO->real_escape_string($bet_group_id);
    $name = $LSportsAdminDAO->real_escape_string($name);
    $limitBetPrice = $LSportsAdminDAO->real_escape_string($limitBetPrice);
    $maxBetPrice = $LSportsAdminDAO->real_escape_string($maxBetPrice);
     
    $sql = "update lsports_markets set display_name = '$name', limit_bet_price = $limitBetPrice, max_bet_price = $maxBetPrice where idx = $idx and bet_group = $bet_group_id";
    $LSportsAdminDAO->executeQuery($sql);
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = "배팅타입 관리 idx=>$idx name=>$name bet_group_id=>$bet_group_id limitBetPrice=>$limitBetPrice maxBetPrice=>$maxBetPrice";
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',31) ;";
    $LSportsAdminDAO->setQueryData($p_data);
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg'] = 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
