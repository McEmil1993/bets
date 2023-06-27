// <!-- 내정보 드롭다운 -->
jQuery(document).ready(function() {
    jQuery("#jquery-accordion-menu").jqueryAccordionMenu();
});

$(function() {
    let path = window.location.pathname;
    let current_page = path.split("/").pop();

    write_access_log(path, current_page);
    
	$("#demo-list li").click(function() {
		$("#demo-list li.active").removeClass("active")
		$(this).addClass("active");
	})
})

//숫자만입력
function numberOnly(obj){
	$(obj).val($(obj).val().replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'))
}

// <!-- /slider-wrapper -->
$(function() {
	var Page = (function() {
		var $nav = $('#nav-dots > span'), slitslider = $('#slider').slitslider(
				{
					onBeforeChange : function(slide, pos) {
						$nav.removeClass('nav-dot-current');
						$nav.eq(pos).addClass('nav-dot-current');
					}
				}), init = function() {
			initEvents();
		}, initEvents = function() {
			$nav.each(function(i) {
				$(this).on('click', function(event) {
					var $dot = $(this);
					if (!slitslider.isActive()) {
						$nav.removeClass('nav-dot-current');
						$dot.addClass('nav-dot-current');
					}
					slitslider.jump(i + 1);
					return false;
				});
			});
		};
		return {
			init : init
		};
	})();
	Page.init();
});

$(document).ready(function() {
    
    var owl = $("#owl-demo");

    owl.owlCarousel({
    autoPlay:false,
    items : 4, //10 items above 1000px browser width
    itemsDesktop : [1480,4], //5 items between 1000px and 901px

    itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
    
    });

    // Custom Navigation Events
    $(".next").click(function(){
      owl.trigger('owl.next');
    })
    $(".prev").click(function(){
      owl.trigger('owl.prev');
    })
    $(".play").click(function(){
      owl.trigger('owl.play',2000);
    })
    $(".stop").click(function(){
      owl.trigger('owl.stop');
    })
  });

/*Add class when scroll down*/
$(window).scroll(function(event){
    var scroll = $(window).scrollTop();
    if (scroll >= 50) {
        $(".go-top").addClass("show");
    } else {
        $(".go-top").removeClass("show");
    }
});
/*Animation anchor*/
/* $('a').click(function(){
	console.log('test');
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1000);
}); */
$('.go-top').click(function(){
	
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1000);
});


/*
 * 날짜포맷 yyyy-MM-dd 변환
 */
function getFormatDate(date){
    var year = date.getFullYear();
    var month = (1 + date.getMonth());
    month = month >= 10 ? month : '0' + month;
    var day = date.getDate();
    day = day >= 10 ? day : '0' + day;
    return year + '-' + month + '-' + day;
}

/*
 * 날짜포맷 MM-dd 변환
 */
function getFormatDateMonth(date){
    date = new Date(date);
    let month = (1 + date.getMonth());
    month = month >= 10 ? month : '0' + month;
    let day = date.getDate();
    day = day >= 10 ? day : '0' + day;
    return month + '-' + day;
}

// 몇일전, 몇일후 날짜 구하기
function getAddDate(date, days){
    date = date.split("-");
    var beforeDate = new Date();
    beforeDate.setDate(beforeDate.getDate()+days);
    var y = beforeDate.getFullYear();
    var m = beforeDate.getMonth() + 1;
    var d = beforeDate.getDate();
    if(m < 10) { m = "0" + m; }
    if(d < 10) { d = "0" + d; }
    return beforeDate = y + "-" + m + "-" + d;
}

// 요일구하기
function getDayOfWeek(date){
    let week = ['일', '월', '화', '수', '목', '금', '토'];
    return week[new Date(date).getDay()];
}

/*
 * url 에서 parameter 추출
 */

function getParam(sname) {
    var params = location.search.substr(location.search.indexOf("?") + 1);
    var sval = "";
    params = params.split("&");
    for (var i = 0; i < params.length; i++) {
        temp = params[i].split("=");
        if ([temp[0]] == sname) { sval = temp[1]; }
    }
    return sval;

}

// 배팅 네임 변경 수정본
function betNameToDisplay_new(str,market_id) {
    if (str === '1'){
        if(market_id == 16){
            str = '홈';
        } else{
            str = '승';
        }
    }
    else if (str === '2'){
        if(market_id == 16){
            str = '원정';
        } else{
            str = '패';
        }
    }

    else if (str === '1 And Over'){
        str = '승+오버';
    }else if (str === '1 And Under'){
        str = '승+언더';

    }else if (str === '2 And Over'){
        str = '패+오버';
    }else if (str === '2 And Under'){
        str = '패+언더';

    }else if (str === 'X And Over'){
        str = '무+오버';
    }else if (str === 'X And Under'){
        str = '무+언더';

    }else if (str === '1st Period'){
        str = '1피어리어드';

    }else if (str === '2nd Period'){
        str = '2피어리어드';

    }else if (str === '3rd Period'){
        str = '3피어리어드';

    }else if (str === '4th Period'){
        str = '4피어리어드';


    }else if (str === '1st Half'){
        str = '전반';
    }else if (str === '2nd Half'){
        str = '후반';
    }else if (str === 'All Periods The Same'){
        if(market_id == 70){
            str = '모든 피어리어드 같음';
        } else{
            str = '전후반 같음';
        }
    }else if (str === 'No Goal'){
        str = '노골';
    }else if (str === 'X'){
        str = '무';
    }else if (str === '1X'){
        str = '승무';
    }else if (str === 'X2'){
        str = '무패';
    }else if (str === '12'){
        str = '승패';
    }else if (str === 'Over'){
        str = '오버';
    }else if (str === 'Under'){
        str = '언더';
    }else if (str === 'Yes'){
        str = '예';
    }else if (str === 'No'){
        str = '아니오';
    }else if (str === '1/1'){
        str = '홈/홈';
    }else if (str === '1/2'){
        str = '홈/원정';
    }else if (str === 'X/2'){
        str = '무/원정';
    }else if (str === '2/1'){
        str = '원정/홈';
    }else if (str === 'X/1'){
        str = '무/홈';
    }else if (str === '2/2'){
        str = '원정/원정';

    }else if (str === '1/X'){
        str = '홈/무';

    }else if (str === '2/X'){
        str = '원정/무';
    }else if (str === 'X/X'){
        str = '무/무';
    }else if (str == 'Any Other Score'){
        str = '그외 점수';
    }else if (str === 'Even'){
        str = '짝';
    }else if (str === 'Odd'){
        str = '홀';
    }
    return str;
}

function setComma(number){
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// 미니게임(파워볼, 파워사다리, 키노사다리) 휴장시간 체크(이때는 api 데이터도 안넘어온다.)
const checkRestTime = function(){
    /*let today = new Date();
    let hours = Number(today.getHours());
    if(hours >= 0 && hours < 6){
        return true;
    }*/
    return false;
}

/* 페이지네이션 함수 */
const dataSize = 10; /* 페이지 버튼 노출 갯수 */
const pageSize = 10; /* 페이지당 노출 데이터 갯수 */
let fnSetPagination = function (totalCnt, curPageNo, fnClick, tagId) {

    totalCnt = parseInt(totalCnt); /* 전체 데이터 갯수 */
    curPageNo = parseInt(curPageNo); /* 현재 페이지 */
    

    let html = new Array();
    if (totalCnt <= 0)
        totalCnt = 1;

    /* 총 페이지 수 */
    let totalPageCnt = Math.ceil(totalCnt / dataSize);

    let pageGroup = Math.ceil(curPageNo / pageSize);

    /* 끝 페이지 번호 */
    let lastNum = pageGroup * pageSize;
    if (lastNum > totalPageCnt)
        lastNum = totalPageCnt;

    /* 첫 페이지 번호 */
    let firstNum = lastNum - (pageSize - 1);
    if (firstNum < 1)
        firstNum = 1;


    /* 이전 영역 */
    if (curPageNo > pageSize) {

        /* 이전 */
        let s2;
        if (curPageNo % pageSize == 0) {
            s2 = curPageNo - pageSize;
        } else {
            s2 = curPageNo - curPageNo % pageSize;
        }
        /*html.push(`<a href="javascript:${fnClick}(${totalCnt},${s2 - 1});"><div class="page"><</div></a>`);*/
        html.push(`<li><a href="javascript:${fnClick}(${totalCnt},${s2 - 1});"><span class="page"><</span></li>`);
    }

    /* 페이지 번호 */
    for (let i = firstNum; i < lastNum + 1; i++) {
        if (i == curPageNo) {
        	html.push(`<li><a href='javascript:${fnClick}(${i});'><span class='pageon'>${i}</span></a></li>`);
        	//html.push(`<a href='javascript:${fnClick}(${i});'><div class='page_on'>${i}</div></a>`);
        } else {
        	html.push(`<li><a href='javascript:${fnClick}(${i});'><span class='page'>${i}</span></a></li>`);
            //html.push(`<a href='javascript:${fnClick}(${i});'><div class='page'>${i}</div></a>`);
        }
        if (i == totalPageCnt) {
            break;
        } else {
            html.push(' ');
        }
    }

    /* 다음 영역*/
    if (totalPageCnt > lastNum) {
        /* 다음 */
        html.push(`<a href="javascript:${fnClick}(${lastNum + 1});"><div class="page">></div></a>`);
    }

    $('#'+tagId).html(html);
    return false;

};

let fnSetPagination_left = function (totalCnt, curPageNo, fnClick, tagId, sport_id = 0) {
    // console.log(totalCnt, curPageNo);

    const dataSize = 50; /* 페이지당 노출 데이터 갯수 */
    const pageSize = 10; /* 페이지 버튼 노출 갯수 */
    totalCnt = parseInt(totalCnt); /* 전체 데이터 갯수 */
    curPageNo = parseInt(curPageNo); /* 현재 페이지 */
    

    let html = new Array();
    if (totalCnt <= 0)
        totalCnt = 1;

    /* 총 페이지 수 */
    let totalPageCnt = Math.ceil(totalCnt / dataSize);

    let pageGroup = Math.ceil(curPageNo / pageSize);

    /* 끝 페이지 번호 */
    let lastNum = pageGroup * pageSize;
    if (lastNum > totalPageCnt)
        lastNum = totalPageCnt;

    /* 첫 페이지 번호 */
    let firstNum = lastNum - (pageSize - 1);
    if (firstNum < 1)
        firstNum = 1;


    /* 이전 영역 */
    if (curPageNo > pageSize) {

        /* 이전 */
        let s2;
        if (curPageNo % pageSize == 0) {
            s2 = curPageNo - pageSize;
        } else {
            s2 = curPageNo - curPageNo % pageSize;
        }
        html.push(`<a href="javascript:${fnClick}(${totalCnt},${s2 - 1});"><div class="page"><</div></a>`);
    }

    /* 페이지 번호 */
    for (let i = firstNum; i < lastNum + 1; i++) {
        let param = 'sports?data=1&page='+i;
        if(sport_id > 0){
            param = 'sports?data=1&sports_id='+sport_id+'&page='+i;
        }else{
            param = 'sports?data=1&page='+i;
        }
        if (i == curPageNo) {
            html.push(`<a href='javascript:${fnClick}("${param}");'><div class='page_on'>${i}</div></a>`);
        } else {
            html.push(`<a href='javascript:${fnClick}("${param}");'><div class='page'>${i}</div></a>`);
        }
        if (i == totalPageCnt) {
            break;
        } else {
            html.push(' ');
        }
    }

    /* 다음 영역*/
    if (totalPageCnt > lastNum) {
        /* 다음 */
        html.push(`<a href="javascript:${fnClick}(${lastNum + 1});"><div class="page">></div></a>`);
    }

    $('.'+tagId).html(html);
    return false;

};

// 종목별 마켓 라인 색상구하기
const getSportsLineColor = function(gameSportsId){
	
    let sportsColor = '';
    switch(Number(gameSportsId)){
        case 6046:
            sportsColor = 'line2';
            break;
        case 48242:
            sportsColor = 'line3';
            break;
        case 154914:
            sportsColor = 'line4'; //
            break;
        case 154830:
            sportsColor = 'line5';
            break;
        case 35232:
            sportsColor = 'line6';
            break;
        case 687890:
            sportsColor = 'line7';
            break;
        case 154919:
            sportsColor = 'line8';
            break;
        default :
        	sportsColor = '';
    }
    return sportsColor;
}

// 모바일인지 아닌지 판단
const mobileCheck = function(){
    return /Mobi/i.test(window.navigator.userAgent);
}

function write_access_log(path, current_page){
    // Here's leave an access log
     $.ajax({
        url: '/api/write_access_log',
        type: 'post',
        data: {
            'path':path,
            'current_page':current_page
        }
    }).done(function (response) {
        //alert('인증코드를 전송하였습니다.');
    }).fail(function (error, response, p) {
        alert(error.responseJSON.messages.messages);
    }).always(function (response) {
    });
}