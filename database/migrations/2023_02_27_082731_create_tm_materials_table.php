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
        Schema::create('tm_materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('part_name');
            $table->string('back_number');
            $table->string('part_number');
            $table->timestamp('date');
            $table->time('time');
            $table->string('supplier');
            $table->string('source');
            $table->integer('limit_qty');
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
        Schema::dropIfExists('tt_stock');
    }
};