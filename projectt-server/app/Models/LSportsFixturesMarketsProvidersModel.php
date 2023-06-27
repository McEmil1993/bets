<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsFixturesMarketsProvidersModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_fixtures_markets_providers';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fixture_id',
        'markets_id',
        'markets_name',
        'providers_id',
        'providers_name',
        'providers_last_update',
        'providers_bet_id',
        'providers_bet_name',
        'providers_bet_status',
        'providers_bet_start_price',
        'providers_bet_settlement',
        'providers_bet_last_update',
        'create_dt',
        'update_dt',
        'delete_dt',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}