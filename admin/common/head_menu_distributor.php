<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

include_once(_BASEPATH.'/common/head.php');
include_once(_BASEPATH.'/common/login_check.php');

?>
<html lang="ko">
<!--<![endif]-->

<body style=”overflow-x:auto;overflow-y:hidden”>
<div class="head_wrap_dis">
    <!-- TOP Area -->
    <div class="head_menu head_menu-distri">
        <ul>
            <!--<li>
                <a href="javascript:;">
                    총 정산금
                    <p><span id="settlement" class="">0</span></p>
                </a>
            </li>
            <li>
                <a href="javascript:;">
                    미 정산금
                    <p><span id="unsettlement" class="tred">0</span></p>
                </a>
            </li>-->
        </ul>
    </div>
    <div class="head_dis">
        <table class="head_dis_table">
            <tr>
                <th rowspan="6">내정산율</th>
            </tr>
            <tr>
                <th>프리매치 단폴(%)</th>
                <th>프리멀티 2폴(%)</th>
                <th>프리멀티 3폴(%)</th>
                <th>프리멀티 4폴(%)</th>
                <th>프리멀티 5폴이상(%)</th>
                <th>실시간 단폴(%)</th>
                <th>실시간 2폴이상(%)</th>
            </tr>
            <tr>
                <td id="bet_pre_s_fee">0</td>
                <td id="bet_pre_d_2_fee">0</td>
                <td id="bet_pre_d_3_fee">0</td>
                <td id="bet_pre_d_4_fee">0</td>
                <td id="bet_pre_d_5_more_fee">0</td>
                <td id="bet_real_s_fee">0</td>
                <td id="bet_real_d_fee">0</td>
            </tr>
            <tr>
                <th colspan="2">죽장(%)</th>
                <th>미니게임(%)</th>
                <th>카지노(%)</th>
                <th>슬롯(%)</th>
                <th>이스포츠(%)</th>
                <th>해쉬(%)</th>
                
            </tr>
            <tr>
                <td colspan="2" id="pre_s_fee">0</td>
                <td id="bet_mini_fee">0</td>
                <td id="bet_casino_fee">0</td>
                <td id="bet_slot_fee">0</td>
                <td id="bet_esports_fee">0</td>
                <td id="bet_hash_fee">0</td>
            </tr>
        </table>
    </div>
    
    <div class="head_btn">
        <table>
            <tr>
                <td><a href="javascript:;" onclick="goLogout();"><i class="mte i_power_settings_new vam"></i>나가기</a></td>
            </tr>
        </table>
    </div>


    <div class="tinfo_wrap">
        <!-- <div class="t_link"><?=$_SESSION['anick']?></div> -->
        <?php
        if (isset($_SESSION) && isset($_SESSION['anick'])) {
            ?>
            <div class="t_link"><?=$_SESSION['anick']?></div>
        <?php } else { ?>
            <div class="t_link">test</div>
        <?php } ?>
    </div>
    
    <!-- END TOP Area -->
</div>
</body>
</html>
<script language='javascript'>
    $(document).ready(function () {
        admNowCallRequest();
        setInterval("admNowCallRequest()", 5000);
    });

    function goLogout() {
        var str_msg = "로그아웃 하시겠습니까?";
        var result = confirm(str_msg);

        if (result) {
            parent.location.href = "/login_w/logout.php";
        }
    }
    
    function admNowCallRequest() {
        //console.log('admNowCallRequest');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/common/_now_call_check_distributor.php',
            success: function (result) {
                if (result['retCode'] == "1000") {
                    $('#bet_pre_s_fee').text(result['bet_pre_s_fee']);
                    $('#bet_pre_d_2_fee').text(result['bet_pre_d_2_fee']);
                    $('#bet_pre_d_3_fee').text(result['bet_pre_d_3_fee']);
                    $('#bet_pre_d_4_fee').text(result['bet_pre_d_4_fee']);
                    $('#bet_pre_d_5_more_fee').text(result['bet_pre_d_5_more_fee']);

                    $('#bet_real_s_fee').text(result['bet_real_s_fee']);
                    $('#bet_real_d_fee').text(result['bet_real_d_fee']);
                    
                    $('#bet_mini_fee').text(result['bet_mini_fee']);
                    $('#bet_casino_fee').text(result['bet_casino_fee']);
                    $('#bet_slot_fee').text(result['bet_slot_fee']);
                    $('#bet_esports_fee').text(result['bet_esports_fee']);
                    $('#bet_hash_fee').text(result['bet_hash_fee']);
                    $('#pre_s_fee').text(result['pre_s_fee']);
                    return;
                } else if (result['retCode'] == "2001") {
                    //parent.location.href = "/login.php?msg=1";
                } else if (result['retCode'] == "2002") {
                    //parent.location.href = "/login.php?msg=2";
                } else {
                    // '0' 으로 처리
                    document.getElementById("betting_fee").innerHTML = 0;
                    document.getElementById("lose_fee").innerHTML = 0;
                    document.getElementById("loss_fee").innerHTML = 0;
                    return;
                }
            },
            error: function (request, status, error) {
                document.getElementById("betting_fee").innerHTML = -1;
                document.getElementById("lose_fee").innerHTML = -1;
                document.getElementById("loss_fee").innerHTML = -1;
                return;
            }
        });
    }
</script>
