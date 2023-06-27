<?php namespace App\Models;

use CodeIgniter\Model;

class NotifySettingModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'notify_setting';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idx',
        'location',
        'content',
        'file_location',
        'create_dt',
        'update_dt',
        'delete_dt',
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getContentRow($idx) 
    {
        $getContent = (object) $this->where('idx', $idx)->first();
        if ($getContent) {
            return $getContent->content;
        }
        return false;
    }
  
}