<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsBookmakerModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_bookmaker';
    // protected $primaryKey = 'idx';
	protected $primaryKey = 'id';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id', 'name', 'lang', 'create_dt', 'update_dt', 'delete_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}