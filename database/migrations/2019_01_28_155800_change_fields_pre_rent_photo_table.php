<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class changeFieldsPreRentPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_rent_photos', function (Blueprint $table) {
            $table->integer('rent_id');
            $table->integer('vehicle_id');
            $table->dropColumn('pre_rent_problem_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_rent_photos', function (Blueprint $table) {
            $table->dropColumn('rent_id');
            $table->dropColumn('vehicle_id');
            $table->integer('pre_rent_problem_id');
            $table->foreign('pre_rent_problem_id')->references('id')->on('pre_rent_problems');
        });
    }
}