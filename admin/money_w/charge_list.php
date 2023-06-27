<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

if(0 != $_SESSION['u_business']){
    die();
}

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = $today;
$p_data['vtype'] = '';
$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 20);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 20;
}

$p_data['monitor_charge'] = trim(isset($_REQUEST['monitor_charge']) ? $_REQUEST['monitor_charge'] : '');
$p_data['srch_status'] = trim(isset($_REQUEST['srch_status']) ? $_REQUEST['srch_status'] : '');
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
$p_data['u_business'] = trim(isset($_REQUEST['u_business']) ? $_REQUEST['u_business'] : 0);

$srch_basic = "";
switch ($p_data["srch_key"]) {
    case "s_idnick":
        if ($p_data['srch_val'] != '') {
            $srch_basic = "  AND (m.id='" . $p_data['srch_val'] . "' OR m.nick_name='" . $p_data['srch_val'] . "') ";
        }
        break;
    case "s_accountname":
        if ($p_data['srch_val'] != '') {
            $srch_basic = " AND m.account_name like '%" . $p_data['srch_val'] . "%' ";
        }
        break;
}

if ($p_data['srch_status'] != '') {
    $srch_basic .= "  AND a.status=" . $p_data['srch_status'] . " ";
}

if ($p_data['monitor_charge'] == 'Y') {
    $srch_basic .= "  AND m.is_monitor_charge='Y' ";
}


$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);

$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']).' 00:00:00';
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']).' 23:59:59';


$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$CASHAdminDAO)){
        die();
    }
    // 입금계좌 정보
    $p_data['sql'] = "SELECT bank_id, account_name, account_number, account_bank FROM account_level_list WHERE idx > 0";
    $result = $CASHAdminDAO->getQueryData($p_data);
    $bankInfos = array();
    foreach ($result as $key => $value) {
        $bankInfos[$value['bank_id']][$value['account_number']] = $value;
    }
    
    // 대총판, 총판 선택시 해당 총파소속 유저들을 가져와야 한다.
    if($p_data['u_business'] > 1){
        $p_data['sql'] = "SELECT id FROM member WHERE u_business = ".$p_data['u_business'];
        $result = $CASHAdminDAO->getQueryData($p_data);
        $disList = array();
        foreach ($result as $key => $value) {
            $disList[] = "'".$value['id']."'";
        }
        $disList = implode(',', $disList);

        $srch_basic .= "  AND m.dis_id in ($disList)";
    }
    
    $p_data['sql'] = " SELECT count(*) as CNT ";
    $p_data['sql'] .= " FROM member_money_charge_history a LEFT JOIN member m ON a.member_idx=m.idx ";
    $p_data['sql'] .= " WHERE  a.idx > 0 AND a.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND  a.create_dt <= '" . $p_data['db_srch_e_date'] . "' ";
    $p_data['sql'] .= $srch_basic;

    $db_dataArrCnt = $CASHAdminDAO->getQueryData($p_data);

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
        $p_data['sql'] = " SELECT m.idx as m_idx, m.id, m.nick_name, m.level, m.account_bank, a.account_number, m.account_name, m.is_monitor_charge, m.dis_id,a.bank_name ";
        $p_data['sql'] .= ", a.bank_id, a.idx as charge_idx, a.deposit_name, a.money, a.bonus_point, a.bonus_option_idx, a.manual_bonus_point, a.status, a.create_dt, a.update_dt, a.delete_dt, a.referenceId, a.set_type ";
        $p_data['sql'] .= " FROM member_money_charge_history a LEFT JOIN member m ON a.member_idx=m.idx ";
        $p_data['sql'] .= " WHERE a.idx > 0 AND a.create_dt >= '" . $p_data['db_srch_s_date'] . "' AND  a.create_dt <= '" . $p_data['db_srch_e_date'] . "' ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY a.idx DESC ";
        $p_data['sql'] .= " LIMIT " . $p_data['start'] . ", " . $p_data['num_per_page'] . ";";

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    }

    // 총판 정보 읽어오기 
    $sql = "select id,name,low_id,high_id from business_type where id <> 1 order by id asc ";
    $db_dists = $CASHAdminDAO->getQueryData_pre($sql, []);

    $CASHAdminDAO->dbclose();
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
            <input type="hidden" id="qna_idx" name="qnaidx">
            <input type="hidden" id="selContent" name="selContent">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "charge_list";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');

            $start_date = date("Y/m/d");
            $end_date = date("Y/m/d");
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_monetization_on vam ml20 mr10"></i>
                        <h4>충전관리</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="monitor_charge" id="monitor_charge">
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="" style="padding-right: 10px;">
                                    <select name="u_business" id="u_business">
                                        <option value="0" <?php if (0 == $u_business){echo "selected";} ?>>전체</option>
                                        <?php
                                        if (!empty($db_dists)) {
                                            foreach ($db_dists as $row) {
                                                $id = $row['id'];
                                                $name = $row['name'];
                                        ?>
                                        <option value="<?= $id ?>" <?php if ($id == $p_data['u_business']) {echo "selected";} ?>><?= $name ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>

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
                                <div class="" style="padding-right: 10px;">
                                    <select name="srch_status" id="srch_status">
                                        <option value="">전체</option>
                                        <option value="1" <?php
                                        if ($p_data['srch_status'] == 1) {
                                            echo "selected";
                                        }
                                        ?>>신청</option>
                                        <option value="2" <?php
                                        if ($p_data['srch_status'] == 2) {
                                            echo "selected";
                                        }
                                        ?>>대기</option>
                                        <option value="11" <?php
                                                if ($p_data['srch_status'] == 4) {
                                                    echo "selected";
                                                }
                                                ?>>취소</option>
                                        <option value="3" <?php
                                                if ($p_data['srch_status'] == 3) {
                                                    echo "selected";
                                                }
                                                ?>>완료</option>

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
                                    </select>
                                </div>

                                <div class="">
                                    <input type="text" name="srch_val" id="srch_val" class=""  placeholder="검색" value="<?= $p_data['srch_val'] ?>"/>
                                </div>
                                <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                            </div>
                            <div class="search_form fr">
                                <div class="checkbox checkbox-css checkbox-inverse">
                                    <input type="checkbox" id="checkbox_css_101" name="checkbox_css_101" <?php
                                        if ($p_data['monitor_charge'] == 'Y') {
                                            echo "checked";
                                        }
                                                ?> />
                                    <label for="checkbox_css_101">충전 모니터링만 보기</label>
                                </div>
                            </div>
                        </div>
                    </form>            
                    <div class="panel_tit">
                        <div class="search_form fl">
                            <div><a href="javascript:;" onClick="setMoney('3');" class="btn h30 btn_red">입금 처리</a></div>
                            <div><a href="javascript:;" onClick="setMoney('11');" class="btn h30 btn_mdark">입금 취소</a></div>
                            <div><a href="javascript:;" onClick="setMoney('2');" class="btn h30 btn_gray">전체 대기 처리</a></div>
                        </div>
                        <div class="search_form fr">
                            <div style="color:#f89d1b!important">
                                ※ 충전 모니터링 회원은 아이디/닉네임이 빨간색으로 표시됩니다.
                            </div>
                        </div>
                    </div>

                    <div class="tline">
                        <table class="mlist">
                            <tr>
                            <th>
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                        <label for="checkbox_css_all"></label>
                                    </div>
                                </th>
                                <th>레벨</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>소속</th>
                                <th>입금자명</th>
                                <th>입금계좌</th>
                                <th>입금계좌명</th>
                                <th>입금금액</th>
                                <th>선택한 옵션</th>
                                <th>옵션 보너스 P</th>
                                <th>수동 보너스 P</th>
                                <th>요청일자</th>
                                <th>처리일자</th>
                                <th>수동 보너스 지급</th>
                                <th>상태</th>
                                <th>쪽지</th>
                            </tr>
                            <?php
                            if ($total_cnt > 0) {
                                $i = 0;
                                if (!empty($db_dataArr)) {
                                    foreach ($db_dataArr as $row) {
                                        $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;

                                        $chkbox_css[$i] = "checkbox_css_" . $i;

                                        if ($row['is_monitor_charge'] == 'Y') {
                                            $css_color_id = "color:#FD0000;";

                                            $db_id = "<font color='red'>" . $row['id'] . "</font>";
                                            $db_nick = "<font color='red'>" . $row['nick_name'] . "</font>";
                                        } else {
                                            $css_color_id = "";
                                            $db_id = $row['id'];
                                            $db_nick = $row['nick_name'];
                                        }

                                        $db_m_idx = $row['m_idx'];

                                        if ($row['deposit_name'] == '') {
                                            $db_account_name = $row['account_name'];
                                        } else {
                                            $db_account_name = $row['deposit_name'];
                                        }
                                        $status_str = "오류";
                                        switch ($row['status']) {
                                            case 1:  $status_style = ""; $status_str = "신청"; break;
                                            case 2: $status_style = "style='background-color:#F7FFB5;'"; $status_str = "대기"; break;
                                            case 3: $status_style = "style='background-color:#c6f17d;'"; $status_str = "<font color='red'>완료</font>"; break;
                                            case 11: $status_style = "style='background-color:#ef6963;'";$status_str = "관리자 취소"; break;
                                        }
                                        $db_account_number = $UTIL->getAccountNumberColor_renew($row['account_number']);
                                        
                                        $db_account_number = true === isset($db_account_number) && false === empty($db_account_number) ? $db_account_number : null;
                                        if(!isset($row['bank_name']))
                                            $db_account_info = $bankInfos[$row['bank_id']][$row['account_number']]['account_bank'] . " " . $db_account_number;
                                        else
                                            $db_account_info = $row['bank_name'] . " " . $db_account_number;
                                        
                                        $db_account_number = $UTIL->getAccountNumberColor_renew($row['account_number']);
                                        $db_account_number = true === isset($db_account_number) && false === empty($db_account_number) ? $db_account_number : null;
                                        
                                        if(true === isset($bankInfos[$row['bank_id']][$row['account_number']]['account_bank'])){
                                            $db_account_info = $bankInfos[$row['bank_id']][$row['account_number']]['account_bank'] . " " . $db_account_number;
                                        }
                                        if(!isset($bankInfos[$row['bank_id']][$row['account_number']])){
                                            $db_deposit_account_name = 'PaySharp';
                                        }else{
                                            $db_deposit_account_name = $bankInfos[$row['bank_id']][$row['account_number']]['account_name'];
                                        }
                                        
                                        // 가상코인이다.
                                        if($row['bank_id'] == 1000){
                                            $db_deposit_account_name = '네오덱스(코인)';
                                        }
                                        ?>
                                        <tr <?= $status_style ?>>
                                            <td>
                                                <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                                    <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $row['charge_idx'] ?>" />
                                                    <label for="<?= $chkbox_css[$i] ?>"></label>
                                                </div>
                                            </td>
                                            <td><?= $row['level'] ?></td>
                                            <td style='text-align:left;<?= $css_color_id ?>'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $db_id ?></a>
                                            </td>
                                            <td style='text-align:left;<?= $css_color_id ?>'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php', 'popuserinfo', 800, 1400, 'userinfo', '<?= $db_m_idx ?>');"><?= $db_nick ?></a>
                                            </td>
                                            <td><?= $row['dis_id'] ?></td>
                                            <td style='text-align:left;'><?= $db_account_name ?></td>
                                            <td style='text-align:left;'><?= $db_account_info ?></td>
                                            <td style='text-align:left;'><?= $db_deposit_account_name ?></td>
                                            <td style='text-align:right;'><?= number_format($row['money']) ?></td>
                                            <td style='text-align:right;'><?= getBonusOptionName($row['bonus_option_idx']) ?></td>
                                            <td style='text-align:right;'><?= number_format($row['bonus_point']) ?> <?=getBonusName($row['set_type'])?></td>
                                            <td style='text-align:right;'><?= number_format($row['manual_bonus_point']) ?></td>
                                            <td><?= $row['create_dt'] ?></td>
                                            <td><?= $row['update_dt'] ?></td>
                                            <td style="width:300px;"><input type="number" name="" id="manualBonusPoint_<?=$row['charge_idx']?>" value="" style="width:120px; margin-right:5px; text-align:center;" placeholder="수동 포인트 입력">
                                                <input type="password" name="" id="secondPassword_<?=$row['charge_idx']?>" value="" style="width:100px; text-align:center;" placeholder="2차비번 입력">
                                                <a href="#" class="btn h25 btn_red" onClick="payManualBonusPoint(<?=$row['charge_idx']?>)">지급</a>
                                            </td>
                                            <td><?= $status_str ?></td>
                                            <td>
                                                <a href="#" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php', 'popmsg', 660, 1000, 'msg', '<?= $db_m_idx ?>');">쪽지</a>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                }
                            }
                        } else {
                            echo "<tr><td colspan='17'>데이터가 없습니다.</tr>";
                        }
                        ?>                    

                        </table>
        <?php
        $requri = explode('?', $_SERVER['REQUEST_URI']);
        $reqFile = basename($requri[0]);
        $default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "&srch_status=" . $p_data['srch_status'] . "";
        $default_link .= "&srch_s_date=" . $p_data['srch_s_date'] . "&srch_e_date=" . $p_data['srch_e_date'] . "&vtype=" . $p_data['vtype'] . " ";
        $default_link .= "&monitor_charge=" . $p_data['monitor_charge']. "&u_business". $p_data['u_business'];

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
            offChargeSound();
            $("#checkbox_css_101").change(function () {
                var fm = document.search;
                if ($("#checkbox_css_101").is(":checked")) {
                    fm.monitor_charge.value = 'Y';
                } else {
                    fm.monitor_charge.value = 'N';
                }

                goSearch();
            });

            $("#checkbox_css_all").change(function () {
                if ($("#checkbox_css_all").is(':checked')) {
                    allCheckFunc(this);
                } else {
                    $("[name=chk]").prop("checked", false);
                }
            });

            $("[name=chk]").change(function () {
                $("[name=checkbox_css_all]").prop("checked", false);
            });
        });

    function offChargeSound(){
        // 이때까지 올라온 사운드 끄기처리
        $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/money_w/_charge_off_sound.php',
                data:{},
                success: function (result) {
                        if(result['retCode'] == "1000"){
                            console.log('처리하였습니다.');
                        }
                },
                error: function (request, status, error) {
                }
        });
    }

        function allCheckFunc(obj) {
            $("[name=chk]").prop("checked", $(obj).prop("checked"));
        }

        /* 체크박스 체크시 전체선택 체크 여부 */
        function oneCheckFunc(obj)
        {
            var allObj = $("[name=checkbox_css_all]");
            var objName = $(obj).attr("name");

            if ($(obj).prop("checked"))
            {
                checkBoxLength = $("[name=" + objName + "]").length;
                checkedLength = $("[name=" + objName + "]:checked").length;

                if (checkBoxLength == checkedLength) {
                    allObj.prop("checked", true);
                } else {
                    allObj.prop("checked", false);
                }
            } else
            {
                allObj.prop("checked", false);
            }
        }

        function setDate(sdate, edate) {
            var fm = document.search;

            fm.srch_s_date.value = sdate;
            fm.srch_e_date.value = edate;
        }

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

        // 1: 입금 전 (신청) 2: 입금 확인 (대기) 3: 충전 완료 (완료) 4: 취소 10: 배팅적중 11:관리자 취소  \\\\n999: 오류
        function setMoney(setidx = 0) {

            var param_url = '/money_w/_set_charge_money.php';

            var str_msg = '';

            var chkboxval = '';

            $("input:checkbox[name='chk']:checked").each(function (index, item) {

                if (index != 0) {
                    chkboxval += ',';
                }
                chkboxval += $(this).val();
            });

            let cash_kind = "입금";

            switch (setidx) {
                case '3':
                    str_msg = '선택하신 유저를 ' + cash_kind + ' 처리 하시겠습니까? \n( ' + cash_kind + ' 요청중인 경우만 승인 처리 됩니다. )';

                    if (chkboxval == '') {
                        alert('유저를 선택해 주세요.');
                        return;
                    }

                    break;
                case '11':
                    str_msg = '선택하신 유저를 ' + cash_kind + ' 취소 하시겠습니까? ( ' + cash_kind + ' 요청중인 경우만 승인 처리 됩니다. )';

                    if (chkboxval == '') {
                        alert('유저를 선택해 주세요.');
                        return;
                    }

                    break;
                case '2':
                    str_msg = '' + cash_kind + ' 요청 중인 전체 유저를 대기 처리 하시겠습니까?';
                    break;
            }


            var result = confirm(str_msg);

            if (result) {

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: param_url,
                    data: {'mtype': setidx, 'chkval': chkboxval},
                    success: function (data) {

                        if (data['retCode'] == 1000) {
                            window.location.reload();
                        }
                        else if(data['retCode'] == "3000"){
                            location.href='/money_w/charge_list.php?srch_status=2';
                            return;
                        }
                        else {
                            alert(data['retMsg']);
                        }
                    },
                    error: function (request, status, error) {
                        alert('서버 오류 입니다.');
                        window.location.reload();
                    }
                });
            }
        }
        
        function payManualBonusPoint(idx){
            let manualBonusPoint = $('#manualBonusPoint_'+idx).val();
            let secondPassword = $('#secondPassword_'+idx).val();
            
            if(0 > manualBonusPoint || '' == manualBonusPoint){
                alert('지급할 포인트를 입력해주세요.');
                return;
            }
            
            if('' == secondPassword){
                alert('2차비번을 입력해주세요.');
                return;
            }
            
            let param_url = '/money_w/_passivePointPayment.php';
            let str_msg = manualBonusPoint + '만큼 포인트를 지급하시겠습니까?';
            var result = confirm(str_msg);

            if (result) {

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: param_url,
                    data: {'idx': idx, 'point': manualBonusPoint, 'second_password':secondPassword},
                    success: function (data) {
                        if (data['retCode'] == 1000) {
                            window.location.reload();
                        }
                        else {
                            alert(data['retMsg']);
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