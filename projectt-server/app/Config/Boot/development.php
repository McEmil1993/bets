<?php
/*
  |--------------------------------------------------------------------------
  | ERROR DISPLAY
  |--------------------------------------------------------------------------
  | Don't show ANY in production environments. Instead, let the system catch
  | it and display a generic error message.
 */
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

/*
  |--------------------------------------------------------------------------
  | DEBUG MODE
  |--------------------------------------------------------------------------
  | Debug mode is an experimental flag that can allow changes throughout
  | the system. It's not widely used currently, and may not survive
  | release of the framework.
 */

defined('CI_DEBUG') || define('CI_DEBUG', 0);

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
define('GAME_DB_DATABASE', 'mydb_bets');

define('LOG_DB_HOST', '210.175.73.245');
define('LOG_DB_ID', 'betsdev');
define('LOG_DB_PASSWORD', 'Mh9aCd5DslUWHujP');
define('LOG_DB_PORT', 3306);
define('LOG_DB_DATABASE', 'mydb_bets');

define('MAIN_DB_HOST', '210.175.73.245');
define('MAIN_DB_ID', 'betsdev');
define('MAIN_DB_PASSWORD', 'Mh9aCd5DslUWHujP');
define('MAIN_DB_PORT', 3306);
define('MAIN_DB_DATABASE', 'main_bets_db');
