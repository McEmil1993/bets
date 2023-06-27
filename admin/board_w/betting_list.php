<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

$base_rule_arr = array();
$base_rule_arr2 = array();
$level_rule_arr = array();
$category_rule = array();

if ($db_conn) {
    
        if(false === GameCode::checkAdminType($_SESSION,$BdsAdminDAO)){
           die();
        }
    
    // base_rule
	$p_data['sql'] = "SELECT COUNT(*) AS CNT FROM base_rule";
	$db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);

	$total_cnt = $db_dataArrCnt[0]['CNT'];
	if ($total_cnt > 0) {
		$p_data['sql'] = "SELECT * FROM base_rule";
		$base_rule_arr = $BdsAdminDAO->getQueryData($p_data);
	}
    
	$p_data['sql'] = "SELECT * FROM base_rule_2";
	$base_rule_arr2 = $BdsAdminDAO->getQueryData($p_data);

	// level_rule
	$p_data['sql'] = "SELECT * FROM t_game_config";
	$result = $BdsAdminDAO->getQueryData($p_data);

	foreach ($result as $row) {
		$idx = $row['idx'];
		$level = $row['u_level'];
		$set_type = $row['set_type'];
		
		if (array_key_exists($set_type, $level_rule_arr)) {
			if (!array_key_exists($level, $level_rule_arr[$set_type])) {
				$level_rule_arr[$set_type][$level] = array(
					'idx' => $idx,
					'set_type_val' => $row['set_type_val'],
					'title' => $row['title'],
					'reg_time' => $row['reg_time']
				);
			}
		} else {
			$level_rule_arr[$set_type][$level] = array(
				'idx' => $idx,
				'set_type_val' => $row['set_type_val'],
				'title' => $row['title'],
				'reg_time' => $row['reg_time']
			);
		}
	}

	// category_rule
	$p_data['sql'] = "SELECT * FROM category_rule";
	$category_rule = $BdsAdminDAO->getQueryData($p_data);

	$BdsAdminDAO->dbclose();
}
?>

<html lang="ko">

<?php 
include_once(_BASEPATH.'/common/head.php');
?>
<script>
$(document).ready(function() {
	App.init();
	FormPlugins.init();
	
	$('ul.tabs li').click(function() {
		var tab_id = $(this).attr('data-tab');

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	});

	fnSelectSubCategory();
});
</script>

<script src="<?=_STATIC_COMMON_PATH?>/js/admCommon.js"></script>
<body>
	<form id="popForm" name="popForm" method="post">
		<input type="hidden" id="seq" name="seq">
		<input type="hidden" id="m_idx" name="m_idx">
		<input type="hidden" id="m_dis_id" name="m_dis_id">
		<input type="hidden" id="selContent" name="selContent" value="3">
	</form>
	
	<div class="wrap">
		<?php
		$menu_name = "board_menu_2";
		include_once(_BASEPATH . '/common/left_menu.php');
		include_once(_BASEPATH . '/common/iframe_head_menu.php');
		?>

		<!-- Contents -->
		<div class="con_wrap">
			<div class="title">
				<a href="">
					<i class="mte i_group mte-2x vam"></i>
					<h4>배팅규정</h4>
				</a>
			</div>
        
			<!-- detail search -->
        	<div class="panel search_box">
				<select id="main_category" onchange="javascript:fnSelectMainCategory();">
					<option value="0" selected>기본 규정 선택</option>
					<option value="1">실시간 종목 선택</option>
					<option value="2">스포츠 종목 선택</option>
					<option value="3">미니게임 종목 선택</option>
				</select>
				<select id="sub_category" onchange="javascript:fnSelectSubCategory();">
					<option value="-1">선택</option>
					<option value="base_rule" selected>기본 규정</option>
					<option value="level_rule">레벨별 혜택</option>
				</select>
			</div>
       		<!-- END detail search -->
			
			<!-- 기본 규정 -->
			<div id="div_base_rule" class="panel reserve" style="display: none">
				<div class="tline">
					<table class="mlist">
						<tr>
							<th>종목</th>
							<th>결과처리</th>
							<th>마감시간</th>
							<th>최대배당</th>
							<th>배팅규제</th>
							<th></th>
						</tr>

						<?php 
						foreach ($base_rule_arr as $row) {
							$idx = $row['idx'];
							$result_process = $row['result_process'];
							$end_tiem = $row['end_time'];
							$max_dividend = $row['max_dividend'];
							$betting_regulation = $row['betting_regulation'];
						?>
						<tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
							<input type="hidden" id="bet_rule_idx_<?=$idx?>" value="<?=$idx?>" />

							<td style='text-align:center'><?=$row['name']?></td>
							<!-- <td style='text-align:center'><input type="text" id="bet_rule_result_process_<?=$idx?>" value="<?=$row['result_process']?>" /></td> -->
							<td style='text-align:center'>
								<select id="bet_rule_result_process_<?=$idx?>">
									<option value="자동처리" <?php if ($result_process == '자동처리') { ?>selected<?php } ?>>자동처리</option>
									<option value="수동처리" <?php if ($result_process == '수동처리') { ?>selected<?php } ?>>수동처리</option>
								</select>
							</td>
							<td style='text-align:center'><input type="text" id="bet_rule_end_time_<?=$idx?>" value="<?=$row['end_time']?>" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<td style='text-align:center'><input type="text" id="bet_rule_max_dividend_<?=$idx?>" value="<?=$row['max_dividend']?>" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<td style='text-align:center'><input type="text" id="bet_rule_betting_regulation_<?=$idx?>" value="<?=$row['betting_regulation']?>" /></td>
							<td><a href="javascript:fnUpdateBetRule(<?=$idx?>);" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<?php
						}
						?>
					</table>
					<br />

					<table class="mlist">
						<tr>
							<th>구분</th>
							<th>내용</th>
							<th></th>
						</tr>

						<?php 
						foreach ($base_rule_arr2 as $row) {
							$idx = $row['idx'];
							$type = $row['type'];
							$contents = $row['contents'];
						?>
						<tr>
							<th style="width: 150px; text-align:left"><?=$type?></th>
							<td style="text-align: left">
								<div id="loading"></div>
								<input type="hidden" id="idx_<?=$idx?>" value="<?=$idx?>">
								<textarea id="b_content_<?=$idx?>" rows="5" cols="100"><?=$contents?></textarea><br />
							</td>
						</tr>
						<?php 
						}
						?>
					</table>
				</div>

            	<div style="margin-top: 30px"><a href="javascript:fnUpdateBetRule2();" class="btn btn_green h30">저장</a> <a href="javascript:fnCancelBetRule2();" class="btn btn_green h30">취소</a></div>
			</div>

			<!-- 레벨별 혜택 -->
			<div id="div_level_rule" class="panel reserve" style="display: none">
				<div class="tline">
					<table class="mlist">
						<tr>
							<th class="list_table_left" width="20%">회원레벨</th>
							<td class="list_table_left">1레벨</td>
							<td class="list_table_left">2레벨</td>
							<td class="list_table_left">3레벨</td>
							<td class="list_table_left">VIP레벨</td> 
							<!--
							<td class="list_table_left">4레벨</td> 
							<td class="list_table_left">5레벨</td>
							<td class="list_table_left">6레벨</td>
							<td class="list_table_left">7레벨</td>
							<td class="list_table_left">8레벨</td>
							<td class="list_table_left">9레벨</td>
							<td class="list_table_left">10레벨</td>
							-->
							<td></td>
						</tr>
						<!--
						<tr>
							<th class="list_table_left" width="20%">가입 첫충 (%)</th>
							<td class="list_table_left"><input type="text" id="charge_first_per_0" value="<?=$level_rule_arr['reg_first_charge'][0]['set_type_val']?>" style="width: 100px" onKeyup="javascript:fnCheckInput(this);" /></td>
							// dummy
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><a href="javascript:fnUpdateLevelRule('reg_first_charge');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						-->
						<tr>
							<th class="list_table_left" width="20%">첫충전 (%)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['charge_first_per'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="charge_first_per_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('charge_first_per');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">첫충전 최대 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['charge_max_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="charge_max_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('charge_max_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">매충전 (%)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['charge_per'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="charge_per_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('charge_per');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">매충전 최대 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['charge_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="charge_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('charge_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">롤링 (%)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['rolling'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="rolling_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('rolling');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<!--
						<tr>
							<th class="list_table_left" width="20%">낙첨 본인 (%)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['lose_self_per'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="lose_self_per_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('lose_self_per');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">낙첨 추천인 (%)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['lose_recomm_per'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="lose_recomm_per_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('lose_recomm_per');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						-->
						<tr>
							<th class="list_table_left" width="20%">프리매치 최소 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['pre_min_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="pre_min_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('pre_min_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">프리매치 최대 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['pre_max_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="pre_max_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('pre_max_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">프리매치 상한 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['pre_limit_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="pre_limit_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('pre_limit_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">실시간 최소 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['real_min_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="real_min_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('real_min_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">실시간 최대 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['real_max_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="real_max_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('real_max_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
						<tr>
							<th class="list_table_left" width="20%">실시간 상한 금액 (원)</th>
							<?php 
							$i = 0;
							foreach ($level_rule_arr['real_limit_money'] as $k => $v) {
							?>
							<td class="list_table_left"><input type="text" id="real_limit_money_<?=$k?>" value="<?=$v['set_type_val']?>" style="width: 100px" onKeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></td>
							<?php 
								++$i;
								if ($i >= 4) {
									break;
								}
							}
							?>
							<td><a href="javascript:fnUpdateLevelRule('real_limit_money');" class="btn h25 btn_blu adm_btn_notice_del">수정</a></td>
						</tr>
					</table>
					<br />

					<!--
					<table class="mlist">
						<?php 
						$level_rule_content = $level_rule_arr['level_rule_content'][0]['set_type_val'];
						?>
						<tr>
                    		<th style="width: 150px; text-align:left">내 용</th>
							<td style="text-align: left">
								<div id="loading"></div>
								<textarea name="level_rule_content" id="level_rule_content" rows="5" cols="100"><?=$level_rule_content?></textarea><br>
							</td>
                   		</tr>
					</table>
					-->
				</div>

				<!-- <div style="margin-top: 30px"><a href="javascript:fnUpdateLevelRuleContent();" class="btn btn_green h30">저장</a> <a href="javascript:fnCancelLevelRuleContent();" class="btn btn_green h30">취소</a></div> -->
			</div>

			<!-- 나머지 항목들 -->
			<?php 
			foreach ($category_rule as $row) {
				$category = $row['category'];
				$sub_category = $row['sub_category'];
				$title = $row['title'];
				$content = $row['content'];
			?>
			<div id="div_category_rule_<?=$category?>_<?=$sub_category?>" class="panel reserve" style="display: none">
				<div class="tline">
                	<table class="mlist">
						<tr>
                    		<th style="width: 150px; text-align:left">제 목</th>
                        	<td>
								<div class="confing_box">
									<input type="text" id="category_title_<?=$category?>_<?=$sub_category?>" placeholder="제목을 입력해 주세요." value="<?=$title?>" readonly />
								</div>
                        	</td>
                    	</tr>
                    	<tr>
                    		<th style="width: 150px; text-align:left">내 용</th>
							<td style="text-align: left">
								<div id="loading"></div>
								<textarea id="category_content_<?=$category?>_<?=$sub_category?>" rows="5" cols="100"><?=$content?></textarea><br>
							</td>
						</tr>
					</table>
				</div>

				<div style="margin-top: 30px"><a href="javascript:fnUpdateCategoryRule();" class="btn btn_green h30">저장</a> <a href="javascript:fnCancelCategoryRule();" class="btn btn_green h30">취소</a></div>
			</div>
			<?php 
			}
			?>
    	</div>
    	<!-- END Contents -->
	</div>
	<?php 
	include_once(_BASEPATH.'/common/bottom.php');
	?>
</body>
<script>
function fnSelectMainCategory() {
	fnDisableAllDiv();

	let category = $('#main_category').val();
	$('#sub_category').empty();

	switch (category) {
		case "0":
			$('#sub_category').append('<option value="-1" selected>선택</option>');
			$('#sub_category').append('<option value="base_rule">기본 규정</option>');
			$('#sub_category').append('<option value="level_rule">레벨별 혜택</option>');
			break;
		case "1":
		case "2":
			$('#sub_category').append('<option value="-1" selected>선택</option>');
			$('#sub_category').append('<option value="1">축구</option>');
			$('#sub_category').append('<option value="2">농구</option>');
			$('#sub_category').append('<option value="3">배구</option>');
			$('#sub_category').append('<option value="4">야구</option>');
			$('#sub_category').append('<option value="5">아이스하키</option>');
			$('#sub_category').append('<option value="6">E-스포츠</option>');
			$('#sub_category').append('<option value="7">그 외</option>');
			break;
		case "3":
			$('#sub_category').append('<option value="-1" selected>선택</option>');
			$('#sub_category').append('<option value="1">파워볼</option>');
			$('#sub_category').append('<option value="2">파워 사다리</option>');
			$('#sub_category').append('<option value="3">키노 사다리</option>');
			$('#sub_category').append('<option value="4">가상축구</option>');
			break;
		default:
			return;
	}
}

function fnSelectSubCategory() {
	fnDisableAllDiv();

	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	switch (category) {
		case '0':
			if (sub_category == 'base_rule') {
				$('#div_base_rule').attr('style', 'display: inline-block');
			} else {
				$('#div_level_rule').attr('style', 'display: inline-block');
			}

			break;
		case '1':
		case '2':
		case '3':
			$('#div_category_rule_' + category + '_' + sub_category).attr('style', 'display: inline-block');
			break;
		default:
			return;
	}
}

function fnDisableAllDiv() {
	$('#div_base_rule').attr('style', 'display: none');
	$('#div_level_rule').attr('style', 'display: none');

	for (var i = 1; i < 4; ++i) {
		for (var j = 1; j < 8; ++j) {
			$('#div_category_rule_' + i + '_' + j).attr('style', 'display: none');
		}
	}
}

function fnUpdateBetRule(idx) {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '0' || sub_category != 'base_rule') {
		return;
	}

	let param = {
		'idx': idx,
		'result_process': $('#bet_rule_result_process_' + idx).val(),
		'end_time': $('#bet_rule_end_time_' + idx).val(),
		'max_dividend': $('#bet_rule_max_dividend_' + idx).val(),
		'betting_regulation': $('#bet_rule_betting_regulation_' + idx).val()
	};

	if (confirm('수정 하시겠습니까?')) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update.php',
			data: param,
			success: function(result) {
				alert('수정하였습니다.');
				return;
			},
			error: function(req, status, err) {
				alert('수정에 실패하였습니다.');
			}
		});
	}
}

function fnUpdateBetRule2() {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '0' || sub_category != 'base_rule') {
		return;
	}
	
	param = {
		'idx1': 1,
		'contents1': $('#b_content_1').val(),
		'idx2': 2,
		'contents2': $('#b_content_2').val(),
		'idx3': 3,
		'contents3': $('#b_content_3').val(),
		'idx4': 4,
		'contents4': $('#b_content_4').val()
	}
	
	if (confirm('수정 하시겠습니까?')) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update_2.php',
			data: param,
			success: function(result) {
				alert('수정하였습니다.');
				return;
			},
			error: function(req, status, err) {
				alert('수정에 실패하였습니다.');
			}
		});
	}
}

function fnCancelBetRule2() {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '0' || sub_category != 'base_rule') {
		return;
	}

	if (confirm('취소 하시겠습니까?')) {
		window.location.reload();
	}
}

function fnUpdateLevelRule(set_type) {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '0' || sub_category != 'level_rule') {
		return;
	}

	let url = null;
	let param = null;

	if (set_type == 'reg_first_charge') {
		// 예외 처리
		param = {
			'level': 0,
			'set_type': set_type,
			'set_type_val': $('#charge_first_per_0').val()
		};

		url = '/board_w/_betting_prc_update_3_1.php';

	} else {
		param = {};
		param['set_type'] = set_type;

		// for (var i = 1; i <= 10; ++i) {
		for (var i = 1; i <= 4; ++i) {
			param['set_type_val_' + i] = $('#' + set_type + '_' + i).val();
		}

		url = '/board_w/_betting_prc_update_3_2.php';
	}

	if (confirm('수정 하시겠습니까?')) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: url,
			data: param,
			success: function(result) {
				alert('수정하였습니다.');
				return;
			},
			error: function(req, status, err) {
				alert('수정에 실패하였습니다.');
			}
		});
	}
}

function fnUpdateLevelRuleContent() {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '0' || sub_category != 'level_rule') {
		return;
	}

	param = {
		'level': 0,
		'set_type': 'level_rule_content',
		'set_type_val': $('#level_rule_content').val()
	};

	if (confirm('수정 하시겠습니까?')) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update_4.php',
			data: param,
			success: function(result) {
				alert('수정하였습니다.');
				return;
			},
			error: function(req, status, err) {
				console.log(req);
				console.log(status);
				console.log(err);
				alert('수정에 실패하였습니다.');
			}
		});
	}
}

function fnCancelLevelRuleContent() {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '0' || sub_category != 'level_rule') {
		return;
	}

	if (confirm('취소 하시겠습니까?')) {
		window.location.reload();
	}
}

function fnUpdateCategoryRule() {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '1' && category != '2' && category != '3') {
		return;
	}

	if (sub_category == '0' || Number(sub_category) > 7) {
		return;
	}

	param = {
		category: category,
		sub_category: sub_category,
		title: $('#category_title_' + category + '_' + sub_category).val(),
		content: $('#category_content_' + category + '_' + sub_category).val()
	};

	if (confirm('수정 하시겠습니까?')) {
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/board_w/_betting_prc_update_5.php',
			data: param,
			success: function(result) {
				alert('수정하였습니다.');
				return;
			},
			error: function(req, status, err) {
				alert('수정에 실패하였습니다.');
			}
		});
	}
}

function fnCancelCategoryRule() {
	let category = $('#main_category').val();
	let sub_category = $('#sub_category').val();

	if (category != '1' || category != '2' || category != '3') {
		return;
	}

	if (sub_category == '0' || Number(sub_category) > 7) {
		return;
	}

	if (confirm('취소 하시겠습니까?')) {
		window.location.reload();
	}
}

let prevInput = '';
function fnCheckInput(o) {
	if (o.value.search(/^\d*(\.\d{0,2})?$/) == -1) {
		o.value = prevInput;
	} else {
		prevInput = o.value;
	}
}
</script>
</html>
