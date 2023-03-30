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
        Schema::table('tt_materials', function (Blueprint $table) {
            $table->unsignedBigInteger('id_area')->unsigned()->after('qty');
            $table->foreign('id_area')->references('id')->on('tm_areas');
            $table->date('date')->after('id_transaction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tt_materials', function (Blueprint $table) {
            //
        });
    }
};
