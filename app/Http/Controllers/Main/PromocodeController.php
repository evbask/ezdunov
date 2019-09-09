<?php

namespace App\Http\Controllers\Main;

use App\Components\ActivatePromocode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PromocodeController extends Controller
{
    public function activatePromocode(Request $request){
        $activator = new ActivatePromocode($request);
        return $activator->activate();
    }
}
