<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();
$arrUserList = array();

if ($db_conn) {
    // 총판목록
    $today = date("Y/m/d");
    $before_week = date("Y/m/d", strtotime("-1 week", time()));
    $before_month = date("Y/m/d", strtotime("-1 month", time()));
    $start_date = date("Y/m/d", strtotime("-3 day", time()));
    $end_date = $today;
    $prd_id = trim(isset($_REQUEST['prd_id']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['prd_id']) : 0);

    $p_data['page'] = trim(isset($_REQUEST['page']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['page']) : 1);
    if ($p_data['page'] < 1) {
        $p_data['page'] = 1;
    }

    $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['v_cnt']) : 20);
    if ($p_data['num_per_page'] < 1) {
        $p_data['num_per_page'] = 20;
    }

    $p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_key']) : 's_idnick');
    $p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_val']) : '');
    $p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_s_date']) : $start_date);
    $p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $LSportsAdminDAO->real_escape_string($_REQUEST['srch_e_date']) : $end_date);

    $p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']) . ' 00:00:00';
    $p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']) . ' 23:59:59';

    $srch_basic = "";
    $srch_url = "";
// 관리자면 전체
    /* if($_SESSION['loginType'] == 1){
      $srch_basic = "";
      $srch_url = "";
      }else{
      $srch_basic = " AND recommend_member = ".$_SESSION['index'];
      $srch_url = "recommend_member".$_SESSION['index'];
      } */

    if ($prd_id > 0) {
        $srch_basic = " AND PRD_ID = $prd_id";
    }

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];


    $p_data['sql'] = "select idx, id, nick_name from member where idx > 0 AND u_business = 3";
    $distributorList = $LSportsAdminDAO->getQueryData($p_data);

    $find = true; // 총판접속시 해당 총판유저만 검색가능하게 한다.
    switch ($p_data["srch_key"]) {
        case "s_idnick":
            if ($p_data['srch_val'] != '') {
                /* $p_data['sql'] = "SELECT count(*) CNT FROM member WHERE (member.id like '%" . $p_data['srch_val'] . "%' OR member.nick_name like '%" . $p_data['srch_val'] . "%') AND recommend_member = ".$_SESSION['index'];
                  $result = $LSportsAdminDAO->getQueryData($p_data);
                  if($result[0]['CNT'] > 0){
                  $find = false;
                  } */

                $srch_basic .= " AND (member.id like '%" . $p_data['srch_val'] . "%' OR member.nick_name like '%" . $p_data['srch_val'] . "%') ";
                $srch_url .= "srch_key=s_idnick&srch_val=" . $p_data['srch_val'];
            }
            break;
    }
    
    // 총판계정 접속시
    if ($_SESSION['u_business'] > 0) {
        //$srch_basic .= " AND member.recommend_member = ".$_SESSION['member_idx'];
        list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'], $LSportsAdminDAO);
        $str_param = implode(',', $param_dist);
        $srch_basic .= " AND member.recommend_member in ($str_param) ";
    }

    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    // 전체갯수
    $p_data['sql'] = "SELECT count(*) as CNT FROM HOLDEM_BET_HIST JOIN member ON HOLDEM_BET_HIST.MBR_IDX = member.idx";
    $p_data['sql'] .= " WHERE UPDATE_DT >= ? AND UPDATE_DT <= ?";
    $p_data['sql'] .= $srch_basic;
    
    $result = $LSportsAdminDAO->getQueryData_pre($p_data['sql'], [$p_data['db_srch_s_date'], $p_data['db_srch_e_date']]);
    $total_cnt = $result[0]['CNT'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    // 소속 유저가 아니면 검색 안됨.
    //if(!$find) $total_cnt = 0;
    if ($total_cnt > 0) {
        $p_data['sql'] = "SELECT HOLDEM_BET_HIST.*, id, nick_name FROM HOLDEM_BET_HIST JOIN member ON HOLDEM_BET_HIST.MBR_IDX = member.idx";
        $p_data['sql'] .= " WHERE UPDATE_DT >= ? AND UPDATE_DT <= ?";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " order by HOLDEM_BET_IDX DESC";
        $p_data['sql'] .= " LIMIT ?, ?;";
        $db_dataArr = $LSportsAdminDAO->getQueryData_pre($p_data['sql'], [$p_data['db_srch_s_date'], $p_data['db_srch_e_date'], $p_data['start'], $p_data['num_per_page']]);
        //print_r($db_dataArr);
    }
    $LSportsAdminDAO->dbclose();
}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
    <!--<![endif]-->

    <?php
    include_once(_BASEPATH . '/common/head.php');
    ?>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js?v=<?php echo date("YmdHis"); ?>"></script>
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
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
            <input type="hidden" id="memberIdxList" name="memberIdxList">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "holdem_betting_list";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <?php //echo $sql;  ?>
                <?php //print_r($db_dataArr);  ?>
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>홀덤 배팅내역</h4>
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
                                <div>
                                    <select name="prd_id" id="prd_id" style="width: 100%">
                                        <option value="0">전체</option>

                                        <?php foreach ($prdList as $key => $item) { ?>
                                            <option value="<?= $key ?>"   <?php if ($prd_id == $key): ?> selected<?php endif; ?>><?= $item ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_key" id="srch_key">
                                        <option value="s_idnick">아이디 및 닉네임</option>
                                    </select>
                                </div>
                                <div class="" style="padding-right: 10px;">
                                    <input type="text" id="srch_val" name="srch_val" class=""  placeholder="" value="<?= $p_data['srch_val'] ?>" />
                                </div>
                                <div> <a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                                <div><a href="javascript:openNotePop();" style="margin-left: 10px" class="btn h30 btn_blu">쪽지</a></div>
                                <!--<div><a href="#" class="btn h30 btn_blu" onclick="onBtnClickTotalCalculate()">전체 정산</a></div>-->
                            </div>
                            <div class="search_form fr">
                                총 <?= number_format($total_cnt) ?>건
                            </div>
                        </div>
                    </form>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th width="6%">날짜</th>
                                <th width="5%">아이디</th>
                                <th width="5%">닉네임</th>
                                <th width="5%">회차</th>
                                <th width="5%">베팅금액</th>
                                <th width="5%">당첨금</th>
                                <th width="5%">수수료</th>
                                <th width="5%">보유금액</th>
                                <th width="6%">베팅결과</th>
                            </tr>
                            <?php
                            $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
                            $i = 0;
                            foreach ($db_dataArr as $key => $item) {
                                //$chkbox_css[$i] = "checkbox_css_" . $i;
                                $status = '게임중';
                                $status_color = '';
                                if ('ROUND_END' == $item['EVENT'] && 0 < $item['WIN_MONEY']) {
                                    $status = '적중';
                                    $status_color = '#9FD5E9';
                                } else if('ROUND_END' == $item['EVENT']) {
                                    $status = '낙첨';
                                    $status_color = '#F9BFBF';
                                }

                                // 당첨금
                                $winMoney = $item['WIN_MONEY'];

                                ?>

                                <tr>
                                    <td width="6%"><?= $item['UPDATE_DT'] ?></td>
                                    <td width="8%">
                                        <a href="javascript:;" onClick="setUserinfoDetail('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $item['MBR_IDX'] ?>', '7');"><?= $item['id'] ?></a> 
                                    </td>
                                    <td width="5%">
                                        <a href="javascript:;" onClick="setUserinfoDetail('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $item['MBR_IDX'] ?>', '7');"><?= $item['nick_name'] ?></a>
                                    </td>
                                    <td width="5%"><?= $item['GAME_NUM'] ?></td>
                                    <td width="5%" style='text-align:right'><?= number_format($item['BET_MONEY']) ?></td>
                                    <td width="5%" style='text-align:right'><?= number_format($item['WIN_MONEY']) ?></td>
                                    <td width="5%" style='text-align:right'><?= number_format($item['FEE']) ?></td>
                                    <td width="5%" style='text-align:right'><?= number_format($item['FINAL_MONEY']) ?></td>
                                    <td width="5%" bgcolor="<?= $status_color ?>"><?= $status ?></td>
                                    <?php $i++;
                                } ?>
                        </table>
                    </div>
                    <?php
                    $requri = explode('?', $_SERVER['REQUEST_URI']);
                    $reqFile = basename($requri[0]);
                    $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'];
                    $default_link .= "&srch_s_date=" . $p_data['srch_s_date'] . "&srch_e_date=" . $p_data['srch_e_date'];
                    include_once(_BASEPATH . '/common/page_num.php');
                    ?>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#srch_key').val('<?= $p_data['srch_key'] ?>').prop("selected", true);
            });

            const goSearch = function () {
                var fm = document.search;
                //const status = $('#srch_key option:selected').val();
                //console.log(status);
                
                fm.method = "get";
                fm.submit();
            }

            const setDate = function (sdate, edate) {
                var fm = document.search;

                fm.srch_s_date.value = sdate;
                fm.srch_e_date.value = edate;
            }

            $(document).ready(function () {
            })
        </script>
        <?php
        include_once(_BASEPATH . '/common/bottom.php');
        include_once(_BASEPATH . '/common/second_check_popup.php');
        ?>
    </body>
</html>