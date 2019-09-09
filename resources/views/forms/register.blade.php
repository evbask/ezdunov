<div class="container-fluid user-auth">
    <div></div>
    <div class="col-xs-12">
            <div class="form-container">
                <div>
                    <!-- Main Heading Starts -->
                <div class="text-center top-text">
                    <h1>@lang('register.title_main')</h1>
                    <p>{{ __('register.title_small') }}</p>
                </div>
                <!-- Main Heading Ends -->
                    <!-- Form Starts -->
                    <form id="register_form" class="custom-form need-validation" action="/register/new_user">
                        <!-- Input Field Starts -->
                        {{csrf_field()}}
                        <div class="form-group">
                            <input class="form-control" name="fio" id="fio" placeholder="{{ __('register.fio_placeholder') }}" data-not_valid_text="Пожалуйста, заполните это поле" type="text" required>
                        </div>
                        <!-- Input Field Ends -->
                        <!-- Input Field Starts -->
                        <div class="form-group">
                            <input class="form-control" data-inputmask="'mask': '@lang('register.phone_mask')'" name="phone" id="phone" placeholder="{{ __('register.phone_placeholder') }}" data-length.min=10 data-not_valid_text="Телефон должен содержать минимум 10 цифр" type="tel" required>
                        </div>
                        <!-- Input Field Ends -->
                        <!-- Input Field Starts -->
                        <div class="form-group">
                            <input class="form-control" name="email" id="email" data-regexp.mask='\S+@\S+\.\S+' data-not_valid_text="Пожалуйста введите валидный e-mail адрес" placeholder="{{ __('register.email_placeholder') }}" type="email" required>
                        </div>
                        <!-- Input Field Ends -->
                        <!-- Input Field Starts -->
                        <div class="form-group">
                            <input class="form-control" name="password" id="password" data-length.min=6 data-not_valid_text="Пароль должен состоять минимум из 6 символов"   placeholder="{{ __('register.password_placeholder') }}" type="password" required>
                        </div>
                        <!-- Input Field Ends -->
                         <!-- Input Field Starts -->
                         <div class="form-group">
                            <input class="form-control" name="password_1" id="password_1" data-length.min=6 data-same.as="password" data-not_valid_text="Пароли не совпадают" placeholder="{{ __('register.password_1_placeholder') }}" type="password" required>
                        </div>
                        <!-- Input Field Ends -->
                        <!-- Submit Form Button Starts -->
                        <div class="form-group">
                            <input type="checkbox" id="terms_agreed" name="terms_agreed" data-checked=true required>
                            <label for="terms_agreed">@lang('register.terms_title')</label>
                        </div>
                        <div class="form-group">
                            <button id="register_submit_btn" class="custom-button" disabled=true type="submit">{{ __('register.create_account') }}</button>
                            <p class="text-center">{{ __('register.allready_have') }} ? <a href="/login">{{ __('register.login') }}</a>
                        </div>
                        <!-- Submit Form Button Ends -->
                    </form>
                    <!-- Form Ends -->
                </div>
            </div>
            <!-- Copyright Text Starts -->
            <p class="text-center copyright-text">{{ __('index.copyrights') }}</p>
            <!-- Copyright Text Ends -->
        </div>
    </div>


<script>

$(document).ready(() => {
    $(":input").inputmask();
    $("#register_form").on('not.valid', 'input', function(e){
        if($(this).data('not_valid_text') !== undefined){
            showTooltip(this, $(this).data('not_valid_text'), {tag: 'span', class: 'text-danger badge top bg-light'});
        }
    });
    
    $("#register_form").on('input.valid', 'input', function(e){
        hideTooltip(this);
    });

    $("#register_form").on('form.not.valid', function(e){
        $("#register_submit_btn").prop('disabled', true);
    });
    
    $("#register_form").on('form.valid', function(e){
        $("#register_submit_btn").prop('disabled', false);
    });

    $("#register_submit_btn").on('click', function(e){
        e.preventDefault();
        if(!$("#register_form")[0].isValid){
            return;
        }
        $.ajax({
            url: '/register/checkUser',
            dataType: "json",
            data:{
                email: $('#email').val(),
                phone: $('#phone').val()
            },
            success: function(data){
                let result = data.result;
                if(result){
                    $("#register_form").submit();
                } else {
                    message("{{ __('register.used_data_message_title') }}", "{{ __('register.used_data_message_content') }}");
                }
            }
        });
    });
});
</script>