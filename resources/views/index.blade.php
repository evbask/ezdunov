@extends('common.page')

@section('content')
    @include('parts.main_slider')
    @include('parts.section', ['h1_span' => __('index.about_us_span') , 'h1_main' => __('index.about_us_main'), 'h4_text' => __('index.about_us_h4')])
@endsection


