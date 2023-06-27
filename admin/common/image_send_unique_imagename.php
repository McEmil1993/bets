<?php
include_once('../_LIB/class_CommonUtil.php');
include_once('../_LIB/base_define.php');
$UTIL = new CommonUtil();
//$UTIL->logWrite('test', "error");

if (isset($_FILES['uploadfile']) ) {
    /*$filename  = $_FILES['uploadfile']['tmp_name'];
    $handle    = fopen($filename, "r");
    $data      = fread($handle, filesize($filename));*/
    
    $headers = array();

    $savePath = trim(isset($_POST['savePath']) ? $_POST['savePath'] : '');
    $saveName = trim(isset($_POST['saveName']) ? $_POST['saveName'] : '');
    $filename  = $_FILES['uploadfile']['name'];
    
    //$filename_ext = strtolower(array_pop(explode('.', $filename)));
    $ext = explode('.',$filename); 
    $filename_ext = strtolower(array_pop($ext));
    $allow_file = array("jpg", "png", "bmp", "gif");
    //$UTIL->logWrite("name : " . $filename_ext, "error");
    
    if (!in_array($filename_ext, $allow_file)) {
        $UTIL->logWrite("NOTALLOW_" . $filename, "error");
    }else{
        $uploadDir = IMAGE_TEMP_PATH;
        $uploadFile = $uploadDir.'/'.basename($filename);
        $UTIL->logWrite($uploadDir);
        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadFile)){
            $file = new stdClass;
            //$file->name = date("YmdHis") . mt_rand() . "." . $filename_ext;
            //$file->content = file_get_contents("php://input");
            $file->name = $saveName;
            $file->content = file_get_contents($uploadFile);

            $UTIL->logWrite($file->name, "error");
            //$UTIL->logWrite($uploadFile, "error");

            $POST_DATA = array(
              //'somevar' => $somevar,
              //'uploadfile' => $data
             'fileName' => $file->name,
             'fileData' => base64_encode($file->content),
             'savePath' => $savePath
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, IMAGE_SERVER_UPLOAD_URL);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
            $response = curl_exec($curl);
            curl_close ($curl);
            echo $response;

            // Now delete local temp file
            unlink($uploadFile);
        }else{
            $UTIL->logWrite("possible file upload attack!\n", "error");
        }
    }
}
?>