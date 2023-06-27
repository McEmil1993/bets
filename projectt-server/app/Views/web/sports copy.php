<?php
use App\Util\DateTimeUtil;
use App\Util\StatusUtil;
$imageBasePath = config(App::class)->imageUrl.'/'.config(App::class)->imagePath;
$p_data['num_per_page'] = SPORRS_BLOCK_COUNT;
$p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
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
$leftSports[6046] = Array('id'=>6046, 'name'=>'축구', 'count'=>0);
$leftSports[48242] = Array('id'=>48242, 'name'=>'농구', 'count'=>0);
$leftSports[154914] = Array('id'=>154914, 'name'=>'야구', 'count'=>0);
$leftSports[154830] = Array('id'=>154830, 'name'=>'배구', 'count'=>0);
$leftSports[35232] = Array('id'=>35232, 'name'=>'아이스하키', 'count'=>0);
$leftSports[687890] = Array('id'=>687890, 'name'=>'이스포츠', 'count'=>0);
$leftSports[154919] = Array('id'=>154919, 'name'=>'UFC', 'count'=>0);
/*foreach ($leftSports as $key => $value) {
    if(!array_key_exists($value, $sports)){
        $sports = 
    }
}
print_r($sports);
return;*/
?>
<script src="/assets_w/js/sports_common_w.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets/js/sports_common.js?v=<?php echo date("YmdHis"); ?>"></script>
<script src="/assets/js/sports_realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script>
<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>
<!-- <input type="hidden" id="sports_id"> -->
<div id="sports_wide_wrap">
    <div class="sports_wide_left">
        <div class="search" style="float: left; width: 250px;">
            <ul>
                <li style="float:left;"><img src="/images/icon_search.png" alt="" style="margin-left:5px">
                    <input name="league_name" id="league_name" type="text" class="input_search" placeholder="국가 및 팀명"></li>
                <li style="float:right; margin-top: 2px;"><a onclick="searchLeague()"><span class="search_btn">검색</span></a></li>
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
                        <div class="left_list1">
                            <span class="menu_left">
                                <img src="<?=$imageBasePath.'/sports/'?>/icon_game<?=$sport['id']?>.png" width="18">&nbsp;&nbsp;&nbsp;<?=$sport['name'].''?>
                            </span>
                            <span class="m enu_right">
                                <span class="menu_right_box" id="sports_count_<?=$sport['id']?>"><?= isset($sports[$sport['id']]['count']) ? $sports[$sport['id']]['count'] : 0 ?></span>
                            </span>
                        </div>
                    </a>
                    <?php if($sports[$sport['id']]['count'] > 0){ 
                        $totalFixtureCnt += $sports[$sport['id']]['count'];
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
                        </div>
                <div class="sports_list_title2">
                <select class="sports_input2" onchange="league_select(this.value)">
                    <option value="0">전체리그</option>
                    <?php foreach ($leagues as $key => $lo){ ?>
                    <option value="<?= $lo['id'] ?>" <?=($lo['id']==$leagueId)?'selected':''?>><?= $lo['display_name'] ?></option>
                    <?php } ?>
                </select>
            </div>      
            <div class="sports_list_title3">
            	<ul>
                    <li><a href="javascript:sports_select(0)"><span id="sports_img_0" class=""><img src="/assets_w/images/icon01on.png" width="18">&nbsp; 전체</span></a></li>
                    <li><a href="javascript:sports_select(6046)"><span id="sports_img_6046" class=""><img src="/assets_w/images/icon02on.png" width="18">&nbsp; 축구</span></a></li>
                    <li><a href="javascript:sports_select(48242)"><span id="sports_img_48242" class=""><img src="/assets_w/images/icon03on.png" width="18">&nbsp; 농구</span></a></li>                    
                    <li><a href="javascript:sports_select(154914)"><span id="sports_img_154914" class=""><img src="/assets_w/images/icon04on.png" width="18">&nbsp; 야구</span></a></li>
                    <li><a href="javascript:sports_select(154830)"><span id="sports_img_154830" class=""><img src="/assets_w/images/icon05on.png" width="18">&nbsp; 배구</span></a></li>
                    <li><a href="javascript:sports_select(35232)"><span id="sports_img_35232" class=""><img src="/assets_w/images/icon06on.png" width="18">&nbsp; 아이스하키</span></a></li>
                    <li><a href="javascript:sports_select(687890)"><span id="sports_img_687890" class=""><img src="/assets_w/images/icon07on.png" width="18">&nbsp; e스포츠</span></a></li> 
                    <li><a href="javascript:sports_select(154919)"><span id="sports_img_154919" class=""><img src="/assets_w/images/icon08on.png" width="18">&nbsp; UFC</span></a></li> 
                </ul>
            </div>
            <div class="sports_list_title4"></div>
        </div>   
		<div class="sports_s_left">  
			<div class="bet_title_wrap">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                    <!-- <tr>
                        <td class="bet_title1">경기시간</td>
                        <td class="bet_title2">국가</td>
                        <td class="bet_title2_1">종목</td>
                        <td class="bet_title3">팀</td>
                        <td class="bet_title4">승</td>
                        <td class="bet_title5">무</td>
                        <td class="bet_title6">패</td>
                        <td class="bet_title8">맥스</td>                        
                        <td class="bet_title7">더보기</td>
                    </tr> -->
                    </thead>
                </table>
            </div>
            
            <ul class="dropdown2">
            <?php
            $firstFixtureid = 0;
            $firstFixtureTime = '';
            $firstFixtureTeam1 = '';
            $firstFixtureTeam2 = '';
            $beforeLeagueType = '';
            $leagueType = '';
            $leagueLine = '';
            $beforeLeagueLine= '';
            $beforeSports_id;
            
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
                    $firstFixtureTeam1 = $mainGame['fixture_participants_1_name'];
                    $firstFixtureTeam2 = $mainGame['fixture_participants_2_name'];
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
                    } else if (0 == strcmp($mainBetData['bet_name'], '2')) {
                        $mainGame['lose_bet_id'] = $mainBetData['bet_id'];
                        $mainGame['lose'] = $mainBetData['bet_price'];
                        $mainGame['bet_status'] = $mainBetData['bet_status'];
                    } else {
                        $mainGame['draw_bet_id'] = $mainBetData['bet_id'];
                        $mainGame['draw'] = $mainBetData['bet_price'];
                       $mainGame['bet_status'] = $mainBetData['bet_status'];
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
                <li class="bet_list1_wrap bettingInfo" id="fixture_row_<?=$mainGame['fixture_id']?>">
                    <a href="javascript:void(0);">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="bet1" onClick="openBetData(<?=$mainGame['fixture_id']?>)"><?=$fixture_date?> (<?=DateTimeUtil::getWeekDay($mainGame['fixture_start_date'])?>) <?=$fixture_time?></td>
                                <td class="bet2" onClick="openBetData(<?=$mainGame['fixture_id']?>)"><img src="<?= $leagueImagePath ?>" width="28"></td>
                                <td class="bet2_1" onClick="openBetData(<?=$mainGame['fixture_id']?>)"><img src="<?=$imageBasePath.'/sports/'?>icon_game<?= $mainGame['fixture_sport_id'] ?>.png" width="18"></td>
                                
                                <!-- 승무패 배당부분 -->
                                <?php if((1 == $mainGame['bet_status'] && 1 == $display_status) || $currentTime > $startTime){ ?>
                                    <td class="bet_team1 odds_btn" data-bet-status="<?= $mainGame['bet_status']?>" data-index="<?= $key ?>" data-odds-type="win" 
                                                                data-bet-id="<?= $mainGame['win_bet_id'] ?>" data-bet-price="<?= $mainGame['win'] ?>"
                                                                data-td-cell="<?= $mainGame['win_bet_id'].'_'.$mainGame['fixture_start_date'] ?>" 
                                                                data-markets_name="<?= $markets_name?>" data-markets_name_origin="<?= $markets_name_origin?>" 
                                                                data-markets_display_name="<?= $markets_display_name?>">
                                        <span class="team_l"><?=$mainGame['fixture_participants_1_name']?></span>
                                        <span class="team_r"><?= $mainGame['win'] ?></span></td>
                                <?php }else{ ?>
                                    <td class="bet_team1">
                                        <span class="team_l"><?=$mainGame['fixture_participants_1_name']?></span>
                                        <span class="team_r"><img src="\images\icon_lock.png" alt="lock" width="13"></td>
                                <?php } ?>
                                
                                <!-- 무 -->
                                <?php if((1 == $mainGame['bet_status'] && 1 == $display_status) || $currentTime > $startTime){ ?>
                                    <?php if(isset($mainGame['draw'])){ ?>
                                        <td class="bet_vs odds_btn" data-bet-status="<?= $mainGame['bet_status']?>" data-index="<?= $key ?>" data-odds-type="draw" 
                                                                    data-bet-id="<?= $mainGame['draw_bet_id'] ?>" data-bet-price="<?= $mainGame['draw'] ?>"
                                                                    data-td-cell="<?= $mainGame['draw_bet_id'].'_'.$mainGame['fixture_start_date'] ?>" 
                                                                    data-markets_name="<?= $markets_name?>" data-markets_name_origin="<?= $markets_name_origin?>" 
                                                                    data-markets_display_name="<?= $markets_display_name?>">
                                        <span class="bet_font1"><?=$mainGame['draw']?></span></td>
                                    <?php }else{ ?>
                                        <td class="bet_vs"><span>VS</span></td>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <td class="bet_vs"><img src="\images\icon_lock.png" alt="lock" width="13"></td>
                                <?php } ?>
                                
                                <!-- 패 -->
                                <?php if((1 == $mainGame['bet_status'] && 1 == $display_status) || $currentTime > $startTime){ ?>
                                    <td class="bet_team2 odds_btn" data-bet-status="<?= $mainGame['bet_status']?>" data-index="<?= $key ?>" data-odds-type="lose" 
                                                                        data-bet-id="<?= $mainGame['lose_bet_id'] ?>" data-bet-price="<?= $mainGame['lose'] ?>"
                                                                        data-td-cell="<?= $mainGame['lose_bet_id'].'_'.$mainGame['fixture_start_date'] ?>" 
                                                                        data-markets_name="<?= $markets_name?>" data-markets_name_origin="<?= $markets_name_origin?>" 
                                                                        data-markets_display_name="<?= $markets_display_name?>">
                                        <span class="team_l"><?= $mainGame['lose'] ?></span>
                                        <span class="team_r"><?=$mainGame['fixture_participants_2_name']?></span></td>
                                <?php }else{ ?>
                                    <td class="bet_team2">
                                        <span class="team_l"><img src="\images\icon_lock.png" alt="lock" width="13"></span>
                                        <span class="team_r"><?=$mainGame['fixture_participants_2_name']?></span></td>
                                <?php } ?>
                                <td class="bet8" onClick="openBetData(<?=$mainGame['fixture_id']?>)">
                                    <span class="bet_font5">
                                        <?php
                                            if ($mainGame['leagues_m_bet_money'] > 10000) {
                                                echo number_format($mainGame['leagues_m_bet_money'] / 10000) . '만';
                                            } else {
                                                echo number_format($mainGame['leagues_m_bet_money']);
                                            }
                                        ?>
                                    </span></td>
                                <td class="bet7" onClick="openBetData(<?=$mainGame['fixture_id']?>)">+ <?= $betCount ?></td>
                            </tr>
                        </table> 
                    </a>
                </li>

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
            </ul>
                                           
        </div><!-- sports_s_left -->   
            <!-- 더보기 -->
            <div class="sports_s_right">  
            <ul class="dropdown3">         
            </ul>
        </div><!-- sports_s_right -->
    </div><!-- sports_wide_center -->
    
    <div class="sports_wide_right">
    	<div class="sports_cart_title">Betting Slip 
            <span class="sports_cart_title2">배당변경 자동적용</span>
            <span class="sports_cart_title_right">
                <a href="javascript:void(0);">                
                    <img src="<?=$is_betting_slip=='ON'?'/assets_w/images/cart_fix1.png':'/assets_w/images/cart_fix2.png'?>" onClick="setBettingSlip(this, '<?=$is_betting_slip?>')" style="margin-top: 7px;">
                </a>
            </span>
        </div>
        <div class="font05" style="width: 100%; display:inline-block; text-align: center; padding: 5px 0; background:#2c2c2c;">보너스폴더는 자동적용됩니다.<br>(3폴더 1.04 , 4폴더 1.06 , 5폴더 이상 1.08)</div>
        <ul class="slip_tab_wrap">
            <!-- 배팅카트 표시 -->
        </ul>
        <div class="sports_cart_wrap">
            <div class="con_box00">
                <table width="100%"cellspacing="0" cellpadding="0">
                    <!--<tr>
                    	<td class="sports_cart_style1">보유머니<span class="sports_cart_style2">0</span></td>
                    </tr>-->
                    <tr>
                    	<td class="sports_cart_style1">최대베팅금<span class="sports_cart_style2 max_bet_money"><?= number_format($maxBetMoney) ?></span></td>
                    </tr>
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
                    <tr>
                        <td width="100%" colspan="3"><a href="javascript:void(0);" class="gm_pop1_open"><span class="sports_btn3">G머니 사용</span></a></td>
                    </tr>

                    <!-- G-money 효과 적용시 -->
                    <tr id="selectItemList" style="display: none">
                        <td width="100%" colspan="3"> <span class="sports_cart_bet2" for="g_money_eff">★ <span id="clickItemName">20% 환급패치</span> ★ <a onclick="fnInitItem()"><span class="sports_cart_bet_img"><img src='/assets_w/images/cart_close.png'></span></span></a></td>
                    	<input type="hidden" id="itemId" value="">
                    	<input type="hidden" id="itemValue" value="">
                    </tr>
                    <!-- G-money 효과 적용시 -->
                                    
                    <tr>
                        <td width="100%" colspan="3" style="padding-top:5px;"><a href="javascript:void(0);"><span class="sports_btn1">베팅하기</span></a></td>
                    </tr>                                      
                </table>
            </div>
        </div>
        <div class="con_box00"><a target="_blank" href="http://겜블.com"><img src="/assets_w/images/domain.png"></a></div>  
    </div><!-- sports_wide_right -->
</div><!-- mini_wide_wrap -->

<!-- G머니 팝업 -->
<div id="gm_pop1" class="gm_popup_none" style="display: none">
	<div class="gm_popup_wrap cf">   
		<div class="gm_close_box"><a href="javascript:void(0);" class="gm_pop1_close"><img src="/assets_w/images/left_close.png"></a></div>
		<div class="gm_popup_box">
            <div class="gm_popup">
                <div class="gm_title1">보유중인 아이템</div>
				<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" class="gm_table">
					<tr>
						<td width="33.3333%" class="gm_table_title">아이템명</td>
						<td width="33.3333%" class="gm_table_title">개수</td>
						<td width="33.3333%" class="gm_table_title">사용하기</td>
					</tr>
					<?php
	                	foreach ($myItemList as $key => $item):
					?>
					<tr>
						<td><?=$item['name']?></td>
						<td><span class="font06"><?=$item['cnt']?></span>개</td>
						<td><a onclick="fnClickItem(<?=$item['item_id']?>,'<?=$item['name']?>', '<?=$item['item_value']?>')"><span class="gm_popup_btn1">사용</span></a></td>
					</tr>
					<?php endforeach;?>
					<!-- <tr>
						<td>20% 환급패치</td>
						<td><span class="font06">1</span>개</td>
						<td><a href="#"><span class="gm_popup_btn1">사용</span></a></td>
					</tr>
					<tr>
						<td>50% 환급패치</td>
						<td><span class="font06">50</span>개</td>
						<td><a href="#"><span class="gm_popup_btn1">사용</span></a></td>
					</tr>
					<tr>
						<td>35% 환급패치</td>
						<td><span class="font06">3</span>개</td>
						<td><a href="#"><span class="gm_popup_btn1">사용</span></a></td>
					</tr>
					<tr>
						<td>20% 환급패치</td>
						<td><span class="font06">1</span>개</td>
						<td><a href="#"><span class="gm_popup_btn1">사용</span></a></td>
					</tr>
					<tr>
						<td>50% 환급패치</td>
						<td><span class="font06">5</span>개</td>
						<td><a href="#"><span class="gm_popup_btn1">사용</span></a></td>
					</tr>
					<tr>
						<td>35% 환급패치</td>
						<td><span class="font06">3</span>개</td>
						<td><a href="#"><span class="gm_popup_btn1">사용</span></a></td>
					</tr> -->
				</table>
                <div class="gm_title2">사용할 아이템을 <span class="font06">클릭</span>해주세요.<br>
                아이템은 <span class="font06">오래된 순</span>으로 사용됩니다.<span class="font06">(최대 30일)</span></div>
            </div>      
		</div>
	</div>
</div>



<?php if(count($gameList) > 0) include('common/page_num.php'); ?>
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->
<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript">
let isMobile = 0; // 1-모바일
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
                
    $("li").click(function(){
        console.log('li click');
        $(this).addClass("on");
        $(this).siblings().removeClass("on");
    });

    $('#sports_count_0').text(<?=$totalFixtureCnt?>);
    // 첫경기 베팅판 가져오기
    sportsFixtureDisplay(<?=$firstFixtureid?>, '<?=$firstFixtureTime?>', '<?=$firstFixtureTeam1?>', '<?=$firstFixtureTeam2?>');

    // 상단 스포츠 항목 선택표시
    choiceSportsImage(<?=$sportsId?>);

    fnSortBetting();
    // 세션에 저장된 베팅슬립이 있으면 출력해준다.
    displayBetSlip();
    betMaxCheck();
    changeWillWinMoney();

    //좌측 상단 리그 및 팀명 검색어 셋팅
    $('#league_name').val('<?=$league_name?>');
    
    $(document).on('click', '.notify-close-btn', function () {
        notifyCloseBtn(this);
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
        wasteBtn();
    });

    // 베팅 선택
    $(document).on('click', '.odds_btn', function () {
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
        const today = new Date();
        const timeValue = new Date(fixture_start_date);
        const betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);

        // 10분이면 배당상태 2인것도 통과
        const dataBetStatus = $(this).data('bet-status');
        if (dataBetStatus && dataBetStatus != '1') {
            if (600 < betweenTime) {
                alert('배팅이 닫혀있습니다.');
                return false;
            }
        }

        let betMoney = $('#betting_slip_money').val();
        betMoney = Number(betMoney.replace(/,/gi, "")); //변경작업
        if (0 < betMoney) {
            $('#betting_slip_money').val(0);
        }

        let betListIndexStr = betListIndex + '';
        let fixtureId = betListIndexStr.split('_')[0];
        let alreadyCombinObj;
        let alreadyCombin = false;
        let checkBet = true;

        // 선택된 베팅이면 체크
        if ('OFF' == is_betting_slip) {
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
                                $(this).data('bet-price', server_betPrice);
                                betPrice = server_betPrice;
                                return;
                            } else {
                                $(this).data('bet-price', server_betPrice);
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

            return;
        }

        // 동일경기 선택
        //if ($('[data-index*="' + fixtureId + '"]').hasClass('bet_on')) {
        if (IsBetFixture(fixtureId)) {
            console.log('동일경기 선택');
            let isCombin = false;
            //const $selecteSports = $(this).closest('.soprts_in_acc').find('.bet_on');
            const $selecteSports = $(this).closest('.bet_list1_wrap').find('.bet_on');
            console.log('selectSports length : '+$selecteSports.length);
            if ($selecteSports.length > 1) {
                const $this = $(this);
                let flag1 = false;
                let flag2 = false;
                let targetObj;

                const validList = [];

                $selecteSports.each(function () {
                    //const targetId = $(this).closest('.sports_in').attr('id');
                    const targetId = $(this).closest('.bet_list1_wrap_in_new').attr('id');

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

                /* 모바일
                 $selecteSports.each(function(){
                 if($this.closest('dd').attr('id') == $(this).closest('dd').attr('id')){
                 flag1 = true;
                 }else{
                 targetObj = $(this);
                 flag2 = true;
                 }
                 });
                 */

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
                if (betMarketId == 28) {

                    const checkFlag1 = fnCheckCombine_renew($(this), betList, 28, 1, fixtureId)
                    const checkFlag2 = fnCheckCombine_renew($(this), betList, 28, 342, fixtureId)
                    if ((checkFlag1 && !checkFlag2) || (!checkFlag1 && checkFlag2)) {
                        isCombin = true;
                    }

                } else if (betMarketId == 1) {
                    if (fnCheckCombine_renew($(this), betList, 1, 28, fixtureId))
                        isCombin = true;

                } else if (betMarketId == 342) {
                    if (fnCheckCombine_renew($(this), betList, 342, 28, fixtureId))
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

        /*if (markets_display_name === "" || markets_display_name === 'null'
         || markets_display_name === null)
         betMarketType = markets_name_origin;
         else
         betMarketType = markets_display_name;*/
        betMarketType = markets_name_origin;
        if (+totalOdds > 100) {
            alert(`최대 배당률을 초과하였습니다. [최대: 100배]`);
            totalOdds = $('.total_odds').html();
            return false;
        }
        //console.log(betListIndex);
        $('.total_odds').html(totalOdds);
        //$(this).addClass('bet_on');
        //$(this).closest('.panel-primary').find('.odds_btn[data-bet-id="' + $(this).data('bet-id') + '"]').addClass('bet_on');
        $('[data-index="' + betListIndex + '"][data-odds-type="' + betOddsTypes + '"]').addClass('bet_on');
        let html = "<li class='sports_cart_bet' data-index=" + betListIndex + " data-odds-types=" + betOddsTypes +
                " data-bet-id=" + betId + " data-bet-price=" + betPrice + " data-markets-name='" + betMarketType +
                "' data-bet-base-line='" + betBaseLine + "' data-fixture-start-date='" + fixture_start_date + "' data-leagues_m_bet_money=" + leagues_m_bet_money + ">" +
                "<div width='100%'class='cart_bet'>" +
                "<div>" +
                "<td>" + gameObj['fixture_participants_1_name'] + "<span class='sports_cart_bet_font1'> " + betOddsTypesDisplay + "</span></td>" +
                "<td><a href='#' class='sports_cart_bet_img'><img src='/assets_w/images/cart_close.png'" +
                "class='notify-close-btn' data-index=" + betListIndex + " data-bet-id=" + betId + "></a><span class='sports_cart_bet_p'>" + betPrice + "</span></td>" +
                "</div>" +
                "<div>" +
                "<td colspan='2'><span class='sports_cart_bet_font2'>" + betMarketType + "</span></td>" +
                "</div>" +
                "<div>" +
                "<td colspan='2'><span class='sports_cart_bet_font3'>" + gameObj['fixture_participants_1_name'] + "<img src='/assets_w/images/vs.png' width='25'>" + gameObj['fixture_participants_2_name'] + "</span></td>" +
                "</div>" +
                "</div>" +
                "</li>";
        $('.slip_tab_wrap').after($(html));

        let team1 = gameObj['fixture_participants_1_name'];
        let team2 = gameObj['fixture_participants_2_name'];
        addBetSlip(betId, betPrice, betListIndex, betOddsTypes, betOddsTypesDisplay, betMarketType, team1, team2, betBaseLine, fixtureId, fixture_start_date, leagues_m_bet_money, betMarketId);

        if (folderType == 'D') {
            if (service_bonus_folder == 'Y') {
                setBonusPrice(totalOdds, getBetSlipCount());
            }
        } else {
            $('.bonus_total_odds').text(0);
        }

        // 뱃팅슬롯 카운트
        if (isMobile) {
            $('.cart_count2').text(fnGetCartCount());
        }

        changeWillWinMoney();
        betMaxCheck();

        if (alreadyCombin) {
            alreadyCombinObj.trigger('click');
        }
    });
}); // end ready
</script>
</body>
</html>