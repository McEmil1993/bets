<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();


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

$menu_name = "board_menu_9";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');
?>
    <!-- Contents -->
    <div class="con_wrap">
<input type="hidden" id="autonum" name="autonum">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>팝업 등록</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            
            <div class="tline">
            	<iframe id="iframe1" name="iframe1" style="display:none"></iframe>
                <table class="mlist">
                    <!-- 
                    <tr>
                    	<th style="width: 150px; text-align:left">이벤트명</th>
                        <td>
                        	<div class="confing_box">
                        		<input type="text" name="send_m_title" id="send_m_title" placeholder="이벤트명을 입력해 주세요." />
                        	</div>
                        </td>
                    </tr>
                     -->
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
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/popup'>
                                <input id="saveName" name="saveName" type=hidden value=''>
                                <input type="file" onchange="setThumbnail(event);" id="uploadfile" name="uploadfile" accept="image/*">
                            </form>
                        </td>
                    </tr>
                    <!--<tr>
                    	<th style="width: 150px; text-align:left">상세</th>
                        <td>
                        	<div id="loading"></div>
                                <input type="text" name="detail" id="detail" />
                        </td>
                    </tr>-->
                    <tr>
                    	<th style="width: 150px; text-align:left">상태</th>
                        <td>
                        	<select name="status" id="status" style="width: 100%">
                                <option value="1" selected>사용</option>
                                <option value="0">미사용</option>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">순번</th>
                        <td>
                        	<select name="rank" id="rank" style="width: 100%">
                                <option value="1" selected>1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                        </td>
                    </tr>
                    <!-- 
					<tr>
                    	<th style="width: 150px; text-align:left">내 용</th>
                        <td>
                            <div id="loading"></div>
                            <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;"></textarea><br>
                        </td>
                    </tr>
                     -->
                </table>                
                *적정 크기 : 500 x 600
                <br>
                *최대 사용할 수 있는 팝업은 4개 입니다.
                <br>
                *메인팝업 정렬 순서는 순번 > 최신 등록시간 입니다.
                <div style="height: 10px"></div>
                
            </div>
            
            <div class="panel_tit">
            	<div>
                    <a href="javascript:;" id="adm_btn_event_send" class="btn h30 btn_green" style="color: white">저장</a>
                    <a href="/board_w/popup_list.php" id="adm_btn_event_cancel" class="btn h30 btn_green" style="color: white">목록</a>
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
        
        var popuplistImg = document.querySelector("#popuplistImgId");
        var popuplistImgWidth = popuplistImg.naturalWidth;
        var popuplistImgHeight = popuplistImg.naturalHeight;

            if(popuplistImgWidth >= 450 && popuplistImgWidth <= 500 && popuplistImgHeight >= 550 && popuplistImgHeight <= 600){

                if(!image_check){
                    alert("파일을 첨부해 주세요.");
                    return;
                }

                $('#saveName').val(getCurrentDate() + "_" + document.getElementById("uploadfile").files[0].name);
                $("#thumbnail_fm").submit();
                
                let str_msg = '등록 하시겠습니까?';

    //            let msg_title = $("#send_m_title").val();
                // document.getElementById 사용하니 fakepath 피해서 파일명이 가져와진다.
                let filename = $('#saveName').val();
                /* 
                // 제목 길이 체크
                if (msg_title.length < 4) {
                        alert('이벤트명 4글자 이상 입력해 주세요.');
                        $('#send_m_title').select();
                        $('#send_m_title').focus();
                        return ;
                } */
                
    //            oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []); 

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
                var rank = $("#rank option:selected").val();

                var result = confirm(str_msg);
                if (result){
                        //var prctype = "reg";

                        $.ajax({
                                type: 'post',
                                dataType: 'json',
                                url: '/board_w/_popup_prc.php',
                                data:{'filename':filename,'status':status,'rank':rank},
                                success: function (result) {
                                    console.log(result['retMsg']);
                                        if(result['retCode'] == "1000"){
                                                alert('등록하였습니다.');
                                                location.href="/board_w/popup_list.php";
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
            } else {
                alert("규정된 이미지 사이즈가 다릅니다");
                console.log(popuplistImgWidth+"--"+popuplistImgHeight);
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

    // 이미지 썸네일 추가 ADD KSG 
    function setThumbnail(event) {
        $('.image_container').html('');
        var reader = new FileReader();
        
        reader.onload = function(event) {
            var img = document.createElement("img");
            img.id = 'popuplistImgId';
            img.setAttribute("src", event.target.result);
            document.querySelector("div.image_container").appendChild(img);
            image_check = true;
        };
        
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>