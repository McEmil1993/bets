<?php

class ChExFunc {
    
    public function __construct() {
        
    }

    public function do_select_charge_first($bonus_level, $bonus_option_idx, $set_money, $memberMCHModel) {
        $sql = "select * from charge_type where level = ?";
        $retChargeTypeData = $memberMCHModel->getQueryData_pre($sql, [$bonus_level]);
        
        $charge_first_per_key = 'bonus_'.$bonus_option_idx.'_charge_first_per';
        $charge_first_money_key = 'bonus_'.$bonus_option_idx.'_charge_first_money';
        $charge_first_max_money_key = 'bonus_'.$bonus_option_idx.'_charge_first_max_money';
        $const_value = $ratio_value = 0;
        if (0 >= $bonus_option_idx) {
            $ch_point = 0;
        } else /*if (1 == $bonus_option_idx)*/ {
            if (0 < $retChargeTypeData[0][$charge_first_money_key]) {
                $ch_point = $retChargeTypeData[0][$charge_first_money_key];
                $const_value = $retChargeTypeData[0][$charge_first_money_key];
            } else {
                $ch_point = ($retChargeTypeData[0][$charge_first_per_key] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0][$charge_first_per_key];
            }

            if ($retChargeTypeData[0][$charge_first_max_money_key] < $ch_point) {
                $ch_point = $retChargeTypeData[0][$charge_first_max_money_key];
            }
        }/* else if (2 == $bonus_option_idx) {
            if (0 < $retChargeTypeData[0][$charge_first_money_key]) {
                $ch_point = $retChargeTypeData[0][$charge_first_money_key];
                $const_value = $retChargeTypeData[0][$charge_first_money_key];
            } else {
                $ch_point = ($retChargeTypeData[0][$charge_first_per_key] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0][$charge_first_per_key];
            }

            if ($retChargeTypeData[0][$charge_first_max_money_key] < $ch_point) {
                $ch_point = $retChargeTypeData[0][$charge_first_max_money_key];
            }
        }*/

        return [$ch_point, $ratio_value, $const_value];
    }

    public function do_select_charge($bonus_level, $bonus_option_idx, $set_money, $memberMCHModel) {
        $sql = "select * from charge_type where level = ?";
        $retChargeTypeData = $memberMCHModel->getQueryData_pre($sql, [$bonus_level]);
        
        $charge_per_key = 'bonus_'.$bonus_option_idx.'_charge_per';
        $charge_money_key = 'bonus_'.$bonus_option_idx.'_charge_money';
        $charge_max_money_key = 'bonus_'.$bonus_option_idx.'_charge_max_money';
        $const_value = $ratio_value = 0;
        if (0 >= $bonus_option_idx) {
            $ch_point = 0;
        } else /*if (1 == $bonus_option_idx)*/ {
            if (0 < $retChargeTypeData[0][$charge_money_key]) {
                $ch_point = $retChargeTypeData[0][$charge_money_key];
                $const_value = $retChargeTypeData[0][$charge_money_key];
            } else {
                $ch_point = ($retChargeTypeData[0][$charge_per_key] * $set_money) / 100;
                $ratio_value = $retChargeTypeData[0][$charge_per_key];
            }

            if ($retChargeTypeData[0][$charge_max_money_key] < $ch_point) {
                $ch_point = $retChargeTypeData[0][$charge_max_money_key];
            }
        }/* else if (2 == $bonus_option_idx) {
            if (0 < $retChargeTypeData[0][$charge_money_key]) {
                $ch_point = $retChargeTypeData[0][$charge_money_key];
                $const_value = $retChargeTypeData[0][$charge_money_key];
            } else {
                $ch_point = ($retChargeTypeData[0][$charge_per_key] * $set_money) / 100;
                $const_value = $retChargeTypeData[0][$charge_per_key];
            }

            if ($retChargeTypeData[0][$charge_max_money_key] < $ch_point) {
                $ch_point = $retChargeTypeData[0][$charge_max_money_key];
            }
        }*/

        return [$ch_point, $ratio_value, $const_value];
    }

}

?>
