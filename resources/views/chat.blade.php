@extends('common.home')
@section('content')

    <div class="lk-content">

        <!-- Responsive Table -->
        <div class="col-lg-9 col-xs-12">

                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        {{--<div class="chat-cmplt-wrap chat-for-widgets-1">--}}

                        {{--<div class="recent-chat-box-wrap" style="">--}}
                            <div class="recent-chat-wrap" style="border: 5px solid rgba(255, 255, 255, 0.05);width: 990px;">
                                <div class="panel-heading ma-0 pt-15">
                                    <div class="goto-back" id="go_bottom_div">
                                       <span class="txt-danger" id="go_bottom"  style="font-size: 16px;text-decoration: underline; visibility: hidden; cursor: pointer;">
                                            @lang('chat.new')
                                       </span>
                                        <span class="inline-block txt-light" style="text-transform: none;">@lang('chat.title')</span>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body pa-0">
                                        <div class="chat-content">
                                            <ul class="chatapp-chat-nicescroll-bar pt-20" id="msgContainer">

                                            </ul>
                                        </div>
                                        <form id="msg_form" method="post" action="#" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <div class="input-group">
                                                <input type="text" autocomplete="off" id="input_msg_send_chatapp" name="send_msg" class="input-msg-send form-control" placeholder="@lang('chat.placeholder')" style="placeholder-transform: none">

                                                <div class="input-group-btn attachment" style="margin-right: 4%">
                                                    <div class="fileupload btn  btn-default"><i class=" icon-paper-clip" style="color:grey;"></i>
                                                        <input type="file" id="chat_files" class="upload" name="files[]" multiple>
                                                    </div>
                                                </div>
                                                <div class="input-group-btn attachment" style="margin-right: 1%;">
                                                    <div class="fileupload btn  btn-default" id="send_msg_button">
                                                        <i class=" icon-paper-plane" id="send_msg_icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        {{--</div>--}}
                    {{--</div>--}}
                    </div>
                </div>
            <input type="hidden" value="{{config('chat.update_interval')}}" style="display: none" id="interval">
        </div>
        <!-- /Responsive Table -->
    </div>
    <script  src="{{asset('js/grandin/scroll_operations.js')}}" type="text/javascript"></script>
    <script  src="{{asset('js/grandin/toolkit.js')}}" type="text/javascript"></script>
    <script  src="{{asset('js/grandin/message.js')}}" type="text/javascript"></script>
    <script  src="{{asset('js/localizator.js')}}" type="text/javascript"></script>
    <script  src="{{asset('js/grandin/chat.js')}}" type="text/javascript"></script>

@endsection
