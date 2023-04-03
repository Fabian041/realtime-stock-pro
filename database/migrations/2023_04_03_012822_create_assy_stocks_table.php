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
        Schema::create('assy_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_part')->unsigned()->unique();
            $table->foreign('id_part')->references('id')->on('tm_parts');
            $table->timestamp('date');
            $table->integer('current_stock')->default(0);
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
        Schema::dropIfExists('assy_stocks');
    }
};
