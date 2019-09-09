<?php

namespace App\Jobs;

use App\LogsPush;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPush implements ShouldQueue
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
     * @var LogsPush
     */
    protected $push;

    /**
     * Создать новый экземпляр задачи.
     *
     * @return void
     */
    public function __construct(LogsPush $push)
    {
        $this->push = $push;
        //
    }

    /**
     * Выполнить задачу.
     *
     * @return void
     */
    public function handle()
    {
        $serviceAccount = ServiceAccount::fromArray(config('push.firebase'));
        
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();
            
        $messaging = $firebase->getMessaging();

        $message = CloudMessage::fromArray([
            'topic' => 'user' . $this->push->user_id,
            'notification' => $this->push->data,
        ]);
        
        try {
            $response = $messaging->send($message);
            $this->push->response = $response;
            $this->push->status = LogsPush::S_SUCCESS;
            $this->push->save();
        } catch (Excepiton $e) {
            throw $e;
        }
    }

    /**
     * Неудачная обработка задачи.
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->push->status = LogsPush::S_ERROR;
        $this->push->response = $exception->getMessage();
        echo $exception->getMessage() . PHP_EOL;
    }
}
