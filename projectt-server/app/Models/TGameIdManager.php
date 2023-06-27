<?php

namespace App\Models;

use CodeIgniter\Model;

class TGameIdManager extends Model
{

    protected $DBGroup = 'default';
    protected $table = 'tb_static_game_id_manager';
    protected $primaryKey = 'game_id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'game_id',
        'game_short_name',
        'desc',
        'create_dt',
        'update_dt',
        'is_use',
        'status',
        'gamet_name_en',
        'game_name_kr',
        'product_id',
        'product_type_id',
        'provider_id',
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
