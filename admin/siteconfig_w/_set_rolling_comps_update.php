<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();


if (!isset($_SESSION)) {
    session_start();
}

$BbsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BbsAdminDAO->dbconnect();

if($db_conn) {

    $idx_prematch_s_myself_bet = trim(isset($_POST['idx_prematch_s_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_prematch_s_myself_bet']) : '');
    $prematch_s_myself_bet = trim(isset($_POST['prematch_s_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_s_myself_bet']) : '');
    $prematch_s_recommender_bet = trim(isset($_POST['prematch_s_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_s_recommender_bet']) : '');
    $prematch_s_myself_lose = trim(isset($_POST['prematch_s_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_s_myself_lose']) : '');
    $prematch_s_recommender_lose = trim(isset($_POST['prematch_s_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_s_recommender_lose']) : '');

    $idx_prematch_d_myself_bet = trim(isset($_POST['idx_prematch_d_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_prematch_d_myself_bet']) : '');
    $prematch_d_myself_bet = trim(isset($_POST['prematch_d_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_d_myself_bet']) : '');
    $prematch_d_recommender_bet = trim(isset($_POST['prematch_d_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_d_recommender_bet']) : '');
    $prematch_d_myself_lose = trim(isset($_POST['prematch_d_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_d_myself_lose']) : '');
    $prematch_d_recommender_lose = trim(isset($_POST['prematch_d_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['prematch_d_recommender_lose']) : '');

    $idx_inplay_s_myself_bet = trim(isset($_POST['idx_inplay_s_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_inplay_s_myself_bet']) : '');
    $inplay_s_myself_bet = trim(isset($_POST['inplay_s_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_s_myself_bet']) : '');
    $inplay_s_recommender_bet = trim(isset($_POST['inplay_s_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_s_recommender_bet']) : '');
    $inplay_s_myself_lose = trim(isset($_POST['inplay_s_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_s_myself_lose']) : '');
    $inplay_s_recommender_lose = trim(isset($_POST['inplay_s_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_s_recommender_lose']) : '');

    $idx_inplay_d_myself_bet = trim(isset($_POST['idx_inplay_d_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_inplay_d_myself_bet']) : '');
    $inplay_d_myself_bet = trim(isset($_POST['inplay_d_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_d_myself_bet']) : '');
    $inplay_d_recommender_bet = trim(isset($_POST['inplay_d_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_d_recommender_bet']) : '');
    $inplay_d_myself_lose = trim(isset($_POST['inplay_d_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_d_myself_lose']) : '');
    $inplay_d_recommender_lose = trim(isset($_POST['inplay_d_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['inplay_d_recommender_lose']) : '');

    $idx_casino_myself_bet = trim(isset($_POST['idx_casino_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_casino_myself_bet']) : '');
    $casino_myself_bet = trim(isset($_POST['casino_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['casino_myself_bet']) : '');
    $casino_recommender_bet = trim(isset($_POST['casino_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['casino_recommender_bet']) : '');
    $casino_myself_lose = trim(isset($_POST['casino_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['casino_myself_lose']) : '');
    $casino_recommender_lose = trim(isset($_POST['casino_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['casino_recommender_lose']) : '');

    $idx_slot_myself_bet = trim(isset($_POST['idx_slot_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_slot_myself_bet']) : '');
    $slot_myself_bet = trim(isset($_POST['slot_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['slot_myself_bet']) : '');
    $slot_recommender_bet = trim(isset($_POST['slot_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['slot_recommender_bet']) : '');
    $slot_myself_lose = trim(isset($_POST['slot_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['slot_myself_lose']) : '');
    $slot_recommender_lose = trim(isset($_POST['slot_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['slot_recommender_lose']) : '');

    $idx_powerball_myself_bet = trim(isset($_POST['idx_powerball_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['idx_powerball_myself_bet']) : '');
    $powerball_myself_bet = trim(isset($_POST['powerball_myself_bet']) ? $BbsAdminDAO->real_escape_string($_POST['powerball_myself_bet']) : '');
    $powerball_recommender_bet = trim(isset($_POST['powerball_recommender_bet']) ? $BbsAdminDAO->real_escape_string($_POST['powerball_recommender_bet']) : '');
    $powerball_myself_lose = trim(isset($_POST['powerball_myself_lose']) ? $BbsAdminDAO->real_escape_string($_POST['powerball_myself_lose']) : '');
    $powerball_recommender_lose = trim(isset($_POST['powerball_recommender_lose']) ? $BbsAdminDAO->real_escape_string($_POST['powerball_recommender_lose']) : '');

    $chex_myself = trim(isset($_POST['chex_myself']) ? $BbsAdminDAO->real_escape_string($_POST['chex_myself']) : '');
    $chex_recommender = trim(isset($_POST['chex_recommender']) ? $BbsAdminDAO->real_escape_string($_POST['chex_recommender']) : '');
    $idx = trim(isset($_POST['idx']) ? $BbsAdminDAO->real_escape_string($_POST['idx']) : '');

    $level = trim(isset($_POST['level']) ? $BbsAdminDAO->real_escape_string($_POST['level']) : '');


    $p_data['sql'] = "SELECT * FROM tb_static_rolling_comps WHERE idx = ?";

    $db_dataArr = $BbsAdminDAO->getQueryData_pre($p_data['sql'],[$idx]);
   
	try {

        // foreach($db_dataArr as $row) {

        //     if($row['type'] == "chex"){

        //         $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_chex = ? ,recommender_chex = ? WHERE level = ? ";
        
        //         $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$chex_myself,$chex_recommender, $level ]);

        //     }

        // }
        $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_chex = ? ,recommender_chex = ? WHERE level = ? ";
        
        $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$chex_myself,$chex_recommender, $level ]);

        // Prematch Single
        $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE idx = ?";
        
        $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$prematch_s_myself_bet,$prematch_s_recommender_bet,$prematch_s_myself_lose,$prematch_s_recommender_lose,$idx_prematch_s_myself_bet]);


        // Prematch Double
        $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE idx = ?";
        
        $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$prematch_d_myself_bet,$prematch_d_recommender_bet,$prematch_d_myself_lose,$prematch_d_recommender_lose,$idx_prematch_d_myself_bet]);

        // Inplay Single
        $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE idx = ?";
        
        $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$inplay_s_myself_bet,$inplay_s_recommender_bet,$inplay_s_myself_lose,$inplay_s_recommender_lose,$idx_inplay_s_myself_bet]);


        // Inplay Double
        $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE idx = ?";
        
        $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$inplay_d_myself_bet,$inplay_d_recommender_bet,$inplay_d_myself_lose,$inplay_d_recommender_lose,$idx_inplay_d_myself_bet]);

         // Casino 
         $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE idx = ?";
        
         $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$casino_myself_bet,$casino_recommender_bet,$casino_myself_lose,$casino_recommender_lose,$idx_casino_myself_bet]);
 
          // Slot 
          $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE idx = ?";
        
          $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$slot_myself_bet,$slot_recommender_bet,$slot_myself_lose,$slot_recommender_lose,$idx_slot_myself_bet]);
  
          // Powerball  
          $p_data['sql'] = "UPDATE tb_static_rolling_comps SET myself_bet = ? ,recommender_bet = ?,myself_lose = ? ,recommender_lose = ? WHERE level = ? AND type in ('powerball','eos_powerball','power_ladder','kino_ladder','virtual_soccer')";
        
          $BbsAdminDAO->setQueryData_pre($p_data['sql'],[$powerball_myself_bet,$powerball_recommender_bet,$powerball_myself_lose,$powerball_recommender_lose,$level]);
  

        $BbsAdminDAO->dbclose();
        
        $result["retCode"]	= 1;
        $result['retMsg']	= '';
        
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
            
  
	} catch (\Exception $e) {
    	$UTIL->logWrite("[UPDATE_ROLLING_COMPS] [error -2]", "error");
    	$result['retCode'] = -3;
    	$result['retMsg'] = 'Exception 예외발생';
    }
} else {
	$UTIL->logWrite("[UPDATE_ROLLING_COMPS] [error 2200]", "error");
	$UTIL->checkFailType('2200', '', '', 'json');
	exit;
}
?>
