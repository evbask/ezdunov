<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Vehicles extends Controller
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

    public function getLKObject(Request $request){
        return $this->makeRequest('getLKObject');
    }
}
