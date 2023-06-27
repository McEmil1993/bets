<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();


$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = $today;
$end_date = $today;


$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $_REQUEST['vtype'] : 'all');
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');

$srch_basic = "";
switch($p_data["srch_key"]) {
    case "s_idnick":
        
        if($p_data['srch_val'] !='') {
            $srch_basic = "  (b.id='".$p_data['srch_val']."' OR b.nick_name='".$p_data['srch_val']."') ";
        }
        break;
    case "s_accountname":
        if($p_data['srch_val'] !='') {
            $srch_basic = " b.account_name like '%".$p_data['srch_val']."%' ";
        }
        break;
    case "s_disline":
        if($p_data['srch_val'] !='') {
            $srch_basic = " b.dis_line_id='".$p_data['srch_val']."' ";
        }
        break;
}


$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);

$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']).' 00:00:00';
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']).' 23:59:59';

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
	// 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM mini_game_bet where markets_id > 0 AND game <> 'b_soccer'";

    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];
    
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];
    
    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지
    
    if ($block >= $total_block) $last_page = $total_page;
    
	// 게시물이 하나 이상이다.
    if($total_cnt > 0) {
        $p_data['sql'] = "SELECT * FROM mini_game_bet_config";
        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }
    
    $betConfig = null;
    foreach($db_dataArr as $row) {
        $betData[$row['level']][$row['game']] = array('min'=>$row['min'], 'max'=>$row['max'], 'limit'=>$row['limit'], 'reward'=>$row['reward']);
    }
    
    $BdsAdminDAO->dbclose();
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
<input type="hidden" id="m_dis_id" name="m_dis_id">
<input type="hidden" id="selContent" name="selContent" value="3">
</form>
<div class="wrap">
<?php

$menu_name = "mini_game_menu3";

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
                <h4>미니게임 설정</h4>
            </a>
        </div>
        
        <!-- detail search -->
        <div class="panel search_box">
            <h5><a href="/mini_game_w/mini_game_config.php">배당설정</a></h5>
            <h5><a href="/mini_game_w/mini_game_bet_config.php"><b>베팅설정</b></a></h5>
        </div>
        <!-- END detail search -->
        
        <!-- list -->
        <div class="panel reserve">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
<input type="hidden" name="vtype" id="vtype" value="<?=$p_data['vtype']?>">
            <div class="panel_tit">
            	<div class="search_form fr">
                	총 <?=number_format($total_cnt)?>건
            	</div>
            	
            </div>
</form>            
            <div class="tline">
                <table class="mlist">
             
                    <tr>
                        <th rowspan="2">레벨</th>
                        <th colspan="4">EOS 파워볼</th>
                        <th colspan="4">엔트리 파워볼</th>
                        <th colspan="4">파워 사다리</th>
                        <th colspan="4">키노 사다리</th>
                        <th colspan="4">가상축구</th>
                    </tr>
                    <tr>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>리워드(%)</th>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>리워드(%)</th>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>리워드(%)</th>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>리워드(%)</th>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>리워드(%)</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($betData)){
        foreach($betData as $key => $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;

            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';" class="small_area">

                        <td><?=$key?></td>
                        <!-- EOS 파워볼 -->
                        <td><input type="text" id="eospb5_min_<?=$key?>" value="<?=$row['eospb5']['min']?>"></td>
                        <td><input type="text" id="eospb5_max_<?=$key?>" value="<?=$row['eospb5']['max']?>"></td>
                        <td><input type="text" id="eospb5_limit_<?=$key?>" value="<?=$row['eospb5']['limit']?>"></td>
                        <td><input type="text" id="eospb5_reward_<?=$key?>" value="<?=$row['eospb5']['reward']?>"></td>
                        <!-- 엔트리 파워볼 -->
                        <td><input type="text" id="powerball_min_<?=$key?>" value="<?=$row['powerball']['min']?>"></td>
                        <td><input type="text" id="powerball_max_<?=$key?>" value="<?=$row['powerball']['max']?>"></td>
                        <td><input type="text" id="powerball_limit_<?=$key?>" value="<?=$row['powerball']['limit']?>"></td>
                        <td><input type="text" id="powerball_reward_<?=$key?>" value="<?=$row['powerball']['reward']?>"></td>
                        <!-- 파워사다리 -->
                        <td><input type="text" id="pladder_min_<?=$key?>" value="<?=$row['pladder']['min']?>"></td>
                        <td><input type="text" id="pladder_max_<?=$key?>" value="<?=$row['pladder']['max']?>"></td>
                        <td><input type="text" id="pladder_limit_<?=$key?>" value="<?=$row['pladder']['limit']?>"></td>
                        <td><input type="text" id="pladder_reward_<?=$key?>" value="<?=$row['pladder']['reward']?>"></td>
                        <!-- 키노사다리 -->
                        <td><input type="text" id="kladder_min_<?=$key?>" value="<?=$row['kladder']['min']?>"></td>
                        <td><input type="text" id="kladder_max_<?=$key?>" value="<?=$row['kladder']['max']?>"></td>
                        <td><input type="text" id="kladder_limit_<?=$key?>" value="<?=$row['kladder']['limit']?>"></td>
                        <td><input type="text" id="kladder_reward_<?=$key?>" value="<?=$row['kladder']['reward']?>"></td>
                        <!-- 가상축구-->
                        <td><input type="text" id="b_soccer_min_<?=$key?>" value="<?=$row['b_soccer']['min']?>"></td>
                        <td><input type="text" id="b_soccer_max_<?=$key?>" value="<?=$row['b_soccer']['max']?>"></td>
                        <td><input type="text" id="b_soccer_limit_<?=$key?>" value="<?=$row['b_soccer']['limit']?>"></td>
                        <td><input type="text" id="b_soccer_reward_<?=$key?>" value="<?=$row['b_soccer']['reward']?>"></td>
                    </tr>
<?php        
            $i++;
        }
    }
    
}
else {
    echo "<tr><td colspan='11'>데이터가 없습니다.</tr>";
}
?>

                </table>
<?php 
$requri = explode('?',$_SERVER['REQUEST_URI']);
$reqFile = basename($requri[0]);
$default_link = "$reqFile?srch_key=".$p_data['srch_key']."&srch_val=".$p_data['srch_val']."";
$default_link .= "&srch_s_date=".$p_data['srch_s_date']."&srch_e_date=".$p_data['srch_e_date']."&vtype=".$p_data['vtype']." ";

//include_once(_BASEPATH.'/common/page_num.php');
?>                
            </div>
            <div style="margin-top: 30px; text-align: right;"><a href="javascript:all_save(<?=count($betData)?>)" class="btn btn_green h30">저장</a></div>
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
// 배당률 수정
function fn_update_betPrice(markets_id) {
    var str_msg = '삭제 하시겠습니까?';
    var result = confirm(str_msg);
    if (result){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/mini_game_w/_mini_game_config_update.php',
            data:{'markets_id':markets_id},
            success: function (result) {
                    if(result['retCode'] == "1000"){
                            alert('수정하였습니다.');
                            window.location.reload();
                            return;
                    }else{
                            alert(result['retMsg']);
                            return;
                    }
            },
            error: function (request, status, error) {
                    alert('수정에 실패하였습니다.');
                    return;
            }
        });
    }
    else {
            return;
    }
}

// 전체수정
function all_save(count){
    //var input_refund_rate = $('#input_refund_rate_'+idx).val();

    let arrBetConfigData = [];
    for(let i=1; i<=count; ++i){
        eospb5_min = $('#eospb5_min_'+i).val();
        eospb5_max = $('#eospb5_max_'+i).val();
        eospb5_limit = $('#eospb5_limit_'+i).val();
        eospb5_reward = $('#eospb5_reward_'+i).val();
        
        powerball_min = $('#powerball_min_'+i).val();
        powerball_max = $('#powerball_max_'+i).val();
        powerball_limit = $('#powerball_limit_'+i).val();
        powerball_reward = $('#powerball_reward_'+i).val();
        
        pladder_min = $('#pladder_min_'+i).val();
        pladder_max = $('#pladder_max_'+i).val();
        pladder_limit = $('#pladder_limit_'+i).val();
        pladder_reward = $('#pladder_reward_'+i).val();
        
        kladder_min = $('#kladder_min_'+i).val();
        kladder_max = $('#kladder_max_'+i).val();
        kladder_limit = $('#kladder_limit_'+i).val();
        kladder_reward = $('#kladder_reward_'+i).val();
        
        b_soccer_min = $('#b_soccer_min_'+i).val();
        b_soccer_max = $('#b_soccer_max_'+i).val();
        b_soccer_limit = $('#b_soccer_limit_'+i).val();
        b_soccer_reward = $('#b_soccer_reward_'+i).val();
        
        let obj = new Object();
        obj = {'level':i, 'powerball_min':powerball_min, 'powerball_max':powerball_max, 'powerball_limit':powerball_limit, 'powerball_reward':powerball_reward
                , 'eospb5_min':eospb5_min, 'eospb5_max':eospb5_max, 'eospb5_limit':eospb5_limit, 'eospb5_reward':eospb5_reward
                , 'pladder_min':pladder_min, 'pladder_max':pladder_max, 'pladder_limit':pladder_limit, 'pladder_reward':pladder_reward
                , 'kladder_min':kladder_min, 'kladder_max':kladder_max, 'kladder_limit':kladder_limit, 'kladder_reward':kladder_reward
                , 'b_soccer_min':b_soccer_min, 'b_soccer_max':b_soccer_max, 'b_soccer_limit':b_soccer_limit, 'b_soccer_reward':b_soccer_reward};
        arrBetConfigData.push(obj);
    }

    let betConfigData = JSON.stringify(arrBetConfigData);
//console.log(betConfigData);    
//return;
    var str_msg = '저장하시겠습니까?';
    var result = confirm(str_msg);
    if (result){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/mini_game_w/_mini_game_bet_config_update_all.php',
            data:{'betConfigData':betConfigData},
            success: function (result) {
                if(result['retCode'] == "1000"){
                    alert('저장하였습니다.');
                    window.location.reload();
                    return;
                }else{
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert('저장에 실패하였습니다.');
                return;
            }
        });
    }
}
</script>
</html>