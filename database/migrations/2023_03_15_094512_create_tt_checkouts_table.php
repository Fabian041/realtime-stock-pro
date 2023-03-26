<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tt_checkouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_area')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->foreign('id_area')->references('id')->on('tm_areas');
            $table->foreign('id_material')->references('id')->on('tm_materials');
            $table->bigInteger('qty');
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
        Schema::dropIfExists('checkouts');
    }
};