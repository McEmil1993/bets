<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');

include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Member_dao.php');



if(0 != $_SESSION['u_business']){
    die();
}

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 20);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 20;
}

$p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
$p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    
$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    
    $p_data["table_name"]=" member ";
    $p_data["sql_where"]="";
    
    $db_total_cnt = $MEMAdminDAO->getTotalCount($p_data);
    
    $total_cnt = $db_total_cnt[0]['CNT'];
    
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];
    
    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지
    
    if ($block >= $total_block) $last_page = $total_page;
    
    if($total_cnt > 0) {
        $db_dataArr = $MEMAdminDAO->getUserList($p_data);
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

$menu_name = "charge_list";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');

$start_date = date("Y/m/d");
$end_date = date("Y/m/d");
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>충전관리</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <div class="panel_tit">
            	<div class="search_form fl">
            	
            		<div class="daterange">
                        <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                        <input type="text" class="" name="start" id="datepicker-default" placeholder="날짜선택" value="<?=$start_date?>"/>
                    </div>
                    ~
                    <div class="daterange">
                        <label for="datepicker-autoClose"><i class="mte i_date_range mte-1x vat"></i></label>
                        <input type="text" class="" name="end" id="datepicker-autoClose" placeholder="날짜선택"  value="<?=$end_date?>"/>
                    </div>
                    <div><a href="" class="btn h30 btn_blu">오늘</a></div>
                    <div><a href="" class="btn h30 btn_orange">1주일</a></div>
                    <div><a href="" class="btn h30 btn_green">한달</a></div>
                    <div class="" style="padding-right: 10px;"></div>
                	<div class="" style="padding-right: 10px;">
                        <select name="" id="">
                            <option value="">전체</option>
                            <option value="">요청</option>
                            <option value="">대기</option>
                            <option value="">취소</option>
                            <option value="">승인</option>
                        </select>
                    </div>
                    <div class="" style="padding-right: 10px;">
                        <select name="" id="">
                            <option value="">아이디 및 닉네임</option>
                            <option value="">예금주</option>
                            <option value="">총판라인</option>
                        </select>
                    </div>
                    
                    <div class="">
                        <input type="text" class=""  placeholder="검색" />
                    </div>
                	<div><a href="" class="btn h30 btn_red">검색</a></div>
            	</div>
            	<div class="search_form fr">
                	<div class="checkbox checkbox-css checkbox-inverse">
    					<input type="checkbox" id="checkbox_css_1" value="" />
    					<label for="checkbox_css_1">충전 모니터링만 보기</label>
    				</div>
            	</div>
            </div>
            <div class="panel_tit">
            	<div class="search_form fl">
                	<div><a href="" class="btn h30 btn_gray">입금 처리</a></div>
                	<div><a href="" class="btn h30 btn_mdark">입금 취소</a></div>
                	<div><a href="" class="btn h30 btn_gray">전체 대기 처리</a></div>
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
                            	<input type="checkbox" id="checkbox_css_1" value="" />
                                 <label for="checkbox_css_1"></label>
                            </div>
                        </th>
                    	<th>레벨</th>
                        <th>아이디</th>
                        <th>닉네임</th>
                        <th>입금자명</th>
                        <th>입금계좌</th>
                        <th>입금금액</th>
                        <th>보너스포인트</th>
                        <th>요청일자</th>
                        <th>처리일자</th>
                        <th>상태</th>
                        <th>쪽지</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td>
                    		<div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                            	<input type="checkbox" id="checkbox_css_1" value="" />
                                 <label for="checkbox_css_1"></label>
                            </div>
                    	</td>
                    	<td><?=$row['level']?></td>
                    	<td style='text-align:left'><?=$row['id']?></td>
                        <td style='text-align:left'><?=$row['nick_name']?></td>
                        <td style='text-align:left'></td>
                        <td style='text-align:left'></td>
                        <td style='text-align:right'><?=number_format($row['money'])?></td>
                        <td style='text-align:right'><?=number_format($row['point'])?></td>
                        <td><?=$row['reg_time']?></td>
                        <td><?=$row['reg_time']?></td>
                        <td></td>
                        <td><a href="javascript:;" class="btn h25 btn_gray">쪽지</a></td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
    
}
?>                    

                </table>
<?php 
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