<?php

namespace App;

use Auth;

use Validator;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = 'user_device';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'platform', 
        'versionOS', 
        'brand',
        'model',
        'versionApp',
    ];

    const P_ANDROID = 0;
    const P_IOS = 1;

    const PLATFORM = [
        self::P_ANDROID => 'Android',
        self::P_IOS     => 'iOS'
    ];

    public function getDateFormat()
    {
        return 'U';
    }

    public function getDates()
    {
        return [];
    }

    protected static $rules = [
        'userDevice.platform'      => 'required', 
        // 'versionOS'     => '', 
        // 'brand'         => '',
        // 'model'         => '',
        // 'versionApp'    => '',
    ];

    /**
     * вернет текстовые представление платформы
     * @return string
     */
    public function getPlatform()
    {
        $vOs = $this->versionOS ? " ({$this->versionOS})" : null; 
        if (array_key_exists($this->platform, self::PLATFORM)) {
            return self::PLATFORM[$this->platform] . $vOs;
        } else {
            return "Undefined" . $vOs;
        }
    }

    /**
     * вернет название девайса
     * @return string
     */
    public function getName()
    {
        return trim("{$this->brand} {$this->model}");
    }

    /**
     * вернет полное текстовое представление
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getName() . ', ' .  $this->getPlatform());
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public static function add($data){
        $validator = Validator::make($data->all(), self::$rules);
        if ($validator->fails()) {
            return false;
        } 
        try {
            return self::updateOrCreate(
                ['user_id' => Auth::user()->id],
                $data->userDevice
            );
        } catch (Exception $e) {
            return false;
        }
    }
}
