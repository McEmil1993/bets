<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');
include_once(_BASEPATH . '/common/login_check.php');
include_once(_LIBPATH . '/class_Code.php');
include_once(_LIBPATH . '/class_CommonStats.php');
include_once(_LIBPATH . '/class_CommonStatsQuery.php');

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", mktime(0, 0, 0, intval(date('m')), 1, intval(date('Y'))));
$end_date = date("Y/m/d");
$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);
$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);
$db_srch_s_date = !empty($p_data['db_srch_s_date']) ? $p_data['db_srch_s_date'] . " 00:00:00" : "NULL";
$db_srch_e_date = !empty($p_data['db_srch_e_date']) ? $p_data['db_srch_e_date'] . " 23:59:59" : "NULL";

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : _NUM_PER_PAGE);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = _NUM_PER_PAGE;
}

$p_data['s_ob'] = trim(isset($_REQUEST['s_ob']) ? $_REQUEST['s_ob'] : '');
$p_data['s_ob_type'] = trim(isset($_REQUEST['s_ob_type']) ? $_REQUEST['s_ob_type'] : 'desc');
$default_link = 'stats_user_list_new.php?srch_s_date=' . $p_data['db_srch_s_date'] . '&srch_e_date=' . $p_data['db_srch_e_date'];
$default_link .= '&s_ob=' . $p_data['s_ob'] . '&s_ob_type=' . $p_data['s_ob_type'];

$displayData = CommonStats::setDisplayData($p_data);

$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
$member_id = trim(isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : '');

$p_data["sql_where"] = " WHERE 1 = 1 ";
$p_data['sql_groupby'] = " GROUP BY mb.idx ";
$srch_basic = "";
$srch_basic_count = "";
switch ($p_data["srch_key"]) {
    case "s_idnick":

        if ($p_data['srch_val'] != '') {
            $srch_basic = " AND (id like '%" . $p_data['srch_val'] . "%' OR nick_name like '%" . $p_data['srch_val'] . "%') ";
        }
        break;
}

$p_data['sql_where'] .= $srch_basic;
$p_data['date_where'] = " AND update_dt >= '$db_srch_s_date'
                        AND update_dt <= '$db_srch_e_date' ";
$p_data['bet_where'] = '';
$p_data['casino_where'] = '';
        
$srch_basic_count = $srch_basic;
if ($db_conn) {
    // 총판목록
    $member_idx = 0;
    // 총판목록
    if ($_SESSION['u_business'] == 0) {
        $distributorList = GameCode::getRecommandMemberInfos(0, $CASHAdminDAO);
        if ($member_id != '') {
            $sql = "SELECT idx FROM member WHERE id = ?";
            $member_idx = $CASHAdminDAO->getQueryData_pre($sql,[$member_id])[0]['idx'];
            
            $p_data['date_where'] .= "AND T1.idx in (SELECT idx FROM member WHERE dis_id = '$member_id')";
            $p_data['bet_where'] .= " AND T1.idx in (SELECT idx FROM member WHERE dis_id = '$member_id')";
            $p_data['casino_where'] = " AND MB.dis_id = '$member_id'";
        }
    } else {
        $distributorList = GameCode::getRecommandMemberInfos($_SESSION['member_idx'], $CASHAdminDAO);
      
        if ($member_id != '') {
            $p_data['date_where'] .= "AND T1.idx in (SELECT idx FROM member WHERE dis_id = '$member_id')";
            $p_data['bet_where'] .= " AND T1.idx in (SELECT idx FROM member WHERE dis_id = '$member_id')";
            $p_data['casino_where'] = " AND MB.dis_id = '$member_id'";
        } else { // 전체
            $member_id = $distributorList[0]['id'];
            $member_idx = $distributorList[0]['idx'];
            list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'], $CASHAdminDAO);
            $str_param = implode(',', $param_dist);
            /*$p_data['date_where'] .= "AND T1.idx in (SELECT idx FROM member WHERE recommend_member in($str_param))";
            $p_data['bet_where'] .= " AND T1.idx in (SELECT idx FROM member WHERE recommend_member in($str_param))";*/
            $p_data['date_where'] .= "AND T1.idx in (SELECT idx FROM member WHERE recommend_member in($member_idx))";
            $p_data['casino_where'] = " AND MB.recommend_member in($member_idx)";
            $p_data['bet_where'] .= " AND T1.idx in (SELECT idx FROM member WHERE recommend_member in($member_idx))";
            
            $srch_basic_count = $srch_basic." AND T1.dis_id = '$member_id'";
        }
        
        //CommonUtil::logWrite("distributorList member_idx: " . $_SESSION['member_idx'] , "info");
        //CommonUtil::logWrite("distributorList str_param: " . $str_param , "info");
    }

    // 검색한 총판번호
    foreach ($distributorList as $key => $value) {
        if ($value['id'] == $member_id) {
            $member_idx = $value['idx'];
            
            $srch_basic_count = $srch_basic." AND T1.dis_id = '$member_id'";
        }
    }

    //CommonUtil::logWrite("distributorList u_business: " . $_SESSION['u_business'] , "info");
    //CommonUtil::logWrite("distributorList data: " . json_encode($distributorList), "info");
    CommonUtil::logWrite("distributorList member_id: " . $member_id , "info");
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $p_data['sql'] = CommonStatsQuery::getTotalUserCountQuery($srch_basic_count);
    CommonUtil::logWrite("getTotalUserCountQuery sql: " . $p_data['sql'], "info");
    $total_cnt = $CASHAdminDAO->getQueryData($p_data)[0]['idx_cnt'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block) {
        $last_page = $total_page;
    }

    // 합계
    $total = [];
    // 충전합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumChargeQuery($p_data['date_where']);
    $total['ch_sum'] = $CASHAdminDAO->getQueryData($p_data)[0]['ch_sum'];
    CommonUtil::logWrite("ch_sum sql: " . $p_data['sql'], "info");

    $p_data['sql'] = CommonStatsQuery::getTotalSumExchangeQuery($p_data['date_where']);
    // 환전합계
    $total['ex_sum'] = $CASHAdminDAO->getQueryData($p_data)[0]['ex_sum'];
    CommonUtil::logWrite("ex_sum sql: " . $p_data['sql'], "info");
    // 차액
    $total['diff_sum'] = $total['ch_sum'] - $total['ex_sum'];

    // 증감률
    if ($total['diff_sum'] == 0) {
        $total['diff_per_sum'] = 0;
    } else {
        $total['diff_per_sum'] = isset($total['diff_sum']) && isset($total['ch_sum']) ? round(($total['diff_sum'] / $total['ch_sum'] * 100), 2) : -100;
        $total['diff_per_sum'] = $total['diff_per_sum'] == -INF ? -100.00 : $total['diff_per_sum'];
    }
    if (is_nan($total['diff_per_sum'])) {
        $total['diff_per_sum'] = 0;
    }

    // 프리매치 - 싱글 - 배팅합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumPreMatchSingleBetMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['pre_bet_sum_s'] = $CASHAdminDAO->getQueryData($p_data)[0]['pre_bet_sum_s'];
    //CommonUtil::logWrite("pre_bet_sum_s sql: " . $p_data['sql'], "info");
    // 프리매치 - 싱글 - 당첨합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumPreMatchSingleTakeMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['pre_take_sum_s'] = $CASHAdminDAO->getQueryData($p_data)[0]['pre_take_sum_s'];
    //CommonUtil::logWrite("pre_take_sum_s sql: " . $p_data['sql'], "info");
    // 프리매치 - 싱글 - 수익 합계
    $total['pre_sum_s'] = $total['pre_bet_sum_s'] - $total['pre_take_sum_s'];

    // 프리매치 - 다폴더 - 배팅합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumPreMatchMultiBetMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['pre_bet_sum_d'] = $CASHAdminDAO->getQueryData($p_data)[0]['pre_bet_sum_d'];
    //CommonUtil::logWrite("pre_bet_sum_d sql: " . $p_data['sql'], "info");
    // 프리매치 - 다폴더 - 당첨합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumPreMatchMultiTakeMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['pre_take_sum_d'] = $CASHAdminDAO->getQueryData($p_data)[0]['pre_take_sum_d'];
    //CommonUtil::logWrite("pre_take_sum_d sql: " . $p_data['sql'], "info");
    // 프리매치 - 다폴더 - 수익 합계
    $total['pre_sum_d'] = $total['pre_bet_sum_d'] - $total['pre_take_sum_d'];

    // 실시간 싱글 - 배팅합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumInplaySingleBetMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['real_bet_sum_s'] = $CASHAdminDAO->getQueryData($p_data)[0]['real_bet_sum_s'];

    //CommonUtil::logWrite("real_bet_sum_s sql: " . $p_data['sql'], "info");
    // 실시간 싱글 - 당첨합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumInplaySingleTakeMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['real_take_sum_s'] = $CASHAdminDAO->getQueryData($p_data)[0]['real_take_sum_s'];

    //CommonUtil::logWrite("real_take_sum_s sql: " . $p_data['sql'], "info");
    // 실시간 - 수익 합계
    $total['real_sum_s'] = $total['real_bet_sum_s'] - $total['real_take_sum_s'];

    // 실시간 - 다폴더 - 배팅합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumInplayMultiBetMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['real_bet_sum_d'] = $CASHAdminDAO->getQueryData($p_data)[0]['real_bet_sum_d'];
    //CommonUtil::logWrite("real_bet_sum_d sql: " . $p_data['sql'], "info");
    // 실시간 - 다폴더 - 당첨합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumInplayMultiTakeMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['real_take_sum_d'] = $CASHAdminDAO->getQueryData($p_data)[0]['real_take_sum_d'];
    //CommonUtil::logWrite("real_take_sum_d sql: " . $p_data['sql'], "info");
    // 실시간 - 다폴더 - 수익 합계
    $total['real_sum_d'] = $total['real_bet_sum_d'] - $total['real_take_sum_d'];

    // classic bet info
    // 클래식  - 배팅합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumClassicBetMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['total_classic_bet_money'] = $CASHAdminDAO->getQueryData($p_data)[0]['total_classic_bet_money'];
    //CommonUtil::logWrite("real_bet_sum_d sql: " . $p_data['sql'], "info");
    // 클래식 - 당첨합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumClassicTakeMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['total_classic_win_money'] = $CASHAdminDAO->getQueryData($p_data)[0]['total_classic_win_money'];
    //CommonUtil::logWrite("real_take_sum_d sql: " . $p_data['sql'], "info");
    // 클래식 - 수익 합계
    $total['total_classic_lose_money'] = $total['total_classic_bet_money'] - $total['total_classic_win_money'];
        
    // 미니게임 - 배팅합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumMiniGameBetMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['mini_bet_sum_d'] = $CASHAdminDAO->getQueryData($p_data)[0]['mini_bet_sum_d'];
    //CommonUtil::logWrite("mini_bet_sum_d sql: " . $p_data['sql'], "info");

    // 미니게임 - 당첨합계
    $p_data['sql'] = CommonStatsQuery::getTotalSumMiniGameTakeMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['bet_where']);
    $total['mini_take_sum_d'] = $CASHAdminDAO->getQueryData($p_data)[0]['mini_take_sum_d'];
    
    //CommonUtil::logWrite("mini_take_sum_d sql: " . $p_data['sql'], "info");
    // 미니게임 - 수익 합계
    $total['mini_sum_d'] = $total['mini_bet_sum_d'] - $total['mini_take_sum_d'];

    // 카지노,슬롯 배팅금,당첨금 
    $p_data['sql'] = CommonStatsQuery::getTotalSumCasinoSlotMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['casino_where']);
    $db_casino_slot_data = $CASHAdminDAO->getQueryData($p_data);
    foreach ($db_casino_slot_data as $value) {
        if ('bet_tot_casino' == $value['stype']) {
            $total['total_casino_bet_money'] = $value['total_bet_money'];
            $total['total_casino_win_money'] = $value['total_win_money'];
            $total['total_casino_lose_money'] = $value['total_bet_money'] - $value['total_win_money'];
        } else {
            $total['total_slot_bet_money'] = $value['total_bet_money'];
            $total['total_slot_win_money'] = $value['total_win_money'];
            $total['total_slot_lose_money'] = $value['total_bet_money'] - $value['total_win_money'];
        }
    }

    // 이스포츠 / 키론 / 해시  배팅금,당첨금 
    $p_data['sql'] = CommonStatsQuery::getTotalSumEsportsHashMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['casino_where']);
    $db_espt_hash_data = $CASHAdminDAO->getQueryData($p_data);
    foreach ($db_espt_hash_data as $value) {
        if ('bet_tot_espt' == $value['stype']) {
            $total['total_espt_bet_money'] = $value['total_bet_money'];
            $total['total_espt_win_money'] = $value['total_win_money'];
            $total['total_espt_lose_money'] = $value['total_bet_money'] - $value['total_win_money'];
        } else {
            $total['total_hash_bet_money'] = $value['total_bet_money'];
            $total['total_hash_win_money'] = $value['total_win_money'];
            $total['total_hash_lose_money'] = $value['total_bet_money'] - $value['total_win_money'];
        }
    }
    
    // 홀덤
    $p_data['sql'] = CommonStatsQuery::getTotalSumHoldemMoneyQuery($db_srch_s_date, $db_srch_e_date, $p_data['casino_where']);
    $db_holdem_data = $CASHAdminDAO->getQueryData($p_data);
    foreach ($db_holdem_data as $value) {
        $total['total_holdem_bet_money'] = $value['total_bet_money'];
        $total['total_holdem_win_money'] = $value['total_win_money'];
        $total['total_holdem_lose_money'] = $value['total_bet_money'] - $value['total_win_money'];
    }

    // 멤버
    $p_data['sql'] = CommonStatsQuery::getTotalSumMemberQuery($db_srch_s_date, $db_srch_e_date, $member_id, $srch_basic
                    , $p_data['sql_groupby'], $displayData['sql_orderby'], $p_data['start'], $p_data['num_per_page']);
    $db_member_dataArr = $CASHAdminDAO->getQueryData($p_data);
    CommonUtil::logWrite("member total sql: " . $p_data['sql'], "info");
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
        $(document).ready(function () {
            App.init();
            FormPlugins.init();

            $('ul.tabs li').click(function () {
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
$menu_name = "stats_user";
include_once(_BASEPATH . '/common/left_menu.php');
include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>

            <!-- Contents -->
            <div class="con_wrap">
                <div class="title">
                    <a href="">
                        <i class="mte i_assessment vam ml20 mr10"></i>
                        <h4>사용자별 현황</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="s_ob" value="<?= $p_data['s_ob'] ?>">
                        <input type="hidden" name="s_ob_type" value="<?= $p_data['s_ob_type'] ?>">
                        <input type="hidden" name="monitor_charge" id="monitor_charge">
                        <div class="panel_tit">
                            <div class="search_form fl">

                                <div class="daterange">
                                    <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                                    <input type="text" class="" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?= $p_data['srch_s_date'] ?>" />
                                </div>
                                ~
                                <div class="daterange">
                                    <label for="datepicker-autoClose"><i class="mte i_date_range mte-1x vat"></i></label>
                                    <input type="text" class="" name="srch_e_date" id="datepicker-autoClose" placeholder="날짜선택" value="<?= $p_data['srch_e_date'] ?>" />
                                </div>
                                <div><a href="javascript:;" onClick="setDate('<?= $today ?>', '<?= $today ?>');" class="btn h30 btn_blu">최신</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_week ?>', '<?= $today ?>');" class="btn h30 btn_orange">1주일</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>', '<?= $today ?>');" class="btn h30 btn_green">한달</a></div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="member_id" id="sports_list" style="width: 100%">
                                        <?php if(0 == $_SESSION['u_business']){ ?>
                                                <option value="">전체</option>
                                        <?php } ?>  
                                        <?php foreach ($distributorList as $key => $item) { ?>
                                            <option value="<?= $item['id'] ?>"   <?php if ($member_id == $item['id']): ?> selected<?php endif; ?>><?= $item['nick_name'] ?></option>
                                        <?php } ?>  
                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick" <?php
                                        if ($p_data['srch_key'] == 's_idnick') {
                                            echo "selected";
                                        }
                                        ?>>아이디 및 닉네임</option>
                                    </select>
                                </div>

                                <div class="">
                                    <input type="text" name="srch_val" id="srch_val" class="" placeholder="검색" value="<?= $p_data['srch_val'] ?>" />
                                </div>
                                <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                            </div>
                            <div class="search_form fr">
                                <div class="checkbox checkbox-css checkbox-inverse">
                                    <input type="checkbox" id="checkbox_css_101" name="checkbox_css_101" <?= !empty($p_data['monitor_charge']) && $p_data['monitor_charge'] == 'Y' ? "checked" : ""; ?> />
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
                                <th rowspan="2">총판 </th>
                                <th rowspan="2">아이디 </th>
                                <th rowspan="2">닉네임 </th>
                                <th colspan="4">충/환</th>
                                <th colspan="3">클래식</th>
                                <th colspan="3">프리매치 싱글</th>
                                <th colspan="3">프리매치 멀티</th>
                                <th colspan="3">실시간 싱글</th>
                                <th colspan="3">실시간 멀티</th>
                                <th colspan="3">미니게임</th>
                                <th colspan="3">카지노</th>
                                <th colspan="3">슬롯머신</th>
                                <th colspan="3">해시게임</th>
                                <?php if('ON' == IS_HOLDEM){ ?>
                                    <th colspan="3">홀덤</th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('ch_sum', '<?= $displayData['ob_ch_sum_change'] ?>');"><?= $displayData['ob_ch_sum_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('ex_sum', '<?= $displayData['ob_ex_sum_change'] ?>');"><?= $displayData['ob_ex_sum_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('diff_sum', '<?= $displayData['ob_diff_sum_change'] ?>');"><?= $displayData['ob_diff_sum_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('diff_per_sum', '<?= $displayData['ob_diff_per_sum_change'] ?>');"><?= $displayData['ob_diff_per_sum_color'] ?></a>
                                </th>
                                <!-- classic -->
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_classic_bet_money', '<?= $displayData['ob_classic_bet_sum_change'] ?>');"><?= $displayData['ob_classic_bet_sum_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_classic_win_money', '<?= $displayData['ob_classic_take_sum_change'] ?>');"><?= $displayData['ob_classic_take_sum_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_classic_lose_money', '<?= $displayData['ob_classic_sum_change'] ?>');"><?= $displayData['ob_classic_sum_color'] ?></a>
                                </th>
                                <!-- end -->
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('pre_bet_sum_s', '<?= $displayData['ob_pre_bet_sum_s_change'] ?>');"><?= $displayData['ob_pre_bet_sum_s_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('pre_take_sum_s', '<?= $displayData['ob_pre_take_sum_s_change'] ?>');"><?= $displayData['ob_pre_take_sum_s_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('pre_sum_s', '<?= $displayData['ob_pre_sum_s_change'] ?>');"><?= $displayData['ob_pre_sum_s_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('pre_bet_sum_d', '<?= $displayData['ob_pre_bet_sum_d_change'] ?>');"><?= $displayData['ob_pre_bet_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('pre_take_sum_d', '<?= $displayData['ob_pre_take_sum_d_change'] ?>');"><?= $displayData['ob_pre_take_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('pre_sum_d', '<?= $displayData['ob_pre_sum_d_change'] ?>');"><?= $displayData['ob_pre_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('real_bet_sum_s', '<?= $displayData['ob_real_bet_sum_s_change'] ?>');"><?= $displayData['ob_real_bet_sum_s_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('real_take_sum_s', '<?= $displayData['ob_real_take_sum_s_change'] ?>');"><?= $displayData['ob_real_take_sum_s_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('real_sum_s', '<?= $displayData['ob_real_sum_d_change'] ?>');"><?= $displayData['ob_real_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('real_bet_sum_d', '<?= $displayData['ob_real_bet_sum_d_change'] ?>');"><?= $displayData['ob_real_bet_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('real_take_sum_d', '<?= $displayData['ob_real_take_sum_d_change'] ?>');"><?= $displayData['ob_real_take_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('real_sum_d', '<?= $displayData['ob_real_sum_d_change'] ?>');"><?= $displayData['ob_real_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('mini_bet_sum_d', '<?= $displayData['ob_mini_bet_sum_d_change'] ?>');"><?= $displayData['ob_mini_bet_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('mini_take_sum_d', '<?= $displayData['ob_mini_take_sum_d_change'] ?>');"><?= $displayData['ob_mini_take_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('mini_sum_d', '<?= $displayData['ob_mini_sum_d_change'] ?>');"><?= $displayData['ob_mini_sum_d_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_casino_bet_money', '<?= $displayData['ob_total_casino_bet_money_change'] ?>');"><?= $displayData['ob_total_casino_bet_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_casino_win_money', '<?= $displayData['ob_total_casino_win_money_change'] ?>');"><?= $displayData['ob_total_casino_win_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_casino_lose_money', '<?= $displayData['ob_total_casino_lose_money_change'] ?>');"><?= $displayData['ob_total_casino_lose_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_slot_bet_money', '<?= $displayData['ob_total_slot_bet_money_change'] ?>');"><?= $displayData['ob_total_slot_bet_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_slot_win_money', '<?= $displayData['ob_total_slot_win_money_change'] ?>');"><?= $displayData['ob_total_slot_win_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_slot_lose_money', '<?= $displayData['ob_total_slot_lose_money_change'] ?>');"><?= $displayData['ob_total_slot_lose_money_color'] ?></a>
                                </th>

                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_hash_bet_money', '<?= $displayData['ob_total_hash_bet_money_change'] ?>');"><?= $displayData['ob_total_hash_bet_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_hash_win_money', '<?= $displayData['ob_total_hash_win_money_change'] ?>');"><?= $displayData['ob_total_hash_win_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_hash_lose_money', '<?= $displayData['ob_total_hash_lose_money_change'] ?>');"><?= $displayData['ob_total_hash_lose_money_color'] ?></a>
                                </th>
                                
                                <?php if('ON' == IS_HOLDEM){ ?>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_holdem_bet_money', '<?= $displayData['ob_total_holdem_bet_money_change'] ?>');"><?= $displayData['ob_total_holdem_bet_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_holdem_win_money', '<?= $displayData['ob_total_holdem_win_money_change'] ?>');"><?= $displayData['ob_total_holdem_win_money_color'] ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('total_holdem_lose_money', '<?= $displayData['ob_total_holdem_lose_money_change'] ?>');"><?= $displayData['ob_total_holdem_lose_money_color'] ?></a>
                                </th>
                                <?php } ?>

                            </tr>
                            <tr class="bg_orange">
                                <td colspan="3">합 계</td>
                                <?php
                                $ch_sum = intval($total['ch_sum']);
                                $ex_sum = intval($total['ex_sum']);
                                $diff_sum = intval($total['diff_sum']);
                                $diff_per_sum = floatval($total['diff_per_sum']);
                                $pre_bet_sum_s = intval($total['pre_bet_sum_s']);
                                $pre_take_sum_s = intval($total['pre_take_sum_s']);
                                $pre_sum_s = intval($total['pre_sum_s']);
                                $pre_bet_sum_d = intval($total['pre_bet_sum_d']);
                                $pre_take_sum_d = intval($total['pre_take_sum_d']);
                                $pre_sum_d = intval($total['pre_sum_d']);
                                $real_bet_sum_s = intval($total['real_bet_sum_s']);
                                $real_take_sum_s = intval($total['real_take_sum_s']);
                                $real_sum_s = intval($total['real_sum_s']);
                                $real_bet_sum_d = intval($total['real_bet_sum_d']);
                                $real_take_sum_d = intval($total['real_take_sum_d']);
                                $real_sum_d = intval($total['real_sum_d']);
                                $mini_bet_sum_d = intval($total['mini_bet_sum_d']);
                                $mini_take_sum_d = intval($total['mini_take_sum_d']);
                                $mini_sum_d = intval($total['mini_sum_d']);

                                $total_casino_bet_money = intval($total['total_casino_bet_money']);
                                $total_casino_win_money = intval($total['total_casino_win_money']);
                                $total_casino_lose_money = intval($total['total_casino_lose_money']);
                                $total_slot_bet_money = intval($total['total_slot_bet_money']);
                                $total_slot_win_money = intval($total['total_slot_win_money']);
                                $total_slot_lose_money = intval($total['total_slot_lose_money']);

                                $total_hash_bet_money = intval($total['total_hash_bet_money']);
                                $total_hash_win_money = intval($total['total_hash_win_money']);
                                $total_hash_lose_money = intval($total['total_hash_lose_money']);
                                
                                $total_holdem_bet_money = intval($total['total_holdem_bet_money']);
                                $total_holdem_win_money = intval($total['total_holdem_win_money']);
                                $total_holdem_lose_money = intval($total['total_holdem_lose_money']);
                                
                                // 클래식
                                $total_classic_bet_money = intval($total['total_classic_bet_money']);
                                $total_classic_win_money = intval($total['total_classic_win_money']);
                                $total_classic_lose_money = intval($total['total_classic_lose_money']);
                                
                                ?>
                                <?php /* 손해금은 부호 반대로 색상 반대로 */ ?>
                                <td style='text-align:right; <?= '' ?>'>
                                    <?= number_format($ch_sum) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$ex_sum) ?>
                                </td>
                                <td style='text-align:right;  <?= $diff_sum > 0 ? 'color:#0021FD' : ($diff_sum < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($diff_sum) ?>
                                </td>
                                <td style='text-align:right;  <?= $diff_per_sum > 0 ? 'color:#0021FD' : ($diff_per_sum < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= $diff_per_sum . ' %' ?>
                                </td>
                                <!-- classic -->
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format($total_classic_bet_money) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$total_classic_win_money) ?>
                                </td>
                                <td style='text-align:right;  <?= $total_classic_lose_money > 0 ? 'color:#0021FD' : ($total_classic_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($total_classic_lose_money) ?>
                                </td>
                                <!-- classic end -->
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format($pre_bet_sum_s) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$pre_take_sum_s) ?>
                                </td>
                                <td style='text-align:right;  <?= $pre_sum_s > 0 ? 'color:#0021FD' : ($pre_sum_s < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($pre_sum_s) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format($pre_bet_sum_d) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$pre_take_sum_d) ?>
                                </td>
                                <td style='text-align:right;  <?= $pre_sum_d > 0 ? 'color:#0021FD' : ($pre_sum_d < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($pre_sum_d) ?>
                                </td>

                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format($real_bet_sum_s) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$real_take_sum_s) ?>
                                </td>
                                <td style='text-align:right;  <?= $real_sum_s > 0 ? 'color:#0021FD' : ($real_sum_s < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($real_sum_s) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format($real_bet_sum_d) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$real_take_sum_d) ?>
                                </td>
                                <td style='text-align:right;  <?= $real_sum_d > 0 ? 'color:#0021FD' : ($real_sum_d < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($real_sum_d) ?>
                                </td>

                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format($mini_bet_sum_d) ?>
                                </td>
                                <td style='text-align:right;  <?= '' ?>'>
                                    <?= number_format(-$mini_take_sum_d) ?>
                                </td>
                                <td style='text-align:right;  <?= $mini_sum_d > 0 ? 'color:#0021FD' : ($mini_sum_d < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($mini_sum_d) ?>
                                </td>

                                <td data-casino-total-bet style='text-align:right;'>
                                    <?= number_format($total_casino_bet_money) ?>
                                </td>
                                <td data-casino-total-win style='text-align:right;'>
                                    <?= number_format(-$total_casino_win_money) ?>
                                </td>
                                <td data-casino-total-diff style='text-align:right; <?= $total_casino_lose_money > 0 ? 'color:#0021FD' : ($total_casino_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($total_casino_lose_money) ?>
                                </td>

                                <td data-slot-total-bet style='text-align:right;'>
                                    <?= number_format($total_slot_bet_money) ?>
                                </td>
                                <td data-slot-total-win style='text-align:right;'>
                                    <?= number_format($total_slot_win_money) ?> 
                                </td>
                                <td data-slot-total-diff style='text-align:right; <?= $total_slot_lose_money > 0 ? 'color:#0021FD' : ($total_slot_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($total_slot_lose_money) ?>
                                </td>

                                <td style='text-align:right;'>
                                    <?= number_format($total_hash_bet_money) ?>
                                </td>
                                <td style='text-align:right;'>
                                    <?= number_format($total_hash_win_money) ?> 
                                </td>
                                <td style='text-align:right; <?= $total_hash_lose_money > 0 ? 'color:#0021FD' : ($total_hash_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                    <?= number_format($total_hash_lose_money) ?>
                                </td>

                                <?php if('ON' == IS_HOLDEM){ ?>
                                    <td style='text-align:right;'>
                                        <?= number_format($total_holdem_bet_money) ?>
                                    </td>
                                    <td style='text-align:right;'>
                                        <?= number_format($total_holdem_win_money) ?> 
                                    </td>
                                    <td style='text-align:right; <?= $total_holdem_lose_money > 0 ? 'color:#0021FD' : ($total_holdem_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($total_holdem_lose_money) ?>
                                    </td>
                                <?php } ?>

                            </tr>
                            <?php
                            $db_member_dataArr = !empty($db_member_dataArr) ? $db_member_dataArr : [];
                            foreach ($db_member_dataArr as $row) {
                                $db_m_idx = $row['idx'];
                                $db_dis_id = $row['dis_id'];
                                $db_id = $row['id'];
                                $db_nick = $row['nick_name'];
                                $ch_sum = intval($row['ch_sum']);
                                $ex_sum = intval($row['ex_sum']);
                                $diff_sum = intval($row['diff_sum']);
                                $diff_per_sum = floatval($row['diff_per_sum']);
                                $pre_bet_sum_s = intval($row['pre_bet_sum_s']);
                                $pre_take_sum_s = intval($row['pre_take_sum_s']);
                                $pre_sum_s = intval($row['pre_sum_s']);
                                $pre_bet_sum_d = intval($row['pre_bet_sum_d']);
                                $pre_take_sum_d = intval($row['pre_take_sum_d']);
                                $pre_sum_d = intval($row['pre_sum_d']);
                                $real_bet_sum_s = intval($row['real_bet_sum_s']);
                                $real_take_sum_s = intval($row['real_take_sum_s']);
                                $real_sum_s = intval($row['real_sum_s']);
                                $real_bet_sum_d = intval($row['real_bet_sum_d']);
                                $real_take_sum_d = intval($row['real_take_sum_d']);
                                $real_sum_d = intval($row['real_sum_d']);
                                
                                //                                 
                                $total_classic_bet_money = intval($row['total_classic_bet_money']);
                                $total_classic_win_money = intval($row['total_classic_win_money']);
                                $total_classic_lose_money = intval($row['total_classic_lose_money']);
                                

                                $mini_bet_sum_d = intval($row['mini_bet_sum_d']);
                                $mini_take_sum_d = intval($row['mini_take_sum_d']);
                                $mini_sum_d = intval($row['mini_sum_d']);

                                $total_casino_bet_money = intval($row['total_casino_bet_money']);
                                $total_casino_win_money = intval($row['total_casino_win_money']);
                                $total_casino_lose_money = intval($row['total_casino_lose_money']);

                                $total_slot_bet_money = intval($row['total_slot_bet_money']);
                                $total_slot_win_money = intval($row['total_slot_win_money']);
                                $total_slot_lose_money = intval($row['total_slot_lose_money']);

                                $total_hash_bet_money = intval($row['total_hash_bet_money']);
                                $total_hash_win_money = intval($row['total_hash_win_money']);
                                $total_hash_lose_money = intval($row['total_hash_lose_money']);
                                
                                $total_holdem_bet_money = intval($row['total_holdem_bet_money']);
                                $total_holdem_win_money = intval($row['total_holdem_win_money']);
                                $total_holdem_lose_money = intval($row['total_holdem_lose_money']);
                                ?>

                                <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                    <td style='text-align:left;'>
                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $db_dis_id ?></a>
                                    </td>
                                    <td style='text-align:left;'>
                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $db_id ?></a>
                                    </td>
                                    <td style='text-align:left;'>
                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $db_nick ?></a>
                                    </td>
                                    <td style='text-align:right; <?= '' ?>'>
                                        <?= number_format($ch_sum) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$ex_sum) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $diff_sum > 0 ? 'color:#0021FD' : ($diff_sum < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($diff_sum) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $diff_per_sum > 0 ? 'color:#0021FD' : ($diff_per_sum < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($diff_per_sum) . ' %' ?>
                                    </td>
                                    <!-- classic -->
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format($total_classic_bet_money) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$total_classic_win_money) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $total_classic_lose_money > 0 ? 'color:#0021FD' : ($total_classic_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($total_classic_lose_money) ?>
                                    </td>
                                    <!-- classic end -->
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format($pre_bet_sum_s) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$pre_take_sum_s) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $pre_sum_s > 0 ? 'color:#0021FD' : ($pre_sum_s < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($pre_sum_s) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format($pre_bet_sum_d) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$pre_take_sum_d) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $pre_sum_d > 0 ? 'color:#0021FD' : ($pre_sum_d < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($pre_sum_d) ?>
                                    </td>

                                        <?php /* 실시간 싱글 */ ?>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format($real_bet_sum_s) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$real_take_sum_s) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $real_sum_s > 0 ? 'color:#0021FD' : ($real_sum_s < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($real_sum_s) ?>
                                    </td>

                                        <?php /* 실시간 다폴더 */ ?>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format($real_bet_sum_d) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$real_take_sum_d) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $real_sum_d > 0 ? 'color:#0021FD' : ($real_sum_d < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($real_sum_d) ?>
                                    </td>

                                        <?php /* 미니게임 */ ?>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format($mini_bet_sum_d) ?>
                                    </td>
                                    <td style='text-align:right;  <?= '' ?>'>
                                        <?= number_format(-$mini_take_sum_d) ?>
                                    </td>
                                    <td style='text-align:right;  <?= $mini_sum_d > 0 ? 'color:#0021FD' : ($mini_sum_d < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($mini_sum_d) ?>
                                    </td>

                                        <?php /* 카지노 */ ?>
                                    <td data-user-casino-total-bet="<?= $db_id ?>" style='text-align:right;'>
                                        <?= number_format($total_casino_bet_money) ?>
                                    </td>
                                    <td data-user-casino-total-win="<?= $db_id ?>" style='text-align:right;'>
                                        <?= number_format($total_casino_win_money) ?>
                                    </td>
                                    <td data-user-casino-total-diff="<?= $db_id ?>" style='text-align:right;  <?= $total_casino_lose_money > 0 ? 'color:#0021FD' : ($total_casino_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($total_casino_lose_money) ?>
                                    </td>

                                        <?php /* 슬롯머신 */ ?>
                                    <td data-user-slot-total-bet="<?= $db_id ?>" style='text-align:right;'>
                                        <?= number_format($total_slot_bet_money) ?>
                                    </td>
                                    <td data-user-slot-total-win="<?= $db_id ?>" style='text-align:right;'>
                                        <?= number_format($total_slot_win_money) ?>
                                    </td>
                                    <td data-user-slot-total-diff="<?= $db_id ?>" style='text-align:right; <?= $total_slot_lose_money > 0 ? 'color:#0021FD' : ($total_slot_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($total_slot_lose_money) ?>
                                    </td>

                                        <?php /* 해시 */ ?>
                                    <td style='text-align:right;'>
                                        <?= number_format($total_hash_bet_money) ?>
                                    </td>
                                    <td style='text-align:right;'>
                                        <?= number_format($total_hash_win_money) ?>
                                    </td>
                                    <td style='text-align:right; <?= $total_hash_lose_money > 0 ? 'color:#0021FD' : ($total_hash_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($total_hash_lose_money) ?>
                                    </td>
                                    
                                    <?php /* 홀덤 */ ?>
                                    <td style='text-align:right;'>
                                        <?= number_format($total_holdem_bet_money) ?>
                                    </td>
                                    <td style='text-align:right;'>
                                        <?= number_format($total_holdem_win_money) ?>
                                    </td>
                                    <td style='text-align:right; <?= $total_holdem_lose_money > 0 ? 'color:#0021FD' : ($total_holdem_lose_money < 0 ? 'color:#FD0000' : '') ?>'>
                                        <?= number_format($total_holdem_lose_money) ?>
                                    </td>
                                    <?php } ?>
                        </table>
                        <?php
                        include_once(_BASEPATH . '/common/page_num.php');
                        ?>
                    </div>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>
            <?php
            include_once(_BASEPATH . '/common/bottom.php');
            ?>
        <script>
            function setDate(sdate, edate) {
                var fm = document.search;

                fm.srch_s_date.value = sdate;
                fm.srch_e_date.value = edate;
            }

            function goSearch() {
                var fm = document.search;

                if ((fm.srch_key.value != '') && (fm.srch_val.value == '')) {

                }

                var fm = document.search;

                fm.method = "get";
                fm.submit();
            }

            function goOrderby(pVal = null, ptype = null) {
                var fm = document.search;

                fm.s_ob.value = pVal;
                fm.s_ob_type.value = ptype;

                fm.method = "get";
                fm.submit();
            }
        </script>


        <?php if (defined('API_GATEWAY_BASE_URL')): ?>
        <script>
        // SBCasino live and slot
        (function ($) {
            ApiGateway.init('<?php echo API_GATEWAY_BASE_URL ?>', 'BTS');

            const numberFormat = (amount) => {
                let formatting = Intl.NumberFormat('en-US');
                return formatting.format(amount);
            }
        
            function fetchStats(startDate, endDate) {
                ApiGateway.fetch(
                    'sbcasino/stats',
                    'GET',
                    {
                        start_date: startDate.replace(/\//g, '-'),
                        end_date: endDate.replace(/\//g, '-'),
                    },
                    {
                        beforeFetch: () => {},
                        afterFetch: (response) => {
                            if (response.success) {
                                const live = response.data.live
                                $('[data-casino-total-bet]').text(numberFormat(live.total_bet))
                                $('[data-casino-total-win]').text(numberFormat(live.total_win))
                                $('[data-casino-total-diff]').text(
                                    numberFormat(parseFloat(live.total_bet) - parseFloat(live.total_win))
                                )
                                
                                const slot = response.data.slot
                                $('[data-slot-total-bet]').text(numberFormat(slot.total_bet))
                                $('[data-slot-total-win]').text(numberFormat(slot.total_win))
                                $('[data-slot-total-diff]').text(
                                    numberFormat(parseFloat(slot.total_bet) - parseFloat(slot.total_win))
                                )
                            }
                        }
                    }
                )
            }

            function fetchStatsUsers(page, startDate, endDate) {
                ApiGateway.fetch(
                    'sbcasino/stats/users',
                    'GET',
                    {
                        page: page,
                        start_date: startDate.replace(/\//g, '-'),
                        end_date: endDate.replace(/\//g, '-'),
                    },
                    {
                        beforeFetch: () => {},
                        afterFetch: (response) => {
                            if (response.success) {
                                $.each(response.data, function (index, value) {
                                    $('[data-user-casino-total-bet="'+value.member_id+'"]').text(numberFormat(value.casino_total_bet))
                                    $('[data-user-casino-total-win="'+value.member_id+'"]').text(numberFormat(value.casino_total_win))
                                    $('[data-user-casino-total-diff="'+value.member_id+'"]').text(
                                        numberFormat(parseFloat(value.casino_total_bet) - parseFloat(value.casino_total_win))
                                    )
                                    $('[data-user-slot-total-bet="'+value.member_id+'"]').text(numberFormat(value.slot_total_bet))
                                    $('[data-user-slot-total-win="'+value.member_id+'"]').text(numberFormat(value.slot_total_win))
                                    $('[data-user-slot-total-diff="'+value.member_id+'"]').text(
                                        numberFormat(parseFloat(value.slot_total_bet) - parseFloat(value.slot_total_win))
                                    )
                                })
                            }
                        }
                    }
                )
            }

            fetchStats(
                '<?= $_GET['srch_s_date'] ?: '' ?>',
                '<?= $_GET['srch_e_date'] ?: '' ?>',
            )

            fetchStatsUsers(
                1,
                '<?= $_GET['srch_s_date'] ?: '' ?>',
                '<?= $_GET['srch_e_date'] ?: '' ?>',
            )
        })(jQuery)
        </script>
        <?php endif ?>
    </body>
</html>