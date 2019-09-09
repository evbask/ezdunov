function localizator(){
    let translations = [];
    if(get_cookie('locale')=='ru'){
        translations['message'] = 'Изменения не сохранены. Повторите попытку. ';
        translations['smth'] = 'Что-то пошло не так.';
        translations['delivered'] = 'Доставлено';
        translations['viewed'] = 'Прочитано';
        translations['enter_msg'] = 'Введите сообщение!';
        translations['error'] = 'Ошибка!'
    }else{
        translations['message'] = 'Changes haven\'t been saved. Try again. ';
        translations['smth'] = 'Something went wrong.';
        translations['delivered'] = 'Delivered';
        translations['viewed'] = 'Viewed';
        translations['enter_msg'] = 'Enter your message!';
        translations['error'] = 'Error!';
    }
    return translations;
}

function get_cookie (cookie_name) {
    let results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
    if (results)
        return (unescape ( results[2] ));
    else
        return null;
}