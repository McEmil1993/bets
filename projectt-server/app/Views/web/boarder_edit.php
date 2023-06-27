<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>
<div class="title">게시판</div>

<div id="contents_wrap">
    <div class="contents_box">
        <div class="con_box00">        
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="write_title_top">
                <input type="hidden" id="h_idx" value="<?= $list[0]['idx'] ?>">
                <tr>
                    <td class="write_title">제목</td>
                    <td class="write_basic">
                        <input class="input1" style="width:100%" value="<?= $list[0]['title'] ?>">
                    </td>
                </tr>
                <tr>
                    <td class="write_title">내용</td>
                    <td class="write_basic">
                        <textarea rows="15" class="input2" style="width:100%"><?= $list[0]['contents'] ?></textarea>
                    </td>
                </tr>
            </table>
        </div>    
        <div class="con_box10">
            <div class="btn_wrap_center">
                <ul>
                    <li><span class="btn2_1">수정</span></li>
                    <li><span class="btn2_2">취소</span></li>
                </ul>
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

        // 수정
        $('.btn2_1').click(function(){

            $.ajax({
                url: '/web/border/updateDB',
                type: 'post',
                data: {
                    'idx': $('#h_idx').val(),
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