<?php namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;
use App\Util\ImageApiUtil;

class MenuBoardModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'menu_board';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
    	'a_id',
        'title',
        'contents',
        'create_dt',
    	'member_idx',
    	'nick_name',
    	'display'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function addBoard($memberIdx, $title, $a_id, $contents, $nickName):bool {       
        $sql = "insert into menu_board(member_idx, a_id, nick_name, title, contents) values(?,?,?,?,?)";
        $result = $this->db->query($sql,[$memberIdx,$a_id,$nickName,$title,$contents]);
        return true;
    }
    
    public function updateBoard($idx, $title, $contents):bool {
        $sql = "update menu_board set title = ?, contents = ? where idx = ?;";
        $result = $this->db->query($sql,[$title,$contents,$idx]);
        return true;
    }
    
    /* public function delBoard($idx):bool {
        $sql = "delete from menu_board where idx = $idx;";
        $result = $this->db->query($sql);
        return true;
    } */

	public function registComment($idx, $member_idx, $nickname, $comment):bool {
		$sql = "INSERT INTO menu_board_comment(idx, board_idx, member_idx, nick_name, comment, display, create_dt) ";
		$sql .= "VALUES(NULL, ?, ?, ?, ?, 1, NOW())";
		$result = $this->db->query($sql,[$idx, $member_idx, $nickname, $comment]);
		return true;
	}
	
	public function delBoard($idx):bool {
		$sql = "delete from menu_board where idx = ?;";
		$boardFileModel = new MenuBoardFileModel();
		try {
			$result = $this->db->query($sql,[$idx]);
			
			$sql = "select count(*) from menu_board_file where board_idx = ?;";
			
			$fileCount = $this->db->query($sql,[$idx])->getResult()[0];
			
			if($fileCount != 0) {
				
				$sql = "select * from menu_board_file where board_idx = ?;";
				
				$fileInfo = $this->db->query($sql,[$idx])->getResult()[0];
				
				ImageApiUtil::imageApiDelete($fileInfo->file_name, "/".config(App::class)->imagePathBoard."/");
			}
				
			$boardFileModel -> delBoardFile($idx);
			
			return true;
		}catch (\ReflectionException $e) {
			
			return false;
		}
	}
	
	public function addBoardAddImage($memberIdx, $title, $a_id, $contents, $nickName, $file_name, $file_path):bool {
		$insertData = [
				'member_idx' => $memberIdx,
				'a_id' => $a_id,
				'nick_name' => $nickName,
				'title' => $title,
				'contents' => $contents,
				'display' => 2
		];
		
		$boardFileModel = new MenuBoardFileModel();
		try {
			
			$idx = $this->insert($insertData);
			$boardFileModel -> addBoardFile($idx, $file_name, $file_path);
			return true;
		}catch (\ReflectionException $e) {
			
			return false;
		}
	}
}
