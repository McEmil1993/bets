<?php 
    // var_dump($locationGameList);
    $imageBasePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath;
    foreach ($sports as $key => $value) {
        $realTimeTotal[$value['id']] = $value['count'];
    }
?>


<?= view('/web/common/header') ?>

<script src="/assets_w/js/realtime_common_w.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets_w/js/realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets_w/js/sports_realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script>

<script src="/assets_w/js/tendina.min.js"></script>


<?= view('/web/common/header_wrap') ?>

<div class="title_wrap"><div class="title">LIVE 스포츠</div></div>

<div id="sports_wide_wrap" class="realtime_wrap">
    
    <?= view('web/realtime_left')?>

    <div class="sports_wide_center">

        <div class="sports_list_title">
            <div class="sports_list_title2">
                <select class="sports_input1" onchange="sports_select(this.value)">
                    <option value="0">전체종목</option>
                    <?php foreach ($sports as $key => $sport) { ?>
                    <option value="<?=$sport['id']?>" <?=($sport['id']==($sportsId ?? 0))?'selected':''?> >
                        <?=$sport['name']?>
                    </option>
                    <?php } ?>
                </select>
                
                <select class="sports_input2" onchange="league_select(this.value)">
                    <option value="0">전체리그</option>
                    <?php foreach ($leagues as $key => $lo){ ?>
                    <option value="<?= $lo['id'] ?>" <?=($lo['id']==($leagueId ?? 0))?'selected':''?>><?= $lo['display_name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- 2022 03 25 daniel -->
            <!-- <div class="sports_list_title3">
            	<ul>
                    <li><a href="javascript:sports_select(0)"><span id="sports_img_0" class=""><img src="/assets_w/images/icon01.png" width="20">&nbsp; 전체</span></a></li>
                    <li><a href="javascript:sports_select(6046)"><span id="sports_img_6046" class=""><img src="/assets_w/images/icon02.png" width="20">&nbsp; 축구</span></a></li>
                    <li><a href="javascript:sports_select(48242)"><span id="sports_img_48242" class=""><img src="/assets_w/images/icon03.png" width="20">&nbsp; 농구</span></a></li>                    
                    <li><a href="javascript:sports_select(154914)"><span id="sports_img_154914" class=""><img src="/assets_w/images/icon04.png" width="20">&nbsp; 야구</span></a></li>
                    <li><a href="javascript:sports_select(154830)"><span id="sports_img_154830" class=""><img src="/assets_w/images/icon05.png" width="20">&nbsp; 배구</span></a></li>
                    <li><a href="javascript:sports_select(35232)"><span id="sports_img_35232" class=""><img src="/assets_w/images/icon06.png" width="20">&nbsp; 아이스하키</span></a></li>
                   <li><a href="javascript:sports_select(687890)"><span id="sports_img_687890" class=""><img src="/assets_w/images/icon07.png" width="20">&nbsp; 이스포츠</span></a></li>
                </ul>
            </div> -->
            
            <div class="sports_list_title3">
            	<ul>
                    
                    <?php foreach ($sports as $key => $sport) { ?>
                    <li>
                        <a href="javascript:sports_select(<?=$sport['id'].''?>)">
                            <span id="sports_img_<?=$sport['id'].''?>">
                                <?php
                                $image_path = "";
                                if($sport['image_path'] != ""){
                                    $image_path = $imageBasePath.'/sports/'.$sport['image_path'];
                                }else{
                                    $image_path = $imageBasePath.'/sports/'.'icon_game'.$sport['id'].'.png';
                                }
                                ?>

                                <img src="<?=$image_path?>" width="24">
                                &nbsp;
                                <?= $sport['display_name']?>
                            </span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="sports_list_title4"></div>
        </div>

        <!--<div class="sports_list_title">
            <div class="sports_list_title4">
                <img src="/assets_w/images/main_live.png"> 잠시 후 00:00 
                <span class="font06">레알마드리드 
                    <img src="/assets_w/images/vs.png" width="30"> FC바르셀로나
                </span>
            </div>
        </div>-->
        <div class="sports_s_left_sports_s_right_wrap">
            <div class="sports_s_left">
                <ul class="dropdown2 live_game_display">
                </ul> 
            </div><!-- sports_s_left -->   

            <!-- <div class="sports_s_right">   -->
                <!-- 배당판 -->
                <!-- <ul class="dropdown3">
                    
                </ul>    -->
            <!-- </div> -->
            <!-- sports_s_right -->
        </div><!-- .sports_s_left_sports_s_right_wrap -->
    </div><!-- sports_wide_center -->

    


    <div class="sports_wide_right">
        <div class="cart_wrap">

            <div class="btn__cart_close">
                <a href="#"><img src="/assets_w/images/m_close.png" width="50"></a>
            </div>


            <div class="sports_cart_title">
                BETTING SLIP
                <span class="sports_cart_title_right">
                    <span class="sports_cart_title2">배당변경 자동적용 &nbsp;&nbsp;</span>
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
                        <tbody><tr>
                            <td class="sports_cart_style1">보유머니<span class="sports_cart_style3 myHoldingMoney"><?= number_format(session()->get('money')) ?></span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">최대베팅금 <span class="sports_cart_style3 max_bet_money"><?= number_format($maxBetMoney) ?></span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">최대적중금 <span class="sports_cart_style3"><?= number_format($limitBetMoney) ?></span></td>
                        </tr>
                    </tbody></table>
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
                <!-- 배팅카트 표시 -->
            </ul>

            <div class="sports_cart_wrap">
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                        <!--<tr>
                            <td class="sports_cart_style1">보유머니<span class="sports_cart_style2">0</span></td>
                        </tr>-->
                        <!-- <tr>
                            <td class="sports_cart_style1">최대베팅금<span class="sports_cart_style2 max_bet_money"><?//= number_format($maxBetMoney) ?></span></td>
                        </tr> -->
                        <tr>
                            <td class="sports_cart_style1">배당률(보너스) <span class="sports_cart_style3 bonus_total_odds">0</span> <span class="sports_cart_style2 total_odds">0</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">베팅금액 <span class="sports_cart_style2"><input class="input3" id="betting_slip_money" style="text-align:right; width:150px;" value="0"></span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">예상적중금 <span class="sports_cart_style2 will_win_money">0</span></td>
                        </tr>
                        <!--<tr>
                            <td class="sports_cart_style1">베팅금액 <span class="sports_cart_style2"><input class="input3" style="text-align:right; width:150px;"></span></td>
                        </tr>-->
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

</div><!-- sports_wide_wrap -->
<?= view('/web/common/footer_wrap') ?>




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
    let isMobile = false;
    let betType = 'S'; // 1: 스포츠, 2: 실시간
    let folderType = 'S'; // 'S': 싱글, 'D': 다폴더
    let service_bonus_folder = '<?= $arr_bonus['service_bonus_folder']; ?>';
    let odds_3_folder_bonus = <?= $arr_bonus['odds_3_folder_bonus']; ?>;
    let odds_4_folder_bonus = <?= $arr_bonus['odds_4_folder_bonus']; ?>;
    let odds_5_folder_bonus = <?= $arr_bonus['odds_5_folder_bonus']; ?>;
    let odds_6_folder_bonus = <?= $arr_bonus['odds_6_folder_bonus']; ?>;
    let odds_7_folder_bonus = <?= $arr_bonus['odds_7_folder_bonus']; ?>;
    let limit_folder_bonus = <?= $arr_bonus['limit_folder_bonus']; ?>;
    let isAlreadyBetting = false;
    let betList = [];
    let is_betting_slip = '<?=$is_betting_slip?>';
    let maxBetMoney = <?= $maxBetMoney ?>;
    let limitBetMoney = <?= $limitBetMoney ?>;
    let betDelayTime = new Array();
    betDelayTime['6046'] = <?= $betDelayTime['6046'] ?>;
    betDelayTime['35232'] = <?= $betDelayTime['35232'] ?>;
    betDelayTime['48242'] = <?= $betDelayTime['48242'] ?>;
    betDelayTime['687890'] = <?= $betDelayTime['687890'] ?>;
    betDelayTime['154830'] = <?= $betDelayTime['154830'] ?>;
    betDelayTime['154914'] = <?= $betDelayTime['154914'] ?>;
    
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
    let isClassic = 'OFF'; // 1-클래식
    let prevBetMoney = 0;
    const serverName = '<?= $serverName ?? '' ?>';

    function getRealTimeGameLiveScoreList(sportsId, locationId, leagueId, league_name) {

        // if(sportsId == 0 && locationId == 0){
        //     console.log("0 클릭");
        //     selectFixtureId = 0;
        // }
        
        //console.log('getRealTimeGameLiveScoreList !! click',sportsId, locationId, leagueId, league_name );

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
        if (leagueId > 0) {
            dataForm['league_id'] = leagueId;
        }
        if (league_name > 0) {
            dataForm['league_name'] = league_name;
        }

        //console.log('dataForm', dataForm);
        let totalGameCnt = 0;   // Live Sports 총 갯수 초기화
        
        $.ajax({
            url: '/api/real_time/getRealTimeGameLiveScoreList',
            type: 'post',
            data: dataForm,
            // beforeSend : function(){
            //     $(document).find("#loadingCircle").show();
            // },
            // complete : function(){
            //     $(document).find("#loadingCircle").hide();
            // }
        }).done(function (response) {
            console.log('response', response);
            $('.live_game_display *').remove();
            //console.log('response', response);

            //let fristBetList = [52,202,203,204,205,206,226,63,464, 349]; // 첫화면에 출력할 마켓타입들
            //let displayOrderMarkets = ['메인','승무패/승패','핸디캡','오버언더','기타'];
            let betting_html = '';
            live_data = response['data']['live_list'];
            
            const activeBetId = [];
            const activeBetIndex = [];
            $('.slip_bet_ing').each(function(){
                activeBetId.push($(this).data('bet-id'));
                activeBetIndex.push($(this).data('index'));
            })

            const betAmount = $('#betting_slip_money').val();
            
            // 종목 세팅
            sports.forEach(function (sports_id) {

                let list = response['data']['live_list'][sports_id];

                if(!list) { return true; }
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

                    let mainKey = Object.keys(fixture_list[firstGameIdx])[0];
                    let mainGame = fixture_list[firstGameIdx][mainKey][0];

                    let classNum = "0";

                    if (sports_id == 6046) {
                        classNum = "1";
                    } else if (sports_id == 154914) {
                        classNum = "2";
                    } else if (sports_id == 48242) {
                        classNum = "3";
                    } else if (sports_id == 154830) {
                        classNum = "4";
                    } else if (sports_id == 35232) {
                        classNum = "6";
                    } else if (sports_id == 687890) {
                        classNum = "7";
                    } else {
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
                    let fixture_displayClass = '';
                    //if($('#display_fixture_'+selectFixtureId).hasClass('fixture_open') == true){
                    if(selectFixtureId == fixtureKey){
                        if(selectFixtureDisplay == 1){
                            fixture_display = 'display:block';
                            fixture_displayClass = 'active';
                            //console.log('fixture_open : '+selectFixtureId);
                        }
                    }
                        // console.log('selectFixtureId', selectFixtureId , 'fixtureKey', fixtureKey);
                    
                    // 첫경기는 오픈된 상태로 준비
                    if(0 == selectFixtureId){
                        fixture_display = 'display:block';
                        fixture_displayClass = 'active';
                        selectFixtureId = fixtureKey;
                        selectFixtureDisplay = 1;
                    }
                    
                    // 스코어 rmq 도착전 null일때
                    let live_results_p1 = mainGame['live_results_p1'];
                    let live_results_p2 = mainGame['live_results_p2'];
                    if(null === live_results_p1 || null === live_results_p2){
                        live_results_p1 = live_results_p2 = 0;
                    }

                    let $fixture_start_date = mainGame['fixture_start_date'];
                    let $date = $fixture_start_date.split(" ")[0].split("-");
                    $date = `${$date[1]}-${$date[2]}`;
                    let $time = $fixture_start_date.split(" ")[1].split(":");
                    $time = `${$time[0]}:${$time[1]}`;

                    let $bet_data_Over = mainGame.bet_data.Over || mainGame.bet_data["0"];      // win
                    let $bet_data_Under = mainGame.bet_data.Under || mainGame.bet_data["1"];    // lose
                    let $bet_data_Draw = mainGame.bet_data["2"] || mainGame.bet_data["X"];      // draw
                    
                    // 경기목록
                    //console.log('mainGame', mainGame);
                    let mainGame_live_results_p1 = mainGame.live_results_p1;
                    let mainGame_live_results_p2 = mainGame.live_results_p2;
                    if(mainGame.live_results_p1 == '' || mainGame.live_results_p1 == null){
                        mainGame_live_results_p1 = 0;
                        mainGame_live_results_p2 = 0;
                    }
                    // title
                    html = `
                        <li id='live_game_display_${mainGame['fixture_sport_id']}'
                            class='live_game_display_wrap ${fixture_displayClass} live_game_display_${mainGame['fixture_sport_id']} live_game_location_${mainGame['fixture_location_id']}'
                            data-fixtureKey="${fixtureKey}"
                        >


                            <div class="live_game_display_ttl" onClick='onDisplayFixture(${fixtureKey})'>                        
                                <div class="sport_live_type">
                                    <img src='${sportsImagePath}' width='24'>
                                </div>
                                <div class="sport_live_team1">
                                    <font class="live_team1">${mainGame['fixture_participants_1_name']}</font>
                                    <span class="item_score">${mainGame_live_results_p1}</span>
                                </div>
                                <div class="sport_live_tie">
                                    <span class="item_hyphen">VS</span>
                                </div>
                                <div class="sport_live_team2">
                                    <span class="item_score">${mainGame_live_results_p2}</span>
                                    <font class="live_team2">${mainGame['fixture_participants_2_name']}</font>
                                </div>
                                <div class="sport_live_league">
                                    <span>${mainGame['fixture_league_name']}</span>
                                </div>
                                <div class="sport_live_time">
                                    <span class="item_state">${mainGame.live_current_period_display}</span>
                                    <font>${$time}</font>
                                </div>
                                <div class="sport_live_more">
                                    <span class="item_btn">+ ${fixture_list['game_count']}</span>
                                </div>
                                <div class="live_game_display_right">
                                

                    `;


                    // title (승무패)
                    // if(  mainGame.bet_data.length <= 2 ){
                    //     html += `
                                        
                    //                         <span class="item_win">승</span>
                    //                         <span class="item_win"></span>
                    //                         <span class="item_win">패</span>
                    //                         <span class="item_btn"></span>
                                        
                    //     `;
                    // } else {
                    //     html += `
                                        
                    //                         <span class="item_win">승</span>
                    //                         <span class="item_win">무</span>
                    //                         <span class="item_win">패</span>
                    //                         <span class="item_btn"></span>
                                        
                    //     `;

                    // }


                    // score data null check
                    
                    
                    html += `
                                    </div>
                                </div><!-- .live_game_display_ttl -->

                                        <div class="live_game_display_cont" onClick='onDisplayFixture(${fixtureKey})'>
                                            <div class="live_game_display_left">
                                                        <span class="item_date">
                                                                    <font>${$date}</font>
                                                                    
                                                        </span>
                                                        <span class="item_team">
                                                                    
                                                                    
                                                        </span>
                                            </div>
                                            <div class="live_game_display_right">
                                                        <span class="item_score">${mainGame_live_results_p1}</span>
                                                        <span class="item_hyphen">-</span>
                                                        <span class="item_score">${mainGame_live_results_p2}</span>
                                                        <span class="item_state">${mainGame.live_current_period_display}</span>
                    `;


                    // 승
                    if($bet_data_Over.bet_status == "2"){   // lock
                        html += `
                                            <span class="item_win"><img src="/images/icon_lock.png" alt="lock" width="13"></span>
                        `;
                    } else {
                        html += `
                                            
                                                <span class="item_win odds_btn"
                                                    data-bet-status="${$bet_data_Over.bet_status}"
                                                    data-index="${mainGame.fixture_id}_${mainGame.markets_id}_${$bet_data_Over.bet_base_line}_${mainGame.providers_id}"
                                                    data-fixture-id="${mainGame.fixture_id}"
                                                    data-odds-type="win"
                                                    data-bet-id="${$bet_data_Over.bet_id}"
                                                    data-bet-price="${$bet_data_Over.bet_price}"
                                                    data-td-cell="${$bet_data_Over.bet_id}_${mainGame.fixture_start_date}"
                                                    data-markets_name="undefined"
                                                    data-markets_name_origin="${mainGame.markets_name_origin}"
                                                    data-markets_display_name="undefined"
                                                    data-bet-name="${$bet_data_Over.bet_name}"
                                                >
                                                    ${$bet_data_Over.bet_price}
                                                </span>
                        `;
                    }

                    // 무
                    if(  mainGame.bet_data.length <= 2 ){
                        html += `
                                            <span class="item_win">VS</span>
                        `;
                    } else {

                        if($bet_data_Draw.bet_status == "2"){   // lock
                            html += `
                                                <span class="item_win"><img src="/images/icon_lock.png" alt="lock" width="13"></span>
                            `;
                        } else {
                            html += `
                                                    <span class="item_win odds_btn"
                                                        data-bet-status="${$bet_data_Draw.bet_status}"
                                                        data-index="${mainGame.fixture_id}_${mainGame.markets_id}_${$bet_data_Draw.bet_base_line}_${mainGame.providers_id}"
                                                        data-fixture-id="${mainGame.fixture_id}"
                                                        data-odds-type="draw"
                                                        data-bet-id="${$bet_data_Draw.bet_id}"
                                                        data-bet-price="${$bet_data_Draw.bet_price}"
                                                        data-td-cell="${$bet_data_Draw.bet_id}_${mainGame.fixture_start_date}"
                                                        data-markets_name="undefined"
                                                        data-markets_name_origin="${mainGame.markets_name_origin}"
                                                        data-markets_display_name="undefined"
                                                        data-bet-name="${$bet_data_Draw.bet_name}"
                                                    >
                                                        ${$bet_data_Draw.bet_price}
                                                    </span>
                            `;
                        }

                    }

                    // 패
                    if($bet_data_Under.bet_status == "2"){   // lock
                        html += `
                                            <td><span class="item_win"><img src="/images/icon_lock.png" alt="lock" width="13"></span></td>
                        `;
                    } else {
                        html += `
                                                <span class="item_win odds_btn"
                                                    data-bet-status="${$bet_data_Under.bet_status}" 
                                                    data-index="${mainGame.fixture_id}_${mainGame.markets_id}_${$bet_data_Under.bet_base_line}_${mainGame.providers_id}" 
                                                    data-fixture-id="${mainGame.fixture_id}" 
                                                    data-odds-type="lose" 
                                                    data-bet-id="${$bet_data_Under.bet_id}" 
                                                    data-bet-price="${$bet_data_Under.bet_price}" 
                                                    data-td-cell="${$bet_data_Under.bet_id}_${mainGame.fixture_start_date}" 
                                                    data-markets_name="undefined" 
                                                    data-markets_name_origin="${mainGame.markets_name_origin}" 
                                                    data-markets_display_name="undefined" 
                                                    data-bet-name="${$bet_data_Under.bet_name}"
                                                >
                                                    ${$bet_data_Under.bet_price}
                                                </span>
                        
                        `;
                    }


                    html += `
                                        
                                    </div><!-- .live_game_display_right -->
                                </div><!-- .live_game_display_cont -->
                                    <div class='live_box_wrap${classNum} live_box_wrap' style='clear:both; ${fixture_display}' id='display_fixture_${fixtureKey}'>
                                    <div class="table_img_mob">
                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                            <tr>
                                                <td class="mob_display" height='40' style='padding:0 0 0 10px; background: #2b2b2b;'>
                                                    <img src='${leagueImagePath}' style="margin-right:5px;">
                                                    
                                                    ${mainGame['fixture_league_name']}
                                                    
                                                    <span class='font06' style="margin-left:5px;">${leagues_bet_money}</span>
                                                </td>
                                                <td rowspan='3' width="65%" class="scoreboard_wrap">${getScoreBoard(sports_id, fixtureKey)}</td>
                                            </tr>
                                            <tr>
                                                <td class="mob_display" height='60' style='padding:0 0 0 10px'>
                                                    <span class="img_font">HOME</span>
                                                    <span class='live_font1'>${mainGame['fixture_participants_1_name']}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mob_display" height='60' style='padding:0 0 0 10px'>
                                                    <span class="img_font">AWAY</span>
                                                    <span class='live_font1'>${mainGame['fixture_participants_2_name']}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    </div>
                                    <div class="sports_s_right ${fixture_displayClass}" style="${fixture_display}">
                                        <ul class="dropdown3"></ul>
                                    </div>
                                    
                        </li>
                    `;

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
            openBetData(selectFixtureId);
            

            
            // 좌측 선택 처리
            if (locationId > 0) {
                dataForm['location_id'] = locationId;
                dataForm['sports_id'] = sportsId;
                $('.dropdown2 li').each(function(){
                    const $this = $(this);
                    if($this.hasClass('live_game_location_'+locationId)){
                        $this.attr('style', 'display: block;');
                        $this.addClass("active");
                        console.log('locationId : '+locationId);
                    }else {
                        $this.attr('style', 'display: none;')
                        $this.removeClass("active");
                        console.log('not locationId : '+locationId);
                    }
                });
                $('.location_block').each(function(){
                    const $this = $(this);
                    if($this.hasClass('location_id_'+locationId)){
                        $this.attr('style', 'display: block;')
                    }else {
                        $this.attr('style', 'display: none;')
                    }
                });
            }
            
            if (sportsId > 0) {
                dataForm['sports_id'] = sportsId;
                $('.dropdown2 li').each(function(){
                    const $this = $(this);
                    if($this.hasClass('live_game_display_'+sportsId)){
                        $this.attr('style', 'display: block;')
                        $this.addClass("active");
                    }else {
                        $this.attr('style', 'display: none;')
                        $this.removeClass("active");
                    }
                });
            }
            
            // 베팅슬립에 있는 배당중 하나라도 배당이 닫혔으면 초기화한다.
            if(activeBetIndex.length > 0){
                //console.log('bet count : '+activeBetIndex.length);
                activeBetIndex.forEach(function(item){
                    //console.log(item);
                    //console.log('check : '+obj.bet_tatus);
                    if(!betList_new.has(item)){
                        console.log('del bet');
                        initForm();
                        cartCount();
                    }else{
                        let obj = betList_new.get(item);
                        if(1 != obj.bet_status){
                            console.log('del2 bet : '+obj.bet_status);
                            initForm();
                            cartCount();
                        }
                    }
                    //$('.odds_btn[data-bet-id="' + item +'"]').trigger('click');
                });
                //$('.input_style07').val(setComma(betAmount));
                //changeWillWinMoney();
            }
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
            
            moreDisplaySelect(activeBetId);
            // 반복실행(이거를 켜면 펼쳐진 항목이 되돌아감) ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
            callGameLiveScoreList = setTimeout(function(){
                getRealTimeGameLiveScoreList(active1, active2);
                clearTimeout(callGameLiveScoreList);
            }, 2000);

            isAsyncGetRealTimeGameLiveScoreList = false;
            contentHeight();

        }).fail(function (error) {
            // alert('데이터 로드에 실패했습니다.');
        }).always(function (response) {
            
        });
    } // end getRealTimeGameLiveScoreList
    
    $(document).ready(function(){
        $('#league_name').val('<?=$league_name?>');
        
        // 새로고침 방지
        document.onkeydown = doNotReload;
        
        // 게임 배팅 리스트
        getRealTimeGameLiveScoreList(0, 0);
        
        // 첫경기 오픈 처리
        //$('#display_fixture_'+selectFixtureDisplay).slideDown();
        //console.log('selectFixtureDisplay'+selectFixtureDisplay);
        
        // 베팅 선택
        $(document).on('click','.odds_btn',function(){

            let betMoney = $('#betting_slip_money').val();
            betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
            if(0 < betMoney){
                // $('#betting_slip_money').val(0);
            }
            //console.log('odds_btn');
            let betListIndex = $(this).data('index');
            const indexArr = betListIndex.split('_');
            const baseLineKey = indexArr[2];
            //console.log(betListIndex);
            //let tdcell = $(this).data('td-cell');
            //console.log(tdcell);
            //const cellData = tdcell.split('_');
            //let fixture_start_date = cellData[1];
            let fixture_start_date = '';
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
            let totalOdds = $('.total_odds').data("total_odds");
            const betName = $(this).data('bet-name');
console.log('betName : '+betName);
            if ($(this).hasClass('bet_on') || $(this).hasClass('sports_select')){
                console.log('동일베팅 항목을 선택(선택해제)');
                // let price = $('[data-bet-id="' + betId + '"].slip_bet_ing .slip_bet_cell_r').text();
                let price = $(this).data('bet-price');
                let dataIndex = $(this).data('index');
                // console.log('price', price);
                price = price == 0 ? 1 : price;
                totalOdds = totalOdds / price;
                totalOdds = totalOdds == 1 ? 0 : totalOdds;
                $(this).removeClass('bet_on');
                $(document).find(`.odds_btn[data-index="${dataIndex}"]`).removeClass('bet_on');
                $('.slip_bet_ing[data-bet-id="' + betId + '"]').remove();
                $('.total_odds').data("total_odds",totalOdds);
                $('.total_odds').html(totalOdds);

                let betSlipCount = getBetSlipCountReal();
                /*if (betSlipCount > 7) {
                    $('.bonus_total_odds').html(odds_5_folder_bonus);
                } else if (betSlipCount < 5 && betSlipCount >= 3) {
                    $('.bonus_total_odds').html(odds_3_folder_bonus);
                } else if (betSlipCount < 3) {
                    $('.bonus_total_odds').html(0);
                }*/
                setBonusPrice(totalOdds, betSlipCount);

                changeWillWinMoney();
                //initForm();

                // 뱃팅슬롯 카운트
                if(isMobile){
                    $('.cart_count').text($('.slip_bet_ing').length);
                    $('.cart_count2').text($('.slip_bet_ing').length);
                }
                changeWillWinMoney();
                return;
            }

            // 같은 경기에 다른 게임이 이미 선택 되어있는 경우 이전 게임 선택 해제 및 리스트 삭제하고
            // 지금 클릭한 게임을 추가한다
            if ($('[data-fixture-id*=' + betListFixId + ']').hasClass('sports_select') || $('[data-fixture-id*=' + betListFixId + ']').hasClass('bet_on')
                    || isBettingFixture(betListFixId)){
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
                //if($selecteSports.length > 1){
                if($('.slip_bet_ing').length > 1){
                    const $this = $(this);
                    let flag1 = false;
                    let flag2 = false;
                    let targetObj;
                    const fixtureMarket1 = $this.data('index').split('_')[0] + '_' + $this.data('index').split('_')[1];
                    //$selecteSports.each(function(){
                    $('.slip_bet_ing').each(function(){
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
                    //$('[data-fixture-id*=' + betListFixId + ']').removeClass('sports_select');
                    //$('[data-fixture-id*=' + betListFixId + ']' + '.slip_bet_ing').remove();
                    //$('[data-fixture-id*=' + betListFixId + ']').removeClass('live_select');
                    $('[data-index*="' + fixtureId + '"]').removeClass('bet_on');
                    $('[data-index*="' + fixtureId + '"]' + '.sports_cart_bet').remove();
                }
                let betSlipCount = getBetSlipCountReal();
                /*if (betSlipCount > 7) {
                    $('.bonus_total_odds').html(odds_5_folder_bonus);
                } else if (betSlipCount < 5 && betSlipCount >= 3) {
                    $('.bonus_total_odds').html(odds_3_folder_bonus);
                } else if (betSlipCount < 3) {
                    $('.bonus_total_odds').html(0);
                }*/
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


            // add Class
            $(this).addClass('bet_on');

            // if ($(this).hasClass('sports_table_in_1') || $(this).hasClass('sports_table_in_2') || $(this).hasClass('sports_table_in_xo')) {
            //     $(this).addClass('bet_on');
            // }else {
            //     $(this).addClass('bet_on');
            // }

            // let html = "<li class='sports_cart_bet slip_bet_ing' data-index='"+betListIndex+"' data-odds-types="+betOddsTypes+
            //                         " data-bet-id="+betId+" data-bet-name=" + betName +" data-bet-price="+betPrice+" data-markets-name="+betMarketType+
            //                         " data-bet-base-line='"+betBaseLine+"' data-fixture-start-date='"+fixture_start_date+"' data-leagues_m_bet_money="+leagues_m_bet_money+">" +
            //                         "<div width='100%'class='cart_bet'>" +
            //                         "<div>" + 
            //                             "<td>"+obj['fixture_participants_1_name']+"<span class='sports_cart_bet_font1'> "+betOddsTypesDisplay+"</span></td>"+
            //                             "<td><a href='#' class='sports_cart_bet_img'><img src='/assets_w/images/cart_close.png'"+
            //                             "class='notify-close-btn' data-index="+betListIndex+" data-bet-id="+betId+"></a><span class='sports_cart_bet_p'>"+betPrice+"</span></td>"+
            //                         "</div>"+
            //                         "<div>"+
            //                             "<td colspan='2'><span class='sports_cart_bet_font2'>"+betMarketType+"</span></td>"+
            //                         "</div>"+
            //                         "<div>"+
            //                             "<td colspan='2'><span class='sports_cart_bet_font3'>"+obj['fixture_participants_1_name']+"<img src='/assets_w/images/vs.png' width='25'>"+obj['fixture_participants_2_name']+"</span></td>"+
            //                         "</div>"+
            //                     "</div>"+
            //                 "</li>";

            $(`[data-index="${betListIndex}"][data-odds-type="${betOddsTypes}"]`).addClass('bet_on');

            let html = `
                <li class='sports_cart_bet slip_bet_ing' data-index='${betListIndex}' data-odds-types='${betOddsTypes}' data-bet-id="${betId}" data-bet-name='${betName}' data-bet-price='${betPrice}' data-markets-name='${betMarketType}' data-bet-base-line='${betBaseLine}' data-fixture-start-date='${fixture_start_date}' data-leagues_m_bet_money="${leagues_m_bet_money}">
                    <div width='100%'class='cart_bet'>
                        <div>
                                <span class='sports_cart_bet_font1'>
                                    ${obj["fixture_participants_1_name"]}
                                    <img src='/assets_w/images/vs.png' width='25'>
                                    ${obj["fixture_participants_2_name"]}
                                </span>
                            
                        </div>
                        <div>
                                <span class='sports_cart_bet_font2'>${betMarketType}</span>
                        </div>
                        <div class="sports_cart_bet_font3">
                                ${obj["fixture_participants_1_name"]}
                                <span class='sports_cart_bet_font1'>${betOddsTypesDisplay}</span>
                                <span class='sports_cart_bet_p'>${betPrice}</span>
                        </div>

                        <a href='#' class='sports_cart_bet_img'>
                            <img src='/assets_w/images/cart_close.png' class='notify-close-btn' data-index='${betListIndex}' data-bet-id='${betId}'>
                        </a>
                    </div>
                </li>
            `;
            //console.log(html);
            $('.slip_tab_wrap').prepend($(html));

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
    function openBetData(fixture_id){
        //console.log('openBetData : '+fixture_id);
        //console.log('selectFixtureId : '+selectFixtureId);
        //selectFixtureId = fixture_id;
        let betting_html= '';
        const today = new Date();
        
        // 이미 불러왔으면 불러오지 않는다. livescorelist에서 반복됨
        /*if($(".fixture_"+selectFixtureId).length > 0){
            //$(".fixture_"+selectFixtureId).slideDown();
            return;
        }*/
        
        sports.forEach(function (sports_id) {
            //console.log(live_data);
            let list = live_data[sports_id];
            if(!list) {
                return true;
            }
            //console.log('openBetData : '+list);
            for (const[fixtureKey, fixture_list] of Object.entries(list)) {
                if(fixture_id != fixtureKey) continue;
                for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
                    // market_data, game_count는 마켓리스트, 게임갯수이므로 출력하지 않는다.
                    if('market_data' === memuKey || 'game_count' === memuKey){
                        //console.log(memuKey);
                        break;
                    }
                        
                    let isMainBetLock = false;
                    // 배구, 야구는 메인만 존재
                    // 갬블은 메인메뉴 개념이 없다.
                    //if(memuKey > 0) continue;
                    betting_html += "<li class='bet_list1_wrap fixture_"+fixtureKey+"' name='fixture_"+fixtureKey+"' >";
                    for (const[marketKey, game_list] of Object.entries(menu_list)) {
                        let markets_name = game_list[0]['markets_name_origin'];
                        let bGameKey = '';
                        // 마켓명 표기
                        $sportsLineColor = getSportsLineColor(sports_id);
                        
                        betting_html += "<a href='#'>"+
                                "<div class='bet_list1_wrap_in_title "+$sportsLineColor+"'style='margin-bottom: 1px;'>"+markets_name+"<span class='bet_list1_wrap_in_title_right'></span></div>"+
                                "</a>"+
                                "<ul class='bet_list1_wrap_in_new' id='market_"+marketKey+"' style='display:block'>"+
                                "<table width='100%' border='0' cellspacing='0' cellpadding='0' style='padding: 0 0 0 0;'>";
                        for (const[bKey, game] of Object.entries(game_list)) {
                            bGameKey = game['fixture_id']+'_'+game['markets_id']+'_'+game['bet_base_line']+'_'+game['providers_id'];
                            markets_name = game['markets_name'];
                            let markets_name_origin = game['markets_name_origin'];
                            let markets_display_name = game['markets_display_name'];
                            //const timeValue = new Date(game['fixture_start_date']);
                            //let betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);
                            //betweenTime = 700;
                            const checkTime = 600;
                            /*let b_find = false;
                            for(const betData of game['bet_data']){
                                if(betData['bet_status'] == 2){
                                    b_find = true;
                                    break;
                                }
                            }

                            if(game['bet_status'] != 1 || true == b_find) continue;*/
                            //thisMarkettotalCnt += 1;
                            betting_html += "<tr>";
                            // 승무패
                            if(1 == game['menu']) {
                                game['bet_data'].forEach(function(betData) {
                                    if(betData['bet_name'] === '1'){
                                         game['win_bet_id'] = betData['bet_id'];
                                         game['win'] = betData['bet_price'];
                                         game['win_bet_name'] = betData['bet_name'];
                                    }else if(betData['bet_name'] === '2'){
                                        game['lose_bet_id'] = betData['bet_id'];
                                        game['lose'] = betData['bet_price'];
                                        game['lose_bet_name'] = betData['bet_name'];
                                    }else{
                                        game['draw_bet_id'] = betData['bet_id'];
                                        game['draw'] = betData['bet_price'];
                                        game['draw_bet_name'] = betData['bet_name'];
                                    }
                                })
                                /*betting_html += "<td class=\"sports_table_in_1 odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + game['win'] +"\" data-odds-type=\"win\" data-bet-id=" + game['win_bet_id'] + " data-bet-price=" + game['win'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
                                    "      <div class=\"sports_v_l\">"+ game['fixture_participants_1_name'] +"</div><div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['win_bet_id'] +"\">"+ game['win'] +"</div>\n"+
                                    "    </td>\n";
                                if(game['draw']){
                                    betting_html +=
                                    "    <td class=\"sports_table_in_xo odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + game['win'] +"\" data-odds-type=\"draw\" data-bet-id=" + game['draw_bet_id'] + " data-bet-price=" + game['draw'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
                                    "      <div class=\"sports_v_l\">무</div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['draw_bet_id'] +"\">"+ game['draw'] +"</div>\n"+
                                    "    </td>\n";
                                }
                                betting_html +=
                                    "    <td class=\"sports_table_in_2 odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds-type=\"lose\" data-bet-id=" + game['lose_bet_id'] + " data-bet-price=" + game['lose'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
                                    "        <div class=\"sports_l_l\">"+ game['fixture_participants_2_name'] +"</div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['lose_bet_id'] +"\">" + game['lose'] + "</div>\n"+
                                    "    </td>\n";*/

                                // 배당 표기
                                if (Object.keys(game['bet_data']).length == 3) {
                                    if((1 == game['bet_status'] /*&& 2 == game['display_status']*/)){
                                        betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                                " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+game['win_bet_name']+"'"+
                                                ">"+game['fixture_participants_1_name']+" <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
                                        betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='draw' data-bet-id='"+ game['draw_bet_id'] +"' data-bet-price='"+ game['draw'] +"'"+
                                                " data-td-cell='"+game['draw_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+game['draw_bet_name']+"'"+
                                                ">무 <span class='betin_right bet_font1'>"+game['draw']+"</span></td>";
                                        betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                                " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+game['lose_bet_name']+"'"+
                                                ">"+game['fixture_participants_2_name']+" <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
                                    }else{
                                        isMainBetLock = true;
                                        betting_html += "<td class='bet_list_td w30'>"+game['fixture_participants_1_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='tdtd lock' width='13'></span></td>"+
                                                "<td class='bet_list_td w30'>무<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                                "<td class='bet_list_td w30'>"+game['fixture_participants_2_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                    }
                                } else {
                                    if((1 == game['bet_status'] && 2 == game['display_status'])){
                                        betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                                " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+game['win_bet_name']+"'"+
                                                ">"+game['fixture_participants_1_name']+" <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
                                        betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                                " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+game['lose_bet_name']+"'"+
                                                ">"+game['fixture_participants_2_name']+" <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
                                    }else{
                                        isMainBetLock = true;
                                        betting_html += "<td class='bet_list_td w50'"+
                                                ">"+game['fixture_participants_1_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                                "<td class='bet_list_td w50'"+
                                                ">"+game['fixture_participants_2_name']+"<span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                    }
                                }
                                html += "</tr>";
                            // 핸디캡
                            }else if(2 == game['menu']) {
                                let handValue_l = 0;
                                let handValue_r = 0;
                                let handValue_c = 0;
                                let tm_l = '';
                                let tm_r = '';
                                let bet_line = bet_line_second = 0;
                                game['win_bet_name'] = game['lose_bet_name'] = '';
                                for (const[bKey, value] of Object.entries(game['bet_data'])) {
                                    if(value['bet_name'] == 1) {
                                        game['win'] = value['bet_price'];
                                        game['win_bet_id'] = value['bet_id'];
                                        game['win_bet_line'] = value['bet_line'];
                                        game['win_bet_name'] = value['bet_name'];
                                        //handValue_l = value['bet_line'].split(' ')[0];
                                        bet_line = Number(value['bet_line'].split(' ')[0]);
                                        if(typeof value['bet_line'].split(' ')[1] != 'undefined'){
                                            bet_line_second = value['bet_line'].split(' ')[1];
                                            bet_line_second = bet_line_second.replace('(', '');
                                            bet_line_second = bet_line_second.replace(')', '');
                                            bet_line_second = bet_line_second.split('-');
                                            handValue_l = bet_line + Number(bet_line_second[0]) - Number(bet_line_second[1]);
                                        }else{
                                            handValue_l = bet_line;
                                        }

                                        if(handValue_l > 0)
                                            handValue_l = '+' + handValue_l.toFixed(1);
                                        else
                                            handValue_l = handValue_l.toFixed(1);

                                        handValue_l = handValue_l == 'NAN' ? value.bet_line : handValue_l;
                                        tm_l = game['fixture_participants_1_name'] +"("+handValue_l+")";

                                        if(13 == game['markets_id']){
                                            let homeScore = Number(value['bet_line'].split(':')[0]);
                                            let awayScore = Number(value['bet_line'].split(':')[1]);
                                            handValue_l = homeScore - awayScore;
                                            tm_l = '승';
                                        }

                                    }else if(value['bet_name'] == 2) {
                                        game['lose'] = value['bet_price'];
                                        game['lose_bet_id'] = value['bet_id'];
                                        game['lose_bet_line'] = value['bet_line'];
                                        game['lose_bet_name'] = value['bet_name'];
                                        //handValue_r = value['bet_line'].split(' ')[0];
                                        bet_line = Number(value['bet_line'].split(' ')[0]);
                                        if(typeof value['bet_line'].split(' ')[1] != 'undefined'){
                                            bet_line_second = value['bet_line'].split(' ')[1];
                                            bet_line_second = bet_line_second.replace('(', '');
                                            bet_line_second = bet_line_second.replace(')', '');
                                            bet_line_second = bet_line_second.split('-');
                                            handValue_r = bet_line + Number(bet_line_second[0]) - Number(bet_line_second[1]);
                                        }else{
                                            handValue_r = bet_line;
                                        }

                                        if(handValue_r > 0)
                                            handValue_r = '+' + handValue_r.toFixed(1);
                                        else
                                            handValue_r = handValue_r.toFixed(1);

                                        handValue_r = handValue_r == 'NAN' ? value.bet_line : handValue_r;
                                        tm_r = game['fixture_participants_2_name'] +"("+handValue_r+")";

                                        if(13 == game['markets_id']){
                                            let homeScore = Number(value['bet_line'].split(':')[0]);
                                            let awayScore = Number(value['bet_line'].split(':')[1]);
                                            handValue_r = homeScore - awayScore;
                                            tm_r = '패';
                                        }
                                    }else {
                                        game['draw'] = value['bet_price'];
                                        game['draw_bet_id'] = value['bet_id'];
                                        game['draw_bet_line'] = value['bet_line'];
                                        let homeScore = Number(value['bet_line'].split(':')[0]);
                                        let awayScore = Number(value['bet_line'].split(':')[1]);
                                        handValue_c = homeScore - awayScore;
                                    }
                                }

                                // 배당 표기
                                if(1 == game['bet_status'] /*&& 2 == game['display_status']*/ && false == isMainBetLock){
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                            " data-odds-type='"+game['fixture_participants_1_name']+"("+ handValue_l +")' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
                                            " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            " data-bet-name='"+game['win_bet_name']+"'"+
                                            ">"+game['fixture_participants_1_name']+"(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['win']+"</span></td>";
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                            " data-odds-type='"+game['fixture_participants_2_name']+"("+ handValue_r +")' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
                                            " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            " data-bet-name='"+game['lose_bet_name']+"'"+
                                            ">"+game['fixture_participants_2_name']+"(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['lose']+"</span></td>";
                                    betting_html += "</tr>";
                                }else{
                                    betting_html += "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                            ">"+game['fixture_participants_1_name']+"(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                            "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
                                            ">"+game['fixture_participants_2_name']+"(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                }
                            }else if(3 == game['menu']) {
                                        let over = 0;
                                        let over_bet_id = 0;
                                        let over_status = 0;
                                        let over_base_line = '';
                                        let under = 0;
                                        let under_bet_id = 0;
                                        let under_status = 0;
                                        let under_base_line = '';
                                        let over_bet_name = under_bet_name = '';
                                game['bet_data'].forEach(function(betData) {
                                    if(betData['bet_name'] === 'Over'){
                                        over = betData['bet_price'];
                                        over_bet_id = betData['bet_id'];
                                        over_status = betData['bet_status'];
                                        over_bet_name = betData['bet_name'];
                                        over_base_line = betData['bet_base_line'];
                                    }else{
                                        under = betData['bet_price'];
                                        under_bet_id = betData['bet_id'];
                                        under_status = betData['bet_status'];
                                        under_bet_name = betData['bet_name'];
                                        under_base_line = betData['bet_base_line'];
                                    }
                                })
                                
                                /*if(9551528 == fixture_id){
                                    console.log('check : '+typeof(over_status));
                                    console.log('check : '+over_status);
                                    
                                    if(1 == over_status){
                                        console.log('check data: '+over_status);
                                    }
                                }*/
                                // 배당 표기
                                if(1 == game['bet_status'] /*&& 2 == game['display_status']*/ && false == isMainBetLock){
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ over_status + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                            " data-odds-type='오버("+ over_base_line +")' data-bet-id='"+ over_bet_id  +"' data-bet-price='"+ over +"'"+
                                            " data-td-cell='"+over_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            " data-bet-name='"+over_bet_name+"'"+
                                            ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr2.gif' style='margin-right: 5px'>"+over+"</span></td>";
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ under_status + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                            " data-odds-type='언더("+ under_base_line +")' data-bet-id='"+ under_bet_id  +"' data-bet-price='"+ under +"'"+
                                            " data-td-cell='"+under_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                            " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                            " data-bet-name='"+under_bet_name+"'"+
                                            ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/assets_w/images/arr1.gif' style='margin-right: 5px'>"+under+"</span></td>";
                                }else{
                                    betting_html += "<td class='bet_list_td w50' data-bet-status='"+ over_status + "'"+
                                            ">오버("+ over_base_line  +") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>"+
                                            "<td class='bet_list_td w50' data-bet-status='"+ under_status + "'"+
                                            ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/images/icon_lock.png' alt='lock' width='13'></span></td>";
                                }
                                betting_html += "</tr>";
                            // 기타
                            }else if(4 == game['menu']) {
                                let yesBetPrice = '';
                                let yesBetId = '';
                                let noBetPrice = '';
                                let noBetId = '';
                                let bet_name_yes = bet_name_no = '';
                                let yesBetStatus = NoBetStatus = 0;
                                game['bet_data'].forEach(function(betData) {
                                    if(betData['bet_name'] === 'Yes'){
                                        yesBetPrice = betData['bet_price'];
                                        yesBetId = betData['bet_id'];
                                        yesBetStatus = betData['bet_status'];
                                        bet_name_yes = betData['bet_name'];
                                    }else{
                                        noBetPrice = betData['bet_price'];
                                        noBetId = betData['bet_id'];
                                        NoBetStatus = betData['bet_status'];
                                        bet_name_no = betData['bet_name'];
                                    }
                                })

                                if(game['bet_data'][0]['bet_name'] === 'No'){
                                    let betData_no = game['bet_data'][0];
                                    let betData_yes = game['bet_data'][1];
                                    let display_bet_name_no = '아니요';//betNameToDisplay_new(betData_no['bet_name'],game['markets_id']);
                                    let display_bet_name_yes = '예';//betNameToDisplay_new(betData_yes['bet_name'],game['markets_id']);

                                    betting_html += "<tr>"
                                    /*betting_html += "<td class='bet_list_td w50 odds_btn' data-index='"+ bGameKey +"' data-fixture-id='"+ game['fixture_id'] +"' data-odds='" + yesBetPrice +"' data-odds-type='" + display_bet_name_yes + "' data-bet-id=" + yesBetId + " data-bet-price=" + yesBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
                                    "        <div class='sports_v_l'>"+ display_bet_name_yes +" </div> <div class='betin_right bet_font1' id='betInfo_"+ game['fixture_id'] + '_' + yesBetId +"'>"+ yesBetPrice +" </div>\n"+
                                    "    </td>\n"+
                                    "    <td class='bet_list_td w50 odds_btn' data-index='"+ bGameKey +"' data-fixture-id='"+ game['fixture_id'] +"' data-odds='" + noBetPrice + "' data-odds-type='" + display_bet_name_no + "' data-bet-id=" + noBetId + " data-bet-price=" + noBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
                                    "        <div class='sports_l_l'>"+ display_bet_name_no +" </div> <div class='betin_right bet_font1' id='betInfo_"+ game['fixture_id'] + '_' + noBetId +"'>" + noBetPrice + " </div>\n"+
                                    "    </td>\n";*/
                            
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ yesBetStatus +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='"+display_bet_name_yes+"' data-bet-id='"+ yesBetId +"' data-bet-price='"+ yesBetPrice +"'"+
                                                " data-td-cell='"+yesBetId+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+betData_yes['bet_name']+"'"+
                                                ">"+display_bet_name_yes+"<span class='betin_right bet_font1'>"+yesBetPrice+"</span></td>";
                                        
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ NoBetStatus +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='"+display_bet_name_no+"' data-bet-id='"+ noBetId +"' data-bet-price='"+ noBetPrice +"'"+
                                                " data-td-cell='"+noBetId+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+betData_no['bet_name']+"'"+
                                                ">"+display_bet_name_no+"<span class='betin_right bet_font1'>"+noBetPrice+"</span></td>";
                                    betting_html += "</tr>";
                                }else if(game['bet_data'][1]['bet_name'] === 'No'){
                                    let betData_no = game['bet_data'][1];
                                    let betData_yes = game['bet_data'][0];
                                    let display_bet_name_no = '아니요';//betNameToDisplay_new(betData_no['bet_name'],game['markets_id']);
                                    let display_bet_name_yes = '예';//betNameToDisplay_new(betData_yes['bet_name'],game['markets_id']);

                                    betting_html += "<tr>"
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ yesBetStatus +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='"+display_bet_name_yes+"' data-bet-id='"+ yesBetId +"' data-bet-price='"+ yesBetPrice +"'"+
                                                " data-td-cell='"+yesBetId+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+display_bet_name_yes+"'"+
                                                "><span class='betin_right bet_font1'>"+yesBetPrice+"</span></td>";
                                        
                                    betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ NoBetStatus +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='"+display_bet_name_no+"' data-bet-id='"+ noBetId +"' data-bet-price='"+ noBetPrice +"'"+
                                                " data-td-cell='"+noBetId+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+display_bet_name_no+"'"+
                                                "><span class='betin_right bet_font1'>"+noBetPrice+"</span></td>";
                                    betting_html += "</tr>";
                                }else{
                                    let count = game['bet_data'].length;
                                    for(let i=0; i<count; ++i){
                                        let betData = game['bet_data'][i];
                                        //let display_bet_name = StatusUtil::betNameToDisplay_new(betData['bet_name']);
                                        let display_bet_name = betData['bet_name'];
                                        /*betting_html += "<tr>"
                                                +"<td class='sports_table_in_1 odds_btn' data-index=\"" + bGameKey + "\" data-odds-type=\"win\" data-bet-id=" + betData['bet_id'] + " data-bet-price='"+betData['bet_price'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}`+"'>"
                                                +"<div class='sports_v_l'>"+display_bet_name+" </div> <div class='sports_v_r'>"+betData['bet_price']+"</div>"
                                                +"</td>";*/
                                        
                                        betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
                                                " data-odds-type='win' data-bet-id='"+ game['bet_id'] +"' data-bet-price='"+ game['bet_price'] +"'"+
                                                " data-td-cell='"+game['bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
                                                " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
                                                " data-bet-name='"+game['bet_name']+"</td>";
                                        if((i+1)%3 == 0){
                                            betting_html += "</tr>"
                                                    +"<tr>";
                                        }
                                    }
                                    betting_html += "</tr>";
                                }
                            } // end game['menu']
                            betting_html += "</tr>\n";

                            //betListIndex ++;
                        } // end game_list
                        betting_html += "</table>";
                        betting_html += "</ul>";
                    } // end menu_list
                } // end fixture_list
            }
        });
        
        //$('[data-bet-id*="' + fixtureId + '"]
        // 이전경기 닫기
         $(".dropdown3 li").remove();
        //$('.dropdown3[fixture_="' + item +'"]').remove();
        //console.log(betting_html);

        //console.log('openBetData : '+fixture_id);
        // console.log('selectFixtureId : '+selectFixtureId);


        // $(".dropdown3").append(betting_html);

        // $(".dropdown3").append(betting_html);
        $(`li[data-fixturekey=${fixture_id}] .dropdown3`).append(betting_html);


        
        // 배팅슬립에 있는 배당 선택처리
        /*$('.slip_bet_ing').each(function(item) {
            console.log(item);
            const betListIndex = $(item).data('index');
            //$('[data-td-cell*="' + betId + '_' + fixture_start_date + '"]').addClass('bet_on');
            //$('[data-bet-id*="' + betId + '"]').addClass('bet_on');
        });*/
    }

    $(function(){
        
        cartCount();
        scrollFixed();

        $(document).on("click", ".odds_btn", function(){
            cartCount();
        });

        // cart open btn
        $(document).on("click", ".btn__cart-open", function(e){
            e.preventDefault();
            $(document).find(".sports_wide_right").toggleClass("active");
        });
        $(document).on("click", ".btn__cart_close, .cart_bg", function(e){
            e.preventDefault();
            $(document).find(".sports_wide_right").removeClass("active");
        });


    });

    


    $(window).on("scroll", function(e){
        scrollFixed();
    })

    $(window).on("resize", function(e){
        scrollFixed();
    })


    const cartCount = function(){
        const cart_count = $(document).find(".sports_wide_right .sports_cart_bet").length;
        $(document).find(".cart_open .cart_count2").html(cart_count);
    }

    const contentHeight = function(){
        let targetHeight = $(document).find(".sports_s_right.active").height();
        targetHeight = Number(targetHeight) + 100;
        $(document).find(".sports_s_left_sports_s_right_wrap").css("min-height", `${targetHeight}px`)
    }


    const scrollFixed = function(){
        let scroll = $(window).scrollTop();
        let winH = $(window).height();

        let myScroll = Math.ceil(scroll + winH);
        let docH = $(document).height();
        let myEnd = docH - 250;

        // console.log(scroll, myScroll, myEnd);
        
        // // data infinite scroll
        // if( (myScroll >= docH) && (docHBefore != docH) ){
        //     docHBefore = docH;
        //     $scrollNum++;
        //     getClassicList();
        // }

        // lnb, cart fixed
        if(scroll > 121 ){      $("body").addClass("fixed");
        } else {                $("body").removeClass("fixed");
        }

        if( myScroll >= myEnd ){     $("body").addClass("scrollEnd");
        } else {                    $("body").removeClass("scrollEnd");
        }
    }

    

</script>


    
<!-- 12/8 라이브스포츠 아코디언 js 수정 -->
<script>
    $(document).ready(function(){
        $(".bet_list1_wrap_in_new").css('display', 'block');

        // $(document).on("click", ".dropdown3 .bet_list1_wrap_in_title", function(e){
        //     e.preventDefault();
        //     // console.log( $(this).parents(".bet_list1_wrap").find(".bet_list1_wrap_in_new").slideToggle() );
        //     // $(this).parents(".bet_list1_wrap").find(".bet_list1_wrap_in_new").css('display', 'block');
        //     //console.log(target);
        //     // $(this).parents(".bet_list1_wrap").find(".bet_list1_wrap_in_new").slideToggle();

        //     return false;

        // });

        // $('.dropdown3 .bet_list1_wrap_in_title').each(function(){
        //     $(this).click(function(){
        //         $(this).parent('a').next('ul').slideToggle();
        //         return false;
        //     });
        // });
        
        // var drop4 = $('.dropdown4');
        // drop4.hide();
        // $(".sport_title_list .sport_more").click(function(){
        //     var width_size = window.innerWidth;
        //     if(width_size<=1240){
        //         if($(this).hasClass('on')){
        //             $(this).removeClass('on');
        //             $(this).next(drop4).remove();
        //         }else{
        //             drop4.show();
        //             $(".sport_title_list .sport_more").removeClass('on');			
        //             $(this).addClass('on');
        //             $(this).parent('ul').append(drop4);
        //         };				
        //     }else{
        //         drop4.hide();
        //         return false;
        //     };
        // });

    });
</script> 

</body>
</html>