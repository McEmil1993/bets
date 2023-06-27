<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');

include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

if(0 != $_SESSION['u_business']){
    die();
}

$selContent = trim(isset($_REQUEST['selContent']) ?$_REQUEST['selContent'] : 1);

$p_data['m_dis_id'] = trim(isset($_POST['m_dis_id']) ? $_POST['m_dis_id'] : '');

if ($p_data['m_dis_id'] == '') {
    $UTIL->alertClose('회원정보가 없습니다.');
    exit;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }     
      
    $p_data['m_dis_id'] = $MEMAdminDAO->real_escape_string($p_data['m_dis_id']);
    
    $p_data['sql'] = "select idx, id, nick_name, u_business, money, point, betting_p, `call`, is_recommend, recommend_member, status, level, recommend_code ";
    $p_data['sql'] .= ", account_number, account_name, account_bank,dist_type ";
    $p_data['sql'] .= " from member where id='".$p_data['m_dis_id']."' ";
    $db_data_mem = $MEMAdminDAO->getQueryData($p_data);
    
    if(false === isset($db_data_mem[0])){
        $db_data_mem[0] = array('idx'=>0,'id'=>'','nick_name'=>'','money'=>0,'point'=>0,
            'betting_p'=>0,'call'=>'','is_recommend'=>'','status'=>0,'level'=>0,
            'recommend_code'=>'','account_number'=>'','account_name'=>'','account_bank'=>'');
    }
    
    $db_m_idx = true === isset($db_data_mem[0]['idx']) ? $db_data_mem[0]['idx'] : 0;
    $db_recode = true === isset($db_data_mem[0]['recommend_code']) ? $db_data_mem[0]['recommend_code'] : '';
    
    
    $p_data_status['sql'] = "SELECT STATUS , COUNT(*) as cnt FROM member where recommend_member=$db_m_idx GROUP BY STATUS ; ";
    $db_data_status = $MEMAdminDAO->getQueryData($p_data_status);
    $db_data_status = true === isset($db_data_status) ? $db_data_status : [];
    $re_user_cnt = $re_out_user_cnt = 0;
    
    foreach($db_data_status as $row) {
        switch ($row['STATUS']) {
            case 1:
            case 2:
            case 11: $re_user_cnt += $row['cnt']; break;
            case 3:  $re_out_user_cnt += $row['cnt']; break;
            default:  break;
        }
    }
    
    $p_data_bank['sql'] = "select idx, account_code, account_name from account ";
    $db_data_bank = $MEMAdminDAO->getQueryData($p_data_bank);
    
    
    $p_data_memo['sql'] = "select idx, member_idx, m_type, content, reg_time from t_member_memo  ";
    $p_data_memo['sql'] .= " where member_idx=$db_m_idx order by idx desc limit 1 ";
    $db_data_memo = $MEMAdminDAO->getQueryData($p_data_memo);
    $db_memo_cnt = true === isset($db_data_memo) && false === empty($db_data_memo) ? count($db_data_memo) : 0;
    
    if ($db_memo_cnt > 0) {
        switch ($db_data_memo[0]['m_type']) {
            case 1: $memo_type_str = "일반메모"; $font_color = ''; break;
            case 2: $memo_type_str = "정보변경"; $font_color = 'color:#0036FD'; break;
            case 3: $memo_type_str = "보안주시"; $font_color = 'color:#FD0C00'; break;
            default: $memo_type_str = "unknow"; $font_color = ''; break;
        }
        
        $db_memo_title = $db_data_memo[0]['content'];
    }
         
    // 총판정산정보
    // 현재값
    $p_data['sql'] = "SELECT * ";
    $p_data['sql'] .= " FROM shop_config";
    $p_data['sql'] .= " WHERE member_idx=$db_m_idx";
    $result = $MEMAdminDAO->getQueryData($p_data);
    $result = true === isset($result) ? $result : [];
    if(0 == count($result)){
        $db_dataShopConfig = array('bet_pre_s_fee'=>0, 'bet_pre_d_fee'=>0, 'bet_pre_d_2_fee'=>0, 'bet_pre_d_3_fee'=>0, 'bet_pre_d_4_fee'=>0,
                        'bet_pre_d_5_more_fee'=>0, 'bet_real_s_fee'=>0, 'bet_real_d_fee'=>0, 'bet_mini_fee'=>0,
                        'pre_s_fee'=>0, 'pre_d_fee'=>0, 'real_s_fee'=>0, 'real_d_fee'=>0, 'mini_fee'=>0, 'bet_casino_fee'=>0, 'bet_slot_fee'=>0,
                        'bet_espt_fee'=>0, 'bet_hash_fee'=>0, 'bet_holdem_fee'=>0);
    }else{
        $db_dataShopConfig = $result[0];
    }
    
    $p_data['dis_id'] = !empty($p_data['dis_id']) ? $p_data['dis_id'] : '';
    // recomm
    $p_data['sql'] = " SELECT count(*) as cnt, sum(a.betting_p) as bet_point ";
    $p_data['sql'] .= ",(SELECT COUNT(*) FROM member b WHERE b.recommend_member=a.idx AND b.status=11) AS leave_cnt ";
    $p_data['sql'] .= " FROM member a WHERE a.dis_id='".$p_data['dis_id']."' or a.recommend_member=$db_m_idx";
    
    $db_dataRecommend = $MEMAdminDAO->getQueryData($p_data);
    $db_recomm_tot_cnt = (isset($db_dataRecommend[0]['cnt']) ? $db_dataRecommend[0]['cnt'] : 0);
    $db_recomm_leave_tot_cnt = (isset($db_dataRecommend[0]['leave_cnt']) ? $db_dataRecommend[0]['leave_cnt'] : 0);
    $db_tot_bet_point = (isset($db_dataRecommend[0]['bet_point']) ? $db_dataRecommend[0]['bet_point'] : 0);
    
    
    $p_data['sql'] = "SELECT COUNT(*) as charge_tot_cnt, SUM(a.money) as charge_tot_cash, MAX(a.money) as charge_max_cash ";
    $p_data['sql'] .= " FROM member_money_charge_history a";
    $p_data['sql'] .= " WHERE a.member_idx=$db_m_idx and a.STATUS=3 ";
    
    $db_dataCharge = $MEMAdminDAO->getQueryData($p_data);
    
    $db_charge_tot_cnt = (isset($db_dataCharge[0]['charge_tot_cnt']) ? $db_dataCharge[0]['charge_tot_cnt'] : 0);
    $db_charge_tot_cash = (isset($db_dataCharge[0]['charge_tot_cash']) ? $db_dataCharge[0]['charge_tot_cash'] : 0);
    $db_charge_max_cash = (isset($db_dataCharge[0]['charge_max_cash']) ? $db_dataCharge[0]['charge_max_cash'] : 0);
    
    $p_data['sql'] = "SELECT COUNT(*) as exchange_tot_cnt, SUM(a.money) as exchange_tot_cash, MAX(a.money) as exchange_max_cash ";
    $p_data['sql'] .= " FROM member_money_exchange_history a ";
    $p_data['sql'] .= " WHERE a.member_idx=$db_m_idx and a.STATUS=3 ";
    
    $db_dataExchange = $MEMAdminDAO->getQueryData($p_data);
    
    $db_exchange_tot_cnt = (isset($db_dataExchange[0]['exchange_tot_cnt']) ? $db_dataExchange[0]['exchange_tot_cnt'] : 0);
    $db_exchange_tot_cash = (isset($db_dataExchange[0]['exchange_tot_cash']) ? $db_dataExchange[0]['exchange_tot_cash'] : 0);
    $db_exchange_max_cash = (isset($db_dataExchange[0]['exchange_max_cash']) ? $db_dataExchange[0]['exchange_max_cash'] : 0);
    
    $db_calculate_today = !empty($db_calculate_today) ? $db_charge_today_cash - $db_exchange_today_cash : 0;
    $db_charge_today_cash = !empty($db_charge_today_cash) ? $db_charge_today_cash : 0;
    $db_exchange_today_cash = !empty($db_exchange_today_cash) ? $db_exchange_today_cash : 0;

    $db_calculate_today = $db_charge_today_cash - $db_exchange_today_cash;
    
    $db_calculate_tot = $db_charge_tot_cash - $db_exchange_tot_cash;
          
    
    // 총판 정보 읽어오기 
    $sql = "select id,name,low_id,high_id from business_type where id <> 1 order by id asc ";
    $db_dists = $MEMAdminDAO->getQueryData_pre($sql,[]);
    
    // 총판리스트
    $sql = "select idx, id, nick_name, u_business";
    $sql .= " from member where u_business in (select high_id from business_type where id = ?)";
    $db_dist_list = $MEMAdminDAO->getQueryData_pre($sql,[$db_data_mem[0]['u_business']]);
    
    // 총판 유형 정보 읽어오기 
    $sql = "select * from dist_type where id <> 0 order by id asc ";
    $db_dist_types = $MEMAdminDAO->getQueryData_pre($sql, []);
        
    $MEMAdminDAO->dbclose();
    
    
    
}

?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
<!--<![endif]-->

<?php 
include_once(_BASEPATH.'/common/head.php');
?>
<script>
    $(document).ready(function() {
        App.init();
        FormPlugins.init();
        
        $('ul.tabs li').click(function(){
            var tab_id = $(this).attr('data-tab');

            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');

            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
        })
    });
</script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admCommon.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admMsg.js" charset="utf-8"></script>
<!-- 
<body style="overflow:hidden;">
 -->
<body>
<form id="popForm" name="popForm" method="post">
<input type="hidden" id="seq" name="seq">
<input type="hidden" id="m_idx" name="m_idx">
</form>
<div class="wrap">
<form id="regform" name="regform" method="post">
<input type="hidden" id="autonum" name="autonum">
<input type="hidden" id="m_idx" name="m_idx" value="<?=$db_m_idx?>">

    <!-- Contents -->
    <div class="">
        <!-- list -->
        <div class="panel reserve" style="min-width: 960px; padding: 10px;">
            <i class="mte i_group mte-2x vam"></i> <h4>총판 상세 정보</h4>
            <span style="float:right">
            	<a href="javascript:;" onclick="setDisinfo();" class="btn h30 btn_blu">저 장</a>
            	<a href="javascript:self.close();" class="btn h30 btn_mdark" style="color:#fff">닫 기</a>
            </span>
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <td style="width: 560px; vertical-align: top">
<?php 
include_once(_BASEPATH.'/member_w/pop_disinfo_inc.php');
?>
                        </td>
                        <td style="vertical-align: top">
<?php 
include_once(_BASEPATH.'/member_w/pop_userinfo_inc_memo.php');
?>
                            <div style="padding: 5px;"></div>
<?php 
include_once(_BASEPATH.'/member_w/pop_disinfo_inc_content.php');
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
<script>
$("#u_business").on("change", function(){
    let u_business = $('#u_business option:selected').val();
    let getUrl = '/member_w/_ajax_get_my_high_dis_list.php';
    let param_val = {
            u_business: u_business,
            m_idx: <?=$db_m_idx?>
        };
    let $resultHTML = '';

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: getUrl,
        data:param_val,
        success: function (data) {
            if(data['retCode'] == "1000"){
                $list = data['list'];
                for(let i=0; i<$list.length; i++){
                    $resultHTML += `<option value="${$list[i].idx}">${$list[i].id}</option>`;
                }
                $("#u_recomm_user").html($resultHTML);
            }else if(data['retCode'] == "2000") {
                $resultHTML += `<option value="0">없음</option>`;
                $("#u_recomm_user").html($resultHTML);
            }else {
                alert('실패하였습니다.');
            }
        },
        error: function (request, status, error) {
            alert('실패하였습니다.');
        }
    });
});

function setDisinfo() {
	var u_nick = $('#u_nick').val();
	var u_pass = $('#u_pass').val();
	var u_hp01 = $('#u_hp01').val();
	var u_hp02 = $('#u_hp02').val();
	var u_hp03 = $('#u_hp03').val();
	var u_is_recomm = $('#u_is_recomm').val();
		
	var u_recomm_user = $('#u_recomm_user option:selected').val();

	var u_acc_bank = $('#u_account_bank option:selected').val();
	var u_acc_number = $('#u_account_number').val();
	var u_acc_name = $('#u_account_name').val();
	var u_acc_pass = $('#u_account_pass').val();
        var u_status = $('#u_status').val();
        var u_business = $('#u_business option:selected').val();
        var second_pass = $('#second_pass').val();
        if (second_pass === '') {
            alert('2차인증 비번을 넣어주세요.');
            return;
        }
        
        let dist_types =  $('#dist_types').val();

	var param_val = {
		m_idx: <?=$db_m_idx?>, u_nick: u_nick, u_pass: u_pass, u_hp01: u_hp01, u_hp02: u_hp02,u_hp03: u_hp03
		,u_is_recomm: u_is_recomm, u_recomm_user: u_recomm_user
		,u_acc_bank: u_acc_bank, u_acc_number: u_acc_number, u_acc_name: u_acc_name, u_acc_pass: u_acc_pass
                , u_status: u_status, second_pass: second_pass, u_business: u_business,'dist_types' : dist_types
	};
	
	var str_msg = '회원 정보를 변경 하시겠습니까?';
	var result = confirm(str_msg);
    if (result){
    	var getUrl = '/member_w/_set_disinfo.php';

    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: getUrl,
    	    data:param_val,
    	    success: function (data) {
    	    	
    	    	if(data['retCode'] == "1000"){
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


function setRecommCode(setkind,urecode=null) {
	var str_msg = '';

	urecode = $('#u_recode').val();
	
	if (setkind == 'r') {
		str_msg = '추천 코드를 등록 하시겠습니까?';

		if (urecode=='' || urecode==null) {
		}
		else {
			str_msg = '기존 코드를 삭제 하고 신규 코드로 등록하시겠습니까?'
		}
	}
        else if (setkind == 'r2') {
            str_msg = '수동코드를 등록 하시겠습니까?';
        }
	else if (setkind == 'd') {
		str_msg = '추천 코드를 제거 하시겠습니까?';
	}
	else {
		alert('필요 정보가 업습니다.(1)');
		ruturn;
	}

	var result = confirm(str_msg);
    if (result){
    	var getUrl = '/member_w/_get_random.php';

    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: getUrl,
    	    data:{'m_idx':<?=$db_m_idx?>,'p_setkind':setkind, 'urecode':urecode},
    	    success: function (data) {
    	    	
    	    	if(data['retCode'] == "1000"){
                    randVal = data['retData'];
                    $('#u_recode').val(randVal);
                }
                else if(data['retCode'] == "2001"){
                    alert('추천가능상태가 아닙니다..');
                }
                else if(data['retCode'] == "3001"){
                    alert('이미 사용중인 코드입니다.');
                }
    	    	else {
    	    		alert('랜덤코드 적용에 실패하였습니다.');
    	    		//$('#u_recode').val(urecode);
    	    	}
    	    },
    	    error: function (request, status, error) {
    	    	alert('랜덤코드 적용에 실패하였습니다.');
    	    	$('#u_recode').val(urecode);
    	    }
    	});
    }
}

function setMoneyPoint(mtype,setkind) {
	var money = 0;
	var point = 0;
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
                
		if(!$.isNumeric(money) || (money < 1) ) {
			alert('숫자만 입력 가능 합니다. 입력 값은 0보다 커야 합니다.');
			$('#u_money').select();
			$('#u_money').focus();
			return;
		}

		if (setkind == 'p') {
			str_msg = money + ' 머니를  지급 하시겠습니까?';
		}
		else if (setkind == 'm') {
			str_msg = '-' + money + ' 머니를  차감 하시겠습니까?';
		}
		else {
			alert('필요 정보가 업습니다.(1)');
			ruturn;
		}
		
	}
	else if (mtype == 'point') {

		point = $('#u_point').val();
                comment = $('#u_point_comment').val();
                if(0 == comment.length){
                    alert('변경사유 입력해주세요.');
                    return;
                }
		
		if(!$.isNumeric(point) || (point < 1) ) {
			alert('숫자만 입력 가능 합니다. 입력 값은 0보다 커야 합니다.');
			$('#u_point').select();
			$('#u_point').focus();
			return;
		}

		if (setkind == 'p') {
			str_msg = point + ' 포인트를  지급 하시겠습니까?';
		}
		else if (setkind == 'm') {
			str_msg = '-' + point + ' 포인트를  차감 하시겠습니까?';
		}
		else {
			alert('필요 정보가 업습니다.(2)');
			ruturn;
		}
	}
	else {
		alert('필요 정보가 업습니다.(3)');
		ruturn;
	}

	var result = confirm(str_msg);
    if (result){
        var m_idx = <?=$db_m_idx?>;
        
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'m_idx':m_idx,'mtype':mtype,'mkind':setkind,'money':money,'point':point, 'second_pass': second_pass, 'comment': comment},
    	    success: function (data) {
    	    	if(data['retCode'] == "1000"){
                    if (mtype == 'money') {
                            $('#db_u_money').val(data['retCash']);
                            $('#u_money').val('');
                            alert('처리했습니다.');
                    } else if (mtype == 'point') {
                            $('#db_u_point').val(data['retCash']);
                            $('#u_point').val('');
                    }
                //window.location.reload();
                }else if(data['retCode'] == "2002") {
                    alert('2차인증 비번이 틀렸습니다.');
                }else {
                    alert('업데이트에 실패 하였습니다.');
                    window.location.reload();
    	    	}
    	    },
    	    error: function (request, status, error) {
    	    	alert('서버 오류 입니다.');
	    		window.location.reload();
    	    }
    	});
    }
    else {
    	if (mtype == 'money') {
    		$('#u_money').val('');
    	}
    	else if (mtype == 'point') {
    		$('#u_point').val('');
    	}
    }
}

function getDisinfoContent(selContent) {

	var m_idx = 0;
	var getUrl = '';

	switch(selContent) {
    	case '1': getUrl = '/member_w/_pop_disinfo_content_renew.php';
    		break;
	}
	
	var no_data = "";
	var dis_id = "<?=$p_data['m_dis_id']?>";
        
	$.ajax({
		type: 'post',
		dataType: 'json',
	    url: getUrl,
	    data:{'m_idx':<?=$db_m_idx?>,'dis_id':dis_id,'p_seltype':selContent},
	    success: function (data) {
	    	
	    	if(data['retCode'] == "1000"){
	    		$("#pop_userinfo_content_1").html(data['retData_1']);
	    		$("#pop_userinfo_content_2").html(data['retData_2']);
			}
	    	else {
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

function confirmShopConfig(member_idx){
    let bet_pre_s_fee = $("#set_bet_pre_s_fee").val();
    let bet_pre_d_fee = $("#set_bet_pre_d_fee").val();
    let bet_pre_d_2_fee = $("#set_bet_pre_d_2_fee").val();
    let bet_pre_d_3_fee = $("#set_bet_pre_d_3_fee").val();
    let bet_pre_d_4_fee = $("#set_bet_pre_d_4_fee").val();
    let bet_pre_d_5_more_fee = $("#set_bet_pre_d_5_more_fee").val();
    let bet_real_s_fee = $("#set_bet_real_s_fee").val();
    let bet_real_d_fee = $("#set_bet_real_d_fee").val();
    let bet_mini_fee = $("#set_bet_mini_fee").val();
    let pre_s_fee = $("#set_pre_s_fee").val();
    let bet_casino_fee = $("#set_bet_casino_fee").val();
    let bet_slot_fee = $("#set_bet_slot_fee").val();
    let bet_esports_fee = $("#set_bet_esports_fee").val();
    let bet_hash_fee = $("#set_bet_hash_fee").val();
    let bet_holdem_fee = $("#set_bet_holdem_fee").val();
    let second_pass = $('#second_pass').val();
    if (second_pass === '') {
        alert('2차인증 비번을 넣어주세요.');
        return;
    }
   
    $.ajax({
        url: '/member_w/_set_confirmShopConfig.php',
        type: 'post',
        data: {
            'member_idx': member_idx,
            'bet_pre_s_fee': bet_pre_s_fee,
            'bet_pre_d_fee': bet_pre_d_fee,
            'bet_pre_d_2_fee': bet_pre_d_2_fee,
            'bet_pre_d_3_fee': bet_pre_d_3_fee,
            'bet_pre_d_4_fee': bet_pre_d_4_fee,
            'bet_pre_d_5_more_fee': bet_pre_d_5_more_fee,
            'bet_real_s_fee': bet_real_s_fee,
            'bet_real_d_fee': bet_real_d_fee,
            'bet_mini_fee': bet_mini_fee,
            'pre_s_fee': pre_s_fee,
            'bet_casino_fee': bet_casino_fee,
            'bet_slot_fee': bet_slot_fee,
            'bet_esports_fee': bet_esports_fee,
            'bet_hash_fee': bet_hash_fee,
            'bet_holdem_fee': bet_holdem_fee,
            'second_pass': second_pass
        },
    }).done(function (response) {
        let result  = JSON.parse(response);
        console.log(result['retCode']);
        if(result['retCode'] == "1000"){
            $("#bet_pre_s_fee").val(bet_pre_s_fee);
            $("#bet_pre_d_fee").val(bet_pre_d_fee);
            $("#bet_pre_d_2_fee").val(bet_pre_d_2_fee);
            $("#bet_pre_d_3_fee").val(bet_pre_d_3_fee);
            $("#bet_pre_d_4_fee").val(bet_pre_d_4_fee);
            $("#bet_pre_d_5_more_fee").val(bet_pre_d_5_more_fee);
            $("#bet_real_s_fee").val(bet_real_s_fee);
            $("#bet_real_d_fee").val(bet_real_d_fee);
            $("#bet_mini_fee").val(bet_mini_fee);
            $("#pre_s_fee").val(pre_s_fee);
            $("#bet_casino_fee").val(bet_casino_fee);
            $("#bet_slot_fee").val(bet_slot_fee);
            $("#bet_esports_fee").val(bet_esports_fee);
            $("#bet_hash_fee").val(bet_hash_fee);
            $("#bet_holdem_fee").val(bet_holdem_fee);
            alert('적용되었습니다.');
        }else if(result['retCode'] == "2002"){
            alert('2차인증 비번이 틀렸습니다.');
        }else if(result['retCode'] == "2003"){
            alert('상위총판의 요율설정을 넘을 수 없습니다.');
        }
        return;
    }).fail(function (error) {
        console.log(error);
        alert(error.responseJSON['messages']['messages']);
        return;
    }).always(function (response) {

    });

    $(".member_list_popup02").show();
};

getDisinfoContent('1');
</script>
</body>
</html>