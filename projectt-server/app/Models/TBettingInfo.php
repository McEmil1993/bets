<?php

namespace App\Models;

use CodeIgniter\Model;

class TBettingInfo extends Model
{

    protected $DBGroup = 'default';
    protected $table = 'tb_betting_info';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'status',
        'trx_id',
        'round_id',
        'bet_money',
        'take_money',
        'game_id',
        'product_id',
        'product_type_id',
        'provider_id',
        'product_group_id',
        'bonus_type',
        'create_dt',
        'calc_dt',
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getOrderByTransId($transactionId)
    {
        return $this->where('trx_id', $transactionId)->first();
    }

    public function getPendingBets($productId=null, $transactionId=null)
    {
        $sql = "SELECT * FROM tb_betting_info WHERE provider_id = 2 AND status = 'B' AND DATE_ADD(create_dt, INTERVAL 3 MINUTE) < NOW()";
        $qCondition = [];
        if($productId) {
            $sql .= ' AND product_id = ?';
            array_push($qCondition, $productId);
        }
        if($transactionId) {
            $sql .= ' AND trx_id = ?';
            array_push($qCondition, $transactionId);
        }

        return $this->db->query($sql, $qCondition)->getResultArray();
    }

    public function findKGOrderStatus($productId, $gameId, $transactionId)
    {
        return $this->where('provider_id', 2)
            ->where('product_id', $productId)
            ->where('game_id', $gameId)
            ->where('trx_id', $transactionId)
            ->first();
    }

    public function findOrderStatusByProvider($providerId, $productId, $gameId, $transactionId)
    {
        return $this->where('provider_id', $providerId)
            ->where('product_id', $productId)
            ->where('game_id', $gameId)
            ->where('trx_id', $transactionId)
            ->first();
    }

    public function setOrderStatus($transactionId, $gameId, $betAmount, $winAmount)
    {
        return $this->set('bet_money', $betAmount)->set('take_money', $winAmount)
            ->where('trx_id', $transactionId)
            ->where('game_id', $gameId)
            ->update();
    }
}
