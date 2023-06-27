<?php

namespace App\GamblePatch;

use App\GamblePatch\BaseGmPt;
use App\Models\MemberModel;
use App\Models\MemberBetModel;
use App\Models\TLogCashModel;

class BullsGmPt extends BaseGmPt {

    // 충전시 적립
    public function giveGMoneyCharge($Model, $u_key, $member_idx, $set_money, $now_cash, $af_cash
    , $ch_point, $now_point, $af_point, $p_a_comment, $charge_idx, $logger) {

        $sql = "select set_type_val from t_game_config where set_type = 'gm_pt_charge_value' ";

        $resultData = $Model->db->query($sql)->getResultArray();
        $f_gm_pt_charge_value = $resultData[0]['set_type_val'];

        $g_money = $f_gm_pt_charge_value * $set_money;

        $sql = "select g_money from member where idx = ? ";

        $resultData = $Model->db->query($sql, [$member_idx])->getResultArray();

        $bf_g_money = $resultData[0]['g_money'];

        $this->insertLog($u_key, $member_idx, 1, $charge_idx, $set_money, $now_cash, $af_cash, 'P', '머니 충전완료', $ch_point, $g_money, $Model, $logger);

        if (0 < $ch_point) {
            $this->insertLog($u_key, $member_idx, 10,$charge_idx, $ch_point, $now_point, $af_point, 'P', $p_a_comment, 0, 0, $Model, $logger);
        }

        if (0 < $g_money) {

            $sql = "update member set g_money = g_money + ? where idx = ? ";
            $Model->db->query($sql, [$g_money, $member_idx]);

            $sql = "update member_money_charge_history set bonus_money = ? where idx = ? ";
            $Model->db->query($sql, [$g_money, $charge_idx]);

            $af_g_money = $bf_g_money + $g_money;
            $this->insertLog($u_key, $member_idx, AC_CH_GM_TAKE, $charge_idx, $g_money, $bf_g_money, $af_g_money, 'P', 'G_Money 충전', 0, 0, $Model, $logger);
        }
    }

    // 게시판 사용시 하루에 한번 지급한다.
    public function giveGmoneyBorder($Model, $member_idx, $logger) {
        $sql = "select g_money,is_set_day_nt_gm from member where idx = ? ";

        $resultData = $Model->db->query($sql, [$member_idx])->getResultArray();

        $bf_g_money = $resultData[0]['g_money'];

        $is_set_day_nt_gm = $resultData[0]['is_set_day_nt_gm'];

        if ('Y' == $is_set_day_nt_gm) {
            return;
        }

        $sql = "select set_type_val from t_game_config where set_type = 'gm_pt_border_value' ";

        $resultData = $Model->db->query($sql)->getResultArray();
        $f_gm_pt_charge_value = $resultData[0]['set_type_val'];

        $g_money = $f_gm_pt_charge_value + $set_money;
        if (0 < $g_money) {
            $sql = "update member set g_money = g_money + ? where idx = ? ";
            $Model->db->query($sql, [$g_money, $member_idx]);
            $ukey = md5($member_idx . strtotime('now'));
            $af_g_money = $bf_g_money + $g_money;

            $this->insertLog($ukey, $member_idx, AC_BD_GM_TAKE, 0, $g_money, $bf_g_money, $af_g_money, 'P', 'G_Money 충전', 0, 0, $Model, $logger);
        }
    }

    // 환급패치 
    public function useItemRefund($Model, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_money, $bf_money, $logger) {

        $logger->info("GambelGmPt useItemRefund start bet_idx : ".$bet_idx);
        list($retval, $error, $item) = $this->checkItemIdx(GM_REFUND,$member_idx, $item_idx, $Model, $logger);
        if (false == $retval){
            //$logger->info("GambelGmPt useItemRefund checkItemIdx bet_idx : ".$bet_idx.' error : '.$error);
            return 0;
        }

        $rollback_money = $total_bet_money * $item[0]['item_value'];
        $af_money = $bf_money + $rollback_money;
        $sql = "update member set money = money + ? where idx = ? ";
        $Model->db->query($sql, [$rollback_money, $member_idx]);

        $logger->info("GambelGmPt useItemRefund end bet_idx : ".$bet_idx);
        
        $this->insertLog($ukey, $member_idx, AC_GM_REFUND_MONEY, $bet_idx, $rollback_money, $bf_money, $af_money, 'P', '환급패치 아이템 사용 머니 충전', 0, 0, $Model, $logger);
        return $rollback_money;
    }

    // 배당 패치 
    public function useItemAllocation($Model, $ukey, $member_idx, $bet_idx, $item_idx, $total_bet_price, $logger) {
        $logger->info("GambelGmPt useItemAllocation start bet_idx : ".$bet_idx);
        
        list($retval, $error, $item) = $this->checkItemIdx(GM_ALLOCATION,$member_idx, $item_idx, $Model, $logger);
        if (false == $retval){
            $logger->error("GambelGmPt useItemAllocation checkItemIdx bet_idx : ".$bet_idx.' error : '.$error);
            return 0;
        }
        
        $rollback_pirce = $total_bet_price * $item[0]['item_value'];
        
        $logger->info("GambelGmPt useItemAllocation call price bet_idx : ".$bet_idx.' rollback_pirce : '.$rollback_pirce.' item_value : '.$item[0]['item_value'].' total_bet_price : '.$total_bet_price);
        
        $this->insertLog($ukey, $member_idx, AC_GM_ALLOCATION_PRICE, $bet_idx, $rollback_pirce, $total_bet_price, $total_bet_price + $rollback_pirce, 'P', '배당패치 아이템 사용 배당 증가', 0, 0, $Model, $logger);
        
        $logger->info("GambelGmPt useItemAllocation end bet_idx : ".$bet_idx.' rollback_pirce : '.$rollback_pirce);
                
        return $rollback_pirce;
    }

    
    // 적특 패치 
    public function useItemHitSpecial($Model, $member_idx, $bet_idx, $bet_detail_idx,$logger) {
        $item = $this->getAvailableOneHitSpecialItemAtOrderbyCreate($Model,$member_idx);
        if(null == $item){
            return [false, '사용할수 있는 아이템이 없습니다.'];
        }
        
        $sql = "select member_bet.*,member.money from member_bet 
                left join member on member.idx = member_bet.member_idx
                where member_bet.idx = ?";

        $bet = $Model->db->query($sql, [$bet_idx])->getResultArray();
        if (!isset($bet) || true === empty($bet))
            return [false, '해당 배팅 정보가 없습니다.'];

        if (3 != $bet[0]['bet_status'] || 1 != $bet[0]['bet_type'] || 0 != $bet[0]['take_money']) {
            return [false, '배팅 정보가 올바르지 않습니다.'];
        }
        
        if (0 != $bet[0]['item_idx'] ) {
            return [false, '중복으로 아이템을 사용할수 없습니다.'];
        }

        $sql = "select bet_dt.idx, 
                bet_dt.bet_type,
                bet_dt.bet_status,
                bet_dt.bet_price as detail_bet_price
                from member_bet_detail as bet_dt 
                left join member_bet as mb_bet on mb_bet.idx = bet_dt.bet_idx
                left join lsports_bet as bet on bet.bet_id =  bet_dt.ls_bet_id
                where bet_dt.bet_idx = ? ";

        $bet_detail = $Model->db->query($sql, [$bet_idx])->getResultArray();
        if (!isset($bet_detail) || true === empty($bet_detail))
            return [false, '이미 사용한 아이템입니다.'];

        if (count($bet_detail) < 3)
            return [false, '3폴더 이상 베팅에서만 사용가능합니다.'];
          
        $find = false;
        foreach ($bet_detail as $key => $detail) {
            if ($bet_detail_idx != $detail['idx'])
                continue;
            if (4 != $detail['bet_status'])
                return [false, '배팅 상세정보의 상태값에 오류가 있습니다'];
            $bet_detail[$key]['bet_status'] = 6;
            $find = true;
            break;
        }
        if (false == $find) {
            return [false, '배팅 상세정보를 찾을 수 없습니다.'];
        }

        $arr_config = $this->getConfigData();
        $bet_total_count = count($bet_detail);
        
      
        $logger->info('useItemHitSpecial bet_detail : '. json_encode($bet_detail));
        list($retval, $total_bet_price, $win_limit_price_count, $win_count, $lose_count) = $this->checkGameResult($bet_detail, $arr_config, $bet_total_count,$logger);

        $logger->info('useItemHitSpecial total_bet_price : '. $total_bet_price);
         
        if (false == $retval) {
            return [false, '배팅 상세정보에 오류가 있습니다'];
        }

        if (0 < $lose_count)
            return [false, '아이템을 사용해도 낙첨된 경기입니다'];

        $money = $bet[0]['money'];
        $folder_type = $bet[0]['folder_type '];
        $bonus_price = $bet[0]['bonus_price'];
        $total_bet_money = $bet[0]['total_bet_money'];
        $a_comment = 'prematch ==>';
        $ukey = md5($member_idx . strtotime('now'));

        $logger->info('useItemHitSpecial calBonusPrice : ');
        list($total_bet_price,$bonus_price) = $this->calBonusPrice($total_bet_price, $bonus_price, $folder_type, $win_limit_price_count, $arr_config);

        $logger->info('useItemHitSpecial calBonusPrice end: ');
        $take_money = sprintf('%0.2f', $total_bet_price * $total_bet_money);

        // 해당 데이터를 업데이트 한다.
        // member 의 머니 업데이트를 해줘야 한다.
        if ($take_money > 0) {
            $sql = "update member set money = money + ? where idx = ? ";
            $Model->db->query($sql, [$take_money, $member_idx]);
            $logger->info('useItemHitSpecial take_money : '.$take_money);
        }

        $memberBetModel = new MemberBetModel();

        $memberBetModel->UpdateMemberBetBonus($bet_idx, 3, $take_money, $bonus_price,$item[0]['idx'],0, 0,0);

        $sql = "UPDATE member_bet_detail SET bet_price = 1,bet_status = 6 WHERE idx = ?";
        $memberBetModel->db->query($sql, [$bet_detail_idx]);

        $a_comment .= "적특패치 적중";

        $tLogCashModel = new TLogCashModel();
        $tLogCashModel->insertCashLog_mem_idx($ukey, $member_idx, AC_GM_HIT_SPECIAL_MONEY, $bet_idx, $take_money, $money,'P',$a_comment);
        
        $this->useItem($ukey, $member_idx, $item[0]['item_id'], $item[0]['idx'], '적특패치 아이템 사용', $Model, $logger);
        
         $logger->info('useItemHitSpecial end : ');
        return [true, '성공'];
    }

}
