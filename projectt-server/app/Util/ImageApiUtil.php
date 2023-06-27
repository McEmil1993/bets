<?php
namespace App\Util;
/**
* 이미지 전송 및 삭제
*/
class ImageApiUtil {

  var $icode_key = "21a5b924d78463aeaeb7c187d2fec8bf";  // 프론트서버
  var $socket_host = "211.172.232.124";
  var $socket_port = 9201;
  var $Data = array();
  var $Result = array();

  // 이미지 전송
  function imageApiSend($file_name, $uploadfile, $api_file_path) {
  	
  	$POST_DATA = array(
  			'fileName' => $file_name,
  			'fileData' => base64_encode(file_get_contents($uploadfile)),
  			'savePath' => "/".config(App::class)->imagePath.$api_file_path
  	);
  	
  	$curl = curl_init();
  	curl_setopt($curl, CURLOPT_URL, config(App::class)->IMAGE_SERVER_UPLOAD_URL);
  	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
  	curl_setopt($curl, CURLOPT_POST, 1);
  	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
  	$response = curl_exec($curl);
  	curl_close ($curl);
  	
  	// Now delete local temp file
  	unlink($uploadfile);
  }
  
  // 이미지 삭제
  function imageApiDelete($file_name, $api_file_path) {
  	
  	$POST_DATA = array(
  			'deletePath' => config(App::class)->imagePath.$api_file_path.$file_name
  	);
  	$curl = curl_init();
  	curl_setopt($curl, CURLOPT_URL,  config(App::class)->IMAGE_SERVER_DELETE_URL);
  	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
  	curl_setopt($curl, CURLOPT_POST, 1);
  	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
  	$response = curl_exec($curl);
  	curl_close ($curl);
  	
  }
}
?>