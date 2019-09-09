<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToLogsPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_push', function (Blueprint $table) {
            $table->string('data')->nullable();
            $table->string('response')->nullable();
            $table->dropColumn('title');
            $table->dropColumn('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_push', function (Blueprint $table) {
            $table->dropColumn('data');
            $table->dropColumn('response')->nullable();
        });
    }
}