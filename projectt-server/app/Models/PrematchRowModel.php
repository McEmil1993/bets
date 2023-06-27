<?php namespace App\Models;

use CodeIgniter\Model;

class PrematchRowModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'prematch_row';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'prematch_idx',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}