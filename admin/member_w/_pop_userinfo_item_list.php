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
    $p_data['sql'] = "SELECT item_id, name, item_value, status, member_item.create_dt, member_item.update_dt"
                    . " FROM member_item join item on member_item.item_id = id WHERE member_idx =".$p_data['m_idx'];
    $db_dataItemList = $CASHAdminDAO->getQueryData($p_data);
    $CASHAdminDAO->dbclose();
}

$data_str = "";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$data_str_2 = "
<table class='mlist scroll_mlist'>
<tr>
<td style='width: 55px;background-color:#6F6F6F;color:#fff'>이름</td>
<td style='width: 70px;background-color:#6F6F6F;color:#fff'>수치</td>
<td style='width: 80px;background-color:#6F6F6F;color:#fff'>상태</td>
<td style='width: 100px;background-color:#6F6F6F;color:#fff'>구매시</td>
<td style='width: 100px;background-color:#6F6F6F;color:#fff'>사용시</td>";

if(count($db_dataItemList) > 0) {
    for ($i=0;$i<count($db_dataItemList);$i++)
    {   
        $name = $db_dataItemList[$i]['name'];
        $value = $db_dataItemList[$i]['item_value'];
        $status = $db_dataItemList[$i]['status']==0?'미사용':'사용';
        $create_dt = $db_dataItemList[$i]['create_dt'];
        $update_dt = $db_dataItemList[$i]['update_dt'];
        
        $data_str_2 .="<tr>";
        $data_str_2 .="<td style='width: 70px;text-align:right;font-size: 0.75em;'>$name</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$value</td>";
        $data_str_2 .="<td style='width: 80px;text-align:right;font-size: 0.75em;'>$status</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$create_dt</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$update_dt</td></tr>";
        $data_str_2 .="</tr>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table>";

$result['retData_2'] = $data_str_2;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>