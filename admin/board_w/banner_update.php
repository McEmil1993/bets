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

$MEMAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    
    $p_data['idx'] = trim(isset($_REQUEST['idx']) ? $MEMAdminDAO->real_escape_string($_REQUEST['idx']) : 0);
    $p_data['status'] = trim(isset($_REQUEST['status']) ? $MEMAdminDAO->real_escape_string($_REQUEST['status']) : 0);
    $p_data['display_type'] = trim(isset($_REQUEST['displayType']) ? $MEMAdminDAO->real_escape_string($_REQUEST['displayType']) : 0);
    $p_data['rank'] = trim(isset($_REQUEST['rank']) ? $MEMAdminDAO->real_escape_string($_REQUEST['rank']) : 0);
    
    $p_data['sql'] = " SELECT * FROM banners WHERE idx = ".$p_data['idx'];
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data)[0];
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

$menu_name = "board_menu_6";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
<input type="hidden" id="autonum" name="autonum">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>배너 수정</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <input id="idx" name="idx" type=hidden value='<?=$db_dataArr['idx']?>'>
            <div class="tline">
                <iframe id="iframe1" name="iframe1" style="display:none"></iframe>
                <table class="mlist">
                    <tr>
                    	<!--<th style="width: 150px; text-align:left">템플릿 선택</th>
                        <td>
                            <select name="set_msg" id="set_msg" onchange="javascript:getSetMsg(this.value);" style="width: 100%">
                                <option value="">-- 선택안함 --</option>
                                <?php 
                                if(!empty($db_dataArr_msg_set)){
                                    foreach($db_dataArr_msg_set as $row) {
                                ?>
                                <option value="<?=$row['idx']?>"><?=$row['title_view']?></option>
                                <?php 
                                     }
                                 }
                                ?>
                            </select>
                        </td>-->
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">썸네일</th>
                        <td class="file_thumb_section">
                            <form id="thumbnail_fm" method="post" action="../common/image_send_unique_imagename.php" enctype="multipart/form-data" target="iframe1">
                        	<div id="loading"></div>
                                <div class="image_container w300"></div>
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/banner'>
                                <input id="saveName" name="saveName" type=hidden value=''>
                                <input type="file" onchange="setThumbnail(event);" id="uploadfile" name="uploadfile" accept="image/*">
                            </form>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">현재</th>
                        <td>
                            <img src="<?=IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/'.$db_dataArr['thumbnail']?>" alt="이미지 파일이 존재하지 않습니다.">
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">상태</th>
                        <td>
                        	<select name="status" id="status" style="width: 100%">
                                <option value="1" <?php if($p_data['status']==1) { echo "selected"; }?>>사용</option>
                                <option value="0" <?php if($p_data['status']==0) { echo "selected"; }?>>미사용</option>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">노출타입</th>
                        <td>
                        	<select name="displayType" id="displayType" style="width: 100%">
                                <option value="1" <?php if($p_data['display_type']==1) { echo "selected"; }?>>PC</option>
                                <option value="2" <?php if($p_data['display_type']==2) { echo "selected"; }?>>모바일</option>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">순번</th>
                        <td>
                        	<select name="rank" id="rank" style="width: 100%">
                                <option value="1" <?php if($p_data['rank']==1) { echo "selected"; }?>>1</option>
                                <option value="2" <?php if($p_data['rank']==2) { echo "selected"; }?>>2</option>
                                <option value="3" <?php if($p_data['rank']==3) { echo "selected"; }?>>3</option>
                                <option value="4" <?php if($p_data['rank']==4) { echo "selected"; }?>>4</option>
                                <option value="5" <?php if($p_data['rank']==5) { echo "selected"; }?>>5</option>
                        </td>
                    </tr>
                </table>                
                
                *PC적정 크기 : 1100 x 320 / *모바일적정 크기 : 340 x 170
                <br>
                *최대 사용할 수 있는 배너는 5개 입니다.
                <br>
                *메인배너 정렬 순서는 순번 > 최신 등록시간 입니다.
                <div style="height: 10px"></div>
            </div>
            
            <div class="panel_tit">
            	<div>
                    <a href="javascript:;" id="adm_btn_event_send" class="btn h30 btn_green" style="color: white">저장</a>
                    <a href="/board_w/banner_list.php" id="adm_btn_event_cancel" class="btn h30 btn_green" style="color: white">목록</a>
                </div>
            </div>
        </div>
        <!-- END list -->
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/prism.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/init.js" charset="utf-8"></script>  
    </div>
    <!-- END Contents -->
</div>
<?php 
include_once(_BASEPATH.'/common/bottom.php');
?> 
<script>
let image_check = false;

$(document).ready(function(){
	// 공지 등록
	$("#adm_btn_event_send").click(function(){

            // 선택한 파일이 있으면 이미지 서버에 전송
            let filename = '';
            if(image_check){
            	$('#saveName').val(getCurrentDate() + "_" + document.getElementById("uploadfile").files[0].name);

                //alert("파일을 첨부해 주세요.");
                $("#thumbnail_fm").submit();
                
                // document.getElementById 사용하니 fakepath 피해서 파일명이 가져와진다.
                //filename = document.getElementById("uploadfile").files[0].name;
                filename = $('#saveName').val();
            }
            
            let str_msg = '등록 하시겠습니까?';
            //let msg_title = $("#send_m_title").val();

            //oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []); 

            // 내용
            /* var msg_content = $("#b_content").val();	
            if (msg_content.length < 5) {
                    alert('내용을 입력해 주세요.');
                    $('#b_content').select();
                    $('#b_content').focus();
                    return ;
            } */

            /*var detail = $("#detail").val();
            if (detail.length < 5) {
                    alert('썸네일을 입력해 주세요.');
                    $('#b_content').select();
                    $('#b_content').focus();
                    return ;
            }*/

            // 상태
            var status = $("#status option:selected").val();

            // 노출 타입
            var displayType = $("#displayType option:selected").val();
            var rank = $("#rank option:selected").val();
            
            let idx = $("#idx").val();

            var result = confirm(str_msg);
            if (result){
                    //var prctype = "reg";

                    $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: '/board_w/_banner_prc_update.php',
                            data:{'idx':idx, 'filename':filename , 'status':status, 'displayType':displayType,'rank':rank},
                            success: function (result) {
                                    if(result['retCode'] == "1000"){
                                            alert('등록하였습니다.');
                                            location.href="/board_w/banner_list.php";
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
    /*$("#adm_btn_event_cancel").click(function(){

            if (admMsgInputCheckPOP('reg')==false) {
                    return false;
            }

            return true;
    });*/
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
    
    // 이미지 썸네일 추가 ADD KSG 
    function setThumbnail(event) {
        var reader = new FileReader();
        
        reader.onload = function(event) {
            var img = document.createElement("img");
            img.setAttribute("src", event.target.result);
            document.querySelector("div.image_container").appendChild(img);
            image_check = true;
        };
        
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>