<?php namespace App\Models;

use CodeIgniter\Model;

class BettingPreMatchAndRealtimeRowModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'betting_prematch_and_realtime_row';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'freemath_idx',
        'game_time',
        'game_type',
        'league',
        'home_team',
        'vs',
        'expedition_team',
        'game_result',
        'betting',
        'status',
        'cancle'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}