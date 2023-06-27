<?php namespace App\Models;

use CodeIgniter\Model;

class MemberMoneyHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_money';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'money',
        'type',
        'type_idx',
        'create_dt',
        'update_dt',
        'delete_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}