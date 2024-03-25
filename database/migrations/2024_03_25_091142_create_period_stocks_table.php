<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('period_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_area')->unsigned();
            $table->unsignedBigInteger('id_part')->unsigned();
            $table->foreign('id_part')->references('id')->on('tm_parts');
            $table->foreign('id_area')->references('id')->on('tm_areas');
            $table->integer('current_stock')->default(0);
            $table->timestamp('captured_at');
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
        Schema::dropIfExists('period_stocks');
    }
};
