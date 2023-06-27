<?= view('/web/common/header') ?>
<link rel="stylesheet" href="/assets_w/css/jquery.mCustomScrollbar.css">
<script src="/assets_w/js/jquery.mCustomScrollbar.concat.min.js"></script>

<!-- <div id="wrap"> -->
	<?= view('/web/common/header_wrap') ?>
    <?php 
        // var_dump($locationGameList);
        // $imageBasePath = config(App::class)->imageUrl.'/'.config(App::class)->imagePath;
        // foreach ($sports as $key => $value) {
        //     $realTimeTotal[$value['id']] = $value['count'];
        // }
    ?>
    <!-- <script src="/assets_w/js/realtime_common_w.js?v=<?php echo date("YmdHis"); ?>"></script>
    <script src="/assets/js/realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script>
    <script src="/assets/js/sports_realtime_common.js?v=<?php echo date("YmdHis"); ?>"></script> -->


    <div class="sports__wrap">

        <div class="sports__list_wrap">
            <ul class="accordion_lnb">
                <!-- <li class="accordion_menu menu1">
                    <strong>
                        <a href="#">
                            <span>전체보기</span>
                            <i>100</i>
                        </a>
                    </strong>
                    <ul class="accordion_submenu">
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                        <li><a href="#">menu<i>100</i></a></li>
                    </ul>
                </li> -->
            </ul>
        </div><!-- .sports__list_wrap -->
        
        
        <div class="sports__contents_wrap">
            <div class="sports__contents-option">
                <select id="sports_key">
                    <option value="0">종목선택</option>
                </select>
                <select id="league_key">
                    <option value="0">리그선택</option>
                </select>
                <div class="menu_wrap">
                    <ul>
                        <li class="menu1"><strong><a href="#">전체</a></strong></li>
                        <li class="menu2"><strong><a href="#">축구</a></strong></li>
                        <li class="menu3"><strong><a href="#">농구</a></strong></li>
                        <li class="menu4"><strong><a href="#">야구</a></strong></li>
                        <li class="menu5"><strong><a href="#">배구</a></strong></li>
                        <li class="menu6"><strong><a href="#">하키</a></strong></li>
                        <li class="menu7"><strong><a href="#">e스포츠</a></strong></li>
                        <li class="menu9"><strong><a href="#">기타</a></strong></li>
                    </ul>
                </div>
            </div>
            <div class="sports__contents-wrap">


                <div class="sports__contents-item">


                    <div class="sports__contents-group">

                        <div class="sports__contents-group-ttl">
                            <div class="sport_league">
                                <span><img src="/assets_w/images/flag/flag001.png"></span>
                                <i class="line"></i>
                                <span><img src="/assets_w/images/icon02.png"></span>
                                <i class="line"></i>
                                <span>챔피언스리그</span>
                            </div>
                            <div class="sport_time">
                                <span class="tgreen">승무패</span>
                                <i class="line"></i>
                                <span>00-00 00:00</span>
                            </div>
                        </div><!-- .sports__contents-group-ttl -->

                        <!-- set start -->
                        <div class="sports__contents-team">
                            <div class="team__item time">00-00 00:00</div>
                            <div class="team__item team1 select__item selected">
                                <span>인도네시아</span>
                                <span>
                                    <img src="/assets_w/images/arr1.gif">
                                    <strong>1.03</strong>
                                </span>
                            </div>
                            <div class="team__item verse">VS</div>
                            <div class="team__item team2 select__item">
                                <span>
                                    <strong>1.03</strong>
                                    <img src="/assets_w/images/arr2.gif">
                                </span>
                                <span>요미우리 자이언츠</span>
                            </div>
                            <div class="team__item money tpurple">5000만</div>
                            <div class="team__item more"><a href="#" class="btn__bet_more">+10</a></div>
                        </div><!-- .sports__contents-team-->

                        <div class="sports__contents-team-cont">
                            <dl>
                                <dt>승무패</dt>
                                <dd class="colgroup3">
                                    <div class="team__item select__item selected">
                                        <span>홈승+오버(2.5)FC바르셀로나FC바르셀로나FC바르셀로나</span>
                                        <span>
                                            <img src="/assets_w/images/arr1.gif">
                                            <strong>5.03</strong>
                                        </span>
                                    </div>
                                    <div class="team__item select__item">
                                        <span>홈승+오버(2.5)</span>
                                        <span>
                                            <img src="/assets_w/images/arr2.gif">
                                            <strong>5.03</strong>
                                        </span>
                                    </div>
                                    <div class="team__item select__item">
                                        <span>홈승+오버(2.5)</span>
                                        <span>
                                            <img src="/assets_w/images/icon_h.gif">
                                            <strong>5.03</strong>
                                        </span>
                                    </div>
                                </dd>
                            </dl>
                            <dl>
                                <dt>승무패</dt>
                                <dd class="colgroup2">
                                    <div class="team__item select__item">
                                        <span>ddddddd</span>
                                        <span>
                                            <img src="/assets_w/images/arr2.gif">
                                            <strong>5.03</strong>
                                        </span>
                                    </div>
                                    <div class="team__item select__item">
                                        <span>ddddddd</span>
                                        <span>
                                            <img src="/assets_w/images/arr2.gif">
                                            <strong>5.03</strong>
                                        </span>
                                    </div>
                                    <div class="team__item select__item">
                                        <span>ddddddd</span>
                                        <span>
                                            <img src="/assets_w/images/arr2.gif">
                                            <strong>5.03</strong>
                                        </span>
                                    </div>
                                </dd>
                            </dl>
                        </div><!-- .sports__contents-team-cont-->
                        <!-- set end -->

                        <!-- set start -->
                        <div class="sports__contents-team">
                            <div class="team__item time">00-00 00:00</div>
                            <div class="team__item team1 select__item">
                                <span>인도네시아</span>
                                <span>
                                    <img src="/assets_w/images/arr1.gif">
                                    <strong>1.03</strong>
                                </span>
                            </div>
                            <div class="team__item verse">VS</div>
                            <div class="team__item team2 select__item selected">
                                <span>
                                    <strong>1.03</strong>
                                    <img src="/assets_w/images/arr2.gif">
                                </span>
                                <span>요미우리 자이언츠</span>
                            </div>
                            <div class="team__item money tpurple">5000만</div>
                            <div class="team__item more"><a href="#" class="btn__bet_more">+10</a></div>
                        </div><!-- .sports__contents-team-->

                        <div class="sports__contents-team-cont">
                            
                        </div><!-- .sports__contents-team-cont-->
                        <!-- set end -->





                    </div><!-- .sports__contents-group -->



                </div><!-- .sports__contents-item -->


            </div><!-- .sports__contents --> 
        </div><!-- .sports__contents_wrap -->


        <div class="sports__betting_wrap">
            <div class="sports__cart-close"><a href="#" class="btn__close_cart">close</a></div>

            <div class="sports__cart">
                <div class="sports__cart-ttl">
                    <strong>BETTING SLIP</strong>
                    <div class="auto-betting">
                        <input type="checkbox" id="auto-betting">
                        <label for="auto-betting">배당변경 자동적용</label>
                    </div>
                </div>
                <dl class="sports__cart-money">
                    <dt>보유머니</dt>
                    <dd>10,000,000</dd>
                    <dt>최대베팅금</dt>
                    <dd>50,000,000</dd>
                    <dt>최대적중금</dt>
                    <dd>100,000,000</dd>
                </dl>
                <div class="cart__item_wrap">
                    <div class="cart__item">
                        <p class="verse">
                            <span>FC 바르셀로나</span>
                            <i></i>
                            <span>레알마드리드</span>
                        </p>
                        <p class="option">오버언더 연장포함</p>
                        <p class="betting">레알마드리드 승<strong>1.00</strong></p>
                        <a href="#" class="btn__remove">remove</a>
                    </div>
                    <div class="cart__item">
                        <p class="verse">
                            <span>FC 바르셀로나</span>
                            <i></i>
                            <span>레알마드리드</span>
                        </p>
                        <p class="option">오버언더 연장포함</p>
                        <p class="betting">레알마드리드 승<strong>1.00</strong></p>
                        <a href="#" class="btn__remove">remove</a>
                    </div>
                    <div class="cart__item">
                        <p class="verse">
                            <span>FC 바르셀로나</span>
                            <i></i>
                            <span>레알마드리드</span>
                        </p>
                        <p class="option">오버언더 연장포함</p>
                        <p class="betting">레알마드리드 승<strong>1.00</strong></p>
                        <a href="#" class="btn__remove">remove</a>
                    </div>
                </div>
                <dl class="sports__cart-betting">
                    <dt>배당률</dt>
                    <dd>1.95</dd>
                    <dt>보너스배당률</dt>
                    <dd>0.00</dd>
                    <dt>총 배당률</dt>
                    <dd>1.95</dd>
                    <dt>당첨예상금</dt>
                    <dd>6,550,000</dd>
                </dl>
                <div class="sports__cart-input">
                    <label for=""></label>
                    <input type="text" id="betting_slip_money" value="0" onlyMoney>
                </div>
                <div class="sports__cart-button">
                    <button type="button" class="money" value="5000">5천원</button>
                    <button type="button" class="money" value="10000">1만원</button>
                    <button type="button" class="money" value="50000">5만원</button>
                    <button type="button" class="money" value="100000">10만원</button>
                    <button type="button" class="money" value="500000">50만원</button>
                    <button type="button" class="money" value="1000000">100만원</button>
                    <button type="button" class="money_max" value="max">MAX</button>
                    <button type="button" class="money_half" value="half">HALF</button>
                    <button type="button" class="reset" value="reset">전체지우기</button>
                </div>
                <div class="sports__cart-submit">
                    <button type="submit">베팅하기</button>
                </div>
            </div><!-- .sports__cart -->
        </div><!-- .sports__betting_wrap -->

        <div class="sports__cart-open"><a href="#" class="btn__open_cart">cart</a><i>1</i></div>

    </div><!-- .sports__wrap -->




    <div class="sports_wide_wrap" style="display:none;">
        <div class="sports_wide_left">
            <!-- lnb start -->
            <div class="con_box00">      
                <ul class="dropdown tendina">
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon01.png">&nbsp;&nbsp;&nbsp;전체보기
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">1000000</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>                                                                                                                                             
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon02.png">&nbsp;&nbsp;&nbsp;축구
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>                      
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>                            
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon03.png">&nbsp;&nbsp;&nbsp;야구
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon04.png">&nbsp;&nbsp;&nbsp;농구
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon05.png">&nbsp;&nbsp;&nbsp;배구
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon06.png">&nbsp;&nbsp;&nbsp;하키
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon07.png">&nbsp;&nbsp;&nbsp;e스포츠
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                            </li>
                        </ul>
                    </li><!-- 1 -->
                    <li class="menu1"><!-- 1 -->
                        <a href="#">
                            <div class="left_list1">
                                <span class="menu_left">
                                    <img src="/assets_w/images/icon09.png">&nbsp;&nbsp;&nbsp;기타
                                </span>
                                <span class="menu_right">
                                    <span class="menu_right_box">100</span></span>
                                
                            </div> 
                        </a>						   
                        <ul style="display: none;">
                            <li>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a> 
                                <a href="#"><span class="left_list1_in">메뉴명 <span class="menu_right_box">100</span></span></a>
                            </li>
                        </ul>
                    </li><!-- 1 -->
                </ul>
            </div>	
            <!-- lnb end -->
        </div> <!-- sports_wide_left -->
        
        <div class="sports_wide_center">
            <div class="sports_list_title">
                <div class="sports_list_title2">
                    <select class="sports_input1">
                        <option>종목선택</option>
                        <option>종목선택</option>
                    </select>
                </div>
                <div class="sports_list_title2">
                    <select class="sports_input1">
                        <option>리그선택</option>
                        <option>리그선택</option>
                    </select>
                </div>            
                <div class="sports_list_title3">
                    <ul>
                        <li><a href="#"><img src="/assets_w/images/icon01.png">&nbsp; 전체</a></li>
                        <li><a href="#"><img src="/assets_w/images/icon02.png">&nbsp; 축구</a></li>
                        <li><a href="#"><img src="/assets_w/images/icon03.png">&nbsp; 농구</a></li>
                        <li><a href="#"><img src="/assets_w/images/icon04.png">&nbsp; 야구</a></li>
                        <li><a href="#"><img src="/assets_w/images/icon05.png">&nbsp; 배구</a></li>
                        <li><a href="#"><img src="/assets_w/images/icon06.png">&nbsp; 하키</a></li>
                        <li><a href="#"><img src="/assets_w/images/icon07.png">&nbsp; e스포츠</a></li>   
                    </ul>
                </div>
            </div> 
            <div class="sports_s_left">  
                <div class="sports_list_bonus">
                    <ul>
                        <li>
                            <span class="bonus1">3폴더</span>이상
                            <div class="bonus_txt">1.03</div>
                        </li>
                        <li>
                            <span class="bonus1">6폴더</span>이상
                            <div class="bonus_txt">1.03</div>
                        </li>
                        <li>
                            <span class="bonus1">9폴더</span>이상
                            <div class="bonus_txt">1.03</div>
                        </li>
                    </ul>
                </div>
                <ul class="dropdown2">
                    <li><!-- 그룹1 -->
                        <div class="sport_title_wrap">
                            <div class="sport_title">
                                <div class="sport_league"><img src="/assets_w/images/flag/flag001.png"> <img src="/assets_w/images/line.png"> <img src="/assets_w/images/icon02.png"> <img src="/assets_w/images/line.png"> 챔피언스리그</div> <div class="sport_title_time"><span class="font04">승무패</span> <img src="/assets_w/images/line.png"> 00-00 00:00</div>
                            </div>
                            <div class="sport_title_list">
                                <ul>
                                    <li class="sport_time">00-00 00:00</li>
                                    <li class="sport_team1"><span class="team_l">인도네시아</span><span class="team_r"><img src="/assets_w/images/arr1.gif"> &nbsp;1.03</span></li>
                                    <li class="sport_tie">VS</li>
                                    <li class="sport_team2"><span class="team_l">1.03&nbsp; <img src="/assets_w/images/arr2.gif"></span><span class="team_r">요미우리 자이언츠</span></li>
                                    <li class="sport_state bet_max">5000만</li>
                                    <li class="sport_more">+10</li>
                                </ul>
                            </div>
                            <div class="sport_title_list">
                                <ul>
                                    <li class="sport_time">00-00 00:00</li>
                                    <li class="sport_team1"><span class="team_l">인도네시아인도네시아인도네시아인도네시아인도네시아</span><span class="team_r"><img src="/assets_w/images/icon_h.gif"> &nbsp;1.53</span></li>
                                    <li class="sport_tie">VS</li>
                                    <li class="sport_team2"><span class="team_l">1.73</span><span class="team_r">요미우리 자이언츠</span></li>
                                    <li class="sport_state bet_max">5000만</li>
                                    <li class="sport_more">+10</li>
                                </ul>
                            </div>
                        </div>                  
                    </li><!-- 그룹1끝 -->
                    <li><!-- 그룹1 -->
                        <div class="sport_title_wrap">
                            <div class="sport_title">
                                <div class="sport_league"><img src="/assets_w/images/flag/flag041.png"> <img src="/assets_w/images/line.png"> <img src="/assets_w/images/icon03.png"> <img src="/assets_w/images/line.png"> NBA</div> <div class="sport_title_time"><span class="font04">승무패</span> <img src="/assets_w/images/line.png"> 00-00 00:00</div>
                            </div>
                            <div class="sport_title_list">
                                <ul>
                                    <li class="sport_time">00-00 00:00</li>
                                    <li class="sport_team1"><span class="team_l">소프트뱅크 호크스</span><span class="team_r">&nbsp;1.03</span></li>
                                    <li class="sport_tie">VS</li>
                                    <li class="sport_team2"><span class="team_l">1.03 &nbsp;</span><span class="team_r">사우스 아델레이드</span></li>
                                    <li class="sport_state bet_max">5000만</li>
                                    <li class="sport_more">+10</li>
                                </ul>
                            </div>
                            <div class="sport_title_list">
                                <ul>
                                    <li class="sport_time">00-00 00:00</li>
                                    <li class="sport_team1"><span class="team_l">인도네시아</span><span class="team_r">&nbsp;1.03</span></li>
                                    <li class="sport_tie">VS</li>
                                    <li class="sport_team2"><span class="team_l">1.03&nbsp;</span><span class="team_r">요미우리 자이언츠</span></li>
                                    <li class="sport_state bet_max">5000만</li>
                                    <li class="sport_more">+10</li>
                                </ul>
                            </div>
                            <div class="sport_title_list">
                                <ul>
                                    <li class="sport_time">00-00 00:00</li>
                                    <li class="sport_team1"><span class="team_l">페리레이크스</span><span class="team_r">&nbsp;1.03</span></li>
                                    <li class="sport_tie">VS</li>
                                    <li class="sport_team2"><span class="team_l">1.03&nbsp;</span><span class="team_r">웰레톤 타이거스</span></li>
                                    <li class="sport_state bet_max">5000만</li>
                                    <li class="sport_more">+10</li>
                                </ul>
                            </div>
                        </div>                  
                    </li><!-- 그룹1끝 -->
                </ul>			
                <ul class="dropdown4" style="display: none;">
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title line1">승무패 </div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)홈승홈승홈승홈승홈승홈승홈승홈승홈승홈승</span><span class="bet_r"><img src="/assets_w/images/arr1.gif"> 5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r"><img src="/assets_w/images/arr2.gif"> 5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r"><img src="/assets_w/images/icon_h.gif"> 5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 --> 
                    
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title line2">언더오버 </div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->  
                    
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title line3">첫 자유투 </div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title line4">전반전 무득점 </div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title line5">4회말 홈런 </div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->              
                </ul>
            </div><!-- sports_s_left -->   
            
            <div class="sports_s_right">  
                <ul class="dropdown3">                  
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30 bet_on"><span class="bet_l">홈승+오버(2.5)FC바르셀로나FC바르셀로나FC바르셀로나</span><span class="bet_r"><img src="/assets_w/images/arr1.gif"> &nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r"><img src="/assets_w/images/arr2.gif"> &nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r"><img src="/assets_w/images/icon_h.gif"> &nbsp;5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                                            
                        </ul>
                    </li><!-- 그룹1끝 -->
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w50"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                    <li class="bet_list1_wrap"><!-- 그룹1 -->
                        <a href="#">                    
                            <div class="bet_list1_wrap_in_title">승무패 <span class="bet_list1_wrap_in_title_right"><img src="/assets_w/images/icon_plus.png" style="margin:-4px 0 0 0;"></span></div>
                        </a>  
                        <ul class="bet_list1_wrap_in">                      
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                            <li class="bet_list_td w30"><span class="cart-item"></span><span class="bet_l">홈승+오버(2.5)</span><span class="bet_r">&nbsp;5.03</span></li>                       
                        </ul>
                    </li><!-- 그룹1끝 -->
                </ul>
            </div><!-- sports_s_right -->
        </div><!-- sports_wide_center -->
        
        <div class="sports_wide_right">
            <div id="sports_cart_title" class="sports_cart_title">BETTING SLIP <span class="sports_cart_title_right">배당변경 자동적용&nbsp;&nbsp;<a href="#"><img src="/assets_w/images/cart_fix2.png"></a><a href="#"><img src="/assets_w/images/cart_fix1.png"></a></span></div>
            <div class="sports_cart_wrap">
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <td class="sports_cart_style1">보유머니<span class="sports_cart_style3">10,000,000</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">최대베팅금 <span class="sports_cart_style3">50.000,000</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">최대적중금 <span class="sports_cart_style3">100.000,000</span></td>
                        </tr>
                    </tbody></table>
                </div>
            </div> 
            <div class="sports_cart_bet">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="cart_bet">
                    <tbody><tr>
                        <td colspan="2"><span class="sports_cart_bet_font1">FC 바르셀로나 <img src="/assets_w/images/vs.png" width="25"> 레알마드리드</span></td>
                        <td rowspan="3"><a href="#"><img src="/assets_w/images/cart_close.png" style="padding:0 3px 0 3px;"></a></td>
                    </tr>                
                    <tr>
                        <td colspan="2"><span class="sports_cart_bet_font2">오버언더 연장포함</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="sports_cart_bet_font3">레알마드리드 승</span><span class="sports_cart_bet_p">1.00</span></td>
                    </tr>
                </tbody></table>                 
            </div> 
            <div class="sports_cart_bet">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="cart_bet">
                    <tbody><tr>
                        <td colspan="2"><span class="sports_cart_bet_font1">FC 바르셀로나 <img src="/assets_w/images/vs.png" width="25"> 레알마드리드</span></td>
                        <td rowspan="3"><a href="#"><img src="/assets_w/images/cart_close.png" style="padding:0 3px 0 3px;"></a></td>
                    </tr>                
                    <tr>
                        <td colspan="2"><span class="sports_cart_bet_font2">오버언더 연장포함</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="sports_cart_bet_font3">레알마드리드 승</span><span class="sports_cart_bet_p">1.00</span></td>
                    </tr>
                </tbody></table>                 
            </div> 
            <div class="sports_cart_wrap">
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <td class="sports_cart_style1 lalalal">배당률<span class="sports_cart_style2">1.95</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">보너스배당률 <span class="sports_cart_style2">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">총 배당률 <span class="sports_cart_style2">1.95</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1">당첨예상금<span class="sports_cart_style2">6,550,000</span></td>
                        </tr>
                        <tr>
                            <td class="sports_cart_style1"><input class="input3"></td>
                        </tr>
                    </tbody></table>
                </div>
                <div class="con_box00">
                    <table width="100%" border="0" align="center" cellspacing="4" cellpadding="0">
                        <tbody><tr>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">5천원</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">1만원</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">5만원</span></a></td>
                        </tr>
                        <tr>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">10만원</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">50만원</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">100만원</span></a></td>
                        </tr> 
                        <tr>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">MAX</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">HALF</span></a></td>
                            <td width="10%" align="center"><a href="#"><span class="sports_btn2">전체지우기</span></a></td>
                        </tr>                     
                        <tr>
                            <td width="100%" colspan="3" align="center"><a href="#"><span class="sports_btn1">베팅하기</span></a></td>
                        </tr>                                      
                    </tbody></table>
                </div>
            </div>	
        </div> <!-- sports_wide_right -->
    </div>


















    
    <?= view('/web/common/footer_wrap') ?>    
    <script>

        $(function(){

            $(".sports__contents-option .menu_wrap").mCustomScrollbar({
                axis:"x",
                theme:"minimal"
            });

            $(".sports__betting_wrap").mCustomScrollbar({
                axis:"y",
                theme:"minimal"
            });


            pub_lnb();
            sports();
            realtime();

            // accordion_menu
            $(document).on("click", ".accordion_menu strong", function(e){
                e.preventDefault();
                $(document).find(".accordion_lnb .accordion_submenu").slideUp();

                let li = $(this).parents("li");

                if( li.hasClass("active") ){
                    li.removeClass("active");
                    li.find(".accordion_submenu").slideUp();
                } else {
                    li.addClass('active');
                    li.find(".accordion_submenu").slideDown();
                }
            });

            // cart item remove
            $(document).on("click", ".cart__item_wrap .cart__item .btn__remove", function(e){
                e.preventDefault();
                $(this).parents(".cart__item").remove();
            })


            // cart button
            $(document).on("click", ".sports__cart-button button", function(e){
                e.preventDefault();
                const cart_item = $(document).find(".cart__item_wrap .cart__item");

                let item_status = itemCheck();  // item check
                if(!item_status){return false}

                const button = $(this);
                const input = $("#betting_slip_money");
                let money = Number(  format_remove(input.val())  );

                if ( button.hasClass("money") ){

                    money = money + Number( format_remove(button.val()) );
                    
                    let money_status = moneyCheck(money);
                    if(!money_status){ return false; }

                    input.val( format_money(money) );

                } else if ( button.hasClass("money_max") ){             // all money

                    let max_money = 9898688;
                    money = max_money;

                    let money_status = moneyCheck(money);
                    if(!money_status){ return false; }

                    input.val( format_money( max_money ) );

                } else if ( button.hasClass("money_half") ){            // half money

                    let half_money =  Math.floor( 9898688 / 2 );
                    money = half_money;
                    
                    let money_status = moneyCheck(money);
                    if(!money_status){ return false; }

                    input.val( format_money(half_money) );

                } else if ( button.hasClass("reset") ){
                    input.val( 0 );
                }
            })

            // submit
            $(document).on("click", ".sports__cart-submit button[type='submit']", function(e){
                e.preventDefault();

                let item_status = itemCheck();  // item check
                if(!item_status){return false}

                let money = $("#betting_slip_money").val();
                let money_status = moneyCheck(money);   // money check
                if(!money_status){ return false; }

                console.log('베팅하기');
            });

            // selected
            $(document).on("click", ".select__item", function(e){
                e.preventDefault();
                $(this).toggleClass('selected');
            })

            // cart open
            $(document).on("click", ".btn__open_cart", function(e){
                e.preventDefault();
                $(document).find(".sports__betting_wrap").addClass("show");
            })
            // cart close
            $(document).on("click", ".btn__close_cart", function(e){
                e.preventDefault();
                $(document).find(".sports__betting_wrap").removeClass("show");
            })

        });

        
        // 
        const sports = function(){
            let request_data = {}
            call_ajax("sports_ready", "/web/sports", request_data);
        }
        const result_sports_ready = function(response){
            console.log(response)
        }

        
        // 
        const realtime = function(){
            let request_data = {}
            call_ajax("realtime_ready", "/web/realtime", request_data);
        }
        const result_realtime_ready = function(response){
            console.log(response)
        }



        // lnb
        const pub_lnb = function (){
            let $resultHTML = ``;
            for(let i=0; i<8; i++){
                let menu, title, cnt;
                switch(i){
                    case 0 : menu =`menu1`; title = '전체보기'; cnt=`100000`; break;
                    case 1 : menu =`menu2`; title = '축구'; cnt=`100`; break;
                    case 2 : menu =`menu3`; title = '야구'; cnt=`100`; break;
                    case 3 : menu =`menu4`; title = '농구'; cnt=`100`; break;
                    case 4 : menu =`menu5`; title = '배구'; cnt=`100`; break;
                    case 5 : menu =`menu6`; title = '하키'; cnt=`100`; break;
                    case 6 : menu =`menu7`; title = 'e스포츠'; cnt=`100`; break;
                    case 7 : menu =`menu9`; title = '기타'; cnt=`100`; break;
                    default : break;
                }
                $resultHTML += `
                    <li class="accordion_menu ${menu}">
                        <strong>
                            <a href="#">
                                <span>${title}</span>
                                <i>${cnt}</i>
                            </a>
                        </strong>
                        <ul class="accordion_submenu">
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                            <li><a href="#">menu<i>100</i></a></li>
                        </ul>
                    </li>
                `;
            }
            $(document).find(".sports__wrap .accordion_lnb").html($resultHTML);
        }

        const moneyCheck = function(money){
            money = Number( format_remove(money) );

            if( money < 1 ){
                alert("금액을 입력해주세요.");
                return false;
            }
            if( money > 9898688 ){
                alert("보유머니를 초과했습니다.");
                return false;
            }

            return true;
        }

        const itemCheck = function(){
            const item = $(".cart__item_wrap .cart__item");
            if( item.length < 1 ){
                alert("경기를 선택해주세요.");
                return false;
            }
            return true;
        }




        // let isMobile = false;
        // let betType = 'S'; // 1: 스포츠, 2: 실시간
        // let folderType = 'S'; // 'S': 싱글, 'D': 다폴더
        // let service_bonus_folder = '<?= $arr_bonus['service_bonus_folder']; ?>';
        // let odds_3_folder_bonus = <?= $arr_bonus['odds_3_folder_bonus']; ?>;
        // let odds_4_folder_bonus = <?= $arr_bonus['odds_4_folder_bonus']; ?>;
        // let odds_5_folder_bonus = <?= $arr_bonus['odds_5_folder_bonus']; ?>;
        // let odds_6_folder_bonus = <?= $arr_bonus['odds_6_folder_bonus']; ?>;
        // let odds_7_folder_bonus = <?= $arr_bonus['odds_7_folder_bonus']; ?>;
        // let limit_folder_bonus = <?= $arr_bonus['limit_folder_bonus']; ?>;
        // let isAlreadyBetting = false;
        // let betList = [];
        // let is_betting_slip = '<?=$is_betting_slip?>';
        // let maxBetMoney = <?= $maxBetMoney ?>;
        // let limitBetMoney = <?= $limitBetMoney ?>;
        // let betDelayTime = new Array();
        // betDelayTime['6046'] = <?= $betDelayTime['6046'] ?>;
        // betDelayTime['35232'] = <?= $betDelayTime['35232'] ?>;
        // betDelayTime['48242'] = <?= $betDelayTime['48242'] ?>;
        // betDelayTime['687890'] = <?= $betDelayTime['687890'] ?>;
        // betDelayTime['154830'] = <?= $betDelayTime['154830'] ?>;
        // betDelayTime['154914'] = <?= $betDelayTime['154914'] ?>;
        
        // let display_6046 = 0;
        // let display_48242 = 0;
        // let display_154914 = 0;
        // let display_154830 = 0;
        // let betList_new = new Map();
        // let live_data = [];
        
        // let isAsyncGetRealTimeGameLiveScoreList = false;
        // let sports = [6046, 48242, 154914, 154830, 687890, 35232];
        // let selectFixtureId = 0;    // 현재 선택한 경기
        // let selectFixtureDisplay = 0;
        // let active1 = 0;
        // let active2 = 0;
        // let callGameLiveScoreList;

        // function getRealTimeGameLiveScoreList(sportsId, locationId) {
            
        //     console.log('getRealTimeGameLiveScoreList !! click',sportsId, locationId );

        //     if(isAsyncGetRealTimeGameLiveScoreList) return false;
        //     isAsyncGetRealTimeGameLiveScoreList = true;
        //     active1 = sportsId ? sportsId : 0;
        //     active2 = locationId ? locationId : 0;
        //     let dataForm = {};

        //     if (locationId > 0) {
        //         dataForm['location_id'] = locationId;
        //     }
        //     if (sportsId > 0) {
        //         dataForm['sports_id'] = sportsId;
        //     }

        //     console.log('dataForm', dataForm);
        //     let totalGameCnt = 0;   // Live Sports 총 갯수 초기화
            
        //     $.ajax({
        //         url: '/api/real_time/getRealTimeGameLiveScoreList',
        //         type: 'post',
        //         data: dataForm,
        //     }).done(function (response) {
        //         $('.live_game_display *').remove();
        //         //console.log('response', response);

        //         //let fristBetList = [52,202,203,204,205,206,226,63,464, 349]; // 첫화면에 출력할 마켓타입들
        //         //let displayOrderMarkets = ['메인','승무패/승패','핸디캡','오버언더','기타'];
        //         let betting_html = '';
        //         live_data = response['data']['live_list'];
                
        //         const activeBetId = [];
        //         $('.slip_bet_ing').each(function(){
        //             activeBetId.push($(this).data('bet-id'));
        //         })

        //         const betAmount = $('#betting_slip_money').val();
                
        //         // 종목 세팅
        //         sports.forEach(function (sports_id) {

        //             let list = response['data']['live_list'][sports_id];

        //             if(!list) {
        //                 return true;
        //             }
        //             const listLength = Object.keys(list).length;
                    
        //             // 하위 종목 갯수
        //             $('#sports_id_'+sports_id).text(listLength);

        //             // 종목 총 갯수
        //             totalGameCnt += Number(listLength);

        //             // 종목 총 갯수 -> left menu 로 바인딩
        //             $('.menu_right_box.realTimeTotalCnt').text(totalGameCnt);

        //             /*let beFid = '';
        //             let listCnt = 0;
        //             let moreCntList = [];
        //             let menuCount = 0;
        //             let arrMenuKey = [];

        //             // 종목별 경기수
        //             if(sports_id == 6046){
        //                 listCnt = <?//=isset($realTimeTotal[6046])?$realTimeTotal[6046]:0?>;
        //             }else if(sports_id == 48242){
        //                 listCnt = <?//=isset($realTimeTotal[48242])?$realTimeTotal[48242]:0?>;
        //             }else if(sports_id == 154914){
        //                 listCnt = <?//=isset($realTimeTotal[154914])?$realTimeTotal[154914]:0?>;
        //             }else if(sports_id == 154830){
        //                 listCnt = <?//=isset($realTimeTotal[154830])?$realTimeTotal[154830]:0?>;
        //             }else if(sports_id == 35232){
        //                 listCnt = <?//=isset($realTimeTotal[35232])?$realTimeTotal[35232]:0?>;
        //             }else if(sports_id == 687890){
        //                 listCnt = <?//=isset($realTimeTotal[687890])?$realTimeTotal[687890]:0?>;
        //             }*/

        //             /* 데이터 바인딩 */
        //             let mainGameList = [];
        //             for (const[fixtureKey, fixture_list] of Object.entries(list)) {
        //                 //let afFid = fixtureKey;
        //                 //let thisMarkettotalCnt = 0;
                        
        //                 // 메인 게임정보, 메인에 속한 키값, 스코어보드를 가져온다.
        //                 const firstGameIdx = Object.keys(fixture_list)[0];
        //                 let mainGame = '';

        //                 let mainKey = Object.keys(fixture_list[firstGameIdx])[0];

        //                 if (sports_id == 6046) {
        //                     mainGame = fixture_list[firstGameIdx][mainKey][0];
        //                 } else if (sports_id == 48242) {
        //                     mainGame = fixture_list[firstGameIdx][mainKey][0];
        //                 } else if (sports_id == 154830) {
        //                     mainGame = fixture_list[firstGameIdx][mainKey][0];
        //                 } else if (sports_id == 35232) {
        //                     mainGame = fixture_list[firstGameIdx][mainKey][0];
        //                 } else if (sports_id == 687890) {
        //                     mainGame = fixture_list[firstGameIdx][mainKey][0];
        //                 } else {
        //                     mainGame = fixture_list[firstGameIdx][mainKey][0];
        //                 }

        //                 // console.log('mainGame', mainGame);

        //                 // 메인게임 리스트에 추가
        //                 betList.push(mainGame);
        //                 mainGameList.push(mainGame);

        //                 //let mainGameKey = mainGame['fixture_id']+'_'+mainGame['markets_id']+'_'+mainGame['bet_base_line']+'_'+mainGame['providers_id'];

        //                 // 경기정보 좌측 진행상태, 시간
        //                 let minute = Math.floor(mainGame['live_time']/60);
        //                 let second = Math.floor(mainGame['live_time']%60);
        //                 if(minute < 10) minute = '0'+minute;
        //                 if(second < 10) second = '0'+second;
        //                 let fixture_time = minute  + ":"+ second;

        //                 // 배구, 야구, e스포츠는 시간이 없다.
        //                 if(sports_id == 154830 || sports_id == 154914 || sports_id == 687890 ){
        //                     fixture_time = '진행중';
        //                 }

        //                 // 타이틀 html
        //                 html = '';

        //                 // 리그 베팅금
        //                 let leagues_bet_money = 0;
        //                 if(+mainGame['leagues_m_bet_money'] > 10000){
        //                     leagues_bet_money = setComma(parseInt(+mainGame['leagues_m_bet_money'] / 10000))+'만';
        //                 }else{
        //                     leagues_bet_money = setComma(parseInt(+mainGame['leagues_m_bet_money']));
        //                 }
                        
        //                 // 리그이미지가 없다.
        //                 let leagueImagePath = '<?=$imageBasePath?>'+'/league/'+mainGame['fixture_league_image_path'];
        //                 if(mainGame['fixture_league_image_path'].indexOf('flag') >= 0){
        //                     leagueImagePath = mainGame['fixture_league_image_path'];
        //                 }
                        
        //                 let sportsImagePath = '<?=$imageBasePath?>'+'/sports/icon_game'+mainGame['fixture_sport_id']+'.png';
                        
        //                 // 현재 오픈된 경기인지 파악해서 오픈상태 유지처리
        //                 let fixture_display = 'display:none';
        //                 //if($('#display_fixture_'+selectFixtureId).hasClass('fixture_open') == true){
        //                 if(selectFixtureId == fixtureKey){
        //                     if(selectFixtureDisplay == 1){
        //                         fixture_display = 'display:block';
        //                         //console.log('fixture_open : '+selectFixtureId);
        //                     }
        //                 }
                        
        //                 // 첫경기는 오픈된 상태로 준비
        //                 if(0 == selectFixtureId){
        //                     fixture_display = 'display:block';
        //                     selectFixtureId = fixtureKey;
        //                     selectFixtureDisplay = 1;
        //                 }
                        
        //                 // 스코어 rmq 도착전 null일때
        //                 let live_results_p1 = mainGame['live_results_p1'];
        //                 let live_results_p2 = mainGame['live_results_p2'];
        //                 if(null === live_results_p1 || null === live_results_p2){
        //                     live_results_p1 = live_results_p2 = 0;
        //                 }
                        
        //                 // 경기목록
        //                 // style=\"display: none;\"
        //                 html = "<li id=\"live_game_display_"+mainGame['fixture_sport_id']+"\" class='live_game_display_"+mainGame['fixture_sport_id']+" live_game_location_"+mainGame['fixture_location_id']+"'>" +
        //                         "<a href=\"#\">" +
        //                         "   <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
        //                         "       <div class=\"live_title_wrap\" onClick=\"onDisplayFixture("+fixtureKey+")\">" +
        //                         "           <div class=\"live_title_left\">" +
        //                         "               <img src=\""+sportsImagePath+"\" width=\"18\">&nbsp; " + 
        //                         "               <span class=\"live_title_left_team1\">" + mainGame['fixture_participants_1_name'] + "</span>" +
        //                         "               <img src=\"/assets_w//assets_w/images/vs.png\" width=\"28\"> " + 
        //                         "               <span class=\"live_title_left_team2\">" + mainGame['fixture_participants_2_name'] + "</span>" +
        //                         "               <span class=\"live_title_left_score\">"+live_results_p1+" : "+live_results_p2+"</span>" +
        //                         "           </div>" +
        //                         "           <div class=\"live_title_right\">" + 
        //                         "               <span class=\"live_title_right_league\">" + mainGame['fixture_league_name'] + "</span>" +
        //                         "               <img src=\"/assets_w//assets_w/images/live_line.png\">" +
        //                         "               <span class=\"font10\">"+
        //                         "               <span class=\"live_title_right_time\">" + mainGame['live_current_period_display'] + "&nbsp;" + fixture_time +  "</span>" +
        //                         "                   <a href=\"#\">" +
        //                         "                       <span class=\"live_title_right_btn\">베팅하기</span>" +
        //                         "                   </a>" +
        //                         "               </span>" +
        //                         "           </div>" +
        //                         "       </div>" +
        //                         "   </table>" +
        //                         "</a>" + 
        //                         "<ul>" +
        //                         "   <div class=\"live_box_wrap2\" style=\"clear:both; "+fixture_display+"\" id='display_fixture_"+fixtureKey+"'>" +
        //                         "       <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
        //                         "           <tr>" +
        //                         "               <td height=\"40\" style=\"padding:0 0 0 10px; background:rgba(0,0,0,0.4);\">" +
        //                         "                   <img src=\""+leagueImagePath+"\" width=\"28\">" +
        //                         "                   <img src=\"/assets_w//assets_w/images/live_line.png\"> " + mainGame['fixture_league_name'] +
        //                         "                   <img src=\"/assets_w//assets_w/images/live_line.png\">" +
        //                         "                   <span class=\"font06\">"+leagues_bet_money+"</span>" +
        //                         "               </td>" +
        //                         "               <td rowspan=\"3\">" +
        //                         //"                   <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" +
        //                         //"                       <tr>" +
        //                         getScoreBoard(sports_id, fixtureKey) +
        //                         //"                       </tr>" +
        //                         //"                   </table>" +
        //                         "               </td>" +
        //                         "           </tr>" +
        //                         "           <tr>" +
        //                         "               <td height=\"60\" style=\"padding:0 0 0 20px; border-bottom:1px solid rgba(255,255,255,0.2);\">" +
        //                         "                   <img src=\"/assets_w//assets_w/images/live_home.png\"> " +
        //                         "                   <span class=\"live_font1\">"+mainGame['fixture_participants_1_name']+"</span>" +
        //                         "               </td>" +
        //                         "           </tr>" +
        //                         "           <tr>" +
        //                         "               <td height=\"60\" style=\"padding:0 0 0 20px\">" +
        //                         "                   <img src=\"/assets_w//assets_w/images/live_away.png\">" +
        //                         "                   <span class=\"live_font1\">"+mainGame['fixture_participants_2_name']+"</span>" +
        //                         "               </td>" +
        //                         "           </tr>" +
        //                         "       </table>" +
        //                         "   </div>" +
        //                         "</ul>" +
        //                         "</li>" ;
                                
        //                         $('.live_game_display').append(html);
                                
        //                         for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
        //                             for (const[marketKey, game_list] of Object.entries(menu_list)) {
        //                                 let bGameKey = '';
        //                                 for (const[bKey, game] of Object.entries(game_list)) {
        //                                     bGameKey = game['fixture_id']+'_'+game['markets_id']+'_'+game['bet_base_line']+'_'+game['providers_id'];
        //                                     betList_new.set(bGameKey, game);
        //                                 }
        //                             }
        //                         }
                        
        //                 // 선택한 경기 배팅
        //                 if(selectFixtureId == fixtureKey || selectFixtureId == 0){
        //                     selectFixtureId = fixtureKey;
        //                 }
        //                 betting_html += "</li>";
        //             } // end fixture
                    
        //             // 스코어 출력
        //             mainGameList.forEach(function(item){
        //                 try {
        //                     setLiveScore(item);
        //                 } catch (err) {
        //                     console.log(err);
        //                 }
        //             });
        //         }); // end sports
        //         openBetData(selectFixtureId);
                
        //         // 좌측 선택 처리
        //         if (locationId > 0) {
        //             dataForm['location_id'] = locationId;
        //             dataForm['sports_id'] = sportsId;
        //             $('.dropdown2 li').each(function(){
        //                 const $this = $(this);
        //                 if($this.hasClass('live_game_location_'+locationId)){
        //                     $this.attr('style', 'display: block;')
        //                     console.log('locationId : '+locationId);
        //                 }else {
        //                     $this.attr('style', 'display: none;')
        //                     console.log('not locationId : '+locationId);
        //                 }
        //             });
        //             $('.location_block').each(function(){
        //                 const $this = $(this);
        //                 if($this.hasClass('location_id_'+locationId)){
        //                     $this.attr('style', 'display: block;')
        //                 }else {
        //                     $this.attr('style', 'display: none;')
        //                 }
        //             });
        //         }
                
        //         if (sportsId > 0) {
        //             dataForm['sports_id'] = sportsId;
        //             $('.dropdown2 li').each(function(){
        //                 const $this = $(this);
        //                 if($this.hasClass('live_game_display_'+sportsId)){
        //                     $this.attr('style', 'display: block;')
        //                 }else {
        //                     $this.attr('style', 'display: none;')
        //                 }
        //             });
        //         }
                
        //         // 베팅슬립에 있는 배당중 하나라도 배당이 닫혔으면 초기화한다.
        //         /*if(activeBetId.length > 0){
        //             activeBetId.reverse().forEach(function(item){
        //                 //console.log('activeBetId event :'+item);
        //                 //if(item != 17162041418486740){
        //                     console.log('activeBetId event :'+item);
        //                     $('.odds_btn[data-bet-id="' + item +'"]').trigger('click');
        //                 //}
        //             });
        //             $('#betting_slip_money').val(setComma(betAmount));
        //             changeWillWinMoney();
        //         }*/

        //         /*if(activeBetId.length > 0){
        //             activeBetId.reverse().forEach(function(item){
        //                 $('.odds_btn[data-bet-id="' + item +'"]').trigger('click');
        //             });
                    
        //             $('#betting_slip_money').val(setComma(betAmount));
        //             changeWillWinMoney();
        //         }*/
                
        //         moreDisplaySelect(activeBetId);
                
        //         // 반복실행(이거를 켜면 펼쳐진 항목이 되돌아감)
        //         /*callGameLiveScoreList = setTimeout(function(){
        //             getRealTimeGameLiveScoreList(active1, active2);
        //             clearTimeout(callGameLiveScoreList);
        //         }, 2000);*/
        //         isAsyncGetRealTimeGameLiveScoreList = false;
        //     }).fail(function (error) {
        //         // alert('데이터 로드에 실패했습니다.');
        //     }).always(function (response) {});
        // } // end getRealTimeGameLiveScoreList
        
        // $(document).ready(function(){
        //     $('#league_name').val('<?=$league_name?>');
            
        //     // 게임 배팅 리스트
        //     getRealTimeGameLiveScoreList(0, 0);
            
        //     // 첫경기 오픈 처리
        //     //$('#display_fixture_'+selectFixtureDisplay).slideDown();
        //     //console.log('selectFixtureDisplay'+selectFixtureDisplay);
            
        //     // 베팅 선택
        //     $(document).on('click','.odds_btn',function(){
        //         let betMoney = $('#betting_slip_money').val();
        //         betMoney = Number(betMoney.replace(/,/gi,"")); //변경작업
        //         if(0 < betMoney){
        //             $('#betting_slip_money').val(0);
        //         }
        //         //console.log('odds_btn');
        //         let betListIndex = $(this).data('index');
        //         const indexArr = betListIndex.split('_');
        //         const baseLineKey = indexArr[2];
        //     console.log(betListIndex);
        //         //let tdcell = $(this).data('td-cell');
        //         //console.log(tdcell);
        //         //const cellData = tdcell.split('_');
        //         //let fixture_start_date = cellData[1];
        //         let fixture_start_date = '';
        //         let betBaseLine = baseLineKey;

        //         let betListFixId = $(this).data('fixture-id');
        //         let betOddsTypes = $(this).data('oddsType');
        //         let betOddsTypesDisplay = betOddsTypes == 'win' ? '승' : betOddsTypes == 'draw' ? '무' : betOddsTypes == 'lose' ? '패' :betOddsTypes;
        //         let betMarketType = "";
        //         let betListFixIdStr = betListFixId + '';
        //         let fixtureId = betListFixIdStr.split('_')[0];
        //         let betId = $(this).data('bet-id');
        //         let betPrice = $(this).data('bet-price');
        //         let leagues_m_bet_money = $(this).data('leagues_m_bet_money');
        //         let obj = betList_new.get(betListIndex);
        //         let betSportId = obj.fixture_sport_id;
        //         let betMarketId = obj.markets_id;
        //         let alreadyCombinObj;
        //         let alreadyCombin = false;
        //         let isCombin = false;
        //         let totalOdds = $('.total_odds').data("total_odds");
        //         //let totalOdds = $('.total_odds').text();
        //     console.log('click odds_btn : ' + betId);
        //     //console.log(obj);

        //         if ($(this).hasClass('bet_on') || $(this).hasClass('sports_select')){
        //             console.log('동일베팅 항목을 선택(선택해제)');
        //             let price = $('[data-bet-id="' + betId + '"].slip_bet_ing .slip_bet_cell_r').text();
        //             price = price == 0 ? 1 : price;
        //             totalOdds = totalOdds / price;
        //             totalOdds = totalOdds == 1 ? 0 : totalOdds;
        //             $(this).removeClass('bet_on');
        //             $('.slip_bet_ing[data-bet-id="' + betId + '"]').remove();
        //             $('.total_odds').data("total_odds",totalOdds);
        //             $('.total_odds').html(totalOdds);

        //             let betSlipCount = getBetSlipCountReal();
        //             /*if (betSlipCount > 7) {
        //                 $('.bonus_total_odds').html(odds_5_folder_bonus);
        //             } else if (betSlipCount < 5 && betSlipCount >= 3) {
        //                 $('.bonus_total_odds').html(odds_3_folder_bonus);
        //             } else if (betSlipCount < 3) {
        //                 $('.bonus_total_odds').html(0);
        //             }*/
        //             setBonusPrice(totalOdds, betSlipCount);

        //             changeWillWinMoney();
        //             //initForm();

        //             // 뱃팅슬롯 카운트
        //             if(isMobile){
        //                 $('.cart_count').text($('.slip_bet_ing').length);
        //                 $('.cart_count2').text($('.slip_bet_ing').length);
        //             }
        //             changeWillWinMoney();
        //             return;
        //         }

        //         // 같은 경기에 다른 게임이 이미 선택 되어있는 경우 이전 게임 선택 해제 및 리스트 삭제하고
        //         // 지금 클릭한 게임을 추가한다
        //         if ($('[data-fixture-id*=' + betListFixId + ']').hasClass('sports_select') || $('[data-fixture-id*=' + betListFixId + ']').hasClass('bet_on')){
        //             console.log('동일경기 선택');
        //             //const $selecteSports = $(this).closest('.soprts_in_acc').find('.bet_on');
        //             let selectTagName = '.dropdown3';
        //             if(isMobile){
        //                 //selectTagName = '.bet_list1_wrap_in_title';
        //                 selectTagName = '#display_fixture_'+betListFixId;
        //             }else{
        //                 selectTagName = '.dropdown3';
        //             }
        //             const $selecteSports = $(selectTagName).find('.bet_on');
        //             if($selecteSports.length > 1){
        //                 const $this = $(this);
        //                 let flag1 = false;
        //                 let flag2 = false;
        //                 let targetObj;
        //                 const fixtureMarket1 = $this.data('index').split('_')[0] + '_' + $this.data('index').split('_')[1];
        //                 $selecteSports.each(function(){
        //                     const fixtureMarket2 = $(this).data('index').split('_')[0] + '_' +$(this).data('index').split('_')[1];
        //                     if(fixtureMarket1 == fixtureMarket2){
        //                         flag1 = true;
        //                     }else{
        //                         targetObj = $(this);
        //                         flag2 = true;
        //                     }
        //                 });

        //                 if(flag1 && flag2){
        //                     alreadyCombin = true;
        //                     alreadyCombinObj = targetObj;
        //                 }
        //             }
        //             if (betSportId == 48242) {
        //                 if (betMarketId == 28) {
        //                     const checkFlag1 = fnCheckCombine($(this), betList, 28, 226)
        //                     const checkFlag2 = fnCheckCombine($(this), betList, 28, 342)
        //                     if ((checkFlag1 && !checkFlag2) || (!checkFlag1 && checkFlag2)) {
        //                         isCombin = true;
        //                     }
        //                 } else if (betMarketId == 226) {
        //                     if (fnCheckCombine($(this), betList, 226, 28))
        //                         isCombin = true;

        //                 } else if (betMarketId == 342) {
        //                     if (fnCheckCombine($(this), betList, 342, 28))
        //                         isCombin = true;

        //                 } else if (betMarketId == 64) {
        //                     if (fnCheckCombine($(this), betList, 64, 21))
        //                         isCombin = true;

        //                 } else if (betMarketId == 21) {
        //                     if (fnCheckCombine($(this), betList, 21, 64))
        //                         isCombin = true;

        //                 } else if (betMarketId == 65) {
        //                     if (fnCheckCombine($(this), betList, 65, 45))
        //                         isCombin = true;

        //                 } else if (betMarketId == 45) {
        //                     if (fnCheckCombine($(this), betList, 45, 65))
        //                         isCombin = true;

        //                 } else if (betMarketId == 66) {
        //                     if (fnCheckCombine($(this), betList, 66, 46))
        //                         isCombin = true;

        //                 } else if (betMarketId == 46) {
        //                     if (fnCheckCombine($(this), betList, 46, 66))
        //                         isCombin = true;

        //                 } else if (betMarketId == 67) {
        //                     if (fnCheckCombine($(this), betList, 67, 47))
        //                         isCombin = true;

        //                 } else if (betMarketId == 47) {
        //                     if (fnCheckCombine($(this), betList, 47, 67))
        //                         isCombin = true;
        //                 }
        //             }
                    
        //             if(!isCombin){
        //                 let price = 1;
        //                 //let price = $('[data-fixture-id*="' + fixtureId + '"].slip_bet_ing .slip_bet_cell_r').text();
        //                 if(isMobile){
        //                     price = $('[data-fixture-id*="' + fixtureId + '"].sports_cart_bet .sports_cart_bet_p').text();
        //                     price = price == 0 ? 1 : price;
        //                 }else{
        //                     $('[data-index*="' + fixtureId + '"].sports_cart_bet .sports_cart_bet_p').each(function () {
        //                         price = price * Number($(this).text());
        //                     });
        //                 }
        //                 totalOdds = totalOdds / price;
        //                 //$('[data-fixture-id*=' + betListFixId + ']').removeClass('sports_select');
        //                 //$('[data-fixture-id*=' + betListFixId + ']' + '.slip_bet_ing').remove();
        //                 //$('[data-fixture-id*=' + betListFixId + ']').removeClass('live_select');
        //                 $('[data-index*="' + fixtureId + '"]').removeClass('bet_on');
        //                 $('[data-index*="' + fixtureId + '"]' + '.sports_cart_bet').remove();
        //             }
        //             let betSlipCount = getBetSlipCountReal();
        //             /*if (betSlipCount > 7) {
        //                 $('.bonus_total_odds').html(odds_5_folder_bonus);
        //             } else if (betSlipCount < 5 && betSlipCount >= 3) {
        //                 $('.bonus_total_odds').html(odds_3_folder_bonus);
        //             } else if (betSlipCount < 3) {
        //                 $('.bonus_total_odds').html(0);
        //             }*/
        //             setBonusPrice(totalOdds, betSlipCount);
        //         }

        //         let isDuplicated = false;
        //         $('.slip_bet_ing').each(function(){
        //             if($(this).data('index') == betListIndex){
        //                 isDuplicated = true;
        //                 return false;
        //             }
        //         });

        //         if(isDuplicated){
        //             console.log('isDuplicated');
        //             initForm();
        //             totalOdds = $('.total_odds').data("total_odds");
        //         }

        //         if(isMobile){
        //             totalOdds = betPrice;

        //             if(isCombin && !isDuplicated){

        //                 const curTotalOdds = Number($('.total_odds').text());

        //                 if(curTotalOdds > 0){
        //                     totalOdds = totalOdds * curTotalOdds;
        //                 }
        //             }
        //         }else{
        //             if (obj['betOddsTypes'] != null && obj['betOddsTypes'] != '') {
        //                 totalOdds = parseFloat(obj['betOddsTypes']).toFixed(2);
        //             }

        //             totalOdds = totalOdds == 0 ? 1 : totalOdds;
        //             totalOdds = totalOdds * parseFloat(betPrice);
        //             totalOdds = Number(totalOdds).toFixed(2);
        //         }

        //         betMarketType = obj['markets_name_origin'];

        //         if(+totalOdds > 100) {
        //             alert('최대 배당률을 초과하였습니다. [최대: 100배]');
        //             totalOdds = $('.total_odds').html();                
        //             return false;
        //         }

        //         $('.total_odds').html(Number(totalOdds).toFixed(2));

        //         if ($(this).hasClass('sports_table_in_1') || $(this).hasClass('sports_table_in_2') || $(this).hasClass('sports_table_in_xo')) {
        //             $(this).addClass('bet_on');
        //         }else {
        //             $(this).addClass('bet_on');
        //         }

        //         let html = "<li class='sports_cart_bet slip_bet_ing' data-index='"+betListIndex+"' data-odds-types="+betOddsTypes+
        //                                 " data-bet-id="+betId+" data-bet-price="+betPrice+" data-markets-name="+betMarketType+
        //                                 " data-bet-base-line='"+betBaseLine+"' data-fixture-start-date='"+fixture_start_date+"' data-leagues_m_bet_money="+leagues_m_bet_money+">" +
        //                                 "<div width='100%'class='cart_bet'>" +
        //                                 "<div>" + 
        //                                     "<td>"+obj['fixture_participants_1_name']+"<span class='sports_cart_bet_font1'> "+betOddsTypesDisplay+"</span></td>"+
        //                                     "<td><a href='#' class='sports_cart_bet_img'><img src='/assets_w//assets_w/images/cart_close.png'"+
        //                                     "class='notify-close-btn' data-index="+betListIndex+" data-bet-id="+betId+"></a><span class='sports_cart_bet_p'>"+betPrice+"</span></td>"+
        //                                 "</div>"+
        //                                 "<div>"+
        //                                     "<td colspan='2'><span class='sports_cart_bet_font2'>"+betMarketType+"</span></td>"+
        //                                 "</div>"+
        //                                 "<div>"+
        //                                     "<td colspan='2'><span class='sports_cart_bet_font3'>"+obj['fixture_participants_1_name']+"<img src='/assets_w//assets_w/images/vs.png' width='25'>"+obj['fixture_participants_2_name']+"</span></td>"+
        //                                 "</div>"+
        //                             "</div>"+
        //                         "</li>";
        //         //console.log(html);
        //         $('.slip_tab_wrap').after($(html));

        //         // 뱃팅슬롯 카운트
        //         if(isMobile){
        //             $('.cart_count').text($('.slip_bet_ing').length);
        //             $('.cart_count2').text($('.slip_bet_ing').length);
        //         }
        //         changeWillWinMoney();

        //         if(alreadyCombin){
        //             console.log('alreadyCombin');
        //             alreadyCombinObj.trigger('click');
        //             if(isMobile) fnSetVisible();
        //         }

        //     });

        //     $(document).on('click', '.notify-close-btn', function () {
        //         notifyCloseBtn(this);
        //     });


        //     $(document).on('click', '.max_btn', function() {
        //         maxBtnClick();
        //     });

        //     $(document).on('click', '.reset_btn', function() {
        //         $('#betting_slip_money').val(0);
        //         changeWillWinMoney();
        //         betting_impossible = false;
        //     });

        //     // 배팅
        //     $(document).on('click', '.sports_btn1', function () {
        //         bettingClick();
        //     }); // end sports_btn1 click


        //     $(document).on('change', '#betting_slip_money', function () {
        //         changeWillWinMoney();
        //     });

        //     $(document).on('focus', '#betting_slip_money', function () {
        //         bettingSlipMoneyFocus();
        //     });

        //     $(document).on('blur', '#betting_slip_money', function () {
        //         bettingSlipMoneyBlur();
        //     });

        //     /*$(document).on('click', '.soprts_in_acc p', function(j) {
        //         var dropDown = $(this).closest('li').find('.sports_in');

        //         //$(this).closest('.accordion').find('p').not(dropDown).slideUp();

        //         if ($(this).hasClass('active')) {
        //             $(this).removeClass('active');
        //         } else {
        //             //$(this).closest('.accordion').find('a.active').removeClass('active');
        //             $(this).addClass('active');
        //         }

        //         dropDown.stop(false, true).slideToggle();

        //         j.preventDefault();
        //     });*/

        //     // 전체삭제
        //     $(document).on('click','.waste_btn',function(){
        //         wasteBtn('slip_bet_ing');
        //     });
        // }); // end ready
        
        // // 베팅판 보여주기
        // function openBetData(fixture_id){
        //     console.log('openBetData : '+fixture_id);
        //     console.log('selectFixtureId : '+selectFixtureId);
        //     //selectFixtureId = fixture_id;
        //     let betting_html= '';
        //     const today = new Date();
            
        //     // 이미 불러왔으면 불러오지 않는다. livescorelist에서 반복됨
        //     /*if($(".fixture_"+selectFixtureId).length > 0){
        //         //$(".fixture_"+selectFixtureId).slideDown();
        //         return;
        //     }*/
            
        //     sports.forEach(function (sports_id) {
        //         //console.log(live_data);
        //         let list = live_data[sports_id];
        //         if(!list) {
        //             return true;
        //         }
        //         //console.log('openBetData : '+list);
        //         for (const[fixtureKey, fixture_list] of Object.entries(list)) {
        //             if(fixture_id != fixtureKey) continue;
        //             for (const[memuKey, menu_list] of Object.entries(fixture_list)) {
        //                 let isMainBetLock = false;
        //                 // 배구, 야구는 메인만 존재
        //                 // 갬블은 메인메뉴 개념이 없다.
        //                 //if(memuKey > 0) continue;
        //                 betting_html += "<li class='bet_list1_wrap fixture_"+fixtureKey+"' name='fixture_"+fixtureKey+"' style='display:block'>";
        //                 for (const[marketKey, game_list] of Object.entries(menu_list)) {
        //                     let markets_name = game_list[0]['markets_name_origin'];
        //                     let bGameKey = '';
        //                     // 마켓명 표기
        //                     $sportsLineColor = getSportsLineColor(sports_id);
                            
        //                     betting_html += "<a href='#'>"+
        //                             "<div class='bet_list1_wrap_in_title "+$sportsLineColor+"'>"+markets_name+"<span class='bet_list1_wrap_in_title_right'></span></div>"+
        //                             "</a>"+
        //                             "<ul class='bet_list1_wrap_in_new' id='market_"+marketKey+"' style='display:block'>"+
        //                             "<table width='100%' border='0' cellspacing='0' cellpadding='0' style='padding: 5px 0 0 0;'>";
        //                     for (const[bKey, game] of Object.entries(game_list)) {
        //                         bGameKey = game['fixture_id']+'_'+game['markets_id']+'_'+game['bet_base_line']+'_'+game['providers_id'];
        //                         markets_name = game['markets_name'];
        //                         let markets_name_origin = game['markets_name_origin'];
        //                         let markets_display_name = game['markets_display_name'];
        //                         //const timeValue = new Date(game['fixture_start_date']);
        //                         //let betweenTime = Math.floor((timeValue.getTime() - today.getTime()) / 1000);
        //                         //betweenTime = 700;
        //                         const checkTime = 600;
        //                         /*let b_find = false;
        //                         for(const betData of game['bet_data']){
        //                             if(betData['bet_status'] == 2){
        //                                 b_find = true;
        //                                 break;
        //                             }
        //                         }

        //                         if(game['bet_status'] != 1 || true == b_find) continue;*/
        //                         //thisMarkettotalCnt += 1;
        //                         betting_html += "<tr>";
        //                         // 승무패
        //                         if(1 == game['menu']) {
        //                             game['bet_data'].forEach(function(betData) {
        //                                 if(betData['bet_name'] === '1'){
        //                                     game['win_bet_id'] = betData['bet_id'];
        //                                     game['win'] = betData['bet_price'];
        //                                 }else if(betData['bet_name'] === '2'){
        //                                     game['lose_bet_id'] = betData['bet_id'];
        //                                     game['lose'] = betData['bet_price'];
        //                                 }else{
        //                                     game['draw_bet_id'] = betData['bet_id'];
        //                                     game['draw'] = betData['bet_price'];
        //                                 }
        //                             })
        //                             /*betting_html += "<td class=\"sports_table_in_1 odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + game['win'] +"\" data-odds-type=\"win\" data-bet-id=" + game['win_bet_id'] + " data-bet-price=" + game['win'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "      <div class=\"sports_v_l\">"+ game['fixture_participants_1_name'] +"</div><div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['win_bet_id'] +"\">"+ game['win'] +"</div>\n"+
        //                                 "    </td>\n";
        //                             if(game['draw']){
        //                                 betting_html +=
        //                                 "    <td class=\"sports_table_in_xo odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + game['win'] +"\" data-odds-type=\"draw\" data-bet-id=" + game['draw_bet_id'] + " data-bet-price=" + game['draw'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "      <div class=\"sports_v_l\">무</div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['draw_bet_id'] +"\">"+ game['draw'] +"</div>\n"+
        //                                 "    </td>\n";
        //                             }
        //                             betting_html +=
        //                                 "    <td class=\"sports_table_in_2 odds_btn table_w30p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds-type=\"lose\" data-bet-id=" + game['lose_bet_id'] + " data-bet-price=" + game['lose'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "        <div class=\"sports_l_l\">"+ game['fixture_participants_2_name'] +"</div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + game['lose_bet_id'] +"\">" + game['lose'] + "</div>\n"+
        //                                 "    </td>\n";*/

        //                             // 배당 표기
        //                             if (Object.keys(game['bet_data']).length == 3) {
        //                                 if((1 == game['bet_status'] && 2 == game['display_status'])){
        //                                     betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                             " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
        //                                             " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                             ">"+game['fixture_participants_1_name']+" <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
        //                                     betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                             " data-odds-type='draw' data-bet-id='"+ game['draw_bet_id'] +"' data-bet-price='"+ game['draw'] +"'"+
        //                                             " data-td-cell='"+game['draw_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                             ">무 <span class='betin_right bet_font1'>"+game['draw']+"</span></td>";
        //                                     betting_html += "<td class='bet_list_td w30 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                             " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
        //                                             " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                             ">"+game['fixture_participants_2_name']+" <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
        //                                 }else{
        //                                     isMainBetLock = true;
        //                                     betting_html += "<td class='bet_list_td w30'>"+game['fixture_participants_1_name']+"<span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>"+
        //                                             "<td class='bet_list_td w30'>무<span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>"+
        //                                             "<td class='bet_list_td w30'>"+game['fixture_participants_2_name']+"<span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>";
        //                                 }
        //                             } else {
        //                                 if((1 == game['bet_status'] && 2 == game['display_status'])){
        //                                     betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                             " data-odds-type='win' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
        //                                             " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                             ">"+game['fixture_participants_1_name']+" <span class='betin_right bet_font1'>"+game['win']+"</span></td>";
        //                                     betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                             " data-odds-type='lose' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
        //                                             " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                             " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                             ">"+game['fixture_participants_2_name']+" <span class='betin_right bet_font1'>"+game['lose']+"</span></td>";
        //                                 }else{
        //                                     isMainBetLock = true;
        //                                     betting_html += "<td class='bet_list_td w50'"+
        //                                             ">"+game['fixture_participants_1_name']+"<span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>"+
        //                                             "<td class='bet_list_td w50'"+
        //                                             ">"+game['fixture_participants_2_name']+"<span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>";
        //                                 }
        //                             }
        //                             html += "</tr>";
        //                         // 핸디캡
        //                         }else if(2 == game['menu']) {
        //                             let handValue_l = 0;
        //                             let handValue_r = 0;
        //                             let handValue_c = 0;
        //                             let tm_l = '';
        //                             let tm_r = '';
        //                             let bet_line = bet_line_second = 0;
        //                             for (const[bKey, value] of Object.entries(game['bet_data'])) {
        //                                 if(value['bet_name'] == 1) {
        //                                     game['win'] = value['bet_price'];
        //                                     game['win_bet_id'] = value['bet_id'];
        //                                     game['win_bet_line'] = value['bet_line'];
        //                                     //handValue_l = value['bet_line'].split(' ')[0];
        //                                     bet_line = Number(value['bet_line'].split(' ')[0]);
        //                                     if(typeof value['bet_line'].split(' ')[1] != 'undefined'){
        //                                         bet_line_second = value['bet_line'].split(' ')[1];
        //                                         bet_line_second = bet_line_second.replace('(', '');
        //                                         bet_line_second = bet_line_second.replace(')', '');
        //                                         bet_line_second = bet_line_second.split('-');
        //                                         handValue_l = bet_line + Number(bet_line_second[0]) - Number(bet_line_second[1]);
        //                                     }else{
        //                                         handValue_l = bet_line;
        //                                     }

        //                                     if(handValue_l > 0)
        //                                         handValue_l = '+' + handValue_l.toFixed(1);
        //                                     else
        //                                         handValue_l = handValue_l.toFixed(1);

        //                                     handValue_l = handValue_l == 'NAN' ? value.bet_line : handValue_l;
        //                                     tm_l = game['fixture_participants_1_name'] +"("+handValue_l+")";

        //                                     if(13 == game['markets_id']){
        //                                         let homeScore = Number(value['bet_line'].split(':')[0]);
        //                                         let awayScore = Number(value['bet_line'].split(':')[1]);
        //                                         handValue_l = homeScore - awayScore;
        //                                         tm_l = '승';
        //                                     }

        //                                 }else if(value['bet_name'] == 2) {
        //                                     game['lose'] = value['bet_price'];
        //                                     game['lose_bet_id'] = value['bet_id'];
        //                                     game['lose_bet_line'] = value['bet_line'];
        //                                     //handValue_r = value['bet_line'].split(' ')[0];
        //                                     bet_line = Number(value['bet_line'].split(' ')[0]);
        //                                     if(typeof value['bet_line'].split(' ')[1] != 'undefined'){
        //                                         bet_line_second = value['bet_line'].split(' ')[1];
        //                                         bet_line_second = bet_line_second.replace('(', '');
        //                                         bet_line_second = bet_line_second.replace(')', '');
        //                                         bet_line_second = bet_line_second.split('-');
        //                                         handValue_r = bet_line + Number(bet_line_second[0]) - Number(bet_line_second[1]);
        //                                     }else{
        //                                         handValue_r = bet_line;
        //                                     }

        //                                     if(handValue_r > 0)
        //                                         handValue_r = '+' + handValue_r.toFixed(1);
        //                                     else
        //                                         handValue_r = handValue_r.toFixed(1);

        //                                     handValue_r = handValue_r == 'NAN' ? value.bet_line : handValue_r;
        //                                     tm_r = game['fixture_participants_2_name'] +"("+handValue_r+")";

        //                                     if(13 == game['markets_id']){
        //                                         let homeScore = Number(value['bet_line'].split(':')[0]);
        //                                         let awayScore = Number(value['bet_line'].split(':')[1]);
        //                                         handValue_r = homeScore - awayScore;
        //                                         tm_r = '패';
        //                                     }
        //                                 }else {
        //                                     game['draw'] = value['bet_price'];
        //                                     game['draw_bet_id'] = value['bet_id'];
        //                                     game['draw_bet_line'] = value['bet_line'];
        //                                     let homeScore = Number(value['bet_line'].split(':')[0]);
        //                                     let awayScore = Number(value['bet_line'].split(':')[1]);
        //                                     handValue_c = homeScore - awayScore;
        //                                 }
        //                             }

        //                             // 배당 표기
        //                             if(1 == game['bet_status'] && 2 == game['display_status'] && false == isMainBetLock){
        //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                         " data-odds-type='"+game['fixture_participants_1_name']+"("+ handValue_l +")' data-bet-id='"+ game['win_bet_id'] +"' data-bet-price='"+ game['win'] +"'"+
        //                                         " data-td-cell='"+game['win_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                         ">"+game['fixture_participants_1_name']+"(" + handValue_l + ") <span class='betin_right bet_font1'><img src='/assets_m//assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['win']+"</span></td>";
        //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ game['bet_status'] +"'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                         " data-odds-type='"+game['fixture_participants_2_name']+"("+ handValue_r +")' data-bet-id='"+ game['lose_bet_id'] +"' data-bet-price='"+ game['lose'] +"'"+
        //                                         " data-td-cell='"+game['lose_bet_id']+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                         ">"+game['fixture_participants_2_name']+"(" + handValue_r + ") <span class='betin_right bet_font1'><img src='/assets_m//assets_w/images/icon_h.gif' style='margin-right: 5px'>"+game['lose']+"</span></td>";
        //                                 betting_html += "</tr>";
        //                             }else{
        //                                 betting_html += "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
        //                                         ">"+game['fixture_participants_1_name']+"(" + handValue_l + ") <span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>"+
        //                                         "<td class='bet_list_td w50' data-bet-status='"+ game['bet_status'] + "'"+
        //                                         ">"+game['fixture_participants_2_name']+"(" + handValue_r + ") <span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>";
        //                             }
        //                         }else if(3 == game['menu']) {
        //                                     let over = 0;
        //                                     let over_bet_id = 0;
        //                                     let over_status = 0;
        //                                     let over_base_line = '';
        //                                     let under = 0;
        //                                     let under_bet_id = 0;
        //                                     let under_status = 0;
        //                                     let under_base_line = '';
        //                             game['bet_data'].forEach(function(betData) {
        //                                 if(betData['bet_name'] === 'Over'){
        //                                     over = betData['bet_price'];
        //                                     over_bet_id = betData['bet_id'];
        //                                     over_status = betData['bet_status'];
        //                                     over_base_line = betData['bet_base_line'];
        //                                 }else{
        //                                     under = betData['bet_price'];
        //                                     under_bet_id = betData['bet_id'];
        //                                     under_status = betData['bet_status'];
        //                                     under_base_line = betData['bet_base_line'];
        //                                 }
        //                             })
                                    
        //                             // 배당 표기
        //                             if(1 == over_status && 2 == game['display_status'] && false == isMainBetLock){
        //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ over_status + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                         " data-odds-type='오버("+ over_base_line +")' data-bet-id='"+ over_bet_id  +"' data-bet-price='"+ over +"'"+
        //                                         " data-td-cell='"+over_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                         ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='/assets_m//assets_w/images/arr2.gif' style='margin-right: 5px'>"+over+"</span></td>";
        //                                 betting_html += "<td class='bet_list_td w50 odds_btn' data-bet-status='"+ under_status + "'  data-index='"+ bGameKey+"'"+ " data-fixture-id='"+ fixture_id+"'"+
        //                                         " data-odds-type='언더("+ under_base_line +")' data-bet-id='"+ under_bet_id  +"' data-bet-price='"+ under +"'"+
        //                                         " data-td-cell='"+under_bet_id+"_"+game['fixture_start_date']+"' data-markets_name='"+ markets_name +"'"+
        //                                         " data-markets_name_origin='"+markets_name_origin+"' data-markets_display_name='"+ markets_display_name +"'"+
        //                                         ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='/assets_m//assets_w/images/arr1.gif' style='margin-right: 5px'>"+under+"</span></td>";
        //                             }else{
        //                                 betting_html += "<td class='bet_list_td w50' data-bet-status='"+ over_status + "'"+
        //                                         ">오버("+ over_base_line + ") <span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>"+
        //                                         "<td class='bet_list_td w50' data-bet-status='"+ under_status + "'"+
        //                                         ">언더("+ under_base_line + ") <span class='betin_right bet_font1'><img src='//assets_w/images/icon_lock.png' alt='lock' width='13'></span></td>";
        //                             }
        //                             betting_html += "</tr>";
        //                         // 기타
        //                         }else if(4 == game['menu']) {
        //                             let yesBetPrice = '';
        //                             let yesBetId = '';
        //                             let noBetPrice = '';
        //                             let noBetId = '';
        //                             game['bet_data'].forEach(function(betData) {
        //                                 if(betData['bet_name'] === 'Yes'){
        //                                     yesBetPrice = betData['bet_price'];
        //                                     yesBetId = betData['bet_id'];
        //                                 }else{
        //                                     noBetPrice = betData['bet_price'];
        //                                     noBetId = betData['bet_id'];
        //                                 }
        //                             })

        //                             /*if(game['bet_data'][0]['bet_name'] === 'No'){
        //                                 let betData_no = game['bet_data'][0];
        //                                 let betData_yes = game['bet_data'][1];
        //                                 let display_bet_name_no = '아니요';//betNameToDisplay_new(betData_no['bet_name'],game['markets_id']);
        //                                 let display_bet_name_yes = '예';//betNameToDisplay_new(betData_yes['bet_name'],game['markets_id']);

        //                                 betting_html += "<tr>"
        //                                 betting_html += "<td class=\"sports_table_in_1 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + yesBetPrice +" \" data-odds-type=\"" + display_bet_name_yes + "\" data-bet-id=" + yesBetId + " data-bet-price=" + yesBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "        <div class=\"sports_v_l\">"+ display_bet_name_yes +" </div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + yesBetId +"\">"+ yesBetPrice +" </div>\n"+
        //                                 "    </td>\n"+
        //                                 "    <td class=\"sports_table_in_2 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + noBetPrice + "\" data-odds-type=\"" + display_bet_name_no + "\" data-bet-id=" + noBetId + " data-bet-price=" + noBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "        <div class=\"sports_l_l\">"+ display_bet_name_no +" </div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + noBetId +"\">" + noBetPrice + " </div>\n"+
        //                                 "    </td>\n";
        //                                 betting_html += "</tr>";
        //                             }else if(game['bet_data'][1]['bet_name'] === 'No'){
        //                                 let betData_no = game['bet_data'][1];
        //                                 let betData_yes = game['bet_data'][0];
        //                                 let display_bet_name_no = '아니요';//betNameToDisplay_new(betData_no['bet_name'],game['markets_id']);
        //                                 let display_bet_name_yes = '예';//betNameToDisplay_new(betData_yes['bet_name'],game['markets_id']);

        //                                 betting_html += "<tr>"
        //                                 betting_html += "<td class=\"sports_table_in_1 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + yesBetPrice +" \" data-odds-type=\"" + display_bet_name_yes + "\" data-bet-id=" + yesBetId + " data-bet-price=" + yesBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "        <div class=\"sports_v_l\">"+ display_bet_name_yes +" </div> <div class=\"sports_v_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + yesBetId +"\">"+ yesBetPrice +" </div>\n"+
        //                                 "    </td>\n"+
        //                                 "    <td class=\"sports_table_in_2 odds_btn table_w50p\" data-index=\""+ bGameKey +"\" data-fixture-id=\""+ game['fixture_id'] +"\" data-odds=\"" + noBetPrice + "\" data-odds-type=\"" + display_bet_name_no + "\" data-bet-id=" + noBetId + " data-bet-price=" + noBetPrice + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}` + ">\n"+
        //                                 "        <div class=\"sports_l_l\">"+ display_bet_name_no +" </div> <div class=\"sports_l_r\" id=\"betInfo_"+ game['fixture_id'] + '_' + noBetId +"\">" + noBetPrice + " </div>\n"+
        //                                 "    </td>\n";
        //                                 betting_html += "</tr>";
        //                             }else{
        //                                 let count = game['bet_data'].length;
        //                                 for(let i=0; i<count; ++i){
        //                                     let betData = game['bet_data'][i];
        //                                     //let display_bet_name = StatusUtil::betNameToDisplay_new(betData['bet_name']);
        //                                     let display_bet_name = betData['bet_name'];
        //                                     betting_html += "<tr>"
        //                                             +"<td class='sports_table_in_1 odds_btn' data-index=\"" + bGameKey + "\" data-odds-type=\"win\" data-bet-id=" + betData['bet_id'] + " data-bet-price='"+betData['bet_price'] + ` data-leagues_m_bet_money=${game['leagues_m_bet_money']}`+"'>"
        //                                             +"<div class='sports_v_l'>"+display_bet_name+" </div> <div class='sports_v_r'>"+betData['bet_price']+"</div>"
        //                                             +"</td>";
        //                                     if((i+1)%3 == 0){
        //                                         betting_html += "</tr>"
        //                                                 +"<tr>";
        //                                     }
        //                                 }
        //                                 betting_html += "</tr>";
        //                             }*/
        //                         } // end game['menu']
        //                         betting_html += "</tr>\n";

        //                         //betListIndex ++;
        //                     } // end game_list
        //                     betting_html += "</table>";
        //                     betting_html += "</ul>";
        //                 } // end menu_list
        //             } // end fixture_list
        //         }
        //     });
            
        //     //$('[data-bet-id*="' + fixtureId + '"]
        //     // 이전경기 닫기
        //     $(".dropdown3 li").remove();
        //     //$('.dropdown3[fixture_="' + item +'"]').remove();
        //     //console.log(betting_html);
        //     $(".dropdown3").append(betting_html);
            
        //     // 배팅슬립에 있는 배당 선택처리
        //     /*$('.slip_bet_ing').each(function(item) {
        //         console.log(item);
        //         const betListIndex = $(item).data('index');
        //         //$('[data-td-cell*="' + betId + '_' + fixture_start_date + '"]').addClass('bet_on');
        //         //$('[data-bet-id*="' + betId + '"]').addClass('bet_on');
        //     });*/
        // }
    </script>
</body>
</html>