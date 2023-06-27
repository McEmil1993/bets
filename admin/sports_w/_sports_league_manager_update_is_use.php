<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$isUse = $_POST['isUse'];
$chkval = $_POST['chkval'];
$bet_type = $_POST['bet_type'];

$leaguesData = json_decode($leaguesData, true);

if($db_conn) {
    
    $isUse = $LSportsAdminDAO->real_escape_string($isUse);
    $chkval = $LSportsAdminDAO->real_escape_string($chkval);
    $bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    
    $sql = "update lsports_leagues set is_use = $isUse where id in ($chkval) and bet_type = $bet_type;";
  
    $LSportsAdminDAO->executeQuery($sql);
    
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
