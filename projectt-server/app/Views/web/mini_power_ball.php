<?= view('/web/common/header') ?>
<?php

use App\Util\StatusUtil; ?>
<?php
// 배팅 유저별 최대배당금 자동 계산
foreach ($member_bet as $key => $value) {
    if ($game_data->id == $value->ls_fixture_id) {

        if ($betList[$value->ls_markets_id]->markets_id == 14001 || $betList[$value->ls_markets_id]->markets_id == 14002) {
            $betList[14001]->max = isset($betList[14001]->max) ? $betList[14001]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
            $betList[14002]->max = isset($betList[14002]->max) ? $betList[14002]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
        } else if ($betList[$value->ls_markets_id]->markets_id == 14003 || $betList[$value->ls_markets_id]->markets_id == 14004) {
            $betList[14003]->max = isset($betList[14003]->max) ? $betList[14003]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
            $betList[14004]->max = isset($betList[14004]->max) ? $betList[14004]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
        } else if ($betList[$value->ls_markets_id]->markets_id == 14005 || $betList[$value->ls_markets_id]->markets_id == 14006 || $betList[$value->ls_markets_id]->markets_id == 14007) {
            $betList[14005]->max = isset($betList[14005]->max) ? $betList[14005]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
            $betList[14006]->max = isset($betList[14006]->max) ? $betList[14006]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
            $betList[14007]->max = isset($betList[14007]->max) ? $betList[14007]->max - ($value->total_bet_money) : ($game_config->max) - ($value->total_bet_money);
        }
    }
}
foreach ($betList as $key => $value) {
    if (isset($value->max) && $value->max < 0) {
        $value->max = 0;
    }
}
// echo '<pre>';
//     var_dump('----------------');
//     var_dump($value -> ls_fixture_id);
//     var_dump('---');
//     var_dump($betList[10001]);
//     var_dump($betList[10002]);
//     var_dump($betList[10003]);
//     var_dump($betList[10004]);
//     var_dump($betList[10005]);
//     var_dump($betList[10006]);
//     var_dump($betList[10007]);
//     var_dump('---');
// var_dump($betList);
//     var_dump('----------------');
// echo '</pre>';
?>

<!-- <div id="wrap"> -->
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap"><div class="title">미니게임</div></div>

<div id="mini_wide_wrap">
    <!-- mini_menu_info -->
    <div class="mini_wide_left">
    	<div class="mini_left_title">
            <div class="mini_left_title_game">엔트리 파워볼</div>
            <div class="mini_left_title_box">제 <span class="mini_left_title_box_font displayRound"></span><span class="mini_left_title_box_font round" style="display: none"></span> 회차</div>
            <div class="mini_left_title_box"><span class="bettingTimerText">베팅마감</span> <span class="mini_left_title_box_font remain_time">00:00</span></div>
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

    <!-- mini_center_movie -->
    <div class="mini_wide_center">
    	<div class="mini_title_wrap">
        	<div class="mini_title">엔트리 파워볼 &nbsp;&nbsp; <span class="mini_round"> 제 <span class="mini_left_title_box_font displayRound"></span> 회차 </span> &nbsp;&nbsp;<span class="mini_time mini_left_title_box_font remain_time"></span></span></div>
        </div>
		<div class="mini_movie_wrap">
        <div class="mini_movie_inner"><iframe defer src="https://ntry.com/scores/powerball/live.php" width="830" height="641" scrolling="no" frameborder="0"></iframe></div>
        </div> 


    <!-- mini_betting_cart -->
        <div class="mini_wide_right">
    	<div class="mini_cart_title">BETTING SLIP</div>
        <div class="mini_cart_wrap">
            <table width="100%" cellspacing="2" cellpadding="0">
                <tr>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14001, <?= $betList[14001]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14001]->max) ? $betList[14001]->max : $game_config->max ?> ) "><span class="mini_power_btn1"><span class="mini_power_font markets_id_14001"><?= $betList[14001]->markets_name ?></span><br><?= $betList[14001]->bet_price ?></span></a></td>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14002, <?= $betList[14002]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14002]->max) ? $betList[14002]->max : $game_config->max ?> ) "><span class="mini_power_btn2"><span class="mini_power_font markets_id_14002"><?= $betList[14002]->markets_name ?></span><br><?= $betList[14002]->bet_price ?></span></a></td>                                                                                                                  
                </tr>
            </table>
            <table width="100%" cellspacing="2" cellpadding="0">
                <tr>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14004, <?= $betList[14004]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14004]->max) ? $betList[14004]->max : $game_config->max ?> ) "><span class="mini_power_btn1"><span class="mini_power_font markets_id_14004"><?= $betList[14004]->markets_name ?></span><br><?= $betList[14004]->bet_price ?></span></a></td>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14003, <?= $betList[14003]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14003]->max) ? $betList[14003]->max : $game_config->max ?> ) "><span class="mini_power_btn2"><span class="mini_power_font markets_id_14003"><?= $betList[14003]->markets_name ?></span><br><?= $betList[14003]->bet_price ?></span></a></td>                                                                                                                  
                </tr>
            </table>
            <table width="100%" cellspacing="2" cellpadding="0">
                <tr>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14005, <?= $betList[14005]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14005]->max) ? $betList[14005]->max : $game_config->max ?> ) "><span class="mini_power_btn3"><span class="mini_power_font markets_id_14005"><?= $betList[14005]->markets_name ?></span><br><?= $betList[14005]->bet_price ?></span></a></td>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14006, <?= $betList[14006]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14006]->max) ? $betList[14006]->max : $game_config->max ?> ) "><span class="mini_power_btn3"><span class="mini_check"></span><span class="mini_power_font markets_id_14006"><?= $betList[14006]->markets_name ?></span><br><?= $betList[14006]->bet_price ?></span></a></td>
                    <td width="10%"><a href="javascript:void(0);" onclick="addMiniGameBet(14007, <?= $betList[14007]->bet_price ?>, <?= session()->get('money') ?>, <?= isset($betList[14007]->max) ? $betList[14007]->max : $game_config->max ?> ) "><span class="mini_power_btn3"><span class="mini_power_font markets_id_14007"><?= $betList[14007]->markets_name ?></span><br><?= $betList[14007]->bet_price ?></span></a></td>                        
				</tr>
            </table>
        </div>
        <div class="mini_cart_bet" style="display: none">
            <table width="100%" cellpadding="0" cellspacing="0" class="cart_bet">
                <tr>
                    <td colspan="2"><span class="mini_cart_bet_font1">제 <span class="displayRound"></span> 회차</span></td>
                    <td rowspan="3" align="right"><a href="#"><img src="/assets_w/images/cart_close.png" style="padding:0 3px 0 3px;" onclick="notifyCloseBtn()"></a></td>
                </tr>                
                <tr>
                    <td colspan="2"><span class="mini_cart_bet_font2 cart_bet_info"></span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="mini_cart_bet_font3 bet_info"></span><span class="mini_cart_bet_p cart_bet_price"></span></td>
                </tr>
            </table>
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
    </div><!-- mini_wide_right -->

        
    <!-- mini_game_result -->
        <div class="mini_conter_wrap">
            <div class="mini_tab_wide">
                <div class="mini_tab_wide_table">
                    <ul class="mini_tab_wide_tr tabs_multi">
                        <li name="powerballType" id="powerballType1" class="mini_tab_wide_td mini_tab_wide_td_on" onclick="javascript:clickPatternButton(1)">
                            <a href="#power_pattern_1">파워볼 홀짝</a>
                        </li>
                        <li name="powerballType" id="powerballType2" class="mini_tab_wide_td"  onclick="javascript:clickPatternButton(2)">
                            <a href="#power_pattern_2">파워볼 언더오버</a>
                        </li>
                        <li name="powerballType" id="powerballType3" class="mini_tab_wide_td" onclick="javascript:clickPatternButton(3)">
                            <a href="#power_pattern_3">일반볼합 대중소</a>
                        </li>
                    </ul>
                </div>
            </div>        
            <div class="mini_center_result tab_container_multi">

                    <div id="power_pattern_1" class="tab_content_multi"></div>
                    <div id="power_pattern_2" class="tab_content_multi"></div>
                    <div id="power_pattern_3" class="tab_content_multi"></div>

            </div>
        </div>

        <!-- mini_game_betinfo -->
        <div class="mini_conter_wrap">
            <div class="mini_tab_wide">
                <div class="mini_tab_wide_table">
                    <ul class="mini_tab_wide_tr">
                        <li name="showList" id="showList1" class="mini_tab_wide_td mini_tab_wide_td_on" onclick="showBetList('betHisList')">
                            <a href="#tab_bet_list">베팅내역</a>
                        </li>
                        <li name="showList" id="showList2" class="mini_tab_wide_td"  onclick="showBetList('gameResultList')">
                            <a href="#tab_bet_result">경기결과</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="con_box00 mini_history_list" id="tab_bet_list">
                <table width="100%" cellspacing="0" cellpadding="0" class="mini_list_title1_bg" id="betHisTable">
                    <tr class='list_tr2 trfirst'>
                        <td class="list_title1">회차</td>
                        <td class="list_title1">배팅일자</td>
                        <td class="list_title1">나의배팅</td>
                        <td class="list_title1">배팅금액</td>
                        <td class="list_title1">당첨금액</td>                    
                        <td class="list_title1">게임결과</td>
                        <td class="list_title1">결과</td>                                                                   
                    </tr>
                    <tbody id="my_bet_list">
					</tbody>        
                </table>
                <div id="tab_bet_result" class="tab_content">
					<table class="mini_list_title1_bg" width="100%" cellspacing="0" cellpadding="0">
                        <tr class='list_tr2 trfirst'>
                            <td class="list_title1">회차</td>
                            <td class="list_title1">시작시간</td>
                            <td class="list_title1">마감시간</td>
                            <td class="list_title1">게임결과</td>
                        </tr>
                        <tbody id="game_result">
                        </tbody>
                    </table>
                </div>        
            </div>
            <div class="con_box10">
                <div class="page_wrap acc_btn_wrap">
                    <ul  id="paginationArea">
                    </ul>
                </div>
            </div>          
        </div>
    </div><!-- mini_wide_center -->

</div><!-- mini_wide_wrap -->
<?= view('/web/common/footer_wrap') ?>
<!-- </div> -->
<!-- wrap -->

<!-- top버튼 -->
<!-- <a href="#myAnchor" class="go-top">▲</a> -->
<!-- <script type="text/javascript" src="/assets_w/js/game_03_timer.js"></script> -->
<script type="text/javascript" src="/assets_w/js/minigame_common.js?v=<?php echo date("YmdHis"); ?>"></script>
<script type="text/javascript">
    let bet_markets_id = 0;
    let patternType = 1;
    let isAlreadyBetting = false;
    let remain_time = 0;
    let timer = 0;
    let game = 'powerball';
    let close_time = <?=$close_time?>;
    console.log(close_time);
    
    $(document).ready(function() {
        initForm();
        setCurrentRound();
        /* setLnbMiniGameTimer();
        setPladderGameTimer();
        setKladderGameTimer();
        setLnbBetTimer(); */
        getPatternData();

        // 선택한 마켓아이디
        $('.max_bet_money').text(setComma(<?= $game_config->max ?>));
        
        // 게임명
        $('.cart_bet_info').text('엔트리 EOS 파워볼');

        $(document).on('click', '.max_btn', function() {
            if (checkRestTime()) {
                alert('휴장시간입니다.');
                return;
            }
            if ($('.bet_info').text().length <= 0) {
                alert('베팅을 선택해주세요.');
                return;
            }

            let maxBetMoney = $('.max_bet_money').text();

            maxBetMoney = Number(maxBetMoney.replace(/,/gi, "")); //변경작업

            // 보유금액이 없다.
            if (0 >= <?= session()->get('money') ?>) {
                alert('보유머니가 부족합니다.');
                return;
            }



            // 최대금액 이상이면 최대금액으로 없으면, 현재보유금 총액으로 한다.
            if (maxBetMoney > <?= session()->get('money') ?>) {
                maxBetMoney = <?= session()->get('money') ?>;
            }

            
            $('.input_style06').val(setComma(maxBetMoney));
            let maxCalc = calcMaxMoney(<?= $game_config->limit ?>);
            // console.log(maxCalc);
            $('.input_style06').val(setComma(maxCalc));
            changeWillWinMoney();
        });

        $(document).on('click', '.reset_btn', function() {
            if (checkRestTime()) {
                alert('휴장시간입니다.');
                return;
            }

            $('.input_style06').val(0);
            changeWillWinMoney();
        });

        // 배팅
        $(document).on('click', '.cart_btn1', function() {
            let betMoney = $('.input_style06').val();

            if (checkRestTime()) {
                alert('휴장시간입니다.');
                return;
            }

            if ($('.bet_info').text().length <= 0) {
                alert('베팅을 선택해주세요.');
                return;
            }

            betMoney = Number(betMoney.replace(/,/gi, "")); //변경작업

            if (betMoney <= 0) {
                alert('베팅 금액을 설정해주세요.');
                return;
            }

            if (remain_time <= close_time || remain_time >= 287) {
                alert('베팅시간이 아닙니다.');
                return;
            }


            let mes = '선택하신 내용으로 베팅금액 : ' + betMoney + '원\n 베팅진행하시겠습니까?'
            if (confirm(mes) == false) {
                return;
            }

            // 오늘의 회자, () 제거후 순수 회차번호를 구한다.
            let round = $('.round').text();
            // 회차 나누는중
            // round = round.match(/\((.*?)\)/)[1];
            // console.log(str3);
            // let round_count = $('.round_count').text();
            round = round.split("(");

            let round_count = round[0]; // 회차
            round = round[1].split(")");
            round = round[0];
            
            let memberBetList = [];
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
                if(isAlreadyBetting)
                    return false;
                isAlreadyBetting = true;
                $('#loadingCircle').show();
                $.ajax({
                    url: '/api/bet/addMiniBet',
                    type: 'post',
                    data: {
                        'betList': memberBetList,
                        'betType': 15,
                        'totalOdds': Number($('.bet_price').text()), // 전체 베팅배율
                        'totalMoney': betMoney,
                        'betGroup': 'mini',
                        'folderType': 'S',
                        'keep_login_access_token': localStorage.getItem("keep_login_access_token")
                    },
                }).done(function(response) {
                    initForm();
                    // key reflush
                    const keep_login_access_token = response['data']['keep_login_access_token'];
                    localStorage.setItem('keep_login_access_token', keep_login_access_token);
                    
                    $('.markets_id_' + bet_markets_id).removeClass('sports_select');
                    /*if(confirm('베팅에 성공하였습니다. 베팅내역을 확인하시겠습니까?') == true){
                        location.href = "/web/betting_history?menu=b";
                    }*/

                    $('.util_money').text(setComma(response['data']['total_money']));
                    alert('베팅에 성공하였습니다.');
                    //$('.util_point').text(setComma(response['data']['total_point']));
                    location.reload();
                    //return;
                    //getBetList();

                }).fail(function(error) {
                    alert(error.responseJSON['messages']['messages']);
                    $('#loadingCircle').hide();
                }).always(function(response) {
                    isAlreadyBetting = false;
                    
                    let path = '/api/bet/addMiniBet?betType=15';
                    let current_page = 'addMiniBet';
                    write_access_log(path, current_page);
                });

        });
    });

    // 수기입력
    $(document).on('change', '.input_style06', function() {
        changeWillWinMoney();
    });

    $(document).on('focus', '.input_style06', function() {
        $('#loadingCircle').hide();
        let userInputBetBefore = $('.input_style06').val();
        sessionStorage.setItem('userInputBet', userInputBetBefore);
    });

    $(document).on('blur', '.input_style06', function() {
        $('#loadingCircle').hide();
        let userInputBetBefore = sessionStorage.getItem('userInputBet');
        let userInputBetAfter = $('.input_style06').val();
        userInputBetAfter = Number(userInputBetAfter.replace(/,/gi, ""));
        let maxReturn = <?= $game_config->limit ?>;

        if ($('.bet_info').text().length <= 0) {
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
        maxBetMoney = Number(maxBetMoney.replace(/,/gi, "")); //변경작업

        let nowMoney = <?= !empty(session()->get('money')) ? session()->get('money') : 0 ?>;
        nowMoney = +nowMoney;

        if (userInputBetAfter > maxBetMoney) {
            $('.input_style06').val(userInputBetBefore);
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            alert('최대배팅금 이상 배팅할 수 없습니다');
            return;
        }
        if (!returnMaxCheck(userInputBetAfter)) {
            $('.input_style06').val(userInputBetBefore);
            changeWillWinMoney();
            sessionStorage.removeItem('userInputBet');
            alert('최대당첨금액 제한 : 배팅할 수 없습니다');
            return;
        }

        if (nowMoney === 0) {
            alert('보유머니가 부족합니다');
            return;
        } else if (maxBetMoney > nowMoney) {
            if (nowMoney < +userInputBetAfter) {
                //$('.input_style06').val(setComma(nowMoney));
                $('.input_style06').val(setComma(nowMoney));
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                return;
            } else {
                //$('.input_style06').val(setComma(userInputBetAfter));
                $('.input_style06').val(setComma(userInputBetAfter));
                changeWillWinMoney();
                sessionStorage.removeItem('userInputBet');
                return;
            }
        }

        //$('.input_style06').val(setComma(userInputBetAfter));
        $('.input_style06').val(setComma(userInputBetAfter));
        changeWillWinMoney();
        sessionStorage.removeItem('userInputBet');
        return;
    });

    // max버튼 자동계산
    function calcMaxMoney(max_bet_money) {
        //let inputBetMoney = $('.input_style06').val();
        let inputBetMoney = $('.input_style06').val();
        let data = inputBetMoney.replace(/,/gi, ""); //변경작업
        let will_win_money = $('.will_win_money').text();
        will_win_money = +will_win_money.replace(/,/gi, "");
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

    // 배팅선택
    const addMiniGameBet = function(markets_id, bet_price, money, max) {
        initForm();
        if (checkRestTime()) {
            alert('휴장시간입니다.');
            return;
        }

        $('.max_bet_money').text(setComma(max));


        let markets_name = $('.markets_id_' + markets_id).text();

        $('.select_game').removeClass('mini_power_c_btn1');
        $('.select_game').removeClass('mini_power_c_btn2');
        $('.select_game').removeClass('mini_power_c_btn3');
        if(markets_id == 10001 ||markets_id == 10004) {
        	$('.select_game').addClass('mini_power_c_btn1');
        }
        if(markets_id == 10003 ||markets_id == 10002) {
        	$('.select_game').addClass('mini_power_c_btn2');
        }
        if(markets_id == 10005 ||markets_id == 10006||markets_id == 10007) {
        	$('.select_game').addClass('mini_power_c_btn3');
        }
        
        $('.bet_info').text(markets_name);
        $('.bet_info').show();
        $('.bet_price').text(bet_price);
        
        $('.mini_cart_bet').show();
        $('.cart_bet_price').text(bet_price);

        // 이전에 선택한 배팅이 있다.(같은거 선택시 아래서 제거)
        if (bet_markets_id > 0 && bet_markets_id != markets_id) {
            if ($('.markets_id_' + bet_markets_id).removeClass('sports_select'));
        }

        bet_markets_id = markets_id;
        //$('.input_style06').text(bet_price);
        changeWillWinMoney();

        /* if ($('.markets_id_' + markets_id).hasClass('sports_select')) {
            $('.markets_id_' + markets_id).removeClass('sports_select');
            initForm();
        } else {
            $('.markets_id_' + markets_id).addClass('sports_select');
        } */
    }

    /*const changeWillWinMoney = function() {
        //let inputBetMoney = $('.input_style06').val();
        let inputBetMoney = $('.input_style06').val();
        let data = inputBetMoney.replace(/,/gi, ""); //변경작업

        let totalOdds = Number($('.bet_price').text());
        let willMoney = (totalOdds == 0 ? 1 : totalOdds) * Number(data);

        //willMoney = Math.ceil(willMoney);
        willMoney = Math.floor(willMoney);
        $('.will_win_money').html(setComma(willMoney));
    }*/

    const returnMaxCheck = function(inputBetMoney = 0) {
        let totalOdds = Number($('.bet_price').text());
        let willMoney = (totalOdds == 0 ? 1 : totalOdds) * Number(inputBetMoney);

        //willMoney = Math.ceil(willMoney);
        willMoney = Math.floor(willMoney);
        if (willMoney > <?= $game_config->limit ?>) {
            return false;
        };
        return true;
    }

    const initForm = function() {
        totalOdds = 0;
        $('.total_odds').html(totalOdds);
        $('.input_style06').val(0);
        $('.will_win_money').html(0);
        $('.bet_info').text('');
        $('.bet_info').hide();
        $('.bet_price').text(0);
        
        $('.mini_cart_bet').hide();
        $('.cart_bet_price').text(0);
        changeWillWinMoney();
    }

    // 금액 버튼
    /*const setBettingMoney = function(money, userMoney) {
        if ($('.bet_info').text().length <= 0) {
            alert('베팅을 선택해주세요.');
            return;
        }

        if (checkRestTime()) {
            alert('휴장시간입니다.');
            return;
        }

        if (money > userMoney) {
            alert('보유머니가 부족합니다.');
            return;
        }

        let inputBetMoney = $('.input_style06').val();
        let data = inputBetMoney.replace(/,/gi, ""); //변경작업
        let maxBetMoney = $('.max_bet_money').text();
        maxBetMoney = maxBetMoney.replace(/,/gi, ""); //변경작업
        let current_bet_money = Number(data);
        let bet_money = current_bet_money + Number(money);

        if (bet_money > userMoney) {
            alert('보유머니가 부족합니다.');
            return;
        }

        if (bet_money > maxBetMoney) {

        	alert('최대배팅금 이상 배팅할 수 없습니다');
            return;
        }

        if (!returnMaxCheck(bet_money)) {
            alert('최대당첨금액 제한 : 더이상 배팅할 수 없습니다');
            return;
        }

        $('.input_style06').val(setComma(bet_money));

        changeWillWinMoney();

    }

    const setCurrentRound = function() {
        $.ajax({
            url: '/minigame/getCurrentRound',
            type: 'post',
            data: {
                'game': 'powerball'
            },
        }).done(function(response) {

            remain_time = response['data']['remain_time'] + 4;
            $('.round').text(response['data']['current_round']+ '(' + response['data']['id'] + ')');
            $('.displayRound').text(response['data']['current_round']);
            timer = setInterval(checkRemainTime, 1000);
   
            getBetList();
            $('#tab_bet_result').hide();
        }).fail(function(error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function(response) {});
    }*/

    // 남은시간 체크
    /*const checkRemainTime = function() {
        // 시간차감
        let displayRemainTime = '';
        remain_time = remain_time - 1;

        if (remain_time <= 60 || remain_time >= 287) {
            if (remain_time <= 60) {
                displayRemainTime = '베팅마감';
            } else {
                displayRemainTime = '베팅준비중';
            }
        } else {
            let minite = Math.floor(remain_time / 60);
            if (minite < 10) {
                minite = '0' + (minite - 1);
            }

            let second = remain_time % 60;
            if (second < 10) {
                second = '0' + second;
            }

            displayRemainTime = minite + ':' + second;
        }

        // 시간출력
        if (remain_time > 0) {
            $('.remain_time').text(displayRemainTime);

            if(displayRemainTime == "베팅마감") {
				$('.bettingTimerText').text("");
            }
            if(displayRemainTime == "베팅준비중") {
            	$('.bettingTimerText').text("베팅마감");
            }
        }

        // 시간끝났을시 정보호출
        if (remain_time <= 0) {
            setCurrentRound();
            clearInterval(timer);
        }

        if (remain_time === 0) {
            location.reload();
            return false;
        }

    }*/

    /*const showBetList = function(type) {

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
    	
    }*/
    
    // 배팅내역, 경기결과
    const getBetList = function(currentPage) {
        if (!currentPage) {
                param = {
                    'curPageNo': 1,
                    'game': game,
                    'displayCnt': 10
                };
        } else {
                $('#my_bet_list').empty();
                param = {
                    'curPageNo': currentPage,
                    'game': game,
                    'displayCnt': 10
                };
        };

        $.ajax({
            url: '/minigame/selectMemberMiniGameBet?game=powerball&betType=15',
            type: 'post',
            data: param,
        }).done(function(response) {
            //console.log('remain_time : ' + response['data']['remain_time']);
            
            let list = response['data']['bet_list'];
            let game_result = response['data']['game_result'];
            // console.log(response);
            let totalCnt = Number(response['data']['bet_count']);
            let curPageNo = Number(response['data']['curPageNo']);
            let html = '';
            $('#my_bet_list').empty();
            for (const [key, betInfo] of Object.entries(list)) {
                let arrCreateDt = betInfo['create_dt'].split(" ");
                let result = JSON.parse(betInfo['result']);
                if(betInfo['result_score']){
                    result = JSON.parse(betInfo['result_score']);
                }
                // console.log(betInfo);
                let start_dt = result['sdate'].split(" ");
                let cnt_date = start_dt[1];
                let cnt_date_arr = cnt_date.split(':');
                let cnt = Math.round(((+cnt_date_arr[0] * 60) + +cnt_date_arr[1]) / 5) + 1; // 시작시간 기준이라서
                let dayOfWeek = getDayOfWeek(betInfo['create_dt'].replace(/-/gi,"/"));

                result = getPowerBallResult(Number(result['pb']), Number(result['num1']), Number(result['num2']), Number(result['num3']), Number(result['num4']), Number(result['num5']));
                let calc_result = calcResult(betInfo['ls_markets_id'], result, betInfo['total_bet_money'], betInfo['bet_price']); 
                
                let $status = '기타';
                let $statusColor = 'sports_division1';
                let temp_money = 0;
                // console.log(betInfo['take_money'] !== undefined ? setComma(betInfo['take_money']) : setComma(temp_money));
                
                if (+betInfo['bet_status'] == 1) {
                    $status = '대기';
                    $statusColor = 'sports_division1';
                }
                if (result !== '-') {
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
                html += "<td class='list1'><span class='font01'>" + `${cnt} 회` +"</span></td>";/*  `(${betInfo['ls_fixture_id']})` +  */
                html += "<td class='list1'><span class='font01'>" + getFormatDateMonth(arrCreateDt[0]) + " (" + dayOfWeek + ") " + arrCreateDt[1] + "</span></td>";
                html += "<td class='list1'><span class='font01'>" + betInfo['ls_markets_name'] + " (" + betInfo['bet_price'] + ")</span></td>";
                html += "<td class='list1'><span class='font01'>" + setComma(betInfo['total_bet_money']) + "</span></td>";
                html += "<td class='list1'><span class='font05'>" + $takemoney + "</span></td>";
                html += "<td class='list1'><span class='font01'>" + result + "</span></td>";
                html += "<td class='list1'><span class='"+$statusColor+"'>" + $status + "</span></td>";
                html += "</tr>";
            }
            
            $('#my_bet_list').append(html);

            

            // 게임결과
            $('#game_result').empty();
            let html2 = '';
            for (const [key, gameResult] of Object.entries(game_result)) {
                // console.log(gameResult);
                let start_dt = gameResult['start_dt'].split(" ");
                let cnt_date = start_dt[1];
                let cnt_date_arr = cnt_date.split(':');
                let cnt = Math.round(((+cnt_date_arr[0] * 60) + +cnt_date_arr[1]) / 5) + 1; // 시작시간 기준이라서
                let end_dt = gameResult['end_dt'].split(" ");
                let result = JSON.parse(gameResult['result']);
                if(gameResult['result_score']){
                    result = JSON.parse(gameResult['result_score']);
                }
                
                let start_dayOfWeek = getDayOfWeek(gameResult['start_dt'].replace(/-/gi,"/"));
                let end_dayOfWeek = getDayOfWeek(gameResult['end_dt'].replace(/-/gi,"/"));
                result = getPowerBallResult(Number(result['pb']), Number(result['num1']), Number(result['num2']), Number(result['num3']), Number(result['num4']), Number(result['num5']));
                html2 += "<tr class='list_tr2 trfirst'>";
                html2 += "<td class='list1'><span class='font01'>" + `${cnt} 회` + "</td>";//+ "</span> " + `(${gameResult['id']})` + "</span>
                html2 += "<td class='list1'><span class='font01'>" + getFormatDateMonth(start_dt[0]) + " (" + start_dayOfWeek + ") " + start_dt[1] + "</span></td>";
                html2 += "<td class='list1'><span class='font01'>" + getFormatDateMonth(end_dt[0]) + " (" + end_dayOfWeek + ") " + end_dt[1] + "</span></td>";
                html2 += "<td class='list1'><span class='font01'>" + result + "</span></td>";
                html2 += "</tr>";
            }
            $('#game_result').append(html2);
            
            fnSetPagination(totalCnt, curPageNo, 'getBetList', 'paginationArea');
        }).fail(function(error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function(response) {});
    }

    // 파워볼 게임 결과
    const getPowerBallResult = function(pb, num1, num2, num3, num4, num5) {
        if (pb === 0 && num1 === 0 && num2 === 0 && num3 === 0 && num4 === 0 && num5 === 0) {
            return '-';
        }
        let result = '';
        if (0 !== pb % 2) {
            result = '[P 홀],';
        } else if (0 === pb % 2) {
            result = '[P 짝],';
        }

        if (5 <= pb && pb <= 9) {
            result += '[P 오버],';
        } else if (0 <= pb && pb <= 4) {
            result += '[P 언더],';
        }

        let sum_num = num1 + num2 + num3 + num4 + num5;
        if (81 <= sum_num && sum_num <= 130) {
            result += '[대]';
        } else if (65 <= sum_num && sum_num <= 80) {
            result += '[중]';
        } else {
            result += '[소]';
        }
        return result;
    }

    const calcResult = function(mybet, result, bet_money, bet_price) {
        //console.log(mybet, result, bet_money, bet_price, bet_money * bet_price);
        if (mybet === '10001' || mybet === '14001') {
            if (result.indexOf('홀') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        } else if (mybet === '10002' || mybet === '14002') {
            if (result.indexOf('짝') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        } else if (mybet === '10003' || mybet === '14003') {
            if (result.indexOf('오버') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        } else if (mybet === '10004' || mybet === '14004') {
            if (result.indexOf('언더') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        }  else if (mybet === '10005' || mybet === '14005') {
            if (result.indexOf('대') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        }  else if (mybet === '10006' || mybet === '14006') {
            if (result.indexOf('중') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }
        }  else if (mybet === '10007' || mybet === '14007') {
            if (result.indexOf('소') !== -1) {
                return {status: 1, rtn: bet_price * bet_money};
            } else {
                return {status: 2, rtn: 0};
            }

        }  else {
                return {status: 3, rtn: 0};
        }

    }
    
    // 패턴버튼 클릭
    const clickPatternButton = function(type){
        patternType = type;
        $('li[name="powerballType"]').removeClass('mini_tab_wide_td_on');
        $('#powerballType'+type).addClass('mini_tab_wide_td_on');
        getPatternData();
    }
    
    // 출목표
    const getPatternData = function(){
        param = {
                    'betType': 15
                };
                
        $.ajax({
            url: '/minigame/selectMiniGamePattenData',
            type: 'post',
            data: param,
        }).done(function (response) {
            //console.log(response);
            let game_result = response['data']['game_result'];
            let html = "<ul class='mini_pattern_list'>";
            let bfData = '';
            let rowCount = 0;   // 아래로 몇줄인지 체크하는 용도
            //let colCount = 0;   // 옆으로 몇줄인지 체크하는 용도
            
            for (const[key, betInfo] of Object.entries(game_result)) {
                let result = JSON.parse(betInfo['result']);
                if(betInfo['result_score']){
                    result = JSON.parse(betInfo['result_score']);
                }
                
                // 결과가 안나왔다.
                if(Number(result['num1']) === 0) continue;
                
                //let checkData = '';
                let checkData = Number(result['pb']);
                let className = 'mini_pattern_blue';
                let name = '홀';
                if(patternType == 1){
                    checkData = checkData % 2;
                    if(checkData == 0){
                        className = 'mini_pattern_red';
                        name = '짝';
                    }
                }else if(patternType == 2){
                    if(checkData >= 5){
                        className = 'mini_pattern_red';
                        name = checkData = '오버';
                    }else{
                        className = 'mini_pattern_blue';
                        name = checkData = '언더';
                    }
                }else{
                    let sum_num = Number(result['num1']) + Number(result['num2']) + Number(result['num3']) + Number(result['num4']) + Number(result['num5']);
                    if(15 <= sum_num && 64 >= sum_num){
                        className = 'mini_pattern_green';
                        name = checkData = '소';
                    }else if(65 <= sum_num && 80 >= sum_num){
                        className = 'mini_pattern_blue';
                        name = checkData = '중';
                    }else if(81 <= sum_num && 130 >= sum_num){
                        className = 'mini_pattern_red';
                        name = checkData = '대';
                    }
                }
                
                // 첫루프가 아니고, 결과가 변경이 되었다.
                if(bfData !== '' && bfData != checkData){
                    // 빈칸을 채워준다.
                    if(rowCount < 12){
                        for(i=rowCount; i<12; ++i){
                            html += "<dd></dd>";
                        }
                    }
                    rowCount = 0;
                    
                    html += "</dl>";
                    html += "</li>";
                    
                    html += "<li class='"+className+"'>";
                    html += "<dl>";
                    html += "<dt>"+name+"</dt>";
                }
                
                // 첫루프시
                if(bfData === ''){
                    html += "<li class='"+className+"'>";
                    html += "<dl>";
                    html += "<dt>"+name+"</dt>";
                }
                bfData = checkData;
                rowCount += 1;
                
                html += "<dd><span>"+betInfo['cnt']+"</span></dd>";
            }
            
            // 빈칸을 채워준다.
            if(rowCount < 12){
                for(i=rowCount; i<12; ++i){
                    html += "<dd></dd>";
                }
            }
            rowCount = 0;
                    
            html += "</dl>";
            html += "</li>";
            html += "</ul>";
            //console.log(html);
            $('#power_pattern_'+patternType).empty();

            //$('div[name=power_patterns]').empty();
	        $('#power_pattern_'+patternType).append(html);
	        $('#power_pattern_'+patternType).show();

            
            // 삭제할 li 갯수
            let delCount = $('#power_pattern_'+patternType).find('li').length - 20;
            $('#power_pattern_'+patternType + ' li').each(function (index, item){
                if(index < delCount){
                    item.remove();
                }
            });
        }).fail(function (error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function (response) {
        });
    }
</script>
</body>
</html>