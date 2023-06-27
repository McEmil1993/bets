<?php

namespace App\Models;

use CodeIgniter\Model;

class TProductIdManager extends Model
{

    protected $DBGroup = 'default';
    protected $table = 'tb_static_product_id_manager';
    protected $primaryKey = 'product_id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'create_dt',
        'update_dt',
        'product_short_name',
        'desc',
        'is_use',
        'status',
        'product_name_en',
        'product_name_kr',
        'product_group_id',
        'product_type_id',
        'provider_id',
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
