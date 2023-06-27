<?php

namespace App\Models;

use CodeIgniter\Model;

class TSTTProductType extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'tb_static_porduct_type';
    protected $primaryKey = 'product_type_id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_type_name',
        'product_type_name_kr',
        'create_dt',
        'update_dt'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
