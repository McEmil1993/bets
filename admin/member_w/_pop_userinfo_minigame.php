<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_Cash_dao.php');
include_once(_LIBPATH . '/class_GameUtil.php');
include_once(_LIBPATH . '/class_GameStatusUtil.php');



function gameResult($type, $result) {
    $result = json_decode($result);
    //print_r($result);
    $gameResult = "";
    switch($type) {
        case 'eospb5':
        case 'powerball':
            if(!empty($result->num1)){
                if (0 !== $result->pb % 2) {
                    $oddEven = '[홀]';
                }else if (0 === $result->pb % 2) {
                    $oddEven = '[짝]';
                }

                if (5 <= $result->pb && $result->pb <= 9) {
                    $overUnder = '[오버]';
                }else if (0 <= $result->pb && $result->pb <= 4) {
                    $overUnder = '[언더]';
                }

                $sum = $result->num1 + $result->num2 + $result->num3 + $result->num4 + $result->num5;
                if (81 <= $sum && $sum <= 130) {
                    $sumCal = '[대]';
                }else if (65 <= $sum && $sum <= 80) {
                    $sumCal = '[중]';
                }else{
                    $sumCal = '[소]';
                }
                $gameResult = "$oddEven, $overUnder,$sumCal";
            }
            break;
        case 'kladder':
            if(!empty($result -> oe)) {
                $oe = GameStatusUtil::get_minigame_result_name($result->oe);
                $start = GameStatusUtil::get_minigame_result_name($result->start);
                $gameResult = "[$start],[$result->line],[$oe]";
            }
            break;
        case 'pladder':
            if(!empty($result -> oe)) {
                $oe = GameStatusUtil::get_minigame_result_name($result->oe);
                $start = GameStatusUtil::get_minigame_result_name($result->start);
                $gameResult = "[$start],[$result->line],[$oe]";
            }
            break;
        case 'b_soccer':
            //$type = GameStatusUtil::get_minigame_result_name($result->type);
            $res = GameStatusUtil::get_minigame_result_name($result->res);
            $gameResult = $res;
            break;
        default:
            break;
    }
    
    return $gameResult;
}

// 현재 프로토콜
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = date("Y/m/d");

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if ($db_conn) {

    $p_data['m_idx'] = $CASHAdminDAO->real_escape_string($p_data['m_idx']);
    
    $p_data['sql'] = "SELECT b.idx AS bet_idx, 
                            a.idx AS m_idx, 
                            a.id as m_id,
                            a.nick_name,
                            m.game,
                            m.markets_id,
                            b.ls_fixture_id,
                            b.ls_markets_name,
                            b.bet_price,
                            b.total_bet_money,
                            b.take_money,
                            b.create_dt,
                            b.bet_status,
                            b.bet_type,
                            g.result,
                            g.result_score,
                            g.start_dt
                        FROM mini_game_member_bet AS b
                        LEFT JOIN mini_game_bet AS m ON m.markets_id = b.ls_markets_id
                        LEFT JOIN member AS a ON b.member_idx = a.idx
                        LEFT JOIN mini_game AS g ON g.id = b.ls_fixture_id";
    // $p_data['sql'] .= " WHERE (CASE WHEN b.member_idx IN($test_expt_member_idxs) THEN b.member_idx IN($test_expt_member_idxs) AND b.bet_status = 3 ELSE 1 = 1 END)";
    $p_data['sql'] .= " WHERE (CASE WHEN b.member_idx IN(' " . $p_data['m_idx'] . "') THEN b.member_idx IN(' " . $p_data['m_idx'] . "') AND b.bet_status = 3 ELSE 1 = 1 END)";
    $p_data['sql'] .= " AND b.member_idx = " . $p_data['m_idx'] . " ";
    $p_data['sql'] .= ' order by b.create_dt desc';
    $p_data['sql'] .= " LIMIT 10 ";
    $p_data['sql'] .= ";";
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $CASHAdminDAO->dbclose();
}

$data_str = "";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$data_str_2 = "
<table class='mlist scroll_mlist'>
<tr>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>게임종류</td> 
<td width='5%' style='background-color:#6F6F6F;color:#fff'>회차</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>경기번호</td>
<td width='12%' style='background-color:#6F6F6F;color:#fff'>배팅진행내역</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>배당율</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>배팅액</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>적중금</td>
<td width='14%' style='background-color:#6F6F6F;color:#fff'>배팅시간</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>배팅결과</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>게임결과</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>취소</td>
</tr>
</table>
<div class='tline scroll_tline' style='max-height: 460px;'>
<table class='mlist scroll_mlist'>";

// 
if (count($db_dataArr) > 0) {
    foreach ($db_dataArr as $row) {

        $bet_idx = $row['bet_idx'];

        $status = '';

        $gameResult = !empty($row['game']) && !empty($row['result']) ? gameResult($row['game'], $row['result']) : [];
        $gameResult = !empty($row['result_score']) ? gameResult($row['game'], $row['result_score']) : $gameResult;

        $status = '';
        $status_color = '';

        if ($row['markets_id'] == '10001' || $row['markets_id'] == '14001') {
            if (strpos($gameResult, '홀') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10002' || $row['markets_id'] == '14002') {
            if (strpos($gameResult, '짝') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10003' || $row['markets_id'] == '14003') {
            if (strpos($gameResult, '오버') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10004' || $row['markets_id'] == '14004') {
            if (strpos($gameResult, '언더') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10005' || $row['markets_id'] == '14005') {
            if (strpos($gameResult, '대') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10006' || $row['markets_id'] == '14006') {
            if (strpos($gameResult, '중') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10007' || $row['markets_id'] == '14007') {
            if (strpos($gameResult, '소') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        /*} else if ($row['markets_id'] == '10007') {
            if (strpos($gameResult, '소') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '10007') {
            if (strpos($gameResult, '소') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }*/
        } else if ($row['markets_id'] == '11001') {
            if (strpos($gameResult, '좌') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '11002') {
            if (strpos($gameResult, '우') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '11003') {
            if (strpos($gameResult, '3') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '11004') {
            if (strpos($gameResult, '4') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '11005') {
            if (strpos($gameResult, '홀') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '11006') {
            if (strpos($gameResult, '짝') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '12001') {
            if (strpos($gameResult, '좌') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '12002') {
            if (strpos($gameResult, '우') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '12003') {
            if (strpos($gameResult, '3') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '12004') {
            if (strpos($gameResult, '4') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '12005') {
            if (strpos($gameResult, '홀') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '12006') {
            if (strpos($gameResult, '짝') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '13001') {
            if (strpos($gameResult, '승') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '13002') {
            if (strpos($gameResult, '무') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '13003') {
            if (strpos($gameResult, '패') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '13004') {
            if (strpos($gameResult, '오버') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        } else if ($row['markets_id'] == '13005') {
            if (strpos($gameResult, '언더') !== false) {
                $status = '적중';
                $status_color = '#9FD5E9';
            } else {
                $status = '낙첨';
                $status_color = '#F9BFBF';
            }
        }

        if ($gameResult == "") {
            $status = '결과전';
            $status_color = '';
        }
        if ($row['total_bet_money'] == $row['take_money'] || $row['bet_status'] == 5) {
            $status = '취소';
            $status_color = '#FFFACC';
        }

        $cnt = 0;
        if($row['bet_type'] == 3){
            $cnt = json_decode($row['result'], true)['dround'];
        }else if($row['bet_type'] == 15){
            $time = explode(' ', $row['start_dt'])[1];
            $time = explode(':', $time);
            $cnt = round((($time[0] * 60) + $time[1]) / 5) + 1;
        } else if ($row['bet_type'] == 6) {
            $cnt = json_decode($row['result'], true)['oid'];
        } else {
            $cnt = json_decode($row['result'], true)['cnt'];
        }

        $data_str_2 .= "<tr>";
        //$data_str_2 .= "<td width='5%'>".$row['m_id']."</td>";
        $data_str_2 .= "<td width='10%'>".GameStatusUtil::get_minigame_name($row['game'])."</td>";
        $data_str_2 .= "<td width='5%'>".$cnt."</td>";
        $data_str_2 .= "<td width='10%'>".$row['ls_fixture_id']."</td>";
        $data_str_2 .= "<td width='12%'>".$row['ls_markets_name']."</td>";
        $data_str_2 .= "<td width='10%'>".$row['bet_price']."</td>";
        $data_str_2 .= "<td width='10%'>".number_format($row['total_bet_money'])."</td>";
        $data_str_2 .= "<td width='10%'>".number_format($row['take_money'])."</td>";
        $data_str_2 .= "<td width='14%'>".$row['create_dt']."</td>";
        $data_str_2 .= "<td width='10%' style='background-color:".$status_color."'; >".$status."</td>";
        CommonUtil::logWrite($data_str_2,"pop_memo");
        $data_str_2 .= "<td width='10%'>".$gameResult."</td>";
        $data_str_2 .= "<td width='10%'><a href='javascript:fn_cancel($bet_idx);' class='btn h25 btn_blu'>취소</a></td>";
        $data_str_2 .= "</tr>";
    }
} else {
    $data_str_2 .= "<tr><td>데이터가 없습니다.</td><tr>";
}
$data_str_2 .= "</table></div>";

$result['retData_2'] = $data_str_2;

//$log_str = "[_pop_userinfo_pointlog] [".$result['retData']."]";
//$UTIL->logWrite($log_str,"pop_memo");

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>