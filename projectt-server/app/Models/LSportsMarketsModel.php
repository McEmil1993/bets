<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsMarketsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_markets';
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

    public function isUseIdList(){
        $idList = [];
        foreach ($this->select('id')->where('is_delete', 0)->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }

    // [ 1, 2, 3, 7, 52, 101, 102, 16, 28, 220, 221, 226, 342, 1050, 1328, 1332, ]
    public function isSportsIdList(){
        $idList = [];
        foreach ($this->select('id')->where('bet_group', 1)->where('is_delete', 0)->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }

    public function isRealTimeIdList(){
        $idList = [];
        foreach ($this->select('id')->where('bet_group', 2)->where('is_delete', 0)->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }

}