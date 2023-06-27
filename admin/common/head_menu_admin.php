<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

include_once(_BASEPATH . '/common/head.php');
include_once(_BASEPATH . '/common/login_check.php');
?>
<html lang="ko">
    <!--<![endif]-->

    <body style=”overflow-x:auto;overflow-y:hidden”>
        <div class="head_wrap">
            <!-- TOP Area -->
            <div class="head_menu">
                <ul>
                    <li>
                        <a href="javascript:;" onclick="goCallPage(5, event);">
                            <i class="mte i_group vam"></i>신규 대기/승인
                            <p >(<span id="today_mem_cnt_wait" class="tred">0</span>/<span id="today_tot_mem_reg" class="tblue">0</span>)</p>
                        </a>
                        <a id="a_join" style="margin-top: 5px" onclick="javascript:fnSetSound(this, 'a_join');">
                            <i style="color: blue;" class="mte i_volume_up vam"></i>
                            <audio id="audio_join" src="../assets_admin/audio/join.mp3" />
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="goCallPage(1, event);">
                            <i class="mte i_open_in_browser vam"></i>충전 신청/대기
                            <p >(<span id="charge_cnt_1" class="tred">0</span>/<span id="charge_cnt_2" class="tblue">0</span>)</p>
                        </a>
                        <a id="a_charge" style="margin-top: 5px" onclick="javascript:fnSetSound(this, 'a_charge');">
                            <i style="color: blue;" class="mte i_volume_up vam"></i>
                            <audio id="audio_charge" src="../assets_admin/audio/charge_wait.mp3" />
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="goCallPage(2, event);">
                            <i class="mte i_open_in_new vam"></i>환전 신청/대기
                            <p>(<span id="exchange_cnt_1" class="tred">0</span>/<span id="exchange_cnt_2" class="tblue">0</span>)</p>
                        </a>
                        <a id="a_exchange" style="margin-top: 5px" onclick="javascript:fnSetSound(this, 'a_exchange');">
                            <i style="color: blue;" class="mte i_volume_up vam"></i>
                            <audio id="audio_exchange" src="../assets_admin/audio/exchange_wait.mp3" />
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="goCallPage(3, event);">
                            <i class="mte i_payment vam"></i>상담 신청
                            <p>(<span id="tot_qna_cnt" class="tred">0</span>/<span id="tot_qna_cnt_2" class="tblue">0</span>)</p>
                        </a>
                        <a id="a_consult" style="margin-top: 5px" onclick="javascript:fnSetSound(this, 'a_consult');">
                            <i style="color: blue;" class="mte i_volume_up vam"></i>
                            <audio id="audio_consult" src="../assets_admin/audio/consult_wait.mp3" />
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="goCallPage(4, event);">
                            <i class="mte i_chat_bubble vam"></i>SMS 미확인
                            <p>(<span id="tot_sms_cnt" class="tred">0</span>)</p>
                        </a>
                        <a id="a_sms" style="margin-top: 5px" onclick="javascript:fnSetSound(this, 'a_sms');">
                            <i style="color: blue;" class="mte i_volume_up vam"></i>
                            <audio id="audio_sms" src="../assets_admin/audio/sms_wait.mp3" />
                        </a>
                    </li>
                </ul>
            </div>
            <div class="head_log">
                <table>
                    <tr>
                        <th>전체회원</th>
                        <th>금일 가입</th>
                        <th>금일 탈퇴</th>
                        <th>금일 충전</th>
                        <th>금일 환전</th>
                        <th>미정산 경기수</th>
                        <th>최대배팅 알림</th>
                    </tr>
                    <tr>
                        <td id="tot_mem_cnt">0</td>
                        <td id="today_mem_cnt_reg">0</td>
                        <td id="today_mem_cnt_leave">0</td>
                        <td id="today_tot_money_ch" class="tred">0</td>
                        <td id="today_tot_money_ex" class="tblue">0</td>
                        <td id="tot_not_calculate_cnt"><a href="/sports_w/prematch_betting_list.php?betting_key=7">0</a></td>
                        <td id="#" class="tred">0</td>
                    </tr>
                </table>
            </div>

            <div class="head_btn">
                <table>
                    <tr>
                        <td><a href="javascript:;" onclick="goLogout();"><i class="mte i_power_settings_new vam"></i>나가기</a></td>
                    </tr>
                </table>
            </div>
            <div class="tinfo_wrap">
              
                <?php
                if (isset($_SESSION) && isset($_SESSION['anick'])) {
                    ?>
                    <div class="t_link"><?= $_SESSION['anick'] ?></div>
                <?php } else { ?>
                    <div class="t_link">test</div>
                <?php } ?>
            </div>
            <!-- END TOP Area -->
        </div>
    </body>
</html>
<script language='javascript'>
    $(document).ready(function () {
        // chrome 정책 우회 처리용
        $('#tot_mem_cnt').trigger('click');

        reLoadRequest(true);
        //window.setTimeout('window.location.reload()',30000); //30초마다 리플리쉬 시킨다 1000이 1초가 된다.
        var timerId = 0;
        timerId = setInterval("reLoadRequest()", 20000);

        let a_join = sessionStorage.getItem("a_join");
        if (a_join !== undefined && null != a_join) {
            let j_a_join = JSON.parse(a_join);
            //console.log("j_a_join", j_a_join);

            let icon = $('#a_join').children()[0];
            let audio = $('#a_join').children()[1];

            if (true == j_a_join['is_on']) {
                $(icon).attr('style', 'color: blue;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_up vam');

                //$(audio).trigger('play');
                audio.muted = false;
                console.log("ready j_a_join play");

            } else {
                $(icon).attr('style', 'color: red;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_off vam');

                //$(audio).trigger('pause');
                //$(audio).prop('currentTime', 0);
                audio.muted = true;
                console.log("ready j_a_join pause");
            }

        }
        
        let a_charge = sessionStorage.getItem("a_charge");
        if (a_charge !== undefined && null != a_charge) {
            let j_a_charge = JSON.parse(a_charge);
            //console.log("j_a_charge", j_a_charge);

            let icon = $('#a_charge').children()[0];
            let audio = $('#a_charge').children()[1];

            if (true == j_a_charge['is_on']) {
                $(icon).attr('style', 'color: blue;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_up vam');

                //$(audio).trigger('play');
                audio.muted = false;
            } else {
                $(icon).attr('style', 'color: red;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_off vam');

                //$(audio).trigger('pause');
                //$(audio).prop('currentTime', 0);
                audio.muted = true;
            }

        }
        
          let a_exchange = sessionStorage.getItem("a_exchange");
        if (a_exchange !== undefined && null != a_exchange) {
            let j_a_exchange = JSON.parse(a_exchange);
            //console.log("j_a_exchange", j_a_exchange);

            let icon = $('#a_exchange').children()[0];
            let audio = $('#a_exchange').children()[1];

            if (true == j_a_exchange['is_on']) {
                $(icon).attr('style', 'color: blue;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_up vam');

                //$(audio).trigger('play');
                audio.muted = false;
                //console.log("j_a_exchange", 'play' + ' is_on' + j_a_exchange['is_on']);
            } else {
                $(icon).attr('style', 'color: red;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_off vam');

                //$(audio).trigger('pause');
                //$(audio).prop('currentTime', 0);
                //console.log("j_a_exchange", 'pause' + ' is_on' + j_a_exchange['is_on']);
                audio.muted = true;
            }

        }
        
        let a_consult = sessionStorage.getItem("a_consult");
        if (a_consult !== undefined && null != a_consult) {
            let j_a_consult = JSON.parse(a_consult);
            //console.log("j_a_consult", j_a_consult);

            let icon = $('#a_consult').children()[0];
            let audio = $('#a_consult').children()[1];

            if (true == j_a_consult['is_on']) {
                $(icon).attr('style', 'color: blue;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_up vam');

                //$(audio).trigger('play');
                audio.muted = false;
            } else {
                $(icon).attr('style', 'color: red;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_off vam');

                //$(audio).trigger('pause');
                //$(audio).prop('currentTime', 0);
                audio.muted = true;
            }

        }
        
          let a_sms = sessionStorage.getItem("a_sms");
        if (a_sms !== undefined  && null != a_sms) {
            let j_a_sms = JSON.parse(a_sms);
            //console.log("j_a_sms", j_a_sms);

            var icon = $('#a_sms').children()[0];
            var audio = $('#a_sms').children()[1];

            if (true == j_a_sms['is_on']) {
                $(icon).attr('style', 'color: blue;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_up vam');

                //$(audio).trigger('play');
                audio.muted = false;
            } else {
                $(icon).attr('style', 'color: red;');
                $(icon).removeClass();
                $(icon).attr('class', 'mte i_volume_off vam');

                //$(audio).trigger('pause');
                //$(audio).prop('currentTime', 0);
                audio.muted = true;
            }

        }
     
    });

    function goLogout() {
        var str_msg = "로그아웃 하시겠습니까?";
        var result = confirm(str_msg);

        if (result) {
            parent.location.href = "/login_w/logout.php";
        }
    }

    function fnSetSound(o, type) {
        var icon = $(o).children()[0];
        var audio = $(o).children()[1];

        if ($(icon).hasClass('i_volume_off')) {
            // sound on
            $(icon).attr('style', 'color: blue;');
            $(icon).removeClass();
            $(icon).attr('class', 'mte i_volume_up vam');

            $(audio).trigger('play');
            audio.muted = false;
            sessionStorage.removeItem(type);
            sessionStorage.setItem(type, JSON.stringify({is_on: true, object: o}));
            let data = sessionStorage.getItem(type);
            let j_data = JSON.parse(data);
            console.log('on_' + type, j_data);
             
        } else {
            // sound off
            $(icon).attr('style', 'color: red;');
            $(icon).removeClass();
            $(icon).attr('class', 'mte i_volume_off vam');

            $(audio).trigger('pause');
            $(audio).prop('currentTime', 0);
            audio.muted = true;
            sessionStorage.removeItem(type);
            sessionStorage.setItem(type, JSON.stringify({is_on: false, object: o}));
            
            let data = sessionStorage.getItem(type);
            let j_data = JSON.parse(data);
            console.log('off_' + type, j_data);
        }


    }

    //let currentPage = '';
    function reLoadRequest(is_load = false) {
        admNowCallRequest(is_load);
    }

    function goCallPage(ckind, e) {
        if (ckind == '1') {
            if (e.ctrlKey && e.type == "click")
            {
                window.open("/money_w/charge_list.php");
                e.preventDefault();
                return;
            }
            parent.location.href = "/money_w/charge_list.php";
        } else if (ckind == '2') {
            if (e.ctrlKey && e.type == "click")
            {
                window.open("/money_w/exchange_list.php");
                e.preventDefault();
                return;
            }
            parent.location.href = "/money_w/exchange_list.php";
        } else if (ckind == '3') {
            if (e.ctrlKey && e.type == "click")
            {
                window.open("/board_w/service_center_list.php");
                e.preventDefault();
                return;
            }
            parent.location.href = "/board_w/service_center_list.php";
        } else if (ckind == '4') {
            if (e.ctrlKey && e.type == "click")
            {
                window.open("/money_w/charge_sms_list.php");
                e.preventDefault();
                return;
            }
            parent.location.href = "/money_w/charge_sms_list.php";
        } else if (ckind == '5') {
            if (e.ctrlKey && e.type == "click")
            {
                window.open("/member_w/mem_list.php?srch_status=11");
                e.preventDefault();
                return;
            }
            parent.location.href = "/member_w/mem_list.php?srch_status=11";
        }
    }


    function admNowCallRequest(is_load = false) {
        //console.log('admNowCallRequest');
      
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/common/_now_call_check_renew.php',
            success: function (result) {
                if (result['retCode'] == "1000") {
                    /*let before_charge_cnt_1 = $('#charge_cnt_1').text();
                     let before_exchange_cnt_1 = $('#exchange_cnt_1').text();
                     let before_qna_cnt = $('#tot_qna_cnt').text();
                     let before_sms_cnt = $('#tot_sms_cnt').text();*/
                    let today_join_cnt = Number(result['today_tot_mem_wait']) + Number(result['today_tot_mem_reg']);

                    document.getElementById("today_mem_cnt_wait").innerHTML = result['today_tot_mem_wait']; // 신규 대기
                    document.getElementById("today_tot_mem_reg").innerHTML = result['today_tot_mem_reg']; // 신규 승인 

                    document.getElementById("charge_cnt_1").innerHTML = result['charge_cnt_1']; // 충전 신청 
                    document.getElementById("charge_cnt_2").innerHTML = result['charge_cnt_2']; // 충전 대기
 
                    document.getElementById("exchange_cnt_1").innerHTML = result['exchange_cnt_1']; //환전 신청
                    document.getElementById("exchange_cnt_2").innerHTML = result['exchange_cnt_2']; //환전 대기 

                    document.getElementById("tot_qna_cnt").innerHTML = result['tot_qna_cnt'];     // 상담 당일 답변 미완료
                    document.getElementById("tot_qna_cnt_2").innerHTML = result['tot_qna_cnt_2']; // 상담 당일 답변 완료

                    document.getElementById("tot_sms_cnt").innerHTML = result['tot_sms_cnt']; // sms 미확인 

                    document.getElementById("tot_mem_cnt").innerHTML = result['tot_mem_cnt']; // 전체 회원 
                    document.getElementById("today_mem_cnt_reg").innerHTML = today_join_cnt.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 금일 가입
                    document.getElementById("today_mem_cnt_leave").innerHTML = result['today_tot_mem_leave']; // 금일 탈퇴
                    document.getElementById("today_tot_money_ch").innerHTML = result['today_tot_money_ch']; // 금일 충전 
                    document.getElementById("today_tot_money_ex").innerHTML = result['today_tot_money_ex']; // 금일 환전 

                    // 신규가입 사운드
                    //console.log('tot_mem_cnt_sound : ' + result['tot_mem_cnt_sound']);
                    if (!is_load && 0 < Number(result['tot_mem_cnt_sound'])) {
                        var icon = $('#a_join').children()[0];
                        var audio = $('#a_join').children()[1];

                        if ($(icon).hasClass('i_volume_up')) {
                            $(audio).trigger('play');
                            console.log('admNowCallRequest a_join tot_mem_cnt_sound' + Number(result['tot_mem_cnt_sound']));
                        }
                    }

                    // 충전 사운드
                    if (!is_load && 0 < Number(result['tot_charge_cnt'])) {
                        var icon = $('#a_charge').children()[0];
                        var audio = $('#a_charge').children()[1];

                        if ($(icon).hasClass('i_volume_up')) {
                            $(audio).trigger('play');
                            
                            console.log('admNowCallRequest a_charge tot_mem_cnt_sound' + Number(result['tot_mem_cnt_sound']));
                        }
                    }

                    // 환전 사운드
                    if (!is_load && 0 < Number(result['tot_exchange_cnt'])) {
                        var icon = $('#a_exchange').children()[0];
                        var audio = $('#a_exchange').children()[1];

                        if ($(icon).hasClass('i_volume_up')) {
                            $(audio).trigger('play');
                            console.log('admNowCallRequest a_exchange tot_mem_cnt_sound' + Number(result['tot_mem_cnt_sound']));
                        }
                    }

                    // 상담신청 사운드
                    if (!is_load && 0 < Number(result['tot_qna_cnt_sound'])) {
                        var icon = $('#a_consult').children()[0];
                        var audio = $('#a_consult').children()[1];

                        if ($(icon).hasClass('i_volume_up')) {
                            $(audio).trigger('play');
                            console.log('admNowCallRequest a_consult tot_mem_cnt_sound' + Number(result['tot_mem_cnt_sound']));
                        }
                    }

                    // 자동충전 미처리
                    /*if (!is_load && Number(before_sms_cnt) > Number(result['tot_sms_cnt'])) {
                     var icon = $('#a_sms').children()[0];
                     var audio = $('#a_sms').children()[1];
                     
                     if ($(icon).hasClass('i_volume_up')) {
                     $(audio).trigger('play');
                     }
                     }*/

                    return;
                } else if (result['retCode'] == "2001") {
                    parent.location.href = "/login.php?msg=1";
                } else if (result['retCode'] == "2002") {
                    parent.location.href = "/login.php?msg=2";
                } else {
                    //alert('입력 값을 확인해 주세요.');
                    // '0' 으로 처리
                    document.getElementById("charge_cnt_1").innerHTML = '-1';
                    document.getElementById("charge_cnt_2").innerHTML = '-1';
                    document.getElementById("exchange_cnt_1").innerHTML = '-1';
                    document.getElementById("exchange_cnt_2").innerHTML = '-1';
                    document.getElementById("tot_qna_cnt").innerHTML = '-1';
                    document.getElementById("tot_sms_cnt").innerHTML = '-1';

                    document.getElementById("tot_mem_cnt").innerHTML = '-1';
                    document.getElementById("today_mem_cnt_reg").innerHTML = '-1';
                    document.getElementById("today_mem_cnt_leave").innerHTML = '-1';
                    document.getElementById("today_tot_money_ch").innerHTML = '-1';
                    document.getElementById("today_tot_money_ex").innerHTML = '-1';
                    return;
                }
            },
            error: function (request, status, error) {
                document.getElementById("charge_cnt_1").innerHTML = '-1';
                document.getElementById("charge_cnt_2").innerHTML = '-1';
                document.getElementById("exchange_cnt_1").innerHTML = '-1';
                document.getElementById("exchange_cnt_2").innerHTML = '-1';
                document.getElementById("tot_qna_cnt").innerHTML = '-1';
                document.getElementById("tot_sms_cnt").innerHTML = '-1';

                document.getElementById("tot_mem_cnt").innerHTML = '-1';
                document.getElementById("today_mem_cnt_reg").innerHTML = '-1';
                document.getElementById("today_mem_cnt_leave").innerHTML = '-1';
                document.getElementById("today_tot_money_ch").innerHTML = '-1';
                document.getElementById("today_tot_money_ex").innerHTML = '-1';
                return;
            }
        });
    }
</script>