<?php

include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if ($db_conn) {
	$dividendPolicy = $LSportsAdminDAO->getDividendPolicy();
    $LSportsAdminDAO->dbclose();

	$arrDividendPolicy = array();
	foreach ($dividendPolicy as $k => $v)
	{
		$rank = $v['rank'];
		$type = $v['type'];

		if (array_key_exists($rank, $arrDividendPolicy)) {
			$arrDividendPolicy[$rank][$type] = array(
				'amount' => $v['amount'],
				'create_dt' => $v['create_dt'],
				'update_dt' => $v['update_dt']
			);
		} else {
			$arrDividendPolicy[$rank][$type] = array(
				'amount' => $v['amount'],
				'create_dt' => $v['create_dt'],
				'update_dt' => $v['update_dt']
			);
		}

		$arrDividendPolicy[$rank][$type] = array(
			'amount' => $v['amount'],
			'create_dt' => $v['create_dt'],
			'update_dt' => $v['update_dt']
		);
	}
}
?>

<div class="title">
	<a href="">
		<i class="mte i_settings vam ml20 mr10"></i>
		<h4>등급별 배팅금액 설정</h4>
	</a>
</div>

<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
	<!-- detail search -->
	<div class="panel search_box">
		<div style="color:#f89d1b!important">
			※ 등록 된 등급별 배팅금액은 [스포츠관리 > 리그 관리] 메뉴에서 리그별로 적용 바랍니다.
		</div>

		<div style="margin-top: 3px;"></div>
		<table class="mlist mline">
			<tr>
				<th>등급</th>
				<th><input id="game_level" name="game_level" type="text" class="" style="width: 100%" placeholder="" maxlength="3" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></th>
				<th>프리매치</th>
				<th><input id="game_level_pre" name="game_level_pre" type="text" class="" style="width: 100%" placeholder="" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></th>
				<th>실시간</th>
				<th><input id="game_level_real" name="game_level_real" type="text" class="" style="width: 100%" placeholder="" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></th>
			</tr>
		</table>

		<div class="panel_tit" style="margin-top: 5px;">
			<div class="" style="text-align: center">
				<a href="javascript:;" onClick="addDividendPolicy();" class="btn h30 btn_blu">등 록</a>
			</div>
		</div>    
	</div>
	<!-- END detail search -->

	<!-- list -->
	<div class="panel reserve">
		<div class="tline">
			<table class="mlist">
				<tr>
					<th>등급</th>
					<th>프리매치</th>
					<th>실시간</th>
					<th>수정/삭제</th>
				</tr>
<?php 

?>
				<?php
					if (count($arrDividendPolicy) > 0) {
						foreach ($arrDividendPolicy as $rank => $v) {
							$t_id_pre = "t_id_pre_" . $rank;
							$t_id_real = "t_id_real_" . $rank;
							$pre_amount = $v[1]['amount'];
							$real_amount = $v[2]['amount'];
				?>
				<tr>
					<td><?=$rank?></td>
					<td><input id="<?=$t_id_pre?>" name="<?=$t_id_pre?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$pre_amount?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
					<td><input id="<?=$t_id_real?>" name="<?=$t_id_real?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$real_amount?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
					<td width="50px">
						<a href="javascript:;" onClick="modifyDividendPolicy(<?=$rank?>);" class="btn h30 btn_green">수정</a>
						<a href="javascript:;" onClick="removeDividendPolicy(<?=$rank?>);" class="btn h30 btn_red">삭제</a>
					</td>
				</tr>
				<?php
					}
				} else {
					echo "<tr><td colspan='4'>데이터가 없습니다.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
	<!-- END list -->
</form>

<script>
function addDividendPolicy() {
	var rank = $('#game_level').val();
	if (!rank || rank <= 0) {
		alert('등급을 입력해주세요.');
		return;
	}

	var pre_amount = $('#game_level_pre').val();
	if (!pre_amount || pre_amount <= 0) {
		alert('프리매치 금액을 입력해주세요.');
		return;
	}

	var real_amount = $('#game_level_real').val();
	if (!real_amount || real_amount <= 0) {
		alert('실시간 금액을 입력해주세요.');
		return;
	}

	var result = confirm('등록 하시겠습니까?');
	if (result) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_league_manager_prc_insert.php',
			data: { 'rank': rank, 'pre_amount': pre_amount, 'real_amount': real_amount },
			success: function (result) {
				if(result['retCode'] == "1000"){
					alert('등록되었습니다.');
					window.location.reload();
					return;
				} else {
					alert(result['retMsg']);
					return;
				}
			},
			error: function (request, status, error) {
				alert('등록에 실패하였습니다.');
				return;
			}
		});
	} else {
		return;
	}
}

function modifyDividendPolicy(rank) {
	var pre_amount = $('#t_id_pre_' + rank).val();
	if (!pre_amount || pre_amount <= 0) {
		alert(rank + ' 등급 프리매치 금액을 입력해주세요.');
		return;
	}

	var real_amount = $('#t_id_real_' + rank).val();
	if (!real_amount || real_amount <= 0) {
		alert(rank + ' 등급 실시간 금액을 입력해주세요.');
		return;
	}

	var result = confirm('수정 하시겠습니까?');
	if (result) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_league_manager_prc_update2.php',
			data: { 'rank': rank, 'pre_amount': pre_amount, 'real_amount': real_amount },
			success: function (result) {
				if(result['retCode'] == "1000"){
					alert('수정하였습니다.');
					window.location.reload();
					return;
				}else{
					alert(result['retMsg']);
					return;
				}
			},
			error: function (request, status, error) {
				alert('수정에 실패하였습니다.');
				return;
			}
		});
	} else {
		return;
	}
}

function removeDividendPolicy(rank) {
	var result = confirm('삭제 하시겠습니까?');
	if (result) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_league_manager_prc_delete.php',
			data: { 'rank': rank },
			success: function (result) {
				if(result['retCode'] == "1000"){
					alert('삭제하였습니다.');
					window.location.reload();
					return;
				}else{
					alert(result['retMsg']);
					return;
				}
			},
			error: function (request, status, error) {
				alert('삭제에 실패하였습니다.');
				return;
			}
		});
	} else {
		return;
	}
}
</script>
