function setSecondPassword(secondPassword){
    sessionStorage.clear();
    
    sessionData = [];
    let obj = {};
    obj['secondTime'] = new Date();
    obj['secondPassword'] = secondPassword;
    sessionData.push(obj);
    sessionStorage.setItem('secondPassword', JSON.stringify(sessionData));
}

function getSecondPassword(){
    let sessionData = sessionStorage.getItem("secondPassword");
    //console.log(sessionData);
    let obj = {};
    let secondPassword = '';
    
    if(null !== sessionData){
        sessionData = JSON.parse(sessionData);
        const today = new Date();
        const timeValue = new Date(sessionData[0]['secondTime']);
        const betweenTime = Math.floor((today.getTime() - timeValue.getTime()) / 1000);
        
        // 시간이 지났다.
        if (300 < betweenTime) {
            secondPassword = '';
        }else{
            secondPassword = sessionData[0]['secondPassword']
        }
    }
    
    return secondPassword;
}

function delSecondPassword(){
    sessionStorage.clear();
}

function onBtnClickBatchApplication(fixture_sport_id, fixture_start_date, fixture_id, bet_type) {

    let markets_name = $('#markets_name').val();
    let live_results_p1 = $('#live_results_p1').val();
    let live_results_p2 = $('#live_results_p2').val();

    if (markets_name == null || markets_name == "") {
        alert('마켓타입을 입력하세요.');
        return;
    }

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);

    alert(second_pass);
    if (live_results_p1 == null || live_results_p1 == "" || live_results_p2 == null || live_results_p2 == "" || fixture_sport_id == null || fixture_sport_id == "") {
        alert('점수 정보를 입력하세요.');
        return;
    }

    // 전송
    var str_msg = '일괄적용 하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_batch_application_ajax.php',
            data: {'second_pass': second_pass, 'sport_id': fixture_sport_id, 'fixture_start_date': fixture_start_date, 'fixture_id': fixture_id, 'markets_name': markets_name, 'bet_type': bet_type, 'live_results_p1': live_results_p1, 'live_results_p2': live_results_p2},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {

                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('호출실패');
                alert(error);
                alert(status);
                return;
            }
        });
    }

}
// 전체 정산 배팅 
function onBtnClickTotalCalculate(fixture_sport_id, fixture_start_date, fixture_id, bet_type) {
    //alert('전체정산 금지');    
    //return;
    let markets_name = $('#markets_name').val();

    if (fixture_sport_id == null || fixture_sport_id == "") {
        alert('스포츠 정보가 잘못되었습니다..');
        return;
    }

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);
    
    // 전송
    let str_msg = '전체정산 하시겠습니까?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_total_re_calculate_ajax.php',
            data: {'second_pass': second_pass, 'sport_id': fixture_sport_id, 'fixture_start_date': fixture_start_date, 'fixture_id': fixture_id, 'bet_type': bet_type, 'markets_name': markets_name},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    delSecondPassword();
                    $('#second_pass').val('');
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.' + error);
                return;
            }
        });
    }

}
// 전체 마감 전
function onBtnClickTotalBeforeCalculate(fixture_id, bet_type) {
    alert('전체 마감전..!!!');    
  
    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);
    
    // 전송
    let str_msg = '전체를 마감 전으로 하시겠습니까?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_total_before_calculate_ajax.php',
            data: {'second_pass': second_pass, 'fixture_id': fixture_id, 'bet_type': bet_type},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    delSecondPassword();
                    $('#second_pass').val('');
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.' + error);
                return;
            }
        });
    }
}

// 개별 정산
function onBtnClickCalculate(idx, fixture_start_date, fixture_id, bet_type, markets_id, bet_base_line, admin_bet_status) {
    // if ('ON' == admin_bet_status) {
    //     alert('수동 정산 모드로 바꿔주세요.');
    //     return;
    // }

    let str_bet_result_p1 = '#bet_result_p1_' + idx;
    let bet_result_p1 = $(str_bet_result_p1).val();

    let str_bet_result_p2 = '#bet_result_p2_' + idx;
    let bet_result_p2 = $(str_bet_result_p2).val();

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);

    let str_bet_result_2_p1 = '#bet_result_2_p1_' + idx;
    let bet_result_2_p1 = $(str_bet_result_2_p1).val();

    let str_bet_result_2_p2 = '#bet_result_2_p2_' + idx;
    let bet_result_2_p2 = $(str_bet_result_2_p2).val();


    // 전송
    let str_msg = '정산 하시겠습니까?';
    let result = confirm(str_msg);

    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_re_calculate_ajax.php',
            data: {'second_pass': second_pass, 'fixture_start_date': fixture_start_date, 'bet_type': bet_type, 'fixture_id': fixture_id, 'markets_id': markets_id, 'bet_base_line': bet_base_line,
                'bet_result_p1': bet_result_p1, 'bet_result_p2': bet_result_p2, 'bet_result_2_p1': bet_result_2_p1, 'bet_result_2_p2': bet_result_2_p2},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    delSecondPassword();
                    $('#second_pass').val('');
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.');
                return;
            }
        });
    }
}
// 개별 마감 전 
function onBtnClickBeforeCalculate(fixture_id, bet_type, markets_id, bet_base_line, admin_bet_status) {
     if ('ON' == admin_bet_status) {
         alert('수동 정산 모드로 바꿔주세요.');
         return;
     }
    

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);

    // 전송
    let str_msg = '해당 마켓을 마감 전으로 하시겠습니까?';
    let result = confirm(str_msg);

    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_before_calculate_ajax.php',
            data: {'second_pass': second_pass,'bet_type': bet_type, 'fixture_id': fixture_id, 'markets_id': markets_id, 'bet_base_line': bet_base_line},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    delSecondPassword();
                    $('#second_pass').val('');
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.');
                return;
            }
        });
    }
}
// 전체 적특
function onBtnClickTotalHitException(fixture_sport_id, fixture_start_date, fixture_id, bet_type) {

    let markets_name = $('#markets_name').val();

    if (fixture_sport_id == null || fixture_sport_id == "") {
        alert('스포츠 정보가 잘못되었습니다..');
        return;
    }

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);

    // 전송
    var str_msg = '적특 하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_total_hit_exception_ajax.php',
            data: {'second_pass': second_pass, 'sport_id': fixture_sport_id, 'fixture_start_date': fixture_start_date, 'fixture_id': fixture_id, 'bet_type': bet_type, 'markets_name': markets_name},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    delSecondPassword();
                    $('#second_pass').val('');
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.');
                return;
            }
        });
    }
}
// 개별 적특 
function onBtnClickIndividualHitException(fixture_start_date, fixture_id, bet_type, markets_id, bet_base_line, admin_bet_status) {
    if ('ON' == admin_bet_status) {
        alert('수동 정산 모드로 바꿔주세요.');
        return;
    }

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);

    // 전송
    var str_msg = '적특 하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_detail_hit_exception_ajax.php',
            data: {'second_pass': second_pass, 'fixture_start_date': fixture_start_date, 'fixture_id': fixture_id, 'bet_type': bet_type, 'markets_id': markets_id, 'bet_base_line': bet_base_line},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    delSecondPassword();
                    $('#second_pass').val('');
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.');
                alert(error);
                return;
            }
        });
    }
}

function betOnOffBtnClick(fixture_start_date, fixture_id, bet_type, markets_id, bet_base_line, status) {
    console.log(fixture_start_date);

    let second_pass = $('#second_pass').val();
    if (second_pass == null || second_pass == "") {
        alert('2차 비번을 입력하세요');
        return;
    }
    setSecondPassword(second_pass);

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/sports_w/_sports_bet_menu_ajax.php',
        data: {'second_pass': second_pass, 'cmd': 'bet_' + status, 'fixture_id': fixture_id, 'bet_type': bet_type, 'fixture_start_date': fixture_start_date, 'markets_id': markets_id, 'bet_base_line': bet_base_line},
        success: function (result) {
            console.log(result['retCode']);
            if (result['retCode'] == "1000") {
                alert('업데이트 되었습니다.');
                window.location.reload();

                return;
            } else {
                delSecondPassword();
                $('#second_pass').val('');
                alert(result['retMsg']);
            }
        },
        error: function (request, status, error) {
            alert('업데이트 실패 (2)');

            return;
        }
    });
}

function fn_cancel(bet_idx) {
    var str_msg = '취소하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/mini_game_w/_mini_game_bet_cancel.php',
            data: {'bet_idx': bet_idx},
            success: function (result) {
                console.log(result);
                if (result['retCode'] == "1000") {
                    alert('취소하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert('취소에 실패하였습니다.');
                window.location.reload();
                return;
            }
        });
    } else {
        return;
    }
}

function on_modify_game_start_date(fixture_id, bet_type) {

    if (fixture_id == null || bet_type == "") {
        alert('인자값 오류');
        return;
    }

    let game_start_date = $('#game_start_date').val();
    if (null == game_start_date) {
        alert('수정할 값을 입력하시오');
        return;
    }


    let str_msg = game_start_date + '수정할건가요 ?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_passivity_modify_game_start_date.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type, 'game_start_date': game_start_date},
            success: function (result) {
                console.log(result);
                if (result['retCode'] == "1000") {
                    alert('성공하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert('수정에 실패하였습니다.');
                window.location.reload();
                return;
            }
        });
    } else {
        return;
    }

}

function on_modify_game_start_date_release(fixture_id, bet_type) {

    if (fixture_id == null || bet_type == "") {
        alert('인자값 오류');
        return;
    }

    let str_msg = '해제할건가요?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_passivity_modify_game_start_date_release.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type},
            success: function (result) {
                console.log(result);
                if (result['retCode'] == "1000") {
                    alert('성공하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert('해제에 실패하였습니다.');
                window.location.reload();
                return;
            }
        });
    } else {
        return;
    }

}
// 수종 적용 
function onClickAplyMnlDvdn(idx, betData, fixture_id, bet_type, markets_id, bet_base_line) {

    //let second_pass = $('#second_pass').val();
    //if (second_pass == null || second_pass == "") {
    //    alert('2차 비번을 입력하세요');
    //    return;
    //}

    let arrData = betData.split(',');

    let arrBetData = new Array();
    for (let value of arrData) {

        let str_ps_bet_price = '#in_bg_' + idx + value;
        let bet_price = $(str_ps_bet_price).val();

        let str_ps_status = '#select_bg_' + idx + value;
        let status = $(str_ps_status).val();

        let objBetData = new Object();
        objBetData.name = value;
        objBetData.price = bet_price;
        objBetData.status = status;
        arrBetData.push(objBetData);
    }

    let j_bet_data = JSON.stringify(arrBetData);
    //alert(j_bet_data);

    // 전송
    let str_msg = '적용하겠습니까?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_passivity_apply_ajax.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type, 'markets_id': markets_id, 'bet_base_line': bet_base_line, 'bet_data': j_bet_data},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.');
                alert(error);
                return;
            }
        });
    }
}
// 수동해제
function onClickPassivityRelease(fixture_id, bet_type, markets_id, bet_base_line) {

    // 전송
    let str_msg = '해제할건가요?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_passivity_release_ajax.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type, 'markets_id': markets_id, 'bet_base_line': bet_base_line},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('해제에 실패하였습니다.');
                alert(error);
                return;
            }
        });
    }
}

function on_modify_game_status(fixture_id, bet_type) {

    if (fixture_id == null || bet_type == "") {
        alert('인자값 오류');
        return;
    }

    let srch_status = $('#srch_status').val();
    if (null == srch_status) {
        alert('수정할 값을 입력하시오');
        return;
    }


    let str_msg = srch_status + '수정할건가요 ?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_passivity_modify_game_status.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type, 'status': srch_status},
            success: function (result) {
                console.log(result);
                if (result['retCode'] == "1000") {
                    alert('성공하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert('수정에 실패하였습니다.');
                window.location.reload();
                return;
            }
        });
    } else {
        return;
    }

}

function on_modify_game_status_release(fixture_id, bet_type) {

    if (fixture_id == null || bet_type == "") {
        alert('인자값 오류');
        return;
    }


    let str_msg = '해제할건가요 ?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_passivity_modify_game_status_release.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type},
            success: function (result) {
                console.log(result);
                if (result['retCode'] == "1000") {
                    alert('성공하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }
            },
            error: function (request, status, error) {
                alert('수정에 실패하였습니다.');
                window.location.reload();
                return;
            }
        });
    } else {
        return;
    }

}

function onClickPassivityAllApply(fixture_id, bet_type, max_count) {
    alert('onClickPassivityAllApply');

    let arrBetData = new Array();
    for (let i = 0; i < max_count; ++i) {
        if (true == $("#checkbox_css_" + i).is(':checked')) {

            let strdata = $("#checkbox_css_" + i).val();

            let arrParentData = strdata.split('^');
            let arrData = arrParentData[1].split(',');

            for (let value of arrData) {

                let str_ps_bet_price = '#in_bg_' + arrParentData[0] + value;
                let bet_price = $(str_ps_bet_price).val();

                let str_ps_status = '#select_bg_' + arrParentData[0] + value;
                let status = $(str_ps_status).val();

                let objBetData = new Object();
                objBetData.name = value;
                objBetData.price = bet_price;
                objBetData.status = status;
                objBetData.market_id = arrParentData[2];
                objBetData.bet_base_line = arrParentData[3];
                arrBetData.push(objBetData);
            }
        }
    }

    let j_bet_data = JSON.stringify(arrBetData);
    alert(j_bet_data);

    // 전송
    let str_msg = '적용하겠습니까?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_passivity_all_apply_ajax.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type, 'bet_data': j_bet_data},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('적용에 실패하였습니다.');
                alert(error);
                return;
            }
        });
    }

}

function onClickPassivityAllRelease(fixture_id, bet_type, max_count) {

    alert('onClickPassivityAllRelease');

    let arrBetData = new Array();
    for (let i = 0; i < max_count; ++i) {
        if (true == $("#checkbox_css_" + i).is(':checked')) {

            let strdata = $("#checkbox_css_" + i).val();
            let arrParentData = strdata.split('^');
            let objBetData = new Object();
            objBetData.market_id = arrParentData[2];
            objBetData.bet_base_line = arrParentData[3];
            arrBetData.push(objBetData);
        }
    }

    let j_bet_data = JSON.stringify(arrBetData);
    alert(j_bet_data);


    // 전송
    let str_msg = '해제할건가요?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_passivity_all_release_ajax.php',
            data: {'fixture_id': fixture_id, 'bet_type': bet_type, 'bet_data': j_bet_data},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('적용하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                }
            },
            error: function (request, status, error) {
                alert('해제에 실패하였습니다.');
                alert(error);
                return;
            }
        });
    }
}

function allCheckFunc(obj) {
    $("[name=chk]").prop("checked", $(obj).prop("checked"));
}