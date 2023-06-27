<?php

/** @noinspection ALL */
include_once(_LIBPATH . '/class_GameStatusUtil.php');

class CommonStats {

    public static function setDisplayData($p_data) {
        $displayData = array();
        $displayData['ob_ch_sum_change']                   = $displayData['ob_ex_sum_change']                  = $displayData['ob_diff_sum_change']              = $displayData['ob_diff_per_sum_change'] 
        = $displayData['ob_pre_bet_sum_s_change']          = $displayData['ob_pre_take_sum_s_change']          = $displayData['ob_pre_sum_s_change']             = $displayData['ob_pre_bet_sum_d_change']
        = $displayData['ob_pre_take_sum_d_change']         = $displayData['ob_pre_sum_d_change']               = $displayData['ob_real_bet_sum_s_change']        = $displayData['ob_real_take_sum_s_change'] 
        = $displayData['ob_real_sum_s_change']             = $displayData['ob_real_bet_sum_d_change']          = $displayData['ob_real_take_sum_d_change']       = $displayData['ob_real_sum_d_change']
                // classic
        = $displayData['ob_classic_bet_sum_change']          = $displayData['ob_classic_take_sum_change']       = $displayData['ob_classic_sum_change']    
                
                
        = $displayData['ob_mini_bet_sum_d_change']         = $displayData['ob_mini_take_sum_d_change']         = $displayData['ob_mini_sum_d_change']            = $displayData['ob_total_casino_bet_money_change'] 
        = $displayData['ob_total_casino_win_money_change'] = $displayData['ob_total_casino_lose_money_change'] = $displayData['ob_total_slot_bet_money_change']  = $displayData['ob_total_slot_win_money_change']
        = $displayData['ob_total_slot_lose_money_change']  = $displayData['ob_total_espt_bet_money_change']    = $displayData['ob_total_espt_win_money_change']  = $displayData['ob_total_espt_lose_money_change']
        = $displayData['ob_total_hash_bet_money_change']   = $displayData['ob_total_hash_win_money_change']    = $displayData['ob_total_hash_lose_money_change'] = "";
        $displayData['ob_ch_sum_color'] = "<font color='#444'>충전</font>";
        $displayData['ob_ex_sum_color'] = "<font color='#444'>환전</font>";
        $displayData['ob_diff_sum_color'] = "<font color='#444'>차액</font>";
        $displayData['ob_diff_per_sum_color'] = "<font color='#444'>수익률</font>";
        $displayData['ob_pre_bet_sum_s_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_pre_take_sum_s_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_pre_sum_s_color'] = "<font color='#444'>차액</font>";
        $displayData['ob_pre_bet_sum_d_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_pre_take_sum_d_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_pre_sum_d_color'] = "<font color='#444'>차액</font>";
        $displayData['ob_real_bet_sum_s_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_real_take_sum_s_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_real_sum_s_color'] = "<font color='#444'>차액</font>";
        $displayData['ob_real_bet_sum_d_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_real_take_sum_d_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_real_sum_d_color'] = "<font color='#444'>차액</font>";
        
        
        $displayData['ob_classic_bet_sum_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_classic_take_sum_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_classic_sum_color'] = "<font color='#444'>차액</font>";
        
        
        
        
        $displayData['ob_mini_bet_sum_d_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_mini_take_sum_d_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_mini_sum_d_color'] = "<font color='#444'>차액</font>";

        $displayData['ob_total_casino_bet_money_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_total_casino_win_money_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_total_casino_lose_money_color'] = "<font color='#444'>차액</font>";

        $displayData['ob_total_slot_bet_money_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_total_slot_win_money_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_total_slot_lose_money_color'] = "<font color='#444'>차액</font>";

        $displayData['ob_total_espt_bet_money_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_total_espt_win_money_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_total_espt_lose_money_color'] = "<font color='#444'>차액</font>";

        $displayData['ob_total_hash_bet_money_color'] = "<font color='#444'>베팅</font>";
        $displayData['ob_total_hash_win_money_color'] = "<font color='#444'>당첨</font>";
        $displayData['ob_total_hash_lose_money_color'] = "<font color='#444'>차액</font>";

        switch ($p_data['s_ob']) {
            case "ch_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY ch_sum DESC ";
                    $displayData['ob_ch_sum_color'] = "<font color='#0021FD'>충전</font>";
                } else {
                    $displayData['ob_ch_sum_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY ch_sum ";
                    $displayData['ob_ch_sum_color'] = "<font color='#FD0000'>충전</font>";
                }
                break;
            case "ex_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY ex_sum DESC";
                    $displayData['ob_ex_sum_color'] = "<font color='#0021FD'>환전</font>";
                } else {
                    $displayData['ob_ex_sum_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY ex_sum ";
                    $displayData['ob_ex_sum_color'] = "<font color='#FD0000'>환전</font>";
                }
                break;
            case "diff_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY diff_sum DESC ";
                    $displayData['ob_diff_sum_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_diff_sum_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY diff_sum ";
                    $displayData['ob_diff_sum_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;
            case "diff_per_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY diff_per_sum DESC ";
                    $displayData['ob_diff_per_sum_color'] = "<font color='#0021FD'>수익률</font>";
                } else {
                    $displayData['ob_diff_per_sum_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY diff_per_sum ";
                    $displayData['ob_diff_per_sum_color'] = "<font color='#FD0000'>수익률</font>";
                }
                break;
            case "pre_bet_sum_s":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY pre_bet_sum_s DESC ";
                    $displayData['ob_pre_bet_sum_s_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_pre_bet_sum_s_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY pre_bet_sum_s ";
                    $displayData['ob_pre_bet_sum_s_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "pre_take_sum_s":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY pre_take_sum_s DESC";
                    $displayData['ob_pre_take_sum_s_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_pre_take_sum_s_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY pre_take_sum_s ";
                    $displayData['ob_pre_take_sum_s_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;
            case "pre_sum_s":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY pre_sum_s DESC ";
                    $displayData['ob_pre_sum_s_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_pre_sum_s_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY pre_sum_s ";
                    $displayData['ob_pre_sum_s_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;
            case "pre_bet_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY pre_bet_sum_d DESC ";
                    $displayData['ob_pre_bet_sum_d_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_pre_bet_sum_d_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY pre_bet_sum_d ";
                    $displayData['ob_pre_bet_sum_d_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "pre_take_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY pre_take_sum_d DESC";
                    $displayData['ob_pre_take_sum_d_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_pre_take_sum_d_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY pre_take_sum_d ";
                    $displayData['ob_pre_take_sum_d_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;
            case "pre_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY pre_sum_d DESC ";
                    $displayData['ob_pre_sum_d_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_pre_sum_d_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY pre_sum_d ";
                    $displayData['ob_pre_sum_d_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;
            case "real_bet_sum_s":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY real_bet_sum_s DESC ";
                    $displayData['ob_real_bet_sum_s_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_real_bet_sum_s_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY real_bet_sum_s ";
                    $displayData['ob_real_bet_sum_s_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;
            case "real_take_sum_s":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY real_take_sum_s DESC";
                    $displayData['ob_real_take_sum_s_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_real_take_sum_s_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY real_take_sum_s ";
                    $displayData['ob_real_take_sum_s_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;
            case "real_sum_s":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY real_sum_s DESC ";
                    $displayData['ob_real_sum_s_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_real_sum_s_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY real_sum_s ";
                    $displayData['ob_real_sum_s_color'] = "<font color='#FD0000'>차액</font>";
                }
            case "real_bet_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY real_bet_sum_d DESC ";
                    $displayData['ob_real_bet_sum_d_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_real_bet_sum_d'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY real_bet_sum_d ";
                    $displayData['ob_real_bet_sum_d_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "real_take_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY real_take_sum_d DESC";
                    $displayData['ob_real_take_sum_s_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_real_take_sum_d'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY real_take_sum_d ";
                    $displayData['ob_real_take_sum_d_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;
            case "real_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY real_sum_d DESC ";
                    $displayData['ob_real_sum_d_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_real_sum_d'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY real_sum_d ";
                    $displayData['ob_real_sum_d_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;
                
            // classic     
            case "classic_bet_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_classic_bet_money DESC ";
                    $displayData['ob_classic_bet_sum_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_classic_bet_sum'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_classic_bet_money ";
                    $displayData['ob_classic_bet_sum_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;
                
            case "classic_take_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_classic_win_money DESC";
                    $displayData['ob_classic_take_sum_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_classic_take_sum'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_classic_win_money ";
                    $displayData['ob_classic_take_sum_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;
            case "classic_sum":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_classic_lose_money DESC ";
                    $displayData['ob_classic_sum_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_classic_sum'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_classic_lose_money ";
                    $displayData['ob_classic_sum_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;     
             // classic end   
                
            case "mini_bet_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY mini_bet_sum_d DESC ";
                    $displayData['ob_mini_bet_sum_d_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_mini_bet_sum_d_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY mini_bet_sum_d ";
                    $displayData['ob_mini_bet_sum_d_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;
            case "mini_take_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY mini_take_sum_d DESC";
                    $displayData['ob_mini_take_sum_d_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_mini_take_sum_d_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY mini_take_sum_d ";
                    $displayData['ob_mini_take_sum_d_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;
            case "mini_sum_d":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY mini_sum_d DESC ";
                    $displayData['ob_mini_sum_d_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_mini_sum_d_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY mini_sum_d ";
                    $displayData['ob_mini_sum_d_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;
            case "total_casino_bet_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_casino_bet_money DESC ";
                    $displayData['ob_total_casino_bet_money_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_total_casino_bet_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_casino_bet_money ";
                    $displayData['ob_total_casino_bet_money_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "total_casino_win_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_casino_win_money DESC ";
                    $displayData['ob_total_casino_win_money_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_total_casino_win_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_casino_win_money ";
                    $displayData['ob_total_casino_win_money_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;

            case "total_casino_lose_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_casino_lose_money DESC ";
                    $displayData['ob_total_casino_lose_money_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_total_casino_lose_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_casino_lose_money ";
                    $displayData['ob_total_casino_lose_money_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;

            case "total_slot_bet_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_slot_bet_money DESC ";
                    $displayData['ob_total_slot_bet_money_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_total_slot_bet_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_slot_bet_money ";
                    $displayData['ob_total_slot_bet_money_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "total_slot_win_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_slot_win_money DESC ";
                    $displayData['ob_total_slot_win_money_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_total_slot_win_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_slot_win_money ";
                    $displayData['ob_total_slot_win_money_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;

            case "total_slot_lose_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_slot_lose_money DESC ";
                    $displayData['ob_total_slot_lose_money_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_total_slot_lose_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_slot_lose_money ";
                    $displayData['ob_total_slot_lose_money_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;

            // 이스포츠 / 키론 / 해시
            case "total_espt_bet_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_espt_bet_money DESC ";
                    $displayData['ob_total_espt_bet_money_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_total_espt_bet_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_espt_bet_money ";
                    $displayData['ob_total_espt_bet_money_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "total_espt_win_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_espt_win_money DESC ";
                    $displayData['ob_total_espt_win_money_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_total_espt_win_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_espt_win_money ";
                    $displayData['ob_total_espt_win_money_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;

            case "total_espt_lose_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_espt_lose_money DESC ";
                    $displayData['ob_total_espt_lose_money_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_total_espt_lose_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_espt_lose_money ";
                    $displayData['ob_total_espt_lose_money_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;
            case "total_hash_bet_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_hash_bet_money DESC ";
                    $displayData['ob_total_hash_bet_money_color'] = "<font color='#0021FD'>베팅</font>";
                } else {
                    $displayData['ob_total_hash_bet_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_hash_bet_money ";
                    $displayData['ob_total_hash_bet_money_color'] = "<font color='#FD0000'>베팅</font>";
                }
                break;

            case "total_hash_win_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_hash_win_money DESC ";
                    $displayData['ob_total_hash_win_money_color'] = "<font color='#0021FD'>당첨</font>";
                } else {
                    $displayData['ob_total_hash_win_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_hash_win_money ";
                    $displayData['ob_total_hash_win_money_color'] = "<font color='#FD0000'>당첨</font>";
                }
                break;

            case "total_hash_lose_money":
                if ($p_data['s_ob_type'] == 'desc') {
                    $displayData['sql_orderby'] = " ORDER BY total_hash_lose_money DESC ";
                    $displayData['ob_total_hash_lose_money_color'] = "<font color='#0021FD'>차액</font>";
                } else {
                    $displayData['ob_total_hash_lose_money_change'] = "desc";
                    $displayData['sql_orderby'] = " ORDER BY total_hash_lose_money ";
                    $displayData['ob_total_hash_lose_money_color'] = "<font color='#FD0000'>차액</font>";
                }
                break;

            default:
                $displayData['sql_orderby'] = " ORDER BY idx DESC ";
                break;
        }
        
        return $displayData;
    }
}

?>
