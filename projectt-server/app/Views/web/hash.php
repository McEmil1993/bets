<?= view('/web/common/header') ?>
<?php
use App\Util\StatusUtil;
?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap"><div class="title">해쉬게임</div></div>

<div id="hash_wide_wrap">
	<div class="contents_box">
		<div class="con_box20">
			<div class="tab_wrap">
				<ul>
					<li><a href="javascript:fnChangeOdds('https://mg-games.net:24102/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');"><span class="tabon cho_roulette">룰렛</span></a></li>
					<li><a href="javascript:fnChangeOdds('https://mg-games.net:24101/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');"><span class="tab cho_high_low">하이로우</span></a></li>
					<li><a href="javascript:fnChangeOdds('https://mg-games.net:24103/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');"><span class="tab cho_baccarat">바카라</span></a></li>
				</ul>
			</div>	<!-- tab_wrap -->
		</div>	<!-- con_box20 -->

		
		<div class="con_box20">
			<iframe class="myFrame" style="width:100%; height:90vh; border:12px solid #171a1c;" src="https://mg-games.net:24102/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>" frameborder="0" scrolling="no"></iframe>
		</div>	<!-- con_box20 -->
	</div>	<!-- contents_box -->
</div>	<!-- hash_wide_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<script>
	
	$(function(){
		$('.tab_wrap li').click(function(){
			var i = $(this).index();
			$('.tab_wrap li').find('span').removeClass('tabon').addClass('tab');
			$(this).find('span').addClass('tabon');
		});
	});


	$(document).ready(function(){
		<?php if (isset($_GET['type'])):?>
		const type = '<?= $_GET['type']?>';
		if(type == 'R') {

			// alert("R");
			$('.cho_roulette').addClass('tabon');
			$('.cho_high_low').removeClass('tabon');
			$('.cho_baccarat').removeClass('tabon');
			$('.cho_high_low').addClass('tab');
			$('.cho_baccarat').addClass('tab');
			fnChangeOdds('https://mg-games.net:24102/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');
		}
		if(type == 'H') {
			fnChangeOdds('https://mg-games.net:24101/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');
			$('.cho_high_low').addClass('tabon');
			$('.cho_roulette').removeClass('tabon');
			$('.cho_baccarat').removeClass('tabon');
			$('.cho_roulette').addClass('tab');
			$('.cho_baccarat').addClass('tab');
		}
		if(type == 'B') {
			fnChangeOdds('https://mg-games.net:24103/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');

			$('.cho_baccarat').addClass('tabon');
			$('.cho_roulette').removeClass('tabon');
			$('.cho_high_low').removeClass('tabon');
			$('.cho_roulette').addClass('tab');
			$('.cho_high_low').addClass('tab');
       
		}
		<?php endif; ?>
	});

	const fnChangeOdds = function(url) {
		$('.myFrame').attr('src', url);
	}

</script>
</body>
</html>