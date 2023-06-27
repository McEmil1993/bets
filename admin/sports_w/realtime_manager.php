<?php
ini_set('memory_limit', '512M');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}

$MEMAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    $today = date("Y/m/d");
    $start_date = $today;

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $MEMAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }


    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $MEMAdminDAO->real_escape_string($_REQUEST['v_cnt']) : 20);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = 20;
    }

    $p_data['srch_s_date'] = !empty($_REQUEST['srch_s_date']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : date("Y/m/d");
    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : $start_date);

    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);


    $p_data_config['sql'] = "select u_level, set_type, set_type_val from t_game_config ";
    $p_data_config['sql'] .= " where set_type = 'inplay_status' ";
    $retData = $MEMAdminDAO->getQueryData($p_data_config);


    //$p_data['db_srch_s_date'] = $MEMAdminDAO->real_escape_string($p_data['db_srch_s_date']);


    $p_data["table_name"] = " lsports_fixtures ";
    $p_data["sql_where"] = "";
    $default_link = 'realtime_manager.php?srch_s_date=' . $p_data['db_srch_s_date'];
    $s_id = "";
    $l_id = "";
    $bs_id = 2;
    $fs_id = 2;
    $tn = "";
    $fix_id = "";

    if (isset($_GET['s_id'])) {

        $s_id = $MEMAdminDAO->real_escape_string($_GET['s_id']);
        if (strlen($s_id) > 0) {
            $default_link .= '&s_id=' . $s_id;
            $p_data["sql_where"] = " AND fix.`fixture_sport_id` = $s_id ";
        }
    }

    // 지역
    $location_id = 0;
    if (isset($_GET['location_id'])) {
        $location_id = $MEMAdminDAO->real_escape_string($_GET['location_id']);
        if (strlen($location_id) > 0) {
            $default_link .= '&location_id=' . $location_id;
            $p_data["sql_where"] = " AND fix.`fixture_location_id` = $location_id ";
        }
    }

    if (isset($_GET['fix_id'])) {
        $fix_id = $MEMAdminDAO->real_escape_string($_GET['fix_id']);
        if (strlen($fix_id) > 0) {
            $default_link .= '&fix_id=' . $fix_id;
            $p_data["sql_where"] = " AND fix.fixture_id LIKE " . '"%' . $fix_id . '%"';
        }
    }

    if (isset($_REQUEST['l_id'])) {
        $l_id = $MEMAdminDAO->real_escape_string($_REQUEST['l_id']);
        if (strlen($l_id) > 0) {
            $default_link .= '&l_id=' . $l_id;
            $p_data["sql_where"] .= " AND fix.fixture_league_id = " . $l_id;
        }
    }
    if (isset($_REQUEST['bs_id'])) {
        $bs_id = $MEMAdminDAO->real_escape_string($_REQUEST['bs_id']);
        if (strlen($bs_id) > 0) {
            $default_link .= '&bs_id=' . $bs_id;

            $p_data["sql_where"] .= " AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = " . $bs_id;
        }
    } else {
        $default_link .= '&bs_id=' . $bs_id;
        $p_data["sql_where"] .= " AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 2";
    }
    if (isset($_REQUEST['tn'])) {
        $tn = $MEMAdminDAO->real_escape_string($_REQUEST['tn']);
        if (strlen($tn) > 0) {
            $default_link .= '&tn=' . $tn;
            $p_data["sql_where"] .= " AND (p1.display_name = '$tn' or p2.display_name = '$tn'  )";
        }
    }

    //$startTime = date("Y-m-d H:i:s", strtotime($p_data['db_srch_s_date'] . "-3"." days"));
    $startTime = date("Y-m-d H:i:s", strtotime($p_data['db_srch_s_date'] . "-1" . " days"));

    //$endTime = date("Y-m-d 23:59:59", strtotime($p_data['db_srch_s_date']));
    $endTime = date("Y-m-d H:i:s", strtotime($p_data['db_srch_s_date'] . "+1" . " days"));
    // 실시간 목록
    $total_cnt = $MEMAdminDAO->getSportsFixturesCount(2, $p_data, $startTime, $endTime);


    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    $db_dataArr = [];
    if ($total_cnt > 0) {
        $db_dataArr = $MEMAdminDAO->getSportsFixturesList(2, $p_data, $startTime, $endTime, $bs_id);
    }

    $sp = $MEMAdminDAO->getSportsList(2);
    $s = $MEMAdminDAO->getLeaguesList($s_id, 2);
    $MEMAdminDAO->dbclose();
}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
    <!--<![endif]-->

    <?php
    include_once(_BASEPATH . '/common/head.php');
    ?>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/sports_list.js"></script>
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
            });

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
                let srch_s_date = $('#datepicker-default').val();
                let fix_id = $('#fix_id').val();


                location.href = '/sports_w/realtime_manager.php?s_id=' + sport_id + '&l_id=' + league_id + '&bs_id=' + bet_status_id + '&tn=' + team_name + '&fix_id=' + fix_id + '&srch_s_date=' + srch_s_date;
            });
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
    </script>
    <body>
        <input type="hidden" id="page" value="<?= $p_data['page'] ?>">
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "realtime_manager";
            include_once(_BASEPATH . '/common/left_menu.php');
            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>실시간 관리</h4>
                    </a>
                </div>
                <!-- detail search -->
                <div class="panel search_box">
                    <h5><a href="/sports_w/realtime_manager.php"><b>실시간(자동)</b></a></h5>
                    <h5><a href="/sports_w/realtime_manager_passivity.php">실시간(수동)</a></h5>
                </div>
                <!-- END detail search -->
                <!-- list -->
                <div class="panel reserve">

                    <div class="panel_tit">
                        <div class="search_form fl">
                            <div class="daterange">
                                <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                                <input type="text" class="" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?= $p_data['srch_s_date'] ?>"/>                                
                            </div>
                            &nbsp;&nbsp;

                            <div class="" style="padding-right: 5px;"><label >종목</label> </div>
                            <div class="" style="padding-right: 5px;">
                                <select name="sport_list" id="sport_list" onchange="fn_On_chnage('realtime_manager.php');">>
                                    <option value="">전체</option>
                                    <?php foreach ($sp as $key => $item) { ?>
                                        <option value="<?= $item['id'] ?>"   <?php if ($s_id == $item['id']): ?> selected<?php endif; ?>><?= $item['name'] ?></option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="" style="padding-right: 5px;"><label >지역</label> </div>
                            <div class="" style="padding-right: 5px;">
                                <select name="location_list" id="location_list" onchange="fn_On_chnage('realtime_manager.php');">
                                    <option value="">전체</option>
                                    <?php $checkLocation = array(); // 중복출력 체크용도    ?>
                                    <?php
                                    foreach ($db_dataArr as $key => $item) {
                                        if (!in_array($item['fixture_location_id'], $checkLocation)) {
                                            ?>
                                            <option value="<?= $item['fixture_location_id'] ?>"   <?php if ($location_id == $item['fixture_location_id']): ?> selected<?php endif; ?> data-image="../assets_admin/images/flag/<?= $item['fixture_location_id'] ?>.png"><?= $item['location_name'] ?></option>
                                            <?php
                                            array_push($checkLocation, $item['fixture_location_id']);
                                        }
                                        ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="" style="padding-right: 5px;"><label >리그</label> </div>
                            <div class="" style="padding-right: 5px;">
                                <select name="league_list" id="league_list" class="league_list_select" onchange="fn_On_chnage('realtime_manager.php');">
                                    <option value="">전체</option>
                                    <?php $checkLeague = array(); // 중복출력 체크용도   ?>
                                    <?php
                                    foreach ($db_dataArr as $key => $item) {
                                        if (!in_array($item['fixture_league_id'], $checkLocation)) {
                                            ?>
                                            <option class="league_list select_option_<?= $item['fixture_sport_id'] ?>"
                                                    style="display: block;"
                                                    value="<?= $item['fixture_league_id'] ?>"
                                                    <?php if ($l_id == $item['fixture_league_id']): ?> selected<?php endif; ?>>
                                                    <?= $item['league_display_name'] ?>
                                            </option>
                                            <?php
                                            array_push($checkLocation, $item['fixture_league_id']);
                                        }
                                        ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="" style="padding-right: 5px;"><label >상태</label> </div>
                            <div class="" style="padding-right: 5px;">
                                <select name="bet_status_list" id="bet_status_list" onchange="fn_On_chnage('realtime_manager.php');">
                                    <option value="1" <?php if ($bs_id == 1): ?> selected<?php endif; ?>>배팅전</option>
                                    <option value="2" <?php if ($bs_id == 2): ?> selected<?php endif; ?>>배팅가능</option>
                                    <option value="3" <?php if ($bs_id == 3): ?> selected<?php endif; ?>>종료</option>
                                    <option value="4" <?php if ($bs_id == 4): ?> selected<?php endif; ?>>대기</option>
                                </select>    
                            </div>
                            <div class="">
                                <input id="fix_id" type="text" class=""  placeholder="경기 ID를 입력하세요"  value="<?= $fix_id ?>"/>
                            </div>
                            <div class="">
                                <input id="team_name" type="text" class=""  placeholder="팀명을 입력하세요"  value="<?= $tn ?>"/>
                            </div>
                            <div><a href="#" class="btn h30 btn_red search_btn">검색</a></div>
                        </div>
                        <div class="search_form fr">
                            <a href="#" class="btn h30 btn_dblu mg_l-10" onClick="fn_all_update_passivity_manager(2, 'ON', 'realtime_manager.php')">수동 관리</a>
                        </div>
                    </div>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                        <label for="checkbox_css_all"></label>
                                    </div>
                                </th>
                                <th>경기ID</th>
                                <th>경기일시</th>
                                <th>종목</th>
                                <th>리그</th>
                                <th>홈팀</th>
                                <th>원정팀</th>
                                <th>스코어</th>
                                <th>총배팅액</th>
                                <th>남은금액</th>
                                <th>배팅내역</th>
                                <th>상태</th>
                                <th>배팅</th>
                            </tr>
                            <tbody id="fixtures_tbody">
                                <?php
                                if ($total_cnt > 0) {
                                    $i = 0;
                                    if (!empty($db_dataArr)) {
                                        foreach ($db_dataArr as $row) {
                                            $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                                            $db_m_idx = $row['fixture_id'];
                                            $chkbox_css[$i] = "checkbox_css_" . $i;

                                            $base_url = "/sports_w/realtime_betting_list.php?";
                                            $fixture_id = $row['fixture_id'];
                                            $popupUrl = $base_url . "fixture_id=" . $fixture_id;

                                            //CommonUtil::logWrite("[menu_1_1] popupUrl : ". $popupUrl, "error");
                                            ?>
                                            <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                                <td>
                                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                                        <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $row['fixture_id'] ?>" />
                                                        <label for="<?= $chkbox_css[$i] ?>"></label>
                                                    </div>
                                                </td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= $row['fixture_id'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= $row['fixture_start_date'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= $row['sports_display_name'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= $row['league_display_name'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= !empty($row['p1_display_name']) ? $row['p1_display_name'] : $row['p1_team_name'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= !empty($row['p2_display_name']) ? $row['p2_display_name'] : $row['p2_team_name'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')"><?= $row['live_results_p1'] ?> : <?= $row['live_results_p2'] ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')" style='text-align:right'><?= ($row['total_bet_money'] == null) ? 0 : number_format($row['total_bet_money']) ?></td>
                                                <td onclick="fn_On_detail('/sports_w/realtime_manager_detail.php?fixture_start_date=<?= urlencode($row['fixture_start_date']) ?>&fixture_id=<?= $row['fixture_id'] ?>')" style='text-align:right'><?= ($row['bet_id_total_bet_money'] == null) ? 0 : number_format($row['bet_id_total_bet_money']) ?></td>
                                                <td style='text-align:center'> 
                                                    <a href="javascript:;" onClick="popupWinPost('<?= $popupUrl ?>', 'userbetinfo', 800, 1400, 'userbetinfo', 0);">바로가기</a>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_name = GameStatusUtil::get_display_stauts_name($row['display_status'], 2);
                                                    ?>
                                                    <?= $status_name ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['admin_bet_status'] == 'ON') { ?>
                                                        <a href="#" onclick="betOnOffBtnClick(this,<?= $row['fixture_id'] ?>, 'off', '<?= $row['fixture_start_date'] ?>', 2)" class="btn h30 btn_green"> <?= $row['admin_bet_status'] ?></a>
                                                    <?php } else { ?>
                                                        <a href="#" onclick="betOnOffBtnClick(this,<?= $row['fixture_id'] ?>, 'on', '<?= $row['fixture_start_date'] ?>', 2)" class="btn h30 btn_gray"> <?= $row['admin_bet_status'] ?></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                                    <?php
                                                    $i++;
                                                }
                                            }
                                        } else {
                                            
                                        }
                                        ?>
                            </tbody>
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
    </body>
</html>
