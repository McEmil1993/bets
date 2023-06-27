<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Mini_Game_dao.php');
include_once(_LIBPATH . '/class_Code.php');

$p_data['second_pass'] = trim(isset($_POST['second_pass']) ? $_POST['second_pass'] : '');

if (!isset($_SESSION)) {
    session_start();
}

$UTIL = new CommonUtil();

$AdminMiniGameDAO = new Admin_Mini_Game_DAO(_DB_NAME_WEB);
$db_conn = $AdminMiniGameDAO->dbconnect();

if (!$db_conn) {
    $UTIL->logWrite("[_mini_game_calMiniGame] [error 2200]", "error");
    //$UTIL->checkFailType('-1000', '', '디비연결실패', 'json');
    return;
}

//2차인증 체크
$p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
$second_pass = $AdminMiniGameDAO->getQueryData($p_data)[0];
if (hash('sha512', $p_data['second_pass']) != $second_pass['set_type_val']) {
    $result['retCode'] = 2002;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    return;
}

if (!$AdminMiniGameDAO->trans_start()) {
    // start transaction error
    $UTIL->logWrite("[_mini_game_calMiniGame] [error -10]", "error");
    //$UTIL->checkFailType('-10001', '', '트랜잭션실패', 'json');
    $AdminMiniGameDAO->dbclose();
    return;
}

try {
    $round = $_POST['round'];
    $miniGameData = $_POST['miniGameData'];
    $admin_id = $_SESSION['aid'];

    $miniGameData = json_decode($miniGameData, true);
    $p_data['sql'] = "SELECT bet_type, result, admin_bet_status FROM mini_game where id = $round";

    $resultData = $AdminMiniGameDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $resultData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $resultData = $resultData[0];
    //$resultData = $AdminMiniGameDAO->getQueryData($p_data)[0];
    $gameResult = json_decode($resultData['result'], true);
    //print_r($gameResult);
    $check = true;
    if ($resultData['bet_type'] == 3) {
        /* if($gameResult['num1'] == 0){
          $check = false;
          } */

        $gameResult['pb'] = $miniGameData[0]['pb'];
        $gameResult['num1'] = $miniGameData[0]['num1'];
        $gameResult['num2'] = $miniGameData[0]['num2'];
        $gameResult['num3'] = $miniGameData[0]['num3'];
        $gameResult['num4'] = $miniGameData[0]['num4'];
        $gameResult['num5'] = $miniGameData[0]['num5'];
    } else if ($resultData['bet_type'] == 4) {
        if ($gameResult['oe'] == '') {
            $check = false;
        }

        $gameResult['oe'] = $miniGameData[0]['oe'];
        $gameResult['line'] = $miniGameData[0]['line'];
        $gameResult['start'] = $miniGameData[0]['start'];
    } else if ($resultData['bet_type'] == 5) {
        if ($gameResult['oe'] == '') {
            $check = false;
        }

        $gameResult['oe'] = $miniGameData[0]['oe'];
        $gameResult['line'] = $miniGameData[0]['line'];
        $gameResult['start'] = $miniGameData[0]['start'];
    } else if ($resultData['bet_type'] == 6) {
        $gameResult['scoreh'] = $miniGameData[0]['home_score'];
        $gameResult['scorea'] = $miniGameData[0]['away_score'];
        /* if($gameResult['res'] == 'None'){
          $check = false;
          } */

        // 승무패
        if ($gameResult['type'] == '1x2') {
            if ($gameResult['scoreh'] > $gameResult['scorea']) {
                $gameResult['res'] = "Win";
            } else if ($gameResult['scoreh'] < $gameResult['scorea']) {
                $gameResult['res'] = "Lose";
            } else {
                $gameResult['res'] = "Draw";
            }
        } else {
            if ($gameResult['draw'] < ($gameResult['scoreh'] + $gameResult['scorea'])) {
                $gameResult['res'] = "Over";
            } else {
                $gameResult['res'] = "Under";
            }
        }
        $gameResult['sts'] = "End";
    }

    if ($resultData['admin_bet_status'] == 'ON') {
        $result["retCode"] = 1001;
        $result['retMsg'] = '해당경기 OFF로 해주세요.';
        $AdminMiniGameDAO->rollback();
        $AdminMiniGameDAO->dbclose();
        //echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!$check) {
        $result["retCode"] = 1001;
        $result['retMsg'] = '결과가 나오기 전입니다.';
        $AdminMiniGameDAO->rollback();
        $AdminMiniGameDAO->dbclose();
        //echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }


    $userMoney = null;  // 유저별 현재머니
    $userPoint = null;  // 유저별 현재포인트
    $arrReResult = $AdminMiniGameDAO->getMiniGamelReCalculate($round, $resultData['bet_type']);
    if(FAIL_DB_SQL_EXCEPTION === $arrReResult){
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    if (true == isset($arrReResult) && count($arrReResult)) {
        foreach ($arrReResult as $re_value) {
            if (isset($userMoney[$re_value['member_idx']])) {
                $re_value['money'] = $userMoney[$re_value['member_idx']];
                $re_value['point'] = $userPoint[$re_value['member_idx']];
            } else {
                $userMoney[$re_value['member_idx']] = $re_value['money'];
                $userPoint[$re_value['member_idx']] = $re_value['point'];
            }
            // detail 정보 bet_status 값 1,result_score = null 
            // mb_bet bet_status = 1 ,take_money = 0,take_point = 0,recom_take_point = 0
            // 낙첨,적중에 따라 금액 롤백 
            GameCode::doRollbackMiniGame($AdminMiniGameDAO, $UTIL, $re_value, $admin_id);

            if (0 < $re_value['take_money']) {
                $userMoney[$re_value['member_idx']] -= $re_value['take_money'];
            }

            if (0 < $re_value['take_point']) {
                $userPoint[$re_value['member_idx']] -= $re_value['take_point'];
            }
        }
    }

    if (0 < count($arrReResult)) {
        foreach ($arrReResult as $value) {
            $bet_status = 1;
            //list($bet_status, $bet_price, $value['result_p1'], $value['result_p2'], $value['result_extra']) = GameUtil::getMiniBetStatus($value, $gameResult);
            list($bet_status, $bet_price) = GameUtil::getMiniBetStatus($value, $gameResult);
            if (1 == $bet_status)
                continue;

            if (1 == $value['bet_status']) { // 미정산 처리
                $value['admin_id'] = $admin_id;
                GameCode::doReCalculateDistributor($AdminMiniGameDAO, $value, 'ADD');
            }

            $ukey = md5($value['member_idx'] . strtotime('now'));
            if ($bet_status === 4) { // 낙첨시 주는 포인트 lose_self_per,lose_recomm_per
               
                if(FAIL_DB_SQL_EXCEPTION ===  $AdminMiniGameDAO->UpdateMemberMiniGameBet($value['calculate_dt'], $value['idx'], 3, 0)){
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
            
                $a_comment = "정산 낙첨 [" . $value['game'] . "] " . $value['ls_fixture_id'] . " " . $value['ls_markets_name'] . " ";
                $a_comment = addslashes($a_comment);
                $UTIL->log_cash($AdminMiniGameDAO, $ukey, $value['member_idx'], 7, $value['idx'], 0, $value['money'], $admin_id, $a_comment, 'P');
                continue;
            }

            // 결과가 다 반영안됐다.
            // 1, 2, 3, 7, 52, 101, 102 => 승무패,오버언더,핸디캡,더블찬스,승패,홈팀 오버언더,원정팀 오버언더
            $take_money = $value['bet_price'] * $value['total_bet_money'];

            // 해당 데이터를 업데이트 한다.
             if(FAIL_DB_SQL_EXCEPTION ===  $AdminMiniGameDAO->UpdateMemberMiniGameBet($value['calculate_dt'], $value['idx'], 3, $take_money)){
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
             }

            // member 의 머니 업데이트를 해줘야 한다.
            if ($take_money > 0) {
                $sql = "update member set money = money + $take_money where idx = " . $value['member_idx'];
                if(FAIL_DB_SQL_EXCEPTION === $AdminMiniGameDAO->executeQuery($sql)){
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
                //$logger->debug($p_data['sql']);

                /* 1:충전,2:환전,3:베팅,4:베팅취소,5:포인트전환(추가),6:포인트차감,10:포인트충전,7:베팅결과처리,8:이벤트충전,9:이벤트차감,101:충전요청,102:환전요청,103:계좌조회,
                 * 111:충전요청취소,112:환전요청취소,113:충전취소,114:환전취소,121:관리자충전,122:관리자회수,123:관리자 포인트 충전, 124:관리자 포인트 회수,998:데이터복구,999:기타 
                 */
                $a_comment = "수동 개별 정산 [" . $bet_type . "] " . $value['ls_fixture_id'] . " " . $value['ls_markets_name'] . " ";

                if (6 === $value['bet_type']) {
                    $a_comment .= $gameResult['home'] . " VS " . $gameResult['away'];
                }

                $a_comment = addslashes($a_comment);
                $UTIL->log_cash($AdminMiniGameDAO, $ukey, $value['member_idx'], 7, $value['idx'], $take_money, $userMoney[$value['member_idx']], $admin_id, $a_comment, 'P');
                $userMoney[$value['member_idx']] += $take_money;
            }
        }
    }

    // 입력한 점수로 업데이트 한다.
    $gameResult = json_encode($gameResult);
    $p_data['sql'] = "update mini_game set result_score = '" . $gameResult . "' where id = $round";
    //$AdminMiniGameDAO->setQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $AdminMiniGameDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
       

    // 어드민 히스토리 로그
    $now_ip = CommonUtil::get_client_ip();
    $log_data = "[미니게임 정산] round=>$round miniGameData=>".json_encode($miniGameData);
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $AdminMiniGameDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$log_data',53) ;";
    //$AdminMiniGameDAO->setQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $AdminMiniGameDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }

    $result["retCode"] = 1000;
    $result['retMsg'] = 'success';
    
    $AdminMiniGameDAO->commit();
    
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('_mini_game_calMiniGame to 2' . $e->getMessage(), "db_error");
    $AdminMiniGameDAO->rollback();
    $result['retCode'] =  FAIL_DB_SQL_EXCEPTION;
    $result['retMsg']  =  FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite("[_mini_game_calMiniGame] [error -2]", "error");
    $AdminMiniGameDAO->rollback();
    $result['retCode'] = -3;
    $result['retMsg'] = 'Exception 예외발생';
} catch (\ReflectionException $e) {
    CommonUtil::logWrite('::::::::::::::: _mini_game_calMiniGame to 2 ReflectionException : ' . $e, "error");
    $AdminMiniGameDAO->rollback();
    $result['retCode'] = -4;
    $result['retMsg'] = 'Exception 예외발생';
} finally {
    $AdminMiniGameDAO->dbclose();
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
