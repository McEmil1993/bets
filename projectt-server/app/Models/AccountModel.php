<?php namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'account_level_list';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'account_code',
        'account_name',
        'account_number',
        'level',
        'u_key',
        'create_dt',
        'update_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}