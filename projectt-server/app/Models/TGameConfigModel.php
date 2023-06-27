<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class TGameConfigModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 't_game_config';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getMemberLevelConfig($type,$level){
        if(1 == $type){
             return $this->where('u_level', $level)->whereIn('set_type',['pre_min_money','pre_max_money','pre_limit_money'])->find();
          
        } else{
             return $this->where('u_level', $level)->whereIn('set_type',['real_min_money','real_max_money','real_limit_money'])->find();
        }
      
    }
    
    // 유저레벨에 맞는 최대 배팅금액을 가져온다.
    public function getMemberMaxBetMoney($type, $level){
        if($type == 2)
            return $this->where('u_level', $level)->where('set_type', 'real_max_money')->find();
        else
            return $this->where('u_level', $level)->where('set_type', 'pre_max_money')->find();
    }

    public function getMemberConfigLevel($member_level) {
    $sql = "SELECT set_type, set_type_val 
            FROM t_game_config 
            WHERE set_type IN (
                'charge_first_per', 
                'charge_per',
                'charge_max_money',
                'charge_money')
            AND u_level = ?
            OR set_type in ('reg_first_charge',
            'event_charge_status',
                'event_charge_start',
                'event_charge_end');";

        return $this->db->query($sql,[$member_level])->getResultArray();
    }

    public function getSetType($set_type) {
        $sql = "SELECT * FROM t_game_config WHERE set_type = ?";
        $res = $this->db->query($sql,[$set_type])->getResult();
        $str = '';
        if (is_array($res) || is_object($res))
        {
            foreach($res as $i){
                $str = $i->set_type_val;
            }
    
        }
        return $str;
        
    }
}