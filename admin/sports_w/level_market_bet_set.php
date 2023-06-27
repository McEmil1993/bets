<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
//include_once(_DAOPATH.'/class_Admin_Member_dao.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if ($db_conn) {
	$dividendPolicy = $LSportsAdminDAO->getLevelBetPolicy();
        $LSportsAdminDAO->dbclose();

	$arrDividendPolicy = array();
	foreach ($dividendPolicy as $k => $v)
	{
		$rank = $v['level'];
		$type = $v['bet_type'];

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
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
<!--<![endif]-->

<?php
include_once(_BASEPATH.'/common/head.php');
?>
<script>
    $(document).ready(function() {
        App.init();
        FormPlugins.init();

        $('ul.tabs li').click(function(){
            var tab_id = $(this).attr('data-tab');

            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');

            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
        })
    });
</script>
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admConfigBet.js" charset="utf-8"></script>
<body>
<div class="wrap">
    <?php

    $menu_name = "level_market_bet_set";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
<div class="title">
	<a href="">
		<i class="mte i_settings vam ml20 mr10"></i>
		<h4>레벨별 마켓배팅금액 설정</h4>
	</a>
</div>

<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
    <!-- list -->
    <div class="panel reserve">
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>등급</th>
                        <th>프리매치</th>
                        <th>실시간</th>
                        <th>수정</th>
                    </tr>
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
    </div>
    <!-- END Contents -->
</div>
<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
<script>
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
			url: '/sports_w/_level_market_bet_set_prc_update.php',
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
</script>

</body>
</html>
