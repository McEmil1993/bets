<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['mtype'] = trim(isset($_POST['mtype']) ? $_POST['mtype'] : '');
$p_data['mkind'] = trim(isset($_POST['mkind']) ? $_POST['mkind'] : '');
$p_data['money'] = trim(isset($_POST['money']) ? $_POST['money'] : 0);
$p_data['point'] = trim(isset($_POST['point']) ? $_POST['point'] : 0);
$p_data['gmoney'] = trim(isset($_POST['gmoney']) ? $_POST['gmoney'] : 0);
$p_data['second_pass'] = trim(isset($_POST['second_pass']) ? $_POST['second_pass'] : '');
$p_data['comment'] = trim(isset($_POST['comment']) ? $_POST['comment'] : 0);

if ( ($p_data['mtype'] == '') || ($p_data['mkind'] == '') || ($p_data['m_idx'] < 1) ) {
    $result['retCode'] = 2001;
}
else {
    $result['retCode'] = 1000;
    
    $retCash = 0;
    
    $MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
    $db_conn = $MEMAdminDAO->dbconnect();
    
    if($db_conn) {
        $now_ip = CommonUtil::get_client_ip();
        $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $st_country = $retData[0]['a_country'];
        
        $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
        $p_data['mtype'] = $MEMAdminDAO->real_escape_string($p_data['mtype']);
        $p_data['mkind'] = $MEMAdminDAO->real_escape_string($p_data['mkind']);
        $p_data['money'] = $MEMAdminDAO->real_escape_string($p_data['money']);
        $p_data['point'] = $MEMAdminDAO->real_escape_string($p_data['point']);
        $p_data['second_pass'] = $MEMAdminDAO->real_escape_string($p_data['second_pass']);
        
        $ac_code = 0;
        
        //2차인증 체크
        $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
        $second_pass = $MEMAdminDAO->getQueryData($p_data)[0];
        if(hash('sha512', $p_data['second_pass']) != $second_pass['set_type_val']){
            $result['retCode'] = 2002;
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if ($p_data['mtype'] == 'money') {
            if ($p_data['mkind'] == 'p') {
                $ac_code = 121;
                $a_comment = "관리자 머니 충전 [".$p_data['money']."] ".$p_data['comment'];
                
                $p_data['sql'] = "select money, id, nick_name from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
                $db_id = $retData[0]['id'];
                $db_nick_name = $retData[0]['nick_name'];
                $now_cash = $retData[0]['money'];
                $af_cash = $now_cash + $p_data['money'];
                
                $p_data['sql'] = "update member set money = money + ".$p_data['money']."  where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 121:관리자충전,122:관리자회수 
                // 123:관리자 포인트 충전, 124:관리자 포인트 회수
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(".$p_data['m_idx'].", $ac_code, 0, ".$p_data['money']." ";
                $p_data['sql'] .= ", $now_cash, $af_cash, '".strtoupper($p_data['mkind'])."','$a_comment','$admin_id')";
                
                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "관리자 머니 지급 이전=>$now_cash  이후=>".$af_cash;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data', 70) ;";
                $MEMAdminDAO->setQueryData($p_data);
                
            }
            else if ($p_data['mkind'] == 'm') {
                $ac_code = 122;
                $a_comment = "관리자 머니 회수 [-".$p_data['money']."] ".$p_data['comment'];
                
                
                $p_data['sql'] = "select money, id, nick_name from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
                $db_id = $retData[0]['id'];
                $db_nick_name = $retData[0]['nick_name'];
                $now_cash = $retData[0]['money'];
                $af_cash = $now_cash - $p_data['money'];
                
                $p_data['sql'] = "update member set money = money - ".$p_data['money']."  where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 121:관리자충전,122:관리자회수
                // 123:관리자 포인트 충전, 124:관리자 포인트 회수
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(".$p_data['m_idx'].", $ac_code, 0, ".$p_data['money']." ";
                $p_data['sql'] .= ", $now_cash, $af_cash, '".strtoupper($p_data['mkind'])."','$a_comment','$admin_id')";
                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "관리자 머니 회수 이전=>$now_cash  이후=>".$af_cash;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data', 71) ;";
                $MEMAdminDAO->setQueryData($p_data);
            }
            else {
                $result['retCode'] = 2001;
            }
            
            $p_data['sql'] = "select money from member where idx=".$p_data['m_idx']." ";
            $retData = $MEMAdminDAO->getQueryData($p_data);
            
            $retCash = $retData[0]['money'];
        }
        else if ($p_data['mtype'] == 'point') {
            // 123:관리자 포인트 충전, 124:관리자 포인트 회수
            if ($p_data['mkind'] == 'p') {
                $ac_code = 123;
                $a_comment = "관리자 포인트 충전 [".$p_data['point']."] ".$p_data['comment'];
                
                $p_data['sql'] = "select point, betting_p, id, nick_name from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
                $db_id = $retData[0]['id'];
                $db_nick_name = $retData[0]['nick_name'];
                $now_point = ($retData[0]['point'] + $retData[0]['betting_p']);
                $af_point = $now_point + $p_data['point'];
                
                $p_data['sql'] = "update member set point = point + ".$p_data['point']."  where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 121:관리자충전,122:관리자회수
                // 123:관리자 포인트 충전, 124:관리자 포인트 회수
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, point, be_point, af_point, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(".$p_data['m_idx'].", $ac_code, 0, ".$p_data['point']." ";
                $p_data['sql'] .= ", $now_point, $af_point, '".strtoupper($p_data['mkind'])."','$a_comment','$admin_id')";

                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "관리자 포인트 회수 이전=>$now_point  이후=>".$af_point;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data', 72) ;";
                $MEMAdminDAO->setQueryData($p_data);
            }
            else if ($p_data['mkind'] == 'm') {
                $ac_code = 124;
                $a_comment = "관리자 포인트 회수 [-".$p_data['point']."] ".$p_data['comment'];
                
                
                $p_data['sql'] = "select point, betting_p, id, nick_name from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
                $db_id = $retData[0]['id'];
                $db_nick_name = $retData[0]['nick_name'];
                $now_point = ($retData[0]['point'] + $retData[0]['betting_p']);
                $af_point = $now_point - $p_data['point'];
                
                $p_data['sql'] = "update member set point = point - ".$p_data['point']."  where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 121:관리자충전,122:관리자회수
                // 123:관리자 포인트 충전, 124:관리자 포인트 회수
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, point, be_point, af_point, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(".$p_data['m_idx'].", $ac_code, 0, ".$p_data['point']." ";
                $p_data['sql'] .= ", $now_point, $af_point, '".strtoupper($p_data['mkind'])."','$a_comment','$admin_id')";
                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "관리자 포인트 회수 이전=>$now_point  이후=>".$af_point;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data', 73) ;";
                $MEMAdminDAO->setQueryData($p_data);
            }
            else {
                $result['retCode'] = 2001;
            }
            
            $p_data['sql'] = "select money, point, betting_p from member where idx=".$p_data['m_idx']." ";
            $retData = $MEMAdminDAO->getQueryData($p_data);
            
            $retCash = ($retData[0]['point'] + $retData[0]['betting_p']);
        }
        else if ($p_data['mtype'] == 'gmoney') {
            if ($p_data['mkind'] == 'p') {
                $ac_code = 504;
                $a_comment = "관리자 지머니 충전 [".$p_data['gmoney']."] ".$p_data['comment'];
                
                $p_data['sql'] = "select g_money, id, nick_name from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
                $db_id = $retData[0]['id'];
                $db_nick_name = $retData[0]['nick_name'];
                $now_cash = $retData[0]['g_money'];
                $af_cash = $now_cash + $p_data['gmoney'];
                
                $p_data['sql'] = "update member set g_money = g_money + ".$p_data['gmoney']."  where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 121:관리자충전,122:관리자회수 
                // 123:관리자 포인트 충전, 124:관리자 포인트 회수
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(".$p_data['m_idx'].", $ac_code, 0, ".$p_data['gmoney']." ";
                $p_data['sql'] .= ", $now_cash, $af_cash, '".strtoupper($p_data['mkind'])."','$a_comment','$admin_id')";
                
                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "관리자 지머니 충전 이전=>$now_cash  이후=>".$af_cash;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data', 65) ;";
                $MEMAdminDAO->setQueryData($p_data);
            }
            else if ($p_data['mkind'] == 'm') {
                $ac_code = 505;
                $a_comment = "관리자 지머니 회수 [-".$p_data['gmoney']."] ".$p_data['comment'];
                
                
                $p_data['sql'] = "select g_money, id, nick_name from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
                $db_id = $retData[0]['id'];
                $db_nick_name = $retData[0]['nick_name'];
                $now_cash = $retData[0]['g_money'];
                $af_cash = $now_cash - $p_data['gmoney'];
                
                $p_data['sql'] = "update member set g_money = g_money - ".$p_data['gmoney']."  where idx=".$p_data['m_idx']." ";
                $MEMAdminDAO->setQueryData($p_data);
                
                // 121:관리자충전,122:관리자회수
                // 123:관리자 포인트 충전, 124:관리자 포인트 회수
                $p_data['sql'] = "insert into  t_log_cash ";
                $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
                $p_data['sql'] .= " values(".$p_data['m_idx'].", $ac_code, 0, ".$p_data['gmoney']." ";
                $p_data['sql'] .= ", $now_cash, $af_cash, '".strtoupper($p_data['mkind'])."','$a_comment','$admin_id')";
                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "관리자 지머니 회수 이전=>$now_cash  이후=>".$af_cash;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data', 66) ;";
                $MEMAdminDAO->setQueryData($p_data);
            }
            else {
                $result['retCode'] = 2001;
            }
            
            $p_data['sql'] = "select g_money from member where idx=".$p_data['m_idx']." ";
            $retData = $MEMAdminDAO->getQueryData($p_data);
            
            $retCash = $retData[0]['g_money'];
        }
        
        $MEMAdminDAO->dbclose();
    }
    
    
    
    $result['retCash'] = number_format($retCash);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>