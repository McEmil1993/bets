<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">마이페이지</div>
</div>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
					<li><a href="javascript:fnLoadingMove('/web/member_info')"><span class="tab">내정보</span></a></li>
					<!-- <li><a href="javascript:fnLoadingMove('/web/change_password')"><span class="tabon">비밀번호변경</span></a></li> -->
					<li><a href="javascript:fnLoadingMove('/web/recommend_member')"><span class="tab">추천회원리스트</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/point_history')"><span class="tab">포인트내역</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/note')"><span class="tab">쪽지함</span></a></li>
				</ul>
			</div>
		</div>
		<div class="con_box10">
			<div class="write_box write_title_top">			
				<div class="write_tr cf">
					<div class="write_title">현재비밀번호</div>
					<div class="write_basic"><input type="password" class="input1 password_before pwd_alert1"></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">새로운 비밀번호</div>
					<div class="write_basic"><input type="password" class="input1 password_after pwd_alert2"></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">새로운 비밀번호 확인</div>
					<div class="write_basic"><input type="password" class="input1 password_after_ pwd_alert3"></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">핸드폰 번호 입력</div>
					<div class="write_basic">
					<input type="text" class="input1 phone_num" value="<?= session()->get('call') ?>">
						<div class="write_basic_btn">
							<a href="#"><span class="btn1_2 auth_request" onclick="requestAuthCode('<?= session()->get('member_idx') ?>')">인증번호발송</span></a>
						</div>
					</div>
				</div>
                <div class="write_tr cf">
					<div class="write_title">핸드폰 인증번호 확인</div>
					<div class="write_basic">
					    <input class="input1 auth_code"> 
                        <input class="input1 auth_code_time" disabled>
                        <a href="#"><span class="btn1_2 check_auth_code_btn">인증확인</span></a>
					</div>
				</div>			
			</div>		
		</div>
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul>
					<li><a href="#"><span class="btn3_1" onclick="passwordChange();">정보변경</span></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>

<script>
    let check_auth_code = false;
    $(document).ready(function(){
        setTimeout(() => {
            let number = $(".phone_num").val();
            let addNum = "****"
            $(".phone_num").val(number.slice(0,3)+addNum+number.slice(-4));
        }, 100);

        // 인증확인
        $('.check_auth_code_btn').on('click', function () {
            if ($('.auth_code').val().length <= 0) {
                alert('인증코드를 입력해주세요.');
                return;
            }
        
            $.ajax({
                url: '/member/authCodeCheck',
                type: 'post',
                data: {
                    'auth_code': $('.auth_code').val()
                },
            }).done(function (response) {
                alert('인증되었습니다.');
                check_auth_code = true;
                $('.auth_code').val('인증완료');
            }).fail(function (error, response, p) {
                alert(error.responseJSON.messages.error);
            }).always(function (response) {});
        });
    });

    // 인증 코드 요청
    function requestAuthCode(member_idx) {

        console.log('member_idx', member_idx);
        console.log('phone_num', $('.phone_num').val());

        if (check_auth_code == true) {
            alert('이미 인증되었습니다.');
            return;
        }
        const phone_num = $('.phone_num').val();
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

    // 인증 카운트다운
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

    function passwordChange() {
        const pwd_before = $('.password_before');
        const pwd_after = $('.password_after');
        const pwd_after_ = $('.password_after_');
        const pwd_alert1 = $('.pwd_alert1');
        const pwd_alert2 = $('.pwd_alert2');
        const pwd_alert3 = $('.pwd_alert3');
        if (!pwd_before.val() || !pwd_after.val() || !pwd_after_.val()) {
            alert("현재비밀번호 및 새 비밀번호를 입력해주세요");
            return;
        }

        pwd_alert1.addClass('d-none');
        pwd_alert2.addClass('d-none');
        pwd_alert3.addClass('d-none');
        if (pwd_after.val().length < 4 || pwd_after.val().length > 12) {
            pwd_alert2.text('비밀번호는 4자~12자로 설정해주세요.');
            pwd_alert2.removeClass('d-none');
            return;
        }

        if (pwd_after.val() !== pwd_after_.val()) {
            pwd_alert3.text('비밀번호가 확인과 일치하지 않습니다.');
            pwd_alert3.removeClass('d-none');
            return;
        }

        if (check_auth_code == false) {
            alert('인증확인 해주세요.');
            return;
        }

        $.ajax({
            url: '/member/passwordChange',
            type: 'post',
            data: {
                'beforePassword': $('.password_before').val(),
                'afterPassword': $('.password_after').val(),
                'afterPassword_': $('.password_after_').val(),
                'authCode': $('.auth_code').val(),
            },
        }).done(function (response) {
            alert('수정에 성공하였습니다.');
            location.reload();
        }).fail(function (error, response, p) {
            $('.password_before').parent().parent().children('.my_text').html(error.responseJSON.messages.messages);
        }).always(function (response) {});
    }

</script>

</body>
</html>