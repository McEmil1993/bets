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

$UTIL = new CommonUtil();

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$id = $_POST['id'];
$input_refund_rate = $_POST['input_refund_rate'];
$input_deduction_refund_rate = $_POST['input_deduction_refund_rate'];
$type = $_POST['type'];
$bet_type = $_POST['bet_type'];
$sports_id = isset($_POST['sports_id']) ? $_POST['sports_id'] : 0;
$league_id = isset($_POST['league_id']) ? $_POST['league_id'] : 0;

if($db_conn) {
    
    $id = $LSportsAdminDAO->real_escape_string($id);
    $input_refund_rate = $LSportsAdminDAO->real_escape_string($input_refund_rate);
    $input_deduction_refund_rate = $LSportsAdminDAO->real_escape_string($input_deduction_refund_rate);
    $type = $LSportsAdminDAO->real_escape_string($type);
    $bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    $sports_id = $LSportsAdminDAO->real_escape_string($sports_id);
    $league_id = $LSportsAdminDAO->real_escape_string($league_id);
    
    if(1 == $type){
        $sql = "update lsports_sports set input_refund_rate = $input_refund_rate, deduction_refund_rate = $input_deduction_refund_rate where id = $id and bet_type = $bet_type";
    }else if(2 == $type){
        $sql = "update lsports_leagues set input_refund_rate = $input_refund_rate, deduction_refund_rate = $input_deduction_refund_rate where id = $id and bet_type = $bet_type";
    }else{
        $sql = "INSERT INTO lsports_refund_rate_market(sports_id, league_id, market_id, bet_type, refund_rate, deduction_refund_rate, is_margin_refund) "
                . "VALUES ($sports_id, $league_id, $id, $bet_type, $input_refund_rate, $input_deduction_refund_rate, 0) "
                . "ON DUPLICATE KEY UPDATE refund_rate = $input_refund_rate, deduction_refund_rate = $input_deduction_refund_rate";
    }
    $UTIL->logWrite($sql);
    $LSportsAdminDAO->executeQuery($sql);
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = "환수율 설정 수정 id=>$id bet_type=>$bet_type type=>$type league_id=>$league_id input_refund_rate=>$input_refund_rate input_deduction_refund_rate=>$input_deduction_refund_rate";
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',31) ;";
    $LSportsAdminDAO->setQueryData($p_data);
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $sql;

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
