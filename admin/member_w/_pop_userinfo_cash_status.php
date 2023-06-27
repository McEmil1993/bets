<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Cash_dao.php');

$UTIL = new CommonUtil();

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['tab_num'] = trim(isset($_POST['tab_num']) ? $_POST['tab_num'] : 0);


$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

$db_charge_tot_cash = $db_charge_max_cash = $db_charge_tot_cnt = 0;
$db_exchange_tot_cnt = $db_exchange_tot_cash = $db_exchange_max_cash = $db_calculate_tot = $db_calculate_rate = 0;

if($db_conn) {
    $p_data['m_idx'] = $CASHAdminDAO->real_escape_string($p_data['m_idx']);
    $p_data['tab_num'] = $CASHAdminDAO->real_escape_string($p_data['tab_num']);
    
    // 통계 내역을 구한다.
    $p_data['sql'] = "SELECT * ";
    $p_data['sql'] .= " FROM total_member_cash ";
    $p_data['sql'] .= " WHERE member_idx=".$p_data['m_idx'];
    $db_dataTotalMemberCash = $CASHAdminDAO->getQueryData($p_data);
    
    $db_charge_total_count = (isset($db_dataTotalMemberCash[0]['charge_total_count']) ? $db_dataTotalMemberCash[0]['charge_total_count'] : 0);
    $db_charge_total_money = (isset($db_dataTotalMemberCash[0]['charge_total_money']) ? $db_dataTotalMemberCash[0]['charge_total_money'] : 0);
    $db_max_charge = (isset($db_dataTotalMemberCash[0]['max_charge']) ? $db_dataTotalMemberCash[0]['max_charge'] : 0);
    
    $db_exchange_total_count = (isset($db_dataTotalMemberCash[0]['exchange_total_count']) ? $db_dataTotalMemberCash[0]['exchange_total_count'] : 0);
    $db_exchange_total_money = (isset($db_dataTotalMemberCash[0]['exchange_total_money']) ? $db_dataTotalMemberCash[0]['exchange_total_money'] : 0);
    $db_max_exchange = (isset($db_dataTotalMemberCash[0]['max_exchange']) ? $db_dataTotalMemberCash[0]['max_exchange'] : 0);
    
    // 충전/환전 완료된 내역으로 금액을 구한다.
    $p_data['sql'] = "SELECT COUNT(*) as charge_tot_cnt, SUM(money) as charge_tot_cash, MAX(money) as charge_max_cash ";
    $p_data['sql'] .= " FROM member_money_charge_history ";
    $p_data['sql'] .= " WHERE member_idx=".$p_data['m_idx']." AND STATUS=3";
    $p_data['sql'] .= " AND status=3 and update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and update_dt <= NOW()";
    
    $db_dataCharge = $CASHAdminDAO->getQueryData($p_data);
    
    $db_charge_tot_cnt = (isset($db_dataCharge[0]['charge_tot_cnt']) ? $db_dataCharge[0]['charge_tot_cnt'] : 0);
    $db_charge_tot_cash = (isset($db_dataCharge[0]['charge_tot_cash']) ? $db_dataCharge[0]['charge_tot_cash'] : 0);
    //$db_charge_max_cash = (isset($db_dataCharge[0]['charge_max_cash']) ? $db_dataCharge[0]['charge_max_cash'] : 0);
    
    $db_charge_tot_cnt = $db_charge_tot_cnt + $db_charge_total_count;
    $db_charge_tot_cash = $db_charge_tot_cash + $db_charge_total_money;
    $db_charge_max_cash = $db_max_charge;
    
    $p_data['sql'] = "SELECT COUNT(*) as exchange_tot_cnt, SUM(money) as exchange_tot_cash, MAX(money) as exchange_max_cash ";
    $p_data['sql'] .= " FROM member_money_exchange_history ";
    $p_data['sql'] .= " WHERE member_idx=".$p_data['m_idx']." AND STATUS=3";
    $p_data['sql'] .= " AND status=3 and update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and update_dt <= NOW()";
    
    $db_dataExchange = $CASHAdminDAO->getQueryData($p_data);
    
    $db_exchange_tot_cnt = (isset($db_dataExchange[0]['exchange_tot_cnt']) ? $db_dataExchange[0]['exchange_tot_cnt'] : 0);
    $db_exchange_tot_cash = (isset($db_dataExchange[0]['exchange_tot_cash']) ? $db_dataExchange[0]['exchange_tot_cash'] : 0);
    //$db_exchange_max_cash = (isset($db_dataExchange[0]['exchange_max_cash']) ? $db_dataExchange[0]['exchange_max_cash'] : 0);
    
    $db_exchange_tot_cnt = $db_exchange_tot_cnt + $db_exchange_total_count;
    $db_exchange_tot_cash = $db_exchange_tot_cash + $db_exchange_total_money;
    $db_exchange_max_cash = $db_max_exchange;
    
    
    $db_calculate_tot = $db_charge_tot_cash - $db_exchange_tot_cash;
    
    $db_rate_buff = $db_calculate_rate = 0;
    
    if ( ($db_exchange_tot_cash > 0) && ($db_charge_tot_cash > 0) ) {
        $db_rate_buff = ( 100 - (($db_exchange_tot_cash/$db_charge_tot_cash) * 100));
        $db_calculate_rate = sprintf('%0.2f', $db_rate_buff); // 520 -> 520.00
    }
    else {
        if ( ($db_exchange_tot_cash < 1) && ($db_charge_tot_cash > 0) ) {
            $db_calculate_rate = 100;
        }
        else if ( ($db_exchange_tot_cash > 0) && ($db_charge_tot_cash < 1) ) {
            $db_calculate_rate = -100;
        }
        else {
            $db_calculate_rate = 0;
        }
    }
    
    if($p_data['tab_num'] == 0){
        $p_data['sql'] = " SELECT * FROM ( ";
        $p_data['sql'] .= " SELECT 'ch' as ctype, a.idx, a.member_idx, a.money, bonus_point as point, a.create_dt, a.update_dt, b.account_bank, b.account_number, b.account_name, a.status, a.result_money ";
        $p_data['sql'] .= " FROM member_money_charge_history a, member b ";
        $p_data['sql'] .= " WHERE a.member_idx=b.idx and b.idx=".$p_data['m_idx']." and a.STATUS > 0 ";
        $p_data['sql'] .= " UNION ALL ";
        $p_data['sql'] .= " SELECT 'ex' as ctype, d.idx, d.member_idx, d.money, 0 as point, d.create_dt, d.update_dt, e.account_bank, e.account_number, e.account_name, d.status, d.result_money ";
        $p_data['sql'] .= " FROM member_money_exchange_history d, member e ";
        $p_data['sql'] .= " WHERE d.member_idx=e.idx and e.idx=".$p_data['m_idx']." and d.STATUS > 0 ";
        $p_data['sql'] .= " ) c ORDER BY create_dt DESC; ";
    }else if($p_data['tab_num'] == 1){
        $p_data['sql'] = " SELECT 'ch' as ctype, a.idx, a.member_idx, a.money, bonus_point as point, a.create_dt, a.update_dt, b.account_bank, b.account_number, b.account_name, a.status, a.result_money ";
        $p_data['sql'] .= " FROM member_money_charge_history a, member b ";
        $p_data['sql'] .= " WHERE a.member_idx=b.idx and b.idx=".$p_data['m_idx']." and a.STATUS > 0 ";
        $p_data['sql'] .= " ORDER BY create_dt DESC; ";
    }else{
        $p_data['sql'] = " SELECT 'ex' as ctype, d.idx, d.member_idx, d.money, 0 as point, d.create_dt, d.update_dt, e.account_bank, e.account_number, e.account_name, d.status, d.result_money ";
        $p_data['sql'] .= " FROM member_money_exchange_history d, member e ";
        $p_data['sql'] .= " WHERE d.member_idx=e.idx and e.idx=".$p_data['m_idx']." and d.STATUS > 0 ";
        $p_data['sql'] .= " ORDER BY create_dt DESC; ";
    }
    
    $db_dataCashList = $CASHAdminDAO->getQueryData($p_data);
    $db_cashlist_cnt = count($db_dataCashList);
    
    
    $CASHAdminDAO->dbclose();
}

$data_str = "
<table>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>충/환전 종합내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>총 입금금액</td>
<td style='text-align:right;'>".number_format($db_charge_tot_cash)." 원</td>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>최대 임금 금액</td>
<td style='text-align:right;'>".number_format($db_charge_max_cash)." 원</td>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>총 입금 횟수</td>
<td style='text-align:right;'>".number_format($db_charge_tot_cnt)." 회</td>
</tr>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>총 출금금액</td>
<td style='text-align:right;'>".number_format($db_exchange_tot_cash)." 원</td>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>최대 출금 금액</td>
<td style='text-align:right;'>".number_format($db_exchange_max_cash)." 원</td>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>총 출금 횟수</td>
<td style='text-align:right;'>".number_format($db_exchange_tot_cnt)." 회</td>
</tr>
<tr>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>총 정산액</td>
<td style='text-align:right;'>".number_format($db_calculate_tot)." 원</td>
<td style='width: 90px; padding: 2px;text-align:left;background-color:#f6f6f6;'>수익률</td>
<td style='text-align:right;'>".$db_calculate_rate." %</td>
</tr>
</table>
</td>
</tr>
</table>";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$data_str_2 = "
<table class='mlist'>
<tr>
<td style='width: 55px;background-color:#6F6F6F;color:#fff'>구분</td>
<td style='width: 55px;background-color:#6F6F6F;color:#fff'>상태</td>
<td style='width: 90px;background-color:#6F6F6F;color:#fff'>처리금액</td>
<td style='width: 90px;background-color:#6F6F6F;color:#fff'>보너스</td>
<td style='width: 90px;background-color:#6F6F6F;color:#fff'>결과금액</td>
<td style='width: 180px;background-color:#6F6F6F;color:#fff'>계좌</td>
<td style='width: 100px;background-color:#6F6F6F;color:#fff'>등록일자</td>
<td style='width: 100px;background-color:#6F6F6F;color:#fff'>처리일자</td>
</tr>
</table>
<div class='tline scroll_tline' style='max-height: 460px;'>
<table class='mlist scroll_mlist'>";

if($db_cashlist_cnt > 0) {
    for ($i=0;$i<$db_cashlist_cnt;$i++)
    {
        $cash_type = '';
        $cash_type_color = '';
        
        if($db_dataCashList[$i]['ctype'] == 'ch') {
            $cash_type = '충전';
            $cash_type_color = 'color:#0036FD;';
        }
        else if($db_dataCashList[$i]['ctype'] == 'ex') {
            $cash_type = '환전';
            $cash_type_color = 'color:#FD0000;';
        }
        
        $db_cash = number_format($db_dataCashList[$i]['money']);
        $point = number_format($db_dataCashList[$i]['point']);
        $db_cash_result = number_format($db_dataCashList[$i]['result_money']);
        $db_a_bank = $db_dataCashList[$i]['account_bank'];
        $db_a_name = $db_dataCashList[$i]['account_name'];
        //$db_a_number = $db_dataCashList[$i]['account_number'];
        $db_a_number = $UTIL->getAccountNumberColor($db_dataCashList[$i]['account_number']);
        $db_c_date = $db_dataCashList[$i]['create_dt'];
        $db_u_date = $db_dataCashList[$i]['update_dt'];
        $db_status = $db_dataCashList[$i]['status'];
        switch($db_status){
            case 1:
                $db_status = '진행중';
                break;
            case 3:
                $db_status = '완료';
                break;
            case 11:
                $db_status = '취소';
                break;
        }
        
        $data_str_2 .="<tr><td style='width: 55px;$cash_type_color'>$cash_type</td>";
        $data_str_2 .="<td style='width: 55px;'>$db_status</td>";
        $data_str_2 .="<td style='width: 90px;text-align:right;font-size: 0.75em;'>$db_cash 원</td>";
        $data_str_2 .="<td style='width: 90px;text-align:right;font-size: 0.75em;'>$point 원</td>";
        $data_str_2 .="<td style='width: 90px;text-align:right;font-size: 0.75em;'>$db_cash_result 원</td>";
        $data_str_2 .="<td style='width: 180px;white-space: pre-line;text-align:left;font-size: 0.75em;'>$db_a_bank $db_a_number $db_a_name</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$db_c_date</td>";
        $data_str_2 .="<td style='width: 100px;font-size: 0.75em;'>$db_u_date</td></tr>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table></div>";

$result['retData_2'] = $data_str_2;


echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>