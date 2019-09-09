<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Pages;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use function GuzzleHttp\json_encode;
/*
|--------------------------------------------------------------------------
| PagesApi
|--------------------------------------------------------------------------
|
| Класс хранит методы api для работы со статическими страницами
|
*/

class PagesApi extends Controller
{

    public function addNewPage(Request $request) {
        
        $data = [];
        $data['url'] = $request->url;
        $data['title'] = $request->title;
        $data['description'] = $request->description;
        $data['keywords'] = $request->keywords;
        $data['content'] = $request->content;

        
        Pages::create($data);
        return json_encode(['success' => true]);
    }

    public function getAllPages() {
        $pages = Pages::all();
        $responce = ['success' => true];
        $responce['data'] = [];
        foreach($pages as $page) {
            $responce['data'][] = $page->getSmallArray();
        }

        return json_encode($responce);
    }

    public function getPage(Request $request) {
        $page_id = $request->page_id;
        $data = [];

        $responce = ['success' => true];
        try{
            $page = Pages::findOrFail($page_id);
            $responce['data'] = $page->getBigArray();
        } catch(ModelNotFoundException $e){
            return json_encode(['success'=> false, 'error'=>'not found']);
        }
        
        return json_encode($responce);
    }

    public function updatePage(Request $request) {
        $page_id = $request->page_id;
        $data = [];
        //$data['url'] = $request->url;
        $data['title'] = $request->title;
        $data['description'] = $request->description;
        $data['keywords'] = $request->keywords;
        $data['content'] = $request->content;
        $page = Pages::find($page_id);

        if($page) {
            $page->title = $data['title'];
            $page->description = $data['description'];

            $page->keywords = $data['keywords'];
            $page->content = $data['content'];
            $page->save();

            return json_encode(["result" => true]);
        }
        return json_encode(["result" => false]);
    }
}