<?php


/** @noinspection ALL */
include_once(_LIBPATH . '/class_GameStatusUtil.php');
include_once(_BASEPATH . '/GamblePatch/GambelGmPt.php');
include_once(_BASEPATH . '/GamblePatch/KwinGmPt.php');
include_once(_BASEPATH . '/GamblePatch/ChoSunGmPt.php');
include_once(_BASEPATH . '/GamblePatch/BetsGmPt.php');
include_once(_BASEPATH . '/GamblePatch/NobleGmPt.php');
include_once(_BASEPATH . '/GamblePatch/BullsGmPt.php');
include_once(_LIBPATH . '/class_CommonStatsQuery.php');
include_once(_LIBPATH . '/class_UserPayBack.php');
class GameCode {

    // 적특시 결과가 나온 (bet_status 3) 인 유저의 상태 1,당첨금은 롤백을 해준다.
    // 낙첨은 상태만 롤백 1로
    // 취소는 해당로직에서 제외
    public static function doRollbackCalculate($ALBetDAO, $UTIL, $re_value, $status) {
        CommonUtil::logWrite("doRollbackCalculate : " . json_encode($re_value), "info");

        if (6 == $status) {
            $p_data['sql'] = "update member_bet_detail set bet_status = $status,passivity_hit_flag = 1,result_score = null where idx = " . $re_value['idx'];
        } else {
            $p_data['sql'] = "update member_bet_detail set bet_status = $status,result_score = null where idx = " . $re_value['idx'];
        }

        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $p_data['sql'] = "select bet_status,take_money,take_point,recom_take_point from member_bet where idx = " . $re_value['bet_idx'];
        $arrbet = $ALBetDAO->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $arrbet) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        if (1 == $arrbet[0]['bet_status'])
            return;

        $p_data['sql'] = "update member_bet set bet_status = 1,take_money = 0,take_point = 0,recom_take_point = 0 where idx = " . $re_value['bet_idx'];
        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $take_money = $arrbet[0]['take_money'];
        $take_point = $arrbet[0]['take_point'];
        $admin_id = $re_value['admin_id'];
        $recom_take_point = $arrbet[0]['recom_take_point']; //$re_value['recom_take_point'];
        // 당첨금이 있으면 롤백한다.
        $p_data['sql'] = "update member set money = money - $take_money,point = point - $take_point where idx = " . $re_value['member_idx'];
        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
       
              
        $recom_member_idx = $re_value['recommend_member'];
        if ($recom_take_point > 0 && $recom_member_idx > 0) {
            $p_data['sql'] = "select * from member where idx = $recom_member_idx";
            $result_recom_member_data = $ALBetDAO->select_query($p_data);

            if (FAIL_DB_SQL_EXCEPTION === $result_recom_member_data) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
            $now_recom_point = $result_recom_member_data[0]['point'];

            $p_data['sql'] = "update member set point = point - $recom_take_point where idx = " . $recom_member_idx;
            //$ALBetDAO->setQueryData($p_data);

            if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }

            $ukey = md5($recom_member_idx . strtotime('now'));
            // 201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소
            $a_comment = '';
            if (1 == $re_value['bet_type']) {
                $a_comment = 'prematch ==>';
            } else if (2 == $re_value['bet_type']) {
                $a_comment = 'inplay ==>';
            }

            $a_comment .= "수동 정산 추천인 포인트 취소 ";
            $UTIL->log_point($ALBetDAO, $ukey, $recom_member_idx, 203, $re_value['bet_idx'], -$recom_take_point, $now_recom_point, $admin_id, $a_comment, 'P');
        }


        if (0 < $take_money) {

            $ukey = md5($re_value['member_idx'] . strtotime('now'));
            // 201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소
            $a_comment = '';
            if (1 == $re_value['bet_type']) {
                $a_comment = 'prematch ==>';
            } else if (2 == $re_value['bet_type']) {
                $a_comment = 'inplay ==>';
            }

            $a_comment .= "수동 정산취소 ";

            $UTIL->log_cash($ALBetDAO, $ukey, $re_value['member_idx'], 201, $re_value['bet_idx'], -$take_money, $re_value['money'], $admin_id, $a_comment, 'P');
        }

        if (0 < $take_point) {
            $ukey = md5($re_value['member_idx'] . strtotime('now'));
            // 201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소
            $a_comment = '';
            if (1 == $re_value['bet_type']) {
                $a_comment = 'prematch ==>';
            } else if (2 == $re_value['bet_type']) {
                $a_comment = 'inplay ==>';
            }

            $a_comment .= "수동 정산 포인트 취소 ";
            $UTIL->log_point($ALBetDAO, $ukey, $re_value['member_idx'], 202, $re_value['bet_idx'], -$take_point, $re_value['point'], $admin_id, $a_comment, 'P');
        }
    }

    // 수동정산시 정산처리 루틴
    public static function doReCalculate($ALBetDAO, $UTIL, $value, $bet_type) {

        $gmPt = null;
        if ('KWIN' == SERVER) {
            $gmPt = new KwinGmPt();
        } else if ('GAMBLE' == SERVER) {
            $gmPt = new GambelGmPt();
        } else if ('CHOSUN' == SERVER) {
            $gmPt = new ChoSunGmPt();
        } else if ('BETS' == SERVER) {
            $gmPt = new BetsGmPt();
        } else if ('NOBLE' == SERVER) {
            $gmPt = new NobleGmPt();
        }else if ('BULLS' == SERVER) {
            $gmPt = new BullsGmPt();
        } else {
            throw new Exception('fail GamblePatch !!!');
        }

        $arr_config = $gmPt->getConfigData($ALBetDAO);

        $arrMbBtDtResult = $ALBetDAO->SelectMemberBetDetail($value['bet_idx']); // 1인값이 있는지 체크한다.
        // member_bet 결과 처리를 해준다(2-성공, 4-실패)
        if (FAIL_DB_SQL_EXCEPTION === $arrMbBtDtResult) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        if (false == isset($arrMbBtDtResult) || true == empty($arrMbBtDtResult)) {
            return [false, $value['total_bet_money'], 0];
        }


        $bet_total_count = count($arrMbBtDtResult); // 다폴더일경우  총 배팅 개수


        list($retval, $total_bet_price, $win_limit_price_count, $win_count, $lose_count) = $gmPt->checkGameResult($arrMbBtDtResult, $arr_config, $bet_total_count);

        $member_idx = $value['member_idx'];
        $sql = "select money,point from member where idx = $member_idx";

        $member_data = $ALBetDAO->select_query($sql);
        if (FAIL_DB_SQL_EXCEPTION === $member_data) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        $value['money'] = $member_data[0]['money'];

        $a_comment = '';
        if (1 == $bet_type) {
            $a_comment = 'prematch ==>';
        } else if (2 == $bet_type) {
            $a_comment = 'inplay ==>';
        }
        $ukey = md5($member_idx . strtotime('now'));
        $admin_id = $value['admin_id'];

        if (0 < $lose_count) { // 낙첨시 주는 포인트 lose_self_per
            list($retval, $ret_total_bet_money, $ret_take_money) = $gmPt->doLose($value, $arrMbBtDtResult, $ukey, $bet_total_count, $win_count, $lose_count, $a_comment, $bet_type
                    , $ALBetDAO, $UTIL);
            return [true, $ret_total_bet_money, $ret_take_money];
        }

        if (false == $retval) {
            return [false, $value['total_bet_money'], 0];
        }

        $take_money = 0;
        $bonus_price = $value['bonus_price'];
        $folder_type = $value['folder_type'];

        list($total_bet_price, $bonus_price) = $gmPt->calBonusPrice($total_bet_price, $bonus_price, $folder_type, $win_limit_price_count, $arr_config);

        $gm_bonus = $gmPt->useItemAllocation($ALBetDAO, $ukey, $member_idx, $value['mb_bt_idx'], $value['item_idx'], $total_bet_price);
        $total_bet_price = $total_bet_price + $gm_bonus;

        //$take_money = $total_bet_price * $value['total_bet_money'];
        $take_money = sprintf('%0.2f', $total_bet_price * $value['total_bet_money']);
        // member 의 머니 업데이트를 해줘야 한다.
        if ($take_money > 0) {
            $p_data['sql'] = "update member set money = money + $take_money where idx = " . $member_idx;

            if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
        }

        if ('M' == $value['flag_bet_sum']) {
            $gmPt->addBetSum($arrMbBtDtResult, $member_idx, $bet_type, $value['total_bet_money'], $ALBetDAO);
        }

        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateMemberBetBonus($value['calculate_dt'], $value['mb_bt_idx'], 3, $take_money, $bonus_price, 'P', $gm_bonus)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        CommonUtil::logWrite("doReCalculate win end bet_idx : " . $value['bet_idx'], 'info');
        if (0 < $gm_bonus) {
            $a_comment .= " 배당 패치 수동 적중";
            $a_comment = addslashes($a_comment);
            $UTIL->log_cash($ALBetDAO, $ukey, $member_idx, AC_GM_ALLOCATION_MONEY, $value['mb_bt_idx'], $take_money, $value['money'], $admin_id, $a_comment, 'P');
        } else {
            $a_comment .= " 수동 적중";
            $a_comment = addslashes($a_comment);
            $UTIL->log_cash($ALBetDAO, $ukey, $member_idx, 7, $value['mb_bt_idx'], $take_money, $value['money'], $admin_id, $a_comment, 'P');
        }

        
         
        return [true, $value['total_bet_money'], $take_money];
    }

    // 미니게임 롤백
    public static function doRollbackMiniGame($AdminMiniGameDAO, $UTIL, $re_value, $admin_id) {
        //CommonUtil::logWrite("doRollbackCalculate : " . json_encode($re_value), "info");

        $p_data['sql'] = "update mini_game_member_bet set bet_status = 1, take_money = 0, take_point = 0 where idx = " . $re_value['idx'];
        if (FAIL_DB_SQL_EXCEPTION === $AdminMiniGameDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $take_money = $re_value['take_money'];
        $take_point = $re_value['take_point'];
        $p_data['sql'] = "update member set money = money - $take_money,point = point - $take_point where idx = " . $re_value['member_idx'];

        if (FAIL_DB_SQL_EXCEPTION === $AdminMiniGameDAO->setQueryData($p_data)) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }


        if (0 < $take_money) {
            $ukey = md5($re_value['member_idx'] . strtotime('now'));
            // 201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소
            $a_comment = "수동 정산취소 ";
            $UTIL->log_cash($AdminMiniGameDAO, $ukey, $re_value['member_idx'], 201, $re_value['idx'], -$take_money, $re_value['money'], $admin_id, $a_comment, 'P');
        }

        if (0 < $take_point) {
            $ukey = md5($re_value['member_idx'] . strtotime('now'));
            // 201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소
            $a_comment = "수동 정산 포인트 취소 ";
            $UTIL->log_point($AdminMiniGameDAO, $ukey, $re_value['member_idx'], 202, $re_value['idx'], -$take_point, $re_value['point'], $admin_id, $a_comment, 'P');
        }
    }

    public static function doReCalculateAndAllHit($arrReResult, $ALBetDAO, $UTIL, $bet_type, $admin_id) {
        $checkBetCalculate = array();  // mb_bt_idx
        if (true == isset($arrReResult) && false == empty($arrReResult)) {
            foreach ($arrReResult as $value) {

                if (in_array($value['mb_bt_idx'], $checkBetCalculate))
                    continue;

                $value['admin_id'] = $admin_id;
                list($return_value, $total_bet_money, $take_money) = GameCode::doReCalculate($ALBetDAO, $UTIL, $value, $bet_type);
                if (false == $return_value)
                    continue;

                $checkBetCalculate[] = $value['mb_bt_idx'];

                // 이전은 정산되어있으나 적특 처리되면 총판 롤링금액에서 제외 된다.
                if (3 == $value['bet_status'] && $value['take_money'] != $value['total_bet_money'] && $total_bet_money == $take_money) {
                    GameCode::doReCalculateDistributor($ALBetDAO, $value, 'DEC');
                }
            }
        }
        $checkBetCalculate = [];
        if (true == isset($arrReResult) && false == empty($arrReResult)) {
            foreach ($arrReResult as $value) {
                if (in_array($value['mb_bt_idx'], $checkBetCalculate))
                    continue;
                $checkBetCalculate[] = $value['mb_bt_idx'];

                $arrMbBtDtResult = $ALBetDAO->SelectMemberBetDetail($value['mb_bt_idx']); // 1인값이 있는지 체크한다.
                if (FAIL_DB_SQL_EXCEPTION === $arrMbBtDtResult) {
                    throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                }

                $is_find = false;
                if (true == isset($arrMbBtDtResult) && false == empty($arrMbBtDtResult)) {
                    foreach ($arrMbBtDtResult as $detail) {
                        if (0 == $detail['passivity_hit_flag']) {
                            $is_find = true;
                        }
                    }
                }

                if (false == $is_find) {
                    //
                    $p_data['sql'] = "update member_bet set passivity_hit_flag = 1 where idx = " . $value['mb_bt_idx'];
                    //$ALBetDAO->setQueryData($p_data);
                    if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->setQueryData($p_data)) {
                        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
                    }
                }
            }
        }
    }

    public static function display_bet_name($is_passivity, $idx, $item, $value, $fixture_id, $fixture_start_date, $count) {
        $color = '#ffffff';
        $display_bet_name = GameStatusUtil::betNameToDisplay_new($value['bet_name'], $item['markets_id']);
        $display_total_bet_money = true == isset($value['bet_id_total_bet_money']) ? $value['bet_id_total_bet_money'] : 0;
        $total_bet_money = true == isset($value['total_bet_money']) ? $value['total_bet_money'] : 0;
        if (0 < $total_bet_money && 0 == $display_total_bet_money) {
            $color = '#f9bfbf';
        }



        $count += 1;

        if (isset($value['bet_price_hit'])) {
            $betPrice = $value['bet_price'];
            if ($value['bet_price_hit'] > 0) {
                $color = '#ffefa5';
                $betPrice = $value['bet_price_hit'];

                //CommonUtil::logWrite("[display_bet_name] value bet_price : ". json_encode($value), "error");
            }
        } else {
            $betPrice = 0;
        }

        //CommonUtil::logWrite("[display_bet_name] value : ". json_encode($value), "error");
        $base_url = '';
        $popupUrl = '';
        if (1 == $item['bet_type']) {
            $base_url = "/sports_w/prematch_betting_list.php?";
        } else {
            $base_url = "/sports_w/realtime_betting_list.php?";
        }

        $popupUrl .= $base_url . "fixture_id=" . $fixture_id . "&markets_id=" . $item['markets_id'] . "&base_line=" . $item['bet_base_line'] . "&bet_name=" . $value['bet_name'];

        //CommonUtil::logWrite("[display_bet_name] popupUrl : ". $popupUrl, "error");

        if (!empty($value['bet_id_total_bet_money'])) {
            $display_bet_style = "font-weight: bold; color: #3B8BFF; background-color:" . $color;
        } else {
            $display_bet_style = "color: #000000; background-color:" . $color;
        }



        if (1 == $value['bet_status']) {
            $price_style = "color:black; ";
        } else if (2 == $value['bet_status']) {
            $price_style = "text-decoration:line-through; color:#808080; ";
        } else {

            $price_style = "text-decoration:line-through; color:red ";
        }
        /* bgcolor = "<?= $color ?>" */

        if ('ON' == $item['passivity_flag']) {
            ?>
            <td id="td_bg_<?= $idx ?>" name ="td_bg_<?= $idx ?>"  style = "<?= $display_bet_style ?>" >
            <?php } else { ?>
            <td id="td_bg_<?= $idx ?>" name ="td_bg_<?= $idx ?>"  style = "<?= $display_bet_style ?>" onClick="onBtnClickCalculate(<?= $idx ?>, '$fixture_start_date',<?= $fixture_id ?>, 1,<?= $item['markets_id'] ?>, '<?= $item['bet_base_line'] ?>', '<?= $item['admin_bet_status'] ?>')">
                <?php
            }
            if (0 < $total_bet_money) {
                ?>
                <a href="javascript:;" style="display: inline;" onClick="javascript:event.stopPropagation();popupWinPost('<?= $popupUrl ?>', 'userbetinfo', 800, 1400, 'userbetinfo', 0);">
                <?php } ?>
                <h6 class="ml10 fl" ><?= $display_bet_name ?></h6>총배팅금액(<?= number_format($total_bet_money) ?>)/남은 금액(<?= number_format($display_total_bet_money) ?>)</a>

            <!-- 배당 변경 추가 -->

            <div class="search_form fr">
                <?php if ('ON' == $is_passivity) { ?>
                    상태
                    <div class="ml10 mr10">
                        <select name="select_bg_<?= $idx . $value['bet_name'] ?>" id="select_bg_<?= $idx . $value['bet_name'] ?>">
                            <option value="1" <?php if ($value['bet_status'] == 1): ?> selected<?php endif; ?>>배팅가능</option>
                            <option value="2" <?php if ($value['bet_status'] == 2): ?> selected<?php endif; ?>>배팅닫힘</option>
                            <option value="3" <?php if ($value['bet_status'] == 3): ?> selected<?php endif; ?>>배팅종료</option>
                        </select>
                    </div>
                <?php } ?>
                <h6 class="ml10" style = "<?= $price_style ?>">(배당 : <?= $betPrice ?>)</h6> <?php if ('ON' == $is_passivity) { ?> <input type="text"  id="in_bg_<?= $idx . $value['bet_name'] ?>" name ="in_bg_<?= $idx . $value['bet_name'] ?>"  style="width: 70px" placeholder="변경값">
                <?php } ?></div>

            <!-- 배당 변경 추가 end -->
        </td>

        <?php
        if ($count >= 3) {
            ?>
            </tr><tr>
                <?php
                $count = 0;
            }


            return $count;
        }

        public static function strColorRet($p_val = 0, $m_val = 0, $color = 0) {
            $ret_val = $p_val - $m_val;

            if ($ret_val > 0) {
                if ($color == 1) {
                    $ret_val = "<font color='blue'>" . number_format($ret_val) . "</font>";
                } else {
                    $ret_val = "<font>" . number_format($ret_val) . "</font>";
                }
            } elseif ($ret_val == 0) {
                $ret_val = 0;
            } else {
                if ($color == 1) {
                    $ret_val = "<font color='red'>" . number_format($ret_val) . "</font>";
                } else {
                    $ret_val = "<font>" . number_format($ret_val) . "</font>";
                }
            }

            return $ret_val;
        }

        public static function decUpdateChargeBetMoney($date, $member_idx, $type, $bet_money, $model) {
            $p_data['sql'] = "select idx from member_money_charge_history where member_idx = $member_idx and status = 3 and update_dt <= '$date' order by update_dt desc limit 1";
            $result = $model->getQueryData($p_data);
            //$result = $model->setQueryData($sql);

            if (FAIL_DB_SQL_EXCEPTION === $result) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }

            if (false === isset($result) || true === empty($result))
                return;

            $idx = $result[0]['idx'];
            switch ($type) {
                case 'SPORTS_S':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET sports_bet_s_money = sports_bet_s_money - $bet_money WHERE idx = $idx";
                    break;
                case 'SPORTS_D':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET sports_bet_d_money = sports_bet_d_money - $bet_money WHERE idx = $idx";
                    break;
                case 'REAL_S':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET real_bet_s_money = real_bet_s_money - $bet_money WHERE idx = $idx";
                    break;
                case 'REAL_D':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET real_bet_d_money = real_bet_d_money - $bet_money WHERE idx = $idx";
                    break;
                case 'MINI':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET mini_bet_money = mini_bet_money - $bet_money WHERE idx = $idx";
                    break;
                case 'CASINO':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET casino_bet_money = casino_bet_money - $bet_money WHERE idx = $idx";
                    break;
                case 'SLOT':
                    $p_data['sql'] = "UPDATE member_money_charge_history SET slot_bet_money = slot_bet_money - $bet_money WHERE idx = $idx";
                    break;

                default:
                    CommonUtil::logWrite("decUpdateChargeBetMoney member_idx :" . $member_idx . ' type =>' . $type, 'error');
                    return;
            }

            if (FAIL_DB_SQL_EXCEPTION === $model->setQueryData($p_data)) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }

            //CommonUtil::logWrite("success decUpdateChargeBetMoney member_idx :" . $member_idx . ' type =>' . $type . ' sql =>' . $p_data['sql'], 'error');
        }

        public static function doReCalculateDistributor($ALBetDAO, $value, $type) { // $type DEC 차감 ,ADD 증가
            //CommonUtil::logWrite("doReCalculateDistributor start data :" . json_encode($value), 'info');
            $calculate_dt = date("Y-m-d", strtotime($value['calculate_dt'])) . ' 00:00:00';
            $today = date("Y-m-d") . ' 00:00:00';

            //CommonUtil::logWrite("doReCalculateDistributor today <= calculate_dt  : " . $today, 'info');
            //CommonUtil::logWrite("doReCalculateDistributor today <= calculate_dt  : " . $calculate_dt, 'info');

            if ($today <= $calculate_dt) {
                //CommonUtil::logWrite("doReCalculateDistributor today <= calculate_dt  : " . $today, 'info');
                //CommonUtil::logWrite("doReCalculateDistributor today <= calculate_dt  : " . $calculate_dt, 'info');
                return;
            }

            $member_idx = $value['member_idx'];

            $p_data['sql'] = " SELECT pm.idx,pm.point FROM member as cm
                    LEFT JOIN member as pm ON pm.idx = cm.recommend_member
                    WHERE cm.idx = $member_idx";

            $result_pm_data = $ALBetDAO->getQueryData($p_data);
            if (FAIL_DB_SQL_EXCEPTION === $result_pm_data) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
            if (false === isset($result_pm_data) || 0 === count($result_pm_data)) {
                CommonUtil::logWrite("doReCalculateDistributor result_pm_data query :" . $p_data['sql'], 'info');
                return;
            }

            $parent_idx = $result_pm_data[0]['idx'];

            $p_data['sql'] = "SELECT * FROM shop_calculate_result where calculate_dt = '$calculate_dt' AND member_idx = $parent_idx";
            $result_data = $ALBetDAO->getQueryData($p_data);

            if (FAIL_DB_SQL_EXCEPTION === $result_data) {
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }

            if (false === isset($result_data) || 0 === count($result_data)) {
                //CommonUtil::logWrite("doReCalculateDistributor result_data query :" . $p_data['sql'], 'info');
                return;
            }

            $total_bet_money = $value['total_bet_money'];
            $point = 0;
            $ac_code = 0;
            $a_comment = "";
            $bet_idx = 0;
            $query_string = '';
            if (1 == $value['bet_type'] && 'S' == $value['folder_type'] && 0 < $result_data[0]['bet_pre_s_fee']) { // 프리매치 싱글
                $bet_idx = $value['bet_idx'];
                $point = $total_bet_money * ($result_data[0]['bet_pre_s_fee'] / 100);
                $a_comment = 'prematch S ==> ';
                $query_string = 'pre_bet_sum_s';
            } else if (1 == $value['bet_type'] && 'D' == $value['folder_type'] && 0 < $result_data[0]['bet_pre_d_fee']) { // 프리매치 멀티
                $bet_idx = $value['bet_idx'];
                $point = $value['total_bet_money'] * ($result_data[0]['bet_pre_d_fee'] / 100);
                $a_comment = 'prematch D ==> ';
                $query_string = 'pre_bet_sum_d';
            } else if (2 == $value['bet_type'] && 'S' == $value['folder_type'] && 0 < $result_data[0]['bet_real_s_fee']) { // 인플레이 싱글
                $bet_idx = $value['bet_idx'];
                $point = $value['total_bet_money'] * ($result_data[0]['bet_real_s_fee'] / 100);
                $a_comment = 'inplay S ==> ';
                $query_string = 'real_bet_sum_s';
            } else if (2 == $value['bet_type'] && 'D' == $value['folder_type'] && 0 < $result_data[0]['bet_real_d_fee']) { // 인플레이 멀티
                $bet_idx = $value['bet_idx'];
                $point = $value['total_bet_money'] * ($result_data[0]['bet_real_d_fee'] / 100);
                $a_comment = 'inplay D ==> ';
                $query_string = 'real_bet_sum_d';
            } else if (2 < $value['bet_type'] && $value['bet_type'] < 7 && 0 < $result_data[0]['bet_mini_fee']) { // 미니게임
                $bet_idx = $value['idx'];
                $point = $value['total_bet_money'] * ($result_data[0]['bet_mini_fee'] / 100);
                $a_comment = 'mini ==> ';
                $query_string = 'mini_bet_sum';
            } else {
                return;
            }

            if ('DEC' == $type) { // 롤링을 차감해야한다.
                $point = -$point;
                $total_bet_money = -$total_bet_money;
                $a_comment .= "재정산 포인트 취소 ";
                $ac_code = 126;                //  124:관리자 포인트 회수
            } else {
                $a_comment .= "재정산 포인트 충전 ";
                $ac_code = 127;                //  123:관리자 포인트 충전
            }

            /* $p_data['sql'] = " UPDATE shop_calculate_result  SET calculate_point = calculate_point + $point
              ,af_point = af_point + $point
              ,$query_string = $query_string + $total_bet_money
              where calculate_dt = $calculate_dt AND member_idx = $parent_idx";

              $ALBetDAO->setQueryData($p_data);
              CommonUtil::logWrite("doReCalculateDistributor shop_calculate_result query :" . $p_data['sql'] , 'info');

              $p_data['sql'] = " UPDATE member  SET point = point + $point
              where idx = $parent_idx";
              $ALBetDAO->setQueryData($p_data);
             */
            //CommonUtil::logWrite("doReCalculateDistributor member query :" . $p_data['sql'], 'info');
            // 로그 남기기
            $ukey = md5($parent_idx . strtotime('now'));

            $admin_id = $value['admin_id'];

            $UTIL = new CommonUtil();
            $UTIL->log_point($ALBetDAO, $ukey, $parent_idx, $ac_code, $bet_idx, $point, $result_pm_data[0]['point'], $admin_id, $a_comment, 'P');

            CommonUtil::logWrite("doReCalculateDistributor end :", 'info');
        }

        public static function getRecommandMemberIdx($dis_idx, $model) {
            $sql = "
            WITH RECURSIVE TEMP AS (
                SELECT member.idx,member.id,member.u_business,member.dis_id, 1 as lvl
                FROM member
                WHERE recommend_member = ? and u_business <> 1

                UNION ALL

                SELECT A.idx,A.id,A.u_business,A.dis_id,lvl + 1 lvl
                FROM member A
                INNER JOIN TEMP B ON A.recommend_member = B.idx WHERE A.recommend_member > 0 and A.u_business <> 1
            )
            SELECT idx FROM TEMP order by u_business asc";

            $result = $model->getQueryData_pre($sql, [$dis_idx]);
            $result = isset($result) ? $result : [];
            $param = array($dis_idx);
            $param_qm = array('?');

            foreach ($result as $dist) {
                array_push($param, $dist['idx']);
                array_push($param_qm, '?');
            }

            $str_param_qm = implode(',', $param_qm);
            return [$param, $str_param_qm];
        }

        public static function getRecommandMemberInfos($dis_idx, $model) {
            $sql = "
            WITH RECURSIVE TEMP AS (
                SELECT member.idx,member.id,member.nick_name,member.u_business,member.dis_id, 1 as lvl
                FROM member
                WHERE recommend_member = ? and u_business <> 1

                UNION ALL

                SELECT A.idx,A.id,A.nick_name,A.u_business,A.dis_id,lvl + 1 lvl
                FROM member A
                INNER JOIN TEMP B ON A.recommend_member = B.idx WHERE A.recommend_member > 0 and A.u_business <> 1
            )
            SELECT idx,id,nick_name FROM TEMP order by u_business asc";

            $result = $model->getQueryData_pre($sql, [$dis_idx]);
            $param = array($dis_idx);

            if(null != $result){
                foreach ($result as $dist) {
                    array_push($param, $dist['idx']);
                }
            }

            $str_param = implode(',', $param);
            $sql = "select idx,id,nick_name from member where idx in($str_param) ";
            $result = $model->getQueryData_pre($sql, []);
            return $result;
        }

        public static function setStatsDay($column, $str_dt, &$stats_day, $val) {
            if (true === isset($stats_day[$str_dt][$column])) {
                $stats_day[$str_dt][$column] += $val;
            } else {
                $stats_day[$str_dt][$column] = $val;
            }
        }

        // Parent Revenue Calculation
        public static function calBetType($ptdata, $chdata, &$shopConfig, $dis_idx, $value, $str_dt, &$stats_day, &$total_point_sub) {
            if (0 < $shopConfig[$dis_idx]['recommend_member'] && 0 < $shopConfig[$dis_idx][$ptdata] - $shopConfig[$dis_idx][$chdata]) {
                $pt_pre_data = $shopConfig[$dis_idx][$ptdata] - $shopConfig[$dis_idx][$chdata];
                $pt_profit_data = ($value) * ($pt_pre_data * 0.01);
                GameCode::setStatsDay('cal_point_sub', $str_dt, $stats_day, $pt_profit_data);
                $total_point_sub = $total_point_sub + $pt_profit_data;
                return true;
            }
            return false;
        }

        // Parent Low Revenue Calculation
        public static function calLowBetType($pt_fee_key, $ppt_fee_key, &$shopConfig, $dis_idx, $profit, $str_dt, &$stats_day, &$total_point_sub) {
            $recommend_member = $shopConfig[$dis_idx]['recommend_member'];
            if (0 < $recommend_member && 0 < $shopConfig[$recommend_member]['recommend_member']) {
                $pt_recommend_member = $shopConfig[$recommend_member]['recommend_member'];
                $ppt_pre_s_fee = $shopConfig[$pt_recommend_member][$ppt_fee_key];
                $pt_pre_s_fee = $shopConfig[$dis_idx][$pt_fee_key];
                $ppt_bet_pre_s_fee = $ppt_pre_s_fee - $pt_pre_s_fee;
                if (0 < $ppt_bet_pre_s_fee) {
                    $profit_ppt_bet_pre_s_fee = $profit * ($ppt_bet_pre_s_fee * 0.01);
                    GameCode::setStatsDay('cal_point_sub', $str_dt, $stats_day, $profit_ppt_bet_pre_s_fee);
                    $total_point_sub = $total_point_sub + $profit_ppt_bet_pre_s_fee;
                    //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc calLowBetType : " . json_encode($stats_day), "info");
                    //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc calLowBetType ppt_pre_s_fee : " . $ppt_pre_s_fee.' pt_pre_s_fee : '.$pt_pre_s_fee, "info");

                    return true;
                }
            }

            return false;
        }

        public static function doSumChExCalc(&$shopConfig, &$stats_day, $db_dataArr,
                &$total_point, &$total_point_sub, &$tot_ch_val, &$tot_ex_val, $select_dist_idx) {
            $convert_arr = [];
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['up_dt']);
                $dis_idx = $row['dis_idx'];
                if ($row['stype'] == 'ch') {
                    $convert_arr[$str_dt][$dis_idx]['ch_val'] = $row['s_money'];
                } elseif ($row['stype'] == 'ex') {
                    $convert_arr[$str_dt][$dis_idx]['ex_val'] = $row['s_money'];
                }
            }
            //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc convert_arr : " . json_encode($convert_arr), "info");
            foreach ($convert_arr as $key => $date) { // The key value in that loop is the date
                $str_dt = $key;
                foreach ($date as $key_idx => $row) { // The key value in this loop is the distributor's unique number
                    $dis_idx = $key_idx;

                    //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc convert_arr roof idx : " . $dis_idx, "info");

                    $ch_val = true === isset($row['ch_val']) ? $row['ch_val'] : 0;
                    $ex_val = true === isset($row['ex_val']) ? $row['ex_val'] : 0;
                    $tot_ch_val += $ch_val;
                    $tot_ex_val += $ex_val;

                    GameCode::setStatsDay('ch_val', $str_dt, $stats_day, $ch_val);

                    GameCode::setStatsDay('ex_val', $str_dt, $stats_day, $ex_val);
                    //if (true === isset($shopConfig[$dis_idx]['pre_s_fee']) && 0 < $shopConfig[$dis_idx]['pre_s_fee']) {

                        $profit_fee = ($ch_val - $ex_val) * ($shopConfig[$dis_idx]['pre_s_fee'] * 0.01);

                        // 날짜별 죽장(입출)
                        if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) { // Cumulative value when the selected distributor and the loop distributor are the same or the selected distributor is zero in the date-by-date status
                            GameCode::setStatsDay('pre_s_fee', $str_dt, $stats_day, $profit_fee);

                            GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $profit_fee);
                            $total_point = $total_point + $profit_fee;
                        }
                    //}
                    //if (true === isset($shopConfig[$dis_idx]['pt_pre_s_fee']) && 0 < $shopConfig[$dis_idx]['pt_pre_s_fee']) {
                        // Parent Revenue Calculation
                        $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                        if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                            if (true == GameCode::calBetType('pt_pre_s_fee', 'pre_s_fee', $shopConfig, $dis_idx, $ch_val - $ex_val, $str_dt, $stats_day, $total_point_sub)) {
                                CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc calBetType : " . json_encode($stats_day), "info");
                            }
                        }

                        $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                        if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                            if (true == GameCode::calLowBetType('pt_pre_s_fee', 'pre_s_fee', $shopConfig, $dis_idx, $ch_val - $ex_val, $str_dt, $stats_day, $total_point_sub)) {
                                CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc calLowBetType : " . json_encode($stats_day), "info");
                            }
                        }
                      //}
                }
                
            }
            
            //CommonUtil::logWrite("stats_day_list_new_tm doSumChExCalc roof end : " . json_encode($stats_day), "info");
        }

        

        public static function doSumSportsRealBetCalc($db_dataArr, &$stats_day, &$shopConfig
                , &$pre_bet_sum_s, &$pre_take_sum_s, &$pre_sum_s
                , &$pre_bet_sum_d, &$pre_take_sum_d, &$pre_sum_d
                , &$real_bet_sum_s, &$real_take_sum_s, &$real_sum_s
                , &$real_bet_sum_d, &$real_take_sum_d, &$real_sum_d
                , &$total_classic_bet_money, &$total_classic_win_money, &$total_classic_lose_money
                , &$total_point, &$total_point_sub, $select_dist_idx
        ) {

            foreach ($db_dataArr as $row) {

                $str_dt = str_replace('-', '', $row['cr_dt']);

                // prematch 1 forder
                GameCode::setStatsDay('pre_bet_sum_s', $str_dt, $stats_day, $row['pre_bet_sum_s']);
                $pre_bet_sum_s += $row['pre_bet_sum_s'];

                GameCode::setStatsDay('pre_take_sum_s', $str_dt, $stats_day, $row['pre_take_sum_s']);
                $pre_take_sum_s += $row['pre_take_sum_s'];

                GameCode::setStatsDay('pre_sum_s', $str_dt, $stats_day, $row['pre_sum_s']);
                $pre_sum_s += $row['pre_sum_s'];

                // prematch 2 forder
                GameCode::setStatsDay('pre_bet_sum_d', $str_dt, $stats_day, $row['pre_bet_sum_2']);
                $pre_bet_sum_d += $row['pre_bet_sum_2'];

                GameCode::setStatsDay('pre_take_sum_d', $str_dt, $stats_day, $row['pre_take_sum_2']);
                $pre_take_sum_d += $row['pre_take_sum_2'];

                GameCode::setStatsDay('pre_sum_d', $str_dt, $stats_day, $row['pre_sum_2']);
                $pre_sum_d += $row['pre_sum_2'];

                // prematch 3 forder
                GameCode::setStatsDay('pre_bet_sum_d', $str_dt, $stats_day, $row['pre_bet_sum_3']);
                $pre_bet_sum_d += $row['pre_bet_sum_3'];

                GameCode::setStatsDay('pre_take_sum_d', $str_dt, $stats_day, $row['pre_take_sum_3']);
                $pre_take_sum_d += $row['pre_take_sum_3'];

                GameCode::setStatsDay('pre_sum_d', $str_dt, $stats_day, $row['pre_sum_3']);
                $pre_sum_d += $row['pre_sum_3'];

                // prematch 4 forder
                GameCode::setStatsDay('pre_bet_sum_d', $str_dt, $stats_day, $row['pre_bet_sum_4']);
                $pre_bet_sum_d += $row['pre_bet_sum_4'];

                GameCode::setStatsDay('pre_take_sum_d', $str_dt, $stats_day, $row['pre_take_sum_4']);
                $pre_take_sum_d += $row['pre_take_sum_4'];

                GameCode::setStatsDay('pre_sum_d', $str_dt, $stats_day, $row['pre_sum_4']);
                $pre_sum_d += $row['pre_sum_4'];

                // prematch 5 forder more
                GameCode::setStatsDay('pre_bet_sum_d', $str_dt, $stats_day, $row['pre_bet_sum_5_more']);
                $pre_bet_sum_d += $row['pre_bet_sum_5_more'];

                GameCode::setStatsDay('pre_take_sum_d', $str_dt, $stats_day, $row['pre_take_sum_5_more']);
                $pre_take_sum_d += $row['pre_take_sum_5_more'];

                GameCode::setStatsDay('pre_sum_d', $str_dt, $stats_day, $row['pre_sum_5_more']);
                $pre_sum_d += $row['pre_sum_5_more'];

                // 실시간 싱글
                GameCode::setStatsDay('real_bet_sum_s', $str_dt, $stats_day, $row['real_bet_sum_s']);
                $real_bet_sum_s += $row['real_bet_sum_s'];

                GameCode::setStatsDay('real_take_sum_s', $str_dt, $stats_day, $row['real_take_sum_s']);
                $real_take_sum_s += $row['real_take_sum_s'];

                GameCode::setStatsDay('real_sum_s', $str_dt, $stats_day, $row['real_sum_s']);
                $real_sum_s += $row['real_sum_s'];

                // 실시간 멀티
                GameCode::setStatsDay('real_bet_sum_d', $str_dt, $stats_day, $row['real_bet_sum_d']);
                $real_bet_sum_d += $row['real_bet_sum_d'];

                GameCode::setStatsDay('real_take_sum_d', $str_dt, $stats_day, $row['real_take_sum_d']);
                $real_take_sum_d += $row['real_take_sum_d'];

                GameCode::setStatsDay('real_sum_d', $str_dt, $stats_day, $row['real_sum_d']);
                $real_sum_d += $row['real_sum_d'];

                // 클래식
                GameCode::setStatsDay('total_classic_bet_money', $str_dt, $stats_day, $row['total_classic_bet_money']);
                $total_classic_bet_money += $row['total_classic_bet_money'];

                GameCode::setStatsDay('total_classic_win_money', $str_dt, $stats_day, $row['total_classic_win_money']);
                $total_classic_win_money += $row['total_classic_win_money'];

                GameCode::setStatsDay('total_classic_lose_money', $str_dt, $stats_day, $row['total_classic_lose_money']);
                $total_classic_lose_money += $row['total_classic_lose_money'];

                $dis_idx = $row['dis_idx'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_pre_s_fee'] > 0) {
                    $value = $row['pre_bet_sum_s'] * ($shopConfig[$dis_idx]['bet_pre_s_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_pre_s_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_pre_s_fee', 'bet_pre_s_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_s'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_pre_s_fee', 'bet_pre_s_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_s'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}

                //if ($shopConfig[$dis_idx]['bet_pre_d_2_fee'] > 0) {
                    $value = $row['pre_bet_sum_2'] * ($shopConfig[$dis_idx]['bet_pre_d_2_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_pre_d_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_pre_d_2_fee', 'bet_pre_d_2_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_2'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_pre_d_2_fee', 'bet_pre_d_2_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_2'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}

                //if ($shopConfig[$dis_idx]['bet_pre_d_3_fee'] > 0) {
                    $value = $row['pre_bet_sum_3'] * ($shopConfig[$dis_idx]['bet_pre_d_3_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_pre_d_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_pre_d_3_fee', 'bet_pre_d_3_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_3'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_pre_d_3_fee', 'bet_pre_d_3_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_3'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}


                //if ($shopConfig[$dis_idx]['bet_pre_d_4_fee'] > 0) {
                    $value = $row['pre_bet_sum_4'] * ($shopConfig[$dis_idx]['bet_pre_d_4_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_pre_d_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_pre_d_4_fee', 'bet_pre_d_4_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_4'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_pre_d_4_fee', 'bet_pre_d_4_fee', $shopConfig, $dis_idx, $row['pre_bet_sum_4'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}

                //if ($shopConfig[$dis_idx]['bet_pre_d_5_more_fee'] > 0) {
                    $value = $row['pre_sum_5_more'] * ($shopConfig[$dis_idx]['bet_pre_d_5_more_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_pre_d_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_pre_d_5_more_fee', 'bet_pre_d_5_more_fee', $shopConfig, $dis_idx, $row['pre_sum_5_more'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_pre_d_5_more_fee', 'bet_pre_d_5_more_fee', $shopConfig, $dis_idx, $row['pre_sum_5_more'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}

                //if ($shopConfig[$dis_idx]['bet_real_s_fee'] > 0) {
                    $value = $row['real_bet_sum_s'] * ($shopConfig[$dis_idx]['bet_real_s_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_real_s_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_real_s_fee', 'bet_real_s_fee', $shopConfig, $dis_idx, $row['real_bet_sum_s'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_real_s_fee', 'bet_real_s_fee', $shopConfig, $dis_idx, $row['real_bet_sum_s'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}

                //if ($shopConfig[$dis_idx]['bet_real_d_fee'] > 0) {
                    $value = $row['real_bet_sum_d'] * ($shopConfig[$dis_idx]['bet_real_d_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_real_d_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }

                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_real_d_fee', 'bet_real_d_fee', $shopConfig, $dis_idx, $row['real_bet_sum_d'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_real_d_fee', 'bet_real_d_fee', $shopConfig, $dis_idx, $row['real_bet_sum_d'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            }
        }
     
        public static function doSumMiniBetCalc($db_dataArr, &$stats_day, &$shopConfig
                , &$mini_bet_sum_d, &$mini_take_sum_d, &$mini_sum_d, &$total_point, &$total_point_sub, $select_dist_idx) {
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $dis_idx = $row['dis_idx'];
                // 매장번호를 넣어준다.(전체일 경우 0으로 들어감)
                GameCode::setStatsDay('mini_bet_sum_d', $str_dt, $stats_day, $row['mini_bet_sum_d']);
                $mini_bet_sum_d += $row['mini_bet_sum_d'];
                GameCode::setStatsDay('mini_take_sum_d', $str_dt, $stats_day, $row['mini_take_sum_d']);
                $mini_take_sum_d += $row['mini_take_sum_d'];
                GameCode::setStatsDay('mini_sum_d', $str_dt, $stats_day, $row['mini_sum_d']);
                $mini_sum_d += $row['mini_sum_d'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_mini_fee'] > 0) {
                    $value = $row['mini_bet_sum_d'] * ($shopConfig[$dis_idx]['bet_mini_fee'] * 0.01);

                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_mini_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }
                //}
                //if($shopConfig[$dis_idx]['pt_bet_mini_fee'] > 0){
                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        CommonUtil::logWrite("stats_day_list_new_tm doSumMiniBetCalc calBetType : " . json_encode($stats_day), "info");
                        GameCode::calBetType('pt_bet_mini_fee', 'bet_mini_fee', $shopConfig, $dis_idx, $row['mini_bet_sum_d'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        CommonUtil::logWrite("stats_day_list_new_tm doSumMiniBetCalc calLowBetType : " . json_encode($stats_day), "info");
                        GameCode::calLowBetType('pt_bet_mini_fee', 'bet_mini_fee', $shopConfig, $dis_idx, $row['mini_bet_sum_d'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            
            }
        }
       

        public static function doSumCasinoBetCalc($db_casinoDataArr, &$stats_day, &$shopConfig
                , &$total_casino_bet_money, &$total_casino_win_money, &$total_casino_lose_money, &$total_point, &$total_point_sub, $select_dist_idx) {

            foreach ($db_casinoDataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $dis_idx = $row['dis_idx'];
                // 매장번호를 넣어준다.(전체일 경우 0으로 들어감)
                GameCode::setStatsDay('total_casino_bet_money', $str_dt, $stats_day, $row['total_bet_money']);

                $total_casino_bet_money += $row['total_bet_money'];

                GameCode::setStatsDay('total_casino_win_money', $str_dt, $stats_day, $row['total_win_money']);

                $total_casino_win_money += $row['total_win_money'];

                GameCode::setStatsDay('total_casino_lose_money', $str_dt, $stats_day, $row['total_lose_money']);

                $total_casino_lose_money += $row['total_lose_money'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_casino_fee'] > 0) {
                    $value = $row['total_bet_money'] * ($shopConfig[$dis_idx]['bet_casino_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_casino_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }
                //}
                //if (true === isset($shopConfig[$dis_idx]['pt_bet_casino_fee']) && 0 < $shopConfig[$dis_idx]['pt_bet_casino_fee']) {
                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_casino_fee', 'bet_casino_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_casino_fee', 'bet_casino_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            
            }
        }
      

        public static function doSumSlotBetCalc($db_slotDataArr, &$stats_day, &$shopConfig
                , &$total_slot_bet_money, &$total_slot_win_money, &$total_slot_lose_money, &$total_point, &$total_point_sub, $select_dist_idx) {

            foreach ($db_slotDataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $dis_idx = $row['dis_idx'];
                // 매장번호를 넣어준다.(전체일 경우 0으로 들어감)

                GameCode::setStatsDay('total_slot_bet_money', $str_dt, $stats_day, $row['total_bet_money']);

                $total_slot_bet_money += $row['total_bet_money'];

                GameCode::setStatsDay('total_slot_win_money', $str_dt, $stats_day, $row['total_win_money']);

                $total_slot_win_money += $row['total_win_money'];

                GameCode::setStatsDay('total_slot_lose_money', $str_dt, $stats_day, $row['total_lose_money']);

                $total_slot_lose_money += $row['total_lose_money'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_slot_fee'] > 0) {
                    $value = $row['total_bet_money'] * ($shopConfig[$dis_idx]['bet_slot_fee'] * 0.01);

                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_slot_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }
                //}
                //if (true === isset($shopConfig[$dis_idx]['pt_bet_slot_fee']) && 0 < $shopConfig[$dis_idx]['pt_bet_slot_fee']) {
                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_slot_fee', 'bet_slot_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_slot_fee', 'bet_slot_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            }
        }
      
        public static function doSumEsptBetCalc($db_esptDataArr, &$stats_day, &$shopConfig
                , &$total_espt_bet_money, &$total_espt_win_money, &$total_espt_lose_money, &$total_point, &$total_point_sub, $select_dist_idx) {
            foreach ($db_esptDataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $dis_idx = $row['dis_idx'];
                // 매장번호를 넣어준다.(전체일 경우 0으로 들어감)
                GameCode::setStatsDay('total_espt_bet_money', $str_dt, $stats_day, $row['total_bet_money']);

                $total_espt_bet_money += $row['total_bet_money'];

                GameCode::setStatsDay('total_espt_win_money', $str_dt, $stats_day, $row['total_win_money']);

                $total_espt_win_money += $row['total_win_money'];

                GameCode::setStatsDay('total_espt_lose_money', $str_dt, $stats_day, $row['total_lose_money']);

                $total_espt_lose_money += $row['total_lose_money'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_esports_fee'] > 0) {
                    $value = $row['total_bet_money'] * ($shopConfig[$dis_idx]['bet_esports_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_esports_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }
                //}
                //if (true === isset($shopConfig[$dis_idx]['pt_bet_esports_fee']) && 0 < $shopConfig[$dis_idx]['pt_bet_esports_fee']) {
                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_esports_fee', 'bet_esports_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_esports_fee', 'bet_esports_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            }
        }

        public static function doSumHashBetCalc($db_hashDataArr, &$stats_day, &$shopConfig
                , &$total_hash_bet_money, &$total_hash_win_money, &$total_hash_lose_money, &$total_point, &$total_point_sub, $select_dist_idx) {
            foreach ($db_hashDataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $dis_idx = $row['dis_idx'];
                // 매장번호를 넣어준다.(전체일 경우 0으로 들어감)
                GameCode::setStatsDay('total_hash_bet_money', $str_dt, $stats_day, $row['total_bet_money']);

                $total_hash_bet_money += $row['total_bet_money'];

                GameCode::setStatsDay('total_hash_win_money', $str_dt, $stats_day, $row['total_win_money']);

                $total_hash_win_money += $row['total_win_money'];

                GameCode::setStatsDay('total_hash_lose_money', $str_dt, $stats_day, $row['total_lose_money']);

                $total_hash_lose_money += $row['total_lose_money'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_hash_fee'] > 0) {
                    $value = $row['total_bet_money'] * ($shopConfig[$dis_idx]['bet_hash_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_hash_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }
                //}
                //if (true === isset($shopConfig[$dis_idx]['pt_bet_hash_fee']) && 0 < $shopConfig[$dis_idx]['pt_bet_hash_fee']) {
                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_hash_fee', 'bet_hash_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_hash_fee', 'bet_hash_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            }
        }
       

        public static function doSumHoldemBetCalc($db_holdemDataArr, &$stats_day, &$shopConfig
                , &$total_holdem_bet_money, &$total_holdem_win_money, &$total_holdem_lose_money, &$total_point, &$total_point_sub, $select_dist_idx) {
            foreach ($db_holdemDataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $dis_idx = $row['dis_idx'];
                // 매장번호를 넣어준다.(전체일 경우 0으로 들어감)
                GameCode::setStatsDay('total_holdem_bet_money', $str_dt, $stats_day, $row['total_bet_money']);

                $total_holdem_bet_money += $row['total_bet_money'];

                GameCode::setStatsDay('total_holdem_win_money', $str_dt, $stats_day, $row['total_win_money']);

                $total_holdem_win_money += $row['total_win_money'];

                GameCode::setStatsDay('total_holdem_lose_money', $str_dt, $stats_day, $row['total_lose_money']);

                $total_holdem_lose_money += $row['total_lose_money'];

                // 베팅롤링 정산
                //if ($shopConfig[$dis_idx]['bet_holdem_fee'] > 0) {
                    $value = $row['total_bet_money'] * ($shopConfig[$dis_idx]['bet_holdem_fee'] * 0.01);
                    if (0 == $select_dist_idx || $dis_idx == $select_dist_idx) {
                        GameCode::setStatsDay('bet_holdem_fee', $str_dt, $stats_day, $value);
                        // 포인트 적립
                        GameCode::setStatsDay('cal_point', $str_dt, $stats_day, $value);
                        $total_point = $total_point + $value;
                    }
                //}
                //if (true === isset($shopConfig[$dis_idx]['pt_bet_holdem_fee']) && 0 < $shopConfig[$dis_idx]['pt_bet_holdem_fee']) {
                    $recommend_member = true === isset($shopConfig[$dis_idx]['recommend_member']) ? $shopConfig[$dis_idx]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $recommend_member == $select_dist_idx) {
                        GameCode::calBetType('pt_bet_holdem_fee', 'bet_holdem_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }

                    $pt_recommend_member = true === isset($shopConfig[$recommend_member]['recommend_member']) ? $shopConfig[$recommend_member]['recommend_member'] : 0;
                    if (0 == $select_dist_idx || $pt_recommend_member == $select_dist_idx) {
                        GameCode::calLowBetType('pt_bet_holdem_fee', 'bet_holdem_fee', $shopConfig, $dis_idx, $row['total_bet_money'], $str_dt, $stats_day, $total_point_sub);
                    }
                //}
            }
        }

        public static function doChExUnitPriceCalc(&$tot_ch_val, &$tot_ex_val, &$tot_rate_buff, &$tot_calculate_rate) {
            if (($tot_ch_val > 0) && ($tot_ex_val > 0)) {
                $tot_rate_buff = (100 - (($tot_ex_val / $tot_ch_val) * 100));
                $tot_calculate_rate = sprintf('%0.2f', $tot_rate_buff); // 520 -> 520.00

                if ($tot_calculate_rate >= 0) {
                    $tot_calculate_rate = "<font color='blue'>$tot_calculate_rate %</font>";
                } else {
                    $tot_calculate_rate = "<font color='red'>$tot_calculate_rate %</font>";
                }
            } else if ($tot_ch_val == 0 && $tot_ex_val == 0) {
                $tot_calculate_rate = "<font>0</font>";
            } else {
                if (($tot_ex_val < 1) && ($tot_ch_val > 0)) {
                    $tot_calculate_rate = 100;
                    $tot_calculate_rate = "<font color='blue'>100 %</font>";
                } else if (($tot_ex_val > 0) && ($tot_ch_val < 1)) {
                    $tot_calculate_rate = "<font color='red'>-100 %</font>";
                } else {
                    $tot_calculate_rate = "<font color='blue'>$tot_calculate_rate %</font>";
                }
            }
        }

        public static function doBdQnJoinBetUserCount($db_dataArr, &$stats_day, &$tot_qna, &$tot_board, &$tot_member, &$tot_charge_cnt) {
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                if ($row['stype'] == 'qna') {
                    $stats_day[$str_dt]['qna'] = $row['cnt'];
                    $tot_qna += $row['cnt'];
                } else if ($row['stype'] == 'board') {
                    $stats_day[$str_dt]['board'] = $row['cnt'];
                    $tot_board += $row['cnt'];
                } else if ($row['stype'] == 'member') {
                    $stats_day[$str_dt]['member'] = $row['cnt'];
                    $tot_member += $row['cnt'];
                } else if ($row['stype'] == 'bet') {
                    $stats_day[$str_dt]['bet'] = $row['cnt'];
                } else if ($row['stype'] == 'charge_cnt') { // 객단가 용 충전건수b
                    $stats_day[$str_dt]['charge_cnt'] = $row['cnt'];
                    $tot_charge_cnt += $row['cnt'];
                }
            }
        }

        public static function doSingleBetUserCount($db_dataArr, &$stats_day, &$tot_s_bet) {
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $stats_day[$str_dt]['s_bet'] = $row['cnt'];
                $tot_s_bet += $row['cnt'];
            }
        }

        public static function doMultiBetUserCount($db_dataArr, &$stats_day, &$tot_d_bet) {
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $stats_day[$str_dt]['d_bet'] = $row['cnt'];
                $tot_d_bet += $row['cnt'];
            }
        }

        public static function doMiniGameBetUserCount($db_dataArr, &$stats_day, &$tot_m_bet) {
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['cr_dt']);
                $stats_day[$str_dt]['m_bet'] = $row['cnt'];
                $tot_m_bet += $row['cnt'];
            }
        }

        public static function doPointPMUserCount($db_dataArr, &$stats_day, &$tot_p_point, &$tot_m_point) {
            foreach ($db_dataArr as $row) {
                $str_dt = str_replace('-', '', $row['reg_dt']);
                if ($row['ac_code'] == '6' || $row['ac_code'] === '10') {
                    $stats_day[$str_dt]['p_point'] = $row['s_point'];
                    $tot_p_point += $row['s_point'];
                }
                if ($row['ac_code'] == '123' || $row['ac_code'] === '124') {
                    $stats_day[$str_dt]['m_point'] = $row['s_point'];
                    $tot_m_point += $row['s_point'];
                }
            }
        }

        public static function doStatsDay(&$stats_day, &$total_point) {
            foreach ($stats_day as $row) {
                $str_dt = str_replace('-', '', $row['ymd']);
                // 롤링
                $bet_pre_s_fee = true === isset($row['bet_pre_s_fee']) && false === empty($row['bet_pre_s_fee']) ? $row['bet_pre_s_fee'] : 0;
                $bet_pre_d_fee = true === isset($row['bet_pre_d_fee']) && false === empty($row['bet_pre_d_fee']) ? $row['bet_pre_d_fee'] : 0;
                $bet_real_s_fee = true === isset($row['bet_real_s_fee']) && false === empty($row['bet_real_s_fee']) ? $row['bet_real_s_fee'] : 0;
                $bet_real_d_fee = true === isset($row['bet_real_d_fee']) && false === empty($row['bet_real_d_fee']) ? $row['bet_real_d_fee'] : 0;
                $bet_mini_fee = true === isset($row['bet_mini_fee']) && false === empty($row['bet_mini_fee']) ? $row['bet_mini_fee'] : 0;

                // 카지노
                $bet_casino_fee = true === isset($row['bet_casino_fee']) && false === empty($row['bet_casino_fee']) ? $row['bet_casino_fee'] : 0;
                //슬롯
                $bet_slot_fee = true === isset($row['bet_slot_fee']) && false === empty($row['bet_slot_fee']) ? $row['bet_slot_fee'] : 0;

                // 이스포츠/키론/해시
                $bet_espt_fee = 0;
                if ('ON' == IS_ESPORTS_KEYRON) {
                    $bet_espt_fee = true === isset($row['bet_espt_fee']) && false === empty($row['bet_espt_fee']) ? $row['bet_espt_fee'] : 0;
                }

                $bet_hash_fee = 0;
                if ('ON' == IS_HASH) {
                    $bet_hash_fee = true === isset($row['bet_hash_fee']) && false === empty($row['bet_hash_fee']) ? $row['bet_hash_fee'] : 0;
                }
                $total_point += $bet_pre_s_fee + $bet_pre_d_fee + $bet_real_s_fee + $bet_real_d_fee + $bet_mini_fee + $bet_casino_fee + $bet_slot_fee + $bet_espt_fee + $bet_hash_fee;
            }
        }

        //It brings settlement information (deposit, holding money, holding points) of distributor members
        public static function doGetDistCalculateInfo($MEMAdminDAO, $db_today_s_date, $db_today_e_date, $db_srch_s_date, $db_srch_e_date, $dist_idx) {
            list($param_dist, $str_param_qm) = GameCode::getRecommandMemberIdx($dist_idx, $MEMAdminDAO);
            $str_param = implode(',', $param_dist);

            $where_new = " AND T1.recommend_member in($str_param)";
            $param_where_new = array();

            list($p_data['sql'], $param) = ComQuery::doComChExQuery($db_today_s_date, $db_today_e_date, $where_new, $param_where_new);
            //CommonUtil::logWrite(" distributor_list  doGetDistCalculateInfo today doComChExQuery sql" . $p_data['sql'], "info");
            //CommonUtil::logWrite(" distributor_list  doGetDistCalculateInfo today doComChExQuery param" . json_encode($param), "info");

            $db_dataChExArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $param);
            $db_dataChExArr = isset($db_dataChExArr) ? $db_dataChExArr : [];
            $dist_arr = [];
            foreach ($db_dataChExArr as $row) {

                //$dis_idx = $row['dis_idx'];
                if ($row['stype'] == 'ch') {
                    if (false === isset($dist_arr['today_ch_val'])) {
                        $dist_arr['today_ch_val'] = $row['s_money'];
                    } else {
                        $dist_arr['today_ch_val'] = $dist_arr['today_ch_val'] + $row['s_money'];
                    }
                } elseif ($row['stype'] == 'ex') {
                    if (false === isset($dist_arr['today_ex_val'])) {
                        $dist_arr['today_ex_val'] = $row['s_money'];
                    } else {
                        $dist_arr['today_ex_val'] = $dist_arr['today_ex_val'] + $row['s_money'];
                    }
                }
            }

            //CommonUtil::logWrite(" distributor_list  today doComChExQuery today db_dataChExArr" . json_encode($db_dataChExArr), "info");
            // 이번달 충전환전
            list($p_data['sql'], $param) = ComQuery::doComChExQuery($db_srch_s_date, $db_srch_e_date, $where_new, $param_where_new);
            $db_dataChExArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $param);
            $db_dataChExArr = isset($db_dataChExArr) ? $db_dataChExArr : [];

            //CommonUtil::logWrite(" distributor_list  doGetDistCalculateInfo month doComChExQuery sql" . $p_data['sql'], "info");
            //CommonUtil::logWrite(" distributor_list  doGetDistCalculateInfo month doComChExQuery param" . json_encode($param), "info");
            //CommonUtil::logWrite(" distributor_list  doGetDistCalculateInfo month doComChExQuery month db_dataChExArr" . json_encode($db_dataChExArr), "info");

            foreach ($db_dataChExArr as $row) {

                //$dis_idx = $row['dis_idx'];
                if ($row['stype'] == 'ch') {
                    if (false === isset($dist_arr['month_ch_val'])) {
                        $dist_arr['month_ch_val'] = $row['s_money'];
                    } else {
                        $dist_arr['month_ch_val'] = $dist_arr['month_ch_val'] + $row['s_money'];
                    }
                } elseif ($row['stype'] == 'ex') {
                    if (false === isset($dist_arr['month_ex_val'])) {
                        $dist_arr['month_ex_val'] = $row['s_money'];
                    } else {
                        $dist_arr['month_ex_val'] = $dist_arr['month_ex_val'] + $row['s_money'];
                    }
                }
            }

            //CommonUtil::logWrite(" distributor_list  doGetDistCalculateInfo month doComChExQuery dist_arr" . json_encode($dist_arr), "info");
            // 금일 총판별 관리자 포인트 차감 합
            $p_data['sql'] = ComQuery::getMPointSum($where_new);
            $param = array_merge([$db_today_s_date, $db_today_e_date], $param_where_new);
            $db_dataPointArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $param);
            $db_dataPointArr = isset($db_dataPointArr) ? $db_dataPointArr : [];

            foreach ($db_dataPointArr as $row) {
                //$dis_idx = $row['dis_idx'];
                if (false === isset($dist_arr['today_total_point'])) {
                    $dist_arr['today_total_point'] = $row['total_point'];
                } else {
                    $dist_arr['today_total_point'] = $dist_arr['today_total_point'] + $row['total_point'];
                }
            }

            // 이번달 총판별 관리자 포인트 차감 합
            $p_data['sql'] = ComQuery::getMPointSum($where_new);
            $param = array_merge([$db_srch_s_date, $db_srch_e_date], $param_where_new);
            $db_dataPointArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $param);
            $db_dataPointArr = isset($db_dataPointArr) ? $db_dataPointArr : [];

            foreach ($db_dataPointArr as $row) {
                //$dis_idx = $row['dis_idx'];
                if (false === isset($dist_arr['month_total_point'])) {
                    $dist_arr['month_total_point'] = $row['total_point'];
                } else {
                    $dist_arr['month_total_point'] = $dist_arr['month_total_point'] + $row['total_point'];
                }
            }

            // 금일 가입자수 
            $p_data['sql'] = CommonStatsQuery::getNewJoinMemberToalCount($where_new);
            $param = array_merge([$db_today_s_date, $db_today_e_date], $param_where_new);
            $db_dataJoinArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $param);
            $db_dataJoinArr = isset($db_dataJoinArr) ? $db_dataJoinArr : [];

            $dist_arr['today_mem_cnt_reg'] = $db_dataJoinArr[0]['total_count'];

            // 이번달 가입자수 
            $p_data['sql'] = CommonStatsQuery::getNewJoinMemberToalCount($where_new);
            $param = array_merge([$db_srch_s_date, $db_srch_e_date], $param_where_new);
            $db_dataJoinArr = $MEMAdminDAO->getQueryData_pre($p_data['sql'], $param);
            $db_dataJoinArr = isset($db_dataJoinArr) ? $db_dataJoinArr : [];

            $dist_arr['month_mem_cnt_reg'] = $db_dataJoinArr[0]['total_count'];

            return $dist_arr;
        }

        public function checkAdminType($session, $model) {

            $sql = "SELECT * FROM t_adm_user where session_key = ? ";
            $recordset = $model->getQueryData_pre($sql, [$session['akey']]);
            if (!isset($recordset)) {
                return false;
            }

            if ($recordset[0]['a_id'] != $session['aid']) {
                return false;
            }

            return true;
        }

        // 실시간 날짜별 현황 총판유형 추가버전
        public static function getRecommandMemberInfosByDistType($dis_idx, $dist_type, $model) {
            $sql = "
            WITH RECURSIVE TEMP AS (
                SELECT member.idx,member.id,member.nick_name,member.u_business,member.dis_id, 1 as lvl
                FROM member
                WHERE recommend_member = ? and u_business <> 1

                UNION ALL

                SELECT A.idx,A.id,A.nick_name,A.u_business,A.dis_id,lvl + 1 lvl
                FROM member A
                INNER JOIN TEMP B ON A.recommend_member = B.idx WHERE A.recommend_member > 0 and A.u_business <> 1
            )
            SELECT idx,id,nick_name FROM TEMP order by u_business asc";

            $result = $model->getQueryData_pre($sql, [$dis_idx]);
            $param = array($dis_idx);

            if(null != $result){
                foreach ($result as $dist) {
                    array_push($param, $dist['idx']);
                }
            }

            $str_param = implode(',', $param);
            $sql = "select idx,id,nick_name from member where idx in($str_param)  and dist_type = $dist_type";
            $result = $model->getQueryData_pre($sql, []);
            return $result;
        }
    }
    ?>
