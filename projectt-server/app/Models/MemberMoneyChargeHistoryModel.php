<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Services;
use Psr\Log\LoggerInterface;
class MemberMoneyChargeHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_money_charge_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'u_key',
        'admin_idx',
        'deposit_name',
        'money',
        'status',
        'comment',
        'create_dt',
        'update_dt',
        'delete_dt',
        'bank_id',
        'bank_name',
        'account_number',
        'account_name',
        'referenceId',
        'bonus_point',
        'set_type',
    	'charge_point_yn',
        'bonus_option_idx',
        'bonus_level'
    ];

    //1: 입금 전 (신청) 2: 입금 확인 (대기) 3: 충전 완료 (완료) 999: 오류

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    // 충전 요청
    public function chargeRequest($depositName, $rMoney, $nowMoney, $uKey,$bank_id,$account_number,$referenceId,$bonus_point,$set_type
            ,$display_account_bank, $account_name, $charge_point_yn,
            $bonus_idx,$level): bool{
        $memberId  = $_SESSION['id'];
        $memberIdx = $_SESSION['member_idx'];

        $insertData = [
            'member_idx' => $memberIdx,
            'u_key' => $uKey,
            'deposit_name' => $depositName,
            'money' => $rMoney,
            'status' => 1,
            'bank_id'=>$bank_id,
            'bank_name'=> $display_account_bank,
            'account_number'=> $account_number,
            'account_name'=> $account_name,
            'referenceId' => $referenceId,
            'bonus_point' => $bonus_point,
            'set_type' => $set_type,
            'charge_point_yn' => $charge_point_yn,
            'bonus_option_idx' => $bonus_idx,
            'bonus_level' => $level,
        ];

        $tLogCashModel = new TLogCashModel();
        try {
            $idx = $this->insert($insertData);
            $tLogCashModel->insertCashLog($uKey, 101, $idx, $rMoney, $nowMoney,'R','');
            return true;
        }catch (\ReflectionException $e) {
        
            return false;
        }
    }
    
     // 코인 충전 요청
    public function chargeCoinRequest($depositName, $rMoney, $nowMoney,$uKey,$bank_id, $charge_point_yn): bool{
        $memberIdx = $_SESSION['member_idx'];

        $insertData = [
            'member_idx' => $memberIdx,
            'u_key' => $uKey,
            'deposit_name' => $depositName,
            'money' => $rMoney,
            'status' => 1,
            'bank_id'=>$bank_id,
            'charge_point_yn' => $charge_point_yn
        ];

        $tLogCashModel = new TLogCashModel();
        try {
            $idx = $this->insert($insertData);
            $tLogCashModel->insertCashLog($uKey, 302, $idx, $rMoney, $nowMoney,'R',''); // 코인 충전용 로그 
            return true;
        }catch (\ReflectionException $e) {
        
            return false;
        }
    }
    
    public function chargeVirtualRequest($depositName, $rMoney, $nowMoney, $uKey,$bank_id,$account_number,$referenceId,$bonus_point,$set_type, $display_account_bank, $account_name, $comment): bool{
        $memberId  = $_SESSION['id'];
        $memberIdx = $_SESSION['member_idx'];

        $insertData = [
            'member_idx' => $memberIdx,
            'u_key' => md5($memberIdx . strtotime('now')),
            'deposit_name' => $depositName,
            'money' => $rMoney,
            'status' => 1,
            'bank_id'=>$bank_id,
            'bank_name'=> $display_account_bank,
            'account_number'=> $account_number,
            'account_name'=> $account_name,
            'referenceId' => $referenceId,
            'bonus_point' => $bonus_point,
            'set_type' => $set_type,
            'comment' => $comment
        ];

        $tLogCashModel = new TLogCashModel();
        try {
            $idx = $this->insert($insertData);
            //$tLogCashModel->insertCashLog($uKey, 311, $idx, $rMoney, $nowMoney, '');
            return true;
        }catch (\ReflectionException $e) {
        
            return false;
        }
    }
}