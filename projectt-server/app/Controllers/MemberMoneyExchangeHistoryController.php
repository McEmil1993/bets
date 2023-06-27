<?php namespace App\Controllers;

use App\Entities\Member;
use App\Models\GameModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyChargeHistoryModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\TotalMemberCashModel;
use App\Util\UserPayBack;

class MemberMoneyExchangeHistoryController extends BaseController
{
    use ResponseTrait;

    public function moneyExchange()
    {
        $memberIdx = session()->get('member_idx');
        $exchangeMoney = isset($_POST['money']) ? $_POST['money'] : NULL;

        $memberModel = new MemberModel();
        
        $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type in (?,?,?,?)";
        
        $result_game_config = $memberModel->db->query($sql, ['service_exchange', 'money_exchange_delay', 'bank_check_start', 'bank_check_end'])->getResultArray();
        
        $arr_config = array();
        foreach ($result_game_config as $key => $value) {
            $arr_config[$value['set_type']] = $value['set_type_val'];
        }
        
        if('Y' == $arr_config['service_exchange']){
           return $this->fail('환전 시스템 점검으로 환전 이용이 불가합니다.');
        }
        
        $findMember = $memberModel->getMemberWhereIdx($memberIdx);

        if ($findMember == null) {
            return $this->fail('조회되는 유저가 없습니다.');
        }

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
           //$response['messages'] = '사용이 불가능 한 계정으로 관리자에게 문의바랍니다.';
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($findMember->getStatus() == 11) {
            //$response['messages'] = '관리자 승인이 필요합니다.';
            return $this->fail('관리자 승인이 필요합니다.');
        }

        if(1 < $findMember->getUBusiness() && 0 != $findMember->getPoint()){
            return $this->fail('남아있는 포인트 전환후 이용해주세요.');
        }
        
        $afterMoney = $findMember->getMoney() - $exchangeMoney;
        if ($afterMoney < 0) {
            return $this->fail('보유금액보다 신청하신 금액이 많습니다.');
        }
        
        // 환전 신청시간 후 3시간동안 재신청 못함
        $str_sql_exchange = "SELECT create_dt FROM member_money_exchange_history where member_idx = ? and status in (1,3)  order by idx desc limit 1;";
        $result = $memberModel->db->query($str_sql_exchange, [$memberIdx])->getResultArray();
       
        $currentDate = date("Y-m-d H:i:s");
        if(count($result) > 0){
            $checkTime = date("Y-m-d H:i:s", strtotime($result[0]['create_dt'] . "+ ".$arr_config['money_exchange_delay']." hour"));
            if ($checkTime > $currentDate) {
                return $this->fail("환전신청 후 ".$arr_config['money_exchange_delay']."시간동안 재신청 불가능합니다.");
            }
        }
        
        // 23:30 ~ 00시30분 은행점검으로 환전신청 불가
        $bankCheckStart = date("Y-m-d ".$arr_config['bank_check_start']);
        $bankCheckEnd = date("Y-m-d ".$arr_config['bank_check_end']);
        $arrBankStart = explode(':', $arr_config['bank_check_start']);
        $arrBankEnd = explode(':', $arr_config['bank_check_end']);
        if($currentDate >= $bankCheckStart || $currentDate <= $bankCheckEnd){
            return $this->fail($arrBankStart[0]."시 ".$arrBankStart[1]."분 ~ ".$arrBankEnd[0]."시 ".$arrBankEnd[1]."분까지는 은행점검으로 환전신청이 불가합니다.");
        }

        //환전 신청시 입금 대비 베팅 롤링비가 5배이상이어야 환전 신청이 가능하다.
        //if (false === $this->checkBettingRollingAmount($memberModel,$memberIdx)) {
        //    return $this->fail('환전조건을 만족하지 못합니다 입금액보다 베팅 롤링비가 적습니다.');
        //}

        $this->initMemberData(session(), session()->get('member_idx'));
        try {
            $memberMEHModel = new MemberMoneyExchangeHistoryModel();
            $memberMEHModel->db->transStart();
            $current_day = date("Y-m-d H:i:s");
            $memberMEHModel->exchangeRequest($current_day,$exchangeMoney, $findMember->getMoney());
            //$memberModel->memberChangeMoney($memberIdx, $afterMoney);
            $memberModel->memberChangeMoney($memberIdx, - $exchangeMoney);
            
            UserPayBack::AddExchange($memberIdx,$exchangeMoney,$memberMEHModel);            
            $memberMEHModel->db->transComplete();
            session()->set('money', $afterMoney);
            
            
            
        }catch (\mysqli_sql_exception $e){
            $memberMEHModel->db->transRollback();
            session()->set('money', $findMember->getMoney());
            $response = [
                'result_code' => 400,
                'messages' => '환전 신청 실패',
                'messages_detail' => $e,
                'data' => []
            ];
            return $this->fail($response);
        }

        $response = [
            'result_code' => 200,
            'messages' => '환전 신청 성공',
            'data' => [
                'total_money'=> $afterMoney
            ]
        ];
        return $this->respond($response, 200);
    }

    private function checkBettingRollingAmount($memberModel, $memberIdx) {
        // 마지막으로 환전신청한 정보를 가져온다.       
        $sql = " SELECT update_dt as updateDt FROM member_money_exchange_history 
      	where member_idx = ? and status = 3 order by idx desc limit 1";

        $result = $memberModel->db->query($sql, [$memberIdx])->getResult();
        if (false === isset($result) || true === empty($result)) {
            return false;
        }

        // 마지막 환전한 시간을 기준으로 해서 입금총액을 가져온다.
        $limitDate = $result[0]->updateDt;
        $sql = " SELECT IF(SUM(money) IS NULL , 0 ,SUM(money)) as totalCharge FROM member_money_charge_history 
		where member_idx = ? and status = 3 and ? <= update_dt and charge_point_yn = 1";

        $resultCharge = $memberModel->db->query($sql, [$memberIdx, $limitDate])->getResult();
        $totalCharge = true === isset($resultCharge) && false === empty($resultCharge) ? $resultCharge->totalCharge : 0;

        // 마지막 환전한 시간을 기준으로 총 배팅한 금액을 가져온다.
        $sql = "  SELECT IF(SUM(T1.totalBet) IS NULL,0 ,SUM(T1.totalBet) ) as totalBet
        FROM (SELECT IF(SUM(BET_MNY) IS NULL,0,SUM(BET_MNY)) as totalBet
					FROM KP_CSN_BET_HIST CB
				  WHERE CB.MBR_IDX = ?
					AND ? <= CB.REG_DTM 
			  UNION
				  SELECT SUM(BET_MNY) as totalBet
					FROM KP_SLOT_BET_HIST SB
				  WHERE SB.MBR_IDX = ?
					AND ? <= SB.REG_DTM 
			  UNION 
				   SELECT SUM(BET_MNY) as totalBet
					FROM KP_ESPT_BET_HIST EB
				  WHERE EB.MBR_IDX = ? 
					AND ? <= EB.REG_DTM  
                    
			  UNION 
				   SELECT SUM(BET_MNY) as totalBet
					FROM OD_HASH_BET_HIST HB
				  WHERE HB.MBR_IDX = ? 
					AND ? <= HB.REG_DTM        
                          UNION
				  SELECT SUM(total_bet_money) as totalBet
					FROM member_bet mb_bet
                                        
				  WHERE mb_bet.member_idx = ? 
					AND ? <= mb_bet.create_dt  AND mb_bet.bet_status <> 5 AND mb_bet.total_bet_money <> mb_bet.take_money
                          UNION
				  SELECT SUM(total_bet_money) as totalBet
					FROM mini_game_member_bet mini_bet
				  WHERE mini_bet.member_idx = ? 
					AND ? <= mini_bet.create_dt  AND mini_bet.bet_status <> 5 AND mini_bet.total_bet_money <> mini_bet.take_money       
                ) T1";

        $resultBetting = $memberModel->db->query($sql, [$memberIdx, $limitDate, $memberIdx, $limitDate, $memberIdx,
                    $limitDate, $memberIdx, $limitDate, $memberIdx, $limitDate, $memberIdx, $limitDate])->getResult();

        $totalBet = true === isset($resultBetting) && false === empty($resultBetting) ? $resultBetting->totalBet : 0;
        if($totalCharge * 5 < $totalBet) return true;
    	else return false;
    }

}
