<div class="container-fluid user-auth">
    <div class="hidden-xs col-sm-4 col-md-4 col-lg-4">
        <!-- Logo Starts -->
        <!-- Logo Ends -->
        <div id="carousel-testimonials" class="carousel slide carousel-fade" data-ride="carousel">
            <!-- Indicators Starts -->
            <ol class="carousel-indicators">
                <li data-target="#carousel-testimonials" data-slide-to="0" class="active"></li>
                <li data-target="#carousel-testimonials" data-slide-to="1"></li>
                <li data-target="#carousel-testimonials" data-slide-to="2"></li>
            </ol>
            <!-- Indicators Ends -->
            <!-- Carousel Inner Starts -->
            <div class="carousel-inner">
                <!-- Carousel Item Starts -->
                <div class="item active item-1" style="background-image: url(/img/backgrounds/passport_1.jpg)">
                    <div>
                        <blockquote>
                            <p>Amira's Team Was Great To Work With And Interpreted Our Needs Perfectly.</p>
                            <footer><span>Lucy Smith</span>, England</footer>
                        </blockquote>
                    </div>
                </div>
                <!-- Carousel Item Ends -->
                <!-- Carousel Item Starts -->
                <div class="item item-2" style="background-image: url(/img/backgrounds/passport_2.jpg)">
                    <div>
                        <blockquote>
                            <p>The Team Is Endlessly Helpful, Flexible And Always Quick To Respond, Thanks Amira!</p>
                            <footer><span>Rawia Chniti</span>, Russia</footer>
                        </blockquote>
                    </div>
                </div>
                <!-- Carousel Item Ends -->
                <!-- Carousel Item Starts -->
                <div class="item item-3" style="background-image: url(/img/backgrounds/passport_3.jpg)">
                    <div>
                        <blockquote>
                            <p>We Are So Appreciative Of Their Creative Efforts, And Love Our New Site!, millions of thanks Amira!</p>
                            <footer><span>Mario Verratti</span>, Spain</footer>
                        </blockquote>
                    </div>
                </div>
                <!-- Carousel Item Ends -->
            </div>
            <!-- Carousel Inner Ends -->
        </div>
        <!-- Slider Ends -->
    </div>
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <div></div>
        <div class="form-container">
            <div>
                <!-- Main Heading Starts -->
                <div class="text-center top-text">
                    <h1>@lang('passport_verify.title_main')</h1>
                    <p>{{ __('passport_verify.title_small') }}</p>
                </div>
                <!-- Main Heading Ends -->
                <!-- Form Starts -->
                <form id="pasport_verify_form" method="post" enctype="multipart/form-data" action="/auth/pasport_verify" class="custom-form">
                    <!-- Input Field Starts -->
                    {{csrf_field()}}
                    <div class="form-group">
                        <input class="form-control" name="name" id="name" data-not_valid_text="Пожалуйста, укажите ваши ФИО" placeholder="{{ __('passport_verify.name') }}" value="{{Auth::user()->name}}" type="text" required>
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div>
                    <!-- Input Field Ends -->
                    <!-- Input Field Starts -->
                    <div class="form-group">
                        <input class="form-control" name="passport_number" id="passport_number" data-not_valid_text="Пожалуйста, введите Серию и номер паспорта" placeholder="{{ __('passport_verify.passport_number') }}" type="tel"  required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="date" name="date_of_birth" id="date_of_birth" data-not_valid_text="Пожалуйста, введите дату рождения" placeholder="{{ __('passport_verify.date_of_birth') }}" required>
                    </div>
                    <div class="form-group">
                        <div id="validationResult"></div>
                            <label for="passport_photo" class="custom-file-field">
                                {{ __('passport_verify.upload_photos') }}
                                <input id="passport_photo" class="form-control" name="passport_photo[]" multiple=true placeholder="Первая и вторая страницы" type="file" />
                            </label>
                        <div id="status" ></div>
                    </div>
                    <!-- Input Field Ends -->

                    <!-- Submit Form Button Starts -->
                    <div class="form-group">
                        <button class="custom-button passport_verify" id="sendPassportData" type="submit">{{ __('passport_verify.passport_verify') }}</button>
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

<script>
//$(":input").inputmask();

$(document).ready( function() {

  $('#sendPassportData').click(function(e){
        e.preventDefault();
        var form_data = new FormData($('#pasport_verify_form')[0]);
      if(validateForm()){
          $.ajax({
              url: "{{ url('/auth/pasport_verify') }}",
              method: 'post',
              processData: false,
              contentType: false,
              data: form_data,
              headers: {

                  'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')

              },
              success: function(response){
                  var parsedResponse = JSON.parse(response);

                  if(parsedResponse.status == "success"){
                      $("#status").removeClass("alert alert-danger");
                      $("#status").addClass("alert alert-success");
                      $("#status").html('<strong>Заявка успешно создана.</strong>');
                  }else{
                      $("#status").addClass("alert alert-danger");
                      var errors = '<strong>';
                      parsedResponse.msg.forEach(function(error){
                          errors += error + "<br/>";
                      });
                      errors += '</strong>';
                      $("#status").html(''+errors);
                  }
              },
              error: function(xhr) {
                  $("#status").addClass("alert alert-danger");
                  $("#status").html('Что-то пошло не так.');
              }

          });
      }
    });

    function validateForm() {
         result = validateName() && validatePhotos() && validatePassportNumber();
        return result;
    }

    function validatePassportNumber(){
        passport_number = $("#passport_number").val();
        result = /^\d{4}\s\d{6}$/.test(passport_number);
        if(!result){
            $("#status").addClass("alert alert-danger");
            $("#status").html('<strong>Некорректный формат серии или номера паспорта</strong>');
        }
        return result;
    }

    function validateName() {
        result = $("#name").val().length > 0;
        if(!result){
            $("#status").addClass("alert alert-danger");
            $("#status").html('<strong>Поле ФИО не должно быть пустым.</strong>');
        }
        return result;
    }

    function validatePhotos(){
        photos = $("#passport_photo")[0].files;
        var fileExtensions = ['jpeg', 'jpg', 'png'];
        var photosCount = photos.length;
        var extValid = true;
        $.each(photos, function(index, value){
            if ($.inArray(value.name.split('.').pop().toLowerCase(), fileExtensions) == -1) {
                extValid = false;
            }
        });
        if(!extValid) {
            $("#status").addClass("alert alert-danger");
            $("#validationResult").text("");
            $("#status").html('<strong>Допустимы только изображения форматов jpeg и png</strong>');
            return false;
        }
        if(photosCount<3){
            $("#status").addClass("alert alert-danger");
            $("#validationResult").text("Выбрано "+ photosCount + " фото");
            $("#status").html('<strong>Требуется как минимум 3 фотографии<strong>');
            return false;
        }else{
            $("#status").removeClass("alert alert-danger");
            $("#status").html('');
            $("#validationResult").text("Выбрано "+ photosCount + " фото");
            return true;
        }

    }

    $("#passport_photo").change(function(e) {
      validatePhotos(e.target.files);
    });

    $("#pasport_verify_form").on('not.valid', 'input', function(e){
        showTooltip(this, $(this).data('not_valid_text'), {tag: 'span', class: 'text-danger badge top bg-light'});
        alert("Not valid");
    });

    $("#pasport_verify_form").on('click', 'input', function(e){
        hideAllTooltips();
    });

    $("#pasport_verify_form").on('form.valid', function(e){
        //e.preventDefault();
        $('#pasport_verify_form').submit();
        /*$.ajax({
            url: '/register/checkUser',
            dataType: "json",
            data:{
                email: $('#email').val(),
                phone: $('#phone').val()
            },
            success: function(data){
                let result = data.result;
                if(result){
                    $("#register_form").submit();
                } else {
                    alert('Пользователь с такими данными уже зарегистрирован в системе');
                }
            }
        });*/
    });
});
</script>