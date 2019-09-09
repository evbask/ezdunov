<div class="form-wrap">
    <form id="edit_settings_form" method="post" action="/settings/update_profile" data-toggle="validator" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-body overflow-hide">
            <div class="form-group">
                <label class="control-label mb-10" for="editable_name">@lang('settings.edit_user.name')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-user"></i></div>
                    <input type="text" class="form-control" id="editable_name" name="name" placeholder="Иван" value="{{$user->name}}" required>
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <label class="control-label mb-10" for="editable_phone">@lang('settings.edit_user.phone')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-phone"></i></div>
                    <input class="form-control" data-inputmask="'mask': '@lang('register.phone_mask')'" name="phone" id="editable_phone" placeholder="{{ __('register.phone_placeholder') }}" data-length.min=10 data-not_valid_text="Телефон должен содержать минимум 10 цифр" type="tel" value="{{substr($user->phone,1,10)}}" required>
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
                <label class="control-label mb-10" for="editable_email">Email</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-envelope-open"></i></div>
                    <input type="email" class="form-control" name="email" id="editable_email" data-error="Введите корректный формат Email." placeholder="ivan@gmail.com" value="{{$user->email}}">
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
                <label class="control-label mb-10" for="current_password">@lang('settings.edit_user.old_pass')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-lock"></i></div>
                    <input type="password" class="form-control" name="current_password" id="current_password" autocomplete="new-password" placeholder="@lang('settings.edit_user.enter_pass')" value="">
                </div>
            </div>

            <div class="form-group has-feedback">
                <label class="control-label mb-10" for="new_password">@lang('settings.edit_user.new_pass')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-lock"></i></div>
                    <input type="password" data-minlength="6" class="form-control" id="new_password" name="new_password" data-error="@lang('validation.messages.passwords.length')" placeholder="@lang('settings.edit_user.enter_pass')">
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
                <label class="control-label mb-10" for="repeated_password">@lang('settings.edit_user.repeat')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-lock"></i></div>
                    <input type="password" data-minlength="6" class="form-control" id="repeated_password" name="repeated_password"  data-match="#new_password" data-match-error="@lang('validation.messages.passwords.same')" data-error="@lang('validation.messages.passwords.length')" placeholder="@lang('settings.edit_user.repeat_pass')">
                </div>
                <div class="help-block with-errors"></div>
            </div>

        </div>

        <div class="form-actions mt-10" id="result">
            @if (session('status')=='fail')

                <div class="alert alert-danger">
                    @lang('settings.edit_user.fail')
                   @foreach(session('error') as $error)
                        {{$error}}<br>
                   @endforeach
                </div>
            @elseif(session('status')=='success')
                <div class="alert alert-success">
                    {{session('msg')}}
                </div>
            @endif
            <button type="submit" class="btn btn-primary mr-10 mb-30">@lang('settings.edit_user.update')</button>
        </div>



    </form>
</div>
<script type="text/javascript">

    $(document).ready(() =>{
        $(":input").inputmask();
    });

</script>