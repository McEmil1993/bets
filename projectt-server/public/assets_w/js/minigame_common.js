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

// 가상축구 남은시간 체크
function checkRemainTimeSoccer(gameCount = 0){
    // 넘어온 다음 게임이 없다.(점검중)
    if(gameCount == 0){
        $('.close_time_1').text('점검중');
        $('.close_time_2').text('점검중');
        $('.close_time_3').text('점검중');
        $('.close_time_11').text('점검중');
        return;
    }

    // 시간차감
    fixture_remain_time_1 -= 1;

    // 첫번째 경기
    let minite = Math.floor(fixture_remain_time_1 / 60);
    if(minite < 10){
        minite = '0'+minite;
    }

    let second = fixture_remain_time_1 % 60;
    if(second < 10){
        second = '0'+second;
    }

    let displayRemainTime_1 = minite + ':' + second;
    $('#timer_' + fixture_id_1).text(displayRemainTime_1);

    if(fixture_remain_time_1 <= 0){
        initForm();
        round = 0;
        bet_markets_id = 0;
    }

    // 리그별시간
    premierShipTime -= 1;
    euroCupTime -= 1;
    superLeagueTime -= 1;
    worldCupTime -= 1;

    // 프리미어십
    minite = Math.floor(premierShipTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }

    second = (premierShipTime - 10) % 60;
    if(second < 10){
        second = '0'+second;
    }
    let displayLeagueTime = minite + ':' + second;
    if(10 > premierShipTime){
        initForm();
        displayLeagueTime = '마감';
    }
    $('.close_time_1').text(displayLeagueTime);

    // 슈퍼리그
    minite = Math.floor(superLeagueTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }

    second = (superLeagueTime - 10) % 60;
    if(second < 10){
        second = '0'+second;
    }
    displayLeagueTime = minite + ':' + second;
    if(10 > superLeagueTime){
        initForm();
        displayLeagueTime = '마감';
    }
    $('.close_time_2').text(displayLeagueTime);

    // 유로컵
    minite = Math.floor(euroCupTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }

    second = (euroCupTime - 10) % 60;
    if(second < 10){
        second = '0'+second;
    }
    displayLeagueTime = minite + ':' + second;
    if(10 > euroCupTime){
        initForm();
        displayLeagueTime = '마감';
    }
    $('.close_time_3').text(displayLeagueTime);

    // 월드컵
    minite = Math.floor(worldCupTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }

    second = (worldCupTime - 10) % 60;
    if(second < 10){
        second = '0'+second;
    }
    displayLeagueTime = minite + ':' + second;
    if(10 > worldCupTime){
        initForm();
        displayLeagueTime = '마감';
    }
    $('.close_time_11').text(displayLeagueTime);

    // 시간종료 체크
    if(premierShipTime <= 0 || euroCupTime <= 0 || superLeagueTime <= 0 || worldCupTime <= 0){
        setCurrentData();
        clearInterval(timer);
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

// 리그별 시간 설정
function set_close_time(leagueTime, currentDate){
    let close_time = display_close_time = display_close_miniutes = display_close_seconds = miniutes = seconds = 0;
    let display_miniutes = display_seconds = '';
    let fixture_date = 0;
    for (const[key, evItem] of Object.entries(leagueTime)) {
        fixture_date = stringToDate(evItem['start_dt']);
        
        // 경기시작까지 남은시간
        close_time = fixture_date.getTime() - currentDate.getTime();
        miniutes = Math.floor((close_time % (1000 * 60 * 60)) / (1000*60));
        seconds = Math.floor((close_time % (1000 * 60)) / 1000);
        
        // 좌측 표시용도
        display_close_time = fixture_date.getTime() - currentDate.getTime() - 10000;
        display_close_miniutes = Math.floor((display_close_time % (1000 * 60 * 60)) / (1000*60));
        display_close_seconds = Math.floor((display_close_time % (1000 * 60)) / 1000);
        if(display_close_miniutes < 10){
            display_miniutes = '0'+display_close_miniutes;
        }else{
            display_miniutes = display_close_miniutes;
        }

        if(display_close_seconds < 10){
            display_seconds = '0'+display_close_seconds;
        }else{
            display_seconds = display_close_seconds;
        }

        let displayRemainTime = display_miniutes + ':' + display_seconds ;
        if(10000 > close_time){
            displayRemainTime = '마감';
        }
//console.log('miniutes : '+miniutes+' seconds : '+seconds+' close_time : '+close_time);
        if(evItem['league'] === 'Premiership'){
            premierShipTime = (miniutes*60) + seconds;
            $('.close_time_1').text(displayRemainTime);
        }else if(evItem['league'] === 'Superleague'){
            superLeagueTime = (miniutes*60) + seconds;
            $('.close_time_2').text(displayRemainTime);
        }else if(evItem['league'] === 'Euro Cup'){
            euroCupTime = (miniutes*60) + seconds;
            $('.close_time_3').text(displayRemainTime);
        }else if(evItem['league'] === 'World Cup'){
            worldCupTime = (miniutes*60) + seconds;
            $('.close_time_11').text(displayRemainTime);
        }
    }
}