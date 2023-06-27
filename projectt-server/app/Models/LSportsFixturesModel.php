<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsFixturesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_fixtures';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';

    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fixture_id',
        'fixture_sport_id',
        //'fixture_sport_name',
        'fixture_location_id',
        //'fixture_location_name',
        'fixture_league_id',
        //'fixture_league_name',
        'fixture_start_date',
        //'fixture_start_date_utc',
        //'fixture_last_update',
        'fixture_status',
        'fixture_participants_1_id',
        //'fixture_participants_1_name',
        'fixture_participants_2_id',
        //'fixture_participants_2_name',
        'livescore',
        'markets',
        'create_dt',
        'update_dt',
        //'delete_dt',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}