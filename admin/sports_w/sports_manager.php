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

$bet_type = trim(isset($_REQUEST['bet_type']) ? $_REQUEST['bet_type'] : 1);

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$LSportsAdminDAO)){
        die();
    }
    $bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    $p_data['page'] = $LSportsAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $LSportsAdminDAO->real_escape_string($p_data['num_per_page']);
    
    $p_data["table_name"]= " lsports_bet ";
    $p_data["sql_where"]= "";
    $default_link = 'sports_manager.php?bet_type='.$bet_type;

    $p_data['sql'] = "SELECT count(*) as CNT FROM lsports_sports WHERE delete_dt is null and bet_type = $bet_type;";
    $db_total_cnt = $LSportsAdminDAO->getQueryData($p_data);
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
        $p_data['sql'] = "SELECT idx,
                                id, 
                                bet_type,
                                name, 
                                display_name, 
                                lang,
                                create_dt,
                                update_dt,
                                delete_dt,
                                is_use,
                                image_path,
                                input_refund_rate
                                FROM lsports_sports 
                                WHERE delete_dt is null and bet_type = $bet_type";
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page'].";";
        $db_dataArr = $LSportsAdminDAO->getQueryData($p_data);
    }
  
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

    $menu_name = "sports_manager";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">

        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>종목 관리</h4>
            </a>
        </div>

        <!-- list -->
        <div class="panel reserve">
            <span style="color:red">주의 이미지 파일명은 icon_game종목번호.png로 해주셔야 합니다.</span>
            <iframe id="iframe1" name="iframe1" style="display:none"></iframe>
            <div class="panel_tit">
                <div class="search_form fl">
                    <div class="" style="padding-right: 10px;">
                        <?php if($bet_type == 1){ ?>
                            <h5><a href="sports_manager.php?&bet_type=1"><b>스포츠</b></a> | </h5>
                            <h5><a href="sports_manager.php?&bet_type=2">실시간</a></h5>
                        <?php }else{ ?>
                            <h5><a href="sports_manager.php?&bet_type=1">스포츠</a> | </h5>
                            <h5><a href="sports_manager.php?&bet_type=2"><b>실시간</b></a></h5>
                        <?php } ?>
                    </div>
                </div>

                <div class="search_form fr">
                    <div>
                        <select id="resetBetType">
                            <option value="1">스포츠</option>
                            <option value="2">실시간</option>
                        </select>
                    </div>
                    <div>
                        <a href="#" class="btn h30 btn_blu" onClick="fnResetBetPrice(0, '<?=INITDATA_PRE_URL?>','<?=INITDATA_REAL_URL?>');">배당 리플래쉬</a>
                    </div>
                </div>
            </div>


            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>종목ID</th>
                        <th>타입</th>
                        <th>종목명(Data 명)</th>
                        <th>한글명(Front)</th>
                        <th>사용여부</th>
                        <th>이미지</th>
                        <th>현재 이미지</th>
                        <!--<th>현재 환수율</th>
                        <th>수정 환수율</th>-->
                        <th>삭제</th>
                    </tr>
                    <?php foreach ($db_dataArr as $key => $item) { ?>
                    <?php $db_m_idx = $item['idx']; 
                        $color = '';
                        if($item['input_refund_rate'] > 0)
                            $color = 'red';
                    ?>
                    <tr>
                        <input type="hidden" value="<?=$item['idx']?>">
                        <td><?=$item['id']?></td>
                        <td><?=$item['bet_type'] == 1 ? '스포츠':'실시간'?></td>
                        <td><?=$item['name']?></td>
                        <td><input id="name_<?=$item['idx']?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$item['display_name']?>"/></td>
                        <td>
                            <select id="is_use_<?=$item['idx']?>" style="width: 100%">
                                <?php if($item['is_use'] == 1){ ?>
                                    <option value="1" selected>사용</option>
                                    <option value="0" >미사용</option>
                                <?php }else{ ?>
                                    <option value="1">사용</option>
                                    <option value="0" selected>미사용</option>
                                <?php } ?>
                            </select>
                        </td>
                        <td class="file_thumb_section">
                            <input id="update_name_<?=$item['idx']?>" type=hidden value=''>
                            <form id="thumbnail_fm_<?=$item['idx']?>" method="post" action="../common/image_send.php" enctype="multipart/form-data" target="iframe1">
                                <div class="image_container_<?=$item['idx']?> w40"></div>
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/sports'>
                                <input type="file" onchange="setThumbnail(event, <?=$item['idx']?>);" id="uploadfile" style="width: 100%" name="uploadfile" accept="image/*">
                            </form>
                        </td>
                        <td><img class="prev_img w40" src="<?=IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/sports/icon_game'.$item['id'].'.png'?>" onerror="this.style.display='none'"></td>
                        <!--<td><input id="input_refund_rate_current_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=$item['input_refund_rate']?>" readonly/></td>
                        <td><input id="input_refund_rate_<?=$item['idx']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=$item['input_refund_rate']?>"/></td>-->
                        <td>
                            <div>
                                <!--<a href="javascript:fn_btn_sports_send(<?=$item['idx']?>);" id="adm_btn_sports_send" class="btn h30 btn_green" style="color: white">이미지 수정</a>-->
                                <a href="javascript:fn_update_menu3(<?=$db_m_idx?>, <?=$item['id']?>);" class="btn h30 btn_blu">수정</a>
                                <a href="javascript:fn_del_menu3(<?=$db_m_idx?>);" class="btn h30 btn_blu">삭제</a>
                            </div>
                        </td>
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
    let image_check = 0;
    $(document).ready(function () {
        $('#sport_list').on('change', function () {
            let select_id = $('#sport_list').val();
            $('.league_list').attr('style', 'display: none;');
            $('.select_option_' + select_id).removeAttr('style');
        });

        $('.search_btn').on('click', function () {
            let sport_id = $('#sport_list').val();
            let league_id = $('#league_list').val();
            let bet_status_id = $('#bet_status_list').val();
            let team_name = $('#team_name').val();

            alert(sport_id + ' ' + league_id + ' ' + bet_status_id + ' ' + team_name);

            location.href = '/sports_w/prematch_manager.php?s_id='+sport_id+'&l_id='+league_id+'&bs_id='+bet_status_id+'&tn='+team_name;
        });
    })
    
    /*const fn_btn_sports_send = function(idx) {
        if(idx != image_check){
            alert("파일을 첨부해 주세요.");
            return;
        }

        $("#thumbnail_fm_"+idx).submit();
        alert('수정했습니다.');
    };
        
    function fn_insert_menu3() {
        var id = $('#id').val();
        var name = $('#name').val();
        //var sport = $("#sport_list option:selected").val();
        var isUse = $("#is_use option:selected").val();
        
	var str_msg = '종목을 등록하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_manager_prc_insert.php',
			data:{'id':id, 'name':name, 'isUse':isUse},
			success: function (result) {
				if(result['retCode'] == "1000"){
					alert('등록하였습니다.');
					window.location.reload();
					return;
				}else{
					alert(result['retMsg']);
					return;
				}
			},
			error: function (request, status, error) {
				alert('등록에 실패하였습니다.');
				return;
			}
		});
	}
	else {
		return;
	}
    }*/
    
    function fn_del_menu3(idx) {
	var str_msg = '종목을 삭제하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_manager_prc_del.php',
			data:{'idx':idx},
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
	}
	else {
		return;
	}
    }
    
    // 종목 수정
    function fn_update_menu3(idx, id) {
        if(idx == image_check){
            //alert("파일을 첨부해 주세요.");
            $("#thumbnail_fm_"+idx).submit();
            let file_name = $('#update_name_'+idx).val();
            let check_name = 'icon_game' + id + '.png';
            console.log(file_name);
            if(file_name !== check_name){
                alert("파일명은 icon_game종목ID.png로 해주셔야 합니다.");
                return;
            }
        }

        var name = $('#name_'+idx).val();
        var isUse = $("#is_use_"+idx+" option:selected").val();
        
	var str_msg = '수정 하시겠습니까?';
	var result = confirm(str_msg);
	if (result){
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: '/sports_w/_sports_manager_prc_update.php',
			data:{'idx':idx, 'name':name, 'isUse':isUse},
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
    // 이미지 썸네일 추가 ADD KSG 
    function setThumbnail(event, idx) {
        var reader = new FileReader();
        $('#update_name_'+idx).val(event.target.files[0].name);
        reader.onload = function(event) {
            var img = document.createElement("img");
            img.setAttribute("src", event.target.result);
            document.querySelector("div.image_container_"+idx).appendChild(img);
            image_check = idx;
        };
        
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
</html>
