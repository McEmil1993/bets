<?php

namespace App\Controllers;

@set_time_limit(0);
ini_set("memory_limit", -1);

use App\Models\GameModel;
use App\Models\LSportsBetModel;
use App\Models\LSportsLeaguesModel;
use App\Models\LSportsLocationsModel;
use App\Models\LSportsMarketsModel;
use App\Models\LSportsSportsModel;
use App\Util\BetDataUtil;
use App\Util\CodeUtil;
use App\Util\PullOperations;
use CodeIgniter\API\ResponseTrait;
use App\Models\MemberBetModel;
use App\Models\MemberModel;
use App\Models\TGameConfigModel;
use App\Util\DateTimeUtil;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;
use App\GamblePatch\NobleGmPt;
use App\GamblePatch\BullsGmPt;

//use CodeIgniter\HTTP\RequestInterface;

class ClassicController extends BaseController {

    use ResponseTrait;

    protected $gmPt; // 겜블패치 

    public function __construct() {
        if ('K-Win' == config(App::class)->ServerName) {
            $this->gmPt = new KwinGmPt();
        } else if ('Gamble' == config(App::class)->ServerName) {
            $this->gmPt = new GambelGmPt();
        } else if ('BetGo' == config(App::class)->ServerName) {
            $this->gmPt = new BetGoGmPt();
        } else if ('CHOSUN' == config(App::class)->ServerName) {
            $this->gmPt = new ChoSunGmPt();
        } else if ('BETS' == config(App::class)->ServerName) {
            $this->gmPt = new BetsGmPt();
        } else if ('NOBLE' == config(App::class)->ServerName) {
            $this->gmPt = new NobleGmPt();
        } else if ('BULLS' == config(App::class)->ServerName) {
            $this->gmPt = new BullsGmPt();
        }
    }

    //// ajax
    public function ajax_index() {
        $start = time();
        if (false == session()->has('member_idx')) {
            return $this->fail('세션이 종료되었습니다.');
        }
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            return $this->fail('로그인 후 이용해주세요.');
        }

        if (0 < session()->get('tm_unread_cnt')) {
            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $location_id = true === isset($_POST['location_id']) && false === empty($_POST['location_id']) ? $_POST['location_id'] : 0;
        $league_id = true === isset($_POST['league_id']) && false === empty($_POST['league_id']) ? $_POST['league_id'] : 0;
        $sports_id = true === isset($_POST['sports_id']) && false === empty($_POST['sports_id']) ? $_POST['sports_id'] : 0;
        $league_name = true === isset($_POST['league_name']) && false === empty($_POST['league_name']) ? $_POST['league_name'] : '';

        if (!is_int((int) $page) || !is_int((int) $location_id) || !is_int((int) $league_id) || !is_int((int) $sports_id)) {
            return $this->fail('인자값 오류입니다.');
        }

        $arr_config = $this->getConfigData($member);
        if ('Y' == $arr_config['service_sports'] && 9 != $member->getLevel()) {
            return $this->fail('스포츠 점검중입니다.');
        }

        # 배팅 리스트
        $lSportsBetModel = new LSportsBetModel();

        $sportsTotal = $this->getAvgClassicAllCountQueryString($lSportsBetModel);

        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sportsTotal " . count($sportsTotal));
        
        list($sportsList, $leaguesList, $array_fix_all) = $this->setSelectTagData($sports_id, $location_id, $league_id, $league_name, $sportsTotal);

        $start_page = ($page - 1) * SPORRS_BLOCK_COUNT;

        $array_fix = array_slice($array_fix_all, $start_page, SPORRS_BLOCK_COUNT);
        $totalCnt = count($array_fix_all);
        $viewGameList = $this->getGameList($array_fix, $member, $sportsList, $leaguesList, $lSportsBetModel);

        $t_time = time() - $start;
        if (2 < $t_time) {
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports Time : $t_time");
        }


        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** viewGameList " . json_encode($viewGameList));
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sportsList " . json_encode($sportsList));

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'sports' => $sportsList, // 상단 좌측에 사용
            //'leagues' => $leaguesList, // 상단 전체리그 항목에 사용(game02)
            'gameList' => $viewGameList,
            'arr_config' => $arr_config,
            'totalCnt' => $totalCnt,
            'page' => $page,
            'sports_id' => $sports_id,
            'league_name' => $league_name,
            'is_betting_slip' => $member->getIsBettingSlip(),
            'serverName' => config(App::class)->ServerName
        ];

        return $this->respond($response, 200);
    }

    private function getGameList($array_fix, $member, &$sportsList, &$leaguesList, $lSportsBetModel) {
        $viewGameList = [];

        if (0 < count($array_fix)) {
            $getAvgMainClassicQueryString_start = time();
            list($sql, $bind_parame) = $this->getAvgMainClassicQueryString($member->getIdx(), $array_fix, $member->getLevel());
            $resultGameList = $lSportsBetModel->db->query($sql, $bind_parame)->getResult();

            $t_getAvgMainClassicQueryString_start = time() - $getAvgMainClassicQueryString_start;
            if (1 < $t_getAvgMainClassicQueryString_start) {
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgMainClassicQueryString_start :" . $t_getAvgMainClassicQueryString_start);
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgMainClassicQueryString_start query :" . $lSportsBetModel->getLastQuery());
            }

            $gameList = BetDataUtil::mergeMainAvgClassicBetData($resultGameList, $this->logger);

            if (true == isset($gameList) && count($gameList) == 0) {
                //$locationList = [];
                $leaguesList = [];
                if ($sports_id > 0)
                    $sportsList[$sports_id]['count'] = 0;
            }

            if (true == isset($gameList) && 0 < count($gameList)) {
                foreach ($gameList as $game) {
                    $fixture_id = $game['fixture_id'];
                    $fixture_start_date = $game['fixture_start_date'];
                    $viewGameList[$fixture_start_date][$fixture_id][] = $game;
                }
            }

            ksort($viewGameList);

            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** sports mergeMainAvgBetData gameList :" . json_encode($gameList));
        }

        return $viewGameList;
    }

    private function getConfigData($member) {
        $tgcModel = new TGameConfigModel();
        $str_sql_config = "SELECT set_type,set_type_val FROM t_game_config 
                                WHERE u_level = ? and set_type IN ('pre_limit_money','pre_max_money') 
                           UNION ALL 
                           SELECT set_type, set_type_val FROM t_game_config 
                                WHERE set_type IN('service_bonus_folder','odds_3_folder_bonus','odds_4_folder_bonus','odds_5_folder_bonus',
                                'inplay_status','inplay_no_betting_list','service_sports','service_real','odds_6_folder_bonus','odds_7_folder_bonus','limit_folder_bonus') ";
        $result_config = $tgcModel->db->query($str_sql_config, [$member->getLevel()])->getResultArray();

        $arr_config = [];
        foreach ($result_config as $key => $value) {
            $arr_config[$value['set_type']] = $value['set_type_val'];
        }
        return $arr_config;
    }

    private function setSelectTagData($sports_id, $location_id, $league_id, $league_name, $sportsTotal) {

        $sportsList[SOCCER] = array('id' => SOCCER, 'name' => '축구', 'count' => 0, 'order_index' => 0);
        $sportsList[BASKETBALL] = array('id' => BASKETBALL, 'name' => '농구', 'count' => 0, 'order_index' => 1);
        $sportsList[BASEBALL] = array('id' => BASEBALL, 'name' => '야구', 'count' => 0, 'order_index' => 2);
        $sportsList[VOLLEYBALL] = array('id' => VOLLEYBALL, 'name' => '배구', 'count' => 0, 'order_index' => 3);
        $sportsList[ICEHOCKEY] = array('id' => ICEHOCKEY, 'name' => '아이스 하키', 'count' => 0, 'order_index' => 4);
        $sportsList[ESPORTS] = array('id' => ESPORTS, 'name' => '이스포츠', 'count' => 0, 'order_index' => 5);
        $sportsList[UFC] = array('id' => UFC, 'name' => 'UFC', 'count' => 0, 'order_index' => 6);

        $array_fix_all = [];
        $leaguesList = [];

        foreach ($sportsTotal as $value) {
            // 종목 > 지역, 종목 >지역 > 리그별 경기숫자
            $sportsList[$value->fixture_sport_id]['count'] = $sportsList[$value->fixture_sport_id]['count'] + 1;
            if (!isset($sportsList[$value->fixture_sport_id]['leagues'][$value->fixture_league_name])) {
                $sportsList[$value->fixture_sport_id]['leagues'][$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                    'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id, 'count' => 1);
            } else {
                $sportsList[$value->fixture_sport_id]['leagues'][$value->fixture_league_name]['count'] = $sportsList[$value->fixture_sport_id]['leagues'][$value->fixture_league_name]['count'] + 1;
            }

            // 상단 리그 리스트
            //$this->getLeaguesList($sports_id, $location_id, $value,$leaguesList);
            // 검색 값에 대한 리스트 추출하기 
            //$this->getSelectFixtureData($sports_id, $location_id, $league_id, $league_name, $value,$array_fix_all);
            CodeUtil::getSelectFixtureData($sports_id, $location_id, $league_id, $league_name, $value,$array_fix_all);
        }
        
        return [$sportsList, null, $array_fix_all];
    }

    /*private function getSelectFixtureData($sports_id, $location_id, $league_id, $league_name, $value,&$array_fix_all) {
        //$array_fix_all = [];
        if (0 < mb_strlen($league_name, "UTF-8")) {
            //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** league_name ==> ".$league_name);    
            if (0 == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name)
                    ) &&
                    $value->fixture_sport_id == $sports_id) {//011
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name)
                    ) &&
                    $value->fixture_sport_id == $sports_id) {//111
                array_push($array_fix_all, $value->fixture_id);
            } else if ($value->fixture_location_id == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name)
                    ) &&
                    0 == $sports_id) {//110
                array_push($array_fix_all, $value->fixture_id);
            } else if (0 == $location_id &&
                    (false !== strpos($value->fixture_location_name, $league_name) ||
                    false !== strpos($value->fixture_league_name, $league_name) ||
                    false !== strpos($value->p1_team_name, $league_name) ||
                    false !== strpos($value->p2_team_name, $league_name) ||
                    false !== strpos($value->p1_display_name, $league_name) ||
                    false !== strpos($value->p2_display_name, $league_name)
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

        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** getSelectFixtureData array_fix_all" . count($array_fix_all));
         
        return $array_fix_all;
    }*/

    // 상단 선택창에 리그 리스트 값 가져오기 
    private function getLeaguesList($sports_id, $location_id, $value,&$leaguesList) {
        //$leaguesList = [];
        if (!isset($leaguesList[$value->fixture_league_name])) {
            // 선택한 종목이 있으면 해당종목을 체크한다.
            if (isset($sports_id) && $sports_id != 0) {
                if ($sports_id == $value->fixture_sport_id) {
                    // 선택한 지역이 있으면 해당지역만 내려준다.
                    if (isset($location_id) && $location_id != '') {
                        if ($location_id == $value->fixture_location_id) {
                            $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                        }
                    } else {
                        $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                            'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                    }
                }
            } else {
                // 선택한 지역이 있으면 해당지역만 내려준다.
                if (isset($location_id) && $location_id != 0) {
                    if ($location_id == $value->fixture_location_id) {
                        $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                            'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                    }
                } else {
                    $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                        'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                }
            }
        }

        return $leaguesList;
    }

    // 총개수 가져오기 
    private function getAvgClassicAllCountQueryString($lSportsBetModel) {
        $getAvgClassicAllCountQueryString_start = time();
        $startTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " minutes"));
        $endTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " days"));

        $bind_parame = array($startTime, $endTime);
        $sql = "SELECT 
                        fix.fixture_id,
                        fix.fixture_sport_id,
                        fix.fixture_location_id,
                        fix.fixture_league_id,
                        IF(league.display_name is NOT NULL,league.display_name,league.name) as fixture_league_name,
                        league.image_path as fixture_league_image_path,
                        IF(locations.display_name is NOT NULL,locations.display_name,locations.name) as fixture_location_name,
                        locations.image_path as fixture_location_image_path
                        FROM lsports_fixtures as fix ";
        $sql .= " LEFT JOIN     lsports_leagues as league 
                            ON fix.fixture_league_id = league.id   
                  LEFT JOIN 	lsports_participant as p1
                            ON	 fix.fixture_participants_1_id = p1.fp_id
                  LEFT JOIN 	lsports_participant as p2
                            ON	 fix.fixture_participants_2_id = p2.fp_id";
        $sql .= " LEFT JOIN lsports_sports as sports ON fix.fixture_sport_id = sports.id";
        $sql .= " LEFT JOIN lsports_bet as bet ON fix.fixture_id = bet.fixture_id ";
        $sql .= " LEFT JOIN lsports_markets as markets ON bet.markets_id = markets.id ";
        $sql .= " LEFT JOIN lsports_locations as locations ON fix.fixture_location_id = locations.id ";
        $sql .= " WHERE 
                    fix.bet_type = 1 
                    AND IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) BETWEEN ? AND ?
                    AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) IN (1)                     
                    AND fix.admin_bet_status = 'ON' 
                    AND bet.bet_type = 1
                    AND IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) IN (1) 
                    AND bet.admin_bet_status = 'ON' ";
        $sql .= " AND sports.is_use = 1 and sports.bet_type = 1";
        $sql .= " AND league.is_use = 1 and league.bet_type = 1";
        $sql .= " AND markets.id IN (1,52,226) AND markets.bet_group = 1 AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id";
        $sql .= " GROUP BY fix.fixture_id ORDER BY fixture_start_date asc,league.display_name,IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status)";

        //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports getAvgClassicAllCountQueryString query : " . $sql);
        //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports getAvgClassicAllCountQueryString param : " . json_encode($bind_parame));

        $sportsTotal = $lSportsBetModel->db->query($sql, $bind_parame)->getResult();
        $t_getAvgClassicAllCountQueryString_start = time() - $getAvgClassicAllCountQueryString_start;

        if (1 < $t_getAvgClassicAllCountQueryString_start) {
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgClassicAllCountQueryString_start : " . $t_getAvgClassicAllCountQueryString_start);
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgClassicAllCountQueryString_start query : " . $sql);
        }
        return $sportsTotal;
    }

    // 프론트 메인 데이터 가져오기 
    private function getAvgMainClassicQueryString($member_idx, $array_fix, $level) {
        $bind_parame = array($member_idx, $level);
        $sql = "SELECT 
                             bet.bet_id,
                             bet.fixture_id,
                             bet.bet_base_line,
                             bet.bet_line,
                             bet.bet_name,
                             bet.providers_id,
                             IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                             IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                             markets.id as markets_id,
                             markets.name,
                             markets.limit_bet_price,
                             markets.max_bet_price,
                             markets.display_order,
                             markets.menu,
                             fix.fixture_sport_id,sports.name as fixture_sport_name,
                             fix.fixture_location_id, location.name as fixture_location_name,
                             fix.fixture_league_id, league.display_name as fixture_league_name,
                             IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date, 
                             IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status,
                             p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                             p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                             league.display_name as league_display_name,
                             league.image_path as league_image_path,
                             location.image_path as location_image_path,
                             location.name as location_display_name,
                             location.name_en as location_name_en,
                             div_policy.amount,
                             div_policy.amount - (select sum_bet_money from fixtures_bet_sum as bet_sum 
                                where bet_sum.member_idx = ? and bet_sum.fixture_id = fix.fixture_id and bet_sum.bet_type = 1) as leagues_m_bet_money
                    FROM         lsports_bet as bet
                    LEFT JOIN    lsports_fixtures as fix
                    ON       bet.fixture_id = fix.fixture_id 
                    LEFT JOIN 	 lsports_participant as p1
                           ON	 fix.fixture_participants_1_id = p1.fp_id
                    LEFT JOIN 	 lsports_participant as p2
                           ON	 fix.fixture_participants_2_id = p2.fp_id
                    LEFT JOIN 	 lsports_leagues as league
                           ON	 fix.fixture_league_id = league.id 
                    LEFT JOIN 	 dividend_policy as div_policy
                           ON	 league.dividend_rank = div_policy.rank 
                    LEFT JOIN 	 lsports_locations as location
                           ON	 fix.fixture_location_id = location.id
                    LEFT JOIN 	 lsports_markets as markets
                           ON	 bet.markets_id = markets.id   
                     LEFT JOIN 	 lsports_sports as sports
                           ON	 sports.id = fix.fixture_sport_id 
                    WHERE 
                    IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) IN(1) 
                    AND bet.admin_bet_status = 'ON'
                    AND bet.bet_type = 1
                    AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) IN(1) 
                    AND fix.fixture_id IN (" . implode(',', $array_fix) . ") AND fix.bet_type = 1 ";

        $sql = $sql . " AND sports.is_use = 1 and sports.bet_type = 1";
        $sql = $sql . " AND league.is_use = 1 and league.bet_type = 1";
        $sql = $sql . " AND markets.id IN (1,2,3,28,52,226,342) AND markets.bet_group = 1 AND markets.is_delete = 0  AND fix.fixture_sport_id = markets.sport_id AND div_policy.type = 20 AND div_policy.level = ?";
        $sql = $sql . " ORDER BY fixture_start_date ,markets.display_order asc , league.display_name,IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status)";

        return [$sql, $bind_parame];
    }

}
