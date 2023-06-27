<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

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
                    <span class="info_num">1</span>&nbsp; 입금계좌요청란에 <span class="font05">로그인 비밀번호를 입력 후 확인을 누르시면 해당 페이지내에 입금 계좌 확인</span>이 가능합니다.<br>
                    <span class="info_num">2</span>&nbsp; 입금하실 때에는 인터넷뱅킹, 폰뱅킹, 무통장입금, ATM이체 등의 방법을 이용하시면 입금이 가능합니다.<br>
                    <span class="info_num">3</span>&nbsp; <span class="font05">수표 입금 시 충전 불가 및 이용제재</span> 되오니 주의 하시길 바랍니다.<br>
                    <span class="info_num">4</span>&nbsp; 입금자명과 송금하시는 통장의 예금주(통장명의자)명이 동일해야 즉시 충전처리가 가능하며,예금주명이 상이할 경우 입금 확인이 불가능하여 충전이 불가하거나 지연될 수 있습니다.<br>
                    <span class="info_num">5</span>&nbsp; 보안관련 충전 계좌는 수시로 변경될 수 있으며, 구계좌 입금시 충전확인이 어려우니, <span class="font05">반드시 입금 계좌 확인 후 입금</span>해 주시길 바랍니다.<br>
                    <span class="info_num">6</span>&nbsp; 본인명의가 아닌 <span class="font05">타명의에서 예금주명 변경으로 입금시</span> 악성이용으로 간주되며 반환 불가 또는 즉시 탈퇴처리 됩니다. <br>
                    <span class="info_num">7</span>&nbsp; <span class="font05">토스/핀크/카카오페이/케이뱅크/증권계좌/저축은행/하나은행</span> 계좌는 사용이 불가합니다. (간편송금은 피해주시기 바라며 , 송금시 입금 확인시간까지 오랜시간이 지연 될 수 있습니다.) <br>
                    <span class="info_num">8</span>&nbsp; 최소 충전 금액은 <span class="font05">10,000원 부터 충전 신청 가능하며,</span> 천단위 충전은 불가능하므로 만원단위로 이용 부탁드립니다. <br>
                    <span class="info_num">9</span>&nbsp; 충전시 고객센터로 계좌문의를 하지않고 <span class="font05">변경전 계좌로 입금</span>하여 불이익 당하는 일이 없도록 주의 부탁드립니다. <br>
                    <span class="info_num">10</span>&nbsp; 고객센터로 계좌문의 후 <span class="font05">선입금을 먼저 하시고 충전신청</span>을 해주시면 빠른충전이 가능합니다. <br>
                    <span class="info_num font06">★</span>&nbsp; <span class="font06"> 1회 충전 최대 금액 500만원</span> <br>
                    <span class="info_num font06">★</span>&nbsp; <span class="font06"> 카지노 & 슬롯 이용시 보너스포인트 사용 불가</span>
                </div>
            </div>
        </div>

        <div class="con_box10">
                    <div class="write_box write_title_top">			
                        <div class="write_tr cf">
                            <div class="write_title">본인계좌은행</div>
                            <div class="write_basic">
                                <input type="text" class="input1 deposit_account" value="" placeholder="" readonly> 
                            </div>
                        </div>
                    <div class="write_tr cf">
					<div class="write_title">본인계좌정보</div>
					<div class="write_basic">
                        <span><input type="text" class="input1 input_deposit_name" style="margin: 0 10px 10px 0;" placeholder="예금주" readonly></span>
                        <span><input type="text" class="input1 input_deposit_number" placeholder="계좌번호" readonly></span>
                        		</div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">충전금액</div>
					<div class="write_basic">
						<input class="input1 input_charge_money">
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
                <div class="write_tr cf">
					<div class="write_title">보너스 포인트</div>
					<div class="write_basic">
                            
                        <div class="radio__group">
                            <div class="radio__item">
                                <input type="radio" name="bonus" id="bonus_y" value="1" checked />
                                <label for="bonus_y">받음</label>
                            </div>
                            <div class="radio__item">
                                <input type="radio" name="bonus" id="bonus_n" value="0" />
                                <label for="bonus_n">받지않음</label>
                            </div>
                            <div class="radio__item">
                                <span class="font06">카지노&amp;슬롯 이용 회원은 '받지않음'을 선택해주세요.</span>
                            </div>
                        </div>

					</div>
				</div>			
			</div>		
		</div>
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

    //let uKey = '';
    //let acNumCheck = false;

    $(document).ready(function () {
        //$('.deposit_account').attr('style', 'display: none;');
        //console.log('<?=$accountName?>');
        $('.deposit_account').val('<?=$account_bank?>');
        $('.input_deposit_name').val('<?=$accountName_origin?>');
        $('.input_deposit_number').val('<?=$accountNumber?>');
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

        /*if (acNumCheck == false) {
            alert('[전용계좌확인]을 먼저 체크해주세요.');
            return;
        }*/
        
        let account_number = $('.deposit_account').val();

        console.log('money', $('.input_charge_money').val());
        console.log('name', $('.input_deposit_name').val());
        //console.log('u_key', uKey);
        console.log('account_number', account_number);
        console.log('bonus point', $(".radio__group .radio__item input:checked").val());

        $.ajax({
            url: '/api/chargePayKiwoomRequest',
            type: 'post',
            data: {
                'money': $('.input_charge_money').val(),
                'charge_point_yn':$(".radio__group .radio__item input:checked").val()
                //'name': $('.input_deposit_name').val()
                //'u_key': uKey,
            },
        }).done(function (response) {
            console.log(response);
            if(response.result_code == 200){
                clearMoney();
                window.open(response.data.redirectURI);
                //alert('입금 신청이 완료되었습니다.');
                //location.reload();
            }else{
                clearMoney();
                alert(response.messages);
            }
        }).fail(function (error) {
            //alert(error.responseJSON['messages']['messages']);
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {});
    }    

</script>
</body>
</html>