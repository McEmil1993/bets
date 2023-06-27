<?php namespace App\Models;

use CodeIgniter\Model;

class PrematchModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'prematch';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'game_type',
        'game_dt',
        'event',
        'league',
        'home_team',
        'expedition_team',
        'score',
        'win',
        'draw',
        'lose',
        'betting_type',
        'betting_total_money',
        'betting_status',
        'betting_on_off'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}