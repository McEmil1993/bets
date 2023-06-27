<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end
include_once(_LIBPATH . '/class_Code.php');

if (!isset($_SESSION)) {
    session_start();
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

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
        
    $p_data['srch_level'] = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
    $p_data['srch_status'] = trim(isset($_REQUEST['srch_status']) ? $_REQUEST['srch_status'] : 0);

    $p_data['srch_level']  = htmlspecialchars($p_data['srch_level'], ENT_QUOTES);
    $p_data['srch_key']    = htmlspecialchars($p_data['srch_key'], ENT_QUOTES);
    $srch_val    = htmlspecialchars($p_data['srch_val'], ENT_QUOTES);
    $p_data['srch_status'] = htmlspecialchars($p_data['srch_status'], ENT_QUOTES);
    
    $srch_val = '%'.$p_data['srch_val'].'%';
    

    $ob_no_change = $ob_level_change = $ob_money_change = $ob_g_money_change = $ob_point_change = $ob_betpoint_change = $ob_regdt_change = $ob_logindt_change = $ob_deposit_change = $ob_withdraw_change = $ob_cal_change = "";
    $ob_no_color = "<font color='#444'>No</font>";
    $ob_level_color = "<font color='#444'>레벨</font>";
    $ob_money_color = "<font color='#444'>머니</font>";
    $ob_g_money_color = "<font color='#444'>G머니</font>";
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
        case "g_money":
            if ($p_data['s_ob_type'] == 'desc') {
                $p_data['sql_orderby'] = " ORDER BY a.g_money DESC, a.idx DESC";
                $ob_g_money_color = "<font color='#0021FD'>G머니</font>";
            } else {
                $ob_g_money_change = "desc";
                $p_data['sql_orderby'] = " ORDER BY a.g_money, a.idx DESC ";
                $ob_g_money_color = "<font color='#FD0000'>G머니</font>";
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

  
    
    
    $srch_basic = "";
    $param_srch = array();
    switch ($p_data["srch_key"]) {
        case "s_idnick":

            if ($p_data['srch_val'] != '') {
                //$srch_basic = " AND (a.id like '%" . $p_data['srch_val'] . "%' OR a.nick_name like '%" . $p_data['srch_val'] . "%') ";
                $srch_basic = " AND (a.id like ? OR a.nick_name like ?) ";
                array_push($param_srch,$srch_val,$srch_val); 
            }
            break;
        case "s_accountname":
            if ($p_data['srch_val'] != '') {
                //$srch_basic = " AND a.account_name like '%" . $p_data['srch_val'] . "%' ";
                $srch_basic = " AND a.account_name like ? ";
                array_push($param_srch,$srch_val); 
            }
            break;
        case "s_disline":
            if ($p_data['srch_val'] != '') {
                //$srch_basic = " AND a.dis_line_id like '%" . $p_data['srch_val'] . "%' ";
                $srch_basic = " AND a.dis_line_id like ? ";
                array_push($param_srch,$srch_val); 
            }
            break;
        case "s_dis":
            if ($p_data['srch_val'] != '') {
                //$srch_basic = " AND a.dis_id like '%" . $p_data['srch_val'] . "%' ";
                $srch_basic = " AND a.dis_id like ? ";
                array_push($param_srch,$srch_val); 
            }
            break;
        case "s_call":
            if ($p_data['srch_val'] != '') {
                //$srch_basic = " AND a.call like '%" . $p_data['srch_val'] . "%' ";
                $srch_basic = " AND a.call like ? ";
                array_push($param_srch,$srch_val); 
            }
            break;
        case "s_account_number":
            if ($p_data['srch_val'] != '') {
                //$srch_basic = " AND a.account_number like '%" . $p_data['srch_val'] . "%' ";
                $srch_basic = " AND a.account_number like ? ";
                array_push($param_srch,$srch_val); 
            }
            break;
    }
    
    $param_where = array();
    $p_data["sql_where"] = '';
    if($_SESSION['u_business'] > 0){
        list($param_dist,$str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'],$MEMAdminDAO);
        $p_data["sql_where"] .= "and (a.recommend_member in(".$str_param_qm."))";
        $param_where = array_merge($param_where,$param_dist);
    }
    
    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
        //$p_data['sql_where'] .= " AND a.level=" . $p_data['srch_level'] . " ";
        $p_data['sql_where'] .= " AND a.level = ? ";
        array_push($param_where,$p_data['srch_level']); 
    }

    // 맴버상태 추가
    if ($p_data['srch_status'] > 0) {
        //$p_data['sql_where'] .= " AND a.status=" . $p_data['srch_status'] . " ";
        $p_data['sql_where'] .= " AND a.status= ? ";
        array_push($param_where,$p_data['srch_status']); 
    }
    
    // 총판 제외
    $p_data['sql_where'] .= " AND a.u_business = ".GENERAL;
    
    $p_data["sql"] = "SELECT COUNT(*) AS CNT FROM member a WHERE 1 = 1 ";
    $p_data['sql'] .= $srch_basic;
    $p_data['sql'] .= $p_data['sql_where'];

    $param = array();
    $param = array_merge($param_srch,$param_where);
    
    //CommonUtil::logWrite(" mem_list 1 srch_basic " . $srch_basic, "info");
    
    //CommonUtil::logWrite(" mem_list 1 " . $p_data['sql'], "info");
    
    //CommonUtil::logWrite(" mem_list param " . json_encode($param), "info");
    
    //$db_total_cnt = $MEMAdminDAO->getTotalCount($p_data['sql'],$param);
    $db_total_cnt = $MEMAdminDAO->getQueryData_pre($p_data['sql'],$param);
    
    $total_cnt = $db_total_cnt[0]['CNT'];
    //CommonUtil::logWrite(" mem_list param total_cnt " .$total_cnt, "info");
    
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
        $p_data['sql'] .= ", a.is_monitor_bet, a.dis_id, a.dis_line_id, a.reg_time, a.g_money ";
        $p_data['sql'] .= ", (SELECT b.id FROM member b WHERE b.idx=a.recommend_member) AS re_id ";
        $p_data['sql'] .= ", IFNULL((IFNULL(t_m_ch.charge_total_money,0) + IFNULL(ch.money,0)),0) AS ch_sum_money ";
        $p_data['sql'] .= ", IFNULL((IFNULL(t_m_ch.exchange_total_money,0) + IFNULL(ex.money,0)),0) AS ex_sum_money ";
        $p_data['sql'] .= ", (
             IFNULL((IFNULL(t_m_ch.charge_total_money,0) + IFNULL(ch.money,0)), 0)
            - 
             (IFNULL((IFNULL(t_m_ch.exchange_total_money,0) + IFNULL(ex.money,0)),0) + a.money)
            ) AS gs
            ";
        $p_data['sql'] .= " FROM member a ";
        $p_data['sql'] .= " left join total_member_cash as t_m_ch ON a.idx = t_m_ch.member_idx ";
        $p_data['sql'] .= " left join ( SELECT IFNULL(SUM(c.money),0) as money,c.member_idx FROM member_money_charge_history c 
			    left join member t1 on c.member_idx=t1.idx 
                                WHERE  c.status=3 AND c.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and c.update_dt <= NOW()
                                group by t1.idx
                                ) as ch ON a.idx = ch.member_idx ";

        $p_data['sql'] .= " left join ( SELECT IFNULL(SUM(e.money),0) as money,e.member_idx FROM member_money_exchange_history e 
             left join member t1 on e.member_idx=t1.idx 
             WHERE e.status=3 and e.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and e.update_dt <= NOW()
             group by t1.idx
             ) as ex ON a.idx = ex.member_idx  
            
            left join member pt on a.recommend_member = pt.idx and pt.u_business != 1  
            left join member ppt on pt.recommend_member = ppt.idx and ppt.u_business != 1
            left join member pppt on ppt.recommend_member = pppt.idx and pppt.u_business != 1
            WHERE 1 = 1 ";

        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= $p_data["sql_where"];
        $p_data['sql'] .= $p_data["sql_orderby"];
        $p_data['sql'] .= " LIMIT ? ,? ";

        $param = array_merge($param_srch,$param_where,[$p_data['start'],$p_data['num_per_page']]);
        $db_dataArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'],$param);
        CommonUtil::logWrite(" mem_list  sql" . $p_data['sql'], "info");
        CommonUtil::logWrite(" mem_list param" . json_encode($param), "info");
        //CommonUtil::logWrite(" mem_list  db_dataArr" . json_encode($db_dataArr), "info");
        
        
    }

    $p_data['sql'] = "SELECT status, COUNT(*) as cnt FROM member ";
    $param = [];
    if($_SESSION['u_business'] > 0){
        array_push($param,$_SESSION['member_idx'],$_SESSION['member_idx']);
        $p_data["sql"] .= "where recommend_member = ? or idx = ? ";
    }
    $p_data['sql'] .= " GROUP BY STATUS";
    
    $db_dataArrUserStatus = $MEMAdminDAO->getQueryData_pre($p_data['sql'],$param);

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
            offJoinSound();

            $('ul.tabs li').click(function () {
                var tab_id = $(this).attr('data-tab');

                $('ul.tabs li').removeClass('current');
                $('.tab-content').removeClass('current');

                $(this).addClass('current');
                $("#" + tab_id).addClass('current');
            })
        });
    </script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js?v=<?php echo date("YmdHis"); ?>"></script>
    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "mem_list";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>회원 정보</h4>
                    </a>
                </div>

                <!-- detail search -->
                <div class="panel search_box">
                    <h5><a href="mem_list.php?srch_status=1">일반회원 (<?= number_format(true === isset($db_user_status_cnt[1]) ? $db_user_status_cnt[1] : 0) ?>)</a></h5>
                    
                    <h5><a href="mem_list.php?srch_status=11">대기회원 (<?= number_format(true === isset($db_user_status_cnt[11]) ? $db_user_status_cnt[11] : 0) ?>)</a></h5>
                    <h5><a href="mem_list.php?srch_status=2">정지회원 (<?= empty($db_user_status_cnt[2]) ? '0' : number_format(true === isset($db_user_status_cnt[2]) ? $db_user_status_cnt[2] : 0) ?>)</a></h5>
                    <h5><a href="mem_list.php?srch_status=3">탈퇴회원 (<?= empty($db_user_status_cnt[3]) ? '0' : number_format(true === isset($db_user_status_cnt[3]) ? $db_user_status_cnt[3] : 0) ?>)</a></h5>
                    <div class="fr">
                        <span><a href="javascript:;" class="btn h30 btn_green btn__excel_down">전체회원 엑셀 다운로드</a></span> 
                    </div>
                </div>
                <!-- END detail search -->

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

                                <div>
                                    <input type="text" name="srch_val" id="srch_val"  placeholder="검색" value="<?= $p_data['srch_val'] ?>"/>
                                </div>
                                <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                            </div>
                            <!-- 총판계정은 탈퇴처리 불가 -->
                            <?php  if(0 == $_SESSION['u_business']){?>
                                <div class="fr">
                                    <span class="fl mr5" style="line-height:30px;">2차인증 비밀번호</span>
                                    <span><input type="password" name="second_pass_input" id="second_pass_input" value="" maxlength="4" placeholder="2차 비밀번호 입력"></span>
                                    <span><a href="javascript:;" class="btn__all_leave btn h30 btn_red ml5">일괄탈퇴</a></span> 
                                </div>
                            <?php } else {?>
                            <?php } ?>
                        </div>
                    </form>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <?php  if(0 == $_SESSION['u_business']){?>
                                    <th>전체</th>
                                    <th colspan="3">추천인</th>
                                    <th colspan="5">가입 정보</th>
                                    <th colspan="4">머니/포인트</th>
                                    <th colspan="4">정산 정보</th>
                                    <th colspan="1">상태 정보</th>
                                    <th rowspan="2">관리</th>
                                <?php } else {?>
                                    <th colspan="3">추천인</th>
                                    <th colspan="3">가입 정보</th>
                                    <th colspan="4">머니/포인트</th>
                                    <th colspan="3">정산 정보</th>
                                    <th colspan="2">상태 정보</th>
                                <?php } ?>
                            </tr>
                            <tr>
                            <!-- 총판계정 체크 삭제 -->
                            <?php  if(0 == $_SESSION['u_business']){?>
                                <th>
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" class="checkbox__set-all" />
                                        <label for="checkbox_css_all"></label>
                                    </div>
                                </th>
                            <?php } else {?>
                            <?php } ?>
                                <th>대총판</th>
                                <th>총판</th>
                                <th>하부총판</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <?php  if(0 == $_SESSION['u_business']){?>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('level', '<?= $ob_level_change ?>');"><?= $ob_level_color ?></a>
                                </th>
                                 <?php } ?>
                                <th>상태</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('money', '<?= $ob_money_change ?>');"><?= $ob_money_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('point', '<?= $ob_point_change ?>');"><?= $ob_point_color ?></a>
                                </th>
                             
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('g_money', '<?= $ob_g_money_change ?>');"><?= $ob_g_money_color ?></a>
                                </th>
                                <?php  if(0 == $_SESSION['u_business']){?>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('betpoint', '<?= $ob_betpoint_change ?>');"><?= $ob_betpoint_color ?></a>
                                </th>
                                <?php } ?>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('deposit', '<?= $ob_deposit_change ?>');"><?= $ob_deposit_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('withdraw', '<?= $ob_withdraw_change ?>');"><?= $ob_withdraw_color ?></a>
                                </th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('cal', '<?= $ob_cal_change ?>');"><?= $ob_cal_color ?></a>
                                </th>
                                <th>로그인</th>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('regdt', '<?= $ob_regdt_change ?>');"><?= $ob_regdt_color ?></a>
                                </th>
                                <?php  if(0 == $_SESSION['u_business']){?>
                                <th>
                                    <a href="javascript:;" onClick="goOrderby('logindt', '<?= $ob_logindt_change ?>');"><?= $ob_logindt_color ?></a>
                                </th>
                                <?php } ?>
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
                                        $chkbox_css[$i] = "checkbox_css_" . $i;
                                        if ($u_business == 2 || $u_business == 3)
                                            $bgColor = '#32CD32';
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="javascript:onMouseOut(this, <?= $u_business ?>);" bgColor="<?= $bgColor ?>">
                                        <!-- 총판계정 체크 삭제 -->
                                        <?php  if(0 == $_SESSION['u_business']){?>
                                            <td>
                                                <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                                    <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $row['idx'] ?>" class="checkbox__set-item" />
                                                    <label for="<?= $chkbox_css[$i] ?>"></label>
                                                </div>
                                            </td>
                                        <?php } else {?>
                                        <?php } ?>
                                            <!-- 추천인 표기 -->
                                            <?php if(null == $row['p_id'] && 1 != $row['u_business']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                                <td style='text-align:left'></td>
                                                <td style='text-align:left'></td>
                                            <?php }else if(TOP_DISTRIBUTOR == $row['p_bu'] && 1 != $row['u_business']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                                <td style='text-align:left'></td>
                                            <?php }else if(DISTRIBUTOR == $row['p_bu'] && 1 != $row['u_business']){ ?>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['pp_id'] ?>');"><?= $row['pp_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                </td>
                                                <td style='text-align:left'>
                                                    <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                </td>
                                            <?php }else if(1 != $row['u_business']){ ?>
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
                                                <?php if(TOP_DISTRIBUTOR == $row['p_bu']){ ?>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'></td>
                                                    <td style='text-align:left'></td>
                                                <?php }else if(DISTRIBUTOR == $row['p_bu']){ ?>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['pp_id'] ?>');"><?= $row['pp_id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'></td>
                                                <?php }else{ ?>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['ppp_id'] ?>');"><?= $row['ppp_id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['pp_id'] ?>');"><?= $row['pp_id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['p_id'] ?>');"><?= $row['p_id'] ?></a>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                            <!--<td style='text-align:left'><?= $row['dis_line_id'] ?></td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $db_dis_id ?>');"><?= $db_dis_id ?></a>
                                            </td>
                                            <td style='text-align:left'><?= $row['re_id'] ?></td>-->
                                            
                                            <?php if($_SESSION['u_business'] == 0){ ?>
                                                <?php if ($u_business != GENERAL) { ?>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['nick_name'] ?></a>
                                                    </td>
                                                <?php } else { ?>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1600, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1600, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                                    </td>
                                                <?php } ?>
                                            <?php } else { ?>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;"><?= $row['id'] ?></a>
                                                    </td>
                                                    <td style='text-align:left'>
                                                        <a href="javascript:;"><?= $row['nick_name'] ?></a>
                                                    </td>
                                            <?php } ?>
                                            <?php  if(0 == $_SESSION['u_business']){?>
                                            <td><?= $row['level'] ?></td>
                                            <?php } ?>
                                            <td><?= $db_status ?></td>
                                     
                                            <td style='text-align:right'><?= number_format($row['money']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['point']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['g_money']) ?></td>
                                            <?php  if(0 == $_SESSION['u_business']){?>
                                            <td style='text-align:right'><?= number_format($row['betting_p']) ?></td>
                                            <?php } ?>
                                            <td style='text-align:right'><?= number_format($row['ch_sum_money']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['ex_sum_money']) ?></td>
                                            <td style='text-align:right'><?= isset($row['gs']) ? number_format($row['gs']) : '' ?></td>
                                            <td></td>
                                            <td><?= $row['reg_time'] ?></td>
                                            <?php  if(0 == $_SESSION['u_business']){?>
                                            <td><?= $row['last_login'] ?></td>
                                            <td>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_userlog.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');">로그</a>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php', 'popmsg', 660, 1000, 'msg', '<?= $db_m_idx ?>');">쪽지</a>
                                         
                                            <a href="javascript:;" class="btn h25 btn_green" onClick="addPoints('20000','<?= $row['g_money']; ?>','<?= $row['idx'] ?>')">20,000 P 지급</a> 
                                            <a href="javascript:;" class="btn h25 btn_blu" onClick="addPoints('50000','<?= $row['g_money']; ?>','<?= $row['idx'] ?>')">50,000 P 지급</a>
                                            <a href="javascript:;" class="btn h25 btn_dblu" onClick="addPoints('100000','<?= $row['g_money']; ?>','<?= $row['idx'] ?>')">100,000 P 지급</a>
                                            <?php } ?>
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
        include_once(_BASEPATH . '/common/second_check_popup.php');
        ?> 
    </body>
    <!-- Sheet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.3/xlsx.full.min.js"></script>
    <!--FileSaver savaAs 이용 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
    <script>
                function addPoints(points,money,m_idx){
            var param_url = '/member_w/_set_userinfo_money.php';
            var second_pass = $("#second_pass_input").val();

            $.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'m_idx':m_idx,'mtype':'point','mkind':'p','money':money,'point':points, 'second_pass': second_pass, 'comment': '-'},
    	    success: function (data) {
    	    	if(data['retCode'] == "1000"){
                    alert("Successfully Added");

                window.location.reload();
                }else if(data['retCode'] == "2002") {
                    alert('2차인증 비번이 틀렸습니다.');
                }else {
                    alert('업데이트에 실패 하였습니다.');
                    // window.location.reload();
    	    	}
    	    },
    	    error: function (request, status, error) {
    	    	alert('서버 오류 입니다.');
	    		window.location.reload();
    	    }
    	});


        }
        // 사용중인 맴버 엑셀 다운로드
	$(document).on("click", ".btn__excel_down", function(e){
            const second_password = $("#second_pass_input").val();
            
            const regExpEmpty = /\s/g;
            if( second_password.length < 4 || regExpEmpty.test(second_password) ){
                alert("2차 비밀번호를 확인해주세요");
                $("#second_pass_input").focus();
                return false;
            }
                
            // 1:talbe, 2:json, 3:array
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/member_w/_ajax_excel_member_list.php',
                data: {'second_password':second_password},
                success: function (result) {
                    if (result['retCode'] == "1000") {
                        result_excel_file_download(result, excelDataType.JSON, 'member_list');
                    } else if (result['retCode'] == "2002") {
                        alert('2차인증 비밀번호가 틀렸습니다.');
                    }
                },
                error: function (request, status, error) {
                }
            });
	});

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

        $( function(){
            // 전체 체크박스 클릭
            $(".checkbox__set-all").on("click", function(){
                if( $(this).prop("checked") ){
                    $(".checkbox__set-item").prop("checked", true);
                } else {
                    $(".checkbox__set-item").prop("checked", false);
                }
            });
            // 체크박스 중 하나 풀었을때 전체체크 해제
            $(".checkbox__set-item").on("click", function(){
                if( $(".checkbox__set-item").length == $(".checkbox__set-item:checked").length ) {
                    $(".checkbox__set-all").prop("checked", true);
                } else {
                    $(".checkbox__set-all").prop("checked", false);
                }
            });

        });







        $(function(){

            // 일괄탈퇴
            $('.btn__all_leave').on("click", function(e){
                e.preventDefault();
                
                const regExpEmpty = /\s/g;
                const checked = $('.checkbox__set-item:checked');
                const password = $("#second_pass_input").val();
                if ( checked.length < 1 ){
                    alert("삭제할 회원을 선택해주세요");
                    return false;
                }

                if( password.length < 4 || regExpEmpty.test(password) ){
                    alert("2차 비밀번호를 확인해주세요");
                    $("#second_pass_input").focus();
                    return false;
                }

                leaveUser();
            });
        });

        const leaveUser = function(){

            const checkUser = $('.checkbox__set-item:checked');
            let checkUser_data = [];
            checkUser.map((index, item) => { checkUser_data.push(item.value); });
            // console.log(checkUser_data);

            $.ajax({
                url: "./_multi_leave_proc.php",
                data: { 
                    'leave_users' : checkUser_data.join(","),
                    'second_password' : $("#second_pass_input").val(),
                },  
                method: "POST",
                dataType : "json"
            })
            .success(function(response) {
                //console.log(response);
                if(1000 == response['retCode']){
                    $("#second_pass_input").val("");
                    $('.checkbox__set-item').prop("checked", false);
                    alert("선택한 회원들의 탈퇴처리가 완료되었습니다.");
                    window.location.reload();
                }else{
                    alert(response['retMsg']);
                }
            })
            .fail(function(error) {
                console.log(error);
                alert("탈퇴처리에 실패했습니다.");
            })
        }
    </script>
</html>