<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">마이페이지</div>
</div>

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
      
        $default_link = 'recommend_member?data=1';
    ?>
<?php
    $recommentChargeList = array();
    if(count($recommentChargeInfo) > 0){
        foreach ($recommentChargeInfo as $key => $value) {
            $recommentChargeList[$value['member_idx']] = $value;
        }
    }
?>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
					<li><a href="javascript:fnLoadingMove('/web/member_info')"><span class="tab">내정보</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/change_password')"><span class="tab">비밀번호변경</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/recommend_member')"><span class="tabon">추천회원리스트</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/point_history')"><span class="tab">포인트내역</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/note')"><span class="tab">쪽지함</span></a></li>
				</ul>
			</div>
		</div>
		<div class="con_box10">
            <div class="info_wrap">             
                <div class="info1">
                    추천 회원수&nbsp; <span class="font14"><?= $recommendAllCount ?></span> 명
                </div>
            </div>
        </div>		
		<div class="con_box10">
            <table class="list_box">
                <tr class="list_tr2">
                    <td width="10%" class="list_title2">번호</td>
                    <td width="10%" class="list_title2">아이디</td>
                    <td width="10%" class="list_title2">닉네임</td>
                    <td width="10%" class="list_title2">가입일시</td>
                </tr>
                <?php if($recommendList) {?>
                    <?php foreach ($recommendList as $key => $val) {?>
                        <tr class="list_tr2">
                            <td class="list2"><?= $val['no']?></td>
                            <td class="list2"><?= $val['id']?></span></td>                       
                            <td class="list2"><?= $val['nick_name']?></span></td>               
                            <td class="list2"><?= $val['reg_time']?></span></td>                    
                        </tr>
                    <?php }?>
                <?php } else {?>
                    <tr>
                        <td colspan="4" class="list2" style="text-align: center;">조회된 데이터가 없습니다.</td>
                    </tr>
                <?php }?>                                           
            </table>             
            <?php if($recommendAllCount> 0){ include('common/page_num.php'); }?>
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