<div class="modal fade" id="user-message" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Header</h4>
            </div>
            <div class="modal-body">
                message
            </div>
            <div class="modal-footer">
                <button id="close_message_btn" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script>
    function message(title, content='', options={} , exit_text='Close'){
        console.log('message title = "'+title+'"');
        console.log('message content = "'+content+'"');
        let message_box = $('#user-message');
        let title_box   = message_box.find(".modal-header > h4");
        let content_box = message_box.find(".modal-body");
        let exit_btn    = message_box.find(".modal-footer > button");

        title_box.html(title);
        content_box.html(content);
        exit_btn.html(exit_text);
        if('width' in options || 'height' in options){
            message_box.removeAttr('style');
        }
        if('width' in options){
            console.log('has width');
            message_box.css("width", options.width);
        }
        if('height' in options){
            console.log('has height');
            message_box.css("height", options.height);
        }
        message_box.modal('show');
    }
</script>
