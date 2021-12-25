    
<?php
        
    function json_read($filename) {
        $file = fopen($filename, "r");
        $data = fread($file, filesize($filename));
        fclose($file);
        return json_decode($data, true);
    }

    function json_write($filename, $data) {
        $file = fopen($filename, "w");
        fwrite($file, json_encode($data));
        fclose($file);
    }

    function get_user($id) {
        $filename = "assets/data/users.json";
        $users = json_read($filename);

        foreach ($users as $user) {
            if ($user["id"] == $id) {
                return [
                    "found" => true,
                    "user" => $user
                ];
            }
        }

        return [
            "found" => false
        ];
    }

    function login($post) {
        $id = $post["id"];
        $passcode = $post["passcode"];
        
        $users = json_read("assets/data/users.json");

        foreach ($users as $user) {
            if ($user["id"] == $id && $user["passcode"] == $passcode) {
                $token = generate_token($id);
                
                setcookie("login_status", true, time() + (3600*24));
                setcookie("id", $id, time() + (3600*24));

                return [
                    "login_status"=> 1, 
                    "name"=> $user["name"],
                    "token"=> $token,
                ];
            }
        }

        return [
            "login_status"=> 0,
            "alerts"=> "<span class='alert'><span class='alert_close'><i class='fad fa-times-circle'></i></span> WRONG CREDENTIAL !!!<span>",
        ];
    }

    function generate_token($id) {
        $filename = "assets/data/tokens.json";
        $tokens = json_read($filename);

        $new_token = "new_token";

        $tokens[$id] = $new_token;
        json_write($filename, $tokens);
        return $new_token;
    }

    function varify_token($post) {
        $filename = "assets/data/tokens.json";
        $tokens = json_read($filename);
        if (isset($post["token"])) {
            if ($tokens[$post["id"]] == $_POST["token"]) {
                unset($tokens[$post["id"]]);
                json_write($filename, $tokens);
                return true;
            }
        }
        return false;
    }

    function enable_chat($cookie) {
        ?>
            <div class="chat_section">
                <?php
                    $user = get_user($cookie["id"])["user"];

                    $filename = "assets/data/chats.json";
                    $chats = array_reverse(json_read($filename));
                ?>

                <div class="chat_heading">
                    Chatting as: <?php echo $user["name"] ?>
                    <button id="logout_btn">Logout</button>
                </div>

                <div class="chat_input">
                    <input type="text" name="chat" id="chat" placeholder="type here to chat">
                    <button class="send_btn"><i class="fad fa-paper-plane" id="send_btn"></i> send</button>
                </div>

                <div class="chat_box" length="<?php echo sizeof($chats) ?>">
                    <?php

                        $user = get_user($cookie["id"])["user"];

                        foreach ($chats as $chat):
                            $class = "other";
                            if ($chat["name"] == $user["name"]) {
                                $class = "me";
                            }
                            ?>
                            <div class="<?php echo $class ?>">
                                <span class="<?php echo "{$class}_name" ?>"><?php echo $chat["name"] ?></span>
                                <span class="colon">:</span>
                                <span class="text_<?php echo $class ?>"><?php echo $chat["text"] ?></span> 
                            </div>
                            <?php
                        endforeach;
                    ?>
                </div>                
            </div>        
        <?php      
    }

    function disable_chat() {

        setcookie("login_status", false, time() - 3600);
        setcookie("id", "", time() - 3600);

        ?>
            <div class="login_section">
                <div class="id">
                    <label>ID:</label>
                    <input type="text" name="id" id="id" placeholder="Enter ID">
                </div>
                <div class="passcode">
                    <label>Passcode:</label>
                    <input type="password" name="passcode" id="passcode" placeholder="Enter Passcode">
                </div>
                <button class="login_btn" id="login_btn">Login</button>
            </div>        
        <?php
    }

    function insert($post, $cookie) {

        $users = json_read("assets/data/users.json");
        $id = $cookie["id"];
        $name = "Unknown";

        foreach ($users as $user) {
            if ($user["id"] == $id) {
                $name = $user["name"];
            }
        }

        $text = $post["text"];
    
        $filename = "assets/data/chats.json";
        $chats = json_read($filename);
    
        array_push($chats, ["name"=>$name, "text"=> $text]);

        json_write($filename, $chats);
    }

    function load($post, $cookie) {
        $length = $post["length"];

        $filename = "assets/data/chats.json";
        $chats = json_read($filename);

        $new_chats = "";
        if ($length < sizeof($chats)) {

            $user = get_user($cookie["id"])["user"];

            for ($i=sizeof($chats)-1; $i>=$length; $i--) {
                $class = "other";
                if ($chats[$i]["name"] == $user["name"]) {
                    $class = "me";
                }
                $new_chats .= "<div class='{$class}'>";
                $new_chats .= "<span class='{$class}_name'>{$chats[$i]['name']}</span>";
                $new_chats .= "<span class='colon'> : </span>";
                $new_chats .= "<span class='text_{$class}'>{$chats[$i]['text']}</span></div>";
                $new_chats .= "</div>";
            }
        }

        echo json_encode(["new_chats_len"=>sizeof($chats)-$length, "new_chats"=>$new_chats]);
    }
?>