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
        Schema::table('tm_boms', function (Blueprint $table) {
            $table->unsignedBigInteger('id_material')->unsigned()->after('id_area');
            $table->foreign('id_material')->references('id')->on('tm_materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_boms', function (Blueprint $table) {
            //
        });
    }
};
