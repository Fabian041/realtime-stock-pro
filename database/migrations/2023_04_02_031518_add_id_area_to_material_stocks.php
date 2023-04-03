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
        Schema::table('material_stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('id_area')->unsigned()->after('id_material');
            $table->foreign('id_area')->references('id')->on('tm_areas');
            $table->timestamp('date')->after('id_area');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('current_stocks', function (Blueprint $table) {
            //
        });
    }
};
