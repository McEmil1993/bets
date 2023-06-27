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

//use CodeIgniter\HTTP\RequestInterface;

class SportsController extends BaseController {

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
        }
    }

    //// PAGE
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

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $location_id = true === isset($_GET['location_id']) && false === empty($_GET['location_id']) ? $_GET['location_id'] : 0;
        $league_id = true === isset($_GET['league_id']) && false === empty($_GET['league_id']) ? $_GET['league_id'] : 0;
        $sports_id = true === isset($_GET['sports_id']) && false === empty($_GET['sports_id']) ? $_GET['sports_id'] : 0;

        if (!is_int((int) $page) || !is_int((int) $location_id) || !is_int((int) $league_id) || !is_int((int) $sports_id)) {
            $url = base_url("/web/note");
            echo "<script>
            alert('인자값 오류입니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $league_name = true === isset($_GET['league_name']) && false === empty($_GET['league_name']) ? $_GET['league_name'] : '';

        $tgcModel = new TGameConfigModel();
        $config = $tgcModel->getMemberMaxBetMoney(1, $member->getLevel());
        $arr_config = $this->gmPt->getConfigData($tgcModel);

        if ('Y' == $arr_config['service_sports'] && 9 != $member->getLevel()) {
            echo "<script>
            alert('스포츠 점검중입니다.');
            window.history.back();
            </script>";
            return;
        }

        // 예상당첨금 상한가
        $str_sql_limit = "SELECT set_type_val FROM t_game_config WHERE u_level = ? and set_type = 'pre_limit_money'";
        $limit_config = $tgcModel->db->query($str_sql_limit, [$member->getLevel()])->getResultArray();

        $startTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " minutes"));
        //$endTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " days"));
        $endTime = date("Y-m-d H:i:s", strtotime("+" . 16 . " hours"));

        # 리그별 게임 리스트
        $leaguesGameList = [];
        # 나라별 게임 리스트
        $locationGameList = [];
        // 나라별 경기수
        $locationFixtureCount = [];
        // 넘어온 날짜로 구함.
        $current_day = date("d", strtotime($startTime));

        // 날짜가 넘어왔으면 해당날짜로 검색
        $bfDate = date("d", strtotime('-1 day'));
        $start_page = ($page - 1) * SPORRS_BLOCK_COUNT;

        # 배팅 리스트
        $lSportsBetModel = new LSportsBetModel();

        $getAvgSportsAllCountQueryString_start = time();
        $sql = $this->getAvgSportsAllCountQueryString($startTime, $endTime);

        $sportsTotal = $lSportsBetModel->db->query($sql, [$startTime, $endTime])->getResult();
        $t_getAvgSportsAllCountQueryString_start = time() - $getAvgSportsAllCountQueryString_start;

        //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start query : " . json_encode([$startTime, $endTime]));

        if (1 < $t_getAvgSportsAllCountQueryString_start) {
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start : " . $t_getAvgSportsAllCountQueryString_start);
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start query : " . $sql);
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start query param : " . json_encode([$startTime, $endTime]));
        }

        $array_fix = [];
        $array_fix_all = [];
        $fixtureCheck_1 = [];
        $fixtureCheck = [];
        $sportsList = [];
        //$arrLeagueList = [];
        $arrLocationList = [];
        $locationList = [];
        $leaguesList = [];

        //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** league_name ==> ".$league_name);    
        //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** location_id ==> ".$location_id.' league_id ==> '.$league_id.' sports_id==> '.$sports_id);    
        foreach ($sportsTotal as $value) {
            // 종목 > 지역, 종목 >지역 > 리그별 경기숫자
            if (!in_array($value->fixture_id, $fixtureCheck_1)) {
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id] = isset($locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id]) ? $locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id] : 0;
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id] += 1;
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name] = isset($locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name]) ? $locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name] : 0;
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name] += 1;
                array_push($fixtureCheck_1, $value->fixture_id);
            }
            if (!isset($sportsList[$value->sport_display_order])) {
                $sportsList[$value->sport_display_order][$value->fixture_sport_id] = array('id' => $value->fixture_sport_id, 'name' => $value->sport_name, 'count' => 1);
            } else {
                $sportsList = !empty($sportsList) ? $sportsList : [];
                if (!in_array($value->fixture_id, $fixtureCheck)) {
                    $sportsList[$value->sport_display_order][$value->fixture_sport_id]['count'] = $sportsList[$value->sport_display_order][$value->fixture_sport_id]['count'] + 1;
                    array_push($fixtureCheck, $value->fixture_id);
                }
            }

            // 종목 > 지역 별 정리
            $locationGameList[$value->fixture_sport_id] = isset($locationGameList[$value->fixture_sport_id]) ? $locationGameList[$value->fixture_sport_id] : [];
            $locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id] = isset($locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id]) ? $locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id] : [];
            array_push($locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id], (array) $value);

            // 종목 > 지역 > 리그 별 정리 
            $locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name] = isset($locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name]) ? $locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name] : [];
            array_push($locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name], (array) $value);

            // 상단 지역 리스트
            if (!isset($locationList[$value->fixture_location_id])) {
                // 선택한 종목이 있으면 해당종목만 내려준다.
                if (isset($_GET['sports_id']) && $_GET['sports_id'] != '') {
                    if ($_GET['sports_id'] == $value->fixture_sport_id) {
                        $locationList[$value->fixture_location_id] = array('id' => $value->fixture_location_id, 'display_name' => $value->fixture_location_name);
                        $arrLocationList[] = $value->fixture_location_id;
                    }
                } else {
                    $locationList[$value->fixture_location_id] = array('id' => $value->fixture_location_id, 'display_name' => $value->fixture_location_name);
                    $arrLocationList[] = $value->fixture_location_id;
                }
            }

            // 상단 리그 리스트
            //if (!isset($leaguesList[$value->fixture_league_id])) {
            if (!isset($leaguesList[$value->fixture_league_name])) {
                // 선택한 종목이 있으면 해당종목을 체크한다.
                if (isset($_GET['sports_id']) && $_GET['sports_id'] != '') {
                    if ($_GET['sports_id'] == $value->fixture_sport_id) {
                        // 선택한 지역이 있으면 해당지역만 내려준다.
                        if (isset($_GET['location_id']) && $_GET['location_id'] != '') {
                            if ($_GET['location_id'] == $value->fixture_location_id) {
                                $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                    'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                                //$arrLeagueList[] = $value->fixture_league_id;
                            }
                        } else {
                            $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                            //$arrLeagueList[] = $value->fixture_league_id;
                        }
                    }
                } else {
                    // 선택한 지역이 있으면 해당지역만 내려준다.
                    if (isset($_GET['location_id']) && $_GET['location_id'] != '') {
                        if ($_GET['location_id'] == $value->fixture_location_id) {
                            $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                            //$arrLeagueList[] = $value->fixture_league_id;
                        }
                    } else {
                        $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                            'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                        //$arrLeagueList[] = $value->fixture_league_id;
                    }
                }
            }
            
            CodeUtil::getSelectFixtureData($sports_id, $location_id, $league_id, $league_name, $value,$array_fix_all);
      
        } // end foreach ($sportsTotal as $value)
        // $this->logger->error("!!!!!!!!!!!!!!!!!! ***************** sports array_fix_all :" . json_encode($array_fix_all));

        $array_fix = array_slice($array_fix_all, $start_page, SPORRS_BLOCK_COUNT);
        $totalCnt = count($array_fix_all);
        $member_idx = session()->get('member_idx');

        if (0 < count($array_fix)) {
            $getAvgSportsQueryString = time();
            list($sql, $bind_parame) = $this->getAvgMainSportsQueryString($member_idx, $array_fix,$startTime, $endTime, $member->getLevel());
            $gameList = $lSportsBetModel->db->query($sql, $bind_parame)->getResult();

            $t_getAvgSportsQueryString = time() - $getAvgSportsQueryString;
            if (1 < $t_getAvgSportsQueryString) {
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsQueryString :" . $t_getAvgSportsQueryString);
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsQueryString query :" . $lSportsBetModel->getLastQuery());
            }

            $gameList = BetDataUtil::mergeMainAvgBetData($gameList, $this->logger);

            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports mergeMainAvgBetData gameList :" . json_encode($gameList));
        }
        ksort($sportsList);

        $tmList = [];
        foreach ($sportsList as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $tmList[$value2['id']] = $value2;
            }
        }
        unset($sportsList);
        $sportsList = $tmList;
        unset($tmList);

        $viewGameList = [];
        $i = 0;
        if (true == isset($gameList) && 0 < count($gameList)) {
            foreach ($gameList as $game) {
                if (empty($game['fixture_id'])) {
                    continue;
                }
                
                $fixture_id = $game['fixture_id'];
                $fixture_start_date = $game['fixture_start_date'];
                $viewGameList[$fixture_start_date][$fixture_id][] = $game;
                ++$i;
            }
        }
        ksort($viewGameList);

        if (true == isset($gameList) && count($gameList) == 0) {
            $locationList = [];
            $leaguesList = [];
            if ($_GET['sports_id'] > 0)
                $sportsList[$_GET['sports_id']]['count'] = 0;
        }

      
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sports leagues :" . json_encode($leaguesList));
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sports arrLeagueList :" . json_encode($arrLeagueList));
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sports array_fix :" . json_encode($array_fix));

        
        $t_time = time() - $start;
        if (2 < $t_time) {
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports Time : $t_time");
        }
        
        //if ('Y' == $arr_config['service_sports']) {
        if ('Y' == $arr_config['service_sports'] && 9 != $member->getLevel()) {
            $sql = "SELECT * FROM inspection WHERE idx = 1";
            $inspection = $memberModel->db->query($sql)->getResultArray()[0];
        
            return view("$viewRoot/inspection", [
                'system_mes' => $inspection
            ]);
        }else{
        
        return view("$viewRoot/sports", [
            'sports' => $sportsList, // 상단 좌측에 사용
            'leagues' => $leaguesList, // 상단 전체리그 항목에 사용(game02)
            'locationList' => $locationList, // 상단 전체지역 항목에 사용(game02)
            'gameList' => $viewGameList,
            'locationGameList' => $locationGameList, // 좌측에 사용
            //'realTimeTotalCnt' => $realTimeTotalCnt,
            'maxBetMoney' => $config[0]['set_type_val'],
            'limitBetMoney' => $limit_config[0]['set_type_val'],
            'arr_bonus' => $arr_config,
            'locationFixtureCount' => $locationFixtureCount, // 경기수
            'arrLocationList' => $arrLocationList,
            'totalCnt' => $totalCnt,
            'page' => $page,
            'sports_id' => $_GET['sports_id'] ?? 0,
            'league_name' => $league_name,
            'is_betting_slip' => $member->getIsBettingSlip(),
            'serverName' => config(App::class)->ServerName
            //'myItemList' => $resultMyItemlist
        ]);
    }
    }
    
    //// ajax
    public function ajax_index() {
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

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $location_id = true === isset($_POST['location_id']) && false === empty($_POST['location_id']) ? $_POST['location_id'] : 0;
        $league_id = true === isset($_POST['league_id']) && false === empty($_POST['league_id']) ? $_POST['league_id'] : 0;
        $sports_id = true === isset($_POST['sports_id']) && false === empty($_POST['sports_id']) ? $_POST['sports_id'] : 0;
        $league_name = true === isset($_POST['league_name']) && false === empty($_POST['league_name']) ? $_POST['league_name'] : '';
        
        if (!is_int((int) $page) || !is_int((int) $location_id) || !is_int((int) $league_id) || !is_int((int) $sports_id)) {
            $url = base_url("/web/note");
            echo "<script>
            alert('인자값 오류입니다.');
            window.location.href='$url';
            </script>";
            return;
        }
        
        $tgcModel = new TGameConfigModel();
        $config = $tgcModel->getMemberMaxBetMoney(1, $member->getLevel());
        $arr_config = $this->gmPt->getConfigData($tgcModel);

        if ('Y' == $arr_config['service_sports'] && 9 != $member->getLevel()) {
            echo "<script>
            alert('스포츠 점검중입니다.');
            window.history.back();
            </script>";
            return;
        }

        // 예상당첨금 상한가
        $str_sql_limit = "SELECT set_type_val FROM t_game_config WHERE u_level = ? and set_type = 'pre_limit_money'";
        $limit_config = $tgcModel->db->query($str_sql_limit, [$member->getLevel()])->getResultArray();

        $startTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " minutes"));
        $endTime = date("Y-m-d H:i:s", strtotime("+" . 1 . " days"));

        # 리그별 게임 리스트
        $leaguesGameList = [];
        # 나라별 게임 리스트
        $locationGameList = [];
        // 나라별 경기수
        $locationFixtureCount = [];
        // 넘어온 날짜로 구함.
        $current_day = date("d", strtotime($startTime));

        // 날짜가 넘어왔으면 해당날짜로 검색
        $bfDate = date("d", strtotime('-1 day'));
        $start_page = ($page - 1) * SPORRS_BLOCK_COUNT;

        # 배팅 리스트
        $lSportsBetModel = new LSportsBetModel();

        $getAvgSportsAllCountQueryString_start = time();
        $sql = $this->getAvgSportsAllCountQueryString();

        $sportsTotal = $lSportsBetModel->db->query($sql, [$startTime, $endTime])->getResult();
        $t_getAvgSportsAllCountQueryString_start = time() - $getAvgSportsAllCountQueryString_start;

        //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start query : " . json_encode([$startTime, $endTime]));

        if (1 < $t_getAvgSportsAllCountQueryString_start) {
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start : " . $t_getAvgSportsAllCountQueryString_start);
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsAllCountQueryString_start query : " . $sql);
        }

        $array_fix = [];
        $array_fix_all = [];
        $fixtureCheck_1 = [];
        $fixtureCheck = [];
        $sportsList = [];
        //$arrLeagueList = [];
        $arrLocationList = [];
        $locationList = [];
        $leaguesList = [];

        //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** league_name ==> ".$league_name);    
        //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** location_id ==> ".$location_id.' league_id ==> '.$league_id.' sports_id==> '.$sports_id);    
        foreach ($sportsTotal as $value) {
            // 종목 > 지역, 종목 >지역 > 리그별 경기숫자
            if (!in_array($value->fixture_id, $fixtureCheck_1)) {
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id] = isset($locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id]) ? $locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id] : 0;
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_location_id] += 1;
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name] = isset($locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name]) ? $locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name] : 0;
                $locationFixtureCount[$value->fixture_sport_id][$value->fixture_league_name] += 1;
                array_push($fixtureCheck_1, $value->fixture_id);
            }
            if (!isset($sportsList[$value->sport_display_order])) {
                $sportsList[$value->sport_display_order][$value->fixture_sport_id] = array('id' => $value->fixture_sport_id, 'name' => $value->sport_name, 'count' => 1);
            } else {
                $sportsList = !empty($sportsList) ? $sportsList : [];
                if (!in_array($value->fixture_id, $fixtureCheck)) {
                    $sportsList[$value->sport_display_order][$value->fixture_sport_id]['count'] = $sportsList[$value->sport_display_order][$value->fixture_sport_id]['count'] + 1;
                    array_push($fixtureCheck, $value->fixture_id);
                }
            }

            // 종목 > 지역 별 정리
            $locationGameList[$value->fixture_sport_id] = isset($locationGameList[$value->fixture_sport_id]) ? $locationGameList[$value->fixture_sport_id] : [];
            $locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id] = isset($locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id]) ? $locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id] : [];
            array_push($locationGameList[$value->fixture_sport_id]['location_all'][$value->fixture_location_id], (array) $value);

            // 종목 > 지역 > 리그 별 정리 
            $locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name] = isset($locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name]) ? $locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name] : [];
            array_push($locationGameList[$value->fixture_sport_id]['location_' . $value->fixture_location_id][$value->fixture_league_name], (array) $value);

            // 상단 지역 리스트
            if (!isset($locationList[$value->fixture_location_id])) {
                // 선택한 종목이 있으면 해당종목만 내려준다.
                if (isset($sports_id) && $sports_id != '') {
                    if ($sports_id == $value->fixture_sport_id) {
                        $locationList[$value->fixture_location_id] = array('id' => $value->fixture_location_id, 'display_name' => $value->fixture_location_name);
                        $arrLocationList[] = $value->fixture_location_id;
                    }
                } else {
                    $locationList[$value->fixture_location_id] = array('id' => $value->fixture_location_id, 'display_name' => $value->fixture_location_name);
                    $arrLocationList[] = $value->fixture_location_id;
                }
            }

            // 상단 리그 리스트
            //if (!isset($leaguesList[$value->fixture_league_id])) {
            if (!isset($leaguesList[$value->fixture_league_name])) {
                // 선택한 종목이 있으면 해당종목을 체크한다.
                if (isset($sports_id) && $sports_id != '') {
                    if ($sports_id == $value->fixture_sport_id) {
                        // 선택한 지역이 있으면 해당지역만 내려준다.
                        if (isset($location_id) && $location_id != '') {
                            if ($location_id == $value->fixture_location_id) {
                                $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                    'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                                //$arrLeagueList[] = $value->fixture_league_id;
                            }
                        } else {
                            $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                            //$arrLeagueList[] = $value->fixture_league_id;
                        }
                    }
                } else {
                    // 선택한 지역이 있으면 해당지역만 내려준다.
                    if (isset($location_id) && $location_id != '') {
                        if ($location_id == $value->fixture_location_id) {
                            $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                                'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                            //$arrLeagueList[] = $value->fixture_league_id;
                        }
                    } else {
                        $leaguesList[$value->fixture_league_name] = array('id' => $value->fixture_league_id, 'display_name' => $value->fixture_league_name,
                            'fixture_league_image_path' => $value->fixture_league_image_path, 'fixture_location_id' => $value->fixture_location_id);
                        //$arrLeagueList[] = $value->fixture_league_id;
                    }
                }
            }

            if (0 < mb_strlen($league_name, "UTF-8")) {

                //$this->logger->error("!!!!!!!!!!!!!!!!!! ***************** league_name ==> ".$league_name);    
                if (0 == $location_id && (false !== strpos($value->fixture_location_name, $league_name)) && $value->fixture_sport_id == $sports_id) {//011
                    array_push($array_fix_all, $value->fixture_id);
                } else if ($value->fixture_location_id == $location_id && (false !== strpos($value->fixture_location_name, $league_name)) && $value->fixture_sport_id == $sports_id) {//111
                    array_push($array_fix_all, $value->fixture_id);
                } else if ($value->fixture_location_id == $location_id && (false !== strpos($value->fixture_location_name, $league_name)) && 0 == $sports_id) {//110
                    array_push($array_fix_all, $value->fixture_id);
                } else if (0 == $location_id && (false !== strpos($value->fixture_location_name, $league_name)) && 0 == $sports_id) {//010
                    array_push($array_fix_all, $value->fixture_id);
                } else if (0 == $location_id && (false !== strpos($value->p1_team_name, $league_name) || false !== strpos($value->p2_team_name, $league_name) || false !== strpos($value->p1_display_name, $league_name) || false !== strpos($value->p2_display_name, $league_name)) && $value->fixture_sport_id == $sports_id) {
                    array_push($array_fix_all, $value->fixture_id);
                } else if ($value->fixture_location_id == $location_id && (false !== strpos($value->p1_team_name, $league_name) || false !== strpos($value->p2_team_name, $league_name) || false !== strpos($value->p1_display_name, $league_name) || false !== strpos($value->p2_display_name, $league_name)) && $value->fixture_sport_id == $sports_id) {//111
                    array_push($array_fix_all, $value->fixture_id);
                } else if ($value->fixture_location_id == $location_id && (false !== strpos($value->p1_team_name, $league_name) || false !== strpos($value->p2_team_name, $league_name) || false !== strpos($value->p1_display_name, $league_name) || false !== strpos($value->p2_display_name, $league_name)) && 0 == $sports_id) {//110
                    array_push($array_fix_all, $value->fixture_id);
                } else if (0 == $location_id && (false !== strpos($value->p1_team_name, $league_name) || false !== strpos($value->p2_team_name, $league_name) || false !== strpos($value->p1_display_name, $league_name) || false !== strpos($value->p2_display_name, $league_name) ) && 0 == $sports_id) {//010
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
        }
        // $this->logger->error("!!!!!!!!!!!!!!!!!! ***************** sports array_fix_all :" . json_encode($array_fix_all));

        $array_fix = array_slice($array_fix_all, $start_page, SPORRS_BLOCK_COUNT);
        $totalCnt = count($array_fix_all);
        $member_idx = session()->get('member_idx');

        if (0 < count($array_fix)) {
            $getAvgSportsQueryString = time();
            list($sql, $bind_parame) = $this->getAvgMainSportsQueryString($member_idx, $array_fix, $member->getLevel());
            $gameList = $lSportsBetModel->db->query($sql, $bind_parame)->getResult();

            $t_getAvgSportsQueryString = time() - $getAvgSportsQueryString;
            if (1 < $t_getAvgSportsQueryString) {
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsQueryString :" . $t_getAvgSportsQueryString);
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports t_getAvgSportsQueryString query :" . $lSportsBetModel->getLastQuery());
            }

            $gameList = BetDataUtil::mergeMainAvgBetData($gameList, $this->logger);

            //$this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports mergeMainAvgBetData gameList :" . json_encode($gameList));
        }
        ksort($sportsList);

        $tmList = [];
        foreach ($sportsList as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $tmList[$value2['id']] = $value2;
            }
        }
        unset($sportsList);
        $sportsList = $tmList;
        unset($tmList);

        $viewGameList = [];
        $i = 0;
        if (true == isset($gameList) && 0 < count($gameList)) {
            foreach ($gameList as $game) {
                $fixture_id = $game['fixture_id'];
                $fixture_start_date = $game['fixture_start_date'];
                $viewGameList[$fixture_start_date][$fixture_id][] = $game;
                ++$i;
            }
        }
        ksort($viewGameList);

        if (true == isset($gameList) && count($gameList) == 0) {
            $locationList = [];
            $leaguesList = [];
            if ($sports_id > 0)
                $sportsList[$sports_id]['count'] = 0;
        }
   
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sports leagues :" . json_encode($leaguesList));
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sports arrLeagueList :" . json_encode($arrLeagueList));
        //$this->logger->info("!!!!!!!!!!!!!!!!!! ***************** sports array_fix :" . json_encode($array_fix));


        $t_time = time() - $start;
        if (2 < $t_time) {
            $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** sports Time : $t_time");
        }
        
         $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'sports' => $sportsList, // 상단 좌측에 사용
            'leagues' => $leaguesList, // 상단 전체리그 항목에 사용(game02)
            'locationList' => $locationList, // 상단 전체지역 항목에 사용(game02)
            'gameList' => $viewGameList,
            'locationGameList' => $locationGameList, // 좌측에 사용
            //'realTimeTotalCnt' => $realTimeTotalCnt,
            'maxBetMoney' => $config[0]['set_type_val'],
            'limitBetMoney' => $limit_config[0]['set_type_val'],
            'arr_bonus' => $arr_config,
            'locationFixtureCount' => $locationFixtureCount, // 경기수
            'arrLocationList' => $arrLocationList,
            'totalCnt' => $totalCnt,
            'page' => $page,
            'sports_id' => $sports_id,
            'league_name' => $league_name,
            'is_betting_slip' => $member->getIsBettingSlip(),
            //'myItemList' => $resultMyItemlist

        ];

        return $this->respond($response, 200);
    }
    
    public function ajax_add_fixtures() {

        //$this->logger->info('ajax_add_fixtures start: ');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (false == session()->has('member_idx')) {
            return $this->fail('로그인 후 이용해주세요.');
        }
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            return $this->fail('로그인 후 이용해주세요.');
        }

        if (0 < session()->get('tm_unread_cnt')) {
            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }


        $fixture_id = $_POST['fixture_id'];

        if (false == isset($fixture_id) || !is_int((int) $fixture_id)) {
            return $this->fail('경기 아이디가 잘못되어있습니다.');
        }

        $startTime = date("Y-m-d H:i:s");
        $endTime = date("Y-m-d 23:59:59", strtotime("+" . 1 . " days"));
        $array_fix = array($fixture_id);

        # 배팅 리스트
        $lSportsBetModel = new LSportsBetModel();
        list($sql, $bind_parame) = $this->getAvgSportsQueryString(session()->get('member_idx'),$array_fix,$startTime, $endTime, $member->getLevel());

        $gameList = $lSportsBetModel->db->query($sql, $bind_parame)->getResult();

        //$this->logger->error('ajax_add_fixtures : ' . $lSportsBetModel->getLastQuery());

        $gameList = BetDataUtil::mergeAvgBetData($gameList, $this->logger);

        //$this->logger->debug('ajax_add_fixtures : ' . json_encode($gameList));

        $viewGameList = [];
        $gameSportsId = 0;  // 게임종목
        if (true == isset($gameList) && 0 < count($gameList)) {
            foreach ($gameList as $game) {
                $market_id = $game['markets_id'];
                $fixture_id = $game['fixture_id'];
                $display_order = $game['display_order'];

                if ($gameSportsId == 0) {
                    $gameSportsId = $game['fixture_sport_id'];
                }

                if ("PC" != $chkMobile) {
                    if (1 == $game['is_main_menu']) { //  0 : 더보기 , 1 : 메인 
                        $main_display_order = $game['main_display_order'];
                        $viewGameList[$fixture_id][0][$main_display_order][$market_id][] = $game;
                        ksort($viewGameList[$fixture_id][0]);
                        if (BASEBALL == $game['fixture_sport_id'] || VOLLEYBALL == $game['fixture_sport_id'] || ESPORTS == $game['fixture_sport_id'] || UFC == $game['fixture_sport_id'])
                            continue;
                    }

                    $menu = $game['menu'];
                    $viewGameList[$fixture_id][$menu][$display_order][$market_id][] = $game;
                    ksort($viewGameList[$fixture_id][$menu]);
                } else {
                    $viewGameList[$fixture_id][0][$display_order][$market_id][] = $game;
                    ksort($viewGameList[$fixture_id][0]);
                }
            }
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'gameList' => $viewGameList,
                'gameSportsId' => $gameSportsId
            ]
        ];
        return $this->respond($response, 200);
    }

    // 총개수 가져오기 
    private function getAvgSportsAllCountQueryString($startTime, $endTime) {
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
                        locations.name as fixture_location_name,
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
                    AND fix.admin_bet_status = 'ON' 
                    AND ( IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 1 
                    OR IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 2 
                    AND NOW() BETWEEN DATE_ADD(IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) , INTERVAL -10 MINUTE) 
                    AND DATE_ADD(IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) , INTERVAL -1 MINUTE)) 
                    AND (CASE WHEN fix.fixture_sport_id = 6046  THEN  bet.markets_id = 1 
                              WHEN fix.fixture_sport_id = 35232  THEN  bet.markets_id = 1 
                              WHEN fix.fixture_sport_id = 48242  THEN  bet.markets_id = 226 
                              WHEN fix.fixture_sport_id = 154914  THEN  bet.markets_id = 226 
                              WHEN fix.fixture_sport_id = 154830  THEN  bet.markets_id = 52 
                              WHEN fix.fixture_sport_id = 154919  THEN  bet.markets_id = 52 
                              WHEN fix.fixture_sport_id = 687890  THEN  bet.markets_id = 52 
                              WHEN fix.fixture_sport_id = 54094  THEN  bet.markets_id = 52
                    ELSE 1 = 1 END) 
                    AND IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) = 1 
                    AND bet.bet_type = 1
                    AND bet.admin_bet_status = 'ON' ";
        $sql = $sql . " AND sports.is_use = 1 and sports.bet_type = 1";
        $sql = $sql . " AND league.is_use = 1 and league.bet_type = 1";
        $sql = $sql . " AND markets.bet_group = 1 AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id";
        $sql .= " GROUP BY fix.fixture_id,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) ORDER BY fixture_start_date asc ,league.display_name ,fix.fixture_sport_id ";
        //$this->logger->error($sql);
        return $sql;
    }
    
    private function getAvgSportsQueryString($member_idx, $array_fix, $startTime, $endTime, $level) {
        $bind_parame = array();
        $sql = "SELECT 
                             bet.bet_id,
                             bet.fixture_id,
                             bet.bet_base_line,
                             bet.bet_line,
                             bet.bet_name,
                             IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                             IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                             bet.last_update,
                             bet.update_dt,
                             markets.id as markets_id,
                             markets.name,
                             markets.display_name as markets_display_name,
                             markets.menu,
                             markets.limit_bet_price,
                             markets.max_bet_price,
                             markets.is_main_menu,
                             markets.main_display_order,
                             markets.display_order,
                             fix.fixture_sport_id, sports.name as fixture_sport_name,
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
                             div_policy.amount,
                             div_policy.amount - (select sum_bet_money from fixtures_bet_sum as bet_sum 
                                where bet_sum.member_idx = $member_idx and bet_sum.fixture_id = fix.fixture_id and bet_sum.bet_type = 1) as leagues_m_bet_money
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
                    WHERE  ( IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 1 
                    OR IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 2 
                    AND NOW() BETWEEN DATE_ADD(IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) , INTERVAL -10 MINUTE) 
                    AND DATE_ADD(IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) , INTERVAL -1 MINUTE)) 
                    AND IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) = 1 
                    AND bet.admin_bet_status = 'ON'
                    AND bet.bet_type = 1
                    AND fix.fixture_id IN (" . implode(',', $array_fix) . ") AND fix.bet_type = 1 ";


        $sql = $sql . " AND sports.is_use = 1 and sports.bet_type = 1";
        $sql = $sql . " AND league.is_use = 1 and league.bet_type = 1";
        $sql = $sql . " AND markets.bet_group = 1 AND markets.is_delete = 0  AND markets.sport_id = fix.fixture_sport_id AND div_policy.type = 1 AND div_policy.level = $level";
        $sql = $sql . " GROUP BY bet.bet_id, bet.fixture_id,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) ORDER BY fixture_start_date asc ,league.display_name,bet.bet_name,bet.bet_base_line *1";


        return [$sql, $bind_parame];
    }
       
    private function getAvgMainSportsQueryString($member_idx, $array_fix, $startTime, $endTime, $level) {
        $bind_parame = array();
        $sql = "SELECT 
                             bet.bet_id,
                             bet.fixture_id,
                             bet.bet_base_line,
                             bet.bet_line,
                             bet.bet_name,
                             bet.providers_id,
                             IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                             IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                             bet.last_update,
                             bet.update_dt,
                             markets.id as markets_id,
                             markets.name,
                             markets.limit_bet_price,
                             markets.max_bet_price,
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
                                where bet_sum.member_idx = $member_idx and bet_sum.fixture_id = fix.fixture_id and bet_sum.bet_type = 1) as leagues_m_bet_money
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
                    WHERE ( IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 1 
                    OR IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 2 
                    AND NOW() BETWEEN DATE_ADD(IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) , INTERVAL -10 MINUTE) AND DATE_ADD(IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) , INTERVAL -1 MINUTE)) 
                    AND IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) = 1 
                    AND bet.admin_bet_status = 'ON'
                    AND bet.bet_type = 1
                    AND fix.fixture_id IN (" . implode(',', $array_fix) . ") AND fix.bet_type = 1 ";

        //AND fix.fixture_id IN (" . implode(',', $array_fix) . ")";
        $sql = $sql . " AND sports.is_use = 1 and sports.bet_type = 1";
        $sql = $sql . " AND league.is_use = 1 and league.bet_type = 1";
        $sql = $sql . " AND markets.bet_group = 1 AND markets.is_delete = 0  AND fix.fixture_sport_id = markets.sport_id AND div_policy.type = 1 AND div_policy.level = $level";
        $sql = $sql . " GROUP BY bet.bet_id, bet.fixture_id,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) ORDER BY fixture_start_date asc ,fix.fixture_sport_id,league.display_name,bet.bet_name,bet.bet_base_line *1";


        return [$sql, $bind_parame];
    }
    // 실시간 경기 수 가져오기 
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
                             markets.not_display_period,
                             markets.not_display_time,
                             markets.not_display_score,
                             markets.not_display_score_team_type,
                             fix.fixture_sport_id, sports.name as fixture_sport_name,
                             fix.fixture_location_id, location.name as fixture_location_name,
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
    // 마감임박 경기목록
    public function getMainSportTimeList() {
        $sportsId = isset($_POST['sportsId']) ? $_POST['sportsId'] : 0;

        if (!is_int((int) $sportsId)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** getMainSportTimeList: ==> ");
            die();
        }
        $lsSportsModel = new LSportsSportsModel();
        $sportsIndexList = $lsSportsModel->isUseIdList(2);

        $current_day = date("d");
        $lSportsBetModel = new LSportsBetModel();
        $sql = "SELECT     
                             bet.fixture_id,
                             bet.bet_base_line,
                             bet.bet_line,
                             bet.bet_name,
                             IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                             markets.id as markets_id,
                             markets.limit_bet_price,
                             markets.max_bet_price,
                             fix.fixture_sport_id, 
                             IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date, 
                             p1.team_name as p1_team_name,
                             p2.team_name as p2_team_name 
                    FROM         lsports_bet as bet
                    LEFT JOIN    lsports_fixtures as fix
                    ON           bet.fixture_id = fix.fixture_id
                    LEFT JOIN 	 lsports_participant as p1
                           ON	 fix.fixture_participants_1_id = p1.fp_id
                    LEFT JOIN 	 lsports_participant as p2
                           ON	 fix.fixture_participants_2_id = p2.fp_id
                    LEFT JOIN 	 lsports_leagues as league
                           ON	 fix.fixture_league_id = league.id 
                    LEFT JOIN    lsports_markets as markets
                           ON    bet.markets_id = markets.id
                    WHERE    
                    bet.bet_type = 1 
                    AND IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) = 1
                    AND bet.admin_bet_status = 'ON'
                    AND IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) between now() and date_add(now(), interval 6 hour) 
                    AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) = 1
                    AND fix.admin_bet_status = 'ON'
                    AND fix.bet_type = 1 
                    AND league.is_use = 1 AND league.bet_type = 1";

        if ($sportsId != 0) {
            $sql = $sql . " AND fix.fixture_sport_id = ? ";
        } else {
            $sql = $sql . " AND fix.fixture_sport_id IN (" . implode(',', $sportsIndexList) . ")";
        }

        $sql = $sql . " AND markets.bet_group = 1 AND markets.is_delete = 0 AND markets.sport_id = fix.fixture_sport_id 
                        GROUP BY bet.bet_id
                        ORDER BY fixture_start_date asc";

        $fixtureList = $lSportsBetModel->db->query($sql, [$sportsId])->getResult();
        $viewGameList = BetDataUtil::mergeMainDeadlineBetData($fixtureList, $this->logger);

        $mainTimeGameList = array();
        $array_in_main_market_id = null;
        foreach ($viewGameList as $fixture_key => $game) {
            $market_id = $game['markets_id'];
            $fixture_id = $game['fixture_id'];
            $fixture_sport_id = $game['fixture_sport_id'];
            if ($fixture_sport_id == SOCCER || $fixture_sport_id == ICEHOCKEY) {
                $array_in_main_market_id = array(1);
            } else if ($fixture_sport_id == BASKETBALL) {
                $array_in_main_market_id = array(226);
            } else if ($fixture_sport_id == VOLLEYBALL || ESPORTS == $fixture_sport_id || UFC == $fixture_sport_id) {
                $array_in_main_market_id = array(52);
            } else if ($fixture_sport_id == BASEBALL) {
                $array_in_main_market_id = array(226);
            } else {
                continue;
            }

            if (true == in_array($game['markets_id'], $array_in_main_market_id)) {
                if (!isset($mainTimeGameList[$fixture_id]))
                    $mainTimeGameList[$fixture_id][] = $game;
            }
        }

        $mainTimeGameList = array_splice($mainTimeGameList, 0, 4);

        //$this->logger->debug(json_encode($mainTimeGameList, JSON_UNESCAPED_UNICODE));

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'event' => $mainTimeGameList,
            ]
        ];
        return $this->respond($response, 200);
    }

}
