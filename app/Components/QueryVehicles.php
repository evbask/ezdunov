<?php
namespace App\Components;
/**
 * работа с транспортными средствами
 * @todo добавить проверку прав доступа
 */
class QueryVehicles
{
    const METHODS = [
        'getAllJsonObject',
        'getAvailableJsonobject',
        'getAllTable',
        'addNewVehicle',
        'getCities',
        'getVehicleTypes'
    ];

    /**
     * послать запрос, в ответ придет json
     * @param string $operation функция на сервере
     * @param array $params массив параметров
     * @return string Json
     */
    public static function send(string $operation, array $params = []){
        if (!in_array($operation, static::METHODS)) {
            throw new \Exception('Доступ запрещен.', 0); 
        }
        $partUrl = null;
        $partUrl = implode('/', $params);
        $operation .= '/' . $partUrl;
        return file_get_contents(config('vehicle.VEHICLE_SERVER') . $operation);
    }
}
