<?php

/*
  |--------------------------------------------------------------------------
  | ERROR DISPLAY
  |--------------------------------------------------------------------------
  | In development, we want to show as many errors as possible to help
  | make sure they don't make it to production. And save us hours of
  | painful debugging.
 */
error_reporting(-1);
ini_set('display_errors', '1');

/*
  |--------------------------------------------------------------------------
  | DEBUG BACKTRACES
  |--------------------------------------------------------------------------
  | If true, this constant will tell the error screens to display debug
  | backtraces along with the other error information. If you would
  | prefer to not see this, set this value to false.
 */
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
  |--------------------------------------------------------------------------
  | DEBUG MODE
  |--------------------------------------------------------------------------
  | Debug mode is an experimental flag that can allow changes throughout
  | the system. It's not widely used currently, and may not survive
  | release of the framework.
 */

defined('CI_DEBUG') || define('CI_DEBUG', 1);

/*
  |--------------------------------------------------------------------------
  | DATABASE INFO
  |--------------------------------------------------------------------------
 */
// 데이타베이스 설정
define('GAME_DB_HOST', '210.175.73.245');
define('GAME_DB_ID', 'betsdev');
define('GAME_DB_PASSWORD', 'Mh9aCd5DslUWHujP');
define('GAME_DB_PORT', 3306);
define('GAME_DB_DATABASE', 'mydb_noble');

define('LOG_DB_HOST', '210.175.73.245');
define('LOG_DB_ID', 'betsdev');
define('LOG_DB_PASSWORD', 'Mh9aCd5DslUWHujP');
define('LOG_DB_PORT', 3306);
define('LOG_DB_DATABASE', 'mydb_noble');

define('MAIN_DB_HOST', '210.175.73.245');
define('MAIN_DB_ID', 'betsdev');
define('MAIN_DB_PASSWORD', 'Mh9aCd5DslUWHujP');
define('MAIN_DB_PORT', 3306);
define('MAIN_DB_DATABASE', 'main_bets_db');
