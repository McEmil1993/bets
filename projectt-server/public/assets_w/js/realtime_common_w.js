// 베팅슬립에 있는 배팅 선택처리
function moreDisplaySelect(activeBetId) {
    if(activeBetId.length > 0){
        activeBetId.reverse().forEach(function(item){
            $('.odds_btn[data-bet-id="' + item +'"]').addClass('bet_on');
        });

        //$('#betting_slip_money').val(setComma(betAmount));
        //changeWillWinMoney();
    }
}


function sports_select(sportsId){
    getRealTimeGameLiveScoreList(sportsId, 0);
}


function league_select(leagueId){
    getRealTimeGameLiveScoreList(0, 0, leagueId);
}