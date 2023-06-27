<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
include_once(_BASEPATH . '/common/login_check.php');
include_once(_LIBPATH . '/class_Code.php');

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 20);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 20;
}

$previous_page = isset($_GET['previous_page']) ? $_GET['previous_page'] : '';
$previous_srch_date = isset($_GET['previous_srch_date']) ? $_GET['previous_srch_date'] : '';
$previous_sport_id = isset($_GET['previous_sport_id']) ? $_GET['previous_sport_id'] : '';
$previous_location_id = isset($_GET['previous_location_id']) ? $_GET['previous_location_id'] : '';
$previous_league_id = isset($_GET['previous_league_id']) ? $_GET['previous_league_id'] : '';
$previous_bet_status_id = isset($_GET['previous_bet_status_id']) ? $_GET['previous_bet_status_id'] : '';
$previous_fix_id = isset($_GET['previous_fix_id']) ? $_GET['previous_fix_id'] : '';
$previous_team_name = isset($_GET['previous_team_name']) ? $_GET['previous_team_name'] : '';
$previous_fix_status = isset($_GET['previous_fix_status']) ? $_GET['previous_fix_status'] : '';

$LSBAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSBAdminDAO->dbconnect();

if ($db_conn) {
  
    $fixture_id = $_GET['fixture_id'];
    $fixture_start_date = $_GET['fixture_start_date'];

    // 해당경기 베팅정보를 가져온다.
    $p_data["table_name"] = " lsports_bet ";
    $s = $LSBAdminDAO->getDetailSportsFixturesList($fixture_id, 2, $p_data);

    $row_data = array('fixture_participants_1_name' => '','fixture_participants_2_name'=>''
        ,'fixture_start_date'=>'','fixture_sport_name'=>'','fixture_league_name'=>'','display_status'=>0
        ,'fixture_sport_id'=>0,'fixture_id'=>0);
    foreach ($s as $row) {
        $row_data = $row;
        break;
    }

    $result_sum_cnt = $LSBAdminDAO->getTotalBetSumCNT($fixture_id, 2);
    $betCnt = $result_sum_cnt[0]['CNT'];
    $betMoney = $result_sum_cnt[0]['SUM_MONEY'];
    $LSBAdminDAO->dbclose();
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
    <script src="<?= _STATIC_COMMON_PATH ?>/js/sports.js"></script>
    <body>
        <input type="hidden" id="previous_page" value="<?= $previous_page ?>">
        <input type="hidden" id="previous_srch_date" value="<?= $previous_srch_date ?>">
        <input type="hidden" id="previous_sport_id" value="<?= $previous_sport_id ?>">
        <input type="hidden" id="previous_location_id" value="<?= $previous_location_id ?>">
        <input type="hidden" id="previous_league_id" value="<?= $previous_league_id ?>">
        <input type="hidden" id="previous_bet_status_id" value="<?= $previous_bet_status_id ?>">
        <input type="hidden" id="previous_fix_id" value="<?= $previous_fix_id ?>">
        <input type="hidden" id="previous_team_name" value="<?= $previous_team_name ?>">
        <input type="hidden" id="previous_fix_status" value="<?= $previous_fix_status ?>">
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <!-- <body> -->
        <div class="wrap">
            <?php
            $menu_name = "realtime_manager_passivity";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <?php $bet_base_line = explode(' ', $row_data['bet_base_line'])[0]; ?>
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>실시간 (수동) 관리 > 상세</h4>
                    </a>
                </div>

                <!-- detail search -->
                <div class="panel search_box">
                            <h5 class="pt10 pb10"><b><?= $row_data['fixture_participants_1_name'] ?></b> vs <b><?= $row_data['fixture_participants_2_name'] ?></b> (경기ID : <?= $fixture_id ?>)</h5>
                            <div style="float:right"><a href="#" onclick="fn_go_sprots_menu2_1();" class="btn h30 btn_blu">목록</a></div><!-- href="realtime_manager_passivity.php"  -->
                    <div class="mline">
                        <table class="mlist">
                            <tr>
                                <th width="10%">경기일시</th>
                                <td class="input__btn"><input id="game_start_date" name ="game_start_date" type="text" value="<?= $row_data['fixture_start_date'] ?>"> <a href="#" class="btn h30 btn_blu ml5" onclick="on_modify_game_start_date(<?= $fixture_id ?>, 1)">적용</a> <a href="#" class="btn h30 btn_dark" onclick="on_modify_game_start_date_release(<?= $fixture_id ?>, 1)">해제</a></td>
                                <th width="10%">종목</th>
                                <td><input type="text" value="<?= $row_data['fixture_sport_name'] ?>"readonly disabled></td>
                                <th width="10%">리그</th>
                                <td width="25%"><input type="text" value="<?= $row_data['fixture_league_name'] ?>"readonly disabled></td>
                            </tr>
                            <tr>
                                <th>배팅수</th>
                                <td><input type="text" value="<?= number_format($betCnt) ?>"readonly disabled></td>
                                <th>상태</th>
                                <td>
                                    <select name="srch_status" id="srch_status" style="float:left;">
                                        <option value="1" <?php if ($row_data['display_status'] == 1): ?> selected<?php endif; ?>>배팅가능</option>
                                        <option value="2" <?php if ($row_data['display_status'] == 2): ?> selected<?php endif; ?>>배팅마감</option>
                                        <option value="3" <?php if ($row_data['display_status'] == 3): ?> selected<?php endif; ?>>종료</option>
                                        <option value="4" <?php if ($row_data['display_status'] == 4): ?> selected<?php endif; ?>>대기</option>
                                    </select>
                                    <a href="#" class="btn h30 btn_blu" onclick="on_modify_game_status(<?= $fixture_id ?>, 1)">적용</a> 
                                    <a href="#" class="btn h30 btn_dark" onclick="on_modify_game_status_release(<?= $fixture_id ?>, 1)">해제</a>
                                </td>
                                <!-- <td><input type="text" value="<?= GameStatusUtil::get_display_stauts_name($row_data['display_status'], 1) ?>"></td> -->
                                <th>총 배팅액</th>
                                <td><input type="text" value="<?= number_format($betMoney) ?>"readonly disabled></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- END detail search -->

                <!-- list -->
                <div class="panel reserve">
                    <div class="panel_tit">
                        <div class="search_form fl ml5">
                            <?php
                            $current_day = date("d", strtotime(($row_data['fixture_start_date'])));
                            ?>

                            <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                <label for="checkbox_css_all"></label>
                            </div>
                            <b class="ml5 mr20">전체</b>

                            <b class="mr5">경기결과</b>
                            <div><input style="width:100px; text-align:center;" id="markets_name" name = "markets_name" type="text" class="" placeholder="마켓타입 입력" value=""></div> 
                            <div class="mr5">
                                <input style="width:100px; text-align:center;" id="live_results_p1" name ="live_results_p1" type="text" class="" value="0">
                            </div>
                            VS
                            <div class="ml5">
                                <input style="width:100px; text-align:center;" id="live_results_p2" name ="live_results_p2" type="text" class="" value="0">
                            </div>
                                                  
                            <div><a href="#" class="btn h30 btn_blu" onclick="onBtnClickBatchApplication(<?= $row_data['fixture_sport_id'] ?>, '<?= $row_data['fixture_start_date'] ?>',<?= $row_data['fixture_id'] ?>, 2)">일괄적용</a></div>
                            
                            <b class="ml20">2차 암호</b><input id="second_pass" name = "second_pass" type="password" class="ml5" placeholder="암호를 입력하세요." value=""/>
                            <div><a href="#" class="btn h30 btn_orange ml5" onclick="onBtnClickTotalHitException(<?= $row_data['fixture_sport_id'] ?>, '<?= $row_data['fixture_start_date'] ?>',<?= $row_data['fixture_id'] ?>, 2)">전체적특</a></div>
                            <div><a href="#" class="btn h30 btn_blu" onclick="onBtnClickTotalCalculate(<?= $row_data['fixture_sport_id'] ?>, '<?= $row_data['fixture_start_date'] ?>',<?= $row_data['fixture_id'] ?>, 2)">전체정산</a></div>
                            <div class="ml5 fr"><a href="#" class="btn h30 btn_dblu" onclick="onBtnClickTotalBeforeCalculate(<?= $row_data['fixture_id'] ?>, 2)">전체마감전</a></div>
                            
                            

                            <!-- 마켓타입 드롭메뉴 추가 lsports_market에서 데이터를 가져온다.-->

                            <!-- <div class="ml30">
                                <select name="srch_code" id="srch_code">
                                    <option value="1">승무패</option>
                                    <option value="3">핸디캡</option>
                                    <option value="">코너-핸디캡</option>
                                    <option value="2">오버언더</option>
                                    <option value="">홈팀 오버언더</option>
                                    <option value="">원정팀 오버언더</option>
                                    <option value="">승무패 및 오버언더</option>
                                    <option value="">첫 득점팀</option>
                                    <option value="">전반전+풀타임</option>
                                    <option value="7">더블찬스</option>
                                    <option value="">정확한스코어</option>
                                    <option value="">양팀 모두득점</option>
                                </select>
                            </div>
                            <div class="ml5"><input id="markets_baseline" name = "markets_baseline" type="text" class="" placeholder="기준점 입력" value="" style="width: 120px"></div>
                            <div class="ml5"><a href="#" class="btn h30 btn_blu" onclick="">마켓 추가</a></div> -->
                        </div>


                        <!-- 전체 수동적용 및 해제-->
                        <div class="search_form fr">
                            <div><a href="#" class="btn h30 btn_blu"   onclick="onClickPassivityAllApply(<?= $fixture_id ?>, 1,<?= $max_count ?>)">전체적용</a></div>
                            <div><a href="#" class="btn h30 btn_dark" onclick="onClickPassivityAllRelease(<?= $fixture_id ?>, 1,<?= $max_count ?>)">전체해제</a></div>
                        </div>                       

                    </div>

                    <?php
                    $idx = 0;
                    foreach ($s as $key => $item):
                        $betData = $item['bet_data'];
                        ++$idx;
                        $bet_line = true === isset($item['bet_line']) && false === empty($item['bet_line']) ? $item['bet_line'] : null;
                        $arr_bet_base_line = explode(" ", $item['bet_base_line']);
                        ?>
                        <div class="tline">
                            <table class="mlist" style="border-color: #ededed">
                                <tr style="border: 1px solid #dfdfdf;">

                                    <!-- 수동적용시 아래 스타일 사용
                                   <th colspan="3" style="background-color: #ebebff">
                                   수동적용시 아래 스타일 사용 -->
                                    <?php if ((true === isset($item['bet_status_passivity']) && false === empty($item['bet_status_passivity'])) ||
                                            (true === isset($item['bet_price_passivity']) && false === empty($item['bet_price_passivity']) )) {
                                        ?>
                                        <th colspan="3" style="background-color: #ebebff">
                                        <?php } else { ?>
                                        <th colspan="3">
    <?php } ?>
                                        <div class="search_form fl">
                                            <div class="checkbox checkbox-css checkbox-inverse" style="float:left; display:inline-block; text-align:center; width:20px; height:20px; margin-right:10px;" >
                                                <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $idx . '^' . $j_bet_data . '^' . $item['markets_id'] . '^' . $item['bet_base_line'] ?>" />
                                                <label for="<?= $chkbox_css[$i] ?>"></label>
                                            </div>
                                            
                                             <?php if (3 != $item['markets_id']) { ?>
                                                <h6><b><?= $item['markets_name_base_line'] . ' ' . $item['providers_name'] ?></b></h6>
                                            <?php } else { ?>
                                                <h6><b><?= $item['markets_name_base_line'] . ' ' . $bet_line . ' ' . $item['providers_name'] ?></b></h6>
<?php } ?>

                                        <!-- 최소/최대 배당 추가 -->

                                            <div class="ml30 mr5" style="display:flex">최소배당<input class="ml5" style="width:60px; text-align:center;" type="text" value="<?=$item['limit_bet_price']?>" readonly disabled></div>
                                            <div class="ml10 mr5"style="display:flex">최대배당<input class="ml5" style="width:60px; text-align:center;" type="text" value="<?=$item['max_bet_price']?>" readonly disabled></div>
                                        </div>

                                        <?php
                                        $bet_id_total_bet_money = 0;
                                        $total_bet_money = 0;

                                        foreach ($betData as $key => $value) {

                                            $bet_id_total_bet_money = $bet_id_total_bet_money + $value['bet_id_total_bet_money'];
                                            $total_bet_money = $total_bet_money + $value['total_bet_money'];
                                        }

                                        // 아직 일괄적용          

                                        $style = "font-weight: bold; color: black;";
                                        if (0 < $total_bet_money && (true === isset($item['bet_result_2_p1']) || true === isset($item['bet_result_2_p2']))) {

                                            if (0 == $bet_id_total_bet_money) { // 정산 완료
                                                $style = "font-weight: bold; color: blue;";
                                            } else { // 정산 미완료 
                                                $style = "font-weight: bold; color: red;";
                                            }
                                        }

                                        //CommonUtil::logWrite("[style] : " . $style, "info");
                                        //CommonUtil::logWrite("[total_bet_money] : " . $total_bet_money . ' bet_id_total_bet_money : ' . $bet_id_total_bet_money, "info");
                                        //CommonUtil::logWrite("[bet_result_2_p1] : " . $item['bet_result_2_p1'] . ' bet_result_2_p2 : ' . $item['bet_result_2_p2'], "info");
                                        ?>
    <?php if (4 == $item['markets_id'] || 390 == $item['markets_id'] || 84 == $item['markets_id']) { ?>
                                            <div class="search_form fr">

                                                <b>전반전  &nbsp;</b> 
                                                <div class="mr5">
                                                    <input style="width:100px; text-align:center;" id="bet_result_p1_<?= $idx ?>" name ="bet_result_p1_<?= $idx ?>"  type="text" class="" value="<?= $item['bet_result_p1'] ?>" />
                                                </div>
                                                VS
                                                <div class="ml5">
                                                    <input style="width:100px; text-align:center;" id="bet_result_p2_<?= $idx ?>" name ="bet_result_p2_<?= $idx ?>"  type="text" class="" value="<?= $item['bet_result_p2'] ?>" />
                                                </div> &nbsp; &nbsp;
                                                <?php
                                                if (84 != $item['markets_id']) {
                                                    echo "<b>풀타임  &nbsp;</b>";
                                                } else {
                                                    echo "<b>후반전  &nbsp;</b>";
                                                }
                                                ?>
                                                <div class="mr5">
                                                    <input style="width:100px; text-align:center;" id="bet_result_2_p1_<?= $idx ?>" name ="bet_result_2_p1_<?= $idx ?>" type="text"  style = "<?= $style ?>" class="" value="<?= $item['bet_result_2_p1'] ?>" />
                                                </div>
                                                VS
                                                <div class="ml5">
                                                    <input style="width:100px; text-align:center;" id="bet_result_2_p2_<?= $idx ?>" name ="bet_result_2_p2_<?= $idx ?>"  type="text" style = "<?= $style ?>" class="" value="<?= $item['bet_result_2_p2'] ?>" />
                                                </div>  &nbsp; 
                                                <div>
                                                    <a href="#" class="btn h30 btn_orange" onclick="onBtnClickIndividualHitException('<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">적특</a>
                                                    <a href="#" class="btn h30 btn_blu" onclick="onBtnClickCalculate(<?= $idx ?>, '<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">정산</a>
                                                    <?php if ($item['admin_bet_status'] == 'ON') { ?>
                                                        <a href="#" onclick="betOnOffBtnClick('<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', 'off')" class="btn h30 btn_green"> <?= $item['admin_bet_status'] ?></a>
        <?php } else { ?>

                                                        <a href="#" onclick="betOnOffBtnClick('<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', 'on')" class="btn h25 btn_gray"> <?= $item['admin_bet_status'] ?></a>
        <?php } ?>
                                                    <a href="#" class="btn h30 btn_dblu" onclick="onBtnClickBeforeCalculate(<?= $fixture_id ?>, 2,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">마감전</a>
                                                </div>

                                                <!-- 배당적용 버튼 추가 -->
                                                <div><a href="#" class="btn h30 btn_blu ml50" onclick="onClickAplyMnlDvdn(
                                                    <?= $idx ?>
                                                        , '<?= $j_bet_data ?>'
                                                                ,<?= $fixture_id ?>
                                                        , 1
                                                                ,<?= $item['markets_id'] ?>
                                                        , '<?= $item['bet_base_line'] ?>')">적용</a></div>
                                                <div><a href="#" class="btn h30 btn_dark" onclick="onClickPassivityRelease(
                                                    <?= $fixture_id ?>
                                                        , 1
                                                                ,<?= $item['markets_id'] ?>
                                                        , '<?= $item['bet_base_line'] ?>')">해제</a></div>
                                            </div>
                                            <?php
                                        } else {
                                            $current_day = date("d", strtotime($row_data['fixture_start_date']));
                                            ?>

                                            <div class="search_form fr">
                                                <div class="mr5" >
                                                    <input style="width:100px; text-align:center;" id="bet_result_p1_<?= $idx ?>" name ="bet_result_p1_<?= $idx ?>" style = "<?= $style ?>" type="text" class="" value="<?= $item['bet_result_p1'] ?>" />
                                                </div>
                                                VS
                                                <div class="ml5" >
                                                    <input  style="width:100px; text-align:center;" id="bet_result_p2_<?= $idx ?>" name ="bet_result_p2_<?= $idx ?>" style = "<?= $style ?>" type="text" class="" value="<?= $item['bet_result_p2'] ?>" />
                                                </div>  &nbsp; 
                                                <div>
                                                    <a href="#" class="btn h30 btn_orange" onclick="onBtnClickIndividualHitException('<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">적특</a>
                                                    <a href="#" class="btn h30 btn_blu" onclick="onBtnClickCalculate(<?= $idx ?>, '<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">정산</a>
                                                    <?php if ($item['admin_bet_status'] == 'ON') { ?>
                                                        <a href="#" onclick="betOnOffBtnClick('<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', 'off')" class="btn h30 btn_green"> <?= $item['admin_bet_status'] ?></a>
        <?php } else { ?>

                                                        <a href="#" onclick="betOnOffBtnClick('<?= $row_data['fixture_start_date'] ?>',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', 'on')" class="btn h30 btn_gray"> <?= $item['admin_bet_status'] ?></a>
        <?php } ?>
                                                        <a href="#" class="btn h30 btn_dblu" onclick="onBtnClickBeforeCalculate(<?= $fixture_id ?>, 2,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">마감전</a>
                                                </div>

                                                <!-- 배당적용 버튼 추가 -->
                                                <div><a href="#" class="btn h30 btn_blu ml50" onclick="onClickAplyMnlDvdn(
                                                    <?= $idx ?>
                                                        , '<?= $j_bet_data ?>'
                                                                ,<?= $fixture_id ?>
                                                        , 1
                                                                ,<?= $item['markets_id'] ?>
                                                        , '<?= $item['bet_base_line'] ?>')">적용</a></div>
                                                <div><a href="#" class="btn h30 btn_dark" onclick="onClickPassivityRelease(
                                                        <?= $fixture_id ?>
                                                        , 1
                                                                ,<?= $item['markets_id'] ?>
                                                        , '<?= $item['bet_base_line'] ?>')">해제</a></div>
                                            </div> 
    <?php } ?>
                                    </th>
                                </tr>
                                <tr>
                                    <?php
                                    $arr_win_draw_lose = array(1, 41, 42, 43, 44, 49, 282, 348);
                                    $arr_win_lose = array(3, 52, 53, 63, 64, 65, 66, 67, 95, 202, 203, 204, 226, 235, 281, 342, 639, 669, 866, 1170, 1558);
                                    $arr_win_losw_no_goal = array(16, 19);
                                    $arr_yes_no = array(17, 22, 23, 34, 35, 69, 96, 97, 98, 99);
                                    $arr_odd_even = array(5, 51);
                                    $arr_over_under = array(2, 11, 21, 28, 30, 31, 45, 46, 47, 48, 77, 101, 102, 153, 155, 214, 220, 221, 236, 352, 353);
                                    $arr_over_exactly_under = array(322);
                                    if (true === in_array($item['markets_id'], $arr_over_under)) {
                                        // over
                                        $count = 0;
                                        $over_value = $betData['Over'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $over_value, $fixture_id, $fixture_start_date, $count);

                                        // under
                                        $under_value = $betData['Under'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $under_value, $fixture_id, $fixture_start_date, $count);
                                    } else if (true === in_array($item['markets_id'], $arr_over_exactly_under)) {
                                        // over
                                        $count = 0;
                                        $over_value = $betData['Over'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $over_value, $fixture_id, $fixture_start_date, $count);

                                        // Exactly
                                        $exactly_value = $betData['Exactly'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $exactly_value, $fixture_id, $fixture_start_date, $count);

                                        // under
                                        $under_value = $betData['Under'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $under_value, $fixture_id, $fixture_start_date, $count);
                                    } else if (true === in_array($item['markets_id'], $arr_win_draw_lose)) {
                                        // 승
                                        $count = 0;
                                        $win_value = $betData['1'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $win_value, $fixture_id, $fixture_start_date, $count);

                                        // 무
                                        $draw_value = $betData['X'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $draw_value, $fixture_id, $fixture_start_date, $count);
                                        // 패
                                        $lose_value = $betData['2'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $lose_value, $fixture_id, $fixture_start_date, $count);
                                    } else if (true === in_array($item['markets_id'], $arr_win_lose) || true === in_array($item['markets_id'], $arr_win_losw_no_goal)) {
                                        // 승
                                        $count = 0;
                                        $win_value = $betData['1'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $win_value, $fixture_id, $fixture_start_date, $count);

                                        $no_goal_value = true === isset($betData['No Goal']) ? $betData['No Goal'] : '';
                                        if ('' != $no_goal_value) {
                                            $count = GameCode::display_bet_name('ON', $idx, $item, $no_goal_value, $fixture_id, $fixture_start_date, $count);
                                        }

                                        $lose_value = $betData['2'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $lose_value, $fixture_id, $fixture_start_date, $count);
                                    } else if (true === in_array($item['markets_id'], $arr_yes_no)) {
                                        // Yes
                                        $count = 0;
                                        $yes_value = $betData['Yes'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $yes_value, $fixture_id, $fixture_start_date, $count);

                                        // No
                                        $no_value = $betData['No'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $no_value, $fixture_id, $fixture_start_date, $count);
                                    } else if (true === in_array($item['markets_id'], $arr_odd_even)) {
                                        // Odd
                                        $count = 0;
                                        $odd_value = $betData['Odd'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $odd_value, $fixture_id, $fixture_start_date, $count);

                                        // Even
                                        $even_value = $betData['Even'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);
                                    } else if (70 == $item['markets_id']) {
                                        // Odd
                                        $count = 0;
                                        $odd_value = $betData['1st Period'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $odd_value, $fixture_id, $fixture_start_date, $count);

                                        // Even
                                        $even_value = $betData['2nd Period'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);

                                        $even_value = $betData['3rd Period'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);

                                        $even_value = $betData['4th Period'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);

                                        if (true === isset($betData['All Periods The Same'])) {
                                            $even_value = $betData['All Periods The Same'];
                                            $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);
                                        }
                                    } else if (71 == $item['markets_id']) {
                                        // Odd
                                        $count = 0;
                                        $odd_value = $betData['1st Half'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $odd_value, $fixture_id, $fixture_start_date, $count);

                                        // Even
                                        $even_value = $betData['2nd Half'];
                                        $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);

                                        if (true === isset($betData['All Periods The Same'])) {
                                            $even_value = $betData['All Periods The Same'];
                                            $count = GameCode::display_bet_name('ON', $idx, $item, $even_value, $fixture_id, $fixture_start_date, $count);
                                        }
                                    } else {
                                        $count = 0;
                                        foreach ($betData as $key => $value) {

                                            $count = GameCode::display_bet_name('ON', $idx, $item, $value, $fixture_id, $fixture_start_date, $count);
                                        }
                                    }
                                    ?>
                                </tr>
                            </table>
                        </div>

<?php endforeach; ?>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>
        <script type="text/javascript">
            $(document).ready(function () {

            })

            function fn_go_sprots_menu2_1() {
                const previous_page = $('#previous_page').val();
                const previous_srch_date = $('#previous_srch_date').val();
                const previous_sport_id = $('#previous_sport_id').val();
                const previous_location_id = $('#previous_location_id').val();
                const previous_league_id = $('#previous_league_id').val();
                const previous_bet_status_id = $('#previous_bet_status_id').val();
                const previous_fix_id = $('#previous_fix_id').val();
                const previous_team_name = $('#previous_team_name').val();
                const previous_fix_status = $('#previous_fix_status').val();

                const previous_page_data_url
                        = "?page=" + previous_page
                        + "&srch_s_date=" + decodeURIComponent(previous_srch_date)
                        + "&s_id=" + previous_sport_id
                        + "&location_id=" + previous_location_id
                        + "&l_id=" + previous_league_id
                        + "&bs_id=" + previous_bet_status_id
                        + "&fix_id=" + decodeURIComponent(previous_fix_id)
                        + "&tn=" + decodeURIComponent(previous_team_name)
                        + "&fs_id=" + previous_fix_status;

                url = "realtime_manager_passivity.php" + previous_page_data_url

                location.href = url;
            }
        </script>
<?php
include_once(_BASEPATH . '/common/bottom.php');
?> 
    </body>
</html>
