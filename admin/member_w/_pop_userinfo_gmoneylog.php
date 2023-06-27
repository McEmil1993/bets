<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Cash_dao.php');


$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);


$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if($db_conn) {
    
    $p_data['m_idx'] = $CASHAdminDAO->real_escape_string($p_data['m_idx']);
    
    $p_data['sql'] = " SELECT ac_code, ac_idx, r_money, af_r_money, m_kind,point,coment, reg_time, 0 as `TYPE`, 0 as BET_MNY, 0 as RSLT_MNY, 0 as HLD_MNY, 0 as PRD_ID FROM t_log_cash ";
    $p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']." AND ac_code IN   (500,501,502,503,504,505,510,511) AND r_money <> 0 order by idx desc";
    
    //$log_str = "[_pop_userinfo_cashlog] [".$p_data['sql']."]";
    //$UTIL->logWrite($log_str,"pop_memo");
    
    $db_dataCashList = $CASHAdminDAO->getQueryData($p_data);
    if (is_null($db_dataCashList)) { 
        $db_cashlist_cnt = 0;
    } else {
        $db_cashlist_cnt = count($db_dataCashList);
    }
    
    $itemList = array();
    $p_data['sql'] = "SELECT id, name, value FROM item WHERE id > 0";
    $item = $CASHAdminDAO->getQueryData($p_data);
    foreach ($item as $key => $value) {
        $itemList[$value['id']] = $value;
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
<td style='width: 70px;background-color:#6F6F6F;color:#fff'>사용 지머니</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>보유 지머니</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>포인트</td>
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
        
        if(502 == $db_dataCashList[$i]['ac_code']){
            $db_dataCashList[$i]['r_money'] = 0;
            $db_dataCashList[$i]['coment'] .= '('.$itemList[$db_dataCashList[$i]['ac_idx']]['name'].')';
        }else if(503 == $db_dataCashList[$i]['ac_code']){
            $db_dataCashList[$i]['point'] = 0;
            $db_dataCashList[$i]['coment'] .= '('.$itemList[$db_dataCashList[$i]['ac_idx']]['name'].')';
        }else if(511 == $db_dataCashList[$i]['ac_code']){
            $db_dataCashList[$i]['r_money'] = 0;
            $db_dataCashList[$i]['coment'] .= '('.$itemList[$db_dataCashList[$i]['ac_idx']]['name'].')';
        }
        
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
        $db_af_r_money = number_format($db_dataCashList[$i]['af_r_money']);
        $db_coment = $db_dataCashList[$i]['coment'];
        $db_reg_date = $db_dataCashList[$i]['reg_time'];
        
        $data_str_2 .="<tr><td style='width: 55px;'>".($db_cashlist_cnt-$i)."</td>";
        $data_str_2 .="<td style='width: 70px;text-align:right;font-size: 0.75em;$cash_type_color'>$db_cash</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$db_af_r_money</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$point</td>";
        $data_str_2 .="<td style='text-align:left;font-size: 0.75em;'>$db_coment</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$db_reg_date</td></tr>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table></div>";

$result['retData_2'] = $data_str_2;

//$UTIL->logWrite($log_str,"pop_memo");

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>