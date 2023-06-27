<?php namespace App\Models;

use CodeIgniter\Model;

class MemberBettingPrematchAndRealtimeHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_betting_prematch_and_realtime_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx',
        'game_type',
        'game_cnt',
        'odds',
        'betting_money',
        'expwin_money',
        'betting_datetime',
        'result',
        'cancel'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}