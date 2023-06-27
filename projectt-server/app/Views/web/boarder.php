<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap"><div class="title">공지사항</div></div>

<?php

    $p_data['num_per_page'] = 10;
    $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    $p_data['start'] = ($page-1) * $p_data['num_per_page'];

    $total_page  = ceil($totalCnt/$p_data['num_per_page']);         // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($page/$p_data['page_per_block']);       // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	// 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;		// 마지막 페이지
    if ($block >= $total_block) $last_page = $total_page;

    $default_link = 'border?data=1';
?>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="list_box"> 
                    
            <!-- 공지글 -->
                <?php if($notice) : ?>
                    <?php foreach ($notice as $key => $val) {?>
                        <ul class="list_tr6" onClick="fnLoadingMove('/web/border/update?idx=<?= $val['idx'] ?>&type=v')" style="cursor: pointer;">
                            <li class="list_notice6"><span class="division3">NOTICE</span></li>
                            <li class="list_notice6"><?=$val['title']?></li>
                            <li class="list_notice6"><img src="/assets_w/images/lva.png" width="30">&nbsp;&nbsp;관리자</li>
                            <!-- <li class="list_notice4"><?=$val['create_dt']?></li> -->
                        </ul>
                    <?php }?>
                <?php else : ?>
                    <ul class="list_tr6">
                        <li class="list_notice6" style="text-align: center; width: 100%;">조회된 데이터가 없습니다.</li>
                    </ul>
	            <?php endif; ?>
                
                <!-- 일반글 -->
                <?php if($list) : ?>
                    <ul class="list_tr6 trfirst">
                        <li class="list_title1">번호</li>
                        <li class="list_title1">제목</li>
                        <li class="list_title1">닉네임</li>
                        <li class="list_title1">날짜</li>
                    </ul>
                    <?php foreach ($list as $key => $val) :?>
                        <ul class="list_tr6" style="cursor:pointer" onClick="fnLoadingMove('/web/border/update?idx=<?= $val['idx'] ?>&type=v')">
                            <li class="list1"><?= count($list) - $key ?></li>
                            <li class="list1" ><?= $val['title']?> 
                                <?= $val['is_new'] >= -3 ? '<img src="/assets_w/images/icon_new.png">' : '' ?> <?= $val['display'] == 2 ? '<img src="/assets_w/images/icon_bet.png">' : '' ?>
                            </li>
                            <li class="list1">
                                <?php
                                    $level ;
                                    if( $val['level'] == 1 ) {
                                        $level = 1 ;
                                    } else if( $val['level'] > 1 && $val['level'] < 9 ) {
                                        $level = 2 ;
                                    } else if( $val['level'] == 9 ) {
                                        $level = 'a';
                                    } else if( $val['level'] == 10 ) {
                                        $level = 5 ;
                                    } else {
                                        $level = '' ;
                                    }
                                ?>
                                <?php if( $val['nick_name'] ) { ?>
                                    <img src="/assets_w/images/lv<?= $level ?>.png" width="36">&nbsp; <?= $val['nick_name'] ? $val['nick_name'] : '' ?>
                                <?php } else { ?>
                                    &nbsp;
                                <?php } ?>
                            </li>
                            <li class="list1"><?= $val['create_dt']?></li>                        
                        </ul>
                <?php endforeach; ?>
                <?php else : ?>
                    <!-- <ul class="list_tr6">
                        <li class="list1" style="text-align: center; width: 100%;">조회된 데이터가 없습니다.</li>
                    </ul> -->
                <?php endif; ?>
			</div>
		</div>

        <div class="con_box10">
            <?php include('common/page_num.php'); ?>
        </div>

        <div class="con_box10">
            <div class="btn_wrap_right">
                <ul class="display_flex">
                    <!-- <li onClick="fnLoadingMove('/web/border/write')"><span class="btn2_1">글쓰기</span></li> -->
                    <!-- <li onClick="fnLoadingMove('/web/customer_service')"><span class="btn2_2">계좌문의</span></li> -->
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