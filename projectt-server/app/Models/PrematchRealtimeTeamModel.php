<?php namespace App\Models;

use CodeIgniter\Model;

class PrematchRealtimeTeamModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'prematch_realtime_team';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'event_type',
        'local',
        'league_front_name',
        'team_id',
        'team_front_name'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}