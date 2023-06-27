<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsBetModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_bet';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fixture_id',
        'markets_id',
        //'markets_name',
        //'real_bet_price',
        'providers_id',
        //'providers_name',
        //'providers_last_update',
        'bet_id',
        'bet_name',
        'bet_status',
        'bet_base_line',
        'bet_settlement',
        //'bet_start_price',
        //'bet_last_update',
        'live_status',
        'live_time',
        //'live_current_period',
        'live_results_p1',
        'live_results_p2',
        'admin_bet_status',
        'create_dt',
        'update_dt',
        //'delete_dt',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}