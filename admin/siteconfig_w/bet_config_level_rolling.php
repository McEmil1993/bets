
<div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>레벨별 롤링콤푸(페이백) 설정</h4>
            </a>
        </div>
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
        <!-- detail search -->
        <div class="panel search_box">
            <table class="mlist mline">
                <tr>
                    <th width="200px">죽장 페이백 주기 설정</th>
                    <td>
                    	<table class='table_noline'>
							<tr>
                                <td style="text-align: center;width: 200px">
                                    <select style="width: 160px;" name="" id="">
                                        <option value="0">일주일</option>
                                        <option value="1">24시간</option>
                                        <option value="2">실시간</option>
                                    </select>						
                                </td>
								<td style="width: 100%; padding:2px; text-align:left;">
                                    <a href="#" class="btn h30 btn_blu">저장</a>
                                </td>
                                <td style="color:#f89d1b!important">※ 일주일 : 매주 월요일 00시 기준 / ※ 24시간 : 매일 00시 기준</td>
							</tr>
						</table>	 
                    </td>
                </tr>
            </table>
        </div>
        <!-- END detail search -->

        <!-- list -->
        <div class="panel reserve">
            <div class="tline">
                <table class="mlist">
                    <tr>
                        <th rowspan="3">레벨</th>
                        <th colspan="2">죽장</th>
                        <th colspan="14">배팅 롤링 (취소/적특 제외)</th>
                        <th colspan="14">낙첨 롤링</th>
                    </tr>
                    <tr>
                        <!-- success -->
                        <th colspan="2">(입금 - 출금)</th>
                        <th colspan="2">프리매치 단폴</th>
                        <th colspan="2">프리매치 2폴 이상</th>
                        <th colspan="2">실시간 단폴</th>
                        <th colspan="2">실시간 2폴 이상</th>
                        <!-- casino/slot -->
                        <th colspan="2">카지노</th>
                        <th colspan="2">슬롯</th>
                        <!-- add power/ladder/virture/hash -->
                        <th colspan="2">미니게임</th>

                        <!-- fail -->
                        <th colspan="2">프리매치 단폴</th>
                        <th colspan="2">프리매치 2폴 이상</th>
                        <th colspan="2">실시간 단폴</th>
                        <th colspan="2">실시간 2폴 이상</th>
                        <!-- casino/slot -->
                        <th colspan="2">카지노</th>
                        <th colspan="2">슬롯</th>
                        <!-- add power/ladder/virture/hash -->
                        <th colspan="2">미니게임</th>
                    </tr>
                    <tr>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
<!-- 베팅 -->
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
<!-- 낙첨 -->
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                        <th>본인(%)</th>
                        <th>추천(%)</th>
                    </tr>
<?php
for ($i=1;$i<=10;$i++) {
    $display_myself_chex[$i] = isset($display_myself_chex[$i]) ? $display_myself_chex[$i] : 0;
    $display_recommender_chex[$i]  = isset($display_recommender_chex[$i]) ? $display_recommender_chex[$i] : 0;
   
    $display_prematch_s_myself_bet[$i] = isset($display_prematch_s_myself_bet[$i]) ? $display_prematch_s_myself_bet[$i] : 0;
    $display_prematch_s_recommender_bet[$i] = isset($display_prematch_s_recommender_bet[$i]) ? $display_prematch_s_recommender_bet[$i] : 0;

    $display_prematch_d_myself_bet[$i] = isset($display_prematch_d_myself_bet[$i]) ? $display_prematch_d_myself_bet[$i] : 0;
    $display_prematch_d_recommender_bet[$i] = isset($display_prematch_d_recommender_bet[$i]) ? $display_prematch_d_recommender_bet[$i] : 0;

    $display_inplay_s_myself_bet[$i] = isset($display_inplay_s_myself_bet[$i]) ? $display_inplay_s_myself_bet[$i] : 0;
    $display_inplay_s_recommender_bet[$i] = isset($display_inplay_s_recommender_bet[$i]) ? $display_inplay_s_recommender_bet[$i] : 0;
    
    $display_inplay_d_myself_bet[$i] = isset($display_inplay_d_myself_bet[$i]) ? $display_inplay_d_myself_bet[$i] : 0;
    $display_inplay_d_recommender_bet[$i] = isset($display_inplay_d_recommender_bet[$i]) ? $display_inplay_d_recommender_bet[$i] : 0;

    $display_casino_myself_bet[$i] = isset($display_casino_myself_bet[$i]) ? $display_casino_myself_bet[$i] : 0;
    $display_casino_recommender_bet[$i] = isset($display_casino_recommender_bet[$i]) ? $display_casino_recommender_bet[$i] : 0;

    $display_slot_myself_bet[$i] = isset($display_slot_myself_bet[$i]) ? $display_slot_myself_bet[$i] : 0;
    $display_slot_recommender_bet[$i] = isset($display_slot_recommender_bet[$i]) ? $display_slot_recommender_bet[$i] : 0;

    $display_powerball_myself_bet[$i] = isset($display_powerball_myself_bet[$i]) ? $display_powerball_myself_bet[$i] : 0;
    $display_powerball_recommender_bet[$i] = isset($display_powerball_recommender_bet[$i]) ? $display_powerball_recommender_bet[$i] : 0;


    // ---- > 

    $display_prematch_s_myself_lose[$i] = isset($display_prematch_s_myself_lose[$i]) ? $display_prematch_s_myself_lose[$i] : 0;
    $display_prematch_s_recommender_lose[$i] = isset($display_prematch_s_recommender_lose[$i]) ? $display_prematch_s_recommender_lose[$i] : 0;

    $display_prematch_d_myself_lose[$i] = isset($display_prematch_d_myself_lose[$i]) ? $display_prematch_d_myself_lose[$i] : 0;
    $display_prematch_d_recommender_lose[$i] = isset($display_prematch_d_recommender_lose[$i]) ? $display_prematch_d_recommender_lose[$i] : 0;

    $display_inplay_s_myself_lose[$i] = isset($display_inplay_s_myself_lose[$i]) ? $display_inplay_s_myself_lose[$i] : 0;
    $display_inplay_s_recommender_lose[$i] = isset($display_inplay_s_recommender_lose[$i]) ? $display_inplay_s_recommender_lose[$i] : 0;
    
    $display_inplay_d_myself_lose[$i] = isset($display_inplay_d_myself_lose[$i]) ? $display_inplay_d_myself_lose[$i] : 0;
    $display_inplay_d_recommender_lose[$i] = isset($display_inplay_d_recommender_lose[$i]) ? $display_inplay_d_recommender_lose[$i] : 0;

    $display_casino_myself_lose[$i] = isset($display_casino_myself_lose[$i]) ? $display_casino_myself_lose[$i] : 0;
    $display_casino_recommender_lose[$i] = isset($display_casino_recommender_lose[$i]) ? $display_casino_recommender_lose[$i] : 0;

    $display_slot_myself_lose[$i] = isset($display_slot_myself_lose[$i]) ? $display_slot_myself_lose[$i] : 0;
    $display_slot_recommender_lose[$i] = isset($display_slot_recommender_lose[$i]) ? $display_slot_recommender_lose[$i] : 0;

    $display_powerball_myself_lose[$i] = isset($display_powerball_myself_lose[$i]) ? $display_powerball_myself_lose[$i] : 0;
    $display_powerball_recommender_lose[$i] = isset($display_powerball_recommender_lose[$i]) ? $display_powerball_recommender_lose[$i] : 0;


?>                    
            <tr>
                <td><?=$i?></td>
<!-- 죽장 -->
                <td><input name="myself_chex" id="myself_chex<?=$i?>" type="text" class="" style="width: 100%" data-id="<?=$idx_chex[$i];?>" placeholder="" value="<?=$display_myself_chex[$i]; ?> "/></td>
                <td><input name="recommender_chex" id="recommender_chex<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_recommender_chex[$i]; ?>"/></td>
<!-- 베팅 -->
                <td><input name="prematch_s_myself_bet" id="prematch_s_myself_bet<?=$i?>" data-id="<?=$idx_prematch_s[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_s_myself_bet[$i]; ?>"/></td>
                <td><input name="prematch_s_recommender_bet" id="prematch_s_recommender_bet<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_s_recommender_bet[$i]; ?>"/></td>
                <td><input name="prematch_d_myself_bet" id="prematch_d_myself_bet<?=$i?>" data-id="<?=$idx_prematch_d[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_d_myself_bet[$i]; ?>"/></td>
                <td><input name="prematch_d_recommender_bet" id="prematch_d_recommender_bet<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_d_recommender_bet[$i]; ?>"/></td>
                <td><input name="inplay_s_myself_bet" id="inplay_s_myself_bet<?=$i?>" data-id="<?=$idx_inplay_s[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_s_myself_bet[$i]; ?>"/></td>
                <td><input name="inplay_s_recommender_bet" id="inplay_s_recommender_bet<?=$i?>"  type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_s_recommender_bet[$i]; ?>"/></td>
                <td><input name="inplay_d_myself_bet" id="inplay_d_myself_bet<?=$i?>" data-id="<?=$idx_inplay_d[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_d_myself_bet[$i]; ?>"/></td>
                <td><input name="inplay_d_recommender_bet" id="inplay_d_recommender_bet<?=$i?>"  type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_d_recommender_bet[$i]; ?>"/></td>
                <td><input name="casino_myself_bet"  id="casino_myself_bet<?=$i?>" data-id="<?=$idx_casino[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_casino_myself_bet[$i]; ?>"/></td>
                <td><input name="casino_recommender_bet" id="casino_recommender_bet<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_casino_recommender_bet[$i]; ?>"/></td>
                <td><input name="slot_myself_bet" id="slot_myself_bet<?=$i?>" data-id="<?=$idx_slot[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_slot_myself_bet[$i]; ?>"/></td>
                <td><input name="slot_recommender_bet" id="slot_recommender_bet<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_slot_recommender_bet[$i]; ?>"/></td>
                <td><input name="powerball_myself_bet" id="powerball_myself_bet<?=$i?>" data-id="<?=$idx_powerball[$i];?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_powerball_myself_bet[$i]; ?>"/></td>
                <td><input name="powerball_recommender_bet" id="powerball_recommender_bet<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_powerball_recommender_bet[$i]; ?>"/></td>
<!-- 낙첨 -->
                <td><input name="prematch_s_myself_lose" id="prematch_s_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_s_myself_lose[$i]; ?>"/></td>
                <td><input name="prematch_s_recommender_lose" id="prematch_s_recommender_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_s_recommender_lose[$i]; ?>"/></td>
                <td><input name="prematch_d_myself_lose" id="prematch_d_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_d_myself_lose[$i]; ?>"/></td>
                <td><input name="prematch_d_recommender_lose" id="prematch_d_recommender_lose<?=$i?>"  type="text" class="" style="width: 100%" placeholder="" value="<?=$display_prematch_d_recommender_lose[$i]; ?>"/></td>
                <td><input name="inplay_s_myself_lose" id="inplay_s_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_s_myself_lose[$i]; ?>"/></td>
                <td><input name="inplay_s_recommender_lose" id="inplay_s_recommender_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_s_recommender_lose[$i]; ?>"/></td>
                <td><input name="inplay_d_myself_lose" id="inplay_d_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_d_myself_lose[$i]; ?>"/></td>
                <td><input name="inplay_d_recommender_lose" id="inplay_d_recommender_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_inplay_d_recommender_lose[$i]; ?>"/></td>
                <td><input name="casino_myself_lose" id="casino_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_casino_myself_lose[$i]; ?>"/></td>
                <td><input name="casino_recommender_lose" id="casino_recommender_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_casino_recommender_lose[$i]; ?>"/></td>
                <td><input name="slot_myself_lose" id="slot_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_slot_myself_lose[$i]; ?>"/></td>
                <td><input name="slot_recommender_lose" id="slot_recommender_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_slot_recommender_lose[$i]; ?>"/></td>
                <td><input name="powerball_myself_lose" id="powerball_myself_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_powerball_myself_lose[$i]; ?>"/></td>
                <td><input name="powerball_recommender_lose" id="powerball_recommender_lose<?=$i?>" type="text" class="" style="width: 100%" placeholder="" value="<?=$display_powerball_recommender_lose[$i]; ?>"/></td>
            </tr>
<?php 
}
?>                    
                </table>
            </div>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="save_rolling();" class="btn h30 btn_blu">등 록</a>
                </div>
            </div>            
        </div>
        <!-- END list -->
</form>        