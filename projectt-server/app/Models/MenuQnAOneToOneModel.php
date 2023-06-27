<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class MenuQnAOneToOneModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'menu_qna_1_to_1';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'a_id',
        'title',
        'type',
        'contents',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function addQ($memberIdx, $contents):bool {
        try {
            $this->insert([
                'member_idx' => $memberIdx,
                'type' => 'Q',
                'contents' => $contents
            ]);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }

    }

}