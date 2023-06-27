<div class="con_box20">
    <div class="tab_wrap" >
        <ul>
            <li class="tab snb2">
                <a href="javascript:searchBtnClick(2);">
                    <span><img src="/assets_w/images/icon_game02.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>스포츠</span>
                </a>
            </li>
            <li class="tab snb1">
                <a href="javascript:searchBtnClick(1);">
                    <span><img src="/assets_w/images/icon_game10.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>LIVE 스포츠</span>
                </a>
            </li>
            <li class="tab snb4">
                <a href="javascript:searchBtnClick(4);">
                    <span><img src="/assets_w/images/mini_icon03.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>카지노</span>
                </a>
            </li>
            <li class="tab snb3">
                <a href="javascript:searchBtnClick(3);">
                    <span><img src="/assets_w/images/mini_icon04.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>미니게임</span>
                </a>
            </li>
            <li class="tab snb6">
                <a href="javascript:searchBtnClick(6);">
                    <span><img src="/assets_w/images/mini_icon02.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>가상게임</span>
                </a>
            </li>
            <li class="tab snb9">
                <a href="javascript:searchBtnClick(9);">
                    <span><img src="/assets_w/images/mini_icon05.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>해쉬게임</span>
                </a>
            </li>
            <li class="tab snb5">
                <a href="javascript:searchBtnClick(5);">
                    <span><img src="/assets_w/images/mini_icon01.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>슬롯게임</span>
                </a>
            </li>
            <?php if('ON' == config(App::class)->IS_ESPORTS){ ?>
            <li class="tab snb7">
                <a href="/web/esportsBettingHistory?prd_type=e&prd_id=101">
                    <span><img src="/assets_w/images/icon_game07.png" width="24" alt="icon">&nbsp;&nbsp;</span>
                    <span>E-스포츠</span>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
</div><!-- .con_box20-->

<script>
    $(function(){
        let clickItemNum = `<?= $clickItemNum?>`;
//console.log(clickItemNum);
        // snb active
        $(document).find(".tab_wrap ul li").removeClass("tabon");
        if( clickItemNum == 0 ){
            $(document).find(".tab_wrap ul li").eq(0).addClass("tabon");
        } else {
            $(document).find(".tab_wrap").find(`.snb${clickItemNum}`).addClass("tabon");
        }
        const betting_history_check = () => {
            if(location.href == "http://noble.com:8080/web/betting_history"){
                searchBtnClick(2);
            }
        }
        betting_history_check()
    });
</script>