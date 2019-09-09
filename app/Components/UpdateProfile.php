<?php
/**
 * Created by PhpStorm.
 * User: Евгений
 * Date: 28.01.2019
 * Time: 18:45
 */

namespace App\Components;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Rules\ComparePasswords;
use Exception;
use Illuminate\Support\Facades\File;
class UpdateProfile
{
    private $request;
    private $user;
    const SETTINGS = 'settings#result';
    const DEFAULT_MSG = 'Изменения успешно сохранены.';

    public function __construct(Request $request){
        $request->merge(['phone' => Toolkit::sanitizePhone($request->phone),'email' => Toolkit::sanitizeEmail($request->email)]);
        $this->request = $request;
        $this->user = $request->user();
    }

    public function update(){
        $validation = $this->validateProfile();

        if($validation['status'] == 'fail'){
            return redirect(self::SETTINGS)->with(['status'=>'fail', 'error' => $validation['msg']]);
        }
        $user = $this->user;
        $msg = self::DEFAULT_MSG;

        $change_email = $this->changeUserEmail();
        if($change_email){
            $msg .= ' Подтвердите email, перейдя по ссылке, отправленной Вам на почту.';
        } else if($change_email === false){
            return redirect(self::SETTINGS)->with(['status'=>'fail', 'error' => ['Не удалось отправить email со ссылкой подтверждения.']]);
        }

        $this->changeUserName();
        $this->changeUserPassword();
        $this->changeUserPhone();

        $user->save();

        if(!$user->sms_verified){
            return redirect('/sms_verify');
        }
        return redirect(self::SETTINGS)->with(['status' => 'success', 'msg' => $msg]);

    }

    private function changeUserName(){
        $new_name = $this->request->name;
        $user = $this->user;
        $user->setName($new_name);
    }

    private function changeUserPassword(){
        $old_password = $this->request->current_password;
        $new_password = $this->request->new_password;
        $repeated_password = $this->request->repeated_password;
        if(!empty($old_password) && !empty($new_password) && !empty($repeated_password)){
            $user = $this->user;
            $new_password = trim($new_password);
            $user->password = Hash::make($new_password);
        }
    }

    private function changeUserPhone(){
        $new_phone = $this->request->phone;
        $user = $this->user;

        if($user->phone != $new_phone){
            $user->phone = $new_phone;
            $user->sms_verified = false;
        }
    }

    private function changeUserEmail(){
        $user = $this->user;
//        if (is_null($user->remember_token)) {
//            return false;
//        }
        $new_email = $this->request->email;
        $old_email = $user->email;

        if($old_email != $new_email){
            $user->email = $new_email;
            $user->save();
            try{
                $this->sendEmail();
            }catch(Exception $ex){
                   $user->email = $old_email;
                   $user->save();
                   return false;
            }
            return true;

        }
        return null;

    }

    private function sendEmail(){
        $user = $this->user;
        Mail::to($user)->send(new VerifyEmail($user));
    }

    private function validateProfile(){
        $validator =  Validator::make($this->request->all(), $this->getProfileRules());
        if ($validator->fails()) {
            $msg = $validator->errors()->all();
            return ['status' => 'fail', 'msg' => $msg];
        }
        return ['status' => 'success'];
    }

    private function getProfileRules(){
        return array(
            'name' => ['required', 'string', 'max:300'],
            'phone' => ['required', 'string', 'min:10', 'unique:users,phone,'.$this->user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'current_password' => ['nullable', new ComparePasswords($this->user->password),'required_with:new_password'],
            'new_password' => ['nullable','min:6','required_with:current_password'],
            'repeated_password' => ['same:new_password']
        );
    }

}