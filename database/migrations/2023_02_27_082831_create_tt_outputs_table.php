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
        Schema::create('tt_outputs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_bom')->unsigned();
            $table->foreign('id_bom')->references('id')->on('tm_boms');
            $table->timestamp('date');
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
        Schema::dropIfExists('tt_outputs');
    }
};
