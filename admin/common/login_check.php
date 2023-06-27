<?php

//////// login check
$UTIL = new CommonUtil();
if (!isset($_SESSION)) {
    session_start();

    if (!isset($_SESSION['aid'])) {
        $msg = "로그인 후 이용해 주세요.";
        $re_url = "/login.php";

        $UTIL->alertLocation($msg, $re_url);
        CommonUtil::logWrite('여기다2','info');
        exit;
    }

    $COMMONDAO = new Admin_Common_DAO(_DB_NAME_WEB);
    $db_conn_common = $COMMONDAO->dbconnect();

    if ($db_conn_common) {
        $u_business = $_SESSION['u_business'];
        if (0 == $u_business) {
            $p_data['sql'] = " SELECT a_nick, session_key,grade FROM t_adm_user ";
            $p_data['sql'] .= " WHERE a_id = '" . $_SESSION['aid'] . "' ";
        } else {
            $p_data['sql'] = " SELECT nick_name as a_nick, session_key,1 as grade FROM member ";
            $p_data['sql'] .= " WHERE id = '" . $_SESSION['aid'] . "' ";
        }

        $db_dataLogin = $COMMONDAO->getQueryData($p_data);
        $db_session_key = $db_dataLogin[0]['session_key'];

        $bLogin = TRUE;
        $msg = "로그인 후 이용해 주세요.";

        if ($db_session_key == '') {
            $bLogin = FALSE;
        } else if ($db_session_key != $_SESSION['akey']) {
            $msg = "다른 곳에서 로그인 하여 자동 로그 아웃 처리 됩니다.";
            $bLogin = FALSE;
        }

        $COMMONDAO->dbclose();

        if ($bLogin == FALSE) {
            $re_url = "/login.php";

            $UTIL->alertLocation($msg, $re_url);
            CommonUtil::logWrite('여기다3','info');
            exit;
        }

        if (2 == $db_dataLogin[0]['grade']) {
            if ('/read_admin/read_mem_list.php' != $_SERVER['PHP_SELF'] && 'login.php' != $_SERVER['PHP_SELF']) {
                $msg = "해당사이트로는 접속할수없습니다.";
                //$msg = $_SERVER['PHP_SELF'];
                $re_url = "/read_admin/read_mem_list.php?srch_status=11&srch_level=1";

                $UTIL->alertLocation($msg, $re_url);
                exit;
            }
        }
    }
} else {
    $msg = "로그인 후 이용해 주세요.";
    $re_url = "/login.php";

    $UTIL->alertLocation($msg, $re_url);
    CommonUtil::logWrite('여기다4','info');
    exit;
}
//////// login check end
?>