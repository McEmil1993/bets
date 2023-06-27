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

$rank_check['1'] = array('rank'=> 1,'status' => 1);
$rank_check['2'] = array('rank'=> 2,'status' =>1);
$rank_check['3'] = array('rank'=> 3,'status' => 1);
$rank_check['4'] = array('rank'=> 4,'status' => 1);
$rank_check['5'] = array('rank'=> 5,'status' => 1);

$getRank = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $getRank->dbconnect();
$data_select_p = 'selected';
$data_select_m = '';
if(isset($_POST['displayType'])){
    if($_POST['displayType'] == 1){
        $data_select_p = 'selected';
        $data_select_m = '';
    }else {
        $data_select_p  = '';
        $data_select_m = 'selected';
    }
}
if($db_conn) {

    $p_data['displayType'] = trim(isset($_POST['displayType']) ? $getRank->real_escape_string($_POST['displayType']) : '1');

   
    
    try {
        $r_data['sql'] = "SELECT * FROM banners WHERE display_type = '".$p_data['displayType']."' group by rank";
        $chck_rank_array = $getRank->getQueryData($r_data);
    
        foreach($chck_rank_array as $data){
            if($data['rank'] == 1){
                $rank_check[$data['rank']] =  array('rank'=> 1,'status' => 0);
            }elseif($data['rank'] == 2){
                $rank_check[$data['rank']] =  array('rank'=> 2,'status' => 0);
            }elseif($data['rank'] == 3){
                $rank_check[$data['rank']] =  array('rank'=> 3,'status' => 0);
            }elseif($data['rank'] == 4){
                $rank_check[$data['rank']] =  array('rank'=> 4,'status' => 0);
            }elseif($data['rank'] == 5){
                $rank_check[$data['rank']] =  array('rank'=> 5,'status' => 0);
            }
            
        
        }

        
    } catch (\Exception $e) {
        $UTIL->logWrite("[_banner_prc] [error -2]", "error");
        $result['retCode'] = -3;
        $result['retMsg'] = 'Exception 예외발생';
    }
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
                <h4>배너 등록</h4>
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
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/banner'>
                                <input id="saveName" name="saveName" type=hidden value=''>
                                <input type="file" onchange="setThumbnail(event);" id="uploadfile" name="uploadfile" accept="image/*">
                            </form>
                        </td>
                    </tr>
                        <?php
                            // $img_size_array = getimagesize("");
                            // $width = $img_size_array[0];
                            // $height = $img_size_array[1];

                        ?>
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
                    	<th style="width: 150px; text-align:left">노출타입</th>
                        <form action="" method="POST">
                        <td>
                        	<select name="displayType" id="displayType" onchange="this.form.submit()" style="width: 100%">
                                <option <?=$data_select_p ?> value="1">PC</option>
                                <option <?=$data_select_m ?> value="2">모바일</option>
                        </td>
                        </form>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">순번</th>
                        
                        <td>
                            
                        <select name="rank" id="rank"  style="width: 100%">
                        <?php 
                            foreach($rank_check as $rnk){
                                if ($rnk['status'] === 1) {
                        ?> 
                            <option  value="<?=$rnk['rank'] ?>"><?=$rnk['rank'] ?></option>

                        <?php 
                            }
                        }
                        ?>
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
                *PC적정 크기 : 1920 x 400 / *모바일적정 크기 : 720 X 550
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

            var myImg = document.querySelector("#sky");
            var currWidth = myImg.naturalWidth;
            var currHeight = myImg.naturalHeight;

            var status = $("#status option:selected").val();
            var displayType = $("#displayType option:selected").val();
            var rank = $("#rank option:selected").val();

            if (displayType == '1') {
                if (currWidth >= 1000 && currWidth <= 1920 && currHeight >= 310 && currHeight <= 400) {

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


                    var result = confirm(str_msg);
                    if (result){
                            //var prctype = "reg";

                            $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: '/board_w/_banner_prc.php',
                                    data:{'filename':filename,'status':status,'displayType':displayType,'rank':rank},
                                    success: function (result) {
                                        console.log(result['retMsg']);
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

                } else {
                    alert("규정된 이미지 사이즈가 다릅니다");
                    console.log(currWidth+"--"+currWidth+"--"+currHeight+"--"+currHeight);
                }
            } else {
                if (currWidth >= 710 && currWidth <= 780 && currHeight >= 500 && currHeight <= 570) {

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


                    var result = confirm(str_msg);
                    if (result){
                            //var prctype = "reg";

                            $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: '/board_w/_banner_prc.php',
                                    data:{'filename':filename,'status':status,'displayType':displayType,'rank':rank},
                                    success: function (result) {
                                        console.log(result['retMsg']);
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

                } else {
                    alert("규정된 이미지 사이즈가 다릅니다");
                    console.log(currWidth+"--"+currWidth+"--"+currHeight+"--"+currHeight);
                }
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
            img.id = 'sky';
            img.setAttribute("src", event.target.result);
            document.querySelector("div.image_container").appendChild(img);
            image_check = true;
        };
        
        reader.readAsDataURL(event.target.files[0]);
    }

</script>

</body>
</html>