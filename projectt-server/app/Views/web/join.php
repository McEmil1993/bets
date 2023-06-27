<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">회원가입</div>
</div>

<div class="contents_wrap">
	<div class="join_box">
		<div class="con_box00">
			<div class="write_box write_title_top">			
				<div class="write_tr cf">
					<div class="write_title">아이디</div>
					<div class="write_basic">
						<input class="join_input" style="width: 69%;" maxlength="12" id="join_id" placeholder="아이디를 입력해 주세요." noSpace>
						<div class="write_basic_btn">
							<a href="#"><span class="btn1_2 check_id_btn">중복확인</span></a>
						</div>
					</div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">닉네임</div>
					<div class="write_basic">
                        <input class="join_input" style="width: 69%;" id="join_nickname" placeholder="닉네임을 입력해주세요. (2자 이상 한글)">
                        <div class="write_basic_btn">
							<a href="#"><span class="btn1_2 check_nick_btn">중복확인</span></a>
						</div>
                    </div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">비밀번호</div>
					<div class="write_basic"><input class="join_input" id="join_password" maxlength="12" placeholder="비밀번호를 입력해주세요. (4~12자)"></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">비밀번호확인</div>
					<div class="write_basic"><input class="join_input" id="join_password_r" maxlength="12" placeholder="비밀번호를 한번 더 입력해주세요. (4~12자)"></div>
				</div>	
                <div class="write_tr cf">
					<div class="write_title">생년월일</div>
                    <div class="write_basic">
                        <input id="join_birth_1" class="join_input" maxlength="6" oninput="numberOnly(this)" placeholder="●●●●●●" style="width: 35%;"> -
                        <input id="join_birth_2" class="join_input" maxlength="1" oninput="numberOnly(this)" placeholder="●" style="width: 7%;"><span> ●●●●●● </span>
                    </div>
                </div>
				<div class="write_tr cf">
					<div class="write_title">휴대폰번호</div>
					<div class="write_basic">
                        <select class="join_input" id="mobile_carrier" style="width: 20%;">
                            <option name="radio_set1" id="radio_id0" value="0">SKT</option>
                            <option name="radio_set1" id="radio_id1" value="1">KT</option>
                            <option name="radio_set1" id="radio_id2" value="2">LG</option>
                            <option name="radio_set1" id="radio_id3" value="3">SKT 알뜰폰</option>
                            <option name="radio_set1" id="radio_id4" value="4">KT 알뜰폰</option>
                            <option name="radio_set1" id="radio_id5" value="5">LG 알뜰폰</option>
                        </select>
						<input class="join_input" style="width: 49%;" id="call" oninput="numberOnly(this)" maxlength="11" placeholder="휴대폰번호'-'없이 입력해주세요.)">
						<div class="write_basic_btn">
							<a href="#"><span class="btn1_2 join_btn auth_request" onclick="requestAuthCode()">인증번호전송</span></a>
						</div>
					</div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">인증번호</div>
					<div class="write_basic">
						<input class="join_input" id="auth_code" style="width: 40%;" placeholder="인증번호">
                        <input class="join_input auth_code_time" id="check_auth_code" style="width: 29%;" readonly>
						<div class="write_basic_btn">
							<a href="#"><span class="btn1_2 check_auth_code_btn">인증하기</span></a>
						</div>
					</div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">계좌정보</div>
					<div class="write_basic">
                        <select style="width: 30%;" id="account_bank" class="join_input">
                            <option style="color:gray; font-size: 14px;" value=""> 은행선택 </option>
                            <?php
                            if (!empty($bankList)) {
                                foreach ($bankList as $row) {
                                    ?>
                                    <option  style="color:gray; font-size: 14px;" value="<?= $row['account_code'] ?>" ><?= $row['account_name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <input class="join_input" style="width: 39%;" id="account_number" oninput="numberOnly(this)" placeholder="계좌번호를 입력해 주세요."></div>
                    </div>
				</div>		
				<div class="write_tr cf">
					<div class="write_title">예금주</div>
					<div class="write_basic"><input class="join_input" id="account_name" placeholder="예금주를 입력해 주세요."></div>
				</div>	
                <div class="write_tr cf">
					<div class="write_title">추천인코드</div>
					<div class="write_basic">
                        <input class="join_input reference_code" style="width: 69%;" id="recommand_id" placeholder="추천인 코드를 입력해주세요.">
                        <div class="write_basic_btn">
                            <a href="#"><span class="btn1_2 join_btn login_join_open login_certification_clos reference_code_btn">확인</span></a>
                        </div>
                    </div>
				</div>			
			</div>		
		</div>
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul>
					<li><a href="#"><span class="btn3_1 joinBtn">회원가입완료</span></a></li>
				</ul>
			</div>
		</div>	
	</div>
</div>
<!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>

<script>
let reference_code = '';
let check_id = false;
let check_name = false;
let check_auth_code = false;
let check_recommender = false;

let check_num = /[0-9]/; // 숫자
let check_eng = /[a-zA-Z]/; // 문자
let check_spc = /[~!@#$%^&*()_+|<>?:{}]/; // 특수문자
let check_kor = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/; // 한글체크
let check_kor_complete = /^[가-힣]+$/; // 완성형 한글

let doubleSubmitFlag = false;

$(document).ready(function() {
    function disableKrTxt(e) {
        
        const regex = /[\u3131-\uD79D]/ugi;
        if ($(e.target).val().match(regex)) {
            alert('비밀번호는 영문자만 포함해야 합니다.');
            $(e.target).val("");
            
        }
    }
    
    $('#join_password').keyup(disableKrTxt)
    $('#join_password_r').keyup(disableKrTxt)
    
    $('.joinBtn').on('click', function(){

        const call = $('#call').val();
    	const regExp = /^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/;

    	if (!check_id) {
            alert('아이디 중복체크를 확인해주세요.');
            return;
        }
        
   
        if (!check_name) {
            alert('닉네임 중복체크를 확인해주세요.');
            return;
        }

        if (
            $('#join_password').val().length < 4 ||
            $('#join_password').val().length > 12) {
            alert('비밀번호는 4 ~ 12자 이내 사용해주세요.');
            return;
        }

        if ($('#join_password').val() != $('#join_password_r').val()) {
            alert('비밀번호 확인이 틀립니다.');
            return;
        }
     	
        if ($('#join_birth_1').val() == "") {
            alert("생년월일을 입력해주세요.");
            return false;
        }

        if ($('#join_birth_2').val() == "") {
            alert("주민번호 뒷자리를 입력해주세요.");
            return false;
        }

        if ($('#join_birth_1').val().length != 6) {
            alert("생년월일 6자리로 입력해주세요.");
            return false;
        }
        
     	if (call.trim() == "") {
    		alert("핸드폰 번호를 입력해주세요.");
    		return false;
    	}
        
        if(!regExp.test(call)) {
    		alert("유효하지 않는 전화번호입니다.");
    		return false;
    	};        
        
        if (!check_auth_code) {
            alert('인증코드를 확인해주세요.');
            return;
        }

        if ($('#account_number').val().replace(/ /gi, "").length == 0) {
            alert('계좌번호를 입력해주세요.');
            return false;
        }

        if ($('#account_bank').val() == "") {
            alert('은행을 선택해주세요.');
            return false;
        }
        
        if ($('#account_name').val().trim() == "") {
            alert('예금주를 입력해주세요.');
            return false;
        }

        if (!check_recommender) {
            alert('추천인 코드를 확인해주세요.');
            return false;
        }
        
        //let mobile_carrier = $("input[name='radio_set1']:checked").val();
        let mobile_carrier = $("#mobile_carrier").val();
                
        if(doubleSubmitFlag){
            return;
        }else{
            doubleSubmitFlag = true;
        }
        
        $.ajax({
            url: '/member/join',
            type: 'post',
            data: {
                'id': $('#join_id').val(),
                'password': $('#join_password').val(),
                'nickname': $('#join_nickname').val(),
                'recommend_code': reference_code,
                'call': call,
                'account_bank': $('#account_bank').val(),
                'account_number': $('#account_number').val(),
                'account_name': $('#account_name').val(),
                'authCode': $('#auth_code').val(),
                'birth' : $('#join_birth_1').val()+ '-' + $('#join_birth_2').val(),
                'mobile_carrier': mobile_carrier
            },
        }).done(function (response) {
            alert('회원 가입에 성공하였습니다. 로그인 해주세요!');
            location.href = "/";
            // 성공 시 동작
        }).fail(function (error) {
            console.log(error);
             alert(error.responseJSON['messages']['error']);
             doubleSubmitFlag = false;
        }).always(function (response) {
            // alert('???');
        });
    });

    $('.reference_code_btn').on('click', function () {
        reference_code = $('.reference_code').val();
        if("" == reference_code){
            alert("추천인 코드를 입력하세요.");
            return false;
        }
        
        // 추천인 코드 확인
        let result = false;
        $.ajax({
            url: '/member/checkRecommendCode',
            type: 'post',
            async : false,
            data: {
                'recommend_code': reference_code
            },
        }).done(function (response) {
            // 성공
            result = true;
            check_recommender = true;
            alert("추천인코드가 확인되었습니다.");
            $('#recommand_id').prop('readonly', true);
        }).fail(function (error) {
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {
            // alert('???');
        });
        
        if(!result){
            return false;
        }
    });




    $('.check_id_btn').on('click', function () {

        const id_input = $('#join_id');
        const id = format_noSpace(id_input.val()); // 공백 제거

        if( id.length < 4 ){
            alert("아이디를 4자 이상 입력해주세요");
            id_input.select();
            return false;
        }
;
        if( !check_eng.test(id) ){
            alert("아이디는 영문이 포함되어 있어야 합니다.");
            id_input.select();
            return false;
        }

        if ( check_kor.test(id) || check_spc.test(id) ){
            alert('영문, 숫자만 입력해주세요.');
            id_input.select();
            return false;
        };

        $.ajax({
            url: '/member/idCheck',
            type: 'post',
            data: {
                'id': id,
            },
        }).done(function (response) {
            check_id = confirm('사용가능한 아이디 입니다. 사용하시겠습니까?');
            if (check_id) {
                $('#join_id').attr('readonly', true);
            }
        }).fail(function (error) {
              alert(error.responseJSON['messages']['error']);
        });
    });






    $('.check_nick_btn').on('click', function () {
        if (!check_id) {
            alert('아이디 중복체크를 확인해주세요.');
            $("#join_id").select();
            return false;
        }
        
        const nickname_input = $("#join_nickname");
        const nick_name = format_noSpace(nickname_input.val()); // 공백 제거

        // $('#join_nickname').val($('#join_nickname').val().replace(/ /gi, "")); // 공백 제거
        // let nick_name = $('#join_nickname').val();

        if( nick_name.length < 2 ){
            alert("닉네임은 2자 이상 입력해주세요.");
            nickname_input.select();
            return false;
        }

        if (check_eng.test(nick_name) || check_num.test(nick_name)|| check_spc.test(nick_name)){
            alert('한글만 입력해주세요.');
            nickname_input.select();
            return;
        };
        
        if (!nick_name.match(check_kor_complete)) {
            alert('완전한 단어를 적어주세요.');
            nickname_input.select();
            return;
        };

        $.ajax({
            url: '/member/nickNameCheck',
            type: 'post',
            data: {
                'nick_name': $('#join_nickname').val(),
            },
        }).done(function (response) {
            check_name = confirm('사용가능한 닉네임 입니다. 사용하시겠습니까?');
            if (check_name) {
                $('#join_nickname').attr('readonly', true);
            }
        }).fail(function (error) {
            
              alert(error.responseJSON['messages']['error']);
        });
    });
    
    // 인증확인
    $('.check_auth_code_btn').on('click', function () {
        if(true == check_auth_code){
            alert('이미 인증되었습니다.');
            return;
        }
        
        if ($('#auth_code').val().length <= 0) {
            alert('인증코드를 입력해주세요.');
            return;
        }

        //console.log($('#call_0').val() + $('#call_1').val() + $('#call_2').val());

        $.ajax({
            url: '/member/authCodeCheck',
            type: 'post',
            data: {
                'auth_code': $('#auth_code').val()
            },
        }).done(function (response) {
            alert('인증되었습니다.');
            check_auth_code = true;
            $('#auth_code').val('인증완료');
            $('#call').attr('readonly', true);
        }).fail(function (error, response, p) {
            alert(error.responseJSON.messages.error);
        }).always(function (response) {
        });
    });
});

function requestAuthCode() {
    if(check_auth_code == true){
        alert('이미 인증되었습니다.');
        return;
    }
    
    /* if ($('#call_0').val().replace(/ /gi, "").length < 3 ||
        $('#call_1').val().replace(/ /gi, "").length > 4 ||
        $('#call_2').val().replace(/ /gi, "").length != 4 ) {
        alert('휴대전화를 확인해주세요.');
        return;
    } */

    const call = $('#call').val();
	const regExp = /^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/;
	
	if (call.trim() == "") {
		alert("핸드폰 번호를 입력해주세요.");
		return false;
	}

	if(!regExp.test(call)) {
		alert("유효하지 않는 전화번호입니다.");
		return false;
	};

    //console.log($('#call_0').val() + $('#call_1').val() + $('#call_2').val());

    $.ajax({
        url: '/member/requestAuthCode',
        type: 'post',
        data: {
            'call': call,
            'type': 0,
            'member_idx' : 0
        },
    }).done(function (response) {
        alert('인증코드를 전송하였습니다.');
        $('.auth_code_time').val('3:00');
        $('.auth_request').text('재요청');
        refreshAuthCodeTime();
    }).fail(function (error, response, p) {
        alert(error.responseJSON.messages.error);
    }).always(function (response) {
        // alert('인증코드를 전송하였습니다.');
        // $('.auth_request').text('인증코드 재요청');
    });
}

function refreshAuthCodeTime() {
    let authCodeTime = $('.auth_code_time').val();
    authCodeTime = authCodeTime.split(':');
    let second = Number(authCodeTime[0] * 60) + Number(authCodeTime[1]);
    second = second - 1;
    authCodeTime = Math.floor(second / 60) + ':' + (second % 60);
    if(second >= 0 && check_auth_code == false){
        $('.auth_code_time').val(authCodeTime);
        setTimeout(refreshAuthCodeTime, 1000);
    }
}
</script>
</body>
</html>