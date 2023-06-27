<?php

namespace App\Models;

use App\Entities\Member;
use App\Entities\MemberBet;
use CodeIgniter\Model;

class MemberBetModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'member_bet';
    protected $primaryKey = 'idx';
//    protected $returnType = 'array';
    protected $returnType = 'App\Entities\MemberBet';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx', // 유저 아이디 -MemberModel
        'u_key', // 로그 연동 키 -TLogCashModel
        //'ls_fixture_id',     // 게임 아이디  -LSportsBetModel
        'ls_markets_id', // 마켓 아이디  -LSportsBetModel
        'bet_type', // 배팅 타입   -1 : 스포츠, 2 : 실시간, 3: 미니게임
        'bet_status', // 배팅 상태   -1: 게임 결과 전 2: 적중 - 정산 전  3: 적중 - 정산 완료 4: 적중 실패
        'game_type', // 게임 타입   -3 : 파워볼, 4 : 파워사다리, 5: 키노사다리, 6: 가상축구 
        'total_bet_price', // 배팅 당시 총 당첨 확률
        'total_bet_money', // 총 배팅 금액
        'create_dt', // 배팅 시간
        'folder_type', // 폴더 타입   -싱글 / 다폴더 ('S', 'D')
        'round', // 회차
        'bonus_price',       // 보너스 배당률
        'is_betting_slip',       // 자동 배당변경
        'item_idx',
        'bet_count',
        'is_classic'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function addMemberBet($today,Member $member, $totalOdds, $totalMoney, $betType, $folderType, $bonus_dividend, array $betList, array $arr_bet_price, $fDetail,$is_betting_slip,$itemIdx,$isClassic): int {
        $uKey = md5($member->getId() . strtotime('Now'));
        // 미니게임이다.
        $gameType = 0;
        if ($betType >= 3) {
            $gameType = $betType;
        }

        $memberBetAdd = [
            'member_idx' => $member->getIdx(),
            'u_key' => $uKey,
            'bet_type' => $betType,
            'game_type' => $gameType,
            'total_bet_money' => $totalMoney,
            'folder_type' => $folderType,
            'round' => $betList[0]['round'],
            'bonus_price' => $bonus_dividend,
            'create_dt' => $today,
            'is_betting_slip' => $is_betting_slip,
            'item_idx' => $itemIdx,
            'bet_count' => count($betList),
            'is_classic' =>$isClassic
        ];
        try {
            $this->db->transStart();

            $fiModel = new LSportsFixturesModel();

            # 배팅 정보 등록
            $insertBetIdx = $this->insert($memberBetAdd);
            $memberBetDetailModel = new MemberBetDetailModel();
            foreach ($betList as $data) {
                // 미니게임 베팅은 경기시간 빈값으로 넣어준다.
                $fixture_start_date = '';
                $bet_price = $data['betPrice'];
                if ($betType < 3) {
                    $fixture_start_date = $data['fixture_start_date'];
                    $bet_price = $arr_bet_price[$data['betId']];
                }

                $betDetailAdd = [
                    'bet_idx' => $insertBetIdx,
                    'ls_bet_id' => $data['betId'],
                    'ls_fixture_id' => $data['fixtureId'],
                    'ls_markets_id' => $data['marketsId'],
                    'ls_markets_name' => $data['marketsName'],
                    'ls_markets_base_line' => $data['betBaseLine'],
                    'bet_price' => $bet_price,
                    'fixture_sport_id' => $data['fixture_sport_id'],
                    'fixture_location_id' => $data['fixture_location_id'],
                    'fixture_league_id' => $data['fixture_league_id'],
                    'fixture_participants_1_id' => $data['fixture_participants_1_id'],
                    'fixture_participants_2_id' => $data['fixture_participants_2_id'],
                    'bet_type' => $betType,
                    'bet_name' => $data['bet_name'],
                    'other_ls_bet_id' => $data['other_ls_bet_id'],
                    'other_bet_price' => $data['other_bet_price'],
                    'other_bet_name' => $data['other_bet_name'],
                    'fixture_start_date' => $data['fixture_start_date']
                ];
                $memberBetDetailModel->insert($betDetailAdd);
            }


            # 로그 추가
            $tLogCashModel = new TLogCashModel();
            $tLogCashModel->insertCashLog($uKey, 3, $insertBetIdx, $totalMoney * -1, $member->getMoney(),'M',$fDetail);

            # 회원 정보 수정
            $memberModel = new MemberModel();
            $memberModel->memberChangeMoney($member->getIdx(), (int) $member->getMoney() - (int) $totalMoney);
            
            # 최종 배팅 시간
            $memberModel->updateBettingDate($member->getIdx());
            
         
            $this->db->transComplete();

            return $insertBetIdx;
        } catch (\mysqli_sql_exception $e) {
            $this->db->transRollback();
            return 0;
        }
    }

    public function getMemberBetList(string $memberIdx): array {
        return $this->asArray()->where('member_idx', $memberIdx)->find();
    }

    public function UpdateMemberBet($idx, $bet_status, $take_money,$take_point = 0,$take_recom_point = 0,$flag_bet_sum = 'P') {
        
        $sql = "UPDATE member_bet SET bet_status = ?,take_money = ?,take_point = ?,recom_take_point = ?,flag_bet_sum = ?, calculate_dt = now() WHERE idx = ?";
    
        return $this->db->query($sql,[$bet_status,$take_money,$take_point,$take_recom_point,$flag_bet_sum,$idx]);
    }
    
    public function UpdateMemberBetBonus($idx, $bet_status, $take_money,$bonus_price,$item_idx = 0,$take_point = 0,$take_recom_point = 0,$item_bonus_price) {
        
        $sql = "UPDATE member_bet SET bet_status = ?,take_money = ?,
                bonus_price = ?,
                take_point = ?,recom_take_point = ?, calculate_dt = now(),item_idx = ?,item_bonus_price = ? WHERE idx = ?";
    
        return $this->db->query($sql,[$bet_status,$take_money,$bonus_price,$take_point,$take_recom_point,$item_idx,$item_bonus_price,$idx]);
    }
    
    

    public function SelectMemberBet($idx) {
        $sql = "SELECT bet_detail.bet_type,bet_detail.ls_fixture_id,mb_bet.total_bet_money,member.money,mb_bet.bet_status,mb_bet.member_idx,
                mb_bet.create_dt,mb_bet.folder_type,
                IF('ON' = fix.passivity_flag AND fix.fixture_start_date_passivity is NOT NULL ,fix.fixture_start_date_passivity,fix.fixture_start_date) as fixture_start_date,
                mb_bet.item_idx
                FROM member_bet_detail as bet_detail
                LEFT JOIN member_bet as mb_bet ON bet_detail.bet_idx = mb_bet.idx
                LEFT JOIN member  ON member.idx = mb_bet.member_idx
                LEFT JOIN lsports_fixtures as fix on bet_detail.ls_fixture_id = fix.fixture_id AND bet_detail.bet_type = fix.bet_type
                WHERE bet_detail.bet_idx = ? ";
    
        return $this->db->query($sql,[$idx])->getResultArray();
    }

}
