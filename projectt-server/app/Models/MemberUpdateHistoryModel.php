<?php namespace App\Models;

use CodeIgniter\Model;

class MemberUpdateHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_update_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx',
        'update_type',
        'before_data',
        'after_data',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}