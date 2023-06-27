<?php namespace App\Models;

use App\Entities\Member;
use CodeIgniter\Model;

class MemberMoneyExchangeHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_money_exchange_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'u_key',
        'admin_idx',
        'money',
        'status',
        'comment',
        'create_dt',
        'update_dt',
        'delete_dt'
    ];

    // 1: 환전 신청 (신청)  2: 환전 대기 (대기)  3: 환전 완료 (완료) 999: 오류

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    // 환전 요청
    public function exchangeRequest($today,$rMoney, $nowMoney): bool{
        $memberIdx = $_SESSION['member_idx'];
        $uKey = md5($memberIdx.strtotime('Now'));

        $insertData = [
            'member_idx' => $memberIdx,
            'u_key' => $uKey,
            'money' => $rMoney,
            'status' => 1,
            'create_dt' => $today
        ];

        $tLogCashModel = new TLogCashModel();
        try {
            $idx = $this->insert($insertData);
            $tLogCashModel->insertCashLog($uKey, 102, $idx, $rMoney, $nowMoney,'R','');
            return true;
        }catch (\ReflectionException $e) {
            return false;
        }
    }

}