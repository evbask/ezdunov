<?php

namespace App\Jobs;

use \Exception;
use App\Logs_rfi_bank;
use App\User;
use App\RentRequest;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * @todo сделано как полное гавно, надо рефакторить RfiBank, Logs_rfi_bank
 */
class WriteOfMoney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * лог рфи
     * @var App\Logs_rfi_bank
     */
    protected $logs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Logs_rfi_bank $logs)
    {
        $this->logs = $logs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::findOrFail($this->logs->user_id);
        echo date("Y-m-d H:i:s") . "{$user->id} {$user->email} {$user->balance} " . PHP_EOL;
        if (!$user->recurrentPayment($this->logs->summ)) {
            throw new Exception($user->getErrors()[0]);
        } else {
            if ($this->logs->rentRequest ?? false) {
                $this->logs->rentRequest->status = RentRequest::T_REQEST_PAID;
                $this->logs->rentRequest->save();

                $this->logs->user->balanceDecrease($this->balancePayment, LogsBalance::T_RENTPAYMENT, $this->id);
            }
        }
    }

    /**
     * Неудачная обработка задачи.
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        /** если пополнение содержало ид аренды, отменим ее */
        if ($this->logs->rentRequest ?? false) {
            $this->logs->rentRequest->status = RentRequest::T_REQUEST_CANCEL;
            $this->logs->rentRequest->save();
        }
        echo $exception->getMessage() . PHP_EOL;
    }
}
