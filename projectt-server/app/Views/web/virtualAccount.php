<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>
<div class="title">충전하기</div>

<div id="contents_wrap">
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
                <div class="info1">1회용계좌 충전방법안내</div>              
                <div class="info3">
                    <span class="info_num">1</span>&nbsp; 충전금액 설정 후 <span class="font05">계좌요청 버튼을 누르시면 아래에 입금 계좌 확인</span>이 가능합니다.<br>
                    <span class="info_num">2</span>&nbsp; ATM입금, 증권계좌,토스 류의 입금형태는 확인이 불가하여 충전처리 되지않습니다.<br>
                    <span class="info_num">3</span>&nbsp; <span class="font05">수표 입금 시 충전 불가 및 이용제재</span> 되오니 주의 하시길 바랍니다.<br>
                    <span class="info_num">4</span>&nbsp; 입금자명과 송금하시는 통장의 예금주(통장명의자)명이 동일해야 즉시 충전처리가 가능하며,예금주명이 상이할 경우 입금 확인이 불가능하여 충전이 불가하거나 지연될 수 있습니다.<br>
                    <span class="info_num">5</span>&nbsp; 보안관련 충전 계좌는 수시로 변경될 수 있으며, 구계좌 입금시 충전확인이 어려우니, <span class="font05">반드시 입금 계좌 확인 후 입금</span>해 주시길 바랍니다.<br>
                    <span class="info_num">6</span>&nbsp; 가상계좌 입금시 <span class="font05">최저 1만원부터 180만원 미만으로</span> 충전 신청 가능합니다. <br>
                    <span class="info_num">7</span>&nbsp; 가상계좌 입금시 <span class="font05">1회 입금금액이 190만원 이상일 경우</span> 충전 처리 불가능합니다. <br>
                    <span class="info_num">8</span>&nbsp; 가상계좌 충전 처리는 <span class="font05">입금 후 5분 이내</span>에 완료가 됩니다. <br>
                    <span class="info_num">9</span>&nbsp; 매일 <span class="font05">23:40분 부터 00:30분까지</span>는 점검시간입니다 점검종료후 이용부탁드립니다.<br>                </div>
            </div>
        </div>
        <div class="con_box10">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="write_title_top">
                <tr>
                    <td class="write_title">충전금액</td>
                    <td class="write_basic">
                        <span style="float:left">
                        <!-- <input type="text" class="input1 input_charge_money" placeholder="충전금액" readonly> -->
                        <input type="text" class="input1 input_charge_money input_virtual_charge_money" placeholder="충전금액" readonly>
                        </span>
                        <span class="write_text">보너스포인트</span>
                        <span class="write_text">
                            <span class="font05">
                                <?php 
                                    switch (true) {
                                        case $member->getRegFirstCharge() == '0':
                                            //가입첫충
                                            echo intval($reg_first_charge)." %";
                                            break;
                                        case $member->getIsExchange() == '1':
                                            //환전여부
                                            echo '0 %';
                                            break;
                                        case $member->getChargeFirstPer() == '0':
                                            //매일첫충
                                            echo $charge_first_per." %";
                                            break;
                                        default:
                                            //매충
                                            echo $charge_per." %";
                                    }
                                ?>
                            </span>&nbsp;&nbsp;
                            <?php 
                                switch (true) {
                                    case $member->getRegFirstCharge() == '0':
                                        //가입첫충
                                        echo "최대 ".$charge_max_money." 원";
                                        break;
                                    case $member->getIsExchange() == '1':
                                        //환전여부
                                        echo '0';
                                        break;
                                    case $member->getChargeFirstPer() == '0':
                                        //매일첫충
                                        echo "최대 ".$charge_max_money." 원";
                                        break;
                                    default:
                                        //매충
                                        echo "최대 ".$charge_money." 원";
                                }
                            ?>
                        </span>
                        <span class="write_text">
                            <input style="margin-left: 20px" name="charge_point_yn" id="charge_point_yn" type="radio" value="1" checked>
                            <label for="accept">받음</label>
                            <input style="margin-left: 10px" name="charge_point_yn" id="charge_point_yn" type="radio" value="0">
                            <label for="reject">받지않음</label>
                        </span>
                        <span class="write_text font03">※ 첫충 5% 매충 3% 기준 롤링 500%입니다.</span>
                    </td>
                </tr>
                <tr>
                    <td class="write_title">충전 및 계좌요청</td>
                    <td class="write_basic" colspan="3">
                        <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(10000, 'charge')">1만원</span></a>
                        <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(30000, 'charge')">3만원</span></a>
                        <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(50000, 'charge')">5만원</span></a>
                        <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(100000, 'charge')">10만원</span></a>
                        <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(500000, 'charge')">50만원</span></a>
                        <a href="javascript:void(0);"><span class="btn1_2" onclick="addMoney(1000000, 'charge')">100만원</span></a>
                        <a href="javascript:void(0);"><span class="btn1_1" onclick="clearMoney()">정정</span></a>
                        <a href="javascript:void(0);"><span class="btn1_1" style="width:180px; margin-left: 10px;" onclick="moneyVirtualCharge()">계좌요청</span></a><!-- moneyCharge() -->
                    </td>
                </tr>
                <tr>
                    <td class="write_title">입금계좌정보</td>
                    <td class="write_basic" colspan="3">
                        <input type="cash_name" class="input1" placeholder="예금주">
                        <input type="cash_account" class="input1" placeholder="계좌번호">
                        <input type="cash_counter" class="input1" placeholder="입금만료시간">
                        * 계좌요청시 입금계좌정보가 표시됩니다.
                    </td>
                </tr>                  
            </table>                
        </div>
        <div class="con_box20">
            <div class="btn_wrap_center">
                <ul>
                    <li>
                        <a href="javascript:void(0);">
                            <span class="btn3_1" onclick="moneyCharge()">충전신청하기</span>
                        </a>
                    </li>
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
                console.log('res', response)
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
                
                $('.input_deposit_name').val(account_name + '(' +  account_number +')');
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
        
        let account_number = $('.deposit_account').val();

        console.log('money', $('.input_charge_money').val());
        console.log('name', $('.input_deposit_name').val());
        console.log('u_key', uKey);
        console.log('account_number', account_number);

        $.ajax({
            url: '/api/memberMoneyCharge/chargeRequest',
            type: 'post',
            data: {
                'money': $('.input_charge_money').val(),
                'name': $('.input_deposit_name').val(),
                'u_key': uKey,
                'account_number':account_number,
                'charge_point_yn':$("input[name='charge_point_yn']:checked").val()
            },
        }).done(function (response) {
            alert('입금 신청이 완료되었습니다.');
            location.reload();
        }).fail(function (error) {
            //alert(error.responseJSON['messages']['messages']);
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {});
    }    

    function moneyVirtualCharge() {
        //console.log($('.input_virtual_charge_money').val());
        if ($('.input_virtual_charge_money').val() * 1 < 10000) {
            //$('.cash_5.charge').html('최소 충전 가능 금액은 1만원 입니다.');
            alert('최소 충전 가능 금액은 1만원 입니다.');
            return;
        }
        if ($('.input_virtual_charge_money').val() * 1 % 10000 != 0) {
            //$('.cash_5.charge').html('1만원 단위로 충전 신청 가능합니다.');
            alert('1만원 단위로 충전 신청 가능합니다.');
            return;
        }
        
        if ($('.input_virtual_charge_money').val() * 1 > 1800000) {
            //$('.cash_5.charge').html('1만원 단위로 충전 신청 가능합니다.');
            alert('180만원 이하로 충전 신청 가능합니다.');
            return;
        }

        console.log($('.input_virtual_charge_money').val());
        console.log(uKey);
        $.ajax({
            url: '/api/memberMoneyCharge/virtualChargeRequest',
            type: 'post',
            data: {
                'money': $('.input_virtual_charge_money').val(),
                'u_key': uKey
            },
        }).done(function (response) {
            console.log(response);
            let account_number = response.data.account_number;
            let account_bank = response.data.account_bank;
            let account_date = response.data.account_date;
            $('.deposit_account').val(account_number + '(' + account_bank +')');
            $('.deposit_account_date').val(account_date);
            $('.deposit_account_name').val('나린마켓');
            alert('입금 신청이 완료되었습니다.');
            //location.reload();
        }).fail(function (error) {
            //alert(error.responseJSON['messages']['messages']);
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {});
    }
</script>
</body>
</html>