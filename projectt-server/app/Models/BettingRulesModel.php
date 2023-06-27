<?php namespace App\Models;

use CodeIgniter\Model;

class BettingRulesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'base_rule';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'id',
        'name',
        'result_process',
        'end_time',
        'max_dividend',
        'betting_regulation'
    ];

    //1: 입금 전 (신청) 2: 입금 확인 (대기) 3: 충전 완료 (완료) 999: 오류

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}