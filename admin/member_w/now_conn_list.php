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


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
  
    $p_data['sql']  = "SELECT a.idx, a.id, a.nick_name, a.u_business, a.money, a.point, a.betting_p, a.is_recommend, a.call ";
    $p_data['sql'] .= ", a.status, a.level, a.auto_level, a.last_login, a.MICRO, a.AG, a.recommend_code, a.recommend_member ";
    $p_data['sql'] .= ", a.account_number, a.account_name, a.account_bank, a.is_monitor, a.is_monitor_charge, a.is_monitor_security ";
    $p_data['sql'] .= ", a.is_monitor_bet, a.dis_id, a.dis_line_id, a.reg_time ";
    $p_data['sql'] .= ", (SELECT b.id FROM member b WHERE b.idx=a.recommend_member) AS re_id ";
    $p_data['sql'] .= ", (SELECT SUM(c.money) FROM member_money_charge_history c WHERE c.member_idx=a.idx AND c.status=3) AS ch_sum_money ";
    $p_data['sql'] .= ", (SELECT SUM(d.money) FROM member_money_exchange_history d WHERE d.member_idx=a.idx AND d.status=3) AS ex_sum_money ";
    $p_data['sql'] .= ", g.ip as login_ip, g.country as login_country, g.login_domain, g.login_datetime ";
    $p_data['sql'] .= " FROM member a left join member_login_history g on g.idx=(select MAX(idx) from member_login_history as h where h.member_idx=a.idx and login_yn='Y') ";
    $p_data['sql'] .= " WHERE a.session_key is not null ";
    $p_data['sql'] .= " order by g.login_datetime desc ";

    
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
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

$menu_name = "mem_now_conn";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>현재 접속자</h4>
            </a>
        </div>
        
        <!-- list -->
        <div class="panel reserve">       
        	<div class="panel_tit">
            	<div class="search_form fr">
                	총 <?=number_format(count($db_dataArr))?>건
            	</div>
            </div>
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>아이디</th>
                        <th>닉네임</th>
                        <th>레벨</th>
                        <th>머니</th>
                        <th>포인트</th>
                        <th>입금</th>
                        <th>출금</th>
                        <th>로그인일시</th>
                        <th>아이피</th>
                        <th>도메인</th>
                        <th>국가</th>
                        <th>라인</th>
                        <th>기능</th>
                    </tr>
<?php 

    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            $db_m_idx = $row['idx'];
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                    	<td style='text-align:left'>
                        	<a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>');"><?=$row['id']?></a>
                        </td>
                        <td style='text-align:left'>
                        	<a href="javascript:;" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_m_idx?>');"><?=$row['nick_name']?></a>
                        </td>
                        <td><?=$row['level']?></td>
                        <td style='text-align:right'><?=number_format($row['money'])?></td>
                        <td style='text-align:right'><?=number_format($row['point'])?></td>
                        <td style='text-align:right'><?=number_format($row['ch_sum_money'])?></td>
                        <td style='text-align:right'><?=number_format($row['ex_sum_money'])?></td>
                        <td><?=$row['login_datetime']?></td>
                        <td><?=$row['login_ip']?></td>
                        <td style='text-align:left'><?=$row['login_domain']?></td>
                        <td style='text-align:left'><?=$row['login_country']?></td>
                        <td></td>
                        <td>
                        	<a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php','popmsg',660,1000,'msg','<?=$db_m_idx?>');">쪽지</a>
                        </td>
                    </tr>
<?php        
            $i++;
        }
    }
    else {
        ?>
					<tr><td colspan="13
					">데이터가 없습니다.</td></tr>
<?php    
    }
?>
                </table>                
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