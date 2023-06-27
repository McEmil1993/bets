<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }


    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = 50;
    }

    $p_data['s_ob'] = trim(isset($_REQUEST['s_ob']) ? $_REQUEST['s_ob'] : '');
    $p_data['s_ob_type'] = trim(isset($_REQUEST['s_ob_type']) ? $_REQUEST['s_ob_type'] : 'desc');

    
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
  
    $p_data['s_ob'] = $MEMAdminDAO->real_escape_string($p_data['s_ob']);
    $p_data['s_ob_type'] = $MEMAdminDAO->real_escape_string($p_data['s_ob_type']);

    $ob_delcnt_change = $ob_recnt_change = $ob_betpoint_change = $ob_point_change = "";
    $ob_delcnt_color = "<font color='#444'>탈퇴수</font>";
    $ob_recnt_color = "<font color='#444'>추천수</font>";
    $ob_betpoint_color = "<font color='#444'>받은 낙첨 포인트</font>";
    $ob_point_color = "<font color='#444'>현재 보유 포인트</font>";

    switch ($p_data['s_ob']) {
        case "delcnt":
            if ($p_data['s_ob_type'] == 'desc') {
                //$p_data['sql_orderby'] = " ORDER BY a.create_dt DESC, a.idx DESC" ;
                $p_data['sql_orderby'] = " ORDER BY delcnt DESC";
                $ob_delcnt_color = "<font color='#0021FD'>탈퇴수</font>";
            } else {
                $ob_delcnt_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY delcnt ";
                $ob_delcnt_color = "<font color='#FD0000'>탈퇴수</font>";
            }
            break;
        case "recnt":
            if ($p_data['s_ob_type'] == 'desc') {
                //$p_data['sql_orderby'] = " ORDER BY a.create_dt DESC, a.idx DESC" ;
                $p_data['sql_orderby'] = " ORDER BY recnt DESC";
                $ob_recnt_color = "<font color='#0021FD'>추천수</font>";
            } else {
                $ob_recnt_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY recnt ";
                $ob_recnt_color = "<font color='#FD0000'>추천수</font>";
            }
            break;
        case "betpoint":
            if ($p_data['s_ob_type'] == 'desc') {
                //$p_data['sql_orderby'] = " ORDER BY a.create_dt DESC, a.idx DESC" ;
                $p_data['sql_orderby'] = " ORDER BY b.betting_p DESC";
                $ob_betpoint_color = "<font color='#0021FD'>받은 낙첨 포인트</font>";
            } else {
                $ob_betpoint_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY b.betting_p ";
                $ob_betpoint_color = "<font color='#FD0000'>받은 낙첨 포인트</font>";
            }
            break;
        case "point":
            if ($p_data['s_ob_type'] == 'desc') {
                //$p_data['sql_orderby'] = " ORDER BY a.create_dt DESC, a.idx DESC" ;
                $p_data['sql_orderby'] = " ORDER BY sum_point DESC";
                $ob_point_color = "<font color='#0021FD'>현재 보유 포인트</font>";
            } else {
                $ob_point_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY sum_point ";
                $ob_point_color = "<font color='#FD0000'>현재 보유 포인트</font>";
            }
            break;
        default:
            $p_data['sql_orderby'] = " ";
            break;
    }

    $p_data['srch_level'] = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

    $p_data['srch_level'] = $MEMAdminDAO->real_escape_string($p_data['srch_level']);
    $p_data['srch_key'] = $MEMAdminDAO->real_escape_string($p_data['srch_key']);
    $p_data['srch_val'] = $MEMAdminDAO->real_escape_string($p_data['srch_val']);
    
    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":

            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND (b.id='" . $p_data['srch_val'] . "' OR b.nick_name='" . $p_data['srch_val'] . "') ";
            }
            break;
        case "s_accountname":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND b.account_name like '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_disline":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND b.dis_line_id='" . $p_data['srch_val'] . "' ";
            }
            break;
    }


    $p_data['sql'] = " SELECT COUNT(DISTINCT(b.id)) AS CNT FROM member a, member b ";
    $p_data['sql'] .= " WHERE a.recommend_member = b.idx AND a.recommend_member IS NOT NULL ";

    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
        $p_data['sql'] .= "     AND b.level=" . $p_data['srch_level'] . " ";
    }
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

        $p_data['sql'] = "SELECT b.idx, b.id, COUNT(*) AS recnt, b.nick_name, b.point, b.betting_p, (b.point + b.betting_p) as sum_point, b.dis_id ";
        $p_data['sql'] .= ", (SELECT nick_name FROM member WHERE id=b.dis_id) AS dis_nick ";
        $p_data['sql'] .= ", (SELECT idx FROM member WHERE id=b.dis_id) AS dis_idx ";
        $p_data['sql'] .= ", (SELECT COUNT(*) FROM member c WHERE c.recommend_member=b.idx AND c.status=3) AS delcnt ";
        $p_data['sql'] .= " FROM member a, member b";
        $p_data['sql'] .= " WHERE a.recommend_member = b.idx AND a.recommend_member IS NOT NULL ";
        if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
            $p_data['sql'] .= "     AND b.level=" . $p_data['srch_level'] . " ";
        }
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " GROUP BY b.id ";
        $p_data['sql'] .= $p_data['sql_orderby'];
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
        <div class="wrap">
<?php
$menu_name = "mem_list_recomm";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>추천회원 정보</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="s_ob" value="<?= $p_data['s_ob'] ?>">
                        <input type="hidden" name="s_ob_type" value="<?= $p_data['s_ob_type'] ?>">
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_level" id="srch_level">
                                        <option value="0">전체레벨</option>
<?php for ($n = 1; $n <= 10; $n++) { ?>
                                            <option value="<?= $n ?>" <?php if ($p_data['srch_level'] == $n) {
        echo "selected";
    } ?>><?= $n ?></option>
<?php } ?>
                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick" <?php if ($p_data['srch_key'] == 's_idnick') {
    echo "selected";
} ?>>아이디 및 닉네임</option>
                                        <option value="s_accountname" <?php if ($p_data['srch_key'] == 's_accountname') {
    echo "selected";
} ?>>예금주</option>
                                        <option value="s_disline" <?php if ($p_data['srch_key'] == 's_disline') {
    echo "selected";
} ?>>총판라인</option>
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
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>No</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>상위 아이디</th>
                                <th>상위 닉네임</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('recnt', '<?= $ob_recnt_change ?>');"><?= $ob_recnt_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('delcnt', '<?= $ob_delcnt_change ?>');"><?= $ob_delcnt_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('betpoint', '<?= $ob_betpoint_change ?>');"><?= $ob_betpoint_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('point', '<?= $ob_point_change ?>');"><?= $ob_point_color ?></a>
                                </th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                                        $db_m_idx = $row['idx'];
                                        $db_dis_idx = $row['dis_idx'];

                                        $db_point = $row['point'] + $row['betting_p'];
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $total_cnt - $num ?></td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_dis_idx ?>');"><?= $row['dis_id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_dis_idx ?>');"><?= $row['dis_nick'] ?></a>
                                            </td>
                                            <td style='text-align:right'><?= number_format($row['recnt']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['delcnt']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['betting_p']) ?></td>
                                            <td style='text-align:right'><?= number_format($db_point) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                            } else {
                                ?>
                                <tr><td colspan="9">데이터가 없습니다.</td></tr>
                                <?php
                            }
                            ?>

                        </table>
                        <?php
                        $reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
                        $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "&srch_level=" . $p_data['srch_level'] . "";
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

        function goSearch(vtype = null) {
            var fm = document.search;

            if ((fm.srch_key.value != '') && (fm.srch_val.value == '')) {
                //alert('검색어를 입력해 주세요.');
                //fm.srch_val.focus();
                //return;
            }

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
</html>