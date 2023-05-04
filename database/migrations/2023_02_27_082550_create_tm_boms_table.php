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
        Schema::create('tm_boms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_part')->unsigned();
            $table->foreign('id_part')->references('id')->on('tm_parts');
            $table->float('qty_use',8,3);
            $table->string('uom');
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
        Schema::dropIfExists('tm_boms');
    }
};