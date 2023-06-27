
function popupWinPost(pop_url,s_title_name,i_pop_h,i_php_w,kind=null,pseq=null,selContent=null) {
	if (pop_url == null || pop_url == undefined) {
		alert('팝업 URL을 확인해 주세요.');
		return;
	}
	
	if (s_title_name == null || s_title_name == undefined) {
		s_title_name = "admin";
	}
	
	if (i_pop_h == null || i_pop_h == undefined || i_pop_h < 400) {
		i_pop_h = 550;
	}
	
	if (i_php_w == null || i_php_w == undefined || i_php_w < 200) {
		i_php_w = 1100;
	}
        
        // 회원상세 화면크기
        if(s_title_name == 'popuserinfo'){
            i_pop_h = 800;
            i_php_w = 1600;
        }

	var frmData = document.popForm ;
	if (kind=='disinfo') {
		frmData.m_dis_id.value = pseq;
	}
	else {
		if(pseq > 0) {
	    	frmData.seq.value = pseq;
	    }
	}
	if (kind !== 'msg' && kind !== 'memo') {
		if (!selContent) {
			frmData.selContent.value = '1';
		} else {
			frmData.selContent.value = selContent;
		}
	}

	var winHeight = document.body.clientHeight;	// 현재창의 높이
	var winWidth = document.body.clientWidth;	// 현재창의 너비
	var winX = window.screenX || window.screenLeft || 0;// 현재창의 x좌표 
	var winY = window.screenY || window.screenTop || 0;	// 현재창의 y좌표 
	var popX = winX + (winWidth - i_php_w)/2+50;
	var popY = winY + (winHeight - i_pop_h)/2+100;
	
	if (kind=='game') {
		if(window.ADMIN_gamewin) {
			ADMIN_gamewin.close();
		}
		
		ADMIN_gamewin = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=yes, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
		
		if(ADMIN_gamewin == null){
			alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
	    }
	}
	else if (kind=='bet_detail') {
		if(window.ADMIN_betdetail_win) {
			ADMIN_betdetail_win.close();
		}
		
		ADMIN_betdetail_win = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=yes, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
		
		if(ADMIN_betdetail_win == null){
			alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
	    }
	}
	else if ( (kind=='msg') || (kind=='memo') ) {
		
		if(window.ADMIN_msg_win) {
			ADMIN_msg_win.close();
		}
		
		ADMIN_msg_win = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=yes, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
		
		if(ADMIN_msg_win == null){
			alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
	    }
		
		if(pseq > 0) {
	    	frmData.m_idx.value = pseq;
	    }
	}
	else if (kind=='userinfo') {
		
		if(window.ADMIN_userinfo_win) {
			ADMIN_userinfo_win.close();
		}
		
		ADMIN_userinfo_win = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=no, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
		
		if(ADMIN_userinfo_win == null){
			alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
	    }
		
		if(pseq > 0) {
	    	frmData.m_idx.value = pseq;
	    }
	}
	else if (kind=='disinfo') {
		
		if(window.ADMIN_disinfo_win) {
			ADMIN_disinfo_win.close();
		}
		
		ADMIN_disinfo_win = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=no, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
		
		if(ADMIN_disinfo_win == null){
			alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
	    }
		
	    frmData.m_dis_id.value = pseq;
	}
	else {
		if(window.ADMIN_betwin) {
			ADMIN_betwin.close();
		}
		
		ADMIN_betwin = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=yes, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
		
		if(ADMIN_betwin == null){
			alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
	    }
	}

    frmData.target = s_title_name;
    frmData.action = pop_url;
    frmData.submit() ;
}


function popupWinPostList(pop_url,s_title_name,i_pop_h,i_php_w,kind=null,memberIdxList=null,selContent=null) {
	if (pop_url == null || pop_url == undefined) {
		alert('팝업 URL을 확인해 주세요.');
		return;
	}
	
	if (s_title_name == null || s_title_name == undefined) {
		s_title_name = "admin";
	}
	
	if (i_pop_h == null || i_pop_h == undefined || i_pop_h < 400) {
		i_pop_h = 550;
	}
	
	if (i_php_w == null || i_php_w == undefined || i_php_w < 200) {
		i_php_w = 1100;
	}
        
        // 회원상세 화면크기
        if(s_title_name == 'popuserinfo'){
            i_pop_h = 800;
            i_php_w = 1600;
        }

	var frmData = document.popForm;
	
	var winHeight = document.body.clientHeight;	// 현재창의 높이
	var winWidth = document.body.clientWidth;	// 현재창의 너비
	var winX = window.screenX || window.screenLeft || 0;// 현재창의 x좌표 
	var winY = window.screenY || window.screenTop || 0;	// 현재창의 y좌표 
	var popX = winX + (winWidth - i_php_w)/2+50;
	var popY = winY + (winHeight - i_pop_h)/2+100;

	if(window.ADMIN_msg_win) {
		ADMIN_msg_win.close();
	}
	
	ADMIN_msg_win = window.open("", s_title_name, 'width='+i_php_w+', height='+i_pop_h+', resizable=0, scrollbars=yes, status=0, titlebar=0, toolbar=0, top='+popY+',left='+popX );
	
	if(ADMIN_msg_win == null){
		alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
    }
	
	if(memberIdxList.length > 0) {
    	frmData.memberIdxList.value = memberIdxList;
    }

    frmData.target = s_title_name;
    frmData.action = pop_url;
    frmData.submit() ;
}

const setUserinfoDetail = function(pop_url,s_title_name,height,width,kind=null,pseq=null) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/member_w/_ajax_user_detail_check.php',
        data: {},
        success: function (result) {
            console.log(result);
            if (result['retCode'] == "1000") {
                console.log('처리하였습니다.');
                popupWinPost(pop_url, s_title_name, height, width, kind, pseq);
            }else{
                $('#user_detail').attr('style', 'display: block');
            }
        },
        error: function (request, status, error) {
        }
    });
}

const fnPopupUserDetailClose = function () {
    $('#user_detail').attr('style', 'display: none');
}

const fnCheckSecodePass = function () {
    let second_pass = $('#second_pass').val();
    if (second_pass === '') {
        alert('2차인증 비번을 넣어주세요.');
        return;
    }

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/member_w/_ajax_second_pass_check.php',
        data: {'second_pass':second_pass},
        success: function (result) {
            console.log(result);
            if (result['retCode'] == "1000") {
                alert('인증성공');
                $('#user_detail').attr('style', 'display: none');
            }else{
                alert(result['retMsg']);
            }
        },
        error: function (request, status, error) {
        }
    });
}