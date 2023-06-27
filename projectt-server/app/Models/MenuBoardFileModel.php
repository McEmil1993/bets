<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class MenuBoardFileModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'menu_board_file';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'board_idx',
        'file_name',
        'file_path',
        'create_dt'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function addBoardFile($board_idx, $file_name, $file_path):bool {       
    	$sql = "insert into menu_board_file(board_idx, file_name, file_path) values(?, ?, ?)";
        $result = $this->db->query($sql,[$board_idx,$file_name,$file_path]);
        return true;
    }
    
    public function delBoardFile($idx):bool {
    	$sql = "delete from menu_board_file where board_idx = ?;";
    	$result = $this->db->query($sql,[$idx]);
    	return true;
    }
}
