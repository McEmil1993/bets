<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class TotalMemberCashModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'total_member_cash';
    protected $primaryKey = 'member_idx';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'action',
        'charge_total_count',
        'loock_up_total_count',
        'charge_total_money',
        'update_dt',
        'create_dt'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
}
