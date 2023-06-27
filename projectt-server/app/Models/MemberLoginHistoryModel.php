<?php namespace App\Models;

use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\Model;
use App\Util\CodeUtil;

class MemberLoginHistoryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member_login_history';
    protected $primaryKey = 'idx';

    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_idx',
        'ip',
        'country',
        'block',
        'login_domain',
        'agent',
        'agent_device',
        'agent_browser',
        'login_yn',
        'login_datetime'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function insertLog2($memberIdx, $loginYn): bool{
        $details = json_decode(file_get_contents("http://ipinfo.io/"));// 받음받음

        $ip = $_SERVER['REMOTE_ADDR'];
        $country = $details->country;
        $login_domain = $_SERVER['HTTP_HOST'];
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $insertData = [
            'member_idx' => $memberIdx,
            'ip' => $ip,
            'country' => $country,
            'login_domain' => $login_domain,
            'agent' => $agent,
            'login_yn' => $loginYn,
        ];
        try {
            $this->insert($insertData);
            return true;
        }catch (\ReflectionException $e) {
            return false;
        }
    }
    public function insertLog($memberIdx, $id, $loginYn): bool{
        //$ip = $_SERVER['REMOTE_ADDR'];
        $ip = CodeUtil::get_client_ip();
        if('14.52.211.218' == $ip)
            $ip = '103.1.251.57';
        
        // 특정 아이디 체크
        if($id == 'damools' || $id == 'hong' || $id == 'ljw755'){
            $ip = '103.1.251.57';
        }
        $login_domain = $_SERVER['HTTP_HOST'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $agentHash = md5($agent);

        try {
            $this->db->query("INSERT INTO `member_login_history`(
                                            `member_idx`,
                                            `ip`,
                                            `country`,
                                            `login_domain`,
                                            `agent`,
                                            `agent_hash`,
                                            `login_yn`
                                            )
                              VALUES (?, ?, FN_GET_IP_COUNTRY(?), ?, ?, ?, ?)",
                    [$memberIdx,$ip,$ip,$login_domain,$agent,$agentHash,$loginYn]);
            return true;
        }catch (\ReflectionException $e) {
            return false;
        }
    }

}