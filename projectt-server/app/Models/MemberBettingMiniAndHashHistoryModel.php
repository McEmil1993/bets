<?php namespace App\Models;

use CodeIgniter\Model;

class MemberBettingMiniAndHashHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_betting_mini_and_hash_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx',
        'data_type',
        'game_type',
        'channel',
        'round',
        'betting_money',
        'hit_money',
        'result',
        'betting_datetime'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}