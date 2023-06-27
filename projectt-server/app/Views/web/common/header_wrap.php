<?php 
session()->get('member_idx')
?>

<noscript class="unscript">
        <p align="center" style="padding:10px;">자바스크립트가 비활성화되었습니다. 제대로 작동하려면 활성화하십시오.</p>
</noscript>

<?php
    if ($result_game_config === 1) {
        session_destroy();
        return redirect()->to(base_url("/$viewRoot/index"));
    }
?>

<body id="myAnchor">

<?php 
    $userLevel = strval(session()->get('level') ?? '');
    $userLevelImagePath = '/assets_w/images/lv1.png';

    switch($userLevel) {
        case "1":
        case "2":
        case "3": 
            $userLevelImagePath = '/assets_w/images/lv1.png'; 
            break;
        case "4":
        case "5": 
            $userLevelImagePath = '/assets_w/images/lv4.png'; 
            break;
        case "6":
        case "7": 
            $userLevelImagePath = '/assets_w/images/lv1.png'; 
            break;
        case "8":
        case "9":
        case "10": 
            $userLevelImagePath = '/assets_w/images/lva.png'; 
            break;
    }
?>

<!-- 좌측메뉴 -->
<div class="aside">
    <div class="aside_wrap">
        <div class="aside_top_wrap">
            <div class="aside_top_left"><img src="/assets_w/images/logo.png"></div>
            <div data-dismiss="aside" class="aside_top_right"><img src="/assets_w/images/m_close.png" width="40"></div>
        </div>
        <div class="nav_list_1">
            <span class="menu_acc_c"><a href="/web/sports"><img src="/assets_w/images/dot.png" width="15"> 스포츠</a></span>		
            <span class="menu_acc_c"><a href="/web/realtime"><img src="/assets_w/images/dot.png" width="15"> LIVE스포츠</a></span>		
            <span class="menu_acc_c"><a href="/web/casino?prd_type=C&prd_id=5"><img src="/assets_w/images/dot.png" width="15"> 카지노</a></span>	
            <ul class="menu_accordion">
                <li>
                    <span class="menu_acc_h">
                        <a href="/web/minigame?betType=3"><img src="/assets_w/images/dot.png" width="15"> 미니게임<img src="/assets_w/images/icon_down.png" class="m_game_arrow"></a>
                    </span>
                    <div class="menu_acc_b">
                        <ul class="menu_acc_b_in">
                            <?php if('ON' == config(App::class)->IS_EOS_POWERBALL){ ?>
                                <li><a href="/web/minigame?betType=3"><div class="menu_btn_4">엔트리 EOS 파워볼</div></a></li>
                            <?php } ?>
                            <?php if('ON' == config(App::class)->IS_POWERBALL){ ?>
                                <li><a href="/web/minigame?betType=15"><div class="menu_btn_4">엔트리 파워볼</div></a></li>
                            <?php } ?>
                            <li><a href="/web/minigame?betType=4"><div class="menu_btn_4">엔트리 파워사다리</div></a></li>
                            <li><a href="/web/minigame?betType=5"><div class="menu_btn_4">엔트리 키노사다리</div></a></li>
                            <li><a href="/web/premiumShip"><div class="menu_btn_4">가상축구-프리미어쉽</div></a></li>
                            <li><a href="/web/superLeague"><div class="menu_btn_4">가상축구-슈퍼리그</div></a></li>
                            <li><a href="/web/euroCup"><div class="menu_btn_4">가상축구-유로컵</div></a></li>
                            <li><a href="/web/worldCup"><div class="menu_btn_4">가상축구-월드컵</div></a></li>
                        </ul>
                    </div>
                </li>
            </ul>
            <ul class="menu_accordion">
                <li>
                    <span class="menu_acc_h">
                        <a href="/web/hash?type=R"><img src="/assets_w/images/dot.png" width="15"> 해쉬게임<img src="/assets_w/images/icon_down.png" class="m_game_arrow"></a>
                    </span>
                    <div class="menu_acc_b">
                        <ul class="menu_acc_b_in">
                            <li><a href="/web/hash?type=R"><div class="menu_btn_4">룰렛</div></a></li>
                            <li><a href="/web/hash?type=H"><div class="menu_btn_4">하이로우</div></a></li>
                            <li><a href="/web/hash?type=B"><div class="menu_btn_4">바카라</div></a></li>
                        </ul>
                    </div>
                </li>
            </ul>			
            <span class="menu_acc_c"><a href="/web/slot"><img src="/assets_w/images/dot.png" width="15"> 슬롯게임</a></span>
            <!-- <span class="menu_acc_c"><a href="/esports"><img src="/assets_w/images/dot.png" width="15"> E-스포츠</a></span> -->
            <?php $from_date = date("Y-m-d", strtotime(date('Y-m-d')."- 8 days"))?>                                
            <span class="menu_acc_d"><a href="/web/betting_history?menu=b&bet_group=2&clickItemNum=2&betToDate=<?php echo date("Y-m-d"); ?>&betFromDate=<?=$from_date?>" ><img src="/assets_w/images/dot.png" width="15"> 배팅내역</a></span>		
            <!-- <span class="menu_acc_d"><a href="/web/betting_rules"><img src="/assets_w/images/dot.png" width="15"> 배팅규정</a></span> -->
        </div>	
        
        <script><!--아코디언-->
            (function($) {
            $('.menu_accordion span').click(function(j) {
                var dropDown = $(this).closest('li').find('div.menu_acc_b');
            
                $(this).closest('.menu_accordion').find('div.menu_acc_b').not(dropDown).slideUp();
            
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $(this).closest('.menu_accordion').find('span.active').removeClass('active');
                    $(this).addClass('active');
                }
            
                dropDown.stop(false, true).slideToggle();
            
                j.preventDefault();
            });
            })(jQuery);
        </script> 		
    </div>
</div><!-- 좌측메뉴 -->

<!-- 우측메뉴 -->
<div class="aside2">
    <div class="aside_wrap">
        <div class="aside_top_wrap">
            <div class="aside_top_left"><img src="/assets_w/images/logo.png"></div>
            <div data-dismiss="aside" class="aside_top_right"> <img src="/assets_w/images/m_close.png" width="40"></div>     
        </div> 
        <div class="aside2_box1_wrap">
            <?php if (isset($_SESSION['session_key'])) {?>
            <div class="aside2_box1">
                <table width="100%">
                    <tr>
                        <td><img src="<?= $userLevelImagePath ?>" class="level-icon" alt="level icon" width="20"><span class="font01"> <?= session()->get('nick_name') ?></span>님</td>
                    </tr>
                    <tr>
                        <td><img src="/assets_w/images/my_icon01.png"> 보유머니 <span class="util_money my_font"><?= number_format(session()->get('money') ?? 0) ?></span></td>
                    </tr>
                    <tr>
                        <td><img src="/assets_w/images/my_icon02.png"> 포인트 <span class="my_font"><?= number_format(session()->get('point') ?? 0) ?></span></td>
                    </tr>
                    <tr>
                        <td><img src="/assets_w/images/my_icon03.png"> G머니 <span class="my_font"><?= number_format(session()->get('g_money')) ?></span></td>
                    </tr>
                    <tr>
                        <td><img src="/assets_w/images/my_icon04.png"> 쪽지 <span class="mynotes my_font">(<?= session()->get('tm_unread_cnt') ?>)</span></td>
                    </tr>
                </table>
            </div>
            <?php } ?>
        </div>

        <div class="aside2_box2_wrap">
            <div class="aside2_box2">          
                <table width="100%">
                    <tr>
                        <td><a href="/web/apply?menu=c"><span class="aside_btn1">충전하기</span></a></td>
                        <td><a href="/web/exchange"><span class="aside_btn1">환전하기</span></a></td>
                    </tr>   
                    <tr>
                        <td><a href="/web/gamble_shop"><span class="aside_btn1">불스마켓</span></a></td>
                        <td><a href="/web/border"><span class="aside_btn1">공지사항</span></a></td>
                    </tr>   
                    <tr>                        
                        <td><a href="/web/event"><span class="aside_btn1">이벤트</span></a></td>
                        <td><a href="/web/customer_service"><span class="aside_btn1">고객센터</span></a></td>
                    </tr>
                </table>
                <?php if (isset($_SESSION['session_key'])) {?>               
                <div class="aside2_box2_title">마이페이지</div>                
                <table width="100%"> 
                    <tr>
                        <td style="width:50%;"><a href="/web/member_info"><span class="aside_btn2">내정보</span></a></td>
                        <td width="50%"><a href="/web/recommend_member"><span class="aside_btn2">추천회원리스트</span></a></td>
                        <!-- <td width="50%"><a href="/web/change_password"><span class="aside_btn2">비밀번호변경</span></a></td> -->
                    </tr>   
                    <tr>                           
                        <td width="50%"><a href="/web/point_history"><span class="aside_btn2">포인트내역</span></a></td>
                        <td width="50%"><a href="/web/note"><span class="aside_btn2">쪽지함</span></a></td>
                    </tr>  
                    <tr>                        
                        <td width="50%"><a href="/member/logout"><span class="aside_btn2">로그아웃</span></a></td>
                    </tr>
                </table>  
                <?php } ?>
            </div>        
        </div>
    </div>
</div><!-- 우측메뉴 -->

<div id="wrap" class="cf">
<div class="header_wrap cf">
    <div class="header_box_wrap">
        <div class="header_box cf">
            <div class="logo"><a href="#" class="m_game_open" onClick="$('.aside').asidebar('open')"><img src="/assets_w/images/m_menu1.png" width="40"></a><a href="/"><img src="/assets_w/images/logo.png"></a> <a href="#" class="m_my_open" onClick="$('.aside2').asidebar('open')"><img src="/assets_w/images/m_menu2.png" width="40"></a></div>
            <div class="time"><span class= "util_time" id="util_time" ><?php echo date('Y-m-d H:i:s'); ?></div>
            <?php if (isset($_SESSION['session_key'])) {?>
            <div class="my pc_my">
                <ul>
                    <li><a href="/web/apply?menu=c"><span class="small_t"><img src="/assets_w/images/my_icon01.png"></span> <span class="big_t">보유머니</span>&nbsp;<span class="my_font"><?= number_format(session()->get('money') ?? 0) ?></span></a></li>
                    <li class="point" ><a href="/web/point_history"><span class="small_t"><img src="/assets_w/images/my_icon02.png"></span> <span class="big_t">포인트</span>&nbsp;<span class="my_font"><?= number_format(session()->get('point') ?? 0) ?></span></a></li>
                    <li><a href="/web/gamble_shop"><span class="small_t"><img src="/assets_w/images/my_icon03.png"></span> <span class="big_t">G머니</span>&nbsp;<span class="my_font"><?= number_format(session()->get('g_money')) ?></span></a></li>
                    <li><a href="/web/note"><span class="small_t"><img src="/assets_w/images/my_icon04.png"></span> <span class="big_t">쪽지</span>&nbsp;<span class="my_font">(<?= session()->get('tm_unread_cnt') ?>)</span></a></li>
                </ul>
            </div>
            <?php } ?>
            <?php if(!isset($_SESSION['session_key'])) {?>
            <form class="login pc_login" data-frm="pc">
                <ul>                   
                    <li><input type="text" class="input_login"  id="user_id" maxlength="12" placeholder="아이디" style="margin:0"></li>
                    <li><input type="password" class="input_login"  id="user_pw" autocomplete="off" maxlength="12"placeholder="비밀번호" style="margin:0"></li>
                    <li><button type="submit" class="login_submit my_btn">로그인</li>                
                    <li><button type="button" class="my_btn" onclick="window.location.href='/member/join' ">회원가입</li>                                         
                </ul>
            </form>
            <?php } ?>
            <div class="lnb">
				<ul>
					<li><a href="/web/member_info">마이페이지</a></li>
					<li><a href="/web/border">공지사항</a></li>
					<li><a href="/web/event">이벤트</a></li>
					<li><a href="/web/customer_service">고객센터</a></li>
					<li class="logout"><a href="/member/logout">로그아웃</a></li>
				</ul>
			</div>
        </div>
    </div>

    <div class="gnb_wrap">
        <div class="gnb_box">
        <?php if(!isset($_SESSION['session_key'])) {?>
            <form class="login m_login" data-frm="m">
                <ul>                   
                    <li><input type="text" class="input_login user_id_m"  id="user_id" maxlength="12" placeholder="아이디" style="margin:0"></li>
                    <li><input type="password" class="input_login user_pw_m"  id="user_pw" autocomplete="off" maxlength="12"placeholder="비밀번호" style="margin:0"></li>
                    <li><button type="submit" class="login_submit my_btn">로그인</li>                
                    <li><button type="button" class="my_btn" onclick="window.location.href='/member/join' ">회원가입</li>                                       
                </ul>
            </form>
        <?php } ?>
            <div class="my m_my">
				<ul>
					<li><a href="/web/apply?menu=c"><span class="small_t"><img src="/assets_w/images/my_icon01.png"></span> <span class="big_t">보유머니</span>&nbsp;<span class="my_font"><?= number_format(session()->get('money') ?? 0) ?></span></a></li>
					<li onclick="pointToMoney()" class="cc" ><a href="/web/point_history"><span class="small_t"><img src="/assets_w/images/my_icon02.png"></span> <span class="big_t">포인트</span>&nbsp;<span class="my_font mb_point"><?= number_format(session()->get('point') ?? 0) ?></span></a></li>
					<li><a href="/web/gamble_shop"><span class="small_t"><img src="/assets_w/images/my_icon03.png"></span> <span class="big_t">G머니</span>&nbsp;<span class="my_font"><?= number_format(session()->get('g_money')) ?></span></a></li>
					<li><a href="/web/note"><span class="small_t"><img src="/assets_w/images/my_icon04.png"></span> <span class="big_t">쪽지</span>&nbsp;<span class="my_font">(<?= session()->get('tm_unread_cnt') ?>)</span></a></li>
				</ul>
			</div>	
            <div class="game">
                <ul>
                    <li><a href="/web/sports">스포츠</a></li>
                    <li><a href="/web/realtime">LIVE스포츠</a></li>
                    <li><a href="/web/casino?prd_type=C&prd_id=5">카지노</a></li>
                    <li><a href="/web/minigame">미니게임&nbsp;&nbsp;<img src="/assets_w/images/icon_down.png"></a>
                        <ul>
                            <?php if('ON' == config(App::class)->IS_EOS_POWERBALL){ ?>
                                <li><a href="/web/minigame?betType=3">엔트리 EOS 파워볼</a></li>
                            <?php } ?>
                            <?php if('ON' == config(App::class)->IS_POWERBALL){ ?>
                                <li><a href="/web/minigame?betType=15">엔트리 파워볼</a></li>
                            <?php } ?>
                            <li><a href="/web/minigame?betType=4">엔트리 파워사다리</a></li>
                            <li><a href="/web/minigame?betType=5">엔트리 키노사다리</a></li>
                            <li><a href="/web/premiumShip">가상축구 - 프리미어쉽</a></li>
                            <li><a href="/web/superLeague">가상축구 - 슈퍼리그</a></li>
                            <li><a href="/web/euroCup">가상축구 - 유로컵</a></li>
                            <li><a href="/web/worldCup">가상축구 - 월드컵</a></li>
                        </ul>					
                    </li>
                    <li><a href="/web/hash?type=R">해쉬게임&nbsp;&nbsp;<img src="/assets_w/images/icon_down.png"></a>
                        <ul>
                            <li><a href="/web/hash?type=R">룰렛</a></li>
                            <li><a href="/web/hash?type=H">하이로우</a></li>
                            <li><a href="/web/hash?type=B">바카라</a></li>
                        </ul>					
                    </li>
                    <li><a href="/web/slot">슬롯게임</a></li>
                    <!-- <li><a href="/esports">E-스포츠</a></li> -->
                </ul>
            </div>
            <div class="gnb">
                <ul>
                    <li><a href="/web/betting_history?menu=b&bet_group=2&clickItemNum=2&betToDate=<?php echo date("Y-m-d"); ?>&betFromDate=<?=$from_date?>">베팅내역</a></li>
                    <!-- <li><a href="/web/betting_rules">베팅규정</a></li> -->
                    <li><a href="/web/apply?menu=c">충전하기</a></li>
                    <li><a href="/web/exchange">환전하기</a></li>
                    <li><a href="/web/gamble_shop">골드템</a></li>
                </ul>
            </div>
        </div>
    </div>
</div><!-- header_wrap -->

<script>






// Header menu drop
let header = document.querySelector(".header_wrap");
let headerHeight = header.offsetHeight;

window.onscroll = function () {
let windowTop = window.scrollY;
if (windowTop >= headerHeight) {
    header.classList.add("drop");
} 
else {
    header.classList.remove("drop");
}
};


const audio_message = new Audio('/sound/site_message.mp3');
let login_call = false;

$(function(){

    // Menu exposure based on login status
    const session_key = `<?= isset($_SESSION['session_key']) ?>`;   // If it is 1, you are logged in
    const pathname = window.location.pathname;

    // console.log(session_key,  `<?=session()->get('member_idx')?>` );
    // console.log(session_key)
    if( session_key !== "1" ){  // non-login

        $(document).find(".cf .header_wrap .top_wrap .login").show();
        $(document).find(".cf .header_wrap .top_wrap .top_right").hide();
        $(document).find(".m_my").hide();
        if( !(pathname == "/web/index" || pathname == "/" || pathname == "/member/join") ){
            window.location.href = "/";
            alert("로그인 후 이용해주세요.");
        }
        $(document).find(".lnb").hide();

    } else {    // login
        $(document).find(".cf .header_wrap .top_wrap .login").hide();
        $(document).find(".cf .header_wrap .top_wrap .top_right").show();
        // $(document).find("#login").hide();
        $(document).find(".m_login").hide();
        $(document).find(".pc_login").hide();
        // $("body").addClass("logined");
        $(document).find(".lnb").show();
        //uplicateLoginCheck();  // user info check
        setInterval(duplicateLoginCheck, 5000);
        
        locationMessageCheck(); // message check
        setInterval(locationMessageCheck, 10000);
    }



    let formLogin = $(document).find("form.login");
    let formLoginSubmit = formLogin.find("button[type='submit']");

    formLogin.on("submit", function(e){
        e.preventDefault();

        var iden = $(this).attr('data-frm');

        // console.log($(this).attr('data-frm'));

        console.log('login');

        if(iden == 'pc'){
            const user_id = $(".cf #user_id");
            if( user_id.val().length < 1 ){
                alert("아이디를 입력해주세요");
                user_id.focus();
                return false;
            }
            const user_pw = $(".cf #user_pw");
            if( user_pw.val().length < 1 ){
                alert("비밀번호를 입력해주세요");
                user_pw.focus();
                return false;
            }

            const id = user_id.val();
            const pw = user_pw.val();
            login(id, pw);  // login
        }else{
            const user_id = $(".cf .user_id_m");
            if( user_id.val().length < 1 ){
                alert("아이디를 입력해주세요");
                user_id.focus();
                return false;
            }
            const user_pw = $(".cf .user_pw_m");
            if( user_pw.val().length < 1 ){
                alert("비밀번호를 입력해주세요");
                user_pw.focus();
                return false;
            }

            const id = user_id.val();
            const pw = user_pw.val();
            login(id, pw);  // login
        }



       
    });

    // point
    $(document).on("click", ".point", function(){
        pointToMoney();
    })

});



// login
const login = function(id, pw){
    if(true == login_call) return;
    login_call = true;

    let request_data = {
        'id': id,
        'password': pw
    }
    // console.log(request_data);
    call_ajax("login_ready", "/member/login", request_data);
}
const result_login_ready = function(response){
    
    let result_code = response.result_code;
    console.log(response)
    
    if( result_code === 200 ){
        localStorage.setItem('keep_login_access_token',response['keep_login_access_token']);
        window.location.href = "/";
        tokenCheck();
    }

    if(response == "loginError"){
        // console.log('loginError 실행');
        login_call = false;
    }
}


// user info & user check
const duplicateLoginCheck = function (){
    let request_data = {
        keep_login_access_token: localStorage.getItem("keep_login_access_token"),
        session_key: '<?= session()->get('session_key') ?>',
    }
    call_ajax("duplicateLoginCheck_ready", "/member/duplicateLoginCheck", request_data);
}
const result_duplicateLoginCheck_ready = function(response){

    let $money = response.data.money;
    let $point = response.data.point;
    let $nick_name = response.data.nick_name;
    let $mynotes = response.data.notes;

    let $level = response.data.level;
    let $level_img = ``;
    switch($level){
        case "1":
        case "2":
        case "3": $level_img = `/assets_w/images/lv1.png`; break;
        case "4":
        case "5": $level_img = `/assets_w/images/lv4.png`; break;
        case "6":
        case "7": $level_img = `/assets_w/images/lv1.png`; break;
        case "8":
        case "9":
        case "10": $level_img = `/assets_w/images/lva.png`; break;
        default : break;
    }
    $(document).find(".cf .header_wrap .top_wrap .top_right .level .level-icon").attr("src", $level_img);
    $(document).find(".cf .header_wrap .top_wrap .top_right .level .level-icon").show();
    $(document).find(".cf .header_wrap .top_wrap .top_right .level span").html(`${$nick_name}님`);
    $(document).find(".cf .header_wrap .top_wrap .top_right .money").html(`<span>보유머니</span><b class="util_money">${format_money($money)}</b>`);
    $(document).find(".cf .header_wrap .top_wrap .top_right .point").html(`<span>포인트 </span><b class="font05">${format_money($point)}</b>`);
    $(document).find(".cf .header_wrap .top_wrap .top_right .note").html(`<a href="/web/note" class="loading__link"><span>쪽지</span><b class="mynotes">(${format_money($mynotes)})</b></a>`);


    // Mobile View Realtime Update
    $(document).find('.mb_point').find("span").text(format_money($point));
    $(document).find('.mb_money').find("span").text(format_money($money));
    // $(document).find('.mb_notes').find("span").text(format_money($mynotes));
}




// note(message) check
function locationMessageCheck(){
    let request_data = {}
    call_ajax("locationMessageCheck_ready", "/api/message/locationMessageCheck", request_data);
}
const result_locationMessageCheck_ready = function(response){
    // console.log('locationMessageCheck', response);

    let $bf_tm_unread_cnt = response.data.bf_tm_unread_cnt;
    let $tm_unread_cnt = response.data.tm_unread_cnt;
    let pathname = window.location.pathname;

    if( Number($bf_tm_unread_cnt) < Number($tm_unread_cnt) ){
        audio_message.volume = 0.2;
        audio_message.play(); // audio play
        
        if( pathname != '/web/note' ){
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href = '/web/note';                
        }
    }
}

setInterval(timeCheck, 1000);

function getTimeStamp() {
    var d = new Date();
    var s =
            leadingZeros(d.getFullYear(), 4) + '-' +
            leadingZeros(d.getMonth() + 1, 2) + '-' +
            leadingZeros(d.getDate(), 2) + ' ' +
            leadingZeros(d.getHours(), 2) + ':' +
            leadingZeros(d.getMinutes(), 2) + ':' +
            leadingZeros(d.getSeconds(), 2);

    return s;
}

function leadingZeros(n, digits) {
    var zero = '';
    n = n.toString();

    if (n.length < digits) {
        for (i = 0; i < digits - n.length; i++)
            zero += '0';
    }
    return zero + n;
}

function timeCheck() {

$('#util_time').text(getTimeStamp());
}





// 포인트 머니 전환
function pointToMoney(point) {
    var message = '포인트를 머니로 전환하시겠습니까?';
    if (!confirm(message)) {
        return false;
    }



    $.ajax({
        url: "/member/pointToMoney",
        data: {},
        method: 'POST'
    }).done(function (response) {

        duplicateLoginCheck();
        alert('전환되었습니다.');

    }).fail(function (error) {
        alert(error.responseJSON['messages']['messages']);
    });
}


// 427 승무패 및 오버언더 정렬함수
const fnSortBetting = function(){
    //$('p[id="marketsName_427"]').each(function(){
    // 해당마켓 테이블 부분 가져온다.
    $('#market_427').each(function(){
        const sort = {
            0 : '승+오버',
            1 : '무+오버',
            2 : '패+오버',
            3 : '승+언더',
            4 : '무+언더',
            5 : '패+언더'
        };
        
        // 마켓 하위 td부분을 돌면서 정렬한다.
        const $ul = $(this).closest('ul');
            $ul.find('td').each(function(){
                const $td = $(this);
                Object.keys(sort).forEach(function(key){
                    if(sort[key] == $td.data('odds-type')){
                        sort[key] = $.parseHTML($td.wrap("<div/>").parent().html());
                    }
                });
            });
        
        let html = '';
        for (let i = 0; i < Object.keys(sort).length; i++){
            if(false == Array.isArray(sort[i])){
                continue;
            }
            
            if(i == 0 || i == 3)
                html += '<tr>';

            sort[i].forEach(function(item){
                if(item.outerHTML)
                    html += item.outerHTML;
            });

            if(i == 2 || i == 5)
                html += '</tr>';
        }
        
        $ul.find('tbody').html(html);
    });

}

function cancel(){
    $('.popup_cont_wrap').hide();
    return false;
}

const tokenCheck = function (){
    <?php if (isset($_SESSION['session_key'])): ?>
        let request_data = {
            'keep_login_access_token': localStorage.getItem('keep_login_access_token')
        };
        
        call_ajax("tokenCheck_ready", "/api/tokenCheck", request_data);
    <?php endif; ?>
};
const result_tokenCheck_ready = function(response){
    console.log('result_tokenCheck_ready : '+JSON.stringify(response));
    
    const keep_login_access_token = response.data.keep_login_access_token;
    localStorage.setItem('keep_login_access_token', keep_login_access_token);
};



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
$('a.go-top').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1000);
});


</script>

