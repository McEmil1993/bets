<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

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
    $idx = $BdsAdminDAO->real_escape_string($_GET['idx']);
    $p_data['sql'] = " SELECT a.idx, a.title, a.contents, a.create_dt, a.is_answer, a.is_status, a.is_view, a.answer, b.nick_name, b.id,b.level, b.idx as member_idx ";
    $p_data['sql'] .= "  FROM menu_qna a ";
    $p_data['sql'] .= " LEFT JOIN member b ON a.member_idx = b.idx ";
    $p_data['sql'] .= " WHERE a.idx = $idx";

    $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    
    $BdsAdminDAO->dbclose(); 
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {
	$db_dataArr_msg_set = $MEMAdminDAO->getTemplateListAll();	// 전체
	$db_dataArr_msg_set_0 = array();	// 쪽지
	$db_dataArr_msg_set_1 = array();	// 답변

	foreach ($db_dataArr_msg_set as $row) {
		if ($row['type'] == 0) {
			$db_dataArr_msg_set_0[] = $row;
		} else if ($row['type'] == 1) {
			$db_dataArr_msg_set_1[] = $row;
		}
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
<script src="<?=_STATIC_COMMON_PATH?>/js/admCommon.js"></script>
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

<link rel="stylesheet" href="<?=_STATIC_COMMON_PATH?>/docsupport/prism.css">
<link rel="stylesheet" href="<?=_STATIC_COMMON_PATH?>/docsupport/chosen.css">

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
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admMsg.js" charset="utf-8"></script>
<form id="popForm" name="popForm" method="post">
    <input type="hidden" id="seq" name="seq">
    <input type="hidden" id="m_idx" name="m_idx">
    <input type="hidden" id="m_dis_id" name="m_dis_id">
    <input type="hidden" id="selContent" name="selContent" value="3">
</form>
<body>
<div class="wrap">
<?php

$menu_name = "board_menu_4";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
<div class="con_wrap">
<?php 
//print_r($db_dataArr);
$db_m_idx = $db_dataArr[0]['idx'];
?>
<form id="regform" name="regform" method="post">
<input type="hidden" id="autonum" name="autonum">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>고객센터 상세</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            
            <div class="tline">
                <table class="mlist">
                    <input type="hidden"  name="idx" id="idx" value="<?php echo $db_dataArr[0]['idx'] ?>">
                    <tr>
                        <th style="width: 150px; text-align:left">아이디(닉네임) / 레벨</th>
                        <td colspan="2">
                            <div class="confing_box" onClick="popupWinPost('/member_w/pop_userinfo.php','popuserinfo',800,1400,'userinfo','<?=$db_dataArr[0]['member_idx']?>');">
                                <input type="text" name="a_id" id="a_id" placeholder="<?php echo $db_dataArr[0]['nick_name'] ?>" value = "<?php $db_dataArr[0]['nick_name'] ?>" style="cursor:pointer;" readonly />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 150px; text-align:left">작성일</th>
                        <td colspan="2">
                            <div class="confing_box">
                                <input type="text"  name="create_dt" id="create_dt" placeholder="<?php echo $db_dataArr[0]['create_dt'] ?>" readonly />
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">제목</th>
                        <td colspan="2">
                            <div class="confing_box">
                                <input type="text"  name="send_m_title" id="send_m_title" value="<?php echo $db_dataArr[0]['title'] ?>" readonly />
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">내용</th>
                        <td colspan="2" class="t_area_article">
                            <div id="loading"></div>
                            <textarea name="content" id="content" rows="5" cols="100" readonly><?=$db_dataArr[0]['contents']?></textarea><br>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">답변</th>
                        <td>
                            <div id="loading"></div>
                            <textarea name="answer" id="answer" rows="5" cols="100" style="width:780px; height:350px; display:none;">
                                    <?=$db_dataArr[0]['answer']?>
                            </textarea><br>
                        </td>
                        <td class="classification">
                                <div class="classification_title">분류</div>
                                <select id="srch_type" style="width: 100%" onchange="javascript:setTemplate();">
                                    <option value="2" selected>전체보기</option>
                                    <option value="0">쪽지</option>
                                    <option value="1">답변</option>
                                </select>

                                <!-- 전체 보기 -->
                                <select name="set_msg_all" id="set_msg_all" size="5" onchange="javascript:setTemplateMsg(this.value);" style="width: 100%; height: 350px">
                                    <?php
                                    if (!empty($db_dataArr_msg_set)) {
                                            foreach ($db_dataArr_msg_set as $row) {
                                    ?>
                                    <option value="<?=$row['idx']?>"><?=$row['title']?></option>
                                    <?php 
                                            }
                                    }
                                    ?>
                                </select>

                                <!-- 쪽지 -->
                                <select name="set_msg_0" id="set_msg_0" size="5" onchange="javascript:setTemplateMsg(this.value);" style="width: 100%; height: 350px; display: none">
                                    <?php
                                    if (!empty($db_dataArr_msg_set_0)) {
                                            foreach ($db_dataArr_msg_set_0 as $row) {
                                    ?>
                                    <option value="<?=$row['idx']?>"><?=$row['title']?></option>
                                    <?php 
                                            }
                                    }
                                    ?>
                                </select>

                                <!-- 답변 -->
                                <select name="set_msg_1" id="set_msg_1" size="5" onchange="javascript:setTemplateMsg(this.value);" style="width: 100%; height: 350px; display: none">
                                    <?php
                                    if (!empty($db_dataArr_msg_set_1)) {
                                            foreach ($db_dataArr_msg_set_1 as $row) {
                                    ?>
                                    <option value="<?=$row['idx']?>"><?=$row['title']?></option>
                                    <?php 
                                            }
                                    }
                                    ?>
                                </select>
                        </td>
                    </tr>
                </table>
                
                <div style="height: 20px"></div>
            </div>
            
            <div class="panel_tit" align="center">
            	<div>
                    <a href="javascript:;" id="adm_btn_answer_send" class="btn h30 btn_green" style="color: white"> 저장</a>
                    <a href="javascript:fn_del_answer(<?=$db_m_idx?>);" id="adm_btn_service_center_answer_del" class="btn h30 btn_green" style="color: white">답변삭제</a>
                    <a href="javascript:void(0)" onclick="goBack()" id="adm_btn_service_center_list" class="btn h30 btn_green" style="color: white">목록</a>
                </div>
            </div>
        </div>
        <!-- END list -->
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/prism.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/init.js" charset="utf-8"></script>
</form>        
    </div>
    <!-- END Contents -->
</div>
<?php 
include_once(_BASEPATH.'/common/bottom.php');
?> 
<script>
$(document).ready(function(){
	// 공지 등록
	$("#adm_btn_answer_send").click(function(){
		var str_msg = '답변을 등록 하시겠습니까?';
		var idx = $("#idx").val();
		oEditors.getById["answer"].exec("UPDATE_CONTENTS_FIELD", []); 

		var msg_answer = $("#answer").val();
		//alert(msg_content);
		//var url_bcontent = encodeURIComponent(msg_content);
		//alert(url_bcontent);
		
		if (msg_answer.length < 3) {
			alert('내용을 입력해 주세요.');
			$('#answer').select();
			$('#answer').focus();
			return ;
		}

		var result = confirm(str_msg);
		if (result){
			//var prctype = "reg";
			
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/board_w/_service_center_prc_answer.php',
				data:{'msg_answer':encodeURIComponent(msg_answer), 'idx':idx},
				success: function (result) {
					if(result['retCode'] == "1000"){
						alert('등록하였습니다.');
						window.location.reload();
						return;
					}else{
						alert(result['retMsg']);
						return;
					}
				},
				error: function (request, status, error) {
					alert('등록에 실패하였습니다(1).');
					return;
				}
			});
		}
		else {
			return;
		}
	});
});

function resize(obj) {
  obj.style.height = "1px";
  obj.style.height = (12+obj.scrollHeight)+"px";
}

var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "answer",
	sSkinURI: "/smarteditor28/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//bSkipXssFilter : true,		// client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
			//alert("완료!");
		},
		SE_EditingAreaManager : {
			//sDefaultEditingMode : 'HTMLSrc'
		}
	}, //boolean
	fOnAppLoad : function(){ 
		//예제 코드 
		//oEditors.getById["answer"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 
		
	}, fCreator: "createSEditor2"

});

// 답변 삭제
function fn_del_answer(idx) {
	var str_msg = '삭제 하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_service_center_prc_answer_del.php',
			data:{'idx':idx},
			success: function (result) {
				if(result['retCode'] == "1000"){
					alert('삭제하였습니다.');
					window.location.reload();
					return;
				}else{
					alert(result['retMsg']);
					return;
				}
			},
			error: function (request, status, error) {
				alert('삭제에 실패하였습니다.');
				return;
			}
		});
	}
	else {
		return;
	}
}

function setTemplate() { 
	let selected = $('#srch_type').val();

    if (selected == 0) {
		$('#set_msg_all').attr('style', 'width: 100%; height: 350px; display: none;');
		$('#set_msg_0').attr('style', 'width: 100%; height: 350px; display: block');
		$('#set_msg_1').attr('style', 'width: 100%; height: 350px; display: none;');
	} else if (selected == 1) {
		$('#set_msg_all').attr('style', 'width: 100%; height: 350px; display: none;');
		$('#set_msg_0').attr('style', 'width: 100%; height: 350px; display: none;');
		$('#set_msg_1').attr('style', 'width: 100%; height: 350px; display: block');
	} else {
		// all
		$('#set_msg_all').attr('style', 'width: 100%; height: 350px; display: block');
		$('#set_msg_0').attr('style', 'width: 100%; height: 350px; display: none;');
		$('#set_msg_1').attr('style', 'width: 100%; height: 350px; display: none;');
	}
}

function setTemplateMsg(seq) {
	if ((seq < 1) || (seq=='')) {
		return;
	}

	document.getElementById("loading").innerHTML = "Data 로딩중 입니다.";
	$.ajax({
		type: 'post',
		dataType: 'json',
	    url: '/member_w/_get_msg_set.php',
	    data: { 'seq': seq },
	    success: function (result) {
	    	document.getElementById("loading").innerHTML = "";
	    	
	    	if (result['retCode'] == "1000") {
	    		retContent = result['db_content'];
				
	    		$("#send_m_title").val(result['db_title']);
				
	    		oEditors.getById["answer"].exec("SET_IR", [""]);
	    		oEditors.getById["answer"].exec("PASTE_HTML", [retContent]);
	    		$("#autonum").val(seq);
	    		return;
			} else {
				alert(result['retMsg']);
				alert('정보를 가져오지 못했습니다.(1)');
				return;
			}
		},
	    error: function (request, status, error) {
	    	document.getElementById("loading").innerHTML = "";
	    	alert('정보를 가져오지 못했습니다.(2)');
			return;
		}
	});
}
function goBack() {
	window.history.back();
}


</script>
</body>
</html>
