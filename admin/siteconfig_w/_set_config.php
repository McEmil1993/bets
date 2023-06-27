<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['aid'];

$result["retCode"] = 2001;
$result["retData"] = '';

$p_data['ptype'] = trim(isset($_POST['ptype']) ? $_POST['ptype'] : '');
$p_data['idx'] = trim(isset($_POST['idx']) ? $_POST['idx'] : 0);
$p_data['adomain'] = trim(isset($_POST['adomain']) ? $_POST['adomain'] : '');
$p_data['adomain_code'] = trim(isset($_POST['adomain_code']) ? $_POST['adomain_code'] : '');
$p_data['second_pass'] = trim(isset($_POST['second_pass']) ? $_POST['second_pass'] : '');

$level_account = array();
$db_level_account = array();

if ($p_data['ptype'] == 'domain_reg') {
    if ( ($p_data['adomain'] == '') || ($p_data['adomain_code'] == '') ) {
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
else if ($p_data['ptype'] == 'domain_del') {
    if ($p_data['idx'] < 1) {
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
else if ($p_data['ptype'] == 'account_level') {
    
    $acc_number_cnt = count($_POST['acc_number']);
    $acc_name_cnt = count($_POST['acc_name']);
    $acc_bank_cnt = count($_POST['acc_bank']);
    $display_acc_bank_cnt = count($_POST['display_acc_bank']);
    
    foreach($_POST['acc_number'] as $key => $value) {
        $level = $key + 1;
        $level_account[$level]['account_number'] = $value;
    }
    
    foreach($_POST['acc_name'] as $key => $value) {
        $level = $key + 1;
        $level_account[$level]['account_name'] = $value;
    }
    
    foreach($_POST['acc_bank'] as $key => $value) {
        $level = $key + 1;
        $level_account[$level]['account_bank'] = $value;
    }
    
    foreach($_POST['display_acc_bank'] as $key => $value) {
        $level = $key + 1;
        $level_account[$level]['display_account_bank'] = $value;
    }
    
    if ( ($acc_number_cnt < 1) || ($acc_name_cnt < 1) || ($acc_bank_cnt < 1) || ($display_acc_bank_cnt < 1) ) {
        $result["retCode"] = $display_acc_bank_cnt;
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }

}
else {
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
    exit;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    //2차인증 체크
    $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
    $second_pass = $MEMAdminDAO->getQueryData($p_data)[0];
    if(hash('sha512', $p_data['second_pass']) != $second_pass['set_type_val']){
        $result['retCode'] = 2002;
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return;
    }
    
    if ($p_data['ptype'] == 'domain_reg') {
        
        $p_data['sql'] = "insert into  t_domain_code ";
        $p_data['sql'] .= " (domain, domain_code) ";
        $p_data['sql'] .= " values('".$p_data['adomain']."', '".$p_data['adomain_code']."') ";
        
        $dbRet = $MEMAdminDAO->setQueryData($p_data);
        
        if ($dbRet) {
            
            $log_data = "[".$p_data['adomain']."] [".$p_data['adomain_code']."] 등록";
            
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, log_data, log_type) ";
            $p_data['sql'] .= " values('".$admin_id."', '".$log_data."', 3) ";
            
            $MEMAdminDAO->setQueryData($p_data);
            
            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
    }
    else if ($p_data['ptype'] == 'domain_del') {
        
        $p_data['sql'] = "select domain FROM t_domain_code ";
        $p_data['sql'] .= " where idx=".$p_data['idx']." ";
        
        $retData = $MEMAdminDAO->getQueryData($p_data);
        $db_domain = $retData[0]['domain'];
        $log_data = "[$db_domain]  삭제";
        
        $p_data['sql'] = "delete FROM t_domain_code ";
        $p_data['sql'] .= " where idx=".$p_data['idx']." ";
        
        $dbRet = $MEMAdminDAO->setQueryData($p_data);
        
        if ($dbRet) {
            $p_data['sql'] = "insert into  t_adm_log ";
            $p_data['sql'] .= " (a_id, log_data, log_type) ";
            $p_data['sql'] .= " values('".$admin_id."', '".$log_data."', 3) ";
            
            $MEMAdminDAO->setQueryData($p_data);
            
            $result["retCode"] = 1000;
            $result["retData"] = '';
        }
    }
    else if ($p_data['ptype'] == 'account_level') {
        // 은행, 은행아이디를 구한다.
        $p_data['sql'] = "select account_code, account_name, bank_id FROM account";
        $accountData = $MEMAdminDAO->getQueryData($p_data);
        
        $accountList = array();
        foreach ($accountData as $key => $value) {
            $accountList[$value['account_name']] = $value['bank_id'];
        }
        
        $p_data['sql'] = "select idx, level, account_name, account_bank, account_number, display_account_bank FROM account_level_list order by level ";
        $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
        
        for ($i=1;$i<=10;$i++) {
            $db_level_account[$i]['use'] = false;
        }
        
        if(!empty($db_dataArr)){
            foreach($db_dataArr as $row) {
                $level = $row['level'];
                
                $db_level_account[$level]['use'] = true;
                $db_level_account[$level]['idx'] = $row['idx'];
                $db_level_account[$level]['account_name'] = trim($row['account_name']);
                $db_level_account[$level]['account_bank'] = trim($row['account_bank']);
                $db_level_account[$level]['account_number'] = trim($row['account_number']);
                $db_level_account[$level]['display_account_bank'] = trim($row['display_account_bank']);
            }
        }
        
        for($i=1;$i<= (count($level_account));$i++) {
            
            if ( ($level_account[$i]['account_bank']!='') && ($level_account[$i]['account_name']!='') 
                && ($level_account[$i]['account_number']!='') && ($level_account[$i]['display_account_bank']!='') ) {
                
                if($db_level_account[$i]['use']) {
                    
                    if ( ($level_account[$i]['account_name'] != $db_level_account[$i]['account_name'])
                        || ($level_account[$i]['account_bank'] != $db_level_account[$i]['account_bank'])
                        || ($level_account[$i]['account_number'] != $db_level_account[$i]['account_number'])
                        || ($level_account[$i]['display_account_bank'] != $db_level_account[$i]['display_account_bank']))
                    {
                        $p_data['sql'] = "update account_level_list set ";
                        $p_data['sql'] .= " account_name='".$level_account[$i]['account_name']."' ";
                        $p_data['sql'] .= ", account_bank='".$level_account[$i]['account_bank']."' ";
                        $p_data['sql'] .= ", account_number='".$level_account[$i]['account_number']."' ";
                        $p_data['sql'] .= ", bank_id='".$accountList[$level_account[$i]['account_bank']]."' ";
                        $p_data['sql'] .= ", display_account_bank='".$level_account[$i]['display_account_bank']."' ";
                        $p_data['sql'] .= " where idx=".$db_level_account[$i]['idx']." ";
                        
                        $dbRet = $MEMAdminDAO->setQueryData($p_data);
                        
                        $log_data = "[레벨별 계좌 설정] [$i level 업데이트]";
                        if ($dbRet) {
                            $p_data['sql'] = "insert into  t_adm_log ";
                            $p_data['sql'] .= " (a_id, log_data, log_type) ";
                            $p_data['sql'] .= " values('".$admin_id."', '".$log_data."', 4) ";
                            
                            $MEMAdminDAO->setQueryData($p_data);
                            
                            $result["retCode"] = 1000;
                            $result["retData"] = '';
                        }
                    }else{
                        $result["retCode"] = 1000;
                    }
                }
                else {
                    $p_data['sql'] = "insert into account_level_list (level, account_name, account_bank, account_number, bank_id, display_account_bank) ";
                    $p_data['sql'] .= " values ($i ";
                    $p_data['sql'] .= ", '".$level_account[$i]['account_name']."' ";
                    $p_data['sql'] .= ", '".$level_account[$i]['account_bank']."' ";
                    $p_data['sql'] .= ", '".$level_account[$i]['account_number']."' ";
                    $p_data['sql'] .= ", '".$accountList[$level_account[$i]['account_bank']]."' ";
                    $p_data['sql'] .= ", '".$level_account[$i]['display_account_bank']."' ";
                    $p_data['sql'] .= " ); ";
                    
                    //$dbRet = $MEMAdminDAO->setQueryData($p_data);
                    
                    $log_data = "[레벨별 계좌 설정] [$i level 등록]";
                    if ($dbRet) {
                        $p_data['sql'] = "insert into  t_adm_log ";
                        $p_data['sql'] .= " (a_id, log_data, log_type) ";
                        $p_data['sql'] .= " values('".$admin_id."', '".$log_data."', 4) ";
                        
                        $MEMAdminDAO->setQueryData($p_data);
                        
                        $result["retCode"] = 1000;
                        $result["retData"] = '';
                    }
                }
                
                    
            }
        }
    }
    
    
    $MEMAdminDAO->dbclose();
}


echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
