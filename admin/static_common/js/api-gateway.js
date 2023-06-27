const ApiGateway = {
    baseUrl: '',
    siteCode: '',

    fetch: function (path, method, data = {}, options = {}) {
        path = path.replace(/^\/|\/$/g, '');

        data.site_code = this.siteCode

        switch (method) {
            case ('PUT'):
                data = JSON.stringify(data)
                break
        }

        options = $.extend({
            beforeFetch: function() {},
            afterFetch: function(response, textStatus, jqXHR, errorThrown) {},
        }, options)

        options.beforeFetch()

        $.ajax({
            url: `${this.baseUrl}/${path}`,
            method: method,
            data: data,
            contentType: 'application/json',
            success: function (response, textStatus, jqXHR) {
                options.afterFetch(response, textStatus, jqXHR)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                options.afterFetch(null, textStatus, jqXHR, errorThrown)
            }
        })
    },

    fetchSiteSettings: function (options) {
        this.fetch('/site-settings', 'GET', {}, options)
    },

    updateSiteSettings: function (data, options) {
        this.fetch('/site-settings', 'PUT', data, options)
    },

    fetchGames: function (provider, data, options) {
        switch (provider) {
            case ('sbcasino'):
                this.fetch('/sbcasino/providers', 'GET', data, options)
                break
        }
    },

    updateGame: function (provider, id, data, options) {
        switch (provider) {
            case ('sbcasino'):
                this.fetch(`/sbcasino/providers/${id}`, 'PUT', data, options)
                break
        }
    },

    fetchBettingHistory: function (provider, data, options) {
        switch (provider) {
            case ('sbcasino'):
                this.fetch(`/betting-history`, 'GET', data, options)
                break
        }
    },

    init: function (baseUrl, siteCode) {
        this.baseUrl = baseUrl
        this.siteCode = siteCode
    }
}
