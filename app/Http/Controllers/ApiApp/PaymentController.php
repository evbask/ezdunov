<?php

namespace App\Http\Controllers\ApiApp;

use Auth;
use App\Http\Controllers\ApiAppController;

use Illuminate\Http\Response;

class PaymentController extends ApiAppController
{
    public function rfi()
    {
        $this->answer['pageHtml'] = view('ApiApp.paymentRfi', ['user' => Auth::user()])->render();
        return response()->json($this->answer);
    }
}
