<?php

    include_once "functions.php";

    $job = $_POST["job"];
    // $job = "test";

    if ($job == "load") {
        load($_POST, $_COOKIE);
    }
    elseif ($job == "insert") {
        insert($_POST, $_COOKIE);
    }
    elseif ($job == "enable_chat") {
        if (varify_token($_POST)) {
            enable_chat($_COOKIE);
        }
        else {
            echo "NO CHANCHE";
        }
    }
    elseif ($job == "login") {
        echo json_encode(login($_POST));
    }
    elseif ($job == "logout") {
        disable_chat();
    }
    // else {
    //     $data = enable_chat();
    // }
?>