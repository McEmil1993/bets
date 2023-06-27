<?php namespace App\Models;

use CodeIgniter\Model;

class MemberIpBlockHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_ip_block_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'admin_idx',
        'member_idx',
        'login_history_idx',
        'memo',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}