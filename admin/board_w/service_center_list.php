<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    
    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = date("Y/m/d", strtotime("-3 day", time()));
    $end_date = $today;

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $MEMAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }

    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $MEMAdminDAO->real_escape_string($_REQUEST['v_cnt']) : _NUM_PER_PAGE);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = _NUM_PER_PAGE;
    }

    $p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $MEMAdminDAO->real_escape_string($_REQUEST['vtype']) : 'all');
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_key']) : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_val']) : '');

    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":
            if ($p_data['srch_val'] != '') {
                // $srch_basic = " (b.id='".$p_data['srch_val']."' OR b.nick_name='".$p_data['srch_val']."') ";
                $srch_basic = " AND (b.id LIKE '%" . $p_data['srch_val'] . "%'";
                $srch_basic .= " OR b.nick_name LIKE '%" . $p_data['srch_val'] . "%') ";
            }
            break;
        case "s_title":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND a.title LIKE '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_accountname":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND b.account_name LIKE '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_dis":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND b.dis_id LIKE '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_disline":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND b.dis_line_id LIKE '%" . $p_data['srch_val'] . "%' ";
            }
            break;
    }

    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_e_date']) : $end_date);

//$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
//$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);

    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';


    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM menu_qna a ";
    $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";
    $p_data['sql'] .= " WHERE a.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND  a.create_dt <= '" . $p_data['db_srch_e_date'] . "' ";

    $p_data['sql'] .= $srch_basic;

    $db_dataArrCnt = $MEMAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];

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
        $p_data['sql'] = " SELECT a.idx, a.title, a.contents, a.create_dt, a.is_answer, a.is_status, a.is_view, a.member_idx, b.nick_name, b.id, b.level";
        $p_data['sql'] .= " FROM menu_qna a ";
        $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";

        $p_data['sql'] .= " WHERE a.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND  a.create_dt <= '" . $p_data['db_srch_e_date'] . "' ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY a.create_dt DESC ";

        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . " ";

        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    }

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
    <script>
        const isMute = true;
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
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap wrap00">
            <?php
            $menu_name = "board_menu_4";

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
                        <h4>고객센터 관리</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="vtype" id="vtype" value="<?= $p_data['vtype'] ?>">
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
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick" <?php if ($p_data['srch_key'] == 's_idnick') {
                echo "selected";
            } ?>>아이디 및 닉네임</option>
                                        <option value="s_title" <?php if ($p_data['srch_key'] == 's_title') {
                echo "selected";
            } ?>>제목</option>
                                        <option value="s_accountname" <?php if ($p_data['srch_key'] == 's_accountname') {
                echo "selected";
            } ?>>예금주</option>
                                        <option value="s_dis" <?php if ($p_data['srch_key'] == 's_dis') {
                echo "selected";
            } ?>>총판</option>
            <!-- <option value="s_disline" <?php if ($p_data['srch_key'] == 's_disline') {
                echo "selected";
            } ?>>총판라인</option> -->
                                    </select>
                                </div>

                                <div class="">
                                    <input type="text" name="srch_val" id="srch_val" class=""  placeholder="검색" value="<?= $p_data['srch_val'] ?>"/>
                                </div>
                                <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                            </div>
                            <div class="search_form fr">
                                총 <?= number_format($total_cnt) ?>건
                            </div>

                        </div>
                        <div class="panel_tit">
                            <div class="search_form fl">
<?php if ($p_data['vtype'] == 'node') {
    $all_btn = "btn_mdark";
    $node_btn = "btn_blu";
} else {
    $all_btn = "btn_blu";
    $node_btn = "btn_mdark";
} ?>
                            </div>
                        </div>
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>번호</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>레벨</th>
                                <th>기능</th>
                                <th style="width:350px;">제목</th>
                                <th>등록일</th>
                                <th>답변상태</th>
                                <th>회원상태</th>
                                <th>삭제</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    //print_r($db_dataArr);
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                                        //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
                                        //$login_log = explode('|',$row['login_data']);

                                        $db_m_idx = $row['idx'];
                                        $member_idx = $row['member_idx'];
                                        $member_id = $row['id'];

                                        /* if ($row['STATUS'] == 3) {
                                          $str_background = "";
                                          }
                                          else {
                                          $str_background = "background-color:#FDF500;";
                                          } */
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $total_cnt - $num ?></td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $member_idx ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $member_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td><?=$row['level']?></td>
                                            <td>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php', 'popmsg', 660, 1000, 'msg', '<?= $member_idx ?>');">쪽지</a>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo',<?= $member_idx ?>, '7');"  class="btn h25 btn_blu adm_btn_notice_del">베팅</a>
                                                <a href="javascript:;"  onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo',<?= $member_idx ?>, '5');"  class="btn h25 btn_blu adm_btn_notice_del">머니</a>
                                            </td>

                                            <td style='text-align:left;'><a href="/board_w/service_center_detail.php?idx=<?= $db_m_idx ?>"><?= $row['title'] ?></a></td>
                                            <td style='text-align:center'><?= $row['create_dt'] ?></td>
                                            <td style='text-align:center'><?= ($row['is_answer'] == 'Y') ? '답변완료' : '대기' ?></td>
                                            <td style='text-align:center'><?= ($row['is_view'] == 'N') ? '글삭제' : '' ?></td>
                                        <?php if ($row['is_view'] == 'Y') { ?>
                                                <td style='text-align:center'><a href="javascript:fn_del_written(<?= $db_m_idx ?>);" class="btn h25 btn_blu adm_btn_notice_del">삭제</a></td>
                                    <?php } else { ?>
                                                <td></td>
                                    <?php } ?>
                                        </tr>
                                    <?php
                                    $i++;
                                }
                            }
                        } else {
                            echo "<tr><td colspan='11'>데이터가 없습니다.</tr>";
                        }
                        ?>

                        </table>
        <?php
        $requri = explode('?', $_SERVER['REQUEST_URI']);
        $reqFile = basename($requri[0]);
        $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "";
        $default_link .= "&srch_s_date=" . $p_data['srch_s_date'] . "&srch_e_date=" . $p_data['srch_e_date'] . "&vtype=" . $p_data['vtype'] . " ";

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
            $(document).ready(function () {
                offConsultSound();
            });

            function offConsultSound() {
                // 상담/신청 사운드 끄기
                /*let icon = $('#a_consult').children()[0];
                 let audio = $('#a_consult').children()[1];
                 
                 $(icon).attr('style', 'color: red;');
                 $(icon).removeClass();
                 $(icon).attr('class', 'mte i_volume_off vam');
                 
                 $(audio).trigger('pause');
                 $(audio).prop('currentTime', 0);
                 audio.muted = true;*/

                // 이때까지 올라온 상담건수 사운드 끄기처리
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/board_w/_service_center_off_sound.php',
                    data: {},
                    success: function (result) {
                        if (result['retCode'] == "1000") {
                            console.log('처리하였습니다.');
                        }
                    },
                    error: function (request, status, error) {
                    }
                });
            }

            function setDate(sdate, edate) {
                var fm = document.search;

                fm.srch_s_date.value = sdate;
                fm.srch_e_date.value = edate;
            }

            function goSearch(vtype = null) {
                var fm = document.search;
                fm.vtype.value = vtype;

                /*if((fm.srch_key.value!='') && (fm.srch_val.value=='')) {
                 
                 if (fm.srch_level.value < 1) {
                 //alert('검색어를 입력해 주세요.');
                 //fm.srch_val.focus();
                 //return;
                 }
                 }*/

                fm.method = "get";
                fm.submit();
            }

            const goBetSearch = function (member_id = ``) {
                var fm = document.search;
                fm.srch_key.value = `s_idnick`;
                fm.srch_val.value = `${member_id}`;
                fm.action = `/sports_w/prematch_betting_list.php`;

                fm.method = "get";
                fm.submit();
            }
            const goMoneySearch = function (member_id = ``) {
                var fm = document.search;
                fm.srch_key.value = `s_idnick`;
                fm.srch_val.value = `${member_id}`;
                fm.action = `/money_w/money_log_list.php`;

                fm.method = "get";
                fm.submit();
            }

            // 글 삭제
            function fn_del_written(idx) {
                var str_msg = '삭제 하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/board_w/_service_center_prc_del.php',
                        data: {'idx': idx},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('삭제하였습니다.');
                                window.location.reload();
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('삭제에 실패하였습니다.');
                            return;
                        }
                    });
                } else {
                    return;
                }
            }
        </script>
    </body>

</html>