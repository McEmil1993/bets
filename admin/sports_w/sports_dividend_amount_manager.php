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
    
    $p_data['sql'] = " SELECT idx, u_level, set_type, set_type_val, title, reg_time FROM t_game_config ";
    $p_data['sql'] .= " WHERE set_type IN ('game_level_pre', 'game_level_real') order by u_level ";
    
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    foreach($db_dataArr as $row) {
        
        $db_level = $row['u_level'];
        $db_game_level[$db_level]['level'] = $db_level;
        
        if ($row['set_type']=='game_level_pre') {
            $db_game_level[$db_level]['pre'] = number_format($row['set_type_val']);
        }
        else if ($row['set_type']=='game_level_real') {
            $db_game_level[$db_level]['real'] = number_format($row['set_type_val']);
        }
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
<script type="text/javascript" src="<?=_STATIC_COMMON_PATH?>/js/admConfigBet.js" charset="utf-8"></script>
<body>
<div class="wrap">
    <?php

    $menu_name = "sports_dividend_amount_manager";

    include_once(_BASEPATH.'/common/left_menu.php');

    include_once(_BASEPATH.'/common/iframe_head_menu.php');
    ?>
    <!-- Contents -->
    <div class="con_wrap">
<?php 
include_once(_BASEPATH.'/siteconfig_w/bet_config_game_level_inc.php');
?>
    </div>
    <!-- END Contents -->
</div>
<?php
include_once(_BASEPATH.'/common/bottom.php');
?>
</body>
</html>
