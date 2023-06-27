<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

    <style>
    .sb_wrapper .game_list ul li {
        position: relative;
    }
    .sb_wrapper .game_list ul li .spinner {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .sb_wrapper .game_list ul li.loading {
        opacity: 0.7;
    }
    .sb_wrapper .game_list ul li.loading .spinner{
        display: inline-block;
    }
    </style>

<div class="title_wrap"><div class="title">카지노</div></div>

    <div id="contents_wrap" class="contents_wrap">
        <!-- <div class="contents_box casino__wrap"> -->
        <div class="contents_box">
            <div class="game_list">
                <ul>
                    <?php foreach ($prodList as $key => $prod) {?>
                        <?php if($prod['IMG_URL'] == '/assets_w/images/game/img_warning.png'){ ?>
                            <li>
                                <a href="javascript:playCasino(<?=$prod['PRD_ID']?>, 0, 'C')">
                                <img src="/assets_w/images/game/img_warning.png">
                                </a>
                            </li>
                        <?php } ?>
                        <?php
                            if($prod['IMG_URL'] != '/assets_w/images/game/img_warning.png'){
                            // 1 에볼루션
                            // 2 빅 게이밍
                            // 5 아시아 게이밍
                            // 6 드림 게이밍
                            // 9 섹시 게이밍
                            // 10 프라그마틱 플레이
                            // 12 플레이테크
                            // 15 TV벳
                            // 16 로얄카지노게이밍
                            // 17 에즈기
                            // 18 보타
                            // 19 스카이윈드
                            // 20 UIG카지노
                        ?>
                            <li>
                                <a href="javascript:playCasino(<?=$prod['PRD_ID']?>, 0, 'C')">
                                    <img src="<?=$prod['IMG_URL']?>">
                                </a>
                            </li>
                        <?php } ?>
                    <?php } ?> 
                </ul>
            </div><!-- .game_list -->
        </div><!-- .container.casino__wrap -->
    </div><!-- contents_wrap -->


    <div id="contents_wrap" class="sb_wrapper">
        <!-- <div class="container casino__wrap"> -->
        <div class="container">
            <div class="game_list">
                <ul></ul>
            </div>
        </div>
    </div>


    <?= view('/web/common/footer_wrap') ?>

    <script>
    const playCasino = function (prd_id, game_id, prd_type){
        $.ajax({
            url: '/web/playCasino',
            type: 'post',
            async: false,
            data: {
                'prd_id': prd_id,
                'game_id' : game_id,
                'prd_type' : prd_type
            },
        }).done(function (response) {
            //console.log('betPrice : '+betPrice+' betPrice : '+server_betPrice);
            if(response['result_code'] == 1){
                // window.location.href = response['data']['launch_url'];

                window.open(response['data']['launch_url']);

                // const iframe = $('<iframe class="myFrame"></iframe>');
                // iframe.attr('allowfullscreen', true);
                // iframe.attr('src', response['data']['launch_url']);
                // $('.game__iframe-wrap').append(iframe);
                // $('.game__iframe-popup').show();
            }else{
                alert(response['messages']);
            }
        }).fail(function (error) {
            alert(error.responseJSON['messages']['messages']);
        }).always(function (response) {
        });
    }

    // $(document).ready(function(){
    //     const prd_id = fnGetParam('prd_id');
    //     $("#prd_id_"+prd_id).addClass('active');
    // });


    // const offCasinoGame = function() {
    //     $('.game__iframe-popup').hide().find('iframe').prop('src', '');
    // }

    // const fnGetParam = function(sname) {
    //     var params = location.search.substr(location.search.indexOf("?") + 1);
    //     var sval = "";
    //     params = params.split("&");
    //     for (var i = 0; i < params.length; i++) {
    //         temp = params[i].split("=");
    //         if ([temp[0]] == sname) { sval = temp[1]; }

    //     }
    //     return sval;
    // };
    </script>

    <?php if (config('CasinoGateway')->enabled): ?>
    <script>
    (function($) {
        var baseUrl = "<?php echo config('CasinoGateway')->baseUrl ?>";
        var token = "<?php echo session()->get('api_gateway_token') ?>";

        var getImgUrl = function (name) {
            switch (name) {
                case ('EVOLUTION'):
                    return '/assets_w/images/casino01.png';
                case ('CQ9 Live'):
                    return '/assets_w/images/casino01.png';
                case ('DreamGame'):
                    return '/assets_w/images/casino04.png';
                case ('WM Live'):
                    return '/assets_w/images/casino01.png';
                case ('EZugi'):
                    return '/assets_w/images/casino10.png';
                case ('TG/PD'):
                    return '/assets_w/images/casino01.png';
                case ('Asia Gaming'):
                    return '/assets_w/images/casino03.png';
                case ('PragmaticPlay Live'):
                    return '/assets_w/images/casino06.png';
                case ('Sexy Casino'):
                    return '/assets_w/images/casino05.png';
                case ('Betgames.tv'):
                    return '/assets_w/images/casino01.png';
                case ('Skywind Live'):
                    return '/assets_w/images/casino12.png';
                case ('Dowin'):
                    return '/assets_w/images/casino01.png';
                case ('MicroGaming Plus'):
                    return '/assets_w/images/casino01.png';
            }
        }

        var displayList = function (items) {
            $.each(items, function (index, value) {
                if (value.in_use == 0 || value.in_use == null) return
                var imgUrl = getImgUrl(value.name);
                $('.sb_wrapper .game_list ul').append(
                    '<li>' +
                        '<a href="casino/'+ value.id +'" title="' + value.name + '" data-id="' + value.id + '" data-total-games="'+ value.total_games +'">' +
                            '<img src="' + imgUrl + '" />' +
                        '</a>' +
                        '<span class="spinner"><span class="cloader"></span></span>' +
                    '</li>'
                );
            });
        }

        var fetchList = function (page = 0) {
            $.ajax({
                url: baseUrl + 'sbcasino/providers?type=live&site_code=BTS',
                beforeSend: function (xhr, settings) {
                    if (page) {
                        settings.url += '&page=' + page;
                    }
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
                type: 'GET',
                success: function (response) {
                    if (!response.success) {
                        // handle error
                        return
                    }

                    displayList(response.data);

                    if (response.pagination.next) {
                        var url = new URL(response.pagination.next);
                        var page = url.searchParams.get('page');
                        if (page) {
                            fetchList(page)
                        }
                    }
                }
            });
        }

        var checkGameProvider = function () {
            $.ajax({
                url: baseUrl + 'site-settings?site_code=BTS',
                beforeSend: function (xhr, settings) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
                type: 'GET',
                success: function (response) {
                    if (!response.success) {
                        // handle error
                        return
                    }
                    if (response.data.casino_provider == 'sbcasino') {
                        $('.original_wrapper').remove();
                        $('.sb_wrapper').show();
                    }
                }
            });
        }

        var fetchGames = function (id, afterFetch) {
            $.ajax({
                url: baseUrl + 'sbcasino/providers/'+id+'/game-list',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
                type: 'GET',
                success: function (response, textStatus, jqXHR) {
                    if (!response.success) {
                        // handle error
                        return;
                    }

                    if (typeof afterFetch == 'function') {
                        afterFetch(response, textStatus, jqXHR);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof afterFetch == 'function') {
                        afterFetch(null, textStatus, jqXHR, errorThrown);
                    }
                }
            });
        }

        var fetchGameLink = function (id, afterFetch) {
            $.ajax({
                url: baseUrl + 'sbcasino/games/' + id + '/play',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
                type: 'POST',
                success: function (response, textStatus, jqXHR) {
                    if (!response.success) {
                        // handle error
                        return;
                    }

                    window.open(response.data.url);

                    if (typeof afterFetch == 'function') {
                        afterFetch(response, textStatus, jqXHR);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof afterFetch == 'function') {
                        afterFetch(null, textStatus, jqXHR, errorThrown)
                    }
                }
            });
        }

        var bindEvents = function () {
            $('.sb_wrapper .game_list ul').on('click', 'a', function (event) {
                var id = $(this).data('id');
                var totalGames = $(this).data('total-games');

                var isEvolution = id === 1;
                var isEzugi = id === 13;

                var parent = $(this).parent();

                if (totalGames === 1 || isEvolution || isEzugi) {
                    parent.addClass('loading');
                    fetchGames(id, function(response) {
                        if (response.success) {
                            fetchGameLink(response.data[0].id, function(response) {
                                if (!response) {
                                    alert('Something went wrong. Please try again later.');
                                }
                                parent.removeClass('loading');
                            });
                        } else {
                            parent.removeClass('loading');
                            alert('Something went wrong. Please try again later.');
                        }
                    });
                    return false;
                }

                return true;
            })
        }

        checkGameProvider();
        fetchList();
        bindEvents();
    })(jQuery);
    </script>
    <?php endif ?>
</body>
</html>