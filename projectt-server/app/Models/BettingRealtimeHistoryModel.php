<?php namespace App\Models;

use CodeIgniter\Model;

class BettingRealtimeHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'betting_realtime_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
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