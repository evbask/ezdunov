let interval = $('#interval').val() * 1000;
let scroll_init = false;
let msgContainer = $('.chatapp-chat-nicescroll-bar');
let options = {height:'490px',size: '4px',color: '#878787',disableFadeOut : true,borderRadius:0, start: 'bottom', useFixedHeight : true, fixedHeight : 200}
let form_data = new FormData();
let lang = localizator();
form_data.append('type', '');
form_data.append('data', '');


$(document).ready(function(){
    Message.checkNew();
    Message.getAll();

    setInterval(function() {
        Message.checkNew().success(function(response){
            let new_messages = parseResponse(response).msg;
           if(new_messages){
               if(isAtBottom()){
                   getAllAndBottom();
               }else{
                   Message.getAll(); $('#go_bottom').css('visibility','visible');
               }

           }else{
               Message.getAll();
           }


        });

    }, interval);

});

$(document).on("keypress","#input_msg_send_chatapp",function (e) {
    if (e.which == 13) {
        e.preventDefault();
        $("#send_msg_button").click();
    }
    return;
});

$("#send_msg_button").click(function(e){
    if ($("#input_msg_send_chatapp").val().length > 0){
        let date = getCurrentDate();
        let data = $("#input_msg_send_chatapp").val();
        // $('<li class="self mb-10"><div class="self-msg-wrap"><div class="msg block pull-right">' + data + '<div class="msg-per-detail mt-5"><span class="msg-time txt-grey">'+date+'</span> <span class="msg-time txt-grey">Доставлено</span></div></div></div><div class="clearfix"></div></li>').insertAfter(".chat-for-widgets-1 .chat-content  ul li:last-child");
        //  msgContainer.append('<li class="self mb-10"><div class="self-msg-wrap"><div class="msg block pull-right">' + data + '<div class="msg-per-detail mt-5"><span class="msg-time txt-grey">'+date+'</span> <span class="msg-time txt-grey">Доставлено</span></div></div></div><div class="clearfix"></div></li>');
        form_data.set('type', 1);
        form_data.set('data', data);
        let msg = {data: data, date: date};
        Message.send(form_data);
        $("#input_msg_send_chatapp").val('');
    }else{
        $.toast().reset('all');
        $.toast({
            heading: lang["error"],
            text: lang["enter_msg"],
            position: 'top-right',
            loaderBg:'red',
            icon: 'error',
            hideAfter: 3500,
            stack: 6
        });
    }
});

$("#chat_files").change(function(e) {
    let files = e.target.files;
    if (files.length > 0){
        let confirm = files_confirm_send(files);
        if(confirm){
            let form_data = new FormData($('#msg_form')[0]);
            form_data.set('type', 2);
            Message.send(form_data);
        }
    }
});

function updateView(messages){
    $('#msgContainer').html('');
    $.each(messages, function (index, message) {
        let is_user = message["user_id"] == message["chat_id"];
        let data = (message["type"] == 2) ? '<span style="text-decoration: underline"><a href="'+message["data"]+'" target="_blank">'+ basename(message["data"])+'</a></span>' : message["data"];
        let status = (message["viewed"]) ? lang["viewed"] : lang["delivered"];
        let li_class, div_class, div_child_class;
        if(is_user){
            li_class = 'self mb-10';
            div_class = 'self-msg-wrap';
            div_child_class = 'msg block pull-right';
        }else{
            li_class = 'friend';
            div_class = 'friend-msg-wrap';
            div_child_class = 'msg pull-left';
            status = '';
        }
        let message_block = '<li class="'+li_class+'"><div class="'+div_class+'"> <div class="'+div_child_class+'">'+data+' <div class="msg-per-detail text-right"><span class="msg-time txt-grey">'+message["created"]+'</span> <span class="msg-time txt-grey">'+status+'</span> </div></div> <div class="clearfix"></div> </div> </li>';
        $('#msgContainer').append(message_block);
    });

}

$('#go_bottom').click(function(){
    goBottom();
    $('#go_bottom').css('visibility', 'hidden');
});

function getAllAndBottom() {
    return Message.getAll(true);
}








