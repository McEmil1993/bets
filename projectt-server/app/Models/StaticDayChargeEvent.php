<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class StaticDayChargeEvent extends Model {

    protected $DBGroup = 'default';
    protected $table = 'tb_static_day_charge_event';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'tg_count',
        'tg_money',
        'reward',
        'create_dt'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
