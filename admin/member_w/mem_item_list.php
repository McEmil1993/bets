<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

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
    
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    
    $ob_no_change = $ob_level_change = $ob_money_change = $ob_point_change = $ob_betpoint_change = $ob_regdt_change = $ob_logindt_change = $ob_deposit_change = $ob_withdraw_change = $ob_cal_change = "";
    
    $p_data['srch_level'] = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);
    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
    $p_data['srch_status'] = trim(isset($_REQUEST['srch_status']) ? $_REQUEST['srch_status'] : 0);

    $p_data['srch_level'] = $MEMAdminDAO->real_escape_string($p_data['srch_level']);
    $p_data['srch_key'] = $MEMAdminDAO->real_escape_string($p_data['srch_key']);
    $p_data['srch_val'] = $MEMAdminDAO->real_escape_string($p_data['srch_val']);
    $p_data['srch_status'] = $MEMAdminDAO->real_escape_string($p_data['srch_status']);
    
    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND (m.id like '%" . $p_data['srch_val'] . "%' OR m.nick_name like '%" . $p_data['srch_val'] . "%') ";
            }
            break;
    }

    $p_data['sql']  = "SELECT COUNT(*) AS CNT FROM member_item as a JOIN member m on a.member_idx = m.idx";
    $p_data["sql"] .= " WHERE a.idx > 0 ";
    $p_data['sql'] .= $srch_basic;
    $db_total_cnt = $MEMAdminDAO->getQueryData($p_data);

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

    $p_data["sql_orderby"] = ' order by a.idx desc';
    if ($total_cnt > 0) {
        $p_data['sql'] = "SELECT item_id, name, item_value, a.status, a.create_dt, a.update_dt, m.id, m.idx, nick_name"
                        . " FROM member_item as a join item on a.item_id = id"
                        . " join member as m on a.member_idx = m.idx";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= $p_data["sql_orderby"];
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . ";";

        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
        //CommonUtil::logWrite(" mem_item_list " . $p_data['sql'], "info");
    }

    $MEMAdminDAO->dbclose();
}
?>
<html lang="ko">
    <?php
    include_once(_BASEPATH . '/common/head.php');
    ?>
    <script>
        $(document).ready(function () {
            App.init();
            FormPlugins.init();
        });
    </script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "mem_item_list";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>회원 아이템 정보</h4>
                    </a>
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
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick" <?php
                                        if ($p_data['srch_key'] == 's_idnick') {
                                            echo "selected";
                                        }
                                        ?>>아이디 및 닉네임</option>
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
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>이름</th>
                                <th>수치</th>
                                <th>상태</th>
                                <th>구매시</th>
                                <th>사용시</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $db_m_idx = $row['idx'];
                                        $db_status = "";
                                        switch ($row['status']) {
                                            case 0: $db_status = "미사용";
                                                break;
                                            case 1: $db_status = "사용";
                                                break;
                                        }
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '';">
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1600, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1600, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td><?= $row['name'] ?></td>
                                            <td><?= $row['item_value'] ?></td>
                                            <td><?= $db_status ?></td>
                                            <td style='text-align:center'><?= $row['create_dt'] ?></td>
                                            <td style='text-align:center'><?= $row['update_dt'] ?></td>
                                        </tr>
                                        <?php
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
                        $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'];
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
            fm.s_ob_type.srch_status = <?= $p_data['srch_status'] ?>;

            fm.method = "get";
            fm.submit();
        }
    </script>
</html>