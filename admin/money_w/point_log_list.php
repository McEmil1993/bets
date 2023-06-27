<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Cash_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = $today;

$p_data['monitor_charge']= '';

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : _NUM_PER_PAGE);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = _NUM_PER_PAGE;
}

$p_data['srch_m_kind'] = trim(isset($_REQUEST['srch_m_kind']) ? $_REQUEST['srch_m_kind'] : '');
$p_data['srch_code'] = trim(isset($_REQUEST['srch_code']) ? $_REQUEST['srch_code'] : '');
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

$srch_basic = "";
switch($p_data["srch_key"]) {
    case "s_idnick":
        if($p_data['srch_val'] !='') {
            $srch_basic = "  AND (m.id='".$p_data['srch_val']."' OR m.nick_name='".$p_data['srch_val']."') ";
        }
        break;
    case "s_accountname":
        if($p_data['srch_val'] !='') {
            $srch_basic = " AND m.account_name like '%".$p_data['srch_val']."%' ";
        }
        break;
}

if ($p_data['srch_code'] != '') {
    $srch_basic .= "  AND a.ac_code=".$p_data['srch_code']." ";
}

if ($p_data['srch_m_kind'] != '') {
    $srch_basic .= "  AND a.m_kind='".$p_data['srch_m_kind']."' ";
}


$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);

//$p_data['db_srch_s_date'] = date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $p_data['srch_s_date'])));
//$p_data['db_srch_e_date'] = date("Y-m-d 23:59:59", strtotime(str_replace('/', '-', $p_data['srch_e_date'])));
$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']).' 00:00:00';
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']).' 23:59:59';

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if($db_conn) {
    
    if(false === GameCode::checkAdminType($_SESSION,$CASHAdminDAO)){
        die();
    }
    
    $p_data['sql'] = " SELECT count(*) as CNT ";
    $p_data['sql'] .= " FROM t_log_cash a LEFT JOIN member m ON a.member_idx=m.idx ";
    $p_data['sql'] .= " WHERE a.reg_time >= '".$p_data['db_srch_s_date']."' AND  a.reg_time <= '".$p_data['db_srch_e_date']."' ";
    $p_data['sql'] .= " AND ((ac_code IN(5,6,10,11,123,124,131,202,203,301,".USER_PAY_BACK_REWARD_POINT.",".RECOMMENDER_PAY_BACK_REWARD_POINT.",".USER_BET_BACK_REWARD_POINT.",".RECOMMENDER_BET_BACK_REWARD_POINT.",".USER_BET_LOSE_BACK_REWARD_POINT.",".RECOMMENDER_BET_LOSE_BACK_REWARD_POINT.") ";
    $p_data['sql'] .= " AND a.af_point != 0) or ac_code in (10, 124))";
    $p_data['sql'] .= $srch_basic;

    $db_dataArrCnt = $CASHAdminDAO->getQueryData($p_data);

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
        $p_data['sql'] = " SELECT m.idx as m_idx, m.id, m.nick_name ";
        $p_data['sql'] .= ",a.idx as log_idx, a.ac_code, a.r_money, a.be_r_money, a.af_r_money, a.point, a.be_point, a.af_point, a.m_kind ";
        $p_data['sql'] .= ",a.title_kind, a.coment, a.reg_time ";
        $p_data['sql'] .= " FROM t_log_cash a LEFT JOIN member m ON a.member_idx=m.idx ";
        $p_data['sql'] .= " WHERE a.reg_time >= '".$p_data['db_srch_s_date']."' AND  a.reg_time <= '".$p_data['db_srch_e_date']."' ";
        $p_data['sql'] .= " AND ((ac_code IN(5,6,10,11,123,124,131,202,203,301,".USER_PAY_BACK_REWARD_POINT.",".RECOMMENDER_PAY_BACK_REWARD_POINT.",".USER_BET_BACK_REWARD_POINT.",".RECOMMENDER_BET_BACK_REWARD_POINT.",".USER_BET_LOSE_BACK_REWARD_POINT.",".RECOMMENDER_BET_LOSE_BACK_REWARD_POINT.") ";
        $p_data['sql'] .= " AND a.af_point != 0) or ac_code in (10, 124))";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY a.idx DESC " ;
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";

        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
        
        //$UTIL->logWrite($p_data['sql'] ,"error");
    }
    
   
    $CASHAdminDAO->dbclose();
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
<input type="hidden" id="selContent" name="selContent">
</form>
<div class="wrap">
<?php

$menu_name = "point_log";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');

$start_date = date("Y/m/d");
$end_date = date("Y/m/d");
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_monetization_on vam ml20 mr10"></i>
                <h4>포인트 사용 이력</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
<input type="hidden" name="monitor_charge" id="monitor_charge">
            <div class="panel_tit">
            	<div class="search_form fl">
            	
            		<div class="daterange">
                        <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                        <input type="text" class="" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?=$p_data['srch_s_date']?>"/>
                    </div>
                    ~
                    <div class="daterange">
                        <label for="datepicker-autoClose"><i class="mte i_date_range mte-1x vat"></i></label>
                        <input type="text" class="" name="srch_e_date" id="datepicker-autoClose" placeholder="날짜선택"  value="<?=$p_data['srch_e_date']?>"/>
                    </div>
                    <div><a href="javascript:;" onClick="setDate('<?=$today?>','<?=$today?>');" class="btn h30 btn_blu">오늘</a></div>
                    <div><a href="javascript:;" onClick="setDate('<?=$before_week?>','<?=$today?>');" class="btn h30 btn_orange">1주일</a></div>
                    <div><a href="javascript:;" onClick="setDate('<?=$before_month?>','<?=$today?>');" class="btn h30 btn_green">한달</a></div>
                    <div class="" style="padding-right: 10px;"></div>
                	<div class="" style="padding-right: 10px;">
                        <select name="srch_m_kind" id="srch_m_kind">
                            <option value="">전체</option>
                            <option value="P" <?php if($p_data['srch_m_kind']=='P') { echo "selected"; }?>>증가</option>
                            <option value="M" <?php if($p_data['srch_m_kind']=='M') { echo "selected"; }?>>차감</option>
                        </select>
                    </div>
                    <div class="" style="padding-right: 10px;">
                        <select name="srch_code" id="srch_code">
                            <option value="">전체</option>
                            <option value="5" <?php if($p_data['srch_code']==5) { echo "selected"; }?>>포인트전환</option>
                            <option value="6" <?php if($p_data['srch_code']==6) { echo "selected"; }?>>포인트차감</option>
                            <option value="10" <?php if($p_data['srch_code']==10) { echo "selected"; }?>>포인트충전</option>
                            <option value="11" <?php if($p_data['srch_code']==11) { echo "selected"; }?>>낙첨포인트지급</option>
                            <option value="123" <?php if($p_data['srch_code']==123) { echo "selected"; }?>>관리자 포인트 충전</option>
                            <option value="124" <?php if($p_data['srch_code']==124) { echo "selected"; }?>>관리자 포인트 회수</option>
                            <option value="202" <?php if($p_data['srch_code']==202) { echo "selected"; }?>>정산포인트취소</option>
                            <option value="203" <?php if($p_data['srch_code']==203) { echo "selected"; }?>>정산추천인포인트취소</option>
                            <option value="301" <?php if($p_data['srch_code']==301) { echo "selected"; }?>>총판정산포인트지급</option>
                        </select>
                    </div>
                    <div class="" style="padding-right: 10px;">
                        <select name="srch_key" id="srch_key">
                            <option value="s_idnick" <?php if($p_data['srch_key']=='s_idnick') { echo "selected"; }?>>아이디 및 닉네임</option>
                            <option value="s_accountname" <?php if($p_data['srch_key']=='s_accountname') { echo "selected"; }?>>예금주</option>
                        </select>
                    </div>
                    
                    <div class="">
                        <input type="text" name="srch_val" id="srch_val" class=""  placeholder="검색" value="<?=$p_data['srch_val']?>"/>
                    </div>
                	<div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
            	</div>
            	<div class="search_form fr">
                	<div class="checkbox checkbox-css checkbox-inverse">
    					<input type="checkbox" id="checkbox_css_101" name="checkbox_css_101" <?php if($p_data['monitor_charge']=='Y') { echo "checked"; }?> />
    				</div>
            	</div>
            </div>
</form>            
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>번호</th>
                        <th>아이디</th>
                        <th>닉네임</th>
                        <th>사용포인트</th>
                        <th>이전포인트</th>
                        <th>보유포인트</th>
                        <th>내용</th>
                        <th>상세내용</th>
                        <th>처리일자</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            
            $db_id = $row['id'];
            $db_nick = $row['nick_name'];
            
            $db_m_idx = $row['m_idx'];
            
            $db_point = "";
         
            
            
            $db_ac_code_str = "";
            switch ($row['ac_code']) {
                case 5: $db_ac_code_str = "포인트전환"; break;
                case 6: $db_ac_code_str = "포인트차감"; break;
                case 10: $db_ac_code_str = "포인트충전"; break;
                case 11: $db_ac_code_str = "낙첨포인트지급"; break;
                case 123: $db_ac_code_str = "관리자 포인트 충전"; break;
                case 124: $db_ac_code_str = "관리자 포인트 회수"; break;     
                case 202: $db_ac_code_str = "정산포인트취소"; break;
                case 203: $db_ac_code_str = "정산추천인포인트취소"; break;
                case 301: $db_ac_code_str = "총판정산포인트지급"; break;
            }
            
            // money에 값을 넣는 경우가 있다.
            if($row['ac_code'] == 10 || $row['ac_code'] == 124){
                if($row['point'] == 0){
                    $row['point'] = $row['r_money'];
                    $row['be_point'] = $row['be_r_money'];
                    $row['af_point'] = $row['af_r_money'];
                }
            }
            
            $no = $total_cnt-$num;
            if('포인트충전' == $db_ac_code_str){
                $no  = "<font color='blue'><b>".$no."</b></font>";
                $db_id  = "<font color='blue'><b>".$db_id."</b></font>";
                $db_nick  = "<font color='blue'><b>".$db_nick."</b></font>";
                $db_point = "<font color='blue'><b>".number_format($row['point'])."</b></font>";
                $be_point = "<font color='blue'><b>".number_format($row['be_point'])."</b></font>";
                $af_point = "<font color='blue'><b>".number_format($row['af_point'])."</b></font>";
                $coment = "<font color='blue'><b>".$row['coment']."</b></font>";
                $ac_code_str = "<font color='blue'><b>".$db_ac_code_str."</b></font>";
                $reg_time = "<font color='blue'><b>".$row['reg_time']."</b></font>";
            } else if('포인트전환' == $db_ac_code_str){
                $no  = "<font color='red'><b>".$no."</b></font>";
                $db_id  = "<font color='red'><b>".$db_id."</b></font>";
                $db_nick  = "<font color='red'><b>".$db_nick."</b></font>";
                $db_point = "<font color='red'><b>".number_format($row['point'])."</b></font>";
                $be_point = "<font color='red'><b>".number_format($row['be_point'])."</b></font>";
                $af_point = "<font color='red'><b>".number_format($row['af_point'])."</b></font>";
                $coment = "<font color='red'><b>".$row['coment']."</b></font>";
                $ac_code_str = "<font color='red'><b>".$db_ac_code_str."</b></font>";
                $reg_time = "<font color='red'><b>".$row['reg_time']."</b></font>";
            } else{
                $no  = "<font color='black'><b>".$no."</b></font>";
                $db_id  = "<font color='black'><b>".$db_id."</b></font>";
                $db_nick  = "<font color='black'><b>".$db_nick."</b></font>";
                $db_point = "<font color='black'><b>".number_format($row['point'])."</b></font>";
                $be_point = "<font color='black'><b>".number_format($row['be_point'])."</b></font>";
                $af_point = "<font color='black'><b>".number_format($row['af_point'])."</b></font>";
                $coment = "<font color='black'><b>".$row['coment']."</b></font>";
                $ac_code_str = "<font color='black'><b>".$db_ac_code_str."</b></font>";
                $reg_time = "<font color='black'><b>".$row['reg_time']."</b></font>";
            }
            
          
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td><?=$no?></td>
                    	<td style='text-align:left;'>
                    		<a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>');"><?=$db_id?></a>
                    	</td>
                        <td style='text-align:left;'>
                        	<a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>');"><?=$db_nick?></a>
                        </td>
                        <td style='text-align:right;'><?=$db_point?></td>
                        <td style='text-align:right;'><?=$be_point?></td>
                        <td style='text-align:right;'><?=$af_point?></td>
                        <td style='text-align:left;'><?=$ac_code_str?></td>
                        <td style='text-align:left;'><?=$coment?></td>
                        <td><?=$reg_time?></td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
    echo "<tr><td colspan='9'>데이터가 없습니다.</tr>";
}
?>                    

                </table>
<?php 
$requri = explode('?',$_SERVER['REQUEST_URI']);
$reqFile = basename($requri[0]);
$srch_key = !empty($p_data['srch_key']) ? $p_data['srch_key'] : "";
$srch_val = !empty($p_data['srch_val']) ? $p_data['srch_val'] : "";
$srch_code = !empty($p_data['srch_code']) ? $p_data['srch_code'] : "";
$srch_s_date = !empty($p_data['srch_s_date']) ? $p_data['srch_s_date'] : "";
$srch_e_date = !empty($p_data['srch_e_date']) ? $p_data['srch_e_date'] : "";
$vtype = !empty($p_data['vtype']) ? $p_data['vtype'] : "";
$srch_m_kind = !empty($p_data['srch_m_kind']) ? $p_data['srch_m_kind'] : "";
$default_link = "$reqFile?srch_key=".$srch_key."&srch_val=".$srch_val."&srch_code=".$srch_code."";
$default_link .= "&srch_s_date=".$srch_s_date."&srch_e_date=".$srch_e_date."&vtype=".$vtype." ";
$default_link .= "&srch_m_kind=".$srch_m_kind;

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

function goPopupUserinfo(midx, selkind) {
	var fm = document.popForm;

	fm.selContent.value = selkind;

	popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo',midx);
}

function setDate(sdate, edate) {
	var fm = document.search;

	fm.srch_s_date.value = sdate;
	fm.srch_e_date.value = edate;
}

function goSearch() {
	var fm = document.search;

	if((fm.srch_key.value!='') && (fm.srch_val.value=='')) {

		//alert('검색어를 입력해 주세요.');
		//fm.srch_val.focus();
		//return;
	}

	fm.method="get";
	fm.submit();	
}


</script>
</html>