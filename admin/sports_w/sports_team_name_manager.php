<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');

include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if($db_conn) {
   
    $p_data['page'] = $LSportsAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $LSportsAdminDAO->real_escape_string($p_data['num_per_page']);
    
    $p_data["table_name"]= " lsports_bet ";
    $p_data["sql_where"]= "";
    $default_link = 'sports_team_name_manager.php?';
    
    $sports_list = "";
    $locations_list = "";
    $src_key = 0;
    $src_value = "";

    if (isset($_REQUEST['sports_list'])) {
        $sports_list = $LSportsAdminDAO->real_escape_string($_REQUEST['sports_list']);
        if (strlen($sports_list) > 0 && $sports_list != '0') {
            $p_data["sql_where"] = " and sports_id = '$sports_list' ";
            $default_link .= "sports_list=$sports_list";
        }
    }
    
    if (isset($_REQUEST['locations_list']) ) {
        $locations_list = $LSportsAdminDAO->real_escape_string($_REQUEST['locations_list']);
               
        if (strlen($locations_list) > 0 && $locations_list > 0) {
            $p_data["sql_where"] .= " and lsports_participant.location_id = '$locations_list' ";
            $default_link .= "&locations_list=$locations_list";
        }
    }
    
    if (isset($_REQUEST['src_value']) ) {
        $src_value = true === isset($_REQUEST['src_value']) && false === empty($_REQUEST['src_value']) ? $_REQUEST['src_value'] : '';
        $src_value = $LSportsAdminDAO->real_escape_string($src_value);
        
        if (strlen($src_value) > 0) {
            $src_key = $_REQUEST['src_key'];
            if($src_key == 1)
                $p_data["sql_where"] .= " and (lsports_participant.team_name like '%$src_value%' || lsports_participant.display_name like '%$src_value%')";
            else
                $p_data["sql_where"] .= " and lsports_participant.league_id in (SELECT id FROM lsports_leagues where name like '%$src_value%' or display_name like '%$src_value%') ";
            
            $lastChar = substr($default_link, -1);
            if(0 != strcmp($lastChar, '?')){
                $default_link .= '&';
            }
            $default_link .= "src_key=$src_key";
            $default_link .= "&src_value=$src_value";
        }
    }

    $p_data['sql'] = "SELECT count(*) as CNT FROM lsports_participant";
    if (strlen($p_data["sql_where"]) > 0) {
        $p_data['sql'] .= ' where 1=1 '.$p_data['sql_where'];
    } else{
        $p_data['sql'] .= ' where idx > 0 AND fp_id > 0';
    }
    
    $db_total_cnt = $LSportsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_total_cnt[0]['CNT'];
    
    // 새로운 팀 카운트
    $p_data['sql'] = "SELECT count(*) as CNT FROM lsports_participant where is_new = 1;";
    $db_total_cnt = $LSportsAdminDAO->getQueryData($p_data);
    $new_total_cnt = $db_total_cnt[0]['CNT'];
    
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];

    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지

    if ($block >= $total_block) $last_page = $total_page;

    if($total_cnt > 0) {
        $start 	= ( $p_data['page'] -1 ) * $p_data['num_per_page'];
        
        $p_data['sql'] = "SELECT lsports_participant.idx, lsports_participant.sports_id, lsports_participant.location_id, lsports_participant.league_id, lsports_locations.name, lsports_sports.name as sports_name, ";
        $p_data['sql'] .= " fp_id, team_name, lsports_participant.display_name, lsports_locations.name as location_name, lsports_leagues.display_name as league_name";
        $p_data['sql'] .= " FROM lsports_participant";
        $p_data['sql'] .= " left join lsports_locations on lsports_participant.location_id = lsports_locations.id";
        $p_data['sql'] .= " left join lsports_leagues on lsports_participant.league_id = lsports_leagues.id";
        $p_data['sql'] .= " left join lsports_sports on lsports_leagues.sport_id = lsports_sports.id";
        if (strlen($p_data["sql_where"]) > 0) {
            $p_data['sql'] .= ' where lsports_participant.idx > 0 AND lsports_participant.fp_id > 0  and lsports_leagues.bet_type = 1 and lsports_sports.bet_type = 1 '.$p_data['sql_where'];
        } else{
            $p_data['sql'] .= ' where lsports_participant.idx > 0  and lsports_leagues.bet_type = 1 and lsports_sports.bet_type = 1';
        }
        $p_data['sql'] .= " ORDER BY lsports_locations.name ASC LIMIT $start, ".$p_data['num_per_page'].";";
        $db_dataArr = $LSportsAdminDAO->getQueryData($p_data);
        //CommonUtil::logWrite("team list : " . $p_data['sql'], "info");
    }

    $s = $LSportsAdminDAO->getLocationsList();
    $sp = $LSportsAdminDAO->getSportsList();
    $LSportsAdminDAO->dbclose();

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

    $menu_name = "sports_team_name_manager";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>팀명 관리</h4>
            </a>
        </div>

        <!-- detail search -->
        <!--<form id="search" name="search" action=''>-->
        <div class="panel search_box">
            <div class="panel_tit">
                <div class="search_form fl">
                    <h5><a href="#"><b>프리매치</b></a></h5>
                    <!--                    <h5><a href="#">프리매치(수동)</a></h5>-->
                </div>
                <!--<div class="search_form fr">
                    <div>
                        <select>
                            <option value="실시간" selected="">실시간</option>
                            <option value="스포츠">스포츠</option>
                        </select>
                    </div>
                    <div>
                        <a href="#" class="btn h30 btn_blu">배당리셋</a>
                    </div>
                </div>-->
            </div>
            <table class="mlist mline">
                <tr>
                    <th>종목</th>
                    <!--<th><input id="" type="text" class="" style="width: 100%" placeholder="" value=""/></th>-->
                    <th>
                        <select name="sports_list" id="sports_list" style="width: 100%">
                            <option value="0">전체</option>
                            
                            <?php foreach ($sp as $key => $item) { ?>
                                 <option value="<?=$item['id']?>"   <?php if ($sports_list == $item['id']):?> selected<?php endif;?>><?=$item['name']?></option>
                            <?php }?>
                                                    
                        </select>
                    </th>
                    <th>지역</th>
                    <th>
                        <select name="locations_list" id="locations_list" style="width: 100%">
                            <option value="0">전체</option>
                            
                            <?php foreach ($s as $key => $item) { ?>
                                 <option value="<?=$item['id']?>"   <?php if ($locations_list == $item['id']):?> selected<?php endif;?>><?=$item['name']?></option>
                            <?php }?>
                                                    
                        </select>
                    </th>
                    <th>리그명</th>
                    <th>
                        <select name="src_key" id="src_key" style="width: 100%">
                            <?php $_REQUEST['src_key'] = true === isset($_REQUEST['src_key']) && false === empty($_REQUEST['src_key']) ?  $_REQUEST['src_key'] : '';  if($_REQUEST['src_key'] == 1){ ?>
                                <option value="1" selected>팀명</option>
                                <option value="2">리그명</option>
                            <?php }else{ ?>
                                <option value="1">팀명</option>
                                <option value="2" selected>리그명</option>
                            <?php } ?>
                        </select>
                    </th>
                    <th><input id="src_value" name="src_value" type="text" class="" style="width: 100%" placeholder="" value="<?= $src_value = true === isset($_REQUEST['src_value']) && false === empty($_REQUEST['src_value']) ? $_REQUEST['src_value'] : '' ?>"/></th>
                    <th><a href="#" class="btn h30 btn_blu search_btn">검색</a></th>
                </tr>
            </table>
        </div>
        <!--</form>-->
        <!-- END detail search -->

        <!-- list -->
        <div class="panel reserve">
            <div class="panel_tit">
                <div class="search_form fl">
                    <h5><a href="#"><b>모든 팀 (<?=number_format($total_cnt)?>)</b></a> | </h5>
                    <h5><a href="#">새로운 팀 (<?=number_format($new_total_cnt)?>)</a></h5>
                </div>
                <div class="search_form fr">
                    <!--<a href="#" class="btn h30 btn_blu">전체 수정</a>-->
                </div>
            </div>
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>종목</th>
                        <th>지역</th>
                        <th>리그명(Front)</th>
                        <th>팀ID</th>
                        <th>팀명(Data)</th>
                        <th>팀명(Front)</th>
                        <th>수정</th>
                    </tr>
                    <?php if($total_cnt > 0){ ?>
                        <?php 
                        $db_dataArr = true === isset($db_dataArr) && false === empty($db_dataArr) ? $db_dataArr : [];
                        foreach ($db_dataArr as $key => $rows) { ?>
                        <?php $db_m_idx = $rows['idx']; ?>
                        <tr>
                            <td><?=$rows['sports_name']?></td>
                            <td><?=$rows['location_name']?></td>
                            <td><?=$rows['league_name']?></td>
                            <td><?=$rows['fp_id']?></td>
                            <td><?=$rows['team_name']?></td>
                            <td><input id="name_<?=$rows['idx']?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$rows['display_name']?>"/></td>
                            <td><a href="javascript:fn_update_menu6(<?=$db_m_idx?>)" class="btn h30 btn_blu">수정</a></td>
                        </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr>
                            <td colspan='11'>데이터가 없습니다.</td>
                        </tr>
                    <?php } ?>
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
            let sports_list = $('#sports_list').val();
            let locations_list = $('#locations_list').val();
            let src_key = $('#src_key').val();
            let src_value = $('#src_value').val();

            //alert(sports_list + ' ' + locations_list + ' ' + src_key + ' ' + src_value);

            location.href = 'sports_team_name_manager.php?sports_list='+sports_list+'&locations_list='+locations_list+'&src_key='+src_key+'&src_value='+src_value;
        });
    })

    function fn_update_menu6(idx) {
        var name = $('#name_'+idx).val();
        
	var str_msg = '수정 하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_team_name_manager_prc_update.php',
			data:{'idx':idx, 'name':name},
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
	}
	else {
		return;
	}
    }
</script>
<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
</html>
