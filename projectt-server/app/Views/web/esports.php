<?php if($mobileType == 'web'){ ?>
    <?= view('/web/common/header') ?>
<?php } ?>
<?php if($mobileType == 'web'){ ?>
    <?= view('/mobile/common/header') ?>
<?php } ?>
<?php
use App\Util\DateTimeUtil;
use App\Util\StatusUtil;
//echo 'segment : '.$segment;
?>
<div id="wrap">

    <?php if($mobileType == 'web'){ ?>
            <?= view('/web/common/header_wrap') ?>
            <div class="title">E-스포츠</div>
            <div id="es_act_con" class="es_act" style="display:none">
                <a href="#" class="btn__modal-show1"><img src="/assets/images/es_btn1.png"></a>
                <a href="#" class="btn__modal-show2"><img src="/assets/images/es_btn2.png"></a>
                <a href="#" class="btn__modal-show3"><img src="/assets/images/es_btn3.png"></a>
            </div>
            <!-- act popup -->
            <div class="modal__popup popup_1">
                <div class="popup__container">
                    <div class="popup_content_es">
                        <img src="/assets/images/es_help_1.jpg" alt="">
                    </div>
                </div>
            </div>
            <div class="modal__popup popup_2">
                <div class="popup__container">
                    <div class="popup_content_es">
                        <img src="/assets/images/es_help_2.jpg" alt="">
                    </div>
                </div>
            </div>
            <div class="modal__popup popup_3">
                <div class="popup__container">
                    <div class="popup_content_es">
                        <img src="/assets/images/es_help_3.jpg" alt="">
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php if($mobileType == 'web'){ ?>
        <?= view('/mobile/common/header_wrap') ?>
    <?php } ?>

    <div id="contents_wrap" style="width:100%; height:100%;">
        <?php if($mobileType == 'web'){ ?>
        <div class="es_sub_title">
            <h2>E-스포츠</h2>
            <div class="es_act_m">
                <a href="#" class="btn__modal-show1"><img src="/assets/images/es_btn1.png"></a>
                <a href="#" class="btn__modal-show2"><img src="/assets/images/es_btn2.png"></a>
                <a href="#" class="btn__modal-show3"><img src="/assets/images/es_btn3.png"></a>
            </div>
        </div>
        <?php } ?>

            <!-- act popup -->
            <div class="modal__popup popup_1">
            <div class="popup__container">
                <div class="popup_content_es">
                    <img src="/assets/images/es_help_1.jpg" alt="">
                </div>
            </div>
        </div>
        <div class="modal__popup popup_2">
            <div class="popup__container">
                <div class="popup_content_es">
                    <img src="/assets/images/es_help_2.jpg" alt="">
                </div>
            </div>
        </div>
        <div class="modal__popup popup_3">
            <div class="popup__container">
                <div class="popup_content_es">
                    <img src="/assets/images/es_help_3.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
    <?= view('/web/common/footer_wrap') ?>
</div>
<!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
</body>


<style>

.system_logo {
            display: block;
            width: 180px;
            margin: 60px auto;
        }
        .system_loding {
            padding: 60px 0 40px;
            background-color: #343434;
            text-align: center;
        }
        .loding_img {
            display: block;
            width: 153px;
            margin: 0 auto;
            padding: 0 0 60px;
        }
        .system_title {
            padding-bottom: 20px;
            font-size: 36px;
            color: #fff;
        }
        .system_description {
            padding-bottom: 20px;
            font-size: 18px;
            color: #a9a9a9;
            letter-spacing: -1px;
        }
        .system_description_2 {
            padding-bottom: 20px;
            font-size: 18px;
            color: #FFFFFF;
            letter-spacing: -1px;
        }
        .system_time {
            max-width: 580px;
            margin: 0 auto 20px;
            padding: 20px 0;
            text-align: center;
            font-size: 24px;
            background-color: #000;
            color: #fff;
            border-radius: 10px;
        }
</style>

<script>
    $.ajax({
        url: '/web/playCasino',
        type: 'post',
        async: false,
        data: {
            'prd_id': '101',
            'game_id' : '0',
            'prd_type' : 'E'
        },
    }).done(function (response) {
        console.log(response);
        if(response['result_code'] == 1){
            const iframe = $('<iframe class="myFrame" style="width:100%;height:72vh;"></iframe>');
            iframe.attr('allowfullscreen', true);
            iframe.attr('src', response['data']['launch_url']);
            $('#contents_wrap').append(iframe);
        }else if(response['result_code'] == -1){
            //let mes = "<img src='/images/logo.png' alt='logo' class='system_logo'>";
            let mes = "<div class='system_loding'>";
                mes += "<img src='/images/img_loding.png' alt='' class='loding_img'>";
                mes += "<h1 class='system_title'>시스템 점검 중 입니다.</h1>";
                mes += "<p class='system_description'>";
                mes += "보다 안정적인 서비스 이용을 위해 서버 점검 작업을 진행 중 입니다. <br>";
                mes += "서비스 이용에 불편을 드려 죄송합니다.";
                mes += "</p>";
                mes += "</div>";
            $('#contents_wrap').append(mes);
            //alert(response['messages']);
        }else{
            alert(response['messages']);
        }
    }).fail(function (error) {
        alert(error.responseJSON['messages']['messages']);
    }).always(function (response) {
        console.log('test');
        $('#es_act_con').attr('style', 'display: block;');
    });

    $('.btn__modal-show1').on('click', function(){
        $('.popup_1').show();
        $('#myAnchor').css('overflow', 'hidden');
    });
    $('.btn__modal-show2').on('click', function(){
        $('.popup_2').show();
        $('#myAnchor').css('overflow', 'hidden');
    });
    $('.btn__modal-show3').on('click', function(){
        $('.popup_3').show();
        $('#myAnchor').css('overflow', 'hidden');
    });
    $('.modal__popup').on('click', function(){
        $('.modal__popup').hide();
        $('#myAnchor').css('overflow', 'auto');
    });
</script>

<script>
    $(document).ready(function(){
        const path = window.location.pathname;

		const pathArr = path.split("/");

        let prd_id;
        let game_id;
		if(pathArr[1] == "slots") {
			prd_id = pathArr[2];
			game_id = pathArr[3];
		}else {
			prd_id = 0;
			game_id = 0;
		}

        $.ajax({
            url: '/web/playCasino',
            type: 'post',
            async: false,
            data: {
                'prd_id': prd_id,
                'game_id' : game_id,
                'prd_type' : 'S'
            },
        }).done(function (response) {
            console.log(response);
            if(response['result_code'] == 1){
                const iframe = $('<iframe class="myFrame" style="width:100%;height:100vh;"></iframe>');
                iframe.attr('allowfullscreen', true);
                iframe.attr('src', response['data']['launch_url']);
                $('.casino__board-list').append(iframe);
            }else{
                alert(response['messages']);
            }
        }).fail(function (error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function (response) {
    	});
    });
                                                
    const playCasino = function (prd_id, game_id, prd_type){
        $.ajax({
            url: '/web/playCasino',
            type: 'post',
            async: false,
            data: {
                'prd_id': prd_id,
                'game_id' : game_id,
                'prd_type' : prd_type
            },
        }).done(function (response) {
            //console.log('betPrice : '+betPrice+' betPrice : '+server_betPrice);

            if(response['result_code'] == 1){
                // window.location.href = response['data']['launch_url'];

                window.open(response['data']['launch_url']);

                // const iframe = $('<iframe class="myFrame"></iframe>');
                // iframe.attr('allowfullscreen', true);
                // iframe.attr('src', response['data']['launch_url']);
                // $('.game__iframe-wrap').append(iframe);
                // $('.game__iframe-popup').show();
            }else{
                alert(response['messages']);
            }
        }).fail(function (error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function (response) {
        });
    }

    const offCasinoGame = function() {
        $('.game__iframe-popup').hide().find('iframe').prop('src', '');
    }

    const fnGetParam = function(sname) {
        var params = location.search.substr(location.search.indexOf("?") + 1);
        var sval = "";
        params = params.split("&");
        for (var i = 0; i < params.length; i++) {
            temp = params[i].split("=");
            if ([temp[0]] == sname) { sval = temp[1]; }

        }
        return sval;
    };
</script>
</html>