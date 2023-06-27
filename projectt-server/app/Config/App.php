<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{

	/*
	|--------------------------------------------------------------------------
	| Base Site URL
	|--------------------------------------------------------------------------
	|
	| URL to your CodeIgniter root. Typically this will be your base URL,
	| WITH a trailing slash:
	|
	|	http://example.com/
	|
	| If this is not set then CodeIgniter will try guess the protocol, domain
	| and path to your installation. However, you should always configure this
	| explicitly and never rely on auto-guessing, especially in production
	| environments.
	|
	*/
	//public $baseURL = 'http://127.0.0.1/';
        public $baseURL = '';
        public function __construct() {
            $this->baseURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
            $this->baseURL .= "://" . $_SERVER['HTTP_HOST'];
            $this->baseURL .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
        }

	/*
	|--------------------------------------------------------------------------
	| Index File
	|--------------------------------------------------------------------------
	|
	| Typically this will be your index.php file, unless you've renamed it to
	| something else. If you are using mod_rewrite to remove the page set this
	| variable so that it is blank.
	|
	*/
	public $indexPage = '';

	/*
	|--------------------------------------------------------------------------
	| URI PROTOCOL
	|--------------------------------------------------------------------------
	|
	| This item determines which getServer global should be used to retrieve the
	| URI string.  The default setting of 'REQUEST_URI' works for most servers.
	| If your links do not seem to work, try one of the other delicious flavors:
	|
	| 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
	| 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
	| 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
	|
	| WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
	*/
	public $uriProtocol = 'REQUEST_URI';

	/*
	|--------------------------------------------------------------------------
	| Default Locale
	|--------------------------------------------------------------------------
	|
	| The Locale roughly represents the language and location that your visitor
	| is viewing the site from. It affects the language strings and other
	| strings (like currency markers, numbers, etc), that your program
	| should run under for this request.
	|
	*/
	public $defaultLocale = 'en';

	/*
	|--------------------------------------------------------------------------
	| Negotiate Locale
	|--------------------------------------------------------------------------
	|
	| If true, the current Request object will automatically determine the
	| language to use based on the value of the Accept-Language header.
	|
	| If false, no automatic detection will be performed.
	|
	*/
	public $negotiateLocale = false;

	/*
	|--------------------------------------------------------------------------
	| Supported Locales
	|--------------------------------------------------------------------------
	|
	| If $negotiateLocale is true, this array lists the locales supported
	| by the application in descending order of priority. If no match is
	| found, the first locale will be used.
	|
	*/
	public $supportedLocales = ['en'];

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| The default timezone that will be used in your application to display
	| dates with the date helper, and can be retrieved through app_timezone()
	|
	*/
	public $appTimezone = 'Asia/Seoul';

	/*
	|--------------------------------------------------------------------------
	| Default Character Set
	|--------------------------------------------------------------------------
	|
	| This determines which character set is used by default in various methods
	| that require a character set to be provided.
	|
	| See http://php.net/htmlspecialchars for a list of supported charsets.
	|
	*/
	public $charset = 'UTF-8';

	/*
	|--------------------------------------------------------------------------
	| URI PROTOCOL
	|--------------------------------------------------------------------------
	|
	| If true, this will force every request made to this application to be
	| made via a secure connection (HTTPS). If the incoming request is not
	| secure, the user will be redirected to a secure version of the page
	| and the HTTP Strict Transport Security header will be set.
	*/
	public $forceGlobalSecureRequests = false;

	/*
	|--------------------------------------------------------------------------
	| Session Variables
	|--------------------------------------------------------------------------
	|
	| 'sessionDriver'
	|
	|	The storage driver to use: files, database, redis, memcached
	|       - CodeIgniter\Session\Handlers\FileHandler
	|       - CodeIgniter\Session\Handlers\DatabaseHandler
	|       - CodeIgniter\Session\Handlers\MemcachedHandler
	|       - CodeIgniter\Session\Handlers\RedisHandler
	|
	| 'sessionCookieName'
	|
	|	The session cookie name, must contain only [0-9a-z_-] characters
	|
	| 'sessionExpiration'
	|
	|	The number of SECONDS you want the session to last.
	|	Setting to 0 (zero) means expire when the browser is closed.
	|
	| 'sessionSavePath'
	|
	|	The location to save sessions to, driver dependent.
	|
	|	For the 'files' driver, it's a path to a writable directory.
	|	WARNING: Only absolute paths are supported!
	|
	|	For the 'database' driver, it's a table name.
	|	Please read up the manual for the format with other session drivers.
	|
	|	IMPORTANT: You are REQUIRED to set a valid save path!
	|
	| 'sessionMatchIP'
	|
	|	Whether to match the user's IP address when reading the session data.
	|
	|	WARNING: If you're using the database driver, don't forget to update
	|	         your session table's PRIMARY KEY when changing this setting.
	|
	| 'sessionTimeToUpdate'
	|
	|	How many seconds between CI regenerating the session ID.
	|
	| 'sessionRegenerateDestroy'
	|
	|	Whether to destroy session data associated with the old session ID
	|	when auto-regenerating the session ID. When set to FALSE, the data
	|	will be later deleted by the garbage collector.
	|
	| Other session cookie settings are shared with the rest of the application,
	| except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
	|
	*/
	public $sessionDriver            = 'CodeIgniter\Session\Handlers\FileHandler';
	public $sessionCookieName        = 'ci_session';
	public $sessionExpiration        = 7200;
	public $sessionSavePath          = WRITEPATH . 'session';
	public $sessionMatchIP           = false;
	public $sessionTimeToUpdate      = 300;
	public $sessionRegenerateDestroy = false;

	/*
	|--------------------------------------------------------------------------
	| Cookie Related Variables
	|--------------------------------------------------------------------------
	|
	| 'cookiePrefix'   = Set a cookie name prefix if you need to avoid collisions
	| 'cookieDomain'   = Set to .your-domain.com for site-wide cookies
	| 'cookiePath'     = Typically will be a forward slash
	| 'cookieSecure'   = Cookie will only be set if a secure HTTPS connection exists.
	| 'cookieHTTPOnly' = Cookie will only be accessible via HTTP(S) (no javascript)
	|
	| Note: These settings (with the exception of 'cookie_prefix' and
	|       'cookie_httponly') will also affect sessions.
	|
	*/
	public $cookiePrefix   = '';
	public $cookieDomain   = '';
	public $cookiePath     = '/';
	public $cookieSecure   = false;
	public $cookieHTTPOnly = false;

	/*
	|--------------------------------------------------------------------------
	| Reverse Proxy IPs
	|--------------------------------------------------------------------------
	|
	| If your server is behind a reverse proxy, you must whitelist the proxy
	| IP addresses from which CodeIgniter should trust headers such as
	| HTTP_X_FORWARDED_FOR and HTTP_CLIENT_IP in order to properly identify
	| the visitor's IP address.
	|
	| You can use both an array or a comma-separated list of proxy addresses,
	| as well as specifying whole subnets. Here are a few examples:
	|
	| Comma-separated:	'10.0.1.200,192.168.5.0/24'
	| Array:		array('10.0.1.200', '192.168.5.0/24')
	*/
	public $proxyIPs = '';

	/*
	|--------------------------------------------------------------------------
	| Cross Site Request Forgery
	|--------------------------------------------------------------------------
	| Enables a CSRF cookie token to be set. When set to TRUE, token will be
	| checked on a submitted form. If you are accepting user data, it is strongly
	| recommended CSRF protection be enabled.
	|
	| CSRFTokenName   = The token name
	| CSRFHeaderName  = The header name
	| CSRFCookieName  = The cookie name
	| CSRFExpire      = The number in seconds the token should expire.
	| CSRFRegenerate  = Regenerate token on every submission
	| CSRFRedirect    = Redirect to previous page with error on failure
	*/
	public $CSRFTokenName  = 'csrf_test_name';
	public $CSRFHeaderName = 'X-CSRF-TOKEN';
	public $CSRFCookieName = 'csrf_cookie_name';
	public $CSRFExpire     = 7200;
	public $CSRFRegenerate = true;
	public $CSRFRedirect   = true;

	/*
	|--------------------------------------------------------------------------
	| Content Security Policy
	|--------------------------------------------------------------------------
	| Enables the Response's Content Secure Policy to restrict the sources that
	| can be used for images, scripts, CSS files, audio, video, etc. If enabled,
	| the Response object will populate default values for the policy from the
	| ContentSecurityPolicy.php file. Controllers can always add to those
	| restrictions at run time.
	|
	| For a better understanding of CSP, see these documents:
	|   - http://www.html5rocks.com/en/tutorials/security/content-security-policy/
	|   - http://www.w3.org/TR/CSP/
	*/
	public $CSPEnabled = false;
                
        /*
        |--------------------------------------------------------------------------
        | SMS
        |--------------------------------------------------------------------------
         */
        public $SMSTokenKey = '67124cc560fdd8178d76eeb866f02352';

        /*
        |--------------------------------------------------------------------------
        | 개발서버인지 라이브인지 판단용도
        |--------------------------------------------------------------------------
         */
        public $IsDevServer = true;

        /*
        |--------------------------------------------------------------------------
        | 이미지서버 주소
        |--------------------------------------------------------------------------
         */
        public $imageUrl = 'http://210.175.73.221';
        public $imagePath = 'dev_bets';
        public $imagePathBoard = 'board';
        public $IMAGE_SERVER = 'https://imghubserver.com';
        public $IMAGE_SERVER_UPLOAD_URL= 'https://imghubserver.com/receiver.php';
        public $IMAGE_SERVER_DELETE_URL= 'https://imghubserver.com/delete.php';
        
        /*
        |--------------------------------------------------------------------------
        | 가상계좌 MID
        |--------------------------------------------------------------------------
         */
        public $mid = 'paywinkw0m';
        
        // 추가값
        public $ServerName = 'BETS'; // AS
        public $AuthCode = '123456';  // 1530
        public $CheckMessage = '점검중입니다. 현재 텔레그램문의만가능합니다. @kwin2021'; // 현재 점검중에 있습니다.
                                                                                 //점검 시간 이후 접속이 안되시면
                                                                                 //쿠키 삭제 이후 재접속 부탁드립니다.
                                                                                 //텔레그램 @asbet7.
        public $ExcludedRecommandCode = ''; // as->''

        public $CheckTimeMessage = '[점검시간] 2022.02.17 14:00~16:00'; // [점검시간] 2021.12.16 16:00~16:15

        public $BorderCount = 0; // 0일때 제한 없음
        
        /*
        |--------------------------------------------------------------------------
        | 실시간 종목별 베팅지연 시간(밀리초)
        |--------------------------------------------------------------------------
         */
        public $betDelayTime = array('6046'=>7000, '35232'=>5000, '48242'=>5000, '131506'=>5000, '687890'=>0, '154830'=>5000, '154914'=>5000);
        
        /*
        |--------------------------------------------------------------------------
        | 카지노 키정보
        |--------------------------------------------------------------------------
         */
        public $ApiUrl = 'http://kplayone.com';
        public $AgToken = 'Gqr6fbXvVjfhZx5tSADrKElq1tATliec';
        public $AgCode = 'BON2506';
        public $SercetKey = 'v2S4uMxJuUIitAdzTb0smTwxhVsNYrHh';
        
        //public $IS_INCLUDING_DISTRIBUTOR = 'OFF'; // 보유포인트 계산시 총판도 포함하는지 여부 asbet 은 포함 kwin은 미포함
        public $IS_MULTI_LOSS_ROLLING = 'ON'; // 다폴더 낙첨 롤링 지급 유무 kwin은 포함 asbet은 미포함

        public $profile = 'dev'; //
        
        /*
        |--------------------------------------------------------------------------
        | 미니게임 항목표기
        |--------------------------------------------------------------------------
         */
        public $IS_EOS_POWERBALL = 'ON';
        public $IS_POWERBALL = 'ON';


	public $IS_HOLDEM = 'ON';
        
        /*
        |--------------------------------------------------------------------------
        | 레디스 정보
        |--------------------------------------------------------------------------
         */
        public $redis_ip = '124.33.132.21';
        public $redis_port = 16389;
        public $redis_password = '';
        public $redis_database = 1;
        public $redis_expire = 0;

}
