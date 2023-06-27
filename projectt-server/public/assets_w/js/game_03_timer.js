let powerball_timer = 0;
let pladder_timer = 0;
let kladder_timer = 0;
let premiership_timer = 0;
let superleague_timer = 0;
let worldcup_timer = 0;

let powerball_remain_time = 0;
let pladder_remain_time = 0;
let kladder_remain_time = 0;
let premiership_remain_time = 0;
let superleague_remain_time = 0;
let worldcup_remain_time = 0;

let soccer_id_1 = soccer_id_2 = soccer_remain_time_1 = soccer_remain_time_2 = 0;
let premierShipTime = euroCupTime = superLeagueTime = worldCupTime = 0;
let soccer_timer = 0;

const setLnbMiniGameTimer = function() {
    $.ajax({
        url: '/minigame/getLnbTimer',
        type: 'post'
    }).done(function(response) {
    	
        powerball_remain_time = response['data']['powerball_remain_time'] + 4;
        //pladder_remain_time = response['data']['pladder_remain_time'] + 4;
        //kladder_remain_time = response['data']['kladder_remain_time'] + 4;

        powerball_timer = setInterval(powerballCheckRemainTime, 1000);
        //pladder_timer = setInterval(pladderCheckRemainTime, 1000);
        //kladder_timer = setInterval(kladderCheckRemainTime, 1000);

    }).fail(function(error) {
        alert(error.responseJSON['messages']['messages']);
    }).always(function(response) {});
}

const setPladderGameTimer = function() {
    $.ajax({
        url: '/minigame/getLnbTimer',
        type: 'post'
    }).done(function(response) {
        pladder_remain_time = response['data']['pladder_remain_time'] + 4;
        pladder_timer = setInterval(pladderCheckRemainTime, 1000);
    }).fail(function(error) {
        alert(error.responseJSON['messages']['messages']);
    }).always(function(response) {});
}

const setKladderGameTimer = function() {
    $.ajax({
        url: '/minigame/getLnbTimer',
        type: 'post'
    }).done(function(response) {
    	kladder_remain_time = response['data']['kladder_remain_time'] + 4;
    	kladder_timer = setInterval(kladderCheckRemainTime, 1000);
    }).fail(function(error) {
        alert(error.responseJSON['messages']['messages']);
    }).always(function(response) {});
}

const setLnbBetTimer = function() {
    $.ajax({
        url: '/minigame/getLnbTimer',
        type: 'post'
    }).done(function(response) {
    	
        let currentDate = stringToDate(response['data']['serverDate']);
        set_close_time(response['data']['leagueTime'], currentDate);
        soccer_timer = setInterval(function(){
        	soccerCheckRemainTime();
        }, 1000);
        
    }).fail(function(error) {
        alert(error.responseJSON['messages']['messages']);
    }).always(function(response) {});
}

// 남은시간 체크
function soccerCheckRemainTime(){

    // 시간차감
    soccer_remain_time_1 -= 1;
    
    // 첫번째 경기
    let minite = Math.floor(soccer_remain_time_1 / 60);
    if(minite < 10){
        minite = '0'+minite;
    }
    
    let second = soccer_remain_time_1 % 60;
    if(second < 10){
        second = '0'+second;
    }
    
    let displayRemainTime_1 = minite + ':' + second;
    $('#timer_' + soccer_id_1).text(displayRemainTime_1);
    
    if(soccer_remain_time_1 <= 0){
        /*initForm();
        round = 0;
        bet_markets_id = 0;*/
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
    
    second = premierShipTime % 60;
    if(second < 10){
        second = '0'+second;
    }
    let displayLeagueTime = minite + ':' + second;
    $('.close_time_1').text(displayLeagueTime);
    
    // 슈퍼리그
    minite = Math.floor(superLeagueTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }
    
    second = superLeagueTime % 60;
    if(second < 10){
        second = '0'+second;
    }
    displayLeagueTime = minite + ':' + second;
    $('.close_time_2').text(displayLeagueTime);
    
    // 유로컵
    minite = Math.floor(euroCupTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }
    
    second = euroCupTime % 60;
    if(second < 10){
        second = '0'+second;
    }
    displayLeagueTime = minite + ':' + second;
    $('.close_time_3').text(displayLeagueTime);
    
    // 월드컵
    minite = Math.floor(worldCupTime / 60);
    if(minite < 10){
        minite = '0'+minite;
    }
    
    second = worldCupTime % 60;
    if(second < 10){
        second = '0'+second;
    }
    displayLeagueTime = minite + ':' + second;
    $('.close_time_11').text(displayLeagueTime);
    
    // 시간종료 체크
    if(premierShipTime <= 0 || euroCupTime <= 0 || superLeagueTime <= 0 || worldCupTime <= 0){
    	clearInterval(soccer_timer);
    	setLnbBetTimer();
    }
}
function clearIntervalMiniGame() {

	clearInterval(powerball_remain_time);
	clearInterval(pladder_remain_time);
	clearInterval(kladder_remain_time);
}

function stringToDate(stringDt){
    let dt = stringDt.split(' ');
    let date = dt[0].split('-');
    let time = dt[1].split(':');

    let soccer_date = new Date(date[0], date[1], date[2], time[0], time[1], time[2]);
    return soccer_date;
}

// 리그별 시간 설정
function set_close_time(leagueTime, currentDate){
    let close_time = miniutes = seconds = 0;
    let display_miniutes = display_seconds = '';
    let soccer_date = 0;
    for (const[key, evItem] of Object.entries(leagueTime)) {
        soccer_date = stringToDate(evItem['start_dt']);
        close_time = soccer_date.getTime() - currentDate.getTime();
        miniutes = Math.floor((close_time % (1000 * 60 * 60)) / (1000*60));
        seconds = Math.floor((close_time % (1000 * 60)) / 1000);
        if(miniutes < 10){
            display_miniutes = '0'+miniutes;
        }else{
            display_miniutes = miniutes;
        }
        
        if(seconds < 10){
            display_seconds = '0'+seconds;
        }else{
            display_seconds = seconds;
        }

        let displayRemainTime = display_miniutes + ':' + display_seconds ;
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

// 남은시간 체크
const powerballCheckRemainTime = function() {

	// 시간차감
	let displayRemainTime = '';
	powerball_remain_time = powerball_remain_time - 1;
	
	if (powerball_remain_time <= 60 || powerball_remain_time >= 287) {
		if (powerball_remain_time <= 60) {
			displayRemainTime = '베팅마감';
		} else {
			displayRemainTime = '베팅준비중';
		}
	} else {
		let minite = Math.floor(powerball_remain_time / 60);
		if (minite < 10) {
			minite = '0' + (minite - 1);
		}

		let second = powerball_remain_time % 60;
		if (second < 10) {
			second = '0' + second;
		}

		displayRemainTime = minite + ':' + second;
	}

 // 시간출력
	if (powerball_remain_time > 0) {
		$('.powerball_remain_time').text(displayRemainTime);

		if(displayRemainTime == "베팅마감") {
			$('.powerballBettingTimerText').text("");
		}
		if(displayRemainTime == "베팅준비중") {
			$('.powerballBettingTimerText').text("베팅마감");
		}
	}

	// 시간끝났을시 정보호출
	/*if (powerball_remain_time <= 0) {
		clearIntervalMiniGame();
	}*/
}

// 남은시간 체크
const pladderCheckRemainTime = function() {

	// 시간차감
	let displayRemainTime = '';
	pladder_remain_time = pladder_remain_time - 1;
	
	if (pladder_remain_time <= 60 || pladder_remain_time >= 287) {
		if (pladder_remain_time <= 60) {
			displayRemainTime = '베팅마감';
		} else {
			displayRemainTime = '베팅준비중';
		}
	} else {
		let minite = Math.floor(pladder_remain_time / 60);
		if (minite < 10) {
			minite = '0' + (minite - 1);
		}

		let second = pladder_remain_time % 60;
		if (second < 10) {
			second = '0' + second;
		}

		displayRemainTime = minite + ':' + second;
	}

 // 시간출력
	if (pladder_remain_time > 0) {
		$('.pladder_remain_time').text(displayRemainTime);

		if(displayRemainTime == "베팅마감") {
			$('.pladderBettingTimerText').text("");
		}
		if(displayRemainTime == "베팅준비중") {
			$('.pladderBettingTimerText').text("베팅마감");
		}
	}

	// 시간끝났을시 정보호출
	if (pladder_remain_time <= 0) {
		setPladderGameTimer();
        clearInterval(pladder_remain_time);
	}
}

// 남은시간 체크
const kladderCheckRemainTime = function() {

	// 시간차감
	let displayRemainTime = '';
	kladder_remain_time = kladder_remain_time - 1;
	
	if (kladder_remain_time <= 60 || kladder_remain_time >= 287) {
		if (kladder_remain_time <= 60) {
			displayRemainTime = '베팅마감';
		} else {
			displayRemainTime = '베팅준비중';
		}
	} else {
		let minite = Math.floor(kladder_remain_time / 60);
		if (minite < 10) {
			minite = '0' + (minite - 1);
		}

		let second = kladder_remain_time % 60;
		if (second < 10) {
			second = '0' + second;
		}

		displayRemainTime = minite + ':' + second;
	}

 // 시간출력
	if (kladder_remain_time > 0) {
		$('.kladder_remain_time').text(displayRemainTime);

		if(displayRemainTime == "베팅마감") {
			$('.kladderBettingTimerText').text("");
		}
		if(displayRemainTime == "베팅준비중") {
			$('.kladderBettingTimerText').text("베팅마감");
		}
	}

	// 시간끝났을시 정보호출
	if (pladder_remain_time <= 0) {
		setKladderGameTimer();
        clearInterval(kladder_remain_time);
	}
}