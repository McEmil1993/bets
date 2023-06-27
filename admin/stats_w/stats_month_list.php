<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');
include_once(_BASEPATH . '/common/login_check.php');
include_once(_LIBPATH . '/class_ComQuery.php');
include_once(_LIBPATH . '/class_Code.php');

if (!isset($_SESSION)) {
    session_start();
}



$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

$p_data['srch_year'] = trim(isset($_REQUEST['srch_year']) ? $_REQUEST['srch_year'] : date("Y"));
$p_data['srch_month'] = trim(isset($_REQUEST['srch_month']) ? $_REQUEST['srch_month'] : date("m"));
//$p_data['srch_year'].'-'.$p_data['srch_month'].'-01'
// 이팀장 여기 날짜를 잡아주면된다.
$db_srch_s_date = $p_data['srch_year'].'-'.$p_data['srch_month'].'-01' . " 00:00:00";
$db_srch_e_date = $p_data['srch_year'].'-'.$p_data['srch_month'].'-31' . " 23:59:59";
//$db_srch_s_date = "2022-03-01 00:00:00";
//$db_srch_e_date = "2022-04-01 23:59:59";

if ($db_conn) {
    // 전체 합
  
    $add_where = "";
    if(0 != $_SESSION['u_business']){
        list($param_dist,$str_param_qm) = GameCode::getRecommandMemberIdx($_SESSION['member_idx'],$CASHAdminDAO);
        $str_param = implode(',', $param_dist);
        $add_where = " and member_idx IN($str_param)";
    }
   
    $p_data['sql'] = ComQuery::doShopResultChExDist($db_srch_s_date, $db_srch_e_date,$add_where);
    CommonUtil::logWrite("stat day doShopResultChExDist sql ==> " . $p_data['sql'], "info");
    $db_dataTotalArr = $CASHAdminDAO->getQueryData($p_data);
    CommonUtil::logWrite("stat day doShopResultChExDist ch_ex ==> " . json_encode($db_dataTotalArr), "info");
    
    $ch_val_sum = 0;
    $ex_val_sum = 0;
    $gab_sum = 0;
    if(true === isset($db_dataTotalArr) || false === empty($db_dataTotalArr) ){
        $ch_val_sum = (int)$db_dataTotalArr[0]['ch_val_sum'];
        $ex_val_sum = (int)$db_dataTotalArr[0]['ex_val_sum'];
        $gab_sum = (int)$db_dataTotalArr[0]['gab_sum'];
    }
    
    // 날짜별 합
    $add_where = " group by calculate_dt order by calculate_dt desc";
    if(0 != $_SESSION['u_business']){
        $add_where = " and member_idx IN($str_param) group by calculate_dt order by calculate_dt desc";
    }

    $p_data['sql'] = ComQuery::doShopResultChExDist($db_srch_s_date, $db_srch_e_date, $add_where);
    CommonUtil::logWrite("stat day doShopResultChExDist p_data['sql'] ==> " . $p_data['sql'], "info");
      
    $db_dataGabArr = $CASHAdminDAO->getQueryData($p_data);
    $db_dataGabArr = !empty($db_dataGabArr) ? $db_dataGabArr : [];
}
?>

<html lang="ko">
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
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>

    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="selContent" name="selContent">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "stats_month_list";
            include_once(_BASEPATH . '/common/left_menu.php');
            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>

            <!-- Contents -->
            <div class="con_wrap">
                <div class="title">
                    <a href="">
                        <i class="mte i_assessment vam ml20 mr10"></i>
                        <h4>월별 통계</h4>
                    </a>
                </div>
                <!-- list -->
                <div class="panel reserve">
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>
                        <input type="hidden" name="srch_month" id="srch_month">
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="pr10">
                                    <select name="srch_year" id="srch_year">
                                         <?php 
                                             $months = array(2023,2024,2025,2026,2027,2028,2029);
                                            foreach ($months as $key => $money) { ?>
                                            <option value="<?= $money ?>"   <?php if ($money == $p_data['srch_year']): ?> selected<?php endif; ?>><?= $money ?></option>
                                        <?php } ?>
                                
                                    </select>
                                </div>

                                <div><a href="javascript:;" onClick="setMonth(1);" class="btn h30 btn_green">1월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(2);" class="btn h30 btn_green">2월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(3);" class="btn h30 btn_green">3월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(4);" class="btn h30 btn_green">4월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(5);" class="btn h30 btn_green">5월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(6);" class="btn h30 btn_green">6월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(7);" class="btn h30 btn_green">7월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(8);" class="btn h30 btn_green">8월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(9);" class="btn h30 btn_green">9월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(10);" class="btn h30 btn_green">10월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(11);" class="btn h30 btn_green">11월</a></div>
                                <div><a href="javascript:;" onClick="setMonth(12);" class="btn h30 btn_green">12월</a></div>
                            </div>
                        </div>
                    </form>
                    <div class="table-wrapper1">
                        <div class="above-table">
                        </div>
                    </div>

                    <div class="tline table-wrapper2">

                        <table class="mlist main-table">
                            <tr>
                                <th>날짜</th>
                                <th>입금</th>
                                <th>출금</th>
                                <th>수익</th>
                            </tr>
                            <tr class="bg_orange">
                                <td>합계</td>
                                <td style='text-align:right;'>
                                    <?= number_format($ch_val_sum) ?>
                                </td>
                                <td style='text-align:right;'>
                                    <?= number_format($ex_val_sum) ?>
                                </td>
                                <td style='text-align:right;'>
                                    
                                    <?= number_format($gab_sum) ?>
                                </td>
                            </tr>
                            <?php
                            //$db_member_dataArr = !empty($db_member_dataArr) ? $db_member_dataArr : [];
                            foreach ($db_dataGabArr as $row) {
                                
                                $tot_ret_money_str = GameCode::strColorRet($row['ch_val_sum'] , $row['ex_val_sum'], 1);
                            ?>

                            <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                <td>
                                       <?= $row['calculate_dt'] ?>
                                </td>
                                <td style='text-align:right;'>
                                       <?= number_format($row['ch_val_sum']) ?>
                                </td>
                                <td style='text-align:right;'>
                                       <?= number_format($row['ex_val_sum']) ?>
                                </td>
                                <td style='text-align:right;'>
                                       <?= $tot_ret_money_str ?>
                                </td>
                                <?php } ?>
                        </table>
                        <!-- 페이징 숨김 처리 -->
                    </div>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>
        <?php
        include_once(_BASEPATH . '/common/bottom.php');
        ?>
        <script>
            function setMonth(month) {
                var fm = document.search;

                fm.srch_month.value = month;
                
                fm.method = "get";
                fm.submit();
            }
        </script>
    </body>
</html>