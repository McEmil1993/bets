<?= view('/web/common/header') ?>
<?php
use App\Util\DateTimeUtil;
use App\Util\StatusUtil; ?>
<div id="wrap">
    <?= view('/web/common/header_wrap') ?>
    <div class="title">베팅내역</div>
    <?php
    $p_data['num_per_page'] = 10;
    $p_data['page_per_block'] = 10; //_B_BLOCK_COUNT;
    $p_data['start'] = ($page - 1) * $p_data['num_per_page'];

    $total_page = ceil($totalCnt / $p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
    $block = ceil($page / $p_data['page_per_block']); // 현재 블럭
    $first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
    $last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지
    if ($block >= $total_block)
        $last_page = $total_page;
    
    $default_link = "esportsBettingHistory?prd_type=e&prd_id=101";
    ?>
    <div id="contents_wrap">
        <div class="contents_box">
            <div class="sports_list_title">
                <div class="sports_list_title3">
                    <ul>
                        <li><a href="javascript:searchBtnClick(2);"><img name="itemImg" id="itemImg2" src="/assets_w/images/mini_c_icon07.png" width="18">&nbsp; 스포츠</span></a></li>
                        <li><a href="javascript:searchBtnClick(1);"><img name="itemImg" id="itemImg1" src="/assets_w/images/mini_c_icon07.png" width="18">&nbsp; LIVE스포츠</span></a></li>
                        <li><a href="javascript:fnLoadingMove('/web/casinoBettingHistory?prd_type=C&prd_id=5&clickItemNum=4')"><img id="itemImg4" name="itemImg" src="/assets_w/images/mini_c_icon07.png" width="18">&nbsp; 카지노</span></a></li>
                        <li><a href="javascript:searchBtnClick(3);"><img id="itemImg3" src="/assets_w/images/mini_c_icon07.png" width="18">&nbsp; 미니게임</span></a></li>
                        <li><a href="javascript:fnLoadingMove('/web/hashBettingHistory?clickItemNum=6')"><img id="itemImg6" name="itemImg" src="/assets_w/images/mini_c_icon07.png" width="18">&nbsp; 해쉬게임</span></a></li>
                        <li><a href="javascript:fnLoadingMove('/web/casinoBettingHistory?prd_type=S&prd_id=201&clickItemNum=5')"><img id="itemImg5" name="itemImg" src="/assets_w/images/mini_c_icon07.png" width="18">&nbsp; 슬롯게임</span></a></li>
                        <li><a href="javascript:fnLoadingMove('/web/esportsBettingHistory?prd_type=e&prd_id=101')"><img id="itemImg7" name="itemImg" src="/assets_w/images/mini_c_icon07on.png" width="18">&nbsp; E-스포츠</span></a></li>
                    </ul>
                </div>
            </div>
                <div class="bet_title_wrap">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="bet_title1">일자</td>
                            <td class="bet_title2">게임</td>
                            <td class="bet_title2">베팅금액</td>
                            <td class="bet_title2">당첨금</td>
                            <td class="bet_title7_big">결과</td>
                        </tr>
                    </table>
                </div>
            <?php
                if(count($betList) > 0){
                    $betStatus = '대기';
                    $betStatusColor = 'bg_gray';
                    foreach ($betList as $key => $row) {
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
            ?>
                <div>
                    <li class="bet_list1_wrap">
                        <!-- 그룹1 -->
                        <a href="#">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="bet1"><?=$row['REG_DTM']?></td>
                                    <td class="bet2"><?=isset($prdList[$row['PRD_ID']])?$prdList[$row['PRD_ID']]:'기타'?></td>
                                    <td class="bet2"><?=number_format($row['BET_MNY'])?></td>
                                    <td class="bet2"><?=number_format($row['BET_MNY'] + $row['RSLT_MNY'])?></td>
                                    <td class="bet7_big"><span class="<?=$betStatusColor?>"><?=$betStatus?></span></td>
                                </tr>
                            </table>
                        </a>
                    </li><!-- 그룹1끝 -->
                </div>
                <?php }?>
                <div class="con_box10">
                    <?php include('common/page_num.php'); ?>
                </div>
            <?php }?>
            </div>
        </div>
    </div><!-- contents_wrap -->
    <?= view('/web/common/footer_wrap') ?>
</div><!-- wrap -->

<!-- top버튼 -->
<a href="#myAnchor" class="go-top">▲</a>
<script type="text/javascript" src="/assets_w/js/sub_05.js?v=<?php echo date("YmdHis"); ?>"></script>
<script type="text/javascript">
$(document).ready(function () {
});
</script>
</body>

</html>