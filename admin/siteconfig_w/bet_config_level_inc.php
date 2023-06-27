
        <div class="title">
            <a href="javascript:;">
                <i class="mte i_settings vam ml20 mr10"></i>
                <h4>레벨별 배팅금액 설정</h4>
            </a>
        </div>
<form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
        <!-- detail search -->
        <div class="panel search_box">
        	<div style="color:#f89d1b!important">
            	※ 카지노 최대 배팅알림 금액을 설정해주세요.
            </div>
            <div style="margin-top: 3px;">
            </div>
            <table class="mlist mline">
                <tr>
                    <th width="200px">최대 배팅금액 알림설정</th>
                    <td>
                    	<table class='table_noline'>
							<tr>
								<td style="width: 210px; padding:2px; text-align:left;">
									<input id="con_reg_first" name="con_reg_first" type="text" style="width: 150px" value="0"/>
								</td>
								<td> 원  &nbsp;&nbsp; </td>
								<td style="width: 100%; padding:2px; text-align:left;"> 
									<!--<a href="#" onClick="setConfig('reg_first_charge','0');" class="btn h30 btn_blu">저장</a>-->
                                                                        <a href="#" class="btn h30 btn_blu">저장</a>
								</td>
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
                        <th rowspan="2">레벨</th>
                        <th colspan="3">클래식</th>
                        <th colspan="4">클래식 리그별배팅금액</th>
                        <th colspan="3">프리매치</th>
                        <th colspan="4">프리매치 리그별배팅금액</th>
                        <th colspan="3">실시간</th>
                        <th colspan="4">실시간 리그별배팅금액</th>
                        <th colspan="2">낙첨</th>
                    </tr>
                    <tr>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>1등급</th>
                        <th>2등급</th>
                        <th>3등급</th>
                        <th>4등급</th>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>1등급</th>
                        <th>2등급</th>
                        <th>3등급</th>
                        <th>4등급</th>
                        <th>최소</th>
                        <th>최대</th>
                        <th>상한</th>
                        <th>1등급</th>
                        <th>2등급</th>
                        <th>3등급</th>
                        <th>4등급</th>
                        <th>본인 (%)</th>
                        <th>추천인 (%)</th>
                    </tr>
<?php
for ($i=1;$i<=10;$i++) {
    $db_pre_min_money[$i] = isset($db_pre_min_money[$i]) ? $db_pre_min_money[$i] : 0;
    $db_lose_self_per[$i] = isset($db_lose_self_per[$i]) ? $db_lose_self_per[$i] : 0;
    $db_lose_recomm_per[$i] = isset($db_lose_recomm_per[$i]) ? $db_lose_recomm_per[$i] : 0;
    $db_charge_first_per[$i] = isset($db_charge_first_per[$i]) ? $db_charge_first_per[$i] : 0;
    $db_charge_per[$i] = isset($db_charge_per[$i]) ? $db_charge_per[$i] : 0;
?>                    
                    <tr>
                        <td><?=$i?></td>
                        <td><input name="classic_min_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_classic_min_money[$i])?>"/></td>
                        <td><input name="classic_max_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_classic_max_money[$i])?>"/></td>
                        <td><input name="classic_limit_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_classic_limit_money[$i])?>"/></td>
                        <td><input name="classic_dividen_1[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][20][1])?>"></td>
                        <td><input name="classic_dividen_2[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][20][2])?>"></td>
                        <td><input name="classic_dividen_3[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][20][3])?>"></td>
                        <td><input name="classic_dividen_4[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][20][4])?>"></td>
                        <td><input name="pre_min_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_pre_min_money[$i])?>"/></td>
                        <td><input name="pre_max_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_pre_max_money[$i])?>"/></td>
                        <td><input name="pre_limit_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_pre_limit_money[$i])?>"/></td>
                        <td><input name="pre_dividen_1[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][1][1])?>"></td>
                        <td><input name="pre_dividen_2[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][1][2])?>"></td>
                        <td><input name="pre_dividen_3[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][1][3])?>"></td>
                        <td><input name="pre_dividen_4[]" type="text" style="width: 100%" value="<?=number_format($dividenArr[$i][1][4])?>"></td>
                        <td><input name="real_min_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_real_min_money[$i])?>"/></td>
                        <td><input name="real_max_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_real_max_money[$i])?>"/></td>
                        <td><input name="real_limit_money[]" type="text" class="" style="width: 100%" placeholder="" value="<?=number_format($db_real_limit_money[$i])?>"/></td>
                        <td><input name="real_dividen_1[]" type="text" style="width: 100%"  value="<?=number_format($dividenArr[$i][2][1])?>"></td>
                        <td><input name="real_dividen_2[]" type="text" style="width: 100%"  value="<?=number_format($dividenArr[$i][2][2])?>"></td>
                        <td><input name="real_dividen_3[]" type="text" style="width: 100%"  value="<?=number_format($dividenArr[$i][2][3])?>"></td>
                        <td><input name="real_dividen_4[]" type="text" style="width: 100%"  value="<?=number_format($dividenArr[$i][2][4])?>"></td>
                        <td>
                        	<table class='table_noline'>
    							<tr>
    								<td>
    									<input name="lose_self_per[]" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_lose_self_per[$i]?>"/>
    								</td>
    								<td style="width: 10px; padding: 2px;text-align:left;"> %</td>
    							</tr>
    						</table>
                        	
                        </td>
                        <td>
                        	<table class='table_noline'>
    							<tr>
    								<td>
    									<input name="lose_recomm_per[]" type="text" class="" style="width: 100%" placeholder="" value="<?=$db_lose_recomm_per[$i]?>"/>
    								</td>
    								<td style="width: 10px; padding: 2px;text-align:left;"> %</td>
    							</tr>
    						</table>
                        </td>
                    </tr>
<?php 
}
?>                    
                </table>
            </div>
            <div class="panel_tit" style="margin-top: 5px;">
                <div class="" style="text-align: center">
                    <a href="javascript:;" onClick="setConfig('bet_config_level',0);" class="btn h30 btn_blu">등 록</a>
                </div>
            </div>            
        </div>
        <!-- END list -->
</form>        