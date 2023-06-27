<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');

include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();


if (!isset($_SESSION)) {
    session_start();
}

/*if(0 != $_SESSION['u_business']){
    die();
}*/
    
$selContent = trim(isset($_REQUEST['selContent']) ? $_REQUEST['selContent'] : 1);

$p_data['m_idx'] = trim(isset($_REQUEST['m_idx']) ? $_REQUEST['m_idx'] : 0);
$page = trim(isset($_REQUEST['page']) ?$_REQUEST['page'] : 1);

if ($p_data['m_idx'] < 1) {
    $UTIL->alertClose('회원정보가 없습니다.');
    exit;
}

$db_m_idx = $p_data['m_idx'];

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {
    /*if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }*/
    $page = $MEMAdminDAO->real_escape_string($page);
    $db_m_idx = $MEMAdminDAO->real_escape_string($db_m_idx);
        
    $p_data["table_name"] = " t_message_set ";
    $p_data["sql_where"] = " where use_kind='Y' ";

    $db_total_cnt = $MEMAdminDAO->getTotalCount($p_data);
    $total_cnt_msg_set = 0;
    if (true === isset($db_total_cnt) && false === empty($db_total_cnt)) {
        $total_cnt_msg_set = $db_total_cnt[0]['CNT'];
    }
    if ($total_cnt_msg_set > 0) {
        $db_dataArr_msg_set = $MEMAdminDAO->getMsgSetList($p_data);
    }

    $p_data['sql'] = "select id, nick_name, money, point, betting_p, `call`, recommend_member, is_recommend, status, level, birth, mobile_carrier, auto_level, dis_id ";
    $p_data['sql'] .= ", account_number, account_name, account_bank, is_monitor, is_monitor_charge, is_monitor_security, is_monitor_bet, u_business, recommend_code, g_money ";
    $p_data['sql'] .= " from member where idx=" . $p_data['m_idx'] . " ";
    $db_data_mem = $MEMAdminDAO->getQueryData($p_data);


    //CommonUtil::logWrite("pop_userinfo.php dis_id => : " . $db_data_mem[0]['dis_id'], "error");
    $p_data_status['sql'] = "SELECT STATUS , COUNT(*) as cnt FROM member where recommend_member=" . $p_data['m_idx'] . " GROUP BY STATUS ; ";
    $db_data_status = $MEMAdminDAO->getQueryData($p_data_status);
    $re_user_cnt = $re_out_user_cnt = 0;

    foreach ($db_data_status as $row) {
        switch ($row['STATUS']) {
            case 1:
            case 2:
            case 11: $re_user_cnt += $row['cnt'];
                break;
            case 3: $re_out_user_cnt += $row['cnt'];
                break;
            default: break;
        }
    }

    $p_data_dis['sql'] = "select idx, id, nick_name, u_business from member where u_business in (1,".TOP_DISTRIBUTOR.",".DISTRIBUTOR.",".SUB_DISTRIBUTOR.") ";
    $db_data_dis = $MEMAdminDAO->getQueryData($p_data_dis);

    $p_data_bank['sql'] = "select idx, account_code, account_name from account ";
    $db_data_bank = $MEMAdminDAO->getQueryData($p_data_bank);

    $p_data_memo['sql'] = "select idx, member_idx, m_type, content, reg_time from t_member_memo  ";
    $p_data_memo['sql'] .= " where member_idx=" . $p_data['m_idx'] . " order by idx desc limit 3 ";
    $db_data_memo = $MEMAdminDAO->getQueryData($p_data_memo);
    $db_memo_cnt = 0;
    if (true === isset($db_data_memo) && false === empty($db_data_memo)) {
        $db_memo_cnt = count($db_data_memo);
    }
    if ($db_memo_cnt > 0) {

        switch ($db_data_memo[0]['m_type']) {
            case 1: $memo_type_str = "일반메모";
                $font_color = '';
                break;
            case 2: $memo_type_str = "정보변경";
                $font_color = 'color:#0036FD';
                break;
            case 3: $memo_type_str = "보안주시";
                $font_color = 'color:#FD0C00';
                break;
            default: $memo_type_str = "unknow";
                $font_color = '';
                break;
        }
    }


    $arr_game_type_sql['sql'] = "select lsports_game_type.id,lsports_game_type.ko_name,member_game_type.status from member_game_type
            left join lsports_game_type on member_game_type.game_type = lsports_game_type.id
            where member_game_type.member_idx=" . $p_data['m_idx'] . " and member_game_type.game_type != 12 order by game_type ";

    //$UTIL->logWrite("query_game_type : " . $arr_game_type_sql['sql'], "error");

    $arr_db_result_game_type = $MEMAdminDAO->getQueryData($arr_game_type_sql);
    
    // 개인계좌 정보
    $p_data_personal['sql'] = "select * from personal_account_number where member_idx=" . $p_data['m_idx'];
    $db_data_personal = $MEMAdminDAO->getQueryData($p_data_personal);
    
    if(false === isset($db_data_personal) || 0 === count($db_data_personal)){
        $db_data_personal[0]['account_number_1'] = '';
        $db_data_personal[0]['account_name_1'] = '';
        $db_data_personal[0]['account_number_2'] = '';
        $db_data_personal[0]['account_name_2'] = '';
        $db_data_personal[0]['account_bank_1'] = '';
        $db_data_personal[0]['account_bank_2'] = '';
    }
    
    // 통신사정보
    $p_data_mobile_carrier['sql'] = "select * from mobile_carrier_list";
    $db_data_mobile_carrier = $MEMAdminDAO->getQueryData($p_data_mobile_carrier);
    
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
    <script type="text/javascript" src="/smarteditor28/js/HuskyEZCreator.js" charset="utf-8"></script>
    <!-- <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js" charset="utf-8"></script>  -->
    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admMsg.js" charset="utf-8"></script>
    <!-- 
    <body style="overflow:hidden;">
    -->
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="qna_idx" name="qnaidx">
            <input type="hidden" id="selContent" name="selContent">
        </form>
        <div class="wrap">
            <form id="regform" name="regform" method="post">
                <input type="hidden" id="autonum" name="autonum">
                <input type="hidden" id="m_idx" name="m_idx" value="<?= $p_data['m_idx'] ?>">

                <!-- Contents -->
                <div class="">
                    <!-- list -->
                    <div class="panel reserve" style="min-width: 960px; padding: 10px;">
                        <i class="mte i_group mte-2x vam"></i> <h4>회원 상세 정보</h4>
                        <span style="float:right">
                            <a href="javascript:;" class="btn h30 btn_blu" onClick="popupWinPost('/member_w/pop_userlog.php', 'popuserinfo', 800, 1400, 'userinfo','<?=$db_m_idx?>');">로그</a>
                            <a href="javascript:;" class="btn h30 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php','popmsg',660,1000,'msg','<?=$db_m_idx?>');">쪽지</a>
                            <a href="javascript:;" onclick="setClipboard();" class="btn h30 btn_blu">복 사</a>
                            <a href="javascript:;" onclick="setUserinfo();" class="btn h30 btn_blu">저 장</a>
                            <a href="javascript:self.close();" class="btn h30 btn_mdark" style="color:#fff">닫 기</a>
                        </span>
                        <div class="tline">
                            <table class="mlist">
                                <tr>
                                    <td style="width: 560px; vertical-align: top">
                                        <?php
                                        include_once(_BASEPATH . '/member_w/pop_userinfo_inc.php');
                                        ?>
                                    </td>
                                    <td style="vertical-align: top">
<?php
include_once(_BASEPATH . '/member_w/pop_userinfo_inc_memo.php');
?>
                                        <div style="padding: 5px;"></div>
<?php
include_once(_BASEPATH . '/member_w/pop_userinfo_inc_content.php');
?>
                                    </td>
                                </tr>

                            </table>    
                        </div>
                        <div style="height: 30px"></div>
                    </div>

                    <!-- END list -->
                </div>
                <!-- END Contents -->
            </form>  
        </div>
        <script src="<?= _STATIC_COMMON_PATH ?>/js/sports.js?v=<?php echo date("YmdHis"); ?>"></script>
        <script>
                  function addPoints(points,money,m_idx){
            var param_url = '/member_w/_set_userinfo_money.php';
            var second_pass = $("#second_pass").val();
            money = $('#u_money').val();
            var m_idx = <?=$db_m_idx?>;

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
            function setUserinfo() {

                var u_nick = $('#u_nick').val();
                var u_pass = $('#u_pass').val();
                var u_hp01 = $('#u_hp01').val();
                var u_hp02 = $('#u_hp02').val();
                var u_hp03 = $('#u_hp03').val();
                var u_is_recomm = $('#u_is_recomm').val();
                var u_status = $('#u_status').val();
                var u_level = $('#u_level').val();
                var second_pass = $('#second_pass').val();
                var birth = $('#u_birth').val();
                if (second_pass === '') {
                    alert('2차인증 비번을 넣어주세요.');
                    return;
                }

                var u_autolevel = '';
                var checked_autolevel = document.querySelector('input[name = "u_autolevel"]:checked');

                if (checked_autolevel != null) {
                    u_autolevel = checked_autolevel.value;
                }

                var u_recomm_user = $('#u_recomm_user option:selected').val();

                var u_acc_bank = $('#u_account_bank option:selected').val();
                var u_acc_number = $('#u_account_number').val();
                var u_acc_name = $('#u_account_name').val();
                var u_acc_pass = $('#u_account_pass').val();
                var u_mobile_carrier = $('#u_mobile_carrier option:selected').val();

                var param_val = {
                    m_idx: <?= $db_m_idx ?>, u_nick: u_nick, u_pass: u_pass, u_hp01: u_hp01, u_hp02: u_hp02, u_hp03: u_hp03
                    , u_is_recomm: u_is_recomm, u_status: u_status, u_level: u_level, u_autolevel: u_autolevel, u_recomm_user: u_recomm_user
                    , u_acc_bank: u_acc_bank, u_acc_number: u_acc_number, u_acc_name: u_acc_name, u_acc_pass: u_acc_pass, second_pass: second_pass, birth: birth
                    , u_mobile_carrier: u_mobile_carrier
                };

                var str_msg = '회원 정보를 변경 하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    var getUrl = '/member_w/_set_userinfo.php';

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: getUrl,
                        data: param_val,
                        success: function (data) {

                            if (data['retCode'] == "1000") {
                                window.location.reload();
                            }else if(data['retCode'] == "2002") {
                                alert('2차인증 비번이 틀렸습니다.');
                            }else {
                                alert('적용에 실패하였습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                        }
                    });
                }
            }

            function setDisId() {
                let m_dis_id = $('#u_dis_id option:selected').val();

                var param_val = {
                    m_idx: <?= $db_m_idx ?>, m_dis_id: m_dis_id
                };

                var str_msg = '회원 정보를 변경 하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    var getUrl = '/member_w/_set_user_dis_id.php';

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: getUrl,
                        data: param_val,
                        success: function (data) {

                            if (data['retCode'] == "1000") {
                                alert('변경하였습니다.');
                                //window.location.reload();
                            } else {
                                alert('적용에 실패하였습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                        }
                    });
                }
            }

            function setMonitor(setkind, chval) {
                var str_msg = '';

                if (chval == 'Y') {
                    str_msg = '모니터링 기능을 ON 하시겠습니까?';
                } else if (chval == 'N') {
                    str_msg = '모니터링 기능을 OFF 하시겠습니까?';
                } else {
                    alert('필요 정보가 업습니다.(1)');
                    return;
                }

                var result = confirm(str_msg);
                if (result) {
                    var getUrl = '/member_w/_set_monitor.php';

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: getUrl,
                        data: {'m_idx':<?= $db_m_idx ?>, 'p_setkind': setkind, 'p_change': chval},
                        success: function (data) {

                            if (data['retCode'] == "1000") {
                                window.location.reload();
                            } else {
                                alert('적용에 실패하였습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                        }
                    });
                }
            }



            function setRecommCode(setkind, urecode = null) {
                var str_msg = '';

                urecode = $('#u_recode').val();

                if (setkind == 'r') {
                    str_msg = '추천 코드를 등록 하시겠습니까?';

                    if (urecode == '' || urecode == null) {
                    } else {
                        str_msg = '기존 코드를 삭제 하고 신규 코드로 등록하시겠습니까?'
                    }
                } else if (setkind == 'r2') {
                    str_msg = '추천 코드를 등록 하시겠습니까?';

                    if (urecode == '' || urecode == null) {
                        alert('추천코드를 입력해주세요.');
                        return;
                    }
                } else if (setkind == 'd') {
                    str_msg = '추천 코드를 제거 하시겠습니까?';
                } else {
                    alert('필요 정보가 업습니다.(1)');
                    return;
                }

                var result = confirm(str_msg);
                if (result) {
                    var getUrl = '/member_w/_get_random.php';

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: getUrl,
                        data: {'m_idx':<?= $db_m_idx ?>, 'p_setkind': setkind, 'urecode': urecode},
                        success: function (data) {
                            if (data['retCode'] == "1000") {
                                randVal = data['retData'];
                                $('#u_recode').val(randVal);
                            } else {
                                $('#u_recode').val(urecode);
                                alert('랜덤코드 적용에 실패하였습니다.');
                                $('#u_recode').val(urecode);
                            }
                        },
                        error: function (request, status, error) {
                            alert('랜덤코드 적용에 실패하였습니다.');
                            $('#u_recode').val(urecode);
                        }
                    });
            }
            }

            function setMoneyPoint(mtype, setkind) {
                var money = 0;
                var point = 0;
                var gmoney = 0;
                var comment = '';

                var param_url = '/member_w/_set_userinfo_money.php';

                var str_msg = '';
                var second_pass = $('#second_pass').val();
                if (second_pass === '') {
                    alert('2차인증 비번을 넣어주세요.');
                    return;
                }

                if (mtype == 'money') {

                    money = $('#u_money').val();
                    comment = $('#u_money_comment').val();
                    if(0 == comment.length){
                        alert('변경사유 입력해주세요.');
                        return;
                    }

                    if (!$.isNumeric(money) || (money < 1)) {
                        alert('숫자만 입력 가능 합니다. 입력 값은 0보다 커야 합니다.');
                        $('#u_money').select();
                        $('#u_money').focus();
                        return;
                    }

                    if (setkind == 'p') {
                        str_msg = money + ' 머니를  지급 하시겠습니까?';
                    } else if (setkind == 'm') {
                        str_msg = '-' + money + ' 머니를  차감 하시겠습니까?';
                    } else {
                        alert('필요 정보가 업습니다.(1)');
                        return;
                    }

                } else if (mtype == 'point') {

                    point = $('#u_point').val();
                    comment = $('#u_point_comment').val();
                    if(0 == comment.length){
                        alert('변경사유 입력해주세요.');
                        return;
                    }

                    if (!$.isNumeric(point) || (point < 1)) {
                        alert('숫자만 입력 가능 합니다. 입력 값은 0보다 커야 합니다.');
                        $('#u_point').select();
                        $('#u_point').focus();
                        return;
                    }

                    if (setkind == 'p') {
                        str_msg = point + ' 포인트를  지급 하시겠습니까?';
                    } else if (setkind == 'm') {
                        str_msg = '-' + point + ' 포인트를  차감 하시겠습니까?';
                    } else {
                        alert('필요 정보가 업습니다.(2)');
                        return;
                    }
                } else if (mtype == 'gmoney') {

                    gmoney = $('#u_gmoney').val();
                    comment = $('#u_gmoney_comment').val();
                    if(0 == comment.length){
                        alert('변경사유 입력해주세요.');
                        return;
                    }

                    if (!$.isNumeric(gmoney) || (gmoney < 1)) {
                        alert('숫자만 입력 가능 합니다. 입력 값은 0보다 커야 합니다.');
                        $('#u_gmoney').select();
                        $('#u_gmoney').focus();
                        return;
                    }

                    if (setkind == 'p') {
                        str_msg = gmoney + ' G머니를  지급 하시겠습니까?';
                    } else if (setkind == 'm') {
                        str_msg = '-' + gmoney + ' G머니를  차감 하시겠습니까?';
                    } else {
                        alert('필요 정보가 업습니다.(1)');
                        return;
                    }
                } else {
                    alert('필요 정보가 업습니다.(3)');
                    return;
                }

                var result = confirm(str_msg);
                if (result) {
                    var m_idx = <?= $db_m_idx ?>;

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: param_url,
                        data: {'m_idx': m_idx, 'mtype': mtype, 'mkind': setkind, 'money': money, 'gmoney': gmoney, 'point': point, 'second_pass': second_pass, 'comment': comment},
                        success: function (data) {

                            if (data['retCode'] == "1000") {
                                if (mtype == 'money') {
                                    $('#db_u_money').val(data['retCash']);
                                    $('#u_money').val('');
                                } else if (mtype == 'point') {
                                    $('#db_u_point').val(data['retCash']);
                                    $('#u_point').val('');
                                } else if (mtype == 'gmoney') {
                                    $('#db_u_gmoney').val(data['retCash']);
                                    $('#u_gmoney').val('');
                                }
                                alert('처리했습니다.');
                                //window.location.reload();
                            } else if(data['retCode'] == "2002") {
                                alert('2차인증 비번이 틀렸습니다.');
                            } else {
                                alert('업데이트에 실패 하였습니다.');
                                window.location.reload();
                            }
                        },
                        error: function (request, status, error) {
                            alert('서버 오류 입니다.');
                            window.location.reload();
                        }
                    });
                } else {
                    if (mtype == 'money') {
                        $('#u_money').val('');
                    } else if (mtype == 'point') {
                        $('#u_point').val('');
                    }
                }


            }

            function getUserinfoContent(selContent, tab_num = 0) {

                var m_idx = 0;
                var getUrl = '';

                document.getElementById("pop_userinfo_bbs").style.display = "none";

                switch (selContent) {
                    case '1':
                        getUrl = '/member_w/_pop_userinfo_bet_status.php';
                        break;
                    case '2':
                        getUrl = '/member_w/_pop_userinfo_cash_status.php';
                        break;
                    case '3':
                        getUrl = '/member_w/_pop_userinfo_loginlog.php';
                        break;
                    case '4':
                        getUrl = '/member_w/_pop_userinfo_qna.php';
                        document.getElementById("pop_userinfo_bbs").style.display = "block";
                        getbbs();
                        break;
                    case '5':
                        getUrl = '/member_w/_pop_userinfo_cashlog.php';
                        break;
                    case '6':
                        getUrl = '/member_w/_pop_userinfo_pointlog.php';
                        break;
                    case '7':
                        getUrl = '/member_w/_pop_userinfo_pre_betting_list.php';
                        break;
                    case '8':
                        getUrl = '/member_w/_pop_userinfo_real_betting_list.php';
                        break;
                    case '9':
                        getUrl = '/member_w/_pop_userinfo_minigame.php';
                        break;
                    case '10':
                        getUrl = '/member_w/_pop_userinfo_casino_betting_list.php';
                        // getUrl = '/member_w/_pop_userinfo_casino_betting_list_sb.php';
                        break;
                    case '11':
                        getUrl = '/member_w/_pop_userinfo_slot_betting_list.php';
                        break;
                    case '12':
                        getUrl = '/member_w/_pop_userinfo_esports_betting_list.php';
                        break;
                    case '13':
                        getUrl = '/member_w/_pop_userinfo_kiron_betting_list.php';
                        break;
                    case '14':
                        getUrl = '/member_w/_pop_userinfo_baccara_betting_list.php';
                        break;
                    case '15':
                        getUrl = '/member_w/_pop_userinfo_roulette_betting_list.php';
                        break;
                    case '16':
                        getUrl = '/member_w/_pop_userinfo_highrow_betting_list.php';
                        break;
                    case '17':
                        getUrl = '/member_w/_pop_userinfo_gmoneylog.php';
                        break;
                    case '18':
                        getUrl = '/member_w/_pop_userinfo_item_list.php';
                        break;
                    case '19':
                        getUrl = '/member_w/_pop_userinfo_pre_betting_list.php';
                        break;
                    case '20':
                        getUrl = '/member_w/_pop_userinfo_holdem_betting_list.php';
                        break;
                }

                var no_data = "";
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: getUrl,
                    data: {'m_idx':<?= $db_m_idx ?>, 'p_seltype': selContent, 'tab_num': tab_num, 'page':<?=$page?>},
                    success: function (data) {
                        if (data['retCode'] == "1000") {
                            $("#pop_userinfo_content_1").html(data['retData_1']);
                            $("#pop_userinfo_content_2").html(data['retData_2']);
                        } else {
                            $("#pop_userinfo_content_1").html(no_data);
                            $("#pop_userinfo_content_2").html(no_data);
                        }
                    },
                    error: function (request, status, error) {
                        $("#pop_userinfo_content_1").html(no_data);
                        $("#pop_userinfo_content_2").html(no_data);
                    }
                });

            }
            console.log('selContent : ' + <?= $selContent ?>);
            getUserinfoContent('<?= $selContent ?>');

            function resize(obj) {
                obj.style.height = "1px";
                obj.style.height = (12 + obj.scrollHeight) + "px";
            }

            var oEditors = [];

            function getbbs() {
                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors,
                    elPlaceHolder: "b_content",
                    sSkinURI: "/smarteditor28/SmartEditor2Skin.html",
                    htParams: {
                        bUseToolbar: true, // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                        bUseVerticalResizer: true, // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                        bUseModeChanger: true, // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                        //bSkipXssFilter : true,		// client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
                        //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                        fOnBeforeUnload: function () {
                            //alert("완료!");
                        },
                        SE_EditingAreaManager: {
                            //sDefaultEditingMode : 'HTMLSrc'
                        }
                    }, //boolean
                    fOnAppLoad: function () {
                        //예제 코드 //oEditors.getById["ir1"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 
                    }, fCreator: "createSEditor2"

                });
            }

            function getQna(pseq = 0, ptype = null) {
                var fm = document.popForm;
                fm.qnaidx.value = pseq;
                var answer = "";

                if (pseq > 0) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/member_w/_getset_qna.php',
                        data: {'seq': pseq},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                answer = result['db_answer'];

                                document.getElementById("qna_title_data").innerHTML = result['db_title'];
                                document.getElementById("qna_content_data").innerHTML = result['db_content'];

                                oEditors.getById["b_content"].exec("SET_IR", [""]);
                                oEditors.getById["b_content"].exec("PASTE_HTML", [answer]);


                                return;
                            } else {
                                alert('정보를 가져오지 못했습니다.(1)');
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('정보를 가져오지 못했습니다.(2)');
                            return;
                        }
                    });
            }
            }

            function goQnaAnswer() {
                var fm = document.popForm;

                if ((qidx < 1) || (qidx = '')) {
                    alert('문의내역이 없습니다. 문의내역을 확인해 주세요.');
                    return;
                }

                var str_msg = "답변을 등록 하시겠습니까?";
                oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []);

                var msg_content = $("#b_content").val();
                //alert(msg_content);
                var url_bcontent = encodeURIComponent(msg_content);
                //alert(url_bcontent);

                var result = confirm(str_msg);
                if (result) {
                    var prctype = "answer";
                    var qidx = fm.qnaidx.value;

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/member_w/_getset_qna.php',
                        data: {'seq': qidx, 'ptype': prctype, 'msg_answer': url_bcontent},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('답변을 등록 하였습니다.');
                                getchange('4');
                                return;
                            } else {
                                alert('답변 등록에 실패 하였습니다(1).');
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('답변 등록에 실패 하였습니다(2).');
                            return;
                        }
                    });
                } else {
                    return;
                }
            }

            function getchange(type = null) {
                getUserinfoContent(type);
            }

            function clickTab(tab) {
                getUserinfoContent('2', tab);
            }

            const open_betting_detail = function (idx, is_open) {
                idx = +idx;
                let display = document.getElementById('betting_detail_' + idx).style.display;
                if (display == 'none') {
                    document.getElementById('betting_detail_' + idx).style.display = 'table';
                } else {
                    document.getElementById('betting_detail_' + idx).style.display = 'none';
                }

                if (!is_open) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_menu_10_update_is_open.php',
                        data: {idx: idx},
                        success: function (result) {
                            $('#open_betting_detail_' + idx).attr("onclick", "open_betting_detail(" + idx + ",1)");
                            $('#open_betting_detail_' + idx).attr("onclick", "open_betting_detail(" + idx + ",1)");
                        },
                        error: function (data, status, error) {
                            console.log(JSON.stringify(data));
                            console.log(`req : ` + data);
                            console.log(`status : ` + status);
                            console.log(`error: ` + error);
                            alert('상세내역이 없습니다');
                            return;
                        }
                    });
                }
            }

        // 배팅 전체취소
            const onBetCancel = function (idx, bet_status) {
                if (bet_status > 1) {
                    alert('취소할 수 없습니다.');
                    return;
                }

                var str_msg = '전체취소 하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_menu_bet_cancel_ajax.php',
                        data: {'idx': idx},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('적용하였습니다.');
                                //window.location.reload('/member_w/pop_userinfo.php?selContent=3');
                                window.location.href = '/member_w/pop_userinfo.php?selContent=7&m_idx=<?= $p_data['m_idx'] ?>';
                                return;
                            } else if (result['retCode'] == "-1") {
                                alert('적용할 데이터가 없습니다.');
                                return;
                            } else if (result['retCode'] == "-2") {
                                alert('파라미터가 잘못되었습니다.');
                                return;
                            } else {
                                alert('디비연결이 실패했습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                            return;
                        }
                    });
                }
            }

        // 클립보드 복사
            const copyToClipboard = function (val) {
                const t = document.createElement("textarea");
                document.body.appendChild(t);
                t.value = val;
                t.select();
                document.execCommand('copy');
                document.body.removeChild(t);
            }

            const setClipboard = function () {
                let account_name = '<?= $db_data_mem[0]['account_name'] ?>';
                let nick_name = '<?= $db_data_mem[0]['nick_name'] ?>';
                let account_bank = '<?= $db_data_mem[0]['account_bank'] ?>';
                let account_number = '<?= $db_data_mem[0]['account_number'] ?>';
                let call = '<?= $db_data_mem[0]['call'] ?>';
                let mes = account_name + ' / ' + nick_name + ' / ' + account_bank + ' / ' + account_number + ' / ' + call;

                // 이름/ 닉네임/ 은행명/ 계좌번호/연락처
                copyToClipboard(mes);
                alert('복사되었습니다.');
            }


            function setStatus(setkind, chval, name) {
                var str_msg = '';

                if (chval == 'ON') {
                    str_msg = name + ' 기능을 ON 하시겠습니까?';
                } else if (chval == 'OFF') {
                    str_msg = name + ' 기능을 OFF 하시겠습니까?';
                } else {
                    alert('필요 정보가 업습니다.(1)');
                    return;
                }

                var result = confirm(str_msg);
                if (result) {

                    var getUrl = '/member_w/_set_game_type.php';

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: getUrl,
                        data: {'m_idx':<?= $db_m_idx ?>, 'p_setkind': setkind, 'p_change': chval, 'name': name},
                        success: function (data) {

                            if (data['retCode'] == "1000") {
                                window.location.reload();
                            } else {
                                alert('적용에 실패하였습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                        }
                    });
                } 
            }
            
            const set_personal_account = function (type){
                let account_bank = account_number = account_name = '';
                if(type == 1){
                    account_bank = $("#personal_account_bank_1").val();
                    account_number = $("#personal_account_number_1").val();
                    account_name = $("#personal_account_name_1").val();
                }else{
                    account_bank = $("#personal_account_bank_2").val();
                    account_number = $("#personal_account_number_2").val();
                    account_name = $("#personal_account_name_2").val();
                }
                
                //console.log(account_bank);
                //console.log(account_number);
                //console.log(type);
                
                if(account_bank == ''){
                    alert('은행명이 비어있습니다.');
                    return;
                }
                
                if(account_number == ''){
                    alert('계좌번호가 비어있습니다.');
                    return;
                }
                
                if(account_name == ''){
                    alert('입금자명이 비어있습니다.');
                    return;
                }
                //console.log({'member_idx':<?= $db_m_idx ?>, 'account_number': account_number, 'account_name': account_bank, 'type': type});
                //return;
                
                let str_msg = '';
                if (type == 1) {
                    str_msg = '등록하시겠습니까?';
                } else {
                    str_msg = '수정하시겠습니까?';
                }
                
                var result = confirm(str_msg);
                if (result) {
                    var getUrl = '/member_w/_set_personal_account_number_type.php';
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: getUrl,
                        data: {'member_idx':<?= $db_m_idx ?>, 'account_number': account_number, 'account_name': account_name,'account_bank': account_bank, 'type': type},
                        success: function (data) {
                            if (data['retCode'] == "1000") {
                                alert('적용하였습니다.');
                            } else if (data['retCode'] == "-1001") {
                                alert('잘못된 데이터입니다.');
                            } else {
                                alert('적용에 실패하였습니다.');
                            }
                        },
                        error: function (request, status, error) {
                            alert('적용에 실패하였습니다.');
                        }
                    });
                }
            }
        </script>
    </body>
</html>