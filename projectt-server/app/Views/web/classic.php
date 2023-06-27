<?php $imageBasePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath;?>
<?php
    // use App\Util\DateTimeUtil;
    // use App\Util\StatusUtil;
    // $imageBasePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath;
    // $p_data['num_per_page'] = SPORRS_BLOCK_COUNT;
    // $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    // $p_data['start'] = ($page - 1) * $p_data['num_per_page'];

    // $total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
    // //$total_page = 0;
    // $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    // $block = ceil($page / $p_data['page_per_block']); // 현재 블럭
    // $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    // $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지
    // if ($block >= $total_block)
    //     $last_page = $total_page;
    // $default_link = 'sports?data=1';
    // $sportsId = isset($_GET['sports_id']) ? $_GET['sports_id'] : 0;
    // $leagueId = isset($_GET['league_id']) ? $_GET['league_id'] : 0;

    // if($sportsId != 0) $default_link .= '&sports_id='.$sportsId;
    // //if($locationId != 0) $default_link .= '&location_id='.$locationId;
    // $leftSports[0] = Array('id'=>0, 'name'=>'전체', 'count'=>0);
    // $leftSports[6046] = Array('id'=>6046, 'name'=>'축구', 'count'=>0);
    // $leftSports[48242] = Array('id'=>48242, 'name'=>'농구', 'count'=>0);
    // $leftSports[154914] = Array('id'=>154914, 'name'=>'야구', 'count'=>0);
    // $leftSports[154830] = Array('id'=>154830, 'name'=>'배구', 'count'=>0);
    // $leftSports[35232] = Array('id'=>35232, 'name'=>'아이스하키', 'count'=>0);
    // $leftSports[687890] = Array('id'=>687890, 'name'=>'이스포츠', 'count'=>0);
    // $leftSports[154919] = Array('id'=>154919, 'name'=>'UFC', 'count'=>0);
?>
<?= view('/web/common/header') ?>
<script src="/assets_w/js/tendina.min.js"></script>

<!-- <script src="/assets_w/js/realtime_common_w.js?v=<?php echo date("YmdHis"); ?>"></script> -->
<!-- <script src="/assets/js/sports_common.js?v=<?php echo date("YmdHis"); ?>"></script> -->
<!-- <script src="/assets/js/realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script> -->
<!-- <script src="/assets/js/sports_realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script> -->


<script src="/assets_w/js/sports_common_w.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets_w/js/sports_common.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets_w/js/sports_realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script>


<?= view('/web/common/header_wrap') ?>
<div id="sports_wide_wrap">

    <!-- left menu -->
    <?//= view('web/classic_left')?>
    <div class="sports_wide_left">
        
        <div class="search_ajax">
            <input type="text" id="league_name_ajax" placeholder="국가 및 리그명">
            <button type="submit" id="league_name_ajax_button">검색</button>
        </div>

        <!-- <div class="search">
            <ul>
                <li>
                    <input name="league_name" id="league_name" type="text" class="input_search" placeholder="국가 및 팀명"></li>
                <li style="float:right;"><a onclick="searchLeague()"><span class="search_btn">검색</span></a></li>
            </ul>
        </div> -->
        <div class="con_box_left">      
            <ul class="dropdown">
                <!-- <li class="menu1">
                    <a href="javascript:void(0);">
                        <div class="left_list1" id="left_menu_0">
                            <span class="menu_left">
                                <img src="https://imghubserver.com/dev_chosun/sports//icon_game0.png" width="18">
                                &nbsp;&nbsp;&nbsp; sport.name
                            </span>
                            <span class="m enu_right">
                                <span class="menu_right_box" id="sports_count_0">109</span>
                            </span>
                        </div>
                    </a>
                </li>
                <li class="menu1 on selected">
                    <a href="javascript:void(0);">
                        <div class="left_list1" id="left_menu_154914">
                            <span class="menu_left">
                                <img src="https://imghubserver.com/dev_chosun/sports//icon_game154914.png" width="18">&nbsp;&nbsp;&nbsp;야구                            </span>
                            <span class="m enu_right">
                                <span class="menu_right_box" id="sports_count_154914">9</span>
                            </span>
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="javascript:leftLeagueItemClick(154914, 4146)">
                                <span class="left_list1_in">
                                    NPB
                                    <span class="menu_right_box">1</span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </li> -->
            </ul>
        </div>
    </div><!-- sports_wide_left -->


    <div class="sports_wide_center classic_wrap">

        <div class="classic_title">
            <h2>클래식스포츠</h2>
            <ul class="classic_title_tab">
                <!-- <li><a href="#"><span class="tabon">탭명</span></a></li>
                <li><a href="#"><span class="tab">탭명</span></a></li> -->
            </ul>
        </div><!-- .classic_title -->
        
        <div class="classic_list_bonus_wrap">
            <div class="sports_list_bonus_title">
                다폴더 보너스 이벤트 배당 <span>(다폴더 조합시 지급)</span>
            </div>
            <div class="sports_list_bonus">
                <ul>
                    <li class="odds_3_folder_bonus">
                        <span class="bonus1">3폴더</span>이상
                        <div class="bonus_txt">0</div>
                    </li>
                    <li class="odds_5_folder_bonus">
                        <span class="bonus1">5폴더</span>이상
                        <div class="bonus_txt">0</div>
                    </li>
                    <li class="odds_7_folder_bonus">
                        <span class="bonus1">7폴더</span>이상
                        <div class="bonus_txt">0</div>
                    </li>
                </ul>
            </div>
        </div><!-- .classic_list_bonus_wrap -->

        <!-- <div class="sport_title_wrap cf">
			<div class="sport_s_title">크로스</div>
			<div class="tab_wrap sports_tab_wrap">
				<ul>
					<li><a href="#"><span class="tabon">탭명</span></a></li>
					<li><a href="#"><span class="tab">탭명</span></a></li>
				</ul>
			</div>
		</div> -->

        <div class="sports_s_center">
            <div class="dropdown2">



                <!-- <div class="sport_title">
                    <div class="sport_league">
                        <img src="/images/flag/58.png" width="28">
                        <img src="/assets_w/images/line.png">
                        <img src="https://imghubserver.com/dev_chosun/sports/icon_game6046.png" width="18">
                        <img src="/assets_w/images/line.png">
                        IDN D1
                    </div>
                    <div class="sport_title_time">
                        <span class="font04">승무패</span>
                        <img src="/assets_w/images/line.png">
                        08-29 17:30
                    </div>
                </div>

                <div class="sport_title_list bet_list1_wrap bettingInfo active bet_list1_wrap_on" id="fixture_row_9112123">
                    <ul>
                        <li class="sport_time bet1" onclick="openBetData(9112123)">08-29 17:30</li>
                        <li class="sport_state state1">승패</li>

                        <li class="sport_team1 bet_team1 odds_btn" data-bet-status="1" data-index="9112123_1__-100_2022-08-29 17:30:00" data-odds-type="win" data-bet-id="11179191429112124" data-bet-price="2.43" data-td-cell="11179191429112124_2022-08-29 17:30:00" data-markets_name="" data-markets_name_origin="승무패" data-markets_display_name="" data-bet-name="1">
                            <span class="team_l">Dewa United FC</span>
                            <span class="team_r">2.43</span>
                        </li>

                        <li class="sport_tie bet_vs odds_btn" data-bet-status="1" data-index="9112123_1__-100_2022-08-29 17:30:00" data-odds-type="draw" data-bet-id="11179190379112124" data-bet-price="3.09" data-td-cell="11179190379112124_2022-08-29 17:30:00" data-markets_name="" data-markets_name_origin="승무패" data-markets_display_name="" data-bet-name="X">
                            <span class="bet_font1">3.09</span>
                        </li>
                        
                        <li class="sport_team2 bet_team2 odds_btn" data-bet-status="1" data-index="9112123_1__-100_2022-08-29 17:30:00" data-odds-type="lose" data-bet-id="11179191399112124" data-bet-price="2.95" data-td-cell="11179191399112124_2022-08-29 17:30:00" data-markets_name="" data-markets_name_origin="승무패" data-markets_display_name="" data-bet-name="2">
                            <span class="team_l">2.95</span>
                            <span class="team_r">PSIS 세마랑</span>
                        </li>
                        
                        <li class="sport_more bet7" onclick="openBetData(9112123)">베팅</li>
                    </ul>
                </div> -->
            </div>
            <?= view('/web/common/pagination') ?>
        </div><!-- .sports_s_center-->
    </div><!-- .sports_wide_center -->
    
    


    
	<!-- 12/8 라이브스포츠 아코디언 js 수정 -->
	<script src="/assets_w/js/tendina.min.js"></script>
	<script>
	$(document).ready(function(){
		$('.dropdown3 .bet_list1_wrap_in_title').each(function(){
			$(this).click(function(){
				$(this).parent('a').next('ul').slideToggle();
				return false;
			});
		});
		
		// var drop4 = $('.dropdown4');
		// drop4.hide();
		// $(".sport_title_list .sport_more").click(function(){
		// 	var width_size = window.innerWidth;
		// 	if(width_size<=1240){
		// 		if($(this).hasClass('on')){
		// 			$(this).removeClass('on');
		// 			$(this).next(drop4).remove();
		// 		}else{
		// 			drop4.show();
		// 			$(".sport_title_list .sport_more").removeClass('on');			
		// 			$(this).addClass('on');
		// 			$(this).parent('ul').append(drop4);
		// 		};				
		// 	}else{
		// 		drop4.hide();
		// 		return false;
		// 	};
		// });
	});
	</script> 

    
    <div class="sports_wide_right">
        <div class="cart_wrap">

            <div class="btn__cart_close">
                <a href="#"><img src="/assets_w/images/m_close.png" width="50"></a>
            </div>

            <div class="sports_cart_title">
                BETTING SLIP
                <span class="sports_cart_title_right">
                    배당변경 자동적용
                    <a href="#">
                        <img src="<?=$is_betting_slip=='ON'?'/assets_w/images/cart_fix1.png':'/assets_w/images/cart_fix2.png'?>" onClick="setBettingSlip(this, '<?=$is_betting_slip?>')">
                    </a>
                </span>
            </div>
            

            <!-- <div class="font06" style="width: 100%; display:inline-block; text-align: center; padding: 5px 0; background:#2c2c2c;">
                보너스폴더는 자동적용됩니다.<br>(3폴더 1.04 , 4폴더 1.06 , 5폴더 이상 1.08)
            </div> -->

            <div class="sports_cart_wrap">
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="sports_cart_style1">
                                보유머니
                                <span class="sports_cart_style3"><?= number_format(session()->get('money')) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">
                                최대베팅금
                                <span class="sports_cart_style3 max_bet_money"><?= number_format($maxBetMoney) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">
                                최대적중금
                                <span class="sports_cart_style3 limit_Bet_Money">0</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div style="width: 100%; display: inline-block; padding:5px; background:#2c2c2c;">
                <a href="javascript:void(0);">
                    <span class="sports_btn2 waste_btn">
                        <img src="/assets_w/images/waste.png" alt="bet_delete" width="28">
                        전체 배팅 삭제
                    </span>
                </a>
            </div>

            <ul class ="slip_tab_wrap">
                <!-- <li class="sports_cart_bet" data-index="9112055_1__-100_2022-08-29 19:00:00" data-odds-types="lose" data-bet-id="5624912719112055" data-bet-name="2" data-bet-price="3.24" data-markets-name="승무패" data-bet-base-line="" data-fixture-start-date="2022-08-29 19:00:00" data-leagues_m_bet_money="1000000">
                    <div width="100%" class="cart_bet">
                    
                        <div>
                            <span class="sports_cart_bet_font1">
                                FC Metalist Kharkiv
                                <img src="/assets_w/images/vs.png" width="25">
                                Inhulets Petrove
                            </span>
                        </div>
                        <div>
                            <span class="sports_cart_bet_font2">승무패</span>
                        </div>
                        <div class="sports_cart_bet_font3">
                                FC Metalist Kharkiv
                                <span>패</span>
                                <span class="sports_cart_bet_p">3.24</span>
                        </div>
                        
                        <a href="#" class="sports_cart_bet_img">
                            <img src="/assets_w/images/cart_close.png" class="notify-close-btn" data-index="9112055_1__-100_2022-08-29 19:00:00" data-bet-id="5624912719112055">
                        </a>
                    </div>
                </li> -->
            </ul>

            <div class="sports_cart_wrap">
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                        <!-- <tr>
                            <td class="sports_cart_style1">보유머니<span class="sports_cart_style2"><?= number_format(session()->get('money')) ?></span></td>
                        </tr> -->
                        <tr>
                            <td class="sports_cart_style1">
                                배당률(보너스)
                                <span class="sports_cart_style3 bonus_total_odds">0</span>
                                <span class="sports_cart_style2 total_odds">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">예상당첨금 <span class="sports_cart_style2 will_win_money">0</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">베팅금액 <span class="sports_cart_style2"><input class="input3" id="betting_slip_money" style="text-align:right; width:150px;" value="0"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="4" cellpadding="0">
                        <tr>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(5000, <?= session()->get('money') ?>)"><span class="sports_btn2">5천원</span></a></td>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(10000, <?= session()->get('money') ?>)"><span class="sports_btn2">1만원</span></a></td>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(50000, <?= session()->get('money') ?>)"><span class="sports_btn2">5만원</span></a></td>
                        </tr>
                        <tr>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(100000, <?= session()->get('money') ?>)"><span class="sports_btn2">10만원</span></a></td>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(300000, <?= session()->get('money') ?>)"><span class="sports_btn2">30만원</span></a></td>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(500000, <?= session()->get('money') ?>)"><span class="sports_btn2">50만원</span></a></td>
                        </tr> 
                        <tr>
                            <td width="10%" align="center"><a href="javascript:setBettingMoney(1000000, <?= session()->get('money') ?>)"><span class="sports_btn2">100만원</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2 max_btn">MAX</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2 reset_btn">전체지우기</span></a></td>
                        </tr>                     
                        <tr>
                            <td width="100%" colspan="3" align="center"><a href="#"><span class="sports_btn1">베팅하기</span></a></td>
                        </tr>                                      
                    </table>
                </div>
            </div>
        </div><!-- .cart_wrap -->
        <div id="domain_pc">
            <a target="_blank" href="https://불스주소.com/"><img src="/assets_w/images/bulls_domain.png"></a>
        </div>
        <div class="cart_bg"></div>
    </div><!-- sports_wide_right -->
    
    <script>
    $(document).ready(function(){
        // <!-- 카트슝슝 -->
        // <!-- 왼쪽 베팅 -->
        // $('.sport_team1,.sport_team2,.sport_tie').on('click',function(){
        //     var button = $(this);
        //     var cartBet = $('.cart_bet');
        //     var scartBet = $('.sports_cart_bet');
        //     button.append('<span class="cart-item"></span>');
        //     button.addClass('sendtocart');
        //     cartBet.addClass('shake');
        //     scartBet.css('background','var(--bgcolor2)');
        //     setTimeout(function(){
        //         button.removeClass('sendtocart');		  
        //         cartBet.removeClass('shake');
        //         scartBet.css('background','var(--bgcolor3)');
        //         setTimeout(function(){
        //                 button.find('.cart-item').remove();
        //         },300);
        //     },800);
        // });
        // <!-- 오른쪽 베팅 -->
        // $('.bet_list_td').on('click',function(){
        //     var button = $(this);
        //     var cartBet = $('.cart_bet');
        //     var scartBet = $('.sports_cart_bet');
        //     button.append('<span class="cart-item"></span>');
        //     button.addClass('sendtocart');
        //     cartBet.addClass('shake');
        //     scartBet.css('background','var(--bgcolor2)');
        //     setTimeout(function(){
        //         button.removeClass('sendtocart');		  
        //         cartBet.removeClass('shake');
        //         scartBet.css('background','var(--bgcolor3)');
        //         setTimeout(function(){
        //                 button.find('.cart-item').remove();
        //         },300);
        //     },800);
        // });
    });
    </script>

</div><!-- mini_wide_wrap -->
<?= view('/web/common/footer_wrap') ?>
<!-- </div> -->
<!-- wrap -->



<div class="btn__cart-open cart_open">
    <a href="#fade_1"><img src="/assets_w/images/m_cart.png" width="50"></a>
    <span class="cart_count2">0</span>
</div>
<!-- <span class="fade_1_open cart_open"><a href="#fade_1"><img src="/assets_w/images/m_cart.png" width="50"></a></span>
<span class="cart_count2">1</span> -->
<script>
</script>

<!-- top버튼 -->
<!-- <a href="#myAnchor" class="go-top">▲</a> -->
<script>
    // arr_config
    let $arr_config = {
        'pre_limit_money' : '',
        'pre_max_money' : '',
        'inplay_no_betting_list' : '',
        'limit_folder_bonus' : '',
        'odds_3_folder_bonus' : '',
        'odds_4_folder_bonus' : '',
        'odds_5_folder_bonus' : '',
        'odds_6_folder_bonus' : '',
        'odds_7_folder_bonus' : '',
        'service_bonus_folder' : '',
        'service_real' : '',
        'service_sports' : ''
    };
    let $ListDataRefresh = true;







    let isMobile = mobileCheck(); // 1-모바일
    let betType = 'S'; // 1: 스포츠, 2: 실시간
    let folderType = 'S'; // 'S': 싱글, 'D': 다폴더
    let totalOdds = 0;

    // arr_config
    let service_bonus_folder = `<?= $arr_bonus['service_bonus_folder']; ?>`;
    let odds_3_folder_bonus = `<?= $arr_bonus['odds_3_folder_bonus']; ?>`;
    let odds_4_folder_bonus = `<?= $arr_bonus['odds_4_folder_bonus']; ?>`;
    let odds_5_folder_bonus = `<?= $arr_bonus['odds_5_folder_bonus']; ?>`;
    let odds_6_folder_bonus = `<?= $arr_bonus['odds_6_folder_bonus']; ?>`;
    let odds_7_folder_bonus = `<?= $arr_bonus['odds_7_folder_bonus']; ?>`;
    let limit_folder_bonus = `<?= $arr_bonus['limit_folder_bonus']; ?>`;



    let isAlreadyBetting = false;
    let betList = [];
    let is_betting_slip = '<?=$is_betting_slip?>';
    let maxBetMoney = `<?= $maxBetMoney ?>`;
    let limitBetMoney = `<?= $limitBetMoney ?>`;
    let nowMoney = <?= !empty(session()->get('money')) ? session()->get('money') : 0 ?>;
    let betDelayTime = new Array();
    betDelayTime['6046'] = `<?= $betDelayTime['6046'] ?>`;
    betDelayTime['35232'] = `<?= $betDelayTime['35232'] ?>`;
    betDelayTime['48242'] = `<?= $betDelayTime['48242'] ?>`;
    betDelayTime['687890'] = `<?= $betDelayTime['687890'] ?>`;
    betDelayTime['154830'] = `<?= $betDelayTime['154830'] ?>`;
    betDelayTime['154914'] = `<?= $betDelayTime['154914'] ?>`;
    
    let display_6046 = 0;
    let display_48242 = 0;
    let display_154914 = 0;
    let display_154830 = 0;
    let betList_new = new Map();
    let live_data = [];
    
    let isAsyncGetRealTimeGameLiveScoreList = false;
    let sports = [6046, 48242, 154914, 154830, 687890, 35232];
    let selectFixtureId = 0;    // 현재 선택한 경기
    let selectFixtureDisplay = 0;
    let active1 = 0;
    let active2 = 0;
    let callGameLiveScoreList;
    let isClassic = 'ON'; // 1-클래식
    let serverName = '';

    function getRealTimeGameLiveScoreList(sportsId, locationId) {
        
        console.log('getRealTimeGameLiveScoreList !! click',sportsId, locationId,  );

        if(isAsyncGetRealTimeGameLiveScoreList) return false;
        isAsyncGetRealTimeGameLiveScoreList = true;
        active1 = sportsId ? sportsId : 0;
        active2 = locationId ? locationId : 0;
        let dataForm = {};

        if (locationId > 0) {
            dataForm['location_id'] = locationId;
        }
        if (sportsId > 0) {
            dataForm['sports_id'] = sportsId;
        }

        console.log('dataForm', dataForm);
        let totalGameCnt = 0;   // Live Sports 총 갯수 초기화
        
        $.ajax({
            url: '/api/real_time/getRealTimeGameLiveScoreList',
            type: 'post',
            data: dataForm,
        }).done(function (response) {
            $('.live_game_display *').remove();
            //console.log('response', response);

            //let fristBetList = [52,202,203,204,205,206,226,63,464, 349]; // 첫화면에 출력할 마켓타입들
            //let displayOrderMarkets = ['메인','승무패/승패','핸디캡','오버언더','기타'];
            let betting_html = '';
            live_data = response['data']['live_list'];
            
            const activeBetId = [];
            $('.slip_bet_ing').each(function(){
                activeBetId.push($(this).data('bet-id'));
            })

            const betAmount = $('#betting_slip_money').val();
            
            // 종목 세팅
            sports.forEach(function (sports_id) {

                let list = response['data']['live_list'][sports_id];

                if(!list) {
                    return true;
                }
                const listLength = Object.keys(list).length;
                
                // 하위 종목 갯수
                $('#sports_id_'+sports_id).text(listLength);

                // 종목 총 갯수
                totalGameCnt += Number(listLength);

                // 종목 총 갯수 -> left menu 로 바인딩
                $('.menu_right_box.realTimeTotalCnt').text(totalGameCnt);

                /*let beFid = '';
                let listCnt = 0;
                let moreCntList = [];
                let menuCount = 0;
                let arrMenuKey = [];

                // 종목별 경기수
                if(sports_id == 6046){
                    listCnt = <?//=isset($realTimeTotal[6046])?$realTimeTotal[6046]:0?>;
                }else if(sports_id == 48242){
                    listCnt = <?//=isset($realTimeTotal[48242])?$realTimeTotal[48242]:0?>;
                }else if(sports_id == 154914){
                    listCnt = <?//=isset($realTimeTotal[154914])?$realTimeTotal[154914]:0?>;
                }else if(sports_id == 154830){
                    listCnt = <?//=isset($realTimeTotal[154830])?$realTimeTotal[154830]:0?>;
                }else if(sports_id == 35232){
                    listCnt = <?//=isset($realTimeTotal[35232])?$realTimeTotal[35232]:0?>;
                }else if(sports_id == 687890){
                    listCnt = <?//=isset($realTimeTotal[687890])?$realTimeTotal[687890]:0?>;
                }*/

                /* 데이터 바인딩 */
                let mainGameList = [];
                for (const[fixtureKey, fixture_list] of Object.entries(list)) {
                    //let afFid = fixtureKey;
                    //let thisMarkettotalCnt = 0;
                    
                    // 메인 게임정보, 메인에 속한 키값, 스코어보드를 가져온다.
                    const firstGameIdx = Object.keys(fixture_list)[0];
                    let mainGame = '';

                    let mainKey = Object.keys(fixture_list[firstGameIdx])[0];

                    let classNum = "0";

                    if (sports_id == 6046) {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "1";
                    } else if (sports_id == 154914) {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "2";
                    } else if (sports_id == 48242) {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "3";
                    } else if (sports_id == 154830) {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "4";
                    } else if (sports_id == 35232) {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "6";
                    } else if (sports_id == 687890) {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "7";
                    } else {
                        mainGame = fixture_list[firstGameIdx][mainKey][0];
                        classNum = "8";
                    }

                    // console.log('mainGame', mainGame);

                    // 메인게임 리스트에 추가
                    betList.push(mainGame);
                    mainGameList.push(mainGame);

                    //let mainGameKey = mainGame['fixture_id']+'_'+mainGame['markets_id']+'_'+mainGame['bet_base_line']+'_'+mainGame['providers_id'];

                    // 경기정보 좌측 진행상태, 시간
                    let minute = Math.floor(mainGame['live_time']/60);
                    let second = Math.floor(mainGame['live_time']%60);
                    if(minute < 10) minute = '0'+minute;
                    if(second < 10) second = '0'+second;
                    let fixture_time = minute  + ":"+ second;

                    // 배구, 야구, e스포츠는 시간이 없다.
                    if(sports_id == 154830 || sports_id == 154914 || sports_id == 687890 ){
                        fixture_time = '진행중';
                    }

                    // 타이틀 html
                    html = '';

                    // 리그 베팅금
                    let leagues_bet_money = 0;
                    if(+mainGame['leagues_m_bet_money'] > 10000){
                        leagues_bet_money = setComma(parseInt(+mainGame['leagues_m_bet_money'] / 10000))+'만';
                    }else{
                        leagues_bet_money = setComma(parseInt(+mainGame['leagues_m_bet_money']));
                    }
                    
                    // 리그이미지가 없다.
                    let leagueImagePath = '<?=$imageBasePath?>'+'/league/'+mainGame['fixture_league_image_path'];
                    if(mainGame['fixture_league_image_path'].indexOf('flag') >= 0){
                        leagueImagePath = mainGame['fixture_league_image_path'];
                    }
                    
                    let sportsImagePath = '<?=$imageBasePath?>'+'/sports/icon_game'+mainGame['fixture_sport_id']+'.png';
                    
                    // 현재 오픈된 경기인지 파악해서 오픈상태 유지처리
                    let fixture_display = 'display:none';
                    //if($('#display_fixture_'+selectFixtureId).hasClass('fixture_open') == true){
                    if(selectFixtureId == fixtureKey){
                        if(selectFixtureDisplay == 1){
                            fixture_display = 'display:block';
                            //console.log('fixture_open : '+selectFixtureId);
                        }
                    }
                    
                    // 첫경기는 오픈된 상태로 준비
                    if(0 == selectFixtureId){
                        fixture_display = 'display:block';
                        selectFixtureId = fixtureKey;
                        selectFixtureDisplay = 1;
                    }
                    
                    // 스코어 rmq 도착전 null일때
                    let live_results_p1 = mainGame['live_results_p1'];
                    let live_results_p2 = mainGame['live_results_p2'];
                    if(null === live_results_p1 || null === live_results_p2){
                        live_results_p1 = live_results_p2 = 0;
                    }
                    
                    // 경기목록
                    // style=\"display: none;\"
                    html = "<li id=\"live_game_display_"+mainGame['fixture_sport_id']+"\" class='live_game_display_"+mainGame['fixture_sport_id']+" live_game_location_"+mainGame['fixture_location_id']+"'>" +
                            "<a href=\"#\">" +
                            "   <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
                            "       <div class=\"live_title_wrap\" onClick=\"onDisplayFixture("+fixtureKey+")\">" +
                            "           <div class=\"live_title_left\">" +
                            "               <img src=\""+sportsImagePath+"\" width=\"18\">&nbsp; " + 
                            "               <span class=\"live_title_left_team1\">" + mainGame['fixture_participants_1_name'] + "</span>" +
                            "               <img src=\"/assets_w/images/vs.png\" width=\"28\"> " + 
                            "               <span class=\"live_title_left_team2\">" + mainGame['fixture_participants_2_name'] + "</span>" +
                            "               <span class=\"live_title_left_score\">"+live_results_p1+" : "+live_results_p2+"</span>" +
                            "           </div>" +
                            "           <div class=\"live_title_right\">" + 
                            "               <span class=\"live_title_right_league\">" + mainGame['fixture_league_name'] + "</span>" +
                            "               <img src=\"/assets_w/images/live_line.png\">" +
                            "               <span class=\"font10\">"+
                            "               <span class=\"live_title_right_time\">" + mainGame['live_current_period_display'] + "&nbsp;" + fixture_time +  "</span>" +
                            "                   <a href=\"#\">" +
                            "                       <span class=\"live_title_right_btn\">베팅하기</span>" +
                            "                   </a>" +
                            "               </span>" +
                            "           </div>" +
                            "       </div>" +
                            "   </table>" +
                            "</a>" + 
                            "<ul>" +
                            "   <div class=\"live_box_wrap"+classNum+"\" style=\"clear:both; "+fixture_display+"\" id='display_fixture_"+fixtureKey+"'>" +
                            "       <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
                            "           <tr>" +
                            "               <td height=\"40\" style=\"padding:0 0 0 10px; background:rgba(0,0,0,0.4);\">" +
                            "                   <img src=\""+leagueImagePath+"\" width=\"28\">" +
                            "                   <img src=\"/assets_w/images/live_line.png\"> " + mainGame['fixture_league_name'] +
                            "                   <img src=\"/assets_w/images/live_line.png\">" +
                            "                   <span class=\"font06\">"+leagues_bet_money+"</span>" +
                            "               </td>" +
                            "               <td rowspan=\"3\">" +
                            //"                   <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
                            //"                       <tr>" +
                            getScoreBoard(sports_id, fixtureKey) +
                            //"                       </tr>" +
                            //"                   </table>" +
                            "               </td>" +
                            "           </tr>" +
                            "           <tr>" +
                            "               <td height=\"60\" style=\"padding:0 0 0 20px; border-bottom:1px solid rgba(255,255,255,0.2);\">" +
                            "                   <img src=\"/assets_w/images/live_home.png\"> " +
                            "                   <span class=\"live_font1\">"+mainGame['fixture_participants_1_name']+"</span>" +
                            "               </td>" +
                            "           </tr>" +
                            "           <tr>" +
                            "               <td height=\"60\" style=\"padding:0 0 0 20px\">" +
                            "                   <img src=\"/assets_w/images/live_away.png\">" +
                            "                   <span class=\"live_font1\">"+mainGame['fixture_participants_2_name']+"</span>" +
                            "               </td>" +
                            "           </tr>" +
                            "       </table>" +
                            "   </div>" +
                            "</ul>" +
                            "</li>" ;
                            
                            $('.live_game_display').append(html);
                            
                            for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
                                for (const[marketKey, game_list] of Object.entries(menu_list)) {
                                    let bGameKey = '';
                                    for (const[bKey, game] of Object.entries(game_list)) {
                                        bGameKey = game['fixture_id']+'_'+game['markets_id']+'_'+game['bet_base_line']+'_'+game['providers_id'];
                                        betList_new.set(bGameKey, game);
                                    }
                                }
                            }
                    
                    // 선택한 경기 배팅
                    if(selectFixtureId == fixtureKey || selectFixtureId == 0){
                        selectFixtureId = fixtureKey;
                    }
                    betting_html += "</li>";
                } // end fixture
                
                // 스코어 출력
                mainGameList.forEach(function(item){
                    try {
                        setLiveScore(item);
                    } catch (err) {
                        console.log(err);
                    }
                });
            }); // end sports
            // openBetData(selectFixtureId);
            
            // // 좌측 선택 처리
            // if (locationId > 0) {
            //     dataForm['location_id'] = locationId;
            //     dataForm['sports_id'] = sportsId;
            //     $('.dropdown2 li').each(function(){
            //         const $this = $(this);
            //         if($this.hasClass('live_game_location_'+locationId)){
            //             $this.attr('style', 'display: block;')
            //             console.log('locationId : '+locationId);
            //         }else {
            //             $this.attr('style', 'display: none;')
            //             console.log('not locationId : '+locationId);
            //         }
            //     });
            //     $('.location_block').each(function(){
            //         const $this = $(this);
            //         if($this.hasClass('location_id_'+locationId)){
            //             $this.attr('style', 'display: block;')
            //         }else {
            //             $this.attr('style', 'display: none;')
            //         }
            //     });
            // }
            
            // if (sportsId > 0) {
            //     dataForm['sports_id'] = sportsId;
            //     $('.dropdown2 li').each(function(){
            //         const $this = $(this);
            //         if($this.hasClass('live_game_display_'+sportsId)){
            //             $this.attr('style', 'display: block;')
            //         }else {
            //             $this.attr('style', 'display: none;')
            //         }
            //     });
            // }
            
            // 베팅슬립에 있는 배당중 하나라도 배당이 닫혔으면 초기화한다.
            /*if(activeBetId.length > 0){
                activeBetId.reverse().forEach(function(item){
                    //console.log('activeBetId event :'+item);
                    //if(item != 17162041418486740){
                        console.log('activeBetId event :'+item);
                        $('.odds_btn[data-bet-id="' + item +'"]').trigger('click');
                    //}
                });
                $('#betting_slip_money').val(setComma(betAmount));
                changeWillWinMoney();
            }*/

            /*if(activeBetId.length > 0){
                activeBetId.reverse().forEach(function(item){
                    $('.odds_btn[data-bet-id="' + item +'"]').trigger('click');
                });
                
                $('#betting_slip_money').val(setComma(betAmount));
                changeWillWinMoney();
            }*/
            
            // moreDisplaySelect(activeBetId);
            
            // 반복실행(이거를 켜면 펼쳐진 항목이 되돌아감) ★★★★★ 작업후 풀기 miok!!!!!!!!!!!!!
            // callGameLiveScoreList = setTimeout(function(){
            //     getRealTimeGameLiveScoreList(active1, active2);
            //     clearTimeout(callGameLiveScoreList);
            // }, 30000);
            isAsyncGetRealTimeGameLiveScoreList = false;
        }).fail(function (error) {
            // alert('데이터 로드에 실패했습니다.');
        }).always(function (response) {});
    } // end getRealTimeGameLiveScoreList
    



    $(document).ready(function(){
        $('#league_name').val('<?=$league_name?>');
        
        // 게임 배팅 리스트
        // getRealTimeGameLiveScoreList(0, 0);
        
        // 첫경기 오픈 처리
        //$('#display_fixture_'+selectFixtureDisplay).slideDown();
        //console.log('selectFixtureDisplay'+selectFixtureDisplay);
        
        // 베팅 선택
        $(document).on('click','.odds_btn',function(){
            
            let betMoney = $('#betting_slip_money').val();
            betMoney = Number(betMoney.replace(/,/gi,""));  //변경작업
            if(0 < betMoney){
                $('#betting_slip_money').val(0);
            }
            //console.log('odds_btn');
            let betListIndex = $(this).data('index');
            const indexArr = betListIndex.split('_');
            const baseLineKey = indexArr[2];
            //console.log(betListIndex);
            let tdcell = $(this).data('td-cell');
            const cellData = tdcell.split('_');
            let fixture_start_date = cellData[1];
            //let fixture_start_date = '';
            let betBaseLine = baseLineKey;

            let betListFixId = $(this).data('fixture-id');
            let betOddsTypes = $(this).data('oddsType');
            let betOddsTypesDisplay = betOddsTypes == 'win' ? '승' : betOddsTypes == 'draw' ? '무' : betOddsTypes == 'lose' ? '패' :betOddsTypes;
            let betMarketType = "";
            let betListFixIdStr = betListFixId + '';
            let fixtureId = betListFixIdStr.split('_')[0];
            let betId = $(this).data('bet-id');
            let betPrice = $(this).data('bet-price');
            let leagues_m_bet_money = $(this).data('leagues_m_bet_money');
            let obj = betList_new.get(betListIndex);
            let betSportId = obj.fixture_sport_id;
            let betMarketId = obj.markets_id;
            let alreadyCombinObj;
            let alreadyCombin = false;
            let isCombin = false;
            //let totalOdds = $('.total_odds').data("total_odds");
            const betName = $(this).data('bet-name');
            
            if ($(this).hasClass('bet_on') || $(this).hasClass('sports_select')){
                console.log('동일베팅 항목을 선택(선택해제)');
                //let price = $('[data-bet-id="' + betId + '"].slip_bet_ing .slip_bet_cell_r').text();
                let price = betPrice;
                price = price == 0 ? 1 : price;
                totalOdds = totalOdds / price;
                totalOdds = totalOdds == 1 ? 0 : totalOdds;
                $(this).removeClass('bet_on');
                $('.slip_bet_ing[data-bet-id="' + betId + '"]').remove();
                $('.total_odds').data("total_odds",totalOdds);
                $('.total_odds').html(totalOdds);

                let betSlipCount = getBetSlipCount();
                setBonusPrice(totalOdds, betSlipCount);

                changeWillWinMoney();
                //initForm();

                // 뱃팅슬롯 카운트
                if(isMobile){
                    $('.cart_count').text($('.slip_bet_ing').length);
                    $('.cart_count2').text($('.slip_bet_ing').length);
                }
                
                cartCount();
                return;
            }

            // 같은 경기에 다른 게임이 이미 선택 되어있는 경우 이전 게임 선택 해제 및 리스트 삭제하고
            // 지금 클릭한 게임을 추가한다
            if ($('[data-fixture-id*=' + betListFixId + ']').hasClass('sports_select') || $('[data-fixture-id*=' + betListFixId + ']').hasClass('bet_on')){
                console.log('동일경기 선택');
                //const $selecteSports = $(this).closest('.soprts_in_acc').find('.bet_on');
                let selectTagName = '.dropdown3';
                if(isMobile){
                    //selectTagName = '.bet_list1_wrap_in_title';
                    selectTagName = '#display_fixture_'+betListFixId;
                }else{
                    selectTagName = '.dropdown3';
                }
                const $selecteSports = $(selectTagName).find('.bet_on');
                if($selecteSports.length > 1){
                    const $this = $(this);
                    let flag1 = false;
                    let flag2 = false;
                    let targetObj;
                    const fixtureMarket1 = $this.data('index').split('_')[0] + '_' + $this.data('index').split('_')[1];
                    $selecteSports.each(function(){
                        const fixtureMarket2 = $(this).data('index').split('_')[0] + '_' +$(this).data('index').split('_')[1];
                        if(fixtureMarket1 == fixtureMarket2){
                            flag1 = true;
                        }else{
                            targetObj = $(this);
                            flag2 = true;
                        }
                    });

                    if(flag1 && flag2){
                        alreadyCombin = true;
                        alreadyCombinObj = targetObj;
                    }
                }
                if (betSportId == 48242) {
                    if (betMarketId == 28) {
                        const checkFlag1 = fnCheckCombine($(this), betList, 28, 226)
                        const checkFlag2 = fnCheckCombine($(this), betList, 28, 342)
                        if ((checkFlag1 && !checkFlag2) || (!checkFlag1 && checkFlag2)) {
                            isCombin = true;
                        }
                    } else if (betMarketId == 226) {
                        if (fnCheckCombine($(this), betList, 226, 28))
                            isCombin = true;

                    } else if (betMarketId == 342) {
                        if (fnCheckCombine($(this), betList, 342, 28))
                            isCombin = true;

                    } else if (betMarketId == 64) {
                        if (fnCheckCombine($(this), betList, 64, 21))
                            isCombin = true;

                    } else if (betMarketId == 21) {
                        if (fnCheckCombine($(this), betList, 21, 64))
                            isCombin = true;

                    } else if (betMarketId == 65) {
                        if (fnCheckCombine($(this), betList, 65, 45))
                            isCombin = true;

                    } else if (betMarketId == 45) {
                        if (fnCheckCombine($(this), betList, 45, 65))
                            isCombin = true;

                    } else if (betMarketId == 66) {
                        if (fnCheckCombine($(this), betList, 66, 46))
                            isCombin = true;

                    } else if (betMarketId == 46) {
                        if (fnCheckCombine($(this), betList, 46, 66))
                            isCombin = true;

                    } else if (betMarketId == 67) {
                        if (fnCheckCombine($(this), betList, 67, 47))
                            isCombin = true;

                    } else if (betMarketId == 47) {
                        if (fnCheckCombine($(this), betList, 47, 67))
                            isCombin = true;
                    }
                }
                
                if(!isCombin){
                    let price = 1;
                    //let price = $('[data-fixture-id*="' + fixtureId + '"].slip_bet_ing .slip_bet_cell_r').text();
                    if(isMobile){
                        price = $('[data-fixture-id*="' + fixtureId + '"].sports_cart_bet .sports_cart_bet_p').text();
                        price = price == 0 ? 1 : price;
                    }else{
                        $('[data-index*="' + fixtureId + '"].sports_cart_bet .sports_cart_bet_p').each(function () {
                            price = price * Number($(this).text());
                        });
                    }
                    totalOdds = totalOdds / price;
                    $('[data-index*="' + fixtureId + '"]').removeClass('bet_on');
                    $('[data-index*="' + fixtureId + '"]' + '.sports_cart_bet').remove();
                }
                let betSlipCount = getBetSlipCount();
                setBonusPrice(totalOdds, betSlipCount);
            }

            let isDuplicated = false;
            $('.slip_bet_ing').each(function(){
                if($(this).data('index') == betListIndex){
                    isDuplicated = true;
                    return false;
                }
            });

            if(isDuplicated){
                console.log('isDuplicated');
                initForm();
                totalOdds = $('.total_odds').data("total_odds");
            }

            if(isMobile){
                totalOdds = betPrice;

                if(isCombin && !isDuplicated){

                    const curTotalOdds = Number($('.total_odds').text());

                    if(curTotalOdds > 0){
                        totalOdds = totalOdds * curTotalOdds;
                    }
                }
            }else{
                if (obj['betOddsTypes'] != null && obj['betOddsTypes'] != '') {
                    totalOdds = parseFloat(obj['betOddsTypes']).toFixed(2);
                }

                totalOdds = totalOdds == 0 ? 1 : totalOdds;
                totalOdds = totalOdds * parseFloat(betPrice);
                totalOdds = Number(totalOdds).toFixed(2);
            }

            betMarketType = obj['markets_name_origin'];

            if(+totalOdds > 100) {
                alert('최대 배당률을 초과하였습니다. [최대: 100배]');
                totalOdds = $('.total_odds').html();                
                return false;
            }

            $('.total_odds').html(Number(totalOdds).toFixed(2));

            if ($(this).hasClass('sports_table_in_1') || $(this).hasClass('sports_table_in_2') || $(this).hasClass('sports_table_in_xo')) {
                $(this).addClass('bet_on');
            }else {
                $(this).addClass('bet_on');
            }
            
            // class sports_cart_bet slip_bet_ing 
            let html = `
                <li class='sports_cart_bet slip_bet_ing'
                    data-index='${betListIndex}'
                    data-odds-types="${betOddsTypes}"
                    data-bet-id="${betId}"
                    data-bet-name="${betName}"
                    data-bet-price="${betPrice}"
                    data-markets-name="${betMarketType}"
                    data-bet-base-line='${betBaseLine}'
                    data-fixture-start-date='${fixture_start_date}'
                    data-leagues_m_bet_money="${leagues_m_bet_money}"
                >
                    <div width='100%'class='cart_bet'>
                        <div>
                                <span class='sports_cart_bet_font1'>
                                    ${obj['fixture_participants_1_name']}
                                    <img src='/assets_w/images/vs.png' width='20'>
                                    ${obj['fixture_participants_2_name']}
                                </span>
                        </div>
                        <div>
                                <span class='sports_cart_bet_font2'>${betMarketType}</span>
                        </div>
                        <div class="sports_cart_bet_font3">
                                ${obj['fixture_participants_1_name']}
                                <span>${betOddsTypesDisplay}</span>
                                <span class='sports_cart_bet_p'>${betPrice}</span>
                        </div>
                        
                        <a href='javascript:void(0);' class='sports_cart_bet_img'>
                            <img src='/assets_w/images/cart_close.png' class='notify-close-btn' data-index="${betListIndex}" data-bet-id="${betId}">
                        </a>

                    </div>
                </li>
            `;
            $('.slip_tab_wrap').prepend($(html));

            //bet_name
            /*let team1 = gameObj['fixture_participants_1_name'];
            let team2 = gameObj['fixture_participants_2_name'];
            addBetSlip(betId, betPrice, betListIndex, betOddsTypes, betOddsTypesDisplay, betMarketType, team1, team2, betBaseLine, fixtureId, fixture_start_date, leagues_m_bet_money, betMarketId, betName);*/
        
            // 뱃팅슬롯 카운트
            if(isMobile){
                $('.cart_count').text($('.slip_bet_ing').length);
                $('.cart_count2').text($('.slip_bet_ing').length);
            }
            changeWillWinMoney();

            if(alreadyCombin){
                console.log('alreadyCombin');
                alreadyCombinObj.trigger('click');
                if(isMobile) fnSetVisible();
            }

            cartCount();

        });







        $(document).on('click', '.notify-close-btn', function () {
            notifyCloseBtn(this);
        });


        $(document).on('click', '.max_btn', function() {
            maxBtnClick();
        });

        $(document).on('click', '.reset_btn', function() {
            $('#betting_slip_money').val(0);
            changeWillWinMoney();
            betting_impossible = false;
        });

        // 배팅
        $(document).on('click', '.sports_btn1', function () {
            bettingClick();
        }); // end sports_btn1 click


        $(document).on('change', '#betting_slip_money', function () {
            changeWillWinMoney();
        });

        $(document).on('focus', '#betting_slip_money', function () {
            bettingSlipMoneyFocus();
        });

        $(document).on('blur', '#betting_slip_money', function () {
            bettingSlipMoneyBlur();
        });

        /*$(document).on('click', '.soprts_in_acc p', function(j) {
            var dropDown = $(this).closest('li').find('.sports_in');

            //$(this).closest('.accordion').find('p').not(dropDown).slideUp();

            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            } else {
                //$(this).closest('.accordion').find('a.active').removeClass('active');
                $(this).addClass('active');
            }

            dropDown.stop(false, true).slideToggle();

            j.preventDefault();
        });*/

        // 전체삭제
        $(document).on('click','.waste_btn',function(){
            wasteBtn('slip_bet_ing', 2);
        });
    }); // end ready





    
    // 베팅판 보여주기
    // function openBetData(fixture_id){
    //     console.log('openBetData : '+fixture_id);
    //     console.log('selectFixtureId : '+selectFixtureId);
    //     //selectFixtureId = fixture_id;
    //     let betting_html= '';
    //     const today = new Date();
        
    //     // 이미 불러왔으면 불러오지 않는다. livescorelist에서 반복됨
    //     /*if($(".fixture_"+selectFixtureId).length > 0){
    //         //$(".fixture_"+selectFixtureId).slideDown();
    //         return;
    //     }*/
        
    //     sports.forEach(function (sports_id) {
    //         //console.log(live_data);
    //         let list = live_data[sports_id];
    //         if(!list) {
    //             return true;
    //         }
    //         //console.log('openBetData : '+list);
    //         for (const[fixtureKey, fixture_list] of Object.entries(list)) {
    //             if(fixture_id != fixtureKey) continue;
    //             for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
    //                 let isMainBetLock = false;
    //                 // 배구, 야구는 메인만 존재
    //                 // 갬블은 메인메뉴 개념이 없다.
    //                 //if(memuKey > 0) continue;
    //                 betting_html += "<li class='bet_list1_wrap fixture_"+fixtureKey+"' name='fixture_"+fixtureKey+"' style='display:block'>";
    //                 for (const[marketKey, game_list] of Object.entries(menu_list)) {
    //                     let markets_name = game_list[0]['markets_name_origin'];
    //                     let bGameKey = '';
    //                     // 마켓명 표기
    //                     $sportsLineColor = getSportsLineColor(sports_id);
                        
    //                     betting_html += "<a href='#'>"+
    //                             "<div class='bet_list1_wrap_in_title "+$sportsLineColor+"'>"+markets_name+"<span class='bet_list1_wrap_in_title_right'></span></div>"+
    //                             "</a>"+
    //                             "<ul class='bet_list1_wrap_in_new' id='market_"+marketKey+"' style='display:block'>"+
    //                             "<table width='100%' border='0' cellspacing='0' cellpadding='0' style='padding: 5px 0 0 0;'>";
    //                     for (const[bKey, game] of Object.entries(game_list)) {
    //                         bGameKey = game['fixture_id']+'_'+game['markets_id']+'_'+game['bet_base_line']+'_'+game['providers_id'];
    //                         markets_name = game['markets_name'];
    //                         let markets_name_origin = game['markets_name_origin'];
    //                         let markets_display_name = game['markets_display_name'];
    //                         //const timeValue = new Date(game['fixture_start_date']);
    //                         //let betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);
    //                         //betweenTime = 700;
    //                         const checkTime = 600;
    //                         /*let b_find = false;
    //                         for(const betData of game['bet_data']){
    //                             if(betData['bet_status'] == 2){
    //                                 b_find = true;
    //                                 break;
    //                             }
    //                         }

    //                         if(game['bet_status'] != 1 || true == b_find) continue;*/
    //                         //thisMarkettotalCnt += 1;
    //                         betting_html += "<tr>";
    //                         // 승무패
    //                         if(1 == game['menu']) {
    //                             game['bet_data'].forEach(function(betData) {
    //                                 if(betData['bet_name'] === '1'){
    //                                      game['win_bet_id'] = betData['bet_id'];
    //                                      game['win'] = betData['bet_price'];
    //                                      game['win_bet_name'] = betData['bet_name'];
    //                                 }else if(betData['bet_name'] === '2'){
    //                                     game['lose_bet_id'] = betData['bet_id'];
    //                                     game['lose'] = betData['bet_price'];
    //                                     game['lose_bet_name'] = betData['bet_name'];
    //                                 }else{
    //                                     game['draw_bet_id'] = betData['bet_id'];
    //                                     game['draw'] = betData['bet_price'];
    //                                     game['draw_bet_name'] = betData['bet_name'];
    //                                 }
    //                             })
    //                             /*betting_html += "<td class=\"sports_table_in_1 odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + game['win'] +"\" data-odds-type=\"win\" data-bet-id=" + game['win_bet_id'] + " data-bet-price=" + game['win'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "      <div class=\"sports_v_l\">"+ game['fixture_participants_1_name'] +"</div><div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['win_bet_id'] +"\">"+ game['win'] +"</div>\n"+
    //                                 "    </td>\n";
    //                             if(game['draw']){
    //                                 betting_html +=
    //                                 "    <td class=\"sports_table_in_xo odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + game['win'] +"\" data-odds-type=\"draw\" data-bet-id=" + game['draw_bet_id'] + " data-bet-price=" + game['draw'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "      <div class=\"sports_v_l\">무</div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['draw_bet_id'] +"\">"+ game['draw'] +"</div>\n"+
    //                                 "    </td>\n";
    //                             }
    //                             betting_html +=
    //                                 "    <td class=\"sports_table_in_2 odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds-type=\"lose\" data-bet-id=" + game['lose_bet_id'] + " data-bet-price=" + game['lose'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "        <div class=\"sports_l_l\">"+ game['fixture_participants_2_name'] +"</div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['lose_bet_id'] +"\">" + game['lose'] + "</div>\n"+
    //                                 "    </td>\n";*/

    //                             // 배당 표기
    //                             if (Object.keys(game['bet_data']).length == 3) {
    //                                 if((1 == game['bet_status'] && 2 == game['display_status'])){
    //                                     betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                             " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
    //                                             " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                             " data-bet-name='"+game['win_bet_name']+"'"+
    //                                             ">"+game['fixture_participants_1_name']+" <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
    //                                     betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                             " data-odds-type='draw' data-bet-id='"+ game['draw_bet_id'] +"' data-bet-price='"+ game['draw'] +"'"+
    //                                             " data-td-cell='"+game['draw_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                             " data-bet-name='"+game['draw_bet_name']+"'"+
    //                                             ">무 <span class='betin_right bet_font1'>"+game['draw']+"</span></td>";
    //                                     betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                             " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
    //                                             " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                             " data-bet-name='"+game['lose_bet_name']+"'"+
    //                                             ">"+game['fixture_participants_2_name']+" <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
    //                                 }else{
    //                                     isMainBetLock = true;
    //                                     betting_html += "<td class='bet_list_td w30'>"+game['fixture_participants_1_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
    //                                             "<td class='bet_list_td w30'>무<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
    //                                             "<td class='bet_list_td w30'>"+game['fixture_participants_2_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
    //                                 }
    //                             } else {
    //                                 if((1 == game['bet_status'] && 2 == game['display_status'])){
    //                                     betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                             " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
    //                                             " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                             " data-bet-name='"+game['win_bet_name']+"'"+
    //                                             ">"+game['fixture_participants_1_name']+" <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
    //                                     betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                             " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
    //                                             " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                             " data-bet-name='"+game['lose_bet_name']+"'"+
    //                                             ">"+game['fixture_participants_2_name']+" <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
    //                                 }else{
    //                                     isMainBetLock = true;
    //                                     betting_html += "<td class='bet_list_td w50'"+
    //                                             ">"+game['fixture_participants_1_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
    //                                             "<td class='bet_list_td w50'"+
    //                                             ">"+game['fixture_participants_2_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
    //                                 }
    //                             }
    //                             html += "</tr>";
    //                         // 핸디캡
    //                         }else if(2 == game['menu']) {
    //                             let handValue_l = 0;
    //                             let handValue_r = 0;
    //                             let handValue_c = 0;
    //                             let tm_l = '';
    //                             let tm_r = '';
    //                             let bet_line = bet_line_second = 0;
    //                             game['win_bet_name'] = game['lose_bet_name'] = '';
    //                             for (const[bKey, value] of Object.entries(game['bet_data'])) {
    //                                 if(value['bet_name'] == 1) {
    //                                     game['win'] = value['bet_price'];
    //                                     game['win_bet_id'] = value['bet_id'];
    //                                     game['win_bet_line'] = value['bet_line'];
    //                                     game['win_bet_name'] = value['bet_name'];
    //                                     //handValue_l = value['bet_line'].split(' ')[0];
    //                                     bet_line = Number(value['bet_line'].split(' ')[0]);
    //                                     if(typeof value['bet_line'].split(' ')[1] != 'undefined'){
    //                                         bet_line_second = value['bet_line'].split(' ')[1];
    //                                         bet_line_second = bet_line_second.replace('(', '');
    //                                         bet_line_second = bet_line_second.replace(')', '');
    //                                         bet_line_second = bet_line_second.split('-');
    //                                         handValue_l = bet_line + Number(bet_line_second[0]) - Number(bet_line_second[1]);
    //                                     }else{
    //                                         handValue_l = bet_line;
    //                                     }

    //                                     if(handValue_l > 0)
    //                                         handValue_l = '+' + handValue_l.toFixed(1);
    //                                     else
    //                                         handValue_l = handValue_l.toFixed(1);

    //                                     handValue_l = handValue_l == 'NAN' ? value.bet_line : handValue_l;
    //                                     tm_l = game['fixture_participants_1_name'] +"("+handValue_l+")";

    //                                     if(13 == game['markets_id']){
    //                                         let homeScore = Number(value['bet_line'].split(':')[0]);
    //                                         let awayScore = Number(value['bet_line'].split(':')[1]);
    //                                         handValue_l = homeScore - awayScore;
    //                                         tm_l = '승';
    //                                     }

    //                                 }else if(value['bet_name'] == 2) {
    //                                     game['lose'] = value['bet_price'];
    //                                     game['lose_bet_id'] = value['bet_id'];
    //                                     game['lose_bet_line'] = value['bet_line'];
    //                                     game['lose_bet_name'] = value['bet_name'];
    //                                     //handValue_r = value['bet_line'].split(' ')[0];
    //                                     bet_line = Number(value['bet_line'].split(' ')[0]);
    //                                     if(typeof value['bet_line'].split(' ')[1] != 'undefined'){
    //                                         bet_line_second = value['bet_line'].split(' ')[1];
    //                                         bet_line_second = bet_line_second.replace('(', '');
    //                                         bet_line_second = bet_line_second.replace(')', '');
    //                                         bet_line_second = bet_line_second.split('-');
    //                                         handValue_r = bet_line + Number(bet_line_second[0]) - Number(bet_line_second[1]);
    //                                     }else{
    //                                         handValue_r = bet_line;
    //                                     }

    //                                     if(handValue_r > 0)
    //                                         handValue_r = '+' + handValue_r.toFixed(1);
    //                                     else
    //                                         handValue_r = handValue_r.toFixed(1);

    //                                     handValue_r = handValue_r == 'NAN' ? value.bet_line : handValue_r;
    //                                     tm_r = game['fixture_participants_2_name'] +"("+handValue_r+")";

    //                                     if(13 == game['markets_id']){
    //                                         let homeScore = Number(value['bet_line'].split(':')[0]);
    //                                         let awayScore = Number(value['bet_line'].split(':')[1]);
    //                                         handValue_r = homeScore - awayScore;
    //                                         tm_r = '패';
    //                                     }
    //                                 }else {
    //                                     game['draw'] = value['bet_price'];
    //                                     game['draw_bet_id'] = value['bet_id'];
    //                                     game['draw_bet_line'] = value['bet_line'];
    //                                     let homeScore = Number(value['bet_line'].split(':')[0]);
    //                                     let awayScore = Number(value['bet_line'].split(':')[1]);
    //                                     handValue_c = homeScore - awayScore;
    //                                 }
    //                             }

    //                             // 배당 표기
    //                             if(1 == game['bet_status'] && 2 == game['display_status'] && false == isMainBetLock){
    //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                         " data-odds-type='"+game['fixture_participants_1_name']+"("+ handValue_l +")' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
    //                                         " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                         " data-bet-name='"+game['win_bet_name']+"'"+
    //                                         ">"+game['fixture_participants_1_name']+"(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['win']+"</span></td>";
    //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                         " data-odds-type='"+game['fixture_participants_2_name']+"("+ handValue_r +")' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
    //                                         " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                         " data-bet-name='"+game['lose_bet_name']+"'"+
    //                                         ">"+game['fixture_participants_2_name']+"(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['lose']+"</span></td>";
    //                                 betting_html += "</tr>";
    //                             }else{
    //                                 betting_html += "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
    //                                         ">"+game['fixture_participants_1_name']+"(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
    //                                         "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
    //                                         ">"+game['fixture_participants_2_name']+"(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
    //                             }
    //                         }else if(3 == game['menu']) {
    //                                     let over = 0;
    //                                     let over_bet_id = 0;
    //                                     let over_status = 0;
    //                                     let over_base_line = '';
    //                                     let under = 0;
    //                                     let under_bet_id = 0;
    //                                     let under_status = 0;
    //                                     let under_base_line = '';
    //                                     let over_bet_name = under_bet_name = '';
    //                             game['bet_data'].forEach(function(betData) {
    //                                 if(betData['bet_name'] === 'Over'){
    //                                     over = betData['bet_price'];
    //                                     over_bet_id = betData['bet_id'];
    //                                     over_status = betData['bet_status'];
    //                                     over_bet_name = betData['bet_name'];
    //                                     over_base_line = betData['bet_base_line'];
    //                                 }else{
    //                                     under = betData['bet_price'];
    //                                     under_bet_id = betData['bet_id'];
    //                                     under_status = betData['bet_status'];
    //                                     under_bet_name = betData['bet_name'];
    //                                     under_base_line = betData['bet_base_line'];
    //                                 }
    //                             })
                                
    //                             // 배당 표기
    //                             if(1 == over_status && 2 == game['display_status'] && false == isMainBetLock){
    //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ over_status + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                         " data-odds-type='오버("+ over_base_line +")' data-bet-id='"+ over_bet_id  +"' data-bet-price='"+ over +"'"+
    //                                         " data-td-cell='"+over_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                         " data-bet-name='"+over_bet_name+"'"+
    //                                         ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr2.gif' style='margin-right: 5px'>"+over+"</span></td>";
    //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ under_status + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
    //                                         " data-odds-type='언더("+ under_base_line +")' data-bet-id='"+ under_bet_id  +"' data-bet-price='"+ under +"'"+
    //                                         " data-td-cell='"+under_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
    //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
    //                                         " data-bet-name='"+under_bet_name+"'"+
    //                                         ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr1.gif' style='margin-right: 5px'>"+under+"</span></td>";
    //                             }else{
    //                                 betting_html += "<td class='bet_list_td w50' data-bet-status='"+ over_status + "'"+
    //                                         ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
    //                                         "<td class='bet_list_td w50' data-bet-status='"+ under_status + "'"+
    //                                         ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
    //                             }
    //                             betting_html += "</tr>";
    //                         // 기타
    //                         }else if(4 == game['menu']) {
    //                             let yesBetPrice = '';
    //                             let yesBetId = '';
    //                             let noBetPrice = '';
    //                             let noBetId = '';
    //                             let bet_name_yes = bet_name_no = '';
    //                             game['bet_data'].forEach(function(betData) {
    //                                 if(betData['bet_name'] === 'Yes'){
    //                                     yesBetPrice = betData['bet_price'];
    //                                     yesBetId = betData['bet_id'];
    //                                     bet_name_yes = betData['bet_name'];
    //                                 }else{
    //                                     noBetPrice = betData['bet_price'];
    //                                     noBetId = betData['bet_id'];
    //                                     bet_name_no = betData['bet_name'];
    //                                 }
    //                             })

    //                             /*if(game['bet_data'][0]['bet_name'] === 'No'){
    //                                 let betData_no = game['bet_data'][0];
    //                                 let betData_yes = game['bet_data'][1];
    //                                 let display_bet_name_no = '아니요';//betNameToDisplay_new(betData_no['bet_name'],game['markets_id']);
    //                                 let display_bet_name_yes = '예';//betNameToDisplay_new(betData_yes['bet_name'],game['markets_id']);

    //                                 betting_html += "<tr>"
    //                                 betting_html += "<td class=\"sports_table_in_1 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + yesBetPrice +" \" data-odds-type=\"" + display_bet_name_yes + "\" data-bet-id=" + yesBetId + " data-bet-price=" + yesBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "        <div class=\"sports_v_l\">"+ display_bet_name_yes +" </div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + yesBetId +"\">"+ yesBetPrice +" </div>\n"+
    //                                 "    </td>\n"+
    //                                 "    <td class=\"sports_table_in_2 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + noBetPrice + "\" data-odds-type=\"" + display_bet_name_no + "\" data-bet-id=" + noBetId + " data-bet-price=" + noBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "        <div class=\"sports_l_l\">"+ display_bet_name_no +" </div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + noBetId +"\">" + noBetPrice + " </div>\n"+
    //                                 "    </td>\n";
    //                                 betting_html += "</tr>";
    //                             }else if(game['bet_data'][1]['bet_name'] === 'No'){
    //                                 let betData_no = game['bet_data'][1];
    //                                 let betData_yes = game['bet_data'][0];
    //                                 let display_bet_name_no = '아니요';//betNameToDisplay_new(betData_no['bet_name'],game['markets_id']);
    //                                 let display_bet_name_yes = '예';//betNameToDisplay_new(betData_yes['bet_name'],game['markets_id']);

    //                                 betting_html += "<tr>"
    //                                 betting_html += "<td class=\"sports_table_in_1 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + yesBetPrice +" \" data-odds-type=\"" + display_bet_name_yes + "\" data-bet-id=" + yesBetId + " data-bet-price=" + yesBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "        <div class=\"sports_v_l\">"+ display_bet_name_yes +" </div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + yesBetId +"\">"+ yesBetPrice +" </div>\n"+
    //                                 "    </td>\n"+
    //                                 "    <td class=\"sports_table_in_2 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + noBetPrice + "\" data-odds-type=\"" + display_bet_name_no + "\" data-bet-id=" + noBetId + " data-bet-price=" + noBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
    //                                 "        <div class=\"sports_l_l\">"+ display_bet_name_no +" </div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + noBetId +"\">" + noBetPrice + " </div>\n"+
    //                                 "    </td>\n";
    //                                 betting_html += "</tr>";
    //                             }else{
    //                                 let count = game['bet_data'].length;
    //                                 for(let i=0; i<count; ++i){
    //                                     let betData = game['bet_data'][i];
    //                                     //let display_bet_name = StatusUtil::betNameToDisplay_new(betData['bet_name']);
    //                                     let display_bet_name = betData['bet_name'];
    //                                     betting_html += "<tr>"
    //                                             +"<td class='sports_table_in_1 odds_btn' data-index=\"" + bGameKey + "\" data-odds-type=\"win\" data-bet-id=" + betData['bet_id'] + " data-bet-price='"+betData['bet_price'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}`+"'>"
    //                                             +"<div class='sports_v_l'>"+display_bet_name+" </div> <div class='sports_v_r'>"+betData['bet_price']+"</div>"
    //                                             +"</td>";
    //                                     if((i+1)%3 == 0){
    //                                         betting_html += "</tr>"
    //                                                 +"<tr>";
    //                                     }
    //                                 }
    //                                 betting_html += "</tr>";
    //                             }*/
    //                         } // end game['menu']
    //                         betting_html += "</tr>\n";

    //                         //betListIndex ++;
    //                     } // end game_list
    //                     betting_html += "</table>";
    //                     betting_html += "</ul>";
    //                 } // end menu_list
    //             } // end fixture_list
    //         }
    //     });
        
    //     //$('[data-bet-id*="' + fixtureId + '"]
    //     // 이전경기 닫기
    //      $(".dropdown3 li").remove();
    //     //$('.dropdown3[fixture_="' + item +'"]').remove();
    //     //console.log(betting_html);
    //     $(".dropdown3").append(betting_html);
        
    //     // 배팅슬립에 있는 배당 선택처리
    //     /*$('.slip_bet_ing').each(function(item) {
    //         console.log(item);
    //         const betListIndex = $(item).data('index');
    //         //$('[data-td-cell*="' + betId + '_' + fixture_start_date + '"]').addClass('bet_on');
    //         //$('[data-bet-id*="' + betId + '"]').addClass('bet_on');
    //     });*/
    // }




</script>

<script>
    let $location_id = 0;      // 미국,영국,한국,일본,중국 
    let $league_id = 0;        // 각각 스포츠의 리그 예) 농구: NBA,KBL....
    let $sports_id = 0;        // 축구:6048,야구,농구,배구,이스포츠,아이스하키,UFC
    let $league_name = ``;     // 검색어의 비슷한 값은 다 나온다.
    
    let $scrollNum = 1;     // infinite scroll num
    let docHBefore = 0;     // infinite scroll check

    $(function(){
        getClassicList();   // get classic data
        cartCount();        // mobile cart count


        // search
        $(document).on("click", "#league_name_ajax_button", function(e){
            e.preventDefault();

            const keyword = $("#league_name_ajax").val();
            if( keyword.length < 1 ){
                alert("검색어를 입력해주세요");
                $("#league_name_ajax").focus();
                return false;
            }

            resetKeyword();
            $ListDataRefresh = true;
            $league_name = keyword;
            getClassicList();
        });
        
        // response cart open
        $(document).on("click", ".btn__cart-open", function(e){
            e.preventDefault();
            $(document).find(".sports_wide_right").toggleClass("active");
        });
        $(document).on("click", ".btn__cart_close, .cart_bg", function(e){
            e.preventDefault();
            $(document).find(".sports_wide_right").removeClass("active");
        });



        // lnb click => slide
        $(document).find('.dropdown').tendina({
            // This is a setup made only
            // to show which options you can use,
            // it doesn't actually make sense!
            animate: true,
            speed: 300,
            onHover: false,
            hoverDelay: 300,
            //activeMenu: $('#deepest'),
            openCallback: function(clickedEl) {
                console.log('Hey dude!');
            },
            closeCallback: function(clickedEl) {
                console.log('Bye dude!');
            }

        })
        // $(document).on("click", ".dropdown li.menu1", function(){
        //     $(this).addClass("on");
        //     $(this).siblings().removeClass("on");
        // });

        // lnb click => keyword setting => data call
        $(document).on("click", ".dropdown .lnb_all", function(e){
            e.preventDefault();
            $ListDataRefresh = true;
            // console.log('all', $(this));
            resetKeyword();
            getClassicList();
        });
        $(document).on("click", ".dropdown .lnb_sports", function(e){
            e.preventDefault();
            $ListDataRefresh = true;
            resetKeyword();
            $sports_id = $(this).parents(".menu1").data("sports_id");
            getClassicList();

        });
        $(document).on("click", ".dropdown .lnb_league", function(e){
            e.preventDefault();
            $ListDataRefresh = true;
            resetKeyword();
            $sports_id = $(this).parents(".menu1").data("sports_id");
            $league_id = $(this).parents("li").data("league_id");
            getClassicList();
        });


        // top tab click
        $(document).on("click", ".classic_title_tab li a", function(e){
            e.preventDefault();

            $ListDataRefresh = true;
            resetKeyword();
            $sports_id = $(this).parents("li").data("sports_id");
            getClassicList();
        });

    });



    $(window).on("scroll", function(e){
        let scroll = $(window).scrollTop();
        let winH = $(window).height();

        let myScroll = Math.ceil(scroll + winH);
        let docH = $(document).height();
        
        // data infinite scroll
        if( (myScroll >= docH) && (docHBefore != docH) ){
            docHBefore = docH;
            $scrollNum++;
            getClassicList();
        }

        // lnb, cart fixed
        if(scroll > 121 ){      $("body").addClass("fixed");
        } else {                $("body").removeClass("fixed");
        }
    })



    const cartCount = function(){
        //const cart_count = $(document).find(".sports_wide_right .sports_cart_bet").length;
        const cart_count = getBetSlipCount();
        $(document).find(".cart_open .cart_count2").html(cart_count);

        // bonus active
        $(document).find(".sports_list_bonus ul li").removeClass("active");
        if( cart_count >= 3 && cart_count < 5){
            $(document).find(".odds_3_folder_bonus").addClass("active");

        } else if ( cart_count > 4 &&  cart_count < 7 ){
            $(document).find(".odds_5_folder_bonus").addClass("active");
        
        } else if ( cart_count >= 7 ){
            $(document).find(".odds_7_folder_bonus").addClass("active");
        }

    }


    // keyword reset
    const resetKeyword = function(){
        $location_id = 0;
        $league_id = 0;
        $sports_id = 0;
        $league_name = '';

        $scrollNum = 1;     // infinite scroll num
        docHBefore = 0;     // infinite scroll check
    }



    // get classic data
    const getClassicList = function(param){
        // page = param ? (param-1)*20 : 0;
        // page = param ? (param*20) : 1;        // It starts with 1
        // page = param ? param : 1;
        // curPageNo = param ? param : 1;

        let request_data = {
            'page' : $scrollNum,
            'location_id' : $location_id,
            'league_id' : $league_id,
            'sports_id' : $sports_id,
            'league_name' : $league_name,
            // 'orderby_type' : $orderby_type
        }
        console.log('request_data', request_data,);
        call_ajax_loading("classic_ready", "/web/classic/ajax_index", request_data);
    }
    const result_classic_ready = function(response){
        console.log('get response', response);


        $arr_config = {
            'pre_limit_money' : response.arr_config.pre_limit_money,
            'pre_max_money' : response.arr_config.pre_max_money,
            'inplay_no_betting_list' : response.arr_config.inplay_no_betting_list,
            'limit_folder_bonus' : response.arr_config.limit_folder_bonus,
            'odds_3_folder_bonus' : response.arr_config.odds_3_folder_bonus,
            'odds_4_folder_bonus' : response.arr_config.odds_4_folder_bonus,
            'odds_5_folder_bonus' : response.arr_config.odds_5_folder_bonus,
            'odds_6_folder_bonus' : response.arr_config.odds_6_folder_bonus,
            'odds_7_folder_bonus' : response.arr_config.odds_7_folder_bonus,
            'service_bonus_folder' : response.arr_config.service_bonus_folder,
            'service_real' : response.arr_config.service_real,
            'service_sports' : response.arr_config.service_sports
        };
        maxBetMoney = $arr_config.pre_max_money;
        limitBetMoney = $arr_config.pre_limit_money;
        is_betting_slip = response.is_betting_slip;
        limit_folder_bonus = response.arr_config.limit_folder_bonus;
        serverName = response.serverName;
        if( is_betting_slip == 'ON' ){
            $(document).find(".sports_cart_title_right img").attr("src", "/assets_w/images/cart_fix1.png");
        }

        // bonus
        $(document).find(".odds_3_folder_bonus .bonus_txt").html($arr_config.odds_3_folder_bonus);
        $(document).find(".odds_5_folder_bonus .bonus_txt").html($arr_config.odds_5_folder_bonus);
        $(document).find(".odds_7_folder_bonus .bonus_txt").html($arr_config.odds_7_folder_bonus);
        // money
        $(document).find(".max_bet_money").html( format_money($arr_config.pre_max_money) );
        $(document).find(".limit_Bet_Money").html( format_money($arr_config.pre_limit_money) );


        // **************************************   //set lnb ( use sports )
        let $sports = response.sports;                      // 6046: {id: 6046, name:'축구', count: 0}

        $sports = Object.values($sports); 
        $sports = $sports.sort((a, b) => {  return a.order_index - b.order_index });

        // let $total_sports_count = 0;                // total cnt
        let $resultLNB = `
            <li class="menu1" data-sports_id="0" data-sports_name="전체보기">
                <a href="#" class="lnb_all">
                    <div class="left_list1" id="left_menu_0">
                        <span class="menu_left">
                            <img src="<?= $imageBasePath ?>/sports/icon_game0.png" width="18">
                            &nbsp;&nbsp;&nbsp;
                            전체보기
                        </span>
                        <span class="m enu_right">
                            <span class="menu_right_box" id="sports_count_0">0</span>
                        </span>
                    </div>
                </a>
            </li>
        `;

        // lnb 1depth => sports
        for(let[key, value] of Object.entries($sports)){

            let $sports_count = value.count;
            let $sports_id = value.id;
            let $sports_name = value.name;

            $resultLNB += `
                <li class="menu1" data-sports_id="${$sports_id}" data-sports_name="${$sports_name}">
                    <a href="#" class="lnb_sports">
                        <div class="left_list1" id="left_menu_${$sports_id}">
                            <span class="menu_left">
                                <img src="<?= $imageBasePath ?>/sports/icon_game${$sports_id}.png" width="18">&nbsp;&nbsp;&nbsp;
                                ${$sports_name}
                            </span>
                            <span class="m enu_right">
                                <span class="menu_right_box" id="sports_count_${$sports_id}">${$sports_count}</span>
                            </span>
                        </div>
                    </a>
                    <ul style="display:none;">
            `;

            let $leagues = value.leagues;
            if( $leagues ){ // Prints only when there is a league.

                for(let[key, value] of Object.entries($leagues)){
                    let $leagues_count = value.count;
                    let $leagues_display_name = value.display_name;
                    let $leagues_fixture_location_id = value.fixture_location_id;
                    let $leagues_id = value.id;

                    $resultLNB += `
                                <li data-league_id="${$leagues_id}" data-league_name="${$leagues_fixture_location_id}">
                                    <a href="#" class="lnb_league">
                                        <span class="left_list1_in">
                                            ${$leagues_display_name}
                                            <span class="menu_right_box">
                                                ${$leagues_count}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                    `;
                }
            }
            $resultLNB += `
                    </ul>
                </li>
            `;
        }
        $(document).find(".con_box_left .dropdown").html($resultLNB);
        $(document).find("#sports_count_0").html(response.totalCnt);    // totalCnt
        // end lnb

        // lnb active
        // console.log($location_id, $league_id, $sports_id, $league_name);
        if( $sports_id != 0 ){
            $(document).find(`.dropdown > li[data-sports_id=${$sports_id}] > ul`).show();
        }


        

        // **************************************   //set top Tab
        let $resultTabHTML = ``;
        $resultTabHTML += `<li data-sports_id="0"><a href="#"><span class="tab">전체</span></a></li>`;
        for(let[key, value] of Object.entries($sports)){

            let $sports_id = value.id;
            let $sports_name = value.name;

            $resultTabHTML += `
                <li data-sports_id="${$sports_id}"><a href="#"><span class="tab">${$sports_name}</span></a></li>
            `;
        }
        $(document).find(".classic_title_tab").html($resultTabHTML);
        
        $(document).find(`.classic_title_tab > li span`).removeClass('tabon');
        $(document).find(`.classic_title_tab > li[data-sports_id=${$sports_id}] span`).addClass('tabon');



        
        
        // **************************************   //set game list
        if( $ListDataRefresh ){
            console.log('첫 로딩 or 메뉴클릭');
            $(document).find(".dropdown2").html('');    // list reset
            $ListDataRefresh = false;
        }

        // gameList => title
        let $resultGameList = ``;
        let $gameList = response.gameList;  // "2022-08-30 18:00:00" : { "9152199" : [{...}], "915723" : [{...}] }

        for(const[key, value] of Object.entries($gameList)){
            // key : 2022-09-07 16:30:00
            // value : {9160014:array(1), 9160016: array(1) }

            const $gameByLeague = value;
            for(const[key, value] of Object.entries($gameByLeague)){
                // key : 9160020
                // value : [{...}, {...}]

                // let $fixture_location_image_path = value[0].fixture_location_image_path;
                let $fixture_sport_id = value[0].fixture_sport_id;
                let $fixture_league_name = value[0].fixture_league_name;
                let $markets_name_origin = value[0].markets_name_origin;
                let $fixture_start_date = value[0].fixture_start_date;
                let $game_date = $fixture_start_date.split(" ")[0].split("-");
                $game_date = `${$game_date[1]}-${$game_date[2]}`;
                let $game_time = $fixture_start_date.split(" ")[1].split(":");
                $game_time = `${$game_time[0]}:${$game_time[1]}`;

                // // flag icon
                let $fixture_league_image_path = value[0].fixture_league_image_path;        // "/images/flag_eng/indonesia.png"  or  "mlb.png"
                let $fixture_location_image_path = value[0].fixture_location_image_path;    // "/images/flag/58.png"
                
                // 리그이미지가 없다.
                let $leagueImagePath = ``;
                if( $fixture_league_image_path.includes('flag_eng') ){
                    $leagueImagePath = $fixture_league_image_path;
                } else {
                    $leagueImagePath = '<?= $imageBasePath ?>/league/'+$fixture_league_image_path;
                }
                
                // title
                $resultGameList += `
                        <div class="sport_title">
                            <div class="sport_league">
                                <img src="${$leagueImagePath}" width="28" alt="flag icon" onError="this.src='/images/flag_eng/international.png'">
                                <img src="/assets_w/images/line.png">
                                <img src="<?= $imageBasePath ?>/sports/icon_game${$fixture_sport_id}.png" alt="icon" width="18">
                                <img src="/assets_w/images/line.png">
                                ${$fixture_league_name}
                            </div>
                            <div class="sport_title_time">
                                <span class="font04">${$markets_name_origin}</span>
                                <img src="/assets_w/images/line.png">
                                ${$game_date} ${$game_time}
                            </div>
                        </div>
                `;

                const $gameByFixture = value;
                for(const[key, value] of Object.entries($gameByFixture)){
                    // key : 0, 1
                    // value : { fixture_id: "9194299", fixture_league_id: "30216", ...}
                        const $useData = value;

                        let $fixture_id = $useData.fixture_id;                                              // 9152199
                        let $fixture_league_id = $useData.fixture_league_id;                                // 4146
                        let $fixture_league_image_path = $useData.fixture_league_image_path;                // npb.png
                        let $fixture_league_name = $useData.fixture_league_name;                            // NPB
                        let $fixture_location_id = $useData.fixture_location_id;                            // 148
                        let $fixture_location_image_path = $useData.fixture_location_image_path;            // /images/flag/148.png
                        let $fixture_location_name = $useData.fixture_location_name;                        // 일본

                        let $fixture_participants_1_name = $useData.fixture_participants_1_name;            // 라쿠텐
                        let $fixture_participants_2_name = $useData.fixture_participants_2_name;            // 오릭스

                        let $fixture_sport_id = $useData.fixture_sport_id;                                  // 154914
                        let $fixture_sport_name = $useData.fixture_sport_name;                              // 야구
                        let $fixture_start_date = $useData.fixture_start_date;                              // 2022-08-30 18:00:00
                        
                        let $leagues_m_bet_money = $useData.leagues_m_bet_money;                            // 3000000
                        let $leagues_m_bet_money_display = $leagues_m_bet_money;
                        if( Number($leagues_m_bet_money) > 10000 ){
                            $leagues_m_bet_money_display = `${Number($leagues_m_bet_money) / 10000}만`
                        }

                        let $limit_bet_price = $useData.limit_bet_price;                                    // 1.60

                        let $max_bet_price = $useData.max_bet_price;                                        // 2.35
                        let $providers_id = $useData.providers_id;                                          // -100
                        let $start_date = $useData.start_date;                                              // 18:00

                        let $bet_base_line = $useData.bet_base_line;
                        let $bet_line = $useData.bet_line;

                        let $markets_name_origin = $useData.markets_name_origin;                            // 오버언더 연장포함
                        let $markets_id = $useData.markets_id;                                              // 28
                        let $bet_data = $useData.bet_data;
                        let $menu = $useData.menu;                                                          // 마켓종류 1:승무패,승패, 2:핸디캡, 3:오버언더

                        // 1:승무패,   2: 오버언더,    3:햅디캡,   28:오버언더연장포함,    52:승패,    226:숭패 연장포함,   342:핸디캡 연장포함
                        let $markets_name_origin_color = '';
                        let vsText = `VS`;
                        if( $markets_id == "1" ){                                       // 1:승무패 (1, 2, X)
                                $markets_name_origin_color = `state4`;

                        } else if ( $markets_id == "52" ){                              // 52:승패 (1, 2)
                                $markets_name_origin_color = `state1`;

                        } else if ( $markets_id == "2" || $markets_id == "28" ){        // 2:오버언더, 28:오버언더 연장포함 (Over, Under)
                                $markets_name_origin_color = `state3`;
                                $fixture_participants_1_name = `오버`;
                                $fixture_participants_2_name = `언더`;
                                vsText = $bet_base_line;

                        } else if ( $markets_id == "3" ||  $markets_id == "342" ){      // 3:핸디캡 (1, 2), 342 : 핸디캡 연장 포함
                                $markets_name_origin_color = `state2`;
                                $handValue_l = $handValue_r = 0;
                                
                                $bet_line_l = $bet_data["1"]["bet_line"];
                                $bet_line_l = $bet_line_l.split(' ')[0];
                                if(isset($bet_line_l.split(' ')[1])){
                                    $bet_line_second = $bet_line_l.split(' ')[1];
                                    $bet_line_second = $bet_line_second.replace('(', '');
                                    $bet_line_second = $bet_line_second.replace(')', '');
                                    $bet_line_second = $bet_line_second.split('-');
                                    $handValue_l = $bet_line_l + $bet_line_second[0] - $bet_line_second[1];
                                }else{
                                    $handValue_l = $bet_line_l;
                                }
                                
                                if($handValue_l > 0){
                                    $handValue_l = '+'+$handValue_l;
                                }else{
                                    $handValue_l = $handValue_l;
                                }
                                
                                $bet_line_r = $bet_data["2"]["bet_line"];
                                $bet_line_r = $bet_line_r.split(' ')[0];
                                if(isset($bet_line_r.split(' ')[1])){
                                    $bet_line_second = $bet_line_r.split(' ')[1];
                                    $bet_line_second = $bet_line_second.replace('(', '');
                                    $bet_line_second = $bet_line_second.replace(')', '');
                                    $bet_line_second = $bet_line_second.split('-');
                                    $handValue_r = $bet_line_r + $bet_line_second[0] - $bet_line_second[1];
                                }else{
                                    $handValue_r = $bet_line_r;
                                }
                                
                                if($handValue_r > 0){
                                    $handValue_r = '+'+$handValue_r;
                                }else{
                                    $handValue_r = $handValue_r;
                                }
                                

                                $fixture_participants_1_name = `${$fixture_participants_1_name} (${$handValue_l})`;
                                $fixture_participants_2_name = `(${$handValue_r}) ${$fixture_participants_2_name}`;
                                vsText = $handValue_l;

                        } else if ( $markets_id == "226" ){ // 226:승패 연장포함 (1, 2)
                                $markets_name_origin_color = `state5`;

                        }


                        let $bet_data_Over = $bet_data.Over || $bet_data["1"];
                        let $bet_data_Under = $bet_data.Under || $bet_data["2"];
                        let $bet_data_Draw = $bet_data["X"] || null;
                        

                        if( !$bet_data_Over || !$bet_data_Under ){
                            console.log('error!!', $fixture_start_date, $fixture_id);
                            continue;
                        }


                        let $handicap_icon = ``;
                        if( $markets_id == "3" || $markets_id == "342"){
                            $handicap_icon = `<img src="/assets_w/images/icon_h.gif" alt="icon"> `;
                        }

                        let $over_icon = ``;
                        let $under_icon = ``;
                        if( $markets_id == "2" || $markets_id == "28" ){
                            $over_icon = `<img src="/assets_w/images/arr2.gif" alt="icon">`;
                            $under_icon = ` <img src="/assets_w/images/arr1.gif" alt="icon">`;
                        }

                        let $bet_status_text = `베팅`;
                        let $bet_status_lock = ``;
                        if ( $bet_data_Over.bet_status == "2" ){    // lock
                            $bet_status_text = `마감`;
                            $bet_status_lock = `bet_status_lock`;
                        }

                        
                        // set betList_new
                        let bGameKey = `${$fixture_id}_${$markets_id}_${$bet_base_line}_${$providers_id}`;
                        betList_new.set(bGameKey, $useData);


                        // list
                        $resultGameList += `
                            <div class="sport_title_list bet_list1_wrap bettingInfo bet_list1_wrap_on classic_game_list ${$bet_status_lock}" id="fixture_row_${$fixture_id}">
                                <ul>
                                    <li class="sport_time bet1">${$game_date} ${$game_time}</li>
                                    <li class="classic_sport_state ${$markets_name_origin_color}">${$markets_name_origin}</li>
                        `;

                        // 승
                        if ( $bet_data_Over.bet_status == "2" ){    // lock
                            
                            $resultGameList += `
                                        <li class="sport_team1 bet_team1">
                                                <span class="team_l">${$fixture_participants_1_name}</span>
                                                <span class="team_r"><img src="/images/icon_lock.png" alt="lock" width="13"></span>
                                        </li>
                            `;

                        } else {
                            
                            $resultGameList += `
                                        <li class="sport_team1 bet_team1 odds_btn"
                                            data-bet-status="${$bet_data_Over.bet_status}"
                                            data-index="${bGameKey}"
                                            data-fixture-id="${$fixture_id}"
                                            data-odds-type="win"
                                            data-bet-id="${$bet_data_Over.bet_id}"
                                            data-bet-price="${$bet_data_Over.bet_price}"
                                            data-td-cell="${$bet_data_Over.bet_id}_${$fixture_start_date}"
                                            data-markets_name="undefined"
                                            data-markets_name_origin="${$markets_name_origin}"
                                            data-markets_display_name="undefined"
                                            data-bet-name="${$bet_data_Over.bet_name}"
                                            data-leagues_m_bet_money="${$leagues_m_bet_money}"
                                        >
                                                <span class="team_l">${$fixture_participants_1_name}</span>
                                                <span class="team_r">${$over_icon}${$handicap_icon}${$bet_data_Over.bet_price}</span>
                                        </li>
                            `;

                        }

                        
                        // 무
                        if( !$bet_data_Draw ){
                                    $resultGameList += `
                                                <li class="sport_tie bet_vs">
                                                        <span class="">${vsText}</span>
                                                </li>
                                    `;

                        } else {

                            if ( $bet_data_Over.bet_status == "2" ){    // lock
                                    $resultGameList += `
                                                <li class="sport_tie bet_vs">
                                                        <span class="bet_font1"><img src="/images/icon_lock.png" alt="lock" width="13"></span>
                                                </li>
                                    `;
                            } else {

                                    $resultGameList += `
                                                <li class="sport_tie bet_vs odds_btn"
                                                    data-bet-status="${$bet_data_Draw.bet_status}"
                                                    data-index="${bGameKey}"
                                                    data-fixture-id="${$fixture_id}"
                                                    data-odds-type="draw"
                                                    data-bet-id="${$bet_data_Draw.bet_id}"
                                                    data-bet-price="${$bet_data_Draw.bet_price}"
                                                    data-td-cell="${$bet_data_Draw.bet_id}_${$fixture_start_date}"
                                                    data-markets_name="undefined"
                                                    data-markets_name_origin="${$markets_name_origin}"
                                                    data-markets_display_name="undefined"
                                                    data-bet-name="${$bet_data_Draw.bet_name}"
                                                    data-leagues_m_bet_money="${$leagues_m_bet_money}"
                                                >
                                                        <span class="bet_font1">${$bet_data_Draw.bet_price}</span>
                                                </li>
                                    `;


                            }
                        }

                        // 패
                        if ( $bet_data_Over.bet_status == "2" ){    // lock
                                $resultGameList += `
                                            <li class="sport_team2 bet_team2">
                                                    <span class="team_l"><img src="/images/icon_lock.png" alt="lock" width="13"></span>
                                                    <span class="team_r">${$fixture_participants_2_name}</span>
                                            </li>
                                `;
                        } else {
                                $resultGameList += `
                                            <li class="sport_team2 bet_team2 odds_btn"
                                                data-bet-status="${$bet_data_Under.bet_status}"
                                                data-index="${bGameKey}"
                                                data-fixture-id="${$fixture_id}"
                                                data-odds-type="lose"
                                                data-bet-id="${$bet_data_Under.bet_id}"
                                                data-bet-price="${$bet_data_Under.bet_price}"
                                                data-td-cell="${$bet_data_Under.bet_id}_${$fixture_start_date}"
                                                data-markets_name="undefined"
                                                data-markets_name_origin="${$markets_name_origin}"
                                                data-markets_display_name="undefined"
                                                data-bet-name="${$bet_data_Under.bet_name}"
                                                data-leagues_m_bet_money="${$leagues_m_bet_money}"
                                            >
                                                    <span class="team_l">${$bet_data_Under.bet_price}${$under_icon}${$handicap_icon}</span>
                                                    <span class="team_r">${$fixture_participants_2_name}</span>
                                            </li>
                                            
                                            
                                `;
                        }



                        $resultGameList += `
                                            <!--
                                            <li class="classic_sport_state bet_max bet8">
                                                <span class="">${$leagues_m_bet_money_display}</span>
                                            </li>
                                            -->
                                            <li class="sport_ing bet7">${$bet_status_text}</li>
                                        </ul>
                                    </div>
                        `;






                }
            }
        }

        // 검색한 키워드가 있으면 데이터를 갱신한다.
        /*const keyword = $("#league_name_ajax").val();
        if(1 < keyword.length){
            $(document).find(".dropdown2").html($resultGameList);
        }else{
            $(document).find(".dropdown2").append($resultGameList);
        }*/
        
        $(document).find(".dropdown2").append($resultGameList);

    }

</script>

</body>
</html>