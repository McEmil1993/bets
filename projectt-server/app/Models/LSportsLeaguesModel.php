<?php namespace App\Models;

use CodeIgniter\Model;

class LSportsLeaguesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'lsports_leagues';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id',
        'name',
        'location_id',
        'sport_id',
        'season',
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
        foreach ($this->select('id')->where('is_use', 1)->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }

    public function isUseIdTypeList($bet_type){
        $idList = [];
        foreach ($this->select('id')->where('bet_type', $bet_type)->where('is_use', 1)->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }
    
    public function isNotUseIdList(){
        $idList = [];
        foreach ($this->select('id')->whereIn('is_use', [0,2])->find() as $item){
            array_push($idList, $item['id']);
        }
        return $idList;
    }
}