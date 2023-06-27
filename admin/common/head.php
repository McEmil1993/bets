<head>
	<title><?=TITLE?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="title" content="BETGO">
	<meta name="author" content="BETGO">
	<meta name="keywords" content="BETGO">
	<meta name="description" content="BETGO">

	<!--  BASE CSS  -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="<?= _STATIC_COMMON_PATH ?>/css/material-icons.min.css" rel="stylesheet">
	<link href="<?= _STATIC_COMMON_PATH ?>/css/animate.min.css" rel="stylesheet" />
	<link href="<?= _STATIC_COMMON_PATH ?>/css/elements.min.css" rel="stylesheet" />
	<link href="<?= _STATIC_COMMON_PATH ?>/css/default.css" rel="stylesheet">
    <link href="<?= _STATIC_COMMON_PATH ?>/css/bootstrap.modal.css" rel="stylesheet">
	<!--  END BASE CSS  -->

	<!--  BASE JS  -->
	<script src="<?= _STATIC_COMMON_PATH ?>/js/jquery-1.9.1.min.js"></script>
	<script src="<?= _STATIC_COMMON_PATH ?>/js/bootstrap.min.js"></script>
	<script src="<?= _STATIC_COMMON_PATH ?>/js/jquery-ui.min.js"></script>
	<script src="<?= _STATIC_COMMON_PATH ?>/js/apps.js"></script>
	<script src="<?= _STATIC_COMMON_PATH ?>/js/form-plugins.demo.min.js"></script>
	<script src="<?= _STATIC_COMMON_PATH ?>/js/app.default.js"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/util.js?v=<?php echo date("YmdHis"); ?>"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/constants.js?v=<?php echo date("YmdHis"); ?>"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/api-gateway.js"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/betting-history.js"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/password-auth.js?v=<?php echo date("YmdHis"); ?>"></script>
        
        
        <!-- favicon -->  
        <link rel="shortcut icon" type="image/x-icon" href="<?= FAVICON_IMG ?>">
      

	
	<?php
	$request_uri = $_SERVER['REQUEST_URI'];
	$request_uri = !empty($request_uri) ? $request_uri : '';
	header(`Location : `.$request_uri);
	?>

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

    <style>
    /* Circle Loader */
    .cloader {
        width: 48px;
        height: 48px;
        border: 5px solid #dfdfdf;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: loaderAnimation 1s linear infinite;
    }
    .cloader.cloader--xs {
        width: 15px;
        height: 15px;
        border-width: 2px;
    }

    @keyframes loaderAnimation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>
<!-- Password Authentication Dialog Box -->
<div class="modal fade" id="passwordAuthModal" role="dialog">
    <div class="modal-dialog"> 
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Second Password Authentication</h4>
            </div>
            <form method="post" id="passwordAuthForm">
            <div class="modal-body">
                <p class="label">Kindly please enter your second password or you'll be automatically logged out.</p>
                <div class="form-group">
                    <input name="password-auth" required type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                </div>       
            </div>

            <div class="modal-footer">
                <button type="submit" class="submit-second-password-button btn btn-primary">Submit Password</button>
            </div>
            </form>
        </div>
    </div>
</div>