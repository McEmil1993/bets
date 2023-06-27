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
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM t_domain_code ";
    
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
        $p_data['sql'] = " SELECT idx, domain, domain_code, is_use, reg_time FROM t_domain_code ";
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

    $menu_name = "domain_code";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>도메인 코드 관리</h4>
            </a>
        </div>
        <!-- detail search -->
        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※도메인은 반드시 소문자로 입력바랍니다.
            </div>
            <div style="margin-top: 3px;">
            </div>
            <table class="mlist mline">
                <tr>
                    <th>도메인</th>
                    <th>코 드</th>
                </tr>
                <tr>
                    <td><input id="adomain" name="adomain" type="text" class="" style="width: 100%" placeholder="도메인을 입력해 주세요."/></td>
                    <td><input id="adomain_code" name="adomain_code" type="text" class="" style="width: 100%" placeholder="코드를 입력해 주세요." /></td>
                </tr>
            </table>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="setConfig('domain_reg',0);" class="btn h30 btn_blu">등 록</a>
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
                        <th width="300px">도메인</th>
                        <th>코 드</th>
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
                        <td width="200px"><?=$row['domain']?></td>
                        <td style="text-align: left"><?=$row['domain_code']?></td>
                        <td width="150px"><?=$row['reg_time']?></td>
                        <td width="80px" >
                        	<a href="javascript:;" onClick="setConfig('domain_del','<?=$db_idx?>');" class="btn h30 btn_gray">삭제</a> 
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

function setConfig(ptype=null,setidx=0) {

	var fm = document.search;

	var param_url = '/siteconfig_w/_set_config.php';

	var buffstr = "";

	if (ptype == 'domain_del') {
		buffstr = "도메인을 삭제";
	}
	else if (ptype == 'domain_reg') {
		
		if ((fm.adomain.value == '') || (fm.adomain.length < 6)) {
			alert('도메인을  입력해 주세요.');
			fm.adomain.select();
			fm.adomain.focus();
			return;
		}
				
		if ((fm.adomain_code.value == '') ||(fm.adomain_code.length < 2)) {
			alert('도메인 코드를 입력해 주세요.');
			fm.adomain_code.select();
			fm.adomain_code.focus();
			return;
		}
		
		buffstr = "도메인을 등록";
	}
	else {
		alert('잘못된 요청 방식입니다.');
		return;
	}
	
	var str_msg = '선택하신  '+buffstr+' 하시겠습니까?';
	
	var result = confirm(str_msg);
	
    if (result){
        
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'ptype':ptype,'idx':setidx,'adomain':fm.adomain.value,'adomain_code':fm.adomain_code.value},
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
