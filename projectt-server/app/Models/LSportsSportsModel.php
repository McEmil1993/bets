<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsSportsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_sports';
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

    public function isUseIdList($bet_type){
        $idList = [];
        foreach ($this->select('id')->where('is_use', 1)->where('bet_type', $bet_type)->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }
    
    // 데이터 배열로 처리
    public function isUseIdListArray($bet_type){
        $idList = [];
        foreach ($this->select(['id', 'name'])->where('is_use', 1)->where('bet_type', $bet_type)->orderBy('display_order', 'asc')->find() as $item){
            array_push($idList, array('id'=>$item['id'], 'name'=>$item['name']));
        }
        return $idList;
    }

}