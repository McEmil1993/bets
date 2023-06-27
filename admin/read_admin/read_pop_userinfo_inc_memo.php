<div class="tline">
    <table class="table_noline">
        <tr>
            <td style="text-align:left;background-color:#6F6F6F;color:#fff">&nbsp;&nbsp;메모현황</td>
            <td style="text-align:right;background-color:#6F6F6F;color:#fff">
                <a href="javascript:;" class="btn h25 btn_gray" onClick="popupWinPost('/member_w/pop_memo_write.php', 'popmemo', 725, 1000, 'memo', '<?= $db_m_idx ?>');">메 모</a>
                &nbsp;&nbsp;
            </td>
        </tr>
    </table>
    <table class="mlist">
        <tr class="">
            <?php if ($db_memo_cnt > 0) { ?> 

                <?php
                foreach ($db_data_memo as $key => $memo) {
                    switch ($memo['m_type']) {
                        case 1: $memo_type_str = "일반메모";
                            $font_color = '';
                            break;
                        case 2: $memo_type_str = "정보변경";
                            $font_color = 'color:#0036FD';
                            break;
                        case 3: $memo_type_str = "보안주시";
                            $font_color = 'color:#FD0C00';
                            break;
                        default: $memo_type_str = "unknow";
                            $font_color = '';
                            break;
                    }
                    ?>
        
                    <tr><td style="width:100px;<?= $font_color ?>"><?= $memo_type_str ?></td>
                    <td style="text-align:left;<?= $font_color ?>"><?= $memo['content'] ?></td>
                    <td style="width:150px;<?= $font_color ?>"><?= $memo['reg_time'] ?></td></tr>

    <?php } ?>
<?php } else { ?>
                <td colspan="3">데이터가 없습니다.</td>
<?php } ?>
        </tr>
    </table>
</div>
