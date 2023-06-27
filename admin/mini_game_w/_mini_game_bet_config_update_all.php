<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');
include_once(_BASEPATH . '/common/login_check.php');

if (!isset($_SESSION)) {
    session_start();
}
$admin_id = $_SESSION['aid'];

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$betConfigData = $_POST['betConfigData'];

$betConfigData = json_decode($betConfigData, true);

if($db_conn) {
    $sql = "";
    foreach ($betConfigData as $key => $value) {
        // EOS 파워볼
        $sql = "update mini_game_bet_config set min = ".$value['eospb5_min'].", max = ".$value['eospb5_max'].
                    ", `limit` = ".$value['eospb5_limit'].", reward = ".$value['eospb5_reward']." where bet_type = 3 and level = ".$value['level'].";";
        $LSportsAdminDAO->executeQuery($sql);
        
        // 파워볼
        $sql = "update mini_game_bet_config set min = ".$value['powerball_min'].", max = ".$value['powerball_max'].
                    ", `limit` = ".$value['powerball_limit'].", reward = ".$value['powerball_reward']." where bet_type = 15 and level = ".$value['level'].";";
        $LSportsAdminDAO->executeQuery($sql);

        // 파워사다리
        $sql = "update mini_game_bet_config set min = ".$value['pladder_min'].", max = ".$value['pladder_max'].
                    ", `limit` = ".$value['pladder_limit'].", reward = ".$value['pladder_reward']." where bet_type = 4 and level = ".$value['level'].";";
        $LSportsAdminDAO->executeQuery($sql);
        
        // 키노사다리
        $sql = "update mini_game_bet_config set min = ".$value['kladder_min'].", max = ".$value['kladder_max'].
                    ", `limit` = ".$value['kladder_limit'].", reward = ".$value['kladder_reward']." where bet_type = 5 and level = ".$value['level'].";";
        $LSportsAdminDAO->executeQuery($sql);
        
        // 가상축구
        $sql = "update mini_game_bet_config set min = ".$value['b_soccer_min'].", max = ".$value['b_soccer_max'].
                    ", `limit` = ".$value['b_soccer_limit'].", reward = ".$value['b_soccer_reward']." where bet_type = 6 and level = ".$value['level'].";";
        $LSportsAdminDAO->executeQuery($sql);
    }
    
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $log_data = "[미니게임 설정] betConfigData=".$_POST['betConfigData'];
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$log_data', 54) ;";
    $LSportsAdminDAO->setQueryData($p_data);
    
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>
