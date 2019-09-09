<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CsrfApi extends Controller
{
    //
    public function getCsrf(Request $request){
        echo $request->api_key;
    }
}
