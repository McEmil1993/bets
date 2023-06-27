<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$bet_type = $_POST['bet_type'];
$sports_list = $_POST['sports_list'];
$main_book_maker = $_POST['main_book_maker'];
$sub_book_maker = $_POST['sub_book_maker'];

if($db_conn) {
    $bet_type = $LSportsAdminDAO->real_escape_string($bet_type);
    $sports_list = $LSportsAdminDAO->real_escape_string($sports_list);
    $main_book_maker = $LSportsAdminDAO->real_escape_string($main_book_maker);
    $sub_book_maker = $LSportsAdminDAO->real_escape_string($sub_book_maker);
    
    $sql = "update lsports_markets set main_book_maker = $main_book_maker, sub_book_maker = $sub_book_maker where bet_group = $bet_type and sport_id = $sports_list";
    $LSportsAdminDAO->executeQuery($sql);
    $LSportsAdminDAO->dbclose();
	
    $result["retCode"]	= 1000;
    $result['retMsg'] = 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>