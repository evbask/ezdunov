<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldRentRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rent_requests', function (Blueprint $table) {
            $table->integer('tariff_id')->nullable();;
            $table->integer('payment')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rent_requests', function (Blueprint $table) {
            $table->dropColumn('tariff_id');
            $table->dropColumn('payment');
        });
    }
}
