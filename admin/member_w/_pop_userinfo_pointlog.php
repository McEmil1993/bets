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
    
    $p_data['sql'] = " SELECT ac_code, point, af_point, r_money, af_r_money, m_kind, coment, reg_time FROM t_log_cash ";
    $p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']." AND ((ac_code IN (5,6,10,11,123,124,131,202,203,301,".USER_PAY_BACK_REWARD_POINT.",".RECOMMENDER_PAY_BACK_REWARD_POINT.",".USER_BET_BACK_REWARD_POINT.",".RECOMMENDER_BET_BACK_REWARD_POINT.",".USER_BET_LOSE_BACK_REWARD_POINT.",".RECOMMENDER_BET_LOSE_BACK_REWARD_POINT.") AND point <> 0) OR ac_code IN (10, 124)) order by idx desc ";

    $db_dataCashList = $CASHAdminDAO->getQueryData($p_data);
    if (is_null($db_dataCashList)) { 
        $db_cashlist_cnt = 0;
    } else {
        $db_cashlist_cnt = count($db_dataCashList);
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
<td style='width: 70px;background-color:#6F6F6F;color:#fff'>사용 포인트</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>보유 포인트</td>
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
        
        // money에 값을 넣는 경우가 있다.
        if($db_dataCashList[$i]['ac_code'] == 10 || $db_dataCashList[$i]['ac_code'] == 124){
            if($db_dataCashList[$i]['point'] == 0){
                $db_dataCashList[$i]['point'] = $db_dataCashList[$i]['r_money'];
                $db_dataCashList[$i]['af_point'] = $db_dataCashList[$i]['af_r_money'];
            }
        }
        
        if($db_dataCashList[$i]['m_kind'] == 'P') {
            $cash_type_color = 'color:#FD0000;';
            $db_point = number_format($db_dataCashList[$i]['point']);
        }
        else if($db_dataCashList[$i]['m_kind'] == 'M') {
            $cash_type_color = 'color:#0036FD;';
            $db_point = number_format(-$db_dataCashList[$i]['point']);
        }
        else {
            $cash_type_color = '';
            $db_point = number_format($db_dataCashList[$i]['point']);
        }
        
        
        $db_af_point = number_format($db_dataCashList[$i]['af_point']);
        $db_coment = $db_dataCashList[$i]['coment'];
        $db_reg_date = $db_dataCashList[$i]['reg_time'];
        
        
        $data_str_2 .="<tr><td style='width: 55px;'>".($db_cashlist_cnt-$i)."</td>";
        $data_str_2 .="<td style='width: 70px;text-align:right;font-size: 0.75em;$cash_type_color'>$db_point</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$db_af_point</td>";
        $data_str_2 .="<td style='text-align:left;font-size: 0.75em;'>$db_coment</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$db_reg_date</td></tr>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table></div>";

$result['retData_2'] = $data_str_2;

//$log_str = "[_pop_userinfo_pointlog] [".$result['retData']."]";
//$UTIL->logWrite($log_str,"pop_memo");

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>