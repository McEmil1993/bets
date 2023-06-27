<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">게시판</div>
</div>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<table class="write_box write_title_top">
                <input type="hidden" id="binaryData" name="binaryData" value="<?=$binary_data ?>" >
                <img src="<?=$binary_data ?>" style="margin-bottom: 1.5%;">
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
					<li><a href="#" onClick="addBoard()"><span class="btn2_1">획인</span></a></li>
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
    function addBoard(){
		
	    $('#loadingCircle').show();

        $.ajax({
            url: '/web/border/writeAddImageDB',
            type: 'post',
            dataType : "text",
            data: {
                'title': $('.input1').val(),
                'contents': $('.input2').val(),
                'binaryData': $('#binaryData').val().replace("data:image/png;base64,", "")
            },
        }).done(function (response) {
        	$('#loadingCircle').hide();

			if(response == 200) {
				alert("등록 성공");
				location.replace('/web/border');
			}else if (response == 411) {
				alert("제목 또는 내용을 입력해 주세요.");
			}else if (response == 412) {
				alert("회원정보가 없는 계정입니다.");
			}else if (response == 413) {
				alert("사용이 불가능 한 계정으로 관리자에게 문의바랍니다.");
			}else if (response == 414) {
				alert("가입승인 이 후 이용이 가능합니다..");
			}else {
				alert("등록 실패 관리자 문의.");
			}
        	
			
            //alert('등록 되었습니다.');
            //location.replace('/web/border');
			
			
            /* if (200 == response['result_code']) {
               location.replace('/web/border');
            } else{
               location.replace('/web');
            } */
            
        }).fail(function (error) {
            alert(error);
        }).always(function (response) {});
    }

</script>
</body>
</html>