<?php 
if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);
$p_data['u_nick'] = trim(isset($_POST['u_nick']) ? $_POST['u_nick'] : '');
$p_data['u_pass'] = trim(isset($_POST['u_pass']) ? $_POST['u_pass'] : '');
$p_data['u_hp01'] = trim(isset($_POST['u_hp01']) ? $_POST['u_hp01'] : '');
$p_data['u_hp02'] = trim(isset($_POST['u_hp02']) ? $_POST['u_hp02'] : '');
$p_data['u_hp03'] = trim(isset($_POST['u_hp03']) ? $_POST['u_hp03'] : '');
$p_data['u_is_recomm'] = trim(isset($_POST['u_is_recomm']) ? $_POST['u_is_recomm'] : '');
$p_data['u_status'] = trim(isset($_POST['u_status']) ? $_POST['u_status'] : '');
$p_data['u_level'] = trim(isset($_POST['u_level']) ? $_POST['u_level'] : 0);
$p_data['u_autolevel'] = trim(isset($_POST['u_autolevel']) ? $_POST['u_autolevel'] : '');
$p_data['u_recomm_user'] = trim(isset($_POST['u_recomm_user']) ? $_POST['u_recomm_user'] : '');
$p_data['u_acc_bank'] = trim(isset($_POST['u_acc_bank']) ? $_POST['u_acc_bank'] : '');
$p_data['u_acc_number'] = trim(isset($_POST['u_acc_number']) ? $_POST['u_acc_number'] : '');
$p_data['u_acc_name'] = trim(isset($_POST['u_acc_name']) ? $_POST['u_acc_name'] : '');
$p_data['u_acc_pass'] = trim(isset($_POST['u_acc_pass']) ? $_POST['u_acc_pass'] : '');
$p_data['second_pass'] = trim(isset($_POST['second_pass']) ? $_POST['second_pass'] : '');
$p_data['birth'] = trim(isset($_POST['birth']) ? $_POST['birth'] : '');
$p_data['u_mobile_carrier'] = trim(isset($_POST['u_mobile_carrier']) ? $_POST['u_mobile_carrier'] : 0);

if ( ($p_data['m_idx'] < 1) ) {
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
                
        $ch_type = $be_data = $af_data = '';
        
        $enc_pass = $enc_acc_pass = '';
        
        if ($p_data['u_pass'] != '') {
            $enc_pass = password_hash($p_data['u_pass'], PASSWORD_DEFAULT);
        }
        
        if ($p_data['u_acc_pass'] != '') {
            $enc_acc_pass = password_hash($p_data['u_acc_pass'], PASSWORD_DEFAULT);
        }
        
        //2차인증 체크
        $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
        $second_pass = $MEMAdminDAO->getQueryData($p_data)[0];
        if(hash('sha512', $p_data['second_pass']) != $second_pass['set_type_val']){
            $result['retCode'] = 2002;
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $p_data['sql'] = "select * from member where idx=".$p_data['m_idx']." ";
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $db_id = $retData[0]['id'];
        $db_nick_name = $retData[0]['nick_name'];
        $log_type = 0;
        
        $update_sql = "";

        if ($enc_pass != '') {
            $ch_type = '비밀번호';
            $be_data = $retData[0]['password'];
            $af_data = $enc_pass;
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.password = '$af_data' ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "비밀번호 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 40;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ($enc_acc_pass != '') {
            $ch_type = '환전비밀번호';
            $be_data = $retData[0]['exchange_password'];
            $af_data = $enc_acc_pass;
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.exchange_password = '$af_data' ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "환전비밀번호 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 41;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_nick'] != '') && ($p_data['u_nick'] != $retData[0]['nick_name']) ) {
            $ch_type = '닉네임';
            $be_data = $retData[0]['nick_name'];
            $af_data = $p_data['u_nick'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.nick_name = '$af_data' ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "닉네임 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 42;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_hp01'] != '') && ($p_data['u_hp02'] != '') && ($p_data['u_hp03'] != '') ) {
            
            $chk_hp = $p_data['u_hp01'].$p_data['u_hp02'].$p_data['u_hp03'];
            
            if ($chk_hp != $retData[0]['call']) {
                $ch_type = '전화번호';
                $be_data = $retData[0]['call'];
                $af_data = $chk_hp;
                
                if ($update_sql != "") {
                    $update_sql .= ",";
                }
                
                $update_sql .= " a.call = '$af_data' ";
                
                $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
                $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
                $MEMAdminDAO->setQueryData($p_data);
                
                $log_data = "전화번호 변경 이전=>$be_data  이후=>".$af_data;
                $log_type = 43;
                
                // 어드민 히스토리 로그
                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
                $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
                $MEMAdminDAO->setQueryData($p_data);
            }
            
        }
        
        if ( ($p_data['birth'] != '') && ($p_data['birth'] != $retData[0]['birth']) ) {
        	$ch_type = '생년월일';
        	$be_data = $retData[0]['birth'];
        	$af_data = $p_data['birth'];
        	
        	if ($update_sql != "") {
        		$update_sql .= ",";
        	}
        	
        	$update_sql .= " a.birth = '$af_data' ";
        	
        	$p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
        	$p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
        	$MEMAdminDAO->setQueryData($p_data);
        	
        	$log_data = "생년월일 변경 이전=>$be_data  이후=>".$af_data;
        	$log_type = 91;
        	
        	// 어드민 히스토리 로그
        	$p_data['sql'] = "insert into  t_adm_log ";
        	$p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
        	$p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
        	$MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_is_recomm'] != '') && ($p_data['u_is_recomm'] != $retData[0]['is_recommend']) ) {
            $ch_type = '추천';
            $be_data = $retData[0]['is_recommend'];
            $af_data = $p_data['u_is_recomm'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.is_recommend = '$af_data' ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "추천 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 44;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_status'] != '') && ($p_data['u_status'] != $retData[0]['status']) ) {
            $ch_type = '상태';
            $be_data = $retData[0]['status'];
            $be_data_str = $af_data_str = '';
            switch ($retData[0]['status']) {
                //회원상태 ( 1:정상, 2:정지, 3:탈퇴, 11:승인 대기회원 )
                case 1: $be_data_str = "정상"; break;
                case 2: $be_data_str = "정지"; break;
                case 3: $be_data_str = "탈퇴"; break;
                case 11: $be_data_str = "승인 대기"; break;
            }
            
            $af_data = $p_data['u_status'];
            switch ($af_data) {
                //회원상태 ( 1:정상, 2:정지, 3:탈퇴, 11:승인 대기회원 )
                case 1: $af_data_str = "정상"; break;
                case 2: $af_data_str = "정지"; break;
                case 3: $af_data_str = "탈퇴"; break;
                case 11: $af_data_str = "승인 대기"; break;
            }
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.status = $af_data ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data_str','$af_data_str', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "유저상태 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 45;
            
            // 유저 탈퇴처리면 로그아웃 처리해야 한다.
            if($af_data == 2){
                $p_data['sql'] = "update member set session_key = null where idx = ".$p_data['m_idx'];
                $MEMAdminDAO->setQueryData($p_data);
            }else if($af_data == 3){
                $p_data['sql'] = "update member set session_key = null, leave_time = now() where idx = ".$p_data['m_idx'];
                $MEMAdminDAO->setQueryData($p_data);
            }
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_level'] != '') && ($p_data['u_level'] != $retData[0]['level']) ) {
            $ch_type = '레벨';
            $be_data = $retData[0]['level'];
            $af_data = $p_data['u_level'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.level = $af_data ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "레벨 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 46;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_autolevel'] != '') && ($p_data['u_autolevel'] != $retData[0]['auto_level']) ) {
            $ch_type = '자동렙업';
            $be_data = $retData[0]['auto_level'];
            $af_data = $p_data['u_autolevel'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.auto_level = '$af_data' ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "자동렙업 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 47;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_recomm_user'] != '') && ($p_data['u_recomm_user'] != $retData[0]['recommend_member']) ) {
            $ch_type = '추천인';
            $be_data = $retData[0]['recommend_member'];
            $af_data = $p_data['u_recomm_user'];
            
            
            $p_data['sql'] = "select ";
            $p_data['sql'] .= " (select id from member where idx=$be_data) as be_data_id ";
            $p_data['sql'] .= ", (select id from member where idx=$af_data) as af_data_id ";
            $p_data['sql'] .= " from member where idx=".$p_data['m_idx']." ";
            $retDataSub = $MEMAdminDAO->getQueryData($p_data);
            $be_data_id = $retDataSub[0]['be_data_id'];
            $af_data_id = $retDataSub[0]['af_data_id'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.recommend_member = $af_data ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data_id','$af_data_id', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "추천인 변경 이전=>$be_data_id  이후=>".$af_data_id;
            $log_type = 48;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_acc_number'] !== '') && ($p_data['u_acc_number'] !== $retData[0]['account_number']) ) {
            $ch_type = '계좌정보';
            $be_data = $retData[0]['account_number'];
            $af_data = $p_data['u_acc_number'];
           
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.account_number = '$af_data' ";
            
            // account info change time
            $p_data['sql'] = "update member set info_change_dt = now() where idx = ".$p_data['m_idx'];
            $MEMAdminDAO->setQueryData($p_data);
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "계좌번호 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 49;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_acc_name'] != '') && ($p_data['u_acc_name'] != $retData[0]['account_name']) ) {
            $ch_type = '계좌이름';
            $be_data = $retData[0]['account_name'];
            $af_data = $p_data['u_acc_name'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.account_name = '$af_data' ";
            
            // account info change time
            $p_data['sql'] = "update member set info_change_dt = now() where idx = ".$p_data['m_idx'];
            $MEMAdminDAO->setQueryData($p_data);
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "계좌예금주 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 50;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_acc_bank'] != '') && ($p_data['u_acc_bank'] != $retData[0]['account_bank']) ) {
            $ch_type = '은행';
            $be_data = $retData[0]['account_bank'];
            $af_data = $p_data['u_acc_bank'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.account_bank = '$af_data' ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "은행 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 51;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ( ($p_data['u_mobile_carrier'] != '') && ($p_data['u_mobile_carrier'] != $retData[0]['mobile_carrier']) ) {
            $ch_type = '통신사';
            $be_data = $retData[0]['mobile_carrier'];
            $af_data = $p_data['u_mobile_carrier'];
            
            if ($update_sql != "") {
                $update_sql .= ",";
            }
            
            $update_sql .= " a.mobile_carrier = $af_data ";
            
            $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
            $p_data['sql'] .= " values ('".$p_data['m_idx']."','$ch_type','$be_data','$af_data', '$admin_id') ";
            $MEMAdminDAO->setQueryData($p_data);
            
            $log_data = "통신사 변경 이전=>$be_data  이후=>".$af_data;
            $log_type = 92;
            
            // 어드민 히스토리 로그
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);
        }
        
        if ($update_sql != '') {
            $p_data['sql'] = "update member a set ";
            $p_data['sql'] .= $update_sql;
            $p_data['sql'] .= " where a.idx=".$p_data['m_idx']." ";
            
            $MEMAdminDAO->setQueryData($p_data);
            
            // 어드민 히스토리 로그
            /*$p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, a_ip, a_country, u_id, u_nick, log_data, log_type) ";
            $p_data['sql'] .= " values('$admin_id','$now_ip','$st_country', '$db_id', '$db_nick_name','$log_data',$log_type) ;";
            $MEMAdminDAO->setQueryData($p_data);*/
        }
        
        $MEMAdminDAO->dbclose();
    }
    
    $result['retCash'] = number_format($retCash);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>