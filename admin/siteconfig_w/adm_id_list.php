<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : _NUM_PER_PAGE);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = _NUM_PER_PAGE;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
     //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    $p_data['sql'] = " SELECT a_id, a_nick, a_memo, last_login, a_status, a_ip, a_before_ip, reg_time FROM t_adm_user ";
    $p_data['sql'] .= " ORDER BY last_login DESC " ;
    
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    
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
<body>
<div class="wrap">
    <?php

    $menu_name = "adm_id_list";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
<input type="hidden" name="monitor_charge" id="monitor_charge">
        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>관리자 아이디 관리</h4>
            </a>
        </div>
        <!-- detail search -->
        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※아이디 삭제는 불가능 합니다.
            </div>
            <div style="margin-top: 3px;">
            </div>
            <table class="mlist mline">
                <tr>
                    <th>ID</th>
                    <th>비밀번호</th>
                    <th>닉네임</th>
                    <th>메모</th>
                </tr>
                <tr>
                    <td width="150px" ><input id="aid" name="aid" type="text" class="" style="width: 100%" maxlength="20" placeholder=""/></td>
                    <td width="150px" ><input id="apw" name="apw" type="text" class="" style="width: 100%" maxlength="20" placeholder=""/></td>
                    <td width="150px" ><input id="anick" name="anick" type="text" class="" style="width: 100%" maxlength="20" placeholder=""/></td>
                    <td><input id="amemo" name="amemo" type="text" class="" style="width: 100%" placeholder="내용을 입력해 주세요." /></td>
                </tr>
            </table>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="setAdmID('reg',0,0);" class="btn h30 btn_blu">등 록</a>
                </div>
            </div>
        </div>
        <!-- END detail search -->
</form>
        <!-- list -->
        <div class="panel reserve">
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th width="120px">아이디</th>
                        <th width="120px">닉네임</th>
                        <th width="150px">최종접속일</th>
                        <th>메 모</th>
                        <th width="150px">등록일시</th>
                        <th width="100px">상태변경</th>
                        <th width="80px">삭제</th>
                    </tr>
<?php 

    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $db_id = $row['a_id'];
            
            if ($row['a_status'] == 'Y') {
                $use_str = "미사용";
                $p_status = 'N';
            }
            else {
                $use_str = "사용";
                $p_status = 'Y';
            }
?>                    
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                        <td width="100px" style="text-align: left"><?=$row['a_id']?></td>
                        <td width="100px" style="text-align: left"><?=$row['a_nick']?></td>
                        <td width="100px"><?=$row['last_login']?></td>
                        <td style="text-align: left"><?=$row['a_memo']?></td>
                        <td width="100px"><?=$row['reg_time']?></td>
                        <td width="80px" >
                        	<a href="javascript:;" onClick="setAdmID('edit','<?=$p_status?>','<?=$db_id?>');" class="btn h30 <?php echo($row['a_status'] == 'Y')? 'btn_gray':'btn_green' ?>"><?=$use_str?></a> 
                        </td>
                        <td width="80px" ><a href="javascript:;" onClick="" class="btn h30 btn_red">삭제</a></td>
                    </tr>
<?php        
            //$i++;
        }
    }
else {
?>
					<tr><td colspan="6">데이터가 없습니다.</td></tr>
<?php    
}
?>                    
                </table>
            </div>
        </div>
        <!-- END list -->
    </div>
    <!-- END Contents -->
</div>
<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
<script>

function setAdmID(ptype=null,puse=null,setid=null) {

	var useridChk =/^[a-zA-Z0-9]{4,20}$/;
	var idStr = " * 4~20자의 영문,숫자만 사용가능 합니다.";
	
	var fm = document.search;

	var uid = fm.aid.value;
	var uid_len = fm.aid.length;
	var upw = fm.apw.value;
	var upw_len = fm.apw.length;
	var unick = fm.anick.value;
	var unick_len = fm.anick.length;
	
	var str_msg = '선택하신 ID 를 '+buffstr+' 하시겠습니까?';
	
	var param_url = '/siteconfig_w/_set_admid.php';

	var buffstr = "";

	if (ptype == 'edit') {
		if (puse == 'Y') {
			str_msg = '선택하신 ID 를 사용 등록 하시겠습니까?';
		}
		else if (puse == 'N') {
			str_msg = '선택하신 ID 를 미사용 으로 처리 하시겠습니까?';
		}
	}
	else if (ptype == 'reg') {
		
		
		if ((uid == '') || (uid_len < 4)) {
			alert('ID 를 입력해 주세요.');
			fm.aid.select();
			fm.aid.focus();
			return;
		}

		if(useridChk.test(uid)==false) {
			alert(idStr);
			$("#aid").focus();
			return;
		}
		
		if ((upw == '') || (upw_len < 4)) {
			alert('비밀번호 를 입력해 주세요.');
			fm.apw.select();
			fm.apw.focus();
			return;
		}
		
		if ((unick == '') ||(unick_len < 2)) {
			alert('닉네임을 입력해 주세요.');
			fm.anick.select();
			fm.anick.focus();
			return;
		}

		if ((fm.amemo.value == '') ||(fm.amemo.length < 2)) {
			alert('내용을 입력해 주세요.');
			fm.amemo.select();
			fm.amemo.focus();
			return;
		}
		
		str_msg = '관리자 아이디를 등록 하시겠습니까?';

		setid = uid;
	}
	else {
		alert('잘못된 요청 방식입니다.');
		return;
	}
	
	var result = confirm(str_msg);
	
    if (result){
        
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'ptype':ptype,'puse':puse,'aid':setid,'apw':upw,'anick':unick,'amemo':fm.amemo.value},
    	    success: function (data) {
    	    	
    	    	if(data['retCode'] == "1000"){
    	    		window.location.reload();
    			}
    	    	else if(data['retCode'] == "2001"){
    	    		alert('잘못된 요청 입니다.');
    			}
    	    	else {
        	    	alert('실패 하였습니다.');
    	    		//window.location.reload();
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
