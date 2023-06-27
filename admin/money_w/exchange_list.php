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
$p_data['vtype'] = '';
$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$p_data['monitor_charge'] = trim(isset($_REQUEST['monitor_charge']) ? $_REQUEST['monitor_charge'] : '');
$p_data['srch_status'] = trim(isset($_REQUEST['srch_status']) ? $_REQUEST['srch_status'] : '');
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
$p_data['u_business'] = trim(isset($_REQUEST['u_business']) ? $_REQUEST['u_business'] : 0);

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

if ($p_data['srch_status'] != '') {
    $srch_basic .= "  AND a.status=".$p_data['srch_status']." ";
}

if ($p_data['monitor_charge'] == 'Y') {
    $srch_basic .= "  AND m.is_monitor_charge='Y' ";
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

if($db_conn) {
    // 대총판, 총판 선택시 해당 총파소속 유저들을 가져와야 한다.
    if($p_data['u_business'] > 1){
        $p_data['sql'] = "SELECT id FROM member WHERE u_business = ".$p_data['u_business'];
        $result = $CASHAdminDAO->getQueryData($p_data);
        $disList = array();
        foreach ($result as $key => $value) {
            $disList[] = "'".$value['id']."'";
        }
        $disList = implode(',', $disList);

        $srch_basic .= "  AND m.dis_id in ($disList)";
    }
    
    $p_data['sql'] = " SELECT count(*) as CNT ";
    $p_data['sql'] .= " FROM member_money_exchange_history a LEFT JOIN member m ON a.member_idx=m.idx ";
    $p_data['sql'] .= " WHERE a.create_dt >= '".$p_data['db_srch_s_date']."' AND  a.create_dt <= '".$p_data['db_srch_e_date']."' ";
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
        /*$p_data['sql'] = " SELECT m.idx as m_idx, m.id, m.nick_name, m.level, m.account_name, m.account_bank, m.account_number, m.is_monitor_charge, m.u_business, m.dis_id ";
        $p_data['sql'] .= ",a.idx as charge_idx, a.money, a.status, a.create_dt, a.update_dt, a.delete_dt ";
        $p_data['sql'] .= " FROM member_money_exchange_history a LEFT JOIN member m ON a.member_idx=m.idx ";
        $p_data['sql'] .= " WHERE a.create_dt >= '".$p_data['db_srch_s_date']."' AND  a.create_dt <= '".$p_data['db_srch_e_date']."' ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY a.idx DESC " ;
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";*/
        
        $p_data['sql'] = " SELECT m.idx as m_idx, m.id, m.nick_name, m.level, m.account_name, m.account_bank, m.account_number, m.is_monitor_charge, m.u_business, m.dis_id, m.info_change_dt ";
        $p_data['sql'] .= ",a.idx as charge_idx, a.money, a.status, a.create_dt, a.update_dt, a.delete_dt ";
        
        $p_data['sql'] .= ", ifnull((SELECT sports_bet_s_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as sports_bet_s_money";
        $p_data['sql'] .= ", ifnull((SELECT sports_bet_d_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as sports_bet_d_money";
        $p_data['sql'] .= ", ifnull((SELECT real_bet_s_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as real_bet_s_money";
        $p_data['sql'] .= ", ifnull((SELECT real_bet_d_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as real_bet_d_money";
        $p_data['sql'] .= ", ifnull((SELECT mini_bet_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as mini_bet_money";
        
        $p_data['sql'] .= ", ifnull((SELECT classic_bet_s_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as classic_bet_s_money";
        $p_data['sql'] .= ", ifnull((SELECT classic_bet_d_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as classic_bet_d_money";
        
        $p_data['sql'] .= ", ifnull((SELECT casino_bet_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as casino_bet_money";
        $p_data['sql'] .= ", ifnull((SELECT slot_bet_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as slot_bet_money";
        $p_data['sql'] .= ", ifnull((SELECT esports_bet_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as esports_bet_money";
        $p_data['sql'] .= ", ifnull((SELECT hash_bet_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as hash_bet_money";
        $p_data['sql'] .= ", ifnull((SELECT holdem_bet_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as holdem_bet_money";
        $p_data['sql'] .= ", ifnull((SELECT money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as charge_money";
        $p_data['sql'] .= ", ifnull((SELECT update_dt from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as charge_date";
        $p_data['sql'] .= ", ifnull((SELECT result_money from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as result_money";
        $p_data['sql'] .= ", ifnull((SELECT bonus_option_idx from member_money_charge_history where member_idx = m.idx and status = 3 and update_dt < a.update_dt order by idx desc limit 1),0) as bonus_option_idx";
        $p_data['sql'] .= " FROM member_money_exchange_history a LEFT JOIN member m ON a.member_idx=m.idx ";
        $p_data['sql'] .= " WHERE a.create_dt >= '".$p_data['db_srch_s_date']."' AND  a.create_dt <= '".$p_data['db_srch_e_date']."' ";
        $p_data['sql'] .= $srch_basic;
        $p_data['sql'] .= " ORDER BY a.idx DESC " ;
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";
        $db_dataArr = $CASHAdminDAO->getQueryData($p_data);
    }
    
    // 총판 정보 읽어오기 
    $sql = "select id,name,low_id,high_id from business_type where id <> 1 order by id asc ";
    $db_dists = $CASHAdminDAO->getQueryData_pre($sql, []);

    //Changing distributor id to distributor name
    foreach($db_dataArr as $key => $row)
    {
        $dis_id = $row["dis_id"];
        $new_query["sql"] = "select * from member where id = '$dis_id'";
        $distributor_info = $CASHAdminDAO->getQueryData($new_query);
        $db_dataArr[$key]["distributor_nickname"] = $distributor_info[0]["nick_name"];

    }

    $p_data['sql'] = " SELECT set_type_val FROM t_game_config where set_type in (?,?,?,?,?,?,?) ORDER BY idx ASC";
    $db_bonus_option = $CASHAdminDAO->getQueryData_pre($p_data['sql'], ['first_charge','default_bonus','bonus_option_1','bonus_option_2','bonus_option_3','bonus_option_4','bonus_option_5']);
    
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

$menu_name = "exchange_list";

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
                <h4>환전관리</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
<input type="hidden" name="monitor_charge" id="monitor_charge">
            <div class="panel_tit">
            	<div class="search_form fl">
                    <div class="" style="padding-right: 10px;">
                        <select name="u_business" id="u_business">
                            <option value="0" <?php if (0 == $u_business){echo "selected";} ?>>전체</option>
                            <?php
                            if (!empty($db_dists)) {
                                foreach ($db_dists as $row) {
                                    $id = $row['id'];
                                    $name = $row['name'];
                            ?>
                            <option value="<?= $id ?>" <?php if ($id == $p_data['u_business']) {echo "selected";} ?>><?= $name ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
            	
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
                        <select name="srch_status" id="srch_status">
                            <option value="">전체</option>
                            <option value="1" <?php if($p_data['srch_status']==1) { echo "selected"; }?>>신청</option>
                            <option value="2" <?php if($p_data['srch_status']==2) { echo "selected"; }?>>대기</option>
                            <option value="4" <?php if($p_data['srch_status']==4) { echo "selected"; }?>>취소</option>
                            <option value="3" <?php if($p_data['srch_status']==3) { echo "selected"; }?>>완료</option>
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
            <div class="panel_tit">
            	<div class="search_form fl">
            		<div><a href="javascript:;" onClick="setMoney('exchange','3');" class="btn h30 btn_red">출금 처리</a></div>
                	<div><a href="javascript:;" onClick="setMoney('exchange','11');" class="btn h30 btn_mdark">출금 취소</a></div>
                	<div><a href="javascript:;" onClick="setMoney('exchange','2');" class="btn h30 btn_gray">전체 대기 처리</a></div>
            	</div>
            </div>

            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>
                    		<div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                            	<input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                 <label for="checkbox_css_all"></label>
                            </div>
                        </th>
                    	<th>레벨</th>
                        <th>아이디</th>
                        <th>닉네임</th>
                        <th>소속</th>
                        <th>예금주</th>
                        <th>은행명</th>
                        <th>계좌번호</th>
                        <th>환전금액</th>
                        <th>신청일자</th>
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
            
            $chkbox_css[$i] = "checkbox_css_".$i;
            
            $css_color_id = "";
            $db_id = $row['id'];
            $db_nick = $row['nick_name'];
            
            $db_m_idx = $row['m_idx'];
            
            
            $status_str = "오류";
            switch ($row['status']) {
                case 1:  $status_style = ""; $status_str = "신청"; break;
                case 2: $status_style = "style='background-color:#F7FFB5;'"; $status_str = "대기"; break;
                case 3: $status_style = "style='background-color:#a2e4ff;'"; $status_str = "<font color='red'>완료</font>"; break;
                case 4: $status_style = ""; $status_str = "취소"; break;
                case 11: $status_style = "style='background-color:#ef6963;'";$status_str = "관리자 취소"; break;
            }

            $bonus_display   = "";

            if ($row['bonus_option_idx'] == -1) {
                $bonus_display = $db_bonus_option[0]['set_type_val'];
            }elseif ($row['bonus_option_idx'] == 0) {
                $bonus_display = $db_bonus_option[1]['set_type_val'];
            }elseif ($row['bonus_option_idx'] == 1) {
                $bonus_display = $db_bonus_option[2]['set_type_val'];
            }elseif ($row['bonus_option_idx'] == 2) {
                $bonus_display = $db_bonus_option[3]['set_type_val'];
            }elseif ($row['bonus_option_idx'] == 3) {
                $bonus_display = $db_bonus_option[4]['set_type_val'];
            }elseif ($row['bonus_option_idx'] == 4) {
                $bonus_display = $db_bonus_option[5]['set_type_val'];
            }elseif ($row['bonus_option_idx'] == 5) {
                $bonus_display = $db_bonus_option[6]['set_type_val'];
            }
            
            // 총판이면 파란색표시
            $u_business = $row['u_business'];
            if($u_business == 2 || $u_business == 3)
                $status_style = "style='background-color:#6CC0FF;'";
            
            $db_account_number = $UTIL->getAccountNumberColor($row['account_number']);
            
            // 배팅 비율
            $sports_s_per = $sports_d_per = $real_s_per = $real_d_per = $mini_per = $casino_bet_per = $slo_bet_per = 
            $esports_bet_per = $hash_bet_per = $holdem_bet_per = 0;
            $classic_s_per = $classic_d_per = 0;
            if($row['sports_bet_s_money'] > 0 && $row['charge_money'] > 0)
                $sports_s_per = round($row['sports_bet_s_money']/$row['charge_money']*100);
            
            if($row['sports_bet_d_money'] > 0 && $row['charge_money'] > 0)
                $sports_d_per = round($row['sports_bet_d_money']/$row['charge_money']*100);
            
            if($row['real_bet_s_money'] > 0 && $row['charge_money'] > 0)
                $real_s_per = round($row['real_bet_s_money']/$row['charge_money']*100);
            
            if($row['real_bet_d_money'] > 0 && $row['charge_money'] > 0)
                $real_d_per = round($row['real_bet_d_money']/$row['charge_money']*100);
            
            if($row['mini_bet_money'] > 0 && $row['charge_money'] > 0)
                $mini_per = round($row['mini_bet_money']/$row['charge_money']*100);
            
            if($row['casino_bet_money'] > 0 && $row['charge_money'] > 0)
                $casino_bet_per = round($row['casino_bet_money']/$row['charge_money']*100);
            
            if($row['slot_bet_money'] > 0 && $row['charge_money'] > 0)
                $slo_bet_per = round($row['slot_bet_money']/$row['charge_money']*100);
            
            if($row['esports_bet_money'] > 0 && $row['charge_money'] > 0)
                $esports_bet_per = round($row['esports_bet_money']/$row['charge_money']*100);
            
            if($row['hash_bet_money'] > 0 && $row['charge_money'] > 0)
                $hash_bet_per = round($row['hash_bet_money']/$row['charge_money']*100);
            
            if($row['holdem_bet_money'] > 0 && $row['charge_money'] > 0)
                $holdem_bet_per = round($row['holdem_bet_money']/$row['charge_money']*100);
            
            // classic
            if($row['classic_bet_s_money'] > 0 && $row['charge_money'] > 0)
                $classic_s_per = round($row['classic_bet_s_money']/$row['charge_money']*100);
            
            if($row['classic_bet_d_money'] > 0 && $row['charge_money'] > 0)
                $classic_d_per = round($row['classic_bet_d_money']/$row['charge_money']*100);
            ?>
                    <tr <?= $status_style ?>>
                    	<td>
                    		<div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                            	<input type="checkbox" name="chk" id="<?=$chkbox_css[$i]?>" value="<?=$row['charge_idx']?>" />
                                <label for="<?=$chkbox_css[$i]?>"></label>
                            </div>
                    	</td>
                    	<td><?=$row['level']?></td>
                    	<td style='text-align:left;<?=$css_color_id?>'>
                            <a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>', '2');"><?=$UTIL->getMemberInfoColor($db_id, $row['info_change_dt'], $row['is_monitor_charge'])?></a>
                    	</td>
                        <td style='text-align:left;<?=$css_color_id?>'>
                        	<a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>', '2');"><?=$UTIL->getMemberInfoColor($db_nick, $row['info_change_dt'], $row['is_monitor_charge'])?></a>
                        </td>
                        <td><?= $row['distributor_nickname'] ?></td>
                        <td style='text-align:left;'><?=$UTIL->getMemberInfoColor($row['account_name'], $row['info_change_dt'], $row['is_monitor_charge'])?></td>
                        <td style='text-align:left;'><?=$row['account_bank']?></td>
                        <td style=''><?=$db_account_number?></td>
                        <td style='text-align:right;'><?=number_format($row['money'])?></td>
                        <td><?=$row['create_dt']?></td>
                        <td><?=$row['update_dt']?></td>
                        <td><?=$status_str?></td>
                        <td>
                            <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php','popmsg',660,1000,'msg','<?=$db_m_idx?>');">쪽지</a>
                            <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>','5');">머니내역</a>
                            <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>','7')">베팅내역</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="13" style="text-align: left; line-height: 20px;">
                            <span>마지막충전: <b><?= number_format($row['charge_money']) ?></b></span>
                            <span class="ml10">시간: <b><?= $row['charge_date'] ?></b></span>
                            <span class="ml10">보너스옵션: <b><?= $bonus_display ?></b></span>
                            <span class="ml10">이전보유머니: <b><?= number_format($row['result_money'] - $row['charge_money']) ?></b></span>
                            
                            <br>

                            <span>프리매치 싱글: <b><?= number_format($row['sports_bet_s_money']) ?></b><b>(<?=$sports_s_per?>%)</b></span>
                            <span class="ml10">프리매치 다폴더: <b><?= number_format($row['sports_bet_d_money']) ?></b><b>(<?=$sports_d_per?>%)</b></span>
                            <span class="ml10">실시간 싱글: <b><?= number_format($row['real_bet_s_money']) ?></b><b>(<?=$real_s_per?>%)</b></span>
                            <span class="ml10">실시간 다폴더: <b><?= number_format($row['real_bet_d_money']) ?></b><b>(<?=$real_d_per?>%)</b></span>
                            <span class="ml10">카지노: <b><?= number_format($row['casino_bet_money']) ?></b><b>(<?=$casino_bet_per?>%)</b></span>
                            <span class="ml10">미니게임: <b><?= number_format($row['mini_bet_money']) ?></b><b>(<?=$mini_per?>%)</b></span>
                            <span class="ml10">해쉬게임: <b><?= number_format($row['hash_bet_money']) ?></b><b>(<?=$hash_bet_per?>%)</b></span>
                            <span class="ml10">슬롯머신: <b><?= number_format($row['slot_bet_money']) ?></b><b>(<?=$slo_bet_per?>%)</b></span>
                            <span class="ml10">E스포츠: <b><?= number_format($row['esports_bet_money']) ?></b><b>(<?=$esports_bet_per?>%)</b></span>
                        </td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
    echo "<tr><td colspan='13'>데이터가 없습니다.</tr>";
}
?>                    

                </table>
<?php 
$requri = explode('?',$_SERVER['REQUEST_URI']);
$reqFile = basename($requri[0]);
$default_link = "$reqFile?srch_key=".$p_data['srch_key']."&srch_val=".$p_data['srch_val']."&srch_status=".$p_data['srch_status']."";
$default_link .= "&srch_s_date=".$p_data['srch_s_date']."&srch_e_date=".$p_data['srch_e_date']."&vtype=".$p_data['vtype']." ";
$default_link .= "&monitor_charge=".$p_data['monitor_charge']. "&u_business". $p_data['u_business'];

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
$(document).ready(function(){
    offExchangeSound();
    $("#checkbox_css_all").change(function(){
    	if ($("#checkbox_css_all").is(':checked')) {
            allCheckFunc( this );
        } 
        else {
        	$("[name=chk]").prop("checked", false);
        }
    });

    $("[name=chk]").change(function(){
        $("[name=checkbox_css_all]").prop("checked", false);
    });
});

function offExchangeSound(){
    // 이때까지 올라온 사운드 끄기처리
    $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/money_w/_exchange_off_sound.php',
            data:{},
            success: function (result) {
                    if(result['retCode'] == "1000"){
                            console.log('처리하였습니다.');
                    }
            },
            error: function (request, status, error) {
            }
    });
}
    
function allCheckFunc( obj ) {
	$("[name=chk]").prop("checked", $(obj).prop("checked") );
}

/* 체크박스 체크시 전체선택 체크 여부 */
function oneCheckFunc( obj )
{
	var allObj = $("[name=checkbox_css_all]");
	var objName = $(obj).attr("name");

	if( $(obj).prop("checked") )
	{
		checkBoxLength = $("[name="+ objName +"]").length;
		checkedLength = $("[name="+ objName +"]:checked").length;

		if( checkBoxLength == checkedLength ) {
			allObj.prop("checked", true);
		} 
		else {
			allObj.prop("checked", false);
		}
	}
	else
	{
		allObj.prop("checked", false);
	}
}

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

	//if((fm.srch_key.value!='') && (fm.srch_val.value=='')) {
	//}

	fm.method="get";
	fm.submit();	
}


function setMoney(mkind,setidx=0) {
        var param_url = '/money_w/_set_exchange_money.php';
	
	var str_msg = '';

	var chkboxval = '';
	
	$("input:checkbox[name='chk']:checked").each(function(index,item) {
		
	    if(index!=0) {
	    	chkboxval += ',';
	    }
	    chkboxval += $(this).val();
        console.log(chkboxval);
	});

	if ( (mkind == 'charge') || (mkind == 'exchange') ) {

		var cash_kind = "";

		if (mkind == 'charge') {
			cash_kind = "입금";
		}
		else if (mkind == 'exchange') {
			cash_kind = "출금";
		}
			
		switch(setidx) {
		case '3':
			str_msg = '선택하신 유저를 '+cash_kind+' 처리 하시겠습니까? \n( '+cash_kind+' 요청중인 경우만 승인 처리 됩니다. )';

			if (chkboxval == '') {
				alert('유저를 선택해 주세요.');
				return;
			}
			
			break;
		case '11':
			str_msg = '선택하신 유저를 '+cash_kind+' 취소 하시겠습니까? ( '+cash_kind+' 요청중인 경우만 승인 처리 됩니다. )';

			if (chkboxval == '') {
				alert('유저를 선택해 주세요.');
				return;
			}
			
			break;
		case '2':
			str_msg = ''+cash_kind+' 요청 중인 전체 유저를 대기 처리 하시겠습니까?';
			break;
		}
	}
	else {
		alert('잘못된 선택 방법입니다.');
		return;
	}
	
	var result = confirm(str_msg);
	
    if (result){
        
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'mkind':mkind,'mtype':setidx,'chkval':chkboxval},
    	    success: function (data) {
    	    	console.log(data['retCode'])
                if(data['retCode'] == "200"){
    	    		window.location.reload();
    			}
    	    	if(data['retCode'] == "1000"){
    	    		window.location.reload();
    			}
    	    	else if(data['retCode'] == "2001"){
    	    		alert('잘못된 요청 입니다.');
    			}
                else if(data['retCode'] == "3000"){
    	    		location.href='/money_w/exchange_list.php?srch_status=2';
                    return;
    			}
    	    	else {
        	    	alert('실패 하였습니다.');
    	    	}
    	    },
    	    error: function (request, status, error) {
                consoel.log(error);
    	    	alert('서버 오류 입니다.');
	    		window.location.reload();
    	    }
    	});
    }
	
}
</script>
</html>