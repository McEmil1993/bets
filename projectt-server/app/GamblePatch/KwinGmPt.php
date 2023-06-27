<?php

namespace App\GamblePatch;

use App\GamblePatch\BaseGmPt;
use App\Models\MemberModel;

class KwinGmPt extends BaseGmPt {

    public function giveGMoneyCharge($Model, $u_key, $member_idx, $set_money, $now_cash, $af_cash
    , $ch_point, $now_point, $af_point, $p_a_comment, $charge_idx, $logger) {

        $this->insertLog($u_key, $member_idx, 1, 0, $set_money, $now_cash, $af_cash, 'P', '머니 충전완료', $ch_point, 0, $Model);

        if (0 < $ch_point) {
            $this->insertLog($u_key, $member_idx, 10, 0, $ch_point, $now_point, $af_point, 'P', $p_a_comment, 0, 0, $Model);
        }
    }

    public function getAvailableOneItemAtOrderbyCreate($Model, $member_idx, $itemId, $itemValue) {
        return null;
    }

}