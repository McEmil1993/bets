
<?php
    // intialize php codes
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
    include_once(_BASEPATH . '/common/_common_inc_class.php');
    include_once(_DAOPATH . '/class_Admin_Common_dao.php');
    // include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
    include_once(_BASEPATH . '/common/login_check.php');
    
    /* initialize variables */
    $menu_name          = "board_menu_6";
?>
<html lang="ko">
    <?php include_once(_BASEPATH . '/common/head.php'); ?>
    <link rel="stylesheet" href="./logpage.css?time=<?php echo time() ?>">

    <!-- initialize script -->
    <script type="text/javascript" src="./dividend_log_check.js?time=<?php echo time() ?>"></script>
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
                    <!-- filters -->
                    <div class="filters-container">
                        <div class="left">
                            <!-- event filter -->
                            <div class="filter-group">
                                <div class="label">Date</div>
                                <!-- <input class="textbox" type="date"> -->
                                <input type="text" class="textbox" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value=""/>
                            </div>

                            <div class="filter-group">
                                <div class="label">Sports</div>
                                <select class="select-sports select-box"></select>
                            </div>

                            <!-- region filter -->
                            <div class="filter-group">
                                <div class="label">Location</div>
                                <select class="select-locations select-box"></select>
                            </div>

                            <!-- league filter -->
                            <div class="filter-group">
                                <div class="label">League</div>
                                <select class="select-leagues select-box"></select>
                            </div>

                            <!-- situation filter -->
                            <div class="filter-group">
                                <div class="label">Status</div>
                                <select class="select-status select-box"></select>
                            </div>
                        </div>
                        <div class="right">
                            <div class="filter-group">
                                <div class="label">Search</div>
                                <input placeholder="Search (Fixture ID)" class="textbox" type="text">
                            </div>
                        </div>
                    </div>
                    <!-- tables -->
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bet Name</th>
                                    <th>Bet Line</th>
                                    <th>Bet Price</th>
                                    <th>Temp Price</th>
                                    <th>Bet Status</th>
                                    <th style="width: 300px">MSG Guide</th>
                                    <th>Deduction</th>
                                    <th>Provider ID</th>
                                    <th>Create Date</th>
                                </tr>
                            </thead>
                            <tbody class="table-loading">
                                <tr><td  colspan="9"><div class="loading lds-dual-ring"></div></td></tr>
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