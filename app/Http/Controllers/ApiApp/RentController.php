<?php
namespace App\Http\Controllers\ApiApp;

use Auth;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Rent;
use App\User;
use App\Http\Controllers\ApiAppController; 
use App\Http\Controllers\Auth\LoginController;

/**
 * контроллер аренд
 * @todo подумать над стдартизированными отвветами по дефолту
 */
class RentController extends ApiAppController
{
    /**
     * создание новой аренды
     * @todo изначально вся логика валидации была в валидаторе
     */
    public function new(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicleId'     => ['required', 'integer'],
            'rentType'      => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $rent = new Rent();
        $rent->build();
        if ($rent->add()) {
            $this->answer['rent'] = $rent->id;
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => $rent->getErrors()[0], 'code' => 0]);
        }
    }

    /**
     * завершение аренды
     * @todo добавить проверки
     */
    public function close(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rentId'     => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        // $user = Auth::user();
        // $rent = $user->rent->where('status', '!=', Rent::S_END)->first();
        
        // if (!$rent) {
        //     return $this->sendAnswerError(['message' => 'У вас нет не завершенных аренд', 'code' => 0]);
        // }
        
        $rent = Rent::find($request->rentId);
        if (!$rent) {
            return $this->sendAnswerError(['message' => 'Аренда не найдена.', 'code' => 0]);
        }

        if ($rent->close()) {
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => $rent->getErrors()[0], 'code' => 0]);
        }
    }

    /**
     * история аренд
     */
    public function history()
    {
        $user = Auth::user();
        $rents = $user->rent;
        $rentsArr = [];
        foreach ($rents as $rent) {
            $rentsArr[] = $rent->getArrayInfoApp();
        }
        $this->answer['history'] = $rentsArr;
        return response()->json($this->answer);
    }

    /**
     * текущая активная аренда
     */
    public function active()
    {
        $user = Auth::user();
        $this->answer['rent'] = $user->rent->where('status', '!=', Rent::S_END)->first();
        return response()->json($this->answer);
    }

    /**
     * перевести бронирование в аренду
     */
    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rentId'     => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        // $user = Auth::user();
        // $rent = $user->rent->
        //             where('status', '!=', Rent::S_END)->
        //             where('type', Rent::T_RESERVATION)->first();
        $rent = Rent::find($request->rentId);
        if (!$rent) {
            return $this->sendAnswerError(['message' => 'Аренда не найдена.', 'code' => 0]);
        }

        if ($rent->start()) {
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => $rent->getErrors()[0], 'code' => 0]);
        }
    }
}
