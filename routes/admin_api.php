<?php
/*************************************************************************************\
|                                                                                     |
|                             Пути для работы с админкой                              |
|                                                                                     |
\*************************************************************************************/

Route::post('/get_csrf', 'CsrfApi@getCsrf');

/** Пути для работы с пользователями */
Route::post('/register', 'UserApi@register');
Route::post('/auth', 'UserApi@auth');
Route::post('/get_session_user','UserApi@getSessionUser');
Route::post('/get_all_user','UserApi@getAllUsers')->middleware('api.key');
Route::post('/get_user','UserApi@getUser')->middleware('api.key');
Route::post('/users/update_user', 'UserApi@updateUser');
Route::post('/users/getRoles', 'UserApi@getUserRoles')->middleware('api.key');

/** Пути для работы с транспортными средствами*/
Route::post('/vehicles/getAllJsonObject', 'VehiclesApi@getAllJsonObject')->middleware('api.key');
Route::post('/vehicles/getAllTable', 'VehiclesApi@getAllTable')->middleware('api.key');
Route::post('/vehicles/getCities', 'VehiclesApi@getCities')->middleware('api.key');
Route::post('/vehicles/getVehicleTypes', 'VehiclesApi@getVehicleTypes')->middleware('api.key');
Route::post('/vehicles/getVehicle', 'VehiclesApi@getVehicle')->middleware('api.key');
Route::post('/vehicles/addNewVehicle', 'VehiclesApi@addNewVehicle')->middleware('api.key');

/** Пути для работы со стат страницами */
Route::post('/pages/getAllPages', 'PagesApi@getAllPages')->middleware('api.key');
Route::post('/pages/getPage', 'PagesApi@getPage')->middleware('api.key');
Route::post('/pages/addNewPage', 'PagesApi@addNewPage')->middleware('api.key');
Route::post('/pages/updatePage', 'PagesApi@updatePage')->middleware('api.key');

/** Пути для работы с шаблонами */
Route::post('/templates/getAllTemplates', 'TemplatesApi@getAllTemplates')->middleware('api.key');
Route::post('/templates/getTemplate', 'TemplatesApi@getTemplate')->middleware('api.key');
Route::post('/templates/addNewTemplate', 'TemplatesApi@addNewTemplate')->middleware('api.key');

/** Пути для работы с настройками сайта */
Route::post('/site/getAllSettings', 'SiteApi@getSettings')->middleware('api.key');
Route::post('/site/setSettings', 'SiteApi@setSettings')->middleware('api.key');
Route::post('/site/addNewSetting', 'SiteApi@addNewSetting')->middleware('api.key');
Route::post('/site/getSettingTypes', 'SiteApi@getSettingsTypes')->middleware('api.key');
Route::post('/site/getLangValues', 'SiteApi@getLangValues')->middleware('api.key');
Route::post('/site/updateLangValue', 'SiteApi@updateLangValue')->middleware('api.key');
Route::post('/site/addNewLangVal', 'SiteApi@addNewLangVal')->middleware('api.key');

//Route::get('/site/checkForUsersWithoutPromocode','UserApi@checkForUsersWithoutPromocode');
/** Пути для работы с верификациями паспорта */
Route::post('/passport/getAllPassportVR', 'PassportVerifyApi@getAllPassportVR')->middleware('api.key');
Route::post('/passport/editVerifyRequest', 'PassportVerifyApi@editPassportVerify')->middleware('api.key');

/***************************************************************************************/
