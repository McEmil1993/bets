/*
 * sports 공통
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
    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let betSlipCount = getBetSlipCount();

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
    
    for (let slip of betSlip) {
        totalOdds *= slip.betPrice;
    };
    let odds = +totalOdds === 0 ? 1 : +totalOdds;
    odds = +odds.toFixed(2);
    totalOdds = +totalOdds * bonus_odds;
    totalOdds = totalOdds.toFixed(2);

    willMoney = +totalOdds * +data;
    willMoney = Math.ceil(willMoney);
    $('.total_odds').text(setComma(totalOdds));
    bonus_odds === 1 ? $('.bonus_total_odds').text(0) : $('.bonus_total_odds').text(bonus_odds);
    $('.will_win_money').html(setComma(willMoney));
}

function betMaxCheck() {
    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let leagues_m_bet_money = Infinity;
    if (!betSlip || !betSlip[0]) {
        $('.max_bet_money').text(setComma(maxBetMoney));
        return;
    }

    for (let slip of betSlip) {
        if (+leagues_m_bet_money >= +slip.leagues_m_bet_money) {
            leagues_m_bet_money = +slip.leagues_m_bet_money;
        };
    }
    if(+leagues_m_bet_money < +maxBetMoney) {
        $('.max_bet_money').text(setComma(+leagues_m_bet_money));
    } else {
        $('.max_bet_money').text(setComma(+maxBetMoney));
    };
    return;
}

// 마켓별로 열고 닫기
const marketClick = function(market_id){
    //console.log(market_id);
    //console.log($('#market_'+market_id).css('display'));
    if($('#market_'+market_id).css('display') == 'block')
        $('#market_'+market_id).slideUp();
    else
        $('#market_'+market_id).slideDown();
}

function leftSportsItemClick(sportsId){
    let moveUrl = location.pathname;
    if(sportsId)
        moveUrl +=  '?' + 'sports_id=' + sportsId;

    fnLoadingMove(moveUrl);
}

function leftLeagueItemClick(sportsId, leagueId){
    const moveUrl = location.pathname + '?' + 'sports_id=' + sportsId + '&' + 'league_id=' + leagueId;
    //if(leagueId)
    //    moveUrl += '&' + 'league_id=' + leagueId;
    fnLoadingMove(moveUrl);
}

function sports_select(sportsId){
    let moveUrl = location.pathname;
    if(sportsId > 0)
        moveUrl +=  '?' + 'sports_id=' + sportsId;
    fnLoadingMove(moveUrl);
}

function league_select(leagueId){
    let moveUrl = location.pathname;

    if(leagueId > 0)
        moveUrl +=  '?' + 'league_id=' + leagueId;
    fnLoadingMove(moveUrl);
}

function set_calc() {
    let calc = $('#betting_slip_money').val();
    calc = +calc.replace(/,/gi,""); //변경작업
    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let totalOdds = 1;
    for (let slip of betSlip) {
        totalOdds *= slip.betPrice;
    };
    let bonus_odds = $('.bonus_total_odds').text();
    bonus_odds = +bonus_odds.replace(/,/gi,""); //변경작업
    bonus_odds = bonus_odds;
    if (!bonus_odds) {
        bonus_odds = 1;
    }
    totalOdds = +totalOdds * bonus_odds;
    totalOdds = Math.round(totalOdds*100)/100;
    let will_win = calc * totalOdds;
    //console.log(calc, totalOdds, will_win);
    $('.will_win_money').html(setComma(parseInt(will_win)));
}

function auto_calc(max = 10000000) {
    max = +max;
    let will_win = $('.will_win_money').text();
        will_win = Number(will_win.replace(/,/gi,""));
        if (will_win > max) {
            let calc = calcMaxMoney(max);
            // console.log(`calc : ` + calc);
            if (+calc !== 0 ) {
                $('#betting_slip_money').val(setComma(""+calc));
                set_calc();
            };
            return true;
    }
    return false;
}

function calcMaxMoney(max_bet_money) {
    let inputBetMoney = $('#betting_slip_money').val();
    let data = inputBetMoney.replace(/,/gi,""); //변경작업
    let will_win_money = $('.will_win_money').val();

    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));

    if (!betSlip || !betSlip[0]) {
        betSlip = [];
    }
    let totalOdds = 1;
    for (let slip of betSlip) {
        totalOdds *= slip.betPrice;
    };
    let bonus_odds = $('.bonus_total_odds').text();
    will_win_money = +will_win_money.replace(/,/gi,"");
    // totalOdds = +totalOdds.replace(/,/gi,""); 
    bonus_odds = +bonus_odds.replace(/,/gi,"");

    let willMoney = 0;
    let odds = totalOdds === 0 ? 1 : totalOdds;
    if (bonus_odds === 0) {
        bonus_odds = 1;
    }
    totalOdds = +totalOdds * +bonus_odds;
    totalOdds = Math.round(totalOdds*100)/100;
    willMoney = +totalOdds * data;
    willMoney = Math.ceil(willMoney);
    max_bet_money = +max_bet_money;
    if (max_bet_money < willMoney) {
        let ret = +max_bet_money / +totalOdds;
        return parseInt(ret) - 1;
    } else {
        let ret = +willMoney / +totalOdds;
        return parseInt(ret) - 1;
    }
}

// 배팅 초기화
function initForm() {
    totalOdds = 0;
    $('.sports_cart_bet').remove();
    $('.bet_on').removeClass('bet_on');
    $('.total_odds').html(totalOdds);
    $('.bonus_total_odds').html(0);
    $('#betting_slip_money').val(0);
    $('.will_win_money').html(0);
    if(isMobile){
        $('.cart_count2').text($('.sports_cart_bet').length);
    }
    changeWillWinMoney();
}

// 금액 버튼
function setBettingMoney(money, userMoney) {
    userMoney = userMoney ? userMoney : Nummber(format_remove($('.util_money').text()));
    // userMoney = $('.util_money').text();
    userMoney = userMoney.replace(/,/gi,"");
    if (money > userMoney) {
        alert('보유머니가 부족합니다.');
        return;
    }

    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let totalMax = 0;

    if(!betSlip || !betSlip[0]) {
        alert(`경기를 선택해주세요.`);
        return;
    }
    for (let slip of betSlip) {
        if (totalMax <= +slip.leagues_m_bet_money) {
            totalMax = slip.leagues_m_bet_money;
        }
    }

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
    let max_flag = auto_calc(limitBetMoney);
    if(max_flag === true) {
        alert('최대당첨금액 제한 : 더이상 배팅할 수 없습니다');
        return;
    }
}

// 베팅슬립 추가
function addBetSlip(betId, betPrice, betListIndex, betOddsTypes, betOddsTypesDisplay, betMarketType, team1, team2, betBaseLine, fixtureId, fixture_start_date, leagues_m_bet_money, betMarketId) {
    let sessionData = sessionStorage.getItem("betSlip");
    let obj = {};
    if(null == sessionData){
        sessionData = [];
        obj['betId'] = betId;
        obj['betPrice'] = betPrice;
        obj['betListIndex'] = betListIndex;
        obj['betOddsTypes'] = betOddsTypes;
        obj['betOddsTypesDisplay'] = betOddsTypesDisplay;
        obj['betMarketType'] = betMarketType;
        obj['team1'] = team1;
        obj['team2'] = team2;
        obj['betBaseLine'] = betBaseLine;
        obj['fixtureId'] = fixtureId;
        obj['fixture_start_date'] = fixture_start_date;
        obj['leagues_m_bet_money'] = leagues_m_bet_money;
        obj['betMarketId'] = betMarketId;
        sessionData.push(obj);
        // console.log(sessionData);
    }else{
        //console.log(sessionData);
        sessionData = JSON.parse(sessionData);
        obj['betId'] = betId;
        obj['betPrice'] = betPrice;
        obj['betListIndex'] = betListIndex;
        obj['betOddsTypes'] = betOddsTypes;
        obj['betOddsTypesDisplay'] = betOddsTypesDisplay;
        obj['betMarketType'] = betMarketType;
        obj['team1'] = team1;
        obj['team2'] = team2;
        obj['betBaseLine'] = betBaseLine;
        obj['fixtureId'] = fixtureId;
        obj['fixture_start_date'] = fixture_start_date;
        obj['leagues_m_bet_money'] = leagues_m_bet_money;
        obj['betMarketId'] = betMarketId;
        sessionData.push(obj);
        // console.log(sessionData);
    }
    sessionStorage.setItem('betSlip', JSON.stringify(sessionData));
    // console.log(sessionStorage.length);
    // console.log(sessionStorage.getItem('betSlip'));
}

// 베팅슬립 삭제
function delBetSlip(betId) {
    let sessionData = sessionStorage.getItem("betSlip");
    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        //console.log('delBetSlip : ' + sessionData);
        for(i=0; i<sessionData.length; ++i){
            if(sessionData[i]['betId'] == betId){
                //console.log('tt '+sessionData[i]['betId']);
                sessionData.splice(i, 1);
            }
        }

        sessionStorage.setItem('betSlip', JSON.stringify(sessionData));
    }
    //console.log(sessionData);
}

/* 모바일것
 function delBetSlip(betId) {
        let sessionData = sessionStorage.getItem("betSlip");
        if(null != sessionData){
            sessionData = JSON.parse(sessionData);
            // console.log('delBetSlip : ' + sessionData);
            let obj = {};
            let sessionData_new = [];
            for(i=0; i<sessionData.length; ++i){
                // console.log('---')
                // console.log(sessionData[i]['betId'])
                // console.log(betId)
                // console.log('---')
                if(sessionData[i]['betId'] != betId){
                    // console.log('tt '+ sessionData[i]['betId']);
                    obj = sessionData[i];
                    sessionData_new.push(obj);
                }
            }
            
            sessionStorage.setItem('betSlip', JSON.stringify(sessionData_new));
            // console.log(sessionStorage);
        }
    }
 */

function delBetSlipFixtureId(fixtureId) {
    let sessionData = sessionStorage.getItem("betSlip");
    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        //console.log('delBetSlip : ' + sessionData);
        for(i=0; i<sessionData.length; ++i){
            if(sessionData[i]['fixtureId'] == fixtureId){
                //console.log('tt '+sessionData[i]['fixtureId']);
                sessionData.splice(i, 1);
            }
        }

        sessionStorage.setItem('betSlip', JSON.stringify(sessionData));
    }
    //console.log(sessionData);
}

/* 모바일것
 function delBetSlipFixtureId(fixtureId) {
        let sessionData = sessionStorage.getItem("betSlip");
        if(null != sessionData){
            sessionData = JSON.parse(sessionData);
            let obj = {};
            let sessionData_new = [];
            //console.log('delBetSlip : ' + sessionData);
            for(i=0; i<sessionData.length; ++i){
                if(sessionData[i]['fixtureId'] != fixtureId){
                    // console.log('tt '+sessionData[i]['fixtureId']);
                    //sessionData.splice(i, 1);
                    obj = sessionData[i];
                    sessionData_new.push(obj);
                }
            }
            //sessionStorage.setItem('betSlip', JSON.stringify(sessionData));
            sessionStorage.setItem('betSlip', JSON.stringify(sessionData_new));
        }
        //console.log(sessionData);
    }
 */

// 베팅슬립 전체삭제
function delAllBetSlip() {
    sessionStorage.clear();
}

// 베팅슬립 로드
function displayBetSlip() {
    let sessionData = sessionStorage.getItem("betSlip");
    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        let totalBetPrice = 1;
        for(i=0; i<sessionData.length; ++i){
            //console.log(sessionData);
            let betListIndex = sessionData[i]['betListIndex'];
            let betOddsTypes = sessionData[i]['betOddsTypes'];
            let betId = sessionData[i]['betId'];
            let betMarketType = sessionData[i]['betMarketType'];
            let betOddsTypesDisplay = sessionData[i]['betOddsTypesDisplay'];
            let betPrice = sessionData[i]['betPrice'];
            let team1 = sessionData[i]['team1'];
            let team2 = sessionData[i]['team2'];
            let betBaseLine = sessionData[i]['betBaseLine'];
            let fixture_start_date = sessionData[i]['fixture_start_date'];
            let leagues_m_bet_money = sessionData[i]['leagues_m_bet_money'];

            //console.log('displayBetSlip : ' + betListIndex);

            // 베팅슬립 출력
            let html = "<li class='sports_cart_bet' data-index="+betListIndex+" data-odds-types="+betOddsTypes+
                            " data-bet-id="+betId+" data-bet-price="+betPrice+" data-markets-name='"+betMarketType+
                            "' data-bet-base-line='"+betBaseLine+"' data-fixture-start-date='"+fixture_start_date+"' data-leagues_m_bet_money="+leagues_m_bet_money+">" +
                            "<div width='100%'class='cart_bet'>" +
                            "<div>" + 
                                "<td>"+team1+"<span class='sports_cart_bet_font1'> "+betOddsTypesDisplay+"</span></td>"+
                                "<td><a href='#' class='sports_cart_bet_img'><img src='/assets_w/images/cart_close.png'"+
                                "class='notify-close-btn' data-index="+betListIndex+" data-bet-id="+betId+"></a><span class='sports_cart_bet_p'>"+betPrice+"</span></td>"+
                            "</div>"+
                            "<div>"+
                                "<td colspan='2'><span class='sports_cart_bet_font2'>"+betMarketType+"</span></td>"+
                            "</div>"+
                            "<div>"+
                                "<td colspan='2'><span class='sports_cart_bet_font3'>"+team1+"<img src='/assets_w/images/vs.png' width='25'>"+team2+"</span></td>"+
                            "</div>"+
                        "</div>"+
                    "</li>";
                $('.slip_tab_wrap').prepend($(html));

                totalBetPrice *= betPrice;
                // 선택표시
                //$('[data-td-cell*="' + betId + '_' + fixture_start_date + '"]').addClass('bet_on');
                // $('[data-bet-id*="' + betId + '"]').addClass('bet_on');
                //$('.odds_btn[data-bet-id*="' + betId + '"]').addClass('bet_on');
        }
        totalOdds = totalBetPrice;
        $('.total_odds').html(totalBetPrice.toFixed(2));
        
        if(isMobile){
            $('.cart_count2').text(sessionData.length);
        }
    }
}

// 더보기 베팅선택 처리
function moreDisplaySelect() {
    let sessionData = sessionStorage.getItem("betSlip");
    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        for(i=0; i<sessionData.length; ++i){
            //console.log(sessionData);
            let betId = sessionData[i]['betId'];
            let fixture_start_date = sessionData[i]['fixture_start_date'];

            $('[data-td-cell*="' + betId + '_' + fixture_start_date + '"]').addClass('bet_on');
        }
    }
}

function IsBetSlip(betId) {
    let result = false;
    let sessionData = sessionStorage.getItem("betSlip");
    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        //console.log('delBetSlip : ' + sessionData);
        for(i=0; i<sessionData.length; ++i){
            if(sessionData[i]['betId'] == betId){
                result = true;
                break;
            }
        }
    }
    return result;
}

// 베팅한 경기인지 체크
function IsBetFixture(fixtureId) {
    let sessionData = sessionStorage.getItem("betSlip");

    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        for(i=0; i<sessionData.length; ++i){
            if(sessionData[i]['fixtureId'] == fixtureId){
                return true;
            }
        }
    }

    return false;
}

/*function betMaxCheck() {
    let maxBetMoney = `<?= number_format($maxBetMoney) ?>`;
    maxBetMoney = +maxBetMoney.replace(/,/gi,""); //변경작업
    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let leagues_m_bet_money = Infinity;
    if (!betSlip || !betSlip[0]) {
        $('.max_bet_money').text(setComma(maxBetMoney));
        return;
    }

    for (let slip of betSlip) {
        if (+leagues_m_bet_money >= +slip.leagues_m_bet_money) {
            leagues_m_bet_money = +slip.leagues_m_bet_money;
        };
    }
    if(+leagues_m_bet_money < +maxBetMoney) {
        $('.max_bet_money').text(setComma(+leagues_m_bet_money));
    } else {
        $('.max_bet_money').text(setComma(+maxBetMoney));
    };
    return;
}*/

const fnCheckCombine = function ($this, betList, bet1, bet2) {
    let count1 = 0;
    let count2 = 0;
    let count3 = 0;
    // $this.closest('div.soprts_in_acc').find('dl').not(':hidden').find('.odds_btn.bet_on').each(function (index, item) {
    $this.closest('ul').find('.odds_btn.bet_on').each(function (index, item) {
        //const gameObj = fnMakeGameObj($(item).data('index'));
        const betListIndex = $(item).data('index');
        const indexArr = betListIndex.split('_');
        const markets_id = indexArr[1];
        if ($(this).closest('li').children('p').first().css('display') == 'none')
            return false;
        if (markets_id == bet1) {
            count1 += 1;
        } else if (markets_id == bet2) {
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

const fnCheckOverlapCombine = function (row, betList, betMarketId) {

    if (342 == betMarketId) {
        if (fnCheckCombine(row, betList, 342, 28)) {
            return true;
        }
    } else if (226 == betMarketId) {
        if (fnCheckCombine(row, betList, 226, 28)) {
            return true;
        }
    } else { // 28
        if (fnCheckCombine(row, betList, 28, 342)) {
            return true;
        } else if (fnCheckCombine(row, betList, 28, 226)) {
            return true;
        }
    }
    return false;
};

const fnCheckCombine_renew = function ($this, betList, bet1, bet2, fixtureId) {
    let count1 = 0;
    let count2 = 0;
    let count3 = 0;
    let sessionData = sessionStorage.getItem("betSlip");

    if(null != sessionData){
        sessionData = JSON.parse(sessionData);
        for(i=0; i<sessionData.length; ++i){
            if(sessionData[i]['fixtureId'] == fixtureId){
                const markets_id = sessionData[i]['betMarketId'];
                //if ($(this).closest('li').children('p').first().css('display') == 'none')
                //    return false;
                if (markets_id == bet1) {
                    count1 += 1;
                } else if (markets_id == bet2) {
                    count2 += 1;
                } else {
                    count3 += 1;
                }
            }
        }
    }

    if (count1 == 0 && count2 == 1 && count3 == 0) {
        return true;
    } else {
        return false;
    }

};

const fnCheckOverlapCombine_renew = function (row, betList, betMarketId, fixtureId) {

    if (342 == betMarketId) {
        if (fnCheckCombine_renew(row, betList, 342, 28, fixtureId)) {
            return true;
        }
    } else if (226 == betMarketId) {
        if (fnCheckCombine_renew(row, betList, 226, 28, fixtureId)) {
            return true;
        }
    } else { // 28
        if (fnCheckCombine_renew(row, betList, 28, 342, fixtureId)) {
            return true;
        } else if (fnCheckCombine_renew(row, betList, 28, 226, fixtureId)) {
            return true;
        }
    }
    return false;
};

const fnMakeGameObj = function(betListIndex){
    const indexArr = betListIndex.split('_');
    const fixtureKey = indexArr[0]; 
    const marketsKey = indexArr[1]; 
    const baseLineKey = indexArr[2]; 
    const providersKey = indexArr[3]; 
    const startDateKey = indexArr[4]; 

    let gameObj = betList[startDateKey][fixtureKey][0];
    //let isBreak = false;

    /*for(const e in betList[startDateKey][fixtureKey]){
        if(isBreak)
            break;
        for (const order in betList[startDateKey][fixtureKey][e]){
            for (const o in betList[startDateKey][fixtureKey][e][order]){
                betList[startDateKey][fixtureKey][e][order][o].some(function(item){
                    if(item.markets_id == marketsKey && item.bet_base_line == baseLineKey && item.providers_id == providersKey){
                        gameObj = item;
                        isBreak = true;
                        return;
                    }
                });
            }
        }
    }*/

    return gameObj;
}

// 자물쇠 표기
function getLockHtml(w_size, menu, bet_status, display_data1, display_data2){
    let html = '';
    if(1 == menu || 2 == menu){
        if('w_30' == w_size){
            html = "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'><span class='sports_team_txt'>"+display_data1+"</span><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                    "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                    "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'><span class='sports_team_txt'>"+display_data2+"</span><img src='/images/icon_lock.png' alt='lock' width='13'></td>";
        }else{
            html = "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'><span class='sports_team_txt'>"+display_data1+"</span><span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                    "<td class='bet_list_td "+w_size+"'><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                    "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'><span class='sports_team_txt'>"+display_data2+"</span><span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
        }
    }else{
        html = "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'">+display_data1+
                "<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                "<td class='bet_list_td "+w_size+"' data-bet-status='"+ bet_status + "'>"+display_data2+
                "<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
    }
    return html;
}

// 베팅판 보여주기
function openBetData(fixture_id){
    //
    if(isMobile){
        // 이미 배당을 받아와서 디스플레이중이면 삭제(닫힘)처리
        if($("#fixture_"+fixture_id).find("li").length > 0){
            //console.log('fixture_ close : '+fixture_id);
            $("#fixture_"+fixture_id+" li").remove();
            $("#fixture_"+fixture_id).slideUp();
            return;
        }
        $("#fixture_"+fixture_id).slideDown();
    }

    $.ajax({
        url: '/web/sports/ajax_add_fixtures',
        type: 'post',
        //async : false,
        data: {
            'fixture_id': fixture_id
        },
    }).done(function (response) {
        //console.log(response);
        let arrMainMarket = new Array();
        let list = response['data']['gameList'];
        let gameSportsId = response['data']['gameSportsId'];
        let teamName = '';
        let fixtureTime = '';
        const today = new Date();
        for (const[key, fixture_list] of Object.entries(list)) {
            //let arrMenuKey = Object.keys(fixture_list);
            let html ='';
            let isMainBetLock = false;

            if(isMobile){
                html = "<li class='bet_list_title'><div class='bet_title_team' id= 'team_name_"+fixture_id+"'></div><div class='bet_title_time' id='fixture_time_"+fixture_id+"'></div></li>";
            }

            html += "<li class='bet_list1_wrap'>";
            for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
                // 배구, 야구는 메인만 존재
                /*if(sportsKey == 'game_154830' || sportsKey == 'game_154914' || sportsKey == 'game_687890' || sportsKey == 'game_154919'){
                    betCount += count(menu_list);
                }else if($memuKey > 0) $betCount += count($menu_list);*/

                // 갬블은 메인메뉴 개념이 없다.
                if(!isMobile){
                    // 배구, 야구, 이스포츠는 메인만 존재
                    /*if(gameSportsId == 154830 || gameSportsId == 154914 || gameSportsId == 687890){
                        if(memuKey > 0) continue;
                    }else{
                        if(memuKey > 0) continue;
                    }*/
                    if(memuKey > 0) continue;
                }else{
                    if(memuKey != 0) continue;
                }
                
                for (const[orderKey, order_list] of Object.entries(menu_list)) {
                    for (const[marketKey, game_list] of Object.entries(order_list)) {
                        arrMainMarket.push(marketKey);
                        firstKey = Object.keys(game_list)[0];
                        isMainMarket = 0;
                        /*style = 'display: none;';
                        if (memuKey == 0) {
                            isMainMarket = 1;
                            style = 'display: block;';
                        }*/

                        teamName = game_list[firstKey]['fixture_participants_1_name']+' VS '+game_list[firstKey]['fixture_participants_2_name'];
                        fixtureTime = (game_list[firstKey]['fixture_start_date']).replace(/-/g, "/");
                        fixtureTime = getFormatDateMonth(fixtureTime)+' '+game_list[firstKey]['start_date'];
                        if (game_list[firstKey]['markets_display_name'] == '' || game_list[firstKey]['markets_display_name'] == null){
                            marketsName = game_list[firstKey]['markets_name_origin'];
                        }else{
                            marketsName = game_list[firstKey]['markets_display_name'];
                        }

                        // 승무패 및 오버언더
                        if (marketKey == 427){
                            marketsName += '(' + game_list[firstKey]['bet_base_line'] + ')';
                        }

                        if (marketKey == 13){
                            marketsName += game_list[firstKey]['markets_name'];
                        }
                        $sportsLineColor = getSportsLineColor(gameSportsId);
                        setSportsBackGroundColor(gameSportsId, fixture_id);

                        // 데이터가 있으면 표기
                        if(Object.keys(game_list[firstKey]['bet_data']).length > 0){
                            // 마켓명 표기
                            if((marketKey != 1 && marketKey != 52 && marketKey != 226) || !isMobile){
                                html += "<div class='bet_list1_wrap_in_title "+$sportsLineColor+"'>"+marketsName+"<span class='bet_list1_wrap_in_title_right'></span></div>"+
                                        "<ul class='bet_list1_wrap_in_new' id='market_"+marketKey+" 'style='display:flex'>"+
                                        "<table width='100%' border='0' cellspacing='0' cellpadding='0' style=''>";
                            }
                        }
                        
                        for (const[gameKey, game] of Object.entries(game_list)) {
                            html += "<tr>";
                            if (0 == game['menu']) continue;
                            let bGameKey = game['fixture_id'] + '_' + game['markets_id'] + '_' + game['bet_base_line'] + '_' + game['providers_id']+'_'+game['fixture_start_date'];
                            let markets_name = game['markets_name'];
                            let markets_name_origin = game['markets_name_origin'];
                            let markets_display_name = game['markets_display_name'];
                            const timeValue = new Date(game['fixture_start_date']);
                            const betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);
                            const checkTime = 600;
                            
                            // 승무패
                            if (1 == game['menu']) {
                                game['win_bet_id'] = game['win'] = game['lose_bet_id'] = game['lose'] = game['draw_bet_id'] = game['draw'] = 0;
                                for (const[betKey, betData] of Object.entries(game['bet_data'])) {
                                    if ('1' == betData['bet_name']) {
                                        game['win_bet_id'] = betData['bet_id'];
                                        game['win'] = betData['bet_price'];
                                        game['bet_status'] = betData['bet_status'];
                                    } else if ('2' == betData['bet_name']) {
                                        game['lose_bet_id'] = betData['bet_id'];
                                        game['lose'] = betData['bet_price'];
                                        game['bet_status'] = betData['bet_status'];
                                    } else {
                                        game['draw_bet_id'] = betData['bet_id'];
                                        game['draw'] = betData['bet_price'];
                                        game['bet_status'] = betData['bet_status'];
                                    }
                                }

                                // 배당 표기
                                if (Object.keys(game['bet_data']).length == 3) {
                                    //console.log('bet_status : '+game['bet_status']);
                                    //console.log('betweenTime : '+betweenTime);
                                    //console.log('display_status : '+game['display_status']);
                                    if((1 == game['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                        if((marketKey != 1 && marketKey != 52 && marketKey != 226) || !isMobile){
                                            html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                    " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                                    " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                    " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                    "><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span> <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
                                            html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                    " data-odds-type='draw' data-bet-id='"+ game['draw_bet_id'] +"' data-bet-price='"+ game['draw'] +"'"+
                                                    " data-td-cell='"+game['draw_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                    " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                    ">무 <span class='betin_right bet_font1'>"+game['draw']+"</span></td>";
                                            html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                    " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                                    " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                    " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                    "><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span> <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
                                        }
                                    }else{
                                        isMainBetLock = true;
                                        if((marketKey != 1 && marketKey != 52 && marketKey != 226) || !isMobile){
                                            /*html += "<td class='bet_list_td w30' data-bet-status='"+ game['bet_status'] +"'><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                                                    "<td class='bet_list_td w30' data-bet-status='"+ game['bet_status'] +"'><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                                                    "<td class='bet_list_td w30' data-bet-status='"+ game['bet_status'] +"'><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span><img src='/images/icon_lock.png' alt='lock' width='13'></td>";*/
                                            html += getLockHtml('w30', game['menu'], game['bet_status'], game['fixture_participants_1_name'], game['fixture_participants_2_name']);
                                        }
                                    }
                                } else {
                                    if((1 == game['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                        if((marketKey != 1 && marketKey != 52 && marketKey != 226) || !isMobile){
                                            html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                    " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                                    " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                    " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                    "><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span> <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
                                            html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                    " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                                    " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                    " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                    "><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span> <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
                                        }
                                    }else{
                                        isMainBetLock = true;
                                        if((marketKey != 1 && marketKey != 52 && marketKey != 226) || !isMobile){
                                            /*html += "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                                    "><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span> <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                                    "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                                    "><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span> <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";*/
                                            html += getLockHtml('w50', game['menu'], game['bet_status'], game['fixture_participants_1_name'], game['fixture_participants_2_name']);
                                        }
                                    }
                                }
                                html += "</tr>";
                                // 핸디캡
                            } if (2 == game['menu']) {
                                if (1 == Object.keys(game['bet_data']).length){
                                    continue;
                                }
                                game['win_bet_id'] = game['win'] = game['lose_bet_id'] = game['lose'] = 0;
                                let handValue_l = handValue_r = 0;
                                for (const[betKey, betData] of Object.entries(game['bet_data'])) {
                                    if (betData['bet_name'] == 1) {
                                        game['win'] = betData['bet_price'];
                                        game['win_bet_id'] = betData['bet_id'];
                                        game['bet_status'] = betData['bet_status'];
                                        //let bet_line = explode(' ', betData['bet_line'])[0];
                                        let bet_line = betData['bet_line'].split(' ')[0];

                                        //if(isset(betData['bet_line'].split(' ')[1])){
                                        if(isset(betData['bet_line'].split(' ')[1])){
                                            let bet_line_second = betData['bet_line'].split(' ')[1];
                                            bet_line_second = bet_line_second.replace('(', '');
                                            bet_line_second = bet_line_second.replace(')', '');
                                            //bet_line_second = explode('-', bet_line_second);
                                            bet_line_second = bet_line_second.split('-');
                                            handValue_l = bet_line + bet_line_second[0] - bet_line_second[1];
                                        }else{
                                            handValue_l = bet_line;
                                        }

                                        if(handValue_l > 0){
                                            handValue_l = '+'+handValue_l;
                                        }else{
                                            handValue_l = handValue_l;
                                        }
                                    } else {
                                        game['lose'] = betData['bet_price'];
                                        game['lose_bet_id'] = betData['bet_id'];
                                        game['bet_status'] = betData['bet_status'];
                                        //let bet_line = explode(' ', betData['bet_line'])[0];
                                        let bet_line = betData['bet_line'].split(' ')[0];

                                        //if(isset(explode(' ', betData['bet_line'])[1])){
                                        if(isset(betData['bet_line'].split(' ')[1])){
                                            let bet_line_second = betData['bet_line'].split(' ')[1];
                                            bet_line_second = bet_line_second.replace('(', '');
                                            bet_line_second = bet_line_second.replace(')', '');
                                            bet_line_second = bet_line_second.split('-');
                                            handValue_r = bet_line + bet_line_second[0] - bet_line_second[1];
                                        }else{
                                            handValue_r = bet_line;
                                        }

                                        if(handValue_r > 0){
                                            //handValue_r = '+'.number_format(handValue_r,1);
                                            handValue_r = '+'+handValue_r;
                                        }else{
                                            //handValue_r = number_format(handValue_r,1);
                                            handValue_r = handValue_r;
                                        }
                                    }
                                }

                                // 배당 표기
                                if((1 == game['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                    console.log('isMainBetLock : '+isMainBetLock);
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='"+game['fixture_participants_1_name']+"("+ handValue_l +")' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                            " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['win']+"</span></td>";
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='"+game['fixture_participants_2_name']+"("+ handValue_r +")' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                            " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['lose']+"</span></td>";
                                }else{
                                    // 자물쇠 처리
                                    html += "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                            "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                    //html += getLockHtml('w50', game['menu'], game['bet_status'], "<span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ")", "<span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ")");
                                }
                                html += "</tr>";
                            // 언더오버
                            } else if (3 == game['menu']) {
                                if (1 == Object.keys(game['bet_data']).length){
                                    continue;
                                }

                                let over = over_bet_id = under = under_bet_id = 0;
                                let over_base_line = under_base_line = '';

                                for (const[betKey, betData] of Object.entries(game['bet_data'])) {
                                    if (betData['bet_name'] == 'Over') {
                                        over = betData['bet_price'];
                                        over_bet_id = betData['bet_id'];
                                        over_status = betData['bet_status'];
                                        //over_base_line = explode(' ', game['bet_base_line'])[0];
                                        over_base_line = game['bet_base_line'].split(' ')[0];
                                    } else {
                                        under = betData['bet_price'];
                                        under_bet_id = betData['bet_id'];
                                        under_status = betData['bet_status'];
                                        //under_base_line = explode(' ', game['bet_base_line'])[0];
                                        under_base_line = game['bet_base_line'].split(' ')[0];
                                    }
                                }

                                // 배당 표기
                                if((1 == over_status && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ over_status + "'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='오버("+ over_base_line +")' data-bet-id='"+ over_bet_id  +"' data-bet-price='"+ over +"'"+
                                            " data-td-cell='"+over_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr2.gif' style='margin-right: 5px'>"+over+"</span></td>";
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ under_status + "'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='언더("+ under_base_line +")' data-bet-id='"+ under_bet_id  +"' data-bet-price='"+ under +"'"+
                                            " data-td-cell='"+under_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr1.gif' style='margin-right: 5px'>"+under+"</span></td>";
                                }else{
                                    // 자물쇠 처리
                                    html += "<td class='bet_list_td w50' data-bet-status='"+ over_status + "'"+
                                            ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                            "<td class='bet_list_td w50' data-bet-status='"+ under_status + "'"+
                                            ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                    //html += getLockHtml('w50', game['menu'], game['bet_status'], "<span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ")", "<span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ")");
                                }
                                html += "</tr>";
                                // 기타
                            } else if (4 == game['menu']) {
                                html += "<tr>";
                                    //$tmBetName = array_keys($game['bet_data'])[0];
                                    let tmBetName = Object.keys(game['bet_data'])[0];
                                    let betData_yes = betData_no = null;
                                    let display_bet_name_yes = display_bet_name_no = 0;
                                    //if ( true == isset($game['bet_data'][0]['bet_name']) && 0 == strcmp($game['bet_data'][0]['bet_name'], 'No')) {
                                    if ( 'Yes' == tmBetName || 'No' == tmBetName) {
                                        for (const[betName, value] of Object.entries(game['bet_data'])) {
                                            if('Yes' == betName){
                                                betData_yes = value;
                                                display_bet_name_yes = betNameToDisplay_new(betName, game['markets_id']);
                                            }else{
                                                betData_no = value;
                                                display_bet_name_no = betNameToDisplay_new(betName, game['markets_id']);
                                            }
                                        }
                                        
                                        if((1 == betData_yes['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                            // yes
                                            html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ betData_yes['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='"+ display_bet_name_yes +"' data-bet-id='"+ betData_yes['bet_id']  +"' data-bet-price='"+ betData_yes['bet_price'] +"'"+
                                                " data-td-cell='"+betData_yes['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                ">"+display_bet_name_yes+"<span class='betin_right bet_font1'>"+betData_yes['bet_price']+"</span></td>";

                                            // no
                                            html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ betData_no['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='"+ display_bet_name_no +"' data-bet-id='"+ betData_no['bet_id']  +"' data-bet-price='"+ betData_no['bet_price'] +"'"+
                                                " data-td-cell='"+betData_no['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                ">"+display_bet_name_no+"<span class='betin_right bet_font1'>"+betData_no['bet_price']+"</span></td>";
                                        }else{
                                            html += "<td class='bet_list_td w50' data-bet-status='"+ betData_yes['bet_status'] + "'"+
                                                    ">"+ display_bet_name_yes + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                                    "<td class='bet_list_td w50' data-bet-status='"+ betData_no['bet_status'] + "'"+
                                                    ">"+ display_bet_name_no + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                            //html += getLockHtml('w50', game['menu'], game['bet_status'], "<span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ")", "<span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ")");
                                        }
                                    } else {
                                        //let count = game['bet_data'].length;
                                        /*$fcount = 3;
                                        if ($count < 3)
                                            $fcount = $count - 1;*/
                                        let i=0;
                                        for (const[betName, value] of Object.entries(game['bet_data'])) {
                                            //teamName = '';
                                            let betData = value;
                                            display_bet_name = betNameToDisplay_new(betData['bet_name'], game['markets_id']);
                                            let classWidth = 'w30';
                                            
                                            //총득점 홀짝
                                            if (game['markets_id'] == 5 || game['markets_id'] == 51) {
                                                classWidth = 'w50';
                                            }

                                            if (game['markets_id'] == 427) {
                                                let alt = 'under';
                                                let img = '/assets_w/images/arr1.gif';
                                                if (betData['bet_name'].indexOf('Over') > -1) {
                                                    alt = 'over';
                                                    img = '/assets_w/images/arr2.gif';
                                                }
                                                
                                                if((1 == betData['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                                    html += "<td class='bet_list_td "+classWidth+" odds_btn' data-bet-status='"+ betData['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                            " data-odds-type='"+ display_bet_name +"' data-bet-id='"+ betData['bet_id']  +"' data-bet-price='"+ betData['bet_price'] +"'"+
                                                            " data-td-cell='"+betData['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'><img src='"+img+"' alt='"+ alt +"' style='margin-right: 5px'>"+betData['bet_price']+"</span></td>";
                                                }else{
                                                    html += "<td class='bet_list_td "+classWidth+"' data-bet-status='"+ betData['bet_status'] + "'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                                }
                                            } else {
                                                if((1 == betData['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                                    html += "<td class='bet_list_td "+classWidth+" odds_btn' data-bet-status='"+ betData['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                            " data-odds-type='"+ display_bet_name +"' data-bet-id='"+ betData['bet_id']  +"' data-bet-price='"+ betData['bet_price'] +"'"+
                                                            " data-td-cell='"+betData['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'>"+betData['bet_price']+"</span></td>";
                                                }else{
                                                    // 자물쇠 처리
                                                    html += "<td class='bet_list_td "+classWidth+"' data-bet-status='"+ betData['bet_status'] + "'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                                }
                                            }

                                            if ((i + 1) % 3 == 0) {
                                                html += "</tr>"+
                                                        "<tr>";
                                            }
                                            i += 1;
                                        } // end game['bet_data']
                                    } // end
                                html +="</tr>";
                            }
                        } // $game_list end for
                                html +="";
                                } // $order_list end for
                                html += "</table>";
                                html += "</ul>";
                                } // $menu_list end for
                                    /*$(function () {
                                        $('.tda_demo'+fixture_id).text(betCount);
                                    });*/
            } // $fixture_list end for
            //html += "<div class='more_content_area' id='btn_more_"+key+"'><a href='javascript:btn_more_click("+key+")' class=''>+ 더보기</a>";
            
            // 더보기 모바일만
            if(isMobile){
            html += "<div class='more_content_area' id='more_content_area_"+key+"' style='display: none;'>";
            //let isMainBetLock = false;
            let isBtnMore = false; // 더보기 존재유무
            for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
                // 배구, 야구는 메인만 존재
                /*if(sportsKey == 'game_154830'|| sportsKey == 'game_154914' || sportsKey == 'game_687890' || sportsKey == 'game_154919'){
                    betCount += menu_list.length;
                }*/

                if(memuKey == 0) continue;
                
                for (const[orderKey, order_list] of Object.entries(menu_list)) {
                    for (const[marketKey, game_list] of Object.entries(order_list)) {
                        if(in_array(marketKey, arrMainMarket)){ 
                            continue;
                        }
                        
                        isBtnMore = true;    
                        firstKey = Object.keys(game_list)[0];
                        isMainMarket = 0;
                        /*style = 'display: none;';
                        if (memuKey == 0) {
                            isMainMarket = 1;
                            style = 'display: block;';
                        }*/

                        if (game_list[firstKey]['markets_display_name'] == '' || game_list[firstKey]['markets_display_name'] == null){
                            marketsName = game_list[firstKey]['markets_name_origin'];
                        }else{
                            marketsName = game_list[firstKey]['markets_display_name'];
                        }

                        // 승무패 및 오버언더
                        if (marketKey == 427){
                            marketsName += '(' + game_list[firstKey]['bet_base_line'] + ')';
                        }

                        if (marketKey == 13){
                            marketsName += game_list[firstKey]['markets_name'];
                        }
                        $sportsLineColor = getSportsLineColor(gameSportsId);
                        setSportsBackGroundColor(gameSportsId, fixture_id);
                        // 마켓명 표기
                        html += "<a href='#'>"+
                                "<div class='bet_list1_wrap_in_title "+$sportsLineColor+"'>"+marketsName+"<span class='bet_list1_wrap_in_title_right'></span></div>"+
                                "</a>"+
                                "<ul class='bet_list1_wrap_in_new' id='market_"+marketKey+"' style='display:block'>"+
                                "<table width='100%' border='0' cellspacing='0' cellpadding='0' style='padding:1px 0 0 0;'>";

                        for (const[gameKey, game] of Object.entries(game_list)) {
                            html += "<tr>";
                            if (0 == game['menu']) continue;
                            let bGameKey = game['fixture_id'] + '_' + game['markets_id'] + '_' + game['bet_base_line'] + '_' + game['providers_id']+'_'+game['fixture_start_date'];
                            let markets_name = game['markets_name'];
                            let markets_name_origin = game['markets_name_origin'];
                            let markets_display_name = game['markets_display_name'];
                            const timeValue = new Date(game['fixture_start_date']);
                            const betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);
                            const checkTime = 600;
                            
                            // 승무패
                            if (1 == game['menu']) {
                                game['win_bet_id'] = game['win'] = game['lose_bet_id'] = game['lose'] = game['draw_bet_id'] = game['draw'] = 0;
                                for (const[betKey, betData] of Object.entries(game['bet_data'])) {
                                    if ('1' == betData['bet_name']) {
                                        game['win_bet_id'] = betData['bet_id'];
                                        game['win'] = betData['bet_price'];
                                        game['bet_status'] = betData['bet_status'];
                                    } else if ('2' == betData['bet_name']) {
                                        game['lose_bet_id'] = betData['bet_id'];
                                        game['lose'] = betData['bet_price'];
                                        game['bet_status'] = betData['bet_status'];
                                    } else {
                                        game['draw_bet_id'] = betData['bet_id'];
                                        game['draw'] = betData['bet_price'];
                                        game['bet_status'] = betData['bet_status'];
                                    }
                                    // game['fixture_participants_1_name']
                                }

                                // 배당 표기
                                if (Object.keys(game['bet_data']).length == 3) {
                                    //console.log('bet_status : '+game['bet_status']);
                                    //console.log('betweenTime : '+betweenTime);
                                    //console.log('display_status : '+game['display_status']);
                                    //if((1 == game['bet_status'] || 1800 > betweenTime) && 1 == game['display_status']){
                                    if((1 == game['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                        html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                                " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                "><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span> <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
                                        html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='draw' data-bet-id='"+ game['draw_bet_id'] +"' data-bet-price='"+ game['draw'] +"'"+
                                                " data-td-cell='"+game['draw_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                ">무 <span class='betin_right bet_font1'>"+game['draw']+"</span></td>";
                                        html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                                " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                "><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span> <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
                                    }else{
                                        isMainBetLock = true;
                                        html += "<td class='bet_list_td w30'><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                                                "<td class='bet_list_td w30'><img src='/images/icon_lock.png' alt='lock' width='13'></td>"+
                                                "<td class='bet_list_td w30'><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span><img src='/images/icon_lock.png' alt='lock' width='13'></td>";
                                    }
                                } else {
                                    if((1 == game['bet_status'] && 1 == game['display_status']) || checkTime > betweenTime){
                                        html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                                " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                "><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span> <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
                                        html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                                " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                "><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span> <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
                                    }else{
                                        isMainBetLock = true;
                                        html += "<td class='bet_list_td w50'"+
                                                "><span class='sports_team_txt'>"+game['fixture_participants_1_name']+"</span> <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                                "<td class='bet_list_td w50'"+
                                                "><span class='sports_team_txt'>"+game['fixture_participants_2_name']+"</span> <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                    }
                                }
                                html += "</tr>";
                                // 핸디캡
                            } if (2 == game['menu']) {
                                if (1 == Object.keys(game['bet_data']).length){
                                    continue;
                                }
                                game['win_bet_id'] = game['win'] = game['lose_bet_id'] = game['lose'] = 0;
                                let handValue_l = handValue_r = 0;
                                for (const[betKey, betData] of Object.entries(game['bet_data'])) {
                                    if (betData['bet_name'] == 1) {
                                        game['win'] = betData['bet_price'];
                                        game['win_bet_id'] = betData['bet_id'];
                                        game['bet_status'] = betData['bet_status'];
                                        //let bet_line = explode(' ', betData['bet_line'])[0];
                                        let bet_line = betData['bet_line'].split(' ')[0];

                                        //if(isset(betData['bet_line'].split(' ')[1])){
                                        if(isset(betData['bet_line'].split(' ')[1])){
                                            let bet_line_second = betData['bet_line'].split(' ')[1];
                                            bet_line_second = bet_line_second.replace('(', '');
                                            bet_line_second = bet_line_second.replace(')', '');
                                            //bet_line_second = explode('-', bet_line_second);
                                            bet_line_second = bet_line_second.split('-');
                                            handValue_l = bet_line + bet_line_second[0] - bet_line_second[1];
                                        }else{
                                            handValue_l = bet_line;
                                        }

                                        if(handValue_l > 0){
                                            handValue_l = '+'+handValue_l;
                                        }else{
                                            handValue_l = handValue_l;
                                        }
                                    } else {
                                        game['lose'] = betData['bet_price'];
                                        game['lose_bet_id'] = betData['bet_id'];
                                        game['bet_status'] = betData['bet_status'];
                                        //let bet_line = explode(' ', betData['bet_line'])[0];
                                        let bet_line = betData['bet_line'].split(' ')[0];

                                        //if(isset(explode(' ', betData['bet_line'])[1])){
                                        if(isset(betData['bet_line'].split(' ')[1])){
                                            let bet_line_second = betData['bet_line'].split(' ')[1];
                                            bet_line_second = bet_line_second.replace('(', '');
                                            bet_line_second = bet_line_second.replace(')', '');
                                            bet_line_second = bet_line_second.split('-');
                                            handValue_r = bet_line + bet_line_second[0] - bet_line_second[1];
                                        }else{
                                            handValue_r = bet_line;
                                        }

                                        if(handValue_r > 0){
                                            //handValue_r = '+'.number_format(handValue_r,1);
                                            handValue_r = '+'+handValue_r;
                                        }else{
                                            //handValue_r = number_format(handValue_r,1);
                                            handValue_r = handValue_r;
                                        }
                                    }
                                }

                                // 배당 표기
                                if((1 == game['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='"+game['fixture_participants_1_name']+"("+ handValue_l +")' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                            " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 10px'>"+game['win']+"</span></td>";
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='"+game['fixture_participants_2_name']+"("+ handValue_r +")' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                            " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 10px'>"+game['lose']+"</span></td>";
                                }else{
                                    html += "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_1_name']+"</span>(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                            "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                            "><span class='sports_team_handy'>"+game['fixture_participants_2_name']+"</span>(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                }
                                html += "</tr>";
                            // 언더오버
                            } else if (3 == game['menu']) {
                                if (1 == Object.keys(game['bet_data']).length){
                                    continue;
                                }

                                let over = over_bet_id = under = under_bet_id = 0;
                                let over_base_line = under_base_line = '';

                                for (const[betKey, betData] of Object.entries(game['bet_data'])) {
                                    if (betData['bet_name'] == 'Over') {
                                        over = betData['bet_price'];
                                        over_bet_id = betData['bet_id'];
                                        over_status = betData['bet_status'];
                                        //over_base_line = explode(' ', game['bet_base_line'])[0];
                                        over_base_line = game['bet_base_line'].split(' ')[0];
                                    } else {
                                        under = betData['bet_price'];
                                        under_bet_id = betData['bet_id'];
                                        under_status = betData['bet_status'];
                                        //under_base_line = explode(' ', game['bet_base_line'])[0];
                                        under_base_line = game['bet_base_line'].split(' ')[0];
                                    }
                                }

                                // 배당 표기
                                if((1 == over_status && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ over_status + "'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='오버("+ over_base_line +")' data-bet-id='"+ over_bet_id  +"' data-bet-price='"+ over +"'"+
                                            " data-td-cell='"+over_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr2.gif' style='margin-right: 10px'>"+over+"</span></td>";
                                    html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ under_status + "'  data-index='"+ bGameKey+"'"+
                                            " data-odds-type='언더("+ under_base_line +")' data-bet-id='"+ under_bet_id  +"' data-bet-price='"+ under +"'"+
                                            " data-td-cell='"+under_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr1.gif' style='margin-right: 10px'>"+under+"</span></td>";
                                }else{
                                    html += "<td class='bet_list_td w50' data-bet-status='"+ over_status + "'"+
                                            ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                            "<td class='bet_list_td w50' data-bet-status='"+ under_status + "'"+
                                            ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                }
                                html += "</tr>";
                                // 기타
                            } else if (4 == game['menu']) {
                                console.log(game['menu']);
                                html += "<tr>";
                                    //$tmBetName = array_keys($game['bet_data'])[0];
                                    let tmBetName = Object.keys(game['bet_data'])[0];
                                    let betData_yes = betData_no = null;
                                    let display_bet_name_yes = display_bet_name_no = 0;
                                    //if ( true == isset($game['bet_data'][0]['bet_name']) && 0 == strcmp($game['bet_data'][0]['bet_name'], 'No')) {
                                    if ( 'Yes' == tmBetName || 'No' == tmBetName) {
                                        for (const[betName, value] of Object.entries(game['bet_data'])) {
                                            if('Yes' == betName){
                                                betData_yes = value;
                                                display_bet_name_yes = betNameToDisplay_new(betName, game['markets_id']);
                                            }else{
                                                betData_no = value;
                                                display_bet_name_no = betNameToDisplay_new(betName, game['markets_id']);
                                            }
                                        }
                                        
                                        if((1 == betData_yes['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                            // yes
                                            html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ betData_yes['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='"+ display_bet_name_yes +"' data-bet-id='"+ betData_yes['bet_id']  +"' data-bet-price='"+ betData_yes['bet_price'] +"'"+
                                                " data-td-cell='"+betData_yes['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                ">"+display_bet_name_yes+"<span class='betin_right bet_font1'>"+betData_yes['bet_price']+"</span></td>";

                                            // no
                                            html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ betData_no['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                " data-odds-type='"+ display_bet_name_no +"' data-bet-id='"+ betData_no['bet_id']  +"' data-bet-price='"+ betData_no['bet_price'] +"'"+
                                                " data-td-cell='"+betData_no['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                ">"+display_bet_name_no+"<span class='betin_right bet_font1'>"+betData_no['bet_price']+"</span></td>";
                                            }else{
                                                html += "<td class='bet_list_td w30' data-bet-status='"+ betData_yes['bet_status'] + "'"+
                                                        ">"+ display_bet_name_yes + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                                        "<td class='bet_list_td w30' data-bet-status='"+ betData_no['bet_status'] + "'"+
                                                        ">"+ display_bet_name_no + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                            }
                                    } else {
                                        //let count = game['bet_data'].length;
                                        /*$fcount = 3;
                                        if ($count < 3)
                                            $fcount = $count - 1;*/
                                        let i=0;
                                        for (const[betName, value] of Object.entries(game['bet_data'])) {
                                            //teamName = '';
                                            let betData = value;
                                            display_bet_name = betNameToDisplay_new(betData['bet_name'], game['markets_id']);

                                            if (game['markets_id'] == 427) {
                                                let alt = 'under';
                                                let img = '/assets_w/images/arr1.gif';
                                                if (betData['bet_name'].indexOf('Over') > -1) {
                                                    alt = 'over';
                                                    img = '/assets_w/images/arr2.gif';
                                                }
                                                
                                                if((1 == betData['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                                    html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ betData['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                            " data-odds-type='"+ display_bet_name +"' data-bet-id='"+ betData['bet_id']  +"' data-bet-price='"+ betData['bet_price'] +"'"+
                                                            " data-td-cell='"+betData['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'><img src='"+img+"' alt='"+ alt +"' style='margin-right: 10px'>"+betData['bet_price']+"</span></td>";
                                                }else{
                                                    html += "<td class='bet_list_td w30' data-bet-status='"+ betData['bet_status'] + "'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                                }
                                            } else {
                                                if((1 == betData['bet_status'] && 1 == game['display_status'] && false == isMainBetLock) || checkTime > betweenTime){
                                                    html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ betData['bet_status'] + "'  data-index='"+ bGameKey+"'"+
                                                            " data-odds-type='"+ display_bet_name +"' data-bet-id='"+ betData['bet_id']  +"' data-bet-price='"+ betData['bet_price'] +"'"+
                                                            " data-td-cell='"+betData['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'>"+betData['bet_price']+"</span></td>";
                                                }else{
                                                    html += "<td class='bet_list_td w30' data-bet-status='"+ betData['bet_status'] + "'"+
                                                            ">"+ display_bet_name + " <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                                }
                                            }

                                            if ((i + 1) % 3 == 0) {
                                                html += "</tr>"+
                                                        "<tr>";
                                            }
                                            i += 1;
                                        } // end game['bet_data']
                                    } // end
                                html +="</tr>";
                            }
                        } // $game_list end for
                                html +="";
                                } // $order_list end for
                                html += "</table>";
                                html += "</ul>";
                                } // $menu_list end for
                                    /*$(function () {
                                        $('.tda_demo'+fixture_id).text(betCount);
                                    });*/
            } // $fixture_list end for
            //html += "<div class='more_content_area_hide'>숨기기</div>";
            //console.log(gameSportsId);
            if(gameSportsId != 154830 && gameSportsId != 154914 && gameSportsId != 687890){
                if(isBtnMore){
                    html += "</div>"+
                                "<a href='javascript:btn_more_click("+key+")' class='btn_more_"+key+" btn_more' >더보기 ▼</a>";
                }
            }
            // 더보기 메뉴빼고 반복
            // 하나의 경기안에 메뉴별로 루프
            } // 모바일 더보기
            html += "</li>";  // sports_dd_p

            if(isMobile){
                $("#fixture_"+fixture_id+" li").remove();
                $("#fixture_"+fixture_id).append(html);
                $("#team_name_"+fixture_id).text(teamName);
                $("#fixture_time_"+fixture_id).text(fixtureTime);
            }else{
                $(".dropdown3 li").remove();
                $(".dropdown3").append(html);

                // 경기선택 처리
                $("#fixture_row_"+selectFixtureId).removeClass('bet_list1_wrap_on');
                $("#fixture_row_"+selectFixtureId).addClass('bet_list1_wrap');
                $("#fixture_row_"+fixture_id).addClass('bet_list1_wrap_on');
                selectFixtureId = fixture_id;
            }
            //contentObj.html(html);
            // 베팅슬립에 있는 베팅이면 선택표시를 해준다.
            moreDisplaySelect();
            fnSortBetting();
            //console.log(arrMenuKey.length);
        }
    }).fail(function (error) {
    // alert(error.responseJSON['messages']['messages']);
    //location.reload();
    }).always(function (response) {
    });
    //e.preventDefault();
}
    
// ready
// 베팅슬립에서 X버튼 눌렀을때
const notifyCloseBtn = function(obj){
    //let index = $(obj).data('index');
    let betId = $(obj).data('bet-id');
    let thisParent = $(obj).parent().parent().parent().parent();

    totalOdds = totalOdds / thisParent.find('.slip_bet_cell_r').html();
    totalOdds = totalOdds.toFixed(2);
    $('.total_odds').html(totalOdds);

    //$('[data-index="' + index + '"]').removeClass('bet_on');
    $('[data-bet-id="' + betId + '"]').removeClass('bet_on');
    thisParent.remove();

    let betSlipCount = getBetSlipCount() - 1;
    if (betSlipCount == 0) {
        $('.total_odds').text(0);
        $('.bonus_total_odds').text(0);
    }
    
    setBonusPrice(totalOdds, betSlipCount);

    delBetSlip(betId);

    if (isMobile) {
        $('.cart_count2').text(fnGetCartCount());
    }
    changeWillWinMoney();
};

// max 버튼
const maxBtnClick = function(){
    let maxBetMoney = $('.max_bet_money').text();
    maxBetMoney = Number(maxBetMoney.replace(/,/gi, "")); //변경작업

    //let maxBetMoney = Number($('.max_bet_money').text());
    nowMoney = +nowMoney;
    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let totalMax = Infinity;
    if (!betSlip || !betSlip[0]) {
        alert(`경기를 선택해주세요.`);
        return;
    }

    for (let slip of betSlip) {
        if (totalMax >= +slip.leagues_m_bet_money) {
            totalMax = slip.leagues_m_bet_money;
        }
    }

    if ($('#betting_slip_money').val() > maxBetMoney) {
        alert(`더이상 배팅할 수 없습니다`);
        return;
    }


    if (nowMoney === 0) {
        alert(`보유머니가 부족합니다`);
        return;
    } else if (maxBetMoney > nowMoney) {
        $('#betting_slip_money').val(setComma(nowMoney));
    } else {
        $('#betting_slip_money').val(setComma(maxBetMoney));
    }
    if (maxBetMoney > totalMax) {
        if (totalMax > nowMoney) {
            $('#betting_slip_money').val(setComma(nowMoney));
        } else {
            $('#betting_slip_money').val(setComma(totalMax));
        }
    }
    changeWillWinMoney();
    auto_calc(limitBetMoney);
}

// 배팅하기
const bettingClick = function(){
    let betMoney = $('#betting_slip_money').val();
    betMoney = Number(betMoney.replace(/,/gi, "")); //변경작업

    let itemId = $('#itemId').val();
    let itemValue = $('#itemValue').val();
    
    if(itemId == null || itemId == ""){
    	itemId = 0;
    	itemValue = 0;
    }
    
    // if (betMoney % 1000 != 0) {
    //     alert('1000원 단위로 배팅해주세요.');
    //     return;
    // }

    if (betMoney == 0) {
        alert('베팅금액을 선택해주세요.');
        return;
    }

    let mes = '선택하신 내용으로 베팅금액 : ' + betMoney + '원\n 베팅진행하시겠습니까?'
    if (confirm(mes) == false) {
        return;
    }

    let betUrl = '/api/bet/addBet';
    /*if ('ON' == is_betting_slip) {
        betUrl = '/api/bet/onAddBet';
    }
    console.log(betUrl);
    console.log(is_betting_slip);*/
    let memberBetList = [];
    if ($('.sports_cart_bet').length > 0) {
        // 다폴더인지 판단(서버로 넘겨줄값)
        let betFolderType = 'S';
        if ($('.sports_cart_bet').length > 1) {
            betFolderType = 'D';
        }

        $('.sports_cart_bet').each(function (index, item) {
            //let oddsType = $(item).data('oddsTypes');
            let arrindexData = $(item).data('index').split('_');
            let fixture_id = arrindexData[0];
            let marketsId = arrindexData[1];

            memberBetList.push({
                //'betId': betList[$(item).data('index')][oddsType+'_bet_id'],
                //'betPrice': betList[$(item).data('index')][oddsType],
                'betId': $(item).data('bet-id'),
                'betPrice': $(item).data('bet-price'),
                'fixtureId': fixture_id,
                'round': 0,
                'marketsId': marketsId,
                'marketsName': $(item).data('markets-name'),
                'betBaseLine': $(item).data('bet-base-line'),
                //'oddsTypes': $(item).data('oddsTypes'),
                //'leagueId': betList[$(item).data('index')]['fixture_league_id'],
                'leagueTagId': 'leagues_bet_' + fixture_id,
                'fixture_start_date': $(item).data('fixture-start-date')
            })
        });

        
        
        let bonus_odds = $('.bonus_total_odds').text();
        if (isAlreadyBetting)
            return false;
        isAlreadyBetting = true;
        $('#bettingLoadingCircle').show();
        $('.cart_close').trigger('click');
        $.ajax({
            url: betUrl,
            type: 'post',
            data: {
                'betList': memberBetList,
                'betType': 1,
                'totalOdds': totalOdds,
                'bonus_odds': bonus_odds,
                'totalMoney': betMoney,
                'betGroup': '스포츠',
                'folderType': betFolderType,
                'isBettingSlip': is_betting_slip,
                'itemId' : itemId,
                'itemValue' : itemValue	
            },
        }).done(function (response) {
            initForm();
            delAllBetSlip();
            betMaxCheck();
            let check = confirm('베팅에 성공하였습니다. 베팅내역을 확인하시겠습니까?');
            if (check === true) {
                $('#loadingCircle').show();
                location.href = "/web/betting_history?menu=b&bet_group=2&clickItemNum=2";
                $('#loadingCircle').hide();
                return;
            } else {

                $('.util_money').text(setComma(response['data']['total_money']));
                console.log(response['data']['total_money']);
                // 베팅제한금액 갱신
                response['data']['arr_tag_ids'].forEach(function (item, index, arr2) {
                    let tag = '#' + item;
                    //alert(tag);
                    let str_league_money = $(tag).text();
                    let n_league_money = 0;
                    //alert(str_league_money);
                    if (0 <= str_league_money.indexOf('만')) {
                        str_league_money = str_league_money.replace(/,/gi, "");
                        n_league_money = Number(str_league_money.replaceAll("만", "")) * 10000;
                    } else {
                        n_league_money = Number(str_league_money.replace(/,/gi, ""));
                    }

                    n_league_money = n_league_money - response['data']['total_bet_money'];

                    n_league_money = parseInt(n_league_money);
                    let betIndex = $(tag).closest('tr').find('.odds_btn').first().data('index');

                    // const gameObj = fnMakeGameObj(betIndex);
                    //  ['leagues_m_bet_money'] = n_league_money;

                    if (+n_league_money > 10000) {
                        n_league_money = setComma(parseInt(+n_league_money / 10000)) + '만';
                    } else {
                        n_league_money = setComma(parseInt(+n_league_money));
                    }
                    $(tag).text(setComma(n_league_money));
                })
                $('#bettingLoadingCircle').show();
                location.reload();
                $('#bettingLoadingCircle').hide();
                return;
            }
        }).fail(function (error) {
            alert(error.responseJSON['messages']['messages']);
            // 최소, 최대베당 제한에 걸린거면 리로드
            if (error.responseJSON['error'] == 401) {
                location.reload();
            }
        }).always(function (response) {
            isAlreadyBetting = false;
            $('#bettingLoadingCircle').hide();
        });

    } else {
        alert('베팅을 선택해주세요.');
    }
}

const bettingSlipMoneyFocus = function(){
    $('#loadingCircle').hide();
    let userInputBetBefore = $('.betting_slip_money').val();
    sessionStorage.setItem('userInputBet', userInputBetBefore);
}

// focus 사라짐
const bettingSlipMoneyBlur = function(){
     $('#loadingCircle').hide();
    let userInputBetBefore = sessionStorage.getItem('userInputBet');
    let userInputBetAfter = $('#betting_slip_money').val();
    userInputBetAfter = Number(userInputBetAfter.replace(/,/gi, ""));


    if (Number.isNaN(userInputBetAfter)) {
        $('#betting_slip_money').val(userInputBetBefore);
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        alert(`숫자형태로 입력해주세요`);
        return;
    }

    let maxBetMoney = $('.max_bet_money').text();
    maxBetMoney = Number(maxBetMoney.replace(/,/gi, "")); //변경작업

    nowMoney = +nowMoney;
    let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
    let totalMax = Infinity;
    if (!betSlip || !betSlip[0]) {
        $('#betting_slip_money').val(userInputBetBefore);
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        alert(`경기를 선택해주세요.`);
        return;
    }
    for (let slip of betSlip) {
        if (totalMax >= +slip.leagues_m_bet_money) {
            totalMax = slip.leagues_m_bet_money;
        }
    }

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
        if (nowMoney < +userInputBetAfter) {
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
            if (nowMoney < +userInputBetAfter) {
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
    if (max_flag === true) {
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
const wasteBtn = function(){
    if ($('.sports_cart_bet').length <= 0) {
        return;
    }

    let mes = '전체베팅 삭제하시겠습니까?'
    if (confirm(mes) == false) {
        return;
    }

    delAllBetSlip();
    initForm();
    cartCount();
}

// 배당선택
const oddsBtn = function(){
    
}

// 종목별 마켓 라인 색상구하기
const setSportsBackGroundColor = function(gameSportsId, fixture_id){
	
	// $(".bettingInfo").css('backgroundColor','');
	$(".bettingInfo").css('border','');
    let sportsColor = '';
    switch(Number(gameSportsId)){
        case 6046: // 축구
            // $("#fixture_row_"+fixture_id).css('backgroundColor','#4d570b');
            $("#fixture_row_"+fixture_id).css('border-color','#7e8f13');
            break;
        case 48242:
        	// $("#fixture_row_"+fixture_id).css('backgroundColor','#704b0f');
            $("#fixture_row_"+fixture_id).css('border-color','#66563c');
            break;
        case 154914:
        	// $("#fixture_row_"+fixture_id).css('backgroundColor','#0c595c');
            $("#fixture_row_"+fixture_id).css('border-color','#13868a');
            break;
        case 154830:
        	// $("#fixture_row_"+fixture_id).css('backgroundColor','#636363');
            $("#fixture_row_"+fixture_id).css('border-color','#b1b1b1');
            break;
        case 35232:
        	// $("#fixture_row_"+fixture_id).css('backgroundColor','#362257');
            $("#fixture_row_"+fixture_id).css('border-color','#4f455e');
            break;
        case 687890:
        	// $("#fixture_row_"+fixture_id).css('backgroundColor','#582241');
            $("#fixture_row_"+fixture_id).css('border-color','#5e4553');
            break;
        case 154919:
            break;
        default :
    }
}

// 아이템 삭제
const fnInitItem = function(){
    console.log('fnInitItem');
    $('#itemId').val("");
    $('#itemValue').val("");
    $('#clickItemName').text("");
    $('#selectItemList').hide();
}

//g머니사용 아이템 선택
const fnClickItem = function(itemId, name, itemValue){
//alert('해당이벤트는 점검중입니다.');
//return;
    if($('#itemId').val() != ""){
            alert("G머니사용 패치는 베팅 경기당 한개만 사용이 가능합니다.");		
            return false;
    }
    $('#selectItemList').show();
    $('.gm_pop1_close').click();
    $('#clickItemName').text(name);
    $('#itemId').val(itemId);
    $('#itemValue').val(itemValue);

    if(isMobile){
        $('.cart_open').click();
    }
}