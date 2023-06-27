<?php
    use App\Util\DateTimeUtil;
    use App\Util\StatusUtil;
?>
<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
    <div class="title">베팅내역</div>
</div>

<?php
    $imageBasePath = config(App::class)->imageUrl.'/'.config(App::class)->imagePath;

    $p_data['num_per_page'] = 10;
    $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    $p_data['start'] = ($page - 1) * $p_data['num_per_page'];

    $total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
    //$total_page = 0;
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($page / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block) {
        $last_page = $total_page;
    }
    
    if (isset($_REQUEST['betFromDate']) && isset($_REQUEST['betToDate'])) {
        $default_link = 'betting_history?menu=' . $menu . '&bet_group=' . $_GET['bet_group'] . '&betFromDate=' . $_REQUEST['betFromDate'] . '&betToDate=' . $_REQUEST['betToDate'] . '&clickItemNum=' . $clickItemNum;
    } else {
        $default_link = 'betting_history?menu=' . $menu . '&bet_group=' . $_GET['bet_group'] . '&clickItemNum=' . $clickItemNum;
    }

    $miniGameName[3] = 'EOS 파워볼';
    $miniGameName[4] = '파워사다리';
    $miniGameName[5] = '키노사다리';
    $miniGameName[6] = '가상축구';
    $miniGameName[15] = '파워볼';
?>

<input type="hidden" id="clickItemNum" value="<?= $clickItemNum?>">

<div class="contents_wrap">
	<div class="contents_box">
        <?= view('/web/common/betting_history_snb') ?>
        <?php if($_GET['bet_group'] < 3 || $_GET['bet_group'] == 9) : ?>
        <?php if(!empty($betList)) : ?>
        <?php foreach ($betList as $key => $bet) : ?>

        <?php
            //$betIdx = explode(' ', $bet->slot_bet_idx);
            $currentDate = date("Y-m-d H:i:s");
            $checkButton = true; // 배팅취소, 삭제 버튼
            list($result, $class) = \App\Util\CodeUtil::bet_status_by_info_2($bet->bet_status, $bet->total_bet_money, $bet->take_money);
        ?>

        <div class="con_box20 bet_history_wrap1" id="<?= $bet->idx;?>" >
            <div class="bet_history_tr">
                <div class="bet_history_title title2">경기시간</div>
                <div class="bet_history_title title2">종목</div>
                <div class="bet_history_title title2">리그명</div>
                <div class="bet_history_title title2">경기타입</div>
                <div class="bet_history_title title4">팀</div>
                <div class="bet_history_title title2">선택게임</div>
                <div class="bet_history_title title1">배당률</div>
                <div class="bet_history_title title1">스코어</div>
                <div class="bet_history_title title2">적중여부</div>
            </div>

            <?php if(!empty($bet)) : ?>
            <?php foreach ($bet->betDetail as $k => $detail) : ?>

            <?php
                $gameName = '';
                if ($bet->bet_type < 3) {
                    if (isset($detail['display_name']) && $detail['display_name'] != '') {
                        $gameName = $detail['display_name'];
                    } else {
                        $gameName = $detail['name'];
                    }

                    if($gameName == "UFC") {
                        $imageIconPath = "/assets_w/images/icon_game09.png";
                    }
                    if($gameName == "야구") {
                        $imageIconPath = "/assets_w/images/icon_game04.png";
                    }
                    if($gameName == "핸드볼") {
                        $imageIconPath = "/assets_w/images/icon_game05.png";
                    }
                    if($gameName == "아이스 하키") {
                        $imageIconPath = "/assets_w/images/icon_game07.png";
                    }
                    if($gameName == "배구") {
                        $imageIconPath = "/assets_w/images/icon_game05.png";
                    }
                    if($gameName == "농구") {
                        $imageIconPath = "/assets_w/images/icon_game03.png";
                    }
                    if($gameName == "축구") {
                        $imageIconPath = "/assets_w/images/icon_game02.png";
                    }
                    if($gameName == "E-스포츠") {
                        $imageIconPath = "/assets_w/images/icon_game08.png";
                    }

                } else {
                    $gameName = $miniGameName[$bet->game_type];
                }
                
                $fixture_start_date = explode(' ', $detail['fixture_start_date']);
                $check_date = date("Y-m-d H:i:s",strtotime($detail['fixture_start_date']) - 60*30);
                //$this->logger->debug('currentDate : '.$currentDate.'    check_date : '.$check_date);
                if($currentDate > $check_date || $bet->bet_status == 5)
                    $checkButton = false;
                    
                    if ($detail['markets_id'] == 6 || $detail['markets_id'] == 9) {
                        $betNameDisplay = $detail['bet_name'];
                    } else {
                        $betNameDisplay = StatusUtil::betNameToDisplay_new($detail['bet_name'], $detail['markets_id']);
                    }
                    
                    if(strlen($detail['markets_base_line']) > 0){
                        $marketsName = '(' . explode(' ', $detail['markets_base_line'])[0] . ') ' . $detail['markets_name'];
                    }else{
                        $marketsName = $detail['markets_name'].'<br>';
                    }
                    
                    $result_score = '-';
                    if (isset($detail['result_score'])) {
                        $score = '';
                        if(strpos($detail['result_score'] , 'result_extra') == false) {
                            $score = explode('result_extra', $detail['result_score'])[0];
                        }else {
                            //$score = explode(',"result_extra', substr($detail['result_score'], 0, -1))[0];
                            
                            
                            $score = substr($detail['result_score'], 0, strpos($detail['result_score'] , ',"result_extra'))."}";
                        }
                        
                        $json_result = json_decode(stripslashes($score), true);
                        $result_score = $json_result['live_results_p1'] . ' : ' . $json_result['live_results_p2'];
                        
                        
                    }
            ?>
            
            <!-- 그룹1 -->
            <div class="bet_history_tr">
                <div class="bet_history_td td2"><?=date("m-d", strtotime($detail['fixture_start_date']))?> <?=date("H:i", strtotime($detail['fixture_start_date']))?></div>
                <div class="bet_history_td td2"><?=$gameName?></div>
                <div class="bet_history_td td2"><?=isset($detail['league_display_name'])? $detail['league_display_name']:$detail['league_name']?></div>
                <div class="bet_history_td td2"><?= $marketsName ?></div>
                <div class="bet_history_td td4">
                    <span class="m_bet_l"><?=$detail['p1_display_name'] != '' ? $detail['p1_display_name']:$detail['p1_team_name']?></span>
                    <span class="m_bet_vs"><img src="/assets_w/images/vs.png" width="32"></span>
                    <span class="m_bet_r"><?=$detail['p2_display_name'] != '' ? $detail['p2_display_name']:$detail['p2_team_name']?></span>
                </div>
                <div class="bet_history_td td2"><?=$betNameDisplay?></div>
                <div class="bet_history_td td1"><span class="font05"><?=number_format($detail['bet_price'], 2)?></span></div>
                <div class="bet_history_td td1"><?= $result_score ?></div>
                <div class="bet_history_td td2"><span class="<?= \App\Util\CodeUtil::bet_status_by_color_2($detail['bet_status']) ?>"><?= \App\Util\CodeUtil::memberBetStatusToStr($detail['bet_status']) ?></span></div>
            </div>
            <!-- 그룹1끝 -->
            
            <?php endforeach; ?>
            <?php endif; ?>

            <div class="bet_history" >
                <ul class="bet_history_span">
                    <li class="font08">전체배당(보너스포함)&nbsp;&nbsp;<span class="font06">x <?= number_format($bet->total_bet_price, 2) ?></span></li>
                    <li class="font08">보너스(아이템)&nbsp;&nbsp;
                        <span class="font06">x <?= number_format($bet->bonus_price, 2) ?></span>
                        <span class="font10">(x <?= number_format($bet->item_bonus_price, 2) ?>)</span>
                    </li>
                    <li class="font08">아이템&nbsp;&nbsp;<span class="font10">
                    <?php
                        if ($bet->item_idx == 0) {
                            $itemName = '미사용';
                        } else {
                            $itemName = $bet->item_name;
                        }
                        echo $itemName;
                    ?>

                    </span></li>
                    <li class="font08">베팅슬립&nbsp;&nbsp;<span class="font03"><?=$bet->is_betting_slip?></span></li>
                    <li class="font08">베팅금액&nbsp;&nbsp;<span class="font03"><?= number_format($bet->total_bet_money) ?> 원</span></li>
                    <li class="font08">예상적중금액&nbsp;&nbsp;<span class="font03"><?= number_format($bet->total_bet_money * number_format($bet->total_bet_price, 2)) ?>원</span></li>
                    <li class="font08">적중금액&nbsp;&nbsp;<span class="font06"><?= number_format($bet->take_money) ?>원</span></li>
                </ul>
                <ul class="bet_history_span">
                    <li class="font08">베팅시간&nbsp;&nbsp; <span class="font03"><?=date("m-d", strtotime($bet->create_dt))?> (<?=DateTimeUtil::getWeekDay($bet->create_dt)?>) <?=date("H:i:s", strtotime($bet->create_dt))?></span></li>
                    <li class="font08">베팅결과&nbsp;&nbsp; <span class="<?= $class ?>"><?= $result ?></span></li>
                </ul>
            </div>
        </div> <!-- .bet_history_wrap1 -->

        <!-- mobile -->
        <div class="con_box20 bet_history_wrap2">
			<div class="bet_history_box">
                <?php foreach ($bet->betDetail as $k => $detail) : ?>

                <?php
                    $gameName = '';
                    if ($bet->bet_type < 3) {
                        if (isset($detail['display_name']) && $detail['display_name'] != '') {
                            $gameName = $detail['display_name'];
                        } else {
                            $gameName = $detail['name'];
                        }

                        if($gameName == "UFC") {
                            $imageIconPath = "/assets_w/images/icon_game09.png";
                        }
                        if($gameName == "야구") {
                            $imageIconPath = "/assets_w/images/icon_game04.png";
                        }
                        if($gameName == "핸드볼") {
                            $imageIconPath = "/assets_w/images/icon_game05.png";
                        }
                        if($gameName == "아이스 하키") {
                            $imageIconPath = "/assets_w/images/icon_game07.png";
                        }
                        if($gameName == "배구") {
                            $imageIconPath = "/assets_w/images/icon_game05.png";
                        }
                        if($gameName == "농구") {
                            $imageIconPath = "/assets_w/images/icon_game03.png";
                        }
                        if($gameName == "축구") {
                            $imageIconPath = "/assets_w/images/icon_game02.png";
                        }
                        if($gameName == "E-스포츠") {
                            $imageIconPath = "/assets_w/images/icon_game08.png";
                        }

                    } else {
                        $gameName = $miniGameName[$bet->game_type];
                    }
                    
                    $fixture_start_date = explode(' ', $detail['fixture_start_date']);
                    $check_date = date("Y-m-d H:i:s",strtotime($detail['fixture_start_date']) - 60*30);
                    //$this->logger->debug('currentDate : '.$currentDate.'    check_date : '.$check_date);
                    if($currentDate > $check_date || $bet->bet_status == 5)
                        $checkButton = false;
                        
                        if ($detail['markets_id'] == 6 || $detail['markets_id'] == 9) {
                            $betNameDisplay = $detail['bet_name'];
                        } else {
                            $betNameDisplay = StatusUtil::betNameToDisplay_new($detail['bet_name'], $detail['markets_id']);
                        }
                        
                        if(strlen($detail['markets_base_line']) > 0){
                            $marketsName = '(' . explode(' ', $detail['markets_base_line'])[0] . ') ' . $detail['markets_name'];
                        }else{
                            $marketsName = $detail['markets_name'].'<br>';
                        }
                        
                        $result_score = '-';
                        if (isset($detail['result_score'])) {
                            $score = '';
                            if(strpos($detail['result_score'] , 'result_extra') == false) {
                                $score = explode('result_extra', $detail['result_score'])[0];
                            }else {
                                //$score = explode(',"result_extra', substr($detail['result_score'], 0, -1))[0];
                                
                                
                                $score = substr($detail['result_score'], 0, strpos($detail['result_score'] , ',"result_extra'))."}";
                            }
                            
                            $json_result = json_decode(stripslashes($score), true);
                            $result_score = $json_result['live_results_p1'] . ' : ' . $json_result['live_results_p2'];
                            
                            
                        }
                    
                    
                ?>
                <div class="bet_history_top">
                    <div class="bet_history_top_l">
                        <div class="m_bet_sport"><img src="<?= $imageIconPath?>" width="18"></div>
                        <img src="/assets_w/images/line.png" alt="">
                        <div class="m_bet_league"><?=isset($detail['league_display_name'])? $detail['league_display_name']:$detail['league_name']?></div>
                        <img src="/assets_w/images/line.png" alt="">
                        <div class="m_bet_time"><?=date("m-d", strtotime($detail['fixture_start_date']))?> <?=date("H:i", strtotime($detail['fixture_start_date']))?></div>
                    </div>
                    <div class="bet_history_top_r">
                        <div class="m_bet_bet">
                            <span class="m_bet_type"><?= $marketsName ?></span>
                            <img src="/assets_w/images/line.png" alt="">
                            <span class="m_bet_mybet font06"><?=$betNameDisplay.' (' .number_format($detail['bet_price'], 2).')'?></span>
                        </div>
                    </div>
                </div>
                
                <div class="bet_history_tr">

                    <div class="bet_history_td td10">
                        <div class="m_bet_l"><?=$detail['p1_display_name'] != '' ? $detail['p1_display_name']:$detail['p1_team_name']?></div>
                        <img src="/assets_w/images/vs.png" width="30">
                        <div class="m_bet_r"><?=$detail['p2_display_name'] != '' ? $detail['p2_display_name']:$detail['p2_team_name']?></div>
                        <span class="m_bet_result"><?= $result_score ?></span>
                        <span class="<?= \App\Util\CodeUtil::bet_status_by_color_2($detail['bet_status']) ?>"><?= \App\Util\CodeUtil::memberBetStatusToStr($detail['bet_status']) ?></span>
                    </div>
                    <!-- <div class="bet_history_td td3"><?= $result_score ?></div> -->
                    <!-- <div class="bet_history_td td3"></div> -->
                </div>
                <?php endforeach; ?>

				<div class="bet_history_bottom">
                    <div class="m_bet_info"><span class="icon_info">베팅시간</span><span class="text_info"><span class="text_info_ellipsis"><?=date("m-d", strtotime($bet->create_dt))?> (<?=DateTimeUtil::getWeekDay($bet->create_dt)?>) <?=date("H:i", strtotime($bet->create_dt)) ?></span></span></div>
					<div class="m_bet_info"><span class="icon_info">베팅금액</span><span class="text_info"><?= number_format($bet->total_bet_money) ?> 원</span></div>
					<div class="m_bet_info"><span class="icon_info">전체배당</span><span class="text_info font06">x <?= number_format($bet->total_bet_price, 2) ?></span></div>
                    <div class="m_bet_info"><span class="icon_info">보너스</span><span class="text_info font06">x <?= number_format($bet->bonus_price, 2) ?></span></div>
                    <div class="m_bet_info"><span class="icon_info">아이템</span><span class="text_info font10">x <?= number_format($bet->item_bonus_price, 2) ?></span></div>
                    <div class="m_bet_info"><span class="icon_info">아이템사용</span><span class="text_info font10">
                    <?php
                        if ($bet->item_idx == 0) {
                            $itemName = '미사용';
                        } else {
                            $itemName = $bet->item_name;
                        }
                        echo $itemName;
                    ?>
                    </span></div>
					<div class="m_bet_info"><span class="icon_info">예상적중금액  </span><span class="text_info"><?= number_format($bet->total_bet_money * number_format($bet->total_bet_price, 2)) ?>원</span></div>
					<div class="m_bet_info"><span class="icon_info">적중금액</span><span class="text_info"><?= number_format($bet->take_money) ?>원</span></div>
                    <div class="m_bet_info"><span class="icon_info">배팅슬립</span><span class="text_info"><?=$bet->is_betting_slip?></span></div>
                    <div class="m_bet_info"><span class="icon_info">적중여부</span><span class="text_info"><span><?= $result ?></span></span></div>
					<!-- <div class="m_bet_info"><span class="icon_info">적중여부</span><span class="text_info"><span class="<?= $class ?>"><?= $result ?></span></span></div> -->
				</div>
			</div>
		</div>
        <!-- mobile end -->

        <div class="con_box20" style="margin-bottom: 1.5%;">
            <div class="btn_wrap_right">
                <ul>
                    <?php if($checkButton) : ?>
                    <!-- <li><a href="javascript:onBetHistory(<?= $bet->idx ?>,'web')"><span class="btn2_1">베팅내역 올리기</span></a></li> -->
                    <li><a href="javascript:onBetCancel(<?= $bet->idx ?>,<?= $bet->bet_status ?>)"><span class="btn2_2">베팅취소</span></a></li>
                    <?php else : ?>

                        <?php if($bet->bet_status == 5 || $bet->bet_status == 3) : ?>
                        <!-- <li><a href="javascript:onBetHistory(<?= $bet->idx ?>,'web')"><span class="btn2_1">베팅내역 올리기</span></a></li> -->
                        <li><a href="javascript:onBetHide(<?= $bet->idx ?>)"><span class="btn2_2">삭제</span></a></li>
                        <?php else : ?>
                        <!-- <li><a href="javascript:onBetHistory(<?= $bet->idx ?>,'web')"><span class="btn2_1">베팅내역 올리기</span></a></li> -->
                        <li><a href="javascript:void(0);"><span class="btn2_2">진행중</span></a></li>                        	
                        <?php endif; ?>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($bet)) : ?>
        <div class="con_box10">
                <?php include('common/page_num.php'); ?>
        </div>
        <?php endif; ?>
        <!-- 미니게임 -->
        <?php else : ?>
        <div class="scroll_h_wrap">
            <div class="scroll_h_inner">
                <div class="con_box10 " id="<?= $bet['idx'] ?? '' ?>">
                    <div class="bet_history_tr">
                        <div class="bet_history_title title2">일자</div>
                        <div class="bet_history_title title3">종목</div>
                        <div class="bet_history_title title2 mini_none">회차</div>
                        <div class="bet_history_title title3 mini_none">선택게임</div>
                        <div class="bet_history_title title2 mini_none">베팅금액</div>
                        <div class="bet_history_title title2 mini_none">회차결과</div>
                        <div class="bet_history_title title2">당첨금</div>
                        <div class="bet_history_title title2">적중여부</div>
                    </div>
                </div>

                <?php if(!empty($betList)) : ?>
                <?php foreach ($betList as $key => $bet) : ?>
        
                <?php 
                    $currentDate = date("Y-m-d H:i:s");
                    
                    // 게임결과
                    $arrResult = json_decode($bet['result'], true);
                    if($bet['result_score']){
                        $arrResult = json_decode($bet['result_score'], true);
                    }
                    $gameResult = \App\Util\StatusUtil::getMiniGameResultString($bet['bet_type'], $arrResult);
//print_r($bet);                    
                    $gameName = '파워볼';
                    $round = 0;
                    if($bet['bet_type'] == 4){
                        $gameName = '파워사다리';
                        $round = $bet['cnt'];
                    }else if($bet['bet_type'] == 5){
                        $gameName = '키노사다리';
                        $round = $bet['cnt'];
                    }else if($bet['bet_type'] == 6){
                        $gameName = '가상축구';
                        $round = $bet['cnt'];
                    }else if($bet['bet_type'] == 15){
                        $gameName = '파워볼';
                        $start_dt = explode(' ', $arrResult['sdate']);
                        $cnt_date = $start_dt[1];
                        $cnt_date_arr = explode(':', $cnt_date);
                        $round = round(((+$cnt_date_arr[0] * 60) + +$cnt_date_arr[1]) / 5) + 1;
                    }else{
                        $gameName = 'EOS 파워볼';
                        $round = $bet['cnt'];
                    }
                    
                    $status = '대기';
                    $statusColor = 'sports_division3';
                    
                    if($bet['bet_status'] == 3){
                        if($bet['total_bet_money'] <  $bet['take_money']){
                            $status = '적중';
                            $statusColor = 'sports_division2';
                        }else if($bet['total_bet_money'] ==  $bet['take_money']){
                            $status = '취소';
                            $statusColor = 'sports_division1';
                        }else{
                            $status = '미적중';
                            $statusColor = 'sports_division1';
                        }
                    }
                ?>

                <!-- 그룹1 -->
                <div class="bet_history_tr">
                    <div class="bet_history_td td2"><?=date("m-d", strtotime($bet['create_dt']))?> (<?=DateTimeUtil::getWeekDay($bet['create_dt'])?>) <?=date("H:i:s", strtotime($bet['create_dt']))?></div>
                    <div class="bet_history_td td3">미니게임 / <?=$gameName?></div>
                    <div class="bet_history_td td2 mini_none"><?=$round?> 회</div>
                    <div class="bet_history_td td3 mini_none"><?=$bet['ls_markets_name']?> (x<?=number_format($bet['bet_price'], 2)?>)</div>
                    <div class="bet_history_td td2 mini_none"><?=number_format($bet['total_bet_money'])?>원</div>
                    <div class="bet_history_td td2 mini_none"><?=$gameResult?></div>
                    <div class="bet_history_td td2"><span class="font06"><?=number_format($bet['take_money'])?>원</span></div>
                    <div class="bet_history_td td2"><span class="<?=$statusColor?>"><?=$status?></span></div>
                </div>
                <!-- 그룹1끝 -->
        
                <?php endforeach; ?>
                <?php endif; ?>
            </div><!-- .scroll_h_inner -->
        </div><!-- .scroll_h_wrap -->


        <?php if(!empty($betList)) : ?>
        <div class="con_box10">
            <?php include('common/page_num.php'); ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div> <!-- .contents_box -->
</div> <!-- .contents_wrap -->
</div>  <!-- for inside header_wrap.php -->

<?= view('/web/common/footer_wrap') ?>

<!-- top버튼 -->
<!-- <a href="#myAnchor" class="go-top">▲</a> -->
<script type="text/javascript" src="/assets_w/js/sub_05.js?v=<?php echo date("YmdHis"); ?>"></script>
<script type="text/javascript">
    
    let del_click = false; // 쪽지 삭제버튼 클릭시 아코디언 클릭 이벤트 무시용도

    (function ($) {
        // $('.popup_accordion > li:eq(0) a').addClass('active').next().slideDown();
        $('.popup_accordion a').click(function (j) {
            // 글삭제 클릭시 이벤트 무시
            if (true == del_click) {
                del_click = false;
                return;
            }

            // 선택한 글을 구한다.
            var dropDown = $(this).closest('li').find('div');

            // 선택한 글을 제외한 나머지 내용을 닫는다.
            $(this).closest('.popup_accordion').find('div').not(dropDown).slideUp();
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            } else {
                // 이전에 펼쳐진 태그에 active 클레스를 제거하고, 새로 펼친 태그에 속성을 추가한다.
                $(this).closest('.popup_accordion').find('a.active').removeClass('active');
                $(this).addClass('active');
            }

            dropDown.stop(false, true).slideToggle();

            j.preventDefault();
        });
    })(jQuery);
   
    let delete_idx = 0;
    let check_auth_code = false;

    let option = $("#select_bet_group option:selected").val();
    
    $(document).ready(function () {
        
        let betGroup = <?= $_SESSION['level'] == 9?$_GET['bet_group'] : $_GET['bet_group'] - 1 ?>;
        $('.bet_detail_tabs').children('li:eq('+betGroup+')').addClass('active');
    
        // 쪽지 전체삭제
        $('.btn2').on('click', function () {
            let mes = '모든 쪽지가 삭제됩니다. 삭제하시겠습니까?'
            if (confirm(mes) == false) {
                return;
            }

            $.ajax({
                url: '/web/betting_history/deleteAllMessage',
                type: 'post',
                data: {
                    'idx': delete_idx,
                },
            }).done(function (response) {
                alert("쪽지가 전부 삭제되었습니다.");
         
                location.replace('/web/betting_history?menu=d');
            }).fail(function (error) {
                alert(error.responseJSON['messages']['error']);
            });
        });

        // 쪽지 전체확인
        $('.all_read').on('click', function () {
            $.ajax({
                url: '/web/betting_history/allReadMessage',
                type: 'post',
            }).done(function (response) {
                alert("쪽지 확인 됐습니다.");

                <?= session()->set('tm_unread_cnt', 0) ?>

                location.replace('/web/betting_history?menu=d');
            }).fail(function (error) {
                alert(error.responseJSON['messages']['error']);
            });
        });

        // 인증확인
        $('.check_auth_code_btn').on('click', function () {
            if ($('.auth_code').val().length <= 0) {
                alert('인증코드를 입력해주세요.');
                return;
            }
        
            $.ajax({
                url: '/member/authCodeCheck',
                type: 'post',
                data: {
                    'auth_code': $('.auth_code').val()
                },
            }).done(function (response) {
                alert('인증되었습니다.');
                check_auth_code = true;
                $('.auth_code').val('인증완료');
            }).fail(function (error, response, p) {
                alert(error.responseJSON.messages.error);
            }).always(function (response) {});
        });

        $("select[id='select_bet_group']").on("change", function () {
            let option = $("#select_bet_group option:selected").val();
        });
 
    });

    $(function () {
        let htmls = ``
        let htmls2 = ``
        if(location.href.includes("betting_history?menu=b&bet_group=2") || location.href.includes("betting_history?menu=b&bet_group=1")){
            
            if($(".bet_history_wrap1").attr('id')){
                htmls = ``
            }
            else{
                htmls = `
                <div class="con_box20 bet_history_wrap1">
                    <div class="bet_history_tr">
                        <div class="bet_history_title title2">경기시간</div>
                        <div class="bet_history_title title2">종목</div>
                        <div class="bet_history_title title2">리그명</div>
                        <div class="bet_history_title title2">경기타입</div>
                        <div class="bet_history_title title4">팀</div>
                        <div class="bet_history_title title2">선택게임</div>
                        <div class="bet_history_title title1">배당률</div>
                        <div class="bet_history_title title1">스코어</div>
                        <div class="bet_history_title title2">적중여부</div>
                    </div>
                </div>
                <?php include('common/page_num.php'); ?>
                `
                htmls2 = `
                <div class="con_box20 bet_history_wrap2">
                    <div class="bet_history_box">
                        <div class="bet_history_top" style="display:block; text-align: center;">기록이 없습니다.</div>
                    </div>
                </div>
                `
                $(".contents_box").append(htmls2)
                $(".contents_box").append(htmls)
            }
        }
        setTimeout(() => {
            if($(".page_wrap").hasClass("page_wrap") === true){
                htmls = ``
            }else{
                htmls2 = `
                <div class="con_box20 bet_history_wrap2">
                    <div class="bet_history_box">
                        <div class="bet_history_top" style="display:block; text-align: center;">기록이 없습니다.</div>
                    </div>
                </div>
                <?php include('common/page_num.php'); ?>
                `
                $(".contents_box").append(htmls2)
            }    
        }, 50);
        if($(document).width() < 500){
            if($(".bet_history_tr").length > 1){
            htmls = ``
            }else{
                $(".scroll_h_wrap").remove()
            }
        }
        
        
        
        if (getParam('menu') == 'b') {
            $('#tab1').attr('style', 'display: none;');
            $('#tab2').attr('style', 'display: block;');
            $('.tabs').children().eq(0).removeClass('active');
            $('.tabs').children().eq(1).addClass('active');
        } else if (getParam('menu') == 'd') {
            $('#tab1').attr('style', 'display: none;');
            $('#tab4').attr('style', 'display: block;');
            $('.tabs').children().eq(0).removeClass('active');
            $('.tabs').children().eq(2).addClass('active');
        }

        // 검색버튼 눌러서 날짜 정보가 넘어왔을시 날짜를 셋팅한다.
        <?php if (isset($_GET['betFromDate'])) : ?>
        $('#from_date').val('<?= $_GET['betFromDate'] ?>');
        <?php else : ?>
        $('#from_date').val(getFormatDate(new Date()));
        <?php endif; ?>

        <?php if (isset($_GET['betToDate'])) : ?>
        $('#to_date').val('<?= $_GET['betToDate'] ?>');
        <?php else : ?>
        $('#to_date').val(getFormatDate(new Date()));
        <?php endif; ?>

        var dateFormat = "yy-mm-dd",
            from = $("#from_date")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3
                })
                .on("change", function () {
                    to.datepicker("option", "minDate", getDate(this));
                }),
            to = $("#to_date").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3
                })
                .on("change", function () {
                    from.datepicker("option", "maxDate", getDate(this));
                });

        function getDate(element) {
            console.log(dateFormat);
            console.log(element.value);
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }
            return date;
        }
    });
   
    function passwordChange() {
        const pwd_before = $('.password_before');
        const pwd_after = $('.password_after');
        const pwd_after_ = $('.password_after_');
        const pwd_alert1 = $('.pwd_alert1');
        const pwd_alert2 = $('.pwd_alert2');
        const pwd_alert3 = $('.pwd_alert3');
        if (!pwd_before.val() || !pwd_after.val() || !pwd_after_.val()) {
            alert("현재비밀번호 및 새 비밀번호를 입력해주세요");
            return;
        }

        pwd_alert1.addClass('d-none');
        pwd_alert2.addClass('d-none');
        pwd_alert3.addClass('d-none');
        if (pwd_after.val().length < 4 || pwd_after.val().length > 12) {
            pwd_alert2.text('비밀번호는 4자~12자로 설정해주세요.');
            pwd_alert2.removeClass('d-none');
            return;
        }

        if (pwd_after.val() !== pwd_after_.val()) {
            pwd_alert3.text('비밀번호가 확인과 일치하지 않습니다.');
            pwd_alert3.removeClass('d-none');
            return;
        }

        if (check_auth_code == false) {
            alert('인증확인 해주세요.');
            return;
        }

        $.ajax({
            url: '/member/passwordChange',
            type: 'post',
            data: {
                'beforePassword': $('.password_before').val(),
                'afterPassword': $('.password_after').val(),
                'afterPassword_': $('.password_after_').val(),
                'authCode': $('.auth_code').val(),
            },
        }).done(function (response) {
            alert('수정에 성공하였습니다.');
            location.reload();
        }).fail(function (error, response, p) {
            $('.password_before').parent().parent().children('.my_text').html(error.responseJSON.messages.messages);
        }).always(function (response) {});
    }

    function changeGameOption(option, sports) {
        let html = '';
        if (option == 0) {
            html += "<option value='0' selected>전체종목</option>";
            html += "<option value='6046'>축구</option>";
            html += "<option value='48242'>농구</option>";
            html += "<option value='154830'>배구</option>";
            html += "<option value='154914'>야구</option>";
        } else if (option == 3) {
            html += "<option value='0' selected>전체종목</option>";
            html += "<option value='3'>파워볼</option>";
            html += "<option value='4'>파워사다리</option>";
            html += "<option value='5'>키노사다리</option>";
            html += "<option value='6'>가상축구</option>";
        } else {
            html += "<option value='0' selected>전체종목</option>";
            html += "<option value='6046'>축구</option>";
            html += "<option value='48242'>농구</option>";
            html += "<option value='154830'>배구</option>";
            html += "<option value='154914'>야구</option>";
        }

        $("select[id='select_sports']").html(html);
        $("#select_sports").val(sports).prop('selected', true);
    }
    // 쪽지함 내용 갱신
    function getAllMessage() {
        $.ajax({
            url: "/api/message/getAllMessage",
            data: {},
            method: 'POST'
        }).done(function (response) {
            //console.log(response['data']);

            var tbody = $("#message_tbody");
            tbody.empty();
            response['data']['messages'].forEach(function (item) {
                console.log('item : ' + item);
                let del_td = '';
                if (item['read_yn'] == 'Y') {
                    del_td = `<td id="del_btn_${item['idx']}" width="5%" class="meno_table_center" onclick="delMsg(${item['idx']})"><span class="btn_t_inner">삭제</span></td>`;
                } else {
                    del_td = `<td id="del_btn_${item['idx']}" width="5%" class="meno_table_center"></td>`;
                }

                let read_time = '';
                if (item['read_time'] == '' || item['read_time'] == null) {
                    read_time = '';
                } else {
                    read_time = item['read_time'];
                }
                let html = `<tr id="tr_${item['idx']}" onclick="readMsg(${item['idx']}, '${item['read_yn']}')">
                            <td  width="55%" class="meno_table_left">${item['title']}</td>
                            <td class="list_table_center">관리자</td>
                            <td width="15%" class="meno_table_center">${item['reg_time']}</td>
                            <td id="read_time_${item['idx']}" width="15%" class="meno_table_center">${read_time}</td>` + del_td +
                        `</tr>`;
                tbody.append(html);
            })
        }).fail(function (error) {
            console.log(error.responseJSON['messages']);
            alert(error.responseJSON['messages']['messages']);
            location.href = '/member/logout';
        });
    }

    const getRankList = function (param) {
        if (!param)
            param = {
                curpageNo: 1
            };
        $.ajax({
            url: '/api/member/getRankList',
            type: 'post',
            data: param,
        }).done(function (res) {
            console.log(res);

            const list = res.data.list;
            const totalCnt = Number(res.data.count[0].count);
            const curPageNo = Number(res.data.curPageNo);

            let html = '';
            list.forEach(function (item, idx) {
                const displayIdx = ((curPageNo - 1) * 10) + idx + 1;
                html += `
                    <tr>
                        <td class="list_table_center"><span class="${displayIdx <= 3 ? 'box101' : 'box102'}">${displayIdx}</span></td>
                        <td class="list_table_center">${item.nick_name}</td>
                        <td class="list_table_center"><span class="font_004">${Commas(item.money)}</span></td>
                    </tr> 
                `;
            });
            fnSetPagination(totalCnt, curPageNo, 'getRankList', 'rankPaginationArea');
            $('#rankListArea').html(html);

        }).fail(function (err) {
            console.log(err);
        })
    }

    const fnUseHitExceptionPatchItem = function(betIdx, betDetailIdx){
        alert('해당이벤트는 점검중입니다.');
        return;
    	//아이템 보유 판단 로직 추가 및 모바일도 로직 추가
    	let itemCnt = <?= $hitExceptionPatchItemCnt?>;

		if(itemCnt < 1) {
			alert("보유 적특패치 아이템이 없습니다.");
			return false;
		}
    	
    	var str_msg = '적특패치는 베팅한 경기 중 한 경기만 사용 가능합니다.\n 적특패치 아이템을 사용하시겠습니까?';
    	
    	const result = confirm(str_msg);

    	if (result) {
    		$.ajax({
    	        url: '/web/useItem',
    	        type: 'post',
    	        data: {
    	            'betIdx' : betIdx
    	            ,'betDetailIdx' : betDetailIdx
    	        },
    	    }).done(function (response) {
    	        alert(response.messages);
    	        location.reload();
    	    }).fail(function (error) {
    	    	alert(error.responseJSON['messages']['error']);
    	    });
    	}
    }
</script>
</body>
</html>