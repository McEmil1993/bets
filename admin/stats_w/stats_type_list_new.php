<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');

$UTIL = new CommonUtil();


if(0 != $_SESSION['u_business']){
    die();
}
include_once(_BASEPATH . '/common/login_check.php');

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", mktime(0, 0, 0, intval(date('m')), 1, intval(date('Y'))));
$end_date = date("Y/m/d");
$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);
$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);
// datetime 쿼리 용도
$db_srch_s_date = isset($p_data['db_srch_s_date']) ? $p_data['db_srch_s_date'] . " 00:00:00" : "NULL";
$db_srch_e_date = isset($p_data['db_srch_e_date']) ? $p_data['db_srch_e_date'] . " 23:59:59" : "NULL";
// 1: 프리매치, 2: 실시간, 3: 미니게임
$srch_type = trim(isset($_REQUEST['srch_type']) ? $_REQUEST['srch_type'] : '1');
$srch_sports = trim(isset($_REQUEST['srch_sports']) ? $_REQUEST['srch_sports'] : '6046');

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$CASHAdminDAO)){
        die();
    }
    $begin = new DateTime($db_srch_s_date);
    $end = new DateTime($db_srch_e_date);

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);

    $total_cnt = 0;

    $period = isset($period) ? $period : [];
    foreach ($period as $dt) {
        $str_dt = $dt->format("Ymd");
        // echo "[".$str_dt.",".strlen($str_dt)."]";
        if (strlen($str_dt) > 5) {

            $stats_day[$str_dt]['ymd'] = $dt->format("Y-m-d");
            $stats_day[$str_dt]['val'] = 0;

            $total_cnt++;
        }
    }

    $str_dt = str_replace('-', '', $p_data['db_srch_e_date']);
    $stats_day[$str_dt]['val'] = 0;
    $stats_day[$str_dt]['ymd'] = $p_data['db_srch_e_date'];

    // 스포츠
    if ($srch_type == '1' || $srch_type == '2') {
        // 배팅 합계용 변수
        $tot_bet_sum = 0;
        $tot_take_sum = 0;

        // 9레벨 멤버 베팅 제외용
        $p_data['sql'] = " SELECT DATE(calculate_dt) AS cr_dt, 
                        MBB.idx AS bet_idx
                            FROM member AS T1
                            LEFT JOIN 
                                member_bet AS MBB
                            ON 
                                MBB.member_idx = T1.idx";
        $p_data['sql'] .= " WHERE T1.level = 9 AND calculate_dt >= '" . $db_srch_s_date . "' AND calculate_dt <= '" . $db_srch_e_date . "' ";
        $p_data['sql'] .= " AND bet_status = 3 
                            AND total_bet_money != take_money
                            ";
        $p_data['sql'] .= " GROUP BY cr_dt, bet_idx ";
        $p_data['sql'] .= ";";

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);

        $db_dataArr =
            $nineBetArr = isset($db_dataArr) ? $db_dataArr : [];

        $nineBetIdStr = '';
        if (!empty($nineBetArr)) {
            $nineBetIdStr = '(';
            foreach ($nineBetArr as $bet) {
                $nineBetIdStr .= $bet['bet_idx'];
                $nineBetIdStr .= ',';
            }

            $nineBetIdStr = substr($nineBetIdStr, 0, -1);
            $nineBetIdStr .= ')';
        }

        // 마켓 배팅
        $p_data['sql'] = "SELECT DATE(T1.calculate_dt) AS cr_dt, 
                            MBD.ls_markets_id AS market,
                            MBD.ls_markets_name AS market_name,
                            (CASE
                                WHEN fix.fixture_sport_id IS NOT null
                                THEN fix.fixture_sport_id
                                WHEN MBD.fixture_sport_id != 0
                                THEN MBD.fixture_sport_id
                                ELSE null END)   AS fixture_sport_id,
                            T1.idx AS bet_idx,
                            T1.bet AS bet,
                            T1.take AS take,
                            T1.bet - T1.take AS diff
                    FROM (SELECT MBB.idx AS idx,
                                MBB.total_bet_money AS bet,
                                -- sum(MBB.total_bet_money) AS bet_sum,
                                MBB.take_money AS take,
                                MBB.calculate_dt AS calculate_dt
                            FROM member_bet MBB 
                            WHERE MBB.bet_status = 3
                                    AND calculate_dt >= '$db_srch_s_date' 
                                    AND calculate_dt <= '$db_srch_e_date'
                                    AND total_bet_money != take_money
                                    ";
        // 9레벨 배팅금액 제외
        if (strlen($nineBetIdStr) > 2) {
            $p_data['sql'] .= " AND MBB.idx NOT IN $nineBetIdStr ";
        }
        if ($srch_type == '1') {
            $p_data['sql'] .= " AND bet_type = 1 ";
        }
        if ($srch_type == '2') {
            $p_data['sql'] .= " AND bet_type = 2 ";
        }

        $p_data['sql'] .= ") AS T1
                    LEFT JOIN 
                            member_bet_detail AS MBD
                    ON 
                        MBD.bet_idx = T1.idx
                    LEFT JOIN 
                        lsports_fixtures AS fix
                    ON 
                        fix.fixture_id = MBD.ls_fixture_id
                    WHERE 1 = 1
                    AND (CASE
                    WHEN fix.fixture_sport_id IS NOT null
                    THEN fix.fixture_sport_id
                    WHEN MBD.fixture_sport_id != 0
                    THEN MBD.fixture_sport_id
                    ELSE null END) = $srch_sports";
        $p_data['sql'] .= " GROUP BY T1.idx, market";
        $p_data['sql'] .= " ORDER BY bet DESC, market";
        $p_data['sql'] .= ";";

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);

        $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

        $total = [];
        $market_arr = [];
        foreach ($db_dataArr as $row) {
            
            $str_dt = str_replace('-', '', $row['cr_dt']);
            $str_market = $row['market_name'];

            // 해당 마켓 키 있으면 더하고 없으면 초기화
            if (isset($stats_day[$str_dt][$str_market])) {
                $stats_day[$str_dt][$str_market] = array(
                    "bet" => $stats_day[$str_dt][$str_market]['bet'] + $row['bet'],
                    "take" => $stats_day[$str_dt][$str_market]['take'] + $row['take'],
                    "diff" => $stats_day[$str_dt][$str_market]['diff'] + $row['diff']
                );
            } else {
                $stats_day[$str_dt][$str_market] = array(
                    "market" => $str_market,
                    "bet" => $row['bet'],
                    "take" => $row['take'],
                    "diff" => $row['diff']
                );
            }

            // 해당 마켓 키 있으면 더하고 없으면 초기화
            if (isset($total[$str_market])) {
                $total[$str_market]['bet_sum'] += $row['bet'];
                $total[$str_market]['take_sum'] += $row['take'];
                $total[$str_market]['diff_sum'] += $row['diff'];
            } else {
                $total[$str_market]['market'] = $str_market;
                $total[$str_market]['bet_sum'] = $row['bet'];
                $total[$str_market]['take_sum'] = $row['take'];
                $total[$str_market]['diff_sum'] = $row['diff'];
            }
        }

        // 배팅 합계기준 내림차순 정렬 함수
        function bet_sum_sort($a, $b)
        {
            return $b['bet_sum'] - $a['bet_sum'];
        }
        // 배팅 기준 내림차순 정렬 함수
        function bet_sort($a, $b)
        {
            return $b['bet'] - $a['bet'];
        }

        // total + 날짜별 배팅 desc 정렬

        // $temp = $total[""];
        // unset($total[""]);
        uasort($total, "bet_sum_sort");
        // $total = $total + array($temp);

        foreach ($total as $key => $market) {
            array_push($market_arr, $market['market']);
        }
        // 기타(null) 맨뒤로
        foreach ($stats_day as $key => $day) {
            $temp_arr = array('ymd' => $day['ymd'], 'val' => $day['val']);
            $sort_arr = array_diff_assoc($day, $temp_arr);
            uasort($sort_arr, "bet_sort");
            $stats_day[$key] = $temp_arr + $sort_arr;
        }

        // 활성종목
        $p_data['sql'] = "SELECT
                id,
                name,
                display_name
            FROM lsports_sports
            WHERE is_use = 1
            GROUP BY id;
            ";

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
        
        $p_data['sport'] = [];
        foreach ($db_dataArr as $sport) {
            array_push($p_data['sport'], $sport);
        }
    }

    // 미니게임
    if ($srch_type == '3') {
        // 미니게임 베팅
        $p_data['sql'] = " SELECT DATE(calculate_dt) AS cr_dt, 
                            
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 3
                                    THEN total_bet_money ELSE 0 END), 0) AS powerball_bet_sum,
            
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 3
                                    THEN take_money ELSE 0 END), 0) AS powerball_take_sum,
    
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 3
                                    THEN total_bet_money ELSE 0 END), 0)
                        - 	IFNULL(SUM(CASE 
                                    WHEN bet_type = 3
                                    THEN take_money ELSE 0 END), 0) AS powerball_sum,
                                    
    
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 4
                                    THEN total_bet_money ELSE 0 END), 0) AS pladder_bet_sum,

                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 4
                                    THEN take_money ELSE 0 END), 0) AS pladder_take_sum,

                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 4
                                    THEN total_bet_money ELSE 0 END), 0)
                        - 	IFNULL(SUM(CASE 
                                    WHEN bet_type = 4
                                    THEN take_money ELSE 0 END), 0) AS pladder_sum,
                                    
                                    
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 5
                                    THEN total_bet_money ELSE 0 END), 0) AS kladder_bet_sum,

                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 5
                                    THEN take_money ELSE 0 END), 0) AS kladder_take_sum,

                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 5
                                    THEN total_bet_money ELSE 0 END), 0)
                        - 	IFNULL(SUM(CASE 
                                    WHEN bet_type = 5
                                    THEN take_money ELSE 0 END), 0) AS kladder_sum,
                                    
                                    
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 6
                                    THEN total_bet_money ELSE 0 END), 0) AS bsoccer_bet_sum,
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 6
                                    THEN take_money ELSE 0 END), 0) AS bsoccer_take_sum,
                            IFNULL(SUM(CASE 
                                    WHEN bet_type = 6
                                    THEN total_bet_money ELSE 0 END), 0)
                            - 	IFNULL(SUM(CASE 
                                    WHEN bet_type = 6
                                    THEN take_money ELSE 0 END), 0) AS bsoccer_sum
                            FROM member AS T1
                            LEFT JOIN 
                                mini_game_member_bet
                            ON 
                                mini_game_member_bet.member_idx = T1.idx";
        $p_data['sql'] .= " WHERE T1.level != 9 AND calculate_dt >= '" . $db_srch_s_date . "' AND calculate_dt <= '" . $db_srch_e_date . "' ";
        $p_data['sql'] .= " AND bet_status = 3 
                            AND total_bet_money != take_money
                            ";
        $p_data['sql'] .= " GROUP BY cr_dt";

        // CommonUtil::logWrite("stats_day_list member_bet total_bet_money : " . $p_data['sql'], "info");

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);

        $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];


        $tot_powerball_bet_sum = 0;
        $tot_powerball_take_sum = 0;
        $tot_powerball_sum = 0;

        $tot_pladder_bet_sum = 0;
        $tot_pladder_take_sum = 0;
        $tot_pladder_sum = 0;

        $tot_kladder_bet_sum = 0;
        $tot_kladder_take_sum = 0;
        $tot_kladder_sum = 0;

        $tot_bsoccer_bet_sum = 0;
        $tot_bsoccer_take_sum = 0;
        $tot_bsoccer_sum = 0;

        foreach ($db_dataArr as $row) {
            $str_dt = str_replace('-', '', $row['cr_dt']);
            $stats_day[$str_dt]['powerball_bet_sum'] = $row['powerball_bet_sum'];
            $tot_powerball_bet_sum += $row['powerball_bet_sum'];

            $stats_day[$str_dt]['powerball_take_sum'] = $row['powerball_take_sum'];
            $tot_powerball_take_sum += $row['powerball_take_sum'];

            $stats_day[$str_dt]['powerball_sum'] = $row['powerball_sum'];
            $tot_powerball_sum += $row['powerball_sum'];


            $stats_day[$str_dt]['pladder_bet_sum'] = $row['pladder_bet_sum'];
            $tot_pladder_bet_sum += $row['pladder_bet_sum'];

            $stats_day[$str_dt]['pladder_take_sum'] = $row['pladder_take_sum'];
            $tot_pladder_take_sum += $row['pladder_take_sum'];

            $stats_day[$str_dt]['pladder_sum'] = $row['pladder_sum'];
            $tot_pladder_sum += $row['pladder_sum'];


            $stats_day[$str_dt]['kladder_bet_sum'] = $row['kladder_bet_sum'];
            $tot_kladder_bet_sum += $row['kladder_bet_sum'];

            $stats_day[$str_dt]['kladder_take_sum'] = $row['kladder_take_sum'];
            $tot_kladder_take_sum += $row['kladder_take_sum'];

            $stats_day[$str_dt]['kladder_sum'] = $row['kladder_sum'];
            $tot_kladder_sum += $row['kladder_sum'];

            $stats_day[$str_dt]['bsoccer_bet_sum'] = $row['bsoccer_bet_sum'];
            $tot_bsoccer_bet_sum += $row['bsoccer_bet_sum'];

            $stats_day[$str_dt]['bsoccer_take_sum'] = $row['bsoccer_take_sum'];
            $tot_bsoccer_take_sum += $row['bsoccer_take_sum'];

            $stats_day[$str_dt]['bsoccer_sum'] = $row['bsoccer_sum'];
            $tot_bsoccer_sum += $row['bsoccer_sum'];
        }

        $tot_powerball_bet_sum_str = strColorRet($tot_powerball_bet_sum, 0);
        $tot_powerball_take_sum_str = strColorRet(0, $tot_powerball_take_sum);
        $tot_powerball_sum_str = strColorRet($tot_powerball_bet_sum, $tot_powerball_take_sum, 1);

        $tot_pladder_bet_sum_str = strColorRet($tot_pladder_bet_sum, 0);
        $tot_pladder_take_sum_str = strColorRet(0, $tot_pladder_take_sum);
        $tot_pladder_sum_str = strColorRet($tot_pladder_bet_sum, $tot_pladder_take_sum, 1);

        $tot_kladder_bet_sum_str = strColorRet($tot_kladder_bet_sum, 0);
        $tot_kladder_take_sum_str = strColorRet(0, $tot_kladder_take_sum);
        $tot_kladder_sum_str = strColorRet($tot_kladder_bet_sum, $tot_kladder_take_sum, 1);

        $tot_bsoccer_bet_sum_str = strColorRet($tot_bsoccer_bet_sum, 0);
        $tot_bsoccer_take_sum_str = strColorRet(0, $tot_bsoccer_take_sum);
        $tot_bsoccer_sum_str = strColorRet($tot_bsoccer_bet_sum, $tot_bsoccer_take_sum, 1);
    }

    
    
    $CASHAdminDAO->dbclose();
}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
<!--<![endif]-->

<?php
include_once(_BASEPATH . '/common/head.php');
?>
<script>
    $(document).ready(function() {
        App.init();
        FormPlugins.init();

        $('ul.tabs li').click(function() {
            var tab_id = $(this).attr('data-tab');

            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');

            $(this).addClass('current');
            $("#" + tab_id).addClass('current');
        })
    });
</script>
<script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>

<body>
    <form id="popForm" name="popForm" method="post">
        <input type="hidden" id="seq" name="seq">
        <input type="hidden" id="m_idx" name="m_idx">
        <input type="hidden" id="selContent" name="selContent">
    </form>
    <div class="wrap">
        <?php

        $menu_name = "stats_type";

        include_once(_BASEPATH . '/common/left_menu.php');

        include_once(_BASEPATH . '/common/iframe_head_menu.php');


        ?>
        <!-- Contents -->
        <div class="con_wrap">

            <div class="title">
                <a href="">
                    <i class="mte i_assessment vam ml20 mr10"></i>
                    <h4>타입별 현황</h4>
                </a>
            </div>

            <!-- detail search -->
            <div class="panel search_box">
                <h5><a <?= $srch_type == '1' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_type_list_new.php?srch_type=1&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>&srch_sports=<?= $srch_sports ?>">프리매치</a></h5>
                <h5><a <?= $srch_type == '2' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_type_list_new.php?srch_type=2&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>&srch_sports=<?= $srch_sports ?>">실시간</a></h5>
                <h5><a <?= $srch_type == '3' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_type_list_new.php?srch_type=3&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>">미니게임</a></h5>
                <h5><a <?= $srch_type == '7' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_type_list_new.php?srch_type=4&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>">카지노</a></h5>
                <h5><a <?= $srch_type == '8' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_type_list_new.php?srch_type=5&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>">슬롯머신</a></h5>
            </div>
            <!-- END detail search -->

            <!-- list -->
            <div class="panel reserve">
                <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                    <div class="panel_tit">
                        <div class="search_form fl">
                            <input type="hidden" class="" name="srch_type" id="srch_type" value="<?= $srch_type ?>" />

                            <div class="daterange">
                                <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                                <input type="text" class="" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?= $p_data['srch_s_date'] ?>" />
                            </div>
                            ~
                            <div class="daterange">
                                <label for="datepicker-autoClose"><i class="mte i_date_range mte-1x vat"></i></label>
                                <input type="text" class="" name="srch_e_date" id="datepicker-autoClose" placeholder="날짜선택" value="<?= $p_data['srch_e_date'] ?>" />
                            </div>
                            <div><a href="javascript:;" onClick="setDate('<?= $today ?>','<?= $today ?>');" class="btn h30 btn_blu">오늘</a></div>
                            <div><a href="javascript:;" onClick="setDate('<?= $before_week ?>','<?= $today ?>');" class="btn h30 btn_orange">1주일</a></div>
                            <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>','<?= $today ?>');" class="btn h30 btn_green">한달</a></div>
                            <div class="" style="padding-right: 10px;"></div>
                            <div class="" style="padding-right: 10px;">
                                <select name="srch_sports" id="srch_sports">
                                    <?php foreach ($p_data['sport'] as $sport) { ?>
                                    <option value="<?=$sport['id']?>" <?= $srch_sports == $sport['id'] ? "selected" : ''; ?>><?= !empty($sport['display_name']) ? $sport['display_name'] : $sport['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="" style="padding-right: 10px;"></div>
                            <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                        </div>
                        <div class="search_form fr">
                            <div class="checkbox checkbox-css checkbox-inverse">
                                <input type="checkbox" id="checkbox_css_101" name="checkbox_css_101" <?= isset($p_data['monitor_charge']) && $p_data['monitor_charge'] == 'Y' ? "checked" : ''; ?> />
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-wrapper1">
                    <div class="above-table">
                    </div>
                </div>
                <div class="tline table-wrapper2">
                    <table class="mlist main-table">
                        <tr>

                            <th rowspan="2">날짜</th>

                            <?php // 스포츠
                                if ($srch_type == '1' || $srch_type == '2') { 
                                $market_cnt = 0; 
                            ?>
                                <?php foreach ($total as $market) { ?>
                                    <th colspan="3"><?= isset($market['market'] ) ? $market['market'] : "기타" ?></th>
                                <?php 
                                    $market_cnt++;
                                    if($market_cnt >= 20) {
                                        // break;
                                    } 
                                } ?>
                            <?php } ?>
                            
                            <?php // 미니게임 ?>
                            <?php if ($srch_type == '3') { ?>
                                <th colspan="3">파워볼</th>
                                <th colspan="3">파워사다리</th>
                                <th colspan="3">키노사다리</th>
                                <th colspan="3">가상축구</th>
                            <?php }  ?>

                            <?php // 카지노 ?>
                            <?php if ($srch_type == '7') { ?>
                                <th colspan="3">아시아게이밍</th>
                                <th colspan="3">에볼루션게이밍</th>
                                <th colspan="3">섹시게이밍</th>
                            <?php }  ?>

                            
                            <?php // 카지노 ?>
                            <?php if ($srch_type == '8') { ?>
                                <th colspan="3">라스베가스</th>
                                <th colspan="3">하나바로</th>
                            <?php }  ?>
                        </tr>
                        <tr>
                            <?php // 스포츠 
                                if ($srch_type == '1' || $srch_type == '2') { 
                                $market_cnt = 0;
                            ?>
                                <?php foreach ($total as $market) { ?>
                                    <th>베팅</th>
                                    <th>당첨</th>
                                    <th>차액</th>
                                <?php 
                                    $market_cnt++;
                                    if($market_cnt >= 20) {
                                        // break;
                                    } 
                                } ?>
                            <?php } ?>

                            <?php // 미니게임 ?>
                            <?php if ($srch_type == '3') { ?>
                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>
                            <?php } ?>

                            <?php // 카지노 ?>
                            <?php if ($srch_type == '7') { ?>
                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>
                            <?php } ?>

                            <?php // 슬롯머시 ?>
                            <?php if ($srch_type == '8') { ?>
                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>
                            <?php } ?>
                        </tr>
                        <tr class="bg_orange">
                            <td>합 계</td>

                            <?php // 스포츠 
                                if($srch_type == '1' || $srch_type == '2') { 
                                $market_cnt = 0;        
                            ?>
                                <?php foreach ($total as $market) { ?>
                                    <td style='text-align:right;'><?= strColorRet($market['bet_sum'], 0) ?></td>
                                    <td style='text-align:right;'><?= strColorRet(0, $market['take_sum']) ?></td>
                                    <td style='text-align:right;'><?= strColorRet($market['bet_sum'], $market['take_sum'], 1) ?></td>
                                <?php 
                                    $market_cnt++;
                                    if($market_cnt >= 20) {
                                        // break;
                                    } 
                                } ?>
                            <?php }  ?>

                            <?php // 미니게임 ?>
                            <?php if ($srch_type == '3') { ?>

                                <td style='text-align:right;'><?= $tot_powerball_bet_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_powerball_take_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_powerball_sum_str ?></td>

                                <td style='text-align:right;'><?= $tot_pladder_bet_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_pladder_take_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_pladder_sum_str ?></td>

                                <td style='text-align:right;'><?= $tot_kladder_bet_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_kladder_take_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_kladder_sum_str ?></td>

                                <td style='text-align:right;'><?= $tot_bsoccer_bet_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_bsoccer_take_sum_str ?></td>
                                <td style='text-align:right;'><?= $tot_bsoccer_sum_str ?></td>
                            <?php } ?>

                            <?php // 카지노 ?>
                            <?php if ($srch_type == '7') { ?>

                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>

                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>

                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                            <?php } ?>
                            
                            <?php // 슬로머신 ?>
                            <?php if ($srch_type == '8') { ?>

                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>

                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                                <td style='text-align:right;'><?= 0 ?></td>
                            <?php } ?>
                        </tr>

                        <?php
                        if ($total_cnt > 0) {
                            if (isset($stats_day)) {
                                // 날짜 최신순으로
                                foreach (array_reverse($stats_day) as $row) {
                                    if ($row['ymd'] == '') continue;

                                    // 스포츠
                                    if ($srch_type == '1' || $srch_type == '2') {
                                        $display_row = [];
                                        // 마켓 없으면 0 으로 채우기
                                        foreach ($market_arr as $market) {
                                            if (isset($row[$market])) {
                                                $display_row[$market]['bet'] = $row[$market]['bet'];
                                                $display_row[$market]['take'] = $row[$market]['take'];
                                                $display_row[$market]['diff'] = $row[$market]['diff'];
                                            } else {
                                                $display_row[$market]['bet'] = 0;
                                                $display_row[$market]['take'] = 0;
                                                $display_row[$market]['diff'] = 0;
                                            }
                                        }
                                    }

                                    // 미니게임
                                    if ($srch_type == '3') {
                                        $powerball_bet_sum = strColorRet($row['powerball_bet_sum'] ?? 0, 0);
                                        $powerball_take_sum = strColorRet(0, $row['powerball_take_sum'] ?? 0);
                                        $powerball_sum = strColorRet($row['powerball_sum'] ?? 0, 0, 1);

                                        $pladder_bet_sum = strColorRet($row['pladder_bet_sum'] ?? 0, 0);
                                        $pladder_take_sum = strColorRet(0, $row['pladder_take_sum'] ?? 0);
                                        $pladder_sum = strColorRet($row['pladder_sum'] ?? 0, 0, 1);

                                        $kladder_bet_sum = strColorRet($row['kladder_bet_sum'] ?? 0, 0);
                                        $kladder_take_sum = strColorRet(0, $row['kladder_take_sum'] ?? 0);
                                        $kladder_sum = strColorRet($row['kladder_sum'] ?? 0, 0, 1);

                                        $bsoccer_bet_sum = strColorRet($row['bsoccer_bet_sum'] ?? 0, 0);
                                        $bsoccer_take_sum = strColorRet(0, $row['bsoccer_take_sum'] ?? 0);
                                        $bsoccer_sum = strColorRet($row['bsoccer_sum'] ?? 0, 0, 1);
                                    } ?>

                                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                                        <td><?= $row['ymd'] ?></td>

                                        <?php // 스포츠 
                                            if ($srch_type == '1' || $srch_type == '2') { 
                                            $market_cnt = 0;
                                        ?>
                                        <?php foreach ($display_row as $dp_col) { ?>
                                                <td style='text-align:right;'><?= strColorRet($dp_col['bet'], 0) ?></td>
                                                <td style='text-align:right;'><?= strColorRet(0, $dp_col['take']) ?></td>
                                                <td style='text-align:right;'><?= strColorRet($dp_col['diff'], 0, 1) ?></td>
                                        <?php 
                                            $market_cnt++;
                                            if($market_cnt >= 20) {
                                                // break;
                                            }
                                        } ?>
                                        <?php } ?>

                                        <?php // 미니게임 ?>
                                        <?php if ($srch_type == '3') { ?>
                                            <td style='text-align:right;'><?= $powerball_bet_sum ?></td>
                                            <td style='text-align:right;'><?= $powerball_take_sum ?></td>
                                            <td style='text-align:right;'><?= $powerball_sum ?></td>
                                            <td style='text-align:right;'><?= $pladder_bet_sum ?></td>
                                            <td style='text-align:right;'><?= $pladder_take_sum ?></td>
                                            <td style='text-align:right;'><?= $pladder_sum ?></td>
                                            <td style='text-align:right;'><?= $kladder_bet_sum ?></td>
                                            <td style='text-align:right;'><?= $kladder_take_sum ?></td>
                                            <td style='text-align:right;'><?= $kladder_sum ?></td>
                                            <td style='text-align:right;'><?= $bsoccer_bet_sum ?></td>
                                            <td style='text-align:right;'><?= $bsoccer_take_sum ?></td>
                                            <td style='text-align:right;'><?= $bsoccer_sum ?></td>
                                        <?php } ?>

                                        <?php // 카지노 ?>
                                        <?php if ($srch_type == '7') { ?>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                        <?php } ?>
                                        
                                        <?php // 슬롯머신 ?>
                                        <?php if ($srch_type == '8') { ?>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                            <td style='text-align:right;'><?= 0 ?></td>
                                        <?php } ?>
                                    </tr>
                        <?php

                                }
                            }
                        } else {
                            echo "<tr><td colspan='13'>데이터가 없습니다.</tr>";
                        }
                        ?>

                    </table>

                </div>
            </div>
            <!-- END list -->
        </div>
        <!-- END Contents -->
    </div>
    <?php
    include_once(_BASEPATH . '/common/bottom.php');
    ?>
</body>
<script>
    function goPopupUserinfo(midx, selkind) {
        var fm = document.popForm;

        fm.selContent.value = selkind;

        popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', midx);
    }

    function setDate(sdate, edate) {
        var fm = document.search;

        fm.srch_s_date.value = sdate;
        fm.srch_e_date.value = edate;
    }

    function goSearch() {
        var fm = document.search;

        fm.method = "get";
        fm.submit();
    }
</script>
<?php
function strColorRet($p_val = 0, $m_val = 0, $color = 0)
{
    $ret_val = $p_val - $m_val;
    
    if ($ret_val > 0) {
        if ($color == 1) {
            $ret_val = "<font color='blue'>".number_format($ret_val)."</font>";
        } else {
            $ret_val = "<font>".number_format($ret_val)."</font>";
        }
    } else if ($ret_val == 0) {
        $ret_val = 0;
    } else {
        if ($color == 1) {
            $ret_val = "<font color='red'>".number_format($ret_val)."</font>";
        } else {
            $ret_val = "<font>".number_format($ret_val)."</font>";
        }
    }
    
    return $ret_val;
}
?>

</html>