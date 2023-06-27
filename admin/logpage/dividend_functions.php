<?php
    function loadOddsInfo()
    {
        // get information from get
        $date_filter                        = $_GET['date_filter'];
        $search_by                          = trim($_GET['search_by']);
        $search_keyword                     = trim($_GET['search_keyword']);
        $next_page_reference                = $_GET['next_page_reference'];
        $next_page_value                    = $_GET['next_page_value'];

        // create filter array
        $filter_list                        = array();
        $filter_list[0]                     = new stdClass();
        $filter_list[0]->filter_by          = 'sports_id';
        $filter_list[0]->filter_value       = $_GET['sports_filter'];
        $filter_list[1]                     = new stdClass();
        $filter_list[1]->filter_by          = 'fixture_id';
        $filter_list[1]->filter_value       = $_GET['locations_filter'];
        $filter_list[2]                     = new stdClass();
        $filter_list[2]->filter_by          = 'league_id';
        $filter_list[2]->filter_value       = $_GET['leagues_filter'];
        $filter_list[3]                     = new stdClass();
        $filter_list[3]->filter_by          = 'bet_status';
        $filter_list[3]->filter_value       = $_GET['status_filter'];

        // create date filter
        $start_time     = date("Y-m-d H:i:s", strtotime($date_filter . "-1" . " days"));
        $end_time       = date("Y-m-d H:i:s", strtotime($date_filter . "+1" . " days"));

        // create query connection
        $database       = new DatabaseConnection(_DB_NAME_LOG_DB);
        $query          = "";

        // select query
        $query          .= "SELECT * FROM rmq1_log_data_real";

        // filter by date
        $query          .= " WHERE create_dt between '$start_time' and '$end_time'";

        // create filter based on filter list
        foreach($filter_list as $filter)
        {
            if($filter->filter_value != 0)
            {
                $query .= " AND " . $filter->filter_by  . " = " . $filter->filter_value;
            }
        }

        // search by id
        if($search_keyword)
        {
            $query          .= " AND $search_by = '$search_keyword'";
        }

        if($next_page_value)
        {
            $query          .= " AND $next_page_reference < '$next_page_value'";
        }


        // sort query
        $query          .= " ORDER BY base_timestamp DESC LIMIT 100";

        // get result
        $data           = $database->getData($query);

        // modify result
        foreach($data as $i => $d)
        {
            $data[$i]["created_date_formatted"] = date("M d, Y - h:i:s A", strtotime($d["create_dt"]));;
        }


        return $data;
    }
    function loadOddsInfoPrematch()
    {
        // get information from get
        $date_filter                        = $_GET['date_filter'];
        $search_by                          = trim($_GET['search_by']);
        $search_keyword                     = trim($_GET['search_keyword']);
        $next_page_reference                = $_GET['next_page_reference'];
        $next_page_value                    = $_GET['next_page_value'];

        // create filter array
        $filter_list                        = array();
        $filter_list[0]                     = new stdClass();
        $filter_list[0]->filter_by          = 'sports_id';
        $filter_list[0]->filter_value       = $_GET['sports_filter'];
        $filter_list[1]                     = new stdClass();
        $filter_list[1]->filter_by          = 'fixture_id';
        $filter_list[1]->filter_value       = $_GET['locations_filter'];
        $filter_list[2]                     = new stdClass();
        $filter_list[2]->filter_by          = 'league_id';
        $filter_list[2]->filter_value       = $_GET['leagues_filter'];
        $filter_list[3]                     = new stdClass();
        $filter_list[3]->filter_by          = 'bet_status';
        $filter_list[3]->filter_value       = $_GET['status_filter'];

        // create date filter
        $start_time     = date("Y-m-d H:i:s", strtotime($date_filter . "-1" . " days"));
        $end_time       = date("Y-m-d H:i:s", strtotime($date_filter . "+1" . " days"));

        // create query connection
        $database       = new DatabaseConnection(_DB_NAME_LOG_DB);
        $query          = "";

        // select query
        $query          .= "SELECT * FROM rmq1_log_data_prematch";

        // filter by date
        $query          .= " WHERE create_dt between '$start_time' and '$end_time'";

        // create filter based on filter list
        foreach($filter_list as $filter)
        {
            if($filter->filter_value != 0)
            {
                $query .= " AND " . $filter->filter_by  . " = " . $filter->filter_value;
            }
        }

        // search by id
        if($search_keyword)
        {
            $query          .= " AND $search_by = '$search_keyword'";
        }

        if($next_page_value)
        {
            $query          .= " AND $next_page_reference < '$next_page_value'";
        }


        // sort query
        $query          .= " ORDER BY base_timestamp DESC LIMIT 100";

        // get result
        $data           = $database->getData($query);

        // modify result
        foreach($data as $i => $d)
        {
            $data[$i]["created_date_formatted"] = date("M d, Y - h:i:s A", strtotime($d["create_dt"]));;
        }


        return $data;
    }
    function loadScoreInfo()
    {
        // get information from get
        $date_filter                        = $_GET['date_filter'];
        $search_by                          = trim($_GET['search_by']);
        $search_keyword                     = trim($_GET['search_keyword']);
        $next_page_reference                = $_GET['next_page_reference'];
        $next_page_value                    = $_GET['next_page_value'];

        // create filter array
        $filter_list                        = array();
        $filter_list[0]                     = new stdClass();
        $filter_list[0]->filter_by          = 'sports_id';
        $filter_list[0]->filter_value       = $_GET['sports_filter'];
        $filter_list[1]                     = new stdClass();
        $filter_list[1]->filter_by          = 'fixture_id';
        $filter_list[1]->filter_value       = $_GET['locations_filter'];
        $filter_list[2]                     = new stdClass();
        $filter_list[2]->filter_by          = 'league_id';
        $filter_list[2]->filter_value       = $_GET['leagues_filter'];
        $filter_list[3]                     = new stdClass();
        $filter_list[3]->filter_by          = 'bet_status';
        $filter_list[3]->filter_value       = $_GET['status_filter'];

        // create date filter
        $start_time     = date("Y-m-d H:i:s", strtotime($date_filter . "-1" . " days"));
        $end_time       = date("Y-m-d H:i:s", strtotime($date_filter . "+1" . " days"));

        // create query connection
        $database       = new DatabaseConnection(_DB_NAME_LOG_DB);
        $query          = "";

        // select query
        $query          .= "SELECT * FROM rmq2_live_score_log_real";

        // filter by date
        $query          .= " WHERE create_dt between '$start_time' and '$end_time'";

        // create filter based on filter list
        foreach($filter_list as $filter)
        {
            if($filter->filter_value != 0)
            {
                $query .= " AND " . $filter->filter_by  . " = " . $filter->filter_value;
            }
        }

        // search by id
        if($search_keyword)
        {
            $query          .= " AND $search_by = '$search_keyword'";
        }

        if($next_page_value)
        {
            $query          .= " AND $next_page_reference < '$next_page_value'";
        }


        // sort query
        $query          .= " ORDER BY base_timestamp DESC LIMIT 100";

        // get result
        $data           = $database->getData($query);

        // modify result
        foreach($data as $i => $d)
        {
            $data[$i]["created_date_formatted"] = date("M d, Y - h:i:s A", strtotime($d["create_dt"]));;
        }


        return $data;
    }
    function loadScoreInfoPrematch()
    {
        // get information from get
        $date_filter                        = $_GET['date_filter'];
        $search_by                          = trim($_GET['search_by']);
        $search_keyword                     = trim($_GET['search_keyword']);
        $next_page_reference                = $_GET['next_page_reference'];
        $next_page_value                    = $_GET['next_page_value'];

        // create filter array
        $filter_list                        = array();
        $filter_list[0]                     = new stdClass();
        $filter_list[0]->filter_by          = 'sports_id';
        $filter_list[0]->filter_value       = $_GET['sports_filter'];
        $filter_list[1]                     = new stdClass();
        $filter_list[1]->filter_by          = 'fixture_id';
        $filter_list[1]->filter_value       = $_GET['locations_filter'];
        $filter_list[2]                     = new stdClass();
        $filter_list[2]->filter_by          = 'league_id';
        $filter_list[2]->filter_value       = $_GET['leagues_filter'];
        $filter_list[3]                     = new stdClass();
        $filter_list[3]->filter_by          = 'bet_status';
        $filter_list[3]->filter_value       = $_GET['status_filter'];

        // create date filter
        $start_time     = date("Y-m-d H:i:s", strtotime($date_filter . "-1" . " days"));
        $end_time       = date("Y-m-d H:i:s", strtotime($date_filter . "+1" . " days"));

        // create query connection
        $database       = new DatabaseConnection(_DB_NAME_LOG_DB);
        $query          = "";

        // select query
        $query          .= "SELECT * FROM rmq2_live_score_log_prematch";

        // filter by date
        $query          .= " WHERE create_dt between '$start_time' and '$end_time'";

        // create filter based on filter list
        foreach($filter_list as $filter)
        {
            if($filter->filter_value != 0)
            {
                $query .= " AND " . $filter->filter_by  . " = " . $filter->filter_value;
            }
        }

        // search by id
        if($search_keyword)
        {
            $query          .= " AND $search_by = '$search_keyword'";
        }

        if($next_page_value)
        {
            $query          .= " AND $next_page_reference < '$next_page_value'";
        }


        // sort query
        $query          .= " ORDER BY base_timestamp DESC LIMIT 100";

        // get result
        $data           = $database->getData($query);

        // modify result
        foreach($data as $i => $d)
        {
            $data[$i]["created_date_formatted"] = date("M d, Y - h:i:s A", strtotime($d["create_dt"]));;
        }


        return $data;
    }
    function loadMatchInfo()
    {
        // get information from get
        $date_filter                        = $_GET['date_filter'];
        $search_by                          = trim($_GET['search_by']);
        $search_keyword                     = trim($_GET['search_keyword']);
        $next_page_reference                = $_GET['next_page_reference'];
        $next_page_value                    = $_GET['next_page_value'];

        // create filter array
        $filter_list                        = array();
        $filter_list[0]                     = new stdClass();
        $filter_list[0]->filter_by          = 'sports_id';
        $filter_list[0]->filter_value       = $_GET['sports_filter'];
        $filter_list[1]                     = new stdClass();
        $filter_list[1]->filter_by          = 'fixture_id';
        $filter_list[1]->filter_value       = $_GET['locations_filter'];
        $filter_list[2]                     = new stdClass();
        $filter_list[2]->filter_by          = 'league_id';
        $filter_list[2]->filter_value       = $_GET['leagues_filter'];
        $filter_list[3]                     = new stdClass();
        $filter_list[3]->filter_by          = 'bet_status';
        $filter_list[3]->filter_value       = $_GET['status_filter'];

        // create date filter
        $start_time     = date("Y-m-d H:i:s", strtotime($date_filter . "-1" . " days"));
        $end_time       = date("Y-m-d H:i:s", strtotime($date_filter . "+1" . " days"));

        // create query connection
        $database       = new DatabaseConnection(_DB_NAME_LOG_DB);
        $query          = "";

        // select query
        $query          .= "SELECT * FROM bet_log_data_rmq_real";

        // filter by date
        $query          .= " WHERE create_dt between '$start_time' and '$end_time'";

        // create filter based on filter list
        foreach($filter_list as $filter)
        {
            if($filter->filter_value != 0)
            {
                $query .= " AND " . $filter->filter_by  . " = " . $filter->filter_value;
            }
        }

        // search by id
        if($search_keyword)
        {
            $query          .= " AND $search_by = '$search_keyword'";
        }

        if($next_page_value)
        {
            $query          .= " AND $next_page_reference < '$next_page_value'";
        }


        // sort query
        $query          .= " ORDER BY base_timestamp DESC LIMIT 100";

        // get result
        $data           = $database->getData($query);

        // modify result
        foreach($data as $i => $d)
        {
            $data[$i]["created_date_formatted"] = date("M d, Y - h:i:s A", strtotime($d["create_dt"]));;
        }


        return $data;
    }
    function loadMatchInfoPrematch()
    {
        // get information from get
        $date_filter                        = $_GET['date_filter'];
        $search_by                          = trim($_GET['search_by']);
        $search_keyword                     = trim($_GET['search_keyword']);
        $next_page_reference                = $_GET['next_page_reference'];
        $next_page_value                    = $_GET['next_page_value'];

        // create filter array
        $filter_list                        = array();
        $filter_list[0]                     = new stdClass();
        $filter_list[0]->filter_by          = 'sports_id';
        $filter_list[0]->filter_value       = $_GET['sports_filter'];
        $filter_list[1]                     = new stdClass();
        $filter_list[1]->filter_by          = 'fixture_id';
        $filter_list[1]->filter_value       = $_GET['locations_filter'];
        $filter_list[2]                     = new stdClass();
        $filter_list[2]->filter_by          = 'league_id';
        $filter_list[2]->filter_value       = $_GET['leagues_filter'];
        $filter_list[3]                     = new stdClass();
        $filter_list[3]->filter_by          = 'bet_status';
        $filter_list[3]->filter_value       = $_GET['status_filter'];

        // create date filter
        $start_time     = date("Y-m-d H:i:s", strtotime($date_filter . "-1" . " days"));
        $end_time       = date("Y-m-d H:i:s", strtotime($date_filter . "+1" . " days"));

        // create query connection
        $database       = new DatabaseConnection(_DB_NAME_LOG_DB);
        $query          = "";

        // select query
        $query          .= "SELECT * FROM bet_log_data_rmq_prematch";

        // filter by date
        $query          .= " WHERE create_dt between '$start_time' and '$end_time'";

        // create filter based on filter list
        foreach($filter_list as $filter)
        {
            if($filter->filter_value != 0)
            {
                $query .= " AND " . $filter->filter_by  . " = " . $filter->filter_value;
            }
        }

        // search by id
        if($search_keyword)
        {
            $query          .= " AND $search_by = '$search_keyword'";
        }

        if($next_page_value)
        {
            $query          .= " AND $next_page_reference < '$next_page_value'";
        }


        // sort query
        $query          .= " ORDER BY base_timestamp DESC LIMIT 100";

        // get result
        $data           = $database->getData($query);

        // modify result
        foreach($data as $i => $d)
        {
            $data[$i]["created_date_formatted"] = date("M d, Y - h:i:s A", strtotime($d["create_dt"]));;
        }


        return $data;
    }
    function loadSportsList()
    {
        $database       = new DatabaseConnection(_DB_NAME_WEB);
        $data           = $database->getData("SELECT * FROM lsports_sports WHERE is_use = '1'");
        return $data;
    }
    function loadLeaguesList()
    {
        $database       = new DatabaseConnection(_DB_NAME_WEB);
        $date_now       = date("Y/m/d");
        $data           = $database->getData("SELECT * FROM lsports_leagues WHERE is_use = 1 GROUP BY display_name;");
        return $data;
    }
    function loadLocationsList()
    {
        $database       = new DatabaseConnection(_DB_NAME_WEB);
        $data           = $database->getData("SELECT * FROM lsports_locations WHERE is_use = '1'");
        return $data;
    }
    function loadStatus()
    {
        $data                       = array();
        $data[0]                    = new stdClass();
        $data[0]->status_id         = 1;
        $data[0]->status_label      = "배팅가능";
        $data[1]                    = new stdClass();
        $data[1]->status_id         = 2;
        $data[1]->status_label      = "배팅마감";
        $data[2]                    = new stdClass();
        $data[2]->status_id         = 3;
        $data[2]->status_label      = "종료";
        $data[3]                    = new stdClass();
        $data[3]->status_id         = 4;
        $data[3]->status_label      = "대기";

        return $data;
    }
    function dd($data)
    {
        echo "<pre>";
        echo print_r($data);
        echo "</pre>";
        die();
    }
?>