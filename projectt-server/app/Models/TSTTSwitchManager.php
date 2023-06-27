<?php

namespace App\Models;

use CodeIgniter\Model;

class TSTTSwitchManager extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'tb_static_switch_manager';
    protected $primaryKey = 'provider_id,product_type_id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'is_use',
        'create_dt',
        'update_dt'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    public function getCanUseData($product_type_id){
        // 사용가능한 프로바이더를 얻어온다.
        $sql = "SELECT gm.product_group_name,pm.product_group_id,stm.provider_id,stm.product_type_id,gm.img_url 
                FROM mydb_bulls.tb_static_switch_manager as stm
                left join tb_static_product_id_manager as pm on pm.provider_id = stm.provider_id and  pm.product_type_id = stm.product_type_id
                left join tb_static_product_group_manager as gm on gm.product_group_id = pm.product_group_id
                where stm.product_type_id = ? and stm.is_use = 'ON' and pm.is_use = 'ON' ";

        return $this->db->query($sql,[$product_type_id])->getResultArray();
    }
}
