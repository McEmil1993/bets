<div class="footer_wrap">
	<div class="footer_box">
		<div class="footer_customer">
            <div id="domain_mobile">
                <a target="_blank" href="https://xn--tl3bs23a.com/"><img src="/images/bets_banner_mobile.jpg" width="95%"></a>
            </div>
			<ul>
				<li><img src="/assets_w/images/sns_telegram.png"><span class="footer_customer_font">@BETSKRCS</span></li>
			</ul>
		</div>
    	<img src="/assets_w/images/f_partners.png"><br>COPYRIGHT ⓒ 2006~2022 BETS All RIGHT RESERVED.
    </div>
</div><!-- footer -->

<?php if(isset($_SESSION['session_key'])){ ?>

<a href="/web/apply?menu=c" class="footer_btn footer_btn1"><img src="/assets_w/images/footer_btn1.png"></a>
<a href="/web/exchange" class="footer_btn footer_btn2"><img src="/assets_w/images/footer_btn2.png"></a>
<?php } ?>
<div id="loadingCircle" class="loding_wrap">
    <div class="loading_logo"><img src="/assets_w/images/logo.png" alt="logo"></div>
    <div class="loding_circle"></div>
</div>
<script>
    if(location.pathname != '/web/sports'){
        //console.log('sessionStorage clear : ' + location.pathname);
        sessionStorage.removeItem('betSlip');
    }
</script>