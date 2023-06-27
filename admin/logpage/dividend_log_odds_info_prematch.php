
<?php
    // intialize php codes
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
    include_once(_BASEPATH . '/common/_common_inc_class.php');
    include_once(_DAOPATH . '/class_Admin_Common_dao.php');
    // include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
    include_once(_BASEPATH . '/common/login_check.php');
    
    /* initialize variables */
    $menu_name          = "board_menu_6";
    $current_date       = date('Y/m/d');
?>
<html lang="ko">
    <?php include_once(_BASEPATH . '/common/head.php'); ?>
    <link rel="stylesheet" href="./logpage.css?time=<?php echo time() ?>">

    <!-- initialize script -->
    <script type="text/javascript" src="./dividend_log_odds_info_prematch.js?time=<?php echo time() ?>"></script>
    <script type="text/javascript">
        let dividend_log_check = new DividendLogCheck();
    </script>

    <body>
        <div class="wrap">
            <!-- header -->
            <?php include_once(_BASEPATH . '/common/left_menu.php'); ?>
            <?php include_once(_BASEPATH . '/common/iframe_head_menu.php'); ?>

            <!-- main-content -->
            <div class="con_wrap main-content">
               
                <!-- title -->
                <div class="title"><a href=""><i class="mte i_group mte-2x vam"></i><h4>&nbsp; 배당 로그 확인 (Sports) </h4></a></div>
                <div class="panel reserve">
                    <!-- tabs -->
                    <?php include_once('./dividend_log_check_tabs.php'); ?>

                    <!-- filters -->
                    <div class="filters-container">
                        <div class="left">
                            <!-- event filter -->
                            <div class="filter-group">
                                <div class="label">Date</div>
                                <!-- <input class="textbox" type="date"> -->
                                <input type="text" class="textbox text-filter-date" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?php echo $current_date ?>"/>
                            </div>

                            <div class="filter-group">
                                <div class="label">Sports</div>
                                <select class="select-sports select-box"><option value="0">Loading</option></select>
                            </div>

                            <!-- region filter -->
                            <div class="filter-group">
                                <div class="label">Location</div>
                                <select class="select-locations select-box"><option value="0">Loading</option></select>
                            </div>

                            <!-- league filter -->
                            <div class="filter-group">
                                <div class="label">League</div>
                                <select class="select-leagues select-box"><option value="0">Loading</option></select>
                            </div>

                            <!-- situation filter -->
                            <div class="filter-group">
                                <div class="label">Status</div>
                                <select class="select-status select-box"><option value="0">Loading</option></select>
                            </div>
                        </div>
                        <div class="right">
                            <div class="filter-group search">
                                <div class="label">Search by</div>
                                <div>
                                    <select class="search-by select-box">
                                        <option value="fixture_id">Fixture ID</option>
                                        <option value="league_id">League ID</option>
                                    </select>
                                    <input class="textbox search-keyword" type="text">
                                    <button class="button-top reload-table">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- tables -->
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>League</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                    <th>Part 1 ID</th>
                                    <th>Part 2 ID</th>
                                    <th>Display Status</th>
                                    <th>Last Update</th>
                                    <th style="width: 300px">MSG GUID</th>
                                    <th>Providers</th>
                                    <th style="width: 200px">Create Date</th>
                                </tr>
                            </thead>
                            <tbody class="table-loading">
                                <tr><td  colspan="10"><div class="loading lds-dual-ring"></div></td></tr>
                            </tbody>
                            <tbody class="table-data hidden"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- footer -->
            <?php include_once(_BASEPATH . '/common/bottom.php'); ?> 
        </div>
    </body>
</html>