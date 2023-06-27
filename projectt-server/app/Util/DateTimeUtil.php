<?php
namespace App\Util;

class DateTimeUtil
{
    public function default(){
        // 현재 timezone이 설정되어 있지 않은 경우에만 추가
        date_default_timezone_set('Asia/Seoul');

// 모음 // https://link2me.tistory.com/755 여기도 유용한거 많음
        $timestamp = strtotime("Now"); // 1970년 1월 1일 0시 부서 시작하는 유닉스 타임스탬프로 변환
        echo "현재 일시 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 seconds");
        echo "현재로부터 1초 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("-1 seconds");
        echo "현재로부터 1초 앞 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 minutes");
        echo "현재로부터 1분 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 hours");
        echo "현재로부터 1시간 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 days");
        echo "현재로부터 1일 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 week");
        echo "현재로부터 1주 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 months");
        echo "현재로부터 1달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+1 years");
        echo "현재로부터 1년 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+4 years +3 months +2 days +1 hours");
        echo "현재로부터 4년 3개월 2일 1시간 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("2001-01-01");
        echo "2001년 1월 1일 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("2001-01-01 +1 months");
        echo "2001년 1월 1일을 기준으로 1달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("2001/01/01 +2 months");
        echo "2001년 1월 1일을 기준으로 2달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("20010101 +3 months");
        echo "2001년 1월 1일을 기준으로 3달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("2001/01/01 000000 +4 months");
        echo "2001년 1월 1일을 기준으로 4달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+5 months", strtotime("2001/01/01 000000"));
        echo "2001년 1월 1일을 기준으로 5달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";

        $timestamp = strtotime("+6 months", strtotime("2001-01-01 00:00:00"));
        echo "2001년 1월 1일을 기준으로 6달 뒤 : ".date("Y-m-d H:i:s", $timestamp)."<br/>";
    }

    static public function changeToKoString($date) {
        $nums = ['(1)','(2)','(3)','(4)','(5)','(6)','(7)'];
        $strs = ['(월)','(화)','(수)','(목)','(금)','(토)','(일)'];
        foreach ($nums as $key => $num) {
            if (strpos($date, $num)) {
                $date = str_replace($num, $strs[$key], $date);
            }
        }
        return $date;
    }

    static public function getToDayYmd(){
        $timestamp = strtotime("Now");
        return date("Y-m-d", $timestamp);
    }

    static public function getDayYmd($day){
        $timestamp = strtotime("$day days");
        return date("Y-m-d", $timestamp);
    }
    
    static public function getWeekDay($date) {
        $week = array("일" , "월"  , "화" , "수" , "목" , "금" ,"토") ;
        $weekday = $week[ date('w'  , strtotime($date)  ) ] ;
        echo $weekday ;
    }
    
    // 주말(금,토,일)이냐
    static public function isWeekendDay($date) {
        $weekday = date('w', strtotime($date));
        if(0 == $weekday || 5 == $weekday || 6 == $weekday)
            return true;
        else
            return false;
    }
    
    
    static public function getWeek($date){
        $DEFAULT_DAYS = 1; //1 ~ 7 (월 ~ 일)
        list($yy, $mm, $dd) = explode('-', $date); // - 로 잘라서 연,월,일을 구합니다
        $first_day = date('N', mktime(0,0,0,$mm, 1, $yy)); //입력한 날짜의 해당하는 월의 1일이 몇요일인지 구합니다.

        if($first_day <= $DEFAULT_DAYS){  
            $remain_days = $DEFAULT_DAYS - $first_day;
            $next_monday = $remain_days +1;
        }else{
            $remain_days= 7 - $first_day; //1일을 기준으로 해당주가 몇일 남았는지 구합니다.
            $next_monday = $remain_days + $DEFAULT_DAYS +1; //1일 기준으로 차주의 월요일이 몇일인지 구합니다.
            echo $next_monday;
        }

        if($dd < $next_monday){ //입력한 날짜가 전달의 마지막주에 포함될 경우.
            $new_date = date('Y-M-d', mktime(0,0,0,$mm,0,$yy)); //날짜를 0으로 입력해서 지난달의 마지막 주를 새로계산하도록 합니다.
            return getWeek($new_date);
        }else{
            $week = ceil(($dd-($next_monday -1))/7); //몇번째 주차인지 구하기.
            return $week;
        }
    }
}