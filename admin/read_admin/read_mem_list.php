<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end


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

$ob_no_change = $ob_level_change = $ob_money_change = $ob_point_change = $ob_betpoint_change = $ob_regdt_change = $ob_logindt_change = $ob_deposit_change = $ob_withdraw_change = $ob_cal_change = "";
$ob_no_color = "<font color='#444'>No</font>";
$ob_level_color = "<font color='#444'>레벨</font>";
$ob_money_color = "<font color='#444'>머니</font>";
$ob_point_color = "<font color='#444'>포인트</font>";
$ob_betpoint_color = "<font color='#444'>베팅 P</font>";
$ob_regdt_color = "<font color='#444'>가입일시</font>";
$ob_logindt_color = "<font color='#444'>최종 로그인 일자</font>";

$ob_deposit_color = "<font color='#444'>입금</font>";
$ob_withdraw_color = "<font color='#444'>출금</font>";
$ob_cal_color = "<font color='#444'>정산</font>";

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
    case "regdt":
        if ($p_data['s_ob_type'] == 'desc') {
            $p_data['sql_orderby'] = " ORDER BY a.idx DESC";
            $ob_regdt_color = "<font color='#0021FD'>가입일시</font>";
        } else {
            $ob_regdt_change = "desc";
            $p_data['sql_orderby'] = " ORDER BY a.idx ";
            $ob_regdt_color = "<font color='#FD0000'>가입일시</font>";
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
    case "deposit":
        if ($p_data['s_ob_type'] == 'desc') {
            $p_data['sql_orderby'] = " ORDER BY ch_sum_money DESC, a.idx DESC";
            $ob_deposit_color = "<font color='#0021FD'>입금</font>";
        } else {
            $ob_deposit_change = "desc";
            $p_data['sql_orderby'] = " ORDER BY ch_sum_money, a.idx DESC ";
            $ob_deposit_color = "<font color='#FD0000'>입금</font>";
        }
        break;
    case "withdraw":
        if ($p_data['s_ob_type'] == 'desc') {
            $p_data['sql_orderby'] = " ORDER BY ex_sum_money DESC, a.idx DESC";
            $ob_withdraw_color = "<font color='#0021FD'>출금</font>";
        } else {
            $ob_withdraw_change = "desc";
            $p_data['sql_orderby'] = " ORDER BY ex_sum_money, a.idx DESC ";
            $ob_withdraw_color = "<font color='#FD0000'>출금</font>";
        }
        break;
    case "cal":
        if ($p_data['s_ob_type'] == 'desc') {
            // $p_data['sql_orderby'] = " ORDER BY bet_prize_money DESC`, a.idx DESC " ;
            $p_data['sql_orderby'] = " ORDER BY gs DESC, a.idx DESC";
            $ob_cal_color = "<font color='#0021FD'>정산</font>";
        } else {
            $ob_cal_change = "desc";
            // $p_data['sql_orderby'] = " ORDER BY bet_prize_money, a.idx DESC " ;
            $p_data['sql_orderby'] = " ORDER BY gs, a.idx DESC";
            $ob_cal_color = "<font color='#FD0000'>정산</font>";
        }
        break;
    default:
        $p_data['sql_orderby'] = " ORDER BY a.idx DESC ";
        break;
}

$p_data['srch_level'] = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
$p_data['srch_status'] = trim(isset($_REQUEST['srch_status']) ? $_REQUEST['srch_status'] : 0);

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
            $srch_basic = " AND a.dis_line_id like '%" . $p_data['srch_val'] . "%' ";
        }
        break;
    case "s_dis":
        if ($p_data['srch_val'] != '') {
            $srch_basic = " AND a.dis_id like '%" . $p_data['srch_val'] . "%' ";
        }
        break;
    case "s_call":
        if ($p_data['srch_val'] != '') {
            $srch_basic = " AND a.call like '%" . $p_data['srch_val'] . "%' ";
        }
        break;
    case "s_account_number":
        if ($p_data['srch_val'] != '') {
            $srch_basic = " AND a.account_number like '%" . $p_data['srch_val'] . "%' ";
        }
        break;
}


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    $p_data["table_name"] = " member a ";
    $p_data["sql_where"] = " WHERE a.idx > 0 ";
    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
        $p_data['sql_where'] .= " AND a.level=" . $p_data['srch_level'] . " ";
    }

    // 맴버상태 추가
    if ($p_data['srch_status'] > 0) {
        $p_data['sql_where'] .= " AND a.status=" . $p_data['srch_status'] . " ";
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
        $p_data['sql'] .= ", a.status, a.level, a.auto_level, a.last_login, a.MICRO, a.AG, a.recommend_code, a.recommend_member ";
        $p_data['sql'] .= ", a.account_number, a.account_name, a.account_bank, a.is_monitor, a.is_monitor_charge, a.is_monitor_security ";
        $p_data['sql'] .= ", a.is_monitor_bet, a.dis_id, a.dis_line_id, a.reg_time ";
        $p_data['sql'] .= ", (SELECT b.id FROM member b WHERE b.idx=a.recommend_member) AS re_id ";
        $p_data['sql'] .= ", IFNULL((t_m_ch.charge_total_money + IFNULL(ch.money,0)),0) AS ch_sum_money ";
        $p_data['sql'] .= ", IFNULL((t_m_ch.exchange_total_money + IFNULL(ex.money,0)),0) AS ex_sum_money ";
        //$p_data['sql'] .= ", (SELECT round(SUM(e.take_money)) FROM member_bet e WHERE e.member_idx=a.idx AND e.bet_status=3) AS bet_prize_money ";
        $p_data['sql'] .= ", (
             IFNULL((t_m_ch.charge_total_money + IFNULL(ch.money,0)), 0)
            - 
             (IFNULL((t_m_ch.exchange_total_money + IFNULL(ex.money,0)),0) + a.money)
            ) AS gs
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
             ) as ex ON a.idx = ex.member_idx ";

        $p_data['sql'] .= $p_data["sql_where"];
        $p_data['sql'] .= $p_data["sql_orderby"];
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . ";";

        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
        CommonUtil::logWrite(" mem_list " . $p_data['sql'], "info");
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
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        
        <?php
        $menu_name = "mem_list";

        //include_once(_BASEPATH . '/common/left_menu.php');
        //include_once(_BASEPATH . '/common/iframe_head_menu.php');
        ?>
        <!-- Contents -->
        <body style=”overflow-x:auto;overflow-y:hidden”>
            
            <div class="read_head_wrap">
                
                <!-- TOP Area -->
                <div class="head_menu">
                    <ul>
                        <li>
                            <a href="javascript:goCallPage();" >
                                <i class="mte i_group vam"></i>신규 대기/승인
                                <p >(<span id="today_mem_cnt_wait" class="tred">0</span>/<span id="today_tot_mem_reg" class="tblue">0</span>)</p>
                            </a>
                            <a id="a_join" onclick="javascript:fnSetSound(this, 'a_join');">
                                <i style="color: blue;" class="mte i_volume_up vam"></i>
                                <audio id="audio_join" src="../assets_admin/audio/join.mp3">
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="head_btn">
                    <table>
                        <tr>
                            <td><a href="javascript:;" onclick="goLogout();"><i class="mte i_power_settings_new vam"></i>나가기</a></td>
                        </tr>
                    </table>
                </div>
                <div class="tinfo_wrap">
                    <?php
                    if (isset($_SESSION) && isset($_SESSION['anick'])) {
                        ?>
                        <div class="t_link"><?= $_SESSION['anick'] ?></div>
                    <?php } else { ?>
                        <div class="t_link">test</div>
                    <?php } ?>
                </div>
            </div>
        
            <!-- END TOP Area -->

            <div class="con_wrap read_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>회원 정보</h4>
                    </a>
                </div>

                <div class="panel search_box">
                    <h5><a href="read_mem_list.php?srch_status=1">정상회원 (<?= !empty($db_user_status_cnt[1]) ? number_format($db_user_status_cnt[1]) : 0 ?>)</a></h5>
                    <h5><a href="read_mem_list.php?srch_status=11">대기회원 (<?= !empty($db_user_status_cnt[11]) ? number_format($db_user_status_cnt[11]) : 0 ?>)</a></h5>
                    <h5><a href="read_mem_list.php?srch_status=3">탈퇴회원 (<?= !empty($db_user_status_cnt[3]) ? number_format($db_user_status_cnt[3]) : 0 ?>)</a></h5>
                    <h5><a href="read_mem_list.php?srch_status=2">정지회원 (<?= !empty($db_user_status_cnt[2]) ? number_format($db_user_status_cnt[2]) : 0 ?>)</a></h5>
                </div>


                <!-- list -->
                <div class="panel reserve">       
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="s_ob" value="<?= $p_data['s_ob'] ?>">
                        <input type="hidden" name="s_ob_type" value="<?= $p_data['s_ob_type'] ?>">
                        <input type="hidden" name="srch_status" value="<?= $p_data['srch_status'] ?>">
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_level" id="srch_level">
                                        <option value="" <?php
                                        if ($p_data['srch_level'] == '') {
                                            echo "selected";
                                        }
                                        ?>>레벨선택</option>
                                        <option value="1" <?php
                                        if ($p_data['srch_level'] == '1') {
                                            echo "selected";
                                        }
                                        ?>>1</option>
                                        <option value="2" <?php
                                        if ($p_data['srch_level'] == '2') {
                                            echo "selected";
                                        }
                                        ?>>2</option>
                                        <option value="3" <?php
                                        if ($p_data['srch_level'] == '3') {
                                            echo "selected";
                                        }
                                        ?>>3</option>
                                        <option value="4" <?php
                                        if ($p_data['srch_level'] == '4') {
                                            echo "selected";
                                        }
                                        ?>>4</option>
                                        <option value="5" <?php
                                        if ($p_data['srch_level'] == '5') {
                                            echo "selected";
                                        }
                                        ?>>5</option>
                                        <option value="6" <?php
                                        if ($p_data['srch_level'] == '6') {
                                            echo "selected";
                                        }
                                        ?>>6</option>
                                        <option value="7" <?php
                                        if ($p_data['srch_level'] == '7') {
                                            echo "selected";
                                        }
                                        ?>>7</option>
                                        <option value="8" <?php
                                        if ($p_data['srch_level'] == '8') {
                                            echo "selected";
                                        }
                                        ?>>8</option>
                                        <option value="9" <?php
                                        if ($p_data['srch_level'] == '9') {
                                            echo "selected";
                                        }
                                        ?>>9</option>
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
                                        <option value="s_dis" <?php
                                        if ($p_data['srch_key'] == 's_dis') {
                                            echo "selected";
                                        }
                                        ?>>총판</option>
                                        <option value="s_call" <?php
                                        if ($p_data['srch_key'] == 's_call') {
                                            echo "selected";
                                        }
                                        ?>>전화번호</option>
                                        <option value="s_account_number" <?php
                                        if ($p_data['srch_key'] == 's_account_number') {
                                            echo "selected";
                                        }
                                        ?>>계좌번호</option>
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
                                <th colspan="9">가입 정보</th>
                                <!-- <th colspan="3">머니/포인트</th>
                                <th colspan="4">정산 정보</th> -->
                                <th colspan="3">상태 정보</th>
                            </tr>
                            <tr>
                                <th>총판라인</th>
                                <th>총판</th>
                                <th>추천인</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>계좌번호</th>
                                <th>예금주</th>
                                <th>은행명</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('level', '<?= $ob_level_change ?>');"><?= $ob_level_color ?></a>
                                </th>
                                <th>상태</th>
                                <th>핸드폰</th>
                                <th>로그인</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('regdt', '<?= $ob_regdt_change ?>');"><?= $ob_regdt_color ?></a>
                                </th>
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

                                        if ($row['level'] == 9) {
                                            $row['ch_sum_money'] = 0;
                                            $row['ex_sum_money'] = 0;
                                            $row['gs'] = 0;
                                        }

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

                                        $u_business = $row['u_business'];
                                        // this.style.backgroundColor='#ffffff';
                                        $bgColor = '';
                                        if (1 != $u_business )
                                            $bgColor = '#32CD32';
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="javascript:onMouseOut(this, <?= $u_business ?>);" bgColor="<?= $bgColor ?>">
                                            <td ><?= $no ?></td>
                                            <td style='text-align:left'><?= $row['dis_line_id'] ?></td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" ><?= $db_dis_id ?></a>
                                            </td>
                                            <td style='text-align:left'><?= $row['re_id'] ?></td>
                                            <?php if (1 != $u_business) { ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/read_admin/read_pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/read_admin/read_pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['nick_name'] ?></a>
                                                </td>
                                            <?php } else { ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/read_admin/read_pop_userinfo.php', 'popuserinfo', 800, 1600, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/read_admin/read_pop_userinfo.php', 'popuserinfo', 800, 1600, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                                </td>
                                            <?php } ?>
                                            <td style='text-align:left'><?= $row['account_number'] ?></td>
                                            <td><?= $row['account_name'] ?></td> <!-- 예금주내용 -->
                                            <td><?= $row['account_bank'] ?></td> <!-- 은행명내용 -->
                                            <td><?= $row['level'] ?></td>
                                            <td><?= $db_status ?></td>
                                            <td><?= $row['call'] ?></td>
                                            <?php /*
                                              <td style='text-align:right'><?=number_format($row['ch_sum_money'] - ($row['ex_sum_money'] + $row['money']))?></td>
                                             */ ?>
                                            <td></td>
                                            <td><?= $row['reg_time'] ?></td>
                                            <td><?= $row['last_login'] ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                            } else {
                                ?>
                                <tr><td colspan="19">데이터가 없습니다.</td></tr>
                                <?php
                            }
                            ?>

                        </table>
                        <?php
                        $reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
                        $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "&srch_level=" . $p_data['srch_level'] . "";
                        $default_link .= "&s_ob=" . $p_data['s_ob'] . "&s_ob_type=" . $p_data['s_ob_type'] . "";
                        if ($p_data['srch_status'] > 0) {
                            $default_link .= " &srch_status=" . $p_data['srch_status'] . " ";
                        }
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

        $(document).ready(function () {

            App.init();
            FormPlugins.init();
            offJoinSound();

            $('ul.tabs li').click(function () {
                var tab_id = $(this).attr('data-tab');

                $('ul.tabs li').removeClass('current');
                $('.tab-content').removeClass('current');

                $(this).addClass('current');
                $("#" + tab_id).addClass('current');
            })
            // chrome 정책 우회 처리용
            $('#tot_mem_cnt').trigger('click');

            reLoadRequest(true);
            //window.setTimeout('window.location.reload()',30000); //30초마다 리플리쉬 시킨다 1000이 1초가 된다.
            var timerId = 0;
            timerId = setInterval("reLoadRequest()", 20000);

            let a_join = sessionStorage.getItem("a_join");
            if (a_join !== undefined && null != a_join) {
                let j_a_join = JSON.parse(a_join);

                let icon = $('#a_join').children()[0];
                let audio = $('#a_join').children()[1];

                if (true == j_a_join['is_on']) {
                    $(icon).attr('style', 'color: blue;');
                    $(icon).removeClass();
                    $(icon).attr('class', 'mte i_volume_up vam');

                    //$(audio).trigger('play');
                    audio.muted = false;
                    console.log("ready j_a_join play");

                } else {
                    $(icon).attr('style', 'color: red;');
                    $(icon).removeClass();
                    $(icon).attr('class', 'mte i_volume_off vam');

                    //$(audio).trigger('pause');
                    //$(audio).prop('currentTime', 0);
                    audio.muted = true;
                    console.log("ready j_a_join pause");
                }

            }


        });

        function reLoadRequest(is_load = false) {
            admNowCallRequest(is_load);
        }

        function admNowCallRequest(is_load = false) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/read_admin/_read_now_call_check_renew.php',
                success: function (result) {
                    if (result['retCode'] == "1000") {

                        document.getElementById("today_mem_cnt_wait").innerHTML = result['today_tot_mem_wait']; // 신규 대기
                        document.getElementById("today_tot_mem_reg").innerHTML = result['today_tot_mem_reg']; // 신규 승인 

                        // 신규가입 사운드
                        if (!is_load && 0 < Number(result['tot_mem_cnt_sound'])) {
                            var icon = $('#a_join').children()[0];
                            var audio = $('#a_join').children()[1];

                            if ($(icon).hasClass('i_volume_up')) {
                                $(audio).trigger('play');
                                console.log('admNowCallRequest a_join tot_mem_cnt_sound' + Number(result['tot_mem_cnt_sound']));
                            }
                        }

                        return;
                    } else if (result['retCode'] == "2001") {
                        parent.location.href = "/login.php?msg=1";
                    } else if (result['retCode'] == "2002") {
                        parent.location.href = "/login.php?msg=2";
                    } else {
                        return;
                    }
                },
                error: function (request, status, error) {

                    return;
                }
            });
        }
        function offJoinSound() {
            // 이때까지 올라온 신규가입 사운드 끄기처리
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/member_w/_mem_list_off_sound.php',
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
            fm.s_ob_type.srch_status = <?= $p_data['srch_status'] ?>;

            fm.method = "get";
            fm.submit();
        }

        function onMouseOut(obj, uBusiness) {
            if (uBusiness == 2 || uBusiness == 3) {
                obj.style.backgroundColor = '#32CD32';
            } else {
                obj.style.backgroundColor = '#ffffff';
            }
        }

        function goLogout() {
            var str_msg = "로그아웃 하시겠습니까?";
            var result = confirm(str_msg);

            if (result) {
                parent.location.href = "/login_w/logout.php";
            }
        }

        function fnSetSound(o, type) {
            var icon = $(o).children()[0];
            var audio = $(o).children()[1];

            if ($(icon).hasClass('i_volume_off')) {
                // sound on
                $(icon).attr('style', 'color: blue;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_up vam');

                $(audio).trigger('play');
                audio.muted = false;
                sessionStorage.removeItem(type);
                sessionStorage.setItem(type, JSON.stringify({is_on: true, object: o}));
                let data = sessionStorage.getItem(type);
                let j_data = JSON.parse(data);
                console.log('on_' + type, j_data);

            } else {
                // sound off
                $(icon).attr('style', 'color: red;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_off vam');

                $(audio).trigger('pause');
                $(audio).prop('currentTime', 0);
                audio.muted = true;
                sessionStorage.removeItem(type);
                sessionStorage.setItem(type, JSON.stringify({is_on: false, object: o}));

                let data = sessionStorage.getItem(type);
                let j_data = JSON.parse(data);
                console.log('off_' + type, j_data);
            }


        }
        
        function goCallPage() {
            window.location.href='/read_admin/read_mem_list.php?srch_status=11&srch_level=1';
        }

    </script>
</html>