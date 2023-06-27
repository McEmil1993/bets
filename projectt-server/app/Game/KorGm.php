<?php

namespace App\Game;

use Exception;
use App\Util\CodeUtil;
use App\Util\httpUtil;
use CodeIgniter\Log\Logger;
use App\Models\TLogCashModel;
use App\RollingComps\UserBettingLose;

class KorGm {

    private $logger;
    private $httpUtil;
    private $apiUrl = 'https://was365api.com';
    private $apiKeyAuth = '1RfZbUCzrisejAK1wBZC';
    private $secretApiToken = 'df5eeacd1d5c27d1633df2fde7c3fed29f0555f414c9d971d313dfc3935737c1';

    public function __construct($logger) {
        $this->logger = $logger;
        $this->httpUtil = new httpUtil();
    }

    public function auth($member, $product_group_id, $product_type_id, $provider_id, $chkMobile, $memberModel, $logger) {
        try {
            $sql = "select * from tb_static_provider_manager where provider_id = ? and product_type_id = ? and is_use = 'ON' ";
            $provider = $memberModel->db->query($sql, [$provider_id, $product_type_id])->getResultArray();
            if (!isset($provider)) {
                return [0, '', '사용할수없는 프로바이더입니다.'];
            }

            $sql = "select * from tb_static_product_id_manager where provider_id = ? and product_type_id = ? and product_group_id = ? and is_use = 'ON' ";
            $product = $memberModel->db->query($sql, [$provider_id, $product_type_id, $product_group_id])->getResultArray();
            if (!isset($product)) {
                return [0, '', '사용할수없는 게임사입니다.'];
            }


            //$logger->error(base_url());
            $ip = CodeUtil::get_client_ip();
            $url = $this->apiUrl . '/game/auth';
            $body = json_encode(
                    array(
                        'playerId' => (int)$member->getIdx(),
                        'playerUsername' => $member->getId(),
                        'playerIP' => $ip,
                        'playerLanguage' => 'ko',
                        'playerCurrency' => 'krw',
                        'playerBalance' => (double)$member->getMoney(),
                        'productId' => (int)$product[0]['product_id'],
                        'gameId' => 0,
                        'lobby' => base_url(),
                        'mobile' => "PC" == $chkMobile ? false : true
                    )
            );
            $logger->info($body);
            $result = $this->httpUtil->postData($url, $body, array('Content-Type: application/json', 'charset=UTF-8', 'X-WA-API-KEY:' . $this->apiKeyAuth));
            $result = json_decode($result);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('[MYSQL EXCEPTION] auto_money_charge (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')' . ' query : ' . $memberModel->getLastQuery());
            return [0, '', '데이터 베이스 처리 실패'];
        }

        return [$result->status, $result->lunchUrl, $result->message];
    }

    public function getKgDataList($url) {
        try {
            $result = $this->httpUtil->getData(
                    $this->apiUrl . $url, ['Content-Type: application/json', 'charset=UTF-8', 'X-WA-API-KEY:' . $this->apiKeyAuth]
            );
            return json_decode($result);
        } catch (Exception $e) {

            $this->logger->error('Something went wrong on request: ' . $e->getMessage());
            return [0, '', 'error message'];
        }
    }

    private function getBettingData($memberIdx, $transactionId, $memberModel) {
        $sql = "select * from tb_betting_info where member_idx = ? and trx_id = ? and provider_id = 2";
        $betinfo = $memberModel->db->query($sql, [$memberIdx, $transactionId])->getResultArray();
        return $betinfo;
    }

    private function addBet($member, $transactionId, $transactionAmount, $productId, $gameId, $roundId, $memberModel) {
        $sql = "select * from tb_static_product_id_manager where product_id = ? and provider_id = 2";
        $product = $memberModel->db->query($sql, [$productId])->getResultArray();

        // after money
        $af_money = $member->getMoney() - $transactionAmount;
        
        $sql = "insert into tb_betting_info (member_idx,status,trx_id, round_id, bet_money, hold_money, game_id, product_id, product_type_id, provider_id,product_group_id)
        values (?,'B', ?, ?, ?, ?,?,?,?,?,?)";
        $memberModel->db->query($sql, [$member->getIdx(), $transactionId, $roundId, $transactionAmount, $af_money, $gameId
            , $productId, $product[0]['product_type_id'], $product[0]['provider_id'], $product[0]['product_group_id']]);
        //$acIdx = $memberModel->getInsertID();

        $sql = 'SELECT idx FROM tb_betting_info where member_idx = ? order by idx desc limit 1';
        $acIdx = $memberModel->db->query($sql, [$member->getIdx()])->getResultArray()[0];

        $sql = "update member set money = money - ? where idx = ?";
        $memberModel->db->query($sql, [$transactionAmount, $member->getIdx()]);

        $log = new TLogCashModel();
        $ukey = md5($member->getIdx() . strtotime('now'));
        $log->insertCashLog($uKey, $member->getIdx(), ADD_BET, $acIdx, $transactionAmount, $member->getMoney(), $af_money, 0, 0, 0, 'M', '카지노베팅');
        return $acIdx;
    }

    private function updateBetting($member, $acIdx, $resultType, $transactionId, $transactionAmount, $memberModel) {
        $UserBettingLose = new UserBettingLose();

        $sql = "select bet_money from tb_betting_info where member_idx = ? and trx_id = ?";
        $bet_money = $memberModel->db->query($sql, [$member->getIdx(), $transactionId])->getResultArray()[0];

        $amount = $transactionAmount;
        $acCode = BET_RESULT;
        if ('C' == $resultType) {
            $amount =- $amount;
            $acCode = BET_CANCEL;
            $UserBettingLose->AddBetting($member->getIdx(),'casino',$bet_money,$memberModel); // 롤링콤프 베팅금액
        }else if ('W' == $resultType) {
            $UserBettingLose->AddBetting($member->getIdx(),'casino',$bet_money,$memberModel); // 롤링콤프 베팅금액
        }else if ('L' == $resultType) {
            $UserBettingLose->AddBetting($member->getIdx(),'casino',$bet_money,$memberModel); // 롤링콤프 베팅금액
            $UserBettingLose->AddLoseBetting($member->getIdx(),'casino',$bet_money,$memberModel); // 롤링콤프 낙첨금액
        }

        $sql = 'select idx from member_money_charge_history where member_idx = ? and status = 3 order by update_dt desc limit 1';
        $bet_idx = $memberModel->db->query($sql, [$member->getIdx()])->getResultArray()[0];

        $sql = "UPDATE member_money_charge_history SET casino_bet_money = casino_bet_money + ? WHERE idx = ?";
        $memberModel->db->query($sql, [$bet_money, $bet_idx]);

        $sql = "update tb_betting_info set status = ?,take_money = take_money + ?, hold_money = ?, calc_dt = now() where member_idx = ? and trx_id = ?";
        $memberModel->db->query($sql, [$resultType, $amount, $member->getMoney() + $transactionAmount, $member->getIdx(), $transactionId]);

        $sql = "update member set money = money + ? where idx = ?";
        $memberModel->db->query($sql, [$transactionAmount, $member->getIdx()]);

        $log = new TLogCashModel();
        $ukey = md5($member->getIdx() . strtotime('now'));
        $log->insertCashLog($uKey, $member->getIdx(), $acCode, $acIdx, $transactionAmount, $member->getMoney(), $member->getMoney() + $transactionAmount, 0, 0, 0, 'P', '베팅결과처리');
    }

    private function updateBettingBonus($member, $acIdx, $resultType, $transactionId, $transactionAmount, $promoType, $memberModel) {
        $sql = "update tb_betting_info set status = ?,take_money = take_money + ?, hold_money = ? ,bonus_type = ? ,calc_dt = now() where member_idx = ? and trx_id = ?";
        $memberModel->db->query($sql, [$resultType, $transactionAmount, $member->getMoney() + $transactionAmount, $promoType, $member->getIdx(), $transactionId]);

        $sql = "update member set money = money + ? where idx = ?";
        $memberModel->db->query($sql, [$transactionAmount, $member->getIdx()]);

        $log = new TLogCashModel();
        $ukey = md5($member->getIdx() . strtotime('now'));
        $log->insertCashLog($uKey, $member->getIdx(), BET_RESULT, $acIdx, $transactionAmount, $member->getMoney(), $member->getMoney() + $transactionAmount, 0, 0, 0, 'P', '베팅결과처리');
    }

    //callback
    public function balance($member, $post, $memberModel) {
        return [1, $member->getMoney(), 'OK'];
    }

    public function debit($member, $post, $memberModel) {

        $transactionId = isset($post['transactionId']) ? $post['transactionId'] : '';
        $transactionAmount = isset($post['transactionAmount']) ? $post['transactionAmount'] : 0;
        $productId = isset($post['productId']) ? $post['productId'] : 0;
        $gameId = isset($post['gameId']) ? $post['gameId'] : '';
        $roundId = isset($post['roundId']) ? $post['roundId'] : '';
        
        if ($member->getMoney() < $transactionAmount) {
            return [0, 0, 'INSUFFICIENT_FUNDS'];
        }
        // 베팅 데이터를 가져온다.
        $betinfo = $this->getBettingData($member->getIdx(), $transactionId, $memberModel);
        //if (true === empty($betinfo)) {
        if (false === empty($betinfo)) {
            return [0, 0, 'DUPLICATE_DEBIT'];
        }

        $acIdx = $this->addBet($member, $transactionId, $transactionAmount, $productId, $gameId, $roundId, $memberModel);

        $UserBettingLose = new UserBettingLose();

        //echo $_POST['creditAmount'];
        // 배팅
        if (!isset($post['creditAmount'])) {
            return [1, $member->getMoney() - $transactionAmount, 'OK'];
            // 배팅과 동시에 결과가 나온거다     
        } else {
            $resultType = 'L';
            if (0 < $post['creditAmount']) {
                $resultType = 'W';
            }

            $this->updateBetting($member, $acIdx, $resultType, $transactionId, $post['creditAmount'], $memberModel);

            return [1, $member->getMoney() - $transactionAmount + $_POST['creditAmount'], 'OK'];
        }
    }

    public function credit($member, $post, $memberModel) {
        $transactionId = isset($post['transactionId']) ? $post['transactionId'] : '';
        $transactionAmount = isset($post['transactionAmount']) ? $post['transactionAmount'] : 0;
        $productId = isset($post['productId']) ? $post['productId'] : 0;

        // 베팅 데이터를 가져온다.
        $betinfo = $this->getBettingData($member->getIdx(), $transactionId, $memberModel);
        if (empty($betinfo)) {
            return [0, 0, 'INVALID_DEBIT'];
        }

        if ('B' != $betinfo[0]['status']) {
            return [0, 0, 'DUPLICATE_CREDIT'];
        }

        // 배팅

        $resultType = 'L';
        if (0 < $transactionAmount) {
            $resultType = 'W';
        }

        $this->updateBetting($member, $betinfo[0]['idx'], $resultType, $transactionId, $transactionAmount, $memberModel);

        return [1, $member->getMoney() + $transactionAmount, 'OK'];
    }

    public function cancel($member, $post, $memberModel) {
        $transactionId = isset($post['transactionId']) ? $post['transactionId'] : '';
        $transactionAmount = isset($post['transactionAmount']) ? $post['transactionAmount'] : 0;
        $productId = isset($post['productId']) ? $post['productId'] : 0;

        // 베팅 데이터를 가져온다.
        $betinfo = $this->getBettingData($member->getIdx(), $transactionId, $memberModel);
        if (empty($betinfo)) {
            return [0, 0, 'INVALID_DEBIT'];
        }

        if ('B' != $betinfo[0]['status']) {
            return [0, 0, 'DUPLICATE_CREDIT'];
        }

        // 배팅
        $resultType = 'C';
        $this->updateBetting($member, $betinfo[0]['idx'], $resultType, $transactionId, $transactionAmount, $memberModel);
        return [1, $member->getMoney() + $transactionAmount, 'OK'];
    }

    public function adjustWinAmount($member, $post, $memberModel) {
        $transactionId = isset($post['transactionId']) ? $post['transactionId'] : '';
        $transactionAmount = isset($post['transactionAmount']) ? $post['transactionAmount'] : 0;    // 당첨금
        $transactionAdjustingAmount = isset($post['transactionAdjustingAmount']) ? $post['transactionAdjustingAmount'] : 0; // 당첨금에서 차증 또는 차감되는 금액
        $productId = isset($post['productId']) ? $post['productId'] : 0;

        // 베팅 데이터를 가져온다.
        $betinfo = $this->getBettingData($member->getIdx(), $transactionId, $memberModel);
        if (empty($betinfo)) {
            return [0, 0, 'INVALID_DEBIT'];
        }

        if ('B' == $betinfo[0]['status']) {
            return [0, 0, 'DUPLICATE_CREDIT'];
        }

        // 배팅
        $resultType = 'W';
        $adjustTakeMoney = $betinfo[0]['take_money'] + $transactionAdjustingAmount;
        if (0 == $adjustTakeMoney) {
            $resultType = 'L';
        }

        $this->updateBetting($member, $betinfo[0]['idx'], $resultType, $transactionId, $transactionAdjustingAmount, $memberModel);
        return [1, $member->getMoney() + $transactionAdjustingAmount, 'OK'];
    }

    public function bonus($member, $post, $memberModel) {
        $transactionId = isset($post['transactionId']) ? $post['transactionId'] : '';
        $transactionAmount = isset($post['transactionAmount']) ? $post['transactionAmount'] : 0;
        $promoType = isset($post['promoType']) ? $post['promoType'] : '';
        $productId = isset($post['productId']) ? $post['productId'] : 0;
        $gameId = isset($post['gameId']) ? $post['gameId'] : '';

        // 베팅 데이터를 가져온다.
        $betinfo = $this->getBettingData($member->getIdx(), $transactionId, $memberModel);
        if (empty($betinfo)) {
            return [0, 0, 'INVALID_DEBIT'];
        }

        if ('L' != $betinfo[0]['status']) {
            return [0, 0, 'DUPLICATE_CREDIT'];
        }

        // 배팅
        $this->updateBettingBonus($member, $betinfo[0]['idx'], 'W', $transactionId, $transactionAmount, $promoType, $memberModel);

        return [1, $member->getMoney() + $transactionAmount, 'OK'];
    }

    public function processRequest($member, $memberModel, $function_name, $__getData, $logger) {
        try {
            // token check
            $SercetKey = $_SERVER["HTTP_WA_SECRET_TOKEN"];
            
            //$logger->info('token : '.$SercetKey);
            list($retCode, $result) = $this->authenticateRequest($SercetKey);
            if (0 == $retCode) {
                //$memberModel->db->transRollback();
                return array(0, $result);
            }

            //$memberModel->db->transStart();
            // user check
            /* $userId = $__getData['userId'];

              $member = $memberModel->setMemberWhereId($userId);
              if ($member == null) {
              //$memberModel->db->transRollback();
              return array(0, 'INVALID_USER');
              } */

            // api function call
            list($retCode, $result, $message) = $this->$function_name($member, $__getData, $memberModel);
            if (0 == $retCode) {
                //$memberModel->db->transRollback();
                return array(0, $message);
            }

            //$memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            //$memberModel->db->transRollback();
            return $this->fail($e);
        }

        return [1, $result];
    }

    private function authenticateRequest($secretKeyToken) {
        //$secretKeyToken = (isset($this->server['HTTP_WA_SECRET_TOKEN'])) ? $this->server['HTTP_WA_SECRET_TOKEN'] : '';
        //check validation of secret key
        if ($secretKeyToken != $this->secretApiToken) {
            return [0, 'INVALID_REQUEST'];
        }

        return [1, 'success'];
    }

    public function isLoginById($userId, $function_name, $memberModel) {

        $member = $memberModel->setMemberWhereId($userId);
        if ($member == null) {
            return [null, '조회되는 회원 또는 메세지 idx 가 없습니다.'];
        }

        if ($member->getUBusiness() != 1) {
            return [null, '총판은 배팅 이용이 불가능합니다.'];
        }

        if ($member->getStatus() == 11) {
            $response['messages'] = '관리자 승인이 필요합니다.';
            return [null, '관리자 승인이 필요합니다.'];
        }

        if ('credit' != $function_name && 'cancel' != $function_name) {
            if ($member->getStatus() == 2 || $member->getStatus() == 3) {
                return [null, '사용이 불가능 한 계정으로 관리자에게 문의바랍니다.'];
            }
        }

        return [$member, ''];
    }

}
