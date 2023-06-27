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
    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM member_ip_block_history";
    
    $db_dataArrCnt = $MEMAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];
    
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];
    
    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지
    
    if ($block >= $total_block) $last_page = $total_page;
    
    if($total_cnt > 0) {
        $p_data['sql'] = " SELECT idx, ip, memo, create_dt FROM member_ip_block_history";
        $p_data['sql'] .= " ORDER BY idx DESC " ;
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";
        
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

    $menu_name = "ip_block";

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
                <h4>개별 IP 차단 관리</h4>
            </a>
        </div>
        <!-- detail search -->
        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※등록된 IP는 로그인 여부와 무관하게 고객(Front) 페이지 접근이 불가능 합니다.
            </div>
            <div style="margin-top: 3px;">
            </div>
            <table class="mlist mline">
                <tr>
                    <th>IP</th>
                    <th>메모</th>
                </tr>
                <tr>
                    <td width="150px" ><input id="aip" name="aip" type="text" class="" style="width: 100%" placeholder=""/></td>
                    <td><input id="amemo" name="amemo" type="text" class="" style="width: 100%" placeholder="내용을 입력해 주세요." /></td>
                </tr>
            </table>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="setAdmIP('block_reg',0);" class="btn h30 btn_blu">등 록</a>
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
                        <th width="80px">번호</th>
                        <th width="200px">IP</th>
                        <th>메 모</th>
                        <th width="150px">등록일시</th>
                        <th width="80px">삭제</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            
            $db_idx = $row['idx'];
?>                    
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                        <td width="80px" ><?=$total_cnt-$num?></td>
                        <td width="200px"><?=$row['ip']?></td>
                        <td style="text-align: left"><?=$row['memo']?></td>
                        <td width="150px"><?=$row['create_dt']?></td>
                        <td width="80px" >
                        	<a href="javascript:;" onClick="setAdmIP('block_del','<?=$db_idx?>');" class="btn h30 btn_gray">삭제</a> 
                        </td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
?>
					<tr><td colspan="6">데이터가 없습니다.</td></tr>
<?php    
}
?>                    
                </table>
<?php
$reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$default_link = "$reqFile?srch_key=";
include_once(_BASEPATH.'/common/page_num.php');
?>
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

function ValidateIPaddress(pVal) {
    var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    if (ipformat.test(pVal)) {
        return true;
    } else {
        alert("입력하신 값은 IP형식이 아닙니다.");
        return false;
    }
}

function setAdmIP(ptype=null,setidx=0) {

	var fm = document.search;

	var param_url = '/siteconfig_w/_set_admip.php';

	var buffstr = "";

	if (ptype == 'block_del') {
		buffstr = "삭제";
	}
	else if (ptype == 'block_reg') {
		
		if ((fm.aip.value == '') || (fm.aip.length < 6)) {
			alert('IP 를 입력해 주세요.');
			fm.aip.select();
			fm.aip.focus();
			return;
		}
		else {
			var bchk = ValidateIPaddress(fm.aip.value);
			if (bchk == false) {
				alert('IP 를 입력해 주세요.');
				fm.aip.select();
				fm.aip.focus();
				return;
			}
		}

		
		if ((fm.amemo.value == '') ||(fm.amemo.length < 2)) {
			alert('내용을 입력해 주세요.');
			fm.amemo.select();
			fm.amemo.focus();
			return;
		}
		
		buffstr = "등록";
	}
	else {
		alert('잘못된 요청 방식입니다.');
		return;
	}
	
	var str_msg = '선택하신 IP를 '+buffstr+' 하시겠습니까?';
	
	var result = confirm(str_msg);
	
    if (result){
        
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'ptype':ptype,'idx':setidx,'aip':fm.aip.value,'amemo':fm.amemo.value},
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
