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


$MEMAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    
    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = $today;
    $end_date = $today;


    $p_data['page'] = trim(isset($_REQUEST['page']) ? $MEMAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }


    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $MEMAdminDAO->real_escape_string($_REQUEST['v_cnt']) : _NUM_PER_PAGE);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = _NUM_PER_PAGE;
    }

    $p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $MEMAdminDAO->real_escape_string($_REQUEST['vtype']) : 'all');
    $p_data['display_type'] = trim(isset($_REQUEST['display_type']) ? $MEMAdminDAO->real_escape_string($_REQUEST['display_type']) : '');
    $p_data['status'] = trim(isset($_REQUEST['status']) ? $MEMAdminDAO->real_escape_string($_REQUEST['status']) : '');
    $p_data['create_dt'] = trim(isset($_REQUEST['create_dt']) ? $MEMAdminDAO->real_escape_string($_REQUEST['create_dt']) : '');
    /* $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_s_date']): $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $MEMAdminDAO->real_escape_string($_REQUEST['srch_e_date']) : $end_date); */

    /* $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59'; */

    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM banners a WHERE a.idx > 0 ";


    if ($p_data['vtype'] == 'node') {
        $p_data['sql'] .= " AND a.status in(1,2) ";
    }

    //$p_data['sql'] .= $srch_basic;

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
        $p_data['sql'] = " SELECT * ";
        $p_data['sql'] .= "     FROM banners a WHERE a.idx > 0 ";

        if($p_data['display_type'] != "") {
        	$p_data['sql'] .= " AND display_type='" . $p_data['display_type'] . "' ";
        }
        if($p_data['status'] != "") {
        	$p_data['sql'] .= " AND status='" . $p_data['status'] . "' ";
        }
        /* $p_data['sql'] .= " AND create_dt >'" . $p_data['db_srch_s_date']. "' ";
        $p_data['sql'] .= " AND create_dt <'" . $p_data['db_srch_e_date']. "' "; */
        $p_data['sql'] .= "order by rank,create_dt desc LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . " ";

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
            $menu_name = "board_menu_6";

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
                        <h4>배너 관리</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="vtype" id="vtype" value="<?= $p_data['vtype'] ?>">
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <!-- <div class="daterange">
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
                                <div><a href="javascript:;" onClick="setDate('<?= $before_month ?>', '<?= $today ?>');" class="btn h30 btn_green">한달</a></div> -->
                                
                                <div class="" style="padding-right: 10px;"></div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="display_type" id="display_type">
                                        <option value="" <?php if ($p_data['display_type'] == '') {
                echo "selected";
            } ?>>전체</option>
                                        <option value="1" <?php if ($p_data['display_type'] == '1') {
                echo "selected";
            } ?>>PC</option>
                                        <option value="2" <?php if ($p_data['display_type'] == '2') {
                echo "selected";
            } ?>>모바일</option>
                                    </select>
                                </div>
                                
                                <div class="" style="padding-right: 10px;">
                                    <select name="status" id="status">
                                        <option value="" <?php if ($p_data['status'] == '') {
                echo "selected";
            } ?>>전체</option>
                                        <option value="1" <?php if ($p_data['status'] == '1') {
                echo "selected";
            } ?>>사용</option>
                                        <option value="0" <?php if ($p_data['status'] == '0') {
                echo "selected";
            } ?>>미사용</option>
                                    </select>
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
                                <div><a href="/board_w/banner_write.php" class="btn h30 <?= $all_btn ?>">배너 등록</a></div>
                            </div>
                        </div>
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>번호</th>
                                <th>노출 타입</th>
                                <th>썸네일</th>
                                <th>사용여부</th>
                                <th>배너 순번</th>
                                <th>등록시간</th>
                                <th>수정/삭제</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                                        //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
                                        //$login_log = explode('|',$row['login_data']);

                                        $db_m_idx = $row['idx'];
                                        $type = ($row['display_type'] == 1) ? 'PC' : '모바일';
                                        $status = ($row['status'] == 0) ? '미사용' : '사용';

                                        /* if ($row['STATUS'] == 3) {
                                          $str_background = "";
                                          }
                                          else {
                                          $str_background = "background-color:#FDF500;";
                                          } */
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $total_cnt - $num ?></td>
                                            <td style='text-align:left'><?= $type?></td>
                                            <td style='text-align:left'>
                                                <img src="<?= IMAGE_SERVER_URL . '/' . IMAGE_PATH . '/' . $row['thumbnail'] ?>" alt="이미지 파일이 존재하지 않습니다.">
                                            </td>
                                            <td style='text-align:left'><?= $status ?></td>
                                            <td style='text-align:left'><?= $row['rank']?></td>
                                            <td style='text-align:left'><?= $row['create_dt']?></td>
                                            <td>
                                                <a href="/board_w/banner_update.php?idx=<?= $db_m_idx ?>&status=<?= $row['status'] ?>&displayType=<?= $row['display_type'] ?>&rank=<?= $row['rank'] ?>" class="btn h25 btn_blu">수정</a>
                                                <a href="javascript:fn_del_event(<?= $db_m_idx ?>);" class="btn h25 btn_blu">삭제</a>
                                            </td>
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
$default_link = "$reqFile?display_type=" . $p_data['display_type']."&status=" . $p_data['status'];
$default_link .= "&vtype=" . $p_data['vtype'] . " "; /* "&srch_s_date=" . $p_data['srch_s_date'] . "&srch_e_date=" . $p_data['srch_e_date'] .  */

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
        /* function setDate(sdate, edate) {
            var fm = document.search;

            fm.srch_s_date.value = sdate;
            fm.srch_e_date.value = edate;
        } */

        function goSearch(vtype = null) {
            var fm = document.search;

            fm.vtype.value = vtype;

            //if((fm.display_type.value!='') && (fm.srch_val.value=='')) {

            //if (fm.srch_level.value < 1) {
            //alert('검색어를 입력해 주세요.');
            //fm.srch_val.focus();
            //return;
            //}
            //}

            fm.method = "get";
            fm.submit();
        }

        function fn_del_event(idx) {
            var str_msg = '삭제 하시겠습니까?';
            var result = confirm(str_msg);
            if (result) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/board_w/_banner_prc_del.php',
                    data: {'idx': idx},
                    success: function (result) {
                        if (result['retCode'] == "1000") {
                            alert('삭제하였습니다.');
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
    </script>
</html>