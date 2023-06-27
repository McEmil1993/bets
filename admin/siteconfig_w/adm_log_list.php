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
    
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM t_adm_log ";
    $p_data['sql'] .= " WHERE log_type IN(1) ";
    
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
        $p_data['sql'] = " SELECT a_id, a_ip, a_country, is_login, reg_time FROM t_adm_log ";
        $p_data['sql'] .= " WHERE log_type IN(1) ";
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
<script src="<?=_STATIC_COMMON_PATH?>/js/admCommon.js"></script>
<body>
<form id="popForm" name="popForm" method="post">
<input type="hidden" id="seq" name="seq">
<input type="hidden" id="m_idx" name="m_idx">
</form>
<div class="wrap">
<?php

$menu_name = "adm_log_list";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>관리자 접속 내역</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>No</th>
                        <th>관리자 ID</th>
                        <th>접속 IP</th>
                        <th>접속 국가</th>
                        <th>접속 일시</th>
                        <th>성공 여부</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            
            $db_is_login = '';
            switch ($row['is_login']) {
                case 'Y': $db_is_login = '성공'; break;
                case 'N': $db_is_login = "<font color='red'>실패</font>"; break;
            }
            
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td><?=$total_cnt-$num?></td>
                        <td style='text-align:left'><?=$row['a_id']?></td>
                        <td style=''><?=$row['a_ip']?></td>
                        <td style='text-align:left'><?=$row['a_country']?></td>
                        <td style=''><?=$row['reg_time']?></td>
                        <td><?=$db_is_login?></td>
                        
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
</html>