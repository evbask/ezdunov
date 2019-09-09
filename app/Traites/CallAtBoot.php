<?php

namespace App\Traites;

trait CallAtBoot{
    protected static $methods_to_boot = [];

    public static function boot()
    {
        parent::boot();
        foreach(self::$methods_to_boot as $method) {
            self::$$method();
        }
    }
}