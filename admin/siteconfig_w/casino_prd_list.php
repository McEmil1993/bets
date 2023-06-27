<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');
include_once(_DAOPATH.'/class_Admin_Member_dao.php');
//////// login check start
include_once(_BASEPATH.'/common/login_check.php');
//////// login check end
$UTIL = new CommonUtil();
if(0 != $_SESSION['u_business']){
    die();
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$MEMAdminDAO)){
        die();
    }
    $p_data['sql'] = "SELECT * FROM KP_PRD_INF WHERE PRD_ID > 0 AND TYPE = 'C';";
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    $MEMAdminDAO->dbclose();
}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
<!--<![endif]-->
<?php include_once(_BASEPATH.'/common/head.php') ?>
<body>
    <style>
    .aligned-btn { display: inline-flex !important; align-items: center; gap: 5px }
    .text-center { text-align: center }
    .hidden { display: none }
    </style>

    <script>
        $(document).ready(function() {
            App.init();
            FormPlugins.init();

            $('ul.tabs li').click(function(){
                var tab_id = $(this).attr('data-tab');

                $('ul.tabs li').removeClass('current');
                $('.tab-content').removeClass('current');

                $(this).addClass('current');
                $("#"+tab_id).addClass('current');
            })
        });
    </script>
    <div class="wrap">
        <?php
            $menu_name = "casino_prd_list";
            include_once(_BASEPATH.'/common/left_menu.php');
            include_once(_BASEPATH.'/common/iframe_head_menu.php');
        ?>
        <!-- Contents -->
        <div class="con_wrap">
            <div class="title">
                <a href="javascript:;">
                    <i class="mte i_settings vam ml20 mr10"></i>
                    <h4>카지노게임사 설정</h4>
                </a>
            </div>

            <?php if (defined('API_GATEWAY_BASE_URL')): ?>
            <div class="panel reserve">
                <form class="search_form mb0" data-provider-switch>
                    <div class="pr10">
                        <select name="provider">
                            <option value="kplay" selected="">Kplay</option>
                            <option value="sbcasino">SB Casino</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn h30 btn_green aligned-btn">
                            Change
                        </button>
                    </div>
                    <span class="cloader cloader--xs hidden ml10" data-provider-switch-loader></span>
                </form>
            </div>
            <?php endif ?>

            <!-- list -->
            <div class="panel reserve" data-defaults>
                <form id="search" name="search" action='<?=$_SERVER['PHP_SELF']?>'>
                    <div class="tline">
                        <table class="mlist">
                            <tr>
                                <th>타입</th>
                                <th>게임사명</th>
                                <th>사용여부</th>
                            </tr>
                            <tbody id="charge_event_tbody">
                                <?php foreach ($db_dataArr as $key => $value): ?>
                                <tr>
                                    <td><?=$value['TYPE']=='C' ?'카지노':'슬롯'?></td>
                                    <td><?=$value['PRD_NM']?></td>
                                    <td>
                                        <?php if ($value['IS_USE'] == 1): ?>
                                            <a href="#" onclick="betOnOffBtnClick(this, <?=$value['PRD_ID']?>, 0)" class="btn h25 btn_green"> ON</a>
                                        <?php else: ?>
                                            <a href="#" onclick="betOnOffBtnClick(this, <?=$value['PRD_ID']?>, 1)" class="btn h25 btn_gray"> OFF</a>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <?php if (defined('API_GATEWAY_BASE_URL')): ?>
            <div class="panel reserve hidden" data-main>
                <div class="text-center hidden" data-main-loader>
                    <span class="cloader"></span>
                </div>
                <div class="tline" data-main-content>
                    <table class="mlist" data-table>
                        <tr>
                            <th>타입</th>
                            <th>게임사명</th>
                            <th>사용여부</th>
                        </tr>
                        <tbody id="charge_event_tbody" data-table-body>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif ?>
            <!-- END list -->
        </div>
        <!-- END Contents -->
    </div>

    <?php include_once(_BASEPATH.'/common/bottom.php') ?>
    <script>
    function betOnOffBtnClick(ateg, prd_id, status) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/siteconfig_w/_casino_prd_onoff.php',
            data: {'prd_id': prd_id, 'cmd': status},
            success: function (result) {
                //console.log(result['retCode']);
                if (result['retCode'] == "1000") {
                    alert('업데이트 되었습니다.');
                    
                    if (1 == status) {
                        $(ateg).removeClass("btn_gray").removeClass("btn_green").addClass("btn_green");
                        $(ateg).attr("onclick", "betOnOffBtnClick(this, "+prd_id+", 0)");
                        $(ateg).text("ON");
                    } else {
                        $(ateg).removeClass("btn_green").removeClass("btn_gray").addClass("btn_gray");
                        $(ateg).attr("onclick", "betOnOffBtnClick(this, "+prd_id+", 1)");
                        $(ateg).text("OFF");
                    }
                    return;
                } else {
                    alert('업데이트 실패 (1)');
                    return;
                }
            },
            error: function (request, status, error) {
                alert('업데이트 실패 (2)');

                return;
            }
        });
    }
    </script>

    <?php if (defined('API_GATEWAY_BASE_URL')): ?>
    <script>
    (function ($) {
        ApiGateway.init('<?php echo API_GATEWAY_BASE_URL ?>', 'BTS')

        const $providerSwitch = $('[data-provider-switch]')
        const $providerSwitchLoader = $('[data-provider-switch-loader]')
        const $main = $('[data-main]')
        const $mainContent = $('[data-main-content]')
        const $mainLoader = $('[data-main-loader]')
        const $table = $('[data-table]')
        const $tableBody = $('[data-table-body]')

        const displayDefaults = (display) => {
            if (display) {
                $('[data-defaults]').removeClass('hidden')
                $main.addClass('hidden')
            } else {
                $('[data-defaults]').addClass('hidden')
                $main.removeClass('hidden')
            }
        }

        const loadSiteSettings = () => {
            ApiGateway.fetchSiteSettings({
                beforeFetch: function () {
                    $providerSwitch.find('[name=provider], [type=submit]').prop('disabled', true)
                    $providerSwitchLoader.removeClass('hidden')
                },
                afterFetch: function (response) {
                    if (!response || !response.success) {
                        // handle error
                    } else {
                        const provider = response.data.casino_provider
                        $providerSwitch.find('[name=provider]').val(provider).change()
                        if (!provider || provider == 'kplay') {
                            displayDefaults(true)
                        } else {
                            displayDefaults(false)
                            loadGamesList(provider)
                        }
                    }

                    $providerSwitch.find('[name=provider], [type=submit]').prop('disabled', false)
                    $providerSwitchLoader.addClass('hidden')
                }
            })
        }

        const displayGamesList = (provider, items) => {
            let html = ''
            $.each(items, function (index, value) {
                html += '<tr>'
                if (value.type == 'live') {
                    html += '<td>카지노</td>'
                } else {
                    html += '<td>슬롯</td>'
                }
                html += '<td>' + value.name + '</td>'
                if (value.in_use === null || value.in_use === "0" || value.in_use === 0) {
                    html += '<td><a href="javascript:;" class="btn h25 btn_gray aligned-btn" data-id="'+value.id+'" data-provider="'+provider+'" data-switch="1">OFF</a></td>'
                } else {
                    html += '<td><a href="javascript:;" class="btn h25 btn_green aligned-btn" data-id="'+value.id+'" data-provider="'+provider+'" data-switch="0">ON</a></td>'
                }
                html += '</tr>'
            })
            $tableBody.append(html)
        }

        const loadGamesList = (provider, page = 1, fresh = true) => {
            if (fresh) {
                $tableBody.html('')
            }
            ApiGateway.fetchGames(
                provider,
                { page: page },
                {
                    beforeFetch: function () {
                        $mainLoader.removeClass('hidden')
                        $mainContent.addClass('hidden')
                    },
                    afterFetch: function (response) {
                        if (response.success) {
                            if ($tableBody.children().length < response.meta.total) {
                                displayGamesList(provider, response.data)
                                loadGamesList(provider, page + 1, false)
                            }
                        }
                        $mainLoader.addClass('hidden')
                        $mainContent.removeClass('hidden')
                    }
                }
            )
        }

        const updateProvider = (provider) => {
            ApiGateway.updateSiteSettings(
                { casino_provider: provider, slot_provider: provider },
                {
                    beforeFetch: function () {
                        $providerSwitchLoader.removeClass('hidden')
                    },
                    afterFetch: function (response) {
                        $providerSwitchLoader.addClass('hidden')
                    }
                }
            )
        }

        const switchButton = (element) => {
            const provider = element.getAttribute('data-provider');
            const id = element.getAttribute('data-id');
            const value = element.getAttribute('data-switch');

            ApiGateway.updateGame(
                provider,
                id,
                { in_use: value },
                {
                    beforeFetch: function () {
                        $(element).html('<span class="cloader cloader--xs"></span>')
                    },
                    afterFetch: function (response) {
                        if (response.success) {
                            if (value == 1) {
                                $(element)
                                    .removeClass('btn_gray')
                                    .addClass('btn_green')
                                    .text('ON')
                                    .attr('data-switch', 0)
                            } else {
                                $(element)
                                    .addClass('btn_gray')
                                    .removeClass('btn_green')
                                    .text('OFF')
                                    .attr('data-switch', 1)
                            }
                        }
                    }
                }
            )
        }

        const bindEvents = () => {
            $providerSwitch.submit(function (event) {
                const provider = this.provider.value
                if (provider == 'kplay') {
                    displayDefaults(true)
                } else {
                    displayDefaults(false)
                }
                updateProvider(provider)
                loadGamesList(provider)
                return false
            })

            $mainContent.on('click', '[data-switch]', function (event) {
                const element = event.target
                switchButton(element)
            })
        }


        loadSiteSettings()
        bindEvents()
    })(jQuery)
    </script>
    <?php endif ?>
</body>
</html>
