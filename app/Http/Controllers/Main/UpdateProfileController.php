<?php

namespace App\Http\Controllers\Main;

use App\Components\ImageWebp;
use App\Components\Toolkit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\UpdateProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class UpdateProfileController extends Controller
{
    public function updateProfile(Request $request){
        $updater = new UpdateProfile($request);
        return $updater->update();
    }

    public function updateOnlyPhone(Request $request){
        $user = $request->user();
        $validator =  Validator::make($request->merge(['new_phone' => Toolkit::sanitizePhone($request->new_phone)])->all(),  ['new_phone' => ['required', 'string', 'min:10', 'unique:users,phone,'.$user->id]]);
        if ($validator->fails()) {
            $msg = $validator->errors()->all();
            return json_encode(['status' => 'fail', 'msg' => $msg]);
        }
        $user->phone = $request->new_phone;
        $user->save();
        return json_encode(['status' => 'success', 'msg'=>'Телефон успешно изменён']);
    }

    public function updateAvatar(Request $request){
        $validator =  Validator::make($request->all(),  ['avatar' => ['image','mimes:jpg,jpeg,png','max:2048']]);

        if ($validator->fails()) {
            $msg = $validator->errors()->all();
            return ["status" => "fail", "msg" => $msg];
        }

        $user = $request->user();
        $avatar = new ImageWebp;
        $avatar->build($request->avatar, config('folders.avatars'));
        $avatar->convert();

        if($avatar->isErrors()){
            return ["status" => "fail", "msg" => $avatar->getErrors()];
        }

        $newFileName = $avatar->getNames()[0];
        $old_avatar = $user->avatar;

        if($old_avatar != config('profile.default_avatar')){
            File::delete(config('folders.avatars').$old_avatar);
        }
        $user->avatar = $newFileName;
        $user->save();

        return ["status" => "success"];
    }

}
