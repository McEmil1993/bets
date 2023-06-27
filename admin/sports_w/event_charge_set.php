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

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $p_data['sql'] = "SELECT level, bonus, max_bonus, pay_back_value FROM charge_event WHERE level > 0;";
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    $p_data['sql'] = "select set_type, set_type_val from t_game_config where idx > 0 AND set_type in ('event_charge_status','event_charge_start','event_charge_end')";
    $arr_config_result = $MEMAdminDAO->getQueryData($p_data);

    $arr_config = array();
    foreach ($arr_config_result as $key => $value) {
        $game_config[$value['set_type']] = $value['set_type_val'];
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

    $menu_name = "event_charge_set";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">

        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>이벤트 충전 설정</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <!-- detail search -->
            <div class="panel search_box">
                <div style="margin-top: 3px;">
                </div>
                <table class="mlist mline">
                    <tr>
                        <th width="200px">이벤트 상태</th>
                        <td>
                            <table class='table_noline'>
                                <tr>
                                    <td style="width: 210px; padding: 2px;text-align:left;">
                                        <?php if ($game_config['event_charge_status'] == 'ON') { ?>
                                            <a href="#" onclick="betOnOffBtnClick(this, 'OFF')" class="btn h25 btn_green"> ON</a>
                                        <?php } else/* if ($row['admin_bet_status'] == 'OFF') */{ ?>
                                            <a href="#" onclick="betOnOffBtnClick(this, 'ON')" class="btn h25 btn_gray"> OFF</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <th width="200px">이벤트 시간</th>
                        <td>
                            <table class='table_noline'>
                                <tr>
                                    <td style="width: 210px; padding: 2px;text-align:left;">시작</td>
                                    <td style="width: 210px; padding: 2px;text-align:left;">
                                        <input id="start_dt" name="start_dt" type="time" class="" style="width: 150px" placeholder="" value="<?=$game_config['event_charge_start']?>"/>
                                    </td>
                                    <td style="width: 210px; padding: 2px;text-align:left;">종료</td>
                                    <td style="width: 210px; padding: 2px;text-align:left;">
                                        <input id="end_dt" name="end_dt" type="time" class="" style="width: 150px" placeholder="" value="<?=$game_config['event_charge_end']?>"/>
                                    </td>
                                    <td style="width: 100%;padding: 2px;text-align:left;"> 
                                            <a href="javascript:;" onClick="setChargeTime();" class="btn h30 btn_blu">저장</a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </div>
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
    <div class="panel reserve">
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>레벨</th>
                        <th>보너스 (%)</th>
                        <th>최대 보너스 (포인트)</th>
                        <th>본인 죽장 페이백 (%)</th>
                    </tr>
                    <tbody id="charge_event_tbody">
<?php
    foreach ($db_dataArr as $key => $value) {
?>
                    <tr>
                        <td><input id="level_<?=$value['level']?>" type="number" class="" style="width: 100%" placeholder="" value="<?=$value['level']?>" readonly/></td>
                        <input id="current_bonus_<?=$value['level']?>" type="hidden" class="" style="width: 100%" placeholder="" value="<?=number_format($value['bonus'])?>" readonly/>
                        <td><input id="bonus_<?=$value['level']?>" type="number" class="" style="width: 100%" placeholder="" value="<?=number_format($value['bonus'])?>"/></td>
                        <input id="current_max_bonus_<?=$value['level']?>" type="hidden" class="" style="width: 100%" placeholder="" value="<?=$value['max_bonus']?>" readonly/>
                        <td><input id="max_bonus_<?=$value['level']?>" type="number" class="" style="width: 100%" placeholder="" value="<?=$value['max_bonus']?>"/></td>
                        <input id="current_pay_back_value_<?=$value['level']?>" type="hidden" class="" style="width: 100%" placeholder="" value="<?= number_format($value['pay_back_value'], 1) ; ?>" readonly/>
                        <td><input id="pay_back_value_<?=$value['level']?>" type="number" class="" style="width: 100%" placeholder="" value="<?= number_format($value['pay_back_value'], 1) ;?>"/></td>
<?php 
    }
?>
                    </tbody>
                </table>
            </div>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="setConfig();" class="btn h30 btn_blu">등 록</a>
                </div>
            </div>            
        </div>
</form>
        </div>
        <!-- END list -->
    </div>
    <!-- END Contents -->
</div>

<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
<script>
function betOnOffBtnClick(ateg, status) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/sports_w/_event_charge_set_onoff.php',
        data: {'cmd': status},
        success: function (result) {
            //console.log(result['retCode']);
            if (result['retCode'] == "1000") {
                alert('업데이트 되었습니다.');

                if ("on" == status || "ON" == status) {
                    $(ateg).removeClass("btn_gray").removeClass("btn_green").addClass("btn_green");
                    $(ateg).attr("onclick", "betOnOffBtnClick(this, 'OFF')");
                    $(ateg).text("ON");
                } else {
                    $(ateg).removeClass("btn_green").removeClass("btn_gray").addClass("btn_gray");
                    $(ateg).attr("onclick", "betOnOffBtnClick(this, 'ON')");
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

function setChargeTime() {
    let start_dt = $('#start_dt').val();
    let end_dt = $('#end_dt').val();
    
    if(start_dt > end_dt){
        alert('시간시작이 종료시간보다 큽니다.');
    }
    
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/sports_w/_event_charge_set_charge_event_time.php',
        data: {'start_dt': start_dt, 'end_dt': end_dt},
        success: function (result) {
            //console.log(result['retCode']);
            if (result['retCode'] == "1000") {
                alert('업데이트 되었습니다.');
                window.location.reload();
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

// 전체수정
function setConfig() {
    //var input_refund_rate = $('#input_refund_rate_'+idx).val();

    let arrChargeEventData = [];

    $('#charge_event_tbody tr').each(function (index, tr) {
        let level = index + 1;
        let current_bonus = $('#current_bonus_' + level).val();
        let bonus = $('#bonus_' + level).val();
        let current_max_bonus = $('#current_max_bonus_' + level).val();
        let max_bonus = $('#max_bonus_' + level).val();
        let current_pay_back_value = $('#current_pay_back_value_' + level).val();
        let pay_back_value = $('#pay_back_value_' + level).val();
        if(current_bonus !== bonus || current_max_bonus !== max_bonus || current_pay_back_value !== pay_back_value){
           let obj = new Object();
           obj = {'level':level, 'bonus':bonus, 'max_bonus':max_bonus, 'pay_back_value':pay_back_value};
           arrChargeEventData.push(obj);
        }
    });
    if(arrChargeEventData.length == 0){
        alert('수정된 내용이 없습니다.');
        return;
    }
    let chargeEventData = JSON.stringify(arrChargeEventData);
    //console.log(chargeEventData);

    var str_msg = '저장하시겠습니까?';
    var result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/sports_w/_event_charge_set_update_all.php',
            data: {'chargeEventData': chargeEventData},
            success: function (result) {
                if (result['retCode'] == "1000") {
                    alert('저장하였습니다.');
                    window.location.reload();
                    arrChargeEventData.forEach(function (item) {
                        $('#bonus_' + item['idx']).val(item['name']);
                    });
                    return;
                } else {
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

</html>
