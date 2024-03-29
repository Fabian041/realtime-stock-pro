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
        Schema::table('tt_dcs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_transaction')->unsigned()->after('id_part');
            $table->foreign('id_transaction')->references('id')->on('tm_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tt_dcs', function (Blueprint $table) {
            //
        });
    }
};
