<?php
namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

/**
 * Класс для работы с настройками
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $title
 * @property string $description
 * @property string $type
 * @property integer $user_id
 * @property string $updated_at
 * @property string $created_at
 * @todo подумать над оптимизацией дергать сразу весь массив и засовывать его в settings0
 * и дергать при многократном обращении из статической переменной, либо применять кэширование
 */
class Settings extends Model 
{
    protected $table = 'settings';

    /**
     * Массив типов настроек:
     * Ключ     - тип
     * Значение - описание типа 
     *
     * @var array
     */
    protected static $TYPES = [
        'main' => 'Общие настройки'
    ];
    protected $fillable = [
        'name',
        'value',
        'title',
        'description',
        'user_id',
        'type',
    ];

    /**
     * массив параметров ключ : значение
     * @var array 
     */
    public static $settings = [];

    /**
     * Поиск по типу
     * @param string $type тип свойств
     * @return array|bool object вернет массив объектов, если типа нет в массиве вернёт null
     */
    function searchType($type) 
    {
        if(self::checkType($type)){
            return self::where('type', '=', $type)->all();
        }

        return null;
    }

    /**
     * Возвращает массив типов
     *
     * @return array
     */
    public static function getTypes(){
        return self::$TYPES;
    }
    /**
     * заполняем массив настроек
     * @return void
     */
    public static function set()
    {
        $settings = self::orderBy('type')->get();
        foreach ($settings AS $setting) {
            self::$settings[$setting->name] = $setting->value;
        }
    }

    /**
	 * Возвращает установленное значение свойства
     * @param string $name имя свойства
     * @return string|bool возвращает значение свойства, если свойство не существует возвращает null
	 */
    public static function get($name)
    {
        self::check();
        return self::$settings[$name] ?? null;
    }

    /**
     * получить массив настроек
     */
    public static function getArray()
    {
        self::check();
        return self::$settings;
    }

    /**
     * установка значения свойству
     * @param string $name настройка
     * @param string $value значение
     * @return void
     */
    public static function setSettingValue($name, $value)
    {
		$setting = self::where('name', '=', $name)->first();
        $setting->value = $value;
        $setting->user_id = Auth::user()->id;
        $setting->save();
        self::$settings[$name] = $value;    
    }

    /**
     * проверяет получены ли настройки
     * заполняет массив в случае необходимости
     * @return void
     * 
     */
    public static function check()
    {
        if (!self::$settings) {
            self::set();
        }
    }

    public static function checkType($type){
        
        if(array_key_exists ($type, self::$TYPES)){
            return true;
        }
        return false;
    }
}