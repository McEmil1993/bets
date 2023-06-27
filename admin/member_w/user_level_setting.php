<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end


$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {
    $p_data['sql'] = "select * from member_level_up";
   
    $dbResult = $MEMAdminDAO->getQueryData($p_data);
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
    <body>
        <div class="wrap">
            <?php
            $menu_name = "user_level_setting";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>

            <!-- Contents -->
            <div class="con_wrap">
                <form action="">
                    <div class="title">
                        <a href="javascript:;">
                            <i class="mte i_settings vam"></i>
                            <h4>레벨등업설정</h4>
                        </a>
                    </div>

                    <div class="panel">
                        <div class="tline">
                            <table class="mlist">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="input__group">
                                                <span>미입금일 </span>
                                                <input type="number" id="nonDepositDatePeriod" class="txt_r" value="<?=$dbResult[0]['nonDepositDatePeriod']?>">
                                                <span> 일 1레벨로 변경</span>
                                            </div>
                                        </td>
                                        <td><a href="#" class="btn h30 btn_blu" onclick="openLevelEdit(0, 1)">수정</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- list -->
                    <div class="panel reserve">
                        <div class="tline">
                            <table class="mlist">
                                <thead>
                                    <tr>
                                        <th>레벨</th>
                                        <th>입금</th>
                                        <th>출금</th>
                                        <th>정산</th>
                                        <th>입금횟수</th>
                                        <th>ON/OFF</th>
                                        <th>수정</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($dbResult as $key=>$row){ ?>
                                    <tr>
                                        <td id="level"><?=$row['level']?></td>
                                        <td><input type="number" id="charge_<?=$row['level']?>" style="width: 100%; text-align:right;" value="<?=$row['charge']?>"></td>
                                        <td><input type="number" id="exchange_<?=$row['level']?>" style="width: 100%; text-align:right;" value="<?=$row['exchange']?>"></td>
                                        <td><input type="number" id="calcurate_<?=$row['level']?>" style="width: 100%; text-align:right;" value="<?=$row['calcurate']?>"></td>
                                        <td><input type="number" id="charge_count_<?=$row['level']?>" style="width: 100%; text-align:right;" value="<?=$row['charge_count']?>"></td>
                                        <td>
                                            <?php if($row['flag'] == 'ON'){ ?>
                                                <a href="#" class="btn h25 btn_onoff btn_green" data-level="<?= $row['level'] ?>"><?= $row['flag'] ?></a>
                                            <?php }else{ ?>
                                                <a href="#" class="btn h25 btn_onoff btn_gray" data-level="<?= $row['level'] ?>"><?= $row['flag'] ?></a>
                                            <?php } ?>
                                        </td>
                                        <td><a href="#" class="btn h30 btn_blu" onclick="openLevelEdit(1, <?=$row['level']?>)">수정</a></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END list -->
                </form>
            </div>
            <!-- END Contents -->
        </div>
        <!--<div id="edit_userLevel_pop" class="pop-window">
            <div class="popup_container">
                <div class="tline">
                    <table class="mlist">
                        <colgroup>
                            <col width="30%">
                            <col width="70%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th colspan="2">2레벨 등급 조건 수정</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>입금</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>출금</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>정산</th>
                                <td>0</td>
                            </tr>
                            <tr>
                                <th>입금횟수</th>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btn_wrap fr mt5">
                    <a href="#" class="btn h30 btn_blu">설정</a>
                    <a href="#" class="btn h30 btn_blu" onclick="closeLevelEdit()">취소</a>
                </div>
            </div>
        </div>-->
        <script>
            // 온오프 버튼 추가
            $(".btn_onoff").click(function(){
                let param_url = '/member_w/_ajax_user_level_on_off_setting.php';
                let level = $(this).data('level');
                let flag = 'ON';
                
                $(this).toggleClass("btn_green btn_gray");
                if($(this).hasClass("btn_green") === true){
                    $(this).text("ON");
                    flag = 'ON';
                } else {
                    $(this).text("OFF");
                    flag = 'OFF';
                };
                
                var result = confirm('수정하시겠습니까?');
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: param_url,
                        data: {'level': level, 'flag': flag},
                        success: function (data) {
                            console.log(data);
                            if (data['retCode'] == "1000") {
                                alert('수정하였습니다.');
                                //window.location.reload();
                            } else {
                                alert(data['retMsg']);
                                window.location.reload();
                            }
                        },
                        error: function (request, status, error) {
                            alert('서버 오류 입니다.');
                            window.location.reload();
                        }
                    });
                }
            });
            // 조건수정 팝업 
            const openLevelEdit = function (type, level) {
                //$('#edit_userLevel_pop').attr('style', 'display: block');
                let charge = $('#charge_'+level).val();
                let exchange = $('#exchange_'+level).val();
                let calcurate = $('#calcurate_'+level).val();
                let charge_count = $('#charge_count_'+level).val();
                let nonDepositDatePeriod = $('#nonDepositDatePeriod').val();
                let param_url = '/member_w/_ajax_user_level_setting.php';

                var result = confirm('수정하시겠습니까?');
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: param_url,
                        data: {'type': type, 'level': level, 'charge': charge, 'charge': charge, 'exchange': exchange, 'calcurate': calcurate, 'charge_count': charge_count, 'nonDepositDatePeriod': nonDepositDatePeriod},
                        success: function (data) {
                            console.log(data);
                            if (data['retCode'] == "1000") {
                                alert('수정하였습니다.');
                                //window.location.reload();
                            } else {
                                alert(data['retMsg']);
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
            /*const closeLevelEdit = function () {
                $('#edit_userLevel_pop').attr('style', 'display: none');
            }*/
        </script>
    </body>
</html>