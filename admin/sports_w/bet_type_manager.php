<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');

include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

$UTIL = new CommonUtil();

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end


$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$bet_type = trim(isset($_REQUEST['bet_type']) ? $_REQUEST['bet_type'] : 1);
$sports_list = isset($_REQUEST['sports_list']) ? $_REQUEST['sports_list'] : 0;
$isClassic = trim(isset($_REQUEST['isClassic']) ? $_REQUEST['isClassic'] : 'OFF');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if ($db_conn) {
  
    $bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    $sports_list = $LSportsAdminDAO->real_escape_string($sports_list);
   
    
    $p_data["table_name"] = " lsports_bet ";
    $p_data["sql_where"] = "";
    $p_data["sql_order_by"] = " ORDER BY id asc";
    $default_link = 'bet_type_manager.php?bet_type='.$bet_type;

    // 배당사 리스트
    $bookmakers = array();
    $p_data['sql'] = "SELECT id, name FROM lsports_bookmaker where is_use = 1;";
    $result = $LSportsAdminDAO->getQueryData($p_data);
    foreach ($result as $key => $value) {
        $bookmakers[$value['id']] = $value['name'];
    }
    
    // 스포츠 리스트
    $sp = $LSportsAdminDAO->getSportsList(2);
    
    // 프리매치, 실시간 목록 조회
    $p_data['sql'] = "SELECT count(*) as CNT FROM lsports_markets where is_delete = 0 and bet_group = $bet_type";
    if($sports_list > 0){
        $p_data['sql'] .= " and sport_id = $sports_list";
    }
    
    // 클래식이다.
    if('ON' == $isClassic){
        $p_data['sql'] .= " and lsports_markets.id in (1,2,3,28,52,226,342)";
    }
    
    $p_data['sql'] .= ";";
    $db_total_cnt = $LSportsAdminDAO->getQueryData($p_data);

    $total_cnt = $db_total_cnt[0]['CNT'];

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    if ($total_cnt > 0) {
        $start = ( $p_data['page'] - 1 ) * $p_data['num_per_page'];

        $p_data['sql'] = "SELECT lsports_markets.*,lsports_sports.name as sp_name FROM lsports_markets
                LEFT JOIN lsports_sports ON lsports_markets.sport_id = lsports_sports.id
                where lsports_markets.is_delete = 0 and lsports_markets.bet_group = $bet_type and lsports_sports.bet_type = $bet_type";
        if($sports_list > 0){
            $p_data['sql'] .= " and sport_id = $sports_list";
            $default_link .= "&sports_list=$sports_list";
        }
        
        // 클래식이다.
        if('ON' == $isClassic){
            $p_data['sql'] .= " and lsports_markets.id in (1,2,3,28,52,226,342)";
            $default_link .= "&isClassic=ON";
        }
    
        $p_data['sql'] .= " LIMIT $start, " . $p_data['num_per_page'] ." ";
        $p_data['sql'] .= ";";
        
        //CommonUtil::logWrite("menu_4 : " . $p_data['sql'], "info");
        $db_dataArr = $LSportsAdminDAO->getQueryData($p_data);
    }

    $LSportsAdminDAO->dbclose();
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
            $menu_name = "bet_type_manager";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>배팅 타입 관리</h4>
                    </a>
                </div>

                <!-- list -->
                <div class="panel reserve">

                    <div class="panel_tit">
                        <div class="search_form fl">
                            <div class="" style="padding-right: 10px;">
                                <?php if($bet_type == 1 && 'OFF' == $isClassic){ ?>
                                    <h5><a href="bet_type_manager.php?&bet_type=1"><b>스포츠</b></a> | </h5>
                                    <h5><a href="bet_type_manager.php?&bet_type=2">실시간</a> | </h5>
                                    <h5><a href="bet_type_manager.php?&bet_type=1&isClassic=ON">클래식</a></h5>
                                <?php }else if($bet_type == 1 && 'ON' == $isClassic){ ?>
                                    <h5><a href="bet_type_manager.php?&bet_type=1">스포츠</a> | </h5>
                                    <h5><a href="bet_type_manager.php?&bet_type=2">실시간</a> | </h5>
                                    <h5><a href="bet_type_manager.php?&bet_type=1&isClassic=ON"><b>클래식</b></a></h5>
                                <?php }else{ ?>
                                    <h5><a href="bet_type_manager.php?&bet_type=1">스포츠</a> | </h5>
                                    <h5><a href="bet_type_manager.php?&bet_type=2"><b>실시간</b></a> | </h5>
                                    <h5><a href="bet_type_manager.php?&bet_type=1&isClassic=ON">클래식</a></h5>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="search_form fr">
                            <div>
                                <select id="resetBetType">
                                    <option value="1">스포츠</option>
                                    <option value="2">실시간</option>
                                    <!--<option value="3">클래식</option>-->
                                </select>
                            </div>
                            <div>
                                <a href="#" class="btn h30 btn_blu" onClick="fnResetBetPrice(0, '<?=INITDATA_PRE_URL?>','<?=INITDATA_REAL_URL?>');">배당 리플래쉬</a>
                            </div>
                        </div>
                        <div class="search_form fr">
                            <div>
                                <span>종목</span>
                                <select name="sports_list" id="sports_list" style="display: inline-block;">
                                    <option value="0">전체</option>
                                    <?php foreach ($sp as $key => $item) { ?>
                                        <option value="<?= $item['id'] ?>"   <?php if ($sports_list == $item['id']): ?> selected<?php endif; ?>><?= $item['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div style="margin-right: 30px;"><a href="#" class="btn h30 btn_blu search_btn">검색</a></div>
                            <?php if($bet_type == 2){ ?>
                            <div>
                                메인배베당
                                <select name="main_book_maker" id="main_book_maker" style="display: inline-block;">
                                    <?php foreach ($bookmakers as $key => $item) { ?>
                                        <option value="<?= $key ?>"><?= $item ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div>
                                서브배당
                                <select name="sub_book_maker" id="sub_book_maker" style="display: inline-block;">
                                    <?php foreach ($bookmakers as $key => $item) { ?>
                                        <option value="<?= $key ?>"><?= $item ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div><a href="#" class="btn h30 btn_blu book_maker_apply">적용</a></div>
                            <?php } ?>
                        </div>
                    </div>


                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>타입ID</th>
                                <th>그룹</th>
                                <th>스포츠타입</th>
                                <th>타입명</th>
                                <th>타입명(Front)</th>
                                <?php if(2 == $bet_type){ ?>
                                    <th>메인배당</th>
                                    <th>서브배당</th>
                                <?php } ?>
                                <!--<th>정산 방식(피리어드 타입)</th>
                                <th>배당 상한(기준값 이상 노출)</th>-->
                                <th>최소 배당률</th>
                                <th>최대 배당률</th>
                                <th>수정 / 삭제</th>
                            </tr>
<?php foreach ($db_dataArr as $key => $rows) { ?>
                                <?php $db_m_idx = $rows['idx']; ?>
                                <tr>
                                    <td><?= $rows['id'] ?></td>
    <?php
    $gid = $rows['bet_group'];
    $td_id = 'td_gid_' . $db_m_idx;
    if ($gid == 0) {
        echo '<td id="' . $td_id . '" name="' . $td_id . '">기타</td>';
    } else if ($gid == 1 && 'OFF' == $isClassic) {
        echo '<td id="' . $td_id . '" name="' . $td_id . '">스포츠</td>';
    } else if ($gid == 1 && 'ON' == $isClassic) {
        echo '<td id="' . $td_id . '" name="' . $td_id . '">클래식</td>';
    } else if ($gid == 2) {
        echo '<td id="' . $td_id . '" name="' . $td_id . '">실시간</td>';
    } else {
        echo "<td>-</td>";
    }
    
    $sportName = $rows['sp_name'];
   
    $limit_color = '';
    $max_color = '';
    $refund_rate_color = '';
    if($rows['limit_bet_price'] > 0)
        $limit_color = 'red';
    if($rows['max_bet_price'] > 0)
        $max_color = 'red';
    if(true === isset($rows['input_refund_rate']) && false === empty($rows['input_refund_rate']) ? $rows['input_refund_rate'] : null != 0)
        $refund_rate_color = 'red';
    ?>
                                    <td><?= $sportName ?></td>
                                    <td><?= $rows['name'] ?></td>
                                    <td><input id="name_<?= $rows['idx'] ?>" type="text" class="" style="width: 100%" placeholder="" value="<?= $rows['display_name'] ?>"/></td>
                                    <?php if(2 == $rows['bet_group']){ ?>
                                        <td><?= $bookmakers[$rows['main_book_maker']] ?></td>
                                        <td><?= $bookmakers[$rows['sub_book_maker']] ?></td>
                                    <?php } ?>
                                    <td><input type="text" id="limit_bet_price_<?= $rows['idx'] ?>" name="limit_bet_price_<?= $rows['idx'] ?>" style="width: 100%; color:<?=$limit_color?>" value="<?= $rows['limit_bet_price'] ?>" onkeyup="javascript:fnCheckInput(this);" /></td>
                                    <td><input type="text" id="max_bet_price_<?= $rows['idx'] ?>" name="max_bet_price_<?= $rows['idx'] ?>" style="width: 100%; color:<?=$max_color?>" value="<?= $rows['max_bet_price'] ?>" onkeyup="javascript:fnCheckInput(this);" /></td>
                                    <td><a href="javascript:fn_update_menu4(<?= $db_m_idx ?>)" class="btn h30 btn_blu">수정</a> <a href="javascript:fn_del_menu4(<?= $db_m_idx ?>)" class="btn h30 btn_blu">삭제</a></td>
                                </tr>
<?php } ?>
                        </table>
                            <?php
                            include_once(_BASEPATH . '/common/page_num.php');
                            ?>
                    </div>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                /*$('#sport_list').on('change', function () {
                    let select_id = $('#sport_list').val();
                    $('.league_list').attr('style', 'display: none;');
                    $('.select_option_' + select_id).removeAttr('style');
                });*/

                $('.search_btn').on('click', function () {
                    let bet_type = <?=$bet_type?>;
                    let sports_list = $('#sports_list').val();
                    let isClassic = '<?=$isClassic?>';

                    //alert(bet_type + ' ' + sport_list);

                    location.href = '/sports_w/bet_type_manager.php?bet_type=' + bet_type + '&sports_list=' + sports_list + '&isClassic=' + isClassic;
                });
                
                $('.book_maker_apply').on('click', function () {
                    let bet_type = <?=$bet_type?>;
                    let sports_list = $('#sports_list').val();
                    let main_book_maker = $('#main_book_maker').val();
                    let sub_book_maker = $('#sub_book_maker').val();
                    
                    if(sports_list == 0){
                        alert('종목을 선택해주세요.');
                        return;
                    }

                    console.log(bet_type + ' ' + sports_list + ' ' + main_book_maker + ' ' + sub_book_maker);
                    
                    let str_msg = '배당을 수정하시겠습니까?';
                    let result = confirm(str_msg);
                    if (result) {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: '/sports_w/_bet_type_manager_bookmaker_prc_update.php',
                            data: {'bet_type': bet_type, 'sports_list': sports_list, 'main_book_maker': main_book_maker, 'sub_book_maker': sub_book_maker},
                            success: function (result) {
                                if (result['retCode'] == "1000") {
                                    alert('등록하였습니다.');
                                    location.href = '/sports_w/bet_type_manager.php?bet_type=' + bet_type + '&sports_list=' + sports_list;
                                    //window.location.reload();
                                    return;
                                } else {
                                    alert(result['retMsg']);
                                    return;
                                }
                            },
                            error: function (request, status, error) {
                                alert('등록에 실패하였습니다.');
                                return;
                            }
                        });
                    }
                });
            })

            let prevInput = '';
            function fnCheckInput(o) {
                if (o.value.search(/^\d*(\.\d{0,2})?$/) == -1) {
                    o.value = prevInput;
                } else {
                    prevInput = o.value;
                }
            }

            function fn_insert_menu4() {
                var id = $('#id').val();
                var name = $('#name').val();
                //var sport = $("#sport_list option:selected").val();
                var isUse = $("#is_use option:selected").val();

                var str_msg = '종목을 등록하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_bet_type_manager_prc_insert.php',
                        data: {'id': id, 'name': name, 'isUse': isUse},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('등록하였습니다.');
                                window.location.reload();
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('등록에 실패하였습니다.');
                            return;
                        }
                    });
                } else {
                    return;
                }
            }

            function fn_del_menu4(idx) {
                var bet_group_id = fnGetBetGroupId(idx);
                var str_msg = '종목을 삭제하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_bet_type_manager_prc_del.php',
                        data: {'idx': idx, 'gid': bet_group_id},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('삭제하였습니다.');
                                window.location.reload();
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('삭제에 실패하였습니다.');
                            return;
                        }
                    });
                } else {
                    return;
                }
            }

            function fnGetBetGroupId(idx) {
                var bet_group_td = $('#td_gid_' + idx).text();
                if (bet_group_td == '기타') {
                    return 0;
                } else if (bet_group_td == '스포츠') {
                    return 1;
                } else if (bet_group_td == '클래식') {
                    return 1;
                } else if (bet_group_td == '실시간') {
                    return 2;
                } else {
                    return -1;
                }
            }

            function fn_update_menu4(idx) {
                let name = $('#name_' + idx).val();
                let bet_group_id = fnGetBetGroupId(idx);
                let limitBetPrice = $('#limit_bet_price_' + idx).val();
                let maxBetPrice = $('#max_bet_price_' + idx).val();
                //let input_refund_rate = $('#input_refund_rate_' + idx).val();
                
                let str_msg = '수정 하시겠습니까?';
                let result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_bet_type_manager_prc_update.php',
                        data: {'idx': idx, 'gid': bet_group_id, 'name': name, 'limit_bet_price': limitBetPrice, 'max_bet_price': maxBetPrice},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('수정하였습니다.');
                                window.location.reload();
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('수정에 실패하였습니다.');
                            return;
                        }
                    });
                } else {
                    return;
                }
            }
        </script>
<?php
include_once(_BASEPATH . '/common/bottom.php');
?>
    </body>
</html>
