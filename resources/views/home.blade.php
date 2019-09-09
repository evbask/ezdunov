@extends('common.home')
@section('content')
        {{--<div id="map" style="width: 100%; height: 100%; position: relative;top: 99px; "></div>--}}
        <div class="lk-content">
                    <div class="row">
                            <div class="col-lg-10 col-xs-12">
                                    <div class="panel panel-default card-view">
                                            <div class="panel-heading">
                                                    <div class="pull-left">
                                                            <h6 class="panel-title txt-dark">ЗАЯВКА НА АРЕНДУ САМОКАТА</h6>
                                                    </div>
                                                    <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                            <div class="row">
                                                                    <div class="col-sm-12 col-xs-12">
                                                                            <div class="form-wrap">
                                                                                    <form id="rent_request_form" method="post" data-toggle="validator" enctype="multipart/form-data">
                                                                                            {{csrf_field()}}
                                                                                            <div class="form-body">
                                                                                                    <div class="row">
                                                                                                            <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                            <label class="control-label mb-10">Куда доставить?*</label>

                                                                                                                            <div class="input-group" id="address_to">
                                                                                                                                    <div class="input-group-addon"><i class="icon-location-pin"></i></div>
                                                                                                                                    <input type="text" name="to_street" id="to_street" placeholder="Улица" class="form-control" required>
                                                                                                                                    <input type="text" name="to_house" id="to_house" placeholder="Дом" class="form-control" required>
                                                                                                                                    <input type="text" name="to_kv" id="to_kv" placeholder="Кв." class="form-control" required>
                                                                                                                            </div>
                                                                                                                            <div class="help-block with-errors"></div>

                                                                                                                    </div>

                                                                                                            </div>
                                                                                                            <!--/span-->
                                                                                                            <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                            <label class="control-label mb-10">Ко скольки?*</label>
                                                                                                                            {{--<div class="input-group">--}}
                                                                                                                                    {{----}}
                                                                                                                                    {{--<input type="datetime-local" name="time_to" id="to_time" class="form-control" required>--}}
                                                                                                                            {{--</div>--}}
                                                                                                                            <div class="input-group">
                                                                                                                                    <div class="input-group-addon"><i class="icon-clock"></i></div>
                                                                                                                                    <input type="text" name="time_to" id="time_to" class="form-control" required>
                                                                                                                            </div>
                                                                                                                            <span class="help-block">Не менее чем через час</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                            <!--/span-->
                                                                                                    </div>
                                                                                                    <!-- /Row -->
                                                                                                    <div class="row">
                                                                                                            <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                            <label class="control-label mb-10">Откуда забрать?*</label>
                                                                                                                                    <div class="input-group" id="address_from">
                                                                                                                                            <div class="input-group-addon"><i class="icon-location-pin"></i></div>
                                                                                                                                            <input type="text" name="from_street" id="from_street" placeholder="Улица" class="form-control" required>
                                                                                                                                            <input type="text" name="from_house" id="from_house" placeholder="Дом" class="form-control" required>
                                                                                                                                            <input type="text" name="from_kv" id="from_kv" placeholder="Кв." class="form-control"  required>
                                                                                                                                    </div>
                                                                                                                                    <div class="help-block with-errors"></div>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                            <!--/span-->
                                                                                                            <div class="col-md-6">
                                                                                                                    <div class="form-group">
                                                                                                                            <label class="control-label mb-10">Во сколько?*</label>
                                                                                                                            <div class="input-group">
                                                                                                                            <div class="input-group-addon"><i class="icon-clock"></i></div>
                                                                                                                            <input type="text" name="time_from" id="time_from" class="form-control" required>
                                                                                                                            </div>
                                                                                                                            <span class="help-block">Минимально возможное время аренды - сутки</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                            <!--/span-->
                                                                                                    </div>
                                                                                                    <div class="row">
                                                                                                            <div class="col-md-12">
                                                                                                                    <div class="form-group">
                                                                                                                            <label class="control-label mb-10">Комментарий</label>
                                                                                                                            <div class="input-group">
                                                                                                                                <div class="input-group-addon"><i class="icon-bubble"></i></div>
                                                                                                                                <input type="text" id="comment" name="comment" class="form-control">
                                                                                                                            </div>
                                                                                                                            <div class="help-block with-errors"></div>
                                                                                                                    </div>

                                                                                                            </div>
                                                                                                    </div>

                                                                                                <!-- /Row -->
                                                                                                <div class="row">
                                                                                                    <div class="col-md-3">
                                                                                                        <div class="form-group">
                                                                                                            <div>
                                                                                                                <label class="control-label mb-10">Цена за сутки: <span id="price">{{$price}}</span> р.</label><br/>
                                                                                                                <label class="control-label mb-10">Выбрано суток: <span id="count">0</span></label><br/>
                                                                                                                <label class="control-label mb-20">Итого: <span id="sum">0</span> р.</label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <!--/span-->

                                                                                                            <div class="col-md-6">
                                                                                                                <div class="button_block">
                                                                                                                   <button type="submit" id="send_request" class="btn btn-danger">Отправить</button>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                    <!--/span-->
                                                                                                </div>

                                                                                            </div>

                                                                                    </form>


                                                                            </div>
                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>

                                    </div>
                            </div>

                    </div>
        </div>
        <script  src="{{asset('js/picker/moment-with-locales.min.js')}}" type="text/javascript"></script>
        <script  src="{{asset('js/picker/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>



        <script type="text/javascript">
                $(document).ready(function(){
                   // $('.jq-toast-wrap').width('500px');

                    $('#rent_request_form').validator({focus: false}).on('submit', function (e) {
                        if (e.isDefaultPrevented()) {
                            $.toast().reset('all');
                            $.toast({
                                heading: 'Ошибка!',
                                text: 'Заполните все поля!',
                                position: 'top-right',
                                loaderBg:'red',
                                icon: 'error',
                                hideAfter: 3500,
                                stack: 6
                            });
                        } else {
                            e.preventDefault();
                            let form_data = new FormData($('#rent_request_form')[0]);
                            $.ajax({
                                    url: "{{ url('/send_rent_request') }}",
                                    method: 'post',
                                    processData: false,
                                    contentType: false,
                                    data: form_data,
                                    headers: {

                                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

                                    },
                                    success: function(response){
                                        var parsedResponse = JSON.parse(response);
                                        if(parsedResponse.status=="success"){
                                            $('.jq-toast-wrap').css('width','250px');
                                            $.toast().reset('all');
                                            $.toast({
                                                heading: 'Успех!',
                                                text: parsedResponse.msg,
                                                position: 'top-right',
                                                loaderBg:'white',
                                                icon: 'success',
                                                hideAfter: 3500,
                                                stack: 6
                                            });
                                            $("#to_street,#to_house,#to_kv,#from_street,#from_house,#from_kv,#comment").val("");
                                        }else{
                                            $('.jq-toast-wrap').css('width','500px');
                                            var errors = '';
                                            parsedResponse.msg.forEach(function(error){
                                                errors += error + "<br/>";
                                            });
                                            $.toast().reset('all');
                                            $.toast({
                                                heading: 'Ошибка!',
                                                text: errors,
                                                position: 'top-right',
                                                loaderBg:'white',
                                                icon: 'error',
                                                hideAfter: 3500,
                                                stack: 6
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        $.toast().reset('all');
                                        $.toast({
                                            heading: 'Ошибка!',
                                            text: 'Что-то пошло не так!',
                                            position: 'top-right',
                                            loaderBg:'red',
                                            icon: 'error',
                                            hideAfter: 3500,
                                            stack: 6
                                        });
                                    }
                                });
                        }
                    });

                    var format = 'DD.MM.YYYY h:m';
                    var date = moment(new Date(),format).add(1,'hours');

                    $('#time_to, #time_from').datetimepicker({
                        locale: 'ru',
                        defaultDate: date,
                        minDate: date,
                    });

                    $('#time_from, #time_to').on('dp.change', function () {
                        updatePrice();
                    });

                    function updatePrice(){
                        var count = getDiff();
                        var price = $('#price').text();
                        $('#count').text(count);
                        $('#sum').text(count*price);
                    }

                    function getDiff(){
                        var time_to = moment($('#time_to').val(),format);
                        var time_from = moment($('#time_from').val(), format);
                        var result = time_from.diff(time_to,'days',true);
                        if(result >= 1){
                            return Math.ceil(result);
                        }else{
                            return 0;
                        }
                    }
                    

                    $('#to_street').blur(function () {
                        $('#from_street').focus();
                        $('#from_street').blur();
                    });
                    $('#to_house').blur(function () {
                        $('#from_house').focus();
                        $('#from_house').blur();
                    });
                    $('#to_kv').blur(function () {
                        $('#from_kv').focus();
                        $('#from_kv').blur();
                    });

                    $('#to_street').keyup(function() {
                        $('#from_street').val($(this).val());
                    });
                    $('#to_house').keyup(function() {
                        $('#from_house').val($(this).val());

                    });
                    $('#to_kv').keyup(function() {
                        $('#from_kv').val($(this).val());
                    });
                })
        </script>
@endsection
<!-- Yandex Api -->
{{--<script src="https://api-maps.yandex.ru/2.1/?apikey=<c7a81df1-3151-4c11-8e2c-fa64f11abbfb>&lang=ru_RU" type="text/javascript"></script>--}}
{{--<script type="text/javascript" src="/js/map/initVehiclesMap.js"></script>--}}
