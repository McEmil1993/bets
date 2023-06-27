<?php namespace App\Controllers;

use App\Models\GameModel;
use App\Models\LSportsLeaguesModel;
use App\Models\LSportsLocationsModel;
use App\Models\LSportsSportsModel;
use CodeIgniter\API\ResponseTrait;

class LSportsLeagueController extends BaseController
{
    use ResponseTrait;

    public function leagueList(){
        $lsSportsModel = new LSportsSportsModel();
        $sportId = isset($_POST['sports_id']) ? [$_POST['sports_id']] : $lsSportsModel->isUseIdList(1);
        //$sportId = isset($_POST['sports_id']) &&  $_POST['sports_id'] != 0 ? [$_POST['sports_id']] : [6046, 48242, 154914, 154830];
        $locationId = isset($_POST['location_id']) ? $_POST['location_id'] : '0';
        // $isPlay = isset($_POST['isPlay']) ? $_POST['isPlay'] : 'N'; // 배팅 가능한 것만 여부

        $day = isset($_POST['day']) ? $_POST['day'] : NULL;

        if ($day != NULL) {
            $day = str_replace('/', '-', $day);
        } else {
            $day = date("m-d", strtotime("Now"));
        }

        $his = 'H:i:s';
        if (isset($_POST['day']) && $_POST['day'] != date("m/d", strtotime("Now"))) {
            $his = '00:00:00';
        }

        $plusDay = '1';
        if (isset($_POST['day']) && $_POST['day'] != date("m/d", strtotime("Now"))) {
            $plusDay = '2';
        }

        $startTime = date("Y-$day $his", strtotime("Now"));
        $endTime = date("Y-m-d 00:00:00", strtotime("+".$plusDay." days"));

        $lsLeagueModel = new LSportsLeaguesModel();
        $sql = "SELECT       fix.fixture_league_id as id, fix.fixture_league_name as name
                FROM            lsports_bet as bet
                LEFT JOIN       lsports_fixtures as fix
                ON       bet.fixture_id = fix.fixture_id
                WHERE    IF('ON' = bet.passivity_flag AND bet.bet_status_passivity is NOT NULL ,bet.bet_status_passivity,bet.bet_status) = 1 ";
        if ($locationId != 0) {
            $sql = $sql . " AND fix.fixture_location_id = $locationId ";
        }
        $sql = $sql." AND      fix.fixture_sport_id IN (".implode(',', $sportId).") 
                 AND      bet.markets_id IN (".implode(',', [ 1, 2, 3, 7, 52, 101, 102, 16, 28, 220, 221, 226, 342, 1050, 1328, 1332 ]).")  
                 AND      fix.fixture_league_id NOT IN (32931, 37287, 37494, 37364, 37994, 37365, 38301, 37493, 38362, 37273, 37814)
                 AND      IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) BETWEEN \"".$startTime."\" AND \"".$endTime."\"
                 GROUP BY fix.fixture_league_id
                ";

        $this->logger->debug($sql);

        $leagueList = $lsLeagueModel->db->query(
            $sql,
            [
//                $sportId,
//                [ 1, 2, 3, 7, 52, 101, 102, 16, 28, 220, 221, 226, 342, 1050, 1328, 1332 ],
//                [1, 2, 3, 7, 17, 21, 28, 34, 35, 41, 42, 42, 43, 44, 45, 46, 47, 48, 49, 52, 53, 63, 64, 65, 66, 67, 77, 101, 102, 113, 202, 203, 204, 205, 206, 211, 220, 221, 226,342, 348, 349, 352, 353, 464, 866],
            ])->getResultArray();

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'leagueList' => $leagueList,
            ]
        ];
        return $this->respond($response, 200);

    }
}
