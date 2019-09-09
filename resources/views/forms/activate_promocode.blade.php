<div class="form-wrap" id="promocode_activate_form">

        <form action="/settings/activate_promocode" method="post" data-toggle="validator">
            {{csrf_field()}}
            <div class="form-body overflow-hide">
                <div class="form-group">
                    <div class="input-group has-feedback">
                        <div class="input-group-addon"><i class="icon-diamond"></i></div>
                        <input type="text" class="form-control text-uppercase"  id="promocode" name="promocode" data-minlength="6" maxlength="6" placeholder="@lang('settings.promo.enter')" required>
                    </div>
                </div>
            </div>
            <div class="form-actions mt-10">
                @if (session('promo_status')=='fail')
                    <div class="alert alert-danger">
                        @lang('settings.promo.error');
                        @foreach(session('error') as $error)
                            {{$error}}<br>
                        @endforeach
                    </div>
                @endif
                @if(session('promo_status')=='success')
                    <div class="alert alert-success">
                         {{session('msg')}}
                    </div>
                @endif
                <button type="submit" class="btn btn-danger mr-10 mb-30">@lang('settings.promo.activate')</button>
            </div>

        </form>

    <div class="form-group">
        <label class="control-label mb-10">@lang('settings.promo.proposal')</label></br>
        <label class="control-label mb-10">@lang('settings.promo.private'):</label> <label id="personal_promocode" class="label label-primary" style="font-size: 13px;">{{$promocode->promo_code}}</label>
    </div>
    <div class="form-actions mt-10">
        <button id="copy" data-clipboard-target="#personal_promocode" class="btn btn-primary mr-10 mb-30">@lang('settings.promo.copy')</button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(e){
        new Clipboard('#copy');
    });
</script>