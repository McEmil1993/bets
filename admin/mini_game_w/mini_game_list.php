<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');
include_once(_LIBPATH . '/class_GameStatusUtil.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end



$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = $today;
$end_date = $today;


$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 20);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 20;
}

if('ON' == IS_EOS_POWERBALL)
    $default_srch_key = 'eospb5';
else if('ON' == IS_POWERBALL)
    $default_srch_key = 'powerball';
else
    $default_srch_key = 'pladder';

// $p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $_REQUEST['vtype'] : 'all');
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : $default_srch_key);
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

$srch_basic = "where bet_type = ";
switch ($p_data["srch_key"]) {
    case "powerball":
        $srch_basic .= "15";
        break;
    case "kladder":
        $srch_basic .= "5";
        break;
    case "pladder":
        $srch_basic .= "4";
        break;
    case "b_soccer":
        $srch_basic .= "6";
        break;
    case "eospb5":
        $srch_basic .= "3";
        break;
    default:
        $srch_basic .= "3";
        break;
}

if($p_data['srch_val'] != '')
    $srch_basic .= " AND (a.id = '" . $p_data['srch_val'] . "' OR a.cnt = '" . $p_data['srch_val'] . "') ";

$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);

$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']).' 00:00:00';
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']).' 23:59:59';

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {
    // 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM mini_game a ";
    
    $p_data['sql'] .= $srch_basic;
    $p_data['sql'] .= " AND a.start_dt >= '".$p_data['db_srch_s_date']."'";
    $p_data['sql'] .= " AND a.end_dt <= '".$p_data['db_srch_e_date']."'";
    $p_data['sql'] .= ";";
    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    
    $total_cnt = $db_dataArrCnt[0]['CNT'];

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page  = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block         = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block'] * ($block - 1)) + 1;          // 첫번째 페이지
    $last_page      = $p_data['page_per_block'] * $block;                // 마지막 페이지

    if ($block >= $total_block) $last_page = $total_page;

    // 게시물이 하나 이상이다.
    if ($total_cnt > 0) {
        $p_data['sql'] = " SELECT game, id, cnt, bet_type, admin_bet_status, start_dt, end_dt, result, result_score, league";
        $p_data['sql'] .= "  FROM mini_game a ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " AND a.start_dt >= '".$p_data['db_srch_s_date']."'";
        $p_data['sql'] .= " AND a.end_dt <= '".$p_data['db_srch_e_date']."'";
        $p_data['sql'] .= ' order by start_dt desc';
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . " ";
        $p_data['sql'] .= ";";
        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }

    $BdsAdminDAO->dbclose();

    $db_dataArr = !empty($db_dataArr) ? $db_dataArr : [];
    foreach ($db_dataArr as $key => $row) {
        $result_arr = json_decode($row['result']);
        if($row['result_score'] != ''){
            $db_dataArr[$key] = (object)array_merge((array) $row, (array) json_decode($row['result_score']));
        }else{
            $db_dataArr[$key] = (object)array_merge((array) $row, (array) $result_arr);
        }
    }
    
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
        <input type="hidden" id="m_dis_id" name="m_dis_id">
        <input type="hidden" id="selContent" name="selContent" value="3">
    </form>
    <div class="wrap">
        <?php

        $menu_name = "mini_game_menu1";

        include_once(_BASEPATH . '/common/left_menu.php');

        include_once(_BASEPATH . '/common/iframe_head_menu.php');


        $start_date = date("Y/m/d");
        $end_date = date("Y/m/d");
        ?>
        <!-- Contents -->
        <div class="con_wrap">

            <div class="title">
                <a href="">
                    <i class="mte i_group mte-2x vam"></i>
                    <h4>미니게임 내역</h4>
                </a>
            </div>
            <!-- list -->
            <div class="panel reserve">
                <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                    <?php /*
                    <input type="hidden" name="vtype" id="vtype" value="<?= $p_data['vtype'] ?>">
                    */ ?>
                    <div class="panel_tit">
                        <div class="search_form fl">
                            <div class="" style="padding-right: 10px;">
                                <select name="srch_key" id="srch_key" onchange="javascript:onChange(this);">
                                  
                                    <?php if('ON' == IS_EOS_POWERBALL){ ?>
                                    <option value="eospb5" <?php if ($p_data['srch_key'] == 'eospb5') {
                                                                    echo "selected";
                                                                } ?>>EOS 파워볼</option>
                                    <?php } ?>
                                    <?php if('ON' == IS_POWERBALL){ ?>
                                    <option value="powerball" <?php if ($p_data['srch_key'] == 'powerball') {
                                                                    echo "selected";
                                                                } ?>>파워볼</option>
                                    <?php } ?>
                                    <option value="pladder" <?php if ($p_data['srch_key'] == 'pladder') {
                                                                echo "selected";
                                                            } ?>>파워사다리</option>
                                    <option value="kladder" <?php if ($p_data['srch_key'] == 'kladder') {
                                                                echo "selected";
                                                            } ?>>키노사다리</option>
                                    <option value="b_soccer" <?php if ($p_data['srch_key'] == 'b_soccer') {
                                                                    echo "selected";
                                                            } ?>>가상축구</option>
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
                    <div><a href="javascript:;" onClick="setDate('<?= $today ?>','<?= $today ?>');" class="btn h30 btn_blu">오늘</a></div>
                    <div><a href="javascript:;" onClick="setDate('<?= $before_week ?>','<?= $today ?>');" class="btn h30 btn_orange">1주일</a></div>
                    <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>','<?= $today ?>');" class="btn h30 btn_green">한달</a></div>
                    <div class="" style="padding-right: 10px;"></div>
                    
                    <div class="">
                        <input type="text" name="srch_val" id="srch_val" class=""  placeholder="경기번호" value="<?= $p_data['srch_val'] ?>"/>
                    </div>
                    <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                        </div>
                        <div class="search_form fr">
                            총 <?= number_format($total_cnt) ?>건
                        </div>
                    </div>
                </form>
                <div class="tline">
                    <table class="mlist">
                        <tr>
                            <th>번호</th>
                            <th>구분</th>
                            <th>회차</th>
                            <th>경기번호</th>
                            <th>시작시간</th>
                        <?php if($p_data['srch_key'] == 'b_soccer'){ ?>
                            <th>리그</th>
                            <th>홈</th>
                            <th>원정</th>
                            <th>점수</th>
                            <th>타입</th>
                            <th>축구결과</th>
                        <?php }else{ ?>
                            <th>마감시간</th>
                        <?php } ?>
                        
                        <?php if($p_data['srch_key'] == 'powerball' || $p_data['srch_key'] == 'eospb5'){ ?>
                            <th>결과1</th>
                            <th>결과2</th>
                            <th>결과3</th>
                            <th>결과4</th>
                            <th>결과5</th>
                            <th>결과6(파워볼)</th>
                        <?php } ?>
                        
                        <?php if($p_data['srch_key'] == 'pladder' || $p_data['srch_key'] == 'kladder'){ ?>
                            <th>출발</th>
                            <th>줄수</th>
                            <th>홀짝</th>                            
                        <?php } ?>
                            
                            <th>정산</th>
                            <th>온오프</th>
                        </tr>
                        <?php
                        if ($total_cnt > 0) {
                            $i = 0;
                            if (!empty($db_dataArr)) {
                                foreach ($db_dataArr as $row) {
                                    $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                               
                                    $dt = '';
                                    
                                    $cnt = 0;
                                    if($p_data['srch_key'] == 'powerball' || $p_data['srch_key'] == 'eospb5'){
                                        $time = explode(' ', $row->start_dt)[1];
                                        $time = explode(':', $time);
                                        $cnt = round((($time[0]*60) + $time[1])/5) + 1;
                                    }else if($p_data['srch_key'] == 'b_soccer'){
                                        $cnt = $row->oid;
                                    }else{
                                        $cnt = $row->cnt;
                                    }
                               
                                    ?>
                                    <tr <?= !empty($row->result_score) ? 'style="background-color: #FFFACC;"' : ''?> onmouseover="this.style.backgroundColor='#FDF2E9';" <?= !empty($row->result_score) ? "onmouseout=\"this.style.backgroundColor='#FFFACC';\"" : "onmouseout=\"this.style.backgroundColor='#ffffff';\""?>>
                                        <td><?= $total_cnt - $num ?></td>
                                        <td style='text-align:center'><?= GameStatusUtil::get_minigame_name($row->game) ?></td>
                                        <td style='text-align:center'><?= $cnt ?></td>
                                        <td style='text-align:center'><?= !empty($row->id) ? $row->id : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->start_dt) ? $row->start_dt : '' ?></td>
                                    <?php if($p_data['srch_key'] == 'b_soccer'){ ?>
                                        <td style='text-align:center'><?= !empty($row->league) ? $row->league : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->home) ? $row->home : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->away) ? $row->away : '' ?></td>
                                        <td style='text-align:center'><?= $row->scoreh.' : '.$row->scorea ?></td>
                                        <td style='text-align:center'><?=GameStatusUtil::get_minigame_result_name($row->type) ?></td>
                                        <td style='text-align:center'><?= !empty($row->res) ? $row->res : '' ?></td>
                                    <?php }else{ ?>
                                        <td style='text-align:center'><?= !empty($row->end_dt) ? $row->end_dt : '' ?></td>
                                    <?php } ?>
                                        
                                    <?php if($p_data['srch_key'] == 'powerball' || $p_data['srch_key'] == 'eospb5'){ ?>    
                                        <td style='text-align:center'><?= !empty($row->num1) ? $row->num1 : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->num2) ? $row->num2 : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->num3) ? $row->num3 : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->num4) ? $row->num4 : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->num5) ? $row->num5 : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->num5) ? $row->pb : ''?></td>
                                    <?php } ?>
                                        
                                    <?php if($p_data['srch_key'] == 'pladder' || $p_data['srch_key'] == 'kladder'){ ?>
                                        <td style='text-align:center'><?= !empty($row->start) ? GameStatusUtil::get_minigame_result_name($row->start) : ''  ?></td>
                                        <td style='text-align:center'><?= !empty($row->line) ?$row->line : '' ?></td>
                                        <td style='text-align:center'><?= !empty($row->oe) ? GameStatusUtil::get_minigame_result_name($row->oe) : '' ?></td>
                                    <?php } ?>
                                        <td>
                                            <a href="#" class="btn h30 btn_blu" onClick="fn_cal_game(<?= $row->id ?>, '<?= $row->game ?>');">정산</a>
                                        </td>
                                        <td style='text-align:left'>
                                            <?php if ($row->admin_bet_status == 'ON') { ?>
                                                <a href="#" onclick="betOnOffBtnClick(this, <?= $row->id ?>, '<?= $row->bet_type ?>', 'OFF')" class="btn h25 btn_green"> <?= $row->admin_bet_status ?></a>
                                            <?php } else if ($row->admin_bet_status == 'OFF') { ?>
                                                <a href="#" onclick="betOnOffBtnClick(this, <?= $row->id ?>, '<?= $row->bet_type ?>', 'ON')" class="btn h25 btn_gray"> <?= $row->admin_bet_status ?></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                $i++;
                                }
                            }
                        } else {
                            echo "<tr><td colspan='21'>데이터가 없습니다.</tr>";
                        }
                        ?>

                    </table>
                    <?php
                    $requri = explode('?', $_SERVER['REQUEST_URI']);
                    $reqFile = basename($requri[0]);
                    $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "";
                    $default_link .= "&srch_s_date=" . $p_data['srch_s_date'] . "&srch_e_date=" . $p_data['srch_e_date'] . " ";

                    include_once(_BASEPATH . '/common/page_num.php');
                    ?>
                </div>
            </div>
            <!-- END list -->
        </div>
        <!-- END Contents -->
        <script>
            let round = 0;
            let game_type = 0;
        </script>
        <div id="cal_game" name="cal_game" class="pop-window">
            <div class="con_wrap">
                <div class="panel reserve">
                    <div class="title">
                        미니게임 정산
                    </div>

                    <div class="tline tline_flexbox">
                        <table id="popup_list" name="popup_list" class="mlist">
                            <tr>
                                <th>게임종류</th>
                                <th>회차</th>
                            </tr>
                            <tr class="flexbox_vertical">
                                <td id="game_type">0</td>
                                <td id="game_round">
                                    <script>
                                        document.write(round)
                                    </script>
                                </td>
                            </tr>
                        </table>
                        <table id="popup_list" name="popup_list" class="mlist">
                            <tr>
                                <!--<th>종류</th>-->
                                <th colspan="7">결과</th>
                            </tr>
                            <tr id="powerball_api" style="display: none;">
                                <td>자동결과</td>
                                <td><input type="number" id="num1_api" value="" min="1" max="28" readonly></td>
                                <td><input type="number" id="num2_api" value="" min="1" max="28" readonly></td>
                                <td><input type="number" id="num3_api" value="" min="1" max="28" readonly></td>
                                <td><input type="number" id="num4_api" value="" min="1" max="28" readonly></td>
                                <td><input type="number" id="num5_api" value="" min="1" max="28" readonly></td>
                                <td><input type="number" id="num6_api" value="" min="0" max="9" placeholder="파워볼" readonly></td>
                            </tr>
                            <tr id="powerball" style="display: none;">
                                <td>수동결과</td>
                                <td><input type="number" id="num1" value="" min="1" max="28"></td>
                                <td><input type="number" id="num2" value="" min="1" max="28"></td>
                                <td><input type="number" id="num3" value="" min="1" max="28"></td>
                                <td><input type="number" id="num4" value="" min="1" max="28"></td>
                                <td><input type="number" id="num5" value="" min="1" max="28"></td>
                                <td><input type="number" id="num6" value="" min="0" max="9" placeholder="파워볼"></td>
                            </tr>

                            <tr id="pladder_api" style="display: none;">
                                <td>자동결과</td>
                                <td><input type="text" id="p_start_api" value="" readonly></td>
                                <td><input type="number" id="p_line_api" value="" readonly></td>
                                <td><input type="text" id="p_oe_api" value="" readonly></td>
                            </tr>
                            <tr id="pladder" style="display: none;">
                                <td>수동결과</td>
                                <td>
                                    <select class="input_style02" id="p_start">
                                        <option value="Left">좌</option>
                                        <option value="Right">우</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="input_style02" id="p_line">
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="input_style02" id="p_oe">
                                        <option value="Odd">홀</option>
                                        <option value="Even">짝</option>
                                    </select>
                                </td>
                            </tr>

                            <tr id="kladder_api" style="display: none;">
                                <td>자동결과</td>
                                <td><input type="text" id="k_start_api" value="" readonly></td>
                                <td><input type="number" id="k_line_api" value="" readonly></td>
                                <td><input type="text" id="k_oe_api" value="" readonly></td>
                            </tr>
                            <tr id="kladder" style="display: none;">
                                <td>수동결과</td>
                                <td>
                                    <select class="input_style02" id="k_start">
                                        <option value="Left">좌</option>
                                        <option value="Right">우</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="input_style02" id="k_line">
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="input_style02" id="k_oe">
                                        <option value="Odd">홀</option>
                                        <option value="Even">짝</option>
                                    </select>
                                </td>
                            </tr>

                            <tr id="bsoccer_api" style="display: none;">
                                <td>자동결과</td>
                                <td><input type="number" id="home_score_api" value="" placeholder="홈팀 스코어" readonly></td>
                                <td><input type="number" id="away_score_api" value="" placeholder="원정팀 스코어" readonly></td>
                            </tr>
                            <tr id="bsoccer" style = "display: none;">
                                <td>수동결과</td>
                                <td><input type="number" id="home_score" value="0" placeholder="0" min="0"></td>
                                <td><input type="number" id="away_score" value="0" placeholder="0" min="0"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="panel_tit">
                        <div class="search_form fr">
                            2차인증 비밀번호
                            <input type="password" name="second_pass" id="second_pass" value="" maxlength="6"/>
                            <a href="javascript:fnUpdateCalMiniGame();" class="btn h30 btn_blu">정산</a>
                            <a href="#" class="btn h30 btn_blu" onclick="javascript:fnPopupClose();">닫기</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function betOnOffBtnClick(ateg, id, bet_type, status) {
            //console.log(fixture_start_date);

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/mini_game_w/_mini_game_onoff_ajax.php',
                data: {'cmd': 'bet_' + status, 'id': id, 'bet_type': bet_type},
                success: function (result) {
                    console.log(result['retCode']);
                    if (result['retCode'] == "1000") {
                        alert('업데이트 되었습니다.');

                        if ("on" == status || "ON" == status) {
                            $(ateg).removeClass("btn_gray").removeClass("btn_green").addClass("btn_green");
                            $(ateg).attr("onclick", "betOnOffBtnClick(this," + id + ", " + bet_type + ", 'OFF')");
                            $(ateg).text("ON");
                        } else {
                            $(ateg).removeClass("btn_green").removeClass("btn_gray").addClass("btn_gray");
                            $(ateg).attr("onclick", "betOnOffBtnClick(this," + id + ", " + bet_type + ", 'ON')");
                            $(ateg).text("OFF");
                        }
                        return;
                    } else {
                        alert('업데이트 실패 (1)');
                        return;
                    }
                },
                error: function (request, status, error) {
                    alert('업데이트 실패 (2)');

                    return;
                }
            });
        }
            
    // 정산
    function fn_cal_game(id, game) {
        $('#game_round').text(id);
        $('#game_type').text(game);
        game_type = game;
//console.log(row);
        // 전부 닫기
        $('#powerball_api').attr('style', 'display: none');
        $('#powerball').attr('style', 'display: none');
        $('#pladder_api').attr('style', 'display: none');
        $('#pladder').attr('style', 'display: none');
        $('#kladder_api').attr('style', 'display: none');
        $('#kladder').attr('style', 'display: none');
        $('#bsoccer').attr('style', 'display: none');

        if (game === "powerball" || game === 'eospb5') {
            $('#powerball_api').attr('style', 'display: table-row');
            $('#powerball').attr('style', 'display: table-row');
        } else if (game === "pladder") {
            $('#pladder_api').attr('style', 'display: table-row');
            $('#pladder').attr('style', 'display: table-row');
        } else if (game === "kladder") {
            $('#kladder_api').attr('style', 'display: table-row');
            $('#kladder').attr('style', 'display: table-row');
        } else {
            $('#bsoccer_api').attr('style', 'display: table-row');
            $('#bsoccer').attr('style', 'display: table-row');
        }

        $('#cal_game').attr('style', 'display: block');

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/mini_game_w/_mini_game_data_id_ajax.php',
            //async: false,
            data: {
                'id': id,
                'game': game
            },
            success: function(result) {
                if (result['retCode'] == "1000") {
                    console.log(result['retData']['result']);
                    let gameResult = JSON.parse(result['retData']['result']);
                    let result_score = '';
                    if(result['retData']['result_score'] !== ''){
                        result_score = JSON.parse(result['retData']['result_score']);
                    }
                    
                    if(gameResult['game'] === 'powerball' || gameResult['game'] === 'eospb5'){
                        $('#num6_api').val(gameResult['pb']);
                        $('#num1_api').val(gameResult['num1']);
                        $('#num2_api').val(gameResult['num2']);
                        $('#num3_api').val(gameResult['num3']);
                        $('#num4_api').val(gameResult['num4']);
                        $('#num5_api').val(gameResult['num5']);
                        
                        if(result_score == ''){
                            $('#num6').val(gameResult['pb']);
                            $('#num1').val(gameResult['num1']);
                            $('#num2').val(gameResult['num2']);
                            $('#num3').val(gameResult['num3']);
                            $('#num4').val(gameResult['num4']);
                            $('#num5').val(gameResult['num5']);
                        }else{
                            $('#num6').val(result_score['pb']);
                            $('#num1').val(result_score['num1']);
                            $('#num2').val(result_score['num2']);
                            $('#num3').val(result_score['num3']);
                            $('#num4').val(result_score['num4']);
                            $('#num5').val(result_score['num5']);
                        }
                    }else if(gameResult['game'] === 'pladder'){
                        $('#p_start_api').val(getMiniGameResultName(gameResult['start']));
                        $('#p_line_api').val(gameResult['line']);
                        $('#p_oe_api').val(getMiniGameResultName(gameResult['oe']));
                                
                        $('#p_start').val(result_score['start']).prop("selected",true);
                        $('#p_line').val(result_score['line']).prop("selected",true);
                        $('#p_oe').val(result_score['oe']).prop("selected",true);
                    }else if(gameResult['game'] === 'kladder'){
                        $('#k_start_api').val(getMiniGameResultName(gameResult['start']));
                        $('#k_line_api').val(gameResult['line']);
                        $('#k_oe_api').val(getMiniGameResultName(gameResult['oe']));
                                
                        $('#k_start').val(result_score['start']).prop("selected",true);
                        $('#k_line').val(result_score['line']).prop("selected",true);
                        $('#k_oe').val(result_score['oe']).prop("selected",true);
                    }else if(gameResult['game'] === 'b_soccer'){
                        $('#home_score_api').val(gameResult['scoreh']);
                        $('#away_score_api').val(gameResult['scorea']);
                        
                        if(result_score != ''){
                            $('#home_score').val(result_score['scoreh']);
                            $('#away_score').val(result_score['scorea']);
                        }
                    }
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function(request, status, error) {
                console.log(request);
                alert('수정에 실패하였습니다.');
                return;
            }
        });
    }

    // 스코어 업데이트
    function fnUpdateCalMiniGame() {
        round = $('#game_round').text();
        game_type = $('#game_type').text();
        let second_pass = $('#second_pass').val();
        if (second_pass === '') {
            alert('2차인증 비번을 넣어주세요.');
            return;
        }

        let arrMiniGameData = [];
        let obj = new Object();
        if (game_type == 'powerball' || game_type == 'eospb5') {
            if ($('#num1').val() <= 0 || $('#num2').val() <= 0 || $('#num3').val() <= 0 || $('#num4').val() <= 0 || $('#num5').val() <= 0 || $('#num6').val() <= 0) {
                alert('숫자입력을 해주세요.');
                return;
            }

            obj = {
                'pb': $('#num6').val(),
                'num1': $('#num1').val(),
                'num2': $('#num2').val(),
                'num3': $('#num3').val(),
                'num4': $('#num4').val(),
                'num5': $('#num5').val()
            };
            arrMiniGameData.push(obj);
        } else if (game_type == 'pladder') {
            obj = {
                'oe': $("#p_oe option:selected").val(),
                'line': $("#p_line option:selected").val(),
                'start': $("#p_start option:selected").val()
            };
            arrMiniGameData.push(obj);
        } else if (game_type == 'kladder') {
            obj = {
                'oe': $("#k_oe option:selected").val(),
                'line': $("#k_line option:selected").val(),
                'start': $("#k_start option:selected").val()
            };
            arrMiniGameData.push(obj);
        } else if (game_type == 'b_soccer') {
            obj = {
                'home_score': $('#home_score').val(),
                'away_score': $('#away_score').val()
            };
            /*if ($('#home_score').val() <= 0 || $('#away_score').val() <= 0) {
                alert('스코어를 입력해주세요.');
                return;
            }*/
            arrMiniGameData.push(obj);
        }

        let miniGameData = JSON.stringify(arrMiniGameData);
//console.log(miniGameData);
//return;
        var result = confirm('정산 하시겠습니까?');
        if (result) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/mini_game_w/_mini_game_calMiniGame.php',
                data: {
                    'miniGameData': miniGameData,
                    'round': round,
                    'second_pass':second_pass
                },
                success: function(result) {
                    if (result['retCode'] == "1000") {
                        alert('정산하였습니다.');
                        url = '/mini_game_w/mini_game_list.php?srch_key=' + game_type;
                        window.location.reload(url);
                        return;
                    }else if(result['retCode'] == "2002") {
                        alert('2차인증 비번이 틀렸습니다.');
                    } else {
                        alert(result['retMsg']);
                        return;
                    }
                },
                error: function(request, status, error) {
                    alert('정산에 실패하였습니다.');
                    return;
                }
            });
        } else {
            return;
        }
    }

    function fnPopupClose() {
        $('#cal_game').attr('style', 'display: none');
    }

    function setDate(sdate, edate) {
        var fm = document.search;

        fm.srch_s_date.value = sdate;
        fm.srch_e_date.value = edate;
    }

    function onChange(obj) {
        url = '/mini_game_w/mini_game_list.php?srch_key=' + obj.value;
        window.location.replace(url);
    }
    function goSearch() {
	var fm = document.search;

	fm.method = "get";
	fm.submit();
}

// 미니게임 결과값 한글로 변환
const getMiniGameResultName = function($result) {
    switch ($result) {
        case 'Odd':
            return '홀';
        case 'Even':
            return '짝';
        case 'Left':
            return '좌';
        case 'Right':
            return '우';
    }
}
</script>
<?php
    include_once(_BASEPATH . '/common/bottom.php');
?>
</body>
</html>
