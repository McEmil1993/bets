<?php $imagePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath.'/'; ?>
<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

<div class="your-class_slide_pc">

	<!--**************** 이미지 서버 링크시 삭제 ****************-->
	<!-- <div class="class_slide_pc slide_pc1"><img src="/assets_w/images/slideshow1_pc.jpg"></div> -->
	<!-- <div class="class_slide_pc slide_pc2"><img src="/assets_w/images/slideshow2_pc.jpg"></div> -->
	<!--**************** 이미지 서버 링크시 여기까지 삭제 ****************-->

		<?php if(count($bannerList) > 0) { ?>
			<?php for($i=0; $i < count($bannerList); $i++) { ?>

				<?php if( $bannerList[$i]['display_type'] == '1' ){ ?>
				
					<div class="class_slide_pc slide_pc">
						<!-- <img src="/assets_w/images/slideshow1_pc.jpg" alt="main slide image"> -->
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

<!--**************** 이미지 서버 링크시 삭제 ****************-->
<script type="text/javascript">
$(function(){
	$(window).resize(function(){
		var width_size = window.innerWidth;
		var slide_item1 = $('.slide_pc1').find('img');
		var slide_item2 = $('.slide_pc2').find('img');
		var slide_attr1 = $('.slide_pc1').find('img').attr('src');
		var slide_attr2 = $('.slide_pc2').find('img').attr('src');
		var slide_attr_pc1 = slide_attr1.replace('_m','_pc');
		var slide_attr_pc2 = slide_attr2.replace('_m','_pc');
		var slide_attr_m1 = slide_attr1.replace('_pc','_m');
		var slide_attr_m2 = slide_attr2.replace('_pc','_m');
		if(width_size<=768){
			slide_item1.attr('src',slide_attr_m1);
			slide_item2.attr('src',slide_attr_m2);
		}else{
			slide_item1.attr('src',slide_attr_pc1);
			slide_item2.attr('src',slide_attr_pc2);
		};
	}).resize();
});
</script>
<!--**************** 이미지 서버 링크시 여기까지 삭제 ****************-->



<div class="main_live_game_wrap">
	<div class="main_live_game_box cf">
		<div class="main_live_game_title">
			<div class="main_live_game_title_l">인기 베팅 경기 목록</div>
				<?php
					$firstFixtureTime = '';
					$firstFixtureTeam1 = $firstFixtureTeam2= '';
					if(count($bestBetList) > 0){
						$firstFixtureTime = date('Y-m-d H:i', strtotime($bestBetList[0]['start_dt']));
						$firstFixtureTeam1 = $bestBetList[0]['p1_display_name'];
						$firstFixtureTeam2 = $bestBetList[0]['p2_display_name'];
					}
				?>
			<div class="main_live_game_title_r"><img src="/assets_w/images/icon_live.png">잠시 후 <span class="lt_font01"> <?=$firstFixtureTime?> </span>에 <span class="lt_font02"><?=$firstFixtureTeam1?> VS <?=$firstFixtureTeam2?></span> 경기가 시작됩니다.</div>
		</div>
		<div class="main_live_game_list">
			
			<?php foreach ($bestBetList as $key => $item): ?>
			<?php if(!empty($item['bet_type']) && 1 == $item['bet_type']){ ?>
				<?php $href = '/web/sports'; ?>
			<?php }else{ ?>
				<?php $href = '/web/realtime'; ?>
				<?php //$href = "javascript:alert('점검중입니다');";  ?>
			<?php } ?>
			<?php
				// 인기배팅 이미지 패스 설정
				/*$league_image_path = '/images/flag/' . $item['fixture_location_id'] . '.png';
				if ($item['league_image_path'] != null || $item['league_image_path'] != '') {
					$league_image_path = $imageBasePath.'/league/'.$item['league_image_path'];
				}*/
			?>
			
			<div class="main_live_game">
				<a href="<?php echo $href.'?leftSports='.$item['fixture_sport_id'] ?>">
					<div class="main_live_game_l">
						<img src="/assets_w/images/sports/icon_game<?=$item['fixture_sport_id']?>.png"><?=date('H:i', strtotime($item['start_dt']))?>
					</div>
					<div class="main_live_game_r">
						<div class="main_live_game_r_vs">
							<div class="game_r_vs"><?=isset($item['p1_display_name'])?$item['p1_display_name']:$item['p1_team_name']?></div>
							<div class="game_r_vs"><img src="/assets_w/images/vs.png"></div>
							<div class="game_r_vs"><?=isset($item['p2_display_name'])?$item['p2_display_name']:$item['p2_team_name']?></div>
						</div>
						<div class="main_live_game_r_odd">
							<div class="game_r_odd"><?=$item['win_bet_price']?></div>
							<div class="game_r_odd"><?=$item['draw_bet_price']>0?$item['draw_bet_price']:'-' ?></div>
							<div class="game_r_odd"><?=$item['lose_bet_price']?></div>
						</div>
					</div>
				</a>
			</div>
			<?php endforeach;?>
			<!-- <div class="main_live_game">
				<a href="#">
					<div class="main_live_game_l"><img src="/assets_w/images/icon03.png">00:00</div>
					<div class="main_live_game_r">
						<div class="main_live_game_r_vs">
							<div class="game_r_vs">바이에른뮌헨바이에른뮌헨</div>
							<div class="game_r_vs"><img src="/assets_w/images/vs.png"></div>
							<div class="game_r_vs">FC바르셀로나</div>
						</div>
						<div class="main_live_game_r_odd">
							<div class="game_r_odd">1.65</div>
							<div class="game_r_odd">2.65</div>
							<div class="game_r_odd">2.25</div>
						</div>
					</div>
				</a>
			</div> -->
		</div>
	</div>
</div>

<div class="main_game_wrap">
	<div class="main_game_box">
		<div class="main_game_choice">BULLS GAMES</div>
		<div class="main_game_list">
			<ul>
				<li><a href="/web/sports"><img src="/assets_w/images/main_game01.png"></a></li>
				<li><a href="/web/realtime"><img src="/assets_w/images/main_game02.png"></a></li>
				<li><a href="/web/casino?prd_type=C&prd_id=5"><img src="/assets_w/images/main_game03.png"></a></li>
				<li><a href="/web/minigame"><img src="/assets_w/images/main_game04.png"></a></li>
				<li><a href="/web/hash"><img src="/assets_w/images/main_game05.png"></a></li>
				<li><a href="/web/slot"><img src="/assets_w/images/main_game06.png"></a></li>
				<li><a href="/esports"><img src="/assets_w/images/main_game07.png"></a></li>
				<li><a href="/web/premiumShip"><img src="/assets_w/images/main_game08.png"></a></li>
			</ul>
		</div>
	</div>
</div>

<div class="main_board_wrap">
	<div class="main_board_box cf">
		<div class="board">
			<div class="board_title">공지사항 <img src="/assets_w/images/icon_plus.png"></div>
			<div class="board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<?php foreach ($noticeList as $key => $item): ?>
						<tr>
							<td class="td1"><a href="javascript:fnLoadingMove('/web/border/update?idx=<?=$item['idx']?>&type=v')"><?= $item['title']?></a></td>
							<td align="right"><span class="font16">Bulls</span></td>
						</tr>
					<?php endforeach;?>
					<tr>
						<td style="width:70%;"></td>
						<td style="width:30%" align="right"></td>
					</tr>
					<!-- <tr>
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
					</tr> -->
				</table>					
			</div>
		</div>
		<div class="board">
			<div class="board_title">이벤트 <img src="/assets_w/images/icon_plus.png"></div>
			<div class="board_box last_board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<?php foreach ($eventList as $key => $item): ?>
						<tr>
							<td style="width:70%;"><a href="/web/event_view?viewFile=event0<?=$item['idx']?>&type=1&idx=<?=$item['idx']?>"><?= $item['name']?></a></td>
							<td style="width:30%;" align="right">Bulls</td>
						</tr>
					<?php endforeach;?>
					<tr>
						<td style="width:70%;"></td>
						<td style="width:30%" align="right"></td>
					</tr>
					<!-- <tr>
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
					</tr> -->
				</table>					
			</div>
		</div>
		<div class="board">
			<div class="board_title">충전내역 <img src="/assets_w/images/icon_plus.png"></div>
			<div class="board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
				<?php foreach ($chargeList as $key => $row) {
						$name = $row['nick_name'];
						$name_len = mb_strlen($row['nick_name']);
						$sub_name1 = iconv_substr($name, 0, 2, "utf-8");
						$name = $sub_name1 . "*";
					?>
                        <tr>
                            <td style="width:30%;"><?=$name?></td>
                            <td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1"><?= number_format($row['money'])?></span> 원</td> 
                            <td style="width:30%;" align="right"><?= $row['create_dt']?></td>                                                 
                        </tr>
					<?php } ?>
					<!-- <tr>
						<td style="width:30%;">홍길*</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">홍길*</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">홍길*</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">홍길*</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr>
					<tr>
						<td style="width:30%;">홍길*</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr> -->
				</table>										
			</div>
		</div>
		<div class="board">
			<div class="board_title">환전내역 <img src="/assets_w/images/icon_plus.png"></div>
			<div class="board_box">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="myTable">
					<?php foreach ($exchangeList as $key => $item): 
						$name = $item['nick_name'];
						$name_len = mb_strlen($item['nick_name']);
						$sub_name1 = iconv_substr($name, 0, 2, "utf-8");
						$name = $sub_name1 . "*";?>
						<tr>
							<td style="width:30%;"><?=$name?></td>
							<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="font06"><?=number_format($item['money'])?></span> 원</td>
							<td style="width:30%;" align="right"><span class="font16"><?=$item['create_dt']?></span></td>
						</tr>
					<?php endforeach;?>
					<!-- <tr>
						<td style="width:30%;">홍길*</td>
						<td style="width:40%; padding:0 10px 0 0;" align="right"><span class="board_font1">100,000,000</span></td>
						<td style="width:30%;" align="right">2022-00-00</td>
					</tr> -->
				</table>										
			</div>
		</div>		
	</div>
</div>
<?php $from_date = date("Y-m-d", strtotime(date('Y-m-d')."- 8 days"))?> 
<div class="main_m_menu_wrap">
	<ul>
		<li><a href="/web/apply?menu=c"><img src="/assets_w/images/game01.png"></a></li>
		<li><a href="/web/exchange"><img src="/assets_w/images/game02.png"></a></li>
		<li><a href="/web/betting_history?menu=b&bet_group=2&clickItemNum=2&betToDate=<?php echo date("Y-m-d"); ?>&betFromDate=<?=$from_date?>"><img src="/assets_w/images/game03.png"></a></li>  
		<li><a href="/web/note"><img src="/assets_w/images/game09.png"></a></li>
		<li><a href="/web/member_info"><img src="/assets_w/images/game05.png"></a></li>
		<li><a href="/web/border"><img src="/assets_w/images/game06.png"></a></li>
		<li><a href="/web/event"><img src="/assets_w/images/game07.png"></a></li>
		<li><a href="/web/customer_service"><img src="/assets_w/images/game08.png"></a></li>
	</ul>      
</div>

	<?= view('/web/common/footer_wrap') ?>
	<?= view('/web/common/popup') ?>
</body>
</html>