<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');

include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$sport_id = isset($_REQUEST['sport_id']) ? $_REQUEST['sport_id'] : 0;
$leagues_id = isset($_REQUEST['leagues_id']) ? $_REQUEST['leagues_id'] : 0;
$leagues_name = isset($_REQUEST['leagues_name']) ? $_REQUEST['leagues_name'] : '';
$bet_type = isset($_REQUEST['bet_type']) ? $_REQUEST['bet_type'] : 0;

$MEMAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {

    $p_data['page'] = $MEMAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $MEMAdminDAO->real_escape_string($p_data['num_per_page']);
    
    $sport_id = $MEMAdminDAO->real_escape_string($sport_id);
    $leagues_id = $MEMAdminDAO->real_escape_string($leagues_id);
    $league_name = $MEMAdminDAO->real_escape_string($league_name);
    $bet_type = $MEMAdminDAO->real_escape_string($bet_type);    
    
    $p_data["table_name"]= " lsports_sports ";
    $p_data["sql_where"]= "";
    $default_link = 'sports_set_exchange_rate_types.php?sport_id='.$sport_id.'&leagues_id='.$leagues_id.'&leagues_name='.$leagues_name;

    // 종목 목록 조회
    $p_data['sql'] = "SELECT count(*) as CNT FROM lsports_markets join lsports_sports on lsports_markets.sport_id = lsports_sports.id";
    $p_data['sql'] .= " left join lsports_refund_rate_market on lsports_markets.sport_id = lsports_refund_rate_market.sports_id";
    $p_data['sql'] .= " and lsports_refund_rate_market.league_id = $leagues_id and lsports_refund_rate_market.market_id = lsports_markets.id";
    $p_data['sql'] .= " and lsports_refund_rate_market.bet_type = $bet_type";
    $p_data['sql'] .= " WHERE lsports_sports.is_use = 1";
    if($sport_id > 0)
        $p_data["sql"] .= " and lsports_markets.sport_id = $sport_id";
    
    if($bet_type > 0)
        $p_data["sql"] .= " and lsports_markets.bet_group = $bet_type and lsports_sports.bet_type = $bet_type";
    
    $db_total_cnt = $MEMAdminDAO->getQueryData($p_data);
    $total_cnt = $db_total_cnt[0]['CNT'];

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];

    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지

    if ($block >= $total_block) $last_page = $total_page;

    if($total_cnt > 0) {
        $p_data['sql'] = "SELECT lsports_sports.name as sports_name, lsports_markets.idx, lsports_markets.id, lsports_markets.name, lsports_markets.sport_id, lsports_markets.lang, "
                            . "lsports_markets.bet_group, "
                            . "lsports_refund_rate_market.is_margin_refund, lsports_refund_rate_market.refund_rate, lsports_refund_rate_market.deduction_refund_rate "
                            . "FROM lsports_markets join lsports_sports on lsports_markets.sport_id = lsports_sports.id";
        $p_data['sql'] .= " left join lsports_refund_rate_market on lsports_markets.sport_id = lsports_refund_rate_market.sports_id";
        $p_data['sql'] .= " and lsports_refund_rate_market.league_id = $leagues_id and lsports_refund_rate_market.market_id = lsports_markets.id";
        $p_data['sql'] .= " and lsports_refund_rate_market.bet_type = $bet_type";
        $p_data['sql'] .= " WHERE lsports_sports.is_use = 1";
        
        if($sport_id > 0)
            $p_data["sql"] .= " and lsports_markets.sport_id = $sport_id";
        
        if($bet_type > 0)
            $p_data["sql"] .= " and lsports_markets.bet_group = $bet_type and lsports_sports.bet_type = $bet_type";
 
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";

        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    }

   
    $MEMAdminDAO->dbclose();
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
<body>
<div class="wrap">
    <?php

    $menu_name = "sports_set_exchange_rate";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>환수율 설정</h4>
            </a>
        </div>

        <!-- list -->
        <div class="panel reserve">
            <div class="panel_tit">
                <div class="search_form fl">
                    <h5><a href="sports_set_exchange_rate.php">종목별</a> | </h5>
                    <h5><a href="sports_set_exchange_rate_leagues.php?sport_id=<?=$sport_id?>"><b>리그별</b></a></h5>
                </div>
                <div class="search_form fr">
                    <a href="javascript:all_save(<?=$bet_type?>, <?=$leagues_id?>)" class="btn h30 btn_blu">전체 수정</a>
                </div>
            </div>
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>고유번호</th>
                        <th>종목ID</th>
                        <th>종목명</th>
                        <th>리그ID</th>
                        <th>리그명</th>
                        <th>타입</th>
                        <th>타입ID</th>
                        <th>타입명</th>
                        <th>현재 환수율</th>
                        <th>수정 환수율</th>
                        <th>현재 차감환수율</th>
                        <th>수정 차감환수율</th>
                        <th>마진</th>
                        <th>저장</th>
                    </tr>
                    <tbody id="sports_tbody">
                    <?php if($total_cnt > 0){ ?>
                        <?php foreach ($db_dataArr as $key => $item) { 
                            $color = '';
                            $color2 = '';
                            
                            $item['input_refund_rate'] = true === isset($item['input_refund_rate']) ? $item['input_refund_rate'] : 0;
                            $item['deduction_refund_rate'] = true === isset($item['deduction_refund_rate']) ? $item['deduction_refund_rate'] : 0;
                            if($item['input_refund_rate'] > 0)
                                $color = 'red';
                            if($item['deduction_refund_rate'] > 0)
                                $color2 = 'red';
                        ?>
                        <tr>
                            <td><?=$item['idx']?></td>
                            <td><?=$item['sport_id']?></td>
                            <td><?=$item['sports_name']?></td>
                            <td><?=$leagues_id?></td>
                            <td><?=$leagues_name?></td>
                            <td><?=$item['bet_group'] == 1?'스포츠':'실시간'?></td>
                            <td><?=$item['id']?></td>
                            <td><?=$item['name']?></td>
                            <td><input id="input_refund_rate_current_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=isset($item['refund_rate'])?$item['refund_rate']:number_format(0, 2)?>" readonly/></td>
                            <?php if(1 == $bet_type){ ?>
                            <td><input id="input_refund_rate_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=isset($item['refund_rate'])?$item['refund_rate']:number_format(0, 2)?>"/></td>
                            <?php }else{ ?>
                            <td><input id="input_refund_rate_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=isset($item['refund_rate'])?$item['refund_rate']:number_format(0, 2)?>" readonly/></td>
                            <?php } ?>
                            <td><input id="input_deduction_refund_rate_current_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color2?>" placeholder="" value="<?=isset($item['deduction_refund_rate'])?$item['deduction_refund_rate']:number_format(0, 2)?>" readonly/></td>
                            <td><input id="input_deduction_refund_rate_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color2?>" placeholder="" value="<?=isset($item['deduction_refund_rate'])?$item['deduction_refund_rate']:number_format(0, 2)?>"/></td>
                            <td style='text-align:center'>
                                <?php if(1 == $bet_type){ ?>
                                    <?php if ($item['is_margin_refund'] == 1) { ?>
                                        <a href="#" onclick="betOnOffBtnClick(this, <?= $item['id'] ?>, '<?= $bet_type ?>', 0, <?= $item['sport_id'] ?>, <?= $leagues_id ?>)" class="btn h25 btn_green"> <?= 'ON' ?></a>
                                    <?php } else if ($item['is_margin_refund'] == 0) { ?>
                                        <a href="#" onclick="betOnOffBtnClick(this, <?= $item['id'] ?>, '<?= $bet_type ?>', 1, <?= $item['sport_id'] ?>, <?= $leagues_id ?>)" class="btn h25 btn_gray"> <?= 'OFF' ?></a>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <td><a href="javascript:save(<?=$item['id']?>, <?=$item['idx']?>, <?=$bet_type?>, <?= $item['sport_id'] ?>, <?= $leagues_id ?>)" class="btn h30 btn_blu">저장</a></td>
                        </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <td colspan="8">해당 데이터가 없습니다.</td>
                    <?php }?>
                    </tbody>
                </table>
                <?php
                include_once(_BASEPATH.'/common/page_num.php');
                ?>
            </div>
        </div>
        <!-- END list -->
    </div>
    <!-- END Contents -->
</div>
<script type="text/javascript">
    $(document).ready(function () {
        /*$('#sport_list').on('change', function () {
            let select_id = $('#sport_list').val();
            $('.league_list').attr('style', 'display: none;');
            $('.select_option_' + select_id).removeAttr('style');
        });*/

        $('.search_btn').on('click', function () {
            let sport_id = $('#sport_list').val();
            let league_id = $('#league_list').val();
            let bet_status_id = $('#bet_status_list').val();
            let team_name = $('#team_name').val();

            alert(sport_id + ' ' + league_id + ' ' + bet_status_id + ' ' + team_name);

            location.href = '/sports_w/prematch_manager.php?s_id='+sport_id+'&l_id='+league_id+'&bs_id='+bet_status_id+'&tn='+team_name;
        });
    })
    
    function betOnOffBtnClick(ateg, id, bet_type, is_margin_refund, sports_id, league_id) {
        //console.log(fixture_start_date);

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_sports_set_exchange_rate_onoff_ajax.php',
            data: {'type': 3, 'id': id, 'bet_type': bet_type, 'is_margin_refund': is_margin_refund, 'sports_id': sports_id, 'league_id': league_id},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('업데이트 되었습니다.');

                    if (1 == is_margin_refund) {
                        $(ateg).removeClass("btn_gray").removeClass("btn_green").addClass("btn_green");
                        $(ateg).attr("onclick", "betOnOffBtnClick(this," + id + ", " + bet_type + ", 0," + sports_id + "," + league_id + ")");
                        $(ateg).text("ON");
                    } else {
                        $(ateg).removeClass("btn_green").removeClass("btn_gray").addClass("btn_gray");
                        $(ateg).attr("onclick", "betOnOffBtnClick(this," + id + ", " + bet_type + ", 1," + sports_id + "," + league_id + ")");
                        $(ateg).text("OFF");
                    }
                    return;
                } else {
                    alert('업데이트 실패 (1)');
                    return;
                }
            },
            error: function (request, status, error) {
                alert('업데이트 실패 (2)');

                return;
            }
        });
    }

    function save(id, idx, bet_type, sports_id, league_id){
        let input_refund_rate = $('#input_refund_rate_'+idx).val();
        let input_deduction_refund_rate = $('#input_deduction_refund_rate_'+idx).val();
        
        let str_msg = '저장하시겠습니까?';
	let result = confirm(str_msg);
	if (result){
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/sports_w/_sports_set_exchange_rate_update_sports.php',
                data:{'id':id, 'idx':idx, 'bet_type':bet_type, 'input_refund_rate':input_refund_rate, 'input_deduction_refund_rate':input_deduction_refund_rate, 'type':3, 'sports_id': sports_id, 'league_id': league_id},
                success: function (result) {
                    if(result['retCode'] == "1000"){
                        alert('저장하였습니다.');
                        //window.location.reload();
                        $('#input_refund_rate_current_'+idx).val(parseFloat(input_refund_rate).toFixed(2));
                        $('#input_refund_rate_'+idx).val(parseFloat(input_refund_rate).toFixed(2));
                        $('#input_deduction_refund_rate_current_'+idx).val(parseFloat(input_deduction_refund_rate).toFixed(2));
                        $('#input_deduction_refund_rate_'+idx).val(parseFloat(input_deduction_refund_rate).toFixed(2));
                        
                        if(input_refund_rate > 0){
                            $('#input_refund_rate_current_'+idx).css("color", "red");
                            $('#input_refund_rate_'+idx).css("color", "red");
                        }else{
                            $('#input_refund_rate_current_'+idx).css("color", "");
                            $('#input_refund_rate_'+idx).css("color", "");
                        }
                        
                        if(input_deduction_refund_rate > 0){
                            $('#input_deduction_refund_rate_current_'+idx).css("color", "red");
                            $('#input_deduction_refund_rate_'+idx).css("color", "red");
                        }else{
                            $('#input_deduction_refund_rate_current_'+idx).css("color", "");
                            $('#input_deduction_refund_rate_'+idx).css("color", "");
                        }
                        return;
                    }else{
                        alert(result['retMsg']);
                        return;
                    }
                },
                error: function (request, status, error) {
                    alert('저장에 실패하였습니다.');
                    return;
                }
            });
	}
    }
    
    // 전체수정
    function all_save(bet_type, league_id){
        //var input_refund_rate = $('#input_refund_rate_'+idx).val();
        
        let arrSportsData = [];

	$('#sports_tbody tr').each(function (index, tr) {
            let idx = tr.cells[0].innerHTML;
            let sports_id = tr.cells[1].innerHTML;
            let market_id = tr.cells[6].innerHTML;
            let current_rate = $('#input_refund_rate_current_' + idx).val();
            let update_rate = $('#input_refund_rate_' + idx).val();
            let current_deduction_rate = $('#input_deduction_refund_rate_current_'+idx).val();
            let update_deduction_rate = $('#input_deduction_refund_rate_'+idx).val();
            if(current_rate !== update_rate || current_deduction_rate !== update_deduction_rate){
                let obj = new Object();
                obj = {'id':market_id, 'idx':idx, 'bet_type':bet_type, 'update_rate':update_rate, 'update_deduction_rate':update_deduction_rate, 'type':3, 'sports_id': sports_id, 'league_id': league_id};
                arrSportsData.push(obj);
            }
        });
        
	let sportsData = JSON.stringify(arrSportsData);
        var str_msg = '저장하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/sports_w/_sports_set_exchange_rate_update_sports_all.php',
                data:{'sportsData':sportsData, 'type':3},
                success: function (result) {
                    if(result['retCode'] == "1000"){
                        alert('저장하였습니다.');
                        //window.location.reload();
                        arrSportsData.forEach(function(item){
                           //console.log(item);
                           $('#input_refund_rate_current_'+item['idx']).val(parseFloat(item['update_rate']).toFixed(2));
                           $('#input_refund_rate_'+item['idx']).val(parseFloat(item['update_rate']).toFixed(2));
                           $('#input_deduction_refund_rate_current_'+item['idx']).val(parseFloat(item['update_deduction_rate']).toFixed(2));
                           $('#input_deduction_refund_rate_'+item['idx']).val(parseFloat(item['update_deduction_rate']).toFixed(2));
                           
                           if(item['update_rate'] > 0){
                                $('#input_refund_rate_current_'+item['idx']).css("color", "red");
                                $('#input_refund_rate_'+item['idx']).css("color", "red");
                            }else{
                                $('#input_refund_rate_current_'+item['idx']).css("color", "");
                                $('#input_refund_rate_'+item['idx']).css("color", "");
                            }

                            if(item['update_deduction_rate'] > 0){
                                $('#input_deduction_refund_rate_current_'+item['idx']).css("color", "red");
                                $('#input_deduction_refund_rate_'+item['idx']).css("color", "red");
                            }else{
                                $('#input_deduction_refund_rate_current_'+item['idx']).css("color", "");
                                $('#input_deduction_refund_rate_'+item['idx']).css("color", "");
                            }
                        });
                        return;
                    }else{
                        alert(result['retMsg']);
                        return;
                    }
                },
                error: function (request, status, error) {
                    alert('저장에 실패하였습니다.');
                    return;
                }
            });
	}
    }
</script>
<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
</html>
