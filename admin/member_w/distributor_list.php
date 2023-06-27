<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');
include_once(_LIBPATH . '/class_ComQuery.php');
include_once(_LIBPATH . '/class_Code.php');
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');

//////// login check end
$result['retCode'] = SUCCESS;
try {

    $MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
    $db_conn = $MEMAdminDAO->dbconnect();

    if ($db_conn) {

        $p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
        if ($p_data['page'] < 1) {
            $p_data['page'] = 1;
        }

        $p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 50);
        if ($p_data['num_per_page'] < 1) {
            $p_data['num_per_page'] = 50;
        }

        $srch_level = trim(isset($_REQUEST['srch_level']) ? $_REQUEST['srch_level'] : 0);

        $p_data['sql_where'] = 'WHERE 1 = 1 ';

        $p_data['sql'] = " SELECT COUNT(*) AS CNT FROM member a left join member pt on a.recommend_member = pt.idx ";

        $where_new = " ";
        $param_where_new = array();
        if (($srch_level == 0)) {
            $p_data['sql_where'] .= " AND a.u_business <> 1 ";
        } else {
            $p_data['sql_where'] .= " AND a.u_business= $srch_level";
            $where_new = " AND parent.u_business = ? ";
            $param_where_new[] = $srch_level;
        }
        //$p_data['sql_where'] .= $srch_basic;
        $p_data['sql'] .= $p_data['sql_where'];

        $db_dataArrCnt = $MEMAdminDAO->getQueryData($p_data);
        $total_cnt = $db_dataArrCnt[0]['CNT'];

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

            $p_data['sql'] = "SELECT a.idx, a.id, a.nick_name, a.money, a.point,a.u_business,business_type.name as bu_name,pt.id as dist_id, pt.idx as dist_idx ";
            $p_data['sql'] .= ", a.status, a.last_login, a.reg_time, a.dis_id,dist_type.name as dist_name ";
            $p_data['sql'] .= ", (SELECT COUNT(*) FROM member WHERE member.recommend_member = a.idx AND status = 1 AND member.u_business = 1) as dis_user_cnt ";
            $p_data['sql'] .= ", (SELECT COUNT(*) FROM member WHERE member.recommend_member = a.idx AND status = 2 AND member.u_business = 1) as dis_user_cnt_2 ";
            $p_data['sql'] .= ", (SELECT COUNT(*) FROM member WHERE member.recommend_member = a.idx AND status = 3 AND member.u_business = 1) as dis_user_cnt_3 ";
            $p_data['sql'] .= " FROM member a ";
            $p_data['sql'] .= " LEFT JOIN business_type ON a.u_business = business_type.id 
                            LEFT JOIN dist_type ON a.dist_type = dist_type.id
                            left join member pt on a.recommend_member = pt.idx and pt.u_business <> 1 ";

            $p_data['sql'] .= $p_data['sql_where'];
            
            //CommonUtil::logWrite(" distributor_list  today_ch_val sql " . $p_data['sql'], "info");
            $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
        }
        $p_data['sql'] = "";

        $p_data['sql'] = "SELECT status, COUNT(*) as cnt FROM member WHERE u_business <> 1 GROUP BY STATUS ; ";
        $db_dataArrUserStatus = $MEMAdminDAO->getQueryData($p_data);

        if (!empty($db_dataArrUserStatus)) {
            foreach ($db_dataArrUserStatus as $row) {
                switch ($row['status']) {
                    case 1: $db_user_status_cnt[1] = $row['cnt'];
                        break;
                    case 2: $db_user_status_cnt[2] = $row['cnt'];
                        break;
                    case 3: $db_user_status_cnt[3] = $row['cnt'];
                        break;
                    case 11: $db_user_status_cnt[11] = $row['cnt'];
                        break;
                }
            }
        }

        // 은행목록
        $p_data['sql'] = "select idx, account_code, account_name from account ";
        $bankList = $db_dataArrUserStatus = $MEMAdminDAO->getQueryData($p_data);
               
        // 금일 충전 환전 
        $db_today_s_date = date("Y-m-d 00:00:00");
        $db_today_e_date = date("Y-m-d 23:59:59");
        
        // 이번달 충전환전
        $start_date = date("Y/m/d  00:00:00", mktime(0, 0, 0, intval(date('m')), 1, intval(date('Y'))));
        $end_date = date("Y/m/d  23:59:59");
        $db_srch_s_date = str_replace('/', '-', $start_date);
        $db_srch_e_date = str_replace('/', '-', $end_date);
        
        // 총판 정보 읽어오기 
        $sql = "select id,name,low_id,high_id from business_type where id <> 1 order by id asc ";
        $db_dists = $MEMAdminDAO->getQueryData_pre($sql, []);
        
        
         // 총판 유형 정보 읽어오기 
        $sql = "select * from dist_type where id <> 0 order by id asc ";
        $db_dist_types = $MEMAdminDAO->getQueryData_pre($sql, []);
    }
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('fail distributor_list mysqli_sql_exception ' . $e->getMessage(), "db_error");
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    CommonUtil::logWrite("fail distributor_list Exception ", "error");
    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {

    if (!$MEMAdminDAO) {
        $MEMAdminDAO->dbclose();
    }
    //CommonUtil::logWrite("fail distributor_list db_recommandArr ". json_encode($db_recommandArr), "error");
    if ($result['retCode'] < 0) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
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
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
    <body>
        <form id="popForm" name="popForm" method="post">
            <input type="hidden" id="seq" name="seq">
            <input type="hidden" id="m_idx" name="m_idx">
            <input type="hidden" id="m_dis_id" name="m_dis_id">
            <input type="hidden" id="selContent" name="selContent" value="3">
        </form>
        <div class="wrap">
            <?php
            $menu_name = "distributor_list";

            include_once(_BASEPATH . '/common/left_menu.php');

            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">

                <div class="title">
                    <a href="">
                        <i class="mte i_group mte-2x vam"></i>
                        <h4>총판 정보</h4>
                    </a>
                </div>

                <!-- detail search -->
                <div class="panel search_box">
                    <h5>일반총판 (<?= !empty($db_user_status_cnt[1]) ? number_format($db_user_status_cnt[1]) : 0 ?>)</h5>
                    <h5>정지총판 (<?= !empty($db_user_status_cnt[2]) ? number_format($db_user_status_cnt[2]) : 0 ?>)</h5>
                    <h5>탈퇴총판 (<?= !empty($db_user_status_cnt[3]) ? number_format($db_user_status_cnt[3]) : 0 ?>)</h5>
                    <h5>대기총판 (<?= !empty($db_user_status_cnt[11]) ? number_format($db_user_status_cnt[11]) : 0 ?>)</h5>
                </div>
                <!-- END detail search -->

                <!-- list -->
                <div class="panel reserve">       
                    <form id="search" name="search" action='<?= $_SERVER['PHP_SELF'] ?>'>        
                        <div class="panel_tit">
                            <div class="search_form fl">
                                <div class="" style="padding-right: 10px;">

                                    <select name="srch_level" id="srch_level">
                                        <option value="0" <?php if (0 == $srch_level){echo "selected";} ?>>전체</option>
                                        <?php
                                        if (!empty($db_dists)) {
                                            foreach ($db_dists as $row) {
                                                $id = $row['id'];
                                                $name = $row['name'];
                                        ?>
                                        <option value="<?= $id ?>" <?php if ($id == $srch_level) {echo "selected";} ?>><?= $name ?></option>
                                            <?php }
                                        } ?>
                                    </select>

                                </div>

                                <div><a href="javascript:goSearch();" class="btn h30 btn_red">검색</a></div>
                            </div>
                            <a href="javascript:;" class="btn h30 btn_blu fr" onClick="openCreateDistributorPop();">총판생성</a>
                        </div>
                    </form>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th colspan="5">가입정보</th>
                                <th colspan="3">회원수</th>
                                <th colspan="3">상태정보</th>
                                <th colspan="12">정산정보</th>
                                <th rowspan="2">관리</th>
                            </tr>
                            <tr>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>총판레벨</th>
                                <th>총판소속</th>
                                <th>총판유형</th>
                                <th>정상</th>
                                <th>정지</th>
                                <th>탈퇴</th>
                                <th>최종로그인</th>
                                <th>가입일시</th>
                                <th>상태</th>
                                <th>금일가입</th>
                                <th>금일충전</th>
                                <th>금일환전</th>
                                <th>금일차액</th>
                                <th>금일%</th>
                                <th>이번달가입</th>
                                <th>이번달충전</th>
                                <th>이번달환전</th>
                                <th>이번달차액</th>
                                <th>이번달%</th>
                                <th>총보유머니</th>
                                <th>총보유포인트</th>
                            </tr>
<?php
if ($total_cnt > 0) {
    $i = 0;
    if (!empty($db_dataArr)) {
        foreach ($db_dataArr as $row) {
            $num = $p_data['num_per_page'] * ($p_data['page'] - 1) + $i;
            $db_m_idx = $row['idx'];
            $db_m_id = $row['id'];

            $db_status = "";
            switch ($row['status']) {
                case 1: $db_status = "사용중";
                    break;
                case 2: $db_status = "정지";
                    break;
                case 3: $db_status = "탈퇴";
                    break;
                case 11: $db_status = "대기";
                    break;
            }

            $db_point = $row['point']; // + $row['betting_p'];
            
            //It brings settlement information (deposit, holding money, holding points) of distributor members
            $dist_arr = GameCode::doGetDistCalculateInfo($MEMAdminDAO,$db_today_s_date,$db_today_e_date,$db_srch_s_date,$db_srch_e_date,$db_m_idx);
            $today_ch_val = (isset($dist_arr['today_ch_val']) ? $dist_arr['today_ch_val'] : 0);
            $today_ex_val = (isset($dist_arr['today_ex_val']) ? $dist_arr['today_ex_val'] : 0);
            $month_ch_val = (isset($dist_arr['month_ch_val']) ? $dist_arr['month_ch_val'] : 0);
            $month_ex_val = (isset($dist_arr['month_ex_val']) ? $dist_arr['month_ex_val'] : 0);
            // today%
            $today_rate_buff = $today_calculate_rate = $month_rate_buff = $month_calculate_rate =0;
            GameCode::doChExUnitPriceCalc($today_ch_val, $today_ex_val, $today_rate_buff, $today_calculate_rate);
            GameCode::doChExUnitPriceCalc($month_ch_val, $month_ex_val, $month_rate_buff, $month_calculate_rate);
            ?>
                                        <tr onmouseover="this.style.backgroundColor = '#FDF2E9';" onmouseout="this.style.backgroundColor = '#ffffff';">
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['id'] ?></a>
                                            </td>
                                            <td style='text-align:left'>
                                                <a href="javascript:;" onClick="popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', '<?= $row['id'] ?>');"><?= $row['nick_name'] ?></a>
                                            </td>
                                            <td><?= $row['bu_name'] ?></td>
                                            <td><?= $row['dist_id'] ?></td>
                                            <td><?= $row['dist_name'] ?></td>
                                            <td style='text-align:right'><?= number_format($row['dis_user_cnt']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['dis_user_cnt_2']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['dis_user_cnt_3']) ?></td>
                                            <td><?= $row['last_login'] ?></td>
                                            <td><?= $row['reg_time'] ?></td>
                                            <td><?= $db_status ?></td>
                                            <td style='text-align:right'><?= number_format(isset($dist_arr['today_mem_cnt_reg']) ? $dist_arr['today_mem_cnt_reg'] : 0) ?></td>
                                            <td style='text-align:right'><?= number_format(isset($dist_arr['today_ch_val']) ? $dist_arr['today_ch_val'] : 0) ?></td>
                                            <td style='text-align:right'><?= number_format(isset($dist_arr['today_ex_val']) ? $dist_arr['today_ex_val'] : 0) ?></td>
                                            <td style='text-align:right'><?= GameCode::strColorRet($today_ch_val, $today_ex_val, 1) ?></td>
                                            <td style='text-align:right'><?=$today_calculate_rate?> %</td>
                                            <td style='text-align:right'><?= number_format(isset($dist_arr['month_mem_cnt_reg']) ? $dist_arr['month_mem_cnt_reg'] : 0) ?></td>
                                            <td style='text-align:right'><?= number_format(isset($dist_arr['month_ch_val']) ? $dist_arr['month_ch_val'] : 0) ?></td>
                                            <td style='text-align:right'><?= number_format(isset($dist_arr['month_ex_val']) ? $dist_arr['month_ex_val'] : 0) ?></td>
                                            <td style='text-align:right'><?= GameCode::strColorRet($month_ch_val, $month_ex_val, 1) ?></td>
                                            <td style='text-align:right'><?=$month_calculate_rate?> %</td>
                                            <td style='text-align:right'><?= number_format($row['money']) ?></td>
                                            <td style='text-align:right'><?= number_format($row['point']) ?></td>
                                            <td>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="disinfo('<?= $db_m_id ?>')">수정</a>
                                                <a href="javascript:;" class="btn h25 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php', 'popmsg', 660, 1000, 'msg', '<?= $db_m_idx ?>');">쪽지</a>
                                            </td>
                                        </tr>
            <?php
            $i++;
        }
    }
} else {
    ?>
                                <tr><td colspan="18">데이터가 없습니다.</td></tr>
                                <?php
                            }
                            ?>

                        </table>
                            <?php
                            $reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
                            //$default_link = "$reqFile?srch_key=" . $p_data['srch_key'] . "&srch_val=" . $p_data['srch_val'] . "&srch_level=" . $srch_level . "";
                            $default_link = "$reqFile?srch_level=" . $srch_level . "";
                            include_once(_BASEPATH . '/common/page_num.php');
                            ?>                
                    </div>
                </div>
                <!-- END list -->
            </div>
            <!-- END Contents -->
        </div>



        <!-- 총판생성 관련 페이지 -->
        <div id="create_distributor_pop" name="create_distributor_pop" class="pop-window">
            <div class="pop_wrap">
                <div class="panel reserve">
                    <div class="title">
                        <h4>총판정보 입력</h4>
                    </div>

                    <div class="tline">
                        <table id="popup_list" name="popup_list" class="mlist">
                            <tr>
                                <th>총판레벨<font color='red'>*</font></th>
                                <td>
                                    <span class="fl mr5">
                                        <select style="width:140" id="u_business">
                                                    <?php
                                                    if ($total_cnt > 0) {
                                                        $i = 0;
                                                        if (!empty($db_dists)) {
                                                            foreach ($db_dists as $row) {
                                                                $id = $row['id'];
                                                                $name = $row['name'];
                                                                echo "selected";
                                                                ?>
                                                        <option value="<?= $id ?>"><?= $name ?></option>
                                                    <?php }
                                                }
                                                    } ?>
                                        </select>
                                    </span>
                                    <span class="ml10 fl">※ 총판, 하부총판은 생성 후 총판상세에서 소속총판을 변경해주세요. </span>
                                </td>
                            </tr>
                            <tr>
                                <th>아이디<font color='red'>*</font></th>
                                <td><span><input  type="text" maxlength="12" id="join_id" name=""  class="input1"></span>
                                    <span class="ml10 fl"><a href="#" class="btn btn_blu h30 check_id_btn">중복확인</a> ※ 4~12자의 영문, 숫자 조합(첫글자는 영문)</span> </td>
                            </tr>
                            <tr>
                                <th>닉네임<font color='red'>*</font></th>
                                <td><span><input id="join_nickname" name="" class="input1"></span> 
                                    <span class="ml10 fl"><a href="#" class="btn btn_blu h30 check_nick_btn">중복확인</a> ※ 2자 이상 한글</span></td>
                            </tr>
                            <tr>
                                <th>비밀번호<font color='red'>*</font></th>
                                <td><input id="join_password" type="password" name="" class="input1" maxlength="12">
                                    <span class="ml10 fl"> ※ 4~12자의 영문, 숫자 조합</span></td>
                            </tr>
                            <tr>
                                <th>비밀번호 확인<font color='red'>*</font></th>
                                <td><input id="join_password_r" type="password" class="input1"></td>
                            </tr>
                            <tr>
                                <th>휴대전화</th>
                                <td>
                                    <span class="fl"><select style="width:90" id="call_0" class="input1">
                                            <option value="010">010</option>
                                            <option value="011">011</option>
                                            <option value="017">017</option>
                                        </select></span> 
                                    <span class="ml10 fl"> - </span>
                                    <span class="ml10 fl"> <input id="call_1" class="input1" maxlength="4"></span>
                                    <span class="ml10 fl"> - </span>
                                    <span class="ml10 fl"> <input id="call_2" class="input1" maxlength="4"></span>
                                </td>
                            </tr>
                            <tr>
                                <th>예금주<font color='red'>*</font></th>
                                <td><input id="account_name" class="input1"></td> 
                            </tr>
                            <tr>
                                <th>계좌번호<font color='red'>*</font></th>
                                <td>
                                    <span class="fl"><select id="account_bank" class="input1">
                                            <option value="">-- 은행선택 --</option>
                                            <?php
                                            if (!empty($bankList)) {
                                                foreach ($bankList as $row) {
                                                    ?>
                                                    <option value="<?= $row['account_code'] ?>" ><?= $row['account_name'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select></span> 
                                    <span class="ml10 fl"> <input id="account_number" class="input1"></td></span>
                            </tr>
                            <tr>
                                <th>총판유형<font color='red'>*</font></th>
                                <td>
                                    <span class="fl mr5">
                                          <select style="width: 140px;" id="dist_types" name="dist_types">
                                                    <?php
                                                    
                                                        $i = 0;
                                                        if (!empty($db_dist_types)) {
                                                            foreach ($db_dist_types as $row) {
                                                                $id = $row['id'];
                                                                $name = $row['name'];
                                                                echo "selected";
                                                                ?>
                                                            <option value="<?= $id ?>"><?= $name ?></option>
                                                    <?php   }
                                                        }
                                                     ?>
                                          </select><!-- comment -->
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="panel_tit">
                        <div class="search_form fr mt20">
                            <a href="#" class="btn h30 btn_blu joinShop">등록</a>
                            <a href="#" class="btn h30 btn_blu ml10" onclick="javascript:fnPopupClose();">닫기</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 총판생성 관련 페이지 끝 -->


<?php
include_once(_BASEPATH . '/common/bottom.php');
?> 
    </body>
    <script>
        let check_id = false;
        let check_name = false;

        let check_num = /[0-9]/; // 숫자
        let check_eng = /[a-zA-Z]/; // 문자
        let check_spc = /[~!@#$%^&*()_+|<>?:{}]/; // 특수문자
        let check_kor = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/; // 한글체크

        function goSearch(vtype = null) {
            var fm = document.search;

            //if ((fm.srch_key.value != '') && (fm.srch_val.value == '')) {
            //alert('검색어를 입력해 주세요.');
            //fm.srch_val.focus();
            //return;
            //}

            fm.method = "get";
            fm.submit();
        }

        function disinfo(dis_id) {
            popupWinPost('/member_w/pop_disinfo.php', 'popdisinfo', 800, 1400, 'disinfo', dis_id);
        }

        // 총판생성 팝업열기
        const openCreateDistributorPop = function () {
            $('#create_distributor_pop').attr('style', 'display: block');
        }

        const fnPopupClose = function () {
            $('#create_distributor_pop').attr('style', 'display: none');
        }

        $('.check_id_btn').on('click', function () {
            $('#join_id').val($('#join_id').val().replace(/ /gi, "")); // 공백 제거
            let id = $('#join_id').val();
            if (/*check_kor.test(id) ||*/ check_spc.test(id) /*|| false == check_num.test(id)*/) {
                alert('영문, 숫자만 입력해주세요.');
                return;
            }
            ;

            if (id.length === 0) {
                alert('아이디를 입력해주세요.');
                return;
            }

            $.ajax({
                url: '/member_w/_ajax_id_check.php',
                type: 'post',
                data: {
                    'id': id,
                },
            }).done(function (response) {
                let res = JSON.parse(response);
                if (res['retCode'] === 1000) {
                    check_id = confirm('사용가능한 아이디 입니다. 사용하시겠습니까?');
                    if (check_id) {
                        $('#join_id').attr('readonly', true);
                    }
                } else {
                    alert(res['retMsg']);
                }
            }).fail(function (error) {
                alert('실패');
            });
        });

        $('.check_nick_btn').on('click', function () {
            if (!check_id) {
                alert('아이디 중복체크를 확인해주세요.');
                return;
            }

            $('#join_nickname').val($('#join_nickname').val().replace(/ /gi, "")); // 공백 제거
            let nick_name = $('#join_nickname').val();
            if (check_eng.test(nick_name) || check_num.test(nick_name) || check_spc.test(nick_name)) {
                alert('한글만 입력해주세요.');
                return;
            }
            ;

            if (nick_name.length === 0) {
                alert('닉네임을 입력해주세요.');
                return;
            }

            $.ajax({
                url: '/member_w/_ajax_nickname_check.php',
                type: 'post',
                data: {
                    'nick_name': nick_name,
                },
            }).done(function (response) {
                let res = JSON.parse(response);
                if (res['retCode'] === 1000) {
                    check_name = confirm('사용가능한 닉네임 입니다. 사용하시겠습니까?');
                    if (check_name) {
                        $('#join_nickname').attr('readonly', true);
                    }
                } else {
                    alert(res['retMsg']);
                }
            }).fail(function (error) {
                alert('에러');
            });
        });

        // 총판생성
        $('.joinShop').on('click', function () {
            if ($('#call_0').val().replace(/ /gi, "").length < 3 ||
                    $('#call_1').val().replace(/ /gi, "").length > 4 ||
                    $('#call_2').val().replace(/ /gi, "").length != 4) {
                alert('핸드폰 번호를 확인해주세요.');
                return;
            }

            if (!check_id) {
                alert('아이디 중복체크를 확인해주세요.');
                return;
            }

            if (!check_name) {
                alert('닉네임 중복체크를 확인해주세요.');
                return;
            }

            if ($('#join_password').val() != $('#join_password_r').val()) {
                alert('비밀번호 확인이 틀립니다.');
                return;
            }

            if (
                    $('#join_password').val().length < 4 ||
                    $('#join_password').val().length > 12) {
                alert('비밀번호는 4 ~ 12자 이내 사용해주세요.');
                return;
            }

            if ($('#account_number').val().replace(/ /gi, "").length == 0) {
                alert('계좌번호를 입력해주세요.');
                return;
            }

            $.ajax({
                url: '/member_w/_ajax_create_distributor.php',
                type: 'post',
                data: {
                    'id': $('#join_id').val(),
                    'u_business': $('#u_business').val(),
                    'password': $('#join_password').val(),
                    'nickname': $('#join_nickname').val(),
                    'call': $('#call_0').val() + '-' + $('#call_1').val() + '-' + $('#call_2').val(),
                    'account_bank': $('#account_bank').val(),
                    'account_number': $('#account_number').val(),
                    'account_name': $('#account_name').val(),
                    'dist_types': $('#dist_types').val()
                },
            }).done(function (response) {
                let result  = JSON.parse(response);
                if(1000 == result['retCode']){
                    alert('총판생성에 성공하였습니다. 로그인 해주세요!');
                    location.reload();
                    // 성공 시 동작
                }else{
                    alert(result['retMsg']);
                }
            }).fail(function (error) {
                console.log(error);
                alert(error.responseJSON['messages']['error']);
            }).always(function (response) {
                // alert('???');
            });
        });
    </script>
</html>