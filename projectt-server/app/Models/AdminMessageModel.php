<?php namespace App\Models;

use CodeIgniter\Model;

class AdminMessageModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'admin_message';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'admin_idx',
        'member_idx',
        'title',
        'contents',
        'read_status',
        'is_delete',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}