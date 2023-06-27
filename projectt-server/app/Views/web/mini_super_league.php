<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap"><div class="title">미니게임</div></div>

<div id="mini_wide_wrap">
    <!-- mini_menu_info -->
    <div class="mini_wide_left">
        <div class="mini_left_title">
        	<div class="mini_left_title_game">슈퍼리그 (BET365)</div>
            <div class="mini_left_title_box">제 <span class="mini_left_title_box_font" id="round"></span> 회차</div>
            <div class="mini_left_title_box">베팅마감 <span class="mini_left_title_box_font close_time_2">00:00</span></div>
		</div>
        <div class="mini_left_list_wrap">
        	<div class="mini_left_list">
                <ul>
                    <?php if('ON' == config(App::class)->IS_EOS_POWERBALL){ ?>
                        <a href="/web/minigame?betType=3">
                            <li class="mini_left_list1">
                                <span class="mini_menu_left">
									<img src="/assets_w/images/mini_icon03.png" width="24"> 엔트리 EOS 파워볼
								</span>
								<span class="mini_left_list_right">
									<span class="mini_left_list_right_font remain_time">00:00</span></span>
								</span>
                            </li>
                        </a>
                    <?php } ?>
                    <?php if('ON' == config(App::class)->IS_POWERBALL){ ?>
                        <a href="/web/minigame?betType=15">
                            <li class="mini_left_list1">
                                <span class="mini_menu_left">
									<img src="/assets_w/images/mini_icon03.png" width="24"> 엔트리 파워볼
								</span>
								<span class="mini_left_list_right">
									<span class="mini_left_list_right_font remain_time">00:00</span></span>
								</span>
                            </li>
                        </a>
                    <?php } ?>
                    <a href="/web/minigame?betType=4">
                        <li class="mini_left_list1">
                            <span class="mini_menu_left">
                                <img src="/assets_w/images/mini_icon04.png" width="24"> 엔트리 파워사다리
                            </span>
                            <span class="mini_left_list_right">
                                <span class="mini_left_list_right_font remain_time">00:00</span></span>
                            </span>
                        </li>
                    </a>
                    <a href="/web/minigame?betType=5">
                        <li class="mini_left_list1">
                            <span class="mini_menu_left">
                                <img src="/assets_w/images/mini_icon04.png" width="24"> 엔트리 키노사다리
                            </span>
                            <span class="mini_left_list_right">
                                <span class="mini_left_list_right_font remain_time">00:00</span></span>
                            </span>
                        </li>
                    </a>

                    <!-- 가상축구 -->
                    <a href="/web/premiumShip">
                        <li class="mini_left_list1">
                            <span class="mini_menu_left">
                                <img src="/assets_w/images/mini_icon02.png" width="24"> 프리미어쉽 (BET365)
                            </span>
                            <span class="mini_left_list_right">
                                <span class="mini_left_list_right_font close_time_1">00:00</span></span>
                            </span>
                        </li>
                    </a>
                    <a href="/web/superLeague">
                        <li class="mini_left_list1">
                            <span class="mini_menu_left">
                                <img src="/assets_w/images/mini_icon02.png" width="24"> 슈퍼리그 (BET365)
                            </span>
                            <span class="mini_left_list_right">
                                <span class="mini_left_list_right_font close_time_11">00:00</span></span>
                            </span>
                        </li>
                    </a>
                    <a href="/web/worldCup">
                        <li class="mini_left_list1">
                            <span class="mini_menu_left">
                                <img src="/assets_w/images/mini_icon02.png" width="24"> 월드컵 (BET365)
                            </span>
                            <span class="mini_left_list_right">
                                <span class="mini_left_list_right_font close_time_2">00:00</span></span>
                            </span>
                        </li>
                    </a>
                    <a href="/web/euroCup">
                        <li class="mini_left_list1">
                            <span class="mini_menu_left">
                                <img src="/assets_w/images/mini_icon02.png" width="24"> 유로컵 (BET365)
                            </span>
                            <span class="mini_left_list_right">
                                <span class="mini_left_list_right_font close_time_3">00:00</span></span>
                            </span>
                        </li>
                    </a>
                </ul>
            </div>
        </div>
    </div><!-- mini_wide_left -->


    <!-- mini_wide_center -->
    <div class="mini_wide_center">
        <div class="vsports_center_wrap">
            <div class="vsports_title_wrap">
                <div class="vsports_title">슈퍼리그 (BET365) &nbsp;&nbsp; 남은시간 <span class="mini_left_title_box_font close_time_2"></span></span></div>
                <script>//location.href=document.URL.replace('http://192.168.0.7/web/bsoccer', 'http://www.ace-abcde.com/web/bsoccer');</script>
                <div class="vsports_movie_wrap">
                    <div class="vsports_movie_inner"><iframe id="soccer_screen" defer src="http://odds-info.net/b3mv/?vn=2" class="bsoccer_video" width="514" height="290" scrolling="no" frameborder="0"></iframe></div>
                </div>
            </div>  
            <div class="vsports_s_right active">
				<div id="soccer_game_list"></div>

                <div class="mini_tab_wide">
                    <div class="mini_tab_wide_table mini_table_cont">
                        <ul class="mini_tab_wide_tr tabs">
                            <li name="showList" id="showList1" class="mini_tab_wide_td mini_tab_wide_td_on" onclick="showBetList('betHisList')">
                                <a href="#tab_bet_list">베팅내역</a>
                            </li>
                            <li name="showList" id="showList2"class="mini_tab_wide_td"  onclick="showBetList('gameResultList')">
                                <a href="#tab_bet_result">경기결과</a>
                            </li>
                        </ul>
                    </div>
                </div> 
                <div class="con_box00" id="tab_bet_list">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mini_list_title1_bg" id="betHisTable">
                        <tr class="list_tr2 trfirst">
                            <th class="list_title1">회차</th>
                            <th class="list_title1">배팅시간</th>
                            <th class="list_title1">경기내용</th>
                            <th class="list_title1">나의베팅</th>
                            <th class="list_title1">베팅금액</th>                    
                            <th class="list_title1">당첨금액</th>                    
                            <th class="list_title1">게임결과</th>
                            <th class="list_title1">회차결과</th>                                                             
                        </tr>
                        <tbody id="my_bet_list">
                        </tbody>                                                                    
                    </table>  
                    <div id="tab_bet_result" class="tab_content">
                        <table class="mini_list_title1_bg" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="list_tr2 trfirst">
                                <th class="list_title1">회차</th>
                                <th class="list_title1">시작시간</th>
                                <th class="list_title1">홈팀</th>
                                <th class="list_title1">점수</th>
                                <th class="list_title1">원정팀</th>
                                <th class="list_title1">마켓타입</th>
                                <th class="list_title1">게임결과</th>
                            </tr>
                            <tbody id="game_result">
                            </tbody>
                        </table>
                    </div>        
                    <div class="con_box10">
                        <div class="page_wrap acc_btn_wrap">
                            <ul  id="paginationArea">
                            </ul>
                        </div>
                    </div>            
                </div>
            </div>
        </div>

        <!-- mini_betting_cart -->
        <div class="mini_wide_right">
            <div class="mini_cart_title">BETTING SLIP</div>
            <div class="vsports_cart_bet">
                <div class ="sports_tab_wrap" id="bettingSlip" style="display: none">
                    <div class="sports_cart_bet">
                        <div width="100%"class="cart_bet">
                            <div>
                                <td>FC 바르셀로나 <span class="sports_cart_bet_font1">언더</span></td>
                                <td><span class="sports_cart_bet_p">1.00</span></td>
                                <td > <a href="javascript:void(0);"><img src="/assets_w/images/cart_close.png" style="padding:0 3px 0 3px; float: right;"></a></td>
                            </div>
                            <div>
                                <td colspan="2"><span class="sports_cart_bet_font2">오버언더</span></td>
                            </div>
                            <div>
                                <td colspan="2"><span class="sports_cart_bet_font3">FC 바르셀로나 <img src="/assets_w/images/vs.png" width="25"> 레알마드리드</span></td>
                            </div>  
                        </div>                
                    </div>   
                </div>
            </div> 
            <div class="mini_cart_wrap">
                <div class="con_box00">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="mini_cart_style1">보유머니 <span class="mini_cart_style3"><?= number_format(session()->get('money')) ?></span></td>
                        </tr>
                        <tr>
                            <td class="mini_cart_style1">최대베팅금 <span class="mini_cart_style3 max_bet_money">0</span></td>
                        </tr>
                        <tr>
                            <td class="mini_cart_style1">총 배당률 <span class="mini_cart_style2 bet_price">0</span></td>
                        </tr>
                        <tr>
                            <td class="mini_cart_style1">예상적중금 <span class="mini_cart_style2 will_win_money">0</span></td>
                        </tr>
                        <tr>
                            <td class="mini_cart_style1"><input class="input3 input_style06"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="mini_cart_wrap">
                <div class="con_box00">
                    <table width="100%" cellspacing="4" cellpadding="0">
                        <tr>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(10000, <?= session()->get('money') ?>)"><span class="cart_btn2">10,000</span></a></td>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(30000, <?= session()->get('money') ?>)"><span class="cart_btn2">30,000</span></a></td>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(50000, <?= session()->get('money') ?>)"><span class="cart_btn2">50,000</span></a></td>
                        </tr>
                        <tr>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(100000, <?= session()->get('money') ?>)"><span class="cart_btn2">100,000</span></a></td>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(300000, <?= session()->get('money') ?>)"><span class="cart_btn2">300,000</span></a></td>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(500000, <?= session()->get('money') ?>)"><span class="cart_btn2">500,000</span></a></td>
                        </tr>
                        <tr>
                            <td width="10%"><a href="javascript:void(0);" onclick="setBettingMoney(1000000, <?= session()->get('money') ?>)"><span class="cart_btn2">1,000,000</span></a></td>
                            <td width="10%"><a href="javascript:void(0);"><span class="cart_btn2 max_btn">MAX</span></a></td>
                            <td width="10%"><a href="javascript:void(0);"><span class="cart_btn2 reset_btn">RESET</span></a></td>
                        </tr> 
                        <tr>
                            <td width="100%" colspan="3"><a href="#"><span class="cart_btn1">베팅하기</span></a></td>
                        </tr>                                      
                    </table>
                </div>
            </div>
            <div id="domain_pc">
                <a target="_blank" href="https://불스주소.com/"><img src="/assets_w/images/bulls_domain.png"></a>
            </div>
        </div>

    </div>  
    

</div><!-- mini_wide_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript">
    let bet_markets_id = 0;
    let round = 0;  // 현재 선택한 id
    let round_count = 0;  // 현재 회차
    let fixture_id_1 = fixture_id_2 = fixture_remain_time_1 = fixture_remain_time_2 = 0;
    let fixture_dt_1 = '';
    let bfScreen = 1; // 이전 스크린
    let premierShipTime = euroCupTime = superLeagueTime = worldCupTime = 0;
    let timer = 0;

    $(document).ready(function(){
        initForm();
        //setCurrentRound();
        setScreen(2);
        setCurrentData();
        $('#tab_bet_result').hide();
        $('#screen_1').addClass('bet_on');
        
        // 선택한 마켓아이디
        $('.max_bet_money').text(setComma(<?= $game_config -> max ?>));
    
        // 베팅슬립에서 X버튼 눌렀을때
        $(document).on('click', '.notify-close-btn', function () {
            // 이전 선택지를 지워준다.
            if($('[data-odds*=' + bet_markets_id + ']').hasClass('bet_on')){
                $('[data-odds*=' + bet_markets_id + ']').removeClass('bet_on');
                initForm();
            }
        });
        
        $(document).on('click', '.max_btn', function() {
            if($('.bet_info').text().length <= 0){
                alert('베팅을 선택해주세요.');
                return;
            }
            
            let maxBetMoney = $('.max_bet_money').text();
            maxBetMoney = Number(maxBetMoney.replace(/,/gi,"")); //변경작업
            
            // 보유금액이 없다.
            if (0 >= <?=session()->get('money')?>) {
                alert('보유머니가 부족합니다.');
                return;
            }
            
            // 최대금액 이상이면 최대금액으로 없으면, 현재보유금 총액으로 한다.
            if (maxBetMoney > <?=session()->get('money')?>) {
                maxBetMoney = <?=session()->get('money')?>;
            }
            
            $('.input_style06').val(setComma(maxBetMoney));
            let maxCalc = calcMaxMoney(<?= $game_config -> limit ?>);
            // console.log(maxCalc);
            $('.input_style06').val(setComma(maxCalc));
            changeWillWinMoney();
        });
        
        $(document).on('click', '.reset_btn', function() {
            $('.input_style06').val(0);
            changeWillWinMoney();
        });
        
        // 배팅
        $(document).on('click', '.cart_btn1', function () {
            let betMoney = $('.input_style06').val();
            let remain_time = 0;
          
            if($('.bet_info').text().length <= 0){
                alert('베팅을 선택해주세요.');
                return;
            }
            
            betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
        
            if(betMoney <= 0){
                alert('베팅 금액을 설정해주세요.');
                return;
            }
            
            // 남은시간 체크
            if(fixture_id_1 == round){
                remain_time = fixture_remain_time_1;
            }else{
                remain_time = fixture_remain_time_2;
            }
                
            /*if(remain_time <= 5) {
                alert('베팅시간이 아닙니다.');
                return;
            }*/
             
            let mes = '선택하신 내용으로 베팅금액 : ' + betMoney + '원\n 베팅진행하시겠습니까?'
            if(confirm(mes) == false){
                return;
            }

            let memberBetList = [];
            //if ($('.slip_bet_ing').length > 0) {
                memberBetList.push({
                    'betId': 0,
                    'betPrice': Number($('.bet_price').text()),
                    'fixtureId': round,
                    'round': round_count,
                    'marketsId': bet_markets_id,
                    'marketsName': $('.bet_info').text(),
                    'betBaseLine': '',
                    'oddsTypes': '',
                })
//console.log(JSON.stringify(memberBetList));
//return;
				$('#loadingCircle').show();
                $.ajax({
                    url: '/api/bet/addMiniBet',
                    type: 'post',
                    data: {
                        'betList': memberBetList,
                        'betType': 6,
                        'totalOdds': Number($('.bet_price').text()), // 전체 베팅배율
                        'totalMoney': betMoney,
                        'betGroup': 'mini',
                        'folderType': 'S',
                        'keep_login_access_token': localStorage.getItem("keep_login_access_token")
                    },
                }).done(function (response) {
                    initForm();
                    // key reflush
                    const keep_login_access_token = response['data']['keep_login_access_token'];
                    localStorage.setItem('keep_login_access_token', keep_login_access_token);
                    
                    /*if(confirm('베팅에 성공하였습니다. 베팅내역을 확인하시겠습니까?') == true){
                        
                        location.href = "/web/betting_history?menu=b";
                    }*/
                                        
                    $('.util_money').text(setComma(response['data']['total_money']));
                    alert('베팅에 성공하였습니다.');
                    location.reload();
                    //return;
                  
                }).fail(function (error) {
                    alert(error.responseJSON['messages']['messages']);
                    $('#loadingCircle').hide();
                    if(Number(error.responseJSON['messages']['code']) === 1001){
                        location.reload();
                    }
                }).always(function (response) {
                    let path = '/api/bet/addMiniBet?betType=6';
                    let current_page = 'addMiniBet';
                    write_access_log(path, current_page);
                });

            //}else {
            //    alert('베팅을 선택해주세요.');
            //}
        });
        
        $(document).on('click','.odds_btn',function(){

            let betMoney = $('.input_style06').val();
            betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
            if(0 < betMoney){
                $('.input_style06').val(0);
            }
            
            let betListIndex = $(this).data('index');
            let betListFixId = $(this).data('fixture-id');
            let betOddsTypes = $(this).data('oddsType');    // 마켓네임
            //let betOddsTypesDisplay = betOddsTypes == 'win' ? '승' : betOddsTypes == 'draw' ? '무' : betOddsTypes == 'lose' ? '패' :betOddsTypes;
            let betMarketType = $(this).data('odds');   // 마켓타입
            //let betListFixIdStr = betListFixId + '';
            //let fixtureId = betListFixIdStr.split('_')[0];
            //let betId = $(this).data('bet-id');
            let betPrice = $(this).data('bet-price');
            let home = $(this).data('home');
            let away = $(this).data('away');
  
            // 이전 선택된 베팅과 같은 베팅을 선택시는 초기화, 선택해지
            if (bet_markets_id == betMarketType){
                console.log(1);
                $(this).removeClass('bet_on');
                initForm();
                round = 0;
                bet_markets_id = 0;
                return;
            }
            // 이전 선택지를 지워준다.
            if($('[data-odds*=' + bet_markets_id + ']').hasClass('bet_on')){
                $('[data-odds*=' + bet_markets_id + ']').removeClass('bet_on');
                initForm();
            }
            $('.bet_info').text(betOddsTypes);
            $('.bet_price').text(betPrice);
            
            $(this).addClass('bet_on');

            bet_markets_id = betMarketType;
            round = betListIndex;   // 선택한 배팅 아이디를 넣어준다.
            round_count = betListFixId;
            
            // 리그명
            //let leagueName = getSceenLeagueName();
            let leagueName = '프리미어십';

            let selectBettingTeam = "";
            let bettingTypeName;
            let drawVal;
			if(betMarketType == 13001 || betMarketType == 13002 || betMarketType == 13003) {
				selectBettingTeam = home;
				bettingTypeName = "승무패";
				drawVal = "";
			}else {
				selectBettingTeam = "";
				bettingTypeName = "오버언더";
				drawVal = "("+$(this).data('draw-val')+")";
			}
            
            // 베팅슬립 출력
            let html = 
                    '<div class="sports_cart_bet">'+
                    '<div width="100%"class="cart_bet">'+
                    '    <div>'+
                    '        <td>'+selectBettingTeam+' <span class="sports_cart_bet_font1 bet_info">'+betOddsTypes+drawVal+'</span></td>'+
                    '        <td><span class="sports_cart_bet_p">'+betPrice+'</span></td>'+
                    '        <td > <a href="javascript:void(0);"><img src="/assets_w/images/cart_close.png" style="padding:0 3px 0 3px; float: right;" onclick="notifyCloseBtn()"></a></td>'+
                    '    </div>'+
                    '    <div>'+
                    '        <td colspan="2"><span class="sports_cart_bet_font2">'+bettingTypeName+'</span></td>'+
                    '    </div>'+
                    '    <div>'+
                    '        <td colspan="2"><span class="sports_cart_bet_font3">'+ home +' <img src="/assets_w/images/vs.png" width="25"> '+ away +'</span></td>'+
                    '    </div>'+
                    '</div>'+
                    '</div>'+';';
                    /*let html = 
                        '	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="cart_bet">                                                                     '+
                        '		<tr>                                                                                                                                                            '+
                        '		<td style="width:70%;">'+selectBettingTeam+' <span class="sports_cart_bet_font1">'+betOddsTypes+drawVal+'</span></td>                                                              '+
                        '       <td><span class="sports_cart_bet_p bet_info">'+betPrice+'</span></td>                                                                                                    '+
                        '		<td align="right"> <a href="javascript:void(0);" class="notify-close-btn"><img src="/assets_w/images/cart_close.png" style="padding:0 3px 0 3px;"></a></td>     '+
                        '		</tr>                                                                                                                                                           '+
                        '		<tr>                                                                                                                                                            '+
                        '			<td colspan="2"><span class="sports_cart_bet_font2">'+bettingTypeName+'</span></td>                                                                                       '+
                        '		</tr>                                                                                                                                                           '+
                        '		<tr>                                                                                                                                                            '+
                        '			<td colspan="2"><span class="sports_cart_bet_font3">'+ home +' <img src="/assets_w/images/vs.png" width="25"> '+ away +'</span></td>                            '+
                        '		</tr>                                                                                                                                                           '+
                        '	</table>                                                                                                                                                            ';*/
                $('.sports_cart_bet').html($(html));
            	$('#bettingSlip').show();
            changeWillWinMoney();
        });
    });
    
    // 수기입력
    $(document).on('change', '.input_style06', function () {
            changeWillWinMoney();
        });
        
    $(document).on('focus', '.input_style06', function () {
            $('#loadingCircle').hide();
            let userInputBetBefore = $('.input_style06').val();
            sessionStorage.setItem('userInputBet', userInputBetBefore);
    });
    
    $(document).on('blur', '.input_style06', function () {
        $('#loadingCircle').hide();
        let userInputBetBefore = sessionStorage.getItem('userInputBet');
        let userInputBetAfter = $('.input_style06').val();
        userInputBetAfter = Number(userInputBetAfter.replace(/,/gi,""));
        let maxReturn = <?= $game_config -> limit ?>;

        if($('.bet_info').text().length <= 0){
            alert('베팅을 선택해주세요.');
            $('.input_style06').val(userInputBetBefore);
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            return;
        }
        
        if (Number.isNaN(userInputBetAfter)) {
            $('.input_style06').val(userInputBetBefore);
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            alert('숫자형태로 입력해주세요');
            return;
        }

        let maxBetMoney = $('.max_bet_money').text();
        maxBetMoney = Number(maxBetMoney.replace(/,/gi,"")); //변경작업

        let nowMoney = <?= !empty(session()->get('money')) ? session()->get('money') : 0 ?>;
        nowMoney = +nowMoney;

        if (userInputBetAfter > maxBetMoney) {
            $('.input_style06').val(userInputBetBefore);
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            alert('최대배팅금 이상 배팅할 수 없습니다');
            return;
        }
        if(!returnMaxCheck(userInputBetAfter)) {
                $('.input_style06').val(userInputBetBefore);
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                alert('최대당첨금액 제한 : 배팅할 수 없습니다');
                return;
        }   
        
        if (nowMoney === 0) {
            alert(`보유머니가 부족합니다`);
            return;
        } else if (maxBetMoney > nowMoney) {
            if(nowMoney < +userInputBetAfter) {
                $('.input_style06').val(setComma(nowMoney));
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                return;
            } else {
                $('.input_style06').val(setComma(userInputBetAfter));
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                return;
            }
        } 
        
        $('.input_style06').val(setComma(userInputBetAfter));
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        return;
    });

    // max버튼 자동계산
    function calcMaxMoney(max_bet_money) {
        let inputBetMoney = $('.input_style06').val();
        let data = inputBetMoney.replace(/,/gi,""); //변경작업
        let will_win_money = $('.will_win_money').text();
        will_win_money = +will_win_money.replace(/,/gi,"");
        let totalOdds = $('.bet_price').text();
        let willMoney = 0;
        totalOdds = +totalOdds;
        willMoney = +totalOdds * data;
        willMoney = Math.ceil(willMoney);
        max_bet_money = +max_bet_money;
        
        if (max_bet_money < willMoney) {
            // console.log("test : " ,+max_bet_money, +totalOdds);
            let ret = +max_bet_money / +totalOdds;
            // ret = Math.ceil(ret);
            
            // console.log("1번", +ret);
            return parseInt(ret);
        } else {
            // console.log("test : " +willMoney, +totalOdds);
            let ret = +willMoney / +totalOdds;
            
            // console.log("2번", +ret);
            return parseInt(ret);
        }
    }
    
    function changeWillWinMoney(){
        let inputBetMoney = $('.input_style06').val();
        let data = inputBetMoney.replace(/,/gi,""); //변경작업
      
        let totalOdds = Number($('.bet_price').text());
        let willMoney = (totalOdds == 0 ? 1 : totalOdds) * Number(data);

        willMoney = Math.ceil(willMoney);
        $('.will_win_money').html(setComma(willMoney));
    }
    
    const returnMaxCheck = function(inputBetMoney = 0) {
        let totalOdds = Number($('.bet_price').text());
        let willMoney = (totalOdds == 0 ? 1 : totalOdds) * Number(inputBetMoney);
        
        //willMoney = Math.ceil(willMoney);
        willMoney = Math.floor(willMoney);
        if(willMoney > <?= $game_config -> limit ?>) {
            return false;
        };
        return true;
    }

    function initForm(){
        totalOdds = 0;
        $('.total_odds').html(totalOdds);
        $('.input_style06').val(0);
        $('.will_win_money').html(0);
        $('.bet_info').text('');
        $('.bet_price').text(0);
        $('.sports_cart_bet').html('');
        $('#bettingSlip').hide();
        changeWillWinMoney();
    }
    
    const notifyCloseBtn = function() {
        initForm();
    }
    
    // 금액 버튼
    function setBettingMoney(money, userMoney){
    	if($('.bet_info').text().length <= 0){
            alert('베팅을 선택해주세요.');
            return;
    	}
        
    	// 보유금액이 없다.
        if (0 >= <?=session()->get('money')?>) {
            alert('보유머니가 부족합니다.');
            return;
        }

        let inputBetMoney = $('.input_style06').val();

        let data = inputBetMoney.replace(/,/gi,""); //변경작업

        let current_bet_money = Number(data);
        let bet_money = current_bet_money + Number(money);
        let maxBetMoney = $('.max_bet_money').text();
        maxBetMoney = maxBetMoney.replace(/,/gi,""); //변경작업

        if(bet_money > userMoney){
            alert('보유머니가 부족합니다.');
            return;
        }
        if(bet_money > maxBetMoney){
            alert('최대배팅금 이상 배팅할 수 없습니다');
            return;
        }
        if(!returnMaxCheck(bet_money)) {
            alert('최대당첨금액 제한 : 더이상 배팅할 수 없습니다');
            return;
        }
        
        $('.input_style06').val(setComma(bet_money));
        changeWillWinMoney();
    }
    
    // 남은시간 체크
    function checkRemainTime(gameCount = 0){
        // 넘어온 다음 게임이 없다.(점검중)
        if(gameCount == 0){
            $('.close_time_1').text('점검중');
            $('.close_time_2').text('점검중');
            $('.close_time_3').text('점검중');
            $('.close_time_11').text('점검중');
            return;
        }
        
        // 시간차감
        fixture_remain_time_1 -= 1;
        
        // 첫번째 경기
        let minite = Math.floor(fixture_remain_time_1 / 60);
        if(minite < 10){
            minite = '0'+minite;
        }
        
        let second = fixture_remain_time_1 % 60;
        if(second < 10){
            second = '0'+second;
        }
        
        let displayRemainTime_1 = minite + ':' + second;
        $('#timer_' + fixture_id_1).text(displayRemainTime_1);
        
        if(fixture_remain_time_1 <= 0){
            initForm();
            round = 0;
            bet_markets_id = 0;
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
            setCurrentData();
            clearInterval(timer);
        }
    }
    
    // 경기 셋팅
    function setCurrentData(){
        $league = 'Superleague';
        /* if(2 == bfScreen){
            $league = 'Superleague';
        }else if(3 == bfScreen){
            $league = 'Euro Cup';
        }else if(11 == bfScreen){
            $league = 'World Cup';
        } */
        
        $.ajax({
            url: '/minigame/bsoccerData',
            type: 'get',
            data: {
                'search_date' : fixture_dt_1,
                'league' : $league
            },
        }).done(function (response) {
            console.log(response);
            let list = response['data']['gameList'];
            let html = '';
            let index = 1;
            let currentDate = stringToDate(response['data']['serverDate']);
            set_close_time(response['data']['leagueTime'], currentDate);
            console.log('count : '+list.length);
            for (const[key, evItem] of Object.entries(list)) {
                //console.log(evItem);
                let jsonData = JSON.parse(evItem['result']);
                if(jsonData['type'] === '1x2'){
                    let fixture_date = stringToDate(jsonData['dt']);
                    let remain_time = fixture_date.getTime() - currentDate.getTime();
                    
                    let miniutes = Math.floor((remain_time % (1000 * 60 * 60)) / (1000*60));
                    let seconds = Math.floor((remain_time % (1000 * 60)) / 1000);
                    //let displayRemainTime = miniutes + ":" + seconds ;
                    
                    if(index == 1){
                        console.log('miniutes : ' + miniutes);
                        fixture_id_1 = jsonData['id'];
                        fixture_remain_time_1 = (miniutes*60) + seconds;
                    }
                    
                    // 리그명
                    let leagueName = getLeagueNameKor(jsonData['league']);

                    
    	               
                    
                    /* html += "<table class='sports_table_in bs_sports_table_in' cellpadding='0' cellspacing='0'>" +
                            "<tr class='bs_under_table_head'><td colspan='2'>[Bet365] - "+ leagueName +"</td><td colspan='2' style='text-align:center;'><span class='highlight'>다음경기(<em class='font_002'>"+jsonData['oid']+"</em>회)</span></td><td class='text_right' colspan='2'>" + jsonData['dt'] + "</td></tr>" +
                            "<tr class='soccer_list_title'><td colspan='6'>승무패</td></tr>" +
                            "<tr>" +
                            "   <td class='sports_table_in_1 odds_btn' colspan='2' data-index=\""+ jsonData['id'] +"\" data-fixture-id=\""+ jsonData['oid'] +"\" data-odds=\"13001\" data-odds-type=\"승\" data-bet-id=0 data-bet-price=" + jsonData['win'] + " data-home=" + jsonData['home'] + " data-away=" + jsonData['away'] + ">\n"+
                            "       <div class=\"sports_v_l\">"+jsonData['home']+"</div><div class=\"sports_v_r\">"+ jsonData['win'] +"</div>\n"+
                            "   </td>\n"+
                            "   <td class='sports_table_in_xo odds_btn' colspan='2' data-index=\""+ jsonData['id'] +"\" data-fixture-id=\""+ jsonData['oid'] +"\" data-odds=\"13002\" data-odds-type=\"무\" data-bet-id=0 data-bet-price=" + jsonData['draw'] + " data-home=" + jsonData['home'] + " data-away=" + jsonData['away'] + ">\n"+
                            "       <div class=\"sports_v_l\">무</div> <div class=\"sports_v_r\">"+ jsonData['draw'] +"</div>\n"+
                            "   </td>\n" +
                            "   <td class='sports_table_in_2 odds_btn' colspan='2' data-index=\""+ jsonData['id'] +"\" data-fixture-id=\""+ jsonData['oid'] +"\" data-odds=\"13003\" data-odds-type=\"패\" data-bet-id='0' data-bet-price=" + jsonData['lose'] + " data-home=" + jsonData['home'] + " data-away=" + jsonData['away'] + ">\n"+
                            "       <div class=\"sports_l_l\">"+jsonData['away']+"</div> <div class=\"sports_l_r\">" + jsonData['lose'] + "</div>\n"+
                            "   </td>\n";
                            "</tr>"; */

					$('#round').text(jsonData['oid']);

					html +=
                    	/* '<li class="vbet_list1_wrap"><!-- 그룹1 -->                                                                                                                                                             '			+
                    	'	<div class="vbet_list1_in_title" style="cursor: auto;">[Bet365] - 프리미어쉽 <span style="padding-left: 15%;">다음경기('+jsonData['oid']+'회)</span> <span style="float: right;margin-right: 10px;">'+jsonData['dt']+'</span></div> '  +
                    	'</li>                                                                                                                                                                                                '         + */
                    	'<li class="vbet_list1_wrap"><!-- 그룹1 -->                                                                                                                                                             '         +
                    	'	<span class="vbet_list1_in_title" style="cursor: auto;">승무패</span>                                                                                                                               '            +
                    	'	<ul class="bet_list2_wrap_in_new">                                                                                                                                                                    '         +
                    	'		<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 10px 0 0 0;">                                                                                                                               '         +
                    	'			<tr>                                                                                                                                                                                      '         +
                    	'				<td class="bet_list_td w30 odds_btn" data-index=\''+ jsonData['id'] +'\' data-fixture-id=\''+ jsonData['oid'] +'\' data-odds=\'13001\' data-odds-type=\'승\' data-bet-id=0 data-bet-price=' + Number(jsonData['win']).toFixed(2) + ' data-home=' + jsonData['home'] + ' data-away=' + jsonData['away'] + '>'+jsonData['home']+' <span class="betin_right bet_font1">'+ Number(jsonData['win']).toFixed(2) +'</span></td>                                                                         '          +               
                    	'				<td class="bet_list_td w30 odds_btn" data-index=\''+ jsonData['id'] +'\' data-fixture-id=\''+ jsonData['oid'] +'\' data-odds=\'13002\' data-odds-type=\'무\' data-bet-id=0 data-bet-price=' + Number(jsonData['draw']).toFixed(2) + ' data-home=' + jsonData['home'] + ' data-away=' + jsonData['away'] + '>무 <span class="betin_right bet_font1">'+Number(jsonData['draw']).toFixed(2)+'</span></td>                                                                                             '          +    
                    	'				<td class="bet_list_td w30 odds_btn" data-index=\''+ jsonData['id'] +'\' data-fixture-id=\''+ jsonData['oid'] +'\' data-odds=\'13003\' data-odds-type=\'패\' data-bet-id=0 data-bet-price=' + Number(jsonData['lose']).toFixed(2) + ' data-home=' + jsonData['home'] + ' data-away=' + jsonData['away'] + '>'+jsonData['away']+' <span class="betin_right bet_font1">' + Number(jsonData['lose']).toFixed(2) + '</span></td>                                                                      '          +                 
                    	'			</tr>                                                                                                                                                                                     '         +
                    	'		</table>                                                                                                                                                                                      '         +
                    	'	</ul>                                                                                                                                                                                             '         +
                    	'</li><!-- 그룹1끝 -->                                                                                                                                                                                   ';
                    index = index + 1;
                }else{
                    /* html += "<tr class='soccer_list_title'><td colspan='6'>오버언더</td></tr>" +
                            "<tr>" +
                            "   <td class='sports_table_in_1 odds_btn' colspan='3' data-index=\""+ jsonData['id'] +"\" data-fixture-id=\""+ jsonData['oid'] +"\" data-odds=\"13004\" data-odds-type=\"오버\" data-bet-id=0 data-bet-price=" + jsonData['lose'] + " data-home=" + jsonData['home'] + " data-away=" + jsonData['away'] + ">\n"+
                            "       <div class=\"sports_v_l\">오버("+jsonData['draw']+")</div><div class=\"sports_v_r\"><img src='/assets/images/arrow_green.gif' alt='over' class='ic_underover'/>"+ jsonData['lose'] +"</div>\n"+
                            "   </td>\n"+
                            "   <td class='sports_table_in_2 odds_btn' colspan='3' data-index=\""+ jsonData['id'] +"\" data-fixture-id=\""+ jsonData['oid'] +"\" data-odds=\"13005\" data-odds-type=\"언더\" data-bet-id='0' data-bet-price=" + jsonData['win'] + " data-home=" + jsonData['home'] + " data-away=" + jsonData['away'] + ">\n"+
                            "       <div class=\"sports_l_l\">언더("+jsonData['draw']+")</div> <div class=\"sports_l_r\"><img src='/assets/images/arrow_red.gif' alt='under' class='ic_underover'/>" + jsonData['win'] + "</div>\n"+
                            "   </td>\n";
                            "</tr>" +
                            "</table>\n"; */
					html +=
						'<li class="vbet_list1_wrap"><!-- 그룹1 -->                                                                                          '+                                                               
						'	<span class="vbet_list1_in_title" style="cursor: auto;">오버언더</span>                            '+                                                               
						'	<ul class="bet_list2_wrap_in_new">                                                                                                 '+                                                              
						'		<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 10px 0 0 0;">                                                            '+                                                              
						'			<tr>                                                                                                                   '+                                                              
						'				<td class="bet_list_td w50 odds_btn" data-index=\''+ jsonData['id'] +'\' data-fixture-id=\''+ jsonData['oid'] +'\' data-odds=\'13004\' data-odds-type=\'오버\' data-bet-id=0 data-draw-val='+jsonData['draw']+' data-bet-price=' + Number(jsonData['lose']).toFixed(2) + ' data-home=' + jsonData['home'] + ' data-away=' + jsonData['away'] + '>오버 ('+jsonData['draw']+') <span class="betin_right bet_font1"><img src="/assets_w/images/arr2.gif" style="margin-right: 10px">'+ Number(jsonData['lose']).toFixed(2) +'</span></td>'+                                                                                             
						'				<td class="bet_list_td w50 odds_btn" data-index=\''+ jsonData['id'] +'\' data-fixture-id=\''+ jsonData['oid'] +'\' data-odds=\'13005\' data-odds-type=\'언더\' data-bet-id=0 data-draw-val='+jsonData['draw']+' data-bet-price=' + Number(jsonData['win']).toFixed(2) + ' data-home=' + jsonData['home'] + ' data-away=' + jsonData['away'] + '>언더 ('+jsonData['draw']+') <span class="betin_right bet_font1"><img src="/assets_w/images/arr1.gif" style="margin-right: 10px">'+ Number(jsonData['win']).toFixed(2) +'</span></td> '+                                                                                            
						'			</tr>                                                                                                                  '+                                                              
						'		</table>                                                                                                                   '+                                                              
						'	</ul>                                                                                                                          '+                                                              
						'</li><!-- 그룹1끝 -->                                                                                                                ';
                }
            }
            //html += '<li class="vbet_list1_wrap"><!-- 그룹1 --><div class="bet_list1_wrap_in_title">[Bet365] - 프리미어십 <span style="padding-left: 15%;">다음경기(0000000000회)</span> <span style="float: right;margin-right: 10px;">2022-04-18 16:51:00</span></div></li><li class="vbet_list1_wrap"><!-- 그룹1 --><a href="javascript:void(0);">                    <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"></span></div></a>  <ul class="bet_list1_wrap_in">                      <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="bet1in">FC바르셀로나 <span class="betin_right bet_font1">0.00</span></td><td class="bet2in">무 <span class="betin_right bet_font1">0.00</span></td>                            <td class="bet7in">레알마드리드 <span class="betin_right bet_font1">0.00</span></td></tr>                                                                                                                                                                       </table>                                       </ul></li><!-- 그룹1끝 --> ";
            $('#soccer_game_list').html(html);
            //timer = setInterval(checkRemainTime, 1000);
            timer = setInterval(function(){
                checkRemainTime(list.length);
            }, 1000);
            getBetList();
        }).fail(function (error) {
            // alert('데이터 로드에 실패했습니다.');
        }).always(function (response) {});
    }
    
    // 리그별 시간 설정
    function set_close_time(leagueTime, currentDate){
        let close_time = miniutes = seconds = 0;
        let display_miniutes = display_seconds = '';
        let fixture_date = 0;
        for (const[key, evItem] of Object.entries(leagueTime)) {
            fixture_date = stringToDate(evItem['start_dt']);
            close_time = fixture_date.getTime() - currentDate.getTime();
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
    
    function getLeagueNameKor(leagueName){
        if(leagueName === 'Premiership'){
            leagueName = '프리미어십';
        }else if(leagueName === 'Superleague'){
            leagueName = '슈퍼리그';
        }else if(leagueName === 'Euro Cup'){
            leagueName = '유로컵';
        }else if(leagueName === 'World Cup'){
            leagueName = '월드컵';
        }
        
        return leagueName;
    }
    
    //현재 선택된 리그명
    function getSceenLeagueName(){
        let leagueName = '';
        if(1 === bfScreen){
            leagueName = '프리미어십';
        }else if(2 === bfScreen){
            leagueName = '슈퍼리그';
        }else if(3 === bfScreen){
            leagueName = '유로컵';
        }else{
            leagueName = '월드컵';
        }
        
        return leagueName;
    }
    
    function stringToDate(stringDt){
        let dt = stringDt.split(' ');
        let date = dt[0].split('-');
        let time = dt[1].split(':');

        let fixture_date = new Date(date[0], date[1], date[2], time[0], time[1], time[2]);
        return fixture_date;
    }
    
    // 중계화면 변경
    function setScreen(vn){
        //console.log(document.URL);
        //location.href=document.URL.replace('http://192.168.0.7/web/bsoccer', 'http://www.ace-abcde.com/web/bsoccer');
        //console.log(document.URL);
        let url = 'https://odds-info.net/b3mv/?vn=' + vn;
	$('#soccer_screen').attr('src', url);
        
        // 선택표시 처리
        $('#screen_'+bfScreen).removeClass('bet_on');
        $('#screen_'+vn).addClass('bet_on');
        
        bfScreen = vn;
        clearInterval(timer);
        //setCurrentData();
    }
    
    // 배팅내역, 경기결과
    const getBetList = function(currentPage){
        if (!currentPage) {
                param = {
                    'curPageNo': 1,
                    'displayCnt': 10
                };
        } else {
                $('#my_bet_list').empty();
                param = {
                    'curPageNo': currentPage,
                    'displayCnt': 10
                };
        };

        $.ajax({
            url: '/minigame/selectMemberMiniGameBet?betType=6&leagueType=2',
            type: 'post',
            data: param,
        }).done(function (response) {
            console.log(response);

            let list = response['data']['bet_list'];
            let game_result = response['data']['game_result'];
            let totalCnt = Number(response['data']['bet_count']);
            let curPageNo = Number(response['data']['curPageNo']);
            $('#my_bet_list').empty();
            let html = '';
            for (const[key, betInfo] of Object.entries(list)) {
                let arrCreateDt = betInfo['create_dt'].split(" ");
                let result = JSON.parse(betInfo['result']);
                if(betInfo['result_score']){
                    result = JSON.parse(betInfo['result_score']);
                }
                
                let dayOfWeek = getDayOfWeek(betInfo['create_dt'].replace(/-/gi,"/"));
                let round = result['oid'];
                let calc_result = calcResult(betInfo['ls_markets_id'], result['res'], betInfo['total_bet_money'], betInfo['bet_price']); 
                
                let $status = '기타';
                let $statusColor = 'sports_division1';
                let temp_money = 0;
                // console.log(betInfo['take_money'] !== undefined ? setComma(betInfo['take_money']) : setComma(temp_money));
                if (+betInfo['bet_status'] == 1) {
                    $status = '대기';
                    $statusColor = 'sports_division1';
                }
                if (result['res'] !== 'None') {
                    if (calc_result.status === 1) {
                        $status = '적중';
                        $statusColor = 'sports_division2';
                        temp_money = calc_result.rtn;
                    } else if (calc_result.status === 2) {
                        $status = '미적중';
                        $statusColor = 'sports_division1';
                        temp_money = calc_result.rtn;
                    }
                }

                if (+betInfo['total_bet_money'] == +betInfo['take_money']) {
                    $status = '취소';
                    $statusColor = 'sports_division1';
                }

                let $takemoney = betInfo['take_money'] !== undefined ? setComma(betInfo['take_money']) : setComma(temp_money);

                html += "<tr class='list_tr2 trfirst'>";
                html += "<td class='list1'><span class='font03'>"+round+"회</span></td>";
                html += "<td class='list1'><span class='font03'>"+arrCreateDt[0]+" <br> "+arrCreateDt[1]+"</span></td>";
                html += "<td class='list1'>"+ result['home'] + '<br> vs <br>' + result['away']+"</td>";
                html += "<td class='list1'><span class='font01'>"+betInfo['ls_markets_name']+" ("+Number(betInfo['bet_price']).toFixed(2)+")</span></td>";
                html += "<td class='list1'><span class='font05'>"+setComma(betInfo['total_bet_money'])+"</span></td>";
                html += "<td class='list1'><span class='font06'>"+ $takemoney +"</span></td>";
                html += "<td class='list1'><span class='font06'>" + getResultByName(result['res']) + "</span></td>";
                html += "<td class='list1'><span class='"+$statusColor+"'>"+$status+"</span></td>";
                html += "</tr>";
            }
            $('#my_bet_list').append(html);
            
            // 게임결과
            $('#game_result').empty();
            let html2 = '';
            for (const[key, gameResult] of Object.entries(game_result)) {
                let start_dt = gameResult['start_dt'].split(" ");
                let result = JSON.parse(gameResult['result']);
                if(gameResult['result_score']){
                    result = JSON.parse(gameResult['result_score']);
                }
                
                let start_dayOfWeek = getDayOfWeek(gameResult['start_dt'].replace(/-/gi,"/"));
                let round = result['oid'];
                let type = '승무패';
                if(result['type'] !== '1x2'){
                    type = '오버언더';
                }
                
                html2 += "<tr class='list_tr2 trfirst'>";
                    html2 += "<td class='list1'><span class='font03'>"+round+"회</span></td>";
					html2 += "<td class='list1'><span class='font03'>"+start_dt[0]+" <br> "+start_dt[1]+"</span></td>";
                    html2 += "<td class='list1'><span class='font01'>"+ result['home'] +"</span></td>";
                    html2 += "<td class='list1'><span class='font06'>"+ result['scoreh'] + ':' + result['scorea']+"</span></td>";
                    html2 += "<td class='list1'><span class='font01'>"+ result['away'] +"</span></td>";
                    html2 += "<td class='list1'><span class='font05'>"+type+"</span></td>";
                    html2 += "<td class='list1'><span class='font06'>"+getResultByName(result['res'])+"</span></td>";
                html2+= "</tr>";
            }
            $('#game_result').append(html2);

            fnSetPagination(totalCnt, curPageNo, 'getBetList', 'paginationArea');
        }).fail(function (error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function (response) {
        });
    }
    
    const calcResult = function(mybet, result, bet_money, bet_price) {
        //console.log(mybet, result, bet_money, bet_price, bet_money * bet_price);
        if (mybet === '13001') {
            if (result.indexOf('W') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        } else if (mybet === '13002') {
            if (result.indexOf('D') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        } else if (mybet === '13003') {
            if (result.indexOf('L') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        } else if (mybet === '13004') {
            if (result.indexOf('O') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        }  else if (mybet === '13005') {
            if (result.indexOf('U') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        }
    }
    
    // 게임결과를 한글로 변경해준다.
    const getResultByName = function(res) {
        if(res == 'None'){
            result = '-';
        }else if(res == 'Win'){
            result = '승';
        }else if(res == 'Draw'){
            result = '무';
        }else if(res == 'Lose'){
            result = '패';
        }else if(res == 'Under'){
            result = '언더';
        }else if(res == 'Over'){
            result = '오버';
        }
        return result;
    }

    const showBetList = function(type) {

    	$('li[name=showList]').removeClass('mini_tab_wide_td_on');
    	$('#betHisTable').hide();
    	$('#tab_bet_result').hide();
        
		if(type=='betHisList') {
			$('#showList1').addClass('mini_tab_wide_td_on');	
			$('#betHisTable').show();
		}
		
		if(type=='gameResultList') {
			$('#showList2').addClass('mini_tab_wide_td_on');
			$('#tab_bet_result').show();
			
		}
    	
    }
</script>
</body>
</html>