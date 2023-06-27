<?php
    /* initialized imports of libaries */
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
    include_once(_BASEPATH . '/common/_common_inc_class.php');
    include_once(_DAOPATH . '/class_Admin_Common_dao.php');

    /* update t_game_config's secondary password on the database */
    $db = new Admin_Common_DAO(_DB_NAME_WEB);
    $db_conn = $db->dbconnect();

    if ($db_conn)
    {
        $key            = "jj5YUsqjwn6hYn5GsAu";
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body, true);
        $decoded        = explode("-", base64_decode($data["second_pass"]));
        $channel        = $data["channel"];

        /* get new secondary pin from decoded message */
        $new_secondary_password =  $decoded[0];
        
        /* validate key */
        if($key != $decoded[1])
        {
            echo "error";
        }
        else
        {
            $hashed         = hash("sha512", $new_secondary_password);
            $query          = "UPDATE `t_game_config` SET `set_type_val`='$hashed', `reg_time`=now() WHERE `set_type`='second_pass'";
            $result         = $db->execute_query($query);

            echo "success";
        }
    }
    else
    {
        die("DB Connection Failed");
    }

    /* send new password to telegram */
    $site       = TITLE;
    $botToken   = "6083189467:AAGCLFD7EHol-zsdGmK9W5F90kCmnIaL4qY";
    $website    = "https://api.telegram.org/bot" . $botToken;

    if($channel == "dev")
    {
        $chatId     = "-953610373";
    }
    else
    {
        $chatId     = "-878647085";
    }
    
    $message    = "New Secondary Password for $site is $new_secondary_password";
    $params     = ['chat_id'=> $chatId, 'text'=> $message];
    $ch         = curl_init($website . '/sendMessage');

    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
?>