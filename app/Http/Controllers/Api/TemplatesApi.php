<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Templates;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use function GuzzleHttp\json_encode;
/*
|--------------------------------------------------------------------------
| TemplatesApi
|--------------------------------------------------------------------------
|
| Класс хранит методы api для работы c шаблонами
|
*/

class TemplatesApi extends Controller
{

    public function addNewTemplate(Request $request) {
        
        $data = [];
        
        $data['name'] = $request->name;
        $data['content'] = $request->content;

        
        Templates::create($data);
        return json_encode(['success' => true]);
    }

    public function getAllTemplates() {
        $templates = Templates::all();
        $responce = ['success' => true];
        $responce['data'] = [];
        foreach($templates as $template) {
            $responce['data'][] = $template->getBigArray();
        }

        return json_encode($responce);
    }

    public function getTemplate(Request $request) {
        $template_id = $request->template_id;
        $data = [];

        $responce = ['success' => true];
        try{
            $template = Templates::findOrFail($template_id);
            $responce['data'] = $template->getBigArray();
        } catch(ModelNotFoundException $e){
            return json_encode(['success'=> false, 'error'=>'not found']);
        }
        
        return json_encode($responce);
    }
}