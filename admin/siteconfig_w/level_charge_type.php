<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end

$arrNames = array('캐쉬','코인','가상계좌','개인계좌1','개인계좌2');
$arr_charge_type = array(1,2,3,4,5);
$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    // 문구, 온오프
    $p_data['sql'] = " SELECT * FROM tb_static_bonus";
    
    $db_dataBaseArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'],[]);
    
    //  가입첫충
    $p_data['sql'] = " SELECT set_type_val FROM t_game_config where set_type = ?";
    $db_gameConfig = $MEMAdminDAO->getQueryData_pre($p_data['sql'], ['reg_first_charge']);
    $db_reg_first_charge = $db_gameConfig[0]['set_type_val'];


    $p_data['sql'] = " SELECT set_type_val FROM t_game_config where set_type in (?,?,?,?,?)";
    $db_bonus_option = $MEMAdminDAO->getQueryData_pre($p_data['sql'], ['bonus_option_1','bonus_option_2','bonus_option_3','bonus_option_4','bonus_option_5']);
    $bonus_opt1 = $db_bonus_option[0]['set_type_val'];
    $bonus_opt2 = $db_bonus_option[1]['set_type_val'];
    $bonus_opt3 = $db_bonus_option[2]['set_type_val'];
    $bonus_opt4 = $db_bonus_option[3]['set_type_val'];
    $bonus_opt5 = $db_bonus_option[4]['set_type_val'];
    
    $p_data['sql'] = " SELECT * FROM charge_type ";
    $p_data['sql'] .= " ORDER BY level" ;
    
    $db_dataArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'],[]);
    /*if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
            $level = $row['level'];
            
            $level_account[$level]['idx'] = $row['idx'];
            $level_account[$level]['account_name'] = trim($row['account_name']);
            $level_account[$level]['account_bank'] = trim($row['account_bank']);
            $level_account[$level]['account_number'] = trim($row['account_number']);
            $level_account[$level]['display_account_bank'] = trim($row['display_account_bank']);
            $level_account[$level]['update_dt'] = trim($row['update_dt']);
        }
    }
    
    $p_data_bank['sql'] = "select idx, account_code, account_name from account ";
    $db_data_bank = $MEMAdminDAO->getQueryData($p_data_bank);*/
    
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

    $menu_name = "level_charge_type";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">

        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>레벨별 충전방식 설정</h4>
            </a>
        </div>

        <!-- detail search -->
        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※ 가입 첫충 보너스 퍼센트 및 레벨별 배팅금액을 설정해주세요.
            </div>

            <table class="mlist mline mt10">
                <tr>
                    <th width="200px">가입첫충</th>
                    <td>
                    	<table class='table_noline'>
                            <tr>
                                <td>
                                    <input id="con_reg_first" name="con_reg_first" type="text" style="width: 150px" value="<?=$db_reg_first_charge?>">
                                </td>
                                <td> % &nbsp;&nbsp;</td>
                                <td style="width: 100%; padding:2px; text-align:left;"> 
                                    <a href="javascript:;" onClick="setConfigRegFirst();" class="btn h30 btn_blu">저장</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※ 충전시 보너스 포인트 지급 조건 및 기능동작을 설정할 수 있습니다.
            </div>

            <table class="mlist mline mt10">
                <tr>
                    <th width="600px">기본 보너스 옵션 기준</th>
                    <th width="200px">기본 보너스 옵션 설정</th>
                    <th width="200px">보너스 옵션 1 설정</th>
                    <th width="200px">보너스 옵션 2 설정</th>
                    <th width="200px">보너스 옵션 3 설정</th>
                    <th width="200px">보너스 옵션 4 설정</th>
                    <th width="200px">보너스 옵션 5 설정</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="desc" class="" style="width: 400px" value="<?=$db_dataBaseArr[0]['desc']?>">
                        <a href="javascript::" onclick="fn_update_bonus_desc(1)" class="btn h30 btn_blu">등록</a>
                    </td>
                    <td>
                        <?php if ($db_dataBaseArr[0]['flag'] == 'ON') { ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'OFF', 1)" class="btn h30 btn_green"> ON</a>
                        <?php }else{ ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'ON', 1)" class="btn h30 btn_gray"> OFF</a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($db_dataBaseArr[0]['bonus_1_flag'] == 'ON') { ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'OFF', 2)" class="btn h30 btn_green"> ON</a>
                        <?php }else{ ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'ON', 2)" class="btn h30 btn_gray"> OFF</a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($db_dataBaseArr[0]['bonus_2_flag'] == 'ON') { ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'OFF', 3)" class="btn h30 btn_green"> ON</a>
                        <?php }else{ ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'ON', 3)" class="btn h30 btn_gray"> OFF</a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($db_dataBaseArr[0]['bonus_3_flag'] == 'ON') { ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'OFF', 4)" class="btn h30 btn_green"> ON</a>
                        <?php }else{ ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'ON', 4)" class="btn h30 btn_gray"> OFF</a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($db_dataBaseArr[0]['bonus_4_flag'] == 'ON') { ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'OFF', 5)" class="btn h30 btn_green"> ON</a>
                        <?php }else{ ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'ON', 5)" class="btn h30 btn_gray"> OFF</a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($db_dataBaseArr[0]['bonus_5_flag'] == 'ON') { ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'OFF', 6)" class="btn h30 btn_green"> ON</a>
                        <?php }else{ ?>
                            <a href="javascript::" onclick="betOnOffBtnClick(this, 'ON', 6)" class="btn h30 btn_gray"> OFF</a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        

        <!-- list -->
        <div class="panel reserve">
            <div class="panel_tit">
                <div class="search_form fl">
                    <div class="" style="padding-right: 10px;color:#f89d1b!important">
            			※ 레벨별 충전방식 및 첫충/매충 보너스 퍼센트 및 금액을 설정할 수 있습니다.
                    </div>
                </div>
            </div>
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
    <div class="tline">
        <table class="mlist noline_r">
            <tr>
                <th rowspan="3">레벨</th>
                <th rowspan="2">충전방식</th>
                <th rowspan="2">이름(Front)</th>
                <th colspan="2"><?=$bonus_opt1 ?></th>
                <th colspan="3"><input type="text" id="bonus_opt1" style="width:100%" value="" placeholder="보너스 옵션1의 명칭을 적어주세요"></th>
                <th><a href="#" class="btn h30 btn_blu" onClick="bonus_option_update('bonus_option_1',1)">등록</a></th>
                <th colspan="2"><?=$bonus_opt2 ?></th>
                <th colspan="3"><input type="text" id="bonus_opt2" style="width:100%" value="" placeholder="보너스 옵션2의 명칭을 적어주세요"></th>
                <th><a href="#" class="btn h30 btn_blu" onClick="bonus_option_update('bonus_option_2',2)">등록</a></th>
                <th rowspan="2">수정</th>
            </tr>
            <tr>
                <th>첫충 고정(원)</th>
                <th>첫충 비율(%)</th>
                <th>첫충 최대(원)</th>
                <th>매충 고정(원)</th>
                <th>매충 비율(%)</th>
                <th>매충 최대(원)</th>
                <th>첫충 고정(원)</th>
                <th>첫충 비율(%)</th>
                <th>첫충 최대(원)</th>
                <th>매충 고정(원)</th>
                <th>매충 비율(%)</th>
                <th>매충 최대(원)</th>
            </tr>
            <tr>
                <th colspan="2">보너스 옵션 기준 설정</th>
                <td colspan="6"><input type="text" id="bonus_1_desc" style="width: 100%;" value="<?=$db_dataBaseArr[0]['bonus_1_desc']?>"></td>
                <td colspan="6"><input type="text" id="bonus_2_desc" style="width: 100%;" value="<?=$db_dataBaseArr[0]['bonus_2_desc']?>"></td>
                <td><a href="#" class="btn h30 btn_blu" onClick="fn_update_bonus_desc(2)">등록</a></td>
            </tr>
<?php 
    foreach ($db_dataArr as $key => $value) {
?>
        <tr>
            <td><?=$value['level']?></td>
            <td style="text-align: center;width: 200px">
                <select id="select_<?=$value['level']?>" style="width: 160px;">
                    <?php
                    if(!empty($arr_charge_type)){
                        foreach($arr_charge_type as $row) {
                    ?>
                    <option value="<?=$row?>" <?php if ($row==$value['charge_type']) {echo "selected";}?>><?=$arrNames[$row-1]?></option>
                    <?php
                         }
                     }
                    ?>
                </select>
            </td>
            <td><input type="text" id="name_<?=$value['level']?>" value="<?=$value['name']?>" style="width: 100%;"></td>
            <td><input type="text" id="bonus_1_charge_first_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_1_charge_first_money']?>"></td>
            <td><input type="text" id="bonus_1_charge_first_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_1_charge_first_per']?>"></td>
            <td><input type="text" id="bonus_1_charge_first_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_1_charge_first_max_money']?>"></td>
            <td><input type="text" id="bonus_1_charge_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_1_charge_money']?>"></td>
            <td><input type="text" id="bonus_1_charge_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_1_charge_per']?>"></td>
            <td><input type="text" id="bonus_1_charge_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_1_charge_max_money']?>"></td>
            
            <td><input type="text" id="bonus_2_charge_first_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_2_charge_first_money']?>"></td>
            <td><input type="text" id="bonus_2_charge_first_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_2_charge_first_per']?>"></td>
            <td><input type="text" id="bonus_2_charge_first_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_2_charge_first_max_money']?>"></td>
            <td><input type="text" id="bonus_2_charge_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_2_charge_money']?>"></td>
            <td><input type="text" id="bonus_2_charge_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_2_charge_per']?>"></td>
            <td><input type="text" id="bonus_2_charge_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_2_charge_max_money']?>"></td>

            <td><a href="#" class="btn h30 btn_blu" onClick="fn_update_charge_type(<?= $value['level'] ?>);">수정</a></td>
        </tr>
<?php    
    }
?>                    
        </table>
    </div>


    <div class="mt20 tline">
        <table class="mlist">
            <tr>
                <th rowspan="3">레벨</th>
                <th colspan="2"><?=$bonus_opt3 ?></th>
                <th colspan="3"><input type="text" id="bonus_opt3" style="width:100%" value="" placeholder="보너스 옵션3의 명칭을 적어주세요"></th>
                <th><a href="#" class="btn h30 btn_blu" onClick="bonus_option_update('bonus_option_3',3)">등록</a></th>
                <th colspan="2"><?=$bonus_opt4 ?></th>
                <th colspan="3"><input type="text" id="bonus_opt4" style="width:100%" value="" placeholder="보너스 옵션4의 명칭을 적어주세요"></th>
                <th><a href="#" class="btn h30 btn_blu" onClick="bonus_option_update('bonus_option_4',4)">등록</a></th>
                <th colspan="2"><?=$bonus_opt5 ?></th>
                <th colspan="3"><input type="text" id="bonus_opt5" style="width:100%" value="" placeholder="보너스 옵션5의 명칭을 적어주세요"></th>
                <th><a href="#" class="btn h30 btn_blu" onClick="bonus_option_update('bonus_option_5',5)">등록</a></th>
                <th rowspan="2">수정</th>
            </tr>
            <tr>
                <th>첫충 고정(원)</th>
                <th>첫충 비율(%)</th>
                <th>첫충 최대(원)</th>
                <th>매충 고정(원)</th>
                <th>매충 비율(%)</th>
                <th>매충 최대(원)</th>
                <th>첫충 고정(원)</th>
                <th>첫충 비율(%)</th>
                <th>첫충 최대(원)</th>
                <th>매충 고정(원)</th>
                <th>매충 비율(%)</th>
                <th>매충 최대(원)</th>
                <th>첫충 고정(원)</th>
                <th>첫충 비율(%)</th>
                <th>첫충 최대(원)</th>
                <th>매충 고정(원)</th>
                <th>매충 비율(%)</th>
                <th>매충 최대(원)</th>
            </tr>
            <tr>
                <td colspan="6"><input type="text" id="bonus_3_desc" style="width: 100%;" value="<?=$db_dataBaseArr[0]['bonus_3_desc']?>"></td>
                <td colspan="6"><input type="text" id="bonus_4_desc" style="width: 100%;" value="<?=$db_dataBaseArr[0]['bonus_4_desc']?>"></td>
                <td colspan="6"><input type="text" id="bonus_5_desc" style="width: 100%;" value="<?=$db_dataBaseArr[0]['bonus_5_desc']?>"></td>
                <td><a href="#" class="btn h30 btn_blu" onClick="fn_update_bonus_desc(3)">등록</a></td>
            </tr>
<?php 
    foreach ($db_dataArr as $key => $value) {
?>
        <tr>
            <td><?=$value['level']?></td>

            <td><input type="text" id="bonus_3_charge_first_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_3_charge_first_money']?>"></td>
            <td><input type="text" id="bonus_3_charge_first_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_3_charge_first_per']?>"></td>
            <td><input type="text" id="bonus_3_charge_first_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_3_charge_first_max_money']?>"></td>
            <td><input type="text" id="bonus_3_charge_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_3_charge_money']?>"></td>
            <td><input type="text" id="bonus_3_charge_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_3_charge_per']?>"></td>
            <td><input type="text" id="bonus_3_charge_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_3_charge_max_money']?>"></td>
            
            <td><input type="text" id="bonus_4_charge_first_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_4_charge_first_money']?>"></td>
            <td><input type="text" id="bonus_4_charge_first_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_4_charge_first_per']?>"></td>
            <td><input type="text" id="bonus_4_charge_first_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_4_charge_first_max_money']?>"></td>
            <td><input type="text" id="bonus_4_charge_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_4_charge_money']?>"></td>
            <td><input type="text" id="bonus_4_charge_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_4_charge_per']?>"></td>
            <td><input type="text" id="bonus_4_charge_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_4_charge_max_money']?>"></td>

            <td><input type="text" id="bonus_5_charge_first_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_5_charge_first_money']?>"></td>
            <td><input type="text" id="bonus_5_charge_first_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_5_charge_first_per']?>"></td>
            <td><input type="text" id="bonus_5_charge_first_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_5_charge_first_max_money']?>"></td>
            <td><input type="text" id="bonus_5_charge_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_5_charge_money']?>"></td>
            <td><input type="text" id="bonus_5_charge_per_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_5_charge_per']?>"></td>
            <td><input type="text" id="bonus_5_charge_max_money_<?=$value['level']?>" class="" style="width: 100%; text-align:right;" value="<?=$value['bonus_5_charge_max_money']?>"></td>

            <td><a href="#" class="btn h30 btn_blu" onClick="fn_update_charge_type(<?= $value['level'] ?>);">수정</a></td>
        </tr>
<?php    
    }
?>                    
        </table>
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
function fn_update_charge_type(level) {
    let select = $('#select_'+level).val();
    let name = $('#name_'+level).val();
    
    let bonus_1_charge_first_money = $('#bonus_1_charge_first_money_'+level).val();
    let bonus_1_charge_first_per = $('#bonus_1_charge_first_per_'+level).val();
    let bonus_1_charge_first_max_money = $('#bonus_1_charge_first_max_money_'+level).val();
    let bonus_1_charge_money = $('#bonus_1_charge_money_'+level).val();
    let bonus_1_charge_per = $('#bonus_1_charge_per_'+level).val();
    let bonus_1_charge_max_money = $('#bonus_1_charge_max_money_'+level).val();
    
    let bonus_2_charge_first_money = $('#bonus_2_charge_first_money_'+level).val();
    let bonus_2_charge_first_per = $('#bonus_2_charge_first_per_'+level).val();
    let bonus_2_charge_first_max_money = $('#bonus_2_charge_first_max_money_'+level).val();
    let bonus_2_charge_money = $('#bonus_2_charge_money_'+level).val();
    let bonus_2_charge_per = $('#bonus_2_charge_per_'+level).val();
    let bonus_2_charge_max_money = $('#bonus_2_charge_max_money_'+level).val();

    let bonus_3_charge_first_money = $('#bonus_3_charge_first_money_'+level).val();
    let bonus_3_charge_first_per = $('#bonus_3_charge_first_per_'+level).val();
    let bonus_3_charge_first_max_money = $('#bonus_3_charge_first_max_money_'+level).val();
    let bonus_3_charge_money = $('#bonus_3_charge_money_'+level).val();
    let bonus_3_charge_per = $('#bonus_3_charge_per_'+level).val();
    let bonus_3_charge_max_money = $('#bonus_3_charge_max_money_'+level).val();

    let bonus_4_charge_first_money = $('#bonus_4_charge_first_money_'+level).val();
    let bonus_4_charge_first_per = $('#bonus_4_charge_first_per_'+level).val();
    let bonus_4_charge_first_max_money = $('#bonus_4_charge_first_max_money_'+level).val();
    let bonus_4_charge_money = $('#bonus_4_charge_money_'+level).val();
    let bonus_4_charge_per = $('#bonus_4_charge_per_'+level).val();
    let bonus_4_charge_max_money = $('#bonus_4_charge_max_money_'+level).val();

    let bonus_5_charge_first_money = $('#bonus_5_charge_first_money_'+level).val();
    let bonus_5_charge_first_per = $('#bonus_5_charge_first_per_'+level).val();
    let bonus_5_charge_first_max_money = $('#bonus_5_charge_first_max_money_'+level).val();
    let bonus_5_charge_money = $('#bonus_5_charge_money_'+level).val();
    let bonus_5_charge_per = $('#bonus_5_charge_per_'+level).val();
    let bonus_5_charge_max_money = $('#bonus_5_charge_max_money_'+level).val();

    let mes = '선택하신 내용으로 수정하시겠습니까?'
    if (confirm(mes) == false) {
        return;
    }
    
    if(0 >= bonus_1_charge_first_max_money && 0 >= bonus_1_charge_max_money 
            && 0 >= bonus_2_charge_first_max_money && 0 >= bonus_2_charge_max_money && 0 >= bonus_3_charge_max_money && 0 >= bonus_4_charge_max_money && 0 >= bonus_5_charge_max_money){
        alert('최대값들은 0보다 커야 합니다.');
        return;
    }
    
    var param_val = {
                        level: level, charge_type: select, name: name
                        , bonus_1_charge_first_money: bonus_1_charge_first_money, bonus_1_charge_first_per: bonus_1_charge_first_per, bonus_1_charge_first_max_money: bonus_1_charge_first_max_money
                        , bonus_1_charge_money: bonus_1_charge_money, bonus_1_charge_per: bonus_1_charge_per, bonus_1_charge_max_money: bonus_1_charge_max_money

                        , bonus_2_charge_first_money: bonus_2_charge_first_money, bonus_2_charge_first_per: bonus_2_charge_first_per, bonus_2_charge_first_max_money: bonus_2_charge_first_max_money
                        , bonus_2_charge_money: bonus_2_charge_money, bonus_2_charge_per: bonus_2_charge_per, bonus_2_charge_max_money: bonus_2_charge_max_money

                        , bonus_3_charge_first_money: bonus_3_charge_first_money, bonus_3_charge_first_per: bonus_3_charge_first_per, bonus_3_charge_first_max_money: bonus_3_charge_first_max_money
                        , bonus_3_charge_money: bonus_3_charge_money, bonus_3_charge_per: bonus_3_charge_per, bonus_3_charge_max_money: bonus_3_charge_max_money

                        , bonus_4_charge_first_money: bonus_4_charge_first_money, bonus_4_charge_first_per: bonus_4_charge_first_per, bonus_4_charge_first_max_money: bonus_4_charge_first_max_money
                        , bonus_4_charge_money: bonus_4_charge_money, bonus_4_charge_per: bonus_4_charge_per, bonus_4_charge_max_money: bonus_4_charge_max_money

                        , bonus_5_charge_first_money: bonus_5_charge_first_money, bonus_5_charge_first_per: bonus_5_charge_first_per, bonus_5_charge_first_max_money: bonus_5_charge_first_max_money
                        , bonus_5_charge_money: bonus_5_charge_money, bonus_5_charge_per: bonus_5_charge_per, bonus_5_charge_max_money: bonus_5_charge_max_money
                    };
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/siteconfig_w/_set_charge_type.php',
        //async: false,
        data: param_val,
        success: function(result) {
            if (result['retCode'] == "1000") {
                $('#select_').val(select);
                $('#name_').val(name);
                alert('수정에 성공하였습니다.');
                return;
            }
        },
        error: function(request, status, error) {
            console.log(request);
            alert('수정에 실패하였습니다.');
            return;
        }
    });
}

function betOnOffBtnClick(ateg, status, type) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/siteconfig_w/_set_charge_bonus_onoff.php',
        data: {'status': status, 'type': type},
        success: function (result) {
            console.log(status);
            if (result['retCode'] == "1000") {
                alert('변경되었습니다.');

                if ("ON" == status) {
                    $(ateg).removeClass("btn_gray").removeClass("btn_green").addClass("btn_green");
                    $(ateg).attr("onclick", "betOnOffBtnClick(this, 'OFF', "+type+")");
                    $(ateg).text("ON");
                } else {
                    $(ateg).removeClass("btn_green").removeClass("btn_gray").addClass("btn_gray");
                    $(ateg).attr("onclick", "betOnOffBtnClick(this, 'ON', "+type+")");
                    $(ateg).text("OFF");
                }
                return;
            } else {
                alert('업데이트 실패 (1)');
                return;
            }
        },
        error: function (error) {
            alert('업데이트 실패 (2)');
            console.log(error);

            return;
        }
    });
}

// 문구수정
function fn_update_bonus_desc(type) {
    let desc = $('#desc').val();
    let bonus_1_desc = $('#bonus_1_desc').val();
    let bonus_2_desc = $('#bonus_2_desc').val();
    let bonus_3_desc = $('#bonus_3_desc').val();
    let bonus_4_desc = $('#bonus_4_desc').val();
    let bonus_5_desc = $('#bonus_5_desc').val();

    let mes = '변경하신 문구로 수정하시겠습니까?'
    if (confirm(mes) == false) {
        return;
    }
    
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/siteconfig_w/_set_bonus_desc.php',
        //async: false,
        data: {
            'type': type,
            'desc': desc,
            'bonus_1_desc': bonus_1_desc,
            'bonus_2_desc': bonus_2_desc,
            'bonus_3_desc': bonus_3_desc,
            'bonus_4_desc': bonus_4_desc,
            'bonus_5_desc': bonus_5_desc
        },
        success: function(result) {
            if (result['retCode'] == "1000") {
                if(1 == type){
                    $('#desc').val(desc);
                }else{
                    $('#bonus_1_desc').val(bonus_1_desc);
                    $('#bonus_2_desc').val(bonus_2_desc);
                    $('#bonus_3_desc').val(bonus_3_desc);
                    $('#bonus_4_desc').val(bonus_4_desc);
                    $('#bonus_5_desc').val(bonus_5_desc);
                }
                alert('수정에 성공하였습니다.');
                return;
            }
        },
        error: function(request, status, error) {
            console.log(request);
            alert('수정에 실패하였습니다.');
            return;
        }
    });
}

function setConfigRegFirst() {
    let con_reg_first = $('#con_reg_first').val();

    let mes = '가입첫충을 '+con_reg_first+'%로 변경하시겠습니까?'
    if (confirm(mes) == false) {
        return;
    }
    
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/siteconfig_w/_set_config_reg_first.php',
        //async: false,
        data: {
            'con_reg_first': con_reg_first
        },
        success: function(result) {
            if (result['retCode'] == "1000") {
                alert('수정에 성공하였습니다.');
                return;
            }
        },
        error: function(request, status, error) {
            console.log(request);
            alert('수정에 실패하였습니다.');
            return;
        }
    });
}


function bonus_option_update(bonus_option,index) {

    let bonus_opt = $('#bonus_opt'+index).val();

    
    
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/siteconfig_w/_set_update_bonus_option.php',
        //async: false,
        data: {
            'bonus_option': bonus_option,'txtval': bonus_opt
        },
        success: function(result) {
            if (result['retCode'] == "1000") {
               
                location.reload();
                return;
            }
        },
        error: function(request, status, error) {
            console.log(request);
            alert('수정에 실패하였습니다.');
            return;
        }
    });
}
</script>

</html>
