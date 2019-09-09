<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResponseController extends Controller
{
    public function buildResponse($status,$msg){
        return json_encode(["status" =>$status, "msg" => $msg]);
    }

}
