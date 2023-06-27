<?php

namespace App\Controllers;

use App\Entities\MemberBet;
use App\Models\GameModel;
use App\Models\MemberBetModel;
use App\Models\MemberModel;
use App\Models\TGameConfigModel;
use App\Models\TLogCashModel;
use App\Models\TotalMemberCashModel;
use App\Models\MiniGameMemberBetModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\BetDataUtil;
use App\Util\CodeUtil;
use App\Util\Calculate;
use App\Util\AbuseIPDB;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;
use App\Util\UserPayBack;
class BetController extends BaseController {

    use ResponseTrait;

    // API
    protected $gmPt; // 겜블패치 

    public function __construct() {
        if ('K-Win' == config(App::class)->ServerName) {
            $this->gmPt = new KwinGmPt();
        } else if ('GAMBLE' == config(App::class)->ServerName) {
            $this->gmPt = new GambelGmPt();
        } else if ('BETGO' == config(App::class)->ServerName) {
            $this->gmPt = new BetGoGmPt();
        } else if ('CHOSUN' == config(App::class)->ServerName) {
            $this->gmPt = new ChoSunGmPt();
        } else if ('BETS' == config(App::class)->ServerName) {
            $this->gmPt = new BetsGmPt();
        }

        helper('form');
        helper('security');
    }

    // is_betting_slip OFF 일때 호출한다.
    public function addBet() {
        if (0 < session()->get('tm_unread_cnt')) {
            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }

        $betList = isset($_POST['betList']) ? $_POST['betList'] : NULL;
        $betType = isset($_POST['betType']) ? $_POST['betType'] : NULL;
        $totalMoney = isset($_POST['totalMoney']) ? $_POST['totalMoney'] : NULL;
        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $itemValue = isset($_POST['itemValue']) ? $_POST['itemValue'] : 0;
        $isBettingSlip = isset($_POST['isBettingSlip']) ? $_POST['isBettingSlip'] : '';
        $isClassic = isset($_POST['isClassic']) ? $_POST['isClassic'] : 'OFF';
        $keep_login_access_token = isset($_POST['keep_login_access_token']) ? $_POST['keep_login_access_token'] : '';

        $response['keep_login_access_token'] = '';
        if (!CodeUtil::only_number($betType) ||
                (GT_SPORTS != $betType && GT_REALTIME != $betType ) ||
                ('ON' == $isClassic && GT_SPORTS != $betType) ||
                !isset($betList) ||
                0 == count($betList) ||
                !CodeUtil::only_number($totalMoney) ||
                $totalMoney < 0 ||
                '' == $isBettingSlip) {
            $response['messages'] = '인자값이 잘못되었습니다.';
            return $this->fail($response);
        }

        $memberModel = new MemberModel();
        try {

            //$memberModel->db->transStart(); // trans start move
            // 회원 상태값 체크
            list($revtal, $message) = $this->checkBettingAccessToken($keep_login_access_token, $memberModel);
            if (false == $revtal) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message;
                return $this->fail($response);
            }

            list($revtal, $message, $member, $memberIdx) = $this->checkMemberStatus(session()->get('member_idx'), $memberModel);
            if (false == $revtal) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message;
                return $this->fail($response);
            }

            if ($member->getMoney() < $totalMoney) {
                //$memberModel->db->transRollback();
                $response['messages'] = '배팅 금액이 부족합니다.';
                return $this->fail($response);
            }

            // Commercial ip check
            list($retVal, $message) = CodeUtil::checkCommercialIp($member, $this->logger);
            if (false == $retVal) {
                $this->logger->error('checkCommercialIp');
                //$memberModel->db->transRollback();
                $response['messages'] = $message;
                return $this->fail($response);
            }

            // 개인별 베팅제한 체크
            list($revtal_in, $message_in, $folderType) = $this->checkIndividual($memberIdx, $betType, $betList, $isClassic, $memberModel);
            if (false == $revtal_in) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message_in;
                return $this->fail($response);
            }

            // 동일시간 베팅한 내용체크
            /*$revtal_same_time = $this->checkSameTimeBetting($memberIdx, $memberModel);
            if (false == $revtal_same_time) {
                //$memberModel->db->transRollback();
                $response['messages'] = '동일시간 베팅이 있습니다.';
                return $this->fail($response);
            }*/

            // 배팅 슬립 데이터를 가져온다.
            if ($isBettingSlip != $member->getIsBettingSlip()) {
                //$memberModel->db->transRollback();
                $response['messages'] = '배팅슬립 설정이 잘못 되어있습니다.';
                return $this->fail($response);
            }

            // 점검,보너스 배당률 정보도 가져온다.
            $tgcModel = new TGameConfigModel();
            $arr_config = $this->gmPt->getConfigData($tgcModel);

            // 스포츠 점검
            if ('Y' == $arr_config['service_sports'] && GT_SPORTS == $betType && 'OFF' == $isClassic && 9 != $member->getLevel()) {
                //$memberModel->db->transRollback();
                $response['messages'] = '스포츠 점검중입니다.';
                return $this->fail($response);
            }// 실시간 점검
            else if ('Y' == $arr_config['service_real'] && GT_REALTIME == $betType && 9 != $member->getLevel()) {
                //$memberModel->db->transRollback();
                $response['messages'] = '실시간 점검중입니다.';
                return $this->fail($response);
            }// 클래식 점검
            else if ('Y' == $arr_config['service_classic'] && GT_SPORTS == $betType && 'ON' == $isClassic && 9 != $member->getLevel()) {
                //$memberModel->db->transRollback();
                $response['messages'] = '클래식 점검중입니다.';
                return $this->fail($response);
            }

            // 중복베팅 체크
            $lastBetInfo = $this->getLastBettingInfo($memberModel, $member->getIdx()); // 
            $checkStartTime = date("Y-m-d H:i:s", strtotime($lastBetInfo[0]['betting_dt'] . "+" . 5 . "seconds"));
            $currentTime = date('Y-m-d H:i:s');

            if (0 < count($lastBetInfo) && $currentTime < $checkStartTime) {
                //$sql = "UPDATE member set status = 11 WHERE idx = ? ";
                $sql = "UPDATE member set is_monitor_bet = 'Y' WHERE idx = ? ";
                $memberModel->db->query($sql, [$memberIdx]);

                //$memberModel->db->transComplete();
                $response['messages'] = '정상배팅이 아닙니다.!';
                return $this->fail($response);
            }



            $arr_bet_price = null;
            $max_dividend = 100;
            $bet_count = 0;

            // $betList 에서 fixtureId 값들을 추출한다.
            $totalOdds = 1;
            //$memberModel->db->transStart();

            list($retval_betlist, $message_betlist, $max_dividend, $totalOdds, $bet_count, $arr_bet_price, $array_fixture, $array_league_tag_id, $fDetail, $betList_renew, $overlap) = $this->checkBetListData($isBettingSlip, $totalOdds, $arr_config, $betType, $betList, $isClassic, $memberModel);
            if (false == $retval_betlist) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message_betlist;
                return $this->fail($response);
            }

            if (count($betList_renew) != count($betList)) {
                //$memberModel->db->transRollback();
                $response['messages'] = '데이터 검증이 잘못되었습니다.';
                return $this->fail($response);
            }

            $bonus_dividend = 1;
            $total_bet_price = 1;
            list($total_bet_price, $bonus_dividend) = $this->gmPt->calBonusPrice($total_bet_price, $bonus_dividend, $folderType, $bet_count, $arr_config);

            // 제한베당이 하나라도 있으면 보너스 적용을 하지 않는다.(조선)
            if ($this->gmPt->isLimitFolder($betList_renew, $arr_config['limit_folder_bonus'])) {
                $bonus_dividend = 1;
            }

            // 야구, 농구, 아이스하키면 조합체크
            list($retval_overlap, $message_overlap) = $this->checkOverLap($overlap);
            if (false == $retval_overlap) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message_overlap;
                return $this->fail($response);
            }

            // 배당제한 체크
            if ($max_dividend < $totalOdds * $bonus_dividend) {
                //$memberModel->db->transRollback();
                $this->logger->debug('max_dividend : ' . $max_dividend . ' totalOdds : ' . $totalOdds . ' bonus_dividend : ' . $bonus_dividend);
                $response['messages'] = '최고 배당 이상을 가져갈수 없습니다. ';
                return $this->fail($response);
            }

            // 리그 최대 배팅금액도 가져온다.
            list($retval_limit, $message_limit, $sql) = $this->checkLimitBetMoney($totalMoney, $betType, $betList, $array_fixture, $member, $isClassic, $memberModel);
            if (false == $retval_limit) {
                //$memberModel->db->transRollback();
                return $this->fail(message_limit);
            }

            if (0 < $itemIdx) {
                $ukey = md5($memberIdx . strtotime('now'));
                $this->gmPt->useItem($ukey, $memberIdx, $itemId, $itemIdx, $comment, $memberModel, $this->logger);
            }

            $memberModel->db->query(
                    'INSERT INTO `fixtures_bet_sum` ('
                    . 'member_idx, '
                    . 'fixture_id, '
                    . 'bet_type, '
                    . 'sum_bet_money) VALUES '
                    . implode(',', $sql)
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'sum_bet_money = sum_bet_money + VALUES(sum_bet_money)'
            );

            // 유저 레벨별 배팅 최소,최대 금액을 체크한다.
            list($retval_min_max, $message_min_max) = $this->checkMinMaxBetMoney($totalOdds, $bonus_dividend, $totalMoney, $betType, $isClassic, $member, $tgcModel);
            if (false == $retval_min_max) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message_min_max;
                return $this->fail($response);
            }

            // 해당 아이템 번호가 유효하면 
            list($retval_item, $message_item) = $this->checkItemIdx($memberIdx, $itemId, $itemValue, $memberModel, $this->logger);
            if (false == $retval_item) {
                //$memberModel->db->transRollback();
                $response['messages'] = $message_item;
                return $this->fail($response);
            }

            $memberBetModel = new MemberBetModel();
            //$add_index = $memberBetModel->addMemberBet(date("Y-m-d H:i:s"), $member, $totalOdds, $totalMoney, $betType
            //        , $folderType, $bonus_dividend, $betList_renew, $arr_bet_price, $fDetail, $isBettingSlip, $itemIdx, $isClassic);
            
            list($result, $errorMessage) = $this->transProcess($member, $totalOdds, $totalMoney, $betType
                    , $folderType, $bonus_dividend, $betList_renew, $arr_bet_price, $fDetail, $isBettingSlip, $itemIdx, $isClassic, $memberBetModel);
             
            if (false == $result) {
                $response['messages'] = $errorMessage;
                return $this->fail($response);
            }
            
            //if (0 == $add_index) {
                //$memberModel->db->transRollback();
            //    return $this->fail("배팅 정보 삽입 실패");
            //}

            $type = '';
            if (GT_SPORTS == $betType && 'OFF' == $isClassic && 'S' == $folderType) {
                $type = 'SPORTS_S';
            } else if (GT_SPORTS == $betType && 'OFF' == $isClassic && 'D' == $folderType) {
                $type = 'SPORTS_D';
            } else if (GT_REALTIME == $betType && 'S' == $folderType) {
                $type = 'REAL_S';
            } else if (GT_REALTIME == $betType && 'D' == $folderType) {
                $type = 'REAL_D';
            } else if (GT_SPORTS == $betType && 'ON' == $isClassic && 'S' == $folderType) {
                $type = 'CLASSIC_S';
            } else if (GT_SPORTS == $betType && 'ON' == $isClassic && 'D' == $folderType) {
                $type = 'CLASSIC_D';
            }

            Calculate::updateChargeBetMoney($memberIdx, $type, $totalMoney, $memberModel, $this->logger);
            UserPayBack::AddBetting($memberIdx,$totalMoney,$memberModel);       
            $af_money = $member->getMoney() - $totalMoney;
            session()->set('money', $af_money);

            $keep_login_access_token = $this->tokenRefresh($member, $memberModel);
            $response['keep_login_access_token'] = $keep_login_access_token;

            //$memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  addMemberBet error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  addMemberBet query : ' . $memberModel->getLastQuery());
            //$memberModel->db->transRollback();
            $response['messages'] = '디비처리 실패로 인한 배팅 실패';
            return $this->fail($response);
        }

        $response = [
            'code' => 200,
            'messages' => '배팅 성공',
            'data' => [
                'bet_id' => $add_index,
                'total_money' => $af_money,
                'total_bet_money' => $totalMoney,
                'arr_tag_ids' => $array_league_tag_id,
                'keep_login_access_token' => $keep_login_access_token
            ]
        ];
        return $this->respond($response, 200);
    }

    private function transProcess($member, $totalOdds, $totalMoney, $betType
            , $folderType, $bonus_dividend, $betList_renew, $arr_bet_price, $fDetail, $isBettingSlip, $itemIdx, $isClassic, $memberBetModel) {

        try {
            $memberBetModel->db->transStart();

            // 동일시간 베팅한 내용체크
            $revtal_same_time = $this->checkSameTimeBetting($member->getIdx(), $memberBetModel);
            if (false == $revtal_same_time) {
                $memberBetModel->db->transRollback();

                return [false, '동일시간 베팅이 있습니다.'];
            }

            $currentDate = date("Y-m-d H:i:s");
            $add_index = $memberBetModel->addMemberBet($currentDate, $member, $totalOdds, $totalMoney, $betType
                    , $folderType, $bonus_dividend, $betList_renew, $arr_bet_price, $fDetail, $isBettingSlip, $itemIdx, $isClassic);

            if (0 == $add_index) {
                $memberBetModel->db->transRollback();
                return [false, '배팅 정보 삽입 실패'];
            }

            $memberBetModel->db->transComplete();
            return [true, 'success'];
        } catch (\mysqli_sql_exception $e) {
            $memberBetModel->db->transRollback();
            $this->logger->error(':::::::::::::::  transProcess error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  transProcess query : ' . $memberBetModel->getLastQuery());
            return [false, '디비처리 실패로 인한 배팅 실패.'];
        }
    }

    // is_betting_slip OFF 일때만 호출한다. 
    public function checkBet() {

        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {

            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }

        $betType = isset($_POST['betType']) ? $_POST['betType'] : NULL;
        $betList = isset($_POST['betList']) ? $_POST['betList'] : NULL;

        if (false == CodeUtil::only_number($betType)) {
            $response['messages'] = '인자값이 잘못되었습니다.';
            return $this->fail($response);
        }

        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            $response['messages'] = '조회되는 유저가 없습니다.';
            return $this->fail($response);
        }

        // 배팅 슬립 데이터를 가져온다.
        if ('OFF' != $member->getIsBettingSlip()) {
            $response['messages'] = '배팅슬립 설정이 잘못 되어있습니다.';
            return $this->fail($response);
        }


        try {
            $array_league_tag_id = [];
            $arr_bet_price = null;
            $max_dividend = 100;
            //$memberModel->db->transStart();

            $return_data = array();
            foreach ($betList as $value) {
                // bet_id 값으로 배팅 
                $str_bet_query = "SELECT    bet.bet_id,
                                        IF('ON' = bet.passivity_flag AND bet.bet_price_passivity is NOT NULL ,bet.bet_price_passivity,bet.bet_price) as bet_price,
                                        IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) as bet_status,
                                        IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                                        markets.limit_bet_price,
                                        markets.max_bet_price
                                    FROM lsports_bet as bet 
                                    LEFT JOIN lsports_markets as markets 
                                        ON bet.markets_id = markets.id
                                    LEFT JOIN lsports_fixtures as fix 
                                        ON bet.fixture_id = fix.fixture_id
                                    WHERE bet.bet_id = ? 
                                    AND bet.bet_type = ?
                                    AND markets.bet_group = ? 
                                    AND markets.is_delete = 0
                                    AND markets.sport_id = ? ;";

                $bet = $memberModel->db->query($str_bet_query, [$value['betId'], $betType, $betType, $value['betSportId']])->getResultArray();

                if (0 == count($bet)) {
                    $retval = array('retval' => -1, 'bet_id' => $value['betId']);
                    $return_data[$value['betId']] = $retval;
                    continue;
                }
                $bet = $bet[0];

                $startTime = date("Y-m-d H:i:s", strtotime($bet['fixture_start_date'] . "-" . 10 . " minutes"));
                $currentTime = date('Y-m-d H:i:s');
                if (1 != $bet['bet_status']) {
                    if (2 == $betType || (1 == $betType && $currentTime < $startTime)) {
                        //$memberModel->db->transRollback();
                        //$response['messages'] = '선택 경기중 마감 된 경기가 있습니다. 재 배팅 부탁드립니다.';
                        //return $this->fail($response);
                        $retval = array('retval' => -2, 'bet_id' => $value['betId']);
                        $return_data[$value['betId']] = $retval;
                        continue;
                    }
                }

                if (0 < $bet['limit_bet_price'] && $bet['bet_price'] < $bet['limit_bet_price']) {
                    $memberModel->db->transRollback();
                    //$response['messages'] = '선택 경기중 마감 된 경기가 있습니다. 재 배팅 부탁드립니다.';
                    //return $this->fail($response);
                    $retval = array('retval' => -2, 'bet_id' => $value['betId']);
                    $return_data[$value['betId']] = $retval;
                    continue;
                }

                if (0 < $bet['max_bet_price'] && $bet['max_bet_price'] < $bet['bet_price']) {
                    //$memberModel->db->transRollback();
                    //$response['messages'] = '선택 경기중 마감 된 경기가 있습니다. 재 배팅 부탁드립니다.';
                    //return $this->fail($response);

                    $retval = array('retval' => -2, 'bet_id' => $value['betId']);
                    $return_data[$value['betId']] = $retval;
                    continue;
                }

                $retval = array('retval' => 1, 'bet_id' => $value['betId'], 'betPrice' => $bet['bet_price']);
                $return_data[$value['betId']] = $retval;
            }

            //$memberModel->db->transComplete();
            //session()->set('money', $af_money);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  checkBet error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  checkBet query : ' . $memberModel->getLastQuery());
            //$memberModel->db->transRollback();
            $response['messages'] = '디비처리 실패로 인한 배팅 실패';
            return $this->fail($response);
        }

        $response = [
            'code' => 200,
            'messages' => '배팅 성공',
            'data' => $return_data
        ];
        return $this->respond($response, 200);
    }

    public function addMiniBet() {
        $memberIdx = session()->get('member_idx');
        $betList = isset($_POST['betList']) ? $_POST['betList'] : NULL;
        $betType = isset($_POST['betType']) ? $_POST['betType'] : NULL;
        $totalMoney = isset($_POST['totalMoney']) ? $_POST['totalMoney'] : NULL;
        $keep_login_access_token = isset($_POST['keep_login_access_token']) ? $_POST['keep_login_access_token'] : '';
        $response['keep_login_access_token'] = '';
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (false == CodeUtil::only_number($memberIdx) || false == CodeUtil::only_number($betType) || false == CodeUtil::only_number($totalMoney)) {
            $response['messages'] = '인자값이 잘못되었습니다.';
            return $this->fail($response);
        }

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $memberModel = new MemberModel();
        try {
            $memberModel->db->transStart();
            list($revtal, $message) = $this->checkBettingAccessToken($keep_login_access_token, $memberModel);
            if (false == $revtal) {
                $memberModel->db->transRollback();
                $response['messages'] = $message;
                return $this->fail($response);
            }

            list($revtal, $message, $member, $memberIdx) = $this->checkMemberStatus(session()->get('member_idx'), $memberModel);
            if (false == $revtal) {
                $memberModel->db->transRollback();
                $response['messages'] = $message;
                return $this->fail($response);
            }

            if ($member->getMoney() < $totalMoney) {
                $memberModel->db->transRollback();
                $response['messages'] = '배팅 금액이 부족합니다.';
                return $this->fail($response);
            }

            // Commercial ip check
            list($retVal, $message) = CodeUtil::checkCommercialIp($member, $this->logger);
            if (false == $retVal) {
                $memberModel->db->transRollback();
                $response['messages'] = $message;
                return $this->fail($response);
            }

            // 중복배팅
            $lastBetInfo = $this->getLastBettingInfo($memberModel, $member->getIdx());
            $checkStartTime = date("Y-m-d H:i:s", strtotime($lastBetInfo[0]['betting_dt'] . "+" . 3 . "seconds"));
            $currentTime = date('Y-m-d H:i:s');

            if (0 < count($lastBetInfo) && $currentTime < $checkStartTime) {
                $sql = "UPDATE member set is_monitor_bet = 'Y' WHERE idx = ? ";
                $memberModel->db->query($sql, [$memberIdx]);

                $response['messages'] = '정상배팅이 아닙니다.!';
                $memberModel->db->transComplete();
                return $this->fail($response);
            }

            $gameType = 5;
            if (3 == $betType) {
                $gameType = 5;
            } else if (4 == $betType) {
                $gameType = 6;
            } if (5 == $betType) {
                $gameType = 7;
            } if (6 == $betType) {
                $gameType = 8;
            } if (15 == $betType) {
                $gameType = 15;
            }
            $game_type_sql = "SELECT status FROM member_game_type where member_idx = ? and game_type = ?";
            $arr_db_result_game_type = $memberModel->db->query($game_type_sql, [$memberIdx, $gameType])->getResultArray();

            if ('OFF' == $arr_db_result_game_type[0]['status']) {
                $memberModel->db->transRollback();
                $response['messages'] = '해당 게임 배팅이 금지되어있습니다.';
                return $this->fail($response);
            }

            $tgcModel = new TGameConfigModel();

            // 배팅 금지 상태인지 확인하자.(레벨9 유저는 베팅가능)
            if (9 != $member->getLevel()) {
                $str_sql_config = "SELECT set_type, set_type_val 
                                FROM t_game_config 
                                WHERE set_type 
                                    IN('mini_service_powerball',
                                        'mini_service_eos_powerball',
                                        'mini_service_power_ladder',
                                        'mini_service_kino_ladder',
                                        'mini_service_v_soccer'
                                    )";
                $arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

                $arr_config = array();
                foreach ($arr_config_result as $key => $value) {
                    $arr_config[$value['set_type']] = $value['set_type_val'];
                }

                if (3 == $betType && 'N' == $arr_config['mini_service_eos_powerball']) {
                    $memberModel->db->transRollback();
                    $response['messages'] = 'EOS 파워볼 배팅은 점검중입니다.';
                    return $this->fail($response);
                } else if (4 == $betType && 'N' == $arr_config['mini_service_power_ladder']) {
                    $memberModel->db->transRollback();
                    $response['messages'] = '파워사다리 점검중입니다.';
                    return $this->fail($response);
                } else if (5 == $betType && 'N' == $arr_config['mini_service_kino_ladder']) {
                    $memberModel->db->transRollback();
                    $response['messages'] = '키노사다리 점검중입니다.';
                    return $this->fail($response);
                } else if (6 == $betType && 'N' == $arr_config['mini_service_v_soccer']) {
                    $memberModel->db->transRollback();
                    $response['messages'] = '가상축구 점검중입니다.';
                    return $this->fail($response);
                } else if (15 == $betType && 'N' == $arr_config['mini_service_powerball']) {
                    $memberModel->db->transRollback();
                    $response['messages'] = '파워볼 배팅은 점검중입니다.';
                    return $this->fail($response);
                }
            }

            // 미니게임 설정
            $m_level = session()->get('level');
            $sql_config = "SELECT * FROM mini_game_bet_config 
                        WHERE level = $m_level 
                        AND bet_type = ?"; // 가상축구 설정값
            $result_config = $tgcModel->db->query($sql_config, [$betType])->getResult();
            foreach ($result_config as $key => $value) {
                $game_config = $value;
            }

            $fixture_id = $betList[0]['fixtureId'];
            $round = $betList[0]['round'];
            $marketsId = $betList[0]['marketsId'];
            if (!is_int((int) $fixture_id) || !is_int((int) $round) || !is_int((int) $marketsId) || !is_double((double) $betList[0]['betPrice'])) {
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** addMiniBet: die ");
                die();
            }

            $dt_current = date("Y-m-d H:i:s");
            $betList[0]['betOtherPrice'] = $betList[0]['betOtherPriceDraw'] = 0;

            // 가상축구이면 동일경기에 이미 베팅을 했는지 확인
            if (6 == $betType) {
                // 배팅가능 시간을 체크한다.
                $sql = "SELECT start_dt, end_dt, result, league FROM mini_game WHERE bet_type = 6 AND id = ?";
                $gameInfo = $tgcModel->db->query($sql, [$fixture_id])->getResultArray();
                $result = json_decode($gameInfo[0]['result'], true);
                
                // 현재 경기가져온다.
                $sql = "SELECT * FROM mini_game where bet_type = ? and start_dt > now() and league = ? order by start_dt asc limit 2;";
                $db_result = $memberModel->db->query($sql, [$betType, $gameInfo[0]['league']])->getResultArray();
                
                if(0 == count($db_result)){
                    $response['messages'] = '경기정보가 없습니다.';
                    $memberModel->db->transRollback();
                    return $this->fail($response);
                }
                
                $current_game = array();
                foreach ($db_result as $key => $value) {
                    $current_game[] = $value['id'];
                }
                
                // 보내준 경기번호가 현재진행중인 경기인지 체크
                if(!in_array($fixture_id, $current_game)){
                    $response['messages'] = '잘못된 경기입니다.';
                    $memberModel->db->transRollback();
                    return $this->fail($response);
                }

                // 경기시작 10초전까지 가능
                $checkStartTime = date("Y-m-d H:i:s", strtotime($gameInfo[0]['start_dt'] . "-" . 10 . "seconds"));
                if ($checkStartTime < $dt_current) {
                    $response['messages'] = '베팅가능 시간을 초과하였습니다.';
                    $response['code'] = 1001;
                    $memberModel->db->transRollback();
                    return $this->fail($response);
                }

                $sql = "SELECT ls_markets_id, total_bet_money
                    FROM mini_game_member_bet 
                    WHERE member_idx = ?
                        AND round = ? 
                        AND bet_type = ? for update";
                $betInfo = $tgcModel->db->query($sql, [$memberIdx, $round, $betType])->getResultArray();

                // 잘못된 마켓아이디인지 체크한다.
                if ($marketsId < 13001 || $marketsId > 13005) {
                    $response['messages'] = '잘못 된 배팅입니다.';
                    $memberModel->db->transRollback();
                    return $this->fail($response);
                }

                // 배당률을 디비걸로 셋팅한다.
                if ($marketsId == 13001) {
                    $betList[0]['betPrice'] = $result['win'];
                    $betList[0]['betOtherPrice'] = $result['lose'];
                    $betList[0]['betOtherPriceDraw'] = $result['draw'];
                } else if ($marketsId == 13002) {
                    $betList[0]['betPrice'] = $result['draw'];
                    $betList[0]['betOtherPrice'] = $result['win'];
                    $betList[0]['betOtherPriceDraw'] = $result['lose'];
                } else if ($marketsId == 13003) {
                    $betList[0]['betPrice'] = $result['lose'];
                    $betList[0]['betOtherPrice'] = $result['win'];
                    $betList[0]['betOtherPriceDraw'] = $result['draw'];
                } else if ($marketsId == 13004) {
                    $betList[0]['betPrice'] = $result['lose'];
                    $betList[0]['betOtherPrice'] = $result['win'];
                } else {
                    $betList[0]['betPrice'] = $result['win'];
                    $betList[0]['betOtherPrice'] = $result['lose'];
                }

                if (0 < count($betInfo)) {
                    $total_money = 0;
                    foreach ($betInfo as $value) {
                        $total_money += $value['total_bet_money'];
                    }
                    // 같은 타입 배팅은 최대한도만 체크한다.(100만)
                    if ($marketsId == $betInfo[0]['ls_markets_id']) {
                        if ($total_money + $totalMoney > $game_config->max) {
                            $response['messages'] = '최대 배팅 및 당첨 상한이 초과되었습니다.';
                            $memberModel->db->transRollback();
                            return $this->fail($response);
                        }
                    } else {
                        $response['messages'] = '이미 베팅한 경기입니다.';
                        $memberModel->db->transRollback();
                        return $this->fail($response);
                    }
                } else { // 첫배팅도 한도 체크
                    if ($totalMoney > $game_config->max) {
                        $response['messages'] = '최대 배팅 및 당첨 상한이 초과되었습니다.';
                        $memberModel->db->transRollback();
                        return $this->fail($response);
                    }
                }
            } else {
                // 배팅가능 시간을 체크한다.
                $sql = "SELECT end_dt, cnt FROM mini_game WHERE bet_type = ? AND id = ?";
                $gameInfo = $tgcModel->db->query($sql, [$betType, $fixture_id])->getResultArray();
                $cnt = $gameInfo[0]['cnt'];

                if ($round != $cnt) {
                    $response['messages'] = '배팅 정보가 잘못되었습니다. 새로고침해주세요.';
                    $response['code'] = 1001;
                    $memberModel->db->transRollback();
                    return $this->fail($response);
                }

                if ($gameInfo[0]['end_dt'] < $dt_current) {
                    $response['messages'] = '배팅가능 시간을 초과하였습니다.';
                    $response['code'] = 1001;
                    $memberModel->db->transRollback();
                    return $this->fail($response);
                }

                $sql = "SELECT ls_markets_id, total_bet_money
                    FROM mini_game_member_bet 
                    WHERE member_idx = ?
                        AND round = ? 
                        AND bet_type = ?
                        AND date(create_dt) >= date_format(now(), '%Y-%m-%d')";
                /* $betInfo = $tgcModel->db->query($sql, [$memberIdx, $round, $betType])->getResultArray();
                  if (0 < count($betInfo)) {
                  $response['messages'] = '이미 베팅한 경기입니다.';
                  return $this->fail($response);
                  } */

                // 배당률을 디비걸로 셋팅한다.(가상축구는 제외)
                $sql = "SELECT bet_price FROM mini_game_bet WHERE markets_id = ?";
                $betInfo = $tgcModel->db->query($sql, [$marketsId])->getResultArray();
                if ($betInfo[0]['bet_price'] > 0)
                    $betList[0]['betPrice'] = $betInfo[0]['bet_price'];
            }

            // 베팅정보를 가져온다.
            $marketsId = $betList[0]['marketsId'];
            $sql = "SELECT game, markets_name, bet_price FROM mini_game_bet WHERE markets_id = ?";
            $miniBet = $tgcModel->db->query($sql, [$marketsId])->getResultArray();
            if (0 == count($miniBet)) {
                $memberModel->db->transRollback();
                $response['messages'] = '배팅 정보가 잘못되었습니다. ';
                return $this->fail($response);
            }


            $array_league_tag_id = [];

            $bonus_dividend = 1;
            $u_level = $member->getLevel();
            $sql = "SELECT * 
                    FROM mini_game_bet_config 
                    WHERE bet_type = ? 
                    AND level = ?";
            $mini_config_result = $tgcModel->db->query($sql, [$betType, $u_level])->getResultArray();

            if ($totalMoney > $mini_config_result[0]['max']) {
                $memberModel->db->transRollback();
                $response['messages'] = '최대 배팅 가능 금액은 ' . number_format($mini_config_result[0]['max']) . '원 입니다.';
                return $this->fail($response);
            }
            if ($totalMoney < $mini_config_result[0]['min']) {
                $memberModel->db->transRollback();
                $response['messages'] = '최소 배팅 금액은 ' . number_format($mini_config_result[0]['min']) . '원 입니다.';
                return $this->fail($response);
            }
            if ($mini_config_result[0]['limit'] < $totalMoney * $betList[0]['betPrice']) {
                $memberModel->db->transRollback();
                $response['messages'] = ' 배팅 상한 제한 금액은 ' . number_format($mini_config_result[0]['limit']) . '원 입니다.';
                return $this->fail($response);
            }

            $af_money = $member->getMoney() - $totalMoney;
            $miniGameMemberBetModel = new MiniGameMemberBetModel();
            $result = $miniGameMemberBetModel->addMemberMiniGameBet($member, $dt_current, $totalMoney, 0, $betType, $betList);

            if (0 == $result) {
                $memberModel->db->transRollback();
                return $this->fail('미니게임베팅 실패');
            }

            Calculate::updateChargeBetMoney($memberIdx, 'MINI', $totalMoney, $memberModel, $this->logger);

            UserPayBack::AddBetting($memberIdx,$totalMoney,$miniGameMemberBetModel);            
             
            $keep_login_access_token = $this->tokenRefresh($member, $memberModel);
            $response['keep_login_access_token'] = $keep_login_access_token;

            $memberModel->db->transComplete();
            session()->set('money', $af_money);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  addMiniBet error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  addMiniBet query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
            return $this->fail('디비처리 실패로 인한 배팅 실패');
        }

        $response = [
            'code' => 200,
            'messages' => '배팅 성공',
            'data' => [
                'bet_id' => $result,
                'total_money' => $af_money,
                'total_bet_money' => $totalMoney,
                'total_point' => $af_point,
                'keep_login_access_token' => $keep_login_access_token
            ]
        ];
        return $this->respond($response, 200);
    }

    //ON/OFF 설정시 호출한다.
    public function setBettingSlip() {
        $is_betting_slip = isset($_POST['is_betting_slip']) ? $_POST['is_betting_slip'] : NULL; // 베팅슬립 on/off 값이다 .

        if (false == CodeUtil::only_alpha_number($is_betting_slip) || ('ON' != $is_betting_slip && 'OFF' != $is_betting_slip)) {
            $response['messages'] = '인자값이 잘못되었습니다.';
            $this->logger->error('is_betting_slip : ' . $is_betting_slip);
            return $this->fail($response);
        }

        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $memberIdx = session()->get('member_idx');

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            $response['messages'] = '조회되는 유저가 없습니다.';
            return $this->fail($response);
        }

        $memberModel->set('is_betting_slip', $is_betting_slip)->where('idx', $memberIdx)->update();

        $response = [
            'code' => 200,
            'messages' => '배팅 성공',
            'is_betting_slip' => $is_betting_slip
        ];
        return $this->respond($response, 200);
    }

    private function checkBetListData($isBettingSlip, $totalOdds, $arr_config, $betType, $betList, $isClassic, $memberModel) {
        $array_league_tag_id = [];
        $array_fixture = [];
        $betList_renew = []; // bet_name 추가 용도
        $overlap = []; // 조합중복 체크 용도
        $fDetail = '';
        $arr_bet_price = array();

        foreach ($betList as $value) {
            $fixtureId = $value['fixtureId'];
            $marketsId = $value['marketsId'];

            $betBaseLine = $value['betBaseLine'];
            $betName = $value['betName'];
            $fixtureStartDate = $value['fixture_start_date'];
            $leagueTagId = $value['leagueTagId'];

            $str_bet_query = Calculate::getMadeAddBetQuery();
            $bet_result = $memberModel->db->query($str_bet_query, [$betType, $fixtureId, $marketsId, $betBaseLine, $betName, $betType, $fixtureStartDate, $betType, $betType])->getResultArray();
            if (!isset($bet_result) || 0 == count($bet_result)) {
                //$memberModel->db->transRollback();
                $this->logger->error('query : ' . $str_bet_query . ' bet_result : ' . json_encode([$betType, $fixtureId, $marketsId, $betBaseLine, $betName, $betType, $fixtureStartDate, $betType, $betType]));
                $messages = '배팅 정보가 잘못되었습니다. ';
                //return $this->fail($response);

                return [false, $messages, $max_dividend, $totalOdds, $bet_count, $arr_bet_price, $array_fixture, $array_league_tag_id, $fDetail, $betList_renew];
            }

            $bet = $bet_result[0];
            $max_dividend = $bet['max_dividend'];
            $value['betId'] = $bet['bet_id'];
            $value['bet_name'] = $bet['bet_name'];
            $value['fixture_sport_id'] = $bet['fixture_sport_id'];
            $value['fixture_location_id'] = $bet['fixture_location_id'];
            $value['fixture_league_id'] = $bet['fixture_league_id'];
            $value['fixture_participants_1_id'] = $bet['fixture_participants_1_id'];
            $value['fixture_participants_2_id'] = $bet['fixture_participants_2_id'];
            $value['fixture_start_date'] = $fixtureStartDate;
            if (1 == $betType) {
                list($other_ls_bet_id, $other_bet_price, $find_bet_name) = $this->getOtherBetInfo($memberModel, $value['fixtureId'], $betType, $value['marketsId'], $value['bet_name'], $value['betBaseLine']);
                $value['other_ls_bet_id'] = $other_ls_bet_id;
                $value['other_bet_price'] = $other_bet_price;
                $value['other_bet_name'] = $find_bet_name;
                $this->logger->error('addMemberBet other_ls_bet_id :' . $other_ls_bet_id . ' other_bet_price : ' . $other_bet_price . ' other_bet_name : ' . $find_bet_name);
            }
            $betList_renew[] = $value;
            $overlap[$fixtureId][] = $value;

            if ('ON' == $isClassic && SOCCER == $value['fixture_sport_id'] && (WDL != $marketsId && OVER_UNDER != $marketsId && HANDICAP != $marketsId) ||
                    'ON' == $isClassic && BASKETBALL == $value['fixture_sport_id'] && (OVER_UNDER_OVERTIME != $marketsId && M_12_OVERTIME != $marketsId && HANDICAP_OVERTIME != $marketsId) ||
                    'ON' == $isClassic && BASEBALL == $value['fixture_sport_id'] && (OVER_UNDER_OVERTIME != $marketsId && M_12_OVERTIME != $marketsId && HANDICAP_OVERTIME != $marketsId) ||
                    'ON' == $isClassic && VOLLEYBALL == $value['fixture_sport_id'] && (OVER_UNDER != $marketsId && WL != $marketsId) ||
                    'ON' == $isClassic && UFC == $value['fixture_sport_id'] && (WL != $marketsId) ||
                    'ON' == $isClassic && ICEHOCKEY == $value['fixture_sport_id'] && (WDL != $marketsId && OVER_UNDER != $marketsId && HANDICAP != $marketsId) ||
                    'ON' == $isClassic && ESPORTS == $value['fixture_sport_id'] && (WL != $marketsId && HANDICAP != $marketsId)) {
                $this->logger->critical("!!!!!!!!!!!!!!!!!! ***************** checkBetListData: marketsId " . $marketsId);
                return [false, '배팅정보오류', $max_dividend, $totalOdds, $bet_count, $arr_bet_price, $array_fixture, $array_league_tag_id, $fDetail, $betList_renew, $overlap];
            }

            if (false == in_array($fixtureId, $array_fixture)) {
                $array_fixture[] = $fixtureId;
                $array_league_tag_id[] = $leagueTagId;
            }

            list($retval_bet, $message_bet, $fDetail, $totalOdds, $bet_count, $arr_bet_price) = $this->checkPerBettingData($bet, $arr_config, $totalOdds, $bet_count, $arr_bet_price, $isBettingSlip, $betType, $this->logger);
            if (false == $retval_bet) {
                //$memberModel->db->transRollback();
                //$messages = $message_bet;
                //return $this->fail($response);
                return [false, $message_bet, $max_dividend, $totalOdds, $bet_count, $arr_bet_price, $array_fixture, $array_league_tag_id, $fDetail, $betList_renew, $overlap];
            }
        } // end foreach betList

        return [true, 'success', $max_dividend, $totalOdds, $bet_count, $arr_bet_price, $array_fixture, $array_league_tag_id, $fDetail, $betList_renew, $overlap];
    }

    private function checkItemIdx($memberIdx, $itemId, $itemValue, $memberModel, $logger) {
        $item = $this->gmPt->getAvailableOneItemAtOrderbyCreate($memberModel, $memberIdx, $itemId, $itemValue);
        $itemIdx = 0;
        $comment = '';
        if (null != $item) {
            $itemIdx = $item[0]['idx'];
            if (1 == $item[0]['type']) {
                $comment = '환급패치 아이템 사용';
            } else if (2 == $item[0]['type']) {
                $comment = '배당패치 아이템 사용';
            } else {

                return [false, '아이템 정보 오류입니다.'];
            }
        }

        if (0 < $itemIdx) {
            $ukey = md5($memberIdx . strtotime('now'));
            $this->gmPt->useItem($ukey, $memberIdx, $itemId, $itemIdx, $comment, $memberModel, $logger);
        }

        return [true, 'success'];
    }

    private function checkMinMaxBetMoney($totalOdds, $bonus_dividend, $totalMoney, $betType, $isClassic, $member, $tgcModel) {
        $config = $tgcModel->getMemberLevelConfig($betType, $member->getLevel());
        //$betGroup = $betType == 2 ? 'real' : 'pre';
        if (2 == $betType) {
            $betGroup = 'real';
        } else {
            $betGroup = $isClassic == 'ON' ? 'classic' : 'pre';
        }

        foreach ($config as $key => $item) {
            if ($item['set_type'] == $betGroup . '_max_money') {
                if ($totalMoney > $item['set_type_val']) {
                    //$memberModel->db->transRollback();
                    $messages = '최대 배팅 가능 금액은 ' . number_format($item['set_type_val']) . '원 입니다.';
                    //return $this->fail($response);
                    return [false, $messages];
                }
            }
            if ($item['set_type'] == $betGroup . '_min_money') {
                if ($totalMoney < $item['set_type_val']) {
                    //$memberModel->db->transRollback();
                    $messages = '최소 배팅 금액은 ' . number_format($item['set_type_val']) . '원 입니다.';
                    //return $this->fail($response);
                    return [false, $messages];
                }
            }

            if ($item['set_type'] == $betGroup . '_limit_money') {
                if ($item['set_type_val'] < $totalMoney * round($totalOdds * $bonus_dividend, 2)) {
                    //$memberModel->db->transRollback();
                    $messages = '최대 당첨 가능 금액은 ' . number_format($item['set_type_val']) . '원 입니다.';
                    //return $this->fail($response);
                    return [false, $messages];
                }
            }
        }

        return [true, 'success'];
    }

    private function checkLimitBetMoney($totalMoney, $betType, $betList, $array_fixture, $member, $isClassic, $memberModel) {
        $plcBetType = $betType;
        if (GT_SPORTS == $betType && 'ON' == $isClassic) {
            $plcBetType = GT_CLASSIC;
        }

        $str_fixture = implode(',', $array_fixture);
        $str_league_query = "   SELECT 
                                        fix.fixture_id,
                                        IFNULL(fix_bet.sum_bet_money, 0) AS sum_bet_money,
                                        d_plc.amount,
                                        leagues.id as league_id 
                                    FROM lsports_fixtures as fix
                                    LEFT JOIN fixtures_bet_sum as fix_bet 
                                        ON fix_bet.fixture_id = fix.fixture_id 
                                        AND fix_bet.bet_type = fix.bet_type 
                                        AND fix_bet.member_idx = ?
                                    LEFT JOIN lsports_leagues as leagues 
                                        ON fix.fixture_league_id = leagues.id
                                        AND leagues.bet_type = ?
                                    LEFT JOIN dividend_policy as d_plc 
                                        ON leagues.dividend_rank = d_plc.rank 
                                        AND d_plc.type = ? AND d_plc.level = ? 
                                    WHERE fix.fixture_id IN ($str_fixture) 
                                    AND   fix.bet_type = ? ;";
        $arr_fix_bet_sum_amount = $memberModel->db->query($str_league_query, [$member->getIdx(), $betType, $plcBetType, $member->getLevel(), $betType])->getResultArray();

        $sql = array();

        if (0 < count($arr_fix_bet_sum_amount)) {
            foreach ($arr_fix_bet_sum_amount as $bet_sum_value) {
                if ($bet_sum_value['sum_bet_money'] + $totalMoney <= $bet_sum_value['amount']) {
                    $insertSql = '('
                            . $member->getIdx() . ', '
                            . $bet_sum_value['fixture_id'] . ', "'
                            . $plcBetType . '", '
                            . $totalMoney . ')';
                    array_push($sql, $insertSql);
                    continue;
                }
                //$memberModel->db->transRollback();
                $messages = '경기당 최대 배팅 가능 금액은 ' . number_format($bet_sum_value['amount']) . '원 입니다.';
                //return $this->fail($response);
                return [false, $messages, []];
            }
        } else {
            foreach ($betList as $value) {
                $insertSql = '('
                        . $member->getIdx() . ', '
                        . $value['fixtureId'] . ', "'
                        . $plcBetType . '", '
                        . $totalMoney . ')';
                array_push($sql, $insertSql);
            }
        }

        return [true, 'success', $sql];
    }

    private function checkOverLap($overlap) {
        foreach ($overlap as $item) {
            $fixture_sport_id = $item[0]['fixture_sport_id'];
            if ($fixture_sport_id == BASEBALL || $fixture_sport_id == BASKETBALL || $fixture_sport_id == ICEHOCKEY) {
                $count = count($item);
                if ($count == 2) {
                    if ($item[0]['marketsId'] == $item[1]['marketsId']) {
                        $this->logger->debug('combine count error sport : ' . $fixture_sport_id . ' count : ' . $count . 'marketsId : ' . $item[0]['marketsId'] . 'marketsId_2 : ' . $item[1]['marketsId']);
                        //$memberModel->db->transRollback();
                        $messages = '동일한 베팅 조합입니다.';
                        //return $this->fail($response);
                        return [false, $messages];
                    }
                }

                if (($count > 1 && $count % 2 != 0) || 2 < $count) {
                    $this->logger->debug('combine count error sport : ' . $fixture_sport_id . ' count : ' . $count);
                    //$memberModel->db->transRollback();
                    $messages = '조합이 잘못되었습니다.';
                    //return $this->fail($response);
                    return [false, $messages];
                }
            }
        }

        return [true, 'success'];
    }

    private function checkPerBettingData($bet, $arr_config, $totalOdds, $bet_count, $arr_bet_price, $isBettingSlip, $betType, $logger) {
        $isLimitFolder = false;
        if ('OFF' == $bet['admin_bet_status'] || 'OFF' == $bet['fix_admin_bet_status']) {
            $messages = '선택 경기중 마감 된 경기가 있습니다. 재 배팅 부탁드립니다.';
            return [false, $messages, '', $totalOdds, $bet_count, $arr_bet_price];
        }

        if (4 == $bet['fixture_status']) {
            $messages = '해당 경기는 취소 된 경기입니다.';
            return [false, $messages, '', $totalOdds, $bet_count, $arr_bet_price];
        }

        $totalOdds *= $bet['bet_price'];
        // 1.15이하의 배당은 보너스배당 카운트 제외
        if ($arr_config['limit_folder_bonus'] < $bet['bet_price']) {
            $bet_count += 1;
        }

        $fDetail = '';
        if (1 == $betType) {
            $fDetail = 'prematch ==>';
        } else if (2 == $betType) {
            $fDetail = 'inplay ==>';
        }
        $fDetail .= ' [' . $bet['fixture_sport_name'] . '] ' . $bet['fixture_location_name'] . ' ' . $bet['fixture_league_name'] . ' ' . $bet['fixture_participants_1_name'] . ' VS ' . $bet['fixture_participants_2_name'];

        // 배당률을 넣어준다.
        $arr_bet_price[$bet['bet_id']] = $bet['bet_price'];

        if ('OFF' == $isBettingSlip) {
            if (0 < $bet['limit_bet_price'] && $bet['bet_price'] < $bet['limit_bet_price']) {
                $messages = '선택 경기중 마감 된 경기가 있습니다. 재 배팅 부탁드립니다.';
                return [false, $messages, $fDetail, $totalOdds, $bet_count, $arr_bet_price];
            }

            if (0 < $bet['max_bet_price'] && $bet['max_bet_price'] < $bet['bet_price']) {
                $messages = '선택 경기중 마감 된 경기가 있습니다. 재 배팅 부탁드립니다.';
                return [false, $messages, $fDetail, $totalOdds, $bet_count, $arr_bet_price];
            }
        }

        $startTime = date("Y-m-d H:i:s", strtotime($bet['fixture_start_date'] . "-" . 10 . " minutes"));
        $currentTime = date('Y-m-d H:i:s');

        if (1 != $bet['bet_status']) {
            if (2 == $betType || (1 == $betType && $currentTime < $startTime)) {
                $messages = '해당 유형의 배팅은 현재 불가합니다.';
                return [false, $messages, $fDetail, $totalOdds, $bet_count, $arr_bet_price];
            }
        }

        if (1 == $betType) {
            $dt_current = date("Y-m-d H:i:s");
            $end_time = '-' . $bet['end_time'];

            $dt_start = $bet['fixture_start_date'];
            $dec_time = $end_time . " minutes";
            $dt_end = date("Y-m-d H:i:s", strtotime($dt_start . $dec_time));

            if ($dt_end < $dt_current || $dt_start <= $dt_current) {
                $messages = '배팅 시간을 초과하였습니다.';
                return [false, $messages, $fDetail, $totalOdds, $bet_count, $arr_bet_price];
            }
        } else if (2 == $betType) {

            $data_object = json_decode($bet['livescore']);
            $period_id = $bet['not_display_period'];

            if (BET_OPEN != BetDataUtil::exp_score_filter($period_id, $data_object, $bet, $logger)) {
                $messages = '해당 유형의 배팅은 현재 불가합니다.!!!';
                return [false, $messages, $fDetail, $totalOdds, $bet_count, $arr_bet_price];
            }

            //$retval = BetDataUtil::checkDisplayMarkets($mergeBet, $logger);
            if (BET_OPEN != BetDataUtil::checkDisplayMarkets($bet, $logger)) {
                $messages = '해당 유형의 배팅은 현재 불가합니다.!!!';
                return [false, $messages, $fDetail, $totalOdds, $bet_count, $arr_bet_price];
            }
        }

        return [true, 'success', $fDetail, $totalOdds, $bet_count, $arr_bet_price];
    }

    private function checkIndividual($memberIdx, $betType, $betList, $isClassic, $memberModel) {
        $betCount = count($betList);
        $folderType = 1 < $betCount ? 'D' : 'S';

        $game_type = array();
        if (1 == $betType) {
            if ('ON' == $isClassic) {
                $game_type[] = 16;
            } else {
                $game_type[] = 3;
                if ('S' == $folderType) {
                    $game_type[] = 4;
                } else if (2 == $betCount) {
                    $game_type[] = 14;
                }
            }
        } else {
            $game_type[] = 1;
            if ('S' == $folderType) {
                $game_type[] = 2;
            }
        }

        $game_type = implode(',', $game_type);
        $game_type_sql = "SELECT game_type, status FROM member_game_type where member_idx = ? and game_type in ($game_type)";
        $arr_db_result_game_type = $memberModel->db->query($game_type_sql, [$memberIdx])->getResultArray();

        foreach ($arr_db_result_game_type as $result_game_type) {
            if ('OFF' == $result_game_type['status']) {
                if (14 == $result_game_type['game_type']) {
                    $messages = '2폴더 게임 배팅은 금지되어있습니다.';
                } else {
                    $messages = '해당 베팅 이용불가합니다.' . PHP_EOL . '자세한 안내는 고객센터로 문의바랍니다.';
                }

                return [false, $messages, $folderType];
            }
        }

        return [true, 'success', $folderType];
    }

    private function checkSameTimeBetting($memberIdx, $memberModel) {
        $currentDate = date("Y-m-d H:i:s");
        $bet_sql = "SELECT count(*) as cnt FROM member_bet where member_idx = ? and create_dt = ? for update";
        $result = $memberModel->db->query($bet_sql, [$memberIdx, $currentDate])->getResultArray();

        if ($result[0]['cnt'] > 0) {

            return false;
        }
        return true;
    }

    private function checkMemberStatus($memberIdx, $memberModel) {
        //$memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            $messages = '조회되는 유저가 없습니다.';
            return [false, $messages, $member, $memberIdx, $memberModel];
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            $messages = '사용이 불가능 한 계정으로 관리자에게 문의바랍니다.';
            return [false, $messages, $member, $memberIdx, $memberModel];
        }

        if ($member->getStatus() == 11) {
            $messages = '관리자 승인이 필요합니다.';
            return [false, $messages, $member, $memberIdx, $memberModel];
        }

        if ($member->getUBusiness() != 1) {
            $messages = '총판은 배팅 이용이 불가능합니다.';
            return [false, $messages, $member, $memberIdx, $memberModel];
        }

        return [true, 'success', $member, $memberIdx];
    }

    // 배팅한 반대 배팅정보를 구한다.(승패, 핸디캡, 오버언더)
    public function getOtherBetInfo($model, $fixture_id, $bet_type, $markets_id, $bet_name, $ls_markets_base_line) {
        $arr_winlose = array(52, 226);
        $arr_handicap = array(3, 342);
        $arr_overunder = array(2, 28);
        /* $arr_winlose = array(52,63,226);
          $arr_handicap = array(3,53,64,95,281,342);
          $arr_overunder = array(2,11,21,28,30
          ,31,77,101,102,153
          ,155,220,221,236); */

        $find_bet_name = '';
        $find_data = false;
        $other_ls_bet_id = 0;
        $other_bet_price = 0.0;
        if (true == in_array($markets_id, $arr_winlose) || true == in_array($markets_id, $arr_handicap)) {
            if ($bet_name == '1') {
                $find_bet_name = '2';
            } else {
                $find_bet_name = '1';
            }
            $find_data = true;
        } else if (true == in_array($markets_id, $arr_overunder)) {
            if ($bet_name == 'Over') {
                $find_bet_name = 'Under';
            } else {
                $find_bet_name = 'Over';
            }
            $find_data = true;
        }

        if (true == $find_data) {
            if (true == in_array($markets_id, $arr_winlose)) {
                $sql = "SELECT bet_id, bet_price FROM lsports_bet "
                        . "where fixture_id = ? and bet_type = ? and markets_id = ? and bet_name = ?";
                $other_bet_result = $model->db->query($sql, [$fixture_id, $bet_type, $markets_id, $find_bet_name])->getResultArray();
            } else {
                $sql = "SELECT bet_id, bet_price FROM lsports_bet "
                        . "where fixture_id = ? and bet_type = ? and markets_id = ? and bet_name = ? and bet_base_line = ?;";
                $other_bet_result = $model->db->query($sql, [$fixture_id, $bet_type, $markets_id, $find_bet_name, $ls_markets_base_line])->getResultArray();
            }

            if ($other_bet_result == null) {
                $this->logger->error('getOtherBetInfo fixture_id : ' . $fixture_id .
                        ' bet_type : ' . $bet_type . ' markets_id : ' . $markets_id .
                        ' bet_name : ' . $bet_name . ' ls_markets_base_line: ' . $ls_markets_base_line);
            }

            $other_ls_bet_id = $other_bet_result[0]['bet_id'];
            $other_bet_price = $other_bet_result[0]['bet_price'];
        }

        return array($other_ls_bet_id, $other_bet_price, $find_bet_name);
    }

    public function getLastBettingInfo($memberModel, $memberIdx) {
        $sql = "SELECT betting_dt FROM member_extend where member_idx = ?";

        return $memberModel->db->query($sql, [$memberIdx])->getResultArray();
    }

    public function tokenRefresh($findMember, $memberModel) {
        $keep_login_access_token = CodeUtil::uuidgen4();

        session()->set('keep_login_access_token', $keep_login_access_token);

        $memberModel->set('keep_login_access_token', $keep_login_access_token)
                ->where('idx', $findMember->getIdx())
                ->update();

        return $keep_login_access_token;
    }

}
