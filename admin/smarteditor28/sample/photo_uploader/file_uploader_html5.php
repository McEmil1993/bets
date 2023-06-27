<?php

include_once('../../../_LIB/class_CommonUtil.php');
include_once('../../../_LIB/base_define.php');
$UTIL = new CommonUtil();

$sFileInfo = '';
$headers = array();

foreach ($_SERVER as $k => $v) {
    if (substr($k, 0, 9) == "HTTP_FILE") {
        $k = substr(strtolower($k), 5);
        $headers[$k] = $v;
    }
}

$filename = rawurldecode($headers['file_name']);

$UTIL->logWrite('header' . json_encode($headers), "error");

$filename_ext = strtolower(array_pop(explode('.', $filename)));
$allow_file = array("jpg", "png", "bmp", "gif");

if (!in_array($filename_ext, $allow_file)) {
    echo "NOTALLOW_" . $filename;
} else {
    $file = new stdClass;
    $file->name = date("YmdHis") . mt_rand() . "." . $filename_ext;
    $file->content = file_get_contents("php://input");

    //$uploadDir = '../../upload/';
    //$uploadDir = '../../upload_html/';
    //if (!is_dir($uploadDir)) {
    //    mkdir($uploadDir, 0777);
    //}
    // Move uploaded file to a temp location
    //$uploadFile = $uploadDir . $file->name;
  
    $UTIL->logWrite($file->name, "error");
    $UTIL->logWrite($uploadFile, "error");

    $UTIL->logWrite('2', "error");
    // Prepare remote upload data
    $uploadRequest = array(
        //'fileName' => basename($uploadFile),
        'fileName' => $file->name,
        'fileData' => base64_encode($file->content),
        'savePath' => "/".IMAGE_PATH."/smarteditor"
    );

    // Execute remote upload
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, IMAGE_SERVER_UPLOAD_URL);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $uploadRequest);
    $response = curl_exec($curl);
    curl_close($curl);

    $UTIL->logWrite('success send image file', "error");

    //$newPath = $uploadDir . $file->name;
    //$host_ip = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://").$_SERVER['HTTP_HOST'];             
    $host_ip = IMAGE_SERVER_URL;
    $sFileInfo .= "&bNewLine=true";
    $sFileInfo .= "&sFileName=" . $filename;
    //$sFileInfo .= "&sFileURL=".$host_ip."/smarteditor28/upload_html/".$file->name;
    $sFileInfo .= "&sFileURL=" . $host_ip . "/".IMAGE_PATH."/smarteditor/" . $file->name;
    echo $sFileInfo;
}
?>