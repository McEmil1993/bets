<?php

namespace App\Models;

use CodeIgniter\Model;

class LSportsLocationsModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'lsports_locations';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id',
        'name',
        'lang',
        'create_dt',
        'update_dt',
        'delete_dt',
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function isUseIdList() {
        $idList = [];
        foreach ($this->select('id')->where('is_use', 1)->find() as $item) {
            array_push($idList, $item['id']);
        }
        return $idList;
    }

   

}
