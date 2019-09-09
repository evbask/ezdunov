<div class="container-fluid user-auth">
    <div></div>
    <div class="col-xs-12">
        <div class="form-container">
            <div>
                <!-- Main Heading Starts -->
            <div class="text-center top-text">
                <h1>@lang('login.title_main')</h1>
                <p>{{ __('login.title_small') }}</p>
            </div>
            <!-- Main Heading Ends -->
                <!-- Form Starts -->
                <form id="login_form" method="post" action="/auth/login" class="custom-form">
                    <!-- Input Field Starts -->
                    {{csrf_field()}}
                    <div class="form-group">
                        <input class="form-control" name="email" id="email" data-not_valid_text="Пожалуйста, введите email или телефон" placeholder="{{ __('login.email_phone') }}" type="text" required>
                    </div>
                    <!-- Input Field Ends -->
                    <!-- Input Field Starts -->
                    <div class="form-group">
                        <input class="form-control" name="password" id="password" data-not_valid_text="Пожалуйста, введите пароль" placeholder="{{ __('login.password') }}" type="password" required>
                    </div>
                    <!-- Input Field Ends -->
                    <!-- Submit Form Button Starts -->
                    <div class="form-group">
                        <button class="custom-button login" type="submit">{{ __('login.login') }}</button>
                        <p class="text-center">{{ __('login.do_not_have') }} ? <a href="/register">{{ __('login.register') }}</a>
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
//$(":input").inputmask();
$(document).ready(() => {
    
    $("#login_form").on('not.valid', 'input', function(e){
        showTooltip(this, $(this).data('not_valid_text'), {tag: 'span', class: 'text-danger badge top bg-light'});
    });

    $("#login_form").on('click', 'input', function(e){
        hideAllTooltips();
    });

    $("#login_form").on('form.valid', function(e){
        //e.preventDefault();
        $('#login_form').submit();
        /*$.ajax({
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
                    alert('Пользователь с такими данными уже зарегистрирован в системе');
                }
            }
        });*/
    });
});
</script>