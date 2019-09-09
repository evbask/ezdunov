<div class="form-wrap">
    <form id="edit_passport_form" method="post" action="/update_passport" data-toggle="validator" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-body overflow-hide">

            <div class="form-group">
                <label class="control-label mb-10" for="edit_passport_number">@lang('passport_verify.passport_number')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-notebook"></i></div>
                    <input class="form-control" name="passport_number" id="passport_number" data-not_valid_text="Пожалуйста, введите Серию и номер паспорта" value="{{$editable_request["passport_number"]}}" placeholder="{{ __('passport_verify.passport_number') }}" type="tel"  required>
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <label class="control-label mb-10" for="edit_passport_date">@lang('passport_verify.date_of_birth')</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="icon-calender"></i></div>
                    <input class="form-control" type="date" name="date_of_birth" id="date_of_birth" data-not_valid_text="Пожалуйста, введите дату рождения" value="{{$editable_request["date_of_birth"]}}" placeholder="{{ __('passport_verify.date_of_birth') }}" required>
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
                <label class="control-label mb-10" for="admin_comment">@lang('passport_verify.admin_comment')</label>
                <div class="input-group">
                    <textarea class="form-control" rows="5" cols="300" name="admin_comment" id="admin_comment" disabled>{{$editable_request["comment_to_user"]}}</textarea>
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
                <label class="control-label mb-10" for="passport_images">@lang('passport_verify.passport_photos')</label>
                <div class="editable_photos_container">
                    @foreach($editable_request["photos"] as $photo)
                        @php $url = $photo["url"] @endphp
                            <figure id="{{$photo["id"]}}">
                                <img src="{{$url}}" class="editable_photos">
                                <figcaption><a href="{{$photo["id"]}}" class="replace_photo">@lang('passport_verify.replace_photo')</a></figcaption>
                            </figure>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="form-actions mt-10">
            <div id="status" ></div>
            <button type="submit" id="update_request" class="btn btn-primary mr-10 mb-30" disabled>@lang('passport_verify.save_changes')</button>
        </div>

    </form>
</div>
<input style="display: none" id="replaced_photo" class="form-control" name="replaced_photo" placeholder="Первая и вторая страницы" type="file" />
<script  src="{{asset('js/passport_photos/manager.js')}}" type="text/javascript"></script>
<script  src="{{asset('js/localizator.js')}}" type="text/javascript"></script>
<script  src="{{asset('js/passport_photos/zoom.js')}}" type="text/javascript"></script>