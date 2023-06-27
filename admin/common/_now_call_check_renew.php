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
    // 총판계정 접속시
    $srch_basic = '';
    if($_SESSION['u_business'] == 0){
        $p_data['sql'] = " SELECT a_nick, session_key FROM t_adm_user ";
        $p_data['sql'] .= " WHERE a_id = '" . $_SESSION['aid'] . "' ";
    }else{
        $p_data['sql'] = " SELECT nick_name as a_nick, session_key FROM member ";
        $p_data['sql'] .= " WHERE id = '" . $_SESSION['aid'] . "' ";
        $srch_basic = " AND member.recommend_member = ".$_SESSION['member_idx'];
    }

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
   
    

    $tot_money_ch_cnt_1 = $tot_money_ch_cnt_2 = $tot_money_ex_cnt_1 = $tot_money_ex_cnt_2 = 0;
    $today_tot_money_ch_3 = $today_tot_money_ex_3 = 0;

    $today_tot_mem_leave = $today_tot_mem_reg = $today_tot_mem_wait = 0;
    $tot_qna_cnt_1 = $tot_qna_cnt_2 = 0;
    $tot_qna_cnt_sound = 0;
    $tot_sms_cnt = 0;
    $tot_charge_cnt = 0;
    $tot_exchange_cnt = 0;
    $tot_mem_cnt_sound = 0;
    
    $p_data_comm['sql'] = " SELECT COUNT(*) AS cnt FROM member WHERE idx > 0";
    $p_data_comm['sql'] .= $srch_basic;
    $db_dataMemCnt = $COMMONDAO->getQueryData($p_data_comm);
    $tot_mem_cnt = $db_dataMemCnt[0]['cnt'];

    $p_data_ch['sql'] = " SELECT mc.status,mc.money,mc.is_sound FROM member_money_charge_history as mc ";
    $p_data_ch['sql'] .= " LEFT JOIN member on member.idx = member_idx ";
    $p_data_ch['sql'] .= $srch_basic;
    $p_data_ch['sql'] .= " WHERE update_dt >= '" . $db_srch_s_date . "' AND update_dt <= '" . $db_srch_e_date . "' AND mc.status IN (1,3) ";
    $db_dataCh = $COMMONDAO->getQueryData($p_data_ch);
    
    foreach ($db_dataCh as $value) {
        switch ($value['status']) {
            case 1:
                $tot_money_ch_cnt_1 = $tot_money_ch_cnt_1 + 1;
                if ('Y' == $value['is_sound']) {
                    $tot_charge_cnt = $tot_charge_cnt + 1;
                }

                break;

            case 3:
                $tot_money_ch_cnt_2 = $tot_money_ch_cnt_2 + 1;
                $today_tot_money_ch_3 = $today_tot_money_ch_3 + $value['money'];
                break;
        }
    }

    $p_data_ex['sql'] = " SELECT me.status,me.money,me.is_sound FROM member_money_exchange_history as me";
    $p_data_ch['sql'] .= " LEFT JOIN member on member.idx = member_idx ";
    $p_data_ch['sql'] .= $srch_basic;
    $p_data_ex['sql'] .= " WHERE update_dt >= '" . $db_srch_s_date . "' AND update_dt <= '" . $db_srch_e_date . "' AND me.status IN (1,3) ";
    $db_dataEx = $COMMONDAO->getQueryData($p_data_ex);

    foreach ($db_dataEx as $value) {
        switch ($value['status']) {
            case 1:
                $tot_money_ex_cnt_1 = $tot_money_ex_cnt_1 + 1;
                if ('Y' == $value['is_sound']) {
                    $tot_exchange_cnt = $tot_exchange_cnt + 1;
                }

                break;

            case 3:
                $tot_money_ex_cnt_2 = $tot_money_ex_cnt_2 + 1;
                $today_tot_money_ex_3 = $today_tot_money_ex_3 + $value['money'];
                break;
        }
    }
    
    $p_data_comm['sql'] = " SELECT * FROM day_now_call_check ";
    $db_dataCommon = $COMMONDAO->getQueryData($p_data_comm);
    //CommonUtil::logWrite(" _now_call_check_renew " . $p_data_comm['sql'], "info");
    
    foreach ($db_dataCommon as $value) {
        if('mem' == $value['stype'] && 1 == $value['status']){
            $today_tot_mem_reg = $value['cnt'];
        }else if('mem' == $value['stype'] && 11 == $value['status']){
            $today_tot_mem_wait = $value['cnt'];
        } else if('mem_leave' == $value['stype']){
            $today_tot_mem_leave = $value['cnt'];
        } else if('mem_sound' == $value['stype']){
            $tot_mem_cnt_sound = $value['cnt'];
        } else if ('sms_cnt' == $value['stype']) {
            $tot_sms_cnt = $value['cnt'];
        }
    }
    
    $p_data_qna['sql'] = " SELECT is_answer,is_view,is_sound  FROM menu_qna ";
    //$p_data_qna['sql'] .= " WHERE DATE(create_dt) = '" . $comm_now_date . "'";
    $p_data_qna['sql'] .= " WHERE create_dt >= '" . $db_srch_s_date . "' AND create_dt <= '" . $db_srch_e_date . "'";

   
    $db_dataQna = $COMMONDAO->getQueryData($p_data_qna);

    //CommonUtil::logWrite(" _now_call_check_renew " . $p_data_comm['sql'], "info");

  
    foreach ($db_dataQna as $row_comm) {
                
        // 상담 신청 갯수
        if ($row_comm['is_view'] == 'Y' && $row_comm['is_answer'] == 'N') { // is_view == 'Y' AND 'N' == is_answer
            
            $tot_qna_cnt_1 = $tot_qna_cnt_1 + 1;
            
        }
        // 상담 처리 갯수(당일)
        if ($row_comm['is_answer'] == 'Y') {
            $tot_qna_cnt_2 = $tot_qna_cnt_2 + 1;
        }
        // 상담 사운드 갯수
        if ($row_comm['is_sound'] == 'Y') {
            $tot_qna_cnt_sound = $tot_qna_cnt_sound + 1;
        }
     }

    $COMMONDAO->dbclose();
}

$result['retCode'] = 1000;
$result['charge_cnt_1'] = number_format($tot_money_ch_cnt_1);
$result['charge_cnt_2'] = number_format($tot_money_ch_cnt_2);
$result['exchange_cnt_1'] = number_format($tot_money_ex_cnt_1);
$result['exchange_cnt_2'] = number_format($tot_money_ex_cnt_2);
$result['tot_qna_cnt'] = number_format($tot_qna_cnt_1);
$result['tot_qna_cnt_2'] = number_format($tot_qna_cnt_2);
$result['tot_qna_cnt_sound'] = number_format($tot_qna_cnt_sound);
$result['tot_sms_cnt'] = $tot_sms_cnt;
$result['today_tot_money_ch'] = number_format($today_tot_money_ch_3);
$result['today_tot_money_ex'] = number_format($today_tot_money_ex_3);
$result['today_tot_mem_reg'] = $today_tot_mem_reg;
$result['today_tot_mem_leave'] = number_format($today_tot_mem_leave);
$result['today_tot_mem_wait'] = $today_tot_mem_wait;
$result['tot_mem_cnt'] = number_format($tot_mem_cnt);
$result['tot_mem_cnt_sound'] = number_format($tot_mem_cnt_sound);
$result['tot_charge_cnt'] = number_format($tot_charge_cnt);
$result['tot_exchange_cnt'] = number_format($tot_exchange_cnt);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
