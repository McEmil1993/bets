
<?php

if (!isset($_SESSION)) {
    session_start();
}

$u_business = $_SESSION['u_business'];
 
$menu_main_act = $menu_mem_act = $menu_money_act = $menu_sports_act = $menu_mini_game_act = $menu_casino_act = $menu_hash_act = $menu_config_act = $menu_stats_act = $menu_board_act = "";

// main & member
$menu_main_chk = $menu_mem_list_chk = $menu_mem_list_normal_chk = $menu_mem_list_charge_chk = $menu_mem_list_security_chk = $menu_mem_list_dupbet_chk = "mte i_radio_button_unchecked vam";
$menu_mem_list_nodeposit_chk = $menu_distributor_list_chk = $menu_now_conn_list_chk = $menu_mem_list_edit_chk = "mte i_radio_button_unchecked vam";
$menu_mem_list_login_chk = $menu_mem_list_sms_chk = $menu_mem_list_recomm_chk = $menu_mem_list_ipblock_chk = "mte i_radio_button_unchecked vam";
$menu_mem_list_multi_chk = $menu_mem_list_account_chk = $menu_mem_list_account_day_chk = "mte i_radio_button_unchecked vam";
$menu_msg_send_list_chk = $menu_msg_write_chk = $user_level_setting = $menu_mem_item_list_chk = "mte i_radio_button_unchecked vam";

// money
$menu_charge_chk = $menu_exchange_chk = $menu_money_log_chk = $menu_point_log_chk = $menu_auto_charge_sms_chk = $menu_gmoney_log_chk = "mte i_radio_button_unchecked vam";

// sports
$menu_sports_menu1_chk = $menu_sports_menu2_chk = $menu_sports_menu3_chk = $menu_sports_menu4_chk = $menu_sports_menu5_chk = "mte i_radio_button_unchecked vam";
$menu_sports_menu6_chk = $menu_sports_menu7_chk = $menu_sports_menu8_chk = $menu_sports_menu9_chk = $menu_sports_menu10_chk = "mte i_radio_button_unchecked vam";
$menu_sports_menu11_chk = $menu_sports_menu12_chk = $menu_sports_menu13_chk = "mte i_radio_button_unchecked vam";

// mini game
$menu_mini_game_menu1_chk = $menu_mini_game_menu2_chk = $menu_mini_game_menu3_chk = $menu_mini_game_menu4_chk = $menu_mini_game_menu5_chk = $menu_mini_game_menu6_chk = "mte i_radio_button_unchecked vam";

// casino
$menu_casino_menu1_chk = $menu_casino_menu2_chk = $menu_casino_menu3_chk = $menu_casino_menu4_chk = $menu_casino_menu5_chk = "mte i_radio_button_unchecked vam";

// hash
$menu_hash_menu1_chk = $menu_hash_menu2_chk = $menu_hash_menu3_chk = "mte i_radio_button_unchecked vam";

// site config
$menu_config_1_chk = $menu_config_2_chk = $menu_config_3_chk = $menu_config_4_chk = $menu_config_5_chk = "mte i_radio_button_unchecked vam";
$menu_config_6_chk = $menu_config_7_chk = $menu_config_8_chk = $menu_config_9_chk = $menu_config_10_chk = $menu_config_11_chk = $menu_config_12_chk = $menu_config_13_chk = $menu_config_14_chk = $menu_config_15_chk  = "mte i_radio_button_unchecked vam";

$menu_config_17_chk = "mte i_radio_button_unchecked vam";

// stats 
$menu_stats_1_chk = $menu_stats_2_chk = $menu_stats_3_chk = $menu_stats_4_chk = $menu_stats_5_chk = $menu_stats_6_chk = $menu_stats_7_chk = $menu_stats_8_chk = $menu_stats_9_chk = "mte i_radio_button_unchecked vam";

// board
$menu_board_menu_1_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_2_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_3_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_4_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_5_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_6_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_7_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_8_chk = "mte i_radio_button_unchecked vam";
$menu_board_menu_9_chk = "mte i_radio_button_unchecked vam";

// 정산
$menu_calculate_menu_1_chk = "mte i_radio_button_unchecked vam";

switch ($menu_name) {
    //////// stats
    case "stats_user":
        $menu_stats_act = "active";
        $menu_stats_1_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_day_list":
        $menu_stats_act = "active";
        $menu_stats_2_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_type":
        $menu_stats_act = "active";
        $menu_stats_3_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_league":
        $menu_stats_act = "active";
        $menu_stats_4_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_day_dis":
        $menu_stats_act = "active";
        $menu_stats_5_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_day_dis_money":
        $menu_stats_act = "active";
        $menu_stats_6_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_dis_list":
        $menu_stats_act = "active";
        $menu_stats_7_chk = "mte i_radio_button_checked vam";
        break;
    case "stats_month_list":
        $menu_stats_act = "active";
        $menu_stats_8_chk = "mte i_radio_button_checked vam";
        break;
    
    case "stats_day_list_new_tm":
        $menu_stats_act = "active";
        $menu_stats_9_chk = "mte i_radio_button_checked vam";
        break;
    ////////
    case "adm_log_list":
        $menu_config_act = "active";
        $menu_config_1_chk = "mte i_radio_button_checked vam";
        break;
    case "adm_ip_list":
        $menu_config_act = "active";
        $menu_config_2_chk = "mte i_radio_button_checked vam";
        break;
    case "adm_id_list":
        $menu_config_act = "active";
        $menu_config_9_chk = "mte i_radio_button_checked vam";
        break;
    case "ip_block":
        $menu_config_act = "active";
        $menu_config_3_chk = "mte i_radio_button_checked vam";
        break;
    case "domain_code":
        $menu_config_act = "active";
        $menu_config_4_chk = "mte i_radio_button_checked vam";
        break;
    case "account_level":
        $menu_config_act = "active";
        $menu_config_5_chk = "mte i_radio_button_checked vam";
        break;
    case "bet_config_level":
        $menu_config_act = "active";
        $menu_config_6_chk = "mte i_radio_button_checked vam";
        break;
    case "bet_config_game_level":
        $menu_config_act = "active";
        $menu_config_7_chk = "mte i_radio_button_checked vam";
        break;
    case "site_config":
        $menu_config_act = "active";
        $menu_config_8_chk = "mte i_radio_button_checked vam";
        break;
    case "level_charge_type":
        $menu_config_act = "active";
        $menu_config_10_chk = "mte i_radio_button_checked vam";
        break;
    case "set_bet_amount_by_level": // 사이트관리 - 레벨별 배팅급액 설정
        $menu_config_act = "active";
        $menu_config_11_chk = "mte i_radio_button_checked vam";
        break;
    case "event_charge_set": // 사이트관리 - 레벨별 이벤트 충전 설정
        $menu_config_act = "active";
        $menu_config_12_chk = "mte i_radio_button_checked vam";
        break;
    case "average_dividend_provider":
        $menu_config_act = "active";
        $menu_config_13_chk = "mte i_radio_button_checked vam";
        break;
    case "user_level_setting": // 레벨등업설정
        $menu_config_act = "active";
        $user_level_setting = "mte i_radio_button_checked vam";
        break;
    case "inspection":
        $menu_config_act = "active";
        $menu_config_14_chk = "mte i_radio_button_checked vam";
        break;
    case "casino_prd_list":
        $menu_config_act = "active";
        $menu_config_15_chk = "mte i_radio_button_checked vam";
        break;



    case "set_rolling_by_level": // 사이트관리 - 레벨별 롤링콤푸 설정
        $menu_config_act = "active";
        $menu_config_17_chk = "mte i_radio_button_checked vam";
        break;
    //////// money type start
    case "charge_list": // 충전
        $menu_money_act = "active";
        $menu_charge_chk = "mte i_radio_button_checked vam";
        break;
    case "exchange_list": // 환전
        $menu_money_act = "active";
        $menu_exchange_chk = "mte i_radio_button_checked vam";
        break;
    case "money_log": // 머니 사용 이력
        $menu_money_act = "active";
        $menu_money_log_chk = "mte i_radio_button_checked vam";
        break;
    case "gmoney_log": // 지머니 사용 이력
        $menu_money_act = "active";
        $menu_gmoney_log_chk = "mte i_radio_button_checked vam";
        break;
    case "point_log": // 포인트 사용 이력
        $menu_money_act = "active";
        $menu_point_log_chk = "mte i_radio_button_checked vam";
        break;
    case "auto_charge_sms": // 
        $menu_money_act = "active";
        $menu_auto_charge_sms_chk = "mte i_radio_button_checked vam";
        break;
    //////// member type start
    case "mem_list": // total
        $menu_mem_act = "active";
        $menu_mem_list_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_item_list": // 유저아이템
        $menu_mem_act = "active";
        $menu_mem_item_list_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_normal": // 일반
        $menu_mem_act = "active";
        $menu_mem_list_normal_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_charge": // 충전
        $menu_mem_act = "active";
        $menu_mem_list_charge_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_security": // 보안
        $menu_mem_act = "active";
        $menu_mem_list_security_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_dupbet": // 중복
        $menu_mem_act = "active";
        $menu_mem_list_dupbet_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_nodeposit": // 미입금
        $menu_mem_act = "active";
        $menu_mem_list_nodeposit_chk = "mte i_radio_button_checked vam";
        break;
    case "distributor_list": // 총판
        $menu_mem_act = "active";
        $menu_distributor_list_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_now_conn": // 현재접속자
        $menu_mem_act = "active";
        $menu_now_conn_list_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_edit": // 회원정보 변경
        $menu_mem_act = "active";
        $menu_mem_list_edit_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_login": // 로그인
        $menu_mem_act = "active";
        $menu_mem_list_login_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_sms": // sms 인증
        $menu_mem_act = "active";
        $menu_mem_list_sms_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_recomm": // 추천회원
        $menu_mem_act = "active";
        $menu_mem_list_recomm_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_ipblock": // ip block
        $menu_mem_act = "active";
        $menu_mem_list_ipblock_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_account": // 통장 조회
        $menu_mem_act = "active";
        $menu_mem_list_account_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_account_day": // 통장 조회 날짜별
        $menu_mem_act = "active";
        $menu_mem_list_account_day_chk = "mte i_radio_button_checked vam";
        break;
    case "mem_list_multi": // multi user
        $menu_mem_act = "active";
        $menu_mem_list_multi_chk = "mte i_radio_button_checked vam";
        break;
    case "msg_send_list": // 쪽지 발송 내역
        $menu_mem_act = "active";
        $menu_msg_send_list_chk = "mte i_radio_button_checked vam";
        break;
    case "msg_write": // 쪽지
        $menu_mem_act = "active";
        $menu_msg_write_chk = "mte i_radio_button_checked vam";
        break;
    //////// sports type start
    case "prematch_manager": // 스포츠 - 프리매치
        $menu_sports_act = "active";
        $menu_sports_menu1_chk = "mte i_radio_button_checked vam";
        break;
    case "realtime_manager": // 스포츠 - 실시간
        $menu_sports_act = "active";
        $menu_sports_menu2_chk = "mte i_radio_button_checked vam";
        break;
    case "sports_manager": // 스포츠 - 종목관리
        $menu_sports_act = "active";
        $menu_sports_menu3_chk = "mte i_radio_button_checked vam";
        break;
    case "bet_type_manager": // 스포츠 - 타입관리
        $menu_sports_act = "active";
        $menu_sports_menu4_chk = "mte i_radio_button_checked vam";
        break;
    case "sports_league_manager": // 스포츠 - 리그관리
        $menu_sports_act = "active";
        $menu_sports_menu5_chk = "mte i_radio_button_checked vam";
        break;
    case "sports_team_name_manager": // 스포츠 - 팀명관리
        $menu_sports_act = "active";
        $menu_sports_menu6_chk = "mte i_radio_button_checked vam";
        break;
    case "sports_set_exchange_rate": // 스포츠 - 환수율관리
        $menu_sports_act = "active";
        $menu_sports_menu7_chk = "mte i_radio_button_checked vam";
        break;
    //case "sports_dividend_amount_manager": // 스포츠 - 등급별 배팅급액 설정
    //    $menu_sports_act     		        = "active";
    //    $menu_sports_menu8_chk    = "mte i_radio_button_checked vam";
    //    break;
    case "prematch_betting_list": // 스포츠 - 프리매치 배팅목록
        $menu_sports_act = "active";
        $menu_sports_menu10_chk = "mte i_radio_button_checked vam";
        break;
    case "realtime_betting_list": // 스포츠 - 실시간 배팅목록
        $menu_sports_act = "active";
        $menu_sports_menu11_chk = "mte i_radio_button_checked vam";
        break;
    case "level_market_bet_set": // 스포츠 - 레벨별 마켓베팅금액 설정
        $menu_sports_act = "active";
        $menu_sports_menu12_chk = "mte i_radio_button_checked vam";
        break;
    //////// minigame type start
    case "mini_game_menu1": // 미니게임 - 내역
        $menu_mini_game_act = "active";
        $menu_mini_game_menu1_chk = "mte i_radio_button_checked vam";
        break;
    case "mini_game_menu2": // 미니게임 - 베팅목록
        $menu_mini_game_act = "active";
        $menu_mini_game_menu2_chk = "mte i_radio_button_checked vam";
        break;
    case "mini_game_menu3": // 미니게임 - 가상축구 베팅목록
        $menu_mini_game_act = "active";
        $menu_mini_game_menu3_chk = "mte i_radio_button_checked vam";
        break;
    case "mini_game_menu4": // 미니게임 - 설정
        $menu_mini_game_act = "active";
        $menu_mini_game_menu4_chk = "mte i_radio_button_checked vam";
        break;
    case "casino_betting_list": // 카지노 - 카지노
    // case "casino_betting_list_sb": // 카지노 - 카지노
        $menu_casino_act = "active";
        $menu_casino_menu1_chk = "mte i_radio_button_checked vam";
        break;
    case "slot_betting_list": // 카지노 - 슬롯
        $menu_casino_act = "active";
        $menu_casino_menu2_chk = "mte i_radio_button_checked vam";
        break;
    case "esport_betting_list": // 카지노 - E스포츠
        $menu_casino_act = "active";
        $menu_casino_menu3_chk = "mte i_radio_button_checked vam";
        break;
    case "kiron_betting_list": // 카지노 - 키론가상
        $menu_casino_act = "active";
        $menu_casino_menu4_chk = "mte i_radio_button_checked vam";
        break;
    case "holdem_betting_list": // 카지노 - 홀덤
        $menu_casino_act     		        = "active";
        $menu_casino_menu5_chk    = "mte i_radio_button_checked vam";
        break;
    case "baccara_betting_list": // 해시 - 바카라
        $menu_hash_act = "active";
        $menu_hash_menu1_chk = "mte i_radio_button_checked vam";
        break;
    case "roulette_betting_list": // 해시 - 룰렛
        $menu_hash_act = "active";
        $menu_hash_menu2_chk = "mte i_radio_button_checked vam";
        break;
    case "highrow_betting_list": // 해시 - 하이로우
        $menu_hash_act = "active";
        $menu_hash_menu3_chk = "mte i_radio_button_checked vam";
        break;
    //////// board type start
    case "board_menu_1": // 이벤트
        $menu_board_act = "active";
        $menu_board_menu_1_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_2": // 배팅규정
        $menu_board_act = "active";
        $menu_board_menu_2_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_3": // 게시판
        $menu_board_act = "active";
        $menu_board_menu_3_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_4": // 고객센터
        $menu_board_act = "active";
        $menu_board_menu_4_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_5": // 템플릿 관리
        $menu_board_act = "active";
        $menu_board_menu_5_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_6": // 메인배너 관리
        $menu_board_act = "active";
        $menu_board_menu_6_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_7": // 공지
        $menu_board_act = "active";
        $menu_board_menu_7_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_8": // 가입메세지
        $menu_board_act = "active";
        $menu_board_menu_8_chk = "mte i_radio_button_checked vam";
        break;
    case "board_menu_9": // 메인팝업관리
        $menu_board_act = "active";
        $menu_board_menu_9_chk = "mte i_radio_button_checked vam";
        break;
    case "calculate_menu_1": // 총판
        $menu_calculate_act = "active";
        $menu_calculate_menu_1_chk = "mte i_radio_button_checked vam";
        break;
}
?>

<!-- SIDE AREA -->
<div id="sidebar" class="sidebar">
    <!-- Side top info -->
<?php
$imgName = LOGIN_BG;
?>
    <div class="admin_title_logo"><img src="../static_common/images/common/<?= $imgName ?>" alt=""></div>
    <!-- END Side top info -->
    <div class="situation_wrap">
        <div class="situation_title">상황판</div>
        <div class="situation_table">
            <table class="table_noline">
                <?php if(0 == $u_business){ ?>
                <tr>
                    <td><a href="/sports_w/prematch_betting_list.php" style="color:#AAAAAA">신규 스포츠 베팅</a></td>
                    <td id="today_bet_sports" class="tblue">0</td>
                </tr>
                <tr>
                    <td><a href="/sports_w/prematch_betting_list.php?betting_type=ON" style="color:#AAAAAA">신규 클래식 베팅</a></td>
                    <td id="today_bet_classic" class="tblue">0</td>
                </tr>
                <tr>
                    <td><a href="/sports_w/realtime_betting_list.php" style="color:#AAAAAA">신규 실시간 베팅</a></td>
                    <td id="today_bet_real" class="tblue">0</td>
                </tr>
                <?php }  ?>
                <tr>
                    <td>총 입금</td>
                    <td id="tot_ch_money_3" class="tblue">0</td>
                </tr>
                <tr>
                    <td>총 출금</td>
                    <td id="tot_ex_money_3" class="tred">0</td>
                </tr>

                <tr><td colspan="2"></td></tr>
                <tr>
                    <td><a href="/sports_w/prematch_betting_list.php?betting_type=OFF&betting_key=1&betting_val=0" style="color:#AAAAAA">남은 스포츠 베팅</a></td>
                    <td id="tot_bet_sports_ing" class="tblue">0</td>
                </tr>
                <tr>
                    <td><a href="/sports_w/prematch_betting_list.php?betting_type=ON&betting_key=1&betting_val=0" style="color:#AAAAAA">남은 클래식 베팅</a></td>
                    <td id="tot_bet_classic_ing" class="tblue">0</td>
                </tr>
                <tr>
                    <td><a href="/sports_w/realtime_betting_list.php?betting_key=1&betting_val=0" style="color:#AAAAAA">남은 실시간 베팅</a></td>
                    <td id="tot_bet_real_ing" class="tblue">0</td>
                </tr>
                <tr>
                    <td><a href="#" style="color:#aaa;">남은 카지노 베팅</a></td>
                    <td id="total_casino_ing_bet_money" class="tblue">0</td>
                </tr>
                <tr>
                    <td><a href="#" style="color:#aaa;">남은 슬롯 베팅</a></td>
                    <td id="total_slot_ing_bet_money" class="tblue">0</td>
                </tr>
                <tr><td colspan="2"></td></tr>

                <tr>
                    <td>스포츠 베팅</td>
                    <td id="tot_bet_sports" class="tblue">0</td>
                </tr>
                <tr>
                    <td>스포츠 당첨</td>
                    <td id="tot_bet_sports_win" class="tred">0</td>
                </tr>
                <tr>
                    <td>클래식 베팅</td>
                    <td id="tot_bet_classic" class="tblue">0</td>
                </tr>
                <tr>
                    <td>클래식 당첨</td>
                    <td id="tot_bet_classic_win" class="tred">0</td>
                </tr>
                <tr>
                    <td>실시간 베팅</td>
                    <td id="tot_bet_real" class="tblue">0</td>
                </tr>
                <tr>
                    <td>실시간 당첨</td>
                    <td id="tot_bet_real_win" class="tred">0</td>
                </tr>

                <?php if('ON' == IS_EOS_POWERBALL){ ?>
                    <tr>
                        <td>EOS 파워볼 베팅</td>
                        <td id="tot_bet_mini_eos_power" class="tblue">0</td>
                    </tr>
                    <tr>
                        <td>EOS 파워볼 당첨</td>
                        <td id="tot_bet_mini_eos_power_win" class="tred">0</td>
                    </tr>
                <?php } ?>
                <?php if('ON' == IS_POWERBALL){ ?>
                    <tr>
                        <td>파워볼 베팅</td>
                        <td id="tot_bet_mini_power" class="tblue">0</td>
                    </tr>
                    <tr>
                        <td>파워볼 당첨</td>
                        <td id="tot_bet_mini_power_win" class="tred">0</td>
                    </tr>
                <?php } ?>

                <tr>
                    <td>파워사다리 베팅</td>
                    <td id="tot_bet_mini_pladder" class="tblue">0</td>
                </tr>
                <tr>
                    <td>파워사다리 당첨</td>
                    <td id="tot_bet_mini_pladder_win" class="tred">0</td>
                </tr>

                <tr>
                    <td>키노사다리 베팅</td>
                    <td id="tot_bet_mini_kladder" class="tblue">0</td>
                </tr>
                <tr>
                    <td>키노사다리 당첨</td>
                    <td id="tot_bet_mini_kladder_win" class="tred">0</td>
                </tr>

                <tr>
                    <td>가상축구 베팅</td>
                    <td id="tot_bet_mini_b_soccer" class="tblue">0</td>
                </tr>
                <tr>
                    <td>가상축구 당첨</td>
                    <td id="tot_bet_mini_b_soccer_win" class="tred">0</td>
                </tr>

                <tr>
                    <td>카지노 배팅</td>
                    <td id="tot_casino_bet" class="tblue">0</td>
                </tr>
                <tr>
                    <td>카지노 당첨</td>
                    <td id="tot_casino_bet_win" class="tred">0</td>
                </tr>
                <tr>
                    <td>슬롯게임 배팅</td>
                    <td id="tot_slot_bet" class="tblue">0</td>
                </tr>
                <tr>
                    <td>슬롯게임 당첨</td>
                    <td id="tot_slot_bet_win" class="tred">0</td>
                </tr>
                <?php if('ON' == IS_HOLDEM){ ?>
                    <tr>
                        <td>홀덤 배팅</td>
                        <td id="tot_holdem_bet" class="tred">0</td>
                    </tr>
                    <tr>
                        <td>홀덤 당첨</td>
                        <td id="tot_holdem_bet_win" class="tred">0</td>
                    </tr>
                <?php } ?>

<?php if ('ON' == IS_ESPORTS_KEYRON) { ?>
                    <tr>
                        <td>이스포츠 배팅</td>
                        <td id="tot_espt_bet" class="tblue">0</td>
                    </tr>
                    <tr>
                        <td>이스포츠 당첨</td>
                        <td id="tot_espt_bet_win" class="tred">0</td>
                    </tr>
<?php } ?>

<?php if ('ON' == IS_HASH) { ?>
                    <tr>
                        <td>해시게임 배팅</td>
                        <td id="tot_hash_bet" class="tblue">0</td>
                    </tr>
                    <tr>
                        <td>해시게임 당첨</td>
                        <td id="tot_hash_bet_win" class="tred">0</td>
                    </tr>
<?php } ?>
                <?php if(0 == $u_business){ ?>
                <tr>
                    <td>보유 머니</td>
                    <td id="tot_mem_money" class="tred">0</td>
                </tr>
                <tr>
                    <td>보유 포인트</td>
                    <td id="tot_mem_point" class="tred">0</td>
                </tr>
                <?php }else{ ?>
                <tr>
                    <td>회원보유 머니</td>
                    <td id="tot_mem_money" class="tred">0</td>
                </tr>
                <tr>
                    <td>회원보유 포인트</td>
                    <td id="tot_mem_point" class="tred">0</td>
                </tr>
                <?php }?>
                <?php if(0 == $u_business){ ?>
                <tr>
                    <td>현재 접속자</td>
                    <td id="tot_user_conn" class="tblue">0</td>
                </tr>
                <tr>
                    <td>총판지급액</td>
                    <td id="tot_distributor_point" class="tred">0</td>
                </tr>
                <?php } ?>
                <tr><td colspan="2"></td></tr>
            </table>
        </div>    
    </div>
    <!-- sidebar nav -->
    <ul class="nav">
        <!--<li class="has-sub">
            <a href="/main.html">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_widgets vam ml20 mr10"></i>
                <h5>상황판</h5>
            </a>
        </li>-->
        <li class="has-sub <?= $menu_mem_act ?>">
            <a href="/member_w/mem_list.php" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_group vam ml20 mr10"></i>
                <h5>회원 관리</h5>
            </a>
            <ul class="sub-menu">
                <li><a href="/member_w/mem_list.php?srch_status=1"><b class="<?= $menu_mem_list_chk ?>"></b>회원정보</a></li>
                <?php if(0 == $u_business){ ?>
                <!--<li><a href="/member_w/mem_item_list.php?srch_status=1"><b class="<?//= $menu_mem_item_list_chk ?>"></b>회원 아이템 정보</a></li>-->
                <li><a href="/member_w/mem_list_normal.php"><b class="<?= $menu_mem_list_normal_chk ?>"></b>일반 모니터링 회원</a></li>
                <li><a href="/member_w/mem_list_charge.php"><b class="<?= $menu_mem_list_charge_chk ?>"></b>충전 모니터링 회원</a></li>
                <li><a href="/member_w/mem_list_security.php"><b class="<?= $menu_mem_list_security_chk ?>"></b>보안 모니터링 회원</a></li>
                <li><a href="/member_w/mem_list_dupbet.php"><b class="<?= $menu_mem_list_dupbet_chk ?>"></b>중복베팅 모니터링 회원</a></li>
                <li><a href="/member_w/mem_list_nodeposit.php"><b class="<?= $menu_mem_list_nodeposit_chk ?>"></b>미입금 회원</a></li>
                <li><a href="/member_w/distributor_list.php"><b class="<?= $menu_distributor_list_chk ?>"></b>총판정보</a></li>
                <li><a href="/member_w/mem_list_edit.php"><b class="<?= $menu_mem_list_edit_chk ?>"></b>회원 정보 변경이력</a></li>
                <li><a href="/member_w/mem_list_login.php"><b class="<?= $menu_mem_list_login_chk ?>"></b>로그인 정보</a></li>
                <li><a href="/member_w/mem_list_sms.php"><b class="<?= $menu_mem_list_sms_chk ?>"></b>SMS 인증 내역</a></li>
                <li><a href="/member_w/mem_list_recomm.php"><b class="<?= $menu_mem_list_recomm_chk ?>"></b>추천회원 정보</a></li>
                <!--<li><a href="/member_w/mem_list_ipblock.php"><b class="<?= $menu_mem_list_ipblock_chk ?>"></b>IP 차단 내역</a></li>-->
                <li><a href="/member_w/mem_list_account_renew.php"><b class="<?= $menu_mem_list_account_chk ?>"></b>통장 로그 조회</a></li>
              
                <li><a href="/member_w/msg_write.php"><b class="<?= $menu_msg_write_chk ?>"></b>쪽지 발송</a></li>
                <li><a href="/member_w/msg_send_list.php"><b class="<?= $menu_msg_send_list_chk ?>"></b>쪽지 발송 내역</a></li>
                <?php } ?>
            </ul>
        </li>
        <li class="has-sub <?= $menu_sports_act ?>"><!--해당메뉴 선택 클래스명 active-->
            <a href="/sports_w/prematch_manager.php" rel="스포츠" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_trending_up vam ml20 mr10"></i>
                <h5>스포츠</h5>
            </a>
            <ul class="sub-menu">
                <?php if(0 == $u_business){ ?>
                <li><a href="/sports_w/prematch_manager.php"><b class="<?= $menu_sports_menu1_chk ?>"></b>프리매치 관리</a></li>
                <li><a href="/sports_w/realtime_manager.php"><b class="<?= $menu_sports_menu2_chk ?>"></b>실시간 관리</a></li>
                <li><a href="/sports_w/sports_manager.php"><b class="<?= $menu_sports_menu3_chk ?>"></b>종목 관리</a></li>
                <li><a href="/sports_w/bet_type_manager.php"><b class="<?= $menu_sports_menu4_chk ?>"></b>배팅 타입 관리</a></li>
                <li><a href="/sports_w/sports_league_manager.php"><b class="<?= $menu_sports_menu5_chk ?>"></b>리그 관리</a></li>
                <li><a href="/sports_w/sports_team_name_manager.php"><b class="<?= $menu_sports_menu6_chk ?>"></b>팀명 관리</a></li>
                <li><a href="/sports_w/sports_set_exchange_rate.php"><b class="<?= $menu_sports_menu7_chk ?>"></b>환수율 설정</a></li>
                 <?php } ?>
                <li><a href="/sports_w/prematch_betting_list.php"><b class="<?= $menu_sports_menu10_chk ?>"></b>프리매치 배팅목록</a></li>
                <li><a href="/sports_w/realtime_betting_list.php"><b class="<?= $menu_sports_menu11_chk ?>"></b>실시간 배팅목록</a></li>
            </ul>
        </li>
        <li class="has-sub <?= $menu_mini_game_act ?>"><!--해당메뉴 선택 클래스명 active-->
            <a href="/mini_game_w/mini_game_list.php" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_monetization_on vam ml20 mr10"></i>
                <h5>미니게임</h5>
            </a>
            <ul class="sub-menu">
                <?php if(0 == $u_business){ ?>
                    <li><a href="/mini_game_w/mini_game_list.php"><b class="<?= $menu_mini_game_menu1_chk ?>"></b>내역</a></li>
                <?php } ?>
                <li><a href="/mini_game_w/mini_game_betting_list.php"><b class="<?= $menu_mini_game_menu2_chk ?>"></b>베팅내역</a></li>
                <li><a href="/mini_game_w/mini_game_b_soccer_betting_list.php"><b class="<?= $menu_mini_game_menu3_chk ?>"></b>가상축구 베팅내역</a></li>
                <?php if(0 == $u_business){ ?>
                <li><a href="/mini_game_w/mini_game_config.php"><b class="<?= $menu_mini_game_menu4_chk ?>"></b>설정</a></li>
                <?php } ?>
            </ul>
        </li>
<?php if (IMAGE_PATH != 'asbet') { ?>
            <li class="has-sub <?= $menu_casino_act ?>">
                <a href="/game_w/casino_betting_list.php" class="disableLink">
                    <b class="ontab fl"></b>
                    <b class="caret fr"></b>
                    <i class="mte i_monetization_on vam ml20 mr10"></i>
                    <h5>카지노게임</h5>
                </a>
                <!-- <a href="/game_w/casino_betting_list_sb.php" class="disableLink">
                    <b class="ontab fl"></b>
                    <b class="caret fr"></b>
                    <i class="mte i_monetization_on vam ml20 mr10"></i>
                    <h5>카지노게임</h5>
                </a> -->
                <ul class="sub-menu">
                    <li><a href="/game_w/casino_betting_list.php"><b class="<?= $menu_casino_menu1_chk ?>"></b>카지노 배팅내역</a></li>
                    <!-- <li><a href="/game_w/casino_betting_list_sb.php"><b class="<?= $menu_casino_menu1_chk ?>"></b>카지노 배팅내역</a></li> -->
                    <li><a href="/game_w/slot_betting_list.php"><b class="<?= $menu_casino_menu2_chk ?>"></b>슬롯 배팅내역</a></li>
                    <?php if('ON' == IS_HOLDEM){ ?>
                    <li><a href="/game_w/holdem_betting_list.php"><b class="<?=$menu_casino_menu5_chk?>"></b>홀덤 배팅내역</a></li>
                    <?php } ?>
    <?php if ('ON' == IS_ESPORTS_KEYRON) { ?>
                        <li><a href="/game_w/esport_betting_list.php"><b class="<?= $menu_casino_menu3_chk ?>"></b>E스포츠 배팅내역</a></li>
                        <!--<li><a href="/game_w/kiron_betting_list.php"><b class=""></b>키론가상 배팅내역</a></li>-->
    <?php } ?>
                </ul>
            </li>

                    <?php if ('ON' == IS_HASH) { ?>
                <li class="has-sub <?= $menu_hash_act ?>">
                    <a href="/game_w/baccara_betting_list.php" class="disableLink">
                        <b class="ontab fl"></b>
                        <b class="caret fr"></b>
                        <i class="mte i_monetization_on vam ml20 mr10"></i>
                        <h5>해쉬게임</h5>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/game_w/baccara_betting_list.php"><b class="<?= $menu_hash_menu1_chk ?>"></b>바카라 배팅내역</a></li>
                        <li><a href="/game_w/roulette_betting_list.php"><b class="<?= $menu_hash_menu2_chk ?>"></b>룰렛 배팅내역</a></li>
                        <li><a href="/game_w/highrow_betting_list.php"><b class="<?= $menu_hash_menu3_chk ?>"></b>하이로우 배팅내역</a></li>
                    </ul>
                </li>  <?php } ?>
<?php } ?>
                 
        <?php if(0 == $u_business){ ?>
        <li class="has-sub <?= $menu_money_act ?>"><!--해당메뉴 선택 클래스명 active-->
            <a href="/money_w/charge_list.php" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_monetization_on vam ml20 mr10"></i>
                <h5>머니관리</h5>
            </a>
            <ul class="sub-menu">
                <li><a href="/money_w/charge_list.php"><b class="<?= $menu_charge_chk ?>"></b>충전관리</a></li>
                <li><a href="/money_w/exchange_list.php"><b class="<?= $menu_exchange_chk ?>"></b>환전관리</a></li>
                <li><a href="/money_w/money_log_list.php"><b class="<?= $menu_money_log_chk ?>"></b>머니사용이력</a></li>
                <!--<li><a href="/money_w/gmoney_log_list.php"><b class="<?= $menu_gmoney_log_chk ?>"></b>지머니사용이력</a></li>-->
                <li><a href="/money_w/point_log_list.php"><b class="<?= $menu_point_log_chk ?>"></b>포인트사용이력</a></li>
                <li><a href="/money_w/charge_sms_list.php"><b class="<?= $menu_auto_charge_sms_chk ?>"></b>SMS입금이력</a></li>
            </ul>
        </li>
        <li class="has-sub <?= $menu_config_act ?>"><!--해당메뉴 선택 클래스명 active-->
            <a href="/siteconfig_w/adm_log_list.php" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_settings vam ml20 mr10"></i>
                <h5>사이트관리</h5>
            </a>
            <ul class="sub-menu">
                <li><a href="/siteconfig_w/adm_log_list.php"><b class="<?= $menu_config_1_chk ?>"></b>관리자접속내역</a></li>
                <li><a href="/siteconfig_w/adm_id_list.php"><b class="<?= $menu_config_9_chk ?>"></b>관리자 아이디 관리</a></li>
                <!--<li><a href="/siteconfig_w/ip_block.php"><b class="<?= $menu_config_3_chk ?>"></b>개별 IP 차단관리</a></li>
                <li><a href="/siteconfig_w/domain_code.php"><b class="<?= $menu_config_4_chk ?>"></b>도메인 코드 관리</a></li>-->
                <li><a href="/siteconfig_w/account_level.php"><b class="<?= $menu_config_5_chk ?>"></b>레벨별 계좌 설정</a></li>
                <li><a href="/siteconfig_w/site_config.php"><b class="<?= $menu_config_8_chk ?>"></b>사이트 설정</a></li>
                <li><a href="/siteconfig_w/level_charge_type.php"><b class="<?= $menu_config_10_chk ?>"></b>레벨별 충전방식 설정</a></li>
                <li><a href="/siteconfig_w/set_bet_amount_by_level.php"><b class="<?= $menu_config_11_chk ?>"></b>레벨별 베팅금액 설정</a></li>
                <li><a href="/siteconfig_w/set_rolling_by_level.php"><b class="<?= $menu_config_17_chk ?>"></b>레벨별 롤링콤푸 설정</a></li>
                <li><a href="/sports_w/event_charge_set.php"><b class="<?= $menu_config_12_chk ?>"></b>이벤트충전 설정</a></li>
                <!-- <li><a href="/siteconfig_w/average_dividend_provider.php"><b class="<?= $menu_config_13_chk ?>"></b>배당사 설정</a></li>-->
                <li><a href="/member_w/user_level_setting.php"><b class="<?= $user_level_setting ?>"></b>레벨등업설정</a></li>
                <li><a href="/siteconfig_w/inspection.php"><b class="<?= $menu_config_14_chk ?>"></b>점검문자설정</a></li>
                <li><a href="/siteconfig_w/casino_prd_list.php"><b class="<?= $menu_config_15_chk ?>"></b>카지노게임사설정</a></li>
            </ul>
        </li>
        <?php } ?>
        <li class="has-sub <?= $menu_stats_act ?>"><!--해당메뉴 선택 클래스명 active-->
            <a href="/stats_w/stats_user_list_new.php" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte i_assessment vam ml20 mr10"></i>
                <h5>통계 관리</h5>
            </a>
            <ul class="sub-menu">
                <li><a href="/stats_w/stats_user_list_new.php"><b class="<?= $menu_stats_1_chk ?>"></b>사용자별 현황</a></li>
                <!-- <li><a href="/stats_w/stats_day_list_new_tm.php"><b class="<?= $menu_stats_2_chk ?>"></b>날짜별 현황</a></li>-->
                <li><a href="/stats_w/stats_day_list_real_time.php"><b class="<?= $menu_stats_9_chk ?>"></b>실시간 날짜별 현황</a></li>
                <li><a href="/stats_w/stats_month_list.php"><b class="<?= $menu_stats_8_chk ?>"></b>월별 통계</a></li>
                <?php if(0 == $u_business){ ?>
                <li><a href="/stats_w/stats_type_list_new.php"><b class="<?= $menu_stats_3_chk ?>"></b>타입별 현황</a></li>
                <li><a href="/stats_w/stats_league_list.php"><b class="<?= $menu_stats_4_chk ?>"></b>리그별 현황</a></li>
                 <?php } ?>
            </ul>
        </li>
        <?php if(0 == $u_business){ ?>
        <li class="has-sub <?= $menu_board_act ?>"><!--해당메뉴 선택 클래스명 active-->
            <a href="/board_w/event_list.php" rel="게시판" class="disableLink">
                <b class="ontab fl"></b>
                <b class="caret fr"></b>
                <i class="mte mte i_chat vam ml20 mr10"></i>
                <h5>게시판</h5>
            </a>
            <ul class="sub-menu">
                <li><a href="/board_w/banner_list.php"><b class="<?= $menu_board_menu_6_chk ?>"></b>메인배너관리</a></li>
                <li><a href="/board_w/popup_list.php"><b class="<?= $menu_board_menu_9_chk ?>"></b>메인팝업관리</a></li>
                <li><a href="/board_w/event_list.php"><b class="<?= $menu_board_menu_1_chk ?>"></b>이벤트</a></li>
                <!-- <li><a href="/board_w/betting_list.php"><b class="<?= $menu_board_menu_2_chk ?>"></b>배팅규정</a></li> --> 
                <li><a href="/board_w/board_list.php"><b class="<?= $menu_board_menu_3_chk ?>"></b>공지사항</a></li>
                <li><a href="/board_w/service_center_list.php"><b class="<?= $menu_board_menu_4_chk ?>"></b>고객센터</a></li>
                <li><a href="/board_w/template_list.php"><b class="<?= $menu_board_menu_5_chk ?>"></b>템플릿관리</a></li>
            </ul>
        </li>
         <?php } ?>
    </ul>
    <!-- end sidebar nav -->
</div>
<div class="side_bg"></div>
<!-- END SIDE AREA -->
<script>
    let is_main_stat = <?= $_SESSION['is_main_stat'] ?>;
    let u_business = <?= $u_business ?>;
    $(document).ready(function () {
        if (is_main_stat == 1) {
            getStat();
           
            let main_timerId = setInterval("getStat()", 20000);
        }
    });

    function getStat() {
      
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/common/_main_stat_data_view.php',
            success: function (result) {
                if (result['retCode'] == "1000") {

                    // 총입금, 총출금
                    (document.getElementById("tot_ch_money_3") || {}).innerHTML = result['tot_ch_money_3'];
                    (document.getElementById("tot_ex_money_3") || {}).innerHTML = result['tot_ex_money_3'];

                    // 보유머니, 보유포인트
                    (document.getElementById("tot_mem_money") || {}).innerHTML = result['tot_mem_money'];
                    (document.getElementById("tot_mem_point") || {}).innerHTML = result['tot_mem_point'];

                    // 남은 스포츠 배팅, 스포츠 배팅, 스포츠 당첨
                    (document.getElementById("tot_bet_sports_ing") || {}).innerHTML = result['tot_bet_money_sports_ing'];
                    (document.getElementById("tot_bet_sports") || {}).innerHTML = result['tot_bet_money_sports'];
                    (document.getElementById("tot_bet_sports_win") || {}).innerHTML = result['tot_bet_money_sports_win'];

                    // 남은 클래식 배팅, 클래식 배팅, 클래식 당첨
                    (document.getElementById("tot_bet_classic_ing") || {}).innerHTML = result['tot_bet_money_classic_ing'];
                    (document.getElementById("tot_bet_classic") || {}).innerHTML = result['tot_bet_money_classic'];
                    (document.getElementById("tot_bet_classic_win") || {}).innerHTML = result['tot_bet_money_classic_win'];

                    // 남은 실시간 배팅, 실시간  배팅, 실시간  당첨
                    (document.getElementById("tot_bet_real_ing") || {}).innerHTML = result['tot_bet_money_real_ing'];
                    (document.getElementById("tot_bet_real") || {}).innerHTML = result['tot_bet_money_real'];
                    (document.getElementById("tot_bet_real_win") || {}).innerHTML = result['tot_bet_money_real_win'];

                    // EOS 파워볼 배팅, 파워볼 당첨
                    (document.getElementById("tot_bet_mini_eos_power") || {}).innerHTML = result['tot_bet_money_mini_eos_power'];
                    (document.getElementById("tot_bet_mini_eos_power_win") || {}).innerHTML = result['tot_bet_money_mini_eos_power_win'];
                    
                    // 파워볼 배팅, 파워볼 당첨
                    (document.getElementById("tot_bet_mini_power") || {}).innerHTML = result['tot_bet_money_mini_power'];
                    (document.getElementById("tot_bet_mini_power_win") || {}).innerHTML = result['tot_bet_money_mini_power_win'];

                    // 파워사다리 배팅, 당첨
                    (document.getElementById("tot_bet_mini_pladder") || {}).innerHTML = result['tot_bet_money_mini_pladder'];
                    (document.getElementById("tot_bet_mini_pladder_win") || {}).innerHTML = result['tot_bet_money_mini_pladder_win'];

                    // 키노사다리 배팅, 당첨
                    (document.getElementById("tot_bet_mini_kladder") || {}).innerHTML = result['tot_bet_money_mini_kladder'];
                    (document.getElementById("tot_bet_mini_kladder_win") || {}).innerHTML = result['tot_bet_money_mini_kladder_win'];

                    // 가상축구 배팅, 당첨
                    (document.getElementById("tot_bet_mini_b_soccer") || {}).innerHTML = result['tot_bet_money_mini_b_soccer'];
                    (document.getElementById("tot_bet_mini_b_soccer_win") || {}).innerHTML = result['tot_bet_money_mini_b_soccer_win'];

                    // 어드민 일때만 추가
                    if(u_business == 0){
                        (document.getElementById("today_bet_sports") || {}).innerHTML = result['tot_sports_count'];
                        (document.getElementById("today_bet_classic") || {}).innerHTML = result['tot_classic_count'];
                        (document.getElementById("today_bet_real") || {}).innerHTML = result['tot_realtime_count'];
                    
                        // 현재 접속자
                        (document.getElementById("tot_user_conn") || {}).innerHTML = result['tot_user_conn'];
                        
                        // 총판지급액
                        (document.getElementById("tot_distributor_point") || {}).innerHTML = result['tot_distributor_point'];
                    }

                    // 카지노
                    (document.getElementById("tot_casino_bet") || {}).innerHTML = result['tot_casino_bet'];
                    (document.getElementById("tot_casino_bet_win") || {}).innerHTML = result['tot_casino_bet_win'];

                    // 슬롯
                    (document.getElementById("tot_slot_bet") || {}).innerHTML = result['tot_slot_bet'];
                    (document.getElementById("tot_slot_bet_win") || {}).innerHTML = result['tot_slot_bet_win'];

                    let IS_ESPORTS_KEYRON = '<?= IS_ESPORTS_KEYRON ?>';
                    let IS_HASH = '<?= IS_HASH ?>';
                    // 이스포츠 / 키론 / 해시
                    if ("ON" == IS_ESPORTS_KEYRON) {
                        (document.getElementById("tot_espt_bet") || {}).innerHTML = result['tot_espt_bet'];
                        (document.getElementById("tot_espt_bet_win") || {}).innerHTML = result['tot_espt_bet_win'];
                    }

                    if ("ON" == IS_HASH) {
                        (document.getElementById("tot_hash_bet") || {}).innerHTML = result['tot_hash_bet'];
                        (document.getElementById("tot_hash_bet_win") || {}).innerHTML = result['tot_hash_bet_win'];
                    }
                    // 카지노,슬롯 남은 베팅금
                    (document.getElementById("total_casino_ing_bet_money") || {}).innerHTML = result['total_casino_ing_bet_money'];
                    (document.getElementById("total_slot_ing_bet_money") || {}).innerHTML = result['total_slot_ing_bet_money'];
                    
                    // 홀덤
                    (document.getElementById("tot_holdem_bet") || {}).innerHTML = result['tot_holdem_bet'];
                    (document.getElementById("tot_holdem_bet_win") || {}).innerHTML = result['tot_holdem_bet_win'];

                    <?php if (defined('API_GATEWAY_BASE_URL')): ?>
                    SBCasinoStats.init()
                    <?php endif ?>
                    return;
                } else {
                    return;
                }
            },
            error: function (request, status, error) {

                return;
            }
        });
    }


// 메뉴 이동현상 제거
    const disable_menu_list = document.querySelectorAll(".disableLink");
    for (let menu of disable_menu_list) {
        // console.log(menu);
        $(menu).removeAttr('href');
        $(menu).css('cursor', 'pointer');
    }

// 슬라이드 추가기능
    $(document).on('click', '.has-sub a', function () {
        // 현재 메뉴
        const current_menu_sub = $(this).next('.sub-menu');
        const current_menu = $(this).closest("li");

        // 다른 메뉴의 서브메뉴
        const other_menu = current_menu.siblings('li');
        const other_menu_sub = other_menu.find('.sub-menu');

        if (current_menu_sub.hasClass('slide-open')) {
            current_menu_sub.slideUp();
            current_menu_sub.removeClass('slide-open');

            current_menu.removeClass("active");

            other_menu.removeClass("active");
            other_menu_sub.slideUp();
            other_menu_sub.removeClass("slide-open");
        } else {
            current_menu_sub.slideDown();
            current_menu_sub.addClass('slide-open');

            current_menu.addClass("active");

            other_menu.removeClass("active");
            other_menu_sub.slideUp();
            other_menu_sub.removeClass("slide-open");
        }
    });


</script>


<?php if (defined('API_GATEWAY_BASE_URL')): ?>
<script>
const SBCasinoStats = (function ($) {
    ApiGateway.init('<?php echo API_GATEWAY_BASE_URL ?>', 'BTS');

    const numberFormat = (amount) => {
        let formatting = Intl.NumberFormat('en-US');
        return formatting.format(amount);
    }

    function fetchStats() {
        ApiGateway.fetch(
            'sbcasino/stats',
            'GET',
            {},
            {
                beforeFetch: () => {},
                afterFetch: (response) => {
                    if (response.success) {
                        const live = response.data.live
                        const slot = response.data.slot
                        $('#tot_casino_bet').text(numberFormat(live.total_bet))
                        $('#tot_casino_bet_win').text(numberFormat(live.total_win))
                        $('#tot_slot_bet').text(numberFormat(slot.total_bet))
                        $('#tot_slot_bet_win').text(numberFormat(slot.total_win))
                    }
                }
            }
        )
    }

    return {
        init: function () {
            fetchStats()
        }
    }
})(jQuery)
</script>
<?php endif ?>