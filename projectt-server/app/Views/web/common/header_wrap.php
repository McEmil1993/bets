<?php session()->get('member_idx')?>

<!-- <body id="myAnchor"> -->
<body>
    <div id="wrap">

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

        <header>
            <div class="header-top">
                <h1><a href="/" class="loading__link"><img src="/assets_w/images/logo.png?v=<?php echo date("YmdHis"); ?>"></a></h1>
                
                <div class="header-mobile">
                    <div class="open-gnb"><a href="#"><img src="/assets_w/images/m_menu1.png"></a></div>
                    <div class="open-mypage"><a href="#"><img src="/assets_w/images/m_menu2.png"></a></div>
                </div>
                
                <nav id="gnb">
                    <div class="header-mobile-logo-area">
                        <a href="#" class="close-gnb"><img src="/assets_w/images/m_close.png"></a>
                    </div>
                    <ul>
                        <li><a class="loading__link" href="/web/realtime">라이브스포츠</a></li>
                        <li><a class="loading__link" href="/web/sports">스포츠</a></li>
                        <li><a class="loading__link" href="/web/classic">클래식스포츠</a></li>
                        <li><a class="loading__link" href="/web/casino?prd_type=C&prd_id=5">라이브카지노</a></li>
                        <li><a class="loading__link" href="/web/slot">슬롯머신</a></li>
                        <li><a class="loading__link" href="/web/minigame">미니게임</a></li>
                        <li><a class="loading__link" href="/web/premiumShip">가상게임</a></li>
                        <li><a class="loading__link" href="/web/hash">해쉬게임</a></li>
                        <?php if('ON' == config(App::class)->IS_HOLDEM/* && $_SESSION['level'] == 9*/){ ?>
                            <li><a class="loading__link" href="/web/holdem" target="blank">홀덤</a></li>
                        <?php } ?>
                    </ul>
                </nav>

                <div id="header-mypage">
                    <div class="header-mobile-logo-area">
                        <a href="#" class="close-mypage"><img src="/assets_w/images/m_close.png"></a>
                    </div>
                    <div class="header-user">
                        <div class="header-login">
                            <form class="user_login">
                                <input type="text" id="user_id" maxlength="12" placeholder="아이디">
                                <input type="password" id="user_pw" autocomplete="off" maxlength="12" placeholder="비밀번호">
                                <button type="submit" class="login_submit">로그인</button>
                                <button type="button" onclick="window.location.href='/member/join' ">회원가입</button>
                            </form>
                        </div>
                        <div class="header-myinfo">
                            <ul class="header-myinfo-txt">
                                <li>
                                    <div class="level">
                                        <img src="<?= $userLevelImagePath ?>" class="level-icon" alt="level icon">
                                        <span><?= session()->get('nick_name') ?>님</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="note">
                                        <a href="/web/note" class="loading__link">
                                            <span>쪽지</span>
                                            (<b class="mynotes"><?= session()->get('tm_unread_cnt') ?></b>)
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="money">
                                        <span>보유머니</span>
                                        <b class="util_money"><?= number_format(session()->get('money') ?? 0) ?></b>
                                    </div>
                                </li>              
                                <li>
                                    <div class="point">
                                        <span>포인트</span>
                                        <b><?= number_format(session()->get('point') ?? 0) ?></b>
                                    </div>
                                </li>
                            </ul>
                            <ul class="header-myinfo-btn">
                                <li>
                                    <a href="/web/apply?menu=c" class="loading__link">
                                        <strong class="">충전신청</strong>
                                    </a>
                                    <a href="/web/exchange" class="loading__link">
                                        <strong class="">환전신청</strong>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="header-menu">
                        <ul>
                            <li class="apply"><a class="loading__link" href="/web/apply?menu=c">충전신청</a></li>                                           
                            <li class="exchange"><a class="loading__link" href="/web/exchange">환전신청</a></li>
                            <li><a class="loading__link" href="/web/member_info">마이페이지</a></li>
                            <li><a class="loading__link" href="/web/note">쪽지함</a></li>
                            <li><a class="loading__link" href="/web/border">공지사항</a></li>
                            <li><a class="loading__link" href="/web/event">이벤트</a></li>
                            <li><a class="loading__link" href="/web/betting_history?menu=b&bet_group=1&clickItemNum=1">베팅내역</a></li>
                            <li><a class="loading__link" href="/web/betting_rules">베팅규정</a></li>
                            <li><img src="/assets_w/images/line.png"></li>
                            <li class="customer"><a class="loading__link" href="/web/customer_service">고객센터</a></li>
                            <li><img src="/assets_w/images/line.png"></li>
                            <li class="logout"><a href="/member/logout">로그아웃</a></li>
                        </ul>
                    </div>
                </div><!-- .header-mypage -->
            </div><!-- .header-top -->
            <div class="header-top-myinfo">
                <ul>
                    <li>
                        <div class="level">
                            <img src="<?= $userLevelImagePath ?>" class="level-icon" alt="level icon">
                            <span><?= session()->get('nick_name') ?>님</span>
                        </div>
                    </li>
                    <li>
                        <div class="note">
                            <a href="/web/note" class="loading__link">
                                <img src="/assets_w/images/icon_m.png">&nbsp;<span>쪽지</span>
                                (<b class="mynotes"><?= session()->get('tm_unread_cnt') ?></b>)
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="money">
                        <img src="/assets_w/images/icon_w.png">&nbsp;<span>보유머니</span>
                            <b class="util_money"><?= number_format(session()->get('money') ?? 0) ?></b>
                        </div>
                    </li>              
                    <li>
                        <div class="point">
                            <img src="/assets_w/images/icon_p.png">&nbsp;<span>포인트</span>
                            <b><?= number_format(session()->get('point') ?? 0) ?></b>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="header-bottom">
                <div class="bottom-myinfo">
                    <ul>
                        <li>
                            <div class="level">
                                <img src="<?= $userLevelImagePath ?>" class="level-icon" alt="level icon">
                                <span><?= session()->get('nick_name') ?>님</span>
                            </div>
                        </li>
                        <li>
                            <div class="note">
                                <a href="/web/note" class="loading__link">
                                    <span>쪽지</span>
                                    (<b class="mynotes"><?= session()->get('tm_unread_cnt') ?></b>)
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="money">
                                <span>보유머니</span>
                                <b class="util_money"><?= number_format(session()->get('money') ?? 0) ?></b>
                            </div>
                        </li>              
                        <li>
                            <div class="point">
                                <span>포인트</span>
                                <b><?= number_format(session()->get('point') ?? 0) ?></b>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- <div class="header-notice">
                    <p>공지사항에 오신 것을 환영합니다.</p>
                </div> -->
            </div>
            <?php if(isset($_SESSION['session_key'])){ ?>

            <div class="float_btn">
                <a href="/web/apply?menu=c" class="footer_btn footer_btn1"><img src="/assets_w/images/footer_btn1.png"></a>
                <a href="/web/exchange" class="footer_btn footer_btn2"><img src="/assets_w/images/footer_btn2.png"></a>
            </div>
            <?php } ?>
        </header>

    <script>
        const audio_message = new Audio('/sound/site_message.mp3');
        let login_call = false;

        $(function(){
            $(document).ready(function(){
                $(".point").css("cursor","pointer");
            })
            // Menu exposure based on login status
            const session_key = `<?= isset($_SESSION['session_key']) ?>`;   // If it is 1, you are logged in
            const pathname = window.location.pathname;
            
            tokenCheck();

            // console.log(session_key,  `<?=session()->get('member_idx')?>` );

            if( session_key !== "1" ){  // non-login
                $(document).find("header #header-mypage .header-user .header-login").show();
                $(document).find("header .header-top-myinfo").hide();
                
                if( !(pathname == "/web/index" || pathname == "/" || pathname == "/member/join") ){
                    window.location.href = "/";
                    alert("로그인 후 이용해주세요.");
                }

                $(document).find(".header-menu .logout").remove();

            } else {    // login
                $(document).find("header #header-mypage .header-user .header-myinfo").show();
                
                $("body").addClass("logined");

                //uplicateLoginCheck();  // user info check
                setInterval(duplicateLoginCheck, 10000);
                
                locationMessageCheck(); // message check
                setInterval(locationMessageCheck, 10000);
            }



            let formLogin = $(document).find("form.user_login");
            let formLoginSubmit = formLogin.find("button[type='submit']");

            // formLoginSubmit.on("click", function(e){
            //     e.preventDefault();
            //     formLogin.submit();
            // });
            formLogin.on("submit", function(e){
                e.preventDefault();

                console.log('login');

                const user_id = $("header #user_id");
                if( user_id.val().length < 1 ){
                    alert("아이디를 입력해주세요");
                    user_id.focus();
                    return false;
                }
                const user_pw = $("header #user_pw");
                if( user_pw.val().length < 1 ){
                    alert("비밀번호를 입력해주세요");
                    user_pw.focus();
                    return false;
                }

                const id = user_id.val();
                const pw = user_pw.val();
                login(id, pw);  // login
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
            if( result_code === 200 ){
                //sessionStorage.setItem('keep_login_access_token',response['keep_login_access_token']);
                localStorage.setItem('keep_login_access_token',response['keep_login_access_token']);
                window.location.href = "/";
            }

            if( response == "loginError" ){
                // console.log('loginError 실행');
                login_call = false;
            }
        }



        // user info & user check
        const duplicateLoginCheck = function (){
            let request_data = {
                //keep_login_access_token: sessionStorage.getItem("keep_login_access_token"),
                keep_login_access_token: localStorage.getItem("keep_login_access_token"),
                session_key: '<?= session()->get('session_key') ?>',
            }
            //console.log('call duplicateLoginCheck : '+JSON.stringify(request_data));
            call_ajax("duplicateLoginCheck_ready", "/member/duplicateLoginCheck", request_data);
        }
        const result_duplicateLoginCheck_ready = function(response){
            //console.log('result_duplicateLoginCheck_ready : '+JSON.stringify(response));
            //let $keep_login_access_token = response.data.keep_login_access_token;
            //sessionStorage.setItem('keep_login_access_token', $keep_login_access_token);
            //localStorage.setItem('keep_login_access_token', $keep_login_access_token);

            //let $g_money = response.data.g_money;
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
            $(document).find("header .header-bottom .bottom-myinfo .level .level-icon").attr("src", $level_img);
            $(document).find("header .header-bottom .bottom-myinfo .level .level-icon").show();
            $(document).find("header .header-bottom .bottom-myinfo .level span").html(`${$nick_name}님`);
            $(document).find("header .header-bottom .bottom-myinfo .money").html(`<span>보유머니</span><b class="util_money">${format_money($money)}</b>`);
            $(document).find("header .header-bottom .bottom-myinfo .point").html(`<span>포인트</span><b class="util_money">${format_money($point)}</b>`);
            $(document).find("header .header-bottom .bottom-myinfo .note").html(`<a href="/web/note" class="loading__link"><span>쪽지</span><b class="mynotes">(${format_money($mynotes)})</b></a>`);

            $(document).find("header #header-mypage .header-user .header-myinfo .header-myinfo-txt .level .level-icon").attr("src", $level_img);
            $(document).find("header #header-mypage .header-user .header-myinfo .header-myinfo-txt .level .level-icon").show();
            $(document).find("header #header-mypage .header-user .header-myinfo .header-myinfo-txt .level span").html(`${$nick_name}님`);
            $(document).find("header #header-mypage .header-user .header-myinfo .header-myinfo-txt .money").html(`<span>보유머니</span><b class="util_money">${format_money($money)}</b>`);
            $(document).find("header #header-mypage .header-user .header-myinfo .header-myinfo-txt .point").html(`<span>포인트</span><b class="util_money">${format_money($point)}</b>`);
            $(document).find("header #header-mypage .header-user .header-myinfo .header-myinfo-txt .note").html(`<a href="/web/note" class="loading__link"><span>쪽지</span><b class="mynotes">(${format_money($mynotes)})</b></a>`);

            $(document).find("header.mobile .header-top-myinfo .level .level-icon").attr("src", $level_img);
            $(document).find("header.mobile .header-top-myinfo .level .level-icon").show();
            $(document).find("header.mobile .header-top-myinfo .level span").html(`${$nick_name}님`);
            $(document).find("header.mobile .header-top-myinfo .money").html(`<img src="/assets_w/images/icon_w.png">&nbsp;<span>보유머니</span>&nbsp;<b class="util_money">${format_money($money)}</b>`);
            $(document).find("header.mobile .header-top-myinfo .point").html(`<img src="/assets_w/images/icon_p.png">&nbsp;<span>포인트</span>&nbsp;<b class="util_money">${format_money($point)}</b>`);
            $(document).find("header.mobile .header-top-myinfo .note").html(`<img src="/assets_w/images/icon_m.png">&nbsp;<span>쪽지</span>&nbsp;<b class="mynotes">(${format_money($mynotes)})</b>`);
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




        // setInterval(changeToken, 60000);

        // function enterkey() {
        //     if (window.event.keyCode == 13) {
        //         // 엔터키가 눌렸을 때 실행할 내용
        //         login();
        //     }
        // }

        // function login() {
        //     $.ajax({
        //         url: '/member/login',
        //         type: 'post',
        //         data: {
        //             'id': $('#id').val(),
        //             'password': $('#password').val()
        //         },
        //     }).done(function (response) {
        //         console.log(response);
        //         sessionStorage.setItem('keep_login_access_token',response['keep_login_access_token']);
        //         location.href="/";


        //         // 성공 시 동작
        //     }).fail(function (error) {
        //         if(error.responseJSON['status'] == 401){
        //             location.href= '/web/inspection';
        //         }else{
        //             alert(error.responseJSON['messages']['error']);
        //         }
        //     }).always(function (response) {
        //     });
        // }










        // function timeCheck() {
        //     console.log('timeCheck');
        //     const time = getTimeStamp().split(' ');
        //     $('#util_time').text(time[1]);
        // }
        // setInterval(timeCheck, 1000);

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







        // 포인트 머니 전환
        function pointToMoney(point) {
            var message = '포인트를 머니로 전환하시겠습니까?';
            if (!confirm(message)) {
                return false;
            }

            /*if (point <= 0) {
                alert('보유 포인트가 없습니다.');
                return false;
            }*/

            $.ajax({
                url: "/member/pointToMoney",
                data: {},
                method: 'POST'
            }).done(function (response) {

                // $('.util_money').text(setComma(response['data']['money']));
                // $('.util_point').text(0);
                
                // let href = 'javascript:pointToMoney(0)';
                // $('#a_point').prop('href', href);
                duplicateLoginCheck();
                alert('전환되었습니다.');

            }).fail(function (error) {
                alert(error.responseJSON['messages']['messages']);
            });
        }

        // function setComma(number){
        //     return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        // }


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
    </script>