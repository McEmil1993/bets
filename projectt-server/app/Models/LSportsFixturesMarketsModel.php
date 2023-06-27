<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsFixturesMarketsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_fixtures_markets';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fixture_id',
        'livescore',
        'markets_id',
        'markets_name',
        'create_dt',
        'update_dt',
        //'delete_dt',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}