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
<!--<form id="regform" name="regform" method="post">-->
<input type="hidden" id="autonum" name="autonum">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>메인배너 관리</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <span style="color:red">주의 PC 배너 파일명은 slideshow.jpg로 해주셔야 합니다.</span><br>
            <span style="color:red">주의 모바일 배너 파일명은 m_slideshow.jpg로 해주셔야 합니다.</span>
            <div class="tline">
                <iframe id="iframe1" name="iframe1" style="display:none"></iframe>
                <table class="mlist">
                    <tr>
                    	<th style="width: 150px; text-align:left">PC 배너 등록</th>
                        <td class="file_thumb_section">
                            <form id="thumbnail_fm" method="post" action="../common/image_send.php" enctype="multipart/form-data" target="iframe1">
                                <div id="loading"></div>
                                <div class="image_container w300"></div>
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/main'>
                                <input type="file" onchange="setThumbnail(event, 0);" id="uploadfile" name="uploadfile" accept="image/*">
                            </form>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">현재</th>
                        <td>
                            <img src="<?=IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/main/slideshow.jpg'?>" alt="이미지 파일이 존재하지 않습니다.">
                        </td>
                    </tr>
                    <?php if(IMAGE_PATH != 'asbet'){ ?>
                    <tr>
                    	<th style="width: 150px; text-align:left">모바일 배너 등록</th>
                        <td class="file_thumb_section">
                            <form id="thumbnail_fm_m" method="post" action="../common/image_send.php" enctype="multipart/form-data" target="iframe1">
                        	<div id="loading"></div>
                                <div class="image_container_m w300"></div>
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/main'>
                                <input type="file" onchange="setThumbnail(event, 1);" id="uploadfile" name="uploadfile" accept="image/*">
                            </form>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">현재</th>
                        <td>
                            <img src="<?=IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/main/m_slideshow.jpg'?>" alt="이미지 파일이 존재하지 않습니다.">
                        </td>
                    </tr>
                    <?php } ?>
                </table>                
                
                <div style="height: 20px"></div>
            </div>
            
            <div class="panel_tit">
            	<div>
                    <a href="javascript:;" id="adm_btn_pc_banner_send" class="btn h30 btn_green" style="color: white">PC 저장</a>
                    <?php if(IMAGE_PATH != 'asbet'){ ?>
                    <a href="javascript:;" id="adm_btn_mobile_banner_send" class="btn h30 btn_green" style="color: white">모바일 저장</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- END list -->
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/chosen.jquery.js"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/prism.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/docsupport/init.js" charset="utf-8"></script>
<!--</form>-->
    </div>
    <!-- END Contents -->
</div>
<?php 
include_once(_BASEPATH.'/common/bottom.php');
?> 
<script>
let pc_check = m_check = false;
$(document).ready(function(){
	// 공지 등록
	$("#adm_btn_pc_banner_send").click(function(){
            if(!pc_check){
                alert("파일을 첨부해 주세요.");
                return;
            }
            
            /*$("#thumbnail_fm").submit();
            alert("적용했습니다.");
            //window.location.reload();
            return;*/
        
            let url = '../common/image_send.php';
            let form = $('#thumbnail_fm')[0];
            let formData = new FormData(form);

            $.ajax({
                url : url,
                type : 'POST',
                data : formData,
                contentType : false,
                processData : false        
            }).done(function(data){
                alert("적용했습니다.");
                window.location.reload();
            });
	});
        
        $("#adm_btn_mobile_banner_send").click(function(){
            if(!m_check){
                alert("파일을 첨부해 주세요.");
                return;
            }
            
            /*$("#thumbnail_fm_m").submit();
            alert("적용했습니다.");
            //window.location.reload();
            return;*/
            let url = '../common/image_send.php';
            let form = $('#thumbnail_fm_m')[0];
            let formData = new FormData(form);

            $.ajax({
                url : url,
                type : 'POST',
                data : formData,
                contentType : false,
                processData : false        
            }).done(function(data){
                alert("적용했습니다.");
                window.location.reload();
            });
	});
});

/*function resize(obj) {
  obj.style.height = "1px";
  obj.style.height = (12+obj.scrollHeight)+"px";
}*/

/*var oEditors = [];

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

});*/
    
    // 이미지 썸네일 추가 ADD KSG 
    function setThumbnail(event, type) {
        var reader = new FileReader();
        
        reader.onload = function(event) {
            var img = document.createElement("img");
            img.setAttribute("src", event.target.result);
            if(type == 0){
                document.querySelector("div.image_container").appendChild(img);
                pc_check = true;
            }else{
                document.querySelector("div.image_container_m").appendChild(img);
                m_check = true;
            }
        };
        
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>