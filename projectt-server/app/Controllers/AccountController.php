<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\GameModel;
use App\Models\MemberModel;
use App\Models\TLogCashModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\TotalMemberCashModel;

class AccountController extends BaseController {

    use ResponseTrait;

    public function getMyAccountInfo() {
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            return $this->fail('조회되는 회원이 없습니다.');
        }
        
        $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_charge' ";
        $result_game_config = $memberModel->db->query($sql)->getResult();
        if ('Y' == $result_game_config[0]->set_type_val) {
            return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
        }

        $level = $member->getLevel();
        $accountModel = new AccountModel();

        $sql = "SELECT charge_type FROM charge_type WHERE level = ? ";
        $result_charge_type = $accountModel->db->query($sql, [$level])->getResult();

        $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::result_charge_type ==> " . json_encode($result_charge_type));

        if (4 == $result_charge_type[0]->charge_type) {
            $sql = "SELECT  account.bank_id ,account_number_1 as account_number ,account_name_1 as account_name, account_bank_1 as display_account_bank 
                    FROM personal_account_number 
                    LEFT JOIN account ON account.account_name = personal_account_number.account_bank_1
                    LEFT JOIN member ON member.idx = personal_account_number.member_idx
                    WHERE member_idx = ?  ";
            $accountByLevel = $accountModel->db->query($sql, [$memberIdx])->getResult();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::accountByLevel ==> " . json_encode($accountByLevel));

            if (0 == count($accountByLevel) || 0 == $accountByLevel[0]->bank_id || false === isset($accountByLevel[0]->account_number) || false === isset($accountByLevel[0]->account_name) || false === isset($accountByLevel[0]->display_account_bank)) {
                $accountByLevel[0] = array('account_number' => '고객센터로 문의주시길 바랍니다.', 'account_name' => '', 'display_account_bank' => '');
            }
        } else if (5 == $result_charge_type[0]->charge_type) {
            $sql = "SELECT  account.bank_id ,account_number_2 as account_number ,account_name_2 as account_name, account_bank_2 as display_account_bank 
                    FROM personal_account_number 
                    LEFT JOIN account ON account.account_name = personal_account_number.account_bank_2
                    LEFT JOIN member ON member.idx = personal_account_number.member_idx
                    WHERE member_idx = ?  ";
            $accountByLevel = $accountModel->db->query($sql, [$memberIdx])->getResult();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::accountByLevel ==> " . json_encode($accountByLevel));

            if (0 == count($accountByLevel) || 0 == $accountByLevel[0]->bank_id || false === isset($accountByLevel[0]->account_number) || false === isset($accountByLevel[0]->account_name) || false === isset($accountByLevel[0]->display_account_bank)) {
                $accountByLevel[0] = array('account_number' => '고객센터로 문의주시길 바랍니다.', 'account_name' => '', 'display_account_bank' => '');
            }
        } else {
            $accountByLevel = $accountModel->where('level', $level)->find();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::accountByLevel ==> " . json_encode($accountByLevel));
            if (0 == count($accountByLevel) || 0 == $accountByLevel[0]['bank_id'] || false === isset($accountByLevel[0]['account_number']) || false === isset($accountByLevel[0]['account_name']) || false === isset($accountByLevel[0]['display_account_bank'])) {
                return $this->fail('조회되는 계좌 정보가 없습니다.');
            }
        }
        // total_member_cash 테이블에 조회수 업데이트 

        $uKey = md5($memberIdx . strtotime('Now'));
        $tLogCashModel = new TLogCashModel();
        try {
            $tLogCashModel->insertCashLog($uKey, 103, 0, 0, $member->getMoney(),'R','');
            
        } catch (\ReflectionException $e) {
            return $this->fail('계좌 조회중에 에러가 발생하였습니다.');
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'account' => $accountByLevel[0],
                'uKey' => $uKey
            ]
        ];
        return $this->respond($response, 200);
    }

    // my exchange account info
    public function getMyExchangeAccountInfo() {
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null) {
            return $this->fail('조회되는 회원이 없습니다.');
        }
        
        $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_charge' ";
        $result_game_config = $memberModel->db->query($sql)->getResult();
        if ('Y' == $result_game_config[0]->set_type_val) {
            return $this->fail('충전 시스템 점검으로 충전 이용이 불가합니다.');
        }

        $level = $member->getLevel();
        $accountModel = new AccountModel();

        $sql = "SELECT charge_type FROM charge_type WHERE level = ? ";
        $result_charge_type = $accountModel->db->query($sql, [$level])->getResult();

        $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::result_charge_type ==> " . json_encode($result_charge_type));

        if (4 == $result_charge_type[0]->charge_type) {
            $sql = "SELECT  account.bank_id ,account_number_1 as account_number ,account_name_1 as account_name, account_bank_1 as display_account_bank 
                    FROM personal_account_number 
                    LEFT JOIN account ON account.account_name = personal_account_number.account_bank_1
                    LEFT JOIN member ON member.idx = personal_account_number.member_idx
                    WHERE member_idx = ?  ";
            $accountByLevel = $accountModel->db->query($sql, [$memberIdx])->getResult();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::accountByLevel ==> " . json_encode($accountByLevel));

            if (0 == count($accountByLevel) || 0 == $accountByLevel[0]->bank_id || false === isset($accountByLevel[0]->account_number) || false === isset($accountByLevel[0]->account_name) || false === isset($accountByLevel[0]->display_account_bank)) {
                $accountByLevel[0] = array('account_number' => '고객센터로 문의주시길 바랍니다.', 'account_name' => '', 'display_account_bank' => '');
            }
        } else if (5 == $result_charge_type[0]->charge_type) {
            $sql = "SELECT  account.bank_id ,account_number_2 as account_number ,account_name_2 as account_name, account_bank_2 as display_account_bank 
                    FROM personal_account_number 
                    LEFT JOIN account ON account.account_name = personal_account_number.account_bank_2
                    LEFT JOIN member ON member.idx = personal_account_number.member_idx
                    WHERE member_idx = ?  ";
            $accountByLevel = $accountModel->db->query($sql, [$memberIdx])->getResult();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::accountByLevel ==> " . json_encode($accountByLevel));

            if (0 == count($accountByLevel) || 0 == $accountByLevel[0]->bank_id || false === isset($accountByLevel[0]->account_number) || false === isset($accountByLevel[0]->account_name) || false === isset($accountByLevel[0]->display_account_bank)) {
                $accountByLevel[0] = array('account_number' => '고객센터로 문의주시길 바랍니다.', 'account_name' => '', 'display_account_bank' => '');
            }
        } else {
            $accountByLevel = $accountModel->where('level', $level)->find();
            $this->logger->debug("!!!!!!!!!!!!!!!!!! ***************** AccountController::getMyAccountInfo::accountByLevel ==> " . json_encode($accountByLevel));
            if (0 == count($accountByLevel) || 0 == $accountByLevel[0]['bank_id'] || false === isset($accountByLevel[0]['account_number']) || false === isset($accountByLevel[0]['account_name']) || false === isset($accountByLevel[0]['display_account_bank'])) {
                return $this->fail('조회되는 계좌 정보가 없습니다.');
            }
        }
        // total_member_cash 테이블에 조회수 업데이트 

        $uKey = md5($memberIdx . strtotime('Now'));
        $tLogCashModel = new TLogCashModel();
        try {
            $tLogCashModel->insertCashLog($uKey, 103, 0, 0, $member->getMoney(),'R','');
            
        } catch (\ReflectionException $e) {
            return $this->fail('계좌 조회중에 에러가 발생하였습니다.');
        }

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'account' => $accountByLevel[0],
                'uKey' => $uKey
            ]
        ];
        return $this->respond($response, 200);
    }
}
