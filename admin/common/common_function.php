<?php
if (!function_exists('Fn_BoardLinkPagination')) {
    function Fn_BoardLinkPagination($page, $pageBlock, $tRecord, $tPage, $link = '', $params = ''){
        $str = '';

        $tblock   = (int)(($tPage-1) / $pageBlock) + 1;
        $block    = (int)(($page-1) / $pageBlock) + 1;

        if ($tRecord == 0){
            $first_ = 1;
            $last_  = 0;
        } else {
            $first_ = $pageBlock *($block -1) + 1;
            $last_  = $pageBlock * $block;
        }

        if($tblock <= $block) $last_ = $tPage;

        $str .= "<div class='page'>";

        if ($page > 1){
            $str .= "<a href='".$link."?page=".($page - 1).$params."'><i class='mte i_navigate_before vam'></i></a>";
        }

        for($i = $first_; $i <= $last_; $i++){
            if ($page == $i){
                $str .= "<a href='' class='on'>".$i."</a>";
            } else {
                $str .= "<a href='".$link."?page=".$i.$params."'>".$i."</a>";
            }
        }

        if ($page < $tPage){
            $str .= "<a href='".$link."?page=".($page + 1).$params."'><i class='mte i_navigate_next vam'></i></a>";
        }

        $str .= "</div>";

        return $str;

    }
    
    function getBonusName($name){
        $bonusName = '';
        switch ($name) {
            case 'charge_event_per':
                $bonusName = '(돌발)';
                break;
            case 'reg_first_charge':
                $bonusName = '(가입첫충)';
                break;
            case 'charge_first_per':
                $bonusName = '(첫충)';
                break;
            case 'charge_per':
                $bonusName = '(매충)';
                break;
            default:
                break;
        }
        return $bonusName;
    }
    function getBonusOptionName($bonus_option_idx){
        $bonusName = '';
        switch ($bonus_option_idx) {
            case -1:
                $bonusName = '적용안함';
                break;
            case 0:
                $bonusName = '기본 보너스';
                break;
            case 1:
                $bonusName = '3% 카지노전용';
                break;
            case 2:
                $bonusName = '5% 보너스';
                break;
            case 3:
                $bonusName = '15% 보너스';
                break;
            case 4:
                $bonusName = '돌발충전 20%';
                break;
            case 5:
                $bonusName = '보너스 옵션 5';
                break;
            default:
                break;
        }
        return $bonusName;
    }
}
?>