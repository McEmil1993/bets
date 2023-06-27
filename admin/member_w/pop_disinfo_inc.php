<!-- 회원 기본정보 -->                            
                            <div>
                            	<table class="mlist">
                                    <tr>
                                    	<th style="padding: 0 20px;">아이디/닉네임<font color='red'>*</font></th>
                                        <td style="padding: 2px;text-align:left">
                                        	<table class="table_noline" style="padding: 2px;">
                                        		<tr>
                                        			<td style="padding: 2px;">
                                        				<input type="text" value="<?=$db_data_mem[0]['id']?>" disabled />
                                        			</td>
                                        			<td style="padding: 2px;">	
                                        				<input type="text" name="u_nick" id="u_nick" value="<?=$db_data_mem[0]['nick_name']?>" maxlength="20"/>
                                        			</td>
                                        		</tr>
                                        	</table>
                                        	
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th style="width: 120px;">비밀번호 <font color='red'>*</font></th>
                                        <td style="padding: 2px;text-align:left">
                                        	<table class="table_noline">
                                        		<tr>
                                        			<td style="padding: 2px;">
                                        				<input type="password" name="u_pass" id="u_pass" maxlength="20"/>
                                        			</td>
                                        		</tr>
                                        	</table>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>연락처 <font color='red'>*</font></th>
                                        <td style="padding: 2px;text-align:left">
                                        <?php 
                                        
                                        $db_hp = explode('-',true === isset($db_data_mem[0]['call']) ? $db_data_mem[0]['call'] : '');
                                        $db_hp_cnt = count($db_hp);
                                        if ($db_hp_cnt == 3) {
                                            $db_hp_2 = $db_hp[1];
                                            $db_hp_3 = $db_hp[2];
                                        }
                                        else {
                                            $db_hp = str_replace('-', '', true === isset($db_data_mem[0]['call']) ? $db_data_mem[0]['call'] : '');
                                            $db_hp_2 = substr($db_hp,3,4);
                                            $db_hp_3 = substr($db_hp,7,4);
                                        }
                                        
                                        ?>
                                        	<table class="table_noline">
                                        		<tr>
                                                    <td style="padding: 2px;">
                                                        <input style="width:90px;" name="u_hp01" id="u_hp01" value="010" disabled>
                                                    </td>
                                        			<td style="padding: 2px;">
                                        				<input type="text" name="u_hp02" id="u_hp02" value="<?=$db_hp_2?>" maxlength="4"/>
                                        			</td>
                                        			<td style="padding: 2px;">	
                                        				<input type="text" name="u_hp03" id="u_hp03" value="<?=$db_hp_3?>" maxlength="4"/>
                                        			</td>
                                        		</tr>
                                        	</table>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>추천가능여부</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<table class="table_noline">
                                        		<tr>
                                        			<td style="padding: 2px;text-align:left">
                                        				<select style="width:160px;" name="u_is_recomm" id="u_is_recomm">
                                        					<option value="Y" <?php if ($db_data_mem[0]['is_recommend']=='Y') {echo "selected";}?>>가능</option>
                                        					<option value="N" <?php if ($db_data_mem[0]['is_recommend']=='N') {echo "selected";}?>>불가능</option>
                                        				</select>
                                        			</td>
                                                    <td> 추천 <?= number_format($re_user_cnt) ?>명 | 탈퇴 <?= number_format($re_out_user_cnt) ?>명</td>
                                        		</tr>
                                        	</table>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>회원상태</th>
                                        <td style="padding: 2px;text-align:left">
                                            <table class="table_noline">
                                                <tr>
                                                    <td style="padding: 2px;text-align:left">
                                                        <select style="width: 160px;" name="u_status" id="u_status">
                                                                <option value="1" <?php if ($db_data_mem[0]['status']=='1') {echo "selected";}?>>사용중</option>
                                                                <option value="2" <?php if ($db_data_mem[0]['status']=='2') {echo "selected";}?>>계정 정지</option>
                                                                <option value="3" <?php if ($db_data_mem[0]['status']=='3') {echo "selected";}?>>탈퇴</option>
                                                                <option value="11" <?php if ($db_data_mem[0]['status']=='11') {echo "selected";}?>>승인 대기 회원</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>추천코드</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fl">
            										<div class="" style="width:1px"></div>
                    								<div class="">
                        								<input type="text" class="" id="u_recode" name="u_recode" value="<?=$db_recode?>"/>
                    								</div>
                    								<div class="" style="width:10px"></div>
                                                                                    <div ><a href="javascript:;" onClick="setRecommCode('r','<?=$db_recode?>');" class="btn h30 btn_blu" style="color:#fff">랜덤코드적용</a></div>
                                                                                    <div class="" style="width:5px"></div>
                                                                                    <div ><a href="javascript:;" onClick="setRecommCode('r2','<?=$db_recode?>');" class="btn h30 btn_blu" style="color:#fff">수동코드적용</a></div>
                                                                                    <div class="" style="width:5px"></div>
                                                                                    <div><a href="javascript:;" onClick="setRecommCode('d');" class="btn h30 btn_mdark" style="color:#fff">코드제거</a></div>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>총판레벨/유형</th>
                                        <td style="padding: 2px;text-align:left">
                                            <table class="table_noline">
                                                <tr>
                                                    <td style="padding: 2px;text-align:left">
                                                        <select style="width: 160px;" name="u_business" id="u_business">
                                                            <?php foreach ($db_dists as $key => $value) { ?>
                                                                <option value="<?=$value['id']?>" <?=$value['id'] == $db_data_mem[0]['u_business']?'selected':'' ?>><?=$value['name']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>/</td>
                                                    <td style="padding: 2px;text-align:left">
                                                       <select style="width: 140px;" id="dist_types" name="dist_types">
                                                            <?php

                                                                $i = 0;
                                                                if (!empty($db_dist_types)) {
                                                                    foreach ($db_dist_types as $row) {
                                                                        $id = $row['id'];
                                                                        $name = $row['name'];
                                                                        echo "selected";
                                                                        ?>
                                                                    <option value="<?=$id?>" <?=$id == $db_data_mem[0]['dist_type']?'selected':'' ?>><?=$name?></option> 
                                                            <?php   }
                                                                }
                                                             ?>
                                                        </select><!-- comment -->
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>소속총판</th>
                                        <td style="padding: 2px;text-align:left">
                                            <table class="table_noline">
                                                <tr>
                                                    <td style="padding: 2px;text-align:left">
                                                        <select style="width: 100%;" name="u_recomm_user" id="u_recomm_user">
                                                            <option value="0">-- 없음 --</option>
                                                            <?php foreach ($db_dist_list as $key => $value) { ?>
                                                                <option value="<?=$value['idx']?>" <?=$value['idx'] == $db_data_mem[0]['recommend_member']?'selected':'' ?>><?=$value['id']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div style="height: 10px"></div>
<!-- 계좌, 보유머니 정보 -->
                            <div>
                            	<table class="mlist">
                            		<tr>
                                    	<th style="width: 120px;">계좌정보 <font color='red'>*</font></th>
                                        <td style="padding: 2px;text-align:left">
                                        	<table class="table_noline">
                                        		<tr>
                                        			<td style="padding: 2px;text-align:left">
                                            			<div class="panel_tit" style="height: 5px;">
                        									<div class="search_form fl">
                        										<div class="" style="width:1px"></div>
                                								<div class="">
                                    								<select style="width: 160px;" name="u_account_bank" id="u_account_bank">
                                                    					<option value="">-- 은행선택 --</option>
                                                    					<?php 
                                                    					if(!empty($db_data_bank)){
                                                    					    foreach($db_data_bank as $row) {
                                                                        ?>
                                                                        <option value="<?=$row['account_code']?>" <?php if ($db_data_mem[0]['account_bank']==$row['account_code']) {echo "selected";}?>><?=$row['account_name']?></option>
                                                                        <?php 
                                                                             }
                                                                         }
                                                                        ?>
                                                    				</select>
                                								</div>
                                								<div class="">
                                    								<input type="text" name="u_account_number" id="u_account_number" class="" style="width: 200px;" value="<?=true === isset($db_data_mem[0]['account_number']) ? $db_data_mem[0]['account_number'] : ''?>" maxlength="30"/>
                                								</div>
                        									</div>
                        								</div>
                                        			</td>
                                        		</tr>
                                        	</table>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>예금주 정보</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fl">
            										<div class="" style="width:1px"></div>
                    								<div class="">
                        								<input type="text" name="u_account_name" id="u_account_name" class="" style="width: 160px;" value="<?=$db_data_mem[0]['account_name']?>"/>
                    								</div>
                    								<div class="" style="width:10px"></div>
                									<div >환전암호</div>
                									<div class="" style="width:5px"></div>
                									<div class="">
                        								<input type="password" name="u_account_pass" id="u_account_pass" class="" maxlength="20"/>
                    								</div>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                    	<th>보유 머니/변경</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fl">
                    								<div class="">
                        								<input type="text" name="db_u_money" id="db_u_money" class="" style="width:100px; text-align:right" value="<?=number_format($db_data_mem[0]['money'])?>" disabled/>
                    								</div>
                    								<div class="">
                        								<input type="text" name="u_money" id="u_money" class=""  style="width:120px" placeholder="변경 머니 입력"/>
                    								</div>
                                                    <div><input type="text" name="u_money_comment" id="u_money_comment" class=""  style="width:140px" placeholder="변경사유*필수*"/></div>
                                                    <div><a href="javascript:;" onClick="setMoneyPoint('money','p');" class="btn h30 btn_blu" style="color:#fff">지급</a></div>
                									<div><a href="javascript:;" onClick="setMoneyPoint('money','m');" class="btn h30 btn_mdark" style="color:#fff">차감</a></div>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>보유 포인트</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fl">
                    								<div class="">
                        								<input type="text" name="db_u_point" id="db_u_point" class="" style="width:100px; text-align:right" value="<?=number_format($db_data_mem[0]['point'] + $db_data_mem[0]['betting_p'])?>" disabled/>
                    								</div>
                    								<div class="">
                        								<input type="text" name="u_point" id="u_point" class=""  style="width:120px" placeholder="변경 포인트 입력"/>
                    								</div>
                                                    <div><input type="text" name="u_point_comment" id="u_point_comment" class=""  style="width:140px" placeholder="변경사유*필수*"/></div>
                                                    <div><a href="javascript:;" onClick="setMoneyPoint('point','p');" class="btn h30 btn_blu" style="color:#fff">지급</a></div>
                									<div><a href="javascript:;" onClick="setMoneyPoint('point','m');" class="btn h30 btn_mdark" style="color:#fff">차감</a></div>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div style="height: 10px"></div>
                            <!-- 베팅 게임 정보 -->
							<div>
                            	<table class="mlist">
                                    <tr>
                                        <td colspan="2" style="background-color:#6F6F6F;color:#fff">총판 정산 정보</td>
                                        <td colspan="1" style="background-color:#6F6F6F;color:#fff">현재</td>
                                        <td colspan="1" style="background-color:#6F6F6F;color:#fff">설정</td>
                                    </tr>
                                    <tr>
                                        <th rowspan="13" style="padding: 2px;width: 120px;">베팅롤링</th>
                                    	<th>프리매치 단폴 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_pre_s_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_s_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_pre_s_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_s_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                    	<th>프리매치 멀티 2폴 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_pre_d_2_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_2_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_pre_d_2_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_2_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>프리매치 멀티 3폴 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_pre_d_3_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_3_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_pre_d_3_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_3_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>프리매치 멀티 4폴 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_pre_d_4_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_4_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_pre_d_4_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_4_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>프리매치 멀티 5폴이상 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_pre_d_5_more_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_5_more_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_pre_d_5_more_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_pre_d_5_more_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>실시간 싱글 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_real_s_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_real_s_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_real_s_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_real_s_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>실시간 2폴이상 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_real_d_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_real_d_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_real_d_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_real_d_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>미니게임 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_mini_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_mini_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_mini_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_mini_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>카지노 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_casino_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_casino_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_casino_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_casino_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>슬롯 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_slot_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_slot_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_slot_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_slot_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>E-스포츠 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_esports_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_esports_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_esports_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_esports_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>해쉬 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_hash_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_hash_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_hash_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_hash_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>홀덤 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="bet_holdem_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_holdem_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_bet_holdem_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['bet_holdem_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>죽장</th>
                                        <th>입금-출금 %</th>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="text" class="" id="pre_s_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['pre_s_fee']?>" readonly/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 2px;text-align:left">
                                            <div class="panel_tit" style="height: 5px;">
                                                <div class="search_form fl">
                                                    <div class="" style="width:1px"></div>
                                                <div class="">
                                                    <input type="number" min ="0" max="100" class="" id="set_pre_s_fee" style="width: 160px;text-align:right" value="<?=$db_dataShopConfig['pre_s_fee']?>"/>
                                                </div>
                                                <div>%</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <a href="javascript:;" onclick="confirmShopConfig(<?=$db_data_mem[0]['idx']?>);" class="btn h30 btn_blu">적용</a>
                            </div>
							<div style="height: 10px"></div>
							<div>
                            	<table class="mlist">
									<tr>
                                        <td colspan="2" style="background-color:#6F6F6F;color:#fff">추천인 종합 정보</td>
                                    </tr>
                                    <tr>
                                    	<th style="padding: 2px;width: 220px;">상위 추천인</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fr">
            										
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>추천수</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fr">
            										<?=number_format($db_recomm_tot_cnt);?>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>탈퇴수</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fr">
            										<?=number_format($db_recomm_leave_tot_cnt);?>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th>총 낙첨 포인트</th>
                                        <td style="padding: 2px;text-align:left">
                                        	<div class="panel_tit" style="height: 5px;">
            									<div class="search_form fr">
            										<?=number_format($db_tot_bet_point);?>
            									</div>
            								</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
							<div style="height: 10px"></div>
							