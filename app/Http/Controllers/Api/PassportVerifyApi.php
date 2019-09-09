<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PasportVerifyRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PassportVerifyApi extends Controller
{
    //
    public function editPassportVerify(Request $request){
        $user_id = $request->user_id;
        $new_verify_status = $request->new_status;
        $new_comment = $request->comment;
        $new_manager_comment = $request->manager_comment;
        
        try{
            $passport_request = PasportVerifyRequest::where('user_id', $user_id)->firstOrFail();
        } catch(ModelNotFoundException $e){
            return json_encode(['result' => false, 'error' => 'Заявка не найдена']);
        }
        try{
            $passport_request->user_updated_id = Auth::user()->id;
            $passport_request->comment_to_user = $new_comment;
            $passport_request->comment_to_manager = $new_manager_comment;
            $passport_request->request_status = $new_verify_status;
            $passport_request->save();
        } catch (\Exception $e){
            return json_encode(['result' => false, 'error' => 'Нельзя изменить']);
        }
        return json_encode(['result' => true, 'error' => '']);
    }

    /**
     * Возвращает все активные или просто все заявки на верификацию паспорта
     *
     * @return json
     */
    public function getAllPassportVR(Request $request){
        $active = $request->active ?? null;
        if($active) {
            $passports = PasportVerifyRequest::where('request_status', PasportVerifyRequest::T_REQUEST_SENT)->get();
        } else {
            $passports = PasportVerifyRequest::all();
        }
        $data = [];
        foreach($passports as $passport) {
            $data[] = $passport->getSmallArray();
        }

        return json_encode(['success' => true,  'data' => $data]);
    }
}
