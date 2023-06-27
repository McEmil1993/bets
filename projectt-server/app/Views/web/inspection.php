<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사이트 점검중</title>

    <style>
        * {margin: 0; padding: 0;}
        body {background-image: url( "/images/img_inspection_bg.jpg" ); background-position:center center; background-size: cover; background-repeat:no-repeat;}

        .system_wrap {
            display:flex;
            height: 100vh;
            justify-content:center;
            text-align:center;
            flex-direction:column;
            background-color: rgba(0, 0, 0, 0.6);
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
            color:#b7b7b7; 
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
            background-color: #009dd9;
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
            font-size:1.8rem; 
            font-weight:900; 
        }
        
        .time_txt {
            max-width: 560px;
            text-align: center;
            color:#959595;
            font-size:0.9rem; 
            font-weight:600;
        }

        
        /* 점검 설명글 */
        .sub_description {
            color:#000; 
            font-size:0.8rem;
            font-weight:600; 
        }
    </style>
</head>
<body>
    <div class="system_wrap">
        <?php
        $start_dt = explode(' ', date("Y-m-d H:i", strtotime($system_mes['start_dt'])));
        $end_dt = explode(' ', date("Y-m-d H:i", strtotime($system_mes['end_dt'])));
        ?>
        <div>
            <img src="/images/logo.png" alt="" class="system_logo">

            <h1 class="system_title"><?=$system_mes['title']?></h1>
            <div class="system_description"><?=$system_mes['contents']?></div>
            <div class="sub_box">
                <div class="time_box"><?='[점검시간]<br> '.$start_dt[0].' '.$start_dt[1].'~'.$end_dt[1];?>
                     <div class="time_txt"><br>이용에 불편을 드려 죄송합니다.<br>최대한 빠른 시간 내에 복구될 예정입니다.</div>
                </div>

                <p class="sub_description">※ 상기 작업시간은 사정에 의해 변경될 수 있습니다. </p>
                <p class="sub_description">※ 현재 텔레그램문의만 가능합니다. @BETSKRCS </p>  
                <p class="sub_description">※ 점검 시간 이후 접속이 안되시면 쿠키 삭제 이후 재접속 부탁드립니다. </p>
            </div>
        </div>
    </div>
</body>
</html>