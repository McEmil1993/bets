<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Cash_dao.php');

include_once(_LIBPATH . '/class_Code.php');
$UTIL = new CommonUtil();

include_once(_BASEPATH . '/common/login_check.php');

if (!isset($_SESSION)) {
    session_start();
}

$CASHAdminDAO = new Admin_Cash_DAO(_DB_NAME_WEB);
$db_conn = $CASHAdminDAO->dbconnect();

$admin_id = $_SESSION['aid'];
$bet_idx = $_POST['bet_idx'];

if ($db_conn) {
    $p_data['sql'] = "SELECT b.bet_status, 
                            b.bet_type, 
                            b.total_bet_money,
                            m.result,
                            a.idx AS m_idx,
                            a.money AS money,
                            a.id,
                            a.nick_name,
                            b.create_dt
                        FROM mini_game_member_bet b
                        LEFT JOIN mini_game m
                        ON m.id = b.ls_fixture_id
                        LEFT JOIN member a
                        ON a.idx = b.member_idx
                        where b.idx = $bet_idx;";
    $resultData = $CASHAdminDAO->getQueryData($p_data)[0];
    $m_idx = $resultData['m_idx'];
    $db_id = $resultData['id'];
    $db_nick_name = $resultData['nick_name'];
    $total_bet_money = $resultData['total_bet_money'];
    $gameResult = json_decode($resultData['result'], true);

    $check = true;
    if ($resultData['bet_type'] == 3) {
        if ($gameResult['num1'] > 0) {
            $check = false;
        }
    } else if ($resultData['bet_type'] == 4) {
        if ($gameResult['oe'] != '') {
            $check = false;
        }
    } else if ($resultData['bet_type'] == 5) {
        if ($gameResult['oe'] != '') {
            $check = false;
        }
    } else if ($resultData['bet_type'] == 6) {
        if ($gameResult['res'] != 'None') {
            $check = false;
        }
    }

    // 결과가 나왔다.
    if (!$check) {
        $result["retCode"] = 1001;
        $result['retMsg'] = '이미 결과가 나왔습니다.';
        $CASHAdminDAO->dbclose();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }

    // 정산처리됨
    if ($resultData['bet_status'] != 1) {
        $result["retCode"] = 1002;
        $result['retMsg'] = '취소 할 수 없는 상태입니다';
        $CASHAdminDAO->dbclose();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }

    // 종료 10초전이다.
    /* $currentDate = date("Y-m-d H:i:s");
      $checkDate = date("Y-m-d H:i:s", strtotime($gameResult['edate'])-10);
      if($currentDate > $checkDate){
      $result["retCode"]	= 1003;
      $result['retMsg']	= '취소할 수 있는 시간이 지났습니다.';
      $CASHAdminDAO->dbclose();
      echo json_encode($result,JSON_UNESCAPED_UNICODE);
      return;
      } */

    $p_data['sql'] = "update mini_game_member_bet set bet_price = 1.0, bet_status = 5, take_money = $total_bet_money where idx = $bet_idx;";
    $CASHAdminDAO->setQueryData($p_data);

    // 환수 조치
    $p_data['sql'] = "update member set money = money + $total_bet_money where idx = $m_idx;";
    $CASHAdminDAO->setQueryData($p_data);

    // 머니 내역에 미니게임 추가해야됨
    $ukey = md5($m_idx . strtotime('now'));
    $a_comment = "관리자 미니게임 배팅 취소";
    $a_comment = addslashes($a_comment);
    $UTIL->log_cash($CASHAdminDAO, $ukey, $m_idx, 4, $bet_idx, $total_bet_money, $resultData['money'], $admin_id, $a_comment, 'P');
  
    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $log_data = "[미니게임 베팅취소] 베팅번호=>$bet_idx  베팅금=>".$resultData['money'];
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $CASHAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',55) ;";
    $CASHAdminDAO->setQueryData($p_data);
            
    GameCode::decUpdateChargeBetMoney($resultData['create_dt'], $m_idx, 'MINI', $total_bet_money, $CASHAdminDAO);
    $CASHAdminDAO->dbclose();

    $result["retCode"] = 1000;
    $result['retMsg'] = 'success';

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
