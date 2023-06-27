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

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = $today;



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

    $p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $_REQUEST['vtype'] : 'all');
    $p_data['srch_level'] = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

    
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    
    $p_data['vtype'] = $MEMAdminDAO->real_escape_string($p_data['vtype']);
    $p_data['srch_level'] = $MEMAdminDAO->real_escape_string($p_data['srch_level']);
    $p_data['srch_key'] = $MEMAdminDAO->real_escape_string($p_data['srch_key']);
    $p_data['srch_val'] = $MEMAdminDAO->real_escape_string($p_data['srch_val']);
    
    
    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":

            if ($p_data['srch_val'] != '') {
                $srch_basic = "  AND (b.id like '%" . $p_data['srch_val'] . "%' OR b.nick_name like '%" . $p_data['srch_val'] . "%') ";
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


    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);

    $p_data['srch_s_date'] = $MEMAdminDAO->real_escape_string($p_data['srch_s_date']);
    $p_data['srch_e_date'] = $MEMAdminDAO->real_escape_string($p_data['srch_e_date']);
    
    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';


    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM t_log_cash a ";
    $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";
    $p_data['sql'] .= " WHERE a.reg_time >= '" . $p_data['db_srch_s_date'] . "' AND  a.reg_time <= '" . $p_data['db_srch_e_date'] . "' AND ac_code = 103 ";
    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 8)) {
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
        $p_data['sql'] = " SELECT a.idx, a.member_idx, b.id, b.nick_name, a.reg_time, a.u_ip, a.a_country, b.last_login ";
        $p_data['sql'] .= " FROM t_log_cash a";
        $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";

        $p_data['sql'] .= " WHERE a.reg_time >= '" . $p_data['db_srch_s_date'] . "' AND  a.reg_time <= '" . $p_data['db_srch_e_date'] . "' AND ac_code = 103";
        if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 8)) {
            $p_data['sql'] .= "     AND b.level=" . $p_data['srch_level'] . " ";
        }

        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY reg_time DESC";
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
            $menu_name = "mem_list_account";

            echo $menu_name;
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
                        <h4>통장 로그 조회</h4>
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
                        <div class="panel_tit">
                            <div class="search_form fl">
<?php if ($p_data['vtype'] == 'node') {
    $all_btn = "btn_mdark";
    $node_btn = "btn_blu";
} else {
    $all_btn = "btn_blu";
    $node_btn = "btn_mdark";
} ?>
                                <div><a href="javascript:;" onClick="goSearch('all');" class="btn h30 <?= $all_btn ?>">전체보기</a></div>
                                <div><a href="javascript:;" onClick="goSearch('node');" class="btn h30 <?= $node_btn ?>">미입금</a></div>
                            </div>
                        </div>
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>번호</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>접속날짜</th>
                                <th>IP</th>
                                <th>접속국가</th>
                                <!--<th>접속도메인</th>
                                <th>충전횟수</th>
                                <th>조회횟수</th>
                                <th>입금액</th>
                                <th>충전금액</th>-->
                                <th>조회일시</th>
                            </tr>
<?php
if ($total_cnt > 0) {
    $i = 0;
    if (!empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
            //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
            $login_log = explode('|', true === isset($row[0]['login_data']) && false === empty($row[0]['login_data']) ? $row[0]['login_data'] : null);
            $db_m_idx = $row['member_idx'];
            $str_id_style = true === isset($str_id_style) && false === empty($str_id_style) ? $str_id_style : null;
            ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $row['member_idx'] ?></td>
                                            <td style='text-align:left; <?= $str_id_style ?>'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left; <?= $str_id_style ?>'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td style='text-align:right'><?= $row['u_ip'] ?></td>
                                            <td style='text-align:right'><?= $row['a_country'] ?></td>
                                            <td style='text-align:right'><?= $row['last_login'] ?></td>
                                            <!--<td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>-->
                                            <td style='text-align:right'><?= $row['reg_time'] ?></td>
                                        </tr>
                                    <?php
                                    $i++;
                                }
                            }
                        } else {
                            echo "<tr><td colspan='12'>데이터가 없습니다.</tr>";
                        }
                        ?>

                        </table>
<?php
$requri = explode('?', $_SERVER['REQUEST_URI']);
$reqFile = basename($requri[0]);
$default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "&srch_level=" . $p_data['srch_level'] . "";
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
        function setDate(sdate, edate) {
            var fm = document.search;

            fm.srch_s_date.value = sdate;
            fm.srch_e_date.value = edate;
        }

        function goSearch(vtype = null) {
            var fm = document.search;

            fm.vtype.value = vtype;

            if ((fm.srch_key.value != '') && (fm.srch_val.value == '')) {

                if (fm.srch_level.value < 1) {
                    //alert('검색어를 입력해 주세요.');
                    //fm.srch_val.focus();
                    //return;
                }
            }

            fm.method = "get";
            fm.submit();
        }
    </script>
</html>