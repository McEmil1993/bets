<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class TMessageListModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 't_message_list';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx_key',
        'title',
        'content',
        'a_id',
        'a_view_nick',
        'w_type',
        'reg_time'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;


}