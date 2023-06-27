<?php

class BaseGmPt {

    // 충전완료시 g_money를 적립해주는 함수 
    public function giveGMoneyCharge($Model, $u_key, $member_idx, $set_money, $now_cash, $af_cash
            , $ch_point, $now_point, $af_point, $p_a_comment, $charge_idx) {

        return 0;
    }

    public function useItemRefund($Model, $ukey, $member_idx, $bet_idx, $item_idx, $take_money, $bf_money) {
        return 0;
    }

    public function useItemAllocation($Model, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_price) {
        return 0;
    }

    public function getAvailableOneItemAtOrderbyCreate($Model, $member_idx, $itemId, $itemValue) {

        $p_data['sql'] = "SELECT
				item.type
				,member_item.*
			FROM
				member_item 
			LEFT JOIN
				item
			ON
				item.id = member_item.item_id
			WHERE
				member_idx = ?
			AND
				item_id = ?
			AND
				item_value = ?
			AND 
				member_item.status = 0
			ORDER BY
				member_item.create_dt
			LIMIT 1;";

        $item = $Model->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $item) {
            CommonUtil::logWrite("getAvailableOneItemAtOrderbyCreate item ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
        return true === isset($item) && false == empty($item) && 0 < $item[0]['idx'] ? $item : null;
    }

    // 아아템 고유번호 검증
    public function checkItemIdx($type, $member_idx, $item_idx, $Model) {
        if (!isset($item_idx) || 0 == $item_idx)
            return [false, '없는 아이템입니다', null];

        $p_data['sql'] = "select item.type,member_item.status,member_item.member_idx,member_item.item_id,member_item.item_value from member_item 
                left join item on item.id = member_item.item_id
                where idx = $item_idx ";

        $item = $Model->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $item) {
            CommonUtil::logWrite("checkItemIdx item ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        if (!isset($item) || true === empty($item))
            return [false, '없는 아이템 입니다', null];

        if ($member_idx != $item[0]['member_idx'])
            return [false, '아이템 정보가 틀립니다.', null];

        if (1 != $item[0]['status'])
            return [false, '사용할수 없는 아이템입니다.', null];

        if ($type != $item[0]['type'])
            return [false, '사용할수 있는 아이템이 아닙니다', null];

        return [true, '성공', $item];
    }

    // 아이템 사용 관련 데이터 업데이트 및 로그 기록 
    public function useItem($ukey, $member_idx, $item_id, $item_idx, $coment, $Model) {
        $p_data['sql'] = "update member_item set status = 1 where idx = $item_idx ";
        if (FAIL_DB_SQL_EXCEPTION === $Model->setQueryData($p_data)) {
            CommonUtil::logWrite("useItem setQueryData ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $this->insertLog($ukey, $member_idx, AC_GM_USEITEM, $item_id, $item_idx, 0, 0, 'M', $coment, 0, 0, $Model);
    }

    public function cancelItemUse($member_idx, $item_idx, $bet_idx, $Model) {

        if (!isset($item_idx) || 0 == $item_idx)
            return [true, ''];

        $p_data['sql'] = "select item.type,member_item.status,member_item.member_idx,member_item.item_id,member_item.item_value from member_item 
                left join item on item.id = member_item.item_id
                where idx = $item_idx ";

        $item = $Model->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $item) {
            CommonUtil::logWrite("cancelItemUse item ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        if (!isset($item) || true === empty($item))
            return [false, '없는 아이템 입니다'];
        $item_id = $item[0]['item_id'];

        $p_data['sql'] = "update member_item set status = 0 where idx = $item_idx ";
        if (FAIL_DB_SQL_EXCEPTION === $Model->setQueryData($p_data)) {
            CommonUtil::logWrite("cancelItemUse setQueryData ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $this->insertLog('', $member_idx, AC_GM_CANCEL_ITEM_USE, $item_id, $item_idx, $bet_idx, 0, 'P', '관리자 베팅취소로 아이템사용 반환', 0, 0, $Model);
        return [true, '아이템 사용취소 성공'];
    }

    public function insertLog($ukey, $member_idx, $ac_code, $ac_idx
            , $r_money, $be_r_money, $af_r_money
            , $kind, $coment, $point, $g_money
            , $model) {

        $p_data['sql'] = "insert into  t_log_cash ";
        $p_data['sql'] .= " (u_key,member_idx, ac_code,ac_idx,r_money, be_r_money, af_r_money, m_kind, coment,point,g_money)  values('$ukey',$member_idx,$ac_code
                ,$ac_idx,$r_money,$be_r_money,$af_r_money,'$kind','$coment',$point,$g_money)";

        if (FAIL_DB_SQL_EXCEPTION === $model->setQueryData($p_data)) {
            CommonUtil::logWrite("insertLog setQueryData ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }
    }

    // 보너스 배당 처리 
    public function calBonusPrice($total_bet_price, $bonus_price, $folder_type, $win_limit_price_count, $arr_config) {
        if ('D' == $folder_type && 3 <= $win_limit_price_count && 'Y' == $arr_config['service_bonus_folder']) {
            if (7 <= $win_limit_price_count) { // 머지 대상 아님 
                $bonus_price = $arr_config['odds_7_folder_bonus'];
            } else if ($win_limit_price_count == 6) {
                $bonus_price = $arr_config['odds_6_folder_bonus'];
            } else if ($win_limit_price_count == 5) {
                $bonus_price = $arr_config['odds_5_folder_bonus'];
            } else if ($win_limit_price_count == 4) {
                $bonus_price = $arr_config['odds_4_folder_bonus'];
            } else if ($win_limit_price_count == 3) {
                $bonus_price = $arr_config['odds_3_folder_bonus'];
            }

            $total_bet_price = round($total_bet_price * $bonus_price, 2);
        } else {
            $total_bet_price = round($total_bet_price, 2);
        }

        return [$total_bet_price, $bonus_price];
    }

    // game config 정보를 가져온다.
    public function getConfigData($tgcModel) {

        $p_data['sql'] = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type IN('service_bonus_folder','odds_3_folder_bonus','odds_4_folder_bonus','odds_5_folder_bonus'"
                . ",'inplay_status','inplay_no_betting_list','service_sports','service_real','odds_6_folder_bonus','odds_7_folder_bonus','limit_folder_bonus')"; // 머지 대상 아님 
        //$arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

        $arr_config_result = $tgcModel->getQueryData($p_data);
        if (FAIL_DB_SQL_EXCEPTION === $arr_config_result) {
            CommonUtil::logWrite("getConfigData arr_config_result ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $arr_config = array();
        foreach ($arr_config_result as $key => $value) {
            $arr_config[$value['set_type']] = $value['set_type_val'];
        }

        return $arr_config;
    }

    // 게임 결과 처리 함수 
    public function checkGameResult($bet_detail, $arr_config, $bet_total_count) {
        $total_bet_price = 1;
        $hit_count = 0; // 적특 카운트 
        $win_limit_price_count = 0; // 적중 $$ 리미티 배당조건 만족 카운트 
        $win_count = 0; // 적중 카운트
        $lose_count = 0; // 미적중 카운트
        //$logger->info("checkGameResult 1 ". json_encode($bet_detail));
        //CommonUtil::logWrite("checkGameResult 1 " . json_encode($bet_detail), "error");
        foreach ($bet_detail as $value_dt) {
            if (6 == $value_dt['bet_status']) {
                ++$hit_count;
            } else if (4 == $value_dt['bet_status']) {
                ++$lose_count;
            } else if (2 == $value_dt['bet_status']) { // 1.15이하의 배당은 보너스배당 카운트 제외
                $total_bet_price = $total_bet_price * $value_dt['detail_bet_price'];
                ++$win_count;
                if ($arr_config['limit_folder_bonus'] <= $value_dt['detail_bet_price']) {
                    ++$win_limit_price_count;
                }
            }
        }

        //$logger->info("checkGameResult 2");
        CommonUtil::logWrite("checkGameResult 2 ", "error");
        $total_count = $hit_count + $win_count + $lose_count;
        if ($total_count != count($bet_detail)) {

            return [false, $total_bet_price, $win_limit_price_count, $win_count, $lose_count];
        }

        return [true, $total_bet_price, $win_limit_price_count, $win_count, $lose_count];
    }

    public function decBetSum($resultDetailBet, $member_idx, $bet_type, $total_bet_money, $memberBetModel) {
        $sql = array();
        foreach ($resultDetailBet as $value) {
            $insertSql = '('
                    . $member_idx . ', '
                    . $value['ls_fixture_id'] . ', "'
                    . $bet_type . '", '
                    . $total_bet_money . ')';
            array_push($sql, $insertSql);
        }

        $p_data['sql'] = 'INSERT INTO `fixtures_bet_sum` ('
                . 'member_idx, '
                . 'fixture_id, '
                . 'bet_type, '
                . 'sum_bet_money) VALUES '
                . implode(',', $sql)
                . ' ON DUPLICATE KEY UPDATE '
                . 'sum_bet_money = sum_bet_money - VALUES(sum_bet_money)';

        CommonUtil::logWrite("decBetSum setQueryData ".$p_data['sql'], "info");
        if (FAIL_DB_SQL_EXCEPTION === $memberBetModel->setQueryData($p_data)) {
            CommonUtil::logWrite("decBetSum setQueryData ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        /* $memberBetModel->db->query(
          'INSERT INTO `fixtures_bet_sum` ('
          . 'member_idx, '
          . 'fixture_id, '
          . 'bet_type, '
          . 'sum_bet_money) VALUES '
          . implode(',', $sql)
          . ' ON DUPLICATE KEY UPDATE '
          . 'sum_bet_money = sum_bet_money - VALUES(sum_bet_money)'
          ); */
    }

    public function addBetSum($resultDetailBet, $member_idx, $bet_type, $total_bet_money, $memberBetModel) {
        $sql = array();
        foreach ($resultDetailBet as $value) {
            $insertSql = '('
                    . $member_idx . ', '
                    . $value['ls_fixture_id'] . ', "'
                    . $bet_type . '", '
                    . $total_bet_money . ')';
            array_push($sql, $insertSql);
        }

        $p_data['sql'] = 'INSERT INTO `fixtures_bet_sum` ('
                . 'member_idx, '
                . 'fixture_id, '
                . 'bet_type, '
                . 'sum_bet_money) VALUES '
                . implode(',', $sql)
                . ' ON DUPLICATE KEY UPDATE '
                . 'sum_bet_money = sum_bet_money + VALUES(sum_bet_money)';

        if (FAIL_DB_SQL_EXCEPTION === $memberBetModel->setQueryData($p_data)) {
            CommonUtil::logWrite("addBetSum setQueryData ", "error");
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        /* $memberBetModel->db->query(
          'INSERT INTO `fixtures_bet_sum` ('
          . 'member_idx, '
          . 'fixture_id, '
          . 'bet_type, '
          . 'sum_bet_money) VALUES '
          . implode(',', $sql)
          . ' ON DUPLICATE KEY UPDATE '
          . 'sum_bet_money = sum_bet_money - VALUES(sum_bet_money)'
          ); */
    }

    public function doLose($value, $arrMbBtDtResult, $ukey, $bet_total_count, $win_count, $lose_count, $a_comment, $bet_type
            , $ALBetDAO,$UTIL) {
        $member_idx = $value['member_idx'];
        $admin_id = $value['admin_id'];
        $calculate_dt = $value['calculate_dt'];

        $ch_point_lose_self_per = 0;
        if ('ON' == IS_MULTI_LOSS_ROLLING) {
            if ((false === isset($calculate_dt) || true === empty($calculate_dt))  // 미정산된 베팅을 수동으로 정산시 낙첨 롤링 적용
                    && ((5 <= $bet_total_count && $bet_total_count == $lose_count) || (5 <= $bet_total_count && 1 == $lose_count && $win_count == $bet_total_count - 1))) {

                list($ch_point_lose_self_per) = CommonUtil::log_lose_bet_bonus_point($ALBetDAO, $member_idx, $value['bet_idx'], $value['total_bet_money']);

                CommonUtil::logWrite("doReCalculate log_lose_bet_bonus_point bet_idx : " . $value['bet_idx'] . ' point : ' . $ch_point_lose_self_per, 'info');
            }
        } else {
            list($ch_point_lose_self_per) = CommonUtil::log_lose_bet_bonus_point($ALBetDAO, $member_idx, $value['bet_idx'], $value['total_bet_money']);

            CommonUtil::logWrite("doReCalculate log_lose_bet_bonus_point bet_idx : " . $value['bet_idx'] . ' point : ' . $ch_point_lose_self_per, 'info');
        }

        $rolling_money = $this->useItemRefund($ALBetDAO, $ukey, $member_idx, $value['mb_bt_idx'], $value['item_idx'], $value['total_bet_money'], $value['money']);

        if ('P' == $value['flag_bet_sum']) {
            $this->decBetSum($arrMbBtDtResult, $member_idx, $bet_type, $value['total_bet_money'], $ALBetDAO);
        }

        if (FAIL_DB_SQL_EXCEPTION === $ALBetDAO->UpdateMemberBet($value['calculate_dt'], $value['mb_bt_idx'], 3, $rolling_money, $ch_point_lose_self_per, 'M')) {
            throw new mysqli_sql_exception('mysqli_sql_exception!!!');
        }

        $a_comment .= "수동 개별 정산 낙첨 [" . $value['fixture_sport_name'] . "] " . $value['fixture_location_name'] . " " . $value['fixture_league_name'] . " ";
        $a_comment .= $value['fixture_participants_1_name'] . " VS " . $value['fixture_participants_2_name'];
        $a_comment = addslashes($a_comment);
        $UTIL->log_cash($ALBetDAO, $ukey, $member_idx, 7, $value['mb_bt_idx'], 0, $value['money'], $admin_id, $a_comment, 'P');

        CommonUtil::logWrite("doReCalculate lose end bet_idx : " . $value['bet_idx'], 'info');

        return [true, $value['total_bet_money'], $rolling_money];
    }

}
