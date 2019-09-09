<?php
namespace App\Http\Controllers\ApiApp;

use Request;
use Validator;

use App\SrvVehicle;
use App\Components\QueryVehicles;
use App\Http\Controllers\ApiAppController;

/**
 * контроллер отвечает за получении информации об устройствах
 */
class DeviceController extends ApiAppController
{
    /** 
     * получение информации об устройствах для вывода на карты
     */
    public function getAvailableDevices()
    {
        return QueryVehicles::send('getAvailableJsonobject');
    }

    /**
     * получение информации о самокате
     * @todo вынести в метод vehiclag
     */
    public function getScooterInfo()
    {
        $validator = Validator::make(Request::all(), [
            'vehicleId' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }
        
        $vehicle = SrvVehicle::find(Request::input('vehicleId'));
        if (!$vehicle ?? false) {
            return $this->sendAnswerError(['message' => 'Устройство не найдено.', 'code' => 0]);
        }
        
        $this->answer['device'] = $vehicle->infoForApp();
        return response()->json($this->answer);
    }
}
