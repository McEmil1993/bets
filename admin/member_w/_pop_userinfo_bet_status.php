<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_Member_dao.php');
include_once(_LIBPATH . '/class_ComQuery.php');
$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$member_idx = $p_data['m_idx'];
$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$today = date("Y-m-d");
if ($db_conn) {
    $member_idx = $MEMAdminDAO->real_escape_string($member_idx);

    $p_data['sql'] = "SELECT  
            IFNULL(charge_total_count, 0) as charge_total_count,
            IFNULL(charge_total_money, 0) as charge_total_money,
            IFNULL(exchange_total_count, 0) as exchange_total_count,
            IFNULL(exchange_total_money, 0) as exchange_total_money,
            IFNULL(loock_up_total_count, 0) as loock_up_total_count,
            IFNULL(bet_total_count, 0) as bet_total_count,
            IFNULL(bet_total_money, 0) as bet_total_money,
            IFNULL(pre_s_total_money, 0) as pre_s_total_money,
            IFNULL(pre_m_total_money, 0) as pre_m_total_money,
            IFNULL(real_s_total_money, 0) as real_s_total_money,
            IFNULL(real_m_total_money, 0) as real_m_total_money,
            IFNULL(mini_total_money, 0) as mini_total_money,
            IFNULL(casino_total_money, 0) as casino_total_money,
            IFNULL(slot_total_money, 0) as slot_total_money,
            IFNULL(take_total_money, 0) as take_total_money,
            IFNULL(take_slot_total_money, 0) as take_slot_total_money,
            IFNULL(take_casino_total_money, 0) as take_casino_total_money,
            IFNULL(take_mini_total_money, 0) as take_mini_total_money,
            IFNULL(take_real_m_total_money, 0) as take_real_m_total_money,
            IFNULL(take_real_s_total_money, 0) as take_real_s_total_money,
            IFNULL(take_pre_m_total_money, 0) as take_pre_m_total_money,
            IFNULL(take_pre_s_total_money, 0) as take_pre_s_total_money,
            
            IFNULL(casino_total_money, 0) as casino_total_money,
            IFNULL(take_casino_total_money, 0) as take_casino_total_money,
            IFNULL(slot_total_money, 0) as slot_total_money,
            IFNULL(take_slot_total_money, 0) as take_slot_total_money
            
            FROM total_member_cash WHERE member_idx = ?";

    $result = $MEMAdminDAO->getQueryData_pre($p_data['sql'],[$member_idx]);

    // 하루전까지의 총합 정보이다.
    $db_arrTotalMemberCash = (true === isset($result) && true === isset($result[0])) ? $result[0] :
            [
        'charge_total_count' => 0,
        'charge_total_money' => 0,
        'exchange_total_count' => 0,
        'exchange_total_money' => 0,
        'loock_up_total_count' => 0,
        'bet_total_count' => 0,
        'bet_total_money' => 0,
        'pre_s_total_money' => 0,
        'pre_m_total_money' => 0,
        'real_s_total_money' => 0,
        'real_m_total_money' => 0,
        'mini_total_money' => 0,
        'casino_total_money' => 0,
        'slot_total_money' => 0,
        'take_total_money' => 0,
        'take_slot_total_money' => 0,
        'take_casino_total_money' => 0,
        'take_mini_total_money' => 0,
        'take_real_m_total_money' => 0,
        'take_real_s_total_money' => 0,
        'take_pre_m_total_money' => 0,
        'take_pre_s_total_money' => 0,
        'casino_total_money' => 0,
        'take_casino_total_money' => 0,
        'slot_total_money' => 0,
        'take_slot_total_money' => 0,
    ];

    $p_data['sql'] = "SELECT 
    mb.id,
    IFNULL(sum(ch.money), 0) as today_total_ch_money, -- 금일 총 입금
    IFNULL(sum(ex.money), 0) as today_total_ex_money, -- 금일 총 출금
    IFNULL(sum(ch.money), 0) - IFNULL(sum(ex.money), 0) as today_total_ch_ex_money_difference, -- 차액
    IFNULL(sum(mb_bet.total_bet_money), 0) as today_total_bet_money, -- 당일 배팅금
    IFNULL(sum(mb_bet.take_money), 0) as today_total_take_bet_money, -- 당일 당첨금 
    IFNULL(ex_count, 0) as today_total_ex_count, -- 환전 횟수 
    IFNULL(ch_count, 0) as today_total_ch_count -- 충전 횟수 
    FROM member as mb
    LEFT JOIN ( SELECT IFNULL(SUM(c.money),0) as money,c.member_idx,count(c.idx) as ch_count FROM member_money_charge_history c 
				left join member t1 on c.member_idx=t1.idx 
             WHERE  c.status=3 AND c.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and c.update_dt <= NOW()
             group by t1.idx
             ) as ch ON mb.idx = ch.member_idx
    LEFT JOIN ( SELECT IFNULL(SUM(e.money),0) as money,e.member_idx,count(e.idx) as ex_count FROM member_money_exchange_history e 
             left join member t1 on e.member_idx=t1.idx 
             WHERE e.status=3 and e.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and e.update_dt <= NOW()
             group by t1.idx
             ) as ex ON mb.idx = ex.member_idx
    LEFT JOIN  ( select  IFNULL(SUM(m.total_bet_money),0) as total_bet_money
				,IFNULL(SUM(m.take_money),0) as take_money
                ,m.member_idx FROM member_bet m
				left join member t1 on m.member_idx=t1.idx 
			    where 
                m.bet_status = 3 
                AND m.calculate_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') 
                AND m.calculate_dt <= NOW()   
                AND m.total_bet_money != m.take_money 
				group by t1.idx
     ) as mb_bet  ON mb.idx = mb_bet.member_idx
    WHERE mb.idx = ?";

    //$db_arrTodayTotalMemberCash = $MEMAdminDAO->getQueryData($p_data)[0];
    $result = $MEMAdminDAO->getQueryData_pre($p_data['sql'],[$member_idx]);
    CommonUtil::logWrite("bet status  db_arrTodayTotalMemberCash sql ==> " . $p_data['sql'], "info");
    // 당일 구분없이 총합 정보  미니,카지노,슬롯은 제외
    $db_arrTodayTotalMemberCash = (true === isset($result) && true === isset($result[0])) ? $result[0] :
            [
        'id' => '',
        'today_total_ch_money' => 0,
        'today_total_ex_money' => 0,
        'today_total_ch_ex_money_difference' => 0,
        'today_total_bet_money' => 0,
        'today_total_take_bet_money' => 0,
        'today_total_ex_count' => 0,
    ];


    $db_srch_s_date = $today . " 00:00:00";
    $db_srch_e_date = $today . " 23:59:59";

    
    // 당일 합산 프리,실시간 s,d 구분한 정보 
    $member_id = $db_arrTodayTotalMemberCash['id'];
    $where_new = " AND 1 = 1";
    $param_where_new = array();
    if ('' != $member_id) {
     $where_new = "AND T1.id = ? ";
     $param_where_new[] = $member_id;
    }

    $param = array_merge([$db_srch_s_date, $db_srch_e_date],$param_where_new);
    $p_data['sql'] = ComQuery::doSportsRealBetQuery($db_srch_s_date, $db_srch_e_date, $where_new);
    //CommonUtil::logWrite("bet status  doSportsRealBetQuery sql==> : " . $p_data['sql'], "info");
    $db_dataArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'],$param);
    $db_toDayDataArr = true === isset($db_dataArr) && true === isset($db_dataArr[0]) ? $db_dataArr[0] : [
        'pre_bet_sum_s' => 0,
        'pre_bet_sum_d' => 0,
        'pre_take_sum_s' => 0,
        'pre_take_sum_d' => 0,
        'real_bet_sum_s' => 0,
        'real_bet_sum_d' => 0,
        'real_take_sum_s' => 0,
        'real_take_sum_d' => 0,
    ];

    

    // 당일 미니게임 총합 정보 
    $param = array_merge([$db_srch_s_date,$db_srch_e_date],$param_where_new);
    array_push($param,0);
    $p_mini_data['sql'] = ComQuery::doMiniBetQuery($db_srch_s_date, $db_srch_e_date, 0, $where_new);
    $db_mini_dataArr = $MEMAdminDAO->getQueryData_pre($p_mini_data['sql'],$param);
    //CommonUtil::logWrite("bet status  doMiniBetQuery sql ==> " . $p_mini_data['sql'], "info");
    $db_mini_dataArr = true === isset($db_mini_dataArr) && true === isset($db_mini_dataArr[0]) ? $db_mini_dataArr[0] : [
        'mini_bet_sum_d' => 0,
        'mini_take_sum_d' => 0
    ];
    
    
    $db_toDayDataArr = array_merge($db_toDayDataArr,$db_mini_dataArr);
    // 당일 카지노 슬롯 총합 정보 

    $p_casino_slot_data['sql'] = " SELECT 
        'bet_tot_casino' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
    FROM
        (`member` `MB`
        LEFT JOIN `KP_CSN_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
    WHERE `MB`.idx = ?
         AND `CBH`.`MOD_DTM` >= ?
         AND `CBH`.`MOD_DTM` <= ?
         AND `MB`.`level` <> 9
         AND `MB`.`u_business` = 1 
    UNION ALL SELECT 
        'bet_tot_slot' AS `stype`,
        IFNULL(SUM(`CBH`.`BET_MNY`), 0) AS `total_bet_money`,
        IFNULL(SUM(CASE
                    WHEN `CBH`.`TYPE` = 'W' THEN `CBH`.`BET_MNY` + `CBH`.`RSLT_MNY`
                END),
                0) AS `total_win_money`
    FROM
        (`member` `MB`
        LEFT JOIN `KP_SLOT_BET_HIST` `CBH` ON (`MB`.`idx` = `CBH`.`MBR_IDX`))
    WHERE `MB`.idx = ? 
        AND `CBH`.`MOD_DTM` >= ?
        AND `CBH`.`MOD_DTM` <= ?
        AND `MB`.`level` <> 9
        AND CBH.TYPE IN ('W', 'L')
        AND `MB`.`u_business` = 1";

    $param = array($member_idx,$db_srch_s_date,$db_srch_e_date,$member_idx,$db_srch_s_date,$db_srch_e_date);
    $db_ca_sl_dataArr = $MEMAdminDAO->getQueryData_pre($p_casino_slot_data['sql'],$param);
    //CommonUtil::logWrite("bet status  casino_slot sql ==> " . $p_casino_slot_data['sql'], "info");
    $db_ca_sl_dataArr = true === isset($db_ca_sl_dataArr) && true === isset($db_ca_sl_dataArr) ? $db_ca_sl_dataArr : [];
    

    $MEMAdminDAO->dbclose();

  
   
    $today_total_casino_betting_money = 0;
    $today_total_slot_betting_money = 0;
    $today_total_casino_betting_win_money = 0;
    $today_total_slot_betting_win_money = 0;
    if (true === isset($db_slot_dataArr)) {
        foreach ($db_ca_sl_dataArr as $value) {
            if ($value['stype'] == 'bet_tot_casino') {
                $total_betting_money = $total_betting_money + $value['total_bet_money'];
                $today_total_casino_betting_money = $value['total_bet_money'];
                $total_betting_win_money = $total_betting_win_money + $value['total_win_money'];
                $today_total_casino_betting_win_money = $value['total_win_money'];
            } else if ($value['stype'] == 'bet_tot_slot') {
                $total_betting_money = $total_betting_money + $value['total_bet_money'];
                $today_total_slot_betting_money = $value['total_bet_money'];
                $total_betting_win_money = $total_betting_win_money + $value['total_win_money'];
                $today_total_slot_betting_win_money = $value['total_win_money'];
            }
        }
    }
    
    $db_toDayDataArr['today_total_casino_betting_money'] = $today_total_casino_betting_money;
    $db_toDayDataArr['today_total_slot_betting_money'] = $today_total_slot_betting_money;
    $db_toDayDataArr['today_total_casino_betting_win_money'] = $today_total_casino_betting_win_money;
    $db_toDayDataArr['today_total_slot_betting_win_money'] = $today_total_slot_betting_win_money;

    // 당일 배팅,당첨금
    $today_total_betting_money = $db_arrTodayTotalMemberCash['today_total_bet_money'] + $db_toDayDataArr['mini_bet_sum_d'] + $db_toDayDataArr['today_total_casino_betting_money'] + $db_toDayDataArr['today_total_slot_betting_money'];
    $today_total_betting_win_money = $db_arrTodayTotalMemberCash['today_total_take_bet_money'] + $db_toDayDataArr['mini_take_sum_d'] + $db_toDayDataArr['today_total_casino_betting_win_money'] + $db_toDayDataArr['today_total_slot_betting_win_money'];
    
    // 전체 배팅,당첨금 
    $total_betting_money = $db_arrTotalMemberCash['bet_total_money'] + $db_arrTotalMemberCash['mini_total_money'] + $today_total_betting_money;
    $total_betting_win_money = $db_arrTotalMemberCash['take_total_money'] + $db_arrTotalMemberCash['take_mini_total_money'] + $today_total_betting_win_money;
    
}

// 객단가 
$total_guest_money = 0;
if ($db_arrTotalMemberCash['charge_total_money'] + $db_arrTodayTotalMemberCash['today_total_ch_money'] > 0 && $db_arrTotalMemberCash['charge_total_count'] + $db_arrTodayTotalMemberCash['today_total_ch_count'] > 0) {
    $total_guest_money = ($db_arrTotalMemberCash['charge_total_money'] + $db_arrTodayTotalMemberCash['today_total_ch_money']) / ($db_arrTotalMemberCash['charge_total_count'] + $db_arrTodayTotalMemberCash['today_total_ch_count']);
}

CommonUtil::logWrite("bet status  charge_total_money sql==> : " . $db_arrTotalMemberCash['charge_total_money'], "info");
CommonUtil::logWrite("bet status  today_total_ch_money sql==> : " . $db_arrTodayTotalMemberCash['today_total_ch_money'], "info");
CommonUtil::logWrite("bet status  today_total_bet_money sql==> : " . $db_arrTodayTotalMemberCash['today_total_bet_money'], "info");
CommonUtil::logWrite("bet status  today_total_take_bet_money sql==> : " . $db_arrTodayTotalMemberCash['today_total_take_bet_money'], "info");

$data_str = "
<table>
<tr>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>종합정보</td>
</tr>
</table>
<table class='mlist'>
<tr>
<th style='width:120px;'>총입금</th>
<td style='text-align:right;'>" . number_format($db_arrTotalMemberCash['charge_total_money'] + $db_arrTodayTotalMemberCash['today_total_ch_money']) . " 원</td>
</tr>
<tr>
<th>총출금</th>
<td style='text-align:right;'>" . number_format($db_arrTotalMemberCash['exchange_total_money'] + $db_arrTodayTotalMemberCash['today_total_ex_money']) . " 원</td>
</tr>
<tr>
<th>총차액</th>
<td style='text-align:right;'>" . number_format(($db_arrTotalMemberCash['charge_total_money'] + $db_arrTodayTotalMemberCash['today_total_ch_money']) - ($db_arrTotalMemberCash['exchange_total_money'] + $db_arrTodayTotalMemberCash['today_total_ex_money'])) . " 원</td>
</tr>
<tr>
<th>총베팅금</th>
<td style='text-align:right;'>" . number_format($total_betting_money) . " 원</td>
</tr>
<tr>
<th>총당첨금</th>
<td style='text-align:right;'>" . number_format($total_betting_win_money) . " 원</td>
</tr>
<tr>
<th>객단가</th>
<td style='text-align:right;'>" . number_format($total_guest_money) . " 원</td>
</tr>
</table>
</td>
<td style='vertical-align: top'>
<table class='table_noline'>
<tr>
<td style='background-color:#6F6F6F;color:#fff'>금일 충/환전 내역</td>
</tr>
</table>
<table class='mlist'>
<tr>
<th style='width:120px;'>입금</th>
<td style='text-align:right;'>" . number_format($db_arrTodayTotalMemberCash['today_total_ch_money']) . " 원</td>
</tr>
<tr>
<th>출금</th>
<td style='text-align:right;'>" . number_format($db_arrTodayTotalMemberCash['today_total_ex_money']) . " 원</td>
</tr>
<tr>
<th>차액</th>
<td style='text-align:right;'>" . number_format($db_arrTodayTotalMemberCash['today_total_ch_ex_money_difference']) . " 원</td>
</tr>
<tr>
<th>베팅금</th>
<td style='text-align:right;'>" . number_format($today_total_betting_money) . " 원</td>
</tr>
<tr>
<th>당첨금</th>
<td style='text-align:right;'>" . number_format($today_total_betting_win_money) . " 원</td>
</tr>
<tr>
<th>환전횟수</td>
<td style='text-align:right;'>" . number_format($db_arrTodayTotalMemberCash['today_total_ex_count']) . " 회</td>
</tr>
</table>
</td>
</tr>
</table>";
$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$bet_type_arr = array('mix' => '조합', 'handi' => '핸디캡', 'sp' => '스페셜', 'real' => '라이브', 'etc' => '기타');
$data_str_2 = "
<table class='mlist'>
<tr>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>구분</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>프리매치싱글</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>프리매치멀티</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>실시간싱글</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>실시간멀티</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>미니게임</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>카지노</th>
<th style='width: 10%;background-color:#6F6F6F;color:#fff'>슬롯머신</th>";
if('ON' == IS_ESPORTS_KEYRON){
    $data_str_2 .= "<th style='width: 10%;background-color:#6F6F6F;color:#fff'>E스포츠</th>";
}

if('ON' == IS_HASH){
    $data_str_2 .= "<th style='width: 10%;background-color:#6F6F6F;color:#fff'>해쉬게임</th>";
}
$data_str_2 .= "</tr>

<tr>
<th style='width: 10%;'>총베팅</th>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['pre_s_total_money'] + $db_toDayDataArr['pre_bet_sum_s']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['pre_m_total_money'] + $db_toDayDataArr['pre_bet_sum_d']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['real_s_total_money'] + $db_toDayDataArr['real_bet_sum_s']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['real_m_total_money'] + $db_toDayDataArr['real_bet_sum_d']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['mini_total_money'] + $db_toDayDataArr['mini_bet_sum_d']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['casino_total_money'] + $db_toDayDataArr['today_total_casino_betting_money']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['slot_total_money'] + $db_toDayDataArr['today_total_slot_betting_money']) . " 원</td>";
if('ON' == IS_ESPORTS_KEYRON){
    $data_str_2 .= "<td style='width: 10%; text-align:right;'>" . number_format(0) . " 원</td>";
}
if('ON' == IS_HASH){
    $data_str_2 .= "<td style='width: 10%; text-align:right;'>" . number_format(0) . " 원</td>";
}
    $data_str_2 .= "</tr>
<tr>
<th style='width: 10%;'>총당첨</th>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_pre_s_total_money'] + $db_toDayDataArr['pre_take_sum_s']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_pre_m_total_money'] + $db_toDayDataArr['pre_take_sum_d']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_real_s_total_money'] + $db_toDayDataArr['real_take_sum_s']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_real_m_total_money'] + $db_toDayDataArr['real_take_sum_d']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_mini_total_money'] + $db_toDayDataArr['mini_take_sum_d']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_casino_total_money'] + $db_toDayDataArr['today_total_casino_betting_win_money']) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format($db_arrTotalMemberCash['take_slot_total_money'] + $db_toDayDataArr['today_total_slot_betting_win_money']) . " 원</td>";
if('ON' == IS_ESPORTS_KEYRON){
    $data_str_2 .= "<td style='width: 10%; text-align:right;'>" . number_format(0) . " 원</td>";
}
if('ON' == IS_HASH){
    $data_str_2 .= "<td style='width: 10%; text-align:right;'>" . number_format(0) . " 원</td>";
}
    $data_str_2 .= "</tr>
<tr>
<tr>
<th style='width: 10%;'>차액</th>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['pre_s_total_money'] + $db_toDayDataArr['pre_bet_sum_s']) - ($db_arrTotalMemberCash['take_pre_s_total_money'] + $db_toDayDataArr['pre_take_sum_s'])) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['pre_m_total_money'] + $db_toDayDataArr['pre_bet_sum_d']) - ($db_arrTotalMemberCash['take_pre_m_total_money'] + $db_toDayDataArr['pre_take_sum_d'])) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['real_s_total_money'] + $db_toDayDataArr['real_bet_sum_s']) - ($db_arrTotalMemberCash['take_real_s_total_money'] + $db_toDayDataArr['real_take_sum_s'])) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['real_m_total_money'] + $db_toDayDataArr['real_bet_sum_d']) - ($db_arrTotalMemberCash['take_real_m_total_money'] + $db_toDayDataArr['real_take_sum_d'])) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['mini_total_money'] + $db_toDayDataArr['mini_bet_sum_d']) - ($db_arrTotalMemberCash['take_mini_total_money'] + $db_mini_dataArr['mini_take_sum_d'])) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['casino_total_money'] + $db_toDayDataArr['today_total_casino_betting_money'] ) - ($db_arrTotalMemberCash['take_casino_total_money'] + $db_toDayDataArr['today_total_casino_betting_win_money'])) . " 원</td>
<td style='width: 10%; text-align:right;'>" . number_format(($db_arrTotalMemberCash['slot_total_money'] + $db_toDayDataArr['today_total_slot_betting_money']) - ($db_arrTotalMemberCash['take_slot_total_money'] + $db_toDayDataArr['today_total_slot_betting_win_money'])) . " 원</td>";
if('ON' == IS_ESPORTS_KEYRON){
    $data_str_2 .= "<td style='width: 10%; text-align:right;'>" . number_format(0) . " 원</td>";
}
if('ON' == IS_HASH){
    $data_str_2 .= "<td style='width: 10%; text-align:right;'>" . number_format(0) . " 원</td>";
}
    $data_str_2 .= "</tr>
<tr>";

$data_str_2 .= "</table></div>";

$result['retData_2'] = $data_str_2;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>