<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">고객센터</div>
</div>

<?php
    $p_data['num_per_page'] = 10;
    $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    $p_data['start'] = ($page - 1) * $p_data['num_per_page'];

    $total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
    //$total_page = 0;
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($page / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지
    if ($block >= $total_block)
        $last_page = $total_page;

    $default_link = 'customer_service?data=1';
?>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="write_box write_title_top">
				<div class="write_tr cf">
					<div class="write_title">제목</div>
					<div class="write_basic"><input class="input1" size="115" style="width:100%"></div>
				</div>			
				<div class="write_tr cf">
					<div class="write_title">내용</div>
					<div class="write_basic"><textarea cols="115" rows="20" class="input2" style="width:100%"></textarea></div>
				</div>
			</div>		
		</div>	    
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul class="display_flex">
                    <li><a href="#"><span style="float:right" class="btn2_2" onClick="location.reload();">새로고침</span></a></li>
					<li><a href="#"><span class="btn3_1" onClick="addQnA()">문의하기</span></a></li>
				</ul>
			</div>
		</div>
		<div class="con_box50">
			<div class="s_title1">문의목록</div>
		</div>
		<div class="con_box10">

			<table class="table-bbs"> 
                <thead>
                    <tr>
                        <th width="130" class="hidden-768">번호</th>
                        <th>제목</th>
                        <th width="130">답변</th>
                        <th width="130" class="hidden-1024">글쓴이</th>
                        <th width="150" class="hidden-600">날짜</th>               
                    </tr>
                </thead>
                <tbody>
                    <?php if($list) {?>
                        <?php $i = 0; ?>
                        <?php foreach ($list as $key => $val) {?>
                            <?php
                                $db_m_idx = $val['idx'];
                                $num = $num_per_page * ($page - 1) + $i;
                            ?>

                            <tr>
                                <td class="hidden-768"><?= $totalCnt - $num ?></td>
                                <td class="subject">
                                    <a href="javascript:selectQnAOneToOne(<?= $db_m_idx ?>);">
                                        <?= $val['title'] ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($val['is_answer'] == 'Y') { ?>
                                        <span class="division1">답변완료</span>
                                    <?php } else { ?>
                                        <span class="division2">답변중</span>
                                    <?php } ?>
                                </td>
                                <td class="hidden-1024"><?= $val['nick_name']?></td>
                                <td class="hidden-600"><?= $val['create_dt'] ?></td>
                            </tr>
                            <?php if ($val['is_answer'] == 'Y') { 
                                $answer = nl2br(htmlspecialchars_decode($val['answer']));
                            ?>
                            <!-- 댓글 -->
                            <tr class="bbs-qna-a">
                                <td class="hidden-768"></td>
                                <td class="subject">
                                    <span><?= $answer ?></span>
                                </td>
                                <td></td>
                                <td class="hidden-1024"><span class="font_002">관리자</span></td>
                                <td class="hidden-600"><?= $val['create_dt'] ?></td>
                            </tr>
                            <?php } ?>

                            <?php ++$i; ?>
                        <?php }?>
                    <?php } else {?>
                        <tr>
                            <td colspan="10" class="no_contents">조회된 데이터가 없습니다.</td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>


            <?php if($recommendAllCount> 0){ include('common/page_num.php'); }?>
		</div>
	</div>
</div>
<!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->
 <style>
	p { line-height: 130%; }
</style>
<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript">

	// 등록
    function addQnA() {
        let title = $('.input1').val();
        let contents = $('.input2').val();
        let idx = $('#idx').val();
		
        if (title.trim(title) == '' || contents.trim(contents) == '') {
            alert('제목 또는 내용이 없습니다.');
            return;
        }

        if (idx > 0) {
            alert('이미 게시글을 로드하였습니다. 새로고침 후 작성해 주세요.');
            return;
        }

        $('#loadingCircle').show();
        $(".dimm-layer").show();
        $.ajax({
            url: '/api/customer_service/qna/add',
            type: 'post',
            data: {
                'title': title,
                'contents': contents,
            },
        }).done(function (response) {
            //alert('질문이 등록 되었습니다.');
            //location.reload();
            alert(response['messages']);
            if (200 == response['result_code']) {
                location.reload();
            } else {
                location.replace('/web');
            }
        }).fail(function (error) {
            alert(error);
        }).always(function (response) {});
    }
	
    // 선택한 사항을 가져온다.
    function selectQnAOneToOne(idx) {
        console.log(idx);
        $.ajax({
            url: '/api/customer_service/qna/selectOne',
            type: 'post',
            data: {
                'idx': idx,
            },
        }).done(function (response) {
            //alert('질문이 등록 되었습니다.');
            //console.log(response);
            $('#idx').val(response['data']['idx']);
            $('.input1').val(response['data']['title']);
            $('.input2').val(response['data']['contents']);
            //location.reload();
        }).fail(function (error) {
            alert(error.responseJSON['messages']['error']);
        }).always(function (response) {});
    }
</script>

</body>
</html>