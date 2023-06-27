/*
 * 실시간 공통
 */
// pc, mobile common
function changeWillWinMoney() {
    betMaxCheck();
    let inputBetMoney = $('#betting_slip_money').val();
    let data = inputBetMoney.replace(/,/gi,""); //변경작업
    let willMoney = 0;
    let totalOdds = 1;
    let bonus_odds = $('.bonus_total_odds').text();

    bonus_odds = +bonus_odds.replace(/,/gi,""); //변경작업
    bonus_odds = bonus_odds;
    if (!bonus_odds) {
        bonus_odds = 1;
    }

    const oddArr = [];

    let betSlip = $('.slip_bet_ing .sports_cart_bet_p').each(function() { 
        oddArr.push(Number($(this).text().trim()));
    });
    let betSlipCount = getBetSlipCountReal();

    if (!betSlip || !betSlip[0]) {
        betSlip = [];
    }
    if (betSlipCount >= 3) {
        bonus_odds = +odds_3_folder_bonus;
    }
    if (betSlipCount >= 4) {
        bonus_odds = +odds_4_folder_bonus;
    }
    if (betSlipCount >= 5) {
        bonus_odds = +odds_5_folder_bonus;
    }
    if (betSlipCount >= 6) {
        bonus_odds = +odds_6_folder_bonus;
    }
    if (betSlipCount >= 7) {
        bonus_odds = +odds_7_folder_bonus;
    }

    for (let odd of oddArr) {
        totalOdds *= odd;
    };

    //totalOdds = +totalOdds * +bonus_odds;
    totalOdds = +totalOdds;

    willMoney = (+totalOdds * +bonus_odds) * +data;
    willMoney = (totalOdds) * +data;
    willMoney = Math.ceil(willMoney);
    
    $('.total_odds').data("total_odds", totalOdds);
    $('.total_odds').text(setComma(totalOdds.toFixed(2)));
    bonus_odds === 1 ? $('.bonus_total_odds').text(0) : $('.bonus_total_odds').text(bonus_odds);
    if(willMoney > limitBetMoney){
        alert('최대당첨금액 제한 : 배팅할 수 없습니다');
        prevBetMoney = 1;
        return true;
    }else{
        prevBetMoney = 0;
        $('.will_win_money').html(setComma(willMoney));

    }

}

function betMaxCheck() {
    let betSlip = $('.slip_bet_ing');
    let leagues_m_bet_money = Infinity;
    if (!betSlip || !betSlip[0]) {
        $('.max_bet_money').text(setComma(+maxBetMoney));
        return;
    }
    leagues_m_bet_money = +betSlip.data('leagues_m_bet_money');

    if(+leagues_m_bet_money < +maxBetMoney) {
        $('.max_bet_money').text(setComma(+leagues_m_bet_money));
    } else {
        $('.max_bet_money').text(setComma(+maxBetMoney));
    };
    return;
}

// 금액 버튼
function setBettingMoney(money, userMoney) {
    // userMoney = $('.util_money').text();
    userMoney = userMoney ? userMoney : Nummber(format_remove($('.util_money').text()));
    
    // userMoney = userMoney.replace(/,/gi,"");
    //userMoney = format_remove(userMoney);

    if (money > userMoney) {
        alert('보유머니가 부족합니다.');
        return;
    }

    //let totalMax = Infinity;

    // let betSlip = $('.slip_bet_ing');
    let betSlip = $('.sports_cart_bet');
    if (!betSlip || !betSlip[0]) {
        alert('경기를 선택해주세요.');
        return;
    }
    //totalMax = +betSlip.data('leagues_m_bet_money');
    let inputBetMoney = $('#betting_slip_money').val();

    let data = inputBetMoney.replace(/,/gi,""); //변경작업

    let current_bet_money = Number(data);
    let bet_money = current_bet_money + Number(money);

    let maxBetMoney = $('.max_bet_money').text();
    maxBetMoney = +maxBetMoney.replace(/,/gi,""); //변경작업

    if (maxBetMoney < bet_money) {
        alert('베팅금액을 초과했습니다');
        return;
    }
    if (bet_money > userMoney) {
        alert('보유머니가 부족합니다.');
        return;
    }
    $('#betting_slip_money').val(setComma(bet_money));

    changeWillWinMoney();
    if(prevBetMoney == 1){
        $('#betting_slip_money').val(setComma(current_bet_money));
    }
    let max_flag = auto_calc(limitBetMoney);
    if(max_flag === true) {
        alert('최대당첨금액 제한 : 더이상 배팅할 수 없습니다');
        return;
    }
}

function waitRealBet(live_score_1, live_score_2, arrBetSlip){
    let fixture_id = 0;
    //let bet_data = '';
    let betId = 0;
    $('.slip_bet_ing').each(function (index, item) {
        betId = $(item).data('bet-id');
        fixture_id = $(item).data('fixture-id');
    });

    if(isAlreadyBetting)
        return false;
    isAlreadyBetting = true;
    $.ajax({
        url: '/api/real_time/checkRealTimeGameLiveScore',
        type: 'post',
        data: {
            'fixture_id': fixture_id,
            'live_score_1': live_score_1,
            'live_score_2': live_score_2,
            'betId' : betId,
            'betList' : JSON.parse(arrBetSlip)
        },
    }).done(function (response) {
        bet_send();
        //location.reload();
        //console.log('checkRealTimeGameLiveScore success');
        return;
    }).fail(function (error) {
        console.log(error);
        alert(error.responseJSON['messages']['messages']);
        $('#bettingLoadingCircle').hide();
        return;
        //setTimeout(getRealTimeGameLiveScoreList(0, 0), 5000);
    }).always(function (response) {
        isAlreadyBetting = false;
        //$('#bet_loading').hide();
    });
}

function auto_calc(max = 10000000) {
    max = +max;
    let will_win = $('.will_win_money').text();
        will_win = Number(will_win.replace(/,/gi,""));
        if (will_win > max) {
            let calc = calcMaxMoney(max);
            if (+calc !== 0 ) {
                $('#betting_slip_money').val(setComma(""+calc));
                changeWillWinMoney();
            };
            return true;
    }
    return false;
}

function calcMaxMoney(max_bet_money) {
    let inputBetMoney = $('#betting_slip_money').val();
    let data = inputBetMoney.replace(/,/gi,""); //변경작업
    let will_win_money = $('.will_win_money').text();
    let totalOdds = $('.total_odds').data("total_odds");
    let bonus_odds = $('.bonus_total_odds').text();
    will_win_money = +will_win_money.replace(/,/gi,"");
    bonus_odds = +bonus_odds.replace(/,/gi,"");

    let willMoney = 0;
    let odds = totalOdds === 0 ? 1 : totalOdds;
    if (bonus_odds === 0) {
        bonus_odds = 1;
    }
    willMoney = (odds * bonus_odds) * data;
    willMoney = Math.ceil(willMoney);
    max_bet_money = +max_bet_money;

    // 주석부분은 스포츠에 되어있는 부분
    // 모바일 부분이 맞는것 같아서 모바일 코드로 대체
    if (max_bet_money < willMoney) {
        //let ret = +max_bet_money / +totalOdds;
        //return parseInt(ret) - 1;
        let ret = +max_bet_money / (+totalOdds * +bonus_odds);
        ret = ret > 10000 ? Math.floor(ret) : +ret;
        return parseInt(ret);
    } else {
        //let ret = +willMoney / +totalOdds;
        //return parseInt(ret) - 1;
        let ret = +willMoney / (+totalOdds * +bonus_odds);
        ret = ret > 10000 ? Math.floor(ret) : +ret;
        return parseInt(ret);
    }
}

function initForm() {
    console.log('initForm');
    let totalOdds = 0;
    $('.slip_bet_ing').remove();
    $('.bet_on').removeClass('bet_on');
    $('.total_odds').data('total_odds', totalOdds);
    $('.total_odds').html(totalOdds);
    $('.bonus_total_odds').html(0);
    $('#betting_slip_money').val(0);
    $('.will_win_money').html(0);
    if(isMobile){
        $('.cart_count2').text($('.slip_bet_ing').length);
    }
    changeWillWinMoney();
}

function bet_send() {
    let memberBetList = [];
    if ($('.slip_bet_ing').length > 0) {

        let betFolderType = 'S';
        if ($('.slip_bet_ing').length > 1) {
            betFolderType = 'D';
        }
        
        let betUrl = '/api/bet/addBet';
        /*if('ON' == is_betting_slip){
            betUrl = '/api/bet/onAddBet';
        }*/
        
        $('.slip_bet_ing').each(function (index, item) {
            //let oddsType = $(item).data('oddsTypes');
            let bet_data = betList_new.get($(item).data('index'));
            memberBetList.push({
                'betId': $(item).data('bet-id'),
                'betPrice': $(item).data('bet-price'),
                'fixtureId': bet_data['fixture_id'],
                'round': 0,
                'marketsId': bet_data['markets_id'],
                'marketsName': bet_data['markets_name_origin'],
                'betBaseLine': bet_data['bet_base_line'],
                'oddsTypes': $(item).data('oddsTypes'),
                'leagueId': bet_data['fixture_league_id'],
                'leagueTagId': 'leagues_bet_'+bet_data['fixture_id'],
                'fixture_start_date': bet_data['fixture_start_date'],
                'leagues_m_bet_money': bet_data['leagues_m_bet_money'],
                'betName' : $(item).data('bet-name')
            })
        });
        console.log('add bet start keep : '+localStorage.getItem("keep_login_access_token"));
        let betMoney = $('#betting_slip_money').val();
        betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
        let totalOdds = $('.total_odds').data("total_odds");
        $.ajax({
            url: betUrl,
            type: 'post',
            data: {
                'betList': memberBetList,
                'betType': 2,
                'totalOdds': totalOdds,
                'totalMoney': betMoney,
                'betGroup': '실시간',
                'folderType': betFolderType,
                'isBettingSlip': is_betting_slip,
                'keep_login_access_token': localStorage.getItem("keep_login_access_token")
            },
        }).done(function (response) {
            initForm();
            // key reflush
            const keep_login_access_token = response['data']['keep_login_access_token'];
            console.log('add bet result : '+keep_login_access_token);
            localStorage.setItem('keep_login_access_token', keep_login_access_token);
            
            let check = confirm('베팅에 성공하였습니다. 베팅내역을 확인하시겠습니까?');
                if(check === true){
                    $('#loadingCircle').show();
                    location.href = "/web/betting_history?menu=b&bet_group=1&clickItemNum=1";
                    $('#loadingCircle').hide();
                    $('#bettingLoadingCircle').hide();

                    return;
                } else {
                    $('.util_money').text(setComma(response['data']['total_money']));
                    response['data']['arr_tag_ids'].forEach(function(item,index,arr2){
                        let tag = '#'+item;
                        //alert(tag);
                        let str_league_money = $(tag).text();
                        //alert(str_league_money);
                        let n_league_money = 0;
                            //alert(str_league_money);
                            if(0 <= str_league_money.indexOf('만')){
                                str_league_money = str_league_money.replace(/,/gi,"");
                                n_league_money = Number(str_league_money.replace(/만/gi,""))*10000;
                            }else{
                                n_league_money = Number(str_league_money.replace(/,/gi,""));
                            }
                            //alert(n_league_money);
                            n_league_money = n_league_money - response['data']['total_bet_money'];
                            n_league_money = parseInt(n_league_money);
                            let betIndex = $(tag).closest('tr').find('.odds_btn').first().data('index');
                            /*betList[betIndex]['leagues_m_bet_money'] = n_league_money;

                            if (n_league_money > 10000) {
                                n_league_money = setComma(parseInt(n_league_money / 10000)) + '만';
                            } else {
                                n_league_money = setComma(parseInt(n_league_money));
                            }
                            $(tag).text(setComma(n_league_money));*/
                            $('#bettingLoadingCircle').hide();

                    })
                    //$('#loadingCircle').show();
                    //location.reload();
                    // 딤레이어, 카트창 닫기
                    $('.loding_wrap').hide();

                    $('#bettingLoadingCircle').hide();
                    $('.loading-overlay').remove();

                    //$('.cart_close').trigger('click');
                    return;
                }
        }).fail(function (error) {
            // key reflush
            /*const keep_login_access_token = error.responseJSON['messages']['keep_login_access_token'];
            console.log('add bet result : '+error.responseJSON['messages']['keep_login_access_token']);
            if('' != keep_login_access_token){
                console.log('fial add bet result : '+error.responseJSON['messages']['keep_login_access_token']);
                localStorage.setItem('keep_login_access_token', keep_login_access_token);
            }*/
            
            alert(error.responseJSON['messages']['messages']);
            //setTimeout(getRealTimeGameLiveScoreList(0, 0), 5000);
            //location.reload();
            if(error.responseJSON['error'] == 401){
                location.reload();
            }
        }).always(function (response) {
            $('#bettingLoadingCircle').hide();
            $('#loadingCircle').hide();
            $('.loading-overlay').remove();
            
            let path = '/api/bet/addBet?bet_group=1';
            let current_page = 'addBet';
            write_access_log(path, current_page);
        });
    }else {
        alert('베팅을 선택해주세요.');
    }
};

// 스코어보드 가져온다.
function getScoreBoard(sports_id, fixture_id) {

    let score_html = '';

    if(sports_id == 6046) { // 축구
        score_html = `
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">스코어</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">코너킥</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">옐로카드</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">레드카드</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">교체</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">페널티킥</td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 font05" id="participants_1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_1_1_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_1_6_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_1_7_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_1_10_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_1_8_${fixture_id}">0</span></td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 font05" id="participants_2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_2_1_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_2_6_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_2_7_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_2_10_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2" id="participants_2_8_${fixture_id}">0</span></td>
                </tr>
            </table>
        `;

    } else if(sports_id == 48242) { // 농구
        score_html = `
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">1q</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">2q</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">Half</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">3q</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">4q</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">T</td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 q1_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q2_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 qh_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q3_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q4_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q6_p1_score_${fixture_id} font05">0</span></td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 q1_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q2_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 qh_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q3_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q4_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q6_p2_score_${fixture_id} font05">0</span></td>
                </tr>
            </table>
        `;

    }else if(sports_id == 154830){ // 배구
        score_html = `
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">1s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">2s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">3s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">4s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">5s</td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 v1_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v2_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v3_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v4_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v5_p1_score_${fixture_id}">0</span></td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 v1_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v2_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v3_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v4_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 v5_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 font05">0</span></td>
                </tr>
            </table>
        `;
    }else if(sports_id == 35232){ // 아이스하키
        score_html = `
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">1p</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">2p</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">3p</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">T</td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 q1_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q2_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q3_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q6_p1_score_${fixture_id} font05">0</span></td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 q1_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q2_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q3_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q6_p2_score_${fixture_id} font05">0</span></td>
                </tr>
            </table>
        `;

    }else if(sports_id == 687890){ // E게임 
        score_html = `
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">1s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">2s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">3s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">4s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">5s</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">T</td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 q1_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q2_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q3_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q4_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q5_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q6_p1_score_${fixture_id} font05">0</span></td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font2 q1_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q2_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q3_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q4_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q5_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font2 q6_p2_score_${fixture_id} font05">0</span></td>
                </tr>
            </table>
        `;

    } else { // 야구
        score_html = `
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">1i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">2i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">3i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">4i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">5i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">6i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">7i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">8i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">9i</td>
                    <td width="10%" height="40" align="center" style="background:var(--bgtitleimg2); font-size:11px; color:#f9f9f9;">T</td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font22 i1_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i2_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i3_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i4_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i5_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i6_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i7_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i8_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i9_p1_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i99_p1_score_${fixture_id} font05">0</span></td>
                </tr>
                <tr>
                    <td height="60" align="center"><span class="live_font22 i1_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i2_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i3_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i4_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i5_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i6_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i7_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i8_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i9_p2_score_${fixture_id}">0</span></td>
                    <td height="60" align="center"><span class="live_font22 i99_p2_score_${fixture_id} font05">0</span></td>
                </tr>
            </table>
        `;
    }
    return score_html;
}

// 라이브 점수 업데이트
function setLiveScore(mainGame){
    let fixture_id = mainGame['fixture_id'];
    
    // 팀명
    $('.participants_1_name_'+mainGame['fixture_id']).html(mainGame['fixture_participants_1_name']);
    $('.participants_2_name_'+mainGame['fixture_id']).html(mainGame['fixture_participants_2_name']);

    let thisSId = mainGame['fixture_sport_id'];
    
    if(mainGame['livescore'] == '' || mainGame['livescore'] == null){
        //console.log('set null: '+ mainGame['livescore']);
        return;
    }
    
    let event = JSON.parse(mainGame['livescore']);
    let minute = 0;
    let second = 0;

    // 스코어 정보
    if (thisSId == 6046) {
        // 하단 합계점수를 출력한다.
        let display_score = event['Scoreboard']['Results'][0]['Value'] + ' : ' + event['Scoreboard']['Results'][1]['Value'];
        $('.display_score_'+fixture_id).text(display_score);

        $('#participants_1_score_'+mainGame['fixture_id']).html(event['Scoreboard']['Results'][0]['Value']);
        $('#participants_2_score_'+mainGame['fixture_id']).html(event['Scoreboard']['Results'][1]['Value']);

        // 경기정보 좌측 진행상태, 시간
        minute = Math.floor(mainGame['live_time']/60);
        second = Math.floor(mainGame['live_time']%60);
        if(minute < 10) minute = '0'+minute;
        if(second < 10) second = '0'+second;
        $('#live_1_'+fixture_id).text(mainGame['live_current_period_display']);
        $('#live_1_time_'+fixture_id).text(minute  + ":"+ second);
        
        // 코너킥(1), 옐로카드(6), 레드카드(7), 프리킥(지원안함), 페널티킥(8), 교체(10)
        if (event['Statistics'].length > 0) {
            event['Statistics'].forEach(function (item) {
                $('#participants_1_' + item['Type']+'_'+mainGame['fixture_id']).html(item['Results'][0]['Value']);
                $('#participants_2_' + item['Type']+'_'+mainGame['fixture_id']).html(item['Results'][1]['Value']);
            });
        }

        // 경기 상태
        let score_status =
            mainGame['live_current_period_display']
           + "<span id=\"score_status_time\" class=\"soccer_score_time\">" + mainGame['live_time']/60  + ":00</span>";
        $('#score_status_'+thisSId+'_'+fixture_id).html(score_status);

    }else if (thisSId == 48242) { // 농구
        //스코어
        periodsName = '4 쿼터'
        let qh_p1_score = qh_p2_score = 0;
        if (event['Periods'].length > 0) {
            event['Periods'].forEach(function (item) {
                if(item['Type'] == 1){
                    $('.q1_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q1_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                    qh_p1_score += Number(item['Results'][0]['Value']);
                    qh_p2_score += Number(item['Results'][1]['Value']);
                }else if(item['Type'] == 2){
                    $('.q2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                    qh_p1_score += Number(item['Results'][0]['Value']);
                    qh_p2_score += Number(item['Results'][1]['Value']);
                }else if(item['Type'] == 3){
                    $('.q3_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q3_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 4){
                    $('.q4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 10){
                    $('.q2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 20){
                    $('.q4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 40){
                    $('.q5_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q5_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                    periodsName = '연장';
                }
            });
            
            $('.qh_p1_score_'+fixture_id).text(qh_p1_score);
            $('.qh_p2_score_'+fixture_id).text(qh_p2_score);
        }

        // 경기정보 좌측 진행상태, 시간
        minute = Math.floor(mainGame['live_time']/60);
        second = Math.floor(mainGame['live_time']%60);
        if(minute < 10) minute = '0'+minute;
        if(second < 10) second = '0'+second;
        $('#live_1_'+fixture_id).text(mainGame['live_current_period_display']);
        $('#live_1_time_'+fixture_id).text(minute  + ":"+ second);

        // 합계점수를 출력한다.
        $('.q6_p1_score_'+fixture_id).text(event['Scoreboard']['Results'][0]['Value']);
        $('.q6_p2_score_'+fixture_id).text(event['Scoreboard']['Results'][1]['Value']);

        // 하단 합계점수를 출력한다.
        let display_score = event['Scoreboard']['Results'][0]['Value'] + ' : ' + event['Scoreboard']['Results'][1]['Value'];
        $('.display_score_'+fixture_id).text(display_score);

        // 경기 상태
        let displayMintue = Math.floor(Number(mainGame['live_time']/60));
        let displaySecond = Number(mainGame['live_time']%60);
        if(displayMintue < 10) displayMintue = '0' + displayMintue;
        if(displaySecond < 10) displaySecond = '0' + displaySecond;

        let score_status =
            "<p class=\"soccer_league_name\">"
            + mainGame['fixture_league_name']+"</p>"
            + mainGame['live_current_period_display'] +" <span class=\"etc_time\"> "
            + displayMintue +":" + displaySecond;
            + "</span>";
        $('#score_status_'+thisSId+'_'+fixture_id).html(score_status);


    } else if (thisSId == 35232) { // 아이스하키
        //스코어
        periodsName = '4 쿼터'
        if (event['Periods'].length > 0) {
            event['Periods'].forEach(function (item) {
                if(item['Type'] == 1){
                    $('.q1_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q1_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 2){
                    $('.q2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 3){
                    $('.q3_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q3_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 4){
                    $('.q4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 10){
                    $('.q2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 20){
                    $('.q4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 40){
                    $('.q5_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q5_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                    periodsName = '연장';
                }
            });
        }

        // 경기정보 좌측 진행상태, 시간
        minute = Math.floor(mainGame['live_time']/60);
        second = Math.floor(mainGame['live_time']%60);
        if(minute < 10) minute = '0'+minute;
        if(second < 10) second = '0'+second;
        $('#live_1_'+fixture_id).text(mainGame['live_current_period_display']);
        $('#live_1_time_'+fixture_id).text(minute  + ":"+ second);

        // 합계점수를 출력한다.
        $('.q6_p1_score_'+fixture_id).text(event['Scoreboard']['Results'][0]['Value']);
        $('.q6_p2_score_'+fixture_id).text(event['Scoreboard']['Results'][1]['Value']);

        // 하단 합계점수를 출력한다.
        let display_score = event['Scoreboard']['Results'][0]['Value'] + ' : ' + event['Scoreboard']['Results'][1]['Value'];
        $('.display_score_'+fixture_id).text(display_score);

        // 경기 상태
        let displayMintue = Math.floor(Number(mainGame['live_time']/60));
        let displaySecond = Number(mainGame['live_time']%60);
        if(displayMintue < 10) displayMintue = '0' + displayMintue;
        if(displaySecond < 10) displaySecond = '0' + displaySecond;

        let score_status =
            "<p class=\"soccer_league_name\">"
            + mainGame['fixture_league_name']+"</p>"
            + mainGame['live_current_period_display'] +" <span class=\"etc_time\"> "
            + displayMintue +":" + displaySecond;
            + "</span>";
        $('#score_status_'+thisSId+'_'+fixture_id).html(score_status);


    } else if (thisSId == 687890) { // E게임
        if(event['Periods'].length == 10){
            periosCount = 9;
        }else{
            periosCount = event['Periods'].length;
        }

        //스코어
        periodsName = '5 SET'
        if (event['Periods'].length > 0) {
            event['Periods'].forEach(function (item) {
                if(item['Type'] == 1){
                    $('.q1_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q1_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 2){
                    $('.q2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 3){
                    $('.q3_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q3_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 4){
                    $('.q4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 10){
                    $('.q2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 20){
                    $('.q4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 40){
                    $('.q5_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.q5_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                    periodsName = '연장';
                }
            });
        }

        // 경기정보 좌측 진행상태, 시간
        // minute = Math.floor(mainGame['live_time']/60);
        // second = Math.floor(mainGame['live_time']%60);
        // if(minute < 10) minute = '0'+minute;
        // if(second < 10) second = '0'+second;
        // $('#live_1_'+fixture_id).text(mainGame['live_current_period_display']);
        // $('#live_1_time_'+fixture_id).text(minute  + ":"+ second);

        // 합계점수를 출력한다.
        $('.q6_p1_score_'+fixture_id).text(event['Scoreboard']['Results'][0]['Value']);
        $('.q6_p2_score_'+fixture_id).text(event['Scoreboard']['Results'][1]['Value']);

        // 하단 합계점수를 출력한다.
        let display_score = event['Scoreboard']['Results'][0]['Value'] + ' : ' + event['Scoreboard']['Results'][1]['Value'];
        $('.display_score_'+fixture_id).text(display_score);

        // 경기 상태
        // let displayMintue = Math.floor(Number(mainGame['live_time']/60));
        // let displaySecond = Number(mainGame['live_time']%60);
        // if(displayMintue < 10) displayMintue = '0' + displayMintue;
        // if(displaySecond < 10) displaySecond = '0' + displaySecond;

        // let score_status =
        //     "<p class=\"soccer_league_name\">"
        //     + mainGame['fixture_league_name']+"</p>"
        //     + mainGame['live_current_period_display'] +" <span class=\"etc_time\"> "
        //     + displayMintue +":" + displaySecond;
        //     + "</span>";
        let score_status =
            //event['Scoreboard']['CurrentPeriod']
            "<p class=\"soccer_league_name\">"
            + mainGame['fixture_league_name']+"</p>"
            + periosCount +" SET <span class=\"etc_time\"> "
            + "</span>";
        $('#score_status_'+thisSId+'_'+fixture_id).html(score_status);


    }else if (thisSId == 154830 ) { // 배구
        //스코어
        if (event['Periods'].length > 0) {
            event['Periods'].forEach(function (item) {
                if(item['Type'] == 1){
                    $('.v1_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.v1_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 2){
                    $('.v2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.v2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 3){
                    $('.v3_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.v3_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 4){
                    $('.v4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.v4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 5){
                    $('.v5_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.v5_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }
            });
        }

        // 경기정보 좌측 진행상태
        $('#live_1_'+fixture_id).text(mainGame['live_current_period_display']);

        // 경기 상태
        let score_status =
            //event['Scoreboard']['CurrentPeriod']
            "<p class=\"soccer_league_name\">"
            + mainGame['fixture_league_name']+"</p>"
            + event['Periods'].length +"세트  <span class=\"etc_time\"> "
            + "</span>";
        $('#score_status_'+thisSId+'_'+fixture_id).html(score_status);

        // 스코어 (반칙 정보..)
        /*let html = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\"></table>";
        $('.soccer_table').html(html);*/
    } else if (thisSId == 154914 ) { // 야구
        //스코어
        //periodsName = '4 쿼터'

        if(event['Periods'].length == 10){
            periosCount = 9;
        }else{
            periosCount = event['Periods'].length;
        }

        // 경기정보 좌측 진행상태
        $('#live_1_'+fixture_id).text(mainGame['live_current_period_display']);

        if (event['Periods'].length > 0) {
            event['Periods'].forEach(function (item) {
                if(item['Type'] == 1){
                    $('.i1_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i1_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 2){
                    $('.i2_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i2_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 3){
                    $('.i3_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i3_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 4){
                    $('.i4_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i4_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 5){
                    $('.i5_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i5_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 6){
                    $('.i6_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i6_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 7){
                    $('.i7_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i7_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 8){
                    $('.i8_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i8_p2_score_'+fixture_id).text(item['Results'][1]['Value']);
                }else if(item['Type'] == 9){
                    $('.i9_p1_score_'+fixture_id).text(item['Results'][0]['Value']);
                    $('.i9_p2_score-'+fixture_id).text(item['Results'][1]['Value']);
                    //periodsName = '연장';
                }
            });
        }

        // 합계점수를 출력한다.
        $('.i99_p1_score_'+fixture_id).text(event['Scoreboard']['Results'][0]['Value']);
        $('.i99_p2_score_'+fixture_id).text(event['Scoreboard']['Results'][1]['Value']);

        // 하단 합계점수를 출력한다.
        let display_score = event['Scoreboard']['Results'][0]['Value'] + ' : ' + event['Scoreboard']['Results'][1]['Value'];
        $('.display_score_'+fixture_id).text(display_score);

        // 경기 상태
        let score_status =
            //event['Scoreboard']['CurrentPeriod']
            "<p class=\"soccer_league_name\">"
            + mainGame['fixture_league_name']+"</p>"
            + periosCount +"회 <span class=\"etc_time\"> "
            + "</span>";
        $('#score_status_'+thisSId+'_'+fixture_id).html(score_status);
    }
}

const onDisplayBet = function(market_id){
    //console.log(market_id);
    //console.log($('#market_'+market_id).css('display'));
    if($('#market_'+market_id).css('display') == 'block')
        $('#market_'+market_id).slideUp();
    else
        $('#market_'+market_id).slideDown();
}

const fnCheckCombine = function ($this, betList, bet1, bet2) {
    let count1 = 0;
    let count2 = 0;
    let count3 = 0;
    
    let findObject = null; 
    if(isMobile){
        findObject = $this.closest('ul');
    }else{
        findObject = $('.dropdown3');
    }
        
    //$this.closest('ul').find('.odds_btn.bet_on').each(function (index, item) {
    //$('.dropdown3').find('.odds_btn.bet_on').each(function (index, item) {
    findObject.find('.odds_btn.bet_on').each(function (index, item) {
        //console.log('fnCheckCombine : '+item);
        const betId = $(item).data('index').split('_')[1];
        //console.log('fnCheckCombine index : '+$(item).data('index'));
        if ($(this).closest('li').children('p').first().css('display') == 'none')
            return false;
        if (betId == bet1) {
            count1 += 1;
        } else if (betId == bet2) {
            count2 += 1;
        } else {
            count3 += 1;
        }
    });

    if (count1 == 0 && count2 == 1 && count3 == 0) {
        return true;
    } else {
        return false;
    }
};

const onDisplayFixture = function(fixture_id){
    // 기존에 열린것 클릭시 닫을 필요가 없다.
    if(selectFixtureId == fixture_id){
        return;
    }
    
    // 기존에 열린것 닫기
    $('#display_fixture_'+selectFixtureId).slideUp();
    //$('#display_fixture_'+selectFixtureId).removeClass('fixture_open');
    // $(document).find(`.live_game_display_wrap`).find(".sports_s_right").hide();
    // $(document).find(`.live_game_display_wrap[data-fixturekey='${fixture_id}']`).find(".sports_s_right").show();

    // $(document).find(".sports_s_right").removeClass("active");
    // $(document).find(`.live_game_display_wrap[data-fixturekey='${fixture_id}']`).find(".sports_s_right").addClass("active");

    $(document).find(".live_game_display_wrap").removeClass("active");
    $(document).find(`.live_game_display_wrap[data-fixturekey='${fixture_id}']`).addClass("active");

    
    $(document).find(`.live_game_display_wrap .sports_s_right`).removeClass("active");
    $(document).find(`.live_game_display_wrap[data-fixturekey='${fixture_id}'] .sports_s_right`).addClass("active");
    
    $(document).find(`.dropdown2 .sports_s_right`).hide();
    $(document).find(`li[data-fixturekey="${fixture_id}"] .sports_s_right`).show();



    //console.log('onDisplayFixture : '+fixture_id);
    //console.log($('#market_'+market_id).css('display'));
    if($('#display_fixture_'+fixture_id).css('display') == 'block'){
        $('#display_fixture_'+fixture_id).slideUp();
        //$('#display_fixture_'+fixture_id).removeClass('fixture_open');
        selectFixtureDisplay = 0;
    }else{
        $('#display_fixture_'+fixture_id).slideDown();
        //$('#display_fixture_'+fixture_id).addClass('fixture_open');
        selectFixtureDisplay = 1;
    }

    //선택한 경기 셋팅
    let bfFixtureId = selectFixtureId;
    selectFixtureId = fixture_id;
    
    if(!isMobile){
        //console.log("bfFixtureId : "+bfFixtureId);
        //console.log("selectFixtureId : "+selectFixtureId);
        // 선택한 경기배당을 불러오지 않았으면 불러온다.
        if($(".fixture_"+selectFixtureId).length == 0){
            //console.log('not change');
            $(".fixture_"+bfFixtureId).slideUp();
            openBetData(fixture_id);
        }else{
            // 보여주는 경기가 변경되었다.
            if(bfFixtureId !== selectFixtureId){
                //console.log('change fixture');
                $(".fixture_"+bfFixtureId).slideUp();
                $(".fixture_"+selectFixtureId).slideDown();
            }
        }
    }
    contentHeight();
}

// 실시간 상단 종목선택
function sports_select_real(sportsId){
    getRealTimeGameLiveScoreList(sportsId, 0);
}

// ready
// 베팅슬립에서 X버튼 눌렀을때
const notifyCloseBtn = function(obj){
    let slip = $(obj).closest('.slip_bet_ing');
    let index = slip.data('index');

    totalOdds = $('.total_odds').data("total_odds");
    totalOdds = totalOdds / slip.find('.slip_bet_cell_r').html();
    totalOdds = totalOdds.toFixed(2);
    $('.total_odds').html(totalOdds);

    if ($('[data-index="' + index + '"]').hasClass('bet_on')) {
        $('[data-index="' + index + '"]').removeClass('bet_on');
    }
    slip.remove();

    let betSlipCount = getBetSlipCount();
    if (betSlipCount == 0) {
        $('.total_odds').text(0);
        $('.bonus_total_odds').text(0);
    }

    // 다폴더이고 베팅제외를 했을때, 3개 밑으로 갈시 보너스 배당률 0
    if (folderType == 'D' && (betSlipCount < 5 && betSlipCount >= 3)) {
        $('.total_odds').html((totalOdds * odds_3_folder_bonus).toFixed(2));
        $('.bonus_total_odds').html(odds_3_folder_bonus);
    } else if (betSlipCount < 3) {
        $('.bonus_total_odds').html(0);
    }

    changeWillWinMoney();
    betting_impossible = false;

    if(isMobile){
        $('.cart_count2').text($('.slip_bet_ing').length);
    }

    cartCount();
}

// max 버튼
const maxWinning = function(myBetMoney){
    cur_totalOdds = parseFloat($('.total_odds').text());
   
    myWinAmnt = cur_totalOdds * myBetMoney;

    if(myWinAmnt > limitBetMoney){
        alert('최대당첨금액 제한 : 배팅할 수 없습니다');
        return true;
    }
}
const maxBtnClick = function(){
        // Maximum winn amount restriction
        let myHoldingMoney = Number($('.myHoldingMoney').text().replace(/,/gi, ""));
    let maxBetMoney = $('.max_bet_money').text();

    maxBetMoney = Number(maxBetMoney.replace(/,/gi,"")); //변경작업
    let nowMoney = $('.util_money').text();
    nowMoney = Number(nowMoney.replace(/,/gi,"")); //변경작업
    nowMoney = +nowMoney;
    let betSlip = $('.slip_bet_ing');
    let totalMax = Infinity;
    if (!betSlip || !betSlip[0]) {
        alert('경기를 선택해주세요.');
        return;
    }
    totalMax = +betSlip.data('leagues_m_bet_money');

    let maxBet = $('.slip_bet_ing').data('data-leagues_m_bet_money');

    if ($('#betting_slip_money').val() > maxBetMoney) {
        alert(`더이상 배팅할 수 없습니다`);
        return;
    }
    if(myHoldingMoney > 0){
        if(maxBetMoney < myHoldingMoney){
            $('#betting_slip_money').val(setComma(maxBetMoney));

        }else{
            $('#betting_slip_money').val(setComma(myHoldingMoney));

        }
    }


    changeWillWinMoney();
    // auto_calc(limitBetMoney);
}

// 배팅하기
const bettingClick = function(){
    //getRealTimeGameLiveScoreList(0,0);
    //return;
    if ($('.slip_bet_ing').length <= 0) {
        alert('베팅을 선택해주세요.');
        return;
    }

    // 배팅불가능 체크(베팅슬립 하나 빠졌을시)
    /*if(true == betting_impossible){
        alert('배당변동으로 재베팅 해주시길 바랍니다.');
        return;
    }*/

    let betMoney = $('#betting_slip_money').val();

    betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
    //
    // 금액 입력여부
    if (betMoney <= 0) {
        alert('베팅금액이 0원입니다.');
        return;
    }

    let mes = '선택하신 내용으로 베팅금액 : ' + betMoney + '원\n 베팅진행하시겠습니까?'
    if(confirm(mes) == false){
        return;
    }

    let fixture_sport_id = 0;
    let live_score_1 = 0;
    let live_score_2 = 0;
    let livescore = '';
    let bet_data = '';
    let fixture_id = 0;
    let betId = 0;
    let arrBetSlip = [];
    let arrFixtures = [];

    $('.slip_bet_ing').each(function (index, item) {
        bet_data = betList_new.get($(item).data('index'));
        console.log($(item).data('index'));
        console.log(betList_new);
        fixture_sport_id = bet_data['fixture_sport_id'];
        
        if(687890 == fixture_sport_id && (bet_data['livescore'] == '' || bet_data['livescore'] == null)){
            live_score_1 = live_score_2 = 0;
        }else{
            livescore = JSON.parse(bet_data['livescore']);
            live_score_1 = livescore['Scoreboard']['Results'][0]['Value'];
            live_score_2 = livescore['Scoreboard']['Results'][1]['Value'];
        }
        fixture_id = bet_data['fixture_id'];
        betId = $(item).data('bet-id');
        let obj = {
            'fixture_sport_id':fixture_sport_id,
            'fixture_id':fixture_id,
            'betId':betId,
            'live_score_1':live_score_1,
            'live_score_2':live_score_2
            //'live_score_1':livescore['Scoreboard']['Results'][0]['Value'],
            //'live_score_2':livescore['Scoreboard']['Results'][1]['Value']
        };
        arrBetSlip.push(obj);
        arrFixtures.push(fixture_id);
    });
    $('#loadingCircle').show();
    // $('body').append("<div class='loading-overlay'></div>");
    // 선택한 실시간 점수 저장
    $.ajax({
        url: '/api/setRealTimeScore',
        type: 'post',
        data: {
            'fixture_id': arrFixtures.join(',')
            //'betList' : JSON.parse(arrBetSlip)
        },
    }).done(function (response) {
            $('#bettingLoadingCircle').hide();

    }).fail(function (error) {
        console.log(error);
        $('#loadingCircle').hide();

        alert(error.responseJSON['messages']['messages']);
        return;

    }).always(function (response) {
    });

    //live_score_1 = livescore['Scoreboard']['Results'][0]['Value'];
    //live_score_2 = livescore['Scoreboard']['Results'][1]['Value'];

    // 베팅지연시간
    // 축구(6046), 아이스하키(35232), 농구(48242), 미식축구(131506), 배구(154830), 야구(154914)
    let time = 0;
    if(6046 == fixture_sport_id){
        live_score_1 = Number($('#participants_1_score_'+fixture_id).text());
        live_score_2 = Number($('#participants_2_score_'+fixture_id).text());
        time = betDelayTime['6046'];
    }else if(35232 == fixture_sport_id){
        live_score_1 = Number($('.q6_p1_score_'+fixture_id).text());
        live_score_2 = Number($('.q6_p2_score_'+fixture_id).text());
        time = betDelayTime['35232'];
    }else if(48242 == fixture_sport_id){
        live_score_1 = Number($('.q6_p1_score_'+fixture_id).text());
        live_score_2 = Number($('.q6_p2_score_'+fixture_id).text());
        time = betDelayTime['48242'];
    }else if(131506 == fixture_sport_id){
        time = betDelayTime['131506'];
    }else if(687890 == fixture_sport_id){
        time = betDelayTime['687890'];
    }else if(154830 == fixture_sport_id){
        let score_tag_1 = '';
        let score_tag_2 = '';
        let score_1 = 0;
        let score_2 = 0;
        for (let i=1; i <= 5; ++i){
            score_tag_1 = 'v'+i+'_p1_score_'+fixture_id;
            score_tag_2 = 'v'+i+'_p2_score_'+fixture_id;
            score_1 = $('.'+score_tag_1).text();
            score_2 = $('.'+score_tag_2).text();
            if(score_1 == '-'){
                live_score_1 += 0;
            }else{
                live_score_1 += Number(score_1);
            }

            if(score_2 == '-'){
                live_score_2 += 0;
            }else{
                live_score_2 += Number(score_2);
            }
        }

        time = betDelayTime['154830'];
    }else{
        live_score_1 = Number($('.i99_p1_score_'+fixture_id).text());
        live_score_2 = Number($('.i99_p2_score_'+fixture_id).text());
        time = betDelayTime['154914'];
    }
    //time = 0;

    //setTimeout("waitRealBet('"+live_score_1+"', '"+live_score_2+"')", time);
    setTimeout("waitRealBet('"+live_score_1+"', '"+live_score_2+"','"+JSON.stringify(arrBetSlip)+"')", time);

    // 카트닫기
    $('.cart_close').trigger('click');
    $('#bettingLoadingCircle').show();
}

const bettingSlipMoneyFocus = function(){
    $('#loadingCircle').hide();
    let userInputBetBefore = $('#betting_slip_money').val();
    sessionStorage.setItem('userInputBet', userInputBetBefore);
}

// focus 사라짐
const bettingSlipMoneyBlur = function(){
    $('#loadingCircle').hide();
    let userInputBetBefore = sessionStorage.getItem('userInputBet');
    let userInputBetAfter = $('#betting_slip_money').val();
    userInputBetAfter = Number(userInputBetAfter.replace(/,/gi,""));


    if (Number.isNaN(userInputBetAfter)) {
        $('#betting_slip_money').val(userInputBetBefore);
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        alert(`숫자형태로 입력해주세요`);
        return;
    }

    let maxBetMoney = $('.max_bet_money').text();
    maxBetMoney = Number(maxBetMoney.replace(/,/gi,"")); //변경작업
    let nowMoney = $('.util_money').text();
    nowMoney = Number(nowMoney.replace(/,/gi,"")); //변경작업
    nowMoney = +nowMoney;
    let betSlip = $('.slip_bet_ing');
    let totalMax = Infinity;
    if (!betSlip || !betSlip[0]) {
        alert('경기를 선택해주세요.');
        return;
    }
    totalMax = +betSlip.data('leagues_m_bet_money');

    if ($('#betting_slip_money').val() > maxBetMoney) {
        $('#betting_slip_money').val(userInputBetBefore);
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        alert(`최대배팅금 이상 배팅할 수 없습니다`);
        return;
    }

    if (nowMoney === 0) {
        alert(`보유머니가 부족합니다`);
        return;
    } else if (maxBetMoney > nowMoney) {
        if(nowMoney < +userInputBetAfter) {
            $('#betting_slip_money').val(setComma(nowMoney));
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            return;
        } else {
            $('#betting_slip_money').val(setComma(userInputBetAfter));
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            return;
        }
    } 
    if (maxBetMoney > totalMax) {
        if (totalMax > nowMoney) {
            if(nowMoney < +userInputBetAfter) {
                $('#betting_slip_money').val(setComma(nowMoney));
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                return;
            } else {
                $('#betting_slip_money').val(setComma(userInputBetAfter));
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                return;
            }
        } else {
            $('#betting_slip_money').val(userInputBetBefore);
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            alert(`최대베팅금 이하로 입력해주세요`);
            return;
        } 
    }

    let max_flag = auto_calc(limitBetMoney);
    if(max_flag === true) {
        $('#betting_slip_money').val(userInputBetBefore);
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        alert('최대당첨금액 제한 : 배팅할 수 없습니다');
        return;
    }

    $('#betting_slip_money').val(setComma(userInputBetAfter));
    changeWillWinMoney();
    sessionStorage.removeItem('userInputBet');
    return;
}

// 전체삭제
const wasteBtn = function(className){
    if ($('.'+className).length <= 0) {
        return;
    }

    let mes = '전체베팅 삭제하시겠습니까?'
    if(confirm(mes) == false){
        return;
    }

    initForm();
    cartCount();
}