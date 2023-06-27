<?php
$UTIL = new CommonUtil();

    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['aid']))
    {
        $msg = "로그인 후 이용해 주세요.";
        $re_url = "/login.php";

        $UTIL->alertLocation($msg, $re_url);
        CommonUtil::logWrite('여기다2','info');
        exit;
    }
?>