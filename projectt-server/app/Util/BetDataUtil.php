<?php

namespace App\Controllers;

namespace App\Util;

use App\Models\MemberBetDetailModel;
use App\Models\MiniGameMemberBetModel;
use App\Models\MemberBetModel;
use App\Models\MemberModel;
use App\Models\LSportsBetModel;
use App\Config\Constants;
use App\Models\TLogCashModel;
use App\Util\BetCodeUtil;
use App\Util\Calculate;
use App\Models\TotalMemberCashModel;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;

class BetDataUtil {

    // 스포츠 일반메뉴

    private static function mergeBaseline($bet, $check_over_under_arr, $arr_check_market) {
        if (true == in_array($bet->markets_id, $arr_check_market)) {
            $p_key = $bet->fixture_id . '_' . $bet->markets_id;
            if (false == isset($check_over_under_arr[$p_key])) {
                $check_over_under_arr[$p_key]['fixture_id'] = $bet->fixture_id;
                $check_over_under_arr[$p_key]['markets_id'] = $bet->markets_id;
                $check_over_under_arr[$p_key]['bet_base_line'] = explode($bet->bet_base_line, ' ');
            }

            $check_over_under_arr[$p_key]['base_lines'][$bet->bet_base_line]['bet_data'][$bet->bet_name] = array('bet_id' => $bet->bet_id, 'bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price, 'bet_status' => $bet->bet_status
                , 'bet_start_price' => $bet->bet_start_price ?? null, 'bet_line' => $bet->bet_line, 'bet_base_line' => explode($bet->bet_base_line, ' '));
        }

        return $check_over_under_arr;
    }

    private static function checkMinBetPrice($check_over_under_arr, $returnBets, $logger) {
        foreach ($check_over_under_arr as $key => $fix_market) {
            $min_gab_price = 100;
            $min_gab_key = '';
            foreach ($fix_market['base_lines'] as $b_key => $baseline) {
                $before_price = 0;
                foreach ($baseline['bet_data'] as $bet_key => $bet) {
                    if (0 == $before_price) {
                        $before_price = $bet['bet_price'];
                    } else {
                        $gab = abs($before_price - $bet['bet_price']);
                        if ($gab < $min_gab_price) {
                            $min_gab_price = $gab;
                            $min_gab_key = $b_key;
                        }
                        break;
                    }
                }
            }

            // Except for the minimum difference reference point
            foreach ($fix_market['base_lines'] as $b_key => $baseline) {
                if ($b_key == $min_gab_key)
                    continue;
                $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                unset($check_over_under_arr[$key][$b_key]);
                unset($returnBets[$del_bet_key]);
            }
        }

        return $returnBets;
    }

    private static function checkOverUnderHandi($check_over_under_arr, $returnBets, $logger) {
        foreach ($check_over_under_arr as $key => $fix_market) {
            $over_before_price = 0;
            $under_before_price = 10000;
            $home_before_price = 10000;
            $away_before_price = 0;
            $base_line = -1000.0;
            foreach ($fix_market['base_lines'] as $b_key => $baseline) {
                foreach ($baseline['bet_data'] as $bet_key => $bet) {
                    if (9504082 == $check_over_under_arr[$key]['fixture_id'] && 2 == $check_over_under_arr[$key]['markets_id']) {
                        $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                        //$logger->info(" !!!!!!!!!!!!!!!! error mergeAvgBetData trace del_bet_key " . $del_bet_key);
                    }

                    if (-1000 == $base_line) {
                        $base_line = (float) $bet['bet_base_line'];
                    } else if ($base_line) {
                        $gab = (float) $bet['bet_base_line'] - $base_line;
                        if (2 < $gab) {
                            $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                            unset($check_over_under_arr[$key][$b_key]);
                            unset($returnBets[$del_bet_key]);

                            //$logger->info(" !!!!!!!!!!!!!!!! error mergeAvgBetData 2 < gab del_bet_key " . $del_bet_key);
                            continue;
                        }
                        $base_line = (float) $bet['bet_base_line'];
                    }

                    if ('Over' == $bet['bet_name']) { // 올림
                        if (0 == $over_before_price) {
                            $over_before_price = $bet['bet_price'];

                            //$logger->debug(" !!!!!!!!!!!!!!!! error mergeAvgBetData over_before_price ==>".$b_key.' over_before_price==>'.$over_before_price.' bet_price==>'.$bet['bet_price']);
                            continue;
                        }

                        if ($over_before_price < $bet['bet_price']) {
                            $over_before_price = $bet['bet_price'];
                            //$logger->debug(" !!!!!!!!!!!!!!!! error mergeAvgBetData set over_before_price ==>".$b_key.' over_before_price==>'.$over_before_price.' bet_price==>'.$bet['bet_price']);
                        } else {
                            // 해당 배당을 다 지운다.

                            $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                            unset($check_over_under_arr[$key][$b_key]);
                            unset($returnBets[$del_bet_key]);

                            //$logger->info(" !!!!!!!!!!!!!!!! error mergeAvgBetData Over del_bet_key " . $del_bet_key);
                        }
                    } else if ('Under' == $bet['bet_name']) { // 내림
                        if (10000 == $under_before_price) {
                            $under_before_price = $bet['bet_price'];
                            continue;
                        }


                        if ($under_before_price > $bet['bet_price']) {
                            $under_before_price = $bet['bet_price'];
                        } else {

                            // 해당 배당을 다 지운다.
                            $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                            unset($check_over_under_arr[$key][$b_key]);
                            unset($returnBets[$del_bet_key]);

                            //$logger->info(" !!!!!!!!!!!!!!!! error mergeAvgBetData Under del_bet_key " . $del_bet_key);
                        }
                    } else if ('1' == $bet['bet_name']) { // 내림
                        //
                        if (10000 == $home_before_price) {
                            $home_before_price = $bet['bet_price'];
                            continue;
                        }

                        if ($home_before_price > $bet['bet_price']) {
                            $home_before_price = $bet['bet_price'];
                        } else {
                            // 해당 배당을 다 지운다.
                            $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                            unset($check_over_under_arr[$key][$b_key]);
                            unset($returnBets[$del_bet_key]);
                        }
                    } else if ('2' == $bet['bet_name']) { // 올림
                        //
                        if (0 == $away_before_price) {
                            $away_before_price = $bet['bet_price'];
                            continue;
                        }

                        if ($away_before_price < $bet['bet_price']) {
                            $away_before_price = $bet['bet_price'];
                        } else {
                            // 해당 배당을 다 지운다.
                            $del_bet_key = $check_over_under_arr[$key]['fixture_id'] . '_' . $check_over_under_arr[$key]['markets_id'] . '_' . $b_key;
                            unset($check_over_under_arr[$key][$b_key]);
                            unset($returnBets[$del_bet_key]);
                        }
                    }
                }
            }
        }

        return $returnBets;
    }

    public static function checkHandiPlus($returnBets, $mergeBet,$l_value,$bet_key,$logger) {
        $arr_handi_check = [3, 342, 53, 64,65,66,67, 95, 281, 866, 1541, 1558];
        // 핸디캡이면 정배에 플핸인지를 체크한다.
        
        $bet_base_line = explode(" ", $l_value['bet_line']);
        $bet_base_line = $bet_base_line[0];
        $logger->info('checkHandiPlus bet_base_line  ==>' . $bet_base_line);
        if (true == in_array($mergeBet['markets_id'], $arr_handi_check) && 0 < $bet_base_line) {
       
            $main_key = '';
            if (SOCCER == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id'].'_'.'1'.'_';
            } else if (BASKETBALL == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id'].'_'.'226'.'_';
            } else if (BASEBALL == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id'].'_'.'226'.'_';
            } else if (VOLLEYBALL == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id']. '_'.'52'.'_';
            } else if (ICEHOCKEY == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id'].'_'.'1'.'_';
            } else if (ESPORTS == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id']. '_'.'52'.'_';
            } else if (TENNIS == $mergeBet['fixture_sport_id']) {
                $main_key = $mergeBet['fixture_id']. '_'.'52'.'_';
            } 

            $main_betObject = $returnBets[$main_key];
            //$logger->info('checkHandiPlus overtime  ==>' . json_encode($main_betObject));
              
            if(true == isset($main_betObject)) {
                //$logger->info('checkHandiPlus overtime  ==>' . json_encode($main_betObject['bet_data']));
                $min_price = 100;
                $min_object = null;
                if (true === isset($main_betObject) && true === isset($main_betObject['bet_data']) && 0 < count($main_betObject)) {
                    foreach ($main_betObject['bet_data'] as $key => $value) {

                        //$logger->info('checkHandiPlus overtime  ==>' . json_encode($value));
                        if ($value['bet_price'] < $min_price) {
                            $min_price = $value['bet_price'];
                            $min_object = $value;
                        }
                    }

                    //$logger->info('checkHandiPlus min_object  ==>' . $min_object['bet_name']);
                    //$logger->info('checkHandiPlus l_value  ==>' . $l_value['bet_name']);
                    if ($min_object['bet_name'] == $l_value['bet_name']) {
                        $logger->info('checkHandiPlus overtime  ==>' . $bet_key);
                        //unset($returnBets[$bet_key]);
                        return true;
                    }
                }
            } 
            //main_betObject
        }
        
        return false;
    }
    
    public static function mergeAvgBetData($bets, $logger) {
        # 첫번째 데이터 설정
        $returnBets = [];
        if (!isset($bets) || 0 == count($bets))
            return $returnBets;

        $arr_check_market = array(2, 3, 11, 21, 28, 30, 31, 53, 64, 77, 95, 101, 102, 153, 155, 214, 220, 221, 281, 342, 866, 1558);
        $check_over_under_arr = [];
        $arr_check_handi_soccer_expecpt = [];
        foreach ($bets as $bet) {

            if (true == isset($bet->bet_base_line) && false == empty($bet->bet_base_line) && 1 == $bet->bet_status) {
                $check_over_under_arr = BetDataUtil::mergeBaseline($bet, $check_over_under_arr, $arr_check_market);
            }
            $key = $bet->fixture_id . '_' . $bet->markets_id . '_' . $bet->bet_base_line;
            if (isset($returnBets[$key])) {
                $mergeBet = $returnBets[$key];
            } else {
                $mergeBet = BetDataUtil::doMergeBetData($bet, 1);
            }

            if (6 == $mergeBet['markets_id'] || 9 == $mergeBet['markets_id']) {
                if ($mergeBet['limit_bet_price'] < $bet->bet_price && $bet->bet_price < $mergeBet['max_bet_price'] && 1 == $bet->bet_status) {
                    $mergeBet['bet_data'][$bet->bet_name] = array('bet_id' => $bet->bet_id, 'bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price, 'bet_status' => $bet->bet_status
                        , 'bet_line' => $bet->bet_line, 'bet_base_line' => $bet->bet_base_line, 'last_update' => $bet->last_update,'update_dt' => $bet->update_dt);
                }
            } else {

                $mergeBet['bet_data'][$bet->bet_name] = array('bet_id' => $bet->bet_id, 'bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price, 'bet_status' => $bet->bet_status
                    , 'bet_start_price' => $bet->bet_start_price, 'bet_line' => $bet->bet_line, 'bet_base_line' => $bet->bet_base_line, 'last_update' => $bet->last_update,'update_dt' => $bet->update_dt);
            }

            // 축구 경기만 따로 정리한다.
            if (SOCCER == $bet->fixture_sport_id && ('1' == $bet->bet_name || '2' == $bet->bet_name) && 2.15 <= $bet->bet_price && $bet->bet_price <= 2.55) {
                if (false == in_array($bet->fixture_id, $arr_check_handi_soccer_expecpt)) {
                    $arr_check_handi_soccer_expecpt[] = $bet->fixture_id;
                }
            }

            $returnBets[$key] = $mergeBet;
        }

        //$logger->debug('check_data ==>' . json_encode($check_over_under_arr));
        // 최소 최대 배당  
        // 이루틴에서 정렬을 한다.
        $arrLeague = array(918, 3371, 5283, 5556, 5769, 14121, 14493, 14497, 14498, 14499, 14530, 14761, 15181, 15997, 16176, 16362, 16482, 16745, 16791, 20736, 22893, 23798, 24136, 25686, 27433, 27488, 29301, 32573, 33252, 35133); //33078

        $arr_check_market_2 = array(2, 3, 5, 11, 17, 21, 22, 23, 28, 30, 31, 45, 46, 47, 63, 64, 52, 53, 65, 66, 67, 77, 95, 101, 102, 153, 155, 202, 214, 220, 221, 226, 235, 281, 342, 352, 353, 669, 866, 1170, 1558);
        $arr_check_market_3 = array(1, 7, 13, 19, 41, 42, 43, 44, 49, 71, 282, 322, 348, 349);

        $arr_check_market_5 = array(70);
        $arr_check_market_6 = array(427);
        $arr_check_market_9 = array(4, 390);
        $arr_check_market_other = array(6, 9);

        //$logger->error('test '.json_encode($arr_check_handi_soccer_expecpt));
        foreach ($returnBets as $bet_key => $mergeBet) {

            // 제공되지 않는 리그의 마켓은 프론트에서 제외된다.
            if (1 != $mergeBet['markets_id'] && true == in_array($mergeBet['fixture_league_id'], $arrLeague)) {
                unset($returnBets[$bet_key]);
                //$logger->error('1 mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id']);
                continue;
            }

            // base_line 값이 문제가 있다 역시 프론트에서 제외된다.
            if ((false === isset($mergeBet['bet_base_line']) || true === empty($mergeBet['bet_base_line'])) && true == in_array($mergeBet['markets_id'], $arr_check_market)) {
                unset($returnBets[$bet_key]);
                //$logger->error('2 mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' bet_base_line : '.$mergeBet['bet_base_line'].' markets_id : '.$mergeBet['markets_id']);
                continue;
            }

            // 핸디캡 배당사 제외 루틴
            // 축구 승무패(마켓아이디 1) 의 승,패 배당이 2.15 ~ 2.55 사이일 경우  핸디캡,핸디캡연장포함일경우 아래 기준점은 프론트에서 제외된다
            $arr_bet_base_line = explode(" ", $mergeBet['bet_base_line']);
            if (true == in_array($mergeBet['fixture_id'], $arr_check_handi_soccer_expecpt) && (3 == $mergeBet['markets_id'] || 342 == $mergeBet['markets_id'])) {
                if ('0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    $logger->info('3mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    continue;
                }
            } else if (BASKETBALL == $mergeBet['fixture_sport_id'] && (3 == $mergeBet['markets_id'] || 342 == $mergeBet['markets_id'])) {// 농구 아래 기준점 프론트 제외
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0] || '1.5' == $arr_bet_base_line[0] || '-1.5' == $arr_bet_base_line[0]) {
                    $logger->info('4mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    unset($returnBets[$bet_key]);
                    continue;
                }
            } else if (VOLLEYBALL == $mergeBet['fixture_sport_id'] && 1558 == $mergeBet['markets_id']) {// 배구 아래 기준점 프론트 제외
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0] || '1.5' == $arr_bet_base_line[0] || '-1.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    $logger->info('5mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    continue;
                }
            } else if (VOLLEYBALL == $mergeBet['fixture_sport_id'] && 866 == $mergeBet['markets_id']) {// 배구 아래 기준점 프론트 제외
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }

                // 아이스하키 +1, 0.5, 0, -0.5, -1 제외 (전체핸디캡만)
            } else if (ICEHOCKEY == $mergeBet['fixture_sport_id'] && 342 == $mergeBet['markets_id']) {// 아이스 하키 아래 기준점 프론트 제외
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    $logger->info('6mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    continue;
                }
            } else if (BASEBALL == $mergeBet['fixture_sport_id'] && 342 == $mergeBet['markets_id']) {// 야구 기준점은 프리매치만 제외한다
                if ('0.0' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            } else if (BASEBALL == $mergeBet['fixture_sport_id'] && 281 == $mergeBet['markets_id']) {// 야구는 0 기준점만 허용한다.
                if ('0.0' != $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            }

            // 최소,최대 값 기준 조건에 맞지 않으면 프론트에서 제외된다.
            foreach ($mergeBet['bet_data'] as $key => $value) {

                 // 핸디캡 일경우 정배 배당에서는 플핸 제외를 해야한다.
                if(true == BetDataUtil::checkHandiPlus($returnBets, $mergeBet,$value,$bet_key,$logger)){
                    unset($returnBets[$bet_key]);
                    break;
                }
                
                if (0 < $mergeBet['limit_bet_price'] && $value['bet_price'] < $mergeBet['limit_bet_price']) {
                    unset($returnBets[$bet_key]);
                    //$logger->info('7mergeAvgBetData limit fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    break;
                }

                if (0 < $mergeBet['max_bet_price'] && $mergeBet['max_bet_price'] < $value['bet_price']) {
                    unset($returnBets[$bet_key]);
                    //$logger->info('8mergeAvgBetData max fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    break;
                }

                // 마켓마다 배팅수가 안맞으면 제거한다 승무패 경기면 bet_data가 3개이고 오버언더변 2개이어야 한다 이개수가 안맞으면 프론트에서 제외 
                $count = count($mergeBet['bet_data']);
                if (2 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_2)) {
                    unset($returnBets[$bet_key]);
                    $logger->info('9mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    break;
                } else if (3 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_3)) {
                    unset($returnBets[$bet_key]);
                    $logger->info('10mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    break;
                } else if (5 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_5)) {
                    unset($returnBets[$bet_key]);
                    $logger->info('11mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    break;
                } else if (6 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_6)) {
                    unset($returnBets[$bet_key]);
                    $logger->info('12mergeAvgBetData fixture_id : '.$mergeBet['fixture_id'].' markets_id : '.$mergeBet['markets_id'].' line : '.__LINE__);
                    break;
                }

                // 죽은 배당이 1개라도 1시간 이상이면 제외한다.
                if (2 == $value['bet_status']) {
                    $checkFixtureDate = date("Y-m-d H:i:s", strtotime($mergeBet['fixture_start_date'] . '-20 minutes'));
                    $lastUpdateDate = date("Y-m-d H:i:s", strtotime($value['last_update'] . '+9 hours'));
                    $currentDate = date('Y-m-d H:i:s');
                    $gapMinute = (int) ((strtotime($currentDate) - strtotime($lastUpdateDate)) / MINUTE);
                    
                    if($gapMinute < DELAY_BETTING_LIMIT && $checkFixtureDate < $currentDate){
                        continue;
                    }
                    
                    //if ($gapMinute > 30 && $currentDate < $checkFixtureDate) {
                    unset($returnBets[$bet_key]);
                    $logger->debug('mergeAvgBetData overtime  ==>' . json_encode($mergeBet));
                    break;
                    //}
                }
            }
        }

        // 오버언더,핸디캡 마켓중 배당이 역배당인 기준점은 프론트에서 제외한다.
        $returnBets = BetDataUtil::checkOverUnderHandi($check_over_under_arr, $returnBets, $logger);

        return $returnBets;
    }

    public static function mergeAvgClassicBetData($bets, $logger) {
        # 첫번째 데이터 설정
        $returnBets = [];
        if (!isset($bets) || 0 == count($bets))
            return $returnBets;

        $arr_check_market = array(2, 3, 28, 342);
        $check_over_under_arr = [];
        $arr_check_handi_soccer_expecpt = [];
        foreach ($bets as $bet) {

            if (true == isset($bet->bet_base_line) && false == empty($bet->bet_base_line)) {
                $check_over_under_arr = BetDataUtil::mergeBaseline($bet, $check_over_under_arr, $arr_check_market);
            }
            $key = $bet->fixture_id . '_' . $bet->markets_id . '_' . $bet->bet_base_line;
            if (isset($returnBets[$key])) {
                $mergeBet = $returnBets[$key];
            } else {
                $mergeBet = BetDataUtil::doMergeBetData($bet, 1);
            }

            $mergeBet['bet_data'][$bet->bet_name] = array('bet_id' => $bet->bet_id, 'bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price, 'bet_status' => $bet->bet_status
                , 'bet_start_price' => $bet->bet_start_price ?? null, 'bet_line' => $bet->bet_line, 'bet_base_line' => $bet->bet_base_line);

            // 축구 경기만 따로 정리한다.
        
            $returnBets[$key] = $mergeBet;
        }

        // 최소 최대 배당  
        // 이루틴에서 정렬을 한다.
        $arrLeague = array(918, 3371, 5283, 5556, 5769, 14121, 14493, 14497, 14498, 14499, 14530, 14761, 15181, 15997, 16176, 16362, 16482, 16745, 16791, 20736, 22893, 23798, 24136, 25686, 27433, 27488, 29301, 32573, 33252, 35133); //33078

        $arr_check_market_1 = array(1);
        $arr_check_market_2 = array(2, 3, 28, 52, 226, 342);

        foreach ($returnBets as $bet_key => $mergeBet) {

            // 제공되지 않는 리그의 마켓은 프론트에서 제외된다.
            if (1 != $mergeBet['markets_id'] && true == in_array($mergeBet['fixture_league_id'], $arrLeague)) {
                unset($returnBets[$bet_key]);
                continue;
            }

            // base_line 값이 문제가 있다 역시 프론트에서 제외된다.
            if ((false === isset($mergeBet['bet_base_line']) || true === empty($mergeBet['bet_base_line'])) && true == in_array($mergeBet['markets_id'], $arr_check_market)) {
                unset($returnBets[$bet_key]);
                continue;
            }

           $arr_bet_base_line = explode(" ", $mergeBet['bet_base_line']);
            // 핸디캡 배당사 제외 루틴
           if (BASEBALL == $mergeBet['fixture_sport_id'] && 342 == $mergeBet['markets_id']) {// 야구는 0 기준점만 제외한다.
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            }
            
            // 축구 승무패(마켓아이디 1) 의 승,패 배당이 2.15 ~ 2.55 사이일 경우  핸디캡,핸디캡연장포함일경우 아래 기준점은 프론트에서 제외된다
            /* $arr_bet_base_line = explode(" ", $mergeBet['bet_base_line']);
              if (true == in_array($mergeBet['fixture_id'], $arr_check_handi_soccer_expecpt) && (3 == $mergeBet['markets_id'] || 342 == $mergeBet['markets_id'])) {
              if ('0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0]) {
              unset($returnBets[$bet_key]);
              continue;
              }
              } else if (48242 == $mergeBet['fixture_sport_id'] && (3 == $mergeBet['markets_id'] || 342 == $mergeBet['markets_id'])) {// 농구 아래 기준점 프론트 제외
              if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0] || '1.5' == $arr_bet_base_line[0] || '-1.5' == $arr_bet_base_line[0]) {
              unset($returnBets[$bet_key]);
              continue;
              }
              } else if (ICEHOCKEY == $mergeBet['fixture_sport_id'] && 342 == $mergeBet['markets_id']) {// 아이스 하키 아래 기준점 프론트 제외
              if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0]) {
              unset($returnBets[$bet_key]);
              continue;
              }
              } */

            // 최소,최대 값 기준 조건에 맞지 않으면 프론트에서 제외된다.
            foreach ($mergeBet['bet_data'] as $key => $value) {
                 // 핸디캡 일경우 정배 배당에서는 플핸 제외를 해야한다.
                if(true == BetDataUtil::checkHandiPlus($returnBets, $mergeBet,$value,$bet_key,$logger)){
                    unset($returnBets[$bet_key]);
                    break;
                }
                
                if (0 < $mergeBet['limit_bet_price'] && $value['bet_price'] < $mergeBet['limit_bet_price']) {
                    if (1 == $mergeBet['markets_id'] || 52 == $mergeBet['markets_id'] || 226 == $mergeBet['markets_id']) {
                        BetDataUtil::deleteAllFixId($returnBets, $mergeBet['fixture_id']);
                    }
                    unset($returnBets[$bet_key]);
                    break;
                }

                if (0 < $mergeBet['max_bet_price'] && $mergeBet['max_bet_price'] < $value['bet_price']) {
                    if (1 == $mergeBet['markets_id'] || 52 == $mergeBet['markets_id'] || 226 == $mergeBet['markets_id']) {
                        BetDataUtil::deleteAllFixId($returnBets, $mergeBet['fixture_id']);
                    }
                    unset($returnBets[$bet_key]);
                    break;
                }

                // 마켓마다 배팅수가 안맞으면 제거한다 승무패 경기면 bet_data가 3개이고 오버언더변 2개이어야 한다 이개수가 안맞으면 프론트에서 제외 
                $count = count($mergeBet['bet_data']);

                if (3 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_1)) {
                    if (1 == $mergeBet['markets_id']) {
                        BetDataUtil::deleteAllFixId($returnBets, $mergeBet['fixture_id']);
                    }
                    unset($returnBets[$bet_key]);
                    break;
                } else if (2 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_2)) {
                    if (52 == $mergeBet['markets_id'] || 226 == $mergeBet['markets_id']) {
                        BetDataUtil::deleteAllFixId($returnBets, $mergeBet['fixture_id']);
                    }
                    unset($returnBets[$bet_key]);
                    break;
                }
            }
        }

        // 오버언더,핸디캡 마켓중 배당이 역배당인 기준점은 프론트에서 제외한다.
        //$returnBets = BetDataUtil::checkOverUnderHandi($check_over_under_arr, $returnBets, $logger);
        // Clean up the difference between the two sides, leaving only the reference point with the minimum difference.
        $returnBets = BetDataUtil::checkMinBetPrice($check_over_under_arr, $returnBets, $logger);
        return $returnBets;
    }

    private static function deleteAllFixId(&$returnBets, $fixture_id) {
        foreach ($returnBets as $bet_key => $mergeBet) {
            if ($fixture_id == $mergeBet['fixture_id']) {
                unset($returnBets[$bet_key]);
            }
        }
    }

    // 마감임박
    public static function mergeMainDeadlineBetData($bets, $logger) {
        # 첫번째 데이터 설정
        $returnBets = [];
        if (!isset($bets) || 0 == count($bets))
            return $returnBets;

        foreach ($bets as $bet) {
            $key = $bet->fixture_id . '_' . $bet->markets_id . '_' . $bet->bet_base_line;
            if (isset($returnBets[$key])) {
                $mergeBet = $returnBets[$key];
            } else {

                $p1_name = $bet->p1_team_name;
                $p2_name = $bet->p2_team_name;
                $mergeBet = ['fixture_id' => $bet->fixture_id,
                    'fixture_sport_id' => $bet->fixture_sport_id,
                    'markets_id' => $bet->markets_id,
                    'start_date' => date("H:i", strtotime($bet->fixture_start_date)),
                    'fixture_participants_1_name' => $p1_name,
                    'fixture_participants_2_name' => $p2_name,
                    'limit_bet_price' => $bet->limit_bet_price,
                    'max_bet_price' => $bet->max_bet_price,
                    'bet_data' => [],];
            }

            if (6 == $mergeBet['markets_id'] || 9 == $mergeBet['markets_id']) {

                if ($mergeBet['limit_bet_price'] < $bet->bet_price && $bet->bet_price < $mergeBet['max_bet_price'] && 1 == $bet->bet_status) {
                    $mergeBet['bet_data'][$bet->bet_name] = array('bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price
                        , 'bet_line' => $bet->bet_line, 'bet_base_line' => $bet->bet_base_line);
                }
            } else {

                $mergeBet['bet_data'][$bet->bet_name] = array('bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price,
                    'bet_line' => $bet->bet_line, 'bet_base_line' => $bet->bet_base_line);
            }

            $returnBets[$key] = $mergeBet;
        }

        // 최소 최대 배당  
        // 이루틴에서 정렬을 한다.
        $arrLeague = array(918, 3371, 5283, 5556, 5769, 14121, 14493, 14497, 14498, 14499, 14530, 14761, 15181, 15997, 16176, 16362, 16482, 16745, 16791, 20736, 22893, 23798, 24136, 25686, 27433, 27488, 29301, 32573, 33252, 35133); //33078
        foreach ($returnBets as $bet_key => $mergeBet) {

            if (1 != $mergeBet['markets_id'] && true == in_array($mergeBet['fixture_league_id'], $arrLeague)) {
                unset($returnBets[$bet_key]);
                continue;
            }

            foreach ($mergeBet['bet_data'] as $key => $value) {

                if (0 < $mergeBet['limit_bet_price'] && $value['bet_price'] < $mergeBet['limit_bet_price'] || 1 == count($mergeBet['bet_data'])) {
                    unset($returnBets[$bet_key]);

                    break;
                }

                if (0 < $mergeBet['max_bet_price'] && $mergeBet['max_bet_price'] < $value['bet_price']) {
                    unset($returnBets[$bet_key]);

                    break;
                }
            }
        }

        return $returnBets;
    }

    public static function mergeMainAvgBetData($bets, $logger) {
        # 첫번째 데이터 설정
        $returnAvgBets = BetDataUtil::mergeAvgBetData($bets, $logger);
        $returnBets = [];
        foreach ($returnAvgBets as $avg_key => $bet) {
            $key = $bet['fixture_id'];

            $is_main = false;
            if ((SOCCER == $bet['fixture_sport_id'] || ICEHOCKEY == $bet['fixture_sport_id']) && 1 == $bet['markets_id']) {
                $is_main = true;
            } else if ((BASKETBALL == $bet['fixture_sport_id'] || BASEBALL == $bet['fixture_sport_id']) && 226 == $bet['markets_id']) {
                $is_main = true;
            } else if ((VOLLEYBALL == $bet['fixture_sport_id'] || UFC == $bet['fixture_sport_id'] 
                    || ESPORTS == $bet['fixture_sport_id'] || TENNIS == $bet['fixture_sport_id']) && 52 == $bet['markets_id']) {
                $is_main = true;
            }

            $mergeBet = [];
            // 해당 키값이 있으면 fix_id
            if (true === isset($returnBets[$key])) {
                $mergeBet = $returnBets[$key];
            }

            if (true == $is_main) {

                $mergeBet['fixture_id'] = $bet['fixture_id'];
                $mergeBet['markets_id'] = $bet['markets_id'];
                $mergeBet['markets_name'] = $bet['markets_name'];
                $mergeBet['markets_name_origin'] = $bet['markets_name_origin'];
                $mergeBet['markets_display_name'] = $bet['markets_name'];
                $mergeBet['start_date'] = $bet['start_date'];
                $mergeBet['fixture_start_date'] = $bet['fixture_start_date'];
                $mergeBet['fixture_sport_id'] = $bet['fixture_sport_id'];
                $mergeBet['fixture_sport_name'] = $bet['fixture_sport_name'];
                $mergeBet['fixture_league_id'] = $bet['fixture_league_id'];
                $mergeBet['fixture_league_name'] = $bet['fixture_league_name'];
                $mergeBet['fixture_league_image_path'] = $bet['fixture_league_image_path'];
                $mergeBet['fixture_participants_1_name'] = $bet['fixture_participants_1_name'];
                $mergeBet['fixture_participants_2_name'] = $bet['fixture_participants_2_name'];
                $mergeBet['fixture_location_id'] = $bet['fixture_location_id'];
                $mergeBet['fixture_location_name'] = $bet['fixture_location_name'];
                $mergeBet['fixture_location_image_path'] = $bet['fixture_location_image_path'];
                $mergeBet['bet_base_line'] = $bet['bet_base_line'];

                $mergeBet['menu'] = $bet['menu'];
                $mergeBet['main_book_maker'] = $bet['main_book_maker'];
                $mergeBet['sub_book_maker'] = $bet['sub_book_maker'];
                $mergeBet['providers_id'] = $bet['providers_id'];
                $mergeBet['limit_bet_price'] = $bet['limit_bet_price'];

                $mergeBet['max_bet_price'] = $bet['max_bet_price'];
                $mergeBet['leagues_m_bet_money'] = $bet['leagues_m_bet_money'];
                $mergeBet['bet_data'] = $bet['bet_data'];
                $mergeBet['is_main_menu'] = $bet['is_main_menu'];
                $mergeBet['display_order'] = $bet['display_order'];
                $mergeBet['main_display_order'] = $bet['main_display_order'];
                $mergeBet['display_status'] = $bet['display_status'];
                $mergeBet['last_update'] = $bet['last_update'];
                $mergeBet['update_dt'] = $bet['update_dt'];
            }

            if (false === isset($mergeBet['market_data'][$bet['markets_id']])) {
                $mergeBet['market_data'][$bet['markets_id']] = $bet['markets_id'];
                // bet_data가 존재할때만 카운트한다.
                if (0 < count($bet['bet_data'])) {
                    $mergeBet['game_count'] = $mergeBet['game_count'] + 1;
                }
                $mergeBet['fix_id'] = $key;
            }

            $returnBets[$key] = $mergeBet;
        }

        return $returnBets;
    }

    // classic 
    public static function mergeMainAvgClassicBetData($bets, $logger) {
        # 첫번째 데이터 설정
        $returnAvgBets = BetDataUtil::mergeAvgClassicBetData($bets, $logger);
     
        return $returnAvgBets;
    }

    public static function mergeRealBetData2($bets, $logger) {
        # 첫번째 데이터 설정
        $returnBets = [];
        if (!isset($bets) || 0 == count($bets))
            return $returnBets;

        $arr_check_market = array(2, 3, 11, 21, 28, 30, 31, 53, 64, 77, 95, 101, 102, 153, 155, 214, 220, 221, 281, 342, 866, 1558);
        $check_over_under_arr = [];
        $arr_check_handi_soccer_expecpt = [];

        foreach ($bets as $bet) {
            if (true == isset($bet->bet_base_line) && false == empty($bet->bet_base_line) && 1 == $bet->bet_status) {
                $check_over_under_arr = BetDataUtil::mergeBaseline($bet, $check_over_under_arr, $arr_check_market);
            }

            $key = $bet->fixture_id . '_' . $bet->markets_id . '_' . $bet->bet_base_line;
            if (isset($returnBets[$key])) {
                $mergeBet = $returnBets[$key];
            } else {
                $mergeBet = BetDataUtil::doRealMergeBetData($bet, 2);
            }
            $mergeBet['bet_data'][] = array('bet_id' => $bet->bet_id, 'bet_name' => $bet->bet_name, 'bet_price' => $bet->bet_price
                , 'bet_start_price' => $bet->bet_start_price ?? null, 'bet_line' => $bet->bet_line, 'bet_status' => $bet->bet_status, 'bet_base_line' => $bet->bet_base_line
                    , 'last_update' => $bet->last_update , 'update_dt' => $bet->update_dt);

            // 축구 경기만 따로 정리한다.
            if (SOCCER == $bet->fixture_sport_id && ('1' == $bet->bet_name || '2' == $bet->bet_name) && 2.15 <= $bet->bet_price && $bet->bet_price <= 2.55) {
                if (false == in_array($bet->fixture_id, $arr_check_handi_soccer_expecpt)) {
                    $arr_check_handi_soccer_expecpt[] = $bet->fixture_id;
                }
            }

            $returnBets[$key] = $mergeBet;
        }

        $arr_check_market_2 = array(2, 3, 5, 11, 17, 21, 22, 23, 28, 30, 31, 45, 46, 47, 63, 64, 52, 53, 65, 66, 67, 77, 95, 101, 102, 153, 155, 202, 214, 220, 221, 226, 235, 281, 342, 352, 353, 669, 866, 1170, 1558);
        $arr_check_market_3 = array(1, 7, 13, 19, 41, 42, 43, 44, 49, 71, 282, 322, 348, 349);

        $arr_check_market_5 = array(70);
        $arr_check_market_6 = array(427);
        $arr_check_market_9 = array(4, 390);
        $arr_check_market_other = array(6, 9);

        $nowday = date("Y-m-d H:i:s");

        foreach ($returnBets as $bet_key => $mergeBet) {

            // 핸디캡 배당사 제외 루틴
            // 축구 
            $arr_bet_base_line = explode(' ', $mergeBet['bet_base_line']);
            $arr_bet_price = explode('.', (string) $arr_bet_base_line[0]);
            if (count($arr_bet_price) > 1 && ('25' == $arr_bet_price[1] || '75' == $arr_bet_price[1])) {
                unset($returnBets[$bet_key]);
                continue;
            }

            if (true == in_array($mergeBet['fixture_id'], $arr_check_handi_soccer_expecpt) && (3 == $mergeBet['markets_id'] || 342 == $mergeBet['markets_id'])) {
                if ('0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            } else if (BASKETBALL == $mergeBet['fixture_sport_id'] && (3 == $mergeBet['markets_id'] || 342 == $mergeBet['markets_id'])) {// 농구
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0] || '1.5' == $arr_bet_base_line[0] || '-1.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            } else if (VOLLEYBALL == $mergeBet['fixture_sport_id'] && 1558 == $mergeBet['markets_id']) {// 배구
                if ('0.0' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0] || '1.5' == $arr_bet_base_line[0] || '-1.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            } else if (ICEHOCKEY == $mergeBet['fixture_sport_id'] && 342 == $mergeBet['markets_id']) {// 아이스 하키
                if ('0.0' == $mergeBet['bet_base_line'] || '0.5' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '1.0' == $arr_bet_base_line[0] || '-1.0' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            }  else if (BASEBALL == $mergeBet['fixture_sport_id'] && 342 == $mergeBet['markets_id']) {// 야구 기준점은 프리매치만 제외한다
                if ('0.0' == $arr_bet_base_line[0] || '-0.5' == $arr_bet_base_line[0] || '0.5' == $arr_bet_base_line[0]) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            }


            foreach ($mergeBet['bet_data'] as $key => $value) {

                 // 핸디캡 일경우 정배 배당에서는 플핸 제외를 해야한다.
                if(true == BetDataUtil::checkHandiPlus($returnBets, $mergeBet,$value,$bet_key,$logger)){
                    unset($returnBets[$bet_key]);
                    break;
                }
                
                if (0 < $mergeBet['limit_bet_price'] && $value['bet_price'] < $mergeBet['limit_bet_price']) {
                    unset($returnBets[$bet_key]);
                    break;
                }

                if (0 < $mergeBet['max_bet_price'] && $mergeBet['max_bet_price'] < $value['bet_price']) {
                    unset($returnBets[$bet_key]);
                    break;
                }

                // 마켓마다 배팅수가 안맞으면 제거한다, 승무패 경기면 bet_data가 3개이고 오버언더변 2개이어야 한다 이개수가 안맞으면 프론트에서 제외 
                $count = count($mergeBet['bet_data']);
                if (2 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_2)) {
                    unset($returnBets[$bet_key]);
                    break;
                } else if (3 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_3)) {
                    unset($returnBets[$bet_key]);
                    break;
                } else if (5 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_5)) {
                    unset($returnBets[$bet_key]);
                    break;
                } else if (6 != $count && true == in_array($mergeBet['markets_id'], $arr_check_market_6)) {
                    unset($returnBets[$bet_key]);
                    break;
                }

                $lastUpdateDate = date("Y-m-d H:i:s", strtotime($value['last_update'] . '+9 hours'));
                $gap_second = (int) (strtotime($nowday) - strtotime($lastUpdateDate));
                if (INPLAY_DELAY_ALLOWED_TIME < $gap_second) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
                
                $gap_second_2 = (int) (strtotime($nowday) - strtotime($value['update_dt']));
                if (INPLAY_DELAY_ALLOWED_TIME < $gap_second_2) {
                    unset($returnBets[$bet_key]);
                    continue;
                }
            }
            $data_object = json_decode($mergeBet['livescore']);
            $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다

            $retval1 = BetDataUtil::exp_score_filter($period_id, $data_object, $mergeBet, $logger);
            if (BET_END == $retval1 || BET_CLOSE == $retval1) {
                unset($returnBets[$bet_key]);
                continue;
            }

            $retval = BetDataUtil::checkDisplayMarkets($mergeBet, $logger);
            if (BET_END == $retval || BET_CLOSE == $retval) {
                unset($returnBets[$bet_key]);
                continue;
            }

            $returnBets[$bet_key]['bet_status'] = $retval;
            $returnBets[$bet_key]['bet_status_af'] = $retval;
        }
    

        // 오버언더,핸디캡 마켓중 배당이 역배당인 기준점은 프론트에서 제외한다.
        $returnBets = BetDataUtil::checkOverUnderHandi($check_over_under_arr, $returnBets, $logger);
        return $returnBets;
    }

    private static function doMergeBetData($bet, $bet_type) {

        // 팀네임 설정
        $p1_name = $bet->p1_team_name; //$bet->fixture_participants_1_name;
        $p2_name = $bet->p2_team_name; //$bet->fixture_participants_2_name;
        if ($bet->p1_display_name != null && $bet->p1_display_name != '') {
            $p1_name = $bet->p1_display_name;
            //$logger->debug($bet->p1_team_name);
        }
        if ($bet->p2_display_name != null && $bet->p2_display_name != '') {
            $p2_name = $bet->p2_display_name;
        }
        // 리그 명 설정
        $league_display_name = $bet->fixture_league_name;
        if ($bet->league_display_name != null || $bet->league_display_name != '') {
            $league_display_name = $bet->league_display_name;
        }
        // 리그 이미지 패스 설정
        //$league_image_path = '/images/flag/' . $bet->fixture_location_id . '.png';
        $league_image_path = '/images/flag_eng/' . strtolower($bet->location_name_en ?? '') . '.png';
        if ($bet->league_image_path != null || $bet->league_image_path != '') {
            $league_image_path = $bet->league_image_path;
        }

        // 지역 이미지 패스 설정
        $location_image_path = '/images/flag/' . $bet->fixture_location_id . '.png';
        if ($bet->location_image_path != null || $bet->location_image_path != '') {
            $location_image_path = $bet->location_image_path;
        }

        // 지역명 설정
        $location_display_name = $bet->fixture_location_name;
        if (isset($bet->location_display_name)) {
            if ($bet->location_display_name != null || $bet->location_display_name != '') {
                $location_display_name = $bet->location_display_name;
            }
        }

        if (!isset($bet->leagues_m_bet_money)) {
            $bet->leagues_m_bet_money = $bet->amount;
        }


        $mergeBet = [
            'fixture_id' => $bet->fixture_id,
            'markets_id' => $bet->markets_id,
            'markets_name' => $bet->name,
            'markets_name_origin' => $bet->name,
            'markets_display_name' => $bet->markets_display_name,
            'start_date' => date("H:i", strtotime($bet->fixture_start_date)),
            'fixture_start_date' => $bet->fixture_start_date,
            'fixture_sport_id' => $bet->fixture_sport_id,
            'fixture_sport_name' => $bet->fixture_sport_name,
            'fixture_league_id' => $bet->fixture_league_id,
            'fixture_league_name' => $league_display_name,
            'fixture_league_image_path' => $league_image_path,
            'fixture_participants_1_name' => $p1_name,
            'fixture_participants_2_name' => $p2_name,
            'fixture_location_id' => $bet->fixture_location_id,
            'fixture_location_name' => $location_display_name,
            'fixture_location_image_path' => $location_image_path,
            'bet_base_line' => $bet->bet_base_line,
            'menu' => $bet->menu,
            'main_book_maker' => $bet->main_book_maker,
            'sub_book_maker' => $bet->sub_book_maker,
            'providers_id' => $bet->providers_id,
            'limit_bet_price' => $bet->limit_bet_price,
            'max_bet_price' => $bet->max_bet_price,
            'leagues_m_bet_money' => $bet->leagues_m_bet_money,
            'bet_data' => [],
            'is_main_menu' => $bet->is_main_menu,
            'display_order' => $bet->display_order,
            'main_display_order' => $bet->main_display_order,
            'display_status' => $bet->display_status,
            'last_update' => $bet->last_update,
            'update_dt' => $bet->update_dt
        ];

        if (2 == $bet_type) {
            $mergeBet['bet_status'] = $bet->bet_status;
            $mergeBet['quarter_time'] = $bet->quarter_time;
            $mergeBet['not_display_period'] = $bet->not_display_period;
            $mergeBet['not_display_time'] = $bet->not_display_time;
            $mergeBet['not_display_score'] = $bet->not_display_score;
            $mergeBet['not_display_score_team_type'] = $bet->not_display_score_team_type;
        }

        return $mergeBet;
    }

    private static function doRealMergeBetData($bet, $bet_type) {

        // 팀네임 설정
        $p1_name = $bet->p1_team_name; //$bet->fixture_participants_1_name;
        $p2_name = $bet->p2_team_name; //$bet->fixture_participants_2_name;
        if ($bet->p1_display_name != null && $bet->p1_display_name != '') {
            $p1_name = $bet->p1_display_name;
        }
        if ($bet->p2_display_name != null && $bet->p2_display_name != '') {
            $p2_name = $bet->p2_display_name;
        }
        // 리그 명 설정
        $league_display_name = $bet->fixture_league_name;
        if ($bet->league_display_name != null || $bet->league_display_name != '') {
            $league_display_name = $bet->league_display_name;
        }
        // 리그 이미지 패스 설정
        $league_image_path = '/images/flag/' . $bet->fixture_location_id . '.png';
        if ($bet->league_image_path != null || $bet->league_image_path != '') {
            $league_image_path = $bet->league_image_path;
        }

        // 지역 이미지 패스 설정
        $location_image_path = '/images/flag/' . $bet->fixture_location_id . '.png';
        if ($bet->location_image_path != null || $bet->location_image_path != '') {
            $location_image_path = $bet->location_image_path;
        }

        // 지역명 설정
        $location_display_name = $bet->fixture_location_name;
        if (isset($bet->location_display_name)) {
            if ($bet->location_display_name != null || $bet->location_display_name != '') {
                $location_display_name = $bet->location_display_name;
            }
        }

        if (!isset($bet->leagues_m_bet_money)) {
            $bet->leagues_m_bet_money = $bet->amount;
        }


        $Livescore = json_decode($bet->livescore);

        $mergeBet = [
            'fixture_id' => $bet->fixture_id,
            'markets_id' => $bet->markets_id,
            'markets_name' => $bet->name,
            'markets_name_origin' => $bet->name,
            'start_date' => date("H:i", strtotime($bet->fixture_start_date)),
            'fixture_start_date' => $bet->fixture_start_date,
            'fixture_sport_id' => $bet->fixture_sport_id,
            'fixture_sport_name' => $bet->fixture_sport_name,
            'fixture_league_id' => $bet->fixture_league_id,
            'fixture_league_name' => $league_display_name,
            'fixture_league_image_path' => $league_image_path,
            'fixture_participants_1_name' => $p1_name,
            'fixture_participants_2_name' => $p2_name,
            'fixture_location_id' => $bet->fixture_location_id,
            'fixture_location_name' => $location_display_name,
            'fixture_location_image_path' => $location_image_path,
            'bet_base_line' => $bet->bet_base_line,
            'live_time' => $Livescore->Scoreboard->Time,
            'live_current_period' => $Livescore->Scoreboard->CurrentPeriod,
            'live_current_period_display' => StatusUtil::periodCodeToStr($Livescore->Scoreboard->CurrentPeriod, $bet->fixture_sport_id),
            'live_results_p1' => is_null($bet->m_live_results_p1) ? $bet->live_results_p1 : $bet->m_live_results_p1,
            'live_results_p2' => is_null($bet->m_live_results_p2) ? $bet->live_results_p2 : $bet->m_live_results_p2,
            'livescore' => is_null($bet->livescore) ? '' : $bet->livescore,
            'break_dt' => $bet->break_dt,
            'update_dt' => $bet->update_dt,
            'last_update' => $bet->last_update,
            'menu' => $bet->menu,
            'main_book_maker' => $bet->main_book_maker,
            'sub_book_maker' => $bet->sub_book_maker,
            'providers_id' => $bet->providers_id,
            'limit_bet_price' => $bet->limit_bet_price,
            'max_bet_price' => $bet->max_bet_price,
            'leagues_m_bet_money' => $bet->leagues_m_bet_money,
            'bet_data' => [],
            'is_main_menu' => $bet->is_main_menu,
            'display_order' => $bet->display_order,
            'main_display_order' => $bet->main_display_order,
            'display_status' => $bet->display_status
        ];

        if (2 == $bet_type) {
            $mergeBet['bet_status'] = $bet->bet_status;
            $mergeBet['quarter_time'] = $bet->quarter_time;
            $mergeBet['not_display_period'] = $bet->not_display_period;
            $mergeBet['not_display_time'] = $bet->not_display_time;
            $mergeBet['not_display_score'] = $bet->not_display_score;
            $mergeBet['not_display_score_team_type'] = $bet->not_display_score_team_type;
        }

        return $mergeBet;
    }

    private static function checkDisplaySoccerMarkets($mergeBet, $logger) {
        $data_object = json_decode($mergeBet['livescore']);
        $limit_time = $mergeBet['not_display_time'];
        $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다
        $first_half = [21, 41, 64]; // 전반전 노출
        // 후반전 노출 마켓 
        if (42 == $mergeBet['markets_id'] && 10 == $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $first_half) && 10 < $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 != $data_object->Scoreboard->CurrentPeriod && 20 < $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 == $data_object->Scoreboard->CurrentPeriod || $data_object->Scoreboard->CurrentPeriod < $period_id) {
            return $mergeBet['bet_status'];
        }

        if (20 == $data_object->Scoreboard->CurrentPeriod) {
            $data_object->Scoreboard->Time = $data_object->Scoreboard->Time - 2700;
        }

        $time = $data_object->Scoreboard->Time / 60;
        if ($time < $limit_time) {
            return $mergeBet['bet_status'];
        }

        return BET_CLOSE;
    }

    // 아이스하키
    private static function checkDisplayIceHockeyMarkets($mergeBet, $logger) {
        $data_object = json_decode($mergeBet['livescore']);
        $limit_time = $mergeBet['not_display_time'];
        $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다

        $period_1p = [21, 41, 64];
        $period_2p = [42, 45, 65];
        $period_3p = [43, 46, 66];

        if (true == in_array($mergeBet['markets_id'], $period_1p) && 1 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_2p) && 80 != $data_object->Scoreboard->CurrentPeriod && 2 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_3p) && 80 != $data_object->Scoreboard->CurrentPeriod && 3 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 != $data_object->Scoreboard->CurrentPeriod && 3 < $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 == $data_object->Scoreboard->CurrentPeriod || $data_object->Scoreboard->CurrentPeriod < $period_id) {
            return $mergeBet['bet_status'];
        }

        $time = $data_object->Scoreboard->Time / 60;
        if ($time > $limit_time) {
            return $mergeBet['bet_status'];
        }

        return BET_CLOSE;
    }

    // 농구
    private static function checkDisplayBasketBallMarkets($mergeBet, $logger) { // 농구 
        $data_object = json_decode($mergeBet['livescore']);

        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return $mergeBet['bet_status'];
        }

        $limit_time = $mergeBet['not_display_time'];
        $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다

        $period_1p = [21, 64];
        $period_2p = [45, 65];
        $period_3p = [46, 66];
        $period_4p = [47, 67];
        //$period_full = [28, 67, 220, 221, 226, 342];



        if (true == in_array($mergeBet['markets_id'], $period_1p) && 1 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_2p) && 80 != $data_object->Scoreboard->CurrentPeriod && 2 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_3p) && 80 != $data_object->Scoreboard->CurrentPeriod && 3 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_4p) && 80 != $data_object->Scoreboard->CurrentPeriod && 4 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 != $data_object->Scoreboard->CurrentPeriod && 4 < $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 == $data_object->Scoreboard->CurrentPeriod) {
            $play_time = strtotime(date('Y-m-d H:i:s')) - strtotime($mergeBet['break_dt']);
            // 농구일경우 휴식시간 시작후 90초가 지나야 배팅을 할수있다
            if ($play_time < 90) {
                return BET_CLOSE;
            }
            return $mergeBet['bet_status'];
        } else if ($data_object->Scoreboard->CurrentPeriod < $period_id) { // 측구는 $period_id 이값이 10,20으로 셋팅 되어있다 
            return $mergeBet['bet_status'];
        }

        $play_time = $mergeBet['quarter_time'] - $data_object->Scoreboard->Time; // 720 부터 시작
        $quarter_time = ($play_time) / 60;
        if (9553959 == $mergeBet['fixture_id'] && 46 == $mergeBet['markets_id'] && 3 == $data_object->Scoreboard->CurrentPeriod) {
            //$logger->info("checkDisplayBasketBallMarkets info quarter_time : ".$quarter_time.' limit_time '.$limit_time);
        }

        if ($quarter_time < $limit_time) {

            if (9553959 == $mergeBet['fixture_id'] && 46 == $mergeBet['markets_id'] && 3 == $data_object->Scoreboard->CurrentPeriod) {
                //$logger->info("checkDisplayBasketBallMarkets NOT BET_CLOSE : quarter_time : ".$quarter_time.' LINE : '.$mergeBet['bet_base_line'] );
            }
            return $mergeBet['bet_status'];
        }


        if (9553959 == $mergeBet['fixture_id'] && 46 == $mergeBet['markets_id'] && 3 == $data_object->Scoreboard->CurrentPeriod) {
            //$logger->info("checkDisplayBasketBallMarkets **** BET_CLOSE : quarter_time : ".$quarter_time.' LINE : '.$mergeBet['bet_base_line'] );
        }

        return BET_CLOSE;
    }

    // 배구 
    private static function checkDisplayVolleyBallMarkets($mergeBet, $logger) {
        $data_object = json_decode($mergeBet['livescore']);

        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return $mergeBet['bet_status'];
        }

        $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다
        $score = $mergeBet['not_display_score'];
        $period_1p = [21, 64];
        $period_2p = [45, 65];
        $period_3p = [46, 66];
        $period_4p = [47, 67];
        $period_full = [2, 52];

        if (true == in_array($mergeBet['markets_id'], $period_1p) && 1 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_2p) && 80 != $data_object->Scoreboard->CurrentPeriod && 2 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_3p) && 80 != $data_object->Scoreboard->CurrentPeriod && 3 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_4p) && 80 != $data_object->Scoreboard->CurrentPeriod && 4 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_full) && 80 != $data_object->Scoreboard->CurrentPeriod && $data_object->Scoreboard->CurrentPeriod < 5) {
            return $mergeBet['bet_status'];
        }else if (true == in_array($mergeBet['markets_id'], $period_full) && 80 != $data_object->Scoreboard->CurrentPeriod && 5 == $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        }else if (80 == $data_object->Scoreboard->CurrentPeriod || $data_object->Scoreboard->CurrentPeriod < $period_id) {
            return $mergeBet['bet_status'];
        }

        //if(true == in_array($mergeBet['markets_id'], $period_full)){
        //    $period_id = $data_object->Scoreboard->CurrentPeriod;
        //}
        
        $period = BetDataUtil::getPeriodScoreNoCheck($period_id, $mergeBet);
        if (null == $period)
            return $mergeBet['bet_status'];

        $result_score1 = $period->Results[0]->Value;
        $result_score2 = $period->Results[1]->Value;

        if ($period->Results[0]->Value < $score && $period->Results[1]->Value < $score) {
            // $logger->debug("expsr_Jdgmn result_score < score sport_id : " . $mergeBet['fixture_sport_id'] . " market_id : " . $mergeBet['markets_id'] . " currnt_period : "
            //         . $data_object->Scoreboard->CurrentPeriod . " period : " . $period_id . " score : " . $score . " limit_time : " . $limit_time . " result_score1 : "
            //         . $period->Results[0]->Value . ' result_score2 : ' . $period->Results[1]->Value . ' bet_status :' . $mergeBet['bet_status'] . ' team_type : ' . $team_type . ' s_quter_time : ' . $mergeBet['quarter_time']);
            return $mergeBet['bet_status'];
        }

        return BET_CLOSE;
    }

    // 야구 
    private static function checkDisplayBaseBallMarkets($mergeBet, $logger) {
        $data_object = json_decode($mergeBet['livescore']);

        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return $mergeBet['bet_status'];
        }

        $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다

        $period_1_5p = [235, 236, 281, 1618];
        $period_2p = [42, 45];
        $period_3p = [43, 46];
        $period_4p = [44, 47];
        $period_5p = [48, 49];
        $period_6p = [348, 352];
        $period_7p = [349, 353];
        $period_full = [28, 226, 342];

        if (true == in_array($mergeBet['markets_id'], $period_1_5p) && 4 < $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_2p) && 80 != $data_object->Scoreboard->CurrentPeriod && 1 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_3p) && 80 != $data_object->Scoreboard->CurrentPeriod && 2 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_4p) && 80 != $data_object->Scoreboard->CurrentPeriod && 3 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_5p) && 80 != $data_object->Scoreboard->CurrentPeriod && 4 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_6p) && 80 != $data_object->Scoreboard->CurrentPeriod && 5 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_7p) && 80 != $data_object->Scoreboard->CurrentPeriod && 6 != $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (true == in_array($mergeBet['markets_id'], $period_full) && 80 != $data_object->Scoreboard->CurrentPeriod && 8 < $data_object->Scoreboard->CurrentPeriod) {
            return BET_END;
        } else if (80 == $data_object->Scoreboard->CurrentPeriod || $data_object->Scoreboard->CurrentPeriod < $period_id) {
            return $mergeBet['bet_status'];
        }

        return BET_CLOSE;
    }

   // 이스포츠
    private static function checkDisplayEsportsMarkets($mergeBet, $logger) {
        
       // return $mergeBet['bet_status'];
           
        $data_object = json_decode($mergeBet['livescore']);
        $limit_time = $mergeBet['not_display_time'];
        $period_id = $mergeBet['not_display_period']; // 해당 마켓이 보여지면 안되는 퍼리어드값 해당값이 되면 프론트에 노출이 되면 안된다
        // 202,203,204,205,206 - 1,2,3,4,5 승패
        // 669 1세트 첫킬, 1170 1세트 첫용
        $period_1p = [Q1_12, Q1_F_KILL, Q1_F_DG];
        $period_2p = [Q2_12];
        $period_3p = [Q3_12];
        $period_4p = [Q4_12];
        $period_5p = [Q5_12];
        $period_full = [OVER_UNDER, WL];

        if (true == in_array($mergeBet['markets_id'], $period_1p) && 1 == $data_object->Scoreboard->CurrentPeriod) {
            return $mergeBet['bet_status'];
        } else if (true == in_array($mergeBet['markets_id'], $period_2p) && BREAK_TIME != $data_object->Scoreboard->CurrentPeriod && 2 == $data_object->Scoreboard->CurrentPeriod) {
            return $mergeBet['bet_status'];
        } else if (true == in_array($mergeBet['markets_id'], $period_3p) && BREAK_TIME != $data_object->Scoreboard->CurrentPeriod && 3 ==  $data_object->Scoreboard->CurrentPeriod) {
            return $mergeBet['bet_status'];
        } else if (true == in_array($mergeBet['markets_id'], $period_4p) && BREAK_TIME != $data_object->Scoreboard->CurrentPeriod && 4 ==  $data_object->Scoreboard->CurrentPeriod) {
            return $mergeBet['bet_status'];
        } else if (true == in_array($mergeBet['markets_id'], $period_5p) && BREAK_TIME != $data_object->Scoreboard->CurrentPeriod && 5 ==  $data_object->Scoreboard->CurrentPeriod) {
            return $mergeBet['bet_status'];
        } else if (true == in_array($mergeBet['markets_id'], $period_full) && BREAK_TIME != $data_object->Scoreboard->CurrentPeriod && $data_object->Scoreboard->CurrentPeriod < 6) {
            return $mergeBet['bet_status'];
        } else if (BREAK_TIME == $data_object->Scoreboard->CurrentPeriod || $data_object->Scoreboard->CurrentPeriod < $period_id) {
            return $mergeBet['bet_status'];
        }

        return BET_CLOSE;
    }

    public static function checkDisplayMarkets($mergeBet, $logger) {
        // 축구 
        if (SOCCER == $mergeBet['fixture_sport_id']) {
            return BEtDataUtil::checkDisplaySoccerMarkets($mergeBet, $logger);
        } else if (ICEHOCKEY == $mergeBet['fixture_sport_id']) {
            return BEtDataUtil::checkDisplayIceHockeyMarkets($mergeBet, $logger);
        } else if (BASKETBALL == $mergeBet['fixture_sport_id']) {
            return BEtDataUtil::checkDisplayBasketBallMarkets($mergeBet, $logger);
        } else if (VOLLEYBALL == $mergeBet['fixture_sport_id']) {
            return BEtDataUtil::checkDisplayVolleyBallMarkets($mergeBet, $logger);
        } else if (BASEBALL == $mergeBet['fixture_sport_id']) {
            return BEtDataUtil::checkDisplayBaseBallMarkets($mergeBet, $logger);
        } else if (ESPORTS == $mergeBet['fixture_sport_id']) {
            return BEtDataUtil::checkDisplayEsportsMarkets($mergeBet, $logger);
        }
    }

    // return true 배당이 닫힌다 false면 열린다.
    /* public static function renew_expsr_Jdgmn($limit_time, $period_id, $team_type, $score, $data_object, $mergeBet, $logger) {
      $result_score1 = 0;
      $result_score2 = 0;

      // 농구 경기에서는 2쿼터 후 휴식시간에 모든 배당을 90초간 닫아야 한다.

      if (BASKETBALL == $mergeBet['fixture_sport_id'] && 80 == $data_object->Scoreboard->CurrentPeriod) {
      if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
      return false;
      }

      $max_period = 0;
      $Periods = $data_object->Periods;
      foreach ($Periods as $key => $period) {
      if (80 == $period->Type)
      continue;
      if ($max_period < $period->Type) {
      $max_period = $period->Type;
      }
      }

      $play_time = strtotime(date('Y-m-d H:i:s')) - strtotime($mergeBet['break_dt']);

      //$logger->debug("renew_expsr_Jdgmn break fixture_id time=>" . $mergeBet['fixture_id'] . ' break time => ' . $play_time . ' bdt ==>' . $mergeBet['break_dt']);
      // 농구일경우 휴식시간 시작후 90초가 지나야 배팅을 할수있다
      if ($play_time < 90) {
      return true;
      }
      }

      if (80 == $data_object->Scoreboard->CurrentPeriod) {
      return false;
      }
      if (0 != $period_id && $period_id < $data_object->Scoreboard->CurrentPeriod) {
      // $logger->debug("success under period expsr_Jdgmn data_object->Scoreboard->CurrentPeriod != period_id sport_id : " . $mergeBet['fixture_sport_id'] . " market_id : " . $mergeBet['markets_id'] .
      //        " currnt_period : " . $data_object->Scoreboard->CurrentPeriod . " period : " . $period_id . " score : " . $score . " limit_time : " . $limit_time . ' bet_status :' . $mergeBet['bet_status']);
      return true;
      }
      if ($data_object->Scoreboard->CurrentPeriod != $period_id) {

      return false;
      }

      if (BASKETBALL == $mergeBet['fixture_sport_id'] && 2 == $data_object->Scoreboard->CurrentPeriod && in_array($mergeBet['markets_id'], [64, 21])) {
      return false;
      } else if (BASKETBALL == $mergeBet['fixture_sport_id'] && 3 == $data_object->Scoreboard->CurrentPeriod && in_array($mergeBet['markets_id'], [64, 21, 65, 45])) {
      return false;
      } else if (BASKETBALL == $mergeBet['fixture_sport_id'] && 4 == $data_object->Scoreboard->CurrentPeriod && in_array($mergeBet['markets_id'], [64, 21, 65, 45, 66, 46])) {
      return false;
      } if (ICEHOCKEY == $mergeBet['fixture_sport_id'] && 2 == $data_object->Scoreboard->CurrentPeriod && in_array($mergeBet['markets_id'], [41, 64, 21])) {
      return false;
      } else if (ICEHOCKEY == $mergeBet['fixture_sport_id'] && 3 == $data_object->Scoreboard->CurrentPeriod && in_array($mergeBet['markets_id'], [41, 64, 21, 42, 65, 45])) {
      return false;
      }


      if (SOCCER == $mergeBet['fixture_sport_id'] && 20 == $data_object->Scoreboard->CurrentPeriod) {
      $data_object->Scoreboard->Time = $data_object->Scoreboard->Time - 2700;
      }
      $time = $data_object->Scoreboard->Time / 60;

      //농구는 남은시간이 오니깐 결국 플레이한 시간이 나온다.
      // 결국 플레이한 시간으로 제한을 한다.
      $play_time = $mergeBet['quarter_time'] - $data_object->Scoreboard->Time;
      $quarter_time = ($play_time) / 60;

      if (VOLLEYBALL != $mergeBet['fixture_sport_id']) {// 배구가 아니다
      if (SOCCER == $mergeBet['fixture_sport_id'] && $time < $limit_time) { // 지나간 시간
      //   $logger->debug("expsr_Jdgmn data_object->Scoreboard->Time / 60) < limit_time sport_id : " . $mergeBet['fixture_sport_id'] . " market_id : " . $mergeBet['markets_id'] .
      //           " currnt_period : " . $data_object->Scoreboard->CurrentPeriod . " period : " . $period_id . " score : " . $score . " limit_time : "
      //           . $limit_time . " result_score1 : " . $period->Results[0]->Value.' result_score2 : '.$period->Results[1]->Value . ' bet_status :' . $mergeBet['bet_status'] . ' data_object->Scoreboard->Time : ' . $data_object->Scoreboard->Time . ' time : ' . $time);
      return false;
      } else if (ICEHOCKEY == $mergeBet['fixture_sport_id'] && $time > $limit_time) { // 남은시간
      return false;
      } else if (BASKETBALL == $mergeBet['fixture_sport_id'] && $quarter_time < $limit_time) { // 남은시간
      // $logger->debug("expsr_Jdgmn data_object->Scoreboard->Time / 60) < limit_time sport_id : " . $mergeBet['fixture_sport_id'] . " market_id : "
      //         . $mergeBet['markets_id'] . " currnt_period : " . $data_object->Scoreboard->CurrentPeriod . " period : " . $period_id . " score : "
      //         . $score . " limit_time : " . $limit_time . ' bet_status :' . $mergeBet['bet_status'] . ' data_object->Scoreboard->Time : ' . $data_object->Scoreboard->Time . ' quarter_time : ' . $quarter_time . ' s_quter_time : ' . $mergeBet['quarter_time']);
      return false;
      }
      } else {
      // 배구 경기이다

      $period = BetDataUtil::getPeriodScoreNoCheck($period_id, $mergeBet);
      if (null == $period)
      return false;


      $result_score1 = $period->Results[0]->Value;
      $result_score2 = $period->Results[1]->Value;

      //$logger->debug("result_score1 : " . $result_score1 . " result_score2 : " . $result_score2);
      if ($period->Results[0]->Value < $score && $period->Results[1]->Value < $score) {
      // $logger->debug("expsr_Jdgmn result_score < score sport_id : " . $mergeBet['fixture_sport_id'] . " market_id : " . $mergeBet['markets_id'] . " currnt_period : "
      //         . $data_object->Scoreboard->CurrentPeriod . " period : " . $period_id . " score : " . $score . " limit_time : " . $limit_time . " result_score1 : "
      //         . $period->Results[0]->Value . ' result_score2 : ' . $period->Results[1]->Value . ' bet_status :' . $mergeBet['bet_status'] . ' team_type : ' . $team_type . ' s_quter_time : ' . $mergeBet['quarter_time']);
      return false;
      }
      }


      //  $logger->debug("=====================> SUCCESS expsr_Jdgmn data_object->Scoreboard->Time / 60) < limit_time fix_id : ".$mergeBet['fixture_id']. " sport_id : " . $mergeBet['fixture_sport_id'] . " market_id : " . $mergeBet['markets_id'] .
      //           " currnt_period : " . $data_object->Scoreboard->CurrentPeriod . " period : " . $period_id . " score : " . $score . " limit_time : "
      //           . $limit_time . " result_score1 : " . $result_score1.' result_score2 : '.$result_score2 . ' bet_status :' . $mergeBet['bet_status'] . ' data_object->Scoreboard->Time : ' . $data_object->Scoreboard->Time . ' time : ' . $time
      //          . ' team_type : ' . $team_type . ' quarter_time : ' . $quarter_time);

      return true;
      } */

    // return true 필터 대상 , false  화면에 보여주는 게임

    /* public static function exp_market_filter($data_object, $mergeBet, $logger) {
      // 축구 실시간
      if (SOCCER == $mergeBet['fixture_sport_id']) { // 전반전 승무패,전반전 핸디캡,전반전 오버언더
      if (true == in_array($data_object->Scoreboard->CurrentPeriod, [20, 30, 35, 40, 80]) && true == in_array($mergeBet['markets_id'], [21, 41, 64])) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [10]) && true == in_array($mergeBet['markets_id'], [42]) && 1 == $mergeBet['bet_status']) {
      return true;
      }
      } else if (ICEHOCKEY == $mergeBet['fixture_sport_id']) { // 아이스 하키
      if (true == in_array($data_object->Scoreboard->CurrentPeriod, [2, 3, 40, 80]) && true == in_array($mergeBet['markets_id'], [21, 41, 64])) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 3, 40]) && true == in_array($mergeBet['markets_id'], [42, 45, 65]) && 1 == $mergeBet['bet_status']) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 2, 40]) && true == in_array($mergeBet['markets_id'], [43, 46, 66]) && 1 == $mergeBet['bet_status']) {
      return true;
      }
      } else if (BASKETBALL == $mergeBet['fixture_sport_id']) { // 농구
      if (true == in_array($data_object->Scoreboard->CurrentPeriod, [2, 3, 4, 40, 80]) && true == in_array($mergeBet['markets_id'], [21, 64])) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 3, 4, 40,80]) && true == in_array($mergeBet['markets_id'], [45, 65]) && 1 == $mergeBet['bet_status']) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 2, 4, 40]) && true == in_array($mergeBet['markets_id'], [46, 66]) && 1 == $mergeBet['bet_status']) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 2, 3, 40]) && true == in_array($mergeBet['markets_id'], [47, 67]) && 1 == $mergeBet['bet_status']) {
      return true;
      }
      } else if (VOLLEYBALL == $mergeBet['fixture_sport_id']) { // 배구
      if (true == in_array($data_object->Scoreboard->CurrentPeriod, [2, 3, 4, 80]) && true == in_array($mergeBet['markets_id'], [21, 64])) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 3, 4]) && true == in_array($mergeBet['markets_id'], [45, 65]) && 1 == $mergeBet['bet_status']) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 2, 4]) && true == in_array($mergeBet['markets_id'], [46, 66]) && 1 == $mergeBet['bet_status']) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 2, 3]) && true == in_array($mergeBet['markets_id'], [47, 67]) && 1 == $mergeBet['bet_status']) {
      return true;
      }
      } else if (BASEBALL == $mergeBet['fixture_sport_id']) { // 야구
      if (2 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [42, 45])) {
      return true;
      } else if (3 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [42, 43, 45, 46])) {
      return true;
      } else if (4 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [42, 43, 44, 45, 46, 47])) {
      return true;
      } else if (5 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [42, 43, 44, 45, 46, 47, 48, 49, 235, 236, 281,])) {
      return true;
      } else if (6 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [42, 43, 44, 45, 46, 47, 48, 49, 235, 236, 281, 348, 352])) {
      return true;
      } else if (7 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [28, 42, 43, 44, 45, 46, 47, 48, 49, 226, 235, 236, 281, 342, 348, 349, 352, 353])) {
      return true;
      } else if (8 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [28, 42, 43, 44, 45, 46, 47, 48, 49, 226, 235, 236, 281, 342, 348, 349, 352, 353])) {
      return true;
      } else if (9 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [28, 42, 43, 44, 45, 46, 47, 48, 49, 226, 235, 236, 281, 342, 348, 349, 352, 353])) {
      return true;
      } else if (40 == $data_object->Scoreboard->CurrentPeriod && true == in_array($mergeBet['markets_id'], [28, 42, 43, 44, 45, 46, 47, 48, 49, 226, 235, 236, 281, 342, 348, 349, 352, 353])) {
      return true;
      }
      } else if (ESPORTS == $mergeBet['fixture_sport_id']) { // 이스포츠
      if (true == in_array($data_object->Scoreboard->CurrentPeriod, [2, 3, 40]) && true == in_array($mergeBet['markets_id'], [202, 669, 1170])) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 3, 40]) && true == in_array($mergeBet['markets_id'], [42]) && 1 == $mergeBet['bet_status']) {
      return true;
      } else if (true == in_array($data_object->Scoreboard->CurrentPeriod, [1, 2, 40]) && true == in_array($mergeBet['markets_id'], [43]) && 1 == $mergeBet['bet_status']) {
      return true;
      }
      }
      } */

    public static function exp_score_filter($period_id, $data_object, $mergeBet, $logger) {

        // 오버언더 경기는 점수로 판단해서 노출 여부를 체크해주자 
        if (true == in_array($mergeBet['fixture_sport_id'], [SOCCER, ICEHOCKEY, UFC, ESPORTS, BASKETBALL, VOLLEYBALL]) && true == in_array($mergeBet['markets_id'], [2, 21, 28, 236])) {
            $score = $data_object->Scoreboard->Results[0]->Value + $data_object->Scoreboard->Results[1]->Value;

            if ($mergeBet['bet_base_line'] < $score) {
                return BET_END;
            }
        } else if (true == in_array($mergeBet['fixture_sport_id'], [SOCCER, ICEHOCKEY, UFC, ESPORTS, BASKETBALL, VOLLEYBALL]) && true == in_array($mergeBet['markets_id'], [101, 102, 220, 221])) {
            if (true == in_array($mergeBet['markets_id'], [101, 221])) {
                $score = $data_object->Scoreboard->Results[0]->Value;
            } else {
                $score = $data_object->Scoreboard->Results[1]->Value;
            }

            if ($mergeBet['bet_base_line'] < $score) {
                return BET_END;
            }
        } else if (true == in_array($mergeBet['fixture_sport_id'], [SOCCER, ICEHOCKEY, UFC, ESPORTS, BASKETBALL, VOLLEYBALL]) && true == in_array($mergeBet['markets_id'], [45, 46, 47, 48, 352, 353])) {

            $period_type = 0;
            if (45 == $mergeBet['markets_id']) {
                $period_type = 2;
            } else if (45 == $mergeBet['markets_id']) {
                $period_type = 2;
            } else if (46 == $mergeBet['markets_id']) {
                $period_type = 3;
            } else if (47 == $mergeBet['markets_id']) {
                $period_type = 4;
            } else if (48 == $mergeBet['markets_id']) {
                $period_type = 5;
            } else if (352 == $mergeBet['markets_id']) {
                $period_type = 6;
            } else if (353 == $mergeBet['markets_id']) {
                $period_type = 7;
            }

            list($bet_status, $period) = BetDataUtil::checkPeriodData($mergeBet, $period_type, $logger);
            if (1 == $bet_status || null == $period)
                return $mergeBet['bet_status'];

            $score = $period->Results[0]->Value + $period->Results[1]->Value;

            if ($mergeBet['bet_base_line'] < $score) {
                return BET_END;
            }
        } else if (true == in_array($mergeBet['fixture_sport_id'], [SOCCER]) && true == in_array($mergeBet['markets_id'], [17])) {
            if (0 < $data_object->Scoreboard->Results[0]->Value && 0 < $data_object->Scoreboard->Results[1]->Value) {
                return BET_END;
            }
        }

        return $mergeBet['bet_status'];
    }

    // 피어리드 점수는 해당 피어리드 까지 점수 연장전 가면 다시 점수는 0부터 시작 
    public static function getPeriodScore($period_value, $value) {
        $data_object = json_decode($value['livescore']);
        $findPeriod = null;
        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return $findPeriod;
        }
        $Periods = $data_object->Periods;

        if (SOCCER == $value['fixture_sport_id']) { // 축구
            foreach ($Periods as $key => $period) {
                if (1 == $period_value && 10 == $period->Type && true == $period->IsFinished && true == $period->IsConfirmed) {
                    $findPeriod = $period;
                    break;
                } else if (2 == $period_value && 20 == $period->Type && true == $period->IsFinished && true == $period->IsConfirmed) {
                    $findPeriod = $period;
                    break;
                }
            }
        } else
            foreach ($Periods as $key => $period) {
                if ($period_value == $period->Type && true == $period->IsFinished && true == $period->IsConfirmed) {
                    $findPeriod = $period;
                    break;
                }
            }

        return $findPeriod;
    }

    public static function getPeriodScoreNoCheck($period_value, $value) {
        $data_object = json_decode($value['livescore']);
        $findPeriod = null;
        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return $findPeriod;
        }
        $Periods = $data_object->Periods;

        if (SOCCER == $value['fixture_sport_id']) { // 축구
            foreach ($Periods as $key => $period) {
                if (1 == $period_value && 10 == $period->Type) {
                    $findPeriod = $period;
                    break;
                } else if (2 == $period_value && 20 == $period->Type) {
                    $findPeriod = $period;
                    break;
                }
            }
        } else
            foreach ($Periods as $key => $period) {
                if ($period_value == $period->Type) {
                    $findPeriod = $period;
                    break;
                }
            }


        return $findPeriod;
    }

    public static function getPeriodBestPeriod($period_value, $value) {
        $data_object = json_decode($value['livescore']);
        $bestScore = -1;
        $findPeriod = array();
        $findPeriod_1 = array();
        $findPeriod_2 = array();
        $findPeriod_3 = array();
        $findPeriod_4 = array();
        $bAllSameScore = false;
        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return [$findPeriod, $bAllSameScore];
        }
        $Periods = $data_object->Periods;

        $nScore_1 = 0;
        $nScore_2 = 0;
        $nScore_3 = 0;
        $nScore_4 = 0;
        if (48242 == $value['fixture_sport_id']) { // 농구
            if ('최고득점 피리어드' == $period_value) {
                foreach ($Periods as $key => $period) {
                    if ((1 == $period->Type || 2 == $period->Type || 3 == $period->Type || 4 == $period->Type) && true == $period->IsFinished && true == $period->IsConfirmed) {
                        if (1 == $period->Type) {
                            $nScore_1 = $period->Results[0]->Value + $period->Results[1]->Value;

                            if ($bestScore < $nScore_1) {
                                $bestScore = $nScore_1;
                                $findPeriod = $period;
                            }
                            continue;
                        }

                        if (2 == $period->Type) {
                            $nScore_2 = $period->Results[0]->Value + $period->Results[1]->Value;

                            if ($bestScore < $nScore_2) {
                                $bestScore = $nScore_2;
                                $findPeriod = $period;
                            }
                            continue;
                        }

                        if (3 == $period->Type) {
                            $nScore_3 = $period->Results[0]->Value + $period->Results[1]->Value;

                            if ($bestScore < $nScore_3) {
                                $bestScore = $nScore_3;
                                $findPeriod = $period;
                            }
                            continue;
                        }

                        if (4 == $period->Type) {
                            $nScore_4 = $period->Results[0]->Value + $period->Results[1]->Value;

                            if ($bestScore < $nScore_4) {
                                $bestScore = $nScore_4;
                                $findPeriod = $period;
                            }
                            continue;
                        }
                    }
                }

                if ($nScore_1 == $nScore_2 && $nScore_3 == $nScore_4 && $nScore_1 == $nScore_4) {
                    $bAllSameScore = true;
                }
            } else if ('최고득점 하프' == $period_value) {
                foreach ($Periods as $key => $period) {
                    if ((1 == $period->Type || 2 == $period->Type || 3 == $period->Type || 4 == $period->Type) && true == $period->IsFinished && true == $period->IsConfirmed) {
                        if (1 == $period->Type || 2 == $period->Type) {

                            $nScore_1 += $period->Results[0]->Value + $period->Results[1]->Value;
                            $findPeriod_1 = $period;
                            continue;
                        }

                        if (3 == $period->Type || 4 == $period->Type) {

                            $nScore_2 += $period->Results[0]->Value + $period->Results[1]->Value;
                            $findPeriod_2 = $period;
                            continue;
                        }

                        $findPeriod = $period;
                    }
                }

                if ($nScore_1 < $nScore_2) {
                    $findPeriod = $findPeriod_2;
                } else if ($nScore_1 > $nScore_2) {
                    $findPeriod = $findPeriod_1;
                } else {
                    $bAllSameScore = true;
                }
            }
        } else if (SOCCER == $value['fixture_sport_id']) { // 축구
            if ('최고득점 하프' == $period_value) {
                foreach ($Periods as $key => $period) {
                    if ((10 == $period->Type || 20 == $period->Type ) && true == $period->IsFinished && true == $period->IsConfirmed) {
                        if (10 == $period->Type) {
                            $nScore_1 = $period->Results[0]->Value + $period->Results[1]->Value;
                            $findPeriod_1 = $period;
                            continue;
                        }

                        if (20 == $period->Type) {
                            $nScore_2 = $period->Results[0]->Value + $period->Results[1]->Value;
                            $findPeriod_2 = $period;
                            continue;
                        }
                    }
                }

                if ($nScore_1 < $nScore_2) {
                    $findPeriod = $findPeriod_2;
                } else if ($nScore_1 > $nScore_2) {
                    $findPeriod = $findPeriod_1;
                } else {
                    $bAllSameScore = true;
                }
            }
        }
        return [$findPeriod, $bAllSameScore];
    }

    public static function getPeriodSumScore($period_value, $value, $logger) {
        $data_object = json_decode($value['livescore']);
        $findPeriod = array();
        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            //$logger->debug('getPeriodSumScore is null periods');
            return $findPeriod;
        }
        $Periods = $data_object->Periods;

        $b_find = false;
        $result0 = 0;
        $result1 = 0;
        $arr_data = [];
        //$logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!! getPeriodSumScore1 is null periods !!!!!!!!!!!!!!!!!!!!!!!!!!');

        foreach ($Periods as $key => $period) {
            if (SOCCER == $value['fixture_sport_id'] && '전반전' == $period_value && 10 == $period->Type && true == $period->IsFinished && true == $period->IsConfirmed) {
                $result0 = $period->Results[0]->Value;
                $result1 = $period->Results[1]->Value;
                $b_find = true;
                break;
            } else if (SOCCER == $value['fixture_sport_id'] && '후반전' == $period_value && 20 == $period->Type && true == $period->IsFinished && true == $period->IsConfirmed) {
                $result0 = $period->Results[0]->Value;
                $result1 = $period->Results[1]->Value;
                $b_find = true;
                break;
            }
            if (BASKETBALL == $value['fixture_sport_id'] && '전반전' == $period_value && (1 == $period->Type || 2 == $period->Type || 10 == $period->Type) && true == $period->IsFinished && true == $period->IsConfirmed) {
                $result0 = $result0 + $period->Results[0]->Value;
                $result1 = $result1 + $period->Results[1]->Value;
                $arr_data[] = $period->Type;
                if (10 == $period->Type) {
                    $b_find = true;
                    break;
                }
            } else if ($result0 == $value['fixture_sport_id'] && '후반전' == $period_value && (3 == $period->Type || 4 == $period->Type || 20 == $period->Type) && true == $period->IsFinished && true == $period->IsConfirmed) {
                $result0 = $result0 + $period->Results[0]->Value;
                $result1 = $result1 + $period->Results[1]->Value;
                $arr_data[] = $period->Type;
                if (20 == $period->Type) {
                    $b_find = true;
                    break;
                }
            }


            if ('풀타임' == $period_value && 100 == $period->Type && true == $period->IsFinished) {//&& true == $period->IsConfirmed) {
                $result0 = $result0 + $period->Results[0]->Value;
                $result1 = $result1 + $period->Results[1]->Value;
                $b_find = true;
                break;
            } else if ('연장전' == $period_value && 40 == $period->Type && true == $period->IsFinished /* && true == $period->IsConfirmed */) {
                $result0 = $result0 + $period->Results[0]->Value;
                $result1 = $result1 + $period->Results[1]->Value;
                $b_find = true;
                break;
            } else if ('연장포함' == $period_value && 101 == $period->Type && true == $period->IsFinished) {//&& true == $period->IsConfirmed) {
                $result0 = $result0 + $period->Results[0]->Value;
                $result1 = $result1 + $period->Results[1]->Value;
                $b_find = true;
                break;
            } else if ('패널티' == $period_value && 102 == $period->Type && true == $period->IsFinished /* && true == $period->IsConfirmed */) {
                $result0 = $result0 + $period->Results[0]->Value;
                $result1 = $result1 + $period->Results[1]->Value;
                $b_find = true;
                break;
            }
        }

        //$logger->debug('!!!!!!!!!!!!!!!!!!!!!!!!!!! getPeriodSumScore2 is null periods arr_data ==>' . count($arr_data));

        if (false == $b_find && BASKETBALL == $value['fixture_sport_id'] && ('전반전' == $period_value || '후반전' == $period_value) && 2 == count($arr_data)) {
            $b_find = true;
        }

        if (true === $b_find) {
            $findPeriod[0] = $result0;
            $findPeriod[1] = $result1;
        } else {
            $findPeriod[0] = -1;
            $findPeriod[1] = -1;
        }
        return $findPeriod;
    }

    public static function getPeriodTotalSumScore($value) {
        $data_object = json_decode($value['livescore']);
        $findPeriod = array(-1, -1);
        $live_results_p1 = 0;
        $live_results_p2 = 0;
        if (!isset($data_object->Periods) || true == empty($data_object->Periods) || 0 === count($data_object->Periods)) {
            return $findPeriod;
        }

        $Periods = $data_object->Periods;
        if (SOCCER == $value['fixture_sport_id'] || BASKETBALL == $value['fixture_sport_id']) { // 축구,농구
            $live_results_p1 = $value['live_results_p1'];
            $live_results_p2 = $value['live_results_p2'];
        } else if (VOLLEYBALL == $value['fixture_sport_id']) { // 배구
            foreach ($Periods as $key => $period) {
                if ((1 == $period->Type || 2 == $period->Type || 3 == $period->Type || 4 == $period->Type || 5 == $period->Type) && true == $period->IsFinished && true == $period->IsConfirmed) {
                    $live_results_p1 = $live_results_p1 + $period->Results[0]->Value;
                    $live_results_p2 = $live_results_p2 + $period->Results[1]->Value;
                }
            }
        } else if (BASEBALL == $value['fixture_sport_id'] || ICEHOCKEY == $value['fixture_sport_id'] || ESPORTS == $value['fixture_sport_id'] || UFC == $value['fixture_sport_id']) { // 야구, 아이스 하키,이 스포츠,권투 
            foreach ($Periods as $key => $period) {
                if ((1 == $period->Type || 2 == $period->Type || 3 == $period->Type || 4 == $period->Type || 5 == $period->Type || 6 == $period->Type || 7 == $period->Type) && true == $period->IsFinished && true == $period->IsConfirmed) {
                    $live_results_p1 = $live_results_p1 + $period->Results[0]->Value;
                    $live_results_p2 = $live_results_p2 + $period->Results[1]->Value;
                }
            }
        }

        $findPeriod[0] = $live_results_p1;
        $findPeriod[1] = $live_results_p2;
        return $findPeriod;
    }

    public static function checkData($value, $type, $logger) {
        if (('풀타임' == $type || '연장포함' == $type || '패널티' == $type) && 3 != $value['fixture_status']) {
            return array(1, null);
        }

        if ('null' == $value['livescore'] || null == $value['live_results_p1'] || null == $value['live_results_p2'] || false == isset($value['livescore']) || null == $value['livescore'] || true == empty($value['livescore'])) {
            //$logger->debug("error checkData : " . ' live_results_p1 : ' . $value['live_results_p1'] . ' live_results_p2 : ' . $value['live_results_p2'] . ' livescore ' . $value['livescore'] . ' fix_id : ' . $value['ls_fixture_id'] . ' m_id :' . $value['ls_markets_id']);
            return array(1, null);
        }

        $period = BetDataUtil::getPeriodSumScore($type, $value, $logger);
        if (!isset($period) || null == $period || -1 == $period[0] || -1 == $period[1]) {
            //$logger->debug("error checkData getPeriodSumScore : " . ' period : ' . json_encode($period) . ' type : ' . $type . ' fix_id : ' . $value['ls_fixture_id'] . ' m_id :' . $value['ls_markets_id'] . ' sp_id :' . $value['fixture_sport_id'] . ' fixture_status : ' . $value['fixture_status']);
            return array(1, null);
        }

        return array(4, $period);
    }

    public static function checkPeriodData($value, $type, $logger) {
        if ('null' == $value['livescore'] || null == $value['live_results_p1'] || null == $value['live_results_p2'] || !isset($value['livescore']) || null == $value['livescore']) {
            //$logger->debug("checkPeriodData error : " . ' live_results_p1 : ' . $value['live_results_p1'] . ' live_results_p2 : ' . $value['live_results_p2'] . ' livescore ' . $value['livescore'] . ' fix_id : ' . $value['ls_fixture_id'] . ' m_id :' . $value['ls_markets_id']);
            return array(1, null);
        }

        $period = BetDataUtil::getPeriodScore($type, $value);

        if (!isset($period) || null == $period) {

            //$logger->debug("checkPeriodData getPeriodScore : " . ' period : ' . json_encode($period) . ' type : ' . $type . ' fix_id : ' . $value['ls_fixture_id'] . ' m_id :' . $value['ls_markets_id'] . ' sp_id :' . $value['fixture_sport_id'] . ' fixture_status : ' . $value['fixture_status']);
            return array(1, null);
        }

        return array(4, $period);
    }

    public static function getBetStatus1X2($value) {
        if (($value['live_results_p1'] > $value['live_results_p2'] && 1 == $value['bet_name']) ||
                ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                ($value['live_results_p1'] < $value['live_results_p2'] && 2 == $value['bet_name'])
        ) {
            return 2;
        }
        return 4;
    }

    public static function getBetStatus12($value) {
        if (($value['live_results_p1'] > $value['live_results_p2'] && '1' == $value['bet_name']) ||
                ($value['live_results_p1'] < $value['live_results_p2'] && '2' == $value['bet_name'])
        ) {
            $bet_status = 2;
        } else if ($value['live_results_p1'] == $value['live_results_p2']) {
            $bet_status = 6;
        } else {
            $bet_status = 4;
        }
        return $bet_status;
    }

    public static function getBetStatusOverUnder($value, $score) {
        if ('Over' == $value['bet_name'] && ($value['ls_markets_base_line'] < $score)) {
            $bet_status = 2;
        } else if ('Under' == $value['bet_name'] && ($score < $value['ls_markets_base_line'])) {
            $bet_status = 2;
        } else if ($score == $value['ls_markets_base_line']) {
            $bet_status = 6;
        } else {
            $bet_status = 4;
        }

        return array($bet_status, $value['bet_price']);
    }

    public static function getBetStatusOverExactlyUnder($value, $score) {
        if ('Over' == $value['bet_name'] && ($value['ls_markets_base_line'] < $score)) {
            $bet_status = 2;
        } else if ('Under' == $value['bet_name'] && ($score < $value['ls_markets_base_line'])) {
            $bet_status = 2;
        } else if ('Exactly' == $value['bet_name'] && $score == $value['ls_markets_base_line']) {
            $bet_status = 2;
        } else {
            $bet_status = 4;
        }

        return array($bet_status, $value['bet_price']);
    }

    public static function getBetStatusHandicap($value, $logger = null) {
        $arr_bet_line = explode(' ', $value['bet_line']);
        $live_results_p1 = $value['live_results_p1'];
        $live_results_p2 = $value['live_results_p2'];
        if (1 == $value['bet_name']) {
            $live_results_p1 = $live_results_p1 + (float) $arr_bet_line[0];
        } else {
            $live_results_p2 = $live_results_p2 + (float) $arr_bet_line[0];
        }

        if (1 == $value['bet_name'] && $live_results_p1 > $live_results_p2) {
            $bet_status = 2;
        } else if (2 == $value['bet_name'] && $live_results_p1 < $live_results_p2) {
            $bet_status = 2;
        } else if ($live_results_p1 == $live_results_p2) {
            $bet_status = 6;
        } else {
            $bet_status = 4;
        }

        return array($bet_status, $value['bet_price']);
    }

    public static function getBetStatusEuroPeanHandicap($value, $logger = null) {
        $arr_bet_line = explode(' ', $value['bet_line']);
        $live_results_p1 = $value['live_results_p1'];
        $live_results_p2 = $value['live_results_p2'];
        if (1 == $value['bet_name']) {
            $live_results_p1 = $live_results_p1 + (float) $arr_bet_line[0];
        } else {
            $live_results_p2 = $live_results_p2 + (float) $arr_bet_line[0];
        }

        if (1 == $value['bet_name'] && $live_results_p1 > $live_results_p2) {
            $bet_status = 2;
        } else if (2 == $value['bet_name'] && $live_results_p1 < $live_results_p2) {
            $bet_status = 2;
        } else if ('X' == $value['bet_name'] && $live_results_p1 == $live_results_p2) {
            $bet_status = 2;
        } else {
            $bet_status = 4;
        }

        return array($bet_status, $value['bet_price']);
    }

    // 적중유무 판단
    public static function getBetStatus($value, $logger) {
        $bet_status = 1;
        $arr_bet_base_line = explode(' ', $value['ls_markets_base_line']);
        $value['ls_markets_base_line'] = (float) $arr_bet_base_line[0];

        switch ($value['ls_markets_id']) {
            case 1: // 승무패
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period) {
                    break;
                }
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                $bet_status = BetDataUtil::getBetStatus1X2($value);
                break;
            case 2: // 오버언더
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period) {
                    break;
                }

                if (VOLLEYBALL == $value['fixture_sport_id']) { // 배구 경기는 세트별 점수 합산이다.
                    $period = BetDataUtil::getPeriodTotalSumScore($value);

                    if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                        break;
                    }
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 3: // 핸디캡
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;
            case 4 :  // 전반전 + 풀타임
                list($bet_status, $period ) = BetDataUtil::checkData($value, '전반전', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $arr_bet_name = explode("/", $value['bet_name']);
                if (1 == $arr_bet_name[0] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        2 == $arr_bet_name[0] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[0] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $period_full = BetDataUtil::getPeriodSumScore('풀타임', $value, $logger);
                if (-1 == $period_full[0] || -1 == $period_full[1]) {
                    $bet_status = 1;
                    break;
                }
                $value['live_results_p1'] = $period_full[0];
                $value['live_results_p2'] = $period_full[1];

                if (1 == $arr_bet_name[1] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        2 == $arr_bet_name[1] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[1] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $bet_status = 2;
                break;

            case 5: // 총득점홀짝
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period) {
                    break;
                }
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $remain = ($value['live_results_p1'] + $value['live_results_p2']) % 2;
                if (1 == $remain && 'Odd' == $value['bet_name']) {
                    $bet_status = 2;
                } else if (0 == $remain && 'Even' == $value['bet_name']) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }

                break;
            case 6 :  // 정확한 스코어
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                $arr_bet_name = explode('-', $value['bet_name']);
                if ($arr_bet_name[0] == $value['live_results_p1'] && $arr_bet_name[1] == $value['live_results_p2']) {
                    $bet_status = 2;
                }
                break;
            case 7: // 더블찬스
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if (($value['live_results_p1'] >= $value['live_results_p2'] && '1X' == $value['bet_name']) ||
                        ($value['live_results_p1'] != $value['live_results_p2'] && '12' == $value['bet_name']) ||
                        ($value['live_results_p1'] <= $value['live_results_p2'] && 'X2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;

            case 9 :  // 1P 정확한스코어
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                $arr_bet_name = explode('-', $value['bet_name']);
                if ($arr_bet_name[0] == $value['live_results_p1'] && $arr_bet_name[1] == $value['live_results_p2']) {
                    $bet_status = 2;
                }
                break;
            case 11: // 총코너킥 오버언더

                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $data_object = json_decode($value['livescore']);
                $Statistics = $data_object->Statistics;
                if (true == empty($Statistics) || !isset($Statistics) || 0 == count($Statistics)) {
                    $bet_status = 1;
                    break;
                }
                if (SOCCER == $value['fixture_sport_id']) { // 축구
                    foreach ($Statistics as $key => $Statistic) {
                        if (1 != $Statistic->Type)
                            continue;
                        if (($value['ls_markets_base_line'] < ($Statistic->Results[0]->Value + $Statistic->Results[1]->Value ) && 'Over' == $value['bet_name']) ||
                                ($value['ls_markets_base_line'] > ($Statistic->Results[0]->Value + $Statistic->Results[1]->Value ) && 'Under' == $value['bet_name'])) {
                            $bet_status = 2;
                        } else if ($value['ls_markets_base_line'] == $Statistic->Results[0]->Value + $Statistic->Results[1]->Value) {
                            $bet_status = 6;
                            //$value['bet_price'] = 1.0;
                        }

                        $value['live_results_p1'] = $Statistic->Results[0]->Value;
                        $value['live_results_p2'] = $Statistic->Results[1]->Value;
                        break;
                    }
                }

                break;

            case 13: // 유로피언 핸디캡
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusEuroPeanHandicap($value);
                break;
            case 16 : // 첫 득점팀 // No Goal, 1,2
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $data_object = json_decode($value['livescore']);
                $Periods = $data_object->Periods;
                $Seconds = 1000000000;
                $ParticipantPosition = -1;
                if (true == empty($Periods) || !isset($Periods) || 0 == count($Periods)) {
                    $bet_status = 1;
                    //$logger->debug("16 bet_status : " . $bet_status . ' Statistics : ' . json_encode($Statistics));
                    break;
                }
                if ("No Goal" == $value['bet_name'] && 0 == ($value['live_results_p1'] + $value['live_results_p2'])) {
                    $bet_status = 2;
                    break;
                }

                break;

            case 17 : // 양팀 득점 성공   
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if ('Yes' == $value['bet_name'] && ($value['live_results_p1'] > 0 && $value['live_results_p2'] > 0)) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && ($value['live_results_p1'] <= 0 || $value['live_results_p2'] <= 0)) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }
                break;
            case 21: // 언더오버 [1 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);

                if (1 == $bet_status || null == $period) {
                    break;
                }

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;
                $result = (float) $value['live_results_p1'] + $value['live_results_p2'];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $result);
                break;

            case 22 :  // 원정팀 득점 성공
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;
                //$value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if ('Yes' == $value['bet_name'] && ( $value['live_results_p2'] > 0)) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && ($value['live_results_p2'] <= 0)) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }
                break;

            case 23 :  // 홈팀 득점 성공
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period[0];
                //$value['live_results_p2'] = $period[1];

                if ('Yes' == $value['bet_name'] && ($value['live_results_p1'] > 0 )) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && ($value['live_results_p1'] <= 0)) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }
                break;
            case 28 : // 오버언더 연장포함
                //list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                if (ICEHOCKEY == $value['fixture_sport_id']) { // 아이스하키는 풀타임으로 체크
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                } else if (BASEBALL == $value['fixture_sport_id'] && 1 == $value['bet_type']) { // 야구 
                    list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                    if (1 == $bet_status || null == $period) {
                        list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                        if (1 == $bet_status || null == $period) {
                            break;
                        }
                    }

                    list($bet_status, $period_nine) = BetDataUtil::checkPeriodData($value, 9, $logger);

                    if (1 == $bet_status || null == $period_nine) {
                        $bet_status = 1;
                        break;
                    }
                } else {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                    if (1 == $bet_status || null == $period) {
                        list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                        if (1 == $bet_status || null == $period) {
                            break;
                        }
                    }
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);

                break;
            case 30 : // 언더오버 - 코너 [홈팀]
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $data_object = json_decode($value['livescore']);
                $Statistics = $data_object->Statistics;
                $nCount = -1;

                if (SOCCER == $value['fixture_sport_id']) { // 축구
                    foreach ($Statistics as $key => $Statistic) {

                        if (1 != $Statistic->Type)
                            continue;

                        $nCount = $Statistic->Results[0]->Value;

                        $value['live_results_p1'] = $Statistic->Results[0]->Value;
                        $value['live_results_p2'] = $Statistic->Results[1]->Value;
                        break;
                    }
                }

                //$value['result_extra'] = $nCount;
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $nCount);
                break;
            case 31 : // 언더오버 - 코너 [원정팀]
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $data_object = json_decode($value['livescore']);
                $Statistics = $data_object->Statistics;
                $nCount = -1;

                if (SOCCER == $value['fixture_sport_id']) { // 축구
                    foreach ($Statistics as $key => $Statistic) {

                        if (1 != $Statistic->Type)
                            continue;

                        $nCount = $Statistic->Results[1]->Value;

                        $value['live_results_p1'] = $Statistic->Results[0]->Value;
                        $value['live_results_p2'] = $Statistic->Results[1]->Value;
                        break;
                    }
                }

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $nCount);
                break;
            //   홈팀 스코어
            case 34 :
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if ("Yes" == $value['bet_name'] && $value['live_results_p1'] == 0 ||
                        "No" == $value['bet_name'] && $value['live_results_p1'] != 0) {
                    break;
                }

                $bet_status = 2;

                break;
            //   원정팀 스코어
            case 35 :
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || 6 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if ("Yes" == $value['bet_name'] && $value['live_results_p2'] == 0 ||
                        "No" == $value['bet_name'] && $value['live_results_p2'] != 0) {
                    break;
                }

                $bet_status = 2;
                break;
            case 41: // 승무패 [1 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && '1' == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && '2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 42: // 승무패 [2 피리어드], 후반전
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 2, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && '1' == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && '2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }

                break;
            case 43: // 승무패 [3 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 3, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && '1' == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && '2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 44: // 승무패 [4 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 4, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && '1' == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && '2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 45: // 언더오버 [2 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 2, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 46: // 언더오버 [3 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 3, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;

            case 47: // 언더오버 [4 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 4, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;

            case 48: // 언더오버 [5 피리어드]

                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;

            case 49: // 승무패 [5 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && '1' == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && '2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;

            case 51: // 총득점홀짝(연장포함)
                list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                if (1 == $bet_status || null == $period) {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                }
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $remain = ($value['live_results_p1'] + $value['live_results_p2']) % 2;
                if (1 == $remain && 'Odd' == $value['bet_name']) {
                    $bet_status = 2;
                } else if (0 == $remain && 'Even' == $value['bet_name']) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }

                break;
            case 52: // 승패
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 53: // 핸디캡 [전반전]

                list($bet_status, $period) = BetDataUtil::checkData($value, '전반전', $logger);

                if (1 == $bet_status || null == $period)
                    break;


                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 63 : // 승패 [전반전]
                list($bet_status, $period) = BetDataUtil::checkData($value, '전반전', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 64: // 핸디캡 [1 피리어드] 
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);

                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 65: // 핸디캡 [2 피리어드] 
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 2, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 66: // 핸디캡 [3 피리어드] 
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 3, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 67: // 핸디캡 [4 피리어드] 
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 4, $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 69: // 연장전 발생
                {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                    if (1 == $bet_status || null == $period)
                        break;

                    $period = BetDataUtil::getPeriodSumScore('연장전', $value, $logger);

                    if ("Yes" == $value['bet_name'] && -1 !== $period[0] ||
                            "No" == $value['bet_name'] && -1 === $period[0]) {
                        $bet_status = 2;
                        break;
                    }
                }

                break;
            // 최고득점 피리어드    
            case 70 :
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                list($period, $bAllSameScore) = BetDataUtil::getPeriodBestPeriod('최고득점 피리어드', $value);
                if (null === $period) {
                    $bet_status = 1;
                    break;
                }

                if ("All Periods The Same" == $value['bet_name'] && false == $bAllSameScore ||
                        ("1st Period" == $value['bet_name'] && 1 != $period->Type) ||
                        ("2nd Period" == $value['bet_name'] && 2 != $period->Type) ||
                        ("3rd Period" == $value['bet_name'] && 3 != $period->Type) ||
                        ("4th Period" == $value['bet_name'] && 4 != $period->Type)) {
                    break;
                }

                $bet_status = 2;
                break;
            // 최고득점 하프  
            case 71 :
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                list($period, $bAllSameScore) = BetDataUtil::getPeriodBestPeriod('최고득점 하프', $value);
                if (null === $period) {
                    $bet_status = 1;
                    break;
                }
                if ("All Periods The Same" == $value['bet_name'] && false == $bAllSameScore ||
                        (48242 == $value['fixture_sport_id'] && "1st Half" == $value['bet_name'] && (1 != $period->Type && 2 != $period->Type) ) ||
                        (48242 == $value['fixture_sport_id'] && "2nd Half" == $value['bet_name'] && (3 != $period->Type && 4 != $period->Type)) ||
                        (SOCCER == $value['fixture_sport_id'] && "1st Half" == $value['bet_name'] && (10 != $period->Type)) ||
                        (SOCCER == $value['fixture_sport_id'] && "2nd Half" == $value['bet_name'] && (20 != $period->Type))) {
                    break;
                }

                $bet_status = 2;
                break;
            case 77: // 언더오버 [전반전]
                list($bet_status, $period) = BetDataUtil::checkData($value, '전반전', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;

            case 95: // 코너 - 핸디캡  

                if (SOCCER != $value['fixture_sport_id'])
                    break;

                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $data_object = json_decode($value['livescore']);
                $Statistics = $data_object->Statistics;
                $nCount_1p = -1;
                $nCount_2p = -1;

                if (true == empty($Statistics) || !isset($Statistics) || 0 == count($Statistics)) {
                    $bet_status = 1;
                    break;
                }

                foreach ($Statistics as $key => $Statistic) {

                    if (1 != $Statistic->Type)
                        continue;

                    $nCount_1p = $Statistic->Results[0]->Value;
                    $nCount_2p = $Statistic->Results[1]->Value;
                    $value['live_results_p1'] = $nCount_1p;
                    $value['live_results_p2'] = $nCount_2p;
                    break;
                }

                if (-1 === $nCount_1p || -1 === $nCount_2p) {
                    $bet_status = 1;
                    break;
                }

                $arr_bet_line = explode(' ', $value['bet_line']);

                if (1 == $value['bet_name']) {
                    $nCount_1p = $nCount_1p + (float) $arr_bet_line[0];
                } else {
                    $nCount_2p = $nCount_2p + (float) $arr_bet_line[0];
                }

                if (($nCount_1p > $nCount_2p && 1 == $value['bet_name']) ||
                        ($nCount_1p < $nCount_2p && 2 == $value['bet_name'])) {
                    $bet_status = 2;
                } else if ($nCount_1p == $nCount_2p) {
                    $bet_status = 6;
                }
                break;
            // 홈팀 무득점
            case 98: {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period)
                        break;

                    $value['live_results_p1'] = $period[0];
                    $value['live_results_p2'] = $period[1];

                    if ("Yes" == $value['bet_name'] && 0 < $value['live_results_p1']) {
                        break;
                    } else if ("No" == $value['bet_name'] && 0 == $value['live_results_p1']) {
                        break;
                    }
                    $bet_status = 2;
                }
                break;
            // 원정팀 무득점
            case 99: {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period)
                        break;

                    $value['live_results_p1'] = $period[0];
                    $value['live_results_p2'] = $period[1];

                    if ("Yes" == $value['bet_name'] && 0 < $value['live_results_p2']) {
                        break;
                    } else if ("No" == $value['bet_name'] && 0 == $value['live_results_p2']) {
                        break;
                    }
                    $bet_status = 2;
                }
                break;
            case 101: // 홈팀 오버언더

                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);

                if (1 == $bet_status || null == $period)
                    break;


                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;

            //$logger->debug("getBetStatus markets_id: 101" . 'live_results_p1 : ' . $value['live_results_p1'] . ' live_results_p2 : ' . $value['live_results_p2'] . ' bet_name :' . $value['bet_name'] . ' bet_status : ' . $bet_status . ' ls_markets_base_line :' . $value['ls_markets_base_line']);

            case 102: // 원정팀 오버언더
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                break;

            case 113 : // 양팀 모두득점 [전반전]
                list($bet_status, $period) = BetDataUtil::checkData($value, '전반전', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                if (($value['live_results_p1'] > 0 && $value['live_results_p2'] > 0 && 'Yes' == $value['bet_name']) ||
                        ($value['live_results_p1'] <= 0 || $value['live_results_p2'] <= 0 && 'No' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 153: // 언더오버 [1 피리어드] <홈팀>
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;

            case 155: // 언더오버 [1 피리어드] <원정팀>
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                break;

            case 202: // 1이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 1, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;
                $bet_status = BetDataUtil::getBetStatus12($value);

                break;

            case 203: // 2이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 2, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                $bet_status = BetDataUtil::getBetStatus12($value);

                break;

            case 204: // 3이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 3, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;
                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 205: // 4이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 4, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 206: // 5이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 211 : // 양팀 모두득점 [후반전]
                list($bet_status, $period) = BetDataUtil::checkData($value, '후반전', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                if (($value['live_results_p1'] > 0 && $value['live_results_p2'] > 0 && 'Yes' == $value['bet_name']) ||
                        ($value['live_results_p1'] <= 0 || $value['live_results_p2'] <= 0 && 'No' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 220 : // 원정팀 오버언더 연장포함
                list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                if (1 == $bet_status || null == $period) {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                break;

            case 221 : // 홈팀 오버언더 연장포함
                list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                if (1 == $bet_status || null == $period) {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;

            case 226 : // 승패 연장포함
                list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                if (1 == $bet_status || null == $period) {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $bet_status = BetDataUtil::getBetStatus12($value);

                break;

            case 235: // 1~5이닝 승무패
                //if (154914 == $value['fixture_sport_id']) {
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $period = BetDataUtil::getPeriodTotalSumScore($value);
                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                $bet_status = BetDataUtil::getBetStatus1X2($value);

                break;
            case 236: // 언더오버 [5 피리어드]

                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $period = BetDataUtil::getPeriodTotalSumScore($value);
                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;

            case 281: // 핸디캡 [5 피리어드] 
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $period = BetDataUtil::getPeriodTotalSumScore($value);
                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;
            case 282 : // 승무패 [전반전]
                list($bet_status, $period) = BetDataUtil::checkData($value, '전반전', $logger);

                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $bet_status = BetDataUtil::getBetStatus1X2($value);

                break;
            case 322: // 언더/동점/오버
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverExactlyUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;

            case 342 : // 핸디캡 연장포함

                /* list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                  if (1 == $bet_status || null == $period) {
                  list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                  if (1 == $bet_status || null == $period) {
                  break;
                  }
                  } */

                if (ICEHOCKEY == $value['fixture_sport_id']) { // 아이스하키는 풀타임으로 체크
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                } else {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                    if (1 == $bet_status || null == $period) {
                        list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                        if (1 == $bet_status || null == $period) {
                            break;
                        }
                    }
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 348 : // 6th Period Winner 승무패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 6, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && 1 == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && 2 == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 349 : // 7th Period Winner
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 7, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                if (($value['live_results_p1'] > $value['live_results_p2'] && 1 == $value['bet_name']) ||
                        ($value['live_results_p1'] == $value['live_results_p2'] && 'X' == $value['bet_name']) ||
                        ($value['live_results_p1'] < $value['live_results_p2'] && 2 == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 352: // 언더오버 [6 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 6, $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 353: // 언더오버 [7 피리어드]
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 7, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period->Results[0]->Value;
                $value['live_results_p2'] = $period->Results[1]->Value;

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 390 : // 전반전 + 풀타임 [연장전 포함]
                list($bet_status, $period) = BetDataUtil::checkData($value, '전반전', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $arr_bet_name = explode("/", $value['bet_name']);
                if (1 == $arr_bet_name[0] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        2 == $arr_bet_name[0] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[0] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $period_full = BetDataUtil::getPeriodSumScore('풀타임', $value, $logger);
                if (-1 == $period_full[0] || -1 == $period_full[1]) {
                    $bet_status = 1;
                    break;
                }
                $value['live_results_p1'] = $period_full[0];
                $value['live_results_p2'] = $period_full[1];

                if (1 == $arr_bet_name[1] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        2 == $arr_bet_name[1] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[1] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $bet_status = 2;
                break;
            case 427: // 승무패 및 언더오버
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if (('1 And Over' == $value['bet_name'] && ($value['live_results_p1'] > $value['live_results_p2']) && ($value['ls_markets_base_line'] < $value['live_results_p1'] + $value['live_results_p2'])) ||
                        ('1 And Under' == $value['bet_name'] && ($value['live_results_p1'] > $value['live_results_p2']) && ($value['ls_markets_base_line'] > $value['live_results_p1'] + $value['live_results_p2'])) ||
                        ('2 And Over' == $value['bet_name'] && ($value['live_results_p1'] < $value['live_results_p2']) && ($value['ls_markets_base_line'] < $value['live_results_p1'] + $value['live_results_p2'])) ||
                        ('2 And Under' == $value['bet_name'] && ($value['live_results_p1'] < $value['live_results_p2']) && ($value['ls_markets_base_line'] > $value['live_results_p1'] + $value['live_results_p2'])) ||
                        ('X And Over' == $value['bet_name'] && ($value['live_results_p1'] == $value['live_results_p2']) && ($value['ls_markets_base_line'] < $value['live_results_p1'] + $value['live_results_p2'])) ||
                        ('X And Under' == $value['bet_name'] && ($value['live_results_p1'] == $value['live_results_p2']) && ($value['ls_markets_base_line'] > $value['live_results_p1'] + $value['live_results_p2']))) {
                    $bet_status = 2;
                }

                break;
            case 464 : // 승패 [후반 및 연장포함] 
                list($bet_status, $period) = BetDataUtil::checkData($value, '연장포함', $logger);
                if (1 == $bet_status || null == $period) {
                    list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                    if (1 == $bet_status || null == $period) {
                        break;
                    }
                }

                $value['live_results_p1'] = $period_over_time[0];
                $value['live_results_p2'] = $period_over_time[1];
                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 472 : // 승부치기 여부 
                list($bet_status, $period) = BetDataUtil::checkData($value, '패널티', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                if ('Yes' == $value['bet_name'] && -1 != $value['live_results_p1'] && -1 != $value['live_results_p2']) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && -1 == $value['live_results_p1'] && -1 == $value['live_results_p2']) {
                    $bet_status = 2;
                }

                break;
            case 525: // 1~7이닝 오버언더
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 7, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $period = BetDataUtil::getPeriodTotalSumScore($value);
                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 866: // Asian Handicap Sets
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value);
                break;

            case 669: // 1세트 첫용
                $bet_status = 1;
                break;

            case 1170: // 1세트 첫킬
                $bet_status = 1;
                break;

            case 1537: // 홈팀승 + 오버언더 Yes/Over,No/Over,Yes/Under,No/Under
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $arr_bet_name = explode(' ', $value['bet_name']);
                if ('Yes' == $arr_bet_name[0] && 'Over' == $arr_bet_name[1]) {
                    if ($value['live_results_p1'] > $value['live_results_p2'] && $value['ls_markets_base_line'] < ($value['live_results_p1'] + $value['live_results_p2'])) {
                        $bet_status = 2;
                    }
                } else if ('Yes' == $arr_bet_name[0] && 'Under' == $arr_bet_name[1]) {
                    if ($value['live_results_p1'] > $value['live_results_p2'] && ($value['live_results_p1'] + $value['live_results_p2']) < $value['ls_markets_base_line']) {
                        $bet_status = 2;
                    }
                } else if ('No' == $arr_bet_name[0] && 'Over' == $arr_bet_name[1]) {
                    if ($value['live_results_p1'] < $value['live_results_p2'] && value['ls_markets_base_line'] < ($value['live_results_p1'] + $value['live_results_p2'])) {
                        $bet_status = 2;
                    }
                } else if ('No' == $arr_bet_name[0] && 'Under' == $arr_bet_name[1]) {
                    if ($value['live_results_p1'] < $value['live_results_p2'] && ($value['live_results_p1'] + $value['live_results_p2']) < $value['ls_markets_base_line']) {
                        $bet_status = 2;
                    }
                }

                break;
            case 1538: // 원정팀승 + 오버언더 Yes/Over,No/Over,Yes/Under,No/Under
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $arr_bet_name = explode(' ', $value['bet_name']);
                if ('Yes' == $arr_bet_name[0] && 'Over' == $arr_bet_name[1]) {
                    if ($value['live_results_p2'] > $value['live_results_p1'] && $value['ls_markets_base_line'] < ($value['live_results_p1'] + $value['live_results_p2'])) {
                        $bet_status = 2;
                    }
                } else if ('Yes' == $arr_bet_name[0] && 'Under' == $arr_bet_name[1]) {
                    if ($value['live_results_p2'] > $value['live_results_p1'] && ($value['live_results_p1'] + $value['live_results_p2']) < $value['ls_markets_base_line']) {
                        $bet_status = 2;
                    }
                } else if ('No' == $arr_bet_name[0] && 'Over' == $arr_bet_name[1]) {
                    if ($value['live_results_p2'] < $value['live_results_p1'] && value['ls_markets_base_line'] < ($value['live_results_p1'] + $value['live_results_p2'])) {
                        $bet_status = 2;
                    }
                } else if ('No' == $arr_bet_name[0] && 'Under' == $arr_bet_name[1]) {
                    if ($value['live_results_p2'] < $value['live_results_p1'] && ($value['live_results_p1'] + $value['live_results_p2']) < $value['ls_markets_base_line']) {
                        $bet_status = 2;
                    }
                }
                break;
            case 1558: // Asian Handicap Points
                list($bet_status, $period) = BetDataUtil::checkData($value, '풀타임', $logger);
                if (1 == $bet_status || null == $period)
                    break;

                $period = BetDataUtil::getPeriodTotalSumScore($value);

                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }
                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                list($bet_status, $value['bet_price']) = BetDataUtil::getBetStatusHandicap($value, $logger);
                break;
            case 1618: // 1~5이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 5, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $period = BetDataUtil::getPeriodTotalSumScore($value);
                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];
                $bet_status = BetDataUtil::getBetStatus12($value);

                break;
            case 2681: // 1~7이닝 승패
                list($bet_status, $period) = BetDataUtil::checkPeriodData($value, 7, $logger);
                if (1 == $bet_status || null == $period)
                    break;
                $period = BetDataUtil::getPeriodTotalSumScore($value);
                if (-1 === $period[0] || -1 === $period[1] || !isset($period)) {
                    break;
                }

                $value['live_results_p1'] = $period[0];
                $value['live_results_p2'] = $period[1];

                $bet_status = BetDataUtil::getBetStatus1X2($value);
                break;

            default:
                break;
        }

        return array($bet_status, $value['bet_price'], $value['live_results_p1'], $value['live_results_p2']);
    }

    public static function getMiniBetStatus($value, $data_object, $logger) {

        //$logger->debug('getMiniBetStatus :'.$value['bet_type']);
        if (null == $data_object) {
            //$logger->debug('getMiniBetStatus 1');
            return array(1, $value['bet_price']);
        }

        $bet_status = 4;
        if (3 == $value['bet_type'] || 15 == $value['bet_type']) { // powerball
            if (0 == $data_object->num1 || 0 == ($data_object->num1 + $data_object->num2 + $data_object->num3 + $data_object->num4 + $data_object->num5)) {
                return array(1, $value['bet_price']);
            }
            switch ($value['ls_markets_id']) {
                case 10001: //  홀
                case 14001:
                    if (0 !== $data_object->pb % 2) {
                        $bet_status = 2;
                    }
                    break;
                case 10002: // 짝
                case 14002:
                    if (0 === $data_object->pb % 2) {
                        $bet_status = 2;
                    }
                    break;
                case 10003: // 파워볼(5-9)
                case 14003:
                    if (5 <= $data_object->pb && $data_object->pb <= 9) {
                        $bet_status = 2;
                    }
                    break;
                case 10004: // 파워볼(0-4)
                case 14004:
                    if (0 <= $data_object->pb && $data_object->pb <= 4) {
                        $bet_status = 2;
                    }
                    break;
                case 10005: // 일반볼 대(81-130)
                case 14005:
                    $sum_num = $data_object->num1 + $data_object->num2 + $data_object->num3 + $data_object->num4 + $data_object->num5;
                    if (81 <= $sum_num && $sum_num <= 130) {
                        $bet_status = 2;
                    }
                    break;
                case 10006: // 일반볼 중(65-80)
                case 14006:
                    $sum_num = $data_object->num1 + $data_object->num2 + $data_object->num3 + $data_object->num4 + $data_object->num5;
                    if (65 <= $sum_num && $sum_num <= 80) {
                        $bet_status = 2;
                    }
                    break;
                case 10007: // 일반볼 소(15-64)
                case 14007:
                    $sum_num = $data_object->num1 + $data_object->num2 + $data_object->num3 + $data_object->num4 + $data_object->num5;
                    //$logger->debug('getMiniBetStatus :' . $sum_num);
                    if (15 <= $sum_num && $sum_num <= 64) {
                        $bet_status = 2;
                    }
                    break;
            }
        } else if (4 == $value['bet_type']) { // pladder
            $current_day = date('Y-m-d H:i:s');
            $end_day = date($data_object->edate);
            if ($current_day < $end_day || !isset($data_object->start) || '' == $data_object->start)
                return array(1, $value['bet_price']);
            switch ($value['ls_markets_id']) {
                case 11001: //  좌
                    if ("Left" === $data_object->start) {
                        $bet_status = 2;
                    }
                    break;
                case 11002: // 우 
                    if ("Right" === $data_object->start) {
                        $bet_status = 2;
                    }
                    break;
                case 11003: // 3줄
                    if (3 == $data_object->line) {
                        $bet_status = 2;
                    }
                    break;
                case 11004: // 4줄
                    if (4 == $data_object->line) {
                        $bet_status = 2;
                    }
                    break;
                case 11005: // 홀
                    if ("Odd" == $data_object->oe) {
                        $bet_status = 2;
                    }
                    break;
                case 11006: // 짝
                    if ("Even" == $data_object->oe) {
                        $bet_status = 2;
                    }
                    break;
            }
        } if (5 == $value['bet_type']) { // kladder
            $current_day = date('Y-m-d H:i:s');
            $end_day = date($data_object->edate);
            if ($current_day < $end_day || !isset($data_object->start) || '' == $data_object->start)
                return array(1, $value['bet_price']);
            switch ($value['ls_markets_id']) {
                case 12001: //  좌
                    if ("Left" === $data_object->start) {
                        $bet_status = 2;
                    }
                    break;
                case 12002: // 우 
                    if ("Right" === $data_object->start) {
                        $bet_status = 2;
                    }
                    break;
                case 12003: // 3줄
                    if (3 == $data_object->line) {
                        $bet_status = 2;
                    }
                    break;
                case 12004: // 4줄
                    if (4 == $data_object->line) {
                        $bet_status = 2;
                    }
                    break;
                case 12005: // 홀
                    if ("Odd" == $data_object->oe) {
                        $bet_status = 2;
                    }
                    break;
                case 12006: // 짝
                    if ("Even" == $data_object->oe) {
                        $bet_status = 2;
                    }
                    break;
            }
        } if (6 == $value['bet_type']) { // b_soccer
            if ("End" != $data_object->sts) {
                $bet_status = 1;
            }
            switch ($value['ls_markets_id']) {
                case 13001: //  승
                    if ("1x2" == $data_object->type && "End" == $data_object->sts) {
                        if ($data_object->scorea < $data_object->scoreh) {
                            $bet_status = 2;
                            $value['bet_price'] = $data_object->win;
                        }
                    }
                    break;
                case 13002: // 무
                    if ("1x2" == $data_object->type && "End" == $data_object->sts) {
                        if ($data_object->scorea == $data_object->scoreh) {
                            $bet_status = 2;
                            $value['bet_price'] = $data_object->draw;
                        }
                    }
                    break;
                case 13003: // 패
                    if ("1x2" == $data_object->type && "End" == $data_object->sts) {
                        if ($data_object->scorea > $data_object->scoreh) {
                            $bet_status = 2;
                            $value['bet_price'] = $data_object->lose;
                        }
                    }
                    break;
                case 13004: // 오버
                    if ("ou" == $data_object->type && "End" == $data_object->sts) {
                        if ("Over" == $data_object->res) {
                            $bet_status = 2;
                            $value['bet_price'] = $data_object->lose;
                        }
                    }
                    break;
                case 13005: // 언더
                    if ("ou" == $data_object->type && "End" == $data_object->sts) {
                        if ("Under" == $data_object->res) {
                            $bet_status = 2;
                            $value['bet_price'] = $data_object->win;
                        }
                    }
                    break;
            }
        }

        return array($bet_status, $value['bet_price']);
    }
}
