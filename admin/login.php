<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');


$p_msg_num = trim(isset($_REQUEST['msg']) ? $_REQUEST['msg'] : 0);

if (!isset($_SESSION)) {
    if($p_msg_num > 0) {
        session_start();
        session_destroy();
    }
}

$UTIL = new CommonUtil();


$bUse = true;

$now_ip = CommonUtil::get_client_ip();

$LOGINDAO = new Admin_Member_DAO(_DB_NAME_WEB);

$db_conn_login = $LOGINDAO->dbconnect();
if($db_conn_login) {
    /*if('14.52.211.218' == $now_ip){
        $bUse = true;
    }*/
    
    $bUse = CommonUtil::checkWhiteList($LOGINDAO,$now_ip);
    
    // 특정 도메인 통과
    $sql = " select type, domain from t_domain_code where is_use = 'Y' and type in (2,3)";
    $domain = $LOGINDAO->getQueryData_pre($sql,[]);
    
    foreach ($domain as $key => $value) {
        if (preg_match("/{$value['domain']}/i", $_SERVER['HTTP_HOST'])) {
            $bUse = true;
        }
    }
    
    //CommonUtil::logWrite($db_dataArr);
    $LOGINDAO->dbclose();
}

$allowed_ips = ['112.204.164.222'];
if (in_array($now_ip, $allowed_ips)) {
    $bUse = true;
}

if ($bUse == false) {
    $msg = "접근이 불가능한 IP 입니다.";
    $UTIL->alertMessage($msg);
    return;
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
<!--<![endif]-->
<head>
    <title><?=TITLE?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="title" content="BetGo">
	<meta name="author" content="BetGo">
	<meta name="keywords" content="BetGo">
    <meta name="description" content="BetGo">
    <meta property="og:type" content="website" />
    
    <!--  BASE CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?=_STATIC_COMMON_PATH?>/css/material-icons.min.css" rel="stylesheet">
	<link href="<?=_STATIC_COMMON_PATH?>/css/animate.min.css" rel="stylesheet" />
	<link href="<?=_STATIC_COMMON_PATH?>/css/elements.min.css" rel="stylesheet" />
	<link href="<?=_STATIC_COMMON_PATH?>/css/default.css" rel="stylesheet">
	<!--  END BASE CSS  -->
	
	<!--  BASE JS  -->
	<script src="<?=_STATIC_COMMON_PATH?>/js/jquery-1.9.1.min.js"></script>
    <script src="<?=_STATIC_COMMON_PATH?>/js/bootstrap.min.js"></script>
    <script src="<?=_STATIC_COMMON_PATH?>/js/jquery-ui.min.js"></script>
    <script src="<?=_STATIC_COMMON_PATH?>/js/apps.js"></script>
    <!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
    <!--  END BASE JS  -->

    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    
    <?php if('ON' == IS_XSS_MODE) { ?>
        <script type="text/javascript">
            // F12 버튼 방지
            $(document).ready(function(){
                $(document).bind('keydown',function(e){
                    if ( e.keyCode == 123 /* F12 */) {
                        e.preventDefault();
                        e.returnValue = false;
                    }
                });
            });

            // 우측 클릭 방지
            document.onmousedown=disableclick;
            status="우측클릭을 허용하지 않습니다.";

            function disableclick(event){
                if (event.button==2) {
                    alert(status);
                    return false;
                }
            }
        </script>
    <?php } ?>
</head>

<body>
<div class="login_wrap">
    <!-- Login area -->
    <div class="login_bg">
        <div class="login_box">
            <?php
            $imgName = LOGIN_BG;
            ?>
            <div class="login_bi"><img src="<?=_STATIC_COMMON_PATH?>/images/common/<?=$imgName?>" alt="chosun"></div>
            <div class="login_input">
                <div class="mb10 fl" style="width:100%">
                    <i class="mte i_person vam"></i>
                    <input type="text" id="userid" name="userid" onkeyup="enterkey();" placeholder="ID" autocomplete="off" maxlength="12">
                </div>
                <div class="fl" style="width:100%">
                    <i class="mte i_lock vam"></i>
                    <input type="password" id="userpw" name="userpw" onkeyup="enterkey();" placeholder="Password" autocomplete="off" maxlength="20">
                </div>
                <button type="submit" id="btn_login" onclick="goLogin();" class="btn h50 btn_blu mt30">로그인</button>
            </div>
            <div class="login_copy">
                Copyright &copy; 2023 ODD House. All rights reserved.
            </div>
        </div>
    </div>
    <!-- END Login area -->
</div>

</body>
<script>
var useridChk =/^[a-zA-Z0-9]{4,12}$/;
var userpassChk =/^[a-zA-Z0-9!@#$%^&*()]{4,20}$/;

function enterkey() {
    if (window.event.keyCode == 13) {
    	goLogin();
    }
}

function goLogin() {
	var chk_userid = $("#userid").val();
	
	//if(chk_userid == '' || useridChk.test(chk_userid) == false) {
        if(chk_userid == '') {
		alert('아이디를 입력해 주세요.');
		$("#userid").focus();
		return false;
	}
	
	var chk_userpass = $("#userpw").val();
	if(chk_userpass == '' || userpassChk.test(chk_userpass) == false) {
		alert('비밀번호를 입력해 주세요.');
		$("#userpw").val('').focus();
		return false;
	}

	$.ajax({
		type: 'post',
		dataType: 'json',
	    url: '/login_w/_login_prc.php',
	    data:{'u_id':chk_userid, 'u_pass':chk_userpass},
	    success: function (result) {
                    console.log(result);
                    if(result['retCode'] == "1000"){

                           if(0 == result['u_business']) {
                                if(1 == result['grade'] || 3 == result['grade']){

                                    window.location.href='/siteconfig_w/adm_log_list.php';
                                    return;
                                } else{
                                    window.location.href='/read_admin/read_mem_list.php?srch_status=11';
                                    return;
                                }
                            } else{
                                  window.location.href='/member_w/mem_list.php';
                                  return;
                            }
                    }else{
                        alert(result['retMsg']);
                        return;
                    }
		},
	    error: function (request, status, error) {
                        
                        alert(error + status);
			alert('시스템 오류 입니다.');
			return;
		}
	});
	
}
</script>
<?php 
if ($p_msg_num == 1) {
    $msg = "다른 곳에서 로그인 하여 자동 로그 아웃 처리 됩니다.";
    $UTIL->alertMessage($msg);
}
else if ($p_msg_num == 2) {
    $msg = "로그인 후 이용해 주세요.";
    
    
    $UTIL->alertMessage($msg);
}

?>
</html>