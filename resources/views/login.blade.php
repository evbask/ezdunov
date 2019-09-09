<?php
$nolayers = true;
?>
@extends('common.page')
@section('body_class')
auth-page
@endsection
@section('content')
    @include('forms.login')
@endsection