<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\MenuBoardModel;
use App\Models\MenuBoardFileModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;
use App\Models\MemberModel;
use App\Util\ImageApiUtil;
//use App\GamblePatch\BaseGmPt;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;
use App\GamblePatch\NobleGmPt;
use App\GamblePatch\BullsGmPt;

class BorderController extends BaseController {

    use ResponseTrait;

    protected $gmPt; // 겜블패치 

    public function __construct() {
        if ('K-Win' == config(App::class)->ServerName) {
            $this->gmPt = new KwinGmPt();
        } else if ('Gamble' == config(App::class)->ServerName) {
            $this->gmPt = new GambelGmPt();
        } else if ('BetGo' == config(App::class)->ServerName) {
            $this->gmPt = new BetGoGmPt();
        } else if ('CHOSUN' == config(App::class)->ServerName) {
            $this->gmPt = new ChoSunGmPt();
        } else if ('BETS' == config(App::class)->ServerName) {
            $this->gmPt = new BetsGmPt();
        } else if ('NOBLE' == config(App::class)->ServerName) {
            $this->gmPt = new NobleGmPt();
        } else if ('BULLS' == config(App::class)->ServerName) {
            $this->gmPt = new BullsGmPt();
        }
    }

    public function index() {
        $member_idx = session()->get('member_idx');

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $chkMobile = CodeUtil::rtn_mobile_chk();

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
            return redirect()->to(base_url("/$viewRoot/"));
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
        $boardModel = new MenuBoardModel();
        $sql = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_board' ";

        $result_game_config = $boardModel->db->query($sql)->getResult();

        if ('Y' == $result_game_config[0]->set_type_val && 9 != $member->getLevel()) {
            $url = base_url("/$viewRoot/");
            echo "<script>
            alert('게시판 기능 점검중입니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $sql = "SELECT COUNT(*) AS CNT FROM menu_board a WHERE a.display = 1"; /* a.member_idx > 0 AND  */
        $result = $boardModel->db->query($sql)->getResult();
        $totalCnt = $result[0]->CNT;

        $start_page = ($page - 1) * 10;
        $sql = "SELECT a.idx, a.a_id, a.title, a.contents, a.create_dt, a.display, b.nick_name, b.level, (SELECT COUNT(*) FROM menu_board_comment WHERE board_idx = a.idx) AS comment_count, DATEDIFF(a.create_dt, NOW()) AS is_new";
        $sql .= " FROM menu_board a ";
        $sql .= " LEFT JOIN member b ON a.a_id = b.id ";
        $sql .= " WHERE a.member_idx > 0 AND a.display = 1 ORDER BY a.idx DESC LIMIT ?, 10";
        $list = $boardModel->db->query($sql, [$start_page])->getResultArray();

        // 최신 공지 5개
        $sql = "SELECT a.idx, a.a_id, a.nick_name, a.title, a.contents, a.create_dt, a.display, DATEDIFF(a.create_dt, NOW()) AS is_new";
        if (0 < config(App::class)->BorderCount) {
            $sql .= " FROM menu_board a WHERE a.member_idx = 0 AND a.display = 1 ORDER BY a.idx DESC LIMIT 5";
        } else {
            $sql .= " FROM menu_board a WHERE a.member_idx = 0 AND a.display = 1 ORDER BY a.idx DESC LIMIT ?, 10";
        }
        $notice_list = $boardModel->db->query($sql, [$start_page])->getResultArray();

        return view("$viewRoot/boarder", [
            'notice' => $notice_list,
            'list' => $list,
            'totalCnt' => $totalCnt,
            'page' => $page,
            'num_per_page' => 10
        ]);
    }

    // 상세 & 수정
    public function update() {
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (false == session()->has('member_idx')) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        $idx = isset($_GET['idx']) ? $_GET['idx'] : 0;
        if (!is_int((int)$idx)) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        $type = isset($_GET['type']) ? $_GET['type'] : '';

        $boardModel = new MenuBoardModel();
        $list = $boardModel->where('idx', $idx)->find();

        $this->initMemberData(session(), session()->get('member_idx'));

        // 이미지 존재 여부 확인
        $sql = "select count(*) from menu_board_file where board_idx = ?";
        $imageInfo = null;
        $imageCount = $boardModel->db->query($sql, [$idx])->getResultArray()[0];
        if ($imageCount > 0) {
            // 이미지 출력
            $sql = "select file_path,file_name from menu_board_file where board_idx = ?";
            $imageInfo = $boardModel->db->query($sql, [$idx])->getResultArray()[0];
        }

        // 댓글 목록
        $sql = "SELECT a.idx, a.member_idx, a.nick_name, a.comment, a.create_dt FROM menu_board_comment AS a WHERE a.board_idx = ? ORDER BY a.idx";
        $comment_list = $boardModel->db->query($sql, [$idx])->getResultArray();

        $ret_page = $type == 'v' ? "$viewRoot/boarder_view" : "$viewRoot/boarder_edit";

        return view($ret_page, [
            'list' => $list,
            'idx' => $idx,
            'member_idx' => session()->get('member_idx'),
            'member_nickname' => session()->get('nick_name'),
            'imageInfo' => $imageInfo,
            'comment' => $comment_list,
            'type' => $type
        ]);
    }

    // 디비에 저장한다.
    public function updateDB() {
        $idx = isset($_POST['idx']) ? $_POST['idx'] : 0;
        $title = isset($_POST['title']) ? $_POST['title'] : NULL;
        $contents = isset($_POST['contents']) ? $_POST['contents'] : NULL;

        if ($title == NULL || $contents == NULL || $idx == 0 || !is_int((int)$idx)) {
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

        $boardModel = new MenuBoardModel();
        $result = $boardModel->updateBoard($idx, $title, $contents);

        $response = [
            'result_code' => 200,
            'messages' => '등록 성공',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    // 게시글 삭제
    public function deleteDB() {
        $idx = isset($_POST['idx']) ? $_POST['idx'] : 0;

        if ($idx == 0 || !is_int((int)$idx)) {
            return $this->fail('잘못된 게시글입니다.');
        }

        try {
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

            $boardModel = new MenuBoardModel();
            $boardModel->db->transStart();
            $result = $boardModel->delBoard($idx);

            $boardModel->db->transComplete();
        } catch (Exception $e) {
            $this->logger->error(':::::::::::::::  deleteDB error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  deleteDB query : ' . $boardModel->getLastQuery());
            $boardModel->db->transRollback();
            $response = [
                'result_code' => 400,
                'messages' => '삭제 오류 관리자 문의.',
                'data' => []
            ];
            return $this->respond($response, 400);
        }

        $response = [
            'result_code' => 200,
            'messages' => '삭제 성공',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    // 글 등록 페이지
    public function write() {
        $id = session()->get('id');
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if ($id == NULL) {
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

        if ($member->getStatus() == 11) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
          alert('가입승인 이 후 이용이 가능합니다.');
          window.location.href='$url';
          </script>";
            return;
        }

        $this->initMemberDataByMember(session(), $member);

        $this->gmPt->giveGmoneyBorder($memberModel, $member->getIdx(), $this->logger);

        return view("$viewRoot/boarder_write", [
            'member_idx' => session()->get('member_idx')
        ]);
    }

    // 글 등록 디비에 저장한다.
    public function writeDB() {
        $title = isset($_POST['title']) ? $_POST['title'] : NULL;
        $contents = isset($_POST['contents']) ? $_POST['contents'] : NULL;
        $member_idx = session()->get('member_idx');
        $nick_name = session()->get('nick_name');

        if ($title == NULL || $contents == NULL || !is_int((int)$member_idx)) {
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

        $boardModel = new MenuBoardModel();
        $result = $boardModel->addBoard($member_idx, $title, session()->get('id'), $contents, $nick_name);

        if ($result == false) {
            return $this->fail('등록 실패 관리자 문의.');
        }

        $this->gmPt->giveGmoneyBorder($memberModel, $member->getIdx(), $this->logger);

        $response = [
            'result_code' => 200,
            'messages' => '글이 등록 되었습니다.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    //바이너리 데이터 전송하려면 post방식 이여야함
    public function writeAddImage() {
        $id = session()->get('id');
        $binaryData = isset($_REQUEST['imgSrc']) ? $_REQUEST['imgSrc'] : NULL;
        $clickItemNum = isset($_REQUEST['clickItemNum']) ? $_REQUEST['clickItemNum'] : 1;

        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if ($id == NULL) {
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

        if ($member->getStatus() == 11) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
    		alert('가입승인 이 후 이용이 가능합니다.');
    		window.location.href='$url';
    		</script>";
            return;
        }

        $this->initMemberDataByMember(session(), $member);

        $this->gmPt->giveGmoneyBorder($memberModel, $member->getIdx(), $this->logger);

        return view("$viewRoot/borader_write_add_image", [
            'member_idx' => session()->get('member_idx')
            , 'binary_data' => $binaryData
            , 'clickItemNum' => $clickItemNum
        ]);
    }

    // 디비에 저장한다.
    public function writeAddImageDB() {
        $title = isset($_POST['title']) ? $_POST['title'] : NULL;
        $contents = isset($_POST['contents']) ? $_POST['contents'] : NULL;
        $binaryData = isset($_POST['binaryData']) ? base64_decode($_POST['binaryData']) : NULL;

        $imagedata = base64_decode($_REQUEST['binaryData']);
        $file_name = "board_betting_history_" . date("YmdHis") . ".png";
        $server_path = $_SERVER['DOCUMENT_ROOT'];
        $file_path = "/images/";
        $api_file_path = "/" . config(App::class)->imagePathBoard;
        $save_path = "/" . config(App::class)->imagePath . "/" . config(App::class)->imagePathBoard . "/";

        try {

            try {
                $uploadfile = $server_path . $file_path . $file_name;

                file_put_contents($uploadfile, $imagedata);

                ImageApiUtil::imageApiSend($file_name, $uploadfile, $api_file_path);
            } catch (Exception $e) {
                return "415";
            }

            $member_idx = session()->get('member_idx');
            $nick_name = session()->get('nick_name');

            if ($title == NULL || $contents == NULL) {
                return "411";
            }

            $memberModel = new MemberModel();
            $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
            if (!isset($member) || null == $member) {
                return "412";
            }

            if ($member->getStatus() == 2 || $member->getStatus() == 3) {
                return "413";
            }

            if ($member->getStatus() == 11) {
                return "414";
            }

            $boardModel = new MenuBoardModel();
            $boardModel->db->transStart();
            $result = $boardModel->addBoardAddImage($member_idx, $title, session()->get('id'), $contents, $nick_name, $file_name, $save_path);

            $this->gmPt->giveGmoneyBorder($memberModel, $member->getIdx(), $this->logger);

            $boardModel->db->transComplete();
        } catch (Exception $e) {
            $this->logger->error(':::::::::::::::  writeAddImageDB error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  writeAddImageDB query : ' . $boardModel->getLastQuery());
            $boardModel->db->transRollback();

            return "400";
        }

        if ($result == false) {
            return "400";
        }

        //return $this->respond($response, 200);
        return "200";
    }

    public function registComment() {
        $idx = isset($_POST['idx']) ? $_POST['idx'] : 0;
        $member_idx = isset($_POST['member_idx']) ? $_POST['member_idx'] : 0;
        $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : NULL;
        $comment = isset($_POST['comment']) ? $_POST['comment'] : NULL;

        if ($idx == 0 || $member_idx == 0 || $nickname == NULL || $comment == NULL || !is_int((int)$idx) || !is_int((int)$member_idx)) {
            return $this->fail('댓글을 입력해주세요.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);
        if (!isset($member) || null == $member) {
            return $this->fail('회원정보가 없는 계정입니다.');
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            return $this->fail('사용이 불가능 한 계정으로 관리자에게 문의바랍니다.');
        }

        if ($member->getStatus() == 11) {
            return $this->fail('가입승인 이 후 이용이 가능합니다.');
        }

        $boardModel = new MenuBoardModel();
        $result = $boardModel->registComment($idx, $member_idx, $nickname, $comment);

        $this->gmPt->giveGmoneyBorder($memberModel, $member->getIdx(), $this->logger);

        $response = [
            'result_code' => 200,
            'messages' => '등록 성공',
            'data' => []
        ];

        return $this->respond($response, 200);
    }

}
