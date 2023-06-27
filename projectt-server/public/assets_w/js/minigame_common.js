const setCurrentRound = function() {
    $.ajax({
        url: '/minigame/getCurrentRound',
        type: 'post',
        data: {
            'game': game
        },
    }).done(function(response) {

        remain_time = response['data']['remain_time'] + 4;
        $('.round').text(response['data']['current_round']+ '(' + response['data']['id'] + ')');
        $('.displayRound').text(response['data']['current_round']);
        timer = setInterval(checkRemainTime, 1000);

        getBetList();
        $('#tab_bet_result').hide();
    }).fail(function(error) {
        alert(error.responseJSON['messages']['messages']);
    }).always(function(response) {});
}

const changeWillWinMoney = function() {
    let inputBetMoney = $('.input_style06').val();
    let data = inputBetMoney.replace(/,/gi, ""); //변경작업

    let totalOdds = Number($('.bet_price').text());
    let willMoney = (totalOdds == 0 ? 1 : totalOdds) * Number(data);
    
    //willMoney = Math.ceil(willMoney);
    willMoney = Math.floor(willMoney);
    $('.will_win_money').html(setComma(willMoney));
}

// 금액 버튼
const setBettingMoney = function(money, userMoney) {
    if ($('.bet_info').text().length <= 0) {
        alert('베팅을 선택해주세요.');
        return;
    }

    if (checkRestTime()) {
        alert('휴장시간입니다.');
        return;
    }

    if (money > userMoney) {
        alert('보유머니가 부족합니다.');
        return;
    }

    let inputBetMoney = $('.input_style06').val();
    let data = inputBetMoney.replace(/,/gi, ""); //변경작업
    let maxBetMoney = $('.max_bet_money').text();
    maxBetMoney = maxBetMoney.replace(/,/gi, ""); //변경작업
    let current_bet_money = Number(data);
    let bet_money = current_bet_money + Number(money);

    if (bet_money > userMoney) {
        alert('보유머니가 부족합니다.');
        return;
    }

    if (bet_money > maxBetMoney) {

            alert('최대배팅금 이상 배팅할 수 없습니다');
        return;
    }

    if (!returnMaxCheck(bet_money)) {
        alert('최대당첨금액 제한 : 더이상 배팅할 수 없습니다');
        return;
    }

    $('.input_style06').val(setComma(bet_money));

    changeWillWinMoney();

}

// 남은시간 체크
const checkRemainTime = function() {
    // 시간차감
    let displayRemainTime = '';
    remain_time = remain_time - 1;

    if (remain_time <= 60 || remain_time >= 287) {
        if (remain_time <= 60) {
            displayRemainTime = '베팅마감';
        } else {
            displayRemainTime = '베팅준비중';
        }
    } else {
        let minite = Math.floor(remain_time / 60);
        if (minite < 10) {
            minite = '0' + (minite - 1);
        }

        let second = remain_time % 60;
        if (second < 10) {
            second = '0' + second;
        }

        displayRemainTime = minite + ':' + second;
    }

    // 시간출력
    if (remain_time > 0) {
        $('.remain_time').text(displayRemainTime);

        if(displayRemainTime == "베팅마감") {
                            $('.bettingTimerText').text("");
        }
        if(displayRemainTime == "베팅준비중") {
            $('.bettingTimerText').text("베팅마감");
        }
    }

    // 시간끝났을시 정보호출
    if (remain_time <= 0) {
        setCurrentRound();
        clearInterval(timer);
    }

    if (remain_time === 0) {
        location.reload();
        return false;
    }

}

const showBetList = function(type) {
    $('li[name=showList]').removeClass('mini_tab_wide_td_on');
    $('#betHisTable').hide();
    $('#tab_bet_result').hide();

    if(type=='betHisList') {
        $('#showList1').addClass('mini_tab_wide_td_on');	
        $('#betHisTable').show();
    }

    if(type=='gameResultList') {
        $('#showList2').addClass('mini_tab_wide_td_on');
        $('#tab_bet_result').show();

    }

}

const notifyCloseBtn = function() {
    initForm();
}