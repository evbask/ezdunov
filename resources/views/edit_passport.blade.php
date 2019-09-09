@extends('common.home')
@section('content')

    <div class="lk-content">

        <!-- Responsive Table -->
        <div class="col-lg-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-light"> @lang('passport_verify.title_edit') </h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                            @include('forms/edit_passport')
                    </div>
                </div>
            </div>
        </div>
        <!-- /Responsive Table -->
    </div>


@endsection
