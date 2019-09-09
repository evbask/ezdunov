<?php

namespace App\Components;

use App\Components\Interfaces\PriceFormating as PriceInterface;

/**
 * работа с ценообразованием  аренд
 * 
 */
class PriceFormating implements PriceInterface
{
    protected $rent;
    public function __construct($rent)
    {
        $this->rent = $rent;
        //$type = $rent->type;
    }
}