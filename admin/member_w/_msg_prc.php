<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
header('Content-Type: text/html; charset=UTF-8');

include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

if (!isset($_SESSION)) {
    session_start();
}

$p_data['aid'] = $_SESSION['aid'];


$u_idxArr = null;

if ($_POST['sel_user'] != '') {
    $u_idxArr = $_POST['sel_user'];
}

$p_data['member_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);

$p_data['sel_level'] = trim(isset($_POST['sel_level']) ? $_POST['sel_level'] : 0);
$p_data['setUserType'] = trim(isset($_POST['setUserType']) ? $_POST['setUserType'] : '');

$p_data['msg_title'] = trim(isset($_POST['msg_title']) ? $_POST['msg_title'] : '');
$p_data['sel_user'] = trim(isset($_POST['sel_user']) ? $_POST['sel_user'] : '');

$p_content_buff = trim(isset($_POST['msg_content']) ? $_POST['msg_content'] : '');
$p_content = (urldecode($p_content_buff));
$p_data['msg_content'] = htmlspecialchars(addslashes($p_content));

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    $p_data['member_idx'] = $MEMAdminDAO->real_escape_string($p_data['member_idx']);
    $p_data['sel_level'] = $MEMAdminDAO->real_escape_string($p_data['sel_level']);
    $p_data['setUserType'] = $MEMAdminDAO->real_escape_string($p_data['setUserType']);

    $p_data['msg_title'] = $MEMAdminDAO->real_escape_string($p_data['msg_title']);
    $p_data['sel_user'] = $MEMAdminDAO->real_escape_string($p_data['sel_user']);

    try {

        if (!$MEMAdminDAO->trans_start()) {
            // start transaction error
            CommonUtil::logWrite("_msg_prc", "error");
            $MEMAdminDAO->dbclose();
            return;
        }

        $p_data['msg_key'] = date("YmdHis") . substr(microtime(), 2, 6) . rand(10000, 99999);

        $dbRet_1 = $MEMAdminDAO->setMsgSendList($p_data);

        if (FAIL_DB_SQL_EXCEPTION === $dbRet_1) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        $p_data['msg_idx'] = $dbRet_1[0]['idx'];


        if ($p_data['setUserType'] == 'alluser') {
            $dbRet = $MEMAdminDAO->setMsgSendUser($p_data);
            if (FAIL_DB_SQL_EXCEPTION === $dbRet) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
        } else if ($p_data['setUserType'] == 'seluser') {

            if (($p_data['sel_level'] > 0) && ($p_data['sel_level'] <= 10)) {

                $in_data = "select " . $p_data['msg_idx'] . ", idx from member where level=" . $p_data['sel_level'] . " ";

                $p_data['sql'] = "INSERT INTO t_message (msg_idx, member_idx) $in_data ";

                if (FAIL_DB_SQL_EXCEPTION === $MEMAdminDAO->setQueryData($p_data)) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }
            }


            if (count($u_idxArr) > 0) {

                $p_data['sql'] = "INSERT INTO t_message (msg_idx, member_idx) VALUES ";
                $in_sql = "";

                foreach ($u_idxArr as $uidx) {
                    if ($in_sql != '') {
                        $in_sql .= ", ";
                    }

                    $in_sql .= " (" . $p_data['msg_idx'] . ", $uidx) ";
                }

                if ($in_sql != '') {
                    $p_data['sql'] .= $in_sql;

                    if (FAIL_DB_SQL_EXCEPTION === $MEMAdminDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                }
            }
        } else if ($p_data['setUserType'] == 'selUserList') {

            $arr = explode(',', $p_data['sel_user']);
            for ($i = 0; $i < count($arr); $i++) {

                if (count($arr) > 0) {
                    $p_data['sql'] = "INSERT INTO t_message (msg_idx, member_idx) VALUES (" . $p_data['msg_idx'] . ", " . $arr[$i] . ") ";

                    if (FAIL_DB_SQL_EXCEPTION === $MEMAdminDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                }
            }
        } else {
            $dbRet = $MEMAdminDAO->setMsgSendUser($p_data);
            if (FAIL_DB_SQL_EXCEPTION === $dbRet) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
        }

        $MEMAdminDAO->commit();

        if ($p_data['setUserType'] == 'alluser') {
            $db_ret = $dbRet[0]['ret'];

            if ($db_ret == 1000) {
                $result["retCode"] = 1000;
                $result['retMsg'] = "메세지를 전송 하였습니다.";
            } else {
                $UTIL->checkFailType('2199', '', '', 'json');
                exit;
            }
        } else {
            $result["retCode"] = 1000;
            $result['retMsg'] = "메세지를 전송 하였습니다.";
        }
    } catch (\mysqli_sql_exception $e) {
        CommonUtil::logWrite('_msg_prc to 2' . $e->getMessage(), "db_error");
        $MEMAdminDAO->rollback();
        $result['retCode'] =  FAIL_DB_SQL_EXCEPTION;
        $result['retMsg']  =  FAIL_DB_SQL_EXCEPTION_MSG;
    } catch (\Exception $e) {
        $UTIL->logWrite("[_msg_prc] [error -2]", "error");
        $MEMAdminDAO->rollback();
        $result['retCode'] = -3;
        $result['retMsg'] = 'Exception 예외발생';
    } finally {
        $MEMAdminDAO->dbclose();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
} else {
    $UTIL->logWrite("[_msg_prc] [error 2200]", "error");
    $UTIL->checkFailType('2200', '', '', 'json');
    exit;
}
?>
