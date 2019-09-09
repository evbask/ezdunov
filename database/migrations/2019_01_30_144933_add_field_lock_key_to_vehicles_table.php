<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldLockKeyToVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('vehicle-server')->table('vehicle', function (Blueprint $table) {
           $table->text('lock_key')->nullable();
           $table->text('mac')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('vehicle-server')->table('vehicle', function (Blueprint $table) {
            //
            $table->dropColumn('lock_key');
            $table->dropColumn('mac');
        });
    }
}
