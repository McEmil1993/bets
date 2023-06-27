<?php


header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: json; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
include_once(_LIBPATH . '/class_Code.php');
include_once(_BASEPATH . '/GamblePatch/GambelGmPt.php');
include_once(_BASEPATH . '/GamblePatch/KwinGmPt.php');
include_once(_BASEPATH . '/GamblePatch/ChoSunGmPt.php');
include_once(_BASEPATH . '/GamblePatch/BetsGmPt.php');
include_once(_BASEPATH . '/GamblePatch/NobleGmPt.php');
include_once(_BASEPATH . '/GamblePatch/BullsGmPt.php');
include_once(_LIBPATH . '/class_UserPayBack.php');
$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$ALBetDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $ALBetDAO->dbconnect();
$result['retCode'] = 1000;
//fixture_id,markets_name,bet_base_line
// 1, 2, 3, 7, 52, 101, 102 => 승무패,오버언더,핸디캡,더블찬스,승패,홈팀 오버언더,원정팀 오버언더

if (!$db_conn) {
    $UTIL->logWrite("[_sports_menu_bet_cancel_ajax] [error 2200]", "error");
    //$UTIL->checkFailType('2200', '', '', 'json');
    return;
}

if (false == isset($_POST['idx'])) {
    $UTIL->logWrite("[_sports_menu_bet_cancel_ajax] [error 2201]", "error");
    $ALBetDAO->dbclose();
    //$UTIL->checkFailType('-2', '', '', 'json');
    return;
}

if (!$ALBetDAO->trans_start()) {
    // start transaction error
    $UTIL->logWrite("[_sports_menu_bet_cancel_ajax] [error -10]", "error");
    $ALBetDAO->dbclose();
    //$UTIL->checkFailType('-10', '', '', 'json');
    return;
}

$idx = $_POST['idx'];
try {

    $idx = $ALBetDAO->real_escape_string($idx);

    $arrMbBtResult = $ALBetDAO->SelectMemberBet($idx);
    if (FAIL_DB_SQL_EXCEPTION === $arrMbBtResult) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    if (0 == count($arrMbBtResult) || 1 != $arrMbBtResult[0]['bet_status']) {
        $UTIL->logWrite("[_sports_menu_bet_cancel_ajax] [error -2]", "error");
        throw new Exception('fail _sports_menu_bet_cancel_ajax empty or error status ');
    }

    //$UTIL->logWrite("[SelectMemberBet] ", "info");

    $take_money = 1 * $arrMbBtResult[0]['total_bet_money'];
    if ($take_money > 0) {
        $p_data['sql'] = "update member set money = money + $take_money where idx = " . $arrMbBtResult[0]['member_idx'];
        //$ALBetDAO->setQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        
        
    }

    $array_fixture = [];
    $member_idx = $arrMbBtResult[0]['member_idx'];
    $bet_type = 0;
    $currentDate = date("Y-m-d H:i:s");
    foreach ($arrMbBtResult as $value) {

        if (true == in_array($value['ls_fixture_id'], $array_fixture)) {
            continue;
        }

        $ls_fixture_id = $value['ls_fixture_id'];
        $array_fixture[] = $ls_fixture_id;
        $bet_type = $value['bet_type'];
        $p_data['sql'] = "update fixtures_bet_sum set sum_bet_money = sum_bet_money - $take_money where member_idx = $member_idx AND fixture_id = $ls_fixture_id AND bet_type = $bet_type";
        //$ALBetDAO->setQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    }

    //$ALBetDAO->UpdateMemberBetDetailByMb_bt_idx($idx, 5);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateMemberBetDetailByMb_bt_idx($idx, 5)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    //$ALBetDAO->UpdateMemberBet($currentDate, $idx, 5, $take_money,0);
    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateMemberBet($currentDate, $idx, 5, $take_money, 0,'M')) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $ukey = md5($arrMbBtResult[0]['member_idx'] . strtotime('now'));

    $a_comment = '';
    if (1 == $bet_type) {
        $a_comment = 'prematch ==>';
    } else if (2 == $bet_type) {
        $a_comment = 'inplay ==>';
    }

    $a_comment .= "관리자 배팅 취소";
    $a_comment = addslashes($a_comment);
    $UTIL->log_cash($ALBetDAO, $ukey, $arrMbBtResult[0]['member_idx'], 4, $idx, $take_money, $arrMbBtResult[0]['money'], $admin_id, $a_comment, 'P');

    $type = '';
    if (1 == $arrMbBtResult[0]['bet_type'] && 'S' == $arrMbBtResult[0]['folder_type']) {
        $type = 'SPORTS_S';
    } else if (1 == $arrMbBtResult[0]['bet_type'] && 'D' == $arrMbBtResult[0]['folder_type']) {
        $type = 'SPORTS_D';
    } else if (2 == $arrMbBtResult[0]['bet_type'] && 'S' == $arrMbBtResult[0]['folder_type']) {
        $type = 'REAL_S';
    } else if (2 == $arrMbBtResult[0]['bet_type'] && 'D' == $arrMbBtResult[0]['folder_type']) {
        $type = 'REAL_D';
    }

    GameCode::decUpdateChargeBetMoney($arrMbBtResult[0]['create_dt'], $arrMbBtResult[0]['member_idx'], $type, $arrMbBtResult[0]['total_bet_money'], $ALBetDAO);
    UserPayBack::AddBetting($arrMbBtResult[0]['member_idx'],-$take_money,$ALBetDAO);
    // 아이템을 사용했다면 아이템도 사용 취소를 해줘야 한다.
    $gmPt = null;
    if ('KWIN' == SERVER) {
        $gmPt = new KwinGmPt();
    } else if ('GAMBLE' == SERVER) {
        $gmPt = new GambelGmPt();
    } else if ('CHOSUN' == SERVER) {
	$gmPt = new ChoSunGmPt();
    } else if ('BETS' == SERVER) {
        $gmPt = new BetsGmPt();
    } else if ('NOBLE' == SERVER) {
        $gmPt = new NobleGmPt();
    }else if ('BULLS' == SERVER) {
        $gmPt = new BullsGmPt();
    } else {
        throw new Exception('fail GamblePatch !!!');
    }
    $item_idx = $arrMbBtResult[0]['item_idx'];
    list($retval, $error) = $gmPt->cancelItemUse($member_idx, $item_idx, $idx, $ALBetDAO);
    if (false == $retval) {
      throw new Exception('fail cancelItemUse error : '.$error);
    }

    $ALBetDAO->commit();
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('sports_menu_bet_cancel_ajax to 2' . $e->getMessage(), "db_error");
    $ALBetDAO->rollback();
    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("[sports_menu_bet_cancel_ajax] [error -2]", "error");
    $ALBetDAO->rollback();
    $result['retCode'] = -3;
    $result['retMsg'] = 'Exception 예외발생';
} finally {
    $ALBetDAO->dbclose();
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>