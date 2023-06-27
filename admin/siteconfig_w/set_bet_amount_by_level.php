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

    $p_data['sql'] = " SELECT idx, u_level, set_type, set_type_val, title, reg_time FROM t_game_config";

    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);


    $p_data['sql'] = " SELECT * FROM dividend_policy";

    $db_dividendArr = $MEMAdminDAO->getQueryData($p_data);

    $dividenArr = [];
    if (!empty($db_dividendArr)) {
        foreach ($db_dividendArr as $row) {
            $dividenArr[$row['level']][$row['type']][$row['rank']] = $row['amount'];
        }
    }

    if (!empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            $db_level = $row['u_level'];
            if ($row['set_type'] == 'reg_first_charge') {
                $db_reg_first_charge = $row['set_type_val'];
            }

            // prematch
            if ($row['set_type'] == 'pre_min_money') {
                $db_pre_min_money[$db_level] = $row['set_type_val'];
                if ($db_pre_min_money[$db_level] == '')
                    $db_pre_min_money[$db_level] = 0;
            }
            else if ($row['set_type'] == 'pre_max_money') {
                $db_pre_max_money[$db_level] = $row['set_type_val'];
                if ($db_pre_max_money[$db_level] == '')
                    $db_pre_max_money[$db_level] = 0;
            }
            else if ($row['set_type'] == 'pre_limit_money') {
                $db_pre_limit_money[$db_level] = $row['set_type_val'];
                if ($db_pre_limit_money[$db_level] == '')
                    $db_pre_limit_money[$db_level] = 0;
            }
            
            // classic
            if ($row['set_type'] == 'classic_min_money') {
                $db_classic_min_money[$db_level] = $row['set_type_val'];
                if ($db_classic_min_money[$db_level] == '')
                    $db_classic_min_money[$db_level] = 0;
            }
            else if ($row['set_type'] == 'classic_max_money') {
                $db_classic_max_money[$db_level] = $row['set_type_val'];
                if ($db_classic_max_money[$db_level] == '')
                    $db_classic_max_money[$db_level] = 0;
            }
            else if ($row['set_type'] == 'classic_limit_money') {
                $db_classic_limit_money[$db_level] = $row['set_type_val'];
                if ($db_classic_limit_money[$db_level] == '')
                    $db_classic_limit_money[$db_level] = 0;
            }
            
            // realtime
            else if ($row['set_type'] == 'real_min_money') {
                $db_real_min_money[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'real_max_money') {
                $db_real_max_money[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'real_limit_money') {
                $db_real_limit_money[$db_level] = $row['set_type_val'];
                
            } else if ($row['set_type'] == 'lose_self_per') {
                $db_lose_self_per[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'lose_recomm_per') {
                $db_lose_recomm_per[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'charge_first_per') {
                $db_charge_first_per[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'charge_max_money') {
                $db_charge_max_money[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'charge_per') {
                $db_charge_per[$db_level] = $row['set_type_val'];
            } else if ($row['set_type'] == 'charge_money') {
                $db_charge_money[$db_level] = $row['set_type_val'];
            }
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
    </script>
    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admConfigBet.js" charset="utf-8"></script>
    <body>
        <div class="wrap">
<?php
$menu_name = "set_bet_amount_by_level";

include_once(_BASEPATH . '/common/left_menu.php');

include_once(_BASEPATH . '/common/iframe_head_menu.php');
?>
            <!-- Contents -->
            <div class="con1_wrap">
            <?php
            include_once(_BASEPATH . '/siteconfig_w/bet_config_level_inc.php');
            ?>
            </div>
            <!-- END Contents -->
        </div>
            <?php
            include_once(_BASEPATH . '/common/bottom.php');
            ?>
    </body>
</html>

