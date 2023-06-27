<?php

namespace App\Util;

class StatusUtil {

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

    static public function fixtureAndScoreboardStatusToKO($code) {
        if ($code == 1)
            return '시작전';
        if ($code == 2)
            return '경기중';
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
            return '보장 손실';
        if ($code == 9)
            return '시작 예정';
    }

    static public function fixtureAndScoreboardStatusEnToKo($code) {
        if ($code == 'Not started')
            return '시작전';
        if ($code == 'In progress')
            return '경기중';
        if ($code == 'Finished')
            return '종료';
        if ($code == 'Cancelled')
            return '취소';
        if ($code == 'Postponed')
            return '연기';
        if ($code == 'Interrupted')
            return '중단';
        if ($code == 'Abandoned')
            return '포기';
        if ($code == 'Coverage lost')
            return '보장 손실';
        if ($code == 'About to start')
            return '시작 예정';
    }

    //! 이걸로 피리어드하고 스포츠 넘겨서 계산하는 함수
    static public function periodCodeToStr($code, $sport_id) {
        // 축구
        if ($sport_id == SOCCER) {
            if ($code == 80)
                return '휴식시간';
            if ($code == 10)
                return '전반전';
            if ($code == 20)
                return '후반전';
            if ($code == 30)
                return '전반전 연장';
            if ($code == 35)
                return '후반전 연장';
            if ($code == 50)
                return '패널티';
            // 농구
        } else if ($sport_id == BASKETBALL) {
            if ($code == 80)
                return '휴식시간';
            if ($code == 1)
                return '1Q';
            if ($code == 2)
                return '2Q';
            if ($code == 3)
                return '3Q';
            if ($code == 4)
                return '4Q';
            if ($code == 40)
                return '연장';
            // 야구
        } else if ($sport_id == BASEBALL) {
            if ($code == 80)
                return '휴식시간';
            if ($code == 1)
                return '1st';
            if ($code == 2)
                return '2st';
            if ($code == 3)
                return '3st';
            if ($code == 4)
                return '4st';
            if ($code == 5)
                return '5st';
            if ($code == 6)
                return '6st';
            if ($code == 7)
                return '7st';
            if ($code == 8)
                return '8st';
            if ($code == 9)
                return '9st';
            if ($code == 40)
                return '연장';
            if ($code == 62)
                return 'ERROR';
            // 배구
        } else if ($sport_id == VOLLEYBALL) {
            if ($code == 80)
                return '휴식시간';
            if ($code == 1)
                return '1세트';
            if ($code == 2)
                return '2세트';
            if ($code == 3)
                return '3세트';
            if ($code == 4)
                return '4세트';
            if ($code == 5)
                return '5세트';
            if ($code == 50)
                return '골든세트';
            // 아이스하키
        } else if ($sport_id == ICEHOCKEY) {
            if ($code == 80)
                return '휴식시간';
            if ($code == 1)
                return '1P';
            if ($code == 2)
                return '2P';
            if ($code == 3)
                return '3P';
            if ($code == 40)
                return '연장';
            if ($code == 50)
                return '패널티';
            // e스포츠
        } else if ($sport_id == ESPORTS) {
            if ($code == 80)
                return '휴식시간';
            if ($code == 1)
                return '1세트';
            if ($code == 2)
                return '2세트';
            if ($code == 3)
                return '3세트';
            if ($code == 4)
                return '4세트';
            if ($code == 5)
                return '5세트';
        }

        if ($code == 100)
            return 'Full time';
        if ($code == 101)
            return 'Full time after overtime';
        if ($code == 102)
            return 'Full time after penalties';

        return '시작전';
    }

    static public function timeSet($time, $sportId) {
        if ($sportId == SOCCER) {
            if (!is_numeric($time)) {
                return 0;
            }
            $time = $time / 60;
            if ($time > 45)
                $time = $time - 45;
        }
        return $time;
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
            $str = '1쿼터';

        else if (0 == strcmp($str, '2nd Period'))
            $str = '2쿼터';

        else if (0 == strcmp($str, '3rd Period'))
            $str = '3쿼터';

        else if (0 == strcmp($str, '4th Period'))
            $str = '4쿼터';


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
            $str = '예';
        else if (0 == strcmp($str, 'No'))
            $str = '아니오';
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

    // 게임결과 문자열로 리턴해준다.
    static public function getMiniGameResultString($betType, $arrResult) {
        $result = '-';

        if ($betType == 3 || $betType == 15) {
            $pb = $arrResult['pb'];
            $num1 = $arrResult['num1'];
            $num2 = $arrResult['num2'];
            $num3 = $arrResult['num3'];
            $num4 = $arrResult['num4'];
            $num5 = $arrResult['num5'];

            if ($num1 == 0) {
                return '-';
            }

            if (0 !== $pb % 2) {
                $result = '[P 홀],';
            } else if (0 === $pb % 2) {
                $result = '[P 짝],';
            }

            if (5 <= $pb && $pb <= 9) {
                $result .= '[P 오버],';
            } else if (0 <= $pb && $pb <= 4) {
                $result .= '[P 언더],';
            }

            $sum_num = $num1 + $num2 + $num3 + $num4 + $num5;
            if (81 <= $sum_num && $sum_num <= 130) {
                $result .= '[대]';
            } else if (65 <= $sum_num && $sum_num <= 80) {
                $result .= '[중]';
            } else {
                $result .= '[소]';
            }
        } else if ($betType == 6) {
            if ($arrResult['res'] == 'Win')
                $result = '승';
            else if ($arrResult['res'] == 'Draw')
                $result = '무';
            else if ($arrResult['res'] == 'Lose')
                $result = '패';
            else if ($arrResult['res'] == 'Under')
                $result = '언더';
            else if ($arrResult['res'] == 'Over')
                $result = '오버';
        } else {
            $line = $arrResult['line'];
            $oe = $arrResult['oe'];
            $start = $arrResult['start'];

            if ($line == 0) {
                return $result;
            }

            if ('Left' !== $start) {
                $result = '[우],';
            } else {
                $result = '[좌],';
            }

            $result .= '[' . $line . '],';

            if ('Even' !== $oe) {
                $result .= '[홀]';
            } else {
                $result .= '[짝]';
            }
        }
        return $result;
    }

    // 배팅아이디 생성
    static public function getBetId($str, $market_id) {
        if (0 == strcmp($str, '1')) {
            $str = $str;
        } else if (0 == strcmp($str, '2')) {
            $str = $str;
        } else if (0 == strcmp($str, '1 And Over'))
            $str = '3';
        else if (0 == strcmp($str, '1 And Under'))
            $str = '4';

        else if (0 == strcmp($str, '2 And Over'))
            $str = '5';
        else if (0 == strcmp($str, '2 And Under'))
            $str = '6';

        else if (0 == strcmp($str, 'X And Over'))
            $str = '7';
        else if (0 == strcmp($str, 'X And Under'))
            $str = '8';

        else if (0 == strcmp($str, '1st Period'))
            $str = '9';

        else if (0 == strcmp($str, '2nd Period'))
            $str = '10';

        else if (0 == strcmp($str, '3rd Period'))
            $str = '11';

        else if (0 == strcmp($str, '4th Period'))
            $str = '12';


        else if (0 == strcmp($str, '1st Half'))
            $str = '13';
        else if (0 == strcmp($str, '2nd Half'))
            $str = '14';
        else if (0 == strcmp($str, 'All Periods The Same'))
            $str = '15';

        else if (0 == strcmp($str, 'No Goal'))
            $str = '16';
        else if (0 == strcmp($str, 'X'))
            $str = '17';
        else if (0 == strcmp($str, '1X'))
            $str = '18';
        else if (0 == strcmp($str, 'X2'))
            $str = '19';
        else if (0 == strcmp($str, '12'))
            $str = '20';
        else if (0 == strcmp($str, 'Over'))
            $str = '21';
        else if (0 == strcmp($str, 'Under'))
            $str = '22';
        else if (0 == strcmp($str, 'Yes'))
            $str = '23';
        else if (0 == strcmp($str, 'No'))
            $str = '24';
        else if (0 == strcmp($str, '1/1'))
            $str = '25';
        else if (0 == strcmp($str, '1/2'))
            $str = '26';
        else if (0 == strcmp($str, 'X/2'))
            $str = '27';
        else if (0 == strcmp($str, '2/1'))
            $str = '28';
        else if (0 == strcmp($str, 'X/1'))
            $str = '29';
        else if (0 == strcmp($str, '2/2'))
            $str = '30';

        else if (0 == strcmp($str, '1/X'))
            $str = '31';

        else if (0 == strcmp($str, '2/X'))
            $str = '32';

        else if (0 == strcmp($str, 'X/X'))
            $str = '33';

        else if (0 == strcmp($str, 'Odd'))
            $str = '34';

        else if (0 == strcmp($str, 'Even'))
            $str = '35';

        else if (0 == strcmp($str, 'Lugano'))
            $str = '36';
        else if (0 == strcmp($str, 'Rosario Central'))
            $str = '37';
        else if (0 == strcmp($str, 'Ross County'))
            $str = '38';

        else if (0 == strcmp($str, 'Exactly'))
            $str = '40';

        else if (0 == strcmp($str, 'Odd'))
            $str = '41';

        else if (0 == strcmp($str, 'Even'))
            $str = '42';

        // 마켓별로 처리해야 할것
        // 정확한 스코어
        if (6 == $market_id || 9 == $market_id || 23 == $market_id || 22 == $market_id) {
            if (0 == strcmp($str, 'Any Other Score')) {
                $str = '39';
            } else {
                $arrStr = explode('-', $str);
                $str = $arrStr[0] . $arrStr[1];
            }
        }

        return $str;
    }

    // 충전보너스 구하기
    static public function getBonusPoint($is_charge_first_per, $charge_types, $bonus_idx){
        $charge_first_per_key = 'bonus_'.$bonus_idx.'_charge_first_per';
        $charge_first_money_key = 'bonus_'.$bonus_idx.'_charge_first_money';
        $charge_first_max_money_key = 'bonus_'.$bonus_idx.'_charge_first_max_money';
        $charge_per_key = 'bonus_'.$bonus_idx.'_charge_per';
        $charge_money_key = 'bonus_'.$bonus_idx.'_charge_money';
        $charge_max_money_key = 'bonus_'.$bonus_idx.'_charge_max_money';
        $displayBonus = $displayMaxBonus = 0;
        
        if(0 == $is_charge_first_per){
            if(0 < $charge_types[0][$charge_first_money_key]){
                $displayBonus = number_format($charge_types[0][$charge_first_money_key]);
            }else{
                $displayBonus = $charge_types[0][$charge_first_per_key].'%';
            }
            
            $displayMaxBonus = number_format($charge_types[0][$charge_first_max_money_key]);
        }else{
            if(0 < $charge_types[0][$charge_money_key]){
                $displayBonus = number_format($charge_types[0][$charge_money_key]);
            }else{
                $displayBonus = $charge_types[0][$charge_per_key].'%';
            }
            
            $displayMaxBonus = number_format($charge_types[0][$charge_max_money_key]);
        }
        
        return array($displayBonus, $displayMaxBonus);
    }
}
