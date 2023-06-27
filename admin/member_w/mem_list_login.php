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

    
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    $p_data['srch_level'] = $MEMAdminDAO->real_escape_string($p_data['srch_level']);
    $p_data['srch_key'] = $MEMAdminDAO->real_escape_string($p_data['srch_key']);
    $p_data['srch_val'] = $MEMAdminDAO->real_escape_string($p_data['srch_val']);
    
    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":

            if ($p_data['srch_val'] != '') {
                $srch_basic = "  (b.id='" . $p_data['srch_val'] . "' OR b.nick_name='" . $p_data['srch_val'] . "') ";
            }
            break;
        case "s_accountname":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " b.account_name like '%" . $p_data['srch_val'] . "%' ";
            }
            break;
        case "s_disline":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " b.dis_line_id='" . $p_data['srch_val'] . "' ";
            }
            break;

        case "ip":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " a.ip like '%" . $p_data['srch_val'] . "%' ";
            }
        break;
    
    }

    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM member_login_history a ";
    $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";

    $p_data['sql_where'] = "";

    if (($p_data['srch_level'] > 0) && ($p_data['srch_level'] <= 10)) {
        if ($p_data['sql_where'] == '') {
            $p_data['sql_where'] .= " WHERE ";
        } else {
            $p_data['sql_where'] .= " AND ";
        }

        $p_data['sql_where'] .= " b.level=" . $p_data['srch_level'] . " ";
    }

    if ($srch_basic != '') {
        if ($p_data['sql_where'] == '') {
            $p_data['sql_where'] .= " WHERE ";
        } else {
            $p_data['sql_where'] .= " AND ";
        }

        $p_data['sql_where'] .= $srch_basic;
    }

    $p_data['sql'] .= $p_data['sql_where'];

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
        $db_dataArr = $MEMAdminDAO->getLogLoginList($p_data);
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
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap">
<?php
$menu_name = "mem_list_login";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>로그인 정보</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <div class="panel_tit">
                        <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>            
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
                                        <option value="ip" <?php if ($p_data['srch_key'] == 'ip') {
                                            echo "selected";
                                        } ?>>IP검색</option>     
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
                        </form>            	
                    </div>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>No</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>상태</th>
                                <th>도메인</th>
                                <th>아이피</th>
                                <th>국가</th>
                                <th>로그인일시</th>
                                <th>관리</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;

                                        $db_m_idx = $row['member_idx'];

                                        // b.id, b.nick_name, b.status, a.login_domain,  a.ip, a.country, a.login_datetime
                                        switch ($row['status']) {
                                            //회원상태 ( 1:정상, 2:정지, 3:탈퇴, 11:승인 대기회원 )
                                            case 1: $db_status = '정상';
                                                break;
                                            case 2: $db_status = '정지';
                                                break;
                                            case 3: $db_status = '탈퇴';
                                                break;
                                            case 11: $db_status = '승인 대기회원';
                                                break;
                                            default: $db_status = '점검 필요';
                                                break;
                                        }
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $total_cnt - $num ?></td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td><?= $db_status ?></td>
                                            <td style='text-align:left'><?= $row['login_domain'] ?></td>
                                            <td style='text-align:left'><?= $row['country'] != '필리핀' ? $row['ip']:'x.x.x.x' ?></td>
                                            <td style='text-align:left'><?= $row['country'] != '필리핀' ? $row['country']:'' ?></td>
                                            <td><?= $row['login_datetime'] ?></td>
                                            <td>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');">로그인 로그</a>
                                                <?php
                                                if ($row['bip'] == '') {
                                                    ?>
                                                    <a href="javascript:;" class="btn h25 btn_blu" onClick="setIPBlock('b', '<?= $row['lidx'] ?>');">IP 차단</a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="javascript:;" class="btn h25 btn_green" onClick="setIPBlock('f', '<?= $row['bidx'] ?>');">IP 해제</a>
                                            <?php
                                        }
                                        ?>

                                            </td>
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

        function setIPBlock(mtype, setidx = 0) {

            var param_url = '/member_w/_set_ipblock.php';

            var str_msg = '';
            if (mtype == 'b') {
                str_msg = 'IP 를 차단 하시겠습니까?';
            } else if (mtype == 'f') {
                str_msg = 'IP 를 해제 하시겠습니까?';
            } else {
                alert('잘못된 요청입니다.');
                return;
            }

            var result = confirm(str_msg);

            if (result) {

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: param_url,
                    data: {'mtype': mtype, 'setidx': setidx},
                    success: function (data) {

                        if (data['retCode'] == "1000") {
                            window.location.reload();
                        } else {
                            alert('실패 하였습니다.');
                            window.location.reload();
                        }
                    },
                    error: function (request, status, error) {
                        alert('서버 오류 입니다.');
                        window.location.reload();
                    }
                });
        }

        }

    </script>
</html>