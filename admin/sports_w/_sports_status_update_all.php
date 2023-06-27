<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');
include_once(_LIBPATH . '/class_Code.php');

try {
    if (!isset($_SESSION)) {
        session_start();
    }
    $UTIL = new CommonUtil();

    $LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);

    $db_conn = $LSportsAdminDAO->dbconnect();

    $result["retCode"] = SUCCESS;
    $is_trans_start = false;
    if (!$db_conn) {
        $UTIL->logWrite("[fail db_conn _sports_status_update_all] ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }
//트랜잭션
    $is_trans_start = $LSportsAdminDAO->trans_start();
    if (false == $is_trans_start) {
        // start transaction error
        CommonUtil::logWrite("fail trans_start _sports_status_update_all", "error");
        $result['retCode'] = FAIL_TRANS_START;
        $result['retMsg'] = FAIL_TRANS_START_MSG;
        return;
    }


    $strFixturesData = $_POST['strFixturesData'];
    $strFixturesStartDate = $_POST['strFixturesStartDate'];
    $status = $LSportsAdminDAO->real_escape_string($_POST['status']);
    $bet_type = $LSportsAdminDAO->real_escape_string($_POST['bet_type']);

    $arrFixturesData = json_decode($strFixturesData, true);
    $arrFixturesStartDate = json_decode($strFixturesStartDate, true);

    $sql = "";
    foreach ($arrFixturesData as $key => $fix_id) {
        // 정산완료로 돌릴때
        //$fix_id = $LSportsAdminDAO->real_escape_string($fix_id);

        if (2 == $status) {
            if ($bet_type == 1) {
                // 업데이트 파라미터
              
                $sql = "update lsports_bet 
                        set bet_status = $status,
                        bet_price_hit = 0.00,
                        admin_bet_status = 'OFF'
                        where fixture_id = $fix_id 
                        AND bet_type = $bet_type;";

                //$LSportsAdminDAO->executeQuery($sql);

                if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->executeQuery($sql)) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
                // 롤백함수
                $arrReResult = $LSportsAdminDAO->getBetTotalReCalculate($fix_id, $bet_type, 0);
                if (FAIL_DB_SQL_EXCEPTION === $arrReResult) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
                //CommonUtil::logWrite('getBetTotalReCalculate : fix_id' . $fix_id, "info");

                if (true == isset($arrReResult) && count($arrReResult)) {
                    foreach ($arrReResult as $re_value) {
                        // detail 정보 bet_status 값 1,result_score = null 
                        // mb_bet bet_status = 1 ,take_money = 0,take_point = 0,recom_take_point = 0
                        // 낙첨,적중에 따라 금액 롤백 
                        //CommonUtil::logWrite("doRollbackCalculate Start : " . json_encode($re_value), "info");

                        $re_value['admin_id'] = $_SESSION['aid'];
                        GameCode::doRollbackCalculate($LSportsAdminDAO, $UTIL, $re_value, 1);
                    }
                }
            }
            if ($bet_type == 2) {
                // 업데이트 파라미터
              
                $sql = "update lsports_bet 
                        set bet_status = $status,
                        admin_bet_status = 'ON'
                        where fixture_id = $fix_id 
                        AND bet_type = $bet_type ;";

                //$LSportsAdminDAO->executeQuery($sql);
                if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->executeQuery($sql)) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
            }
        } else if (3 == $status) {
        
            $sql = "update lsports_bet set bet_status = $status,
                admin_bet_status = 'OFF' 
                where fixture_id = $fix_id 
                AND bet_type = $bet_type;";

            //$LSportsAdminDAO->executeQuery($sql);
            if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->executeQuery($sql)) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
        }
    }

    $now_ip = CommonUtil::get_client_ip();
    $admin_id = $_SESSION['aid'];
    $log_data = '경기 상태값 변경 : time=>' . $strFixturesStartDate . ' fixture=>' . $strFixturesData . ' bet_type=>' . $bet_type . ' $status=>' . $status;
    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $LSportsAdminDAO->getQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $retData) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
    $st_country = $retData[0]['a_country'];

    $p_data['sql'] = "insert into  t_adm_log ";
    $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
    $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country','$log_data',27) ;";
    //$LSportsAdminDAO->setQueryData($p_data);
    if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('fail _sports_status_update_all ' . $e->getMessage(), "db_error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    $UTIL->logWrite(" fail _sports_status_update_all " . $e->getMessage(), "error");

    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {
    if (true == $is_trans_start) {
        if (0 < $result['retCode']) {
            $LSportsAdminDAO->commit();
        } else {
            $LSportsAdminDAO->rollback();
        }
    }

    if ($db_conn) {
        $LSportsAdminDAO->dbclose();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}


