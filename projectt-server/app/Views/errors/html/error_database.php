<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>긴급점검</title>

    <style>
        * {margin: 0; padding: 0;}
        body {background-image: url( "/images/img_inspection_bg.jpg" ); background-position:center center; background-size: cover; background-repeat:no-repeat;}

        .system_wrap {
            display:flex;
            height: 100vh;
            justify-content:center;
            text-align:center;
            flex-direction:column;
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        .system_logo {
            display: block;
            width: 200px;
            margin: 60px auto;
        }
        
        .loding_img {
            display: block;
            width: 260px;
            margin: 0 auto;
            padding: 0 0 30px;
        }
        /* 점검내용 */
        .system_title {
            padding-bottom: 10px;
            font-size: 24px;
            color: #fff;
        }
        /* 점검내용 */
        .system_description {
            color:#00a7d6; 
            font-size:16px; 
            font-weight:600; 
            margin:10px 0;
            padding: 20px;
        }
                
        .sub_box {
            width: 580px;
            max-width: 80%;
            margin: 0 auto;
            padding: 20px;
            text-align: left;
            background-color: #00a7d6;
            border-radius: 0px;
        }

        /* 점검시간 */
        .time_box {
            max-width: 560px;
            margin: 0 auto 10px;
            padding: 20px 10px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.9);;
            color:#ffffff;
            border-radius: 0px;
            font-size:1.35rem;
            font-weight:400;  

        }

        .time_txt {
            max-width: 560px;
            text-align: center;
            color:#00c7ff;
            font-size:1rem; 
        }
        
        /* 점검 설명글 */
        .sub_description {
            color:#000; 
            font-size:0.8rem;
            font-weight:600; 
        }

		h1 {
			text-align: center;
			color:#ffffff;
			font-size:2.2rem; 
            font-weight:900; 
			margin: 20px;
		}

    </style>
</head>
<body>
    <div class="system_wrap">
        <div>
            <img src="/images/logo.png" alt="" class="system_logo">
			
			<h1>[보안서버 데이터패치 안내]</h1>
			<div class="sub_box">
                <div class="time_box">
					안녕하세요 Bets 입니다.<br><br>
					패치가 진행되는동안 이용이 불가하오니<br>
                    양해해주시기 바라며,궁금하신 내용은<br> 
                    모바일 고객센터로 문의하여 주시기 바랍니다.<br>
                    <div class="time_txt"><br>★ 점검시간 : 2023/05/29 23:00 ~ 익일 03:00 까지 (약 4시간) ★<br>
						★ 점검내용 : 보안서버패치 , 데이터패치 ★</div>
                </div>
                
                <p class="sub_description">※ 상기 작업시간은 사정에 의해 변경될 수 있습니다. </p>
                <p class="sub_description">※ 현재 텔레그램문의만 가능합니다. @BETSKRCS  ※사칭 아이디 주의바랍니다.</p>
                <p class="sub_description">※ 점검 시간 이후 접속이 안되시면 쿠키 삭제 이후 재접속 부탁드립니다. </p>
            </div>
        </div>
    </div>
</body>
</html>