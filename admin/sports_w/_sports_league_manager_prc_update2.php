<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$rank = $_POST['rank'];
$pre_amount = $_POST['pre_amount'];
$real_amount = $_POST['real_amount'];

if ($db_conn) {
    $rank = $LSportsAdminDAO->real_escape_string($rank);
    $pre_amount = $LSportsAdminDAO->real_escape_string($pre_amount);
    $real_amount = $LSportsAdminDAO->real_escape_string($real_amount);

    $sql = "UPDATE dividend_policy SET amount = $pre_amount, update_dt = NOW() WHERE rank = $rank AND `type` = 1";
    $LSportsAdminDAO->executeQuery($sql);

    $sql = "UPDATE dividend_policy SET amount = $real_amount, update_dt = NOW() WHERE rank = $rank AND `type` = 2";
    $LSportsAdminDAO->executeQuery($sql);

    $LSportsAdminDAO->dbclose();

    $result["retCode"] = 1000;
    // $result['retMsg']	= $p_data['sql'];
    $result['retMsg'] = 'success';

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
