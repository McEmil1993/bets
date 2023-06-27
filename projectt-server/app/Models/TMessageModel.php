<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class TMessageModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 't_message';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'msg_idx',
        'member_idx',
        'read_yn',
        'u_ip',
        'reg_time',
        'read_time'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;


}