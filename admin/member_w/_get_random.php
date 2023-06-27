<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');


$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['p_setkind'] = trim(isset($_POST['p_setkind']) ? $_POST['p_setkind'] : '');
$p_data['urecode'] = trim(isset($_POST['urecode']) ? $_POST['urecode'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$result["retCode"] = 2001;
$result["retData"] = '';

if($db_conn) {
    $now_ip = CommonUtil::get_client_ip();
    $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
    $p_data['p_setkind'] = $MEMAdminDAO->real_escape_string($p_data['p_setkind']);
    $p_data['urecode'] = $MEMAdminDAO->real_escape_string($p_data['urecode']);
    
    $p_data['sql'] = "select id, nick_name, recommend_code, u_business, is_recommend";
    $p_data['sql'] .= " from member where idx=".$p_data['m_idx']." ";
    $db_data_mem = $MEMAdminDAO->getQueryData($p_data);
    $db_recommend_code = $db_data_mem[0]['recommend_code'];
    $db_id = $db_data_mem[0]['id'];
    $db_nick_name = $db_data_mem[0]['nick_name'];
    
    // 일반회원이면 추천가능상태 체크
    if($db_data_mem[0]['u_business'] == 1 && $db_data_mem[0]['recommend_code'] == 'N'){
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        $MEMAdminDAO->dbclose();
        return;
    }
    
    if ($p_data['p_setkind'] == 'r') {
        $_randStr = '';
        for ($i=0;$i<10;$i++) {
            
            $_strbuff = "ABCDEFGHJKLMNPQRSTUVWXYZ";
            foreach(range(0, 1) as $Key) $_randStr .= substr($_strbuff, rand(0, strlen($_strbuff)-1), 1);
            $rand = rand(1000,9999);
            $p_data['randval'] = $rand.$_randStr;
            
            $p_data['sql'] = "select count(idx) as cnt  ";
            $p_data['sql'] .= " from member where recommend_code='".$p_data['randval']."' ";
            $db_data_mem = $MEMAdminDAO->getQueryData($p_data);
            if ($db_data_mem[0]['cnt'] < 1) {
                
                $p_data['sql'] = " update  member set recommend_code='".$p_data['randval']."', recommend_code_dt = now() ";
                $p_data['sql'] .= " where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // log
                $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
                $p_data['sql'] .= " values ('".$p_data['m_idx']."','추천코드','$db_recommend_code','".$p_data['randval']."','$admin_id') ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 어드민 히스토리 로그
                //$now_ip = $_SERVER['REMOTE_ADDR'];
                $log_data = "추천코드 변경 이전=>$db_recommend_code  이후=>".$p_data['randval'];
                $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                $st_country = $retData[0]['a_country'];

                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',28) ;";
                $MEMAdminDAO->setQueryData($p_data);
                
                $result["retCode"]	= 1000;
                $result["retData"]	= $p_data['randval'];
                
                break;
            }
        }
    }
    else if ($p_data['p_setkind'] == 'r2') {    // 총판일경우 넘어온 값으로 업데이트한다.
        $p_data['sql'] = "select count(idx) as cnt  ";
        $p_data['sql'] .= " from member where recommend_code='".$p_data['urecode']."' ";
        $db_data_mem = $MEMAdminDAO->getQueryData($p_data);
        if ($db_data_mem[0]['cnt'] < 1) {

            $p_data['sql'] = " update  member set recommend_code='".$p_data['urecode']."', recommend_code_dt = now() ";
            $p_data['sql'] .= " where idx=".$p_data['m_idx']." ";
            $MEMAdminDAO->setQueryData($p_data);

            // log
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','추천코드','$db_recommend_code','".$p_data['urecode']."','$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            // 어드민 히스토리 로그
            //$now_ip = $_SERVER['REMOTE_ADDR'];
            $log_data = "추천코드 변경 이전=>$db_recommend_code  이후=>".$p_data['urecode'];
            $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
            $retData = $MEMAdminDAO->getQueryData($p_data);
            $st_country = $retData[0]['a_country'];

            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',28) ;";
            $MEMAdminDAO->setQueryData($p_data);

            $result["retCode"]	= 1000;
            $result["retData"]	= $p_data['urecode'];
        }else
            $result["retCode"] = 3001;
    }
    else if ($p_data['p_setkind'] == 'd') {
        $p_data['sql'] = " update  member set recommend_code=null, recommend_code_dt = now() ";
        $p_data['sql'] .= " where idx=".$p_data['m_idx']." ";
        $MEMAdminDAO->setQueryData($p_data);
        
        // log
        $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
        $p_data['sql'] .= " values ('".$p_data['m_idx']."','추천코드','$db_recommend_code','','$admin_id') ";
        $MEMAdminDAO->setQueryData($p_data);
        
        // 어드민 히스토리 로그
        //$now_ip = $_SERVER['REMOTE_ADDR'];
        $log_data = "추천코드 변경 이전=>$db_recommend_code  이후=>";
        $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $st_country = $retData[0]['a_country'];

        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
        $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',28) ;";
        $MEMAdminDAO->setQueryData($p_data);
        
        $result["retCode"]	= 1000;
        $result["retData"]	= '';
    }
    
	$MEMAdminDAO->dbclose();
}


echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
