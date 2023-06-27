<?php
    /* initialize session */
    session_start();

    /* headers */
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Type: text/html; charset=UTF-8');

    /* load configurations */
    include_once('./dividend_functions.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
    include_once(_BASEPATH . '/common/_common_inc_class.php');

    /* connect to database */
    include_once('./database_connection.php');

    $response = null;
    if(isset($_GET["action"]))
    {
        switch ($_GET["action"])
        {
            case 'load_sports':
                $response = loadSportsList();
            break;

            case 'load_status':
                $response = loadStatus();
            break;  
            case 'load_locations':
                $response = loadLocationsList();
            break;
    
            case 'load_leagues':
                $response = loadLeaguesList();
            break; 
    
            case 'load_match_info':
                $response = loadMatchInfo();
            break;
            case 'load_match_info_prematch':
                $response = loadMatchInfoPrematch();
            break;
            case 'load_score_info':
                $response = loadScoreInfo();
            break;
            case 'load_score_info_prematch':
                $response = loadScoreInfoPrematch();
            break;
            case 'load_odds_info':
                $response = loadOddsInfo();
            break;
            case 'load_odds_info_prematch':
                $response = loadOddsInfoPrematch();
            break;
        }
    }


    $returned_data              = new stdClass();
    $returned_data->response    = $response; 
    echo json_encode($returned_data);
?>