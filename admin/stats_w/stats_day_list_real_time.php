<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

include_once(_LIBPATH . '/class_ComQuery.php');
include_once(_LIBPATH . '/class_ComQueryUser.php');
include_once(_LIBPATH . '/class_Code.php');
include_once(_LIBPATH . '/class_CodeUser.php');

$UTIL = new CommonUtil();

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
$dist_type = trim(isset($_REQUEST['dist_type']) ? $_REQUEST['dist_type'] : -1);
$dist_name = trim(isset($_REQUEST['dist_name']) ? $_REQUEST['dist_name'] : '');


$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if ($db_conn) {
        
    if('' != $dist_name){
        $sql = "select id from member where nick_name = ? ";
        $result_dist_name = $CASHAdminDAO->getQueryData_pre($sql,[$dist_name]);
        $dist_id = $result_dist_name[0]['id'];
    }

    // 정산 제외 유저 정보 가져오기
    $p_data['sql'] = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'users_excluded_from_settlement' ";
    $result_game_config = $CASHAdminDAO->getQueryData($p_data);
    $excluded_member_idx = $result_game_config[0]['set_type_val'];
    $where = '';
    $sel_dist_idx = $dist_id; // Selected Distributors
    // 총판목록
    //$param = array();
    // 총판목록
    if ($_SESSION['u_business'] == 0) {
        $distributorList = GameCode::getRecommandMemberInfos(0, $CASHAdminDAO);
    } else { // 하위총판도 가져와야 한다.
        $distributorList = GameCode::getRecommandMemberInfos($_SESSION['member_idx'], $CASHAdminDAO);
    }

    $where_new = " AND 1 = 1";
    $param_where_new = array();

    $where_dis = " AND 1 = 1";
    $param_dis = array();

    $where_kplay = " AND 1 = 1";
    if ($dist_id != '') {
        // 본인의 정보를 가져온다. 하위 총판의 idx를 가져와서 
        $p_data['sql'] = "select idx, u_business from member where id = ? or nick_name = ?";
        $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], [$dist_id, $dist_id]);
        if (isset($db_dataArr)) {
            $sel_dist_idx = $db_dataArr[0]['idx'];
            list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($sel_dist_idx, $CASHAdminDAO);
            $str_param = implode(',', $param_dist);
            $where_new = " AND T1.recommend_member in($str_param)";
            $where_dis = " AND parent.idx in($str_param)";
            $where_kplay = " AND PRT.idx in($str_param)";
        }
    } else if ($_SESSION['u_business'] > 0) { // 총판
        list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'], $CASHAdminDAO);
        $str_param = implode(',', $param_dist);
        $where_new = " AND T1.recommend_member in($str_param)";
        $where_dis = " AND parent.idx in($str_param)";
        $where_kplay = " AND PRT.idx in($str_param)";
    }

    // 총판유형별 검색
    if ($dist_type >= 0) {
        $arrDist = array();
        // 유형에 맞는 총판 가져오기
        $sql = "SELECT idx FROM member WHERE u_business > 1 and dist_type = ?";
        $resultDistType = $CASHAdminDAO->getQueryData_pre($sql, [$dist_type]);
        if (isset($resultDistType)) {
            foreach ($resultDistType as $key => $value) {
                $arrDist[] = $value['idx'];
            }
            $where_new = " AND T1.recommend_member in (" . implode(',', $arrDist) . ")";
            $where_dis = "AND parent.idx in (" . implode(',', $arrDist) . ")";
            $where_kplay = "AND PRT.idx in (" . implode(',', $arrDist) . ")";
        } else {
            $dist_type = -1;
            $UTIL->alertMessage('해당유형의 총판이 없습니다.');
        }
    }

    $shopConfig = array();
    $p_data['sql'] = ComQuery::getDistShopInfo();
    $result = $CASHAdminDAO->getQueryData($p_data);
    $shopConfig = null;
    foreach ($result as $key => $value) {
        $shopConfig[$value['member_idx']] = $value;
    }

    //if (0 < $sel_dist_idx) {
    //    $shopConfig[$sel_dist_idx]['recommend_member'] = 0;
    //}
    //CommonUtil::logWrite("minibetting shopConfig: " . json_encode($shopConfig), "info");

    $begin = new DateTime($db_srch_s_date);
    $end = new DateTime($db_srch_e_date);

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);

    $total_cnt = 0;
    $period = isset($period) ? $period : [];
    $stats_day = [];
    foreach ($period as $dt) {
        $str_dt = $dt->format("Ymd");
        if (strlen($str_dt) > 5) {
            $stats_day[$str_dt]['ymd'] = $dt->format("Y-m-d");
            $stats_day[$str_dt]['val'] = 0;
            $stats_day[$str_dt]['ch_val_user'] = 0;
            $stats_day[$str_dt]['ex_val_user'] = 0;
            $stats_day[$str_dt]['charge_user_cnt'] = 0;
            $total_cnt++;
        }
    }

    $str_dt = str_replace('-', '', $p_data['db_srch_e_date']);
    $stats_day[$str_dt]['val'] = 0;
    $stats_day[$str_dt]['ymd'] = $p_data['db_srch_e_date'];

    // 충전 환전 -- child.recommend_member = parent.idx
    list($p_data['sql'], $param) = ComQuery::doComChExQuery($db_srch_s_date, $db_srch_e_date, $where_new, $param_where_new);
    //list($p_data['sql'], $param) = ComQueryUser::doComChExQuery($db_srch_s_date, $db_srch_e_date, $where_new, $param_where_new);
    //CommonUtil::logWrite("stats_day_list_new_tm doComChExQuery sql : " . $p_data['sql'], "info");
    //CommonUtil::logWrite("stats_day_list_new_tm doComChExQuery param : " . json_encode($param), "info");

    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

    //CommonUtil::logWrite("GameCodeUser doSumChExCalc db_dataArr : " . json_encode($db_dataArr), "info");
    // 충전,환전 합산 구하기 
    $tot_ch_val = $tot_ex_val = 0;
    $total_point = 0;
    $total_point_sub = 0;
    GameCode::doSumChExCalc($shopConfig, $stats_day, $db_dataArr, $total_point, $total_point_sub, $tot_ch_val, $tot_ex_val, $sel_dist_idx);
    //CommonUtil::logWrite("stats_day_list_new_tm doComChExQuery total_point : " . $total_point, "info");

    //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc 1 stats_day : " . json_encode($stats_day), "info");
    
    //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc 1 sel_dist_idx : " . $sel_dist_idx, "info");

    // 일반유저의 입출금 현황도 가져온다.
    if (0 == $_SESSION['u_business']  && 0 == $sel_dist_idx && -1 == $dist_type) {
        list($p_data['sql'], $param) = ComQueryUser::doComChExQuery($db_srch_s_date, $db_srch_e_date);

        $db_dataUserArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
        $db_dataUserArr = isset($db_dataUserArr) ? $db_dataUserArr : [];

        //CommonUtil::logWrite("GameCodeUser doSumChExCalc db_dataUserArr : " . json_encode($db_dataUserArr), "info");
        //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc 2 stats_day : " . json_encode($stats_day), "info");
        GameCodeUser::doSumChExCalc($stats_day, $db_dataUserArr, $tot_ch_val, $tot_ex_val);
    }


    // 전체죽장(입출)
    $tot_rate_buff = $tot_calculate_rate = 0;
    // Get the unit price
    GameCode::doChExUnitPriceCalc($tot_ch_val, $tot_ex_val, $tot_rate_buff, $tot_calculate_rate);




    // 게시판, 문의, 가입, 배팅회원
    $tot_qna = $tot_board = $tot_member = $tot_s_bet = $tot_d_bet = $tot_m_bet = $tot_charge_cnt = 0;
    $p_data['sql'] = ComQuery::doComBdQnJoinBetUserQuery($where_new);
    $param = array_merge([$db_srch_s_date, $db_srch_e_date]
            , $param_where_new
            , [$db_srch_s_date, $db_srch_e_date]
            , $param_where_new
            , [$db_srch_s_date, $db_srch_e_date]
            , [$db_srch_s_date, $db_srch_e_date]
            , $param_where_new
            , [$db_srch_s_date, $db_srch_e_date]
            , $param_where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];

    GameCode::doBdQnJoinBetUserCount($db_dataArr, $stats_day, $tot_qna, $tot_board, $tot_member, $tot_charge_cnt);

    $tot_devide_ch = $tot_ch_val ?? 0;
    $tot_charge_cnt = !empty($tot_charge_cnt) ? $tot_charge_cnt : 0;
    if ($tot_devide_ch > 0 && $tot_charge_cnt > 0) {
        $tot_guest_price = $tot_devide_ch / $tot_charge_cnt;
    }

    // 싱글 배팅회원수
    $p_data['sql'] = ComQuery::doComSingleBetUserCountQuery($where_new);
    $param = array_merge([$db_srch_s_date, $db_srch_e_date], $param_where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = true === isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doSingleBetUserCount($db_dataArr, $stats_day, $tot_s_bet);

    // 멀티 배팅회원수
    $p_data['sql'] = ComQuery::doComMultiBetUserCountQuery($where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doMultiBetUserCount($db_dataArr, $stats_day, $tot_d_bet);

    // 미니게임 배팅회원수
    $p_data['sql'] = ComQuery::doComMiniGameBetUserCountQuery($where_new);
    $param = array_merge([$db_srch_s_date, $db_srch_e_date, $excluded_member_idx], $param_where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
    GameCode::doMiniGameBetUserCount($db_dataArr, $stats_day, $tot_m_bet);

    // 포인트 적립 ,차감 
    $tot_p_point = $tot_m_point = 0;
    $p_data['sql'] = ComQuery::doComPointPMQuery($where_new);
    $param = array_merge([$db_srch_s_date, $db_srch_e_date], $param_where_new);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doPointPMUserCount($db_dataArr, $stats_day, $tot_p_point, $tot_m_point);

    // 배팅 합계용 변수
    $pre_bet_sum_s = 0;
    $pre_take_sum_s = 0;
    $pre_sum_s = 0;

    $pre_bet_sum_d = 0;
    $pre_take_sum_d = 0;
    $pre_sum_d = 0;

    $real_bet_sum_s = 0;
    $real_take_sum_s = 0;
    $real_sum_s = 0;
    $real_bet_sum_d = 0;
    $real_take_sum_d = 0;
    $real_sum_d = 0;

    // 클래식 
    $total_classic_bet_money = 0;
    $total_classic_win_money = 0;
    $total_classic_lose_money = 0;

    $param = array_merge([$db_srch_s_date, $db_srch_e_date], $param_dis);
    $p_data['sql'] = ComQuery::doSportsRealForderBetQuery($db_srch_s_date, $db_srch_e_date, $where_dis);
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doSumSportsRealBetCalc($db_dataArr, $stats_day, $shopConfig
            , $pre_bet_sum_s, $pre_take_sum_s, $pre_sum_s
            , $pre_bet_sum_d, $pre_take_sum_d, $pre_sum_d
            , $real_bet_sum_s, $real_take_sum_s, $real_sum_s
            , $real_bet_sum_d, $real_take_sum_d, $real_sum_d
            , $total_classic_bet_money, $total_classic_win_money, $total_classic_lose_money
            , $total_point, $total_point_sub, $sel_dist_idx
    );
    //CommonUtil::logWrite("stats_day_list_new_tm doSumSportsRealBetCalc total_point : " . $total_point, "info");     
    // 미니게임 베팅 $excluded_member_idx 값은 포함해서 보여준다.
    $mini_bet_sum_d = 0;
    $mini_take_sum_d = 0;
    $mini_sum_d = 0;
    $param = array_merge([$db_srch_s_date, $db_srch_e_date], $param_dis);
    array_push($param, $excluded_member_idx);
    $p_data['sql'] = ComQuery::doMiniBetQuery($db_srch_s_date, $db_srch_e_date, $excluded_member_idx, $where_dis);
    CommonUtil::logWrite("stats_day_list_new_tm doMiniBetQuery query : " .  $p_data['sql'] , "info");     
    
    $db_dataArr = $CASHAdminDAO->getQueryData_pre($p_data['sql'], $param);
    $db_dataArr = isset($db_dataArr) ? $db_dataArr : [];
    GameCode::doSumMiniBetCalc($db_dataArr, $stats_day, $shopConfig
            , $mini_bet_sum_d, $mini_take_sum_d, $mini_sum_d, $total_point, $total_point_sub, $sel_dist_idx);

    
    // 카지노 
    $total_casino_bet_money = 0;
    $total_casino_win_money = 0;
    $total_casino_lose_money = 0;

    $p_data['sql'] = ComQuery::doCasinoByDistQuery($db_srch_s_date, $db_srch_e_date, $where_kplay);
    $db_casinoDataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_casinoDataArr = isset($db_casinoDataArr) ? $db_casinoDataArr : [];

    GameCode::doSumCasinoBetCalc($db_casinoDataArr, $stats_day, $shopConfig
            , $total_casino_bet_money, $total_casino_win_money, $total_casino_lose_money, $total_point, $total_point_sub, $sel_dist_idx);

    //CommonUtil::logWrite("stats_day_list_new_tm doSumCasinoBetCalc total_point : " . $total_point, "info");     
    // 슬롯 
    $total_slot_bet_money = 0;
    $total_slot_win_money = 0;
    $total_slot_lose_money = 0;
    $p_data['sql'] = ComQuery::doSlotByDistQuery($db_srch_s_date, $db_srch_e_date, $where_kplay);
    $db_slotDataArr = $CASHAdminDAO->getQueryData($p_data);
    $db_slotDataArr = isset($db_slotDataArr) ? $db_slotDataArr : [];
    GameCode::doSumSlotBetCalc($db_slotDataArr, $stats_day, $shopConfig
            , $total_slot_bet_money, $total_slot_win_money, $total_slot_lose_money, $total_point, $total_point_sub, $sel_dist_idx);

    //CommonUtil::logWrite("stats_day_list_new_tm doSumSlotBetCalc total_point : " . $total_point, "info");     
    // 이스포츠 / 키론
    $total_espt_bet_money = 0;
    $total_espt_win_money = 0;
    $total_espt_lose_money = 0;

    if ('ON' == IS_ESPORTS_KEYRON) {
        $p_data['sql'] = ComQuery::doEsptByDistQuery($db_srch_s_date, $db_srch_e_date, $where_kplay);
        $db_esptDataArr = $CASHAdminDAO->getQueryData($p_data);
        $db_esptDataArr = isset($db_esptDataArr) ? $db_esptDataArr : [];

        GameCode::doSumEsptBetCalc($db_esptDataArr, $stats_day, $shopConfig
                , $total_espt_bet_money, $total_espt_win_money, $total_espt_lose_money, $total_point, $total_point_sub, $sel_dist_idx);

        //CommonUtil::logWrite("stats_day_list_new_tm doSumEsptBetCalc total_point : " . $total_point, "info");     
    }

    // 해시
    $total_hash_bet_money = 0;
    $total_hash_win_money = 0;
    $total_hash_lose_money = 0;

    if ('ON' == IS_HASH) {
        $p_data['sql'] = ComQuery::doHashByDistQuery($db_srch_s_date, $db_srch_e_date, $where_kplay);
        $db_hashDataArr = $CASHAdminDAO->getQueryData($p_data);
        $db_hashDataArr = isset($db_hashDataArr) ? $db_hashDataArr : [];
        GameCode::doSumHashBetCalc($db_hashDataArr, $stats_day, $shopConfig
                , $total_hash_bet_money, $total_hash_win_money, $total_hash_lose_money, $total_point, $total_point_sub, $sel_dist_idx);

        //CommonUtil::logWrite("stats_day_list_new_tm doSumHashBetCalc total_point : " . $total_point, "info");     
    }

    // holdem
    $total_holdem_bet_money = 0;
    $total_holdem_win_money = 0;
    $total_holdem_lose_money = 0;

    if ('ON' == IS_HOLDEM) {
        $p_data['sql'] = ComQuery::doHoldemByDistQuery($db_srch_s_date, $db_srch_e_date, $where_kplay);
        $db_holdemDataArr = $CASHAdminDAO->getQueryData($p_data);
        $db_holdemDataArr = isset($db_holdemDataArr) ? $db_holdemDataArr : [];
        GameCode::doSumHoldemBetCalc($db_holdemDataArr, $stats_day, $shopConfig
                , $total_holdem_bet_money, $total_holdem_win_money, $total_holdem_lose_money, $total_point, $total_point_sub, $sel_dist_idx);

        //CommonUtil::logWrite("stats_day_list_new_tm doHoldemByDistQuery total_point : " . $total_point, "info");     
    }

    // 정산내역
    //GameCode::doStatsDay($stats_day, $total_point);

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
        const sel_dist_type =<?= $dist_type ?>;

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

            $('#dist_type').val(sel_dist_type).prop('selected', true);
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

$menu_name = "stats_day_list_real_time";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_assessment vam ml20 mr10"></i>
                        <h4>실시간 날짜별 현황</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <div class="panel_tit">
                            <div class="search_form fl">
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
                                <div class="pl30">
                                    <select class="mr30" name="dist_type" id="dist_type" style="width: 100%">
                                        <option value="-1">전체</option>
                                        <option value="0">기본</option>
                                        <option value="1">죽장</option>
                                        <option value="2">롤링</option>
                                        <option value="3">배너</option>
                                    </select>
                                </div>
                                <div>
                                    <select name="dist_id" id="dist_id" style="width: 100%">
                                        <option value="">전체</option>
                                        <?php foreach ($distributorList as $key => $item) { ?>
                                            <option value="<?= $item['id'] ?>"   <?php if ($dist_id == $item['id']): ?> selected<?php endif; ?>><?= $item['nick_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" name="dist_name" id="dist_name"  placeholder="총판명 입력" value="<?= $dist_name ?>"/>
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
                                <th colspan="3">E스포츠/키론</th>
                                <th colspan="3">홀덤</th>
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
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_point, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_point_sub, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($tot_ch_val, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $tot_ex_val) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($tot_ch_val, $tot_ex_val, 1) ?></td>
                                <td style='text-align:right;'><?= $tot_calculate_rate ?></td>
                                <td style='text-align:right;'><?= isset($tot_guest_price) ? number_format(intval($tot_guest_price)) : 0 ?></td>
                                <!-- classic -->
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_classic_bet_money, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $total_classic_win_money) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_classic_lose_money, 0, 1) ?></td>



                                <td style='text-align:right;'><?= GameCode::strColorRet($pre_bet_sum_s, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $pre_take_sum_s) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($pre_sum_s, 0, 1) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($pre_bet_sum_d, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $pre_take_sum_d) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($pre_sum_d, 0, 1) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($real_bet_sum_s, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $real_take_sum_s) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($real_sum_s, 0, 1) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($real_bet_sum_d, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $real_take_sum_d) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($real_sum_d, 0, 1) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($mini_bet_sum_d, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $mini_take_sum_d) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($mini_sum_d, 0, 1) ?></td>

                                <!-- 카지노 -->
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_casino_bet_money, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $total_casino_win_money) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_casino_lose_money, 0, 1) ?></td>

                                <!-- 슬롯머신 -->
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_slot_bet_money, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $total_slot_win_money) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_slot_lose_money, 0, 1) ?></td>
                                
                                <!-- E스포츠/키론 -->
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_espt_bet_money, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_espt_win_money) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_espt_lose_money, 0, 1) ?></td>
                                
                                <!-- 홀덤 -->
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_holdem_bet_money, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $total_holdem_win_money) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_holdem_lose_money, 0, 1) ?></td>

                                <!-- 해시게임 -->
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_hash_bet_money, 0) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $total_hash_win_money) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($total_hash_lose_money, 0, 1) ?></td>

                                <td style='text-align:right;'><?= GameCode::strColorRet(0, $tot_p_point) ?></td>
                                <td style='text-align:right;'><?= GameCode::strColorRet($tot_m_point, 0) ?></td>

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
                                        //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc 3 stats_day : " . json_encode($stats_day), "info");

                                        foreach (array_reverse($stats_day) as $row) {
                                            if ($row['ymd'] == '') {
                                                continue;
                                            }

                                            //$row['ch_val'] = true === isset($row['ch_val']) ? $row['ch_val'] : 0  + $row['ch_val_user'];
                                            //$row['ex_val'] = true === isset($row['ex_val']) ? $row['ex_val'] : 0  + $row['ex_val_user'];
                                            $row['ch_val'] = (isset($row['ch_val']) ? $row['ch_val'] : 0)  + $row['ch_val_user'];
                                            $row['ex_val'] = (isset($row['ex_val']) ? $row['ex_val'] : 0)  + $row['ex_val_user'];

                                            //CommonUtil::logWrite("roof ch_val 3 row : " . json_encode($row), "info");

                                            $devide_ch = $row['ch_val'] ?? 0;
                                            $charge_cnt = !empty($row['charge_cnt']) ? $row['charge_cnt'] : 0;
                                            $charge_cnt = $charge_cnt + $row['charge_user_cnt'];
                                            $guest_price = 0;
                                            if ($devide_ch > 0 && $charge_cnt > 0) {
                                                $guest_price = $devide_ch / $charge_cnt;
                                            }
                                            $db_rate_buff = $db_calculate_rate = 0;

                                            // 정산금
                                            $cal_point = true === isset($row['cal_point']) && false === empty($row['cal_point']) ? $row['cal_point'] : 0;
                                            // 하부총판 정산금
                                            $cal_point_sub = true === isset($row['cal_point_sub']) && false === empty($row['cal_point_sub']) ? $row['cal_point_sub'] : 0;

                                            if (($row['ch_val'] ?? 0 > 0) && ($row['ex_val'] ?? 0 > 0)) {
                                                $db_rate_buff = (100 - (($row['ex_val'] / $row['ch_val']) * 100));
                                                $db_calculate_rate = sprintf('%0.2f', $db_rate_buff); // 520 -> 520.00

                                                if ($db_calculate_rate >= 0) {
                                                    $db_calculate_rate = "<font color='blue'>$db_calculate_rate %</font>";
                                                } else {
                                                    $db_calculate_rate = "<font color='red'>$db_calculate_rate %</font>";
                                                }
                                            } else {
                                                if (($row['ex_val'] ?? 0 < 1) && ($row['ch_val'] ?? 0 > 0)) {
                                                    //$db_calculate_rate = 100;
                                                    $db_calculate_rate = "<font color='blue'>100 %</font>";
                                                } elseif (($row['ex_val'] ?? 0 > 0) && ($row['ch_val'] ?? 0 < 1)) {
                                                    $db_calculate_rate = "<font color='red'>-100 %</font>";
                                                } else {
                                                    $db_calculate_rate = 0;
                                                }
                                            }
                                            ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $row['ymd'] ?></td>
                                            <td style='text-align:right;'><?= number_format(round($cal_point)) ?></td>
                                            <td style='text-align:right;'><?= number_format(round($cal_point_sub)) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['ch_val'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['ex_val'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['ch_val'] ?? 0, $row['ex_val'] ?? 0, 1) ?></td>
                                            <td style='text-align:right;'><?= $db_calculate_rate ?></td>
                                            <td style='text-align:right;'><?= isset($guest_price) ? number_format(intval($guest_price)) : 0 ?></td>
                                            <!-- classic -->
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_classic_bet_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['total_classic_win_money'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_classic_lose_money'] ?? 0, 0, 1) ?></td>

                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['pre_bet_sum_s'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['pre_take_sum_s'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['pre_sum_s'] ?? 0, 0, 1) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['pre_bet_sum_d'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['pre_take_sum_d'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['pre_sum_d'] ?? 0, 0, 1) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['real_bet_sum_s'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['real_take_sum_s'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['real_sum_s'] ?? 0, 0, 1) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['real_bet_sum_d'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['real_take_sum_d'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['real_sum_d'] ?? 0, 0, 1) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['mini_bet_sum_d'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['mini_take_sum_d'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['mini_sum_d'] ?? 0, 0, 1) ?></td>
                                            <!-- 카지노 -->
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_casino_bet_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['total_casino_win_money'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_casino_lose_money'] ?? 0, 0, 1) ?></td>
                                            <!-- 슬롯머신 -->
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_slot_bet_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['total_slot_win_money'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_slot_lose_money'] ?? 0, 0, 1) ?></td>
                                            
                                            <!-- E스포츠/키론 -->
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_espt_bet_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_espt_win_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_espt_lose_money'] ?? 0, 0) ?></td>
                                            
                                            <!-- Holdem -->
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_holdem_bet_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['total_holdem_win_money'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_hash_holdem_money'] ?? 0, 0, 1) ?></td>

                                            <!-- 해시게임 -->
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_hash_bet_money'] ?? 0, 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['total_hash_win_money'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['total_hash_lose_money'] ?? 0, 0, 1) ?></td>

                                            <td style='text-align:right;'><?= GameCode::strColorRet(0, $row['p_point'] ?? 0) ?></td>
                                            <td style='text-align:right;'><?= GameCode::strColorRet($row['m_point'] ?? 0, 0) ?></td>

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
        $("#dist_type").on("change", function(){
            let dist_type = $('#dist_type option:selected').val();
            let getUrl = '/member_w/_ajax_distributor_list.php';
            let param_val = {
                    dist_type: dist_type
                };
            let $resultHTML = '';

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: getUrl,
                data:param_val,
                success: function (data) {
                    if(data['retCode'] == "1000"){
                        $list = data['list'];
                        $resultHTML += `<option value="">전체</option>`;
                        for(let i=0; i<$list.length; i++){
                            $resultHTML += `<option value="${$list[i].id}">${$list[i].nick_name}</option>`;
                        }
                        $("#dist_id").html($resultHTML);
                    }else if(data['retCode'] == "2000") {
                        $resultHTML += `<option value="">없음</option>`;
                        $("#dist_id").html($resultHTML);
                    }else {
                        alert('실패하였습니다.');
                    }
                },
                error: function (request, status, error) {
                    alert('실패하였습니다.');
                }
            });
        });

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
            let dist_type = $('#dist_type option:selected').val();
            let dist_id = $('#dist_id option:selected').val();
            let dist_name = $('#dist_name').val();
            
            //lert(dist_type);
            //alert(dist_id);
            //alert(dist_name);
            /*console.log('dist_type : '+dist_type);
            console.log('dist_id : '+dist_id);
            console.log('dist_name : '+dist_name);*/
            //if('' != dist_name && (-1 != dist_type || '' != dist_id)){

            if('' != dist_id && '' != dist_name ){
                alert('총판유형, 총판 선택이 전체로 되어있어야 합니다.');
                return;
            }
            
            /*if('' == dist_id){
                alert('총판 선택이 되어있어야 합니다.');
                return;
            }*/
            
            var fm = document.search;
            fm.method = "get";
            fm.submit();
        }


    </script>
<?php ?>
</html>