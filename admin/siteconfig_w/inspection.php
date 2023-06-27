<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $p_data['sql'] = "select * from inspection where idx = 1";
    $arr_config = $MEMAdminDAO->getQueryData($p_data);
    $arr_config = $arr_config[0];
    $start_dt = explode(' ', $arr_config['start_dt']);
    $end_dt = explode(' ', $arr_config['end_dt']);

    /*$arr_config = array();
    foreach ($arr_config_result as $key => $value) {
        $game_config[$value['set_type']] = $value['set_type_val'];
    }*/
    
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
<body>
<div class="wrap">
    <?php

    $menu_name = "inspection";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">

        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>점검 문자 설정</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <!-- detail search -->
            
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
    <div class="panel reserve">
        <input id="idx" name="idx" type=hidden value='<?=$db_dataArr['idx']?>'>
            <div class="tline">
                <iframe id="iframe1" name="iframe1" style="display:none"></iframe>
                <table class="mlist">
                    <tr>
                        <th style="width: 10%; text-align:left">날짜</th>
                        <td style="width: 90%; padding: 2px;text-align:left;">
                            <input id="check_date" name="check_date" type="date" class="" style="width: 150px" placeholder="" value="<?=$start_dt[0]?>"/>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 10%; text-align:left">시작시간</th>
                        <td style="width: 90%px; padding: 2px;text-align:left;">
                            <input id="start_dt" name="start_dt" type="time" class="" style="width: 150px" placeholder="" value="<?=$start_dt[1]?>"/>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 10%; text-align:left">종료시간</th>
                        <td style="width: 90%; padding: 2px;text-align:left;">
                            <input id="end_dt" name="end_dt" type="time" class="" style="width: 150px" placeholder="" value="<?=$end_dt[1]?>"/>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 10%; text-align:left">공지명</th>
                        <td>
                        	<div class="confing_box">
                        		<input type="text" name="send_m_title" id="send_m_title" placeholder="공지명을 입력해 주세요." value="<?=$arr_config['title']?>"/>
                        	</div>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 10%; text-align:left">내 용</th>
                        <td>
                            <div id="loading"></div>
                            <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:block;"><?=$arr_config['contents']?></textarea><br>
                        </td>
                    </tr>
                </table>                
                
                <div style="height: 20px"></div>
            </div>
            
            <div class="panel_tit">
            	<div>
                    <a href="javascript:;" id="adm_btn_update_send" class="btn h30 btn_green" style="color: white">저장</a>
                </div>
            </div>
    </div>
</form>
        </div>
        <!-- END list -->
    </div>
    <!-- END Contents -->
</div>

<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
<script type="text/javascript" src="/smarteditor28/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admMsg.js" charset="utf-8"></script>
<script>
$(document).ready(function(){
    $("#adm_btn_update_send").click(function(){
        let str_msg = '수정 하시겠습니까?';
        let msg_title = $("#send_m_title").val();
        let check_date = $("#check_date").val();
        let start_dt = $("#start_dt").val();
        let end_dt = $("#end_dt").val();

        // 제목 길이 체크
        if (msg_title.length < 4) {
                alert('이벤트명 4글자 이상 입력해 주세요.');
                $('#send_m_title').select();
                $('#send_m_title').focus();
                return ;
        }

        oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []); 

        // 내용
        var msg_content = $("#b_content").val();	
        if (msg_content.length < 5) {
                alert('내용을 입력해 주세요.');
                $('#b_content').select();
                $('#b_content').focus();
                return ;
        }

        var result = confirm(str_msg);
        if (result){
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/siteconfig_w/_update_inspection.php',
                data:{'msg_title':msg_title, 'check_date':check_date,'start_dt':start_dt,'end_dt':end_dt, 'detail':encodeURIComponent(msg_content)},
                success: function (result) {
                    console.log(result['retMsg']);
                        if(result['retCode'] == "1000"){
                            alert('등록하였습니다.');
                            location.href="/siteconfig_w/inspection.php";
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
});

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
</script>

</html>
