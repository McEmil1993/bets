<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class MemberDayChargeEventHistory extends Model {

    protected $DBGroup = 'default';
    protected $table = 'tb_member_day_charge_event_reward_history';
    protected $primaryKey = 'member_idx';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'reward_idx',
        'create_dt'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
