<?php

/*
  |--------------------------------------------------------------------------
  | ERROR DISPLAY
  |--------------------------------------------------------------------------
  | Don't show ANY in production environments. Instead, let the system catch
  | it and display a generic error message.
 */
ini_set('display_errors', '1');
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
define('GAME_DB_HOST', '54.250.249.165');
define('GAME_DB_ID', 'nova_dev');
define('GAME_DB_PASSWORD', 'ZwgUG8f9XTwvXM');
define('GAME_DB_PORT', 2450);
define('GAME_DB_DATABASE', 'mydb_bulls');

define('LOG_DB_HOST', '54.250.249.165');
define('LOG_DB_ID', 'nova_dev');
define('LOG_DB_PASSWORD', 'ZwgUG8f9XTwvXM');
define('LOG_DB_PORT', 2450);
define('LOG_DB_DATABASE', 'mydb_bulls');

define('MAIN_DB_HOST', '54.250.249.165');
define('MAIN_DB_ID', 'nova_dev');
define('MAIN_DB_PASSWORD', 'ZwgUG8f9XTwvXM');
define('MAIN_DB_PORT', 2450);
define('MAIN_DB_DATABASE', 'main_bulls_db');

