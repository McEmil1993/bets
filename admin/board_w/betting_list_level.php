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
    if(false === GameCode::checkAdminType($_SESSION,$BdsAdminDAO)){
        die();
    }
    // 게시물 전체갯수
    $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM base_rule";
    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];
    
    if($total_cnt > 0){
        $p_data['sql'] = " SELECT * FROM base_rule";
        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }
    
    $p_data['sql'] = " SELECT * FROM base_rule_2";
    $db_dataArr_2 = $BdsAdminDAO->getQueryData($p_data);
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
<script type="text/javascript" src="/smarteditor28/js/HuskyEZCreator.js" charset="utf-8"></script>
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
            <!--<h5><a href="/board_w/betting_list.php"><b>기본규정</b></a></h5>
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
                <option value="betting_list_level.php" selected>레벨별 혜택</option>
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
            <!--<a href="#" class="btn h30 btn_red">적용</a>-->
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
                        <th class="list_table_left" width="20%">회원레벨</th>
                        <td class="list_table_left">1레벨</td>
                        <td class="list_table_left">2레벨</td>
                        <td class="list_table_left">3레벨</td>
                        <td class="list_table_left">4레벨</td>
                        <td class="list_table_left">5레벨</td>
                    </tr>
                    <tr>
                        <th class="list_table_left" width="20%">첫충전</th>
                        <td class="list_table_left"><input type="text" value="5%"></td>
                        <td class="list_table_left"><input type="text" value="5%"></td>
                        <td class="list_table_left"><input type="text" value="5%"></td>
                        <td class="list_table_left"><input type="text" value="7%"></td>
                        <td class="list_table_left"><input type="text" value="10%"></td>
                    </tr>
                    <tr>
                        <th class="list_table_left" width="20%">매충전</th>
                        <td class="list_table_left"><input type="text" value="1%"></td>
                        <td class="list_table_left"><input type="text" value="2%"></td>
                        <td class="list_table_left"><input type="text" value="3%"></td>
                        <td class="list_table_left"><input type="text" value="4%"></td>
                        <td class="list_table_left"><input type="text" value="5%"></td>
                    </tr>
                    <tr>
                        <th class="list_table_left" width="20%">롤링</th>
                        <td class="list_table_left"><input type="text" value="100%"></td>
                        <td class="list_table_left"><input type="text" value="100%"></td>
                        <td class="list_table_left"><input type="text" value="100%"></td>
                        <td class="list_table_left"><input type="text" value="100%"></td>
                        <td class="list_table_left"><input type="text" value="100%"></td>
                    </tr>
                    <tr>
                        <th class="list_table_left" width="20%">추천인낙첨</th>
                        <td class="list_table_left"><input type="text" value="1%"></td>
                        <td class="list_table_left"><input type="text" value="1%"></td>
                        <td class="list_table_left"><input type="text" value="1%"></td>
                        <td class="list_table_left"><input type="text" value="2%"></td>
                        <td class="list_table_left"><input type="text" value="2%"></td>
                    </tr>
                    <tr>
                        <th class="list_table_left" width="10%">첫충 최대 지급금액</th>
                        <td class="list_table_left"><input type="text" value="5만원"></td>
                        <td class="list_table_left"><input type="text" value="5만원"></td>
                        <td class="list_table_left"><input type="text" value="5만원"></td>
                        <td class="list_table_left"><input type="text" value="10만원"></td>
                        <td class="list_table_left"><input type="text" value="15만원"></td>
                    </tr>
                </table>
                <br>
<!-- 하단 테이블 -->                
                <table class="mlist">
                <!--<tr>
                    	<th>구분</th>
                        <th>내용</th>
                        <th></th>
                    </tr>-->
<?php 
/*
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr_2)){
    //print_r($db_dataArr_2);
        foreach($db_dataArr_2 as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            //d.member_idx, d.ip, d.country, d.login_domain, d.login_datetime
            $db_m_idx = $row['idx'];
            $type = $row['type'];
            $contents = $row['contents'];
*/
            ?>

                    <tr>
                    	<th style="width: 150px; text-align:left">내 용</th>
                        <td style="text-align: left">
                        	<div id="loading"></div>
                        	<textarea name="b_content" id="b_content" rows="5" cols="100"></textarea><br>
                        </td>
                    </tr>
					
                    <!-- <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';"> -->
                        <!--<td style='text-align:center'><input type="text" value="<?=$row['type']?>"></td>-->
                        <!-- <th style="border-top: 1px solid #dfdfdf; width: 200px;">내용</th> -->
                        <!-- <td style='text-align:left'> -->
                            <!-- <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;"></textarea><br> -->
                        <!-- </td> -->
                        <!--<td><a href="javascript:fn_update_betting_2(<?=$db_m_idx?>,'<?=$type?>','<?=$contents?>');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>-->
                    <!-- </tr> -->
<?php
/*
        }
    }
    
}
else {
    echo "<tr><td colspan='11'>데이터가 없습니다.</tr>";
}
*/
?>

                </table>
<?php 
$requri = explode('?',$_SERVER['REQUEST_URI']);
$reqFile = basename($requri[0]);
$default_link = "$reqFile?srch_key=".$p_data['srch_key']."&srch_val=".$p_data['srch_val']."";
$default_link .= "&srch_s_date=".$p_data['srch_s_date']."&srch_e_date=".$p_data['srch_e_date']."&vtype=".$p_data['vtype']." ";

?>                
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
function fn_update_betting(idx, result_process, end_time, max_dividend, betting_regulation) {
     var str_msg = '수정 하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update.php',
			data:{'idx':idx, 'result_process':result_process, 'end_time':end_time, 'max_dividend':max_dividend, 'betting_regulation':betting_regulation},
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
			url: '/board_w/_betting_prc_update_2.php',
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

//var oEditors = [];
//
//nhn.husky.EZCreator.createInIFrame({
//	oAppRef: oEditors,
//	elPlaceHolder: "b_content",
//	sSkinURI: "/smarteditor28/SmartEditor2Skin.php",	
//	htParams : {
//		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
//		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
//		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
//		//bSkipXssFilter : true,		// client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
//		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
//		fOnBeforeUnload : function(){
//			//alert("완료!");
//		},
//		SE_EditingAreaManager : {
//			//sDefaultEditingMode : 'HTMLSrc'
//		}
//	}, //boolean
//	fOnAppLoad : function(){ 
//		//예제 코드 
//		//oEditors.getById["b_content"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 
//		
//	}, fCreator: "createSEditor2"
//
//});
</script>
</html>