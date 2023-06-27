<?php namespace App\Models;

use CodeIgniter\Model;

class MemberBettingSetModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_betting_set';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx',
        'game_1',
        'game_2',
        'game_3',
        'game_4',
        'game_5',
        'game_6',
        'game_7',
        'game_8',
        'game_9',
        'game_10',
        'game_11',
        'game_12',
        'game_13',
        'game_14',
        'game_15',
        'game_16',
        'game_17',
        'game_18',
        'game_19',
        'game_20',
        'game_21',
        'game_22'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}