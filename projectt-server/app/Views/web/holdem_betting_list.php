<?php
    use App\Util\DateTimeUtil;
    use App\Util\StatusUtil;
?>
<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

<div class="title_wrap">
    <div class="title">베팅내역</div>
</div>

<?php
    $p_data['num_per_page'] = 10;
    $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    $p_data['start'] = ($page - 1) * $p_data['num_per_page'];

    $total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
    //$total_page = 0;
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($page / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지
    $default_link = "holdemBettingHistory?1=1";

    if ($block >= $total_block) {
        $last_page = $total_page;
    }
?>
<input type="hidden" id="clickItemNum" value="<?= $clickItemNum?>">
<div class="contents_wrap">
    <div class="contents_box">
        <?= view('/web/common/betting_history_snb') ?>
        <div class="con_box20">
            <div class="scroll_h_wrap">
                <div class="scroll_h_inner">
                    <div class="bet_history_tr">
                        <div class="bet_history_title title3">일자</div>
                        <div class="bet_history_title title3 mini_none">회차</div>
                        <div class="bet_history_title title3">베팅금액</div>
                        <div class="bet_history_title title3">당첨금</div>
                        <div class="bet_history_title title3 mini_none">수수료</div>
                        <div class="bet_history_title title3">결과</div>
                    </div>

                    <?php if(count($betList) > 0) : ?>

                    <?php
                        $betStatus = '대기';
                        //$betStatusColor = 'sports_division1';
                        $betStatusColor = 'sports_division1';
                    ?>
                    
                        <?php foreach ($betList as $key => $row) :
                                if($row['WIN_MONEY'] > 0){
                                        $betStatus = '적중';
                                        $betStatusColor = 'sports_division2';
                                    }else{
                                        $betStatus = '낙첨';
                                        $betStatusColor = 'sports_division1';
                                    }
                        ?>

                        <!-- 그룹1 -->
                        <div class="bet_history_tr">
                            <div class="bet_history_td td3"><?=$row['REG_DTM']?></div>
                            <div class="bet_history_td td3 mini_none"><?=$row['GAME_NUM']?> 회</div>
                            <div class="bet_history_td td3"><?=number_format($row['BET_MONEY'])?> 원</div>
                            <div class="bet_history_td td3"><span class="font06"><?=number_format($row['WIN_MONEY'])?> 원</span></div>
                            <div class="bet_history_td td3 mini_none"><?=number_format(floor($row['FEE']))?> 원</div>
                            <div class="bet_history_td td3"><span class="<?=$betStatusColor ?>"><?=$betStatus?></span></div>
                        </div>
                        <!-- 그룹1끝 -->
        
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div><!-- .scroll_h_inner-->
            </div><!-- .scroll_h_wrap -->

            <div class="con_box10">
                <?php include('common/page_num.php'); ?>
            </div>
        </div>
        
    </div><!-- contents_box -->
</div><!-- contents_wrap -->

<?= view('/web/common/footer_wrap') ?>

<!-- top버튼 -->
<!-- <a href="#myAnchor" class="go-top">▲</a> -->
<script type="text/javascript" src="/assets_w/js/sub_05.js?v=<?php echo date("YmdHis"); ?>"></script>
<script type="text/javascript">
    $(function(){   
    });
</script>
</body>
</html>