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

    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

    
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    $p_data['srch_key'] = $MEMAdminDAO->real_escape_string($p_data['srch_key']);
    $p_data['srch_val'] = $MEMAdminDAO->real_escape_string($p_data['srch_val']);
    

    $srch_basic = "";
    switch ($p_data["srch_key"]) {
        case "s_idnick":

            if ($p_data['srch_val'] != '') {
                $srch_basic = "  AND (b.id='" . $p_data['srch_val'] . "' OR b.nick_name='" . $p_data['srch_val'] . "') ";
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
        case "s_ip":
            if ($p_data['srch_val'] != '') {
                $srch_basic = " AND a.ip='" . $p_data['srch_val'] . "' ";
            }
            break;
    }

    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM member_ip_block_history a, member b ";
    $p_data['sql'] .= "  WHERE a.member_idx = b.idx ";
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
        $p_data['sql'] = "SELECT a.idx, a.ip, a.country, a.memo, a.create_dt, b.id, b.nick_name ";
        $p_data['sql'] .= " FROM member_ip_block_history a, member b";
        $p_data['sql'] .= " WHERE a.member_idx = b.idx ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY create_dt DESC";
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
    <body>
        <div class="wrap">
            <?php
            $menu_name = "mem_list_ipblock";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>IP 차단 내역</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>        
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_ip" <?php if ($p_data['srch_key'] == 's_ip') {
                echo "selected";
            } ?>>차단 IP</option>
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
                                <a href="javascript:;"  onClick="setIPBlock('a', '0');" class="btn h30 btn_green">IP차단 전체 해제</a>
                                &nbsp;&nbsp;
                                총 <?= number_format($total_cnt) ?>건
                            </div>
                        </div>
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th colspan="3">IP 정보</th>
                                <th rowspan="2">ID</th>
                                <th rowspan="2">닉네임</th>
                                <th rowspan="2">차단 사유</th>
                                <th rowspan="2">해제</th>
                            </tr>
                            <tr>
                                <th>IP</th>
                                <th>국가</th>
                                <th>차단 일시</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td style='text-align:left'><?= $row['ip'] ?></td>
                                            <td style='text-align:left'><?= $row['country'] ?></td>
                                            <td><?= $row['create_dt'] ?></td>
                                            <td style='text-align:left'><?= $row['id'] ?></td>
                                            <td style='text-align:left'><?= $row['nick_name'] ?></td>
                                            <td style='text-align:left'><?= $row['memo'] ?></td>
                                            <td>
                                                <a href="javascript:;" onClick="setIPBlock('f', '<?= $row['idx'] ?>');" class="btn h25 btn_green">차단 해제</a>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                            } else {
                                ?>
                                <tr><td colspan="8">데이터가 없습니다.</td></tr>
                            <?php
                        }
                        ?>

                        </table>
                        <?php
                        $reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
                        $default_link = "$reqFile?idx=";
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


        function goSearch() {
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

            var str_msg = 'IP 를 해제 하시겠습니까?';

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