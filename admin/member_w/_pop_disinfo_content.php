<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_Member_dao.php');



$p_data['dis_id'] = trim(isset($_POST['dis_id']) ? $_POST['dis_id'] : '');
$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$db_charge_tot_cash = $db_charge_max_cash = $db_charge_tot_cnt = 0;
$db_exchange_tot_cnt = $db_exchange_tot_cash = $db_exchange_max_cash = $db_calculate_tot = $db_calculate_rate = 0;
$db_charge_today_cash = $db_charge_today_cnt = 0;
$db_exchange_today_cash = $db_exchange_today_cnt = 0;
$db_Recnt = 0;
if ($db_conn) {
    
    $dis_id = $MEMAdminDAO->real_escape_string($p_data['dis_id']);
    $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);

    $db_srch_s_date = date("Y-m-d 00:00:00");
    $db_srch_e_date = date("Y-m-d 23:59:59");
    // 총판소속 유저들을 구한다.
    //$p_data['sql'] = "SELECT idx from member where recommend_member = " . $p_data['m_idx'];
    $p_data['sql'] = "SELECT idx from member where dis_id = '$dis_id' ";
    $dbResult = $MEMAdminDAO->getQueryData($p_data);
    if (0 < count($dbResult)) {
        $userList = array();
        foreach ($dbResult as $key => $value) {
            $userList[] = $value['idx'];
        }

        $userList = implode(',', $userList);

        // bet
        $p_data['sql'] = "SELECT sum(total_betting_money) as total_betting_money, sum(total_betting_count) as total_betting_count, sum(total_betting_win_money) as total_betting_win_money, ";
        $p_data['sql'] .= "sum(total_deposit_money) as total_deposit_money, sum(total_withdraw_money) as total_withdraw_money, ";
        $p_data['sql'] .= "sum(total_deposit_count) as total_deposit_count, sum(total_withdraw_count) as total_withdraw_count, ";
        $p_data['sql'] .= "max(max_deposit_money) as max_deposit_money, max(max_withdraw_money) as max_withdraw_money";
        $p_data['sql'] .= " FROM member_summary";
        $p_data['sql'] .= " WHERE member_idx in (" . $userList . ")";
        $db_dataSummary = $MEMAdminDAO->getQueryData($p_data);

        // 총 베팅횟수, 총 베팅금액
        $db_bet_tot_cnt = (isset($db_dataSummary[0]['total_betting_count']) ? $db_dataSummary[0]['total_betting_count'] : 0);
        $db_bet_tot_cash = (isset($db_dataSummary[0]['total_betting_money']) ? $db_dataSummary[0]['total_betting_money'] : 0);

        $db_bet_tot_cnt  = true === isset($db_bet_tot_cnt)  ? $db_bet_tot_cnt  : 0;
        $db_bet_tot_cash = true === isset($db_bet_tot_cash) ? $db_bet_tot_cash : 0;
        
        //return;
        // 금일 충전
        $p_data['sql'] = "SELECT COUNT(*) as charge_tot_cnt, SUM(a.money) as charge_tot_cash ";
        $p_data['sql'] .= " FROM member_money_charge_history a , member b ";
        $p_data['sql'] .= " WHERE a.member_idx = b.idx and b.dis_id='$dis_id' and a.STATUS=3 and update_dt >= '$db_srch_s_date' AND update_dt <= '$db_srch_e_date' ";

        $db_dataChargeToday = $MEMAdminDAO->getQueryData($p_data);

        $db_charge_today_cnt = (isset($db_dataChargeToday[0]['charge_tot_cnt']) ? $db_dataChargeToday[0]['charge_tot_cnt'] : 0);
        $db_charge_today_cash = (isset($db_dataChargeToday[0]['charge_tot_cash']) ? $db_dataChargeToday[0]['charge_tot_cash'] : 0);

        // 금일 환전
        $p_data['sql'] = "SELECT COUNT(*) as exchange_tot_cnt, SUM(a.money) as exchange_tot_cash ";
        $p_data['sql'] .= " FROM member_money_exchange_history a , member b ";
        $p_data['sql'] .= " WHERE a.member_idx = b.idx and b.dis_id= '$dis_id' and a.STATUS=3 and update_dt >= '$db_srch_s_date' AND update_dt <= ' $db_srch_e_date' ";

        $db_dataExchangeToday = $MEMAdminDAO->getQueryData($p_data);

        $db_exchange_today_cnt = (isset($db_dataExchangeToday[0]['exchange_tot_cnt']) ? $db_dataExchangeToday[0]['exchange_tot_cnt'] : 0);
        $db_exchange_today_cash = (isset($db_dataExchangeToday[0]['exchange_tot_cash']) ? $db_dataExchangeToday[0]['exchange_tot_cash'] : 0);

    
        // 총충전
        $db_charge_tot_cnt = (isset($db_dataSummary[0]['total_deposit_count']) ? $db_dataSummary[0]['total_deposit_count'] : 0);
        $db_charge_tot_cash = (isset($db_dataSummary[0]['total_deposit_money']) ? $db_dataSummary[0]['total_deposit_money'] : 0);
        $db_charge_max_cash = (isset($db_dataSummary[0]['max_deposit_money']) ? $db_dataSummary[0]['max_deposit_money'] : 0);

  
        // 총 환전
        $db_exchange_tot_cnt = (isset($db_dataSummary[0]['total_withdraw_count']) ? $db_dataSummary[0]['total_withdraw_count'] : 0);
        $db_exchange_tot_cash = (isset($db_dataSummary[0]['total_withdraw_money']) ? $db_dataSummary[0]['total_withdraw_money'] : 0);
        $db_exchange_max_cash = (isset($db_dataSummary[0]['max_withdraw_money']) ? $db_dataSummary[0]['max_withdraw_money'] : 0);

        $db_calculate_today = $db_charge_today_cash - $db_exchange_today_cash;
        $db_calculate_today = true === isset($db_calculate_today) ? $db_calculate_today : 0;
         
        $db_calculate_tot = $db_charge_tot_cash - $db_exchange_tot_cash;

        $db_rate_buff = $db_calculate_rate = 0;

        if (($db_exchange_tot_cash > 0) && ($db_charge_tot_cash > 0)) {
            $db_rate_buff = ( 100 - (($db_exchange_tot_cash / $db_charge_tot_cash) * 100));
            $db_calculate_rate = sprintf('%0.2f', $db_rate_buff); // 520 -> 520.00
        } else {
            if (($db_exchange_tot_cash < 1) && ($db_charge_tot_cash > 0)) {
                $db_calculate_rate = 100;
            } else if (($db_exchange_tot_cash > 0) && ($db_charge_tot_cash < 1)) {
                $db_calculate_rate = -100;
            } else {
                $db_calculate_rate = 0;
            }
        }

        $p_data['sql'] = " select total_deposit_money, total_withdraw_money, id, money, point, status ";
        $p_data['sql'] .= "from member_summary join member on member_summary.member_idx = member.idx ";
        $p_data['sql'] .= "where member_idx in (" . $userList . ")";


        $db_dataRecommend = $MEMAdminDAO->getQueryData($p_data);
        $db_Recnt = count($db_dataRecommend);
    }


    $MEMAdminDAO->dbclose();
}

$data_str = "
<table>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>하부회원 종합내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 베팅금액</td>
<td style='text-align:right;'>" . number_format($db_bet_tot_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 충전금액</td>
<td style='text-align:right;'>" . number_format($db_charge_today_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 환전금액</td>
<td style='text-align:right;'>" . number_format($db_exchange_today_cash) . " 원</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 베팅횟수</td>
<td style='text-align:right;'>" . number_format($db_bet_tot_cnt) . " 회</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 충전횟수</td>
<td style='text-align:right;'>" . number_format($db_charge_today_cnt) . " 회</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 환전횟수</td>
<td style='text-align:right;'>" . number_format($db_exchange_today_cnt) . " 회</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;금일 정산액</td>
<td style='text-align:right;'>" . number_format($db_calculate_today) . " 원</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>하부회원 충/환전 내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 입금금액</td>
<td style='text-align:right;'>" . number_format($db_charge_tot_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;최대 입금금액</td>
<td style='text-align:right;'>" . number_format($db_charge_max_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 입금 횟수</td>
<td style='text-align:right;'>" . number_format($db_charge_tot_cnt) . " 회</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 출금금액</td>
<td style='text-align:right;'>" . number_format($db_exchange_tot_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;최대 출금금액</td>
<td style='text-align:right;'>" . number_format($db_exchange_max_cash) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 출금 횟수</td>
<td style='text-align:right;'>" . number_format($db_exchange_tot_cnt) . " 회</td>
</tr>
<tr>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;총 정산액</td>
<td style='text-align:right;'>" . number_format($db_calculate_tot) . " 원</td>
<td style='width: 110px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;수익률</td>
<td style='text-align:right;'>" . $db_calculate_rate . " %</td>
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
<td colspan='7' style='background-color:#6F6F6F;color:#fff'>추천인 목록</td>
</tr>
<tr>
<td style='width: 60px;background-color:#6F6F6F;color:#fff'>아이디</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>입금</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>출금</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>정산</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>보유머니</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>포인트</td>
<td style='width: 140px;background-color:#6F6F6F;color:#fff'>상태</td>
</tr>
</table>
<div class='tline' style='max-height: 460px;'>
<table class='mlist'>";
if ($db_Recnt > 0) {

    for ($i = 0; $i < $db_Recnt; $i++) {

        $db_id = $db_dataRecommend[$i]['id'];
        $db_total_deposit_money = number_format($db_dataRecommend[$i]['total_deposit_money']);
        $db_total_withdraw_money = number_format($db_dataRecommend[$i]['total_withdraw_money']);
        $db_total_cal_money = $db_total_deposit_money - $db_total_withdraw_money;
        $db_money = number_format($db_dataRecommend[$i]['money']);
        $db_point = number_format($db_dataRecommend[$i]['point']);

        $db_status = "";
        switch ($db_dataRecommend[$i]['status']) {
            case 1: $db_status = "사용중";
                break;
            case 2: $db_status = "정지";
                break;
            case 3: $db_status = "탈퇴";
                break;
            case 11: $db_status = "대기";
                break;
        }

        $data_str_2 .= "<td style='width: 60px;'>$db_id</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_total_deposit_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_total_withdraw_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_total_cal_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_money</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_point</td>";
        $data_str_2 .= "<td style='width: 140px;text-align:right;'>$db_status</td></tr>";
    }
} else {
    $data_str_2 .= "<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .= "</table></div>";
$result['retData_2'] = $data_str_2;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>