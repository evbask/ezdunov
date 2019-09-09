<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    protected $login_field = 'email';
    
    protected $good_login_fields = [
        'email',
        'phone'
    ];
    
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function username()
    {
        return $this->login_field;
    }

    public function setLoginField($field){
        if(in_array($field, $this->good_login_fields))
            $this->login_field = $field;
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user){
        $responce = [];
        $responce['role'] = $user->role;
        $responce['data'] = [
            'displayName' => $user->name,
            'login'       => $user->login,
            'photoURL'    => 'assets/images/avatars/profile.jpg',
            'email'       => $user->email,
            'settings'    => $user->settings['settings'],
            'shortcuts'   => $user->settings['shortcuts'],
        ];

        if ($request->from_admin ?? false) {
            return json_encode($responce);
        }
    }
}
