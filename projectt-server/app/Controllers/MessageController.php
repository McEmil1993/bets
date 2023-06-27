<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\GameModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyChargeHistoryModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Models\TLogCashModel;
use App\Models\TMessageModel;
use Cassandra\Date;
use CodeIgniter\API\ResponseTrait;

class MessageController extends BaseController {

    use ResponseTrait;

    public function messageRead() {
        $messageIdx = isset($_POST['message_idx']) ? $_POST['message_idx'] : NULL;
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);

        if ($member == null || $messageIdx == NULL) {
            return $this->fail('조회되는 회원 또는 메세지 idx 가 없습니다.');
        }

        $mes_cnt = session()->get('tm_unread_cnt');
        if ($mes_cnt > 0)
            $mes_cnt -= 1;
        session()->set('tm_unread_cnt', $mes_cnt);

        $tmModel = new TMessageModel();
        $tmUpdate = $tmModel
                ->set('read_time', date("Y-m-d H:i:s", strtotime("Now")))
                ->set('read_yn', 'Y')
                ->where('idx', $messageIdx)
                ->update();

        $response = [
            'result_code' => 200,
            'messages' => '수정 성공',
            'data' => [
                'read' => true,
                'time' => date("Y-m-d H:i:s")
            ]
        ];
        return $this->respond($response, 200);
    }

    // 헤더에서 메세지 체크를 위해 호출
    public function locationMessageCheck() {
    	$member_idx = session()->get('member_idx');
    	
        if (!isset($member_idx) || $member_idx == NULL) {
            //return $this->fail('조회되는 회원 또는 메세지 idx 가 없습니다.');
            return $this->respond($response, 200);
        }

        $tMessageModel = new TMessageModel();
        $sql = "select count(idx) as cnt from t_message
            where member_idx = ? and read_yn = 'N' ";

        $result_sql = $tMessageModel->db->query($sql,[$member_idx])->getResult();

        $bf_tm_unread_cnt = session()->get('tm_unread_cnt');

        session()->set('tm_unread_cnt', $result_sql[0]->cnt);

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'bf_tm_unread_cnt' => $bf_tm_unread_cnt,
                'tm_unread_cnt' => $result_sql[0]->cnt
            ]
        ];
        return $this->respond($response, 200);
    }

  

    // ajax 메세지 전체 갱신용도
    public function getAllMessage() {
        $member_idx = session()->get('member_idx');

        if (!isset($member_idx) || $member_idx == NULL) {
            return $this->fail('조회되는 회원 또는 메세지 idx 가 없습니다.');
        }
        $tMessageModel = new TMessageModel();
        $messages = $tMessageModel
                ->select(['t_message.*', 'tml.title', 'tml.content', 'tml.a_id'])
                ->join('t_message_list tml', 't_message.msg_idx = tml.idx', 'left')
                ->where('t_message.member_idx', $member_idx)
                ->orderBy('t_message.idx', 'desc')
                ->find();

        $response = [
            'result_code' => 200,
            'messages' => '조회 성공',
            'data' => [
                'messages' => $messages
            ]
        ];
        return $this->respond($response, 200);
    }

}
