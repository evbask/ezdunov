<?php

//use Symfony\Component\Routing\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * проверка емаил
 */
Route::get('/auth/verifyEmail/{id}/{token}', 'Main\VerifyEmail@Activation')->name('verifyEmail');


/**
 * URL для сброса пароля...
 */
//POST запрос для отправки email письма пользователю для сброса пароля
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//ссылка для сброса пароля (можно размещать в письме)
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
//страница с формой для сброса пароля
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//POST запрос для сброса старого и установки нового пароля
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
Route::get('/update', function(){
    echo "Заглушка после изменения пароля";
})->name('password.update.get');
/***************************/


/** 
 * PagesController routes 
 */
Route::get('/', 'Main\Pages@index')->middleware('user.logged');
Route::get('/register', 'Main\Pages@register');
Route::get('/payment', 'Main\Pages@payment')->middleware('auth');
Route::get('/passport_verify', 'Main\Pages@passport_verify')->middleware('auth');
Route::get('/login', 'Main\Pages@login')->name('login');
Route::get('/about', 'Main\Pages@about');
Route::get('/getvehs', 'Main\Pages@testVeh');


/**
 * Личный кабинет
 */

Route::get('/home', 'Main\Pages@home')->middleware('sms.verify')->middleware('passport.verify')->middleware('need.passport.correct');
Route::get('/rents', 'Main\Pages@rents')->middleware('sms.verify')->middleware('passport.verify')->middleware('need.passport.correct');
Route::get('/chat', 'Main\Pages@chat')->middleware('sms.verify')->middleware('passport.verify')->middleware('need.passport.correct');
Route::get('/edit_passport', 'Main\Pages@edit_passport')->middleware('sms.verify')->middleware('passport.verify')->middleware('check.passport.status');;
Route::get('/settings', 'Main\Pages@settings')->middleware('sms.verify')->middleware('passport.verify')->middleware('need.passport.correct');
Route::get('/getLKObject', 'Main\Vehicles@getLKObject');
Route::get('/get_messages', 'Main\ChatController@getMessages');
Route::get('/check_new', 'Main\ChatController@checkNew');
Route::get('/get_options', 'Main\RentsController@getOptions');
Route::get('/get_more', 'Main\RentsController@getMore');

Route::post('/update_passport', 'Main\PassportController@update');
Route::post('/settings/update_profile','Main\UpdateProfileController@updateProfile');
Route::post('/settings/update_avatar','Main\UpdateProfileController@updateAvatar');
Route::post('/settings/activate_promocode','Main\PromocodeController@activatePromocode');

Route::post('/changePhone','Main\UpdateProfileController@updateOnlyPhone');
Route::post('/add_message', 'Main\ChatController@addMessage');

Route::post('/send_rent_request','Main\RentRequestController@add');

/**************************/


Route::get('/pages/{pagename}', 'Main\Pages@page');

Route::get('/sms_verify', 'Main\Pages@sms_verify')->middleware('auth')->middleware('check.sms.verify');

/** 
 * Function routes 
 */
Route::get('/getbrowser', function(){
    print_r(session('browser'));
});
Route::get('/logout', function(){
    Auth::logout();
    return redirect('/');
});
Route::get('/contacts', function () {
    return view('about');
});
Route::get('/socket', function () {
    return view('welcome');
});
Route::get('/test_db/test2', function(){
    echo "hello";
});
Route::get('/phpinfo', function(){
    phpinfo();
})->middleware('auth');
/********************/


/** 
 * Ограничения по авторизации для получения изображений
 * @todo: Добавить обрабутку личных пользовательских путей и проверку авторизации
 */
Route::get('/secfile/{file_path}', function($path){
    if(Auth::check()){
        $file_path = base_path().'/secureImages/'.$path;
        $type = mime_content_type($file_path);
        return response(file_get_contents($file_path))->header('Content-Type', $type);
    }
    return response("not authorized", 403);
})->where('file_path', '.*');
/*********************************************************************************/

/**
 * Пути для оплаты онлайн
 */
Route::post('/payment/fullNotificationRfiBank', 'Payment\PaymentController@actionFullNotificationRfiBank');
Route::get('/payment/rfiSuccessPayment', 'Payment\PaymentController@actionRfiSuccessPayment');
Route::post('/payment/ValidateRfiLog', 'Payment\PaymentController@actionValidateRfiLog');
/**************************/

Route::post('/auth/pasport_verify', 'Main\PassportController@verify');
Route::get('/auth/send_verification_sms', 'Api\SmsApi@sendVerification');

Route::get('/sms_verify/check_sms_code', 'Main\SmsVerify@checkSmsCode');
Route::get('/{pagename}', 'Main\Pages@page');
Route::post('/auth/login', 'Main\Login@login');
Route::get('/register/new_user', 'Main\Register@newUser');
Route::get('/register/checkUser', 'Main\Register@checkUser');




