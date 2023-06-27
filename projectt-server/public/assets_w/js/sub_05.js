$.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd', //날짜 포맷이다. 보통 yy-mm-dd 를 많이 사용하는것 같다.
});

function requestAuthCode(member_idx, call) {
    if (check_auth_code == true) {
        alert('이미 인증되었습니다.');
        return;
    }
    const phone_num = call;
    const pwd_before = $(".password_before").val();
    const pwd_after = $(".password_after").val();
    const pwd_after_ = $(".password_after_").val();
    if (!pwd_before || !pwd_after || !pwd_after_) {
        alert("현재비밀번호 및 새 비밀번호를 입력해주세요");
        return;
    }

    $.ajax({
        url: '/member/requestAuthCode',
        type: 'post',
        data: {
            'type': 1,
            'member_idx': member_idx,
            'call': phone_num,
        },
    }).done(function (response) {
        alert('인증코드를 전송하였습니다.');
    }).fail(function (error, response, p) {
        alert(error.responseJSON.messages.messages);
    }).always(function (response) {
        $('.auth_code_time').val('03:00');
        $('.auth_request').text('인증코드 재요청');
        refreshAuthCodeTime();
    });
}


function refreshAuthCodeTime() {
    let authCodeTime = $('.auth_code_time').val();
    authCodeTime = authCodeTime.split(':');
    let second = Number(authCodeTime[0] * 60) + Number(authCodeTime[1]);
    second = second - 1;
    authCodeTime = Math.floor(second / 60) + ':' + (second % 60);
    if (second >= 0 && check_auth_code == false) {
        $('.auth_code_time').val(authCodeTime);
        setTimeout(refreshAuthCodeTime, 1000);
    }
}


function addQ() {
    $.ajax({
        url: '/api/customer_service/qna_1to1/add',
        type: 'post',
        data: {
            'contents': $('.inquiry_input').val(),
        },
    }).done(function (response) {
        let html = "<div class=\"inquiry\">\n" +
                "                                            <div class=\"inquiry_user\">\n" +
                "                                                <div class=\"inquiry_text\">" + $('.inquiry_input').val() + "</div>\n" +
                "                                                <div class=\"user_date\"></div>\n" +
                "                                            </div>\n" +
                "                                        </div>";
        $('.inquiry_wrap').append(html);
        $('.inquiry_input').val('');
    }).fail(function (error, response, p) {

    }).always(function (response) {
    });
}

// 쪽지 클릭
function readMsg(message_idx, read_yn) {
    console.log('readMsg');
    //delete_idx = message_idx;
    if ("Y" == read_yn)
        return;
    $.ajax({
        url: '/api/message/read',
        type: 'post',
        data: {
            'message_idx': message_idx,
        },
    }).done(function (response) {
        console.log(response['data']['time']);
        $('#read_time_' + message_idx).text(response['data']['time']);
        $('#del_btn_' + message_idx).html('<span class="btn_t_inner">삭제</span>');
        $('#del_btn_' + message_idx).attr("onclick", "delMsg(" + message_idx + ")");
        $('#tr_' + message_idx).attr("onclick", "readMsg(" + message_idx + ", 'Y')");
    }).fail(function (error, response, p) {
    });
}

// 쪽지 하나 삭제
function delMsg(message_idx) {
    del_click = true;

    let mes = '삭제하시겠습니까?'
    if (confirm(mes) == false) {
        return;
    }

    $.ajax({
        url: '/web/betting_history/deleteMessage',
        type: 'post',
        data: {
            'idx': message_idx,
        },
    }).done(function (response) {
        alert("쪽지가 삭제되었습니다.");
        location.replace('/web/betting_history?menu=d');
    }).fail(function (error) {
        alert(error.responseJSON['messages']['error']);
    });
}
;

function searchBtnClick(betGroup) {

    // let today = getFormatDate(new Date());
    // let betFromDate = getAddDate(today, -7);
    // let betToDate = getAddDate(today, 1);

    // $('#loadingCircle').show();
    // location.href = '/web/betting_history?menu=b&bet_group=' + betGroup +
    //         '&betFromDate=' + betFromDate +
    //         '&betToDate=' + betToDate +
    //         '&clickItemNum=' + betGroup;


    let today = getFormatDate(new Date());
    let betFromDate = getAddDate(today, -7);
    let betToDate = getAddDate(today, 1);

    $('#loadingCircle').show();

    if (betGroup == 4) {
        location.href = '/web/betting_history?prd_type=C&prd_id=1&clickItemNum=' + betGroup;
    }else if (betGroup == 5) {
        location.href = '/web/betting_history?prd_type=S&prd_id=201&clickItemNum=' + betGroup;
    }else if (betGroup == 8) {
        location.href = '/web/betting_history?prd_type=e&prd_id=101&clickItemNum=' + betGroup;
    }else if (betGroup == 9) {
        location.href = '/web/betting_history?clickItemNum=' + betGroup;
    }else{
        location.href = '/web/betting_history?menu=b&bet_group=' + betGroup +
        '&betFromDate=' + betFromDate +
        '&betToDate=' + betToDate +
        '&clickItemNum=' + betGroup;
    }
}


function onBetCancel(idx, bet_status) {
    if (bet_status > 1) {
        alert('취소할 수 없습니다.');
        return;
    }

    var str_msg = '해당 배팅이 취소처리 됩니다.\n그래도 취소처리 하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            url: '/api/betting_cancel',
            data: {'idx': idx},
        }).done(function (response) {
            alert(response['messages']);
            window.location.reload();
        }).fail(function (error) {
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {
        });
    }
};

function onBetHistory(idx, type) {
	
	$('#loadingCircle').show();
	
	//특정부분 스크린샷
	html2canvas(document.getElementById(idx), {
		backgroundColor: "#333333"
	}).then(function (canvas) {
		
		var img = canvas.toDataURL('image/png');
		const clickItemNum = $('#clickItemNum').val();
		
		var newForm = $('<form method="post"></form>');
		newForm.attr("name", "newForm");
		newForm.attr("action","/"+type+"/border/writeAddImage");
		newForm.append($('<input/>', {type: 'hidden', name: 'imgSrc', value: img}));
		newForm.append($('<input/>', {type: 'hidden', name: 'clickItemNum', value: clickItemNum}));
		newForm.appendTo('body');
		newForm.submit();

	}).catch(function (err) {
		console.log(err);
	});
	
};

function drawImg(imgData) {
	console.log(imgData);
	//imgData의 결과값을 console 로그롤 보실 수 있습니다.
	return new Promise(function reslove() {
	//내가 결과 값을 그릴 canvas 부분 설정
	var canvas = document.getElementById('canvas');
	var ctx = canvas.getContext('2d');
	//canvas의 뿌려진 부분 초기화
	ctx.clearRect(0, 0, canvas.width, canvas.height);

	console.log(canvas.width);
	console.log(canvas.height);
	
	var imageObj = new Image();
	imageObj.onload = function () {
	ctx.drawImage(imageObj, 10, 10);
	//canvas img를 그리겠다.
	};
	imageObj.src = imgData;
	//그릴 image데이터를 넣어준다.

	}, function reject() { });

}

function onBetHide(idx) {

    str_msg = '배팅이 삭제처리되면 재확인이 불가능합니다.\n그래도 삭제처리 하시겠습니까?';
    result = confirm(str_msg);
    if (!result) {
        return;
    }

    $('#loadingCircle').show();
    if (result) {
        $.ajax({
            type: 'post',
            url: '/api/betting_hide',
            data: {'idx': idx},
        }).done(function (response) {
            alert('해당 배팅의 삭제처리가 완료되었습니다.');
            window.location.reload();
        }).fail(function (error) {
            $('#loadingCircle').hide();
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {
        });
    }
};



function onBetMiniGameHide(idx) {

    str_msg = '배팅이 삭제처리되면 재확인이 불가능합니다.\n그래도 삭제처리 하시겠습니까?';
    result = confirm(str_msg);
    if (!result) {
        return;
    }

    $('#loadingCircle').show();
    if (result) {
        $.ajax({
            type: 'post',
            url: '/api/bettingMiniGameHide',
            data: {'idx': idx},
        }).done(function (response) {
            alert('해당 배팅의 삭제처리가 완료되었습니다.');
            window.location.reload();
        }).fail(function (error) {
            $('#loadingCircle').hide();
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {
        });
    }
};


function Commas(x) {
    if (x === undefined)
        return 0;
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}