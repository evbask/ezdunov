<?php
/**
 * Created by PhpStorm.
 * User: Евгений
 * Date: 04.02.2019
 * Time: 17:26
 */

namespace App\Components;


use App\Promocode;
use App\PromocodesLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivatePromocode
{
    private $request;
    private $user;
    const SETTINGS = 'settings';

    public function __construct(Request $request){
        $this->request = $request;
        $this->user = $request->user();
    }

    public function activate(){
        $validation = $this->validatePromocode();

        if($validation['status'] == 'fail'){
            return redirect(self::SETTINGS)->with(['promo_status'=>'fail', 'error' => $validation['msg']]);
        }

        try{
            $promocode = $this->findPromocode();
        }catch(ModelNotFoundException $e){
            return redirect(self::SETTINGS)->with(['promo_status'=>'fail', 'error' => ['Указанный промокод не найден.']]);
        }

        if(!$this->checkPromocode($promocode->id)){
            return redirect(self::SETTINGS)->with(['promo_status'=>'fail', 'error' => ['Указанный промокод уже активирован.']]);
        }

        PromocodesLog::create(["promocode_id" => $promocode->id, "rent_id" => null, 'user_id' => $this->user->id]);

        return redirect(self::SETTINGS)->with(['promo_status'=>'success', 'msg' => 'Промокод активирован.']);
    }

    private function validatePromocode(){
        $validator =  Validator::make(
            ["promocode" => $this->request->promocode],
            $this->getPromocodeRules()
        );
        if ($validator->fails()) {
            $msg = $validator->errors()->all();
            return ['status' => 'fail', 'msg' => $msg];
        }
        return ['status' => 'success'];
    }

    private function getPromocodeRules(){
       return ['promocode' => ['required', 'string','min:6', 'max:6']];
    }

    private function findPromocode(){
        return Promocode::where('promo_code',$this->request->promocode)->where('id','!=',$this->user->promocode_id)->firstOrFail();
    }

     // TODO типы промокодов, будет ли многоразовый промокод?
    private function checkPromocode($id){
        return !PromocodesLog::where('user_id',$this->user->id)->where('promocode_id', $id)->exists();
    }

}