<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberBetDetailModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'member_bet_detail';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'bet_idx', // 배팅 아이디 -MemberBetModel
        'ls_bet_id', // LS 배팅 아이디 -LSportsBetModel
        'ls_fixture_id',
        'ls_markets_id',
        'ls_markets_name',
        'ls_markets_base_line',
        'bet_price', // 배팅 당시 당첨 확률
        //'bet_price_win', // 배팅 당시 당첨 확률
        //'bet_price_draw', // 배팅 당시 당첨 확률
        //'bet_price_lose', // 배팅 당시 당첨 확률
        'bet_status', // 배팅 상태   -1: 게임 결과 전 2: 적중 - 정산 전  3: 적중 - 정산 완료 4: 적중 실패
        'create_dt', // 배팅 시간
        //'fixture_start_date', // 경기 시간
        'fixture_sport_id', // 스포츠번호
        'fixture_location_id', // 지역번호
        'fixture_league_id', // 리그번호
        'fixture_participants_1_id', // 홈팀
        'fixture_participants_2_id', // 원정팀
        'bet_type', // 경기 종류 
        'bet_name', // 배팅한 배팅명
        'other_ls_bet_id', // 배팅한 반대 뱃아이디 
        'other_bet_price', // 배팅한 반대 배당
        'other_bet_name', // 배팅한 반대 배팅 이름
        'fixture_start_date'
        
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function UpdateMemberBetDetail($idx, $bet_status, $result_score) {
        $sql = "UPDATE member_bet_detail SET bet_status = ?,result_score = ? WHERE idx = ?";
        return $this->db->query($sql,[$bet_status,$result_score,$idx]);
    }
    
    public function UpdateOtherMemberBetDetail($value, $bet_status, $result_score) {
        $sql = "UPDATE member_bet_detail SET bet_status = ?,result_score = ?
                ,ls_bet_id = ?, bet_price = ?, bet_name = ?
                ,other_ls_bet_id = ?,other_ls_bet_id = ?,other_ls_bet_id = ? WHERE idx = ?";
        return $this->db->query($sql,[$bet_status,$result_score
                ,$value['other_ls_bet_id'],$value['other_bet_price'],$value['other_bet_name']
                ,$value['ls_bet_id'],$value['bet_price'],$value['bet_name']
                ,$value['idx']]);
    }
    
    public function CheckRemainMemberBetDetail($test_expt_member_idxs){
        $sql = "select count(*) as cnt from member_bet
                 left join member_bet_detail on member_bet.idx = member_bet_detail.bet_idx
                 where  member_bet.member_idx in ($test_expt_member_idxs) and member_bet_detail.bet_status = 1 and member_bet_detail.bet_type = 1";
        $result =  $this->db->query($sql)->getResultArray();
        
        return !isset($result) ? 0 : $result[0]['cnt'];
    }
    
    public function UpdateScoreMemberBetDetail($idx, $bet_status, $result_score) {
        $sql = "UPDATE member_bet_detail SET result_score = ? WHERE idx = ?";
        return $this->db->query($sql,[$result_score,$idx]);
    }

    
    public function SelectMemberBetDetail($bet_idx) {
        $sql = "SELECT mb_bet.bet_status as mb_bet_status,member_bet_detail.bet_status,bet.admin_bet_status "
                . ",member_bet_detail.bet_price as detail_bet_price , member_bet_detail.ls_fixture_id "
                . " FROM member_bet_detail "
                . " LEFT JOIN lsports_bet as bet on  member_bet_detail.ls_bet_id = bet.bet_id AND bet.bet_type = member_bet_detail.bet_type "
                . " LEFT JOIN member_bet as mb_bet on mb_bet.idx = member_bet_detail.bet_idx "
                . "where member_bet_detail.bet_idx = ? AND mb_bet.bet_status = 1 ";

        return $this->db->query($sql,[$bet_idx])->getResultArray();
    }

    public function SelectMemberBetResultProcessing($bet_type) {

        $sql = "SELECT 
              bet_detail.ls_fixture_id,
              bet_detail.bet_type,
              bet_detail.idx,
              bet_detail.bet_idx,
              bet_detail.ls_bet_id,
              bet_detail.bet_status,
              bet_detail.bet_price,
              IF ( fix.`m_live_results_p1` is not null,fix.`m_live_results_p1`,fix.`live_results_p1`) as live_results_p1,
              IF ( fix.`m_live_results_p2` is not null,fix.`m_live_results_p2`,fix.`live_results_p2`) as live_results_p2,
              fix.livescore,
              fix.fixture_sport_id,
              fix.fixture_status,
              bet.bet_id,
              bet.bet_name,
              bet.markets_id as ls_markets_id,
              bet.bet_line,
              bet.bet_base_line as ls_markets_base_line,
              bet.bet_settlement,
              mb_bet.member_idx,
              bet_detail.other_ls_bet_id,
              bet_detail.other_bet_price,
              bet_detail.other_bet_name
           FROM member_bet_detail as bet_detail
            -- LEFT JOIN lsports_bet as bet ON bet_detail.ls_bet_id = bet.bet_id
            LEFT JOIN lsports_bet as bet ON bet_detail.ls_fixture_id = bet.fixture_id 
                 AND bet_detail.ls_markets_id = bet.markets_id 
                 AND bet_detail.ls_markets_base_line = bet.bet_base_line 
                 AND bet_detail.bet_name = bet.bet_name
                 AND bet_detail.bet_type = bet.bet_type
            LEFT JOIN member_bet as mb_bet ON mb_bet.idx = bet_detail.bet_idx
            LEFT JOIN lsports_fixtures as fix ON bet_detail.ls_fixture_id = fix.fixture_id 
         
          WHERE 
            bet_detail.bet_type = ? AND bet_detail.bet_status = 1
            AND bet.bet_type = ?
            AND bet.admin_bet_status = 'ON' ";

        return $this->db->query($sql,[$bet_type,$bet_type])->getResultArray();
    }

    
    public function SelectMemberBetResultScoreProcessing() {
        $sql = "SELECT 
              bet_detail.idx,
              bet_detail.bet_status,
              bet_detail.ls_markets_id,
              bet_detail.result_score,
              fix.livescore,
              IF ( fix.`m_live_results_p1` is not null,fix.`m_live_results_p1`,fix.`live_results_p1`) as live_results_p1,
              IF ( fix.`m_live_results_p2` is not null,fix.`m_live_results_p2`,fix.`live_results_p2`) as live_results_p2
           FROM member_bet_detail as bet_detail
             LEFT JOIN lsports_fixtures as fix ON bet_detail.ls_fixture_id = fix.fixture_id AND bet_detail.bet_type = fix.bet_type
          WHERE 
            bet_detail.create_dt > DATE_SUB(NOW(), interval 3 DAY)
            AND bet_detail.create_dt <= NOW()
            AND bet_detail.bet_status IN (2,4) 
            AND (bet_detail.result_score is null OR bet_detail.result_score = '') ";

        return $this->db->query($sql)->getResultArray();
    }
    
    
    public function SelectMemberBetTotalCalculate($bet_type) {

        $sql = "SELECT 
              mb_bet.*,
              mb.money
           FROM member_bet as mb_bet
             LEFT JOIN member_bet_detail as bet_detail ON mb_bet.idx = bet_detail.bet_idx
             LEFT JOIN member as mb ON mb.idx = mb_bt.member_idx
          WHERE 
            mb_bet.bet_status = 1 AND mb_bet.bet_type = ? AND bet_detail.bet_status <> 1  ";



        return $this->db->query($sql,[$bet_type])->getResultArray();
    }

    public function SelectMiniMemberBetDetail($bet_idx) {
        $sql = "SELECT bet_status,bet_price  FROM member_bet_detail where member_bet_detail.bet_idx = ? ";

        return $this->db->query($sql,[$bet_idx])->getResultArray();
    }

    public function UpdateMemberBetDetailByMb_bt_idx($idx, $bet_status) {
        $sql = "UPDATE member_bet_detail SET bet_status = ? WHERE bet_idx = ?";
        return $this->db->query($sql,[$bet_status,$idx]);
    }
    
   

}
