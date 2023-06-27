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
    $p_data['sql'] = " SELECT idx, title, is_answer, is_view, is_status, create_dt FROM menu_qna ";
    $p_data['sql'] .= " WHERE member_idx = ".$p_data['m_idx']."  order by idx desc ";

    $db_dataList = $CASHAdminDAO->getQueryData($p_data);
    $db_list_cnt = count($db_dataList);
    
    $CASHAdminDAO->dbclose();
}

$data_str = "
<table class='mlist'>
<tr>
<td style='width: 55px;background-color:#6F6F6F;color:#fff'>번호</td>
<td style='background-color:#6F6F6F;color:#fff'>제목</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>답변</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>노출</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>상태</td>
<td style='width: 135px;background-color:#6F6F6F;color:#fff'>등록일시</td>
</tr>
</table>
<div class='tline scroll_tline' style='max-height: 200px;'>
<table class='mlist scroll_mlist'>";

if($db_list_cnt > 0) {
    for ($i=0;$i<$db_list_cnt;$i++)
    {
        $red_type_color = '';
        
        if($db_dataList[$i]['is_answer'] == 'Y') {
            $red_type_color = 'color:#FD0000;';
            $is_answer_str = "답변완료";
        }
        else {
            $red_type_color = '';
            $is_answer_str = "답변대기";
        }
        
        if($db_dataList[$i]['is_status'] == 'D') {
            $is_del_str = "삭제";
        }
        else {
            $is_del_str = "";
        }
        
        if($db_dataList[$i]['is_view'] == 'Y') {
            $is_view_str = "고객노출";
        }
        else {
            $is_view_str = "고객숨김";
        }
        
        $db_idx = $db_dataList[$i]['idx'];
        $db_title = $db_dataList[$i]['title'];
        $db_reg_date = $db_dataList[$i]['create_dt'];
        
        
        $data_str .="<tr><td style='width: 55px;'>".($db_list_cnt-$i)."</td>";
        $data_str .="<td style='text-align:left;font-size: 0.75em;'><a href=\"javascript:getQna($db_idx);\">$db_title</a></td>";
        $data_str .="<td style='width: 80px;text-align:left;font-size: 0.75em;$red_type_color'>$is_answer_str</td>";
        $data_str .="<td style='width: 80px;text-align:left;font-size: 0.75em;'>$is_view_str</td>";
        $data_str .="<td style='width: 80px;text-align:left;font-size: 0.75em;'>$is_del_str</td>";
        $data_str .="<td style='width: 135px;font-size: 0.75em;'>$db_reg_date</td></tr>";
    }
}
else {
    $data_str .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str .="</table></div>";

$result['retData_2'] = $data_str;


$data_str_2 = "";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str_2;


echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>