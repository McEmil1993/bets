
<!-- 회원 기본정보 -->                            
<div>
    <table class="mlist">
        <tr>
            <th style="width: 120px;">아이디/닉네임 <font color='red'>*</font></th>
            <td style="padding: 2px;text-align:left">
                <table class="table_noline" style="padding: 2px;">
                    <tr>
                        <td style="padding: 2px;">
                            <input type="text" value="<?= true === isset($db_data_mem[0]['id']) && false === empty($db_data_mem[0]['id']) ? $db_data_mem[0]['id'] : null ?>" disabled />
                        </td>
                        <td style="padding: 2px;">	
                            <input type="text" name="u_nick" id="u_nick" value="<?= true === isset($db_data_mem[0]['nick_name']) && false === empty($db_data_mem[0]['nick_name']) ? $db_data_mem[0]['nick_name'] : null ?>" maxlength="20" disabled />
                        </td>
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
                            <?php $u_status = true === isset($db_data_mem[0]['status']) && false === empty($db_data_mem[0]['status']) ? $db_data_mem[0]['status'] : 1 ?>
                            <select style="width: 160px;" name="u_status" id="u_status">
                                <option value="1" <?php
                                if ($u_status == 1) {
                                    echo "selected";
                                }
                                ?>>사용중</option>
                                <option value="2" <?php
                                if ($u_status == 2) {
                                    echo "selected";
                                }
                                ?>>계정 정지</option>
                                <option value="3" <?php
                                if ($u_status == 3) {
                                    echo "selected";
                                }
                                ?>>탈퇴</option>
                                <option value="11" <?php
                                if ($u_status == 11) {
                                    echo "selected";
                                }
                                ?>>승인 대기 회원</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>회원레벨</th>
            <td style="padding: 2px;text-align:left">
                <table class="table_noline">
                    <tr>
                        <td style="padding: 2px;text-align:left">
                            <?php $u_level = true === isset($db_data_mem[0]['level']) && false === empty($db_data_mem[0]['level']) ? $db_data_mem[0]['level'] : 1 ?>
                            <select style="width: 160px;" name="u_level" id="u_level">
                                <?php
                                for ($i = 1; $i <= 10; $i++) {
                                    ?>    
                                    <option value='<?= $i ?>' <?php
                                    if ($u_level == $i) {
                                        echo 'selected';
                                    }
                                    ?> ><?= $i ?> 레벨</option>
                                            <?php
                                        }
                                        ?>	
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>연락처 <font color='red'>*</font></th>
            <td style="padding: 2px;text-align:left">
                <?php
                $db_hp = explode('-', true === isset($db_data_mem[0]['call']) && false === empty($db_data_mem[0]['call']) ? $db_data_mem[0]['call'] : null);
                $db_hp_cnt = count($db_hp);
                if ($db_hp_cnt == 3) {
                    $db_hp_2 = $db_hp[1];
                    $db_hp_3 = $db_hp[2];
                } else {
                    $db_hp = str_replace('-', '', true === isset($db_data_mem[0]['call']) && false === empty($db_data_mem[0]['call']) ? $db_data_mem[0]['call'] : null);
                    $db_hp_2 = substr($db_hp, 3, 4);
                    $db_hp_3 = substr($db_hp, 7, 4);
                }
                ?>
                <table class="table_noline">
                    <tr>
                        <td style="padding: 2px;">
                            <select name="u_hp01" id="u_hp01">
                                <option value="010">010</option>
                            </select>
                        </td>
                        <td style="padding: 2px;">
                            <input type="text" name="u_hp02" id="u_hp02" value="<?= $db_hp_2 ?>" maxlength="4"/>
                        </td>
                        <td style="padding: 2px;">	
                            <input type="text" name="u_hp03" id="u_hp03" value="<?= $db_hp_3 ?>" maxlength="4"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- 생년월일 추가 -->
        <tr>
            <th>생년월일 <font color='red'>*</font></th>
            <td style="padding: 2px;text-align:left">
                <table class="table_noline">
                    <tr>
                        <td style="padding: 2px;">
                            <input type="text" name="u_birth" id="u_birth" maxlength="8" value="<?= true === isset($db_data_mem[0]['id']) && false === empty($db_data_mem[0]['birth']) ? $db_data_mem[0]['birth'] : null ?>"/>
                        </td>
                        <td >통신사</td>            
                        <td class="">
                            <select style="width: 160px;" name="u_mobile_carrier" id="u_mobile_carrier">
                                <?php foreach ($db_data_mobile_carrier as $key => $value) { ?>
                                        <option value="<?=$value['idx']?>" <?=$value['idx'] == $db_data_mem[0]['mobile_carrier']?'selected':'' ?>><?=$value['name']?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- 생년월일 추가 끝 -->
        <tr>
            <th>가입총판라인</th>
            <td style="padding: 2px;text-align:left">
                <table class="table_noline">
                    <tr>
                        <td style="padding: 2px;text-align:left">
                            <select style="width: 100%;" name="u_dis_id" id="u_dis_id">
                                <option value="">-- 없음 --</option>
                                <?php
                                if (!empty($db_data_dis)) {
                                    foreach ($db_data_dis as $row) {
                                        if ($row['u_business'] == 1)
                                            continue;

                                        $dis_id = true === isset($db_data_mem[0]['dis_id']) && false === empty($db_data_mem[0]['dis_id']) ? $db_data_mem[0]['dis_id'] : null;
                                        // CommonUtil::logWrite("pop_userinfo.php af dis_id => : " . $db_data_mem[0]['dis_id'], "error");
                                        ?>
                                        <option value="<?= $row['id'] ?>" <?php
                                        if ($dis_id == $row['id']) {
                                            echo "selected";
                                        }
                                        ?>><?= $row['id'] ?> (<?= $row['nick_name'] ?>)</option>
                                                <?php
                                            }
                                        }
                                        ?>
                            </select>
                        </td>
                        <td>
                            <div ><a href="javascript:setDisId()" class="btn h30 btn_blu" style="color:#fff">적용</a></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>추천인</th>
            <td style="padding: 2px;text-align:left">
                <table class="table_noline">
                    <tr>
                        <td style="padding: 2px;text-align:left">
                            <select style="width: 100%;" name="u_recomm_user" id="u_recomm_user">
                                <option value="">-- 없음 --</option>
                                <?php
                                if (!empty($db_data_dis)) {
                                    foreach ($db_data_dis as $row) {
                                        $recommend_member = true === isset($db_data_mem[0]['recommend_member']) && false === empty($db_data_mem[0]['recommend_member']) ? $db_data_mem[0]['recommend_member'] : null;
                                        ?>
                                        <option value="<?= $row['idx'] ?>" 
                                        <?php
                                        if ($recommend_member == $row['idx']) {
                                            echo "selected";
                                        }
                                        ?>><?= $row['id'] ?> (<?= $row['nick_name'] ?>)</option>
                                                <?php
                                            }
                                        }
                                        ?>
                            </select>
                        </td>
                        <!--<td>
                            <div ><a href="javascript:;" class="btn h30 btn_blu" style="color:#fff">적용</a></div>
                        </td>-->
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
                                        <select style="width: 160px;" name="u_account_bank" id="u_account_bank" disabled>
                                            <option value="">-- 은행선택 --</option>
                                            <?php
                                            if (!empty($db_data_bank)) {
                                                foreach ($db_data_bank as $row) {
                                                    if (0 == strcmp($db_data_mem[0]['account_bank'], 'KB국민은행'))
                                                        $mem_account_bank = '국민은행';
                                                    else if (0 == strcmp($db_data_mem[0]['account_bank'], 'NH농협은행'))
                                                        $mem_account_bank = '농협';
                                                    else
                                                        $mem_account_bank = $db_data_mem[0]['account_bank'];
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
                                    </div>
                                    <div class="">
                                        <input type="text" name="u_account_number" id="u_account_number" class="" style="width: 200px;" value="<?= true === isset($db_data_mem[0]['account_number']) && false === empty($db_data_mem[0]['account_number']) ? $db_data_mem[0]['account_number'] : null ?>" maxlength="30" disabled/>
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
                            <input type="text" name="u_account_name" id="u_account_name" class="" style="width: 160px;" value="<?= true === isset($db_data_mem[0]['account_name']) && false === empty($db_data_mem[0]['account_name']) ? $db_data_mem[0]['account_name'] : null ?>" disabled/>
                        </div>
                        <div class="" style="width:10px"></div>
                        <!-- <div >환전암호</div>
                        <div class="" style="width:5px"></div>
                        <div class="">
                            <input type="password" name="u_account_pass" id="u_account_pass" class="" maxlength="20"/>
                        </div> -->
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<div style="height: 10px"></div>
<!-- 계좌, 보유머니 정보 -->
<div>
    <table class="mlist">
        <tr>
            <th style="width: 120px;">보유 포인트</th>
            <td style="padding: 2px;text-align:left">
                <div class="panel_tit" style="height: 5px;">
                    <div class="search_form fl">
                        <div class="" style="width:1px"></div>
                        <div class="">
                            <input type="text" name="db_u_point" id="db_u_point" class="" style="width:160px; text-align:right" value="<?= number_format(true === isset($db_data_mem[0]['point']) && false === empty($db_data_mem[0]['point']) ? $db_data_mem[0]['point'] : 0) ?>" disabled/>
                        </div>
                        <div class="" style="width:2px"></div>
                        <div class="">
                            <input type="text" name="u_point" id="u_point" class=""  style="width:120px" placeholder="변경 포인트 입력"/>
                        </div>
                        <div class="" style="width:5px"></div>
                        <div><a href="javascript:;" onClick="setMoneyPoint('point', 'p');" class="btn h30 btn_blu" style="color:#fff">지급</a></div>
                        <div class="" style="width:2px"></div>
                        <div><a href="javascript:;" onClick="setMoneyPoint('point', 'm');" class="btn h30 btn_mdark" style="color:#fff">차감</a></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<script>
    // 베팅가능 토글
    //$(".bet__controll-table td").on("click", function () {
    //    $(this).toggleClass("active");
    //})
</script>