<?php namespace App\Models;

use CodeIgniter\Model;

class HelpModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'help';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx',
        'admin_idx',
        'title',
        'contents',
        'status',
        'is_delete',
        'create_datetime'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}