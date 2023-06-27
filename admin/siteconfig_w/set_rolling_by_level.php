<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();



if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    // SELECT * FROM `mydb_casino`.`tb_static_rolling_comps`
    $p_data['sql'] = " SELECT * FROM tb_static_rolling_comps ";

    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);


    $p_data['sql'] = " SELECT set_type_val FROM t_game_config where set_type = ?";
    $db_gameConfig = $MEMAdminDAO->getQueryData_pre($p_data['sql'], ['max_betting_alarm']);
    $db_max_betting_alarm = $db_gameConfig[0]['set_type_val'];
    // 다른 유형을 사용하는 경우 주석 해제
    $display_myself_chex = array();
    $display_recommender_chex = array();
    // 
    $display_prematch_s_myself_bet = array();
    $display_prematch_s_recommender_bet = array();
    $display_prematch_d_myself_bet = array();
    $display_prematch_d_recommender_bet = array();
    $display_inplay_s_myself_bet = array();
    $display_inplay_s_recommender_bet = array();
    $display_inplay_d_myself_bet = array();
    $display_inplay_d_recommender_bet = array();
    $display_casino_myself_bet = array();
    $display_casino_recommender_bet = array();
    $display_slot_myself_bet = array();
    $display_slot_recommender_bet = array();
    $display_powerball_myself_bet = array();
    $display_powerball_recommender_bet = array();

    // 

    $display_prematch_s_myself_lose = array();
    $display_prematch_s_recommender_lose = array();
    $display_prematch_d_myself_lose = array();
    $display_prematch_d_recommender_lose = array();
    $display_inplay_s_myself_lose = array();
    $display_inplay_s_recommender_lose = array();
    $display_inplay_d_myself_lose = array();
    $display_inplay_d_recommender_lose = array();
    $display_casino_myself_lose = array();
    $display_casino_recommender_lose = array();
    $display_slot_myself_lose = array();
    $display_slot_recommender_lose = array();
    $display_powerball_myself_lose = array();
    $display_powerball_recommender_lose = array();


    $idx_chex = array();
    $idx_prematch_s = array();
    $idx_prematch_d = array();
    $idx_inplay_s = array();
    $idx_inplay_d = array();
    $idx_casino = array();
    $idx_slot = array();
    $idx_powerball = array();


    if (!empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            for ($i=0; $i <= 10 ; $i++) { 
                if($row['type'] == 'chex'){
                    if($i == $row['level']){
                        $display_myself_chex[$i] = $row['myself_chex'];
                        $display_recommender_chex[$i] = $row['recommender_chex'];

                        $idx_chex[$i] = $row['idx'];
                    }
                }

                if($row['type'] == 'prematch_s'){
                    if($i == $row['level']){
                        $display_prematch_s_myself_bet[$i] = $row['myself_bet'];
                        $display_prematch_s_recommender_bet[$i] = $row['recommender_bet'];

                        $display_prematch_s_myself_lose[$i] = $row['myself_lose'];
                        $display_prematch_s_recommender_lose[$i] = $row['recommender_lose'];

                        $idx_prematch_s[$i] = $row['idx'];
                    }
                }

                if($row['type'] == 'prematch_d'){
                    if($i == $row['level']){
                        $display_prematch_d_myself_bet[$i] = $row['myself_bet'];
                        $display_prematch_d_recommender_bet[$i] = $row['recommender_bet'];

                        $display_prematch_d_myself_lose[$i] = $row['myself_lose'];
                        $display_prematch_d_recommender_lose[$i] = $row['recommender_lose'];

                        $idx_prematch_d[$i] = $row['idx'];
                    }
                }


                if($row['type'] == 'inplay_s'){
                    if($i == $row['level']){
                        $display_inplay_s_myself_bet[$i] = $row['myself_bet'];
                        $display_inplay_s_recommender_bet[$i] = $row['recommender_bet'];

                        $display_inplay_s_myself_lose[$i] = $row['myself_lose'];
                        $display_inplay_s_recommender_lose[$i] = $row['recommender_lose'];

                        $idx_inplay_s[$i] = $row['idx'];
                    }
                }

                if($row['type'] == 'inplay_d'){
                    if($i == $row['level']){
                        $display_inplay_d_myself_bet[$i] = $row['myself_bet'];
                        $display_inplay_d_recommender_bet[$i] = $row['recommender_bet'];

                        $display_inplay_d_myself_lose[$i] = $row['myself_lose'];
                        $display_inplay_d_recommender_lose[$i] = $row['recommender_lose'];

                        $idx_inplay_d[$i] = $row['idx'];
                    }
                }

                if($row['type'] == 'casino'){
                    if($i == $row['level']){
                        $display_casino_myself_bet[$i] = $row['myself_bet'];
                        $display_casino_recommender_bet[$i] = $row['recommender_bet'];

                        $display_casino_myself_lose[$i] = $row['myself_lose'];
                        $display_casino_recommender_lose[$i] = $row['recommender_lose'];


                        $idx_casino[$i] = $row['idx'];
                    }
                }


                if($row['type'] == 'slot'){
                    if($i == $row['level']){
                        $display_slot_myself_bet[$i] = $row['myself_bet'];
                        $display_slot_recommender_bet[$i] = $row['recommender_bet'];

                        $display_slot_myself_lose[$i] = $row['myself_lose'];
                        $display_slot_recommender_lose[$i] = $row['recommender_lose'];

                        $idx_slot[$i] = $row['idx'];
                        
                    }
                }


                if($row['type'] == 'powerball'){
                    if($i == $row['level']){
                        $display_powerball_myself_bet[$i] = $row['myself_bet'];
                        $display_powerball_recommender_bet[$i] = $row['recommender_bet'];

                        $display_powerball_myself_lose[$i] = $row['myself_lose'];
                        $display_powerball_recommender_lose[$i] = $row['recommender_lose'];


                        $idx_powerball[$i] = $row['idx'];
                        
                    }
                }
            }
        }
    }

}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
    <!--<![endif]-->

<?php
include_once(_BASEPATH . '/common/head.php');
?>
    <script>
        $(document).ready(function () {
            App.init();
            FormPlugins.init();

            $('ul.tabs li').click(function () {
                var tab_id = $(this).attr('data-tab');

                $('ul.tabs li').removeClass('current');
                $('.tab-content').removeClass('current');

                $(this).addClass('current');
                $("#" + tab_id).addClass('current');
            })
        });

        function save_rolling() {
            var count  = 0 ;

            for (let index = 1; index <= 10; index++) {
               
                var idx = $('#myself_chex'+index).attr('data-id');
                var chex_myself = $('#myself_chex'+index).val();
                var chex_recommender =  $('#recommender_chex'+index).val(); 

                // save_chex(idx,chex_myself,chex_recommender);

                var idx_prematch_s_myself_bet = $('#prematch_s_myself_bet'+index).attr('data-id');
                var prematch_s_myself_bet = $('#prematch_s_myself_bet'+index).val();
                var prematch_s_recommender_bet =  $('#prematch_s_recommender_bet'+index).val(); 

                var prematch_s_myself_lose = $('#prematch_s_myself_lose'+index).val();
                var prematch_s_recommender_lose =  $('#prematch_s_recommender_lose'+index).val(); 

                // save_self(idx_prematch_s_myself_bet,prematch_s_myself_bet,prematch_s_recommender_bet,prematch_s_myself_lose,prematch_s_recommender_lose)

                var idx_prematch_d_myself_bet = $('#prematch_d_myself_bet'+index).attr('data-id');
                var prematch_d_myself_bet = $('#prematch_d_myself_bet'+index).val();
                var prematch_d_recommender_bet =  $('#prematch_d_recommender_bet'+index).val(); 

                var prematch_d_myself_lose = $('#prematch_d_myself_lose'+index).val();
                var prematch_d_recommender_lose =  $('#prematch_d_recommender_lose'+index).val(); 

                // save_self(idx_prematch_d_myself_bet,prematch_d_myself_bet,prematch_d_recommender_bet,prematch_d_myself_lose,prematch_d_recommender_lose)


                var idx_inplay_s_myself_bet = $('#inplay_s_myself_bet'+index).attr('data-id');
                var inplay_s_myself_bet = $('#inplay_s_myself_bet'+index).val();
                var inplay_s_recommender_bet =  $('#inplay_s_recommender_bet'+index).val(); 

                var inplay_s_myself_lose = $('#inplay_s_myself_lose'+index).val();
                var inplay_s_recommender_lose =  $('#inplay_s_recommender_lose'+index).val(); 
                // save_self(idx_inplay_s_myself_bet,inplay_s_myself_bet,inplay_s_recommender_bet,inplay_s_myself_lose,inplay_s_recommender_lose)


                var idx_inplay_d_myself_bet = $('#inplay_d_myself_bet'+index).attr('data-id');
                var inplay_d_myself_bet = $('#inplay_d_myself_bet'+index).val();
                var inplay_d_recommender_bet =  $('#inplay_d_recommender_bet'+index).val(); 

                var inplay_d_myself_lose = $('#inplay_d_myself_lose'+index).val();
                var inplay_d_recommender_lose =  $('#inplay_d_recommender_lose'+index).val();
                
                // save_self(idx_inplay_d_myself_bet,inplay_d_myself_bet,inplay_d_recommender_bet,inplay_d_myself_lose,inplay_d_recommender_lose)
                
                
                var idx_casino_myself_bet = $('#casino_myself_bet'+index).attr('data-id');
                var casino_myself_bet = $('#casino_myself_bet'+index).val();
                var casino_recommender_bet =  $('#casino_recommender_bet'+index).val(); 

                var casino_myself_lose = $('#casino_myself_lose'+index).val();
                var casino_recommender_lose =  $('#casino_recommender_lose'+index).val(); 
                // save_self(idx_casino_myself_bet,casino_myself_bet,casino_recommender_bet,casino_myself_lose,casino_recommender_lose)


                var idx_slot_myself_bet = $('#slot_myself_bet'+index).attr('data-id');
                var slot_myself_bet = $('#slot_myself_bet'+index).val();
                var slot_recommender_bet =  $('#slot_recommender_bet'+index).val(); 

                var slot_myself_lose = $('#slot_myself_lose'+index).val();
                var slot_recommender_lose =  $('#slot_recommender_lose'+index).val(); 
                // save_self(idx_slot_myself_bet,slot_myself_bet,slot_recommender_bet,slot_myself_lose,slot_recommender_lose)

                var idx_powerball_myself_bet = $('#powerball_myself_bet'+index).attr('data-id');
                var powerball_myself_bet = $('#powerball_myself_bet'+index).val();
                var powerball_recommender_bet =  $('#powerball_recommender_bet'+index).val(); 

                var powerball_myself_lose = $('#powerball_myself_lose'+index).val();
                var powerball_recommender_lose =  $('#powerball_recommender_lose'+index).val(); 
                // save_self(idx_powerball_myself_bet,powerball_myself_bet,powerball_recommender_bet,powerball_myself_lose,powerball_recommender_lose)

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/siteconfig_w/_set_rolling_comps_update.php',
                    data: {'idx':idx,'chex_myself':chex_myself,'chex_recommender':chex_recommender,'idx_prematch_s_myself_bet': idx_prematch_s_myself_bet,'idx_prematch_d_myself_bet':idx_prematch_d_myself_bet,'idx_inplay_s_myself_bet': idx_inplay_s_myself_bet,'idx_inplay_d_myself_bet':idx_inplay_d_myself_bet,'idx_casino_myself_bet':idx_casino_myself_bet,'idx_slot_myself_bet':idx_slot_myself_bet,'idx_powerball_myself_bet':idx_powerball_myself_bet, 'prematch_s_myself_bet': prematch_s_myself_bet, 'prematch_s_recommender_bet': prematch_s_recommender_bet,'prematch_s_myself_lose':prematch_s_myself_lose,'prematch_s_recommender_lose':prematch_s_recommender_lose, 'prematch_d_myself_bet': prematch_d_myself_bet, 'prematch_d_recommender_bet': prematch_d_recommender_bet,'prematch_d_myself_lose':prematch_d_myself_lose,'prematch_d_recommender_lose':prematch_d_recommender_lose, 'inplay_s_myself_bet': inplay_s_myself_bet, 'inplay_s_recommender_bet': inplay_s_recommender_bet,'inplay_s_myself_lose':inplay_s_myself_lose,'inplay_s_recommender_lose':inplay_s_recommender_lose,'inplay_d_myself_bet': inplay_d_myself_bet, 'inplay_d_recommender_bet': inplay_d_recommender_bet,'inplay_d_myself_lose':inplay_d_myself_lose,'inplay_d_recommender_lose':inplay_d_recommender_lose, 'casino_myself_bet': casino_myself_bet, 'casino_recommender_bet': casino_recommender_bet,'casino_myself_lose':casino_myself_lose,'casino_recommender_lose':casino_recommender_lose,'slot_myself_bet': slot_myself_bet, 'slot_recommender_bet': slot_recommender_bet,'slot_myself_lose':slot_myself_lose,'slot_recommender_lose':slot_recommender_lose,'powerball_myself_bet': powerball_myself_bet, 'powerball_recommender_bet': powerball_recommender_bet,'powerball_myself_lose':powerball_myself_lose,'powerball_recommender_lose':powerball_recommender_lose,'level':index},
                    success: function (data) {
                        // console.log(data['retCode']);
                        // count += data['retCode'];

                        window.location.reload();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });

                count++;

            }
            // setTimeout(function(){
            //     if (count == 10) {
            //         window.location.reload();
            //     }
            // }, 5000);

            // if (count == 10) {
            //     window.location.reload();
            // }
           
    
        }
 

    </script>
    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admConfigBet.js" charset="utf-8"></script>
    <body>
        <div class="wrap">
<?php
$menu_name = "set_rolling_by_level";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con_wrap">
            <?php
            include_once(_BASEPATH . '/siteconfig_w/bet_config_level_rolling.php');
            ?>
            </div>
            <!-- END Contents -->
        </div>
            <?php
            include_once(_BASEPATH . '/common/bottom.php');
            ?>
    </body>
    
</html>


