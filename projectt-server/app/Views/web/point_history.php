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
    
    $default_link = 'point_history?data=1';
?>

<div class="contents_wrap">
	<div class="contents_box">
		<div class="con_box00">
			<div class="tab_wrap">
				<ul>
					<li><a href="javascript:fnLoadingMove('/web/member_info')"><span class="tab">내정보</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/change_password')"><span class="tab">비밀번호변경</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/recommend_member')"><span class="tab">추천회원리스트</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/point_history')"><span class="tabon">포인트내역</span></a></li>
					<li><a href="javascript:fnLoadingMove('/web/note')"><span class="tab">쪽지함</span></a></li>
				</ul>
			</div>
		</div>
		<div class="con_box10">
            <div class="info_wrap">             
                <div class="info1">
                    보유 포인트&nbsp; <span class="font14"><?= number_format(session()->get('point')) ?></span>  P <span class="point_tip">포인트내역 보관 기간은 <span class="font06">최대 7일</span>까지 입니다</span>
                </div>
            </div>
        </div>		
		<div class="con_box10">
            <table class="list_box">
                <tr class="list_tr2">
                    <td width="10%" class="list_title2">번호</td>
                    <td width="10%" class="list_title2">지급일</td>
                    <td width="10%" class="list_title2">지급포인트</td>
                    <td width="10%" class="list_title2">비고</td>
                </tr>
                <?php if($pointList) {?>
                    <?php foreach ($pointList as $key => $val) {
                    	$db_ac_code_str = "";
                    	/*1:충전,2:환전,3:베팅,4:베팅취소,5:포인트전환(추가),6:포인트차감,7:베팅결과처리,8:이벤트충전,9:이벤트차감,10:포인트충전,11 : 낙첨 포인트 지급,101:충전요청,102:환전요청,103:계좌조회,\\\\\\\\\\\\\\\\n             * 111:충전요청취소,112:환전요청취소,113:충전취소,114:환전취소,121:관리자충전,122:관리자회수,123:관리자 포인트 충전, 124:관리자 포인트 회수,125 : 관리자 환전 회수,998:데이터복구,999:기타 \\\\\\\\\\\\\\\\n     \\\\n201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소  ,126 : 재정산으로 인한 정산 ->적특시 이미 지급된 포인트 관리자 회수\n,127 : 재정산으로 인한 적특 ->정산 시 지급으로 인한 관리자포인트 충전  */
                    	$pointColor;
                    	$pointPlusFont = "font05";
                    	$pointMinusFont = "font01";
                    	
                    	switch ($val['ac_code']) {
                    		case 5: $db_ac_code_str = "포인트전환";$pointColor = $pointMinusFont;break;
                    		case 6: $db_ac_code_str = "포인트차감";$pointColor = $pointMinusFont;break;
                    		case 8: $db_ac_code_str = "이벤트충전";$pointColor = $pointPlusFont;break;
                    		case 9: $db_ac_code_str = "이벤트차감";$pointColor = $pointMinusFont;break;
                    		case 9: $db_ac_code_str = "이벤트차감";$pointColor = $pointMinusFont;break;
                    		case 10: $db_ac_code_str = "포인트충전";$pointColor = $pointPlusFont;break;
                    		case 11: $db_ac_code_str = "낙첨포인트지급";$pointColor = $pointPlusFont;break;
                    		case 123: $db_ac_code_str = $val['coment'];$pointColor = $pointPlusFont;break;
                    		case 124: $db_ac_code_str = $val['coment'];$pointColor = $pointMinusFont;break;
                    		case 126: $db_ac_code_str = "재정산으로 인한 정산 ->적특시 이미 지급된 포인트 관리자 회수";$pointColor = $pointMinusFont;break;
                    		case 127: $db_ac_code_str = "재정산으로 인한 적특 ->정산 시 지급으로 인한 관리자포인트 충전";$pointColor = $pointPlusFont;break;
                    		case 202: $db_ac_code_str = "정산포인트취소";$pointColor = $pointMinusFont;break;
                    		case 203: $db_ac_code_str = "정산추천인포인트취소";$pointColor = $pointMinusFont;break;
                    	}
                    	
                    ?>
                        <tr class="list_tr2">
                            <td class="list2"><?= $val['no']?></td>
                            <td class="list2"><?= $val['reg_time']?></td>                       
                            <td class="list2"><span class="font05"><?= $val['r_money'] == 0 ? number_format($val['point']) : number_format($val['r_money']) ?></span></td>               
                            <td class="list2"><?= $db_ac_code_str ?></span></td>                    
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
<script>
//포인트 머니 전환
function pointToMoney(point) {
    var message = '포인트를 머니로 전환하시겠습니까?';
    if (!confirm(message)) {
        return false;
    }

    if (point < 1000) {
        alert('포인트 전환의 경우 최소 1000원 이상 전환이 가능합니다.');
        return false;
    }

    $.ajax({
        url: "/member/pointToMoney",
        data: {},
        method: 'POST'
    }).done(function (response) {
        $('.util_money').text(setComma(response['data']['money']));
        $('.util_point').text(0);
        
        let href = 'javascript:pointToMoney(0)';
        $('#a_point').prop('href', href);
        alert(setComma(response['data']['bePoint'])+'원이 보유머니로 전환되었습니다.');
        location.reload();
    }).fail(function (error) {
        alert(error.responseJSON['messages']['messages']);
    });
}
</script>
</html>