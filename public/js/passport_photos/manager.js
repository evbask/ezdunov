let image_selector = $('#replaced_photo');
let submit = $('#update_request');
let current_photo_id;
let replaced_id = [];
let form_data = new FormData();
let basic_photos_container = $('.editable_photos_container').html();
let photos_count;

$(document).on("input",function(ev){
    submit.removeAttr('disabled');
});

$(document).on('click', '.replace_photo', function(e){
    e.preventDefault();
    current_photo_id = $(this).attr('href');
    image_selector.click();
});

$(document).on('change', '#replaced_photo', function(){
    let photo = this.files[0];
    if(photo){
        if(validatePhoto(photo)) {
            submit.removeAttr('disabled');
            let fr = new FileReader();
            let name = photo.name;
            fr.onload = function (e) {
                let thumb = '<figure><img src="' + e.target.result + '" class="editable_photos"> <figcaption>' + name + '</figcaption></figure>';
                $('#' + current_photo_id).replaceWith(thumb);
            };
            fr.readAsDataURL(photo);
            replaced_id.push(current_photo_id);
            form_data.append('passport_photo[]', photo);
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

submit.click(function(e){
   e.preventDefault();
   $(this).attr('disabled', true);
    let lang = localizator();
    form_data.append('passport_number',$('#passport_number').val());
    form_data.append('date_of_birth',$('#date_of_birth').val());
    photos_count = replaced_id.length;
    photos_id = JSON.stringify(replaced_id);
    if(photos_count > 0){
       form_data.append('replaced_id', photos_id);
   }
    $.ajax({
        url: "/update_passport",
        method: 'post',
        processData: false,
        contentType: false,
        data: form_data,
        headers: {

            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

        },
        success: function(response){
            let parsedResponse = JSON.parse(response);

            if(parsedResponse.status == "success"){
                $("#status").removeClass("alert alert-danger");
                $("#status").addClass("alert alert-success");
                setTimeout('location.replace("/home")',1000);
                $("#status").html('<strong>'+parsedResponse.msg+'</strong>');
                replaced_id = [];
            }else{
                $("#status").addClass("alert alert-danger");
                let errors = '<strong>';
                parsedResponse.msg.forEach(function(error){
                    errors += error + "<br/>";
                });
                errors += '</strong>';

                rollback();
                $("#status").html(lang['message'] + errors);
            }
        },
        error: function(xhr) {
            $("#status").addClass("alert alert-danger");
            $("#status").html(lang['smth']);
            rollback();
        }

    });

});

function rollback(){
    form_data.delete('passport_number');
    form_data.delete('date_of_birth');
    if(photos_count > 0){
        $('.editable_photos_container').html(basic_photos_container);
        replaced_id = [];
        form_data.delete('replaced_id');
        form_data.delete('passport_photo[]');
        $('#replaced_photo').val('');
    }
}



