<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION)) {
    session_start();
}

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');


$COMMONDAO = new Admin_Common_DAO(_DB_NAME_WEB);
$db_conn_common = $COMMONDAO->dbconnect();

$UTIL = new CommonUtil();

if ($db_conn_common) {
    // login check 
    $p_data['sql'] = " SELECT a_nick, session_key FROM t_adm_user ";
    $p_data['sql'] .= " WHERE a_id = '" . $_SESSION['aid'] . "' ";

    $db_dataLogin = $COMMONDAO->getQueryData($p_data);
    $db_session_key = $db_dataLogin[0]['session_key'];

    $nLogin = 0;

    if ($db_session_key == '') {
        $nLogin = 2;
        $result['retCode'] = 2002;
    } else if ($db_session_key != $_SESSION['akey']) {
        $nLogin = 1;
        $result['retCode'] = 2001;
    }

    if ($nLogin > 0) {
        $COMMONDAO->dbclose();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //$comm_now_date = date("Y-m-d");
    $db_srch_s_date = date("Y-m-d 00:00:00");
    $db_srch_e_date = date("Y-m-d 23:59:59");
      
    $today_tot_mem_reg = $today_tot_mem_wait = 0;
      
    $tot_mem_cnt_sound = 0;
  
      
    $p_data_comm['sql'] = " SELECT * FROM day_now_call_check ";
    $db_dataCommon = $COMMONDAO->getQueryData($p_data_comm);
    //CommonUtil::logWrite(" _now_call_check_renew " . $p_data_comm['sql'], "info");
    
    foreach ($db_dataCommon as $value) {
        if('mem' == $value['stype'] && 1 == $value['status']){
            $today_tot_mem_reg = $value['cnt'];
        }else if('mem' == $value['stype'] && 11 == $value['status']){
            $today_tot_mem_wait = $value['cnt'];
        } else if('mem_sound' == $value['stype']){
            $tot_mem_cnt_sound = $value['cnt'];
        } 
        
    }
    
    $COMMONDAO->dbclose();
}

$result['retCode'] = 1000;
$result['today_tot_mem_reg'] = $today_tot_mem_reg; //
$result['today_tot_mem_wait'] = $today_tot_mem_wait; //
$result['tot_mem_cnt_sound'] = number_format($tot_mem_cnt_sound);//

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
