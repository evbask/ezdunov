<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $imei
 * @property string $command
 * @property boolean $done
 * @property string $args
 * @property string $answer
 */
class SrvCommandsQueue extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'commands_queue';

    /**
     * @var array
     */
    protected $fillable = ['id', 'imei', 'command', 'done', 'args', 'answer'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'vehicle-server';

}
