function check_required(element){
    if($(element).val().length < 1) {
        $(element).trigger( "required.not.set" );
        $(element).trigger("not.valid");
        $(element)[0].isvalid = false;
        console.log('required and not set');
    }
}
function check_length(element, min, max){
    if(min > 0){
        if($(element).val().length < min){
            $(element).trigger( "length.not.valid" );
            $(element).trigger( "length.less.than.min" );
            $(element).trigger("not.valid");
            $(element)[0].isvalid = false;
            console.log('less than min');
        }
    }
    if(max > 0 && max > min){
        if($(element).val().length > max){
            $(element).trigger( "length.not.valid" );
            $(element).trigger( "length.more.than.max" );
            $(element).trigger("not.valid");
            $(element)[0].isvalid = false;

            console.log('more than max');
        }
    }
}

function check_same(first, second){
    if($(first).val() != $(second).val()){
        $(first).trigger( "value.not.same");
        $(first).trigger("not.valid");
        $(first)[0].isvalid = false;

        console.log('not same as');
    }
}

function check_regexp(element, re){
    let str = $(element).val();
    let found = str.match(re);
    
    if(found === null){
        $(element).trigger( "regexp.not.match");
        $(element).trigger("not.valid");
        $(element)[0].isvalid = false;

        console.log('not match regexp');
    }
}

function check_func(element, func){
    if(!func(element)){
        $(element).trigger("not.valid");
        $(element)[0].isvalid = false;
    }
}

function check_checked(element, need){
    if(element.checked != need){
        $(element).trigger("not.valid");
        $(element)[0].isvalid = false;
    }
}
function check_val(element){
    
    //console.log($(element));
    /** Проверим hodden */
    $(element)[0].isvalid = true;
    if($(element).attr('type') == 'hidden') {
        return true;
    }
    
    /** Проверим required */
    if($(element).attr('required') !== undefined){
        check_required(element);
    }
    if( !$(element)[0].isvalid){
        $(element)[0].isvalid = true;
        return false;
    }
    /** проверим length */
    let minlength = 0, maxlength = 0;

    if($(element).data('length.min') !== undefined){
        minlength = parseInt($(element).data('length.min'));
    }

    if($(element).data('length.max') !== undefined){
        maxlength = parseInt($(element).data('length.max'));
    }
    check_length(element, minlength, maxlength);

    if( !$(element)[0].isvalid){
        $(element)[0].isvalid = true;
        return false;
    }
    /** проверим same as */
    if($(element).data('same.as') !== undefined){
        let second_id = '#'+$(element).data('same.as');
        
        check_same(element, $(second_id));
        if( !$(element)[0].isvalid){
            $(element)[0].isvalid = true;
            return false;
        }
    }

    /** Проверим regexp */
    if($(element).data('regexp.mask') !== undefined) {
        let re = $(element).data('regexp.mask');
        
        check_regexp(element, re);
        if( !$(element)[0].isvalid){
            $(element)[0].isvalid = true;
            return false;
        }
    }
    
    /** Проверим пользовательскую функцию */
    if($(element).data('validation.func') !== undefined) {
        let func = $(element).data('validation.func');
        
        check_func(element, func);
        if( !$(element)[0].isvalid){
            $(element)[0].isvalid = true;
            return false;
        }
    }
    
    if($(element).data('checked') !== undefined) {
        let need = $(element).data('checked');
        
        check_checked(element, need);
        if( !$(element)[0].isvalid){
            $(element)[0].isvalid = true;
            return false;
        }
    }
    $(element).trigger('input.valid');
    return true;
}

$(document).ready(() => {
    $('body').on('change', 'form.need-validation', function(e){
        //console.log("Clicked");
        //e.preventDefault();
        let form = $(this);//.parents('form')[0]
        
        let inputs = $(form).find('input');
        let result = [];
        let form_valid = true;
        for(i = 0; i<inputs.length; i++) {
            if ( !check_val(inputs[i]) ){
                if(form_valid) {
                    $(form)[0].isValid = false;
                    $(form).trigger('form.not.valid');
                    form_valid = false;
                    console.log('not valid');
                    console.log(inputs[i]);
                }
            }
        }
        if(form_valid){
            $(form)[0].isValid = true;
            $(form).trigger('form.valid');
            //console.log('valid');
        }
    });
});
