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
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');

$member_idx = trim(isset($_POST['member_idx']) ? $_POST['member_idx'] : 0);
$bet_pre_s_fee = trim(isset($_POST['bet_pre_s_fee']) ? $_POST['bet_pre_s_fee'] : 0);
$bet_pre_d_fee = trim(isset($_POST['bet_pre_d_fee']) ? $_POST['bet_pre_d_fee'] : 0);
$bet_pre_d_2_fee = trim(isset($_POST['bet_pre_d_2_fee']) ? $_POST['bet_pre_d_2_fee'] : 0);
$bet_pre_d_3_fee = trim(isset($_POST['bet_pre_d_3_fee']) ? $_POST['bet_pre_d_3_fee'] : 0);
$bet_pre_d_4_fee = trim(isset($_POST['bet_pre_d_4_fee']) ? $_POST['bet_pre_d_4_fee'] : 0);
$bet_pre_d_5_more_fee = trim(isset($_POST['bet_pre_d_5_more_fee']) ? $_POST['bet_pre_d_5_more_fee'] : 0);
$bet_real_s_fee = trim(isset($_POST['bet_real_s_fee']) ? $_POST['bet_real_s_fee'] : 0);
$bet_real_d_fee = trim(isset($_POST['bet_real_d_fee']) ? $_POST['bet_real_d_fee'] : 0);
$bet_mini_fee = trim(isset($_POST['bet_mini_fee']) ? $_POST['bet_mini_fee'] : 0);
$pre_s_fee = trim(isset($_POST['pre_s_fee']) ? $_POST['pre_s_fee'] : 0);
$real_s_fee = trim(isset($_POST['real_s_fee']) ? $_POST['real_s_fee'] : 0);
$real_d_fee = trim(isset($_POST['real_d_fee']) ? $_POST['real_d_fee'] : 0);
$mini_fee = trim(isset($_POST['mini_fee']) ? $_POST['mini_fee'] : 0);
$bet_casino_fee = trim(isset($_POST['bet_casino_fee']) ? $_POST['bet_casino_fee'] : 0);
$bet_slot_fee = trim(isset($_POST['bet_slot_fee']) ? $_POST['bet_slot_fee'] : 0);
$bet_esports_fee = trim(isset($_POST['bet_esports_fee']) ? $_POST['bet_esports_fee'] : 0);
$bet_hash_fee = trim(isset($_POST['bet_hash_fee']) ? $_POST['bet_hash_fee'] : 0);
$bet_holdem_fee = trim(isset($_POST['bet_holdem_fee']) ? $_POST['bet_holdem_fee'] : 0);
$p_data['second_pass'] = trim(isset($_POST['second_pass']) ? $_POST['second_pass'] : '');

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

$result["retCode"] = 2001;
$result["retData"] = '';

do {
    try {
        if($db_conn) {
             $member_idx = $MEMAdminDAO->real_escape_string($member_idx);
             $bet_pre_s_fee = $MEMAdminDAO->real_escape_string($bet_pre_s_fee);
             $bet_pre_d_fee = $MEMAdminDAO->real_escape_string($bet_pre_d_fee);
             $bet_pre_d_2_fee = $MEMAdminDAO->real_escape_string($bet_pre_d_2_fee);
             $bet_pre_d_3_fee = $MEMAdminDAO->real_escape_string($bet_pre_d_3_fee);
             $bet_pre_d_4_fee = $MEMAdminDAO->real_escape_string($bet_pre_d_4_fee);
             $bet_pre_d_5_more_fee = $MEMAdminDAO->real_escape_string($bet_pre_d_5_more_fee);

             $bet_real_s_fee = $MEMAdminDAO->real_escape_string($bet_real_s_fee);
             $bet_real_d_fee = $MEMAdminDAO->real_escape_string($bet_real_d_fee);

             $bet_mini_fee = $MEMAdminDAO->real_escape_string($bet_mini_fee);
             $pre_s_fee = $MEMAdminDAO->real_escape_string($pre_s_fee);
             $real_s_fee = $MEMAdminDAO->real_escape_string($real_s_fee);
             $real_d_fee = $MEMAdminDAO->real_escape_string($real_d_fee);
             $mini_fee = $MEMAdminDAO->real_escape_string($mini_fee);
             $bet_casino_fee = $MEMAdminDAO->real_escape_string($bet_casino_fee);
             $bet_slot_fee = $MEMAdminDAO->real_escape_string($bet_slot_fee);
             $bet_esports_fee = $MEMAdminDAO->real_escape_string($bet_esports_fee);
             $bet_hash_fee = $MEMAdminDAO->real_escape_string($bet_hash_fee);
             $bet_holdem_fee = $MEMAdminDAO->real_escape_string($bet_holdem_fee);
             $p_data['second_pass'] = $MEMAdminDAO->real_escape_string($p_data['second_pass']);

            //2차인증 체크
            $p_data['sql'] = "select set_type_val from t_game_config where set_type='second_pass'";
            $second_pass = $MEMAdminDAO->getQueryData_pre($p_data['sql'], [])[0];
            if(hash('sha512', $p_data['second_pass']) != $second_pass['set_type_val']){
                $result['retCode'] = 2002;
                break;
            }

            // 부모
            $p_data['sql'] = "select recommend_member from member where idx = ?";
            $dbResult = $MEMAdminDAO->getQueryData_pre($p_data['sql'], [$member_idx]);
            $recommend_member = $dbResult[0]['recommend_member'];
            
            $arr_qa_marks = array('?');
            $arr_param = array($member_idx);
            if(0 < $recommend_member){
                array_push($arr_qa_marks, '?');
                array_push($arr_param, $recommend_member);
            }
            $arr_qa_marks = implode(',', $arr_qa_marks);
            
            $be_data = $af_data = $arrShopConfig = array();
            $p_data['sql'] = "select * from shop_config where member_idx in ($arr_qa_marks)";
            $dbResult = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $arr_param);
            foreach ($dbResult as $key => $value) {
                $arrShopConfig[$value['member_idx']] = $value;
            }
            
            if(count($dbResult) > 0){
                $myShopConfig = $arrShopConfig[$member_idx];
                
                // 상위총판의 요율설정을 넘을수 없다.
                if(2 == count($dbResult)){
                    $pShopConfig = $arrShopConfig[$recommend_member];
                    if($pShopConfig['bet_pre_s_fee'] < $bet_pre_s_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_pre_d_fee'] < $bet_pre_d_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_pre_d_2_fee'] < $bet_pre_d_2_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_pre_d_3_fee'] < $bet_pre_d_3_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_pre_d_4_fee'] < $bet_pre_d_4_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_pre_d_5_more_fee'] < $bet_pre_d_5_more_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_real_s_fee'] < $bet_real_s_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_real_d_fee'] < $bet_real_d_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_mini_fee'] < $bet_mini_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_casino_fee'] < $bet_casino_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_slot_fee'] < $bet_slot_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_esports_fee'] < $bet_esports_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['bet_hash_fee'] < $bet_hash_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                    
                    if($pShopConfig['pre_s_fee'] < $pre_s_fee){
                        $result['retCode'] = 2003;
                        break;
                    }
                }
                
                if($myShopConfig['bet_pre_s_fee'] != $bet_pre_s_fee){
                    $be_data[] = '프리싱글% : '.$myShopConfig['bet_pre_s_fee'];
                    $af_data[] = '프리싱글% : '.$bet_pre_s_fee;
                }

                if($myShopConfig['bet_pre_d_fee'] != $bet_pre_d_fee){
                    $be_data[] = '프리멀티% : '.$myShopConfig['bet_pre_d_fee'];
                    $af_data[] = '프리멀티% : '.$bet_pre_d_fee;
                }

                if($myShopConfig['bet_pre_d_2_fee'] != $bet_pre_d_2_fee){
                    $be_data[] = '프리멀티 2폴% : '.$myShopConfig['bet_pre_d_2_fee'];
                    $af_data[] = '프리멀티 2폴% : '.$bet_pre_d_2_fee;
                }

                if($myShopConfig['bet_pre_d_3_fee'] != $bet_pre_d_3_fee){
                    $be_data[] = '프리멀티 3폴% : '.$myShopConfig['bet_pre_d_3_fee'];
                    $af_data[] = '프리멀티 3폴% : '.$bet_pre_d_3_fee;
                }

                if($myShopConfig['bet_pre_d_4_fee'] != $bet_pre_d_4_fee){
                    $be_data[] = '프리멀티 4폴% : '.$myShopConfig['bet_pre_d_4_fee'];
                    $af_data[] = '프리멀티 4폴% : '.$bet_pre_d_4_fee;
                }

                if($myShopConfig['bet_pre_d_5_more_fee'] != $bet_pre_d_5_more_fee){
                    $be_data[] = '프리멀티 5폴% : '.$myShopConfig['bet_pre_d_5_more_fee'];
                    $af_data[] = '프리멀티 5폴% : '.$bet_pre_d_5_more_fee;
                }

                if($myShopConfig['bet_real_s_fee'] != $bet_real_s_fee){
                    $be_data[] = '실시간싱글% : '.$myShopConfig['bet_real_s_fee'];
                    $af_data[] = '실시간싱글% : '.$bet_real_s_fee;
                }

                if($myShopConfig['bet_real_d_fee'] != $bet_real_d_fee){
                    $be_data[] = '실시간멀티% : '.$myShopConfig['bet_real_d_fee'];
                    $af_data[] = '실시간멀티% : '.$bet_real_d_fee;
                }

                if($myShopConfig['bet_mini_fee'] != $bet_mini_fee){
                    $be_data[] = '미니게임% : '.$myShopConfig['bet_mini_fee'];
                    $af_data[] = '미니게임% : '.$bet_mini_fee;
                }

                if($myShopConfig['bet_casino_fee'] != $bet_casino_fee){
                    $be_data[] = '카지노% : '.$myShopConfig['bet_casino_fee'];
                    $af_data[] = '카지노% : '.$bet_casino_fee;
                }

                if($myShopConfig['bet_slot_fee'] != $bet_slot_fee){
                    $be_data[] = '슬롯% : '.$myShopConfig['bet_slot_fee'];
                    $af_data[] = '슬롯% : '.$bet_slot_fee;
                }

                if($myShopConfig['bet_esports_fee'] != $bet_esports_fee){
                    $be_data[] = '이스포츠% : '.$myShopConfig['bet_esports_fee'];
                    $af_data[] = '이스포츠% : '.$bet_esports_fee;
                }

                if($myShopConfig['bet_hash_fee'] != $bet_hash_fee){
                    $be_data[] = '해쉬% : '.$myShopConfig['bet_hash_fee'];
                    $af_data[] = '해쉬% : '.$bet_hash_fee;
                }
                
                if($myShopConfig['bet_holdem_fee'] != $bet_holdem_fee){
                    $be_data[] = '홀덤% : '.$myShopConfigs['bet_holdem_fee'];
                    $af_data[] = '홀덤% : '.$bet_holdem_fee;
                }

                if($myShopConfig['pre_s_fee'] != $pre_s_fee){
                    $be_data[] = '입출% : '.$myShopConfig['pre_s_fee'];
                    $af_data[] = '입출% : '.$pre_s_fee;
                }

                $be_data = implode(', ', $be_data);
                $af_data = implode(', ', $af_data);
            }

            $sql = '('
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?, '
                    . '?)';

                    $p_data['sql'] = 
                        'INSERT INTO `shop_config` ('
                        . 'member_idx, '
                        . 'bet_pre_s_fee, '
                        . 'bet_pre_d_fee, '
                        . 'bet_pre_d_2_fee, '
                        . 'bet_pre_d_3_fee, '
                        . 'bet_pre_d_4_fee, '
                        . 'bet_pre_d_5_more_fee, '
                        . 'bet_real_s_fee, '
                        . 'bet_real_d_fee, '
                        . 'bet_mini_fee, '
                        . 'pre_s_fee, '
                        . 'bet_casino_fee, '
                        . 'bet_slot_fee, '
                        . 'bet_esports_fee, '
                        . 'bet_hash_fee, '
                        . 'bet_holdem_fee) VALUES '
                        . $sql
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'bet_pre_s_fee = ?, '
                        . 'bet_pre_d_fee = ?, '
                        . 'bet_pre_d_2_fee = ?, '
                        . 'bet_pre_d_3_fee = ?, '
                        . 'bet_pre_d_4_fee = ?, '
                        . 'bet_pre_d_5_more_fee = ?, '
                        . 'bet_real_s_fee = ?, '
                        . 'bet_real_d_fee = ?, '
                        . 'bet_mini_fee = ?, '
                        . 'pre_s_fee = ?, '
                        . 'bet_casino_fee = ?, '
                        . 'bet_slot_fee = ?, '
                        . 'bet_esports_fee = ?, '
                        . 'bet_hash_fee = ?, '
                        . 'bet_holdem_fee = ?';
            //echo $p_data['sql'];
            $MEMAdminDAO->setQueryData_pre($p_data['sql'], [$member_idx, $bet_pre_s_fee, $bet_pre_d_fee, $bet_pre_d_2_fee, $bet_pre_d_3_fee,
                $bet_pre_d_4_fee, $bet_pre_d_5_more_fee, $bet_real_s_fee, $bet_real_d_fee, $bet_mini_fee,
                $pre_s_fee, $bet_casino_fee, $bet_slot_fee, $bet_esports_fee, $bet_hash_fee, $bet_holdem_fee,
                $bet_pre_s_fee, $bet_pre_d_fee, $bet_pre_d_2_fee, $bet_pre_d_3_fee, $bet_pre_d_4_fee,
                $bet_pre_d_5_more_fee, $bet_real_s_fee, $bet_real_d_fee, $bet_mini_fee, $pre_s_fee,
                $bet_casino_fee, $bet_slot_fee, $bet_esports_fee, $bet_hash_fee, $bet_holdem_fee]);
            $result["retCode"]	= 1000;

            // 변경사항이 있으면 남긴다.
            if($be_data != '' && $af_data != ''){
                $ch_type = '정산요율';
                $p_data['sql'] = " insert into  member_update_history (member_idx, update_type, before_data, after_data, a_id) ";
                $p_data['sql'] .= " values (?,?,?,?,?) ";
                $MEMAdminDAO->setQueryData_pre($p_data['sql'], [$member_idx,$ch_type,$be_data,$af_data,$admin_id]);

                // 어드민 히스토리 로그
                $now_ip = CommonUtil::get_client_ip();
                $log_data = "[정산요율 설정] 이전=>$be_data 이후=>$af_data";
                $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY(?) as a_country; ";
                $retData = $MEMAdminDAO->getQueryData_pre($p_data['sql'],[$now_ip]);
                $st_country = $retData[0]['a_country'];

                $p_data['sql'] = "insert into  t_adm_log ";
                $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
                $p_data['sql'] .= " values(?,?,?,?,29) ;";
                $MEMAdminDAO->setQueryData_pre($p_data['sql'],[$admin_id,$now_ip,$st_country,$log_data]);
            }

            $MEMAdminDAO->dbclose();
        }
    } catch (\mysqli_sql_exception $e) {
        CommonUtil::logWrite('[MYSQL EXCEPTION] _set_confirmShopConfig mysqli_sql_exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "db_error");
        $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
        $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
    } catch (\Exception $e) {
        CommonUtil::logWrite('::::::::::::::: _set_confirmShopConfig Exception : ' . $e->getMessage(), "error");
        $result['retCode'] = FAIL_EXCEPTION;
        $result['retMsg'] = FAIL_EXCEPTION_MSG;
    }
} while (0);

echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>
