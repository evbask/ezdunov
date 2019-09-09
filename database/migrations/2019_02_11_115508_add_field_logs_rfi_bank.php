<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldLogsRfiBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs_rfi_bank', function (Blueprint $table) {
            $table->string('result_cashout_str')->nullable();
            $table->string('status_cashout')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_rfi_bank', function (Blueprint $table) {
            $table->dropColumn('result_cashout_str');
            $table->dropColumn('status_cashout');
        });
    }
}
