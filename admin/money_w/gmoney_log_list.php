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


CommonUtil::logWrite("money log search key : " . $p_data["srch_key"], "info");
CommonUtil::logWrite("money log srch_val : " . $p_data['srch_val'], "info");

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

//$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
//$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);


$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

CommonUtil::logWrite("money log srch_basic : " . $srch_basic, "info");

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$CASHAdminDAO)){
        die();
    }
        
    $p_data['sql'] = " SELECT count(*) as CNT ";
    $p_data['sql'] .= " FROM t_log_cash a LEFT JOIN member m ON a.member_idx=m.idx ";
    $p_data['sql'] .= " WHERE a.reg_time >= '".$p_data['db_srch_s_date']."' AND  a.reg_time <= '".$p_data['db_srch_e_date']."' ";
    $p_data['sql'] .= " AND ac_code IN(500,501,502,503,504,505,510,511) ";
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
        $p_data['sql'] .= ",a.title_kind, a.coment, a.reg_time, a.ac_idx ";
        $p_data['sql'] .= ", 0 as `TYPE`, 0 as BET_MNY, 0 as RSLT_MNY, 0 as HLD_MNY, 0 as PRD_ID";
        $p_data['sql'] .= " FROM t_log_cash a LEFT JOIN member m ON a.member_idx=m.idx ";
        $p_data['sql'] .= " WHERE a.reg_time >= '".$p_data['db_srch_s_date']."' AND a.reg_time <= '".$p_data['db_srch_e_date']."' ";
        $p_data['sql'] .= " AND ac_code IN(500,501,502,503,504,505,510,511) ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY reg_time DESC " ;
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";
               
        //CommonUtil::logWrite("gmoney log query : " . $p_data['sql'], "info");
        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    }
    
    $itemList = array();
    $p_data['sql'] = "SELECT id, name, value FROM item WHERE id > 0";
    $item = $CASHAdminDAO->getQueryData($p_data);
    foreach ($item as $key => $value) {
        $itemList[$value['id']] = $value;
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

$menu_name = "gmoney_log";

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
                <h4>지머니 사용 이력</h4>
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
                            <option value="500" <?php if($p_data['srch_code']==500) { echo "selected"; }?>>충전 습득</option>
                            <option value="501" <?php if($p_data['srch_code']==501) { echo "selected"; }?>>게시판 습득</option>
                            <option value="502" <?php if($p_data['srch_code']==502) { echo "selected"; }?>>아이템 사용</option>
                            <option value="503" <?php if($p_data['srch_code']==503) { echo "selected"; }?>>아이템 구매</option>
                            <option value="504" <?php if($p_data['srch_code']==504) { echo "selected"; }?>>관리자 지급</option>
                            <option value="505" <?php if($p_data['srch_code']==505) { echo "selected"; }?>>관리자 회수</option>
                            <option value="511" <?php if($p_data['srch_code']==511) { echo "selected"; }?>>베팅취소(아이템 반환)</option>
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
    					<input type="checkbox" id="checkbox_css_101" name="checkbox_css_101" <?= !empty($p_data['monitor_charge']) && $p_data['monitor_charge'] == 'Y' ? "checked" : "" ?> />
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
                        <th>사용머니</th>
                        <th>이전머니</th>
                        <th>보유머니</th>
                        <th>포인트</th>
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
            
            $db_money = "";
            switch (strtoupper($row['m_kind'])) {
                case 'P': 
                    $db_money = "<font color='red'><b>".number_format($row['r_money'])."</b></font>";
                    break;
                case 'M': 
                    $db_money = "<font color='blue'><b>-".number_format($row['r_money'])."</b></font>";
                    break;
                default:
                    $db_money = number_format($row['r_money']);
                    break;
                
            }
            
            
            $db_ac_code_str = "";
            switch ($row['ac_code']) {
                case 500: $db_ac_code_str = "충전 습득"; break;
                case 501: $db_ac_code_str = "게시판 습득"; break;
                case 502: $db_ac_code_str = "아이템 사용";
                    $row['coment'] .= '('.$itemList[$row['ac_idx']]['name'].')';
                    $db_money = 0;
                    break;
                case 503: $db_ac_code_str = "아이템 구매";
                    $row['coment'] .= '('.$itemList[$row['ac_idx']]['name'].')';
                    $row['point'] = 0;
                    break;
                case 504: $db_ac_code_str = "관리자 지급"; break;
                case 505: $db_ac_code_str = "관리자 회수"; break;
                case 511:
                    $db_money = 0;
                    $row['coment'] .= '('.$itemList[$row['ac_idx']]['name'].')';
                    break;
            }
            
            $no = $total_cnt-$num;
            $be_r_money = $row['be_r_money'];
            $af_r_money = $row['af_r_money'];
            $point = $row['point'];
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td><?=$no?></td>
                    	<td style='text-align:left;'>
                            <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>');"><?=$db_id?></a>
                    	</td>
                        <td style='text-align:left;'>
                            <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>');"><?=$db_nick?></a>
                        </td>
                        <td style='text-align:right;'><?=$db_money?></td>
                        <td style='text-align:right;'><?=number_format($be_r_money)?></td>
                        <td style='text-align:right;'><?=number_format($af_r_money)?></td>
                        <td style='text-align:right;'><?=number_format($row['point'])?></td>
                        <td style='text-align:left;'><?=$db_ac_code_str?></td>
                        <td style='text-align:left;'><?=$row['coment']?></td>
                        <td><?=$row['reg_time']?></td>
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