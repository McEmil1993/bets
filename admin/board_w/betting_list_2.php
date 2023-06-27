<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}
$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
    
    $id = $BdsAdminDAO->real_escape_string($_REQUEST['id']);
    // 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM sports_rule where idx > 0 AND id = $id";
    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];
    
    if($total_cnt > 0){
        $p_data['sql'] = " SELECT * FROM sports_rule where idx > 0 AND id = $id";
        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }
    
    // 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM sports_rule_2 where idx > 0 AND id = $id";
    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt_2 = $db_dataArrCnt[0]['CNT'];
    
    if($total_cnt > 0){
        $p_data['sql'] = " SELECT * FROM sports_rule_2 where idx > 0 AND id = $id";
        $db_dataArr_2 = $BdsAdminDAO->getQueryData($p_data);
    }
    
    $BdsAdminDAO->dbclose();
}
?>

<html lang="ko">

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
$menu_name = "board_menu_2";

include_once(_BASEPATH.'/common/left_menu.php');
include_once(_BASEPATH.'/common/iframe_head_menu.php');

?>
    <!-- Contents -->
    <div class="con_wrap">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>배팅규정</h4>
            </a>
        </div>
        
        <!-- detail search -->
        <div class="panel search_box">
<!--            <h5><a href="/board_w/betting_list.php"><b>기본규정</b></a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=0">스포츠</a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=1">실시간</a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=6046">축구</a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=48242">농구</a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=154914">야구</a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=154830">배구</a></h5>
            <h5><a href="/board_w/betting_list_2.php?id=54094">테니스</a></h5>-->
            <select onchange="Show_hidden(this.value);">
                <option value="1">기본 규정 선택</option>
                <option value="2">실시간 종목 선택</option>
                <option value="2">스포츠 종목 선택</option>
                <option value="3">미니게임 종목 선택</option>
            </select>
            <select id="selectItem_1" onchange="location.href=this.value" style="display:inline-block;">
                <option>선택</option>
                <option value="betting_list.php">기본 규정</option>
                <option value="betting_list_level.php">레벨별 혜택</option>
            </select>
            <select id="selectItem_2" onchange="location.href=this.value" style="display:none;">
                <option>선택</option>
                <option value="betting_list_2.php">축구</option>
                <option value="betting_list_2.php">농구</option>
                <option value="betting_list_2.php">배구</option>
                <option value="betting_list_2.php">야구</option>
            </select>
            <select id="selectItem_3" onchange="location.href=this.value" style="display:none;">
                <option>선택</option>
                <option value="betting_list_2.php">파워볼</option>
                <option value="betting_list_2.php">파워 사다리</option>
                <option value="betting_list_2.php">키노 사다리</option>
                <option value="betting_list_2.php">가상축구</option>
            </select>
        </div>
        <!-- END detail search -->
        <script>
            function Show_hidden(e){
                var menu = new Array("selectItem_1","selectItem_2","selectItem_3");
                for(var i=0;i < menu.length;i++){
                    if("selectItem_"+e==menu[i]){
                        document.all[menu[i]].style.display="inline-block";
                    }else{
                        document.all[menu[i]].style.display="none";
                    }
                }
            }
        </script>
        <!-- list -->
        <div class="panel reserve">
<!--<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
<input type="hidden" name="vtype" id="vtype" value="<?=$p_data['vtype']?>">
</form>            -->
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th style="width: 150px; text-align:left">제 목</th>
                        <td>
                        	<div class="confing_box">
                        		<input type="text"  name="send_m_title" id="send_m_title" placeholder="제목을 입력해 주세요." />
                        	</div>
                        </td>
                    </tr>

                    <tr>
                    	<th style="width: 150px; text-align:left">내 용</th>
                        <td style="text-align: left">
                        	<div id="loading"></div>
                        	<textarea name="b_content" id="b_content" rows="5" cols="100"></textarea><br>
                        </td>
                    </tr>
                </table>
                <br>
<!-- 하단 테이블 -->                
<!--                <table class="mlist">
                    <tr>
                    	<th>타입 및 경기시간</th>
                        <th>설명</th>
                        <th></th>
                    </tr>
<?php 
if($total_cnt_2 > 0) {
    $i=0;
    if(!empty($db_dataArr_2)){
    //print_r($db_dataArr_2);
        foreach($db_dataArr_2 as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
            $db_m_idx = $row['id'];
            $type = $row['type'];
            $contents = $row['contents'];
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                        <td style='text-align:center'><input type="text" value="<?=$row['type']?>"></td>
                        <td style='text-align:left'>
                            <textarea name="contents" id="contents" rows="5" cols="10" style="width:780px; height:350px;">
                                <?=$row['contents']?>
                            </textarea><br>
                        </td>
                        <td><a href="javascript:fn_update_betting_2(<?=$db_m_idx?>,'<?=$type?>','<?=$contents?>');" class="btn h25 btn_blu adm_btn_notice_del">수정</a>
                        </td>
                    </tr>
<?php        
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

?>                -->
            </div>
            <div style="margin-top: 30px"><a href="#" class="btn btn_green h30">저장</a> <a href="#" class="btn btn_green h30">취소</a></div>
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
function fn_update_betting(idx, betting_type, application_time, contents) {
     var str_msg = '수정 하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update_sports.php',
			data:{'idx':idx, 'betting_type':betting_type, 'application_time':application_time, 'contents':contents},
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

function fn_update_betting_2(idx, type, contents) {
     var str_msg = '수정 하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update_sports_2.php',
			data:{'idx':idx, 'type':type, 'contents':contents},
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
</script>
</html>