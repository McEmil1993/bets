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
    $imageBasePath = config(App::class)->imageUrl.'/'.config(App::class)->imagePath;
    
    $p_data['num_per_page'] = 10;
    $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    $p_data['start'] = ($page - 1) * $p_data['num_per_page'];

    $total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
    //$total_page = 0;
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($page / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지
    $default_link = "hashBettingHistory?clickItemNum=$clickItemNum";

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
                        <div class="bet_history_title title3 mini_none">게임</div>
                        <div class="bet_history_title title3">베팅금액</div>
                        <div class="bet_history_title title3">당첨금</div>
                        <div class="bet_history_title title3">결과</div>
                    </div>

                    <?php if(count($betList) > 0) : ?>

                    <?php
                        $betStatus = '대기';
                        $betStatusColor = 'sports_division1';
                    ?>
                    
                        <?php foreach ($betList as $key => $row) : ?>

                        <?php
                            switch($row['TYPE']){
                                case 'W':
                                    $betStatus = '적중';
                                    $betStatusColor = 'sports_division2';
                                    break;
                                case 'L':
                                    $betStatus = '미적중';
                                    $betStatusColor = 'sports_division1';
                                    break;
                                case 'C':
                                    $betStatus = '취소';
                                    $betStatusColor = 'sports_division1';
                                    break;
                                case 'I':
                                    $betStatus = '인게임보너스';
                                    $betStatusColor = 'sports_division1';
                                    break;
                                case 'P':
                                    $betStatus = '프로모션보너스';
                                    $betStatusColor = 'sports_division1';
                                    break;
                                case 'J':
                                    $betStatus = '잭팟보너스';
                                    $betStatusColor = 'sports_division1';
                                    break;
                            }
                            
                            $prdList = array('B'=>'바카라', 'R'=>'룰렛', 'H'=>'하이로우');
                        ?>

                        <!-- 그룹1 -->
                        <div class="bet_history_tr">
                            <div class="bet_history_td td4"><?=$row['REG_DTM']?></div>
                            <div class="bet_history_td td4 mini_none"><?=isset($prdList[$row['PRD_TYPE']])?$prdList[$row['PRD_TYPE']]:'기타'?></div>
                            <div class="bet_history_td td4"><?=number_format($row['BET_MNY'])?></div>
                            <div class="bet_history_td td4"><span class="font06"><?=number_format($row['BET_MNY'] + $row['RSLT_MNY'])?> 원</span></div>
                            <div class="bet_history_td td4"><span class="<?=$betStatusColor ?>"><?=$betStatus?></span></div>
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
        let htmls = ``
        let htmls2 = ``

        if($(document).width() < 500){
            $(".bet_history_title").css("width","25%");
            if($(".bet_history_tr").length > 1){
            htmls = ``
            }else{
                htmls2 = `
                    <div class="con_box20 bet_history_wrap2">
                        <div class="bet_history_box">
                            <div class="bet_history_top" style="display:block; text-align: center;">기록이 없습니다.</div>
                        </div>
                    </div>
                    `
                $(".tab_wrap").append(htmls2)
                $(".tab_livecasino").remove()
                $(".scroll_h_wrap").remove()
            }
        }
    });
</script>
</body>
</html>