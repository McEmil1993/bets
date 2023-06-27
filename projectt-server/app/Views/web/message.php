<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">마이페이지</div>
</div>

<?php
    $notReadMessage = 0;
    foreach ($messageList as $key => $item) {
        if ($item['read_yn'] == 'N')
            $notReadMessage += 1;
    }
?>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
					<li><a href="javascript:fnLoadingMove('/web/member_info')"><span class="tab">내정보</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/change_password')"><span class="tab">비밀번호변경</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/recommend_member')"><span class="tab">추천회원리스트</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/point_history')"><span class="tab">포인트내역</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/note')"><span class="tabon">쪽지함</span></a></li>
				</ul>
			</div>
		</div>
        <div class="con_box10">
            <div class="info_wrap">             
                <div class="info1">
                    <a href="#"><span class="btn1_1 all_read">전체확인하기</span></a> <a href="#"><span class="btn1_2 btn2">전체삭제</span></a> <span class="point_tip">쪽지 보관 기간은 <span class="font06">최대 3일</span>까지 입니다</span>
                </div>
            </div>
        </div>	
		<div class="con_box10">
            <table class="list_box pc_table">
                <tr class="list_tr trfirst">
                    <td width="10%" class="list_title1">보낸이</td>
                    <td width="10%" class="list_title1">제목</td>
                    <td width="10%" class="list_title1">발신일시</td>
                    <td width="10%" class="list_title1">확인일시</td>
                    <td width="10%" class="list_title1">삭제</td>
                </tr>
                <?php foreach ($messageList as $key => $item) : ?>
                    <?php
                        $db_buff = stripslashes($item['content']);
                        $db_content = nl2br(htmlspecialchars_decode($db_buff));
                    ?>
                    <tr class="list_tr" id="tr_<?= $item['idx'] ?>" onclick="readMsg(<?= $item['idx'] ?>, '<?= $item['read_yn'] ?>')">
                        
                        <td class="list1 " id="mb_sender" style="padding: 0 0 0 0;text-align: center;">
                            <?php
                                if (isset($item['nick_name'])) {
                                    echo $item['nick_name'];
                                } else {
                                    echo '관리자';
                                }
                            ?>
                        </td>    
                        <td class="list1">
                        <?= $item['title']?>                                                                                     
                        </td>
                        <td class="list1" id="reg_time_"><?= $item['reg_time'] ?></td>
                        <td class="list1" id="read_time_<?= $item['idx'] ?>" ><?= $item['read_time'] ?></td>
                        <?php if ($item['read_yn'] == 'Y') { ?>
                            <td id="del_btn_<?= $item['idx'] ?>" width="5%" class="list1" onclick="delMsg(<?= $item['idx'] ?>)" >
                                <span class="btn1_2">삭제</span></a>
                            </td>
                        <?php } else { 
                            echo '<td id="del_btn_' . $item['idx'] . '" width="5%" class="list1"></td>';
                        }?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="5">
                            <div class="listview_note detail_content"><?= $db_content ?></div>
                        </td>
                    </tr>

                <?php endforeach; ?>
                <?php if(!$messageList) {?>
                    <tr>
                        <td colspan="5" class="list2">조회된 데이터가 없습니다.</td>
                    </tr>
                <?php }?>                                           
            </table>  
            <?php foreach ($messageList as $key => $item) : ?>
                <?php 
                    $db_buff = stripslashes($item['content']);
                    $db_content = nl2br(htmlspecialchars_decode($db_buff));
                ?>
            <div class="memo_mobile tr_<?= $item['idx'] ?>"  onclick="readMsg(<?= $item['idx'] ?>, '<?= $item['read_yn'] ?>')">
                    <h4 class="sender_name">
                         <?php
                                if (isset($item['nick_name'])) {
                                    echo $item['nick_name'];
                                } else {
                                    echo '관리자';
                                }
                            ?>
                    </h4>
                    <span class="btn_del" style="position: absolute;right: 0px;top: 16px;" id="del_btn_<?= $item['idx'] ?>"   onclick="delMsg(<?= $item['idx'] ?>)">삭제</span></a>

                    <p class="font06">  <?= $item['title']?></p>
                    <p style="color: #999;">수신:<?= $item['reg_time'] ?>&nbsp;&nbsp; 확인:<?= $item['read_time'] ?></p>
                 
                    <div class="clickex_<?= $item['idx'] ?>" data-key="0" data-values="<?= $db_content ?>" style="display:none;">
                
            </div>
                    
            </div>
           
                    
            
            <?php endforeach; ?>

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

    $(document).ready(function () {
        let delete_idx = 0;
        // 상세 기본 감추기
        $('.detail_content').hide();

        dd = true;
        $('.con_box10 tr').click(function(){
            if(dd){
                $(this).next().find('div').slideDown();
                dd=false;
            }else{
                $(this).next().find('div').slideUp();
                dd=true;
            };
        });

        // 쪽지 전체삭제
        $('.btn2').on('click', function () {
            let mes = '모든 쪽지가 삭제됩니다. 삭제하시겠습니까?'
            if (confirm(mes) == false) {
                return;
            }

            $.ajax({
                url: '/web/betting_history/deleteAllMessage',
                type: 'post',
                data: {
                    'idx': delete_idx,
                },
            }).done(function (response) {
                alert("쪽지가 전부 삭제되었습니다.");
         
                location.replace('/web/note');
            }).fail(function (error) {
                alert(error.responseJSON['messages']['error']);
            });
        });

        // 쪽지 전체확인
        $('.all_read').on('click', function () {
            $.ajax({
                url: '/web/betting_history/allReadMessage',
                type: 'post',
            }).done(function (response) {
                alert("쪽지 확인 됐습니다.");
                <?= session()->set('tm_unread_cnt', 0) ?>
                location.replace('/web/note');
            }).fail(function (error) {
                alert(error.responseJSON['messages']['error']);
            });
        });

    });

    // 쪽지 클릭
    function readMsg(message_idx, read_yn) {
        console.log('readMsg', message_idx, read_yn);
        
        // $('#tr_' + message_idx).attr("onclick", "readMsg(" + message_idx + ", 'Y')");
        
        // $('.clickex_'+ message_idx).attr('style','display:block');
        var key = $('.clickex_'+ message_idx).attr('data-key');

        if(key == "0"){
            $('.clickex_'+ message_idx).attr('style','display:block;overflow-y: auto;height: 200px;background:#212121;padding: 25px 10px 25px 10px;border-bottom: solid 1px #2e3032;line-height: 30px;border-bottom: solid 1px #2e3032;transition: all 1s ease-in-out');
            $('.clickex_'+ message_idx).attr('data-key','1');
            $('.clickex_'+ message_idx).html('<table width="100%" cellpadding="0" cellspacing="0" class="meno_table_in"><tr><td>'+ $('.clickex_'+ message_idx).attr('data-values') +'</td></tr></table>');

        }else{
            $('.clickex_'+ message_idx).attr('style','display:none');
            $('.clickex_'+ message_idx).attr('data-key','0');
        }

        

        
        if ("Y" == read_yn) return;

        $.ajax({
            url: '/api/message/read',
            type: 'post',
            data: {
                'message_idx': message_idx,
            },
        }).done(function (response) {
            console.log(response['data']['time']+'12');
            $('#read_time_' + message_idx).text(response['data']['time']);
            $('#del_btn_' + message_idx).html('<span class="btn1_2">삭제</span>');
            $('#del_btn_' + message_idx).attr("onclick", "delMsg(" + message_idx + ")");
            $('#tr_' + message_idx).attr("onclick", "readMsg(" + message_idx + ", 'Y')");
        }).fail(function (error, response, p) {
            console.log(response['error']);

        });
    }

    // 쪽지 하나 삭제
    function delMsg(message_idx) {
        del_click = true;

        let mes = '삭제하시겠습니까?'
        if (confirm(mes) == false) {
            return;
        }

        $.ajax({
            url: '/web/betting_history/deleteMessage',
            type: 'post',
            data: {
                'idx': message_idx,
            },
        }).done(function (response) {
            alert("쪽지가 삭제되었습니다.");
            location.replace('/web/note');
        }).fail(function (error) {
            alert(error.responseJSON['messages']['error']);
        });
    }

</script>
</body>
</html>