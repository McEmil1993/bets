<?php namespace App\Models;

use CodeIgniter\Model;

class SmsAuthModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'sms_auth';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'phone_number',
        'auth_number',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}