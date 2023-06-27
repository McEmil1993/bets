<?php namespace App\Models;

use CodeIgniter\Model;

class AccountLogModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'account_log';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx', 'member_id'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}