<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');
include_once(_LIBPATH . '/class_Code.php');

//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

if (!isset($_SESSION)) {
    session_start();
}

//$UTIL = new CommonUtil();

// 내가 속할수 있는 총판리스트를 가져온다.
$dist_type = isset($_POST['dist_type']) ? $_POST['dist_type'] : -1;
//$dist_id = isset($_POST['dist_id']) ? $_POST['dist_id'] : 0;
$u_business = $_SESSION['u_business'];
$memer_idx = $_SESSION['member_idx'];

//CommonUtil::logWrite("_ajax_distributor_list dist_type: " . $dist_type, "info");
$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if($db_conn) {
    $dist_type = $BdsAdminDAO->real_escape_string($dist_type);
    //$dist_id = $BdsAdminDAO->real_escape_string($dist_id);
    
    // 총판목록
    if ($u_business == 0) {
        if(-1 == $dist_type){
            $sql = "select idx, id, nick_name, u_business from member where u_business <> 1";
            $dbResult = $BdsAdminDAO->getQueryData_pre($sql, []);
        }else{
            $sql = "select idx, id, nick_name, u_business from member where u_business <> 1 and dist_type = ?";
            $dbResult = $BdsAdminDAO->getQueryData_pre($sql, [$dist_type]);
        }
    } else { // 하위총판도 가져와야 한다.
        if(-1 == $dist_type){
            $dbResult = GameCode::getRecommandMemberInfos($memer_idx, $BdsAdminDAO);
        }else{
            $dbResult = GameCode::getRecommandMemberInfosByDistType($memer_idx, $dist_type, $BdsAdminDAO);
        }
    }
    
    $BdsAdminDAO->dbclose();

    if(null == $dbResult){
        $result["retCode"]	= 2000;
        $result['retMsg']	= '해당 목록이 없습니다.';
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        return;
    }
	
    $result["retCode"]	= 1000;
    $result['retMsg']	= 'success';
    $result["list"]	= $dbResult;

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>