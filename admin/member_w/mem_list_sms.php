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


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    $p_data['srch_val'] = $MEMAdminDAO->real_escape_string($p_data['srch_val']);
    
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM sms_auth ";
    if($p_data['srch_val'] !='') {
        $p_data['sql'] .= " WHERE phone_number = '".$p_data['srch_val']."' ";
    }
    
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
        $p_data['sql']  = "SELECT idx, phone_number, auth_number, create_dt, status ";
        $p_data['sql'] .= " FROM sms_auth ";
        if($p_data['srch_val'] !='') {
            $p_data['sql'] .= " WHERE phone_number = '".$p_data['srch_val']."' ";
        }
        $p_data['sql'] .= " ORDER BY create_dt DESC";
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page']." ";
        
        
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

$menu_name = "mem_list_sms";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>SMS 인증 내역</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
            <div class="panel_tit">
            	<div class="search_form fl">
            		핸드폰 번호 &nbsp;
                	<div class="">
                        <input type="text" name="srch_val" id="srch_val" class=""  placeholder="검색" value="<?=$p_data['srch_val']?>"/>
                    </div>
                	<div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
            	</div>
            	<div class="search_form fr">
                	총 <?=number_format($total_cnt)?>건
            	</div>
            </div>
</form>            
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>No</th>
                        <th>핸드폰 번호</th>
                        <th>인증 번호</th>
                        <th>인증 일시</th>
                        <th>상태</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            $status = '인증코드요청';
            if($row['status'] == 2)
                $status = '인증완료';
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td><?=$total_cnt-$num?></td>
                        <td><?=$row['phone_number']?></td>
                        <td><?=$row['auth_number']?></td>
                        <td><?=$row['create_dt']?></td>
                        <td><?=$status?></td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
    ?>
					<tr><td colspan="4">데이터가 없습니다.</td></tr>
<?php    
}
?>
                </table>
<?php
$reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$default_link = "$reqFile?srch_val=".$p_data['srch_val']."";
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

function goSearch(vtype=null) {
	var fm = document.search;
	
	fm.method="get";
	fm.submit();	
}
</script>
</html>