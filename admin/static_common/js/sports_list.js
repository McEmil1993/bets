function allCheckFunc(obj) {
    $("[name=chk]").prop("checked", $(obj).prop("checked"));
}

function betOnOffBtnClick(ateg, fixture_id, status, fixture_start_date, bet_type) {
    console.log(fixture_start_date);

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/sports_w/_sports_menu_ajax.php',
        data: {'cmd': 'bet_' + status, 'fixture_id': fixture_id, 'bet_type': bet_type, 'fixture_start_date': fixture_start_date},
        success: function (result) {
            console.log(result['retCode']);
            if (result['retCode'] == "1000") {
                alert('업데이트 되었습니다.');

                if ("on" == status || "ON" == status) {
                    $(ateg).removeClass("btn_gray").removeClass("btn_green").addClass("btn_green");
                    $(ateg).attr("onclick", "betOnOffBtnClick(this," + fixture_id + ", 'off', '" + fixture_start_date + "'," + bet_type + ")");
                    $(ateg).text("ON");
                } else {
                    $(ateg).removeClass("btn_green").removeClass("btn_gray").addClass("btn_gray");
                    $(ateg).attr("onclick", "betOnOffBtnClick(this," + fixture_id + ", 'on', '" + fixture_start_date + "'," + bet_type + ")");
                    $(ateg).text("OFF");
                }
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


// 전체수정
function fn_all_update_menu5(bet_type, url) {

    let arrFixturesData = [];
    let arrFixturesStartDate = [];
    let fix_status_id = $('#fix_status_list').val();
    $('#fixtures_tbody tr').each(function (index, tr) {

        if (true == $("#checkbox_css_" + index).is(':checked')) {

            let fixture_id = tr.cells[1].innerHTML;
            let fixture_start_date = tr.cells[2].innerHTML;

            arrFixturesData.push(fixture_id);
            arrFixturesStartDate.push(fixture_start_date);
            // alert(fixture_id);
        }
    });
    let strFixturesData = JSON.stringify(arrFixturesData);
    let strFixturesStartDate = JSON.stringify(arrFixturesStartDate);
    var str_msg = '체크된 목록을 수정하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_status_update_all.php',
            data: {strFixturesData: strFixturesData, status: fix_status_id, bet_type: bet_type, strFixturesStartDate: strFixturesStartDate},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('수정하였습니다.');
                    //location.href = `/sports_w/prematch_manager.php?s_id=&l_id=&bs_id=${fix_status_id}&tn=&fix_id=`;
                    location.href = `/sports_w/` + url + `?s_id=&l_id=&bs_id=${fix_status_id}&tn=&fix_id=`;
                    //return;
                } else {
                    alert(result['retMsg']);
                    //return;
                }
                window.location.reload();
            },
            error: function (request, status, error) {
                alert('수정에 실패하였습니다.');
                return;
            }
        });
    }
}

// 전체 수동관리
function fn_all_update_passivity_manager(bet_type,flag, url) {

    let arrFixturesData = [];

    let fix_status_id = $('#fix_status_list').val();
    $('#fixtures_tbody tr').each(function (index, tr) {

        if (true == $("#checkbox_css_" + index).is(':checked')) {

            let fixture_id = tr.cells[1].innerHTML;
            arrFixturesData.push(fixture_id);

            alert(fixture_id);
        }
    });
    let strFixturesData = JSON.stringify(arrFixturesData);

    var str_msg = '체크된 목록을 수정하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_passivity_flag_update_all.php',
            data: {'strFixturesData': strFixturesData, 'bet_type': bet_type,'flag' : flag},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('수정하였습니다.');
                    //location.href = `/sports_w/prematch_manager.php?s_id=&l_id=&bs_id=${fix_status_id}&tn=&fix_id=`
                    location.href = `/sports_w/` + url + `?s_id=&l_id=&bs_id=${fix_status_id}&tn=&fix_id=`;
                    //window.location.reload();
                    //return;
                } else {
                    alert(result['retMsg']);
                    //return;
                }
                window.location.reload();
                
            },
            error: function (request, status, error) {
                alert('수정에 실패하였습니다.');
                return;
            }
        });
    }
}

function fn_On_chnage(url)
{
    let sport_id = $('#sport_list').val();
    let league_id = $('#league_list').val();
    let location_list = $('#location_list').val();
    //alert(league_id);
    let bet_status_id = $('#bet_status_list').val();
    let team_name = $('#team_name').val();
    let srch_s_date = $('#datepicker-default').val();
    //alert(sport_id + ' ' + league_id + ' ' + bet_status_id + ' ' + team_name);

    //location.href = '/sports_w/prematch_manager.php?s_id=' + sport_id + '&location_id=' + location_list + '&l_id=' + league_id + '&bs_id=' + bet_status_id + '&tn=' + team_name + '&srch_s_date=' + srch_s_date;
    location.href = '/sports_w/' + url + '?s_id=' + sport_id + '&location_id=' + location_list + '&l_id=' + league_id + '&bs_id=' + bet_status_id + '&tn=' + team_name + '&srch_s_date=' + srch_s_date;
}

function fn_On_detail(url)
{
    const page = $('#page').val();
    const srch_s_date = $('#datepicker-default').val();
    const sport_id = $('#sport_list').val();
    const location_id = $('#location_list').val();
    const league_id = $('#league_list').val();
    const bet_status_id = $('#bet_status_list').val();
    const fix_id = $('#fix_id').val();
    const team_name = $('#team_name').val();
    const fix_status = $('#fix_status_list').val();

    let previous_page_data_url
            = "&previous_page=" + page
            + "&previous_srch_date=" + encodeURIComponent(srch_s_date)
            + "&previous_sport_id=" + sport_id
            + "&previous_location_id=" + location_id
            + "&previous_league_id=" + league_id
            + "&previous_bet_status_id=" + bet_status_id
            + "&previous_fix_id=" + encodeURIComponent(fix_id)
            + "&previous_team_name=" + encodeURIComponent(team_name)
            + "&previous_fix_status=" + fix_status;

    url = url + previous_page_data_url

    location.href = url;
}