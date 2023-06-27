<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');
// 총판계정 전용 페이지
if (!isset($_SESSION)) {
    session_start();
}

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');


$COMMONDAO = new Admin_Common_DAO(_DB_NAME_WEB);
$db_conn_common = $COMMONDAO->dbconnect();

$UTIL = new CommonUtil();

if($db_conn_common) {
    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    // login check 
    $p_data['sql'] = "SELECT idx, nick_name as a_nick, session_key FROM member where u_business <> 1 and id='".$_SESSION['aid']."'";
    $db_dataLogin = $COMMONDAO->getQueryData($p_data);
    $db_session_key = $db_dataLogin[0]['session_key'];
    
    $nLogin = 0;
    if ($db_session_key == '') {
        $nLogin = 2;
        $result['retCode'] 	= 2002;
    }
    else if ($db_session_key != $_SESSION['akey']) {
        $nLogin = 1;
        $result['retCode'] 	= 2001;
    }
    //$UTIL->logWrite("[_now_call_check] [retCode] ". $nLogin, "error");
    if ($nLogin > 0) {
        $COMMONDAO->dbclose();
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $comm_now_date = date("Y-m-d");
    
    $p_data_comm['sql'] = " SELECT * FROM shop_config WHERE member_idx = ".$_SESSION['member_idx'];
    $db_data = $COMMONDAO->getQueryData($p_data_comm);
    $db_data = $db_data[0];
    $COMMONDAO->dbclose();
}

$result['retCode'] 		= 1000;
$result['bet_pre_s_fee'] 	= $db_data['bet_pre_s_fee'];
$result['bet_pre_d_fee'] 	= $db_data['bet_pre_d_fee'];
$result['bet_pre_d_2_fee'] 	= $db_data['bet_pre_d_2_fee'];
$result['bet_pre_d_3_fee'] 	= $db_data['bet_pre_d_3_fee'];
$result['bet_pre_d_4_fee'] 	= $db_data['bet_pre_d_4_fee'];
$result['bet_pre_d_5_more_fee'] 	= $db_data['bet_pre_d_5_more_fee'];
$result['bet_real_s_fee'] 	= $db_data['bet_real_s_fee'];
$result['bet_real_d_fee'] 	= $db_data['bet_real_d_fee'];
$result['bet_mini_fee'] 	= $db_data['bet_mini_fee'];
$result['pre_s_fee']            = $db_data['pre_s_fee'];
$result['bet_casino_fee'] 	= $db_data['bet_casino_fee'];
$result['bet_slot_fee'] 	= $db_data['bet_slot_fee'];
$result['bet_esports_fee'] 	= $db_data['bet_esports_fee'];
$result['bet_hash_fee'] 	= $db_data['bet_hash_fee'];

echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>