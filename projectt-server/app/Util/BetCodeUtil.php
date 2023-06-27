<?php

namespace App\Util;

use CodeIgniter\Log\Logger;

class BetCodeUtil {

    protected $array_in_prematch_soccer_market_id = array(1, 2, 3, 4, 6, 7, 11, 16, 17, 30, 31, 95, 98, 99, 101, 102, 41, 427);
    protected $array_in_prematch_basket_ball_market_id = array(21, 28, 53, 63, 64, 69, 70, 71, 77, 153, 155, 220, 221, 226, 282, 342, 390);
    protected $array_in_prematch_valley_ball_market_id = array(2, 52, 64, 866, 1558);
    protected $array_in_prematch_base_ball_market_id = array(21, 41, 28, 226, 236, 281, 342, 1537, 1538);
    protected $array_in_real_soccer_market_id = array(1, 2, 3, 17, 21, 34, 35, 41, 42, 64, 101, 102, 113, 211); // 6046
    protected $array_in_real_basket_ball_market_id = array(21, 28, 45, 46, 47, 53, 63, 64, 65, 66, 67, 77, 220, 221, 226, 342, 464); // 48242
    protected $array_in_real_valley_ball_market_id = array(2, 21, 45, 46, 47, 52, 64, 65, 66, 67, 866); // 154830
    protected $array_in_real_base_ball_market_id = array(42, 43, 44, 45, 46, 47, 48, 49, 348, 349, 352, 353); // 154914
    protected $array_in_prematch_base_line_soccer_market_id = array(11, 30, 31, 95, 101, 102); // 6046  2, 3,
    protected $array_in_prematch_base_line_basket_ball_market_id = array(220, 221, 153, 155); // 48242 342,28,64,21,53, 77
    protected $array_in_prematch_base_line_valley_ball_market_id = array(1558, 866, 2, 64); // 154830
    protected $array_in_prematch_base_line_base_ball_market_id = array(21); // 154914 342, 28, 281, 236
    protected $array_in_inplay_base_line_soccer_market_id = array(2, 3, 64, 21, 101, 102); // 6046
    //protected $array_in_inplay_base_line_soccer_market_id = array(3, 64, 21, 101, 102); // 6046
    protected $array_in_inplay_base_line_basket_ball_market_id = array(342, 64, 65, 66, 67, 53, 45, 46, 47, 28, 21, 220, 221); // 48242
    protected $array_in_inplay_base_line_valley_ball_market_id = array(866, 2, 64, 65, 66, 67, 21, 45, 46, 47, 21); // 154830
    //protected $array_in_inplay_base_line_base_ball_market_id = array(42, 43, 44, 49, 348, 349, 45, 46, 47, 48, 352, 353); // 154914
    protected $array_in_inplay_base_line_base_ball_market_id = array(45, 46, 47, 48, 352, 353); // 154914
    protected $array_in_refund_rate_market_id = array(4, 6, 7, 9, 16, 17, 70, 98, 99, 390, 427, 1537, 1538);
    public $array_basket_main_sub_market = array(342, 64, 53, 28, 21, 77, 220, 221, 153, 155);
    public $array_velly_main_sub_market = array(1558, 2);
    private $l_logger;
    private $array_base_line_check_market = array(2, 3, 11, 13, 21, 28, 30, 31, 45, 46, 47, 48, 53, 64, 65, 66, 67, 77, 95, 101, 102, 153, 155, 214, 220, 221, 236, 281, 322, 342, 352, 353, 427, 866, 1558);

    public function __construct($logger = null) {
        $this->logger = $logger;
    }

    public function prematch_base_line_markets_except($bet, $sport_id, $market_id, $logger) {
        if (true === in_array($market_id, $this->array_base_line_check_market) && !isset($bet->BaseLine)) {
            return true;
        }

        if (!isset($bet->BaseLine)) {
            $bet->BaseLine = '';
            return false;
        }

        $BaseLine = $bet->BaseLine;
        $arr_bet_base_line = explode(" ", $BaseLine);
        $arr_bet_price = explode(".", $arr_bet_base_line[0]);
        if (isset($arr_bet_price[1])) {
            if ("25" == $arr_bet_price[1] || "75" == $arr_bet_price[1]) {
                return true;
            }
        }

        if (SOCCER === $sport_id && $market_id == 427 && 2.5 != $arr_bet_base_line[0])
            return true;
        if (SOCCER === $sport_id && true == in_array($market_id, $this->array_in_prematch_base_line_soccer_market_id)) {

            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {

                //$this->logger->debug(' array_in_prematch_base_line_soccer_market_id : ' . $arr_bet_base_line[0] . " round : " . $round);
                return true;
            }
        } else if (BASEBALL === $sport_id && true == in_array($market_id, $this->array_in_prematch_base_line_base_ball_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        } else if (VOLLEYBALL === $sport_id && true == in_array($market_id, $this->array_in_prematch_base_line_valley_ball_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        } else if (BASKETBALL === $sport_id && true == in_array($market_id, $this->array_in_prematch_base_line_basket_ball_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        }

        return false;
    }

    public function inplay_base_line_markets_except($bet, $sport_id, $market_id, $logger) {
        if (true === in_array($market_id, $this->array_base_line_check_market) && !isset($bet->BaseLine)) {
            return true;
        }

        if (!isset($bet->BaseLine)) {
            $bet->BaseLine = '';
            return false;
        }
        $arr_bet_base_line = explode(" ", $bet->BaseLine);
        $arr_bet_price = explode(".", $arr_bet_base_line[0]);
        if ("25" == $arr_bet_price[1] || "75" == $arr_bet_price[1]) {
            //$this->logger->debug('::::::::::::::: setFixtureMarketsToInsertBetType3 25_75 : ');
            return true;
        }
        $BaseLine = $bet->BaseLine;

        if (SOCCER === $sport_id && true == in_array($market_id, $this->array_in_inplay_base_line_soccer_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        } else if (BASEBALL === $sport_id && true == in_array($market_id, $this->array_in_inplay_base_line_base_ball_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        } else if (VOLLEYBALL === $sport_id && true == in_array($market_id, $this->array_in_inplay_base_line_valley_ball_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        } else if (BASKETBALL === $sport_id && true == in_array($market_id, $this->array_in_inplay_base_line_basket_ball_market_id)) {
            $round = round($arr_bet_base_line[0]);
            if ($round == $arr_bet_base_line[0]) {
                return true;
            }
        }


        return false;
    }

    public function prematch_markets_except($bet, $sport_id, $market_id, $logger) {
        if (SOCCER === $sport_id && false == in_array($market_id, $this->array_in_prematch_soccer_market_id)) {
            //$this->logger->debug('6046 => id : '.$market->Id);
            return true;
        } else if (BASEBALL === $sport_id && false == in_array($market_id, $this->array_in_prematch_base_ball_market_id)) {
            //$this->logger->debug('154914 => id : '.$market->Id);
            return true;
        } else if (VOLLEYBALL === $sport_id && false == in_array($market_id, $this->array_in_prematch_valley_ball_market_id)) {
            //$this->logger->debug('154830 => id : '.$market->Id);
            return true;
        } else if (BASKETBALL === $sport_id && false == in_array($market_id, $this->array_in_prematch_basket_ball_market_id)) {
            //$this->logger->debug('48242 => id : '.$market->Id);
            return true;
        }
        return false;
    }

    public function inplay_markets_except($bet, $sport_id, $market_id, $logger) {
        if (SOCCER === $sport_id && false == in_array($market_id, $this->array_in_real_soccer_market_id)) {
            return true;
        } else if (BASEBALL === $sport_id && false == in_array($market_id, $this->array_in_real_base_ball_market_id)) {
            //$this->logger->debug('::::::::::::::: start fail array_in_real_base_ball_market_id setFixtureMarketsToInsertBetType3 result day : '.$day);
            return true;
        } else if (VOLLEYBALL === $sport_id && false == in_array($market_id, $this->array_in_real_valley_ball_market_id)) {
            return true;
        } else if (BASKETBALL === $sport_id && false == in_array($market_id, $this->array_in_real_basket_ball_market_id)) {
            //$this->logger->debug('::::::::::::::: start 48242 fail array_in_real_basket_ball_market_id setFixtureMarketsToInsertBetType3 result day : '.$day);
            return true;
        }
        return false;
    }

    public function do_update_bet($sport_id,$league_id, $provider, $fixtureId, $bet, $market, $bet_type, $kstStartDate, $sql, $log_sql, $logger) {
        $arr = [342];
        if (7695961 == $fixtureId && 2 == $bet_type && true == in_array($market->Id, $arr) && 1 == $bet->Status) {
            //if ( true == in_array($bet->Id, [11027129847698967,11027129847698968,13379838197698967,13379838197698968])) {
            $logger->debug('do_update_bet fixture_id : ' . $fixtureId . ' markets_id : ' . $market->Id . ' Line : ' . $bet->Line . ' BaseLine : ' . $bet->BaseLine . ' providerId : ' . $provider->Id . ' betPrice : ' . $bet->Price . ' status :' . $bet->Status . ' settlement :' . $bet->Id);
        }


        $Line = '';
        $Settlement = 0;
        if (isset($bet->Line)) {
            $Line = $bet->Line;
        }
        if (isset($bet->Settlement)) {
            $Settlement = $bet->Settlement;
        }

        $sql[] = '("'
                . $fixtureId . '", '
                . $bet_type . ', '
                . $market->Id . ', "'
                . $provider->Id . '", "'
                . $bet->Id . '", "'
                . $bet->Name . '", "'
                . $bet->Status . '", "'
                . $bet->Price . '", "'
                . $Line . '", "'
                . $bet->BaseLine . '", "'
                . $Settlement . '")';



        $sql_log[] = '("'
                . $bet->Id . '", "'
                . $sport_id . '", "'
                . $league_id . '", "'
                . $fixtureId . '", '
                . $market->Id . ', "'
                . $bet->BaseLine . '", "'
                . $bet->Name . '", "'
                . $Line . '", "'
                . $bet->Price . '", "'
                . 0 . '", "'
                . $bet->Status . '", "'
                . $bet->LastUpdate . '", "'
                . 0 . '", "'
                . $kstStartDate . '", "'
                . '' . '")';

        return [$sql, $sql_log];
    }

    public function do_renew_refund_update_bet($avg_bet,$sport_id ,$league_id, $bet_type, $sql, $sql_log) {

        $provider_id = -100;
        $provider_name = 'avgBet';
        $ids = [7767673132500191];
        foreach ($avg_bet as $key => $a_bet) {

            foreach ($a_bet['Bets'] as $l_bet) {
                $Line = '';
                $Settlement = 0;
                if (isset($l_bet['Line'])) {
                    $Line = $l_bet['Line'];
                }
                if (isset($l_bet['Settlement'])) {
                    $Settlement = $l_bet['Settlement'];
                }

                if ($l_bet['Price'] < 1) {
                    $l_bet['Price'] = 1;
                }
                if (7987106 == $a_bet['FixtureId'] && 342 == $a_bet['markets_id'] && '-7.5 (0-0)'  == $a_bet['BaseLine']) {
                //if (7909858122021 == $l_bet['Id']) {
                    $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_refund_update_bet start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    $this->logger->debug('====> Start do_renew_refund_update_bet FixtureId : ' . $a_bet['FixtureId'] . ' bet_type : '
                            . $a_bet['bet_type'] . ' sport_id : ' . $a_bet['sport_id'] . ' market : '
                            . $a_bet['markets_id'] . ' BaseLine : ' . $a_bet['BaseLine'] . ' bet_price : '
                            . $l_bet['Price'] . ' tempPrice : ' . $l_bet['tempPrice']
                            . ' total_rate : ' . $l_bet['total_rate'] . ' refund_rate : ' . $l_bet['refund_rate'] . ' Name : ' . $l_bet['Name'] . ' bet id : ' . $l_bet['Id']);
                    $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_refund_update_bet end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                }


                $sql[] = '("'
                        . $a_bet['FixtureId'] . '", '
                        . $bet_type . ', '
                        . $a_bet['markets_id'] . ', "'
                        . $provider_id . '", "'
                        . $l_bet['Id'] . '", "'
                        . $l_bet['Name'] . '", "'
                        . $l_bet['Status'] . '", "'
                        . $l_bet['Price'] . '", "'
                        . $Line . '", "'
                        . $a_bet['BaseLine'] . '", "'
                        . $Settlement . '")';


                $sql_log[] = '("'
                        . $l_bet['Id'] . '", "'
                        . $sport_id . '", "'
                        . $league_id . '", "'
                        . $a_bet['FixtureId'] . '", '
                        . $a_bet['markets_id'] . ', "'
                        . $a_bet['BaseLine'] . '", "'
                        . $l_bet['Name'] . '", "'
                        . $Line . '", "'
                        . $l_bet['Price'] . '", "'
                        . $l_bet['tempPrice'] . '", "'
                        . $l_bet['Status'] . '", "'
                        . $l_bet['LastUpdate'] . '", "'
                        . $l_bet['refund_rate'] . '", "'
                        . $l_bet['kstStartDate'] . '", "'
                        . addslashes(json_encode($a_bet['providers'])) . '")';
            }
        }


        return [$sql, $sql_log];
    }

    public function do_renew_update_bet($avg_bet,$sport_id ,$league_id,$bet_type, $sql, $sql_log) {

        $provider_id = -100;
        $provider_name = 'avgBet';

        foreach ($avg_bet as $key => $a_bet) {

            $Line = '';
            $Settlement = 0;
            if (isset($a_bet['Line'])) {
                $Line = $a_bet['Line'];
            }
            if (isset($a_bet['Settlement'])) {
                $Settlement = $a_bet['Settlement'];
            }

            if ($a_bet['Price'] < 1) {
                $a_bet['Price'] = 1;
            }


            $FixtureId = $a_bet['FixtureId'];
            $markets_id = $a_bet['markets_id'];
            $markets_name = $a_bet['markets_name'];
            $Price = $a_bet['Price'];
            $tempPrice = $a_bet['tempPrice'];
            $LastUpdate = $a_bet['LastUpdate'];
            $Id = $a_bet['Id'];
            $Name = $a_bet['Name'];
            $Status = $a_bet['Status'];
            $StartPrice = $a_bet['StartPrice'];
            $BaseLine = $a_bet['BaseLine'];
            $kstStartDate = $a_bet['kstStartDate'];

            //if (7661951 == $FixtureId && 342 == $markets_id && (14.0 == $BaseLine || -14.0 == $BaseLine || 14.5 == $BaseLine || -14.5 == $BaseLine )) {
            if (7987106 == $FixtureId && 342 == $markets_id && '-7.5 (0-0)'  == $BaseLine) {
            //if (7909858122021 == $Id) {
                $this->logger->debug(' do_renew_update_bet : FixtureId :' . $FixtureId . ' markets_id : ' . $markets_id . ' BaseLine :' . $BaseLine . ' betName :' . $Name . ' Id :' . $Id . ' Price :' . $Price);
            }
            $sql[] = '("'
                    . $FixtureId . '", '
                    . $bet_type . ', '
                    . $markets_id . ', "'
                    . $provider_id . '", "'
                    . $Id . '", "'
                    . $Name . '", "'
                    . $Status . '", "'
                    . $Price . '", "'
                    . $Line . '", "'
                    . $BaseLine . '", "'
                    . $Settlement . '")';

            $sql_log[] = '("'
                    . $Id . '", "'
                    . $sport_id . '", '
                    . $league_id . ', '
                    . $FixtureId . ', '
                    . $markets_id . ', "'
                    . $BaseLine . '", "'
                    . $Name . '", "'
                    . $Line . '", "'
                    . $Price . '", "'
                    . $tempPrice . '", "'
                    . $Status . '", "'
                    . $LastUpdate . '", "'
                    . $a_bet['refund_rate'] . '", "'
                    . $kstStartDate . '", "'
                    . addslashes(json_encode($a_bet['providers'])) . '")';
        }


        return [$sql, $sql_log];
    }

    public function do_update_bet_query($sql, $log_type, $lSportsBetModel, $logger) {
        try {
            $_s = array_chunk($sql, 5000);
            $query_string = '';
            foreach ($_s as $key => $data) {
                $query_string = 'INSERT INTO `lsports_bet` ('
                        . 'fixture_id, '
                        . 'bet_type, '
                        . 'markets_id, '
                        . 'providers_id, '
                        . 'bet_id, '
                        . 'bet_name, '
                        . 'bet_status, '
                        . 'bet_price, '
                        . 'bet_line, '
                        . 'bet_base_line, '
                        . 'bet_settlement
                        ) VALUES '
                        . implode(',', $data)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'bet_name = VALUES(bet_name), '
                        . 'bet_status = VALUES(bet_status), '
                        . 'bet_line = VALUES(bet_line), '
                        . 'bet_base_line = VALUES(bet_base_line), '
                        . 'bet_settlement = VALUES(bet_settlement), '
                        . 'bet_price = VALUES(bet_price)'
                    ;

                $lSportsBetModel->db->query($query_string);
            }
        } catch (\mysqli_sql_exception $e) {
            $logger->error('do_update_bet_query [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('do_update_bet_query [MYSQL EXCEPTION] message query : ' . $lSportsBetModel->getLastQuery());
        } 
    }
   
    public function do_update_fix_live_score($event, $display_status, $live_status, $kstStartDate, $bet_type, $sql_fixture, $logger) {
        // 경기스코어
        $value1 = 0;
        $value2 = 0;
        $live_time = '';
        $live_current_period = '';
        if (isset($event->Livescore->Scoreboard->Results)) {
            $value1 = $event->Livescore->Scoreboard->Results[0]->Value;
            $value2 = $event->Livescore->Scoreboard->Results[1]->Value;
        }

        $Periods = true == isset($event->Livescore->Periods) ? $event->Livescore->Periods : [];

        foreach ($Periods as $key => $Period) {
            //$logger->error('::::::::::::::: do_update_fix_live_score bf Incidents: ' . json_encode($Periods[$key]->Incidents));
            unset($Periods[$key]->Incidents);

            //$logger->error('::::::::::::::: do_update_fix_live_score af Incidents: ' . json_encode($Periods[$key]->Incidents));
        }

        if (true == isset($Periods) && false == empty($Periods)) {
            $event->Livescore->Periods = $Periods;
            //$logger->error('::::::::::::::: do_update_fix_live_score set Periods: ');
        }

        $insertSql = '("'
                . $event->FixtureId . '", "'
                . $bet_type . '", "'
                . $live_status . '", "'
                . $display_status . '", "'
                . $kstStartDate . '", "'
                . $value1 . '", "'
                . $value2 . '", \''
                . addslashes(json_encode($event->Livescore)) . '\')';
        array_push($sql_fixture, $insertSql);
        return $sql_fixture;
    }

    public function do_update_fix_live_score_query($sql, $lSportsBetModel, $logger) {
        try {
            $_s = array_chunk($sql, 4000);
            $query_string = '';
            foreach ($_s as $key => $data) {
                $query_string = 'INSERT INTO `lsports_fixtures` ('
                        . 'fixture_id, '
                        . 'bet_type, '
                        . 'fixture_status, '
                        . 'display_status, '
                        . 'fixture_start_date, '
                        . 'live_results_p1, '
                        . 'live_results_p2, '
                        . 'livescore) VALUES '
                        . implode(',', $data)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'livescore = VALUES(livescore), '
                        . 'fixture_status = VALUES(fixture_status), '
                        . 'live_results_p1 = VALUES(live_results_p1), '
                        . 'live_results_p2 = VALUES(live_results_p2), '
                        . 'fixture_start_date = VALUES(fixture_start_date), '
                        . 'display_status = VALUES(display_status)';
                $lSportsBetModel->db->query($query_string);
            }

            $str_sql = "UPDATE lsports_fixtures SET display_status = 2 WHERE display_status = 1 AND bet_type = 1 AND fixture_start_date < now()";
            $lSportsBetModel->db->query($str_sql);
            //$str_sql = " DELETE FROM lsports_fixtures WHERE bet_type = 1 AND fixture_sport_id = 0;";
            //$lSportsBetModel->db->query($str_sql);
        } catch (\mysqli_sql_exception $e) {
            $logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        }
    }

    public function do_update_participants($fixture, $participantSql, $logger) {
        // 팀이름 업데이트 1
        $insertParticipantSql = '("'
                . $fixture->Participants[0]->Id . '", "'
                . $fixture->Sport->Id . '", "'
                . $fixture->Location->Id . '", "'
                . $fixture->League->Id . '", "'
                . addslashes($fixture->Participants[0]->Name) . '")';
        $participantSql[$fixture->Participants[0]->Id . '_'] = $insertParticipantSql;
        // 팀이름 업데이트 2
        $insertParticipantSql = '("'
                . $fixture->Participants[1]->Id . '", "'
                . $fixture->Sport->Id . '", "'
                . $fixture->Location->Id . '", "'
                . $fixture->League->Id . '", "'
                . addslashes($fixture->Participants[1]->Name) . '")';
        $participantSql[$fixture->Participants[1]->Id . '_'] = $insertParticipantSql;

        return $participantSql;
    }

    public function do_update_participants_query($participantSql, $lSportsFixturesModel, $logger) {
        try {
            $lSportsFixturesModel->db->query(
                    'INSERT INTO `lsports_participant` ('
                    . 'fp_id, '
                    . 'sports_id, '
                    . 'location_id, '
                    . 'league_id, '
                    . 'team_name) VALUES '
                    . implode(',', $participantSql)
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'sports_id = VALUES(sports_id), '
                    . 'location_id = VALUES(location_id), '
                    . 'league_id = VALUES(league_id), '
                    . 'team_name = VALUES(team_name)');
        } catch (\mysqli_sql_exception $e) {
            $logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        }
    }

    public function do_update_fix($row, $sql, $bet_type, $kstStartDate, $display_status, $logger) {
        //$display_status = 100;
        $break_dt = date("Y-m-d H:i:s");
        $insertSql = '("'
                . $row->FixtureId . '", '
                . $bet_type . ', '
                . $row->Fixture->Sport->Id . ', "'
                . $row->Fixture->Location->Id . '", "'
                . $row->Fixture->League->Id . '", "'
                . $kstStartDate . '", "'
                . $row->Fixture->Status . '", "'
                . $row->Fixture->Participants[0]->Id . '", "'
                . $row->Fixture->Participants[1]->Id . '", "'
                . $display_status . '", "'
                . $break_dt . '")';
        $sql[] = $insertSql;
        return $sql;
    }

    public function do_update_fix_query($sql, $lSportsFixturesModel, $logger) {
        try {
            $_s = array_chunk($sql, 4000);
            //$_s = array_chunk($sql, 1);
            $query_str = '';
            foreach ($_s as $key => $data) {
                $query_str = 'INSERT INTO `lsports_fixtures` ('
                        . 'fixture_id, '
                        . 'bet_type, '
                        . 'fixture_sport_id, '
                        . 'fixture_location_id, '
                        . 'fixture_league_id, '
                        . 'fixture_start_date, '
                        . 'fixture_status, '
                        . 'fixture_participants_1_id, '
                        . 'fixture_participants_2_id, '
                        . 'display_status, '
                        . 'break_dt) VALUES '
                        . implode(',', $data)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'fixture_sport_id = VALUES(fixture_sport_id), '
                        . 'fixture_location_id = VALUES(fixture_location_id), '
                        . 'fixture_league_id = VALUES(fixture_league_id), '
                        . 'fixture_start_date = VALUES(fixture_start_date), '
                        . 'fixture_status = VALUES(fixture_status), '
                        . 'fixture_participants_1_id = VALUES(fixture_participants_1_id), '
                        . 'fixture_participants_2_id = VALUES(fixture_participants_2_id), '
                        . 'display_status = VALUES(display_status), '
                        . 'break_dt = VALUES(break_dt)';
                $lSportsFixturesModel->db->query($query_str);
            }
        } catch (\mysqli_sql_exception $e) {
            $logger->error('[MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');

            $logger->error('[MYSQL EXCEPTION] message (query_str) : ' . $query_str);
        }
    }

    public function do_update_fix_read_query($sql, $lSportsFixturesModel, $logger) {
        try {
            $_s = array_chunk($sql, 4000);
            $query_str = '';
            foreach ($_s as $key => $data) {
                $query_str = 'INSERT INTO `lsports_fixtures` ('
                        . 'fixture_id, '
                        . 'bet_type, '
                        . 'fixture_sport_id, '
                        . 'fixture_sport_name, '
                        . 'fixture_location_id, '
                        . 'fixture_location_name, '
                        . 'fixture_league_id, '
                        . 'fixture_league_name, '
                        . 'fixture_start_date, '
                        . 'fixture_start_date_utc, '
                        . 'fixture_last_update, '
                        . 'fixture_status, '
                        . 'fixture_participants_1_id, '
                        . 'fixture_participants_1_name, '
                        . 'fixture_participants_2_id, '
                        . 'fixture_participants_2_name,'
                        . 'display_status) VALUES '
                        . implode(',', $data)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'fixture_sport_id = VALUES(fixture_sport_id), '
                        . 'fixture_sport_name = VALUES(fixture_sport_name), '
                        . 'fixture_location_id = VALUES(fixture_location_id), '
                        . 'fixture_location_name = VALUES(fixture_location_name), '
                        . 'fixture_league_id = VALUES(fixture_league_id), '
                        . 'fixture_league_name = VALUES(fixture_league_name), '
                        . 'fixture_start_date = VALUES(fixture_start_date), '
                        . 'fixture_start_date_utc = VALUES(fixture_start_date_utc), '
                        . 'fixture_last_update = VALUES(fixture_last_update), '
                        . 'fixture_status = VALUES(fixture_status), '
                        . 'fixture_participants_1_id = VALUES(fixture_participants_1_id), '
                        . 'fixture_participants_1_name = VALUES(fixture_participants_1_name), '
                        . 'fixture_participants_2_id = VALUES(fixture_participants_2_id), '
                        . 'fixture_participants_2_name = VALUES(fixture_participants_2_name), '
                        . 'display_status = VALUES(display_status)';
                $lSportsFixturesModel->db->query($query_str);

                $query_str = 'INSERT INTO `lsports_fixtures_read` ('
                        . 'fixture_id, '
                        . 'bet_type, '
                        . 'fixture_sport_id, '
                        . 'fixture_sport_name, '
                        . 'fixture_location_id, '
                        . 'fixture_location_name, '
                        . 'fixture_league_id, '
                        . 'fixture_league_name, '
                        . 'fixture_start_date, '
                        . 'fixture_start_date_utc, '
                        . 'fixture_last_update, '
                        . 'fixture_status, '
                        . 'fixture_participants_1_id, '
                        . 'fixture_participants_1_name, '
                        . 'fixture_participants_2_id, '
                        . 'fixture_participants_2_name,'
                        . 'display_status) VALUES '
                        . implode(',', $data)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'fixture_sport_id = VALUES(fixture_sport_id), '
                        . 'fixture_sport_name = VALUES(fixture_sport_name), '
                        . 'fixture_location_id = VALUES(fixture_location_id), '
                        . 'fixture_location_name = VALUES(fixture_location_name), '
                        . 'fixture_league_id = VALUES(fixture_league_id), '
                        . 'fixture_league_name = VALUES(fixture_league_name), '
                        . 'fixture_start_date = VALUES(fixture_start_date), '
                        . 'fixture_start_date_utc = VALUES(fixture_start_date_utc), '
                        . 'fixture_last_update = VALUES(fixture_last_update), '
                        . 'fixture_status = VALUES(fixture_status), '
                        . 'fixture_participants_1_id = VALUES(fixture_participants_1_id), '
                        . 'fixture_participants_1_name = VALUES(fixture_participants_1_name), '
                        . 'fixture_participants_2_id = VALUES(fixture_participants_2_id), '
                        . 'fixture_participants_2_name = VALUES(fixture_participants_2_name), '
                        . 'display_status = VALUES(display_status)';
                $lSportsFixturesModel->db->query($query_str);
            }
        } catch (\mysqli_sql_exception $e) {
            $logger->error('[MYSQL EXCEPTION] message (code) do_update_fix_read_query : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        } 
    }

    public function do_update_fix_score($row, $sql, $bet_type, $kstStartDate, $display_status, $logger) {

        $Periods = true == isset($row->Livescore->Periods) ? $row->Livescore->Periods : [];
        foreach ($Periods as $key => $Period) {
            unset($Periods[$key]->Incidents); // = null;
        }

        if (true == isset($Periods) && false == empty($Periods)) {
            $row->Livescore->Periods = $Periods;
        }


        $livescore = addslashes(json_encode($row->Livescore));
        $live_results_p1 = isset($row->Livescore->Scoreboard->Results[0]->Value) ? $row->Livescore->Scoreboard->Results[0]->Value : 0;
        $live_results_p2 = isset($row->Livescore->Scoreboard->Results[1]->Value) ? $row->Livescore->Scoreboard->Results[1]->Value : 0;

        $insertSql = "('"
                . $row->FixtureId . "', '"
                . $bet_type . "', '"
                . $row->Fixture->Sport->Id . "', '"
                . $row->Fixture->Location->Id . "', '"
                . $row->Fixture->League->Id . "', '"
                . $kstStartDate . "', '"
                . $row->Fixture->Status . "', '"
                . $row->Fixture->Participants[0]->Id . "', '"
                . $row->Fixture->Participants[1]->Id . "', '"
                . $livescore . "', '"
                . $live_results_p1 . "', '"
                . $live_results_p2 . "', '"
                . $display_status . "')";
        array_push($sql, $insertSql);
        return $sql;
    }

    public function do_update_fix_score_query($sql, $lSportsFixturesModel, $logger) {
        try {
            $_s = array_chunk($sql, 1000);
            foreach ($_s as $key => $data) {
                $query_str = 'INSERT INTO `lsports_fixtures` ('
                        . 'fixture_id, '
                        . 'bet_type, '
                        . 'fixture_sport_id, '
                        . 'fixture_location_id, '
                        . 'fixture_league_id, '
                        . 'fixture_start_date, '
                        . 'fixture_status, '
                        . 'fixture_participants_1_id, '
                        . 'fixture_participants_2_id, '
                        . 'livescore, '
                        . 'live_results_p1, '
                        . 'live_results_p2, '
                        . 'display_status) VALUES '
                        . implode(',', $data)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'fixture_sport_id = VALUES(fixture_sport_id), '
                        . 'fixture_location_id = VALUES(fixture_location_id), '
                        . 'fixture_league_id = VALUES(fixture_league_id), '
                        . 'fixture_start_date = VALUES(fixture_start_date), '
                        . 'fixture_status = VALUES(fixture_status), '
                        . 'fixture_participants_1_id = VALUES(fixture_participants_1_id), '
                        . 'fixture_participants_2_id = VALUES(fixture_participants_2_id), '
                        . 'livescore = VALUES(livescore), '
                        . 'live_results_p1 = VALUES(live_results_p1), '
                        . 'live_results_p2 = VALUES(live_results_p2),'
                        . 'display_status = VALUES(display_status)';
                $lSportsFixturesModel->db->query($query_str);
            }
            //$str_sql = "UPDATE lsports_fixtures SET display_status = 3 WHERE bet_type = 2 AND display_status = 1 AND fixture_start_date < now();";
            //$lSportsFixturesModel->db->query($str_sql);
        } catch (\mysqli_sql_exception $e) {
            $logger->error('do_update_fix_score_query [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error($query_str);
        } 
    }

    public function get_market_data($bet_type, $lSportsFixturesModel, $logger) {
        $array_book_maker = [];
        $str_sql = " SELECT markets.id,markets.sport_id,markets.main_book_maker,sub_book_maker
                    ,markets.limit_bet_price,markets.max_bet_price
                    FROM lsports_markets as markets
                    WHERE markets.bet_group = $bet_type AND markets.is_delete = 0";

        $arr_markets_result = $lSportsFixturesModel->db->query($str_sql)->getResultArray();
        foreach ($arr_markets_result as $market) {
            $array_book_maker[$market['sport_id']][$market['id']] = array('main_book_maker' => $market['main_book_maker'], 'sub_book_maker' => $market['sub_book_maker']
                , 'limit_bet_price' => $market['limit_bet_price'], 'max_bet_price' => $market['max_bet_price'], 'mk_input_refund_rate' => $market['mk_input_refund_rate']);
        }

        return $array_book_maker;
    }

    public function get_sp_lg_refund_data($fixtureId, $bet_type, $lSportsFixturesModel, $logger) {
        $str_sql = " SELECT IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status
                                ,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date
                                ,fix.fixture_sport_id,fix.fixture_last_update,
                                sports.input_refund_rate as sp_input_refund_rate,
                                leagues.input_refund_rate as lg_input_refund_rate
                                FROM lsports_fixtures as fix
                                    LEFT JOIN   lsports_sports as sports
                                ON   fix.fixture_sport_id = sports.id   
                                    LEFT JOIN   lsports_leagues as leagues
                                ON   fix.fixture_league_id = leagues.id   
                            WHERE fix.fixture_id = $fixtureId AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) IN (1,2,3) AND fix.bet_type = $bet_type
                                AND sports.is_use = 1 AND sports.bet_type = $bet_type AND leagues.bet_type = $bet_type";
        if (1 == $bet_type) {
            $str_sql .= " AND leagues.is_use = 1 ";
        }

        //if(7230419 == $fixtureId){
        //    $logger->debug("get_sp_lg_refund_data sql: ".$str_sql);
        //}
        $arr_result = $lSportsFixturesModel->db->query($str_sql)->getResultArray();
        return $arr_result;
    }

    public function get_renew_sp_lg_deduction_refund_data($fixtureId, $bet_type, $lSportsFixturesModel, $logger) {
        
        $str_sql = " SELECT IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status
                                ,IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date
                                ,fix.fixture_sport_id,
                                sports.deduction_refund_rate as sp_deduction_refund_rate,
                                leagues.deduction_refund_rate as lg_deduction_refund_rate
                                FROM lsports_fixtures as fix
                                    LEFT JOIN   lsports_sports as sports
                                ON   fix.fixture_sport_id = sports.id   
                                    LEFT JOIN   lsports_leagues as leagues
                                ON   fix.fixture_league_id = leagues.id   
                            WHERE fix.fixture_id = $fixtureId AND fix.bet_type = $bet_type
                                AND sports.is_use = 1 AND sports.bet_type = $bet_type AND leagues.bet_type = $bet_type";
        if (1 == $bet_type) {
            $str_sql .= " AND leagues.is_use = 1 ";
        }

        $arr_result = $lSportsFixturesModel->db->query($str_sql)->getResultArray();
        return $arr_result;
    }

    public function do_min_odd_data($event, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $arr_sql, $logger) {
        foreach ($market->Providers as $provider) {

            $returnBets = $this->do_merge_data($event, $provider, $sport_id, $market, $bet_type, $logger);
            if (1 == $display_status && 0 < $refund_rate && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                $arr_sql = $this->do_cal_deduction_refund_rate($event, $provider, $returnBets, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $arr_sql, $logger);
            } else {
                foreach ($returnBets as $bet_key => $mergeBet) {
                    foreach ($mergeBet['Bets'] as $key => $bet) {

                        $arr_sql[] = '("'
                                . $event->FixtureId . '", '
                                . $bet_type . ', '
                                . $market->Id . ', "'
                                . $market->Name . '", "'
                                . $bet['Price'] . '", "'
                                . $mergeBet['providerId'] . '", "'
                                . $mergeBet['providerName'] . '", "'
                                . $mergeBet['providerLastUpdate'] . '", "'
                                . $bet['Id'] . '", "'
                                . $bet['Name'] . '", "'
                                . $bet['Status'] . '", "'
                                . $bet['StartPrice'] . '", "'
                                . $bet['Price'] . '", "'
                                . $bet['Line'] . '", "'
                                . $bet['BaseLine'] . '", "'
                                . $bet['Settlement'] . '", "'
                                . $bet['LastUpdate'] . '", "'
                                . $bet['Status'] . '", "now()", "'
                                . $bet['Status'] . '", "'
                                . $kstStartDate . '")';
                    }
                }
            }
        }
        return $arr_sql;
    }

    public function do_refund_rate($event, $provider, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $arr_sql, $log_sql, $logger) {
        $returnBets = $this->do_merge_data($event, $provider, $sport_id, $market, $bet_type, $logger);
        $arr_sql = $this->do_cal_deduction_refund_rate($event, $provider, $returnBets, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $arr_sql, $log_sql, $logger);
        return $arr_sql;
    }

    private function do_merge_data($event, $provider, $sport_id, $market, $bet_type, $logger) {
        $returnBets = [];
        $arr = [28];
        foreach ($provider->Bets as $bet) {

            if (7695961 == $event->FixtureId && 342 == $market->Id && 1 == $bet->Status) {
                $logger->debug('do_merge_data fixture_id : ' . $event->FixtureId . ' markets_id : ' . $market->Id . ' Line : ' . $bet->Line . ' BaseLine : ' . $bet->BaseLine . ' providerId : ' . $provider->Id . ' betPrice : ' . $bet->Price . ' status :' . $bet->Status . ' settlement :' . $bet->Settlement);
            }

            if (0 == $bet->Price)
                continue;
            if (1 == $bet_type && true == $this->prematch_base_line_markets_except($bet, $sport_id, $market->Id, $logger))
                continue;
            if (2 == $bet_type && true == $this->inplay_base_line_markets_except($bet, $sport_id, $market->Id, $logger))
                continue;

            if (isset($returnBets[$market->Id . '_' . $bet->BaseLine])) {
                $mergeBet = $returnBets[$market->Id . '_' . $bet->BaseLine];
            } else {
                $mergeBet = [
                    'markets_id' => $market->Id,
                    'BaseLine' => $bet->BaseLine,
                    'providerId' => $provider->Id,
                    'providerName' => $provider->Name,
                    'providerLastUpdate' => $provider->LastUpdate,
                    'limit_bet_price' => $bet->Price,
                    'Bets' => []
                ];
            }
            $mergeBet['Bets'][] = array('Id' => $bet->Id, 'Name' => $bet->Name, 'StartPrice' => $bet->StartPrice
                , 'Price' => $bet->Price, 'Line' => isset($bet->Line) ? $bet->Line : ''
                , 'BaseLine' => $bet->BaseLine, 'Settlement' => isset($bet->Settlement) ? $bet->Settlement : 0, 'Status' => $bet->Status
                , 'ex_refund_rate' => 0, 'total_rate' => 0, 'LastUpdate' => $bet->LastUpdate);

            $returnBets[$market->Id . '_' . $bet->BaseLine] = $mergeBet;
        }

        return $returnBets;
    }

    // 차감 환수율
    private function do_cal_deduction_refund_rate($event, $provider, $returnBets, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $arr_sql, $logger) {
        foreach ($returnBets as $betData) {
            $total_rate = 0;
            foreach ($betData['Bets'] as $key => $bet) {
                $ex_refund_rate = (100 / $bet['Price']);

                $betData['Bets'][$key]['ex_refund_rate'] = $ex_refund_rate;
                $total_rate = $total_rate + $ex_refund_rate; // 배당별 확률 계산
            }

            $total_rate = sprintf('%0.2f', $total_rate);
            $total_rate = sprintf('%0.2f', (100 / ($total_rate)) * 100); // 전체 승률 
            $bf_total_rate = $total_rate;
            $af_total_rate = $bf_total_rate - $refund_rate;
            // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A
            foreach ($betData['Bets'] as $key => $bet) {
                $betData['Bets'][$key]['tempPrice'] = $betData['Bets'][$key]['Price'];
                $ex_refund_rate = sprintf('%0.2f', $bf_total_rate / $betData['Bets'][$key]['Price']);
                $betData['Bets'][$key]['ex_refund_rate'] = $ex_refund_rate;
                $betData['Bets'][$key]['Price'] = ($af_total_rate / $ex_refund_rate);
                $betData['Bets'][$key]['Price'] = sprintf('%0.2f', $betData['Bets'][$key]['Price']); // 전체 승률 
                $betData['Bets'][$key]['total_rate'] = $total_rate;

                $arr_sql[] = '("'
                        . $event->FixtureId . '", '
                        . $bet_type . ', '
                        . $market->Id . ', "'
                        //. $market->Name . '", "'
                        //. $betData['Bets'][$key]['Price'] . '", "'
                        . $provider->Id . '", "'
                        //. $provider->Name . '", "'
                        //. $provider->LastUpdate . '", "'
                        . $betData['Bets'][$key]['Id'] . '", "'
                        . $betData['Bets'][$key]['Name'] . '", "'
                        . $betData['Bets'][$key]['Status'] . '", "'
                        //. $betData['Bets'][$key]['StartPrice'] . '", "'
                        . $betData['Bets'][$key]['Price'] . '", "'
                        . $betData['Bets'][$key]['Line'] . '", "'
                        . $betData['Bets'][$key]['BaseLine'] . '", "'
                        //. $betData['Bets'][$key]['Settlement'] . '", "'
                        //. $betData['Bets'][$key]['LastUpdate'] . '", "'
                        //. $betData['Bets'][$key]['Status'] . '", "now()", "'
                        //. $betData['Bets'][$key]['Status'] . '", "'
                        . $betData['Bets'][$key]['Settlement'] . '")';

                $sql_log[] = '("'
                        . $betData['Bets'][$key]['Id'] . '", "'
                        . $event->Fixture->Sport->Id . '", "'
                        . $event->Fixture->League->Id . '", "'
                        . $event->FixtureId . '", '
                        . $market->Id . ', "'
                        . $betData['Bets'][$key]['BaseLine'] . '", "'
                        . $betData['Bets'][$key]['Name'] . '", "'
                        . $betData['Bets'][$key]['Line'] . '", "'
                        . $betData['Bets'][$key]['Price'] . '", "'
                        . $betData['Bets'][$key]['tempPrice'] . '", "'
                        . $betData['Bets'][$key]['Status'] . '", "'
                        . $betData['Bets'][$key]['LastUpdate'] . '", "'
                        . $refund_rate . '", "'
                        . $kstStartDate . '", "'
                        . '' . '")';
                if (7677146 == $event->FixtureId && 226 == $market->Id) {
                    $logger->debug('============================= http oodservice do_cal_deduction_refund_rate start ======================================');
                    $logger->debug('league id : ' . $event->Fixture->League->Id . ' bf_total_rate :' . $bf_total_rate . ' af_total_rate :' . $af_total_rate . ' ex_refund_rate :' . $betData['Bets'][$key]['ex_refund_rate']);
                    $logger->debug('=> Start rf Fixid :' . $event->FixtureId . ' type :' . $bet_type . ' sp_id :' . $sport_id . ' market :' . $market->Id . ' BaseLine :' . $betData['Bets'][$key]['BaseLine'] . ' bet_price :'
                            . $betData['Bets'][$key]['Price'] . ' tempPrice :' . $betData['Bets'][$key]['tempPrice'] . ' total_rate :' . $betData['Bets'][$key]['total_rate'] . ' refund_rate :' . $refund_rate . ' id : ' . $betData['Bets'][$key]['Id']);
                    $logger->debug('============================= http oodservice do_cal_deduction_refund_rate end ======================================');
                }
            }
        }

        return [$arr_sql,$sql_log];
    }

    // 기본 환수율 
    private function do_cal_refund_rate($event, $provider, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $sql, $logger) {
        $returnBets = [];
        foreach ($provider->Bets as $bet) {
            if (0 == $bet->Price)
                continue;
            if (1 == $bet_type && true == $this->prematch_base_line_markets_except($bet, $sport_id, $market->Id, $logger))
                continue;

            if (2 == $bet_type && true == $this->inplay_base_line_markets_except($bet, $sport_id, $market->Id, $logger))
                continue;

            if (isset($returnBets[$market->Id . '_' . $bet->BaseLine])) {
                $mergeBet = $returnBets[$market->Id . '_' . $bet->BaseLine];
            } else {
                $mergeBet = [
                    'markets_id' => $market->Id,
                    'Bets' => []
                ];
            }

            $mergeBet['Bets'][] = array('Id' => $bet->Id, 'Name' => $bet->Name, 'Status' => $bet->Status, 'StartPrice' => $bet->StartPrice
                , 'Price' => $bet->Price, 'Line' => isset($bet->Line) ? $bet->Line : ''
                , 'BaseLine' => $bet->BaseLine, 'Settlement' => isset($bet->Settlement) ? $bet->Settlement : 0, 'Status' => $bet->Status
                , 'ex_refund_rate' => 0, 'total_rate' => 0, 'LastUpdate' => $bet->LastUpdate);

            $returnBets[$market->Id . '_' . $bet->BaseLine] = $mergeBet;
        }

        foreach ($returnBets as $betData) {
            $total_rate = 0;
            foreach ($betData['Bets'] as $key => $bet) {
                $ex_refund_rate = (1 / $bet['Price']) * 100;

                $betData['Bets'][$key]['ex_refund_rate'] = $ex_refund_rate;
                $total_rate = $total_rate + $ex_refund_rate; // 배당별 확률 계산
            }

            $total_rate = sprintf('%0.2f', (1 / $total_rate) * 100); // 전체 승률 
            // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A
            foreach ($betData['Bets'] as $key => $bet) {
                $betData['Bets'][$key]['tempPrice'] = $betData['Bets'][$key]['Price'];
                $betData['Bets'][$key]['Price'] = ($betData['Bets'][$key]['Price'] / $total_rate) * $refund_rate;
                $betData['Bets'][$key]['Price'] = sprintf('%0.2f', $betData['Bets'][$key]['Price']); // 전체 승률 
                $betData['Bets'][$key]['total_rate'] = $total_rate;

                $sql[] = '("'
                        . $event->FixtureId . '", '
                        . $bet_type . ', '
                        . $market->Id . ', "'
                        . $market->Name . '", "'
                        . $betData['Bets'][$key]['Price'] . '", "'
                        . $provider->Id . '", "'
                        . $provider->Name . '", "'
                        . $provider->LastUpdate . '", "'
                        . $betData['Bets'][$key]['Id'] . '", "'
                        . $betData['Bets'][$key]['Name'] . '", "'
                        . $betData['Bets'][$key]['Status'] . '", "'
                        . $betData['Bets'][$key]['StartPrice'] . '", "'
                        . $betData['Bets'][$key]['Price'] . '", "'
                        . $betData['Bets'][$key]['Line'] . '", "'
                        . $betData['Bets'][$key]['BaseLine'] . '", "'
                        . $betData['Bets'][$key]['Settlement'] . '", "'
                        . $betData['Bets'][$key]['LastUpdate'] . '", "'
                        . $betData['Bets'][$key]['Status'] . '", "now()", "'
                        . $betData['Bets'][$key]['Status'] . '", "'
                        . $kstStartDate . '")';
                $logger->debug('====> Start refund_rate FixtureId : ' . $event->FixtureId . ' bet_type : ' . $bet_type . ' sport_id : ' . $sport_id . ' market : ' . $market->Id . ' BaseLine : ' . $betData['Bets'][$key]['BaseLine'] . ' bet_price : '
                        . $betData['Bets'][$key]['Price'] . ' tempPrice : ' . $betData['Bets'][$key]['tempPrice'] . ' total_rate : ' . $betData['Bets'][$key]['total_rate'] . ' refund_rate : ' . $refund_rate . ' id : ' . $betData['Bets'][$key]['Id']);
            }
        }

        return $sql;
    }

    // 평균배당의 bet_id 값을 생성한다 
    private function create_id($event, $bet_type, $market, $bet, $bfind) {
        $id = $event->FixtureId . $bet_type . $market->Id;

        if (7755162 == $event->FixtureId && 64 == $market->Id && '2.5 (0-0)' == $base_line) {
            $this->logger->debug('create_id FixtureId start ==>' . $event->FixtureId . ' id ==>> ' . $id);
        }

        $base_line = $bet->BaseLine;

        if (isset($base_line) && isset($bet->Line)) {
            $arr_bet_line = explode(' ', $base_line);
            //if (isset($arr_bet_line) && 0 < count($arr_bet_line)) {
            //  $str_base_line = $arr_bet_line[0];
            //} else {
            $str_base_line = $base_line;
            //}
            // 문자열에서 정수만 추출한다.
            $int = preg_replace('/[^0-9]/', '', $str_base_line);

            if (7755162 == $event->FixtureId && 64 == $market->Id && '2.5 (0-0)' == $base_line) {
                $this->logger->debug('create_id $str_base_line 1 FixtureId ==>' . $event->FixtureId . ' preg_replace str_base_line ==>> ' . $str_base_line . ' int ==>> ' . $int . ' base_line ==> ' . $bet->BaseLine);
            }

            //$this->logger->debug('create_id market name ==>'.$market->Name.' BaseLine ==>> '.$base_line.' preg_replace str_base_line ==>> '.$str_base_line.' int ==>> '.$int.' Line'.$bet->Line);
            $bf_int = $int;

            // line 값이 음수이면 1을 붙여준다.
            if ($arr_bet_line[0] < 0) {
                $int = $int . '1';

                if (7755162 == $event->FixtureId && 64 == $market->Id && '2.5 (0-0)' == $base_line) {
                    $this->logger->debug('create_id $str_base_line 2 FixtureId ==>' . $event->FixtureId . ' preg_replace str_base_line ==>> ' . $str_base_line . ' int ==>> ' . $int . ' base_line ==> ' . $bet->BaseLine);
                }
            }

            $id = $id . $int;

            if (true == $bfind) {
                $arr_bet_line = explode(' ', $bet->Line);
                if ($arr_bet_line[0] < 0) {
                    $id = $id . '9';
                    if (7755162 == $event->FixtureId && 64 == $market->Id && '2.5 (0-0)' == $base_line) {
                        $this->logger->debug('create_id $arr_bet_line 3 FixtureId ==>' . $event->FixtureId . ' preg_replace str_base_line ==>> ' . $str_base_line . ' id ==>> ' . $id . ' Line' . $bet->Line);
                    }
                }
            }
        }

        $bet_name_id = StatusUtil::getBetId($bet->Name, $market->Id);
        $id = $id . $bet_name_id;
        //if (7732361 == $event->FixtureId && 21 == $market->Id && 2.5 == $base_line) {
        if (7755162 == $event->FixtureId && 64 == $market->Id && '2.5 (0-0)' == $base_line) {
            $this->logger->debug('create_id End FixtureId ==>' . $event->FixtureId . ' bet_name ==>' . $bet->Name . ' id ==>> ' . $id);
        }
        return $id;
    }

    private function new_bet_data($id, $event, $sport_id, $bet_type, $market, $bet, $kstStartDate) {
        $mergeBet = [
            'Id' => $id,
            'FixtureId' => $event->FixtureId,
            'sport_id' => $sport_id,
            'bet_type' => $bet_type,
            'markets_id' => $market->Id,
            'markets_name' => $market->Name,
            'BaseLine' => $bet->BaseLine,
            'Line' => isset($bet->Line) ? $bet->Line : '',
            'LastUpdate' => $bet->LastUpdate,
            'providerId' => -100,
            'providerName' => 'avgBet',
            'Name' => $bet->Name,
            'totalStartPriceSum' => $bet->StartPrice,
            'StartPrice' => 0,
            'totalPriceSum' => $bet->Price,
            'Price' => 0,
            'count' => 1,
            'Status' => $bet->Status,
            'Settlement' => $bet->Settlement,
            'kstStartDate' => $kstStartDate,
            'provider' => []
        ];

        return $mergeBet;
    }

    public function do_average_dividend($event, $sport_id, $market, $bet_type, $kstStartDate, $array_average_provider) {

        $returnBets = [];
        // 핸디캡 경기들은 baseline에 문자열이 포함된다.
        $arr_handi = array(3, 53, 64, 95, 281, 342, 866, 1558);

        $bfind = false;
        if (true == in_array($market->Id, $arr_handi)) {
            $bfind = true;
        }

        $except_bet = [];
        foreach ($market->Providers as $provider) {
            foreach ($provider->Bets as $bet) {
                if (isset($array_average_provider[$sport_id])) {
                    if (true == in_array($provider->Id, $array_average_provider[$sport_id])) {
                        continue;
                    }
                }

                $bet->provider_id = $provider->Id;

                if (0 == $bet->Price) {
                    continue;
                }

                if (1 == $bet_type && true == $this->prematch_base_line_markets_except($bet, $sport_id, $market->Id, $logger))
                    continue;

                if (2 == $bet->Status) {
                    $except_bet[$market->Id . '_' . $bet->BaseLine . '_' . $bet->Name] = $bet;
                    continue;
                }
                
                if (7987106 == $event->FixtureId && 342 == $market->Id && '-7.5 (0-0)' == $bet->BaseLine) {
                     $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_average_dividend start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                     $this->logger->debug('do_average_dividend provider id =>'.$provider->Id. ' bet =>: '. json_encode($bet));
                     $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_average_dividend end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                }

                if (isset($returnBets[$market->Id . '_' . $bet->BaseLine . '_' . $bet->Name])) {
                    $mergeBet = $returnBets[$market->Id . '_' . $bet->BaseLine . '_' . $bet->Name];
                    $mergeBet['totalStartPriceSum'] = $mergeBet['totalStartPriceSum'] + $bet->StartPrice;
                    $mergeBet['totalPriceSum'] = $mergeBet['totalPriceSum'] + $bet->Price;
                    $mergeBet['count'] = $mergeBet['count'] + 1;
                } else {
                    $id = $this->create_id($event, $bet_type, $market, $bet, $bfind);
                    $mergeBet = $this->new_bet_data($id, $event, $sport_id, $bet_type, $market, $bet, $kstStartDate);
                    //$mergeBet['market_base'] = $market->Id . '_' . $bet->BaseLine;
                    //if (7724281 == $event->FixtureId && 1558 == $market->Id && (11.5 == $bet->BaseLine || -11.5 == $bet->BaseLine || 10.5 == $bet->BaseLine || -10.5 == $bet->BaseLine )) {
                    //if (7724281 == $event->FixtureId && 226 == $market->Id) {
                    //    $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_average_dividend start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    //    $this->logger->debug('do_average_dividend : base line : ' . $bet->BaseLine . ' line : ' . $bet->Line . ' result bf : ' . $bf_int . ' result_af : ' . $int . ' id : ' . $id);
                    //    $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_average_dividend end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    //}
                }

                $mergeBet['providers'][] = $bet;

                $returnBets[$market->Id . '_' . $bet->BaseLine . '_' . $bet->Name] = $mergeBet;
            }
        }

        // 해당 타입 프로바이더가 전체가 2면 2라고 상태값을 기록해줘야 한다.
        foreach ($except_bet as $key => $bet) {
            if (true == isset($returnBets[$key]))
                continue;
            $id = $this->create_id($event, $bet_type, $market, $bet, $bfind);
            $mergeBet = $this->new_bet_data($id, $event, $sport_id, $bet_type, $market, $bet, $kstStartDate);

            $mergeBet['providers'][] = $bet;

            $returnBets[$market->Id . '_' . $bet->BaseLine . '_' . $bet->Name] = $mergeBet;
        }

        if (6 != $market->Id && 9 != $market->Id) { // 정확한 스코어 계열은 제외한다. 해당 타입중에 1개라도 status가 2이면 모든 해당 타입은 2로 셋팅이 되어야 한다.
            foreach ($except_bet as $key => $bet) {

                $arr_key = explode('_', $key);

                foreach ($returnBets as $mergeBet) {
                    if ($arr_key[0] == $mergeBet['markets_id'] && $arr_key[1] == $mergeBet['BaseLine']) {
                        $mergeBet['Status'] = 2;
                    }
                }
            }
        }

        foreach ($returnBets as $key => $mergeBet) {
            $bf_price = $returnBets[$key]['Price'];
            $returnBets[$key]['Price'] = sprintf('%0.2f', $mergeBet['totalPriceSum'] / $mergeBet['count']);
            $returnBets[$key]['StartPrice'] = sprintf('%0.2f', $mergeBet['totalStartPriceSum'] / $mergeBet['count']);


            //if (7724281 == $event->FixtureId && 1558 == $market->Id && (11.5 == $bet->BaseLine || -11.5 == $bet->BaseLine || 10.5 == $bet->BaseLine || -10.5 == $bet->BaseLine )) {
            if (7987106 == $event->FixtureId && 342 == $market->Id && ('-7.5 (0-0)' == $returnBets[$key]['BaseLine'] )) {
                $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_average_dividend start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                $this->logger->debug('do_average_dividend avg: fix_id ' . $event->FixtureId . ' market_id : ' . $market->Id . ' result bf : ' . $bf_price . ' result_af : ' . $returnBets[$key]['Price'] . ' name : ' . $returnBets[$key]['Name'] . ' base_line => ' . $returnBets[$key]['BaseLine']);
                $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_average_dividend end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
            }
        }
        return $returnBets;
    }

    // 기본 환수율
    private function do_merge_bet_data($avg_bet) {
        $returnBets = [];

        foreach ($avg_bet as $bet) {
            $FixtureId = $bet['FixtureId'];
            $markets_id = $bet['markets_id'];
            $BaseLine = $bet['BaseLine'];
            $Line = $bet['Line'];
            $StartPrice = $bet['StartPrice'];
            $Price = $bet['Price'];
            $LastUpdate = $bet['LastUpdate'];
            $Name = $bet['Name'];
            $sport_id = $bet['sport_id'];
            $bet_type = $bet['bet_type'];

            $Status = $bet['Status'];
            $kstStartDate = $bet['kstStartDate'];
            $markets_name = $bet['markets_name'];
            $Settlement = $bet['Settlement'];
            $Id = $bet['Id'];
            if (isset($returnBets[$markets_id . '_' . $BaseLine])) {
                $mergeBet = $returnBets[$markets_id . '_' . $BaseLine];
            } else {
                $mergeBet = [
                    'FixtureId' => $FixtureId,
                    'markets_id' => $markets_id,
                    'sport_id' => $sport_id,
                    'bet_type' => $bet_type,
                    'BaseLine' => $BaseLine,
                    'markets_name' => $markets_name,
                    'kstStartDate' => $kstStartDate,
                    'Bets' => [],
                    'providers' => $bet['providers']
                ];
            }

            $mergeBet['Bets'][$Name] = array('Id' => $Id, 'Name' => $Name, 'StartPrice' => $StartPrice
                , 'Price' => $Price, 'Line' => isset($Line) ? $Line : ''
                , 'BaseLine' => $BaseLine, 'tempPrice' => 0, 'Status' => $Status, 'Settlement' => $Settlement
                , 'ex_refund_rate' => 0, 'total_rate' => 0, 'LastUpdate' => $LastUpdate);

            $returnBets[$markets_id . '_' . $BaseLine] = $mergeBet;
        }


        return $returnBets;
    }

    private function do_call_total_rate($betData) {
        $total_rate = 0;
        foreach ($betData['Bets'] as $key => $bet) {
            $ex_refund_rate = (100 / $bet['Price']);
            //$returnBets[$r_key]['Bets'][$key]['ex_refund_rate'] = $ex_refund_rate;
            $total_rate = $total_rate + $ex_refund_rate; // 배당별 확률 계산
        }
        $total_rate = sprintf('%0.2f', (100 / ($total_rate)) * 100); // 전체 승률 
        return $total_rate;
    }

    public function do_renew_cal_refund_rate($avg_bet, $refund_rate, $is_margin_refund) {
        $returnBets = $this->do_merge_bet_data($avg_bet);
        foreach ($returnBets as $r_key => $betData) {
            $total_rate = $this->do_call_total_rate($betData);
            // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A
            foreach ($betData['Bets'] as $key => $bet) {
                $returnBets[$r_key]['Bets'][$key]['tempPrice'] = $returnBets[$r_key]['Bets'][$key]['Price'];
                $returnBets[$r_key]['Bets'][$key]['ex_refund_rate'] = sprintf('%0.2f', $refund_rate / $returnBets[$r_key]['Bets'][$key]['Price']);
                $returnBets[$r_key]['Bets'][$key]['Price'] = sprintf('%0.2f', ($returnBets[$r_key]['Bets'][$key]['Price'] / $total_rate) * $refund_rate);
                $returnBets[$r_key]['Bets'][$key]['total_rate'] = $total_rate;
                $returnBets[$r_key]['Bets'][$key]['refund_rate'] = $refund_rate;
                 
                if (7987106 == $returnBets[$r_key]['FixtureId'] && 342 == $returnBets[$r_key]['markets_id'] && '-7.5 (0-0)' == $returnBets[$r_key]['BaseLine']) {
                    $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_cal_refund_rate start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    $this->logger->debug('====> Start do_renew_cal_refund_rate FixtureId : ' . $returnBets[$r_key]['FixtureId'] . ' bet_type : '
                            . $returnBets[$r_key]['bet_type'] . ' sport_id : ' . $returnBets[$r_key]['sport_id'] . ' market : '
                            . $returnBets[$r_key]['markets_id'] . ' BaseLine : ' . $returnBets[$r_key]['BaseLine'] . ' bet_price : '
                            . $returnBets[$r_key]['Bets'][$key]['Price'] . ' tempPrice : ' . $returnBets[$r_key]['Bets'][$key]['tempPrice']
                            . ' total_rate : ' . $returnBets[$r_key]['Bets'][$key]['total_rate'] . ' refund_rate : ' . $refund_rate . ' Name : ' . $returnBets[$r_key]['Bets'][$key]['Name'] . ' bet id : ' . $returnBets[$r_key]['Bets'][$key]['Id']);
                    $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_cal_refund_rate end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                }
            }
        }

        return $returnBets;
    }

    // 차감 환수율
    public function do_renew_cal_deduction_refund_rate($avg_bet, $deduction_refund_rate, $refund_rate) {

        if (0 < $refund_rate) {
            foreach ($avg_bet as $avg_key => $betData) {
                $total_rate = $this->do_call_total_rate($betData);
                $af_total_rate = $total_rate - $deduction_refund_rate;
                // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A
                foreach ($betData['Bets'] as $key => $bet) {
                    $avg_bet[$avg_key]['Bets'][$key]['tempPrice'] = $avg_bet[$avg_key]['Bets'][$key]['Price'];
                    $avg_bet[$avg_key]['Bets'][$key]['ex_refund_rate'] = sprintf('%0.2f', $total_rate / $avg_bet[$avg_key]['Bets'][$key]['Price']);
                    $avg_bet[$avg_key]['Bets'][$key]['Price'] = sprintf('%0.2f', ($af_total_rate / $avg_bet[$avg_key]['Bets'][$key]['ex_refund_rate']));
                    $avg_bet[$avg_key]['Bets'][$key]['total_rate'] = $total_rate;
                    $avg_bet[$avg_key]['Bets'][$key]['deduction_refund_rate'] = $deduction_refund_rate;

                    if (7987106 == $avg_bet[$avg_key]['FixtureId'] && 342 == $avg_bet[$avg_key]['markets_id'] && '-7.5 (0-0)' == $avg_bet[$avg_key]['BaseLine']) {
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 1 do_renew_cal_deduction_refund_rate start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                        $this->logger->debug('====> 1 Start do_renew_cal_deduction_refund_rate FixtureId : ' . $avg_bet[$avg_key]['FixtureId'] . ' bet_type : '
                                . $avg_bet[$avg_key]['bet_type'] . ' sport_id : ' . $avg_bet[$avg_key]['sport_id'] . ' market : '
                                . $avg_bet[$avg_key]['markets_id'] . ' BaseLine : ' . $avg_bet[$avg_key]['BaseLine'] . ' bet_price : '
                                . $avg_bet[$avg_key]['Bets'][$key]['Price'] . ' tempPrice : ' . $avg_bet[$avg_key]['Bets'][$key]['tempPrice']
                                . ' total_rate : ' . $avg_bet[$avg_key]['Bets'][$key]['total_rate'] . ' deduction_refund_rate : ' . $deduction_refund_rate . ' Name : ' . $avg_bet[$avg_key]['Bets'][$key]['Name']);
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 1 do_renew_cal_deduction_refund_rate end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    }
                }
            }
            return $avg_bet;
        } else {
            $returnBets = $this->do_merge_bet_data($avg_bet);
            foreach ($returnBets as $r_key => $betData) {
                $total_rate = $this->do_call_total_rate($betData);
                $af_total_rate = $total_rate - $deduction_refund_rate;
                // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A
                foreach ($betData['Bets'] as $key => $bet) {
                    $returnBets[$r_key]['Bets'][$key]['tempPrice'] = $returnBets[$r_key]['Bets'][$key]['Price'];
                    $returnBets[$r_key]['Bets'][$key]['ex_refund_rate'] = sprintf('%0.2f', $total_rate / $returnBets[$r_key]['Bets'][$key]['Price']);
                    $returnBets[$r_key]['Bets'][$key]['Price'] = sprintf('%0.2f', ($af_total_rate / $returnBets[$r_key]['Bets'][$key]['ex_refund_rate']));
                    $returnBets[$r_key]['Bets'][$key]['total_rate'] = $total_rate;

                    if (7987106 == $returnBets[$r_key]['FixtureId'] && 342 == $returnBets[$r_key]['markets_id'] && '-7.5 (0-0)' == $returnBets[$r_key]['BaseLine']) {
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 2 do_renew_cal_deduction_refund_rate start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                        $this->logger->debug('====>2 Start do_renew_cal_deduction_refund_rate FixtureId : ' . $returnBets[$r_key]['FixtureId'] . ' bet_type : '
                                . $returnBets[$r_key]['bet_type'] . ' sport_id : ' . $returnBets[$r_key]['sport_id'] . ' market : '
                                . $returnBets[$r_key]['markets_id'] . ' BaseLine : ' . $returnBets[$r_key]['BaseLine'] . ' bet_price : '
                                . $returnBets[$r_key]['Bets'][$key]['Price'] . ' tempPrice : ' . $returnBets[$r_key]['Bets'][$key]['tempPrice']
                                . ' total_rate : ' . $returnBets[$r_key]['Bets'][$key]['total_rate'] . ' deduction_refund_rate : ' . $deduction_refund_rate . ' Name : ' . $returnBets[$r_key]['Bets'][$key]['Name']);
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 2 do_renew_cal_deduction_refund_rate end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    }
                }
            }
            return $returnBets;
        }
    }

    // 마진 환수율 
    public function do_renew_cal_margin_refund_rate($avg_bet, $deduction_refund_rate, $refund_rate, $model) {
        $arr = array(236, 281);
        //mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
        if (0 < $refund_rate || 0 < $deduction_refund_rate) {
            foreach ($avg_bet as $avg_key => $betData) {
                $total_rate = $this->do_call_total_rate($betData);
                $bf_total_rate = $total_rate;
                //=(100/((100/C7)+(100/D7))*100)
                // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A

                $type = count($betData['Bets']);
                if (2 !== $type && 3 !== $type)
                    continue;
                foreach ($betData['Bets'] as $key => $bet) {
                    $avg_bet[$avg_key]['Bets'][$key]['tempPrice'] = $avg_bet[$avg_key]['Bets'][$key]['Price'];
                    $ex_refund_rate = sprintf('%0.2f', $total_rate / $avg_bet[$avg_key]['Bets'][$key]['Price']);
                    $bf_ex_refund_rate = $ex_refund_rate;
                    $str_sql = " SELECT value FROM lsports_magin_range WHERE type = $type and min <= $ex_refund_rate AND  $ex_refund_rate < max ";
                    $arr_result = $model->db->query($str_sql)->getResultArray();
                    $avg_bet[$avg_key]['Bets'][$key]['ex_refund_rate'] = $ex_refund_rate + $arr_result[0]['value'];
                    $ex_refund_rate = $avg_bet[$avg_key]['Bets'][$key]['ex_refund_rate'];
                    $avg_bet[$avg_key]['Bets'][$key]['Price'] = sprintf('%0.2f', ($total_rate / $ex_refund_rate));
                    $avg_bet[$avg_key]['Bets'][$key]['total_rate'] = $total_rate;

                    if (7987106 == $avg_bet[$avg_key]['FixtureId'] && 342 == $avg_bet[$avg_key]['markets_id'] && '-7.5 (0-0)' == $avg_bet[$avg_key]['BaseLine']) {
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_cal_margin_refund_rate start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                        $this->logger->debug('====> Start do_renew_cal_margin_refund_rate FixtureId : ' . $avg_bet[$avg_key]['FixtureId'] . ' bet_type : '
                                . $avg_bet[$avg_key]['bet_type'] . ' sport_id : ' . $avg_bet[$avg_key]['sport_id'] . ' market : '
                                . $avg_bet[$avg_key]['markets_id'] . ' BaseLine : ' . $avg_bet[$avg_key]['BaseLine'] . ' bet_price : '
                                . $avg_bet[$avg_key]['Bets'][$key]['Price'] . ' tempPrice : ' . $avg_bet[$avg_key]['Bets'][$key]['tempPrice']
                                . ' total_rate : ' . $avg_bet[$avg_key]['Bets'][$key]['total_rate'] . ' lsports_magin_range : ' . $arr_result[0]['value'] .
                                ' bf_ex_refund_rate : ' . $bf_ex_refund_rate . ' Name : ' . $avg_bet[$avg_key]['Bets'][$key]['Name'] . ' bet id :' . $avg_bet[$avg_key]['Bets'][$key]['Id']);
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_cal_margin_refund_rate end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    }
                }
            }
            return $avg_bet;
        } else {
            $returnBets = $this->do_merge_bet_data($avg_bet);

            foreach ($returnBets as $r_key => $betData) {
                $total_rate = $this->do_call_total_rate($betData);
                $bf_total_rate = $total_rate;

                // [a/[100/[(100/a)+(100/b)+(100/c)]]]*R=A
                $type = count($betData['Bets']);
                if (2 !== $type && 3 !== $type)
                    continue;
                foreach ($betData['Bets'] as $key => $bet) {
                    $returnBets[$r_key]['Bets'][$key]['tempPrice'] = $returnBets[$r_key]['Bets'][$key]['Price'];
                    $ex_refund_rate = sprintf('%0.2f', $bf_total_rate / $returnBets[$r_key]['Bets'][$key]['Price']);
                    $bf_ex_refund_rate = $ex_refund_rate;
                    $str_sql = " SELECT value FROM lsports_magin_range WHERE type = $type and min <= $ex_refund_rate AND  $ex_refund_rate < max ";
                    $arr_result = $model->db->query($str_sql)->getResultArray();

                    $returnBets[$r_key]['Bets'][$key]['ex_refund_rate'] = $ex_refund_rate + $arr_result[0]['value'];
                    $ex_refund_rate = $returnBets[$r_key]['Bets'][$key]['ex_refund_rate'];
                    $returnBets[$r_key]['Bets'][$key]['Price'] = sprintf('%0.2f', ($bf_total_rate / $ex_refund_rate));
                    $returnBets[$r_key]['Bets'][$key]['total_rate'] = $total_rate;

                    if (7987106 == $returnBets[$r_key]['FixtureId'] && 342 == $returnBets[$r_key]['markets_id'] && '-7.5 (0-0)' == $returnBets[$r_key]['BaseLine']) {
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_cal_margin_refund_rate start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                        $this->logger->debug('====> Start do_renew_cal_margin_refund_rate FixtureId : ' . $returnBets[$r_key]['FixtureId'] . ' bet_type : '
                                . $returnBets[$r_key]['bet_type'] . ' sport_id : ' . $returnBets[$r_key]['sport_id'] . ' market : '
                                . $returnBets[$r_key]['markets_id'] . ' BaseLine : ' . $returnBets[$r_key]['BaseLine'] . ' bet_price : '
                                . $returnBets[$r_key]['Bets'][$key]['Price'] . ' tempPrice : ' . $returnBets[$r_key]['Bets'][$key]['tempPrice']
                                . ' total_rate : ' . $returnBets[$r_key]['Bets'][$key]['total_rate'] . ' lsports_magin_range : ' .
                                $arr_result[0]['value'] . ' bf_ex_refund_rate : ' . $bf_ex_refund_rate . ' Name : ' . $returnBets[$r_key]['Bets'][$key]['Name'] . ' bet id :' . $returnBets[$r_key]['Bets'][$key]['Id']);
                        $this->logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! do_renew_cal_margin_refund_rate end !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');
                    }
                }
            }
            return $returnBets;
        }
    }

    public function get_sp_lg_mk_refund_data($sport_id, $fixtureId, $market, $bet_type, $Model) {
        $arr = array(236, 281);
        $str_sql = " SELECT     IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status,
                                IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                                fix.fixture_sport_id,
                                market.refund_rate as mk_input_refund_rate,
                                market.deduction_refund_rate as mk_deduction_refund_rate,
                                market.is_margin_refund as mk_is_margin_refund
                            FROM lsports_fixtures as fix
                                LEFT JOIN   lsports_refund_rate_market as market
                                ON   fix.fixture_sport_id = market.sports_id  AND fix.fixture_league_id = market.league_id   
                            WHERE fix.fixture_id = $fixtureId AND fix.bet_type = $bet_type
                                AND market.market_id = $market->Id AND market.bet_type = $bet_type";

        if (154914 == $sport_id && true == in_array($market->Id, $arr)) {
            //$this->logger->debug($str_sql);
        }
        $arr_result = $Model->db->query($str_sql)->getResultArray();
        return $arr_result;
    }

    public function get_sp_lg_sl_refund_data($fixtureId, $bet_type, $Model) {
        $str_sql = " SELECT     IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) as display_status,
                                IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                                fix.fixture_sport_id,
                                sports.input_refund_rate as sp_input_refund_rate,
                                sports.deduction_refund_rate as sp_deduction_refund_rate,
                                sports.is_margin_refund as sp_is_margin_refund,
                                leagues.input_refund_rate as lg_input_refund_rate,
                                leagues.deduction_refund_rate as lg_deduction_refund_rate,
                                leagues.is_margin_refund as lg_is_margin_refund
                                FROM lsports_fixtures as fix
                                    LEFT JOIN   lsports_sports as sports
                                ON   fix.fixture_sport_id = sports.id   
                                    LEFT JOIN   lsports_leagues as leagues
                                ON   fix.fixture_league_id = leagues.id   
                            WHERE fix.fixture_id = $fixtureId AND IF('ON' = fix.passivity_flag AND fix.display_status_passivity is NOT NULL ,fix.display_status_passivity,fix.display_status) IN (1,2,3) AND fix.bet_type = $bet_type
                                AND sports.is_use = 1 AND sports.bet_type = $bet_type AND leagues.bet_type = $bet_type";

        if (1 == $bet_type) {
            $str_sql .= " AND leagues.is_use = 1 ";
        }

        //if (7272133 == $fixtureId && 226 == $market->Id) {
        //    $this->logger->debug($str_sql);
        //}
        $arr_result = $Model->db->query($str_sql)->getResultArray();
        return $arr_result;
    }

    public function get_average_dividend_provider_data($lSportsFixturesModel) {
        $array_average_provider = [];
        $str_sql = " select sports_id, provider from average_dividend_provider";
        $arr_provider_result = $lSportsFixturesModel->db->query($str_sql)->getResultArray();
        foreach ($arr_provider_result as $provider) {
            $array_average_provider[$provider['sports_id']] = explode(',', $provider['provider']);
        }

        return $array_average_provider;
    }

    public function checkOverUnderPrice($avg_bet) {


        $over_before_price = 0;
        foreach ($avg_bet as $avg_key => $betData) {
            foreach ($betData['Bets'] as $key => $bet) {
                if (1 != $bet['Status'])
                    continue;

                if ('Over' == $bet['Name']) {
                    if ($over_before_price < $bet['Price']) {
                        $over_before_price = $bet['Price'];
                        break;
                    } else {
                        $avg_bet[$avg_key]['Bets']['Over']['Status'] = 2;
                        $avg_bet[$avg_key]['Bets']['Under']['Status'] = 2;
                    }
                }
            }
        }


        $this->logger->debug(' checkOverUnderPrice fix_id==>' . $mergeBet['fixture_id'] . ' base_line==>' . $mergeBet['bet_base_line']);
    }
    
    

}
