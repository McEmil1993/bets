<?php

namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */
use App\Models\MemberModel;
use App\Models\TGameConfigModel;
use CodeIgniter\Controller;

class BaseController extends Controller {

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    //protected $request;

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();
        //$this->logger->error($_REQUEST);
        //$this->logger->error($_POST);
        //$this->logger->error($_GET);

        unset($arr_request);
        unset($arr_post);
        unset($arr_get);
        unset($arr_illegal);
        unset($arr_replaced);

        $arr_request = $_REQUEST;
        $arr_post = $_POST;
        $arr_get = $_GET;

        reset($arr_request);
        reset($arr_post);
        reset($arr_get);

        /** 한글 표현 때문에 ; 를 무조건 제거: semi-colon 사용하기 위해서 |mMm|59 라고 치환하시면 됩니다. * */
        $arr_illegal = array('&', ';', '&#', '--', '/*', '*/', 'iframe', 'script', 'embed', 'cookie', 'drop', 'truncate', 'select', 'fopen', 'fsockopen', 'file_get_contents', 'readfile', 'unlink', 'object', 'phpinfo', '1=1', 'union', '<', '>');
        $arr_replaced = array('', '', '', '', '', '', 'if_rame', 'scr_ipt', 'emb_ed', 'coo_kie', 'dr_op', 'trun_cate_', 'sel_ect', 'fo_pen', 'fsoc_kopen_', '_file_get_cont_ents_', 'read_file', 'un_link', 'obj_ect', 'php_info', '1_=_1', 'u_ni_on', 'lt', 'gt');

        $arr_request = str_ireplace($arr_illegal, $arr_replaced, $arr_request);
        $arr_post = str_ireplace($arr_illegal, $arr_replaced, $arr_post);
        $arr_get = str_ireplace($arr_illegal, $arr_replaced, $arr_get);

        $_REQUEST = $arr_request;
        $_POST = $arr_post;
        $_GET = $arr_get;

        unset($arr_request);
        unset($arr_post);
        unset($arr_get);
        unset($arr_illegal);
        unset($arr_replaced);

        //$this->logger->error('after request : '.json_encode($_REQUEST));
        //$this->logger->error('after post : '.json_encode($_POST));
        //$this->logger->error('after get : '.json_encode($_GET));
        $member_model       = new MemberModel();
        $sql                = "SELECT * FROM t_game_config where set_type = 'service_site'";
        $site_check_config  = $member_model->db->query($sql)->getResultArray()[0];

        /* site inspection page redirect if set by admin */
        $member_id          = session()->get('id');

        if($site_check_config["set_type_val"] == 'Y' && $member_id)
        {
            $find_member        = $member_model->getMemberWhereId($member_id);
            $member_level       = $find_member->getLevel();
            $is_inspection_page = strpos($_SERVER['REQUEST_URI'], "inspection") !== false;

            if($member_level != 9 && $is_inspection_page == false)
            {
                header("Location: /web/inspection"); 
                exit;
            }
        }
    }

    public function initMemberData($session, $idx) {
        $memberModel = new MemberModel();
        $findMember = $memberModel->getMemberWhereIdx($idx);
        if (!isset($findMember) || null == $findMember)
            return;
        $session->set('id', $findMember->getId());
        $session->set('member_idx', $findMember->getIdx());
        $session->set('money', $findMember->getMoney());
        $session->set('point', $findMember->getPoint());
        $session->set('nick_name', $findMember->getNickName());
        $session->set('level', $findMember->getLevel());
        $session->set('account_name', $findMember->getAccountName());
        $session->set('account_number', $findMember->getAccountNumber());
        $session->set('account_bank', $findMember->getAccountBank());
        $session->set('call', $findMember->getCall());
        $session->set('g_money', $findMember->getGmoney());
        $session->set('keep_login_access_token', $findMember->get_keep_login_access_token());
        //$this->logger->critical('::::::::::::::: initMemberData keep_login_access_token : '.$findMember->get_keep_login_access_token());
    }

    public function initMemberDataByMember($session, $findMember) {

        $session->set('id', $findMember->getId());
        $session->set('member_idx', $findMember->getIdx());
        $session->set('money', $findMember->getMoney());
        $session->set('point', $findMember->getPoint());
        $session->set('nick_name', $findMember->getNickName());
        $session->set('level', $findMember->getLevel());
        $session->set('account_name', $findMember->getAccountName());
        $session->set('account_number', $findMember->getAccountNumber());
        $session->set('call', $findMember->getCall());
        $session->set('g_money', $findMember->getGmoney());
        $session->set('keep_login_access_token', $findMember->get_keep_login_access_token());
        //$this->logger->critical('::::::::::::::: initMemberDataByMember keep_login_access_token : '.$findMember->get_keep_login_access_token());
         
    }

    public function checkLoginAccessToken($call_function) {
        if (!isset($_POST['keep_login_access_token'])) {
            return [null, null, '인자값이 잘못되었습니다.']; //return $this->fail('인자값이 잘못되었습니다.');
        }

        //$keep_login_access_token = $_POST['keep_login_access_token'];
        $memberIdx = session()->get('member_idx');
        $memberModel = new MemberModel();
        //$findMember = $memberModel->getMemberWhereKeepLoginAccessToken($keep_login_access_token);
        $findMember = $memberModel->getMemberWhereIdx($memberIdx);
        if ($findMember == NULL) {
            $this->logger->critical('::::::::::::::: checkLoginAccessToken keep_login_access_token : '.$keep_login_access_token);
            // return [null, null, '인증토큰값이 잘못되었습니다.'];
            return [null, null, '조회되는 유저가 없습니다.'];
        }

        /*if ($keep_login_access_token != session()->get('keep_login_access_token') || $keep_login_access_token != $findMember->get_keep_login_access_token()) {
            $this->logger->error(':: post keep_login_access_token : '.$keep_login_access_token);
            $this->logger->error(':: session keep_login_access_token : '.session()->get('keep_login_access_token'));
            $this->logger->error(':: db keep_login_access_token : '.$findMember->get_keep_login_access_token().' __function__ : '.$call_function );
            return [null, null, '로그인 인증키가 잘못되었습니다.']; //return $this->fail('로그인 인증키가 잘못되었습니다');
        }*/

        if ($findMember->getStatus() == 2 || $findMember->getStatus() == 3) {
            //$response['messages'] = '사용이 불가능한 계정입니다. 관리자에게 문의해주세요.';
            return [null, null, '사용이 불가능한 계정입니다. 관리자에게 문의해주세요.']; //return $this->fail('사용이 불가능한 계정입니다. 관리자에게 문의해주세요.');
        }

        if ($findMember->getStatus() == 11) {
            //$response['messages'] = '관리자 승인이 필요합니다.';
            return [null, null, '관리자 승인이 필요합니다.']; //return $this->fail('관리자 승인이 필요합니다.');
        }


        $this->initMemberDataByMember(session(), $findMember);

        return [$findMember, $memberModel, '성공'];
    }
    
    // sports, retime, class, minigame betting is check token
    public function checkBettingAccessToken($keep_login_access_token,$memberModel) {
        if (!isset($keep_login_access_token)) {
            return [false, '인자값이 잘못되었습니다.']; //return $this->fail('인자값이 잘못되었습니다.');
        }

        //$memberIdx = session()->get('member_idx');
        //$memberModel = new MemberModel();
        $findMember = $memberModel->getMemberWhereKeepLoginAccessToken($keep_login_access_token);
        if ($findMember == NULL) {
            $this->logger->critical('::::::::::::::: checkBettingAccessToken keep_login_access_token : '.$keep_login_access_token);
            return [false, '잘못된 베팅입니다.'];
        }

        /*if ($keep_login_access_token != session()->get('keep_login_access_token') || $keep_login_access_token != $findMember->get_keep_login_access_token()) {
            $this->logger->error(':: post keep_login_access_token : '.$keep_login_access_token);
            $this->logger->error(':: session keep_login_access_token : '.session()->get('keep_login_access_token'));
            $this->logger->error(':: db keep_login_access_token : '.$findMember->get_keep_login_access_token().' __function__ : '.$call_function );
            return [null, null, '로그인 인증키가 잘못되었습니다.']; //return $this->fail('로그인 인증키가 잘못되었습니다');
        }*/

        return [true, '성공'];
    }

}
