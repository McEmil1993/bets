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

$menu_name = "board_menu_1";

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
                <h4>쪽지 발송</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            
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
                    	<th style="width: 150px; text-align:left">템플릿 선택</th>
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
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">내 용</th>
                        <td>
                        	<div id="loading"></div>
                        	<textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;"></textarea><br>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">전체 회원 발송 </th>
                        <td style="text-align:left">
							<div class="radio radio-css radio-danger" style="display:inline-block">
                                <input type="radio" name="chk_set1" id="radio_css_1" value="alluser">
                                <label for="radio_css_1">전체 회원 발송</label>
                            </div>
                            &nbsp;&nbsp;
                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                <input type="radio" name="chk_set1" id="radio_css_2" value="seluser" checked>
                                <label for="radio_css_2">선택 발송</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">개별 회원 및 총판 발송 </th>
                        <td tyle="text-align:left">
                        	<select data-placeholder="쪽지를 발송할 회원 및 총판을 선택해주세요." id='sel_user_multi' name='sel_user_multi[]' class="chosen-select" multiple style="width: 100%">
                                <option value=""></option>
                                <?php 
                                if(!empty($db_dataArr_mem)){
                                    foreach($db_dataArr_mem as $row) {
                                ?>
                                <option value="<?=$row['idx']?>"><?=$row['id']?> (<?=$row['nick_name']?>)</option>
                                <?php 
                                     }
                                 }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">일반회원 레벨별 발송 </th>
                        <td>
                            <select id='sel_user_level' name='sel_user_level' style="width: 100%">
                                <option value="0">쪽지를 발송할 회원 레벨을 선택해주세요.</option>
                                <?php 
                                for ($i=1;$i<10;$i++) {
                                    echo "<option value='$i'>$i Level</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">라인 하위 회원 발송 </th>
                        <td>
                            <select name="" id="" style="width: 100%">
                                <option value="">-- 준비중 -- 쪽지를 발송할 대총판 라인을 선택해주세요.</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">총판 하위 회원 발송 </th>
                        <td>
                            <select name="" id="" style="width: 100%">
                                <option value="">-- 준비중 -- 쪽지를 발송할 총판 라인을 선택해주세요.</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">라인 하위 총판 발송 </th>
                        <td>
                            <select name="" id="" style="width: 100%">
                                <option value="">-- 준비중 -- 쪽지를 발송할 대총판 라인을 선택해주세요.</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">총판 하위 총판 발송 </th>
                        <td>
                            <select name="" id="" style="width: 100%">
                                <option value="">-- 준비중 -- 쪽지를 발송할 총판 라인을 선택해주세요.</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                    	<th style="width: 150px; text-align:left">총판회원 등급별 발송 </th>
                        <td>
                            <select name="" id="" style="width: 100%">
                                <option value="">-- 준비중 -- 쪽지를 발송할 총판 등급을 선택해주세요.</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<th style="width: 150px; text-align:left">시스템 그룹 발송 </th>
                        <td>
                            <select name="" id="" style="width: 100%">
                                <option value="">-- 준비중 -- 쪽지를 발송할 그룹을 선택해주세요.</option>
                            </select>
                        </td>
                    </tr>
                </table>                
                
                <div style="height: 20px"></div>
            </div>
            
            <div class="panel_tit">
            	<div><a href="javascript:;" id="adm_btn_msg_send" class="btn h30 btn_green" style="color: white">쪽지 발송</a></div>
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

</script>

</body>
</html>