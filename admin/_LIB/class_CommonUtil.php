<?php

class CommonUtil {

    private $id_pattern;
    private $pass_pattern;
    private $nick_pattern;
    private $hpChk;
    private $hpChk1;
    private $hpChk2;
    private $common_filter;
    private $id_filter;
    private $nick_filter;

    public function __construct() {
        $this->common_filter = array('&', ';', '&#', '--', '/*', '*/', 'iframe', 'script', 'embed', 'cookie', 'fopen', 'fsockopen', 'file_get_contents', 'readfile', 'unlink', 'object', 'phpinfo'
            , '1=1', 'drop', 'truncate', 'select', 'insert', 'update', 'delete', 'union');
        $this->id_filter = array('webadmin', 'admin', 'master', 'webmaster', 'mysql', 'oracle');
        $this->nick_filter = array('관리자', '웹관리자', '운영자', '마스터', '웹마스터', 'admin', 'master', 'webmaster');

        $this->id_pattern = "/^[a-zA-Z0-9]{4,12}$/";
        $this->eng_pattern = "/[a-zA-Z]/";
        //$this->nick_pattern  = "/^[0-9a-zA-Z가-힣]{4,20}$/";
        //$this->nick_pattern  = "/^[\wㄱ-ㅎㅏ-ㅣ가-힣0-9]{2,20}$/";
        $this->nick_pattern = "/^[가-힣0-9a-zA-Z]{2,30}$/"; //한글문제로 1글자 -> 3바이트
        $this->pass_pattern = "/^[a-zA-Z0-9!@#$%^&*()]{6,20}$/";
        $this->hpChk = "/^[0-9]{3,3}$/";
        $this->hpChk1 = "/^[0-9]{3,4}$/";
        $this->hpChk2 = "/^[0-9]{4,4}$/";
    }

    public static function logWrite($log_data, $log_name = null) {
        if (!empty($log_name)) {
            $f_name = $log_name . "_" . date("Ymd") . ".log";
            $f_path = $_SERVER["DOCUMENT_ROOT"] . "/log/" . $log_name . "/";
        } else {
            $f_name = date("Ymd") . ".log";
            $f_path = $_SERVER["DOCUMENT_ROOT"] . "/log/";
        }

        if (!is_dir($f_path)) {
            @mkdir($f_path, 0777);
            @chmod($f_path, 0777);
        }

        $f_log_path = $f_path . "/" . $f_name;

        $fp = fopen($f_log_path, 'a+');

        if ($fp) {
            $log = date("[Y:m:d H:i:s] ") . trim($log_data);
            fwrite($fp, $log . "\n");
            fclose($fp);
        }

        return 0;
    }

    public static function getReffer() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $reffer = $_SERVER['HTTP_REFERER'];
        } else {
            $reffer = NULL;
        }


        return $reffer;
    }

    public static function alertMessage($msg, $charset = "utf-8") {
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . $charset . "\" /><script language=\"javascript\">";
        if ($msg != "") {
            echo "alert(\"" . $msg . "\");";
        }
        echo "</script>";

        return;
    }

    //alert_back 함수
    public static function alertBack($msg, $charset = "utf-8") {
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . $charset . "\" /><script language=\"javascript\">";
        if ($msg != "") {
            echo "alert(\"" . $msg . "\");";
        }
        echo "history.back(-1);";
        echo "</script>";

        exit;
    }

    //alert_location 함수
    public static function alertLocation($msg, $url, $charset = "utf-8") {
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . $charset . "\" /><script language=\"javascript\">";
        if ($msg != "") {
            echo "alert(\"" . $msg . "\");";
        }

        if ($url != "") {
            echo "parent.location.href=\"" . $url . "\";";
        }

        echo "</script>";

        exit;
    }

    public static function alertClose($msg, $charset = "utf-8") {
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . $charset . "\" /><script language=\"javascript\">";
        if ($msg != "") {
            echo "alert(\"" . $msg . "\");";
        }
        echo "self.close();";
        echo "</script>";

        return;
    }

    function numToWon($val) {

        $vr = null;

        if (!is_numeric($val)) {
            $vr = "오류";
            return $vr;
        }

        $price = $val;
        $trans_kor = array("", "일", "이", "삼", "사", "오", "육", "칠", "팔", "구");
        $price_unit = array("", "십", "백", "천", "만", "십", "백", "천", "억", "십", "백", "천", "조", "십", "백", "천");
        $valuecode = array("", "만", "억", "조");
        $value = strlen($price);
        $k = 0;
        for ($i = $value; $i > 0; $i--) {
            $vv = "";
            $vc = substr($price, $k, 1);
            $vt = $trans_kor[$vc];
            $k++;

            if ($i % 5 == 0) {
                $vv = $valuecode[$i / 5];
            } else {
                if ($vc) {
                    $vv = $price_unit[$i - 1];
                }
            }
            $vr = $vr . $vt . $vv;
        }

        return $vr . " 원";
    }

    function strIDCheck($val) {
        if (!preg_match($this->id_pattern, $val)) {
            return false;
        }

        if (!preg_match($this->eng_pattern, $val)) {
            return false;
        }

        return true;
    }

    function strNICKCheck($val) {
        if (!preg_match($this->nick_pattern, $val)) {
            return false;
        }

        return true;
    }

    function strPassCheck($val) {
        if (!preg_match($this->pass_pattern, $val)) {
            return false;
        }

        return true;
    }

    function strHPCheck($val) {

        $firstHp = array("010", "011", "016", "017", "018", "019");
        if (!in_array($val[0], $firstHp)) {
            return false;
        }

        if (!preg_match($this->hpChk1, $val[1])) {
            return false;
        }

        if (!preg_match($this->hpChk2, $val[2])) {
            return false;
        }

        return true;
    }

    function strEmailCheck($val) {
        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    function getRandom() {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        //$random_val = $micro.date("YmdHis");
        $random_val = $micro . date("sHis") * 3;
        $random_ret = null;
        $random_ret[0] = $micro;
        $random_ret[1] = $random_val;

        return $random_ret;
    }

    function strFilter($type = null, $str) {
        $temp = str_replace(' ', '', $str);

        foreach ($this->common_filter as $value) {
            if (strpos($temp, $value) !== false) {
                return false;
            }
        }

        if ($type == 'id') {
            foreach ($this->id_filter as $value) {
                if (stripos($temp, $value) !== false) {
                    return false;
                }
            }
        } else if ($type == 'nick') {
            foreach ($this->nick_filter as $value) {
                if (stripos($temp, $value) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    //만나이 계산
    function getAge($birthday) {
        $now_yy = date("Y");
        $now_mm = date("m");
        $now_dd = date("d");

        $u_yy = substr($birthday, 0, 4);
        $u_mm = substr($birthday, 4, 2);
        $u_dd = substr($birthday, 6, 2);

        if ($now_mm > $u_mm) {
            $age = $now_yy - $u_yy;
        } else if ($now_mm == $u_mm) {
            if ($now_dd >= $u_dd) {
                $age = $now_yy - $u_yy;
            } else {
                $age = $now_yy - $u_yy - 1;
            }
        } else {
            $age = $now_yy - $u_yy - 1;
        }


        return $age;
    }

    //파일 유무
    function getFileExist($filepath, $bremote = false) {

        if ($bremote) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $filepath);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if (curl_exec($ch) !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            if (file_exists($filepath)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // 이미지 mine type
    function boolMineTypeImage($minetype) {
        $tmp = explode('/', $minetype);

        if (trim($tmp[0]) == 'image') {
            return true;
        } else {
            return false;
        }
    }

    // 첨부파일 mine type : image, txt
    function boolMineTypeAttach($minetype) {
        $tmp = explode('/', $minetype);

        if (trim($tmp[0]) == 'image') {
            return true;
        } else if (trim($tmp[0]) == 'text') {
            if (trim($tmp[1]) == 'plain') {
                return true;
            }

            return false;
        } else {
            return false;
        }
    }

    // 이미지 확장자 
    function boolExtImage($ext) {
        $AttachFileExt = array('jpg', 'gif', 'png', 'JPG', 'GIF', 'PNG');
        if (!in_array($ext, $AttachFileExt)) {
            return false;
        }

        return true;
    }

    // 첨부파일 허용 확장자
    function boolExtAttach($ext) {
        $AttachFileExt = array('jpg', 'gif', 'png', 'JPG', 'GIF', 'PNG', 'txt', 'TXT');
        if (!in_array($ext, $AttachFileExt)) {
            return false;
        }

        return true;
    }

    // 게시판 문자 바꾸기
    function replace_bracket($var) {
        $search = array(
            'nbsp',
            'lt',
            'gt',
            'amp',
        );

        $replace = array(
            '&nbsp;',
            '&lt;',
            '&gt;',
            '&amp;',
        );

        $var = str_replace($search, $replace, $var);

        return $var;
    }

    function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && getenv('HTTP_CLIENT_IP')) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && getenv('HTTP_X_FORWARDED_FOR')) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_HOST']) && getenv('REMOTE_HOST')) {
            return $_SERVER['REMOTE_HOST'];
        } elseif (!empty($_SERVER['REMOTE_ADDR']) && getenv('REMOTE_ADDR')) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return false;
    }

    function certFailHeader($return_path = null) {
        if ($return_path) {
            $loc_url = $return_path;
        } else {
            $loc_url = "index";
        }

        //test 용.
        $msg = "경로가 잘못되었습니다.( Test 입니다. )" . $loc_url;
        echo "$msg <br>";
        $this::alertMessage($msg);

        //exit(header("Location: $loc_url"));

        return true;
    }

    function errorMsg($errorCode) {

        switch ($errorCode) {
            case '2101': $errormsg = '입력값 오류입니다. 아이디를 정확히 입력하세요.';
                break;
            case '2102': $errormsg = '입력값 오류입니다. 매장 아이디를 정확히 입력하세요.';
                break;
            case '2103': $errormsg = '입력값 오류입니다. 회원 상태정보를 정확히 입력하세요.';
                break;
            case '2130': $errormsg = '입력값 오류입니다.';
                break;
            case '2199': $errormsg = 'DB 처리에 실패 하였습니다.';
                break;
            case '2200': $errormsg = 'DB 연결에 실패 하였습니다.';
                break;
            default:
                if (empty($errormsg)) {
                    $errormsg = '시스템 오류 입니다. 잠시 후 이용해 주세요.';
                    break;
                }
        }

        return $errormsg;
    }

    function checkFailType($errorCode, $loc_url = null, $errMsg = null, $errType = null) { //error 처리 합친거
        if ($errMsg == '') {
            $errormsg = $this::errorMsg($errorCode);
        } else {
            $errormsg = $errMsg;
        }

        if ($errType == 'json') {
            $result['retCode'] = $errorCode;
            $result['retMsg'] = $errormsg;
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            //exit;
        } else {
            $this::alertMessage($errormsg);
        }
    }

    function checkFail($errorCode, $errMsg = null, $errType = null) {
        $errormsg = $errMsg;

        switch ($errorCode) {
            //회원가입
            case '2001': $errormsg = '사용 불가능한 아이디입니다.다른 아이디를 사용하세요(Server Error)';
                break;
            case '2002': $errormsg = '자동입력방지 체크를 해주세요.';
                break;
            case '2003': $errormsg = '자동입력방지 체크를 해주세요.(robot)';
                break;
            case '2004': $errormsg = '사용 불가능한 닉네임입니다.다른 닉네임을 사용하세요(Server Error)';
                break;
            case '2013': $errormsg = '입력값 오류입니다(00). 회원정보를 정확히 입력하세요.';
                break;
            case '2014': $errormsg = '입력값 오류입니다(06). 회원정보를 정확히 입력하세요.';
                break;
            case '2015': $errormsg = '입력값 오류입니다(01). 회원정보를 정확히 입력하세요.';
                break;
            case '2016': $errormsg = '입력값 오류입니다(02). 회원정보를 정확히 입력하세요.';
                break;
            case '2017': $errormsg = '입력값 오류입니다(03). 회원정보를 정확히 입력하세요.';
                break;
            case '2018': $errormsg = '입력값 오류입니다(04). 회원정보를 정확히 입력하세요.';
                break;
            case '2019': $errormsg = '입력값 오류입니다(05). 회원정보를 정확히 입력하세요.';
                break;
            case '2101': $errormsg = '사용 불가능한 아이디입니다.<br>다른 아이디를 사용하세요';
                break;
            //로그인
            case '2201': $errormsg = '로그인에 실패하였습니다.(01)';
                break;

            default:
                if (!$errormsg) {
                    $errormsg = '시스템 오류 입니다. 잠시 후 이용해 주세요.';
                    break;
                }
        }

        if ($errType == 'login' || $errType == 'myinfo') {
            $this::alertBack($errormsg);
        } else {
            $this::alertMessage($errormsg);
        }
    }

    function checkFailLocation($errorCode, $loc_url, $errMsg = null, $errType = null) { //나중에 위에 거랑 합쳐서 처리
        if ($loc_url == '') {
            $loc_url = "/index";
        }

        $errormsg = $errMsg;

        switch ($errorCode) {
            case '2001': $errormsg = '시스템 오류 입니다. 잠시 후 이용해 주세요.(01)';
                break;
            case '2002': $errormsg = '자동입력방지 체크를 해주세요.';
                break;
            case '2003': $errormsg = '자동입력방지 체크를 해주세요.(robot)';
                break;
            case '2004': $errormsg = '사용 불가능한 닉네임입니다.다른 닉네임을 사용하세요(Server Error)';
                break;
            case '2013': $errormsg = '입력값 오류입니다(00). 회원정보를 정확히 입력하세요.';
                break;
            case '2014': $errormsg = '입력값 오류입니다(06). 회원정보를 정확히 입력하세요.';
                break;
            case '2015': $errormsg = '입력값 오류입니다(01). 회원정보를 정확히 입력하세요.';
                break;
            case '2016': $errormsg = '입력값 오류입니다(02). 회원정보를 정확히 입력하세요.';
                break;
            case '2017': $errormsg = '입력값 오류입니다(03). 회원정보를 정확히 입력하세요.';
                break;
            case '2018': $errormsg = '입력값 오류입니다(04). 회원정보를 정확히 입력하세요.';
                break;
            case '2019': $errormsg = '입력값 오류입니다(05). 회원정보를 정확히 입력하세요.';
                break;
            case '2023': $errormsg = '로그인 후 이용해 주세요.(01)';
                break;
            case '2024': $errormsg = '로그인 후 이용해 주세요.(02)';
                break;
            case '2025': $errormsg = '로그인 후 이용해 주세요.(03)';
                break;

            case '2026': $errormsg = '로그인 후 이용해 주세요.';

                break;
            case '2031': $errormsg = '회원 정보 등록에 실패 하였습니다.(01)';
                break;
            case '3001': $errormsg = '경로를 찾을수 없습니다. 잘못된 접근 방식 입니다.(01)';
                break;

            default:
                if (!$errormsg) {
                    $errormsg = '시스템 오류 입니다. 잠시 후 이용해 주세요.';
                    break;
                }
        }

        $this::alertLocation($errormsg, $loc_url);
        exit;
    }

    function checkFailJson($errorCode, $errMsg = null) {
        $errormsg = $errMsg;

        switch ($errorCode) {
            case '2020':
                $errormsg = '입력값 오류입니다(00). 비밀번호를 정확히 입력하세요.';
                $result['retMsg'] = $errormsg;
                break;
            case '2021':
                $errormsg = '로그인 후 이용해 주세요.';
                $result['retMsg'] = $errormsg;

                CommonUtil::logWrite('여기다','info');

                break;
            case '2022':
                $errormsg = '입력값 오류입니다(01). 회원정보를 확인해 주세요.';
                $result['retMsg'] = $errormsg;
                break;
            default:
                $errorCode = 2029;
                $errormsg = '시스템 오류 입니다. 잠시 후 이용해 주세요.';
                break;
        }

        $result['retCode'] = $errorCode;

        echo json_encode($result);
        exit;
    }

    //날짜 처리 함수 : 10일 단위 구간. 현재일 기준으로
    //현재일, 이번달 마지막 일, 이전달 마지막 일
    function getMonthLastDay() {

        $now_day = date("d");
        $now_y = date('Y');
        $now_m = date('m');
        $now_end = date('t', mktime(0, 0, 0, $now_m, 1, $now_y));
        $buff_date = date('Y-m', strtotime('-1 month'));
        $buff_arr = explode('-', $buff_date);
        $be_end = date('t', mktime(0, 0, 0, $buff_arr[1], 1, $buff_arr[0]));

        if ($now_day <= 10) {
            $day_arr[0]['s'] = date("Y-m") . "-01";
            $day_arr[0]['e'] = date("Y-m") . "-10";
            $day_arr[1]['s'] = $buff_arr[0] . "-" . $buff_arr[1] . "-21";
            $day_arr[1]['e'] = $buff_arr[0] . "-" . $buff_arr[1] . "-" . $be_end;
            $day_arr[2]['s'] = $buff_arr[0] . "-" . $buff_arr[1] . "-11";
            $day_arr[2]['e'] = $buff_arr[0] . "-" . $buff_arr[1] . "-20";
        } else if (($now_day > 10) && ($now_day <= 20)) {
            $day_arr[0]['s'] = date("Y-m") . "-11";
            $day_arr[0]['e'] = date("Y-m") . "-20";
            $day_arr[1]['s'] = date("Y-m") . "-01";
            $day_arr[1]['e'] = date("Y-m") . "-10";
            $day_arr[2]['s'] = $buff_arr[0] . "-" . $buff_arr[1] . "-21";
            $day_arr[2]['e'] = $buff_arr[0] . "-" . $buff_arr[1] . "-" . $be_end;
        } else {
            $day_arr[0]['s'] = date("Y-m") . "-21";
            $day_arr[0]['e'] = date("Y-m") . "-" . $now_end;
            $day_arr[1]['s'] = date("Y-m") . "-11";
            $day_arr[1]['e'] = date("Y-m") . "-20";
            $day_arr[2]['s'] = date("Y-m") . "-01";
            $day_arr[2]['e'] = date("Y-m") . "-10";
        }

        return $day_arr;
    }

    //해당 월의 날자 요일 정보
    function getMonthInfo($year, $month, $kind) {

        if ($kind == 'prev') {
            $time = mktime(0, 0, 0, $month, 1, $year);

            $buff_time = strtotime('-1 month', $time);
            $buff_date = date('Y-m', $buff_time);
            $buff_arr = explode('-', $buff_date);
            $year = $buff_arr[0];
            $month = $buff_arr[1];
        } else if ($kind == 'next') {
            $time = mktime(0, 0, 0, $month, 1, $year);
            $buff_time = strtotime('+1 month', $time);
            $buff_date = date('Y-m', $buff_time);
            $buff_arr = explode('-', $buff_date);
            $year = $buff_arr[0];
            $month = $buff_arr[1];
        }

        $week_w = array('일', '월', '화', '수', '목', '금', '토');
        //$wnum = date('w', $year."-".$month."-01");
        $wnum = date('w', strtotime($year . "-" . $month . "-01"));

        $day_arr['year'] = $year;
        $day_arr['month'] = $month;
        $day_arr['day_end'] = date('t', mktime(0, 0, 0, $month, 1, $year));
        $day_arr['first_week'] = $week_w[$wnum];

        return $day_arr;
    }

    // 사용자 agent 정보
    function getBrowser($p_agent = null) {
        if ($p_agent == null) {
            $u_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $u_agent = $p_agent;
        }

        $ub = 'Unknown';
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        // mobile platform iPhone, iPod, BlackBerry, Android, Windows CE, LG, MOT, SAMSUNG, SonyEricsson

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh/i', $u_agent)) {
            $platform = 'macintosh';
        } elseif (preg_match('/mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/iPhone/i', $u_agent)) {
            $platform = 'iphone';
        } elseif (preg_match('/iPod/i', $u_agent)) {
            $platform = 'ipod';
        } elseif (preg_match('/iPad/i', $u_agent)) {
            $platform = 'ipad';
        } elseif (preg_match('/Android/i', $u_agent)) {
            $platform = 'android';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'ubname' => $ub,
            'bname' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    function getAccountNumberColor($pVal = null) {

        $strbuff = str_replace('-', '', $pVal);

        // 계좌번호가 아닌 문자열을 입력할수도 있다.(한글)
        $strarr = str_split($strbuff, 4);

        $retVal = "";
        for ($i = 0; $i < sizeof($strarr); $i++) {
            if (($i % 2) == 0) {
                $retVal .= "<font color='red'>" . $strarr[$i] . "</font>";
            } else {
                $retVal .= "<font color='blue'>" . $strarr[$i] . "</font>";
            }
        }

        return $retVal;
    }

    function getAccountNumberColor_renew($pVal = null) {

        $strbuff = str_replace('-', '', $pVal);

        // 계좌번호가 아닌 문자열을 입력할수도 있다.(한글)
        if(!preg_match("/[xA1-xFE][xA1-xFE]/", $pVal)){
            return $pVal;
        }

        $strarr = str_split($strbuff, 4);

        $retVal = "";
        for ($i = 0; $i < sizeof($strarr); $i++) {
            if (($i % 2) == 0) {
                $retVal .= "<font color='red'>" . $strarr[$i] . "</font>";
            } else {
                $retVal .= "<font color='blue'>" . $strarr[$i] . "</font>";
            }
        }

        return $retVal;
    }
    
    // 계좌명의및 번호 변경시 24시간 동안 들어오는 환전은 환전관리 페이지에서 아이디 닉네임 예금주 글씨 굵게 빨간색
    function getMemberInfoColor($pVal = null, $date_time, $is_monitor_charge) {
        if(!isset($date_time)){
            return $pVal;
        }
        
        $checkTime = date("Y-m-d H:i:s", strtotime($date_time . "+" . 24 . " hour"));
        
        $retVal = "";
        if($checkTime > date("Y-m-d H:i:s") || 'Y' == $is_monitor_charge) {
            $retVal = "<font color='red'>" . $pVal . "</font>";
        } else {
            $retVal = $pVal;
        }

        return $retVal;
    }

    public function log_cash($model_dao, $ukey, $member_idx, $ac_code, $ac_idx, $set_money, $now_money, $admin_id = null, $a_comment, $kind) {

        $p_data['sql'] = "insert into  t_log_cash ";
        $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id,u_key) ";
        $p_data['sql'] .= " values($member_idx, $ac_code, $ac_idx,$set_money, $now_money, $set_money + $now_money, '$kind','$a_comment','$admin_id','$ukey')";
       
        if(FAIL_DB_SQL_EXCEPTION === $model_dao->setQueryData($p_data)){
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        CommonUtil::logWrite("log_cash sql : " . $p_data['sql'], "info");
    }

    public function log_point($model_dao, $ukey, $member_idx, $ac_code, $ac_idx, $set_money, $now_money, $admin_id = null, $a_comment, $kind) {

        $p_data['sql'] = "insert into  t_log_cash ";
        $p_data['sql'] .= " (member_idx, ac_code, ac_idx, point, be_point, af_point, m_kind, coment, a_id,u_key) ";
        $p_data['sql'] .= " values($member_idx, $ac_code, $ac_idx,$set_money, $now_money, $set_money + $now_money, '$kind','$a_comment','$admin_id','$ukey')";
      
        if(FAIL_DB_SQL_EXCEPTION === $model_dao->setQueryData($p_data)){
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        
        CommonUtil::logWrite("log_cash sql : " . $p_data['sql'], "info");
    }

    public function log_lose_bet_bonus_point($model_dao, $member_idx, $bet_idx, $set_money) {// 낙첨시 주는 포인트 lose_self_per,lose_recomm_per
        $get_config_str = "'lose_self_per'";
        $p_data['sql'] = "select u_level, set_type, set_type_val from t_game_config ";
        $p_data['sql'] .= " where set_type in ($get_config_str) ";
        $retData = $model_dao->getQueryData($p_data);

        $str_set_type = '';
        $ch_point_lose_self_per = 0;
        $ch_point_lose_recomm_per = 0;
        $a_comment_lose_self_per = '';
        $a_comment_lose_recomm_per = '';
        foreach ($retData as $row) {
            $db_level = $row['u_level'];
            $str_set_type = $row['set_type'];
            switch ($row['set_type']) {
                case 'lose_self_per':
                    $db_config[$db_level]['lose_self_per'] = $row['set_type_val'];
                    break;
            }
        }

        $p_data['sql'] = "select * from member where idx = $member_idx";
        $result_member_data = $model_dao->getQueryData($p_data);

        $cash_use_kind = strtoupper('P');
        $ac_code = 11; // 낙첨 포인트 지급 
        $u_level = $result_member_data[0]['level'];
        $now_point = true === isset($result_member_data[0]['point']) ? $result_member_data[0]['point'] : 0;

        $ch_point_lose_self_per = ($set_money) * $db_config[$u_level]['lose_self_per'] / 100;
        $a_comment_lose_self_per = '낙첨 롤링 포인트 수동 지급 ';
        $af_point = $now_point + $ch_point_lose_self_per;

        $p_data['sql'] = "update member set point = point + $ch_point_lose_self_per where idx= $member_idx ";
        $model_dao->setQueryData($p_data);

        $p_data['sql'] = "insert into  t_log_cash ";
        $p_data['sql'] .= " (member_idx, ac_code, ac_idx, point, be_point, af_point, m_kind, coment, a_id) ";
        $p_data['sql'] .= " values($member_idx, $ac_code, $bet_idx, " . $ch_point_lose_self_per;
        $p_data['sql'] .= ",$now_point, $af_point, '$cash_use_kind','$a_comment_lose_self_per','SYSTEM')";
        $model_dao->setQueryData($p_data);

        return array($ch_point_lose_self_per);
    }

    public static function only_number(String $content) {
        return preg_replace('#[^0-9]#', '', $content);
    }

    public static function only_alpha_number(String $content) {
        return preg_replace('#[^a-zA-Z0-9]#', '', $content);
    }

    public static function only_hangul(String $content) {
        return preg_replace('#[^가-힣]#', '', $content);
    }

    // 클라이언트 실제 주소를 가져온다.
    public static function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    public static function chk_ipv6($now_ip) {
        if(!preg_match("/^([0-9a-f\.\/:]+)$/",strtolower($now_ip))) {
            return false;
        }

        if(substr_count($now_ip,":") < 2) {
            return false;
        }

        $part = preg_split("/[:\/]/", $now_ip);
        foreach($part as $i) {
            if(strlen($i) > 4) {
                return false;
            }
        }
        return true;
     }
    
    // 접속허용 아이피 체크(화이트리스트)
    public static function checkWhiteList($model_dao, $now_ip) {
        if(CommonUtil::chk_ipv6($now_ip) === true){
            return CommonUtil::checkIpv6($model_dao, $now_ip);
        } else {
            return CommonUtil::checkIpv4($model_dao, $now_ip);
        }
    }
    
    // ipv4 check
    private static function checkIpv4($model_dao, $now_ip) {
        $sql = "SELECT a_ip FROM t_adm_ip where type = 1 and isuse = 1";
        $adm_ip = $model_dao->getQueryData_pre($sql, []);

        // 로그인 허용 아이피 체크
        $login_allowable = false;
        if (null != $adm_ip) {
            foreach ($adm_ip as $key => $value) {
                $arr_ip = explode('.', $value['a_ip']);
                if(count($arr_ip) == 4)
                    $checkChar = $arr_ip[3];
                else
                    $checkChar = $arr_ip[2];

                if ($checkChar == '+') {
                    $arr_now_ip = explode('.', $now_ip);
                    if (count($arr_ip) == 4 && ($arr_now_ip[0] == $arr_ip[0] && $arr_now_ip[1] == $arr_ip[1] && $arr_now_ip[2] == $arr_ip[2])
                        || count($arr_ip) == 3 && ($arr_now_ip[0] == $arr_ip[0] && $arr_now_ip[1] == $arr_ip[1])) {
                        $login_allowable = true;
                        break;
                    }
                } else {
                    if ($now_ip == $value['a_ip']) {
                        $login_allowable = true;
                        break;
                    }
                }
            }
        } else {
            $login_allowable = true;
        }
        
        return $login_allowable;
    }
    
    // ipv6 check
    private static function checkIpv6($model_dao, $now_ip) {
        $sql = "SELECT a_ip FROM t_adm_ip where type = 2 and isuse = 1";
        $adm_ip = $model_dao->getQueryData_pre($sql, []);
        
        // 로그인 허용 아이피 체크
        $login_allowable = false;
        if (null != $adm_ip) {
            foreach ($adm_ip as $key => $value) {
                if ($now_ip == $value['a_ip']) {
                    $login_allowable = true;
                    break;
                }
            }
        }/* else {
            $login_allowable = true;
        }*/
        
        return $login_allowable;
    }
}
?>
