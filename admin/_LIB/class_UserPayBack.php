<?php

//include_once('../class_CommonUtil.php');

class UserPayBack {

    static public function payback($level, $amount, $model) {
        try {
            $sql = "UPDATE charge_event SET pay_back_value =  ? where level = ?;";
            $model->setQueryData_pre($sql, [$amount, $level]);
        } catch (\mysqli_sql_exception $ex) {
            
            return false;
        }

        return true;
    }

    static public function AddCharge($member_idx, $amount, $model) {
        try {
            $sql = 'INSERT INTO tb_user_pay_back_info ('
                    . 'member_idx, '
                    . 'charge) VALUE '
                    . '(?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'charge = charge + VALUES(charge)';
            $model->setQueryData_pre($sql, [$member_idx, $amount]);
        } catch (\mysqli_sql_exception $ex) {
            return false;
        }

        return true;
    }

    static public function AddExchange($member_idx, $amount, $model) {

        try {
            $sql = "select charge from tb_user_pay_back_info where member_idx = ?";

            $result = $model->getQueryData_pre($sql, [$member_idx]);

            if (false === isset($result) || 0 == $result[0]['charge']) {
                return;
            }

            $sql = 'INSERT INTO `tb_user_pay_back_info` ('
                    . 'member_idx, '
                    . 'exchange) VALUE '
                    . '(?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'exchange = exchange + VALUES(exchange)';
            $model->setQueryData_pre($sql, [$member_idx, $amount]);
        } catch (\mysqli_sql_exception $ex) {
            return false;
        }

        return true;
    }

    static public function AddBetting($member_idx, $amount, $model) {
        try {
            $sql = "select charge from tb_user_pay_back_info where member_idx = ?";

            $result = $model->getQueryData_pre($sql, [$member_idx]);

            if (false === isset($result) || 0 == $result[0]['charge']) {
                return;
            }

            $sql = 'INSERT INTO tb_user_pay_back_info ('
                    . 'member_idx, '
                    . 'tot_bet_money) VALUE '
                    . '(?,?)'
                    . ' ON DUPLICATE KEY UPDATE '
                    . 'tot_bet_money = tot_bet_money + VALUES(tot_bet_money)';
            $model->setQueryData_pre($sql, [$member_idx, $amount]);
        } catch (\mysqli_sql_exception $ex) {
            return false;
        }

        return true;
    }

}

?>