<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">공지사항</div>
</div>

<div class="contents_wrap">
    <div class="contents_box">
        <div class="con_box10">
			<div class="list_box">
                <ul class="list_tr4">
                    <li class="list_notice4 num"><span class="division3">NOTICE</span></li>
                    <li class="list_notice4"><?= $list[0]['title'] ?></li>
                    <li class="list_notice4"><span class="view1_po">글쓴이</span>관리자</li>
                    <li class="list_notice4"><span class="view1_po">작성일</span><?= $list[0]['create_dt'] ?></li>
                </ul>
                <?php
                    $db_buff = stripslashes( $list[0]['contents'] );
                    $db_content = nl2br(htmlspecialchars_decode($db_buff));
                    $pdf_attachment = stripslashes( $list[0]['pdf_attachment'] );
                ?>
                <div class="view2">
                    <p><?=$db_content?></p>
                    <?php if($pdf_attachment): ?>
                    <p><iframe src="<?=$pdf_attachment?>#toolbar=0&navpanes=0" width="100%" height="800px"></iframe></p>
                    <?php endif ?>
                </div>
            </div>
                <?php 
				if ($list[0]['member_idx'] > 0) {
				?>
                <?php foreach ($comment as $k => $v):?>
                <div class="list_box">
                    <ul class="list_tr4">
                        <li class="list_notice4 num"><span class="view1_po"><?= $list[0]['idx'] ?></span></li>
                        <li class="list_notice4"><?=$v['comment']?></li>
                        <li class="list_notice4"><span class="view1_po">글쓴이</span><?=$v['nick_name']?></li>
                        <li class="list_notice4"><span class="view1_po">작성일</span><?= $list[0]['create_dt'] ?></li>
                    </ul>
                </div>
                <?php endforeach;?>
                <div class="list_box">
                    <ul class="list_tr4">
                        <li class="list_notice4 num">댓글</li>
                        <li class="list_notice4"><textarea rows="2" class="input2" style="width:88%" id="comment_input" name="comment_input"></textarea></li>
                        <li class="list_notice4"><span class="btn2_2" onclick="registComment(<?=$list[0]['idx']?>, <?=$member_idx?>, '<?=$member_nickname?>');" style="cursor:pointer">등록</span></li>
                    </ul>
                </div>
                <?php } ?>
            </div>
            <div class="con_box10">
            <div class="btn_wrap_center">
                <ul class="display_flex">
                    <li><span class="btn2_1 list" style="cursor:pointer">목록</span></a></li>
                    <?php if ( session()->get('member_idx') == $list[0]['member_idx'] ) { ?>
                    <li><span class="btn2_1" style="cursor:pointer" onClick="fnLoadingMove('/mobile/border/update?idx=<?= $list[0]['idx'] ?>&type=e')">수정</span></a></li>
                    <li><span class="btn2_2 delBoard" style="cursor:pointer">삭제</span></a></li>
                    <?php }?>
                </ul>
            </div>
        </div>  
        </div>
               
    </div>
</div><!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript">

    $(document).ready(function () {

        // 목록
        $('.list').click(function(){
            history.back(-1);
        });

        // 삭제
        $('.delBoard').click(function(){

        	var str_msg = '삭제 하시겠습니까?';
        	var result = confirm(str_msg);
        	if (result){   
	            $.ajax({
	                url: '/web/border/deleteDB',
	                type: 'post',
	                data: {
	                'idx': $('#h_idx').val(),
	                },
	            }).done(function (response) {
	                console.log('response', response);
	                if( response.result_code == 200) {
	                    alert(response.messages);
	                    fnLoadingMove('/web/border');
	                }
	            }).fail(function (error) {
	                //alert(error.responseJSON['messages']['messages']);
	                alert(error.responseJSON['messages']['error']);
	            }).always(function (response) {});
        	}
        });
    });

    function registComment(idx, member_idx, nickname) {
        var comment = $('#comment_input').val();
        if (!comment) {
            alert('댓글을 입력해주세요.');
            return;
        }
        
        var str_msg = '댓글을 등록 하시겠습니까?';
        var result = confirm(str_msg);
        if (result) {
            $('#loadingCircle').show();
            $.ajax({
                url: '/web/border/registComment',
                type: 'post',
                data:{'idx': idx, 'member_idx': member_idx, 'nickname': nickname, 'comment': comment },
            }).done(function (response) {
                alert('등록 되었습니다.');
                location.reload();

            }).fail(function (error) {
                console.log(error);
                alert(error);
            }).always(function (response) {});
        }
    }
</script>
</body>
</html>