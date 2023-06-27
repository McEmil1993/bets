<?php $imagePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath.'/'; ?>
<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">이벤트</div>
</div>

<div id="contents_wrap">
	<div class="container event__wrap">

        <!-- <div class="con_box00">
            <div class="tab_wrap">
                <ul>
                    <li><a href="#"><span class="tabon">전체보기</span></a></li>
                    <li><a href="#"><span class="tab">공지사항</span></a></li>
                    <li><a href="#"><span class="tab">이벤트</span></a></li>                    
                </ul>
            </div> 
        </div>    	 -->
		<!-- <div class="con_box10">
			<div class="event_wrap"> -->

            <div class="list__event">
                <ul>
                    <?php if($list) {?>
                        
                        <?php foreach ($list as $key => $val) {?>
                            <li>
                                <a href="/web/event_view?idx=<?= $val['idx'] ?>&type=1">
                                    <span><img src="<?php echo $imagePath.$val['thumbnail'] ?>"></span>
                                </a>
                            </li> 
                        <?php }?>

                    <?php } else {?>
                        <li class="no_contents">조회된 데이터가 없습니다.</li>
                    <?php }?>
                </ul>
            </div><!-- .list__event -->



			<!-- </div>
		</div>	 -->
	</div>
</div>

<?= view('/web/common/footer_wrap') ?>
</body>
</html>