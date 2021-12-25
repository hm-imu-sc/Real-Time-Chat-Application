let chats_loader;

function send_message() {
    let text = $("#chat").val();

    if (text.length <= 0) {
        return;
    }

    $.ajax({
        url: "chat_processor.php",
        type: "POST",
        data: {job: "insert", text: text},
        success: function(data){
            // let chat_box_content = $(".chat_box").html();
            // $(".chat_box").html("<p>" + name + " => " + text + "</p><br>" + chat_box_content);
            // $(".chat_box").attr("length", Number($(".chat_box").attr("length"))+1);
            $("#chat").val('');
        }
    });
}

function enable_chat(data) {
    $(".login_section").after(data);

    $("#chat").keyup(function(e){
        if (e.key == "Enter") {
            send_message();
        }
    });
    
    $(".send_btn").click(send_message);
    
    $("#logout_btn").click(logout);

    chats_loader = setInterval(function(){
        let current_length = Number($(".chat_box").attr("length"));
        // console.log(current_length);
    
        $.ajax({
            url: "chat_processor.php",
            type: "POST",
            data: {job: "load", length: current_length},
            datatype: "json",
            success: function(data) {
    
                data = JSON.parse(data);
    
                if (data["new_chats_len"] == 0) {
                    return;
                }
    
                let chat_box_content = $(".chat_box").html();
                $(".chat_box").html(data["new_chats"] + chat_box_content);
                $(".chat_box").attr("length", current_length + data["new_chats_len"]);
            }
        });
    
    }, 1000);   
}

function login() {

    let id = $("#id").val();
    let passcode = $("#passcode").val();

    $.ajax({
        url: "chat_processor.php",
        type: "POST",
        data: {job: "login", id: id, passcode: passcode},
        success: function(data) {

            data = JSON.parse(data);
            
            if (data["login_status"] == 1) {
                $.ajax({
                    url: "chat_processor.php",
                    type: "POST",
                    data: {job: "enable_chat", id: id, token: data["token"]},
                    success: function(data) {
                        enable_chat(data); 
                        $(".login_section").remove();
                    }
                });
            }
            else {
                if ($(".alert_section").length==0) {
                    $(".login_section").prepend("<div class='alert_section'></div>");
                }
                $(".alert_section").html(data["alerts"]);
                $(".alert_close").click(function(){
                    $(".alert_section").remove();
                });
            }
        }
    });
}

function activate_login() {
    $("#login_btn").click(login);
    $("#passcode").keyup(function(e) {
        if (e.key == "Enter") {
            login();
        }
    });
}

function logout() {
    $.ajax({
        url: "chat_processor.php",
        type: "POST",
        data: {job: "logout"},
        success: function(data) {
            $(".chat_section").before(data);    
            activate_login();
            clearInterval(chats_loader);    
            $(".chat_section").remove();
        }
    });
}

if ($(".chat_section").length == 1) {
    enable_chat("");
}
else {
    activate_login();
}
 