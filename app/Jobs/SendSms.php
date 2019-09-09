<?php

namespace App\Jobs;

use \Exception;

use App\LogsSms;
use App\Components\Sms;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество секунд, во время которых может выполняться задача до таймаута.
     * @var int
     */
    // public $timeout = 5;

    /**
     * Автоматическое удаление работ с отсутствующими моделями
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * @var LogsSms
     */
    protected $sms;

    /**
     * Создать новый экземпляр задачи.
     * @return void
     */
    public function __construct(LogsSms $sms)
    {
        $this->sms = $sms;
        //
    }

    /**
     * Выполнить задачу.
     * @return void
     */
    public function handle()
    {
        $sms = new Sms();
        $sms->jobOfSend($this->sms);
    }

    /**
     * Неудачная обработка задачи.
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
