<?php $imagePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath.'/'; ?>
<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>


	<div class="your-class_slide_pc">
			<?php if(count($bannerList) > 0) { ?>
				<?php for($i=0; $i < count($bannerList); $i++) { ?>

					<?php if( $bannerList[$i]['display_type'] == '1' ){ ?>
					
						<div class="class_slide_pc slide_pc">
							<img src="<?=$imagePath.$bannerList[$i]['thumbnail']?>?v=<?php echo date("YmdHis"); ?>" alt="main slide image">
						</div>

					<?php } ?>

				<?php } ?>
			<?php } ?>
	</div>
	<!-- 메인슬라이드 -->
	<link rel="stylesheet" href="/assets_w/jq/slick/slick.css?v=<?php echo date("YmdHis"); ?>"/>
	<link rel="stylesheet" href="/assets_w/jq/slick/slick-theme.css?v=<?php echo date("YmdHis"); ?>"/>
	<script src="/assets_w/jq/slick/slick.min.js?v=<?php echo date("YmdHis"); ?>"></script>
	<script>
		let $imagePath = `<?=$imagePath?>`;
		let mainBn = <?= json_encode($bannerList)?>;
		let mainBnPC = [];
		let mainBnMobile = [];
		// let mainBnType = 1;		// 1: PC,  2: 모바일
		if( mainBn.length > 0 ){
			mainBnPC = mainBn.filter((item, index)=>{ return item.display_type == 1; })			// PC arr
			mainBnMobile = mainBn.filter((item, index)=>{ return item.display_type == 2; })		// Mobile arr
		}

		$(function(){

			indexSlideCheck();

			$('.your-class_slide_pc').slick({
				slide: 'div',
				dots: true,
				arrows: false,
				centerMode: false,
				centerPadding: '0px',
				pauseOnHover : true,
				slidesToShow: 1,
				slidesToScroll: 1,
				autoplay: true,
				autoplaySpeed: 2000,
				infinite : false,
				// prevArrow: $('.prev'),
				// nextArrow: $('.next'),
				vertical : false// 세로 방향 슬라이드 옵션
			});
			
		});

		$(window).on("resize", function(){
			indexSlideCheck();
		});

		const indexSlideCheck = function(){
			let winW = $(window).outerWidth();
			if( winW <= 768 ){		mainSlidetoMobile();	// mobile
			} else {				mainSlidetoPC();		// pc
			}
		}

		const mainSlidetoPC = function(){
			let item = $(document).find(".slide_pc");

			item.each((index, item)=>{
				let src = $(item).find("img").attr("src");
				let src_pc = `${$imagePath}${mainBnPC[index].thumbnail}`;
				let src_mob = `${$imagePath}${mainBnMobile[index].thumbnail}`;

				if( src.includes(src_mob) ){
					$(item).find("img").attr("src", src_pc);
				}
			})
		}

		const mainSlidetoMobile = function(){
			let item = $(document).find(".slide_pc");

			item.each((index, item)=>{
				let src = $(item).find("img").attr("src");
				let src_pc = `${$imagePath}${mainBnPC[index].thumbnail}`;
				let src_mob = `${$imagePath}${mainBnMobile[index].thumbnail}`;

				if( src.includes(src_pc) ){
					$(item).find("img").attr("src", src_mob);
				}
			})
		}
	</script>

<div class="main_game_wrap">
	<div class="main_game_box">
		<?php if (isset($bestBetList) && count($bestBetList) > 0) : ?>
		<div class="main_game_title">

			<?php
	        	$nextGame = $bestBetList[0];
				$nextTeam1Name = isset($nextGame['p1_display_name']) && !empty($nextGame['p1_display_name']) ? $nextGame['p1_display_name'] : $nextGame['p1_team_name'];
				$nextTeam2Name = isset($nextGame['p2_display_name']) && !empty($nextGame['p2_display_name']) ? $nextGame['p2_display_name'] : $nextGame['p2_team_name'];
				$nextStartTime = date('H:i', strtotime($nextGame['start_dt']));
				$nextSportsImage = '/assets_w/images/sports/b_icon_' . $nextGame['fixture_sport_id'] . '.png';
			?>		
			
			<div class="game_title_l">
				<div class="l_next">Next&nbsp;<span>game</span></div>
				<div class="l_sports"><img src="<?= $nextSportsImage ?>" onerror="this.src='/assets_w/images/b_icon09.png'; this.onerror=null;"></div>
				<div class="l_time">잠시후&nbsp;<span class="l_time_font"><?= $nextStartTime ?></span></div>
			</div>
			<div class="game_title_r">
				<div class="r_team_1">				
					<div class="team_name"><?= $nextTeam1Name ?></div>
					<!-- <div class="team_logo"><img src="/assets_w/images/@premier_00.png"></div> -->
				</div>
				<div class="r_vs"><img src="/assets_w/images/vs.png"></div>
				<div class="r_team_2">
					<!-- <div class="team_logo"><img src="/assets_w/images/@premier_00.png"></div> -->
					<div class="team_name"><?= $nextTeam2Name ?></div>
				</div>
				<div class="r_text">의 경기가 시작됩니다!</div>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="main_game_choice">원하시는 스포츠 베팅스타일을 선택하세요!</div>
		<div class="main_game_list">
			<ul>
				<li><a class="loading__link" href="/web/realtime"><img src="/assets_w/images/main_game01.png"></a></li>
				<li><a class="loading__link" href="/web/sports"><img src="/assets_w/images/main_game02.png"></a></li>
				<li><a class="loading__link" href="/web/classic"><img src="/assets_w/images/main_game03.png"></a></li>
			</ul>
		</div>
	</div>
</div>

<div class="main_best_wrap">
	<div class="main_best_box">
		<div class="main_game_best_title">
			인기 베팅 경기를 확인해보세요!
			<span class="best_title_right">
				<span class="arrow-prev"></span><span class="arrow-next"></span>
			</span>
		</div>
		<div class="main_game_best">
			<?php foreach ($bestBetList as $bestBet): ?>
			
			<?php
	            $sportsLink = !empty($bestBet['bet_type']) && $bestBet['bet_type'] == 1 ? '/web/sports' : '/web/realtime';
				$team1Name = isset($bestBet['p1_display_name']) && !empty($bestBet['p1_display_name']) ? $bestBet['p1_display_name'] : $bestBet['p1_team_name'];
				$team2Name = isset($bestBet['p2_display_name']) && !empty($bestBet['p2_display_name']) ? $bestBet['p2_display_name'] : $bestBet['p2_team_name'];
	            $sportsImage = '/assets_w/images/sports/b_icon_' . $bestBet['fixture_sport_id'] . '.png';
			?>
			
			<div class="game_best">
				<!--<a href="<?//= $sportsLink ?>?leftSports=<?//= $bestBet['fixture_sport_id'] ?>">-->
                                <a href="<?= $sportsLink ?>?league_name=<?= $bestBet['league_display_name'] ?>">
					<div class="game_best_l">
						<img src="<?= $sportsImage ?>" onerror="this.src='/assets_w/images/b_icon09.png'; this.onerror=null;" />
					</div>
					<div class="game_best_r">
						<div class="best_date"><?= $bestBet['start_dt'] ?></div>
						<div class="best_match">
							<div class="bm_team_1">				
								<div class="bm_team_name"><?= $team1Name ?></div>
								<!-- <div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div> -->
							</div>
							<div class="bm_vs">
								<img src="/assets_w/images/vs.png">
							</div>
							<div class="bm_team_2">
								<!-- <div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div> -->
								<div class="bm_team_name"><?= $team2Name ?></div>
							</div>
						</div>
					</div>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="banner_wrap">
	<div class="banner_box">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<div class="swiper-slide"><a href="/web/casino?prd_type=C&prd_id=5" style="display:block;"><img src="/assets_w/images/banner1.png" width="100%" style="max-width:898px;"></a></div>
				<div class="swiper-slide"><a href="/web/casino?prd_type=C&prd_id=5" style="display:block;"><img src="/assets_w/images/banner1.png" width="100%" style="max-width:898px;"></a></div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="/assets_w/jq/swiper/swiper.css"/>
<script src="/assets_w/jq/swiper/swiper.js"></script>	
<script>
		var swiper = new Swiper({
			el: '.swiper-container',
			slidesPerView: 1, //레이아웃 뷰 개수 
			spaceBetween: 0,    // 슬라이드 사이 여백
			centeredSlides: true,    //센터모드
			loop : true,   // 슬라이드 반복 여부
			autoplay: true,
			loopAdditionalSlides : 1, // 슬라이드 반복 시 마지막 슬라이드에서 다음 슬라이드가 보여지지 않는 현상 수정
			slideToClickedSlide : true, // 해당 슬라이드 클릭시 슬라이드 위치로 이동
			initialSlide: 0,
			grabCursor: false,
			scrollbar: {
			  el: '.swiper-scrollbar',
			},
			mousewheel: {
			  enabled: false,
			},
			keyboard: {
			  enabled: false,
			}
		});		
</script>

<div class="main_board_wrap">
	<div class="main_board_box cf">
		<div class="board">
			<div class="board_title">실시간 출금현황</div>
			<div class="board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<?php foreach ($exchangeList as $exchange) : ?>
					<tr>
						<td style="width:30%;"><?= iconv_substr($exchange['nick_name'], 0, 2, "utf-8") . '*' ?></td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1"><?= number_format($exchange['money']) ?></span></td>
						<td style="width:30%;" align="right"><?= date('Y-m-d', strtotime($exchange['create_dt'])) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>										
			</div>
		</div>
		<div class="board">
			<div class="board_title">공지사항</div>
			<div class="board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<?php foreach ($noticeList as $notice) : ?>
					<tr>
						<td style="width:70%;">
							<a href="javascript:fnLoadingMove('/web/border')">
								<?= $notice['title'] ?>
							</a>
						</td>
						<!--<td style="width:30%" align="right"><?//= date('Y-m-d', strtotime($notice['create_dt'])) ?></td>-->
					</tr>
					<?php endforeach; ?>
				</table>					
			</div>
		</div>
		<div class="board">
			<div class="board_title">이벤트</div>
			<div class="board_box last_board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<?php foreach ($eventList as $event) : ?>
					<tr>
						<td style="width:70%;">
							<a href="/web/event_view?viewFile=event0<?=$event['idx']?>&type=1&idx=<?=$event['idx']?>">
								<?= $event['name'] ?>
							</a>
						</td>
						<!--<td style="width:30%" align="right"><?= date('Y-m-d', strtotime($event['create_dt'])) ?></td>-->
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
</div>

	<?= view('/web/common/footer_wrap') ?>
	<?= view('/web/common/popup') ?>
</body>
</html>