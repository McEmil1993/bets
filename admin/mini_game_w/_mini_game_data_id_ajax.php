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

$id = $_POST['id'];
$game = $_POST['game'];


if ($game == 'powerball') {
    $bet_type = 15;
} else if ($game == 'eospb5') {
    $bet_type = 3;
} else if ($game == 'pladder') {
    $bet_type = 4;
} else if ($game == 'kladder') {
    $bet_type = 5;
} else if ($game == 'b_soccer') {
    $bet_type = 6;
} else {
  die();
}

if ($db_conn) {
    $p_data['sql'] = "SELECT result, result_score FROM mini_game where bet_type = $bet_type and id = $id";
    $data = $LSportsAdminDAO->getQueryData($p_data);
    $LSportsAdminDAO->dbclose();

    $result["retCode"] = 1000;
    $result["retData"] = $data[0];

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>
