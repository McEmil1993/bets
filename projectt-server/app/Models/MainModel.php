<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class MainModel extends Model {

    protected $DBGroup = 'main_db';
    protected $table = 'banners';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'thumbnail',
        'display_type',
        'status',
        'rank',
        'create_dt',
        'update_dt',
        'delete_dt'
      
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
