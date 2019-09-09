<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_user', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('user_id');
            $table->index('user_id');

            $table->integer('by_user_id');
            $table->index('by_user_id');

            $table->string('property');
            
            $table->string('after');
            $table->string('before');

            $table->string('ip');

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
        Schema::dropIfExists('logs_user');
    }
}
