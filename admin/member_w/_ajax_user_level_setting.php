<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$type = isset($_POST['type']) ? $_POST['type'] : NULL;  // 0 : 1

if (false === isset($type)) {
    $result["retCode"] = -1; // 파라미터 오류 
    $result['retMsg'] = '파라미터 오류 ';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    return;
}

if (0 == $type) {
    $nonDepositDatePeriod = isset($_POST['nonDepositDatePeriod']) ? $_POST['nonDepositDatePeriod'] : NULL;
    
    
    if (false === isset($nonDepositDatePeriod)) {
        $result["retCode"] = -2; // 파라미터 오류 
        $result['retMsg'] = '파라미터 오류';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
} else {
    $level = isset($_POST['level']) ? $_POST['level'] : NULL;
    $charge = isset($_POST['charge']) ? $_POST['charge'] : NULL;
    $exchange = isset($_POST['exchange']) ? $_POST['exchange'] : NULL;
    $calcurate = isset($_POST['calcurate']) ? $_POST['calcurate'] : NULL;
    $charge_count = isset($_POST['charge_count']) ? $_POST['charge_count'] : NULL;
    if (false === isset($level) || false === isset($charge) || false === isset($exchange) || false === isset($calcurate) || false === isset($charge_count)) {
        $result["retCode"] = -3; // 파라미터 오류 
        $result['retMsg'] = '파라미터 오류';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
    
  
    
}

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {

    $type = $BdsAdminDAO->real_escape_string($type);
    $nonDepositDatePeriod = $BdsAdminDAO->real_escape_string($nonDepositDatePeriod);
    $level = $BdsAdminDAO->real_escape_string($level);
    $charge = $BdsAdminDAO->real_escape_string($charge);
    $exchange = $BdsAdminDAO->real_escape_string($exchange);
    $calcurate = $BdsAdminDAO->real_escape_string($calcurate);
    $charge_count = $BdsAdminDAO->real_escape_string($charge_count);

    
    if (0 == $type) {
        $p_data['sql'] = "update member_level_up set nonDepositDatePeriod = $nonDepositDatePeriod";
    } else {
        $p_data['sql'] = "select * from member_level_up where level = $level - 1";

        $dbResult = $BdsAdminDAO->getQueryData($p_data);
        // 아래단계 값보다 작은지 체크한다.
        if (true === isset($dbResult)) {
            /*if ($charge < $dbResult[0]['charge'] || $exchange < $dbResult[0]['exchange']  || $calcurate < $dbResult[0]['calcurate']  || $charge_count < $dbResult[0]['charge_count'] ) {
                $result["retCode"] = -4; // 하위 레벨보다 설정값이 작을 수없습니다.
                $result['retMsg'] = '하위 레벨보다 설정값이 작을 수없습니다';
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                return;
            }*/
        }

        $p_data['sql'] = "select * from member_level_up where level = $level + 1";

        $dbResult = $BdsAdminDAO->getQueryData($p_data);
        // 상위단계 값보다 작은지 체크한다.
        if (true === isset($dbResult)) {
            // 상위값에 하나라도 0보다 크면 체크를 한다.
            if ($dbResult[0]['charge'] > 0 || $dbResult[0]['exchange'] > 0 || $dbResult[0]['calcurate'] > 0 || $dbResult[0]['charge_count'] > 0) {
                /*if ($dbResult[0]['charge'] <= $charge || $dbResult[0]['exchange'] <= $exchange || $dbResult[0]['calcurate'] <= $calcurate || $dbResult[0]['charge_count'] <= $charge_count) {
                    $result["retCode"] = -5; // 상위 레벨보다 설정값이 클수 없습니다.
                    $result['retMsg'] = '상위 레벨보다 설정값이 클수 없습니다';
                    echo json_encode($result, JSON_UNESCAPED_UNICODE);
                    return;
                }*/
            }
        }


        $p_data['sql'] = "update member_level_up set charge = $charge, 
                 exchange = $exchange, 
                 calcurate = $calcurate,
                 charge_count = $charge_count 
                where level = $level";
    }

    $dbResult = $BdsAdminDAO->setQueryData($p_data);

    $BdsAdminDAO->dbclose();
    $result["retCode"] = 1000;
    $result['retMsg'] = 'success';

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>