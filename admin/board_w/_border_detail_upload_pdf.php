<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

if(isset($_FILES['pdf_attachment']["name"]))
{
    $tmp_name       = $_FILES['pdf_attachment']['tmp_name'];
    $filename       = $_FILES['pdf_attachment']['name'];
    $name           = $_FILES['pdf_attachment']['name'];
    $save_path      = "/" . IMAGE_PATH . "/smarteditor/";
    $allow_file     = array("pdf");
    $filename_ext   = pathinfo($name, PATHINFO_EXTENSION);
    $save_name      = time() . "_" . $filename;

    if (!in_array($filename_ext, $allow_file))
    {
        die("Invalid File Type");
    }
    else
    {
        $uploadDir = IMAGE_TEMP_PATH;
        $uploadFile = $uploadDir.'/'.basename($filename);

        //for local development - only triggers when using windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $uploadFile = dirname(__FILE__) . "/" . basename($filename);
        }

        if (move_uploaded_file($_FILES['pdf_attachment']['tmp_name'], $uploadFile))
        {
            $file = new stdClass;
            $file->name = $save_name;
            $file->content = file_get_contents($uploadFile);

            $POST_DATA = array('fileName' => $file->name, 'fileData' => base64_encode($file->content), 'savePath' => $save_path);

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, IMAGE_SERVER_UPLOAD_URL);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);

            $response = curl_exec($curl);
            curl_close ($curl);
            unlink($uploadFile);

            $response_message                   = new stdClass;
            $response_message->save_name        = $file->name;
            $response_message->name             = $name;
            $response_message->save_path        = $save_path;
            $response_message->temp_path        = $uploadFile;
            $response_message->filename_ext     = $filename_ext;
            $response_message->curl_response    = $response;
            $response_message->full_path        =  IMAGE_SERVER_URL . $save_path . $save_name;

            echo json_encode($response_message);
        }
    }
}
?>