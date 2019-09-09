<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('vehicle_id');
            $table->integer('type')->nullable();
            $table->integer('status')->nullable();
            $table->integer('price');
            $table->integer('parent_id')->nullable();
            $table->integer('promocode_id')->nullable();
            $table->foreign('promocode_id')->references('id')->on('promocodes');
            $table->integer('tariff_id')->nullable();
            $table->foreign('tariff_id')->references('id')->on('tariffs');

            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });
    }    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rents');
    }
}
