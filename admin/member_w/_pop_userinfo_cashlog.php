<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Cash_dao.php');


$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);


$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if($db_conn) {
    
    $p_data['m_idx'] = $CASHAdminDAO->real_escape_string($p_data['m_idx']);
    
    $p_data['sql'] = " SELECT ac_code, r_money, af_r_money, m_kind,point,g_money,coment, reg_time, 0 as `TYPE`, 0 as BET_MNY, 0 as RSLT_MNY, 0 as HLD_MNY, 0 as PRD_ID FROM t_log_cash ";
    //$p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']." AND ac_code IN (1,2,3,4,5,6,7,8,9,10,101,102,103,111,112,113,114,121,122,123,124,125,201,202,203,204,205,302,303,998,999) AND r_money <> 0 order by idx desc ";
    $p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']." AND ac_code IN   (1,2,3,4,7,8,9,101,102,103,111,112,113,114,121,122,125,201,204,205,302,303,506,507,508,998,999) AND r_money <> 0";
    
    $p_data['sql'] .= " UNION ALL";
    $p_data['sql'] .= " SELECT 1000 as ac_code, 0 as r_money, 0 as af_r_money, 'P' as m_kind, 0 as point, 0 as g_money, '' as coment, REG_DTM as reg_time,`TYPE`, BET_MNY, RSLT_MNY, HLD_MNY, PRD_ID FROM KP_CSN_BET_HIST ";
    $p_data['sql'] .= " WHERE MBR_IDX = ".$p_data['m_idx'];
    
    $p_data['sql'] .= " UNION ALL";
    $p_data['sql'] .= " SELECT 1001 as ac_code, 0 as r_money, 0 as af_r_money, 'P' as m_kind, 0 as point, 0 as g_money, '' as coment, REG_DTM as reg_time,`TYPE`, BET_MNY, RSLT_MNY, HLD_MNY, PRD_ID FROM KP_SLOT_BET_HIST ";
    $p_data['sql'] .= " WHERE MBR_IDX = ".$p_data['m_idx']." order by reg_time desc ";
    $p_data['sql'] .= " limit 100";
    
    //$log_str = "[_pop_userinfo_cashlog] [".$p_data['sql']."]";
    //$UTIL->logWrite($log_str,"pop_memo");
    
    $db_dataCashList = $CASHAdminDAO->getQueryData($p_data);
    if (is_null($db_dataCashList)) { 
        $db_cashlist_cnt = 0;
    } else {
        $db_cashlist_cnt = count($db_dataCashList);
    } 
    //$log_str = "[_pop_userinfo_cashlog] [check 2] [".$db_cashlist_cnt."]";
    //$UTIL->logWrite($log_str,"pop_memo");

    // prd list
    $p_data['sql'] = "select PRD_ID, PRD_NM from KP_PRD_INF";
    $result_prd = $CASHAdminDAO->getQueryData($p_data);
    
    $prdList = array();
    $tmList = array();
    if(count($result_prd) > 0){
        foreach ($result_prd as $key => $value) {
            $prdList[$value['PRD_ID']] = $value['PRD_NM'];
            $tmList[] = $value['PRD_ID'];
        }
    }
    
    $CASHAdminDAO->dbclose();
}

$data_str = "";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$data_str_2 = "
<table class='mlist scroll_mlist'>
<tr>
<td style='width: 55px;background-color:#6F6F6F;color:#fff'>번호</td>
<td style='width: 70px;background-color:#6F6F6F;color:#fff'>사용 머니</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>보유 머니</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>포인트</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>지머니</td>
<td style='background-color:#6F6F6F;color:#fff'>내용</td>
<td style='width: 100px;background-color:#6F6F6F;color:#fff'>등록일시</td>
</tr>
</table>
<div class='tline scroll_tline' style='max-height: 460px;'>
<table class='mlist scroll_mlist'>";

if($db_cashlist_cnt > 0) {
    for ($i=0;$i<$db_cashlist_cnt;$i++)
    {
        $cash_type_color = '';
        
        if($db_dataCashList[$i]['m_kind'] == 'P') {
            $cash_type_color = 'color:#FD0000;';
            $db_cash = number_format($db_dataCashList[$i]['r_money']);
        }
        else if($db_dataCashList[$i]['m_kind'] == 'M') {
            $cash_type_color = 'color:#0036FD;';
            $db_cash = number_format(-$db_dataCashList[$i]['r_money']);
        }
        else {
            $cash_type_color = '';
            $db_cash = number_format($db_dataCashList[$i]['r_money']);
        
        }
        
        $point = number_format($db_dataCashList[$i]['point']);
        $g_money = number_format($db_dataCashList[$i]['g_money']);
        $db_af_r_money = number_format($db_dataCashList[$i]['af_r_money']);
        $db_coment = $db_dataCashList[$i]['coment'];
        $db_reg_date = $db_dataCashList[$i]['reg_time'];
        
        if(1000 == $db_dataCashList[$i]['ac_code'] || 1001 == $db_dataCashList[$i]['ac_code']){
            if(1000 == $db_dataCashList[$i]['ac_code']){
                $gameName = '카지노 '.$prdList[$db_dataCashList[$i]['PRD_ID']];
            }else{
                $gameName = '슬롯 '.$prdList[$db_dataCashList[$i]['PRD_ID']];
            }
            // 사용머니
            //$db_cash = number_format($db_dataCashList[$i]['BET_MNY']);
            $db_cash = $db_dataCashList[$i]['RSLT_MNY'] + $db_dataCashList[$i]['BET_MNY'];

            // 이전머니
            //$be_r_money = $db_dataCashList[$i]['HLD_MNY'];
            $db_af_r_money = number_format($db_dataCashList[$i]['HLD_MNY'] + $db_dataCashList[$i]['RSLT_MNY']);

            $db_coment = $gameName.' 배팅';
            if($db_dataCashList[$i]['TYPE'] == 'W'){
                $db_cash = number_format($db_dataCashList[$i]['RSLT_MNY'] + $db_dataCashList[$i]['BET_MNY']);
                //$be_r_money = $db_dataCashList[$i]['HLD_MNY'] - ($db_dataCashList[$i]['RSLT_MNY'] + $db_dataCashList[$i]['BET_MNY']);
                $db_coment = $gameName.' 적중';
            }else if($db_dataCashList[$i]['TYPE'] == 'L'){
                //$be_r_money = $db_dataCashList[$i]['HLD_MNY'] + $db_dataCashList[$i]['BET_MNY'];
                $db_coment = $gameName.' 낙첨';
            }else if($db_dataCashList[$i]['TYPE'] == 'C'){
                $db_coment = $gameName.' 취소';
            }else if('I' == $db_dataCashList[$i]['TYPE']){
                $db_coment = $gameName.' 인게임보너스';
            }else if('P' == $db_dataCashList[$i]['TYPE']){
                $db_coment = $gameName.' 프로모션보너스';
            }else if('J' == $db_dataCashList[$i]['TYPE']){
                $db_coment = $gameName.' 잭팟보너스';
            }
            $db_coment .= '(배팅금 : '.$db_dataCashList[$i]['BET_MNY'].')';
        }
        
        
        $data_str_2 .="<tr><td style='width: 55px;'>".($db_cashlist_cnt-$i)."</td>";
        $data_str_2 .="<td style='width: 70px;text-align:right;font-size: 0.75em;$cash_type_color'>$db_cash</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$db_af_r_money</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$point</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$g_money</td>";
        $data_str_2 .="<td style='text-align:left;font-size: 0.75em;'>$db_coment</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$db_reg_date</td></tr>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table></div>";

$result['retData_2'] = $data_str_2;

//$log_str = "[_pop_userinfo_cashlog] [".$result['retData']."]";
//$UTIL->logWrite($log_str,"pop_memo");

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>