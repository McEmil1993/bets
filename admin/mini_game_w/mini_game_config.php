<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end



$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = $today;
$end_date = $today;


$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 10);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 20;
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

$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']);
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']);

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
    $sql = "SELECT set_type, set_type_val FROM t_game_config where set_type in ('mini_powerball_deadline','mini_power_ladder_deadline',"
            . "'mini_kino_ladder_deadline')";
    $arr_config_result = $BdsAdminDAO->getQueryData_pre($sql,[]);
    $arr_config = array();
    foreach ($arr_config_result as $key => $value) {
        $arr_config[$value['set_type']] = $value['set_type_val'];
    }

    // 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM mini_game_bet where game <> 'b_soccer'";
   
    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];
    
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];
    
    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block       = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지
    
    if ($block >= $total_block) $last_page = $total_page;
    
	// 게시물이 하나 이상이다.
    if($total_cnt > 0) {
        $p_data['sql'] = "SELECT * FROM mini_game_bet where game <> 'b_soccer'";
        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }
    
    $betData = null;
    foreach($db_dataArr as $row) {
        $betData[$row['markets_id']] = $row['bet_price'];
         
        /*if($row['markets_id'] == 10001){
            $betData[10001] = $row['bet_price'];
        }else if($row['markets_id'] == 10003){
            $betData[10003] = $row['bet_price'];
        }else if($row['markets_id'] == 10005){
            $betData[10005] = $row['bet_price'];
        }else if($row['markets_id'] == 10006){
            $betData[10006] = $row['bet_price'];
        }else if($row['markets_id'] == 10007){
            $betData[10007] = $row['bet_price'];
        }else if($row['markets_id'] == 11001){
            $betData[11001] = $row['bet_price'];
        }else if($row['markets_id'] == 11003){
            $betData[11003] = $row['bet_price'];
        }else if($row['markets_id'] == 11005){
            $betData[11005] = $row['bet_price'];
        }else if($row['markets_id'] == 12001){
            $betData[12001] = $row['bet_price'];
        }else if($row['markets_id'] == 12003){
            $betData[12003] = $row['bet_price'];
        }else if($row['markets_id'] == 12005){
            $betData[12005] = $row['bet_price'];
        }else if($row['markets_id'] == 14001){
            $betData[14001] = $row['bet_price'];
        }else if($row['markets_id'] == 14003){
            $betData[14003] = $row['bet_price'];
        }else if($row['markets_id'] == 14005){
            $betData[14005] = $row['bet_price'];
        }else if($row['markets_id'] == 14006){
            $betData[14006] = $row['bet_price'];
        }else if($row['markets_id'] == 14007){
            $betData[14007] = $row['bet_price'];
        }*/
    }
    //print_r($db_dataArr);
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

$menu_name = "mini_game_menu4";

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
            <h5><a href="/mini_game_w/mini_game_config.php"><b>배당설정</b></a></h5>
            <h5><a href="/mini_game_w/mini_game_bet_config.php">베팅설정</a></h5>
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
                <!--<tr>
                    	<th>게임</th>
                        <th>마켓명</th>
                        <th>배당률</th>
                        <th>수정</th>
                    </tr>-->
                <tr>
                    <th colspan="6">EOS 파워볼</th>
                    <th colspan="6">엔트리 파워볼</th>
                    <th colspan="4">파워 사다리</th>
                    <th colspan="4">키노 사다리</th>
                </tr>
                <tr>
                    <th>마감</th>
                    <th>언오버</th>
                    <th>(파)홀짝</th>
                    <th>소</th>
                    <th>중</th>
                    <th>대</th>
                    <th>마감</th>
                    <th>언오버</th>
                    <th>(파)홀짝</th>
                    <th>소</th>
                    <th>중</th>
                    <th>대</th>
                    <th>마감</th>
                    <th>홀짝</th>
                    <th>34</th>
                    <th>좌우</th>
                    <th>마감</th>
                    <th>홀짝</th>
                    <th>34</th>
                    <th>좌우</th>
                </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
            //print_r($row);
            ?>
            <tbody id="bet_info">
                <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';" class="small_area">
                    <td><input type="text" id="pb_end_time" value="<?=$arr_config['mini_powerball_deadline']?>"></td>
                    <td><input type="text" id="10003" value="<?=$betData[10003]?>"></td>
                    <td><input type="text" id="10001" value="<?=$betData[10001]?>"></td>
                    <td><input type="text" id="10005" value="<?=$betData[10005]?>"></td>
                    <td><input type="text" id="10006" value="<?=$betData[10006]?>"></td>
                    <td><input type="text" id="10007" value="<?=$betData[10007]?>"></td>
                    <td><input type="text" id="pb_end_time" value="<?=$arr_config['mini_powerball_deadline']?>"></td>
                    <td><input type="text" id="14003" value="<?=$betData[14003]?>"></td>
                    <td><input type="text" id="14001" value="<?=$betData[14001]?>"></td>
                    <td><input type="text" id="14005" value="<?=$betData[14005]?>"></td>
                    <td><input type="text" id="14006" value="<?=$betData[14006]?>"></td>
                    <td><input type="text" id="14007" value="<?=$betData[14007]?>"></td>
                    
                    <td><input type="text" id="pladder_end_time" value="<?=$arr_config['mini_power_ladder_deadline']?>"></td>
                    <td><input type="text" id="11005" value="<?=$betData[11005]?>"></td>
                    <td><input type="text" id="11003" value="<?=$betData[11003]?>"></td>
                    <td><input type="text" id="11001" value="<?=$betData[11001]?>"></td>
                    <td><input type="text" id="kladder_end_time" value="<?=$arr_config['mini_kino_ladder_deadline']?>"></td>
                    <td><input type="text" id="12005" value="<?=$betData[12005]?>"></td>
                    <td><input type="text" id="12003" value="<?=$betData[12003]?>"></td>
                    <td><input type="text" id="12001" value="<?=$betData[12001]?>"></td>
                </tr>
            </tbody>
<?php        
            $i++;
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
            <div style="margin-top: 30px; text-align: right;"><a href="#" class="btn btn_green h30" onClick="all_save()">저장</a></div>
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

// 배당률 전체수정
function all_save(){   
    let idList = ['10003', '10001', '10005', '10006', '10007', '11005', '11003', '11001', '12005', '12003', '12001','14003', '14001', '14005', '14006', '14007'];
    
    let arrBetData = [];
    idList.forEach(function (markets_id) {
        let bet_price = $('#'+markets_id).val();
        let obj = new Object();
        obj = {'markets_id':markets_id, 'bet_price':bet_price};
        arrBetData.push(obj);
    });
    
    let pb_end_time = $('#pb_end_time').val();
    let pladder_end_time = $('#pladder_end_time').val();
    let kladder_end_time = $('#kladder_end_time').val();
    
    let betData = JSON.stringify(arrBetData);

    var str_msg = '저장하시겠습니까?';
    var result = confirm(str_msg);
    if (result){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/mini_game_w/_mini_game_config_update_all.php',
            data:{'betData':betData, 'pb_end_time':pb_end_time, 'pladder_end_time':pladder_end_time, 'kladder_end_time':kladder_end_time},
            success: function (result) {
                if(result['retCode'] == "1000"){
                    alert('저장하였습니다.');
                    //console.log(result['retMsg']);
                    //window.location.reload();
                    arrBetData.forEach(function(item){
                       $('#'+item['markets_id']).val(item['bet_price']);
                    });
                    return;
                }else{
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert(error);
                alert('저장에 실패하였습니다.');
                return;
            }
        });
    }
}
</script>
</html>