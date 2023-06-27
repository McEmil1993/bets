<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$member_idx = isset($_POST['member_idx']) ? $_POST['member_idx'] : 0;
$account_number = trim(isset($_POST['account_number']) ? $_POST['account_number'] : '');
$account_name = trim(isset($_POST['account_name']) ? $_POST['account_name'] : '');
$account_bank = trim(isset($_POST['account_bank']) ? $_POST['account_bank'] : '');

$type = isset($_POST['type']) ? $_POST['type'] : 0;

$result["retCode"] = 1000;
$result["retData"] = '';

if(false === isset($member_idx)|| false === isset($account_number) || false === isset($account_name) || false === isset($account_bank)){
    $result["retCode"] = -1000; // 파라미터 버그 
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    return;
}

if(0 === $type || 2 < $type || 0 === $member_idx){
    $result["retCode"] = -1001; // 데이터 버그 
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    return;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    $member_idx = $MEMAdminDAO->real_escape_string($member_idx);
    $account_number = $MEMAdminDAO->real_escape_string($account_number);
    $account_name = $MEMAdminDAO->real_escape_string($account_name);
    $account_bank = $MEMAdminDAO->real_escape_string($account_bank);
    
    $p_data['sql'] = "SELECT bank_id FROM account where account_name = '$account_bank' ";
    $result_account_code = $MEMAdminDAO->getQueryData($p_data);
    if(false === isset($result_account_code) || 0 === count($result_account_code)){
        
        $result["retCode"] = -1001; // 
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
    if (1 == $type) {
        $p_data['sql'] = 'INSERT INTO `personal_account_number` ('
                . 'member_idx, '
                . 'account_number_1, '
                . 'account_name_1, '
                . 'account_bank_1
                        ) VALUES ('.$member_idx.',"' . $account_number . '","' . $account_name . '","' . $account_bank . '")'
                . ' ON DUPLICATE KEY UPDATE '
                . 'account_number_1 = VALUES(account_number_1), '
                . 'account_name_1 = VALUES(account_name_1), '
                . 'account_bank_1 = VALUES(account_bank_1)';
    } else {
       $p_data['sql'] = 'INSERT INTO `personal_account_number` ('
                . 'member_idx, '
                . 'account_number_2, '
                . 'account_name_2, '
                . 'account_bank_2
                        ) VALUES ('.$member_idx.',"' . $account_number . '","' . $account_name . '","' . $account_bank . '")'
                . ' ON DUPLICATE KEY UPDATE '
                . 'account_number_2 = VALUES(account_number_2), '
                . 'account_name_2 = VALUES(account_name_2), '
                . 'account_bank_2 = VALUES(account_bank_2)';
    }

    $MEMAdminDAO->setQueryData($p_data);
    $result["retCode"] = 1000;

    $MEMAdminDAO->dbclose();
}


echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
