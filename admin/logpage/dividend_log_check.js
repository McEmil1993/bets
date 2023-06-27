class DividendLogCheck
{
    constructor(database)
    {
        this.table_data         = [];
        this.column_data        = ['bet_name', 'bet_line', 'bet_price', 'tempPrice','bet_status', 'msg_guid', 'deduction_rate', 'providers', 'create_dt'];   

        /* initialization after document ready */
        $(document).ready(() =>
        {
            this.initialize();
        })
    }
    async initialize()
    {
        this.sports_options         = $(".select-sports");
        this.locations_options      = $(".select-locations");
        this.leagues_options        = $(".select-leagues");
        this.status_options         = $(".select-status");

        this.loadTableData();
        this.addSelectEvents();

        this.__loadOptions(this.sports_options, '/logpage/dividend_log_check_api.php', { action: 'load_sports' }, 'id', 'display_name');
        this.__loadOptions(this.locations_options, '/logpage/dividend_log_check_api.php', { action: 'load_locations' }, 'id', 'name');
        this.__loadOptions(this.leagues_options, '/logpage/dividend_log_check_api.php', { action: 'load_leagues' }, 'id', 'display_name');
        this.__loadOptions(this.status_options, '/logpage/dividend_log_check_api.php', { action: 'load_status' }, 'status_id', 'status_label');
    }

    async addSelectEvents()
    {
        this.sports_options.change(() =>
        {
            this.loadTableData();
        });

        this.locations_options.change(() =>
        {
            this.loadTableData();
        });

        this.leagues_options.change(() =>
        {
            this.loadTableData();
        });

        this.status_options.change(() =>
        {
            this.loadTableData();
        });
    }
    async loadTableData()
    {
        this.__showLoading();

        $.ajax(
        {
            url: '/logpage/dividend_log_check_api.php',
            method: 'get',
            data:
            {
                action: 'load_dividend_table',
            },
            dataType: 'json',
            success: (data) =>
            {
                this.table_data = data.response;
                this.__loadTableData();
                this.__hideLoading();
            }
        });
    }
    async __loadOptions(target, url, params, value, label)
    {
        target.attr('disabled', 'disabled');
        target.append(`<option value="0">Loading</option>`);

        $.ajax(
        {
            url: url,
            method: 'get',
            data: params,
            dataType: 'json',
            success: (res) =>
            {
                let list = res.response;

                target.html('');
                target.append(`<option value="0">전체</option>`);

                for(let data of list)
                {
                    target.append(`<option value="${data[value]}">${data[label]}</option>`);
                }

                target.removeAttr('disabled');
            }
        });
    }
    async __loadTableData()
    {
        let table_data_html = "";
        
        for(let data of this.table_data)
        {
            table_data_html += "<tr>";

            for(let column of this.column_data)
            {
                if(column == "providers")
                {
                    let provider_info = JSON.parse(data[column]);

                    if(provider_info.hasOwnProperty('provider_id'))
                    {
                        table_data_html += this.__tableTD(provider_info.provider_id);
                    }
                    else
                    {
                        table_data_html += this.__tableTD(provider_info.Providers.provider_id);
                    }
                   
                }
                else
                {
                    table_data_html += this.__tableTD(data[column]);
                }
                
            }
            
            table_data_html += "</tr>";
        }

        $(".table-data").html(table_data_html);
    }
    __tableTD(value)
    {
        return `<td>${value}</td>`;
    }
    async __showLoading()
    {
        $(".table-loading").show();
        $(".table-data").hide();
    }
    async __hideLoading()
    {
        $(".table-loading").hide();
        $(".table-data").show();
    }
}