<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');

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

if ( ($p_data['mtype'] == '') || ($p_data['mkind'] == '') || ($p_data['m_idx'] < 1) ) {
    $result['retCode'] = 2001;
}
else {
    $result['retCode'] = 1000;
    
    $retCash = 0;
    
    $MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
    $db_conn = $MEMAdminDAO->dbconnect();
    
    if($db_conn) {
        
        /*$p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
        $p_data['mtype'] = $MEMAdminDAO->real_escape_string($p_data['mtype']);
        $p_data['mkind'] = $MEMAdminDAO->real_escape_string($p_data['mkind']);
        $p_data['money'] = $MEMAdminDAO->real_escape_string($p_data['money']);
        $p_data['point'] = $MEMAdminDAO->real_escape_string($p_data['point']);*/

        $ac_code = 0;       
        
        if ($p_data['mtype'] == 'money') {
            if ($p_data['mkind'] == 'p') {
                $ac_code = 121;
                $a_comment = "관리자 충전 [".$p_data['money']."]";
                
                $p_data['sql'] = "select money from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
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
                
            }
            else if ($p_data['mkind'] == 'm') {
                $ac_code = 122;
                $a_comment = "관리자 회수 [-".$p_data['money']."]";
                
                
                $p_data['sql'] = "select money from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
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
                $a_comment = "관리자 포인트 충전 [".$p_data['point']."]";
                
                $p_data['sql'] = "select point, betting_p from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
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
                
            }
            else if ($p_data['mkind'] == 'm') {
                $ac_code = 124;
                $a_comment = "관리자 포인트 회수 [-".$p_data['point']."]";
                
                
                $p_data['sql'] = "select point, betting_p from member where idx=".$p_data['m_idx']." ";
                $retData = $MEMAdminDAO->getQueryData($p_data);
                
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
            }
            else {
                $result['retCode'] = 2001;
            }
            
            $p_data['sql'] = "select money, point, betting_p from member where idx=".$p_data['m_idx']." ";
            $retData = $MEMAdminDAO->getQueryData($p_data);
            
            $retCash = ($retData[0]['point'] + $retData[0]['betting_p']);
        }
        
        $MEMAdminDAO->dbclose();
    }
    
    
    
    $result['retCash'] = number_format($retCash);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>