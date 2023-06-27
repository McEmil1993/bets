<?php namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'test_admin';
    protected $primaryKey = 'adminCd';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['adminId', 'adminPw',];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}