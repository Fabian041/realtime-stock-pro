<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\TmArea;
use App\Models\TmTransaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        \App\Models\TmArea::create([
            'name' => 'Warehouse'
        ]);
        \App\Models\TmArea::create([
            'name' => 'DC'
        ]);
        \App\Models\TmArea::create([
            'name' => 'MA'
        ]);
        \App\Models\TmArea::create([
            'name' => 'ASSY'
        ]);

        // TmTransaction::create([
        //     'code' => '111',
        //     'name' => 'STO',
        //     'type' => 'supply',
        // ]);

        // TmTransaction::create([
        //     'code' => '112',
        //     'name' => 'STO (R)',
        //     'type' => 'checkout',
        // ]);

        // TmTransaction::create([
        //     'code' => '211',
        //     'name' => 'Unboxing',
        //     'type' => 'supply',
        // ]);

        // TmTransaction::create([
        //     'code' => '212',
        //     'name' => 'Unboxing (R)',
        //     'type' => 'checkout',
        // ]);
        // TmTransaction::create([
        //     'code' => '311',
        //     'name' => 'Pulling Production',
        //     'type' => 'supply',
        // ]);

        // TmTransaction::create([
        //     'code' => '312',
        //     'name' => 'Pulling Production (R)',
        //     'type' => 'checkout',
        // ]);
        // TmTransaction::create([
        //     'code' => '411',
        //     'name' => 'Pulling Delivery',
        //     'type' => 'supply',
        // ]);

        // TmTransaction::create([
        //     'code' => '412',
        //     'name' => 'Pulling Delivery (R)',
        //     'type' => 'checkout',
        // ]);
        // TmTransaction::create([
        //     'code' => '511',
        //     'name' => 'Traceability',
        //     'type' => 'supply',
        // ]);

        // TmTransaction::create([
        //     'code' => '512',
        //     'name' => 'Traceability (R)',
        //     'type' => 'checkout',
        // ]);
        // TmTransaction::create([
        //     'code' => '611',
        //     'name' => 'NG Judgement',
        //     'type' => 'supply',
        // ]);

        // TmTransaction::create([
        //     'code' => '612',
        //     'name' => 'NG Judgement (R)',
        //     'type' => 'checkout',
        // ]);

    }
}
