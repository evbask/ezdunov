<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User as User;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException; 


use App\Http\Controllers\Auth\LoginController;
use App\Components\Toolkit;

class Login extends Controller
{

    public function login(Request $request){
        /*$data = [];
        $data['name']     = $request->fio;
        $data['phone']    = str_replace(['+','-','_',' ','(',')','!'], '', $request->phone);
        $data['email']    = $request->email;
        $data['password'] = $request->password;
        $data['login']    = $data['phone'];
        $data['role']     = 'user';
        $data['settings'] = ['settings' => [], 'shortcuts' => []];
        $exists = true;
        try{
            $user = User::where('phone', $data['phone'])->orWhere('email', $data['email'])->firstOrFail();
        } catch(ModelNotFoundException $e){
            $exists = false;
        } 
        if(!$exists){
            $register_1 = new RegisterController;

            $result = $register_1->registerNewUser($data);
        }*/
        $login = new LoginController;
        $login->setLoginField(filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone');
        $request->merge([$login->username() => $request->email]);
        
        return $login->login($request);
    }
}