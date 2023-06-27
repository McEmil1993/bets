<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\MenuQnAModel;
use App\Models\MenuQnAOneToOneModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;
use App\Models\MemberModel;

class CustomerServiceController extends BaseController {

    use ResponseTrait;

    public function index() {
        $member_idx = session()->get('member_idx');
        
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (false == session()->has('member_idx') || !isset($member_idx)) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        }   

        if (0 < session()->get('tm_unread_cnt')) {
        	$url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $id = session()->get('id');
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        if ($id == NULL || !is_int((int)$page)) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('회원정보가 없는 계정입니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }
     

        $this->initMemberDataByMember(session(), $member);
     
        $boardModel = new MenuQnAModel();
     
        $list_count = $boardModel
                ->select(['menu_qna.idx'])
                ->join('member as m', 'menu_qna.member_idx = m.idx', 'left')
                ->where('menu_qna.member_idx', session()->get('member_idx'))
                ->where('menu_qna.is_view', 'Y')
                ->find();
        $totalCnt = count($list_count);

        $list = $boardModel
                ->select(['menu_qna.*', 'm.nick_name'])
                ->join('member as m', 'menu_qna.member_idx = m.idx', 'left')
                ->where('menu_qna.member_idx', session()->get('member_idx'))
                ->where('menu_qna.is_view', 'Y')
                ->limit(10, ($page - 1) * 10)
                ->orderBy('idx', 'desc')
                ->find();


        $this->logger->debug($boardModel->getLastQuery());
        return view("$viewRoot/customer_service", [
            'list' => $list,
            'totalCnt' => $totalCnt,
            'num_per_page' => 10,
            'page' => $page
        ]);
    }

    public function addQnA() {
        $title = isset($_POST['title']) ? $_POST['title'] : NULL;
        $contents = isset($_POST['contents']) ? $_POST['contents'] : NULL;

        if ($title == NULL || $contents == NULL) {
            return $this->failRespond('제목 또는 내용을 입력해 주세요.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            return $this->failRespond('회원정보가 없는 계정입니다.');
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            return $this->failRespond('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($member->getStatus() == 11) {
            return $this->failRespond('가입승인 이 후 이용이 가능합니다.');
        }

        $menuQnAModel = new MenuQnAModel();
        
        $str_sql_qna = "SELECT * FROM menu_qna WHERE member_idx = ? order by idx desc limit 1;";
        $result = $menuQnAModel->db->query($str_sql_qna, [session()->get('member_idx')])->getResultArray();
        
        // 고객센터는 문의 후 90초 동안 재문의 불가
        if(count($result) > 0){
            $checkTime = date("Y-m-d H:i:s", strtotime($result[0]['create_dt'] . "+ 90 seconds"));
            if ($checkTime > date("Y-m-d H:i:s")) {
                return $this->failRespond("문의 후 재 문의는 1분 30초 후 가능합니다.");
            }
        }
        
        $result = $menuQnAModel->addQ(session()->get('member_idx'), $title, $contents);
                       
        if ($result == false) {
            return $this->failRespond('등록 실패 관리자 문의.');
        }
                                     
        $response = [
            'result_code' => 200,
            'messages' => '질문 성공'
        ];
        return $this->respond($response);
    }

    public function addQnAOneToOne() {
        $contents = isset($_POST['contents']) ? $_POST['contents'] : NULL;

        if ($contents == NULL) {
            return $this->failRespond('제목 또는 내용을 입력해 주세요.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            return $this->failRespond('회원정보가 없는 계정입니다.');
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            return $this->failRespond('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($member->getStatus() == 11) {
            return $this->failRespond('가입승인 이 후 이용이 가능합니다.');
        }

        $menuQnAOneToOneModel = new MenuQnAOneToOneModel();
        $result = $menuQnAOneToOneModel->addQ($member->getIdx(), $contents);

        if ($result == false) {
            return $this->fail('등록 실패 관리자 문의.');
        }

        $response = [
            'result_code' => 200,
            'messages' => '질문 성공'
        ];
        return $this->respond($response);
    }

    public function selectOne() {
        $idx = isset($_POST['idx']) ? $_POST['idx'] : NULL;

        if ($idx == NULL || !is_int((int)$idx)) {
            return $this->fail('제목 또는 내용을 입력해 주세요.');
        }

        $menuQnAModel = new MenuQnAModel();
        $result = $menuQnAModel
                ->select('idx, member_idx, title, contents')
                ->where('idx', $idx)
                ->find();

        $response = [
            'result_code' => 200,
            'messages' => '질문 성공',
            'data' => $result[0]
        ];
        return $this->respond($response, 200);
    }

    public function deleteQnA() {
        $idx = isset($_POST['idx']) ? $_POST['idx'] : NULL;
        $member_idx = session()->get('member_idx');

        if ($idx == NULL || !is_int((int)$idx)) {
            return $this->fail('제목 또는 내용을 입력해 주세요.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            return $this->fail('회원정보가 없는 계정입니다.');
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($member->getStatus() == 11) {
            return $this->fail('가입승인 이 후 이용이 가능합니다.');
        }

        $menuQnAModel = new MenuQnAModel();
        $result = $menuQnAModel
                ->select('idx, member_idx, title, contents')
                ->where('idx', $idx)
                ->find();

        if ($member_idx != $result[0]['member_idx']) {
            $response = [
                'result_code' => 300,
                'messages' => '본인의 게시글이 아닙니다.',
                'data' => ''
            ];

            return $this->respond($response, 200);
        }
        
        $menuQnAModel->db->query("update menu_qna set is_view ='N' where idx = ?",[$idx]);
      
        $this->logger->debug("******************** deleteQnA ******************** " . $idx);

        $response = [
            'result_code' => 200,
            'messages' => '삭제 성공',
            'data' => ''
        ];
        return $this->respond($response, 200);
    }

}
