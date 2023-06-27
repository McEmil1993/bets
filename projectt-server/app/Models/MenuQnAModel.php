<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class MenuQnAModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'menu_qna';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        
        'member_idx',
        'a_id',
        'title',
        'contents',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function addQ($memberIdx, $title, $contents):bool {
        try {
            $this->insert([
                'member_idx' => $memberIdx,
                'title' => $title,
                'contents' => $contents
            ]);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }

    }
    
    // 하나의 게시글 가져온다.
    public function selectQOneToOne($idx):bool {
        try {
            return $this->select('idx, member_idx, title, contents')
            ->where('idx', $idx)
            ->find();
        } catch (\ReflectionException $e) {
            return null;
        }
    }
    
    // 하나의 게시글 삭제한다.
    public function deleteQ($idx):bool {
        try {
            return $this->deleteQ
            ->where('idx', $idx)
            ->find();
            
            //return true;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

}