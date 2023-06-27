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
    
    $p_data['sql'] = " SELECT idx, level, account_name, account_bank, account_number, display_account_bank, update_dt FROM account_level_list ";
    $p_data['sql'] .= " ORDER BY level " ;
    
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    if(!empty($db_dataArr)){
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
    $db_data_bank = $MEMAdminDAO->getQueryData($p_data_bank);
    
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

    $menu_name = "account_level";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">

        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>레벨별 계좌 설정</h4>
            </a>
        </div>
        <!-- list -->
        <div class="panel reserve">
            <div class="panel_tit">
                <div class="search_form fl">
                    <div class="" style="padding-right: 10px;color:#f89d1b!important">
            			※ 레벨별 입금계좌를 지정할 수 있습니다.
                    </div>
                </div>
            </div>
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th>레벨</th>
                        <th>은행명</th>
                        <th>은행명(Front)</th>
                        <th>계좌번호</th>
                        <th>예금주</th>
                    </tr>
<?php 
for ($i=1;$i<=10;$i++) {
    
    $db_account_name =$level_account[$i]['account_name'];
    $db_account_bank =$level_account[$i]['account_bank'];
    $db_account_number =$level_account[$i]['account_number'];
    $db_display_account_bank =$level_account[$i]['display_account_bank'];
    
    $td_acc_name = "account_name".$i;
    $td_acc_number = "account_number".$i;
    $td_acc_bank = "account_bank".$i;
    $td_display_acc_bank = "display_account_bank".$i;
?>
                    <tr>
                        <td width="80px"><?=$i?></td>
						<td style="text-align: center;width: 200px">
							<select style="width: 160px;" name="account_bank[]" id="<?=$td_acc_bank?>">
            					<option value="">-- 은행선택 --</option>
            					<?php 
            					if(!empty($db_data_bank)){
            					    foreach($db_data_bank as $row) {
                                ?>
                                <option value="<?=$row['account_code']?>" <?php if ($db_account_bank==$row['account_code']) {echo "selected";}?>><?=$row['account_name']?></option>
                                <?php 
                                     }
                                 }
                                ?>
            				</select>						
						</td>
                        <td><input id="<?=$td_display_acc_bank?>" name="display_account_bank[]" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_display_account_bank?>"/></td>
                        <td><input id="<?=$td_acc_number?>" name="account_number[]" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_account_number?>"/></td>
                        <td><input id="<?=$td_acc_name?>" name="account_name[]" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_account_name?>"/></td>
                    </tr>
<?php    
}
?>                    
                </table>
            </div>
            <div class="panel_tit" style="margin-top: 5px;">
                <table class="mlist">
                    <tr>
                        <td>2차인증 비밀번호</td>
                        <td><input type="password" name="second_pass" id="second_pass" value="" maxlength="6"/></td>
                    </tr>
                </table>
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="setConfig('account_level',0);" class="btn h30 btn_blu">등 록</a>
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

function setConfig(ptype=null,setidx=0) {

	var fm = document.search;

	var param_url = '/siteconfig_w/_set_config.php';
        var second_pass = $('#second_pass').val();
        if (second_pass === '') {
            alert('2차인증 비번을 넣어주세요.');
            return;
        }
	
	var acc_name_arr = ["", "", "", "", "", "", "", "", "", ""];
	var acc_bank_arr = ["", "", "", "", "", "", "", "", "", ""];
	var acc_number_arr = ["", "", "", "", "", "", "", "", "", ""];
        var display_acc_bank_arr = ["", "", "", "", "", "", "", "", "", ""];
	
	var i=0;
	$('input[name="account_name[]"').each(function(idx, item) { 
		var tmpValue = $(item).val();
		acc_name_arr[i]=tmpValue;
		i++; 
	});

	JSON.stringify(acc_name_arr);
	
	i=0;
	$('input[name="account_number[]"').each(function(idx, item) { 
		var tmpValue = $(item).val();
		acc_number_arr[i]=tmpValue;
		i++; 
	});

	JSON.stringify(acc_number_arr);

	i=0;
	$('select[name="account_bank[]"').each(function(idx, item) { 
		var tmpValue = $(item).val();
		acc_bank_arr[i]=tmpValue;
		i++; 
	});
        
	JSON.stringify(acc_bank_arr);
        
        i=0;
	$('input[name="display_account_bank[]"').each(function(idx, item) { 
		var tmpValue = $(item).val();
		display_acc_bank_arr[i]=tmpValue;
		i++; 
	});
        
        JSON.stringify(display_acc_bank_arr);

	var str_msg = '선택하신 계좌설정을 등록 하시겠습니까?';

	var result = confirm(str_msg);
        if (result){
        
    	$.ajax({
    		type: 'post',
    		dataType: 'json',
    	    url: param_url,
    	    data:{'ptype':ptype,'idx':setidx,'acc_number':acc_number_arr,'acc_name':acc_name_arr,'acc_bank':acc_bank_arr,'display_acc_bank':display_acc_bank_arr, 'second_pass':second_pass},
    	    success: function (data) {
    	    	if(data['retCode'] == "1000"){
                    window.location.reload();
                }else if(data['retCode'] == "2001"){
                    alert('잘못된 요청 입니다.');
                }else if(data['retCode'] == "2002") {
                    alert('2차인증 비번이 틀렸습니다.');
                }else {
                    alert('실패 하였습니다.');
                    window.location.reload();
    	    	}
    	    },
    	    error: function (request, status, error) {
    	    	alert('서버 오류 입니다.');
                window.location.reload();
    	    }
    	});
    }
		
}
</script>

</html>
