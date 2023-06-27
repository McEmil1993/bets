<?php

// default redirection
$url = 'callback.html?callback_func=' . $_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
if (bSuccessUpload) {
    $tmp_name = $_FILES['Filedata']['tmp_name'];
    $name = $_FILES['Filedata']['name'];

    $filename_ext = strtolower(array_pop(explode('.', $name)));
    $allow_file = array("jpg", "png", "bmp", "gif");

    if (!in_array($filename_ext, $allow_file)) {
        $url .= '&errstr=' . $name;
    } else {


        $uploadDir = '../../upload_tem/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777);
        }

        $newPath = $uploadDir . urlencode($_FILES['Filedata']['name']);

        @move_uploaded_file($tmp_name, $newPath);
        //$host_ip = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $host_ip = 'http://210.175.73.221';
        $url .= "&bNewLine=true";
        $url .= "&sFileName=" . urlencode(urlencode($name));
        //$url .= "&sFileURL=" . $host_ip . "/smarteditor28/upload_tem/" . urlencode(urlencode($name));
        $url .= "&sFileURL=" . $host_ip . "/dev/notice/" . urlencode(urlencode($name));


        // Move uploaded file to a temp location
        $uploadDir = '/var/www/uploads/';
        $uploadFile = $uploadDir . basename($name);
        if (move_uploaded_file($tmp_name, $uploadFile)) {
            // Prepare remote upload data
            $uploadRequest = array(
                'fileName' => basename($uploadFile),
                'fileData' => base64_encode(file_get_contents($uploadFile)),
                'savePath' => "dev/notice"
            );

            // Execute remote upload
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'http://210.175.73.221/receiver.php');
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $uploadRequest);
            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;

            // Now delete local temp file
            unlink($uploadFile);
        } else {
            echo "Possible file upload attack!\n";
        }
    }
}
// FAILED
else {
    $url .= '&errstr=error';
}

header('Location: ' . $url);
?>