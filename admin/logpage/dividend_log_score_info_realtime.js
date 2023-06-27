class DividendLogCheck
{
    constructor(database)
    {
        this.table_data         = [];
        this.column_data        = ['fixture_id', 'value1', 'value2', 'liveTime', 'liveCurrentPeriod', 'msg_guid', 'live_score', 'fixture_id','created_date_formatted'];   

        /* initialization after document ready */
        $(document).ready(() =>
        {
            this.initialize();
        })
    }
    async initialize()
    {
        this.sports_options             = $(".select-sports");
        this.locations_options          = $(".select-locations");
        this.leagues_options            = $(".select-leagues");
        this.status_options             = $(".select-status");
        this.date_filter                = $(".text-filter-date");
        this.search_by                  = $(".search-by");
        this.search_keyword             = $(".search-keyword");
        this.reload_table               = $(".reload-table");
        this.next_page_reference        = 'base_timestamp';
        this.next_page_reference_value  = null;
        this.last_page                  = false;


        this.loadTableData();
        this.addSelectEvents();
        
        this.__loadOptions(this.sports_options, '/logpage/dividend_log_check_api.php', { action: 'load_sports' }, 'id', 'display_name');
        this.__loadOptions(this.locations_options, '/logpage/dividend_log_check_api.php', { action: 'load_locations' }, 'id', 'name');
        this.__loadOptions(this.leagues_options, '/logpage/dividend_log_check_api.php', { action: 'load_leagues' }, 'id', 'display_name');
        this.__loadOptions(this.status_options, '/logpage/dividend_log_check_api.php', { action: 'load_status' }, 'status_id', 'status_label');
    }

    async addSelectEvents()
    {
        let this_instance = this;

        this.search_keyword.keypress(function (e)
        {
            var key = e.which;
            
            if(key == 13)
            {
                this.next_page_reference_value = null;
                this_instance.loadTableData();
            }
        });   
        this.reload_table.click(() =>
        {
            this.next_page_reference_value = null;
            this.loadTableData();
        });

        this.sports_options.change(() =>
        {
            this.next_page_reference_value = null;
            this.loadTableData();
        });

        this.locations_options.change(() =>
        {
            this.next_page_reference_value = null;
            this.loadTableData();
        });

        this.leagues_options.change(() =>
        {
            this.next_page_reference_value = null;
            this.loadTableData();
        });

        this.status_options.change(() =>
        {
            this.next_page_reference_value = null;
            this.loadTableData();
        });

        this.date_filter.change(() =>
        {
            this.next_page_reference_value = null;
            this.loadTableData();
        });
    }
    async loadTableData(append_mode = false)
    {
        if(!append_mode)
        {
            this.__showLoading();
        }

        
        $.ajax(
        {
            url: '/logpage/dividend_log_check_api.php',
            method: 'get',
            data:
            {
                action              : 'load_score_info',
                date_filter         : this.date_filter.val(),
                sports_filter       : this.sports_options.val(),
                locations_filter    : this.locations_options.val(),
                leagues_filter      : this.leagues_options.val(),
                status_filter       : this.status_options.val(),
                search_by           : this.search_by.val(),
                search_keyword      : this.search_keyword.val(),
                next_page_reference : this.next_page_reference,
                next_page_value     : this.next_page_reference_value,
                
            },
            dataType: 'json',
            success: (data) =>
            {
                this.table_data = data.response;

                this.__loadTableData(append_mode);
                this.__hideLoading();
            }
        });
    }
    async __addScrollEventForPageLoading()
    {
        let this_instance = this;
        
        $(window).unbind('scroll');
        $(window).bind('scroll',function()
        { 
            let window_scroll = $(window).scrollTop();
            let window_size = $(document).height() - $(window).height();

            if(window_scroll >= (window_size - 300))
            {
                $(window).unbind('scroll');
                this_instance.loadTableData(true)
            }
        })
    }
    async __loadOptions(target, url, params, value, label)
    {
        target.attr('disabled', 'disabled');
        target.html(`<option value="0">Loading</option>`);

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
    async __loadTableData(append_mode = false)
    {
        let table_data_html = "";
        
        //check if not empty
        if(this.table_data)
        {
            for(let data of this.table_data)
            {
                table_data_html += "<tr>";
    
                for(let column of this.column_data)
                {
                    if(column == "live_score")
                    {    
                        let live_score  = JSON.parse(data[column]);
                        console.log(live_score);
                        let score       = `${live_score.Scoreboard.Results[0].Value} - ${live_score.Scoreboard.Results[1].Value}`;
                        table_data_html += this.__tableTD(score);
                    }
                    else
                    {
                        table_data_html += this.__tableTD(data[column]);
                    }
                    
                }
                
                table_data_html += "</tr>";
            }  
            
            this.next_page_reference_value = this.table_data[this.table_data.length-1][this.next_page_reference];
        }
        else
        {
            table_data_html += "<tr><td colspan='10'>No Result</td></tr>";
            this.last_page = true;
        }

        if(append_mode)
        {
            $(".table-data").append(table_data_html);
        }
        else
        {
            $(".table-data").html(table_data_html);
        }

        if(!this.last_page)
        {
            this.__addScrollEventForPageLoading();
        }
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