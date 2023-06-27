<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');
include_once(_LIBPATH . '/class_Code.php');

include_once(_BASEPATH . '/GamblePatch/GambelGmPt.php');
include_once(_BASEPATH . '/GamblePatch/KwinGmPt.php');
include_once(_BASEPATH . '/GamblePatch/ChoSunGmPt.php');
include_once(_BASEPATH . '/GamblePatch/BetsGmPt.php');
include_once(_LIBPATH . '/class_chExFunc.php');
include_once(_LIBPATH . '/class_UserPayBack.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$p_data['mtype'] = trim(isset($_POST['mtype']) ? $_POST['mtype'] : '');
$p_data['chkval'] = trim(isset($_POST['chkval']) ? $_POST['chkval'] : '');

if ($p_data['chkval'] != '') {
    $money_idx_arr = explode(',', $p_data['chkval']);
    $chkval = $p_data['chkval'];
}

// 1: 입금 전 (신청) 2: 입금 확인 (대기) 3: 충전 완료 (완료) 4: 취소 11:관리자 취소  
$cash_use_kind = 'P';

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();
if (!$db_conn) {
    $UTIL->logWrite("[_set_charge_money] [error 2200]", "error");
    //$UTIL->checkFailType('-1000', '', '디비연결실패', 'json');
    return;
}

do {

    if (!$CASHAdminDAO->trans_start()) {
        // start transaction error
        $UTIL->logWrite("[_set_charge_money] [error -1004]", "error");
        $result['retCode'] = -1004;
        $result['retMsg'] = "fail start trans";
        break;
    }

    try {
        $result['retCode'] = 1000;
        //신청 - 대기,승인,취소 가능
        //대기 - 취소,승인 가능
        //승인 - 취소만가능

        switch ($p_data['mtype']) {
            case "2": // 1 신청 상태 일때 대기 
                $result["retCode"] = 3000;
                $p_data['sql'] = "select b.idx,b.member_idx, a.level, a.money, b.money as set_money,a.point,a.reg_first_charge,a.charge_first_per,b.status from member a, member_money_charge_history b ";
                $p_data['sql'] .= " where a.idx=b.member_idx and b.status = 1";
                $result_data = $CASHAdminDAO->getQueryData($p_data);
                if (FAIL_DB_SQL_EXCEPTION === $result_data) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }

                foreach ($result_data as $value) {
                    $idx = $value['idx'];
                    $p_data['sql'] = "update member_money_charge_history set status = 2, update_dt = now() where idx = $idx ";
                    //$CASHAdminDAO->setQueryData($p_data);
                    if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                    $member_idx = $value['member_idx'];
                    $now_cash = $value['money'];
                    $af_cash = $value['money'];
                    $a_comment = "충전요청 대기";
                    $ac_code = 204;
                    $p_data['sql'] = "insert into  t_log_cash ";
                    $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                    $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx, 0 ";
                    $p_data['sql'] .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                    //$CASHAdminDAO->setQueryData($p_data);
                    if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                }
                break;
            case "3": // 1 신청, 2 대기 , 11 취소 일때만 승인 가능 
                // 돌발충전 보너스 값
                $p_data['sql'] = "select bonus, max_bonus from charge_event where level > 0;";
                $chargeEventData = $CASHAdminDAO->getQueryData($p_data);
                if (FAIL_DB_SQL_EXCEPTION === $chargeEventData) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
                $get_config_str = "'charge_first_per', 'charge_per', 'charge_max_money', 'charge_money','reg_first_charge','event_charge_status','event_charge_start','event_charge_end'";
                $p_data['sql'] = "select u_level, set_type, set_type_val from t_game_config ";
                $p_data['sql'] .= " where set_type in ($get_config_str) ";
                $retData = $CASHAdminDAO->getQueryData($p_data);
                if (FAIL_DB_SQL_EXCEPTION === $retData) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
                $n_reg_first_charge = 0;
                $n_event_charge_status = 'OFF';
                $n_event_charge_start = date("Y-m-d H:i:s");
                $n_event_charge_end = date("Y-m-d H:i:s");
                $currentDate = date("Y-m-d H:i:s");

                foreach ($retData as $row) {
                    $db_level = $row['u_level'];

                    switch ($row['set_type']) {
                        case 'charge_first_per':
                            $db_config[$db_level]['charge_first_per'] = $row['set_type_val'];
                            break;
                        case 'charge_per':
                            $db_config[$db_level]['charge_per'] = $row['set_type_val'];
                            break;
                        case 'charge_max_money':
                            $db_config[$db_level]['charge_max_money'] = $row['set_type_val'];
                            break;
                        case 'charge_money':
                            $db_config[$db_level]['charge_money'] = $row['set_type_val'];
                            break;
                        case 'reg_first_charge':
                            $n_reg_first_charge = $row['set_type_val'];
                            break;
                        case 'event_charge_status':
                            $n_event_charge_status = $row['set_type_val'];
                            break;
                        case 'event_charge_start':
                            $n_event_charge_start = $row['set_type_val'];
                            break;
                        case 'event_charge_end':
                            $n_event_charge_end = $row['set_type_val'];
                            break;
                    }
                }
                
                // create gamble patch
                $gmPt = null;
                if ('KWIN' == SERVER) {
                    $gmPt = new KwinGmPt();
                } else if ('GAMBLE' == SERVER) {
                    $gmPt = new GambelGmPt();
                } else if ('CHOSUN' == SERVER) {
                    $gmPt = new ChoSunGmPt();
                } else if ('BETS' == SERVER) {
                    $gmPt = new BetsGmPt();
                } else {
                    throw new Exception('fail GamblePatch !!!');
                }
                $p_data['sql'] = "select b.u_key,b.idx,b.member_idx, a.level, a.money, b.money as set_money,a.point,a.reg_first_charge,a.charge_first_per, a.is_exchange ,b.status,b.create_dt, b.charge_point_yn ,b.bonus_level,b.bonus_option_idx"
                        . " from member a, member_money_charge_history b ";
                $p_data['sql'] .= " where a.idx=b.member_idx and b.idx in ($chkval) and b.status in(1,2) ";
                $result_data = $CASHAdminDAO->getQueryData($p_data);
                if (FAIL_DB_SQL_EXCEPTION === $result_data) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }

                foreach ($result_data as $value) {
                    $idx = $value['idx'];
                    $member_idx = $value['member_idx'];
                    $now_cash = $value['money'];
                    $set_money = $value['set_money'];
                    $u_level = $value['level'];
                    $now_point = $value['point'];
                    $is_exchange = $value['is_exchange'];
                    $create_dt = $value['create_dt'];
                    $u_key = $value['u_key'];
                    $charge_point_yn = $value['charge_point_yn'];
                    $af_point = 0;
                    $p_a_comment = '';
                    $ch_point = 0;
                    $p_ac_code = 0;
                    $str_set_type = '';

                    $n_is_reg_first_charge = $value['reg_first_charge'];
                    $n_is_charge_first_per = $value['charge_first_per'];
                    $n_is_charge_event = false;
                    $ratio_value = 0;
                    $const_value = 0;
                    //if (1 == $value['charge_point_yn']) {
                        if ($n_event_charge_status == 'ON' && date("Y-m-d " . $n_event_charge_start) <= $currentDate && date("Y-m-d " . $n_event_charge_end) >= $currentDate &&
                                $chargeEventData[$u_level - 1]['bonus'] > 0) { // 돌발첫충
                            $ch_point = ($chargeEventData[$u_level - 1]['bonus'] * $set_money) / 100;
                            if ($chargeEventData[$u_level - 1]['max_bonus'] < $ch_point) {
                                $ch_point = $chargeEventData[$u_level - 1]['max_bonus'];
                            }
                            $p_a_comment = '포인트 돌발충전 : ' . 'charge_event_per';
                            $str_set_type = 'charge_event_per';
                            //CommonUtil::logWrite('::::::::::::::: _set_charge_money n_event_charge_status : ' . $n_event_charge_status, "error");
                            $n_is_charge_event = true;
                        } else {
                            if ('0' == $n_is_reg_first_charge) { // 가입 첫충전 
                                $ch_point = ($n_reg_first_charge * $set_money) / 100;
                                if ($db_config[$u_level]['charge_max_money'] < $ch_point) {
                                    $ch_point = $db_config[$u_level]['charge_max_money'];
                                }
                                $p_a_comment = '포인트 충전 : ' . 'reg_first_charge';
                                $str_set_type = 'reg_first_charge';
                            } else if ('0' == $n_is_charge_first_per) { // 매일 첫 충전
                                //$ch_point = ($db_config[$u_level]['charge_first_per'] * $set_money) / 100;
                                //if ($db_config[$u_level]['charge_max_money'] < $ch_point) {
                                //    $ch_point = $db_config[$u_level]['charge_max_money'];
                                //}
                                $chEx = new ChExFunc();
                                $bonus_level = $value['bonus_level'];
                                $bonus_option_idx = $value['bonus_option_idx'];

                                list($ch_point, $ratio_value, $const_value) = $chEx->do_select_charge_first($bonus_level,$bonus_option_idx,$set_money,$CASHAdminDAO);
                        
                                $p_a_comment = '포인트 충전 : ' . 'charge_first_per';
                                $str_set_type = 'charge_first_per';
                            //} else if(0 == $is_exchange){
                            } else {
                                //$ch_point = ($db_config[$u_level]['charge_per'] * $set_money) / 100;
                                //if ($db_config[$u_level]['charge_money'] < $ch_point) {
                                //    $ch_point = $db_config[$u_level]['charge_money'];
                                //}
                                
                                $chEx = new ChExFunc();
                                $bonus_level = $value['bonus_level'];
                                $bonus_option_idx = $value['bonus_option_idx'];

                                list($ch_point, $ratio_value, $const_value) = $chEx->do_select_charge($bonus_level,$bonus_option_idx,$set_money,$CASHAdminDAO);
                        
                                
                                $p_a_comment = '포인트 충전 : ' . 'charge_per';
                                $str_set_type = 'charge_per';
                            }
                        }
                    //}

                    $p_ac_code = 10;
                    $af_point = $ch_point + $now_point;
                    $af_cash = $now_cash + $set_money;

                    // 가입첫충이거나 이벤트 충전이다.
                    if ('0' == $n_is_reg_first_charge || true == $n_is_charge_event) {
                        $p_data['sql'] = "update member set reg_first_charge = 1, charge_first_per = 1, money = money + " . $set_money . ", point = point + $ch_point where idx=" . $member_idx . " ";
                    } else if (0 == $n_is_charge_first_per) {
                        $p_data['sql'] = "update member set charge_first_per = 1,money = money + " . $set_money . ", point = point + $ch_point where idx=" . $member_idx . " ";
                    } else {
                        if (IMAGE_PATH != 'asbet') {
                            $p_data['sql'] = "update member set money = money + " . $set_money . ", point = point + $ch_point where idx=" . $member_idx . " ";
                        } else {
                            $p_data['sql'] = "update member set money = money + " . $set_money . ", point = point + $ch_point, is_exchange = 1 where idx=" . $member_idx . " ";
                        }
                    }

                    //$CASHAdminDAO->setQueryData($p_data);
                    if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                    $p_data['sql'] = "update member_money_charge_history set bonus_point = $ch_point,set_type = '$str_set_type', status = 3, update_dt = now(), result_money = $af_cash ,const_value = $const_value,ratio_value = $ratio_value where idx= $idx";
                    // 로그에는 두개합산값으로 저장한다.
                    //$set_money = $set_money + $ch_point;
                    //$CASHAdminDAO->setQueryData($p_data);
                    if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }

                    $gmPt->giveGMoneyCharge($CASHAdminDAO, $u_key, $member_idx, $set_money, $now_cash, $af_cash
                            , $ch_point, $now_point, $af_point, $p_a_comment, $idx);

                    UserPayBack::AddCharge($member_idx,$set_money,$CASHAdminDAO);
                    /* $a_comment = "머니 충전완료";
                      $ac_code = 1;

                      $p_data['sql'] = "insert into  t_log_cash ";
                      $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id,point) ";
                      $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx, " . $set_money . " ";
                      $p_data['sql'] .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id',$ch_point)";
                      //$CASHAdminDAO->setQueryData($p_data);
                      if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                      throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                      }
                      if (0 < $ch_point) {
                      $p_data['sql'] = "insert into  t_log_cash ";
                      $p_data['sql'] .= " (member_idx, ac_code, ac_idx, point, be_point, af_point, m_kind, coment, a_id) ";
                      $p_data['sql'] .= " values(" . $member_idx . ", $p_ac_code, $idx, " . $ch_point . " ";
                      $p_data['sql'] .= ", $now_point, $af_point, '" . strtoupper($cash_use_kind) . "','$p_a_comment','$admin_id')";
                      //$CASHAdminDAO->setQueryData($p_data);
                      if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                      throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                      }
                      } */


                    // 입금성공한 금액이 최대 입금금액 보다 많으면 데이터를 갱신해준다.
                    $p_data_ch['sql'] = "SELECT max_charge FROM total_member_cash WHERE member_idx = $member_idx";
                    $max_charge_arr = $CASHAdminDAO->getQueryData($p_data_ch);
                    if (FAIL_DB_SQL_EXCEPTION === $max_charge_arr) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                    if (true === isset($max_charge_arr) && 0 < count($max_charge_arr)) {
                        $max_charge = $max_charge_arr[0]['max_charge'];
                        if ($max_charge < $set_money) {
                            $p_data_up_ch['sql'] = "UPDATE total_member_cash set bf_max_charge = $max_charge, max_charge = $set_money WHERE member_idx = $member_idx";
                            //$CASHAdminDAO->setQueryData($p_data_up_ch);
                            if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data_up_ch)) {
                                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                            }
                        }
                    }
                }

                break;

            case "11": // 1 신청, 2 대기, 3번 승인 시  취소만 가능  처리를 한다.

                $p_data['sql'] = "select b.idx,b.member_idx, a.money,a.g_money,a.point, b.money as set_money,b.bonus_point,b.bonus_money,b.set_type,b.status,b.create_dt from member a, member_money_charge_history b ";
                $p_data['sql'] .= " where a.idx=b.member_idx and b.idx in ($chkval) and b.status in(1,2,3) ";
                $result_data = $CASHAdminDAO->getQueryData($p_data);
                if (FAIL_DB_SQL_EXCEPTION === $result_data) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
                CommonUtil::logWrite("set_charge_money 11 : " . $p_data['sql'], "info");

                foreach ($result_data as $value) {
                    //if (1 != $value['status'] && 2 != $value['status'] && 3 != $value['status'])
                    //    continue;
                    if (1 == $value['status'] || 2 == $value['status']) {
                        $idx = $value['idx'];
                        $member_idx = $value['member_idx'];
                        $now_cash = $value['money'];
                        $set_money = $value['set_money'];
                        $af_cash = $now_cash;
                        $p_data['sql'] = "update member_money_charge_history set status = 11  where idx = $idx";
                        //$CASHAdminDAO->setQueryData($p_data);
                        if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                        }
                        $a_comment = "충전요청 취소";
                        $ac_code = 111;

                        $p_data['sql'] = "insert into  t_log_cash ";
                        $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                        $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx, " . $set_money . " ";
                        $p_data['sql'] .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                        //$CASHAdminDAO->setQueryData($p_data);
                        if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                        }
                    } else if (3 == $value['status']) { // 완료 -> 취소
                        $idx = $value['idx'];
                        $p_data['sql'] = "update member_money_charge_history set status = 11,bonus_point = 0,result_money = 0,set_type = '', update_dt = now() where idx = $idx ";
                        $CASHAdminDAO->setQueryData($p_data);
                        if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                        }
                        $member_idx = $value['member_idx'];
                        $now_cash = $value['money'];
                        $now_point = $value['point'];
                        $set_money = -$value['set_money'];
                        $point = -$value['bonus_point'];
                        $g_money = $value['bonus_money'];
                        $bf_g_money = $value['g_money'];
                        $af_cash = $now_cash + $set_money;
                        $af_point = $now_point + $point;
                        if ("reg_first_charge" == $value['set_type']) {
                            $p_data['sql'] = "update member set reg_first_charge = 0, charge_first_per = 0, money = money + $set_money , point = point + $point,g_money = g_money - $g_money where idx = $member_idx ";
                        } else if ("charge_first_per" == $value['set_type']) {
                            $p_data['sql'] = "update member set charge_first_per = 0 , money = money + $set_money , point = point + $point,g_money = g_money - $g_money where idx = $member_idx ";
                        } else {
                            $p_data['sql'] = "update member set money = money + $set_money , point = point + $point,g_money = g_money - $g_money where idx = $member_idx ";
                        }

                        //$CASHAdminDAO->setQueryData($p_data);
                        if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                        }

                        $a_comment = "충전 취소";
                        $ac_code = 113;
                        $p_data['sql'] = "insert into  t_log_cash ";
                        $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                        $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx,  " . $set_money . "  ";
                        $p_data['sql'] .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                        //$CASHAdminDAO->setQueryData($p_data);
                        if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                        }

                        if ($point < 0) {
                            $ac_code = 124;
                            $a_comment = '충전취소로 인한 관리자 포인트 회수 : ' . $value['set_type'];
                            $p_data['sql'] = "insert into  t_log_cash ";
                            $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                            $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx,  " . $point . "  ";
                            $p_data['sql'] .= ", $now_point, $af_point, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                            //$CASHAdminDAO->setQueryData($p_data);
                            if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data)) {
                                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                            }
                        }

                        $gmPt = null;
                        if ('KWIN' == SERVER) {
                            $gmPt = new KwinGmPt();
                        } else if ('GAMBLE' == SERVER) {
                            $gmPt = new GambelGmPt();
                        } else if ('CHOSUN' == SERVER) {
                            $gmPt = new ChoSunGmPt();
                        } else if ('BETS' == SERVER) {
                            $gmPt = new BetsGmPt();
                        } else {
                            throw new Exception('fail GamblePatch !!!');
                        }

                        if (0 < $g_money) {
                            $gmPt->insertLog('',$member_idx, AC_GM_ROLLBACK_RE_CALL_CHARGE,$idx,$g_money, $bf_g_money, $bf_g_money - $g_money, 'M', '충전취소로 g_money 회수', 0, 0, $CASHAdminDAO);
                        }
                        // $set_money 값이 -이라서 -를 붙여준다.
                        // 만약 취소한 금액이 최대입금액이면 이전 최대값으로 롤백해줘야 한다.
                        $p_data_ch['sql'] = "SELECT max_charge, bf_max_charge FROM total_member_cash WHERE member_idx = $member_idx";
                        $max_charge_arr = $CASHAdminDAO->getQueryData($p_data_ch);
                        if (true === isset($max_charge_arr) && 0 < count($max_charge_arr)) {
                            $max_charge = $max_charge_arr[0]['max_charge'];
                            $bf_max_charge = $max_charge_arr[0]['bf_max_charge'];
                            if ($max_charge == $value['set_money']) {
                                $p_data_up_ch['sql'] = "UPDATE total_member_cash set max_charge = $bf_max_charge WHERE member_idx = $member_idx";
                                //$CASHAdminDAO->setQueryData($p_data_up_ch);
                                if (FAIL_DB_SQL_EXCEPTION === $CASHAdminDAO->setQueryData($p_data_up_ch)) {
                                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                                }
                            }
                        }
                        
                       UserPayBack::AddCharge($member_idx,$set_money,$CASHAdminDAO);
                         
                    }
                }
                break;
        }
    } catch (\mysqli_sql_exception $e) {
        $UTIL->logWrite('[MYSQL EXCEPTION] mysqli_sql_exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "db_error");
        $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
        $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
    } catch (\Exception $e) {
        $UTIL->logWrite('::::::::::::::: set_charge_money Exception : ' . $e->getMessage(), "error");
        $result['retCode'] = -3;
        $result['retMsg'] = "Exception error";
    } catch (\ReflectionException $e) {
        $UTIL->logWrite('::::::::::::::: set_charge_money ReflectionException : ' . $e->getMessage(), "error");
        $result['retCode'] = -4;
        $result['retMsg'] = "ReflectionException error";
    }
} while (0);

if ($result['retCode'] > 0) {
    $CASHAdminDAO->commit();
} else {
    $CASHAdminDAO->rollback();
}
$CASHAdminDAO->dbclose();
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
