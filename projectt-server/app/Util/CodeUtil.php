<?php

namespace App\Util;

class CodeUtil {

    static public function sportCodeToStr($code) {
        if ($code == SOCCER)
            return '축구';
        return '야구';
    }

    static public function memberMoneyChargeStatusToStr($code) {
        if ($code == 1)
            return '신청';
        if ($code == 2)
            return '대기';
        if ($code == 3)
            return '완료';
        if ($code == 4)
            return '취소';
        if ($code == 10)
            return '배팅적중';
        if ($code == 11)
            return '관리자 취소';
        return '관리자 문의';
    }

    static public function memberMoneyExchangeStatusToStr($code) {
        if ($code == 1)
            return '신청';
        if ($code == 2)
            return '대기';
        if ($code == 3)
            return '완료';
        if ($code == 4)
            return '관리자 취소';
        if ($code == 11)
            return '취소';
        return '관리자 문의';
    }

    static public function memberBetStatusToStr($code) {
        if ($code == 1)
            return '진행중';
        if ($code == 2)
            return '적중';
        if ($code == 3)
            return '정산완료';
        /*         if ($code == 4)
          return '낙첨'; */
        if ($code == 4)
            return '미적중';
        if ($code == 5)
            return '취소';
        if ($code == 6)
            return '적특';
        return '대기';
    }

    static public function TLogACCodeToStr($code) {
        if ($code == 1)
            return '충전';
        if ($code == 2)
            return '환전';
        if ($code == 3)
            return '베팅';
        if ($code == 4)
            return '베팅취소';
        if ($code == 5)
            return '포인트전환(추가)';
        if ($code == 6)
            return '포인트차감';
        if ($code == 7)
            return '베팅결과처리';
        if ($code == 8)
            return '이벤트충전';
        if ($code == 9)
            return '이벤트차감';
        if ($code == 101)
            return '충전요청';
        if ($code == 102)
            return '환전요청';
        if ($code == 103)
            return '계좌조회';
        if ($code == 111)
            return '충전요청취소';
        if ($code == 112)
            return '환전요청취소';
        if ($code == 113)
            return '충전취소';
        if ($code == 114)
            return '환전취소';
        if ($code == 121)
            return '관리자충전';
        if ($code == 122)
            return '관리자회수';
        if ($code == 301)
            return '총판정산';
        if ($code == 302)
            return '코인충전 요청';
        if ($code == 303)
            return '코인충전 완료';
        if ($code == 998)
            return '데이터복구';
        if ($code == 999)
            return '기타';
        if ($code == USER_PAY_BACK_REWARD_POINT)
            return '페이백 지급';
        if ($code == DAY_CHRGE_EVENT_REWARD_POINT) {
            if ('GAMBLE' == config(App::class)->ServerName) {
                return '겜블지원';
            } else if ('NOVA' == config(App::class)->ServerName) {
                return '노바지원';
            } else if ('NOBLE' == config(App::class)->ServerName) {
                return '노블지원';
            } else if ('BULLS' == config(App::class)->ServerName) {
                return '황소 지원';
            } 
           
        }
        return '-';
    }

    // 맴버 테이블의 상태값
    static public function memberStatusToStr($code) {
        if ($code == 1)
            return '정상';
        if ($code == 2)
            return '정지';
        if ($code == 3)
            return '탈퇴';
        if ($code == 11)
            return '대기';
        return '기타';
    }

    // 서비스 중인 리그,스포츠,지역인지 체크하는 로직
    static public function checkLeagues($Fixture, $lSportsFixturesModel, $logger) {
        $location_id = $Fixture->Location->Id;
        $league_id = $Fixture->League->Id;
        $sport_id = $Fixture->Sport->Id;
        $str_sql = " SELECT id FROM lsports_leagues WHERE id = $league_id AND location_id = $location_id AND sport_id = $sport_id AND is_use = 1";
        $arr_result = $lSportsFixturesModel->db->query($str_sql)->getResult();
        if (0 === count($arr_result)) {
            //$logger->error("getFixturesDailyData lsports_fixtures: $str_sql");
            return false;
        }

        return true;
    }

    static public function rtn_mobile_chk() {
        // 모바일 기종(배열 순서 중요, 대소문자 구분 안함)
        $ary_m = array("iPhone", "iPod", "IPad", "Android", "Blackberry", "SymbianOS|SCH-M\d+", "Opera Mini", "Windows CE", "Nokia", "Sony", "Samsung", "LGTelecom", "SKT", "Mobile", "Phone");

        for ($i = 0; $i < count($ary_m); $i++) {
            if (preg_match("/$ary_m[$i]/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return $ary_m[$i];
                break;
            }
        }
        return "PC";
    }

    static public function get_display_stauts($status, $bet_type) {
        if (1 == $bet_type) {
            switch ($status) {
                case 1:
                case 8: // 신호읽음
                case 9: // 경기시작 30분전 
                    return 1;  // dis 경기전 =>배팅중
                case 2:  // 경기중
                    return 2;  // dis 경기중 => 배팅마감
                case 3: // 경기 종료
                case 4: // 취소
                case 7: // 버려진 경기 
                    return 3;  // dis 종료 => 정산 완료
                case 5: // 지연
                case 6: // 중단 
                    return 4;  // dis 대기
            }
        } else {
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
                    return 3;
                case 5:
                case 6:
                    return 4;
            }
        }
    }

    static public function get_display_stauts_name($status) {
        switch ($status) {
            case 1:
                return '배팅가능';
            case 2:
                return '배팅마감';
            case 3:
                return '종료';
            case 4:
                return '대기';
        }
    }

    // 베팅 결과값에 따른 배경색 리턴
    static public function bet_status_by_color($status) {
        if ($status == 1) {
            $color = '#ffffff';
        } else if ($status == 2) {
            $color = '#47a1ed';
        } else if ($status == 3) {
            $color = '#ffff00';
        } else if ($status == 4) {
            $color = '#e71707';
        } else if ($status == 5) {
            $color = '#c9c9c9';
        } else if ($status == 6) {
            $color = '#fae345';
        }
        return $color;
    }

    // 베팅결과 및 색상값 리턴
    static public function bet_status_by_info($status, $total_bet_money, $take_money) {
        $color = '#ffffff';
        $result = '진행중';

        if (1 == $status) {
            $result = '진행중';
            $color = '#ffffff';
        } else if (5 == $status) {
            $result = '취소';
            $color = '#c9c9c9';
        } else {
            if ($total_bet_money < $take_money) {
                $result = '적중';
                $color = '#47a1ed';
            }
            if (0 == $take_money) {
                $result = '낙첨';
                $color = '#e71707';
            }
            if ($total_bet_money == $take_money) {
                $result = '취소';
                $color = '#c9c9c9';
            }
        }

        return array($result, $color);
    }

    // 베팅결과 및 클래스 리턴
    static public function bet_status_by_info_2($status, $total_bet_money, $take_money) {
        //$class = '';
        $class = 'sports_division3';
        $result = '진행중';

        if (1 == $status) {
            $result = '진행중';
            $class = 'sports_division3';
            //$class = '';
        } else if (5 == $status) {
            $result = '취소';
            $class = 'sports_division1';
            //$class = 'bet_result_icon bg_gray';
        } else {
            if ($total_bet_money < $take_money) {
                $result = '적중';
                $class = 'sports_division2';
                //$class = 'bet_result_icon bg_blue';
            }
            if (0 == $take_money) {
                //$result = '낙첨';
                $result = '미적중';
                $class = 'sports_division1';
                //$class = 'bet_result_icon bg_red';
            }
            if ($total_bet_money == $take_money) {
                $result = '취소';
                $class = 'sports_division1';
                //$class = 'bet_result_icon bg_gray';
            }

            // 환급패치 사용
            if ($total_bet_money > $take_money && 0 < $take_money) {
                $result = '미적중';
                $class = 'sports_division1';
            }
            // bet_result_icon bg_yellow
        }

        return array($result, $class);
    }

    // 베팅 결과값에 따른 배경색 클래스 리턴
    static public function X_bet_status_by_color_2($status) {
        if ($status == 1) {
            $color = '';
        } else if ($status == 2) {
            $color = 'bet_result_icon bg_blue';
        } else if ($status == 3) {
            $color = 'bet_result_icon bg_green';
        } else if ($status == 4) {
            $color = 'bet_result_icon bg_red';
        } else if ($status == 5) {
            $color = 'bet_result_icon bg_gray';
        } else if ($status == 6) {
            $color = 'bet_result_icon bg_yellow';
        }
        return $color;
    }

    //1: 게임 결과 전\\n2: 적중 - 정산 전\\\\\n3: 적중 - 정산 완료\\\\\n4: 적중 실패  \\n5: 취소, \\\\n 6:적특
    // 베팅 결과값에 따른 배경색 클래스 리턴
    static public function bet_status_by_color_2($status) {
        if ($status == 1) {
            $color = 'sports_division3';
        } else if ($status == 2) {
            $color = 'sports_division2';
        } else if ($status == 3) {
            $color = 'sports_division2';
        } else if ($status == 4) { // 낙첨
            $color = 'sports_division1';
        } else if ($status == 5) {
            $color = 'sports_division1';
        } else if ($status == 6) {
            $color = 'sports_division4';
        } else {
            $color = 'sports_division1';
        }
        return $color;
    }

    // 클라이언트 실제 주소를 가져온다.
    static public function get_client_ip() {
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

    static public function idCheck($id) {

        $id = preg_replace("/[^0-9]/", "", $id);

        if (!preg_match("/^01[0-9]{8,9}$/", $id))
            return false;

        return true;
    }

    static public function uuidgen4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    static public function only_number(String $content) {
        return preg_replace('#[^0-9]#', '', $content);
    }

    static public function only_alpha_number(String $content) {
        return preg_replace('#[^a-zA-Z0-9]#', '', $content);
    }

    static public function only_hangul(String $content) {
        return preg_replace('#[^가-힣]#', '', $content);
    }

    // use sports, classic
    static public function getSelectFixtureData($sports_id, $location_id, $league_id, $league_name, $value, &$array_fix_all) {
        //$array_fix_all = [];
        if (0 < mb_strlen($league_name, "UTF-8")) {
            //$logger->error("!!!!!!!!!!!!!!!!!! ***************** league_name ==> ".$league_name);    
            if (0 == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name) ||
                    false !== strpos($value->sport_name, $league_name)
                    ) &&
                    $value->fixture_sport_id == $sports_id) {//011
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name) ||
                    false !== strpos($value->sport_name, $league_name)
                    ) &&
                    $value->fixture_sport_id == $sports_id) {//111
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name) ||
                    false !== strpos($value->sport_name, $league_name)
                    ) &&
                    0 == $sports_id) {//110
                array_push($array_fix_all, $value->fixture_id);
            } else if (0 == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name) ||
                    false !== strpos($value->sport_name, $league_name)
                    ) &&
                    0 == $sports_id) {//010
                array_push($array_fix_all, $value->fixture_id);
            }
        } else {
            if (0 == $location_id && 0 == $league_id && 0 == $sports_id) { // 000
                array_push($array_fix_all, $value->fixture_id);
            } else if (0 == $location_id && 0 == $league_id && $value->fixture_sport_id == $sports_id) { // 001
                array_push($array_fix_all, $value->fixture_id);
            } else if (0 == $location_id && $value->fixture_league_id == $league_id && $value->fixture_sport_id == $sports_id) {//011
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id && $value->fixture_league_id == $league_id && $value->fixture_sport_id == $sports_id) {//111
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id && $value->fixture_league_id == $league_id && 0 == $sports_id) {//110
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id && 0 == $league_id && 0 == $sports_id) {//100
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id && 0 == $league_id && $value->fixture_sport_id == $sports_id) {//101
                array_push($array_fix_all, $value->fixture_id);
            } else if (0 == $location_id && $value->fixture_league_id == $league_id && 0 == $sports_id) {//010
                array_push($array_fix_all, $value->fixture_id);
            }
        }

        //$logger->info("!!!!!!!!!!!!!!!!!! ***************** getSelectFixtureData array_fix_all" . count($array_fix_all));

        return $array_fix_all;
    }

    static public function checkCommercialIp($findMember, $logger) {
        $abuse = new AbuseIPDB($logger);
        if (!$abuse) {
            $logger->error('------------- fail new abuse ----------------------------');
            return [false, '시스템 오류입니다. 관리자에게 문의바랍니다.'];
        }

        if (9 != $findMember->getLevel()) {
            $client_ip = CodeUtil::get_client_ip();
            $ret_check = $abuse->checkEndPoint($client_ip);
            if ('Commercial' == $ret_check->data->usageType) {
                $logger->error('checkCommercialIp id : ' . $findMember->getId() . ' ip : ' . $client_ip . ' ip type : ' . $ret_check->data->usageType);
                $messages = '산업용 아이피로 베팅할 수 없습니다.';
                return [false, $messages];
            }
        }

        return [true, 'success'];
    }

}
