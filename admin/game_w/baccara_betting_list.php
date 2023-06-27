<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end
include_once(_LIBPATH . '/class_Code.php');

if (!isset($_SESSION)) {
    session_start();
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();
$arrUserList = array();

if ($db_conn) {
    // 총판목록
    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = date("Y/m/d", strtotime("-3 day", time()));
    $end_date = $today;
    $prd_id = trim(isset($_REQUEST['prd_id']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['prd_id']) : 0);

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }

    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['v_cnt']) : 20);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = 20;
    }

    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_key']) : 's_idnick');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_val']) : '');
    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_e_date']) : $end_date);

    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';

    $srch_basic = "";
    $srch_url = "";
// 관리자면 전체
    /* if($_SESSION['loginType'] == 1){
      $srch_basic = "";
      $srch_url = "";
      }else{
      $srch_basic = " AND recommend_member = ".$_SESSION['index'];
      $srch_url = "recommend_member".$_SESSION['index'];
      } */

    if ($prd_id > 0) {
        $srch_basic = " AND PRD_ID = $prd_id";
    }

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $find = true; // 총판접속시 해당 총판유저만 검색가능하게 한다.
    switch ($p_data["srch_key"]) {
        case "s_idnick":
            if ($p_data['srch_val'] != '') {
                /* $p_data['sql'] = "SELECT count(*) CNT FROM member WHERE (member.id like '%" . $p_data['srch_val'] . "%' OR member.nick_name like '%" . $p_data['srch_val'] . "%') AND recommend_member = ".$_SESSION['index'];
                  $result = $LSportsAdminDAO->getQueryData($p_data);
                  if($result[0]['CNT'] > 0){
                  $find = false;
                  } */

                $srch_basic .= " AND (member.id like '%" . $p_data['srch_val'] . "%' OR member.nick_name like '%" . $p_data['srch_val'] . "%') ";
                $srch_url .= "srch_key=s_idnick&srch_val=" . $p_data['srch_val'];
            }
            break;
    }

    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    // 총판계정 접속시
    if ($_SESSION['u_business'] > 0) {
        //$srch_basic .= " AND member.recommend_member = ".$_SESSION['member_idx'];
        list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'], $LSportsAdminDAO);
        $str_param = implode(',', $param_dist);
        $srch_basic .= " AND member.recommend_member in ($str_param) ";
    }

    // 전체갯수
    $p_data['sql'] = "SELECT count(*) as CNT FROM OD_HASH_BET_HIST JOIN member ON OD_HASH_BET_HIST.MBR_IDX = member.idx";
    $p_data['sql'] .= " WHERE PRD_TYPE = 'B' AND REG_DTM >= '" . $p_data['db_srch_s_date'] . "' AND REG_DTM <= '" . $p_data['db_srch_e_date'] . "'";
    $p_data['sql'] .= $srch_basic;
    $result = $LSportsAdminDAO->getQueryData($p_data);
    $total_cnt = $result[0]['CNT'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    // 소속 유저가 아니면 검색 안됨.
    //if(!$find) $total_cnt = 0;
    if ($total_cnt > 0) {
        $p_data['sql'] = "SELECT OD_HASH_BET_HIST.*, id, nick_name FROM OD_HASH_BET_HIST JOIN member ON OD_HASH_BET_HIST.MBR_IDX = member.idx";
        $p_data['sql'] .= " WHERE PRD_TYPE = 'B' AND REG_DTM >= '" . $p_data['db_srch_s_date'] . "' AND REG_DTM <= '" . $p_data['db_srch_e_date'] . "'";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " order by HASH_BET_IDX DESC";
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . ";";
        $db_dataArr = $LSportsAdminDAO->getQueryData($p_data);
        //print_r($db_dataArr);
    }
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
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
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
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
            <input type="hidden" id="memberIdxList" name="memberIdxList">
        </form>
        <div class="wrap">
<?php
$menu_name = "baccara_betting_list";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">
<?php //echo $sql;   ?>
                <?php //print_r($db_dataArr);   ?>
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>해쉬바카라 배팅내역</h4>
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
                                <div><a href="javascript:;" onClick="setDate('<?= $today ?>', '<?= $today ?>');" class="btn h30 btn_blu">오늘</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_week ?>', '<?= $today ?>');" class="btn h30 btn_orange">1주일</a></div>
                                <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>', '<?= $today ?>');" class="btn h30 btn_green">한달</a></div>
                                <div class="" style="padding-right: 10px;"></div>
                                <div>
                                    <select name="prd_id" id="prd_id" style="width: 100%">
                                        <option value="0">전체</option>

<?php foreach ($prdList as $key => $item) { ?>
                                            <option value="<?= $key ?>"   <?php if ($prd_id == $key): ?> selected<?php endif; ?>><?= $item ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick">아이디 및 닉네임</option>
                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <input type="text" id="srch_val" name="srch_val" class=""  placeholder="" value="<?= $p_data['srch_val'] ?>" />
                                </div>
                                <div> <a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
<?php if (0 == $u_business) { ?>
                                    <div><a href="javascript:openNotePop();" style="margin-left: 10px" class="btn h30 btn_blu">쪽지</a></div>
                                <?php } ?>
                                <!--<div><a href="#" class="btn h30 btn_blu" onclick="onBtnClickTotalCalculate()">전체 정산</a></div>-->
                            </div>
                            <div class="search_form fr">
                                총 <?= number_format($total_cnt) ?>건
                            </div>
                        </div>
                    </form>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th width="3%">
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                        <label for="checkbox_css_all"></label>
                                    </div>
                                </th>
                                <th width="6%">날짜</th>
                                <th width="5%">아이디</th>
                                <th width="5%">닉네임</th>
                                <th width="5%">베팅금액</th>
                                <th width="5%">당첨금</th>
                                <th width="5%">보유금액</th>
                                <th width="6%">베팅결과</th>
                                <th width="5%">웨이브</th>
                            </tr>
<?php
$db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
$i = 0;
foreach ($db_dataArr as $key => $item) {
    $chkbox_css[$i] = "checkbox_css_" . $i;
    $gameResult = '';
    $status = '배팅';
    $status_color = '';
    if ('W' == $item['TYPE']) {
        $status = '적중';
        $status_color = '#9FD5E9';
    } else if ('L' == $item['TYPE']) {
        $status = '낙첨';
        $status_color = '#F9BFBF';
    } else if ('C' == $item['TYPE']) {
        $status = '취소';
        $status_color = '#FFFCBB';
    } else if ('I' == $item['TYPE']) {
        $status = '인게임보너스';
        $status_color = '#FFFCBB';
    } else if ('P' == $item['TYPE']) {
        $status = '프로모션보너스';
        $status_color = '#FFFCBB';
    } else if ('J' == $item['TYPE']) {
        $status = '잭팟보너스';
        $status_color = '#FFFCBB';
    }

    // 당첨금
    $winMoney = $item['RSLT_MNY'] + $item['BET_MNY'];

    // 보유금액
    $hld_money = $item['HLD_MNY'] + $item['RSLT_MNY'];
    //if($item['RSLT_MNY'] > 0)
    //    $hld_money = $item['HLD_MNY'] + $item['RSLT_MNY'];
    ?>

                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                            <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $item['SLOT_BET_IDX'] ?>" data-member-idx="<?= $item['MBR_IDX'] ?>"/>
                                            <label for="<?= $chkbox_css[$i] ?>"></label>
                                        </div>
                                    </td>
                                    <td width="6%"><?= $item['REG_DTM'] ?></td>
    <?php if (0 == $_SESSION['u_business']) { ?>
                                        <td width="8%">
                                            <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $item['MBR_IDX'] ?>', '7');"><?= $item['id'] ?></a> 
                                        </td>
                                        <td width="5%">
                                            <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $item['MBR_IDX'] ?>', '7');"><?= $item['nick_name'] ?></a>
                                        </td>

    <?php } else { ?>

                                        <td width="8%">
                                            <a href="javascript:;" ><?= $item['id'] ?></a> 
                                        </td>
                                        <td width="5%">
                                            <a href="javascript:;" ><?= $item['nick_name'] ?></a>
                                        </td>
    <?php } ?>

                                    <td width="5%" style='text-align:right'><?= number_format($item['BET_MNY']) ?></td>
                                    <td width="5%" style='text-align:right'><?= number_format($winMoney) ?></td>
                                    <td width="5%" style='text-align:right'><?= number_format($hld_money) ?></td>
                                    <td width="5%" bgcolor="<?= $status_color ?>"><?= $status ?></td>
                                    <td width="5%"><?= $item['WAVE'] ?></td>
    <?php $i++;
}
?>
                        </table>
                    </div>
                    <?php
                    $requri = explode('?', $_SERVER['REQUEST_URI']);
                    $reqFile = basename($requri[0]);
                    $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'];
                    $default_link .= "&srch_s_date=" . $p_data['srch_s_date'] . "&srch_e_date=" . $p_data['srch_e_date'];
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
                $('#srch_key').val('<?= $p_data['srch_key'] ?>').prop("selected", true);
            });

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

            function allCheckFunc(obj) {
                $("[name=chk]").prop("checked", $(obj).prop("checked"));
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

                $("#checkbox_css_all").change(function () {
                    if ($("#checkbox_css_all").is(':checked')) {
                        allCheckFunc(this);
                    } else {
                        $("[name=chk]").prop("checked", false);
                    }
                });
            })
        </script>
        <?php
        include_once(_BASEPATH . '/common/bottom.php');
        ?>
    </body>
</html>