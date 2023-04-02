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
        DB::unprepared('CREATE TRIGGER update_material_stock AFTER INSERT ON tt_materials FOR EACH ROW
            BEGIN
                DECLARE type VARCHAR(255);
                SELECT tm_transactions.type INTO type FROM tm_transactions
                JOIN tt_materials ON tm_transactions.id = NEW.id_transaction LIMIT 1;
            
                IF type = "supply" THEN
                    INSERT INTO material_stocks (id_material, id_area, DATE, current_stock) 
                    VALUES (NEW.id_material, NEW.id_area, NEW.date ,NEW.qty) 
                    ON DUPLICATE KEY UPDATE 
                    current_stock = current_stock + VALUES(current_stock);
                ELSE
                    INSERT INTO material_stocks (id_material, id_area, DATE, current_stock)
                    VALUES (NEW.id_material,NEW.id_area, NEW.date , -NEW.qty) 
                    ON DUPLICATE KEY UPDATE 
                    current_stock = current_stock - VALUES(current_stock);
                END IF;
            END'
        );
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
