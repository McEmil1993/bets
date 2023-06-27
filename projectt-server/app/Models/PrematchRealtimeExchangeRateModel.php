<?php namespace App\Models;

use CodeIgniter\Model;

class PrematchRealtimeExchangeRateModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'prematch_realtime_exchange_rate';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'event_id',
        'event_type',
        'exchange_rate'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}