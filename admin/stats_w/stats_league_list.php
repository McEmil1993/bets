<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');

$UTIL = new CommonUtil();


if(0 != $_SESSION['u_business']){
    die();
}
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end


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
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

$srch_basic = "";
$p_data["sql_where"] = " WHERE 1 = 1 ";
switch ($p_data["srch_key"]) {
    case "s_league":

        if ($p_data['srch_val'] != '') {
            if (trim($p_data['srch_val'] == '기타')) {
                $srch_basic = " AND (name is null OR display_name is null) ";
            } else {
                $srch_basic = " AND (name like '%" . $p_data['srch_val'] . "%' OR display_name like '%" . $p_data['srch_val'] . "%') ";
            }
        }
        break;
}
$p_data['sql_where'] .= $srch_basic;
$p_data['date_where'] = " AND update_dt >= '$db_srch_s_date'
                        AND update_dt <= '$db_srch_e_date' ";

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

        // 리그 배팅
        $p_data['sql'] = "SELECT DATE(T1.calculate_dt) AS cr_dt, 
                            MBD.ls_fixture_id AS fix_id,
                            lg.id AS league_id,
                            MBD.fixture_league_id AS mbd_league_id,
                            fix.fixture_league_id AS fix_league_id,
                            lg.name AS league_name,
                            lg.display_name AS league_dp_name,
                            T1.idx AS bet_idx,
                            T1.bet AS bet,
                            T1.take AS take,
                            T1.bet - T1.take AS diff
                    FROM (SELECT MBB.idx AS idx,
                                MBB.total_bet_money AS bet,
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
                LEFT JOIN 
                    lsports_leagues AS lg
                ON 
                (CASE
                    WHEN MBD.fixture_league_id != 0
                    THEN MBD.fixture_league_id
                    WHEN fix.fixture_league_id IS NOT null
                    THEN fix.fixture_league_id
                    ELSE null
                END) = lg.id
                ";
        $p_data['sql'] .= " WHERE 1 = 1 ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " GROUP BY T1.idx, league_id";
        $p_data['sql'] .= " ORDER BY bet DESC , league_id";
        $p_data['sql'] .= ";";

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);

        $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

        $total = [];
        $league_arr = [];
        foreach ($db_dataArr as $row) {
            $str_dt = str_replace('-', '', $row['cr_dt']);
            $str_league = $row['league_id'];
            $str_league_name = $row['league_name'];
            $str_league_dp_name = $row['league_dp_name'];

            // 해당 리그 키 있으면 더하고 없으면 초기화
            if (isset($stats_day[$str_dt][$str_league])) {
                $stats_day[$str_dt][$str_league] = array(
                    "bet" => $stats_day[$str_dt][$str_league]['bet'] + $row['bet'],
                    "take" => $stats_day[$str_dt][$str_league]['take'] + $row['take'],
                    "diff" => $stats_day[$str_dt][$str_league]['diff'] + $row['diff']
                );
            } else {
                $stats_day[$str_dt][$str_league] = array(
                    "league" => $str_league,
                    "league_name" => $str_league_name,
                    "league_dp_name" => $str_league_dp_name,
                    "bet" => $row['bet'],
                    "take" => $row['take'],
                    "diff" => $row['diff']
                );
            }

            // 해당 리그 키 있으면 더하고 없으면 초기화
            if (isset($total[$str_league])) {
                $total[$str_league]['bet_sum'] += $row['bet'];
                $total[$str_league]['take_sum'] += $row['take'];
                $total[$str_league]['diff_sum'] += $row['diff'];
            } else {
                $total[$str_league]['league'] = $str_league;
                $total[$str_league]['league_name'] = $str_league_name;
                $total[$str_league]['league_dp_name'] = $str_league_dp_name;
                $total[$str_league]['bet_sum'] = $row['bet'];
                $total[$str_league]['take_sum'] = $row['take'];
                $total[$str_league]['diff_sum'] = $row['diff'];
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

        foreach ($total as $key => $league) {
            array_push($league_arr, $league['league']);
        }
        // 기타(null) 맨뒤로
        foreach ($stats_day as $key => $day) {
            $temp_arr = array('ymd' => $day['ymd'], 'val' => $day['val']);
            $sort_arr = array_diff_assoc($day, $temp_arr);
            uasort($sort_arr, "bet_sort");
            $stats_day[$key] = $temp_arr + $sort_arr;
        }
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

        $menu_name = "stats_league";

        include_once(_BASEPATH . '/common/left_menu.php');

        include_once(_BASEPATH . '/common/iframe_head_menu.php');


        ?>
        <!-- Contents -->
        <div class="con_wrap">

            <div class="title">
                <a href="">
                    <i class="mte i_assessment vam ml20 mr10"></i>
                    <h4>리그별 현황</h4>
                </a>
            </div>

            <!-- detail search -->
            <div class="panel search_box">
                <h5><a <?= $srch_type == '1' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_league_list.php?srch_type=1&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>">프리매치</a></h5>
                <h5><a <?= $srch_type == '2' ? 'style="font-weight:bold; font-size: 1.4rem;"' : '' ?> href="stats_league_list.php?srch_type=2&srch_s_date=<?= $p_data['srch_s_date'] ?>&srch_e_date=<?= $p_data['srch_e_date'] ?>">실시간</a></h5>
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
                                <select name="srch_key" id="srch_key">
                                    <option value="s_league" <?php if ($p_data['srch_key'] == 's_league') {
            echo "selected";
        } ?>>리그명</option>
                                </select>
                            </div>

                            <div class="">
                                <input type="text" name="srch_val" id="srch_val" class="" placeholder="검색" value="<?= $p_data['srch_val'] ?>" />
                            </div>
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
                                     $league_cnt = 0; ?>
                                <?php foreach ($total as $league) { ?>
                                    <th colspan="3"><?php
                                        if (isset($league['league_dp_name'])) {
                                            echo $league['league_dp_name'];
                                        } elseif (isset($league['league_name'])) {
                                            echo $league['league_name'];
                                        } else {
                                            echo '기타';
                                        }
                                        ?>
                                    </th>
                                <?php
                                    $league_cnt++;
                                    if ($league_cnt >= 20) {
                                        break;
                                    }
                                } ?>
                            <?php
                                 } ?>
                            
                        </tr>
                        <tr>
                            <?php // 스포츠
                                if ($srch_type == '1' || $srch_type == '2') {
                                    $league_cnt = 0; ?>
                                <?php foreach ($total as $league) { ?>
                                    <th>베팅</th>
                                    <th>당첨</th>
                                    <th>차액</th>
                                <?php
                                    $league_cnt++;
                                    if ($league_cnt >= 20) {
                                        break;
                                    }
                                } ?>
                            <?php
                                } ?>
                        </tr>
                        <tr class="bg_orange">
                            <td>합 계</td>

                            <?php // 스포츠
                                if ($srch_type == '1' || $srch_type == '2') {
                                    $league_cnt = 0; ?>
                                <?php foreach ($total as $league) { ?>
                                    <td style='text-align:right;'><?= strColorRet($league['bet_sum'], 0) ?></td>
                                    <td style='text-align:right;'><?= strColorRet(0, $league['take_sum']) ?></td>
                                    <td style='text-align:right;'><?= strColorRet($league['bet_sum'], $league['take_sum'], 1) ?></td>
                                <?php
                                    $league_cnt++;
                                    if ($league_cnt >= 20) {
                                        break;
                                    }
                                } ?>
                            <?php
                                }  ?>
                        </tr>

                        <?php
                        if ($total_cnt > 0) {
                            if (isset($stats_day)) {
                                // 날짜 최신순으로
                                foreach (array_reverse($stats_day) as $row) {
                                    if ($row['ymd'] == '') {
                                        continue;
                                    }

                                    // 스포츠
                                    if ($srch_type == '1' || $srch_type == '2') {
                                        $display_row = [];
                                        // 리그 없으면 0 으로 채우기
                                        foreach ($league_arr as $league) {
                                            if (isset($row[$league])) {
                                                $display_row[$league]['bet'] = $row[$league]['bet'];
                                                $display_row[$league]['take'] = $row[$league]['take'];
                                                $display_row[$league]['diff'] = $row[$league]['diff'];
                                            } else {
                                                $display_row[$league]['bet'] = 0;
                                                $display_row[$league]['take'] = 0;
                                                $display_row[$league]['diff'] = 0;
                                            }
                                        }
                                    } ?>

                                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                                        <td><?= $row['ymd'] ?></td>

                                        <?php // 스포츠
                                            if ($srch_type == '1' || $srch_type == '2') {
                                                $league_cnt = 0; ?>
                                        <?php foreach ($display_row as $dp_col) { ?>
                                                <td style='text-align:right;'><?= strColorRet($dp_col['bet'], 0) ?></td>
                                                <td style='text-align:right;'><?= strColorRet(0, $dp_col['take']) ?></td>
                                                <td style='text-align:right;'><?= strColorRet($dp_col['diff'], 0, 1) ?></td>
                                        <?php
                                            $league_cnt++;
                                            if ($league_cnt >= 20) {
                                                break;
                                            }
                                        } ?>
                                        <?php
                                            } ?>
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
        } elseif ($ret_val == 0) {
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