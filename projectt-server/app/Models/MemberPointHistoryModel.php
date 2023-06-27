<?php namespace App\Models;

use CodeIgniter\Model;

class MemberPointHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_point_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'member_idx',
        'use_point',
        'bef_point',
        'use_type'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}