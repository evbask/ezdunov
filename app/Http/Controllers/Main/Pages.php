<?php

namespace App\Http\Controllers\Main;

use App\RentRequestsTariffs;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Pages as Page;
use \App\VehicleSrv as Vehicle;
use \App\Rent;
use \App\Chat;
use \App\Promocode;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Flynsarmy\DbBladeCompiler\Facades\DbView;

class Pages extends Controller
{
    /**
     * Показ страницы по её url
     *
     * @param [type] $name
     * @param bool $from_db : Нужно ли делать рендер из поля content модели Page
     * @param array $data_to_add : Дополнительные параметры, которые нужно передать в шаблон
     * @return void
     */

    public function getCurrentUser(){
        return Auth::user();
    }

    public function page($name, $from_db = true, $data_to_add = []){

        try {
            $page = Page::where('url', $name)->firstOrFail();
        } catch(ModelNotFoundException $e){
            if(!$from_db){
                try {
                    $page = Page::where('url', 'index')->firstOrFail();
                } catch(ModelNotFoundException $e){
                    unset ($page);
                }
            }
        }

        if (isset($page)) {
            $data = $data_to_add + [
                'user' => $this->getCurrentUser(),
                'page_title' => $page->title,
                'page_description' => $page->description,
                'page_keywords' => $page->keywords,
                'page_content' => $page->content
            ];
            
            if ($from_db) {
                unset($data['page_content']);
                try{
                    return DbView::make($page)->with($data)->render();
                } catch(\Exception $e){
                    abort(404);
                }
            }

            try {
                return view($name, $data);
            } catch(\Exception $e){
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function testPage(){
        $page = new Page();
        
    }
    public function index()
    {
        return $this->page('index', false);
    }
    
    public function login()
    {
        return $this->page('login', false);
    }
    
    public function payment()
    {
        return $this->page('payment', false);
    }

    public function passport_verify()
    {
        return $this->page('passport_verify', false);
    }

    public function sms_verify()
    {
        return $this->page('sms_verify', false);
    }

    public function home()
    {
        $price = RentRequestsTariffs::getTariff();
        return $this->page('home', false, ["grandin"=>"true", "price" => $price]);
    }
    
    public function register()
    {
        return $this->page('register', false, ["attr" => "position: static"]);
    }
    public function about()
    {
        return $this->page('about', false);
    }
    public function rents()
    {
        return $this->page('rents', false,["grandin"=>"true"]);
    }

    public function chat()
    {
        return $this->page('chat', false,["grandin"=>"true"]);
    }

    public function edit_passport(){
        $user_id = $this->getCurrentUser()->id;
        return $this->page('edit_passport',false,["grandin" => "true", "editable_request" => $this->getCurrentUser()->passport->getBigArray()]);
    }

    public function settings(){
        $promocode = $this->getCurrentUser()->promocode;
        return $this->page('settings', false,["promocode"=>$promocode,"grandin"=>"true"]);
    }

    public function testVeh(){
        $vehicles = Vehicle::all();
        print_r($vehicles);
    }
//     public function rfi(){
//         return view('ApiApp.paymentRfi', ['user' => Auth::user()])->render();
//     }
}
