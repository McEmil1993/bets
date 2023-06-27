/*
 * php isset 대응
 */
function isset(type)
{
    if(typeof type !== 'undefined')
    {
        return true;
    }
    return false;
}

function in_array(target, arr)
{
    return arr.includes(target);
}

function getMarketsName(marketKey, game_list){
    marketsName = '';
    if(game_list['markets_display_name'] == '' || game_list['markets_display_name'] == null) {
        marketsName = game_list['markets_name_origin'];
    } else {
        marketsName = game_list['markets_display_name'];
    }

    // 승무패 및 오버언더
    if(marketKey == 427) {
        marketsName += '('+game_list['bet_base_line']+')';
    }
    
    // 핸디캡승무패
    if (marketKey == 13)
        marketsName += game_list['markets_name'];
    
    return marketsName;
}

// 스포츠 리그검색
function searchLeague(){
    let leagueName = $('#league_name').val();
    let moveUrl = location.pathname;
    
    if(leagueName.length === 0){
        alert('검색할 리그명을 입력해주세요.');
        return;
    }
    
    if(leagueName == "한국"){
        leagueName = "대한민국";
    }
    
    moveUrl +=  '?' + 'league_name=' + leagueName;

    fnLoadingMove(moveUrl);
}

// 보너스 배당 체크를 위한 베팅갯수(스포츠) - only bonus bet count
function getBetSlipCount() {
    let betCount = 0;
    let isLimitFolder = false;
    
    if('ON' == isClassic){
        betSlip = $('.sports_cart_bet');
        if(!betSlip || !betSlip[0]) {
            return betCount;
        }
        
        for (let slip of betSlip) {
            if($(slip).data('bet-price') > limit_folder_bonus){
                betCount += 1;
            }else{
                isLimitFolder = true;
            }
        }
        
        if('CHOSUN' == serverName && true == isLimitFolder){
            betCount = 0;
        }
        
        //return $('.slip_bet_ing').length;
    }else{
        let betSlip = JSON.parse(sessionStorage.getItem("betSlip"));
        if(!betSlip || !betSlip[0]) {
            return betCount;
        }

        for (let slip of betSlip) {
            if(slip.betPrice > limit_folder_bonus){
                betCount += 1;
            }else{
                isLimitFolder = true;
            }
        }
        
        // if('CHOSUN' == serverName && true == isLimitFolder){
        //     betCount = 0;
        // }
    }
    return betCount;
}

// 보너스 배당 체크를 위한 베팅갯수(실시간)
function getBetSlipCountReal() {
    let betCount = 0;
    let isLimitFolder = false;
    
    $('.slip_bet_ing .sports_cart_bet_p').each(function() { 
        if(Number($(this).text().trim()) > limit_folder_bonus){
            betCount += 1;
        }else{
            isLimitFolder = true;
        }
    });
    
    //if('CHOSUN' == serverName && true == isLimitFolder){
    if(true == isLimitFolder){
        betCount = 0;
    }
    
    /*if(!betSlip || !betSlip[0]) {
        return betCount;
    }*/
    return betCount;
}

// 스포츠 공통
const setBettingSlip = function(ateg, current_is_betting_slip){
    let imgsrc = '';
    // 현재값이 온이면 오프, 오프면 온으로 한다.
    if ('ON' == current_is_betting_slip) {
        current_is_betting_slip = 'OFF';
        imgsrc = '/assets_w/images/cart_fix2.png';    
    } else {   
        current_is_betting_slip = 'ON';
        imgsrc = '/assets_w/images/cart_fix1.png';
    }
    
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/web/setBettingSlip',
        data: {'is_betting_slip': current_is_betting_slip},
        success: function (result) {
            //console.log(result['code']);
            if (result['code'] == 200) {
                alert('업데이트 되었습니다.');

                $(ateg).attr("onclick", "setBettingSlip(this, '"+current_is_betting_slip+"')");
                $(ateg).attr("src", imgsrc);
                is_betting_slip = current_is_betting_slip;
                return;
            } else {
                alert('업데이트 실패 (1)');
                return;
            }
        },
        error: function (request, status, error) {
            alert('업데이트 실패 (2)');

            return;
        }
    });
}