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
use App\Models\TLogLsportsBetModel; 
use App\Util\Calculate;

// 최초 데이터 셋팅하는 컨트롤러
class LSportsRealTimeInitController extends BaseController {

    use ResponseTrait;

    //// API
    protected $bet_code_util;
    protected $array_in_refund_rate_market_id = array(4, 6, 7, 9, 16, 17, 70, 98, 99, 390, 427, 1537, 1538);

    //// API
    public function __construct() {
        //$this->bet_code_util = new BetCodeUtil();
    }

    public function initData() {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }

    public function getInPlayScheduleURL() {
        $pullOperations = new PullOperations();
        $schedule = $pullOperations->getInPlayScheduleURL();
        $fixtures = $schedule->Body;
        // JSON File Save
        $fileName = '../writable/logs/getInPlaySchedule/' . 'getInPlayScheduleData_' . date('Y-m-d_His', strtotime('Now')) . '.txt';
        $myFile = fopen($fileName, 'a+');
        fwrite($myFile, json_encode($schedule, JSON_UNESCAPED_UNICODE));
        fclose($myFile);

        return;
        $sql = array();
        $participantSql = array();
        foreach ($fixtures as $row) {

            $row->Fixture->StartDate;
        }
    }

    public function packageControl() {
        $enable = $_GET['enable'];
        $inplay = $_GET['inplay'];

        $pullOperations = new PullOperations('ODD', $this->logger);
        $result = $pullOperations->packageControl($enable, $inplay);
        //return $this->curl($url);
        return true;
    }

    public function getOrderFixtures() {
        $fixtures = $_GET['fixtures'];
        $pullOperations = new PullOperations('ODD', $this->logger);
        $result = $pullOperations->getOrderFixtures($fixtures);
        print_r($result);
        return true;
    }

    public function cancelOrderFixtures() {
        $fixtures = $_GET['fixtures'];
        $pullOperations = new PullOperations('ODD', $this->logger);
        $result = $pullOperations->cancelOrderFixtures($fixtures);
        print_r($result);
        return true;
    }

    public function getViewOrderedFixtures() {
        $fixtures = ''; //$_GET['fixtures'];
        $pullOperations = new PullOperations('ODD', $this->logger);
        $order = $pullOperations->getViewOrderedFixtures($fixtures);
        if (false == isset($order))
            return;

        $sql = array();
        $request_fixtures = array();
        $searchDate = date("Y-m-d 00:00:00", strtotime(" -10 day"));
        
        $this->logger->info('::::::::::::::: setRefundFixturesDataReal 1 :::::::::::::::');
        foreach ($order->FixtureOrders as $row) {
            if ($row->OrderDate < $searchDate)
                continue;
            $request_fixtures[] = $row->FixtureId;
        }
        
        $request_fixtures = implode(',', $request_fixtures);
        $lSportsFixturesModel = new LSportsFixturesModel();
        $result = $pullOperations->getShapshot($request_fixtures, $this->logger);
        // JSON File Save
        $Body = $result->Body;
        $Header = $result->Header;
        if (true == isset($Body->Events) && count($Body->Events) > 0) {
            $this->bet_code_util = new BetCodeUtil($this->logger);
            $this->setRefundFixturesDataReal($Body->Events, 2);
        }
    }

    public function setRefundFixturesDataReal($fixtures, $bet_type) {
        $this->logger->info('::::::::::::::: setRefundFixturesDataReal Start :::::::::::::::');
        $lSportsFixturesModel = new LSportsFixturesModel();
        $sql = array();
        $sql_bet = array();
        $participantSql = array();
        $array_book_maker = $this->bet_code_util->get_market_data($bet_type, $lSportsFixturesModel, $this->logger);

        $sql_bet = [];
        $log_sql = [];
        foreach ($fixtures as $row) {

            $sport_id = $row->Fixture->Sport->Id;
            $league_id = $row->Fixture->League->Id;
            $kstStartDate = date("Y-m-d H:i:s", strtotime($row->Fixture->StartDate . '+9 hours'));
            $arr_result = $this->bet_code_util->get_renew_sp_lg_deduction_refund_data($row->FixtureId, $bet_type, $lSportsFixturesModel, $this->logger);

            if (8 == $row->Fixture->Status) {
                $display_status = $arr_result[0]['display_status'];
            } else {
                $display_status = CodeUtil::get_display_stauts($row->Fixture->Status, 2);
            }


            $sql = $this->bet_code_util->do_update_fix_score($row, $sql, $bet_type, $kstStartDate, $display_status, $this->logger);

            $participantSql = $this->bet_code_util->do_update_participants($row->Fixture, $participantSql, $this->logger);

            // 베팅정보
            foreach ($row->Markets as $market) {

                if (false == isset($array_book_maker[$sport_id][$market->Id]))
                    continue;
                $provider_main = null;
                foreach ($market->Providers as $provider) {
                    //$this->logger->debug('====> Log setRefundFixturesDataReal provider id  : ' . $provider->Id );
                    if ($provider->Id != $array_book_maker[$sport_id][$market->Id]['main_book_maker'])
                        continue;
                    $provider_main = $provider;
                    break;
                }

                if (null == $provider_main) {
                    continue;
                }
                // 마켓 차감 데이터를 가져온다.

                $arr_mk_result = $this->bet_code_util->get_sp_lg_mk_refund_data($sport_id, $row->FixtureId, $market, $bet_type, $lSportsFixturesModel);

                $refund_rate = 0;
                if (isset($arr_mk_result) && 0 < count($arr_mk_result) && 0 < $arr_mk_result[0]['mk_deduction_refund_rate']) {
                    $refund_rate = $arr_mk_result[0]['mk_deduction_refund_rate'];
                } else if (0 < count($arr_result) && 0 < $arr_result[0]['lg_deduction_refund_rate']) {
                    $refund_rate = $arr_result[0]['lg_deduction_refund_rate'];
                } else if (0 < count($arr_result) && 0 < $arr_result[0]['sp_deduction_refund_rate']) {
                    $refund_rate = $arr_result[0]['sp_deduction_refund_rate'];
                }

                if (2 == $display_status && 0 < $refund_rate && false == in_array($market->Id, $this->array_in_refund_rate_market_id)) {
                    list($sql_bet, $sql_log) = $this->bet_code_util->do_refund_rate($row, $provider_main, $sport_id, $market, $bet_type, $refund_rate, $kstStartDate, $sql_bet, $log_sql, $this->logger);
                } else {
                    foreach ($provider_main->Bets as $bet) {
                        if (0 == $bet->Price || true == $this->bet_code_util->inplay_base_line_markets_except($bet, $row->Fixture->Sport->Id, $market->Id, $this->logger))
                            continue;
                        list($sql_bet, $sql_log) = $this->bet_code_util->do_update_bet($sport_id,$league_id, $provider_main, $row->FixtureId, $bet, $market, $bet_type, $kstStartDate, $sql_bet, $log_sql, $this->logger);
                    }
                }
            }
        }

        if (count($participantSql) > 0) {
            $this->bet_code_util->do_update_participants_query($participantSql, $lSportsFixturesModel, $this->logger);
        }
        // 경기데이터 넣기
        if (count($sql) > 0) {
            $this->bet_code_util->do_update_fix_score_query($sql, $lSportsFixturesModel, $this->logger);
        }
        if (count($sql_bet) > 0) {
            //$this->bet_code_util->do_update_bet_query($sql_bet, 2, $lSportsFixturesModel, $this->logger); //rmq랑 겹치지 않게 하기위해서 
        }

        if (count($sql_log) > 0) {
            $log_model = new TLogLsportsBetModel();
            $log_model->do_insert_bet_real_query($sql_log, $this->logger);
        }
        $this->logger->info('::::::::::::::: setRefundFixturesDataReal End :::::::::::::::');
    }

    // 실시간 정산
    public function real_doTotalCalculate() {
    //    Calculate::doResultProcessing(2, $this->logger);
    //    Calculate::renew_doTotalCalculate(2, $this->logger);
    }

}
