<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();


if(0 != $_SESSION['u_business']){
    die();
}


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$set_type_arr = array("service_site", "service_charge", "service_exchange", "service_board", "service_bonus_folder", "odds_3_folder_bonus"
    ,"odds_5_folder_bonus", "mini_service_powerball", "mini_service_v_soccer", "mini_service_power_ladder", "mini_service_baccarat"
    , "mini_service_kino_ladder", "mini_service_roulette", "mini_service_power_pk", "mini_service_hilow", "service_coin_charge"
    , "odds_6_folder_bonus", "odds_7_folder_bonus", "service_sports", "service_real", "service_casino", "service_slot"
    , "service_esports", "service_kiron", "service_hash", "odds_4_folder_bonus","service_classic","mini_service_eos_powerball","service_holdem");

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $in_sql = "";
    
    foreach ($set_type_arr as $key => $val) {
        if ($in_sql != '') {
            $in_sql .= ",";
        }
        
        $in_sql .= "'$val'";
    }
    
    $p_data['sql'] = " SELECT set_type, set_type_val FROM t_game_config ";
    $p_data['sql'] .= " WHERE idx > 0 AND set_type IN ($in_sql) " ;
    
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            
            $db_set_type = $row['set_type'];
            
            foreach ($set_type_arr as $key => $val) {
            
                if ($db_set_type == $val) {
                
                    $db_type[$db_set_type] = $row['set_type_val'];
                    break;
                }
                
            }
        }
    }
    //print_r($db_type);
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

    $menu_name = "site_config";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>사이트 설정</h4>
            </a>
        </div>
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>        
        <!-- list -->
        <div class="panel reserve">   
            <div class="panel_tit">
                <a href="javascript:;" onClick="setConfigWeb('w_config_all');" class="btn h30 btn_blu" style="color: white">전체 저장</a>
            </div>
            
            <div class="tline">
                <table class="mlist">

                <!-- 점검여부 -->

                    <tr>
                        <td width="25%" style="vertical-align: top">
                            <div class="tline">
                            	<table class="mlist">
                                    <tr>
                                        <td style="text-align:left;background-color:#6F6F6F;color:#fff">&nbsp;&nbsp;점검 여부</td>
                                        <td style="text-align:right;background-color:#6F6F6F;color:#fff">
											<a href="javascript:;" onClick="setConfigWeb('w_site');" class="btn h25 btn_blu" style="color: white">저 장</a>
                                        &nbsp;&nbsp;
                                        </td>
                                    </tr>
                                	<tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;사이트 점검</td>
                                        <td >
                                        	<div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_site" id="radio_css_1" value="Y" <?php if ($db_type[$set_type_arr[0]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_1">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_site" id="radio_css_2" value="N" <?php if ($db_type[$set_type_arr[0]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_2">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;충전 점검</td>
                                        <td >
                                        	<div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_charge" id="radio_css_3" value="Y" <?php if ($db_type[$set_type_arr[1]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_3">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_charge" id="radio_css_4" value="N" <?php if ($db_type[$set_type_arr[1]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_4">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;환전 점검</td>
                                        <td >
                                        	<div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_exchange" id="radio_css_5" value="Y" <?php if ($db_type[$set_type_arr[2]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_5">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_exchange" id="radio_css_6" value="N" <?php if ($db_type[$set_type_arr[2]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_6">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;게시판 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_board" id="radio_css_7" value="Y" <?php if ($db_type[$set_type_arr[3]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_7">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_board" id="radio_css_8" value="N" <?php if ($db_type[$set_type_arr[3]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_8">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;코인충전 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_coin_charge" id="radio_css_27" value="Y" <?php if ($db_type[$set_type_arr[15]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_27">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_coin_charge" id="radio_css_28" value="N" <?php if ($db_type[$set_type_arr[15]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_28">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;스포츠 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_sports" id="radio_css_29" value="Y" <?php if ($db_type[$set_type_arr[18]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_29">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_sports" id="radio_css_30" value="N" <?php if ($db_type[$set_type_arr[18]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_30">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;클래식 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_classic" id="radio_css_43" value="Y" <?php if ($db_type[$set_type_arr[26]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_43">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_classic" id="radio_css_44" value="N" <?php if ($db_type[$set_type_arr[26]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_44">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;실시간 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_real" id="radio_css_31" value="Y" <?php if ($db_type[$set_type_arr[19]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_31">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_real" id="radio_css_32" value="N" <?php if ($db_type[$set_type_arr[19]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_32">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;카지노 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_casino" id="radio_css_33" value="Y" <?php if ($db_type[$set_type_arr[20]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_33">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_casino" id="radio_css_34" value="N" <?php if ($db_type[$set_type_arr[20]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_34">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;슬롯머신 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_slot" id="radio_css_35" value="Y" <?php if ($db_type[$set_type_arr[21]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_35">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_slot" id="radio_css_36" value="N" <?php if ($db_type[$set_type_arr[21]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_36">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- // add Holdem -->
                                    <?php  if ('ON' == IS_HOLDEM) { ?>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;홀덤 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_holdem" id="radio_css_51" value="Y" <?php if ($db_type[$set_type_arr[28]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_51">점검중</label>
                                            </div>
                                            &nbsp;&nbsp;
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_holdem" id="radio_css_52" value="N" <?php if ($db_type[$set_type_arr[28]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_52">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php  } ?>

                                    <?php  if ('ON' == IS_ESPORTS_KEYRON) { ?>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;이스포츠 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_esports" id="radio_css_37" value="Y" <?php if ($db_type[$set_type_arr[22]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_37">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_esports" id="radio_css_38" value="N" <?php if ($db_type[$set_type_arr[22]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_38">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="display:none">
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;키론 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_kiron" id="radio_css_39" value="Y" <?php if ($db_type[$set_type_arr[23]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_39">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_kiron" id="radio_css_40" value="N" <?php if ($db_type[$set_type_arr[23]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_40">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <?php  } ?>
                                    
                                    <?php  if ('ON' == IS_HASH) { ?>
                                    <tr>
                                        <td style='width: 150px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;해쉬 점검</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_hash" id="radio_css_41" value="Y" <?php if ($db_type[$set_type_arr[24]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_41">점검중</label>
                                            </div>
                                            &nbsp;&nbsp;
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_hash" id="radio_css_42" value="N" <?php if ($db_type[$set_type_arr[24]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_42">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                   <?php  } ?>
                                </table>
                            </div>
                        </td>

                <!-- 보너스 배당 -->
                    
                        <td width="25%" style="vertical-align: top">
                            <div class="tline">
                            	<table class="mlist">
                                    <tr>
                                        <td style="text-align:left;background-color:#6F6F6F;color:#fff">&nbsp;&nbsp;보너스 배당</td>
                                        <td style="text-align:right;background-color:#6F6F6F;color:#fff">
											<a href="javascript:;" onClick="setConfigWeb('w_bonus');" class="btn h25 btn_blu" style="color: white">저 장</a>
                                        &nbsp;&nbsp;
                                        </td>
                                    </tr>
                                	<tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;보너스 폴더 사용여부</td>
                                        <td >
                                        	<div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="con_bonus_f" id="radio_css_9" value="Y" <?php if ($db_type[$set_type_arr[4]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_9">사용중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="con_bonus_f" id="radio_css_10" value="N" <?php if ($db_type[$set_type_arr[4]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_10">사용안함</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;3폴더 이상 보너스 배당</td>
                                        <td style='text-align:left;'>
                                        	<input id="odds_3" name="odds_3" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_type[$set_type_arr[5]]?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;4폴더 이상 보너스 배당</td>
                                        <td style='text-align:left;'>
                                        	<input id="odds_4" name="odds_4" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_type[$set_type_arr[25]]?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;5폴더 이상 보너스 배당</td>
                                        <td style='text-align:left;'>
                                        	<input id="odds_5" name="odds_5" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_type[$set_type_arr[6]]?>"/>
                                        </td>
                                    </tr>
                                        <tr style="width: 180px;">
                                            <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;6폴더 이상 보너스 배당</td>
                                            <td style='text-align:left;'>
                                                    <input id="odds_6" name="odds_6" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_type[$set_type_arr[16]]?>"/>
                                            </td>
                                        </tr>
                                        <tr style="width: 180px;">
                                            <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;7폴더 이상 보너스 배당</td>
                                            <td style='text-align:left;'>
                                                    <input id="odds_7" name="odds_7" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_type[$set_type_arr[17]]?>"/>
                                            </td>
                                        </tr>
                                </table>
                            </div>
                        </td>

                <!-- 미니게임 -->
                    
                        <td width="25%" style="vertical-align: top">
                            <div class="tline">
                            	<table class="mlist">
                                    <tr>
                                        <td style="text-align:left;background-color:#6F6F6F;color:#fff">&nbsp;&nbsp;미니게임</td>
                                        <td style="text-align:right;background-color:#6F6F6F;color:#fff">
											<a href="javascript:;" onClick="setConfigWeb('w_mini');" class="btn h25 btn_blu" style="color: white">저 장</a>
                                        &nbsp;&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;EOS파워볼</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_eos_pb" id="radio_css_46" value="N" <?php if ($db_type[$set_type_arr[27]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_46">점검중</label>
                                            </div>
                                            <div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_eos_pb" id="radio_css_45" value="Y" <?php if ($db_type[$set_type_arr[27]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_45">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                	<tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;파워볼</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_pb" id="radio_css_12" value="N" <?php if ($db_type[$set_type_arr[7]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_12">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_pb" id="radio_css_11" value="Y" <?php if ($db_type[$set_type_arr[7]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_11">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;파워 사다리</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_p_ladder" id="radio_css_16" value="N" <?php if ($db_type[$set_type_arr[9]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_16">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_p_ladder" id="radio_css_15" value="Y" <?php if ($db_type[$set_type_arr[9]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_15">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;키노 사다리</td>
                                        <td>
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_k_ladder" id="radio_css_20" value="N" <?php if ($db_type[$set_type_arr[11]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_20">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_k_ladder" id="radio_css_19" value="Y" <?php if ($db_type[$set_type_arr[11]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_19">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;가상축구</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_v_soccer" id="radio_css_14" value="N" <?php if ($db_type[$set_type_arr[8]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_14">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_v_soccer" id="radio_css_13" value="Y" <?php if ($db_type[$set_type_arr[8]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_13">사용가능</label>
                                            </div>
                                        </td>
                                    </tr>

<!-- 아래 파워프리킥은 없는걸로 보이는데 체크바람?? -->
                                    <!-- <tr>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;파워 프리킥</td>
                                        <td>
                                        	<div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_power_pk" id="radio_css_23" value="Y" <?php if ($db_type[$set_type_arr[13]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_23">사용가능</label>
                                            </div>
                                            &nbsp;&nbsp;
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_power_pk" id="radio_css_24" value="N" <?php if ($db_type[$set_type_arr[13]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_24">점검중</label>
                                            </div>
                                        </td>
                                    </tr> -->
<!-- 아래 파워프리킥은 없는걸로 보이는데 체크바람?? -->

                                </table>
                            </div>
                        </td>

                        <!-- 미니게임 -->
    
                        <td width="25%" style="vertical-align: top; display:none">
                            <div class="tline">
                            	<table class="mlist">
                                    <tr>
                                        <td style="text-align:left;background-color:#6F6F6F;color:#fff">&nbsp;&nbsp;해시게임</td>
                                        <td style="text-align:right;background-color:#6F6F6F;color:#fff">
											<a href="javascript:;" onClick="setConfigWeb('w_mini');" class="btn h25 btn_blu" style="color: white">저 장</a>
                                        &nbsp;&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                         <?php  if ('ON' == IS_HASH) { ?>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;바카라(해시)</td>
                                        <td >
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_baccarat" id="radio_css_18" value="N" <?php if ($db_type[$set_type_arr[10]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_18">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_baccarat" id="radio_css_17" value="Y" <?php if ($db_type[$set_type_arr[10]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_17">사용가능</label>
                                            </div>
                                        </td>
                                         <?php } ?>
                                    </tr>
                                    
                                    <tr>
                                         <?php  if ('ON' == IS_HASH) { ?>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;룰렛(해시)</td>
                                        <td>
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_roulette" id="radio_css_22" value="N" <?php if ($db_type[$set_type_arr[12]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_22">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_roulette" id="radio_css_21" value="Y" <?php if ($db_type[$set_type_arr[12]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_21">사용가능</label>
                                            </div>
                                        </td>
                                           <?php } ?>
                                    </tr>
                                    <tr>
                                           <?php  if ('ON' == IS_HASH) { ?>
                                        <td style='width: 180px; padding: 2px;text-align:left;background-color:#f6f6f6;'>&nbsp;&nbsp;하이로우(해시)</td>
                                        <td>
                                            <div class="radio radio-css radio-danger" style="display:inline-block">
                                                <input type="radio" name="mini_hilow" id="radio_css_26" value="N" <?php if ($db_type[$set_type_arr[14]]=='N') {echo 'checked';}?>>
                                                <label for="radio_css_26">점검중</label>
                                            </div>
                                        	<div class="radio radio-css radio-danger ml10" style="display:inline-block">
                                                <input type="radio" name="mini_hilow" id="radio_css_25" value="Y" <?php if ($db_type[$set_type_arr[14]]=='Y') {echo 'checked';}?>>
                                                <label for="radio_css_25">사용가능</label>
                                            </div>
                                        </td>
                                          <?php } ?>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>    
            </div>
            <div class="panel_tit" style="margin-top: 25px;">
                <a href="javascript:;" onClick="setConfigWeb('w_config_all');" class="btn h30 btn_blu" style="color: white">전체 저장</a>
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
</body>
<script>

function setConfigWeb(ptype=null,setidx=-1,txtobj=null,txtobj2=null) {

	var param_url = '/siteconfig_w/_set_config_game.php';
	
	var fm = document.search;
	var regVal = '';
	var str_msg = '';
	var w_bonus_f = '';
	var w_site_f = w_charge_f = w_exchange_f = w_board_f = w_coin_charge_f = w_sports_f = w_real_f = w_classic_f ='';
	var w_mini_pb = w_mini_v_soccer = w_mini_p_ladder = w_mini_baccarat = w_mini_eos_pb = '';
	var w_mini_k_ladder = w_mini_roulette = w_mini_power_pk = w_mini_hilow = '';
	var w_odds_3 = 0;
        var w_odds_4 = 0;
	var w_odds_5 = 0;
        var w_odds_6 = 0;
        var w_odds_7 = 0;
        var w_casino_f = w_slot_f = w_esports_f = w_kiron_f = w_hash_f = w_holdem_f = '';
        //var radio_casino_f = document.querySelector('input[name = "con_casino"]:checked');
        //var radio_slot_f = document.querySelector('input[name = "con_slot"]:checked');
	
	//alert(ptype + ',' + setidx + ',' + txtobj + ',' + txtobj2);

	if (ptype=='w_config_all') {
		str_msg = "변경사항을 적용하시겠습니까?";
	}
			
	if ( (ptype=='w_config_all') || (ptype=='w_site') ) {
		if (str_msg == '') {
			str_msg = "점검 여부를 적용하시겠습니까?";
		}
		
		var radio_site_f = document.querySelector('input[name = "con_site"]:checked');
		var radio_charge_f = document.querySelector('input[name = "con_charge"]:checked');
		var radio_exchange_f = document.querySelector('input[name = "con_exchange"]:checked');
		var radio_board_f = document.querySelector('input[name = "con_board"]:checked');
                var radio_coin_charge_f = document.querySelector('input[name = "con_coin_charge"]:checked');
                var radio_sports_f = document.querySelector('input[name = "con_sports"]:checked');
                var radio_classic_f = document.querySelector('input[name = "con_classic"]:checked');
                var radio_real_f = document.querySelector('input[name = "con_real"]:checked');
                var radio_casino_f = document.querySelector('input[name = "con_casino"]:checked');
                var radio_slot_f = document.querySelector('input[name = "con_slot"]:checked');
                var radio_esports_f = document.querySelector('input[name = "con_esports"]:checked');
                var radio_kiron_f = document.querySelector('input[name = "con_kiron"]:checked');
                var radio_hash_f = document.querySelector('input[name = "con_hash"]:checked');
                var radio_holdem_f = document.querySelector('input[name = "con_holdem"]:checked');

		if(radio_site_f != null) {
                    w_site_f = radio_site_f.value;
		}

		if(radio_charge_f != null) {
                    w_charge_f = radio_charge_f.value;
		}

		if(radio_exchange_f != null) {
                    w_exchange_f = radio_exchange_f.value;
		}

		if(radio_board_f != null) {
                    w_board_f = radio_board_f.value;
		}
                
                if(radio_coin_charge_f != null) {
                    w_coin_charge_f = radio_coin_charge_f.value;
		}
                
                if(radio_sports_f != null) {
                    w_sports_f = radio_sports_f.value;
		}
                
                if(radio_classic_f != null) {
                    w_classic_f = radio_classic_f.value;
		}
                
                if(radio_real_f != null) {
                    w_real_f = radio_real_f.value;
		}
                
                if(radio_casino_f != null) {
                    w_casino_f = radio_casino_f.value;
                }

                if(radio_slot_f != null) {
                    w_slot_f = radio_slot_f.value;
                }
                
                if(radio_esports_f != null) {
                    w_esports_f = radio_esports_f.value;
                }
                
                if(radio_kiron_f != null) {
                    w_kiron_f = radio_kiron_f.value;
                }
                
                if(radio_hash_f != null) {
                    w_hash_f = radio_hash_f.value;
                }
                
                if(radio_holdem_f != null) {
                    w_holdem_f = radio_holdem_f.value;
                }
                
	}

	if ( (ptype=='w_config_all') || (ptype=='w_mini') ) {
		if (str_msg == '') {
			str_msg = "미니게임 설정을 적용하시겠습니까?";
		}
		
                var radio_mini_eos_pb_f = document.querySelector('input[name = "mini_eos_pb"]:checked');
		var radio_mini_pb_f = document.querySelector('input[name = "mini_pb"]:checked');
		var radio_mini_v_soccer_f = document.querySelector('input[name = "mini_v_soccer"]:checked');
		var radio_mini_p_ladder_f = document.querySelector('input[name = "mini_p_ladder"]:checked');
		var radio_mini_baccarat_f = document.querySelector('input[name = "mini_baccarat"]:checked');
		var radio_mini_k_ladder_f = document.querySelector('input[name = "mini_k_ladder"]:checked');
		var radio_mini_roulette_f = document.querySelector('input[name = "mini_roulette"]:checked');
		var radio_mini_power_pk_f = document.querySelector('input[name = "mini_power_pk"]:checked');
		var radio_mini_hilow_f = document.querySelector('input[name = "mini_hilow"]:checked');

                if(radio_mini_eos_pb_f != null) {
			w_mini_eos_pb = radio_mini_eos_pb_f.value;
		}
                
		if(radio_mini_pb_f != null) {
			w_mini_pb = radio_mini_pb_f.value;
		}

		if(radio_mini_v_soccer_f != null) {
			w_mini_v_soccer = radio_mini_v_soccer_f.value;
		}

		if(radio_mini_p_ladder_f != null) {
			w_mini_p_ladder = radio_mini_p_ladder_f.value;
		}

		if(radio_mini_baccarat_f != null) {
			w_mini_baccarat = radio_mini_baccarat_f.value;
		}

		if(radio_mini_k_ladder_f != null) {
			w_mini_k_ladder = radio_mini_k_ladder_f.value;
		}

		if(radio_mini_roulette_f != null) {
			w_mini_roulette = radio_mini_roulette_f.value;
		}

		if(radio_mini_power_pk_f != null) {
			w_mini_power_pk = radio_mini_power_pk_f.value;
		}

		if(radio_mini_hilow_f != null) {
			w_mini_hilow = radio_mini_hilow_f.value;
		}
	}
	
	if ( (ptype=='w_config_all') || (ptype=='w_bonus') ) {
		
		if (str_msg == '') {
			str_msg = "보너스 배당을 적용하시겠습니까?";
		}

		var radio_bonus_f = document.querySelector('input[name = "con_bonus_f"]:checked');

		if(radio_bonus_f != null) {
			w_bonus_f = radio_bonus_f.value;
		}

		w_odds_3 = fm.odds_3.value;
                w_odds_4 = fm.odds_4.value;
		w_odds_5 = fm.odds_5.value;
                w_odds_6 = fm.odds_6.value;
                w_odds_7 = fm.odds_7.value;
		
		if (w_bonus_f == 'Y') {
			if ( (w_odds_3 == '') || (Number.isNaN(Number(w_odds_3))) ) {
				alert('3폴더 이상 보너스 배당 값을 입력해 주세요. 숫자만 입력 가능합니다.');
				fm.odds_3.value='';
				fm.odds_3.focus();
				return;
			}
                        
                        if ( (w_odds_4 == '') || (Number.isNaN(Number(w_odds_4))) ) {
				alert('4폴더 이상 보너스 배당 값을 입력해 주세요. 숫자만 입력 가능합니다.');
				fm.odds_4.value='';
				fm.odds_4.focus();
				return;
			}

			if ( (w_odds_5 == '') || (Number.isNaN(Number(w_odds_5))) ) {
				alert('5폴더 이상 보너스 배당 값을 입력해 주세요. 숫자만 입력 가능합니다.');
				fm.odds_5.value='';
				fm.odds_5.focus();
				return;
			}
                        
                        if ( (w_odds_6 == '') || (Number.isNaN(Number(w_odds_6))) ) {
				alert('6폴더 이상 보너스 배당 값을 입력해 주세요. 숫자만 입력 가능합니다.');
				fm.odds_6.value='';
				fm.odds_6.focus();
				return;
			}
                        
                        if ( (w_odds_7 == '') || (Number.isNaN(Number(w_odds_7))) ) {
				alert('7폴더 이상 보너스 배당 값을 입력해 주세요. 숫자만 입력 가능합니다.');
				fm.odds_7.value='';
				fm.odds_7.focus();
				return;
			}
		}
			
	}
	
	var result = confirm(str_msg);
    if (result){

    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'ptype':ptype,'idx':setidx,'w_site_f':w_site_f,'w_charge_f':w_charge_f,'w_exchange_f':w_exchange_f,'w_board_f':w_board_f,'w_coin_charge_f':w_coin_charge_f
                    ,'w_sports_f':w_sports_f,'w_classic_f':w_classic_f, 'w_real_f':w_real_f
        	    ,'w_bonus_f':w_bonus_f,'w_odds_3':w_odds_3,'w_odds_4':w_odds_4,'w_odds_5':w_odds_5,'w_odds_6':w_odds_6,'w_odds_7':w_odds_7
        	    ,'w_mini_eos_pb' : w_mini_eos_pb,'w_mini_pb':w_mini_pb,'w_mini_v_soccer':w_mini_v_soccer
        	    ,'w_mini_p_ladder':w_mini_p_ladder,'w_mini_baccarat':w_mini_baccarat,'w_mini_k_ladder':w_mini_k_ladder
        	    ,'w_mini_roulette':w_mini_roulette,'w_mini_power_pk':w_mini_power_pk,'w_mini_hilow':w_mini_hilow
                    ,'w_casino_f':w_casino_f,'w_slot_f':w_slot_f
                    ,'w_esports_f':w_esports_f,'w_kiron_f':w_kiron_f,'w_hash_f':w_hash_f,'w_holdem_f':w_holdem_f},
    	    success: function (data) {
    	    	if(data['retCode'] == "1000"){
    	    		window.location.reload();
    			}
    	    	else if(data['retCode'] == "2001"){
    	    		alert('잘못된 요청 입니다.');
    			}
    	    	else {
        	    	alert('실패 하였습니다.');
    	    		//window.location.reload();
    	    	}
    	    },
    	    error: function (request, status, error) {
    	    	alert('서버 오류 입니다.');
	    		window.location.reload();
    	    }
    	});
    }
    

	return;
}

</script>
</html>