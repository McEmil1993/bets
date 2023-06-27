<?php
        define('_DB_IP', '54.250.249.165');
        define('_DB_PORT', '2380');
	//define('_DB_PORT', '3306');

	define('_DB_USER_ADMIN', 'nobledev');
	define('_DB_PASS_ADMIN', 'wXaQANRTJg1hE640');
	define('_DB_NAME_ADMIN', 'BULLS_ADMIN');
	define('_DB_NAME_WEB', 'mydb_bulls');
	define('_DB_USER_WEB', 'nobledev');
	define('_DB_PASS_WEB', 'wXaQANRTJg1hE640');
        
        
        
	//define('_DB_LOG_IP', '54.250.249.165');
        define('_DB_LOG_IP', '54.250.249.165');
        define('_DB_LOG_PORT', '2380');
	//define('_DB_LOG_PORT', '3306');

	define('_DB_USER_LOG', 'nobledev');
	define('_DB_PASS_LOG', 'wXaQANRTJg1hE640');
	define('_DB_NAME_LOG_DB', 'log_db');
        
        // image server
        define('IMAGE_TEMP_PATH', '/var/www/bulls/admin/log');
        define('IMAGE_PATH', 'dev_bulls');
	define('IMAGE_SERVER_URL', 'https://imghubserver.com');
        define('IMAGE_SERVER_UPLOAD_URL', 'https://imghubserver.com/receiver.php');
        define('IMAGE_SERVER_DELETE_URL', 'https://imghubserver.com/delete.php');
        
        // initData port
        define('INITDATA_PRE_URL', 'http://210.175.73.161:20003/admin/change_provider_refundRate');
        define('INITDATA_REAL_URL', 'http://210.175.73.161:20002/admin/change_provider_refundRate');
        
        // define('SERVER', 'BETS');
        define('SERVER', 'BULLS');
        
        // title
        define('TITLE', 'BULLS-ADMIN(DEV)');
        define ('IS_HASH','ON');
        define ('IS_ESPORTS_KEYRON','OFF');
        
        // 마우스 우측, 개발자모드 막기
        define ('IS_XSS_MODE','OFF');

        define ('IS_INCLUDING_DISTRIBUTOR','OFF'); // 보유포인트 계산시 총판도 포함하는지 여부 asbet 은 포함 kwin은 미포함

        define ('IS_MULTI_LOSS_ROLLING','ON'); // 다폴더 낙첨 롤링 지급 유무 kwin은 포함 asbet은 미포함

        define ('LOGIN_BG','login_bi.png'); // login_bi_asbet.png
        define ('FAVICON_IMG','/favicon.ico'); // /favicon_asbet.ico
        
        define ('IS_EOS_POWERBALL','ON');
        define ('IS_POWERBALL','ON');

        define ('REDIS_IP','54.250.249.165');
        define ('REDIS_PORT',6379);
        define ('REDIS_PASSWORD','');
        define ('REDIS_DATABASE',1);
        define ('REDIS_EXPIRE',0);

        define ('IS_HOLDEM','ON');
?>
