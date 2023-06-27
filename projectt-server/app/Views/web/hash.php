<?= view('/web/common/header') ?>
<?php
use App\Util\StatusUtil;
?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>
<div class="title_wrap">
	<div class="title">해쉬게임</div>
</div>
<div id="hash_wide_wrap">
    <div class="mini_wide_left">
        <div class="mini_left_list_wrap">
        	<div class="mini_left_list">
            	<ul>
                	<a href="javascript:fnChangeOdds('https://mg-games.net:21102/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');">
						<li style="border-bottom: 1px solid #2c2c2c;"><img src="/assets_w/images/icon_mini05.png" width="18">&nbsp;  룰렛</li>
					</a>
                	<a href="javascript:fnChangeOdds('https://mg-games.net:21101/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');">
						<li style="border-bottom: 1px solid #2c2c2c;"><img src="/assets_w/images/icon_mini06.png" width="18">&nbsp;  하이로우</li>
					</a>                    
                	<a href="javascript:fnChangeOdds('https://mg-games.net:21103/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>');">
						<li style="border-bottom: 1px solid #2c2c2c;"><img src="/assets_w/images/icon_mini04.png" width="18">&nbsp;  바카라</li>
					</a>                                                            
                </ul>
            </div>
        </div>
    </div><!-- mini_wide_left -->
    
    <div class="mini_wide_center">
		<iframe class="myFrame" style="width:100%;height:90vh;" src="https://mg-games.net:21102/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>" frameborder="0" scrolling="no"></iframe>
    </div><!-- mini_wide_center -->
</div>
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<script>
$(document).ready(function(){
	<?php if (isset($_REQUEST['type'])):?>
	const type = $('#type').val();
	if(type == 'R') {
		fnChangeOdds('https://mg-games.net:21102/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>','tab1');
	}
	if(type == 'H') {
		fnChangeOdds('https://mg-games.net:21101/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>','tab2');
	}
	if(type == 'B') {
		fnChangeOdds('https://mg-games.net:21103/?id=<?= session()->get('id') ?>&code=<?= session()->get('member_idx') ?>','tab3');
	}
	<?php endif; ?>
});


    const fnChangeOdds = function(url) {
        $('.myFrame').attr('src', url);
    }
</script>
</body>
</html>