<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\TLogCashModel;
use App\Models\MemberMoneyChargeHistoryModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Models\TGameConfigModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;
use App\Util\DateTimeUtil;
use App\Execute\DayChargeEvent;

class UserPayBackController extends BaseController {

    use ResponseTrait;

    public function schedulerRollingComps() {
        $this->calPayBackComps();
        $this->calBetLoseComps();
    }


    private function calPayBackComps() {

        $this->logger->error(':::::::::::::::  calPayBack start');
        $memberModel = new MemberModel();
        $tLogCashModel = new TLogCashModel();
        $sql = "select mb.money,mb.point,rcms.myself_chex
                ,if(parent.idx is null, 0,rcms.recommender_chex) as recommender_chex
                ,mb.recommend_member
                ,parent.u_business
                ,upb.* 
                from tb_user_pay_back_info as upb
                left join member as mb on upb.member_idx = mb.idx
                left join member as parent on mb.recommend_member = parent.idx
                left join tb_static_rolling_comps as rcms on rcms.level = mb.level and rcms.type = 'chex' 
                where mb.u_business ='1' for update";
        try {
            $memberModel->transStart();
            $result = $memberModel->db->query($sql, [])->getResultArray();

            foreach ($result as $value) {

                if (0 == $value['charge'] || $value['tot_bet_money'] < $value['charge']) {

                    $this->logger->error(':::::::::::::::  calPayBack error member_idx: ' . $value['member_idx']);
                    // 데이터를 이월시킨다.
                    $sql = "update tb_user_pay_back_info set last_charge = charge,last_exchange = exchange,last_tot_bet_money = tot_bet_money where member_idx = ?";
                    $memberModel->db->query($sql, [$value['member_idx']]);
                    continue;
                }

                $reward = ($value['charge'] - ($value['exchange'] + $value['money'])) * ($value['myself_chex'] * 0.01);

                if ($reward <= 0) {
                    // 데이터 초기화를 해야한다.
                    
                    $extern_data = array('charge'=>$value['charge'],'exchange'=>$value['exchange'],'money'=>$value['money'],'tot_bet_money'=>$value['tot_bet_money'],'pay_back_value'=>$value['myself_chex'] );
                    $a_comment = json_encode($extern_data);

                    $ukey = md5($value['member_idx'] . strtotime('now'));
                    $tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_PAY_BACK_REWARD_MYNUS_POINT, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'M', $a_comment);

                
                    $sql = "update tb_user_pay_back_info set charge = 0,exchange = 0,tot_bet_money = 0,last_charge = 0,last_exchange = 0,last_tot_bet_money = 0 where member_idx = ?";

                    $memberModel->db->query($sql, [$value['member_idx']]);
                    $this->logger->error(':::::::::::::::  calPayBack error reward: ' . $reward . ' data : ' . json_encode($value));
                    continue;
                }

                if (MAX_PAY_BACK_POINT < $reward) {
                    $reward = MAX_PAY_BACK_POINT;
                }

                $sql = "update member set point = point + ? where idx = ?";

                $memberModel->db->query($sql, [$reward, $value['member_idx']]);

                $sql = "update tb_user_pay_back_info set charge = 0,exchange = 0,tot_bet_money = 0 ,last_charge = 0,last_exchange = 0,last_tot_bet_money = 0 where member_idx = ?";

                $memberModel->db->query($sql, [$value['member_idx']]);

                //$a_comment = ' 페이백 포인트 지급 => ' . ' [' . $reward . '] '.'charge :'.$value['charge'].
                //        ' exchange:'.$value['exchange'].' money:'.$value['money'].' pay_back_value:'.$value['pay_back_value'];
                //$a_comment = ' 페이백 포인트 지급 => ' . ' [' . $reward . '] ';

                $charge = $value['charge'] - $value['last_charge'];
                $exchange = $value['exchange'] - $value['last_exchange'];
                //$a_comment = '[{(저번주 누적 미정산 입금액 : '. $value['last_charge'].'<br />'
                //.'+ 이번주 입금액 :'.$charge.')'.'<br />'
                //.' - '.'(저번주 출금액+이번주 출금액 :'.$value['exchange'].')}'.'<br />'
                //.' - (현재 보유머니:'.$value['money']. ')]=정산 포인트 : '.$reward;
                //$today = date("2022-06-21");
                $start_date = date("Y-m-d", strtotime("-7 day", time()));
                $month = (int) date("m", strtotime("-7 day", time()));

                $now_week = DateTimeUtil::getWeek($start_date); //date("Y-m-d") 입력도 가능합니다
                //echo $today."는 6월의".$now_week."번째 주 입니다.";

                $a_comment = '(' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(충전 ' . $value['charge'] . '원 / 환전 ' . $value['exchange'] . '원)';

                $ukey = md5($value['member_idx'] . strtotime('now'));
                $tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_PAY_BACK_REWARD_POINT, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'P', $a_comment);

                //$tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_PAY_BACK_REWARD_POINT, 0, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'P', $a_comment);

                if (0 < $value['recommender_chex'] && 1 == $value['u_business']) {

                    $reward = ($value['charge'] - $value['exchange']) * ($value['recommender_chex'] * 0.01);
                    if (0 < $reward) {
                        if (MAX_PAY_BACK_POINT < $reward) {
                            $reward = MAX_PAY_BACK_POINT;
                        }

                        $sql = "select point from member where idx = ? for update";
                        $recommander_result = $memberModel->db->query($sql, [$value['recommend_member']])->getResultArray();

                        $sql = "update member set point = point + ? where idx = ?";

                        $memberModel->db->query($sql, [$reward, $value['recommend_member']]);

                        $charge = $value['charge'] - $value['last_charge'];
                        $exchange = $value['exchange'] - $value['last_exchange'];

                        $start_date = date("Y-m-d", strtotime("-7 day", time()));
                        $month = (int) date("m", strtotime("-7 day", time()));

                        $now_week = DateTimeUtil::getWeek($start_date);

                        $a_comment = '(' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(충전 ' . $value['charge'] . '원 / 환전 ' . $value['exchange'] . '원)';

                        $ukey = md5($value['recommend_member'] . strtotime('now'));

                        $tLogCashModel->insertCashLog_3($ukey, $value['recommend_member'], RECOMMENDER_PAY_BACK_REWARD_POINT, 0, 0, 0, $reward, $recommander_result[0]['point'], $recommander_result[0]['point'] + $reward, 'P', $a_comment);
                    }
                }

                $this->logger->error(':::::::::::::::  calPayBack success reward: ' . $reward . ' data : ' . $a_comment);
            }

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  calPayBack error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  calPayBack query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
        }
    }

    private function calBetLoseComps() {

        $this->logger->error(':::::::::::::::  calPayBack start');
        $memberModel = new MemberModel();
        $tLogCashModel = new TLogCashModel();
        $sql = "select mb.money,mb.point,
	         group_concat(rcms.type) as types
                ,group_concat(rcms.myself_bet) as myself_bets
                ,group_concat(if(parent.u_business <> 1, 0,rcms.recommender_chex)) as recommender_bets
                ,group_concat(rcms.myself_lose) as myself_loses
                ,group_concat(if(parent.u_business <> 1, 0,rcms.recommender_lose)) as recommender_loses
                ,group_concat(upb.bet_total) as bet_totals
                ,group_concat(upb.lose_bet_total) as lose_bet_totals
                ,mb.recommend_member,upb.member_idx
                ,parent.point as p_point
                ,parent.u_business
                 from tb_user_rolling_comps as upb
                 left join member as mb on upb.member_idx = mb.idx
                 left join member as parent on mb.recommend_member = parent.idx
                 left join tb_static_rolling_comps as rcms on rcms.level = mb.level 
                 where mb.u_business ='1' and rcms.type = upb.type group by member_idx for update";

        try {
            $memberModel->transStart();
            $result = $memberModel->db->query($sql, [])->getResultArray();

            foreach ($result as $value) {

                $reward_point = 0;
                $recom_reward_point = 0;

                $types = explode(',', $value['types']);

                $myself_bets = explode(',', $value['myself_bets']);
                $recommender_bets = explode(',', $value['recommender_bets']);

                $myself_loses = explode(',', $value['myself_loses']);
                $recommender_loses = explode(',', $value['recommender_loses']);

                $bet_totals = explode(',', $value['bet_totals']);
                $lose_bet_totals = explode(',', $value['lose_bet_totals']);

                $p_u_business = $value['u_business'];
                $arr_sql_log = array();
                for ($i = 0; $i < count($types); ++$i) {
                    $type = $types[$i];
                    $myself_bet = $myself_bets[$i];
                    $recommender_bet = $recommender_bets[$i];

                    $bet_total = $bet_totals[$i];


                    $myself_lose = $myself_loses[$i];
                    $recommender_lose = $recommender_loses[$i];

                    $lose_bet_total = $lose_bet_totals[$i];

                    if (0 < $myself_bet && 0 < $bet_total) {
                        $reward = ($bet_total * $myself_bet * 0.01);
                        if (MAX_PAY_BACK_POINT < $reward) {
                            $reward = MAX_PAY_BACK_POINT;
                        }
                
                        $reward_point = $reward_point + $reward;
                        $start_date = date("Y-m-d", strtotime("-7 day", time()));
                        $month = (int) date("m", strtotime("-7 day", time()));
                        
                        $now_week = DateTimeUtil::getWeek($start_date);

                        $a_comment = $type . ' (' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(배팅 ' . $bet_total . '원 / 보상 ' . $reward . '원)';

                        $ukey = md5($value['member_idx'] . strtotime('now'));
                        //$tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_BET_BACK_REWARD_POINT, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'P', $a_comment);

                        // $insertSql = '('
                        //         . $ukey . ', '
                        //         . $value['member_idx'] . ', "'
                        //         . USER_BET_BACK_REWARD_POINT . '", '
                        //         . 'P' . '", '
                        //         . $_SERVER['REMOTE_ADDR'] . ', "'
                        //         . $reward . ', "'
                        //         . $value['point'] . ', "'
                        //         . $value['point'] + $reward . ', "'
                        //         . CodeUtil::TLogACCodeToStr(USER_BET_BACK_REWARD_POINT) . ' ' . $a_comment . ')';
                        // array_push($arr_sql_log, $insertSql);

                        $insertSql = '('
                                . "'" . $ukey . "', "
                                . $value['member_idx'] . ', "'
                                . USER_BET_BACK_REWARD_POINT . '", '
                                . '"P", "'
                                . $_SERVER['REMOTE_ADDR'] . '", "'
                                . $reward . '", "'
                                . $value['point'] . '", "'
                                . ($value['point'] + $reward) . '", "'
                                . CodeUtil::TLogACCodeToStr(USER_BET_BACK_REWARD_POINT) . ' ' . $a_comment . '")';
                        array_push($arr_sql_log, $insertSql);
                        $value['point'] = $value['point'] + $reward;
                    }

                    if (0 < $recommender_bet && 0 < $bet_total && 1 == $p_u_business) {
                        $reward = ($bet_total * $recommender_bet * 0.01);
                        if (MAX_PAY_BACK_POINT < $reward) {
                            $reward = MAX_PAY_BACK_POINT;
                        }
                        $recom_reward_point = $recom_reward_point + $reward;
                        $start_date = date("Y-m-d", strtotime("-7 day", time()));
                        $month = (int) date("m", strtotime("-7 day", time()));

                        $now_week = DateTimeUtil::getWeek($start_date);

                        $a_comment = $type . ' (' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(배팅 ' . $bet_total . '원 / 보상 ' . $reward . '원)';

                        $ukey = md5($value['recommend_member'] . strtotime('now'));
                        // $insertSql = '('
                        //         . $ukey . ', '
                        //         . $value['recommend_member'] . ', "'
                        //         . RECOMMENDER_BET_BACK_REWARD_POINT . '", '
                        //         . 'P' . '", '
                        //         . $_SERVER['REMOTE_ADDR'] . ', "'
                        //         . $reward . ', "'
                        //         . $value['p_point'] . ', "'
                        //         . $value['p_point'] + $reward . ', "'
                        //         . CodeUtil::TLogACCodeToStr(RECOMMENDER_BET_BACK_REWARD_POINT) . ' ' . $a_comment . ')';
                        $insertSql = '('
                                . "'" . $ukey . "', "
                                . $value['recommend_member'] . ', "'
                                . RECOMMENDER_BET_BACK_REWARD_POINT . '", '
                                . '"P", "'
                                . $_SERVER['REMOTE_ADDR'] . '", "'
                                . $reward . '", "'
                                . $value['p_point'] . '", "'
                                . ($value['p_point'] + $reward) . '", "'
                                . CodeUtil::TLogACCodeToStr(RECOMMENDER_BET_BACK_REWARD_POINT) . ' ' . $a_comment . '")';
                        array_push($arr_sql_log, $insertSql);

                        //$tLogCashModel->insertCashLog_3($ukey, $value['recommend_member'], RECOMMENDERBET_BACK_REWARD_POINT, 0, 0, 0, $reward, $value['p_point'], $value['p_point'] + $reward, 'P', $a_comment);
                        $value['p_point'] = $value['p_point'] + $reward;
                    }

                    // 낙첨 롤링

                    if (0 < $myself_lose && 0 < $lose_bet_total && 1 == $p_u_business) {
                        $reward = ($lose_bet_total * $myself_lose * 0.01);
                        if (MAX_PAY_BACK_POINT < $reward) {
                            $reward = MAX_PAY_BACK_POINT;
                        }
                        $reward_point = $reward_point + $reward;
                        $start_date = date("Y-m-d", strtotime("-7 day", time()));
                        $month = (int) date("m", strtotime("-7 day", time()));

                        $now_week = DateTimeUtil::getWeek($start_date);

                        $a_comment = $type . ' (' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(배팅 ' . $lose_bet_total . '원 / 보상 ' . $reward . '원)';

                        $ukey = md5($value['member_idx'] . strtotime('now'));
                        // //$tLogCashModel->insertCashLog_3($ukey, $value['member_idx'], USER_BET_LOSE_BACK_REWARD_POINT, 0, 0, 0, $reward, $value['point'], $value['point'] + $reward, 'P', $a_comment);
                        // $insertSql = '('
                        //         . $ukey . ', '
                        //         . $value['member_idx'] . ', "'
                        //         . USER_BET_LOSE_BACK_REWARD_POINT . '", '
                        //         . 'P' . '", '
                        //         . $_SERVER['REMOTE_ADDR'] . ', "'
                        //         . $reward . ', "'
                        //         . $value['point'] . ', "'
                        //         . $value['point'] + $reward . ', "'
                        //         . CodeUtil::TLogACCodeToStr(USER_BET_LOSE_BACK_REWARD_POINT) . ' ' . $a_comment . ')';

                        $insertSql = '('
                                . "'" . $ukey . "', "
                                . $value['member_idx'] . ', "'
                                . USER_BET_LOSE_BACK_REWARD_POINT . '", '
                                . '"P", "'
                                . $_SERVER['REMOTE_ADDR'] . '", "'
                                . $reward . '", "'
                                . $value['point'] . '", "'
                                . ($value['point'] + $reward) . '", "'
                                . CodeUtil::TLogACCodeToStr(USER_BET_LOSE_BACK_REWARD_POINT) . ' ' . $a_comment . '")';
                        array_push($arr_sql_log, $insertSql);

                        $value['point'] = $value['point'] + $reward;
                    }
                    
                    if (0 < $recommender_lose && 0 < $lose_bet_total && 1 == $p_u_business) {
                        $reward = ($lose_bet_total * $recommender_lose * 0.01);
                        if (MAX_PAY_BACK_POINT < $reward) {
                            $reward = MAX_PAY_BACK_POINT;
                        }
                        $recom_reward_point = $recom_reward_point + $reward;
                        $start_date = date("Y-m-d", strtotime("-7 day", time()));
                        $month = (int) date("m", strtotime("-7 day", time()));

                        $now_week = DateTimeUtil::getWeek($start_date);

                        $a_comment = $type . ' (' . $month . '월 ' . $now_week . '주차)' . '<br />' . '(배팅 ' . $lose_bet_total . '원 / 보상 ' . $reward . '원)';

                        $ukey = md5($value['recommend_member'] . strtotime('now'));
                        //$tLogCashModel->insertCashLog_3($ukey, $value['recommend_member'], RECOMMENDER_BET_LOSE_BACK_REWARD_POINT, 0, 0, 0, $reward, $value['p_point'], $value['p_point'] + $reward, 'P', $a_comment);
                        // $insertSql = '('
                        //         . $ukey . ', '
                        //         . $value['recommend_member'] . ', "'
                        //         . RECOMMENDER_BET_LOSE_BACK_REWARD_POINT . '", '
                        //         . 'P' . '", '
                        //         . $_SERVER['REMOTE_ADDR'] . ', "'
                        //         . $reward . ', "'
                        //         . $value['p_point'] . ', "'
                        //         . $value['p_point'] + $reward . ', "'
                        //         . CodeUtil::TLogACCodeToStr(RECOMMENDER_BET_LOSE_BACK_REWARD_POINT) . ' ' . $a_comment . ')';
                        $insertSql = '('
                        . "'" . $ukey . "', "
                        . $value['recommend_member'] . ', "'
                        . RECOMMENDER_BET_LOSE_BACK_REWARD_POINT . '", '
                        . '"P", "'
                        . $_SERVER['REMOTE_ADDR'] . '", "'
                        . $reward . '", "'
                        . $value['p_point'] . '", "'
                        . ($value['p_point'] + $reward) . '", "'
                        . CodeUtil::TLogACCodeToStr(RECOMMENDER_BET_LOSE_BACK_REWARD_POINT) . ' ' . $a_comment . '")';
                        array_push($arr_sql_log, $insertSql);
                        $value['p_point'] = $value['p_point'] + $reward;
                    }
                }

                if (0 < $reward_point) {
                    $sql = "update member set point = point + ? where idx = ?";
                    $memberModel->db->query($sql, [$reward_point, $value['member_idx']]);
                }
                if (0 < $recom_reward_point) {
                    $sql = "update member set point = point + ? where idx = ?";
                    $memberModel->db->query($sql, [$recom_reward_point, $value['recommend_member']]);
                }
                $arr_sql_log_cnt =  count($arr_sql_log);

                if (0 < $arr_sql_log_cnt) {
                    // $memberModel->db->query(
                    //         'INSERT INTO `t_log_cash` ('
                    //         . 'u_key, '
                    //         . 'member_idx, '
                    //         . 'ac_code, '
                    //         . 'm_kind, '
                    //         . 'u_ip, '
                    //         . 'point, '
                    //         . 'be_point, '
                    //         . 'af_point, '
                    //         . 'coment) VALUES '
                    //         . implode(',', $arr_sql_log)
                    // );
                    
                    $sql = 'INSERT INTO `t_log_cash` ('
                        . 'u_key, '
                        . 'member_idx, '
                        . 'ac_code, '
                        . 'm_kind, '
                        . 'u_ip, '
                        . 'point, '
                        . 'be_point, '
                        . 'af_point, '
                        . 'coment) VALUES '
                        . implode(', ', $arr_sql_log);
                    $memberModel->db->query($sql);    
                }
                $this->logger->error(':::::::::::::::  calPayBack success reward: ' . $reward . ' data : ' . $a_comment);
            }

            $sql = "update tb_user_rolling_comps set bet_total = 0,lose_bet_total = 0 where member_idx > 0 ";
            $memberModel->db->query($sql);

            $memberModel->db->transComplete();
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  calPayBack error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  calPayBack query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
        }
    }

    public function calDayChargeEvent() {
        $this->logger->error(':::::::::::::::  calDayChargeEvent start');
        $memberModel = new MemberModel();
        try {
            
            //$execute = new DayChargeEvent($this->logger);
            //$execute->checkReward();
            
            $memberModel->transStart();
            $sql = 'update tb_member_day_charge_event set count = 0, tot_charge = 0,tot_exchange = 0';
            $memberModel->db->query($sql);
            $sql = 'truncate tb_member_day_charge_event_reward_history';
            $memberModel->db->query($sql);
            $memberModel->db->transComplete();
            
        } catch (\mysqli_sql_exception $e) {
            $this->logger->error(':::::::::::::::  calDayChargeEvent error : ' . $e->getMessage());
            $this->logger->error(':::::::::::::::  calDayChargeEvent query : ' . $memberModel->getLastQuery());
            $memberModel->db->transRollback();
        }
    }

}
