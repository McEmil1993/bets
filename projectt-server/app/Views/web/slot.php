<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

    <link rel="stylesheet" href="/assets_w/jq/slick/slick.css">
    <script src="/assets_w/jq/slick/slick.min.js"></script>

    <style>
    .sb_wrapper .game_company_list,
    .sb_wrapper .game_list ul { list-style-type: none; margin: 0; padding: 0; display: flex; }
    .sb_wrapper .game_company_list .slick-list { flex: 1; margin: 0 5px; }
    .sb_wrapper .game_company_list .slick-arrow { background-color: #009dd9; padding: 5px; transition: 0.4s ease all; }
    .sb_wrapper .game_company_list .slick-arrow:hover { background-color: #25b9f2;}
    .sb_wrapper .game_company_list .slick-arrow.slick-disabled { background-color: #575757;}
    .sb_wrapper .game_company_list .slick-arrow img { width: 12px; }
    .sb_wrapper .game_company_list .game_company_list_item { padding: 0 5px }
    .sb_wrapper .game_company_list .game_company_list_item a:before { content: ''; transition: 0.4s ease all }
    .sb_wrapper .game_company_list .game_company_list_item:hover a:before,
    .sb_wrapper .game_company_list .game_company_list_item.active a:before { display: block; width: 100%; height: 100%; position: absolute; top: 0; left: 0; background-color: #009dd9; z-index: 2;opacity: 0.3; }
    .sb_wrapper .game_company_list .game_company_list_item a { position: relative;z-index: 1; }
    .sb_wrapper .game_company_list .game_company_list_item a .game_company_name { position: absolute; z-index: 1; width: 100%; top: 50%; transform: translateY(-50%); text-align: center; }
    .sb_wrapper .game_list { text-align: center }
    .sb_wrapper .game_list ul { margin-left: -5px; margin-right: -5px }
    .sb_wrapper .game_list ul li { max-width: 14.28%; flex: 0 0 14.28%; padding: 5px; position: relative; }
    .sb_wrapper .game_list ul li img { max-width: 100%; height: auto; }
    .sb_wrapper .game_list ul li .spinner { display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
    .sb_wrapper .game_list ul li.loading { opacity: 0.7; }
    .sb_wrapper .game_list ul li.loading .spinner{ display: inline-block; }
    </style>

    <div class="title_wrap">
        <div class="title">슬롯머신</div>
    </div>

    <div id="contents_slot_wrap" class="original_wrapper">
        <div class="contents_box game__board-wrap">
            <div class="con_box30 game__board-menu">
                <ul></ul>
            </div>
            <div class="game__board-list casino__board-list original_game_list">
            </div>
        </div>
    </div>


    <div id="contents_wrap" class="sb_wrapper" style="display: none">
        <div class="container casino__wrap">
            <div class="game_company_list">
            </div>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <div class="game_list">
                <ul></ul>
                <span class="main_loader cloader"></span>
            </div>
        </div>
    </div>

    <?= view('/web/common/footer_wrap') ?>


    <script>
        $(document).ready(function(){
            const path = window.location.pathname;

            const pathArr = path.split("/");

            let prd_id;
            let game_id;
            if(pathArr[1] == "slots") {
                prd_id = pathArr[2];
                game_id = pathArr[3];
            }else {
                prd_id = 0;
                game_id = 0;
            }

            $.ajax({
                url: '/web/playCasino',
                type: 'post',
                async: false,
                data: {
                    'prd_id': prd_id,
                    'game_id' : game_id,
                    'prd_type' : 'S'
                },
            }).done(function (response) {
                console.log(response);
                if(response['result_code'] == 1){
                    const iframe = $('<iframe class="myFrame" style="width:100%;height:100vh;"></iframe>');
                    iframe.attr('allowfullscreen', true);
                    iframe.attr('src', response['data']['launch_url']);
                    $('.casino__board-list').append(iframe);
                }else{
                    alert(response['messages']);
                }
            }).fail(function (error) {
                alert(error.responseJSON['messages']['messages']);
            }).always(function (response) {
            });
        });
                                                    
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

        const offCasinoGame = function() {
            $('.game__iframe-popup').hide().find('iframe').prop('src', '');
        }

        const fnGetParam = function(sname) {
            var params = location.search.substr(location.search.indexOf("?") + 1);
            var sval = "";
            params = params.split("&");
            for (var i = 0; i < params.length; i++) {
                temp = params[i].split("=");
                if ([temp[0]] == sname) { sval = temp[1]; }

            }
            return sval;
        };
    </script>

    
    <?php if (config('CasinoGateway')->enabled): ?>
    <script>
    (function($) {
        var token = "<?php echo session()->get('api_gateway_token') ?>";

        var baseUrl = "<?php echo config('CasinoGateway')->baseUrl ?>";

        var stopDisplayGames = false;

        var getGameCompanyImage = (name) => {
            switch (name.toLowerCase()) {
                case('evoplay'):
                    return '/assets_w/images/slots/evoplay-logo.png';
                case('cq9'):
                    return '/assets_w/images/slots/qc-logo.png';
                case('playstar'):
                    return '/assets_w/images/slots/playstar-logo.png';
                case('habanero'):
                    return '/assets_w/images/slots/habanero-provider-logo.png';
                case('playngo'):
                    return '/assets_w/images/slots/png-logo.png';
                case('relax gaming'):
                    return '/assets_w/images/slots/relax-gaming.png';
                case('microgaming plus slo'):
                    return '/assets_w/images/slots/MG-Logo.png';
                default:
                    return false;
            }
        }

        var displayList = function (items) {
            $.each(items, function (index, value) {
                if (value.active) alert()
                if (value.in_use == 0 || value.in_use == null) return
                var img = getGameCompanyImage(value.name) || '/assets_w/images/slots/none.png';
                var html = '<div class="game_company_list_item">' +
                    '<a href="javascript:void(0)" data-id="' + value.id + '" title="' + value.name + '">' +
                        '<img src="' + img + '" alt="' + value.name + '" />' +
                        (!getGameCompanyImage(value.name) ?'<span class="game_company_name">' + value.name + '</span>' : '') +
                    '</a>' +
                '</div>';
                // $('.sb_wrapper .game_company_list').append(html);
                $('.sb_wrapper .game_company_list').slick('slickAdd', html);
            });
        }

        var fetchList = function (page = 0, fresh = true) {
            $.ajax({
                url: baseUrl + 'sbcasino/providers?type=slot&site_code=BTS',
                beforeSend: function (xhr, settings) {
                    if (page) {
                        settings.url += '&page=' + page;
                    }
                    xhr.setRequestHeader('Authorization', 'Bearer <?php echo session()->get('api_gateway_token') ?>');
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

                    if (fresh) {
                        fetchGames(response.data[0].id, 1, true);
                        $('.game_company_list_item [data-id="' + response.data[0].id + '"]').parent().addClass('active');
                    }
                }
            });
        }

        var displayGames = function (items, fresh = false) {
            if (fresh) {
                $('.sb_wrapper .game_list ul').html('');
                stopDisplayGames = false;
            }
            if (stopDisplayGames) {
                return;
            }
            $.each(items, function (index, value) {
                $('.sb_wrapper .game_list ul').append(
                    '<li>' +
                        '<a href="javascript:void(0)" data-id="'+ value.id +'" title="' + value.name_eng + '">' +
                            '<img src="' + value.img + '" alt="" />' +
                        '</a>' +
                        '<span class="spinner"><span class="cloader"></span></span>' +
                    '</li>'
                );
            });
        }

        var fetchGames = function (gameCompanyId, page = 0, fresh = false) {
            $('.sb_wrapper .game_list .main_loader').show();
            $.ajax({
                url: baseUrl + 'sbcasino/providers/' + gameCompanyId + '/game-list?site_code=BTS',
                beforeSend: function (xhr, settings) {
                    if (page) {
                        settings.url += '&page=' + page;
                    }
                    xhr.setRequestHeader('Authorization', 'Bearer <?php echo session()->get('api_gateway_token') ?>');
                },
                type: 'GET',
                success: function (response) {
                    if (!response.success) {
                        alert('Something went wrong. Please try again later.');
                        return
                    }

                    displayGames(response.data, fresh);

                    if (response.pagination.next) {
                        var url = new URL(response.pagination.next);
                        var page = url.searchParams.get('page');
                        if (page) {
                            fetchGames(gameCompanyId, page)
                        }
                    }

                    $('.sb_wrapper .game_list .main_loader').hide();
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

        var checkGameProvider = function () {
            $.ajax({
                url: baseUrl + 'site-settings?site_code=BTS',
                beforeSend: function (xhr, settings) {
                    xhr.setRequestHeader('Authorization', 'Bearer <?php echo session()->get('api_gateway_token') ?>');
                },
                type: 'GET',
                success: function (response) {
                    if (!response.success) {
                        // handle error
                        return
                    }
                    if (response.data.slot_provider == 'sbcasino') {
                        $('.original_wrapper').remove();
                        $('.sb_wrapper').show();
                    }
                }
            });
        }

        var bindEvents = function () {
            $('.sb_wrapper .game_company_list').on('click', 'a', function (event) {
                event.preventDefault();
                var id = $(this).data('id');
                stopDisplayGames = true;
                fetchGames(id, 1, true);
                $('.sb_wrapper .game_company_list .game_company_list_item').removeClass('active');
                $(this).parent().addClass('active');
            });

            $('.sb_wrapper .game_list ul').on('click', 'a', function (event) {
                event.preventDefault();
                var id = $(this).data('id');
                var parent = $(this).parent();
                parent.addClass('loading');
                fetchGameLink(id, function (response) {
                    if (!response) {
                        alert('Something went wrong. Please try again later.');
                    }
                    parent.removeClass('loading');
                });
            });
            
            $('.sb_wrapper .game_company_list').slick({
                slidesToShow: 8,
                slidesToScroll: 1,
                infinite: false,
                prevArrow: '<button type="button" class="prev-btn"><img src="/assets_w/images/arrow1_left.png" /></button>',
                nextArrow: '<button type="button" class="next-btn"><img src="/assets_w/images/arrow1_right.png" /></button>',
                // responsive: [
                //     {
                //         breakpoint: 1024,
                //         settings: {
                //             slidesToShow: 3,
                //             slidesToScroll: 3,
                //             infinite: true,
                //             dots: true
                //         }
                //     },
                // ]
            });
        }

        checkGameProvider();
        fetchList(1, true);
        bindEvents();
    })(jQuery);
    </script>
    <?php endif ?>
</body>
</html>