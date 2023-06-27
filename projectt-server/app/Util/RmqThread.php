<?php

namespace App\Util;

use App\Models\LSportsFixturesModel;
use App\Util\CodeUtil;
use pht\{Queue};
class RmqThread extends Thread {

    public $queue;
    protected $logger;
    public $runing = false;
    public $name = '';

    public function __construct($name, $logger) {
        $this->runing = true;
        $this->logger = $logger;
        $this->name = $name;
        $this->queue = new Queue();
    }

    public function run() {

        echo "in Thread({$this->name}) is runed!\n";

        while ($this->runing) {
          
            $this->queue->lock();
            while ($this->queue->size()) {
                
                $msg = $this->queue->pop();
                $result = json_decode($msg->body);
                $Header = $result->Header;
                if (2 == $Header->Type) {
                    $Body = $result->Body;
                    //! 쓰레드로 빼도됨 (큐 방식으로 하면 제어가 될거임)
                    $this->setFixturesDataType2($Body->Events, 1);
                } else if (32 == $Header->Type) {
                    $logger->debug('prematch : ' . $Header->Type);
                }
            }
            $this->queue->unlock();
            sleep(1);
        }
    }

    public function setFixturesDataType2($fixtures, $bet_type) {
        $lSportsFixturesModel = new LSportsFixturesModel();
        $sql = array();
        try {
            $query_str = '';
            foreach ($fixtures as $row) {

                $query_str_fix = '';
                // 해당하는 pkey를 가져온다.
                if (1 == $bet_type) {
                    $query_str_fix = "SELECT fixtures.display_status,fixtures.fixture_start_date,fixtures.fixture_sport_id from lsports_fixtures as fixtures "
                            . " LEFT JOIN lsports_leagues as leagues ON fixtures.fixture_league_id = leagues.id AND fixtures.fixture_sport_id = leagues.sport_id "
                            . " WHERE fixtures.fixture_id = $row->FixtureId AND fixtures.display_status IN (1,2) AND fixtures.bet_type = $bet_type AND leagues.is_use = 1 ORDER BY fixtures.fixture_start_date DESC LIMIT 1";
                } else {
                    $query_str_fix = "SELECT fixtures.display_status,fixtures.fixture_start_date,fixtures.fixture_sport_id from lsports_fixtures as fixtures"
                            . " WHERE fixtures.fixture_id = $row->FixtureId AND fixtures.display_status IN (1,2) AND fixtures.bet_type = $bet_type  ORDER BY fixtures.fixture_start_date DESC LIMIT 1";
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

                $live_time = $row->Livescore->Scoreboard->Time;
                $live_current_period = $row->Livescore->Scoreboard->CurrentPeriod;
                $value1 = $row->Livescore->Scoreboard->Results[0]->Value;
                $value2 = $row->Livescore->Scoreboard->Results[1]->Value;

                // 해당 경기데이터를 업뎃
                $status = $row->Livescore->Scoreboard->Status;
                $liveScore = addslashes(json_encode($row->Livescore));
                $query_str .= " UPDATE lsports_fixtures "
                        . "SET live_time = '$live_time', live_current_period = $live_current_period,display_status = $display_status, fixture_status = '$status', live_results_p1 = $value1, live_results_p2 = $value2, "
                        . "livescore = '$liveScore' WHERE fixture_id = $row->FixtureId AND bet_type = $bet_type; ";
            }
            if ('' != $query_str) {
                $lSportsFixturesModel->db->query($query_str);
            }

            //$lSportsFixturesModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            //$lSportsBetModel->db->transRollback();
        } 
    }

    public function setRenewFixtureMarketsToInsertBetType3($events) {
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
                $arr_result = $this->bet_code_util->get_sp_lg_refund_data($event->FixtureId, 2, $lSportsBetModel, $this->logger);
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

                    $refund_rate = 0;

                    if (0 < $arr_result[0]['lg_input_refund_rate']) {
                        $refund_rate = $arr_result[0]['lg_input_refund_rate'];
                    } else if (0 < $arr_result[0]['sp_input_refund_rate']) {
                        $refund_rate = $arr_result[0]['sp_input_refund_rate'];
                    }
                    $array_in_refund_rate_market_id = array(4, 6, 7, 9, 16, 17, 70, 98, 99, 390, 427, 1537, 1538);

                    //$this->logger->debug('::::::::::::::: setRenewFixtureMarketsToInsertBetType3 FixtureId :'.$event->FixtureId.' refund_rate : '.$refund_rate.' display_status :'.$display_status.' market_id : '.$market->Id);

                    if (2 == $display_status && 0 < $refund_rate && false == in_array($market->Id, $array_in_refund_rate_market_id)) {
                        $sql = $this->bet_code_util->do_refund_rate($event, $provider_main, $fixture_sport_id, $market, 2, $refund_rate, $kstStartDate, $sql, $this->logger);
                    } else {
                        foreach ($provider_main->Bets as $bet) {
                            if (0 == $bet->Price || true == $this->bet_code_util->inplay_base_line_markets_except($bet, $fixture_sport_id, $market->Id, $this->logger))
                                continue;
                            $sql = $this->bet_code_util->do_update_bet($fixture_sport_id,$provider_main, $event->FixtureId, $bet, $market, 2, $kstStartDate, $sql, $this->logger);
                        }
                    }
                }
            }

            if (count($sql) > 0) {
                //! 얘만 일단 쓰레드로 빠지면됨
                $this->bet_code_util->do_update_bet_query($sql, 3, $lSportsBetModel, $this->logger);
            }
        } catch (\mysqli_sql_exception $e) {
            $logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        } 

        //$this->logger->debug('::::::::::::::: setRenewFixtureMarketsToInsertBetType3 End :::::::::::::::');
    }

}
