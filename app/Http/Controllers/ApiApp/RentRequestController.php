<?php

namespace App\Http\Controllers\ApiApp;

use Auth;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\RentRequest;
use App\RentRequestsTariffs;
use App\User;
use App\Http\Controllers\ApiAppController; 
use App\Http\Controllers\Auth\LoginController;
use App\Rules\Dates\MinDeliveryTime;
use App\Rules\Dates\MinRentTime;

/**
 * какой то бесполезный костыль, недошаринг
 */
class RentRequestController extends ApiAppController
{
    /**
     * отправить заявку на аренду самоката
     */
    public function new(Request $request)
    {
        $validator =  Validator::make($request->all(), [
                'to_street'     => 'required', 
                'to_house'      => 'required', 
                'to_kv'         => 'required',
                'from_street'   => 'required', 
                'from_house'    => 'required', 
                'from_kv'       => 'required',
                'time_to'       => ['required', 'date', new MinDeliveryTime()],
                'time_from'     => ['required', 'date', new MinRentTime($request->time_to)]
            ]
        );

        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $rent = new RentRequest();
        $rent->build();
        if ($rent->add()) {
            $this->answer['rent'] = $rent->id;
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => $rent->getErrors()[0], 'code' => 0]);
        }
    }

    public function getRentRequestTariff()
    {
        $tariff = RentRequestsTariffs::where('active', true)->first() ?? false;
        if ($tariff) {
            $this->answer['tariff'] = [
                'name'  => $tariff->name,
                'price' => $tariff->price
            ];
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => 'Произошла ошибка, попробуйте позже или обратитесь в службу поддержки.', 'code' => 0]);
        }
    }
}
