<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
include_once(_BASEPATH . '/common/login_check.php');
include_once(_LIBPATH . '/class_Code.php');
if (!isset($_SESSION)) {
    session_start();
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if ($db_conn) {

    $UTIL = new CommonUtil();
    
    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = date("Y/m/d", strtotime("-3 day", time()));
    $end_date = $today;

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }

    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['v_cnt']) : 50);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = 50;
    }

    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_key']) : 's_idnick');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_val']) : '');
    $p_data['betting_key'] = trim(isset($_REQUEST['betting_key']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['betting_key']) : 0);
    $p_data['betting_val'] = trim(isset($_REQUEST['betting_val']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['betting_val']) : 0);
    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_e_date']) : $end_date);
    $p_data['betting_type'] = trim(isset($_REQUEST['betting_type']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['betting_type']) : 'OFF');

    $fixture_id = trim(isset($_REQUEST['fixture_id']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['fixture_id']) : 0);
    $markets_id = trim(isset($_REQUEST['markets_id']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['markets_id']) : 0);
    $base_line = trim(isset($_REQUEST['base_line']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['base_line']) : '');
    $bet_name = trim(isset($_REQUEST['bet_name']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['bet_name']) : '');

    //$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';
    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
    //$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);

    $srch_basic = "";
    $srch_url = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND (member.id like '%" . $p_data['srch_val'] . "%' OR member.nick_name like '%" . $p_data['srch_val'] . "%') ";
                $srch_url .= "srch_key=s_idnick&srch_val=" . $p_data['srch_val'];
            }
            break;
        case "s_team":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND (p1.team_name like '%" . $p_data['srch_val'] . "%' OR p2.team_name like '%" . $p_data['srch_val'] . "%' ";
                $srch_basic .= " OR p1.display_name like '%" . $p_data['srch_val'] . "%' OR p2.display_name like '%" . $p_data['srch_val'] . "%')";
                $srch_url .= "srch_key=s_team&srch_val=" . $p_data['srch_val'];
            }
            break;
        case "s_distributor":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND member.dis_id = '" . $p_data['srch_val'] . "'";
                $srch_url .= "srch_key=s_distributor&srch_val=" . $p_data['srch_val'];
            }
            break;
    }
//$p_data["sql_where"] = " and (team_name = '$src_value' || display_name = '$src_value')";
    switch ($p_data["betting_key"]) {
        case 1:
            $srch_basic .= " AND member_bet.bet_status = 1 AND member_bet.idx not in (
                        select bet_idx from member_bet_detail where bet_status = 4 and bet_type = 1 group by bet_idx
                       )";
            $srch_url .= "&betting_key=" . $p_data["betting_key"] . "&bet_status=1";
            break;
        case 2:
            $srch_basic .= " AND member_bet.bet_status = 3 and total_bet_money < take_money";
            $srch_url .= "&betting_key=" . $p_data["betting_key"] . "&bet_status=2";
            break;
        case 3:
            $srch_basic .= " AND member_bet.bet_status = 3";
            $srch_url .= "&betting_key=" . $p_data["betting_key"] . "&bet_status=3";
            break;
        case 4:
            $srch_basic .= " AND member_bet.bet_status = 3 and take_money = 0";
            $srch_url .= "&betting_key=" . $p_data["betting_key"] . "&bet_status=4";
            break;
        case 5:
            $srch_basic .= " AND member_bet.bet_status = 5 OR (member_bet.bet_type = 1 AND member_bet.bet_status = 3 and total_bet_money = take_money)";
            $srch_url .= "&betting_key=" . $p_data["betting_key"] . "&bet_status=5";
            break;
        case 6:
            $srch_basic = " AND member_bet.bet_status = 6";
            $srch_url .= "&betting_key=" . $p_data["betting_key"] . "&bet_status=6";
            break;
    }

    if ($p_data['betting_val'] > 0) {
        $srch_basic .= " AND member_bet.total_bet_money >= " . $p_data['betting_val'];
        $srch_url .= "&betting_val=" . $p_data['betting_val'];
    }
    
    $srch_basic .= " AND member_bet.is_classic = '" . $p_data['betting_type']."'";
    //$srch_url .= "&betting_type='" . $p_data['betting_type'];
    $srch_url .= "&betting_type=" . $p_data['betting_type'];
    
    $p_data["table_name"] = " lsports_bet ";
    $p_data["sql_where"] = "";    
    $default_link = 'prematch_betting_list.php?srch_s_date=' . $p_data['db_srch_s_date'] . '&srch_e_date=' . $p_data['db_srch_e_date'];
    
    // 프로매치 관리 상세에서 넘어왔을시
    if($fixture_id > 0 && $markets_id > 0){
        $default_link = 'prematch_betting_list.php?';
        $srch_url .= "fixture_id=$fixture_id&markets_id=$markets_id&base_line=$base_line&bet_name=$bet_name";
    }
    
    if (strlen($srch_url) > 0) {
        $default_link .= $srch_url;
    }
    
    // 프리매치 목록 전체 읽음 처리
    $sql = 'update member_bet set is_open = 1 where idx > 0 and is_open = 0 and bet_type = 1';
    $LSportsAdminDAO->executeQuery($sql);

    $p_data['sql'] = "SELECT member_bet.idx,member_bet_detail.bet_status FROM member_bet";
    $p_data['sql'] .= " left join member_bet_detail on member_bet_detail.bet_idx = member_bet.idx";
    $p_data['sql'] .= " left join lsports_fixtures on member_bet_detail.ls_fixture_id = lsports_fixtures.fixture_id";
    $p_data['sql'] .= " left join lsports_participant as p1 on lsports_fixtures.fixture_participants_1_id = p1.fp_id";
    $p_data['sql'] .= " left join lsports_participant as p2 on lsports_fixtures.fixture_participants_2_id = p2.fp_id";
    $p_data['sql'] .= " left join member on member_bet.member_idx = member.idx";
    $p_data['sql'] .= " WHERE member_bet.bet_type = 1 AND member_bet.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND  member_bet.create_dt <= '" . $p_data['db_srch_e_date'] . "' ";
    $p_data['sql'] .= $srch_basic;

    if (0 < $fixture_id && 0 < $markets_id && $base_line != '') {
        $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id AND member_bet_detail.ls_markets_id = $markets_id AND member_bet_detail.ls_markets_base_line = '$base_line' AND member_bet_detail.bet_name = '$bet_name' ";
    } else if (0 < $fixture_id && 0 < $markets_id && $base_line == '') {
        $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id AND member_bet_detail.ls_markets_id = $markets_id AND member_bet_detail.bet_name = '$bet_name' ";
    } else if (0 < $fixture_id && 0 == $markets_id) {
        $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id";
    }

    if($_SESSION['u_business'] > 0){
        list($param_dist,$str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'],$LSportsAdminDAO);
        //$p_data['sql'] .= " AND recommend_member = ".$_SESSION['member_idx'];
        //$srch_basic .= " AND recommend_member = ".$_SESSION['member_idx'];
        $str_param = implode(',', $param_dist);
        $p_data['sql'] .= " AND recommend_member in($str_param)";
        $srch_basic .= " AND recommend_member in ($str_param) ";
    }
    $p_data['sql'] .= " group by member_bet.idx";

    $UTIL->logWrite("[prematch_betting_list] count query : " . $p_data['sql'], "error");

    $db_total_cnt = $LSportsAdminDAO->getQueryData($p_data);


    $total_cnt = count($db_total_cnt);


    $UTIL->logWrite("[prematch_betting_list] count : " . $total_cnt, "error");

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    if ($total_cnt > 0) {
        $p_data['sql'] = "SELECT member_bet.*, member.idx as member_idx, member.id, member.nick_name, member.is_monitor_bet ";
        $p_data['sql'] .= ", (SELECT mi.item_id FROM member_item mi WHERE mi.idx=member_bet.item_idx) AS item_id ";
        $p_data['sql'] .= " FROM member_bet";
        $p_data['sql'] .= " left join member_bet_detail on member_bet_detail.bet_idx = member_bet.idx";
        $p_data['sql'] .= " left join lsports_fixtures on member_bet_detail.ls_fixture_id = lsports_fixtures.fixture_id";
        $p_data['sql'] .= " left join lsports_participant as p1 on lsports_fixtures.fixture_participants_1_id = p1.fp_id";
        $p_data['sql'] .= " left join lsports_participant as p2 on lsports_fixtures.fixture_participants_2_id = p2.fp_id";
        $p_data['sql'] .= " left join member on member_bet.member_idx = member.idx";
        $p_data['sql'] .= " WHERE "
                . " member_bet.bet_type = 1 and member_bet.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND  member_bet.create_dt <= '" . $p_data['db_srch_e_date'] . "' ";

        $p_data['sql'] .= $srch_basic;
        if (0 < $fixture_id && 0 < $markets_id && $base_line != '') {
            $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id AND member_bet_detail.ls_markets_id = $markets_id AND member_bet_detail.ls_markets_base_line = '$base_line' AND member_bet_detail.bet_name = '$bet_name' ";
        } else if (0 < $fixture_id && 0 < $markets_id && $base_line == '') {
            $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id AND member_bet_detail.ls_markets_id = $markets_id AND member_bet_detail.bet_name = '$bet_name' ";
        } else if (0 < $fixture_id && 0 == $markets_id) {
            $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id";
        } else if (0 < $fixture_id && 0 == $markets_id) {
            $p_data['sql'] .= " AND member_bet_detail.ls_fixture_id = $fixture_id";
        }

        $p_data['sql'] .= " group by member_bet.idx order by create_dt desc";
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . ";";

        $UTIL->logWrite("[prematch_betting_list] data query : " . $p_data['sql'], "error");

        $db_dataArr = $LSportsAdminDAO->getQueryData($p_data);
    }

   
    if ($total_cnt > 0) {
        $detail_list = [];
        
        $db_dataArr = !isset($db_dataArr)  ?  [] : $db_dataArr;
        //if (true === isset($db_dataArr) && false === empty($db_dataArr)) {
            foreach ($db_dataArr as $key => $item) {
                $detail_list[] = $item['idx'];
            }
        //}

        $szDetail_list = implode(',', $detail_list);
        $tmDeatil = null;
        if (count($detail_list) > 0) {
            $p_data['sql'] = "select member_bet_detail.ls_fixture_id, member_bet_detail.idx, member_bet_detail.bet_idx, member_bet_detail.bet_status, member_bet_detail.bet_status as display_bet_status
                    ,IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity, ifnull(lsports_fixtures.fixture_start_date, member_bet_detail.fixture_start_date)) as fixture_start_date
                    , member_bet_detail.result_score, ";
            $p_data['sql'] .= "ls_markets_name, ls_markets_base_line, lsports_leagues.display_name as fixture_league_name";
            $p_data['sql'] .= ",member_bet_detail.bet_price as bet_price";
            $p_data['sql'] .= ",member_bet_detail.bet_name, IFNULL(lsports_fixtures.fixture_status, 3) as fixture_status, ";
            $p_data['sql'] .= "p1.team_name as p1_team_name, p1.display_name as fixture_participants_1_name, p2.team_name as p2_team_name, p2.display_name as fixture_participants_2_name from member_bet_detail ";
            $p_data['sql'] .= " LEFT JOIN member_bet as mb_bet on mb_bet.idx = member_bet_detail.bet_idx ";
            $p_data['sql'] .= " left join lsports_fixtures on member_bet_detail.ls_fixture_id = lsports_fixtures.fixture_id";
            $p_data['sql'] .= " left join lsports_participant as p1 on member_bet_detail.fixture_participants_1_id = p1.fp_id";
            $p_data['sql'] .= " left join lsports_participant as p2 on member_bet_detail.fixture_participants_2_id = p2.fp_id";
            $p_data['sql'] .= " left join lsports_leagues  on lsports_leagues.id = member_bet_detail.fixture_league_id";
            $p_data['sql'] .= " where bet_idx in ($szDetail_list) and mb_bet.bet_type = 1 ";
            $p_data['sql'] .= " group by member_bet_detail.idx;";
            
            
            CommonUtil::logWrite('prematch list query sql : ' .  $p_data['sql'], "error");
             
            $tmDeatil = $LSportsAdminDAO->getQueryData($p_data);
        }

        $db_dataArrDetail = null;
        if (true === isset($tmDeatil) && false === empty($tmDeatil)) {
            foreach ($tmDeatil as $key => $item) {
                $db_dataArrDetail[$item['bet_idx']][] = $item;
            }
        }
        unset($tmDeatil);
    }

    $p_data['sql'] = "select id, name from item where 1=1;";
    $tmItem = $LSportsAdminDAO->getQueryData($p_data);    
    $itemList = array();
    if (true === isset($tmItem) && false === empty($tmItem)) {
        foreach ($tmItem as $key => $item) {
            $itemList[$item['id']] = $item;
        }
    }
    unset($tmItem);

    $LSportsAdminDAO->dbclose();
}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
    <!--<![endif]-->

<?php
include_once(_BASEPATH . '/common/head.php');
?>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js?v=<?php echo date("YmdHis"); ?>"></script>
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
    <div id="set_game_result" name="set_game_result" class="pop-window">
        <div class="con_wrap">
            <div class="panel reserve">
                <div class="title">
                    게임결과
                </div>

                <div class="tline tline_flexbox">
                    <table id="popup_list" name="popup_list" class="mlist">
                        <tr>
                            <th colspan="3">결과</th>
                        </tr>
                        <tr>
                            <td></td>
                            <td>홈</td>
                            <td>원정</td>
                        </tr>
                        <tr id="result_score" style="display: none;">
                            <td>결과</td>
                            <td><input type="number" id="home_score_api" value="" min="1" max="28" readonly></td>
                            <td><input type="number" id="away_score_api" value="" min="1" max="28" readonly></td>
                        </tr>
                        <tr id="result_score_extra" style="display: none;">
                            <td>결과입력</td>
                            <td><input type="number" id="home_score" value="" min="1" max="28"></td>
                            <td><input type="number" id="away_score" value="" min="1" max="28"></td>
                        </tr>
                    </table>
                </div>

                <div class="panel_tit">
                    <div class="search_form fr">
                        <a href="javascript:fnUpdateScore();" class="btn h30 btn_blu">결과수정</a>
                        <a href="#" class="btn h30 btn_blu" onclick="javascript:fnPopupClose();">닫기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="memberIdxList" name="memberIdxList">
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap">
<?php
$menu_name = "prematch_betting_list";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">
            <?php //echo $sql; ?>
<?php //print_r($db_dataArr);   ?>
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>프리매치 배팅 목록</h4>
                    </a>
                </div>

                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="" style="padding-right: 10px;">
                                    <select name="betting_type" id="betting_type">
                                        <option value="OFF" <?='OFF' == $p_data['betting_type']?'selected':'' ?>>프리매치</option>
                                        <option value="ON"  <?='ON' == $p_data['betting_type']?'selected':'' ?>>클래식</option>
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
                                <div><a href="javascript:;" onClick="setDate('<?= $today ?>', '<?= $today ?>');" class="btn h30 btn_blu">오늘</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_week ?>', '<?= $today ?>');" class="btn h30 btn_orange">1주일</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>', '<?= $today ?>');" class="btn h30 btn_green">한달</a></div>
                                <div class="" style="padding-right: 10px;"></div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick">아이디 및 닉네임</option>
                                        <option value="s_team">팀명</option>
                                        <option value="s_distributor">총판아이디</option>

                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <input type="text" id="srch_val" name="srch_val" class=""  placeholder="" value="<?= $p_data['srch_val'] ?>" />
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="betting_key" id="betting_key">
                                        <option value="0">배팅상태</option>
                                        <option value="1">결과전</option>
                                        <option value="2">적중</option>
                                        <option value="3">정산완료</option>
                                        <option value="4">낙첨</option>
                                        <option value="5">취소&적특</option>
                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <input type="text" id="betting_val" name="betting_val" class="" value="<?= $p_data['betting_val'] ?>"  placeholder="최소배팅액" />
                                </div>
                                <div> <a href="javascript:goSearch();" class="btn h30 btn_red" >검색</a></div>
                                <!--<div><a href="#" class="btn h30 btn_blu" onclick="onBtnClickTotalCalculate()">전체 정산</a></div>-->
                                <?php  if(0 == $_SESSION['u_business']){?>
                                <div> <a href="javascript:openNotePop();" style="margin-left: 10px" class="btn h30 btn_blu">쪽지</a></div>
                                <?php } ?>
                            </div>
                            <div class="search_form fr">
                                <!--                    총 --><?//=number_format($total_cnt)?><!--건-->
                            </div>
                        </div>
                    </form>
                    <p>※ 적중 : 파란색 / 낙첨 : 빨간색 / 적특 : 노란색 / 취소 : 회색</p>
                    <div class="tline">
                        <table class="mlist separate_table">
                            <tr>
                                <!-- 
                                <th width="5%"><input type="checkbox" name="checkAllService" class="checkAllService" onclick="checkAllNote()"></th>
                                -->
                                <th width="3%">
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                        <label for="checkbox_css_all"></label>
                                    </div>
                                </th>
                                <th width="6%">번호</th>
                                <th width="8%">아이디</th>
                                <th width="5%">닉네임</th>
                                <th width="5%">게임수</th>
                                <th width="20%">배팅진행내역</th>
                                <th width="5%">배당율(보너스)</th>
                                <th width="5%">배팅액</th>
                                <th width="6%">예상당첨액</th>
                                <th width="5%">적중금</th>
                                <th width="14%">배팅시간</th>
                                <th width="10%">베팅종류</th>
                                <th width="10%">결과</th>
                                <th width="10%">기능</th>
                            </tr>
                        </table>
                    </div>

<?php
$db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
$i = 0;
foreach ($db_dataArr as $key => $item) {
    $bgColor = '';
    $chkbox_css[$i] = "checkbox_css_" . $i;
    if ($item['is_monitor_bet'] == 'Y')
        $bgColor = '#F6BB43';
    
    // 클래식 색상
    if($item['is_classic'] == 'ON')
        $bgColor = '#d4f6d8';
    ?>
                        <div class="tline">
                            <table class="mlist separate_table">

                                <tr bgColor="<?= $bgColor ?>">
                                <input type="hidden" value="<?= $item['idx'] ?>">
                                <td width="3%">
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $item['idx'] ?>" data-member-idx="<?= $item['member_idx'] ?>"/>
                                        <label for="<?= $chkbox_css[$i] ?>"></label>
                                    </div>
                                </td>
                                <td width="6%"><?= $item['idx'] ?></td>
                                 <?php  if(0 == $_SESSION['u_business']){?>
                                <td width="8%">
                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $item['member_idx'] ?>', '7');"><?= $item['id'] ?></a> 
                                </td>
                                <td width="5%">
                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $item['member_idx'] ?>', '7');"><?= $item['nick_name'] ?></a>
                                </td>
                                 <?php }  else { ?>
                                <td width="8%">
                                    <a href="javascript:;" ><?= $item['id'] ?></a> 
                                </td>
                                <td width="5%">
                                    <a href="javascript:;" ><?= $item['nick_name'] ?></a>
                                </td>
                                 <?php } ?>
    <?php $idx = !empty($db_dataArrDetail[$item['idx']]) ? count($db_dataArrDetail[$item['idx']]) : 0; ?>
                                <td width="5%"><?= $idx ?></td>
                                <td width="20%" id="open_betting_detail_<?= $item['idx'] ?>" onClick="open_betting_detail(<?= $item['idx'] ?>,<?= $item['is_open'] ?>)">
                                <?php
                                $betViewCount = 0; //배팅진행내역 카운트
                                $totalBetPrice = 1; // 배당률
                                list($status, $status_color) = getBetResult($item['bet_status'], $item['total_bet_money'], $item['take_money'], $item['cancel_type']);
                                ?>
                                    <?php $status_arr = !empty($db_dataArrDetail[$item['idx']]) ? $db_dataArrDetail[$item['idx']] : [] ?>
                                    <?php foreach ($status_arr as $key => $value) { ?>
                                        <?php
                                        $totalBetPrice *= $value['bet_price'];
                                        if ($value['display_bet_status'] == 1) {
                                            $color = 'btn_while';
                                            // 경기가 연기되었으면 색상을 적특하고 같게한다.
                                            if ($value['fixture_status'] == 5) {
                                                $color = 'btn_yellow';
                                            }
                                        } else if ($value['display_bet_status'] == 2) {
                                            $color = 'btn_blu';
                                        } else if ($value['display_bet_status'] == 4) {
                                            $color = 'btn_red';
                                        } else if ($value['display_bet_status'] == 5) {
                                            $color = 'btn_gray';
                                        } else if ($value['display_bet_status'] == 6) {
                                            $color = 'btn_yellow';
                                        }
                                        ?>
                                        <?php
                                        if ($betViewCount >= 8) {
                                            $betViewCount = 0;
                                            echo '<br>';
                                        }
                                        ?>
                                        <?php $bet_name = !empty($value['bet_name']) ? $value['bet_name'] : '' ?>
                                        <?php $ls_markets_id = !empty($value['ls_markets_id']) ? $value['ls_markets_id'] : '' ?>
                                        <a class="btn h30 <?= !empty($color) ? $color : '' ?>" style="width: 25px; padding: 0px; margin: 0px; border: 1px solid black; color:black"><?php echo GameStatusUtil::betNameToDisplay_new($bet_name, $ls_markets_id) ?></a>
                                        <?php
                                        $betViewCount ++;
                                    }
                                    ?>
                                </td>

                                <td width="5%" style="text-align:center"><?= !empty($totalBetPrice) ? number_format($totalBetPrice, 2) : 0; ?>
    <?php if ($item['bonus_price'] > 0) echo '(' . $item['bonus_price'] . ')' ?>
                                </td>
                                <td width="5%" style="text-align:right"><?= !empty($item['total_bet_money']) ? number_format($item['total_bet_money']) : 0; ?></td>
                                    <?php
                                    $rating = round($totalBetPrice * $item['bonus_price'], 2) * $item['total_bet_money'];
                                    $take_money_color = 'color:blue;';
                                    if ($item['bet_status'] == 3 || $item['bet_status'] == 5)
                                        $take_money_color = 'color:red;';
                                    ?>
                                <td width="6%" style="text-align:right"><?= !empty($rating) ? number_format($rating) : 0; ?></td>
                                <td width="5%" style="text-align:right; <?= $take_money_color ?>"><?= number_format($item['take_money']) ?></td>
                                <td width="14%"><?= $item['create_dt'] ?></td>
  <!-- 배팅종류(클래식) 표시 --> <td width="10%"><?= $item['is_classic'] == 'OFF' ? '스포츠' : '클래식' ?></td>
                                <td width="10%" bgcolor="<?= $status_color ?>" id="open_betting_detail_<?= $item['idx'] ?>" onClick="open_betting_detail(<?= $item['idx'] ?>, <?= $item['is_open'] ?>)"><?= $status ?></td>
                                <?php  if(0 == $_SESSION['u_business']){?>
                                <td width="10%">
                                    <a href="javascript:onBetCancel(<?= $item['idx'] ?>,<?= $item['bet_status'] ?>)" class="btn h30 btn_blu">취소</a>
                                </td>
                                <?php } ?>
                                </tr>
                            </table>
                        </div>
                        <div id="betting_detail_<?= $item['idx'] ?>" class="tline" style="display: none;" >
    <?php //print_r($db_dataArrDetail[72]);    ?>
                            <table class="mlist separate_table">
                                <tr>
                                    <th>경기시간</th>
                                    <th>리그</th>
                                    <th>홈</th>
                                    <th>VS</th>
                                    <th>원정</th>
                                    <th>타입</th>
                                    <th>베팅</th>
                                    <th>게임결과</th>
                                    <th>결과변경</th>
                                </tr>
    <?php $result_row = !empty($db_dataArrDetail[$item['idx']]) ? $db_dataArrDetail[$item['idx']] : [] ?>
    <?php
    foreach ($result_row as $key => $item2) {
        $result_score = '-';
        if (isset($item2['result_score'])) {
            if ($item2['result_score'] != '') {
                $item2['result_score'] = stripslashes($item2['result_score']);
                $arrScore = explode('result_extra', $item2['result_score']);
                if (count($arrScore) == 1)
                    $score = substr($arrScore[0], 0, -1) . '}';
                else
                    $score = substr($arrScore[0], 0, -2) . '}';
                $json_result = json_decode($score, true);
                $result_score = $json_result['live_results_p1'] . ':' . $json_result['live_results_p2'];
                /* $json_result = json_decode ($item2['result_score'], true);
                  if($json_result['result_extra'] != '')
                  $result_score = $json_result['result_extra'];
                  else
                  $result_score = $json_result['live_results_p1'].':'.$json_result['live_results_p2']; */
            }
        }

        /* if ($item2['ls_market_id'] == 6 || $item2['ls_market_id'] == 9) {
          $betNameDisplay = $item2['bet_name'];
          } else {
          $betNameDisplay = GameStatusUtil::betNameToDisplay_new($item2['bet_name'], $item2['ls_market_id']);
          } */
        $betNameDisplay = $item2['ls_markets_name'];

        if (strlen($item2['ls_markets_base_line']) > 0) {
            $betNameDisplay = $betNameDisplay . '(' . explode(' ', $item2['ls_markets_base_line'])[0] . ')';
        }
        ?>

                                    <tr>
                                        <td><?= $item2['fixture_start_date'] ?></td>
                                        <td><?= $item2['fixture_league_name'] ?></td>
                                        <?php  if(0 == $_SESSION['u_business']){?>
                                        <td><a href="prematch_manager_detail.php?fixture_start_date=<?= urlencode($item2['fixture_start_date']) ?>&fixture_id=<?= $item2['ls_fixture_id'] ?>" target='_blank'><?= isset($item2['fixture_participants_1_name']) ? $item2['fixture_participants_1_name'] : $item2['p1_team_name'] ?></a></td>
                                        <td><?= $item2['bet_price'] ?></td>
                                        <td><a href="prematch_manager_detail.php?fixture_start_date=<?= urlencode($item2['fixture_start_date']) ?>&fixture_id=<?= $item2['ls_fixture_id'] ?>" target='_blank'><?= isset($item2['fixture_participants_2_name']) ? $item2['fixture_participants_2_name'] : $item2['p2_team_name'] ?></a></td>
                                        <td><?= $betNameDisplay ?></td>
                                        <?php } else {?>
                                        <td><a target='_blank'><?= isset($item2['fixture_participants_1_name']) ? $item2['fixture_participants_1_name'] : $item2['p1_team_name'] ?></a></td>
                                        <td><?= $item2['bet_price'] ?></td>
                                        <td><a target='_blank'><?= isset($item2['fixture_participants_2_name']) ? $item2['fixture_participants_2_name'] : $item2['p2_team_name'] ?></a></td>
                                        <td><?= $betNameDisplay ?></td>
                                        <?php } ?>
        <?php   
        $ls_market_id = array_key_exists('ls_market_id', $item2) ? $item2['ls_market_id'] : '-';
        $bet_name = array_key_exists('bet_name', $item2) ? $item2['bet_name'] : '-';
        $gamestate = !empty(GameStatusUtil::betNameToDisplay_new($bet_name, $ls_market_id)) ? GameStatusUtil::betNameToDisplay_new($bet_name, $ls_market_id) : '-';
        ?>
                                        <td><?= $gamestate ?>(<?= $item2['bet_price'] ?>)</td>
                                        <?php if ($item2['bet_status'] == 6) { ?>
                                            <td bgcolor="yellow">적특</td>
                                        <?php } else { ?>
                                            <td><?= $result_score ?></td>
                                        <?php } ?>
                                            
                                        <?php  if(0 == $_SESSION['u_business']){?>
                                         <td>
                                            <a href="#" class="btn h30 btn_blu" onClick="fn_set_game_result(<?= $item2['idx'] ?>, '<?= $result_score ?>')">결과변경</a>
                                        </td>
                                        <?php } ?>
                                    </tr>
    <?php } ?>
                            </table>
                        </div>
                                <?php $i++;
                            } ?>
                    <?php
                    include_once(_BASEPATH . '/common/page_num.php');
                    ?>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>
        <script type="text/javascript">
            let bet_idx = 0;
            $(document).ready(function () {
                $('#betting_key').val(<?= $p_data['betting_key'] ?>).prop("selected", true);
                $('#srch_key').val('<?= $p_data['srch_key'] ?>').prop("selected", true);

                $("#checkbox_css_all").change(function () {
                    if ($("#checkbox_css_all").is(':checked')) {
                        allCheckFunc(this);
                    } else {
                        $("[name=chk]").prop("checked", false);
                    }
                });

                $("[name=chk]").change(function () {
                    $("[name=checkbox_css_all]").prop("checked", false);
                });
            });

            function allCheckFunc(obj) {
                $("[name=chk]").prop("checked", $(obj).prop("checked"));
            }

            const goSearch = function () {
                var fm = document.search;
                const status = $('#srch_key option:selected').val();
                console.log(status);
                // console.log(fm.srch_s_date);
                //fm.vtype.value=vtype;

                //if((fm.srch_key.value!='') && (fm.srch_val.value=='')) {

                //if (fm.srch_level.value < 1) {
                //alert('검색어를 입력해 주세요.');
                //fm.srch_val.focus();
                //return;
                //}
                //}

                fm.method = "get";
                fm.submit();
            }

            const openNotePop = function () {

                const memberIdxList = [];
                $('input[name="chk"]:checked').each(function () {
                    memberIdxList.push($(this).data('member-idx'));
                });

                if (memberIdxList.length == 0) {
                    alert("항목이 선택되지 않았습니다. 쪽지 보낼 회원을 선택해주세요.");
                    return false;
                }

                popupWinPostList('/member_w/pop_msg_write_list.php', 'popmsg', 660, 1000, 'msg', memberIdxList);
            }

            const open_betting_detail = function (idx, is_open) {
                idx = +idx;
                let display = document.getElementById('betting_detail_' + idx).style.display;
                if (display == 'none') {
                    document.getElementById('betting_detail_' + idx).style.display = 'block';
                } else {
                    document.getElementById('betting_detail_' + idx).style.display = 'none';
                }

                if (!is_open) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_menu_10_update_is_open.php',
                        data: {idx: idx},
                        success: function (result) {
                            $('#open_betting_detail_' + idx).attr("onclick", "open_betting_detail(" + idx + ",1)");
                            $('#open_betting_detail_' + idx).attr("onclick", "open_betting_detail(" + idx + ",1)");
                        },
                        error: function (data, status, error) {
                            console.log(JSON.stringify(data));
                            console.log(`req : ` + data);
                            console.log(`status : ` + status);
                            console.log(`error: ` + error);
                            alert('상세내역이 없습니다');
                            return;
                        }
                    });
                }
            }

            // 전체취소
            const onBetCancel = function (idx, bet_status) {

                if (bet_status > 1) {
                    alert('취소할 수 없습니다.');
                    return;
                }

                var str_msg = '선택하신 베팅 취소하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_menu_bet_cancel_ajax.php',
                        data: {'idx': idx},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('적용하였습니다.');
                                window.location.reload();
                                return;
                            } else if (result['retCode'] == "-1") {
                                alert('적용할 데이터가 없습니다.');
                                return;
                            } else if (result['retCode'] == "-2") {
                                alert('파라미터가 잘못되었습니다.');
                                return;
                            } else {
                                alert('디비연결이 실패했습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                            return;
                        }
                    });
                }
            }

            const setDate = function (sdate, edate) {
                var fm = document.search;

                fm.srch_s_date.value = sdate;
                fm.srch_e_date.value = edate;
            }

            // 정산
            function fn_set_game_result(idx, result_score) {
                bet_idx = idx;
                let score = result_score.split(':');

                // 전부 닫기
                $('#result_score').attr('style', 'display: none');
                $('#result_score_extra').attr('style', 'display: none');

                $('#result_score').attr('style', 'display: table-row');
                $('#result_score_extra').attr('style', 'display: table-row');

                $('#set_game_result').attr('style', 'display: block');

                // 스코어
                $('#home_score_api').val(score[0]);
                $('#away_score_api').val(score[1]);
            }

            // 스코어 업데이트
            function fnUpdateScore() {
                let home_score = $('#home_score').val();
                let away_score = $('#away_score').val();

                /*if(home_score == 0 || away_score == 0){
                 alert('스코어를 입력해주세요.');
                 return;
                 }*/

                //console.log(bet_idx);
                //return;
                var result = confirm('수정 하시겠습니까?');
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_set_game_score.php',
                        data: {
                            'idx': bet_idx,
                            'home_score': home_score,
                            'away_score': away_score
                        },
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('수정하였습니다.');
                                //url = '/sports_w/prematch_betting_list.php?srch_key=' + game_type;
                                window.location.reload();
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            console.log(request);
                            alert('정산에 실패하였습니다.');
                            return;
                        }
                    });
                } else {
                    return;
                }
            }

            function fnPopupClose() {
                $('#set_game_result').attr('style', 'display: none');
            }

            $(document).ready(function () {
                $('#sport_list').on('change', function () {
                    let select_id = $('#sport_list').val();
                    $('.league_list').attr('style', 'display: none;');
                    $('.select_option_' + select_id).removeAttr('style');
                });

                $('.search_btn').on('click', function () {
                    let sport_id = $('#sport_list').val();
                    let league_id = $('#league_list').val();
                    let bet_status_id = $('#bet_status_list').val();
                    let team_name = $('#team_name').val();

                    alert(sport_id + ' ' + league_id + ' ' + bet_status_id + ' ' + team_name);

                    location.href = '/sports_w/prematch_manager.php?s_id=' + sport_id + '&l_id=' + league_id + '&bs_id=' + bet_status_id + '&tn=' + team_name;
                });
            })

        </script>
<?php
include_once(_BASEPATH . '/common/bottom.php');

function getBetResult($status, $total_bet_money, $take_money, $cancel_type) {
    $color = '#ffffff';
    $result = '결과전';

    if (1 == $status) {
        $result = '결과전';
        $color = '#ffffff';
    } else if (5 == $status) {
        if ($cancel_type == 1)
            $result = '관리자 취소';
        else
            $result = '취소';
        $color = '#c9c9c9';
    }else {
        if ($total_bet_money < $take_money) {
            $result = '적중';
            $color = '#9fd5e9';
        }
        if (0 == $take_money || (0 < $take_money && $total_bet_money > $take_money)) {
            $result = '낙첨';
            $color = '#f9bfbf';
        }
        if ($total_bet_money == $take_money) {
            $result = '적특';
            $color = '#ffefa5';
        }
    }

    return array($result, $color);
}
?>
    </body>
</html>
