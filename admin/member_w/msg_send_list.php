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


    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 10);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = 2;
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


    $p_data["sql_where"] = " WHERE a.member_idx=b.idx ";
    $p_data["sql_where"] .= $srch_basic;


    $db_dataArrCnt = $MEMAdminDAO->getMsgListCount($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];

    //$p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['page_per_block'] = 3;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    if ($total_cnt > 0) {
        $db_dataArr = $MEMAdminDAO->getMsgList($p_data);
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
            $menu_name = "msg_send_list";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>쪽지 발송 내역</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>        
                        <div class="panel_tit">
                            <div class="search_form fl">
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
                    </form>            
                    <div class="panel_tit">
                        <div class="search_form fl">
                            <div><a href="javascript:;" onClick="setMsg('a', '0');" class="btn h30 btn_mdark">모든 쪽지 삭제</a></div>
                            <div><a href="javascript:;" onClick="setMsg('r', '0');" class="btn h30 btn_mdark">읽은 쪽지 삭제</a></div>
                            <div><a href="javascript:;" onClick="setMsg('week', '0');" class="btn h30 btn_mdark">7일 이전 모든 쪽지 삭제</a></div>
                            <div style="color:#f89d1b!important">
                                &nbsp;&nbsp; ※ 검색된 쪽지만 삭제하는 것이 아닌 모든 쪽지를 삭제합니다.
                            </div>
                        </div>
                    </div>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th colspan="6">쪽지 정보</th>
                                <th colspan="6">수신인 정보</th>
                                <th rowspan="2">관리</th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>제목</th>
                                <th>발송일</th>
                                <th>상태</th>
                                <th>확인</th>
                                <th>관리</th>
                                <th>총판라인</th>
                                <th>총판</th>
                                <th>추천인</th>
                                <th>레벨</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;

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

                                        $db_buff = stripslashes($row['content']);
                                        $db_content = nl2br(htmlspecialchars_decode($db_buff));

                                        $db_m_idx = $row['m_idx'];

                                        $db_msg_idx = $row['idx'];
                                        ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td><?= $total_cnt - $num ?></td>
                                            <td style='text-align:left'><?= $row['title'] ?></td>
                                            <td><?= $row['reg_time'] ?></td>
                                            <td style='text-align:center'><?= $db_status ?></td>
            <?php
            $read_status = $row['read_yn'] == 'Y' ? 'O' : '';
            $read_style = $row['read_yn'] == 'Y' ? "background-color:#01B255; color:#fff" : '';
            ?>
                                            <td style='text-align:center; <?= $read_style ?>'><?= $read_status ?></td>
                                            <td><a href="javascript:;" class="btn h25 btn_red" onClick="setMsg('d', '<?= $db_msg_idx ?>');">쪽지 삭제</a></td>
                                            <td></td>
                                            <td style='text-align:left'><?= $row['dis_id'] ?></td>
                                            <td style='text-align:left'><?= $row['recommend_member'] ?></td>
                                            <td><?= $row['level'] ?></td>
                                            <td style='text-align:left'><?= $row['id'] ?></td>
                                            <td style='text-align:left'><?= $row['nick_name'] ?></td>
                                            <td>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php', 'popmsg', 660, 1000, 'msg', '<?= $db_m_idx ?>');">쪽지</a>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popmsg', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');">회원정보</a>
                                            </td>
                                        </tr>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td>내용</td>
                                            <td style='text-align:left' colspan="12"><?= $db_content ?></td>
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
$reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "";
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
                alert('검색어를 입력해 주세요.');
                fm.srch_val.focus();
                return;
            }

            fm.method = "get";
            fm.submit();
        }


        function setMsg(mtype, setidx = 0) {

            var param_url = '/member_w/_set_msg.php';

            var str_msg = '메세지를 삭제 하시겠습니까?';

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