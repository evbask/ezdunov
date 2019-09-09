<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosCompletedRentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos_completed_rent', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->index('user_id');

            $table->integer('rent_id');
            $table->index('rent_id');

            $table->integer('vehicle_id');
            $table->index('vehicle_id');

            $table->string('photo');

            $table->json('gps')->nullable();

            $table->timestamp('date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photos_completed_rent');
    }
}
