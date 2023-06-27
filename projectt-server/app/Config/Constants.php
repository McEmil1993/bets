<?php

//--------------------------------------------------------------------
// App Namespace
//--------------------------------------------------------------------
// This defines the default Namespace that is used throughout
// CodeIgniter to refer to the Application directory. Change
// this constant to change the namespace that all application
// classes should use.
//
// NOTE: changing this will require manually modifying the
// existing namespaces of App\* namespaced-classes.
//
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
|--------------------------------------------------------------------------
| Composer Path
|--------------------------------------------------------------------------
|
| The path that Composer's autoload file is expected to live. By default,
| the vendor folder is in the Root directory, but you can customize that here.
*/
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
|--------------------------------------------------------------------------
| Timing Constants
|--------------------------------------------------------------------------
|
| Provide simple ways to work with the myriad of PHP functions that
| require information to be in seconds.
*/
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2592000);
defined('YEAR')   || define('YEAR', 31536000);
defined('DECADE') || define('DECADE', 315360000);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


// Period_id
defined('FIRST_HALF')      || define('FIRST_HALF', 10); // 전반전
defined('TWOND_HALF')      || define('TWOND_HALF', 20); // 후반전


//market_id 

defined('WDL')      || define('WDL', 1); // 승무패
defined('HANDICAP')      || define('HANDICAP', 3); // 승무패

defined('WL')      || define('WL', 52); // 승패
defined('DOUBLE_CHANCE')      || define('DOUBLE_CHANCE', 7); // 더블찬스
defined('BOTH_TEAMS_SCORED_SUCCESS')      || define('BOTH_TEAMS_SCORED_SUCCESS', 17); // 양팀득점성공
defined('OVER_UNDER')      || define('OVER_UNDER', 2); // 오버언더
defined('AWAY_TEAM_SCORE')      || define('AWAY_TEAM_SCORE', 35); // 원정팀 스코어



defined('Q1_HANDICAP')      || define('Q1_HANDICAP', 64);       // 1Q Handicap
defined('Q2_HANDICAP')      || define('Q2_HANDICAP', 65);       // 2Q Handicap
defined('Q3_HANDICAP')      || define('Q3_HANDICAP', 66);       // 3Q Handicap
defined('Q4_HANDICAP')      || define('Q4_HANDICAP', 67);       // 4Q Handicap


defined('Q1_1X2')      || define('Q1_1X2', 41);   // 1Q 1X2
defined('Q2_1X2')      || define('Q2_1X2', 42);   // 2Q 1X2
defined('Q3_1X2')      || define('Q3_1X2', 43);   // 3Q 1X2
defined('Q4_1X2')      || define('Q4_1X2', 44);   // 4Q 1X2
defined('Q5_1X2')      || define('Q5_1X2', 49);   // 5Q 1X2
defined('Q6_1X2')      || define('Q6_1X2', 348);   // 6Q 1X2
defined('Q7_1X2')      || define('Q7_1X2', 349);   // 7Q 1X2


defined('Q1_12')      || define('Q1_12', 202);  // 1Q 12
defined('Q2_12')      || define('Q2_12', 203);   // 2Q 12
defined('Q3_12')      || define('Q3_12', 204);   // 3Q 12
defined('Q4_12')      || define('Q4_12', 205);   // 4Q 12
defined('Q5_12')      || define('Q5_12', 206);   // 5Q 12

defined('Q1_F_KILL')      || define('Q1_F_KILL', 669);   // 1Q first kill
defined('Q1_F_DG')      || define('Q1_F_DG', 1170);   // 1Q first dragon


defined('Q1_OVER_UNDER')      || define('Q1_OVER_UNDER', 21);   // 1Q OverUnder
defined('Q2_OVER_UNDER')      || define('Q2_OVER_UNDER', 45);   // 2Q OverUnder
defined('Q3_OVER_UNDER')      || define('Q3_OVER_UNDER', 46);   // 3Q OverUnder
defined('Q4_OVER_UNDER')      || define('Q4_OVER_UNDER', 47);   // 4Q OverUnder
defined('Q5_OVER_UNDER')      || define('Q5_OVER_UNDER', 48);   // 5Q OverUnder
defined('Q6_OVER_UNDER')      || define('Q6_OVER_UNDER', 352);   // 6Q OverUnder
defined('Q7_OVER_UNDER')      || define('Q7_OVER_UNDER', 353);   // 7Q OverUnder

defined('BREAK_TIME')      || define('BREAK_TIME', 80);       // break time


defined('OVER_UNDER_OVERTIME')      || define('OVER_UNDER_OVERTIME', 28);    // OverUnder OverTime


defined('AWAY_OVER_UNDER_OVERTIME')      || define('AWAY_OVER_UNDER_OVERTIME', 220);    // Away OverUnder OverTime
defined('HOME_OVER_UNDER_OVERTIME')      || define('HOME_OVER_UNDER_OVERTIME', 221);    // Home OverUnder OverTime


defined('M_12_OVERTIME')      || define('M_12_OVERTIME', 226);    // 12 OverTime

defined('HANDICAP_OVERTIME')      || define('HANDICAP_OVERTIME', 342);       // Handicap OverTime

//defined('SPORRS_BLOCK_COUNT')   || define('SPORRS_BLOCK_COUNT', 50); // 스포츠 페이지 한화면에 보여주는 수
defined('SPORRS_BLOCK_COUNT')   || define('SPORRS_BLOCK_COUNT', 50); // 스포츠 페이지 한화면에 보여주는 수
defined('CAL_BLOCK_COUNT')   || define('CAL_BLOCK_COUNT', 50); // 배팅내역 페이지 한화면에 보여주는 수

defined('COIN_SITE_URL')   || define('COIN_SITE_URL', 'https://te.neo-dex.com'); // 코인 사이트

// 종목 선언
defined('SOCCER')   || define('SOCCER', 6046); // 축구
defined('BASKETBALL')   || define('BASKETBALL', 48242); // 농구
defined('BASEBALL')   || define('BASEBALL', 154914); // 야구
defined('VOLLEYBALL')   || define('VOLLEYBALL', 154830); // 배구
defined('UFC')   || define('UFC', 154919); // ufc
defined('ICEHOCKEY')   || define('ICEHOCKEY', 35232); // 아이스하키
defined('ESPORTS')   || define('ESPORTS', 687890); // 이스포츠
defined('TENNIS')   || define('TENNIS', 54094); // 테니스
//
// 마켓
defined('1X2')   || define('1X2', 1); // 축구


// t_log_cash ac_code 정의값 
defined('AC_CH_GM_TAKE')   || define('AC_CH_GM_TAKE', 500); //충전시 습득
defined('AC_BD_GM_TAKE')   || define('AC_BD_GM_TAKE', 501); //게시판 활동 습득

defined('AC_GM_USEITEM')   || define('AC_GM_USEITEM', 502); // 아이템 사용
defined('AC_GM_BUYITEM')   || define('AC_GM_BUYITEM', 503); // 아이템 구매



defined('AC_GM_REFUND_MONEY')   || define('AC_GM_REFUND_MONEY', 506); //아이템 환급패치 사용으로 머니 습득
defined('AC_GM_ALLOCATION_MONEY')   || define('AC_GM_ALLOCATION_MONEY', 507); //아이템 배당패치 사용으로 머니 습득
defined('AC_GM_HIT_SPECIAL_MONEY')   || define('AC_GM_HIT_SPECIAL_MONEY', 508); //아이템 적특패치 사용으로 머니 습득

defined('AC_GM_ALLOCATION_PRICE')   || define('AC_GM_ALLOCATION_PRICE', 509); //아이템 배당패치 사용으로 배당업
defined('AC_GM_CANCEL_ITEM_USE')   || define('AC_GM_CANCEL_ITEM_USE', 511); //배팅 취소로 인한 item 회수



// 아이템 타입
defined('GM_REFUND')   || define('GM_REFUND', 1); // 환급
defined('GM_ALLOCATION')   || define('GM_ALLOCATION', 2); // 배당
defined('GM_HIT_SPECIAL')   || define('GM_HIT_SPECIAL', 3); // 적특

// 베팅상태
defined('BET_OPEN')   || define('BET_OPEN', 1); // 배팅 오픈
defined('BET_CLOSE')   || define('BET_CLOSE', 2); // 배팅 닫힘
defined('BET_END')   || define('BET_END', 3); // 배팅 종료


//

defined('INPLAY_DELAY_ALLOWED_TIME')   || define('INPLAY_DELAY_ALLOWED_TIME', 300); // 인플레이 지연 허용 시간 

// display exchange list count
defined('VIRTUAL_EXCHANGE_LIST_COUNT') || define('VIRTUAL_EXCHANGE_LIST_COUNT', 6);
defined('VIRTUAL_EXCHANGE_LIST_MONEY') || define('VIRTUAL_EXCHANGE_LIST_MONEY', 500);
defined('EXCHANGE_LIST_COUNT') || define('EXCHANGE_LIST_COUNT', 8);

// Game Type Definition Value => Associated with the lsports_markets table
defined('GT_SPORTS')   || define('GT_SPORTS', 1); 
defined('GT_REALTIME')   || define('GT_REALTIME', 2); 
defined('GT_EOS_POWERBALL')   || define('GT_EOS_POWERBALL', 3); 
defined('GT_POWER_LADDER')   || define('GT_POWER_LADDER', 4); 
defined('GT_KINO_LADDER')   || define('GT_KINO_LADDER', 5); 
defined('GT_VIRSUAL_SOCCER')   || define('GT_VIRSUAL_SOCCER', 6); 
defined('GT_CASINO')   || define('GT_CASINO', 7); 
defined('GT_SLOT')   || define('GT_SLOT', 8); 
defined('GT_ESPORTS_KIRON')   || define('GT_ESPORTS_KIRON', 9); 
defined('GT_HASH')   || define('GT_HASH', 10); 
defined('GT_ENTRY_POWERBALL')   || define('GT_ENTRY_POWERBALL', 15); 
defined('GT_CLASSIC')   || define('GT_CLASSIC', 20); 
defined('GT_HOLDEM') || define('GT_HOLDEM', 21); 

// Game batting type => Associated with the lsports_game_type,member_game_type table
defined('LGTB_REALTIME')   || define('LGTB_REALTIME', 1); 
defined('LGTB_REALTIME_SINGLE_FOLDER')   || define('LGTB_REALTIME_SINGLE_FOLDER', 2); 
defined('LGTB_SPORTS')   || define('LGTB_SPORTS', 3); 
defined('LGTB_SPORTS_SINGLE_FOLDER')   || define('LGTB_SPORTS_SINGLE_FOLDER', 4); 
defined('LGTB_EOS_POWERBALL')   || define('LGTB_EOS_POWERBALL', 5); 
defined('LGTB_POWER_LADDER')   || define('LGTB_POWER_LADDER', 6); 
defined('LGTB_KINO_LADDER')   || define('LGTB_KINO_LADDER', 7); 
defined('LGTB_VIRSUAL_SOCCER')   || define('LGTB_VIRSUAL_SOCCER', 8); 
defined('LGTB_CASINO')   || define('LGTB_CASINO', 9); 
defined('LGTB_SLOT')   || define('LGTB_SLOT', 10); 
defined('LGTB_ESPORTS')   || define('LGTB_ESPORTS', 11); 
defined('LGTB_KIRON')   || define('LGTB_KIRON', 12); 
defined('LGTB_HASH')   || define('LGTB_HASH', 13); 
defined('LGTB_SPORTS_TWO_FOLDER')   || define('LGTB_SPORTS_TWO_FOLDER', 14); 
defined('LGTB_ENTRY_POWERBALL')   || define('LGTB_ENTRY_POWERBALL', 15); 
defined('LGTB_CLASSIC')   || define('LGTB_CLASSIC', 16); 
defined('LGTB_HOLDEM') || define('LGTB_HOLDEM', 21); 

// ac_code
defined('ADD_BET') || define('ADD_BET', 3);
defined('BET_RESULT') || define('BET_RESULT', 7);
defined('HOLDEM_START') || define('HOLDEM_START', 1001);
defined('HOLDEM_END') || define('HOLDEM_END', 1002);
defined('HOLDEM_BET_LIST') || define('HOLDEM_BET_LIST', 1003);
defined('HOLDEM_ROUND_END') || define('HOLDEM_ROUND_END', 1004);

defined('HOLDEM_BUY_IN') || define('HOLDEM_BUY_IN', 2000);
defined('HOLDEM_BUY_OUT') || define('HOLDEM_BUY_OUT', 2001);
defined('HOLDEM_QUERY_CREDIT') || define('HOLDEM_QUERY_CREDIT', 2002);



defined('USER_PAY_BACK_REWARD_POINT') || define('USER_PAY_BACK_REWARD_POINT', 3001);



//Definition value of business_type table
// The value that matches the u_business column of the member table
define('GENERAL', 1);
define('TOP_DISTRIBUTOR', 10);
define('DISTRIBUTOR', 11);
define('SUB_DISTRIBUTOR', 12);

define('CONSTANTS_TYPE_CASINO', 'C');
define('CONSTANTS_TYPE_SLOT', 'S');
define('CONSTANTS_TYPE_ESPORTS', 'E');
define('DELAY_BETTING_LIMIT', 30);

define('MAX_PAY_BACK_POINT', 1000000);
