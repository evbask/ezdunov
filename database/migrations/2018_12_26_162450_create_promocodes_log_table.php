<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocodesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('promocode_id');
            $table->foreign('promocode_id')->references('id')->on('promocodes');
            $table->integer('rent_id')->nullable();
            $table->foreign('rent_id')->references('id')->on('rents');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('promocodes_log');
    }
}
