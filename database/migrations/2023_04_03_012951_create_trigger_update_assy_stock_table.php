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
        DB::unprepared('CREATE TRIGGER update_assy_stock AFTER INSERT ON tt_assy FOR EACH ROW
            BEGIN
                DECLARE type VARCHAR(255);
                SELECT tm_transactions.type INTO type FROM tm_transactions
                JOIN tt_assy ON tm_transactions.id = NEW.id_transaction LIMIT 1;
            
                IF type = "supply" THEN
                    IF EXISTS (SELECT 1 FROM assy_stocks WHERE id_part = NEW.id_part AND DAY(DATE) = DAY(NEW.date)) THEN
                        UPDATE assy_stocks SET current_stock = current_stock + NEW.qty 
                        WHERE id_part = NEW.id_part AND DAY(DATE) = DAY(NEW.date);
                    ELSE
                        INSERT INTO assy_stocks (id_part, DATE, current_stock) 
                        VALUES (NEW.id_part, NEW.date ,NEW.qty);
                    END IF;
                ELSE
                    IF EXISTS (SELECT 1 FROM assy_stocks WHERE id_part = NEW.id_part AND DAY(DATE) = DAY(NEW.date)) THEN
                        UPDATE assy_stocks SET current_stock = current_stock - NEW.qty 
                        WHERE id_part = NEW.id_part AND DAY(DATE) = DAY(NEW.date);
                    ELSE
                        INSERT INTO assy_stocks (id_part, DATE, current_stock) 
                        VALUES (NEW.id_part, NEW.date , - NEW.qty);
                    END IF;
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
        Schema::table('assy_stocks', function (Blueprint $table) {
            //
        });
    }
};
