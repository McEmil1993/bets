<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
$p_data['m_idx'] = trim(isset($_REQUEST['m_idx']) ? $_REQUEST['m_idx'] : 0);
$p_data['tab_num'] = trim(isset($_REQUEST['tab_num']) ? $_REQUEST['tab_num'] : 0);
$p_data['selContent'] = trim(isset($_REQUEST['p_seltype']) ?$_REQUEST['p_seltype'] : 7);


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
    $p_data['tab_num'] = $MEMAdminDAO->real_escape_string($p_data['tab_num']);
    $p_data['selContent'] = $MEMAdminDAO->real_escape_string($p_data['selContent']);
    
    $limit = 10;
    $startLimit = $limit * ( $p_data['page'] - 1 );

    // 배팅내역 총 카운트를 가져온다.
    $g_data['sql'] = "
        SELECT 
                COUNT(*) AS cnt
            FROM 
                OD_HASH_BET_HIST
            WHERE
                PRD_TYPE = 'R'
                AND MBR_IDX = ".$p_data['m_idx']."
    ";
    
    $total_page = $MEMAdminDAO->getQueryData($g_data)[0]['cnt'];
    
    // 배팅내역 리스트를 가져온다.
    $q_data['sql'] = "
        SELECT 
            TYPE,
            MBR_IDX,
            HLD_MNY,
            BET_MNY,
            RSLT_MNY,
            WAVE,
            REG_DTM
        FROM 
            OD_HASH_BET_HIST
        WHERE 
            PRD_TYPE = 'R'
            AND MBR_IDX = ".$p_data['m_idx']."

        ORDER BY HASH_BET_IDX DESC
        LIMIT ".$startLimit.", ".$limit."
    ";
    
    $db_list = $MEMAdminDAO->getQueryData($q_data);

    $pagination = Fn_BoardLinkPagination($p_data['page'], $limit, $total_page, ceil($total_page/$limit), "/member_w/pop_userinfo.php", "&m_idx=".$p_data['m_idx']."&tab_num=".$p_data['tab_num']."&selContent=".$p_data['selContent']);
    
    $MEMAdminDAO->dbclose();
}


$result['retCode'] = 1000;
$result['retData_1'] = '';

$data_str_2 = "
<table class='mlist'>
<tr>
<td style='width: 10%;background-color:#6F6F6F;color:#fff'>날짜</td>
<td style='width: 5%;background-color:#6F6F6F;color:#fff'>베팅금액</td>
<td style='width: 5%px;background-color:#6F6F6F;color:#fff'>당첨금</td>
<td style='width: 5%;background-color:#6F6F6F;color:#fff'>보유금액</td>
<td style='width: 5%;background-color:#6F6F6F;color:#fff'>베팅결과</td>
<td style='width: 5%;background-color:#6F6F6F;color:#fff'>웨이브</td>
</tr>";

if($db_list > 0) {
    for ($i=0; $i<count($db_list); $i++)
    {
        $status = '배팅';
        $status_color = '';
        if('W' == $db_list[$i]['TYPE']){
            $status = '적중';
            $status_color = '#9FD5E9';
        }else if('L' == $db_list[$i]['TYPE']){
            $status = '낙첨';
            $status_color = '#F9BFBF';
        }else if('C' == $db_list[$i]['TYPE']){
            $status = '취소';
            $status_color = '#FFFCBB';
        }else if('I' == $db_list[$i]['TYPE']){
            $status = '인게임보너스';
            $status_color = '#FFFCBB';
        }else if('P' == $db_list[$i]['TYPE']){
            $status = '프로모션보너스';
            $status_color = '#FFFCBB';
        }else if('J' == $db_list[$i]['TYPE']){
            $status = '잭팟보너스';
            $status_color = '#FFFCBB';
        }
        
        // 당첨금
        $winMoney = $db_list[$i]['RSLT_MNY'] + $db_list[$i]['BET_MNY'];

        // 보유금액
        $hld_money = $db_list[$i]['HLD_MNY'] + $db_list[$i]['RSLT_MNY'];
        
        $data_str_2 .="<tr><td style='width: 10%;'>".$db_list[$i]['REG_DTM']."</td>";
        $data_str_2 .="<td style='width: 5%; text-align:right;'>".number_format($db_list[$i]['BET_MNY'])."</td>";
        $data_str_2 .="<td style='width: 5%; text-align:right;'>".number_format($winMoney)."</td>";
        $data_str_2 .="<td style='width: 5%; text-align:right;'>".number_format($hld_money)."</td>";
        $data_str_2 .="<td width='5%' bgcolor='$status_color'>$status</td>";
        $data_str_2 .="<td style='width: 10%;'>".$db_list[$i]['WAVE']."</td>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table><br><br>";

echo json_encode(array( 'retCode' => 1000, 'retData_1' => "", 'retData_2' => $data_str_2.$pagination  ), JSON_UNESCAPED_UNICODE);
?>