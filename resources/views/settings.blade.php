@extends('common.home')
@section('content')
    @if (session('promo_status'))
        @php $promocode_status = "active"; $edit_profile_status = "";  @endphp @else
        @php $edit_profile_status = "active"; $promocode_status = ""; @endphp
    @endif

    <div class="lk-content">
        <!-- Row -->
        <div class="row">
            <div class="col-lg-3 col-xs-12">
                <div class="panel panel-default card-view  pa-0 bg-gradient">
                    <div class="panel-wrapper collapse in" >
                        <div class="panel-body  pa-0">
                            @include('parts.profile_box')
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-xs-12" >
                <div class="panel panel-default card-view pa-0">
                    <div class="panel-wrapper collapse in">
                        <div  class="panel-body pb-0">
                            <div  class="tab-struct custom-tab-1">
                                <ul role="tablist" class="nav nav-tabs nav-tabs-responsive" id="myTabs_8">
                                    <li class="{{$edit_profile_status}}" role="presentation"><a  data-toggle="tab" id="settings_tab_8" role="tab" href="#settings" aria-expanded="false"><span>@lang('settings.profile.edit')</span></a></li>
                                    <li class="{{$promocode_status}}" role="presentation"><a  data-toggle="tab" id="earning_tab_8" role="tab" href="#promocode" aria-expanded="false"><span>@lang('settings.promo.name')</span></a></li>
                                    <li role="presentation"><a  data-toggle="tab" id="earning_tab_8" role="tab" href="#card" aria-expanded="false"><span>@lang('settings.card.name')</span></a></li>
                                </ul>
                                <div class="tab-content" id="myTabContent_8">

                                    <div  id="promocode" class="tab-pane fade {{$promocode_status}} in" role="tabpanel">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body pa-0">
                                                        <div class="col-sm-12 col-xs-12 nicescroll-bar" id="scrolled_form" style="width:100%;">
                                                                @include('forms.activate_promocode')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div  id="settings" class="tab-pane fade {{$edit_profile_status}} in" role="tabpanel">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                    <div class="panel-wrapper collapse in">
                                                        <div class="panel-body pa-0">
                                                            <div class="col-sm-12 col-xs-12" style="width: 100%;">
                                                                @include('forms.edit_profile')
                                                            </div>
                                                        </div>
                                                    </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div  id="card" class="tab-pane fade in" role="tabpanel">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body pa-0">
                                                        <div class="col-sm-12 col-xs-12 nicescroll-bar" id="scrolled_form" style="width: 100%;">
                                                            @include('forms.bank_card')
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Row -->
    </div>
@endsection

