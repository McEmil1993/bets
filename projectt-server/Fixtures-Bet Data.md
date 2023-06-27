# Fixtures - Bet Data
LSports_Fixtures, LSports_Bet table 과의 연관 관계 정리 문서 

## 1. 용어 정의
- Fixture: 경기 정보, lsports_fixtures table 에서 목록 조회 가능
- Bet: 각 경기마다 등록되어 있는 배팅 정보, lsports_bet 에서 조회 가능 
- market : 배팅 종류 (승무패, 언더오버, 핸디캡 등등), lsports_market table 에서 목록 조회 가능
- sports : 스포츠 종류 (축구, 농구 야구.. 등등), lsprots_sports table 에서 목록 조회 가능 

## 2. 사용 markets 정의 
- 아래 정의되지 않은 markets 는 사용하지 않는다. 
- https://docs.google.com/spreadsheets/d/1rJ06QvostS0WJBH_nEb-zMHGt5AUkYcCqXF0YxkW3Mg/edit#gid=551742047

## 3. 데이터 정리 방법
### 3.1 bet 정보 가져오기 
- lsports_bet, lsports_fixtures JOIN 하여 조건 넣어 조회
- 현재는 bet.markets_id 
### 3.2  
 
 아직 작성중 ... 
```php
// * SportsController.php index() 참고하였습니다.
$lSportsBetModel = new LSportsBetModel();
    // 가져 오려는 스포츠 목록만 조회 
    // $sportsIndexList = [6046, 48242, 154914, 154919];
    foreach ($sportsIndexList as $sport){
        $sql = "SELECT   bet.*,  
                         fix.fixture_sport_id, fixture_sport_name,
                         fix.fixture_location_id, fix.fixture_location_name,
                         fix.fixture_league_id, fix.fixture_league_name,
                         fix.fixture_start_date, 
                         fix.fixture_participants_1_name, fix.fixture_participants_2_name
                FROM            lsports_bet as bet
                LEFT JOIN       lsports_fixtures as fix
                ON       bet.fixture_id = fix.fixture_id
                WHERE    bet.bet_status = 1 
                AND      fix.fixture_sport_id = ? 
                AND      bet.markets_id IN ? 
                AND NOT  fix.fixture_location_id = 248
                AND      fix.fixture_start_date > ?
                AND      fix.fixture_start_date < ?
                ORDER BY fix.fixture_start_date asc
                ";

        $gameList = $lSportsBetModel->db->query(
            $sql,
            [
                $sport,
                [1,41,42,43,44,49,50,161,207,208,282,284,409,410,415,419,427],//승무패
                date("Y-$day 00:00:00", strtotime("Now")),
                date("Y-$day 23:59:59", strtotime("Now"))
            ])->getResult();

        $gameList = BetDataUtil::mergeBetWinDrawLoseData2($gameList);
        $sportsGameList += ['game_'.$sport => $gameList] ;
        $sportsGameAllList += $gameList;
```
- SportsController.php, RealTimeController.php
- BetDataUtil.php