<?php
$nolayers = true;
?>
@extends('common.page')
@section('body_class')
auth-page sms_verify
@endsection
@section('content')
    @include('forms.sms_verify')
@endsection