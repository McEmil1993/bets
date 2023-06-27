<?php

namespace App\GamblePatch;

use App\Models\TGameConfigModel;

class BaseGmPt {

    // 충전완료시 g_money를 적립해주는 함수 
    public function giveGMoneyCharge($Model, $u_key, $member_idx, $set_money, $now_cash, $af_cash
            , $ch_point, $now_point, $af_point, $p_a_comment, $charge_idx, $logger) {
        
    }

    // 게시판 사용시 하루에 한번 지급한다.
    public function giveGmoneyBorder($Model, $member_idx, $logger) {
        
    }

    // 환급패치 사용
    public function useItemRefund($Model, $ukey, $member_idx, $bet_idx, $item_idx, $take_money, $bf_money, $logger) {

        $logger->info("BaseGmPt useItemRefund 1 bet_idx : " . $bet_idx);

        return 0;
    }

    //배당 패치 사용
    public function useItemAllocation($Model, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_price, $logger) {
        return 0;
    }

    //적특 배치 사용
    public function useItemHitSpecial($Model, $member_idx, $bet_idx, $bet_detail_idx, $logger) {
        
    }

    // 아아템 고유번호 검증
    public function checkItemIdx($type, $member_idx, $item_idx, $Model, $logger) {
        if (!isset($item_idx) || 0 == $item_idx)
            return [false, '없는 아이템입니다', null];

        $sql = "select item.type,member_item.status,member_item.member_idx,member_item.item_id,member_item.item_value from member_item 
                left join item on item.id = member_item.item_id
                where idx = ? ";

        $item = $Model->db->query($sql, [$item_idx])->getResultArray();

        if (!isset($item) || true === empty($item))
            return [false, '없는 아이템 입니다', null];

        if ($member_idx != $item[0]['member_idx'])
            return [false, '아이템 정보가 틀립니다.', null];

        if (0 == $item[0]['status'])
            return [false, '이미 사용한 아이템입니다', null];

        if ($type != $item[0]['type'])
            return [false, '사용할수 있는 아이템이 아닙니다', null];

        return [true, '성공', $item];
    }

    // 아이템 사용 관련 데이터 업데이트 및 로그 기록 
    public function useItem($ukey, $member_idx, $item_id, $item_idx, $coment, $Model, $logger) {

        $logger->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!! useItem !!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $sql = "update member_item set status = 1 where idx = ? ";
        $Model->db->query($sql, [$item_idx]);

        $this->insertLog($ukey, $member_idx, AC_GM_USEITEM, $item_id, $item_idx, 0, 0, 'M', $coment, 0, 0, $Model, $logger);
    }

    public function cancelItemUse($member_idx, $item_idx, $bet_idx, $Model, $logger) {

        if (!isset($item_idx) || 0 == $item_idx)
            return [true, ''];

        $sql = "select item.type,member_item.status,member_item.member_idx,member_item.item_id,member_item.item_value from member_item 
                left join item on item.id = member_item.item_id
                where idx = ? ";

        $item = $Model->db->query($sql, [$item_idx])->getResultArray();
        if (!isset($item) || true === empty($item))
            return [false, '없는 아이템 입니다'];
        $item_id = $item[0]['item_id'];

        $sql = "update member_item set status = 0 where idx = ? ";
        $Model->db->query($sql, [$item_idx]);

        $this->insertLog('', $member_idx, AC_GM_CANCEL_ITEM_USE, $item_id, $item_idx, $bet_idx, 0, 'P', '베팅취소로 아이템사용 반환', 0, 0, $Model, $logger);
        return [true, '아이템 사용취소 성공'];
    }

    // 로그 
    public function insertLog($ukey, $member_idx, $ac_code, $ac_idx
            , $r_money, $be_r_money, $af_r_money
            , $kind, $coment, $point, $g_money
            , $model, $logger) {
        $logger->info("insertLog");
        $sql = "insert into  t_log_cash ";
        $sql .= " (u_key,member_idx, ac_code,ac_idx,r_money, be_r_money, af_r_money, m_kind, coment,point,g_money)  values(?,?,?,?,?,?,?,?,?,?,?)";
        $model->db->query($sql, [$ukey, $member_idx, $ac_code, $ac_idx, $r_money, $be_r_money, $af_r_money, $kind, $coment, $point, $g_money]);
    }

    // 보유 아이템에서 특정 아이템 정보를 가져온다.
    public function getAvailableOneItemAtOrderbyCreate($Model, $member_idx, $itemId, $itemValue) {


        $sql = "SELECT
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

        $item = $Model->db->query($sql, [$member_idx, $itemId, $itemValue])->getResultArray();

        return true === isset($item) && false == empty($item) && 0 < $item[0]['idx'] ? $item : null;
    }

    // 보유 아이템에서 가장오래된 적특 패치 아이템을 가져온다. 
    public function getAvailableOneHitSpecialItemAtOrderbyCreate($Model, $member_idx) {

        $sql = "SELECT
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
                            item.type = 3        
                    AND
                            member_item.status = 0
                    AND 
                            member_item.create_dt >= date_add(now(), interval -1 month)
                    ORDER BY
                            member_item.create_dt
                    LIMIT 1;";

        $item = $Model->db->query($sql, [$member_idx])->getResultArray();

        return true === isset($item) && false == empty($item) && 0 < $item[0]['idx'] ? $item : null;
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
    
    // 보너스 배당 1.3이하 있는지 체크
    public function isLimitFolder($betList, $limitFolderBonus) {
        return false;
    }

    // game config 정보를 가져온다.
    public function getConfigData($tgcModel = null) {
        if (null == $tgcModel) {
            $tgcModel = new TGameConfigModel();
        }

        $str_sql_config = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type IN('service_bonus_folder','odds_3_folder_bonus','odds_4_folder_bonus','odds_5_folder_bonus'"
                . ",'inplay_status','inplay_no_betting_list','service_sports','service_real','odds_6_folder_bonus','odds_7_folder_bonus','limit_folder_bonus','service_classic')"; // 머지 대상 아님 

        $arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

        $arr_config = array();
        foreach ($arr_config_result as $key => $value) {
            $arr_config[$value['set_type']] = $value['set_type_val'];
        }


        return $arr_config;
    }

    // 게임 결과 처리 함수 
    public function checkGameResult($bet_detail, $arr_config, $bet_total_count, $logger) {
        $total_bet_price = 1;
        $hit_count = 0; // 적특 카운트 
        $win_limit_price_count = 0; // 적중 $$ 리미티 배당조건 만족 카운트 
        $win_count = 0; // 적중 카운트
        $lose_count = 0; // 미적중 카운트
        //$logger->info("checkGameResult 1 ". json_encode($bet_detail));

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
        $total_count = $hit_count + $win_count + $lose_count;
        if ($total_count != count($bet_detail)) {

            return [false, $total_bet_price, $win_limit_price_count, $win_count, $lose_count];
        }

        return [true, $total_bet_price, $win_limit_price_count, $win_count, $lose_count];
    }

    public function decBetSum($resultDetailBet, $member_idx, $bet_type, $total_bet_money, $memberBetModel, $logger) {
        $sql = array();
        foreach ($resultDetailBet as $value) {
            $insertSql = '('
                    . $member_idx . ', '
                    . $value['ls_fixture_id'] . ', "'
                    . $bet_type . '", '
                    . $total_bet_money . ')';
            array_push($sql, $insertSql);
        }
        $memberBetModel->db->query(
                'INSERT INTO `fixtures_bet_sum` ('
                . 'member_idx, '
                . 'fixture_id, '
                . 'bet_type, '
                . 'sum_bet_money) VALUES '
                . implode(',', $sql)
                . ' ON DUPLICATE KEY UPDATE '
                . 'sum_bet_money = sum_bet_money - VALUES(sum_bet_money)'
        );
    }

    public function doLose($valueMbBet, $resultDetailBet, $ukey, $money, $bet_total_count
            , $win_count, $lose_count,$a_comment,$bet_type
            , $memberModel, $tLogCashModel, $memberBetModel, $logger) {
        
        $bet_idx = $valueMbBet['idx'];
        $member_idx = $valueMbBet['member_idx'];
        $total_bet_money = $valueMbBet['total_bet_money'];
        $item_idx = $valueMbBet['item_idx'];
        // 5폴더이상이고 모두 낙첨일때 보너스 포인트 지급 
        $ch_point_lose_self_per = 0;

        if ('ON' == config(App::class)->IS_MULTI_LOSS_ROLLING) {
            if ((5 <= $bet_total_count && $bet_total_count == $lose_count) || (5 <= $bet_total_count && 1 == $lose_count && $win_count == $bet_total_count - 1)) {
                list($ch_point_lose_self_per) = $memberModel->log_lose_bet_bonus_point($member_idx, $bet_idx, $total_bet_money);
            }
        } else {
            list($ch_point_lose_self_per) = $memberModel->log_lose_bet_bonus_point($member_idx, $bet_idx, $total_bet_money);
        }

        $rolling_money = $this->useItemRefund($memberBetModel, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_money, $money, $logger);

        $this->decBetSum($resultDetailBet, $member_idx, $bet_type, $total_bet_money, $memberBetModel, $logger);
        $memberBetModel->UpdateMemberBet($bet_idx, 3, $rolling_money, $ch_point_lose_self_per, 0, 'M');

        $a_comment .= " 낙첨 ";

        $tLogCashModel->insertCashLog_mem_idx($ukey, $member_idx, 7, $bet_idx, 0, $money,'R',$a_comment);
                                             
    }

}
