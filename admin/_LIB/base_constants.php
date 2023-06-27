<?php

// define 고정값

define('SUCCESS', 1000);

define('FAIL_DB_CONNECT', -1);
define('FAIL_DB_CONNECT_MSG', '디비연결실패');

define('FAIL_TRANS_START', -10);
define('FAIL_TRANS_START_MSG', '디비 트랜잭션 오류');

define('FAIL_PARAM', -20);
define('FAIL_PARAM_MSG', '파라미터 오류');

define('FAIL_SECOND_PASS', -30);
define('FAIL_SECOND_PASS_MSG', '2차 비밀번호 오류');

define('FAIL_DB_SQL_EXCEPTION', -40);
define('FAIL_DB_SQL_EXCEPTION_MSG', '디비쿼리실행오류');

define('FAIL_EXCEPTION', -50);
define('FAIL_EXCEPTION_MSG', '구문오류');

define('FAIL_REFLECTION_EXCEPTION', -60);
define('FAIL_REFLECTION_EXCEPTION_MSG', '리프렉션 구문 오류');

define('FAIL_EMPTY_DATA', -70);
define('FAIL_EMPTY_DATA_MSG', '적용할 데이터가 없습니다.');

//t_adm_log => log_type 값 정의 
// 1 운영툴 로그인\\\\\\\\\\\\\\\\n2. 관리자 ip관리, 개별 ip 차단관리\\\\\\\\\\\\\\\\n3. 도메인 코드 관리\\\\\\\\\\\\\\\\n4. 레벨별 계좌설정\\\\\\\\\\\\\\\\n
// 5. 가입첫충\\\\\\\\\\\\\\\\n6. 등급별 베팅금액 설정\\\\\\\\\\\\\\\\n7. 사이트 설정\\\\\\\\\\\\\\\\n11. 관리자 아이디 관리\\\\\\\\n
// 20. 전체수동정산  21. 개별수동정산  22. 전체수동적특  23. 개별수동적특  ,24 전체 마감전, 25 개별 마감전 
// 24. 개별마켓ON/OFF  25. 경기ON/OFF  26. 일괄적용  27. 경기상태변경  28. 추천코드 변경  29. 정산요율\\\\\\\\n30. 회원상세-배팅가능  
// 31. 배팅타입관리  32. 리그관리  33. 미니게임on/off  34. 가상축구on/off  35. 이벤트충전 설정  36. 이벤트충전 상태\\\\\\\\n40. 비밀번호  
// 41. 환전비밀번호  42. 닉네임  43. 전화번호  44. 추천  45. 상태  46. 레벨  47. 자동렙업  48. 추천인  49. 계좌정보\\\\\\\\n
// 50. 계좌이름  51. 은행 52: 경기 수동관리 53 : 경기 자동관리 54 : 경기시작시간 변경 55 : 마켓 상태,배당 수동변경,56  수동해제
// 60. g_money 지급,61 g_money 차감 , 62 g_money 아이템 능력치 수정 , 62 g_money 아이템 추가 ,63 g_money 아이템 삭제 ,64 : g_money 관리자지급,65 : g_money 관리자회수 ,66 관리자 베팅취소로인한 아이템 반환
// 70. 관리자 머니 지급, 71. 관리자 머니 회수, 73. 관리자 포인트 지급, 74. 관리자 포인트 회수
// 81 : 유저 탈퇴 처리 
// 91. 생년월일, 92. 통신사
// 92. change u_business
// 93. change dist_type
// 94 보안모니터링 상태 변경
// 95 충전보너스 온오프
// 96 충전보너스 문구 변경
// 97 충전방식 변경


define('TOTAL_BF_CALC', 24); // 전체 마감 전
define('INDIVIDUAL_BF_CALC', 25); // 개별 마감 전

define('PASSIVITY_APPLY', 55); // 수동적용
define('PASSIVITY_RELEASE', 56); // 수동해제

define('PASSIVITY_MODIFY_STATUS', 57); // 경기 상태값 수동 변경 

define('PASSIVITY_MODIFY_START_DATE_RELEASE', 58); // 경기 시간값 수동 적용 해제 
define('PASSIVITY_MODIFY_STATUS_RELEASE', 59); // 경기 상태값 수동 적용 해제 

define('PASSIVITY_ALL_APPLY', 60); // 전체수동적용
define('PASSIVITY_ALL_RELEASE', 61); // 전체수동해제

define('USER_LEAVE', 81); // 유저 탈퇴
define('CHANGE_BUSINESS_TYPE', 92); //  change u_business
define('CHANGE_DIST_TYPE', 93); // change dist_type

define('CHARGE_BONUS_ONOFF', 95);
define('CHARGE_BONUS_DESC_CHANGE', 96);
define('CHARGE_TYPE_CHANGE', 97);


// t_log_cash => ac_code 값 정의
/*1:충전,2:환전,3:베팅,4:베팅취소,5:포인트전환(추가),6:포인트차감,7:베팅결과처리,8:이벤트충전,9:이벤트차감,10:포인트충전,
 * 11 : 낙첨 포인트 지급,101:충전요청,102:환전요청,103:계좌조회,\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\n             *
 *  111:충전요청취소,112:환전요청취소,113:충전취소,114:환전취소,121:관리자충전,122:관리자회수,
 * 123:관리자 포인트 충전, 124:관리자 포인트 회수,125 : 관리자 환전 회수,998:데이터복구,999:기타 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\n     \\\\\\\\n
 * 201 : 정산 취소,202 : 정산 포인트 취소,203 : 정산 추천인 포인트 취소  ,126 : 재정산으로 인한 정산 ->적특시 이미 지급된 포인트 관리자 회수\\n,
 * 127 : 재정산으로 인한 적특 ->정산 시 지급으로 인한 관리자포인트 충전  
 * 500 : g_money 충전시 습득 ,501 : g_money 게시판 활동 습득 , 502 : item 사용, 503 : g_money 아이템 구매,504 관리자 지급, 505 관리자 회수,506 환급패치 머니 회수,507 배당업 아이팀 사용으로인한 배당증가
 * 508 : 적특 패치 사용, 509 :  배당패치 아이템 사용으로 배당업,510 : 출금취소로 인한 g_money 회수, 511 : 배팅 취소 : 아이템 반환
 * 600 : 미니게임 베팅, 601 : 카지노 베팅, 602 : 슬롯 베팅, 603 : 이스포츠 베팅, 604 : 키론 베팅, 605 : 해쉬 베팅
 * 650 : 미니게임 베팅결과처리, 621 : 카지노 베팅결과처리, 622 : 슬롯 베팅결과처리, 623 : 이스포츠 베팅결과처리, 624 : 키론 베팅결과처리, 625 : 해쉬 베팅결과처리
 */



// t_log_cash ac_code 정의값 
defined('AC_GM_ADD_POINT') || define('AC_GM_ADD_POINT', 123); //관리자 포인트 지급
defined('AC_GM_ADD_MANAUAL_POINT') || define('AC_GM_ADD_MANAUAL_POINT', 131); //관리자 수동 포인트 지급
defined('AC_CH_GM_TAKE')   || define('AC_CH_GM_TAKE', 500); //충전시 습득
defined('AC_BD_GM_TAKE')   || define('AC_BD_GM_TAKE', 501); //게시판 활동 습득

defined('AC_GM_USEITEM')   || define('AC_GM_USEITEM', 502); // 아이템 사용
defined('AC_GM_BUYITEM')   || define('AC_GM_BUYITEM', 503); // 아이템 구매



defined('AC_GM_REFUND_MONEY')   || define('AC_GM_REFUND_MONEY', 506); //아이템 환급패치 사용으로 머니 습득
defined('AC_GM_ALLOCATION_MONEY')   || define('AC_GM_ALLOCATION_MONEY', 507); //아이템 배당패치 사용으로 머니 습득
defined('AC_GM_HIT_SPECIAL_MONEY')   || define('AC_GM_HIT_SPECIAL_MONEY', 508); //아이템 적특패치 사용으로 머니 습득

defined('AC_GM_ALLOCATION_PRICE')   || define('AC_GM_ALLOCATION_PRICE', 509); //아이템 배당패치 사용으로 배당업

defined('AC_GM_ROLLBACK_RE_CALL_CHARGE')   || define('AC_GM_ROLLBACK_RE_CALL_CHARGE', 510); //출금취소로 인한 g_money 회수
defined('AC_GM_CANCEL_ITEM_USE')   || define('AC_GM_CANCEL_ITEM_USE', 511); //배팅 취소로 인한 item 회수
defined('AC_GM_ADMIN_ITEM_REWARD_PAYMENT')   || define('AC_GM_ADMIN_ITEM_REWARD_PAYMENT', 512); //Admin Item Reward Payment
// defined('AC_USER_PAYBACK')   || define('AC_USER_PAYBACK', 3001);
// defined('AC_DAY_CHRGE_EVENT_REWARD_POINT')   || define('AC_DAY_CHRGE_EVENT_REWARD_POINT', 3002);

defined('USER_PAY_BACK_REWARD_POINT') || define('USER_PAY_BACK_REWARD_POINT', 3001);
defined('DAY_CHRGE_EVENT_REWARD_POINT') || define('DAY_CHRGE_EVENT_REWARD_POINT', 3002);
defined('USER_PAY_BACK_REWARD_MYNUS_POINT') || define('USER_PAY_BACK_REWARD_MYNUS_POINT', 3003);
defined('RECOMMENDER_PAY_BACK_REWARD_POINT') || define('RECOMMENDER_PAY_BACK_REWARD_POINT', 4001);

defined('USER_BET_BACK_REWARD_POINT') || define('USER_BET_BACK_REWARD_POINT', 4002);
defined('RECOMMENDER_BET_BACK_REWARD_POINT') || define('RECOMMENDER_BET_BACK_REWARD_POINT', 4003);

defined('USER_BET_LOSE_BACK_REWARD_POINT') || define('USER_BET_LOSE_BACK_REWARD_POINT', 4004);
defined('RECOMMENDER_BET_LOSE_BACK_REWARD_POINT') || define('RECOMMENDER_BET_LOSE_BACK_REWARD_POINT', 4005);

// 아이템 타입
defined('GM_REFUND')   || define('GM_REFUND', 1); // 환급
defined('GM_ALLOCATION')   || define('GM_ALLOCATION', 2); // 배당
defined('GM_HIT_SPECIAL')   || define('GM_HIT_SPECIAL', 3); // 적특

//Definition value of business_type table
// The value that matches the u_business column of the member table
define('GENERAL', 1);
define('TOP_DISTRIBUTOR', 10);
define('DISTRIBUTOR', 11);
define('SUB_DISTRIBUTOR', 12);

// domain type
define('DOMAIN_HO', 1);
define('DOMAIN_BO', 2);
define('DOMAIN_GARO', 3);

// test id
define('TEST_ID', 'betsadmin');

// sports id
defined('SOCCER')   || define('SOCCER', 6046); // football
defined('BASKETBALL')   || define('BASKETBALL', 48242); // basketball
defined('BASEBALL')   || define('BASEBALL', 154914); // baseball
defined('VOLLEYBALL')   || define('VOLLEYBALL', 154830); // volleyball
defined('UFC')   || define('UFC', 154919); // ufc
defined('ICEHOCKEY')   || define('ICEHOCKEY', 35232); // ice hockey
defined('ESPORTS')   || define('ESPORTS', 687890); // esports
?>