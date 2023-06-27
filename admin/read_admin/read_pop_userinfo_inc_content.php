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
                            </div>
							
                            