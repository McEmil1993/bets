<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();


if(0 != $_SESSION['u_business']){
    die();
}

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$BdsAdminDAO)){
        die();
    }

    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = $today;
    $end_date = $today;

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $BdsAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }

    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $BdsAdminDAO->real_escape_string($_REQUEST['v_cnt']) : _NUM_PER_PAGE);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = _NUM_PER_PAGE;
    }

    $p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $BdsAdminDAO->real_escape_string($_REQUEST['vtype']) : 'all');
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $BdsAdminDAO->real_escape_string($_REQUEST['srch_key']) : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $BdsAdminDAO->real_escape_string($_REQUEST['srch_val']) : '');

    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":
            if ($p_data['srch_val'] != '') {
                // $srch_basic = " AND (b.id='".$p_data['srch_val']."' OR b.nick_name='".$p_data['srch_val']."') ";
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

    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $BdsAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $BdsAdminDAO->real_escape_string($_REQUEST['srch_e_date']) : $end_date);

//$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
//$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);

    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';

    // 최신 공지 5개만 가져온다.
    $p_data['sql'] = "SELECT a.idx, a.a_id, a.title, a.contents, a.create_dt, a.display, (SELECT COUNT(*) FROM menu_board_comment WHERE board_idx = a.idx) AS comment_count ";
    $p_data['sql'] .= "FROM menu_board a WHERE a.idx > 0 AND a.member_idx = 0 ORDER BY a.idx";
    $db_dataNoticeArrCnt = $BdsAdminDAO->getQueryData($p_data);

    // 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM menu_board a ";
    // $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";
    $p_data['sql'] .= " LEFT JOIN member b ON a.a_id = b.id ";
    $p_data['sql'] .= " WHERE a.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND a.create_dt <= '" . $p_data['db_srch_e_date'] . "' AND a.member_idx > 0";

    /*
      if ($p_data['vtype'] == 'node') {
      $p_data['sql'] .= " AND a.status in(1,2) ";
      }
     */

    $p_data['sql'] .= $srch_basic;

    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block) {
        $last_page = $total_page;
    }

    // 게시물이 하나 이상이다.
    if ($total_cnt > 0) {
        $p_data['sql'] = "SELECT a.idx, a.a_id, a.title, a.contents, a.create_dt, a.display, b.nick_name, (SELECT COUNT(*) FROM menu_board_comment WHERE board_idx = a.idx) AS comment_count";
        $p_data['sql'] .= " FROM menu_board a ";
        $p_data['sql'] .= " LEFT JOIN member b ON a.a_id = b.id ";
        $p_data['sql'] .= " WHERE a.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND a.create_dt <= '" . $p_data['db_srch_e_date'] . "' AND a.member_idx > 0";

        /*
          if ($p_data['vtype'] == 'node') {
          $p_data['sql'] .= " AND a.status in(1,2) ";
          }
         */

        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . " ";

        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }

    $BdsAdminDAO->dbclose();
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
            });
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
            $menu_name = "board_menu_3";

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
                        <h4>공지사항 관리</h4>
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
                                    <input type="text" class="" name="srch_e_date" id="datepicker-autoClose" placeholder="날짜선택" value="<?= $p_data['srch_e_date'] ?>"/>
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
                                <div><a href="/board_w/board_write.php" onClick="goSearch('all');" class="btn h30 <?= $all_btn ?>">등록</a></div>
                            </div>
                        </div>
                    </form>

                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>번호</th>
                                <th>제목</th>
                                <!-- <th>작성자</th> -->
                                <th>댓글수</th>
                                <th>등록일</th>
                                <th>노출여부</th>
                                <th>삭제</th>
                            </tr>

                                    <?php
                                    foreach ($db_dataNoticeArrCnt as $row) {
                                        ?>
                                <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                    <td>공지사항</td>
                                    <td width="800px"><a href="/board_w/board_detail.php?idx=<?= $row['idx'] ?>"><?= $row['title'] ?></a></td>
                                    <td><?= $row['comment_count'] ?></td>
                                    <td><?= $row['create_dt'] ?></td>
                                    <td>
    <?php
    if ($row['display'] == 1) {
        ?>
                                            <a href="javascript:fn_display_noti(<?= $row['idx'] ?>, 0);" class="btn h25 btn_blu adm_btn_notice_del">노출</a>
                                    <?php
                                } else {
                                    ?>
                                            <a href="javascript:fn_display_noti(<?= $row['idx'] ?>, 1);" class="btn h25 btn_red adm_btn_notice_del">비노출</a>
                                    <?php
                                }
                                ?>
                                    </td>
                                    <td>
                                        <a href="javascript:fn_del_noti(<?= $row['idx'] ?>);" class="btn h25 btn_blu adm_btn_notice_del">삭제</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

<?php
if ($total_cnt > 0) {
    $i = 0;
    if (!empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
            // d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
            // print_r($row);
            $db_m_idx = $row['idx'];
            ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                                <!-- <td colspan='3'><?= $total_cnt - $num ?></td> -->
                                            <td><?= $total_cnt - $num ?></td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['a_id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td><a href="/board_w/board_detail.php?idx=<?= $db_m_idx ?>"><?= $row['title'] ?></a></td>
                                            <td><?= $row['comment_count'] ?></td>
                                            <td style='text-align:left'><?= $row['create_dt'] ?></td>
                                            <td><?= $row['display'] ? 'Y' : 'N' ?></td>
                                            <td>
                                                <a href="javascript:fn_del_noti(<?= $db_m_idx ?>);" class="btn h25 btn_blu adm_btn_notice_del">삭제</a>
                                            </td>
                                            <!--
                                            <td style='text-align:right'><?= number_format($row['money']) ?></td>
                                            <td style='<?= $str_background ?>'><?= $row['create_dt'] ?></td>
                                            -->
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
    </body>
    <script>
        // 공지 노출 변경
        function fn_display_noti(idx, display) {
            var result = confirm('변경 하시겠습니까?');
            if (result) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/board_w/_board_prc_notice_display.php',
                    data: {'idx': idx, 'display': display},
                    success: function (result) {
                        if (result['retCode'] == "1000") {
                            alert('변경 하였습니다.');
                            window.location.reload();
                            return;
                        } else {
                            alert(result['retMsg']);
                            return;
                        }
                    },
                    error: function (request, status, error) {
                        alert('변경에 실패하였습니다.');
                        return;
                    }
                });
            }
        }

        // 삭제
        function fn_del_noti(idx) {
            var str_msg = '삭제 하시겠습니까?';
            var result = confirm(str_msg);
            if (result) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/board_w/_board_prc_del.php',
                    data: {'idx': idx},
                    success: function (result) {
                        if (result['retCode'] == "1000") {
                            alert('삭제 하였습니다.');
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

        function setDate(sdate, edate) {
            var fm = document.search;
            fm.srch_s_date.value = sdate;
            fm.srch_e_date.value = edate;
        }

        function goSearch(vtype = null) {
            var fm = document.search;
            fm.vtype.value = vtype;

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
    </script>
</html>
