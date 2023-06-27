<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
	<div class="title">충/환전내역</div>
</div>

<?php
        $p_data['num_per_page'] = 10;
        $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
        $p_data['start'] = ($page-1) * $p_data['num_per_page'];

        $total_page  = ceil($dataAllCount/$p_data['num_per_page']);         // 페이지 수
        $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
        $block		 = ceil($page/$p_data['page_per_block']);       // 현재 블럭
        $first_page  = ($p_data['page_per_block']*($block-1))+1;  	// 첫번째 페이지
        $last_page 	 = $p_data['page_per_block']*$block;		// 마지막 페이지
        if ($block >= $total_block) $last_page = $total_page;

        $default_link = 'charge_exchange_history?data=1&menu='.$menu.'&type='.$type;
?>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
                    <li><a href="javascript:fnLoadingMove('/web/apply?menu=c')"><span class="tab">충전하기</span></a></li>
                    <li><a href="javascript:fnLoadingMove('/web/exchange')"><span class="tab">환전하기</span></a></li>
                    <li><a href="javascript:fnLoadingMove('/web/charge_exchange_history')"><span class="tabon">충/환전내역</span></a></li> 
				</ul>
			</div>
		</div>
        <div class="con_box10">
            <div class="info_wrap">             
                <div class="info2">
                    <a href="#"><span class="btn1_2" onClick="javascript:fnLoadingMove('/web/charge_exchange_history?menu=c&type=1')">
                        <input name="select_c_e" id="charge" class="select_click" type="radio" <?= $type == '1' ? 'checked' : '' ?> value="CH">충전목록보기</span></a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="#"><span class="btn1_2" onClick="javascript:fnLoadingMove('/web/charge_exchange_history?menu=e&type=2')">
                        <input name="select_c_e" id="exchange" class="select_click" type="radio" <?= $type == '2' ? 'checked' : '' ?> value="EX">환전목록보기</span></a>
                </div>
            </div>
        </div>		
		<div class="con_box10">
            <table class="list_box">
                <tr class="list_tr3">
                    <td class="list_title3">번호</td>
                    <td class="list_title3">구분</td>
                    <td class="list_title3">신청일자</td>
                    <td class="list_title3">신청금액</td>
                    <td class="list_title3">진행결과</td>
                    <td class="list_title3">처리일자</td>
                    <td  class="list_title3">삭제</td>
                </tr>
                <?php if($dataList) {?>
                    <?php foreach ($dataList as $key => $val) {?>
                        <tr class="list_tr3">
                            <td class="list3"><span class="font03"><?= count($dataList) - $key ?></span></td>
                            <td class="list3"><span class="<?= $val['etype'] == 'CH' ? 'icon_a1' : 'icon_a2' ?>">
                                <?= $val['etype'] == 'CH' ? '충전' : '환전' ?>
                                </span>
                            </td>
                            <td class="list3"><span class="font03"><?= $val['create_dt']?></span></td>                       
                            <td class="list3"><span class="font05"><?= number_format($val['money'])?></span> 원</td>
                            <td class="list3">
                                <span class="<?= $val['status'] == 1 ? 'division2' : 'division1' ?>">
                                    <?= \App\Util\CodeUtil::memberMoneyChargeStatusToStr($val['status']) ?>
                                </span>
                            </td>

                            <td class="list3"><span class="font03"><?= $val['update_dt']?></span></td>     
                            <td class="list3"><button class="division2" onClick="delHist(<?= $val['idx']?>,'<?= $val['etype']?>')">삭제</button></td>               
                        </tr>
                    <?php }?>
                <?php } else {?>
                    <tr class="">
                        <td colspan="6" class="list3">조회된 데이터가 없습니다.</td>
                    </tr>
                <?php }?>

            </table>
		</div>
		<div class="con_box10">
            <div class="acc_btn_wrap">
                <?php include('common/page_num.php'); ?>
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

        var spans = $('.info3 > span');

        for (var i = 0; i < spans.length; ++i) {
            $(spans[i]).click(function() {
                $(this).parent().children('input.select_click')[0].checked = true;
            });
        }
    });

    function delHist(thisId,trans_type){
        $.ajax({
		type: 'get',
		dataType: 'json',
	    url: '/web/apply/delete',
	    data:{'delId':thisId,'trans_type':trans_type},
	    success: function (result) {
            var result = confirm("삭제 하시겠습니까?");
                    if (result==true) {
                        alert("삭제하였습니다")
                   location.reload();
                    return true;
                    } else {
                    return false;
                    }
             

		},
	    error: function (request, status, error) {
                        
                        //alert(error + status);
			alert('시스템 오류 입니다.');
			return;
		}
	});
    }
    
</script>
</body>
</html>