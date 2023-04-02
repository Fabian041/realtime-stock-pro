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
        Schema::create('tm_parts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('part_name');
            $table->string('back_number')->unique();
            $table->string('part_number')->unique();
            $table->integer('qty_limit');
            $table->integer('status');
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
        Schema::dropIfExists('tm_part_numbers');
    }
};