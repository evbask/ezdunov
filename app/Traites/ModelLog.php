<?php
/**
 */
namespace App\Traites;

trait ModelLog{

    use CallAtBoot;

    protected static $methods_to_boot = [
        
    ];

    /** массив хранящий измененные значений атрибутов, которые необходимо логировать */
    private $changeLogs = [];
    public static function boot()
    {
        parent::boot();
        
        self::updating(function($model) {
            $propertyLogs = $model->arrayPropertyLogs;

            foreach ($model->getDirty() as $key => $value) {
                if (in_array($key, $propertyLogs)) {
                    $model->changeLogs[] = [
                        'before'    => $model->getOriginal($key),
                        'after'     => $value,
                        'property'  => $key,
                    ];
                }
            }
        });

        self::updated(function($model) {
            foreach ($model->changeLogs as $log) {
                self::$LogClass::add($model, $log);
            }
            $model->changeLogs = [];
        });
    }
}