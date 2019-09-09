<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\PasportVerifyRequest;
use App\PasportVerifyPhoto;
use App\Http\Controllers\Main\Register;
use Illuminate\Support\Facades\Auth;
use function GuzzleHttp\json_encode;
use App\Components\Toolkit;
use App\SrvCities;
/*
|--------------------------------------------------------------------------
| UserApi
|--------------------------------------------------------------------------
|
| Класс хранит методы api для работы с пользователями из админки
|
*/

class UserApi extends Controller
{

    /**
     * Метод производит регистрацию нового пользователя
     * 
     */
    public function register(Request $request) {

        /** Стандартные настройки интерфейса */
        $default_settings = [
            'layout' => [
                'style' => 'layout1',
                'config' => [
                    'scroll' => 'content',
                    'navbar' => [
                        'display'  => true,
                        'folded'   => true,
                        'position' => 'left'
                    ],
                    'toolbar' => [
                        'display'  => true,
                        'style'    => 'fixed',
                        'position' => 'below'
                    ],
                    'footer' => [
                        'display'  => true,
                        'style'    => 'fixed',
                        'position' => 'below'
                    ],
                    'mode'   => 'fullwidth'
                ]
            ],
            'customScrollbars' => true,
            'theme'            => [
                'main'   => 'defaultDark',
                'navbar' => 'defaultDark',
                'toolbar'=> 'defaultDark',
                'footer' => 'defaultDark'
            ]
        ];

        /** Стандартные ссылки быстрого доступа */
        $default_shortcuts = [
            'calendar',
            'mail',
            'contacts'
        ];

        $register_1 = new RegisterController;

        /** Регистрация нового пользователя */
        $result = $register_1->registerNewUser([
            'role' => 'user',
            'login' => $request->login,
            'name' => $request->displayName,
            'email' => $request->email,
            'password' => $request->password,
            'settings' => ['settings' => $default_settings, 'shortcuts' => $default_shortcuts],
        ]);
        
        if ($result['status']) {
            $user = $result['result'];
        } else {
            if ($result['result']) {
                return json_encode(['data' => [ 'error' => $result['result']->errors()->all()]]);
            } else {
                return json_encode(['data' => [ 'error' => 'Bad news']]);
            }
        }

        /** Собираем  */
        $responce = [];
        $responce['role'] = $user->role;
        $responce['data'] = [
            'displayName' => $user->name,
            'photoURL'    => 'assets/images/avatars/profile.jpg',
            'email'       => $user->email,
            'settings'    => $user->settings['settings'],
            'shortcuts'    => $user->settings['shortcuts'],
            
        ];
        
        return json_encode($responce);
    }

    /**
     * Проходим авторизацию
     */
    public function auth(Request $request) {
        try{
            $user = User::where('login', $request->email)->firstOrFail();
            $request->merge(['email' => $user->email]);
        } catch (ModelNotFoundException $e) {
        }
        
        $login = new LoginController;
        return $login->login($request);
    }

    /**
     * Запрашиваем юзера из существующей сессии
     */
    public function getSessionUser(Request $request){
        $user =  $request->user();
        return $user->getUserApiJson();
    }

    /**
     * Запрашиваем всех юзеров (возвращаем 10 первых)
     */
    public function getAllUsers(Request $request) {
        if($request->user()->checkGroup('staff')) {
            $users = User::orderBy('name', 'desc')->get();
            $data = [];
            foreach ($users as $user) {
                $data[] = $user->getSmallArray();
            }
            return json_encode([ 'data' => $data]);
        }

        return '{error: "bad user group"}';
    }

    /**
     * Обновляем поле у существующего юзера
     */
    public function updateUser(Request $request) {
        $id = $request->user_id;
        $field = $request->field;
        $new_value = $request->new_value;
        
        $responce = ['error' => '', 'result' => false];

        /** Пробуем найти пользователя по id */
        try{
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $responce['error'] = 'user_not_found';
            return json_encode($responce);
        }

        /** Обновляем поле юзера */
        if($field == 'phone'){
            try{
                $user->$field = Toolkit::sanitizePhone($new_value);
                $user->save();
            } catch(\Exception $e){
                $responce['error'] = "Невозможно изменить";
                return json_encode($responce);
            }
        } else if($field == 'passport_fio'){
            $passport = $user->passport;
            if($passport){
                $passport->user_fio = $new_value;
                $passport->save();
            } else {
                $responce['error'] = "Нет заявки на верификацию паспорта";
                return json_encode($responce);
            }
        } else if ($field == "role" && !User::isAdmin()){
            $responce['error'] = "Не позволено";
            return json_encode($responce);
        } 
        else {
            try{
                $user->$field = $new_value;
                $user->save();
            } catch(\Exception $e){
                $responce['error'] = "Невозможно изменить";
                return json_encode($responce);
            }
        }
        $responce['result'] = true;

        return json_encode($responce);
    }
    public function getUser(Request $request) {
        $id = $request->user_id;
        $responce = ['error' => '', 'data'=>''];        
        
        /** Пробуем найти пользователя по id */
        try{
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $responce['error'] = 'user_not_found';
            return json_encode($responce);
        }
        $data = $user->getBigArray();
        $data['city_id'] = "".$data['city_id'];
        $data['roles'] = User::$roleNames;
        $cities = SrvCities::all();
        $data['cities'] = [];
        foreach($cities as $city){
            $data['cities'][$city->id] = $city->name; 
        }

        $responce['data'] = $data;
        return json_encode($responce);
        
    }

    

    /**
     * Возвращает активную заявку на верификацию паспорта по id пользователя Или id заявки
     *
     * @param Request $request
     * @return void
     */
    public function getUserPassportVR(Request $request) {
        $user_id =  $request->user_id ?? null;
        $request_id = $request->request_id ?? null;

        if($user_id){
            $passport = PasportVerifyRequest::where('request_status', PasportVerifyRequest::T_REQUEST_SENT)->where('user_id', $user_id)->first();
        } else if( $request_id) {
            $passport = PasportVerifyRequest::find($request_id);
        }
        $item = [];
        $item['fio'] = $passport->user_fio;
        $item['date_of_birth']   = $passport->date_of_birth;
        $item['passport_number'] = $passport->passport_number;
        $item['created'] = $passport->created_at;
        $item['updated'] = $passport->updated_at;
        $item['photos']  = [];

        $passport_photos = PasportVerifyPhoto::where('request_id', $passport->id)->get();
        
        foreach($passport_photos as $photo) {
            $item['photos'][] = ['url' => $photo->img_url];
        } 
    }

    public function checkForUsersWithoutPromocode(Request $request){
        if(!(Auth::user()->checkGroup('admin'))){
            return 10;
        }
        $users = User::whereNull('promocode_id')->get();
        foreach($users as $user){
            $user->promocode_id = User::newPromocode();
            $user->save();
        }

        return count($users);
    }

    public function getUserRoles(Request $request){
        $roles = User::$roleNames;

        return json_encode(['roles' => $roles]);
    }
}
