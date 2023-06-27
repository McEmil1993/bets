<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

if(0 != $_SESSION['u_business']){
    die();
}


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

    $ob_no_change = $ob_level_change = $ob_money_change = $ob_point_change = $ob_betpoint_change = $ob_regdt_change = $ob_logindt_change = "";
    $ob_no_color = "<font color='#444'>No</font>";
    $ob_level_color = "<font color='#444'>레벨</font>";
    $ob_money_color = "<font color='#444'>머니</font>";
    $ob_point_color = "<font color='#444'>포인트</font>";
    $ob_betpoint_color = "<font color='#444'>베팅 P</font>";
    $ob_logindt_color = "<font color='#444'>최종 로그인 일자</font>";

    switch ($p_data['s_ob']) {
        case "no":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.idx DESC ";
                $ob_no_color = "<font color='#0021FD'>No</font>";
            } else {
                $ob_no_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.idx ";
                $ob_no_color = "<font color='#FD0000'>No</font>";
            }
            break;
        case "level":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.level DESC, a.idx DESC";
                $ob_level_color = "<font color='#0021FD'>레벨</font>";
            } else {
                $ob_level_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.level, a.idx DESC ";
                $ob_level_color = "<font color='#FD0000'>레벨</font>";
            }
            break;
        case "money":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.money DESC, a.idx DESC";
                $ob_money_color = "<font color='#0021FD'>머니</font>";
            } else {
                $ob_money_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.money, a.idx DESC ";
                $ob_money_color = "<font color='#FD0000'>머니</font>";
            }
            break;
        case "point":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.point DESC, a.idx DESC";
                $ob_point_color = "<font color='#0021FD'>포인트</font>";
            } else {
                $ob_point_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.point, a.idx DESC ";
                $ob_point_color = "<font color='#FD0000'>포인트</font>";
            }
            break;
        case "betpoint":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.betting_p DESC, a.idx DESC";
                $ob_betpoint_color = "<font color='#0021FD'>베팅 P</font>";
            } else {
                $ob_betpoint_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.betting_p, a.idx DESC ";
                $ob_betpoint_color = "<font color='#FD0000'>베팅 P</font>";
            }
            break;
        case "logindt":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.last_login DESC, a.idx DESC";
                $ob_logindt_color = "<font color='#0021FD'>최종 로그인 일자</font>";
            } else {
                $ob_logindt_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.last_login, a.idx DESC ";
                $ob_logindt_color = "<font color='#FD0000'>최종 로그인 일자</font>";
            }
            break;
        default:
            $p_data['sql_orderby'] = " ORDER BY a.idx DESC ";
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
                $srch_basic = " AND (a.id like '%" . $p_data['srch_val'] . "%' OR a.nick_name like '%" . $p_data['srch_val'] . "%') ";
            }
            break;
        case "s_accountname":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND a.account_name like '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_disline":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND a.dis_line_id='" . $p_data['srch_val'] . "' ";
            }
            break;
        case "s_call":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND a.call like '%" . $p_data['srch_val'] . "%' ";
            }
            break;
    }

    $p_data["table_name"] = " member a ";
    $p_data["sql_where"] = " where a.idx > 0 and a.is_monitor='Y' AND a.u_business = ".GENERAL;
    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
        $p_data['sql_where'] .= " AND a.level=" . $p_data['srch_level'] . " ";
    }
    $p_data['sql_where'] .= $srch_basic;
    $p_data['sql'] .= $p_data['sql_where'];

    $db_total_cnt = $MEMAdminDAO->getTotalCount($p_data);

    $total_cnt = $db_total_cnt[0]['CNT'];

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
        $p_data['sql'] = "SELECT a.idx, a.id, a.nick_name, a.u_business, a.money, a.point, a.betting_p, a.is_recommend, a.call ";
        $p_data['sql'] .= ", pt.id as p_id, pt.u_business as p_bu, ppt.id as pp_id, ppt.u_business as pp_bu, pppt.id as ppp_id, pppt.u_business as ppp_bu ";
        $p_data['sql'] .= ", a.status, a.level, a.auto_level, a.last_login, a.MICRO, a.AG, a.recommend_code, a.recommend_member ";
        $p_data['sql'] .= ", a.account_number, a.account_name, a.account_bank, a.is_monitor, a.is_monitor_charge, a.is_monitor_security ";
        $p_data['sql'] .= ", a.is_monitor_bet, a.dis_id, a.dis_line_id, a.reg_time ";
        $p_data['sql'] .= ", (SELECT b.id FROM member b WHERE b.idx=a.recommend_member) AS re_id ";
        $p_data['sql'] .= ", IFNULL((IFNULL(t_m_ch.charge_total_money,0) + IFNULL(ch.money,0)),0) AS ch_sum_money ";
        $p_data['sql'] .= ", IFNULL((IFNULL(t_m_ch.exchange_total_money,0) + IFNULL(ex.money,0)),0) AS ex_sum_money ";
        //$p_data['sql'] .= ", (SELECT round(SUM(e.total_bet_money)) FROM member_bet e WHERE e.member_idx=a.idx AND e.bet_status=3) AS bet_prize_money ";
        $p_data['sql'] .= ", (
             IFNULL((IFNULL(t_m_ch.charge_total_money,0) + IFNULL(ch.money,0)), 0)
            - 
             (IFNULL((IFNULL(t_m_ch.exchange_total_money,0) + IFNULL(ex.money,0)),0) + a.money)
            ) AS bet_prize_money
            ";

        $p_data['sql'] .= " FROM member a ";
        $p_data['sql'] .= " left join total_member_cash as t_m_ch ON a.idx = t_m_ch.member_idx ";
        $p_data['sql'] .= " left join ( SELECT IFNULL(SUM(c.money),0) as money,c.member_idx FROM member_money_charge_history c 
				left join member t1 on c.member_idx=t1.idx 
             WHERE  c.status=3 AND c.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and c.update_dt <= NOW()
             group by t1.idx
             ) as ch ON a.idx = ch.member_idx ";

        $p_data['sql'] .= "  left join ( SELECT IFNULL(SUM(e.money),0) as money,e.member_idx FROM member_money_exchange_history e 
             left join member t1 on e.member_idx=t1.idx 
             WHERE e.status=3 and e.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and e.update_dt <= NOW()
             group by t1.idx
             ) as ex ON a.idx = ex.member_idx 
                left join member pt on a.recommend_member = pt.idx and pt.u_business != 1  
                left join member ppt on pt.recommend_member = ppt.idx and ppt.u_business != 1
                left join member pppt on ppt.recommend_member = pppt.idx and pppt.u_business != 1
             ";

        $p_data['sql'] .= $p_data["sql_where"];
        $p_data['sql'] .= $p_data["sql_orderby"];
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . ";";

        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    }

    $p_data['sql'] = "SELECT status, COUNT(*) as cnt FROM member GROUP BY STATUS ; ";
    $db_dataArrUserStatus = $MEMAdminDAO->getQueryData($p_data);

    if (!empty($db_dataArrUserStatus)) {
        foreach ($db_dataArrUserStatus as $row) {
            switch ($row['status']) {
                case 1: $db_user_status_cnt[1] = $row['cnt'];
                    break;
                case 2: $db_user_status_cnt[2] = $row['cnt'];
                    break;
                case 3: $db_user_status_cnt[3] = $row['cnt'];
                    break;
                case 11: $db_user_status_cnt[11] = $row['cnt'];
                    break;
            }
        }
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
            $menu_name = "mem_list_normal";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>일반 모니터링 회원</h4>
                    </a>
                </div>

                <!-- detail search -->
                <div class="panel search_box">
                    <h5>일반회원 (<?= !empty($db_user_status_cnt[1]) ? number_format($db_user_status_cnt[1]) : 0 ?>)</h5>
                    <h5>정지회원 (<?= !empty($db_user_status_cnt[2]) ? number_format($db_user_status_cnt[2]) : 0 ?>)</h5>
                    <h5>탈퇴회원 (<?= !empty($db_user_status_cnt[3]) ? number_format($db_user_status_cnt[3]) : 0 ?>)</h5>
                    <h5>대기회원 (<?= !empty($db_user_status_cnt[11]) ? number_format($db_user_status_cnt[11]) : 0 ?>)</h5>
                </div>
                <!-- END detail search -->

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
                                            <option value="<?= $n ?>" <?php
                                            if ($p_data['srch_level'] == $n) {
                                                echo "selected";
                                            }
                                            ?>><?= $n ?></option>
<?php } ?>
                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick" <?php
if ($p_data['srch_key'] == 's_idnick') {
    echo "selected";
}
?>>아이디 및 닉네임</option>
                                        <option value="s_accountname" <?php
                                                if ($p_data['srch_key'] == 's_accountname') {
                                                    echo "selected";
                                                }
                                                ?>>예금주</option>
                                        <option value="s_disline" <?php
                                        if ($p_data['srch_key'] == 's_disline') {
                                            echo "selected";
                                        }
                                                ?>>총판라인</option>
                                        <option value="s_call" <?php
                                        if ($p_data['srch_key'] == 's_call') {
                                            echo "selected";
                                        }
                                                ?>>전화번호</option>
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
                                <th rowspan="2">
                                    <a href="javascript:;" onClick="goOrderby('no', '<?= $ob_no_change ?>');"><?= $ob_no_color ?></a>
                                </th>
                                <th colspan="3">추천인</th>
                                <th colspan="5">가입 정보</th>
                                <th colspan="3">머니/포인트</th>
                                <th colspan="3">정산 정보</th>
                                <th colspan="2">상태 정보</th>
                                <th rowspan="2">관리</th>
                            </tr>
                            <tr>
                                <th>대총판</th>
                                <th>총판</th>
                                <th>하부총판</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('level', '<?= $ob_level_change ?>');"><?= $ob_level_color ?></a>
                                </th>
                                <th>상태</th>
                                <th>핸드폰</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('money', '<?= $ob_money_change ?>');"><?= $ob_money_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('point', '<?= $ob_point_change ?>');"><?= $ob_point_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('betpoint', '<?= $ob_betpoint_change ?>');"><?= $ob_betpoint_color ?></a>
                                </th>
                                <th>입금</th>
                                <th>출금</th>
                                <th>정산</th>
                                <th>로그인</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('logindt', '<?= $ob_logindt_change ?>');"><?= $ob_logindt_color ?></a>
                                </th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;

                                        $db_m_idx = $row['idx'];
                                        $db_dis_id = $row['dis_id'];
                                        $db_status = "";
                                        switch ($row['status']) {
                                            case 1: $db_status = "사용중";
                                                break;
                                            case 2: $db_status = "정지";
                                                break;
                                            case 3: $db_status = "탈퇴";
                                                break;
                                            case 11: $db_status = "대기";
                                                break;
                                        }

                                        $no = $total_cnt - $num;

                                        if ($p_data['s_ob'] == "no") {
                                            if ($p_data['s_ob_type'] == 'desc') {
                                                $no = $total_cnt - $num;
                                            } else {
                                                $no = $num + 1;
                                            }
                                        }
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td ><?= $no ?></td>
                                            <!-- 추천인 표기 -->
                                            <?php if(null == $row['p_id'] && 1 != $row['u_business']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                                <td style='text-align:left'></td>
                                                <td style='text-align:left'></td>
                                            <?php }else if(TOP_DISTRIBUTOR == $row['p_bu']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                                <td style='text-align:left'></td>
                                            <?php }else if(DISTRIBUTOR == $row['p_bu']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['pp_id'] ?>');"><?= $row['pp_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                            <?php }else if(1 != $row['p_bu']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['ppp_id'] ?>');"><?= $row['ppp_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['pp_id'] ?>');"><?= $row['pp_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                </td>
                                            <?php }else{ ?>
                                                <td style='text-align:left'></td>
                                                <td style='text-align:left'></td>
                                                <td style='text-align:left'></td>
                                            <?php } ?>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td><?= $row['level'] ?></td>
                                            <td><?= $db_status ?></td>
                                            <td><?= $row['call'] ?></td>
                                            <td style='text-align:right'><?= number_format($row['money']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['point']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['betting_p']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['ch_sum_money']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['ex_sum_money']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['bet_prize_money']) ?></td>
                                            <td></td>
                                            <td><?= $row['last_login'] ?></td>
                                            <td>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');">로그</a>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php', 'popmsg', 660, 1000, 'msg', '<?= $db_m_idx ?>');">쪽지</a>
                                            </td>
                                        </tr>
            <?php
            $i++;
        }
    }
} else {
    ?>
                                <tr><td colspan="18">데이터가 없습니다.</td></tr>
                            <?php
                        }
                        ?>                    

                        </table>
<?php
$reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "&srch_level=" . $p_data['srch_level'] . "";
$default_link .= "&s_ob=" . $p_data['s_ob'] . "&s_ob_type=" . $p_data['s_ob_type'] . "";
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