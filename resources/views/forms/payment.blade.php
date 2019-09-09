<div class="container-fluid user-auth">
    <div></div>
    <div class="col-xs-12">
            <div class="form-container">
                <div>
                    <!-- Main Heading Starts -->
                <div class="text-center top-text">
                    <h1>@lang('payment.title_main')</h1>
                    <p>{{ __('payment.title_small') }}</p>
                </div>
                <!-- Main Heading Ends -->
                    <!-- Form Starts -->
                    <form id="payment_form" class="custom-form need-validation" action="none">
                        <!-- Input Field Starts -->
                        {{csrf_field()}}
                        <div class="form-group">
                            <input class="form-control" name="sum" id="sum" placeholder="{{ __('payment.summ_placeholder') }}" data-not_valid_text="Пожалуйста, заполните это поле" type="text" required>
                        </div>

                        <div class="form-group">
                            <button id="payment_submit_btn" class="custom-button" disabled=true type="submit">{{ __('payment.submit') }}</button>
                        </div>
                        <!-- Submit Form Button Ends -->
                    </form>
                    <!-- Form Ends -->
                </div>
            </div>
            <!-- Copyright Text Starts -->
            <p class="text-center copyright-text">{{ __('index.copyrights') }}</p>
            <!-- Copyright Text Ends -->
        </div>
    </div>
    <!--Форма РФИ-Банка-->
    <form id="rfi_bank" method="POST" target="bank_message"  class="application"  accept-charset="UTF-8" action="<?= App\Components\RfiBank::BASE_URL . App\Components\RfiBank::PAYMENT_URL ?>">
        {{csrf_field()}}
        <input type="hidden" id="rfi_cost" name="cost" value="" />
        <input type="hidden" id="rfi_name" name="name" value="Пополнение баланса в системе Ezdunov.ru"/>
        <input type="hidden" name="email" value="<?= Auth::user()->email ?>" />
        <input type="hidden" name="phone_number" value="<?= Auth::user()->phone ?>"/>
        <input type="hidden" name="user_id" value="<?= Auth::user()->id ?>"/>
        <input type="hidden" name="service_id" value="<?= App\Components\RfiBank::SERVICE_ID ?>" />
        <input type="hidden" id="rfi_order_id" name="order_id" value="0" />
        <input type="hidden" id="rfi_check" name="check" value="" />
        <input type="hidden" name="version" value="2.0" />
        <input type="hidden" name="payment_type" value="<?= App\Components\RfiBank::PAYMENT_TYPE ?>" />
        <input type="hidden" id="card" name="card" value="" />
        <input type="hidden" id="card_binding_id" name="card_binding_id" value="" />
        <input type="hidden" id="card_on_rec_pays" name="card_on_rec_pays" value="" />
        <input type="hidden" id="recurrent_type" name="recurrent_type" value="first" />
        <input type="hidden" id="recurrent_comment" name="recurrent_comment" value="first rec pay" />
        <input type="hidden" id="recurrent_url" name="recurrent_url" value="http://example.com/rules" />
        <input type="hidden" id="recurrent_period" name="recurrent_period" value="byrequest" />
    </form>
</div>

<script>

$(document).ready(() => {

    $("#payment_form").on('not.valid', 'input', function(e){
        if($(this).data('not_valid_text') !== undefined){
            showTooltip(this, $(this).data('not_valid_text'), {tag: 'span', class: 'text-danger badge top bg-light'});
        }
    });

    $("#payment_form").on('input.valid', 'input', function(e){
        hideTooltip(this);
    });

    $("#payment_form").on('form.not.valid', function(e){
        $("#payment_submit_btn").prop('disabled', true);
    });

    $("#payment_form").on('form.valid', function(e){
        $("#payment_submit_btn").prop('disabled', false);
    });

    $("#payment_submit_btn").on('click', function(e){
        e.preventDefault();
        if(!$("#payment_form")[0].isValid){
            return;
        }
        $("#rfi_bank").submit();
        /*$.ajax({
            url: '/payment/checkUser',
            dataType: "json",
            data:{
                email: $('#email').val(),
                phone: $('#phone').val()
            },
            success: function(data){
                let result = data.result;
                if(result){
                    $("#payment_form").submit();
                } else {
                    message("{{ __('payment.used_data_message_title') }}", "{{ __('payment.used_data_message_content') }}");
                }
            }
        });*/
    });

    $("#rfi_bank, #rfi_bank_1").submit(function(e){
        var bankForm = $(this);
        e.preventDefault();
        $("#rfi_cost").val($("#sum").val());
		var card = $(".card").val();
		var cardBind = $("[value=" + card + "]").attr("data-card-bind");
		var onRec = $("[value=" + card + "]").attr("data-on-rec");
		$("#card").val(card);
		$("#card_binding_id").val(cardBind);
        $("#card_on_rec_pays").val(onRec);


		if ($("#rfi_cost").val() != parseInt($("#rfi_cost").val()) || $("#rfi_cost").val() <= 0) {
			alert('Введите корректную сумму(положительное целое число)');
			return;
		}

        var data = $(bankForm).serialize();
        if ($("#checkOnlineActivation").val() == 1) {
            data += "&checkOnlineActivation=1";
        }
        if ($("#bindingCardToService").val() == 1) {
            data += "&bindingCardToService=1";
        }
		$.ajax({
		    type: "POST",
		    async: false,
            url: "/payment/ValidateRfiLog",
            data: data,
            success: function(resp) {
                var response = JSON.parse(resp);
		        if (response.success) {
		        	if (response.card_on_rec_pays) {
		        		$("#recurrent_type").remove();
		        		$("#recurrent_comment").remove();
		        		$("#recurrent_url").remove();
		        		$("#recurrent_period").remove();
		        	}
                    $(bankForm).find('input[name="_token"]').remove();
		        	$("#card").remove();
		        	$("#card_on_rec_pays").remove();
		        	$("#card_binding_id").val(response.card_binding_id);
		            $("#rfi_name").val(response.name);
		            $("#rfi_order_id").val(response.order_id);
                    console.log(response.check);
                    console.log($("#rfi_name").val());
                    $("#rfi_check").val(response.check);

                    // Если юзер сам привязывает карту к сервису, отправляем другую форму
                    if ($("#bindingCardToService").val() == 1) {


                        if (response.card_on_rec_pays) {
                            $("#recurrent_type_1").remove();
                            $("#recurrent_comment_1").remove();
                            $("#recurrent_url_1").remove();
                            $("#recurrent_period_1").remove();
                        }
                        $("#card_1").remove();
                        $("#card_on_rec_pays_1").remove();
                        $("#card_binding_id_1").val(response.card_binding_id);
                        $("#rfi_name_1").val(response.name);
                        $("#rfi_order_id_1").val(response.order_id);

                        $("#rfi_check_1").val(response.check);
                    }

                    $(bankForm).off("submit");
                    console.log($(bankForm).serialize());
                    message('', '<iframe style="width: 100%; height: 100%" name="bank_message"></iframe>', { height: 'calc(100% - 50px)' })
                    $(bankForm).submit();
                } else {
		            alert('Возникла ошибка при попытке пополнения баланса');
                }
            },
            error: function(e){
                alert('Возникла ошибка при попытке пополнения баланса');
            }
        });

		return false;
	});

    $("#recPayAdd").click(function() {
        var bankForm = $('#recPayRfi');
        var data = $(bankForm).serialize();
        $.ajax({
            type: "POST",
            async: false,
            url: "/ajax/user/validateRfiLog",
            context: bankForm,
            data: data,
            success: function(resp) {
                var response = JSON.parse(resp);
                if (response.success) {
                $(this).find("[name='card_binding_id']").val('');
                $(this).find("[name='check']").val(response.check);
                $(this).find("[name='name']").val(response.name);
                $(this).find("[name='order_id']").val(response.order_id);
                $(this).find("[name='checkOnlineActivation']").remove();
                $(this).submit();
                } else {
                    alert('Возникла ошибка при попытке пополнения баланса');
                }
            },
            error: function() {
                alert('Возникла ошибка при попытке пополнения баланса');
            }
        });
    });
});
</script>
