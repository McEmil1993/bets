                            <div>
                                <table>
                                    <tr>
                                        <th colspan="5" style="background-color: #6F6F6F; color: #fff; text-align: left;" colspan="4">개인계좌-1</th>
                                    </tr>
                                    <tr>
                                        <th>계좌번호</th>
                                        <td>
                                            <!--<input type="text" id="personal_account_bank_1" placeholder="은행명" value="">-->
                                            <select style="width: 160px;" id="personal_account_bank_1">
                                            <option value="">-- 은행선택 --</option>
                                            <?php
                                            if (!empty($db_data_bank)) {
                                                foreach ($db_data_bank as $row) {
                                                    if (0 == strcmp($db_data_personal[0]['account_bank_1'], 'KB국민은행'))
                                                        $mem_account_bank = '국민은행';
                                                    else if (0 == strcmp($db_data_personal[0]['account_bank_1'], 'NH농협은행'))
                                                        $mem_account_bank = '농협';
                                                    else
                                                        $mem_account_bank = $db_data_personal[0]['account_bank_1'];
                                                    ?>
                                                    <option value="<?= $row['account_code'] ?>" <?php
                                                    if ($mem_account_bank == $row['account_code']) {
                                                        echo "selected";
                                                    }
                                                    ?>><?= $row['account_name'] ?></option>
                                                            <?php
                                                        }
                                            }
                                            ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" id="personal_account_number_1" placeholder="계좌번호" value="<?=true === isset($db_data_personal[0]['account_number_1']) ? $db_data_personal[0]['account_number_1'] : ''?>">
                                        </td>
                                        <td>
                                            <input type="text" id="personal_account_name_1" placeholder="예금주" value="<?=$db_data_personal[0]['account_name_1']?>">
                                        </td>
                                        <td>            
                                            <a href="javascript:set_personal_account(1)" class="btn h30 btn_blu">등록</a>
                                            <a href="javascript:set_personal_account(1)" class="btn h30 btn_mdark">수정</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="5" style="background-color: #6F6F6F; color: #fff; text-align: left;" colspan="4">개인계좌-2</th>
                                    </tr>
                                    <tr id="personal_account_2">
                                        <th>계좌번호</th>
                                        <td>
                                            <select style="width: 160px;" id="personal_account_bank_2">
                                            <option value="">-- 은행선택 --</option>
                                            <?php
                                         
                                            if (!empty($db_data_bank)) {
                                                foreach ($db_data_bank as $row) {
                                                    if (0 == strcmp($db_data_personal[0]['account_bank_2'], 'KB국민은행'))
                                                        $mem_account_bank = '국민은행';
                                                    else if (0 == strcmp($db_data_personal[0]['account_bank_2'], 'NH농협은행'))
                                                        $mem_account_bank = '농협';
                                                    else
                                                        $mem_account_bank = $db_data_personal[0]['account_bank_2'];
                                                    ?>
                                                    <option value="<?= $row['account_code'] ?>" <?php
                                                    if ($mem_account_bank == $row['account_code']) {
                                                        echo "selected";
                                                    }
                                                    ?>><?= $row['account_name'] ?></option>
                                                            <?php
                                                        }
                                            }
                                            ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" id="personal_account_number_2" placeholder="계좌번호" value="<?=true === isset($db_data_personal[0]['account_number_2']) ? $db_data_personal[0]['account_number_2'] : ''?>">
                                        </td>
                                        <td>
                                            <input type="text" id="personal_account_name_2" placeholder="예금주" value="<?=$db_data_personal[0]['account_name_2']?>">
                                        </td>
                                        <td>            
                                            <a href="javascript:set_personal_account(2)" class="btn h30 btn_blu">등록</a>
                                            <a href="javascript:set_personal_account(2)" class="btn h30 btn_mdark">수정</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                            	<table class=table_noline>
                                    <tr>
                                    	<td>
                                    		<select style="width: 200px;" name="pop_userinfo_sel_content" id="pop_userinfo_sel_content"  onchange="javascript:getUserinfoContent(this.value);" >
                                                    <option value="1" <?php if ($selContent==1) {echo "selected";}?>>종합 내역</option>
                                                    <option value="2" <?php if ($selContent==2) {echo "selected";}?>>충/환전 내역</option>
                                                    <option value="3" <?php if ($selContent==3) {echo "selected";}?>>로그인 내역</option>
                                                    <option value="4" <?php if ($selContent==4) {echo "selected";}?>>문의 내역</option>
                                                    <option value="5" <?php if ($selContent==5) {echo "selected";}?>>머니 내역</option>
                                                    <option value="6" <?php if ($selContent==6) {echo "selected";}?>>포인트 내역</option>
                                                    <option value="7" <?php if ($selContent==7) {echo "selected";}?>>프리매치 베팅 내역</option>
                                                    <option value="19" <?php if ($selContent==19) {echo "selected";}?>>클래식 베팅 내역</option>
                                                    <option value="8" <?php if ($selContent==8) {echo "selected";}?>>실시간 베팅 내역</option>
                                                    <option value="9" <?php if ($selContent==9) {echo "selected";}?>>미니게임 베팅 내역</option>
                                                    <option value="10" <?php if ($selContent==10) {echo "selected";}?>>카지노 베팅 내역</option>
                                                    <option value="11" <?php if ($selContent==11) {echo "selected";}?>>슬롯 베팅 내역</option>
                                                     <?php if('ON' == IS_ESPORTS_KEYRON){ ?>
                                                    <option value="12" <?php if ($selContent==12) {echo "selected";}?>>E스포츠 베팅 내역</option>
                                                     <?php } ?>
                                                     <?php if('ON' == IS_HASH){ ?>
                                                    <option value="14" <?php if ($selContent==14) {echo "selected";}?>>바카라 베팅 내역</option>
                                                    <option value="15" <?php if ($selContent==15) {echo "selected";}?>>룰렛 베팅 내역</option>
                                                    <option value="16" <?php if ($selContent==16) {echo "selected";}?>>하이로우 베팅 내역</option>
                                                     <?php } ?>
                                                    <!-- <option value="17" <?php if ($selContent==17) {echo "selected";}?>>지머니 내역</option> -->
                                                    <!-- <option value="18" <?php if ($selContent==18) {echo "selected";}?>>보유아이템</option> -->
                                                    <?php if('ON' == IS_HOLDEM){ ?>
                                                    <option value="20" <?php if ($selContent==20) {echo "selected";}?>>홀덤 베팅 내역</option>
                                                     <?php } ?>
                                                </select>
                                    	</td>
                                        
                                    </tr>
								</table>
							</div>
							<div style="height: 10px"></div>
							
							<div id="pop_userinfo_content_1">
                				
                			</div>
                			<div class="tline" id="pop_userinfo_content_2">
                				
                			</div>
                			<div style="height: 10px"></div>
                			<div class="tline" id="pop_userinfo_bbs" style ="">
                				<div class="">
                                    <table class="mlist">
                                    	<tr>
                                        	<th style="width: 80px; text-align:left">제 목</th>
                                            <td style="text-align:left" id="qna_title_data" ></td>
                                        </tr>
                                        <tr>
                                        	<th style="width: 80px; text-align:left">내 용</th>
                                            <td style="text-align:left" id="qna_content_data" ></td>
                                        </tr>
                                        <tr>
                                        	<th rowspan="2" style="width: 80px; text-align:left">답 변</th>
                                            <td>
                                                <select name="set_msg" id="set_msg" onchange="javascript:getSetMsg(this.value);" style="width: 100%">
                                                    <option value="">-- 템플릿 선택 --</option>
                                                    <?php 
                                                    if(!empty($db_dataArr_msg_set)){
                                                        foreach($db_dataArr_msg_set as $row) {
                                                    ?>
                                                    <option value="<?=$row['idx']?>"><?=$row['title_view']?></option>
                                                    <?php 
                                                         }
                                                     }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td>
                                            <div id="loading"></div>
                                            <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:600px; height:220px; display:none;"></textarea><br>
                                            </td>
                                        </tr>
                                        
                                    </table>                
                                    
                                    <div style="height: 20px"></div>
                                </div>
                                
                                <div class="panel_tit" style="text-align: center;">
                                	<div class="clx modal_foot">
                                        <a href="javascript:goQnaAnswer();" id="" class="btn h30 btn_blu" data-dismiss="modal">저장</a>
                                        <a href="javascript:;" class="btn h30 btn_mdark" data-dismiss="modal">삭제</a>
                                    </div>
                                </div>
                			</div>
							
                            