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
        DB::unprepared('CREATE TRIGGER update_part_stock AFTER INSERT ON tt_dcs, tt_mas, tt_assy FOR EACH ROW
            BEGIN
                DECLARE type VARCHAR(255);
                SELECT tm_transactions.type INTO type FROM tm_transactions
                JOIN tt_dcs ON tm_transactions.id = NEW.id_transaction
                JOIN tt_mas ON tm_transactions.id = NEW.id_transaction
                JOIN tt_assy ON tm_transactions.id = NEW.id_transaction LIMIT 1
            
                IF type = "supply" THEN
                    INSERT INTO part_stocks (id_part, id_area, DATE, current_stock) 
                    VALUES (NEW.id_part, NEW.id_area, NEW.date ,NEW.qty) 
                    ON DUPLICATE KEY UPDATE 
                        current_stock = current_stock + VALUES(current_stock);
                ELSE
                    INSERT INTO part_stocks (id_part, id_area, DATE, current_stock)
                    VALUES (NEW.id_part,NEW.id_area, NEW.date , -NEW.qty) 
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_part_stock');
    }
};
