<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $url
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $content
 */
class Pages extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'url', 'title', 'description', 'keywords', 'content'];

    protected $forSmallArray = ['id', 'url', 'title', 'description', 'keywords', 'content'];
    protected $forBigArray = ['id', 'url', 'title', 'description', 'keywords', 'content'];

    public function getSmallArray() {
        $array = [];
        foreach ($this->forSmallArray as $value) {
            $array[$value] = $this->$value;
        }
        
        return $array;
    }

    public function getBigArray() {
        $array = [];
        foreach ($this->forBigArray as $value) {
            $array[$value] = $this->$value;
        }
        
        return $array;
    }
}
