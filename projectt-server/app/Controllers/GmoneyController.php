<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Util\CodeUtil;
use CodeIgniter\API\ResponseTrait;
//use App\GamblePatch\BaseGmPt;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;

class GmoneyController extends BaseController {

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
        }
    }

    public function index() {

        $id = session()->get('id');
        if ($id == NULL) {
            return redirect()->to(base_url("/$viewRoot/"));
        }

        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
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

        // 나의 아이템 리스트 가져오기
        // 상점 정보 가져오기 

        $sql = "SELECT
				a.create_dt
				,a.status
				,a.item_value
				,b.name
			FROM
				member_item a
				,item b
			WHERE
				1=1
			AND 
				a.item_id = b.id 
			AND 	
				member_idx = ? 
			ORDER BY idx DESC";
        $resultMyItemlist = $memberModel->db->query($sql, [$member->getIdx()])->getResultArray();

        $sql = "SELECT * FROM item WHERE is_open = 'Y'";
        $resultShopItemlist = $memberModel->db->query($sql)->getResultArray();

        return view("$viewRoot/gamble_shop", [
            'myItemList' => $resultMyItemlist,
            'shopItemlist' => $resultShopItemlist,
        ]);
    }

    public function getMyItemList() {
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $memberModel = new MemberModel();

        $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
        if (!isset($member) || null == $member) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('회원정보가 없는 계정입니다.');
            window.location.href='$url';
            </script>";
            return ;
        }

        $sql = "UPDATE member_item SET status = 3 WHERE member_idx = ? AND status = 0 AND DATE_ADD(create_dt, INTERVAL 30 DAY) < NOW()";
        $memberModel->db->query($sql, [$member->getIdx()])->getResult();
        
        $sql = "SELECT * FROM member_item WHERE member_idx = ? AND status = 0";
        $resultMyItemlist = $memberModel->db->query($sql, [$member->getIdx()])->getResult();

        $response = [
            'result_code' => 200,
            'messages' => '성공',
            'myItemList' => $resultMyItemlist,
        ];
        return $this->respond($response, 200);
    }

    public function buyItem() {
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {

            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }

        try {
            $memberModel = new MemberModel();

            $memberModel->db->transStart();

            $member_idx = session()->get('member_idx');
            if ($member_idx == NULL) {
                $memberModel->db->transRollback();
                return $this->fail('로그인 후 시도해주세요.');
            }
            $member = $memberModel->getMemberWhereIdx($member_idx);
            if (!isset($member) || null == $member) {

                $memberModel->db->transRollback();
                return $this->fail('회원정보가 없는 계정입니다.');
            }

            $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 1;

            $sql = "SELECT * FROM item WHERE id = ? AND is_open = 'Y' ";
            $resultData = $memberModel->db->query($sql, [$itemId])->getResult();

            if (!isset($resultData)) {
                $memberModel->db->transRollback();
                return $this->fail("존재하지 않는 상품입니다.");
            }

            if ($member->getGmoney() < $resultData[0]->price) {
                $memberModel->db->transRollback();
                return $this->fail("G머니 부족으로 사용이 불가합니다.");
            }

            $sql = "INSERT INTO member_item(member_idx, item_id,item_value) VALUES(?,?,?)";
            $memberModel->db->query($sql, [$member->getIdx(), $itemId,$resultData[0]->value]);
            $row = $memberModel->db->query('SELECT LAST_INSERT_ID()')->getResultArray();

            $itemIdx = $row[0]['LAST_INSERT_ID()'];

            $bf_g_money = $member->getGmoney();
            $af_g_money = $bf_g_money - $resultData[0]->price;
            $sql = "UPDATE member SET g_money = g_money - ? WHERE idx = ?";
            $memberModel->db->query($sql, [$resultData[0]->price, $member->getIdx()]);

            // 구매 로그 및 g_money 사용로그 추가 
          
            $ukey = md5($member->getIdx() . strtotime('now'));
            $this->gmPt->insertLog($ukey, $member->getIdx(), AC_GM_BUYITEM, $itemId, $resultData[0]->price, $bf_g_money, $af_g_money, 'M', '아이템 구매', $itemIdx, 0, $memberModel, $this->logger);
            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('buyItem [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: buyItem query : ' . $memberModel->getLastQuery());

            $memberModel->db->transRollback();
            $response['messages'] = '디비처리 실패로 인한 배팅 실패';
            return $this->fail($response);
        }

        $response = [
            'result_code' => 200,
            'messages' => '구매 성공',
        ];
        return $this->respond($response, 200);
    }

    public function useItem() {
        
        $this->logger->error('useItem ddddd' );
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {
            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }

        try {
            $memberModel = new MemberModel();

            $memberModel->db->transStart();
            $member = $memberModel->getMemberWhereIdx(session()->get('member_idx'));
            if (!isset($member) || null == $member) {

                $memberModel->db->transRollback();
                return $this->fail('회원정보가 없는 계정입니다.');
            }

            //$itemIdx = isset($_POST['itemIdx']) ? $_POST['itemIdx'] : 0;
            $betIdx = isset($_POST['betIdx']) ? $_POST['betIdx'] : 0;
            $betDetailIdx = isset($_POST['betDetailIdx']) ? $_POST['betDetailIdx'] : 0;

            //$this->logger->info('useItem itemIdx : '.$itemIdx);
            list($retValue, $error) = $this->gmPt->useItemHitSpecial($memberModel, $member->getIdx(), $betIdx, $betDetailIdx,$this->logger);
            if (false == $retValue) {
                $memberModel->db->transRollback();
                return $this->fail($error);
            }

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('useItem [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: useItem query : ' . $memberModel->getLastQuery());

            $memberModel->db->transRollback();
            $response['messages'] = '디비처리 실패로 인한 배팅 실패';
            return $this->fail($response);
        }

        $response = [
            'result_code' => 200,
            'messages' => '사용 성공'
        ];
        return $this->respond($response, 200);
    }

}
