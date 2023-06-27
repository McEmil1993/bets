<?php

namespace App\Controllers;

@set_time_limit(0);
ini_set("memory_limit", -1);

use App\Models\GameModel;
use App\Models\LSportsBetModel;
use App\Models\LSportsLeaguesModel;
use App\Models\LSportsMarketsModel;
use App\Models\LSportsSportsModel;
use App\Util\BetDataUtil;
use App\Util\PullOperations;
use App\Util\StatusUtil;
use CodeIgniter\API\ResponseTrait;
use App\Models\MemberModel;
use App\Models\TGameConfigModel;
use App\Util\CodeUtil;
use App\Util\DateTimeUtil;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;
use App\GamblePatch\NobleGmPt;
use App\GamblePatch\BullsGmPt;
use App\Util\accessLogRedis;

class RealTimeController extends BaseController {

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

    // 실시간 페이지
    public function index() {

        $start = time();
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (false == session()->has('member_idx')) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('로그인 후 이용해주세요.');
            window.location.href='$url';
            </script>";
            return;
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('로그인 후 이용해주세요.');
            window.location.href='$url';
            </script>";
            return;
        }

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $tgcModel = new TGameConfigModel();
        $config = $tgcModel->getMemberMaxBetMoney(2, $member->getLevel());

        $arr_config = $this->gmPt->getConfigData($tgcModel);

        // 실시간 점검 체크
        if ('Y' == $arr_config['service_real'] && 9 != $member->getLevel()) {
            echo "<script>
            alert('실시간 점검중입니다.');
            window.history.back();
            </script>";
            return;
        }

        // 예상당첨금 상한가
        $str_sql_limit = "SELECT set_type_val FROM t_game_config WHERE u_level = ? and set_type = 'real_limit_money'";
        $limit_config = $tgcModel->db->query($str_sql_limit, [$member->getLevel()])->getResultArray();

        /* 어드민 값 셋팅 시작 */
        $lsSportsModel = new LSportsSportsModel();

        /* 어드민 값 셋팅 끝 */
        $startTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " minutes"));
        $endTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " days"));

        $sportsModel = new LSportsSportsModel();
        $sportsData = $sportsModel->where('is_use', 1)->where('bet_type', 2)->find();
        $sportsList = [];

        foreach ($sportsData as $key => $sports) {
            $sports['count'] = 0;
            $sportsList[$sports['display_order']] = $sports;
            //$sportsList[$key]['count'] = 0;
        }
        unset($sportsData);

        if ('OFF' == $arr_config['inplay_status'] && 9 != $member->getLevel()) {
            $arr_sports_ids = explode(',', $arr_config['inplay_no_betting_list']);

            foreach ($arr_sports_ids as $del_sports_id) {
                foreach ($sportsList as $key => $sports) {
                    if ($sports['id'] != $del_sports_id)
                        continue;
                    unset($sportsList[$key]);
                    break;
                }
            }
        }
        ksort($sportsList);

        // 좌측 메뉴 셋팅
        $leaguesGameList = []; # 리그별 게임 리스트
        $locationGameList = []; # 나라별 게임 리스트
        $lSportsBetModel = new LSportsBetModel();

        $current_day = date("d");
        $next_day = date("d", strtotime("+1 days"));
        $bfDate = date("d", strtotime('-1 day'));


        $sports_id = true === isset($_GET['sports_id']) && false === empty($_GET['sports_id']) ? $_GET['sports_id'] : 0;
        $league_name = true === isset($_GET['league_name']) && false === empty($_GET['league_name']) ? $_GET['league_name'] : '';
        $league_id = true === isset($_GET['league_id']) && false === empty($_GET['league_id']) ? $_GET['league_id'] : 0;

        // 실시간 경기 목록
        $str_fix_result = "SELECT fixture_id, livescore,
                         m_live_results_p1,
                         m_live_results_p2,
                         live_results_p1,
                         live_results_p2,break_dt, 
                         league.display_name as fixture_league_name,
                         league.name as league_name,
                         p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                         p2.team_name as p2_team_name, p2.display_name as p2_display_name
                  FROM lsports_fixtures 
                  LEFT JOIN lsports_leagues as league
                   ON lsports_fixtures.fixture_league_id = league.id 
                  LEFT JOIN 	 lsports_participant as p1
                   ON	 lsports_fixtures.fixture_participants_1_id = p1.fp_id
                  LEFT JOIN 	 lsports_participant as p2
                   ON	 lsports_fixtures.fixture_participants_2_id = p2.fp_id
                  WHERE IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.display_status_passivity is NOT NULL ,lsports_fixtures.display_status_passivity,lsports_fixtures.display_status) = 2 
                  AND lsports_fixtures.bet_type = 2
                  AND lsports_fixtures.admin_bet_status = 'ON' AND league.is_use = 1 AND league.bet_type = 2 ";

        $str_fix_time = time();

        $arr_fix_result = $lSportsBetModel->db->query($str_fix_result)->getResult();

        // echo json_encode($arr_fix_result);

        $t_str_fix_time = time() - $str_fix_time;
        if (1 < $t_str_fix_time) {
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_str_fix_time :" . $t_str_fix_time);
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_str_fix_time query :" . $str_fix_result);
        }

        $fixtureList = [];
        if (0 < count($arr_fix_result)) {
            $arr_fix = [];
            foreach ($arr_fix_result as $key => $fix) {
                if (0 < mb_strlen($league_name, "UTF-8")) {
                    if (false !== strpos($fix->fixture_location_name, $league_name)) {
                        $arr_fix[] = $fix->fixture_id;
                    } else if (false !== strpos($fix->p1_team_name, $league_name) || false !== strpos($fix->p2_team_name, $league_name) || false !== strpos($fix->p1_display_name, $league_name) || false !== strpos($fix->p2_display_name, $league_name)) {
                        $arr_fix[] = $fix->fixture_id;
                    }
                } else {
                    $arr_fix[] = $fix->fixture_id;
                }
            }

            $str_fix_ids = implode(',', $arr_fix);
            $getRealTimeData = time();
            $member_idx = session()->get('member_idx');
            list($fixtureList, $sql) = $this->getRealTimeData($lSportsBetModel, $member_idx, '', $str_fix_ids, $arr_fix_result, session()->get('level'));
            $t_getRealTimeData = time() - $getRealTimeData;

            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_getRealTimeData query :" . $sql);
            if (1 < $t_getRealTimeData) {
                //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_getRealTimeData :" . $t_getRealTimeData);
                //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_getRealTimeData query :" . $sql);
            }
        }

        $fixtureCheck_1 = array();
        $locationFixtureCount = array();
        $leaguesList = array();

        $realTimeTotalCnt = 0;

        //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real realTimeTotalCnt 1 ");

        foreach ($fixtureList as $game) {

            if (empty($game['fixture_sport_id'])) {
                continue;
            }

            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real realTimeTotalCnt 1 ");
            // 축구 > 리그 별 정리 
            $leaguesGameList[$game['fixture_sport_id']] = isset($leaguesGameList[$game['fixture_sport_id']]) ? $leaguesGameList[$game['fixture_sport_id']] : [];
            $leaguesGameList[$game['fixture_sport_id']]['leagues_' . $game['fixture_league_name']] = isset($leaguesGameList[$game['fixture_sport_id']]['leagues_' . $game['fixture_league_name']]) ? $leaguesGameList[$game['fixture_sport_id']]['leagues_' . $game['fixture_league_name']] : [];
            array_push($leaguesGameList[$game['fixture_sport_id']]['leagues_' . $game['fixture_league_name']], $game);

            // 축구 > 지역 별 정리 
            $locationGameList[$game['fixture_sport_id']] = isset($locationGameList[$game['fixture_sport_id']]) ? $locationGameList[$game['fixture_sport_id']] : [];
            $locationGameList[$game['fixture_sport_id']]['location_all'][$game['fixture_location_id']] = isset($locationGameList[$game['fixture_sport_id']]['location_all'][$game['fixture_location_id']]) ? $locationGameList[$game['fixture_sport_id']]['location_all'][$game['fixture_location_id']] : [];
            array_push($locationGameList[$game['fixture_sport_id']]['location_all'][$game['fixture_location_id']], $game);

            // 축구 > 지역 > 리그 별 정리 
            $locationGameList[$game['fixture_sport_id']]['location_' . $game['fixture_location_id']][$game['fixture_league_name']] = isset($locationGameList[$game['fixture_sport_id']]['location_' . $game['fixture_location_id']][$game['fixture_league_name']]) ? $locationGameList[$game['fixture_sport_id']]['location_' . $game['fixture_location_id']][$game['fixture_league_name']] : [];
            array_push($locationGameList[$game['fixture_sport_id']]['location_' . $game['fixture_location_id']][$game['fixture_league_name']], $game);

            // 종목 > 지역, 종목 >지역 > 리그별 경기숫자
            if (!in_array($game['fixture_id'], $fixtureCheck_1)) {
                //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real realTimeTotalCnt 2 ");
                $locationFixtureCount[$game['fixture_sport_id']][$game['fixture_location_id']] = isset($locationFixtureCount[$game['fixture_sport_id']][$game['fixture_location_id']]) ? $locationFixtureCount[$game['fixture_sport_id']][$game['fixture_location_id']] : 0;
                $locationFixtureCount[$game['fixture_sport_id']][$game['fixture_location_id']] += 1;
                $locationFixtureCount[$game['fixture_sport_id']][$game['fixture_league_name']] = isset($locationFixtureCount[$game['fixture_sport_id']][$game['fixture_league_name']]) ? $locationFixtureCount[$game['fixture_sport_id']][$game['fixture_league_name']] : 0;
                $locationFixtureCount[$game['fixture_sport_id']][$game['fixture_league_name']] += 1;
                array_push($fixtureCheck_1, $game['fixture_id']);

                foreach ($sportsList as $sports) {
                    //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real realTimeTotalCnt 3 ");
                    if ($game['fixture_sport_id'] != $sports['id'])
                        continue;
                    $sports['count'] = $sports['count'] + 1;
                    $realTimeTotalCnt += 1;
                    break;
                }
            }

            if (!isset($leaguesList[$game['fixture_league_name']])) {
                // 선택한 종목이 있으면 해당종목을 체크한다.
                if (isset($sports_id) && $sports_id != '') {
                    if ($sports_id == $game['fixture_sport_id']) {
                        $leaguesList[$game['fixture_league_name']] = array('id' => $game['fixture_league_id'], 'display_name' => $game['fixture_league_name'],
                            'fixture_league_image_path' => $game['fixture_league_image_path'], 'fixture_location_id' => $game['fixture_location_id']);
                    }
                } else {
                    $leaguesList[$game['fixture_league_name']] = array('id' => $game['fixture_league_id'], 'display_name' => $game['fixture_league_name'],
                        'fixture_league_image_path' => $game['fixture_league_image_path'], 'fixture_location_id' => $game['fixture_location_id']);
                }
            }
        }

        //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real realTimeTotalCnt : ".json_encode($leaguesGameList));

        $t_time = time() - $start;
        if (2 < $t_time) {
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real index Time : $t_time");
        }


        if ('Y' == $arr_config['service_real'] && 9 != $member->getLevel()) {

            // return view("$viewRoot/inspection");

            echo "<script>
            alert('라이브 스포츠 점검중입니다.');
            window.history.back();
            </script>";
            return;
        } else {

            // 좌측 메뉴 셋팅 끝
            return view("$viewRoot/realtime", [
                'sports' => $sportsList,
                'leagues' => $leaguesList, // 상단 전체리그 항목에 사용
                'leaguesGameList' => $leaguesGameList,
                'locationGameList' => $locationGameList,
                //'totalCnt' => $sportsTotalCnt,
                'realTimeTotalCnt' => $realTimeTotalCnt,
                'arr_bonus' => $arr_config,
                'maxBetMoney' => $config[0]['set_type_val'],
                'limitBetMoney' => $limit_config[0]['set_type_val'],
                'locationFixtureCount' => $locationFixtureCount,
                'league_name' => $league_name,
                'betDelayTime' => config(App::class)->betDelayTime,
                'is_betting_slip' => $member->getIsBettingSlip()
                    ]
            );
        }
    }

    private function getAvgSportsAllCountQueryString() {
        $sql = "SELECT 
                        fix.fixture_id,
                        fix.fixture_sport_id,
                        fix.fixture_location_id,
                        fix.fixture_league_id,
                        IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                        sports.name as sport_name,
                        sports.display_order as sport_display_order,
                        league.display_name as fixture_league_name,
                        league.name as league_name,
                        league.image_path as fixture_league_image_path,
                        IF(locations.display_name is NOT NULL,locations.display_name,locations.name) as fixture_location_name,
                        locations.image_path as fixture_location_image_path
                        FROM lsports_fixtures as fix ";
        $sql .= " LEFT JOIN lsports_leagues as league ON fix.fixture_league_id = league.id";
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

        $sql = $sql . " AND sports.is_use = 1 and sports.bet_type = 1";
        $sql = $sql . " AND league.is_use = 1 and league.bet_type = 1";
        $sql = $sql . " AND markets.bet_group = 1 AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id";
        $sql .= " GROUP BY fix.fixture_id,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) ORDER BY fixture_start_date asc ,league.display_name ,fix.fixture_sport_id ";
        return $sql;
    }

    private function getRealTimeData($lSportsBetModel, $member_idx, $str_sports_ids, $str_fix_ids, $arr_fix_result, $level) {

        if (false === isset($str_fix_ids) || true === empty($str_fix_ids)) {
            $str_fix_ids = '0';
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getRealTimeData str_fix_ids :" . $str_fix_ids);
        }

        $sql = "SELECT   bet.bet_id,
                             bet.fixture_id,
                             bet.bet_base_line,
                             bet.bet_line,
                             bet.bet_name,
                             bet.providers_id,
                             bet.update_dt,
                             bet.last_update,
                             IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                             IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                             markets.id as markets_id,
                             markets.name,
                             markets.menu,
                             markets.main_book_maker,
                             markets.sub_book_maker,
                             markets.limit_bet_price,
                             markets.max_bet_price,
                             markets.is_main_menu,
                             markets.display_order,
                             markets.main_display_order,
                             markets.not_display_period,
                             markets.not_display_time,
                             markets.not_display_score,
                             markets.not_display_score_team_type,
                             fix.fixture_sport_id, sports.name as fixture_sport_name,
                             fix.fixture_location_id, 
                             
                             IF(location.display_name is NOT NULL,location.display_name,location.name) as fixture_location_name,
                             fix.fixture_league_id, league.display_name as fixture_league_name,
                             IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date, 
                             IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status,
                             p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                             p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                             league.display_name as league_display_name ,
                             league.image_path as league_image_path,
                             location.image_path as location_image_path,
                             div_policy.amount,
                             div_policy.amount - (select sum_bet_money from fixtures_bet_sum as bet_sum 
                                where bet_sum.member_idx = ? and bet_sum.fixture_id = fix.fixture_id and bet_sum.bet_type = 2) as leagues_m_bet_money,
                             league.quarter_time
                    FROM     lsports_bet as bet
                    LEFT JOIN    lsports_fixtures as fix
                    ON           bet.fixture_id = fix.fixture_id
                    LEFT JOIN 	 lsports_participant as p1
                           ON	 fix.fixture_participants_1_id = p1.fp_id
                    LEFT JOIN 	 lsports_participant as p2
                           ON	 fix.fixture_participants_2_id = p2.fp_id
                    LEFT JOIN 	 lsports_leagues as league
                           ON	 fix.fixture_league_id = league.id 
                    LEFT JOIN    dividend_policy as div_policy
		           ON	 league.dividend_rank = div_policy.rank           
                    LEFT JOIN 	 lsports_locations as location
                           ON	 fix.fixture_location_id = location.id
                    LEFT JOIN    lsports_markets as markets
                           ON    bet.markets_id = markets.id  
                    LEFT JOIN 	 lsports_sports as sports
                           ON	 sports.id = fix.fixture_sport_id 
                    WHERE    
                    bet.bet_type = 2 
                    AND IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) IN (1)
                    AND bet.admin_bet_status = 'ON'
                    AND fix.fixture_id in ($str_fix_ids) 
                    AND fix.bet_type = 2 
                    AND league.is_use = 1 and league.bet_type = 2";
        if (true === isset($str_sports_ids) && false === empty($str_sports_ids)) {
            $sql = $sql . " AND sports.id in($str_sports_ids) and sports.is_use = 1 and  sports.bet_type = 2";
        } else {
            $sql = $sql . " and sports.is_use = 1 and  sports.bet_type = 2";
        }

        $sql = $sql . " AND markets.bet_group = 2 AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id AND div_policy.type = 2 AND div_policy.level = ?";
        $sql = $sql . " GROUP BY bet.bet_id ORDER BY fixture_start_date asc ,league.display_name,bet.bet_name,bet.bet_base_line *1 ";


        $fixtureList = $lSportsBetModel->db->query($sql, [$member_idx, $level])->getResult();

        foreach ($fixtureList as $key => $value) {
            foreach ($arr_fix_result as $fix) {
                if ($value->fixture_id === $fix->fixture_id) {
                    $value->livescore = $fix->livescore;
                    $value->m_live_results_p1 = $fix->m_live_results_p1;
                    $value->m_live_results_p2 = $fix->m_live_results_p2;
                    $value->live_results_p1 = $fix->live_results_p1;
                    $value->live_results_p2 = $fix->live_results_p2;
                    $value->break_dt = $fix->break_dt;
                }
            }
        }

        $arrList = BetDataUtil::mergeRealBetData2($fixtureList, $this->logger);

        return [$arrList, $sql];

        //return $sql;
    }

    //// API
    // 메인 화면
    // 진행중인 실시간 경기목록
    public function getMainRealTimeList() {
        $sportsId = isset($_POST['sportsId']) ? $_POST['sportsId'] : 0;

        if (!is_int((int) $sportsId)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** getMainRealTimeList: ==> ");
            die();
        }
        $current_day = date("d");
        $bfDate = date("d", strtotime('-1 day'));
        $lSportsBetModel = new LSportsBetModel();

        $str_fix_result = "SELECT fixture_id, livescore,
                         m_live_results_p1,
                         m_live_results_p2,
                         live_results_p1,
                         live_results_p2, break_dt FROM lsports_fixtures WHERE display_status = 2 
                  AND bet_type = 2
                  AND  admin_bet_status = 'ON' ";
        $arr_fix_result = $lSportsBetModel->db->query($str_fix_result)->getResult();


        $viewGameList = [];
        if (0 < count($arr_fix_result)) {
            $arr_fix = [];
            foreach ($arr_fix_result as $key => $fix) {
                $arr_fix[] = $fix->fixture_id;
            }

            $str_fix_ids = implode(',', $arr_fix);
            $member_idx = session()->get('member_idx');
            list($viewGameList, $sql) = $this->getRealTimeData($lSportsBetModel, $member_idx, (String) $sportsId, $str_fix_ids, $arr_fix_result, session()->get('level'));
        }

        $mainRealTimeGameList = array();
        $array_in_real_main_market_id = array();
        foreach ($viewGameList as $fixture_key => $game) {
            $market_id = $game['markets_id'];
            $fixture_id = $game['fixture_id'];
            $fixture_sport_id = $game['fixture_sport_id'];
            if ($fixture_sport_id == SOCCER) {
                $array_in_real_main_market_id = array(1);
            } else if ($fixture_sport_id == BASKETBALL) {
                $array_in_real_main_market_id = array(226);
            } else if ($fixture_sport_id == VOLLEYBALL) {
                $array_in_real_main_market_id = array(52);
            } else if ($fixture_sport_id == BASEBALL) {
                $array_in_real_main_market_id = array(42, 43, 44, 49, 348, 349);
            } else if ($fixture_sport_id == ICEHOCKEY) {
                $array_in_real_main_market_id = array(1);
            } else if ($fixture_sport_id == ESPORTS) {
                $array_in_real_main_market_id = array(52);
            }

            if (true == in_array($market_id, $array_in_real_main_market_id)) {
                if (!isset($mainRealTimeGameList[$fixture_id]))
                    $mainRealTimeGameList[$fixture_id][] = $game;
            }
        }

        $mainRealTimeGameList = array_splice($mainRealTimeGameList, 0, 4);

        //$this->logger->debug(json_encode($mainRealTimeGameList, JSON_UNESCAPED_UNICODE));
        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'event' => $mainRealTimeGameList,
            ]
        ];
        return $this->respond($response, 200);
    }

    // 실시간 페이지 - 라이브 스코어 (상단)
    public function getRealTimeGameLiveScore() {
        $fixtureId = $_POST['fixtureId'];

        if (!is_int((int) $fixtureId)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** getRealTimeGameLiveScore: ==> ");
            die();
        }

        $pullOperations = new PullOperations('ODD', $this->logger);

        if ($fixtureId == 0) {
            // 0 인경우 데이터 주지 않도록 처리
            $response = [
                'result_code' => 200,
                'messages' => '조회 성공 (데이터 없음)',
                'data' => [
                    'event' => [],
                ]
            ];
            return $this->respond($response, 200);
        }

        $event = [];

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'event' => $event,
            ]
        ];
        return $this->respond($response, 200);
    }

    // 실시간 페이지 >> 실시간 배팅 리스트(프론트에서 주기적으로 호출)
    public function getRenewRealTimeGameLiveScoreList() {
        $start = time();
        if (false == session()->has('member_idx')) {
            return $this->fail('로그인 후 이용해주세요.');
        }

        $member_idx = session()->get('member_idx');
        $arr_config = $this->gmPt->getConfigData();
        $sportsModel = new LSportsSportsModel();
        $sportsList = $sportsModel->where('is_use', 1)->where('bet_type', 2)->find();
        $arr_sports_ids = [];
        if ('OFF' == $arr_config['inplay_status'] && 9 != session()->get('level')) {
            $arr_del_sports_ids = explode(',', $arr_config['inplay_no_betting_list']);
            foreach ($arr_del_sports_ids as $del_sports_id) {
                foreach ($sportsList as $key => $sports) {
                    if ($sports['id'] != $del_sports_id) {
                        continue;
                    }
                    unset($sportsList[$key]);
                    break;
                }
            }
        }
        foreach ($sportsList as $key => $sports) {
            $arr_sports_ids[] = $sports['id'];
        }
        $str_sports_ids = implode(',', $arr_sports_ids);


        $location_id = true === isset($_POST['location_id']) && false === empty($_POST['location_id']) ? $_POST['location_id'] : 0;
        $sports_id = true === isset($_POST['sports_id']) && false === empty($_POST['sports_id']) ? $_POST['sports_id'] : 0;
        $league_name = true === isset($_POST['league_name']) && false === empty($_POST['league_name']) ? $_POST['league_name'] : '';
        $league_id = true === isset($_POST['league_id']) && false === empty($_POST['league_id']) ? $_POST['league_id'] : 0;

        //$this->logger->debug(" getRenewRealTimeGameLiveScoreList league_name => ".$league_name);

        $lSportsBetModel = new LSportsBetModel();
        $str_fix_result = "SELECT fixture_id, livescore,
                         m_live_results_p1,
                         m_live_results_p2,
                         live_results_p1,
                         live_results_p2,break_dt, 
                         league.display_name as fixture_league_name,
                         league.name as league_name,
                         p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                         p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                         IF(locations.display_name is NOT NULL,locations.display_name,locations.name) as fixture_location_name,
                         lsports_fixtures.fixture_sport_id,
                         lsports_fixtures.fixture_location_id,
                         lsports_fixtures.fixture_league_id
                  FROM lsports_fixtures 
                  LEFT JOIN lsports_leagues as league
                   ON lsports_fixtures.fixture_league_id = league.id 
                  LEFT JOIN 	 lsports_participant as p1
                   ON	 lsports_fixtures.fixture_participants_1_id = p1.fp_id
                  LEFT JOIN 	 lsports_participant as p2
                   ON	 lsports_fixtures.fixture_participants_2_id = p2.fp_id
                  LEFT JOIN lsports_locations as locations 
                  ON lsports_fixtures.fixture_location_id = locations.id
                  WHERE IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.display_status_passivity is NOT NULL ,lsports_fixtures.display_status_passivity,lsports_fixtures.display_status) IN (2)
                  AND lsports_fixtures.bet_type = 2
                  AND lsports_fixtures.admin_bet_status = 'ON' AND league.is_use = 1 AND league.bet_type = 2";

        $str_fix_time = time();
        $arr_fix_result = $lSportsBetModel->db->query($str_fix_result)->getResult();
        //$this->logger->debug(json_encode($arr_fix_result));
        $t_str_fix_time = time() - $str_fix_time;
        if (1 < $t_str_fix_time) {
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime getRenewRealTimeGameLiveScoreList t_str_fix_time :" . $t_str_fix_time);
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime getRenewRealTimeGameLiveScoreList t_str_fix_time query :" . $str_fix_result);
        }

        $viewGameList = [];
        $leaguesList = array();
        if (0 < count($arr_fix_result)) {
            $array_fix_all = [];
            foreach ($arr_fix_result as $key => $fix) {
                // 상단 리그 리스트
                if (!isset($leaguesList[$fix->fixture_league_name])) {
                    // 선택한 종목이 있으면 해당종목을 체크한다.
                    if (isset($sports_id) && $sports_id != '') {
                        if ($sports_id == $fix->fixture_sport_id) {
                            $leaguesList[$fix->fixture_league_name] = array('id' => $fix->fixture_league_id, 'display_name' => $fix->fixture_league_name,
                                'fixture_league_image_path' => ($fix->fixture_league_image_path ?? ''), 'fixture_location_id' => $fix->fixture_location_id);
                        }
                    } else {
                        $leaguesList[$fix->fixture_league_name] = array('id' => $fix->fixture_league_id, 'display_name' => $fix->fixture_league_name,
                            'fixture_league_image_path' => ($fix->fixture_league_image_path ?? ''), 'fixture_location_id' => $fix->fixture_location_id);
                    }
                }

                if (0 < mb_strlen($league_name, "UTF-8")) {
                    //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** league_name ==> ".$league_name);    
                    if (0 == $location_id && (false !== strpos($fix->fixture_location_name, $league_name)) && $fix->fixture_sport_id == $sports_id) {//011
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && (false !== strpos($fix->fixture_location_name, $league_name)) && $fix->fixture_sport_id == $sports_id) {//111
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && (false !== strpos($fix->fixture_location_name, $league_name)) && 0 == $sports_id) {//110
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if (0 == $location_id && (false !== strpos($fix->fixture_location_name, $league_name)) && 0 == $sports_id) {//010
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if (0 == $location_id && (false !== strpos($fix->p1_team_name, $league_name) || false !== strpos($fix->p2_team_name, $league_name) || false !== strpos($fix->p1_display_name, $league_name) || false !== strpos($fix->p2_display_name, $league_name)) && $fix->fixture_sport_id == $sports_id) {
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && (false !== strpos($fix->p1_team_name, $league_name) || false !== strpos($fix->p2_team_name, $league_name) || false !== strpos($fix->p1_display_name, $league_name) || false !== strpos($fix->p2_display_name, $league_name)) && $fix->fixture_sport_id == $sports_id) {//111
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && (false !== strpos($fix->p1_team_name, $league_name) || false !== strpos($fix->p2_team_name, $league_name) || false !== strpos($fix->p1_display_name, $league_name) || false !== strpos($fix->p2_display_name, $league_name)) && 0 == $sports_id) {//110
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if (0 == $location_id && (false !== strpos($fix->p1_team_name, $league_name) || false !== strpos($fix->p2_team_name, $league_name) || false !== strpos($fix->p1_display_name, $league_name) || false !== strpos($fix->p2_display_name, $league_name) ) && 0 == $sports_id) {//010
                        array_push($array_fix_all, $fix->fixture_id);
                    }
                } else {
                    if (0 == $location_id && 0 == $league_id && 0 == $sports_id) { // 000
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if (0 == $location_id && 0 == $league_id && $fix->fixture_sport_id == $sports_id) { // 001
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if (0 == $location_id && $fix->fixture_league_id == $league_id && $fix->fixture_sport_id == $sports_id) {//011
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && $fix->fixture_league_id == $league_id && $fix->fixture_sport_id == $sports_id) {//111
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && $fix->fixture_league_id == $league_id && 0 == $sports_id) {//110
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && 0 == $league_id && 0 == $sports_id) {//100
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if ($fix->fixture_location_id == $location_id && 0 == $league_id && $fix->fixture_sport_id == $sports_id) {//101
                        array_push($array_fix_all, $fix->fixture_id);
                    } else if (0 == $location_id && $fix->fixture_league_id == $league_id && 0 == $sports_id) {//010
                        array_push($array_fix_all, $fix->fixture_id);
                    }
                }
            }

            $str_fix_ids = implode(',', $array_fix_all);

            $getRealTimeData = time();
            list($liveList, $sql) = $this->getRealTimeData($lSportsBetModel, $member_idx, $str_sports_ids, $str_fix_ids, $arr_fix_result, session()->get('level'));

            $t_getRealTimeData = time() - $getRealTimeData;
            if (1 < $t_getRealTimeData) {
                //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_getRealTimeData :" . $t_getRealTimeData);
                // $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** realtime t_getRealTimeData query :" . $sql);
            }

            foreach ($liveList as $fixture_key => $game) {
                if (empty($game['fixture_sport_id'])) {
                    continue;
                }

                $fixture_sport_id = $game['fixture_sport_id'];
                if (isset($sports_id) && $sports_id != '') {
                    if ($sports_id == $fixture_sport_id) {
                        $market_id = $game['markets_id'];
                        $fixture_id = $game['fixture_id'];
                        $display_order = $game['display_order'];
                        $viewGameList[$fixture_sport_id][$fixture_id][$display_order][$market_id][] = $game;
                    }
                } else {
                    $market_id = $game['markets_id'];
                    $fixture_id = $game['fixture_id'];
                    $display_order = $game['display_order'];
                    $viewGameList[$fixture_sport_id][$fixture_id][$display_order][$market_id][] = $game;
                }

                if (false === isset($viewGameList[$fixture_sport_id][$fixture_id]['market_data'][$market_id])) {
                    $viewGameList[$fixture_sport_id][$fixture_id]['market_data'][$market_id] = $market_id;
                    $viewGameList[$fixture_sport_id][$fixture_id]['game_count'] = ($viewGameList[$fixture_sport_id][$fixture_id]['game_count'] ?? 0) + 1;
                }
            }
        }

        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** real getRenewRealTimeGameLiveScoreList viewGameList : ". json_encode($viewGameList));

        $t_time = time() - $start;
        if (2 < $t_time) {
            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** real getRenewRealTimeGameLiveScoreList Time : $t_time");
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'live_list' => $viewGameList,
                'league_name' => $league_name,
                'leagues' => $leaguesList, // 상단 전체리그 항목에 사용
            ]
        ];

        //$this->logger->debug('getRealTimeGameLiveScoreList : '. json_encode($response, JSON_UNESCAPED_UNICODE));
        return $this->respond($response, 200);
    }

    // 실시간 베팅전 스코어 저장
    public function setRealTimeScore() {
        $arr_fixture = isset($_POST['fixture_id']) ? $_POST['fixture_id'] : ''; // fixture_id,fixture_id...

        if (false == session()->has('member_idx')) {
            $this->logger->error(__FUNCTION__ . ' not find member_idx : ' . session()->get('member_idx'));
            $response['messages'] = '유저를 찾을수 없습니다.';
            return $this->fail($response);
        }

        if ('' == $arr_fixture) {
            $this->logger->error(__FUNCTION__ . ' not find fixture_id : ' . json_encode($arr_fixture));
            $response['messages'] = 'not find fixture_id';
            return $this->fail($response);
        }

        $accessLogRedis = new accessLogRedis(config(App::class)->redis_ip, config(App::class)->redis_port, config(App::class)->redis_password, config(App::class)->redis_database, 180, $this->logger);
        if (!$accessLogRedis->connect()) {
            $response['messages'] = '레디스 연결 실패.';
            return $this->fail($response);
        }

        // get current score
        $lsSportsModel = new LSportsSportsModel();
        $sql = "SELECT fixture_id, livescore, fixture_sport_id, live_results_p1, live_results_p2 from lsports_fixtures where fixture_id in ($arr_fixture) and bet_type = ?";
        $result = $lsSportsModel->db->query($sql, [2])->getResultArray();
        if (0 == count($result)) {
            $this->logger->error(__FUNCTION__ . ' not find arr_fixture : ' . json_encode($arr_fixture));
            $response['messages'] = '존재하지 않는 경기입니다.';
            return $this->fail($response);
        }

        foreach ($result as $key => $value) {
            // 배구면 세트점수로 넘어오기 때문에 피리어드에서 각 세트당 점수를 합산해줘야 함.
            $live_results_p1 = 0;
            $live_results_p2 = 0;
            $fixture_id = $value['fixture_id'];
            $fixture_sport_id = $value['fixture_sport_id'];
            $livescore = $value['livescore'];

            if (VOLLEYBALL == $fixture_sport_id) {
                $arrLivescore = json_decode($value['livescore'], true);
                foreach ($arrLivescore['Periods'] as $key => $value) {
                    if ($value['Type'] < 6) {
                        $live_results_p1 += $value['Results'][0]['Value'];
                        $live_results_p2 += $value['Results'][1]['Value'];
                    }
                }
            } else if (ESPORTS == $fixture_sport_id && ($value['livescore'] == '' || $value['livescore'] == null)) {
                $live_results_p1 = $live_results_p2 = 0;
            } else {
                //$totalLiveScore = $arrLivescore['Scoreboard']['Results'][0]['Value'] + $arrLivescore['Scoreboard']['Results'][1]['Value'];
                $live_results_p1 = $value['live_results_p1'];
                $live_results_p2 = $value['live_results_p2'];
            }

            // save redis---+
            $redis_key = 'realtime_bet_' . session()->get('member_idx') . '_' . $fixture_id;
            $arrRedisData = json_encode(array('live_results_p1' => $live_results_p1, 'live_results_p2' => $live_results_p2
                , 'fixture_sport_id' => $fixture_sport_id, 'livescore' => $livescore));
            $accessLogRedis->set($redis_key, json_encode($arrRedisData));
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
            ]
        ];

        return $this->respond($response, 200);
    }

    // 실시간 경기 베팅가능 체크
    public function checkRealTimeGameLiveScore() {
        $fixture_id = isset($_POST['fixture_id']) ? $_POST['fixture_id'] : 0;
        $live_score_1 = isset($_POST['live_score_1']) ? $_POST['live_score_1'] : 0;
        $live_score_2 = isset($_POST['live_score_2']) ? $_POST['live_score_2'] : 0;
        $betId = isset($_POST['betId']) ? $_POST['betId'] : 0;
        $betList = isset($_POST['betList']) ? $_POST['betList'] : '';
        if (!is_int((int) $fixture_id) || !is_int((int) $live_score_1) || !is_int((int) $live_score_2) || !is_int((int) $betId)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** checkRealTimeGameLiveScore: ==> ");
            die();
        }

        if (false == session()->has('member_idx')) {
            $this->logger->error(__FUNCTION__ . ' not find member_idx : ' . session()->get('member_idx'));
            $response['messages'] = '유저를 찾을수 없습니다.';
            return $this->fail($response);
        }
        $member_idx = session()->get('member_idx');

        // create redis object
        $accessLogRedis = new accessLogRedis(config(App::class)->redis_ip, config(App::class)->redis_port, config(App::class)->redis_password, config(App::class)->redis_database, 120, $this->logger);
        if (!$accessLogRedis->connect()) {
            $this->logger->error(__FUNCTION__ . ' redis connect fail');
            $response['messages'] = '레디스 연결 실패.';
            return $this->fail($response);
        }

        $live_score_1 = 0;
        $live_score_2 = 0;

        //$checkBaseBallMarkets = array(Q2_1X2, Q2_OVER_UNDER, Q3_1X2,Q4_1X2,Q3_OVER_UNDER,Q4_OVER_UNDER,Q5_OVER_UNDER, Q5_1X2, Q6_1X2, Q6_OVER_UNDER, Q7_1X2, Q7_OVER_UNDER);
        $checkBasketBallMarkets = array(Q1_OVER_UNDER, Q2_OVER_UNDER, Q3_OVER_UNDER, Q4_OVER_UNDER);

        foreach ($betList as $key => $value) {
            $fixture_id = $value['fixture_id'];

            if (!is_int((int) $fixture_id) /* || !is_int((int)$live_score_1) || !is_int((int)$live_score_2) || !is_int((int)$betId) */) {
                $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** checkRealTimeGameLiveScore foreach: ==> ");
                die();
            }

            // get before bet_data is redis...
            $redis_key = 'realtime_bet_' . $member_idx . '_' . $fixture_id;
            //echo $redis_key;

            $redis_result = $accessLogRedis->get($redis_key);
            
            
                 
            if (null == $redis_result) {
                $response['messages'] = '스코어가 맞지않습니다.!!!';
                return $this->fail($response);
            }
            $redis_result = json_decode(json_decode($redis_result), true);

            //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** checkRealTimeGameLiveScore redis_result: ==> ".$redis_result);
            $live_score_1 = $redis_result['live_results_p1'];
            $live_score_2 = $redis_result['live_results_p2'];


            if (0 == $fixture_id) {
                $response['messages'] = '잘못된 경기번호입니다.';
                return $this->fail($response);
            }

            $lsSportsModel = new LSportsSportsModel();

            // 축구(6046), 아이스하키(35232), 농구(48242), 미식축구(131506), 배구(154830), 야구(154914),이스포츠(687890),UFC(154919)

            $sql = "SELECT livescore, fixture_sport_id, live_results_p1, live_results_p2, fixture_sport_id from lsports_fixtures where fixture_id = ? and bet_type = 2";

            $result = $lsSportsModel->db->query($sql, [$fixture_id])->getResult();

            // 베팅정보를 가져온다. 
            $sql = "SELECT markets_id  FROM lsports_bet where bet_type = 2 and fixture_id = ? and bet_id = ?";
            $betInfo = $lsSportsModel->db->query($sql, [$fixture_id, $betId])->getResult();

            $checkScore = 0;
            //$checkMarkets = array(42, 45, 43, 46, 44, 47, 49, 48, 348, 352, 349, 353);
            // 축구
            if (SOCCER == $result[0]->fixture_sport_id) {
                $checkScore = 1;
            } else if (ICEHOCKEY == $result[0]->fixture_sport_id) {// 7초있다가 
                $checkScore = 1;
            } else if (BASKETBALL == $result[0]->fixture_sport_id) {
                $checkScore = 4;
            } else if (VOLLEYBALL == $result[0]->fixture_sport_id) {
                //$checkScore = 2;
                $checkScore = 9999;
            } else if (BASEBALL == $result[0]->fixture_sport_id) {
                // 야구는 아래 마켓 타입이면 통과
                //if (in_array($betInfo[0]->markets_id, $checkMarkets)) {
                //    $checkScore = 9999;
                //} else {
                //$checkScore = 1;
                //}
                $checkScore = 1;
            } else {
                $checkScore = 9999;
            }

            // 스코어 차이체크
            $betTotalLiveScore = $live_score_1 + $live_score_2;
            // 배구면 세트점수로 넘어오기 때문에 피리어드에서 각 세트당 점수를 합산해줘야 함.
            $totalLiveScore = 0;
            if (VOLLEYBALL == $result[0]->fixture_sport_id) {
                $livescore = json_decode($result[0]->livescore, true);
                foreach ($livescore['Periods'] as $key => $value) {
                    if ($value['Type'] < 6) {
                        $totalLiveScore += ($value['Results'][0]['Value'] + $value['Results'][1]['Value']);
                    }
                }
            } else if (ESPORTS == $result[0]->fixture_sport_id && ($result[0]->livescore == '' || $result[0]->livescore == null)) {
                $totalLiveScore = 0;
            } else {
                $livescore = json_decode($result[0]->livescore, true);
                $totalLiveScore = $livescore['Scoreboard']['Results'][0]['Value'] + $livescore['Scoreboard']['Results'][1]['Value'];
            }

            if (BASKETBALL == $result[0]->fixture_sport_id && in_array($betInfo[0]->markets_id, $checkBasketBallMarkets) && 'Over' == $betInfo[0]->bet_name && 3 == abs($betTotalLiveScore - $totalLiveScore)) {
                $response['messages'] = '스코어가 맞지 않아서 배팅에 실패했습니다.';
                return $this->fail($response);
            }

            if ($checkScore <= abs($totalLiveScore - $betTotalLiveScore)) {
                $this->logger->debug('clientScore_1 : ' . $live_score_1);
                $this->logger->debug('clientScore_2 : ' . $live_score_2);
                $this->logger->debug('serverScore_1 : ' . $livescore['Scoreboard']['Results'][0]['Value']);
                $this->logger->debug('serverScore_2 : ' . $livescore['Scoreboard']['Results'][1]['Value']);

                $response['messages'] = '스코어가 맞지 않아서 배팅에 실패했습니다.';
                return $this->fail($response);
            }

            //  주자 상태가 바뀌었어도 체크한다.

            if (BASEBALL == $result[0]->fixture_sport_id) {
                $redis_livescore = json_decode($redis_result['livescore'],true);
                $redis_LivescoreExtraData = $redis_livescore['LivescoreExtraData'];

                $livescore = json_decode($result[0]->livescore, true);
                $LivescoreExtraData = $livescore['LivescoreExtraData'];
                
                //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** checkRealTimeGameLiveScore redis_LivescoreExtraData: ==> ".json_encode($redis_LivescoreExtraData));
                //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** checkRealTimeGameLiveScore LivescoreExtraData: ==> ".json_encode($LivescoreExtraData));

                $redis_turn = 1;
                $turn = 1;
                foreach ($redis_LivescoreExtraData as $value) {
                    if ('Turn' != $value['Name'])
                        continue;

                    $redis_turn = $value['Value'];
                    break;
                }

                foreach ($LivescoreExtraData as $value) {
                    if ('Turn' != $value['Name'])
                        continue;

                    $turn = $value['Value'];
                    break;
                }

                if ($redis_turn != $turn) {
                    foreach ($LivescoreExtraData as $value) {
                        if ('Bases' != $value['Name'])
                            continue;

                        $bases = $value['Value'];
                        $arr_bases = explode('/', $bases);

                        foreach ($arr_bases as $base) {
                            if (0 == $base)
                                continue;
                        }

                        $response['messages'] = '배팅중 주자가 진루하여 배팅이 취소됩니다.';
                        return $this->fail($response);
                        //break;
                    }
                } else {
                    $arr_redis_bases = [];
                    $arr_bases = [];

                    foreach ($redis_LivescoreExtraData as $value) {
                        if ('Bases' != $value['Name'])
                            continue;

                        $bases = $value['Value'];
                        $arr_redis_bases = explode('/', $bases);

                        break;
                    }

                    foreach ($LivescoreExtraData as $value) {
                        if ('Bases' != $value['Name'])
                            continue;

                        $bases = $value['Value'];
                        $arr_bases = explode('/', $bases);

                        break;
                    }
                    
                    if(0 == count($arr_redis_bases) && 0 < count($arr_bases)){
                        $sumbase = $arr_bases[0] + $arr_bases[1] + $arr_bases[2];
                        if(0 < $sumbase){
                            $response['messages'] = '배팅중 주자가 진루하여 배팅이 취소됩니다.';
                            return $this->fail($response);
                        }
                    } else if(0 < count($arr_redis_bases) && 0 < count($arr_bases)){
                        if($arr_redis_bases[0] !== $arr_bases[0] 
                                || $arr_redis_bases[1] !== $arr_bases[1] 
                                || $arr_redis_bases[2] !== $arr_bases[2] ){

                            $response['messages'] = '배팅중 주자가 진루하여 배팅이 취소됩니다.';
                            return $this->fail($response);
                        }
                    }
                    
                }
            }
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
            //'live_list_6046' => $liveList_6046
            ]
        ];

        return $this->respond($response, 200);
    }

}
