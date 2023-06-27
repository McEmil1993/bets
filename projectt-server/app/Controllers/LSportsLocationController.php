<?php namespace App\Controllers;

use App\Models\GameModel;
use App\Models\LSportsLeaguesModel;
use App\Models\LSportsLocationsModel;
use App\Models\LSportsSportsModel;
use CodeIgniter\API\ResponseTrait;

class LSportsLocationController extends BaseController
{
    use ResponseTrait;

    public function locationList(){
        $lsSportsModel = new LSportsSportsModel();
        $sportId = isset($_POST['sports_id']) ? [$_POST['sports_id']] : $lsSportsModel->isUseIdList(1);
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

        $lsLocationModel = new LSportsLocationsModel();
        $sql = "SELECT       fix.fixture_location_id as id, fix.fixture_location_name as name
                FROM            lsports_bet as bet
                LEFT JOIN       lsports_fixtures as fix
                ON       bet.fixture_id = fix.fixture_id
                WHERE    1 = 1 
                AND      fix.fixture_sport_id IN (".implode(',', $sportId).") 
                AND      bet.markets_id IN (".implode(',', [ 1, 2, 3, 7, 52, 101, 102, 16, 28, 220, 221, 226, 342, 1050, 1328, 1332 ]).")  
                AND      fix.fixture_league_id NOT IN (32931, 37287, 37494, 37364, 37994, 37365, 38301, 37493, 38362, 37273, 37814)
                AND      IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) BETWEEN '".$startTime."' AND '".$endTime."'
                GROUP BY fix.fixture_location_id
                ";

        $this->logger->debug($sql);

        $locationList = $lsLocationModel->db->query(
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
                'locationList' => $locationList,
            ]
        ];
        return $this->respond($response, 200);

    }
}
