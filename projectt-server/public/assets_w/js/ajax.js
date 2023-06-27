const call_ajax = function (call_name, url, data, type='post') {
    let result_code = 200;
    let result_data = null;
    
    $.ajax({
        url: url,
        type: type,
        data: data,
        // beforeSend : function(){
        //     $(document).find("#loadingCircle").show();
        // },
        // complete : function(){
        //     $(document).find("#loadingCircle").hide();
        // }
    }).done(function (response) {
        result_done(call_name, response);
    }).fail(function (error) {
        console.log(error);
        console.log(call_name);
       //alert(error.responseJSON['messages']['messages']);
       result_fail(call_name, error);
    }).always(function (response) {
    });
}

const call_ajax_loading = function (call_name, url, data, type='post') {
    let result_code = 200;
    let result_data = null;
    
    $.ajax({
        url: url,
        type: type,
        data: data,
        beforeSend : function(){
            $(document).find("#loadingCircle").show();
        },
        complete : function(){
            $(document).find("#loadingCircle").hide();
        }
    }).done(function (response) {
        result_done(call_name, response);
    }).fail(function (error) {
       //alert(error.responseJSON['messages']['messages']);
       result_fail(call_name, error);
    }).always(function (response) {
    });
}

const result_done = function (call_name, response) {
    switch(call_name){
        case 'login_ready' :
            result_login_ready(response);
            break;
        case 'duplicateLoginCheck_ready' :  // user info
            result_duplicateLoginCheck_ready(response);
            break;
        case 'locationMessageCheck_ready' : //  message
            result_locationMessageCheck_ready(response);
            break;

        case 'realtime_ready':
            result_realtime_ready(response);
            break;
                

        case 'sports_ready':
            result_sports_ready(response);
            if(response.totalCnt){ paging(response.totalCnt, 'sports', 'pagination'); }
            break;
        // case 'sports_lnb_ready' :
        //     result_sports_lnb_ready(response);
        //     break;


        case 'classic_ready' : 
            result_classic_ready(response);
            // if(response.totalCnt){ paging(response.totalCnt, 'getClassicList', 'pagination'); }
            break;


        case 'getList_ready':       // list
            result_getList_ready(response);
            break;
        case 'getRankList_ready':   // pageing list
            result_getRankList_ready(response);
            if(response.totalCnt){ paging(response.totalCnt, 'getRankList', 'pagination'); }
            break;
			
        default:
            console.log('error call_name : '+call_name);
            break;
    }
}

const result_fail = function (call_name, error) {
    //console.log('error', error);
    //console.log(error.responseJSON.messages.error);

    if(400 == error.status){
        let messages = error.responseJSON.messages.error;
        alert(messages);
    }

    //if( messages == '인증토큰값이 잘못되었습니다.' ){
    if( call_name == 'duplicateLoginCheck_ready' ){
        console.log(call_name);
        if(400 == error.status){
            location.href = '/member/logout';
        }else{
            location.reload(true);
        }
    } else if(call_name == 'locationMessageCheck_ready'){
        if(400 == error.status){
            location.href = '/member/logout';
        }else{
            location.reload(true);
        }
    } else if(call_name == 'login_ready' ){
    //    result_login_ready(error);
        result_login_ready('loginError');
    }
}



let paging = function (totalCnt, fnClick, tagId) {
	
    let dataSize = 20; /* 페이지당 노출 데이터 갯수 */
	let pageSize = 5; /* 페이지 버튼 노출 갯수 */
    
    totalCnt = parseInt(totalCnt); /* 전체 데이터 갯수 */
    curPageNo = parseInt(curPageNo); /* 현재 페이지 */

    // curPageNo = 1;
    // console.log(totalCnt, curPageNo);


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
        html.push(`<a class="page_prev" href="javascript:${fnClick}(${s2 - 1});">이전</a>`);
    }

    /* 페이지 번호 */
    for (let i = firstNum; i < lastNum + 1; i++) {
        if (i == curPageNo) {
            html.push(`<a class='page active' href='javascript:${fnClick}(${i});'>${i}</a>`);
        } else {
            html.push(`<a class='page' href='javascript:${fnClick}(${i});'>${i}</a>`);
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
        html.push(`<a class="page_next" href="javascript:${fnClick}(${lastNum + 1});">다음</a>`);
    }

    $('#'+tagId).html(html);

    return false;

};