<?= view('/web/common/header') ?>
<?= view('/web/common/header_wrap') ?>

    <style>
    .cloader {
        width: 48px;
        height: 48px;
        border: 5px solid #FFF;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: cloaderRotation 1s linear infinite;
    }

    @keyframes cloaderRotation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    } 
    
    .sb_wrapper .casino__wrap .game_list ul li { flex: 0 0 20% }

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

    <div class="title_wrap">
        <div class="title">라이브카지노</div>
    </div>

    <div id="contents_wrap" class="sb_wrapper">
        <div class="container casino__wrap">
            <div class="game_list">
                <ul>
                </ul>
            </div>
        </div>
    </div>

    <?= view('/web/common/footer_wrap') ?>

    <?php if (config('CasinoGateway')->enabled): ?>
    <script>
    (function($) {
        var baseUrl = "<?php echo config('CasinoGateway')->baseUrl ?>";
        var token = "<?php echo session()->get('api_gateway_token') ?>";
        var gameId = "<?php echo $id ?>";

        var displayGames = function (provider, items) {
            $.each(items, function (index, value) {
                $('.game_list ul').append(
                    '<li>' +
                        '<a href="#" title="' + value.name_kor + '" data-id="' + value.id + '" data-provider="' + provider + '">' +
                            '<img src="' + value.img + '" />' +
                        '</a>' +
                        '<span class="spinner"><span class="cloader"></span></span>' +
                    '</li>'
                );
            });
        }

        var fetchGames = function (provider, id, page = 1, afterFetch = null) {
            if (provider === 'sbcasino') {
                $.ajax({
                    url: baseUrl + 'sbcasino/providers/'+id+'/game-list',
                    beforeSend: function (xhr, settings) {
                        if (page) {
                            settings.url += '?page=' + page;
                        }
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

                    var provider = response.data.casino_provider;
                    var fetchGamesCallback = function (response) {
                        if (!response.success) {
                            alert('Something went wrong. Please try again later.');
                            return
                        }

                        displayGames(provider, response.data)

                        if (response.pagination.next) {
                            var url = new URL(response.pagination.next);
                            var page = url.searchParams.get('page');
                            if (page) {
                                fetchGames(provider, gameId, page, fetchGamesCallback);
                            }
                        }
                    };
                    fetchGames(provider, gameId, 1, fetchGamesCallback);
                }
            });
        }

        var bindEvents = function () {
            $('body').on('click', '.game_list a', function (event) {
                event.preventDefault();

                var id = $(this).data('id');
                var provider = $(this).data('provider');

                var parent = $(this).parent();

                parent.addClass('loading');

                if (provider === 'sbcasino') {
                    $.ajax({
                        url: baseUrl + 'sbcasino/games/' + id + '/play',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                        },
                        type: 'POST',
                        success: function (response) {
                            if (!response.success) {
                                console.log(response.message);
                                return;
                            }

                            if (response.data.url) {
                                window.open(response.data.url);
                            }

                            console.log(response);
                            parent.removeClass('loading');
                        }
                    });
                }
            });
        }

        checkGameProvider();
        bindEvents();
    })(jQuery);
    </script>
    <?php endif ?>
</body>
</html>
