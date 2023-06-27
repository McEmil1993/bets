<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class MemberDayChargeEvent extends Model {

    protected $DBGroup = 'default';
    protected $table = 'tb_member_day_charge_event';
    protected $primaryKey = 'member_idx';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'count',
        'tot_charge',
        'update_dt',
        'create_dt'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
