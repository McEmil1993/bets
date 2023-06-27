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

$betData = $_POST['betData'];
$pb_end_time = $_POST['pb_end_time'];
$pladder_end_time = $_POST['pladder_end_time'];
$kladder_end_time = $_POST['kladder_end_time'];

$betData = json_decode($betData, true);
if($db_conn) {
    $sql = "";
    foreach ($betData as $key => $value) {
        $update_markets_id = '';
        if($value['markets_id'] == 10001)
            $update_markets_id = '10001,10002';
        else if($value['markets_id'] == 10003)
            $update_markets_id = '10003,10004';
        else if($value['markets_id'] == 11001)
            $update_markets_id = '11001,11002';
        else if($value['markets_id'] == 11003)
            $update_markets_id = '11003,11004';
        else if($value['markets_id'] == 11005)
            $update_markets_id = '11005,11006';
        else if($value['markets_id'] == 12001)
            $update_markets_id = '12001,12002';
        else if($value['markets_id'] == 12003)
            $update_markets_id = '12003,12004';
        else if($value['markets_id'] == 12005)
            $update_markets_id = '12005,12006';
        else if($value['markets_id'] == 14001)
            $update_markets_id = '14001,14002';
        else if($value['markets_id'] == 14003)
            $update_markets_id = '14003,14004';
        else
            $update_markets_id = $value['markets_id'];
        
        $sql = "update mini_game_bet set bet_price = ".$value['bet_price']." where markets_id in (".$update_markets_id.");";
        $LSportsAdminDAO->executeQuery($sql);
    }
    
    $sql = "update t_game_config set set_type_val = $pb_end_time where u_level = 0 and set_type = 'mini_powerball_deadline'";
        $LSportsAdminDAO->executeQuery($sql);
        
    $sql = "update t_game_config set set_type_val = $pladder_end_time where u_level = 0 and set_type = 'mini_power_ladder_deadline'";
    $LSportsAdminDAO->executeQuery($sql);

    $sql = "update t_game_config set set_type_val = $kladder_end_time where u_level = 0 and set_type = 'mini_kino_ladder_deadline'";
    $LSportsAdminDAO->executeQuery($sql);
    
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= SUCCESS;
    $result['retMsg']	= $sql;

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
