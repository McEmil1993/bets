<?php namespace App\Models;

use CodeIgniter\Model;

class PrematchRealtimeEventModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'prematch_realtime_event';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'event_id',
        'event_name',
        'event_front_name',
        'image_url',
        'is_use',
        'is_delete'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}