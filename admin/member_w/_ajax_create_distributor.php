<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Bbs_dao.php');


$Id = isset($_POST['id']) ? $_POST['id'] : NULL;
$u_business = isset($_POST['u_business']) ? $_POST['u_business'] : NULL;
$password = isset($_POST['password']) ? $_POST['password'] : NULL;
$nickName = isset($_POST['nickname']) ? $_POST['nickname'] : NULL;
$call = isset($_POST['call']) ? $_POST['call'] : NULL;
$accountBank = isset($_POST['account_bank']) ? $_POST['account_bank'] : NULL;
$accountNumber = isset($_POST['account_number']) ? $_POST['account_number'] : NULL;
$accountName = isset($_POST['account_name']) ? $_POST['account_name'] : NULL;
$dist_types = isset($_POST['dist_types']) ? $_POST['dist_types'] : NULL;

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if(!is_int((int)$u_business)){
    die();
}

if($db_conn) {
        
    $Id = $BdsAdminDAO->real_escape_string($Id);
    $password = $BdsAdminDAO->real_escape_string($password);
    $nickName = $BdsAdminDAO->real_escape_string($nickName);
    $call = $BdsAdminDAO->real_escape_string($call);
    $accountBank = $BdsAdminDAO->real_escape_string($accountBank);
    $accountNumber = $BdsAdminDAO->real_escape_string($accountNumber);
    $accountName = $BdsAdminDAO->real_escape_string($accountName);
        
    $p_data['sql'] = "select count(idx) as cnt from member where id = ? ";
    $dbResult = $BdsAdminDAO->getQueryData_pre($p_data['sql'],[$Id]);
    
    if($dbResult[0]['cnt'] > 0){
        $result["retCode"]	= 2000;
        $result['retMsg']	= '이미 사용중인 아이디입니다.';
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        $BdsAdminDAO->dbclose();
        return;
    }
    
    $p_data['sql'] = "select count(*) as count from member where `call` = ? ";
    $dbResult = $BdsAdminDAO->getQueryData_pre($p_data['sql'],[$call]);
    if (0 < $dbResult[0]['count']) {
        $result["retCode"]	= 2000;
        $result['retMsg']	= '중복 전화번호로 확인되어 사용이 불가합니다.';
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        $BdsAdminDAO->dbclose();
        return;
    }

    $p_data['sql'] = "select count(*) as count from member where account_number = ? ";
    $dbResult = $BdsAdminDAO->getQueryData_pre($p_data['sql'],[$accountNumber]);
    if (0 < $dbResult[0]['count']) {
        $result["retCode"]	= 2000;
        $result['retMsg']	= '중복 계좌번호로 확인되어 사용이 불가합니다.';
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        $BdsAdminDAO->dbclose();
        return;
    }

    //$rememberIdx = $member_idx;
    //$rememberId = $disInfo[0]->id;
    $money = 0;

    // 총판생성 
    //$u_business = 3;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $p_data['sql'] = "INSERT INTO member (id, password, nick_name, u_business, money, `call`, account_bank, account_number, account_name, status, level,dist_type)"
                    . " VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, 1, 9,?)";
    
    
    CommonUtil::logWrite("create_dist sql: " . $p_data['sql'], "info");
    CommonUtil::logWrite("create_dist param: " . json_encode([$Id,$password,$nickName,$u_business,$call,$accountBank,$accountNumber,$accountName,$dist_types]), "info");
    
    $BdsAdminDAO->setQueryData_pre($p_data['sql'],[$Id,$password,$nickName,$u_business,$call,$accountBank,$accountNumber,$accountName,$dist_types]);
    
   
    // 요율설정 디폴트로 생성해준다.
    $i_data['sql'] = "INSERT INTO shop_config (member_idx)
                        VALUES ( (SELECT idx FROM member WHERE id = ?) )";
    $BdsAdminDAO->setQueryData_pre($i_data['sql'],[$Id]);
    
    $BdsAdminDAO->dbclose();
    $result["retCode"]	= SUCCESS;
    $result['retMsg']	= 'success';

    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>