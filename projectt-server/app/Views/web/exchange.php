<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">환전신청</div>
</div>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
                    <li><a href="javascript:fnLoadingMove('/web/apply?menu=c')"><span class="tab">충전하기</span></a></li>
                    <li><a href="javascript:fnLoadingMove('/web/exchange')"><span class="tabon">환전하기</span></a></li>
                    <li><a href="javascript:fnLoadingMove('/web/charge_exchange_history')"><span class="tab">충/환전내역</span></a></li>    
				</ul>
			</div>
		</div>
		<div class="con_box10">
			<div class="info_wrap">
				<div class="info2">환전방법안내</div>
				<div class="info3">
                    <span class="info_star">★</span>&nbsp; 1회 최소 환전 금액은 <span class="font06">10,000원</span> [만원단위로만 가능]  <br>
                    <span class="info_star">★</span>&nbsp; 1회 최대 <span class="font06">20,000,000원</span>까지 환전가능<br>
                    <span class="info_star">★</span>&nbsp; 환전 후 재 환전은 환전 완료 1시간 이후 가능<br>
                    <span class="info_star">★</span>&nbsp; 배팅 이용 후 [롤링 조건] 충족시에만 환전 처리가 가능합니다.<br>
                    <span class="info_star font06">※&nbsp; 자세한 사항은 공지사항 확인 부탁드리며, 은행 점검 및 경기시작, 종료 시에는 다소 환전이 지연 될 수 있습니다.<br></span>
				</div>
			</div>
		</div>
		<div class="con_box10">
			<div class="write_box write_title_top">			
				<div class="write_tr cf">
					<div class="write_title">보유금액</div>
					<div class="write_basic"><span class="font11"><?= number_format(session()->get('money')) ?></span> &nbsp;원 <span class="font03">* 최저 10,000원 부터 환전 가능합니다.</span></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">환전금액</div>
					<div class="write_basic">
						<input class="input1 input_exchange_money" oninput="numberOnly(this)>
						<div class="write_basic_btn">
                            <span class="btn1_2" onclick="addMoney(10000, 'exchange')">1만원</span>
                            <span class="btn1_2" onclick="addMoney(30000, 'exchange')">3만원</span>
                            <span class="btn1_2" onclick="addMoney(50000, 'exchange')">5만원</span>
                            <span class="btn1_2" onclick="addMoney(100000, 'exchange')">10만원</span>
                            <span class="btn1_2" onclick="addMoney(500000, 'exchange')">50만원</span>
                            <span class="btn1_2" onclick="addMoney(1000000, 'exchange')">100만원</span>
                            <span class="btn1_1" onclick="clearMoney()">정정</span>
						</div>
					</div>
				</div>
                <div class="write_tr cf">
                    <div class="write_title">배팅롤링정보</div>
                    <div class="write_basic">
                        <ul>
                            <li class="font05">마지막 충전금 : <?= number_format($lastChargeInfo['charge_money'])?></li>
                            <li style="margin-top:5px;">스포츠(프리매치 싱글) : <?= number_format($lastChargeInfo['sports_bet_s_money'])?>(<?= $lastChargeInfo['sports_s_per']?>%)</li>
                            <li style="margin-top:5px;">스포츠(프리매치 다폴) : <?= number_format($lastChargeInfo['sports_bet_d_money'])?>(<?= $lastChargeInfo['sports_d_per']?>%)</li>
                            <li style="margin-top:5px;">스포츠(실시간 싱글) : <?= number_format($lastChargeInfo['real_bet_s_money'])?>(<?= $lastChargeInfo['real_s_per']?>%)</li>
                            <li style="margin-top:5px;">스포츠(실시간 다폴) : <?= number_format($lastChargeInfo['real_bet_d_money'])?>(<?= $lastChargeInfo['real_d_per']?>%)</li>
                            <li style="margin-top:5px;">카지노 : <?= number_format($lastChargeInfo['casino_bet_money'])?>(<?= $lastChargeInfo['casino_bet_per']?>%)</li>
                            <li style="margin-top:5px;">미니/가상/슬롯/해쉬게임 : <?= number_format($lastChargeInfo['mini_bet_money'] + $lastChargeInfo['slot_bet_money'] + $lastChargeInfo['hash_bet_money'])?>
                                (<?= $lastChargeInfo['mini_per'] + $lastChargeInfo['slo_bet_per'] + $lastChargeInfo['hash_bet_per']?>%)</li>
                            <li style="margin-top:5px;">E스포츠/키론 : <?= number_format($lastChargeInfo['esports_bet_money'])?>(<?= $lastChargeInfo['esports_bet_per']?>%)</li>
                        </ul>
                    </div>
                </div>	
			</div>		
		</div>
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul>
					<li><a href="javascript:void(0);"><span class="btn3_1" onclick="moneyExchange()">환전신청하기</span></a></li>
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
<script type="text/javascript">
    $(document).ready(function () {

    });

    // 환전금액
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
        // $('.cash_5.exchange').html('');
    }

    // 환전신청
    function moneyExchange() {
        if ($('.input_exchange_money').val() * 1 < 10000) {
            // $('.cash_5.exchange').html('최소 환전 가능 금액은 1만원 입니다.');
            alert('최소 환전 가능 금액은 1만원 입니다.');
            return
        }
        if ($('.input_exchange_money').val() * 1 % 10000 != 0) {
            // $('.cash_5.exchange').html('1만원 단위로 환전 신청 가능합니다.');
            alert('1만원 단위로 환전 신청 가능합니다.');
            return
        }
        
        let path = '/api/memberMoneyCharge/moneyExchange';
        let current_page = path.split("/").pop();
        write_access_log(path, current_page);

        $.ajax({
            url: '/api/memberMoneyExchange/moneyExchange',
            type: 'post',
            data: {
                'money': $('.input_exchange_money').val(),
            },
        }).done(function (response) {
            alert('환전 신청이 완료되었습니다.');
            location.reload();
        }).fail(function (error) {
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {});
    }    
</script>
</body>
</html>