<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

include_once(_LIBPATH . '/class_ComQuery.php');
include_once(_LIBPATH . '/class_Code.php');

$today = date("Y/m/d", strtotime("-2 day", time()));
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", mktime(0, 0, 0, intval(date('m')), 1, intval(date('Y'))));
$end_date = date("Y/m/d");
$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);
$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);
$db_srch_s_date = isset($p_data['db_srch_s_date']) ? $p_data['db_srch_s_date'] . " 00:00:00" : "NULL";
$db_srch_e_date = isset($p_data['db_srch_e_date']) ? $p_data['db_srch_e_date'] . " 23:59:59" : "NULL";
$dist_id = trim(isset($_REQUEST['dist_id']) ? $_REQUEST['dist_id'] : '');
$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if ($db_conn) {
    // 정산 제외 유저 정보 가져오기
    $p_data['sql'] = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'users_excluded_from_settlement' ";
    $result_game_config = $CASHAdminDAO->getQueryData($p_data);
    $excluded_member_idx = $result_game_config[0]['set_type_val'];

    // 총판목록
    if ($_SESSION['u_business'] == 0) {
        $distributorList = GameCode::getRecommandMemberInfos(0, $CASHAdminDAO);
    } else { // 하위총판도 가져와야 한다.
        $distributorList = GameCode::getRecommandMemberInfos($_SESSION['member_idx'], $CASHAdminDAO);
    }

    $where_new = " AND 1 = 1";
    $where_pm_point = " AND 1 = 1";
    $add_new = "";
    $add_day_new = " group by calculate_dt order by calculate_dt desc";

    if ($dist_id != '') {
        // 본인의 정보를 가져온다.
        $p_data['sql'] = "select idx, u_business from member where id = '$dist_id'";
        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
        $dist_idx = $db_dataArr[0]['idx'];
        
        $where_new = " AND T1.recommend_member ='$dist_idx' GROUP BY cr_dt";
        $where_pm_point = " AND T1.recommend_member ='$dist_idx' ";
        $add_new = " and member_idx = $dist_idx";
        $add_day_new = " and member_idx = $dist_idx group by calculate_dt order by calculate_dt desc";
    } else {
        if ($_SESSION['u_business'] > 0) {
            list($param_dist,$str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'],$CASHAdminDAO);
            $str_param = implode(',', $param_dist);
            $where_new = " AND T1.recommend_member in($str_param) GROUP BY cr_dt";
            $where_pm_point = " AND T1.recommend_member in($str_param) ";
            $add_new = " and member_idx in ($str_param)";
            $add_day_new = " and member_idx in ($str_param) group by calculate_dt order by calculate_dt desc";
        }
    }

    // 토탈금액 읽어온다
    $p_data['sql'] = ComQuery::doSumDistCalculate($db_srch_s_date, $db_srch_e_date, $add_new);
    $db_dataTotalSumArr = $CASHAdminDAO->getQueryData($p_data);

    // 날짜별 토탈긍액을 총판별로 읽어온다
    $p_data['sql'] = ComQuery::doDaySumDistCalculate($db_srch_s_date, $db_srch_e_date, $add_day_new);
    $db_dataDayTotalSumArr = $CASHAdminDAO->getQueryData($p_data);

    $stats_day = [];

    $begin = new DateTime($db_srch_s_date);
    $end = new DateTime($db_srch_e_date);

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);

    $total_cnt = 0;

    $period = isset($period) ? $period : [];
    $stats_day = [];
    foreach ($period as $dt) {
        $str_dt = $dt->format("Y-m-d");
        if (strlen($str_dt) > 5) {
            $stats_day[$str_dt]['calculate_dt'] = $dt->format("Y-m-d");
            $stats_day[$str_dt]['cal_point'] = 0;
            $stats_day[$str_dt]['low_cal_point'] = 0;
            $stats_day[$str_dt]['ch_val'] = 0;
            $stats_day[$str_dt]['ex_val'] = 0;
            $stats_day[$str_dt]['charge_cnt'] = 0;

            $stats_day[$str_dt]['pre_bet_sum_s'] = 0;
            $stats_day[$str_dt]['pre_take_sum_s'] = 0;
            $stats_day[$str_dt]['pre_sum_s'] = 0;

            $stats_day[$str_dt]['pre_bet_sum_d'] = 0;
            $stats_day[$str_dt]['pre_take_sum_d'] = 0;
            $stats_day[$str_dt]['pre_sum_d'] = 0;

            $stats_day[$str_dt]['real_bet_sum_s'] = 0;
            $stats_day[$str_dt]['real_take_sum_s'] = 0;
            $stats_day[$str_dt]['real_sum_s'] = 0;

            $stats_day[$str_dt]['real_bet_sum_d'] = 0;
            $stats_day[$str_dt]['real_take_sum_d'] = 0;
            $stats_day[$str_dt]['real_sum_d'] = 0;

            $stats_day[$str_dt]['mini_bet_sum_d'] = 0;
            $stats_day[$str_dt]['mini_take_sum_d'] = 0;
            $stats_day[$str_dt]['mini_sum_d'] = 0;

            $stats_day[$str_dt]['total_casino_bet_money'] = 0;
            $stats_day[$str_dt]['total_casino_win_money'] = 0;
            $stats_day[$str_dt]['total_casino_lose_money'] = 0;

            $stats_day[$str_dt]['total_slot_bet_money'] = 0;
            $stats_day[$str_dt]['total_slot_win_money'] = 0;
            $stats_day[$str_dt]['total_slot_lose_money'] = 0;

            $stats_day[$str_dt]['total_espt_bet_money'] = 0;
            $stats_day[$str_dt]['total_espt_win_money'] = 0;
            $stats_day[$str_dt]['total_espt_lose_money'] = 0;

            $stats_day[$str_dt]['total_hash_bet_money'] = 0;
            $stats_day[$str_dt]['total_hash_win_money'] = 0;
            $stats_day[$str_dt]['total_hash_lose_money'] = 0;
                        
            // 클래식 베팅 정보
            $stats_day[$str_dt]['total_classic_bet_money'] = 0;
            $stats_day[$str_dt]['total_classic_win_money'] = 0;
            $stats_day[$str_dt]['total_classic_lose_money'] = 0;
            $total_cnt++;
        }
    }

    foreach ($db_dataDayTotalSumArr as $shCal) {
        $str_dt = $shCal['calculate_dt'];
        $stats_day[$str_dt] = $shCal;
        //++$total_cnt;
    }
   
    //CommonUtil::logWrite("stats_day: " . json_encode($stats_day), "info");

    $total_point = $db_dataTotalSumArr[0]['total_point'];
    $total_low_point = $db_dataTotalSumArr[0]['total_low_point'];

    $total_point = GameCode::strColorRet($total_point, 0);
    $total_low_point = GameCode::strColorRet($total_low_point, 0);
    $tot_ch_val = $db_dataTotalSumArr[0]['tot_ch_val'];
    $tot_ex_val = $db_dataTotalSumArr[0]['tot_ex_val'];

    $tot_ch_val_str = GameCode::strColorRet($db_dataTotalSumArr[0]['tot_ch_val'], 0);
    $tot_ex_val_str = GameCode::strColorRet(0, $db_dataTotalSumArr[0]['tot_ex_val']);
    $tot_ret_money_str = GameCode::strColorRet($tot_ch_val, $tot_ex_val, 1);
    // 전체죽장(입출)

    $tot_rate_buff = $tot_calculate_rate = 0;

    if (($tot_ch_val > 0) && ($tot_ex_val > 0)) {
        $tot_rate_buff = (100 - (($tot_ex_val / $tot_ch_val) * 100));
        $tot_calculate_rate = sprintf('%0.2f', $tot_rate_buff); // 520 -> 520.00

        if ($tot_calculate_rate >= 0) {
            $tot_calculate_rate = "<font color='blue'>$tot_calculate_rate %</font>";
        } else {
            $tot_calculate_rate = "<font color='red'>$tot_calculate_rate %</font>";
        }
    } else if ($tot_ch_val == 0 && $tot_ex_val == 0) {
        $tot_calculate_rate = "<font>0</font>";
    } else {
        if (($tot_ex_val < 1) && ($tot_ch_val > 0)) {
            $tot_calculate_rate = 100;
            $tot_calculate_rate = "<font color='blue'>100 %</font>";
        } else if (($tot_ex_val > 0) && ($tot_ch_val < 1)) {
            $tot_calculate_rate = "<font color='red'>-100 %</font>";
        } else {
            $tot_calculate_rate = "<font color='blue'>$tot_calculate_rate %</font>";
        }
    }

    // 게시판, 문의, 가입, 배팅회원
    $p_data['sql'] = ComQuery::getBoadQnaJoinBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new);
    $db_dataBoardArr = $CASHAdminDAO->getQueryData($p_data);

    CommonUtil::logWrite("getBoadQnaJoinBetMemberCount sql : " . $p_data['sql'], "info");
    CommonUtil::logWrite("getBoadQnaJoinBetMemberCount data : " . json_encode($db_dataBoardArr), "info");
     
    $tot_qna = $tot_board = $tot_member = $tot_s_bet = $tot_d_bet = $tot_m_bet = $tot_charge_cnt = 0;
    $db_dataBoardArr = isset($db_dataBoardArr) ? $db_dataBoardArr : [];
    foreach ($db_dataBoardArr as $row) {
        if(!isset($row['cr_dt'])){
            continue;
        }
        $str_dt = $row['cr_dt'];

        if ($row['stype'] == 'qna') {
            $stats_day[$str_dt]['qna'] = $row['cnt'];
            $tot_qna += $row['cnt'];
        } else if ($row['stype'] == 'board') {
            $stats_day[$str_dt]['board'] = $row['cnt'];
            $tot_board += $row['cnt'];
        } else if ($row['stype'] == 'member') {
            $stats_day[$str_dt]['member'] = $row['cnt'];
            $tot_member += $row['cnt'];
        } else if ($row['stype'] == 'bet') {
            $stats_day[$str_dt]['bet'] = $row['cnt'];
        }
        // 객단가 용 충전건수
        else if ($row['stype'] == 'charge_cnt') {
            $stats_day[$str_dt]['charge_cnt'] = $row['cnt'];
            $tot_charge_cnt += $row['cnt'];
        }
    }

    $tot_devide_ch = $tot_ch_val ?? 0;
    $tot_charge_cnt = !empty($tot_charge_cnt) ? $tot_charge_cnt : 0;
    if ($tot_devide_ch > 0 && $tot_charge_cnt > 0) {
        $tot_guest_price = $tot_devide_ch / $tot_charge_cnt;
    }

    // 싱글 배팅회원수
    $p_data['sql'] = ComQuery::getSingleBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) ? $db_dataArr : [];
    foreach ($db_dataArr as $row) {
        $str_dt = $row['cr_dt'];
        $stats_day[$str_dt]['s_bet'] = $row['cnt'];
        $tot_s_bet += $row['cnt'];
    }

    // 멀티 배팅회원수
    $p_data['sql'] = ComQuery::getMultiBetMemberCount($db_srch_s_date, $db_srch_e_date, $where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    foreach ($db_dataArr as $row) {
        $str_dt = $row['cr_dt'];
        $stats_day[$str_dt]['d_bet'] = $row['cnt'];
        $tot_d_bet += $row['cnt'];
    }


    // 미니게임 배팅회원수
    $p_data['sql'] = ComQuery::getMiniGameBetMemberCount($db_srch_s_date, $db_srch_e_date, $excluded_member_idx, $where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    foreach ($db_dataArr as $row) {
        $str_dt = $row['cr_dt'];
        $stats_day[$str_dt]['m_bet'] = $row['cnt'];
        $tot_m_bet += $row['cnt'];
    }

    // 포인트 증차감 합
    $p_data['sql'] = ComQuery::getPMPointSum($db_srch_s_date, $db_srch_e_date, $where_pm_point);
    //CommonUtil::logWrite("stats_day_list t_log_cash : " . $p_data['sql'], "info");
    $db_dataArr = $CASHAdminDAO->getQueryData($p_data);

    $tot_p_point = $tot_m_point = 0;
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    foreach ($db_dataArr as $row) {
        $str_dt = $row['reg_dt'];
        if ($row['ac_code'] == '6' || $row['ac_code'] === '10') {
            $stats_day[$str_dt]['p_point'] = $row['s_point'];
            $tot_p_point += $row['s_point'];
        }
        if ($row['ac_code'] == '123' || $row['ac_code'] === '124') {
            $stats_day[$str_dt]['m_point'] = $row['s_point'];
            $tot_m_point += $row['s_point'];
        }
    }

    $tot_p_point_str = GameCode::strColorRet(0, $tot_p_point);
    $tot_m_point_str = GameCode::strColorRet($tot_m_point, 0);

    $tot_p_point_str = GameCode::strColorRet(0, $tot_p_point);
    $tot_m_point_str = GameCode::strColorRet($tot_m_point, 0);

    // 배팅 합계용 변수
    $pre_bet_sum_s = $db_dataTotalSumArr[0]['pre_bet_sum_s'];
    $pre_take_sum_s = $db_dataTotalSumArr[0]['pre_take_sum_s'];
    $pre_sum_s = $db_dataTotalSumArr[0]['pre_sum_s']; // 프리매치 싱글 차액

    $pre_bet_sum_d = $db_dataTotalSumArr[0]['pre_bet_sum_d'];
    $pre_take_sum_d = $db_dataTotalSumArr[0]['pre_take_sum_d'];
    $pre_sum_d = $db_dataTotalSumArr[0]['pre_sum_d'];

    $real_bet_sum_s = $db_dataTotalSumArr[0]['real_bet_sum_s'];
    $real_take_sum_s = $db_dataTotalSumArr[0]['real_take_sum_s'];
    $real_sum_s = $db_dataTotalSumArr[0]['real_sum_s'];

    $real_bet_sum_d = $db_dataTotalSumArr[0]['real_bet_sum_d'];
    $real_take_sum_d = $db_dataTotalSumArr[0]['real_take_sum_d'];
    $real_sum_d = $db_dataTotalSumArr[0]['real_sum_d'];
    
    //  클래식 
    $total_classic_bet_money = $db_dataTotalSumArr[0]['total_classic_bet_money'];
    $total_classic_win_money = $db_dataTotalSumArr[0]['total_classic_win_money'];
    $total_classic_lose_money = $db_dataTotalSumArr[0]['total_classic_lose_money'];
    
    // 배팅 합계용 변수
    $pre_bet_sum_s_str = GameCode::strColorRet($pre_bet_sum_s, 0);
    $pre_take_sum_s_str = GameCode::strColorRet(0, $pre_take_sum_s);
    $pre_sum_s_str = GameCode::strColorRet($pre_sum_s, 0, 1);
    $pre_bet_sum_d_str = GameCode::strColorRet($pre_bet_sum_d, 0);
    $pre_take_sum_d_str = GameCode::strColorRet(0, $pre_take_sum_d);
    $pre_sum_d_str = GameCode::strColorRet($pre_sum_d, 0, 1);
    $real_bet_sum_s_str = GameCode::strColorRet($real_bet_sum_s, 0);
    $real_take_sum_s_str = GameCode::strColorRet(0, $real_take_sum_s);
    $real_sum_s_str = GameCode::strColorRet($real_sum_s, 0, 1);
    $real_bet_sum_d_str = GameCode::strColorRet($real_bet_sum_d, 0);
    $real_take_sum_d_str = GameCode::strColorRet(0, $real_take_sum_d);
    $real_sum_d_str = GameCode::strColorRet($real_sum_d, 0, 1);

    // 클래식 
    $total_classic_bet_money_str = GameCode::strColorRet($total_classic_bet_money, 0);
    $total_classic_win_money_str = GameCode::strColorRet(0, $total_classic_win_money);
    $total_classic_lose_money_str = GameCode::strColorRet($total_classic_lose_money, 0, 1);
        
    $mini_bet_sum_d = $db_dataTotalSumArr[0]['mini_bet_sum_d'];
    $mini_take_sum_d = $db_dataTotalSumArr[0]['mini_take_sum_d'];
    $mini_sum_d = $db_dataTotalSumArr[0]['mini_sum_d'];

    $mini_bet_sum_d_str = GameCode::strColorRet($mini_bet_sum_d, 0);
    $mini_take_sum_d_str = GameCode::strColorRet(0, $mini_take_sum_d);
    $mini_sum_d_str = GameCode::strColorRet($mini_sum_d, 0, 1);

    // 카지노 
    $total_casino_bet_money = $db_dataTotalSumArr[0]['total_casino_bet_money'];
    $total_casino_win_money = $db_dataTotalSumArr[0]['total_casino_win_money'];
    $total_casino_lose_money = $db_dataTotalSumArr[0]['total_casino_lose_money'];
    $total_casino_bet_money_str = GameCode::strColorRet($total_casino_bet_money, 0);
    $total_casino_win_money_str = GameCode::strColorRet(0, $total_casino_win_money);
    $total_casino_lose_money_str = GameCode::strColorRet($total_casino_lose_money, 0, 1);

    // 슬롯 
    $total_slot_bet_money = $db_dataTotalSumArr[0]['total_slot_bet_money'];
    $total_slot_win_money = $db_dataTotalSumArr[0]['total_slot_win_money'];
    $total_slot_lose_money = $db_dataTotalSumArr[0]['total_slot_lose_money'];
    $total_slot_bet_money_str = GameCode::strColorRet($total_slot_bet_money, 0);
    $total_slot_win_money_str = GameCode::strColorRet(0, $total_slot_win_money);
    $total_slot_lose_money_str = GameCode::strColorRet($total_slot_lose_money, 0, 1);

    // 이스포츠 / 키론
    $total_espt_bet_money = $db_dataTotalSumArr[0]['total_espt_bet_money'];
    $total_espt_win_money = $db_dataTotalSumArr[0]['total_espt_win_money'];
    $total_espt_lose_money = $db_dataTotalSumArr[0]['total_espt_lose_money'];

    $total_espt_bet_money_str = '';
    $total_espt_win_money_str = '';
    $total_espt_lose_money_str = '';

    if ('ON' == IS_ESPORTS_KEYRON) {

        $total_espt_bet_money_str = GameCode::strColorRet($total_espt_bet_money, 0);
        $total_espt_win_money_str = GameCode::strColorRet(0, $total_espt_win_money);
        $total_espt_lose_money_str = GameCode::strColorRet($total_espt_lose_money, 0, 1);
    }

    // 해시
    $total_hash_bet_money = $db_dataTotalSumArr[0]['total_hash_bet_money'];
    $total_hash_win_money = $db_dataTotalSumArr[0]['total_hash_win_money'];
    $total_hash_lose_money = $db_dataTotalSumArr[0]['total_hash_lose_money'];
    $total_hash_bet_money_str = '';
    $total_hash_win_money_str = '';
    $total_hash_lose_money_str = '';
    if ('ON' == IS_HASH) {

        $total_hash_bet_money_str = GameCode::strColorRet($total_hash_bet_money, 0);
        $total_hash_win_money_str = GameCode::strColorRet(0, $total_hash_win_money);
        $total_hash_lose_money_str = GameCode::strColorRet($total_hash_lose_money, 0, 1);
    }



    // 하부총판이 있으면 정산을 계산해준다.
    //$total_point_sub = 0;

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
$menu_name = "stats_day";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_assessment vam ml20 mr10"></i>
                        <h4>날짜별 현황</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div>
                                    <select name="dist_id" id="sports_list" style="width: 100%">
                                        <option value="">전체</option>
                                        <?php foreach ($distributorList as $key => $item) { ?>
                                            <option value="<?= $item['id'] ?>"   <?php if ($dist_id == $item['id']): ?> selected<?php endif; ?>><?= $item['nick_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="daterange">
                                    <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                                    <input type="text" class="" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?= $p_data['srch_s_date'] ?>"/>
                                </div>
                                ~
                                <div class="daterange">
                                    <label for="datepicker-autoClose"><i class="mte i_date_range mte-1x vat"></i></label>
                                    <input type="text" class="" name="srch_e_date" id="datepicker-autoClose" placeholder="날짜선택"  value="<?= $p_data['srch_e_date'] ?>"/>
                                </div>
                                <div><a href="javascript:;" onClick="setDate('<?= $today ?>', '<?= $today ?>');" class="btn h30 btn_blu">최신</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_week ?>', '<?= $today ?>');" class="btn h30 btn_orange">1주일</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>', '<?= $today ?>');" class="btn h30 btn_green">한달</a></div>
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
                                <th rowspan="2">정산포인트</th>
                                <th rowspan="2">정산포인트(하부총판)</th>
                                <th rowspan="2">충전</th>
                                <th rowspan="2">환전</th>
                                <th rowspan="2">차액</th>
                                <th rowspan="2">수익률</th>
                                <th rowspan="2">객단가</th>
                                <th colspan="3">클래식</th>
                                <th colspan="3">프리매치 싱글</th>
                                <th colspan="3">프리매치 멀티</th>
                                <th colspan="3">실시간 싱글</th>
                                <th colspan="3">실시간 멀티</th>
                                <th colspan="3">미니게임</th>
                                <th colspan="3">카지노</th>
                                <th colspan="3">슬롯머신</th>
                                <th colspan="3">해시게임</th>
                                <th rowspan="2">포인트 적립</th>
                                <th rowspan="2">포인트 차감</th>
                                <!-- 총판관리쪽 제외 -->
<?php if (0 == $_SESSION['u_business']) { ?>
                                    <th rowspan="2">게시판</th>
                                    <th rowspan="2">고객센터</th>
                                    <th rowspan="2">가입</th>
                                    <th rowspan="2">입금건수</th>
                                    <th colspan="3">베팅회원수</th> 
<?php } ?>
                            </tr>
                            <tr>

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
                                <th>베팅</th>
                                <th>당첨</th>
                                <th>차액</th>

                                <!-- 총판관리쪽 제외 -->
<?php if (0 == $_SESSION['u_business']) { ?>
                                    <th>싱글</th> 
                                    <th>멀티</th> 
                                    <th>미니게임</th>
<?php } ?>
                            </tr>
                            <tr class="bg_orange">
                                <td>합 계</td>
                                <td style='text-align:right;'><?= $total_point ?></td>
                                <td style='text-align:right;'><?= $total_low_point ?></td>
                                <td style='text-align:right;'><?= $tot_ch_val_str ?></td>
                                <td style='text-align:right;'><?= $tot_ex_val_str ?></td>
                                <td style='text-align:right;'><?= $tot_ret_money_str ?></td>
                                <td style='text-align:right;'><?= $tot_calculate_rate ?></td>
                                <td style='text-align:right;'><?= isset($tot_guest_price) ? number_format(intval($tot_guest_price)) : 0 ?></td>
                                <!-- classic -->
                                <td style='text-align:right;'><?= $total_classic_bet_money_str ?></td>
                                <td style='text-align:right;'><?= $total_classic_win_money_str ?></td>
                                <td style='text-align:right;'><?= $total_classic_lose_money_str ?></td>

                                <td style='text-align:right;'><?= $pre_bet_sum_s_str ?></td>
                                <td style='text-align:right;'><?= $pre_take_sum_s_str ?></td>
                                <td style='text-align:right;'><?= $pre_sum_s_str ?></td>
                                <td style='text-align:right;'><?= $pre_bet_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $pre_take_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $pre_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $real_bet_sum_s_str ?></td>
                                <td style='text-align:right;'><?= $real_take_sum_s_str ?></td>
                                <td style='text-align:right;'><?= $real_sum_s_str ?></td>
                                <td style='text-align:right;'><?= $real_bet_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $real_take_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $real_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $mini_bet_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $mini_take_sum_d_str ?></td>
                                <td style='text-align:right;'><?= $mini_sum_d_str ?></td>

                                <!-- 카지노 -->
                                <td style='text-align:right;'><?= $total_casino_bet_money_str ?></td>
                                <td style='text-align:right;'><?= $total_casino_win_money_str ?></td>
                                <td style='text-align:right;'><?= $total_casino_lose_money_str ?></td>

                                <!-- 슬롯머신 -->
                                <td style='text-align:right;'><?= $total_slot_bet_money_str ?></td>
                                <td style='text-align:right;'><?= $total_slot_win_money_str ?></td>
                                <td style='text-align:right;'><?= $total_slot_lose_money_str ?></td>

                                <!-- 해시게임 -->
                                <td style='text-align:right;'><?= $total_hash_bet_money_str ?></td>
                                <td style='text-align:right;'><?= $total_hash_win_money_str ?></td>
                                <td style='text-align:right;'><?= $total_hash_lose_money_str ?></td>

                                <td style='text-align:right;'><?= $tot_p_point_str ?></td>
                                <td style='text-align:right;'><?= $tot_m_point_str ?></td>

                                <!-- 총판관리쪽 제외 -->
<?php if (0 == $_SESSION['u_business']) { ?>
                                    <td style='text-align:right;'><?= number_format($tot_board) ?></td>
                                    <td style='text-align:right;'><?= number_format($tot_qna) ?></td>
                                    <td style='text-align:right;'><?= number_format($tot_member) ?></td>
                                    <td style='text-align:right;'><?= number_format($tot_charge_cnt) ?></td>
                                    <td style='text-align:right;'><?= number_format($tot_s_bet) ?></td>
                                    <td style='text-align:right;'><?= number_format($tot_d_bet) ?></td>
                                    <td style='text-align:right;'><?= number_format($tot_m_bet) ?></td>
<?php } ?>
                            </tr>
                                <?php
                                if ($total_cnt > 0) {
                                    if (isset($stats_day)) {
                                        // 날짜 최신순으로
                                        foreach (array_reverse($stats_day) as $row) {

                                            $db_ret_money_str = GameCode::strColorRet($row['ch_val'] ?? 0, $row['ex_val'] ?? 0, 1);

                                            $devide_ch = $row['ch_val'] ?? 0;
                                            $charge_cnt = !empty($row['charge_cnt']) ? $row['charge_cnt'] : 0;
                                            if ($devide_ch > 0 && $charge_cnt > 0) {
                                                $guest_price = $devide_ch / $charge_cnt;
                                            }

                                            $db_rate_buff = $db_calculate_rate = 0;

                                            $pre_bet_sum_s = GameCode::strColorRet($row['pre_bet_sum_s'] ?? 0, 0);
                                            $pre_take_sum_s = GameCode::strColorRet(0, $row['pre_take_sum_s'] ?? 0);
                                            $pre_sum_s = GameCode::strColorRet($row['pre_sum_s'] ?? 0, 0, 1);
                                            $pre_bet_sum_d = GameCode::strColorRet($row['pre_bet_sum_d'] ?? 0, 0);
                                            $pre_take_sum_d = GameCode::strColorRet(0, $row['pre_take_sum_d'] ?? 0);
                                            $pre_sum_d = GameCode::strColorRet($row['pre_sum_d'] ?? 0, 0, 1);
                                            $real_bet_sum_s = GameCode::strColorRet($row['real_bet_sum_s'] ?? 0, 0);
                                            $real_take_sum_s = GameCode::strColorRet(0, $row['real_take_sum_s'] ?? 0);
                                            $real_sum_s = GameCode::strColorRet($row['real_sum_s'] ?? 0, 0, 1);
                                            $real_bet_sum_d = GameCode::strColorRet($row['real_bet_sum_d'] ?? 0, 0);
                                            $real_take_sum_d = GameCode::strColorRet(0, $row['real_take_sum_d'] ?? 0);
                                            $real_sum_d = GameCode::strColorRet($row['real_sum_d'] ?? 0, 0, 1);
                                            $mini_bet_sum_d = GameCode::strColorRet($row['mini_bet_sum_d'] ?? 0, 0);
                                            $mini_take_sum_d = GameCode::strColorRet(0, $row['mini_take_sum_d'] ?? 0);
                                            $mini_sum_d = GameCode::strColorRet($row['mini_sum_d'] ?? 0, 0, 1);

                                            $total_casino_bet_money = GameCode::strColorRet($row['total_casino_bet_money'] ?? 0, 0);
                                            $total_casino_win_money = GameCode::strColorRet(0, $row['total_casino_win_money'] ?? 0);
                                            $total_casino_lose_money = GameCode::strColorRet($row['total_casino_lose_money'] ?? 0, 0, 1);

                                            $total_slot_bet_money = GameCode::strColorRet($row['total_slot_bet_money'] ?? 0, 0);
                                            $total_slot_win_money = GameCode::strColorRet(0, $row['total_slot_win_money'] ?? 0);
                                            $total_slot_lose_money = GameCode::strColorRet($row['total_slot_lose_money'] ?? 0, 0, 1);

                                            $total_espt_bet_money = GameCode::strColorRet($row['total_espt_bet_money'] ?? 0, 0);
                                            $total_espt_win_money = GameCode::strColorRet(0, $row['total_espt_win_money'] ?? 0);
                                            $total_espt_lose_money = GameCode::strColorRet($row['total_espt_lose_money'] ?? 0, 0, 1);

                                            $total_hash_bet_money = GameCode::strColorRet($row['total_hash_bet_money'] ?? 0, 0);
                                            $total_hash_win_money = GameCode::strColorRet(0, $row['total_hash_win_money'] ?? 0);
                                            $total_hash_lose_money = GameCode::strColorRet($row['total_hash_lose_money'] ?? 0, 0, 1);
                                            
                                            // 클래식
                                            $total_day_classic_bet_money = GameCode::strColorRet($row['total_classic_bet_money'] ?? 0, 0);
                                            $total_day_classic_win_money = GameCode::strColorRet(0, $row['total_classic_win_money'] ?? 0);
                                            $total_day_classic_lose_money = GameCode::strColorRet($row['total_classic_lose_money'] ?? 0, 0, 1);
                                            
                                            
                                            CommonUtil::logWrite("stats_day: " . json_encode($row), "info");


                                            // 정산금
                                            $cal_point = $row['cal_point'];
                                            // 하부총판 정산금
                                            //$str_dt_sub = str_replace('-', '', $row['ymd']);
                                            $cal_point_sub = $row['low_cal_point'];

                                            if (($row['ch_val'] ?? 0 > 0) && ($row['ex_val'] ?? 0 > 0)) {
                                                if (0 < $row['ch_val']) {
                                                    $db_rate_buff = (100 - (($row['ex_val'] / $row['ch_val']) * 100));
                                                }

                                                $db_calculate_rate = sprintf('%0.2f', $db_rate_buff); // 520 -> 520.00

                                                if ($db_calculate_rate >= 0) {
                                                    $db_calculate_rate = "<font color='blue'>$db_calculate_rate %</font>";
                                                } else {
                                                    $db_calculate_rate = "<font color='red'>$db_calculate_rate %</font>";
                                                }
                                            } else {
                                                if (($row['ex_val'] ?? 0 < 1) && ($row['ch_val'] ?? 0 > 0)) {
                                                    $db_calculate_rate = 100;
                                                    $db_calculate_rate = "<font color='blue'>100 %</font>";
                                                } elseif (($row['ex_val'] ?? 0 > 0) && ($row['ch_val'] ?? 0 < 1)) {
                                                    $db_calculate_rate = "<font color='red'>-100 %</font>";
                                                } else {
                                                    $db_calculate_rate = '0.00 %';
                                                }
                                            }

                                            $db_ch_val_str = GameCode::strColorRet($row['ch_val'] ?? 0, 0);
                                            $db_ex_val_str = GameCode::strColorRet(0, $row['ex_val'] ?? 0);

                                            $db_p_point_str = GameCode::strColorRet(0, $row['p_point'] ?? 0);
                                            $db_m_point_str = GameCode::strColorRet($row['m_point'] ?? 0, 0);

                                            $db_m_point = $row['m_point'] ?? 0;
                                            if ($db_m_point > 0) {
                                                $db_m_point = "<font color='blue'>-" . number_format($db_m_point) . "</font>";
                                            } else {
                                                $db_m_point = 0;
                                            }

                                            $day_bet_money_str = GameCode::strColorRet($row['tot_bet_money'] ?? 0, 0);
                                            $day_prize_money_str = GameCode::strColorRet(0, $row['tot_prize_money'] ?? 0);
                                            ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $row['calculate_dt'] ?></td>
                                            <td style='text-align:right;'><?= number_format(round($cal_point)) ?></td>
                                            <td style='text-align:right;'><?= number_format(round($cal_point_sub)) ?></td>

                                            <td style='text-align:right;'><?= $db_ch_val_str ?></td>
                                            <td style='text-align:right;'><?= $db_ex_val_str ?></td>
                                            <td style='text-align:right;'><?= $db_ret_money_str ?></td>
                                            <td style='text-align:right;'><?= $db_calculate_rate ?></td>
                                            <td style='text-align:right;'><?= isset($guest_price) ? number_format(intval($guest_price)) : 0 ?></td>
                                            <!-- classic -->
                                            <td style='text-align:right;'><?= $total_day_classic_bet_money ?></td>
                                            <td style='text-align:right;'><?= $total_day_classic_win_money ?></td>
                                            <td style='text-align:right;'><?= $total_day_classic_lose_money ?></td>

                                            <td style='text-align:right;'><?= $pre_bet_sum_s ?></td>
                                            <td style='text-align:right;'><?= $pre_take_sum_s ?></td>
                                            <td style='text-align:right;'><?= $pre_sum_s ?></td>
                                            <td style='text-align:right;'><?= $pre_bet_sum_d ?></td>
                                            <td style='text-align:right;'><?= $pre_take_sum_d ?></td>
                                            <td style='text-align:right;'><?= $pre_sum_d ?></td>
                                            <td style='text-align:right;'><?= $real_bet_sum_s ?></td>
                                            <td style='text-align:right;'><?= $real_take_sum_s ?></td>
                                            <td style='text-align:right;'><?= $real_sum_s ?></td>
                                            <td style='text-align:right;'><?= $real_bet_sum_d ?></td>
                                            <td style='text-align:right;'><?= $real_take_sum_d ?></td>
                                            <td style='text-align:right;'><?= $real_sum_d ?></td>
                                            <td style='text-align:right;'><?= $mini_bet_sum_d ?></td>
                                            <td style='text-align:right;'><?= $mini_take_sum_d ?></td>
                                            <td style='text-align:right;'><?= $mini_sum_d ?></td>
                                            <!-- 카지노 -->
                                            <td style='text-align:right;'><?= $total_casino_bet_money ?></td>
                                            <td style='text-align:right;'><?= $total_casino_win_money ?></td>
                                            <td style='text-align:right;'><?= $total_casino_lose_money ?></td>
                                            <!-- 슬롯머신 -->
                                            <td style='text-align:right;'><?= $total_slot_bet_money ?></td>
                                            <td style='text-align:right;'><?= $total_slot_win_money ?></td>
                                            <td style='text-align:right;'><?= $total_slot_lose_money ?></td>

                                            <!-- 해시게임 -->
                                            <td style='text-align:right;'><?= $total_hash_bet_money ?></td>
                                            <td style='text-align:right;'><?= $total_hash_win_money ?></td>
                                            <td style='text-align:right;'><?= $total_hash_lose_money ?></td>

                                            <td style='text-align:right;'><?= $db_p_point_str ?></td>
                                            <td style='text-align:right;'><?= $db_m_point_str ?></td>

                                            <!-- 총판관리쪽 제외 -->
            <?php if (0 == $_SESSION['u_business']) { ?>
                                                <td style='text-align:right;'><?= number_format($row['board'] ?? 0) ?></td>
                                                <td style='text-align:right;'><?= number_format($row['qna'] ?? 0) ?></td>
                                                <td style='text-align:right;'><?= number_format($row['member'] ?? 0) ?></td>
                                                <td style='text-align:right;'><?= number_format($row['charge_cnt'] ?? 0) ?></td>
                                                <td style='text-align:right;'><?= number_format($row['s_bet'] ?? 0) ?></td>
                                                <td style='text-align:right;'><?= number_format($row['d_bet'] ?? 0) ?></td>
                                                <td style='text-align:right;'><?= number_format($row['m_bet'] ?? 0) ?></td>
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
<?php ?>
</html>