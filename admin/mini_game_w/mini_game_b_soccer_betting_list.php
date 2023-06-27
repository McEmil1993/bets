<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
include_once(_LIBPATH . '/class_GameStatusUtil.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();

$today = date("Y/m/d");
$before_week = date("Y/m/d", strtotime("-1 week", time()));
$before_month = date("Y/m/d", strtotime("-1 month", time()));
$start_date = date("Y/m/d", strtotime("-3 day", time()));
$end_date = $today;


$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$p_data['vtype'] = trim(isset($_REQUEST['vtype']) ? $_REQUEST['vtype'] : 'all');
$p_data['srch_key'] = trim(isset($_REQUEST['srch_key']) ? $_REQUEST['srch_key'] : '');
$p_data['srch_val'] = trim(isset($_REQUEST['srch_val']) ? $_REQUEST['srch_val'] : '');
$p_data['betting_key'] = trim(isset($_REQUEST['betting_key']) ? $_REQUEST['betting_key'] : 0);
$p_data['league'] = trim(isset($_REQUEST['league']) ? $_REQUEST['league'] : 'Premiership');

$srch_basic = " b.bet_type = 6";
if($p_data['league'] != ''){
    $srch_basic .= " and g.league = '".$p_data['league']."'";
}

// 베팅상태
switch($p_data["betting_key"]) {
    case 1:
        $srch_basic .= " and b.bet_status = 1";
        break;
    case 2:
        $srch_basic .= " and b.bet_status = 3 and total_bet_money < take_money";
        break;
    case 3:
        $srch_basic .= " and b.bet_status = 3";
        break;
    case 4:
        $srch_basic .= " and b.bet_status = 3 and take_money = 0";
        break;
    case 5:
        $srch_basic .= " and b.bet_status = 5";
        break;
}

switch($p_data["srch_key"]) {
    case "s_idnick":
        if($p_data['srch_val'] !='') {
            $srch_basic .= "  and (a.id LIKE '%".$p_data['srch_val']."%' OR a.nick_name LIKE '%".$p_data['srch_val']."%') ";
        }
        break;
    case "s_accountname":
        if($p_data['srch_val'] !='') {
            $srch_basic .= " and a.account_name like '%".$p_data['srch_val']."%' ";
        }
        break;
    case "s_disline":
        if($p_data['srch_val'] !='') {
            $srch_basic .= " and a.dis_line_id='".$p_data['srch_val']."' ";
        }
        break;
    case "s_gameid":
        if($p_data['srch_val'] !='') {
            $srch_basic .= " and b.ls_fixture_id = ".$p_data['srch_val'];
            $srch_basic .= " or g.cnt = ".$p_data['srch_val'];
        }
        break;
}

// 총판
if ($_SESSION['u_business'] != 0) {
    $srch_basic = $srch_basic." AND a.dis_id = '".$_SESSION['aid']."'";
}   

$p_data['srch_s_date'] = trim(isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : $start_date);
$p_data['srch_e_date'] = trim(isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : $end_date);
$p_data['db_srch_s_date'] = str_replace('/', '-', $p_data['srch_s_date']).' 00:00:00';
$p_data['db_srch_e_date'] = str_replace('/', '-', $p_data['srch_e_date']).' 23:59:59';


$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
    // 게시물 전체갯수
    $p_data['sql'] = "SELECT COUNT(b.idx) AS CNT
                        FROM mini_game_member_bet AS b
                        LEFT JOIN mini_game_bet AS m ON m.markets_id = b.ls_markets_id
                        LEFT JOIN member AS a ON b.member_idx = a.idx
                        LEFT JOIN mini_game AS g ON g.id = b.ls_fixture_id";
    $p_data['sql'] .= " WHERE b.create_dt >= '".$p_data['db_srch_s_date']."' AND  b.create_dt <= '".$p_data['db_srch_e_date']."' and ";
    
    $p_data['sql'] .= $srch_basic;
    $p_data['sql'] .= ";";

    $db_dataArrCnt = $BdsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_dataArrCnt[0]['CNT'];
    
    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];
    
    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지
    
    if ($block >= $total_block) $last_page = $total_page;
    
    // 게시물이 하나 이상이다.
    if($total_cnt >= 0) {
        $p_data['sql'] = "SELECT b.idx AS bet_idx, 
                            a.idx AS m_idx, 
                            a.id as m_id,
                            a.nick_name,
                            m.game,
                            m.markets_id,
                            b.ls_fixture_id,
                            b.ls_markets_name,
                            b.bet_price,
                            b.total_bet_money,
                            b.take_money,
                            b.create_dt,
                            b.bet_status,
                            b.bet_type,
                            g.result,
                            g.result_score,
                            g.start_dt,
                            g.league
                        FROM mini_game_member_bet AS b
                        LEFT JOIN mini_game_bet AS m ON m.markets_id = b.ls_markets_id
                        LEFT JOIN member AS a ON b.member_idx = a.idx
                        LEFT JOIN mini_game AS g ON g.id = b.ls_fixture_id";
        $p_data['sql'] .= " WHERE b.create_dt >= '".$p_data['db_srch_s_date']."' AND  b.create_dt <= '".$p_data['db_srch_e_date']."' and ";
        $p_data['sql'] .= $srch_basic.' order by b.create_dt desc';
        $p_data['sql'] .= " LIMIT ".$p_data['start'].", ".$p_data['num_per_page']." ";
        $p_data['sql'] .= ";";

        $db_dataArr = $BdsAdminDAO->getQueryData($p_data);
    }
    
    $BdsAdminDAO->dbclose();
    

    foreach ($db_dataArr as $key => $row) {
        $result_arr = json_decode($row['result']);
        if($row['result_score'] != ''){
            $db_dataArr[$key] = (object)array_merge((array) $row, (array) json_decode($row['result_score']));
        }else{
            $db_dataArr[$key] = (object)array_merge((array) $row, (array) $result_arr);
        }
    }
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
        $('#betting_key').val(<?=$p_data['betting_key']?>).prop("selected",true);
        
        $('ul.tabs li').click(function(){
            var tab_id = $(this).attr('data-tab');

            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');

            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
        })

        $("#checkbox_css_all").change(function () {
            if ($("#checkbox_css_all").is(':checked')) {
                allCheckFunc(this);
            } else {
                $("[name=chk]").prop("checked", false);
            }
        });

        $("[name=chk]").change(function () {
            $("[name=checkbox_css_all]").prop("checked", false);
        });
    });
</script>
<script src="<?=_STATIC_COMMON_PATH?>/js/admCommon.js"></script>
<body>
<form id="popForm" name="popForm" method="post">
<input type="hidden" id="seq" name="seq">
<input type="hidden" id="m_idx" name="m_idx">
<input type="hidden" id="m_dis_id" name="m_dis_id">
<input type="hidden" id="selContent" name="selContent" value="3">
<input type="hidden" id="memberIdxList" name="memberIdxList">
</form>
<div class="wrap">
<?php

$menu_name = "mini_game_menu3";

include_once(_BASEPATH.'/common/left_menu.php');

include_once(_BASEPATH.'/common/iframe_head_menu.php');


$start_date = date("Y/m/d");
$end_date = date("Y/m/d");
?>
    <!-- Contents -->
    <div class="con_wrap">
        
        <div class="title">
            <a href="">
                <i class="mte i_group mte-2x vam"></i>
                <h4>가상축구 베팅내역</h4>
            </a>
            <div class="panel search_box">
                <?php if($p_data['league'] == 'Premiership'){ ?>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Premiership"><b>프리미어십</b></a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Superleague">슈퍼리그</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Euro Cup">유로컵</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=World Cup">월드컵</a></h5>
                <?php }else if($p_data['league'] == 'Superleague'){ ?>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Premiership">프리미어십</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Superleague"><b>슈퍼리그</b></a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Euro Cup">유로컵</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=World Cup">월드컵</a></h5>
                <?php }else if($p_data['league'] == 'Euro Cup'){ ?>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Premiership">프리미어십</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Superleague">슈퍼리그</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Euro Cup"><b>유로컵</b></a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=World Cup">월드컵</a></h5>
                <?php }else{ ?>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Premiership">프리미어십</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Superleague">슈퍼리그</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=Euro Cup">유로컵</a></h5>
                    <h5><a href="/mini_game_w/mini_game_b_soccer_betting_list.php?league=World Cup"><b>월드컵</b></a></h5>
                <?php } ?>
            </div>
        </div>
        <!-- list -->
        <div class="panel reserve">
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
<input type="hidden" name="vtype" id="vtype" value="<?=$p_data['vtype']?>">
<input type="hidden" name="league" id="league" value="<?=$p_data['league']?>">
            <div class="panel_tit">
            	<div class="search_form fl">
            		<div class="daterange">
                        <label for="datepicker-default"><i class="mte i_date_range mte-1x vat"></i></label>
                        <input type="text" class="" name="srch_s_date" id="datepicker-default" placeholder="날짜선택" value="<?=$p_data['srch_s_date']?>"/>
                    </div>
                    ~
                    <div class="daterange">
                        <label for="datepicker-autoClose"><i class="mte i_date_range mte-1x vat"></i></label>
                        <input type="text" class="" name="srch_e_date" id="datepicker-autoClose" placeholder="날짜선택"  value="<?=$p_data['srch_e_date']?>"/>
                    </div>
                    <div><a href="javascript:;" onClick="setDate('<?=$today?>','<?=$today?>');" class="btn h30 btn_blu">오늘</a></div>
                    <div><a href="javascript:;" onClick="setDate('<?=$before_week?>','<?=$today?>');" class="btn h30 btn_orange">1주일</a></div>
                    <div><a href="javascript:;" onClick="setDate('<?=$before_month?>','<?=$today?>');" class="btn h30 btn_green">한달</a></div>
                    <div class="" style="padding-right: 10px;"></div>
                    <div class="" style="padding-right: 10px;">
                        <select name="betting_key" id="betting_key">
                            <option value="0">배팅상태</option>
                            <option value="1">결과전</option>
                            <option value="2">적중</option>
                            <option value="3">정산완료</option>
                            <option value="4">낙첨</option>
                            <option value="5">취소</option>
                        </select>
                    </div>
                    <div class="" style="padding-right: 10px;">
                        <select name="srch_key" id="srch_key">
                            <option value="s_idnick" <?php if($p_data['srch_key']=='s_idnick') { echo "selected"; }?>>아이디 및 닉네임</option>
                            <option value="s_accountname" <?php if($p_data['srch_key']=='s_accountname') { echo "selected"; }?>>계정명</option>
                            <option value="s_gameid" <?php if($p_data['srch_key']=='s_gameid') { echo "selected"; }?>>경기번호</option>
                        </select>
                    </div>
                    
                    <div class="">
                        <input type="text" name="srch_val" id="srch_val" class=""  placeholder="검색" value="<?=$p_data['srch_val']?>"/>
                    </div>
                	<div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                	<div><a href="javascript:openNotePop();" style="margin-left: 10px" class="btn h30 btn_blu">쪽지</a></div>
            	</div>
            	<div class="search_form fr">
                	총 <?=number_format($total_cnt)?>건
            	</div>
            	
            </div>
</form>            
            <div class="tline">
                <table class="mlist">
                    <tr>
                    	<th>
                            <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                <label for="checkbox_css_all"></label>
                            </div>
                        </th>
                    	<th>번호</th>
                        <th>아이디</th>
                        <th>닉네임</th>
                        <th>리그</th>
                        <th>팀</th>
                        <th>스코어</th>
                        <th>회차</th>
                        <th>경기번호</th>
                        <th>배팅진행내역</th>
                        <th>배당율</th>
                        <th>베팅액</th>
                        <th>적중금</th>
                        <th>베팅시간</th>
                        <th>베팅결과</th>
                        <th>게임결과</th>
                        <th>취소</th>
                    </tr>
<?php 
if($total_cnt > 0) {
    $i=0;
    if(!empty($db_dataArr)){
        foreach($db_dataArr as $row) {
        	$chkbox_css[$i] = "checkbox_css_" . $i;
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            //print_r($row);
            $bet_idx = $row -> bet_idx;
            
            $status = '';
            //! 추후에 status 구현되면 사용
            // switch ($row['bet_status']) {
            //     case 1: $status = '결과전'; break;
            //     case 2: $status = '당첨'; break;
            //     case 3: $status = '정산완료'; break;
            //     case 4: $status = '미당첨'; break;
            //     case 5: $status = '취소'; break;
            //     case 6: $status = '적특'; break;
            //     default: $status = '결과전'; break;
            // }
        
            $gameResult = !empty($row -> game) && !empty($row -> result) ? gameResult($row -> game, $row ->result) : [];
            $gameResult = !empty($row -> result_score) ? gameResult($row -> game, $row ->result_score) : $gameResult;

            $status = '';
            $status_color = '';

            // else if ($row ->total_bet_money > $row -> take_money) {
            //     $status = '낙첨';
            //     $status_color = '#F9BFBF';
            // }

            if ($row -> markets_id == '13001') {
                if (strpos($gameResult,'승') !== false) {
                    $status = '적중';
                    $status_color = '#9FD5E9';
                } else {
                    $status = '낙첨';
                    $status_color = '#F9BFBF';
                }
            } else if ($row -> markets_id == '13002') {
                if (strpos($gameResult,'무') !== false) {
                    $status = '적중';
                    $status_color = '#9FD5E9';
                } else {
                    $status = '낙첨';
                    $status_color = '#F9BFBF';
                }
            } else if ($row -> markets_id == '13003') {
                if (strpos($gameResult,'패') !== false) {
                    $status = '적중';
                    $status_color = '#9FD5E9';
                } else {
                    $status = '낙첨';
                    $status_color = '#F9BFBF';
                }
            } else if ($row -> markets_id == '13004') {
                if (strpos($gameResult,'오버') !== false) {
                    $status = '적중';
                    $status_color = '#9FD5E9';
                } else {
                    $status = '낙첨';
                    $status_color = '#F9BFBF';
                }
            } else if ($row -> markets_id == '13005') {
                if (strpos($gameResult,'언더') !== false) {
                    $status = '적중';
                    $status_color = '#9FD5E9';
                } else {
                    $status = '낙첨';
                    $status_color = '#F9BFBF';
                }
            }
            
            if ($gameResult == "") {
                $status = '결과전';
                $status_color = '';
            }
            if($row -> total_bet_money == $row -> take_money) {
                $status = '취소';
                $status_color = '#FFFACC';
            }

            $cnt = 0;
            if($row -> bet_type == 3){
                $time = explode(' ', $row -> start_dt)[1];
                $time = explode(':', $time);
                $cnt = round((($time[0]*60) + $time[1])/5) + 1;
            }else if($row -> bet_type == 6){
                $cnt = json_decode($row -> result , true)['oid'];
            }else{
                $cnt = json_decode($row -> result , true)['cnt'];
            }
            ?>
                    <tr onmouseover="this.style.backgroundColor='#FDF2E9';" onmouseout="this.style.backgroundColor='#ffffff';">
                        <td>
                        	<div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                        		<input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $row -> bet_idx ?>" data-member-idx="<?= $row -> m_idx ?>"/>
                        		<label for="<?= $chkbox_css[$i] ?>"></label>
                        	</div>
                        </td>
                    	<td><?=$total_cnt-$num?></td>
                        <td style='text-align:center'><?=$row -> m_id ?></td>
                        <td style='text-align:center'><?=$row -> nick_name?></td>
                    	<td style='text-align:center'><?=GameStatusUtil::getLeagueName($row->league)?></td>
                        <td style='text-align:center'><?=$row -> home.' VS '.$row -> away?></td>
                        <td style='text-align:center'><?=$row -> scoreh.' : '.$row -> scorea?></td>
                        <td style='text-align:center'><?=$cnt?></td>
                        <td style='text-align:center'><?=$row -> ls_fixture_id ?></td>
                        <td style='text-align:center'><?=$row -> ls_markets_name ?></td>
                        <td style='text-align:right'><?=$row -> bet_price?></td>
                        <td style='text-align:right'><?=number_format($row -> total_bet_money)?></td>
                        <td style='text-align:right'><?=number_format($row -> take_money)?></td>
                        <td style='text-align:right'><?=$row -> create_dt ?></td>
                        <td style='text-align:center; <?= "background-color: $status_color;" ?>'><?=$status?></td>
                        <td style='text-align:center;'><?= $gameResult ?></td>
                        <td>
                            <a href="javascript:fn_cancel(<?=$bet_idx?>);" class="btn h25 btn_blu">취소</a>
                        </td>
                        <?php /*
                        <!--<td style='text-align:right'><?=number_format($row -> money)?></td>
                        <td style='<?=$str_background?>'><?=$row ->create_dt?></td>-->
                        */ ?>
                        <?php
                        
                        // echo '<pre>';
                        // var_dump($row);
                        // echo '</pre>';
                        
                        ?>
                        </tr>
        <?php        
            $i++;
        }
    }
    
}
else {
    echo "<tr><td colspan='12'>데이터가 없습니다.</tr>";
}
?>

                </table>
<?php 
$requri = explode('?',$_SERVER['REQUEST_URI']);
$reqFile = basename($requri[0]);
$default_link = "$reqFile?srch_key=".$p_data['srch_key']."&srch_val=".$p_data['srch_val']."";
$default_link .= "&srch_s_date=".$p_data['srch_s_date']."&srch_e_date=".$p_data['srch_e_date']."&vtype=".$p_data['vtype']."&league=".$p_data['league']." ";

include_once(_BASEPATH.'/common/page_num.php');
?>                
            </div>
        </div>
        <!-- END list -->
    </div>
    <!-- END Contents -->
</div>
<?php 
include_once(_BASEPATH.'/common/bottom.php');

function gameResult($type, $result) {
    $result = json_decode($result);
    //print_r($result);
    $gameResult = "";
    switch($type) {
        case 'powerball':
            if(!empty($result->num1)){
                if (0 !== $result->pb % 2) {
                    $oddEven = '[홀]';
                }else if (0 === $result->pb % 2) {
                    $oddEven = '[짝]';
                }

                if (5 <= $result->pb && $result->pb <= 9) {
                    $overUnder = '[오버]';
                }else if (0 <= $result->pb && $result->pb <= 4) {
                    $overUnder = '[언더]';
                }

                $sum = $result->num1 + $result->num2 + $result->num3 + $result->num4 + $result->num5;
                if (81 <= $sum && $sum <= 130) {
                    $sumCal = '[대]';
                }else if (65 <= $sum && $sum <= 80) {
                    $sumCal = '[중]';
                }else{
                    $sumCal = '[소]';
                }
                $gameResult = "$oddEven, $overUnder,$sumCal";
            }
            break;
        case 'kladder':
            if(!empty($result -> oe)) {
                $oe = GameStatusUtil::get_minigame_result_name($result->oe);
                $start = GameStatusUtil::get_minigame_result_name($result->start);
                $gameResult = "[$start],[$result->line],[$oe]";
            }
            break;
        case 'pladder':
            if(!empty($result -> oe)) {
                $oe = GameStatusUtil::get_minigame_result_name($result->oe);
                $start = GameStatusUtil::get_minigame_result_name($result->start);
                $gameResult = "[$start],[$result->line],[$oe]";
            }
            break;
        case 'b_soccer':
            //$type = GameStatusUtil::get_minigame_result_name($result->type);
            $res = GameStatusUtil::get_minigame_result_name($result->res);
            $gameResult = $res;
            break;
        default:
            break;
    }
    
    return $gameResult;
}
?> 
</body>
<script>
// 취소
function fn_cancel(bet_idx) {
    var str_msg = '취소하시겠습니까?';
    var result = confirm(str_msg);
    if (result){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/mini_game_w/_mini_game_bet_cancel.php',
            data:{'bet_idx':bet_idx},
            success: function (result) {
                console.log(result);
                    if(result['retCode'] == "1000"){
                            alert('취소하였습니다.');
                            window.location.reload();
                            return;
                        }else{
                            alert(result['retMsg']);
                            return;
                        }
                    },
                    error: function (request, status, error) {
                        alert('취소에 실패하였습니다.');
                        window.location.reload();
                    return;
            }
        });
    }
    else {
            return;
    }
}

function setDate(sdate, edate) {
	var fm = document.search;

	fm.srch_s_date.value = sdate;
	fm.srch_e_date.value = edate;
}

function goSearch(vtype=null) {
	var fm = document.search;

	fm.vtype.value=vtype;
	
	//if((fm.srch_key.value!='') && (fm.srch_val.value=='')) {

		//if (fm.srch_level.value < 1) {
    		//alert('검색어를 입력해 주세요.');
    		//fm.srch_val.focus();
    		//return;
		//}
	//}

	fm.method="get";
	fm.submit();	
}

function allCheckFunc(obj) {
    $("[name=chk]").prop("checked", $(obj).prop("checked"));
}

const openNotePop = function () {

	const memberIdxList = [];
	$('input[name="chk"]:checked').each(function(){
        memberIdxList.push($(this).data('member-idx'));
    });

	if(memberIdxList.length == 0) {
		alert("항목이 선택되지 않았습니다. 쪽지 보낼 회원을 선택해주세요.");
		return false;
	}

	popupWinPostList('/member_w/pop_msg_write_list.php', 'popmsg', 660, 1000, 'msg', memberIdxList);
}
</script>
</html>
