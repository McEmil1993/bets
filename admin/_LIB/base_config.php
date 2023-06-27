<?php

header("Content-Type: text/html; charset=utf-8");

define('_BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('_LIBPATH', _BASEPATH.'/_LIB');
define('_DAOPATH', _BASEPATH.'/_DAO');

if (defined("_MODE")=='') {
	define('_MODE','SERVICE');
}

if (_MODE == 'SERVICE') {
	include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_define.php');
}
else {
	include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_define.php');
}
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_constants.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/class_Code.php');

define('_B_BLOCK_COUNT', '10');
define('_B_PAGING_COUNT', '10');
define('_NUM_PER_PAGE', '50');      // 한 페이지에 보여줄 게시글 카운트

define('_STATIC_COMMON_PATH','/static_common');

global $injectionCheck;

if($injectionCheck=='') {
	$injectionCheck=0;
}

if(!$injectionCheck){
	unset($arr_request);
	unset($arr_post);
	unset($arr_get);
	unset($arr_illegal);
	unset($arr_replaced);

	$arr_request = $_REQUEST;
	$arr_post = $_POST;
	$arr_get = $_GET;

	
	reset($arr_request);
	reset($arr_post);
	reset($arr_get);

	/** 한글 표현 때문에 ; 를 무조건 제거: semi-colon 사용하기 위해서 |mMm|59 라고 치환하시면 됩니다. **/
	$arr_illegal = array('&',';','&#','--','/*','*/','iframe','script','embed','cookie','drop','truncate','select','fopen','fsockopen','file_get_contents','readfile','unlink','object','phpinfo','1=1','union','mydb_bets.member','mydb');
	$arr_replaced = array('','','','','','','if_rame','scr_ipt','emb_ed','coo_kie','dr_op','trun_cate_','sel_ect','fo_pen','fsoc_kopen_','_file_get_cont_ents_','read_file','un_link','obj_ect','php_info','1_=_1','u_ni_on','','');

	$arr_request = str_ireplace($arr_illegal,$arr_replaced,$arr_request);
	$arr_post = str_ireplace($arr_illegal,$arr_replaced,$arr_post);
	$arr_get = str_ireplace($arr_illegal,$arr_replaced,$arr_get);

	$_REQUEST = $arr_request;
	$_POST = $arr_post;
	$_GET = $arr_get;
	
	unset($arr_request);
	unset($arr_post);
	unset($arr_get);
	unset($arr_illegal);
	unset($arr_replaced);
}

if($injectionCheck=='2'){
	unset($arr_request);
	unset($arr_post);
	unset($arr_get);
	unset($arr_illegal);
	unset($arr_replaced);

	$arr_request = $_REQUEST;
	$arr_post = $_POST;
	$arr_get = $_GET;


	reset($arr_request);
	reset($arr_post);
	reset($arr_get);

	/** 한글 표현 때문에 ; 를 무조건 제거: semi-colon 사용하기 위해서 |mMm|59 라고 치환하시면 됩니다. **/
	$arr_illegal = array('&',';','&#','--','/*','*/','iframe','script','embed','cookie','drop','truncate','select','fopen','fsockopen','file_get_contents','readfile','unlink','object','phpinfo','1=1','union','mydb_bets.member','mydb');
	$arr_replaced = array('','','','','','','','','','','','','','','','','','','','','','','','');

	$arr_request = str_ireplace($arr_illegal,$arr_replaced,$arr_request);
	$arr_post = str_ireplace($arr_illegal,$arr_replaced,$arr_post);
	$arr_get = str_ireplace($arr_illegal,$arr_replaced,$arr_get);

	$_REQUEST = $arr_request;
	$_POST = $arr_post;
	$_GET = $arr_get;

	unset($arr_request);
	unset($arr_post);
	unset($arr_get);
	unset($arr_illegal);
	unset($arr_replaced);
}

function htmlspecialchars2($s) {
	return(strtr($s, array('&#'=>'&#', '&'=>'&amp;', '"'=>'&quot;', '<'=>'&lt;', '>'=>'&gt;')));
}

?>
