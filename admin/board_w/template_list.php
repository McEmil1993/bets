<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$BdsAdminDAO)){
        die();
    }
    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = $today;
    $end_date = $today;

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $BdsAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }

    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $BdsAdminDAO->real_escape_string($_REQUEST['v_cnt']) : _NUM_PER_PAGE);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = _NUM_PER_PAGE;
    }

//$p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $_REQUEST['vtype'] : 'all');
    $p_data['srch_type'] = trim(isset($_REQUEST['srch_type']) ? $BdsAdminDAO->real_escape_string($_REQUEST['srch_type']) : 2);

    $srch_basic = "";
    switch ($p_data["srch_type"]) {
        case 0:
            $srch_basic = "where type = " . $p_data['srch_type'];
            break;
        case 1:
            $srch_basic = "where type = " . $p_data['srch_type'];
            break;
        case 2:
        	$srch_basic = "";
            break;
        case 3:
        	$srch_basic = "where type = " . $p_data['srch_type'];
        	break;
    }


    // 게시물 전체갯수
    $p_data['sql'] = "SELECT COUNT(*) AS CNT FROM ";
    $p_data['sql'] .= "(SELECT '1' as idx, '3' as type, '회원가입' as division, title, contents  as content, update_dt FROM join_message";
    $p_data['sql'] .= " union all ";
    $p_data['sql'] .= "SELECT idx,type,division,title,content,update_dt FROM template) a ";
    $p_data['sql'] .= $srch_basic;

    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
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

    // 게시물이 하나 이상이다.
    /* if ($total_cnt > 0) {
        $p_data['sql'] = " SELECT * FROM template ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . " ";
        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    } */

    if ($total_cnt > 0) {
    	$p_data['sql'] = "SELECT idx,type,division,title,content,update_dt FROM ";
    	$p_data['sql'] .= "(SELECT '0' as idx, '3' as type, '회원가입' as division, title, contents  as content, update_dt FROM join_message";
    	$p_data['sql'] .= " union all ";
    	$p_data['sql'] .= "SELECT idx,type,division,title,content,update_dt FROM template) a ";
    	$p_data['sql'] .= $srch_basic;
    	$p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . " ";
    	$db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }
    $BdsAdminDAO->dbclose();
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
        <div class="wrap">
            <?php
            $menu_name = "board_menu_5";

            include_once(_BASEPATH . '/common/left_menu.php');
            include_once(_BASEPATH . '/common/iframe_head_menu.php');

            $start_date = date("Y/m/d");
            $end_date = date("Y/m/d");
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <?php //echo $p_data['sql'] ?>
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>템플릿 관리</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="vtype" id="vtype" value="<?= true === isset($p_data['vtype']) ? $p_data['vtype'] : 0 ?>">
                        <input type="hidden" name="h_srch_type" id="h_srch_type" value="<?= $p_data['srch_type'] ?>">
                        <div class="panel_tit">
                            <div class="search_form fl">

                                <div class="">
                                    <select id="srch_type" style="width: 100%" onchange="fn_On_chnage();">
                                        <option value="2" selected>전체보기</option>
                                        <option value="0" >쪽지</option>
                                        <option value="1" >답변</option>
                                        <option value="3" >가입</option>
                                    </select>
                                </div>
                                <div><a href="/board_w/template_write.php" class="btn h30 btn_blu">새 템플릿 등록</a></div>
                            </div>
                        </div>
                    </form>            
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>번호</th>
                                <th>구분</th>
                                <th>분류</th>
                                <th>제목</th>
                                <th>수정일시</th>
                                <th>설정</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
                                        //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
                                        //print_r($row);
                                        $db_m_idx = $row['idx'];
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $row['idx'] ?></td>
                                            <td><?php if($row['type'] == 0) {echo '쪽지';}else if ($row['type'] == 1){echo '답변';}else if ($row['type'] == 3){echo '가입';}?></td>
                                            <td><?= $row['division'] ?></td>
                                            <?php if($row['idx'] != 0) {?>
                                            <td><a href="/board_w/template_detail.php?idx=<?= $row['idx'] ?>"><?= $row['title'] ?></a></td>
                                            <?php } else {?>
                                            <td><a href="/board_w/join_message_detail.php"><?= $row['title'] ?></a></td>
                                            <?php } ?>
                                            <td><?= $row['update_dt'] ?></td>
                                            <td>
												<?php if($row['idx'] != 0) {?>
                                                <a href="javascript:fn_del_template(<?= $db_m_idx ?>);" class="btn h25 btn_blu adm_btn_notice_del">삭제</a>
                                            	<?php } ?>
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

            var h_srch_type = $("#h_srch_type").val();
            $("#srch_type").val(h_srch_type).prop("selected", true);
        });

        function fn_del_template(idx) {

			if(idx == 0) {
				alert("회원가입 메세지는 삭제할 수 없습니다.");
				return false;
			}
			
            var str_msg = '삭제 하시겠습니까?';
            var result = confirm(str_msg);
            if (result) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/board_w/_template_prc_del.php',
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

        function fn_update_title_template(idx) {
            var division = $("#division_" + idx).val();
            var title = $("#title_" + idx).val();

            var str_msg = '수정 하시겠습니까?';
            var result = confirm(str_msg);
            if (result) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/board_w/_template_title_prc_update.php',
                    data: {'idx': idx, 'division': division, 'title': title},
                    success: function (result) {
                        if (result['retCode'] == "1000") {
                            alert('수정하였습니다.');
                            window.location.reload();
                            return;
                        } else {
                            alert(result['retMsg']);
                            return;
                        }
                    },
                    error: function (request, status, error) {
                        alert('수정에 실패하였습니다.');
                        return;
                    }
                });
            } else {
                return;
            }
        }

        function goSearch(vtype = null) {
            var fm = document.search;

            fm.vtype.value = vtype;

            fm.method = "get";
            fm.submit();
        }

        function fn_On_chnage()
        {
            let srch_type = $('#srch_type').val();

            //alert(srch_type);

            location.href = '/board_w/template_list.php?srch_type=' + srch_type;
        }
    </script>
</html>