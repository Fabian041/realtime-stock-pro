<?php

use Illuminate\Support\Facades\DB;
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
        DB::unprepared('CREATE TRIGGER update_current_stock AFTER INSERT ON tt_materials FOR EACH ROW
            BEGIN
                IF NEW.transaction_type = "supply" THEN
                    UPDATE current_stocks SET current_stock = current_stock + NEW.qty WHERE id_material = NEW.id_material;
                ELSE
                    UPDATE current_stocks SET current_stock = current_stock - NEW.qty WHERE id_material = NEW.id_material;
                END IF;
            END;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_current_stock');
    }
};
