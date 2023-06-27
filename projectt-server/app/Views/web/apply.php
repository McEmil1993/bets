<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>
<?php use App\Util\StatusUtil; ?>
<div class="title_wrap">
	<div class="title">충전신청</div>
</div>

<div class="contents_wrap">
    <div class="contents_box">
        <div class="con_box00">
            <div class="tab_wrap">
                <ul>
                    <li><a href="javascript:fnLoadingMove('/web/apply?menu=c')"><span class="tabon">충전하기</span></a></li>
                    <li><a href="javascript:fnLoadingMove('/web/exchange')"><span class="tab">환전하기</span></a></li>
                    <li><a href="javascript:fnLoadingMove('/web/charge_exchange_history')"><span class="tab">충/환전내역</span></a></li>                    
                </ul>
            </div> 
        </div>  
        <div class="con_box10">
            <div class="info_wrap">
                <div class="info2">충전방법 및 주의사항</div>              
                <div class="info3">
                    <span class="info_num">★</span>&nbsp; <span> 최소 충전 금액은 <span class="font05">10,000원 </span>신청가능 , 천단위 이용불가</span> <br>
                    <span class="info_num">★</span>&nbsp; <span> 1회 충전 최대 금액은 <span class="font05">5,000,000원</span> 입니다. [해당금액 이상 이용을 원할 시 분할입금 부탁드립니다.]</span> <br>
                    <span class="info_num">★</span>&nbsp; <span> [실시간] 전용계좌 발급으로 항시 변경이 될 수 있습니다.</span> <br>
                    <span class="info_num">★</span>&nbsp; <span> 본인명의가 아닌 타 명의 입금시 충전 처리 불가</span> <br>
                    <span class="info_num">★</span>&nbsp; <span> 자세한 사항은 공지사항 확인 바랍니다.</span> <br>
                    <span class="info_num">★</span>&nbsp; <span> 신규 이용 시 선택충전 선택 항목이 없으면 [이벤트] 확인 입금 후 고객센터로 참여 문의 바랍니다.</span> <br>
                    <span class="info_num font06">※ &nbsp; 충전 방법 안내</span><br>
                    <span class="font05">&nbsp;&nbsp;&nbsp;&nbsp; 1. 로그인 비밀번호 입력 후 [전용계좌] 확인</span> <br>
                    <span class="font05">&nbsp;&nbsp;&nbsp;&nbsp; 2. 원하는 보너스 선택+입금금액 신청</span> <br>
                </div>
            </div>
        </div>

        <div class="con_box10">
			<div class="write_box write_title_top">			
				<div class="write_tr cf">
					<div class="write_title">입금계좌요청</div>
					<div class="write_basic">
						<input type="password" class="input1 input_password" placeholder="비밀번호">
                        <input type="text" class="input1 deposit_account" value="" placeholder="전용계좌 확인 후 입금계좌가 표시됩니다." readonly> 
						<div class="write_basic_btn">
							<a href="javascript:void(0);"><span class="btn1_2 cash_3">계좌확인</span></a> *로그인 비밀번호를 입력하세요.
						</div>
					</div>
				</div>
                <div class="write_tr cf">
					<div class="write_title">입금계좌정보</div>
					<div class="write_basic">
                        <span><input type="text" class="input1 input_deposit_name" style="margin: 0 10px 0 0;" placeholder="예금주" readonly></span>
                        <span><input type="text" class="input1 input_deposit_number" placeholder="계좌번호" readonly></span>
                        
                        <span class="bonus_text">보너스포인트</span>
                        <span class="bonus_text">
                            <span class="font05" id="bonus_display">
                                <?php 
                                    switch (true) {
                                        case $unexpectedEvent == 1:
                                            echo intval($charge_per)." % &nbsp;&nbsp;&nbsp;";
                                            echo "<span class='font03'>최대보너스</span> ".number_format($charge_money)." 원";
                                            break;
                                        case $member->getRegFirstCharge() == '0':
                                            //가입첫충
                                            echo intval($reg_first_charge)." % &nbsp;&nbsp;&nbsp;";
                                            echo "<span class='font03'>최대보너스</span> ".$charge_max_money." 원";
                                            break;
                                        case $member->getIsExchange() == '1':
                                            //환전여부
                                            echo '0 %';
                                            break;
                                        default:
                                            echo '0 %';
                                            break;
                                    }
                                ?>
                            </span>
                        </span>
                        
					</div>
				</div>	
                            
                <!-- add bonus option -->
                <div class="write_tr cf">
                    <div class="write_title">보너스 선택</div>
                    <div class="write_basic">
                        <?php if($display_select_bonus){
                            $displayBonus = $displayBonus2 = 0;
                        ?>
                        <div>
                            <?php if('ON' == $bonus_infos[0]['flag']){ ?>
                            <div class="radio__item">
                                <input name="charge_point_yn" id="charge_point_1" type="radio" value="0">
                                <label for="charge_point_1">보너스포인트 없음 <span class="font06"><?=$bonus_infos[0]['desc']?></div></label>
                            </div>
                            <?php } ?>
                            <?php if('ON' == $bonus_infos[0]['bonus_1_flag']){
                                list($displayBonus, $displayMaxBonus) = StatusUtil::getBonusPoint($is_charge_first_per, $charge_types, 1);
                            ?>
                            <div class="radio__item">
                                <input name="charge_point_yn" id="charge_point_2" type="radio" value="1">
                                <label for="charge_point_2">보너스포인트 <span class="font05"><?=$displayBonus?></span> 최대보너스 <span class="font05"><?=$displayMaxBonus?></span>&nbsp;<span class="font06"><?=$bonus_infos[0]['bonus_1_desc']?></span></label>
                            </div>
                            <?php } ?>
                            <?php if('ON' == $bonus_infos[0]['bonus_2_flag']){
                                list($displayBonus2, $displayMaxBonus2) = StatusUtil::getBonusPoint($is_charge_first_per, $charge_types, 2);
                            ?>
                            <div class="radio__item">
                                <input name="charge_point_yn" id="charge_point_3" type="radio" value="2">
                                <label for="charge_point_3">보너스포인트 <span class="font05"><?=$displayBonus2?></span> 최대보너스 <span class="font05"><?=$displayMaxBonus2?></span>&nbsp;<span class="font06"><?=$bonus_infos[0]['bonus_2_desc']?></span></label>
                            </div>
                            <?php } ?>

                            <?php if('ON' == $bonus_infos[0]['bonus_3_flag']){
                                list($displayBonus3, $displayMaxBonus3) = StatusUtil::getBonusPoint($is_charge_first_per, $charge_types, 3);
                            ?>
                            <div class="radio__item">
                                <input name="charge_point_yn" id="charge_point_4" type="radio" value="3">
                                <label for="charge_point_4">보너스포인트 <span class="font05"><?=$displayBonus3?></span> 최대보너스 <span class="font05"><?=$displayMaxBonus3?></span>&nbsp;<span class="font06"><?=$bonus_infos[0]['bonus_3_desc']?></span></label>
                            </div>
                            <?php } ?>
                        
                            <?php if('ON' == $bonus_infos[0]['bonus_4_flag']){
                                list($displayBonus4, $displayMaxBonus4) = StatusUtil::getBonusPoint($is_charge_first_per, $charge_types, 4);
                            ?>
                            <div class="radio__item">
                                <input name="charge_point_yn" id="charge_point_5" type="radio" value="4">
                                <label for="charge_point_5">보너스포인트 <span class="font05"><?=$displayBonus4?></span> 최대보너스 <span class="font05"><?=$displayMaxBonus4?></span>&nbsp;<span class="font06"><?=$bonus_infos[0]['bonus_4_desc']?></span></label>
                            </div>
                            <?php } ?>
                        
                            <?php if('ON' == $bonus_infos[0]['bonus_5_flag']){
                                list($displayBonus5, $displayMaxBonus5) = StatusUtil::getBonusPoint($is_charge_first_per, $charge_types, 5);
                            ?>
                            <div class="radio__item">
                                <input name="charge_point_yn" id="charge_point_6" type="radio" value="5">
                                <label for="charge_point_6">보너스포인트 <span class="font05"><?=$displayBonus5?></span> 최대보너스 <span class="font05"><?=$displayMaxBonus5?></span>&nbsp;<span class="font06"><?=$bonus_infos[0]['bonus_5_desc']?></span></label>
                            </div>
                            <?php } ?>
                        </div>
                        <?php }else{ ?>
                        <div>
                            <?php
                                $displayBonus = '이용전 공지사항/이벤트 페이지 확인 후 이용해주시기 바랍니다.';
                            ?>
                            <label for="reject"><?=$displayBonus?></label>
                        </div>
                        <?php } ?>
                    </div>
                </div>       
                <div class="write_tr cf">
                    <div class="write_title">충전금액</div>
                    <div class="write_basic">
                        <input class="input1 input_charge_money" oninput="numberOnly(this)">
                        <div class="write_basic_btn">
                            <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(10000, 'charge')">1만원</span></a>
                            <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(30000, 'charge')">3만원</span></a>
                            <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(50000, 'charge')">5만원</span></a>
                            <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(100000, 'charge')">10만원</span></a>
                            <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(500000, 'charge')">50만원</span></a>
                            <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(1000000, 'charge')">100만원</span></a>
                            <a href="javascript:void(0);"><span class="btn1_1" onclick="clearMoney()">정정</span></a>
                        </div>
                    </div>
                </div>
            </div>		
		</div><!-- end con_box10 -->
        
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul>
					<li><a href="javascript:void(0);"><span class="btn3_1" onclick="moneyCharge()">충전신청하기</span></a></li>
				</ul>
			</div>
		</div>
    </div>
</div><!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript">

    let uKey = '';
    let acNumCheck = false;
    let display_select_bonus = <?=$display_select_bonus?>;
    let level = <?=$level?>;
    let displayBonus = '<?=$displayBonus?>';
    let displayBonus2 = '<?=$displayBonus2?>';
    let displayBonus3 = '<?=$displayBonus3?>';
    let displayBonus4 = '<?=$displayBonus4?>';
    let displayBonus5 = '<?=$displayBonus5?>';
    let displayMaxBonus = '<?=$displayMaxBonus?>';
    let displayMaxBonus2 = '<?=$displayMaxBonus2?>';
    let displayMaxBonus3 = '<?=$displayMaxBonus3?>';
    let displayMaxBonus4 = '<?=$displayMaxBonus4?>';
    let displayMaxBonus5 = '<?=$displayMaxBonus5?>';

    $(document).ready(function () {
        $('.deposit_account').attr('style', 'display: none;');

        $(document).on('click', '.cash_3', function () {
            let password = $('.input_password').val();
            if(0 == password.length){
                alert('패스워드를 입력해주세요.');
                return;
            }
            passwordCheck();
        });

    });

    // 충전금액
    function addMoney(addMoney, type) {
        let money = $('.input_' + type + '_money').val() * 1;
        addMoney = addMoney * 1;
        $('.input_' + type + '_money').val(money + addMoney);
    }
    
    // 정정
    function clearMoney() {
        $('.input_charge_money').val(0);
        $('.input_virtual_charge_money').val(0);
        $('.input_exchange_money').val(0);
    }

    // 비밀번호 확인
    function passwordCheck() {
        $.ajax({
            url: '/member/passwordCheck',
            type: 'post',
            data: {
                'password': $('.input_password').val(),
            },
        }).done(function (response) {
            $.ajax({
                url: '/api/account/getMyAccountInfo',
                type: 'post',
                data: {},
            }).done(function (response) {
                console.log('res', response);
				alert("계좌확인이 완료되었습니다.");
                let account_name = response.data.account.account_name;
                // //let account_bank = response.data.account.account_bank;
                let account_number = response.data.account.account_number;
                let display_account_bank = response.data.account.display_account_bank;
                uKey = response.data.uKey;

                // $('.input_password').val(account_name + ':' + account_number);
                $('.input_password').attr('disabled', 'true');
                if(display_account_bank === ''){
                    $('.deposit_account').val(account_number);
                }else{
                    // $('.deposit_account').val(account_number + '(' + display_account_bank +')');
                    $('.deposit_account').val(display_account_bank);
                }
                
                $('.input_deposit_name').val(account_name);
                $('.input_deposit_number').val(account_number);
                $('.input_password').attr('style', 'display: none;');
                $('.deposit_account').attr('style', 'display: initial;');
                
                acNumCheck = true;

            }).fail(function (error) {
                alert(error.responseJSON['messages']['error']);
                //$('.input_password').val('전용계좌가 조회되지 않습니다. 고객센터에 문의해주세요');
            });

        }).fail(function (error) {
            $('.input_password').attr('placeholder', error.responseJSON['messages']['messages']);
            $('.input_password').val('');
        }).always(function (response) {});
    }

    // 충전신청
    function moneyCharge() {
        if ($('.input_charge_money').val() * 1 < 10000) {
            //$('.cash_5.charge').html('최소 충전 가능 금액은 1만원 입니다.');
            alert('최소 충전 가능 금액은 1만원 입니다.');
            return;
        }
        if ($('.input_charge_money').val() * 1 % 10000 != 0) {
            //$('.cash_5.charge').html('1만원 단위로 충전 신청 가능합니다.');
            alert('1만원 단위로 충전 신청 가능합니다.');
            return;
        }

        if (acNumCheck == false) {
            //$('.cash_5.charge').html('[전용계좌확인]을 먼저 체크해주세요.');
            alert('[전용계좌확인]을 먼저 체크해주세요.');
            return;
        }
        
        // 체크된 보너스옵션 값
        let bonus_idx = -1;
        if (true == display_select_bonus){
            bonus_idx = $('input[name=charge_point_yn]:checked').val();
            if(undefined === bonus_idx) {
                alert('보너스 선택을 주세요.');
                return;
            }
        }

        let account_number = $('.deposit_account').val();

        /*console.log('money', $('.input_charge_money').val());
        console.log('name', $('.input_deposit_name').val());
        console.log('u_key', uKey);
        console.log('account_number', account_number);
        console.log('bonus point', $(".radio__group .radio__item input:checked").val());*/
        
        let path = '/api/memberMoneyCharge/chargeRequest';
        let current_page = path.split("/").pop();
        write_access_log(path, current_page);
        
        $.ajax({
            url: '/api/memberMoneyCharge/chargeRequest',
            type: 'post',
            data: {
                'money': $('.input_charge_money').val(),
                'name': $('.input_deposit_name').val(),
                'u_key': uKey,
                'account_number':account_number,
                'charge_point_yn':$(".radio__group .radio__item input:checked").val(),
                'bonus_idx':bonus_idx,
                'level':level
            },
        }).done(function (response) {
            alert('입금 신청이 완료되었습니다.');
            location.reload();
        }).fail(function (error) {
            //alert(error.responseJSON['messages']['messages']);
            alert(error.responseJSON['messages']['error']);
            // 잘못된 보너스 번호 일때 갱신
            if(1001 == error.responseJSON['error']){
                window.location.reload();
            }
        }).always(function (response) {});
    }    

    $("input:radio[name=charge_point_yn]").click(function(){
        let valueCheck = $('input[name=charge_point_yn]:checked').val(); // 체크된 Radio 버튼의 값을 가져옵니다.
        if(0 == valueCheck){
            $('#bonus_display').html('<span class="font05">0 %</span>');
        }else if(1 == valueCheck){
            $('#bonus_display').html('<span class="font05">'+displayBonus+'</span>&nbsp;'+'&nbsp;<span class="font03">최대보너스</span>&nbsp;'+'&nbsp;<span class="font05">'+displayMaxBonus+'원</span>');
        }else if(2 == valueCheck){
            $('#bonus_display').html('<span class="font05">'+displayBonus2+'</span>&nbsp;'+'&nbsp;<span class="font03">최대보너스</span>&nbsp;'+'&nbsp;<span class="font05">'+displayMaxBonus2+'원</span>');
        }else if(3 == valueCheck){
            $('#bonus_display').html('<span class="font05">'+displayBonus3+'</span>&nbsp;'+'&nbsp;<span class="font03">최대보너스</span>&nbsp;'+'&nbsp;<span class="font05">'+displayMaxBonus3+'원</span>');
        }else if(4 == valueCheck){
            $('#bonus_display').html('<span class="font05">'+displayBonus4+'</span>&nbsp;'+'&nbsp;<span class="font03">최대보너스</span>&nbsp;'+'&nbsp;<span class="font05">'+displayMaxBonus4+'원</span>');
        }else if(5 == valueCheck){
            $('#bonus_display').html('<span class="font05">'+displayBonus5+'</span>&nbsp;'+'&nbsp;<span class="font03">최대보너스</span>&nbsp;'+'&nbsp;<span class="font05">'+displayMaxBonus5+'원</span>');
        }
        
    });
</script>
</body>
</html>