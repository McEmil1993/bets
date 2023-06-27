<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_LSports_Bet_dao.php');

$LSportsAdminDAO = new Admin_LSports_Bet_DAO(_DB_NAME_WEB);
$db_conn = $LSportsAdminDAO->dbconnect();

$level = $_POST['level'];
$charge_type = $_POST['charge_type'];
$name = $_POST['name'];

$bonus_1_charge_first_money = $_POST['bonus_1_charge_first_money'];
$bonus_1_charge_first_per = $_POST['bonus_1_charge_first_per'];
$bonus_1_charge_first_max_money = $_POST['bonus_1_charge_first_max_money'];
$bonus_1_charge_money = $_POST['bonus_1_charge_money'];
$bonus_1_charge_per = $_POST['bonus_1_charge_per'];
$bonus_1_charge_max_money = $_POST['bonus_1_charge_max_money'];

$bonus_2_charge_first_money = $_POST['bonus_2_charge_first_money'];
$bonus_2_charge_first_per = $_POST['bonus_2_charge_first_per'];
$bonus_2_charge_first_max_money = $_POST['bonus_2_charge_first_max_money'];
$bonus_2_charge_money = $_POST['bonus_2_charge_money'];
$bonus_2_charge_per = $_POST['bonus_2_charge_per'];
$bonus_2_charge_max_money = $_POST['bonus_2_charge_max_money'];

$bonus_3_charge_first_money = $_POST['bonus_3_charge_first_money'];
$bonus_3_charge_first_per = $_POST['bonus_3_charge_first_per'];
$bonus_3_charge_first_max_money = $_POST['bonus_3_charge_first_max_money'];
$bonus_3_charge_money = $_POST['bonus_3_charge_money'];
$bonus_3_charge_per = $_POST['bonus_3_charge_per'];
$bonus_3_charge_max_money = $_POST['bonus_3_charge_max_money'];

$bonus_4_charge_first_money = $_POST['bonus_4_charge_first_money'];
$bonus_4_charge_first_per = $_POST['bonus_4_charge_first_per'];
$bonus_4_charge_first_max_money = $_POST['bonus_4_charge_first_max_money'];
$bonus_4_charge_money = $_POST['bonus_4_charge_money'];
$bonus_4_charge_per = $_POST['bonus_4_charge_per'];
$bonus_4_charge_max_money = $_POST['bonus_4_charge_max_money'];

$bonus_5_charge_first_money = $_POST['bonus_5_charge_first_money'];
$bonus_5_charge_first_per = $_POST['bonus_5_charge_first_per'];
$bonus_5_charge_first_max_money = $_POST['bonus_5_charge_first_max_money'];
$bonus_5_charge_money = $_POST['bonus_5_charge_money'];
$bonus_5_charge_per = $_POST['bonus_5_charge_per'];
$bonus_5_charge_max_money = $_POST['bonus_5_charge_max_money'];

if($db_conn) {
    $result["retCode"]	= 1000;
    $result['retMsg']	= 'success';

    try {
        //$sql = "update charge_type set charge_type = $charge_type, name = '$name' where level = $level;";
        $sql = "update charge_type set charge_type = ?, name = ?"
                . ", bonus_1_charge_first_money = ?, bonus_1_charge_first_per = ?, bonus_1_charge_first_max_money = ?"
                . ", bonus_1_charge_money = ?, bonus_1_charge_per = ?, bonus_1_charge_max_money = ?"
                . ", bonus_2_charge_first_money = ?, bonus_2_charge_first_per = ?, bonus_2_charge_first_max_money = ?"
                . ", bonus_2_charge_money = ?, bonus_2_charge_per = ?, bonus_2_charge_max_money = ?"
                . ", bonus_3_charge_first_money = ?, bonus_3_charge_first_per = ?, bonus_3_charge_first_max_money = ?"
                . ", bonus_3_charge_money = ?, bonus_3_charge_per = ?, bonus_3_charge_max_money = ?"
                . ", bonus_4_charge_first_money = ?, bonus_4_charge_first_per = ?, bonus_4_charge_first_max_money = ?"
                . ", bonus_4_charge_money = ?, bonus_4_charge_per = ?, bonus_4_charge_max_money = ?"
                . ", bonus_5_charge_first_money = ?, bonus_5_charge_first_per = ?, bonus_5_charge_first_max_money = ?"
                . ", bonus_5_charge_money = ?, bonus_5_charge_per = ?, bonus_5_charge_max_money = ?"
                . " where level = ?;";
        $arrData = array($charge_type, $name
                            , $bonus_1_charge_first_money, $bonus_1_charge_first_per, $bonus_1_charge_first_max_money
                            , $bonus_1_charge_money, $bonus_1_charge_per, $bonus_1_charge_max_money
                            , $bonus_2_charge_first_money, $bonus_2_charge_first_per, $bonus_2_charge_first_max_money
                            , $bonus_2_charge_money, $bonus_2_charge_per, $bonus_2_charge_max_money
                            , $bonus_3_charge_first_money, $bonus_3_charge_first_per, $bonus_3_charge_first_max_money
                            , $bonus_3_charge_money, $bonus_3_charge_per, $bonus_3_charge_max_money
                            , $bonus_4_charge_first_money, $bonus_4_charge_first_per, $bonus_4_charge_first_max_money
                            , $bonus_4_charge_money, $bonus_4_charge_per, $bonus_4_charge_max_money
                            , $bonus_5_charge_first_money, $bonus_5_charge_first_per, $bonus_5_charge_first_max_money
                            , $bonus_5_charge_money, $bonus_5_charge_per, $bonus_5_charge_max_money
                            , $level);
        $LSportsAdminDAO->setQueryData_pre($sql, $arrData);
        
        $now_ip = CommonUtil::get_client_ip();
        $admin_id = $_SESSION['aid'];
        $log_data = '충전방식 변경 => ' . json_encode($_POST, JSON_UNESCAPED_UNICODE);
        $p_data['sql'] = "SELECT FN_GET_IP_COUNTRY('$now_ip') as a_country; ";
        $retData = $LSportsAdminDAO->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $retData) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $str_country = $retData[0]['a_country'];

        $p_data['sql'] = "insert into t_adm_log ";
        $p_data['sql'] .= " (a_id, a_ip, a_country, log_data, log_type) ";
        $p_data['sql'] .= " values('$admin_id','$now_ip','$str_country','$log_data', ".CHARGE_TYPE_CHANGE.");";

        if (FAIL_DB_SQL_EXCEPTION === $LSportsAdminDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    } catch (\mysqli_sql_exception $e) {
        CommonUtil::logWrite('_set_charge_type' . $e->getMessage(), "db_error");
        $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
        $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
    }catch (\Exception $e) {
        $UTIL->logWrite("[_set_charge_type] [error -2]", "error");
        $result['retCode'] = -3;
        $result['retMsg'] = 'Exception 예외발생';

    } catch (\ReflectionException $e) {
        CommonUtil::logWrite('::::::::::::::: _set_charge_type to 2 ReflectionException : ' . $e, "error");
        $result['retCode'] = -4;
        $result['retMsg'] = 'Exception 예외발생';

    } finally {
        $LSportsAdminDAO->dbclose();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    
}
?>
