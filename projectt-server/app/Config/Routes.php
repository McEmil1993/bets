<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

 $routes->get('/web/apply/delete', 'ApplyController::deleteHistory');

$routes->get('', 'HomeController::index');
$routes->get('/', 'HomeController::index');
$routes->get('/web/login', 'HomeController::index');
//$routes->get('/mobile/login', 'HomeController::login');
$routes->get('/member/join', 'HomeController::join');

$routes->get('/web/index', 'HomeController::index');
$routes->get('/mobile/index', 'HomeController::index');


$routes->get('/web', 'HomeController::index');
$routes->get('/mobile', 'HomeController::index');


$routes->get('/web/realtime', 'RealTimeController::index'); // game01
$routes->get('/web/sports', 'SportsController::index');     // game02
$routes->get('/web/minigame', 'MiniGameController::index'); // game03

$routes->get('/web/classic', 'PublisherController::classic');     // classic
$routes->post('/web/classic/ajax_index', 'ClassicController::ajax_index');     // classic ajax

// sports
// $routes->get('/web/sports', 'PublisherController::sports');
// $routes->post('/sports/ajax_index', 'SportsController::ajax_index');
// $routes->get('/web/realtime', 'RealTimeController::index'); // game01
// $routes->get('/web/sports', 'SportsController::index');     // game02
// $routes->get('/web/realtime', 'PublisherController::realtime'); // live sports

$routes->get('/web/kinoladder', 'MiniGameController::kinoladder'); // game03
$routes->get('/web/powerladder', 'MiniGameController::powerladder'); // game03
$routes->get('/web/premiumShip', 'MiniGameController::premiumShip'); // game03
$routes->get('/web/superLeague', 'MiniGameController::superLeague'); // game03
$routes->get('/web/worldCup', 'MiniGameController::worldCup'); // game03
$routes->get('/web/euroCup', 'MiniGameController::euroCup'); // game03

$routes->get('/web/virtualsoccer', 'MiniGameController::virtualsoccer'); // game03
$routes->get('/web/virtualsoccer', 'MiniGameController::virtualsoccer'); // game03

$routes->get('/web/pladder', 'MiniGameController::pladder'); // game03
$routes->get('/web/kladder', 'MiniGameController::kladder'); // game03
$routes->get('/web/bsoccer', 'MiniGameController::bsoccer'); // game03
$routes->get('/web/event', 'EventController::index');       // 이벤트 sub01 -> event
$routes->get('/web/event_view', 'EventController::viewEventDetail');       // 이벤트 상세 sub01view -> event_view
$routes->get('/web/betting_rules', 'BettingRulesController::index'); //sub02
$routes->get('/web/bnf_lvl', 'BettingRulesController::bnf_lvl'); //sub02a
$routes->get('/mobile/bnf_lvl', 'BettingRulesController::bnf_lvl'); //sub02a
$routes->post('/web/sports/ajax_add_fixtures', 'SportsController::ajax_add_fixtures');     // game02 - 스포츠 경기하나
//$routes->get('/web/ajax_lnb', 'SportsController::ajax_lnb');     // game02 left
//
//
$routes->get('/mobile/realtime', 'RealTimeController::index'); // game01
$routes->get('/mobile/sports', 'SportsController::index');     // game02
//$routes->get('/mobile/minigame', 'MiniGameController::index'); // game03
//$routes->get('/mobile/pladder', 'MiniGameController::pladder'); // game03
//$routes->get('/mobile/kladder', 'MiniGameController::kladder'); // game03
//$routes->get('/mobile/bsoccer', 'MiniGameController::bsoccer'); // game03
$routes->get('/mobile/event', 'EventController::index');       // sub01
$routes->get('/mobile/event_view', 'EventController::viewEventDetail');       // sub01view
$routes->get('/mobile/betting_rules', 'BettingRulesController::index'); //sub02
//$routes->get('/web/casinoGuide', 'CasinoGuideController::index');

//g머니
$routes->get('/web/gamble_shop', 'GmoneyController::index'); // 아이템 상점 진입시
$routes->get('/web/getMyItemList', 'GmoneyController::getMyItemList'); //내 보유아이템 리스트 호출
$routes->post('/web/buyItem', 'GmoneyController::buyItem'); // 상품 구매시
$routes->post('/web/useItem', 'GmoneyController::useItem'); // 아이템 사용시


// 게시판
$routes->get('/web/border', 'BorderController::index'); // 리스트 sub03 -> border
$routes->get('/web/border/write', 'BorderController::write'); // 글쓰기페이지 sub03write -> board_write
$routes->post('/web/border/writeDB', 'BorderController::writeDB'); // 글쓰기 처리 sub03writeDB

$routes->get('/web/border/update', 'BorderController::update'); // 상세 sub03view -> boarder_edit

$routes->post('/web/border/updateDB', 'BorderController::updateDB'); //sub03updateDB
$routes->post('/web/border/deleteDB', 'BorderController::deleteDB'); //sub03deleteDB
$routes->post('/web/border/registComment', 'BorderController::registComment'); //sub03view_regidstComment
$routes->post('/web/border/writeAddImage', 'BorderController::writeAddImage'); //sub03writeAddImage
$routes->post('/web/border/writeAddImageDB', 'BorderController::writeAddImageDB'); //sub03writeDB

$routes->get('/web/customer_service', 'CustomerServiceController::index'); // 고객센터 sub04 -> customer_service
$routes->get('/web/member_info', 'BettingHistoryController::memberInfo'); // 내정보 sub05 -> member_info
$routes->get('/web/betting_history', 'BettingHistoryController::betting_history'); //sub05

$routes->post('/web/betting_history/deleteMessage', 'BettingHistoryController::deleteMessage');
$routes->post('/web/betting_history/deleteAllMessage', 'BettingHistoryController::deleteAllMessage');
$routes->post('/web/betting_history/allReadMessage', 'BettingHistoryController::allReadMessage');
$routes->get('/web/apply', 'ApplyController::index'); // 충전하기 sub06 -> apply
$routes->get('/web/virtualAccount', 'ApplyController::index'); // 충전하기 sub06 -> apply
$routes->get('/web/exchange', 'ApplyController::exchange'); // 환전하기 sub14 -> exchange
$routes->get('/web/charge_exchange_history', 'ApplyController::renew_index'); // 충/환전내역 sub15 -> charge_exchange_history

$routes->get('/web/match_results', 'MatchResultsController::index'); //sub07

$routes->get('/web/memberList', 'MemberListController::index'); // 회원조회
$routes->get('/web/chargeList', 'ChargeListController::index'); // 충전관리
$routes->get('/web/calculateList', 'CalculateListController::index'); // 정산
$routes->post('/web/requestShopInfo', 'MemberListController::requestShopInfo'); // 상점설정 변경 요청
$routes->post('/web/getShopInfo', 'MemberListController::getShopInfo'); // 상점설정 가져오기
$routes->post('/web/confirmShopConfig', 'MemberListController::confirmShopConfig'); // 상점설정 적용하기
$routes->post('/web/joinShop', 'MemberListController::joinShop'); // 총판생성

$routes->get('/web/point_history', 'MemberController::pointHistory');	// 포인트내역  sub19 -> point_history
$routes->get('/web/change_password', 'MemberController::changePassword'); // 비밀번호 변경 sub17 -> change_password
$routes->get('/web/recommend_member', 'MemberController::recommendMember');	// 추천회원리스트 sub18 -> recommend_member
$routes->get('/web/note', 'MemberController::note');	// 쪽지함	sub20 -> message



$routes->get('/mobile/border', 'BorderController::index'); //sub03
$routes->get('/mobile/border/update', 'BorderController::update'); //sub03update
$routes->post('/mobile/border/updateDB', 'BorderController::updateDB'); //sub03updateDB
$routes->post('/mobile/border/deleteDB', 'BorderController::deleteDB'); //sub03deleteDB
$routes->get('/mobile/border/write', 'BorderController::write'); //sub03write
$routes->post('/mobile/border/writeDB', 'BorderController::writeDB'); //sub03writeDB
$routes->post('/mobile/border/writeAddImage', 'BorderController::writeAddImage'); //sub03writeAddImage
$routes->get('/mobile/customer_service', 'CustomerServiceController::index'); //sub04
//$routes->get('/mobile/betting_history', 'BettingHistoryController::index'); //sub05

$routes->post('/mobile/betting_history/deleteMessage', 'BettingHistoryController::deleteMessage');
$routes->post('/mobile/betting_history/allReadMessage', 'BettingHistoryController::allReadMessage');
$routes->get('/mobile/apply', 'ApplyController::index'); //sub06

$routes->get('/mobile/match_results', 'MatchResultsController::index'); //sub07

$routes->get('/mobile/memberList', 'MemberListController::index'); // 회원조회
$routes->get('/mobile/chargeList', 'ChargeListController::index'); // 충전관리
$routes->get('/mobile/calculateList', 'CalculateListController::index'); // 정산

// API
$routes->post('/member/login', 'MemberController::login');
$routes->get('/member/logout', 'MemberController::logout');
$routes->post('/member/join', 'MemberController::join');
$routes->post('/member/passwordChange', 'MemberController::passwordChange');
$routes->post('/member/passwordCheck', 'MemberController::passwordCheck');
$routes->post('/member/idCheck', 'MemberController::idCheck');
$routes->post('/member/nickNameCheck', 'MemberController::nickNameCheck');
$routes->post('/member/duplicateLoginCheck', 'MemberController::duplicateLoginCheck');
$routes->post('/member/pointToMoney', 'MemberController::pointToMoney'); // 포인트 머니전환
$routes->post('/member/requestAuthCode', 'MemberController::requestAuthCode'); // 인증코드 요청
$routes->post('/member/authCodeCheck', 'MemberController::authCodeCheck'); // 인증코드 체크
$routes->post('/member/checkRecommendCode', 'MemberController::checkRecommendCode');    // 추천인 코드 체크
$routes->post('/api/member/getRankList', 'MemberController::getRankList');
$routes->post('/api/member/setRecommentCode', 'MemberController::setRecommentCode'); // 추천인 코드 변경
$routes->post('/api/member/setMemberStatus', 'MemberController::setMemberStatus');

$routes->post('/sports/getFixtures', 'SportsController::getFixtures');
$routes->post('/sports/getFixtureMarkets', 'SportsController::getFixtureMarkets');
$routes->post('/sports/getListAjax', 'SportsController::getListAjax');

$routes->post('/api/account/getMyAccountInfo', 'AccountController::getMyAccountInfo');

$routes->post('/api/message/read', 'MessageController::messageRead');
$routes->post('/api/message/locationMessageCheck', 'MessageController::locationMessageCheck');
$routes->post('/api/message/getAllMessage', 'MessageController::getAllMessage');

$routes->post('/api/real_time/getMainRealTimeList', 'RealTimeController::getMainRealTimeList');
$routes->post('/api/real_time/getRealTimeGameLiveScore', 'RealTimeController::getRealTimeGameLiveScore');
// $routes->post('/api/real_time/getRealTimeGameLiveScoreList', 'RealTimeController::getRealTimeGameLiveScoreList');
$routes->post('/api/real_time/getRealTimeGameLiveScoreList', 'RealTimeController::getRenewRealTimeGameLiveScoreList');
$routes->post('/api/real_time/getRealTimeGameLiveScoreList_delMenu', 'RealTimeController::getRealTimeGameLiveScoreList_delMenu');
$routes->post('/api/real_time/checkRealTimeGameLiveScore', 'RealTimeController::checkRealTimeGameLiveScore');
$routes->post('/api/sport/getMainSportTimeList', 'SportsController::getMainSportTimeList'); // 스포츠 마감임박

$routes->post('/api/bet/addBet', 'BetController::addBet');
$routes->post('/api/bet/onAddBet', 'BetController::onAddBet');
$routes->post('/api/bet/addMiniBet', 'BetController::addMiniBet');
$routes->post('/api/bet/checkBet', 'BetController::checkBet');

$routes->post('/api/memberMoneyCharge/chargeRequest', 'MemberMoneyChargeHistoryController::chargeRequest');
$routes->post('/api/memberMoneyExchange/moneyExchange', 'MemberMoneyExchangeHistoryController::moneyExchange');
$routes->post('/api/memberMoneyCharge/depositNotice', 'MemberMoneyChargeHistoryController::depositNotice');
$routes->get('/api/memberMoneyCharge/auto_money_charge', 'MemberMoneyChargeHistoryController::auto_money_charge');

$routes->post('/api/customer_service/qna/add', 'CustomerServiceController::addQnA');
$routes->post('/api/customer_service/qna_1to1/add', 'CustomerServiceController::addQnAOneToOne');
$routes->post('/api/customer_service/qna/selectOne', 'CustomerServiceController::selectOne');
$routes->post('/api/customer_service/qna/delete', 'CustomerServiceController::deleteQnA');


$routes->post('/api/league/get_list', 'LSportsLeagueController::leagueList');
$routes->post('/api/location/get_list', 'LSportsLocationController::locationList');

// Init Data
$routes->get('/api/leagues_stm', 'LSportsInitController::getLeagues_stm');

$routes->get('/api/initData', 'LSportsInitController::initData');
$routes->get('/api/initMember', 'LSportsInitController::initMember');
$routes->get('/test/crontab', 'TestController::index');
$routes->get('/test/member', 'TestController::member');
$routes->get('/api/getLeagues', 'LSportsInitController::getLeagues');
$routes->get('/api/initDayWork', 'LSportsInitController::initDayWork'); // 일일초기화
$routes->get('/api/initTotalMemberCash', 'LSportsInitController::initTotalMemberCash'); // 일일통계
$routes->post('/api/betting_cancel', 'BettingHistoryController::bettingCancel'); //sub05
$routes->post('/api/betting_hide', 'BettingHistoryController::bettingHide');
$routes->post('/api/betting_all_hide', 'BettingHistoryController::bettingAllHide');
$routes->get('/api/getFixturesData', 'LSportsInitController::getFixturesData');
$routes->post('/api/bettingMiniGameHide', 'BettingHistoryController::bettingMiniGameHide');


$routes->get('/api/doTotalCalculate', 'LSportsInitController::doTotalCalculate');
$routes->get('/api/distributorCalculate', 'LSportsInitController::distributorCalculate'); // 일일 총판정산
$routes->get('/api/distributorCalculatePay', 'LSportsInitController::distributorCalculatePay'); // 총판정산 지급
//$routes->get('/api/userCalculate', 'LSportsInitController::userCalculate'); // 개별정산 보기
$routes->get('/api/real_doTotalCalculate', 'LSportsRealTimeInitController::real_doTotalCalculate'); // 실시간 정산


//스코어 자동 업데이트 기능  
//정산 완료된 배팅중 점수가 누수된것들을 보정해준다.
$routes->get('/api/doProcessScoreResult', 'LSportsInitController::doProcessScoreResult'); 

// 자동레벨업 기능
$routes->get('/api/doProcessAutoLevelup', 'LSportsInitController::doProcessAutoLevelup'); 


$routes->get('/test/rmqTest', 'TestController::rmq_test');
$routes->get('/test/rmqTest_1', 'TestController_1::rmq_test');

$routes->get('/test/rmqTestInplay', 'TestController::rmq_test_inplay');

$routes->get('/rmq/rmqInPlay', 'RmqController::rmqInPlay');
$routes->get('/rmq/rmqPreMatch', 'RmqController::rmqPreMatch');
$routes->get('/rmq/rmqRenewPreMatch', 'RmqController::rmqRenewPreMatch');
    
// 실시간
$routes->get('/api/getInPlayScheduleURL', 'LSportsRealTimeInitController::getInPlayScheduleURL');
$routes->get('/api/getOrderFixtures', 'LSportsRealTimeInitController::getOrderFixtures');
$routes->get('/api/cancelOrderFixtures', 'LSportsRealTimeInitController::cancelOrderFixtures');
$routes->get('/api/getViewOrderedFixtures', 'LSportsRealTimeInitController::getViewOrderedFixtures');
$routes->get('/api/getRenewViewOrderedFixtures', 'RenewLSportsRealTimeInitController::getViewOrderedFixtures');
$routes->get('/api/packageControl', 'LSportsRealTimeInitController::packageControl');

// 프리매치
$routes->get('/api/getFixturesDailyData', 'LSportsInitController::getFixturesDailyData');

// MOBILE
$routes->get('/m', 'HomeController::index');

// ADMIN
//$routes->get('/admin', 'AdminController::login');
//$routes->get('/admin/index', 'AdminController::index');
//$routes->post('/admin/loginCheck', 'AdminController::loginCheck');
//$routes->post('/admin/logout', 'AdminController::logout')//;

//$routes->get('/admin/member', 'AdminController::member');
//$routes->get('/admin/pre_match', 'AdminController::preMatch');
//$routes->get('/admin/mini_game', 'AdminController::miniGame');
//$routes->get('/admin/money', 'AdminController::money');
//$routes->get('/admin/statistics', 'AdminController::statistics');
//$routes->get('/admin/site', 'AdminController::site');
//$routes->get('/admin/board', 'AdminController::board');
//$routes->get('/api/test_total', 'LSportsInitController::testTotal');

// 복기
$routes->get('/api/inPlayDebug', 'RealDebugController::inPlayDebug');

// 미니게임
$routes->get('/minigame/pbInitData', 'MiniGameController::pbInitData');
//$routes->get('/minigame/pbIndex', 'MiniGameController::index');
$routes->get('/minigame/bsoccerData', 'MiniGameController::bsoccerData');
$routes->post('/minigame/getCurrentRound', 'MiniGameController::getCurrentRound');
$routes->post('/minigame/getCurrentRound2', 'MiniGameController::getCurrentRound2');
$routes->get('/minigame/getMiniGameData', 'MiniGameController::getMiniGameData');
$routes->get('/minigame/doMiniTotalCalculate', 'MiniGameController::doMiniTotalCalculate');
$routes->post('/minigame/selectMemberMiniGameBet', 'MiniGameController::selectMemberMiniGameBet');
$routes->post('/minigame/selectMiniGamePattenData', 'MiniGameController::selectMiniGamePattenData');
$routes->post('/minigame/getLnbTimer', 'MiniGameController::getLnbTimer');

// sms_test_url
$routes->get('/test/authvalidate', 'TestController::sms_test_authvalidate');
$routes->get('/test/authvalidate_get', 'TestController::sms_test_authvalidate_get');
$routes->get('/test/phpinfo', 'TestController::phpinfo');


$routes->get('/api/authvalidate', 'MemberMoneyChargeHistoryController::sms_authvalidate');
$routes->get('/api/authvalidate_get', 'MemberMoneyChargeHistoryController::sms_authvalidate_get');
$routes->get('/api/do_auto_charge', 'MemberMoneyChargeHistoryController::do_auto_charge');
$routes->get('/api/move_data_fix_bet', 'LSportsInitController::backupFixBetData');

// 코인 -> 게임머니
$routes->post('/api/setIsCoinGuid', 'MemberMoneyChargeHistoryController::setIsCoinGuid'); // 코인 충전 설명서 읽음처리
$routes->post('/api/chargeCoinRequest', 'MemberMoneyChargeHistoryController::chargeCoinRequest');
$routes->post('/api/getIsJoinCoinSite', 'MemberMoneyChargeHistoryController::getIsJoinCoinSite');
$routes->get('/api/coin_convert_money', 'MemberMoneyChargeHistoryController::coin_convert_money'); // 코인 -> 머니 전환 스케쥴
$routes->post('/api/getBalance', 'MemberMoneyChargeHistoryController::getBalance');

// 가상계좌
$routes->get('/api/memberMoneyCharge/markerSellerInfo', 'MemberMoneyChargeHistoryController::markerSellerInfo');
$routes->get('/api/memberMoneyCharge/virtual_auto_charge_money', 'MemberMoneyChargeHistoryController::virtual_auto_charge_money_renew');
$routes->post('/api/memberMoneyCharge/virtualChargeRequest', 'MemberMoneyChargeHistoryController::virtualChargeRequest_renew');

// 가상계좌 신규
$routes->post('/api/memberMoneyCharge/virtualDepositNotice', 'MemberMoneyChargeHistoryController::virtualDepositNotice');   // 결과통지

// pay-kiwoom
$routes->post('/api/chargePayKiwoomRequest', 'MemberMoneyChargeHistoryController::chargePayKiwoomRequest');

// 레벨변경(일주일 입금완료 없을시)
$routes->get('/api/memberMoneyCharge/changelevel', 'MemberMoneyChargeHistoryController::changelevel');

// 가상축구 영상
$routes->get('/api/bsoccerMovie', 'MiniGameController::bsoccerMovie');

// 점검화면
$routes->get('/web/inspection', 'HomeController::inspection');

// 동접자수
$routes->get('/api/updateCurrentUserCount', 'MemberController::updateCurrentUserCount');

// 카지노, 슬롯머신
$routes->get('/web/casino', 'CasinoController::index');
$routes->get('/web/casino/(:num)', static function ($id) {
	return view('web/casino', ['id' => $id]);
});
$routes->get('/web/slot', 'CasinoController::slot'); // 20220404 슬롯 분리 이종민
$routes->post('/web/playCasino', 'CasinoController::playCasino');
$routes->get('/web/casinoBettingHistory', 'BettingHistoryController::casinoBettingHistory');
$routes->get('/slots/(:any)', 'CasinoController::slotStart');
$routes->get('/web/play/(:any)', static function ($id) {
	return view('web/casino', ['id' => $id]);
});
$routes->get('/esports', 'CasinoController::esports');
$routes->get('/kiron_soccer', 'CasinoController::kiron_soccer');
$routes->get('/web/hash', 'CasinoController::hash');
$routes->get('/web/esportsBettingHistory', 'BettingHistoryController::esportsBettingHistory');
$routes->get('/web/hashBettingHistory', 'BettingHistoryController::hashBettingHistory');
$routes->get('/web/holdemBettingHistory', 'BettingHistoryController::holdemBettingHistory');

// 홀덤
$routes->get('/web/holdem', 'HoldemController::index');
$routes->post('/web/holdem_start', 'HoldemController::index');
$routes->post('/web/holdem_end_session', 'HoldemController::end_session');

$routes->post('/web/update_host', 'HoldemController::update_host');
$routes->post('/web/call_back', 'HoldemController::call_back');
$routes->get('/web/history_event', 'HoldemController::history_event');
$routes->post('/web/history_session', 'HoldemController::history_session');
$routes->post('/web/update_session', 'HoldemController::update_session');
$routes->get('/web/cron_end_session', 'HoldemController::cron_end_session');
$routes->post('/web/getHoldemToken', 'HoldemController::getHoldemToken');
$routes->post('/web/setHoldemToken', 'HoldemController::setHoldemToken');

$routes->post('/web/game-list', 'CasinoController::enterProductType');


// 프리매치 경기 시작 시간 지나도 마감 처리 안되는 경기 마감처리
$routes->get('/api/doProcessUpdateDisplayStatus', 'LSportsInitController::doProcessUpdateDisplayStatus');


$routes->post('/web/setBettingSlip', 'BetController::setBettingSlip');
$routes->get('/api/doInsertFromMydbToMainDb', 'HomeController::doInsertFromMydbToMainDb');

$routes->post('/api/write_access_log', 'AccessLogController::index');
// 실시간 베팅전 스코어 저장
$routes->post('/api/setRealTimeScore', 'RealTimeController::setRealTimeScore');

// token reflush
$routes->post('/api/tokenCheck', 'MemberController::tokenCheck');

$routes->get('/api/calPayBack', 'UserPayBackController::calPayBack');
$routes->get('/api/calDayChargeEvent', 'UserPayBackController::calDayChargeEvent');

$routes->get('/test/testChargeEvent', 'TestController::testChargeEvent');



/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
