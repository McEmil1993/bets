<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if (!$db_conn) {
    $UTIL->logWrite("[_betting_prc_update] [error 2200]", "error");
    $UTIL->checkFailType('2200', '', '', 'json');
    return;
}


$idx = $BdsAdminDAO->real_escape_string($_POST['idx']);
$result_process = $BdsAdminDAO->real_escape_string($_POST['result_process']);
$end_time = $BdsAdminDAO->real_escape_string($_POST['end_time']);
$max_dividend = $BdsAdminDAO->real_escape_string($_POST['max_dividend']);
$betting_regulation = $BdsAdminDAO->real_escape_string($_POST['betting_regulation']);


$p_data['sql'] = "
        UPDATE 
                base_rule 
        SET 
                result_process = '$result_process', 
                end_time = $end_time, 
                max_dividend = $max_dividend,
                betting_regulation = '$betting_regulation' 
        WHERE 
                idx = $idx
        ";


$r = $BdsAdminDAO->setQueryData($p_data);

$BdsAdminDAO->dbclose();
$result["retCode"] = 1000;
$result['retMsg'] = $p_data['sql'];

echo json_encode($result, JSON_UNESCAPED_UNICODE);

// }
?>
