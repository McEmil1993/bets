<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">이벤트</div>
</div>
<?php $imagePath = config(App::class)->imageUrl.'/'.config(App::class)->imagePath.'/'; ?>

<div class="contents_wrap">
	<div class="contents_box">
        <!-- <div class="con_box00">
            <div class="tab_wrap">
                <ul>
                    <li><a href="#"><span class="tabon">전체보기</span></a></li>
                    <li><a href="#"><span class="tab">공지사항</span></a></li>
                    <li><a href="#"><span class="tab">이벤트</span></a></li>                    
                </ul>
            </div> 
        </div>    	 -->
        <div class="con_box10">        
			<div class="view_box">
				<div class="view_tr">
					<div class="view1_title"><?= $item['name'] ?></div>
				</div>
				<div class="view2 cf">
					<div class="view2_l">
						<img src="<?php echo $imagePath.$item['thumbnail'] ?>">
					</div>
                    <?php
                        $db_buff = stripslashes( $item['detail'] );
                        $detail_contents = nl2br(htmlspecialchars_decode($db_buff));
                    ?>
                    <div class="view2_r">
                        <?= $detail_contents ?>
                    </div>
				</div>
			</div>
        </div>      
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul class="display_flex">
					<li><a href="#" onClick="fnLoadingMove('/web/event')"><span class="btn2_1">목록</span></a></li>
				</ul>
			</div>
		</div>	
	</div>
</div>
<!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
</body>
</html>