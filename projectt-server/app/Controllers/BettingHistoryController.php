<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\LSportsMarketsModel;
use App\Models\MemberBetDetailModel;
use App\Models\MemberBetModel;
use App\Models\MemberModel;
use App\Models\TMessageListModel;
use App\Models\TMessageModel;
use App\Util\DateTimeUtil;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;
use App\Models\TLogCashModel;
use App\Models\MiniGameMemberBetModel;
use App\Models\TotalMemberCashModel;
use App\Util\Calculate;
use App\GamblePatch\GambelGmPt;
use App\GamblePatch\KwinGmPt;
use App\GamblePatch\BetGoGmPt;
use App\GamblePatch\ChoSunGmPt;
use App\GamblePatch\BetsGmPt;
use App\GamblePatch\NobleGmPt;
use App\GamblePatch\BullsGmPt;
use App\Util\UserPayBack;

class BettingHistoryController extends BaseController {

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

    public function mb_str_split($str) {

        $ret = array();

        for ($i = 0; $i < mb_strlen($str, "euc-kr"); $i++) {

            array_push($ret, mb_substr($str, $i, 1, "euc-kr"));
        }

        return $ret;
    }

    public function index() {
        try {
            $member_idx = session()->get('member_idx');
            $chkMobile = CodeUtil::rtn_mobile_chk();

            $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

            $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
            $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
            $_GET['bet_group'] = isset($_GET['bet_group']) ? $_GET['bet_group'] : 1;

            // 마지막 시간에 하루 더해준다.
            $timestamp = strtotime($betToDate . "+1 days");
            $betToDate = date("Y-m-d H:i:s", $timestamp);

            if ($member_idx == NULL || null == session()->get('call')) {
                //$url = base_url("/$viewRoot/login");
                return redirect()->to(base_url("/$viewRoot/index"));
            }

            if (!is_int((int) $member_idx) || !is_int((int) $_GET['bet_group'])) {
                $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::index : ==> ");
                die();
            }

            //$member_idx = 3666;
            $this->initMemberData(session(), $member_idx);

            $memberModel = new MemberModel();
            $member = $memberModel->getMemberWhereIdx($member_idx);

            $arr = str_split($member->getAccountNumber());
            for ($i = 2; $i < count($arr) - 2; ++$i) {
                $arr[$i] = "*";
            }

            $str_at_number = implode(" ", $arr);
            $member->setAccountNumber($str_at_number);

            $name = $member->getAccountName();
            $sub_name1 = "";
            $sub_name2 = "";
            $name_len = mb_strlen($name);

            $call = $member->getCall();
            $call_len = strlen($call);

            $call = substr_replace($call, "****", $call_len - 4, 4);

            $member->setCall($call);
            if (2 == $name_len) {
                $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
                $name = $sub_name1 . "*";
            } else if (3 == $name_len) {
                $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
                $sub_name2 = iconv_substr($name, 2, 2, "utf-8");
                $name = $sub_name1 . "*" . $sub_name2;
            } else if (4 == $name_len) {
                $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
                $sub_name2 = iconv_substr($name, 3, 3, "utf-8");
                $name = $sub_name1 . "**" . $sub_name2;
            }

            $member->setAccountName($name);

            $memberBetModel = new MemberBetModel();
            $memberBetModelCount = new MemberBetModel();
            $memberBetDetailModel = new MemberBetDetailModel();
            $miniGameMemberBetModel = new MiniGameMemberBetModel();
            $miniGameMemberBetModelCount = new MiniGameMemberBetModel();

            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $menu = isset($_GET['menu']) ? $_GET['menu'] : 'a'; // a 이면 내정보 b면 배팅내역 
            $recommentChargeInfo = array();

            //$member_idx = 2652;

            if ($menu == 'b') {
                $recommendList = [];

                // 스포츠, 실시간
                if ($_GET['bet_group'] < 3) {
                    if ($_GET['bet_group'] == 1)
                        $bet_type = 2;
                    else
                        $bet_type = 1;
                    $betList = $memberBetModel
                            ->select(['member_bet.*'])
                            ->where('member_bet.member_idx', $member_idx);

                    $betListCnt = $memberBetModelCount
                            ->select(['member_bet.*'])
                            ->where('member_bet.member_idx', $member_idx);

                    $betList = $betList->where('member_bet.create_dt > ', $betFromDate)
                            ->where('member_bet.create_dt < ', $betToDate)
                            ->where('member_bet.bet_type = ', $bet_type)
                            ->where('member_bet.is_hide = ', 0)
                            ->join('member_bet_detail as detail', 'member_bet.idx = detail.bet_idx', 'left')
                            ->join('lsports_fixtures as lf', 'detail.ls_fixture_id = lf.fixture_id', 'left')
                            ->join('lsports_sports as ls', 'lf.fixture_sport_id = ls.id', 'left')
                            ->groupBy('idx')
                            ->orderBy('member_bet.idx', 'desc');

                    $betListCnt = $betListCnt->where('member_bet.create_dt > ', $betFromDate)
                            ->where('member_bet.create_dt < ', $betToDate)
                            ->where('member_bet.bet_type = ', $bet_type)
                            ->where('member_bet.is_hide = ', 0)
                            ->join('member_bet_detail as detail', 'member_bet.idx = detail.bet_idx', 'left')
                            ->join('lsports_fixtures as lf', 'detail.ls_fixture_id = lf.fixture_id', 'left')
                            ->groupBy('idx')
                            ->orderBy('member_bet.idx', 'desc');

                    $betList = $betList->limit(10, ($page - 1) * 10)->find();
                    $betListCnt = $betListCnt->find();
                } else {  // 미니게임
                    $betList = $miniGameMemberBetModel
                            ->select(['mini_game_member_bet.idx', 'mini_game_member_bet.bet_type', 'total_bet_money', 'bet_price', 'bet_status', 'cnt', 'create_dt', 'ls_fixture_id', 'ls_markets_name', 'take_money', 'is_open', 'is_hide', 'mini_game.result', 'mini_game.result_score'])
                            ->join('mini_game', 'mini_game_member_bet.ls_fixture_id = mini_game.id', 'left')
                            ->where('mini_game_member_bet.member_idx', $member_idx)
                            ->where('create_dt > ', $betFromDate)
                            ->where('create_dt < ', $betToDate)
                            ->where('is_hide', 0)
                            ->orderBy('idx', 'desc');

                    $betListCnt = $miniGameMemberBetModelCount
                            ->select(['mini_game_member_bet.*'])
                            ->where('mini_game_member_bet.member_idx', $member_idx)
                            ->where('create_dt > ', $betFromDate)
                            ->where('create_dt < ', $betToDate)
                            ->orderBy('idx', 'desc');

                    $betList = ($betList->limit(10, ($page - 1) * 10)->find());
                    $betListCnt = $betListCnt->find();
                }

                $betListCnt = count($betListCnt);
            } else {
                $betList = [];
                $betListCnt = 0;

                // 추천인 리스트를 가져온다.
                $sql_count = "SELECT id FROM member where recommend_member =  ? ";

                $recommendCountList = $memberModel->db->query(
                                $sql_count, [$member_idx]
                        )->getResultArray();

                $recommendAllCount = count($recommendCountList);

                $start_page = ($page - 1) * 10;
                $sql = "SELECT id,idx,nick_name,recommend_code_dt,reg_time  FROM member where recommend_member =  ? order by reg_time desc limit ?,10";
                $recommendList = $memberModel->db->query($sql, [$member_idx, $start_page])->getResultArray();

                if ($recommendAllCount > 0) {
                    $recomment_idxs = array();
                    foreach ($recommendList as $key => $value) {
                        $recomment_idxs[] = $value['idx'];
                    }
                    $recomment_idxs = implode(',', $recomment_idxs);

                    // 추천회원 리스트
                    $sql = "SELECT id,idx,nick_name,recommend_code_dt,reg_time  FROM member where recommend_member =  ? order by reg_time desc limit ?,10";
                    $recommendList = $memberModel->db->query($sql, [$member_idx, $start_page])->getResultArray();

                    $sql = "SELECT member_idx, count(idx) as cnt, sum(money) as money FROM member_money_charge_history where member_idx in ($recomment_idxs) and status = 3 group by member_idx;";
                    $recommentChargeInfo = $memberModel->db->query($sql)->getResultArray();
                }
            }

            $betIds = array();
            if ($betListCnt > 0 && $_GET['bet_group'] < 3) {
                foreach ($betList as $key => $bet) {
                    $betIds[] = $bet->idx;
                }
                $betIds = implode(',', $betIds);

                $current_day = date("d");
                $sql = "select         
                member_bet_detail.idx,
				member_bet_detail.bet_idx,
                member_bet_detail.bet_status,
                member_bet_detail.ls_markets_name as markets_name,
                member_bet_detail.ls_bet_id,
                member_bet_detail.ls_markets_base_line as markets_base_line,
                member_bet_detail.ls_markets_id as markets_id,
                member_bet_detail.result_score,
                member_bet_detail.ls_fixture_id as fixture_id,
                -- IF('ON' = mb_bet.is_betting_slip ,bet.bet_price,member_bet_detail.bet_price) as bet_price,
                member_bet_detail.bet_price as bet_price,
                member_bet_detail.bet_name,
                member_bet_detail.fixture_sport_id,
                member_bet_detail.fixture_location_id,
                member_bet_detail.fixture_league_id,
                IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity,lsports_fixtures.fixture_start_date) as fixture_start_date,
                p1.team_name as p1_team_name, p1.display_name as p1_display_name, 
                p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                league.display_name as league_display_name,
                league.name as league_name,
                league.image_path as league_image_path,
                location.image_path as location_image_path,
                ls.display_name,
                ls.name
                from member_bet_detail 
                LEFT JOIN       member_bet as mb_bet ON member_bet_detail.bet_idx = mb_bet.idx
                -- LEFT JOIN       lsports_bet as bet ON member_bet_detail.ls_fixture_id = bet.fixture_id
                LEFT JOIN 	lsports_participant as p1
                       ON	member_bet_detail.fixture_participants_1_id = p1.fp_id
                LEFT JOIN 	lsports_participant as p2
                       ON	member_bet_detail.fixture_participants_2_id = p2.fp_id
                LEFT JOIN 	lsports_leagues as league
                       ON	member_bet_detail.fixture_league_id = league.id
                LEFT JOIN 	lsports_locations as location
                       ON	member_bet_detail.fixture_location_id = location.id
                LEFT JOIN lsports_sports as ls 
                       ON   member_bet_detail.fixture_sport_id = ls.id
                LEFT JOIN lsports_fixtures 
                       ON lsports_fixtures.fixture_id = member_bet_detail.ls_fixture_id
                       AND lsports_fixtures.bet_type  = member_bet_detail.bet_type
                where 
                member_bet_detail.bet_idx in ($betIds)
                and member_bet_detail.create_dt between ? and  ?
                group by member_bet_detail.idx";

                $betDetailResult = $memberBetDetailModel->db->query($sql, [$betFromDate, $betToDate])->getResultArray();
                //$this->logger->error(json_encode($betDetailResult));

                $betDetail = array();
                foreach ($betDetailResult as $bet_detail) {
                    $betDetail[$bet_detail['bet_idx']][] = $bet_detail;
                }

                foreach ($betList as $key => $bet) {
                    $bet_price = 1;
                    if (!empty($betDetail[$bet->idx])) {
                        foreach ($betDetail[$bet->idx] as $bet_detail) {
                            $bet_price = $bet_price * $bet_detail['bet_price'];
                        }
                        $bet->betDetail = $betDetail[$bet->idx];
                        $bet->total_bet_price = $bet_price * $bet->bonus_price;
                    } else {
                        $bet->betDetail = [];
                        $bet->total_bet_price = 0000;
                    }
                }
            } else {
                
            }
            $timestamp = strtotime("-3 days");

            $tMessageModel = new TMessageModel();

            $member_idx = $member->getIdx();
            $sql = "delete tm, tmlt from t_message as tm 
                left join t_message_list as tmlt 
                on tm.msg_idx = tmlt.idx 
                where tm.member_idx = ? and tm.reg_time < date_sub( now(),interval 7 day);";

            $tMessageModel->db->query($sql, [$member_idx]);

            $messages = $tMessageModel
                    ->select(['t_message.*', 'tml.title', 'tml.content', 'tml.a_id'])
                    ->join('t_message_list tml', 't_message.msg_idx = tml.idx', 'left')
                    ->where('t_message.member_idx', $member->getIdx())
                    ->where('t_message.is_delete', 0)
                    ->orderBy('t_message.idx', 'desc')
                    ->find();

            // 포인트 사용이력을 가져온다. -7일 기준 
            if ('e' == $menu) {
                $sql = "SELECT * FROM t_log_cash WHERE member_idx = ? AND ac_code IN (6,8,9,10,11,123,124,126,127,202,203) AND reg_time BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW() ORDER BY reg_time DESC ";
                $pointList = $memberBetDetailModel->db->query($sql, [$member_idx])->getResultArray();
            }

            $totalCnt = 0;
            if ('a' == $menu) {
                $totalCnt = $recommendAllCount;
            } else if ('b' == $menu) {
                $totalCnt = $betListCnt;
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: doTotalCalculate query : ' . $tMessageModel->getLastQuery());
            $this->logger->error('::::::::::::::: doTotalCalculate query : ' . $memberBetDetailModel->getLastQuery());
        }

        return view("$viewRoot/betting_history", [
            'member' => $member,
            'betList' => $betList,
            'betListCnt' => $betListCnt,
            'messageList' => $messages,
            'page' => $page,
            'recommendList' => $recommendList,
            'totalCnt' => $totalCnt,
            'menu' => $menu,
            'recommentChargeInfo' => $recommentChargeInfo,
            'pointList' => $pointList
        ]);
    }

    // 쪽지 개별삭제
    public function deleteMessage() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        $delete_idx = isset($_POST['idx']) ? $_POST['idx'] : NULL;

        if (!is_int((int) $delete_idx) || !is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::deleteMessage: ==> ");
            die();
        }

        $tMessageModel = new TMessageModel();

        /* $sql = "delete tm, tmlt from t_message as tm 
          left join t_message_list as tmlt
          on tm.msg_idx = tmlt.idx
          where tm.member_idx = $member_idx and tm.idx = $delete_idx;"; */
        $sql = "update t_message set is_delete = 1 where member_idx = ? and idx = ?";

        $tMessageModel->db->query($sql, [$member_idx, $delete_idx]);

        $response = [
            'result_code' => 200,
            'messages' => '삭제 성공.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    // 쪽지 전체삭제
    public function deleteAllMessage() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/"));
        }

        $delete_idx = isset($_POST['idx']) ? $_POST['idx'] : 0;

        if (!is_int((int) $delete_idx) || !is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::deleteAllMessage: ==> ");
            die();
        }

        $tMessageModel = new TMessageModel();

        /* $sql = "delete tm, tmlt from t_message as tm 
          left join t_message_list as tmlt
          on tm.msg_idx = tmlt.idx
          where tm.member_idx = $member_idx;"; */
        $sql = "update t_message set is_delete = 1, read_yn = 'Y', read_time = now() where member_idx = ?";

        $tMessageModel->db->query($sql, [$member_idx]);

        $response = [
            'result_code' => 200,
            'messages' => '삭제 성공.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function allReadMessage() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::deleteAllMessage: ==> ");
            die();
        }

        $tMessageModel = new TMessageModel();

        $sql = "update t_message set read_yn = 'Y', read_time = now() where  member_idx =  ? and read_yn = 'N' ";

        $tMessageModel->db->query($sql, [$member_idx]);

        $response = [
            'result_code' => 200,
            'messages' => '삭제 성공.',
            'data' => []
        ];
        return $this->respond($response, 200);
    }

    public function bettingCancel() {
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        $idx = $_POST['idx'];
        $member_idx = session()->get('member_idx');
        if ($member_idx == NULL) {

            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::deleteAllMessage: ==> ");
            die();
        }

        $memberBetDetailModel = new MemberBetDetailModel();
        $memberBetModel = new MemberBetModel();
        try {
            $memberBetDetailModel->db->transStart();

            $arrMbBtResult = $memberBetModel->SelectMemberBet($idx);

            if (0 == count($arrMbBtResult) || 1 != $arrMbBtResult[0]['bet_status']) {
                $memberBetDetailModel->db->transRollback();
                return $this->fail('취소할 데이터가 없습니다. ');
            }

            $str_sql_limit = "SELECT set_type_val FROM t_game_config WHERE set_type = 'limit_cancel_time'";
            $limit_config = $memberBetModel->db->query($str_sql_limit)->getResultArray();

            $dt_current = date("Y-m-d H:i:s");

            $limitTime = date("Y-m-d H:i:s", strtotime($arrMbBtResult[0]['create_dt'] . "+" . $limit_config[0]['set_type_val'] . " minutes"));

            if ($limitTime < $dt_current) {
                $memberBetDetailModel->db->transRollback();
                //return $this->fail('베팅 취소는 베팅 후' . $limit_config[0]['set_type_val'] . '분 내로 가능합니다.');
                return $this->fail("스포츠 배팅시 배팅 후 30분 이내\n경기시작 30분전 정상 취소처리 가능합니다.");
            }

            $array_fixture = [];
            $take_money = 0;
            $take_money = 1 * $arrMbBtResult[0]['total_bet_money'];
            $bet_type = 0;
            foreach ($arrMbBtResult as $value) {

                if (true == in_array($value['ls_fixture_id'], $array_fixture))
                    continue;

                $ls_fixture_id = $value['ls_fixture_id'];
                $array_fixture[] = $ls_fixture_id;
                $bet_type = $value['bet_type'];
                $p_data['sql'] = "update fixtures_bet_sum set sum_bet_money = sum_bet_money - ? where member_idx = ? AND fixture_id = ? AND bet_type = ?";
                $memberBetDetailModel->db->query($p_data['sql'], [$take_money, $member_idx, $ls_fixture_id, $bet_type]);

                $dt_start = $value['fixture_start_date'];
                $endTime = date("Y-m-d H:i:s", strtotime($dt_start . "-" . 30 . " minutes"));
                if ($endTime < $dt_current) {
                    $memberBetDetailModel->db->transRollback();
                    return $this->fail("배팅 취소는 배팅 후 30분 이내\n경기시작 30분전 정상 취소처리 가능합니다.");
                }
            }


            if ($take_money > 0) {
                $p_data['sql'] = "update member set money = money + ? where idx = ? ";
                $memberBetDetailModel->db->query($p_data['sql'], [$take_money, $arrMbBtResult[0]['member_idx']]);
            }

            $memberBetDetailModel->UpdateMemberBetDetailByMb_bt_idx($idx, 5);

            $memberBetModel->UpdateMemberBet($idx, 5, $take_money, 0, 0, 'M');

            /* 1:충전,2:환전,3:베팅,4:베팅취소,5:포인트전환(추가),6:포인트차감,10:포인트충전,7:베팅결과처리,8:이벤트충전,9:이벤트차감,101:충전요청,102:환전요청,103:계좌조회,
             * 111:충전요청취소,112:환전요청취소,113:충전취소,114:환전취소,121:관리자충전,122:관리자회수,123:관리자 포인트 충전, 124:관리자 포인트 회수,998:데이터복구,999:기타 
             */

            $ukey = md5($arrMbBtResult[0]['member_idx'] . strtotime('now'));

            $a_comment = '';
            if (1 == $bet_type) {
                $a_comment = 'prematch ==>';
            } else if (2 == $bet_type) {
                $a_comment = 'inplay ==>';
            }

            $a_comment .= " 배팅 취소";
            $a_comment .= addslashes($a_comment);
            $tLogCashModel = new TLogCashModel();
            $tLogCashModel->insertCashLog_mem_idx($ukey, $arrMbBtResult[0]['member_idx'], 4, $idx, $take_money, $arrMbBtResult[0]['money'], 'P', $a_comment);

            $type = '';
            if (1 == $arrMbBtResult[0]['bet_type'] && 'S' == $arrMbBtResult[0]['folder_type']) {
                $type = 'SPORTS_S';
            } else if (1 == $arrMbBtResult[0]['bet_type'] && 'D' == $arrMbBtResult[0]['folder_type']) {
                $type = 'SPORTS_D';
            } else if (2 == $arrMbBtResult[0]['bet_type'] && 'S' == $arrMbBtResult[0]['folder_type']) {
                $type = 'REAL_S';
            } else if (2 == $arrMbBtResult[0]['bet_type'] && 'D' == $arrMbBtResult[0]['folder_type']) {
                $type = 'REAL_D';
            }

            Calculate::decUpdateChargeBetMoney($arrMbBtResult[0]['create_dt'], $arrMbBtResult[0]['member_idx'], $type, $arrMbBtResult[0]['total_bet_money'], $tLogCashModel, $this->logger);
            UserPayBack::AddBetting($arrMbBtResult[0]['member_idx'],-$arrMbBtResult[0]['total_bet_money'],$tLogCashModel);       
            $item_idx = $arrMbBtResult[0]['item_idx'];
            list($retval, $error) = $this->gmPt->cancelItemUse($member_idx, $item_idx, $idx, $memberBetModel, $this->logger);
            if (false == $retval) {
                $memberBetDetailModel->db->transRollback();
                return $this->fail($error);
            }


            $memberBetDetailModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('bettingCancel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: doTotalCalculate query : ' . $memberBetDetailModel->getLastQuery());
            $memberBetDetailModel->db->transRollback();
            return $this->fail('디비 처리 에러 입니다.');
        }

        $response = [
            'result_code' => 200,
            'messages' => '배팅 취소 성공.',
        ];
        return $this->respond($response, 200);
    }

     public function bettingHide() {
        $idx = $_POST['idx'];
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $idx) || !is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::bettingHide: ==> ");
            die();
        }

        $memberBetModel = new MemberBetModel();
        try {
            $sql = "select IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity,lsports_fixtures.fixture_start_date) as fixture_start_date
                    , member_bet_detail.bet_status as detail_bet_status 
                    , member_bet.bet_status from member_bet 
                     left join member_bet_detail on member_bet.idx = member_bet_detail.bet_idx 
                     left join lsports_fixtures on member_bet_detail.ls_fixture_id = lsports_fixtures.fixture_id 
                     where member_bet.idx = ? and member_idx = ? AND member_bet_detail.bet_type = lsports_fixtures.bet_type ";
            $arrMbBtResult = $memberBetModel->db->query($sql, [$idx, $member_idx])->getResultArray();

            if (0 == count($arrMbBtResult)) {
                return $this->fail('숨김처리할 데이터가 없습니다. ');
            }

            //$dt_current = date("Y-m-d H:i:s");
            $loseCount = 0;
            foreach ($arrMbBtResult as $value) {
                //$dt_start = $value['fixture_start_date'];
                //$bet_status = $value['bet_status'];
                $detail_bet_status = $value['detail_bet_status'];
                //$endTime = date("Y-m-d H:i:s", strtotime($dt_start . "-" . 30 . " minutes"));
                //if ($endTime > $dt_current && $bet_status != 5) {
                //    return $this->fail('숨김처리 할 수 없는 경기입니다.');
                //}
                
                if(4 == $detail_bet_status){
                   ++$loseCount; 
                }
                
            }
            $bet_status = $arrMbBtResult[0]['bet_status'];
            if(1 == $bet_status && 0 == $loseCount){
                return $this->fail('숨김처리 할 수 없는 경기입니다.');
            }

            $sql = "update member_bet set is_hide = 1 where idx = ? and member_idx = ?";
            $memberBetModel->db->query($sql, [$idx, $member_idx]);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('bettingHide [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: bettingCancel query : ' . $memberBetModel->getLastQuery());
            return $this->fail('디비 처리 에러 입니다.');
        }
        $response = [
            'result_code' => 200,
            'messages' => '배팅 숨기기 성공.',
        ];
        return $this->respond($response, 200);
    }

    public function bettingMiniGameHide() {
        $idx = $_POST['idx'];
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $idx) || !is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::bettingHide: ==> ");
            die();
        }

        $memberBetModel = new MemberBetModel();
        try {

            $sql = "select member_idx,bet_status from mini_game_member_bet where idx = ?";
            $arrMbBtResult = $memberBetModel->db->query($sql, [$idx])->getResultArray();
            if (0 == count($arrMbBtResult)) {
                return $this->fail('숨김처리할 데이터가 없습니다. ');
            }

            foreach ($arrMbBtResult as $value) {

                if ($member_idx != $value['member_idx'] || (5 != $value['bet_status'] && 3 != $value['bet_status'])) {
                    return $this->fail('숨김처리 할 수 없는 경기입니다.');
                }
            }

            $sql = "update mini_game_member_bet set is_hide = 1 where idx = ?";
            $memberBetModel->db->query($sql, [$idx]);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('bettingMiniGameHide [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: bettingMiniGameHide query : ' . $memberBetModel->getLastQuery());
            return $this->fail('디비 처리 에러 입니다.');
        }
        $response = [
            'result_code' => 200,
            'messages' => '배팅 숨기기 성공.',
        ];
        return $this->respond($response, 200);
    }

    public function bettingAllHide() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';
        if ($member_idx == NULL) {
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $member_idx)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::bettingHide: ==> ");
            die();
        }

        $memberBetModel = new MemberBetModel();
        try {
            $sql = "select member_bet.idx, member_bet.bet_status
                     ,IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity,lsports_fixtures.fixture_start_date) as fixture_start_date
                     from member_bet
                    left join member_bet_detail on member_bet.idx = member_bet_detail.bet_idx 
                    left join lsports_fixtures on member_bet_detail.ls_fixture_id = lsports_fixtures.fixture_id 
                    where member_idx = ? and is_hide = 0 AND member_bet_detail.bet_type = lsports_fixtures.bet_type group by member_bet.idx ";
            $arrMbBtResult = $memberBetModel->db->query($sql, [$member_idx])->getResultArray();

            $dt_current = date("Y-m-d H:i:s");
            $arrHideList = array();
            foreach ($arrMbBtResult as $value) {
                $dt_start = $value['fixture_start_date'];
                $bet_status = $value['bet_status'];
                $endTime = date("Y-m-d H:i:s", strtotime($dt_start . "-" . 30 . " minutes"));
                if ($endTime < $dt_current || $bet_status == 5) {
                    $arrHideList[] = $value['idx'];
                }
            }

            if (0 == count($arrHideList)) {
                return $this->fail('숨김처리할 데이터가 없습니다. ');
            }
            $arrHideList = implode(',', $arrHideList);
            $sql = "update member_bet set is_hide = 1 where member_idx = ? and idx in ($arrHideList)";
            $memberBetModel->db->query($sql, [$member_idx]);
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('bettingAllHide [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: bettingAllHide query : ' . $memberBetModel->getLastQuery());
            return $this->fail('디비 처리 에러 입니다.');
        }

        $response = [
            'result_code' => 200,
            'messages' => '배팅 숨기기 성공.',
        ];
        return $this->respond($response, 200);
    }

    // 카지노, 슬롯 베팅내역
    public function casinoBettingHistory() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
        $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
        $prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'C';
        $prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 1;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $clickItemNum = isset($_REQUEST['clickItemNum']) ? $_REQUEST['clickItemNum'] : NULL;

        $start_limit = ($page - 1) * 10;

        // 마지막 시간에 하루 더해준다.
        $timestamp = strtotime($betToDate . "+1 days");
        $betToDate = date("Y-m-d H:i:s", $timestamp);

        if ($member_idx == NULL || null == session()->get('call')) {
            //$url = base_url("/$viewRoot/index");
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $member_idx) || !is_int((int) $prd_id) || !is_int((int) $page) || !is_int((int) $clickItemNum)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::casinoBettingHistory: ==> ");
            die();
        }

        $memberBetModel = new MemberBetModel();

        try {
            // 사용하는 게임종류
            $sql = "SELECT PRD_ID, PRD_NM FROM KP_PRD_INF WHERE TYPE = ? AND IS_USE = 1";
            $prdList = $memberBetModel->db->query($sql, [$prd_type])->getResultArray();

            // 상세게임명
            $sql = "SELECT PRD_ID, PRD_NM FROM KP_PRD_INF WHERE TYPE = ? AND IS_USE = 1";
            $prdList = $memberBetModel->db->query($sql, [$prd_type])->getResultArray();

            // 베팅내역
            $param_cnt = array();
            $param = array();
            if ('C' == $prd_type) {
                $sql_cnt = "SELECT count(*) AS CNT FROM KP_CSN_BET_HIST WHERE PRD_ID = ? AND MBR_IDX = ?"
                        . " AND REG_DTM >= ? and REG_DTM <= ? order by CSN_BET_IDX desc";
                array_push($param_cnt, $prd_id, $member_idx, $betFromDate, $betToDate);
                $sql = "SELECT * FROM KP_CSN_BET_HIST WHERE PRD_ID = ? AND MBR_IDX = ?"
                        . " AND REG_DTM >= ? and REG_DTM <= ? order by CSN_BET_IDX desc limit ?, 10";
                array_push($param, $prd_id, $member_idx, $betFromDate, $betToDate, $start_limit);
            } else {
                $sql_cnt = "SELECT count(*) AS CNT FROM KP_SLOT_BET_HIST WHERE MBR_IDX = ?"
                        . " AND REG_DTM >= ? and REG_DTM <= ? order by SLOT_BET_IDX desc";
                array_push($param_cnt, $member_idx, $betFromDate, $betToDate);
                $sql = "SELECT * FROM KP_SLOT_BET_HIST WHERE MBR_IDX = ?"
                        . " AND REG_DTM >= ? and REG_DTM <= ? order by SLOT_BET_IDX desc limit ?, 10";
                array_push($param, $member_idx, $betFromDate, $betToDate, $start_limit);
            }
            $betList_cnt = $memberBetModel->db->query($sql_cnt, $param_cnt)->getResultArray();
            $betList = $memberBetModel->db->query($sql, $param)->getResultArray();

            // prd list
            $sql = "select PRD_ID, PRD_NM from KP_PRD_INF";
            $tmList = $memberBetModel->db->query($sql)->getResultArray();

            $prdList = array();
            if (count($tmList) > 0) {
                foreach ($tmList as $key => $value) {
                    $prdList[$value['PRD_ID']] = $value['PRD_NM'];
                }
            }

            // game list
            $sql = "select GAME_ID, GAME_NM from KP_GAME_INF";
            $tmList = $memberBetModel->db->query($sql)->getResultArray();

            $gameList = array();
            if (count($tmList) > 0) {
                foreach ($tmList as $key => $value) {
                    $gameList[$value['GAME_ID']] = $value['GAME_NM'];
                }
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: casinoBettingHistory query : ' . $memberBetModel->getLastQuery());
        }

        return view("$viewRoot/casino_betting_list", [
            'page' => $page,
            'prd_type' => $prd_type,
            'prd_id' => $prd_id,
            'prdList' => $prdList,
            'betList' => $betList,
            'totalCnt' => $betList_cnt[0]['CNT'],
            'gameList' => $gameList,
            'clickItemNum' => $clickItemNum
        ]);
    }

    // 이스포츠, 키론
    public function esportsBettingHistory() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
        $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
        $prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'e';
        $prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 101;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $start_limit = ($page - 1) * 10;

        // 마지막 시간에 하루 더해준다.
        $timestamp = strtotime($betToDate . "+1 days");
        $betToDate = date("Y-m-d H:i:s", $timestamp);

        if ($member_idx == NULL || null == session()->get('call')) {
            //$url = base_url("/$viewRoot/index");
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $member_idx) || !is_int((int) $prd_id) || !is_int((int) $page)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::esportsBettingHistory: ==> ");
            die();
        }

        $memberBetModel = new MemberBetModel();
        $param_cnt = array();
        $param = array();
        try {
            $sql_cnt = "SELECT count(*) AS CNT FROM KP_ESPT_BET_HIST WHERE PRD_ID = ? AND MBR_IDX = ?"
                    . " AND REG_DTM >= ? and REG_DTM <= ? order by SLOT_BET_IDX desc";
            array_push($param_cnt, $prd_id, $member_idx, $betFromDate, $betToDate);

            $sql = "SELECT * FROM KP_ESPT_BET_HIST WHERE PRD_ID = ? AND MBR_IDX = ?"
                    . " AND REG_DTM >= ? and REG_DTM <= ? order by SLOT_BET_IDX desc limit ?, 10";
            array_push($param, $prd_id, $member_idx, $betFromDate, $betToDate, $start_limit);
            $betList_cnt = $memberBetModel->db->query($sql_cnt, $param_cnt)->getResultArray();
            $betList = $memberBetModel->db->query($sql, $param)->getResultArray();

            // prd list
            $sql = "select PRD_ID, PRD_NM from KP_PRD_INF";
            $tmList = $memberBetModel->db->query($sql)->getResultArray();

            $prdList = array();
            if (count($tmList) > 0) {
                foreach ($tmList as $key => $value) {
                    $prdList[$value['PRD_ID']] = $value['PRD_NM'];
                }
            }
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: esportsBettingHistory query : ' . $memberBetModel->getLastQuery());
        }
        return view("$viewRoot/esports_betting_list", [
            'page' => $page,
            'prd_type' => $prd_type,
            'prd_id' => $prd_id,
            'prdList' => $prdList,
            'betList' => $betList,
            'totalCnt' => $betList_cnt[0]['CNT'],
            'prdList' => $prdList
        ]);
    }

    // 이스포츠, 키론
    public function hashBettingHistory() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();
        //$member_idx = 19;
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
        $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
        //$prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'e';
        //$prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 101;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $clickItemNum = isset($_GET['clickItemNum']) ? $_GET['clickItemNum'] : 0;

        $start_limit = ($page - 1) * 10;

        // 마지막 시간에 하루 더해준다.
        $timestamp = strtotime($betToDate . "+1 days");
        $betToDate = date("Y-m-d H:i:s", $timestamp);

        if ($member_idx == NULL || null == session()->get('call')) {
            //$url = base_url("/$viewRoot/login");
            return redirect()->to(base_url("/$viewRoot/index"));
        }

        if (!is_int((int) $member_idx) || !is_int((int) $page) || !is_int((int) $clickItemNum)) {
            $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::hashBettingHistory: ==> ");
            die();
        }

        $memberBetModel = new MemberBetModel();
        $param_cnt = array();
        $param = array();
        try {
            $sql_cnt = "SELECT count(*) AS CNT FROM OD_HASH_BET_HIST WHERE MBR_IDX = ?"
                    . " AND REG_DTM >= ? and REG_DTM <= ? order by HASH_BET_IDX desc";
            array_push($param_cnt, $member_idx, $betFromDate, $betToDate);
            $sql = "SELECT * FROM OD_HASH_BET_HIST WHERE MBR_IDX = ?"
                    . " AND REG_DTM >= ? and REG_DTM <= ? order by HASH_BET_IDX desc limit ?, 10";
            array_push($param, $member_idx, $betFromDate, $betToDate, $start_limit);

            $betList_cnt = $memberBetModel->db->query($sql_cnt, $param_cnt)->getResultArray();
            $betList = $memberBetModel->db->query($sql, $param)->getResultArray();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: hashBettingHistory query : ' . $memberBetModel->getLastQuery());
        }
        return view("$viewRoot/hash_betting_list", [
            'page' => $page,
            'clickItemNum' => $clickItemNum,
            //'prd_type' => $prd_type,
            //'prd_id' => $prd_id,
            'betList' => $betList,
            'totalCnt' => $betList_cnt[0]['CNT'],
                //'prdList' => $prdList
        ]);
    }
    
    public function holdemBettingHistory() {
        $member_idx = session()->get('member_idx');
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'mobile';

        $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
        $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
        $prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'C';
        $prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 1;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $clickItemNum = isset($_REQUEST['clickItemNum']) ? $_REQUEST['clickItemNum'] : NULL;
        
        $start_limit = ($page - 1) * 10;

        // 마지막 시간에 하루 더해준다.
        $timestamp = strtotime($betToDate . "+1 days");
        $betToDate = date("Y-m-d H:i:s", $timestamp);

        if ($member_idx == NULL || null == session()->get('call')) {
            $url = base_url("/$viewRoot/login");
            return redirect()->to(base_url("/$viewRoot/login"));
        }

        $memberBetModel = new MemberBetModel();
        try {
            // 베팅내역
            $sql_cnt = "SELECT count(*) AS CNT FROM HOLDEM_BET_HIST WHERE MBR_IDX = $member_idx"
                    . " AND REG_DTM >= '$betFromDate' and REG_DTM <= '$betToDate' order by HOLDEM_BET_IDX desc";

            $sql = "SELECT * FROM HOLDEM_BET_HIST WHERE MBR_IDX = $member_idx"
                    . " AND REG_DTM >= '$betFromDate' and REG_DTM <= '$betToDate' order by HOLDEM_BET_IDX desc limit $start_limit, 10";
            $betList_cnt = $memberBetModel->db->query($sql_cnt)->getResultArray();
            $betList = $memberBetModel->db->query($sql)->getResultArray();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $this->logger->error('::::::::::::::: casinoBettingHistory query : ' . $tMessageModel->getLastQuery());
            $this->logger->error('::::::::::::::: casinoBettingHistory query : ' . $memberBetDetailModel->getLastQuery());
        } catch (\Exception $e) {
            $this->logger->error('tMessageModel Exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
        } catch (\ReflectionException $e) {
            $this->logger->error('::::::::::::::: tMessageModel ReflectionException : ' . $e);
        }

        return view("$viewRoot/holdem_betting_list", [
            'page' => $page,
            //'prd_type' => $prd_type,
            //'prd_id' => $prd_id,
            'betList' => $betList,
            'totalCnt' => $betList_cnt[0]['CNT'],
            'clickItemNum' => $clickItemNum
        ]);
    }

    // 배팅내역
    public function betting_history() {
      
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
            $clickItemNum = isset($_GET['clickItemNum']) ? $_GET['clickItemNum'] : 0;

            if($clickItemNum == 1 || $clickItemNum == 2 || $clickItemNum == 3 || $clickItemNum == 6){
                try {

                    $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
                    $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');

                    $_GET['bet_group'] = isset($_GET['bet_group']) ? $_GET['bet_group'] : 1;

                    
                    
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    // 마지막 시간에 하루 더해준다.
                    $timestamp = strtotime($betToDate . "+1 days");
                    $betToDate = date("Y-m-d H:i:s", $timestamp);

                    if ($member_idx == NULL || null == session()->get('call')) {
                        $url = base_url("/$viewRoot/index");
                        //$this->logger->debug('url : ' . $url);
                        return redirect()->to(base_url("/$viewRoot/index"));
                    }
                    if (!is_int((int) $member_idx) || !is_int((int) $_GET['bet_group']) || !is_int((int) $page) || !is_int((int) $clickItemNum)) {
                        $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::casinoBettingHistory: ==> ");
                        die();
                    }

                    $this->initMemberData(session(), $member_idx);

                    $memberModel = new MemberModel();
                    $member = $memberModel->getMemberWhereIdx($member_idx);

                    $arr = str_split($member->getAccountNumber());
                    for ($i = 2; $i < count($arr) - 2; ++$i) {
                        $arr[$i] = "*";
                    }

                    $str_at_number = implode(" ", $arr);
                    $member->setAccountNumber($str_at_number);

                    $name = $member->getAccountName();
                    $sub_name1 = "";
                    $sub_name2 = "";
                    $name_len = mb_strlen($name);

                    $call = $member->getCall();
                    $call_len = strlen($call);

                    $call = substr_replace($call, "****", $call_len - 4, 4);

                    $member->setCall($call);
                    if (2 == $name_len) {
                        $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
                        $name = $sub_name1 . "*";
                    } else if (3 == $name_len) {
                        $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
                        $sub_name2 = iconv_substr($name, 2, 2, "utf-8");
                        $name = $sub_name1 . "*" . $sub_name2;
                    } else if (4 == $name_len) {
                        $sub_name1 = iconv_substr($name, 0, 1, "utf-8");
                        $sub_name2 = iconv_substr($name, 3, 3, "utf-8");
                        $name = $sub_name1 . "**" . $sub_name2;
                    }

                    $member->setAccountName($name);

                    $memberBetModel = new MemberBetModel();
                    $memberBetModelCount = new MemberBetModel();
                    $memberBetDetailModel = new MemberBetDetailModel();
                    $miniGameMemberBetModel = new MiniGameMemberBetModel();
                    $miniGameMemberBetModelCount = new MiniGameMemberBetModel();

                
                    $menu = 'b'; // a 이면 내정보 b면 배팅내역
                    $recommentChargeInfo = array();

                    if ($menu == 'b') {
                        $recommendList = [];

                        // 스포츠, 실시간
                        if ($_GET['bet_group'] < 3 || $_GET['bet_group'] == 9) {
                            if ($_GET['bet_group'] == 1)
                                $bet_type = 2;
                            else
                                $bet_type = 1;
                            
                            // classic check
                            $is_classic = 'OFF';
                            if($_GET['bet_group'] == 9){
                                $is_classic = 'ON';
                            }
                            
                            $betList = $memberBetModel
                                    ->select(['member_bet.*', 'i.name as item_name', 'i.type', 'i.value', '0 as item_bonus_price'])
                                    ->where('member_bet.member_idx', $member->getIdx());

                            $betListCnt = $memberBetModelCount
                                    ->select(['member_bet.*'])
                                    ->where('member_bet.member_idx', $member->getIdx());

                            $betList = $betList->where('member_bet.create_dt > ', $betFromDate)
                                    ->where('member_bet.create_dt < ', $betToDate)
                                    ->where('member_bet.bet_type = ', $bet_type)
                                    ->where('member_bet.is_hide = ', 0)
                                    ->where('member_bet.is_classic = ', $is_classic)
                                    ->join('member_bet_detail as detail', 'member_bet.idx = detail.bet_idx', 'left')
                                    ->join('lsports_fixtures as lf', 'detail.ls_fixture_id = lf.fixture_id', 'left')
                                    ->join('lsports_sports as ls', 'lf.fixture_sport_id = ls.id', 'left')
                                    ->join('member_item as mi', 'mi.idx = member_bet.item_idx', 'left')
                                    ->join('item as i', 'mi.item_id = i.id', 'left')
                                    ->groupBy('idx')
                                    ->orderBy('member_bet.idx', 'desc');

                            $betListCnt = $betListCnt->where('member_bet.create_dt > ', $betFromDate)
                                    ->where('member_bet.create_dt < ', $betToDate)
                                    ->where('member_bet.bet_type = ', $bet_type)
                                    ->where('member_bet.is_hide = ', 0)
                                    ->where('member_bet.is_classic = ', $is_classic)
                                    ->join('member_bet_detail as detail', 'member_bet.idx = detail.bet_idx', 'left')
                                    ->join('lsports_fixtures as lf', 'detail.ls_fixture_id = lf.fixture_id', 'left')
                                    ->groupBy('idx')
                                    ->orderBy('member_bet.idx', 'desc');

                            $betList = $betList->limit(10, ($page - 1) * 10)->find();
                            $betListCnt = $betListCnt->find();
                        } else {  // 미니게임
                            $mini_group_data = [];
                            if(3 == $_GET['bet_group']){
                            $mini_group_data = [3,4,5,15];
                            } else{
                            $mini_group_data = [6];
                            }
                            $betList = $miniGameMemberBetModel
                                    ->select(['mini_game_member_bet.idx', 'mini_game_member_bet.bet_type', 'total_bet_money', 'bet_price', 'bet_status', 'cnt', 'create_dt', 'ls_fixture_id', 'ls_markets_name', 'take_money', 'is_open', 'is_hide', 'mini_game.result', 'mini_game.result_score'])
                                    ->join('mini_game', 'mini_game_member_bet.ls_fixture_id = mini_game.id', 'left')
                                    ->where('mini_game_member_bet.member_idx', $member->getIdx())
                                    ->where('create_dt > ', $betFromDate)
                                    ->where('create_dt < ', $betToDate)
                                    ->where('is_hide', 0)
                                    ->whereIn('mini_game_member_bet.bet_type', $mini_group_data)    
                                    ->orderBy('idx', 'desc');

                            $betListCnt = $miniGameMemberBetModelCount
                                    ->select(['mini_game_member_bet.*'])
                                    ->where('mini_game_member_bet.member_idx', $member->getIdx())
                                    ->where('create_dt > ', $betFromDate)
                                    ->where('create_dt < ', $betToDate)
                                    ->whereIn('mini_game_member_bet.bet_type', $mini_group_data)    
                                    ->orderBy('idx', 'desc');

                            $betList = ($betList->limit(10, ($page - 1) * 10)->find());
                            $betListCnt = $betListCnt->find();
                        }

                        $betListCnt = count($betListCnt);
                    } else {
                        $betList = [];
                        $betListCnt = 0;

                        // 추천인 리스트를 가져온다.
                        $sql_count = "SELECT id FROM member where recommend_member =  $member_idx";

                        $recommendCountList = $memberModel->db->query(
                                        $sql_count
                                )->getResultArray();

                        $recommendAllCount = count($recommendCountList);

                        $start_page = ($page - 1) * 10;
                        $sql = "SELECT id,idx,nick_name,recommend_code_dt,reg_time  FROM member where recommend_member =  ? order by reg_time desc limit ?,10";
                        $recommendList = $memberModel->db->query($sql,[$member_idx,$start_page])->getResultArray();

                        if ($recommendAllCount > 0) {
                            $recomment_idxs = array();
                            foreach ($recommendList as $key => $value) {
                                $recomment_idxs[] = $value['idx'];
                            }
                            $recomment_idxs = implode(',', $recomment_idxs);

                            // 추천회원 리스트
                            $sql = "SELECT id,idx,nick_name,recommend_code_dt,reg_time  FROM member where recommend_member =  ? order by reg_time desc limit ?,10";
                            $recommendList = $memberModel->db->query($sql,[$member_idx,$start_page])->getResultArray();

                            $sql = "SELECT member_idx, count(idx) as cnt, sum(money) as money FROM member_money_charge_history where member_idx in ($recomment_idxs) and status = 3 group by member_idx;";
                            $recommentChargeInfo = $memberModel->db->query($sql)->getResultArray();
                        }
                    }

                    $betIds = array();
                    if ($betListCnt > 0 && ($_GET['bet_group'] < 3 || $_GET['bet_group'] == 9)) {
                        foreach ($betList as $key => $bet) {
                            $betIds[] = $bet->idx;
                        }
                        $betIds = implode(',', $betIds);

                        $current_day = date("d");
                        $sql = "select
                        member_bet_detail.idx,
                        member_bet_detail.bet_idx,
                        member_bet_detail.bet_status,
                        member_bet_detail.ls_markets_name as markets_name,
                        member_bet_detail.ls_bet_id,
                        member_bet_detail.ls_markets_base_line as markets_base_line,
                        member_bet_detail.ls_markets_id as markets_id,
                        member_bet_detail.result_score,
                        member_bet_detail.ls_fixture_id as fixture_id,
                        -- IF('ON' = mb_bet.is_betting_slip ,bet.bet_price,member_bet_detail.bet_price) as bet_price,
                                member_bet_detail.bet_price as bet_price,
                        member_bet_detail.bet_name,
                        member_bet_detail.fixture_sport_id,
                        member_bet_detail.fixture_location_id,
                        member_bet_detail.fixture_league_id,
                        IF('ON' = lsports_fixtures.passivity_flag AND lsports_fixtures.fixture_start_date_passivity is NOT NULL ,lsports_fixtures.fixture_start_date_passivity,lsports_fixtures.fixture_start_date) as fixture_start_date,
                        p1.team_name as p1_team_name, p1.display_name as p1_display_name,
                        p2.team_name as p2_team_name, p2.display_name as p2_display_name,
                        league.display_name as league_display_name,
                        league.name as league_name,
                        league.image_path as league_image_path,
                        location.image_path as location_image_path,
                        ls.display_name,
                        ls.name
                        from member_bet_detail
                                LEFT JOIN       member_bet as mb_bet ON mb_bet.idx = member_bet_detail.bet_idx
                                -- LEFT JOIN       lsports_bet as bet ON member_bet_detail.ls_bet_id = bet.bet_id
                        LEFT JOIN 	lsports_participant as p1
                        ON	member_bet_detail.fixture_participants_1_id = p1.fp_id
                        LEFT JOIN 	lsports_participant as p2
                        ON	member_bet_detail.fixture_participants_2_id = p2.fp_id
                        LEFT JOIN 	lsports_leagues as league
                        ON	member_bet_detail.fixture_league_id = league.id
                        LEFT JOIN 	lsports_locations as location
                        ON	member_bet_detail.fixture_location_id = location.id
                        LEFT JOIN lsports_sports as ls
                        ON   member_bet_detail.fixture_sport_id = ls.id
                        LEFT JOIN lsports_fixtures
                        ON lsports_fixtures.fixture_id = member_bet_detail.ls_fixture_id
                        AND lsports_fixtures.bet_type  = member_bet_detail.bet_type
                        where
                        member_bet_detail.bet_idx in ($betIds)
                        and member_bet_detail.create_dt between ? and  ?
                        group by member_bet_detail.idx";

                        $betDetailResult = $memberBetDetailModel->db->query($sql, [$betFromDate, $betToDate])->getResultArray();
                        //$this->logger->error(json_encode($betDetailResult));
                        //$this->logger->critical("betDetailResult : " . $betDetailResult);

                        $betDetail = array();
                        foreach ($betDetailResult as $bet_detail) {
                            $betDetail[$bet_detail['bet_idx']][] = $bet_detail;
                        }

                        foreach ($betList as $key => $bet) {
                            $bet_price = 1;
                            $item_idx = $bet->item_idx;
                            $item_type = $bet->type;
                            $item_value = $bet->value;
                    
                            if (!empty($betDetail[$bet->idx])) {
                                foreach ($betDetail[$bet->idx] as $bet_detail) {
                                    $bet_price = $bet_price * $bet_detail['bet_price'];
                                }
                                $bet->betDetail = $betDetail[$bet->idx];
                                $bet->total_bet_price = $bet_price * $bet->bonus_price;
                            } else {
                                $bet->betDetail = [];
                                $bet->total_bet_price = 0000;
                            }

                            if (GM_ALLOCATION == $item_type) {
                                $item_bonus_price = $bet->total_bet_price * $item_value;
                                $bet->total_bet_price = $bet->total_bet_price + $item_bonus_price;
                                $bet->item_bonus_price = $item_bonus_price;
                            }
                        }
                    }

                    $timestamp = strtotime("-3 days");
            
                    $tMessageModel = new TMessageModel();

                    $member_idx = $member->getIdx();
                    $sql = "delete tm, tmlt from t_message as tm
                    left join t_message_list as tmlt
                    on tm.msg_idx = tmlt.idx
                    where tm.member_idx = ? and tm.reg_time < date_sub( now(),interval 7 day);";

                    $tMessageModel->db->query($sql,[$member_idx]);

                    $messages = $tMessageModel
                            ->select(['t_message.*', 'tml.title', 'tml.content', 'tml.a_id'])
                            ->join('t_message_list tml', 't_message.msg_idx = tml.idx', 'left')
                            ->where('t_message.member_idx', $member->getIdx())
                            ->where('t_message.is_delete', 0)
                            ->orderBy('t_message.idx', 'desc')
                            ->find();

                    // 포인트 사용이력을 가져온다. -7일 기준
                    if ('e' == $menu) {
                        $sql = "SELECT * FROM t_log_cash WHERE member_idx = ? AND ac_code IN (6,8,9,10,11,123,124,126,127,202,203) AND reg_time BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW() ORDER BY reg_time DESC ";
                        $pointList = $memberBetDetailModel->db->query($sql, [$member_idx])->getResultArray();
                    }
                } catch (\mysqli_sql_exception $e) {
                    $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
                    $this->logger->error('::::::::::::::: doTotalCalculate query : ' . $tMessageModel->getLastQuery());
                    $this->logger->error('::::::::::::::: doTotalCalculate query : ' . $memberBetDetailModel->getLastQuery());
                }

                $totalCnt = 0;
                if ('a' == $menu) {
                    $totalCnt = $recommendAllCount;
                } else if ('b' == $menu) {
                    $totalCnt = $betListCnt;
                }

                $sql = "SELECT
                            count(*) as cnt
                        FROM
                            member_item a
                            ,item b
                        WHERE
                            1=1
                        AND
                            a.item_id = b.id
                        AND
                            a.member_idx = ?
                        AND
                            a.status =0
                        AND
                            b.`type` = 3
                        AND
                            a.create_dt >= date_add(now(), interval -1 month);";

                $hitExceptionPatchItemCnt = $memberBetDetailModel->query($sql, [$member_idx])->getResultArray()[0]['cnt'];

                //$this->logger->critical("hitExceptionPatchItemCnt : ".$hitExceptionPatchItemCnt);
                //$this->logger->critical("betList : " . json_encode($betList));

                return view("$viewRoot/betting_history_new", [
                    'member' => $member,
                    'betList' => $betList,
                    'betListCnt' => $betListCnt,
                    'messageList' => $messages,
                    'page' => $page,
                    'recommendList' => $recommendList,
                    'totalCnt' => $totalCnt,
                    'menu' => $menu,
                    'recommentChargeInfo' => $recommentChargeInfo,
                    'clickItemNum' => $clickItemNum,
                    'hitExceptionPatchItemCnt' => $hitExceptionPatchItemCnt
                ]);
                
            }elseif ($clickItemNum == 4 || $clickItemNum == 5) {
                

                $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
                $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
                $prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'C';
                $prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 1;
                $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

                $start_limit = ($page - 1) * 10;

                // 마지막 시간에 하루 더해준다.
                $timestamp = strtotime($betToDate . "+1 days");
                $betToDate = date("Y-m-d H:i:s", $timestamp);

                if ($member_idx == NULL || null == session()->get('call')) {
                    //$url = base_url("/$viewRoot/index");
                    return redirect()->to(base_url("/$viewRoot/index"));
                }

                if (!is_int((int) $member_idx) || !is_int((int) $prd_id) || !is_int((int) $page) || !is_int((int) $clickItemNum)) {
                    $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::casinoBettingHistory: ==> ");
                    die();
                }

                $memberBetModel = new MemberBetModel();

                try {
                    // 사용하는 게임종류
                    $sql = "SELECT PRD_ID, PRD_NM FROM KP_PRD_INF WHERE TYPE = ? AND IS_USE = 1";
                    $prdList = $memberBetModel->db->query($sql, [$prd_type])->getResultArray();

                    // 상세게임명
                    $sql = "SELECT PRD_ID, PRD_NM FROM KP_PRD_INF WHERE TYPE = ? AND IS_USE = 1";
                    $prdList = $memberBetModel->db->query($sql, [$prd_type])->getResultArray();

                    // 베팅내역
                    $param_cnt = array();
                    $param = array();
                    if ('C' == $prd_type) {
                        $sql_cnt = "SELECT count(*) AS CNT FROM KP_CSN_BET_HIST WHERE PRD_ID = ? AND MBR_IDX = ?"
                                . " AND REG_DTM >= ? and REG_DTM <= ? order by CSN_BET_IDX desc";
                        array_push($param_cnt, $prd_id, $member_idx, $betFromDate, $betToDate);
                        $sql = "SELECT * FROM KP_CSN_BET_HIST WHERE PRD_ID = ? AND MBR_IDX = ?"
                                . " AND REG_DTM >= ? and REG_DTM <= ? order by CSN_BET_IDX desc limit ?, 10";
                        array_push($param, $prd_id, $member_idx, $betFromDate, $betToDate, $start_limit);
                    } else {
                        $sql_cnt = "SELECT count(*) AS CNT FROM KP_SLOT_BET_HIST WHERE MBR_IDX = ?"
                                . " AND REG_DTM >= ? and REG_DTM <= ? order by SLOT_BET_IDX desc";
                        array_push($param_cnt, $member_idx, $betFromDate, $betToDate);
                        $sql = "SELECT * FROM KP_SLOT_BET_HIST WHERE MBR_IDX = ?"
                                . " AND REG_DTM >= ? and REG_DTM <= ? order by SLOT_BET_IDX desc limit ?, 10";
                        array_push($param, $member_idx, $betFromDate, $betToDate, $start_limit);
                    }
                    $betList_cnt = $memberBetModel->db->query($sql_cnt, $param_cnt)->getResultArray();
                    $betList = $memberBetModel->db->query($sql, $param)->getResultArray();

                    // prd list
                    $sql = "select PRD_ID, PRD_NM from KP_PRD_INF";
                    $tmList = $memberBetModel->db->query($sql)->getResultArray();

                    $prdList = array();
                    if (count($tmList) > 0) {
                        foreach ($tmList as $key => $value) {
                            $prdList[$value['PRD_ID']] = $value['PRD_NM'];
                        }
                    }

                    // game list
                    $sql = "select GAME_ID, GAME_NM from KP_GAME_INF";
                    $tmList = $memberBetModel->db->query($sql)->getResultArray();

                    $gameList = array();
                    if (count($tmList) > 0) {
                        foreach ($tmList as $key => $value) {
                            $gameList[$value['GAME_ID']] = $value['GAME_NM'];
                        }
                    }
                } catch (\mysqli_sql_exception $e) {
                    $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
                    $this->logger->error('::::::::::::::: casinoBettingHistory query : ' . $memberBetModel->getLastQuery());
                }

                return view("$viewRoot/betting_history_new", [
                    'page' => $page,
                    'prd_type' => $prd_type,
                    'prd_id' => $prd_id,
                    'prdList' => $prdList,
                    'betList' => $betList,
                    'totalCnt' => $betList_cnt[0]['CNT'],
                    'gameList' => $gameList,
                    'clickItemNum' => $clickItemNum
                ]);
            }elseif ($clickItemNum == 9) {

                $betFromDate = isset($_REQUEST['betFromDate']) ? $_REQUEST['betFromDate'] : DateTimeUtil::getDayYmd('-7');
                $betToDate = isset($_REQUEST['betToDate']) ? $_REQUEST['betToDate'] : DateTimeUtil::getDayYmd('+1');
                //$prd_type = isset($_REQUEST['prd_type']) ? $_REQUEST['prd_type'] : 'e';
                //$prd_id = isset($_REQUEST['prd_id']) ? $_REQUEST['prd_id'] : 101;
                $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
                $start_limit = ($page - 1) * 10;

                // 마지막 시간에 하루 더해준다.
                $timestamp = strtotime($betToDate . "+1 days");
                $betToDate = date("Y-m-d H:i:s", $timestamp);

                if ($member_idx == NULL || null == session()->get('call')) {
                    //$url = base_url("/$viewRoot/login");
                    return redirect()->to(base_url("/$viewRoot/index"));
                }

                if (!is_int((int) $member_idx) || !is_int((int) $page) || !is_int((int) $clickItemNum)) {
                    $this->logger->info("!!!!!!!!!!!!!!!!!! ***************** BettingHistoryController::hashBettingHistory: ==> ");
                    die();
                }

                $memberBetModel = new MemberBetModel();
                $param_cnt = array();
                $param = array();
                try {
                    $sql_cnt = "SELECT count(*) AS CNT FROM OD_HASH_BET_HIST WHERE MBR_IDX = ?"
                            . " AND REG_DTM >= ? and REG_DTM <= ? order by HASH_BET_IDX desc";
                    array_push($param_cnt, $member_idx, $betFromDate, $betToDate);
                    $sql = "SELECT * FROM OD_HASH_BET_HIST WHERE MBR_IDX = ?"
                            . " AND REG_DTM >= ? and REG_DTM <= ? order by HASH_BET_IDX desc limit ?, 10";
                    array_push($param, $member_idx, $betFromDate, $betToDate, $start_limit);

                    $betList_cnt = $memberBetModel->db->query($sql_cnt, $param_cnt)->getResultArray();
                    $betList = $memberBetModel->db->query($sql, $param)->getResultArray();
                } catch (\mysqli_sql_exception $e) {
                    $this->logger->error('tMessageModel [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
                    $this->logger->error('::::::::::::::: hashBettingHistory query : ' . $memberBetModel->getLastQuery());
                }
                return view("$viewRoot/betting_history_new", [
                    'page' => $page,
                    'clickItemNum' => $clickItemNum,
                    //'prd_type' => $prd_type,
                    //'prd_id' => $prd_id,
                    'betList' => $betList,
                    'totalCnt' => $betList_cnt[0]['CNT'],
                        //'prdList' => $prdList
                ]);
            }
           
            
            
    }

    // 내정보
    public function memberInfo() {
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
        
        $member_idx = session()->get('member_idx');

        $memberModel = new MemberModel();
        $member = $memberModel->getMemberWhereIdx($member_idx);

        return view("$viewRoot/member_info", [
            'member' => $member
        ]);
    }

}
