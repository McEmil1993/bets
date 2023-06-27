<?php namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'events';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'name',
        'thumbnail',
        'detail',
        'status',
        'del_flag',
        'create_dt',
        'update_dt',
        'delete_dt',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
  

}