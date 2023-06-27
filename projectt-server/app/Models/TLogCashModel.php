<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class TLogCashModel extends Model {

    protected $DBGroup = 'default';
    protected $table = 't_log_cash';
    protected $primaryKey = 'idx';
    protected $returnType = 'array';
//    protected $returnType = 'App\Entities\Member';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'u_key',
        'member_idx',
        'ac_code',
        'ac_idx',
        'r_money',
        'be_r_money',
        'af_r_money',
        'point',
        'be_point',
        'af_point',
        'm_kind',
        'coment',
        'u_ip',
        'a_country',
        'a_id',
        'reg_time',
        'holdem_user_money'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /*  코드 관련 설명
        u_key = 테이블 연관 키
        ac_code = 1:충전,2:환전,3:베팅,4:베팅취소,5:포인트전환(추가),6:포인트차감,7:베팅결과처리,8:이벤트충전,9:이벤트차감,
                  101:충전요청,102:환전요청,103:계좌조회,111:충전요청취소,112:환전요청취소,113:충전취소,114:환전취소,121:관리자충전,122:관리자회수,998:데이터복구,999:기타
     *            1001 : 홀덤시작, 1002 : 홀덤종료
        ac_idx = 관련 게시물 idx
        r_money = 충/환전 등 처리할 금액
        be_r_money = 처리 이전 금액
        af_r_money = 처리 이후 금액
        point = 충/환전 등 처리할 금액
        be_r_point = 처리 이전 금액
        af_r_point = 처리 이후 금액
        m_kind = R:요청, P:더하기, M:빼기
    */

    public function insertCashLog($uKey, $acCode, $acIdx, $rMoney, $beRMoney, $kind, $commend = ''): bool{
        $ip = CodeUtil::get_client_ip();
        $a_country = $this->db->query('SELECT FN_GET_IP_COUNTRY(?)', [$ip])->getResultArray();
        $insertData = [
            'u_key' => $uKey,
            'member_idx' => $_SESSION['member_idx'],
            'ac_code' => $acCode,
            'ac_idx' => $acIdx,
            'r_money' => abs($rMoney),
            'be_r_money' => $beRMoney,
            'm_kind' => $kind,
            'u_ip' => $ip,
            'a_country' => $a_country[0],
            'coment' => CodeUtil::TLogACCodeToStr($acCode) . ' ' . $commend,
        ];

        if ('R' == $kind) { // 요청인 경우 여기
            //$insertData['m_kind'] = 'R';
            $insertData['af_r_money'] = $beRMoney;
        } else {
            //$insertData['m_kind'] = $rMoney >= 0 ? 'P' : 'M';
            $insertData['af_r_money'] = $beRMoney + $rMoney;
        }



        try {
            $this->insert($insertData);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    public function insertCashLog_mem_idx($uKey, $member_idx, $acCode, $acIdx, $rMoney, $beRMoney, $kind, $commend = ''): bool {
        /*  $afMoney =   $rMoney + $beRMoney;
          $p_data['sql'] = "insert into  t_log_cash ";
          $p_data['sql'] .= " (member_idx, ac_code, ac_idx, r_money, be_r_money, af_r_money, m_kind, coment, a_id) ";
          $p_data['sql'] .= " values($member_idx, $acCode, 0, $rMoney,$beRMoney,$afMoney,'P','$commend','SYSTEM')";
          $this->db->query($p_data['sql']);
          return true; */
        $insertData = [
            'u_key' => $uKey,
            'member_idx' => $member_idx,
            'ac_code' => $acCode,
            'ac_idx' => $acIdx,
            'r_money' => abs($rMoney),
            'be_r_money' => $beRMoney,
            'm_kind' => $kind,
            'u_ip' => $_SERVER['REMOTE_ADDR'],
            'coment' => CodeUtil::TLogACCodeToStr($acCode) . ' ' . $commend,
        ];

        /* if ($acCode == 101 || $acCode == 102 ||$acCode == 103 || $acCode == 111 || $acCode == 112) { // 요청인 경우 여기
          $insertData['m_kind'] = 'R';
          $insertData['af_r_money'] = $beRMoney;
          }else {
          $insertData['m_kind'] = $rMoney >= 0 ? 'P' : 'M';
          $insertData['af_r_money'] = $beRMoney + $rMoney;
          } */

        if ('R' == $kind) { // 요청인 경우 여기
            //$insertData['m_kind'] = 'R';
            $insertData['af_r_money'] = $beRMoney;
        } else {
            //$insertData['m_kind'] = $rMoney >= 0 ? 'P' : 'M';
            $insertData['af_r_money'] = $beRMoney + $rMoney;
        }

        try {
            $this->insert($insertData);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    // 포인트 추가 버전
    public function insertCashLog_2($uKey, $acCode, $acIdx, $rMoney, $beRMoney, $point, $be_point, $af_point, $kind, $commend = ''): bool {
        $insertData = [
            'u_key' => $uKey,
            'member_idx' => $_SESSION['member_idx'],
            'ac_code' => $acCode,
            'ac_idx' => $acIdx,
            'r_money' => abs($rMoney),
            'be_r_money' => $beRMoney,
            'm_kind' => $kind,
            'u_ip' => $_SERVER['REMOTE_ADDR'],
            'point' => $point,
            'be_point' => $be_point,
            'af_point' => $af_point,
            'coment' => CodeUtil::TLogACCodeToStr($acCode) . ' ' . $commend,
        ];

        if ('R' == $kind) { // 요청인 경우 여기
            //$insertData['m_kind'] = 'R';
            $insertData['af_r_money'] = $beRMoney;
        } else {
            //$insertData['m_kind'] = $rMoney >= 0 ? 'P' : 'M';
            $insertData['af_r_money'] = $beRMoney + $rMoney;
        }

        try {
            $this->insert($insertData);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    // 포인트 추가(총판정산)-멤버번호 추가
    public function insertCashLog_3($uKey, $member_idx, $acCode, $acIdx, $rMoney, $beRMoney, $point, $be_point, $af_point, $kind, $commend = ''): bool {
        $insertData = [
            'u_key' => $uKey,
            'member_idx' => $member_idx,
            'ac_code' => $acCode,
            'ac_idx' => $acIdx,
            'r_money' => abs($rMoney),
            'be_r_money' => $beRMoney,
            'm_kind' => $kind,
            'u_ip' => $_SERVER['REMOTE_ADDR'],
            'point' => $point,
            'be_point' => $be_point,
            'af_point' => $af_point,
            'coment' => CodeUtil::TLogACCodeToStr($acCode) . ' ' . $commend,
        ];

        if ('R' == $kind) { // 요청인 경우 여기
            //$insertData['m_kind'] = 'R';
            $insertData['af_r_money'] = $beRMoney;
        } else {
            //$insertData['m_kind'] = $rMoney >= 0 ? 'P' : 'M';
            $insertData['af_r_money'] = $beRMoney + $rMoney;
        }

        try {
            $this->insert($insertData);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }
    }
    
    // holdem only use
    public function insertCashLog_mem_idx_holdem($uKey,$member_idx, $acCode, $acIdx, $rMoney, $beRMoney, $regTime, $holdem_user_money, $commend = ''): bool{
        $insertData = [
            'u_key' => $uKey,
            'member_idx' => $member_idx,
            'ac_code' => $acCode,
            'ac_idx' => $acIdx,
            'r_money' => abs($rMoney),
            'be_r_money' => $beRMoney,
            'm_kind' => $rMoney >= 0 ? 'P' : 'M',
            'u_ip' => $_SERVER['REMOTE_ADDR'],
            'coment' => CodeUtil::TLogACCodeToStr($acCode).' '.$commend,
            'reg_time' => $regTime,
            'holdem_user_money' => $holdem_user_money
        ];
 
        $insertData['m_kind'] = $rMoney >= 0 ? 'P' : 'M';
        $insertData['af_r_money'] = $beRMoney + $rMoney;

        try {
            $this->insert($insertData);
            return true;
        } catch (\ReflectionException $e) {
            return false;
        }   
    }
}