<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">공지사항</div>
</div>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<table class="write_box write_title_top">
				<tr class="write_tr cf">
					<td class="write_title">제목</td>
					<td class="write_basic"><input class="input1" size="115" style="width:100%"></td>
                </tr>			
				<tr class="write_tr cf">
					<td class="write_title">내용</td>
					<td class="write_basic"><textarea cols="115" rows="20" class="input2" style="width:100%"></textarea></td>
                </tr>
			</table>		
		</div>	
		<div class="con_box10">
			<div class="btn_wrap_center">
				<ul class="display_flex">
                    <li><a href="#" onClick="addBoard()"><span class="btn2_1">확인</span></a></li>                                                             
                    <li><a href="#" onclick="window.history.back();"><span class="btn2_2">취소</span></a></li>
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
<script type="text/javascript">

    $(document).ready(function () {

        // 등록
        $('.btn2_1').click(function(){

            $.ajax({
                url: '/web/border/writeDB',
                type: 'post',
                data: {
                'title': $('.input1').val(),
                'contents': $('.input2').val(),
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
        });

        // 취소
        $('.btn2_2').click(function(){
            history.back(-1);
        });

    });
    
</script>
</body>
</html>