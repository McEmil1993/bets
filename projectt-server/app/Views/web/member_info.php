<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">마이페이지</div>
</div>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
					<li><a href="javascript:fnLoadingMove('/web/member_info')"><span class="tabon">내정보</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/change_password')"><span class="tab">비밀번호변경</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/recommend_member')"><span class="tab">추천회원리스트</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/point_history')"><span class="tab">포인트내역</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/note')"><span class="tab">쪽지함</span></a></li>
				</ul>
			</div>
		</div>
		<div class="con_box10">
			<div class="write_box write_title_top">			
				<div class="write_tr cf">
					<div class="write_title">아이디</div>
					<div class="write_basic">
                    <?php
                            $level ;
                            if( $member->level >= 1 && $member->level <= 3) {
                                $level = 1 ;
                            } else if( $member->level > 3 && $member->level < 6 ) {
                                $level = 4 ;
                            } else if( $member->level > 5 && $member->level < 8 ) {
                                $level = 1 ;
                            } else if( $member->level >= 8 ) {
                                $level = 'a';
                            } else {
                                $level = '' ;
                            }
                        ?>                        
                        <img src="/assets_w/images/lv<?= $level ?>.png" width="26">&nbsp; <span class="font13"><?= $member->id ?></span></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">닉네임</div>
					<div class="write_basic"><span class="font07"><?= $member->nick_name ?></span></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">은행명</div>
					<div class="write_basic"><?= $member->account_bank ?></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">계좌번호</div>
					<div class="write_basic"><?= preg_replace('/(?<=.{2})./u','*',$member->account_number)?></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">예금주</div>
					<div class="write_basic">
                        <?php $accountName;
                        if(mb_strlen($member->account_name, 'utf-8') == 2) {
                            $accountName = preg_replace('/(?<=.{1})./u','*',$member->account_name);
                        }else {
                            $accountName = preg_replace('/.(?=.$)/u','*',$member->account_name);                	
                        }?>
                        <?= $accountName ?>
                    </div>
				</div>			
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