
function setConfigGameLevel(ptype = null, setidx = - 1, txtobj = null, txtobj2 = null) {

    var param_url = '/siteconfig_w/_set_config_game.php';

    var fm = document.search;
    var regVal = '';
    var str_msg = '';

    var g_level = '';
    var g_level_pre = '';
    var g_level_real = '';

    if (ptype == 'reg_game_level') {
        g_level = fm.game_level.value;
        g_level_pre = fm.game_level_pre.value;
        g_level_real = fm.game_level_real.value;

        if ((g_level == '') || (Number.isNaN(Number(g_level)))) {
            alert('등급을 입력해 주세요. 숫자만 입력 가능합니다.');
            fm.game_level.value = '';
            fm.game_level.focus();
            return;
        }


        if ((g_level_pre == '') || (Number.isNaN(Number(g_level_pre)))) {
            alert('프리매치 금액을 입력해 주세요. 숫자만 입력 가능합니다.');
            fm.game_level_pre.value = '';
            fm.game_level_pre.focus();
            return;
        }

        if ((g_level_real == '') || (Number.isNaN(Number(g_level_real)))) {
            alert('실시간 금액을 입력해 주세요. 숫자만 입력 가능합니다.');
            fm.game_level_real.value = '';
            fm.game_level_real.focus();
            return;
        }

        str_msg = '등급별 베팅금액 정보를 등록 하시겠습니까?';
    } else if (ptype == 'mod_game_level') {
        g_level_pre = document.getElementById(txtobj).value;
        g_level_real = document.getElementById(txtobj2).value;

        g_level_pre = g_level_pre.replace(/\,/gi, '');
        g_level_real = g_level_real.replace(/\,/gi, '');

        str_msg = '선택하신 정보를 수정 하시겠습니까?';
    } else if (ptype == 'del_game_level') {
        str_msg = '선택하신 정보를 삭제 하시겠습니까?';
    } else {
        alert('잘못된 요청방식 입니다.');
        return;
    }

    var result = confirm(str_msg);

    if (result) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: param_url,
            data: {'ptype': ptype, 'idx': setidx, 'g_level': g_level, 'g_pre_money': g_level_pre, 'g_real_money': g_level_real},
            success: function (data) {

                if (data['retCode'] == "1000") {
                    window.location.reload();
                } else if (data['retCode'] == "2001") {
                    alert('잘못된 요청 입니다.');
                } else {
                    alert('실패 하였습니다.');
                    //window.location.reload();
                }
            },
            error: function (request, status, error) {
                alert('서버 오류 입니다.');
                window.location.reload();
            }
        });
    }

    return;
}

function setConfig(ptype = null, setidx = 0) {
    var fm = document.search;
    var i = 0;
    var bChk = true;

    var param_url = '/siteconfig_w/_set_config_game.php';
    var buffstr = "";

    var rf_per = 0;
    var f_min_money = ["", "", "", "", "", "", "", "", "", ""];
    var f_max_money = ["", "", "", "", "", "", "", "", "", ""];
    var f_limit_money = ["", "", "", "", "", "", "", "", "", ""];
    var classic_min_money = ["", "", "", "", "", "", "", "", "", ""];
    var classic_max_money = ["", "", "", "", "", "", "", "", "", ""];
    var classic_limit_money = ["", "", "", "", "", "", "", "", "", ""];
    var r_min_money = ["", "", "", "", "", "", "", "", "", ""];
    var r_max_money = ["", "", "", "", "", "", "", "", "", ""];
    var r_limit_money = ["", "", "", "", "", "", "", "", "", ""];
    var ls_per = ["", "", "", "", "", "", "", "", "", ""];
    var lr_per = ["", "", "", "", "", "", "", "", "", ""];
    var c_first_per = ["", "", "", "", "", "", "", "", "", ""];
    var c_max_money = ["", "", "", "", "", "", "", "", "", ""];
    var c_per = ["", "", "", "", "", "", "", "", "", ""];
    var c_money = ["", "", "", "", "", "", "", "", "", ""];


    var pre_dividen_1 = ["", "", "", "", "", "", "", "", "", ""];
    var pre_dividen_2 = ["", "", "", "", "", "", "", "", "", ""];
    var pre_dividen_3 = ["", "", "", "", "", "", "", "", "", ""];
    var pre_dividen_4 = ["", "", "", "", "", "", "", "", "", ""];
    
    var classic_dividen_1 = ["", "", "", "", "", "", "", "", "", ""];
    var classic_dividen_2 = ["", "", "", "", "", "", "", "", "", ""];
    var classic_dividen_3 = ["", "", "", "", "", "", "", "", "", ""];
    var classic_dividen_4 = ["", "", "", "", "", "", "", "", "", ""];

    var real_dividen_1 = ["", "", "", "", "", "", "", "", "", ""];
    var real_dividen_2 = ["", "", "", "", "", "", "", "", "", ""];
    var real_dividen_3 = ["", "", "", "", "", "", "", "", "", ""];
    var real_dividen_4 = ["", "", "", "", "", "", "", "", "", ""];


    if (ptype == 'reg_first_charge') {
        var regVal = Number(fm.con_reg_first.value);

        if (Number.isNaN(Number(fm.con_reg_first.value))) {
            alert('숫자만 입력 가능합니다.');
            fm.con_reg_first.value = '';
            fm.con_reg_first.focus();
            return;
        }

        rf_per = regVal.toFixed(1);
        fm.con_reg_first.value = rf_per;

        buffstr = "가입첫충";

    } else if (ptype == 'bet_config_level') {
        i = 0;
        bChk = true;
        $('input[name="pre_min_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            f_min_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(f_min_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="pre_max_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                i++;
                bChk = false;
                return;
            }

            f_max_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(f_max_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="pre_limit_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            f_limit_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(f_limit_money);

        if (bChk == false)
            return;
        
        // classic
        i = 0;
        bChk = true;
        $('input[name="classic_min_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            classic_min_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_min_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="classic_max_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                i++;
                bChk = false;
                return;
            }

            classic_max_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_max_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="classic_limit_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            classic_limit_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_limit_money);

        if (bChk == false)
            return;
        
        // real
        i = 0;
        bChk = true;
        $('input[name="real_min_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            r_min_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(r_min_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="real_max_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            r_max_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(r_max_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="real_limit_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            r_limit_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(r_limit_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="lose_self_per[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val();
                i++;
                bChk = false;
                return;
            }

            ls_per[i] = tmpValue;
            i++;
        });

        JSON.stringify(ls_per);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="lose_recomm_per[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            lr_per[i] = tmpValue;
            i++;
        });

        JSON.stringify(lr_per);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="charge_first_per[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            c_first_per[i] = tmpValue;
            i++;
        });

        JSON.stringify(c_first_per);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="charge_max_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            c_max_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(c_max_money);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="charge_per[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            c_per[i] = tmpValue;
            i++;
        });

        JSON.stringify(c_per);

        if (bChk == false)
            return;

        i = 0;
        bChk = true;
        $('input[name="charge_money[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            c_money[i] = tmpValue;
            i++;
        });

        JSON.stringify(c_money);

        if (bChk == false)
            return;


        // pre_dividen_1
        i = 0;
        bChk = true;
        $('input[name="pre_dividen_1[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }
            //alert('tmpValue' + tmpValue);
            pre_dividen_1[i] = tmpValue;
            i++;
        });

        JSON.stringify(pre_dividen_1);

        if (bChk == false)
            return;


        // pre_dividen_2
        i = 0;
        bChk = true;
        $('input[name="pre_dividen_2[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            pre_dividen_2[i] = tmpValue;
            i++;
        });

        JSON.stringify(pre_dividen_2);

        if (bChk == false)
            return;


        // pre_dividen_3
        i = 0;
        bChk = true;
        $('input[name="pre_dividen_3[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            pre_dividen_3[i] = tmpValue;
            i++;
        });

        JSON.stringify(pre_dividen_3);

        if (bChk == false)
            return;

        // pre_dividen_4
        i = 0;
        bChk = true;
        $('input[name="pre_dividen_4[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            pre_dividen_4[i] = tmpValue;
            i++;
        });

        JSON.stringify(pre_dividen_4);

        if (bChk == false)
            return;
        
        // classic_dividen_1
        i = 0;
        bChk = true;
        $('input[name="classic_dividen_1[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }
            //alert('tmpValue' + tmpValue);
            classic_dividen_1[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_dividen_1);

        if (bChk == false)
            return;


        // classic_dividen_2
        i = 0;
        bChk = true;
        $('input[name="classic_dividen_2[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            classic_dividen_2[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_dividen_2);

        if (bChk == false)
            return;


        // classic_dividen_3
        i = 0;
        bChk = true;
        $('input[name="classic_dividen_3[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            classic_dividen_3[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_dividen_3);

        if (bChk == false)
            return;

        // classic_dividen_4
        i = 0;
        bChk = true;
        $('input[name="classic_dividen_4[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            classic_dividen_4[i] = tmpValue;
            i++;
        });

        JSON.stringify(classic_dividen_4);

        if (bChk == false)
            return;


        // real_dividen_1
        i = 0;
        bChk = true;
        $('input[name="real_dividen_1[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            real_dividen_1[i] = tmpValue;
            i++;
        });

        JSON.stringify(real_dividen_1);

        if (bChk == false)
            return;


        // real_dividen_2
        i = 0;
        bChk = true;
        $('input[name="real_dividen_2[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            real_dividen_2[i] = tmpValue;
            i++;
        });

        JSON.stringify(real_dividen_2);

        if (bChk == false)
            return;


        // real_dividen_3
        i = 0;
        bChk = true;
        $('input[name="real_dividen_3[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            real_dividen_3[i] = tmpValue;
            i++;
        });

        JSON.stringify(real_dividen_3);

        if (bChk == false)
            return;


        // real_dividen_4
        i = 0;
        bChk = true;
        $('input[name="real_dividen_4[]"').each(function (idx, item) {

            if (bChk == false)
                return;

            var tmpValue = $(item).val();

            if (tmpValue == '') {
                $(item).val(0);
                tmpValue = 0;
            } else {
                tmpValue = tmpValue.replace(/\,/gi, '');
            }

            if (Number.isNaN(Number(tmpValue))) {
                alert('숫자만 입력 가능합니다.');
                $(item).val(0);
                i++;
                bChk = false;
                return;
            }

            real_dividen_4[i] = tmpValue;
            i++;
        });

        JSON.stringify(real_dividen_4);

        if (bChk == false)
            return;
        buffstr = "레벨별 베팅금액 설정";



    } else {
        alert('잘못된 요청 방식입니다.');
        return;
    }


    var str_msg = buffstr + ' 정보를 등록 하시겠습니까?';

    var result = confirm(str_msg);

    if (result) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: param_url,
            data: {'ptype': ptype, 'idx': setidx, 'reg_first_per': rf_per, 'pre_min_money': f_min_money, 'pre_max_money': f_max_money,
                'pre_limit_money': f_limit_money, 'real_min_money': r_min_money, 'real_max_money': r_max_money,
                'classic_min_money': classic_min_money, 'classic_max_money': classic_max_money, 'classic_limit_money': classic_limit_money,
                'real_limit_money': r_limit_money, 'lose_self_per': ls_per, 'lose_recomm_per': lr_per,
                'charge_first_per': c_first_per, 'charge_max_money': c_max_money, 'charge_per': c_per, 'charge_money': c_money,
                'pre_dividen_1': pre_dividen_1, 'pre_dividen_2': pre_dividen_2, 'pre_dividen_3': pre_dividen_3, 'pre_dividen_4': pre_dividen_4,
                'classic_dividen_1': classic_dividen_1, 'classic_dividen_2': classic_dividen_2, 'classic_dividen_3': classic_dividen_3, 'classic_dividen_4': classic_dividen_4,
                'real_dividen_1': real_dividen_1, 'real_dividen_2': real_dividen_2, 'real_dividen_3': real_dividen_3, 'real_dividen_4': real_dividen_4},
       
       
            success: function (data) {

                if (data['retCode'] == "1000") {
                    window.location.reload();
                } else if (data['retCode'] == "2001") {
                    alert('잘못된 요청 입니다.');
                } else {
                    alert('실패 하였습니다.');
                    //window.location.reload();
                }
            },
            error: function (request, status, error) {
                alert('서버 오류 입니다.');
                window.location.reload();
            }
        });
}
}

