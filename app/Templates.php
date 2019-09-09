<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $content
 */
class Templates extends Model
{

    use \App\Traites\ApiArrays;
    
    /**
     * @var array
     */
    protected $fillable = ['created_at', 'updated_at', 'name', 'content'];

    protected $forSmallArray = ['id', 'name'];
    protected $forBigArray = ['id', 'name', 'content'];

    
}
