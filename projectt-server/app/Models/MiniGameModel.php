<?php namespace App\Models;

use CodeIgniter\Model;

class MiniGameModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'mini_game';
    protected $primaryKey = 'game,id';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'game',
        'id',
        'oid',
        'start_dt',
        'end_dt',
        'league',
        'league',
        'home',
        'away',
        '1x2',
        'ou',
        'num1',
        'num2',
        'num3',
        'num4',
        'num5',
        'num6',
        'oe',
        'line',
        'start'
    ];
    
    // 마감시간 구하기
    /*public function getEndTime($bet_type){
        $sql = "SELECT end_time FROM base_rule where bet_type = 6 and start_dt > '$search_date' order by start_dt asc limit 4;";
        $gameList = $MiniGameModel->db->query($sql)->getResult();
    }*/

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}