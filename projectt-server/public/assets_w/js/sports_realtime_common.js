/*
 * 스포츠, 실시간 공통
 */
// 보너스 배당표기
function setBonusPrice(totalOdds, betSlipCount) {
    let bonusPrice = 0;
    if (betSlipCount < 3) {
        $('.bonus_total_odds').html(bonusPrice);
        return;
    }
    
    if (betSlipCount == 3) {
        bonusPrice = odds_3_folder_bonus;
    } else if (betSlipCount == 4) {
        bonusPrice = odds_4_folder_bonus;
    } else if (betSlipCount == 5) {
        bonusPrice = odds_5_folder_bonus;
    } else if (betSlipCount == 6) {
        bonusPrice = odds_6_folder_bonus;
    } else{
        bonusPrice = odds_7_folder_bonus;
    }

    $('.total_odds').html((totalOdds * bonusPrice).toFixed(2));
    $('.bonus_total_odds').html(bonusPrice);
}

// 같은 경기에 다른 게임이 이미 선택 되어있는지 체크
// cart checking
function isBettingFixture(betListIndex){
    let result = false;
    $('.slip_bet_ing').each(function(){
        let fixtureId = $(this).data('index').split('_')[0];
        if(fixtureId == betListIndex){
            result = true;
        }
    });
    return result;
}

function doNotReload(){
    if(    (event.ctrlKey == true && (event.keyCode == 78 || event.keyCode == 82)) //ctrl+N , ctrl+R 
        || (event.keyCode == 116)) // function F5
    {
      event.keyCode = 0;
      event.cancelBubble = true;
      event.returnValue = false;
    }
}