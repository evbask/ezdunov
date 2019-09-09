<?php

namespace App\Http\Controllers\Main;
use App\Rent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class RentsController
{
    public function getMore(Request $request){
      $start_from = $request->startFrom;
      $per_page = config('rents.count_per_page');
      $rents = Rent::where('user_id', $request->user()->id)->orderBy('id','asc')->offset($start_from)->limit($per_page)->get();
      return $this->convert($rents);
    }

    /**
     * @param $rents
     * отдает аренды в удобном для фронта формате
     * @return array
     */
    public function convert($rents){
        $formatted_rents = array();
        foreach ($rents as $key => $rent){
            $formatted_rents[$key]["id"] = $rent["id"];
            $formatted_rents[$key]["time"] = $this->getDifference($rent["start_time"],$rent["end_time"])." ".Lang::get('rents.minutes');
            $formatted_rents[$key]["created_at"] = $rent["created_at"]->format('d-m-Y');
            $formatted_rents[$key]["balancePayment"] = $rent["payment"]["balancePayment"]." ".Lang::get('rents.r');
            $formatted_rents[$key]["bonusPayment"] = $rent["payment"]["bonusPayment"];
            $formatted_rents[$key]["resultPaymentAmount"] = $rent["payment"]["resultPaymentAmount"]." ".Lang::get('rents.r');
            $formatted_rents[$key]["status"] = Lang::get('rents.rent_statuses.'.$rent["status"]);
            $formatted_rents[$key]["class"] = $this->getClass($rent["status"]);
            $formatted_rents[$key]["price"] = $rent["price"]." ".implode(" ", [Lang::get('rents.r'), "/", Lang::get('rents.min')]);
            $formatted_rents[$key]["type"] =   Lang::get('rents.rent_types.'.$rent["type"]);
        }
        return $formatted_rents;
    }

    public function getOptions(Request $request){
        return [Rent::where('user_id', $request->user()->id)->count(), config('rents.count_per_page')];
    }

    public function getDifference($start_time, $end_time){
        $start_time = \Illuminate\Support\Carbon::parse($start_time);
        $end_time = \Illuminate\Support\Carbon::parse($end_time);
        return $end_time->diffInMinutes($start_time);
    }

    public function getClass($status){
        $class = null;
        switch ($status){
            case 1:  $class = 'primary'; break;
            case 2:  $class = 'default'; break;
            case 3:  $class = 'danger'; break;
        }
        return $class;
    }
}
