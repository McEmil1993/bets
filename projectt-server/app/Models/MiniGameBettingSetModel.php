<?php namespace App\Models;

use CodeIgniter\Model;

class MiniGameBettingSetModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'mini_game_betting_set';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}