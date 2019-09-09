<?php

namespace App\Console\Commands;

use App\SrvVehicle; 

use Illuminate\Console\Command;

/**
 * для дебага, в проде убрать
 */
class VehicleClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        SrvVehicle::where('status', '!=', SrvVehicle::S_FREE)->
            update(['status' => SrvVehicle::S_FREE]);
    }
}
