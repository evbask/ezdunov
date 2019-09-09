<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_bonus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('type');
            $table->integer('rent_id')->nullable();
            $table->integer('before');
            $table->integer('after');
            $table->integer('amount');
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
        Schema::dropIfExists('logs_bonus');
    }
}
