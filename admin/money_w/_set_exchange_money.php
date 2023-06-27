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
include_once(_LIBPATH . '/class_UserPayBack.php');
$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$result["retCode"] = 2001;
$result["retData"] = '';

$p_data['mtype'] = trim(isset($_POST['mtype']) ? $_POST['mtype'] : '');
//$p_data['mkind'] = trim(isset($_POST['mkind']) ? $_POST['mkind'] : '');
$p_data['chkval'] = trim(isset($_POST['chkval']) ? $_POST['chkval'] : '');

if ($p_data['chkval'] != '') {
    //$money_idx_arr = explode(',', $p_data['chkval']);
    $chkval = $p_data['chkval'];
}


$cash_use_kind = "P";

// 1: 입금 전 (신청) 2: 입금 확인 (대기) 3: 충전 완료 (완료) 4: 취소 10: 배팅적중 11:관리자 취소  \\\\n999: 오류
//신청 - 대기,승인,취소 가능
//대기 - 취소,승인 가능
//승인 - 변경 불가능
//취소 - 변경 불가능

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

if ($db_conn) {


    switch ($p_data['mtype']) {
        case "2": // 1 환전 요청 일때만 가능
            $result["retCode"] = 3000;
            $result["retData"] = '';

            $p_data['sql'] = "select b.idx,b.member_idx, a.level, a.money, b.money as set_money,a.point from member a, member_money_exchange_history b ";
            $p_data['sql'] .= " where a.idx=b.member_idx and b.status = 1 ";
            $result_data = $CASHAdminDAO->getQueryData($p_data);

            $a_comment = "환전 요청 대기";
            $ac_code = 205;

            foreach ($result_data as $value) {
                $idx = $value['idx'];
                $p_data['sql'] = "update member_money_exchange_history set status = 2, update_dt = now() where idx = $idx ";
                $CASHAdminDAO->setQueryData($p_data);
                $member_idx = $value['member_idx'];
                $now_cash = $value['money'];
                $af_cash = $value['money'];
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx, 0 ";
                $p_data['sql'] .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                $CASHAdminDAO->setQueryData($p_data);
            }

            break;
        case "3": // 1 : 신청, 2 : 대기 상태에서 이루어진다.


            $result["retCode"] = 1000;
            $result["retData"] = '';

            $a_comment = "환전완료 ";
            $ac_code = 2;
            $cash_use_kind = 'M';

            $p_data['sql'] = "select b.idx,b.member_idx, a.level, a.money, b.money as set_money,a.point,b.create_dt from member a, member_money_exchange_history b ";
            $p_data['sql'] .= " where a.idx=b.member_idx and b.idx in ($chkval) and b.status in (1,2) ";
            $result_data = $CASHAdminDAO->getQueryData($p_data);
            foreach ($result_data as $value) {

                $idx = $value['idx'];
                $member_idx = $value['member_idx'];
                $now_cash = $value['money'];
                $set_money = $value['set_money'];
                $create_dt = $value['create_dt'];
                $af_cash = $now_cash;
                $bf_cash = $now_cash + $set_money;
                if (IMAGE_PATH != 'asbet') {
                    $p_data['sql'] = "update member set is_exchange = 1 where idx =" . $member_idx;
                    $CASHAdminDAO->setQueryData($p_data);
                }
                $p_data['sql'] = "update member_money_exchange_history set status = 3, update_dt = now(), result_money = $af_cash  where idx= $idx";
                $CASHAdminDAO->setQueryData($p_data);
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx, " . -1 * $set_money . " ";
                $p_data['sql'] .= ", $bf_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";
                $CASHAdminDAO->setQueryData($p_data);

                // 출금성공한 금액이 최대 입금금액 보다 많으면 데이터를 갱신해준다.
                $p_data_ex['sql'] = "SELECT max_exchange FROM total_member_cash WHERE member_idx = $member_idx";
                $max_exchange_arr = $CASHAdminDAO->getQueryData($p_data_ex);
                if (true === isset($max_exchange_arr) && 0 < count($max_exchange_arr)) {
                    $max_exchange = $max_exchange_arr[0]['max_exchange'];
                    if ($max_exchange < $set_money) {
                        $p_data_up_ex['sql'] = "UPDATE total_member_cash set bf_max_exchange = $max_exchange, max_exchange = $set_money WHERE member_idx = $member_idx";
                        $CASHAdminDAO->setQueryData($p_data_up_ex);
                    }
                }
            }

            break;

        case "11": // 1,2 번만 취소 처리를 한다.
            $result["retCode"] = 1000;
            $result["retData"] = '';

            $a_comment = "환전요청취소 ";
            $ac_code = 112;

            $p_data['sql'] = "select b.idx,b.member_idx, a.level, a.money, b.money as set_money,a.point,b.create_dt from member a, member_money_exchange_history b ";
            $p_data['sql'] .= " where a.idx=b.member_idx and b.idx in ($chkval) and b.status in (1,2) ";
            $result_data = $CASHAdminDAO->getQueryData($p_data);
            foreach ($result_data as $value) {
                $idx = $value['idx'];
                $member_idx = $value['member_idx'];
                $now_cash = $value['money'];
                $set_money = $value['set_money'];
                $af_cash = $now_cash + $set_money;


                $af_cash = $now_cash + $set_money;
                $p_data['sql'] = "update member set money = money + $set_money  where idx= $member_idx ";
                $CASHAdminDAO->setQueryData($p_data);

                $p_data['sql'] = "update member_money_exchange_history set status = 11  where idx=$idx";
                $CASHAdminDAO->setQueryData($p_data);

                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(" . $member_idx . ", $ac_code, $idx, " . $set_money . " ";
                $p_data['sql'] .= ", $now_cash, $af_cash, '" . strtoupper($cash_use_kind) . "','$a_comment','$admin_id')";

                $CASHAdminDAO->setQueryData($p_data);
                
                UserPayBack::AddExchange($member_idx,-$set_money,$CASHAdminDAO);
            }
            break;
    }

    $CASHAdminDAO->dbclose();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
