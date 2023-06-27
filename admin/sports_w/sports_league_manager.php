<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');

include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}


$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 50;
}

$bet_type = trim(isset($_REQUEST['bet_type']) ? $_REQUEST['bet_type'] : 1);

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

if ($db_conn) {

    $bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    $p_data['page'] = $LSportsAdminDAO->real_escape_string($p_data['page']);
    $p_data['num_per_page'] = $LSportsAdminDAO->real_escape_string($p_data['num_per_page']);
    
    $p_data["table_name"] = " lsports_bet ";
    $p_data["sql_where"] = "";
    $default_link = 'sports_league_manager.php?';
    $status_link = 'sports_league_manager.php?';

    $sports_list = 0;
    $locations_list = 0;
    $src_key = 0;
    $src_value = "";

    if (isset($bet_type)) {
        $p_data["sql_where"] = " and bet_type = $bet_type ";
        $default_link .= "bet_type=$bet_type";
    }
    
    if (isset($_REQUEST['sports_list'])) {
        $sports_list = $_REQUEST['sports_list'];
        if ($sports_list > 0) {
            $p_data["sql_where"] .= " and sport_id = $sports_list ";
            $default_link .= "&sports_list=$sports_list";
            $status_link .= "&sports_list=$sports_list";
        }
    }

    if (isset($_REQUEST['locations_list'])) {
        $locations_list = $_REQUEST['locations_list'];

        if (strlen($locations_list) > 0 && $locations_list > 0) {
            $p_data["sql_where"] .= " and location_id = $locations_list ";
            $default_link .= "&locations_list=$locations_list";
            $status_link .= "&locations_list=$locations_list";
        }
    }

    if (isset($_REQUEST['is_use'])) {
        $is_use = $_REQUEST['is_use'];

        //if (strlen($locations_list) > 0 && $locations_list > 0) {
            $p_data["sql_where"] .= " and lsports_leagues.is_use = $is_use ";
            $default_link .= "&is_use=$is_use";
        //}
    }else{
        $is_use = 1;
        $p_data["sql_where"] .= " and lsports_leagues.is_use = 1 ";
        $default_link .= "&is_use=1";
    }

    if (isset($_REQUEST['src_value'])) {
        $src_value = $_REQUEST['src_value'];
        if (strlen($src_value) > 0) {
            $p_data["sql_where"] .= " and (lsports_leagues.name like '%$src_value%' or lsports_leagues.display_name like '%$src_value%') ";
            $default_link .= "&src_value=$src_value";
            $status_link .= "&src_value=$src_value";
        }
    }
    
    if (isset($_REQUEST['dividend_rank'])) {
        $dividend_rank = $_REQUEST['dividend_rank'];

        if ($dividend_rank > 0) {
            $p_data["sql_where"] .= " and dividend_rank = $dividend_rank ";
            $default_link .= "&dividend_rank=$dividend_rank";
            $status_link .= "&dividend_rank=$dividend_rank";
        }
    }

    $p_data['sql'] = "SELECT count(*) as CNT FROM lsports_leagues";
    if (strlen($p_data["sql_where"]) > 0) {
        $p_data['sql'] .= ' where id > 0 ' . $p_data['sql_where'];
    }

    $db_total_cnt = $LSportsAdminDAO->getQueryData($p_data);
    $total_cnt = $db_total_cnt[0]['CNT'];

    // 등록완료, 대기, 미사용 카운트
    $p_data['sql'] = "SELECT is_use, count(*) as CNT FROM lsports_leagues";
    if (strlen($p_data["sql_where"]) > 0) {
        $p_data['sql'] .= ' where id > 0 ';
    }
    $p_data['sql'] = $p_data['sql'] . ' group by is_use';

    $useCount = $waitCount = $notUseCount = 0;
    $result = $LSportsAdminDAO->getQueryData($p_data);
    foreach ($result as $key => $value) {
        if (1 == $value['is_use'])
            $useCount = $value['CNT'];
        else if (0 == $value['is_use'])
            $notUseCount = $value['CNT'];
        else
            $waitCount = $value['CNT'];
    }

    $p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

    $total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

    if ($block >= $total_block)
        $last_page = $total_page;

    $sport_list = null;
    $result = $LSportsAdminDAO->getSportsList();
    foreach ($result as $key => $value) {
        $sport_list[] = $value['id'];
    }
    
    if ($total_cnt > 0) {
        $start = ( $p_data['page'] - 1 ) * $p_data['num_per_page'];

        $p_data['sql'] = "SELECT lsports_leagues.id, lsports_leagues.name as name, lsports_locations.name_en AS country_name, lsports_leagues.display_name, location_id, sport_id, lsports_leagues.is_use, lsports_leagues.image_path, "
                        . "input_refund_rate, dividend_rank, bet_type";
        $p_data['sql'] .= " FROM lsports_leagues";
        $p_data['sql'] .= " left join lsports_locations on lsports_leagues.location_id = lsports_locations.id";
        if (strlen($p_data["sql_where"]) > 0) {
            if ($sports_list > 0) {
                $p_data['sql'] .= ' where lsports_leagues.id > 0 '.$p_data['sql_where'];
            }else{
                $p_data['sql'] .= ' where lsports_leagues.id > 0 and sport_id in ('.implode(',', $sport_list).')' . $p_data['sql_where'];
            }
        }
        $p_data['sql'] .= " ORDER BY lsports_locations.name ASC, lsports_leagues.id ASC LIMIT $start, " . $p_data['num_per_page'] . ";";
        $db_dataArr = $LSportsAdminDAO->getQueryData($p_data);
    }

    $s = $LSportsAdminDAO->getLocationsList();
    //$sp = $LSportsAdminDAO->getSportsList();
    $sp = $result;
    $dividendPolicy = $LSportsAdminDAO->getDividendPolicy();
    $LSportsAdminDAO->dbclose();

    // 종목, 지역을 id를 키로해서 배열생성
    $arrSports = null;
    $arrLocations = null;
    foreach ($sp as $key => $value) {
        $arrSports[$value['id']] = $value;
    }

    foreach ($s as $key => $value) {
        $arrLocations[$value['id']] = $value;
    }

    $arrDividendPolicy = null;
    foreach ($dividendPolicy as $k => $v) {
        $arrDividendPolicy[$v['rank']][$v['type']] = array(
           
            'amount' => $v['amount'],
            'create_dt' => $v['create_dt'],
            'update_dt' => $v['update_dt']
             
        );
    }

    $UTIL->logWrite(json_encode($arrDividendPolicy),"info");
    
    //$UTIL->logWrite($p_data['sql'] ,"error");
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
            $menu_name = "sports_league_manager";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <?php //print_r($arrLocations); ?>
                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>리그 관리</h4>
                    </a>
                </div>

                <!-- detail search -->
                <div class="panel search_box">
                    <iframe id="iframe1" name="iframe1" style="display:none"></iframe>
                    <table class="mlist mline">
                        <tr>
                            <th>타입</th>
                            <th><select name="bet_type" id="bet_type" style="width: 100%">
                                    <option value="1">스포츠</option>
                                    <option value="2">실시간</option>
                                </select></th>
                            <th>종목</th>
                            <th><select name="sports_list" id="sports_list" style="width: 100%">
                                    <option value="0">전체</option>
                                    <?php foreach ($sp as $key => $item) { ?>
                                        <option value="<?= $item['id'] ?>"   <?php if ($sports_list == $item['id']): ?> selected<?php endif; ?>><?= $item['name'] ?></option>
                                    <?php } ?>
                                </select></th>
                            <th>지역</th>
                            <th><select name="locations_list" id="locations_list" style="width: 100%">
                                    <option value="0">전체</option>
                                    <?php foreach ($s as $key => $item) { ?>
                                        <option value="<?= $item['id'] ?>"   <?php if ($locations_list == $item['id']): ?> selected<?php endif; ?>><?= $item['name'] ?></option>
                                    <?php } ?>
                                </select></th>
                            <th>리그명</th>
                            <?php $src_value = !empty($_REQUEST['src_value']) ? $_REQUEST['src_value'] : ''?>
                            <th><input id="src_value" name="src_value" type="text" class="" style="width: 100%" placeholder="" value="<?= $src_value ?>"/></th>
                            <th>배당등급</th>
                            <th><select id="dividend_rank" name="dividend_rank" style="width: 100%">
                                    <option value="0" <?php
                                    $dividend_rank = true === isset($dividend_rank) && false === empty($dividend_rank) ? $dividend_rank : [];
                                    if (0 == $dividend_rank) {
                                        echo "selected";
                                    }
                                    ?>>전체</option>
                                <?php foreach ($arrDividendPolicy as $k => $v) { ?>
                                    <option value="<?= $k ?>" <?php
                                    $dividend_rank = true === isset($dividend_rank) && false === empty($dividend_rank) ? $dividend_rank : [];
                                    if ($k == $dividend_rank) {
                                        echo "selected";
                                    }
                                    ?>><?= $k ?>등급</option>
                                <?php } ?>
                            </select></th>
                            <th><th><a href="#" class="btn h30 btn_blu search_btn">검색</a></th></th>
                        </tr>
                    </table>
                    <br/>
                    <br/>
                    <div class="panel_tit">
                        <div class="search_form fl">
                            <?php if($is_use == 1){ ?>
                                <h5><a href="<?= $status_link ?>"><b>등록완료 (<?= $useCount ?>)</b></a> | </h5>
                                <h5><a href="<?= $status_link . '&is_use=2' ?>">대기 (<?= $waitCount ?>)</a> | </h5>
                                <h5><a href="<?= $status_link . '&is_use=0' ?>">미사용 (<?= $notUseCount ?>)</a></h5>
                            <?php }else if($is_use == 2){ ?>
                                <h5><a href="<?= $status_link . '&is_use=1' ?>">등록완료 (<?= $useCount ?>)</a> | </h5>
                                <h5><a href="<?= $status_link . '&is_use=2' ?>"><b>대기 (<?= $waitCount ?>)</b></a> | </h5>
                                <h5><a href="<?= $status_link . '&is_use=0' ?>">미사용 (<?= $notUseCount ?>)</a></h5>
                            <?php }else{ ?>
                                <h5><a href="<?= $status_link . '&is_use=1' ?>">등록완료 (<?= $useCount ?>)</a> | </h5>
                                <h5><a href="<?= $status_link . '&is_use=2' ?>">대기 (<?= $waitCount ?>)</a> | </h5>
                                <h5><a href="<?= $status_link . '&is_use=0' ?>"><b>미사용 (<?= $notUseCount ?>)</b></a></h5>
                            <?php } ?>
                        </div>
                        
                        <div class="search_form fr">
                            <div>
                                <select id="resetBetType">
                                    <option value="1">스포츠</option>
                                    <option value="2">실시간</option>
                                </select>
                            </div>
                            <div>
                                <a href="#" class="btn h30 btn_blu" onClick="fnResetBetPrice(0, '<?=INITDATA_PRE_URL?>','<?=INITDATA_REAL_URL?>');">배당 리플래쉬</a>
                            </div>
                        </div>
                    </div>

                <!-- list -->
                <div class="panel reserve">
                    <div class="panel_tit">
                        <span style="color:red">리그 일괄 수정은 리그명(FRONT)가 동일한 경우 해당 리그 이미지 모두 변경됩니다.</span><br>
                        <span style="color:red">리그 일괄 수정을 원하실 경우 리그명(FRONT)부터 정리하시고 이미지 등록해주세요.</span><br>
                        <div class="search_form fl">
                            <?php if($is_use != 0){ ?>
                            <a href="#" class="btn h30 btn_blu" onClick="setIsUse(0, <?= $sports_list ?>, <?= $locations_list ?>, <?= $bet_type ?>);">미사용</a>
                            <?php } ?>
                            <?php if($is_use != 1){ ?>
                            <a href="#" class="btn h30 btn_blu" onClick="setIsUse(1, <?= $sports_list ?>, <?= $locations_list ?>, <?= $bet_type ?>);">등록완료(사용)</a>
                            <?php } ?>
                            <?php if($is_use != 2){ ?>
                            <a href="#" class="btn h30 btn_blu" onClick="setIsUse(2, <?= $sports_list ?>, <?= $locations_list ?>, <?= $bet_type ?>);">대기</a>
                            <?php } ?>
                            <a href="#" class="btn h30 btn_red" onClick="fnClickManagePolicy();">배당금액 관리</a>
                        </div>
                        <div class="search_form fr">
                            <!--<form id="thumbnail_fm" class="file_thumb_section preview_area" method="post" action="../common/image_send.php" enctype="multipart/form-data" target="iframe1">
                                <div class="image_container w30"></div>
                                <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/league'>
                                <input type="file" onchange="setThumbnail(event);" id="uploadfile" name="uploadfile" accept="image/*">
                            </form>
                            <a href="javascript:fn_btn_league_send()" id="adm_btn_league_send" class="btn h30 btn_green" style="color: white">이미지 등록</a>-->
                            <a href="#" class="btn h30 btn_blu" onClick="fn_all_update_menu5(<?=$bet_type?>)">전체 수정</a>
                        </div>
                        <input type="hidden" id="is_use" value="<?=$is_use?>">
                    </div>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>
                                    <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                        <input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" />
                                        <label for="checkbox_css_all"></label>
                                    </div>
                                </th>
                                <!--<th>번호</th>-->
                                <th>리그ID</th>
                                <th>타입</th>
                                <th>종목</th>
                                <th>지역</th>
                                <th>국가 이미지</th>
                                <th>리그 이미지 변경</th>
                                <th>현재 이미지</th>
                                <th>리그명(Data)</th>
                                <th>리그명(Front)</th>
                                <th>등급</th>
                                <th>상태</th>
                                <!--<th>현재 환수율</th>
                                <th>수정 환수율</th>-->
                                <th>이미지 수정</th>
                                <th>수정</th>
                            </tr>
                            <tbody id="leagues_tbody">
                                <?php
                                $i = 0;
                                if ($total_cnt > 0) {
                                    ?>
                                    <?php foreach ($db_dataArr as $key => $rows) { ?>
                                        <?php
                                        //$db_m_idx = $rows['idx'];
                                        $db_m_idx = $rows['id'];
                                        $db_m_bet_type = $rows['bet_type'];
                                        $chkbox_css[$i] = "checkbox_css_" . $i;
                                        
                                        $league_image_path = '../assets_admin/images/flag/' . $rows['location_id'] . '.png';
                                        if ($rows['image_path'] != null || $rows['image_path'] != '') {
                                            $league_image_path = IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/league/'.$rows['image_path'];
                                        }
                                        $imageLeagueBasePath = IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/league/';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;" >
                                                    <input type="checkbox" name="chk" id="<?= $chkbox_css[$i] ?>" value="<?= $rows['id'] ?>" />
                                                    <label for="<?= $chkbox_css[$i] ?>"></label>
                                                </div>
                                            </td>
                                            <td><?= $rows['id'] ?></td>
                                            <td><?=$rows['bet_type'] == 1 ? '스포츠':'실시간'?></td>
                                            <td><?= !empty($arrSports[$rows['sport_id']]) ? $arrSports[$rows['sport_id']]['name'] : "N/A" ?></td>
                                            <td><?= !empty($arrLocations[$rows['location_id']]) ? $arrLocations[$rows['location_id']]['name'] : "N/A" ?></td>
                                            <td><img src="<?= IMAGE_SERVER_URL.'/'.IMAGE_PATH.'/flag/' . strtolower($rows['country_name']) . '.png'?>"  onerror="this.style.display='none'"></td>
                                            <td class="file_thumb_section">
                                                <input id="update_name_<?=$rows['id']?>" type=hidden value=''>
                                                <form id="thumbnail_fm_<?=$rows['id']?>" method="post" action="../common/image_send.php" enctype="multipart/form-data" target="iframe1">
                                                    <div class="image_container_<?=$rows['id']?> w128"></div>
                                                    <input name="savePath" type=hidden value='/<?=IMAGE_PATH?>/league'>
                                                    <input type="file" onchange="setThumbnail(event, <?=$rows['id']?>);" id="uploadfile" style="width: 100%" name="uploadfile" accept="image/*">
                                                </form>
                                            </td>
                                            <td><img src="<?=$league_image_path?>" onerror="this.style.display='none'"></td>
                                            <td><?= !empty($rows['name']) ? $rows['name'] : "N/A" ?></td>
                                            <td><input id="name_<?= $rows['id'] ?>" type="text" class="" style="width: 100%" placeholder="" value="<?= !empty($rows['display_name']) ? $rows['display_name'] : "N/A" ?>"/></td>
                                            <td style="width:75px;" >
                                                <select id="policy_<?= $rows['id'] ?>" name="policy_<?= $rows['id'] ?>" style="width: 100%">
                                                    <?php foreach ($arrDividendPolicy as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?php
                                                        $dividend_rank = true === isset($dividend_rank) && false === empty($dividend_rank) ? $dividend_rank : [];
                                                        if ($k == $rows['dividend_rank']) {
                                                            echo "selected";
                                                        }
                                                        ?>><?= $k ?>등급 </option>
                                                            <?php } ?>
                                                </select>
                                            </td>
                                            <td style="width:75px;">
                                                <select name="is_use_<?= $rows['id'] ?>" id="is_use_<?= $rows['id'] ?>" style="width: 100%">
                                                        <option value="0"   <?php if (0 == $rows['is_use']): ?> selected<?php endif; ?>>미사용</option>
                                                        <option value="1"   <?php if (1 == $rows['is_use']): ?> selected<?php endif; ?>>사용</option>
                                                        <option value="2"   <?php if (2 == $rows['is_use']): ?> selected<?php endif; ?>>대기</option>
                                                </select>
                                            </td>
                                            <?php
                                                $color = '';
                                                if($rows['input_refund_rate'] > 0)
                                                    $color = 'red';
                                            ?>
                                            <!--<td><input id="input_refund_rate_current_<?=$rows['id']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=$rows['input_refund_rate']?>" readonly/></td>
                                            <td><input id="input_refund_rate_<?=$rows['id']?>" type="number" class="" style="width: 100%; color:<?=$color?>" placeholder="" value="<?=$rows['input_refund_rate']?>"/></td>-->
                                            <td><a href="javascript:adm_btn_league_update(<?= $rows['id'] ?>,'<?= $rows['display_name'] ?>')" class="btn h30 btn_green" style="color: white">리그 일괄 수정</a></td>
                                            <td><a href="javascript:fn_update_menu5(<?= $db_m_idx ?>, <?= $is_use ?>, <?= $db_m_bet_type ?>)" class="btn h30 btn_blu">수정</a></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
<?php } else { ?>
                                    <tr>
                                        <td colspan='11'>데이터가 없습니다.</td>
                                    </tr>
<?php } ?>
                            </tbody>
                        </table>
                        <?php
                        include_once(_BASEPATH . '/common/page_num.php');
                        ?>
                    </div>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->

            <div id="manage_policy" name="manage_policy" class="pop-window">
                <div class="con_wrap">
                    <div class="panel reserve">
                        <div class="title">
                            배당금액 관리
                        </div>
                        <div class ="tline">
                            <table id="popup_list" name="popup_list" class="mlist">
                                <tr>
                                    <th>등급</th>
                                    <th>금액</th>
                                    <th>생성일</th>
                                    <th>수정일</th>
                                    <th>수정</th>
                                </tr>

<?php foreach ($arrDividendPolicy as $k => $v) { 
    $v['amount'] = true === isset($v['amount']) && false === empty($v['amount']) ? $v['amount'] : 0;
    $v['create_dt'] = true === isset($v['create_dt']) && false === empty($v['create_dt']) ? $v['create_dt'] : '';
    $v['update_dt'] = true === isset($v['update_dt']) && false === empty($v['update_dt']) ? $v['update_dt'] : '';
    ?>
                                <tr>
                                    <td><?= $k ?></td>
                                    <td><input type="text" id="p_amount_<?= $k ?>" name="p_amount_<?= $k ?>" value="<?= $v['amount'] ?>" style="width: 100%" /></td>
                                    <td><?= $v['create_dt'] ?></td>
                                    <td><?= $v['update_dt'] ?></td>
                                    <td><a href="javascript:fnUpdateDividendPolicy(<?= $k ?>);" class="btn h30 btn_blu">수정</a></td>
                                </tr>
<?php } ?>
                            </table>
                        </div>

                        <div class="panel_tit">
                            <div class="search_form fr">
                                <!-- <a href="#" class="btn h30 btn_red" onclick="javascript:fnPopupAdd();">추가</a> -->
                                <a href="#" class="btn h30 btn_blu" onclick="javascript:fnPopupClose();">닫기</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            let image_check = 0;
            $(document).ready(function () {
                $('#bet_type').val(<?=$bet_type?>).prop("selected",true);
                
                $('#sport_list').on('change', function () {
                    let select_id = $('#sport_list').val();
                    $('.league_list').attr('style', 'display: none;');
                    $('.select_option_' + select_id).removeAttr('style');
                });

                $('.search_btn').on('click', function () {
                    let sports_list = $('#sports_list').val();
                    let locations_list = $('#locations_list').val();
                    let src_value = $('#src_value').val();
                    let is_use = $('#is_use').val();
                    let bet_type = $('#bet_type').val();
                    let dividend_rank = $('#dividend_rank').val();

                    //alert(sports_list + ' ' + locations_list + ' ' + src_value);

                    location.href = '/sports_w/sports_league_manager.php?sports_list=' + sports_list + '&locations_list=' + locations_list + '&src_value=' + src_value + '&is_use='+is_use + '&bet_type='+bet_type + '&dividend_rank=' + dividend_rank;
                });

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
            })
            
            // 이미지 등록
            const fn_btn_league_send = function(id) {
                // document.getElementById 사용하니 fakepath 피해서 파일명이 가져와진다.
                let name = document.getElementById('uploadfile').files[0].name;
                
                if(id != image_check){
                    alert("파일을 첨부해 주세요.");
                    return;
                }
                $("#thumbnail_fm_"+id).submit();
                
                let str_msg = '수정 하시겠습니까?';
                let result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_league_manager_league_image_name_insert.php',
                        data: {'name': name},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('등록했습니다.');
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
            };
            
            // 리그 일괄 수정
            const adm_btn_league_update = function(id, front_name) {
                if(id != image_check){
                    alert("파일을 첨부해 주세요.");
                    return;
                }
                $("#thumbnail_fm_"+id).submit();
                
                let file_name = $('#update_name_'+id).val();
                let str_msg = '수정 하시겠습니까?';
                let result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_league_manager_league_image_name_update.php',
                        data: {'id': id, 'name': file_name, 'front_name': front_name},
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
            };

            function allCheckFunc(obj) {
                $("[name=chk]").prop("checked", $(obj).prop("checked"));
            }

            /* 체크박스 체크시 전체선택 체크 여부 */
            function oneCheckFunc(obj)
            {
                var allObj = $("[name=checkbox_css_all]");
                var objName = $(obj).attr("name");

                if ($(obj).prop("checked"))
                {
                    checkBoxLength = $("[name=" + objName + "]").length;
                    checkedLength = $("[name=" + objName + "]:checked").length;

                    if (checkBoxLength == checkedLength) {
                        allObj.prop("checked", true);
                    } else {
                        allObj.prop("checked", false);
                    }
                } else
                {
                    allObj.prop("checked", false);
                }
            }

            // 수정
            function fn_update_menu5(idx, is_use_status, bet_type) {
                let name = $('#name_' + idx).val();
                let policy = $('#policy_' + idx).val();

                let is_use = $('#is_use_' + idx).val();
            
                let str_msg = '수정 하시겠습니까?';
                let result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_league_manager_prc_update.php',
                        data: {'idx': idx, 'name': name, 'policy': policy,'is_use' : is_use/*, 'input_refund_rate' : input_refund_rate*/, 'bet_type':bet_type/*, 'file_name': file_name*/},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('수정하였습니다.');
                                let url = '/sports_w/sports_league_manager?is_use='+is_use_status;
                                window.location.reload(url);
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

            // 전체수정
            function fn_all_update_menu5(bet_type) {
                //var input_refund_rate = $('#input_refund_rate_'+idx).val();

                let arrLeaguesData = [];

                $('#leagues_tbody tr').each(function (index, tr) {
                    let idx = tr.cells[1].innerHTML;
                    //alert(idx);
                    let name = $('#name_' + idx).val();
                    let policy = $('#policy_' + idx).val();
                    let is_use = $('#is_use_' + idx).val();
                    //let input_refund_rate = $('#input_refund_rate_' + idx).val();
                    /*let current_rate = $('#input_refund_rate_current_' + id).val();
                     let update_rate = $('#input_refund_rate_' + id).val();
                     if(current_rate !== update_rate){
                     let obj = new Object();
                     obj = {'id':id, 'name':name, 'update_rate':update_rate};
                     arrSportsData.push(obj);
                     }*/
                    let obj = new Object();
                    obj = {'idx': idx, 'name': name, 'policy': policy,'is_use' : is_use};
                    arrLeaguesData.push(obj);
                });
                let leaguesData = JSON.stringify(arrLeaguesData);

                var str_msg = '저장하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_league_manager_update_all.php',
                        data: {'leaguesData': leaguesData, 'bet_type':bet_type},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('저장하였습니다.');
                                //window.location.reload();
                                arrLeaguesData.forEach(function (item) {
                                    $('#name_' + item['idx']).val(item['name']);
                                });
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('저장에 실패하였습니다.');
                            return;
                        }
                    });
                }
            }

            function setIsUse(isUse, sports_list, locations_list, bet_type) {
                let param_url = '/sports_w/_sports_league_manager_update_is_use.php';
                let str_msg = '';
                let chkboxval = [];

                $("input:checkbox[name='chk']:checked").each(function (index, item) {
                    /*console.log($(this).val());
                     if(index!=0) {
                     chkboxval += ',';
                     }*/
                    chkboxval.push($(this).val());
                });

                chkboxval = chkboxval.join(",");

                str_msg = '수정하시겠습니까?';
                var result = confirm(str_msg);
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: param_url,
                        data: {'isUse': isUse, 'chkval': chkboxval, 'bet_type':bet_type},
                        success: function (data) {
                            if (data['retCode'] == "1000") {
                                alert('변경되었습니다.');
                                window.location = '/sports_w/sports_league_manager.php?sports_list=' + sports_list + '&locations_list=' + locations_list;
                            } else if (data['retCode'] == "2001") {
                                alert('잘못된 요청 입니다.');
                            } else {
                                alert('실패 하였습니다.');
                                //window.location.reload();
                            }
                        },
                        error: function (request, status, error) {
                            alert('서버 오류 입니다.');
                            window.location.reload();
                        }
                    });
                }
            }

            function fnClickManagePolicy() {
                // $('#manage_policy').attr('style', 'display: block');
                location.replace('/sports_w/sports_dividend_amount_manager.php');
            }

            function fnUpdateDividendPolicy(rank) {
                var amount = $('#p_amount_' + rank).val();

                var result = confirm('수정 하시겠습니까?');
                if (result) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/sports_w/_sports_league_manager_prc_update2.php',
                        data: {'rank': rank, 'amount': amount},
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

            /*
             function fnPopupAdd() {
             
             }
             */

            function fnPopupClose() {
                $('#manage_policy').attr('style', 'display: none');
            }

            let prevInput = '';
            function fnCheckInput(o) {
                if (o.value.search(/^\d*(\.\d{0,2})?$/) == -1) {
                    o.value = prevInput;
                } else {
                    prevInput = o.value;
                }
            }
            // 이미지 썸네일 추가 ADD KSG 
            function setThumbnail(event, id) {
                var reader = new FileReader();
                $('#update_name_'+id).val(event.target.files[0].name);
                reader.onload = function(event) {
                    var img = document.createElement("img");
                    img.setAttribute("src", event.target.result);
                    document.querySelector("div.image_container_"+id).appendChild(img);
                    image_check = id;
                };

                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
        <?php
        include_once(_BASEPATH . '/common/bottom.php');
        ?>
    </body>
</html>
