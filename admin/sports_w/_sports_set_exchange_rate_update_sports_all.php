<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$sportsData = $_POST['sportsData'];
$type = $_POST['type'];



if($db_conn) {
   
    $sportsData = json_decode($sportsData, true);
    
    $type= $LSportsAdminDAO->real_escape_string($type);
    
    $sql = "";
    foreach ($sportsData as $key => $value) {
        $value['update_rate'] = true === isset($value['update_rate']) && false === empty($value['update_rate']) ? $value['update_rate'] : 0;
        if(1 == $type){
            $sql = "update lsports_sports set input_refund_rate = ".$value['update_rate'].", deduction_refund_rate = ".$value['update_deduction_rate']." where id = ".$value['id']." and bet_type = ".$value['bet_type'].";";
        }else if(2 == $type){
            $sql = "update lsports_leagues set input_refund_rate = ".$value['update_rate'].", deduction_refund_rate = ".$value['update_deduction_rate']." where id = ".$value['id']." and bet_type = ".$value['bet_type'].";";
        }else{
            $sports_id = $value['sports_id'];
            $league_id = $value['league_id'];
            $id = $value['id'];
            $bet_type = $value['bet_type'];
            $input_refund_rate = $value['update_rate'];
            $input_deduction_refund_rate = $value['update_deduction_rate'];
            //$sql = "update lsports_markets set input_refund_rate = ".$value['update_rate'].", deduction_refund_rate = ".$value['update_deduction_rate']." where idx = ".$value['id'].";";
            $sql = "INSERT INTO lsports_refund_rate_market(sports_id, league_id, market_id, bet_type, refund_rate, deduction_refund_rate, is_margin_refund) "
                    . "VALUES ($sports_id, $league_id, $id, $bet_type, $input_refund_rate, $input_deduction_refund_rate, 0) "
                    . "ON DUPLICATE KEY UPDATE refund_rate = $input_refund_rate, deduction_refund_rate = $input_deduction_refund_rate";
        }
        $LSportsAdminDAO->executeQuery($sql);
    }
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = "환수율 설정 전체수정 type=$type sportsData=>".$_POST['sportsData'];
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',31) ;";
    $LSportsAdminDAO->setQueryData($p_data);
    
    // idx값 때문에 무조건 추가로 간다.
    /*$sql = array();
    foreach ($sportsData as $key => $item) {
        $insertSql = '('
                . $item['id'] . ', "'
                . $item['name'] . '", '
                . $item['update_rate'] . ', "'
                . 'ko")';
        array_push($sql, $insertSql);
    }
 
    if (count($sql) > 0) {
        $LSportsAdminDAO->executeQuery(
            'INSERT INTO `lsports_sports` ('
            . 'id, '
            . 'name, '
            . 'input_refund_rate, '
            . 'lang) VALUES'
            . implode(',', $sql)
            . ' ON DUPLICATE KEY UPDATE '
            . 'id = VALUES(id), '
            . 'name = VALUES(name), '
            . 'input_refund_rate = VALUES(input_refund_rate)'
        );
    }*/

    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= $p_data['sql'];

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
