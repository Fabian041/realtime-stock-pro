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
        Schema::create('tt_assy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_part')->unsigned();
            $table->foreign('id_part')->references('id')->on('tm_parts');
            $table->date('date');
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
        Schema::dropIfExists('tt_ass');
    }
};
