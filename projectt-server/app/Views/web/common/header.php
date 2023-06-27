<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <title>벳츠(BETS)</title>
    <link rel="shortcut icon" href="/assets_w/images/favicon.ico?v=<?php echo date("YmdHis"); ?>">




    <link rel="stylesheet" href="/assets_w/css/animations.css">
    <link rel="stylesheet" href="/assets_w/css/font.css">
    <link rel="stylesheet" href="/assets_w/css/reset.css">

    <link rel="stylesheet" href="/assets_w/css/common.css?v=<?php echo date("YmdHis"); ?>">
    <link rel="stylesheet" href="/assets_w/css/basic.css?v=<?php echo date("YmdHis"); ?>">
    <link rel="stylesheet" href="/assets_w/css/layout.css?v=<?php echo date("YmdHis"); ?>">
    <link rel="stylesheet" href="/assets_w/css/layout_mini.css?v=<?php echo date("YmdHis"); ?>"><!-- 미니게임 -->
    <link rel="stylesheet" href="/assets_w/css/layout_sports.css?v=<?php echo date("YmdHis"); ?>"><!-- 스포츠 -->
    <link rel="stylesheet" href="/assets_w/css/game.css?v=<?php echo date("YmdHis"); ?>"><!-- game -->
    <link rel="stylesheet" href="/assets_w/css/animations.css"><!-- CSS animations1 -->


    <link rel="stylesheet" href="/assets_w/jq/hamburger1/menu.css"><!-- 햄버거1 -->
    <script src="/assets_w/jq/hamburger1/menu.js"></script>

    <link rel="stylesheet" href="/assets_w/css/response.css">


    
    <script src="/assets_w/js/jquery.min.js"></script>
    <script src="/assets_w/js/jquery-ui.js"></script>
    <script src="/assets_w/js/ajax.js?v=<?php echo date("YmdHis"); ?>" defer></script>
    <script src="/assets_w/js/script.js?v=<?php echo date("YmdHis"); ?>" defer></script>

    
    <!-- main_visual 메인 비주얼-->
    <link rel="stylesheet" href="/assets_w/jq/slider/style.css">
    <link rel="stylesheet" href="/assets_w/jq/slider/custom.css">


    <!-- product_rolling1 메인 미니게임 롤링 -->
    <link rel="stylesheet" href="/assets_w/jq/product_rolling1/style.css">
        

    <script src="/assets_w/js/showid.js"></script><!-- sk_기본 -->
    <!-- <script src="http://code.jquery.com/jquery-1.12.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
    <script src="/assets_w/js/sub_tab.js"></script><!-- sub_tab -->
    <script src="/assets_w/js/header.js?v=<?php echo date("YmdHis"); ?>" charset="utf-8"></script><!-- header -->
    <script src="/assets_w/js/common.js?v=<?php echo date("YmdHis"); ?>" charset="utf-8"></script><!-- header -->
    <script src="/assets_w/js/sk_tab.js?v=<?php echo date("YmdHis"); ?>" charset="utf-8"></script><!-- sk_탭 -->
    <script src="/assets_w/js/sk_opacity.js?v=<?php echo date("YmdHis"); ?>" charset="utf-8"></script><!-- sk_메뉴투명도 -->
    <script src="/assets_w/jq/sk_popup/sk_popup.js?v=<?php echo date("YmdHis"); ?>" charset="utf-8"></script><!-- sk_팝업 -->




    <!-- 메인 공지/이벤트/환전 롤링 -->
    <script src="/assets_w/js/sk_table.js?v=<?php echo date("YmdHis"); ?>"></script><!-- sk_테이블 -->

    <!-- main_visual 메인 비주얼-->
    <script src="/assets_w/jq/slider/modernizr.custom.79639.js"></script>

    <!-- /slider-wrapper -->
    <script src="/assets_w/jq/slider/jquery.ba-cond.min.js"></script>
    <script src="/assets_w/jq/slider/jquery.slitslider.js"></script>

    <!-- product_rolling1 메인 미니게임 롤링 -->
    <script src="/assets_w/jq/product_rolling1/owl.carousel.js"></script>

    <!-- 내정보 드롭다운 -->
    <link rel="stylesheet" href="/assets_w/jq/dropdown1/jquery-accordion-menu.css">
    <script src="/assets_w/jq/dropdown1/jquery-accordion-menu.js"></script>
    <script src="/assets_w/js/html2canvas.js"></script><!-- util -->
    
    
    <!-- 12/8 라이브스포츠 아코디언 js 수정 -->
    <script src="/assets_w/js/tendina.min.js"></script>
    <script src="/assets_w/js/status_util.js?v=<?php echo date("YmdHis"); ?>"></script>
    
    <script src="/assets_w/js/base_constants.js?v=<?php echo date("YmdHis"); ?>"></script>


    <?php if(config(App::class)->profile == "prd") { ?>
    <script>
        $(function(){
            $(document).on('keydown',function(e){
                if ( e.keyCode == 123 ) {  // F12 Button Protection
                    e.preventDefault();
                    e.returnValue = false;
                }
            });
        
            // 우측 클릭 방지
            document.onmousedown = disableclick;
            status="우측클릭을 허용하지 않습니다.";
        
            function disableclick(event){
                if (event.button==2) {
                    alert(status);
                    return false;
                }
            }
        });
    </script>
    <?php } ?>

    <style>
    .cloader {
        width: 48px;
        height: 48px;
        border: 5px solid #FFF;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: cloaderRotation 1s linear infinite;
    }

    @keyframes cloaderRotation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    } 
    </style>
</head>

<!-- <body id="myAnchor"> -->