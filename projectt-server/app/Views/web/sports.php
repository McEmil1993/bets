<?php
use App\Util\DateTimeUtil;
use App\Util\StatusUtil;
$imageBasePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath;
$p_data['num_per_page'] = SPORRS_BLOCK_COUNT;
$p_data['page_per_block'] = 15; //_B_BLOCK_COUNT;
$p_data['start'] = ($page - 1) * $p_data['num_per_page'];

$total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
//$total_page = 0;
$total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
$block = ceil($page / $p_data['page_per_block']); // 현재 블럭
$first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
$last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지
if ($block >= $total_block)
    $last_page = $total_page;
$default_link = 'sports?data=1';
$sportsId = isset($_GET['sports_id']) ? $_GET['sports_id'] : 0;
$leagueId = isset($_GET['league_id']) ? $_GET['league_id'] : 0;

if($sportsId != 0) $default_link .= '&sports_id='.$sportsId;
//if($locationId != 0) $default_link .= '&location_id='.$locationId;
$leftSports[0] = Array('id'=>0, 'name'=>'전체', 'count'=>0);
$leftSports[SOCCER] = Array('id'=>SOCCER, 'name'=>'축구', 'count'=>0);
$leftSports[BASKETBALL] = Array('id'=>BASKETBALL, 'name'=>'농구', 'count'=>0);
$leftSports[BASEBALL] = Array('id'=>BASEBALL, 'name'=>'야구', 'count'=>0);
$leftSports[VOLLEYBALL] = Array('id'=>VOLLEYBALL, 'name'=>'배구', 'count'=>0);
$leftSports[ICEHOCKEY] = Array('id'=>ICEHOCKEY, 'name'=>'아이스하키', 'count'=>0);
$leftSports[ESPORTS] = Array('id'=>ESPORTS, 'name'=>'이스포츠', 'count'=>0);
$leftSports[UFC] = Array('id'=>UFC, 'name'=>'UFC', 'count'=>0);
$leftSports[TENNIS] = Array('id'=>TENNIS, 'name'=>'테니스', 'count'=>0);
?>



<?= view('/web/common/header') ?>
<script src="/assets_w/js/sports_common_w.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets_w/js/sports_common.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets_w/js/sports_realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script>


<!-- <div id="wrap"> -->
<?= view('/web/common/header_wrap') ?>
<!-- <input type="hidden" id="sports_id"> -->
<div id="sports_wide_wrap">
    <div class="sports_wide_left">
        <div class="search">
            <ul>
                <li>
                    <input name="league_name" id="league_name" type="text" class="input_search" placeholder="국가 및 팀명"></li>
                <li style="float:right;"><a onclick="searchLeague()"><span class="search_btn">검색</span></a></li>
            </ul>
        </div>
        <div class="con_box_left">
            <ul class="dropdown">
                <?php foreach ($leftSports as $key => $value) {
                    $arrLeagueCheck = array();
                    if(isset($sports[$key]))
                        $sport= $sports[$key];
                    else
                        $sport= $value;
                ?>
                <li class="menu1">
                    <a href="javascript:void(0);">
                        <div class="left_list1" id="left_menu_<?=$sport['id']?>">
                            <span class="menu_left">
                                <img src="<?=$imageBasePath.'/sports/'?>/icon_game<?=$sport['id']?>.png" width="18">&nbsp;&nbsp;&nbsp;<?=$sport['name'].''?>
                            </span>
                            <span class="m enu_right">
                                <span class="menu_right_box" id="sports_count_<?=$sport['id']?>"><?= isset($sports[$sport['id']]['count']) ? $sports[$sport['id']]['count'] : 0 ?></span>
                            </span>
                        </div>
                    </a>
                    <?php if($sports[$sport['id']]['count'] ?? 0 > 0){ 
                        $totalFixtureCnt = ($totalFixtureCnt ?? 0) + $sports[$sport['id']]['count'];
                    ?>
                        <ul style="display: none;">
                        <li>
                        <?php foreach ($locationGameList[$sport['id']]['location_all'] as $locationKey => $locationData){ 
                                foreach ($locationData as $llKey => $leagueData){
                                    // 이미 출력한 리그는 제외
                                    if(in_array($leagueData['fixture_league_id'], $arrLeagueCheck)) continue;
                                    $arrLeagueCheck[] = $leagueData['fixture_league_id'];
                        ?>
                            <a href="javascript:leftLeagueItemClick(<?=$sport['id'].''?>, <?= $leagueData['fixture_league_id'] ?>)"><span class="left_list1_in"><?= $leagueData['fixture_league_name'] ?> <span class="menu_right_box"><?= $locationFixtureCount[$sport['id']][$leagueData['fixture_league_name']] ?></span></span></a>
                        <?php 
                                 }
                                }
                        ?>
                        </li>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </div>          
		<!--<script src="js/tendina.min.js"></script>-->          
		<script src="/assets_w/js/tendina.min.js"></script>
		<script>
			  $('.dropdown').tendina({
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
		
			$(document).ready(function(){
			$("li.menu1").click(function(){
				$(this).addClass("on");
				$(this).siblings().removeClass("on");
			});
		});
		</script>   	




        
    </div><!-- sports_wide_left -->
    
    <div class="sports_wide_center">
        <div class="sports_list_title">
            <div class="sports_list_title2">
                <select class="sports_input1" onchange="sports_select(this.value)">
                    <option value="0">전체종목</option>
                    <?php foreach ($sports as $key => $s) { ?>
                    <option value="<?= $s['id'] ?>" <?=($s['id']==$sportsId)?'selected':''?>><?= $s['name'] ?></option>
                    <?php } ?>
                </select>
                
                <select class="sports_input2" onchange="league_select(this.value)">
                    <option value="0">전체리그</option>
                    <?php foreach ($leagues as $key => $lo){ ?>
                    <option value="<?= $lo['id'] ?>" <?=($lo['id']==$leagueId)?'selected':''?>><?= $lo['display_name'] ?></option>
                    <?php } ?>
                </select>

            </div>
            <!-- <div class="sports_list_title2">
            </div>       -->
            <div class="sports_list_title3">
            	<ul>
                    <li><a href="javascript:sports_select(0)"><span id="sports_img_0" class=""><img src="/assets_w/images/icon01.png" width="18">&nbsp; 전체</span></a></li>
                    <li><a href="javascript:sports_select(6046)"><span id="sports_img_6046" class=""><img src="/assets_w/images/icon02.png" width="18">&nbsp; 축구</span></a></li>
                    <li><a href="javascript:sports_select(48242)"><span id="sports_img_48242" class=""><img src="/assets_w/images/icon03.png" width="18">&nbsp; 농구</span></a></li>                    
                    <li><a href="javascript:sports_select(154914)"><span id="sports_img_154914" class=""><img src="/assets_w/images/icon04.png" width="18">&nbsp; 야구</span></a></li>
                    <li><a href="javascript:sports_select(154830)"><span id="sports_img_154830" class=""><img src="/assets_w/images/icon05.png" width="18">&nbsp; 배구</span></a></li>
                    <li><a href="javascript:sports_select(35232)"><span id="sports_img_35232" class=""><img src="/assets_w/images/icon06.png" width="18">&nbsp; 아이스하키</span></a></li>
                    <li><a href="javascript:sports_select(687890)"><span id="sports_img_687890" class=""><img src="/assets_w/images/icon07.png" width="18">&nbsp; e스포츠</span></a></li> 
                    <li><a href="javascript:sports_select(154919)"><span id="sports_img_154919" class=""><img src="/assets_w/images/icon08.png" width="18">&nbsp; UFC</span></a></li>
                    <li><a href="javascript:sports_select(54094)"><span id="sports_img_54094" class=""><img src="/assets_w/images/icon11.png" width="18">&nbsp; 테니스</span></a></li>
                </ul>
            </div>
            <!-- <div class="sports_list_title4"></div> -->
        </div>
        <div class="sports_s_left_sports_s_right_wrap">
            <div class="sports_s_left">
                <!-- <div class="bet_title_wrap">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <td class="bet_title1">경기시간</td>
                            <td class="bet_title2">국가</td>
                            <td class="bet_title2_1">종목</td>
                            <td class="bet_title3">팀</td>
                            <td class="bet_title4">승</td>
                            <td class="bet_title5">무</td>
                            <td class="bet_title6">패</td>
                            <td class="bet_title8">맥스</td>                        
                            <td class="bet_title7">더보기</td>
                        </tr>
                        </thead>
                    </table>
                </div> -->

                <div class="sports_list_bonus">
                    <ul>
                        <li class="odds_3_folder_bonus">
                            <span class="bonus1">3폴더</span>이상
                            <div class="bonus_txt"><?= $arr_bonus['odds_3_folder_bonus']; ?></div>
                        </li>
                        <li class="odds_5_folder_bonus">
                            <span class="bonus1">5폴더</span>이상
                            <div class="bonus_txt"><?= $arr_bonus['odds_5_folder_bonus']; ?></div>
                        </li>
                        <li class="odds_7_folder_bonus">
                            <span class="bonus1">7폴더</span>이상
                            <div class="bonus_txt"><?= $arr_bonus['odds_7_folder_bonus']; ?></div>
                        </li>
                    </ul>
                </div>
                
                <div class="dropdown2">
                <?php
                $firstFixtureid = 0;
                $firstFixtureTime = '';
                $firstFixtureTeam1 = '';
                $firstFixtureTeam2 = '';
                $beforeLeagueType = '';
                $leagueType = '';
                $leagueLine = '';
                $beforeLeagueLine= '';
                $beforeSports_id = null;

                // echo json_encode($gameList);
                // exit;
                
                foreach ($gameList as $timeKey => $sports_list) {
                    // 빈배열이 섞여들어온다. 제외한다.
                    if($timeKey == ''){
                        continue;
                    }

                    foreach ($sports_list as $fixtureKey => $fixture_list) {
                    $sportsKey = $fixture_list[0]['fixture_sport_id'];
                    $betCount = $fixture_list[0]['game_count'];
                    $afFid = $fixtureKey;

                    if($sportsId != '' && str_replace('game_', '', $sportsKey) != $sportsId){
                        continue;
                    }

                    if (!isset($fixture_list[0])) {
                        continue;
                    }

                    // 각종목 메인항목이 없으면 거른다.
                    if (!isset($fixture_list[0])) {
                        continue;
                    }

                    //$mainGame = $fixture_list[0][1][$sportsDisplay[$sportsKey]][0];
                    $mainGame = $fixture_list[0];

                    if(!isset($mainGame)){
                        continue;
                    }
                    
                    if($firstFixtureid == 0){
                        $firstFixtureid = $mainGame['fixture_id'];
                        $firstFixtureTime = date('H:i', strtotime($mainGame['fixture_start_date']));
                        $firstFixtureTeam1 = addslashes($mainGame['fixture_participants_1_name']);
                        $firstFixtureTeam2 = addslashes($mainGame['fixture_participants_2_name']);
                    }

                    $key = $mainGame['fixture_id'] . '_' . $mainGame['markets_id'] . '_' . $mainGame['bet_base_line'] . '_' . $mainGame['providers_id'].'_'.$mainGame['fixture_start_date'];  // 앞단에 출력되는 게임데이터키
                    //$mainKeys = array_keys($fixture_list[0]);
    //$this->logger->debug(json_encode($mainGame));
                    // 메인메뉴 키값(메인, 승무패/승패...)
                    $arrMenuKey = array_keys($fixture_list);
                    sort($arrMenuKey);

                    // 메인화면 베팅 - 베팅슬립에 출력할 선택한 베팅명
                    $mainMarketsName = $mainGame['markets_name_origin'];
                    $mainMarketsId = $mainGame['markets_id'];
                    $tm_l = '';
                    $tm_r = '';

                    if ($mainMarketsId == 1 || $mainMarketsId == 226 || $mainMarketsId == 52) {
                        $tm_l = 'win';
                        $tm_r = 'lose';
                    }

                    // 앞단 테이블에 뿌려질 데이터
                    foreach ($mainGame['bet_data'] as $mainBetKey => $mainBetData) {
                        if (0 == strcmp($mainBetData['bet_name'], '1')) {
                            $mainGame['win_bet_id'] = $mainBetData['bet_id'];
                            $mainGame['win'] = $mainBetData['bet_price'];
                            $mainGame['bet_status'] = $mainBetData['bet_status'];
                            $mainGame['win_bet_name'] = $mainBetData['bet_name'];
                        } else if (0 == strcmp($mainBetData['bet_name'], '2')) {
                            $mainGame['lose_bet_id'] = $mainBetData['bet_id'];
                            $mainGame['lose'] = $mainBetData['bet_price'];
                            $mainGame['bet_status'] = $mainBetData['bet_status'];
                            $mainGame['lose_bet_name'] = $mainBetData['bet_name'];
                        } else {
                            $mainGame['draw_bet_id'] = $mainBetData['bet_id'];
                            $mainGame['draw'] = $mainBetData['bet_price'];
                            $mainGame['bet_status'] = $mainBetData['bet_status'];
                            $mainGame['draw_bet_name'] = $mainBetData['bet_name'];
                        }
                    }
                    
                    $fixture_date = date('m-d', strtotime($mainGame['fixture_start_date']));
                    $fixture_time = date('H:i', strtotime($mainGame['fixture_start_date']));
                    $markets_name = $mainGame['markets_name'];
                    $markets_name_origin = $mainGame['markets_name_origin'];
                    $markets_display_name = $mainGame['markets_display_name'];
                    $display_status = $mainGame['display_status'];

                    // 리그이미지가 없다.
                    $leagueImagePath = $imageBasePath.'/league/'.$mainGame['fixture_league_image_path'];
                    if(strpos($mainGame['fixture_league_image_path'], 'flag') !== false){
                        $leagueImagePath = $mainGame['fixture_league_image_path'];
                    }
                    
                    $currentTime = date('Y-m-d H:i:s');
                    //$startTime = date($mainGame['fixture_start_date'], strtotime("-" . 10 . " minutes"));
                    $startTime = date("Y-m-d H:i:s", strtotime($mainGame['fixture_start_date'] . "-" . 10 . " minutes"));
                    $leagueType = $mainGame['fixture_league_id'];
                    $sports_id = $mainGame['fixture_sport_id'];
                    
                    switch($sports_id){
                        case 6046:
                            $leagueLine= '#b8ca4e';
                            break;
                        case 48242:
                            $leagueLine= '#c99645';
                            break;
                        case 154914:
                            $leagueLine= '#45c6cb';
                            break;
                        case 154830:
                            $leagueLine= '#ffffff';
                            break;
                        case 35232:
                            $leagueLine= '#8c6dc1';
                            break;
                        case 687890:
                            $leagueLine= '#ff5a7f';
                            break;
                        case 154919:
                            $leagueLine= '#ff5a7f';
                            break;
                        case 54094:
                            $leagueLine= '#ff5a7f';
                            break;
                        default :
                            $leagueLine= '';
                    }
                    $tempColor = $leagueLine;

                    if(!($leagueType != $beforeLeagueType && $beforeLeagueType != "")) {
                        $leagueLine = "";
                    }
                    
                    if($sports_id != $beforeSports_id) {
                        $leagueLine = $beforeLeagueLine;
                    }
                    
                    ?>


                    <?php if ($leagueLine != "")  {?>
                    <!-- <span style="float:left; width:100%; background:#414141; border:1px solid #585858; border-top:1px solid <?=$leagueLine?>;" ></span> -->
                    <?php } ?>


                    <!-- $beforeLeagueType = $leagueType; -->
                    <?php if ( ($leagueType != $beforeLeagueType) || ($sports_id != $beforeSports_id) )  {   // 리그 타이틀 ?>
                        <div class="sport_title">
                            <div class="sport_league">
                                <img src="<?= $leagueImagePath ?>" width="28" onError="this.src='/images/flag_eng/international.png'">
                                <img src="/assets_w/images/line.png">
                                <img src="<?=$imageBasePath.'/sports/'?>icon_game<?= $mainGame['fixture_sport_id'] ?>.png" width="18">
                                <img src="/assets_w/images/line.png">
                                <?= $mainGame['fixture_league_name'] ?>
                            </div>
                            <div class="sport_title_time">
                                <span class="font04"><?= $mainGame['markets_name_origin'] ?></span>
                                <img src="/assets_w/images/line.png">
                                <?=$fixture_date?> <?=$fixture_time?>
                            </div>
                        </div>
                    <?php } ?>


                        <div class="sport_title_list bet_list1_wrap bettingInfo" id="fixture_row_<?=$mainGame['fixture_id']?>">
                            <ul onClick="openBetData(<?=$mainGame['fixture_id']?>)">
                                        <li class="sport_time bet1">
                                            <!-- <?//=$fixture_date?> (<?//=DateTimeUtil::getWeekDay($mainGame['fixture_start_date'])?>) <?//=$fixture_time?> -->
                                            <?=$fixture_date?> <?=$fixture_time?>
                                        </li>                                        
                                        <!-- 승무패 배당부분 -->
                                        <?php if((1 == $mainGame['bet_status'] && 1 == $display_status) || $currentTime > $startTime){ ?>
                                            <li class="sport_team1 bet_team1 odds_btn" data-bet-status="<?= $mainGame['bet_status']?>" data-index="<?= $key ?>" data-odds-type="win" 
                                                                        data-bet-id="<?= $mainGame['win_bet_id'] ?>" data-bet-price="<?= $mainGame['win'] ?>"
                                                                        data-td-cell="<?= $mainGame['win_bet_id'].'_'.$mainGame['fixture_start_date'] ?>" 
                                                                        data-markets_name="<?= $markets_name?>" data-markets_name_origin="<?= $markets_name_origin?>" 
                                                                        data-markets_display_name="<?= $markets_display_name?>"  data-bet-name="<?= $mainGame['win_bet_name'] ?>">
                                                <span class="team_l"><?=$mainGame['fixture_participants_1_name']?></span>
                                                <span class="team_r server_betPrice">
                                                    <!-- <img src="/assets_w/images/arr1.gif"> -->
                                                    <?= $mainGame['win'] ?>
                                                </span>
                                            </li>
                                        <?php }else{ ?>
                                            <li class="sport_team1 bet_team1">
                                                <span class="team_l"><?=$mainGame['fixture_participants_1_name']?></span>
                                                <span class="team_r"><img src="\images\icon_lock.png" alt="lock" width="13">
                                            </li>
                                        <?php } ?>
                                        
                                        <!-- 무 -->
                                        <?php if((1 == $mainGame['bet_status'] && 1 == $display_status) || $currentTime > $startTime){ ?>
                                            <?php if(isset($mainGame['draw'])){ ?>
                                                <li class="sport_tie bet_vs odds_btn" data-bet-status="<?= $mainGame['bet_status']?>" data-index="<?= $key ?>" data-odds-type="draw" 
                                                                            data-bet-id="<?= $mainGame['draw_bet_id'] ?>" data-bet-price="<?= $mainGame['draw'] ?>"
                                                                            data-td-cell="<?= $mainGame['draw_bet_id'].'_'.$mainGame['fixture_start_date'] ?>" 
                                                                            data-markets_name="<?= $markets_name?>" data-markets_name_origin="<?= $markets_name_origin?>" 
                                                                            data-markets_display_name="<?= $markets_display_name?>" data-bet-name="<?= $mainGame['draw_bet_name'] ?>">
                                                    <span class="bet_font1 server_betPrice"><?=$mainGame['draw']?></span>
                                                </li>
                                            <?php }else{ ?>
                                                <li class="sport_tie bet_vs">
                                                    <span>VS</span>
                                                </li>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <li class="sport_tie bet_vs"><img src="/assets_w/images/icon_lock.png" alt="lock" width="13"></li>
                                        <?php } ?>
                                        
                                        <!-- 패 -->
                                        <?php if((1 == $mainGame['bet_status'] && 1 == $display_status) || $currentTime > $startTime){ ?>
                                            <li class="sport_team2 bet_team2 odds_btn" data-bet-status="<?= $mainGame['bet_status']?>" data-index="<?= $key ?>" data-odds-type="lose" 
                                                                                data-bet-id="<?= $mainGame['lose_bet_id'] ?>" data-bet-price="<?= $mainGame['lose'] ?>"
                                                                                data-td-cell="<?= $mainGame['lose_bet_id'].'_'.$mainGame['fixture_start_date'] ?>" 
                                                                                data-markets_name="<?= $markets_name?>" data-markets_name_origin="<?= $markets_name_origin?>" 
                                                                                data-markets_display_name="<?= $markets_display_name?>" data-bet-name="<?= $mainGame['lose_bet_name'] ?>">
                                                <span class="team_l server_betPrice">
                                                    <?= $mainGame['lose'] ?>
                                                    <!-- <img src="/assets_w/images/arr2.gif"> -->
                                                </span>
                                                <span class="team_r">
                                                    <?=$mainGame['fixture_participants_2_name']?>
                                                </span>
                                            </li>
                                        <?php }else{ ?>
                                            <li class="sport_team2 bet_team2">
                                                <span class="team_l"><img src="/assets_w/images/icon_lock.png" alt="lock" width="13"></span>
                                                <span class="team_r"><?=$mainGame['fixture_participants_2_name']?></span>
                                        </li>
                                        <?php } ?>
                                        <li class="sport_state bet_max bet8">
                                            <span class="">
                                                <?php
                                                    if ($mainGame['leagues_m_bet_money'] > 10000) {
                                                        echo number_format($mainGame['leagues_m_bet_money'] / 10000) . '만';
                                                    } else {
                                                        echo number_format($mainGame['leagues_m_bet_money']);
                                                    }
                                                ?>
                                            </span>
                                        </li>
                                        <li class="sport_more bet7">+ <?= $betCount ?></li>
                                    <!-- </tr>
                                </table> -->
                            </ul>
                            <div class="sports_s_right">
                                <ul class="dropdown3">
                                </ul>
                            </div>
                        </div><!-- .sport_title_list -->

    <!-- 잠김표시 처리 -->
    <!-- 승무패 배당부분 -->
    <!--<td class="bet4"><img src="\images\icon_lock.png" alt="lock" width="13"></td>
    <td class="bet5"><img src="\images\icon_lock.png" alt="lock" width="13"></td>
    <td class="bet6"><img src="\images\icon_lock.png" alt="lock" width="13"></td>-->
    <!-- 잠김표시 처리예시 끝 -->
                    <?php
                        $beforeLeagueType = $leagueType;
                        $beforeLeagueLine = $tempColor;
                        $beforeSports_id = $sports_id;
                        } // $sports_list end for
                    } // $gameList end for
                    
                    ?>
                </div><!-- .dropdown2 -->
                                            
            </div><!-- sports_s_left -->   
                <!-- 더보기 -->
            <!-- <div class="sports_s_right">
                    <ul class="dropdown3">
                    </ul>
            </div> -->
        </div><!-- .sports_s_left_sports_s_right_wrap -->
    </div><!-- sports_wide_center -->
	<!-- 12/8 라이브스포츠 아코디언 js 수정 -->
	<script src="/assets_w/js/tendina.min.js"></script>
	<script>

        $(document).ready(function(){
            // $(document).find('.dropdown3 .bet_list1_wrap_in_title').each(function(){
            //     $(this).click(function(){
            //         $(this).parent('a').next('ul').slideToggle();
            //         return false;
            //     });
            // });
            
            // // market slide
            // $(document).on("click", ".bet_list1_wrap_in_title", function(e){
            //     e.preventDefault();
            //     console.log('dd');
            //     $(this).next('.bet_list1_wrap_in_new').slideToggle();
            //     return false;
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


    
    <div class="sports_wide_right">
        <div class="cart_wrap">

            <div class="btn__cart_close">
                <a href="#"><img src="/assets_w/images/m_close.png" width="50"></a>
            </div>

            <div class="sports_cart_title" id="sports_cart_title">
                BETTING SLIP
                <span class="sports_cart_title_right">
                    <span class="sports_cart_title2">배당변경 자동적용</span>
                    <a href="javascript:void(0);">
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

            <ul class="slip_tab_wrap">
                <!-- 배팅카트 표시 -->
            </ul>
            <div class="sports_cart_wrap">
                <div class="con_box00">
                    <table width="100%"cellspacing="0" cellpadding="0">
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
                            <td class="sports_cart_style1">예상당첨금 <span class="sports_cart_style2 will_win_money">0</span></td>
                        </tr>
                        <!--<tr>
                            <td class="sports_cart_style1">베팅금액 <span class="sports_cart_style2"><input class="input3" style="text-align:right; width:150px;"></span></td>
                        </tr>-->
                    </table>
                </div>
                <div class="con_box00">
                    <table width="100%"cellspacing="4" cellpadding="0">
                        <tr>
                            <td width="10%"><a href="javascript:setBettingMoney(5000, <?= session()->get('money') ?>)"><span class="sports_btn2">5천원</span></a></td>
                            <td width="10%"><a href="javascript:setBettingMoney(10000, <?= session()->get('money') ?>)"><span class="sports_btn2">1만원</span></a></td>
                            <td width="10%"><a href="javascript:setBettingMoney(50000, <?= session()->get('money') ?>)"><span class="sports_btn2">5만원</span></a></td>
                        </tr>
                        <tr>
                            <td width="10%"><a href="javascript:setBettingMoney(100000, <?= session()->get('money') ?>)"><span class="sports_btn2">10만원</span></a></td>
                            <td width="10%"><a href="javascript:setBettingMoney(300000, <?= session()->get('money') ?>)"><span class="sports_btn2">30만원</span></a></td>
                            <td width="10%"><a href="javascript:setBettingMoney(500000, <?= session()->get('money') ?>)"><span class="sports_btn2">50만원</span></a></td>
                        </tr> 
                        <tr>
                            <td width="10%"><a href="javascript:setBettingMoney(1000000, <?= session()->get('money') ?>)"><span class="sports_btn2">100만원</span></a></td>
                            <td width="10%"><a href="javascript:void(0);"><span class="sports_btn2 max_btn">MAX</span></a></td>
                            <td width="10%"><a href="javascript:void(0);"><span class="sports_btn2 reset_btn">전체지우기</span></a></td>
                        </tr> 
                        <!-- <tr>
                            <td width="100%" colspan="3"><a href="javascript:void(0);" class="gm_pop1_open"><span class="sports_btn3">G머니 사용</span></a></td>
                        </tr> -->

                        <!-- G-money 효과 적용시 -->
                        <!-- <tr id="selectItemList" style="display: none">
                            <td width="100%" colspan="3"> <span class="sports_cart_bet2" for="g_money_eff">★ <span id="clickItemName">20% 환급패치</span> ★ <a onclick="fnInitItem()"><span class="sports_cart_bet_img"><img src='/assets_w/images/cart_close.png'></span></span></a></td>
                            <input type="hidden" id="itemId" value="">
                            <input type="hidden" id="itemValue" value="">
                        </tr> -->
                        <!-- G-money 효과 적용시 -->
                                        
                        <tr>
                            <td width="100%" colspan="3" style="padding-top:5px;"><a href="javascript:void(0);"><span class="sports_btn1">베팅하기</span></a></td>
                        </tr>                                      
                    </table>
                </div>
            </div>
        </div><!-- cart_wrap -->
        <div id="domain_pc">
            <a target="_blank" href="https://xn--tl3bs23a.com/"><img src="/images/bets_banner_pc.jpg"></a>
        </div>
        <div class="cart_bg"></div>
    </div><!-- sports_wide_right -->
</div><!-- mini_wide_wrap -->

<div class="btn__cart-open cart_open">
    <a href="#fade_1"><img src="/assets_w/images/m_cart.png" width="50"></a>
    <span class="cart_count2">0</span>
</div>
<!-- <span class="fade_1_open cart_open" data-popup-ordinal="0"><a href="#fade_1"><img src="/assets_w/images/m_cart.png" width="50"></a></span> -->


<script>
    $(function(){
        $(document).on("click", ".btn__cart-open", function(e){
            e.preventDefault();
            $(document).find(".sports_wide_right").toggleClass("active");
        });
        $(document).on("click", ".btn__cart_close, .cart_bg", function(e){
            e.preventDefault();
            $(document).find(".sports_wide_right").removeClass("active");
        });
    });
</script>


<?php if(count($gameList) > 0) include('common/page_num.php'); ?>


<script>
$(document).ready(function(){
	// <!-- 카트슝슝 -->
    
	// <!-- 왼쪽 베팅 -->
	// $('.sport_team1,.sport_team2,.sport_tie').on('click',function(){
	// 	var button = $(this);
	// 	var cartBet = $('.cart_bet');
	// 	var scartBet = $('.sports_cart_bet');
	// 	button.append('<span class="cart-item"></span>');
	// 	button.addClass('sendtocart');
	// 	cartBet.addClass('shake');
	// 	scartBet.css('background','var(--bgcolor2)');
	// 	setTimeout(function(){
	// 		button.removeClass('sendtocart');		  
	// 		cartBet.removeClass('shake');
	// 		scartBet.css('background','var(--bgcolor3)');
	// 		setTimeout(function(){
	// 				button.find('.cart-item').remove();
	// 		},300);
	// 	},800);
	// });
	// <!-- 오른쪽 베팅 -->
	// $('.bet_list_td').on('click',function(){
	// 	var button = $(this);
	// 	var cartBet = $('.cart_bet');
	// 	var scartBet = $('.sports_cart_bet');
	// 	button.append('<span class="cart-item"></span>');
	// 	button.addClass('sendtocart');
	// 	cartBet.addClass('shake');
	// 	scartBet.css('background','var(--bgcolor2)');
	// 	setTimeout(function(){
	// 		button.removeClass('sendtocart');		  
	// 		cartBet.removeClass('shake');
	// 		scartBet.css('background','var(--bgcolor3)');
	// 		setTimeout(function(){
	// 				button.find('.cart-item').remove();
	// 		},300);
	// 	},800);
	// });
});
</script>


<?= view('/web/common/footer_wrap') ?>



<!-- </div> -->
<!-- wrap -->
<!-- top버튼 -->
<!-- <a href="#myAnchor" class="go-top">▲</a> -->
<script type="text/javascript">
let isMobile = mobileCheck(); // 1-모바일
let folderType = 'D'; // 'S': 싱글, 'D': 다폴더
let totalOdds = 0;
let service_bonus_folder = '<?= $arr_bonus['service_bonus_folder']; ?>';
let odds_3_folder_bonus = <?= $arr_bonus['odds_3_folder_bonus']; ?>;
let odds_4_folder_bonus = <?= $arr_bonus['odds_4_folder_bonus']; ?>;
let odds_5_folder_bonus = <?= $arr_bonus['odds_5_folder_bonus']; ?>;
let odds_6_folder_bonus = <?= $arr_bonus['odds_6_folder_bonus']; ?>;
let odds_7_folder_bonus = <?= $arr_bonus['odds_7_folder_bonus']; ?>;
let limit_folder_bonus = <?= $arr_bonus['limit_folder_bonus']; ?>;
let isAlreadyBetting = false;
let betList = <?php echo json_encode($gameList) ?>;
let selectFixtureId = 0;    // 현재 선택한 경기
let is_betting_slip = '<?=$is_betting_slip?>';
let maxBetMoney = <?= $maxBetMoney ?>;
let limitBetMoney = <?= $limitBetMoney ?>;
let nowMoney = <?= !empty(session()->get('money')) ? session()->get('money') : 0 ?>;
let isClassic = 'OFF'; // 1-클래식
let prevBetMoney = 0;
const serverName = '<?= $serverName; ?>';

$(document).ready(function(){
	/* const urlParams = new URLSearchParams(location.search);
	$('#sports_id').val(urlParams.get('sports_id')); */
	
    $('.dropdown').tendina({
        // This is a setup made only
        // to show which options you can use,
        // it doesn't actually make sense!
        animate: true,
        speed: 300,
        onHover: false,
        hoverDelay: 300,
        //activeMenu: $('#deepest'),
        openCallback: function(clickedEl) {
          //console.log('Hey dude!');
        },
        closeCallback: function(clickedEl) {
          //console.log('Bye dude!');
        }
  })
                
    // $("li").click(function(){
    //     //console.log('li click');
    //     $(this).addClass("on");
    //     $(this).siblings().removeClass("on");
    // });

    $('#sports_count_0').text(<?=$totalFixtureCnt?>);
    // 첫경기 베팅판 가져오기
    sportsFixtureDisplay(<?=$firstFixtureid?>, '<?=$firstFixtureTime?>', '<?=$firstFixtureTeam1?>', '<?=$firstFixtureTeam2?>');

    // 상단 스포츠 항목 선택표시
    choiceSportsImage(<?=$sportsId?>);

    fnSortBetting();
    // 세션에 저장된 베팅슬립이 있으면 출력해준다.total_odds
    displayBetSlip();
    betMaxCheck();
    changeWillWinMoney();
    
    $(document).on("click",".notify-close-btn , .odds_btn",function(e){
        e.preventDefault();
        setTimeout(() => {
            let backOdds = $('.total_odds').text();
            console.log(backOdds)
            if(backOdds == 0 || isNaN(backOdds)){
                $('.total_odds').html(0);
            }
        },50)
    })
    // 좌측 펼쳐짐 처리
    $('#left_menu_'+<?=$sportsId?>).trigger('click');

    //좌측 상단 리그 및 팀명 검색어 셋팅
    $('#league_name').val('<?=$league_name?>');
    
    $(document).on('click', '.notify-close-btn', function () {
        console.log('notify-close-btn');
        notifyCloseBtn(this);
        cartCount();
    });

    $(document).on('click', '.max_btn', function () {
        maxBtnClick();
    });

    $(document).on('click', '.reset_btn', function () {
        $('#betting_slip_money').val(0);
        changeWillWinMoney();
    });

    // 배팅하기
    $(document).on('click', '.sports_btn1', function () {
        bettingClick();
    });

    $(document).on('change', '#betting_slip_money', function () {
        changeWillWinMoney();
    });

    $(document).on('focus', '#betting_slip_money', function () {
        bettingSlipMoneyFocus();
    });

    $(document).on('blur', '#betting_slip_money', function () {
       bettingSlipMoneyBlur();
    });

    // 전체삭제
    $(document).on('click', '.waste_btn', function () {
        wasteBtn('sports_cart_bet', 1);
    });

    // 베팅 선택
    $(document).on('click', '.odds_btn', function () {

        console.log('odds_btn click!!');

        /*const dataBetStatus = $(this).data('bet-status');

         if(dataBetStatus && dataBetStatus != '1'){
         alert('배팅이 닫혀있습니다.');
         return false;
         }

         let betMoney = $('#betting_slip_money').val();
         betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
         if (0 < betMoney) {
         $('#betting_slip_money').val(0);
         }*/

        let $this = $(this);
        let betListIndex = $(this).data('index');
        let betOddsTypes = $(this).data('oddsType');
        let betId = $(this).data('bet-id');
        let betPrice = $(this).data('bet-price');

        const indexArr = betListIndex.split('_');
        const fixtureKey = indexArr[0];
        const marketsKey = indexArr[1];
        const baseLineKey = indexArr[2];
        const providersKey = indexArr[3];
        const startDateKey = indexArr[4];

        const gameObj = fnMakeGameObj(betListIndex);
        //console.log(gameObj);

        let betOddsTypesDisplay = betOddsTypes == 'win' ? '승' : betOddsTypes == 'draw' ? '무' : betOddsTypes == 'lose' ? '패' : betOddsTypes;
        let betMarketType = "";
        let betMarketId = marketsKey;//gameObj['markets_id'];
        let betBaseLine = baseLineKey;//gameObj['bet_base_line'];
        let fixture_start_date = gameObj['fixture_start_date'];
        let leagues_m_bet_money = gameObj['leagues_m_bet_money'];
        let betSportId = gameObj['fixture_sport_id'];
        let markets_name_origin = $(this).data('markets_name_origin');
        const betName = $(this).data('bet-name');
        const today = new Date();
        const timeValue = new Date(fixture_start_date);
        const betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);

        // 10분이면 배당상태 2인것도 통과
        /*const dataBetStatus = $(this).data('bet-status');
        if (dataBetStatus && dataBetStatus != '1') {
            if (600 < betweenTime) {
                alert('배팅이 닫혀있습니다.');
                return false;
            }
        }*/

        let betMoney = $('#betting_slip_money').val();
        betMoney = Number(betMoney.replace(/,/gi, "")); //변경작업
        if (0 < betMoney) {
            // $('#betting_slip_money').val(0);
        }

        let betListIndexStr = betListIndex + '';
        let fixtureId = betListIndexStr.split('_')[0];
        let alreadyCombinObj;
        let alreadyCombin = false;
        let checkBet = true;

        // 선택된 베팅이면 체크
        if ('OFF' == is_betting_slip) {
            console.log('1');
            if (!IsBetSlip(betId)) {
                let memberBetList = [];
                memberBetList.push({
                    'betId': betId,
                    'betSportId': betSportId
                });

                // 정상베팅인지 체크
                $.ajax({
                    url: '/api/bet/checkBet',
                    type: 'post',
                    async: false,
                    data: {
                        'betType': 1,
                        'betList': memberBetList
                                //'betId': betId,
                                //'betSportId': betSportId
                    },
                }).done(function (response) {
                    //let server_betId = response['data']['betId'];
                    let server_betPrice = response['data'][betId]['betPrice'];
                    let retval = response['data'][betId]['retval'];
                    //console.log('betPrice : '+betPrice+' betPrice : '+server_betPrice);
                    if(1 == retval){
                        if (betPrice != server_betPrice) {
                            let check = confirm('베당이 ' + server_betPrice + '으로 변경되었습니다. 변경된 베당으로 베팅하시겠습니까?');
                            if (check === true) {
                                console.log('ddd');
                                $(this).data('bet-price', server_betPrice);
                                $this.find(".server_betPrice").text(server_betPrice);
                                betPrice = server_betPrice;
                                return;
                            } else {
                                $(this).data('bet-price', server_betPrice);
                                $this.find(".server_betPrice").text(server_betPrice);
                                checkBet = false;
                                return;
                            }
                        }
                    }else if(-2 == retval){
                        alert('해당 유형의 배팅은 현재 불가합니다.');
                        checkBet = false;
                    }else{
                        alert('해당 유형의 배팅은 현재 불가합니다.');
                        checkBet = false;
                    }
                }).fail(function (error) {
                    alert(error.responseJSON['messages']['messages']);
                    checkBet = false;
                    // location.reload();
                }).always(function (response) {
                });
            }

            if (!checkBet) {
                return;
            }
        }

        // 동일베팅 항목을 선택(선택해제)
        if ($(this).hasClass('bet_on')) {
            console.log('동일베팅 항목을 선택(선택해제)');
            let price = betPrice;
            price = price == 0 ? 1 : price;
            totalOdds = totalOdds / price;
            totalOdds = totalOdds == 1 ? 0 : totalOdds;
            $(this).removeClass('bet_on');
            $('.odds_btn[data-bet-id="' + $(this).data('bet-id') + '"]').removeClass('bet_on');
            $('[data-bet-id*="' + betId + '"]' + '.sports_cart_bet').remove();
            $('.total_odds').html(totalOdds.toFixed(2));

            delBetSlip(betId);
            changeWillWinMoney();

            // 다폴더이고 베팅제외를 했을때, 3개 밑으로 갈시 보너스 배당률 0
            setBonusPrice(totalOdds, getBetSlipCount());

            cartCount();

            return;
        }

        // 동일경기 선택
        //if ($('[data-index*="' + fixtureId + '"]').hasClass('bet_on')) {
        if (IsBetFixture(fixtureId)) {
            console.log('동일경기 선택');
            let isCombin = false;
            //const $selecteSports = $(this).closest('.soprts_in_acc').find('.bet_on');
            const $selecteSports = $(this).closest('.bet_list1_wrap > ul').find('.bet_on');
            // console.log( $(this).closest('.bet_list1_wrap') );
            
            //console.log('selectSports length : '+$selecteSports.length);
            if ($selecteSports.length > 1) {
                const $this = $(this);
                let flag1 = false;
                let flag2 = false;
                let targetObj;

                const validList = [];

                $selecteSports.each(function () {
                    //const targetId = $(this).closest('.sports_in').attr('id');
                    
                    // const targetId = $(this).closest('.bet_list1_wrap_in_new').attr('id');
                    const targetId = $(this).parent('.bet_list1_wrap_in_new').attr('id');
                    // console.log('targetId',  targetId, $(this).parent('.bet_list1_wrap') );

                    //console.log('targetId :  '+ targetId);

                    // kwin 데이터 : marketsData_8566708_226_0
                    // gamble 데이터 : market_226
                    //const targetData = targetId.split('_')[1] + targetId.split('_')[2]
                    const targetData = fixtureKey + targetId.split('_')[1]
                    //console.log('targetData : '+targetData);
                    if (validList.includes(targetData)) {
                        return true;
                    }

                    validList.push(targetData);
                    if ($this.closest('.bet_list1_wrap_in_new').attr('id') == targetId) {
                        flag1 = true;
                    } else {
                        targetObj = $(this);
                        flag2 = true;
                    }
                });

                if (flag1 && flag2) {
                    alreadyCombin = true;
                    alreadyCombinObj = targetObj;
                }
            }

            // 농구, 야구이면 조합베팅인지 체크
            if (betSportId == 154914) {

                if (28 == betMarketId || 342 == betMarketId || 226 == betMarketId) {
                    isCombin = fnCheckOverlapCombine_renew($(this), betList, betMarketId, fixtureId);
                } else if (betMarketId == 281 || betMarketId == 236) {
                    if (betMarketId == 281) {
                        if (fnCheckCombine_renew($(this), betList, 281, 236, fixtureId)) {
                            isCombin = true;
                        }

                    } else if (betMarketId == 236) {
                        if (fnCheckCombine_renew($(this), betList, 236, 281, fixtureId)) {
                            isCombin = true;
                        }
                    }
                }

            } else if (betSportId == 48242) {

                if (betMarketId == 28) {

                    const checkFlag1 = fnCheckCombine_renew($(this), betList, 28, 226, fixtureId)
                    const checkFlag2 = fnCheckCombine_renew($(this), betList, 28, 342, fixtureId)
                    if ((checkFlag1 && !checkFlag2) || (!checkFlag1 && checkFlag2)) {
                        isCombin = true;
                    }

                } else if (betMarketId == 226) {
                    if (fnCheckCombine_renew($(this), betList, 226, 28, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 342) {
                    if (fnCheckCombine_renew($(this), betList, 342, 28, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 64) {
                    if (fnCheckCombine_renew($(this), betList, 64, 21, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 21) {
                    if (fnCheckCombine_renew($(this), betList, 21, 64, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 53) {
                    if (fnCheckCombine_renew($(this), betList, 53, 77, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 77) {
                    if (fnCheckCombine_renew($(this), betList, 77, 53, fixtureId))
                        isCombin = true;

                }
            } else if (betSportId == 35232) {
                if (betMarketId == 2) {

                    const checkFlag1 = fnCheckCombine_renew($(this), betList, 2, 1, fixtureId)
                    const checkFlag2 = fnCheckCombine_renew($(this), betList, 2, 3, fixtureId)
                    if ((checkFlag1 && !checkFlag2) || (!checkFlag1 && checkFlag2)) {
                        isCombin = true;
                    }

                } else if (betMarketId == 1) {
                    if (fnCheckCombine_renew($(this), betList, 1, 2, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 3) {
                    if (fnCheckCombine_renew($(this), betList, 3, 2, fixtureId))
                        isCombin = true;

                }
            }

            //console.log('isCombin : ' + isCombin);
            if (!isCombin) {
                let price = 1;
                //$('[data-index*="' + fixtureId + '"].sports_cart_bet .slip_bet_cell_r').each(function () {
                $('[data-index*="' + fixtureId + '"].sports_cart_bet .sports_cart_bet_p').each(function () {
                    price = price * Number($(this).text());
                    delBetSlipFixtureId(fixtureId);
                });
                /* 모바일
                 price = price == 0 ? 1 : price;

                 delBetSlipFixtureId(fixtureId);*/

                totalOdds = totalOdds / price;
                $('[data-index*="' + fixtureId + '"]').removeClass('bet_on');
                $('[data-index*="' + fixtureId + '"]' + '.sports_cart_bet').remove();
            }

            
        }

        if (folderType == 'S') {
            $('.sports_cart_bet').remove();
            $('.bet_on').removeClass('bet_on');
            if (gameObj[betOddsTypes] != null && gameObj[betOddsTypes] != '') {
                totalOdds = parseFloat(gameObj[betOddsTypes]).toFixed(2);
            }
            totalOdds = betPrice;
        } else {
            totalOdds = totalOdds == 0 ? 1 : totalOdds;
            totalOdds = totalOdds * parseFloat(betPrice);
            totalOdds = totalOdds.toFixed(2);
        }
        
        betMarketType = markets_name_origin;
        if (+totalOdds > 100) {
            alert(`최대 배당률을 초과하였습니다. [최대: 100배]`);
            totalOdds = $('.total_odds').html();
            return false;
        }
        
        $('.total_odds').html(totalOdds);

        $(`[data-index="${betListIndex}"][data-odds-type="${betOddsTypes}"]`).addClass('bet_on');

        let html = `
            <li class='sports_cart_bet' data-index="${betListIndex}" data-odds-types="${betOddsTypes}" data-bet-id="${betId}" data-bet-name="${betName}" data-bet-price="${betPrice}" data-markets-name="${betMarketType}" data-bet-base-line="${betBaseLine}" data-fixture-start-date="${fixture_start_date}" data-leagues_m_bet_money="${leagues_m_bet_money}">
                <div width='100%'class='cart_bet'>
                
                    <div>
                        <span class='sports_cart_bet_font1'>
                            ${gameObj['fixture_participants_1_name']}
                            <img src='/assets_w/images/vs.png' width='25'>
                            ${gameObj['fixture_participants_2_name']}
                        </span>
                    </div>
                    <div>
                        <span class='sports_cart_bet_font2'>${betMarketType}</span>
                    </div>
                    <div class="sports_cart_bet_font3">
                            ${gameObj['fixture_participants_1_name']}
                            <span>${betOddsTypesDisplay}</span>
                            <span class="sports_cart_bet_p">${betPrice}</span>
                    </div>
                    
                    <a href="#" class="sports_cart_bet_img">
                        <img src="/assets_w/images/cart_close.png" class='notify-close-btn' data-index="${betListIndex}" data-bet-id="${betId}">
                    </a>
                </div>
            </li>
        `;
        $('.slip_tab_wrap').prepend($(html));

        //bet_name
        let team1 = gameObj['fixture_participants_1_name'];
        let team2 = gameObj['fixture_participants_2_name'];
        addBetSlip(betId, betPrice, betListIndex, betOddsTypes, betOddsTypesDisplay, betMarketType, team1, team2, betBaseLine, fixtureId, fixture_start_date, leagues_m_bet_money, betMarketId, betName);

        if (folderType == 'D') {
            if (service_bonus_folder == 'Y') {
                setBonusPrice(totalOdds, getBetSlipCount());
            }
        } else {
            $('.bonus_total_odds').text(0);
        }

        // 뱃팅슬롯 카운트
        /*if (isMobile) {
            $('.cart_count2').text(fnGetCartCount());
        }*/

        changeWillWinMoney();
        betMaxCheck();

        if (alreadyCombin) {
            alreadyCombinObj.trigger('click');
        }
        
        cartCount();
    }); // end .odds_btn click
    
    cartCount();
    scrollFixed();
    /*$(document).on("click", ".odds_btn", function(){
        cartCount();
    });*/


}); // end ready



    



    $(window).on("scroll", function(e){
        scrollFixed();
    })

    $(window).on("resize", function(e){
        scrollFixed();
    })



    const cartCount = function(){
        //console.log('cartCount');
        //const cart_count = $(document).find(".sports_wide_right .sports_cart_bet").length;
        const cart_count = getBetSlipCount();
        $(document).find(".cart_open .cart_count2").html(cart_count);

        // console.log(cart_count);

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


    const contentHeight = function(){
        console.log('contentHeight');
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
</body>
</html>