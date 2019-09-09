class Message{
    static send(form_data){
        $.ajax({
            url: "/add_message",
            method: 'post',
            processData: false,
            contentType: false,
            data: form_data,
            headers: {

                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

            },
            success: function(response){
                let parsedResponse = JSON.parse(response);
                if(parsedResponse.status == "fail"){
                    alert('Ошибка! '+ parsedResponse.msg);
                }else{
                        Message.getAll(true);
                }
            },
            error: function(xhr) {
                alert('Что-то пошло не так');
            }

        });
    }
    static getAll(toBottom){
        return $.ajax({
            url: "/get_messages",
            method: 'get',
            processData: false,
            contentType: false,
            headers: {

                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

            },
            success: function(response){
                updateView(response);
                if(toBottom) {
                    goBottom();
                }
                if(!scroll_init) {
                    initScroll();
                    scroll_init = true;
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }

        });
    }
    static checkNew(){
        return $.ajax({
            url: "/check_new",
            method: 'get',
            processData: false,
            contentType: false,
            headers: {

                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

            }
        })
    }
}