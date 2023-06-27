<?php

namespace App\Controllers;

@set_time_limit(0);
ini_set("memory_limit", -1);

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Models\GameModel;
use App\Models\MemberModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\PullOperations;
use App\Models\LSportsBetModel;
use App\Models\LSportsBookmakerModel;
use App\Models\LSportsFixturesModel;
use App\Models\LSportsLeaguesModel;
use App\Models\LSportsLocationsModel;
use App\Models\LSportsMarketsModel;
use App\Models\LSportsSportsModel;
use App\Util\BetDataUtil;
use App\Util\CodeUtil;
use App\Util\BetCodeUtil;

//use App\Util\RmqThread;

class RmqController extends BaseController {

    use ResponseTrait;

    protected $bet_code_util;
    //protected $rmqPrematchThread;
    protected $array_in_refund_rate_market_id = array(4, 6, 7, 9, 16, 17, 70, 98, 99, 390, 427, 1537, 1538);

    //// API
    public function __construct() {
        $this->bet_code_util = new BetCodeUtil();
        //$this->rmqPrematchThread = new RmqThread('thread', $this->logger);
        //$this->rmqPrematchThread->start();
    }

    public function __destruct() {
        //$this->rmqPrematchThread->runing = false;
    }

    public function setFixturesDataType1($fixtures, $bet_type) {
        $lSportsFixturesModel = new LSportsFixturesModel();
        $sql = array();
        $participantSql = array();
        foreach ($fixtures as $row) {
            $kstStartDate = date("Y-m-d H:i:s", strtotime($row->Fixture->StartDate . '+9 hours'));
            $day = date("d", strtotime($row->Fixture->StartDate . '+9 hours'));
            $str_sql = " SELECT display_status,fixture_last_update FROM lsports_fixtures WHERE pkey = '$day' AND fixture_id = $row->FixtureId AND bet_type = $bet_type";
            $arr_result = $lSportsFixturesModel->db->query($str_sql)->getResultArray();

            if (0 < count($arr_result) && 8 == $row->Fixture->Status) {
                $display_status = $arr_result[0]['display_status'];
            } else {
                $display_status = CodeUtil::get_display_stauts($row->Fixture->Status, 1);
            }

            if (0 < count($result) && (1 == $display_status || 2 == $display_status) && 3 == $arr_result[0]['display_status'])
                continue;
            // if (0 < count($arr_result) && $row->Fixture->LastUpdate < $arr_result[0]['fixture_last_update'])
            //     continue;

            if (3 == $row->Fixture->Status || 2 == $row->Fixture->Status) {
                $dt_current = date("Y-m-d H:i:s");
                if ($dt_current < $kstStartDate)
                    continue;
            }
            $this->bet_code_util->do_update_fix($row, $sql, $bet_type, $kstStartDate, $display_status, $this->logger);
        }
        if (count($sql) > 0) {
            $this->bet_code_util->do_update_fix_query($sql, $lSportsFixturesModel, $this->logger);
        }
    }

    public function setFixturesDataType2($fixtures, $bet_type) {
        $lSportsFixturesModel = new LSportsFixturesModel();
        $sql = array();
        try {
            $query_str = '';
            foreach ($fixtures as $row) {
                //if(8 == $row->Livescore->Scoreboard->Status) continue;
                $query_str_fix = '';
                // 해당하는 pkey를 가져온다.
                if (1 == $bet_type) {
                    $query_str_fix = "SELECT IF('ON' = fixtures.passivity_flag AND fixtures.display_status_passivity is NOT NULL ,fixtures.display_status_passivity,fixtures.display_status) as display_status"
                            . ",fixtures.fixture_start_date,fixtures.fixture_sport_id from lsports_fixtures as fixtures "
                            . " LEFT JOIN lsports_leagues as leagues ON fixtures.fixture_league_id = leagues.id AND fixtures.fixture_sport_id = leagues.sport_id "
                            . " WHERE fixtures.fixture_id = $row->FixtureId AND IF('ON' = fixtures.passivity_flag AND fixtures.display_status_passivity is NOT NULL ,fixtures.display_status_passivity,fixtures.display_status) IN (1,2) "
                            . "AND fixtures.bet_type = $bet_type AND leagues.is_use = 1 ORDER BY fixtures.fixture_start_date DESC LIMIT 1";
                } else {
                    $query_str_fix = "SELECT IF('ON' = fixtures.passivity_flag AND fixtures.display_status_passivity is NOT NULL ,fixtures.display_status_passivity,fixtures.display_status) as display_status"
                            . ",fixtures.fixture_start_date,fixtures.fixture_sport_id from lsports_fixtures as fixtures"
                            . " WHERE fixtures.fixture_id = $row->FixtureId AND IF('ON' = fixtures.passivity_flag AND fixtures.display_status_passivity is NOT NULL ,fixtures.display_status_passivity,fixtures.display_status) IN (1,2) "
                            . "AND fixtures.bet_type = $bet_type  ORDER BY fixtures.fixture_start_date DESC LIMIT 1";
                }

                $result = $lSportsFixturesModel->db->query($query_str_fix)->getResultArray();
                if (!isset($result) || 0 == count($result))
                    continue;

                $fixture_start_date = $result[0]['fixture_start_date'];
                if (8 == $row->Livescore->Scoreboard->Status) {
                    $display_status = $result[0]['display_status'];
                } else {
                    $display_status = CodeUtil::get_display_stauts($row->Livescore->Scoreboard->Status, $bet_type);
                }

                //$display_status = CodeUtil::get_display_stauts($row->Livescore->Scoreboard->Status, $bet_type);
                if (0 < count($result) && (1 == $display_status || 2 == $display_status) && 3 == $result[0]['display_status'])
                    continue;

                if (3 == $row->Livescore->Scoreboard->Status || 2 == $row->Livescore->Scoreboard->Status) {
                    $dt_current = date("Y-m-d H:i:s");
                    if ($dt_current < $fixture_start_date)
                        continue;
                }

                // 경기
                $value1 = 0;
                $value2 = 0;
                $live_time = 0;
                $live_current_period = '';
                if (false == isset($row->Livescore->Scoreboard->Results) ||
                        false == isset($row->Livescore->Scoreboard->CurrentPeriod) ||
                        false == isset($row->Livescore->Scoreboard->Time) ||
                        false == isset($row->Livescore->Scoreboard->Results[0]->Value) ||
                        false == isset($row->Livescore->Scoreboard->Results[1]->Value))
                    continue;

                $value1 = $row->Livescore->Scoreboard->Results[0]->Value;
                $value2 = $row->Livescore->Scoreboard->Results[1]->Value;


                $Periods = true == isset($row->Livescore->Periods) ? $row->Livescore->Periods : [];
                foreach ($Periods as $key => $Period) {
                    unset($Periods[$key]->Incidents); // = null;
                }


                if (true == isset($Periods) && false == empty($Periods)) {
                    $row->Livescore->Periods = $Periods;
                }

                // 해당 경기데이터를 업뎃
                $status = $row->Livescore->Scoreboard->Status;
                $liveScore = addslashes(json_encode($row->Livescore));
                /* $query_str .= " UPDATE lsports_fixtures "
                  . "SET live_time = '$live_time', live_current_period = $live_current_period,display_status = $display_status, fixture_status = '$status', live_results_p1 = $value1, live_results_p2 = $value2, "
                  . "livescore = '$liveScore' WHERE fixture_id = $row->FixtureId AND bet_type = $bet_type; "; */

                $query_str .= " UPDATE lsports_fixtures "
                        . "SET display_status = $display_status, fixture_status = '$status', live_results_p1 = $value1, live_results_p2 = $value2, "
                        . "livescore = '$liveScore' WHERE fixture_id = $row->FixtureId AND bet_type = $bet_type; ";
            }
            if ('' != $query_str) {
                $lSportsFixturesModel->db->query($query_str);
            }

            //$lSportsFixturesModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            //$lSportsBetModel->db->transRollback();
        } 
    }

    public function setDeductionFixtureMarketsToInsertBetType3($events) {
        try {
            //$this->logger->debug('::::::::::::::: setRenewFixtureMarketsToInsertBetType3 Start :::::::::::::::');
            $lSportsBetModel = new LSportsBetModel();
            $sql = array();
            //! redis 예정
            $array_book_maker = $this->bet_code_util->get_market_data(2, $lSportsBetModel, $this->logger);
            $sql_bet = [];
            foreach ($events as $event) {
                // 해당하는 pkey를 가져온다.
                //! redis 예정
                $arr_result = $this->bet_code_util->get_renew_sp_lg_deduction_refund_data($event->FixtureId, 2, $lSportsBetModel, $this->logger);
                if (!isset($arr_result) || 0 == count($arr_result)) {
                    continue;
                }
                $display_status = $arr_result[0]['display_status'];
                $fixture_start_date = $arr_result[0]['fixture_start_date'];
                $fixture_sport_id = $arr_result[0]['fixture_sport_id'];
                foreach ($event->Markets as $market) {
                    if (false == isset($array_book_maker[$fixture_sport_id][$market->Id]))
                        continue;
                    $provider_main = null;
                    foreach ($market->Providers as $provider) {
                        if ($provider->Id != $array_book_maker[$fixture_sport_id][$market->Id]['main_book_maker'])
                            continue;
                        $provider_main = $provider;
                        break;
                    }

                    if (null == $provider_main) {
                        continue;
                    }

                    $arr_mk_result = $this->bet_code_util->get_sp_lg_mk_refund_data($fixture_sport_id, $event->FixtureId, $market, 2, $lSportsBetModel);

                    $refund_rate = 0;
                    if (isset($arr_mk_result) && 0 < count($arr_mk_result) && 0 < $arr_mk_result[0]['mk_deduction_refund_rate']) {
                        $refund_rate = $arr_mk_result[0]['mk_deduction_refund_rate'];
                    } else if (0 < count($arr_result) && 0 < $arr_result[0]['lg_deduction_refund_rate']) {
                        $refund_rate = $arr_result[0]['lg_deduction_refund_rate'];
                    } else if (0 < count($arr_result) && 0 < $arr_result[0]['sp_deduction_refund_rate']) {
                        $refund_rate = $arr_result[0]['sp_deduction_refund_rate'];
                    }

                    //$this->logger->debug('::::::::::::::: setRenewFixtureMarketsToInsertBetType3 FixtureId :'.$event->FixtureId.' refund_rate : '.$refund_rate.' display_status :'.$display_status.' market_id : '.$market->Id);

                    if (2 == $display_status && 0 < $refund_rate && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                        $sql = $this->bet_code_util->do_refund_rate($event, $provider_main, $fixture_sport_id, $market, 2, $refund_rate, $kstStartDate, $sql, $this->logger);
                    } else {
                        foreach ($provider_main->Bets as $bet) {
                            if (0 == $bet->Price || true == $this->bet_code_util->inplay_base_line_markets_except($bet, $fixture_sport_id, $market->Id, $this->logger))
                                continue;
                            $sql = $this->bet_code_util->do_update_bet($fixture_sport_id, $provider_main, $event->FixtureId, $bet, $market, 2, $kstStartDate, $sql, $this->logger);
                        }
                    }
                }
            }

            if (count($sql) > 0) {
                //! 얘만 일단 쓰레드로 빠지면됨
                $this->bet_code_util->do_update_bet_query($sql, 3, $lSportsBetModel, $this->logger);
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        }

        //$this->logger->debug('::::::::::::::: setRenewFixtureMarketsToInsertBetType3 End :::::::::::::::');
    }

    public function setFixtureMarketsToInsertBetType35($events, $bet_type) {
        try {
            //$this->logger->debug('::::::::::::::: setFixtureMarketsToInsertBetType35 Start :::::::::::::::');
            $lSportsBetModel = new LSportsBetModel();
            $sql = array();
            //! redis 예정
            $array_book_maker = $this->bet_code_util->get_market_data($bet_type, $lSportsBetModel, $this->logger);
            $sql_bet = [];
            foreach ($events as $event) {
                // 해당하는 pkey를 가져온다.
                //! redis 예정
                $arr_result = $this->bet_code_util->get_sp_lg_refund_data($event->FixtureId, $bet_type, $lSportsBetModel, $this->logger);
                if (!isset($arr_result) || 0 == count($arr_result)) {
                    continue;
                }
                $display_status = $arr_result[0]['display_status'];
                $fixture_start_date = $arr_result[0]['fixture_start_date'];
                $fixture_sport_id = $arr_result[0]['fixture_sport_id'];
                foreach ($event->Markets as $market) {
                    if (false == isset($array_book_maker[$fixture_sport_id][$market->Id]))
                        continue;

                    $refund_rate = 0;
                    if (0 < $arr_result[0]['lg_input_refund_rate']) {
                        $refund_rate = $arr_result[0]['lg_input_refund_rate'];
                    } else if (0 < $arr_result[0]['sp_input_refund_rate']) {
                        $refund_rate = $arr_result[0]['sp_input_refund_rate'];
                    }

                    if (1 == $bet_type) {
                        $sql = $this->bet_code_util->do_min_odd_data($event, $fixture_sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $sql, $array_book_maker[$fixture_sport_id][$market->Id], $this->logger);
                    } else {
                        $provider_main = null;
                        foreach ($market->Providers as $provider) {
                            if ($provider->Id != $array_book_maker[$fixture_sport_id][$market->Id]['main_book_maker'])
                                continue;
                            $provider_main = $provider;
                            break;
                        }

                        if (null == $provider_main) {
                            continue;
                        }
                        //$this->logger->debug('::::::::::::::: setRenewFixtureMarketsToInsertBetType3 FixtureId :'.$event->FixtureId.' refund_rate : '.$refund_rate.' display_status :'.$display_status.' market_id : '.$market->Id);
                        if (2 == $display_status && 0 < $refund_rate && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                            $sql = $this->bet_code_util->do_refund_rate($event, $provider_main, $fixture_sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $sql, $this->logger);
                        } else {
                            foreach ($provider_main->Bets as $bet) {
                                if (0 == $bet->Price || true == $this->bet_code_util->inplay_base_line_markets_except($bet, $fixture_sport_id, $market->Id, $this->logger))
                                    continue;
                                $sql = $this->bet_code_util->do_update_bet($fixture_sport_id, $provider_main, $event->FixtureId, $bet, $market, $bet_type, $kstStartDate, $sql, $this->logger);
                            }
                        }
                    }
                }
            }

            if (count($sql) > 0) {
                //! 얘만 일단 쓰레드로 빠지면됨
                $this->bet_code_util->do_update_bet_query($sql, 2, $lSportsBetModel, $this->logger);
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        } 
    }

    public function rmqPreMatch() {

        // $connection = new AMQPStreamConnection('prematch-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, false, 580);
        $connection = new AMQPStreamConnection('prematch-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, true, 580);

        $channel = $connection->channel();

        $queue = '_3065_';

        $channel->basic_qos(0, 1000, false);
        $callback = function($msg) {
            //https://prematch.lsports.eu/OddService/EnablePackage?username=tombow3455@gmail.com&password=43SDLK4sd3&guid=a51eae0f-1c1d-453a-84eb-3219061e418d
            //https://prematch.lsports.eu/OddService/DisablePackage?username=tombow3455@gmail.com&password=43SDLK4sd3&guid=a51eae0f-1c1d-453a-84eb-3219061e418d
            //http://localhost/rmq/rmqPreMatch
            $result = json_decode($msg->body);
            $Header = $result->Header;
            if (2 == $Header->Type) {
                $Body = $result->Body;
                //! 쓰레드로 빼도됨 (큐 방식으로 하면 제어가 될거임)
                $this->setFixturesDataType2($Body->Events, 1);
            } else if (32 == $Header->Type) {
                $this->logger->debug('prematch : ' . $Header->Type);
            }
        };

        $channel->basic_consume($queue, 'consumer', false, true, false, false, $callback);
        while (count($channel->callbacks)) {

            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function rmqRenewPreMatch() {

        // $connection = new AMQPStreamConnection('prematch-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, false, 580);
        $connection = new AMQPStreamConnection('prematch-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, true, 580);

        $channel = $connection->channel();

        $queue = '_3065_';

        $channel->basic_qos(0, 1000, false);
        $callback = function($msg) {
            //https://prematch.lsports.eu/OddService/EnablePackage?username=tombow3455@gmail.com&password=43SDLK4sd3&guid=a51eae0f-1c1d-453a-84eb-3219061e418d
            //http://localhost/rmq/rmqPreMatch
            $this->rmqPrematchThread->queue->lock();
            $this->rmqPrematchThread->queue->push($msg);
            $this->rmqPrematchThread->unlock();
        };

        $channel->basic_consume($queue, 'consumer', false, true, false, false, $callback);
        while (count($channel->callbacks)) {

            $channel->wait();
        }

        $this->rmqPrematchThread->runing = false;
        $channel->close();
        $connection->close();
    }

    //https://inplay.lsports.eu/api/schedule/GetInPlaySchedule?username=tombow3455@gmail.com&password=43SDLK4sd3&packageid=3066
    public function rmqInPlay() {
        //https://inplay.lsports.eu/api/Package/DisablePackage?username=tombow3455@gmail.com&password=43SDLK4sd3&packageid=3066
        //https://inplay.lsports.eu/api/Package/EnablePackage?username=tombow3455@gmail.com&password=43SDLK4sd3&packageid=3066
        //http://localhost/rmq/rmqInPlay
        // $connection = new AMQPStreamConnection('inplay-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, false, 580);
        $connection = new AMQPStreamConnection('inplay-rmq.lsports.eu', 5672, 'tombow3455@gmail.com', '43SDLK4sd3', "Customers", false, 'AMQPLAIN', null, 'en_US', 1160, 1160, null, true, 580);

        $channel = $connection->channel();

        $queue = '_3066_';

        $channel->basic_qos(0, 1000, false);

        $callback = function($msg) {
            $result = json_decode($msg->body);
            $Header = $result->Header;
            if (2 == $Header->Type) {
                $Body = $result->Body;
                //! 쓰레드로 빼도됨
                $this->setFixturesDataType2($Body->Events, 2);
            } else if (3 == $Header->Type) {
                $Body = $result->Body;
                //! 쓰레드로 빼도됨
                $this->setDeductionFixtureMarketsToInsertBetType3($Body->Events);
            } else if (32 == $Header->Type) {
                $this->logger->debug($Header->Type);
            }
        };

        $channel->basic_consume($queue, 'consumer', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

}
