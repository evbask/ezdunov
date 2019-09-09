<div class="profile-box">
    <div class="profile-cover-pic">
        <div class="profile-image-overlay"></div>
    </div>
    <div class="profile-info text-center">
        <div class="profile-img-wrap">
            <img id="current_avatar" class="inline-block mb-10" src="{{asset('img/avatars/'.$user->avatar)}}" alt="user"/>
            <div class="fileupload btn btn-default">
                <span class="btn-text">@lang('settings.profile.change_avatar')</span>
                <input id="new_avatar" class="upload" type="file">
            </div>
        </div>
        <h5 class="block mt-10 mb-5 weight-500 capitalize-font txt-warning">{{$user->name}}</h5>
        <h6 class="block capitalize-font pb-20 txt-light">
            @switch($user->role)
            @case('user')
            @lang('settings.profile.user')
            @break
            @case('admin')
            @lang('settings.profile.admin')
            @break
            @endswitch
        </h6>
    </div>
    <div class="social-info">
        <div class="row">
            <div class="col-xs-4 text-center">
                <span class="counts block head-font txt-light"><span>0</span></span>
                <span class="counts-text block txt-light">@lang('settings.profile.rents')</span>
            </div>

            <div class="col-xs-4 text-center">
                <span class="counts block head-font txt-light"><span>0</span></span>
                <span class="counts-text block txt-light">@lang('settings.profile.bonus')</span>
            </div>

            <div class="col-xs-4 text-center">
                <span class="counts block head-font txt-light"><span>0</span></span>
                <span class="counts-text block txt-light">@lang('settings.profile.invited')</span>
            </div>

        </div>
    </div>
</div>
<script src="{{asset('js/grandin/profile_manager.js')}}" type="text/javascript"></script>