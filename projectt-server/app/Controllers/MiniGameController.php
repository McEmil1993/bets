<?php

namespace App\Controllers;

use App\Models\GameModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\MiniGameModel;
use App\Util\BetDataUtil;
use App\Util\CodeUtil;
use App\Util\DateTimeUtil;
use App\Models\MemberModel;
use App\Entities\Member;
use App\Util\Calculate;

class MiniGameController extends BaseController {

    use ResponseTrait;

    // 파워볼 스케쥴
    public function pbInitData() {
        $game = isset($_REQUEST['game']) ? $_REQUEST['game'] : 'powerball';
        $bet_type = 3;
        if ($game == 'powerball') {
            $bet_type = 3;
        } else if ($game == 'pladder') {
            $bet_type = 4;
        } else if ($game == 'kladder') {
            $bet_type = 5;
        } else {
            $bet_type = 6;
        }

        // 개발서버이면 라이브서버에서 데이터를 받아온다.
        $url = "http://mg.korodd.com/getdata.php?game=$game";
        if (config(App::class)->IsDevServer)
            $url = "https://kw-02.com/minigame/getMiniGameData?game=$game";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
        //curl_setopt($ch, CURLOPT_POST,    true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));
        $response = curl_exec($ch);
        $result = json_decode($response);
        curl_close($ch);

        if (strpos($response, 'Not Accessible IP') !== false) {
            $this->logger->error('error : ' . $response . ' game : ' . $game);
            return;
        }

        if ($result == null) {
            return;
        }

        if (count($result) == 0) {
            //$this->logger->error('not minigameData');
            return;
        }

        $sql = array();
        if (isset($result) && count($result) > 0) {
            foreach ($result as $item) {
                $cnt = 0;
                if ($game == 'b_soccer') {
                    // 현재는 승무패, 언더오버만 사용
                    if ($item->type != '1x2' && $item->type != 'ou') {
                        continue;
                    }
                    $sdate = $item->dt;
                    $edate = '';
                    $league = $item->league;
                    $cnt = $item->oid;
                } else {
                    $sdate = $item->sdate;
                    $edate = $item->edate;
                    $league = '';
                    $cnt = $item->cnt;
                    if ($game == 'powerball') {
                        $start_dt = explode(' ', $sdate);
                        $cnt_date = $start_dt[1];
                        $cnt_date_arr = explode(':', $cnt_date);
                        $round = round(((+$cnt_date_arr[0] * 60) + +$cnt_date_arr[1]) / 5) + 1;
                        $cnt = $round;
                    }
                }
                $insertSql = '("'
                        . $item->game . '", "'
                        . $item->id . '", "'
                        . $cnt . '", "'
                        . $bet_type . '", "'
                        . $sdate . '", "'
                        . $edate . '", "'
                        . $league . '", "'
                        . addslashes(json_encode($item)) . '")';
                array_push($sql, $insertSql);
            }
        }

        $MiniGameModel = new MiniGameModel();
     
        if (count($sql) > 0) {
            try {
                //$MiniGameModel->db->transStart();
                $MiniGameModel->db->query(
                        'INSERT INTO `mini_game` ('
                        . 'game, '
                        . 'id, '
                        . 'cnt, '
                        . 'bet_type, '
                        . 'start_dt, '
                        . 'end_dt, '
                        . 'league, '
                        . 'result) VALUES '
                        . implode(',', $sql)
                        . ' ON DUPLICATE KEY UPDATE '
                        . 'game = VALUES(game), '
                        . 'id = VALUES(id), '
                        . 'start_dt = VALUES(start_dt), '
                        . 'end_dt = VALUES(end_dt), '
                        . 'league= VALUES(league), '
                        . 'result= VALUES(result)'
                );
                //$MiniGameModel->db->transComplete();
            } catch (\mysqli_sql_exception $e) {
                $query_str = (string) $MiniGameModel->getLastQuery();
                $this->logger->error("- pbInitData error query_string : " . $query_str);
                //$MiniGameModel->db->transRollback();
                return;
            }
        }

        //return view("$viewRoot/game03");
        //BetDataUtil::doMiniTotalCalculate($this->logger);
    }

    // 미니게임 정산
    public function doMiniTotalCalculate() {
        Calculate::doMiniTotalCalculate_renew($this->logger);
    }

    // 미니게임 데이터 가져오기
    public function getMiniGameData() {
        $game = isset($_REQUEST['game']) ? $_REQUEST['game'] : 'b_soccer';

        $url = "http://mg.korodd.com/getdata.php?game=$game";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }

    // 파워볼
    public function index() {
        //$viewRoot = strpos($_SERVER['REQUEST_URI'],'web/') > 0 ? 'web' : 'web';
        $id = session()->get('id');
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        
        $betType = isset($_REQUEST['betType']) ? $_REQUEST['betType'] : 3;
        $game = 'eospb5';
        $viewFile = 'mini_eos_power_ball';
        // close_time
        $sql_game_config = "SELECT set_type_val FROM t_game_config where set_type = 'mini_powerball_deadline'";
        if(4 == $betType){
            $game = 'pladder';
            $viewFile = 'mini_power_ladder';
            $sql_game_config = "SELECT set_type_val FROM t_game_config where set_type = 'mini_power_ladder_deadline'";
        }else if(5 == $betType){
            $game = 'kladder';
            $viewFile = 'mini_kino_ladder';
            $sql_game_config = "SELECT set_type_val FROM t_game_config where set_type = 'mini_kino_ladder_deadline'";
        }else if(15 == $betType){
            $game = 'powerball';
            $viewFile = 'mini_power_ball';
            $sql_game_config = "SELECT set_type_val FROM t_game_config where set_type = 'mini_powerball_deadline'";
        }
    	
    	if (false == session()->has('member_idx')) {
    		$url = base_url("/$viewRoot/index");
    		echo "<script>
    		alert('로그인 후 이용해주세요.');
    		window.location.href='$url';
    		</script>";
    		return;
    		
    		//alert('로그인 후 이용해주세요.');
    	}
    	
        if (false == session()->has('member_idx') || false == session()->has('level')) {
            //$url = base_url("/$viewRoot/index");
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (0 < session()->get('tm_unread_cnt')) {
        	$url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $MiniGameModel = new MiniGameModel();
        $game_config_result = $MiniGameModel->db->query($sql_game_config)->getResultArray();
        $close_time = $game_config_result[0]['set_type_val'];
        
        $sql = "SELECT * FROM mini_game_bet where game=?;";
        $result = $MiniGameModel->db->query($sql, [$game])->getResult();
        $betList = null;
        foreach ($result as $key => $value) {
            $betList[$value->markets_id] = $value;
        }

        if (false == session()->has('member_idx') || false == session()->has('level')) {
            //$url = base_url("/$viewRoot/login");
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        $m_level = session()->get('level');
        $sql_config = "SELECT * FROM mini_game_bet_config 
                        WHERE bet_type > 0 AND level = ? AND game = ?;";
        $result_config = $MiniGameModel->db->query($sql_config,[$m_level, $game])->getResult();
        foreach ($result_config as $key => $value) {
            $game_config = $value;
        }

        $m_idx = session()->get('member_idx');
        $sql_config = "SELECT * FROM mini_game_member_bet
                        WHERE member_idx = ?
                        AND bet_type = ?;"; // 파워볼 설정값
        $member_bet_list = $MiniGameModel->db->query($sql_config,[$m_idx, $betType])->getResult();
        $member_bet = [];
        foreach ($member_bet_list as $key => $value) {
            $member_bet[$key] = $value;
        }

        $sql_config = "SELECT * FROM mini_game where bet_type = ? and now() >= start_dt and now() <= end_dt order by id desc;"; // 파워볼 설정값
        $game_data_list = $MiniGameModel->db->query($sql_config, $betType)->getResult();
        $game_data = (Object) [];
        $game_data->id = '';
        foreach ($game_data_list as $key => $value) {
            $game_data = $value;
        }

        return view("$viewRoot/$viewFile", [
            'betList' => $betList,
            'game_config' => $game_config,
            'member_bet' => $member_bet,
            'game_data' => $game_data,
            'close_time' => $close_time
        ]);
    }

    public function getCurrentRound() {
        $game = $_REQUEST['game'];
        $bet_type = 3;
        if ('eospb5' == $game) {
            $bet_type = 3;
        } else if ('pladder' == $game) {
            $bet_type = 4;
        } else if ('kladder' == $game) {
            $bet_type = 5;
        } else if ('b_soccer' == $game) {
            $bet_type = 6;
        } else if ('powerball' == $game) {
            $bet_type = 15;
        } else {
            $response['messages'] = '요청이 잘못되어있습니다 game : ' . $game;
            return $this->fail($response);
        }

        $MiniGameModel = new MiniGameModel();
        $sql = "SELECT id, end_dt, cnt FROM mini_game where bet_type = ? and now() >= start_dt and now() <= end_dt order by id desc;";
        $resultList = $MiniGameModel->db->query($sql, [$bet_type])->getResult();
        if (0 == count($resultList)) {
            $response['messages'] = '미니게임 라운드 정보가 없습니다 game : ' . $game;
            return $this->fail($response);
        }

        $gameList = $resultList[0];
        $endDt = strtotime($gameList->end_dt);
        $nowDt = strtotime(date('Y-m-d H:i:s'));

        // 파워볼 현재 회차
        $current_round = $gameList->cnt;

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'remain_time' => $endDt - $nowDt,
                'id' => $gameList->id,
                'current_round' => $current_round
            ]
        ];
        return $this->respond($response, 200);
    }

    // 가상축구
    public function bsoccer() {
        $id = session()->get('id');
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        $league = $_REQUEST['game'];
    	
    	if (false == session()->has('member_idx') || false == session()->has('level')) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if (0 < session()->get('tm_unread_cnt')) {
    		$url = base_url("/web/note");
    		echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
        
        $m_idx = session()->get('member_idx');
        $m_level = session()->get('level');
        
        if(!is_int((int)$m_idx) || !is_int((int)$m_level) ){
            $this->logger->critical('premiumShip die');
            die();
        }
        
    	
    	$MiniGameModel = new MiniGameModel();
    	
    	$sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'mini_service_v_soccer' ";
        $result_game_config = $MiniGameModel->db->query($sql)->getResult();
    	if ('N' == $result_game_config[0]->set_type_val && 9 != session()->get('level')) {
    		$url = base_url("/");
    		echo "<script>
    		alert('점검 중으로 관리자에게 문의바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	
    	$sql = "SELECT id, end_dt FROM mini_game where now() >= start_dt and now() <= end_dt order by id desc;";
    	$resultList = $MiniGameModel->db->query($sql)->getResult();
    	if (0 == count($resultList)) {
    		$url = base_url("/$viewRoot/index");
    		echo "<script>
    		alert('가상축구 라운드 정보가 없습니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	$gameList = $resultList[0];
    	$endDt = strtotime($gameList->end_dt);
    	$nowDt = time();
    	    	
    	$sql_config = "SELECT * FROM mini_game_bet_config
    	WHERE bet_type= 6 AND level = ? ;"; // 가상축구 설정값
    	
    	$result_config = $MiniGameModel->db->query($sql_config,[$m_level])->getResult();
    	foreach ($result_config as $key => $value) {
    		$game_config = $value;
    	}
    	    	
    	$sql_config = "SELECT * FROM mini_game_member_bet
    	WHERE member_idx = ?
    	AND bet_type = 6;"; // 파워사다리 설정값
    	$member_bet_list = $MiniGameModel->db->query($sql_config,[$m_idx])->getResult();
    	$member_bet = [];
    	foreach ($member_bet_list as $key => $value) {
    		$member_bet[$key] = $value;
    	}
    	
    	$sql_config = "SELECT * FROM mini_game where bet_type = 6 and now() >= start_dt and now() <= end_dt order by id desc;"; // 파워볼 설정값
    	$game_data_list = $MiniGameModel->db->query($sql_config)->getResult();
    	$game_data = (Object) [];
    	$game_data->id = '';
    	foreach ($game_data_list as $key => $value) {
    		$game_data = $value;
    	}
    	
    	return view("$viewRoot/mini_premium_ship", [
    			'remain_time' => $endDt - $nowDt,
    			'id' => $gameList->id,
    			'game_config' => $game_config,
    			'member_bet' => $member_bet,
    			'game_data' => $game_data,
    	]);
    }

    // 가상축구 데이터 갱신
    public function bsoccerData() {
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $league = isset($_REQUEST['league']) ? $_REQUEST['league'] : 'Premiership';

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if (false == session()->has('member_idx')) {
            //$url = base_url("/$viewRoot/login");
            return redirect()->to(base_url("/$viewRoot/index"));
        }
        $this->initMemberData(session(), session()->get('member_idx'));
        $search_date = trim(isset($_REQUEST['search_date']) ? $_REQUEST['search_date'] : '');

        $MiniGameModel = new MiniGameModel();
        /* if($search_date == '')
          $sql = "SELECT * FROM mini_game where bet_type = 6 and start_dt > now() and league = '$league' order by start_dt asc limit 2;";
          else
          $sql = "SELECT * FROM mini_game where bet_type = 6 and start_dt > '$search_date' and league = '$league' order by start_dt asc limit 2;"; */


        $sql = "SELECT * FROM mini_game where bet_type = 6 and start_dt > now() and league = ? order by start_dt asc limit 2;";

        $gameList = $MiniGameModel->db->query($sql, [$league])->getResult();
        $serverDate = date("Y-m-d H:i:s");

        // 리그별 경기 시간을 가져온다.
        $sql = "SELECT start_dt, league FROM mini_game where bet_type = 6 and start_dt > now() group by league limit 4;";
        $leagueTime = $MiniGameModel->db->query($sql)->getResult();

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'gameList' => $gameList,
                'leagueTime' => $leagueTime,
                'serverDate' => $serverDate
            ]
        ];

        return $this->respond($response, 200);
    }

    // 미니게임 배팅내역
    public function selectMemberMiniGameBet() {

        if (false == session()->has('member_idx')) {
            $response['messages'] = '세션연결이 끊겼습니다.';
            return $this->fail($response);
        }

        $memberIdx = session()->get('member_idx');
        $betType = isset($_REQUEST['betType']) ? $_REQUEST['betType'] : 3;
        $curPageNo = isset($_POST['curPageNo']) ? $_POST['curPageNo'] : 1;
        $displayCnt = isset($_POST['displayCnt']) ? (int)$_POST['displayCnt'] : 10;
        $startCnt = isset($_POST['startCnt']) ? ($curPageNo - 1) * $_POST['startCnt'] : ($curPageNo - 1) * 10;
        $leagueType = isset($_REQUEST['leagueType']) ? $_REQUEST['leagueType'] : 0;
        
        if(!is_int((int)$memberIdx) || !is_int((int)$betType) || !is_int((int)$curPageNo) || !is_int((int)$displayCnt) || !is_int((int)$startCnt) || !is_int((int)$leagueType)){
            $this->logger->critical('selectMemberMiniGameBet die');
            die();
        }
        
        $league = "";
        if($leagueType == 1) {
        	$league = "Premiership";
        }else if ($leagueType == 2){
        	$league = "Superleague";
        }else if ($leagueType == 3){
        	$league = "World Cup";
        } else{
            $league = "Euro Cup";
            //$this->logger->critical('selectMemberMiniGameBet leagueType else ');
            //die();
        }
        
        $betFromDate = DateTimeUtil::getDayYmd('-7');

        //$this->logger->info('betType : '.$betType.' curPageNo : '.$curPageNo.' startCnt : '.$startCnt.' displayCnt'.$displayCnt);
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);
        if ($member == null) {
            $response['messages'] = '조회되는 유저가 없습니다.';
            return $this->fail($response);
        }

        $memberIdx = $member->getIdx();
        if($leagueType == "") {
        	
        	$sql = "select count(*) as count
        			from mini_game_member_bet left join mini_game on mini_game_member_bet.ls_fixture_id = mini_game.id 
        			WHERE member_idx = ? and mini_game_member_bet.bet_type = ? and create_dt >= ? ";
        	$arrCount = $memberModel->db->query($sql, [$memberIdx,$betType, $betFromDate])->getResultArray();
        	$sql = '';				
        	$sql = "select mini_game_member_bet.*, mini_game.result, mini_game.result_score
        			 from mini_game_member_bet left join mini_game on mini_game_member_bet.ls_fixture_id = mini_game.id 
        			WHERE member_idx = ? and mini_game_member_bet.bet_type = ? and create_dt >= ? order by idx desc LIMIT ?,? ";
        	$arrResult = $memberModel->db->query($sql,[$memberIdx,$betType, $betFromDate,$startCnt,$displayCnt])->getResultArray();
        									
        	// 미니게임 결과
        	if ($betType != 6) {
        			$sql = "SELECT id, start_dt, end_dt, result, result_score FROM mini_game where bet_type = ? and end_dt < now() order by id desc limit ? ";
        	}
        	else {
        			$sql = "SELECT id, start_dt, end_dt, result, result_score FROM mini_game where bet_type = ? and start_dt < now() order by start_dt desc limit ? ";
        	}
        	$arrGameResult = $memberModel->db->query($sql, [$betType,$displayCnt])->getResultArray();

        	
        }else {
        
	        $sql = "select count(*) as count"
	                . " from mini_game_member_bet left join mini_game on mini_game_member_bet.ls_fixture_id = mini_game.id "
	                . "WHERE member_idx = ? and mini_game_member_bet.bet_type = ? and mini_game.league = ? and create_dt >= ? ";
	        $arrCount = $memberModel->db->query($sql, [$memberIdx,$betType, $league, $betFromDate])->getResultArray();
	
	        $sql = "select mini_game_member_bet.*, mini_game.result, mini_game.result_score"
	                . " from mini_game_member_bet left join mini_game on mini_game_member_bet.ls_fixture_id = mini_game.id "
	                . "WHERE member_idx = ? and mini_game_member_bet.bet_type = ? and mini_game.league = ? and create_dt >= ? order by idx desc LIMIT ?,? ";
	        $arrResult = $memberModel->db->query($sql, [$memberIdx,$betType, $league, $betFromDate, $startCnt,$displayCnt])->getResultArray();
	
	        // 미니게임 결과
	        if ($betType != 6) {
	            $sql = "SELECT id, start_dt, end_dt, result, result_score FROM mini_game where bet_type = ? and league =? and end_dt < now() order by id desc limit ?";	        	
	        }
	        else {
	            $sql = "SELECT id, start_dt, end_dt, result, result_score FROM mini_game where bet_type = ? and league =? and start_dt < now() order by start_dt desc limit ?";
	        }
	        $arrGameResult = $memberModel->db->query($sql, [$betType, $league,$displayCnt])->getResultArray();
	            
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'bet_list' => $arrResult,
                'bet_count' => $arrCount[0]['count'],
                'game_result' => $arrGameResult,
                'curPageNo' => $curPageNo
            ]
        ];

        return $this->respond($response, 200);
    }

    // 출목표 데이터
    public function selectMiniGamePattenData() {
        $betType = isset($_REQUEST['betType']) ? $_REQUEST['betType'] : 3;

        if(!is_int((int)$betType) ){
            $this->logger->critical('selectMiniGamePattenData die');
            die();
        }
        
        $memberModel = new MemberModel();
        $sql = "SELECT cnt, result, result_score FROM mini_game where bet_type = ? order by start_dt desc limit 65;";
        $arrGameResult = $memberModel->db->query($sql, [$betType])->getResultArray();

        $arrGameResult = array_reverse($arrGameResult);
        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'game_result' => $arrGameResult
            ]
        ];

        return $this->respond($response, 200);
    }

    // 가상축구 영상
    public function bsoccerMovie() {
        $vn = isset($_REQUEST['vn']) ? $_REQUEST['vn'] : 1;
   
        return view("web/mini-bsoccer-movie.php", [
            'vn' => $vn
        ]);
    }
    
    // 프리미어쉽
    public function premiumShip() {
    	$id = session()->get('id');
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    	
    	if (false == session()->has('member_idx') || false == session()->has('level')) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if (0 < session()->get('tm_unread_cnt')) {
    		$url = base_url("/web/note");
    		echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
        
        $m_idx = session()->get('member_idx');
        $m_level = session()->get('level');
        
        if(!is_int((int)$m_idx) || !is_int((int)$m_level) ){
            $this->logger->critical('premiumShip die');
            die();
        }
        
    	
    	$MiniGameModel = new MiniGameModel();
    	
    	$sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'mini_service_v_soccer' ";
        $result_game_config = $MiniGameModel->db->query($sql)->getResult();
    	if ('N' == $result_game_config[0]->set_type_val && 9 != session()->get('level')) {
    		$url = base_url("/");
    		echo "<script>
    		alert('점검 중으로 관리자에게 문의바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	
    	$sql = "SELECT id, end_dt FROM mini_game where now() >= start_dt and now() <= end_dt order by id desc;";
    	$resultList = $MiniGameModel->db->query($sql)->getResult();
    	if (0 == count($resultList)) {
    		$url = base_url("/$viewRoot/index");
    		echo "<script>
    		alert('가상축구 라운드 정보가 없습니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	$gameList = $resultList[0];
    	$endDt = strtotime($gameList->end_dt);
    	$nowDt = time();
    	    	
    	$sql_config = "SELECT * FROM mini_game_bet_config
    	WHERE bet_type= 6 AND level = ? ;"; // 가상축구 설정값
    	
    	$result_config = $MiniGameModel->db->query($sql_config,[$m_level])->getResult();
    	foreach ($result_config as $key => $value) {
    		$game_config = $value;
    	}
    	    	
    	$sql_config = "SELECT * FROM mini_game_member_bet
    	WHERE member_idx = ?
    	AND bet_type = 6;"; // 파워사다리 설정값
    	$member_bet_list = $MiniGameModel->db->query($sql_config,[$m_idx])->getResult();
    	$member_bet = [];
    	foreach ($member_bet_list as $key => $value) {
    		$member_bet[$key] = $value;
    	}
    	
    	$sql_config = "SELECT * FROM mini_game where bet_type = 6 and now() >= start_dt and now() <= end_dt order by id desc;"; // 파워볼 설정값
    	$game_data_list = $MiniGameModel->db->query($sql_config)->getResult();
    	$game_data = (Object) [];
    	$game_data->id = '';
    	foreach ($game_data_list as $key => $value) {
    		$game_data = $value;
    	}
    	
    	return view("$viewRoot/mini_premium_ship", [
    			'remain_time' => $endDt - $nowDt,
    			'id' => $gameList->id,
    			'game_config' => $game_config,
    			'member_bet' => $member_bet,
    			'game_data' => $game_data,
    	]);
    }
    
    // 수퍼리그
    public function superLeague() {
    	$id = session()->get('id');
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    	
    	if (false == session()->has('member_idx') || false == session()->has('level')) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if (0 < $id = session()->get('tm_unread_cnt')) {
    		$url = base_url("/web/note");
    		echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
        
        $m_idx = session()->get('member_idx');
        $m_level = session()->get('level');
        
        if(!is_int((int)$m_idx) || !is_int((int)$m_level) ){
            $this->logger->critical('superLeague die');
            die();
        }
    	
    	$MiniGameModel = new MiniGameModel();
    	
    	$sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'mini_service_v_soccer' ";
    	$result_game_config = $MiniGameModel->db->query($sql)->getResult();
    	if ('N' == $result_game_config[0]->set_type_val && 9 != session()->get('level')) {
    		$url = base_url("/");
    		echo "<script>
    		alert('점검 중으로 관리자에게 문의바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	
    	$sql = "SELECT id, end_dt FROM mini_game where now() >= start_dt and now() <= end_dt order by id desc;";
    	$resultList = $MiniGameModel->db->query($sql)->getResult();
    	if (0 == count($resultList)) {
    		//return redirect()->to(base_url("/$viewRoot/index"));
    		$url = base_url("/$viewRoot/index");
    		echo "<script>
    		alert('가상축구 라운드 정보가 없습니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	$gameList = $resultList[0];
    	$endDt = strtotime($gameList->end_dt);
    	$nowDt = time();
    	
    	
    	$sql_config = "SELECT * FROM mini_game_bet_config
    	WHERE bet_type= 6 AND level = ? ;"; // 가상축구 설정값
    	
    	$result_config = $MiniGameModel->db->query($sql_config,[$m_level])->getResult();
    	foreach ($result_config as $key => $value) {
    		$game_config = $value;
    	}
    	    
    	$sql_config = "SELECT * FROM mini_game_member_bet
    	WHERE member_idx = ?
    	AND bet_type = 6;"; // 파워사다리 설정값
    	$member_bet_list = $MiniGameModel->db->query($sql_config,[$m_idx])->getResult();
    	$member_bet = [];
    	foreach ($member_bet_list as $key => $value) {
    		$member_bet[$key] = $value;
    	}
    	
    	$sql_config = "SELECT * FROM mini_game where bet_type = 6 and now() >= start_dt and now() <= end_dt order by id desc;"; // 파워볼 설정값
    	$game_data_list = $MiniGameModel->db->query($sql_config)->getResult();
    	$game_data = (Object) [];
    	$game_data->id = '';
    	foreach ($game_data_list as $key => $value) {
    		$game_data = $value;
    	}
    	
    	return view("$viewRoot/mini_super_league", [
    			'remain_time' => $endDt - $nowDt,
    			'id' => $gameList->id,
    			'game_config' => $game_config,
    			'member_bet' => $member_bet,
    			'game_data' => $game_data,
    	]);
    }
    
    // 월드컵
    public function worldCup() {
    	$id = session()->get('id');
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    	
    	if (false == session()->has('member_idx') || false == session()->has('level')) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if (0 < $id = session()->get('tm_unread_cnt')) {
    		$url = base_url("/web/note");
    		echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	
        $m_idx = session()->get('member_idx');
        $m_level = session()->get('level');
        
        if(!is_int((int)$m_idx) || !is_int((int)$m_level) ){
            $this->logger->critical('worldCup die');
            die();
        }
        
        
    	$MiniGameModel = new MiniGameModel();
    	
    	$sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'mini_service_v_soccer' ";
    	$result_game_config = $MiniGameModel->db->query($sql)->getResult();
    	if ('N' == $result_game_config[0]->set_type_val  && 9 != session()->get('level')) {
    		$url = base_url("/");
    		echo "<script>
    		alert('점검 중으로 관리자에게 문의바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	
    	$sql = "SELECT id, end_dt FROM mini_game where now() >= start_dt and now() <= end_dt order by id desc;";
    	$resultList = $MiniGameModel->db->query($sql)->getResult();
    	if (0 == count($resultList)) {
    		$url = base_url("/$viewRoot/index");
    		echo "<script>
    		alert('가상축구 라운드 정보가 없습니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
        
    	$gameList = $resultList[0];
    	$endDt = strtotime($gameList->end_dt);
    	$nowDt = time();
    	    	
    	$sql_config = "SELECT * FROM mini_game_bet_config
    	WHERE bet_type= 6 AND level = ? ;"; // 가상축구 설정값
    	
    	$result_config = $MiniGameModel->db->query($sql_config,[$m_level])->getResult();
    	foreach ($result_config as $key => $value) {
    		$game_config = $value;
    	}
    	    	
    	$sql_config = "SELECT * FROM mini_game_member_bet
    	WHERE member_idx = ?
    	AND bet_type = 6;"; // 파워사다리 설정값
    	$member_bet_list = $MiniGameModel->db->query($sql_config,[$m_idx])->getResult();
    	$member_bet = [];
    	foreach ($member_bet_list as $key => $value) {
    		$member_bet[$key] = $value;
    	}
    	
    	$sql_config = "SELECT * FROM mini_game where bet_type = 6 and now() >= start_dt and now() <= end_dt order by id desc;"; // 파워볼 설정값
    	$game_data_list = $MiniGameModel->db->query($sql_config)->getResult();
    	$game_data = (Object) [];
    	$game_data->id = '';
    	foreach ($game_data_list as $key => $value) {
    		$game_data = $value;
    	}
    	
    	return view("$viewRoot/mini_world_cup", [
    			'remain_time' => $endDt - $nowDt,
    			'id' => $gameList->id,
    			'game_config' => $game_config,
    			'member_bet' => $member_bet,
    			'game_data' => $game_data,
    	]);
    }
    
    // 유로컵
    // 프리미어쉽
    public function euroCup() {
    	$id = session()->get('id');
    	$chkMobile = CodeUtil::rtn_mobile_chk();
    	$viewRoot = "PC" == $chkMobile ? 'web' : 'web';
    	
    	if (false == session()->has('member_idx') || false == session()->has('level')) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
    	}
    	
    	if (0 < session()->get('tm_unread_cnt')) {
    		$url = base_url("/web/note");
    		echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
        
        $m_idx = session()->get('member_idx');
        $m_level = session()->get('level');
        
        if(!is_int((int)$m_idx) || !is_int((int)$m_level) ){
            $this->logger->critical('premiumShip die');
            die();
        }
        
    	
    	$MiniGameModel = new MiniGameModel();
    	
    	$sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'mini_service_v_soccer' ";
        $result_game_config = $MiniGameModel->db->query($sql)->getResult();
    	if ('N' == $result_game_config[0]->set_type_val && 9 != session()->get('level')) {
    		$url = base_url("/");
    		echo "<script>
    		alert('점검 중으로 관리자에게 문의바랍니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	
    	$sql = "SELECT id, end_dt FROM mini_game where now() >= start_dt and now() <= end_dt order by id desc;";
    	$resultList = $MiniGameModel->db->query($sql)->getResult();
    	if (0 == count($resultList)) {
    		$url = base_url("/$viewRoot/index");
    		echo "<script>
    		alert('가상축구 라운드 정보가 없습니다.');
    		window.location.href='$url';
    		</script>";
    		return;
    	}
    	$gameList = $resultList[0];
    	$endDt = strtotime($gameList->end_dt);
    	$nowDt = time();
    	    	
    	$sql_config = "SELECT * FROM mini_game_bet_config
    	WHERE bet_type= 6 AND level = ? ;"; // 가상축구 설정값
    	
    	$result_config = $MiniGameModel->db->query($sql_config,[$m_level])->getResult();
    	foreach ($result_config as $key => $value) {
    		$game_config = $value;
    	}
    	    	
    	$sql_config = "SELECT * FROM mini_game_member_bet
    	WHERE member_idx = ?
    	AND bet_type = 6;"; // 파워사다리 설정값
    	$member_bet_list = $MiniGameModel->db->query($sql_config,[$m_idx])->getResult();
    	$member_bet = [];
    	foreach ($member_bet_list as $key => $value) {
    		$member_bet[$key] = $value;
    	}
    	
    	$sql_config = "SELECT * FROM mini_game where bet_type = 6 and now() >= start_dt and now() <= end_dt order by id desc;"; // 파워볼 설정값
    	$game_data_list = $MiniGameModel->db->query($sql_config)->getResult();
    	$game_data = (Object) [];
    	$game_data->id = '';
    	foreach ($game_data_list as $key => $value) {
    		$game_data = $value;
    	}
    	
    	return view("$viewRoot/mini_euro_cup", [
    			'remain_time' => $endDt - $nowDt,
    			'id' => $gameList->id,
    			'game_config' => $game_config,
    			'member_bet' => $member_bet,
    			'game_data' => $game_data,
    	]);
    }
    
    public function getLnbTimer() {
    	
    	$MiniGameModel = new MiniGameModel();
    	
    	$sql = "SELECT id, game,end_dt FROM mini_game where bet_type in (3,4,5) and now() >= start_dt and now() <= end_dt order by id desc;";
    	$resultList = $MiniGameModel->db->query($sql)->getResult();
    	
    	if (0 == count($resultList)) {
    		$response['messages'] = '미니게임 라운드 정보가 없습니다 game : ' . $game;
    		return $this->fail($response);
    	}
    	
    	for($i=0; $i<count($resultList); $i++) {
    		
    		if($resultList[$i]->game == 'powerball') {
    			$powerballEndDt = strtotime($resultList[$i]->end_dt);
    			$powerballNowDt = strtotime(date('Y-m-d H:i:s'));
    		}
    		if($resultList[$i]->game == 'pladder') {
    			$pladderEndDt = strtotime($resultList[$i]->end_dt);
    			$pladderNowDt = strtotime(date('Y-m-d H:i:s'));
    		}
    		if($resultList[$i]->game == 'kladder') {
    			$kladderEndDt = strtotime($resultList[$i]->end_dt);
    			$kladderNowDt = strtotime(date('Y-m-d H:i:s'));
    		}
    	}
    	
    	// 리그별 경기 시간을 가져온다.
    	$sql = "SELECT start_dt, league FROM mini_game where bet_type = 6 and start_dt > now() group by league limit 4;";
    	$leagueTime = $MiniGameModel->db->query($sql)->getResult();
    	$serverDate = date("Y-m-d H:i:s");
    	
    	$response = [
    			'result_code' => 200,
    			'messages' => '조회 성공',
    			'data' => [
    					'powerball_remain_time' => $powerballEndDt - $powerballNowDt,
    					'pladder_remain_time' => $pladderEndDt- $pladderNowDt,
    					'kladder_remain_time' => $kladderEndDt- $kladderNowDt,
    					'leagueTime' => $leagueTime,
    					'serverDate' => $serverDate
    			]
    	];
    	return $this->respond($response, 200);
    }
}
