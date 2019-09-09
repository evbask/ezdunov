<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\User;
use \App\SrvVehicle as Vehicle;
use \App\KnownImeis;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\SrvVehicleTypes;
use App\SrvCities;

/*
|--------------------------------------------------------------------------
| VehiclesApi
|--------------------------------------------------------------------------
|
| Класс хранит методы api для работы со средствами передвижения из админки
|
*/

class VehiclesApi extends Controller
{
    private $VEHICLE_SERVER;

    /**
     * Установим сервер устройств
     */
    public function __construct(){
        $this->VEHICLE_SERVER = config('vehicle.VEHICLE_SERVER');
    }

    /**
     * Собираем url на сервер устройств
     *
     * @param [type] $url
     * @return void
     */
    private function buildUrl($url){
        return $this->VEHICLE_SERVER.$url;
    }

    /**
     * Совершаем запрос на сервер устройств
     *
     * @param [type] $url
     * @return void
     */
    private function makeRequest($url){
        return file_get_contents($this->buildUrl($url));
    }

    public function getAllJsonObject(Request $request){
        return $this->makeRequest('getAllJsonObject');
    }
    public function getAllTable(Request $request){
        return $this->makeRequest('getAllTable');
    }
    public function addNewVehicle(Request $request) {
        $imei = $request->imei;
        
        $city_id = $request->city_id;
        $type = $request->type;
        $vehicle  = Vehicle::create(['imei' => $imei, 'city_id' => $city_id, 'type' => $type]);
        //return  $this->makeRequest('addNewVehicle/'.$imei .'/'.$type.'/'.$city_id);
        return json_encode(['success' => true]);
    }
    public function getCities(Request $request){
        return $this->makeRequest('getCities');
    }
    public function getVehicleTypes(Request $request){
        return $this->makeRequest('getVehicleTypes');
    }

    public function getVehicle(Request $request){
        $vehicle_id = $request->vehicle_id;
        $vehicle = null;
        try{
            $vehicle = Vehicle::findOrFail($vehicle_id);
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return 'not found';
        }

        $data = [];
        $data['type'] = SrvVehicleTypes::find($vehicle->type)->name;
        $data['city'] = SrvCities::find($vehicle->city_id)->name;
        $data['id']   = $vehicle->id;
        $data['status'] = $vehicle->getTypeName();
        return json_encode(['success' => true, 'data' => $data]);
        //return $this->makeRequest("getVehicleInfoForAdmin/$vehicle_id");
    }
}