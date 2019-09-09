@extends('common.home')
@section('content')

    <div class="lk-content">
        <!-- Responsive Table -->
        <div class="col-lg-11 col-xs-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-light"> @lang('rents.title') </h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                            <p class="text-muted">@lang('rents.message')</p>
                            <div class="table-wrap mt-40">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                        <tr>
                                            <th>№</th>
                                            <th>@lang('rents.time')</th>
                                            <th style="padding-left: 3%">@lang('rents.date')</th>
                                            <th>@lang('rents.card')</th>
                                            <th>@lang('rents.bonuses')</th>
                                            <th>@lang('rents.cost')</th>
                                            <th>@lang('rents.status')</th>
                                            <th>@lang('rents.tariff')</th>
                                            <th>@lang('rents.type')</th>
                                        </tr>
                                        </thead>
                                        <tbody id="rent_table"></tbody>
                                    </table>
                                        <div class="form-actions mt-10">
                                            <button id="get_more" style="display: none" class="btn btn-primary mr-10 mb-30">@lang('rents.show_more')</button>
                                        </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /Responsive Table -->
    </div>
    <script  src="{{asset('js/lazy_loading.js')}}" type="text/javascript"></script>
@endsection
