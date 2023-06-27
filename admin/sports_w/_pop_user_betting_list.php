<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_LIBPATH.'/class_CommonUtil.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
include_once(_BASEPATH.'/common/head.php');


$UTIL = new CommonUtil();


$fixture_id = trim(isset($_GET['fixture_id']) ? $_GET['fixture_id'] : 0);
$markets_id = trim(isset($_GET['markets_id']) ? $_GET['markets_id'] : 0);
$bet_id = trim(isset($_GET['bet_id']) ? $_GET['bet_id'] : '');
$bet_type = trim(isset($_GET['bet_type']) ? $_GET['bet_type'] : 0);
$fixture_start_date = trim(isset($_GET['fixture_start_date']) ? $_GET['fixture_start_date'] : 0);


$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();




?>
<body>
<div class="panel reserve">
    <div class="tline">
        <table class="mlist separate_table">
            <tr>
                <th width="6%">번호</th>
                <th width="8%">아이디</th>
                <th width="10%">닉네임</th>
                <th width="5%">게임수</th>
                <th width="10%">배팅진행내역</th>
                <th width="8%">배당율(보너스)</th>
                <th width="5%">배팅액</th>
                <th width="6%">예상당첨액</th>
                <th width="5%">적중금</th>
                <th width="14%">배팅시간</th>
                <th width="10%">결과</th>
                <th width="10%">기능</th>
            </tr>
        </table>
    </div>
    <div class="tline">
        <table class="mlist separate_table">
            <tr>
                <td width="6%">223</td>
                <td width="8%"><a href="javascript:;">jake2</a></td>
                <td width="10%"><a href="javascript:;">제잌총판테스트</a></td>
                <td width="5%">1</td>
                <td width="10%" onclick="open_betting_detail(236,1)"><a class="btn h30 btn_while" style="width: 25px; padding: 0px; margin: 0px; border: 1px solid black; color:black">승</a></td>
                <td width="8%">1.60(1)</td>
                <td width="5%" style="text-align:right;">300,000</td>
                <td width="6%" style="text-align:right;">480,000</td>
                <td width="5%" style="text-align:right; color: red">300,000</td>
                <td width="14%">2021-12-08 13:30:41</td>
                <td width="10%">관리자 취소</td>
                <td width="10%"><a class="btn h30 btn_gray">취소</a></td>
            </tr>
        </table>
    </div>

    <div class="tline" id="betting_detail_236" style="display: none;">
        <table class="mlist">
            <tr>
                <th>경기시간</th>
                <th>리그</th>
                <th>홈</th>
                <th>VS</th>
                <th>원정</th>
                <th>타입</th>
                <th>베팅</th>
                <th>게임결과</th>
                <th>결과변경</th>
            </tr>
            <tr>
                <td>2021-12-08 19:00:00</td>
                <td>V리그</td>
                <td><a href="#">대전 블루팡스</a></td>
                <td>1.63</td>
                <td><a href="#">인천 대한항공 점보스 배구단</a></td>
                <td>승패</td>
                <td>패(1.63)</td>
                <td>2:3</td>
                <td><a href="javascript:onBetCancel(236,3)" class="btn h30 btn_blu">결과변경</a></td>
            </tr>
        </table>
    </div>

    <div class="tline">
        <table cellspacing="0" class="mlist">
            <thead>
                <th>경기번호</th>
                <th>시간</th>
                <th>배팅명</th>
                <th>아이디</th>
                <th>닉네임</th>
                <th>배팅금액</th>
                <th>획득액</th>
            </thead>
            <tbody id="tbody">
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    //App.init();
    //FormPlugins.init();

    getBettingUserList(<?=$fixture_id?>, <?=$markets_id?>, '<?=$bet_id?>', <?=$bet_type?>, '<?=$fixture_start_date?>');
});

const getBettingUserList = function(fixture_id, markets_id, bet_id, bet_type, fixture_start_date){
    /*console.log('fixture_id : ' + fixture_id);
    console.log('markets_id : ' + markets_id);
    console.log('bet_id : ' + bet_id);
    console.log('bet_type : ' + bet_type);
    console.log('fixture_start_date : ' + fixture_start_date);*/
    var param_url = '/sports_w/_sports_menu_bet_detail_list_ajax.php';
    $.ajax({
            type: 'post',
            dataType: 'json',
        url: param_url,
        data:{'fixture_id':fixture_id,'markets_id':markets_id,'bet_id':bet_id,'bet_type':bet_type,'fixture_start_date':fixture_start_date},
        success: function (data) {
            if(data['retCode'] == "1000"){
                //console.log(data);
                let html = '';
                for (const[key, value] of Object.entries(data['data'])) {
                    html += "<tr>";
                    html += "<td>"+fixture_id+"</td>";
                    html += "<td>"+value['fixture_start_date']+"</td>";
                    html += "<td>"+value['ls_markets_name']+"</td>";
                    html += "<td>"+value['id']+"</td>";
                    html += "<td>"+value['nick_name']+"</td>";
                    html += "<td>"+value['total_bet_money']+"</td>";
                    html += "<td>"+value['take_money']+"</td>";
                    html += "</tr>"
                }
                console.log(html);
                $("#tbody").append(html);
            }
            else {
                alert('실패 하였습니다.');
            }
        },
        error: function (request, status, error) {
            alert('서버 오류 입니다.');
        }
    });
}

// 테이블 토글 
const open_betting_detail = function (idx, is_open) {
    idx = +idx;
    let display = document.getElementById('betting_detail_' + idx).style.display;
    if (display == 'none') {
        document.getElementById('betting_detail_' + idx).style.display = 'block';
    } else {
        document.getElementById('betting_detail_' + idx).style.display = 'none';
    }

    if (!is_open) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_menu_10_update_is_open.php',
            data: {idx: idx},
            success: function (result) {
                $('#open_betting_detail_' + idx).attr("onclick", "open_betting_detail(" + idx + ",1)");
                $('#open_betting_detail_' + idx).attr("onclick", "open_betting_detail(" + idx + ",1)");
            },
            error: function (data, status, error) {
                console.log(JSON.stringify(data));
                console.log(`req : ` + data);
                console.log(`status : ` + status);
                console.log(`error: ` + error);
                alert('상세내역이 없습니다');
                return;
            }
        });
    }
}

</script>
</body>
</html>