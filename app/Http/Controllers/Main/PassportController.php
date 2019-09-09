<?php

namespace App\Http\Controllers\Main;

use App\Components\ImageWebp;
use App\Components\Toolkit;
use App\PasportVerifyPhoto;
use App\PasportVerifyRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traites\Validators;
use App\Components\VerifyPassport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Exception;

class PassportController extends ResponseController
{
    use Validators;
    public function verify(Request $request){
        $validator = $this->validatePassport($request);
        if ($validator->fails()) {
            $msg = $validator->errors()->all();
            return $this->buildResponse('fail', $msg);
        }
        $verify = new VerifyPassport($request);
        if ($verify->add()) {
            return $this->buildResponse('success', Lang::get('passport_verify.success'));
        } else {
            return $this->buildResponse('fail', $verify->getErrors());
        }
    }

    public function update(Request $request){
        $user = $request->user();

        $passport = $user->passport;

        if(!$passport){
            return $this->buildResponse('fail', ['У вас нет заявки.']);
        }

        $validator = $this->validateUpdatedPassport($request,$passport->id);

        if($validator->fails()){
            $msg =  $validator->errors()->all();
            return $this->buildResponse('fail', $msg);
        }

        $request->merge(['name' => $passport->user_fio]);

        $verify = new VerifyPassport($request);

        if ($verify->add()) {//если добавление новых фоток прошло удачно, то удалить старые


            try{
                if(isset($request['replaced_id'])){
                    $verify->deleteOldPhotos(json_decode($request['replaced_id']));
                }
            }catch(Exception $e){
                $this->buildResponse('fail', $e->getMessage());
            }


            return $this->buildResponse('success', Lang::get('passport_verify.success'));

        } else {
            return $this->buildResponse('fail', $verify->getErrors());
        }

    }

}
