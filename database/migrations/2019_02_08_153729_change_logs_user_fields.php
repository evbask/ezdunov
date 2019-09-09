<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLogsUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_user', function (Blueprint $table) {
            $table->string('before')->nullable()->change();
            $table->string('after')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_user', function (Blueprint $table) {
            $table->change('before')->nullable(false)->change();
            $table->change('after')->nullable(false)->change();
        });
    }
}
