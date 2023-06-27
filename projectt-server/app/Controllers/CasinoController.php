<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\MemberModel;
use App\Models\TLogCashModel;
use App\Models\TGameConfigModel;
use App\Util\CodeUtil;
use App\Util\Casino;
use Cassandra\Date;
use CodeIgniter\API\ResponseTrait;

class CasinoController extends BaseController {

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

        $prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 1;
        $prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'C';

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);

        if ($member == null) {
            $response['messages'] = '조회되는 유저가 없습니다.';
            return $this->fail($response);
        }

        // 게임목록
        $sql = "SELECT * FROM KP_GAME_INF WHERE PRD_ID = ? AND IS_USE = 1";
        $gameList = $memberModel->db->query($sql, [$prd_id])->getResultArray();
        if (count($gameList) == 0) {
            $response['messages'] = '사용가능한 게임이 없습니다.';
            return $this->fail($response);
        }

        // 제품목록
        $sql = "SELECT * FROM KP_PRD_INF WHERE TYPE = ? AND IS_USE = 1 ORDER BY SORT_NUM";
        $prodList = $memberModel->db->query($sql, [$prd_type])->getResultArray();
        if (count($prodList) == 0) {
            $response['messages'] = '사용가능한 게임이 없습니다.';
            return $this->fail($response);
        }

        return view("$viewRoot/casino_list", [
            'prd_type' => $prd_type,
            'gameList' => $gameList,
            'prodList' => $prodList
        ]);
    }

    public function playCasino() {
        $memberIdx = session()->get('member_idx');
        $prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 1;
        $game_id = isset($_REQUEST['game_id']) ? $_REQUEST['game_id'] : 0;
        $prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'C';
        $chkMobile = CodeUtil::rtn_mobile_chk();
        //$viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (false == session()->has('member_idx') || !isset($memberIdx)) {
            return $this->fail('로그인 후 이용해주세요.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($memberIdx);
        if ($member == null) {
            return $this->fail('조회되는 회원 또는 메세지 idx 가 없습니다.');
        }

        if ($member->getStatus() == 2 || $member->getStatus() == 3) {
            $response['messages'] = '사용이 불가능 한 계정으로 관리자에게 문의바랍니다.';
            return $this->fail($response);
        }

        if ($member->getStatus() == 11) {
            $response['messages'] = '관리자 승인이 필요합니다.';
            return $this->fail($response);
        }

        if ($member->getUBusiness() != 1) {
            $response['messages'] = '총판은 배팅 이용이 불가능합니다.';
            return $this->fail($response);
        }

        $tgcModel = new TGameConfigModel();
        $str_sql_config = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type IN('service_casino','service_slot','service_esports','service_kiron','service_hash')";
        $arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

        $arr_config = array();
        foreach ($arr_config_result as $key => $value) {
            $arr_config[$value['set_type']] = $value['set_type_val'];
        }

        // 유저개인 점검
        $game_type_sql = "SELECT game_type, status FROM member_game_type where member_idx = ? and game_type in (9,10,11,12)";
        $arr_member_config_result = $tgcModel->db->query($game_type_sql, [$memberIdx])->getResultArray();

        $arr_member_config = array();
        foreach ($arr_member_config_result as $key => $value) {
            $arr_member_config[$value['game_type']]['status'] = $value['status'];
        }

        // 점검체크
        if ($prd_type == 'C') {
            if ('Y' == $arr_config['service_casino'] && 9 != $member->getLevel()) {
                $response['messages'] = '카지노 점검중입니다.';
                return $this->fail($response);
            }

            if ('OFF' == $arr_member_config[9]['status'] && 9 != $member->getLevel()) {
                $response['messages'] = '해당 게임 배팅이 금지되어있습니다.';
                return $this->fail($response);
            }
        }

        if ($prd_type == 'S') {
            if ($prd_id > 0 || $game_id > 0) {
                if ('Y' == $arr_config['service_slot'] && 9 != $member->getLevel()) {
                    $response = [
                        'result_code' => -1,
                        'messages' => '점검중입니다.',
                        'data' => [
                            'user_id' => $result->status,
                            'username' => $result->username,
                            'launch_url' => $result->launch_url
                        ]
                    ];
                    return $this->respond($response, 200);
                }
            }

            if ('OFF' == $arr_member_config[10]['status'] && 9 != $member->getLevel()) {
                $response = [
                    'result_code' => -1,
                    'messages' => '해당 게임 배팅이 금지되어있습니다.',
                    'data' => [
                        'user_id' => $result->status,
                        'username' => $result->username,
                        'launch_url' => $result->launch_url
                    ]
                ];
                return $this->respond($response);
            }
        }

        if ($prd_type == 'E') {
            if ('Y' == $arr_config['service_esports'] && 9 != $member->getLevel()) {
                $response = [
                    'result_code' => -1,
                    'messages' => '점검중입니다.',
                    'data' => [
                        'user_id' => $result->status,
                        'username' => $result->username,
                        'launch_url' => $result->launch_url
                    ]
                ];
                return $this->respond($response, 200);
            }

            if ('OFF' == $arr_member_config[11]['status'] && 9 != $member->getLevel()) {
                $response = [
                    'result_code' => -1,
                    'messages' => '해당 게임 배팅이 금지되어있습니다.',
                    'data' => [
                        'user_id' => $result->status,
                        'username' => $result->username,
                        'launch_url' => $result->launch_url
                    ]
                ];
                return $this->respond($response);
            }
        }

        if ($prd_type == 'K') {
            if ('Y' == $arr_config['service_kiron'] && 9 != $member->getLevel()) {
                $response = [
                    'result_code' => -1,
                    'messages' => '점검중입니다.',
                    'data' => [
                        'user_id' => $result->status,
                        'username' => $result->username,
                        'launch_url' => $result->launch_url
                    ]
                ];
                return $this->respond($response, 200);
            }

            if ('OFF' == $arr_member_config[12]['status'] && 9 != $member->getLevel()) {
                $response = [
                    'result_code' => -1,
                    'messages' => '해당 게임 배팅이 금지되어있습니다.',
                    'data' => [
                        'user_id' => $result->status,
                        'username' => $result->username,
                        'launch_url' => $result->launch_url
                    ]
                ];
                return $this->fail($response);
            }
        }

        if (0 < session()->get('tm_unread_cnt')) {
            return $this->fail('미확인 쪽지를 확인 바랍니다.');
        }

        $member_id = $member->getId();
        $money = $member->getMoney();
        $domain_url = substr(config(App::class)->baseURL, 0, -1);
        $type = $game_id;
        $is_mobile = "PC" == $chkMobile ? 0 : 1;

        $profile = config(App::class)->profile;

        // if ($profile != "prd") {
        //     $response['messages'] = '개발서버에서 접근이 불가능합니다.';
        //     return $this->fail($response);
        // }
        $apiUrl = config(App::class)->ApiUrl;
        $agToken = config(App::class)->AgToken;
        $agCode = config(App::class)->AgCode;
        $sercetKey = config(App::class)->SercetKey;
        $casino = new Casino($apiUrl, $agToken, $agCode, $sercetKey, $this->logger);
        $result = $casino->authCasino($memberIdx, $member_id, $money, $domain_url, $prd_id, $type, $is_mobile);
        if ($result->status == 0) {
            $response = [
                'result_code' => $result->status,
                'messages' => $result->error,
                'data' => [
                    'error' => $result->status
                ]
            ];
        } else {
            $sql = "SELECT count(*) as cnt FROM KP_MBR_INF WHERE KP_ID = $result->user_id";
            $mbrinf = $memberModel->db->query($sql)->getResultArray();
            if ($mbrinf[0]['cnt'] == 0) {
                $sql = "INSERT INTO KP_MBR_INF(MBR_IDX, KP_ID, KP_NM, REG_DTM) VALUES(?,?,?, now())";
                $memberModel->db->query($sql, [$memberIdx, $result->user_id, $result->username]);
            }

            $response = [
                'result_code' => $result->status,
                'messages' => '성공',
                'data' => [
                    'user_id' => $result->status,
                    'username' => $result->username,
                    'launch_url' => $result->launch_url
                ]
            ];
        }

        return $this->respond($response, 200);
    }

    public function esports() {
        $member_idx = session()->get('member_idx');
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

        return view("$viewRoot/slots_esports", [
                //'mobileType' => $mobileType
        ]);
    }

    public function kiron_soccer() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $mobileType = "PC" == $chkMobile ? 'web' : 'web';
        $viewRoot = "web";

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        return view("$viewRoot/kiron_soccer", [
            'mobileType' => $mobileType
        ]);
    }

    public function hash() {
        $member_idx = session()->get('member_idx');
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

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);
        if ($member == null) {
            return $this->fail('조회되는 회원 또는 메세지 idx 가 없습니다.');
        }

        $tgcModel = new TGameConfigModel();
        $str_sql_config = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type = 'service_hash'";
        $arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

        $arr_config = array();
        foreach ($arr_config_result as $key => $value) {
            $arr_config[$value['set_type']] = $value['set_type_val'];
        }

        // 유저개인 점검
        $game_type_sql = "SELECT game_type, status FROM member_game_type where member_idx = ? and game_type in (13)";
        $arr_member_config = $tgcModel->db->query($game_type_sql, [$member_idx])->getResultArray();

        // 점검체크
        if ('Y' == $arr_config['service_hash'] && 9 != $member->getLevel()) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('해쉬게임 점검중입니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        if ('OFF' == $arr_member_config[0]['status'] && 9 != $member->getLevel()) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
            alert('해당 게임 배팅이 금지되어있습니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $profile = config(App::class)->profile;

        // if ($profile != "prd") {
        //     $url = base_url("/$viewRoot/index");
        //     echo "<script>
    	// 	alert('개발서버에서 접근이 불가능합니다.');
    	// 	window.location.href='$url';
    	// 	</script>";
        //     return;
        // }

        $mobileType = "PC" == $chkMobile ? 'web' : 'web';
        $viewRoot = $mobileType;

        return view("$viewRoot/hash", [
            'mobileType' => $mobileType
            , 'type' => $type
        ]);
    }

    public function slot() {
        $member_idx = session()->get('member_idx');
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

        $prd_id = 201;
        $prd_type = 'S';

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
    		alert('미확인 쪽지를 확인 바랍니다.');
    		window.location.href='$url';
    		</script>";
            return;
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);

        if ($member == null) {
            $response['messages'] = '조회되는 유저가 없습니다.';
            return $this->fail($response);
        }

        // 게임목록
        $sql = "SELECT * FROM KP_GAME_INF WHERE PRD_ID = ? AND IS_USE = 1";
        $gameList = $memberModel->db->query($sql, [$prd_id])->getResultArray();
        if (count($gameList) == 0) {
            $response['messages'] = '사용가능한 게임이 없습니다.';
            return $this->fail($response);
        }

        // 제품목록
        $sql = "SELECT * FROM KP_PRD_INF WHERE TYPE = ? AND IS_USE = 1 ORDER BY SORT_NUM";
        $prodList = $memberModel->db->query($sql, [$prd_type])->getResultArray();
        if (count($prodList) == 0) {
            $response['messages'] = '사용가능한 게임이 없습니다.';
            return $this->fail($response);
        }

        return view("$viewRoot/slot", [
            'prd_type' => $prd_type,
            'gameList' => $gameList,
            'prodList' => $prodList
        ]);
    }

    public function slotStart() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $mobileType = "PC" == $chkMobile ? 'web' : 'web';
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $uri = new \CodeIgniter\HTTP\URI();
        $uri = service('uri');
        $first = $uri->getSegment(2);
        $second = $uri->getSegment(3);
        return view("$viewRoot/slot", [
            'mobileType' => $mobileType,
            'productId' => $first,
            'gameId' => $second
        ]);
    }

    public function debit() {
        $this->logger->info("#################################");
        $this->logger->info("CasinoController debit start param =>" . json_encode($_POST));
        $memberModel = new MemberModel();
        try {
            $memberModel->db->transStart();
            $kpId = $_POST['user_id'];
            $productId = $_POST['prd_id'];
            $gameId = isset($_POST['game_id']) ? $_POST['game_id'] : 0;
            $transactionId = $_POST['txn_id'];
            $betMoney = (int) (round($_POST['amount'])); // Long.valueOf(Math.round(Double.valueOf(params.get("amount").toString()))).intValue();

            /* 배팅 & 승리 동시 처리일 시 승리금액 */
            $winMoney = 0;
            $hasWinMoney = false;
            if (true === isset($_POST['credit_amount']) && false === empty($_POST['credit_amount'])) {
                $hasWinMoney = true;
                $winMoney = (int) $_POST['credit_amount'];
            }

            /* 회원 조회 */
            $findMember = $memberModel->getMemberWhereId($kpId);
            if ($findMember == null) {
                $memberModel->db->transRollback();
                $response = [
                    'status' => 0,
                    'balance' => 0.0,
                    'error' => 'INVALID_USER'
                ];
                return $this->respond($response, 200);
            }

            $memberIdx = $findMember->getIdx();

            $tgcModel = new TGameConfigModel();
            $str_sql_config = "SELECT set_type, set_type_val FROM t_game_config WHERE set_type IN('service_casino')";
            $arr_config_result = $tgcModel->db->query($str_sql_config)->getResultArray();

            $arr_config = array();
            foreach ($arr_config_result as $key => $value) {
                $arr_config[$value['set_type']] = $value['set_type_val'];
            }

            // 유저개인 점검
            $game_type_sql = "SELECT game_type, status FROM member_game_type where member_idx = ? and game_type in (9)";
            $arr_member_config_result = $tgcModel->db->query($game_type_sql, [$memberIdx])->getResultArray();

            $arr_member_config = array();
            foreach ($arr_member_config_result as $key => $value) {
                $arr_member_config[$value['game_type']]['status'] = $value['status'];
            }

            // 점검체크
            if ('Y' == $arr_config['service_casino'] && 9 != $findMember->getLevel()) {
                $memberModel->db->transRollback();
                $response = [
                    'status' => 0,
                    'balance' => 0.0,
                    'error' => '현재 사이트 점검중입니다.'
                ];
                return $this->respond($response, 200);
            }

            if ('OFF' == $arr_member_config[9]['status'] && 9 != $findMember->getLevel()) {
                //$response['messages'] = '해당 게임 배팅이 금지되어있습니다.';
                //return $this->fail($response);
                $memberModel->db->transRollback();
                $response = [
                    'status' => 0,
                    'balance' => 0.0,
                    'error' => '현재 사이트 점검중입니다.'
                ];
                return $this->respond($response, 200);
            }


            /* 보유 금액보다 큰 배팅 */
            $holdMoney = $findMember->getMoney();
            if ($holdMoney < $betMoney) {
                $memberModel->db->transRollback();
                $response = [
                    'status' => 0,
                    'balance' => 0.0,
                    'error' => 'INSUFFICIENT_FUNDS'
                ];
                return $this->respond($response, 200);
            }

             /* 이미 진행된 배팅 */
            $sql = "SELECT PRD_ID, TYPE FROM KP_SLOT_BET_HIST WHERE TRX_ID = ?
                    UNION
                    SELECT PRD_ID, TYPE FROM KP_CSN_BET_HIST WHERE TRX_ID = ?
                    UNION
                    SELECT PRD_ID, TYPE FROM KP_ESPT_BET_HIST WHERE TRX_ID = ?";

            $resultBetInfo = $memberModel->db->query($sql, [$transactionId, $transactionId, $transactionId])->getResultArray();
            if (!is_null($resultBetInfo) && 0 < count($resultBetInfo)) {
                throw new Exception('DUPLICATED_DEBIT');
            }
        
            /* 배팅 저장 */
            $casino = new Casino(null, null, null, null, $this->logger);
            $afterMoney = $casino->saveKpBetting($findMember, $betMoney, $productId, $gameId, $transactionId,$memberModel);

            /* IF 배팅 & 승리 동시 처리 */
            if ($winMoney > 0) {
                /* 배팅 승리 처리 */
                $casino->modifyWinBetting($findMember,$winMoney,$resultBetInfo,$memberModel);
                $afterMoney += $winMoney;
            } else if ($hasWinMoney && $winMoney == 0) {
                /* 배팅 패배 처리 */
                //Map<String, Object> betParams = new HashMap<>();
                //betParams . put("kpId", kpId);
                //betParams . put("memberIdx", memberIdx);
                //betParams . put("amount", winMoney);
                //betParams . put("transactionId", transactionId);
                //betParams . put("betMoney", betMoney);
                $casino->modifyLoseBetting($findMember,$kpId,$memberIdx,$winMoney,$transactionId,$betMoney);
            }

            /* Response */
            $response = [
                'status' => 1,
                'balance' => $afterMoney
            ];
            
            $this->logger->info("CasinoController debit end param =>" . json_encode($response));
            return $this->respond($response, 200);
        
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  CasinoController debit error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  CasinoController debit query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
            //$response['messages'] = '디비처리 실패로 인한 배팅 실패';
            return $this->respond($response, 200);
        } catch (\Exception $e){
            $this->logger->error(':::::::::::::::  CasinoController debit error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  CasinoController debit query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
            
            return $this->respond($response, 200);
        }
    }
}
