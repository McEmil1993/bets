<?php namespace App\Entities;

use CodeIgniter\Entity;

class MemberBet extends Entity
{
    protected $casts = [
        'idx'               => 'int',
        'member_idx'        => 'int',
        'ls_fixture_id'     => 'int',
        'ls_markets_id'     => 'int',
        'bet_type'          => 'string',
        'total_bet_price'   => 'double',
        'total_bet_money'   => 'int',
        'is_betting_slip'   => 'string'
    ];

    public function getIdx()
    {
        return $this->attributes['idx'];
    }

    public function getMemberIdx()
    {
        return $this->attributes['member_idx'];
    }

    public function getLsFixtureId()
    {
        return $this->attributes['ls_fixture_id'];
    }

    public function getLsMarketsId()
    {
        return $this->attributes['ls_markets_id'];
    }

    public function getBetType()
    {
        return $this->attributes['bet_type'];
    }

    public function getTotalBetPrice()
    {
        return $this->attributes['total_bet_price'];
    }

    public function getTotalBetMoney()
    {
        return $this->attributes['total_bet_money'];
    }

    public function getCreateDt()
    {
        return $this->attributes['create_dt'];
    }

    public function getUpdateDt()
    {
        return $this->attributes['update_dt'];
    }

    public function getDeleteDt()
    {
        return $this->attributes['delete_dt'];
    }
}