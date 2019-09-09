let form_data = new FormData();

$(document).on('change', '#new_avatar', function(){
    let photo = this.files[0];
    if(photo){
        if(validatePhoto(photo)) {
            form_data.append('avatar', photo);
            sendPhoto();
        }else{
            alert('Принимаются только файлы форматов jpg и png');
        }
    }
});

function validatePhoto(photo){
    let file_extensions = ['jpeg', 'jpg', 'png'];
    if($.inArray(photo.name.split('.').pop().toLowerCase(), file_extensions) == -1){
        return false;
    }
    return true;
}

function sendPhoto(){
    $.ajax({
        url: "/settings/update_avatar",
        method: 'post',
        processData: false,
        contentType: false,
        data: form_data,
        headers: {

            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

        },
        success: function(response){
            console.log(response);
            if(response["status"] == "fail"){
                let errors = '';
                response["msg"].forEach(function (value, index) {
                    let separator = ', ';
                    if(index == response["msg"].length-1){
                        separator = '.';
                    }
                    errors+= value+separator;
                });
                alert(errors);
            }else{
                updateView(form_data.get('avatar'));
                form_data.delete('avatar');
            }
        },
        error: function(xhr) {
            console.log(xhr);
        }

    });
}

function updateView(photo) {
    let fr = new FileReader();
    let name = photo.name;

    fr.onload = function (e) {
        $('#current_avatar').attr("src", e.target.result);
        $('#sidebar_avatar').attr("src", e.target.result);
    };

    fr.readAsDataURL(photo);
}