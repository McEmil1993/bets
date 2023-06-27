<div class="tab_bettingHistory scroll_x">
    <ul>
        <li class="snb1">
            <a href="javascript:searchBtnClick(1);">
                <span><img src="/assets_w/images/icon10.png" alt="icon"></span>
                <span>라이브스포츠</span>
            </a>
        </li>
        <li class="snb2">
            <a href="javascript:searchBtnClick(2);">
                <span><img src="/assets_w/images/icon02.png" alt="icon"></span>
                <span>스포츠</span>
            </a>
        </li>
        <li class="snb9">
            <a href="javascript:searchBtnClick(9);">
                <span><img src="/assets_w/images/icon_mini03.png" alt="icon"></span>
                <span>클래식스포츠</span>
            </a>
        </li>
        <li class="snb4">
            <a href="/web/casinoBettingHistory?prd_type=C&prd_id=1&clickItemNum=4">
                <span><img src="/assets_w/images/icon_mini06.png" alt="icon"></span>
                <span>라이브카지노</span>
            </a>
        </li>
        <li class="snb5">
            <a href="/web/casinoBettingHistory?prd_type=S&prd_id=201&clickItemNum=5">
                <span><img src="/assets_w/images/icon_mini05.png" alt="icon"></span>
                <span>슬롯머신</span>
            </a>
        </li>
        <li class="snb3">
            <a href="javascript:searchBtnClick(3);">
                <span><img src="/assets_w/images/icon_mini01.png" alt="icon"></span>
                <span>미니게임</span>
            </a>
        </li>
        <li class="snb6">
            <a href="javascript:searchBtnClick(6);">
                <span><img src="/assets_w/images/icon_mini02.png" alt="icon"></span>
                <span>가상게임</span>
            </a>
        </li>
        <li class="snb7">
            <a href="/web/hashBettingHistory?clickItemNum=7">
                <span><img src="/assets_w/images/icon09.png" alt="icon"></span>
                <span>해쉬게임</span>
            </a>
        </li>
        <?php if('ON' == config(App::class)->IS_HOLDEM){ ?>
        <li class="snb8">
            <a href="/web/holdemBettingHistory?clickItemNum=8">
                <span><img src="/assets_w/images/icon_mini04.png" alt="icon"></span>
                <span>홀덤</span>
            </a>
        </li>
        <?php } ?>

    </ul>
</div><!-- .tab_bettingHistory-->

<script>
    $(function(){
        let clickItemNum = `<?= $clickItemNum?>`;
//console.log(clickItemNum);
        // snb active
        $(document).find(".tab_bettingHistory ul li").removeClass("active");
        if( clickItemNum == 0 ){
            $(document).find(".tab_bettingHistory ul li").eq(0).addClass("active");
        } else {
            $(document).find(".tab_bettingHistory").find(`.snb${clickItemNum}`).addClass("active");
        }

    });
</script>