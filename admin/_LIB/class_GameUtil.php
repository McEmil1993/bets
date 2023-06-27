<?php

/** @noinspection ALL */
include_once(_LIBPATH . '/class_GameStatusUtil.php');

class GameUtil {

    public static function mergeBetData($bets) {
        $returnBets = [];

        foreach ($bets as $bet) {
            //if ($bet['main_book_maker'] != $bet['providers_id'] && $bet['sub_book_maker'] != $bet['providers_id'])
            //    continue;
            if ($bet['main_book_maker'] != $bet['providers_id'])
                continue;

            if (isset($returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['providers_id']])) {
                $mergeBet = $returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['providers_id']];
            } else {
                // 팀네임 설정
                $p1_name = $bet['fixture_participants_1_name'];
                $p2_name = $bet['fixture_participants_2_name'];
                if ($bet['p1_display_name'] != null || $bet['p1_display_name'] != '') {
                    $p1_name = $bet['p1_display_name'];
                }
                if ($bet['p2_display_name'] != null || $bet['p2_display_name'] != '') {
                    $p2_name = $bet['p2_display_name'];
                }
                // 리그 명 설정
                $league_display_name = $bet['fixture_league_name'];
                if ($bet['league_display_name'] != null || $bet['league_display_name'] != '') {
                    $league_display_name = $bet['league_display_name'];
                }
                // 지역 이미지 패스 설정
                $location_image_path = '/images/flag/' . $bet['fixture_location_id'] . '.png';
                if ($bet['location_image_path'] != null || $bet['location_image_path'] != '') {
                    $location_image_path = $bet['location_image_path'];
                }

                $mergeBet = [
                    'fixture_id' => $bet['fixture_id'],
                    'markets_id' => $bet['markets_id'],
                    'markets_name_base_line' => $bet['markets_name'] . ' ' . $bet['bet_base_line'],
                    'markets_name' => $bet['markets_name'],
                    'fixture_start_date' => $bet['fixture_start_date'],
                    'fixture_sport_id' => $bet['fixture_sport_id'],
                    'fixture_sport_name' => $bet['sports_display_name'],
                    'fixture_league_name' => $league_display_name,
                    'fixture_participants_1_name' => $p1_name,
                    'fixture_participants_2_name' => $p2_name,
                    'fixture_location_name' => $bet['fixture_location_name'],
                    'fixture_location_image_path' => $location_image_path,
                    'fixture_status' => $bet['fixture_status'],
                    'bet_status' => $bet['bet_status'],
                    //'bet_line' => $bet['bet_line'],
                    'bet_base_line' => $bet['bet_base_line'],
                    'live_results_p1' => $bet['live_results_p1'],
                    'live_results_p2' => $bet['live_results_p2'],
                    'admin_bet_status' => $bet['admin_bet_status'],
                    'main_book_maker' => $bet['main_book_maker'],
                    'sub_book_maker' => $bet['sub_book_maker'],
                    'providers_id' => $bet['providers_id'],
                    'providers_name' => $bet['providers_name'],
                    'limit_bet_price' => $bet['limit_bet_price'],
                    'max_bet_price' => $bet['max_bet_price'],
                    'bet_result_p1' => !isset($bet['result_p1']) ? '' : $bet['result_p1'],
                    'bet_result_p2' => !isset($bet['result_p2']) ? '' : $bet['result_p2'],
                    'bet_result_2_p1' => !isset($bet['result_2_p1']) ? '' : $bet['result_2_p1'],
                    'bet_result_2_p2' => !isset($bet['result_2_p2']) ? '' : $bet['result_2_p2'],
                    'display_status' => $bet['display_status'],
                    'bet_data' => [],
                ];
            }

            if ($bet['providers_id'] == $bet['sub_book_maker']) {
                if (isset($returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['main_book_maker']])) {
                    if (isset($returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['sub_book_maker']])) {
                        $key = $bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['sub_book_maker'];
                        //CommonUtil::logWrite("getDetailSportsFixturesList delete : providers_id : " . $bet['providers_id'] . " markets_id : " . $bet['markets_id'] . " key : " . $key, "info");
                        unset($returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['sub_book_maker']]);
                    }

                    continue;
                }
            } else if ($bet['providers_id'] == $bet['main_book_maker']) {
                if (isset($returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['sub_book_maker']])) {
                    unset($returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['sub_book_maker']]);
                }
            }


            $mergeBet['bet_data'][] = array('bet_id' => $bet['bet_id'], 'bet_name' => $bet['bet_name'], 'bet_price' => $bet['bet_price'], 'bet_price_hit' => $bet['bet_price_hit'],
                'bet_start_price' => $bet['bet_start_price'], 'bet_line' => $bet['bet_line'], 'bet_status' => $bet['bet_status'],
                'bet_base_line' => $bet['bet_base_line'], 'bet_id_total_bet_money' => $bet['bet_id_total_bet_money'], 'total_bet_money' => $bet['total_bet_money']);

            $returnBets[$bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['providers_id']] = $mergeBet;
        }

        $arrLeague = array(918, 3371, 5283, 5556, 5769, 14121, 14493, 14497, 14498, 14499, 14530, 14761, 15181, 15997, 16176, 16362, 16482, 16745, 16791, 20736, 22893, 23798, 24136, 25686, 27433, 27488, 29301, 32573, 33252, 35133); //33078

        foreach ($returnBets as $bet_key => $mergeBet) {
            if (1 != $mergeBet['markets_id'] && true == in_array($mergeBet['fixture_league_id'], $arrLeague)) {
                unset($returnBets[$bet_key]);
                continue;
            }
        }
        return $returnBets;
    }

    public static function mergeRenewBetData2($bets) {
        # 첫번째 데이터 설정

        $array_basket_main_sub_market = array(342, 64, 53, 28, 21, 77, 220, 221, 153, 155);
        $array_velly_main_sub_market = array(1558, 2);

        $returnBets = [];
        if (!isset($bets) || 0 == count($bets))
            return $returnBets;

        foreach ($bets as $bet) {

            /*if (BASKETBALL == $bet['fixture_sport_id'] && true == in_array($bet['markets_id'], $array_basket_main_sub_market) ||
                    VOLLEYBALL == $bet['fixture_sport_id'] && true == in_array($bet['markets_id'], $array_velly_main_sub_market)) {
                //if ($bet->main_book_maker != $bet->providers_id && $bet->sub_book_maker != $bet->providers_id)
                if ($bet['main_book_maker'] != $bet['providers_id'])
                    continue;
            }*/


            $key = $bet['fixture_id'] . '_' . $bet['markets_id'] . '_' . $bet['bet_base_line'] . '_' . $bet['providers_id'];
            if (isset($returnBets[$key])) {
                $mergeBet = $returnBets[$key];
                if ($bet['bet_price'] < $mergeBet['fitter_limit_bet_price']) {
                    $mergeBet['fitter_limit_bet_price'] = $bet['bet_price'];
                }
            } else {
                // 팀네임 설정
                $p1_name = $bet['fixture_participants_1_name'];
                $p2_name = $bet['fixture_participants_2_name'];
                if ($bet['p1_display_name'] != null || $bet['p1_display_name'] != '') {
                    $p1_name = $bet['p1_display_name'];
                }
                if ($bet['p2_display_name'] != null || $bet['p2_display_name'] != '') {
                    $p2_name = $bet['p2_display_name'];
                }
                // 리그 명 설정
                $league_display_name = $bet['fixture_league_name'];
                if ($bet['league_display_name'] != null || $bet['league_display_name'] != '') {
                    $league_display_name = $bet['league_display_name'];
                }
                // 지역 이미지 패스 설정
                $location_image_path = '/images/flag/' . $bet['fixture_location_id'] . '.png';
                if ($bet['location_image_path'] != null || $bet['location_image_path'] != '') {
                    $location_image_path = $bet['location_image_path'];
                }

                $mergeBet = [
                    'fixture_id' => $bet['fixture_id'],
                    'markets_id' => $bet['markets_id'],
                    'markets_name_base_line' => $bet['markets_name'] . ' ' . $bet['bet_base_line'],
                    'markets_name' => $bet['markets_name'],
                    'fixture_start_date' => $bet['fixture_start_date'],
                    'fixture_sport_id' => $bet['fixture_sport_id'],
                    'fixture_sport_name' => $bet['sports_display_name'],
                    'fixture_league_name' => $league_display_name,
                    'fixture_participants_1_name' => $p1_name,
                    'fixture_participants_2_name' => $p2_name,
                    'fixture_location_name' => $bet['fixture_location_name'],
                    'fixture_location_image_path' => $location_image_path,
                    'fixture_status' => $bet['fixture_status'],
                    'bet_status' => $bet['bet_status'],
                    'bet_type' => $bet['bet_type'],
                    'bet_base_line' => $bet['bet_base_line'],
                    'live_results_p1' => $bet['live_results_p1'],
                    'live_results_p2' => $bet['live_results_p2'],
                    'admin_bet_status' => $bet['admin_bet_status'],
                    'main_book_maker' => $bet['main_book_maker'],
                    'sub_book_maker' => $bet['sub_book_maker'],
                    'providers_id' => $bet['providers_id'],
                    'providers_name' => $bet['providers_name'],
                    'limit_bet_price' => $bet['limit_bet_price'],
                    'max_bet_price' => $bet['max_bet_price'],
                    'bet_result_p1' => !isset($bet['result_p1']) ? '' : $bet['result_p1'],
                    'bet_result_p2' => !isset($bet['result_p2']) ? '' : $bet['result_p2'],
                    'bet_result_2_p1' => !isset($bet['result_2_p1']) ? '' : $bet['result_2_p1'],
                    'bet_result_2_p2' => !isset($bet['result_2_p2']) ? '' : $bet['result_2_p2'],
                    'display_status' => $bet['display_status'],
                    'bet_data' => [],
                    'fitter_limit_bet_price' => $bet['bet_price'],
                    'passivity_flag' => $bet['passivity_flag'],
                    'bet_status_passivity' => $bet['bet_status_passivity'],
                    'bet_price_passivity' => $bet['bet_price_passivity'],
                ];
            }

            $mergeBet['bet_data'][$bet['bet_name']] = array('bet_id' => $bet['bet_id'], 'bet_name' => $bet['bet_name'], 'bet_price' => $bet['bet_price'], 'bet_price_hit' => $bet['bet_price_hit'], 'bet_status' => $bet['bet_status']
                , 'bet_line' => $bet['bet_line'], 'bet_base_line' => $bet['bet_base_line'], 'bet_id_total_bet_money' => $bet['bet_id_total_bet_money'], 'total_bet_money' => $bet['total_bet_money']);
            $returnBets[$key] = $mergeBet;
        }


        return $returnBets;
        // 최소 최대 배당
        /* foreach ($returnBets as $bet_key => $mergeBet) {
          foreach ($mergeBet['bet_data'] as $key => $value) {

          if (BASKETBALL != $mergeBet['fixture_sport_id'] || VOLLEYBALL != $mergeBet['fixture_sport_id'] || (BASKETBALL == $mergeBet['fixture_sport_id'] && true != in_array($mergeBet['markets_id'], $array_basket_main_sub_market)) ||
          (VOLLEYBALL == $mergeBet['fixture_sport_id'] && true != in_array($mergeBet['markets_id'], $array_velly_main_sub_market))) {
          if (!isset($returnBets[$bet_key]['fitter_limit_bet_price'])) {
          $returnBets[$bet_key]['fitter_limit_bet_price'] = $value['bet_price'];
          } else {
          if ($value['bet_price'] < $returnBets[$bet_key]['fitter_limit_bet_price']) {
          $returnBets[$bet_key]['fitter_limit_bet_price'] = $value['bet_price'];
          }
          }
          }
          }
          } */

        /* $minOddBets = [];
          foreach ($returnBets as $value) {
          $key = $value['fixture_id'] . '_' . $value['markets_id'] . '_' . $value['bet_base_line'];

          //$logger->debug('mergeRenewBetData2 fix id : '.$value['fixture_id'].' market id :'.$value['markets_id'].' BaseLine :'.$value['bet_base_line'].' providerId :'.$value['providers_id']);

          if (!isset($minOddBets[$key])) {
          $minOddBets[$key] = $value;
          } else {
          if (BASKETBALL != $value['fixture_sport_id'] || VOLLEYBALL != $value['fixture_sport_id'] || (BASKETBALL == $value['fixture_sport_id'] && true != in_array($value['markets_id'], $array_basket_main_sub_market)) ||
          (VOLLEYBALL == $value['fixture_sport_id'] && true != in_array($value['markets_id'], $array_velly_main_sub_market))) {
          if ($value['fitter_limit_bet_price'] < $minOddBets[$key]['fitter_limit_bet_price']) {
          unset($minOddBets[$key]);
          }
          }

          $minOddBets[$key] = $value;
          }
          }

          return $minOddBets; */
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
            //$value['bet_price'] = 1.0;
        } else {
            $bet_status = 4;
        }

        CommonUtil::logWrite("getBetStatus getBetStatus12 : value : " . json_encode($value), "info");
        return array($bet_status, $value['bet_price']);
    }

    public static function getBetStatusOverUnder($value, $score) {
        $ls_markets_base_line = (float) $value['ls_markets_base_line'];
        if ('Over' == $value['bet_name'] && ($ls_markets_base_line < (float) $score)) {
            $bet_status = 2;
        } else if ('Under' == $value['bet_name'] && ((float) $score < $ls_markets_base_line )) {
            $bet_status = 2;
        } else if ($ls_markets_base_line == $score) {
            $bet_status = 6;
            //$value['bet_price'] = 1.0;
        } else {
            $bet_status = 4;
        }

        //CommonUtil::logWrite("getBetStatus getBetStatusOverUnder : score : " . $score . " bet_status : " . $bet_status . ' market_id : ' . $value['ls_markets_id'] . ' ls_markets_base_line : ' . $value['ls_markets_base_line'], "info");
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

    public static function getBetStatusHandicap($value) {
        $arr_bet_line = explode(' ', $value['bet_line']);
        $live_results_p1 = $value['live_results_p1'];
        $live_results_p2 = $value['live_results_p2'];
        if (1 == $value['bet_name']) {
            $live_results_p1 = $live_results_p1 + (float) $arr_bet_line[0];
        } else {
            $live_results_p2 = $live_results_p2 + (float) $arr_bet_line[0];
        }

        if (($live_results_p1 > $live_results_p2 && 1 == $value['bet_name']) ||
                ($live_results_p1 < $live_results_p2 && 2 == $value['bet_name'])) {
            $bet_status = 2;
        } else if ($live_results_p1 == $live_results_p2) {
            $bet_status = 6;
            //$value['bet_price'] = 1.0;
        } else {
            $bet_status = 4;
        }
        return array($bet_status, $value['bet_price']);
    }

    public static function getBetStatusEuroPeanHandicap($value) {
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

    public static function getBetStatus($value, $ALBetDAO) {
        $bet_status = 4;

        if ($value['result_p1'] == null || $value['result_p2'] == null || $value['result_p1'] == '' || $value['result_p2'] == '') {
            $bet_status = 1;
            CommonUtil::logWrite("getBetStatus : live_results_p1 : " . $value['live_results_p1'] . " live_results_p2 : " . $value['live_results_p2'], "info");
            return array($bet_status, $value['bet_price'], $value['result_p1'], $value['result_p2'], '');
        }

        $value['live_results_p1'] = (float) $value['result_p1'];
        $value['live_results_p2'] = (float) $value['result_p2'];

        // CommonUtil::logWrite("getBetStatus : live_results_p1 : " . $value['live_results_p1'] . " live_results_p2 : " . $value['live_results_p2'], "info");

        $arr_bet_base_line = explode(' ', $value['ls_markets_base_line']);
        $value['ls_markets_base_line'] = (float) $arr_bet_base_line[0];

        switch ($value['ls_markets_id']) {
            case 1: // 승무패
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 2: // 오버언더
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 3: // 핸디캡
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 4 :  // 전반전 + 풀타임
                $arr_bet_name = explode("/", $value['bet_name']);
                if ('1' == $arr_bet_name[0] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        '2' == $arr_bet_name[0] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[0] && $value['live_results_p1'] != $value['live_results_p2']) {

                    break;
                }

                //CommonUtil::logWrite("getBetStatus 4 : live_results_p1 : " . $value['live_results_p1'] . " live_results_p2 : " . $value['live_results_p2'] . ' status : ' . $bet_status . ' arr_bet_name : ' . json_encode($arr_bet_name), "error");
                $value['live_results_p1'] = $value['result_2_p1'];
                $value['live_results_p2'] = $value['result_2_p2'];

                if ('1' == $arr_bet_name[1] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        '2' == $arr_bet_name[1] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[1] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $bet_status = 2;
                //CommonUtil::logWrite("getBetStatus 4 : live_results_p1 : " . $value['live_results_p1'] . " live_results_p2 : " . $value['live_results_p2'] . ' status : ' . $bet_status . ' arr_bet_name : ' . json_encode($arr_bet_name), "error");
                break;
            case 5: // 총득점홀짝

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
                if (0 != $value['bet_settlement']) {
                    if (1 == $value['bet_settlement']) {
                        $bet_status = 4;
                    } else if (2 == $value['bet_settlement']) {
                        $bet_status = 2;
                    } else {
                        $bet_status = 1;
                    }
                } else {
                    if ('Any Other Score' == $value['bet_name']) {
                        $bet_status = 2;
                        $fixture_id = $value['ls_fixture_id'];
                        $bet_type = $value['bet_type'];
                        $markets_id = $value['ls_markets_id'];
                        $p_data['sql'] = "SELECT bet_name FROM lsports_bet where fixture_id = $fixture_id and bet_type = $bet_type and markets_id = $markets_id and bet_name <> 'Any Other Score';";
                        $arrbet_names = $ALBetDAO->getQueryData($p_data);
                        foreach ($arrbet_names as $bet_name) {
                            $arr_bet_name = explode('-', $bet_name['bet_name']);
                            if ($arr_bet_name[0] == $value['live_results_p1'] && $arr_bet_name[1] == $value['live_results_p2']) {
                                $bet_status = 4;
                                break;
                            }
                        }
                    } else {
                        $arr_bet_name = explode('-', $value['bet_name']);
                        if ($arr_bet_name[0] == $value['live_results_p1'] && $arr_bet_name[1] == $value['live_results_p2']) {
                            $bet_status = 2;
                        }
                    }
                }

                break;
            case 7: // 더블찬스
                if (($value['live_results_p1'] >= $value['live_results_p2'] && '1X' == $value['bet_name']) ||
                        ($value['live_results_p1'] != $value['live_results_p2'] && '12' == $value['bet_name']) ||
                        ($value['live_results_p1'] <= $value['live_results_p2'] && 'X2' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 9 :  // 1P  정확한 스코어
                if ('Any Other Score' == $value['bet_name']) {
                    if (1 == $value['bet_settlement']) {
                        $bet_status = 4;
                    } else if (2 == $value['bet_settlement']) {
                        $bet_status = 2;
                    } else {
                        $bet_status = 1;
                    }
                } else {
                    $arr_bet_name = explode('-', $value['bet_name']);
                    if ($arr_bet_name[0] == $value['live_results_p1'] && $arr_bet_name[1] == $value['live_results_p2']) {
                        $bet_status = 2;
                    }
                }
                break;
            case 11: // 총코너킥 오버언더
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 13: // 유로피언 핸디캡
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusEuroPeanHandicap($value);
                break;
            case 16 : // 첫 득점팀 => 홈팀 1 : 0, 노골 0 : 0, 원정 0 : 1 
                if (1 == $value['live_results_p1'] && 1 == $value['bet_name'] || 1 == $value['live_results_p2'] && 2 == $value['bet_name'] ||
                        (0 == $value['live_results_p1'] && 0 == $value['live_results_p2']) && 'No Goal' == $value['bet_name']) {
                    $bet_status = 2;
                }
                break;
            case 17 : // 양팀 득점 성공
                if ('Yes' == $value['bet_name'] && ($value['live_results_p1'] > 0 && $value['live_results_p2'] > 0)) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && ($value['live_results_p1'] <= 0 || $value['live_results_p2'] <= 0)) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }
                //CommonUtil::logWrite("17 getBetStatus : live_results_p1 : " . $value['live_results_p1'] . " live_results_p2 : " . $value['live_results_p2'] . ' bet_name :' . $value['bet_name'] . ' status : ' . $bet_status, "error");
                break;
            case 21: // 언더오버 [1 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 22 :  // 원정팀 득점 성공
                if ('Yes' == $value['bet_name'] && ($value['live_results_p2'] > 0 )) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && ($value['live_results_p2'] <= 0)) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }
                break;
            case 23 :  // 홈팀 득점 성공
                if ('Yes' == $value['bet_name'] && ($value['live_results_p1'] > 0 )) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && ($value['live_results_p1'] <= 0)) {
                    $bet_status = 2;
                } else {
                    $bet_status = 4;
                }
                break;
            case 28 : // 오버언더 연장포함
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 30 : // 언더오버 - 코너 [홈팀] => live_results_p1
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;
            case 31 : // 언더오버 - 코너 [원정팀] => live_results_p2
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                break;
            case 34 :
                if ("Yes" == $value['bet_name'] && $value['live_results_p1'] == 0 ||
                        "No" == $value['bet_name'] && $value['live_results_p1'] != 0) {
                    break;
                }

                $bet_status = 2;

                break;
            case 35 :
                if ("Yes" == $value['bet_name'] && $value['live_results_p2'] == 0 ||
                        "No" == $value['bet_name'] && $value['live_results_p2'] != 0) {
                    break;
                }

                $bet_status = 2;

                break;
            case 41: // 승무패 [1 피리어드]
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 42: // 승무패 [2 피리어드] 후반전
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 43: // 승무패 [3 피리어드]
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 44: // 승무패 [4 피리어드]
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 45: // 언더오버 [2 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 46: // 언더오버 [3 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 47: // 언더오버 [4 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 48: // 언더오버 [5 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 49: // 승무패 [5 피리어드]
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 51: // 총득점홀짝(연장포함)

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
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 53: // 핸디캡 [전반전]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 63 : // 승패 [전반전]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 64: // 핸디캡 [1 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 65: // 핸디캡 [2 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 66: // 핸디캡 [3 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 67: // 핸디캡 [4 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 69: // 연장전 발생 live_results_p1 = 1 발생 live_results_p1 = 0 발생안함

                if ("Yes" == $value['bet_name'] && 1 == $value['live_results_p1'] ||
                        "No" == $value['bet_name'] && 0 == $value['live_results_p1']) {
                    $bet_status = 2;
                    break;
                }

                break;
            // 최고득점 피리어드
            case 70 :  // 0 : All Periods The Same,1 : 1st,2 : 2nd,3 : 3rd,4 : 4th
                if ("All Periods The Same" == $value['bet_name'] && 0 != $value['live_results_p1'] ||
                        ("1st Period" == $value['bet_name'] && 1 != $value['live_results_p1']) ||
                        ("2nd Period" == $value['bet_name'] && 2 != $value['live_results_p1']) ||
                        ("3rd Period" == $value['bet_name'] && 3 != $value['live_results_p1']) ||
                        ("4th Period" == $value['bet_name'] && 4 != $value['live_results_p1'])) {
                    break;
                }

                $bet_status = 2;

                break;
            // 최고득점 하프
            case 71 :  // 0 : All Periods The Same,1 : 1st,2 : 2nd
                if ("All Periods The Same" == $value['bet_name'] && 0 != $value['live_results_p1'] ||
                        (BASKETBALL == $value['fixture_sport_id'] && "1st Half" == $value['bet_name'] && 1 != $value['live_results_p1'] ) ||
                        (BASKETBALL == $value['fixture_sport_id'] && "2nd Half" == $value['bet_name'] && 2 != $value['live_results_p1']) ||
                        (SOCCER == $value['fixture_sport_id'] && "1st Half" == $value['bet_name'] && 1 != $value['live_results_p1']) ||
                        (SOCCER == $value['fixture_sport_id'] && "2nd Half" == $value['bet_name'] && 2 != $value['live_results_p1'])) {
                    break;
                }

                $bet_status = 2;

                break;
            case 77: // 언더오버 [전반전]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 95: // 코너 - 핸디캡
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            // 홈팀 무득점
            case 98: {
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
                    if ("Yes" == $value['bet_name'] && 0 < $value['live_results_p2']) {
                        break;
                    } else if ("No" == $value['bet_name'] && 0 == $value['live_results_p2']) {
                        break;
                    }
                    $bet_status = 2;
                }
                break;
            case 101: // 홈팀 오버언더
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;
            case 102: // 원정팀 오버언더
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                //CommonUtil::logWrite("result : bet_status : " . $bet_status . " live_results_p2 :" . $value['live_results_p2'] . " ls_markets_base_line : " . $value['ls_markets_base_line'] . " bet_name :" . $value['bet_name'], "error");
                break;
            case 113 : // 양팀 모두득점 [전반전]
                if (($value['live_results_p1'] > 0 && $value['live_results_p2'] > 0 && 'Yes' == $value['bet_name']) ||
                        ($value['live_results_p1'] <= 0 || $value['live_results_p2'] <= 0 && 'No' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 153: // 언더오버 [1 피리어드] <홈팀>
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;
            case 155: // 언더오버 [1 피리어드] <원정팀>
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                break;
            case 202: // 1이닝 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 203: // 2세트 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 204: // 3세트 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 205: // 4세트 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 206: // 5세트 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 211 : // 양팀 모두득점 [후반전]

                if (($value['live_results_p1'] > 0 && $value['live_results_p2'] > 0 && 'Yes' == $value['bet_name']) ||
                        ($value['live_results_p1'] <= 0 || $value['live_results_p2'] <= 0 && 'No' == $value['bet_name'])
                ) {
                    $bet_status = 2;
                }
                break;
            case 220 : // 원정팀 오버언더 연장포함
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p2']);
                break;
            case 221 : // 홈팀 오버언더 연장포함
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1']);
                break;
            case 226 : // 승패 연장포함
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 235: // 5이닝 승무패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus1X2($value);
                break;
            case 236: // 언더오버 [5 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 281: // 핸디캡 [5 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 282 : // 전반전 승무패
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 322: // 언더/동점/오버
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverExactlyUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 342 : // 핸디캡 연장포함
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 348 : // 6th Period Winner 승무패
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 349 : // 7th Period Winner
                $bet_status = GameUtil::getBetStatus1X2($value);
                break;
            case 352: // 언더오버 [6 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 353: // 언더오버 [7 피리어드]
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 390 : // 전반전 + 풀타임 [연장전 포함]
                $arr_bet_name = explode("/", $value['bet_name']);
                if (1 == $arr_bet_name[0] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        2 == $arr_bet_name[0] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[0] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $value['live_results_p1'] = $value['result_2_p1'];
                $value['live_results_p2'] = $value['result_2_p2'];

                if (1 == $arr_bet_name[1] && $value['live_results_p1'] <= $value['live_results_p2'] ||
                        2 == $arr_bet_name[1] && $value['live_results_p1'] >= $value['live_results_p2'] ||
                        'X' == $arr_bet_name[1] && $value['live_results_p1'] != $value['live_results_p2']) {
                    break;
                }

                $bet_status = 2;
                break;
            case 427: // 승무패 및 언더오버
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
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 472 : // 승부치기 여부 // 홈팀 점수가 1이면 승부치기가 있는거다 0 이면 없는거다.
                if ('Yes' == $value['bet_name'] && 1 == $value['live_results_p1']) {
                    $bet_status = 2;
                } else if ('No' == $value['bet_name'] && 0 == $value['live_results_p1']) {
                    $bet_status = 2;
                }
                break;

            case 525 : //1~7이닝 오버언더
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 669: // 1세트 첫용  홈팀 점수가 1이면 첫용 있는거다 1 : 0 홈팀 출현 0 : 1 원정팀 출현
                if (1 == $value['bet_name'] && 1 == $value['live_results_p1'] && 0 == $value['live_results_p2']) {
                    $bet_status = 2;
                } else if (2 == $value['bet_name'] && 0 == $value['live_results_p1'] && 1 == $value['live_results_p2']) {
                    $bet_status = 2;
                }
                break;

            case 866: // Asian Handicap Sets
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 1170: // 1세트 첫킬   1 : 0 홈팀 첫스킬 0 : 1 원정팀 첫스킬
                if (1 == $value['bet_name'] && 1 == $value['live_results_p1'] && 0 == $value['live_results_p2']) {
                    $bet_status = 2;
                } else if (2 == $value['bet_name'] && 0 == $value['live_results_p1'] && 1 == $value['live_results_p2']) {
                    $bet_status = 2;
                }
                break;
            case 1327: // 첫 2점슛 홈팀 득점 1 : 0 원정팀 득점 0 : 1
                if (1 == $value['live_results_p1'] && 1 == $value['bet_name']) {
                    $bet_status = 2;
                } else if (1 == $value['live_results_p2'] && 2 == $value['bet_name']) {
                    $bet_status = 2;
                }
                break;
            case 1328: // 첫 3점슛
                if (1 == $value['live_results_p1'] && 1 == $value['bet_name']) {
                    $bet_status = 2;
                } else if (1 == $value['live_results_p2'] && 2 == $value['bet_name']) {
                    $bet_status = 2;
                }
                break;
            case 1537: // 홈팀승 + 오버언더 Yes/Over,No/Over,Yes/Under,No/Under

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
                    if ($value['live_results_p1'] < $value['live_results_p2'] && $value['ls_markets_base_line'] < ($value['live_results_p1'] + $value['live_results_p2'])) {
                        $bet_status = 2;
                    }
                } else if ('No' == $arr_bet_name[0] && 'Under' == $arr_bet_name[1]) {
                    if ($value['live_results_p1'] < $value['live_results_p2'] && ($value['live_results_p1'] + $value['live_results_p2']) < $value['ls_markets_base_line']) {
                        $bet_status = 2;
                    }
                }

                break;
            case 1538: // 원정팀승 + 오버언더 Yes/Over,No/Over,Yes/Under,No/Under
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
                    if ($value['live_results_p2'] < $value['live_results_p1'] && $value['ls_markets_base_line'] < ($value['live_results_p1'] + $value['live_results_p2'])) {
                        $bet_status = 2;
                    }
                } else if ('No' == $arr_bet_name[0] && 'Under' == $arr_bet_name[1]) {
                    if ($value['live_results_p2'] < $value['live_results_p1'] && ($value['live_results_p1'] + $value['live_results_p2']) < $value['ls_markets_base_line']) {
                        $bet_status = 2;
                    }
                }
                break;
            case 1558: // Asian Handicap Points
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusHandicap($value);
                break;
            case 1618: // 5이닝 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;
            case 1677: // 마지막 득점팀
                if (1 == $value['live_results_p1'] && 1 == $value['bet_name']) {
                    $bet_status = 2;
                } else if (1 == $value['live_results_p2'] && 2 == $value['bet_name']) {
                    $bet_status = 2;
                }
                break;
            case 1832 : //3점슛 언/오버
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatusOverUnder($value, $value['live_results_p1'] + $value['live_results_p2']);
                break;
            case 2402: // 첫 홈런 홈팀 승 1 : 0, 원정팀 승이면 0 : 1
                if (1 == $value['live_results_p1'] && 1 == $value['bet_name']) {
                    $bet_status = 2;
                } else if (1 == $value['live_results_p2'] && 2 == $value['bet_name']) {
                    $bet_status = 2;
                }
                break;
            case 2681 : // 1~7이닝 승패
                list($bet_status, $value['bet_price']) = GameUtil::getBetStatus12($value);
                break;

            default:
                break;
        }
        return array($bet_status, $value['bet_price'], $value['live_results_p1'], $value['live_results_p2'], '');
    }

    // 미니게임 적중판단
    public static function getMiniBetStatus($value, $data_object) {
        if (null == $data_object) {
            return array(1, $value['bet_price']);
        }

        if (is_array($data_object) == 1) {
            $data_object = (object) $data_object;
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
                case 14004;
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
                return array(1, $value['bet_price']);
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

?>
