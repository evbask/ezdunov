<?php

namespace App\Http\Controllers\Main;

use App\Components\Toolkit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User as User;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use App\PasportVerifyRequest;
use App\Promocode;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Carbon\Carbon;

class Register extends Controller
{
    public function checkUser(Request $request){
        $request->merge(['phone' => Toolkit::sanitizePhone($request->phone), 'email' => Toolkit::sanitizeEmail($request->email)]);
        $email = $request->email;
        $phone = $request->phone;
        $exists = true;
        try{
            $user = User::where('phone', $phone)->orWhere('email', $email)->firstOrFail();
        } catch(ModelNotFoundException $e){
            $exists = false;
        }
        $response = ['result' => !$exists];
        return json_encode($response);
    }
    //
    public function newUser(Request $request){
        $data = [];
        $data['name']     = $request->fio;
        $data['phone']    = $request->phone;
        $data['email']    = $request->email;
        $data['password'] = $request->password;
        $data['login']    = $data['phone'];
        $data['role']     = 'user';
        $data['settings'] = ['settings' => [], 'shortcuts' => []];
        $data['promocode_id'] = User::newPromocode();
        $exists = true;
        try{
            $user = User::where('phone', $data['phone'])->orWhere('email', $data['email'])->firstOrFail();
        } catch(ModelNotFoundException $e){
            $exists = false;
        } 
        if(!$exists){
            $register_1 = new RegisterController;
            $result = $register_1->registerNewUser($data);
            $user = $result['result'];
        }
        $login = new LoginController;
        return $login->login($request);
    }
}
