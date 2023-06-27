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
    $default_link = "casinoBettingHistory?prd_type=$prd_type&prd_id=$prd_id&clickItemNum=$clickItemNum";

    if ($block >= $total_block) {
        $last_page = $total_page;
    }
?>

<input type="hidden" id="clickItemNum" value="<?= $clickItemNum?>">

<div class="contents_wrap">
    <div class="contents_box">
        <?= view('/web/common/betting_history_snb') ?>

        <?php if ($prd_type == 'C') : ?>
        <div class="tab_livecasino scroll_x">
                <!--
                1 에볼루션
                2 빅 게이밍
                5 아시아 게이밍
                6 드림 게이밍
                9 섹시 게이밍
                10 프라그마틱 플레이
                12 플레이테크
                15 TV벳
                16 로얄카지노게이밍
                17 에즈기
                18 보타
                19 스카이윈드
                20 UIG카지노
                -->
            <ul>
                <li class="ssnb1">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=1&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live01.png" width="56"></span>
                        <span>에볼루션게이밍</span>
                    </a>
                </li>
                <li class="ssnb2">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=2&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live02.png" width="56"></span>
                        <span>빅게이밍</span>
                    </a>
                </li>
                <li class="ssnb5">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=5&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live03.png" width="56"></span>
                        <span>아시아게이밍</span>
                    </a>
                </li>
                <li class="ssnb6">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=6&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live04.png" width="56"></span>
                        <span>드림게이밍</span>
                    </a>
                </li>
                <li class="ssnb9">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=9&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live05.png" width="56"></span>
                        <span>섹시게이밍</span>
                    </a>
                </li>
                <li class="ssnb16">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=16&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live09.png" width="56"></span>
                        <span>로얄카지노</span>                                
                    </a>
                </li>
                <li class="ssnb17">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=17&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live10.png" width="56"></span>
                        <span>에즈기</span>                                
                    </a>
                </li>
                <li class="ssnb18">
                    <a href="/web/casinoBettingHistory?prd_type=C&prd_id=18&clickItemNum=<?= $clickItemNum?>">
                        <span><img src="/assets_w/images/casino_live11.png" width="56"></span>
                        <span>보타</span>                            
                    </a>
                </li>
            </ul>
        </div><!-- .tab_livecasino -->
        <?php endif; ?>

        
        <div class="con_box20">
            <div class="scroll_h_wrap">
                <div class="scroll_h_inner">
                    <div class="bet_history_tr">
                        <div class="bet_history_title title3">일자</div>
                        <div class="bet_history_title title3">게임사</div>
                        <div class="bet_history_title title3">종목</div>
                        <div class="bet_history_title title3">베팅금액</div>
                        <div class="bet_history_title title3">당첨금</div>
                        <div class="bet_history_title title3">적중여부</div>
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
                        ?>

                        <!-- 그룹1 -->
                        <div class="bet_history_tr">
                            <div class="bet_history_td td3"><?=$row['REG_DTM']?></div>
                            <div class="bet_history_td td3"><?=isset($prdList[$row['PRD_ID']])?$prdList[$row['PRD_ID']]:'기타'?></div>
                            <div class="bet_history_td td3"><?=isset($gameList[$row['GAME_ID']])?$gameList[$row['GAME_ID']]:'기타'?></div>
                            <div class="bet_history_td td3"><?=number_format($row['BET_MNY'])?></div>
                            <div class="bet_history_td td3"><?=number_format($row['BET_MNY'] + $row['RSLT_MNY'])?></div>
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
    let del_click = false; // 쪽지 삭제버튼 클릭시 아코디언 클릭 이벤트 무시용도
    (function ($) {
        // $('.popup_accordion > li:eq(0) a').addClass('active').next().slideDown();
        $('.popup_accordion a').click(function (j) {
            // 글삭제 클릭시 이벤트 무시
            if (true == del_click) {
                del_click = false;
                return;
            }

            // 선택한 글을 구한다.
            var dropDown = $(this).closest('li').find('div');

            // 선택한 글을 제외한 나머지 내용을 닫는다.
            $(this).closest('.popup_accordion').find('div').not(dropDown).slideUp();
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            } else {
                // 이전에 펼쳐진 태그에 active 클레스를 제거하고, 새로 펼친 태그에 속성을 추가한다.
                $(this).closest('.popup_accordion').find('a.active').removeClass('active');
                $(this).addClass('active');
            }

            dropDown.stop(false, true).slideToggle();

            j.preventDefault();
        });
    })(jQuery);
   
    let delete_idx = 0;
    let check_auth_code = false;

    let option = $("#select_bet_group option:selected").val();
    






    $(function () {
        $('.tabs').children().removeClass('active');
        $('.tabs').children().eq(1).addClass('active');

        // 검색버튼 눌러서 날짜 정보가 넘어왔을시 날짜를 셋팅한다.
            <?php if (isset($_GET['betFromDate'])) : ?>
            $('#from_date').val('<?= $_GET['betFromDate'] ?>');
            <?php else : ?>
            $('#from_date').val(getFormatDate(new Date()));
            <?php endif; ?>

            <?php if (isset($_GET['betToDate'])) : ?>
            $('#to_date').val('<?= $_GET['betToDate'] ?>');
            <?php else : ?>
            $('#to_date').val(getFormatDate(new Date()));
            <?php endif; ?>

        var dateFormat = "yy-mm-dd",
                from = $("#from_date")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3
                })
                .on("change", function () {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#to_date").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3
        })
                .on("change", function () {
                    from.datepicker("option", "maxDate", getDate(this));
                });

        function getDate(element) {
            console.log(dateFormat);
            console.log(element.value);
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }
            return date;
        }
    });






    $(function(){

        // tab_livecasino active
        let prd_id = <?= $_REQUEST['prd_id'] ?>;
        const urlParams = new URL(location.href).searchParams;
        const prdIdNum = urlParams.get('prd_id');
        $(document).find(`.tab_livecasino .ssnb${prdIdNum}`).addClass("active");
        
    });
</script>
</body>
</html>