<div class="container-fluid user-auth">
    <div></div>
    <div class="col-xs-12">
        <div class="form-container">
            <div>
                <!-- Main Heading Starts -->
                <div class="text-center top-text">
                    <h1>@lang('sms_verify.title_main')</h1>
                    <p>@lang('sms_verify.title_small', ['number' => Auth::user()->phone])</p>
                </div>
            <!-- Main Heading Ends -->
                <!-- Form Starts -->
                <form id="sms_verify_form" method="post" class="custom-form need-validation">
                    <!-- Input Field Starts -->
                    {{csrf_field()}}
                    <div class="form-group">
                        <input class="form-control" style="width: 130px; margin: 0 auto;" name="sms_code" id="sms_code" data-not_valid_text="Пожалуйста, введите код" placeholder="@lang('sms_verify.code_placeholder')" type="tel" required>
                    </div>
                    <!-- Input Field Ends -->
                    <!-- Submit Form Button Starts -->
                    <div class="form-group text-center">
                        <button id="sms_verify_btn" class="custom-button none inline verify">{{ __('sms_verify.verify_btn') }}</button>
                        <a href="#" id="change_phone" class="change-phone">{{ __('sms_verify.change_btn') }}</a>
                    </div>
                    <!-- Submit Form Button Ends -->
                </form>
                <!-- Form Ends -->
                <!-- Modal starts -->
                <!-- HTML-код модального окна -->
                <div id="myModalBox" class="modal fade">
                    <div class="modal-dialog" id="change_phone_dialog">
                        <div class="modal-content">
                            <!-- Заголовок модального окна -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 id="phone_title" class="modal-title">Новый номер</h4>
                            </div>
                            <!-- Основное содержимое модального окна -->
                            <div class="modal-body">
                                <form id="new_phone_form">
                                <input id="new_phone" class="form-control" data-inputmask="'mask': '@lang('register.phone_mask')'" name="new_phone" placeholder="{{ __('register.phone_placeholder') }}" data-length.min=10 data-not_valid_text="Телефон должен содержать минимум 10 цифр" type="tel" value="{{substr(Auth::user()->phone,1,10)}}" required>
                                </form>
                            </div>
                            <!-- Футер модального окна -->
                            <div class="modal-footer">
                                <button type="button" id="update_phone" class="btn btn-danger" data-dismiss="modal">Сохранить изменения</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal ends -->
            </div>
        </div>
        <!-- Copyright Text Starts -->
        <p class="text-center copyright-text">{{ __('index.copyrights') }}</p>
        <!-- Copyright Text Ends -->
    </div>
</div>

<script>

$(document).ready(() => {
    $("#new_phone").inputmask();

    $.ajax({
        url: '/auth/send_verification_sms',
        dataType: "json",
        success: function(data){
            let result = data.result;
            console.log(data);
        }
    });


    $("#change_phone").click(function(e){
        e.preventDefault();
        $("#myModalBox").modal('show');
    });

    $('#update_phone').click(function (e) {
        var form_data = new FormData($('#new_phone_form')[0]);
        $.ajax({
            url: "{{ url('/changePhone') }}",
            method: 'post',
            processData: false,
            contentType: false,
            data: form_data,
            headers: {

                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

            },
            success: function(response){
                parsed_response_status = JSON.parse(response).status;
                console.log(parsed_response_status);
                if(parsed_response_status=="success"){
                    $.ajax({
                        url: '/auth/send_verification_sms',
                        dataType: "json",
                        success: function(data){
                            let result = data.result;
                            console.log(data);
                            let new_phone = $('#new_phone').val();
                            $('#phone').text(new_phone);
                        }
                    });

                }else{
                    alert(JSON.parse(response).msg);
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }

        });


    });

    $("#sms_verify_form").on('not.valid', 'input', function(e){
        showTooltip(this, $(this).data('not_valid_text'), {tag: 'span', class: 'text-danger badge top bg-light'});
    });


    $("#sms_verify_form").on('click', 'input', function(e){
        hideAllTooltips();
    });

    $("#sms_verify_btn").on('click', function(e){
        //e.preventDefault();
        e.preventDefault();
        if(!$("#sms_verify_form")[0].isValid){
            return;
        }
        $.ajax({
            url: '/sms_verify/check_sms_code',
            dataType: "json",
            data:{
                sms_code: $('#sms_code').val(),
            },
            success: function(data){
                let result = data.result;
                if(result == 'done'){
                    window.location.href = '/home';
                } else {
                    message('Ошибка','Код введён не верно. Попробуйте ещё раз');
                }
            }
        });
    });
});
</script>