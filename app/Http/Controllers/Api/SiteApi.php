<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Settings;
use Illuminate\Support\Facades\Auth;
use App\User;
use function GuzzleHttp\json_encode;
/**
 * Класс для работы с настройками системы и ещё может чем-нибудь
 */
class SiteApi extends Controller
{
    /**
     * Получаем все настройки
     *
     * @param Request $request
     * @return json
     */
    public function getSettings(Request $request){
        $settings = Settings::all();

        $types = Settings::getTypes();
        foreach($settings as $key => $setting){
            $settings[$key]->the_type = $types[$setting->type];
            $settings[$key]->user_name = User::find($setting->user_id)->name;
        }
        $data = ['settings' => $settings];

        return json_encode($data);
    }

    /**
     * Добавляем новую настройку
     *
     * @param Request $request
     * @return json
     */
    public function addNewSetting(Request $request){
        $have_setting = Settings::get($request->name);

        if($have_setting ) {
            return json_encode(['result' => 'bad',  'error' => 'already have this setting']);
        }

        if(!(Settings::checkType($request->type))){
            return json_encode(['result' => 'bad', 'error' => 'bad type']);
        }

        Settings::create([
            'name' => $request->name,  
            'value' => $request->value,
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::user()->id,
            'type' => $request->type,
        ]);
        $data = ['result' => 'Ok'];

        return json_encode($data);
    }

    /**
     * Устанавливаем новое значение определённой настройки, либо всех
     *
     * @param Request $request
     * @return json
     */
    public function setSettings(Request $request)
    {
        $settings = $request->settings ?? [];

        if(!empty($settings)) {
            foreach($settings as $setting) {
                $name = $setting['name'];
                $new_val = $setting['val'];
                Settings::setSettingValue($name, $new_val);
            }
            return json_encode(['result' => 'Ok', 'success' => true]);
        }
        return json_encode([ 'result' => 'bad', 'error' => 'empty']);
    }

    public function getSettingsTypes(Request $request){
        $types = Settings::getTypes();

        $data = [];
        foreach($types as $key => $type){
            $data[] = ['id' => $key, 'name' => $type];
        }

        return json_encode(['types' => $data]);
    }

    /**
     * Возвращает массив языковых переменных в json
     *
     * @param Request $request
     * @return void
     */
    public function getLangValues(Request $request){
        $folder = base_path('resources/lang');
        $files = scandir($folder);

        $json_files = array_filter($files,function($v){
            $ext = substr($v, -5);

            return $ext && $ext == '.json';
        });

        $lang_values = [];
        $langs = [];

        foreach($json_files as $lang_file){
            $json_content = file_get_contents($folder.DIRECTORY_SEPARATOR.$lang_file);
            $lang_array = json_decode($json_content,true);
            $lang = substr($lang_file, 0, -5);
            $langs[] = $lang;

            foreach($lang_array as $key => $lang_value){
                if(!isset($lang_values[$key])){
                    $lang_values[$key]=[];
                }
                $lang_values[$key][$lang] = $lang_value;
            }
        }

        $lang_json_array = [];

        foreach($lang_values as $key => $value){
            $lang_json_array[] = ['value_name' => $key] + $value;
        }

        return json_encode(['langs' => $langs, 'data' => $lang_json_array]);
    }

    /**
     * Обновляем значение существующей языковой переменной
     *
     * @param Request $request
     * @return json
     */
    public function updateLangValue(Request $request){
        $responce = ['result' => true, 'error' => ''];
        
        $name = $request->name ?? null;
        $lang = $request->lang ?? null;
        $value = $request->value ?? null;

        if($lang == null || $name == null || $value == null){
            $responce = ['result' => false, 'error' => 'Недостаточно данных'];
        } else {
            $name = strval($name);
            $lang = strval($lang);
            $value = strval($value);

            $folder = base_path('resources/lang');
            $json_file = $folder.DIRECTORY_SEPARATOR.$lang.".json";

            if(file_exists($json_file)) {
                $json_content = file_get_contents($json_file);
                $lang_array = json_decode($json_content,true);

                if(isset($lang_array[$name])){
                    $lang_array[$name] = $value;

                    file_put_contents($json_file, json_encode($lang_array));
                } else {
                    $responce = ['result' => false, 'error' => 'Нет такой переменной'];
                }
            } else {
                $responce = ['result' => false, 'error' => 'Такого файла нет'];
            }
        }

        return json_encode($responce);
    }

    /**
     * Добавляем новую языковую переменную во все файлы
     *
     * @param Request $request
     * @return void
     */
    public function addNewLangVal(Request $request){
        $responce = ['result' => true, 'error' => ''];

        $folder = base_path('resources/lang');
        $files = scandir($folder);

        $langs = [];

        $name = $request['name'] ?? null;
        $values = $request['values'] ?? null;
        if($name == null ||  empty($values) || !is_array($values) || empty($values['ru'])){
            $responce = ['result' => false, 'error' => 'Недостаточно данных'];
        } else {
            $json_files = array_filter($files,function($v) use($folder, $values, $name) {
           
                $ext = substr($v, -5);
                $lang = substr($v, 0, -5);
                if ($ext && $ext == '.json'){
                    $json_file = $folder.DIRECTORY_SEPARATOR.$v;

                    $json_content = file_get_contents($json_file);
                    $lang_array = json_decode($json_content,true);

                    if( !empty($values[$lang])) {
                        $lang_array[$name] = $values[$lang];
                    } else {
                        $lang_array[$name] = $values['ru'];
                    }
                    file_put_contents($json_file, json_encode($lang_array));
                }
                return $ext && $ext == '.json';
            });
        }

        return json_encode($responce);
    }
}
