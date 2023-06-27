<?php

/** @noinspection ALL */
include_once(_LIBPATH . '/class_GameStatusUtil.php');
include_once(_LIBPATH . '/class_Code.php');

class GameCodeUser {

    public static function doSumChExCalc(&$stats_day, $db_dataArr, &$tot_ch_val, &$tot_ex_val) {
       
        //if(0 == count($db_dataArr)) return;
        
        $convert_arr = [];
        
        foreach ($db_dataArr as $row) {
            $str_dt = str_replace('-', '', $row['up_dt']);

            if ($row['stype'] == 'ch') {
                $convert_arr[$str_dt]['ch_val'] = $row['s_money'];
                $convert_arr[$str_dt]['ch_user_cnt'] = $row['user_cnt'];
            } elseif ($row['stype'] == 'ex') {
                $convert_arr[$str_dt]['ex_val'] = $row['s_money'];
            }
        }
        //CommonUtil::logWrite("GameCodeUser doSumChExCalc convert_arr : " . json_encode($convert_arr), "info");

        foreach ($convert_arr as $key => $row) { // The key value in that loop is the date
            $str_dt = $key;

            //CommonUtil::logWrite("GameCodeUser doSumChExCalc convert_arr roof idx : " . $dis_idx, "info");

            $ch_val = true === isset($row['ch_val']) ? $row['ch_val'] : 0;
            $ex_val = true === isset($row['ex_val']) ? $row['ex_val'] : 0;
            $tot_ch_val += $ch_val;
            $tot_ex_val += $ex_val;

            GameCode::setStatsDay('ch_val_user', $str_dt, $stats_day, $ch_val);

            GameCode::setStatsDay('ex_val_user', $str_dt, $stats_day, $ex_val);
            
            GameCode::setStatsDay('charge_user_cnt', $str_dt, $stats_day, $row['ch_user_cnt']);

            //CommonUtil::logWrite("GameCodeUser doSumChExCalc roof end : " . json_encode($stats_day), "info");
        }
        
        //CommonUtil::logWrite("GameCodeUser doSumChExCalc stats_day : " . json_encode($stats_day), "info");
    }

}

?>
