<?php

namespace App\Models;

use App\Entities\Member;
use CodeIgniter\Model;

class MemberModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 'member';
    protected $primaryKey = 'idx';
//    protected $returnType = 'array';
    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id',
        'password',
        'nick_name',
        'u_business',
        'money',
        'point',
        'betting_p',
        'call',
        'is_recommend',
        'status',
        'level',
        'auto_level',
        'last_login',
        'session_key',
        'MICRO',
        'AG',
        'recommend_code',
        'recommend_member',
        'account_number',
        'account_name',
        'account_bank',
        'exchange_password',
        'is_monitor',
        'is_monitor_charge',
        'is_monitor_security',
        'is_monitor_bet',
        'dis_id',
        'dis_line_id',
        'reg_time',
        //'mod_time',
        //'leave_time',
        //'admin_memo',
        'is_exchange',
        'reg_first_charge',
        'charge_first_per',
        'is_coin_guid',
        'coin_password',
        'is_betting_slip',
        'birth',
        'mobile_carrier',
        'g_money',
        'keep_login_access_token'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getMemberWhereId(string $memberId): ?Member {
        $result = $this->asArray()->where('id', $memberId)->first();
        if ($result != null) {
            $member = new Member();
            return $member->fill($result);
        } else {
            return null;
        }
    }

    public function getMemberWhereCall(string $call): ?Member {
        $result = $this->asArray()->where('call', $call)->first();
        if ($result != null) {
            $member = new Member();
            return $member->fill($result);
        } else {
            return null;
        }
    }

    public function getMemberWhereIdx(int $memberIdx): ?Member {
        $result = $this->asArray()->where('idx', $memberIdx)->first();
        if ($result != null) {
            $member = new Member();
            return $member->fill($result);
        } else {
            return null;
        }
    }

    public function getMemberWhereKeepLoginAccessToken($keep_login_access_token): ?Member {
        $result = $this->asArray()->where('keep_login_access_token', $keep_login_access_token)->first();
        if ($result != null) {
            $member = new Member();
            return $member->fill($result);
        } else {
            return null;
        }
    }

    public function getMemberWhereNickName(string $nickName): ?Member {
        $result = $this->asArray()->where('nick_name', $nickName)->first();
        if ($result != null) {
            $member = new Member();
            return $member->fill($result);
        } else {
            return null;
        }
    }

       public function memberChangeMoney($memberIdx, $cMoney) {
        $sql = "update member set money = money + ? where idx = ?";
        $this->db->query($sql,[$cMoney,$memberIdx]);
        //return $this->set('money', $cMoney)->where('idx', $memberIdx)->update();
    }

    public function memberChangeIsExchange($memberIdx) {
        return $this->set('is_exchange', 1)->where('idx', $memberIdx)->update();
    }

    public function log_lose_bet_bonus_point($member_idx, $bet_idx, $set_money) {// 낙첨시 주는 포인트 lose_self_per
        $get_config_str = "'lose_self_per'";
        $p_data['sql'] = "select u_level, set_type, set_type_val from t_game_config ";
        $p_data['sql'] .= " where set_type in ($get_config_str) ";
        $retData = $this->db->query($p_data['sql'])->getResultArray();

        $str_set_type = '';
        $ch_point_lose_self_per = 0;
        $ch_point_lose_recomm_per = 0;
        $a_comment_lose_self_per = '';
        $a_comment_lose_recomm_per = '';
        foreach ($retData as $row) {
            $db_level = $row['u_level'];
            $str_set_type = $row['set_type'];
            switch ($row['set_type']) {
                case 'lose_self_per':
                    $db_config[$db_level]['lose_self_per'] = $row['set_type_val'];
                    break;
            }
        }

        $p_data['sql'] = "select * from member where idx = ?";
        $result_member_data = $this->db->query($p_data['sql'],[$member_idx])->getResultArray();

        $cash_use_kind = strtoupper('P');
        $ac_code = 11; // 낙첨 포인트 지급 
        $u_level = $result_member_data[0]['level'];
        $now_point = true === isset($result_member_data[0]['point']) ? $result_member_data[0]['point'] : 0;

        $ch_point_lose_self_per = ($set_money) * $db_config[$u_level]['lose_self_per'] / 100;
        $a_comment_lose_self_per = '낙첨 롤링 포인트 지급 ';
        $af_point = $now_point + $ch_point_lose_self_per;

        $p_data['sql'] = "update member set point = point + ? where idx= ? ";
        $this->db->query($p_data['sql'],[$ch_point_lose_self_per,$member_idx]);

        $p_data['sql'] = "insert into  t_log_cash ";
        $p_data['sql'] .= " (member_idx, ac_code, ac_idx, point, be_point, af_point, m_kind, coment, a_id) ";
        $p_data['sql'] .= " values(?, ?, ?, ? ,?, ?, ?,?,'SYSTEM')";
        
        $this->db->query($p_data['sql'],[$member_idx, $ac_code, $bet_idx,$ch_point_lose_self_per,$now_point, $af_point,$cash_use_kind,$a_comment_lose_self_per]);

        return array($ch_point_lose_self_per);
    }

    // 회원가입 축하쪽지 발송
    public function sendJoinMessage($member_id) {

        $msg_key = date("YmdHis") . substr(microtime(), 2, 6) . rand(10000, 99999);

        // 가입메세지
        $sql = "SELECT a_id, title, contents FROM join_message";
        $sql .= " WHERE a_id <> '';";
        $joinMessage = $this->db->query($sql)->getResultArray()[0];

        $title = $joinMessage['title'];
        $contents = $joinMessage['contents'];
        $a_id = $joinMessage['a_id'];

        // 유저구하기
        $sql = "SELECT idx FROM member ";
        $sql .= " WHERE id=?";
        $userInfo = $this->db->query($sql,[$member_id])->getResultArray()[0];

        //$in_data = "'" . $msg_key . "','" . $title . "','" . $contents . "','$a_id'";
        $sql = "INSERT INTO t_message_list (idx_key, title, content, a_id) VALUES(?,?,?,?)";
        $this->db->query($sql,[$msg_key,$title,$contents,$a_id]);

        $sel_sql = "SELECT idx FROM t_message_list ";
        $sel_sql .= " WHERE idx_key=?";
        $result = $this->db->query($sel_sql,[$msg_key])->getResultArray()[0];

        //$in_data = $result['idx'] . "," . $userInfo['idx'];
        $sql = "INSERT INTO t_message (msg_idx, member_idx) VALUES(?,?)";
        $p_data['sql'] = "insert into  t_log_cash ";
        $this->db->query($sql,[$result['idx'],$userInfo['idx']]);
    }

    // 최종 배팅시간 업데이트
    public function updateBettingDate($memberIdx) {
        $sql = "INSERT INTO member_extend (member_idx,betting_dt) VALUES ($memberIdx, now()) "
                . "ON DUPLICATE KEY UPDATE betting_dt = now()";
        $this->db->query($sql);
    }
    public function filterSanitize($postsData){
        $post = array();
        foreach($postsData as $row=>$a){
            $post[$row] = filter_var($a, FILTER_SANITIZE_STRING);
        }

        return $post;
    }   
}

//  session()->set('is_exchange', $findMember->getIsExchange());
//  session()->set('reg_first_charge', $findMember->getRegFirstCharge());
//  session()->set('charge_first_per', $findMember->getChargeFirstPer());