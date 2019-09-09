<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTariffField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tariffs', function(Blueprint $table){
            $table->boolean('enable')->nullable();
            $table->integer('type_payment')->nullable();
            $table->integer('type_rent')->nullable();
            $table->integer('quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tariffs', function(Blueprint $table){
            $table->dropColumn('enable');
            $table->dropColumn('type_payment');
            $table->dropColumn('type_rent');
            $table->dropColumn('quantity');
        });
    }
}
