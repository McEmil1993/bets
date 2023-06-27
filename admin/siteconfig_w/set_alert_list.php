<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if (!isset($_SESSION)) {
    session_start();
}

if(0 != $_SESSION['u_business']){
    CommonUtil::logWrite("[adm_log_list_ubusiness] [error 2200]", "error");
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
        CommonUtil::logWrite("[adm_log_list_checkAdminType] [error 2200]", "error");
        die();
    }
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM notify_setting ";
    
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
        $p_data['sql'] = " SELECT * FROM notify_setting";
        $p_data['sql'] .= " ORDER BY idx ASC " ;
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
<script src="<?=_STATIC_COMMON_PATH?>/js/admCommon.js"></script>
<body>
<form id="popForm" name="popForm" method="post">
<input type="hidden" id="seq" name="seq">
<input type="hidden" id="m_idx" name="m_idx">
</form>
<div class="wrap">
<?php

$menu_name = "set_alert_list";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>알림 문자 설정</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※새 알림 추가.
            </div>
            <div style="margin-top: 3px;">
            </div>
            <table class="mlist mline">
                <tr>
                    <th>사용위치</th>
                    <th>알림내용</th>
                    <th>페이지 라인</th>
                    <th>파일위치</th>
                </tr>
                <tr>
                    <td width="20%" ><input id="location" name="location" type="text" class="" style="width: 100%" placeholder=""/></td>
                    <td width="50%" ><input id="content" name="content" type="text" class="" style="width: 100%" placeholder=""/></td>
                    <td width="10%" ><input id="page_line" name="page_line" type="text" class="" style="width: 100%" placeholder=""/></td>
                    <td width="20%" ><input id="file_location" name="file_location" type="text" class="" style="width: 100%" placeholder=""/></td>
                </tr>
            </table>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="addNewContent();" class="btn h30 btn_blu">등 록</a>
                </div>
            </div>
        </div>
            <!-- <div class="panel_tit">
                <a href="javascript:;" onClick="setConfigWeb('w_config_all');" class="btn h30 btn_blu fr" style="color: white">전체 수정</a>
            </div> -->
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>No</th>
                        <th>사용위치</th>
                        <th>알림내용</th>
                        <th>페이지 라인</th>
                        <th>파일위치</th>
                        <th>수정</th> 
                        
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td width="8%"><?=$row['idx'] ?></td>
                        <td width="20%"><input id="location<?=$row['idx'] ?>" name="location" type="text" class="u_location" style="width: 100%" placeholder="" value="<?=$row['location'] ?>"/></td>
                        <td width="37%" ><input id="content<?=$row['idx'] ?>" name="content" type="text" class="mr10 u_content" style="width: 100%" placeholder="" value="<?=$row['content'] ?>"></td>
                        <td width="8%"><input id="page_line<?=$row['idx'] ?>" name="page_line" type="text" class="u_page_line" style="width: 100%" placeholder="" value="<?=$row['page_line'] ?>"/></td>
                        <td width="17%"><input id="file_location<?=$row['idx'] ?>" name="file_location" type="text" class="u_file_location" style="width: 100%" placeholder="" value="<?=$row['file_location'] ?>"/></td>
                        <td width="10%" ><a href="javascript:;" onClick="updateContent(<?=$row['idx'] ?>);" class="btn h30 btn_blu" style="color: white">수정</a></td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
?>
					<tr><td colspan="5">데이터가 없습니다.</td></tr>
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
<script>

function addNewContent() {

	var location = $('#location').val();
    var content = $('#content').val();
    var file_location = $('#file_location').val();
    var page_line = $('#page_line').val();
	if (location == '') {
		// 위치가 비어 있습니다
        alert('위치가 비어 있습니다.');
	}else if (content == '') {
		// 내용이 비어 있습니다
        alert('내용이 비어 있습니다.');
	}else if (file_location == '')  {
		// 파일 위치가 비어 있습니다.
        alert('파일 위치가 비어 있습니다.');
	}
    else if (page_line == '')  {
		// 파일 위치가 비어 있습니다.
        alert('페이지 줄이 비어 있습니다.');
	}else{
        $.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: '/siteconfig_w/_set_alert_add.php',
    	    data:{'location':location,'content':content,'file_location':file_location,'page_line':page_line},
    	    success: function (data) {
                // console.log(data);
    	    	
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
    	    error: function (error) {
    	    	console.log(error);
	    		// window.location.reload();
    	    }
    	});
    }
	
	
}

function updateContent(idx) {

    var location = $('#location'+idx).val();
    var content = $('#content'+idx).val();
    var file_location = $('#file_location'+idx).val();
    var page_line = $('#page_line'+idx).val();

    if (location == '') {
        // 위치가 비어 있습니다
        alert('위치가 비어 있습니다');
    }else if (content == '') {
        // 내용이 비어 있습니다
        alert('내용이 비어 있습니다');
    }else if (file_location == '')  {
        // 파일 위치가 비어 있습니다.
        alert('파일 위치가 비어 있습니다.');
    }else if (page_line == '')  {
		// 파일 위치가 비어 있습니다.
        alert('페이지 줄이 비어 있습니다.');
	}else{
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/siteconfig_w/_set_alert_update.php',
            data:{'idx':idx,'location':location,'content':content,'file_location':file_location,'page_line':page_line},
            success: function (data) {
                // console.log(data);
        
                if(data['retCode'] == "1000"){
                    alert('수정이 되었습니다.');
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
            error: function (error) {
                console.log(error);
                // window.location.reload();
            }
        });
    }


}
</script>
</body>
</html>