<?php

namespace App\Util;

class UserPayBack {

    static public function AddCharge($member_idx, $amount, $model) {

        $sql = 'INSERT INTO `tb_user_pay_back_info` ('
                . 'member_idx, '
                . 'charge) VALUE '
                . '(?,?)'
                . ' ON DUPLICATE KEY UPDATE '
                . 'charge = charge + VALUES(charge)';
        $model->db->query($sql, [$member_idx, $amount]);
    }

    static public function AddExchange($member_idx, $amount, $model) {
        $sql = "select charge from tb_user_pay_back_info where member_idx = ?";

        $result = $model->db->query($sql, [$member_idx])->getResultArray();

        if (false === isset($result) || 0 == $result[0]['charge']) {
            return;
        }

        $sql = 'INSERT INTO `tb_user_pay_back_info` ('
                . 'member_idx, '
                . 'exchange) VALUE '
                . '(?,?)'
                . ' ON DUPLICATE KEY UPDATE '
                . 'exchange = exchange + VALUES(exchange)';
        $model->db->query($sql, [$member_idx, $amount]);
    }

    static public function AddBetting($member_idx, $amount, $model) {
        
        $sql = "select charge from tb_user_pay_back_info where member_idx = ?";

        $result = $model->db->query($sql, [$member_idx])->getResultArray();

        if (false === isset($result) || 0 == $result[0]['charge']) {
            return;
        }
        
        $sql = 'INSERT INTO `tb_user_pay_back_info` ('
                . 'member_idx, '
                . 'tot_bet_money) VALUE '
                . '(?,?)'
                . ' ON DUPLICATE KEY UPDATE '
                . 'tot_bet_money = tot_bet_money + VALUES(tot_bet_money)';
        $model->db->query($sql, [$member_idx, $amount]);
    }

}
