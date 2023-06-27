<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');

include_once(_DAOPATH . '/class_Admin_Member_dao.php');
include_once(_LIBPATH . '/class_Code.php');

$UTIL = new CommonUtil();

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    $p_data['u_id'] = trim(isset($_POST['u_id']) ? $MEMAdminDAO->real_escape_string($_POST['u_id']) : '');
    $p_data['u_pass'] = trim(isset($_POST['u_pass']) ? $MEMAdminDAO->real_escape_string($_POST['u_pass']) : '');

    if (($p_data['u_id'] == '') || ($p_data['u_pass'] == '')) {
        $result['retCode'] = 2001;
        $result['retMsg'] = '아이디 또는 비밀번호를 정확히 입력해 주세요.';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        $MEMAdminDAO->dbclose();
        exit;
    }
    
    $now_ip = CommonUtil::get_client_ip();
    // 로그인 허용 아이피 체크
    $login_allowable = CommonUtil::checkWhiteList($MEMAdminDAO,$now_ip);

    /*if ('14.52.211.218' == $now_ip) {
        $login_allowable = true;
    }*/
    
    // 특정 도메인 통과
    $sql = " select type, domain from t_domain_code where is_use = 1 and type in (1,2,3)";
    $domainResult = $MEMAdminDAO->getQueryData_pre($sql,[]);
    $domainList = array();
    foreach ($domainResult as $key => $value) {
        $domainList[$value['type']] = $value['domain'];
        
        /*if (preg_match("/{$value['domain']}/i", $_SERVER['HTTP_HOST'])) {
            $login_allowable = true;
        }*/
    }
    
    $searchHQ = $domainList[DOMAIN_HO];
    $searchDis = $domainList[DOMAIN_BO];
    $searchGaro = $domainList[DOMAIN_GARO];
    /*$searchHQ = "gm-topclass.com";
    if (preg_match("/{$searchHQ}/i", $_SERVER['HTTP_HOST'])) {
        $login_allowable = true;
    }
    
    $searchDis = "gm-7979.com";*/
    if (preg_match("/{$searchDis}/i", $_SERVER['HTTP_HOST'])) {
        $login_allowable = true;
    }
    
    if (preg_match("/{$searchGaro}/i", $_SERVER['HTTP_HOST'])) {
        $login_allowable = true;
    }

    CommonUtil::logWrite("_login_prc now_ip: " . $now_ip, "info");
    
    if('171.253.139.245' == $now_ip){
        $login_allowable = false;
    }


    // @TODO Remove from comments after development
    // if (!$login_allowable) {
    //     $result['retCode'] = 2002;
    //     $result['retMsg'] = '접근이 불가능한 IP 입니다.';
    //     echo json_encode($result, JSON_UNESCAPED_UNICODE);
    //     $MEMAdminDAO->dbclose();
    //     exit;
    // }
    //@END_TODO


    // 특정 아이피면 일본 아이피로 로그 남기게 한다.
    //if ('14.52.211.218' == $now_ip)
    //    $now_ip = '180.54.70.87';

    $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
    $retData = $MEMAdminDAO->getQueryData($p_data);
    $st_country = $retData[0]['a_country'];
    
    /*if($st_country == '미국'){
        $result['retCode'] = 2002;
        $result['retMsg'] = '접근이 불가능한 IP 입니다.';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        $MEMAdminDAO->dbclose();
        exit;
    }*/
    
    $u_id = $p_data['u_id'];
    $p_data['sql'] = "select 0 as idx,a_id      ,a_nick             ,      a_ip,     is_main_stat,      grade,0 as u_business,a_pw   from t_adm_user where a_id= ? 
                       UNION 
                      select idx ,id as a_id,nick_name as a_nick,'' as a_ip, 1 as is_main_stat,1 as grade,     u_business,password as a_pw from member where id = ? ";
    $retData = $MEMAdminDAO->getQueryData_pre($p_data['sql'],[$u_id, $u_id]);
    CommonUtil::logWrite("_login_prc prepare: " . json_encode($retData), "info");

    $grade = 1;
    if (password_verify($p_data['u_pass'], $retData[0]['a_pw'])) {
        $rand = mt_rand() . $p_data['u_id'];

        $db_aid = $retData[0]['a_id'];
        $db_anick = $retData[0]['a_nick'];
        $db_aip = $retData[0]['a_ip'];
        $db_is_main_stat = true === isset($retData[0]['is_main_stat']) ? $retData[0]['is_main_stat'] : 0;
        $grade = $retData[0]['grade'];
        $member_idx = $retData[0]['idx'];
        $u_business = $retData[0]['u_business'];
        

        if (1 == $u_business) {
            $result['retCode'] = 2001;
            $result['retMsg'] = '일반회원은 로그인 할수 없습니다.';
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            $MEMAdminDAO->dbclose();
            exit;
        }

        $s_key = md5($rand);
        CommonUtil::logWrite("_login_prc u_business: " . $u_business, "info");
        CommonUtil::logWrite("_login_prc HTTP_HOST: " . $_SERVER['HTTP_HOST'], "info");
        CommonUtil::logWrite("_login_prc searchDis: " . $searchDis, "info");
        if (0 == $u_business) {

            $p_data['sql'] = " update t_adm_user SET session_key=?, last_login=now(), a_ip=? ";
            $p_data['sql'] .= " ,a_before_ip= ? WHERE a_id=? ";
            
            // 가로계정은 도메인 상관없이 통과
            if(2 != $grade){
                // 본사계정이면 본사도메인으로 로그인 해야한다.
                if (preg_match("/{$searchDis}/i", $_SERVER['HTTP_HOST'])) {
                    $result['retCode'] = 2001;
                    $result['retMsg'] = 'Error 404 not found!';
                    echo json_encode($result, JSON_UNESCAPED_UNICODE);
                    $MEMAdminDAO->dbclose();
                    exit;
                }

                if (preg_match("/{$searchGaro}/i", $_SERVER['HTTP_HOST'])) {
                    $result['retCode'] = 2001;
                    $result['retMsg'] = 'Error 404 not found!';
                    echo json_encode($result, JSON_UNESCAPED_UNICODE);
                    $MEMAdminDAO->dbclose();
                    exit;
                }
            }
        } else {
            $p_data['sql'] = " update member SET session_key=?, last_login=now(), a_ip=? ";
            $p_data['sql'] .= " ,a_before_ip=? WHERE id=? ";
            
            // 총판계정이면 총판도메인으로 로그인 해야한다.
            if (preg_match("/{$searchHQ}/i", $_SERVER['HTTP_HOST'])) {
                $result['retCode'] = 2001;
                $result['retMsg'] = 'Error 404 not found!';
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                $MEMAdminDAO->dbclose();
                exit;
            }
        }
        $MEMAdminDAO->setQueryData_pre($p_data['sql'],[$s_key,$now_ip,'',$db_aid]);

        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, is_login, log_type) ";
        $p_data['sql'] .= " values(?,?,?, 'Y', 1) ;";

        // 특정 아이디면 로그를 남기지 않는다.
        $search = TEST_ID;
        if (!preg_match("/{$search}/i", $p_data['u_id'])) {
            $MEMAdminDAO->setQueryData_pre($p_data['sql'],[$p_data['u_id'],$now_ip,$st_country]);
        }

        session_start();
        $_SESSION['member_idx'] = $member_idx;
        $_SESSION['aid'] = $db_aid;
        $_SESSION['anick'] = $db_anick;
        $_SESSION['akey'] = $s_key;
        $_SESSION['is_main_stat'] = $db_is_main_stat;
        $_SESSION['grade'] = $grade;
        $_SESSION['u_business'] = $u_business;
        $_SESSION['member_idx'] = $member_idx;
    } else {
        $p_data['sql'] = "insert into  t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, is_login, log_type) ";
        $p_data['sql'] .= " values(?,?,?, 'N', 1) ;";

        // 특정 아이디면 로그를 남기지 않는다.
        $search = TEST_ID;
        if (!preg_match("/{$search}/i", $p_data['u_id'])) {
            $MEMAdminDAO->setQueryData_pre($p_data['sql'],[$p_data['u_id'],$now_ip,$st_country]);
        }
        
        $result['retCode'] = 2001;
        $result['retMsg'] = '아이디 또는 비밀번호가 틀렸습니다.';
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        $MEMAdminDAO->dbclose();
        exit;
    }

    $MEMAdminDAO->dbclose();
    $result['retCode'] = 1000;
    $result['grade'] = $grade;
    $result['u_business'] = $u_business;
    $result['member_idx'] = $member_idx;
    //CommonUtil::logWrite("_login_prc 1000: " , "info");
} else {
    $result['retCode'] = 2001;
    
    CommonUtil::logWrite("_login_prc 2001: " , "info");
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
