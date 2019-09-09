<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::domain('{mobile}.ezdunov.ru')->group(function () {

});
/**
 * в относительной готовности
 */

/**
 * auth
 */
Route::post('/login', 'ApiApp\AuthenticationController@login');
Route::post('/logout', 'ApiApp\AuthenticationController@logout');
Route::post('/registry', 'ApiApp\AuthenticationController@registry');
Route::post('/passwordReset', 'ApiApp\AuthenticationController@passwordReset');
Route::post('/passwordSmsReset', 'ApiApp\AuthenticationController@passwordSmsReset');

/**
 * verify
 */
Route::post('/phoneToVerify', 'ApiApp\VerifyController@phoneToVerify');
Route::post('/phoneVerifyCode', 'ApiApp\VerifyController@phoneVerifyCode');
Route::post('/passportVerify', 'ApiApp\VerifyController@passportVerify');
Route::post('/emailVerify', 'ApiApp\VerifyController@emailVerify');

/**
 * User
 * @todo добавить необходимую информацию
 */
Route::post('/getUserInfo', 'ApiApp\UserController@getUserInfo');
Route::post('/sendGps', 'ApiApp\UserController@sendGps');
Route::post('/setGcmToken', 'ApiApp\UserController@setGcmToken');
Route::post('/getHistoryBonus', 'ApiApp\UserController@getHistoryBonus');
Route::post('/getHistoryPush', 'ApiApp\UserController@getHistoryPush');
Route::post('/setAvatar', 'ApiApp\UserController@setAvatar');

/**
 * payment
 * @todo нудно ли? зависит от степени готовности проекта
 */
Route::post('/payment', 'ApiApp\PaymentController@rfi');

/**
 * Аренда и все что с ней связано
 */
Route::post('/rentNew', 'ApiApp\RentController@new');
Route::post('/rentClose', 'ApiApp\RentController@close');
Route::post('/rentHistory', 'ApiApp\RentController@history');
Route::post('/rentActive', 'ApiApp\RentController@active');
Route::post('/rentStart', 'ApiApp\RentController@start');

/**
 * Костыльная аренда и все что с ней связано
 */
Route::post('/rentRequestNew', 'ApiApp\RentRequestController@new');
Route::post('/getRentRequestTariff', 'ApiApp\RentRequestController@getRentRequestTariff');

/**
 * чат
 */
Route::post('/getMessages', 'ApiApp\SupportController@getMessages');
Route::post('/sendMessage', 'ApiApp\SupportController@sendMessage');

/**
 * требуется разработка
 */
Route::post('/getAvailableDevices', 'ApiApp\DeviceController@getAvailableDevices');
Route::post('/getScooterInfo', 'ApiApp\DeviceController@getScooterInfo');

/**
 * файловый манагер
 */
Route::post('/getAvatar', 'ApiApp\FileController@getAvatar');
Route::get('/getPhotoDeviceProblem/{id}', 'ApiApp\FileController@getPhotoDeviceProblem');
Route::get('/getPassportVerifyPhoto/{id}', 'ApiApp\FileController@getPassportVerifyPhoto');
Route::get('/getChatFile/{id}', 'ApiApp\FileController@getChatFile');




/**
 * @todo для тестов, в проде удалить
 */
Route::post('/test', 'ApiApp\TestController@test');
Route::post('/balance', 'ApiApp\TestController@balance');
Route::post('/bonus', 'ApiApp\TestController@bonus');
Route::post('/setAttribute', 'ApiApp\UserController@setAttribute');
Route::post('/sendPush', 'ApiApp\TestController@sendPush');