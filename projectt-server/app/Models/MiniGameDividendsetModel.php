<?php namespace App\Models;

use CodeIgniter\Model;

class MiniGameDividendsetModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'mini_game_dividend_set';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'game_type',
        'end_time',
        'un_over',
        'odd_even',
        'small',
        'medium',
        'large',
        '34',
        'left_right',
        '12',
        'goal_nogoal'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}