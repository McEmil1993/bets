<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');

include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);

if ($p_data['m_idx'] < 1) {
    $UTIL->alertClose('회원정보가 없습니다.');
    exit;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
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
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admMsg.js" charset="utf-8"></script>

<body>

<div class="wrap">
<form id="regform" name="regform" method="post">
<input type="hidden" id="autonum" name="autonum">
<input type="hidden" id="m_idx" name="m_idx" value="<?=$p_data['m_idx']?>">
    <!-- Contents -->
    <div class="" style="margin:0px 10px 0px">
        <!-- list -->
        <div class="panel reserve" style="min-width: 960px; padding: 10px;">
            <i class="mte i_chat mte-2x vam"></i> <h4>회원 메모</h4>
            <div class="tline">
                <div class="tline">
					<table class="table_noline">
                        <tr>
                            <td style="text-align:left;background-color:#6F6F6F;color:#fff">&nbsp;&nbsp;메모 등록</td>
                            <td style="text-align:right;background-color:#6F6F6F;color:#fff">
                                <a href="javascript:self.close();" class="btn h30 btn_gray" data-dismiss="modal">닫기</a>
                                &nbsp;&nbsp;
                            </td>
                        </tr>
                    </table>
                    <table class="mlist">
                        <tr class="">
                            <td style="width:120px">
                            	<select style="width: 100px;" name="memo_type" id="memo_type">
                					<option value="1">일반메모</option>
                					<option value="2">정보변경</option>
                					<option value="3">보안주시</option>
                				</select>
                            </td>
                            <td style="text-align:left;">
                            	<div class="panel_tit" style="height: 5px;">
									<div class="search_form fl">
										<div class="" style="width:1px"></div>
        								<div class="">
            								<input type="text" name="memo_title" id="memo_title" class=""  style="width:700px" placeholder="메모 입력"/>
        								</div>
        								<div class="" style="width:5px"></div>
    									<div ><a href="javascript:getPopUserMemoList('insert',1,0);" class="btn h30 btn_blu" style="color:#fff">등 록</a></div>
									</div>
								</div>
                            </td>
                        </tr>
					</table>
				</div>
                <div style="height: 20px"></div>
                
                <div class="tline" id="pop_memo_list">
                
                </div>
            </div>
        </div>
       
        <!-- END list -->
    </div>
    <!-- END Contents -->
</form>  
</div>
<script type="text/javascript">

function getPopUserMemoList(p_type,p_page=1,memo_idx=0) {

	var memo_type = '';
	var memo_title = '';
	
	if (p_type=='del') {
		var result = confirm('메모를 삭제 하시겠습니까?');
		
	    if (result == false){
	    	return;
	    }
	}
	else if (p_type=='insert') {

		memo_type = $("#memo_type").val();
		var memo_title_buff = $("#memo_title").val();
		memo_title = memo_title_buff.trim();
		
		if (memo_title == '') {
			alert('메모를 입력해 주세요.');
			$("#memo_title").focus();
			return;
		}
		
		var url_memo_title = encodeURIComponent(memo_title);
		
		var result = confirm('메모를 등록 하시겠습니까?');
		
	    if (result == false){
	    	return;
	    }
	}
	
	var m_idx = $("#m_idx").val();
	var no_data = "<table class='mlist'><tr><th>번호</th><th>구분</th><th>메모내용</th><th>일자</th><th>삭제</th></tr><tr><td colspan='5'>데이터를 가져 올 수 없습니다.</td></tr></table>";
	$.ajax({
		type: 'post',
		dataType: 'json',
	    url: '/member_w/_pop_memo_list.php',
	    data:{'m_idx':m_idx,'page':p_page,'p_type':p_type,'memo_idx':memo_idx,'memo_type':memo_type,'memo_title':url_memo_title},
	    success: function (data) {
	    	
	    	if(data['retCode'] == "1000"){
	    		$("#pop_memo_list").html(data['retData']);
			}
	    	else {
	    		$("#pop_memo_list").html(no_data);
	    	}
	    },
	    error: function (request, status, error) {
		    $("#pop_memo_list").html(no_data);
	    }
	});
}

getPopUserMemoList('list',1,0);
</script>
</body>
</html>