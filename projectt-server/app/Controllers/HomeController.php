<?php namespace App\Controllers;

use App\Models\GameModel;
use App\Models\MemberBetDetailModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Models\LSportsFixturesModel;
use App\Models\LSportsSportsModel;
use App\Models\MainModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\PullOperations;
use CodeIgniter\Log\Logger;
use App\Util\CodeUtil;

class HomeController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    
    	$LSportsFixturesModel = new LSportsFixturesModel();
    
        // 인기베팅데이터
        $mainModel = new MainModel();
        $memberModel = new MemberModel();

        try{
            $bestBetList = $this->getPplBetGame($mainModel);
            $eventList = $this->getEvents($memberModel);
            $noticeList = $this->getMainMenuBoard($mainModel);
            $exchangeList = $this->getMainExchange($mainModel);
        } catch (\mysqli_sql_exception $e) {

        }
        
        // $nextGameList = $this->getNextGame($memberModel);
        /*if ($viewRoot == 'web') {
            $displayType = 1;
        } else {
            $displayType = 2;
        }*/

        $bannerList = $this->getMainBanners($mainModel);
        $popupList = $this->getMainPopups($mainModel);
        
        return view("$viewRoot/index", [
              'bestBetList' => $bestBetList
            , 'eventList' => $eventList
            , 'noticeList' => $noticeList
            , 'bannerList' => $bannerList
            , 'popupList' => $popupList
            , 'exchangeList' => $exchangeList
        ]);
    }

    public function doInsertFromMydbToMainDb() {
        $mainModel = new MainModel();
        $memberModel = new MemberModel();

        $bestBetList  = $this->getBestGame($memberModel);
        $eventList    = $this->getEvents($memberModel);
        $noticeList   = $this->getMenuBoard($memberModel);
        $exchangeList = $this->getExchange($memberModel);
        $bannerList   = $this->getBanners($memberModel);
        $popupList    = $this->getPopups($memberModel);

        $this->insertPplBetGame($bestBetList, $mainModel);
        $this->insertEvents($eventList, $mainModel);
        $this->insertMenuBoard($noticeList, $mainModel);
        $this->insertExchange($exchangeList, $mainModel);
        $this->insertBanners($bannerList, $mainModel);
        $this->insertPopups($popupList, $mainModel);
    }

    public function insertPplBetGame($bestBetList, $mainModel) {

        $mainModel->db->query("truncate ppl_bet_game");
        $arrInsertData = array();

        foreach ($bestBetList as $row) {
            $fixture_id = $row['fixture_id'];
            $start_dt = $row['start_dt'];
            $fixture_location_id = $row['fixture_location_id'];
            $fixture_sport_id = $row['fixture_sport_id'];
            $bet_type = $row['bet_type'];
            $league_display_name = $row['league_display_name'];
            $league_name = $row['league_name'];
            $league_image_path = $row['league_image_path'];
            $p1_team_name = $row['p1_team_name'];
            $p1_display_name = $row['p1_display_name'];
            $p2_team_name = $row['p2_team_name'];
            $p2_display_name = $row['p2_display_name'];
            $total_bet_money = true === isset($row['total_bet_money']) ? $row['total_bet_money'] : 0;
            $bet_price = $row['bet_price'];
            $bet_name = $row['bet_name'];

            $addQueryData = "($fixture_id,'$start_dt', $fixture_location_id,$fixture_sport_id,$bet_type"
                    . ",'$league_display_name','$league_name','$league_image_path','$p1_team_name','$p1_display_name'"
                    . ",'$p2_team_name','$p2_display_name',$total_bet_money,$bet_price,'$bet_name')";

            array_push($arrInsertData, $addQueryData);
        }

        if (count($arrInsertData) > 0) {

            $sql = "INSERT INTO `ppl_bet_game`
                        (fixture_id, start_dt, fixture_location_id,fixture_sport_id,bet_type
                        ,league_display_name,league_name,league_image_path,p1_team_name,p1_display_name
                        ,p2_team_name,p2_display_name,total_bet_money,bet_price,bet_name) VALUES " . implode(',', $arrInsertData);
            
            //$this->logger->info('------------- insertPplBetGame sql ==>' . $sql);
            $mainModel->db->query($sql);
                   
        }
    }

    public function insertEvents($eventList, $mainModel) {
        $mainModel->db->query("truncate main_events");
        $arrInsertData = array();
        foreach ($eventList as $row) {
            $idx = $row['idx'];
            $name = $row['name'];

            $create_dt = $row['create_dt'];
            $addQueryData = "($idx,'$name','$create_dt')";
            array_push($arrInsertData, $addQueryData);
        }
        
        if (count($arrInsertData) > 0) {
            $mainModel->db->query("INSERT INTO main_events (idx, name,create_dt) VALUES " . implode(',', $arrInsertData));
        }
    }

    public function insertMenuBoard($noticeList, $mainModel) {
        $mainModel->db->query("truncate main_menu_board");
        $arrInsertData = array();
        foreach ($noticeList as $row) {
            $idx = $row['idx'];
            $title = $row['title'];
            $create_dt = $row['create_dt'];
            $addQueryData = "($idx,'$title','$create_dt')";
            array_push($arrInsertData, $addQueryData);
        }

        if (count($arrInsertData) > 0) {
            $mainModel->db->query("INSERT INTO main_menu_board (idx, title,create_dt) VALUES " . implode(',', $arrInsertData));
        }
    }

    public function insertExchange($exchangeList, $mainModel) {
        $mainModel->db->query("truncate main_meh");
        $arrInsertData = array();
        foreach ($exchangeList as $row) {
            $id = $row['id'];
            $nick_name = $row['nick_name'];
            $create_dt = $row['create_dt'];
            $money = $row['money'];
            $addQueryData = "('$id','$nick_name','$create_dt',$money)";
            array_push($arrInsertData, $addQueryData);
        }

        if (count($arrInsertData) > 0) {
            $sql = "INSERT INTO main_meh(id,nick_name,create_dt,money) VALUES " . implode(',', $arrInsertData);
            $this->logger->info('------------- insertExchange sql ==>' . $sql);
              
            $mainModel->db->query($sql);
        }
    }

    public function insertBanners($bannerList, $mainModel) {
        $mainModel->db->query("truncate main_banners");
        $arrInsertData = array();
        foreach ($bannerList as $row) {
            $idx = $row['idx'];
            $thumbnail = $row['thumbnail'];
            $display_type = $row['display_type'];
            $status = $row['status'];
            $rank = $row['rank'];
            $create_dt = $row['create_dt'];
            $update_dt = $row['update_dt'];
            $delete_dt = $row['delete_dt'];

            $addQueryData = "($idx,'$thumbnail',$display_type,$status,$rank,'$create_dt','$update_dt','$delete_dt')";
            array_push($arrInsertData, $addQueryData);
        }

        if (count($arrInsertData) > 0) {
            $mainModel->db->query("INSERT INTO main_banners (idx,thumbnail,display_type,status,rank,create_dt,update_dt,delete_dt) VALUES " . implode(',', $arrInsertData));
        }
    }

    public function insertPopups($popupList, $mainModel) {
        $mainModel->db->query("truncate main_popups");
        $arrInsertData = array();
        foreach ($popupList as $row) {
            $idx = $row['idx'];
            $thumbnail = $row['thumbnail'];
            $status = $row['status'];
            $rank = $row['rank'];
            $create_dt = $row['create_dt'];
            $update_dt = $row['update_dt'];
            $delete_dt = $row['delete_dt'];

            $addQueryData = "($idx,'$thumbnail',$status,$rank,'$create_dt','$update_dt','$delete_dt')";
            array_push($arrInsertData, $addQueryData);
        }

        if (count($arrInsertData) > 0) {
            $mainModel->db->query("INSERT INTO main_popups (idx,thumbnail,status,rank,create_dt,update_dt,delete_dt) VALUES " . implode(',', $arrInsertData));
        }
    }

    public function getEvents($model) {
        $sql = "select
				idx
				,name
				,create_dt
				,'event' as t
			from events
			where
				status = 1
			and
				del_flag = 0
			order by
				create_dt desc
			limit 0,8";
    	
    	$eventList = $model->db->query($sql)->getResultArray();
        return $eventList;
    	/*$sql =
	    	"select
					idx
					,title
					,create_dt
					,'notice' as t
				from
					menu_board
				WHERE 
					display = 1
				order by
					create_dt desc
				limit 0,8";
    	
    	$noticeList = $model->db->query($sql)->getResultArray();
        
        // 환전내역
        $sql =
	    	"select id, nick_name, meh.money, create_dt
                    from
                        member_money_exchange_history as meh 
                    join 
                        member on meh.member_idx = member.idx 
                    WHERE 
                        meh.status = 3
                    order by
                        meh.idx desc
                    limit 0,8";

        $exchangeList = $model->db->query($sql)->getResultArray();

        return $exchangeList;*/
    }
    
    public function getMenuBoard($model) {
        $sql = "select
					idx
					,title
					,create_dt
					,'notice' as t
				from
					menu_board
				WHERE 
					display = 1
				order by
					create_dt desc
				limit 0,8";

        $noticeList = $model->db->query($sql)->getResultArray();

        return $noticeList;
    }
    
    public function getExchange($model) {
        // 환전내역
        $sql = "select id, nick_name, meh.money, create_dt
                    from
                        member_money_exchange_history as meh 
                    join 
                        member on meh.member_idx = member.idx 
                    WHERE 
                        meh.status = 3
                    order by
                        meh.idx desc
                    limit 0,8";

        $exchangeList = $model->db->query($sql)->getResultArray();

        return $exchangeList;
    }

    public function getMainEvents($model) {
        $sql = "select
                    idx
                    ,name
                    ,create_dt
                    ,'event' as t
		from main_events order by create_dt desc";
        $eventList = $model->db->query($sql)->getResultArray();

        return $eventList;
    }

    public function getMainMenuBoard($model) {
        $sql = "select
                idx
                ,title
                ,create_dt
                ,'notice' as t
                from main_menu_board order by create_dt desc";
        $noticeList = $model->db->query($sql)->getResultArray();
        return $noticeList;
    }

    public function getMainExchange($model) {
        // 환전내역
        $sql = "select id, nick_name, money, create_dt
                    from main_meh 
                    order by create_dt desc ";
        $exchangeList = $model->db->query($sql)->getResultArray();
        return $exchangeList;
    }

    public function getBanners($model) {
        $sql = "select * from banners where status = 1 order by rank,create_dt desc LIMIT 0,5";

        $bannerList = $model->db->query($sql)->getResultArray();
        return $bannerList;
    }

    public function getMainBanners($model) {
        $sql = "select * from main_banners where status = 1 order by rank,create_dt desc";
        $bannerList = $model->db->query($sql, [])->getResultArray();
        return $bannerList;
    }
    public function getAllBanners($model) {
        $sql = "select * from banners where status = 1 and display_type = 1 
                UNION ALL 
                select * from banners where status = 1 and display_type = 2
                order by rank,create_dt desc LIMIT 0,5";
        $bannerList = $model->db->query($sql)->getResultArray();
        return $bannerList;
    }
    
    public function getPopups($model) {
        $sql = "select * from popups where status = 1 order by rank desc,create_dt desc LIMIT 0,4";

        $popupList = $model->db->query($sql)->getResultArray();
        return $popupList;
    }
    
    public function getMainPopups($model) {
        $sql = "select * from main_popups order by rank desc,create_dt desc";
        $popupList = $model->db->query($sql)->getResultArray();
        return $popupList;

    }
    
      // 인기베팅 경기
    public function getPplBetGame($mainModel) {
        $fixtureCount = 0;
        $sql = "select * from ppl_bet_game";
        $tmList = $mainModel->db->query($sql)->getResultArray();
        $result = array();
        foreach ($tmList as $key => $value) {
            if (!isset($result[$value['fixture_id']])) {
                $fixtureCount += 1;
                if (9 == $fixtureCount)
                    break;

                $result[$value['fixture_id']] = array(
                    'fixture_id' => $value['fixture_id'], 
                    'start_dt' => $value['start_dt'], 
                    'fixture_location_id' => $value['fixture_location_id'], 
                    'fixture_sport_id' => $value['fixture_sport_id'], 
                    'league_display_name' => $value['league_display_name'], 
                    'league_name' => $value['league_name'], 
                    'league_image_path' => $value['league_image_path'], 
                    'p1_display_name' => $value['p1_display_name'], 
                    'p2_display_name' => $value['p2_display_name'], 
                    'p1_team_name' => $value['p1_team_name'], 
                    'p2_team_name' => $value['p2_team_name'], 
                    'bet_type' => $value['bet_type'],
                    'win_bet_price' => 0, 
                    'lose_bet_price' => 0, 
                    'draw_bet_price' => 0,
                );
            }

            if ('1' == $value['bet_name']) {
                $result[$value['fixture_id']]['win_bet_price'] = $value['bet_price'];
            } else if ('2' == $value['bet_name']) {
                $result[$value['fixture_id']]['lose_bet_price'] = $value['bet_price'];
            } else {
                $result[$value['fixture_id']]['draw_bet_price'] = $value['bet_price'];
            }
        }

        // 키값 재정의
        $i = 0;
        foreach ($result as $key => $val) {
            unset($result[$key]);
            $new_key = $i;
            $result[$new_key] = $val;
            $i++;
        }

        return $result;
    }
    
    // 인기베팅 경기
    public function getBestGame($LSportsFixturesModel){
        $fixtureCount = 0;
        $sql="select
                lsports_fixtures.fixture_id,
                IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity,lsports_fixtures.fixture_start_date) as start_dt,
                fixture_location_id,
                fixture_sport_id,
                lsports_fixtures.bet_type,
                league.display_name as league_display_name,
                league.name as league_name,
                league.image_path as league_image_path,
                p1.team_name as p1_team_name, p1.display_name as p1_display_name,
                p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                (select sum(mb_bet.total_bet_money) as total_bet_money  from member_bet_detail as detail
                                                                                                         LEFT JOIN member_bet as mb_bet ON detail.bet_idx = mb_bet.idx
                                                                                                         where detail.ls_fixture_id = lsports_fixtures.fixture_id
                                                                                                         AND detail.bet_type = 1
                                                                                                         ) as total_bet_money
                                                             , bet_price
                                                             , bet_name
                                from lsports_fixtures
                        left join lsports_participant as p1 on fixture_participants_1_id = p1.fp_id
                        left join lsports_participant as p2 on fixture_participants_2_id = p2.fp_id
                        left join lsports_leagues as league on fixture_league_id = league.id
                        left join lsports_sports as sports on fixture_sport_id = sports.id
                        left join lsports_bet as bet on lsports_fixtures.fixture_id = bet.fixture_id
                        where lsports_fixtures.bet_type = 1
                        and fixture_start_date > now()
                        and display_status = 1
                        and league.is_use = 1
                        and league.bet_type = 1
                        and sports.is_use = 1
                        and sports.bet_type =1
                        and league.id in (67,8363,61,65,4,1552,1958,7014,7807,183,4768,4146,4811,2540,4914,64,4179,4178,19)

                        and (CASE 
                        WHEN lsports_fixtures.fixture_sport_id = 6046 THEN bet.markets_id = 1
                        WHEN lsports_fixtures.fixture_sport_id = 48242 THEN bet.markets_id = 226
                        WHEN lsports_fixtures.fixture_sport_id = 154914 THEN bet.markets_id = 226
                        WHEN lsports_fixtures.fixture_sport_id = 154830 THEN bet.markets_id = 52
                        WHEN lsports_fixtures.fixture_sport_id = 35232 THEN bet.markets_id = 1
                        WHEN lsports_fixtures.fixture_sport_id = 687890 THEN bet.markets_id = 52
                        else 1 = 1 END)

                        order by fixture_start_date asc, total_bet_money desc
                        limit 0, 30";
        $tmList = $LSportsFixturesModel->db->query($sql)->getResultArray();
        return $tmList;
     
    }
    
    
    
       // 인기베팅 경기
    public function getNextGame($memberModel){
     
        $sql="select
                lsports_fixtures.fixture_id,
                        fixture_start_date,
                        p1.team_name as p1_team_name, p1.display_name as p1_display_name,
                        p2.team_name as p2_team_name, p2.display_name as p2_display_name
              from lsports_fixtures
                        left join lsports_participant as p1 on fixture_participants_1_id = p1.fp_id
                        left join lsports_participant as p2 on fixture_participants_2_id = p2.fp_id
                        left join lsports_leagues as league on fixture_league_id = league.id
                        left join lsports_sports as sports on fixture_sport_id = sports.id
                        left join lsports_bet as bet on lsports_fixtures.fixture_id = bet.fixture_id
                        where lsports_fixtures.bet_type = 1
                        and fixture_start_date > now()
                        and display_status = 1
                        and league.is_use = 1
                        and league.bet_type = 1
                        and sports.is_use = 1
                        and sports.bet_type =1
                        and league.id in (67,8363,61,65,4,1552,1958,7014,7807,183,4768,4146,4811,2540,4914,64,4179,4178,19)

                        and (CASE 
                        WHEN lsports_fixtures.fixture_sport_id = 6046 THEN bet.markets_id = 1
                        WHEN lsports_fixtures.fixture_sport_id = 48242 THEN bet.markets_id = 226
                        WHEN lsports_fixtures.fixture_sport_id = 154914 THEN bet.markets_id = 226
                        WHEN lsports_fixtures.fixture_sport_id = 154830 THEN bet.markets_id = 52
                        WHEN lsports_fixtures.fixture_sport_id = 35232 THEN bet.markets_id = 1
                        WHEN lsports_fixtures.fixture_sport_id = 687890 THEN bet.markets_id = 52
                        else 1 = 1 END)
                        order by fixture_start_date asc
                        limit 1";
        
        //$this->logger->info($sql);
        $tmList = $memberModel->db->query($sql)->getResultArray();
        return $tmList;
     
    }
    
    public function join()
    {
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    	
    	$memberModel = new MemberModel();
    	$str_sql_config = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'join_excluded_bank'"; // 머지 대상 아님
    	$arr_config = $memberModel->db->query($str_sql_config)->getResultArray()[0]['set_type_val'];
    	
    	$sql = "select idx, account_code, account_name from account where idx not in ($arr_config)";
    	$bankList = $memberModel->db->query(
    			$sql
    			)->getResultArray();
    	
    	return view("$viewRoot/join", [
			'bankList' => $bankList
    	]);
    }
   
 
    public function login()
    {
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    	
    	return view("$viewRoot/index", [
    	]);
    }
    
    public function inspection()
    {       
        $chkMobile = CodeUtil::rtn_mobile_chk();
        
        $memberModel = new MemberModel();
        $sql = "SELECT * FROM inspection WHERE idx = 1";
        $result = $memberModel->db->query($sql)->getResultArray()[0];
        
        $sql = "SELECT * FROM t_game_config where set_type = 'service_site'";
        $gameConfig = $memberModel->db->query($sql)->getResultArray()[0];
       
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
      
        if($gameConfig['set_type_val'] == 'Y'){
            return view("$viewRoot/inspection", [
                'system_time' => config(App::class)->CheckTimeMessage,
                'system_mes' => $result
            ]);
        }else{
            return redirect()->to(base_url("/$viewRoot/index"));
        }
    }
}
