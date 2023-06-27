


$(document).ready(function(){
	//쪽지 발송 - 관리자 에서 발송
	$("#adm_btn_msg_send").click(function(){

		if (admMsgInputCheck('reg')==false) {
			return false;
		}
		
		return true;
	});
	
	//쪽지 발송 팝업 - 관리자 팝업창 에서 발송
	$("#adm_btn_msg_send_pop").click(function(){
		
		if (admMsgInputCheckPOP('reg')==false) {
			return false;
		}
		
		return true;
	});
	
	//쪽지 리스트 발송 팝업 - 관리자 팝업창 에서 발송
	$("#adm_btn_msg_send_list_pop").click(function(){

		if (admMsgInputCheckListPOP('reg')==false) {
			return false;
		}
		
		return true;
	});
});

// setting 한 쪽지 정보 -> 발송 입력창 불러오기
var retContent=null;
function getSetMsg(mSEQ) {
	if ( (mSEQ < 1) || (mSEQ=='') ) {
		return;
	}
	
	document.getElementById("loading").innerHTML = "Data 로딩중 입니다.";
	$.ajax({
		type: 'post',
		dataType: 'json',
	    url: '/member_w/_get_msg_set.php',
	    data: { 'seq': mSEQ },
	    success: function (result) {
	    	document.getElementById("loading").innerHTML = "";
	    	
	    	if (result['retCode'] == "1000") {
	    		retContent = result['db_content'];
				
	    		$("#send_m_title").val(result['db_title']);
				
	    		oEditors.getById["b_content"].exec("SET_IR", [""]);
	    		oEditors.getById["b_content"].exec("PASTE_HTML", [retContent]);
	    		$("#autonum").val(mSEQ);
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


function admMsgInputCheckPOP(pType=null) {
	
	var str_msg = '선택한 발송대상 에게 쪽지를 발송 하시겠습니까?';
	var set_user = '';
	var msg_title = $("#send_m_title").val();
	var m_idx = $("#m_idx").val();
	
	oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []); 

	var msg_content = $("#b_content").val();
	
	if (msg_title.trim() == "" || msg_title.trim() == null) {
        alert('제목을 입력해 주세요.');
        $('#send_m_title').select();
        $('#send_m_title').focus();
        return false;
	}
	
	const msg_length = msg_content.replace(/\<p\>\&nbsp\;\<\/p\>/gi, '').length;
	if (msg_length === 0) {
		alert('내용을 입력해 주세요.');
		$('#b_content').select();
		$('#b_content').focus();
		return ;
	}
	
	//alert(msg_content);
	var url_bcontent = encodeURIComponent(msg_content);
	//alert(url_bcontent);
	
	var result = confirm(str_msg);
    if (result){
    	var prctype = "reg";
    	
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: '/member_w/_msg_prc.php',
    	    data:{'setUserType':set_user, 'sel_user':'','m_idx':m_idx, 'msg_title':msg_title,'msg_content':url_bcontent},
    	    success: function (result) {
    	    	if(result['retCode'] == "1000"){
    	    		alert('쪽지를 발송 하였습니다.');
    	    		window.location.reload();
    	    		return;
    			}else{
    				alert(result['retMsg']);
    				return;
    			}
    		},
    	    error: function (request, status, error) {
    			alert('쪽지 발송에 실패 하였습니다(1).');
    			return;
    		}
    	});
	}
	else {
		return;
	}
	
	
	return true;
}



function admMsgInputCheck(pType=null) {
	
	var str_msg = '';
	var selUser = '';
	var selLevel = '';
	
	var msg_title = $("#send_m_title").val();
	
	if (msg_title.length < 2) {
		alert('제목을 입력해 주세요.');
		$('#send_m_title').select();
		$('#send_m_title').focus();
		return ;
	}
	
	oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []); 

	var msg_content = $("#b_content").val();
	
	// alert(msg_body);
	const msg_length = msg_content.replace(/\<p\>\&nbsp\;\<\/p\>/gi, '').length;
	if (msg_length === 0) {
		alert('내용을 입력해 주세요.');
		$('#b_content').select();
		$('#b_content').focus();
		return ;
	}
	
	// alert(msg_length);
	var url_bcontent = encodeURIComponent(msg_content);
	
	// if (msg_content.length < 2) {
	// 	alert('내용을 입력해 주세요.');
	// 	$('#b_content').select();
	// 	$('#b_content').focus();
	// 	return ;
	// }
	
	
	var set_user = $(':radio[name="chk_set1"]:checked').val();
	
	if (set_user == 'alluser') {
		str_msg = '전체 유저 에게 쪽지를 발송 하시겠습니까?';
	}
	else {
		str_msg = '선택한 발송대상 에게 쪽지를 발송 하시겠습니까?';
		
		selUser = $('#sel_user_multi').val();
		
		selLevel = $('#sel_user_level').val();
		
		if ( ((selUser == '') || (selUser == null) || (selUser == undefined))&& ( (selLevel < 1)|| (selLevel == null) || (selLevel == undefined)) ) {
			alert('발송 대상을 선택해 주세요.');
			return ;
		}
	}
	
	var result = confirm(str_msg);
    if (result){
    	var prctype = "reg";
    	
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: '/member_w/_msg_prc.php',
    	    data:{'setUserType':set_user, 'sel_user':selUser,'sel_level':selLevel,'msg_title':msg_title,'msg_content':url_bcontent},
    	    success: function (result) {
    	    	if(result['retCode'] == "1000"){
    	    		alert('쪽지를 발송 하였습니다.');
    	    		window.location.reload();
    	    		return;
    			}else{
    				alert(result['retMsg']);
    				return;
    			}
    		},
    	    error: function (request, status, error) {
    			alert('쪽지 발송에 실패 하였습니다(1).');
    			return;
    		}
    	});
	}
	else {
		return;
	}
	
	
	return true;
}

function admMsgInputCheckListPOP(pType=null) {
	
	var str_msg = '선택한 발송대상 에게 쪽지를 발송 하시겠습니까?';
	var set_user = 'selUserList';
	var msg_title = $("#send_m_title").val();
	var m_idx = $("#memberIdxList").val();
	
	if (msg_title.trim() == "" || msg_title.trim() == null) {
        alert('제목을 입력해 주세요.');
        $('#send_m_title').select();
        $('#send_m_title').focus();
        return false;
	}
	
	oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []); 

	var msg_content = $("#b_content").val();
	
	const msg_length = msg_content.replace(/\<p\>\&nbsp\;\<\/p\>/gi, '').length;
	if (msg_length === 0) {
		alert('내용을 입력해 주세요.');
		$('#b_content').select();
		$('#b_content').focus();
		return ;
	}
    
	//alert(msg_content);
	var url_bcontent = encodeURIComponent(msg_content);
	//alert(url_bcontent);
	
	var result = confirm(str_msg);
    if (result){
    	var prctype = "reg";
    	
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: '/member_w/_msg_prc.php',
    	    data:{'setUserType':set_user, 'sel_user':m_idx, 'msg_title':msg_title,'msg_content':url_bcontent},
    	    success: function (result) {
    	    	if(result['retCode'] == "1000"){
    	    		alert('쪽지를 발송 하였습니다.');
    	    		self.close();
    	    		return;
    			}else{
    				alert(result['retMsg']);
    				return;
    			}
    		},
    	    error: function (request, status, error) {
    			alert('쪽지 발송에 실패 하였습니다(1).');
    			return;
    		}
    	});
	}
	else {
		return;
	}
	
	
	return true;
}
