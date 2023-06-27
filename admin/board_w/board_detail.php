<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end


$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {
    $idx = $BdsAdminDAO->real_escape_string($_GET['idx']);
	$p_data['sql'] = " SELECT a.idx, a.pdf_attachment, a.member_idx, a.a_id, a.title, a.contents, a.create_dt, a.display, b.nick_name";
	$p_data['sql'] .= " FROM menu_board a ";
	$p_data['sql'] .= " LEFT JOIN member b ON a.a_id = b.id ";
	$p_data['sql'] .= " WHERE a.idx = $idx";

	$db_dataArr = $BdsAdminDAO->getQueryData($p_data);

	$p_data['sql'] = "SELECT a.idx, a.member_idx, a.nick_name, a.comment, a.create_dt FROM menu_board_comment AS a WHERE a.board_idx = $idx ORDER BY a.idx";
	$db_dataCommentArr = $BdsAdminDAO->getQueryData($p_data);

    $BdsAdminDAO->dbclose();
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

    $('ul.tabs li').click(function() {
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#"+tab_id).addClass('current');
    });
});
</script>
<script type="text/javascript" src="/smarteditor28/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admMsg.js" charset="utf-8"></script>

<body>
	<div class="wrap">
		<?php
			$menu_name = "board_menu_3";
			
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
                		<h4>게시글 상세</h4>
            		</a>
        		</div>

        		<!-- list -->
		        <div class="panel reserve">
            		<div class="tline">
                		<table class="mlist">
                   			<input type="hidden"  name="idx" id="idx" value="<?php echo $db_dataArr[0]['idx'] ?>">
							<th>항목</th>
							<th>내용</th>
                    		<tr>
                    			<th style="width: 150px; text-align:left">제 목</th>
                        		<td>
                        			<div class="confing_box">
                                	    <input type="text"  name="send_m_title" id="send_m_title" value="<?php echo $db_dataArr[0]['title'] ?>" <?php if ($db_dataArr[0]['member_idx'] > 0) { echo 'readonly'; } ?> />
                        			</div>
                        		</td>
                    		</tr>
                    		<tr>
                        		<th style="width: 150px; text-align:left">작성자</th>
								<td>
									<div class="confing_box">
										<input type="text"  name="a_id" id="a_id"value="<?php echo $db_dataArr[0]['a_id'] ?>" readonly />
									</div>
								</td>
                    		</tr>
                    		<tr>
	                            <th style="width: 150px; text-align:left">작성일</th>
    	                        <td>
                                    <div class="confing_box">
                                        <input type="text"  name="create_dt" id="create_dt" placeholder="<?php echo $db_dataArr[0]['create_dt'] ?>" readonly />
                                    </div>
                            	</td>
                    		</tr>
							<tr class="wysiswyg">
								<th style="width: 150px; text-align:left">내 용</th>
								<td>
									<?php 
									if ($db_dataArr[0]['member_idx'] == 0) {
									?>
									<div id="loading"></div>
									<textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;">
										<?=$db_dataArr[0]['contents']?>
									</textarea><br>
									<?php 
									} else {
									?>
									<textarea name="b_content" id="b_content" class="readonly" rows="5" cols="100" style="width:780px; height:350px; display:none;">
										<?=$db_dataArr[0]['contents']?>
									</textarea><br>
									<?php 
									}
									?>
								</td>
							</tr>

							<!-- PDF UPLOAD -->
                    		<tr>
                        		<th style="width: 150px; text-align:left">PDF Attachment (Optional)</th>
								<td  style="white-space: normal; !important">
									<div class="confing_box">
									

										<div style="display: none; text-align: center;" class="upload-pdf-loading">
											<div>Uploading PDF Please Wait</div>
											<div>
												<img style="margin: auto;" src="https://media.tenor.com/tEBoZu1ISJ8AAAAC/spinning-loading.gif" />
											</div>
										</div>

										<!-- check if embed is enabled -->
										<?php if ($db_dataArr[0]['pdf_attachment'] != ""): ?>
											<div class="upload-pdf-container" style="display: none"><input accept="application/pdf" type="file" id="pdf_attachment" name="pdf_attachment" value="" /></div>
											<div class="remove-pdf" style="text-align: left;"><button type="button">Remove PDF</button></div>
											<div>
												<input readonly type="text" id="pdf_attachment_url" name="pdf_attachment_url" value="<?=$db_dataArr[0]['pdf_attachment']?>" />
												<div class="upload-pdf-content"><p><embed src="<?=$db_dataArr[0]['pdf_attachment']?>#toolbar=0&navpanes=0" width="100%" height="500px" />
											</div>
										<?php else: ?>
											<div class="upload-pdf-container"><input accept="application/pdf" type="file" id="pdf_attachment" name="pdf_attachment" value="" /></div>
											<div class="remove-pdf" style="text-align: left; display: none;"><button type="button">Remove PDF</button></div>
											<div><input readonly type="text" id="pdf_attachment_url" name="pdf_attachment_url" value="" /></div>
											<div class="upload-pdf-content"></div>
										<?php endif; ?>
									</div>
								</td>
                    		</tr>
                		</table>

						<table class="mlist">
							<th colspan="4">댓글</th>
							<?php 
							foreach ($db_dataCommentArr as $row) {
							?>
							<tr>
								<td><?=$row['nick_name']?></td>
								<td><?=$row['comment']?></td>
								<td><?=$row['create_dt']?></td>
								<td><a href="javascript:fn_del_comment(<?=$row['idx']?>);" class="btn h25 btn_blu adm_btn_notice_del">삭제</a></td>
							</tr>
							<?php 
							}
							?>
						</table>
                		<div style="height: 20px"></div>
            		</div>

					<div class="panel_tit">
						<div align="center">
							<?php 
							if ($db_dataArr[0]['member_idx'] == 0) {
							?>
								<a href="javascript:;" id="adm_btn_notice_send" class="btn h30 btn_green" style="color: white">수정</a>
							<?php
							} else {
							?>
								<a href="javascript:;" id="adm_btn_board_delete" class="btn h30 btn_green" style="color: white">삭제</a>
							<?php
							}
							?>
							
							<a href="/board_w/board_list.php" id="adm_btn_notice_cancel" class="btn h30 btn_green" style="color: white">목록</a>
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
	let content_readonly = false;


	$(document).ready(function()
	{
		//PDF ATTACHMENT
		$(".remove-pdf").click(() =>
		{
			$(".wysiswyg").show();
			$(".upload-pdf-container").show();
			$(".remove-pdf").hide();
			$(".upload-pdf-content").html('');
			$("#pdf_attachment_url").val('');
			$("#pdf_attachment").val('');
		})

		$("#pdf_attachment").change(() =>
		{
			let form_data 	= new FormData();
			let pdf_file 	= $("#pdf_attachment")[0].files

			form_data.append('pdf_attachment', pdf_file[0]);
			

			$(".upload-pdf-loading").show();
			$(".upload-pdf-content").hide();
			
			
			$.ajax(
			{
				type:'POST',
				url: '/board_w/_border_detail_upload_pdf.php',
				data: form_data,
				dataType: 'json',
				cache:false,
				contentType: false,
				processData: false,
				success:function(data)
				{
					$(".upload-pdf-loading").hide();
					$(".upload-pdf-content").show();

					let html_to_append = `<p><embed src="${data.full_path}#toolbar=0&navpanes=0" width="100%" height="500px" />`;

					$(".upload-pdf-content").html(html_to_append);
					$("#pdf_attachment_url").val(data.full_path);
					$(".upload-pdf-container").hide();
					$(".remove-pdf").show();
				},
				error: function(data)
				{
					console.log("error");
					console.log(data);
				}
			});
		});

		// 공지 등록
		$("#adm_btn_notice_send").click(function() {
			var str_msg = '수정 하시겠습니까?';
			var idx = $("#idx").val();
			var msg_title = $("#send_m_title").val();
			var pdf_attachment = $("#pdf_attachment_url").val();
			
			
			// 제목 길이 체크
			if (msg_title.length < 3) {
				alert('제목을 입력해 주세요.');
				$('#send_m_title').select();
				$('#send_m_title').focus();
				return ;
			}
			
			oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []);

			var msg_content = $("#b_content").val();

			if (msg_content.length < 3) {
				alert('내용을 입력해 주세요.');
				$('#b_content').select();
				$('#b_content').focus();
				return;
			}
			
			
			var result = confirm(str_msg);
			if (result) {
				var prctype = "reg";
				
				$.ajax(
				{
					type: 'post',
					dataType: 'json',
					url: '/board_w/_board_prc_update.php',
					data:
					{ 
						'msg_title': msg_title,
						'msg_content': encodeURIComponent(msg_content),
						'idx': idx,
						'pdf_attachment': encodeURIComponent(pdf_attachment),
					},
					success: function (result)
					{
						if(result['retCode'] == "1000")
						{
							alert('수정하였습니다.');
							location.replace('/board_w/board_list.php');
							return;
						}
						else
						{
							alert(result['retMsg']);
							return;
						}
					},
					error: function (request, status, error)
					{
						alert('수정에 실패하였습니다(1).');
						return;
					}
				});
			} else {
				return;
			}
		});

		$('#adm_btn_board_delete').click(function() {
			var str_msg = '삭제 하시겠습니까?';
			var idx = $("#idx").val();

			var result = confirm(str_msg);
			if (result) {
				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/board_w/_board_prc_del.php',
					data: { 'idx': idx },
					success: function (result) {
						if(result['retCode'] == "1000"){
							alert('삭제 하였습니다.');
							location.replace('/board_w/board_list.php');
							return;
						} else {
							alert(result['retMsg']);
							return;
						}
					},
					error: function (request, status, error) {
						alert('삭제에 실패하였습니다(1).');
						return;
					}
				});
			} else {
				return;
			}
		});
		
		// 공지 취소
		/*
		$("#adm_btn_notice_cancel").click(function(){

			if (admMsgInputCheckPOP('reg')==false) {
				return false;
			}
			
			return true;
		});
		*/

		if ($('#b_content').hasClass('readonly')) {
			content_readonly = true;
		} else {
			content_readonly = false;
		}
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
		htParams: {
			bUseToolbar: true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer: true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger: true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//bSkipXssFilter: true,		// client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
			//aAdditionalFontList: aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload: function() {
				//alert("완료!");
			},
			SE_EditingAreaManager: {
				//sDefaultEditingMode : 'HTMLSrc'
			}
		}, //boolean
		fOnAppLoad: function() {
			//예제 코드 
			//oEditors.getById["b_content"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 
			if (content_readonly) {
				var editor = oEditors.getById["b_content"];
				editor.exec('DISABLE_WYSIWYG');
				editor.exec('DISABLE_ALL_UI');
			}
		}, fCreator: "createSEditor2"
	});

	// oEditors.getById["b_content"].exec("SET_IR",[""]);
	// oEditors.getById["b_content"].exec("PASTE_HTML",["test"]);

	function fn_del_comment(idx) {
		var str_msg = '삭제 하시겠습니까?';
		var result = confirm(str_msg);
		if (result) {
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/board_w/_board_comment_del.php',
				data: { 'idx': idx },
				success: function (result) {
					if (result['retCode'] == "1000") {
						alert('삭제 하였습니다.');
						window.location.reload();
						return;
					} else {
						alert(result['retMsg']);
						return;
					}
				},
				error: function (request, status, error) {
					alert('삭제에 실패하였습니다.');
					return;
				}
			});
		} else {
			return;
		}
	}
</script>
</body>
</html>
