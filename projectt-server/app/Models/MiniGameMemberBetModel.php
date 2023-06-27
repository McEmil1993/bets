<?php

namespace App\Models;
use App\Entities\Member;
use CodeIgniter\Model;

class MiniGameMemberBetModel extends Model {
    protected $DBGroup = 'default';
    protected $table = 'mini_game_member_bet';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx', // 유저 아이디 -MemberModel
        'u_key', // 로그 연동 키 -TLogCashModel
        'ls_markets_id', // 마켓 아이디  -LSportsBetModel
        'bet_type', // 배팅 타입   -1 : 스포츠, 2 : 실시간, 3: 미니게임
        'bet_status', // 배팅 상태   -1: 게임 결과 전 2: 적중 - 정산 전  3: 적중 - 정산 완료 4: 적중 실패
        'total_bet_money', // 총 배팅 금액
        'bet_price', // 배팅률
        'create_dt', // 배팅 시간
        'update_dt', // 업데이트 시간
        'calculate_dt', // 정산 시간
        'ls_fixture_id',
        'ls_markets_id',
        'ls_markets_name',
        'round',        // 회차번호
        'take_money'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    public function addMemberMiniGameBet(Member $member,$today, $totalMoney, $takePoint, $betType, array $betList): int {
        $uKey = md5($member->getId() . strtotime('Now'));

        $data = $betList[0];
        $memberMiniGameBetAdd = [
            'u_key' => $uKey,
            'member_idx' => $member->getIdx(),
            'bet_type' => $betType,
            'bet_status' => 1,
            'ls_fixture_id' => $data['fixtureId'],
            'ls_markets_id' => $data['marketsId'],
            'ls_markets_name' => trim($data['marketsName']),
            'bet_price' => $data['betPrice'],
            'total_bet_money' => $totalMoney,
            'take_point' => $takePoint,
            'round' => $data['round'],
            'create_dt' => $today
        ];
        
        $gameName = 'minigame[파워볼] '.$data['fixtureId'].' '.$data['marketsName'];
        if($betType == 4)
            $gameName = 'minigame[파워사다리] '.$data['fixtureId'].' '.$data['marketsName'];
        else if($betType == 5)
            $gameName = 'minigame[키노사다리] '.$data['fixtureId'].' '.$data['marketsName'];
        else if($betType == 6)
            $gameName = 'minigame[가상축구]';
        
        try {
            $this->db->transStart();

            # 배팅 정보 등록
            $insertBetIdx = $this->insert($memberMiniGameBetAdd);
            
            # 로그 추가
            $tLogCashModel = new TLogCashModel();
            $tLogCashModel->insertCashLog($uKey, 3, $insertBetIdx, $totalMoney * -1, $member->getMoney(),'M', $gameName);

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
    
    public function UpdateMemberMiniGameBet($idx, $bet_status, $take_money,$take_point = 0) {
        $sql = "UPDATE mini_game_member_bet SET bet_status = ?,take_money = ?,take_point = ?, calculate_dt = now() WHERE idx = ?";
    
        return $this->db->query($sql,[$bet_status,$take_money,$take_point,$idx]);
    }
}
