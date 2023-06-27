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

$MEMAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    
    $sp = array();
    $result = $MEMAdminDAO->getSportsList();
    foreach ($result as $key => $value) {
        $sp[$value['id']] = $value['display_name'];
    }
    
    $p_data['sql'] = "select id, name from lsports_bookmaker where is_use = 1";
    $result = $MEMAdminDAO->getQueryData($p_data);
    $arrBookMaker = array();
    foreach ($result as $key => $value) {
        $arrBookMaker[$value['id']] = $value['name'];
    }
    
    $p_data['sql'] = "select sports_id, provider from average_dividend_provider where sports_id > 0";
    $result = $MEMAdminDAO->getQueryData($p_data);

    $db_dataArr = array();
    foreach ($result as $key => $value) {
        $db_dataArr[$value['sports_id']] = $value['provider'];
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

    $menu_name = "average_dividend_provider";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>배당사 설정</h4>
            </a>
        </div>
        <!-- list -->
            <!-- detail search -->
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
    <div class="panel reserve">
        <div class="panel_tit">
            <!--<span style="color:red">리그, 마켓정보 변경, 배당사 설정,환수율 변경 후에는 동기화 버튼을 눌러주세요.</span><br>
            -->
            <div class="search_form fr">
                <div>
                <select id="resetBetType">
                    <option value="1">스포츠</option>
                </select>
                </div>
                <a href="#" class="btn h30 btn_blu" onClick="fnResetBetPrice(1, '<?=INITDATA_PRE_URL?>','<?=INITDATA_REAL_URL?>');">배당 리플래쉬</a>
            </div>
        </div>
        <div class="tline">
            <table class="mlist">
                <tr>
                    <th>종목</th>
                    <th>포함된 배당사</th>
                    <th>제외된 배당사</th>
                    <th>배당사</th>
                    <th>적용</th>
                </tr>
                <tbody id="charge_event_tbody">
<?php
    foreach ($db_dataArr as $key => $value) {
        $arrValue = explode(',', $value);
        $includeProvider = array();
        $viewProvider = array();
        foreach ($arrBookMaker as $bookMaker_id => $item) {
            if(!in_array($bookMaker_id, $arrValue)){
                $includeProvider[] = $item;
            }
        }
        
        foreach ($arrValue as $provider_id) {
            if(-1 == $provider_id){
                $viewProvider[] = '없음';
            }else{
                $viewProvider[] = $arrBookMaker[$provider_id];
            }
        }
?>
                <tr>
                    <td><?=$sp[$key]?></td>
                    <input id="provider_<?=$key?>" type="hidden" class="" style="width: 100%" placeholder="" value="<?=$value?>"/>
                    <td><?=implode(',', $includeProvider)?></td>
                    <td><?=implode(',', $viewProvider)?></td>
                    <td width ="130">
                    <select id="book_maker_<?=$key?>" style="display: inline-block; width: 100%">
                        <?php foreach ($arrBookMaker as $id => $name) { ?>
                            <option value="<?= $id ?>"><?= $name ?></option>
                        <?php } ?>
                    </select>
                    </td>
                    <td><a href="javascript:;" onClick="setProvider(<?=$key?>, 1);" class="btn h30 btn_blu">제외</a>
                    <a href="javascript:;" onClick="setProvider(<?=$key?>, 2);" class="btn h30 btn_blu">추가</a></td>
                </tr>
<?php 
    }
?>
                </tbody>
            </table>
        </div>
    </div>
</form>
        <!-- END list -->
    </div>
    <!-- END Contents -->
</div>

<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
<script>
function setProvider(sports_id, type) {
    let provider = $('#provider_'+sports_id).val();
    let book_maker = $('#book_maker_'+sports_id).val();
    let arrProvider = provider.split(',');
    
    // 1- 제외된 배당사 추가, 2 - 포함된 배당사 추가
    if(1 == type){
        if(-1 < jQuery.inArray(book_maker, arrProvider)){
            alert('이미 제외된 배당사 입니다.');
            return;
        }
        
        // 0(없음)이 있을시 제거
        let index = jQuery.inArray('-1', arrProvider);
        if(-1 != index){
            arrProvider.splice(index, 1);
        }
        
        arrProvider.push(book_maker);
    }else{
        let index = jQuery.inArray(book_maker, arrProvider);
        if(-1 == index){
            alert('이미 포함된 배당사입니다.');
            return;
        }
        
        if(arrProvider.length == 1){
            //alert('설정값이 하나밖에 없습니다. 제외할 수 없습니다.');
            arrProvider.splice(index, 1);
            arrProvider.push(-1);
        }else{
            arrProvider.splice(index, 1);
        }
    }
    provider = arrProvider.join(',');

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '/siteconfig_w/_set_average_dividened_provider.php',
        data: {'sports_id': sports_id, 'provider': provider},
        success: function (result) {
            //console.log(result['retCode']);
            if (result['retCode'] == "1000") {
                alert('업데이트 되었습니다.');
                window.location.reload();
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

// 프리매치 동기화
function initData() {
    //let url = 'http://210.175.73.208/api/initData';
    let url = 'http://192.168.0.7/test/phpinfo';
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: url,
        data: {},
        success: function (result) {
            alert('test');
            //console.log(result['retCode']);
            if (result['retCode'] == "1000") {
                alert('업데이트 되었습니다.');
                window.location.reload();
            } else {
                alert('업데이트 실패 (1)');
                return;
            }
        },
        error: function (request, status, error) {
            console.log(error);
            alert('업데이트 실패 (2)');
            return;
        }
    });
}
</script>

</html>
