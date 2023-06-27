<?php

class GameStatusUtil {

    public static function fixtureAndScoreboardStatusToStr($code) {
        if ($code == 1)
            return 'Not started';
        if ($code == 2)
            return 'In progress';
        if ($code == 3)
            return 'Finished';
        if ($code == 4)
            return 'Cancelled';
        if ($code == 5)
            return 'Postponed';
        if ($code == 6)
            return 'Interrupted';
        if ($code == 7)
            return 'Abandoned';
        if ($code == 8)
            return 'Coverage lost';
        if ($code == 9)
            return 'About to start';
    }

    public static function footballPeriodCodeToStr($code) {
        if ($code == 80)
            return '휴식시간';
        else if ($code == 10)
            return '전반전';
        else if ($code == 20)
            return '후반전';
        else if ($code == 30)
            return '전반전 연장';
        else if ($code == 35)
            return '후반전 연장';
        else if ($code == 50)
            return '패널티';
        else if ($code == 100)
            return 'Full time';
        else if ($code == 101)
            return 'Full time after overtime';
        else if ($code == 102)
            return 'Full time after penalties';
        else
            return '시작전';
    }

    public static function footballTimeSet($time) {
        if (!is_numeric($time)) {
            return 0;
        }
        $time = $time / 60;
        if ($time > 45)
            $time = $time - 45;
        return $time;
    }

    public static function timeSet($time, $sportId) {
        if ($sportId == 6046) {
            if (!is_numeric($time)) {
                return 0;
            }
            $time = $time / 60;
            if ($time > 45)
                $time = $time - 45;
        }
        return $time;
    }
    
    static public function StatusToStr($code) {
        if ($code == 0)
            return '논';
        if ($code == 1)
            return '시작전';
        if ($code == 2)
            return '배팅중';
        if ($code == 3)
            return '종료';
        if ($code == 4)
            return '취소';
        if ($code == 5)
            return '연기';
        if ($code == 6)
            return '중단';
        if ($code == 7)
            return '포기';
        if ($code == 8)
            return '커버리지 상실';
        if ($code == 9)
            return '시작예정';
    }

    static public function fixtureAndScoreboardStatusToEN($code) {
        if ($code == 1)
            return 'Not started';
        if ($code == 2)
            return 'In progress';
        if ($code == 3)
            return 'Finished';
        if ($code == 4)
            return 'Cancelled';
        if ($code == 5)
            return 'Postponed';
        if ($code == 6)
            return 'Interrupted';
        if ($code == 7)
            return 'Abandoned';
        if ($code == 8)
            return 'Coverage lost';
        if ($code == 9)
            return 'About to start';
    }
  
 

    // 배팅 네임 변경 수정본
    static public function betNameToDisplay_new($str, $market_id) {
        if (0 == strcmp($str, '1')) {
            if ($market_id == 16 || $market_id == 1677) {
                $str = '홈';
            } else {
                $str = '승';
            }
        } else if (0 == strcmp($str, '2')) {
            if ($market_id == 16 || $market_id == 1677) {
                $str = '원정';
            } else {
                $str = '패';
            }
        } else if (0 == strcmp($str, '1 And Over'))
            $str = '승+오버';
        else if (0 == strcmp($str, '1 And Under'))
            $str = '승+언더';

        else if (0 == strcmp($str, '2 And Over'))
            $str = '패+오버';
        else if (0 == strcmp($str, '2 And Under'))
            $str = '패+언더';

        else if (0 == strcmp($str, 'X And Over'))
            $str = '무+오버';
        else if (0 == strcmp($str, 'X And Under'))
            $str = '무+언더';

        else if (0 == strcmp($str, '1st Period'))
            $str = '1Q';

        else if (0 == strcmp($str, '2nd Period'))
            $str = '2Q';

        else if (0 == strcmp($str, '3rd Period'))
            $str = '3Q';

        else if (0 == strcmp($str, '4th Period'))
            $str = '4Q';


        else if (0 == strcmp($str, '1st Half'))
            $str = '전반';
        else if (0 == strcmp($str, '2nd Half'))
            $str = '후반';
        else if (0 == strcmp($str, 'All Periods The Same')) {
            if ($market_id == 70) {
                $str = '모든 피어리어드 같음';
            } else {
                $str = '전후반 같음';
            }
        } else if (0 == strcmp($str, 'No Goal'))
            $str = '노골';
        else if (0 == strcmp($str, 'X'))
            $str = '무';
        else if (0 == strcmp($str, '1X'))
            $str = '승무';
        else if (0 == strcmp($str, 'X2'))
            $str = '무패';
        else if (0 == strcmp($str, '12'))
            $str = '승패';
        else if (0 == strcmp($str, 'Over'))
            $str = '오버';
        else if (0 == strcmp($str, 'Under'))
            $str = '언더';
        else if (0 == strcmp($str, 'Yes'))
            $str = '예스';
        else if (0 == strcmp($str, 'No'))
            $str = '노';
        else if (0 == strcmp($str, '1/1'))
            $str = '홈/홈';
        else if (0 == strcmp($str, '1/2'))
            $str = '홈/원정';
        else if (0 == strcmp($str, 'X/2'))
            $str = '무/원정';
        else if (0 == strcmp($str, '2/1'))
            $str = '원정/홈';
        else if (0 == strcmp($str, 'X/1'))
            $str = '무/홈';
        else if (0 == strcmp($str, '2/2'))
            $str = '원정/원정';

        else if (0 == strcmp($str, '1/X'))
            $str = '홈/무';

        else if (0 == strcmp($str, '2/X'))
            $str = '원정/무';

        else if (0 == strcmp($str, 'X/X'))
            $str = '무/무';
        
        else if (0 == strcmp($str, 'Any Other Score'))
            $str = '그외 점수';
        
        else if (0 == strcmp($str, 'Odd'))
            $str = '홀';

        else if (0 == strcmp($str, 'Even'))
            $str = '짝';

        return $str;
    }

    // 베팅 결과값에 따른 배경색 리턴
    static public function bet_status_by_color($status) {
        if ($status == 1) {
            $color = '#ffffff';
        } else if ($status == 2) {
            $color = '#47a1ed';
        } else if ($status == 4) {
            $color = '#e71707';
        } else if ($$status == 5) {
            $color = '#c9c9c9';
        } else if ($status == 6) {
            $color = '#fae345';
        }
        return $color;
    }

    static public function get_display_stauts($status) {
        switch ($status) {
            case 1:
            case 8:
            case 9:
                return 1;
            case 2: 
                return 2;
            case 3:
            case 4:
            case 7:
                return 3; // 종료
            case 5:
            case 6:
                return 4; // 대기 
        }
    }

    static public function get_display_stauts_name($status,$type) {
    
        switch ($status) {
            case 1:
                return $type == 1 ? '배팅가능':'배팅전';
            case 2:
                return $type == 1 ? '배팅마감' : '배팅가능';
            case 3:
                return '종료';
            case 4:
                return '대기';
        }
    }
    
    // 미니게임 결과값 한글로 변환
    static public function get_minigame_result_name($result) {
        switch ($result) {
            case 'Odd':
                return '홀';
            case 'Even':
                return '짝';
            case 'Left':
                return '좌';
            case 'Right':
                return '우';
            case 'Win':
                return '승';
            case 'Draw':
                return '무';
            case 'Lose':
                return '패';
            case 'Over':
                return '오버';
            case 'Under':
                return '언더';
            case '1x2':
                return '승무패';
            case 'ou':
                return '오버언더';
        }
    }
    
    // 미니게임명 한글로 변환
    static public function get_minigame_name($game) {
        switch ($game) {
            case 'eospb5':
                return 'EOS 파워볼';
            case 'powerball':
                return '파워볼';
            case 'pladder':
                return '파워사다리';
            case 'kladder':
                return '키노사다리';
            case 'b_soccer':
                return '가상축구';
        }
    }
    
    // 리그명 한글로 변환
    static public function getLeagueName($league) {
        switch ($league) {
            case 'Premiership':
                return '프리미어십';
            case 'Superleague':
                return '슈퍼리그';
            case 'Euro Cup':
                return '유로컵';
            case 'World Cup':
                return '월드컵';
        }
    }

}
