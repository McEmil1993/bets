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
    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM member_money_charge_sms ";
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
        $p_data['sql']  = "SELECT * ";
        $p_data['sql'] .= " FROM member_money_charge_sms ";
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

$menu_name = "auto_charge_sms";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>SMS 입금 내역</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
            <div class="panel_tit">
            	<div class="search_form fr">
                	총 <?=number_format($total_cnt)?>건
            	</div>
                <a href="#" class="btn h25 btn_blu fr" style="margin-right: 8px;" onClick="onAllComplete()">전체완료</a>
            </div>
</form>            
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>번호</th>
                      
                        <th>도착일자</th>
                        <th>처리일자</th>
                        <th>문자내용</th>
                        <th>처리</th>
                        <th>충전신청정보</th>
                        <th>완료처리</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $currentDate = date("Y-m-d H:i:s");
    //$i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $checkDate = date("Y-m-d H:i:s", strtotime($row['create_dt'] . " +15 minute"));;
            $status = '대기';
            $mes = '';
            $color = '';
            if($row['status'] == 1){
                $status = '대기';
                if($checkDate <= $currentDate){
                    $mes = '수동 완료 처리 또는 시간 초과 완료';
                    $color = 'color:red';
                }
            }else if($row['status'] == 2){
                $status = '완료(시간)';
                $mes = '수동 완료 처리 또는 시간 초과 완료';
                $color = 'color:red';
            }else if($row['status'] == 3){
                $status = '완료';
                $mes = '신청일자 : '.$row['create_dt'].' / 입금액 : '.$row['money'].' / 입금자명 : '.$row['deposit_name'];
            }else if($row['status'] == 4){
                $status = '완료(운영자)';
                $mes = '수동 완료 처리 또는 시간 초과 완료';
                $color = 'color:red';
            }
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td><?=$row['idx']?></td>
                        <td><?=$row['create_dt']?></td>
                        <td><?=$row['update_dt']?></td>
                        <td><?=$row['ori']?></td>
                        <td><?=$status?></td>
                        <td style="<?=$color?>"><?=$mes?></td>
                        <td>
                            <?php if($row['status'] == 1){ ?>
                                <a href="javascript:;" class="btn h25 btn_blu" onClick="onComplete(<?=$row['idx']?>)">완료처리</a>
                            <?php } ?>
                        </td>
                    </tr>
<?php        
            //$i++;
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
<script>
const onComplete = function(idx){
    var str_msg = '완료처리 하시겠습니까?';
    var result = confirm(str_msg);
    if (result){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/money_w/_set_charge_sms_complete.php',
            data:{'idx':idx},
            success: function (result) {
                if(result['retCode'] == "1000"){
                    alert('적용하였습니다.');
                    window.location.href ='/money_w/charge_sms_list.php';
                    return;
                }else{
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

const onAllComplete = function(){
    var str_msg = '전체완료 하시겠습니까?';
    var result = confirm(str_msg);
    if (result){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/money_w/_set_charge_all_sms_complete.php',
            data:{},
            success: function (result) {
                if(result['retCode'] == "1000"){
                    alert('적용하였습니다.');
                    window.location.href ='/money_w/charge_sms_list.php';
                    return;
                }else{
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
</script>
</body>
</html>