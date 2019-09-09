<?php

namespace App\Console\Commands;

use App\User;
use App\Logs_rfi_bank;
use App\Components\RfiBank;
use App\Jobs\WriteOfMoney as Job;

use Illuminate\Console\Command;

class WriteOfMoney extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     *
     * @var string
     */
    protected $signature = 'payment:writeOfMoney';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Рекурентное списание денег у должников';

    /**
     * Создание нового экземпляра команды.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Выполнение консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        echo 'Выполнение...';
        /**
         * @todo JIorD 2019/02/07 скорее всего надо кидать в планировщик задач
         */
        $users = User::where('balance', '<', 0)->
            whereNotNull('email')->
            get();
        foreach ($users as $user) {
            echo "{$user->id} {$user->balance}" . PHP_EOL;

            $rfi = Logs_rfi_bank::create([
                'user_id'       => $user->id,
                'summ'          => abs($user->balance),
                'status'        => Logs_rfi_bank::STATUS_WAITING,
                'service_id'    => RfiBank::SERVICE_ID,
                'phone'         => $user->phone,
                'type'          => RfiBank::PAYMENT_TYPE,
                'email'         => $user->email
            ]);

            dispatch((new Job($rfi))->
                onConnection('database')->
                onQueue('WriteOfMoney'));
        }
    }
}
