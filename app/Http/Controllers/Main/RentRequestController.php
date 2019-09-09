<?php

namespace App\Http\Controllers\Main;

use App\RentRequest;
use App\RentRequestsTariffs;
use App\Rules\Dates\MinDeliveryTime;
use App\Rules\Dates\MinRentTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RentRequestController extends Controller
{
    public function add(Request $request){

        $validator =  Validator::make($request->all(),
            [
                'to_street' => 'required', 'to_house' => 'required', 'to_kv' => 'required',
                'from_street' => 'required', 'from_house' => 'required', 'from_kv' => 'required',
                'time_to' => ['required','date_format:d.m.Y H:i', new MinDeliveryTime()],
                'time_from' => ['required','date_format:d.m.Y H:i',new MinRentTime($request->time_to)]
            ]
        );

        if ($validator->fails()) {
            $msg = $validator->errors()->all();
            return json_encode(["status" => "fail", "msg" => $msg], JSON_UNESCAPED_UNICODE);
        }

        $rent = new RentRequest();
        $rent->build();

        if ($rent->add()) {
            return json_encode(["status"=>"success", "msg" => "Заявка отправлена!!!"],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(["status"=>"fail", "msg" => [$rent->getErrors()[0]]],JSON_UNESCAPED_UNICODE);
        }
    }
}
