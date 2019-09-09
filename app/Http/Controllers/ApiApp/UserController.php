<?php

namespace App\Http\Controllers\ApiApp;

use Auth;
use Validator;

use App\LogsBonus;
use App\LogsGeolog;
use App\User;
use App\LogsPush;
use App\Http\Controllers\ApiAppController;
use App\Components\ImageWebp;
use App\Components\Toolkit;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends ApiAppController
{
    /**
     * получить информацию о пользователее
     */
    public function getUserInfo()
    {
        $this->answer['userInfo'] = Auth::user()->getArrayInfo();
        return response()->json($this->answer);
    }

    /**
     * получить от пользователя Gps
     */
    public function sendGps(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gps' => ['required', 'array'],
            'gps.lat' => ['required', 'numeric'],
            'gps.lng' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }
        LogsGeolog::create([
            'user_id'   =>  $request->user()->id,
            'lat'       =>  $request->gps['lat'],
            'lng'       =>  $request->gps['lng'],
        ]);
        return response()->json($this->answer);
    }

    /**
     * установить gcm токен
     */
    public function setGcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gcm' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }
        
        $user = $request->user();
        $user->gcm_token = $request->gcm;
        $user->save();

        return response()->json($this->answer);
    }

    /**
     * история бонусов
     */
    public function getHistoryBonus()
    {
        $this->answer['history'] = LogsBonus::historyForApp(Auth::user());
        return response()->json($this->answer);
    }

    /**
     * @todo для тестов в проде убрать
     */
    public function setAttribute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attr' => ['required', 'string'],
            'value' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $access = [
            'sms_verified',
            'recurrent_pay_status',
            'passport_verified',
            'email',
            'phone'
        ];

        $user = Auth::user();
        $attr = $request->attr;
        $value = $request->value;
        
        if (in_array($attr, $access)) {
            $user->$attr = $value;
            $user->save();
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => 'Данный атрибут запрещен', 'code' => 0]);
        }
    }

    /**
     * загрузка аватара
     */
    public function setAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar'    => ['image', 'mimes:jpg,jpeg,png']
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }
        $user = Auth::user();
        $path = config('folders.avatars');

        $image = new ImageWebp();
        $image->build($request->avatar, $path);
        $image->convert();
        if ($image->isErrors()) {
            return $this->sendAnswerError(['message' => $image->getErrors()[0], 'code' => 0]);
        } else {
            $pathOldPhoto = $path . $user->avatar;
            if (File::exists($pathOldPhoto)) {
                File::delete($pathOldPhoto);
            }
            $user->avatar = $image->getNames()[0];
            $user->save();
            return response()->json($this->answer);
        }
    }

    /**
     * история пушей
     */
    public function getHistoryPush()
    {
        $this->answer['history'] = LogsPush::historyForApp(Auth::user());
        return response()->json($this->answer);
    }
}