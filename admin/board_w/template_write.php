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

?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
<!--<![endif]-->

<?php 
include_once(_BASEPATH.'/common/head.php');
?>

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


<body>
<div class="wrap">
<?php

$menu_name = "board_menu_5";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
<form id="regform" name="regform" method="post">
<input type="hidden" id="autonum" name="autonum">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>템플릿 작성</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th style="width: 150px; text-align:left">구분</th>
                        <td>
                            <select name="type" id="type" style="width: 100%" onchange="changeType()">
                                <option value=0>쪽지</option>
                                <option value=1>답변</option>
                                <option value=2>회원가입</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 150px; text-align:left">분 류</th>
                        <td><input type="text" name="division" id="division" style="width:780px; height:30px;"></td>
                    </tr>
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
                        <td>
                            <div id="loading"></div>
                            <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;"></textarea><br>
                        </td>
                    </tr>
                </table>                
                
                <div style="height: 20px"></div>
            </div>
            
            <div class="panel_tit">
            	<div>
					<a href="javascript:;" id="adm_btn_template_send" class="btn h30 btn_green" style="color: white">저장</a>
					<a href="javascript:;" id="adm_btn_template_cancel" class="btn h30 btn_green" style="color: white">취소</a>
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
    $("#adm_btn_template_send").click(function(){
        var str_msg = '등록 하시겠습니까?';

        var type = $("#type").val();
        var division = $("#division").val();
        var msg_title = $("#send_m_title").val();

        // 제목 길이 체크
        if (msg_title.length < 3) {
            alert('제목을 입력해 주세요.');
            $('#send_m_title').select();
            $('#send_m_title').focus();
            return ;
        }

        oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []);

        var msg_content = $("#b_content").val();
        //alert(msg_content);
        // var url_bcontent = encodeURIComponent(msg_content);
        //alert(url_bcontent);

        //글자 제한 풀기
        if (msg_content.length < 3) {
            alert('내용을 입력해 주세요.');
            $('#b_content').select();
            $('#b_content').focus();
            return ;
        }

        var type = $("#type option:selected").val();
		var fileName;
		var data;
        if (type == 2) {
        	fileName = "_join_message_prc.php";
        	data = {'msg_title':msg_title,'msg_content':msg_content};
        }else {
        	fileName = "_template_prc.php";
        	data = {'msg_title':msg_title,'msg_content':msg_content,'type':type,'division':division};
        }

        var result = confirm(str_msg);
        if (result){
            var prctype = "reg";

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/board_w/'+fileName,
                data: data,
                success: function (result) {
                    if(result['retCode'] == "1000"){
                            alert('등록하였습니다.');
                            window.location.href = 'template_list.php';
                            return;
                    }else{
                            alert(result['retMsg']);
                            return;
                    }
                },
                error: function (request, status, error) {
                    alert('등록에 실패하였습니다.');
                    return;
                }
            });
        }
        else {
            return;
        }
    });

    // 공지 취소
    $("#adm_btn_template_cancel").click(function(){
        window.location.href = 'template_list.php';
    });
});

function resize(obj) {
  obj.style.height = "1px";
  obj.style.height = (12+obj.scrollHeight)+"px";
}

var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "b_content",
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
		//oEditors.getById["b_content"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 
		
	}, fCreator: "createSEditor2"

});

function changeType(){
	var type = $("#type option:selected").val();

	$('#division').attr("disabled", false);
	$('#division').val("");
	$('#send_m_title').val("");
	if (type == 2) {
		$('#division').val("회원가입 메세지");
		$('#division').attr("disabled", true);
		$('#send_m_title').val("가입을 축하드립니다.");
	}
}
</script>

</body>
</html>