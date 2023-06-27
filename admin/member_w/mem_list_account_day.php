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

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = $today;


$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);

$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';

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

    $p_data['srch_level'] = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":

            if ($p_data['srch_val'] != '') {
                $srch_basic = "  AND (m.id='" . $p_data['srch_val'] . "' OR m.nick_name='" . $p_data['srch_val'] . "') ";
            }
            break;
        case "s_accountname":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND m.account_name like '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_disline":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND m.dis_line_id='" . $p_data['srch_val'] . "' ";
            }
            break;
    }

    $p_data['sql'] = " SELECT dt, charge_cnt, amoney, ";
    $p_data['sql'] .= "     (SELECT COUNT(*) FROM member_money_charge_history c WHERE DATE(c.update_dt) = b.dt) AS charge_tot "; // 잘못가져오고 있다 수정필요 
    $p_data['sql'] .= " FROM ( ";
    $p_data['sql'] .= "     SELECT DATE(a.update_dt) as dt, COUNT(*) AS charge_cnt, SUM(a.money) AS amoney ";
    $p_data['sql'] .= "     FROM member_money_charge_history a LEFT JOIN member m ON a.member_idx=m.idx ";
    $p_data['sql'] .= "     WHERE a.STATUS IN (3) ";
    $p_data['sql'] .= "     AND a.update_dt >= '" . $p_data['db_srch_s_date'] . "' AND  a.update_dt <= '" . $p_data['db_srch_e_date'] . "' ";
    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
        $p_data['sql'] .= "     AND m.level=" . $p_data['srch_level'] . " ";
    }

    if ($p_data['srch_val'] != '') {
        $p_data['sql'] .= $srch_basic;
    }

    $p_data['sql'] .= "     GROUP BY DATE(a.create_dt) ";
    $p_data['sql'] .= " ) b ORDER BY dt desc ";

    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);

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
    <body>
        <div class="wrap">
            <?php
            $menu_name = "mem_list_account_day";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>통장 조회 날짜별 로그</h4>
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
                        </div>
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>날짜</th>
                                <th>충전 횟수</th>
                                <th>조회 횟수</th>
                                <th>충전 금액</th>
                            </tr>
                            <?php
                            if (!empty($db_dataArr)) {
                                foreach ($db_dataArr as $row) {
                                    ?>
                                    <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                        <td><?= $row['dt'] ?></td>
                                        <td style='text-align:right'><?= number_format($row['charge_cnt']) ?></td>
                                        <td style='text-align:right'><?= number_format($row['charge_tot']) ?></td>
                                        <td style='text-align:right'><?= number_format($row['amoney']) ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr><td colspan="4">데이터가 없습니다.</td></tr>
                                <?php
                            }
                            ?>

                        </table>
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

        function goSearch() {
            var fm = document.search;

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