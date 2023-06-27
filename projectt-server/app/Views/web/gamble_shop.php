<?= view('/web/common/header') ?>
<div id="wrap">
<?= view('/web/common/header_wrap') ?>
<div class="title">GOLD SHOP</div>

<div id="contents_wrap">
    <div class="contents_box"> 
        <div class="con_box00">
            <div class="g_info_wrap">
                <div class="g_info1">겜블 골드템 규정</div>              
                <div class="g_info3">
                    <span class="g_info_num">1</span>&nbsp;  골드템 포인트는 <span class="font10">충전금액의 0.001%</span>가 자동 적립됩니다.<br>
                    <span class="g_info_num">2</span>&nbsp;  골드템포인트로 골드템 상점에서 아이템을 구매 후 스포츠 배팅에 대해서만 사용이 가능합니다.<br>
                    <span class="g_info_num">3</span>&nbsp;  골드템은 스포츠 배팅전 사용이 가능하며, <span class="font10">배팅이 진행중이거나 마감된 배팅에 대해서는 적용이 불가</span>합니다.<br>
                    <span class="g_info_num">4</span>&nbsp;  골드템은 스포츠 배팅을 주로 이용해주시는 회원분들께 지원되는 이벤트로 인하여, 가입이후 <span class="font10">카지노, 미니게임만 이용하셨을경우 사용이 불가함</span>을 알려드립니다.<br>
                    <span class="g_info_num">5</span>&nbsp;  시스템에 의하여 스포츠+카지노 / 스포츠 + 미니게임의 이용비율에서 <span class="font10">스포츠 비율이 낮을경우 배팅취소 및 골드템 회수처리</span>가 진행되오니 해당내용 참고하여주시기 바랍니다.<br>
					<span class="g_info_num">※</span>&nbsp;  <span class="font10">주로 이용하시는 배팅이 " 카지노/미니게임 " 이신 회원분께서는 골드템 사용전 반드시 고객센터를 통해 골드템 사용 가능 여부에 대한 문의 후 이용해주시기 바랍니다.</span><br>
					<span class="g_info_num">※</span>&nbsp;  <span class="font10">해당문의를 진행하지 않고 이용하실 경우 위에 기재된 내용에 의하여 취소처리 될 수 있습니다.</span><br>
                </div>
            </div>
        </div>
        <div class="con_box10">
            <div class="g_info_wrap">
                <div class="g_info1">GOLD SHOP 이용안내</div>              
                <div class="g_info3">
                    <span class="g_info_num">1</span>&nbsp;  골드템은 적립 포인트로만 구매가 가능하며, 보유머니로는 구매가 불가합니다.<br>
                    <span class="g_info_num">2</span>&nbsp;  골드템 구매시 아이템의 가격만큼 마일리지가 차감됩니다.<br>
					<span class="g_info_num">3</span>&nbsp;  구매한 골드템은 환불이 불가능하며, 1경기에만 골드템이 적용됩니다.<br>
                    <span class="g_info_num">4</span>&nbsp;  골드템은 인플레이 배팅은 사용이 불가하며, 오직 스포츠 페이지에서만 사용이 가능합니다.<br>
					<span class="g_info_num">5</span>&nbsp;  골드템 사용시 당첨/미당첨 결과에 관계없이 사용하신 골드템은 소멸됩니다.<br>
                    <span class="g_info_num">6</span>&nbsp;  사용하신 골드템은 배팅내역에서 확인이 가능하며, 골드템 사용은 배팅카트에서 적용이 가능합니다.<br>
                </div>
            </div>
        </div>
		<div class="con_box10">
            <div class="g_info_wrap">
                <div class="g_info1">GOLD ITEM LIST</div> 
				<div class="g_info2" style="margin-top:15px">★ 배당패치 ★ (배당UP 골드템)</div>
				<div class="g_info3"><span class="font10">선택하신 경기의 배당을 사용하신 아이템의 수치만큼 상승시킵니다.</span></div> 
				<div class="g_info3">
                    <span class="g_info_num">1</span>&nbsp;  배당률 5% 증가 : <span class="font10">10,000 G</span><br>
                    <span class="g_info_num">2</span>&nbsp;  배당률 10% 증가 : <span class="font10">20,000 G</span><br>
					<span class="g_info_num">3</span>&nbsp;  배당률 15% 증가 : <span class="font10">30,000 G</span><br>
                </div>
				<div class="g_info2" style="margin-top:15px">★ 환급패치 ★ (페이백 골드템)</div>
				<div class="g_info3"><span class="font10">선택하신 경기가 낙첨 되었을경우, 사용하신 아이템의 수치만큼 배팅금액을 돌려드립니다.</span></div> 
				<div class="g_info3">
                    <span class="g_info_num">1</span>&nbsp;  낙첨환급 20% : <span class="font10">40,000 G</span><br>
                    <span class="g_info_num">2</span>&nbsp;  낙첨환급 35% : <span class="font10">70,000 G</span><br>
					<span class="g_info_num">3</span>&nbsp;  낙첨환급 50% : <span class="font10">100,000 G</span><br>
                </div>
				
				<!-- 적특패치 숨김처리 -->
				<!-- <div class="g_info2" style="margin-top:15px">★ 적특패치 ★ (미적중 쉴드 골드템)</div>
				<div class="g_info3"><span class="font10">선택하신 경기가 낙첨 되었을경우, 적중특례로 처리할 수 있습니다.</span></div> 
				<div class="g_info3">
                    <span class="g_info_num">1</span>&nbsp;  적특쉴드 : <span class="font10">50,000 G</span><br>
                </div> -->
            </div>
        </div>
        <div class="con_box10">
            <div class="g_info_wrap">
                <div class="g_info1">겜블상점</div>              
                <div class="g_info2">★ 배당패치 ★ (배당UP 골드템)</div>              
                <div class="g_info3">
					<table width="100%" border="0" cellpadding="0" cellspacing="1" class="g_table">
						<tr>
						  <td bgcolor="#262626" align="center" width="20%">사용명</td>
							<td bgcolor="#262626" align="center">설명</td>
							<td bgcolor="#262626" align="center" width="10%">가격</td>
							<td bgcolor="#262626" align="center" width="10%">구매하기</td>                    
						</tr>
						<?php
							foreach ($shopItemlist as $key => $item):
								$buyType = '';
								$itemVal = number_format($item['value'],2)*100;
								$desc = str_replace($itemVal.'%','<span class="g_font1">'.$itemVal.'%</span>',$item['desc']);
								
								if($item['buy_type'] == 0) {
									$buyType = 'G';
								}
								if($item['buy_type'] == 1) {
									$buyType = 'P';
								}
								if($item['buy_type'] == 2) {
									$buyType = 'M';
								}
								if($item['buy_type'] == 3) {
									$buyType = 'F';
								}
								
								if($item['type'] == 2){
						?>
						<tr>
							<td bgcolor="#3e3e3e" align="center"><?=$item['name']?></td>
							<td bgcolor="#3e3e3e" align="center"><?=$desc?></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_font1"><?=$buyType ?> <?=number_format($item['price'])?></span></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_btn" style="cursor: pointer" onclick="fnBuyItem(<?=$item['id']?>,'<?=$item['name']?>',<?= $item['price']?>)">구매</span></td>
						</tr>
						<?php } endforeach;?>
					</table>
                </div>
				<div class="g_info2">★ 환급패치 ★ (페이백 골드템)</div>              
                <div class="g_info3">
					<table width="100%" border="0" cellpadding="0" cellspacing="1" class="g_table">
					<tr>
						  <td bgcolor="#262626" align="center" width="20%">사용명</td>
							<td bgcolor="#262626" align="center">설명</td>
							<td bgcolor="#262626" align="center" width="10%">가격</td>
							<td bgcolor="#262626" align="center" width="10%">구매하기</td>
                                                
						</tr>
					<?php
					foreach ($shopItemlist as $key => $item):
						$buyType = '';
					
						$itemVal = number_format($item['value'],2)*100;
						
						$desc = str_replace($itemVal.'%','<span class="g_font1">'.$itemVal.'%</span>',$item['desc']);
						
						if($item['buy_type'] == 0) {
							$buyType = 'G';
						}
						if($item['buy_type'] == 1) {
							$buyType = 'P';
						}
						if($item['buy_type'] == 2) {
							$buyType = 'M';
						}
						if($item['buy_type'] == 3) {
							$buyType = 'F';
						}
						
						if($item['type'] == 1){
					?>
						<tr>
							<td bgcolor="#3e3e3e" align="center"><?=$item['name']?></td>
							<td bgcolor="#3e3e3e" align="center"><?=$desc?></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_font1"><?=$buyType ?> <?=number_format($item['price'])?></span></td>
							<td bgcolor="#3e3e3e" align="center"><span style="cursor: pointer" class="g_btn" onclick="fnBuyItem(<?=$item['id']?>,'<?=$item['name']?>',<?= $item['price']?>)">구매</span></td>
						</tr>
						<!-- <tr>
							<td bgcolor="#3e3e3e" align="center">35% 환급패치</td>
							<td bgcolor="#3e3e3e" align="center">미적중시 베팅금액의 <span class="g_font1">35%</span> 를 돌려드립니다. </td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_font1">G 70,000</span></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_btn">구매</span></td>
						</tr>
						<tr>
							<td bgcolor="#3e3e3e" align="center">50% 환급패치</td>
							<td bgcolor="#3e3e3e" align="center">미적중시 베팅금액의 <span class="g_font1">50%</span> 를 돌려드립니다. </td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_font1">G 100,000</span></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_btn">구매</span></td>
						</tr> -->
					<?php } endforeach;?>
					</table>
                </div>

				<!-- 적특패치 숨김 처리 -->
                <!-- <div class="g_info2">★ 적특패치 ★ (미적중 쉴드 골드템)</div>              
                <div class="g_info3">
					<table width="100%" border="0" cellpadding="0" cellspacing="1" class="g_table">
						<tr>
						  <td bgcolor="#262626" align="center" width="20%">사용명</td>
							<td bgcolor="#262626" align="center">설명</td>
							<td bgcolor="#262626" align="center" width="10%">가격</td>
							<td bgcolor="#262626" align="center" width="10%">구매하기</td>
						</tr>
						<?php
							foreach ($shopItemlist as $key => $item):
								$buyType = '';
							
								$desc = str_replace('적중특례','<span class="g_font1">적중특례</span>',$item['desc']);
								
								if($item['buy_type'] == 0) {
									$buyType = 'G';
								}
								if($item['buy_type'] == 1) {
									$buyType = 'P';
								}
								if($item['buy_type'] == 2) {
									$buyType = 'M';
								}
								if($item['buy_type'] == 3) {
									$buyType = 'F';
								}
								
								if($item['type'] == 3){
						?>
						<tr>
							<td bgcolor="#3e3e3e" align="center"><?=$item['name']?></td>
							<td bgcolor="#3e3e3e" align="center"><?=$desc?></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_font1"><?=$buyType ?> <?=number_format($item['price'])?></span></td>
							<td bgcolor="#3e3e3e" align="center"><span class="g_btn" style="cursor: pointer" onclick="fnBuyItem(<?=$item['id']?>,'<?=$item['name']?>',<?= $item['price']?>)">구매</span></td>
						</tr>
						<?php } endforeach;?>
					</table>
                </div> -->
            </div>
        </div>

		<div class="g_title2">나의 골드템</div>
        <div class="con_box10">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="g_table">
                <tr>
                    <td class="g_list_title1">보유중인 골드템</td>                    
                    <td width="15%" class="g_list_title1">구매일</td>
                    <td width="15%" class="g_list_title1">사용여부</td>
                </tr>
                <?php
                	foreach ($myItemList as $key => $item):
                	
                	$status = '';
                	if($item['status'] == 0) {
                		$status = '<span class="division2">NO</span>';
                	}else if ($item['status'] == 2) {
                		$status = '<span class="division1">기간만료</span>';
                	}else {
                		$status = '<span class="division1">Yes</span>';
                	}
				?>
				<tr>
                    <td class="g_list1"><span class="font01"><?= $item['name']?></span></td>
                    <td class="g_list1"><span class="font03"><?=date("Y-m-d H:i", strtotime($item['create_dt']))?></span></td>                       
                    <td class="g_list1"><?= $status ?></td>                    
                </tr>
				<?php endforeach;?>
            </table>             
		</div>
	</div>
</div><!-- contents_wrap -->
<?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript">
const fnBuyItem = function(itemId, itemName, itemPrice){

	const gmoney = <?= session()->get('g_money') ?>;

	if(itemPrice > gmoney) {
		alert("보유 G머니를 확인해주세요.");
		return false;
	}
	
	var str_msg = itemName+' 상품을 구매하시겠습니까?';
	
	const result = confirm(str_msg);

	if (result) {
		$.ajax({
	        url: '/web/buyItem',
	        type: 'post',
	        data: {
	            'itemId': itemId
	        },
	    }).done(function (response) {
	        alert(itemName + ' 상품이 구매되었습니다.');
	        location.reload();
	    }).fail(function (error) {
	    	alert(error.responseJSON['messages']['error']);
	    });
	}
}
</script>
</body>
</html>