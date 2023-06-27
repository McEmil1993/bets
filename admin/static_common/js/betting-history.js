const BettingHistory = (function ($, ApiGateway) {
    let PROVIDER = ''
    let GAME_TYPE = ''

    let gameList = []
    let dataList = []

    let $defaultContainer
    let $mainContainer
    let $paginationContainer
    let $startDateInput
    let $endDateInput
    let $gamesSelection
    let $searchForm

    let renderRowsFunc

    function _getQueryParms(name) {
        const params = new URLSearchParams(window.location.search)
        return params.get(name)
    }

    function _setQueryParms(name, value) {
        const params = new URLSearchParams(window.location.search);
        params.set(name, value);
        window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    }

    const _displayDefaults = (display) => {
        if (display) {
            $defaultContainer.removeClass('hidden')
            $mainContainer.addClass('hidden')
        } else {
            $defaultContainer.addClass('hidden')
            $mainContainer.removeClass('hidden')
        }
    }

    const _checkSiteSettings = () => {
        ApiGateway.fetchSiteSettings({
            beforeFetch: () => {},
            afterFetch: (response) => {
                if (!response || !response.success) {
                    // handle error
                    return
                }

                PROVIDER = response.data.casino_provider
                if (PROVIDER == 'kplay') {
                    _displayDefaults(true)
                } else {
                    _displayDefaults(false)
                    _fetchBettingHistory()
                    _fetchGames()
                }
            }
        })
    }

    const _formatMoney = (amount) => {
        let formatting = Intl.NumberFormat('en-US');
        return formatting.format(amount);
    }

    const _resultStatus = (type) => {
        switch (type) {
            case (1):
                return 'Lose';
            case (2):
                return 'Win';
            case (3):
                return 'Refund';
        }
    }

    const _renderBettingHistory = (items, fresh = true) => {
        let html = ''
        $.each(items, function (index, value) {
            html += '<tr>'
            html += '<th width="3%"><div class="checkbox checkbox-css checkbox-inverse" style="display:inline-block; text-align:center; width:20px; height:20px;"><input type="checkbox" id="checkbox_css_all" name="checkbox_css_all" /><label for="checkbox_css_all"></label></div></th>'
            html += '<td>' + value.created_at + '</td>'
            html += '<td>' + value.member_id + '</td>'
            html += '<td>' + value.member_id + '</td>'
            html += '<td>' + _formatMoney(value.bet_amount) + '</td>'
            html += '<td>' + _formatMoney(value.result_amount) + '</td>'
            html += '<td>' + _formatMoney(parseInt(value.result_amount) + parseInt(value.hold_money)) + '</td>'
            html += '<td>' + value.game_company_name + '</td>'
            html += '<td>' + value.game_id + '</td>'
            html += '<td>' + _resultStatus(value.result_amount < 1 ? 1 : 2) + '</td>'
            html += '<td>' + value.transaction_id + '</td>'
            html += '</tr>'
        })

        if (typeof renderRowsFunc == 'function') {
            html = renderRowsFunc(html, items)
        }

        if (fresh) {
            $mainContainer.find('table tbody').html(html)
        } else {
            $mainContainer.find('table tbody').append(html)
        }
    }

    const _renderPagination = (total, perPage, currentPage) => {
        const count = Math.ceil(total / perPage)

        let html = ''
        html += '<a href="javascript:;" data-page="1" class="' + (currentPage <= 1  ? 'none' : '') + '"><i class="mte i_navigate_before vam"></i></a>'

        for (let i = 1; i <= count; i++) {
            html += '<a href="javascript:;" data-page="' + i + '" class="' + (i == currentPage ? 'on' : '') + '">' + i + '</a>'
        }

        html += '<a href="javascript:;" data-page="' + count + '" class="' + (currentPage >= count  ? 'none' : '') + '"><i class="mte i_navigate_next vam"></i></a>'
        $paginationContainer.html(html)
    }

    const _fetchBettingHistory = (input = {}, fresh = true) => {
        let data = {
            game_type: GAME_TYPE,
            page: input.page || _getQueryParms('start_date'),
            start_date: input.startDate || _getQueryParms('start_date'),
            endDate: input.endDate || _getQueryParms('end_date'),
            game_company_id: input.gameId || _getQueryParms('game_id'),
            search_type: input.searchType || _getQueryParms('search_type'),
            search_value: input.searchValue || _getQueryParms('search_value'),
        }

        ApiGateway.fetchBettingHistory(
            PROVIDER,
            data,
            {
                beforeFetch: function () {},
                afterFetch: function (response) {
                    if (!response.success) {
                        // handle error
                        return
                    }

                    dataList = response.data
                    _renderBettingHistory(response.data, fresh)
                    _renderPagination(
                        response.meta.total,
                        response.meta.per_page,
                        response.meta.current_page,
                    )
                }
            }
        )
    }

    const _renderGamesSelection = (items, fresh = true) => {
        let html = ''

        if (fresh) {
            html += '<option value="">전체</option>'
        }

        $.each(items, function (index, value) {
            html += '<option value="' + value.code + '">' + value.name + '</option>'
        })

        if (fresh) {
            $gamesSelection.html(html)
        } else {
            $gamesSelection.append(html)
        }
    }

    const _fetchGames = (page = 1, fresh = true) => {
        ApiGateway.fetchGames(
            PROVIDER,
            { page: page, type: GAME_TYPE },
            {
                beforeFetch: () => {},
                afterFetch: (response) => {
                    if (!response.success) {
                        // handle error
                    }

                    if (gameList.length < response.meta.total) {
                        if (fresh) {
                            gameList = response.data
                        } else {
                            $.merge(gameList, response.data)
                        }
                        _renderGamesSelection(response.data, fresh)
                        _fetchGames(page + 1, false)
                    }
                }
            }
        )
    }

    const _bindEvents = () => {
        $paginationContainer.on('click', 'a', function (event) {
            const page = $(this).data('page')
            if (page) {
                _setQueryParms('page', page)
                _fetchBettingHistory({ page: page }, true)
            }
            $paginationContainer.find('a').removeClass('on')
            $(this).addClass('on')
        })
        
        $searchForm.on('submit', function (event) {
            var data = Object.fromEntries(new FormData(this))
            _fetchBettingHistory({
                startDate: data.start_date,
                endDate: data.end_date,
                gameId: data.game_id,
                searchType: data.search_type,
                searchValue: data.search_value,
            }, true)
            return false
        })
    }

    const setDate = (startDate, endDate) => {
        $startDateInput.val(startDate)
        $endDateInput.val(endDate)
    }

    const init = (ApiGatewayUrl, siteCode, gameType, renderRows) => {
        ApiGateway.init(ApiGatewayUrl, siteCode)
        GAME_TYPE = gameType

        $defaultContainer = $('[data-defaults]')
        $mainContainer = $('[data-main]')
        $paginationContainer = $('[data-pagination]')
        $startDateInput = $('[data-start-date-input]')
        $endDateInput = $('[data-end-date-input]')
        $gamesSelection = $('[data-games-select]')
        $searchForm = $('[data-search-form]')

        renderRowsFunc = renderRows

        _checkSiteSettings()
        _bindEvents()
    }

    return {
        init: function (ApiGatewayUrl, siteCode, gameType, renderRows) {
            init(ApiGatewayUrl, siteCode, gameType, renderRows)
        },
        setDate: function (startDate, endDate) {
            setDate(startDate, endDate)
        }
    }
})(jQuery, ApiGateway)