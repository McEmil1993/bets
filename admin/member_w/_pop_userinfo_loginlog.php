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
     
    $p_data['sql'] = " SELECT ip, country, login_yn, login_datetime ";
    $p_data['sql'] .= " FROM member_login_history ";
    $p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']." order by idx desc ";
    
    $db_dataLoginList = $CASHAdminDAO->getQueryData($p_data);
    $db_loginlist_cnt = count($db_dataLoginList);
    
    $db_first_ip = $db_first_time = '';
    if($db_loginlist_cnt > 0) {
        $db_first_ip = $db_dataLoginList[0]['ip'];
        $db_first_country = $db_dataLoginList[0]['country'];
        $db_first_time = $db_dataLoginList[0]['login_datetime'];
    } else {
        $db_first_ip = '';
        $db_first_country = '';
        $db_first_time = '';
    }
    
    $p_data['sql'] = " SELECT ip, country, COUNT(*) AS cnt  ";
    $p_data['sql'] .= " FROM member_login_history ";
    $p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']." GROUP BY ip, country ";
    
    $db_dataLoginIP = $CASHAdminDAO->getQueryData($p_data);
    $db_loginIP_cnt = count($db_dataLoginIP);
    
    $p_data['sql'] = " SELECT reg_time  ";
    $p_data['sql'] .= " FROM member ";
    $p_data['sql'] .= " WHERE idx = ".$p_data['m_idx'];
    $db_reg_time_list = $CASHAdminDAO->getQueryData($p_data);
    
    $db_reg_time = $db_reg_time_list[0]['reg_time'];

    $CASHAdminDAO->dbclose();
}

$data_str = "
<table>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>회원 접속 내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>가입 일시</td>
<td style='text-align:left;'>$db_reg_time</td>
</tr>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>최근 접속 IP</td>
<td style='text-align:left;'>$db_first_ip / $db_first_country</td>
</tr>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>최근 접속 일시</td>
<td style='text-align:left;'>$db_first_time</td>
</tr>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>최근 접속 페이지</td>
<td style='text-align:ldeft;'></td>
</tr>
</table>
</td>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>접속 IP목록</td>
</tr>
</table>
<div class='tline' style='max-height: 120px;'>
<table class='mlist'>";
if($db_loginIP_cnt > 0) {
    for ($i=0;$i<$db_loginIP_cnt;$i++)
    {
        $db_ip = $db_dataLoginIP[$i]['ip'];
        $db_country = $db_dataLoginIP[$i]['country'];
        $db_cnt = $db_dataLoginIP[$i]['cnt'];
        
        $data_str .="<tr>";
        $data_str .="<td style='width: 90px;text-align:left;'>$db_ip</td>";
        $data_str .="<td style='width: 60px;text-align:left;font-size: 0.75em;'>$db_country</td>";
        $data_str .="<td style='width: 120px;text-align:right;font-size: 0.75em;'>$db_cnt 회</td>";
        $data_str .="</tr>";
    }
}
else {
    $data_str .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str .= "
</table>
</div>
</td>
</tr>
</table>";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$data_str_2 = "
<table class='mlist'>
<tr>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>IP</td>
<td style='width: 60px;background-color:#6F6F6F;color:#fff'>국가</td>
<td style='width: 120px;background-color:#6F6F6F;color:#fff'>접속일시</td>
<td style='width: 150px;background-color:#6F6F6F;color:#fff'>차단</td>
</tr>
</table>
<div class='tline scroll_tline' style='max-height: 460px;'>
<table class='mlist scroll_mlist'>";

if($db_loginlist_cnt > 0) {
    for ($i=0;$i<$db_loginlist_cnt;$i++)
    {
        $db_ip = $db_dataLoginList[$i]['ip'];
        $db_country = $db_dataLoginList[$i]['country'];
        $db_login_yn = $db_dataLoginList[$i]['login_yn'];
        $db_date = $db_dataLoginList[$i]['login_datetime'];
        
        $data_str_2 .="<tr><td style='width: 80px;text-align:left;'>$db_ip</td>";
        $data_str_2 .="<td style='width: 60px;text-align:left;font-size: 0.75em;'>$db_country</td>";
        $data_str_2 .="<td style='width: 120px;font-size: 0.75em;'>$db_date</td>";
        $data_str_2 .="<td style='width: 150px;font-size: 0.75em;'>$db_login_yn</td></tr>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table></div>";

$result['retData_2'] = $data_str_2;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>