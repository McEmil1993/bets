<?php

namespace App\Controllers;

use App\Entities\MemberBet;
use App\Models\GameModel;
use App\Models\LSportsBetModel;
use App\Models\LSportsBookmakerModel;
use App\Models\LSportsFixturesModel;
use App\Models\LSportsLeaguesModel;
use App\Models\LSportsLocationsModel;
use App\Models\LSportsMarketsModel;
use App\Models\LSportsSportsModel;
use App\Models\MemberBetDetailModel;
use App\Models\MemberBetModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyChargeHistoryModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Util\PullOperations;
use CodeIgniter\API\ResponseTrait;
use mysql_xdevapi\Exception;
use App\Util\BetDataUtil;
use App\Util\CodeUtil;
use App\Util\BetCodeUtil;
use App\Util\DateTimeUtil;
use App\Models\TLogLsportsBetModel;
use App\Util\Calculate;

// 최초 데이터 셋팅하는 컨트롤러
class LSportsInitController extends BaseController {

    use ResponseTrait;

    protected $bet_code_util;
    protected $array_in_refund_rate_market_id = array(4, 6, 7, 9, 16, 17, 69, 70, 71, 98, 99, 390, 427, 472, 1537, 1538);
    protected $arrLeague = array(918, 3371, 5283, 5556, 5769, 14121, 14493, 14497, 14498, 14499, 14530, 14761, 15181, 15997, 16176, 16362, 16482, 16745, 16791, 20736, 22893, 23798, 24136, 25686, 27433, 27488, 29301, 32573, 33252, 35133); //33078
    protected $str_sports_ids;
    protected $str_leagues_ids;
    protected $str_locations_ids;
    protected $str_markets_ids;

    //// API
    public function __construct() {
        
    }

    public function initData() {

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $this->bet_code_util = new BetCodeUtil($this->logger);

        $lsSportsModel = new LSportsSportsModel();
        $sportList = $lsSportsModel->isUseIdList(1);
        $this->str_sports_ids = implode(',', $sportList);

        $lsLeaguesModel = new LSportsLeaguesModel();
        $leagueList = $lsLeaguesModel->isUseIdTypeList(1);
        $this->str_leagues_ids = implode(',', $leagueList);

        // 1분마다 돌릴
        $arr_fixtures = $this->getFixturesDailyData();
        $this->convertAverageBet($arr_fixtures);

        // 기본 설정 값 (최초 한번만 실행) // 점검시 실행 해서 업는 데이터는 새로 추가한다.
    }

    // 하나의 경기 데이터 확인용도
    public function getFixturesData() {
        $fixtureId = isset($_REQUEST['fixtureId']) ? $_REQUEST['fixtureId'] : null;

        $lSportsFixturesModel = new LSportsFixturesModel();
        $pullOperations = new PullOperations('ODD', $this->logger);

        $fromDate = strtotime('-300 minutes');
        $toDate = strtotime('+1 day');
        //$toDate = strtotime('+720 minutes');

        $fixturesResult = $pullOperations->getFixtures($fromDate, $toDate, null, null, null, null, 7078958);
        $this->logger->debug(json_encode($fixturesResult));

        if (!isset($fixturesResult) || null == $fixturesResult)
            return null;

        $eventResult = $pullOperations->getEvents($this->logger, $fromDate, $toDate, null, null, null, null, null, 7078958);
        $this->logger->debug(json_encode($eventResult));
    }

    public function testTotal() {
        
    }

    public function doTotalCalculate() {

        Calculate::doResultProcessing(1, $this->logger);
        Calculate::renew_doTotalCalculate(1, $this->logger);
        Calculate::doResultProcessing(2, $this->logger);
        Calculate::renew_doTotalCalculate(2, $this->logger);
    }

    public function initMember() {
        $this->memberChargeAndExchangeConfirm(); // 충전, 환전 신청 처리
        $this->battingResultProcessing();        // 배팅 결과 처리
    }

    public function getSports() {
        $pullOperations = new PullOperations('ODD', $this->logger);
        $sports = $pullOperations->getSports();

        $data = [];
        foreach ($sports as $item) {

            $insertData = [
                'id' => $item->Id,
                'name' => $item->Name,
                'lang' => 'ko'
            ];
            array_push($data, $insertData);
        }

        $model = new LSportsSportsModel();
        $model->insertBatch($data);
    }

    public function getLocations() {
        $pullOperations = new PullOperations('ODD', $this->logger);
        $locations = $pullOperations->getLocations();

        $data = [];
        foreach ($locations as $item) {
            $insertData = [
                'id' => $item->Id,
                'name' => $item->Name,
                'lang' => 'ko'
            ];
            array_push($data, $insertData);
        }

        $model = new LSportsLocationsModel();
        $model->insertBatch($data);
    }

    public function getLocationsByEn() {
        $pullOperations = new PullOperations('ODD', $this->logger);
        $locations = $pullOperations->getLocationsByEn();

        $data = [];
        foreach ($locations as $item) {
            $updateData = [
                'id' => $item->Id,
                'name_en' => $item->Name,
            ];
            array_push($data, $updateData);
        }

        $model = new LSportsLocationsModel();
        $model->updateBatch($data, 'id');
    }

    public function getLeagues() {

        try {
            $pullOperations = new PullOperations('ODD', $this->logger);

            $locations = $pullOperations->getLeagues();
            
            if (false == isset($locations) || null == $locations){
                $this->logger->info("getLeagues empty : ");
               return;
            }
            
            $count = 0;
            $arr = array();

            foreach ($locations->League as $object) {
                $arr[] = $object->{'@attributes'};
            }
           
            

            $this->logger->debug("getLeagues count : " . count($arr));
    
            $sql = array(); // 스포츠
            $sqlReal = array(); // 실시간
            if (count($arr) > 0) {
                foreach ($arr as $item) {
                    $name = addslashes($item->Name);
                    $insertSql = '('
                            . $item->Id . ', '
                            . 1 . ', "'
                            . $name . '", '
                            . $item->LocationId . ', '
                            . $item->SportId . ', "'
                            . $item->Season . '", "'
                            . 'ko")';
                    array_push($sql, $insertSql);
    
                    // 실시간
                    $insertSql = '('
                            . $item->Id . ', '
                            . 2 . ', "'
                            . $name . '", '
                            . $item->LocationId . ', '
                            . $item->SportId . ', "'
                            . $item->Season . '", "'
                            . 'ko")';
                    array_push($sqlReal, $insertSql);
                }
               
            }
    
            $lSportsLeaguesModel = new LSportsLeaguesModel();
    
            if (count($sql) > 0) {
                try {
                    //$lSportsLeaguesModel->db->transStart();
                    $lSportsLeaguesModel->db->query(
                            'INSERT INTO `lsports_leagues` ('
                            . 'id, '
                            . 'bet_type, '
                            . 'name, '
                            . 'location_id, '
                            . 'sport_id, '
                            . 'season, '
                            . 'lang) VALUES '
                            . implode(',', $sql)
                            . ' ON DUPLICATE KEY UPDATE '
                            . 'name = VALUES(name), '
                            . 'location_id = VALUES(location_id), '
                            . 'sport_id = VALUES(sport_id), '
                            . 'season = VALUES(season), '
                            . 'lang = VALUES(lang)'
                    );
                   
                    //$lSportsLeaguesModel->db->transComplete();
                } catch (\mysqli_sql_exception $e) {
                    $query_str = (string) $lSportsLeaguesModel->getLastQuery();
                    $this->logger->error("- getViewOrderedFixtures error query_string : " . $query_str);
                    //$lSportsLeaguesModel->db->transRollback();
                    return;
                }
            }
    
            if (count($sqlReal) > 0) {
                try {
                    //$lSportsLeaguesModel->db->transStart();
                    $lSportsLeaguesModel->db->query(
                            'INSERT INTO `lsports_leagues` ('
                            . 'id, '
                            . 'bet_type, '
                            . 'name, '
                            . 'location_id, '
                            . 'sport_id, '
                            . 'season, '
                            . 'lang) VALUES '
                            . implode(',', $sqlReal)
                            . ' ON DUPLICATE KEY UPDATE '
                            . 'name = VALUES(name), '
                            . 'location_id = VALUES(location_id), '
                            . 'sport_id = VALUES(sport_id), '
                            . 'season = VALUES(season), '
                            . 'lang = VALUES(lang)'
                    );
                   
                    //$lSportsLeaguesModel->db->transComplete();
                } catch (\mysqli_sql_exception $e) {
                    $query_str = (string) $lSportsLeaguesModel->getLastQuery();
                    $this->logger->error("- getViewOrderedFixtures error query_string : " . $query_str);
                    //$lSportsLeaguesModel->db->transRollback();
                    return;
                }
            }


        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
       
    }


    public function getLeagues_stm() {
        $pullOperations = new PullOperations('STM', $this->logger);
        $locations = $pullOperations->getLeagues_stm();
        if (false == isset($locations) || null == $locations){
             $this->logger->info("getLeagues_stm empty : ");
            //  echo "Leagues_stm Empty!";
            return;
        }
        // $count = 0;

        // echo json_encode($locations);

        foreach($locations as $key ){ $count = count($key); }
       
       
        $this->logger->info("getLeagues_stm count : " . $count);
        // echo " Leagues_stm getLeagues count!". $count;
        $sql = array(); // 스포츠
        $sqlReal = array(); // 실시간
        if ($count > 0) {
            foreach ($locations->Leagues as $item) {
                $name = addslashes($item->Name);
                $insertSql = '('
                        . $item->Id . ', '
                        . 1 . ', "'
                        . $name . '", '
                        . $item->LocationId . ', '
                        . $item->SportId . ', "'
                        . $item->Season . '", "'
                        . 'ko")';
                array_push($sql, $insertSql);

                // 실시간
                $insertSql = '('
                        . $item->Id . ', '
                        . 2 . ', "'
                        . $name . '", '
                        . $item->LocationId . ', '
                        . $item->SportId . ', "'
                        . $item->Season . '", "'
                        . 'ko")';
                array_push($sqlReal, $insertSql);
            }
        }

        $lSportsLeaguesModel = new LSportsLeaguesModel();

        if (count($sql) > 0) {
            try {
                //$lSportsLeaguesModel->db->transStart();
                $lSportsLeaguesModel->db->query(
                        'INSERT INTO `lsports_leagues` ('
                        . 'id, '
                        . 'bet_type, '
                        . 'name, '
                        . 'location_id, '
                        . 'sport_id, '
                        . 'season, '
                        . 'lang) VALUES '
                        . implode(',', $sql)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'name = VALUES(name), '
                        . 'location_id = VALUES(location_id), '
                        . 'sport_id = VALUES(sport_id), '
                        . 'season = VALUES(season), '
                        . 'lang = VALUES(lang)'
                );
                //$lSportsLeaguesModel->db->transComplete();
            } catch (\Exception $e) {
                $query_str = (string) $lSportsLeaguesModel->getLastQuery();
                $this->logger->error("- getViewOrderedFixtures error query_string : " . $query_str);
                //$lSportsLeaguesModel->db->transRollback();
                return;
            }
        }

        if (count($sqlReal) > 0) {
            try {
                //$lSportsLeaguesModel->db->transStart();
                $lSportsLeaguesModel->db->query(
                        'INSERT INTO `lsports_leagues` ('
                        . 'id, '
                        . 'bet_type, '
                        . 'name, '
                        . 'location_id, '
                        . 'sport_id, '
                        . 'season, '
                        . 'lang) VALUES '
                        . implode(',', $sqlReal)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'name = VALUES(name), '
                        . 'location_id = VALUES(location_id), '
                        . 'sport_id = VALUES(sport_id), '
                        . 'season = VALUES(season), '
                        . 'lang = VALUES(lang)'
                );
                //$lSportsLeaguesModel->db->transComplete();
            } catch (\Exception $e) {
                $query_str = (string) $lSportsLeaguesModel->getLastQuery();
                $this->logger->error("- getViewOrderedFixtures error query_string : " . $query_str);
                //$lSportsLeaguesModel->db->transRollback();
                return;
            }
        }
    }

    public function getBookmakers() {
        $pullOperations = new PullOperations('ODD', $this->logger);
        $locations = $pullOperations->getBookmakers();

        $values = array();
        foreach ($locations as $item) {
            $p = "("
                    . $item->Id . ", "
                    . "'" . $item->Name . "')";

            array_push($values, $p);
        }

        $model = new LSportsBookmakerModel();
        if (count($values) > 0) {
            try {
                //$model->db->transStart();
                $model->db->query(
                        'INSERT INTO `lsports_bookmaker`'
                        . '(id, `name`) VALUES '
                        . implode(',', $values)
                        . ' ON DUPLICATE KEY UPDATE '
                        . '`name` = VALUES(`name`)'
                );

                //$model->db->transComplete();
            } catch (\mysqli_sql_exception $e) {
                $query_str = (string) $model->getLastQuery();
                $this->logger->error("- getBookmakers error query_string : " . $query_str);
                //$model->db->transRollback();
                return;
            }
        }
    }

    public function getMarkets() {
        $pullOperations = new PullOperations('ODD', $this->logger);
        $locations = $pullOperations->getMarkets();

        $data = [];
        foreach ($locations as $item) {
//            $this->logger->debug("  * Id: $item->Id        Name: $item->Name       ");
            $insertData = [
                'id' => $item->Id,
                'name' => $item->Name,
            ];
            array_push($data, $insertData);
        }

        $model = new LSportsMarketsModel();
        $model->insertBatch($data);
    }

    public function getFixturesDailyData() {
        $this->logger->info('::::::::::::::: getFixturesDailyData Start :::::::::::::::');
        $lSportsFixturesModel = new LSportsFixturesModel();
        $pullOperations = new PullOperations('ODD', $this->logger); // ODD

        $fromDate = strtotime('-4 hours');
        if (DateTimeUtil::isWeekendDay(date('Y-m-d H:i:s'))) {
            if (config(App::class)->IsDevServer) {
                $toDate = strtotime('+24 hours');
            } else {
                $toDate = strtotime('+24 hours');
            }
        } else {
            if (config(App::class)->IsDevServer) {
                $toDate = strtotime('+24 hours');
            } else {
                $toDate = strtotime('+24 hours');
            }
        }

        //$fixturesResult = $pullOperations->getFixtures($fromDate, $toDate, null, $this->str_sports_ids, null, $this->str_leagues_ids, null);
        $fixturesResult = $pullOperations->getFixtures($fromDate, $toDate, null, null, null, null, null);
        if (!isset($fixturesResult) || null == $fixturesResult) {
            $this->logger->error('::::::::::::::: getFixturesDailyData end fixturesResult is body null :::::::::::::::');
            return null;
        }
        $fixtures = $fixturesResult->Body;
        $Header = $fixturesResult->Header;

        if (false == isset($fixtures) || null == $fixtures) {
            $this->logger->error('::::::::::::::: getFixturesDailyData end fixtures is body null :::::::::::::::');
            return null;
        }
        $sql = [];
        $participantSql = array();
        $arr_fixtures = array();
        foreach ($fixtures as $row) {

            if (false == CodeUtil::checkLeagues($row->Fixture, $lSportsFixturesModel, $this->logger)) {
                continue;
            }


            $kstStartDate = date("Y-m-d H:i:s", strtotime($row->Fixture->StartDate . '+9 hours'));
            $str_sql = " SELECT display_status FROM lsports_fixtures WHERE fixture_id = ? AND bet_type = 1";
            $arr_result = $lSportsFixturesModel->db->query($str_sql,[$row->FixtureId])->getResultArray();
            // 해당경기가 있을때만 체크

            if ($row->FixtureId === 7810502) {
                $this->logger->error('::::::::::::::: getFixturesDailyData kstStartDate =>' . $kstStartDate . ' status ==>' . $row->Fixture->Status);
            }

            if (0 < count($arr_result) && 8 == $row->Fixture->Status) {
                $display_status = $arr_result[0]['display_status'];
            } else {
                $display_status = CodeUtil::get_display_stauts($row->Fixture->Status, 1);
            }

            if (0 < count($arr_result) && (1 == $display_status || 2 == $display_status) && 3 == $arr_result[0]['display_status']) {
                continue;
            }

            if (3 == $row->Fixture->Status || 2 == $row->Fixture->Status) {
                $dt_current = date("Y-m-d H:i:s");
                if ($dt_current < $kstStartDate) {
                    continue;
                }
            }

            $arr_fixtures[] = $row->FixtureId;
            // 경기
            $sql = $this->bet_code_util->do_update_fix($row, $sql, 1, $kstStartDate, $display_status, $this->logger);
            $participantSql = $this->bet_code_util->do_update_participants($row->Fixture, $participantSql, $this->logger);
        }
        // 경기 업뎃
        if (count($sql) > 0) {
            $this->bet_code_util->do_update_fix_query($sql, $lSportsFixturesModel, $this->logger);
        }
        // 팀이름 업데이트 쿼리 실행
        if (count($participantSql) > 0) {
            $this->bet_code_util->do_update_participants_query($participantSql, $lSportsFixturesModel, $this->logger);
        }
        $this->logger->info('::::::::::::::: getFixturesDailyData End :::::::::::::::');
        return $arr_fixtures;
    }

    public function convertAverageBet($arr_fixtures) {
        try {

            //$arr_fixtures = [7909858];
            if (null == $arr_fixtures)
                return;
            $this->logger->info('::::::::::::::: convertAverageBet Start :::::::::::::::');
            $pullOperations = new PullOperations('ODD', $this->logger);
            $lSportsBetModel = new LSportsBetModel();

            $fromDate = strtotime('-4 hours');
            if (DateTimeUtil::isWeekendDay(date('Y-m-d H:i:s'))) {
                if (config(App::class)->IsDevServer) {
                    $toDate = strtotime('+24 hours');
                } else {
                    $toDate = strtotime('+24 hours');
                }
            } else {
                if (config(App::class)->IsDevServer) {
                    $toDate = strtotime('+24 hours');
                } else {
                    $toDate = strtotime('+24 hours');
                }
            }

            if (config(App::class)->IsDevServer) {
                $get_count = 20;
            } else {
                $get_count = 100;
            }

            $_fixtures = array_chunk($arr_fixtures, $get_count);
            $query_string = '';
            $events = [];
            foreach ($_fixtures as $key => $fixs) {
                $str_fixtures = implode(',', $fixs);
                //$eventResult = $pullOperations->getEvents($this->logger, $fromDate, $toDate, null, null, null, $this->str_leagues_ids, null, $str_fixtures);
                $eventResult = $pullOperations->getEvents($this->logger, $fromDate, $toDate, null, null, null, null, null, $str_fixtures);
                $ss = $eventResult->Body;
                $Header = $eventResult->Header;
                if (null == $ss || !isset($ss) || 0 == count($ss)) {
                    $this->logger->error('::::::::::::::: fail convertAverageBet $ss Null :::::::::::::::');
                    $this->logger->error('::::::::::::::: fail convertAverageBet str_fixture => ' . $str_fixtures);
                    continue;
                }
                $events = array_merge($events, $ss);

                $this->logger->debug('::::::::::::::: success getEvents events count ==> ' . $get_count);
                //sleep(3);
            }

            if (null == $events || !isset($events) || 0 == count($events)) {
                $this->logger->error('!!!!!!!!!!!!!!!!!!!************** fail convertAverageBet evnet Null ****************!!!!!!!!!!!!!!!!!!!!!!!!');
                $this->logger->error('!!!!!!!!!!!!!!!!!!!:::::::::::::: fail convertAverageBet evnet Null  :::::::::::::::!!!!!!!!!!!!!!!!!!!!!!!!');
                $this->logger->error('!!!!!!!!!!!!!!!!!!!************** fail convertAverageBet evnet Null ****************!!!!!!!!!!!!!!!!!!!!!!!!');
                return;
            }

            $sql = array();
            $sql_fixture = array(); // 경기 스코어 업데이트

            $array_book_maker = $this->bet_code_util->get_market_data(1, $lSportsBetModel, $this->logger);
            // 평균배당 제외 리스트
            $array_average_provider = $this->bet_code_util->get_average_dividend_provider_data($lSportsBetModel);
            $sql = [];
            //$avg_bet_sql = [];
            foreach ($events as $event) {
                $sport_id = $event->Fixture->Sport->Id;
                $kstStartDate = date("Y-m-d H:i:s", strtotime($event->Fixture->StartDate . '+9 hours'));
                // 실시간 정보 있을 경우 채워준다.
                $live_status = '';
                if ($event->Livescore != null) {
                    $live_status = $event->Livescore->Scoreboard->Status;
                } else {
                    $live_status = $event->Fixture->Status;
                }

                $display_status = CodeUtil::get_display_stauts($live_status, 1);
                // 해당경기가 있을때만 체크
                // display_status 값이 강제 종료 처리여서 더이상 데이터 업데이트를 받지 않는다.
                if (3 == $live_status || 2 == $live_status) {
                    $dt_current = date("Y-m-d H:i:s");
                    if ($dt_current < $kstStartDate) {
                        $this->logger->error(' kstStartDate : FixtureId :' . $event->FixtureId);
                        continue;
                        //if (7256235 == $event->FixtureId) {
                        //}
                    }
                }

                // 경기스코어
                $sql_fixture = $this->bet_code_util->do_update_fix_live_score($event, $display_status, $live_status, $kstStartDate, 1, $sql_fixture, $this->logger);

                if ($event->Markets == null)
                    continue;

                foreach ($event->Markets as $market) {
                    // 1XBet : 145 ,Bet-At-Home : 3,Bet365 : 8,MarathonBet : 74
                    $avg_bet = [];
                    if (false == isset($array_book_maker[$sport_id][$market->Id]))
                        continue;
                    if (1 != $market->Id && true == in_array($event->Fixture->League->Id, $this->arrLeague)) {
                        if (7724281 == $sport_id && 226 == $market->Id) {
                            $this->logger->error(' event->Fixture->League->Id : FixtureId :' . $event->FixtureId . ' league id : ' . $event->Fixture->League->Id . ' display_status :' . $display_status);
                        }
                        continue;
                    }
                    // 평균 배당 구하기
                    $avg_bet = $this->bet_code_util->do_average_dividend($event, $sport_id, $market, 1, $kstStartDate, $array_average_provider);
                    if (0 == count($avg_bet))
                        continue;

                    $arr_result = $this->bet_code_util->get_sp_lg_mk_refund_data($sport_id, $event->FixtureId, $market, 1, $lSportsBetModel);
                    if (!isset($arr_result) || 0 == count($arr_result)) {
                        $arr_sl_result = $this->bet_code_util->get_sp_lg_sl_refund_data($event->FixtureId, 1, $lSportsBetModel);
                    }

                    $refund_rate = 0;
                    $deduction_refund_rate = 0;
                    $is_margin_refund = 0;

                    if (isset($arr_result) && 0 < count($arr_result) && 0 < $arr_result[0]['mk_input_refund_rate']) {
                        $refund_rate = $arr_result[0]['mk_input_refund_rate'];
                    } else if (isset($arr_sl_result) && 0 < count($arr_sl_result) && 0 < $arr_sl_result[0]['lg_input_refund_rate']) {
                        $refund_rate = $arr_sl_result[0]['lg_input_refund_rate'];
                    } else if (isset($arr_sl_result) && 0 < count($arr_sl_result) && 0 < $arr_sl_result[0]['sp_input_refund_rate']) {
                        $refund_rate = $arr_sl_result[0]['sp_input_refund_rate'];
                    }

                    if (isset($arr_result) && 0 < count($arr_result) && 0 < $arr_result[0]['mk_deduction_refund_rate']) {
                        $deduction_refund_rate = $arr_result[0]['mk_deduction_refund_rate'];
                    } else if (isset($arr_sl_result) && 0 < count($arr_sl_result) && 0 < $arr_sl_result[0]['lg_deduction_refund_rate']) {
                        $deduction_refund_rate = $arr_sl_result[0]['lg_deduction_refund_rate'];
                    } else if (isset($arr_sl_result) && 0 < count($arr_sl_result) && 0 < $arr_sl_result[0]['sp_deduction_refund_rate']) {
                        $deduction_refund_rate = $arr_sl_result[0]['sp_deduction_refund_rate'];
                    }

                    if (isset($arr_result) && 0 < count($arr_result) && 0 < $arr_result[0]['mk_is_margin_refund']) {
                        $is_margin_refund = $arr_result[0]['mk_is_margin_refund'];
                    } else if (isset($arr_sl_result) && 0 < count($arr_sl_result) && 0 < $arr_sl_result[0]['lg_is_margin_refund']) {
                        $is_margin_refund = $arr_sl_result[0]['lg_is_margin_refund'];
                    } else if (isset($arr_sl_result) && 0 < count($arr_sl_result) && 0 < $arr_sl_result[0]['sp_is_margin_refund']) {
                        $is_margin_refund = $arr_sl_result[0]['sp_is_margin_refund'];
                    }

                    //$refund_rate = 0;
                    //$deduction_refund_rate = 0;
                    //$is_margin_refund = 0;
                    $b_do_refund = false;
                    if (1 == $display_status && 0 < $refund_rate && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                        $avg_bet = $this->bet_code_util->do_renew_cal_refund_rate($avg_bet, $refund_rate, $is_margin_refund);
                        $b_do_refund = true;
                    }
                    if (1 == $display_status && 0 < $deduction_refund_rate && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                        $avg_bet = $this->bet_code_util->do_renew_cal_deduction_refund_rate($avg_bet, $deduction_refund_rate, $refund_rate);
                        $b_do_refund = true;
                    }

                    // 마진 환수율은 일반,차감 환수율이 적용이 되야 적용이 된다.
                    if (true === $b_do_refund && 1 == $display_status && 1 == $is_margin_refund && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                        $avg_bet = $this->bet_code_util->do_renew_cal_margin_refund_rate($avg_bet, $deduction_refund_rate, $refund_rate, $lSportsBetModel);
                        $b_do_refund = true;
                    }

                    if (true == $b_do_refund) {
                        list($sql, $sql_log) = $this->bet_code_util->do_renew_refund_update_bet($avg_bet, $sport_id, $event->Fixture->League->Id, 1, $sql, $sql_log);
                    } else {
                        list($sql, $sql_log) = $this->bet_code_util->do_renew_update_bet($avg_bet, $sport_id, $event->Fixture->League->Id, 1, $sql, $sql_log);
                    }
                }
            }

            if (count($sql) > 0) {
                //$this->bet_code_util->do_update_bet_query($sql, 1, $lSportsBetModel, $this->logger);
            }
            // 경기스코어 업데이트
            if (count($sql_fixture) > 0) {
                $this->bet_code_util->do_update_fix_live_score_query($sql_fixture, $lSportsBetModel, $this->logger);
            }

            if (count($sql_log) > 0) {
                $log_model = new TLogLsportsBetModel();
                $log_model->do_insert_bet_query($sql_log, $this->logger);
            }
            $this->logger->info('::::::::::::::: convertAverageBet End :::::::::::::::');
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('convertAverageBet exception' . $e);
        } catch (\ReflectionException $e) {
            $this->logger->error($e);
        }
    }

    // lsports_fixtures, lsports_bet 초기화(일주일 지난거)
    public function initFixturesData() {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $delDate = date("Y-m-d H:i:s", strtotime("-1 week")); // 일주일 전
        $lSportsFixturesModel = new LSportsFixturesModel();
        $lSportsBetModel = new LSportsBetModel();
        $result = $lSportsBetModel->db->query("SELECT * FROM lsports_fixtures WHERE create_dt <= ?",[$delDate]);
    }

    // 일초기화
    public function initDayWork() {
        // 현재는 member.charge_first_per만 초기화
        $memberModel = new MemberModel();
        $result = $memberModel->db->query("UPDATE member SET charge_first_per = 0, is_exchange = 0, is_set_day_nt_gm = 'N' where idx > 0;");

        $this->logger->error('#######  reset charge_first_per, is_exchange ######');
        $this->logger->error($result);
        $this->logger->error('###################################################');
    }

    // 기간내 정산 내용 보기
    public function distributorCalculate() {
        $db_srch_s_date = isset($_REQUEST['srch_s_date']) ? $_REQUEST['srch_s_date'] : date("Y-m-d 00:00:00");
        $db_srch_e_date = isset($_REQUEST['srch_e_date']) ? $_REQUEST['srch_e_date'] : date("Y-m-d 23:59:59");

        $shopConfig = Calculate::distributorCalculate($this->logger, $db_srch_s_date, $db_srch_e_date);
        print_r(json_encode($shopConfig));
    }

    // 정산지급
    public function distributorCalculatePay() {
        $db_srch_s_date = date("Y-m-d 00:00:00", strtotime("-" . 1 . " days"));
        $db_srch_e_date = date("Y-m-d 23:59:59", strtotime("-" . 1 . " days"));

        //list($shopCalculate, $shopCalculateData, $shopConfig) = Calculate::distributorCalculate($this->logger, $db_srch_s_date, $db_srch_e_date);
        $shopConfig = Calculate::distributorCalculate($this->logger, $db_srch_s_date, $db_srch_e_date);
        /* print_r($shopCalculate);
          print_r($shopCalculateData); */
        //print_r($shopConfig);

        Calculate::distributorCalculatePoint($this->logger, $shopConfig, $db_srch_s_date);
    }
   

    // 일통계
    public function initTotalMemberCash() {
        $db_srch_s_date = date("Y-m-d 00:00:00", strtotime("-" . 1 . " days"));
        $db_srch_e_date = date("Y-m-d 23:59:59", strtotime("-" . 1 . " days"));

        try {
            $memberModel = new MemberModel();

            //$memberModel->db->transStart();
            $result = $memberModel->db->query("call insert_total_member_cash('$db_srch_s_date', '$db_srch_e_date')");
            //$memberModel->db->transComplete();
            $this->logger->info('#######  initTotalMemberCash query => ' . $memberModel->getLastQuery());
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('bettingCancel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: initTotalMemberCash query : ' . $memberModel->getLastQuery());
            //$memberModel->db->transRollback();
        }
    }

    // 결과 처리 보정 함수
    public function doProcessScoreResult() {

        $this->logger->error('::::::::::::::: doProcessScoreResult start : ');
        $memberBetDetailModel = new MemberBetDetailModel();

        try {
            $memberBetDetailModel->db->transStart();

            $arrMbBetResult = $memberBetDetailModel->SelectMemberBetResultScoreProcessing(); // 
            //$this->logger->error('::::::::::::::: doProcessScoreResult $arrMbBetResult : '. json_encode($arrMbBetResult));
            if (true == empty($arrMbBetResult) || !isset($arrMbBetResult) || 0 === count($arrMbBetResult)) {
                $memberBetDetailModel->db->transComplete();
                $this->logger->error('::::::::::::::: doProcessScoreResult return end : ');
                return;
            }

            foreach ($arrMbBetResult as $detail) {
                $bet_status = 0;
                $bet_price = 0;
                list($bet_status, $bet_price, $detail['live_results_p1'], $detail['live_results_p2']) = BetDataUtil::getBetStatus($detail, $this->logger); // 2,4,6중 하나의 값을 갖는다.

                if (true === isset($detail['live_results_p1']) && true === isset($detail['live_results_p2'])) {
                    $array_result_score = array('live_results_p1' => $detail['live_results_p1'], 'live_results_p2' => $detail['live_results_p2']);
                    $array_result_score = addslashes((json_encode($array_result_score)));
                    $memberBetDetailModel->UpdateScoreMemberBetDetail($detail['idx'], -1, $array_result_score); // member_bet_detail 
                }
            }

            $memberBetDetailModel->db->transComplete();
            $this->logger->error('::::::::::::::: doProcessScoreResult end : ');
        } catch (\mysqli_sql_exception $e) {
            $logger->error('doProcessScoreResult [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: doProcessScoreResult query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
        } 
    }

    // 자동레벨업 체크 함수
    public function doProcessAutoLevelup() {
        $this->logger->error('::::::::::::::: doProcessAutoLevelup start : ');
        $memberModel = new MemberModel();
        try {

            $memberModel->db->transStart();
            $sql = "SELECT * from member_level_up where flag = 'ON' order by level desc ";
            $arrLevelUpData = $memberModel->db->query($sql)->getResultArray();
            if (false === isset($arrLevelUpData) || 0 === count($arrLevelUpData)) {

                $this->logger->error('::::::::::::::: doProcessAutoLevelup return end : arrLevelUpData');
                $memberModel->db->transRollback();
                return;
            }

            $sql = "WITH CTE AS (SELECT 
                        mb.idx,
                        mb.level,
                        IFNULL((select sum(money) from member_money_charge_history as ch 
                                                where mb.idx = ch.member_idx 
                                                AND ch.status = 3 
                                    AND ch.update_dt >= DATE_SUB(NOW(), INTERVAL mb_lu.nonDepositDatePeriod DAY)
                                    AND ch.update_dt <= NOW() ),0) as today_total_ch_money, -- 기간 총 입금
                        IFNULL((select sum(money) from member_money_exchange_history as ex 
                                                where mb.idx = ex.member_idx 
                                                AND ex.status = 3 
                                    AND ex.update_dt >= DATE_SUB(NOW(), INTERVAL mb_lu.nonDepositDatePeriod DAY)
                                    AND ex.update_dt <= NOW() ),0)as today_total_ex_money, -- 기간 총 출금
                        IFNULL((select sum(total_bet_money) from member_bet as mb_bet where mb.idx = mb_bet.member_idx 
                                                AND mb_bet.bet_status = 3 
                                    AND mb_bet.calculate_dt >= DATE_SUB(NOW(), INTERVAL  mb_lu.nonDepositDatePeriod DAY)
                                    AND mb_bet.calculate_dt <= NOW()),0) as today_total_bet_money, -- 기간 배팅금

                        IFNULL((select count(ch.idx) from member_money_charge_history as ch 
                                                where mb.idx = ch.member_idx 
                                                AND ch.status = 3 
                                    AND ch.update_dt >= DATE_SUB(NOW(), INTERVAL mb_lu.nonDepositDatePeriod DAY)
                                    AND ch.update_dt <= NOW() ),0) as today_total_ch_count, -- 충전 횟수 
                        mb_lu.nonDepositDatePeriod,
                        IFNULL(tot_mb_cs.charge_total_money,0) as charge_total_money,
                        IFNULL(tot_mb_cs.exchange_total_money,0) as exchange_total_money,
                            IFNULL(tot_mb_cs.charge_total_count,0) as charge_total_count,
                        IFNULL(tot_mb_cs.charge_total_money,0) -  IFNULL(tot_mb_cs.exchange_total_money,0) -  mb.money as calcurator
                        FROM member as mb
                            LEFT JOIN member_level_up as mb_lu ON mb.level = mb_lu.level      
                        LEFT JOIN  total_member_cash as tot_mb_cs ON mb.idx = tot_mb_cs.member_idx   
                                    WHERE mb.level <> 9 AND mb.u_business = 1 AND mb.status = 1 AND mb.auto_level = 'Y'  -- and mb.idx = 1166
                                    GROUP BY mb.idx
                    )  SELECT * FROM CTE;";

            $arrMemberData = $memberModel->db->query($sql)->getResultArray();

            if (false === isset($arrMemberData) || 0 === count($arrMemberData)) {

                $this->logger->error('::::::::::::::: doProcessAutoLevelup return end arrMemberData: ');
                $memberModel->db->transRollback();
                return;
            }

            foreach ($arrMemberData as $value) {
                $today_total_ch_ex_money_difference = $value['today_total_ch_money'] - $value['today_total_ex_money'];

                //$this->logger->error('::::::::::::::: doProcessAutoLevelup start data : '. json_encode($value));
                $idx = $value['idx'];
                $level = $value['level'];
                //$this->logger->error('::::::::::::::: doProcessAutoLevelup start level 2: ');
                $today_total_ch_money = $value['today_total_ch_money'];
                $today_total_ex_money = $value['today_total_ex_money'];
                $today_total_bet_money = $value['today_total_bet_money'];

                $today_total_ch_count = $value['today_total_ch_count'];
                $nonDepositDatePeriod = $value['nonDepositDatePeriod'];
                $charge_total_money = $value['charge_total_money'];
                $exchange_total_money = $value['exchange_total_money'];

                $charge_total_count = $value['charge_total_count'];
                $calcurator = $value['calcurator'];
                $updateLevel = $levelData['level'];

                if (1 != $level && 0 == $today_total_ch_money && 0 == $today_total_ex_money && 0 == $today_total_bet_money) {
                    // SELECT v_idx,1;
                    $sql = "UPDATE member set level = 1,auto_level = 'N' WHERE idx = ?";
                    $memberModel->db->query($sql,[$idx]);
                    $this->logger->error('::::::::::::::: doProcessAutoLevelup start level 3: ');
                    $sql = "insert into temp_level_up(member_idx,bf_level,level,v_today_total_ch_money,v_today_total_ex_money,v_today_total_bet_money) 
                    values($idx,$level,1,$today_total_ch_money,$today_total_ex_money,$today_total_bet_money)";
                    $memberModel->db->query($sql);
                    continue;
                }

                if (1 == $level && 0 == $today_total_ch_money && 0 == $today_total_ex_money && 0 == $today_total_bet_money) {
                    //$this->logger->error('::::::::::::::: doProcessAutoLevelup start level 2: ');
                    $this->logger->error('doProcessAutoLevelup continue idx => ' . $idx . ' level =>' . $level . ' today_total_ch_money =>' . $today_total_ch_money
                            . ' today_total_ex_money =>' . $today_total_ex_money . ' today_total_bet_money =>' . $today_total_bet_money);
                    continue;
                }

                $bFind = false;
               
                foreach ($arrLevelUpData as $levelData) {
                  if($level == $updateLevel){
                      $bFind = true;
                      break;
                  }
                }
                
                // 해당 유저는 고정 레벨이다
                if(false == $bFind) continue;
                
                foreach ($arrLevelUpData as $levelData) {
                    if ((0 < $levelData['charge'] && $levelData['charge'] <= $charge_total_money) || (0 < $levelData['exchange'] && $levelData['exchange'] <= $exchange_total_money) || (0 < $levelData['calcurate'] && $levelData['calcurate'] <= $calcurator) || (0 < $levelData['charge_count'] && $levelData['charge_count'] <= $charge_total_count)) {

                        //$this->logger->error('::::::::::::::: doProcessAutoLevelup start level 1: ');
                        $sql = "UPDATE member set level = $updateLevel WHERE idx = $idx";
                        $memberModel->db->query($sql);
                        
                        $sql = "insert into temp_level_up(
                                 member_idx
                                ,bf_level
                                ,level
                                ,v_today_total_ch_money
                                ,v_today_total_ex_money
                                ,v_today_total_bet_money
                                ,v_today_total_ch_ex_money_difference
                                ,v_today_total_ch_count
                                ,v_nonDepositDatePeriod
                                ,r_charge_total_money
                                ,r_exchange_total_money
                                ,r_charge_total_count
                                ,r_calcurator
                            ) 
                            values($idx,$level
                                ,$updateLevel,$today_total_ch_money
                                ,$today_total_ex_money
                                ,$today_total_bet_money
                                ,$today_total_ch_ex_money_difference
                                ,$today_total_ch_count
                                ,$nonDepositDatePeriod
                                ,$charge_total_money
                                ,$exchange_total_money
			        ,$charge_total_count
			        ,$calcurator)";

                        $memberModel->db->query($sql);

                        break;
                    }
                }
            }

            $memberModel->db->transComplete();
            $this->logger->error('::::::::::::::: doProcessAutoLevelup end : ');
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('doProcessAutoLevelup [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: doProcessAutoLevelup query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
        } 
    }

    
    
    // 프리매치 경기 시작 시간 지나도 마감 처리 안되는 경기 마감처리 display_status => 2로 변경 
    public function doProcessUpdateDisplayStatus() {

        $this->logger->info('::::::::::::::: doProcessUpdateDisplayStatus start : ');
        $memberBetDetailModel = new MemberBetDetailModel();

        try {
                      
            $str_sql = "UPDATE lsports_fixtures SET display_status = 2 WHERE fixture_start_date < now() AND display_status = 1 AND bet_type = 1 ";
            $memberBetDetailModel->db->query($str_sql);
            $this->logger->info('::::::::::::::: doProcessUpdateDisplayStatus end : ');
        } catch (\mysqli_sql_exception $e) {
            $logger->error('doProcessUpdateDisplayStatus [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('::::::::::::::: doProcessUpdateDisplayStatus query : ' . $memberBetDetailModel->getLastQuery());
            
        } 
    }
}
