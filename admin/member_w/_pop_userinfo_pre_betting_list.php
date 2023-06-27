<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Cash_dao.php');
include_once(_LIBPATH . '/class_GameUtil.php');

$UTIL = new CommonUtil();

// 현재 프로토콜
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['p_seltype'] = trim($_POST['p_seltype'])==7?'OFF':'ON';
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = date("Y/m/d");

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

    
if($db_conn) {
    $p_data['m_idx'] = $CASHAdminDAO->real_escape_string($p_data['m_idx']);
    $p_data['sql'] = "SELECT member_bet.*, member.idx as member_idx, member.id, member.nick_name";
    $p_data['sql'] .= ", (SELECT mi.item_id FROM member_item mi WHERE mi.idx=member_bet.item_idx) AS item_id ";
    $p_data['sql'] .= " FROM member_bet";
    //$p_data['sql'] .= " left join member_bet_detail on member_bet_detail.bet_idx = member_bet.idx";
    $p_data['sql'] .= " left join member on member_bet.member_idx = member.idx";
    $p_data['sql'] .= " WHERE member_bet.bet_type = 1 ";
    $p_data['sql'] .= "AND member.idx = ".$p_data['m_idx']." AND is_classic = ?";
    $p_data['sql'] .= " group by member_bet.idx";
    $p_data['sql'] .= " order by create_dt desc limit 50 ";
    $p_data['sql'] .= ";";
    
    //$UTIL->logWrite("_pop_userinfo_pre_betting_list query 1: " . $p_data['sql'] , "info");
    // $p_data['sql'] .= " LIMIT 50;";
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], [$p_data['p_seltype']]);
    //$UTIL->logWrite("_pop_userinfo_pre_betting_list query 1: " . json_encode($db_dataArr) , "info");
    $detail_list = [];
    foreach ($db_dataArr as $key => $item){
        $detail_list[] = $item['idx'];
    }

    $szDetail_list = implode(',', $detail_list);
    $tmDeatil = [];
    if(count($detail_list) > 0){
        $p_data['sql'] = "select member_bet_detail.ls_fixture_id, member_bet_detail.idx, member_bet_detail.bet_idx, member_bet_detail.bet_status, member_bet_detail.bet_status as display_bet_status
                , IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity,lsports_fixtures.fixture_start_date) as fixture_start_date
                , member_bet_detail.result_score, ";
        $p_data['sql'] .= "ls_markets_name, ls_markets_base_line, lsports_leagues.display_name as fixture_league_name"
                //. ", IF('ON' = mb_bet.is_betting_slip , IF('ON' = lsports_bet.passivity_flag AND lsports_bet.bet_price_passivity is NOT NULL ,lsports_bet.bet_price_passivity,lsports_bet.bet_price) ,member_bet_detail.bet_price) as bet_price"
                . ", member_bet_detail.bet_price as bet_price"
                . ", member_bet_detail.bet_name, lsports_fixtures.fixture_status, ";
        $p_data['sql'] .= "p1.team_name as p1_team_name, p1.display_name as fixture_participants_1_name, p2.team_name as p2_team_name, p2.display_name as fixture_participants_2_name from member_bet_detail ";
        $p_data['sql'] .= " LEFT JOIN member_bet as mb_bet on mb_bet.idx = member_bet_detail.bet_idx ";
        $p_data['sql'] .= " join lsports_fixtures on member_bet_detail.ls_fixture_id = lsports_fixtures.fixture_id";
        $p_data['sql'] .= " join lsports_bet on member_bet_detail.ls_fixture_id = lsports_bet.fixture_id";
        $p_data['sql'] .= " join lsports_participant as p1 on lsports_fixtures.fixture_participants_1_id = p1.fp_id";
        $p_data['sql'] .= " join lsports_participant as p2 on lsports_fixtures.fixture_participants_2_id = p2.fp_id";
        $p_data['sql'] .= " join lsports_leagues on member_bet_detail.fixture_league_id = lsports_leagues.id";
        
        $p_data['sql'] .= " where bet_idx in ($szDetail_list) and member_bet_detail.bet_type = 1 AND lsports_bet.bet_type = 1";
        $p_data['sql'] .= " group by idx;";
        
        $tmDeatil = $CASHAdminDAO->getQueryData($p_data);
        //$UTIL->logWrite("_pop_userinfo_pre_betting_list query 2: " . json_encode($tmDeatil) , "info");
        
    }

    $db_dataArrDetail = null;
    $tmDeatil = true === isset($tmDeatil) && false === empty($tmDeatil) ? $tmDeatil : [];
    foreach ($tmDeatil as $key => $item){
        $db_dataArrDetail[$item['bet_idx']][] = $item;
    }
    unset($tmDeatil);
    //$UTIL->logWrite("_pop_userinfo_pre_betting_list query 3: " . json_encode($db_dataArrDetail) , "info");
    
    $p_data['sql'] = "select id, name from item where 1=1;";
    $tmItem = $CASHAdminDAO->getQueryData($p_data);    
    $itemList = array();
    if (true === isset($tmItem) && false === empty($tmItem)) {
        foreach ($tmItem as $key => $item) {
            $itemList[$item['id']] = $item;
        }
    }
    unset($tmItem);
    $CASHAdminDAO->dbclose();
}

$data_str = "";

$result['retCode'] = 1000;
$result['retData_1'] = $data_str;

$data_str_2 = "
<table class='mlist scroll_mlist'>
<tr>
<td width='5%' style='background-color:#6F6F6F;color:#fff'>번호</td>
<td width='5%' style='background-color:#6F6F6F;color:#fff'>게임수</td>
<td width='20%' style='background-color:#6F6F6F;color:#fff'>배팅진행내역</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>배당율(보너스)</td>
<td width='7%' style='background-color:#6F6F6F;color:#fff'>배팅액</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>예상당첨액</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>적중금</td>
<td width='20%' style='background-color:#6F6F6F;color:#fff'>배팅시간</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>배팅타입</td>
<td width='5%' style='background-color:#6F6F6F;color:#fff'>결과</td>
<td width='10%' style='background-color:#6F6F6F;color:#fff'>기능</td>
</tr>
</table>
<div class='tline scroll_tline' style='max-height: 460px;'>
<table class='mlist'>";

if($db_dataArr > 0) {
    foreach ($db_dataArr as $key => $item) {
        //$UTIL->logWrite("_pop_userinfo_pre_betting_list query 4: " . json_encode($item) , "info");
        
        $gameCount = !empty($db_dataArrDetail[$item['idx']]) ? count($db_dataArrDetail[$item['idx']]) : 0;
        
        $data_str_2 .="<table class='mlist scroll_mlist'>";
        $data_str_2 .="<tr><td width='5%'>".$item['idx']."</td>";
        $data_str_2 .="<td width='5%'>".$gameCount."</td>";
        $data_str_2 .="<td width='20%' id='open_betting_detail_".$item['idx']."' onClick='open_betting_detail(".$item['idx'].",".$item['is_open'].")'>";
        
        $betViewCount = 0; //배팅진행내역 카운트
        $totalBetPrice = 1; // 배당률
        list($status, $status_color) = getBetResult($item['bet_status'], $item['total_bet_money'], $item['take_money'], $item['cancel_type']);                    
        $status_arr = !empty($db_dataArrDetail[$item['idx']]) ? $db_dataArrDetail[$item['idx']] : [];
        foreach ($status_arr as $key => $value) {
            $totalBetPrice *= $value['bet_price'];
            $color = '';
            if($value['display_bet_status'] == 1){
                $color = 'btn_while';
                if($value['fixture_status'] == 5){
                    $color = 'btn_yellow';
                }
            }else if($value['display_bet_status'] == 2){
                $color = 'btn_blu';
            }else if($value['display_bet_status'] == 4){
                $color = 'btn_red';
            }else if($value['display_bet_status'] == 5){
                $color = 'btn_gray';
            }else if($value['display_bet_status'] == 6){
                $color = 'btn_yellow';
            }
            
            //if($betViewCount >= 8) { $betViewCount = 0; echo '<br>'; }
            if($betViewCount >= 8) { $betViewCount = 0; $data_str_2.='<br>'; }
            $bet_name = !empty($value['bet_name']) ? $value['bet_name'] : '';
            $ls_markets_id = !empty($value['ls_markets_id']) ? $value['ls_markets_id'] : '';
            $data_str_2 .="<a class='btn h30 ".$color."' style='width: 25px; padding: 0px; margin: 0px; border: 1px solid black; color:black'>".GameStatusUtil::betNameToDisplay_new($bet_name, $ls_markets_id)."</a>";
            $betViewCount ++;
        }
        $data_str_2 .="</td>";

        $total_bet_money = !empty($item['total_bet_money']) ? number_format($item['total_bet_money']) : 0;
        $display_totalBetPrice = !empty($totalBetPrice) ? number_format($totalBetPrice,2) : 0;
        $bonusPrice = '';
        if($item['bonus_price'] > 0) $bonusPrice = '('.$item['bonus_price'].')';
        $data_str_2 .="<td width='10%' style='text-align:center'>".$display_totalBetPrice.$bonusPrice."</td>";
        $data_str_2 .="<td width='7%' style='text-align:right'>".$total_bet_money."</td>";
        $rating = round($totalBetPrice * $item['bonus_price'], 2) * $item['total_bet_money'];
        $rating = !empty($rating) ? number_format($rating) : 0;
        $take_money_color = 'color:blue;';
        if($item['bet_status'] == 3 || $item['bet_status'] == 5)
            $take_money_color = 'color:red;';
        //$itemName = '';
        //if(!is_null($item['item_id']))
        //    $itemName = $itemList[$item['item_id']]['name'];
        
        $is_classic = 'OFF' == $item['is_classic'] ? '스포츠' : '클래식';
        $data_str_2 .="<td width='10%' style='text-align:right'>".$rating."</td>";
        $data_str_2 .="<td width='10%' style='text-align:right; ".$take_money_color."'>".number_format($item['take_money'])."</td>";
        $data_str_2 .="<td width='20%'>".$item['create_dt']."</td>";
        $data_str_2 .="<td width='10%'>".$is_classic."</td>";
        $data_str_2 .="<td width='5%' bgcolor='".$status_color."' id='open_betting_detail_".$item['idx']."' onClick='open_betting_detail(".$item['idx'].",".$item['is_open'].")'>".$status."</td>";
        $data_str_2 .="<td width='10%'>";
        $data_str_2 .="<a href='javascript:onBetCancel(".$item['idx'].",".$item['bet_status'].")' class='btn h30 btn_gray'>취소</a>";
        $data_str_2 .="</td>";
        $data_str_2 .="</tr>";
        $data_str_2 .="</table>";
        
        $data_str_2 .="<table id=betting_detail_".$item['idx']." class='mlist separate_table' style='display:none'>";
        $data_str_2 .="<tr>";
        $data_str_2 .="<th width='15%'>경기시간</th>";
        $data_str_2 .="<th width='10%'>리그</th>";
        $data_str_2 .="<th width='15%'>홈</th>";
        $data_str_2 .="<th>VS</th>";
        $data_str_2 .="<th width='15%'>원정</th>";
        $data_str_2 .="<th>타입</th>";
        $data_str_2 .="<th>베팅</th>";
        $data_str_2 .="<th>게임결과</th>";
        $data_str_2 .="</tr>";
        $result_row = !empty($db_dataArrDetail[$item['idx']]) ? $db_dataArrDetail[$item['idx']] : [];
        foreach ($result_row as $key => $item2) {
            $result_score = '-';
            if(isset($item2['result_score'])){
                if($item2['result_score'] != ''){
                    $item2['result_score'] = stripslashes($item2['result_score']);
                    $arrScore = explode('result_extra', $item2['result_score']);
                    if(count($arrScore) == 1)
                        $score = substr($arrScore[0], 0, -1) . '}';
                    else
                        $score = substr($arrScore[0], 0, -2) . '}';
                    $json_result = json_decode($score, true);
                    $result_score = $json_result['live_results_p1'].':'.$json_result['live_results_p2'];
                    /*$json_result = json_decode ($item2['result_score'], true);
                    if($json_result['result_extra'] != '')
                        $result_score = $json_result['result_extra'];
                    else
                        $result_score = $json_result['live_results_p1'].':'.$json_result['live_results_p2'];*/
                }
            }

            $betNameDisplay = $item2['ls_markets_name'];

            if(strlen($item2['ls_markets_base_line']) > 0){
                $betNameDisplay = $betNameDisplay .'(' . explode(' ', $item2['ls_markets_base_line'])[0] . ')';
            }
            
            $data_str_2 .="<tr>";
            $data_str_2 .="<td>".$item2['fixture_start_date']."</td>";
            $data_str_2 .="<td>".$item2['fixture_league_name']."</td>";
            $team1 = isset($item2['fixture_participants_1_name'])?$item2['fixture_participants_1_name']:$item2['p1_team_name'];
            $team2 = isset($item2['fixture_participants_2_name'])?$item2['fixture_participants_2_name']:$item2['p2_team_name'];
            $data_str_2 .="<td><a target='_blank' href='".$protocol.$_SERVER['HTTP_HOST']."/sports_w/prematch_manager_detail.php?fixture_start_date=".urlencode($item2['fixture_start_date'])."&fixture_id=".$item2['ls_fixture_id']."'>".$team1."</a></td>";
            $data_str_2 .="<td>".$item2['bet_price']."</td>";
            $data_str_2 .="<td><a target='_blank' href='".$protocol.$_SERVER['HTTP_HOST']."/sports_w/prematch_manager_detail.php?fixture_start_date=".urlencode($item2['fixture_start_date'])."&fixture_id=".$item2['ls_fixture_id']."'>".$team2."</a></td>";
            $data_str_2 .="<td>".$betNameDisplay."</td>";
            
            $ls_market_id =array_key_exists('ls_market_id', $item2) ? $item2['ls_market_id'] : '-';
            $bet_name =array_key_exists('bet_name', $item2) ? $item2['bet_name'] : '-';
            $gamestate = !empty(GameStatusUtil::betNameToDisplay_new($bet_name,$ls_market_id)) ? GameStatusUtil::betNameToDisplay_new($bet_name,$ls_market_id) : '-';
            
            $data_str_2 .="<td>".$gamestate."(".$item2['bet_price'].")</td>";
            $data_str_2 .="<td>".$result_score."</td>";
            $data_str_2 .="</tr>";
        }
        $data_str_2 .="</table>";
    }
}
else {
    $data_str_2 .="<tr><td>데이터가 없습니다.</td><tr>";
}

$data_str_2 .="</table></div>";

$result['retData_2'] = $data_str_2;

//$log_str = "[_pop_userinfo_pointlog] [".$result['retData']."]";
//$UTIL->logWrite($log_str,"pop_memo");
//$UTIL->logWrite("_pop_userinfo_pre_betting_list query 5: " . json_encode($result) , "info");
echo json_encode($result, JSON_UNESCAPED_UNICODE);

function getBetResult($status, $total_bet_money, $take_money, $cancel_type){
    $color = '#ffffff';
    $result = '결과전';

    if (1 == $status){
        $result = '결과전';
        $color = '#ffffff';
    }else if (5 == $status){
        if($cancel_type == 1)
            $result = '관리자 취소';
        else
            $result = '취소';
        $color = '#c9c9c9';
    }else{
        if ($total_bet_money < $take_money){
            $result = '적중';
            $color = '#9fd5e9';
        }
        if (0 == $take_money || (0 < $take_money && $total_bet_money > $take_money)){
            $result = '낙첨';
            $color = '#f9bfbf';
        }
        if ($total_bet_money == $take_money){
            $result = '적특';
            $color = '#ffefa5';
        }
    }
    
    return array($result, $color);
}
?>