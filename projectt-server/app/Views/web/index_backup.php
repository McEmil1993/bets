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
		<div class="main_game_title">
			<div class="game_title_l">
				<div class="l_next">Next&nbsp;<span>game</span></div>
				<div class="l_sports"><img src="/assets_w/images/b_icon02.png"></div>
				<div class="l_time">잠시후&nbsp;<span class="l_time_font">00:00</span></div>
			</div>
			<div class="game_title_r">
				<div class="r_team_1">				
					<div class="team_name">맨체스터 시티</div>
					<div class="team_logo"><img src="/assets_w/images/@premier_00.png"></div>
				</div>
				<div class="r_vs"><img src="/assets_w/images/vs.png"></div>
				<div class="r_team_2">
					<div class="team_logo"><img src="/assets_w/images/@premier_00.png"></div>
					<div class="team_name">맨체스터 유나이티드</div>
				</div>
				<div class="r_text">의 경기가 시작됩니다!</div>
			</div>
		</div>
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
			<div class="game_best">
				<div class="game_best_l"><img src="/assets_w/images/b_icon02.png"></div>
				<div class="game_best_r">
					<div class="best_date">2022-00-00 00:00</div>
					<div class="best_match">
						<div class="bm_team_1">				
							<div class="bm_team_name">팀명이 길어집니다 길어집니다</div>
							<div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div>
						</div>
						<div class="bm_vs"><img src="/assets_w/images/vs.png"></div>
						<div class="bm_team_2">
							<div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div>
							<div class="bm_team_name">레알마드리드</div>
						</div>
					</div>
				</div>
			</div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon03.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon04.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon04.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon05.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon06.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon07.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon08.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
			<div class="game_best"><a href="#"><div class="game_best_l"><img src="/assets_w/images/b_icon09.png"></div><div class="game_best_r"><div class="best_date">2022-00-00 00:00</div><div class="best_match"><div class="bm_team_1"><div class="bm_team_name">레알마드리드</div><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div></div><div class="bm_vs"><img src="/assets_w//assets_w/images/vs.png"></div><div class="bm_team_2"><div class="bm_team_logo"><img src="/assets_w/images/@premier_00.png"></div><div class="bm_team_name">레알마드리드</div></div></div></div></a></div>
		</div>
	</div>
</div>

<div class="banner_wrap">
	<div class="banner_box">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<div class="swiper-slide"><a href="#" style="display:block;"><img src="/assets_w/images/banner1.png" width="100%" style="max-width:898px;"></a></div>
				<div class="swiper-slide"><a href="#" style="display:block;"><img src="/assets_w/images/banner1.png" width="100%" style="max-width:898px;"></a></div>
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
					<tr>
						<td style="width:30%;">ABCDEFG***</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">ABCDEFG***</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">ABCDEFG***</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">ABCDEFG***</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">ABCDEFG***</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">ABCDEFG***</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
				</table>										
			</div>
		</div>
		<div class="board">
			<div class="board_title">공지사항</div>
			<div class="board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
				</table>					
			</div>
		</div>
		<div class="board">
			<div class="board_title">이벤트</div>
			<div class="board_box last_board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:70%;">제목에 오신것을오신것을오신것을오신것을 진심으로 환영합니다. 맘보에서는...</td>
						<td style="width:30%" align="right">2022-00-00</td>
					</tr>
				</table>					
			</div>
		</div>
	</div>
</div>



	<?= view('/web/common/footer_wrap') ?>
	<?= view('/web/common/popup') ?>
</body>
</html>